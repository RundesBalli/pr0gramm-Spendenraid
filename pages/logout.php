<?php
/**
 * pages/logout.php
 * 
 * Page for logging out the user.
 */

/**
 * Inclusion of the cookie check.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Title
 */
$title = $lang['logout']['title'];
$content.= '<h1>'.$lang['logout']['title'].'</h1>';

/**
 * Check if the form has already been submitted.
 */
if(!isset($_POST['submit'])) {
  /**
   * Form will be displayed.
   */
  $content.= '<form action="/logout" method="post">';
  /**
   * Session hash.
   */
  $content.= '<input type="hidden" name="token" value="'.$sessionHash.'">';
  /**
   * Choice.
   */
  $content.= '<div class="row">'.
    '<div class="col-s-12 col-l-12">'.$lang['logout']['form']['question'].'</div>'.
    '<div class="col-s-12 col-l-12"><input type="submit" name="submit" value="'.$lang['logout']['form']['submit'].'"></div>'.
  '</div>';
  $content.= '</form>';
} else {
  /**
   * Form has been submitted.
   */
  /**
   * Check if the session hash is correct.
   */
  if($_POST['token'] != $sessionHash) {
    http_response_code(403);
    $content.= '<div class="warnBox">'.$lang['logout']['logoutFailed']['invalidToken'].'</div>';
    $content.= '<div class="row">'.
      '<div class="col-s-12 col-l-12"><a href="/overview">'.$lang['logout']['logoutFailed']['submit'].'</a></div>'.
    '</div>';
  } else {
    /**
     * Deletion of the session and the cookie. Redirecting to the login page.
     */
    mysqli_query($dbl, 'DELETE FROM `sessions` WHERE `userId`='.$userId) OR DIE(MYSQLI_ERROR($dbl));$qc++;
    mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `text`) VALUES ('.$userId.', 1, "Logout")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
    setcookie(COOKIE_NAME, NULL, 0, NULL, NULL, TRUE, TRUE);
    header('Location: /');
    die();
  }
}
?>
