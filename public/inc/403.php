<?php
/**
 * 403.php
 * 
 * 403 ErrorDocument.
 * Gibt die Fehlermeldung, sowie den angeforderten Pfad zurück.
 */
$title = "403";
http_response_code(403);
$content.= "<h1>403 - Forbidden</h1>";
$content.= "<div class='row'>".
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Du hast keine Berechtigung auf die von dir angeforderte Ressource <span class='italic'>".output($_SERVER['REQUEST_URI'])."</span> zuzugreifen.</div>".
"</div>";
?>
