<?php
/**
 * pages/checkPost.php
 * 
 * Show information about an item.
 */

/**
 * Title and heading
 */
$title = $lang['checkPost']['title'];
$content.= '<h1>'.$lang['checkPost']['title'].'</h1>';

/**
 * Show form
 */
$content.= '<form method="post">';

/**
 * itemId/URL / Submit
 */
$content.= '<div class="row">'.
  '<div class="col-s-12 col-l-8"><input name="itemId" type="text" autocomplete="off" placeholder="'.$lang['checkPost']['form']['placeholder'].'" autofocus></div>'.
  '<div class="col-s-12 col-l-4"><input type="submit" value="'.$lang['checkPost']['form']['submit'].'"></div>'.
'</div>';

$content.= '</form>';
$content.= '<div class="spacer"></div>';

/**
 * Show item info if an itemId has been provided.
 */
if(empty($_POST['itemId'])) {
  return;
}

/**
 * Check whether a correct itemId or link has been provided.
 */
if(preg_match(ITEM_REGEX, defuse($_POST['itemId']), $match) !== 1) {
  $content.= '<div class="warnBox">'.$lang['checkPost']['invalid'].'</div>';
  return;
}
$itemId = intval($match[1]);

/**
 * Check if the item exists.
 */
$result = mysqli_query($dbl, 'SELECT * FROM `items` WHERE `itemId`="'.$itemId.'" LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
if(mysqli_num_rows($result) == 0) {
  $content.= '<div class="warnBox">'.$lang['checkPost']['notFound'].'</div>';
  return;
}
$row = mysqli_fetch_assoc($result);

/**
 * Check if the item fulfills the requirements to count as a donation.
 */
if($row['isDonation'] === NULL) {
  $content.= '<div class="infoBox">'.$lang['checkPost']['notCheckedRightNow'].'</div>';
  return;
} elseif($row['isDonation'] == 1) {
  $content.= '<div class="successBox">'.$lang['checkPost']['isDonation'].'</div>';
  return;
} elseif($row['isDonation'] == 0) {
  $content.= '<div class="infoBox">'.$lang['checkPost']['isNoDonation'].'</div>';
  return;
}
?>
