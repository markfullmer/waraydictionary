<?php

require '../vendor/autoload.php';
require './includes/head.php';
require '../variables.php';

use League\Csv\Reader;
use League\Csv\Statement;
use markfullmer\waraydictionary\Db;

//die();

$csv = Reader::createFromPath('../top-words.csv', 'r');
$csv->setEnclosure('"');
$csv->setHeaderOffset(0); //set the CSV header offset

$stmt = Statement::create()->limit(28000);

$speech = [
  'noun' => 'reference',
  'modifier' => 'modificative',
  'verb' => 'predicative',
  'pronoun' => 'reference',
  'imperative' => 'predicative',
  'adjective' => 'modifier',
];

// $db = Db::connect();
// //Our SQL statement. This will empty / truncate the table "videos"
// $sql = "TRUNCATE TABLE `word`";
// //Prepare the SQL query.
// $statement = $db->prepare($sql);
// //Execute the statement.
// $statement->execute();

function mb_ucfirst($string) {
  return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
}
function multiexplode($delimiters, $string) {
  $ready = str_replace($delimiters, $delimiters[0], $string);
  $launch = explode($delimiters[0], $ready);
  return  $launch;
}
function prep_pos($string) {
  global $speech;
  $string = trim($string);
  if (in_array($string, array_keys($speech))) {
    $string = $speech[$string];
  }
  return $string;
}
function prep_string($string) {
  $string = trim($string, '1 2 3 ;');
  return $string;
}

$processed = [];

$records = $stmt->process($csv);
$preppers = ['one_def', 'two_def', 'one_ex', 'two_ex'];
foreach ($records as $record) {
  if ($in_dict = Db::search($record['word'])) {
    print_r('Already in dictionary:'. $record['word']);
    continue;
  }
  if (in_array($record['word'], $processed)) {
    continue;
  }
  $processed[] = $record['word'];
  // Migrate & split parts of speech.
  if (isset($record['one_pos'])) {
    $parts = multiexplode(['/',';',','], $record['one_pos']);
    if (isset($parts[0])) {
      $record['one_pos'] = prep_pos($parts[0]);
    }
    if (isset($parts[1])) {
      $record['two_pos'] = prep_pos($parts[1]);
    }
    if (isset($parts[2])) {
      $record['three_pos'] = prep_pos($parts[2]);
    }
  }
  // Split multiple definitions.
  $defs = multiexplode(['2',';'], $record['one_def']);
  if (isset($defs[0])) {
    $record['one_def'] = prep_string($defs[0]);
  }
  if (isset($defs[1])) {
    $defs_two = explode('3', $defs[1]);
    if (isset($defs_two[1])) {
      $record['two_def'] = prep_string($defs_two[0]);
      $record['three_def'] = prep_string($defs_two[1]);
    }
    else {
      $record['two_def'] = prep_string($defs[1]);
    }
  }
  // Split multiple examples.
  if (strpos($record['one_ex'], '2') !== FALSE) {
    $exs = explode('2', $record['one_ex']);
    if (isset($exs[0])) {
      $record['one_ex'] = prep_string($exs[0]);
    }
    if (isset($exs[1])) {
      $exs_two = explode('3', $exs[1]);
      if (isset($exs_two[1])) {
        $record['two_ex'] = prep_string($exs_two[0]);
        $record['three_ex'] = prep_string($exs_two[1]);
      }
      else {
        $record['two_ex'] = prep_string($exs[1]);
      }
    }
  }
  foreach ($preppers as $prepper) {
    if (isset($record[$prepper])) {
      $record[$prepper] = mb_ucfirst($record[$prepper]);
    }
  }
  Db::insertWord($record);
}


