<?php
session_start();

use markfullmer\waraydictionary\Data;
use markfullmer\waraydictionary\Render;
use markfullmer\waraydictionary\SpeechTagger;
use markfullmer\waraydictionary\tests\PartOfSpeechTest;
use markfullmer\waraydictionary\MorphoSyntaxData;

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
  ?>
  <br />
  <br />
  <h3>Reference: Ragging of Verbal predicates</h3>
  <table class="default">
    <tr>
      <th>Part of Speech</th>
      <th>Example</th>
    </tr>
    <?php
    foreach (Data::$partsOfSpeech as $part => $example) {
      echo '<tr><td>' . $part . '</td><td>' . $example . '</td></tr>';
    }
    echo '</table>';
    ?>
    <br />
    <br />
    <h3>Methodology of the Waray Part of Speech Identifier</h3>
    <p>This algorithm is based on principles outlined by Voltaire Oyzon in "A Corpus-based study of the morphosyntactic functions of Waray substantive lexical items" (2020). It uses a dictionary of known syntax (location in clause) and morphology (prefix, suffix) patterns in the Waray language to evaluate 23 rules, outlined below. It then applies a scoring system to estimate the probability of predicate (verb), referential (noun), or modificative (adjective) of the target word.</p>

    <p>Common modifiers (e.g., "la," "pa,", "gad", "ngay-an") are often inserted between substantive words that would indicate part of speech. Therefore, the algorithm ignores these when evaluating syntax. For example, it will parse "gin-aanak pa la hiya" as "gin-aanak hiya," and can identify that a pronoun ("hiya") is following the word "gin-aanak".</p>

    <p>For similar reasons, clausal beginnings ("kun", "kay", "ano") are ignored. For example, "Kun ano kadakó an butones sugad man an kadákó han ohales" will consider "kadakó" the beginning of the clause for the purposes of identifying part of speech.</p>

    <p>The part of speech of adjacent words often indicates a word's likely part of speech. For example, a clause is less likely to have a predicate adjacent to another predicate, rathern than adjacent to a modifier or referential. The algorithm therefore evaluates the part of speech of adjacent words to predict the target word's part of speech. It achieves this in two ways: first, it checks the Waray dictionary for the adjacent word's part of speech; if the word's part of speech is not found in the dictionary, it then applies its own part-of-speech guessing algorithm to the adjacent word (the algorithm uses itself to improve its guess for the target word!). If it can establish a high probability of that word's part of speech, it then can apply 'adjacency' rules to the target word.</p>

    <p>At present, this algorithm does not evaluate infixes, which are a common feature of Filipino languages (e.g., "palit" [buy] becomes "pumalit" [bought] with the infix "um"). In order for the algorithm to evaluate infixes, it would need a dictionary of Waray word roots. This effort is underway.</p>

    <?php

    echo '<table class="default"><tr><th>Rule</th><th>Example (target word is underlined)</th><th>Weight</th></tr>';
    foreach (MorphoSyntaxData::$rules as $rule => $value) {
      $score = '';
      foreach ($value['score'] as $p => $s) {
        $score .= $p . ': ' . $s . ' points<br />';
      }
      echo '<tr><td>' . $rule . '</td><td>' . implode('<br />', $value['example']) . '</td><td>' . $score . '</td></tr>';
    }
    echo '</table>';

    echo '<h4>Baseline tests for part of speech algorithm</h4>';
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

    echo '</div>';
