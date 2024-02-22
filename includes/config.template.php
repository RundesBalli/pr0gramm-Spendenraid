<?php
/**
 * includes/config.php
 * 
 * Configuration file
 */

/**
 * MySQL-Credentials
 * 
 * @var array
 *   @var string host    MySQL connection host
 *   @var string user    Username for the MySQL connection
 *   @var string pass    Password for the MySQL connection
 *   @var string db      Database on the SQL server in which to work.
 *   @var string charset Charset of the connection. Default: utf8
 */
$mysqlCredentials = [
  'host' => 'localhost',
  'user' => '',
  'pass' => '',
  'db' => '',
  'charset' => 'utf8'
];

/**
 * Crawler settings.
 * 
 * @var array
 *   @var int    newer Item ID from which the crawler fetches the items.
 *   @var string tags  SearchQuery for pr0gramm.
 */
$crawler = [
  'newer' => 0,
  'tags' => '',
];

/**
 * Destination of the apiCall
 * Download: https://github.com/RundesBalli/pr0gramm-apiCall
 * Will be included if necessary.
 * 
 * @var string
 */
$apiCall = '/path/to/apiCall.php';

/**
 * Secret token to unlock the perk and set the badge to the user.
 * 
 * @var string
 */
$perkSecret = '';

/**
 * Settings for the AI api.
 * 
 * @var array
 *   @var string apiToken      Token for transmitting the itemIds to the AI API.
 *   @var string editPostToken Token for the AI to transmit the post data to the editPost API.
 *   @var int    userId        userId of the AI.
 *   @var array  cURL          cURL settings.
 *     @var string bindTo      Interface with which cURL should establish the connection, e.g. eth0.
 *     @var string userAgent   The UserAgent with which the request is to be sent.
 *     @var string url         The AI URL to be called.
 */
$aiSettings = [
  'apiToken' => '',
  'editPostToken' => '',
  'userId' => 1,
  'cURL' => [
    'bindTo' => '',
    'userAgent' => '',
    'url' => '',
  ],
];


/**
 * 
 * DO NOT CHANGE ANYTHING BELOW THIS LINE, EVEN IF YOU KNOW WHAT YOU ARE DOING!
 * 
 */

/**
 * Config version
 * 
 * @var int
 */
$configVersion = 1;
?>
