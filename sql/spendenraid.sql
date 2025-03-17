-- Adminer 4.8.1 MySQL 10.11.6-MariaDB-0+deb12u1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP DATABASE IF EXISTS `spendenraid_2025`;
CREATE DATABASE `spendenraid_2025` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `spendenraid_2025`;

DROP TABLE IF EXISTS `fakes`;
CREATE TABLE `fakes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `itemIdOriginal` int(10) unsigned NOT NULL COMMENT 'items.itemId',
  `itemIdFake` int(10) unsigned NOT NULL COMMENT 'items.itemId',
  `certain` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '0 = not sure; 1 = sure',
  `timestamp` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Timestamp of the entry',
  PRIMARY KEY (`id`),
  UNIQUE KEY `itemIdOriginal_itemIdFake` (`itemIdOriginal`,`itemIdFake`),
  KEY `certain` (`certain`),
  KEY `timestamp` (`timestamp`),
  KEY `itemIdFake` (`itemIdFake`),
  KEY `itemIdOriginal` (`itemIdOriginal`),
  CONSTRAINT `fakes_ibfk_4` FOREIGN KEY (`itemIdOriginal`) REFERENCES `items` (`itemId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fakes_ibfk_5` FOREIGN KEY (`itemIdFake`) REFERENCES `items` (`itemId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Suspected fakes';


DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `itemId` int(10) unsigned NOT NULL COMMENT 'Item id on pr0gramm',
  `promoted` tinyint(1) unsigned NOT NULL COMMENT '1 = /top; 0 = /new',
  `up` int(10) unsigned NOT NULL COMMENT 'Upvotes',
  `down` int(10) unsigned NOT NULL COMMENT 'Downvotes',
  `benis` int(10) NOT NULL COMMENT 'Sum of up- and downvotes',
  `created` datetime NOT NULL COMMENT 'Timestamp of the post',
  `image` varchar(255) NOT NULL COMMENT 'Image URL',
  `thumb` varchar(255) NOT NULL COMMENT 'Thumbnail URL',
  `fullsize` varchar(255) DEFAULT NULL COMMENT 'Fullsize image URL',
  `width` int(10) unsigned NOT NULL COMMENT 'Width of the image',
  `height` int(10) unsigned NOT NULL COMMENT 'Height of the image',
  `audio` tinyint(1) unsigned NOT NULL COMMENT '0 = no audio; 1 = audio',
  `extension` varchar(5) NOT NULL COMMENT 'e.g. png or jpeg',
  `flags` tinyint(2) unsigned NOT NULL COMMENT 'N/SFW/L/P / Pol',
  `username` varchar(50) NOT NULL COMMENT 'Username',
  `mark` tinyint(2) unsigned NOT NULL COMMENT 'Usermark',
  `firstsightValue` double(10,2) unsigned DEFAULT NULL COMMENT 'NULL or evaluated value',
  `firstsightUserId` int(10) unsigned DEFAULT NULL COMMENT 'NULL or users.id of the first sight user',
  `confirmedValue` double(10,2) unsigned DEFAULT NULL COMMENT 'NULL or evaluated value',
  `confirmedUserId` int(10) unsigned DEFAULT NULL COMMENT 'NULL or users.id of the confirming user',
  `isDonation` tinyint(1) unsigned DEFAULT NULL COMMENT 'Initial NULL; 0 = no donation; 1 = donation',
  `firstsightOrgaId` int(10) unsigned DEFAULT NULL COMMENT 'NULL or first sight orgas.id',
  `firstsightOrgaUserId` int(10) unsigned DEFAULT NULL COMMENT 'NULL or users.id of the first sight user',
  `confirmedOrgaId` int(10) unsigned DEFAULT NULL COMMENT 'NULL or confirmed orgas.id',
  `confirmedOrgaUserId` int(10) unsigned DEFAULT NULL COMMENT 'NULL or users.id of the confirming user',
  `delFlag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '0 = item exists on pr0gramm; 1 = item has been deleted or can no longer be found using the search pattern',
  PRIMARY KEY (`id`),
  UNIQUE KEY `itemId` (`itemId`),
  KEY `promoted` (`promoted`),
  KEY `created` (`created`),
  KEY `firstsightValue` (`firstsightValue`),
  KEY `confirmedValue` (`confirmedValue`),
  KEY `isDonation` (`isDonation`),
  KEY `delFlag` (`delFlag`),
  KEY `firstsightUserId` (`firstsightUserId`),
  KEY `confirmedUserId` (`confirmedUserId`),
  KEY `firstsightOrgaUserId` (`firstsightOrgaUserId`),
  KEY `confirmedOrgaUserId` (`confirmedOrgaUserId`),
  KEY `firstsightOrgaId` (`firstsightOrgaId`),
  KEY `confirmedOrgaId` (`confirmedOrgaId`),
  CONSTRAINT `items_ibfk_1` FOREIGN KEY (`firstsightUserId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_2` FOREIGN KEY (`confirmedUserId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_3` FOREIGN KEY (`firstsightOrgaUserId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_4` FOREIGN KEY (`confirmedOrgaUserId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_5` FOREIGN KEY (`firstsightOrgaId`) REFERENCES `metaOrganizations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_7` FOREIGN KEY (`confirmedOrgaId`) REFERENCES `metaOrganizations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Items';


DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `userId` int(10) unsigned DEFAULT NULL COMMENT 'users.id',
  `timestamp` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Timestamp of the log entry',
  `logLevel` int(10) unsigned NOT NULL COMMENT 'metaLogLevel.id',
  `itemId` int(10) unsigned DEFAULT NULL COMMENT 'items.itemId',
  `text` varchar(255) DEFAULT NULL COMMENT 'Log text',
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`),
  KEY `logLevel` (`logLevel`),
  KEY `userId` (`userId`),
  KEY `itemId` (`itemId`),
  CONSTRAINT `log_ibfk_1` FOREIGN KEY (`logLevel`) REFERENCES `metaLogLevel` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `log_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `log_ibfk_5` FOREIGN KEY (`itemId`) REFERENCES `items` (`itemId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log';


DROP TABLE IF EXISTS `metaLogLevel`;
CREATE TABLE `metaLogLevel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` varchar(50) NOT NULL COMMENT 'Log entry type',
  `color` varchar(6) NOT NULL COMMENT 'Color hex for the log entry',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log level types';

TRUNCATE `metaLogLevel`;
INSERT INTO `metaLogLevel` (`id`, `type`, `color`) VALUES
(1,	'system',	'888888'),
(2,	'firstsight',	'e108e9'),
(3,	'confirmingReset',	'ff9900'),
(4,	'confirmingOk',	'5bb91c'),
(5,	'reset',	'c52b2f'),
(6,	'perk',	'337fd2'),
(7,	'fake',	'10366f'),
(8,	'note',	'addc8d');

DROP TABLE IF EXISTS `metaOrganizations`;
CREATE TABLE `metaOrganizations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(200) NOT NULL COMMENT 'Name of the organization',
  `shortName` varchar(20) DEFAULT NULL COMMENT 'Short name of the organization (for fast evaluation navigation)',
  `sortIndex` int(10) unsigned NOT NULL DEFAULT 9999999 COMMENT 'Sort index; 0 = higher',
  `exportSortIndex` int(10) unsigned NOT NULL DEFAULT 9999999 COMMENT 'Sort index for the export; 0 = higher',
  PRIMARY KEY (`id`),
  KEY `sortIndex` (`sortIndex`),
  KEY `exportSortIndex` (`exportSortIndex`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Organizations';

TRUNCATE `metaOrganizations`;
INSERT INTO `metaOrganizations` (`id`, `name`, `shortName`, `sortIndex`, `exportSortIndex`) VALUES
(1,	'Deutsche Knochenmarkspenderdatei (DKMS)',	'DKMS',	10,	20),
(2,	'Deutsche Krebshilfe (auch dt. Kinderkrebshilfe)',	'DtKrebshilfe',	20,	10),
(3,	'Deutsches Krebsforschungszentrum (DKFZ)',	NULL,	30,	30),
(4,	'Deutsche Kinderkrebsstiftung',	NULL,	40,	40),
(5,	'Österreichische Spendenorganisationen',	NULL,	50,	50),
(6,	'Schweizer Spendenorganisationen',	NULL,	60,	60),
(7,	'diverse andere',	NULL,	110,	110),
(8,	'nicht ersichtlich',	NULL,	120,	120),
(10,	'Sonstige Depressionshilfe',	NULL,	70,	70),
(11,	'Sonstige Tier-/Naturschutzorganisationen',	NULL,	80,	80),
(12,	'Ukraine Nothilfe',	NULL,	90,	90),
(13,	'DRK ohne Ukraine',	NULL,	100,	100);

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) NOT NULL COMMENT 'Name of the permission',
  `description` varchar(250) NOT NULL COMMENT 'Description',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Special permissions';

TRUNCATE `permissions`;
INSERT INTO `permissions` (`id`, `name`, `description`) VALUES
(1,	'delList',	'Allow access to the delList and delete items'),
(2,	'fastOrgaEvaluation',	'Allow access to the fast organization evaluation'),
(3,	'fakes',	'Allow access to the fakes list and the modification of entrys'),
(4,	'showQueue',	'Permission to view the perk queue.');

DROP TABLE IF EXISTS `queue`;
CREATE TABLE `queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) NOT NULL COMMENT 'Username',
  `action` tinyint(1) unsigned NOT NULL COMMENT '1 = unlock; 0 = lock',
  `error` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '1 = error',
  PRIMARY KEY (`id`),
  KEY `error` (`error`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Queue for the perks';


DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'users.id',
  `hash` varchar(64) NOT NULL COMMENT 'Hash',
  `lastActivity` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp() COMMENT 'Time of last activity',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `hash` (`hash`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sessions';


DROP TABLE IF EXISTS `userPermissions`;
CREATE TABLE `userPermissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'users.id',
  `permissionId` int(10) unsigned NOT NULL COMMENT 'permissions.id',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `permissionId` (`permissionId`),
  CONSTRAINT `userPermissions_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `userPermissions_ibfk_2` FOREIGN KEY (`permissionId`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User special permissions';


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) NOT NULL COMMENT 'Username',
  `password` varchar(255) DEFAULT NULL COMMENT 'Hashed password',
  `salt` varchar(255) DEFAULT NULL COMMENT 'Salt',
  `bot` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '1 = bot account',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `bot` (`bot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2025-03-17 19:26:40
