<?php
/**
 * dellist.php
 * 
 * Anzeige der Posts, die das Löschkennzeichen vom großen Crawl haben.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "Löschliste";
$content.= "<h1>Löschliste</h1>";

/**
 * Posts mit delflag=1 selektieren und ausgeben
 */
$result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `delflag`='1'") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) != 0) {
  $content.= "<div class='row highlight bold'>".
  "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>PostID</div>".
  "<div class='col-x-6 col-s-6 col-m-4 col-l-4 col-xl-4'>Spendensummen</div>".
  "<div class='col-x-6 col-s-6 col-m-4 col-l-4 col-xl-4'>Organisationen</div>".
  "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Aktionen</div>".
  "</div>";
  while($row = mysqli_fetch_array($result)) {
    $content.= "<div class='row hover bordered'>".
    "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'><a href='https://pr0gramm.com/new/".$row['postId']."' target='_blank' rel='noopener'>".$row['postId']."</a></div>".
    "<div class='col-x-6 col-s-6 col-m-4 col-l-4 col-xl-4'>Erstsicht: ".($row['firstsightValue'] !== NULL ? number_format($row['firstsightValue'], 2, ".", ",") : "<span class='italic'>NULL</span>")." €<br>Zweitsicht: ".($row['confirmedValue'] !== NULL ? number_format($row['confirmedValue'], 2, ".", ",") : "<span class='italic'>NULL</span>")." €</div>".
    "<div class='col-x-6 col-s-6 col-m-4 col-l-4 col-xl-4'>Erstsicht: ".($row['firstsightOrgaId'] !== NULL ? $row['firstsightOrgaId'] : "<span class='italic'>NULL</span>")."<br>Zweitsicht: ".($row['confirmedOrgaId'] !== NULL ? $row['confirmedOrgaId'] : "<span class='italic'>NULL</span>")."</div>".
    "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'><a href='/delpost?postId=".$row['postId']."'>Löschen</a></div>".
    "</div>";
  }
} else {
  $content.= "<div class='infobox'>Es gibt keine Posts mit Löschkennzeichen.</div>";
}
?>
