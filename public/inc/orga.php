<?php
/**
 * orga.php
 * 
 * Bewertungsseite zum Setzen der Spendenorganisation
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Organisation eintragen, falls das Formular übergeben wurde.
 */
if(isset($_POST['submit'])) {
  $postId = (int)defuse($_POST['postId']);
  /**
   * Prüfung ob eine gültige Zahl eingegeben wurde
   */
  if(is_numeric($_POST['orga'])) {
    $orga = (int)defuse($_POST['orga']);
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
        if($row['firstsightOrgaId'] === NULL OR $row['firstsightOrgaUserId'] === NULL) {
          /**
           * Wenn noch keine Erstsichtung durchgeführt wurde, dann leg sie an.
           */
          mysqli_query($dbl, "UPDATE `items` SET `firstsightOrgaId`='".$orga."', `firstsightOrgaUserId`='".$userId."' WHERE `postId`='".$postId."' LIMIT 1");
          if(mysqli_errno($dbl) == 1452) {
            $content.= "<div class='warnbox'>Die Organisation existiert nicht.</div>".PHP_EOL;
          } elseif(mysqli_errno($dbl) == 0) {
            mysqli_query($dbl, "INSERT INTO `log` (`userId`, `loglevel`, `postId`, `text`) VALUES ('".$userId."', 2, '".$postId."', 'Orga: ".$orga."')") OR DIE(MYSQLI_ERROR($dbl));
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
            if($row['firstsightOrgaId'] != $orga) {
              /**
               * Erst- und Zweitsichtung stimmen nicht überein. Post wird zurückgesetzt und die Zweitsichtung wird zur Erstsichtung.
               */
              mysqli_query($dbl, "UPDATE `items` SET `firstsightOrgaId`='".$orga."', `firstsightOrgaUserId`='".$userId."' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
              mysqli_query($dbl, "INSERT INTO `log` (`userId`, `loglevel`, `postId`, `text`) VALUES ('".$userId."', 3, '".$postId."', 'Orga: ".$orga." (Erstsichtung: ".$row['firstsightOrgaId'].")')") OR DIE(MYSQLI_ERROR($dbl));
              $content.= "<div class='successbox'>Organisation eingetragen.</div>".PHP_EOL;
            } else {
              /**
               * Erst- und Zweitsichtung stimmen überein.
               */
              mysqli_query($dbl, "UPDATE `items` SET `confirmedOrgaId`='".$orga."', `confirmedOrgaUserId`='".$userId."' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
              mysqli_query($dbl, "INSERT INTO `log` (`userId`, `loglevel`, `postId`, `text`) VALUES ('".$userId."', 4, '".$postId."', 'Orga: ".$orga."')") OR DIE(MYSQLI_ERROR($dbl));
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
  }
}

/**
 * Titel und Überschrift
 */
$title = "Organisationen";
$content.= "<h1>Organisationen</h1>".PHP_EOL;

/**
 * Selektieren eines Posts, der noch nicht bewertet oder noch nicht durch einen selbst bewertet wurde.
 */
$result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `isDonation`='1' AND (`firstsightOrgaId` IS NULL OR (`confirmedOrgaId` IS NULL AND `firstsightOrgaUserId`!='".$userId."')) ORDER BY RAND() LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_array($result);
  if($row['firstsightOrgaId'] !== NULL) {
    $content.= "<h3 class='highlight'>Erstsichtung: Orga ".$row['firstsightOrgaId']."</h3>".PHP_EOL;
  }
  if($row['extension'] != "mp4") {
    /**
     * Bilder werden direkt angezeigt. Falls der Benis kleiner oder gleich 0 ist, wird der Benis Hinweis vergrößert dargestellt.
     * Da Bilder direkt angezeigt werden kann im Formular der Autofocus im Wert-Feld liegen.
     */
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 center'><a href='https://pr0gramm.com/new/".$row['postId']."' target='_blank' rel='noopener'><img src='https://img.pr0gramm.com/".$row['image']."' alt='Bild' class='imgmaxheight'></a><br><span class='info'>Zur Post-Ansicht einfach auf das Bild klicken</span><br><".($row['benis'] <= 0 ? "h1" : "span")." class='highlight'>Score: ".$row['benis']."</".($row['benis'] <= 0 ? "h1" : "span><br")."><span class='highlight'>Bestätigter Betrag: ".number_format($row['confirmedValue'], 2, ",", ".")."</span></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $autofocus = TRUE;
  } else {
    /**
     * Videos nicht direkt anzeigen. Stattdessen den Post verlinken und das Formular nicht automatisch fokussieren.
     */
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 center'><h1 class='highlight'>VIDEO</h1><a href='https://pr0gramm.com/new/".$row['postId']."' target='_blank' rel='noopener'>Video auf pr0gramm ansehen</a><br><".($row['benis'] <= 0 ? "h1" : "span")." class='highlight'>Score: ".$row['benis']."</".($row['benis'] <= 0 ? "h1" : "span><br")."><span class='highlight'>Bestätigter Betrag: ".number_format($row['confirmedValue'], 2, ",", ".")."</span></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $autofocus = FALSE;
  }
  /**
   * Formularanzeige
   */
  $content.= "<form action='/orga' method='post'>".PHP_EOL;
  /**
   * Post-ID
   */
  $content.= "<input type='hidden' name='postId' value='".$row['postId']."'>".PHP_EOL;
  /**
   * Sitzungstoken
   */
  $content.= "<input type='hidden' name='token' value='".$sessionhash."'>".PHP_EOL;
  /**
   * Organisationen auslesen und Formularfeld anzeigen
   */
  $result = mysqli_query($dbl, "SELECT `id`, `organame` FROM `orgas` ORDER BY `sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
  while($row = mysqli_fetch_array($result)) {
    $orgas[] = $row['id']." - ".$row['organame'];
  }
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Organisation</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input name='orga' type='text' autocomplete='off' placeholder='siehe Organisationen'".($autofocus === TRUE ? " autofocus" : NULL)."></div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-6 col-l-6 col-xl-6'>".implode("<br>", $orgas)."</div>".PHP_EOL.
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
   * Zurücksetzen
   */
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Zurücksetzen</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-10 col-l-10 col-xl-10'><a href='/resetpost?postId=".$row['postId']."'>Post zurücksetzen</a></div>".PHP_EOL.
  "</div>".PHP_EOL;
} else {
  /**
   * Alles erledigt.
   */
  $content.= "<div class='infobox'>Alles erledigt.</div>".PHP_EOL;
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
}
?>
