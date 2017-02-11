<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin - geeklog CMS                                              |
// +--------------------------------------------------------------------------+
// | confirmation.php                                                         |
// |                                                                          |
// | Check page for users of the paypal plugin                                |
// |                                                                          |
// | By default displays available products along with links to purchase      |
// | history and detailed product views                                       |
// +--------------------------------------------------------------------------+
// |                                                                          |
// | Copyright (C) 2011 by the following authors:                             |
// |                                                                          |
// | Authors: Ben     -    ben AT geeklog DOT fr                              |
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

// START SESSION
session_start();
// INITIALIZE JCART AFTER SESSION START
$cart =& $_SESSION['jcart']; 

// take user back to the homepage if the plugin is not active
if (! in_array('paypal', $_PLUGINS) || COM_isAnonUser() || ($cart->itemcount) < 1) {
    echo COM_refresh($_CONF['site_url'] . '/index.php');
    exit;
}

/* Ensure sufficient privs to read this page */
paypal_access_check('paypal.user');

$vars = array('msg' => 'text',
              'shipping' => 'text'
              );
paypal_filterVars($vars, $_REQUEST);

/* valid price, access and active product only */
$items = array();
$i = 1;
$quantities = array();
$valid_prices = true;
foreach ($cart->get_contents() as $item) {
    $realid = PAYPAL_realId($item['id']);
	$item_id	= $realid[0];
	$items[$i] = $item['id'];
	$namesfromcart[$i] = $item['name'];
	$quantities[$i] = $item['qty'];
	$item_price[$i]	= $item['price'];
	$A = DB_fetchArray(DB_query("SELECT * FROM {$_TABLES['paypal_products']} WHERE id = '{$item_id}' LIMIT 1"));
	if ($item_price[$i] <> PAYPAL_productPrice($A) || !SEC_hasAccess2($A) || $A['active'] != '1') $valid_prices = false;
	$i++;
}
if ($valid_prices !== true) {
	echo COM_refresh($_CONF['site_url'] . '/index.php');
	exit;
}


//Main

// EMPTY THE CART
$cart->empty_cart();

$display .= PAYPAL_siteHeader();
$display .= paypal_user_menu();

switch ($_REQUEST['mode']) {
		
	default :

        //Display cart
        $display .= '
		             <div class="uk-width-1-1 uk-margin uk-margin-small-bottom "><div class="uk-child-width-1-2@s uk-child-width-1-3@m uk-text-center uk-button-group">
			           <button class="uk-button uk-button-default uk-margin-small-bottom uk-text-nowrap">' . $LANG_PAYPAL_1['checkout_step_1'] . '</li>
                 <button class="uk-button uk-button-default uk-margin-small-bottom uk-text-nowrap">' . $LANG_PAYPAL_1['checkout_step_2'] . '</li>
							   <button class="uk-button uk-button-secondary uk-margin-small-bottom uk-text-nowrap">' . $LANG_PAYPAL_1['checkout_step_3'] . '</div>
						</div></div>';

		$display .= PAYPAL_handlePurchase($items, $quantities, $data, $namesfromcart, $item_price);
		
        $display .= PAYPAL_siteFooter();
}

COM_output($display);

?>