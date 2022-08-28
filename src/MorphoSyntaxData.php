<?php

namespace markfullmer\waraydictionary;

class MorphoSyntaxData {

  public static $rules = [
    'Target word is preceded by a pronoun' => [
      'score' => [
        'm' => 0,
        'r' => 2,
        'p' => 0,
      ],
      'example' => ['...harayo kaupay han <strong>akon <u>gindakoan</u></strong>'],
    ],
    'Target word is followed by a pronoun' => [
      'score' => [
        'm' => 0,
        'r' => 0,
        'p' => 1,
      ],
      'example' => ['<strong><u>Dinako</u> ako</strong> nga waray kag-anak'],
    ],
    'Target word is preceded by "nga"' => [
      'score' => [
        'm' => 0,
        'r' => 1,
        'p' => 0,
      ],
      'example' => ['...makit-an it\' usa <strong>nga <u>burod</u></strong>'],
    ],
    'Target word is followed by "nga"' => [
      'score' => [
        'm' => 5,
        'r' => 1,
        'p' => 0,
      ],
      'example' => ['Nakabati ka na han <strong><u>burod</u> nga</strong> lalaki nga baboy?'],
    ],
    'Predicative precedes target word' => [
      'score' => [
        'm' => 3,
        'r' => 3,
        'p' => 0,
      ],
      'example' => ['Harayo na gud ngay-an an ak\' <strong>ginkaturungan <u>kagab-i</u></strong> nga salida'],
    ],
    'Modificative precedes target word' => [
      'score' => [
        'm' => 0,
        'r' => 3,
        'p' => 3,
      ],
      'example' => ['<strong>Waray <u>palad</u></strong> nga maraut, waray palad nga maupay.'],
    ],
    'Referential precedes target word' => [
      'score' => [
        'm' => 3,
        'r' => 0,
        'p' => 3,
      ],
      'example' => ['Kay it\' <strong>babayi <u>nabuburod</u></strong> man;'],
    ],
    'Predicative follows target word' => [
      'score' => [
        'm' => 3,
        'r' => 3,
        'p' => 0,
      ],
      'example' => [''],
    ],
    'Modificative follows target word' => [
      'score' => [
        'm' => 0,
        'r' => 3,
        'p' => 3,
      ],
      'example' => [''],
    ],
    'Referential follows target word' => [
      'score' => [
        'm' => 3,
        'r' => 0,
        'p' => 3,
      ],
      'example' => [''],
    ],
    'Following word likely indicates target is predicative' => [
      'score' => [
        'm' => 0,
        'r' => 0,
        'p' => 3,
      ],
      'example' => ['Min, <strong><u>magpapatron</u> na</strong>'],
    ],
    'Following word suggests target is predicative' => [
      'score' => [
        'm' => 0,
        'r' => 0,
        'p' => 1,
      ],
      'example' => ['Kun ano <strong><u>kadakó</u> an</strong> butones sugad man an kadákó han ohales.'],
    ],
    'Preceding word likely indicates target is referential' => [
      'score' => [
        'm' => 0,
        'r' => 3,
        'p' => 0,
      ],
      'example' => [''],
    ],
    'Preceding word suggests target is referential' => [
      'score' => [
        'm' => 0,
        'r' => 1,
        'p' => 0,
      ],
      'example' => ['Waray hunong <strong>an <u>dalagan.</u></strong>'],
    ],
    'Preceding word likely indicates target is modificative' => [
      'score' => [
        'm' => 3,
        'r' => 0,
        'p' => 0,
      ],
      'example' => [''],
    ],
    'Target word begins clause' => [
      'score' => [
        'm' => 0,
        'r' => 0,
        'p' => 3,
      ],
      'example' => ['<strong><u>Nagtadong</u></strong> hiya ngan nag-asawa'],
    ],
    'Target word ends clause' => [
      'score' => [
        'm' => 0,
        'r' => 3,
        'p' => 0,
      ],
      'example' => ['Dii liwat pwede sumakob it\' <strong><u>táwo</u></strong>'],
    ],
    'Prefix likely indicates predicative' => [
      'score' => [
        'm' => 0,
        'r' => 0,
        'p' => 4,
      ],
      'example' => ['Nakit-an ko hi Papa Jesus nga <u><strong>gin-</strong>aanak</u> pa la hiya'],
    ],
    'Prefix suggests predicative' => [
      'score' => [
        'm' => 0,
        'r' => 0,
        'p' => 1,
      ],
      'example' => ['Didto han tabo ha Palo an ak\' tawgi <u><strong>na</strong>palit</u> mo intawon', '<u><strong>Nag</strong>tadong</u> hiya ngan nag-asawa'],
    ],
    'Prefix suggests modificative' => [
      'score' => [
        'm' => 1,
        'r' => 0,
        'p' => 0,
      ],
      'example' => [''],
    ],
    'Suffix suggests predicative' => [
      'score' => [
        'm' => 0,
        'r' => 0,
        'p' => 1,
      ],
      'example' => [''],
    ],
    'Suffix suggests modificative, less likely referential' => [
      'score' => [
        'm' => 2,
        'r' => 1,
        'p' => 0,
      ],
      'example' => [''],
    ],
    'Suffix suggests referential' => [
      'score' => [
        'm' => 0,
        'r' => 1,
        'p' => 0,
      ],
      'example' => ['Waray hunong an <u>dalag<strong>an</strong></u>.'],
    ],
  ];

  public static $fillers = [
    'ano',
    'gad',
    'la',
    'kay',
    'kun',
    'ngay-an',
    'ngayan',
    'man',
    'pa',
    'ba',
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
