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
  public static function buildEntry($row) {
    $output = [];
    $output[] = '<strong>' . $row['word'] . '</strong>';
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
    if (!empty($row['one_def'])) {
      $output[] = '<em>' . Data::getPosShort($row['one_def']) . '</em>';
    }
    if (Db::isAuthenticated()) {
      $output[] = '<a href="/edit.php?id=' . $row['id'] . '">edit</a>';
    }
    if (!empty($row['root'])) {
      $output[] = '<br /><em>Root form: ' . $row['root'] . '</em>';
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
    $output[] = '</ul>';
    return implode(" ", $output);
  }

  /**
   * Get available parts of speech
   */
  public static function getPosOptions(string $selected) {
    foreach (array_keys(Data::$pos) as $label) {
      $output .= '<option value="' . $label . '"';
      if ($label === $selected) {
        $output .= ' selected="selected"';
      }
      $output .= '>' . $label . '</option>';
    }
    return $output;
  }
}
