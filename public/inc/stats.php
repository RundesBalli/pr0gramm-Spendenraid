<?php
/**
 * stats.php
 * 
 * Statistikseite
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Überschreitungen verschiedener Meilensteine
 */
$content.= "<h1>Zeitpunkte der Tausender-Überschreitung</h1>";
$result = mysqli_query($dbl, "SELECT * FROM `items` ORDER BY `postId` ASC") OR DIE(MYSQLI_ERROR($dbl));
$totalsum = 0;
$thousands = 0;
while($row = mysqli_fetch_array($result)) {
  $totalsum+=$row['confirmedValue'];
  if(floor($totalsum/1000)>$thousands) {
    $thousands = floor($totalsum/1000);
    $content.= $thousands."k - ".date("d.m.Y, H:i:s", $row['created'])."<br>";
  }
}
$content.= "<h1>Zeitpunkte der Zehntausender-Überschreitung</h1>";
$result = mysqli_query($dbl, "SELECT * FROM `items` ORDER BY `postId` ASC") OR DIE(MYSQLI_ERROR($dbl));
$totalsum = 0;
$thousands = 0;
while($row = mysqli_fetch_array($result)) {
  $totalsum+=$row['confirmedValue'];
  if(floor($totalsum/10000)>$thousands) {
    $thousands = floor($totalsum/10000);
    $content.= $thousands."0k - ".date("d.m.Y, H:i:s", $row['created'])."<br>";
  }
}
$content.= "<h1>Zeitpunkte der Hunderttausender-Überschreitung</h1>";
$result = mysqli_query($dbl, "SELECT * FROM `items` ORDER BY `postId` ASC") OR DIE(MYSQLI_ERROR($dbl));
$totalsum = 0;
$thousands = 0;
while($row = mysqli_fetch_array($result)) {
  $totalsum+=$row['confirmedValue'];
  if(floor($totalsum/100000)>$thousands) {
    $thousands = floor($totalsum/100000);
    $content.= $thousands."00k - ".date("d.m.Y, H:i:s", $row['created'])."<br>";
  }
}

/**
 * Häufigste Spendenbeträge
 */
$content.= "<h1>Häufigste Spendenbeträge (5x oder öfter)</h1>";
$result = mysqli_query($dbl, "SELECT `confirmedValue`, count(`confirmedValue`) AS `count` FROM `items` WHERE `isDonation` = '1' AND `confirmedValue`!=0.01 GROUP BY `confirmedValue` HAVING `count`>=5 ORDER BY `count` DESC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $content.= $row['count']."x ".number_format($row['confirmedValue'], 2, ",", ".")." €<br>";
}

/**
 * Größte Spendenbeträge
 */
$content.= "<h1>Größte Spendenbeträge (ab 500 Euro)</h1>";
$result = mysqli_query($dbl, "SELECT `confirmedValue`, `postId`, `username` FROM `items` WHERE `isDonation` = '1' AND `confirmedValue`>=500 ORDER BY `confirmedValue` DESC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $content.= number_format($row['confirmedValue'], 2, ",", ".")." € - <a href='https://pr0gramm.com/new/".$row['postId']."' target='_blank' rel='noopener'>".$row['postId']."</a> von <a href='https://pr0gramm.com/user/".$row['username']."' target='_blank' rel='noopener'>".$row['username']."</a><br>";
}
?>
