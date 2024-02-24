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
$content.= '<div class="row highlight bold bordered" style="border-left: 6px solid #888888;">'.
  '<div class="col-s-4 col-l-1">'.$lang['log']['log']['id'].'</div>'.
  '<div class="col-s-4 col-l-2">'.$lang['log']['log']['username'].'</div>'.
  '<div class="col-s-4 col-l-3">'.$lang['log']['log']['timestamp'].'</div>'.
  '<div class="col-s-4 col-l-2">'.$lang['log']['log']['itemId'].'</div>'.
  '<div class="col-s-8 col-l-4">'.$lang['log']['log']['text'].'</div>'.
  '<div class="col-s-12 col-l-0"><div class="spacer"></div></div>'.
'</div>';

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
  $colorRgb = hex2rgb($row['color']);
  $content.= '<div class="row hover bordered" style="border-left: 6px solid #'.$row['color'].'; background-color: rgba('.$colorRgb['r'].', '.$colorRgb['g'].', '.$colorRgb['b'].', 0.04);">'.
    '<div class="col-s-4 col-l-1">'.$row['id'].'</div>'.
    '<div class="col-s-4 col-l-2">'.($row['name'] === NULL ? '<span class="italic">'.$lang['log']['log']['system'].'</span>' : ($row['name'] == $username ? '<span class="highlight">'.output($row['name']).'</span>' : ($row['bot'] ? '<span class="italic">'.output($row['name']).'</span>' : output($row['name'])))).'</div>'.
    '<div class="col-s-4 col-l-3">'.date('d.m.Y, H:i:s', strtotime($row['timestamp'])).'</div>'.
    '<div class="col-s-4 col-l-2">'.($row['itemId'] === NULL ? '<span class="italic">NULL</span>' : '<a href="https://pr0gramm.com/new/'.$row['itemId'].'" target="_blank" rel="noopener">'.$row['itemId'].'</a> (<a href="/itemInfo?itemId='.$row['itemId'].'">'.$lang['log']['itemInfo'].'</a>)'.($row['logLevel'] != 5 ? '<br>'.$lang['log']['reset'].': <a href="/resetItem?itemId='.$row['itemId'].'">'.$lang['log']['resetItem'].'</a> - <a href="/resetOrga?itemId='.$row['itemId'].'">'.$lang['log']['resetOrga'].'</a>' : NULL)).'</div>'.
    '<div class="col-s-8 col-l-4">'.clickableLinks(output($row['text'])).'</div>'.
    '<div class="col-s-12 col-l-0"><div class="spacer"></div></div>'.
  '</div>';
  $logIds[] = $row['id'];
}

/**
 * Older
 */
$result = mysqli_query($dbl, 'SELECT `id` FROM `log` WHERE `id`<'.min($logIds).' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
if(mysqli_num_rows($result)) {
  $content.= '<div class="row">'.
    '<div class="col-s-12 col-l-12 textRight"><a href="/log?older='.min($logIds).'">'.$lang['log']['older'].' »</a></div>'.
  '</div>';
}
$content.= '<div class="spacer"></div>';

/**
 * Loglevel
 */
$content.= '<h3>'.$lang['log']['logLevel'].'</h3>';
$logLevelResult = mysqli_query($dbl, 'SELECT * FROM `metaLogLevel` ORDER BY `id` ASC') OR DIE(MYSQLI_ERROR($dbl));$qc++;
$content.= '<div class="row">';
while($logLevelRow = mysqli_fetch_assoc($logLevelResult)) {
  $content.= '<div class="col-s-12 col-l-3" style="color: #'.$logLevelRow['color'].';">'.$lang['logLevel'][$logLevelRow['type']].'</div>';
}
$content.= '</div>';
$content.= '<div class="spacer"></div>';

/**
 * Highscore (log entrys)
 */
$content.= '<h2>'.$lang['log']['highscore']['title'].'</h2>';
$content.= '<div class="row highlight bold">'.
  '<div class="col-s-2 col-l-2">'.$lang['log']['highscore']['place'].'</div>'.
  '<div class="col-s-6 col-l-4">'.$lang['log']['highscore']['name'].'</div>'.
  '<div class="col-s-4 col-l-6">'.$lang['log']['highscore']['delta'].' (Δ)</div>'.
'</div>';

/**
 * User
 */
$result = mysqli_query($dbl, 'SELECT count(`log`.`id`) AS `count`, `users`.`name` FROM `log` LEFT OUTER JOIN `users` ON `users`.`id`=`log`.`userId` WHERE `userId` IS NOT NULL'.(!empty($aiSettings['userId']) ? ' AND `userId`!='.$aiSettings['userId'] : NULL).' GROUP BY `userId` ORDER BY `count` DESC') OR DIE(MYSQLI_ERROR($dbl));$qc++;
$place = 0;
$previous = 0;
while($row = mysqli_fetch_assoc($result)) {
  $place++;
  $content.= '<div class="row hover">'.
    '<div class="col-s-2 col-l-2">'.($place == 1 ? '&#x1F451;' : $place).'</div>'.
    '<div class="col-s-6 col-l-4">'.output($row['name']).'</div>'.
    '<div class="col-s-4 col-l-6">'.$row['count'].($previous != 0 ? ' ('.($previous-$row['count']).')' : NULL).'</div>'.
    '<div class="col-s-12 col-l-0"><div class="spacer"></div></div>'.
  '</div>';
  $previous = $row['count'];
}
$content.= '<div class="spacer"></div>';

/**
 * System
 */
$content.= '<h2>'.$lang['log']['highscoreSystem']['title'].'</h2>';
$content.= '<div class="row highlight bold">'.
  '<div class="col-s-2 col-l-2">'.$lang['log']['highscoreSystem']['symbol'].'</div>'.
  '<div class="col-s-6 col-l-4">'.$lang['log']['highscoreSystem']['name'].'</div>'.
  '<div class="col-s-4 col-l-6">'.$lang['log']['highscoreSystem']['entrys'].'</div>'.
'</div>';
$result = mysqli_query($dbl, 'SELECT count(`log`.`id`) AS `count`, `userId` FROM `log` WHERE `userId` IS NULL'.(!empty($kiUserId) ? ' OR `userId`='.$kiUserId : NULL).' GROUP BY `userId` ORDER BY `count` DESC') OR DIE(MYSQLI_ERROR($dbl));$qc++;
while($row = mysqli_fetch_assoc($result)) {
  $content.= '<div class="row hover">'.
    '<div class="col-s-2 col-l-2">&#x1F5A5;</div>'.
    '<div class="col-s-6 col-l-4">'.($row['userId'] === NULL ? '<span class="italic">'.$lang['log']['highscoreSystem']['system'].'</span>' : '<span class="italic">'.$lang['log']['highscoreSystem']['ai'].'</span>').'</div>'.
    '<div class="col-s-4 col-l-6">'.$row['count'].'</div>'.
    '<div class="col-s-12 col-l-0"><div class="spacer"></div></div>'.
  '</div>';
}
$content.= '<div class="spacer"></div>';
?>
