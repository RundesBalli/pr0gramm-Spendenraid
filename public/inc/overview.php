<?php
/**
 * overview.php
 * 
 * Übersichtsseite
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');


/**
 * Titel und Überschrift
 */
$title = "Übersicht";
$content.= "<h1>Übersicht</h1>".PHP_EOL;

/**
 * Allgemeine Infos
 */
$content.= "<div class='row'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Eingeloggt als: <span class='warn bold'>".$username."</span> - (<a href='/logout'>Ausloggen</a>)</div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * DKMS SMS Info
 */
$content.= "<h1 class='warn'>Info zu DKMS-SMS-Spenden</h1>".PHP_EOL;
$content.= "<div class='row'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Siehe <a href='https://pr0gramm.com/top/dkms%20sms/2465205' target='_blank' rel='noopener'>hier</a>:</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Alle <span class='highlight'>DKMS5, DKMS10, DKMSxx</span> SMS sind <span class='highlight'>FÜNF</span> Euro wert.</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Alle <span class='highlight'>LEBEN</span> SMS sind <span class='highlight'>EINEN</span> Euro wert.</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 warn'>WICHTIG! Wenn keine Antwort von der DKMS kommt, dann zählt die Spende nicht (Drittanbietersperre)!</div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Suchinfo
 */
$content.= "<h1>Suchparameter</h1>".PHP_EOL;
$content.= "<div class='row hover'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>Crawlen ab Post-ID</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-8 col-l-9 col-xl-9'>".$crawler['newer']."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>Suchquery</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-8 col-l-9 col-xl-9 wb'>".$crawler['tags']."</div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Posts / Sichtungen
 */
$content.= "<h1>Posts / Sichtungen</h1>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT (SELECT count(`id`) FROM `items`) AS `postcountTotal`, (SELECT count(`id`) FROM `items` WHERE `isDonation`='1') AS `postcountIsDonation`, (SELECT count(`id`) FROM `items` WHERE `isDonation`='0') AS `postcountIsNoDonation`, (SELECT count(`id`) FROM `items` WHERE `firstsightValue` IS NULL) AS `pendingFirst`, (SELECT count(`id`) FROM `items` WHERE `firstsightValue` IS NOT NULL AND `confirmedValue` IS NULL) AS `pendingSecond`, (SELECT count(`id`) FROM `items` WHERE `isDonation`='1' AND `firstsightOrgaId` IS NULL) AS `pendingOrgaFirst`, (SELECT count(`id`) FROM `items` WHERE `isDonation`='1' AND `confirmedOrgaId` IS NULL) AS `pendingOrgaSecond`") OR DIE(MYSQLI_ERROR($dbl));
$row = mysqli_fetch_array($result);
$content.= "<div class='row hover'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>Gesamt</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>".number_format($row['postcountTotal'], 0, ",", ".")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-6 col-xl-6'>Anzahl der Posts, die in das o.g. Suchmuster fallen</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>Spendenposts</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>".number_format($row['postcountIsDonation'], 0, ",", ".")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-6 col-xl-6'>Anzahl der bestätigten Spendenposts</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>Nicht-Spendenposts</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>".number_format($row['postcountIsNoDonation'], 0, ",", ".")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-6 col-xl-6'>Anzahl der bestätigten Nicht-Spendenposts</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>ausstehende Erstsichtung</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>".number_format($row['pendingFirst'], 0, ",", ".")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-6 col-xl-6'>Anzahl der Posts, bei denen noch keine Erstsichtung stattgefunden hat.</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>ausstehende Zweitsichtung</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>".number_format($row['pendingSecond'], 0, ",", ".")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-6 col-xl-6'>Anzahl der Posts, bei denen noch keine Zweitsichtung stattgefunden hat.</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>ausstehende Orga-Erstsichtung</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>".number_format($row['pendingOrgaFirst'], 0, ",", ".")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-6 col-xl-6'>Anzahl der Spendenposts, bei denen noch keine Orga-Erstsichtung stattgefunden hat.</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>ausstehende Orga-Zweitsichtung</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>".number_format($row['pendingOrgaSecond'], 0, ",", ".")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-6 col-xl-6'>Anzahl der Spendenposts, bei denen noch keine Orga-Zweitsichtung stattgefunden hat.</div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Summen
 */
$content.= "<h1>Summen</h1>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT (SELECT IFNULL(sum(`firstsightValue`), 0) FROM `items` WHERE `firstsightValue` IS NOT NULL) AS `unconfirmedTotalsum`, (SELECT IFNULL(sum(`confirmedValue`), 0) FROM `items` WHERE `confirmedValue` IS NOT NULL) AS `confirmedTotalsum`") OR DIE(MYSQLI_ERROR($dbl));
$row = mysqli_fetch_array($result);
$content.= "<div class='row hover'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>Gesamtsumme nach Erstsichtung</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>".number_format($row['unconfirmedTotalsum'], 2, ",", ".")." €</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-6 col-xl-6'>Gesamtsumme der Erstsichtungen</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>bestätigte Gesamtsumme nach Zweitsichtung</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>".number_format($row['confirmedTotalsum'], 2, ",", ".")." €</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-6 col-xl-6'>Gesamtsumme der Zweitsichtungen (Bei der Zweitsichtung wurde der Wert aus der Erstsichtung bestätigt und ist damit gültig)</div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Organisationen
 */
$content.= "<h1>Organisationen</h1>".PHP_EOL;
$content.= "<div class='row highlight bold'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Name der Organisation</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>bestätigte Spendensumme</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>bestätigte Spendenposts</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Ø pro Spende</div>".PHP_EOL.
"</div>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT * FROM `orgas` ORDER BY `sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $innerresult = mysqli_query($dbl, "SELECT IFNULL(sum(`confirmedValue`), 0) AS `sum`, COUNT(`id`) AS `count` FROM `items` WHERE `isDonation`='1' AND `confirmedOrgaId`='".$row['id']."'") OR DIE(MYSQLI_ERROR($dbl));
  $innerrow = mysqli_fetch_array($innerresult);
  $content.= "<div class='row hover'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>".$row['organame']."</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>".number_format($innerrow['sum'], 2, ",", ".")." €</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>".number_format($innerrow['count'], 0, ",", ".")."</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>".number_format(($innerrow['count'] == 0 ? 0 : ($innerrow['sum']/$innerrow['count'])), 2, ",", ".")." €</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
  "</div>".PHP_EOL;
}
$content.= "<div class='spacer-m'></div>".PHP_EOL;
?>
