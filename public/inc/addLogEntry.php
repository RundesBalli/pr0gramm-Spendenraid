<?php
/**
 * addLogEntry.php
 * 
 * Händisch einen Logeintrag hinzufügen.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Prüfen ob die postId und ein Text übergeben wurde.
 */
if(!empty($_POST['postId'])) {
  $postId = (int)defuse($_POST['postId']);
  if(!empty($_POST['text'])) {
    /**
     * CSRF Prüfung
     */
    if($_POST['token'] != $sessionhash) {
      /**
       * Token ungültig
       */
      $content.= "<div class='warnbox'>Ungültiges Token</div>";
      $content.= "<div class='row'>".
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/overview'>Zurück zur Übersicht</a></div>".
      "</div>";
    } else {
      /**
       * Token gültig. Selektion des Posts.
       */
      $result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_num_rows($result) == 0) {
        /**
         * Wenn der Post nicht existiert, beende mit einer Fehlermeldung.
         */
        $content.= "<div class='infobox'>Der Post existiert nicht (mehr).</div>";
        $content.= "<div class='row'>".
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/overview'>Zurück zur Übersicht</a></div>".
        "</div>";
      } else {
        /**
         * Der Post existiert.
         */
        mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `postId`, `text`) VALUES (".$userId.", 8, ".$postId.", '".defuse($_POST['text'])."')") OR DIE(MYSQLI_ERROR($dbl));
        $content.= "<div class='infobox'>Notiz eingetragen.</div>";
        $content.= "<div class='row'>".
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/postinfo?postId=".$postId."'>Zurück zur PostInfo</a></div>".
        "</div>";
      }
    }
  } else {
    header("Location: /postinfo?postId=".$postId); DIE();
  }
} else {
  header("Location: /overview"); DIE();
}
?>
