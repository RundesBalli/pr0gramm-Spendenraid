<?php
/**
 * shellScripts/delUser.php
 * 
 * Shell script to delete an user.
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
  die(sprintf($lang['cli']['delUser']['invalidUsername'], $argv[0]));
}

/**
 * Deleting the account.
 */
mysqli_query($dbl, "DELETE FROM `users` WHERE `name`='".$username."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_affected_rows($dbl) == 1) {
  mysqli_query($dbl, "INSERT INTO `log` (`logLevel`, `text`) VALUES (1, '".sprintf($lang['cli']['delUser']['log'], $username)."')") OR DIE(MYSQLI_ERROR($dbl));
  die($lang['cli']['delUser']['success']);
} else {
  die($lang['cli']['delUser']['notFound']);
}
?>
