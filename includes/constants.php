<?php
/**
 * includes/constants.php
 * 
 * Setting constants
 */

/**
 * Minimum config version
 */
const MIN_CONFIG_VERSION = 1;

/**
 * INCLUDE_DIR
 * 
 * Base include directory.
 */
const INCLUDE_DIR = __DIR__.DIRECTORY_SEPARATOR;

/**
 * PAGE_INCLUDE_DIR
 * 
 * Directory from which each subpage is included.
 */
const PAGE_INCLUDE_DIR = INCLUDE_DIR.'..'.DIRECTORY_SEPARATOR.'pages'.DIRECTORY_SEPARATOR;

/**
 * Cookie duration
 * 
 * The time period during which the cookies are valid, or the time until which they are extended.
 */
const COOKIE_DURATION = 86400*7;

/**
 * Cookie name
 * 
 * The name of the cookie.
 */
const COOKIE_NAME = 'spendenraid';

/**
 * Item regex
 * 
 * Regex to match the itemId.
 */
const ITEM_REGEX = '/(?:(?:https?:\/\/pr0gramm\.com)?\/(?:top|new|user\/\w+\/(?:uploads|likes)|stalk)(?:(?:\/\w+)?)\/)?([1-9]\d*)(?:(?::comment(?:\d+))?)?/i';

/**
 * Fake finder querys
 */
const FAKE_QUERYS = [
  [
    'query' => 'SELECT COUNT(`id`) AS `k`, `height`, `width`, `confirmedValue`, `confirmedOrgaId` FROM `items` WHERE `isDonation`="1" AND ((`extension` != "gif" AND `extension` != "mp4") AND (`confirmedOrgaId` IS NOT NULL AND (`confirmedOrgaId`!="1" AND `confirmedOrgaId`!="2"))) GROUP BY `height`, `width`, `confirmedValue`, `confirmedOrgaId` HAVING `k`>1 ORDER BY `k` DESC',
    'widthHeightSubquery' => TRUE,
  ],
  [
    'query' => 'SELECT COUNT(`id`) AS `k`, `height`, `width`, `confirmedValue`, `confirmedOrgaId` FROM `items` WHERE `isDonation`="1" AND ((`extension` != "gif" AND `extension` != "mp4") AND `confirmedOrgaId`="1") GROUP BY `height`, `width`, `confirmedValue`, `confirmedOrgaId` HAVING `k`>1 ORDER BY `k` DESC',
    'widthHeightSubquery' => TRUE,
  ],
  [
    'query' => 'SELECT COUNT(`id`) AS `k`, `height`, `width`, `confirmedValue`, `confirmedOrgaId` FROM `items` WHERE `isDonation`="1" AND ((`extension` != "gif" AND `extension` != "mp4") AND `confirmedOrgaId`="2") GROUP BY `height`, `width`, `confirmedValue`, `confirmedOrgaId` HAVING `k`>1 ORDER BY `k` DESC',
    'widthHeightSubquery' => TRUE,
  ],
  [
    'query' => 'SELECT COUNT(`id`) AS `k`, `height`, `width`, `confirmedValue`, `confirmedOrgaId` FROM `items` WHERE `isDonation`="1" AND ((`extension` != "gif" AND `extension` != "mp4") AND `confirmedOrgaId`="7") GROUP BY `height`, `width`, `confirmedValue`, `confirmedOrgaId` HAVING `k`>1 ORDER BY `k` DESC',
    'widthHeightSubquery' => TRUE,
  ],
  [
    'query' => 'SELECT COUNT(`id`) AS `k`, `height`, `width`, `confirmedValue`, `confirmedOrgaId` FROM `items` WHERE `isDonation`="1" AND ((`extension` != "gif" AND `extension` != "mp4") AND `confirmedOrgaId` IS NOT NULL GROUP BY `confirmedValue`, `confirmedOrgaId` HAVING `k`>1 AND `k`<=7 ORDER BY `k` DESC',
    'widthHeightSubquery' => FALSE,
  ],
];
?>
