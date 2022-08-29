<?php

namespace markfullmer\waraydictionary;

use markfullmer\waraydictionary\Data;
use markfullmer\waraydictionary\Db;

/**
 * Application View logic.
 */
class Render {

  public static function messages($get) {
    $output = [];
    if (isset($get['auth'])) {
      if ($get['auth'] === 'fail') {
        $output[] = 'Unauthorized access.';
      }
    }
    if (isset($get['update'])) {
      $word = Db::getWord($get['id']);
      $output[] = 'The word "' . $word['word'] . '" was successfully updated.';
    }
    if (!empty($output)) {
      return '<div class="blurb-box">' . implode('<br />', $output) . '</div>';
    }
  }

  public static function partOfSpeech(array $pos) {
    $output = [];
    switch ($pos['id']) {
      case 'm':
        $long = 'Modificative';
        break;
      case 'p':
        $long = 'Predicative';
        break;
      case 'r':
        $long = 'Referential';
        break;
      default:
        $long = 'Indeterminable';
        break;
    }
    $rules = '<ul>';
    foreach ($pos['rules'] as $rule) {
      $rules .= '<li>' . $rule . '</li>';
    }
    $rules .= '</ul>';
    $output[] = '<strong>Identified part of speech in sentence:</strong> ' . $long;
    $output[] = '<strong>Confidence:</strong> ' . $pos['score'];
    $output[] = '<strong>Rationale:</strong> ' . $rules;
    return implode('<br />', $output);
  }

  /**
   * Display a dictionary entry.
   */
  public static function entry($row) {
    $output = [];
    $output[] = '<strong>' . $row['word'] . '</strong>';
    if (Db::isAuthenticated()) {
      $output[] = '<a href="/edit.php?id=' . $row['id'] . '">edit</a>';
      $output[] = '&nbsp;&nbsp;&nbsp;<a href="/delete.php?id=' . $row['id'] . '">delete</a>';
    }
    if (!empty($row['pronunciation'])) {
      $output[] = '[' . $row['pronunciation'] . ']';
    }
    if (!empty($row['one_pos'])) {
      $output[] = '<strong><1' . Data::getPosShort($row['one_pos']) . '></strong>';
    }
    if (!empty($row['two_pos'])) {
      $output[] = '<strong><2' . Data::getPosShort($row['two_pos']) . '></strong>';
    }
    if (!empty($row['three_pos'])) {
      $output[] = '<strong><3' . Data::getPosShort($row['three_pos']) . '></strong>';
    }
    if (!empty($row['one_def']) && empty($row['two_def'])) {
      $output[] = '<em>' . $row['one_def'] . '</em>';
    }
    if (!empty($row['root'])) {
      $output[] = '[root <strong>' . $row['root'] . '</strong>]';
    }
    $output[] = '<ul style="list-style-type:none;">';
    if (!empty($row['one_ex'])) {
      $output[] = '<li>';
      if (!empty($row['two_ex'] || !empty($row['two_pos'] || (!empty($row['two_def']))))) {
        $output[] = '<strong>1</strong>';
      }
      if (!empty($row['one_def']) && !empty($row['two_def'])) {
        $output[] = '<em>[' . $row['one_def'] . ']</em> ';
      }
      if (!empty($row['one_ex'])) {
        $output[] = self::highlight($row['one_ex'], $row['word']);
      }
      $output[] = '</li>';
    }
    if (!empty($row['two_ex']) || !empty($row['two_def'])) {
      $output[] = '<li><strong>2</strong> ';
      if (!empty($row['two_def'])) {
        $output[] = '<em>[' . $row['two_def'] . ']</em> ';
      }
      if (!empty($row['two_ex'])) {
        $output[] = self::highlight($row['two_ex'], $row['word']);
      }
      $output[] = '</li>';
    }
    if (!empty($row['three_ex']) || !empty($row['three_def'])) {
      $output[] = '<li><strong>3</strong> ';
      if (!empty($row['three_def'])) {
        $output[] = '<em>[' . $row['three_def'] . ']</em> ';
      }
      if (!empty($row['two_ex'])) {
        $output[] = self::highlight($row['three_ex'], $row['word']);
      }
      $output[] = '</li>';
    }
    if (!empty($row['synonym'])) {
      $output[] = '<li>[see also <strong>' . $row['synonym'] . '</strong>]</li>';
    }
    $output[] = '</ul>';
    return implode(" ", $output);
  }

  public static function glossary($sort = 'word') {
    $output = [];
    foreach (Data::$glossary as $key) {
      $output[] = '<a href="./index.php?glossary=' . $key . '&sort=' . $sort .'">' . $key . '</a> | ';
    }
    return implode($output);
  }

  public static function getPosLong($pos) {
    switch ($pos) {
      case 'p':
        return 'predicative';
      case 'm':
        return 'modificative';
      case 'r':
        return 'referential';
      default:
        return $pos;
    }
  }

  public static function tags($tagged) {
    $output = [];
    $output[] = '<table class="default"><tr>';
    foreach ($tagged as $data) {
      $pos = self::getPosLong($data[1]);
      $output[] = '<td style="text-align:center;"><strong>' . $data[0] . '</strong><br />' . $pos . '</td>';
    }
    $output[] = '</tr></table>';
    return implode('', $output);
  }

  /**
   * Highlight a word in context.
   */
  public static function highlight($context, $word) {
    // For words with 3 or more letters...
    // Find the word/root, allowing for spaces,hyphens or word-characters adjacent
    $word = str_replace("/"," ", $word);
    $re = "/(\s|\w+-|-|\w+|^)(" . $word . ")(\s?|-?\w+|\.,\?)/mi";
    if (mb_strlen($word, 'UTF-8') < 4) {
      // For words of three letters or less, do not include other letters.
      $re = "/(\s|-|^)(" . $word . ")(\s?|-|\.|,|\?)/mi";
    }
    $subst = '<u>$0</u>';
    return preg_replace($re, $subst, $context);
  }

  /**
   * Display linked cognates.
   */
  public static function cognates($rows) {
    $output = [];
    foreach ($rows as $row) {
      $output[] = '<a href="./index.php?word=' . $row['word'] . '">' . $row['word'] . '</a>';
    }
    return implode(", ", $output);
  }

  /**
   * Get available parts of speech
   */
  public static function getPosOptions(string $selected) {
    $output = '<option value="">--Select--</option>';
    foreach (Db::getAllPos() as $pos) {
      $output .= '<option value="' . $pos['pos'] . '"';
      if ($pos['pos'] === $selected) {
        $output .= ' selected="selected"';
      }
      $output .= '>' . $pos['pos'] . '</option>';
    }
    return $output;
  }

  public static function loginForm() {
    if (Db::isAuthenticated()) {
      return '<button type="submit" name="logout">Sign out</button>';
    }
    else {
      return '
    <input class="span2" type="text" name="username" placeholder="Email">
    <input class="span2" type="password" name="password" placeholder="Password">
    <input type="checkbox" class="intransigent" name="login" />
    <input type="checkbox" class="intransigent" name="id" checked="checked" />
    <button type="submit" name="submit">Sign in</button>';
    }
  }
}
