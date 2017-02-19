<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.4                                                           |
// +---------------------------------------------------------------------------+
// | mysql_install.php                                                         |
// |                                                                           |
// | Installation SQL                                                          |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2014 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben                                                            |
// +---------------------------------------------------------------------------+
// | Created with the Geeklog Plugin Toolkit.                                  |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is licensed under the terms of the GNU General Public License|
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                      |
// | See the GNU General Public License for more details.                      |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

if (strpos(strtolower($_SERVER['PHP_SELF']), 'mysql_install.php') !== false) {
    die('This file can not be used on its own!');
}

$_SQL[] = "
CREATE TABLE {$_TABLES['maps_geo']} (
  gid INT NOT NULL AUTO_INCREMENT,
  geo varchar(255) NOT NULL default '',
  lat FLOAT( 10, 6 ) NOT NULL ,
  lng FLOAT( 10, 6 ) NOT NULL ,
  PRIMARY KEY (gid)
) ENGINE=InnoDB
";

$_SQL[] = "
CREATE TABLE {$_TABLES['maps_maps']} (
  mid INT NOT NULL AUTO_INCREMENT,
  name varchar(80) NOT NULL default '',
  description varchar(255) NOT NULL default '',
  created datetime NOT NULL,
  modified datetime NOT NULL,
  free_marker tinyint(1) unsigned NOT NULL default '1',
  paid_marker tinyint(1) unsigned NOT NULL default '1',
  active tinyint(1) unsigned NOT NULL default '1',
  hidden tinyint(1) unsigned NOT NULL default '0',
  geo varchar(255) NOT NULL default '',
  lat FLOAT( 10, 6 ) NOT NULL ,
  lng FLOAT( 10, 6 ) NOT NULL ,
  width varchar(6) NOT NULL default '100%',
  height varchar(6) NOT NULL default '600px',
  zoom tinyint(1) unsigned NOT NULL default '6',
  type varchar(20) NOT NULL default 'ROADMAP',
  header text,
  footer text,
  mmk_default tinyint(1) unsigned NOT NULL default '1',
  primary_color VARCHAR(7) NOT NULL default '#666666',
  stroke_color VARCHAR(7) NOT NULL default '#666666',
  label char(1),
  label_color tinyint(1) unsigned NOT NULL default '0',
  mmk_icon smallint(5) unsigned default '0',
  hits int(11) NOT NULL default '0',
  owner_id mediumint(8) unsigned NOT NULL default '2',
  group_id mediumint(8) unsigned NOT NULL default '1',
  perm_owner tinyint(1) unsigned NOT NULL default '3',
  perm_group tinyint(1) unsigned NOT NULL default '2',
  perm_members tinyint(1) unsigned NOT NULL default '2',
  perm_anon tinyint(1) unsigned NOT NULL default '2',
  PRIMARY KEY (mid)
) ENGINE=InnoDB
";

$_SQL[] = "
CREATE TABLE {$_TABLES['maps_markers']} (
  mkid BIGINT NOT NULL,
  name varchar(80) NOT NULL default '',
  address varchar(255) NOT NULL default '',
  lat FLOAT( 10, 6 ) NOT NULL ,
  lng FLOAT( 10, 6 ) NOT NULL ,
  mid INT NOT NULL,
  mk_default tinyint(1) unsigned NOT NULL default '1',
  mk_pcolor VARCHAR(7) NOT NULL default '#666666',
  mk_scolor VARCHAR(7) NOT NULL default '#666666',
  mk_label char(1),
  mk_label_color tinyint(1) unsigned NOT NULL default '0',
  mk_icon smallint(5) unsigned default '0',
  payed tinyint(1) unsigned NOT NULL default '0',
  created datetime NOT NULL,
  modified datetime NOT NULL,
  validity tinyint(1) unsigned NOT NULL default '0',
  validity_start datetime NOT NULL,
  validity_end datetime NOT NULL,
  active tinyint(1) unsigned NOT NULL default '1',
  hidden tinyint(1) unsigned NOT NULL default '0',
  remark text NOT NULL default '',
  description TEXT NOT NULL default '',
  street varchar(255) NOT NULL default '',
  code varchar(10) NOT NULL default '',
  city varchar(255) NOT NULL default '',
  state varchar(255) NOT NULL default '',
  country varchar(255) NOT NULL default '',
  tel varchar(20) NOT NULL default '',
  fax varchar(20) NOT NULL default '',
  web varchar(255) NOT NULL default '',
  item_1 varchar(255) NOT NULL default '',
  item_2 varchar(255) NOT NULL default '',
  item_3 varchar(255) NOT NULL default '',
  item_4 varchar(255) NOT NULL default '',
  item_5 varchar(255) NOT NULL default '',
  item_6 varchar(255) NOT NULL default '',
  item_7 varchar(255) NOT NULL default '',
  item_8 varchar(255) NOT NULL default '',
  item_9 varchar(255) NOT NULL default '',
  item_10 varchar(255) NOT NULL default '',
  hits int(11) NOT NULL default '0',
  url varchar(255) NOT NULL default '',
  type varchar(20) NOT NULL default '',
  owner_id mediumint(8) unsigned NOT NULL default '1',
  group_id mediumint(8) unsigned NOT NULL default '1',
  perm_owner tinyint(1) unsigned NOT NULL default '3',
  perm_group tinyint(1) unsigned NOT NULL default '2',
  perm_members tinyint(1) unsigned NOT NULL default '2',
  perm_anon tinyint(1) unsigned NOT NULL default '2',
  submission tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (mkid)
) ENGINE=InnoDB
";

