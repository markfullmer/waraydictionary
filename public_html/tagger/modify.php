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

if (!isset($_REQUEST['id'])) {
  header('Location: /tagger/edit.php');
}
$pos = Db::getPosById($_REQUEST['id']);
?>
<div class="container">
  <div class="row">
    <h1>Edit parts of speech</h1>
    <form method="post" action="/tagger/save.php">
      <label>New part of speech abbreviation*<input type="text" name="pos" value="<?php echo $pos['pos']; ?>" required /></label>
      Single-letter form*:
      <select name="short">
        <option value="" selected>--Select--</option>
        <option value="p" <?php if ($pos['short'] === 'p') { echo " selected"; } ?> >p</option>
        <option value="m" <?php if ($pos['short'] === 'm') { echo " selected"; } ?>>m</option>
        <option value="r" <?php if ($pos['short'] === 'r') { echo " selected"; } ?>>r</option>
      </select>
      <input type="hidden" name="id" value="<?php echo $pos['id']; ?>" />
      <label>Full form:<input type="text" name="tooltip" value="<?php echo $pos['tooltip']; ?>" /></label>
      <label><input value="Add" name="submit" type="submit"></label>
    </form>
  </div>
</div>
