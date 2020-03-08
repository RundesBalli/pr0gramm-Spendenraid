<?php
/**
 * valuation.php
 * 
 * Bewertungsseite zum Setzen des Spendenwertes / des Nicht-Spenden-Status
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Spendenbetrag eintragen, falls das Formular übergeben wurde.
 */
if(isset($_POST['submit'])) {
  $postId = (int)defuse($_POST['postId']);
  /**
   * Prüfung ob eine gültige Zahl eingegeben wurde
   */
  if(is_numeric($_POST['value'])) {
    $value = (double)str_replace(",", ".", defuse($_POST['value']));
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
       * Token gültig. Selektion des Posts.
       */
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
        if($row['firstsightValue'] === NULL OR $row['firstsightUserId'] === NULL) {
          /**
           * Wenn noch keine Erstsichtung durchgeführt wurde, dann leg sie an.
           */
          mysqli_query($dbl, "UPDATE `items` SET `firstsightValue`='".$value."', `firstsightUserId`='".$userId."' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          mysqli_query($dbl, "INSERT INTO `log` (`userId`, `loglevel`, `postId`, `text`) VALUES ('".$userId."', 2, '".$postId."', '".number_format($value, 2, ",", ".")." €')") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Spendenwert eingetragen.</div>".PHP_EOL;
        } elseif($row['confirmedValue'] === NULL OR $row['confirmedUserId'] === NULL) {
          /**
           * Wenn bereits eine Erstsichtung stattgefunden hat, dann prüfe, ob man selbst der Prüfende war.
           */
          if($row['firstsightUserId'] == $userId) {
            /**
             * Fehlermeldung, wenn man selbst der Erstsichtende war.
             */
            $content.= "<div class='warnbox'>Du kannst nicht die Erst- und Zweitsichtung machen.</div>".PHP_EOL;
          } else {
            /**
             * Erstsichtung erfolgte von jemand anderem. Prüfe ob die eingetragene Summe mit der übergebenen Summe übereinstimmt.
             */
            if($row['firstsightValue'] != $value) {
              /**
               * Erst- und Zweitsichtung stimmen nicht überein. Post wird zurückgesetzt und die Zweitsichtung wird zur Erstsichtung.
               */
              mysqli_query($dbl, "UPDATE `items` SET `firstsightValue`='".$value."', `firstsightUserId`='".$userId."' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
              mysqli_query($dbl, "INSERT INTO `log` (`userId`, `loglevel`, `postId`, `text`) VALUES ('".$userId."', 3, '".$postId."', '".number_format($value, 2, ",", ".")." € (Erstsichtung: ".number_format($row['firstsightValue'], 2, ",", ".").")')") OR DIE(MYSQLI_ERROR($dbl));
              $content.= "<div class='successbox'>Spendenwert eingetragen.</div>".PHP_EOL;
            } else {
              /**
               * Erst- und Zweitsichtung stimmen überein. Jetzt wird noch geprüft, ob es sich um eine Spende handelt, oder nicht.
               */
              if($value == 0) {
                /**
                 * Es ist kein Spendenpost.
                 */
                mysqli_query($dbl, "UPDATE `items` SET `confirmedValue`='".$value."', `confirmedUserId`='".$userId."', `isDonation`='0' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
                mysqli_query($dbl, "INSERT INTO `log` (`userId`, `loglevel`, `postId`, `text`) VALUES ('".$userId."', 4, '".$postId."', 'kein Spendenpost')") OR DIE(MYSQLI_ERROR($dbl));
                $content.= "<div class='successbox'>Spendenwert eingetragen.</div>".PHP_EOL;
              } else {
                /**
                 * Es ist ein Spendenpost.
                 */
                mysqli_query($dbl, "UPDATE `items` SET `confirmedValue`='".$value."', `confirmedUserId`='".$userId."', `isDonation`='1' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
                mysqli_query($dbl, "INSERT INTO `log` (`userId`, `loglevel`, `postId`, `text`) VALUES ('".$userId."', 4, '".$postId."', '".number_format($value, 2, ",", ".")." €')") OR DIE(MYSQLI_ERROR($dbl));
                $content.= "<div class='successbox'>Spendenwert eingetragen.</div>".PHP_EOL;
              }
            }
          }
        } else {
          /**
           * Wenn alle Felder ausgefüllt waren, dann war jemand anders schneller :o)
           */
        }
      }
    }
  }
}

/**
 * Titel und Überschrift
 */
$title = "Bewertung";
$content.= "<h1>Bewertung</h1>".PHP_EOL;

/**
 * Selektieren eines Posts, der noch nicht bewertet oder noch nicht durch einen selbst bewertet wurde.
 */
$result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `firstsightValue` IS NULL OR (`confirmedValue` IS NULL AND `firstsightUserId` != '".$userId."') ORDER BY RAND() LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_array($result);
  if($row['extension'] != "mp4") {
    /**
     * Bilder werden direkt angezeigt. Falls der Benis kleiner oder gleich 0 ist, wird der Benis Hinweis vergrößert dargestellt.
     * Da Bilder direkt angezeigt werden kann im Formular der Autofocus im Wert-Feld liegen.
     */
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 center'><a href='https://pr0gramm.com/new/".$row['postId']."' target='_blank' rel='noopener'><img src='https://img.pr0gramm.com/".$row['image']."' alt='Bild' class='imgmaxheight'></a><br><span class='info'>Zur Post-Ansicht einfach auf das Bild klicken</span><br><".($row['benis'] <= 0 ? "h1" : "span")." class='warn'>Score: ".$row['benis']."</".($row['benis'] <= 0 ? "h1" : "span")."></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $autofocus = TRUE;
  } else {
    /**
     * Videos nicht direkt anzeigen. Stattdessen den Post verlinken und das Formular nicht automatisch fokussieren.
     */
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 center'><h1 class='highlight'>VIDEO</h1><a href='https://pr0gramm.com/new/".$row['postId']."' target='_blank' rel='noopener'>Video auf pr0gramm ansehen</a><br><".($row['benis'] <= 0 ? "h1" : "span")." class='warn'>Score: ".$row['benis']."</".($row['benis'] <= 0 ? "h1" : "span")."></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $autofocus = FALSE;
  }
  /**
   * Formularanzeige
   */
  $content.= "<form action='/valuation' method='post'>".PHP_EOL;
  /**
   * Post-ID
   */
  $content.= "<input type='hidden' name='postId' value='".$row['postId']."'>".PHP_EOL;
  /**
   * Sitzungstoken
   */
  $content.= "<input type='hidden' name='token' value='".$sessionhash."'>".PHP_EOL;
  /**
   * Geldbetrag
   */
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Geldbetrag</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-10 col-l-10 col-xl-10'><input name='value' type='text' autocomplete='off' placeholder='Siehe Info unten'".($autofocus === TRUE ? " autofocus" : NULL)."></div>".PHP_EOL.
  "</div>".PHP_EOL;
  /**
   * Absenden
   */
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Eintragen</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-10 col-l-10 col-xl-10'><input name='submit' type='submit' value='Eintragen'></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "</form>".PHP_EOL;
  /**
   * Bewertungsinfos
   */
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2 highlight'>Info</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-10 col-l-10 col-xl-10'>Spendenpost: Geldwert eintragen (Komma oder Punkt als Dezimaltrennung ist egal),<br>kein Spendenpost: die Zahl 0 eintragen,<br>unsicher: leer lassen oder F5, dann kommt ein neues Bild.<br>Wenn der Post eine Spende ist, man aber den Wert nicht erkennt 0,01 eintragen!<br>CHF und USD einfach 1:1 eintragen.<br>DKMS siehe <a href='/overview'>hier</a>!</div>".PHP_EOL.
  "</div>".PHP_EOL;
} else {
  /**
   * Alles erledigt.
   */
  $content.= "<div class='infobox'>Alles erledigt. Nächster Crawl alle 5 Minuten.</div>".PHP_EOL;
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
}
?>
