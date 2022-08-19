<?php
session_start();

use markfullmer\waraydictionary\Db;
use markfullmer\waraydictionary\Data;
use markfullmer\waraydictionary\Render;

require '../vendor/autoload.php';
require '../variables.php';
require './includes/head.php';

echo '<div class="container">';
echo '<h2>Parts of Speech Reference</h2>';
echo '<table>';
echo '<tr><th>Part of Speech</th><th>Example</th></tr>';
foreach (Data::$partsOfSpeech as $part => $example) {
  echo '<tr><td>' . $part . '</td><td>' . $example . '</td></tr>';
}
echo '</table>';
echo '</div>';
