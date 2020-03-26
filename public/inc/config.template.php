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
 * Nutzerfreischaltung für das Perk
 * 
 * @var string
 */
$perkSecret = "";
?>
