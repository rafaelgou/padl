# CocoaMySQL dump
# Version 0.5
# http://cocoamysql.sourceforge.net
#
# Host: localhost (MySQL 4.0.21 Complete MySQL by Server Logistics)
# Database: audit
# Generation Time: 2005-06-20 23:03:50 +0100
# ************************************************************

# Dump of table licenses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `licenses`;

CREATE TABLE `licenses` (
  `LICENSE_ID` int(11) NOT NULL auto_increment,
  `USER_ID` int(11) default NULL,
  `FLAGGED` tinyint(1) default NULL,
  `STATUS` varchar(255) default NULL,
  `START_DATE` int(11) default NULL,
  `DATE_SPAN` int(11) default NULL,
  `EXPIRY_DATE` int(11) default NULL,
  `NOTES` text,
  `COST` int(11) default NULL,
  `PAID` tinyint(1) default NULL,
  `REQUEST_KEY` text,
  `TEMP_KEY` text,
  `LICENSE_KEY` text,
  `PAYMENT_NEXT_DUE` int(11) default NULL,
  `DATA` text,
  `MAC` varchar(255) default NULL,
  `PATH` text,
  `IP` int(11) default NULL,
  PRIMARY KEY  (`LICENSE_ID`)
) TYPE=MyISAM;



# Dump of table remote_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `remote_log`;

CREATE TABLE `remote_log` (
  `ID` int(11) NOT NULL auto_increment,
  `FLAGGED` tinyint(1) default NULL,
  `TIMESTAMP` int(20) NOT NULL default '0',
  `LICENSE_ID` int(10) NOT NULL default '0',
  `MESSAGE` varchar(255) NOT NULL default '',
  `SERVER` text NOT NULL,
  `IP` varchar(22) NOT NULL default '',
  `NOTES` text,
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `ID` int(11) NOT NULL auto_increment,
  `FLAGGED` tinyint(1) default NULL,
  `USERNAME` varchar(255) default NULL,
  `EMAIL` varchar(255) default NULL,
  `QUESTION` text,
  `ANSWER` varchar(255) default NULL,
  `FIRSTNAME` varchar(255) default NULL,
  `LASTNAME` varchar(255) default NULL,
  `COMPANY` varchar(255) default NULL,
  `ADDRESS_1` varchar(255) default NULL,
  `ADDRESS_2` varchar(255) default NULL,
  `ADDRESS_3` varchar(255) default NULL,
  `COUNTY` varchar(255) default NULL,
  `POSTCODE` varchar(255) default NULL,
  `COUNTRY` varchar(255) default NULL,
  `NOTES` text,
  `RESELLER` tinyint(1) default NULL,
  `STATUS` varchar(255) default NULL,
  `HISTORY` text,
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;
