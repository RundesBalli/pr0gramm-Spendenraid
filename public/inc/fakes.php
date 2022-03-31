<?php
/**
 * fakes.php
 * 
 * Seite zum Verwalten von Fake-Vermutungen
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "Fälschungen";
$content.= "<h1>Fälschungen</h1>";

/**
 * Sicher/Unsicher-Status umstellen
 */
if(isset($_GET['setCertain']) AND !empty($_GET['id'])) {
  $fakeId = (int)defuse($_GET['id']);
  mysqli_query($dbl, "UPDATE `fakes` SET `certain`='1' WHERE `id`='".$fakeId."' LIMIT 1") OR DIE(MYSQLI_ERROR());
  mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `text`) VALUES ('".$userId."', 7, 'Fake als sicher eingestuft (ID: ".$fakeId.")')") OR DIE(MYSQLI_ERROR($dbl));
  $content.= "<div class='successbox'>Fake-Eintrag als sicher markiert.</div>";
}
if(isset($_GET['setUncertain']) AND !empty($_GET['id'])) {
  $fakeId = (int)defuse($_GET['id']);
  mysqli_query($dbl, "UPDATE `fakes` SET `certain`='0' WHERE `id`='".$fakeId."' LIMIT 1") OR DIE(MYSQLI_ERROR());
  mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `text`) VALUES ('".$userId."', 7, 'Fake als unsicher eingestuft (ID: ".$fakeId.")')") OR DIE(MYSQLI_ERROR($dbl));
  $content.= "<div class='successbox'>Fake-Eintrag als unsicher markiert.</div>";
}

/**
 * Formularauswertung
 */
if(isset($_POST['submit']) AND (!empty($_POST['original']) AND !empty($_POST['fake']))) {
  /**
   * Matchen der Post-IDs
   */
  $error = 0;
  if(preg_match('/(?:(?:http(?:s?):\/\/pr0gramm\.com)?\/(?:top|new|user\/\w+\/(?:uploads|likes)|stalk)(?:(?:\/\w+)?)\/)?([1-9]\d*)(?:(?::comment(?:\d+))?)?/i', defuse($_POST['original']), $match) === 1) {
    $postIdOriginal = (int)$match[1];
  } else {
    $content.= "<div class='warnbox'>Originalpost ungültig.</div>";
    $error = 1;
  }
  if(preg_match('/(?:(?:http(?:s?):\/\/pr0gramm\.com)?\/(?:top|new|user\/\w+\/(?:uploads|likes)|stalk)(?:(?:\/\w+)?)\/)?([1-9]\d*)(?:(?::comment(?:\d+))?)?/i', defuse($_POST['fake']), $match) === 1) {
    $postIdFake = (int)$match[1];
  } else {
    $content.= "<div class='warnbox'>Fakepost ungültig.</div>";
    $error = 1;
  }
  /**
   * Prüfen ob die Post-IDs gültige Zahlen sind und ob sie ungleich miteinander sind.
   */
  if($error == 0 AND (($postIdOriginal != $postIdFake) AND (is_int($postIdOriginal) AND is_int($postIdFake)))) {
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
       * Token gültig, kann eingetragen werden.
       */
      mysqli_query($dbl, "INSERT INTO `fakes` (`postIdOriginal`, `postIdFake`, `userId`, `certain`) VALUES ('".($postIdOriginal < $postIdFake ? $postIdOriginal : $postIdFake)."', '".($postIdFake > $postIdOriginal ? $postIdFake : $postIdOriginal)."', '".$userId."', '".(isset($_POST['certain']) AND $_POST['certain'] == 1 ? 1 : 0)."')");
      if(mysqli_errno($dbl) == 1452) {
        $content.= "<div class='warnbox'>Der/die Post(s) existiert/en nicht.</div>";
      } elseif(mysqli_errno($dbl) == 1062) {
        $content.= "<div class='warnbox'>Dieser Fakeeintrag existiert bereits.</div>";
      } elseif(mysqli_errno($dbl) == 0) {
        mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `text`) VALUES ('".$userId."', 7, 'Fake eingetragen - Orig: ".($postIdOriginal < $postIdFake ? $postIdOriginal : $postIdFake).", Fake: ".($postIdFake > $postIdOriginal ? $postIdFake : $postIdOriginal)."')") OR DIE(MYSQLI_ERROR($dbl));
        $content.= "<div class='successbox'>Fake eingetragen.</div>";
      } else {
        die(MYSQLI_ERROR($dbl));
      }
    }
  } else {
    $content.= "<div class='warnbox'>Post-IDs ungültig.</div>";
  }
}

/**
 * Untermenü
 */
