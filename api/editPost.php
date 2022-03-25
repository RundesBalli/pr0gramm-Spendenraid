<?php
/**
 * editPosts.php
 * 
 * API Endpunkt, damit die KI Wert- und Organisations-Eintragungen machen kann.
 */

/**
 * Einbinden der Konfigurationsdatei sowie der Funktionsdatei
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."config.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."functions.php");

/**
 * Ausgabeformat auf JSON setzen.
 */
header("Content-Type: application/json; charset=utf-8");

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

/**
 * Header auslesen und prüfen ob der Auth Header gesetzt ist und korrekt ist.
 */
$headers = getallheaders();

if(empty($headers['apiAuth'])) {
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

if($headers['apiAuth'] != $editPostToken) {
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
 * Prüfen des übergebenen Bodys, ob es gültiges JSON beinhaltet.
 */
if(!$postData = json_decode(file_get_contents('php://input'), TRUE)) {
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
 * Prüfen ob eine PostId übergeben wurde.
 */
if(empty($postData['id']) OR intval($postData['id']) == 0) {
  http_response_code(400);
  die(
    json_encode(
      [
        'error' => 'invalidId',
        'errorMsg' => 'The postId is invalid.'
      ]
    )
  );
}

/**
 * Prüfen ob die übergebene PostId existiert.
 */
$postId = intval(defuse($postData['id']));
$result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  http_response_code(404);
  die(
    json_encode(
      [
        'error' => 'idNotFound',
        'errorMsg' => 'The postId was not found.'
      ]
    )
  );
}

/**
 * Laden des SELECTs in ein Array.
 */
$row = mysqli_fetch_assoc($result);

/**
 * Prüfung des optional übergebbaren Wertes.
 */
if(isset($postData['amount']) AND (is_numeric($postData['amount']) AND $postData['amount'] != "")) {
  /**
   * Umwandlung der value in eine Float Zahl.
   */
  $value = floatval(defuse($postData['amount']));

  /**
   * Da der Post existiert, wird zuerst geprüft, ob schon eine Erstsichtung durchgeführt wurde.
   */
  if($row['firstsightValue'] === NULL OR $row['firstsightUserId'] === NULL) {
    /**
     * Wenn noch keine Erstsichtung durchgeführt wurde, dann leg sie an.
     */
    mysqli_query($dbl, "UPDATE `items` SET `firstsightValue`='".$value."', `firstsightUserId`='".$kiUserId."' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `postId`, `text`) VALUES ('".$kiUserId."', 2, '".$postId."', '".number_format($value, 2, ",", ".")." €')") OR DIE(MYSQLI_ERROR($dbl));
  }
}

/**
 * Prüfung der optional übergebbaren Organisation.
 */
$orga = 0;
if((isset($postData['orga']) AND !empty($postData['orga']))) {
  $orga = intval(defuse($postData['orga']));
  $orgaResult = mysqli_query($dbl, "SELECT `id` FROM `orgas` WHERE `id`=".$orga." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  /**
   * Wenn die Organisation nicht existiert, wird mit einer Fehlermeldung beendet.
   */
  if(mysqli_num_rows($orgaResult) == 0) {
    http_response_code(404);
    die(
      json_encode(
        [
          'error' => 'orgaNotFound',
          'errorMsg' => 'The organization with the provided orgaId could not be found.'
        ]
      )
    );
  } else {
    /**
     * Es wurde eine gültige Organsations-ID übergeben.
     */
    if($row['firstsightOrgaId'] === NULL OR $row['firstsightOrgaUserId'] === NULL) {
      /**
       * Wenn noch keine Erstsichtung durchgeführt wurde, dann leg sie an.
       */
      mysqli_query($dbl, "UPDATE `items` SET `firstsightOrgaId`='".$orga."', `firstsightOrgaUserId`='".$kiUserId."' WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `postId`, `text`) VALUES ('".$kiUserId."', 2, '".$postId."', 'Orga: ".$orga."')") OR DIE(MYSQLI_ERROR($dbl));
    }
  }
}

/**
 * Alles okay, bzw. angefallene Fehlermeldungen innerhalb der Verarbeitung werden zurückgegeben.
 */
http_response_code(200);
die(json_encode(
    [
      'ok' => 'ok'
    ]
  )
);
?>
