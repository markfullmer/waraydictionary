<?php
session_start();

use markfullmer\waraydictionary\Db;

require '../../vendor/autoload.php';
require '../../variables.php';

if (!Db::isAuthenticated()) {
  header('Location: /index.php?fail=auth');
  die();
}

if (isset($_POST['id'])) {
  $result = Db::updatePos($_POST['id'], $_POST['pos'], $_POST['short'], $_POST['tooltip']);
}
elseif ($_POST['pos'] !== '') {
  $result = Db::insertPos($_POST['pos'], $_POST['short'], $_POST['tooltip']);
}

header('Location: /tagger/edit.php?update=1');
