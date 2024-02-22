<?php
/**
 * passwd.php
 * 
 * Shell script to change the password of a given user.
 * 
 * @param string $argv[1] Username
 */

/**
 * Including the configuration and function loader.
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'loader.php');

/**
 * Check if the script runs in the shell.
 */
if(php_sapi_name() != 'cli') {
  die($lang['error']['noCli']);
}

/**
 * Read and process the given username.
 */
if(!empty($argv[1]) AND preg_match('/^[0-9a-zA-Z]{2,32}$/', defuse($argv[1]), $match) === 1) {
  $username = $match[0];
} else {
  die($lang['cli']['passwd']['invalidUsername']);
}

/**
 * Create random strings to use as a password and salt.
 */
$pw = hash('sha256', random_bytes(4096));
$salt = hash('sha256', random_bytes(4096));
$password = password_hash($pw.$salt, PASSWORD_DEFAULT);

/**
 * Update the existing account.
 */
 mysqli_query($dbl, "UPDATE `users` SET `password`='".$password."', `salt`='".$salt."' WHERE `username`='".$username."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_affected_rows($dbl) == 1) {
  mysqli_query($dbl, "INSERT INTO `log` (`logLevel`, `text`) VALUES (1, '".sprintf($lang['cli']['passwd']['log'], $username)."')") OR DIE(MYSQLI_ERROR($dbl));
  die(sprintf($lang['cli']['passwd']['success'], $username, $pw));
} else {
  die($lang['cli']['passwd']['notFound']);
}
?>
