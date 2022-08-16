<?php
session_start();

use markfullmer\waraydictionary\Db;
use markfullmer\waraydictionary\Cache;
use markfullmer\waraydictionary\Render;

require '../vendor/autoload.php';
require '../variables.php';

if (!Db::isAuthenticated()) {
  header('Location: /index.php?fail=auth');
  die();
}
if (isset($_GET['confirmed'])) {
  Db::deleteWord($_REQUEST['id']);
  Cache::clear();
  header('Location: /index.php?deleted=1');
}
require './includes/head.php';

echo '<div class="container">';

$word = Db::getWord($_REQUEST['id']);
echo '<h1>Delete word</h1>';
echo '<p>Confirm you want to delete the word "<strong>' . $word['word'] . '</strong>".</p>';
echo '<a href="/delete.php?id=' . $_REQUEST['id'] . '&confirmed=1">Yes</a>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/index.php">No</a>';

echo '</div>';


