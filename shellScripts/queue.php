<?php
/**
 * shellScripts/queue.php
 * 
 * Shell script to send the lock/unlock data and sums to pr0gramm.
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
 * Check whether a perkSecret is configured.
 */
if(empty($perkSecret)) {
  die($lang['cli']['queue']['noPerkSecret']);
}

/**
 * Check if there are any users to lock or unlock.
 */
$result = mysqli_query($dbl, 'SELECT * FROM `queue` WHERE `error`=0 ORDER BY `id` ASC') OR DIE(MYSQLI_ERROR($dbl));

/**
 * Exit if nothing is in the queue.
 */
if(!mysqli_num_rows($result)) {
  /**
   * Silent die.
   */
  die();
}

/**
 * Include the apiCall.
 */
require_once($apiCall);

/**
 * Iterate through queue.
 */
while($row = mysqli_fetch_assoc($result)) {
  /**
   * Get sums and counts.
   */
  $innerResult = mysqli_query($dbl, 'SELECT * FROM `items` WHERE `username`="'.defuse($row['name']).'"') OR DIE(MYSQLI_ERROR($dbl));
  $confirmedValue = 0;
  while($innerRow = mysqli_fetch_assoc($innerResult)) {
    $confirmedValue += $innerRow['confirmedValue'];
  }

  /**
   * Check whether the user should be unlocked or locked.
   */
  if($row['action']) {
    /**
     * User should be unlocked, so it is necessary to check if the unlocking is really allowed.
     */
    if($confirmedValue > 0) {
      /**
       * Unlock the user.
       */
      $response = apiCall('https://pr0gramm.com/api/casino/unlockUser', ['secret' => $perkSecret, 'name' => $row['name'], 'confirmedValue' => floatval($confirmedValue)]);
      if($response['success'] == TRUE) {
        /**
         * All right. Remove the entry from the queue.
         */
        mysqli_query($dbl, 'DELETE FROM `queue` WHERE `name`="'.$row['name'].'"') OR DIE(MYSQLI_ERROR($dbl));
        mysqli_query($dbl, 'INSERT INTO `log` (`logLevel`, `text`) VALUES (6, "'.sprintf($lang['cli']['queue']['unlock']['success'], defuse($row['name'])).'")') OR DIE(MYSQLI_ERROR($dbl));
      } else {
        /**
         * Something went wrong. Set the error status in the queue entry.
         */
        mysqli_query($dbl, 'UPDATE `queue` SET `error`=1 WHERE `id`='.$row['id'].' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));
        mysqli_query($dbl, 'INSERT INTO `log` (`logLevel`, `text`) VALUES (6, "'.sprintf($lang['cli']['queue']['unlock']['failure'], defuse($row['name'])).'")') OR DIE(MYSQLI_ERROR($dbl));
      }
    } else {
      /**
       * The user has no donations. Lock the user again, just in case.
       */
      mysqli_query($dbl, 'UPDATE `queue` SET `error`=1 WHERE `id`='.$row['id'].' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));
      mysqli_query($dbl, 'INSERT INTO `log` (`logLevel`, `text`) VALUES (6, "'.sprintf($lang['cli']['queue']['unknownError'], defuse($row['name']), $confirmedValue).'")') OR DIE(MYSQLI_ERROR($dbl));
    }
  } else {
    /**
     * User should be locked. If the user has any donations, the user will not be locked and the
     * queue will silently continue.
     */
    if($confirmedValue == 0) {
      /**
       * The user has no donations, so the locking will take place.
       */
      $response = apiCall('https://pr0gramm.com/api/casino/lockUser', ['secret' => $perkSecret, 'name' => $row['name'], 'confirmedValue' => floatval(0)]);
      if($response['success'] == TRUE) {
        /**
         * All right. Remove the entry from the queue.
         */
        mysqli_query($dbl, 'DELETE FROM `queue` WHERE `name`="'.$row['name'].'"') OR DIE(MYSQLI_ERROR($dbl));
        mysqli_query($dbl, 'INSERT INTO `log` (`logLevel`, `text`) VALUES (6, "'.sprintf($lang['cli']['queue']['lock']['success'], defuse($row['name'])).'")') OR DIE(MYSQLI_ERROR($dbl));
      } else {
        /**
         * Something went wrong. Set the error status in the queue entry.
         */
        mysqli_query($dbl, 'UPDATE `queue` SET `error`=1 WHERE `id`='.$row['id'].' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));
        mysqli_query($dbl, 'INSERT INTO `log` (`logLevel`, `text`) VALUES (6, "'.sprintf($lang['cli']['queue']['lock']['failure'], defuse($row['name'])).'")') OR DIE(MYSQLI_ERROR($dbl));
      }
    } else {
      /**
       * The user has any donations, so no locking is necessary.
       */
      mysqli_query($dbl, 'DELETE FROM `queue` WHERE `name`="'.$row['name'].'" AND `action`=0') OR DIE(MYSQLI_ERROR($dbl));
    }
  }
  usleep(300000);
}
?>
