<?php
/**
 * pages/log.php
 * 
 * Action log
 */

/**
 * Inclusion of the cookie check.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Title and heading
 */
$title = $lang['log']['title'];
$content.= '<h1>'.$lang['log']['title'].'</h1>';

/**
 * Log table heading
 */
$content.= '<div class="overflowXAuto"><table>';
$content.= '<tr style="border-left: 6px solid #888888;">
  <th>'.$lang['log']['log']['id'].'</td>
  <th>'.$lang['log']['log']['username'].'</td>
  <th>'.$lang['log']['log']['timestamp'].'</td>
  <th>'.$lang['log']['log']['itemId'].'</td>
  <th>'.$lang['log']['log']['text'].'</td>
</tr>';

/**
 * Search parameters
 */
$where = '';
if(!empty($_GET['older'])) {
  $older = intval(defuse($_GET['older']));
  if($older > 1) {
    $where = 'WHERE `log`.`id` < "'.$older.'" ';
  }
}

/**
 * Log entrys
 */
$result = mysqli_query($dbl, 'SELECT `log`.`id`, `users`.`name`, `users`.`bot`, `log`.`timestamp`, `log`.`logLevel`, `metaLogLevel`.`color`, `log`.`itemId`, `log`.`text` FROM `log` LEFT OUTER JOIN `users` ON `users`.`id`=`log`.`userId` JOIN `metaLogLevel` ON `log`.`logLevel`=`metaLogLevel`.`id` '.$where.'ORDER BY `log`.`id` DESC LIMIT 100') OR DIE(MYSQLI_ERROR($dbl));$qc++;
$logIds = [];
while($row = mysqli_fetch_assoc($result)) {
  /**
   * Calculate color.
   */
  $colorRgb = hex2rgb($row['color']);

  /**
   * Timezone shit.
   */
  $ts = new DateTime($row['timestamp'], new DateTimeZone('UTC'));
  $ts->setTimezone(new DateTimeZone('Europe/Berlin'));

  /**
   * Table row
   */
  $content.= '<tr style="border-left: 6px solid #'.$row['color'].'; background-color: rgba('.$colorRgb['r'].', '.$colorRgb['g'].', '.$colorRgb['b'].', 0.04);">
    <td>'.$row['id'].'</td>
    <td>'.($row['name'] === NULL ? '<span class="italic">'.$lang['log']['log']['system'].'</span>' : ($row['name'] == $username ? '<span class="highlight">'.output($row['name']).'</span>' : ($row['bot'] ? '<span class="italic">'.output($row['name']).'</span>' : output($row['name'])))).'</td>
    <td class="noBreak">'.$ts->format('Y-m-d H:i:s').'</td>
    <td class="noBreak">'.($row['itemId'] === NULL ? '<span class="italic">NULL</span>' : '<a href="https://pr0gramm.com/new/'.$row['itemId'].'" target="_blank" rel="noopener">'.$row['itemId'].'</a> (<a href="/itemInfo?itemId='.$row['itemId'].'">'.$lang['log']['itemInfo'].'</a>)'.($row['logLevel'] != 5 ? '<br>'.$lang['log']['reset'].': <a href="/reset?itemId='.$row['itemId'].'">'.$lang['log']['resetItem'].'</a> - <a href="/reset?organization&itemId='.$row['itemId'].'">'.$lang['log']['resetOrga'].'</a>' : NULL)).'</td>
    <td>'.clickableLinks(output($row['text'])).'</td>
  </tr>';
  $logIds[] = $row['id'];
}
$content.= '</table></div>';

/**
 * Older
 */
$result = mysqli_query($dbl, 'SELECT `id` FROM `log` WHERE `id`<'.min($logIds).' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
if(mysqli_num_rows($result)) {
  $content.= '<p class="textRight"><a href="/log?older='.min($logIds).'">'.$lang['log']['older'].' »</a></p>';
}

/**
 * Loglevel
 */
$content.= '<h2>'.$lang['log']['logLevel'].'</h2>';
$logLevelResult = mysqli_query($dbl, 'SELECT * FROM `metaLogLevel` ORDER BY `id` ASC') OR DIE(MYSQLI_ERROR($dbl));$qc++;
$content.= '<div class="row">';
while($logLevelRow = mysqli_fetch_assoc($logLevelResult)) {
  $content.= '<div class="col-s-12 col-l-3" style="color: #'.$logLevelRow['color'].';">'.$lang['logLevel'][$logLevelRow['type']].'</div>';
}
$content.= '</div>';

/**
 * Highscore (log entrys)
 */
$content.= '<h2>'.$lang['log']['highscore']['title'].'</h2>';
$content.= '<div class="overflowXAuto"><table>';
$content.= '<tr>
  <th>'.$lang['log']['highscore']['place'].'</th>
  <th>'.$lang['log']['highscore']['name'].'</th>
  <th>'.$lang['log']['highscore']['delta'].' (Δ)</th>
</tr>';

/**
 * User
 */
$result = mysqli_query($dbl, 'SELECT count(`log`.`id`) AS `count`, `users`.`name` FROM `log` LEFT OUTER JOIN `users` ON `users`.`id`=`log`.`userId` WHERE `userId` IS NOT NULL'.(!empty($aiSettings['userId']) ? ' AND `userId`!='.$aiSettings['userId'] : NULL).' GROUP BY `userId` ORDER BY `count` DESC') OR DIE(MYSQLI_ERROR($dbl));$qc++;
$place = 0;
$previous = 0;
while($row = mysqli_fetch_assoc($result)) {
  $place++;
  $content.= '<tr>
    <td>'.($place == 1 ? '&#x1F451;' : $place).'</td>
    <td>'.output($row['name']).'</td>
    <td>'.$row['count'].($previous != 0 ? ' ('.($previous-$row['count']).')' : NULL).'</td>
  </tr>';
  $previous = $row['count'];
}
$content.= '</table></div>';

/**
 * System
 */
$content.= '<h2>'.$lang['log']['highscoreSystem']['title'].'</h2>';
$content.= '<div class="overflowXAuto"><table>';
$content.= '<tr>
  <th>'.$lang['log']['highscoreSystem']['symbol'].'</td>
  <th>'.$lang['log']['highscoreSystem']['name'].'</td>
  <th>'.$lang['log']['highscoreSystem']['entrys'].'</td>
</tr>';
$result = mysqli_query($dbl, 'SELECT count(`log`.`id`) AS `count`, `userId` FROM `log` WHERE `userId` IS NULL'.(!empty($aiSettings['userId']) ? ' OR `userId`='.$aiSettings['userId'] : NULL).' GROUP BY `userId` ORDER BY `count` DESC') OR DIE(MYSQLI_ERROR($dbl));$qc++;
while($row = mysqli_fetch_assoc($result)) {
  $content.= '<tr>
    <td>&#x1F5A5;</td>
    <td>'.($row['userId'] === NULL ? '<span class="italic">'.$lang['log']['highscoreSystem']['system'].'</span>' : '<span class="italic">'.$lang['log']['highscoreSystem']['ai'].'</span>').'</td>
    <td>'.$row['count'].'</td>
  </tr>';
}
$content.= '</table></div>';
?>
