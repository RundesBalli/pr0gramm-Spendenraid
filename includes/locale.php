<?php
/**
 * includes/locale.php
 * 
 * German locale, because all of the users will be German speaking.
 */
$lang = [
  /**
   * Locale
   */
  'locale' => 'de',

  /**
   * Error pages and messages
   */
  'error' => [
    403 => 'Du hast keine Berechtigung auf die von dir angeforderte Ressource <code>%s</code> zuzugreifen.',
    404 => 'Die von dir angeforderte Ressource <code>%s</code> existiert nicht.',
    500 => [
      'includeFileNotFound' => 'Die zu inkludierende Datei existiert nicht auf dem Server.',
      'minConfigVersion' => 'Die vorhandene Konfigurationsdatei ist nicht ausreichend. Bitte lege eine neue Konfigurationsdatei mithilfe der im Repository vorhandenen <code>config.template.php</code> Datei an.',
      'unknownError' => 'Unbekannter Fehler.',
    ],
    'templateFileNotFound' => 'Templatedatei nicht gefunden.',
    'noCli' => 'Das Script kann nur im Terminal ausgeführt werden.'."\n\n",
  ],

  /**
   * CLI scripts
   */
  'cli' => [
    'addUser' => [
      'invalidUsername' => 'Der Name ist ungültig. Er muss zwischen 2 und 32 Zeichen lang sein und darf keine Sonderzeichen enthalten (0-9a-zA-Z).'."\n".'Beispielaufruf:'."\n".'php %s Hans'."\n".'Erstellt den Nutzer "Hans" mit einem zufälligen Passwort.'."\n\n",
      'success' => 'Account erfolgreich angelegt.'."\n\n".'User: %s'."\n".'Pass: %s'."\n\n",
      'duplicate' => 'Es existiert bereits ein Account mit diesem Namen.'."\n\n",
      'unknownError' => 'Unknown error: %s'."\n\n",
      'log' => '[CLI] User angelegt: %s',
    ],
    'delUser' => [
      'invalidUsername' => 'Der Name ist ungültig. Er muss zwischen 2 und 32 Zeichen lang sein und darf keine Sonderzeichen enthalten (0-9a-zA-Z).'."\n".'Beispielaufruf:'."\n".'php %s Hans'."\n".'Löscht den Nutzer "Hans".'."\n\n",
      'success' => 'Account erfolgreich entfernt.'."\n\n",
      'notFound' => 'Es existiert kein Account mit diesem Namen.'."\n\n",
      'log' => '[CLI] User gelöscht: %s',
    ],
    'passwd' => [
      'invalidUsername' => 'Der Name ist ungültig. Er muss zwischen 2 und 32 Zeichen lang sein und darf keine Sonderzeichen enthalten (0-9a-zA-Z).'."\n".'Beispielaufruf:'."\n".'php %s Hans'."\n".'Setzt ein neues, zufälliges Passwort für den Nutzer "Hans".'."\n\n",
      'success' => 'Passwort erfolgreich geändert.'."\n\n".'User: %s'."\n".'Pass: %s'."\n\n",
      'notFound' => 'Es existiert kein Account mit diesem Namen.'."\n\n",
      'log' => '[CLI] User-Passwort geändert: %s',
    ],
    'resetWholeOrga' => [
      'invalidId' => 'Die Organisations-ID ist ungültig.'."\n\n",
      'question' => 'Möchtest du wirklich die ganze Organisation "%s" zurücksetzen? STRG+C zum Abbrechen oder "ok" eingeben und Enter drücken zum Fortfahren.'."\n\n",
      'aborting' => 'Abbruch.'."\n\n",
      'log' => '[CLI] Organisation zurückgesetzt.',
      'done' => 'Erledigt.'."\n\n",
    ],
    'crawler' => [
      'startFull' => '[CRON, Crawl] Crawlvorgang gestartet (groß); Beginnend bei itemId %s.',
      'startSmall' => '[CRON, Crawl] Crawlvorgang gestartet (klein); Beginnend bei itemId %s.',
      'finished' => '[CRON, Crawl] Crawlvorgang beendet (total: %d, new: %d, updated: %d.',
      'transmitToAi' => '[CRON, KI] Übergabe von %d Post%s an die KI.',
      'transmitToAiSuccessful' => '[CRON, KI] Übergabe an die KI erfolgreich.',
      'transmitToAiFailed' => '[CRON, KI] Übergabe an die KI NICHT erfolgreich. Response: %s',
    ],
  ],

  /**
   * API
   */
  'api' => [
    'getJPG' => [
      'log' => '[KI, JPG] KI Anfrage JPG',
    ],
  ],

  /**
   * metaLogLevel
   */
  'logLevel' => [
    'system' => 'User-/Systemaktion',
    'firstsight' => 'Erstsichtung',
    'confirmingReset' => 'Zweitsichtung - zurückgesetzt',
    'confirmingOk' => 'Zweitsichtung - ok',
    'reset' => 'Post zurückgesetzt',
    'perk' => 'Belohnung und Badge',
    'fake' => 'Fakes',
    'note' => 'Notiz',
  ],

  /**
   * Navigation elements
   */
  'nav' => [
    'login' => 'Login',
    'contact' => 'Kontakt per PN',
    'github' => 'GitHub',
    'overview' => 'Übersicht',
    'evaluation' => 'Bewertung',
    'orga' => 'Organisationen',
    'postInfo' => 'PostInfo',
    'log' => 'Log',
    'stats' => 'Statistiken',
    'logout' => 'Logout',
    'delList' => 'Löschliste',
    'fakes' => 'Fälschungen',
    'fastOrga' => 'fastOrga',
  ],

  /**
   * 
   * Pages
   * 
   */
  /**
   * Login
   */
  'login' => [
    'title' => 'Login',
    'cookieNote' => 'Ab diesem Punkt werden Cookies verwendet! Mit dem Fortfahren stimmst du dem zu!',
    'form' => [
      'name' => 'Name',
      'password' => 'Passwort',
      'submit' => 'Login',
    ],
    'loginFailed' => [
      'title' => 'Login gescheitert',
      'warnBox' => 'Die Zugangsdaten sind falsch.',
      'tryAgain' => 'Erneut versuchen',
    ]
  ],

  /**
   * Logout
   */
  'logout' => [
    'title' => 'Logout',
    'form' => [
      'question' => 'Möchtest du dich ausloggen?',
      'submit' => 'Logout',
    ],
    'logoutFailed' => [
      'invalidToken' => 'Ungültiges Token.',
      'back' => 'Zurück zur Übersicht',
    ]
  ],

  /**
   * Overview
   */
  'overview' => [
    'title' => 'Übersicht',
    'general' => 'Eingeloggt als: <span class="warn bold">%s</span> - (<a href="/logout">Ausloggen</a>)',
    'dkmsInfo' => [
      'Siehe <a href="https://pr0gramm.com/top/dkms%20sms/2465205" target="_blank" rel="noopener">hier</a>:',
      'Alle <span class="highlight">DKMS5, DKMS10, DKMSxx</span> SMS sind <span class="highlight">FÜNF</span> Euro wert.',
      'Alle <span class="highlight">LEBEN</span> SMS sind <span class="highlight">EINEN</span> Euro wert.',
      '<span class="warn bold">WICHTIG! Wenn keine Antwort von der DKMS kommt, dann zählt die Spende nicht (Drittanbietersperre)!</span>',
    ],
    'search' => [
      'title' => 'Suchparameter',
      'newer' => 'Crawlen neuer als Post-ID',
      'tags'=> 'Suchquery',
    ],
    'total' => [
      'title' => 'Posts / Bewertungen',
      'items' => [
        'total' => [
          'title' => 'Gesamt',
          'description' => 'Anzahl der Posts, die in das o.g. Suchmuster fallen',
        ],
        'isDonation' => [
          'title' => 'Spendenposts',
          'description' => 'Anzahl der bestätigten Spendenposts',
        ],
        'isGoodAct' => [
          'title' => 'Gute Taten',
          'description' => 'Anzahl der bestätigten guten Taten',
        ],
        'isNoDonation' => [
          'title' => 'Nicht-Spendenposts',
          'description' => 'Anzahl der bestätigten Nicht-Spendenposts',
        ],
        'pendingFirst' => [
          'title' => 'ausstehende Erstsichtung',
          'description' => 'Anzahl der Posts, bei denen noch keine Erstsichtung stattgefunden hat.',
        ],
        'pendingSecond' => [
          'title' => 'ausstehende Zweitsichtung',
          'description' => 'Anzahl der Posts, bei denen noch keine Zweitsichtung stattgefunden hat.',
        ],
        'pendingOrgaFirst' => [
          'title' => 'ausstehende Orga-Erstsichtung',
          'description' => 'Anzahl der Spendenposts, bei denen noch keine Orga-Erstsichtung stattgefunden hat.',
        ],
        'pendingOrgaSecond' => [
          'title' => 'ausstehende Orga-Zweitsichtung',
          'description' => 'Anzahl der Spendenposts, bei denen noch keine Orga-Zweitsichtung stattgefunden hat.',
        ],
      ],
    ],
    'sums' => [
      'title' => 'Summen',
      'items' => [
        'unconfirmedTotalsum' => [
          'title' => 'Gesamtsumme nach Erstsichtung',
          'description' => 'Gesamtsumme der Erstsichtungen',
        ],
        'confirmedTotalsum' => [
          'title' => 'bestätigte Gesamtsumme nach Zweitsichtung',
          'description' => 'Gesamtsumme der Zweitsichtungen (Bei der Zweitsichtung wurde der Wert aus der Erstsichtung bestätigt und ist damit gültig)',
        ],
      ],
    ],
    'organizations' => [
      'title' => 'Organisationen',
      'name' => 'Name der Organisation',
      'confirmedValue' => 'bestätigte Spendensumme',
      'confirmedCount' => 'bestätigte Spendenposts',
      'average' => 'Ø pro Spende',
    ]
  ],

  /**
   * Evaluation
   */
  'evaluation' => [
    'title' => 'Bewertung',
    'invalidToken' => 'Ungültiges Token',
    'itemNotFound' => 'Der Post existiert nicht (mehr).',
    'log' => [
      'confirmingReset' => 'Erstsichtung',
      'goodAct' => 'Orga: 9',
      'noDonation' => 'Kein Spendenpost',
    ],
    'success' => 'Spendenwert eingetragen.',
    'resetItem' => 'Post zurücksetzen',
    'aiPrefix' => 'KI-',
    'firstsight' => 'Erstsichtung',
    'firstsightGoodAct' => 'Erstsichtung: Gute Tat',
    'clickImage' => 'Zur Post-Ansicht einfach auf das Bild klicken',
    'video' => 'Video auf pr0gramm ansehen',
    'value' => 'Geldbetrag',
    'seeInfo' => 'Siehe Info unten',
    'fastEvaluation' => 'Schnellbewertung',
    'submit' => 'Eintragen',
    'links' => 'Links',
    'itemInfo' => 'Post-Info',
    'info' => 'Informationen zur Auswertung',
    'infoText' => 'Spendenpost: Geldwert eintragen (Komma oder Punkt als Dezimaltrennung ist egal),<br>kein Spendenpost: die Zahl 0 eintragen,<br>unsicher: leer lassen und Formular absenden, dann kommt ein neues Bild.<br>Wenn der Post eine Spende ist, man aber den Wert nicht erkennt 0,01 eintragen!<br>CHF und USD einfach 1:1 eintragen.<br>DKMS siehe Info <a href="/overview">hier</a>!<br><span class="warn">NEU IN 2024:</span> Gute Tat = g, G oder + eintragen!',
    'allDone' => 'Alles erledigt. Nächster Crawl alle 5 Minuten. (in %d Sekunden)',
    'evaluateOrganizations' => 'Organisationen bewerten'
  ],
];
?>
