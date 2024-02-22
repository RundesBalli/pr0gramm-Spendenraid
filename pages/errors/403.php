<?php
/**
 * pages/errors/403.php
 * 
 * 403 ErrorDocument.
 * Returns the error message, as well as the requested path.
 */
$title = '403 Forbidden';
http_response_code(403);
$content.= '<h1>403 Forbidden</h1>';
$content.= '<div class="row">'.
  '<div class="col-s-12 col-l-12">'.sprintf($lang['error']['403'], output($_SERVER['REQUEST_URI'])).'</div>'.
'</div>';
?>
