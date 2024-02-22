<?php
/**
 * includes/cookieCheck.php
 * 
 * Checks whether a valid cookie is set.
 */
if(isset($_COOKIE[COOKIE_NAME]) AND !empty($_COOKIE[COOKIE_NAME])) {
  /**
   * Check cookie content for validity.
   */
  $sessionHash = defuse($_COOKIE[COOKIE_NAME]);
  if(preg_match('/[a-f0-9]{64}/i', $sessionHash, $match) === 1) {
    /**
     * Query in the database whether a session with this hash exists.
     */
    $sessionHash = $match[0];
    $result = mysqli_query($dbl, "SELECT `users`.`id`, `users`.`name` FROM `sessions` JOIN `users` ON `users`.`id`=`sessions`.`userId` WHERE `hash`='".$sessionHash."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));$qc++;
    if(mysqli_num_rows($result) == 1) {
      /**
       * If a session exists, the last activity is updated and the username is loaded into the $username
       * variable. Furthermore, the navigation is changed to the logged-in version.
       */
      mysqli_query($dbl, "UPDATE `sessions` SET `lastActivity`=NOW() WHERE `hash`='".$sessionHash."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));$qc++;
      setcookie(COOKIE_NAME, $sessionHash, time()+COOKIE_DURATION, NULL, NULL, TRUE, TRUE);
      $userRow = mysqli_fetch_assoc($result);
      $username = $userRow['name'];
      $userId = $userRow['id'];
      $loggedIn = TRUE;

      /**
       * Furthermore it will be checked if the user has certain special permissions.
       */
      $permRes = mysqli_query($dbl, "SELECT `permissions`.`name` FROM `userPermissions` JOIN `permissions` ON `userPermissions`.`permissionId`=`permissions`.`id` WHERE `userPermissions`.`userId` = ".$userId) OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_num_rows($permRes) > 0) {
        while($permRow = mysqli_fetch_assoc($permRes)) {
          define('perm-'.$permRow['name'], TRUE);
        }
      }
    } else {
      /**
       * If no session with the transferred hash exists, the user is logged out by removing the cookie and
       * redirecting to the login page.
       */
      setcookie(COOKIE_NAME, NULL, 0, NULL, NULL, TRUE, TRUE);
      header("Location: /");
      die();
    }
  } else {
    /**
     * If no valid sha256 hash is provided, the user will be logged out by removing the cookie and redirecting
     * to the login page.
     */
    setcookie(COOKIE_NAME, NULL, 0, NULL, NULL, TRUE, TRUE);
    header("Location: /");
    die();
  }
} else {
  /**
   * If no cookie or an empty cookie has been transferred, the user will be redirected to the login page.
   */
  header("Location: /");
  die();
}
?>
