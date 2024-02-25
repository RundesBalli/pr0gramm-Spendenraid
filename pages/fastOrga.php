<?php
/**
 * pages/fastOrga.php
 * 
 * Quick evaluation page to quickly assign organisations.
 */

/**
 * Inclusion of the cookie check.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Title and heading
 */
$title = $lang['fastOrga']['title'];
$content.= '<h1>'.$lang['fastOrga']['title'].'</h1>';

/**
 * Check whether the user has the permission to enter this site.
 */
if(!defined('perm-fastOrgaEvaluation')) {
  $content.= '<div class="warnBox">'.$lang['fastOrga']['noPermission'].'</div>';
  return;
}

/**
 * Check if an organization ID has been provided.
 */
if(empty($_GET['id']) OR !is_numeric($_GET['id'])) {
  $content.= '<div class="warnBox">'.$lang['fastOrga']['noId'].'</div>';
  return;
}
$orgaId = intval(defuse($_GET['id']));

/**
 * Check whether the organization exists and whether it is allowed to be fast evaluated.
 */
$result = mysqli_query($dbl, 'SELECT `name` FROM `metaOrganizations` WHERE `id`='.$orgaId.' AND `shortName` IS NOT NULL LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
if(!mysqli_num_rows($result)) {
  $content.= '<div class="warnBox">'.$lang['fastOrga']['invalidId'].'</div>';
  return;
}
$row = mysqli_fetch_assoc($result);

/**
 * Show which organization will be evaluated in this view.
 */
$content.= '<h2 class="highlight">'.$orgaId.' - '.output($row['name']).'</h2>';
$content.= '<div class="spacer"></div>';

/**
 * Fast evaluation.
 */
if(!empty($_GET['itemId']) AND is_numeric($_GET['itemId'])) {
  $itemId = intval(defuse($_GET['itemId']));
  $result = mysqli_query($dbl, 'SELECT `firstsightOrgaId` FROM `items` WHERE `itemId`='.$itemId.' AND (`isDonation`=1 AND (`firstsightOrgaId` IS NULL OR (`confirmedOrgaId` IS NULL AND `firstsightOrgaUserId`!='.$userId.'))) AND (`extension`!="mp4" AND `extension`!="gif") LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;

  /**
   * Check if the item exists, is a donation and is not already evaluated and not already evaluated by the user.
   */
  if(!mysqli_num_rows($result)) {
    $content.= '<div class="warnBox">'.$lang['fastOrga']['invalidItemId'].'</div>';
    return;
  }
  $row = mysqli_fetch_assoc($result);

  if($row['firstsightOrgaId'] === NULL) {
    /**
     * First sight.
     */
    mysqli_query($dbl, 'UPDATE `items` SET `firstsightOrgaId`='.$orgaId.', `firstsightOrgaUserId`='.$userId.' WHERE `itemId`='.$itemId.' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
    mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `itemId`, `text`) VALUES ("'.$userId.'", 2, "'.$itemId.'", "'.$lang['fastOrga']['log']['organization'].': '.$orgaId.'")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
  } elseif($row['firstsightOrgaId'] != $orgaId) {
    /**
     * First sight and confirming sight are not equal.
     */
    mysqli_query($dbl, 'UPDATE `items` SET `firstsightOrgaId`='.$orgaId.', `firstsightOrgaUserId`='.$userId.' WHERE `itemId`='.$itemId.' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
    mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `itemId`, `text`) VALUES ("'.$userId.'", 3, "'.$itemId.'", "'.$lang['fastOrga']['log']['organization'].': '.$orgaId.' ('.$lang['fastOrga']['log']['confirmingReset'].': '.$row['firstsightOrgaId'].')")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
  } elseif($row['firstsightOrgaId'] == $orgaId) {
    /**
     * First sight is equal to the confirming sight.
     */
    mysqli_query($dbl, 'UPDATE `items` SET `confirmedOrgaId`='.$orgaId.', `confirmedOrgaUserId`='.$userId.' WHERE `itemId`='.$itemId.' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
    mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `itemId`, `text`) VALUES ("'.$userId.'", 4, "'.$itemId.'", "'.$lang['fastOrga']['log']['organization'].': '.$orgaId.'")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
  }
  $content.= '<div class="successBox">'.$lang['fastOrga']['success'].'</div>';
  $content.= '<div class="infoBox">'.$lang['fastOrga']['successTab'].'</div>';
  return;
}

/**
 * Note
 */
$content.= '<h3 class="warn">'.$lang['fastOrga']['noteTitle'].'</h3>';
$content.= '<div class="row">'.
  '<div class="col-s-12 col-l-12 warn bold">'.$lang['fastOrga']['note1'].'</div>'.
  '<div class="col-s-12 col-l-12">'.$lang['fastOrga']['note2'].'</div>'.
'</div>';
$content.= '<div class="spacer"></div>';

/**
 * Selection of items that have not yet been rated at all or have not yet been rated by the user.
 */
$result = mysqli_query($dbl, 'SELECT * FROM `items` WHERE (`isDonation`=1 AND (`firstsightOrgaId` IS NULL OR (`confirmedOrgaId` IS NULL AND `firstsightOrgaUserId`!='.$userId.'))) AND (`extension`!="mp4" AND `extension`!="gif") ORDER BY RAND()') OR DIE(MYSQLI_ERROR($dbl));$qc++;
if(mysqli_num_rows($result) != 0) {
  $content.= '<div class="row">'.
  '<div class="col-s-12 col-l-12">';
  while($row = mysqli_fetch_assoc($result)) {
    $content.= '<a href="/fastOrga?id='.$orgaId.'&itemId='.$row['itemId'].'"><img src="https://thumb.pr0gramm.com/'.$row['thumb'].'" alt="Thumb" style="margin: 5px;"></a>';
  }
  $content.= '</div>';
} else {
  /**
   * All done.
   */
  $content.= '<div class="infoBox">'.$lang['fastOrga']['allDone'].'</div>';
  $content.= '<div class="spacer"></div>';
}
?>
