<?php
/**
 * orgareset.php
 * 
 * Seite zum Zurücksetzen der Orga eines Posts. Nur aus dem Log aufrufbar.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Prüfung ob eine Post-ID übergeben wurde.
 */
if(!empty($_GET['postId'])) {
  /**
   * Entschärfen der Post-ID und Abfrage ob der Post existiert.
   */
  $postId = (int)defuse($_GET['postId']);
  $result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Wenn der Post nicht existiert, beende mit einer Fehlermeldung.
     */
    $content.= "<div class='warnbox'>Der angeforderte Post existiert nicht.</div>".PHP_EOL;
  } else {
    if(!isset($_POST['submit'])) {
      /**
       * Es wurde noch nicht bestätigt, dass der Post zurückgesetzt werden soll.
       */
      /**
       * Formularanzeige
       */
      $content.= "<form action='/orgareset?postId=".$postId."' method='post'>".PHP_EOL;
      /**
       * Sitzungstoken
       */
      $content.= "<input type='hidden' name='token' value='".$sessionhash."'>".PHP_EOL;
      /**
       * Bestätigung
       */
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Soll die Orga vom Post zurückgesetzt werden?</div>".PHP_EOL.
      "</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><input type='submit' name='submit' value='ja, zurücksetzen'></div>".PHP_EOL.
      "</div>".PHP_EOL;
      $content.= "</form>".PHP_EOL;
    } else {
      /**
       * Es wurde bestätigt, dass der Post zurückgesetzt werden soll.
       */
      /**
       * CSRF Prüfung
       */
      if($_POST['token'] != $sessionhash) {
        /**
         * Token ungültig
         */
        $content.= "<div class='warnbox'>Ungültiges Token</div>".PHP_EOL;
      } else {
        /**
         * Token gültig. Post zurücksetzen.
         */
        mysqli_query($dbl, "UPDATE `items` SET `firstsightOrgaId`=NULL, `firstsightOrgaUserId`=NULL, `confirmedOrgaId`=NULL, `confirmedOrgaUserId`=NULL WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `postId`, `text`) VALUES ('".$userId."', 5, '".$postId."', 'Orga zurückgesetzt')") OR DIE(MYSQLI_ERROR($dbl));
        $content.= "<div class='successbox'>Post zurückgesetzt.<br><a href='/orga'>Organisationen bewerten</a></div>".PHP_EOL;
      }
    }
  }
} else {
  /**
   * Es wurde keine Post-ID übergeben. Beende mit einer Fehlermeldung.
   */
  $content.= "<div class='warnbox'>Es wurde keine Post-ID übergeben.</div>".PHP_EOL;
}
?>
