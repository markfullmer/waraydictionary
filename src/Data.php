<?php

namespace markfullmer\waraydictionary;

/**
 * Application model logic.
 */
class Data {

  public static $pos = [
    'reference' => 'r',
    'modificative' => 'm',
    'predicative' => 'p',
    'bitr.r.del.' => 'p',
    'bitr.r.spnt.' => 'p',
    'int.ir.ctrl.' => 'p',
    'int.r.ntrl.impf.' => 'p',
    'int.ir.dcd.' => 'p',
    'int.r.ctrl.' => 'p',
    'intr.irrealis.prd.' => 'p',
    'intr.ir.dcd.' => 'p',
    'intr.r.n.' => 'p',
    'tr.ir.' => 'p',
    'tr.ir.imp.' => 'p',
    'tr.ir.imp.apl.' => 'p',
    'tr.ir.inty.' => 'p',
    'tr.ir.prd.' => 'p',
    'tr.ir.prt.' => 'p',
    'tr.r.' => 'p',
    'tr.r.n.' => 'p',
    'tr.r.del.' => 'p',
    'tr.r.cntrl.' => 'p',
    'numeral' => 'r',
  ];

  public static $partsOfSpeech = [
    'tr.ir.imp.(transitive.irrealis.imperative)' => '-kuha, get (some thing)',
    'tr.ir.prt. (transitive.irrealis.partitive)' => '-kuhai, get some',
    'tr.ir.inty.(transitive.irrealis.intiretive)' => '-kuhaa, get the entire thing',
    'tr.ir.prd. (transitive.irrealis.predictive)' => '-kuhaon, will get s.t.',
    'intr.irrealis.prd.(intransitive.irrealis.predictive.' => '-makuha, will get.s.t.',
    'intr.ir.dcd. (intransitive.irrealis.decided)' => '-tikuha, will get s.t.',
    'tr.r.n. (transitive.realis.neutral)' => '-nakuha, got s.t. (long syllable on ku, naKUha)',
    'intr.r.n.(intransitive.realis.neutral)' => '-nakuha, is getting s.t. (long syllables both first & 2nd, NAKUha)',
    'tr.r.cntrl.(transitive.realis.controlled)' => '-kinuha, got s.t.',
    'intr.r.cntrl. (intransitive.realis.controlled)' => '-kinuha, got s.t. (long syllable on  oth first & 2nd, KINUha) (Eastern samar variety); -kumuha, got s.t.(Leyte-Samar variety)',
    'tr.r.del.(transitive.realis.deliberate)' => '-ginkuha, got s.t.',
  ];

  public static $glossary = ['A', 'Á', 'B', 'C', 'D', 'E', 'É', 'F', 'G', 'H', 'I', 'Í', 'J', 'K', 'L', 'M', 'N', 'O', 'Ó', 'P', 'R', 'S', 'T', 'U', 'Ú', 'V', 'W', 'X', 'Y'];

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
