<?php
/**
 * shellScripts/crawler.php
 * 
 * Shell script to crawl the pr0gramm api for items.
 * 
 * @param string $argv[1] "full" for full scan.
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
 * Include the apiCall.
 */
require_once($apiCall);

/**
 * Crawl preparation
 */
if(!empty($argv[1]) AND $argv[1] == 'full') {
  /**
   * If the "full" parameter has been provided, a full crawl will occur.
   * 
   * A full crawl goes through all items from the configured ID and updates all entries.
   */
  $newer = $crawler['newer'];
  mysqli_query($dbl, "INSERT INTO `log` (`logLevel`, `text`) VALUES (1, '".sprintf($lang['cli']['crawler']['startFull'], $newer)."')") OR DIE(MYSQLI_ERROR($dbl));
  /**
   * Set the delFlag in every item, so possible item deletions will be recognised.
   */
  mysqli_query($dbl, "UPDATE `items` SET `delFlag`=1") OR DIE(MYSQLI_ERROR($dbl));
} else {
  /**
   * A small crawl goes through all posts from the last post ID onwards.
   */
  $result = mysqli_query($dbl, "SELECT `itemId` FROM `items` ORDER BY `itemId` DESC LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * If there are no items, then start a full crawl.
     */
    $newer = $crawler['newer'];
    mysqli_query($dbl, "INSERT INTO `log` (`logLevel`, `text`) VALUES (1, '".sprintf($lang['cli']['crawler']['startFull'], $newer)."')") OR DIE(MYSQLI_ERROR($dbl));
  } else {
    /**
     * If there are posts, start the crawl at the last existing itemId.
     */
    $row = mysqli_fetch_assoc($result);
    $newer = $row['itemId'];
    mysqli_query($dbl, "INSERT INTO `log` (`logLevel`, `text`) VALUES (1, '".sprintf($lang['cli']['crawler']['startSmall'], $newer)."')") OR DIE(MYSQLI_ERROR($dbl));
  }
}
$atStart = FALSE;

/**
 * Crawling
 */
$stats = [
  'total' => 0,
  'new' => 0,
  'updated' => 0,
];
$aiIds = [];

do {
  $response = apiCall('https://pr0gramm.com/api/items/get?tags='.urlencode($crawler['tags']).'&newer='.$newer.'&flags=31');
  if($response['atStart'] === TRUE OR $response['error'] !== NULL) {
    $atStart = TRUE;
  }
  if(empty($response['error']) OR $response['error'] === NULL) {
    foreach($response['items'] AS $item) {
      $stats['total']++;
      $result = mysqli_query($dbl, 'SELECT `itemId` FROM `items` WHERE `itemId`='.intval(defuse($item['id'])).' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_num_rows($result) === 0) {
        /**
         * Item does not exist. Creating item.
         */
        $stats['new']++;
        mysqli_query($dbl, "INSERT INTO `items` (`itemId`, `promoted`, `up`, `down`, `benis`, `created`, `image`, `thumb`, `fullsize`, `width`, `height`, `audio`, `extension`, `source`, `flags`, `username`, `mark`) VALUES ('".defuse($item['id'])."', '".($item['promoted'] ? 1 : 0)."', '".defuse($item['up'])."', '".defuse($item['down'])."', '".(defuse($item['up'])-defuse($item['down']))."', '".defuse($item['created'])."', '".defuse($item['image'])."', '".defuse($item['thumb'])."', '".defuse($item['fullsize'])."', '".defuse($item['width'])."', '".defuse($item['height'])."', '".($item['audio'] === TRUE ? 1 : 0)."', '".defuse(pathinfo($item['image'])['extension'])."', '".defuse($item['source'])."', '".defuse($item['flags'])."', '".defuse($item['user'])."', '".defuse($item['mark'])."')") OR DIE(MYSQLI_ERROR($dbl));
        if((intval($item['up'])-intval($item['down'])) >= 0) {
          /**
           * Nur Posts automatisieren, die nicht im negativen Scorebereich sind.
           */
          $ext = strtolower(pathinfo($item['image'])['extension']);
          if($ext != "mp4" AND $ext != "gif") {
            $aiIds[] = [
              'id' => $item['id'],
            ];
          }
        }
      } else {
        /**
         * Item does exist. Important values will be updated.
         */
        $stats['updated']++;
        mysqli_query($dbl, "UPDATE `items` SET `delFlag`='0', `promoted`='".($item['promoted'] ? 1 : 0)."', `up`='".defuse($item['up'])."', `down`='".defuse($item['down'])."', `benis`='".(defuse($item['up'])-defuse($item['down']))."', `flags`='".defuse($item['flags'])."', `username`='".defuse($item['user'])."', `mark`='".defuse($item['mark'])."' WHERE `itemId`='".defuse($item['id'])."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      }
      if($newer < $item['id']) {
        $newer = (int)$item['id'];
      }
    }
  }
} while($atStart === FALSE);

/**
 * Create log entry for completion.
 */
mysqli_query($dbl, 'INSERT INTO `log` (`logLevel`, `text`) VALUES (1, "'.sprintf($lang['cli']['crawler']['finished'], $stats['total'], $stats['new'], $stats['updated']).'")') OR DIE(MYSQLI_ERROR($dbl));

/**
 * Transmit the new itemIds with the image URL to the AI api.
 */
if(empty($aiSettings['apiToken']) OR empty($aiIds)) {
  /**
   * Silent die, because the AI thing is optional.
   */
  die();
}

$itemCount = count($aiIds);
mysqli_query($dbl, 'INSERT INTO `log` (`logLevel`, `text`) VALUES (1, "'.sprintf($lang['cli']['crawler']['transmitToAi'], $itemCount, ($itemCount != 1 ? 's' : '')).'")') OR DIE(MYSQLI_ERROR($dbl));

/**
 * Initialize cURL
*/
$ch = curl_init();

/**
 * Set connection options.
 * @see https://www.php.net/manual/de/function.curl-setopt.php
 */
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => TRUE,
  CURLOPT_USERAGENT => $aiSettings['cURL']['userAgent'],
  CURLOPT_INTERFACE => $aiSettings['cURL']['bindTo'],
  CURLOPT_CONNECTTIMEOUT => 5,
  CURLOPT_TIMEOUT => 10,
  CURLOPT_POST => TRUE,
  CURLOPT_POSTFIELDS => json_encode($aiIds),
  CURLOPT_HTTPHEADER => [
    'token: '.$kiApiToken,
    'Content-Type: application/json; charset=utf-8'
  ],
  CURLOPT_URL => $aiSettings['cURL']['url'],
]);

/**
 * Execute the cURL operation.
 */
$response = curl_exec($ch);

/**
 * Evaluate the HTTP and the error code.
 */
$httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

if($httpCode == 200 AND curl_errno($ch) == 0) {
  mysqli_query($dbl, 'INSERT INTO `log` (`logLevel`, `text`) VALUES (1, "'.$lang['cli']['crawler']['transmitToAiSuccessful'].'")') OR DIE(MYSQLI_ERROR($dbl));
} else {
  mysqli_query($dbl, 'INSERT INTO `log` (`logLevel`, `text`) VALUES (1, "'.sprintf($lang['cli']['crawler']['transmitToAiFailed'], defuse($response)).'")') OR DIE(MYSQLI_ERROR($dbl));
}

/**
 * Close the cURL handle.
 */
curl_close($ch);
?>
