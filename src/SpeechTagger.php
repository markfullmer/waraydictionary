<?php

namespace markfullmer\waraydictionary;

use markfullmer\waraydictionary\Db;
use markfullmer\waraydictionary\Data;
use markfullmer\waraydictionary\MorphoSyntaxData;

/**
 * Application model logic.
 */
class SpeechTagger {

  public $attributes = [
    'id' => '?',
    'count' => [
      'm' => 0,
      'p' => 0,
      'r' => 0,
    ],
    'score' => 'N/A',
    'rules' => [],
  ];

  public function identify($word, $sentence, $recur = TRUE) {
    // Words of 3 characters or less are unreliable. Skip.
    if (mb_strlen($word) < 4) {
      return $this->attributes;
    }
    // Apply scoring.
    $this->evaluateLocation($word, $sentence);
    $this->evaluatePreceder($word, $sentence);
    $this->evaluateFollower($word, $sentence);
    $this->evaluatePrefix($word);
    $this->evaluateSuffix($word);
    if ($recur) {
      $this->evaluateAdjacentPos($word, $sentence, 'preceding');
      $this->evaluateAdjacentPos($word, $sentence, 'following');
    }
    $this->applyScoring();
  }

  public function applyScoring() {
    foreach ($this->attributes['rules'] as $rule) {
      if (!isset(MorphoSyntaxData::$rules[$rule])) {
        print_r('Rule "' . $rule . '" was not found.');
        die();
      }
      foreach (MorphoSyntaxData::$rules[$rule] as $pos => $score) {
        $this->attributes['count'][$pos] = $this->attributes['count'][$pos] + $score;
      }
    }
    // Get top scorer, if there is one.
    $collapsed = array_flip($this->attributes['count']);
    ksort($collapsed);
    $high = 0;
    $total = 0;
    $highcount = 0;
    $scores = [];
    foreach ($this->attributes['count'] as $pos => $score) {
      $total = $total + $score;
      if ($score > $high) {
        $high = $score;
      }
    }
    foreach ($this->attributes['count'] as $pos => $score) {
      $percent = $total == 0 ? '0' : number_format($score / $total * 100);
      if ($total < 5 && $percent > 80) {
        $percent = $percent - 30;
      }
      $scores[] = $pos . ': ' . $percent . '%';
      if ($score === $high) {
        $highcount++;
      }
    }
    if ($highcount > 1) {
      $this->attributes['id'] = '?';
    }
    elseif (count($collapsed) === 1) {
      $this->attributes['id'] = '?';
    }
    else {
      $this->attributes['id'] = end($collapsed);
    }
    $this->attributes['score'] = implode('<br />', $scores);
  }

  public function evaluateLocation($word, $sentence) {
    if (self::beginsSentence($word, $sentence)) {
      $this->attributes['rules'][] = 'Word begins clause';
    }
    if (self::endsSentence($word, $sentence)) {
      $this->attributes['rules'][] = 'Word ends clause';
    }
  }

  public function evaluatePreceder($word, $sentence) {
    $preceder = self::getPreceder($word, $sentence);
    if (in_array($preceder, MorphoSyntaxData::$highConfidenceReferentialPreceder)) {
      $this->attributes['rules'][] = 'Preceding word likely indicates target is referential';
    }
    elseif (in_array($preceder, MorphoSyntaxData::$lowConfidenceReferentialPreceder)) {
      $this->attributes['rules'][] = 'Preceding word suggests target is referential';
    }
    elseif (in_array($preceder, MorphoSyntaxData::$pronouns)) {
      $this->attributes['rules'][] = 'Pronoun precedes word';
      // $confidence['r'] = $confidence['r'] + 2;
    }
    elseif ($preceder === 'nga') {
      $this->attributes['rules'][] = 'Word is preceded by "nga"';
    }
    if (in_array($preceder, MorphoSyntaxData::$highConfidenceModificativePreceder)) {
      $this->attributes['rules'][] = 'Preceding word likely indicates target is modificative';
    }
  }


