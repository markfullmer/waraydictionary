<?php
session_start();

use markfullmer\waraydictionary\Db;
use markfullmer\waraydictionary\Data;
use markfullmer\waraydictionary\Render;
use markfullmer\waraydictionary\SpeechTagger;

require '../vendor/autoload.php';
require '../variables.php';
require './includes/head.php';

echo '<div class="container">';
echo '<h2>Part of Speech Identifier</h2>';

$sentence = 'Han bata pa ini hi Layong Uray, nasasabtan na an iya kaibahan han nga tanan nga tagabungtoí';
SpeechTagger::test();
$uncategorized = Db::getUncategorized('100');
echo '<table border="1"><tr><th>Word</th><th>Sentence</th><th>Prediction</th><th>Confidence</th>';
foreach ($uncategorized as $row) {
  $pos = SpeechTagger::identify($row['word'], $row['one_ex']);
  echo '<tr style="border: 1px solid black;"><td>' . $row['word'] . '</td><td>' . Render::highlight($row['one_ex'], $row['word']) . '</td><td>' . $pos['id'] . '</td><td>' . $pos['score'] . '</td></tr>';
}

echo '<h2>Parts of Speech Reference</h2>';
echo '<table>';
echo '<tr><th>Part of Speech</th><th>Example</th></tr>';
foreach (Data::$partsOfSpeech as $part => $example) {
  echo '<tr><td>' . $part . '</td><td>' . $example . '</td></tr>';
}
echo '</table>';
echo '</div>';
