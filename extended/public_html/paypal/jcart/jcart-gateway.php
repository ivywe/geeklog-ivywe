<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Paypal Plugin 1.1                                                         |
// +---------------------------------------------------------------------------+
// | jcart-gateway.php                                                         |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben - cordiste AT free DOT fr                                  |
// +---------------------------------------------------------------------------+
// | Based on JCART v1.1                                                       |
// |                                                                           |
// | Copyright (C) 2010 by the following authors:                              |
// | JCART v1.1  http://conceptlogic.com/jcart/                                |
// |                                                                           |   
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

/**
 * require core geeklog code
 */
require_once '../../lib-common.php';

// START SESSION
session_start();

// INITIALIZE JCART AFTER SESSION START
$cart =& $_SESSION['jcart']; if(!is_object($cart)) $cart = new jcart();

// WHEN JAVASCRIPT IS DISABLED THE UPDATE AND EMPTY BUTTONS ARE DISPLAYED
// RE-DISPLAY THE CART IF THE VISITOR CLICKS EITHER BUTTON
if ($_POST['jcart_update_cart']  || $_POST['jcart_empty'])
	{

	// UPDATE THE CART
	if ($_POST['jcart_update_cart'])
		{
		$cart_updated = $cart->update_cart();
		if ($cart_updated !== true)
			{
			$_SESSION['quantity_error'] = true;
			}
		}

	// EMPTY THE CART
	if ($_POST['jcart_empty'])
		{
		$cart->empty_cart();
		}

	// REDIRECT BACK TO THE CHECKOUT PAGE
	header('Location: ' . $_POST['jcart_checkout_page']);
	exit;
	}

// THE VISITOR HAS CLICKED THE PAYPAL CHECKOUT BUTTON
else 
	{

	///////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////
	/*

	A malicious visitor may try to change item prices before checking out,
	either via javascript or by posting from an external script.

	Here you can add PHP code that validates the submitted prices against
	your database or validates against hard-coded prices.

	The cart data has already been sanitized and is available thru the
	$cart->get_contents() function. For example:

	foreach ($cart->get_contents() as $item)
		{
		$item_id	= $item['id'];
		$item_name	= $item['name'];
		$item_price	= $item['price'];
		$item_qty	= $item['qty'];
		}

	*/
	///////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////

	$valid_prices = true;

	foreach ($cart->get_contents() as $item)
		{
		$realid = COM_sanitizeID(explode("|", $item['id']));
	    $item_id	= $realid[0];
		$item_price	= $item['price'];
        $A = DB_fetchArray(DB_query("SELECT * FROM {$_TABLES['paypal_products']} WHERE id = '{$item_id}' LIMIT 1"));
		$price = $A['price'];
		if ($A['discount_a'] != '' && $A['discount_a'] != 0) {
    	    $price = number_format($A['price'] - $A['discount_a'], 2, '.', '');
    	}
    	if ($A['discount_p'] != '' && $A['discount_p'] != 0) {
    		$price = number_format($A['price'] - ($A['price'] * ($A['discount_p']/100)), 2, '.', '');
		}
        if ($item_price <> $price || !SEC_hasAccess2($A) || $A['active'] != '1') $valid_prices = false;
		}

	///////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////

	// IF THE SUBMITTED PRICES ARE NOT VALID
	if ($valid_prices !== true)
		{
		// KILL THE SCRIPT
		die($jcart['text']['checkout_error']);
		}

	// PRICE VALIDATION IS COMPLETE
	// SEND CART CONTENTS TO PAYPAL USING THEIR UPLOAD METHOD, FOR DETAILS SEE http://tinyurl.com/djoyoa
	else if ($valid_prices === true)
		{
			if ($_POST['pay_by'] == 'check') {
			   echo COM_refresh($_PAY_CONF['site_url'] . '/informations.php?shipping=' . $_POST['shipping'] . '&pay_by=check');
			   exit();
			} else {
				// PAYPAL COUNT STARTS AT ONE INSTEAD OF ZERO
				$paypal_count = 1;
				$items_query_string;
				foreach ($cart->get_contents() as $item)
					{
					// BUILD THE QUERY STRING
					$items_query_string .= '&item_number_' . $paypal_count . '=' . $item['id'];
					$items_query_string .= '&item_name_' . $paypal_count . '=' . urlencode($item['name']);
					$items_query_string .= '&amount_' . $paypal_count . '=' . $item['price'];
					$items_query_string .= '&quantity_' . $paypal_count . '=' . $item['qty'];

					// INCREMENT THE COUNTER
					++$paypal_count;
					}
				
				$items_query_string .= '&currency_code=' . $_PAY_CONF['currency'];
				$items_query_string .= '&cancel_return=' . urlencode($_PAY_CONF['site_url'] . '/index.php?mode=cancel');
				$items_query_string .= '&return=' . urlencode($_PAY_CONF['site_url'] . '/index.php?mode=endTransaction');
				$items_query_string .= '&notify_url=' . urlencode($_PAY_CONF['site_url'] . '/ipn.php');
				$items_query_string .= '&rm=2';
				$items_query_string .= '&no_note=1';

				$items_query_string .= '&handling_cart=' . $_POST['shipping'];
				//$items_query_string .= '&shipping_cart=' . $_POST['shipping'];
				$items_query_string .= '&custom=' . $_USER['uid'];
				$items_query_string .= '&cbt=' . urlencode($LANG_PAYPAL_1['cbt'] . ' ' . $_CONF['site_name']);
				$items_query_string .= '&charset=' . $_CONF['default_charset'];
				if ($_PAY_CONF['image_url']) {
					$items_query_string .= '&image_url=' . urlencode($_PAY_CONF['image_url']);
				}
				if ($_PAY_CONF['cpp_header_image']) {
					$items_query_string .= '&cpp_header_image=' . urlencode($_PAY_CONF['cpp_header_image']);
				}
				if ($_PAY_CONF['cpp_headerback_color']) {
					$items_query_string .= '&cpp_headerback_color=' . $_PAY_CONF['cpp_headerback_color'];
				}
				if ($_PAY_CONF['cpp_headerborder_color']) {
					$items_query_string .= '&cpp_headerborder_color=' . $_PAY_CONF['cpp_headerborder_color'];
				}
				if ($_PAY_CONF['cpp_payflow_color']) {
					$items_query_string .= '&cpp_payflow_color=' . $_PAY_CONF['cpp_payflow_color'];
				}
				if ($_PAY_CONF['cs']) {
				 $items_query_string .= '&cs=' . $_PAY_CONF['cs'];
				}
							 
				// REDIRECT TO PAYPAL WITH MERCHANT ID AND CART CONTENTS
				header( 'Location: https://' . $_PAY_CONF['paypalURL'] . '/cgi-bin/webscr?cmd=_cart&upload=1&business=' . $jcart['paypal_id'] . $items_query_string);
			}
		}
	}

?>
