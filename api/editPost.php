<?php
/**
 * api/editPost.php
 * 
 * API endpoint to set value and organization data by the AI.
 */

/**
 * Including the configuration and function loader.
 */
define('api', TRUE);
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'loader.php');

/**
 * Set output format to JSON.
 */
header("Content-Type: application/json; charset=utf-8");

/**
 * Check whether the script was called via HTTP-POST method.
 */
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  header('Access-Control-Allow-Methods: POST');
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
 * Check whether a token for transmission and an userId for the AI user is configured.
 */
if(empty($aiSettings['editPostToken']) OR empty($aiSettings['userId'])) {
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
 * Read out the header and check whether the Auth header is set and correct.
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

if($headers['apiAuth'] != $aiSettings['editPostToken']) {
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
 * Check the transferred body to see whether it contains valid JSON.
 */
$postData = json_decode(file_get_contents('php://input'), TRUE);
if(json_last_error() != JSON_ERROR_NONE) {
  http_response_code(400);
  die(
    json_encode(
      [
        'error' => 'wrongFormat',
        'errorMsg' => 'The post data has to be passed in JSON format.'
      ]
    )
  );
}

/**
 * If an empty array was transmitted, then it should be cancelled.
 * Prevents further parsing of {} as postData.
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
 * Check whether a itemId has been transferred.
 */
if(empty($postData['id']) OR intval($postData['id']) == 0) {
  http_response_code(400);
  die(
    json_encode(
      [
        'error' => 'invalidId',
        'errorMsg' => 'The id is invalid.'
      ]
    )
  );
}

/**
 * Check whether the transmitted itemId exists.
 */
$itemId = intval(defuse($postData['id']));
$result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `itemId`='".$itemId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  http_response_code(404);
  die(
    json_encode(
      [
        'error' => 'idNotFound',
        'errorMsg' => 'The id was not found.'
      ]
    )
  );
}
$row = mysqli_fetch_assoc($result);

/**
 * Checking the optionally transferable value.
 */
if(isset($postData['amount']) AND (is_numeric($postData['amount']) AND $postData['amount'] != '')) {
  /**
   * Conversion of the value into a float number.
   */
  $value = floatval(defuse($postData['amount']));

  /**
   * As the post exists, the system first checks whether an initial inspection has already been made.
   */
  if($row['firstsightValue'] === NULL OR $row['firstsightUserId'] === NULL) {
    /**
     * Inserting the data when the first sight has not yet been made.
     */
    mysqli_query($dbl, "UPDATE `items` SET `firstsightValue`='".$value."', `firstsightUserId`='".$aiSettings['userId']."' WHERE `itemId`='".$itemId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `itemId`, `text`) VALUES ('".$aiSettings['userId']."', 2, '".$itemId."', '".number_format($value, 2, ",", ".")." €')") OR DIE(MYSQLI_ERROR($dbl));
  }
}

/**
 * Checking the optionally transferable organization.
 */
$orga = 0;
if((isset($postData['orga']) AND !empty($postData['orga']))) {
  $orga = intval(defuse($postData['orga']));
  $orgaResult = mysqli_query($dbl, "SELECT `id` FROM `metaOrganizations` WHERE `id`=".$orga." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  /**
   * If the organization does not exist, the process ends with an error message.
   */
  if(mysqli_num_rows($orgaResult) == 0) {
    http_response_code(404);
    die(
      json_encode(
        [
          'error' => 'organizationNotFound',
          'errorMsg' => 'The organization with the provided id could not be found.'
        ]
      )
    );
  } else {
    /**
     * A valid organization ID has been transmitted.
     */
    if($row['firstsightOrgaId'] === NULL OR $row['firstsightOrgaUserId'] === NULL) {
      /**
       * Inserting the data when the first sight has not yet been made.
       */
      mysqli_query($dbl, "UPDATE `items` SET `firstsightOrgaId`='".$orga."', `firstsightOrgaUserId`='".$aiSettings['userId']."' WHERE `itemId`='".$itemId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `itemId`, `text`) VALUES ('".$aiSettings['userId']."', 2, '".$itemId."', 'Orga: ".$orga."')") OR DIE(MYSQLI_ERROR($dbl));
    }
  }
}

/**
 * Everyting is okay.
 */
http_response_code(200);
die(json_encode(
    [
      'ok' => 'ok'
    ]
  )
);
?>
