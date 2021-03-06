<?php
/**
 * index.php
 * 
 * pr0gramm-Spendenraid
 * 
 * Ein Tool zum Auswerten des Spendenraids auf pr0gramm
 * 
 * @author    RundesBalli <webspam@rundesballi.com>
 * @copyright 2020 RundesBalli
 * @version   1.0
 * @license   MIT-License
 * @see       https://github.com/RundesBalli/pr0gramm-Spendenraid
 */

/**
 * Einbinden der Konfigurationsdatei sowie der Funktionsdatei
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."config.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."functions.php");

/**
 * Initialisieren des Outputs, Standardtitels und des Loginzustandes für die Navigation
 */
$content = "";
$title = "";
$loginNav = 0;

/**
 * Herausfinden welche Seite angefordert wurde
 */
if(!isset($_GET['p']) OR empty($_GET['p'])) {
  $getp = "login";
} else {
  preg_match("/([\d\w-]+)/i", $_GET['p'], $match);
  $getp = $match[1];
}

/**
 * Das Seitenarray für die Seitenzuordnung
 */
$pageArray = array(
  /* Standardseiten */
  'login'           => 'login.php',
  'logout'          => 'logout.php',
  'log'             => 'log.php',
  'resetpost'       => 'resetpost.php',
  'orgareset'       => 'orgareset.php',
  'valuation'       => 'valuation.php',
  'orga'            => 'orga.php',
  'overview'        => 'overview.php',
  'stats'           => 'stats.php',
  'dellist'         => 'dellist.php',
  'delpost'         => 'delpost.php',
  'fakes'           => 'fakes.php',
  'delfake'         => 'delfake.php',
  'fakeposts'       => 'fakeposts.php',
  'fakepostsdkms'   => 'fakepostsdkms.php',
  'postinfo'        => 'postinfo.php',
  'unlockuser'      => 'unlockuser.php',

  /* Schnellbewertung */
  'dkmsfast'        => 'dkmsfast.php',
  'krebshilfefast'  => 'krebshilfefast.php',
  'gtfast'          => 'gtfast.php',
  'orgafast'        => 'orgafast.php',

  /* Fehlerseiten */
  '404'             => '404.php',
  '403'             => '403.php'
);

/**
 * Prüfung ob die Unterseite im Array existiert, falls nicht 404
 */
if(isset($pageArray[$getp])) {
  require_once(__DIR__.DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR.$pageArray[$getp]);
} else {
  require_once(__DIR__.DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."404.php");
}

/**
 * Navigation
 * Hinweis: das Toggle-Element ist im Template enthalten.
 */
$nav = "";
if($loginNav == 1) {
  $nav.= "<a href='/overview'>Übersicht</a>";
  $nav.= "<a href='/valuation'>Bewertung</a>";
  $nav.= "<a href='/orga'>Organisationen</a>";
  $nav.= "<a href='/postinfo'>PostInfo</a>";
  $nav.= "<a href='/log'>Log</a>";
  $nav.= "<a href='/stats'>Statistiken</a>";
  $nav.= "<a href='/logout'>Logout</a>";
  $nav.= "<br>";
  $nav.= "<a href='/dellist'>Löschliste</a>";
  $nav.= "<a href='/fakes'>Fälschungen</a>";
  $nav.= "<a href='/dkmsfast'>DKMS SB</a>";
  $nav.= "<a href='/krebshilfefast'>DtKrebshilfe SB</a>";
  $nav.= "<a href='/gtfast'>GT SB</a>";
} else {
  $nav.= "<a href='/login'>Login</a>";
  $nav.= "<a href='https://RundesBalli.com' target='_blank' rel='noopener'>RundesBalli</a>";
  $nav.= "<a href='https://pr0gramm.com/inbox/messages/RundesBalli' target='_blank' rel='noopener'>Kontakt per PN</a>";
}

/**
 * Templateeinbindung und Einsetzen der Variablen
 */
$templatefile = __DIR__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."template.tpl";
$fp = fopen($templatefile, "r");
echo preg_replace(array("/{TITLE}/im", "/{NAV}/im", "/{CONTENT}/im"), array(($title == "" ? "" : " - ".$title), $nav, $content), fread($fp, filesize($templatefile)));
fclose($fp);
?>
