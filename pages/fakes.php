<?php
/**
 * pages/fakes.php
 * 
 * Page to administer fakes and fake assumptions.
 */

/**
 * Inclusion of the cookie check.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Title and heading
 */
$title = $lang['fakes']['title'];
$content.= '<h1>'.$lang['fakes']['title'].'</h1>';

/**
 * Check whether the user has the permission to enter this site.
 */
if(!defined('perm-fakes')) {
  $content.= '<div class="warnBox">'.$lang['fakes']['noPermission'].'</div>';
  return;
}

/**
 * Toggle certain status or delete fake entry.
 */
if((isset($_POST['certain']) OR isset($_POST['del'])) AND !empty($_POST['id']) AND is_numeric($_POST['id'])) {
  $id = intval(defuse($_POST['id']));
  /**
   * Check whether the fake entry exists.
   */
  $result = mysqli_query($dbl, 'SELECT `certain`, `itemIdFake` FROM `fakes` WHERE `id`='.$id.' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
  if(!mysqli_num_rows($result)) {
    /**
     * The fake entry does not exist.
     */
    $content.= '<div class="warnBox">'.$lang['fakes']['notFound'].'</div>';
  } else {
    $row = mysqli_fetch_assoc($result);
    /**
     * The fake entry does exist.
     */
    if($_POST['token'] != $sessionHash) {
      /**
       * Invalid token.
       */
      $content.= '<div class="warnBox">'.$lang['fakes']['invalidToken'].'</div>';
    } else {
      /**
       * Token is correct.
       */
      if(isset($_POST['del'])) {
        /**
         * Fake entry should be deleted.
         */
        mysqli_query($dbl, 'DELETE FROM `fakes` WHERE `id`='.$id.' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
        mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `itemId`, `text`) VALUES ("'.$userId.'", 7, '.$row['itemIdFake'].', "'.$lang['fakes']['del']['log'].'")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
        $content.= '<div class="successBox">'.$lang['fakes']['del']['success'].'</div>';
      } elseif(isset($_POST['certain'])) {
        /**
         * Toggle certain.
         */
        mysqli_query($dbl, 'UPDATE `fakes` SET `certain`='.($row['certain'] == 1 ? 0 : 1).' WHERE `id`='.$id.' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
        mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `itemId`, `text`) VALUES ("'.$userId.'", 7, '.$row['itemIdFake'].', "'.$lang['fakes']['toggleCertain']['log'][($row['certain'] == 1 ? 'uncertain' : 'certain')].'")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
        $content.= '<div class="successBox">'.$lang['fakes']['toggleCertain']['success'][($row['certain'] == 1 ? 'uncertain' : 'certain')].'</div>';
      }
    }
  }
}

/**
 * Add new entry.
 */
if(isset($_POST['add']) AND (!empty($_POST['original']) AND !empty($_POST['fake']))) {
  /**
   * Get itemIds.
   */
  if(preg_match(ITEM_REGEX, defuse($_POST['original']), $matchOriginal) !== 1 OR preg_match(ITEM_REGEX, defuse($_POST['fake']), $matchFake) !== 1) {
    $content.= '<div class="warnBox">'.$lang['fakes']['add']['invalidIds'].'</div>';
  } else {
    $itemIdOriginal = intval($matchOriginal[1]);
    $itemIdFake = intval($matchFake[1]);

    /**
     * Check whether the provided itemIds are the same.
     */
    if($itemIdOriginal == $itemIdFake) {
      /**
       * itemIds are the same.
       */
      $content.= '<div class="warnBox">'.$lang['fakes']['add']['sameIds'].'</div>';
    } else {
      /**
       * itemIds are different. Check if the itemIds are in correct order.
       */
      if($itemIdOriginal > $itemIdFake) {
        $tmp = $itemIdOriginal;
        $itemIdOriginal = $itemIdFake;
        $itemIdFake = $tmp;
        unset($tmp);
      }

      /**
       * Check if the items exist.
       */
      $result = mysqli_query($dbl, 'SELECT `id` FROM `items` WHERE `itemId`='.$itemIdOriginal.' OR `itemId`='.$itemIdFake.' LIMIT 2') OR DIE(MYSQLI_ERROR($dbl));$qc++;
      if(mysqli_num_rows($result) != 2) {
        $content.= '<div class="warnBox">'.$lang['fakes']['add']['idsNotFound'].'</div>';
      } else {
        /**
         * CSRF check.
         */
        if($_POST['token'] != $sessionHash) {
          /**
           * Invalid token.
           */
          $content.= '<div class="warnBox">'.$lang['fakes']['invalidToken'].'</div>';
        } else {
          /**
           * The token is valid. Inserting the fake.
           */
          mysqli_query($dbl, 'INSERT INTO `fakes` (`itemIdOriginal`, `itemIdFake`, `certain`) VALUES ('.$itemIdOriginal.', '.$itemIdFake.', '.((isset($_POST['certain']) AND $_POST['certain'] == 1) ? 1 : 0).')');$qc++;
          if(mysqli_errno($dbl) == 1062) {
            $content.= '<div class="warnBox">'.$lang['fakes']['add']['fakeExists'].'</div>';
          } elseif(mysqli_errno($dbl) == 0) {
            mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `itemId`, `text`) VALUES ("'.$userId.'", 7, '.$itemIdFake.', "'.sprintf($lang['fakes']['add']['log']['message'], ((isset($_POST['certain']) AND $_POST['certain'] == 1) ? $lang['fakes']['add']['log']['certain'] : $lang['fakes']['add']['log']['uncertain']), $itemIdOriginal).'")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
            $content.= '<div class="successBox">'.$lang['fakes']['add']['success'].'</div>';
          } else {
            die(MYSQLI_ERROR($dbl));
          }
        }
      }
    }
  }
}

