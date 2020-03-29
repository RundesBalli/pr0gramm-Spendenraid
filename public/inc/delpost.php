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
$title = "Post löschen";
$content.= "<h1>Post löschen</h1>".PHP_EOL;

/**
 * Prüfung ob eine Post-ID übergeben wurde.
 */
if(!empty($_GET['postId'])) {
  /**
   * Entschärfen der Post-ID und Abfrage ob der Post existiert.
   */
  $postId = (int)defuse($_GET['postId']);
  $result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `postId`='".$postId."' AND `delflag`='1' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Wenn der Post nicht existiert, beende mit einer Fehlermeldung.
     */
    $content.= "<div class='warnbox'>Der angeforderte Post existiert nicht oder ist nicht mit einem Löschkennzeichen versehen.</div>".PHP_EOL;
  } else {
    if(!isset($_POST['submit'])) {
      /**
       * Es wurde noch nicht bestätigt, dass der Post gelöscht werden soll.
       */
      /**
       * Formularanzeige
       */
      $content.= "<form action='/delpost?postId=".$postId."' method='post'>".PHP_EOL;
      /**
       * Sitzungstoken
       */
      $content.= "<input type='hidden' name='token' value='".$sessionhash."'>".PHP_EOL;
      /**
       * Bestätigung
       */
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Soll der Post gelöscht werden?</div>".PHP_EOL.
      "</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><input type='submit' name='submit' value='ja, löschen'></div>".PHP_EOL.
      "</div>".PHP_EOL;
      $content.= "</form>".PHP_EOL;
    } else {
      /**
       * Es wurde bestätigt, dass der Post gelöscht werden soll.
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
         * Token gültig. Post kann gelöscht werden. Nun muss geprüft werden, ob der zu löschende
         * Post ein Spendenpost war, und ob dem User das Perk wieder gesperrt werden muss.
         */
        $row = mysqli_fetch_array($result);
        if(!empty($perkSecret) AND $row['isDonation'] == 1) {
          $innerresult = mysqli_query($dbl, "SELECT * FROM `items` WHERE (`username`='".$row['username']."' AND `isDonation`='1') AND `delflag`='0'") OR DIE(MYSQLI_ERROR($dbl));
          if(mysqli_num_rows($innerresult) == 0) {
            /**
             * Keine weiteren validierten Spendenposts, also wird der User wieder gesperrt.
             */
            require_once($apiCall);
            $response = apiCall("https://pr0gramm.com/api/slots/lockuser", array("secret" => $perkSecret, "username" => $row['username']));
            if($response['success'] == TRUE) {
              /**
               * Bei Erfolg wird ein Logeintrag erzeugt.
               */
              $content.= "<div class='successbox'>Perk gesperrt.</div>".PHP_EOL;
              mysqli_query($dbl, "INSERT INTO `log` (`userId`, `loglevel`, `text`) VALUES ('".$userId."', 6, 'Perk gesperrt (User: ".$row['username'].", ID: ".$row['postId'].")')") OR DIE(MYSQLI_ERROR($dbl));
            } else {
              /**
               * Wenn die Freischaltung nicht geklappt hat, wird ein gesonderter Logeintrag erzeugt und eine Fehlermeldung ausgegeben.
               */
              $content.= "<div class='warnbox'>Konnte Perk nicht sperren.</div>".PHP_EOL;
              mysqli_query($dbl, "INSERT INTO `log` (`userId`, `loglevel`, `text`) VALUES ('".$userId."', 6, 'Perk-Sperrung fehlgeschlagen! (User: ".$row['username'].", ID: ".$row['postId'].")')") OR DIE(MYSQLI_ERROR($dbl));
            }
          } else {
            $content.= "<div class='infobox'>User hat noch andere Spendenpost(s). Daher muss der Perk nicht gesperrt werden.</div>".PHP_EOL;
          }
        }
        mysqli_query($dbl, "DELETE FROM `items` WHERE `id`='".$row['id']."' AND `delflag`='1' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        mysqli_query($dbl, "INSERT INTO `log` (`userId`, `loglevel`, `text`) VALUES ('".$userId."', 5, 'Post gelöscht da auf pr0gramm nicht mehr vorhanden (User: ".$row['username'].", ID: ".$row['postId'].")')") OR DIE(MYSQLI_ERROR($dbl));
        $content.= "<div class='successbox'>Post gelöscht.</div>".PHP_EOL;
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
