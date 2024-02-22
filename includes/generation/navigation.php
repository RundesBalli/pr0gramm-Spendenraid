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
  $nav.= '<a href="/"'.($route == 'login' ? $a : NULL).'>'.$lang['nav']['login'].'</a>';
  $nav.= '<a href="https://RundesBalli.com" target="_blank" rel="noopener">RundesBalli</a>';
  $nav.= '<a href="https://pr0gramm.com/inbox/messages/RundesBalli" target="_blank" rel="noopener">'.$lang['nav']['contact'].'</a>';
  $nav.= '<a href="https://github.com/RundesBalli/pr0gramm-Spendenraid" target="_blank" rel="noopener">'.$lang['nav']['github'].'</a>';
} else {
  $nav.= '<a href="/overview"'.($route == 'overview' ? $a : NULL).'>'.$lang['nav']['overview'].'</a>';
  $nav.= '<a href="/evaluation"'.($route == 'evaluation' ? $a : NULL).'>'.$lang['nav']['evaluation'].'</a>';
  $nav.= '<a href="/orga"'.($route == 'orga' ? $a : NULL).'>'.$lang['nav']['orga'].'</a>';
  $nav.= '<a href="/postInfo"'.($route == 'postInfo' ? $a : NULL).'>'.$lang['nav']['postInfo'].'</a>';
  $nav.= '<a href="/log"'.($route == 'log' ? $a : NULL).'>'.$lang['nav']['log'].'</a>';
  $nav.= '<a href="/stats"'.($route == 'stats' ? $a : NULL).'>'.$lang['nav']['stats'].'</a>';
  $nav.= '<a href="/logout"'.($route == 'logout' ? $a : NULL).'>'.$lang['nav']['logout'].'</a>';
  if(defined('perm-delList') OR defined('perm-fakes') OR defined('perm-fastOrgaEvaluation')) {
    $nav.= '<br>';
  }
  if(defined('perm-delList')) {
    $nav.= '<a href="/delList"'.($route == 'delList' ? $a : NULL).'>'.$lang['nav']['delList'].'</a>';
  }
  if(defined('perm-fakes')) {
    $nav.= '<a href="/fakes"'.($route == 'fakes' ? $a : NULL).'>'.$lang['nav']['fakes'].'</a>';
  }
  if(defined('perm-fastOrgaEvaluation')) {
    $result = mysqli_query($dbl, 'SELECT `id`, `shortName` FROM `metaOrganizations` WHERE `shortName` IS NOT NULL ORDER BY `sortIndex` ASC') OR DIE(MYSQLI_ERROR($dbl));
    while($row = mysqli_fetch_assoc($result)) {
      $nav.= '<a href="/fastOrga?id='.output($row['id']).'"'.(($route == 'fastOrga' AND !empty($_GET['id']) AND $_GET['id'] == $row['id']) ? $a : NULL).'>'.output($row['shortName']).'-'.$lang['nav']['fastOrga'].'</a>';
    }
  }
}
?>
