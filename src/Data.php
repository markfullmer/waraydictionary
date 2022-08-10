<?php

namespace markfullmer\waraydictionary;

/**
 * Application model logic.
 */
class Data {

  public static $pos = [
    'referent' => 'r',
    'modifier' => 'm',
    'predicate' => 'p',
    'tr.ir.imp' => 'p',
    'tr.ir.imp.apl' => 'p',
  ];

  public static function clean($string) {
    $string = (string) $string;
    return strip_tags($string);
  }

  /**
   * Get available parts of speech
   */
  public static function getPosShort($long) {
    if (isset(self::$pos[$long])) {
      return self::$pos[$long];
    }
    return $long;
  }

}
