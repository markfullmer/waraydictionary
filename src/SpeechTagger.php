<?php

namespace markfullmer\waraydictionary;

use markfullmer\waraydictionary\Render;
use markfullmer\waraydictionary\Db;
use markfullmer\waraydictionary\Data;

/**
 * Application model logic.
 */
class SpeechTagger {

  public static $tests = [
    ['dalagan', 'Waray hunong an dalagan.', 'r', 'Suffix is "an"'],
    ['Nagtadong', 'Nagtadong hiya ngan nag-asawa', 'p', 'Prefix is "nag"; Starts sentence'],
    ['magpapatron', 'Min, magpapatron na', 'p', 'Prefix is "mag"'],
    ['tadong', 'han iya tadong nga balan', 'm', 'Followed by "nga"'],
    ['nánay', 'Hala gad bga baga nauli na an nánay nakadungog hia ngatitinuok an iya anak', 'r', 'Preceded by noun marker "an"'],
    ['nanay', 'Amo adto hi nanay nagmata', 'r', 'Preceded by "hi"'],
    ['Nánay', 'Nánay ka na.', 'p', 'Starts sentence'],
    ['nánay', 'Amo baya ini an nánay nga halas', 'm', 'Followed by "nga"'],
    ['táwo', 'Dii liwat pwede sumakob it\' táwo', 'r', 'Ends sentence'],
    ['matáwo', 'Ini usa nga matáwo ha akon balay amo an magigin akon sumuronod.', 'p', 'Prefix is "ma"'],
    ['táwo', 'Nakilal-an an nasabi nga táwo nga hi X nga nakita nga makuri hidapkan.', 'm', 'Followed by "nga"'],
    ['anak', 'Dadayawon nire usa nga masinugtanon nga anak', 'r', 'Ends sentence'],
    ['gin-aanak', 'Nakit-an ko hi Papa Jesus nga gin-aanak pa la hiya', 'p', '"Prefix is "gin-"'],
    ['anak', 'Ini ka han tikaiha na kay tungod iton nga an ira problema iton usa nga anak nga kabayo', 'm', 'Followed by "nga"'],
    ['ginsisiring', 'Ito hiya an ginsisiring nga may-ada healthy lifestyle', 'm', 'Followed by "nga"'],
    ['gindakóan', '...harayou kaupay han akon gindakóan', 'r', 'End of sentence; ends with "an"'],
    ['kadakó', 'Kun ano kadakó an butones sugad man an kadákó han ohales.', 'p', ''],
    ['damo', 'Linalauman an damo pa nga LGUs ug iba pa nga regulatory offices', 'm', 'Followed by "nga" ("pa" is ignored)'],
    ['napalit', 'Didto han tabo ha Palo an ak\' tawgi napalit mo intawon', 'p', 'Followed by "mo"; prefix is "na"'],
    ['ginpalit', 'Ta, kay an bucket an am\' ginpalit nga tag-80 di na la nam\' babaydan.', 'm', 'Followed by "nga"'],
  ];

  public static function identify($word, $sentence, $recur = TRUE) {
    if (mb_strlen($word) < 4) {
      return '';
    }
    $confidence = [
      'm' => 0,
      'p' => 0,
      'r' => 0,
    ];
    // Apply scoring.
    $confidence = self::locationInSentence($word, $sentence, $confidence);
    $confidence = self::precededBy($word, $sentence, $confidence);
    $confidence = self::followedBy($word, $sentence, $confidence);
    $confidence = self::prefix($word, $confidence);
    $confidence = self::suffix($word, $confidence);
    if ($recur) {
      $confidence = self::adjacentPos($word, $sentence, $confidence, 'preceding');
      $confidence = self::adjacentPos($word, $sentence, $confidence, 'following');
    }
    $scores = [];
    // Get top scorer, if there is one.
    $score = array_flip($confidence);
    $high = 0;
    $total = 0;
    foreach ($confidence as $p => $c) {
      $total = $total + $c;
      if ($c > $high) {
        $high = $c;
      }
    }
    $highcount = 0;
    foreach ($confidence as $p => $c) {
      $percent = $total == 0 ? '0' : number_format($c / $total * 100);
      if ($total < 5 && $percent > 80) {
        $percent = $percent - 30;
      }
      $scores[] = $p . ': ' . $percent . '%';
      if ($c === $high) {
        $highcount++;
      }
    }
    ksort($score);
    if ($highcount > 1) {
      $pos = '?';
    }
    elseif (count($score) === 1) {
      $pos = '?';
    }
    else {
      $pos = end($score);
    }
    return ['id' => $pos, 'score' => implode("<br />", $scores)];
  }

