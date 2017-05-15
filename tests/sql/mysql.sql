SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `inline_content`;
CREATE TABLE `inline_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` varchar(255) NOT NULL,
  `locale` varchar(2) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`namespace`,`locale`,`name`),
  KEY `index` (`namespace`,`locale`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
