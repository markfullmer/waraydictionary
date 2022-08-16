<?php

namespace markfullmer\waraydictionary;

use markfullmer\waraydictionary\Data;
use markfullmer\waraydictionary\Db;

/**
 * Application View logic.
 */
class Render {

  /**
   * Display a dictionary entry.
   */
  public static function entry($row) {
    $output = [];
    $output[] = '<strong>' . $row['word'] . '</strong>';
    if (Db::isAuthenticated()) {
      $output[] = '<a href="/edit.php?id=' . $row['id'] . '">edit</a>';
    }
    if (!empty($row['pronunciation'])) {
      $output[] = '[' . $row['pronunciation'] . ']';
    }
    if (!empty($row['one_pos'])) {
      if (!empty($row['two_pos'])) {
        $output[] = '<strong><1' . Data::getPosShort($row['one_pos']) . '></strong>';
      }
      else {
        $output[] = '&lt;' . $row['one_pos'] . '&gt;';
      }
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
      if (!empty($row['two_ex'] || !empty($row['two_pos']))) {
        $output[] = '<strong>1</strong>';
      }
      if (!empty($row['one_def']) && !empty($row['two_def'])) {
        $output[] = '<em>(' . $row['one_def'] . ')</em> ';
      }
      if (!empty($row['one_ex'])) {
        $output[] = self::highlight($row['one_ex'], $row['word']);
      }
      $output[] = '</li>';
    }
    if (!empty($row['two_ex']) || !empty($row['two_def'])) {
      $output[] = '<li><strong>2</strong> ';
      if (!empty($row['two_def'])) {
        $output[] = '<em>(' . $row['two_def'] . ')</em> ';
      }
      if (!empty($row['two_ex'])) {
        $output[] = self::highlight($row['two_ex'], $row['word']);
      }
      $output[] = '</li>';
    }
    if (!empty($row['three_ex']) || !empty($row['three_def'])) {
      $output[] = '<li><strong>3</strong> ';
      if (!empty($row['three_def'])) {
        $output[] = '<em>(' . $row['three_def'] . ')</em> ';
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

  public static function glossary() {
    $output = [];
    foreach (Data::$glossary as $key) {
      $output[] = '<a href="./index.php?glossary=' . $key . '">' . $key . '</a> | ';
    }
    return implode($output);
  }

  /**
   * Highlight a word in context.
   */
  public static function highlight($context, $word) {
    // For words with 3 or more letters...
    // Find the word/root, allowing for spaces,hyphens or word-characters adjacent
    $re = '/(\s|\w+-|-|\w+|^)(' . $word . ')(\s|-?\w+|\.,\?)/mi';
    if (mb_strlen($word, 'UTF-8') < 4) {
      // For words of three letters or less, do not include other letters.
      $re = '/(\s|-|^)(' . $word . ')(\s|-|\.|,|\?)/mi';
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
    foreach (array_keys(Data::$pos) as $label) {
      $output .= '<option value="' . $label . '"';
      if ($label === $selected) {
        $output .= ' selected="selected"';
      }
      $output .= '>' . $label . '</option>';
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
