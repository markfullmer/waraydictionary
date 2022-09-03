<?php
session_start();

use markfullmer\waraydictionary\Db;

require '../../vendor/autoload.php';
require '../../variables.php';

if (!Db::isAuthenticated()) {
  header('Location: /index.php?fail=auth');
  die();
}

if ($_POST['pos'] !== '') {
  $result = Db::insertPos($_POST['pos'], $_POST['short']);
}

header('Location: /tagger/edit.php?update=1');
