<?php

use markfullmer\waraydictionary\Db;
use markfullmer\waraydictionary\Data;
use markfullmer\waraydictionary\Render;
use Tlr\Tables\Elements\Table;

require '../vendor/autoload.php';

require './includes/head.php';
require '../variables.php';

$db = Db::connect();
$words = $db->query("SELECT * FROM word")->fetchAll();

// $table = new Table;
// $table->class('table');
// $row = $table->header()->row();
// $row->cell('Word');
// $row->cell('Pronunciation');
// $row->cell('Definitions');
// $row->cell('Root');
// foreach ($words as $word) {
//   $row = $table->body()->row();
//   $row->cell($word['word']);
//   $row->cell($word['pronunciation']);
//   $row->cell('Food');
//   $row->cell($word['root']);
// }
// echo $table->render();

foreach ($words as $row) {
  echo Render::buildEntry($row);
}