$_SQL[] = "
CREATE TABLE {$_TABLES['maps_submission']} (
  mkid BIGINT NOT NULL,
  name varchar(80) NOT NULL default '',
  address varchar(255) NOT NULL default '',
  lat FLOAT( 10, 6 ) NOT NULL ,
  lng FLOAT( 10, 6 ) NOT NULL ,
  mid INT NOT NULL,
  mk_default tinyint(1) unsigned NOT NULL default '1',
  payed tinyint(1) unsigned NOT NULL default '0',
  created datetime NOT NULL,
  modified datetime NOT NULL,
  validity tinyint(1) unsigned NOT NULL default '0',
  validity_start datetime NOT NULL,
  validity_end datetime NOT NULL,
  active tinyint(1) unsigned NOT NULL default '1',
  hidden tinyint(1) unsigned NOT NULL default '0',
  remark text NOT NULL default '',
  description TEXT NOT NULL default '',
  street varchar(255) NOT NULL default '',
  code varchar(10) NOT NULL default '',
  city varchar(255) NOT NULL default '',
  state varchar(255) NOT NULL default '',
  country varchar(255) NOT NULL default '',
  tel varchar(20) NOT NULL default '',
  fax varchar(20) NOT NULL default '',
  web varchar(255) NOT NULL default '',
  item_1 varchar(255) NOT NULL default '',
  item_2 varchar(255) NOT NULL default '',
  item_3 varchar(255) NOT NULL default '',
  item_4 varchar(255) NOT NULL default '',
  item_5 varchar(255) NOT NULL default '',
  item_6 varchar(255) NOT NULL default '',
  item_7 varchar(255) NOT NULL default '',
  item_8 varchar(255) NOT NULL default '',
  item_9 varchar(255) NOT NULL default '',
  item_10 varchar(255) NOT NULL default '',
  hits int(11) NOT NULL default '0',
  url varchar(255) NOT NULL default '',
  type varchar(20) NOT NULL default '',
  owner_id mediumint(8) unsigned NOT NULL default '1',
  group_id mediumint(8) unsigned NOT NULL default '1',
  perm_owner tinyint(1) unsigned NOT NULL default '3',
  perm_group tinyint(1) unsigned NOT NULL default '2',
  perm_members tinyint(1) unsigned NOT NULL default '2',
  perm_anon tinyint(1) unsigned NOT NULL default '2',
  submission tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY (mkid)
) ENGINE=InnoDB
";

$_SQL[] = "
CREATE TABLE {$_TABLES['maps_overlays']} (
  oid INT NOT NULL AUTO_INCREMENT,
  o_name VARCHAR(255),
  o_group SMALLINT(5) NOT NULL DEFAULT '0',
  o_image VARCHAR(255),
  o_active TINYINT(1),
  o_zoom_min TINYINT(1),
  o_zoom_max TINYINT(1),
  o_sw_lat FLOAT( 10, 6 ) NOT NULL,
  o_sw_lng FLOAT( 10, 6 ) NOT NULL,
  o_ne_lat FLOAT( 10, 6 ) NOT NULL,
  o_ne_lng FLOAT( 10, 6 ) NOT NULL,
  o_order smallint(5) unsigned NOT NULL default '1',
  PRIMARY KEY (oid)
) ENGINE=InnoDB
";

$_SQL[] = "CREATE TABLE {$_TABLES['maps_map_overlay']} (
	mo_id int(11) NOT NULL auto_increment,
	mo_mid int(11),
	mo_oid int(11),
	PRIMARY KEY (mo_id)
) ENGINE=InnoDB
";

$_SQL[] = "
CREATE TABLE {$_TABLES['maps_map_icons']} (
  icon_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  icon_name VARCHAR(255),
  icon_image VARCHAR(255),
  PRIMARY KEY (icon_id)
) ENGINE=InnoDB
";

$_SQL[] = "
CREATE TABLE {$_TABLES['maps_overlays_groups']} (
  o_group_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  o_group_name VARCHAR(255),
  PRIMARY KEY (o_group_id)
) ENGINE=InnoDB
";

$_SQL[] = "INSERT INTO {$_TABLES['vars']} (name, value) VALUES ('globalMapHits', '0')";
?>
