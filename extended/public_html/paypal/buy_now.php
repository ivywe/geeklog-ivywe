<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin - geeklog CMS                                              |
// +--------------------------------------------------------------------------+
// | buy_now.php                                                          |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben - cordiste AT free DOT fr                                  |
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

/**
 * require core geeklog code
 */
require_once '../lib-common.php';

// Incoming variable filter
$vars = array('item_number' => 'number',
              'amount' => 'text',
			  'shipping' => 'number'
			 );
paypal_filterVars($vars, $_POST);

$valid_process = true;
$item_id = $_POST['item_number'];
$item_price = $_POST['amount'];
$paypalURL = 'https://' . $_PAY_CONF['paypalURL'] . '/cgi-bin/webscr?cmd=_xclick';


/* MAIN */

$A = DB_fetchArray(DB_query("SELECT * FROM {$_TABLES['paypal_products']} WHERE id = '{$item_id}' LIMIT 1"));

if ($item_price <> $A['price'] || !SEC_hasAccess2($A) || $A['active'] != '1') $valid_process = false;

$PAYPAL_POST['business'] = $_PAY_CONF['receiverEmailAddr'];
$PAYPAL_POST['item_name'] = $A['name'];
$PAYPAL_POST['custom'] = $_USER['uid'];
$PAYPAL_POST['item_number'] = $A['id'];
$PAYPAL_POST['amount'] = $A['price'];
$PAYPAL_POST['no_note'] = '1';
$PAYPAL_POST['currency_code'] = $_PAY_CONF['currency'];
$PAYPAL_POST['return'] = $_PAY_CONF['site_url'] . '/index.php?mode=endTransaction';
$PAYPAL_POST['notify_url'] = $_PAY_CONF['site_url'] . '/ipn.php';
//TODO how to choose shipping cost? Do not use Buy now button...
$PAYPAL_POST['handling_cart'] = $_POST['shipping'];
$PAYPAL_POST['rm'] = '2';
$PAYPAL_POST['cbt'] = $LANG_PAYPAL_1['cbt'] . ' ' . $_CONF['site_name'];
$PAYPAL_POST['cancel_return'] = $_PAY_CONF['site_url'] . '/index.php?mode=cancel';
$PAYPAL_POST['image_url'] = $_PAY_CONF['image_url'];
$PAYPAL_POST['cpp_header_image'] = $_PAY_CONF['cpp_header_image'];
$PAYPAL_POST['cpp_headerback_color'] = $_PAY_CONF['cpp_headerback_color'];
$PAYPAL_POST['cpp_headerborder_color'] = $_PAY_CONF['cpp_headerborder_color'];
$PAYPAL_POST['cpp_payflow_color'] = $_PAY_CONF['cpp_payflow_color'];
$PAYPAL_POST['cs'] = $_PAY_CONF['cs'];
$PAYPAL_POST['charset'] = $_CONF['default_charset'];


foreach ($PAYPAL_POST as $key => $value) {
    $value = urlencode(stripslashes($value));
    $req .= "&$key=$value";
}

if ($valid_process) {
    header('Location:'. $paypalURL .$req);
    exit;
} else {
    die($jcart['text']['checkout_error']);
}
?>