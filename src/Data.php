<?php

namespace markfullmer\waraydictionary;

/**
 * Application model logic.
 */
class Data {

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

  public static $glossary = ['A', 'Â', 'Á', 'À', 'B', 'C', 'D', 'E', 'É', 'Ê', 'È', 'F', 'G', 'H', 'I', 'Í', 'Î', 'Ì', 'J', 'K', 'L', 'M', 'N', 'O', 'Ó', 'Ô', 'Ò', 'P', 'R', 'S', 'T', 'U', 'Ú', 'Û', 'Ù', 'V', 'W', 'X', 'Y'];

  public static function clean($string) {
    $string = (string) $string;
    return strip_tags($string);
  }

}
