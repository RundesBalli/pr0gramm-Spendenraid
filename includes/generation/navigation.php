<?php
/**
 * includes/generation/navigation.php
 * 
 * Navigation generation
 */
$a = ' class="active"';
$nav = '<a id="toggleElement"></a>';
$nav.= '<a href="/" class="title">pr0gramm-Spendenraid</a>';

if(empty($loggedIn)) {
  /**
   * Navigation elements when not logged in.
   */
  $nav.= '<a href="/"'.((!empty($route) AND $route == 'login') ? $a : NULL).'>'.$lang['nav']['login'].'</a>';
  $nav.= '<a href="https://RundesBalli.com" target="_blank" rel="noopener">RundesBalli</a>';
  $nav.= '<a href="https://pr0gramm.com/inbox/messages/RundesBalli" target="_blank" rel="noopener">'.$lang['nav']['contact'].'</a>';
  $nav.= '<a href="https://github.com/RundesBalli/pr0gramm-Spendenraid" target="_blank" rel="noopener">'.$lang['nav']['github'].'</a>';
} else {
  /**
   * Display of the item count which can be evaluated by the user.
   */
  // Evaluation
  $result = mysqli_query($dbl, "SELECT count(`id`) AS `c` FROM `items` WHERE `firstsightValue` IS NULL OR (`confirmedValue` IS NULL AND `firstsightUserId` != '".$userId."')") OR DIE(MYSQLI_ERROR($dbl));$qc++;
  $row = mysqli_fetch_assoc($result);
  $valCount = $row['c'];
  // Organizations
  $result = mysqli_query($dbl, "SELECT count(`id`) AS `c` FROM `items` WHERE `isDonation`='1' AND (`firstsightOrgaId` IS NULL OR (`confirmedOrgaId` IS NULL AND `firstsightOrgaUserId`!='".$userId."'))") OR DIE(MYSQLI_ERROR($dbl));$qc++;
  $row = mysqli_fetch_assoc($result);
  $orgaCount = $row['c'];

  /**
   * Navigation Elements when logged in.
   */
  $nav.= '<a href="/overview"'.((!empty($route) AND $route == 'overview') ? $a : NULL).'>'.$lang['nav']['overview'].'</a>';
  $nav.= '<a href="/evaluation"'.((!empty($route) AND $route == 'evaluation') ? $a : NULL).'>'.$lang['nav']['evaluation'].(!empty($valCount) ? " (".$valCount.")" : NULL).'</a>';
  $nav.= '<a href="/organization"'.((!empty($route) AND $route == 'organization') ? $a : NULL).'>'.$lang['nav']['organization'].(!empty($orgaCount) ? " (".$orgaCount.")" : NULL).'</a>';
  $nav.= '<a href="/itemInfo"'.((!empty($route) AND $route == 'itemInfo') ? $a : NULL).'>'.$lang['nav']['itemInfo'].'</a>';
  $nav.= '<a href="/log"'.((!empty($route) AND $route == 'log') ? $a : NULL).'>'.$lang['nav']['log'].'</a>';
  $nav.= '<a href="/stats"'.((!empty($route) AND $route == 'stats') ? $a : NULL).'>'.$lang['nav']['stats'].'</a>';
  $nav.= '<a href="/logout"'.((!empty($route) AND $route == 'logout') ? $a : NULL).'>'.$lang['nav']['logout'].'</a>';
  if(defined('perm-delList')) {
    $nav.= '<a href="/delList"'.((!empty($route) AND $route == 'delList') ? $a : NULL).'>'.$lang['nav']['delList'].'</a>';
  }
  if(defined('perm-fakes')) {
    $nav.= '<a href="/fakes"'.((!empty($route) AND $route == 'fakes') ? $a : NULL).'>'.$lang['nav']['fakes'].'</a>';
  }
  if(defined('perm-fastOrgaEvaluation')) {
    $result = mysqli_query($dbl, 'SELECT `id`, `shortName` FROM `metaOrganizations` WHERE `shortName` IS NOT NULL ORDER BY `sortIndex` ASC') OR DIE(MYSQLI_ERROR($dbl));$qc++;
    while($row = mysqli_fetch_assoc($result)) {
      $nav.= '<a href="/fastOrga?id='.output($row['id']).'"'.(((!empty($route) AND $route == 'fastOrga') AND !empty($_GET['id']) AND $_GET['id'] == $row['id']) ? $a : NULL).'>'.output($row['shortName']).'-'.$lang['nav']['fastOrga'].'</a>';
    }
  }
  if(defined('perm-showQueue')) {
    $result = mysqli_query($dbl, 'SELECT `id` FROM `queue` WHERE `error`=1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
    $queueCount = mysqli_num_rows($result);
    $nav.= '<a href="/queue"'.((!empty($route) AND $route == 'queue') ? $a : NULL).'>'.$lang['nav']['queue'].($queueCount > 0 ? ' <span class="warn bold">('.$queueCount.')</span>' : NULL).'</a>';
  }
}
?>
