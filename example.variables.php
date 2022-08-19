<?php

$dbhost = "database";
$dbuser = "lamp";
$dbpass = "lamp";
$dbname = "lamp";
$username = '';
// This uses the md5() algorithm, plus a salt.
$password = '';
$cache = TRUE;

try {
  $db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
  // Wet the PDO error mode to exception.
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->query('SET NAMES "utf8"')->execute();
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
