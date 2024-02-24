<?php
/**
 * pages/reset.php
 * 
 * Page to reset the whole item or just the organization.
 */

/**
 * Inclusion of the cookie check.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Check whether an itemId has been provided.
 */
if(empty($_GET['itemId']) OR !is_numeric(trim($_GET['itemId']))) {
  $content.= '<div class="warnBox">'.$lang['reset']['noId'].'</div>';
  return;
}
$itemId = intval(trim(defuse($_GET['itemId'])));

/**
 * Check whether the itemId is valid.
 */
$result = mysqli_query($dbl, 'SELECT * FROM `items` WHERE `itemId`='.$itemId.' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
if(!mysqli_num_rows($result)) {
  $content.= '<div class="warnBox">'.$lang['reset']['invalidId'].'</div>';
  return;
}

/**
 * Check whether the user has already confirmed the action. If not, a form is displayed.
 */
if(!isset($_POST['submit'])) {
  /**
   * The confirmation has not been send yet. Show the form.
   */
  $content.= '<form action="/reset?'.(isset($_GET['organization']) ? 'organization&' : NULL).'itemId='.$itemId.'" method="post">';

  /**
   * Token
   */
  $content.= '<input type="hidden" name="token" value="'.$sessionHash.'">';

  /**
   * Confirmation
   */
  $content.= '<div class="row">'.
    '<div class="col-s-12 col-l-12">'.sprintf($lang['reset']['confirmationQuestion'], (isset($_GET['organization']) ? $lang['reset']['organization'] : $lang['reset']['item'])).'</div>'.
  '</div>';
  $content.= '<div class="row">'.
    '<div class="col-s-12 col-l-12"><input type="submit" name="submit" value="'.$lang['reset']['confirmation'].'"></div>'.
  '</div>';
  $content.= '</form>';

  return;
}

/**
 * CSRF Check
 */
if($_POST['token'] != $sessionHash) {
  /**
   * Invalid token
   */
  $content.= '<div class="warnBox">'.$lang['reset']['invalidToken'].'</div>';
}

/**
 * Token is valid. Resetting the item/organization.
 */
mysqli_query($dbl, 'UPDATE `items` SET '.(!isset($_GET['organization']) ? '`firstsightValue`=NULL, `firstsightUserId`=NULL, `confirmedValue`=NULL, `confirmedUserId`=NULL, `isDonation`=NULL, ' : NULL).'`firstsightOrgaId`=NULL, `firstsightOrgaUserId`=NULL, `confirmedOrgaId`=NULL, `confirmedOrgaUserId`=NULL WHERE `itemId`="'.$itemId.'" LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `itemId`, `text`) VALUES ('.$userId.', 5, '.$itemId.', "'.(!isset($_GET['organization']) ? $lang['reset']['log']['resetItem'] : $lang['reset']['log']['resetOrganization']).'")') OR DIE(MYSQLI_ERROR($dbl));$qc++;

/**
 * If the whole item has been resetted, the perk has to be checked and locked.
 */
if(!isset($_GET['organization'])) {
  mysqli_query($dbl, 'INSERT INTO `queue` (`name`, `action`) VALUES ("'.$row['username'].'", 0)') OR DIE(MYSQLI_ERROR($dbl));$qc++;
}
$content.= '<div class="successBox">'.(isset($_GET['organization']) ? $lang['reset']['successOrga'] : $lang['reset']['success']).'<br><a href="/itemInfo?itemId='.$itemId.'">'.$lang['reset']['itemInfo'].'</a> - <a href="/evaluation">'.$lang['reset']['evaluateItems'].'</a></div>';
?>
