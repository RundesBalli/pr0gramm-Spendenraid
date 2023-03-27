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
if(isset($_POST['value'])) {
  $postId = (int)defuse($_POST['postId']);
  /**
   * Prüfung ob eine gültige Zahl eingegeben wurde
   * 
   * Hinweis: Hier kann nicht empty() benutzt werden, da "0" true zurückgeben würde.
   */
  if($_POST['value'] != "") {
    $value = (double)str_replace(",", ".", defuse($_POST['value']));
    if(is_numeric($value)) {
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
         * Token gültig. Selektion des Posts.
         */
        $result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) == 0) {
          /**
           * Wenn der Post nicht existiert, beende mit einer Fehlermeldung.
           */
          $content.= "<div class='infobox'>Der Post existiert nicht (mehr).</div>";
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
            mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `postId`, `text`) VALUES ('".$userId."', 2, '".$postId."', '".number_format($value, 2, ",", ".")." €')") OR DIE(MYSQLI_ERROR($dbl));
            $content.= "<div class='successbox'>Spendenwert eingetragen.<br><a href='/resetpost?postId=".$postId."'>Post zurücksetzen</a></div>";
          } elseif($row['confirmedValue'] === NULL OR $row['confirmedUserId'] === NULL) {
            /**
             * Wenn bereits eine Erstsichtung stattgefunden hat, dann prüfe, ob man selbst der Prüfende war.
             */
            if($row['firstsightUserId'] == $userId) {
              /**
               * Fehlermeldung, wenn man selbst der Erstsichtende war.
               */
              $content.= "<div class='warnbox'>Du kannst nicht die Erst- und Zweitsichtung machen.</div>";
            } else {
              /**
               * Erstsichtung erfolgte von jemand anderem. Prüfe ob die eingetragene Summe mit der übergebenen Summe übereinstimmt.
               */
              if($row['firstsightValue'] != $value) {
                /**
                 * Erst- und Zweitsichtung stimmen nicht überein. Post wird zurückgesetzt und die Zweitsichtung wird zur Erstsichtung.
                 */
                mysqli_query($dbl, "UPDATE `items` SET `firstsightValue`='".$value."', `firstsightUserId`='".$userId."' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
                mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `postId`, `text`) VALUES ('".$userId."', 3, '".$postId."', '".number_format($value, 2, ",", ".")." € (Erstsichtung: ".number_format($row['firstsightValue'], 2, ",", ".").")')") OR DIE(MYSQLI_ERROR($dbl));
                $content.= "<div class='successbox'>Spendenwert eingetragen.<br><a href='/resetpost?postId=".$postId."'>Post zurücksetzen</a></div>";
              } else {
                /**
                 * Erst- und Zweitsichtung stimmen überein. Jetzt wird noch geprüft, ob es sich um eine Spende handelt, oder nicht.
                 */
                if($value == 0) {
                  /**
                   * Es ist kein Spendenpost.
                   */
                  mysqli_query($dbl, "UPDATE `items` SET `confirmedValue`='".$value."', `confirmedUserId`='".$userId."', `isDonation`='0', `firstsightOrgaId`=NULL, `firstsightOrgaUserId`=NULL, `confirmedOrgaId`=NULL, `confirmedOrgaUserId`=NULL WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
                  mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `postId`, `text`) VALUES ('".$userId."', 4, '".$postId."', 'kein Spendenpost')") OR DIE(MYSQLI_ERROR($dbl));
                  $content.= "<div class='successbox'>Spendenwert eingetragen.<br><a href='/resetpost?postId=".$postId."'>Post zurücksetzen</a></div>";
                } else {
                  /**
                   * Es ist ein Spendenpost.
                   */
                  mysqli_query($dbl, "UPDATE `items` SET `confirmedValue`='".$value."', `confirmedUserId`='".$userId."', `isDonation`='1' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
                  mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `postId`, `text`) VALUES ('".$userId."', 4, '".$postId."', '".number_format($value, 2, ",", ".")." €')") OR DIE(MYSQLI_ERROR($dbl));
                  $content.= "<div class='successbox'>Spendenwert eingetragen.<br><a href='/resetpost?postId=".$postId."'>Post zurücksetzen</a></div>";
                  /**
                   * Nutzer für das Perk auf pr0gramm freischalten.
                   */
                  if(!empty($perkSecret)) {
                    require_once($apiCall);
                    $response = apiCall("https://pr0gramm.com/api/casino/unlockUser", array("secret" => $perkSecret, "name" => $row['username']));
                    if($response['success'] == TRUE) {
                      /**
                       * Bei Erfolg wird ein Logeintrag erzeugt.
                       */
                      mysqli_query($dbl, "INSERT INTO `log` (`logLevel`, `postId`, `text`) VALUES (6, '".$postId."', 'User ".$row['username']." freigeschaltet')") OR DIE(MYSQLI_ERROR($dbl));
                    } else {
                      /**
                       * Wenn die Freischaltung nicht geklappt hat, wird der Post zurückgesetzt.
                       */
                      mysqli_query($dbl, "UPDATE `items` SET `firstsightValue`=NULL, `firstsightUserId`=NULL, `confirmedValue`=NULL, `confirmedUserId`=NULL, `isDonation`=NULL, `firstsightOrgaId`=NULL, `firstsightOrgaUserId`=NULL, `confirmedOrgaId`=NULL, `confirmedOrgaUserId`=NULL WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
                      mysqli_query($dbl, "INSERT INTO `log` (`logLevel`, `postId`, `text`) VALUES (5, '".$postId."', 'zurückgesetzt, da Perkfreischaltung fehlschlug')") OR DIE(MYSQLI_ERROR($dbl));
                      $content.= "<div class='warnbox'>Post zurückgesetzt, da Perkfreischaltung fehlschlug.</div>";
                    }
                  }
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
}

/**
 * Titel und Überschrift
 */
$title = "Bewertung";
$content.= "<h1>Bewertung</h1>";

/**
 * Selektieren eines Posts, der noch nicht bewertet oder noch nicht durch einen selbst bewertet wurde.
 */
$result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `firstsightValue` IS NULL OR (`confirmedValue` IS NULL AND `firstsightUserId` != '".$userId."') ORDER BY RAND() LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_array($result);
  if($row['firstsightValue'] !== NULL) {
    $content.= "<h3 class='highlight'>".((!empty($kiUserId) AND $row['firstsightUserId'] == $kiUserId) ? "KI-" : NULL)."Erstsichtung: ".number_format($row['firstsightValue'], 2, ",", ".")." €</h3>";
  }
  if($row['extension'] != "mp4") {
    /**
     * Bilder werden direkt angezeigt. Falls der Benis kleiner oder gleich 0 ist, wird der Benis Hinweis vergrößert dargestellt.
     * Da Bilder direkt angezeigt werden kann im Formular der Autofocus im Wert-Feld liegen.
     */
    $content.= "<div class='row'>".
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 center'><a " . (($row['flags'] == 2 || $row['flags'] == 4) ? "class='nsfw-blurred'" : "") . " href='https://pr0gramm.com/new/".$row['postId']."' target='_blank' rel='noopener'><img src='https://img.pr0gramm.com/".$row['image']."' alt='Bild' class='imgmaxheight'></a><br><span class='info'>Zur Post-Ansicht einfach auf das Bild klicken</span><br><".($row['benis'] <= 0 ? "h1" : "span")." class='warn'>Score: ".$row['benis']."</".($row['benis'] <= 0 ? "h1" : "span")."></div>".
    "</div>";
  } else {
    /**
     * Videos nicht direkt anzeigen. Stattdessen den Post verlinken und das Formular nicht automatisch fokussieren.
     */
    $content.= "<div class='row'>".
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 center'><h1 class='highlight'>VIDEO</h1><a href='https://pr0gramm.com/new/".$row['postId']."' target='_blank' rel='noopener'>Video auf pr0gramm ansehen</a><br><".($row['benis'] <= 0 ? "h1" : "span")." class='warn'>Score: ".$row['benis']."</".($row['benis'] <= 0 ? "h1" : "span")."></div>".
    "</div>";
  }
  /**
   * Formularanzeige
   */
  $content.= "<form action='/valuation' id='valuation-form' method='post'>";
  /**
   * Post-ID
   */
  $content.= "<input type='hidden' name='postId' value='".$row['postId']."'>";
  /**
   * Sitzungstoken
   */
  $content.= "<input type='hidden' name='token' value='".$sessionhash."'>";
  /**
   * NSFW Blur
   */
  if ($row['flags'] == 2 || $row['flags'] == 4){
    $content.= "<div class='row'>".
    "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>NSFW-Blur</div>".
    "<div class='col-x-12 col-s-12 col-m-10 col-l-10 col-xl-10'><input id='nsfw-blur-cb' type='checkbox' checked></div>".
    "</div>";
  }
  /**
   * Geldbetrag
   */
  $content.= "<div class='row'>".
  "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Geldbetrag</div>".
  "<div class='col-x-12 col-s-12 col-m-10 col-l-10 col-xl-10'><input id='value-input' name='value' type='text' autocomplete='off' placeholder='Siehe Info unten' autofocus></div>".
  "</div>";
  /**
   * Mobile Schnellbewertung (sichtbar ab <= 600px)
   */
  $content.= "<div class='row mobile-only'>".
  "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Schnellbewertung</div>".
  "<div class='col-x-12 col-s-12 col-m-10 col-l-10 col-xl-10'><a href='#' class='msb-btn'>0</a><a href='#' class='msb-btn'>0.01</a><a href='#' class='msb-btn'>5</a><a href='#' class='msb-btn'>10</a><a href='#' class='msb-btn'>15</a><a href='#' class='msb-btn'>20</a><a href='#' class='msb-btn'>25</a><a href='#' class='msb-btn'>30</a><a href='#' class='msb-btn'>35</a><a href='#' class='msb-btn'>40</a><a href='#' class='msb-btn'>50</a><a href='#' class='msb-btn'>100</a></div>".
  "</div>";
  /**
   * Absenden
   */
  $content.= "<div class='row'>".
  "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Eintragen</div>".
  "<div class='col-x-12 col-s-12 col-m-10 col-l-10 col-xl-10'><input id='value-submit' name='value-submit' type='submit' value='Eintragen'></div>".
  "</div>";
  $content.= "</form>";
  /**
   * PostInfo
   */
  $content.= "<div class='row'>".
  "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Links</div>".
  "<div class='col-x-12 col-s-12 col-m-10 col-l-10 col-xl-10'><a href='/postinfo?postId=".$row['postId']."'>PostInfo</a></div>".
  "</div>";
  $content.= "</form>";
  /**
   * Bewertungsinfos
   */
  $content.= "<div class='row'>".
  "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2 highlight'>Info</div>".
  "<div class='col-x-12 col-s-12 col-m-10 col-l-10 col-xl-10'>Spendenpost: Geldwert eintragen (Komma oder Punkt als Dezimaltrennung ist egal),<br>kein Spendenpost: die Zahl 0 eintragen,<br>unsicher: leer lassen oder F5, dann kommt ein neues Bild.<br>Wenn der Post eine Spende ist, man aber den Wert nicht erkennt 0,01 eintragen!<br>CHF und USD einfach 1:1 eintragen.<br>DKMS siehe <a href='/overview'>hier</a>!<br>Gute Tat = 0,01</div>".
  "</div>";
} else {
  /**
   * Alles erledigt.
   */
  $content.= "<div class='infobox'>Alles erledigt. Nächster Crawl alle 5 Minuten.<br><a href='/orga'>Organisationen bewerten</a></div>";
  $content.= "<div class='spacer-m'></div>";
}
?>
