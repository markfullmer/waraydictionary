<?php

namespace markfullmer\waraydictionary;

class MorphoSyntaxData {

  public static $rules = [
    'Predicative is preceding target word' => [
      'm' => 3,
      'r' => 3,
      'p' => 0,
    ],
    'Modificative is preceding target word' => [
      'm' => 0,
      'r' => 3,
      'p' => 3,
    ],
    'Referential is preceding target word' => [
      'm' => 3,
      'r' => 0,
      'p' => 3,
    ],
    'Predicative is following target word' => [
      'm' => 3,
      'r' => 3,
      'p' => 0,
    ],
    'Modificative is following target word' => [
      'm' => 0,
      'r' => 3,
      'p' => 3,
    ],
    'Referential is following target word' => [
      'm' => 3,
      'r' => 0,
      'p' => 3,
    ],
    'Prefix likely indicates predicative' => [
      'm' => 0,
      'r' => 0,
      'p' => 4,
    ],
    'Prefix suggests predicative' => [
      'm' => 0,
      'r' => 0,
      'p' => 1,
    ],
    'Prefix suggests modificative' => [
      'm' => 1,
      'r' => 0,
      'p' => 0,
    ],
    'Suffix suggests predicative' => [
      'm' => 0,
      'r' => 0,
      'p' => 1,
    ],
    'Suffix suggests modificative, less likely referential' => [
      'm' => 2,
      'r' => 1,
      'p' => 0,
    ],
    'Suffix suggests referential' => [
      'm' => 0,
      'r' => 1,
      'p' => 0,
    ],
    'Following word likely indicates target is predicative' => [
      'm' => 0,
      'r' => 0,
      'p' => 3,
    ],
    'Following word suggests target is predicative' => [
      'm' => 0,
      'r' => 0,
      'p' => 1,
    ],
    'Word is followed by a pronoun' => [
      'm' => 0,
      'r' => 0,
      'p' => 1,
    ],
    'Word is followed by "nga"' => [
      'm' => 5,
      'r' => 1,
      'p' => 0,
    ],
    'Preceding word likely indicates target is referential' => [
      'm' => 0,
      'r' => 3,
      'p' => 0,
    ],
    'Preceding word suggests target is referential' => [
      'm' => 0,
      'r' => 1,
      'p' => 0,
    ],
    'Pronoun precedes word' => [
      'm' => 0,
      'r' => 2,
      'p' => 0,
    ],
    'Word is preceded by "nga"' => [
      'm' => 0,
      'r' => 1,
      'p' => 0,
    ],
    'Preceding word likely indicates target is modificative' => [
      'm' => 3,
      'r' => 0,
      'p' => 0,
    ],
    'Word begins clause' => [
      'm' => 0,
      'r' => 0,
      'p' => 3,
    ],
    'Word ends clause' => [
      'm' => 0,
      'r' => 3,
      'p' => 0,
    ],
  ];

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

}