  public static function adjacentPos($word, $sentence, $confidence, $position = 'preceding') {
    if ($position === 'preceding') {
      $adjacent = self::preceder($word, $sentence);
      if (!$adjacent) {
        return $confidence;
      }
    }
    else {
      $adjacent = self::follower($word, $sentence);
      if (!$adjacent) {
        return $confidence;
      }
    }

    if ($adjacentPos = Db::getPosByWord($adjacent)) {
      $pos = Data::getPosShort($adjacentPos);
      if ($pos === 'p') {
        $confidence['m'] = $confidence['m'] + 3;
        $confidence['r'] = $confidence['r'] + 3;
      }
      elseif ($pos === 'm') {
        $confidence['p'] = $confidence['p'] + 3;
        $confidence['r'] = $confidence['r'] + 3;
      }
      elseif ($pos === 'r') {
        $confidence['p'] = $confidence['p'] + 3;
        $confidence['m'] = $confidence['m'] + 3;
      }
    }
    else {
      // The word isn't in the dictionary. Try to guess the part of speech.
      // Fourth parameter is to prevent infinite recursion.
      $pos = self::identify($adjacent, $sentence, FALSE);
      if ($pos) {
        if ($pos === 'p') {
          $confidence['m']++;
          $confidence['r']++;
        }
        elseif ($pos === 'm') {
          $confidence['p']++;
          $confidence['r']++;
        }
        elseif ($pos === 'r') {
          $confidence['m']++;
          $confidence['p']++;
        }
      }
    }
    return $confidence;
  }

  public static function prefix($word, $confidence) {
    $found = FALSE;
    foreach (self::$highConfidencePredicativePrefix as $prefix) {
      if (self::startsWith($word, $prefix)) {
        $confidence['p'] = $confidence['p'] + 4;
        $found = TRUE;
        break;
      }
    }
    if (!$found) {
      foreach (self::$lowConfidencePredicativePrefix as $prefix) {
        if (self::startsWith($word, $prefix)) {
          $confidence['p'] = $confidence['p'] + 1;
          $found = TRUE;
          break;
        }
      }
    }
    if (!$found) {
      foreach (self::$lowConfidenceModificativePrefix as $prefix) {
        if (self::startsWith($word, $prefix)) {
          $confidence['m']++;
          $found = TRUE;
          break;
        }
      }
    }
    return $confidence;
  }

  public static function suffix($word, $confidence) {
    // If the word ends with a [low confidence modificative suffix], give 1 point to "modificative" and remove 1 point from "predicative" (karuyagon)
    $found = FALSE;
    foreach (self::$lowConfidencePredicativeSuffix as $suffix) {
      if (self::endsWith($word, $suffix)) {
        $confidence['p']++;
        $found = TRUE;
        break;
      }
    }
    if (!$found) {
      foreach (self::$lowConfidenceModificativeSuffix as $suffix) {
        if (self::endsWith($word, $suffix)) {
          $confidence['m'] = $confidence['m'] + 2;
          $confidence['r'] = $confidence['r'] + 1;
          $found = TRUE;
          break;
        }
      }
    }
    if (!$found) {
      foreach (self::$lowConfidenceReferentialSuffix as $prefix) {
        if (self::endsWith($word, $prefix)) {
          $confidence['r']++;
          $found = TRUE;
          break;
        }
      }
    }
    return $confidence;
  }

