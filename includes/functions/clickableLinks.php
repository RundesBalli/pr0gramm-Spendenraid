<?php
/**
 * includes/functions/clickableLinks.php
 */
/**
 * clickableLinks
 * 
 * Function to link urls in texts.
 *
 * @param string $string String whose urls are to be converted into links.
 * 
 * @return string The string with converted links.
 */
function clickableLinks(string $string) {
  return preg_replace('/https?:\/\/[^\s]+/im', '<a href=\'$0\' target=\'_blank\' rel=\'noopener\'>$0</a>', $string);
}
?>
