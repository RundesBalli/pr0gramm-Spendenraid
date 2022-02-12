<?php
/**
 * editPosts.php
 * 
 * API Endpunkt, damit die KI Ersteintragungen machen kann.
 */

/**
 * Einbinden der Konfigurationsdatei sowie der Funktionsdatei
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."config.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."functions.php");

/**
 * Ausgabeformat auf JSON setzen.
 */
header("Content-type: application/json; charset=utf-8");

/**
 * Prüfen ob das Script per HTTP-POST Methode aufgerufen wurde.
 */
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  header("Access-Control-Allow-Methods: POST");
  die(
    json_encode(
      [
        'error' => 'HTTPMethod',
        'errorMsg' => 'This endpoint must be called via HTTP POST.'
      ]
    )
  );
}

/**
 * Prüfen ob ein Token zur Übermittlung an diese API konfiguriert ist und wenn ja, ob es übergeben wurde und korrekt ist.
 */
if(empty($editPostToken) OR empty($kiUserId)) {
  http_response_code(501);
  die(
    json_encode(
      [
        'error' => 'apiNotConfigured',
        'errorMsg' => 'This API was not properly configured.'
      ]
    )
  );
}

if(empty($_POST['token'])) {
  http_response_code(400);
  die(
    json_encode(
      [
        'error' => 'tokenNotProvided',
        'errorMsg' => 'You have to provide a token for your operation.'
      ]
    )
  );
}

if($_POST['token'] != $editPostToken) {
  http_response_code(401);
  die(
    json_encode(
      [
        'error' => 'wrongToken',
        'errorMsg' => 'The token you provided is wrong.'
      ]
    )
  );
}
/**
 * Das übergebene Token ist korrekt.
 */

/**
 * Prüfung ob das postData Feld übergeben wurde.
 */
if(empty($_POST['postData'])) {
  http_response_code(400);
  die(
    json_encode(
      [
        'error' => 'missingPostData',
        'errorMsg' => 'You have to provide the postData parameter with data that should be changed.'
      ]
    )
  );
}

/**
 * Prüfen des übergebenen Feldes, ob es gültiges JSON beinhaltet.
 */
if(!$postData = json_decode($_POST['postData'])) {
  http_response_code(400);
  die(
    json_encode(
      [
        'error' => 'wrongFormat',
        'errorMsg' => 'The postData field has to be passed in JSON format.'
      ]
    )
  );
}

/**
 * Wenn ein leeres Array übergeben wurde, dann soll abgebrochen werden.
 * Verhindert weiteres parsen von {} als postData.
 */
if(empty($postData)) {
  http_response_code(400);
  die(
    json_encode(
      [
        'error' => 'emptyPostData',
        'errorMsg' => 'The postData field has to contain at least one entry.'
      ]
    )
  );
}

/**
 * Initialisieren des errorData Arrays für die Ausgabe.
 */
$errorData = array();

/**
 * Iterieren der PostIds und setzen der Werte in der Datenbank.
 * Beispielwerte der postData: {"123456":{"value":1337.00,"orga":2},"123457":{"value":13.37,"orga":1}}
 */

