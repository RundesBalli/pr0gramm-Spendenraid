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
$content.= '<p>'.sprintf($lang['overview']['general'], $username).'</p>';

/**
 * DKMS SMS Info
 */
$content.= '<h2 class="warn">Info zu DKMS-SMS-Spenden</h2>';
foreach($lang['overview']['dkmsInfo'] as $value) {
  $content.= '<p>'.$value.'</p>';
}

/**
 * Search information
 */
$content.= '<h2>'.$lang['overview']['search']['title'].'</h2>';
$content.= '<div class="overflowXAuto"><table>';
$content.= '<tr>
  <th>'.$lang['overview']['search']['table']['param'].'</th>
  <th>'.$lang['overview']['search']['table']['value'].'</th>
</tr>';
$content.= '<tr>
  <td>'.$lang['overview']['search']['newer'].'</td>
  <td>'.$crawler['newer'].'</td>
</tr>';
$content.= '<tr>
  <td>'.$lang['overview']['search']['tags'].'</td>
  <td class="wb">'.$crawler['tags'].'</td>
</tr>';
$content.= '</table></div>';

/**
 * Total counts
 */
$content.= '<h2>'.$lang['overview']['total']['title'].'</h2>';
$result = mysqli_query($dbl, 'SELECT (SELECT count(`id`) FROM `items`) AS `total`, (SELECT count(`id`) FROM `items` WHERE `isDonation`="1") AS `isDonation`, (SELECT count(`id`) FROM `items` WHERE `isDonation`="0") AS `isNoDonation`, (SELECT count(`id`) FROM `items` WHERE `firstsightValue` IS NULL) AS `pendingFirst`, (SELECT count(`id`) FROM `items` WHERE `firstsightValue` IS NOT NULL AND `confirmedValue` IS NULL) AS `pendingSecond`, (SELECT count(`id`) FROM `items` WHERE `isDonation`="1" AND `firstsightOrgaId` IS NULL) AS `pendingOrgaFirst`, (SELECT count(`id`) FROM `items` WHERE `isDonation`="1" AND `confirmedOrgaId` IS NULL) AS `pendingOrgaSecond`') OR DIE(MYSQLI_ERROR($dbl));$qc++;
$row = mysqli_fetch_assoc($result);
$content.= '<div class="overflowXAuto"><table>';
$content.= '<tr>
  <th>'.$lang['overview']['total']['table']['title'].'</th>
  <th>'.$lang['overview']['total']['table']['count'].'</th>
  <th>'.$lang['overview']['total']['table']['description'].'</th>
</tr>';
foreach($lang['overview']['total']['items'] as $key => $value) {
  $content.= '<tr>
    <td>'.$lang['overview']['total']['items'][$key]['title'].'</td>
    <td class="noBreak">'.number_format($row[$key], 0, ',', '.').'</td>
    <td>'.$lang['overview']['total']['items'][$key]['description'].'</td>
  </tr>';
}
$content.= '</table></div>';

/**
 * Sums
 */
$content.= '<h2>'.$lang['overview']['sums']['title'].'</h2>';
$result = mysqli_query($dbl, 'SELECT (SELECT IFNULL(sum(`firstsightValue`), 0) FROM `items` WHERE `firstsightValue` IS NOT NULL) AS `unconfirmedTotalsum`, (SELECT IFNULL(sum(`confirmedValue`), 0) FROM `items` WHERE `confirmedValue` IS NOT NULL) AS `confirmedTotalsum`') OR DIE(MYSQLI_ERROR($dbl));$qc++;
$row = mysqli_fetch_assoc($result);
$content.= '<div class="overflowXAuto"><table>';
$content.= '<tr>
  <th>'.$lang['overview']['sums']['table']['title'].'</th>
  <th>'.$lang['overview']['sums']['table']['count'].'</th>
  <th>'.$lang['overview']['sums']['table']['description'].'</th>
</tr>';
foreach($lang['overview']['sums']['items'] as $key => $value) {
  $content.= '<tr>
    <td>'.$lang['overview']['sums']['items'][$key]['title'].'</td>
    <td class="noBreak">'.number_format($row[$key], 2, ',', '.').' €</td>
    <td>'.$lang['overview']['sums']['items'][$key]['description'].'</td>
  </tr>';
}
$content.= '</table></div>';

/**
 * Organizations
 */
$content.= '<h2>'.$lang['overview']['organizations']['title'].'</h2>';
$content.= '<div class="overflowXAuto"><table>';
$content.= '<tr>
  <th>'.$lang['overview']['organizations']['table']['name'].'</th>
  <th>'.$lang['overview']['organizations']['table']['confirmedValue'].'</th>
  <th>'.$lang['overview']['organizations']['table']['confirmedCount'].'</th>
  <th>'.$lang['overview']['organizations']['table']['average'].'</th>
</tr>';
$result = mysqli_query($dbl, 'SELECT `metaOrganizations`.`name`, IFNULL(sum(`confirmedValue`), 0) AS `confirmedValue`, COUNT(`items`.`id`) AS `count` FROM `items` JOIN `metaOrganizations` ON `metaOrganizations`.`id`=`items`.`confirmedOrgaId` WHERE `isDonation`!="0" AND `confirmedOrgaId` IN (SELECT `id` FROM `metaOrganizations`) GROUP BY `confirmedOrgaId` ORDER BY `metaOrganizations`.`sortIndex`') OR DIE(MYSQLI_ERROR($dbl));$qc++;
while($row = mysqli_fetch_assoc($result)) {
  $content.= '<tr>
    <td>'.$row['name'].'</td>
    <td class="noBreak">'.number_format($row['confirmedValue'], 2, ',', '.').' €</td>
    <td class="noBreak">'.number_format($row['count'], 0, ',', '.').'</td>
    <td class="noBreak">'.number_format(($row['count'] == 0 ? 0 : ($row['confirmedValue']/$row['count'])), 2, ',', '.').' €</td>
  </tr>';
}
$content.= '</table></div>';
?>
