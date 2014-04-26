CREATE TABLE `todo` (
  `ID` bigint(64) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(500) NOT NULL,
  `completed` tinyint(1) DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
