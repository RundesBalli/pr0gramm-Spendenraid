<?php
/**
 * config.php
 * 
 * Konfigurationsdatei
 */

/**
 * MySQL-Datenbank
 */
$dbl = mysqli_connect("localhost", "", "", "") OR DIE(MYSQLI_ERROR($dbl));
mysqli_set_charset($dbl, "utf8") OR DIE(MYSQLI_ERROR($dbl));

/**
 * Crawler Einstellungen
 * @var int $crawler['newer']   Post-ID ab der der Crawler anfängt zu suchen
 * @var string $crawler['tags'] Tagliste. Suchstring wie in der normalen pr0gramm Suche
 */
$crawler['newer'] = 0;
$crawler['tags'] = '';

/**
 * Speicherort des apiCalls.
 * Download: https://github.com/RundesBalli/pr0gramm-apiCall
 * Wird - sofern erforderlich - eingebunden.
 * 
 * Beispielwert: /home/user/apiCall/apiCall.php
 * 
 * @var string
 */
$apiCall = "";

/**
 * Geheimes Token zur Nutzerfreischaltung für das Perk / Setzen des Badges
 * 
 * @var string
 */
$perkSecret = "";

/**
 * Einstellungen für die KI API
 * 
 * @var string $kiApiToken          Token zur Übergabe der PostIds an die KI API
 * @var string $editPostToken       Token für die KI API zur Übermittlung der Postdaten an die editPosts API
 * @var int    $kiUserId            UserId der KI
 * @var string $kiCURL['bindTo']    Interface mit welchem cURL die Verbindung aufbauen soll, z.B. eth0 (sudo ifconfig)
 * @var string $kiCURL['userAgent'] Der UserAgent mit dem die Anfrage gesendet werden soll
 * @var string $kiCURL['url']       Die KI-URL, die aufgerufen werden soll.
 */
$kiApiToken = "";
$editPostToken = "";
$kiUserId = 1;
$kiCURL['bindTo'] = "eth0";
$kiCURL['userAgent'] = "";
$kiCURL['url'] = "";
?>
