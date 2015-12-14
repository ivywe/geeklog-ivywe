<?php
// 2011/04/08 tsuchitani AT ivywe DOT co DOT jp
// Last Update 20110415

//XML 定義
$_SQL[] = "
DROP TABLE IF EXISTS `{$_TABLES['DATABOX_def_xml']}`
";

$_SQL[] = "
CREATE TABLE IF NOT EXISTS {$_TABLES['DATABOX_def_xml']} (
  `seq` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(64) NOT NULL,
  `value` varchar(64) DEFAULT NULL,
  `field` varchar(50) NOT NULL,
  `udatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uuid` mediumint(8) NOT NULL,
  PRIMARY KEY (`seq`)
) TYPE=MyISAM  ;
";


?>
