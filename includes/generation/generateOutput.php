<?php
/**
 * includes/generation/generateOutput.php
 * 
 * Generates the output with previous generated contents.
 */
$output = preg_replace(
  [
    '/{LANG}/im',
    '/{TITLE}/im',
    '/{NAV}/im',
    '/{CONTENT}/im',
    '/{FOOTER}/im',
  ],
  [
    $lang['locale'],
    (!empty($title) ? $title.' - ' : NULL),
    $nav,
    $content,
    $footer,
  ],
  $template
);
?>
