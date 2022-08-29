<?php
session_start();

use markfullmer\waraydictionary\Db;
use markfullmer\waraydictionary\Data;
use markfullmer\waraydictionary\Cache;
use markfullmer\waraydictionary\Render;

require '../vendor/autoload.php';
require './includes/head.php';
require '../variables.php';

// Uncomment to wipe all data.
// Db::install();

$search = '';
$match = FALSE;
$message = '';
$cognates = FALSE;
if (isset($_REQUEST['word'])) {
  $match = Db::search($_REQUEST['word']);
  $cognates = Db::searchRoots($_REQUEST['word']);
  $search = isset($match['word']) ? $match['word'] : '';
  if ($search === '') {
    $message = 'Not found in the dictionary.';
  }
}
?>

<div class="container">
  <div class="row">
    <?php echo Render::messages($_GET); ?>
  </div>
  <div class="row">
    <div class="col-md-8 blurb-box">
      <h2>Waray Dictionary</h2>
      <form action="./index.php" method="post">
        <input type="text" id="search" name="word" value="<?php echo $search; ?>" placeholder="Search for a word"><br>
        <input type="submit" name="search" value="Find">
      </form>

      <?php
      if (isset($match['word'])) {
        echo Render::entry($match);
      }
      if (!empty($cognates)) {
        echo 'Words with same root: ' . Render::cognates($cognates);
      }
      if (Db::isAuthenticated()) {
        echo '<h5><a href="/edit.php?id=add">Add new word</a></h5>';
      }
      ?>
    </div>
  </div>
  <div class="row">
  <h2>List of Words</h1>

    <?php
    $letter = 'A';
    $sort = 'word';
    if (isset($_REQUEST['sort']) && $_REQUEST['sort'] === 'count') {
      $sort = 'count';
    }
    echo '<p>';
    echo Render::glossary($sort);
    echo '</p>';
    if (isset($_REQUEST['glossary']) && in_array($_REQUEST['glossary'], Data::$glossary)) {
      $letter = $_REQUEST['glossary'];
    }
    if ($sort === 'count') {
      echo '<a href="./index.php?glossary=' . $letter . '">Sort alphabetically</a> | Sort by frequency';
    }
    else {
      echo 'Sort alphabetically | <a href="./index.php?sort=count&glossary=' . $letter .'">Sort by frequency</a>';
    }
    echo '</p>';
    if ($cache && !Db::isAuthenticated() && $cache_data = Cache::get('glossary_' . $letter . $sort)) {
      $glossary = unserialize($cache_data);
    }
    else {
      $words = Db::getGlossary($letter, $sort);
      $glossary = [];
      $glossary[] = '<h2>' . $letter . '</h3>';
      foreach ($words as $row) {
        $glossary[] = '<div class="row"><div class="col">' . Render::entry($row) . '</div></div>';
      }
      if (!Db::isAuthenticated() && $cache) {
        Cache::set('glossary_' . $letter . $sort, serialize($glossary));
      }
    }
    echo implode($glossary);
    ?>

</div>
</div>