foreach($postData AS $key => $values) {
  $postId = intval(defuse($key));
  $result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));

  /**
   * Wenn der Post nicht existiert, wird mit einer Fehlermeldung die Iteration fortgesetzt.
   */
  if(mysqli_num_rows($result) == 0) {
    $errorData[$key] = [
      'error' => 'postNotFound',
      'errorMsg' => "The post with the provided postId could not be found."
    ];
    continue;
  }

  /**
   * Laden des SELECTs in ein Array.
   */
  $row = mysqli_fetch_assoc($result);

  /**
   * Wenn das value Feld leer ist, dann wird der Post mit einer Fehlermeldung ignoriert.
   * Hier kann ausdrücklich nicht die Funktion empty verwendet werden, da auch der Wert "0" gültig sein muss.
   */
  if(!isset($value['value']) OR $value['value'] == "") {
    $errorData[$postId] = [
      'error' => 'emptyValue',
      'errorMsg' => "The value field is empty."
    ];
    continue;
  }

  /**
   * Prüfung ob der Wert numerisch ist.
   */
  if(!is_numeric($value['value'])) {
    $errorData[$postId] = [
      'error' => 'notNumericValue',
      'errorMsg' => "The provided value is not numeric."
    ];
  }

  /**
   * Umwandlung der value in eine Float Zahl.
   */
  $value = floatval(defuse($value['value']));

  /**
   * Prüfung der optional übergebbaren Organisation.
   */
  $orga = 0;
  if(isset($value['orga']) AND !empty($value['orga'])) {
    $orga = intval(defuse($value['orga']));
    $orgaresult = mysqli_query($dbl, "SELECT `id` FROM `orgas` WHERE `id`=".$orga." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));

    /**
     * Wenn die Organisation nicht existiert, wird mit einer Fehlermeldung die Iteration fortgesetzt.
     */
    if(mysqli_num_rows($result) == 0) {
      $errorData[$postId] = [
        'error' => 'orgaNotFound',
        'errorMsg' => "The organization with the provided orgaId could not be found."
      ];
      continue;
    }
  }

  /**
   * Da der Post existiert, wird zuerst geprüft, ob schon eine Erstsichtung durchgeführt wurde.
   */
  if($row['firstsightValue'] === NULL OR $row['firstsightUserId'] === NULL) {
    /**
     * Wenn noch keine Erstsichtung durchgeführt wurde, dann leg sie an.
     */
    mysqli_query($dbl, "UPDATE `items` SET `firstsightValue`='".$value."', `firstsightUserId`='".$kiUserId."' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `postId`, `text`) VALUES ('".$kiUserId."', 2, '".$postId."', '".number_format($value, 2, ",", ".")." €')") OR DIE(MYSQLI_ERROR($dbl));
  } elseif($row['confirmedValue'] === NULL OR $row['confirmedUserId'] === NULL) {
    /**
     * Wenn bereits eine Erstsichtung stattgefunden hat, dann prüfe, ob man selbst der Prüfende war.
     * Kann nur eintreten, wenn die API mehrmals mit den selben Daten aufgerufen wird.
     */
    if($row['firstsightUserId'] == $kiUserId) {
      /**
       * Fehlermeldung, da die KI der Erstsichtende war.
       */
      $errorData[$postId] = [
        'error' => 'firstSightSameUser',
        'errorMsg' => "The first sight has already been done by this user."
      ];
      continue;
    } else {
      /**
       * Erstsichtung erfolgte von jemand anderem. Prüfe ob die eingetragene Summe mit der übergebenen Summe übereinstimmt.
       */
      if($row['firstsightValue'] != $value) {
        /**
         * Erst- und Zweitsichtung stimmen nicht überein. Post wird zurückgesetzt und die Zweitsichtung wird zur Erstsichtung.
         */
        mysqli_query($dbl, "UPDATE `items` SET `firstsightValue`='".$value."', `firstsightUserId`='".$kiUserId."' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `postId`, `text`) VALUES ('".$kiUserId."', 3, '".$postId."', '".number_format($value, 2, ",", ".")." € (Erstsichtung: ".number_format($row['firstsightValue'], 2, ",", ".").")')") OR DIE(MYSQLI_ERROR($dbl));
      } else {
        /**
         * Erst- und Zweitsichtung stimmen überein. Jetzt wird noch geprüft, ob es sich um eine Spende handelt, oder nicht.
         */
        if($value == 0) {
          /**
           * Es ist kein Spendenpost.
           */
          mysqli_query($dbl, "UPDATE `items` SET `confirmedValue`='".$value."', `confirmedUserId`='".$kiUserId."', `isDonation`='0', `firstsightOrgaId`=NULL, `firstsightOrgaUserId`=NULL, `confirmedOrgaId`=NULL, `confirmedOrgaUserId`=NULL WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `postId`, `text`) VALUES ('".$kiUserId."', 4, '".$postId."', 'kein Spendenpost')") OR DIE(MYSQLI_ERROR($dbl));
        } else {
          /**
           * Es ist ein Spendenpost.
           */
          mysqli_query($dbl, "UPDATE `items` SET `confirmedValue`='".$value."', `confirmedUserId`='".$kiUserId."', `isDonation`='1' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `postId`, `text`) VALUES ('".$kiUserId."', 4, '".$postId."', '".number_format($value, 2, ",", ".")." €')") OR DIE(MYSQLI_ERROR($dbl));
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
            }
          }
        }
      }
    }
  }

  /**
   * Wenn der Post nur mit einem Wert übergeben wurde, dann wird jetzt die nächste Iteration angestoßen.
   */
  if($orga == 0) {
    continue;
  }

  /**
   * Es wurde eine gültige Organsations-ID übergeben.
   */
  if($row['firstsightOrgaId'] === NULL OR $row['firstsightOrgaUserId'] === NULL) {
    /**
     * Wenn noch keine Erstsichtung durchgeführt wurde, dann leg sie an.
     */
    mysqli_query($dbl, "UPDATE `items` SET `firstsightOrgaId`='".$orga."', `firstsightOrgaUserId`='".$kiUserId."' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `postId`, `text`) VALUES ('".$kiUserId."', 2, '".$postId."', 'Orga: ".$orga."')") OR DIE(MYSQLI_ERROR($dbl));
  } elseif($row['confirmedOrgaId'] === NULL OR $row['confirmedOrgaUserId'] === NULL) {
    /**
     * Wenn bereits eine Erstsichtung stattgefunden hat, dann prüfe, ob man selbst der Prüfende war.
     * Kann nur eintreten, wenn die API mehrmals mit den selben Daten aufgerufen wird.
     */
    if($row['firstsightOrgaUserId'] == $kiUserId) {
      /**
       * Fehlermeldung, da die KI der Erstsichtende war.
       */
      $errorData[$postId] = [
        'error' => 'orgaFirstSightSameUser',
        'errorMsg' => "The first sight of the organization has already been done by this user."
      ];
      continue;
    } else {
      /**
       * Erstsichtung erfolgte von jemand anderem. Prüfe ob die eingetragene Organisation mit der übergebenen Organisation übereinstimmt.
       */
      if($row['firstsightOrgaId'] != $orga) {
        /**
         * Erst- und Zweitsichtung stimmen nicht überein. Post wird zurückgesetzt und die Zweitsichtung wird zur Erstsichtung.
         */
        mysqli_query($dbl, "UPDATE `items` SET `firstsightOrgaId`='".$orga."', `firstsightOrgaUserId`='".$kiUserId."' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `postId`, `text`) VALUES ('".$kiUserId."', 3, '".$postId."', 'Orga: ".$orga." (Erstsichtung: ".$row['firstsightOrgaId'].")')") OR DIE(MYSQLI_ERROR($dbl));
      } else {
        /**
         * Erst- und Zweitsichtung stimmen überein.
         */
        mysqli_query($dbl, "UPDATE `items` SET `confirmedOrgaId`='".$orga."', `confirmedOrgaUserId`='".$kiUserId."' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `postId`, `text`) VALUES ('".$kiUserId."', 4, '".$postId."', 'Orga: ".$orga."')") OR DIE(MYSQLI_ERROR($dbl));
      }
    }
  }
}

/**
 * Alles okay, bzw. angefallene Fehlermeldungen innerhalb der Verarbeitung werden zurückgegeben.
 */
http_response_code(200);
die(json_encode($errorData));
?>
