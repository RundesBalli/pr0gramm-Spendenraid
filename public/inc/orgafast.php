<?php
/**
 * orgafast.php
 * 
 * Eintragungsseite für z.B. dkmsfast.php
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "Organisation Schnellbewertung";
$content.= "<h1>Organisation Schnellbewertung</h1>".PHP_EOL;

/**
 * Organisation eintragen
 */
if(!empty($_GET['postId']) AND !empty($_GET['orgaId'])) {
  $postId = (int)defuse($_GET['postId']);
  $orgaId = (int)defuse($_GET['orgaId']);
  $result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Wenn der Post nicht existiert, beende mit einer Fehlermeldung.
     */
    $content.= "<div class='infobox'>Der Post existiert nicht (mehr).</div>".PHP_EOL;
  } else {
    /**
     * Wenn der Post existiert prüfe zuerst ob schon eine Erstsichtung durchgeführt wurde.
     */
    $row = mysqli_fetch_array($result);
    if($row['firstsightOrgaId'] === NULL OR $row['firstsightOrgaUserId'] === NULL) {
      /**
       * Wenn noch keine Erstsichtung durchgeführt wurde, dann leg sie an.
       */
      mysqli_query($dbl, "UPDATE `items` SET `firstsightOrgaId`='".$orgaId."', `firstsightOrgaUserId`='".$userId."' WHERE `postId`='".$postId."' LIMIT 1");
      if(mysqli_errno($dbl) == 1452) {
        $content.= "<div class='warnbox'>Die Organisation existiert nicht.</div>".PHP_EOL;
      } elseif(mysqli_errno($dbl) == 0) {
        mysqli_query($dbl, "INSERT INTO `log` (`userId`, `loglevel`, `postId`, `text`) VALUES ('".$userId."', 2, '".$postId."', '(Schnell) Orga: ".$orgaId."')") OR DIE(MYSQLI_ERROR($dbl));
        $content.= "<div class='successbox'>Organisation eingetragen.</div>".PHP_EOL;
      } else {
        die(MYSQLI_ERROR($dbl));
      }
    } elseif($row['confirmedOrgaId'] === NULL OR $row['confirmedOrgaUserId'] === NULL) {
      /**
       * Wenn bereits eine Erstsichtung stattgefunden hat, dann prüfe, ob man selbst der Prüfende war.
       */
      if($row['firstsightOrgaUserId'] == $userId) {
        /**
         * Fehlermeldung, wenn man selbst der Erstsichtende war.
         */
        $content.= "<div class='warnbox'>Du kannst nicht die Erst- und Zweitsichtung machen.</div>".PHP_EOL;
      } else {
        /**
         * Erstsichtung erfolgte von jemand anderem. Prüfe ob die eingetragene Summe mit der übergebenen Summe übereinstimmt.
         */
        if($row['firstsightOrgaId'] != $orgaId) {
          /**
           * Erst- und Zweitsichtung stimmen nicht überein. Post wird zurückgesetzt und die Zweitsichtung wird zur Erstsichtung.
           */
          mysqli_query($dbl, "UPDATE `items` SET `firstsightOrgaId`='".$orgaId."', `firstsightOrgaUserId`='".$userId."' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          mysqli_query($dbl, "INSERT INTO `log` (`userId`, `loglevel`, `postId`, `text`) VALUES ('".$userId."', 3, '".$postId."', '(Schnell) Orga: ".$orgaId." (Erstsichtung: ".$row['firstsightOrgaId'].")')") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Organisation eingetragen.</div>".PHP_EOL;
        } else {
          /**
           * Erst- und Zweitsichtung stimmen überein.
           */
          mysqli_query($dbl, "UPDATE `items` SET `confirmedOrgaId`='".$orgaId."', `confirmedOrgaUserId`='".$userId."' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          mysqli_query($dbl, "INSERT INTO `log` (`userId`, `loglevel`, `postId`, `text`) VALUES ('".$userId."', 4, '".$postId."', '(Schnell) Orga: ".$orgaId."')") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Organisation eingetragen.</div>".PHP_EOL;
        }
      }
    } else {
      /**
       * Wenn alle Felder ausgefüllt waren, dann war jemand anders schneller :o)
       */
    }
  }
}
?>
