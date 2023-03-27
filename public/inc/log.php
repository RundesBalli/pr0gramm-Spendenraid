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
$content.= "<h1>Log</h1>";

/**
 * Tabellenüberschrift
 */
$content.= "<div class='row highlight bold bordered' style='border-left: 6px solid #888888;'>".
"<div class='col-x-4 col-s-4 col-m-1 col-l-1 col-xl-1'>ID</div>".
"<div class='col-x-8 col-s-4 col-m-2 col-l-2 col-xl-2'>Username</div>".
"<div class='col-x-12 col-s-4 col-m-3 col-l-3 col-xl-3'>Zeitpunkt</div>".
"<div class='col-x-12 col-s-4 col-m-2 col-l-2 col-xl-2'>PostID</div>".
"<div class='col-x-12 col-s-8 col-m-4 col-l-4 col-xl-4'>Text</div>".
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".
"</div>";

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
$result = mysqli_query($dbl, "SELECT `log`.`id`, `users`.`username`, `users`.`isBot`, `log`.`timestamp`, `logLevel`.`id` AS `logLevelId`, `logLevel`.`color`, `log`.`postId`, `log`.`text` FROM `log` LEFT OUTER JOIN `users` ON `users`.`id`=`log`.`userId` JOIN `logLevel` ON `log`.`logLevel`=`logLevel`.`id` ".$where."ORDER BY `log`.`id` DESC LIMIT 100") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $colorRgb = hex2rgb($row['color']);
  $content.= "<div class='row hover bordered' style='border-left: 6px solid #".$row['color']."; background-color: rgba(".$colorRgb['r'].", ".$colorRgb['g'].", ".$colorRgb['b'].", 0.04);'>".
  "<div class='col-x-4 col-s-4 col-m-1 col-l-1 col-xl-1'>".$row['id']."</div>".
  "<div class='col-x-8 col-s-4 col-m-2 col-l-2 col-xl-2'>".($row['username'] === NULL ? "<span class='italic'>System</span>" : ($row['username'] == $username ? "<span class='highlight'>".output($row['username'])."</span>" : ($row['isBot'] ? "<span class='italic'>".output($row['username'])."</span>" : output($row['username']))))."</div>".
  "<div class='col-x-12 col-s-4 col-m-3 col-l-3 col-xl-3'>".date("d.m.Y, H:i:s", strtotime($row['timestamp']))."</div>".
  "<div class='col-x-12 col-s-4 col-m-2 col-l-2 col-xl-2'>".($row['postId'] === NULL ? "<span class='italic'>NULL</span>" : "<a href='https://pr0gramm.com/new/".$row['postId']."' target='_blank' rel='noopener'>".$row['postId']."</a> - <a href='/postinfo?postId=".$row['postId']."' rel='noopener'>Info</a>".($row['logLevelId'] != 5 ? "<br><a href='/resetpost?postId=".$row['postId']."'>Post zurücksetzen</a><br><a href='/orgareset?postId=".$row['postId']."'>Orga zurücksetzen</a>" : NULL))."</div>".
  "<div class='col-x-12 col-s-8 col-m-4 col-l-4 col-xl-4'>".clickableLink(output($row['text']))."</div>".
  "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".
  "</div>";

  $logIds[] = $row['id'];
}
$result = mysqli_query($dbl, "SELECT (SELECT count(`id`) FROM `log` WHERE `id`<'".min($logIds)."') AS `older`") OR DIE(MYSQLI_ERROR($dbl));
$row = mysqli_fetch_array($result);
$content.= "<div class='row'>".
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 text-right'>".($row['older'] != 0 ? "<a href='/log?older=".min($logIds)."'>Älter »</a>" : NULL)."</div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

/**
 * Loglevel
 */
$content.= "<h2>Loglevel</h2>";
$result = mysqli_query($dbl, "SELECT * FROM `logLevel` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $content.= "<div class='row'>".
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 hover' style='color: #".$row['color'].";'>".$row['title']."</div>".
  "</div>";
}
$content.= "<div class='spacer-m'></div>";

/**
 * Highscore (Logeinträge)
 */
$content.= "<h2>Highscore (Logeinträge)</h2>";
$content.= "<div class='row highlight bold'>".
"<div class='col-x-2 col-s-2 col-m-2 col-l-2 col-xl-2'>Platz</div>".
"<div class='col-x-6 col-s-6 col-m-4 col-l-4 col-xl-4'>Username</div>".
"<div class='col-x-4 col-s-4 col-m-6 col-l-6 col-xl-6'>Einträge (Δ)</div>".
"</div>";
/**
 * User
 */
$result = mysqli_query($dbl, "SELECT count(`log`.`id`) AS `count`, `users`.`username` FROM `log` LEFT OUTER JOIN `users` ON `users`.`id`=`log`.`userId` WHERE `userId` IS NOT NULL".(!empty($kiUserId) ? " AND `userId`!=".$kiUserId : NULL)." GROUP BY `userId` ORDER BY `count` DESC") OR DIE(MYSQLI_ERROR($dbl));
$platz = 0;
$previous = 0;
while($row = mysqli_fetch_array($result)) {
  $platz++;
  $content.= "<div class='row hover'>".
  "<div class='col-x-2 col-s-2 col-m-2 col-l-2 col-xl-2'>".($platz == 1 ? "&#x1F451;" : $platz)."</div>".
  "<div class='col-x-6 col-s-6 col-m-4 col-l-4 col-xl-4'>".output($row['username'])."</div>".
  "<div class='col-x-4 col-s-4 col-m-6 col-l-6 col-xl-6'>".$row['count'].($previous != 0 ? " (".($previous-$row['count']).")" : NULL)."</div>".
  "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".
  "</div>";
  $previous = $row['count'];
}
/**
 * System
 */
$content.= "<div class='spacer-m'></div>";
$content.= "<h2>Highscore (System / KI)</h2>";
$content.= "<div class='row highlight bold'>".
"<div class='col-x-2 col-s-2 col-m-2 col-l-2 col-xl-2'>Symbol</div>".
"<div class='col-x-6 col-s-6 col-m-4 col-l-4 col-xl-4'>System / KI</div>".
"<div class='col-x-4 col-s-4 col-m-6 col-l-6 col-xl-6'>Einträge</div>".
"</div>";
$result = mysqli_query($dbl, "SELECT count(`log`.`id`) AS `count`, `userId` FROM `log` WHERE `userId` IS NULL".(!empty($kiUserId) ? " OR `userId`=".$kiUserId : NULL)." GROUP BY `userId` ORDER BY `count` DESC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $content.= "<div class='row hover'>".
  "<div class='col-x-2 col-s-2 col-m-2 col-l-2 col-xl-2'>&#x1F5A5;</div>".
  "<div class='col-x-6 col-s-6 col-m-4 col-l-4 col-xl-4'>".($row['userId'] === NULL ? "<span class='italic'>System</span>" : "<span class='italic'>KI</span>")."</div>".
  "<div class='col-x-4 col-s-4 col-m-6 col-l-6 col-xl-6'>".$row['count']."</div>".
  "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".
  "</div>";
}
$content.= "<div class='spacer-m'></div>";
?>
