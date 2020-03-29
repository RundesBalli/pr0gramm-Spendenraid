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
$content.= "<h1>PostInfo</h1>".PHP_EOL;

/**
 * Formularanzeige
 */
$content.= "<form action='/postinfo' method='post'>".PHP_EOL;
/**
 * Geldbetrag
 */
$content.= "<div class='row'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Post</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-5 col-l-5 col-xl-5'><input name='postId' type='text' autocomplete='off' placeholder='Postlink / ID' autofocus></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-5 col-l-5 col-xl-5'>Ganzer Link oder ID</div>".PHP_EOL.
"</div>".PHP_EOL;
/**
 * Absenden
 */
$content.= "<div class='row'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Info</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-10 col-l-10 col-xl-10'><input name='submit' type='submit' value='Info'></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "</form>".PHP_EOL;
$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Anzeige des Posts
 */
if(isset($_POST['submit']) AND !empty($_POST['postId'])) {
  $content.= "<h1>Info</h1>";
  if(preg_match('/(?:(?:http(?:s?):\/\/pr0gramm\.com)?\/(?:top|new|user\/\w+\/(?:uploads|likes)|stalk)(?:(?:\/\w+)?)\/)?([1-9]\d*)(?:(?::comment(?:\d+))?)?/i', defuse($_POST['postId']), $match) === 1) {
    $postId = (int)$match[1];
    $result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Der Post ist nicht in der Datenbank.</div>".PHP_EOL;
    } else {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/resetpost?postId=".$row['postId']."'>Post zurücksetzen</a> - <a href='/orgareset?postId=".$row['postId']."'>Orga zurücksetzen</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><pre>".var_export($row, TRUE)."</pre></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  } else {
    $content.= "<div class='warnbox'>Eingabe ungültig.</div>".PHP_EOL;
  }
}
?>
