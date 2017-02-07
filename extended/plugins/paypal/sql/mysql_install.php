<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Paypal Plugin 1.1 2                                                      |
// +---------------------------------------------------------------------------+
// | mysql_install.php                                                         |
// |                                                                           |
// | Installation SQL                                                          |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben - cordiste AT free DOT fr                                  |
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

$_SQL[] = "CREATE TABLE {$_TABLES['paypal_ipnlog']} (
    id int auto_increment,
    ip_addr varchar(15) NOT NULL,
    time datetime NOT NULL,
    verified tinyint(1) default '0',
    txn_id varchar(255),
    ipn_data text NOT NULL,
    PRIMARY KEY (id) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$_SQL[] = "CREATE TABLE {$_TABLES['paypal_products']} (
    id int auto_increment,
	type varchar(15) default 'product',
	item_id varchar(40) NOT NULL,
    name varchar(255) NOT NULL,
    cat_id int(11) unsigned NOT NULL default '0' ,
    short_description varchar(255),
    description text,
	created datetime DEFAULT NULL, 
    price decimal(12,2) unsigned,
	price_ref  decimal(12,2) unsigned,
	discount_p tinyint(2),
	discount_a  decimal(12,2) unsigned,
	customisable tinyint(1),
    product_type tinyint(1) default '0',
	weight FLOAT(6,3) DEFAULT '0.000',
	shipping_type TINYINT(1) NOT NULL default '0',
	logged tinyint(1) default '0',
    file varchar(255),
    expiration int,
	hits mediumint(8) unsigned NOT NULL default '0', 
	hidden tinyint(1) default '0',
	active tinyint(1) default '1',
	show_in_blocks tinyint(1) unsigned default '1',
	prov_id  mediumint(8) default NULL,
	duration int(5) default NULL,
    duration_type varchar(10) NOT NULL default 'month',
    add_to_group int(5) default NULL,
    owner_id mediumint(8) unsigned NOT NULL default '2',
    group_id mediumint(8) unsigned NOT NULL default '1',
    perm_owner tinyint(1) unsigned NOT NULL default '3',
    perm_group tinyint(1) unsigned NOT NULL default '2',
    perm_members tinyint(1) unsigned NOT NULL default '2',
    perm_anon tinyint(1) unsigned NOT NULL default '2',
    PRIMARY KEY (id),
    INDEX products_name (name),
    INDEX products_price (price),
    INDEX products_category (cat_id) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

