<?php
/**
 * includes/functions/hex2rgb.php
 */

/**
 * hex2rgb function
 * 
 * Calculates a hex code (3 or 6 digit) to RGB.
 * 
 * @author    RundesBalli <webspam@rundesballi.com>
 * @copyright 2020 RundesBalli
 * @version   1.0
 * @license   MIT-License
 * @see       https://gist.github.com/RundesBalli/32f5491df25abb7fe0864e6447a26b75
 * @see       https://www.php.net/manual/en/function.hexdec.php#99478
 * @see       https://stackoverflow.com/questions/1636350/how-to-identify-a-given-string-is-hex-color-format/1637260#1637260
 * 
 * @param string $hex The hex string to be calculated.
 * 
 * @return array or boolean The RGB array or false.
 */
function hex2rgb($hex) {
  if(preg_match("/^(?:(?:[0-9a-f]{2}){3}|(?:[0-9a-f]){3})$/i", preg_replace("/[^0-9a-f]/i", "", $hex), $result) === 1) {
    if(strlen($result[0]) == 6) {
      return array(
        "r" => hexdec(substr($result[0], 0, 2)),
        "g" => hexdec(substr($result[0], 2, 2)),
        "b" => hexdec(substr($result[0], 4, 2)),
        "hex" => $result[0]
      );
    } elseif(strlen($result[0]) == 3) {
      return array(
        "r" => hexdec(str_repeat(substr($result[0], 0, 1), 2)),
        "g" => hexdec(str_repeat(substr($result[0], 1, 1), 2)),
        "b" => hexdec(str_repeat(substr($result[0], 2, 1), 2)),
        "hex" => $result[0]
      );
    } else  {
      return FALSE;
    }
  } else {
    return FALSE;
  }
}
?>
