<?php

namespace markfullmer\waraydictionary\tests;

use markfullmer\waraydictionary\Render;
use markfullmer\waraydictionary\SpeechTagger;

/**
 * Test parts of speech.
 */
class PartOfSpeechTest {

  public static $tests = [
    ['ninang', 'Ini  hi Mak-Mak kinanhi, an babayi ikakasal ha bulan han yana nga june, gin-iimbitar ak\' usa nga ninang, an upod hi Grace.', 'r', 'ninang at end of clause'],
    ['dalagan', 'Waray hunong an dalagan.', 'r', 'Word ends sentence; Suffix is "an"'],
    ['Nagtadong', 'Nagtadong hiya ngan nag-asawa', 'p', 'Prefix is "nag"; Starts sentence'],
    ['magpapatron', 'Min, magpapatron na', 'p', 'Followed by "na"; Prefix is "mag"'],
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

  public static function test() {
    echo '<table class="default"><tr><th>Word</th><th>Sentence</th><th>Actual</th><th>Prediction</th><th>Result</th><th style="width:75px;">Confidence</th><th>Rationale</th>';
    foreach (self::$tests as $value) {
      $pos = new SpeechTagger();
      $pos->identify($value[0], $value[1], TRUE);
      $result = $pos->attributes['id'] === $value[2] ? 'PASS' : 'FAIL';
      $rules = '<ul>';
      foreach ($pos->attributes['rules'] as $rule) {
        $rules .= '<li>' . $rule . '</li>';
      }
      $rules .= '</ul>';
      echo '<tr><td>' . $value[0] . '</td><td>' . Render::highlight($value[1], $value[0]) . '</td><td>' . $value[2] . '</td><td>' . $pos->attributes['id'] . '</td><td>' . $pos->attributes['score'] . '</td><td>' . $result . '</td><td>' . $rules . '</tr>';
    }
    echo '</table>';
  }

}
