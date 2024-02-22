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
];
?>
