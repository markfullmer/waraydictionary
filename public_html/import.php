<?php

require '../vendor/autoload.php';
require './includes/head.php';
require '../variables.php';

use League\Csv\Reader;
use League\Csv\Statement;
use markfullmer\waraydictionary\Db;

// die();

$csv = Reader::createFromPath('../import.csv', 'r');
$csv->setHeaderOffset(0); //set the CSV header offset

$stmt = Statement::create();

$db = Db::connect();
//Our SQL statement. This will empty / truncate the table "videos"
$sql = "TRUNCATE TABLE `word`";
//Prepare the SQL query.
$statement = $db->prepare($sql);
//Execute the statement.
$statement->execute();

function mb_ucfirst($string) {
  return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
}

$records = $stmt->process($csv);
$preppers = ['one_def', 'two_def', 'one_ex', 'two_ex'];
foreach ($records as $record) {
  if (strpos($record['one_def'], '2') !== FALSE) {
    $defs = explode('2', $record['one_def']);
    if (isset($defs[0])) {
      $record['one_def'] = trim($defs[0], '1 2 3');
    }
    if (isset($defs[1])) {
      $defs_two = explode('3', $defs[1]);
      if (isset($defs_two[1])) {
        $record['two_def'] = trim($defs_two[0], '1 2 3 ;');
        $record['three_def'] = trim($defs_two[1], '1 2 3 ;');
      }
      else {
        $record['two_def'] = trim($defs[1], '1 2 3 ;');
      }
    }
  }
  if (strpos($record['one_ex'], '2') !== FALSE) {
    $exs = explode('2', $record['one_ex']);
    if (isset($exs[0])) {
      $record['one_ex'] = trim($exs[0], '1 2 3 ;');
    }
    if (isset($exs[1])) {
      $exs_two = explode('3', $exs[1]);
      if (isset($exs_two[1])) {
        $record['two_ex'] = trim($exs_two[0], '1 2 3 ;');
        $record['three_ex'] = trim($exs_two[1], '1 2 3 ;');
      }
      else {
        $record['two_ex'] = trim($exs[1], '1 2 3 ;');
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


