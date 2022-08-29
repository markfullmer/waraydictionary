<?php

namespace markfullmer\waraydictionary;

use markfullmer\waraydictionary\Data;

/**
 * Application Database connection.
 */
class Db {

  public static $keys = [
    'word',
    'one_pos',
    'one_def',
    'one_ex',
    'two_pos',
    'two_def',
    'two_ex',
    'three_pos',
    'three_def',
    'three_ex',
    'pronunciation',
    'root',
    'synonym',
  ];

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

  public static function getWord(int $id) {
    $db = self::connect();
    $stmt = $db->prepare("SELECT * FROM word WHERE id=:id");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    if (isset($row['word'])) {
      return $row;
    }
    return FALSE;
  }

  public static function getAllPos() {
    $db = self::connect();
    $stmt = $db->prepare("SELECT * FROM pos ORDER BY pos");
    $stmt->execute();
    $rows = $stmt->fetchAll();
    if (isset($rows)) {
      return $rows;
    }
    return FALSE;
  }

  public static function deletePos(int $id) {
    $db = self::connect();
    $sql = "DELETE FROM pos WHERE id=?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id]);
  }

  public static function insertPos($pos) {
    $db = self::connect();
    $sql = "INSERT INTO pos (pos) VALUES (?)";
    $db->prepare($sql)->execute([$pos]);
    return $db->lastInsertId();
  }

  public static function deleteWord(int $id) {
    $db = self::connect();
    $sql = "DELETE FROM word WHERE id=?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id]);
  }

  public static function getPosByWord(string $word) {
    $clean = Data::clean($word);
    $db = self::connect();
    $stmt = $db->prepare("SELECT one_pos FROM word WHERE BINARY word=:string");
    $stmt->execute(['string' => $clean]);
    $row = $stmt->fetch();
    if (isset($row['one_pos'])) {
      return $row['one_pos'];
    }
    return FALSE;
  }

  public static function getUncategorized(int $limitcount = 100) {
    $db = self::connect();
    $stmt = $db->prepare("SELECT * FROM word WHERE one_ex <> '' AND one_pos = '' LIMIT :limitcount");
    $stmt->bindValue(':limitcount', (int) $limitcount, \PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    if (isset($rows[0])) {
      return $rows;
    }
    return FALSE;
  }

  public static function search(string $string) {
    $clean = Data::clean($string);
    $db = self::connect();
    $stmt = $db->prepare("SELECT * FROM word WHERE BINARY word=:string");
    $stmt->execute(['string' => $clean]);
    $row = $stmt->fetch();
    if (isset($row['word'])) {
      return $row;
    }
    return FALSE;
  }

  public static function getGlossary(string $string, $sort = 'word') {
    $order = "ORDER BY `word` ASC";
    if ($sort === 'count') {
      $order = '';
    }
    if (!in_array($string, Data::$glossary)) {
      return [];
    }
    $first_letter = mb_strtolower(mb_substr(Data::clean($string), 0, 1));
    $db = self::connect();
    $stmt = $db->prepare("SELECT * FROM `word` WHERE `word` LIKE BINARY :string " . $order);
    $stmt->execute([':string' => $first_letter . '%']);
    $rows = $stmt->fetchAll();
    if (!empty($rows)) {
      return $rows;
    }
    return [];
  }

  public static function searchRoots(string $string) {
    $clean = Data::clean($string);
    $db = self::connect();
    $stmt = $db->prepare("SELECT * FROM word WHERE BINARY word=:string");
    $stmt->execute(['string' => $clean]);
    $row = $stmt->fetch();
    if (!$row || !$row['root']) {
      return FALSE;
    }
    $stmt = $db->prepare("SELECT * FROM word WHERE NOT BINARY word=:string AND `root`=:root");
    $stmt->execute([
      'string' => $clean,
      'root' => $row['root'],
    ]);
    $rows = $stmt->fetchAll();
    if (isset($rows)) {
      return $rows;
    }
    return FALSE;
  }

  /**
   * Whether or not there is an active authentication.
   */
  public static function isAuthenticated() {
    if (isset($_SESSION["authenticated"]) && $_SESSION["authenticated"] === TRUE) {
      return TRUE;
    }
    return FALSE;
  }

  public static function insertWord($post) {
    $data = [];
    foreach (self::$keys as $key) {
      $value = $post[$key] ?? '';
      $data[$key] = strip_tags($value);
    }
    $db = self::connect();
    $sql = "INSERT INTO word (";
    foreach (self::$keys as $key) {
      $delimiter = $key == end(self::$keys) ? " " : ", ";
      $sql .= "$key" . $delimiter;
    }
    $sql .= ") VALUES (";
    foreach (self::$keys as $key) {
      $delimiter = $key == end(self::$keys) ? " " : ", ";
      $sql .= ":$key" . $delimiter;
    }
    $sql .= ")";
    $db->prepare($sql)->execute($data);
    return $db->lastInsertId();
  }

  public static function updateWord($post) {
    $data = [];

    foreach (self::$keys as $key) {
      $value = $post[$key] ?? '';
      $data[$key] = strip_tags($value);
    }
    $db = self::connect();
    $sql = "UPDATE word SET ";
    foreach (self::$keys as $key) {
      $delimiter = $key == end(self::$keys) ? " " : ", ";
      $sql .= "$key=:$key" . $delimiter;
    }
    $sql .= "WHERE id =:id";
    $data['id'] = $post['id'];
    $db->prepare($sql)->execute($data);
    return (int) $post['id'];
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
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;";
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
