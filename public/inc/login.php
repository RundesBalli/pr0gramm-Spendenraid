<?php
/**
 * login.php
 * 
 * Seite zum Einloggen in den Nutzerbereich.
 */
$title = "Login";

/**
 * Kein Cookie gesetzt oder Cookie leer und Formular nicht übergeben.
 */
if((!isset($_COOKIE['spendenraid']) OR empty($_COOKIE['spendenraid'])) AND !isset($_POST['submit'])) {
  $content.= "<h1>Login</h1>".PHP_EOL;
  /**
   * Cookiewarnung
   */
  $content.= "<div class='infobox'>Ab diesem Punkt werden Cookies verwendet! Mit dem Fortfahren stimmst du dem zu!</div>".PHP_EOL;
  /**
   * Loginformular
   */
  $content.= "<form action='/login' method='post'>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>Name</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-8 col-l-9 col-xl-9'><input type='text' name='username' placeholder='Name' autofocus></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>Passwort</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-8 col-l-9 col-xl-9'><input type='password' name='password' placeholder='Passwort'></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'>Einloggen</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-8 col-l-9 col-xl-9'><input type='submit' name='submit' value='Einloggen'></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "</form>".PHP_EOL;
} elseif((!isset($_COOKIE['spendenraid']) OR empty($_COOKIE['spendenraid'])) AND isset($_POST['submit'])) {
  /**
   * Kein Cookie gesetzt oder Cookie leer und Formular wurde übergeben.
   */
  /**
   * Entschärfen der Usereingaben.
   */
  $username = defuse($_POST['username']);
  /**
   * Abfragen ob eine Übereinstimmung in der Datenbank vorliegt.
   */
  $result = mysqli_query($dbl, "SELECT * FROM `users` WHERE `username`='".$username."' AND `isBot`=0 LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 1) {
    /**
     * Wenn der User existiert, muss der Passworthash validiert werden.
     */
    $row = mysqli_fetch_array($result);
    if(password_verify($_POST['password'].$row['salt'], $row['password'])) {
      /**
       * Wenn das Passwort verifiziert werden konnte wird eine Sitzung generiert und im Cookie gespeichert.
       * Danach erfolg eine Weiterleitung zur Übersichts-Seite.
       */
      $sessionhash = hash('sha256', random_bytes(4096));
      mysqli_query($dbl, "INSERT INTO `sessions` (`userId`, `hash`) VALUES ('".$row['id']."', '".$sessionhash."')") OR DIE(MYSQLI_ERROR($dbl));
      setcookie('spendenraid', $sessionhash, time()+(6*7*86400));
      mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `text`) VALUES ('".$row['id']."', 1, 'Login: ".$username."')") OR DIE(MYSQLI_ERROR($dbl));
      header("Location: /overview");
      die();
    } else {
      /**
       * Wenn das Passwort nicht verifiziert werden konnte wird HTTP403 zurückgegeben und eine Fehlermeldung wird ausgegeben.
       */
      http_response_code(403);
      $content.= "<h1>Login gescheitert</h1>".PHP_EOL;
      $content.= "<div class='warnbox'>Die Zugangsdaten sind falsch.</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/login'>Erneut versuchen</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  } else {
    /**
     * Wenn keine Übereinstimmung vorliegt, dann wird HTTP403 zurückgegeben und eine Fehlermeldung wird ausgegeben.
     */
    http_response_code(403);
    $content.= "<h1>Login gescheitert</h1>".PHP_EOL;
    $content.= "<div class='warnbox'>Die Zugangsdaten sind falsch.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/login'>Erneut versuchen</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
  }
} else {
  /**
   * Wenn bereits ein Cookie gesetzt ist wird auf die Übersichts-Seite weitergeleitet.
   */
  header("Location: /overview");
  die();
}
?>
