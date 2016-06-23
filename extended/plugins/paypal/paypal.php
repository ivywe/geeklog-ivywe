<?php
// +--------------------------------------------------------------------------+
// | Paypal Plugin - geeklog CMS                                              |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                             |
// |                                                                          |
// | Authors: ::Ben - cordiste AT free DOT fr                                 |
// +--------------------------------------------------------------------------+
// |                                                                          |
// | This program is free software; you can redistribute it and/or            |
// | modify it under the terms of the GNU General Public License              |
// | as published by the Free Software Foundation; either version 2           |
// | of the License, or (at your option) any later version.                   |
// |                                                                          |
// | This program is distributed in the hope that it will be useful,          |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
// | GNU General Public License for more details.                             |
// |                                                                          |
// | You should have received a copy of the GNU General Public License        |
// | along with this program; if not, write to the Free Software Foundation,  |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.          |
// |                                                                          |
// +--------------------------------------------------------------------------+

if (!defined ('VERSION')) {
    die ('This file can not be used on its own.');
}

/* Paypal Path Configuration. If you want to move the directory where
 * the Paypal programs are stored and accessed, change the
 * directory name in the config area.
 */
$_PAY_CONF['path_html']  = $_CONF['path_html'] . $_PAY_CONF['paypal_folder'] . '/';
$_PAY_CONF['site_url']   = $_CONF['site_url'] . '/' . $_PAY_CONF['paypal_folder'];
$_PAY_CONF['path_images']  = $_CONF['path_images'] . 'paypal/products/';
$_PAY_CONF['path_cat_images']  = $_CONF['path_images'] . 'paypal/categories/';
$_PAY_CONF['path_at_images']  = $_CONF['path_images'] . 'paypal/attributes/';
$_PAY_CONF['images_url']  = $_CONF['site_url'] . '/'. substr($_CONF['path_images'], strlen($_CONF['path_html']), -1) . '/paypal/products/';
$_PAY_CONF['images_cat_url']  = $_CONF['site_url'] . '/'. substr($_CONF['path_images'], strlen($_CONF['path_html']), -1) . '/paypal/categories/';
$_PAY_CONF['images_at_url']  = $_CONF['site_url'] . '/'. substr($_CONF['path_images'], strlen($_CONF['path_html']), -1) . '/paypal/attributes/';

/**
 * Paypal plugin table(s)
 */
