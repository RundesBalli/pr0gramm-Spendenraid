<?php
/**
 * jsonOutput.php
 * 
 * Datei auf die das pr0gramm zugreifen kann um Informationen abzurufen
 */

/**
 * Einbinden der Konfigurationsdatei
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."config.php");

/**
 * Das JSON Output Array initialisieren
 */
$output = array();

/**
 * Summen zusammenrechnen
 */
$result = mysqli_query($dbl, "SELECT (SELECT IFNULL(sum(`firstsightValue`), 0) FROM `items` WHERE `firstsightValue` IS NOT NULL) AS `unconfirmed`, (SELECT IFNULL(sum(`confirmedValue`), 0) FROM `items` WHERE `confirmedValue` IS NOT NULL) AS `confirmed`") OR DIE(MYSQLI_ERROR($dbl));
$bla = mysqli_fetch_array($result);
$output['sums']['total']['unconfirmed'] = (double)$bla['unconfirmed'];
$output['sums']['total']['confirmed'] = (double)$bla['confirmed'];

/**
 * Items zählen
 */
$result = mysqli_query($dbl, "SELECT (SELECT count(`id`) FROM `items`) AS `total`, (SELECT count(`id`) FROM `items` WHERE `isDonation`='1') AS `isDonation`, (SELECT count(`id`) FROM `items` WHERE `isDonation`='0') AS `isNotDonation`") OR DIE(MYSQLI_ERROR($dbl));
$bla = mysqli_fetch_array($result);
$output['items']['total'] = (int)$bla['total'];
$output['items']['isDonation'] = (int)$bla['isDonation'];
$output['items']['isNotDonation'] = (int)$bla['isNotDonation'];

/**
 * Tags
 */
$output['tags'] = $crawler['tags'];

/**
 * Zu den oben gezählten Summen noch die Summen pro Organisation hinzufügen
 */
$result = mysqli_query($dbl, "SELECT * FROM `orgas` WHERE `exportCountOnly`='0' ORDER BY `sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
$orgaslfd = 0;
$output['sums']['orgas'] = array();
while($row = mysqli_fetch_array($result)) {
  $orgaslfd++;
  $output['sums']['orgas'][$orgaslfd] = array();
  $output['sums']['orgas'][$orgaslfd]['name'] = $row['organame'];
  $innerresult = mysqli_query($dbl, "SELECT IFNULL(sum(`confirmedValue`), 0) as `k`, count(`id`) as `j` FROM `items` WHERE `isDonation`='1' AND `confirmedOrgaId`='".$row['id']."'") OR DIE(MYSQLI_ERROR($dbl));
  $bla = mysqli_fetch_array($innerresult);
  $output['sums']['orgas'][$orgaslfd]['confirmed'] = (double)$bla['k'];
  $output['sums']['orgas'][$orgaslfd]['postCount'] = (int)$bla['j'];
}

/**
 * Organisationen, die das exportCountOnly Flag haben, werden gesondert angezeigt und aus der Gesamtsumme herausgerechnet.
 */
$result = mysqli_query($dbl, "SELECT * FROM `orgas` WHERE `exportCountOnly`='1' ORDER BY `sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
$orgaslfd = 0;
$output['sums']['orgasCountOnly'] = array();
while($row = mysqli_fetch_array($result)) {
  $orgaslfd++;
  $output['sums']['orgasCountOnly'][$orgaslfd] = array();
  $output['sums']['orgasCountOnly'][$orgaslfd]['name'] = $row['organame'];
  $innerresult = mysqli_query($dbl, "SELECT IFNULL(sum(`confirmedValue`), 0) as `k`, count(`id`) as `j` FROM `items` WHERE `isDonation`='1' AND `confirmedOrgaId`='".$row['id']."'") OR DIE(MYSQLI_ERROR($dbl));
  $bla = mysqli_fetch_array($innerresult);
  $output['sums']['total']['unconfirmed'] -= (float)$bla['k'];
  $output['sums']['total']['confirmed'] -= (float)$bla['k'];
  $output['sums']['orgasCountOnly'][$orgaslfd]['postCount'] = (int)$bla['j'];
}

header("Content-type: application/json; charset=utf-8");
die(json_encode($output));
?>
