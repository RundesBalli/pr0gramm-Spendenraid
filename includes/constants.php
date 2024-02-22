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
?>
