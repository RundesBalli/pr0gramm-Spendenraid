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
if(isset($_POST['value']) AND !empty($_POST['itemId'])) {
  $itemId = (int)defuse($_POST['itemId']);
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
        $result = mysqli_query($dbl, 'SELECT * FROM `items` WHERE `itemId`="'.$itemId.'" LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
        if(mysqli_num_rows($result) == 0) {
          /**
           * The item does not exist.
           */
          $content.= '<div class="infoBox">'.$lang['evaluation']['itemNotFound'].'</div>';
        } else {
          /**
           * The item exists.
           */
          $row = mysqli_fetch_assoc($result);
          $error = TRUE;

          /**
           * Check whether the minimum donation amount has been reached.
           */
          if($value < $minDonationValue) {
            $value = 0;
          }

          if($row['firstsightValue'] === NULL OR $row['firstsightUserId'] === NULL) {
            /**
             * Firstsight.
             */
            $fields = [
              'query' => 'UPDATE `items` SET `firstsightValue`="'.$value.'", `firstsightUserId`="'.$userId.'", `firstsightOrgaId`=NULL, `firstsightOrgaUserId`=NULL WHERE `itemId`="'.$itemId.'" LIMIT 1',
              'logLevel' => 2,
              'logText' => number_format($value, 2, ',', '.').' €',
            ];
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
                'query' => 'UPDATE `items` SET `firstsightValue`="'.$value.'", `firstsightUserId`="'.$userId.'", `firstsightOrgaId`=NULL, `firstsightOrgaUserId`=NULL WHERE `itemId`="'.$itemId.'" LIMIT 1',
                'logLevel' => 3,
                'logText' => number_format($value, 2, ',', '.').' € ('.$lang['evaluation']['log']['confirmingReset'].': '.number_format($row['firstsightValue'], 2, ',', '.').' €)',
              ];
            } else {
              /**
               * Firstsight value and entered value are equal. Check whether it is a donation or not.
               */
              if($value == 0) {
                /**
                 * It is not a donation.
                 */
                $fields = [
                  'query' => 'UPDATE `items` SET `confirmedValue`="'.$value.'", `confirmedUserId`="'.$userId.'", `isDonation`="0", `firstsightOrgaId`=NULL, `firstsightOrgaUserId`=NULL, `confirmedOrgaId`=NULL, `confirmedOrgaUserId`=NULL WHERE `itemId`="'.$itemId.'" LIMIT 1',
                  'logLevel' => 4,
                  'logText' => $lang['evaluation']['log']['noDonation'],
                ];
              } else {
                /**
                 * It is a donation.
                 */
                $perk = TRUE;
                $fields = [
                  'query' => 'UPDATE `items` SET `confirmedValue`="'.$value.'", `confirmedUserId`="'.$userId.'", `isDonation`="1" WHERE `itemId`="'.$itemId.'" LIMIT 1',
                  'logLevel' => 4,
                  'logText' => number_format($value, 2, ',', '.').' €',
                ];
              }
            }
            unset($error);
          }

          if(empty($error)) {
            mysqli_query($dbl, $fields['query']) OR DIE(MYSQLI_ERROR($dbl));$qc++;
            mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `itemId`, `text`) VALUES ("'.$userId.'", '.$fields['logLevel'].', "'.$itemId.'", "'.$fields['logText'].'")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
            if(!empty($perk) AND $perk) {
              mysqli_query($dbl, 'INSERT INTO `queue` (`name`, `action`) VALUES ("'.$row['username'].'", 1)') OR DIE(MYSQLI_ERROR($dbl));$qc++;
            }
            $content.= '<div class="successBox">'.$lang['evaluation']['success'].' - <a href="/reset?itemId='.$row['itemId'].'">'.$lang['evaluation']['resetItem'].'</a> - <a href="/itemInfo?itemId='.$row['itemId'].'">'.$lang['evaluation']['itemInfo'].'</a></div>';
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
    $content.= '<h3 class="highlight">'.((!empty($aiSettings['userId']) AND $row['firstsightUserId'] == $aiSettings['userId']) ? $lang['evaluation']['aiPrefix'] : NULL).$lang['evaluation']['firstsight'].': '.number_format($row['firstsightValue'], 2, ',', '.').' €</h3>';
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
     * Do not display videos directly. Instead, link the post and do not automatically focus the form.
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
   * itemId & Token
   */
  $content.= '<input type="hidden" name="itemId" value="'.$row['itemId'].'">';
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
    '<div class="col-s-12 col-l-9"><a href="#" class="msb-btn">0</a><a href="#" class="msb-btn">5</a><a href="#" class="msb-btn">10</a><a href="#" class="msb-btn">15</a><a href="#" class="msb-btn">20</a><a href="#" class="msb-btn">25</a><a href="#" class="msb-btn">30</a><a href="#" class="msb-btn">35</a><a href="#" class="msb-btn">40</a><a href="#" class="msb-btn">50</a><a href="#" class="msb-btn">100</a></div>'.
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
    '<div class="col-s-12 col-l-9"><a href="/itemInfo?itemId='.$row['itemId'].'">'.$lang['evaluation']['itemInfo'].'</a></div>'.
  '</div>';

  /**
   * Informations for evaluation
   */
  $content.= '<div class="row">'.
    '<div class="col-s-12 col-l-3 highlight">'.$lang['evaluation']['info'].'</div>'.
    '<div class="col-s-12 col-l-9">'.sprintf($lang['evaluation']['infoText'], number_format($minDonationValue, 2, ',', '.').' €').'</div>'.
  '</div>';
} else {
  /**
   * Everything done.
   */
  $secondsToNextCrawl = (ceil(time()/60)*60)-time();
  $title = $secondsToNextCrawl.'s - '.$lang['evaluation']['title'];
  $content.= '<div class="infoBox">'.sprintf($lang['evaluation']['allDone'], $secondsToNextCrawl).'<br><a href="/organization" autofocus>'.$lang['evaluation']['evaluateOrganizations'].'</a></div>';

  $result = mysqli_query($dbl, 'SELECT * FROM `log` WHERE `userId` IS NULL AND `text` LIKE "%CRON, Crawl%" AND `timestamp`>=DATE_SUB(NOW(), INTERVAL 10 SECOND) ORDER BY `timestamp` DESC LIMIT 5') OR DIE(MYSQLI_ERROR($dbl));
  $count = mysqli_num_rows($result);
  if($count) {
    $content.= '<h3>'.$lang['evaluation']['lastCronLog'].'</h3>';
    $content.= '<div class="overflowXAuto"><table>';
    $content.= '<tr style="border-left: 6px solid #888888;">
      <th>'.$lang['log']['log']['id'].'</td>
      <th>'.$lang['log']['log']['timestamp'].'</td>
      <th>'.$lang['log']['log']['text'].'</td>
    </tr>';

    while($row = mysqli_fetch_assoc($result)) {
      /**
       * Calculate color.
       */
      $colorRgb = hex2rgb($row['color']);
    
      /**
       * Timezone shit.
       */
      $ts = new DateTime($row['timestamp'], new DateTimeZone('UTC'));
      $ts->setTimezone(new DateTimeZone('Europe/Berlin'));
    
      /**
       * Table row
       */
      $content.= '<tr style="border-left: 6px solid #'.$row['color'].'; background-color: rgba('.$colorRgb['r'].', '.$colorRgb['g'].', '.$colorRgb['b'].', 0.04);">
        <td>'.$row['id'].'</td>
        <td class="noBreak">'.$ts->format('Y-m-d H:i:s').'</td>
        <td>'.clickableLinks(output($row['text'])).'</td>
      </tr>';
    }

    $content.= '</table></div>';
  }
  $content.= '<meta http-equiv="refresh" content="'.(($secondsToNextCrawl <= 5 OR ($secondsToNextCrawl >= 50 AND $count != 2)) ? '1' : '5').'">';
}
?>
