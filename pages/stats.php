<?php
/**
 * pages/stats.php
 * 
 * Statistics
 */

/**
 * Inclusion of the cookie check.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Title and heading
 */
$title = $lang['stats']['title'];
$content.= '<h1>'.$lang['stats']['title'].'</h1>';

/**
 * Milestones and total.
 */
$milestones = [
  1000 => [],
  10000 => [],
  100000 => [],
];
$sum = 0;

/**
 * Get the item data.
 */
$result = mysqli_query($dbl, "SELECT * FROM `items` ORDER BY `itemId` ASC") OR DIE(MYSQLI_ERROR($dbl));$qc++;
while($row = mysqli_fetch_assoc($result)){
  $sum += $row['confirmedValue'];
  foreach($milestones as $key => $value) {
    if(floor($sum/$key)>count($milestones[$key])) {
      $milestones[$key][] = [
        'sum' => floor($sum/1000).'k',
        'timestamp' => $row['created'],
      ];
    }
  }
}

/**
 * Show the data.
 */
foreach($milestones as $key => $value) {
  $content.= '<h2>'.$lang['stats'][$key.'title'].'</h2>';
  $content.= '<div class="overflowXAuto"><table class="notFullWidth">';
  $content.= '<tr>
    <th>'.$lang['stats']['table']['value'].'</th>
    <th>'.$lang['stats']['table']['timestamp'].'</th>
  </tr>';
  if(empty($value)) {
    $content.= '<tr><td colspan="2">'.$lang['stats']['none'].'</td></tr>';
  } else {
    foreach($value as $val) {
      $content.= '<tr>
        <td>'.$val['sum'].'</td>
        <td>'.$val['timestamp'].'</td>
      </tr>';
    }
  }
  $content.= '</table></div>';
}

/**
 * Most frequent donation amounts
 */
$content.= '<h2>'.$lang['stats']['mostFrequentAmounts'].'</h2>';
$content.= '<div class="overflowXAuto"><table class="notFullWidth">';
$content.= '<tr>
  <th>'.$lang['stats']['table']['count'].'</th>
  <th>'.$lang['stats']['table']['value'].'</th>
</tr>';
$result = mysqli_query($dbl, 'SELECT `confirmedValue`, count(`confirmedValue`) AS `count` FROM `items` WHERE `isDonation`=1 GROUP BY `confirmedValue` HAVING `count`>=5 ORDER BY `count` DESC') OR DIE(MYSQLI_ERROR($dbl));$qc++;
if(!mysqli_num_rows($result)) {
  $content.= '<tr><td colspan="2">'.$lang['stats']['none'].'</td></tr>';
} else {
  while($row = mysqli_fetch_assoc($result)) {
    $content.= '<tr>
      <td>'.$row['count'].'x</td>
      <td>'.number_format($row['confirmedValue'], 2, ',', '.').' €</td>
    </tr>';
  }
}
$content.= '</table></div>';

/**
 * Biggest donation amounts
 */
$content.= '<h2>'.$lang['stats']['biggestAmounts'].'</h2>';
$content.= '<div class="overflowXAuto"><table class="notFullWidth">';
$content.= '<tr>
  <th>'.$lang['stats']['table']['value'].'</th>
  <th>'.$lang['stats']['table']['item'].'</th>
  <th>'.$lang['stats']['table']['author'].'</th>
</tr>';
$result = mysqli_query($dbl, 'SELECT `confirmedValue`, `itemId`, `username` FROM `items` WHERE `isDonation`=1 AND `confirmedValue`>=500 ORDER BY `confirmedValue` DESC') OR DIE(MYSQLI_ERROR($dbl));$qc++;
if(!mysqli_num_rows($result)) {
  $content.= '<tr><td colspan="3">'.$lang['stats']['none'].'</td></tr>';
} else {
  while($row = mysqli_fetch_assoc($result)) {
    $content.= '<tr>
      <td>'.number_format($row['confirmedValue'], 2, ',', '.').' €</td>
      <td><a href="https://pr0gramm.com/new/'.output($row['itemId']).'" target="_blank" rel="noopener">'.output($row['itemId']).'</a></td>
      <td><a href="https://pr0gramm.com/user/'.output($row['username']).'" target="_blank" rel="noopener">'.output($row['username']).'</a></td>
    </tr>';
  }
}
$content.= '</table></div>';
?>
