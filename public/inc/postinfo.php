<?php
/**
 * postinfo.php
 * 
 * Seite zum Anzeigen eines Posts
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "PostInfo";
$content.= "<h1>PostInfo</h1>";

/**
 * Formularanzeige
 */
$content.= "<form action='/postinfo' method='get'>";
/**
 * PostId/Link
 */
$content.= "<div class='row'>".
"<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Post</div>".
"<div class='col-x-12 col-s-12 col-m-5 col-l-5 col-xl-5'><input name='postId' type='text' autocomplete='off' placeholder='Postlink / ID' autofocus></div>".
"<div class='col-x-12 col-s-12 col-m-5 col-l-5 col-xl-5'>Ganzer Link oder ID</div>".
"</div>";
/**
 * Absenden
 */
$content.= "<div class='row'>".
"<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Info</div>".
"<div class='col-x-12 col-s-12 col-m-10 col-l-10 col-xl-10'><input type='submit' value='Info'></div>".
"</div>";
$content.= "</form>";
$content.= "<div class='spacer-m'></div>";

/**
 * Anzeige des Posts
 */
if(!empty($_GET['postId'])) {
  if(preg_match('/(?:(?:http(?:s?):\/\/pr0gramm\.com)?\/(?:top|new|user\/\w+\/(?:uploads|likes)|stalk)(?:(?:\/\w+)?)\/)?([1-9]\d*)(?:(?::comment(?:\d+))?)?/i', defuse($_GET['postId']), $match) === 1) {
    $postId = (int)$match[1];
    $result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Der Post ist nicht in der Datenbank.</div>";
    } else {
      $row = mysqli_fetch_assoc($result);
      /**
       * Überschrift
       */
      $content.= "<h1>Info, PostId ".$row['postId']."</h1>";
      /**
       * Links
       */
      $content.= "<h2>Links</h2>";
      $content.= "<div class='row'>".
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/resetpost?postId=".$row['postId']."'>Post zurücksetzen</a> - <a href='/orgareset?postId=".$row['postId']."'>Orga zurücksetzen</a>".(($row['isDonation'] == 1 AND !empty($perkSecret)) ? " - <a href='/unlockuser?user=".$row['username']."'>User erneut freischalten</a>" : NULL)."</div>".
      "</div>";
      /**
       * DB Dump
       */
      $content.= "<h2>DB-Dump</h2>";
      $content.= "<div class='row'>".
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><pre>".var_export($row, TRUE)."</pre></div>".
      "</div>";
      /**
       * Logeintrag hinzufügen
       */
      $content.= "<h2>Logeintrag hinzufügen</h2>";
      $content.= "<form action='/addLogEntry' method='post'>";
      $content.= "<input type='hidden' name='postId' value='".$row['postId']."'>";
      $content.= "<input type='hidden' name='token' value='".$sessionhash."'>";
      $content.= "<div class='row'>".
      "<div class='col-x-12 col-s-12 col-m-8 col-l-8 col-xl-8'><input name='text' type='text' autocomplete='off' placeholder='Notiz'></div>".
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' value='Eintragen'></div>".
      "</div>";
      $content.= "</form>";
      /**
       * Log zum Artikel anzeigen
       */
      $content.= "<h2>Log</h2>";
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
      $result = mysqli_query($dbl, "SELECT `log`.`id`, `users`.`username`, `users`.`isBot`, `log`.`timestamp`, `logLevel`.`id` AS `logLevelId`, `logLevel`.`color`, `log`.`postId`, `log`.`text` FROM `log` LEFT OUTER JOIN `users` ON `users`.`id`=`log`.`userId` JOIN `logLevel` ON `log`.`logLevel`=`logLevel`.`id` WHERE `postId`='".$row['postId']."' ORDER BY `log`.`id` DESC") OR DIE(MYSQLI_ERROR($dbl));
      while($row = mysqli_fetch_array($result)) {
        $content.= "<div class='row hover bordered' style='border-left: 6px solid #".$row['color'].";'>".
        "<div class='col-x-4 col-s-4 col-m-1 col-l-1 col-xl-1'>".$row['id']."</div>".
        "<div class='col-x-8 col-s-4 col-m-2 col-l-2 col-xl-2'>".($row['username'] === NULL ? "<span class='italic'>System</span>" : ($row['username'] == $username ? "<span class='highlight'>".output($row['username'])."</span>" : ($row['isBot'] ? "<span class='italic'>".output($row['username'])."</span>" : output($row['username']))))."</div>".
        "<div class='col-x-12 col-s-4 col-m-3 col-l-3 col-xl-3'>".date("d.m.Y, H:i:s", strtotime($row['timestamp']))."</div>".
        "<div class='col-x-12 col-s-4 col-m-2 col-l-2 col-xl-2'>".($row['postId'] === NULL ? "<span class='italic'>NULL</span>" : "<a href='https://pr0gramm.com/new/".$row['postId']."' target='_blank' rel='noopener'>".$row['postId']."</a>".($row['logLevelId'] != 5 ? "<br><a href='/resetpost?postId=".$row['postId']."'>Post zurücksetzen</a><br><a href='/orgareset?postId=".$row['postId']."'>Orga zurücksetzen</a>" : NULL))."</div>".
        "<div class='col-x-12 col-s-8 col-m-4 col-l-4 col-xl-4'>".$row['text']."</div>".
        "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".
        "</div>";
      }
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
    }
  } else {
    $content.= "<div class='warnbox'>Eingabe ungültig.</div>";
  }
}
?>
