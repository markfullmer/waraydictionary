<?php

namespace markfullmer\waraydictionary;

use markfullmer\waraydictionary\Data;

/**
 * Application cache.
 */
class Cache {

  public static function connect() {
    global $dbhost;
    global $dbname;
    global $dbuser;
    global $dbpass;
    try {
      $db = new \PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
      // set the PDO error mode to exception
      $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      $db->query('SET NAMES "utf8mb4" COLLATE utf8mb4_unicode_ci')->execute();
    }
    catch (\PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }
    return $db;
  }

  public static function get(string $string) {
    $db = self::connect();
    $stmt = $db->prepare("SELECT * FROM `cache` WHERE `id`=:string");
    $stmt->execute([':string' => $string]);
    $row = $stmt->fetch();
    if (!empty($row['value'])) {
      return $row['value'];
    }
    return FALSE;
  }

  public static function set(string $id, $value) {
    $db = self::connect();
    $sql = "INSERT INTO cache(id,value) VALUES (:id,:value)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
    $stmt->bindParam(':value', $value, \PDO::PARAM_STR);
    $stmt->execute(); 
  }

  public static function clear() {
    $db = self::connect();
    $sql = "TRUNCATE TABLE `cache`";
    $statement = $db->prepare($sql);
    $statement->execute();
  }


}
