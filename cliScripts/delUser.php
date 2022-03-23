<?php
/**
 * delUser.php
 * 
 * Datei zum Entfernen eines Nutzeraccounts.
 * 
 * @param string $argv[1] Benutzername
 */

/**
 * Einbinden der Konfigurationsdatei sowie der Funktionsdatei
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."config.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."functions.php");

/**
 * Prüfen ob das Script in der Konsole läuft.
 */
if(php_sapi_name() != 'cli') {
  die("Das Script kann nur per Konsole ausgeführt werden.\n\n");
}

/**
 * Auslesen und verarbeiten des Nutzernamens.
 */
if(isset($argv[1]) AND preg_match('/^[0-9a-zA-Z]{3,32}$/', defuse($argv[1]), $match) === 1) {
  $username = $match[0];
} else {
  die("Der Name ist ungültig. Er muss zwischen 3 und 32 Zeichen lang sein und darf keine Sonderzeichen enthalten (0-9a-zA-Z).\nBeispielaufruf:\nphp ".$argv[0]." Hans\nDer Nutzer \"Hans\" wird gelöscht.\n\n");
}

/**
 * Entfernen des Accounts.
 */
mysqli_query($dbl, "DELETE FROM `users` WHERE `username`='".$username."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_affected_rows($dbl) == 1) {
  mysqli_query($dbl, "INSERT INTO `log` (`logLevel`, `text`) VALUES (1, '[CLI] User gelöscht: ".$username."')") OR DIE(MYSQLI_ERROR($dbl));
  die("Account erfolgreich entfernt.\n\n");
} else {
  die("Dieser Account existiert nicht.\n\n");
}
?>
