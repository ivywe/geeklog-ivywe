<?php
// 2014/05/08 tsuchitani AT ivywe DOT co DOT jp

//CSV
$_SQL[] = "
DROP TABLE IF EXISTS `{$_TABLES['DATABOX_def_csv']}`
";

$_SQL[] = "
CREATE TABLE IF NOT EXISTS {$_TABLES['DATABOX_def_csv']} (
  `seq` int(11) NOT NULL AUTO_INCREMENT,
  `csvheader` varchar(64) NOT NULL,
  `value` varchar(64) DEFAULT NULL,
  `field` varchar(50) NOT NULL,
  `udatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uuid` mediumint(8) NOT NULL,
  PRIMARY KEY (`seq`)
) TYPE=MyISAM  ;
";

$_SQL[] = "
DROP TABLE IF EXISTS `{$_TABLES['DATABOX_def_csv_sel']}`
";
$_SQL[] = "
CREATE TABLE {$_TABLES['DATABOX_def_csv_sel']} (
  `csv_sel_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` mediumtext,
  `udatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uuid` mediumint(8) NOT NULL,
  PRIMARY KEY (`csv_sel_id`)
) ENGINE=MyISAM
";


$_SQL[] = "
DROP TABLE IF EXISTS `{$_TABLES['DATABOX_def_csv_sel_dtl']}`
";
$_SQL[] = "
CREATE TABLE {$_TABLES['DATABOX_def_csv_sel_dtl']} (
  `seq` int(11) NOT NULL AUTO_INCREMENT,
  `csv_sel_id` int(11) NOT NULL,
  `csvheader` varchar(64) NOT NULL,
  `value` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`seq`)
) ENGINE=MyISAM
";
?>
