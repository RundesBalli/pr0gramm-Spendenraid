<?php
/**
 * pages/fakeFinder.php
 * 
 * Page to find fakes.
 */

/**
 * Inclusion of the cookie check.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Title and heading
 */
$title = $lang['fakes']['fakeFinder']['title'];
$content.= '<h1>'.$lang['fakes']['fakeFinder']['title'].'</h1>';

/**
 * Check whether the user has the permission to enter this site.
 */
if(!defined('perm-fakes')) {
  $content.= '<div class="warnBox">'.$lang['fakes']['noPermission'].'</div>';
  return;
}

/**
 * Check if a valid query ID has been provided.
 */
if(!isset($_GET['id']) OR !array_key_exists($_GET['id'], FAKE_QUERYS)) {
  $content.= '<div class="warnBox">'.$lang['fakes']['fakeFinder']['invalidQueryId'].'</div>';
  return;
}

/**
 * Show which query will be executed.
 */
$query = intval(trim($_GET['id']));
$content.= '<h2>'.$lang['fakes']['fakeFinder']['queryTitles'][$query].'</h2>';

/**
 * Execute Query.
 */
$result = mysqli_query($dbl, FAKE_QUERYS[$query]['query']) OR DIE(MYSQLI_ERROR($dbl));$qc++;

/**
 * Iterate through results.
 */
while($row = mysqli_fetch_assoc($result)) {
  /**
   * Subquery to search for items with the previously grouped search groups.
   * Also check if the width and height should be added.
   */
  $subquery = 'SELECT * FROM `items` WHERE '.(FAKE_QUERYS[$query]['widthHeightSubquery'] ? '`height`="'.$row['height'].'" AND `width`="'.$row['width'].'" AND ' : NULL).'`confirmedValue`="'.$row['confirmedValue'].'" AND `confirmedOrgaId`="'.$row['confirmedOrgaId'].'" ORDER BY `itemId` ASC';
  $content.= '<h3 style="font-family: monospace;" class="highlight">'.$subquery.'</h3>';
  $innerres = mysqli_query($dbl, $subquery) OR DIE(MYSQLI_ERROR($dbl));$qc++;
  $content.= '<div class="row">';
    $content.= '<div class="col-s-12 col-l-12">';
    while($innerrow = mysqli_fetch_assoc($innerres)) {
      $content.= '<a href="https://pr0gramm.com/new/'.$innerrow['itemId'].'" target="_blank" rel="noopener"><img src="https://img.pr0gramm.com/'.$innerrow['image'].'" alt="Bild" class="imgMaxHeight" style="margin: 5px;"></a>';
    }
    $content.= '</div>';
  $content.= '</div>';
  $content.= '<div class="spacer"></div>';
  $content.= '<hr>';
}
?>
