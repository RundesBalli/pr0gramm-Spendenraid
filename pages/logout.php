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
$title = $lang['login']['title'];
$content.= '<h1>'.$lang['login']['title'].'</h1>';

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
  $content.= '<div class="row hover bordered">'.
    '<div class="col-s-12 col-l-3">'.$lang['login']['form']['question'].'</div>'.
    '<div class="col-s-12 col-l-4"><input type="submit" name="submit" value="'.$lang['login']['form']['submit'].'"></div>'.
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
    $content.= '<div class="warnBox">'.$lang['login']['logoutFailed']['invalidToken'].'</div>';
    $content.= '<div class="row">'.
      '<div class="col-s-12 col-l-12"><a href="/overview">'.$lang['login']['logoutFailed']['submit'].'</a></div>'.
    '</div>';
  } else {
    /**
     * Deletion of the session and the cookie. Redirecting to the login page.
     */
    mysqli_query($dbl, 'DELETE FROM `sessions` WHERE `userId`='.$userId) OR DIE(MYSQLI_ERROR($dbl));$qc++;
    mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `text`) VALUES ('.$userId.', 1, "Logout: '.$username.'")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
    setcookie(COOKIE_NAME, NULL, 0, NULL, NULL, TRUE, TRUE);
    header('Location: /');
    die();
  }
}
?>
