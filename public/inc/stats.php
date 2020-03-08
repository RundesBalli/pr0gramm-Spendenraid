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
?>
