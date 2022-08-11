<?php
session_start();

require '../vendor/autoload.php';
require '../variables.php';

$_SESSION["authenticated"] = FALSE;

if (isset($_POST['id']) && !isset($POST['login'])) {
  if ($_POST['username'] === $username && (md5($_POST['password']) . 23) === $password) {
    $_SESSION["authenticated"] = TRUE;
  }
}

header('Location: /index.php');
