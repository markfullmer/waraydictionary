<?php

namespace markfullmer\waraydictionary;

/**
 * Application Database connection.
 */
class Db {

  public static function connect() {
    global $dbhost;
    global $dbname;
    global $dbuser;
    global $dbpass;
    try {
      $db = new \PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
      // set the PDO error mode to exception
      $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      $db->query('SET NAMES "utf8"')->execute();
    }
    catch (\PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }
    return $db;
  }

  /**
   * Whether or not there is an active authentication.
   */
  public static function isAuthenticated() {
    return TRUE;
  }

  /**
   * Create database tables.
   */
  public static function install() {
    $db = self::connect();
    $sql = [];

    $sql[] = "CREATE TABLE IF NOT EXISTS `word` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `word` varchar(64) NOT NULL,
      `one_pos` varchar(64) NOT NULL,
      `one_def` varchar(512) NOT NULL,
      `one_ex` varchar(512) NOT NULL,
      `two_pos` varchar(64) NOT NULL,
      `two_def` varchar(512) NOT NULL,
      `two_ex` varchar(512) NOT NULL,
      `three_pos` varchar(64) NOT NULL,
      `three_def` varchar(512) NOT NULL,
      `three_ex` varchar(512) NOT NULL,
      `synonym` varchar(64) NOT NULL,
      `root` varchar(64) NOT NULL,
      `pronunciation` varchar(64) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
    foreach ($sql as $table) {
      try {
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, FALSE);
        $stmt = $db->prepare($table);
        $stmt->execute();
      }
      catch (\PDOException $e) {
        echo $e->getMessage();
      }
    }
  }

}
