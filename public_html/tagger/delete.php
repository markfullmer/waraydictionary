<?php
session_start();

use markfullmer\waraydictionary\Db;

require '../../vendor/autoload.php';
require '../../variables.php';

if (!Db::isAuthenticated()) {
  header('Location: /index.php?fail=auth');
  die();
}
if (isset($_REQUEST['id'])) {
  Db::deletePos($_REQUEST['id']);
  header('Location: /tagger/edit.php?deleted=1');
}


