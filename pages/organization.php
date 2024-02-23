<?php
/**
 * pages/organization.php
 * 
 * Page to evaluate the organizations.
 */

/**
 * Inclusion of the cookie check.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Enter the organization if the form has been submitted.
 */
if(!empty($_POST['organization']) AND !empty($_POST['id'])) {
  $id = intval(defuse($_POST['id']));
  /**
   * Check whether a valid number has been entered.
   */
  if(is_numeric($_POST['organization'])) {
    $organization = intval(defuse($_POST['organization']));
    /**
     * Check if the organization exists.
     */
    $result = mysqli_query($dbl, 'SELECT `id` FROM `metaOrganizations` WHERE `id`='.$organization.' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
    if(!mysqli_num_rows($result)) {
      /**
       * Organization does not exist.
       */
      $content.= '<div class="warnBox">'.$lang['organization']['organizationNotFound'].'</div>';
    } else {
      /**
       * CSRF check
       */
      if(empty($_POST['token']) OR $_POST['token'] != $sessionHash) {
        /**
         * Token invalid or not provided.
         */
        $content.= '<div class="warnBox">'.$lang['organization']['invalidToken'].'</div>';
      } else {
        /**
         * Token is valid. Check whether the item exists.
          */
        $result = mysqli_query($dbl, 'SELECT * FROM `items` WHERE `id`='.$id.' AND `isDonation`!=0 LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
        if(mysqli_num_rows($result) == 0) {
          /**
           * The item does not exist.
           */
          $content.= '<div class="infoBox">'.$lang['organization']['itemNotFound'].'</div>';
        } else {
          /**
           * The item exists.
           */
          $row = mysqli_fetch_assoc($result);
          $error = TRUE;
          if($row['firstsightOrgaId'] === NULL OR $row['firstsightOrgaUserId'] === NULL) {
            /**
             * First sight
             */
            $fields = [
              'query' => 'UPDATE `items` SET `firstsightOrgaId`='.$organization.', `firstsightOrgaUserId`='.$userId.' WHERE `id`='.$id.' LIMIT 1',
              'logLevel' => 2,
              'logText' => $lang['organization']['log']['organization'].': '.$organization,
            ];
            unset($error);
          } elseif(($row['confirmedOrgaId'] === NULL OR $row['confirmedOrgaUserId'] === NULL) AND $row['firstsightOrgaUserId'] != $userId) {
            /**
             * Check whether the entered organization is equal to the firstsight organization or not.
             */
            if($row['firstsightOrgaId'] != $organization) {
              /**
               * Firstsight organization and entered organization are not equal.
               */
              $fields = [
                'query' => 'UPDATE `items` SET `firstsightOrgaId`='.$organization.', `firstsightOrgaUserId`='.$userId.' WHERE `id`='.$id.' LIMIT 1',
                'logLevel' => 3,
                'logText' => $lang['organization']['log']['organization'].': '.$organization.' ('.$lang['organization']['log']['confirmingReset'].': '.$row['firstsightOrgaId'].')',
              ];
            } else {
              /**
               * Firstsight organization and entered organization are equal.
               */
              $fields = [
                'query' => 'UPDATE `items` SET `confirmedOrgaId`='.$organization.', `confirmedOrgaUserId`='.$userId.' WHERE `id`='.$id.' LIMIT 1',
                'logLevel' => 4,
                'logText' => $lang['organization']['log']['organization'].': '.$organization,
              ];
            }
            unset($error);
          }

          if(empty($error)) {
            mysqli_query($dbl, $fields['query']) OR DIE(MYSQLI_ERROR($dbl));$qc++;
            mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `itemId`, `text`) VALUES ("'.$userId.'", '.$fields['logLevel'].', "'.$id.'", "'.$fields['logText'].'")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
            $content.= '<div class="successBox">'.$lang['organization']['success'].'<br><a href="/resetItem?itemId='.$row['itemId'].'">'.$lang['organization']['resetItem'].'</a><br><a href="/resetOrga?itemId='.$row['itemId'].'">'.$lang['organization']['resetOrga'].'</a></div>';
          }
        }
      }
    }
  }
}

/**
 * Title and heading
 */
$title = $lang['organization']['title'];
$content.= '<h1>'.$lang['organization']['title'].'</h1>';

/**
 * Select a post that has not yet been evaluated or has not yet been evaluated by the user itself.
 */
$result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `isDonation`='1' AND (`firstsightOrgaId` IS NULL OR (`confirmedOrgaId` IS NULL AND `firstsightOrgaUserId`!='".$userId."')) ORDER BY RAND() LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));$qc++;
if(mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  if($row['firstsightOrgaId'] !== NULL) {
    $content.= '<h3 class="highlight">'.((!empty($aiSettings['userId']) AND $row['firstsightUserId'] == $aiSettings['userId']) ? $lang['organization']['aiPrefix'] : NULL).$lang['organization']['firstsight'].': '.$row['firstsightOrgaId'].'</h3>';
  }

  /**
   * Load organizations and prepare form
   */
  $orgaResult = mysqli_query($dbl, "SELECT `id`, `name` FROM `metaOrganizations` ORDER BY `sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));$qc++;
  $orgas = [];
  while($orgaRow = mysqli_fetch_assoc($orgaResult)) {
    $orgas[] = $orgaRow['id']." - ".$orgaRow['name'];
  }
  $orgas = implode('<br>', $orgas);
  if($row['extension'] != "mp4") {
    /**
     * Images are displayed directly. If the Benis is less than or equal to 0, the Benis note is displayed
     * enlarged.
     */
    $content.= '<div class="row">'.
      '<div class="col-s-12 col-l-8 center"><a'.(($row['flags'] == 2 || $row['flags'] == 4) ? ' class="nsfw-blurred"' : NULL).' href="https://pr0gramm.com/new/'.$row['itemId'].'" target="_blank" rel="noopener"><img src="https://img.pr0gramm.com/'.$row['image'].'" alt="Bild" class="imgMaxHeight"></a><br><span class="info">'.$lang['organization']['clickImage'].'</span><br><'.($row['benis'] <= 0 ? 'h1' : 'span').' class="highlight">Score: '.$row['benis'].'</'.($row['benis'] <= 0 ? 'h1' : 'span><br').'><span class="highlight">'.$lang['organization']['confirmedValue'].': '.number_format($row['confirmedValue'], 2, ',', '.').'</span></div>'.
      '<div class="col-s-0 col-l-4 textLeft">'.$orgas.'</div>'.
    '</div>';
  } else {
    /**
     * Do not display videos directly. Instead, link the post and do not automatically focus the form.
     */
    $content.= '<div class="row">'.
      '<div class="col-s-12 col-l-8 center"><h1 class="highlight">VIDEO</h1><a href="https://pr0gramm.com/new/'.$row['itemId'].'" target="_blank" rel="noopener">'.$lang['organization']['video'].'</a><br><'.($row['benis'] <= 0 ? 'h2' : 'span').' class="highlight">Score: '.$row['benis'].'</'.($row['benis'] <= 0 ? 'h2' : 'span><br').'><span class="highlight">'.$lang['organization']['confirmedValue'].': '.number_format($row['confirmedValue'], 2, ',', '.').'</span></div>'.
      '<div class="col-s-0 col-l-4 textLeft">'.$orgas.'</div>'.
    '</div>';
  }

  /**
   * Show form.
   */
  $content.= '<form action="/organization" id="valuation-form" method="post">';

  /**
   * ID & Token
   */
  $content.= '<input type="hidden" name="id" value='.$row['id'].'>';
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
    '<div class="col-s-12 col-l-3">'.$lang['organization']['value'].'</div>'.
    '<div class="col-s-12 col-l-9"><input name="organization" id="value-input" type="text" autocomplete="off" placeholder="'.$lang['organization']['seeInfo'].'" autofocus></div>'.
  '</div>';

  /**
   * Mobile fast evaluation (visible below 600px viewport)
   */
  $content.= '<div class="row mobile-only">'.
    '<div class="col-s-12 col-l-3">'.$lang['organization']['fastEvaluation'].'</div>'.
    '<div class="col-s-12 col-l-9"><a href="#" class="msb-btn">1</a><a href="#" class="msb-btn">2</a><a href="#" class="msb-btn">3</a><a href="#" class="msb-btn">4</a><a href="#" class="msb-btn">5</a><a href="#" class="msb-btn">6</a><a href="#" class="msb-btn">7</a><a href="#" class="msb-btn">8</a><a href="#" class="msb-btn">9</a><a href="#" class="msb-btn">10</a><a href="#" class="msb-btn">11</a><a href="#" class="msb-btn">12</a><a href="#" class="msb-btn">13</a><a href="#" class="msb-btn">14</a></div>'.
  '</div>';

  /**
   * Submit
   */
  $content.= '<div class="row">'.
    '<div class="col-s-12 col-l-3">'.$lang['organization']['submit'].'</div>'.
    '<div class="col-s-12 col-l-9"><input id="value-submit" name="value-submit" type="submit" value="'.$lang['organization']['submit'].'"></div>'.
  '</div>';
  $content.= '</form>';

  /**
   * In the mobile phone view, the organisations are displayed below the input field.
   */
  $content.= '<div class="row">'.
    '<div class="col-s-12 col-l-0">'.$orgas.'</div>'.
  '</div>';

  /**
   * ItemInfo
   */
  $content.= '<div class="row">'.
    '<div class="col-s-12 col-l-3">'.$lang['organization']['links'].'</div>'.
    '<div class="col-s-12 col-l-9"><a href="/itemInfo?itemId='.$row['itemId'].'">'.$lang['organization']['itemInfo'].'</a></div>'.
  '</div>';
} else {
  /**
   * Everything done.
   */
  $content.= '<div class="infoBox">'.$lang['organization']['allDone'].'<br><a href="/evaluation" autofocus>'.$lang['organization']['evaluateItems'].'</a></div>';
}
?>
