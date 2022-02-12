<?php
/**
 * unlockuser.php
 * 
 * Seite zum erneuten Freischalten des Perks eines Users.
 * Wenn die Freischaltung bei einem User nicht korrekt funktioniert hat,
 * kann man ihn hiermit neu freischalten ohne den Post zurückzusetzen.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Prüfen ob überhaupt ein perkSecret gesetzt ist.
 */
if(!empty($perkSecret)) {
  /**
   * Prüfung ob eine Post-ID übergeben wurde.
   */
  if(!empty($_GET['user'])) {
    /**
     * Entschärfen des Usernamens und validieren ebenjenes
     */
    if(preg_match('/^[a-z0-9-_]{2,32}$/i', defuse($_GET['user']), $match) === 1) {
      $user = defuse($match[0]);
      $result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `username`='".$user."' AND `isDonation`='1' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_num_rows($result) == 0) {
        /**
         * Wenn der User keinen Spendenpost hat, dann wird er auch nicht freigeschaltet.
         */
        $content.= "<div class='warnbox'>Der User <span class='italic'>".$user."</span> hat keinen Spendenpost erstellt.</div>".PHP_EOL;
      } else {
        if(!isset($_POST['submit'])) {
          /**
           * Es wurde noch nicht bestätigt, dass der User freigeschaltet werden soll.
           */
          /**
           * Formularanzeige
           */
          $content.= "<form action='/unlockuser?user=".$user."' method='post'>".PHP_EOL;
          /**
           * Sitzungstoken
           */
          $content.= "<input type='hidden' name='token' value='".$sessionhash."'>".PHP_EOL;
          /**
           * Bestätigung
           */
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Soll der User freigeschaltet werden?</div>".PHP_EOL.
          "</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><input type='submit' name='submit' value='ja, freischalten'></div>".PHP_EOL.
          "</div>".PHP_EOL;
          $content.= "</form>".PHP_EOL;
        } else {
          /**
           * Es wurde bestätigt, dass der User freigeschaltet werden soll.
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
             * Token gültig. User freischalten.
             */
            require_once($apiCall);
            $response = apiCall("https://pr0gramm.com/api/casino/unlockUser", array("secret" => $perkSecret, "name" => $user));
            if($response['success'] == TRUE) {
              /**
               * Bei Erfolg wird ein Logeintrag erzeugt und eine Erledigtmeldung ausgegeben.
               */
              mysqli_query($dbl, "INSERT INTO `log` (`logLevel`, `userId`, `text`) VALUES (6, '".$userId."', 'User ".$user." manuell freigeschaltet.')") OR DIE(MYSQLI_ERROR($dbl));
              $content.= "<div class='successbox'>User freigeschaltet.</div>".PHP_EOL;
            } else {
              /**
               * Wenn die Freischaltung nicht geklappt hat, wird eine Fehlermeldung ausgegeben und ein Logeintrag erzeugt.
               */
              mysqli_query($dbl, "INSERT INTO `log` (`logLevel`, `userId`, `text`) VALUES (6, '".$userId."', 'User ".$user." konnte nicht manuell freigeschaltet werden.')") OR DIE(MYSQLI_ERROR($dbl));
              $content.= "<div class='warnbox'>Post zurückgesetzt, da Perkfreischaltung fehlschlug.</div>".PHP_EOL;
            }
          }
        }
      }
    } else {
      $content.= "<div class='warnbox'>Der Username <span class='italic'>".output($_GET['user'])."</span> ist ungültig.</div>".PHP_EOL;
    }
  } else {
    /**
     * Es wurde kein Username übergeben. Beende mit einer Fehlermeldung.
     */
    $content.= "<div class='warnbox'>Es wurde kein Username übergeben.</div>".PHP_EOL;
  }
} else {
  /**
   * Kein perkSecret gesetzt.
   */
  $content.= "<div class='warnbox'>Es ist kein perkSecret gesetzt.</div>".PHP_EOL;
}
?>
