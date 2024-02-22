<?php
/**
 * shellScripts/addUser.php
 * 
 * Shell script to add a new user.
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
  die($lang['cli']['addUser']['invalidUsername']);
}

/**
 * Create random strings to use as a password and salt.
 */
$pw = hash('sha256', random_bytes(4096));
$salt = hash('sha256', random_bytes(4096));
$password = password_hash($pw.$salt, PASSWORD_DEFAULT);

/**
 * Create the new user.
 */
if(mysqli_query($dbl, "INSERT INTO `users` (`name`, `password`, `salt`) VALUES ('".$username."', '".$password."', '".$salt."')")) {
  mysqli_query($dbl, "INSERT INTO `log` (`logLevel`, `text`) VALUES (1, '".sprintf($lang['cli']['addUser']['log'], $username)."')") OR DIE(MYSQLI_ERROR($dbl));
  die(sprintf($lang['cli']['addUser']['success'], $username, $pw));
} elseif(mysqli_errno($dbl) == 1062) {
  die($lang['cli']['addUser']['duplicate']);
} else {
  die(sprintf($lang['cli']['addUser']['unknownError'], mysqli_error($dbl)));
}
?>
