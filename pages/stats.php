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
      $milestones[$key][] = floor($sum/1000).'k - '.$row['created'];
    }
  }
}

/**
 * Show the data.
 */
foreach($milestones as $key => $value) {
  $content.= '<h2>'.$lang['stats'][$key.'title'].'</h2>';
  $content.= '<div class="row">';
  if(empty($value)) {
    $content.= '<div class="col-s-12 col-l-12">'.$lang['stats']['none'].'</div>';
  } else {
    foreach($value as $val) {
      $content.= '<div class="col-s-12 col-l-12">'.$val.'</div>';
    }
  }
  $content.= '</div>';
  $content.= '<div class="spacer"></div>';
}

/**
 * Most frequent donation amounts
 */
$content.= '<h2>'.$lang['stats']['mostFrequentAmounts'].'</h2>';
$result = mysqli_query($dbl, 'SELECT `confirmedValue`, count(`confirmedValue`) AS `count` FROM `items` WHERE `isDonation`=1 GROUP BY `confirmedValue` HAVING `count`>=5 ORDER BY `count` DESC') OR DIE(MYSQLI_ERROR($dbl));$qc++;
$amounts = [];
while($row = mysqli_fetch_assoc($result)) {
  $amounts[] = $row['count'].'x '.number_format($row['confirmedValue'], 2, ',', '.').' €';
}
$content.= '<div class="row">';
if(empty($amounts)) {
  $content.= '<div class="col-s-12 col-l-12">'.$lang['stats']['none'].'</div>';
} else {
  foreach($amounts as $val) {
    $content.= '<div class="col-s-12 col-l-12">'.$val.'</div>';
  }
}
$content.= '</div>';
$content.= '<div class="spacer"></div>';

/**
 * Biggest donation amounts
 */
$content.= '<h2>'.$lang['stats']['biggestAmounts'].'</h2>';
$amounts = [];
$result = mysqli_query($dbl, 'SELECT `confirmedValue`, `itemId`, `username` FROM `items` WHERE `isDonation`=1 AND `confirmedValue`>=500 ORDER BY `confirmedValue` DESC') OR DIE(MYSQLI_ERROR($dbl));$qc++;
while($row = mysqli_fetch_assoc($result)) {
  $amounts[] = number_format($row['confirmedValue'], 2, ',', '.').' € - <a href="https://pr0gramm.com/new/'.$row['itemId'].'" target="_blank" rel="noopener">'.$row['itemId'].'</a> '.$lang['stats']['from'].' <a href="https://pr0gramm.com/user/'.$row['username'].'" target="_blank" rel="noopener">'.$row['username'].'</a>';
}
$content.= '<div class="row">';
if(empty($amounts)) {
  $content.= '<div class="col-s-12 col-l-12">'.$lang['stats']['none'].'</div>';
} else {
  foreach($amounts as $val) {
    $content.= '<div class="col-s-12 col-l-12">'.$val.'</div>';
  }
}
$content.= '</div>';
$content.= '<div class="spacer"></div>';
?>
