<?php
/**
 * pages/queue.php
 * 
 * Queue list
 */

/**
 * Inclusion of the cookie check.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Title and heading
 */
$title = $lang['queue']['title'];
$content.= '<h1>'.$lang['queue']['title'].'</h1>';

/**
 * Check whether the user has the permission to enter this site.
 */
if(!defined('perm-showQueue')) {
  $content.= '<div class="warnBox">'.$lang['queue']['noPermission'].'</div>';
  return;
}

/**
 * Reset
 */
if(isset($_GET['reset'])) {
  mysqli_query($dbl, 'UPDATE `queue` SET `error`=0') OR DIE(MYSQLI_ERROR($dbl));
  $content.= '<div class="successBox">'.$lang['queue']['resetSuccess'].'</div>';
  mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `text`) VALUES ("'.$userId.'", 6, "'.$lang['queue']['resetLog'].'")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
}

/**
 * Check whether elements are available.
 */
$result = mysqli_query($dbl, 'SELECT * FROM `queue` ORDER BY `id` ASC') OR DIE(MYSQLI_ERROR($dbl));$qc++;
if(!mysqli_num_rows($result)) {
  $content.= '<div class="infoBox">'.$lang['queue']['noElements'].'</div>';
  return;
}

/**
 * Reset link
 */
$content.= '<div class="row">'.
  '<div class="col-s-12 col-l-12"><a href="/queue?reset">'.$lang['queue']['resetLink'].'</a></div>'.
'</div>';
$content.= '<div class="spacer"></div>';

/**
 * Table heading.
 */
$content.= '<div class="row highlight bold">'.
  '<div class="col-s-0 col-l-2">'.$lang['queue']['id'].'</div>'.
  '<div class="col-s-12 col-l-6">'.$lang['queue']['name'].'</div>'.
  '<div class="col-s-6 col-l-3">'.$lang['queue']['action'].'</div>'.
  '<div class="col-s-6 col-l-1">'.$lang['queue']['error'].'</div>'.
'</div>';

/**
 * Iterate through elements.
 */
while($row = mysqli_fetch_assoc($result)) {
  $content.= '<div class="row hover bordered">'.
    '<div class="col-s-0 col-l-2">'.$row['id'].'</div>'.
    '<div class="col-s-12 col-l-6">'.$row['name'].'</div>'.
    '<div class="col-s-6 col-l-3">'.($row['action'] ? $lang['queue']['unlock'] : $lang['queue']['lock']).'</div>'.
    '<div class="col-s-6 col-l-1">'.($row['error'] ? $lang['queue']['yes'] : $lang['queue']['no']).'</div>'.
  '</div>';
}
$content.= '<div class="spacer"></div>';
?>
