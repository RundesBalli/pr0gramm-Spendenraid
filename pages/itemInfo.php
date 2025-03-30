<?php
/**
 * pages/itemInfo.php
 * 
 * Page to show all data about an item.
 */

/**
 * Inclusion of the cookie check.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Title and heading
 */
$title = $lang['itemInfo']['title'];
$content.= '<h1>'.$lang['itemInfo']['title'].'</h1>';

/**
 * Show form
 */
$content.= '<form action="/itemInfo" method="get">';

/**
 * itemId/URL / Submit
 */
$content.= '<div class="row">'.
  '<div class="col-s-12 col-l-8"><input name="itemId" type="text" autocomplete="off" placeholder="'.$lang['itemInfo']['form']['placeholder'].'" autofocus></div>'.
  '<div class="col-s-12 col-l-4"><input type="submit" value="'.$lang['itemInfo']['form']['submit'].'"></div>'.
'</div>';

$content.= '</form>';
$content.= '<div class="spacer"></div>';

/**
 * Show item info if an itemId has been provided.
 */
if(empty($_GET['itemId'])) {
  return;
}

/**
 * Check whether a correct itemId or link has been provided.
 */
if(preg_match(ITEM_REGEX, defuse($_GET['itemId']), $match) !== 1) {
  $content.= '<div class="warnBox">'.$lang['itemInfo']['invalid'].'</div>';
  return;
}
$itemId = intval($match[1]);

/**
 * Check if the item exists.
 */
$result = mysqli_query($dbl, 'SELECT * FROM `items` WHERE `itemId`="'.$itemId.'" LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
if(mysqli_num_rows($result) == 0) {
  $content.= '<div class="infoBox">'.$lang['itemInfo']['notFound'].'</div>';
  return;
}
$row = mysqli_fetch_assoc($result);

/**
 * Heading
 */
$content.= '<h2>'.sprintf($lang['itemInfo']['heading'], $row['itemId']).'</h2>';

/**
 * Links
 */
$content.= '<h3>'.$lang['itemInfo']['links'].'</h3>';
$content.= '<p>'.$lang['itemInfo']['reset'].': <a href="/reset?itemId='.$row['itemId'].'">'.$lang['itemInfo']['resetItem'].'</a> - <a href="/reset?organization&itemId='.$row['itemId'].'">'.$lang['itemInfo']['resetOrga'].'</a>'.(($row['isDonation'] > 0 AND !empty($perkSecret)) ? ' - <a href="/itemInfo?itemId='.$row['itemId'].'&unlockUser">'.$lang['itemInfo']['unlockUser'].'</a>' : NULL).'</p>';

/**
 * Unlock user again.
 */
if(isset($_GET['unlockUser'])) {
  mysqli_query($dbl, 'INSERT INTO `queue` (`name`, `action`) VALUE ("'.defuse($row['username']).'", 1)') OR DIE(MYSQLI_ERROR($dbl));$qc++;
  mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `text`) VALUES ('.$userId.', 6, "'.sprintf($lang['itemInfo']['unlock']['log'], defuse($row['username'])).'")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
  $content.= '<div class="successBox">'.$lang['itemInfo']['unlock']['success'].'</div>';
}

/**
 * Add note to log
 */
$content.= '<h3>'.$lang['itemInfo']['commentForm']['title'].'</h3>';
$content.= '<form action="/itemInfo?itemId='.$row['itemId'].'" method="post">';
  $content.= '<input type="hidden" name="token" value="'.$sessionHash.'">';
$content.= '<div class="row">'.
  '<div class="col-s-12 col-l-8"><input name="text" type="text" autocomplete="off" placeholder="'.$lang['itemInfo']['commentForm']['note'].'"></div>'.
  '<div class="col-s-12 col-l-4"><input type="submit" value="'.$lang['itemInfo']['commentForm']['submit'].'"></div>'.
'</div>';
$content.= '</form>';

/**
 * Add note to database
 */
if(!empty($_POST['text']) AND !empty(trim($_POST['text']))) {
  /**
   * CSRF check
   */
  if($_POST['token'] != $sessionHash) {
    /**
     * Token invalid
     */
    $content.= '<div class="warnBox">'.$lang['itemInfo']['addNote']['invalidToken'].'</div>';
  } else {
    /**
     * Token is valid.
     */
    mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `itemId`, `text`) VALUES (".$userId.", 8, ".$itemId.", '".defuse($_POST['text'])."')") OR DIE(MYSQLI_ERROR($dbl));$qc++;
    $content.= '<div class="infoBox">'.$lang['itemInfo']['addNote']['success'].'</div>';
  }
}

