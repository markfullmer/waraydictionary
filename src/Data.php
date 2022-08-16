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
    'tr.ir.imp.' => 'p',
    'tr.ir.imp.apl.' => 'p',
    'tr.ir.' => 'p',
    'tr.r.' => 'p',
    'tr.r.ctrl.' => 'p',
    'int.r.ctrl.' => 'p',
    'tr.r.del.' => 'p',
    'int.ir.ctrl.' => 'p',
    'bitr.r.spnt.' => 'p',
    'bitr.r.del.' => 'p',
    'int.r.ntrl. impf.' => 'p',
    'int.ir.dcd.' => 'p',
    'int.r.ctrl.' => 'p',
    ];

  public static $glossary = ['A', 'Á', 'B', 'C', 'D', 'E', 'É', 'F', 'G', 'H', 'I', 'Í', 'J', 'K', 'L', 'M', 'N', 'O', 'Ó', 'P', 'Q', 'R', 'S', 'T', 'U', 'Ú', 'V', 'W', 'X', 'Y', 'Z'];

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
    return mb_substr($long, 0, 1);
  }

}
