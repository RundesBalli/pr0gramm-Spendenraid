<?php
/**
 * delfake.php
 * 
 * Fakes löschen, die sich als unbestätigt herausgestellt haben.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "Fake löschen";
$content.= "<h1>Fake löschen</h1>";

/**
 * Prüfung ob eine Post-ID übergeben wurde.
 */
if(!empty($_GET['id'])) {
  /**
   * Entschärfen der Post-ID und Abfrage ob der Post existiert.
   */
  $id = (int)defuse($_GET['id']);
  $result = mysqli_query($dbl, "SELECT * FROM `fakes` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Wenn der Eintrag nicht existiert, beende mit einer Fehlermeldung.
     */
    $content.= "<div class='warnbox'>Der Fake-Eintrag existiert nicht.</div>";
  } else {
    if(!isset($_POST['submit'])) {
      /**
       * Es wurde noch nicht bestätigt, dass der Eintrag gelöscht werden soll.
       */
      /**
       * Formularanzeige
       */
      $content.= "<form action='/delfake?id=".$id."' method='post'>";
      /**
       * Sitzungstoken
       */
      $content.= "<input type='hidden' name='token' value='".$sessionhash."'>";
      /**
       * Bestätigung
       */
      $content.= "<div class='row'>".
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Soll der Eintrag gelöscht werden?</div>".
      "</div>";
      $content.= "<div class='row'>".
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><input type='submit' name='submit' value='ja, löschen'></div>".
      "</div>";
      $content.= "</form>";
    } else {
      /**
       * Es wurde bestätigt, dass der Eintrag gelöscht werden soll.
       */
      /**
       * CSRF Prüfung
       */
      if($_POST['token'] != $sessionhash) {
        /**
         * Token ungültig
         */
        $content.= "<div class='warnbox'>Ungültiges Token</div>";
      } else {
        /**
         * Token gültig. Eintrag kann gelöscht werden.
         */
        $row = mysqli_fetch_array($result);
        mysqli_query($dbl, "DELETE FROM `fakes` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `text`) VALUES ('".$userId."', 7, 'Fake-Eintrag gelöscht (Orig: ".$row['postIdOriginal'].", Fake: ".$row['postIdFake'].")')") OR DIE(MYSQLI_ERROR($dbl));
        $content.= "<div class='successbox'>Eintrag gelöscht.</div>";
      }
    }
  }
} else {
  /**
   * Es wurde keine Eintrags-ID übergeben. Beende mit einer Fehlermeldung.
   */
  $content.= "<div class='warnbox'>Es wurde keine Eintrags-ID übergeben.</div>";
}
?>