$content.= "<h3>Menü</h3>";
$content.= "<div class='row'>".
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/fakeposts'>alle ohne 1, 2 & 9</a></div>".
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/fakepostsdkms'>alle DKMS</a></div>".
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/fakepostskrebshilfe'>alle dt. Krebshilfe</a></div>".
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/fakepostsgt'>alle guten Taten</a></div>".
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/fakepostsvalue'>alle sortiert nach gleichem Wert/Orga</a></div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

/**
 * Eintragen
 */
$content.= "<h3>Eintragen</h3>";
/**
 * Formularanzeige
 */
$content.= "<form action='/fakes' method='post'>";
/**
 * Sitzungstoken
 */
$content.= "<input type='hidden' name='token' value='".$sessionhash."'>";
/**
 * Geldbetrag
 */
$content.= "<div class='row'>".
"<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Posts</div>".
"<div class='col-x-12 col-s-12 col-m-5 col-l-5 col-xl-5'><input name='original' type='text' autocomplete='off' placeholder='Originalpost'></div>".
"<div class='col-x-12 col-s-12 col-m-5 col-l-5 col-xl-5'><input name='fake' type='text' autocomplete='off' placeholder='Fälschung'></div>".
"</div>";
/**
 * Sicher?
 */
$content.= "<div class='row'>".
"<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Sicher?</div>".
"<div class='col-x-12 col-s-12 col-m-5 col-l-5 col-xl-5'><input name='certain' type='radio' autocomplete='off' value='1' id='certain-1'><label for='certain-1'> Ja</label> - <input name='certain' type='radio' autocomplete='off' value='0' id='certain-0' checked><label for='certain-0'> Nein</label></div>".
"</div>";
/**
 * Absenden
 */
$content.= "<div class='row'>".
"<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Eintragen</div>".
"<div class='col-x-12 col-s-12 col-m-10 col-l-10 col-xl-10'><input name='submit' type='submit' value='Eintragen'></div>".
"</div>";
$content.= "</form>";
$content.= "<div class='spacer-m'></div>";

/**
 * Anzeige der vorhandenen Fakes
 */
$content.= "<h3>Fälschungen</h3>";
$result = mysqli_query($dbl, "SELECT `fakes`.*, `fakes`.`id` AS `fakeId`, `users`.`username` FROM `fakes` LEFT OUTER JOIN `users` ON `users`.`id`=`fakes`.`userId` ORDER BY `fakeId` ASC") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  $content.= "<div class='infobox'>Es sind keine Einträge vorhanden.</div>";
} else {
  /**
   * Tabellenüberschrift
   */
  $content.= "<div class='row highlight bold'>".
  "<div class='col-x-2 col-s-2 col-m-1 col-l-1 col-xl-1'>ID</div>".
  "<div class='col-x-5 col-s-5 col-m-2 col-l-2 col-xl-2'>Original</div>".
  "<div class='col-x-5 col-s-5 col-m-2 col-l-2 col-xl-2'>Fälschung</div>".
  "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Zeitpunkt</div>".
  "<div class='col-x-6 col-s-6 col-m-2 col-l-2 col-xl-2'>User</div>".
  "<div class='col-x-6 col-s-6 col-m-2 col-l-2 col-xl-2'>Aktionen</div>".
  "</div>";
  while($row = mysqli_fetch_array($result)) {
    $content.= "<div class='row hover'>".
    "<div class='col-x-2 col-s-2 col-m-1 col-l-1 col-xl-1'>".$row['fakeId']."</div>".
    "<div class='col-x-5 col-s-5 col-m-2 col-l-2 col-xl-2'><a href='https://pr0gramm.com/new/".$row['postIdOriginal']."' target='_blank' rel='noopener'>".$row['postIdOriginal']."</a></div>".
    "<div class='col-x-5 col-s-5 col-m-2 col-l-2 col-xl-2'><a href='https://pr0gramm.com/new/".$row['postIdFake']."' target='_blank' rel='noopener'>".$row['postIdFake']."</a> ".($row['certain'] == 1 ? "(sicher)" : "(unsicher)")."</div>".
    "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>".date("d.m.Y, H:i:s", strtotime($row['ts']))."</div>".
    "<div class='col-x-6 col-s-6 col-m-2 col-l-2 col-xl-2'>".($row['username'] === NULL ? "<span class='italic'>NULL</span>" : ($row['username'] == $username ? "<span class='highlight'>".$row['username']."</span>" : $row['username']))."</div>".
    "<div class='col-x-6 col-s-6 col-m-2 col-l-2 col-xl-2'><a href='/delfake?id=".$row['fakeId']."'>Löschen</a><br><a href='/fakes?set".($row['certain'] == 1 ? "Uncertain" : "Certain")."&id=".$row['fakeId']."'>".($row['certain'] == 1 ? "Unsicher" : "Sicher")."</a></div>".
    "</div>";
  }
}
$content.= "<div class='spacer-m'></div>";
?>
