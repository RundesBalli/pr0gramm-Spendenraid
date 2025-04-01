<?php
/**
 * shellScripts/exportForAi.php
 * 
 * Shell script to export the existing data as training data for the ai.
 */

/**
 * Including the configuration and function loader.
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'loader.php');

/**
 * Check if the script runs in the shell.
 */
if(php_sapi_name() != 'cli') {
  die($lang['error']['noCli']);
}

/**
 * Initialize variables
 */
$wget = ['mkdir ./files/'];
$itemInfo = [];

/**
 * Select all donations with organizations from the database.
 */
$result = mysqli_query($dbl, '
SELECT
  `items`.`itemId`,
  `items`.`image`,
  `items`.`extension` AS `ext`,
  `items`.`confirmedValue` AS `value`,
  `items`.`confirmedOrgaId` AS `organizationId`,
  `metaOrganizations`.`name` AS `organizationName`
FROM
  `items`
JOIN
  `metaOrganizations` ON `metaOrganizations`.`id`=`items`.`confirmedOrgaId`
WHERE
  `items`.`isDonation`=1 AND
  `items`.`extension` != "mp4" AND
  `items`.`extension` != "gif"
ORDER BY
  `items`.`itemId` ASC
') OR DIE(MYSQLI_ERROR($dbl));

/**
 * Iterate through items.
 */
while($row = mysqli_fetch_assoc($result)) {
  $filename = md5(random_bytes(4096)).'.'.$row['ext'];
  $itemInfo[] = [
    'itemId' => intval($row['itemId']),
    'value' => floatval(round($row['value'], 2)),
    'orgaId' => intval($row['organizationId']),
    'orgaName' => $row['organizationName'],
    'filename' => $filename,
  ];
  $wget[] = 'wget -nv -O ./files/'.$filename.' https://img.pr0gramm.com/'.$row['image'];
}

/**
 * Output files.
 */
$fp = fopen('itemInfo.json', 'w+');
fwrite($fp, json_encode($itemInfo));
fclose($fp);

$fp = fopen('download.sh', 'w+');
fwrite($fp, implode("\n", $wget));
fclose($fp);

chmod('download.sh', 0770);
?>
