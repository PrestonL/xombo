sessionhandler	CREATE TABLE `sessionhandler` (
	`ID` bigint(64) unsigned NOT NULL AUTO_INCREMENT,
	`savePath` varchar(100) NOT NULL,
	`name` varchar(100) NOT NULL,
	`sessionId` varchar(100) NOT NULL,
	`data` longtext NOT NULL,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`ID`),
	KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
