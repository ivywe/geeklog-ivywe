<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Paypal Plugin 1.1                                                         |
// +---------------------------------------------------------------------------+
// | upgrade.php                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben - ben AT geeklog DOT fr                                    |   
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

if (strpos(strtolower($_SERVER['PHP_SELF']), 'upgrade.php') !== false) {
    die('This file can not be used on its own!');
}

function paypal_upgrade()
{
    global $_CONF, $_TABLES, $_USER, $_DB_dbms, $LANG_PAYPAL_1;

    $currentVersion = DB_getItem($_TABLES['plugins'], 'pi_version',
                                    "pi_name = 'paypal'");
    $code_version = plugin_chkVersion_paypal();
    if ($currentVersion == $code_version) {
        // nothing to do
        return true;
    }

    require_once $_CONF['path'] . 'plugins/paypal/autoinstall.php';
	require_once $_CONF['path'] . 'plugins/paypal/install_defaults.php';
	require_once $_CONF['path_system'] . 'classes/config.class.php';

    if (! plugin_compatible_with_this_version_paypal('paypal')) {
        return 3002;
    }

    // other update code goes here

    switch( $currentVersion ) {
        
		case '0.1rc1':
		case '0.1' :
		case '0.1.1':
		case '0.2' :
			DB_query("UPDATE {$_TABLES['plugins']} SET pi_homepage='http://geeklog.fr' WHERE pi_name='paypal",1);
			
		case '1.0' :			
			$c = config::get_instance();
			//This is main subgroup #0
			$c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'paypal');	
			//Main settings   
			$c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true, 'paypal');
			$c->add('paypal_folder', $_PAY_DEFAULT['paypal_folder'],
					'text', 0, 0, 0, 1, true, 'paypal');
			$c->add('menulabel', $_PAY_DEFAULT['menulabel'],
					'text', 0, 0, 0, 2, true, 'paypal');
			$c->add('paypal_login_required', $_PAY_DEFAULT['paypal_login_required'],
					'select', 0, 0, 3, 12, true, 'paypal');
			$c->add('hide_paypal_menu', $_PAY_DEFAULT['hide_paypal_menu'],
					'select', 0, 0, 3, 13, true, 'paypal');
			$c->add('paypalURL', $_PAY_DEFAULT['paypalURL'],
					'text', 0, 0, 0, 23, true, 'paypal');
			$c->add('receiverEmailAddr', $_PAY_DEFAULT['receiverEmailAddr'],
					'text', 0, 0, 0, 24, true, 'paypal');
			$c->add('currency', $_PAY_DEFAULT['currency'],
					'select', 0, 0, 20, 33, true, 'paypal');
			$c->add('anonymous_buy', $_PAY_DEFAULT['anonymous_buy'],
					'select', 0, 0, 3, 35, true, 'paypal');
			$c->add('purchase_email_user', $_PAY_DEFAULT['purchase_email_user'],
					'select', 0, 0, 3, 47, true, 'paypal');
			$c->add('purchase_email_user_attach', $_PAY_DEFAULT['purchase_email_user_attach'],
					'select', 0, 0, 3, 49, true, 'paypal');
			$c->add('purchase_email_anon', $_PAY_DEFAULT['purchase_email_anon'],
					'select', 0, 0, 3, 51, true, 'paypal');
			$c->add('purchase_email_anon_attach', $_PAY_DEFAULT['purchase_email_anon_attach'],
					'select', 0, 0, 3, 53, true, 'paypal');
			$c->add('maxPerPage', $_PAY_DEFAULT['maxPerPage'],
					'text', 0, 0, 0, 63, true, 'paypal');
			$c->add('categoryColumns', $_PAY_DEFAULT['categoryColumns'],
					'text', 0, 0, 0, 65, true, 'paypal');
			//images
            $c->add('fs_images', NULL, 'fieldset', 0, 1, NULL, 0, true, 'paypal');
		    $c->add('max_images_per_products', $_PAY_DEFAULT['max_images_per_products'],
                'text', 0, 1, 0, 1, true, 'paypal');
		    $c->add('max_image_width', $_PAY_DEFAULT['max_image_width'],
                'text', 0, 1, 0, 2, true, 'paypal');
		    $c->add('max_image_height', $_PAY_DEFAULT['max_image_height'],
                'text', 0, 1, 0, 3, true, 'paypal');
		    $c->add('max_image_size', $_PAY_DEFAULT['max_image_size'],
                'text', 0, 1, 0, 4, true, 'paypal');
		    $c->add('max_thumbnail_size', $_PAY_DEFAULT['max_thumbnail_size'],
                'text', 0, 1, 0, 5, true, 'paypal');
			//This is display subgroup #1
			$c->add('sg_display', NULL, 'subgroup', 1, 0, NULL, 0, true, 'paypal');
			// Display settings
			$c->add('fs_display', NULL, 'fieldset', 1, 8, NULL, 0, true, 'paypal');
			$c->add('paypal_main_header', NULL, 'text', 1, 8, 0, 2, true, 'paypal');
			$c->add('paypal_main_footer', NULL, 'text', 1, 8, 0, 4, true, 'paypal');
			DB_query("CREATE TABLE {$_TABLES['paypal_downloads']} (
            id int auto_increment,
            product_id int NOT NULL,
            file varchar(255),
            PRIMARY KEY (id)
	        ) ENGINE=MyISAM
	        ",1);
			DB_query("CREATE TABLE {$_TABLES['paypal_images']} (
            pi_pid varchar(40) NOT NULL,
            pi_img_num tinyint(2) unsigned NOT NULL,
            pi_filename varchar(128) NOT NULL,
            PRIMARY KEY (pi_pid,pi_img_num)
	        ) ENGINE=MyISAM
	        ",1);
			DB_query("ALTER TABLE {$_TABLES['paypal_products']}
            DROP small_pic, 
            DROP picture, 
		    ADD logged tinyint(1) default '0'
		    ",1);
			DB_query("INSERT INTO {$_TABLES['blocks']} (is_enabled, name, type, title, tid, blockorder, content, onleft, phpblockfn, owner_id, group_id, perm_owner, perm_group) VALUES (1,'cart_block','phpblock','Cart','all',1,'',0,'phpblock_paypal_cart',{$_USER['uid']},4,3,3)",1);
			
		case '1.1' :
		case '1.1.1' :
		    DB_query("ALTER TABLE {$_TABLES['paypal_products']}
		    ADD hits mediumint(8) unsigned NOT NULL default '0', 
		    ADD hidden tinyint(1) default '0'
		    ",1);
			
		case '1.1.2' :
		    $c = config::get_instance();
			$c->add('thumb_width', $_PAY_DEFAULT['thumb_width'],
				    'text', 0, 1, 0, 10, true, 'paypal');
		    $c->add('thumb_height', $_PAY_DEFAULT['thumb_height'],
				    'text', 0, 1, 0, 11, true, 'paypal');
			$c->add('products_col', $_PAY_DEFAULT['products_col'],
				    'select', 1, 8, 21, 10, true, 'paypal');
			DB_query("ALTER TABLE {$_TABLES['paypal_products']}
		    ADD active tinyint(1) default '1'
		    ",1);
		
		case '1.1.3' :
		    DB_query("ALTER TABLE {$_TABLES['paypal_downloads']}
		    ADD dl_date datetime,
		    ADD user_id int NOT NULL
		    ",1);
			
		case '1.1.4' :
			DB_query("CREATE TABLE {$_TABLES['paypal_subscriptions']} (
            id int(11) auto_increment,
            product_id int NOT NULL,
            user_id int NOT NULL,
            txn_id varchar(255),
            purchase_date datetime,
            expiration datetime,
            price decimal(12,2) unsigned,
            status varchar(12),
            add_to_group int(5) default NULL,
            notification tinyint(1) unsigned NOT NULL default '0',
            PRIMARY KEY  (id)
	        ) ENGINE=MyISAM
	        ");
			DB_query("ALTER TABLE {$_TABLES['paypal_products']}
		    ADD type varchar(15) default 'product' AFTER id,
			ADD item_id varchar(40) NOT NULL AFTER type,
			ADD show_in_blocks tinyint(1) unsigned default '1',
			ADD duration int(5) default NULL,
            ADD duration_type varchar(10) NOT NULL default 'month',
            ADD add_to_group int(5) default NULL
		    ");
            DB_query("INSERT INTO {$_TABLES['blocks']} (is_enabled, name, type, title, tid, blockorder, content, onleft, phpblockfn, owner_id, group_id, perm_owner, perm_group) VALUES (1,'paypal_randomBlock','phpblock','Random product','all',1,'',0,'phpblock_paypal_randomBlock',{$_USER['uid']},#group#,3,3)");
			
			$c->add('sg_myshop', NULL, 'subgroup', 2, 0, NULL, 0, true, 'paypal');
		    $c->add('fs_shopdetails', NULL, 'fieldset', 2, 20, NULL, 0, true, 'paypal');
		    $c->add('shop_name', NULL, 'text', 2, 20, 0, 2, true, 'paypal');
		    $c->add('shop_street1', NULL, 'text', 2, 20, 0, 4, true, 'paypal');
			$c->add('shop_street2', NULL, 'text', 2, 20, 0, 5, true, 'paypal');
		    $c->add('shop_postal', NULL, 'text', 2, 20, 0, 6, true, 'paypal');
		    $c->add('shop_city', NULL, 'text', 2, 20, 0, 8, true, 'paypal');
			$c->add('shop_country', NULL, 'text', 2, 20, 0, 9, true, 'paypal');
		    $c->add('shop_siret', NULL, 'text', 2, 20, 0, 10, true, 'paypal');
		    $c->add('shop_phone1', NULL, 'text', 2, 20, 0, 12, true, 'paypal');
		    $c->add('shop_phone2', NULL, 'text', 2, 20, 0, 14, true, 'paypal');
		    $c->add('shop_fax', NULL, 'text', 2, 20, 0, 16, true, 'paypal');
		
		    DB_query("CREATE TABLE {$_TABLES['paypal_users']} (
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
            ) ENGINE=MyISAM
            ");
		
			$c->add('fs_checkoutpage', NULL, 'fieldset', 1, 10, NULL, 0, true, 'paypal');
            $c->add('image_url', NULL, 'text', 1, 10, 0, 2, true, 'paypal');
            $c->add('cpp_header_image', NULL, 'text', 1, 10, 0, 4, true, 'paypal');
            $c->add('cpp_headerback_color', NULL, 'text', 1, 10, 0, 6, true, 'paypal');
            $c->add('cpp_headerborder_color', NULL, 'text', 1, 10, 0, 8, true, 'paypal');
            $c->add('cpp_payflow_color', NULL, 'text', 1, 10, 0, 10, true, 'paypal');
            $c->add('cs', 0, 'select', 1, 10, 22, 12, true, 'paypal');

            DB_query("ALTER TABLE {$_TABLES['paypal_products']}
            ADD owner_id mediumint(8) unsigned NOT NULL default '2',
            ADD group_id mediumint(8) unsigned NOT NULL default '1',
            ADD perm_owner tinyint(1) unsigned NOT NULL default '3',
            ADD perm_group tinyint(1) unsigned NOT NULL default '2',
            ADD perm_members tinyint(1) unsigned NOT NULL default '2',
            ADD perm_anon tinyint(1) unsigned NOT NULL default '2'
            ");

            $c->add('fs_permissions', NULL, 'fieldset', 0, 2, NULL, 0, true, 'paypal');
            $c->add('default_permissions', $_PAY_DEFAULT['default_permissions'],
                '@select', 0, 2,12, 10, true, 'paypal');
            $c->add('site_name', $_PAY_DEFAULT['site_name'],
                'text', 0, 0, 0, 7, true, 'paypal');
            $c->add('order', $_PAY_DEFAULT['order'],
                'select', 1, 8, 23, 15, true, 'paypal');
            $c->add('view_membership', $_PAY_DEFAULT['view_membership'],
                'select', 1, 8, 3, 20, true, 'paypal');
            $c->add('view_review', $_PAY_DEFAULT['view_review'],
                'select', 1, 8, 3, 25, true, 'paypal');
            $c->add('display_2nd_buttons', $_PAY_DEFAULT['display_2nd_buttons'],
                'select', 1, 8, 3, 35, true, 'paypal');
		
		case '1.2.1' :
            $c = config::get_instance();
            $c->add('display_blocks', '3','select', 1, 8, 24, 45, true, 'paypal');
            $c->add('display_item_id', '0','select', 1, 8, 3, 55, true, 'paypal');
            $c->add('display_complete_memberships', '0','select', 1, 8, 3, 22, true, 'paypal');
            $c->add('enable_pay_by_ckeck', 0,
                'select', 0, 0, 3, 70, true, 'paypal');
            $c->add('enable_buy_now', 1,
                'select', 0, 0, 3, 80, true, 'paypal');
			
			$c->del('site_name', 'paypal');
			
			$_PAY_CONF_OLD = $c->get_config('paypal');
			
			//move images settings
			$c->del('fs_images', 'paypal');
			$c->del('max_images_per_products', 'paypal');
			$c->del('max_image_width', 'paypal');
			$c->del('max_image_height', 'paypal');
			$c->del('max_image_size', 'paypal');
			$c->del('max_thumbnail_size', 'paypal');
			$c->del('thumb_width', 'paypal');
			$c->del('thumb_height', 'paypal');
			$c->del('maxPerPage', 'paypal');
		    $c->del('categoryColumns', 'paypal');
			
			$c->add('fs_images', NULL, 'fieldset', 1, 9, NULL, 0, true, 'paypal');
			$c->add('max_images_per_products', $_PAY_CONF_OLD['max_images_per_products'],
					'text', 1, 9, 0, 1, true, 'paypal');
			$c->add('max_image_width', $_PAY_CONF_OLD['max_image_width'],
					'text', 1, 9, 0, 2, true, 'paypal');
			$c->add('max_image_height', $_PAY_CONF_OLD['max_image_height'],
					'text', 1, 9, 0, 3, true, 'paypal');
			$c->add('max_image_size', $_PAY_CONF_OLD['max_image_size'],
					'text', 1, 9, 0, 4, true, 'paypal');
			$c->add('max_thumbnail_size', $_PAY_CONF_OLD['max_thumbnail_size'],
					'text', 1, 9, 0, 5, true, 'paypal');
			$c->add('thumb_width', $_PAY_CONF_OLD['thumb_width'],
					'text', 1, 9, 0, 10, true, 'paypal');
			$c->add('thumb_height', $_PAY_CONF_OLD['thumb_height'],
					'text', 1, 9, 0, 11, true, 'paypal');
			$c->add('maxPerPage', $_PAY_CONF_OLD['maxPerPage'],
                'text', 1, 9, 0, 20, true, 'paypal');
		    $c->add('categoryColumns', $_PAY_CONF_OLD['categoryColumns'],
                'text', 1, 9, 0, 22, true, 'paypal');
				
		case '1.3' :
            $c = config::get_instance();
            $c->add('enable_pay_by_paypal', 1,
                'select', 0, 0, 3, 65, true, 'paypal');
			
		case '1.3.1' :
		    DB_query("CREATE TABLE {$_TABLES['paypal_categories']} (
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
			PRIMARY KEY  (cat_id)
			) ENGINE=MyISAM
			");
			
			DB_query("ALTER TABLE {$_TABLES['paypal_products']} 
            ADD cat_id int(11) unsigned NOT NULL default '0' AFTER name
            ");
		
			// Migrate existing categories to the new category table - Lee Garner glfusion.org
			$res = DB_query("SELECT DISTINCT category
					FROM {$_TABLES['paypal_products']}
					WHERE category <> '' and category IS NOT NULL");
			if (DB_error()) {
				COM_errorLog("Could not retrieve old categories");
				return 1;
			}
			$admin_group = addslashes(DB_getItem($_TABLES['groups'], 'grp_id', "grp_name = 'Paypal Admin'"));
			if (DB_numRows($res) > 0) {
				while ($A = DB_fetchArray($res, false)) {
				    $category = addslashes($A['category']);
					DB_query("INSERT INTO {$_TABLES['paypal_categories']}
							(cat_name, group_id, owner_id)
						VALUES ('{$category}','{$admin_group}',{$_USER['uid']})");
					if (DB_error()) {
						COM_errorLog("Could not add new category {$A['category']}");
						return 1;
					}
					$cats[$A['category']] = DB_insertID();
				}
				// Now populate the cross-reference table
				$res = DB_query("SELECT id, category
						FROM {$_TABLES['paypal_products']}");
				if (DB_error()) {
					COM_errorLog("Error retrieving category data from products");
					return 1;
				}
				if (DB_numRows($res) > 0) {
					while ($A = DB_fetchArray($res, false)) {
						DB_query("UPDATE {$_TABLES['paypal_products']}
							SET cat_id = '{$cats[$A['category']]}'
							WHERE id = '{$A['id']}'");
						if (DB_error()) {
							COM_errorLog("Error updating prodXcat table");
							return 1;
						}
					}
				}
				DB_query("ALTER TABLE {$_TABLES['paypal_products']}
						DROP category");
			}
		case '1.3.2' :
		    $c = config::get_instance();
			$c->add('categoryHeading', $LANG_PAYPAL_1['category_heading'],
                'text', 1, 9, 0, 21, true, 'paypal');
		    $c->add('displayCatImage', 1,
                'select', 1, 9, 3, 30, true, 'paypal');
			$c->add('catImageWidth', '100',
                'text', 1, 9, 0, 40, true, 'paypal');
			$c->add('seo_shop_title', NULL,
    			'text', 2, 20, 0, 100, true, 'paypal');
			$c->add('displayCatDescription', 1,
                'select', 1, 9, 3, 50, true, 'paypal');
				
		case '1.3.3' :
		    DB_query("ALTER TABLE {$_TABLES['paypal_products']} 
            ADD created datetime DEFAULT NULL AFTER description,
			ADD customisable tinyint(1) AFTER price,
			ADD discount_a  decimal(12,2) unsigned AFTER price,
			ADD discount_p tinyint(2) AFTER price,
			ADD price_ref  decimal(12,2) unsigned AFTER price,
			ADD prov_id mediumint(8) default NULL AFTER show_in_blocks
            ");
			
			$created = date("YmdHis");
			DB_query("UPDATE {$_TABLES['paypal_products']}
			SET created='{$created}' 
			WHERE 1=1
			");
			
			DB_query("ALTER TABLE {$_TABLES['paypal_purchases']} 
            ADD product_name varchar(255) AFTER product_id
            ");
			
			DB_query("CREATE TABLE {$_TABLES['paypal_attributes']} (
			at_id int(11) NOT NULL auto_increment,
			at_type int(11) NOT NULL default '0',
			at_name varchar(255),
			at_code varchar(30),
			at_enabled tinyint(1) default '1',
			at_price decimal(12,2) default '0',
			at_image varchar(255) default NULL,
			PRIMARY KEY (at_id)
			) ENGINE=MyISAM
			");
			
			DB_query("CREATE TABLE {$_TABLES['paypal_attribute_type']} (
			at_tid int(11) NOT NULL auto_increment,
			at_tname varchar(255),
			at_torder tinyint(3) default NULL,
			PRIMARY KEY (at_tid)
			) ENGINE=MyISAM
			");
			
			DB_query("CREATE TABLE {$_TABLES['paypal_product_attribute']} (
			pa_id int(11) NOT NULL auto_increment,
			pa_pid int(11),
			pa_aid int(11),
			PRIMARY KEY (pa_id)
			) ENGINE=MyISAM
			");
			
			DB_query("CREATE TABLE {$_TABLES['paypal_stock']} (
			st_id varchar(255) NOT NULL,
			st_pid int(11) NOT NULL,
			st_qty int(6) default '0',
			qmax int(6) default NULL,
			qmin int(6) default NULL,
			PRIMARY KEY (st_id)
			) ENGINE=MyISAM
			");
			
			DB_query("CREATE TABLE {$_TABLES['paypal_delivery']} (
			did int(11) NOT NULL auto_increment,
			deli_date datetime DEFAULT NULL,
			user_id mediumint(8),
			provider_id mediumint(8),
			PRIMARY KEY  (did)
			) ENGINE=MyISAM
			");
			
			DB_query("CREATE TABLE {$_TABLES['paypal_stock_movements']} (
			mid int(11) NOT NULL auto_increment,
			move_date datetime DEFAULT NULL,
			stock_id varchar(255) NOT NULL,
			deli_id mediumint(8) NOT NULL,
			PRIMARY KEY (mid)
			) ENGINE=MyISAM
			");
			
			DB_query("CREATE TABLE {$_TABLES['paypal_providers']} (
			prov_id mediumint(8) NOT NULL auto_increment,
			prov_name VARCHAR(80)  NOT NULL,
			PRIMARY KEY (prov_id)
			) ENGINE=MyISAM
			");
			
		case '1.3.4' :
		    DB_query("ALTER TABLE {$_TABLES['paypal_attributes']} 
            ADD at_order tinyint(3) default '1'
			");
			
		case '1.3.5':
			$c = config::get_instance();
			$c->add('attribute_thumbnail_size', $_PAY_DEFAULT['attribute_thumbnail_size'],
                'text', 1, 9, 0, 7, true, 'paypal');
				
		case '1.3.6' :
		    DB_query("ALTER TABLE {$_TABLES['paypal_stock_movements']} 
            ADD move_qty int(6) DEFAULT '0'
			");
			
		case '1.3.7' :
			// Set default item_id
			$res = DB_query("SELECT id, item_id
					FROM {$_TABLES['paypal_products']}");
			if (DB_error()) {
				COM_errorLog("Error retrieving item_id data from products");
				return 1;
			}
			if (DB_numRows($res) > 0) {
				while ($A = DB_fetchArray($res, false)) {
				    if ($A['item_id'] == '') {
						DB_query("UPDATE {$_TABLES['paypal_products']}
							SET item_id = '{$A['id']}'
							WHERE id = '{$A['id']}'");
						if (DB_error()) {
							COM_errorLog("Error updating default item_id");
							return 1;
						}
					}
				}
			}
			
		case '1.3.12' :
			DB_query("ALTER TABLE {$_TABLES['paypal_stock_movements']} 
            ADD move_cpid varchar(30) NOT NULL AFTER move_qty
			");
			DB_query("ALTER TABLE {$_TABLES['paypal_stock']} 
            ADD st_cpid varchar(30) NOT NULL AFTER st_pid
			");
			
		case '1.3.13' :
			// Set stock
			set_time_limit(120); 
			$sql = "SELECT * FROM {$_TABLES['paypal_purchases']} "
                 . "WHERE status='complete'";
            $res = DB_query($sql);
			$nrows = DB_numRows( $res );
			COM_errorLog('Initial stock movement: *** Number of movements= '. $nrows);
            for( $i = 0; $i < $nrows; $i++ ) {
                $A = DB_fetchArray($res);
				$sql_ipn = "SELECT * FROM {$_TABLES['paypal_ipnlog']} WHERE txn_id = '{$A['txn_id']}'";
	            $res_ipn = DB_query($sql_ipn);
	            $B = DB_fetchArray($res_ipn);
				COM_errorLog('Initial stock movement: txn_id='. $A['txn_id']);
				// Allow all serialized data to be available to the template
				$ipn ='';
				if ($B['ipn_data'] != '') {
					$out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $B['ipn_data'] ); 
					$ipn = unserialize($out);
					
					if ($ipn['quantity1'] != '') {
					    //multi products
						$i2 = 1;
						for (; ; ) {
							if ($ipn['quantity'.$i2] == '') {
								break;
							}
						// stock movement
						$stock_id = PAYPAL_getStockId($ipn['item_number'.$i2]);
						$qty = $ipn['quantity'.$i2];
						PAYPAL_stockMovement ($stock_id, $ipn['item_number'.$i2], -$qty);
						COM_errorLog('Initial stock movement: -- stock_id='. $stock_id . ' | qty= '. -$qty);
						$i2++;
						}
					} else {
					    // stock movement
						$stock_id = PAYPAL_getStockId($ipn['item_number']);
						$qty = $ipn['quantity1'];
						PAYPAL_stockMovement ($stock_id, $ipn['item_number'], -$qty);
						COM_errorLog('Initial stock movement: -- stock_id= '. $stock_id . ' | qty= '. -$qty);
					}
				}
			}
			
		case '1.3.14' :
			DB_query("ALTER TABLE {$_TABLES['paypal_products']} 
            CHANGE download product_type TINYINT(1) NOT NULL default '0' AFTER customisable,
			ADD shipping_type TINYINT(1) NOT NULL default '0' AFTER physical,
			ADD taxable tinyint(1) AFTER physical,
			ADD weight FLOAT(6,3) DEFAULT '0.000' AFTER physical
            ");
			
			DB_query("ALTER TABLE {$_TABLES['paypal_products']} 
			DROP physical
            ");
			
		case '1.3.15' :
		
			DB_query("CREATE TABLE {$_TABLES['paypal_shipper_service']} (
				shipper_service_id int(11) NOT NULL auto_increment,
				shipper_service_name varchar(100) NOT NULL,
				shipper_service_service varchar(255) NOT NULL,
				shipper_service_description text,
				PRIMARY KEY  (shipper_service_id) 
			) ENGINE=MyISAM
			");

			DB_query("CREATE TABLE {$_TABLES['paypal_shipping_to']} (
				shipping_to_id int(11) NOT NULL auto_increment,
				shipping_to_name varchar(255) NOT NULL,
				PRIMARY KEY  (shipping_to_id) 
			) ENGINE=MyISAM
			");

			DB_query("CREATE TABLE {$_TABLES['paypal_shipping_cost']} (
				shipping_id int(11) NOT NULL auto_increment,
				shipping_shipper_id int(11) NOT NULL,
				shipping_min FLOAT(6,2)  NOT NULL DEFAULT '0.00',
				shipping_max FLOAT(6,2) NOT NULL DEFAULT '0.00',
				shipping_destination_id int(11) NOT NULL,
				shipping_amt FLOAT (6,2) NOT NULL DEFAULT '0.00',
				PRIMARY KEY  (shipping_id) 
			) ENGINE=MyISAM
			");
		case '1.3.16' :
            $c = config::get_instance();		    
			$c->del('enable_buy_now','paypal');
		
		case '1.3.17' :
		    DB_query("ALTER TABLE {$_TABLES['paypal_shipping_to']} 
			ADD shipping_to_order tinyint(3) default '1'
			");
			
		case '1.3.18' :
		    DB_query("ALTER TABLE {$_TABLES['paypal_shipping_cost']} 
			    MODIFY shipping_min FLOAT(6,3) NOT NULL DEFAULT '0.000',
			    MODIFY shipping_max FLOAT(6,3) NOT NULL DEFAULT '0.000'
			");
		case '1.3.19' :
		    DB_query("ALTER TABLE {$_TABLES['paypal_attributes']} 
			    MODIFY at_order smallint(5) unsigned NOT NULL default '1'
			");
			DB_query("ALTER TABLE {$_TABLES['paypal_attribute_type']}
			    MODIFY at_torder smallint(5) unsigned NOT NULL default '1'
			");
			DB_query("ALTER TABLE {$_TABLES['paypal_shipping_to']}
			    MODIFY shipping_to_order smallint(5) unsigned NOT NULL default '1'
			");
		case '1.3.20' :
		    DB_query("ALTER TABLE {$_TABLES['paypal_shipper_service']}
		        ADD shipper_service_exclude_cat smallint(5) unsigned NOT NULL default '0'
			");
		case '1.3.21' :	
		case '1.4.0':
		case '1.4.1':
		case '1.4.2':
		     DB_query("ALTER TABLE {$_TABLES['paypal_products']}
            DROP taxable 
		    ",1);
		case '1.4.3': 
		case '1.4.4' :
		case '1.5.0' :
        
		default :
			// update plugin version number
            $inst_parms = plugin_autoinstall_paypal('paypal');
            $pi_gl_version = $inst_parms['info']['pi_gl_version'];
            DB_query("UPDATE {$_TABLES['plugins']} SET pi_version = '$code_version', pi_gl_version = '$pi_gl_version' WHERE pi_name = 'paypal'");
	        COM_errorLog( "Updated paypal plugin from v$currentVersion to v$code_version", 1 );
            /* This code is for statistics ONLY */
            $message =  'Completed paypal plugin upgrade: ' . date('m d Y',time()) . "   AT " . date('H:i', time()) . "\n";
            $message .= 'Site: ' . $_CONF['site_url'] . ' and Sitename: ' . $_CONF['site_name'] . "\n";
			if (function_exists('PAYPALPRO_notifyExpiration')) $message .= 'Proversion' . "\n";
            COM_mail("ben@geeklog.fr","Updated paypal plugin from v$currentVersion to v$code_version",$message);
    }
	
    return true;
}
?>