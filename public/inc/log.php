<?php
/**
 * Log.php
 * 
 * Aktionslog
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');


/**
 * Titel und Überschrift
 */
$title = "Log";
$content.= "<h1>Log</h1>".PHP_EOL;

/**
 * Tabellenüberschrift
 */
$content.= "<div class='row highlight bold bordered' style='border-left: 6px solid #888888;'>".PHP_EOL.
"<div class='col-x-4 col-s-4 col-m-1 col-l-1 col-xl-1'>ID</div>".PHP_EOL.
"<div class='col-x-8 col-s-4 col-m-2 col-l-2 col-xl-2'>Username</div>".PHP_EOL.
"<div class='col-x-12 col-s-4 col-m-3 col-l-3 col-xl-3'>Zeitpunkt</div>".PHP_EOL.
"<div class='col-x-12 col-s-4 col-m-2 col-l-2 col-xl-2'>PostID</div>".PHP_EOL.
"<div class='col-x-12 col-s-8 col-m-4 col-l-4 col-xl-4'>Text</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

/**
 * Suchparameter
 */
$where = "";
if(!empty($_GET['older'])) {
  $older = (int)defuse($_GET['older']);
  if($older > 1) {
    $where = "WHERE `log`.`id` < '".$older."' ";
  }
}

/**
 * Loganzeige
 */
$result = mysqli_query($dbl, "SELECT `log`.`id`, `users`.`username`, `log`.`timestamp`, `loglevel`.`id` AS `loglevelId`, `loglevel`.`color`, `log`.`postId`, `log`.`text` FROM `log` LEFT OUTER JOIN `users` ON `users`.`id`=`log`.`userId` JOIN `loglevel` ON `log`.`loglevel`=`loglevel`.`id` ".$where."ORDER BY `log`.`id` DESC LIMIT 100") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $content.= "<div class='row hover bordered' style='border-left: 6px solid #".$row['color'].";'>".PHP_EOL.
  "<div class='col-x-4 col-s-4 col-m-1 col-l-1 col-xl-1'>".$row['id']."</div>".PHP_EOL.
  "<div class='col-x-8 col-s-4 col-m-2 col-l-2 col-xl-2'>".($row['username'] === NULL ? "<span class='italic'>System</span>" : ($row['username'] == $username ? "<span class='highlight'>".$row['username']."</span>" : $row['username']))."</div>".PHP_EOL.
  "<div class='col-x-12 col-s-4 col-m-3 col-l-3 col-xl-3'>".date("d.m.Y, H:i:s", strtotime($row['timestamp']))."</div>".PHP_EOL.
  "<div class='col-x-12 col-s-4 col-m-2 col-l-2 col-xl-2'>".($row['postId'] === NULL ? "<span class='italic'>NULL</span>" : "<a href='https://pr0gramm.com/new/".$row['postId']."' target='_blank' rel='noopener'>".$row['postId']."</a> - <a href='/postinfo?postId=".$row['postId']."' target='_blank' rel='noopener'>Info</a>".($row['loglevelId'] != 5 ? "<br><a href='/resetpost?postId=".$row['postId']."'>Post zurücksetzen</a><br><a href='/orgareset?postId=".$row['postId']."'>Orga zurücksetzen</a>" : NULL))."</div>".PHP_EOL.
  "<div class='col-x-12 col-s-8 col-m-4 col-l-4 col-xl-4'>".$row['text']."</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
  "</div>".PHP_EOL;

  $logIds[] = $row['id'];
}
$result = mysqli_query($dbl, "SELECT (SELECT count(`id`) FROM `log` WHERE `id`<'".min($logIds)."') AS `older`") OR DIE(MYSQLI_ERROR($dbl));
$row = mysqli_fetch_array($result);
$content.= "<div class='row'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 text-right'>".($row['older'] != 0 ? "<a href='/log?older=".min($logIds)."'>Älter »</a>" : NULL)."</div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Loglevel
 */
$content.= "<h2>Loglevel</h2>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT * FROM `loglevel` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 hover' style='color: #".$row['color'].";'>".$row['title']."</div>".PHP_EOL.
  "</div>".PHP_EOL;
}
$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Highscore (Logeinträge)
 */
$content.= "<h2>Highscore (Logeinträge)</h2>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT count(`log`.`id`) AS `count`, `users`.`username` FROM `log` LEFT OUTER JOIN `users` ON `users`.`id`=`log`.`userId` GROUP BY `userId` ORDER BY `count` DESC") OR DIE(MYSQLI_ERROR($dbl));
$content.= "<div class='row highlight bold'>".PHP_EOL.
"<div class='col-x-2 col-s-2 col-m-2 col-l-2 col-xl-2'>Platz</div>".PHP_EOL.
"<div class='col-x-6 col-s-6 col-m-4 col-l-4 col-xl-4'>Username</div>".PHP_EOL.
"<div class='col-x-4 col-s-4 col-m-6 col-l-6 col-xl-6'>Einträge</div>".PHP_EOL.
"</div>".PHP_EOL;
$platz = 0;
while($row = mysqli_fetch_array($result)) {
  if($row['username'] !== NULL) {
    $platz++;
  }
  $content.= "<div class='row hover'>".PHP_EOL.
  "<div class='col-x-2 col-s-2 col-m-2 col-l-2 col-xl-2'>".($row['username'] === NULL ? "&#x1F5A5;" : ($platz == 1 ? "&#x1F451;" : $platz))."</div>".PHP_EOL.
  "<div class='col-x-6 col-s-6 col-m-4 col-l-4 col-xl-4'>".($row['username'] === NULL ? "<span class='italic'>System</span>" : $row['username'])."</div>".PHP_EOL.
  "<div class='col-x-4 col-s-4 col-m-6 col-l-6 col-xl-6'>".$row['count']."</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
  "</div>".PHP_EOL;
}
$content.= "<div class='spacer-m'></div>".PHP_EOL;
?>
