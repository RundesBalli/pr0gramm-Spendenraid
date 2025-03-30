<?php
/**
 * pages/errors/500.php
 * 
 * 500 ErrorDocument.
 * Outputs an internal server error.
 */
$title = '500 Internal Server Error';
http_response_code(500);
$content.= '<h1>500 Internal Server Error</h1>';
if(!empty($error) AND array_key_exists($error, $lang['error']['500'])) {
  if(!empty($lang['error']['500'][$error])) {
    $errorMessage = $lang['error']['500'][$error];
  } else {
    $errorMessage = $lang['error']['500']['unknownError'];
  }
} else {
  $errorMessage = $lang['error']['500']['unknownError'];
}
$content.= '<p>'.$errorMessage.'</p>';
?>
