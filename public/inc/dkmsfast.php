<?php
/**
 * dkmsfast.php
 * 
 * Schnellbewertungsseite zum Setzen der Spendenorganisation "DKMS"
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "DKMS Schnellbewertung";
$content.= "<h1>DKMS Schnellbewertung</h1>".PHP_EOL;
$content.= "<h3 class='warn'>GENAU LESEN:</h3>".PHP_EOL;
$content.= "<div class='row'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 warn bold'>Beim Klick auf einen Thumbnail wird er DIREKT der DKMS zugeordnet (Erst- und Zweitsichtung wie über die normale Bewertung finden trotzdem statt!).</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Mittelklick (Mausrad) öffnet neuen Tab.</div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Selektieren von Posts, der noch nicht bewertet oder noch nicht durch einen selbst bewertet wurde.
 */
$result = mysqli_query($dbl, "SELECT * FROM `items` WHERE (`isDonation`='1' AND (`firstsightOrgaId` IS NULL OR (`confirmedOrgaId` IS NULL AND `firstsightOrgaUserId`!='".$userId."'))) AND `extension`!='mp4' ORDER BY RAND()") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) != 0) {
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>".PHP_EOL;
  while($row = mysqli_fetch_array($result)) {
    $content.= "<a href='/orgafast?postId=".$row['postId']."&orgaId=1'><img src='https://thumb.pr0gramm.com/".$row['thumb']."' alt='Thumb'></a>".PHP_EOL;
  }
  $content.= "</div>".PHP_EOL;
} else {
  /**
   * Alles erledigt.
   */
  $content.= "<div class='infobox'>Alles erledigt.</div>".PHP_EOL;
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
}
?>
