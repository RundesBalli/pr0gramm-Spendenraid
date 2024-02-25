<?php
/**
 * includes/routing/routes.php
 * 
 * Routes
 * 
 * @var array
 */
$routes = [
  /**
   * Pages
   */
  'login' => 'login.php',
  'logout' => 'logout.php',
  'overview' => 'overview.php',
  'evaluation' => 'evaluation.php',
  'organization' => 'organization.php',
  'itemInfo' => 'itemInfo.php',
  'log' => 'log.php',
  'stats' => 'stats.php',
  'reset' => 'reset.php',
  'fakes' => 'fakes.php',

  /**
   * Error pages
   */
  '404' => 'errors'.DIRECTORY_SEPARATOR.'404.php',
  '403' => 'errors'.DIRECTORY_SEPARATOR.'403.php',
];
?>