/**
 * Find fakes.
 */
$content.= '<h3>'.$lang['fakes']['findFakes']['title'].'</h3>';
$content.= '<div class="row">';
foreach(FAKE_QUERYS as $key => $val) {
  $content.= '<div class="col-s-12 col-l-4"><a href="/fakeItems?id='.$key.'">'.$lang['fakes']['findFakes']['queryTitles'][$key].'</a></div>';
}
$content.= '</div>';
$content.= '<div class="spacer"></div>';

/**
 * Form to add a new entry.
 */
$content.= '<h3>'.$lang['fakes']['addForm']['title'].'</h3>';
$content.= '<form action="/fakes" method="post">';
$content.= '<input type="hidden" name="add">';
$content.= '<input type="hidden" name="token" value="'.$sessionHash.'">';

/**
 * ItemIds
 */
$content.= '<div class="row">'.
  '<div class="col-s-12 col-l-2">'.$lang['fakes']['addForm']['items'].'</div>'.
  '<div class="col-s-12 col-l-5"><input name="original" type="text" autocomplete="off" placeholder="'.$lang['fakes']['addForm']['original'].'"></div>'.
  '<div class="col-s-12 col-l-5"><input name="fake" type="text" autocomplete="off" placeholder="'.$lang['fakes']['addForm']['fake'].'"></div>'.
'</div>';

/**
 * Certain?
 */
$content.= '<div class="row">'.
  '<div class="col-s-12 col-l-2">'.$lang['fakes']['addForm']['certain'].'</div>'.
  '<div class="col-s-12 col-l-2"><input name="certain" type="radio" autocomplete="off" value="1" id="certain-1"><label for="certain-1"> '.$lang['fakes']['addForm']['yes'].'</label></div>'.
  '<div class="col-s-12 col-l-2"><input name="certain" type="radio" autocomplete="off" value="0" id="certain-0" checked><label for="certain-0"> '.$lang['fakes']['addForm']['no'].'</label></div>'.
'</div>';

/**
 * Submit
 */
$content.= '<div class="row">'.
  '<div class="col-s-12 col-l-2">'.$lang['fakes']['addForm']['submit'].'</div>'.
  '<div class="col-s-12 col-l-10"><input name="submit" type="submit" value="'.$lang['fakes']['addForm']['submit'].'"></div>'.
'</div>';
$content.= '</form>';
$content.= '<div class="spacer"></div>';

/**
 * Display of existing fake entrys
 */
$content.= '<h3>'.$lang['fakes']['fakes']['title'].'</h3>';
$result = mysqli_query($dbl, 'SELECT `fakes`.* FROM `fakes` ORDER BY `id` ASC') OR DIE(MYSQLI_ERROR($dbl));$qc++;
if(mysqli_num_rows($result) == 0) {
  $content.= '<div class="infoBox">'.$lang['fakes']['fakes']['noEntrys'].'</div>';
} else {
  /**
   * Table heading
   */
  $content.= '<div class="row highlight bold">'.
  '<div class="col-s-2 col-l-1">'.$lang['fakes']['fakes']['id'].'</div>'.
  '<div class="col-s-5 col-l-2">'.$lang['fakes']['fakes']['original'].'</div>'.
  '<div class="col-s-5 col-l-2">'.$lang['fakes']['fakes']['fake'].'</div>'.
  '<div class="col-s-12 col-l-3">'.$lang['fakes']['fakes']['timestamp'].'</div>'.
  '<div class="col-s-12 col-l-4">'.$lang['fakes']['fakes']['actions'].'</div>'.
  '</div>';
  while($row = mysqli_fetch_assoc($result)) {
    $content.= '<div class="row hover bordered">'.
    '<div class="col-s-2 col-l-1">'.$row['id'].'</div>'.
    '<div class="col-s-5 col-l-2"><a href="https://pr0gramm.com/new/'.$row['itemIdOriginal'].'" target="_blank" rel="noopener">'.$row['itemIdOriginal'].'</a></div>'.
    '<div class="col-s-5 col-l-2"><a href="https://pr0gramm.com/new/'.$row['itemIdFake'].'" target="_blank" rel="noopener">'.$row['itemIdFake'].'</a><br><span class="smaller">('.($row['certain'] == 1 ? $lang['fakes']['fakes']['certain'] : $lang['fakes']['fakes']['uncertain']).')</span></div>'.
    '<div class="col-s-12 col-l-3">'.$row['timestamp'].'</div>'.
    '<div class="col-s-6 col-l-2"><form action="/fakes" method="post"><input type="hidden" name="token" value="'.$sessionHash.'"><input type="hidden" name="id" value="'.$row['id'].'"><input type="submit" name="certain" value="'.$lang['fakes']['fakes']['certainButton'].'"></form></div>'.
    '<div class="col-s-6 col-l-2"><form action="/fakes" method="post"><input type="hidden" name="token" value="'.$sessionHash.'"><input type="hidden" name="id" value="'.$row['id'].'"><input type="submit" name="del" value="'.$lang['fakes']['fakes']['delButton'].'"></form></div>'.
    '</div>';
  }
}
$content.= '<div class="spacer"></div>';
?>
