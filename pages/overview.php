<?php
/**
 * pages/overview.php
 * 
 * Overview page.
 */

/**
 * Inclusion of the cookie check.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Title
 */
$title = $lang['overview']['title'];
$content.= '<h1>'.$lang['overview']['title'].'</h1>';

/**
 * General information
 */
$content.= '<div class="row">'.
  '<div class="col-s-12 col-l-12">'.sprintf($lang['overview']['general'], $username).'</div>'.
'</div>';
$content.= '<div class="spacer"></div>';

/**
 * DKMS SMS Info
 */
$content.= '<h2 class="warn">Info zu DKMS-SMS-Spenden</h2>';
$content.= '<div class="row">';
foreach($lang['overview']['dkmsInfo'] as $value) {
  $content.= '<div class="col-s-12 col-l-12">'.$value.'</div>';
}
$content.= '</div>';
$content.= '<div class="spacer"></div>';

/**
 * Search information
 */
$content.= '<h2>'.$lang['overview']['search']['title'].'</h2>';
$content.= '<div class="row hover bordered">'.
  '<div class="col-s-12 col-l-3">'.$lang['overview']['search']['newer'].'</div>'.
  '<div class="col-s-12 col-l-9">'.$crawler['newer'].'</div>'.
'</div>';
$content.= '<div class="row hover bordered">'.
  '<div class="col-s-12 col-l-3">'.$lang['overview']['search']['tags'].'</div>'.
  '<div class="col-s-12 col-l-9 wb">'.$crawler['tags'].'</div>'.
'</div>';
$content.= '<div class="spacer"></div>';

/**
 * Total counts
 */
$content.= '<h2>'.$lang['overview']['total']['title'].'</h2>';
$result = mysqli_query($dbl, 'SELECT (SELECT count(`id`) FROM `items`) AS `total`, (SELECT count(`id`) FROM `items` WHERE `isDonation`="1") AS `isDonation`, (SELECT count(`id`) FROM `items` WHERE `isDonation`="2") AS `isGoodAct`, (SELECT count(`id`) FROM `items` WHERE `isDonation`="0") AS `isNoDonation`, (SELECT count(`id`) FROM `items` WHERE `firstsightValue` IS NULL) AS `pendingFirst`, (SELECT count(`id`) FROM `items` WHERE `firstsightValue` IS NOT NULL AND `confirmedValue` IS NULL) AS `pendingSecond`, (SELECT count(`id`) FROM `items` WHERE `isDonation`="1" AND `firstsightOrgaId` IS NULL) AS `pendingOrgaFirst`, (SELECT count(`id`) FROM `items` WHERE `isDonation`="1" AND `confirmedOrgaId` IS NULL) AS `pendingOrgaSecond`') OR DIE(MYSQLI_ERROR($dbl));$qc++;
$row = mysqli_fetch_assoc($result);
foreach($lang['overview']['total']['items'] as $key => $value) {
  $content.= '<div class="row hover bordered">'.
    '<div class="col-s-12 col-l-3">'.$lang['overview']['total']['items'][$key]['title'].'</div>'.
    '<div class="col-s-12 col-l-3">'.number_format($row[$key], 0, ',', '.').'</div>'.
    '<div class="col-s-12 col-l-6">'.$lang['overview']['total']['items'][$key]['description'].'</div>'.
  '</div>';
}
$content.= '<div class="spacer"></div>';

/**
 * Sums
 */
$content.= '<h2>'.$lang['overview']['sums']['title'].'</h2>';
$result = mysqli_query($dbl, 'SELECT (SELECT IFNULL(sum(`firstsightValue`), 0) FROM `items` WHERE `firstsightValue` IS NOT NULL) AS `unconfirmedTotalsum`, (SELECT IFNULL(sum(`confirmedValue`), 0) FROM `items` WHERE `confirmedValue` IS NOT NULL) AS `confirmedTotalsum`') OR DIE(MYSQLI_ERROR($dbl));$qc++;
$row = mysqli_fetch_assoc($result);
foreach($lang['overview']['sums']['items'] as $key => $value) {
  $content.= '<div class="row hover bordered">'.
    '<div class="col-s-12 col-l-3">'.$lang['overview']['sums']['items'][$key]['title'].'</div>'.
    '<div class="col-s-12 col-l-3">'.number_format($row[$key], 2, ',', '.').' €</div>'.
    '<div class="col-s-12 col-l-6">'.$lang['overview']['sums']['items'][$key]['description'].'</div>'.
  '</div>';
}
$content.= '<div class="spacer"></div>';

/**
 * Organizations
 */
$content.= '<h2>'.$lang['overview']['organizations']['title'].'</h2>';
$content.= '<div class="row highlight bold">'.
  '<div class="col-s-12 col-l-3">'.$lang['overview']['organizations']['name'].'</div>'.
  '<div class="col-s-12 col-l-3">'.$lang['overview']['organizations']['confirmedValue'].'</div>'.
  '<div class="col-s-12 col-l-3">'.$lang['overview']['organizations']['confirmedCount'].'</div>'.
  '<div class="col-s-12 col-l-3">'.$lang['overview']['organizations']['average'].'</div>'.
'</div>';
$result = mysqli_query($dbl, 'SELECT `metaOrganizations`.`name`, IFNULL(sum(`confirmedValue`), 0) AS `confirmedValue`, COUNT(`items`.`id`) AS `count` FROM `items` JOIN `metaOrganizations` ON `metaOrganizations`.`id`=`items`.`confirmedOrgaId` WHERE `isDonation`!="0" AND `confirmedOrgaId` IN (SELECT `id` FROM `metaOrganizations`) GROUP BY `confirmedOrgaId` ORDER BY `metaOrganizations`.`sortIndex`') OR DIE(MYSQLI_ERROR($dbl));$qc++;
while($row = mysqli_fetch_assoc($result)) {
  $content.= '<div class="row hover bordered">'.
    '<div class="col-s-12 col-l-3">'.$row['name'].'</div>'.
    '<div class="col-s-12 col-l-3">'.number_format($row['confirmedValue'], 2, ',', '.').' €</div>'.
    '<div class="col-s-12 col-l-3">'.number_format($row['count'], 0, ',', '.').'</div>'.
    '<div class="col-s-12 col-l-3">'.number_format(($row['count'] == 0 ? 0 : ($row['confirmedValue']/$row['count'])), 2, ',', '.').' €</div>'.
  '</div>';
}
$content.= '<div class="spacer"></div>';
?>