/**
 * Log
 */
$content.= '<h3>'.$lang['itemInfo']['log']['title'].'</h3>';

/**
 * Log table heading
 */
$content.= '<div class="overflowXAuto"><table>';
$content.= '<tr style="border-left: 6px solid #888888;">
  <th>'.$lang['itemInfo']['log']['id'].'</td>
  <th>'.$lang['itemInfo']['log']['username'].'</td>
  <th>'.$lang['itemInfo']['log']['timestamp'].'</td>
  <th>'.$lang['itemInfo']['log']['itemId'].'</td>
  <th>'.$lang['itemInfo']['log']['text'].'</td>
</tr>';

/**
 * Log entrys
 */
$logResult = mysqli_query($dbl, 'SELECT `log`.`id`, `users`.`name`, `users`.`bot`, `log`.`timestamp`, `log`.`logLevel`, `metaLogLevel`.`color`, `log`.`itemId`, `log`.`text` FROM `log` LEFT OUTER JOIN `users` ON `users`.`id`=`log`.`userId` JOIN `metaLogLevel` ON `log`.`logLevel`=`metaLogLevel`.`id` WHERE `itemId`="'.$row['itemId'].'" ORDER BY `log`.`id` DESC') OR DIE(MYSQLI_ERROR($dbl));$qc++;
while($logRow = mysqli_fetch_assoc($logResult)) {
  /**
   * Calculate color.
   */
  $colorRgb = hex2rgb($logRow['color']);

  /**
   * Timezone shit.
   */
  $ts = new DateTime($logRow['timestamp'], new DateTimeZone('UTC'));
  $ts->setTimezone(new DateTimeZone('Europe/Berlin'));

  /**
   * Table row
   */
  $content.= '<tr style="border-left: 6px solid #'.$logRow['color'].'; background-color: rgba('.$colorRgb['r'].', '.$colorRgb['g'].', '.$colorRgb['b'].', 0.04);">
    <td>'.$logRow['id'].'</td>
    <td>'.($logRow['name'] === NULL ? '<span class="italic">'.$lang['itemInfo']['log']['system'].'</span>' : ($logRow['name'] == $username ? '<span class="highlight">'.output($logRow['name']).'</span>' : ($logRow['bot'] ? '<span class="italic">'.output($logRow['name']).'</span>' : output($logRow['name'])))).'</td>
    <td class="noBreak">'.$ts->format('Y-m-d H:i:s').'</td>
    <td class="noBreak">'.($logRow['itemId'] === NULL ? '<span class="italic">NULL</span>' : '<a href="https://pr0gramm.com/new/'.$logRow['itemId'].'" target="_blank" rel="noopener">'.$logRow['itemId'].'</a> (<a href="/itemInfo?itemId='.$logRow['itemId'].'">'.$lang['itemInfo']['itemInfo'].'</a>)'.($logRow['logLevel'] != 5 ? '<br>'.$lang['itemInfo']['reset'].': <a href="/reset?itemId='.$logRow['itemId'].'">'.$lang['itemInfo']['resetItem'].'</a> - <a href="/reset?organization&itemId='.$logRow['itemId'].'">'.$lang['itemInfo']['resetOrga'].'</a>' : NULL)).'</td>
    <td>'.clickableLinks(output($logRow['text'])).'</td>
  </tr>';
}
$content.= '</table></div>';

/**
 * Loglevel
 */
$content.= '<h3>'.$lang['itemInfo']['logLevel'].'</h3>';
$logLevelResult = mysqli_query($dbl, 'SELECT * FROM `metaLogLevel` ORDER BY `id` ASC') OR DIE(MYSQLI_ERROR($dbl));$qc++;
$content.= '<div class="row">';
while($logLevelRow = mysqli_fetch_assoc($logLevelResult)) {
  $content.= '<div class="col-s-12 col-l-3" style="color: #'.$logLevelRow['color'].';">'.$lang['logLevel'][$logLevelRow['type']].'</div>';
}
$content.= '</div>';

/**
 * DB Dump
 */
$content.= '<h3>'.$lang['itemInfo']['dbDump'].'</h3>';
$content.= '<p><pre class="smaller">'.var_export($row, TRUE).'</pre></p>';
?>
