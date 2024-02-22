<?php
/**
 * includes/routing/router.php
 * 
 * Router for the requested page.
 */

/**
 * Check if an error occoured already.
 */
if(!empty($error)) {
  require_once(PAGE_INCLUDE_DIR.'errors'.DIRECTORY_SEPARATOR.'500.php');
} else {
  /**
   * Check if a specifig page is being requested.
   */
  if(empty($_GET['p'])) {
    $page = 'login';
  } else {
    $page = $_GET['p'];
  }

  /**
   * Check if the requested page exists.
   */
  if(preg_match('/([a-z-\d]+)/i', $page, $pageMatch) === 1) {
    /**
     * Check if the requested page exist in the routes.
     */
    if(array_key_exists($pageMatch[1], $routes)) {
      /**
       * The route exists. Include the file.
       */
      $route = $pageMatch[1];
      $fileToInclude = PAGE_INCLUDE_DIR.$routes[$route];
      if(file_exists($fileToInclude)) {
        /**
         * File exists
         */
        require_once($fileToInclude);
      } else {
        /**
         * File doesn't exist.
         */
        $error = 'includeFileNotFound';
        require_once(PAGE_INCLUDE_DIR.'errors'.DIRECTORY_SEPARATOR.'500.php');
      }
    } else {
      /**
       * The requested page doesn't exist in the routes.
       */
      require_once(PAGE_INCLUDE_DIR.'errors'.DIRECTORY_SEPARATOR.'404.php');
    }
  } else {
    /**
     * No page was requested or the requested page doesn't match the pattern.
     * Redirection to the home page.
     */
    header('Location: /');
    die();
  }
}
?>
