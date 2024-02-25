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
  $result = mysqli_query($dbl, 'SELECT `id` FROM `items` WHERE `itemId`='.$itemId.' AND `delFlag`=1 LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
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
$content.= '<div class="row highlight bold">'.
  '<div class="col-s-4 col-l-3">'.$lang['delList']['itemId'].'</div>'.
  '<div class="col-s-4 col-l-3">'.$lang['delList']['sums'].'</div>'.
  '<div class="col-s-4 col-l-3">'.$lang['delList']['organizations'].'</div>'.
  '<div class="col-s-12 col-l-3">'.$lang['delList']['actions'].'</div>'.
'</div>';

/**
 * Iterate through items.
 */
while($row = mysqli_fetch_assoc($result)) {
  $content.= '<div class="row hover bordered">'.
    '<div class="col-s-4 col-l-3"><a href="https://pr0gramm.com/new/'.$row['itemId'].'" target="_blank" rel="noopener">'.$row['itemId'].'</a><br>(<a href="/itemInfo?itemId='.$row['itemId'].'">'.$lang['delList']['itemInfo'].'</a>)</div>'.
    '<div class="col-s-4 col-l-3">'.($row['firstsightValue'] !== NULL ? number_format($row['firstsightValue'], 2, '.', ',') : '<span class="italic">NULL</span>').' €<br>'.($row['confirmedValue'] !== NULL ? number_format($row['confirmedValue'], 2, '.', ',') : '<span class="italic">NULL</span>').' €</div>'.
    '<div class="col-s-4 col-l-3">'.($row['firstsightOrgaId'] !== NULL ? $row['firstsightOrgaId'] : '<span class="italic">NULL</span>').'<br>'.($row['confirmedOrgaId'] !== NULL ? $row['confirmedOrgaId'] : '<span class="italic">NULL</span>').'</div>'.
    '<div class="col-s-12 col-l-3"><form action="/delList" method="post"><input type="hidden" name="token" value="'.$sessionHash.'"><input type="hidden" name="itemId" value="'.$row['itemId'].'"><input type="submit" name="del" value="'.$lang['delList']['delete'].'"></form></div>'.
  '</div>';
}
?>
