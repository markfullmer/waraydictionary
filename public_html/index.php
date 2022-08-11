<?php
session_start();

use markfullmer\waraydictionary\Db;
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
    <div class="col-md-8 blurb-box">
      <h2>Waray Dictionary</h2>
      <form action="./index.php" method="post">
        <input type="text" id="search" name="word" value="<?php echo $search; ?>" placeholder="Search for a word"><br>
        <input type="submit" name="search" value="Find"></form>
        <?php echo $message; ?>
      </form>
      <?php
        if (isset($match['word'])) {
          echo Render::entry($match);
        }
        if (!empty($cognates)) {
          echo 'Words with same root: ' . Render::cognates($cognates);
        }
      ?>
    </div>
  </div>
  <h2>List of Words</h1>

    <?php
    if (Db::isAuthenticated()) {
      echo '<h6><a href="/edit.php?id=add">Add new word</a></h6>';
    }
    $db = Db::connect();
    $words = $db->query("SELECT * FROM word ORDER BY word ASC")->fetchAll();
    foreach ($words as $row) {
      echo '<div class="row"><div class="col">' . Render::entry($row) . '</div></div>';
    }
    ?>

</div>
