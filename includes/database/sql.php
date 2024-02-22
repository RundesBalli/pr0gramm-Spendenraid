<?php
/**
 * includes/database/sql.php
 * 
 * Establishes the database connection and sets up the correct charset.
 */
$dbl = mysqli_connect($mysqlCredentials['host'], $mysqlCredentials['user'], $mysqlCredentials['pass'], $mysqlCredentials['db']) OR DIE(MYSQLI_ERROR($dbl));
mysqli_set_charset($dbl, $mysqlCredentials['charset']) OR DIE(MYSQLI_ERROR($dbl));
?>
