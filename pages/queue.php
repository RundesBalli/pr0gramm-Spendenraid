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
$content.= '<p><a href="/queue?reset">'.$lang['queue']['resetLink'].'</a></p>';

/**
 * Table heading.
 */
$content.= '<div class="overflowXAuto"><table>';
$content.= '<tr>
  <th>'.$lang['queue']['id'].'</th>
  <th>'.$lang['queue']['name'].'</th>
  <th>'.$lang['queue']['action'].'</th>
  <th>'.$lang['queue']['error'].'</th>
</tr>';

/**
 * Iterate through elements.
 */
while($row = mysqli_fetch_assoc($result)) {
  $content.= '<tr>
    <td>'.$row['id'].'</td>
    <td>'.$row['name'].'</td>
    <td>'.($row['action'] ? $lang['queue']['unlock'] : $lang['queue']['lock']).'</td>
    <td>'.($row['error'] ? $lang['queue']['yes'] : $lang['queue']['no']).'</td>
  </tr>';
}
$content.= '</table></div>';
?>
