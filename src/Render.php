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
    if (!empty($row['pronunciation'])) {
      $output[] = '[' . $row['pronunciation'] . ']';
    }
    if (!empty($row['one_pos'])) {
      if (!empty($row['two_pos'])) {
        $output[] = '<strong><1' . Data::getPosShort($row['one_pos']) . '></strong>';
      }
      else {
        $output[] = '<em>' . $row['one_pos'] . '</em>';
      }
    }
    if (!empty($row['two_pos'])) {
      $output[] = '<strong><2' . Data::getPosShort($row['two_pos']) . '></strong>';
    }
    if (!empty($row['three_pos'])) {
      $output[] = '<strong><3' . Data::getPosShort($row['three_pos']) . '></strong>';
    }
    if (!empty($row['one_def'])) {
      $output[] = '<em>' . Data::getPosShort($row['one_def']) . '</em>';
    }
    if (!empty($row['root'])) {
      $output[] = '[root <strong>' . $row['root'] . '</strong>]';
    }
    if (Db::isAuthenticated()) {
      $output[] = '<a href="/edit.php?id=' . $row['id'] . '">edit</a>';
    }
    $output[] = '<ul style="list-style-type:none;">';
    if (!empty($row['one_ex'])) {
      $output[] = '<li><strong>1</strong> ' . $row['one_ex'] . '</li>';
    }
    if (!empty($row['two_ex'])) {
      $output[] = '<li><strong>2</strong> ' . $row['two_ex'] . '</li>';
    }
    if (!empty($row['three_ex'])) {
      $output[] = '<li><strong>3</strong> ' . $row['three_ex'] . '</li>';
    }
    if (!empty($row['synonym'])) {
      $output[] = '<li>[see also <strong>' . $row['synonym'] . '</strong>]</li>';
    }
    $output[] = '</ul>';
    return implode(" ", $output);
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
