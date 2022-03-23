<?php
/**
 * passwd.php
 * 
 * Datei zum Ändern eines Nutzerpassworts.
 * 
 * @param string $argv[1] Benutzername
 * @param string $argv[2] Passwort
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
  die("Der Name ist ungültig. Er muss zwischen 3 und 32 Zeichen lang sein und darf keine Sonderzeichen enthalten (0-9a-zA-Z).\nBeispielaufruf:\nphp ".$argv[0]." Hans LOL1asdf123xyz456lol\nBearbeitet den Nutzer \"Hans\" und setzt das Passwort \"LOL1asdf123xyz456lol\".\n\n");
}

/**
 * Auslesen und verarbeiten des Passworts.
 */
if(isset($argv[2]) AND preg_match('/^.{20,}$/', $argv[2], $match) === 1) {
  $salt = hash('sha256', random_bytes(4096));
  $password = password_hash($match[0].$salt, PASSWORD_DEFAULT);
} else {
  die("Das Passwort ist zu kurz. Es muss mindestens 20 Zeichen enthalten.\nBeispielaufruf:\nphp ".$argv[0]." Hans LOL1asdf123xyz456lol\nBearbeitet den Nutzer \"Hans\" und setzt das Passwort \"LOL1asdf123xyz456lol\".\n\n");
}

/**
 * Updaten des bestehenden Nutzers.
 */
 mysqli_query($dbl, "UPDATE `users` SET `password`='".$password."', `salt`='".$salt."' WHERE `username`='".$username."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_affected_rows($dbl) == 1) {
  mysqli_query($dbl, "INSERT INTO `log` (`logLevel`, `text`) VALUES (1, '[CLI] User-Passwort geändert: ".$username."')") OR DIE(MYSQLI_ERROR($dbl));
  die("Passwort erfolgreich geändert.\n\n");
} else {
  die("Dieser Account existiert nicht.\n\n");
}
?>
