<?php

use markfullmer\waraydictionary\Db;
use markfullmer\waraydictionary\Data;
use markfullmer\waraydictionary\Render;

require '../vendor/autoload.php';

require './includes/head.php';
require '../variables.php';

$search = '';
$match = FALSE;
$message = '';
if (isset($_POST['word'])) {
  $match = Db::search($_POST['word']);
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
        <input type="text" id="search" name="word" value="<?php echo $search; ?>" placeholder="Search by word or root form"><br>
        <input type="submit" name="search" value="Find"></form>
        <?php echo $message; ?>
      </form>
      <?php
        if (isset($match['word'])) {
          echo Render::buildEntry($match);
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
      echo '<div class="row"><div class="col">' . Render::buildEntry($row) . '</div></div>';
    }
    ?>

</div>