  public static function followedBy($word, $sentence, $confidence) {
    $follower = self::follower($word, $sentence);
    // 1. If the word is followed by a [high confidence predicative follower], give 3 points to "predicative" ("Min, *magpapatron na*")
    // 2. If the word is followed by a [low confidence predicative follower], give 1 points to "predicative" ("*Nagtadong hiya* ngan nag-asawa")
    // 3. If the word is followed by "nga", give 1 point to "modificative" ("han iya *tadong nga* balan", "han burod nga lalaki"; contraindication: "waray *palad nga* maupay" [referential])
    if (in_array($follower, self::$highConfidencePredicativeFollower)) {
      $confidence['p'] = $confidence['p'] + 3;
    }
    elseif (in_array($follower, self::$lowConfidencePredicativeFollower)) {
      $confidence['p'] = $confidence['p'] + 1;
    }
    elseif (in_array($follower, self::$pronouns)) {
      $confidence['p'] = $confidence['p'] + 1;
    }
    elseif ($follower === "nga") {
      $confidence['m'] = $confidence['m'] + 5;
      $confidence['r'] = $confidence['r'] + 1;
    }
    return $confidence;
  }

  public static function precededBy($word, $sentence, $confidence) {
    $preceder = self::preceder($word, $sentence);
    // 1. If the word is preceded by a [high confidence referential preceder] give 3 points to "referential"
    // 2. Elseif the word is preceded by a [low confidence referential preceder] give 1 point to "referential" ("Cebu *an tinadong* )
    // 3. If the word is preceded by "nga", give 1 point to "referential" ("Niyan may usa *nga padi.*")
    if (in_array($preceder, self::$highConfidenceReferentialPreceder)) {
      $confidence['r'] = $confidence['r'] + 3;
    }
    elseif (in_array($preceder, self::$lowConfidenceReferentialPreceder)) {
      $confidence['r'] = $confidence['r'] + 1;
    }
    elseif (in_array($preceder, self::$pronouns)) {
      $confidence['r'] = $confidence['r'] + 2;
    }
    elseif ($preceder === 'nga') {
      $confidence['r'] = $confidence['r'] + 1;
    }
    // 4. If the word is preceded by a [high confidence modificative preceder], give 3 points to "modificative" ("mas matadong")
    if (in_array($preceder, self::$highConfidenceModificativePreceder)) {
      $confidence['m'] = $confidence['m'] + 3;
    }
    return $confidence;
  }

  public static $highConfidencePredicativePrefix = [
    'nagka',
    'pagka',
    'gin-',
    'ma-',
    'mag-',
    'pag-',
    'naka',
    'maka',
    'magp',
    'igpa',
    'magpa',
    'ginpa',
    'pagpa',
  ];

  public static $lowConfidencePredicativePrefix = [
    'pag',
    'nag',
    'gin',
    'in',
    'na',
    'ti',
    'ma',
    'ka',
    'mu',
  ];

  public static $lowConfidenceModificativePrefix = [
    'ginki',
    'kini',
  ];

  public static $highConfidencePredicativeFollower = [
    'mga',
    'na',
  ];

  public static $lowConfidencePredicativeFollower = [
    'hit',
    'an',
    'han',
    'hin',
    'ini',
    'inin',
    'hini',
    'didi',
    'hito',
    'man',
    'ha',
  ];

  public static $highConfidenceReferentialPreceder = [
    'mga',
    'na',
  ];

  public static $lowConfidenceReferentialPreceder = [
    'hit',
    'an',
    'ha',
    'han',
    'hin',
    'ini',
    'hini',
    'didi',
    'hito',
    'man',
  ];

  public static $pronouns = [
    'ak',
    'ako',
    'ka',
    'hiya',
    'iya',
    'ko',
    'hi',
    'kita',
    'akon',
    'hira',
    'kami',
    'adi',
    'ira',
    'niya',
    'niyo',
    'mo',
    'kamo',
    'iyo',
    'ta',
    'nira',
    'nakon',
    'sira',
    'amon',
    'am',
  ];

