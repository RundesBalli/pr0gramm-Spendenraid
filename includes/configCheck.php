<?php
/**
 * includes/configCheck.php
 * 
 * Check if the config version is sufficient for the operation of the site.
 */
if($configVersion < MIN_CONFIG_VERSION) {
  $error = 'minConfigVersion';
}
?>
