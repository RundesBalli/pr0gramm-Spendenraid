<?php
/**
 * pages/errors/404.php
 * 
 * 404 ErrorDocument.
 * Returns the error message, as well as the requested path.
 */
$title = '404 Not Found';
http_response_code(404);
$content.= '<h1>404 Not Found</h1>';
$content.= '<p>'.sprintf($lang['error']['404'], output($_SERVER['REQUEST_URI'])).'</p>';
?>
