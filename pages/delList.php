<?php
/**
 * pages/delList.php
 * 
 * Display items that have the deletion flag from the full crawl.
 */

/**
 * Inclusion of the cookie check.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Title and heading
 */
$title = $lang['delList']['title'];
$content.= '<h1>'.$lang['delList']['title'].'</h1>';

/**
 * Check whether the user has the permission to enter this site.
 */
if(!defined('perm-delList')) {
  $content.= '<div class="warnBox">'.$lang['delList']['noPermission'].'</div>';
  return;
}

/**
 * Delete item.
 */
if(isset($_POST['del']) AND !empty($_POST['itemId']) AND is_numeric($_POST['itemId'])) {
  $itemId = intval(defuse($_POST['itemId']));
  /**
   * Check whether the item exists.
   */
  $result = mysqli_query($dbl, 'SELECT `id`, `username`, `isDonation` FROM `items` WHERE `itemId`='.$itemId.' AND `delFlag`=1 LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
  if(!mysqli_num_rows($result)) {
    /**
     * The item does not exist.
     */
    $content.= '<div class="warnBox">'.$lang['delList']['notFound'].'</div>';
  } else {
    $row = mysqli_fetch_assoc($result);
    /**
     * The item does exist.
     */
    if($_POST['token'] != $sessionHash) {
      /**
       * Invalid token.
       */
      $content.= '<div class="warnBox">'.$lang['delList']['invalidToken'].'</div>';
    } else {
      /**
       * Token is correct.
       */
      mysqli_query($dbl, 'DELETE FROM `items` WHERE `itemId`='.$itemId.' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
      if($row['isDonation'] != 0) {
        mysqli_query($dbl, 'INSERT INTO `queue` (`name`, `action`) VALUES ("'.$row['username'].'", 0)') OR DIE(MYSQLI_ERROR($dbl));$qc++;
      }
      mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `text`) VALUES ("'.$userId.'", 8, "'.sprintf($lang['delList']['log'], $itemId).'")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
      $content.= '<div class="successBox">'.$lang['delList']['success'].'</div>';
    }
  }
}

/**
 * Select all items with delFlag = 1.
 */
$result = mysqli_query($dbl, 'SELECT * FROM `items` WHERE `delFlag`=1 ORDER BY `itemId` ASC') OR DIE(MYSQLI_ERROR($dbl));$qc++;
if(!mysqli_num_rows($result)) {
  $content.= '<div class="infoBox">'.$lang['delList']['noItems'].'</div>';
  return;
}

/**
 * Table heading.
 */
$content.= '<div class="overflowXAuto"><table>';
$content.= '<tr>
  <th>'.$lang['delList']['itemId'].'</th>
  <th>'.$lang['delList']['isDonation'].'</th>
  <th>'.$lang['delList']['sums'].'</th>
  <th>'.$lang['delList']['organizations'].'</th>
  <th>'.$lang['delList']['actions'].'</th>
</tr>';

/**
 * Iterate through items.
 */
while($row = mysqli_fetch_assoc($result)) {
  $content.= '<tr>
    <td><a href="https://pr0gramm.com/new/'.output($row['itemId']).'" target="_blank" rel="noopener">'.output($row['itemId']).'</a><br>(<a href="/itemInfo?itemId='.output($row['itemId']).'">'.$lang['delList']['itemInfo'].'</a>)</td>
    <td>'.$lang['delList']['isDonation'.$row['isDonation']].'</td>
    <td>'.($row['firstsightValue'] !== NULL ? number_format($row['firstsightValue'], 2, '.', ',') : '<span class="italic">NULL</span>').' €<br>'.($row['confirmedValue'] !== NULL ? number_format($row['confirmedValue'], 2, '.', ',') : '<span class="italic">NULL</span>').' €</td>
    <td>'.($row['firstsightOrgaId'] !== NULL ? $row['firstsightOrgaId'] : '<span class="italic">NULL</span>').'<br>'.($row['confirmedOrgaId'] !== NULL ? $row['confirmedOrgaId'] : '<span class="italic">NULL</span>').'</td>
    <td><form action="/delList" method="post"><input type="hidden" name="token" value="'.$sessionHash.'"><input type="hidden" name="itemId" value="'.$row['itemId'].'"><input type="submit" name="del" value="'.$lang['delList']['delete'].'"></form></td>
  </tr>';
}
$content.= '</table></div>';
?>
