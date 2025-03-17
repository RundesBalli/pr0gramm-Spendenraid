<?php
/**
 * pr0gramm-Spendenraid
 * 
 * A website to evaluate posts that are found using a specific search pattern.
 * 
 * @author    RundesBalli <GitHub@RundesBalli.com>
 * @copyright 2025 RundesBalli
 * @version   2.1
 * @see       https://github.com/RundesBalli/pr0gramm-Spendenraid
 */

/**
 * Initialize the output, the default title and the queryCount
 */
$content = '';
$title = '';
$qc = 0;

/**
 * Including the configuration and function loader, the page generation elements, the router and the output generation.
 */
require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'loader.php');

/**
 * Output the generated and tidied output.
 */
echo $output;
?>
