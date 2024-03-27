<?php
/**
 * api/getJPG.php
 * 
 * API endpoint to obtain a JPG version of the images on pr0gramm.
 */

/**
 * Including the configuration and function loader.
 */
define('api', TRUE);
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'loader.php');

/**
 * Can not set output format to JSON here, because if everything is ok, the api endpoint will return an image.
 */
#header("Content-Type: application/json; charset=utf-8");

/**
 * Check whether the script was called via HTTP-GET method.
 */
if($_SERVER['REQUEST_METHOD'] !== 'GET') {
  header("Content-Type: application/json; charset=utf-8");
  http_response_code(405);
  header('Access-Control-Allow-Methods: GET');
  die(
    json_encode(
      [
        'error' => 'HTTPMethod',
        'errorMsg' => 'This endpoint must be called via HTTP GET.'
      ]
    )
  );
}

/**
 * Check whether a token for transmission and an userId for the AI user is configured.
 */
if(empty($aiSettings['editPostToken']) OR empty($aiSettings['userId'])) {
  header("Content-Type: application/json; charset=utf-8");
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
 * Read out if the apiAuth is set and correct.
 */
if(empty($_GET['apiAuth'])) {
  header("Content-Type: application/json; charset=utf-8");
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

if($_GET['apiAuth'] != $aiSettings['editPostToken']) {
  header("Content-Type: application/json; charset=utf-8");
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
 * Check whether a itemId has been transferred.
 */
if(empty($_GET['itemId'])) {
  header("Content-Type: application/json; charset=utf-8");
  http_response_code(400);
  die(
    json_encode(
      [
        'error' => 'itemIdNotProvided',
        'errorMsg' => 'You have to provide a itemId for your request.'
      ]
    )
  );
}

/**
 * Check whether the transferred itemId exists in the database.
 */
$itemId = intval(defuse($_GET['itemId']));
$result = mysqli_query($dbl, "SELECT `image`, `extension` FROM `items` WHERE `itemId`='".$itemId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) != 1) {
  header("Content-Type: application/json; charset=utf-8");
  http_response_code(404);
  die(
    json_encode(
      [
        'error' => 'itemIdNotFound',
        'errorMsg' => 'The itemId you provided was not found in the database.'
      ]
    )
  );
}
$row = mysqli_fetch_assoc($result);
$image = "https://img.pr0gramm.com/".$row['image'];

/**
 * Creating a log entry that the AI has requested the image.
 */
mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `itemId`, `text`) VALUES ("'.$kiUserId.'", 1, "'.$itemId.'", "'.$lang['api']['getJPG']['log'].'")') OR DIE(MYSQLI_ERROR($dbl));

/**
 * Initialize cURL
*/
$ch = curl_init();

/**
 * Set connection options.
 * @see https://www.php.net/manual/de/function.curl-setopt.php
 */
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => TRUE,
  CURLOPT_USERAGENT => $aiSettings['cURL']['userAgent'],
  CURLOPT_INTERFACE => $aiSettings['cURL']['bindTo'],
  CURLOPT_CONNECTTIMEOUT => 5,
  CURLOPT_TIMEOUT => 10,
  CURLOPT_URL => $image,
]);

/**
 * Execute the cURL operation.
 */
$response = curl_exec($ch);

/**
 * Evaluate the HTTP and the error code.
 */
$httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

if($httpCode != 200 OR curl_errno($ch) != 0) {
  header("Content-Type: application/json; charset=utf-8");
  http_response_code(500);
  die(
    json_encode(
      [
        'error' => 'cURLErrorImageDownload',
        'errorMsg' => 'cURL Error while downloading image file.'
      ]
    )
  );
}

/**
 * Close the cURL handle.
 */
curl_close($ch);

/**
 * Convert image to jpeg.
 */
$outputImage = imagecreatefromstring($response);
header("Content-Type: image/jpeg");
imagejpeg($outputImage, NULL);
imagedestroy($outputImage);
?>
