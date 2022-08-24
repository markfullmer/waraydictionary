<?php
session_start();

use markfullmer\waraydictionary\Data;
use markfullmer\waraydictionary\Render;
use markfullmer\waraydictionary\SpeechTagger;
use markfullmer\waraydictionary\tests\PartOfSpeechTest;

require '../vendor/autoload.php';
require '../variables.php';
require './includes/head.php';

$word = 'kadakó';
$sentence = 'Kun ano kadakó an butones sugad man an kadákó han ohales.';
if (isset($_REQUEST['word']) && isset($_REQUEST['sentence'])) {
  $word = Data::clean($_REQUEST['word']);
  $sentence = Data::clean($_REQUEST['sentence']);
}
$pos = new SpeechTagger();
$pos->identify($word, $sentence);
?>
<div class="container">
  <h2>Part of Speech Identifier</h2>
  <form action="./parts-of-speech.php" method="post">
    Target word: <input type="text" id="search" name="word" value="<?php echo $word; ?>" placeholder="Target word" /><br>
    Sentence<br />
    <textarea name="sentence"><?php echo $sentence; ?></textarea>
    <input type="submit" name="search" value="Analyze">
  </form>
<?php
if (isset($pos->attributes['id'])) {
  echo Render::partOfSpeech($pos->attributes);
  echo '<strong>Location in sentence:</strong> ' . Render::highlight($sentence, $word) . '<br />';
}
echo '<br /><br />';
echo '<h4>Baseline logic for part of speech algorithm</h4>';
PartOfSpeechTest::test();
// die();
// $uncategorized = Db::getUncategorized(100);
// $identified = 0;
// $total = 0;
// echo '<table class="default"><tr><th>Word</th><th>Prediction</th><th>Confidence</th><th>Sentence</th><th>Rules</th>';
// foreach ($uncategorized as $row) {
//   $pos = new SpeechTagger();
//   $pos->identify($row['word'], $row['one_ex']);
//   if ($pos->attributes['id'] !== '?') {
//     $identified++;
//   }
//   $total++;
//   $rules = '<ul>';
//   foreach ($pos->attributes['rules'] as $rule) {
//     $rules .= '<li>' . $rule . '</li>';
//   }
//   $rules .= '</ul>';
//   echo '<tr><td>' . $row['word'] . '</td><td>' . $pos->attributes['id'] . '</td><td>' . $pos->attributes['score'] . '</td><td>' . Render::highlight($row['one_ex'], $row['word']) . '</td><td>' . $rules . '</td></tr>';
// }
// echo '</table>';
// echo 'Comprehensivenss: ' . number_format($identified / $total * 100) . '%';

echo '<h2>Parts of Speech Reference</h2>';
echo '<table class="default">';
echo '<tr><th>Part of Speech</th><th>Example</th></tr>';
foreach (Data::$partsOfSpeech as $part => $example) {
  echo '<tr><td>' . $part . '</td><td>' . $example . '</td></tr>';
}
echo '</table>';
echo '</div>';
