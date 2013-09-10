# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.1.44)
# Database: OneToOne
# Generation Time: 2013-09-10 03:12:06 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table apikeys
# ------------------------------------------------------------

DROP TABLE IF EXISTS `apikeys`;

CREATE TABLE `apikeys` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` text,
  `name` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `history`;

CREATE TABLE `history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `laptop` int(11) NOT NULL DEFAULT '0',
  `student` text NOT NULL,
  `ticket` int(11) NOT NULL DEFAULT '0',
  `action` int(11) NOT NULL,
  `data` text NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table laptops
# ------------------------------------------------------------

DROP TABLE IF EXISTS `laptops`;

CREATE TABLE `laptops` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hostname` text NOT NULL,
  `serial` text NOT NULL,
  `assetTag` int(11) NOT NULL,
  `wirelessMAC` text NOT NULL,
  `ethernetMAC` text NOT NULL,
  `building` text NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table students
# ------------------------------------------------------------

DROP TABLE IF EXISTS `students`;

CREATE TABLE `students` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sid` text NOT NULL,
  `name` text NOT NULL,
  `grade` int(11) NOT NULL,
  `laptop` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table tickets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tickets`;

CREATE TABLE `tickets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `body` text NOT NULL,
  `helper` text NOT NULL,
  `student` text NOT NULL,
  `state` int(11) NOT NULL,
  `timestamp` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
