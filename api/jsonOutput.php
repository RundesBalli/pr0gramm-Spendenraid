<?php
/**
 * api/jsonOutput.php
 * 
 * Endpoint for pr0gramm administrators to retrieve the analyzed data.
 */

/**
 * Including the configuration and function loader.
 */
define('api', TRUE);
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'loader.php');

/**
 * Set output format to JSON.
 */
header("Content-Type: application/json; charset=utf-8");

/**
 * Check whether the script was called via HTTP-GET method.
 */
if($_SERVER['REQUEST_METHOD'] !== 'GET') {
  header("Content-Type: application/json; charset=utf-8");
  http_response_code(405);
  header('Access-Control-Allow-Methods: GET');
  die(
    json_encode(
      [
        'error' => 'HTTPMethod',
        'errorMsg' => 'This endpoint must be called via HTTP GET.'
      ]
    )
  );
}

/**
 * Initialize the output array.
 */
$output = [];

/**
 * Sum up everything.
 */
$result = mysqli_query($dbl, "SELECT (SELECT IFNULL(sum(`confirmedValue`), 0) FROM `items` WHERE `confirmedValue` IS NOT NULL AND `isDonation`=1) AS `confirmed`") OR DIE(MYSQLI_ERROR($dbl));
$row = mysqli_fetch_assoc($result);
$output['sums']['total'] = (double)$row['confirmed'];

/**
 * Count items.
 */
$result = mysqli_query($dbl, "SELECT (SELECT count(`id`) FROM `items`) AS `total`, (SELECT count(`id`) FROM `items` WHERE `isDonation`='1') AS `isDonation`, (SELECT count(`id`) FROM `items` WHERE `isDonation`='0') AS `isNotDonation`") OR DIE(MYSQLI_ERROR($dbl));
$row = mysqli_fetch_assoc($result);
$output['items']['total'] = (int)$row['total'];
$output['items']['isDonation'] = (int)$row['isDonation'];
$output['items']['isNotDonation'] = (int)$row['isNotDonation'];

/**
 * Tags
 */
$output['tags'] = $crawler['tags'];

/**
 * Add the sums per organization to the above outputs.
 */
$result = mysqli_query($dbl, "SELECT * FROM `metaOrganizations` ORDER BY `exportSortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
$output['sums']['organizations'] = [];
while($row = mysqli_fetch_assoc($result)) {
  $innerResult = mysqli_query($dbl, "SELECT IFNULL(sum(`confirmedValue`), 0) as `confirmedValue`, count(`id`) as `count` FROM `items` WHERE `isDonation`='1' AND `confirmedOrgaId`='".$row['id']."'") OR DIE(MYSQLI_ERROR($dbl));
  $innerRow = mysqli_fetch_assoc($innerResult);
  $output['sums']['organizations'][] = [
    'name' => $row['name'],
    'confirmedValue' => doubleval($innerRow['confirmedValue']),
    'count' => intval($innerRow['count']),
  ];
}

header("Content-type: application/json; charset=utf-8");
die(json_encode($output));
?>