//TODO: multiple downloads...
$_SQL[] = "CREATE TABLE {$_TABLES['paypal_downloads']} (
    id int auto_increment,
    product_id int NOT NULL,
    file varchar(255),
	dl_date datetime,
	user_id int NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$_SQL[] = "CREATE TABLE {$_TABLES['paypal_purchases']} (
    id int auto_increment,
    product_id int NOT NULL,
	product_name varchar(255),
    quantity int NOT NULL DEFAULT 1,
    user_id int NOT NULL,
    txn_id varchar(255),
    purchase_date datetime,
    status varchar(12),
    expiration datetime,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$_SQL[] = "CREATE TABLE {$_TABLES['paypal_images']} (
    pi_pid varchar(40) NOT NULL,
    pi_img_num tinyint(2) unsigned NOT NULL,
    pi_filename varchar(128) NOT NULL,
    PRIMARY KEY (pi_pid,pi_img_num)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$_SQL[] = "CREATE TABLE {$_TABLES['paypal_subscriptions']} (
    id int(11) auto_increment,
    product_id int NOT NULL,
    user_id int NOT NULL,
    txn_id varchar(255),
    purchase_date datetime NOT NULL,
    expiration datetime NOT NULL,
    price decimal(12,2) unsigned,
    status varchar(12),
    add_to_group int(5) default NULL,
    notification tinyint(1) unsigned NOT NULL default '0',
    PRIMARY KEY  (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
			
$_SQL[] = "CREATE TABLE {$_TABLES['paypal_users']} (
    user_id mediumint(8) unsigned NOT NULL,
    user_name VARCHAR(80) default NULL,
	user_contact VARCHAR(80) default NULL,
	user_proid VARCHAR(20) default NULL,
	user_street1 VARCHAR(50) default NULL,
	user_street2 VARCHAR(50) default NULL,
    user_postal VARCHAR(20) default NULL,
    user_city VARCHAR(30) default NULL,
	user_country VARCHAR(30) default NULL,
	user_phone1 varchar(20) default NULL,
	user_phone2 varchar(20) default NULL,
	user_fax varchar(20) default NULL,
    status tinyint(1) DEFAULT '0',
    PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$_SQL[] = "CREATE TABLE {$_TABLES['paypal_categories']} (
    cat_id smallint(5) unsigned NOT NULL auto_increment,
    parent_id smallint(5) unsigned default '0',
    cat_name varchar(255) default '',
    description text default '',
	image varchar(255) default '',
    enabled tinyint(1) unsigned default '1',
    group_id mediumint(8) unsigned NOT NULL default '1',
    owner_id mediumint(8) unsigned NOT NULL default '1',
    perm_owner tinyint(1) unsigned NOT NULL default '3',
    perm_group tinyint(1) unsigned NOT NULL default '3',
    perm_members tinyint(1) unsigned NOT NULL default '2',
    perm_anon tinyint(1) unsigned NOT NULL default '2',
    PRIMARY KEY (cat_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";	
	
$_SQL[] = "CREATE TABLE {$_TABLES['paypal_attributes']} (
	at_id int(11) NOT NULL auto_increment,
	at_type int(11) NOT NULL default '0',
	at_name varchar(255),
	at_code varchar(30),
	at_enabled tinyint(1) default '1',
	at_price decimal(12,2) default '0',
	at_image varchar(255) default NULL,
	at_order smallint(5) unsigned NOT NULL default '1',
	PRIMARY KEY (at_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$_SQL[] = "CREATE TABLE {$_TABLES['paypal_attribute_type']} (
	at_tid int(11) NOT NULL auto_increment,
	at_tname varchar(255),
	at_torder smallint(5) unsigned NOT NULL default '1',
	PRIMARY KEY (at_tid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
	";
	
$_SQL[] = "CREATE TABLE {$_TABLES['paypal_product_attribute']} (
	pa_id int(11) NOT NULL auto_increment,
	pa_pid int(11),
	pa_aid int(11),
	PRIMARY KEY (pa_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$_SQL[] = "CREATE TABLE {$_TABLES['paypal_stock']} (
	st_id varchar(128) NOT NULL,
	st_pid int(11) NOT NULL,
	st_cpid varchar(30) NOT NULL,
	st_qty int(6) DEFAULT '0',
	qmax int(6) DEFAULT NULL,
	qmin int(6) DEFAULT NULL,
	PRIMARY KEY (st_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$_SQL[] = "CREATE TABLE {$_TABLES['paypal_delivery']} (
	did int(11) NOT NULL auto_increment,
	deli_date datetime DEFAULT NULL,
	user_id mediumint(8),
	provider_id mediumint(8),
	PRIMARY KEY (did)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$_SQL[] = "CREATE TABLE {$_TABLES['paypal_stock_movements']} (
	mid int(11) NOT NULL auto_increment,
	move_date datetime DEFAULT NULL,
	move_qty int(6) DEFAULT '0',
	move_cpid varchar(30) NOT NULL,
	stock_id varchar(255) NOT NULL,
	deli_id mediumint(8) NOT NULL,
	PRIMARY KEY (mid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
			
$_SQL[] = "CREATE TABLE {$_TABLES['paypal_providers']} (
	prov_id mediumint(8) NOT NULL auto_increment,
	prov_name VARCHAR(80)  NOT NULL,
	PRIMARY KEY (prov_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$_SQL[] = "CREATE TABLE {$_TABLES['paypal_shipper_service']} (
    shipper_service_id int(11) NOT NULL auto_increment,
    shipper_service_name varchar(100) NOT NULL,
    shipper_service_service varchar(255) NOT NULL,
    shipper_service_description text,
	shipper_service_exclude_cat smallint(5) unsigned NOT NULL default '0',
    PRIMARY KEY  (shipper_service_id) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$_SQL[] = "CREATE TABLE {$_TABLES['paypal_shipping_to']} (
    shipping_to_id int(11) NOT NULL auto_increment,
    shipping_to_name varchar(255) NOT NULL,
	shipping_to_order smallint(5) unsigned NOT NULL default '1',
    PRIMARY KEY  (shipping_to_id) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$_SQL[] = "CREATE TABLE {$_TABLES['paypal_shipping_cost']} (
    shipping_id int(11) NOT NULL auto_increment,
    shipping_shipper_id int(11) NOT NULL,
    shipping_min FLOAT(6,3)  NOT NULL DEFAULT '0.000',
    shipping_max FLOAT(6,3) NOT NULL DEFAULT '0.000',
	shipping_destination_id int(11) NOT NULL,
	shipping_amt FLOAT (6,2) NOT NULL DEFAULT '0.00',
    PRIMARY KEY  (shipping_id) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

/*	
Remove for Geeklog 2.0 compatibility
See http://geeklog.fr/wiki/plugins:paypal to add blocks
$_SQL[] = "INSERT INTO {$_TABLES['blocks']} (is_enabled, name, type, title, tid, blockorder, content, onleft, phpblockfn, owner_id, group_id, perm_owner, perm_group) VALUES (1,'cart_block','phpblock','Cart','all',1,'',0,'phpblock_paypal_cart',{$_USER['uid']},#group#,3,3)";
$_SQL[] = "INSERT INTO {$_TABLES['blocks']} (is_enabled, name, type, title, tid, blockorder, content, onleft, phpblockfn, owner_id, group_id, perm_owner, perm_group) VALUES (1,'paypal_randomBlock','phpblock','Random product','all',1,'',0,'phpblock_paypal_randomBlock',{$_USER['uid']},#group#,3,3)";
*/
?>
