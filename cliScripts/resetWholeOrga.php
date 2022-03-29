<?php
/**
 * resetWholeOrga.php
 * 
 * Datei zum Anlegen eines Nutzeraccounts.
 * 
 * @param int $argv[1] Organisation die zurückgesetzt werden soll.
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
 * Auslesen und verarbeiten der Organisation.
 */
if(isset($argv[1]) AND preg_match('/^[\d]{1,2}$/', defuse($argv[1]), $match) === 1) {
  $orgaId = intval(defuse($match[0]));
  $result = mysqli_query($dbl, "SELECT `id`, `organame` FROM `orgas` WHERE `id`=".$orgaId) OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) != 1) {
    die("Die eingegebene Organisation ist ungültig.\n\n");
  }
  $row = mysqli_fetch_assoc($result);
}

/**
 * Abfrage ob man wirklich die Organisation zurücksetzen möchte.
 */
echo "Möchtest du die Organisation '".$row['organame']."' wirklich zurücksetzen?\n'sicher' eingeben zum fortsetzen.\n";
if(fread(fopen("php://stdin","r"), 6) != 'sicher'){
  die("Abbruch.\n\n");
}

/**
 * Bestätigung wurde erteilt.
 */
$result = mysqli_query($dbl, "SELECT `postId` FROM `items` WHERE `firstsightOrgaId`=".$orgaId) OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_assoc($result)) {
  mysqli_query($dbl, "UPDATE `items` SET `firstsightOrgaId`=NULL, `firstsightOrgaUserId`=NULL, `confirmedOrgaId`=NULL, `confirmedOrgaUserId`=NULL WHERE `postId`=".$row['postId']." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  mysqli_query($dbl, "INSERT INTO `log` (`logLevel`, `postId`, `text`) VALUE (5, ".$row['postId'].", '[CLI] Organisation zurückgesetzt')") OR DIE(MYSQLI_ERROR($dbl));
}
echo "Erledigt.\n\n";
?>