  public static $highConfidenceModificativePreceder = [
    'permi',
    'mas',
  ];

  public static $lowConfidenceModificativeSuffix = [
    'on',
  ];

  public static $lowConfidenceReferentialSuffix = [
    'an',
  ];

  public static $lowConfidencePredicativeSuffix = [
    'han',
    'hon',
    'i',
  ];

  public static function locationInSentence($word, $sentence, $confidence) {
    // If the word is located at the beginning of the sentence, give 3 points to "predicative" ("*Nagtadong* hiya ngan nag-asawa")
    // Else if the word is located at the end of the sentence, give 1 point to "referential ("Waray hunong *an dalagan.*")
    if (self::beginsSentence($word, $sentence)) {
      $confidence['p'] = $confidence['p'] + 3;
    }
    if (self::endsSentence($word, $sentence)) {
      $confidence['r'] = $confidence['r'] + 3;
    }
    return $confidence;
  }

  public static function test() {
    echo '<table border="1"><tr><th>Word</th><th>Sentence</th><th>Actual</th><th>Prediction</th><th>Result</th><th style="width:75px;">Confidence</th><th>Rationale</th>';
    foreach (self::$tests as $value) {
      $pos = self::identify($value[0], $value[1], TRUE);
      $result = $pos['id'] === $value[2] ? 'PASS' : 'FAIL';
      echo '<tr style="border: 1px solid black;"><td>' . $value[0] . '</td><td>' . Render::highlight($value[1], $value[0]) . '</td><td>' . $value[2] . '</td><td>' . $pos['id'] . '</td><td>' . $pos['score'] . '</td><td>' . $result . '</td><td>' . $value[3] . '</tr>';
    }
    echo '</table>';
  }

  /**
   * Split on word boundaries.
   */
  public static function tokenize($string) {
    // Remove URLs.
    $regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@";
    $string = preg_replace($regex, ' ', $string);

    // This regex is similar to any non-word character (\W),
    // but retains the following symbols: @'#$%
    $tokens = preg_split("/\s|[,.!?:*&;\"()\[\]_+=”]/", $string);
    $result = [];
    $strip_chars = ":,.!&\?;-\”'()^*";
    foreach ($tokens as $token) {
      if (mb_strlen($token) == 1) {
        if (!in_array($token, ["a", "i", "I", "A"])) {
          continue;
        }
      }
      $token = mb_strtolower(trim($token, $strip_chars));
      if (in_array($token, ['la', 'pa', 'kun', 'ano', 'gad']) && $token !== end($tokens)) {
        continue;
      }
      if ($token) {
        $result[] = $token;
      }
    }
    return $result;
  }

  public static function beginsSentence($word, $sentence) {
    $tokens = self::tokenize($sentence);
    if (isset($tokens[0]) && $tokens[0] === mb_strtolower($word)) {
      return TRUE;
    }
    return FALSE;
  }

  public static function endsSentence($word, $sentence) {
    $tokens = self::tokenize($sentence);
    $end = end($tokens);
    if ($end === mb_strtolower($word)) {
      return TRUE;
    }
    return FALSE;
  }

  public static function preceder($word, $sentence) {
    $tokens = self::tokenize($sentence);
    foreach ($tokens as $key => $token) {
      if ($key === 0) {
        continue;
      }
      $previous = $key;
      $previous--;
      if ($token === mb_strtolower($word)) {
        return $tokens[$previous];
      }
    }
    return '';
  }

  public static function follower($word, $sentence) {
    $tokens = self::tokenize($sentence);
    foreach ($tokens as $key => $token) {
      $next = $key;
      $next++;
      if ($token === mb_strtolower($word) && isset($tokens[$next])) {
        return $tokens[$next];
      }
    }
    return '';
  }

  public static function startsWith($word, $prefix) {
    if (stripos($word, $prefix) === 0) {
      return TRUE;
    }
    return FALSE;
  }

  public static function endsWith($word, $suffix) {
    if (strpos(strrev($word), strrev($suffix)) === 0) {
      return TRUE;
    }
    return FALSE;
  }

}
