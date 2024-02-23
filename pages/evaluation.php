<?php
/**
 * pages/evaluation.php
 * 
 * Page to evaluate the items.
 */

/**
 * Inclusion of the cookie check.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Enter the donation value if the form has been submitted.
 */
if(isset($_POST['value']) AND !empty($_POST['id'])) {
  $id = (int)defuse($_POST['id']);
  /**
   * Check whether a valid number has been entered.
   * 
   * Note: Empty() cannot be used here, as '0' would return true.
   */
  if($_POST['value'] != '') {
    $value = doubleval(str_replace(',', '.', defuse($_POST['value'])));
    if(is_numeric($value)) {
      /**
       * CSRF check
       */
      if(empty($_POST['token']) OR $_POST['token'] != $sessionHash) {
        /**
         * Token invalid or not provided.
         */
        $content.= '<div class="warnBox">'.$lang['evaluation']['invalidToken'].'</div>';
      } else {
        /**
         * Token is valid. Check whether the item exists.
         */
        $result = mysqli_query($dbl, 'SELECT * FROM `items` WHERE `id`="'.$id.'" LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
        if(mysqli_num_rows($result) == 0) {
          /**
           * Wenn der Post nicht existiert, beende mit einer Fehlermeldung.
           */
          $content.= '<div class="infoBox">'.$lang['evaluation']['itemNotFound'].'</div>';
        } else {
          /**
           * The post exists.
           */
          $row = mysqli_fetch_assoc($result);
          $error = TRUE;
          $goodAct = ((strtolower($_POST['value']) === 'g' OR $_POST['value'] === '+') ? TRUE : FALSE);
          if($row['firstsightValue'] === NULL OR $row['firstsightUserId'] === NULL) {
            /**
             * First sight
             */
            $fields = [
              'query' => 'UPDATE `items` SET `firstsightValue`="'.$value.'", `firstsightUserId`="'.$userId.'", '.($goodAct ? '`firstsightOrgaId`=9, `firstsightOrgaUserId`="'.$userId.'"' : '`firstsightOrgaId`=NULL, `firstsightOrgaUserId`=NULL').' WHERE `id`="'.$id.'" LIMIT 1',
              'logLevel' => 2,
              'logText' => number_format($value, 2, ',', '.').' €',
            ];
            if($goodAct) {
              $fields['logText2'] = $lang['evaluation']['log']['goodAct'];
            }
            unset($error);
          } elseif(($row['confirmedValue'] === NULL OR $row['confirmedUserId'] === NULL) AND $row['firstsightUserId'] != $userId) {
            /**
             * Check whether the entered value is equal to the firstsight value or not.
             */
            if($row['firstsightValue'] != $value) {
              /**
               * Firstsight value and entered value are not equal.
               */
              $fields = [
                'query' => 'UPDATE `items` SET `firstsightValue`="'.$value.'", `firstsightUserId`="'.$userId.'", '.($goodAct ? '`firstsightOrgaId`=9, `firstsightOrgaUserId`="'.$userId.'"' : '`firstsightOrgaId`=NULL, `firstsightOrgaUserId`=NULL').' WHERE `id`="'.$id.'" LIMIT 1',
                'logLevel' => 3,
                'logText' => number_format($value, 2, ',', '.').' € ('.$lang['evaluation']['log']['confirmingReset'].': '.number_format($row['firstsightValue'], 2, ',', '.').' €)',
              ];
              if($row['firstsightOrgaId'] != 9 AND $goodAct) {
                $fields['logText2'] = $lang['evaluation']['log']['goodAct'];
              }
            } else {
              /**
               * Firstsight value and entered value are equal. Check whether it is a donation or not.
               */
              if($value == 0 AND !$goodAct) {
                /**
                 * It is not a donation.
                 */
                $fields = [
                  'query' => 'UPDATE `items` SET `confirmedValue`="'.$value.'", `confirmedUserId`="'.$userId.'", `isDonation`="0", `firstsightOrgaId`=NULL, `firstsightOrgaUserId`=NULL, `confirmedOrgaId`=NULL, `confirmedOrgaUserId`=NULL WHERE `id`="'.$id.'" LIMIT 1',
                  'logLevel' => 4,
                  'logText' => $lang['evaluation']['log']['noDonation'],
                ];
              } else {
                /**
                 * It is a donation, check if it is a good act or a money donation.
                 */
                $perk = TRUE;
                $fields = [
                  'query' => 'UPDATE `items` SET `confirmedValue`="'.$value.'", `confirmedUserId`="'.$userId.'", `isDonation`="'.($goodAct ? 2 : 1).'", '.($goodAct ? '`confirmedOrgaId`=9, `confirmedOrgaUserId`="'.$userId.'"' : '`confirmedOrgaId`=NULL, `confirmedOrgaUserId`=NULL').' WHERE `id`="'.$id.'" LIMIT 1',
                  'logLevel' => 4,
                  'logText' => number_format($value, 2, ',', '.').' €',
                ];
                if($goodAct) {
                  $fields['logText2'] = $lang['evaluation']['log']['goodAct'];
                }
              }
            }
            unset($error);
          }

          if(empty($error)) {
            mysqli_query($dbl, $fields['query']) OR DIE(MYSQLI_ERROR($dbl));$qc++;
            mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `itemId`, `text`) VALUES ("'.$userId.'", '.$fields['logLevel'].', "'.$id.'", "'.$fields['logText'].'")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
            if(!empty($fields['logText2'])) {
              mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `itemId`, `text`) VALUES ("'.$userId.'", '.$fields['logLevel'].', "'.$id.'", "'.$fields['logText2'].'")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
            }
            if(!empty($perk) AND $perk) {
              mysqli_query($dbl, 'INSERT INTO `queue` (`name`, `action`) VALUES ("'.$row['username'].'", 1)') OR DIE(MYSQLI_ERROR($dbl));$qc++;
            }
            $content.= '<div class="successBox">'.$lang['evaluation']['success'].'<br><a href="/resetItem?id='.$id.'">'.$lang['evaluation']['resetItem'].'</a></div>';
          }
        }
      }
    }
  }
}

