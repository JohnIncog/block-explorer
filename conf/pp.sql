/*
SQLyog Ultimate v9.63 
MySQL - 5.5.43-0ubuntu0.14.04.1 : Database - pp
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`pp` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `pp`;

/*Table structure for table `blocks` */

DROP TABLE IF EXISTS `blocks`;

CREATE TABLE `blocks` (
  `height` int(11) NOT NULL,
  `hash` varchar(64) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `merkleroot` varchar(64) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `nonce` int(11) DEFAULT NULL,
  `bits` varchar(16) DEFAULT NULL,
  `difficulty` float DEFAULT NULL,
  `mint` float DEFAULT NULL,
  `previousblockhash` varchar(64) DEFAULT NULL,
  `nextblockhash` varchar(64) DEFAULT NULL,
  `flags` varchar(64) DEFAULT NULL,
  `proofhash` varchar(64) DEFAULT NULL,
  `entropybit` int(1) DEFAULT NULL,
  `modifier` varchar(16) DEFAULT NULL,
  `modifierchecksum` varchar(8) DEFAULT NULL,
  `raw` text,
  `transactions` int(11) DEFAULT NULL,
  `valueout` decimal(18,8) DEFAULT NULL,
  PRIMARY KEY (`height`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `transactions` */

DROP TABLE IF EXISTS `transactions`;

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block_height` int(11) DEFAULT NULL,
  `txid` varchar(64) DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `locktime` int(11) DEFAULT NULL,
  `raw` text,
  PRIMARY KEY (`id`),
  KEY `block_height` (`block_height`),
  KEY `txid` (`txid`)
) ENGINE=InnoDB AUTO_INCREMENT=6137 DEFAULT CHARSET=latin1;

/*Table structure for table `transactions_in` */

DROP TABLE IF EXISTS `transactions_in`;

CREATE TABLE `transactions_in` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txid` varchar(64) DEFAULT NULL,
  `coinbase` varchar(32) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `vout` int(1) DEFAULT NULL,
  `asm` varchar(255) DEFAULT NULL,
  `hex` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10855 DEFAULT CHARSET=latin1;

/*Table structure for table `transactions_out` */

DROP TABLE IF EXISTS `transactions_out`;

CREATE TABLE `transactions_out` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txid` varchar(64) DEFAULT NULL,
  `value` decimal(18,8) DEFAULT NULL,
  `n` int(11) DEFAULT NULL,
  `asm` varchar(255) DEFAULT NULL,
  `hex` varchar(64) DEFAULT NULL,
  `reqSigs` varchar(64) DEFAULT NULL,
  `type` varchar(64) DEFAULT NULL,
  `address` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `txid` (`txid`)
) ENGINE=InnoDB AUTO_INCREMENT=20791 DEFAULT CHARSET=latin1;

/*Table structure for table `wallets` */

DROP TABLE IF EXISTS `wallets`;

CREATE TABLE `wallets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(32) DEFAULT NULL,
  `value` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8414 DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
