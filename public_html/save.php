<?php
session_start();

use markfullmer\waraydictionary\Db;

require '../vendor/autoload.php';
require '../variables.php';

if (!Db::isAuthenticated()) {
  header('Location: /edit.php?fail=auth');
  die();
}

if ($_POST['id'] === 'add') {
  $result = Db::insertWord($_POST);
}
else {
  $result = Db::updateWord($_POST);
}
header('Location: /edit.php?update=1&id=' . $result);
