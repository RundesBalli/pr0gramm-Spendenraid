<?php
/**
 * crawler.php
 * 
 * Cron-Crawler zum Crawlen aller ins Suchmuster fallenden Posts
 */

/**
 * Einbinden der Konfigurations- und Funktionsdatei, sowie des apiCalls.
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."config.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."functions.php");
require_once($apiCall);

/**
 * Crawlvorbereitung
 */
if(isset($argv[1]) AND $argv[1] == "full") {
  /**
   * Wenn der "full" Parameter übergeben wurde, dann starte einen vollen Crawl.
   */
  $newer = $crawler['newer'];
  mysqli_query($dbl, "INSERT INTO `log` (`loglevel`, `text`) VALUES (1, '[CRON] Crawlvorgang gestartet (groß)')") OR DIE(MYSQLI_ERROR($dbl));
  /**
   * Überall das Löschflag auf 1 setzen
   */
  mysqli_query($dbl, "UPDATE `items` SET `delflag`=1") OR DIE(MYSQLI_ERROR($dbl));
} else {
  $result = mysqli_query($dbl, "SELECT `postId` FROM `items` ORDER BY `postId` DESC LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Wenn keine Posts vorhanden sind, dann starte einen vollen Crawl.
     */
    mysqli_query($dbl, "INSERT INTO `log` (`loglevel`, `text`) VALUES (1, '[CRON] Crawlvorgang gestartet (groß)')") OR DIE(MYSQLI_ERROR($dbl));
    $newer = $crawler['newer'];
  } else {
    /**
     * Wenn Posts vorhanden sind, dann starte die Suche beim letzten Post.
     */
    mysqli_query($dbl, "INSERT INTO `log` (`loglevel`, `text`) VALUES (1, '[CRON] Crawlvorgang gestartet (klein)')") OR DIE(MYSQLI_ERROR($dbl));
    $row = mysqli_fetch_array($result);
    $newer = $row['postId'];
  }
}
$atStart = FALSE;

/**
 * Crawlvorgang
 */
$totalPosts = 0;
$newPosts = 0;
$updatedPosts = 0;
do {
  $response = apiCall('https://pr0gramm.com/api/items/get?tags='.urlencode($crawler['tags']).'&newer='.$newer.'&flags=15');
  if($response['atStart'] === TRUE OR $response['error'] !== NULL) {
    $atStart = TRUE;
  }
  if(!isset($response['error']) OR $response['error'] === NULL) {
    foreach($response['items'] AS $itemkey => $itemcontent) {
      $totalPosts++;
      $innerres = mysqli_query($dbl, "SELECT `postId` FROM `items` WHERE `postId`='".defuse($itemcontent['id'])."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_num_rows($innerres) === 0) {
        /**
         * Post nicht vorhanden -> wird angelegt
         */
        $newPosts++;
        mysqli_query($dbl, "INSERT INTO `items` (`postId`, `promoted`, `up`, `down`, `benis`, `created`, `image`, `thumb`, `fullsize`, `width`, `height`, `audio`, `extension`, `source`, `flags`, `username`, `mark`) VALUES ('".defuse($itemcontent['id'])."', '".defuse($itemcontent['promoted'])."', '".defuse($itemcontent['up'])."', '".defuse($itemcontent['down'])."', '".(defuse($itemcontent['up'])-defuse($itemcontent['down']))."', '".defuse($itemcontent['created'])."', '".defuse($itemcontent['image'])."', '".defuse($itemcontent['thumb'])."', '".defuse($itemcontent['fullsize'])."', '".defuse($itemcontent['width'])."', '".defuse($itemcontent['height'])."', '".($itemcontent['audio'] === TRUE ? 1 : 0)."', '".defuse(pathinfo($itemcontent['image'])['extension'])."', '".defuse($itemcontent['source'])."', '".defuse($itemcontent['flags'])."', '".defuse($itemcontent['user'])."', '".defuse($itemcontent['mark'])."')") OR DIE(MYSQLI_ERROR($dbl));
      } else {
        /**
         * Post ist bereits angelegt -> wichtige Felder bekommen ein Update
         */
        $updatedPosts++;
        mysqli_query($dbl, "UPDATE `items` SET `delflag`='0', `promoted`='".defuse($itemcontent['promoted'])."', `up`='".defuse($itemcontent['up'])."', `down`='".defuse($itemcontent['down'])."', `benis`='".(defuse($itemcontent['up'])-defuse($itemcontent['down']))."', `flags`='".defuse($itemcontent['flags'])."', `username`='".defuse($itemcontent['user'])."', `mark`='".defuse($itemcontent['mark'])."' WHERE `postId`='".$itemcontent['id']."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      }
      if($newer < $itemcontent['id']) {
        $newer = (int)$itemcontent['id'];
      }
    }
  }
} while($atStart === FALSE);

/**
 * Logeintrag zum Ende erzeugen
 */
mysqli_query($dbl, "INSERT INTO `log` (`loglevel`, `text`) VALUES (1, '[CRON] Crawlvorgang beendet (total: ".$totalPosts.", new: ".$newPosts.", updated: ".$updatedPosts.")')") OR DIE(MYSQLI_ERROR($dbl));
?>
