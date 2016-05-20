<?php
// 2010/02/26 tsuchitani AT ivywe DOT co DOT jp
// Last Update 20160422

//ADDTION DATA
$_SQL[] = "
CREATE TABLE {$_TABLES['DATABOX_addition']} (
  `seq` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `value` mediumtext ,
  PRIMARY KEY (`seq`),
  KEY `id` (`id`),
  KEY `field_id` (`field_id`),
  KEY `value` (`value`(16))
) ENGINE=MyISAM
";

//BASE DATA
$_SQL[] = "
CREATE TABLE {$_TABLES['DATABOX_base']} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(40) NOT NULL DEFAULT '',
  `title` varchar(128) DEFAULT NULL,
  `page_title` varchar(128) DEFAULT NULL,
  `description` mediumtext,
  `defaulttemplatesdirectory` varchar(40) NOT NULL DEFAULT '',
  `comments` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `comment_expire` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `commentcode` tinyint(4) NOT NULL DEFAULT '0',
  `meta_description` text,
  `meta_keywords` text,
  `language_id` char(2) DEFAULT NULL,
  `owner_id` mediumint(8) NOT NULL DEFAULT '1',
  `group_id` mediumint(8) NOT NULL DEFAULT '2',
  `perm_owner` tinyint(1) unsigned NOT NULL DEFAULT '3',
  `perm_group` tinyint(1) unsigned NOT NULL DEFAULT '3',
  `perm_members` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `perm_anon` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  `released` datetime NOT NULL,
  `expired` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `orderno` int(2) NOT NULL DEFAULT '0',
  `fieldset_id` int(11) NOT NULL DEFAULT '0',
  `trackbackcode` tinyint(4) NOT NULL DEFAULT '0',
  `cache_time` int(11) NOT NULL DEFAULT '0',
  `draft_flag` tinyint(3) NOT NULL DEFAULT '0',
  `udatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `uuid` mediumint(8) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `modified` (`modified`),
  KEY `created` (`created`),
  KEY `released` (`released`),
  KEY `expired` (`expired`)
) ENGINE=MyISAM
";

//カテゴリ
$_SQL[] = "
CREATE TABLE {$_TABLES['DATABOX_category']} (
  `seq` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`seq`),
  KEY `id` (`id`)
) ENGINE=MyISAM
";

//カテゴリ 定義
$_SQL[] = "
CREATE TABLE {$_TABLES['DATABOX_def_category']} (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(40) DEFAULT NULL,
  `name` varchar(64) NOT NULL,
  `description` mediumtext,
  `defaulttemplatesdirectory` varchar(40) DEFAULT NULL,
  `orderno` int(2) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `categorygroup_id` int(11) NOT NULL DEFAULT '0',
  `allow_display` binary(1) NOT NULL DEFAULT '0',
  `udatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uuid` mediumint(8) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM
";


//アトリビュート定義
$_SQL[] = "
CREATE TABLE {$_TABLES['DATABOX_def_field']} (
  `field_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `templatesetvar` varchar(64) NOT NULL,
  `description` mediumtext,
  `type` int(2) NOT NULL DEFAULT '0',
  `selection` mediumtext,
  `selectlist` VARCHAR( 16 ) NULL DEFAULT NULL,
  `checkrequried` binary(1) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `maxlength` int(11) DEFAULT NULL,
  `rows` int(11) DEFAULT NULL,
  `br` binary(1) DEFAULT NULL,
  `orderno` int(2) DEFAULT NULL,
  `allow_display` binary(1) DEFAULT 0,
  `allow_edit` binary(1) DEFAULT 0,
  `textcheck` binary(2) DEFAULT '0',
  `textconv` binary(2) DEFAULT '0',
  `searchtarget` binary(1) DEFAULT '0',
  `initial_value` mediumtext NOT NULL,
  `range_start` mediumtext NOT NULL,
  `range_end` mediumtext NOT NULL,
  `dfid` tinyint(3) NOT NULL DEFAULT '0',
  `description2` mediumtext NOT NULL,
  `fieldgroupno` int(2) NOT NULL,
  `udatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uuid` mediumint(8) NOT NULL,
  PRIMARY KEY (`field_id`)
) ENGINE=MyISAM
";

//グループ 定義
$_SQL[] = "
CREATE TABLE {$_TABLES['DATABOX_def_group']} (
  `group_id` int(11) NOT NULL,
  `code` varchar(40) DEFAULT NULL,
  `name` varchar(64) NOT NULL,
  `description` mediumtext,
  `orderno` int(2) DEFAULT NULL,
  `parent_flg` binary(1) NOT NULL DEFAULT '0',
  `input_type` BINARY(1) NOT NULL DEFAULT '0',
  `defaulttemplatesdirectory` varchar(40) NOT NULL DEFAULT '',
  `allow_display` binary(1) DEFAULT 0,
  `allow_edit` binary(1) DEFAULT 0,
  `udatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uuid` mediumint(8) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM
";

$_SQL[] = "
INSERT INTO {$_TABLES['DATABOX_def_group']} (
`group_id` 
)
VALUES (
'0'
)";

//マスタ
$_SQL[] = "
CREATE TABLE {$_TABLES['DATABOX_mst']} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kind` varchar(16) NOT NULL,
  `no` int(2) NOT NULL,
  `value` varchar(64) NOT NULL,
  `value2` varchar(64) DEFAULT NULL,
  `disp` varchar(64) DEFAULT NULL,
  `orderno` int(2) DEFAULT NULL,
  `relno` int(11) DEFAULT NULL,
  `udatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uuid` mediumint(8) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kind` (`kind`,`no`),
  KEY `kind_2` (`kind`,`orderno`)
) ENGINE=MyISAM
";

//アクセス数テーブル
$_SQL[] = "
CREATE TABLE {$_TABLES['DATABOX_stats']} (
  `id` int(11) NOT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `udatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM
";

//属性セットテーブル
$_SQL[] = "
CREATE TABLE {$_TABLES['DATABOX_def_fieldset']} (
  `fieldset_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` mediumtext,
  `defaulttemplatesdirectory` varchar(40) NOT NULL,
  `layout` varchar(16) NOT NULL,
  `udatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uuid` mediumint(8) NOT NULL,
  PRIMARY KEY (`fieldset_id`)
) ENGINE=MyISAM
";
$_SQL[] = "
INSERT INTO {$_TABLES['DATABOX_def_fieldset']} (
`fieldset_id` 
)
VALUES (
'0'
)";

//属性セット 属性関連
$_SQL[] = "
CREATE TABLE {$_TABLES['DATABOX_def_fieldset_assignments']} (
  `seq` int(11) NOT NULL AUTO_INCREMENT,
  `fieldset_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  PRIMARY KEY (`seq`),
  KEY `fieldset_id` (`fieldset_id`)
) ENGINE=MyISAM
";

//属性セット グループ関連
$_SQL[] = "
CREATE TABLE {$_TABLES['DATABOX_def_fieldset_group']} (
  `seq` int(11) NOT NULL AUTO_INCREMENT,
  `fieldset_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`seq`),
  KEY `fieldset_id` (`fieldset_id`)
) ENGINE=MyISAM
";





$pro=$_CONF['path'] . 'plugins/databox/proversion/';
if (file_exists($pro)) {
	include_once($pro.'sql/mysql_install_xml.php');
}
$csv=$_CONF['path'] . 'plugins/databox/csv/';
if (file_exists($csv)) {
	include_once($csv.'sql/mysql_install_csv.php');
}

?>
