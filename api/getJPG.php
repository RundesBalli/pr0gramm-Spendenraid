<?php
/**
 * getJPG.php
 * 
 * API Endpunkt um von den Bildern auf pr0gramm eine JPG Variante zu erhalten.
 */

/**
 * Einbinden der Konfigurationsdatei sowie der Funktionsdatei
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."config.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."functions.php");

/**
 * Prüfen ob das Script per HTTP-GET Methode aufgerufen wurde.
 */
if($_SERVER['REQUEST_METHOD'] !== 'GET') {
  http_response_code(405);
  header("Access-Control-Allow-Methods: GET");
  header("Content-Type: application/json; charset=utf-8");
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
 * Prüfen ob ein Token zur Übermittlung an diese API konfiguriert ist und wenn ja, ob es übergeben wurde und korrekt ist.
 */
if(empty($editPostToken) OR empty($kiUserId)) {
  http_response_code(501);
  header("Content-Type: application/json; charset=utf-8");
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
 * Um die Anfrage so einfach wie möglich zu halten wird in diesem Fall auf den Auth per Header
 * verzichtet und stattdessen per GET das Token übergeben.
 */
if(empty($_GET['apiAuth'])) {
  http_response_code(400);
  header("Content-Type: application/json; charset=utf-8");
  die(
    json_encode(
      [
        'error' => 'tokenNotProvided',
        'errorMsg' => 'You have to provide a token for your request.'
      ]
    )
  );
}

if($_GET['apiAuth'] != $editPostToken) {
  http_response_code(401);
  header("Content-Type: application/json; charset=utf-8");
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
 * Prüfen ob eine PostID übergeben wurde.
 */
if(empty($_GET['postId'])) {
  http_response_code(400);
  header("Content-Type: application/json; charset=utf-8");
  die(
    json_encode(
      [
        'error' => 'postIdNotProvided',
        'errorMsg' => 'You have to provide a postId for your request.'
      ]
    )
  );
}

/**
 * Prüfen ob die übergebene PostId in der Datenbank existiert.
 */
$postId = intval(defuse($_GET['postId']));
$result = mysqli_query($dbl, "SELECT `image`, `extension` FROM `items` WHERE `postId`='".$postId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) != 1) {
  http_response_code(404);
  header("Content-Type: application/json; charset=utf-8");
  die(
    json_encode(
      [
        'error' => 'postIdNotFound',
        'errorMsg' => 'The postId you provided was not found in the database.'
      ]
    )
  );
} else {
  $row = mysqli_fetch_assoc($result);
  $image = "https://img.pr0gramm.com/".$row['image'];
}

/**
 * Erzeugen eines Logeintrages, dass die KI das Bild angefragt hat.
 */
mysqli_query($dbl, "INSERT INTO `log` (`logLevel`, `postId`, `text`) VALUES (1, '".$postId."', '[KI, JPG] KI Anfrage JPG')") OR DIE(MYSQLI_ERROR($dbl));

/**
 * cURL initialisieren
*/
$ch = curl_init();

/**
 * Verbindungsoptionen vorbereiten
 * @see https://www.php.net/manual/de/function.curl-setopt.php
 */
$options = array(
  CURLOPT_RETURNTRANSFER => TRUE,
  CURLOPT_USERAGENT => $kiCURL['userAgent'],
  CURLOPT_INTERFACE => $kiCURL['bindTo'],
  CURLOPT_CONNECTTIMEOUT => 5,
  CURLOPT_TIMEOUT => 10
);

/**
 * Setzen der Bild URL
 */
$options[CURLOPT_URL] = $image;

/**
 * Das Optionsarray in den cURL-Handle einfügen
 */
curl_setopt_array($ch, $options);

/**
 * Ausführen des cURLs und speichern der Antwort
 */
$response = curl_exec($ch);

/**
 * Auswerten des HTTP und des Fehlercodes.
 */
$httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

if($httpCode != 200 OR curl_errno($ch) != 0) {
  http_response_code(500);
  header("Content-Type: application/json; charset=utf-8");
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
 * Beenden des cURL-Handles.
 */
curl_close($ch);

/**
 * Umwandeln in JPG
 */
$outputImage = imagecreatefromstring($response);
header("Content-Type: image/jpeg");
imagejpeg($outputImage, NULL);
imagedestroy($outputImage);
?>
