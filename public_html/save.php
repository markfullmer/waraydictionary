<?php
session_start();

use markfullmer\waraydictionary\Db;
use markfullmer\waraydictionary\Cache;

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
Cache::clear();

header('Location: /index.php?update=1&id=' . $result);