/**
 * Title and heading
 */
$title = $lang['evaluation']['title'];
$content.= '<h1>'.$lang['evaluation']['title'].'</h1>';

/**
 * Select a post that has not yet been evaluated or has not yet been evaluated by the user itself.
 */
$result = mysqli_query($dbl, 'SELECT * FROM `items` WHERE `firstsightValue` IS NULL OR (`confirmedValue` IS NULL AND `firstsightUserId` != "'.$userId.'") ORDER BY RAND() LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
if(mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  if($row['firstsightValue'] !== NULL) {
    if($row['firstsightOrgaId'] == 9) {
      $content.= '<h3 class="highlight">'.$lang['evaluation']['firstsightGoodAct'].'</h3>';
    } else {
      $content.= '<h3 class="highlight">'.((!empty($aiSettings['userId']) AND $row['firstsightUserId'] == $aiSettings['userId']) ? $lang['evalutation']['aiPrefix'] : NULL).$lang['evaluation']['firstsight'].': '.number_format($row['firstsightValue'], 2, ',', '.').' €</h3>';
    }
  }
  if($row['extension'] != 'mp4') {
    /**
     * Images are displayed directly. If the Benis is less than or equal to 0, the Benis note is displayed
     * enlarged.
     */
    $content.= '<div class="row">'.
      '<div class="col-s-12 col-l-12 center"><a'.(($row['flags'] == 2 || $row['flags'] == 4) ? ' class="nsfw-blurred"' : NULL).' href="https://pr0gramm.com/new/'.$row['itemId'].'" target="_blank" rel="noopener"><img src="https://img.pr0gramm.com/'.$row['image'].'" alt="Bild" class="imgMaxHeight"></a><br><span class="info">'.$lang['evaluation']['clickImage'].'</span><br><'.($row['benis'] <= 0 ? 'h2' : 'span').' class="warn">Score: '.$row['benis'].'</'.($row['benis'] <= 0 ? 'h2' : 'span').'></div>'.
    '</div>';
  } else {
    /**
     * Videos nicht direkt anzeigen. Stattdessen den Post verlinken und das Formular nicht automatisch fokussieren.
     */
    $content.= '<div class="row">'.
      '<div class="col-s-12 col-l-12 center"><h2 class="highlight">VIDEO</h2><a href="https://pr0gramm.com/new/'.$row['itemId'].'" target="_blank" rel="noopener">'.$lang['evaluation']['video'].'</a><br><'.($row['benis'] <= 0 ? 'h2' : 'span').' class="warn">Score: '.$row['benis'].'</'.($row['benis'] <= 0 ? 'h2' : 'span').'></div>'.
    '</div>';
  }
  /**
   * Show form.
   */
  $content.= '<form action="/evaluation" id="valuation-form" method="post">';
  /**
   * ID & Token
   */
  $content.= '<input type="hidden" name="id" value="'.$row['id'].'">';
  $content.= '<input type="hidden" name="token" value="'.$sessionHash.'">';
  /**
   * NSFW Blur
   */
  if ($row['flags'] == 2 || $row['flags'] == 4){
    $content.= '<div class="row">'.
      '<div class="col-s-12 col-l-3">NSFW-Blur</div>'.
      '<div class="col-s-12 col-l-9"><input id="nsfw-blur-cb" type="checkbox" checked></div>'.
    '</div>';
  }
  /**
   * Value
   */
  $content.= '<div class="row">'.
    '<div class="col-s-12 col-l-3">'.$lang['evaluation']['value'].'</div>'.
    '<div class="col-s-12 col-l-9"><input id="value-input" name="value" type="text" autocomplete="off" placeholder="'.$lang['evaluation']['seeInfo'].'" autofocus></div>'.
  '</div>';
  /**
   * Mobile fast evaluation (visible below 600px viewport)
   */
  $content.= '<div class="row mobile-only">'.
    '<div class="col-s-12 col-l-3">'.$lang['evaluation']['fastEvaluation'].'</div>'.
    '<div class="col-s-12 col-l-9"><a href="#" class="msb-btn">G</a><a href="#" class="msb-btn">0</a><a href="#" class="msb-btn">0.01</a><a href="#" class="msb-btn">5</a><a href="#" class="msb-btn">10</a><a href="#" class="msb-btn">15</a><a href="#" class="msb-btn">20</a><a href="#" class="msb-btn">25</a><a href="#" class="msb-btn">30</a><a href="#" class="msb-btn">35</a><a href="#" class="msb-btn">40</a><a href="#" class="msb-btn">50</a><a href="#" class="msb-btn">100</a></div>'.
  '</div>';
  /**
   * Submit
   */
  $content.= '<div class="row">'.
    '<div class="col-s-12 col-l-3">'.$lang['evaluation']['submit'].'</div>'.
    '<div class="col-s-12 col-l-9"><input id="value-submit" name="value-submit" type="submit" value="'.$lang['evaluation']['submit'].'"></div>'.
  '</div>';
  $content.= '</form>';
  /**
   * ItemInfo
   */
  $content.= '<div class="row">'.
  '<div class="col-s-12 col-l-3">'.$lang['evaluation']['links'].'</div>'.
  '<div class="col-s-12 col-l-9"><a href="/itemInfo?id='.$row['id'].'">'.$lang['evaluation']['itemInfo'].'</a></div>'.
  '</div>';
  /**
   * Informations for evaluation
   */
  $content.= '<div class="row">'.
    '<div class="col-s-12 col-l-3 highlight">'.$lang['evaluation']['info'].'</div>'.
    '<div class="col-s-12 col-l-9">'.$lang['evaluation']['infoText'].'</div>'.
  '</div>';
} else {
  /**
   * Everything done.
   */
  $secondsToNextCrawl = (ceil(time()/300)*300)-time();
  $content.= '<div class="infoBox">'.sprintf($lang['evaluation']['allDone'], $secondsToNextCrawl).'<br><a href="/orga" autofocus>'.$lang['evaluation']['evaluateOrganization'].'</a></div>';
  $content.= '<meta http-equiv="refresh" content="5">';
  $content.= '<div class="spacer"></div>';
}
?>
