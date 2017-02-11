<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | paypal plugin 1.1                                                         |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// |                                                                           |
// | Initial Installation Defaults used when loading the online configuration  |
// | records. These settings are only used during the initial installation     |
// | and not referenced any more once the plugin is installed.                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors: Ben        - cordiste AT free DOT fr                             |
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
//

if (strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

/*
 * paypal default settings
 *
 * Initial Installation Defaults used when loading the online configuration
 * records. These settings are only used during the initial installation
 * and not referenced any more once the plugin is installed
 *
 */
 
/**
*   Default values to be used during plugin installation/upgrade
*   @global array $_PAY_DEFAULT
*/
global $_DB_table_prefix, $_PAY_DEFAULT, $LANG_PAY_1;

$_PAY_DEFAULT = array();

$_PAY_DEFAULT['pi_name']    = 'paypal'; // Plugin name

/**
*   Main settings
*/
$_PAY_DEFAULT['paypal_folder']    = 'paypal'; //Allow to move the directory where the users's paypal program is store
$_PAY_DEFAULT['menulabel']    = 'Shop';
$_PAY_DEFAULT['paypal_login_required'] = 1;

// Set to 1 to hide the "paypal" entry from the top menu:
$_PAY_DEFAULT['hide_paypal_menu'] = 0;

// Set the default permissions
$_PAY_DEFAULT['default_permissions'] =  array (3, 3, 2, 2);

/**
*   Display Settings
*/
/**
 * URL for paypal verification should be www.paypal.com for live service
 * or www.sandbox.paypal.com for testing.
 * If your version of PHP is 4.3.0 or greater and OpenSSL has been compiled
 * into PHP, you can prefix the url below with "ssl://" to encrypt the
 * paypal 'postback'.
 */
$_PAY_DEFAULT['paypalURL'] = 'www.sandbox.paypal.com';

/**
 * PayPal email addresses for your business receiver account,
 */
$_PAY_DEFAULT['receiverEmailAddr'] = '';

/**
 * This is the currency that you want all transactions to occur in.  Your
 * Paypal account must have the choosen currency enabled (on the Paypal website
 * see: Selling Preferences->Paypment Receiving Preferences).  This plugin
 * currently requires you to have only one currency balance enabled in your
 * Paypal account. 
 */
 $_PAY_DEFAULT['currency'] = '円';

/**
 * 1 to allow anonymous users to make purchases without logging in first.  Note
 * that anonymous users won't have access to purchase history or to direct downloads
 */
$_PAY_DEFAULT['anonymous_buy'] = 1;

/**
 * Configuration parameters to specify whether "on purchase" emails will be sent.
 * Be sure to edit the files templates/purchase_email_subject.txt and
 *  templates/purchase_email_message.txt to customize the email message.
 * You should disable the *_attach options below if you are distributing large files
 *  or it is likely that a purchaser will be buying many medium files otherwise the
 *  email will become too large and will likely never be received
 * purchase_email_user true if logged in users should get a purchase email
 * purchase_email_user_attach true if logged in users should get purchases
 *                            emailed as attachments
 * purchase_email_anon true if anonymous users should get a purchase email
 * purchase_email_anon_attach true if anonymous users should get purchases
 *                            emailed as attachments
 */
$_PAY_DEFAULT['purchase_email_user']        = true;
$_PAY_DEFAULT['purchase_email_user_attach'] = true;
$_PAY_DEFAULT['purchase_email_anon']        = true;
$_PAY_DEFAULT['purchase_email_anon_attach'] = true;

/**
 * Number of products to display per page.  0 indicates that all products should
 * be displayed on a single page.
 */
$_PAY_DEFAULT['maxPerPage'] = 10;

/**
 * Number of columns of categories to display.  Set to 0 to disable categories.
 * If you don't have any categories set, this setting is meaningless.
 */
 $_PAY_DEFAULT['categoryColumns'] = 3;
 
 /**
 * Images settings
 */
$_PAY_DEFAULT['max_images_per_products'] = 5;
$_PAY_DEFAULT['max_image_width'] = 1600;
$_PAY_DEFAULT['max_image_height'] = 1600;
$_PAY_DEFAULT['max_image_size'] = 1048576;
$_PAY_DEFAULT['max_thumbnail_size'] = 75;
$_PAY_DEFAULT['attribute_thumbnail_size'] = 75;
//size in pixel for products list
$_PAY_DEFAULT['thumb_width'] = 75;
$_PAY_DEFAULT['thumb_height'] = 75;
// Number of columns of products to display (max)
$_PAY_DEFAULT['products_col'] = 3;

/**
 * Ordering of products in lists
 * Values must be a column of the produts table: 'name', 'price', 'id'
 * Values can be modified by either 'ASC' or 'DESC': 'name DESC'
 */
$_PAY_DEFAULT['order'] = 'name';

$_PAY_DEFAULT['view_memberships'] = 0;
$_PAY_DEFAULT['view_review'] = 0;
$_PAY_DEFAULT['display_2nd_buttons'] = 0;

/**
* Initialize paypal plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. 
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_paypal()
{
    global $_CONF, $_PAY_DEFAULT, $LANG_PAYPAL_1;
	
    $c = config::get_instance();
    if (!$c->group_exists('paypal')) {

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
		$c->add('enable_pay_by_paypal', 1,
                'select', 0, 0, 3, 65, true, 'paypal');
		$c->add('enable_pay_by_ckeck', 0,
                'select', 0, 0, 3, 70, true, 'paypal');

		
        // Permissions
        $c->add('fs_permissions', NULL, 'fieldset', 0, 2, NULL, 0, true, 'paypal');
        $c->add('default_permissions', $_PAY_DEFAULT['default_permissions'],
                '@select', 0, 2, 12, 10, true, 'paypal');
				
		//Display settings subgroup #1
		$c->add('sg_display', NULL, 'subgroup', 1, 0, NULL, 0, true, 'paypal');
		
		$c->add('fs_display', NULL, 'fieldset', 1, 8, NULL, 0, true, 'paypal');
		$c->add('paypal_main_header', NULL, 'text', 1, 8, 0, 2, true, 'paypal');
		$c->add('paypal_main_footer', NULL, 'text', 1, 8, 0, 4, true, 'paypal');
		$c->add('products_col', $_PAY_DEFAULT['products_col'],
				'select', 1, 8, 21, 10, true, 'paypal');
        $c->add('order', $_PAY_DEFAULT['order'],
                'select', 1, 8, 23, 15, true, 'paypal');
        $c->add('view_membership', $_PAY_DEFAULT['view_membership'],
                'select', 1, 8, 3, 20, true, 'paypal');
		$c->add('display_complete_memberships', '0','select', 1, 8, 3, 22, true, 'paypal');
        $c->add('view_review', $_PAY_DEFAULT['view_review'],
                'select', 1, 8, 3, 25, true, 'paypal');
        $c->add('display_2nd_buttons', $_PAY_DEFAULT['display_2nd_buttons'],
                'select', 1, 8, 3, 35, true, 'paypal');
		$c->add('display_blocks', '3','select', 1, 8, 24, 45, true, 'paypal');
		$c->add('display_item_id', '0','select', 1, 8, 3, 55, true, 'paypal');
		
		//images
        $c->add('fs_images', NULL, 'fieldset', 1, 9, NULL, 0, true, 'paypal');
		$c->add('max_images_per_products', $_PAY_DEFAULT['max_images_per_products'],
                'text', 1, 9, 0, 1, true, 'paypal');
		$c->add('max_image_width', $_PAY_DEFAULT['max_image_width'],
                'text', 1, 9, 0, 2, true, 'paypal');
		$c->add('max_image_height', $_PAY_DEFAULT['max_image_height'],
                'text', 1, 9, 0, 3, true, 'paypal');
		$c->add('max_image_size', $_PAY_DEFAULT['max_image_size'],
                'text', 1, 9, 0, 4, true, 'paypal');
		$c->add('max_thumbnail_size', $_PAY_DEFAULT['max_thumbnail_size'],
                'text', 1, 9, 0, 5, true, 'paypal');
		$c->add('attribute_thumbnail_size', $_PAY_DEFAULT['attribute_thumbnail_size'],
                'text', 1, 9, 0, 7, true, 'paypal');
		$c->add('thumb_width', $_PAY_DEFAULT['thumb_width'],
				'text', 1, 9, 0, 10, true, 'paypal');
		$c->add('thumb_height', $_PAY_DEFAULT['thumb_height'],
				'text', 1, 9, 0, 11, true, 'paypal');
		$c->add('maxPerPage', $_PAY_DEFAULT['maxPerPage'],
                'text', 1, 9, 0, 20, true, 'paypal');
		$c->add('categoryHeading', $LANG_PAYPAL_1['category_heading'],
                'text', 1, 9, 0, 21, true, 'paypal');
		$c->add('categoryColumns', $_PAY_DEFAULT['categoryColumns'],
                'text', 1, 9, 0, 22, true, 'paypal');
		$c->add('displayCatImage', 1,
                'select', 1, 9, 3, 30, true, 'paypal');
		$c->add('catImageWidth', '100',
                'text', 1, 9, 0, 40, true, 'paypal');
		$c->add('displayCatDescription', 1,
                'select', 1, 9, 3, 50, true, 'paypal');

		
		//paypal checkout page
		$c->add('fs_checkoutpage', NULL, 'fieldset', 1, 10, NULL, 0, true, 'paypal');
		$c->add('image_url', NULL, 'text', 1, 10, 0, 2, true, 'paypal');
		$c->add('cpp_header_image', NULL, 'text', 1, 10, 0, 4, true, 'paypal');
		$c->add('cpp_headerback_color', NULL, 'text', 1, 10, 0, 6, true, 'paypal');
		$c->add('cpp_headerborder_color', NULL, 'text', 1, 10, 0, 8, true, 'paypal');
		$c->add('cpp_payflow_color', NULL, 'text', 1, 10, 0, 10, true, 'paypal');
		$c->add('cs', 0, 'select', 1, 10, 22, 12, true, 'paypal');
		
		// My shop settings subgroup #2
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
		$c->add('seo_shop_title', NULL, 'text', 2, 20, 0, 100, true, 'paypal');
		
    }				

    return true;
}

?>