$_DB_paypal_table_prefix = 'paypal_';
$_TABLES['paypal_ipnlog'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'ipnlog';
$_TABLES['paypal_products'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'products';
$_TABLES['paypal_downloads'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'downloads';
$_TABLES['paypal_purchases'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'purchases';
$_TABLES['paypal_images'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'images';
$_TABLES['paypal_subscriptions'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'subscriptions';
$_TABLES['paypal_categories'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'categories';
$_TABLES['paypal_users'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'users';
$_TABLES['paypal_attributes'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'attributes';
$_TABLES['paypal_attribute_type'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'attribute_type';
$_TABLES['paypal_product_attribute'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'product_attribute';
$_TABLES['paypal_stock'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'stock';
$_TABLES['paypal_delivery'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'delivery';
$_TABLES['paypal_stock_movements'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'stock_movements';
$_TABLES['paypal_providers'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'providers';
$_TABLES['paypal_shipper_service'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'shipper_service';
$_TABLES['paypal_shipping_to'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'shipping_to';
$_TABLES['paypal_shipping_cost'] = $_DB_table_prefix . $_DB_paypal_table_prefix . 'shipping_cost';

/**
 * array of allowed extensions.  make sure that every downloadable file extension
 * is included in the this list.  for security you may want to remove unused file
 * extensions.  also try to avoid php and phps.
 * NOTE: extensions must be defined in $_CONF['path']/system/classes/downloader.class.php
 *       to be listed here.
 */
$_PAY_CONF['allowedextensions'] = array (
    'tgz'  => 'application/x-gzip-compressed',
    'gz'   => 'application/x-gzip-compressed',
    'zip'  => 'application/x-zip-compresseed',
    'tar'  => 'application/x-tar',
    'php'  => 'text/plain',
    'phps' => 'text/plain',
    'txt'  => 'text/plain',
    'html' => 'text/html',
    'htm'  => 'text/html',
    'bmp'  => 'image/bmp',
    'ico'  => 'image/bmp',
    'gif'  => 'image/gif',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/x-png',
    'mp3'  => 'audio/mpeg',
    'wav'  => 'audio/wav',
    'pdf'  => 'application/pdf',
    'swf'  => 'application/x-shockwave-flash',
    'doc'  => 'application/msword',
    'xls'  => 'application/vnd.ms-excel',
    'exe'  => 'application/octet-stream'
);

 $_PAY_CONF['download_path'] = $_CONF['path_data'] . 'private/paypal/files/';
 $_PAY_CONF['logfile'] = $_CONF['path'] . 'logs/paypal_downloads.log';
 $_PAY_CONF['debug'] = false;
 ($_PAY_CONF['debug']) ? define("DEBUG", true) : define("DEBUG", false);
 $_PAY_CONF['days_before_expiration'] = 7; // Notify user 1 week before subscription expiration

/**
 * Paypal products types
 */ 
 $_PAY_CONF['types'] = array (
    'product'       => $LANG_PAYPAL_TYPE['product'],
    'subscription'  => $LANG_PAYPAL_TYPE['subscription'],
    'donation'      => $LANG_PAYPAL_TYPE['donation'],
	'rent'          => $LANG_PAYPAL_TYPE['rent'],
);

/************************************************/
/* WARNING: Items below this line should never be altered */
/* Paypal constants */
define('PAYPAL_FAILURE_UNKNOWN', 0);
define('PAYPAL_FAILURE_VERIFY', 1);
define('PAYPAL_FAILURE_COMPLETED', 2);
define('PAYPAL_FAILURE_UNIQUE', 3);
define('PAYPAL_FAILURE_EMAIL', 4);
define('PAYPAL_FAILURE_FUNDS', 5);


///////////////////////////////////////////////////////////////////////
// REQUIRED SETTINGS

// THE HTML NAME ATTRIBUTES USED IN YOUR ADD-TO-CART FORM
$jcart['item_id']		= 'item_number';			// ITEM ID
$jcart['item_ref']		= 'item_ref';			// ITEM ITEM_ID
$jcart['item_name']		= 'item_name';		// ITEM NAME
$jcart['item_price']	= 'amount';		// ITEM PRICE
$jcart['item_weight']	= 'item_weight';		// ITEM WEIGHT
$jcart['item_qty']		= 'add';		// ITEM QTY
$jcart['item_add']		= 'submit';		// ADD-TO-CART BUTTON

// PATH TO THE DIRECTORY CONTAINING JCART FILES
$jcart['path'] = $_PAY_CONF['path_html'] . 'jcart/';
$jcart['url'] = $_PAY_CONF['site_url'] . '/jcart/';

// THE PATH AND FILENAME WHERE SHOPPING CART CONTENTS SHOULD BE POSTED WHEN A VISITOR CLICKS THE CHECKOUT BUTTON
// USED AS THE ACTION ATTRIBUTE FOR THE SHOPPING CART FORM
$jcart['form_action']	= 'checkout.php';

// YOUR PAYPAL SECURE MERCHANT ACCOUNT ID
$jcart['paypal_id']		= $_PAY_CONF['receiverEmailAddr'];


///////////////////////////////////////////////////////////////////////
// OPTIONAL SETTINGS


// OVERRIDE THE DEFAULT BUTTONS WITH YOUR IMAGES BY SETTING THE PATH FOR EACH IMAGE
$jcart['button']['checkout']				= ''; //$_PAY_CONF['site_url'] . '/images/PayPal_mark_50x34.gif';
//$jcart['button']['paypal_checkout']			= $_PAY_CONF['site_url'] . '/images/horizontal_solution_PPeCheck.gif';
$jcart['button']['paypal_checkout']			= 'https://fpdbs.paypal.com/dynamicimageweb?cmd=_dynamic-image&locale=' . str_replace('.UTF-8', '', $_CONF['locale']); 
$jcart['button']['update']					= '';
$jcart['button']['empty']					= '';

if (!$jcart['path']) die('The path to jCart isn\'t set. Please see <strong>jcart-config.php</strong> for more info.');

$jcart['text']['cart_title']				= $LANG_PAYPAL_CART['cart'];
$jcart['text']['single_item']				= $LANG_PAYPAL_CART['item'];
$jcart['text']['multiple_items']			= $LANG_PAYPAL_CART['items'];
$jcart['text']['currency_symbol']			= $_PAY_CONF['currency'];
$jcart['text']['subtotal']				    = $LANG_PAYPAL_CART['subtotal'];
$jcart['text']['total']				        = $LANG_PAYPAL_CART['total'];

$jcart['text']['update_button']				= $LANG_PAYPAL_CART['update'];
$jcart['text']['checkout_button']			= $LANG_PAYPAL_CART['checkout'];
$jcart['text']['checkout_paypal_button']	= $LANG_PAYPAL_CART['paypal_checkout'];
$jcart['text']['remove_link']				= $LANG_PAYPAL_CART['remove'];
$jcart['text']['empty_button']				= $LANG_PAYPAL_CART['empty'];
$jcart['text']['empty_message']				= $LANG_PAYPAL_CART['cart_empty'];
$jcart['text']['item_added_message']		= $LANG_PAYPAL_CART['added'];

$jcart['text']['price_error']				= $LANG_PAYPAL_CART['invalid_price'];
$jcart['text']['quantity_error']			= $LANG_PAYPAL_CART['item_quantities'];
$jcart['text']['checkout_error']			= $LANG_PAYPAL_CART['error'];

$jcart['text']['description']		    	= $LANG_PAYPAL_CART['description'];
$jcart['text']['unit_price']			    = $LANG_PAYPAL_CART['unit_price'];
$jcart['text']['quantity']			        = $LANG_PAYPAL_CART['quantity'];
$jcart['text']['item_price']			    = $LANG_PAYPAL_CART['item_price'];    

?>