<?php
/**
 * fakepostsgt.php
 * 
 * Anzeige aller Posts, die die selben Maße und die selben Beträge und Organisationen haben.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "Duplikatfinder Gute Tat";
$content.= "<h1>Duplikatfinder Gute Tat</h1>".PHP_EOL;

/**
 * Alle gleichen Werte finden (Höhe, Breite, Spendenbetrag, Organisation)
 */
$result = mysqli_query($dbl, "SELECT COUNT(`id`) AS `k`, `height`, `width`, `confirmedValue`, `confirmedOrgaId` FROM `items` WHERE `isDonation`='1' AND ((`extension` != 'gif' AND `extension` != 'mp4') AND `confirmedOrgaId`='9') GROUP BY `height`, `width`, `confirmedValue`, `confirmedOrgaId` HAVING `k`>1 ORDER BY `k` DESC") OR DIE(MYSQLI_ERROR($dbl));

/**
 * Innerhalb dieser zusammengefassten Werte alle Posts ausfindig machen und ausgeben
 */
while($row = mysqli_fetch_array($result)) {
  $query = "SELECT * FROM `items` WHERE `height`='".$row['height']."' AND `width`='".$row['width']."' AND `confirmedValue`='".$row['confirmedValue']."' AND `confirmedOrgaId`='".$row['confirmedOrgaId']."' ORDER BY `postId` ASC";
  $content.= "<h3 style='font-family: monospace;' class='highlight'>$query</h3>";
  $innerres = mysqli_query($dbl, $query) OR DIE(MYSQLI_ERROR($dbl));
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>".PHP_EOL;
  while($innerrow = mysqli_fetch_array($innerres)) {
    $content.= "<a href='https://pr0gramm.com/new/".$innerrow['postId']."' target='_blank' rel='noopener'><img src='https://img.pr0gramm.com/".$innerrow['image']."' alt='Bild' class='imgmaxheight' style='margin: 5px;'></a>".PHP_EOL;
  }
  $content.= "</div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
}
?>
