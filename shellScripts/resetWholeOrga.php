<?php
/**
 * addUser.php
 * 
 * Shell script to reset a whole organization.
 * 
 * @param int $argv[1] Organization ID
 */

/**
 * Including the configuration and function loader.
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'loader.php');

/**
 * Check if the script runs in the shell.
 */
if(php_sapi_name() != 'cli') {
  die($lang['error']['noCli']);
}

/**
 * Read and process the given organization id.
 */
if(isset($argv[1]) AND preg_match('/^[\d]{1,2}$/', defuse($argv[1]), $match) === 1) {
  $orgaId = intval(defuse($match[0]));
  $result = mysqli_query($dbl, "SELECT `id`, `name` FROM `metaOrganizations` WHERE `id`=".$orgaId) OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) != 1) {
    die($lang['cli']['resetWholeOrga']['invalidId']);
  }
  $row = mysqli_fetch_assoc($result);
}

/**
 * Abfrage ob man wirklich die Organisation zurücksetzen möchte.
 */
echo sprintf($lang['cli']['resetWholeOrga']['question'], $row['name']);
if(fread(fopen("php://stdin","r"), 2) != 'ok'){
  die($lang['cli']['resetWholeOrga']['aborting']);
}

/**
 * Bestätigung wurde erteilt.
 */
$result = mysqli_query($dbl, "SELECT `itemId` FROM `items` WHERE `firstsightOrgaId`=".$orgaId) OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_assoc($result)) {
  mysqli_query($dbl, "UPDATE `items` SET `firstsightOrgaId`=NULL, `firstsightOrgaUserId`=NULL, `confirmedOrgaId`=NULL, `confirmedOrgaUserId`=NULL WHERE `itemId`=".$row['itemId']." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  mysqli_query($dbl, "INSERT INTO `log` (`logLevel`, `itemId`, `text`) VALUE (5, ".$row['itemId'].", '".$lang['cli']['resetWholeOrga']['log']."')") OR DIE(MYSQLI_ERROR($dbl));
}
die($lang['cli']['resetWholeOrga']['done']);
?>
