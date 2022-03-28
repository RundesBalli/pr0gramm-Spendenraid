<?php
/**
 * gtfast.php
 * 
 * Schnellbewertungsseite zum Setzen der Spendenorganisation "Gute Tat"
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "Gute Tat Schnellbewertung";
$content.= "<h1>Gute Tat Schnellbewertung</h1>";
$content.= "<h3 class='warn'>GENAU LESEN:</h3>";
$content.= "<div class='row'>".
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 warn bold'>Beim Klick auf einen Thumbnail wird er DIREKT der <span class='italic'>Gute Tat</span>-Orga zugeordnet (Erst- und Zweitsichtung wie über die normale Bewertung finden trotzdem statt!).</div>".
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Mittelklick (Mausrad) öffnet neuen Tab.</div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

/**
 * Selektieren von Posts, der noch nicht bewertet oder noch nicht durch einen selbst bewertet wurde.
 */
$result = mysqli_query($dbl, "SELECT * FROM `items` WHERE ((`isDonation`='1' AND (`firstsightOrgaId` IS NULL OR (`confirmedOrgaId` IS NULL AND `firstsightOrgaUserId`!='".$userId."'))) AND `extension`!='mp4') AND `confirmedValue`='0.01' ORDER BY RAND()") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) != 0) {
  $content.= "<div class='row'>".
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>";
  while($row = mysqli_fetch_array($result)) {
    $content.= "<a href='/orgafast?postId=".$row['postId']."&orgaId=9'><img src='https://thumb.pr0gramm.com/".$row['thumb']."' alt='Thumb'></a>";
  }
  $content.= "</div>";
} else {
  /**
   * Alles erledigt.
   */
  $content.= "<div class='infobox'>Alles erledigt.</div>";
  $content.= "<div class='spacer-m'></div>";
}
?>