  public function evaluateFollower($word, $sentence) {
    $follower = self::getFollower($word, $sentence);
    if (in_array($follower, MorphoSyntaxData::$highConfidencePredicativeFollower)) {
      $this->attributes['rules'][] = 'Following word likely indicates target is predicative';
    }
    elseif (in_array($follower, MorphoSyntaxData::$lowConfidencePredicativeFollower)) {
      $this->attributes['rules'][] = 'Following word suggests target is predicative';
    }
    elseif (in_array($follower, MorphoSyntaxData::$pronouns)) {
      $this->attributes['rules'][] = 'Word is followed by a pronoun';
    }
    elseif ($follower === "nga") {
      $this->attributes['rules'][] = 'Word is followed by "nga"';
    }
  }

  public function evaluateAdjacentPos($word, $sentence, $position = 'preceding') {
    if ($position === 'preceding') {
      $adjacent = self::getPreceder($word, $sentence);
    }
    else {
      $adjacent = self::getFollower($word, $sentence);
    }
    if (!$adjacent) {
      return;
    }

    if ($adjacentPos = Db::getPosByWord($adjacent)) {
      $pos = Data::getPosShort($adjacentPos);
      if ($pos === 'p') {
        $this->attributes['rules'][] = 'Predicative is ' . $position . ' target word';
      }
      elseif ($pos === 'm') {
        $this->attributes['rules'][] = 'Modificative is ' . $position . ' target word';
      }
      elseif ($pos === 'r') {
        $this->attributes['rules'][] = 'Referential is ' . $position . ' target word';
      }
    }
    else {
      // The word isn't in the dictionary. Try to guess the part of speech.
      // Second parameter is to prevent infinite recursion.
      $pos = $this->identify($adjacent, $sentence, FALSE);
      if ($pos) {
        if ($pos === 'p') {
          $this->attributes['rules'][] = 'Predicative is ' . $position . ' target word';
        }
        elseif ($pos === 'm') {
          $this->attributes['rules'][] = 'Modificative is ' . $position . ' target word';
        }
        elseif ($pos === 'r') {
          $this->attributes['rules'][] = 'Referential is ' . $position . ' target word';
        }
      }
    }
  }

  public function evaluatePrefix($word) {
    $found = FALSE;
    foreach (MorphoSyntaxData::$highConfidencePredicativePrefix as $prefix) {
      if (self::startsWith($word, $prefix)) {
        $this->attributes['rules'][] = 'Prefix likely indicates predicative';
        $found = TRUE;
        break;
      }
    }
    if (!$found) {
      foreach (MorphoSyntaxData::$lowConfidencePredicativePrefix as $prefix) {
        if (self::startsWith($word, $prefix)) {
          $this->attributes['rules'][] = 'Prefix suggests predicative';
          $found = TRUE;
          break;
        }
      }
    }
    if (!$found) {
      foreach (MorphoSyntaxData::$lowConfidenceModificativePrefix as $prefix) {
        if (self::startsWith($word, $prefix)) {
          $this->attributes['rules'][] = 'Prefix suggests modificative';
          $found = TRUE;
          break;
        }
      }
    }
  }

  public function evaluateSuffix($word) {
    $found = FALSE;
    foreach (MorphoSyntaxData::$lowConfidencePredicativeSuffix as $suffix) {
      if (self::endsWith($word, $suffix)) {
        $this->attributes['rules'][] = 'Suffix suggests predicative';
        $found = TRUE;
        break;
      }
    }
    if (!$found) {
      foreach (MorphoSyntaxData::$lowConfidenceModificativeSuffix as $suffix) {
        if (self::endsWith($word, $suffix)) {
          $this->attributes['rules'][] = 'Suffix suggests modificative, less likely referential';
          $found = TRUE;
          break;
        }
      }
    }
    if (!$found) {
      foreach (MorphoSyntaxData::$lowConfidenceReferentialSuffix as $prefix) {
        if ($this->endsWith($word, $prefix)) {
          $this->attributes['rules'][] = 'Suffix suggests referential';
          $found = TRUE;
          break;
        }
      }
    }
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

  public static function getPreceder($word, $sentence) {
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

  public static function getFollower($word, $sentence) {
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
