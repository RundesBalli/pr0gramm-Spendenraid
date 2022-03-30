<?php
/**
 * functions.php
 * 
 * Datei mit Funktionen für den Betrieb.
 */

/**
 * Entschärffunktion
 * 
 * @param  string $defuse_string String der "entschärft" werden soll, um ihn in einen DB-Query zu übergeben.
 * @param  bool   $trim          Gibt an ob Leerzeichen/-zeilen am Anfang und Ende entfernt werden sollen.
 * 
 * @return string Der vorbereitete, "entschärfte" String.
 */
function defuse($defuse_string, $trim = TRUE) {
  if($trim === TRUE) {
    $defuse_string = trim($defuse_string);
  }
  global $dbl;
  return mysqli_real_escape_string($dbl, strip_tags($defuse_string));
}

/**
 * Ausgabefunktion
 * 
 * @param  string $string String, der ausgegeben werden soll.
 * 
 * @return string Der vorbereitete String.
 */
function output($string) {
  return htmlentities($string, ENT_QUOTES);
}

/**
 * clickableLinks
 * 
 * Funktion zum Anklickbar machen von Links in Fließtexten
 *
 * @param string $string String dessen Links umgewandelt werden sollen.
 * 
 * @return string Der String mit umgewandelten Links.
 */
function clickableLink(string $string) {
  return preg_replace('/https?:\/\/[^\s]+/im', '<a href=\'$0\' target=\'_blank\' rel=\'noopener\'>$0</a>', $string);
}
?>
