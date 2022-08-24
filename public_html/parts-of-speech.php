<?php
session_start();

use markfullmer\waraydictionary\Db;
use markfullmer\waraydictionary\Data;
use markfullmer\waraydictionary\Render;
use markfullmer\waraydictionary\SpeechTagger;
use markfullmer\waraydictionary\tests\PartOfSpeechTest;

require '../vendor/autoload.php';
require '../variables.php';
require './includes/head.php';

echo '<div class="container">';
echo '<h2>Part of Speech Identifier</h2>';


PartOfSpeechTest::test();

die();
$uncategorized = Db::getUncategorized(100);
$identified = 0;
$total = 0;
echo '<table class="default"><tr><th>Word</th><th>Prediction</th><th>Confidence</th><th>Sentence</th><th>Rules</th>';
foreach ($uncategorized as $row) {
  $pos = new SpeechTagger();
  $pos->identify($row['word'], $row['one_ex']);
  if ($pos->attributes['id'] !== '?') {
    $identified++;
  }
  $total++;
  $rules = '<ul>';
  foreach ($pos->attributes['rules'] as $rule) {
    $rules .= '<li>' . $rule . '</li>';
  }
  $rules .= '</ul>';
  echo '<tr><td>' . $row['word'] . '</td><td>' . $pos->attributes['id'] . '</td><td>' . $pos->attributes['score'] . '</td><td>' . Render::highlight($row['one_ex'], $row['word']) . '</td><td>' . $rules . '</td></tr>';
}
echo '</table>';
echo 'Comprehensivenss: ' . number_format($identified / $total * 100) . '%';

echo '<h2>Parts of Speech Reference</h2>';
echo '<table>';
echo '<tr><th>Part of Speech</th><th>Example</th></tr>';
foreach (Data::$partsOfSpeech as $part => $example) {
  echo '<tr><td>' . $part . '</td><td>' . $example . '</td></tr>';
}
echo '</table>';
echo '</div>';
