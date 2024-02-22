<?php
/**
 * includes/functions/defuse.php
 * 
 * Defuse function to prepare user inputs for the database.
 * 
 * @param  string $defuseString String that is to be "defused" in order to pass it into a database query.
 * @param  bool   $trim Specifies whether to remove spaces/lines at the beginning and end.
 * 
 * @return string The prepared "defused" string.
 */
function defuse($defuseString, bool $trim = TRUE) {
  if($trim === TRUE) {
    $defuseString = trim($defuseString);
  }
  global $dbl;
  return mysqli_real_escape_string($dbl, strip_tags($defuseString));
}
?>
