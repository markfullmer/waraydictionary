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
      <label>New part of speech abbreviation*<input type="text" name="pos" required /></label>
      Single-letter form*:
      <select name="short">
        <option value="" selected>--Select--</option>
        <option value="p">p</option>
        <option value="m">m</option>
        <option value="r">r</option>
      </select>
      <label>Full form:<input type="text" name="tooltip" /></label>
      <label><input value="Add" name="submit" type="submit"></label>
    </form>
    <h3>Existing parts of speech</h3>
    <table class="default"><tr><th>Abbreviation</th><th>Single letter</th><th>Tooltip</th><th>Operations</th></tr>
    <?php
    $parts = Db::getAllPos();
    foreach ($parts as $part) {
      echo '<tr><td>' . $part['pos'] . '</td><td>' . $part['short'] . '</td><td>' . $part['tooltip'] . '<td><a href="/tagger/modify.php?id=' . $part['id'] . '">edit</a> | <a href="/tagger/delete.php?id=' . $part['id'] . '">delete</a></td></tr>';
    }
    ?>
    </table>
  </div>
</div>
