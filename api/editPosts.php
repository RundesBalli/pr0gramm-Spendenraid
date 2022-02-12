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
 * Iterieren der PostIds und setzen der Werte in der Datenbank.
 * Beispielwerte der postData: {"123456":{"value":1337.00,"orga":2},"123457":{"value":13.37,"orga":1}}
 */
$errorData = array();

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
    $errorData[$key] = [
      'error' => 'emptyValue',
      'errorMsg' => "The value field is empty."
    ];
    continue;
  }

  /**
   * Prüfung ob der Wert numerisch ist.
   */
  if(!is_numeric($value['value'])) {
    $errorData[$key] = [
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
      $errorData[$key] = [
        'error' => 'orgaNotFound',
        'errorMsg' => "The organization with the provided orgaId could not be found."
      ];
      continue;
    }
  }

  /**
   * Eintragen
   */

  
}