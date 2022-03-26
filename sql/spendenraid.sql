-- Adminer 4.8.1 MySQL 5.5.5-10.1.48-MariaDB-0+deb9u2 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP DATABASE IF EXISTS `spendenraid`;
CREATE DATABASE `spendenraid` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `spendenraid`;

DELIMITER ;;

CREATE EVENT `Sitzungsbereinigung` ON SCHEDULE EVERY 1 HOUR STARTS '2021-03-01 00:00:00' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Löscht abgelaufene Sitzungen nach zwei Wochen' DO DELETE FROM `sessions` WHERE `lastActivity` < DATE_SUB(NOW(), INTERVAL 2 WEEK);;

DELIMITER ;

DROP TABLE IF EXISTS `fakes`;
CREATE TABLE `fakes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `postIdOriginal` int(10) unsigned NOT NULL COMMENT 'Querverweis - items.id',
  `postIdFake` int(10) unsigned NOT NULL COMMENT 'Querverweis - items.id',
  `userId` int(10) unsigned DEFAULT NULL COMMENT 'Querverweis - users.id',
  `certain` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 = nicht ganz sicher; 1 = sicher',
  `ts` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Eintrages',
  PRIMARY KEY (`id`),
  UNIQUE KEY `postIdOriginal_postIdFake` (`postIdOriginal`,`postIdFake`),
  KEY `userId` (`userId`),
  KEY `postIdOriginal` (`postIdOriginal`),
  KEY `postIdFake` (`postIdFake`),
  CONSTRAINT `fakes_ibfk_3` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fakes_ibfk_4` FOREIGN KEY (`postIdOriginal`) REFERENCES `items` (`postId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fakes_ibfk_5` FOREIGN KEY (`postIdFake`) REFERENCES `items` (`postId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabelle mit auffälligen Posts';


DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `postId` int(10) unsigned NOT NULL COMMENT 'Post-ID (pr0gramm)',
  `promoted` tinyint(1) unsigned NOT NULL COMMENT 'Beliebt (1) oder nicht (0)',
  `up` int(10) unsigned NOT NULL COMMENT 'Upvotes',
  `down` int(10) unsigned NOT NULL COMMENT 'Downvotes',
  `benis` int(10) NOT NULL COMMENT 'Summe der Up/Downvotes',
  `created` int(10) unsigned NOT NULL COMMENT 'Timestamp des Posts',
  `image` varchar(250) CHARACTER SET utf8 NOT NULL COMMENT 'Bild-URL',
  `thumb` varchar(250) CHARACTER SET utf8 NOT NULL COMMENT 'Thumbnail-URL',
  `fullsize` varchar(250) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Fullsize-URL',
  `width` int(10) unsigned NOT NULL COMMENT 'Breite des Bildes',
  `height` int(10) unsigned NOT NULL COMMENT 'Höhe des Bildes',
  `audio` tinyint(1) unsigned NOT NULL COMMENT 'Audio',
  `extension` varchar(250) CHARACTER SET utf8 NOT NULL COMMENT 'Zum Beispiel PNG oder JPEG',
  `source` varchar(500) CHARACTER SET utf8 NOT NULL COMMENT 'Quell-URL',
  `flags` tinyint(2) unsigned NOT NULL COMMENT 'N/SFW/L/P',
  `username` varchar(250) CHARACTER SET utf8 NOT NULL COMMENT 'Username',
  `mark` tinyint(2) unsigned NOT NULL COMMENT 'Usermark',
  `firstsightValue` double(10,2) unsigned DEFAULT NULL COMMENT 'Initial NULL; sonst angenommener Wert oder NULL oder 0 bei nicht-Spende',
  `firstsightUserId` int(10) unsigned DEFAULT NULL COMMENT 'Initial NULL; sonst Querverweis UserID des Bestätigers',
  `confirmedValue` double(10,2) unsigned DEFAULT NULL COMMENT 'Initial NULL; sonst bestätigter Wert oder NULL oder 0 bei nicht-Spende',
  `confirmedUserId` int(10) unsigned DEFAULT NULL COMMENT 'Initial NULL; sonst Querverweis UserID des Bestätigers',
  `isDonation` tinyint(1) unsigned DEFAULT NULL COMMENT 'Initial NULL; 1=ist Spende; 0=ist keine Spende',
  `firstsightOrgaId` int(10) unsigned DEFAULT NULL COMMENT 'Initial NULL; sonst Querverweis angenommene Orga-ID',
  `firstsightOrgaUserId` int(10) unsigned DEFAULT NULL COMMENT 'Initial NULL; sonst Querverweis UserID des Bewertenden',
  `confirmedOrgaId` int(10) unsigned DEFAULT NULL COMMENT 'Initial NULL; sonst Querverweis bestätigte Orga-ID',
  `confirmedOrgaUserId` int(10) unsigned DEFAULT NULL COMMENT 'Initial NULL; sonst Querverweis UserID des Bestätigenden',
  `delflag` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0=Post ist noch vorhanden, 1=Post wurde gelöscht',
  PRIMARY KEY (`id`),
  UNIQUE KEY `postId` (`postId`),
  KEY `created` (`created`),
  KEY `flags` (`flags`),
  KEY `username` (`username`),
  KEY `firstsightValue` (`firstsightValue`),
  KEY `firstsightUserId` (`firstsightUserId`),
  KEY `confirmedValue` (`confirmedValue`),
  KEY `confirmedUserId` (`confirmedUserId`),
  KEY `isDonation` (`isDonation`),
  KEY `firstsightOrgaId` (`firstsightOrgaId`),
  KEY `firstsightOrgaUserId` (`firstsightOrgaUserId`),
  KEY `confirmedOrgaId` (`confirmedOrgaId`),
  KEY `confirmedOrgaUserId` (`confirmedOrgaUserId`),
  KEY `delflag` (`delflag`),
  CONSTRAINT `items_ibfk_1` FOREIGN KEY (`firstsightUserId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_2` FOREIGN KEY (`confirmedUserId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_3` FOREIGN KEY (`firstsightOrgaUserId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_4` FOREIGN KEY (`confirmedOrgaUserId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_5` FOREIGN KEY (`firstsightOrgaId`) REFERENCES `orgas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_6` FOREIGN KEY (`confirmedOrgaId`) REFERENCES `orgas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `userId` int(10) unsigned DEFAULT NULL COMMENT 'Querverweis - users.id',
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Eintrags',
  `logLevel` int(10) unsigned NOT NULL COMMENT 'Querverweis - logLevel.id',
  `postId` int(10) unsigned DEFAULT NULL COMMENT 'Querverweis - items.postId, oder NULL bei User-/Systemaktion',
  `text` text COLLATE utf8mb4_unicode_ci COMMENT 'Logtext (optional)',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `logLevel` (`logLevel`),
  KEY `itemId` (`postId`),
  CONSTRAINT `log_ibfk_2` FOREIGN KEY (`logLevel`) REFERENCES `logLevel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_ibfk_5` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `log_ibfk_6` FOREIGN KEY (`postId`) REFERENCES `items` (`postId`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `logLevel`;
CREATE TABLE `logLevel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Meldungsart',
  `color` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'HexCode der Meldungsfarbe',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Querverweistabelle - Loglevel Farben';

INSERT INTO `logLevel` (`id`, `title`, `color`) VALUES
(1,	'User-/Systemaktion',	'888888'),
(2,	'Erstsichtung',	'e108e9'),
(3,	'Zweitsichtung - zurückgesetzt',	'ff9900'),
(4,	'Zweitsichtung - okay',	'5bb91c'),
(5,	'Post zurückgesetzt',	'c52b2f'),
(6,	'Perk',	'337fd2'),
(7,	'Fakes',	'10366f');

DROP TABLE IF EXISTS `orgas`;
CREATE TABLE `orgas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `organame` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name der Organisation',
  `sortIndex` int(10) unsigned NOT NULL COMMENT 'Sortierindex',
  `exportCountOnly` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 = normaler Export, 1 = nur Anzahl exportieren',
  `exportSortIndex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Sortierindex für jsonOutput.php',
  PRIMARY KEY (`id`),
  KEY `sortIndex` (`sortIndex`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Querverweistabelle - Organisationen';

INSERT INTO `orgas` (`id`, `organame`, `sortIndex`, `exportCountOnly`, `exportSortIndex`) VALUES
(1,	'Deutsche Knochenmarkspenderdatei (DKMS)',	10,	0,	20),
(2,	'Deutsche Krebshilfe (auch dt. Kinderkrebshilfe)',	20,	0,	10),
(3,	'Deutsches Krebsforschungszentrum (DKFZ)',	30,	0,	30),
(4,	'Deutsche Kinderkrebsstiftung',	40,	0,	40),
(5,	'Österreichische Spendenorganisationen',	50,	0,	50),
(6,	'Schweizer Spendenorganisationen',	60,	0,	60),
(7,	'diverse andere',	70,	0,	70),
(8,	'nicht ersichtlich',	80,	0,	80),
(9,	'Gute Tat',	90,	1,	90),
(10,	'Sonstige Depressionshilfe',	65,	0,	65),
(11,	'Sonstige Tier-/Naturschutzorganisationen',	67,	0,	67),
(12,	'Ukraine Nothilfe',	68,	0,	68);

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'Querverweis users.id',
  `hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Sitzungshash',
  `lastActivity` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Aktivität',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `hash` (`hash`),
  KEY `lastActivity` (`lastActivity`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `username` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Username',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Passworthash',
  `salt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Passwortsalt',
  `isBot` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 = der Account ist ein Bot',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `isBot` (`isBot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usertabelle';

TRUNCATE `users`;
INSERT INTO `users` (`id`, `username`, `password`, `salt`, `isBot`) VALUES
(1,	'KI',	NULL,	NULL,	1);

-- 2022-03-26 22:09:00
