<?php
/**
 * pages/login.php
 * 
 * Page for logging into the user area.
 */

/**
 * No cookie set or cookie empty and form not submitted.
 */
if((!isset($_COOKIE['spendenraid']) OR empty($_COOKIE['spendenraid'])) AND !isset($_POST['submit'])) {
  $content.= '<h1>'.$lang['login']['title'].'</h1>';
  /**
   * Cookiewarnung
   */
  $content.= '<div class="infoBox">'.$lang['login']['cookieNote'].'</div>';
  /**
   * Loginformular
   */
  $content.= '<form method="post">';
  $content.= '<div class="row">'.
    '<div class="col-s-12 col-l-3">'.$lang['login']['form']['name'].'</div>'.
    '<div class="col-s-12 col-l-9"><input type="text" name="username" placeholder="'.$lang['login']['form']['name'].'" autofocus></div>'.
  '</div>';
  $content.= '<div class="row">'.
    '<div class="col-s-12 col-l-3">'.$lang['login']['form']['password'].'</div>'.
    '<div class="col-s-12 col-l-9"><input type="password" name="password" placeholder="'.$lang['login']['form']['password'].'"></div>'.
  '</div>';
  $content.= '<div class="row">'.
    '<div class="col-s-12 col-l-3">'.$lang['login']['form']['submit'].'</div>'.
    '<div class="col-s-12 col-l-9"><input type="submit" name="submit" value="'.$lang['login']['form']['submit'].'"></div>'.
  '</div>';
  $content.= '</form>';
} elseif((!isset($_COOKIE['spendenraid']) OR empty($_COOKIE['spendenraid'])) AND isset($_POST['submit'])) {
  /**
   * No cookie set or cookie empty and form submitted.
   */
  $username = defuse($_POST['username']);
  /**
   * Check if the user exists.
   */
  $result = mysqli_query($dbl, 'SELECT * FROM `users` WHERE `username`="'.$username.'" AND `isBot`=0 LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));$qc++;
  if(mysqli_num_rows($result) == 1) {
    /**
     * If the user exists, the password has to be verified.
     */
    $row = mysqli_fetch_assoc($result);
    if(password_verify($_POST['password'].$row['salt'], $row['password'])) {
      /**
       * If the password could be verified, a session is generated and saved in the cookie.
       * The user will then be redirected to the overview page.
       */
      $sessionHash = hash('sha256', random_bytes(4096));
      mysqli_query($dbl, 'INSERT INTO `sessions` (`userId`, `hash`) VALUES ("'.$row['id'].'", "'.$sessionHash.'")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
      setcookie('spendenraid', $sessionHash, time()+COOKIE_DURATION);
      mysqli_query($dbl, 'INSERT INTO `log` (`userId`, `logLevel`, `text`) VALUES ("'.$row['id'].'", 1, "Login")') OR DIE(MYSQLI_ERROR($dbl));$qc++;
      header('Location: /overview');
      die();
    } else {
      /**
       * If the password could not be verified, HTTP403 is returned and an error message is displayed.
       */
      http_response_code(403);
      $content.= '<h1>'.$lang['login']['loginFailed']['title'].'</h1>';
      $content.= '<div class="warnBox">'.$lang['login']['loginFailed']['warnBox'].'</div>';
      $content.= '<div class="row">'.
        '<div class="col-s-12 col-l-12"><a href="/">'.$lang['login']['loginFailed']['tryAgain'].'</a></div>'.
      '</div>';
    }
  } else {
    /**
     * If there is no match, HTTP403 is returned and an error message is displayed.
     */
    http_response_code(403);
    $content.= '<h1>'.$lang['login']['loginFailed']['title'].'</h1>';
    $content.= '<div class="warnBox">'.$lang['login']['loginFailed']['warnBox'].'</div>';
    $content.= '<div class="row">'.
      '<div class="col-s-12 col-l-12"><a href="/">'.$lang['login']['loginFailed']['tryAgain'].'</a></div>'.
    '</div>';
  }
} else {
  /**
   * If a cookie is already set, the user will be redirected to the overview page.
   */
  header('Location: /overview');
  die();
}
?>
