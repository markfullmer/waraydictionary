<?php
session_start();

use markfullmer\waraydictionary\Db;
use markfullmer\waraydictionary\Render;

require '../../vendor/autoload.php';
require '../../variables.php';

if (!Db::isAuthenticated()) {
  header('Location: /index.php?auth=fail');
  die();
}
require './../includes/head.php';

?>
<div class="container">
  <div class="row">
    <h1>Edit parts of speech</h1>
    <form method="post" action="/tagger/save.php">
      <label>New part of speech*<input type="text" name="pos" required /></label>
      Short form*:
      <select name="short">
        <option value="" selected>--Select--</option>
        <option value="p">p</option>
        <option value="m">m</option>
        <option value="r">r</option>
      </select>
      <label><input value="Add" name="submit" type="submit"></label>
    </form>
    <h3>Existing parts of speech</h3>
    <?php
    $parts = Db::getAllPos();
    foreach ($parts as $part) {
      echo $part['pos'] . ' (' . $part['short'] . ') <a href="/tagger/delete.php?id=' . $part['id'] . '">delete</a><br />';
    }
    ?>
  </div>
</div>
