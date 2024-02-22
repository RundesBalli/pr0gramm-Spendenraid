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
      'invalidUsername' => 'Der Name ist ungültig. Er muss zwischen 2 und 32 Zeichen lang sein und darf keine Sonderzeichen enthalten (0-9a-zA-Z).'."\n".'Beispielaufruf:'."\n".'php '.$argv[0].' Hans'."\n".'Erstellt den Nutzer "Hans" mit einem zufälligen Passwort.'."\n\n",
      'success' => 'Account erfolgreich angelegt.'."\n\n".'User: %s'."\n".'Pass: %s'."\n\n",
      'duplicate' => 'Es existiert bereits ein Account mit diesem Namen.'."\n\n",
      'unknownError' => 'Unknown error: %s'."\n\n",
      'log' => '[CLI] User angelegt: %s',
    ],
    'delUser' => [
      'invalidUsername' => 'Der Name ist ungültig. Er muss zwischen 2 und 32 Zeichen lang sein und darf keine Sonderzeichen enthalten (0-9a-zA-Z).'."\n".'Beispielaufruf:'."\n".'php '.$argv[0].' Hans'."\n".'Löscht den Nutzer "Hans".'."\n\n",
      'success' => 'Account erfolgreich entfernt.'."\n\n",
      'notFound' => 'Es existiert kein Account mit diesem Namen.'."\n\n",
      'log' => '[CLI] User gelöscht: %s',
    ],
    'passwd' => [
      'invalidUsername' => 'Der Name ist ungültig. Er muss zwischen 2 und 32 Zeichen lang sein und darf keine Sonderzeichen enthalten (0-9a-zA-Z).'."\n".'Beispielaufruf:'."\n".'php '.$argv[0].' Hans'."\n".'Setzt ein neues, zufälliges Passwort für den Nutzer "Hans".'."\n\n",
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

  'logout' => [
    'title' => 'Logout',
    'form' => [
      'question' => 'Möchtest du dich ausloggen?',
      'submit' => 'logout',
    ],
    'logoutFailed' => [
      'invalidToken' => 'Ungültiges Token.',
      'back' => 'Zurück zur Übersicht',
    ]
  ],
];
?>
