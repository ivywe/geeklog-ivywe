<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Paypal Plugin 1.1                                                         |
// +---------------------------------------------------------------------------+
// | jcart.class.php                                                           |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben - cordiste AT free DOT fr                                  |
// +---------------------------------------------------------------------------+
// | Based on JCART v1.1 & Webforce Cart v.1.5                                 |
// |                                                                           |
// | Copyright (C) 2010 by the following authors:                              |
// | JCART v1.1  http://conceptlogic.com/jcart/                                |
// |                                                                           |
// | Copyright (C) 2004 - 2005 by the following authors:                       |
// | Webforce Ltd, NZ http://www.webforce.co.nz/cart/                          |   
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

define("PAYBYCHECK", false);
	
// JCART
class jcart {
	var $total = 0;
	var $itemcount = 0;
	var $totalweight = 0;
	var $items = array();
	var $itemprices = array();
	var $itemqtys = array();
	var $itemname = array();
	var $itemweights = array();

	// CONSTRUCTOR FUNCTION
	function cart() {}

	// GET CART CONTENTS
	function get_contents()
		{
		$items = array();
		foreach($this->items as $tmp_item)
			{
			$item = FALSE;

			$item['id'] = $tmp_item;
			$item['qty'] = $this->itemqtys[$tmp_item];
			$item['price'] = $this->itemprices[$tmp_item];
			$item['name'] = $this->itemname[$tmp_item];
			$item['weight'] = $this->itemweights[$tmp_item];
			$item['subtotal'] = $item['qty'] * $item['price'];
			$items[] = $item;
			}
		return $items;
		}


	// ADD AN ITEM
	function add_item($item_id, $item_qty=1, $item_price, $item_name, $item_weight)
		{
		// VALIDATION
		$valid_item_qty = $valid_item_price = false;

		// IF THE ITEM QTY IS AN INTEGER, OR ZERO
		if (preg_match("/^[0-9-]+$/i", $item_qty))
			{
			$valid_item_qty = true;
			}
		// IF THE ITEM PRICE IS A FLOATING POINT NUMBER
		if (is_numeric($item_price))
			{
			$valid_item_price = true;
			}

		// ADD THE ITEM
		if ($valid_item_qty !== false && $valid_item_price !== false)
			{
			// IF THE ITEM IS ALREADY IN THE CART, INCREASE THE QTY
			if( $this->itemqtys[$item_id] > 0 )
				{
				$this->itemqtys[$item_id] = $item_qty + $this->itemqtys[$item_id];
				$this->_update_total();
				$this->_update_totalweight();
				}
			// THIS IS A NEW ITEM
			else
				{
				$this->items[] = $item_id;
				$this->itemqtys[$item_id] = $item_qty;
				$this->itemprices[$item_id] = $item_price;
				$this->itemname[$item_id] = $item_name;
				$this->itemweights[$item_id] = $item_weight;
				}
			$this->_update_total();
			$this->_update_totalweight();
			return true;
			}

		else if	($valid_item_qty !== true)
			{
			$error_type = 'qty';
			return $error_type;
			}
		else if	($valid_item_price !== true)
			{
			$error_type = 'price';
			return $error_type;
			}
		}


	// UPDATE AN ITEM
	function update_item($item_id, $item_qty)
		{
		// IF THE ITEM QTY IS AN INTEGER, OR ZERO
		// UPDATE THE ITEM
		if (preg_match("/^[0-9-]+$/i", $item_qty))
			{
			if($item_qty < 1)
				{
				$this->del_item($item_id);
				}
			else
				{
				$this->itemqtys[$item_id] = $item_qty;
				}
			$this->_update_total();
			$this->_update_totalweight();
			return true;
			}
		}


	// UPDATE THE ENTIRE CART
	// VISITOR MAY CHANGE MULTIPLE FIELDS BEFORE CLICKING UPDATE
	// ONLY USED WHEN JAVASCRIPT IS DISABLED
	// WHEN JAVASCRIPT IS ENABLED, THE CART IS UPDATED ONKEYUP
	function update_cart()
		{
		// POST VALUE IS AN ARRAY OF ALL ITEM IDs IN THE CART
		if (is_array($_POST['jcart_item_ids']))
			{
			// TREAT VALUES AS A STRING FOR VALIDATION
			$item_ids = implode($_POST['jcart_item_ids']);
			}

		// POST VALUE IS AN ARRAY OF ALL ITEM QUANTITIES IN THE CART
		if (is_array($_POST['jcart_item_qty']))
			{
			// TREAT VALUES AS A STRING FOR VALIDATION
			$item_qtys = implode($_POST['jcart_item_qty']);
			}

		// IF NO ITEM IDs, THE CART IS EMPTY
		if ($_POST['jcart_item_id'])
			{
			// IF THE ITEM QTY IS AN INTEGER, OR ZERO, OR EMPTY
			// UPDATE THE ITEM
			if (preg_match("/^[0-9-]+$/i", $item_qtys) || $item_qtys == '')
				{
				// THE INDEX OF THE ITEM AND ITS QUANTITY IN THEIR RESPECTIVE ARRAYS
				$count = 0;

				// FOR EACH ITEM IN THE CART
				foreach ($_POST['jcart_item_id'] as $item_id)
					{
					// GET THE ITEM QTY AND DOUBLE-CHECK THAT THE VALUE IS AN INTEGER
					$update_item_qty = intval($_POST['jcart_item_qty'][$count]);

					if($update_item_qty < 1)
						{
						$this->del_item($item_id);
						}
					else
						{
						// UPDATE THE ITEM
						$this->update_item($item_id, $update_item_qty);
						}

					// INCREMENT INDEX FOR THE NEXT ITEM
					$count++;
					}
				return true;
				}
			}
		// IF NO ITEMS IN THE CART, RETURN TRUE TO PREVENT UNNECSSARY ERROR MESSAGE
		else if (!$_POST['jcart_item_id'])
			{
			return true;
			}
		}


	// REMOVE AN ITEM
	/*
	GET VAR COMES FROM A LINK, WITH THE ITEM ID TO BE REMOVED IN ITS QUERY STRING
	AFTER AN ITEM IS REMOVED ITS ID STAYS SET IN THE QUERY STRING, PREVENTING THE SAME ITEM FROM BEING ADDED BACK TO THE CART
	SO WE CHECK TO MAKE SURE ONLY THE GET VAR IS SET, AND NOT THE POST VARS

	USING POST VARS TO REMOVE ITEMS DOESN'T WORK BECAUSE WE HAVE TO PASS THE ID OF THE ITEM TO BE REMOVED AS THE VALUE OF THE BUTTON
	IF USING AN INPUT WITH TYPE SUBMIT, ALL BROWSERS DISPLAY THE ITEM ID, INSTEAD OF ALLOWING FOR USER FRIENDLY TEXT SUCH AS 'remove'
	IF USING AN INPUT WITH TYPE IMAGE, INTERNET EXPLORER DOES NOT SUBMIT THE VALUE, ONLY X AND Y COORDINATES WHERE BUTTON WAS CLICKED
	CAN'T USE A HIDDEN INPUT EITHER SINCE THE CART FORM HAS TO ENCOMPASS ALL ITEMS TO RECALCULATE TOTAL WHEN A QUANTITY IS CHANGED, WHICH MEANS THERE ARE MULTIPLE REMOVE BUTTONS AND NO WAY TO ASSOCIATE THEM WITH THE CORRECT HIDDEN INPUT
	*/
	function del_item($item_id)
		{
		$ti = array();
		$this->itemqtys[$item_id] = 0;
		foreach($this->items as $item)
			{
			if($item != $item_id)
				{
				$ti[] = $item;
				}
			}
		$this->items = $ti;
		$this->_update_total();
		$this->_update_totalweight();
		}


	// EMPTY THE CART
	function empty_cart()
		{
		$this->total = 0;
		$this->itemcount = 0;
		$this->totalweight = 0;
		$this->items = array();
		$this->itemprices = array();
		$this->itemqtys = array();
		$this->itemname = array();
		$this->itemweights = array();
		}


	// INTERNAL FUNCTION TO RECALCULATE TOTAL
	function _update_total()
		{
		$this->itemcount = 0;
		$this->total = 0;
		if(sizeof($this->items > 0))
			{
			foreach($this->items as $item)
				{
				$this->total = $this->total + ($this->itemprices[$item] * $this->itemqtys[$item]);

				// TOTAL ITEMS IN CART (ORIGINAL wfCart COUNTED TOTAL NUMBER OF LINE ITEMS)
				$this->itemcount += $this->itemqtys[$item];
				}
			}
		}

	// INTERNAL FUNCTION TO RECALCULATE TOTALWEIGHT
	function _update_totalweight()
		{
		$this->itemcount = 0;
		$this->totalweight = 0;
		if(sizeof($this->items > 0))
			{
			foreach($this->items as $item)
				{
				$this->totalweight = $this->totalweight + ($this->itemweights[$item] * $this->itemqtys[$item]);

				// TOTAL ITEMS IN CART (ORIGINAL wfCart COUNTED TOTAL NUMBER OF LINE ITEMS)
				$this->itemcount += $this->itemqtys[$item];
				}
			}
		}

	// PROCESS AND DISPLAY CART
	function display_cart($jcart, $block=0)
		{
		global $_CONF, $_PAY_CONF, $LANG_PAYPAL_1, $LANG_PAYPAL_CART, $_USER, $_TABLES, $LANG_PAYPAL_ADMIN, $_SCRIPTS;
		
		// JCART ARRAY HOLDS USER CONFIG SETTINGS
		extract($jcart);

		// ASSIGN USER CONFIG VALUES AS POST VAR LITERAL INDICES
		// INDICES ARE THE HTML NAME ATTRIBUTES FROM THE USERS ADD-TO-CART FORM
		$item_id = $_POST[$item_id];
		$item_qty = $_POST[$item_qty];
		$item_price = $_POST[$item_price];
		//Todo if block==1 shorten name
		$item_name = $_POST[$item_name];
		$item_weight = $_POST[$item_weight];

		// ADD AN ITEM
		if ($_POST[$item_add])
			{
			$item_added = $this->add_item($item_id, $item_qty, $item_price, $item_name, $item_weight);
			// IF NOT TRUE THE ADD ITEM FUNCTION RETURNS THE ERROR TYPE
			if ($item_added !== true)
				{
				$error_type = $item_added;
				switch($error_type)
					{
					case 'qty':
						$error_message = $text['quantity_error'];
						break;
					case 'price':
						$error_message = $text['price_error'];
						break;
					}
				}
			}

		// UPDATE A SINGLE ITEM
		// CHECKING POST VALUE AGAINST $text ARRAY FAILS?? HAVE TO CHECK AGAINST $jcart ARRAY
		if ($_POST['jcart_update_item'] == $jcart['text']['update_button'])
			{
			$item_updated = $this->update_item($_POST['item_id'], $_POST['item_qty']);
			if ($item_updated !== true)
				{
				$error_message = $text['quantity_error'];
				}
			}

		// UPDATE ALL ITEMS IN THE CART
		if($_POST['jcart_update_cart'] || $_POST['jcart_checkout'])
			{
			$cart_updated = $this->update_cart();
			if ($cart_updated !== true)
				{
				$error_message = $text['quantity_error'];
				}
			}

		// REMOVE AN ITEM
		if($_GET['jcart_remove'] && !$_POST[$item_add] && !$_POST['jcart_update_cart'] && !$_POST['jcart_check_out'])
			{
			$this->del_item($_GET['jcart_remove']);
			}

		// EMPTY THE CART
		if($_POST['jcart_empty'])
			{
			$this->empty_cart();
			}

		// DETERMINE WHICH TEXT TO USE FOR THE NUMBER OF ITEMS IN THE CART
		if ($this->itemcount > 1)
			{
			$text['items_in_cart'] = $text['multiple_items'];
			}
		if ($this->itemcount <= 1)
			{
			$text['items_in_cart'] = $text['single_item'];
			}

		// DETERMINE IF THIS IS THE CHECKOUT PAGE
		// WE FIRST CHECK THE REQUEST URI AGAINST THE USER CONFIG CHECKOUT (SET WHEN THE VISITOR FIRST CLICKS CHECKOUT)
		// WE ALSO CHECK FOR THE REQUEST VAR SENT FROM HIDDEN INPUT SENT BY AJAX REQUEST (SET WHEN VISITOR HAS JAVASCRIPT ENABLED AND UPDATES AN ITEM QTY)
		$is_checkout = strpos($_SERVER['REQUEST_URI'], $form_action);
		if ($is_checkout !== false || $_REQUEST['jcart_is_checkout'] == 'true')
			{
			$is_checkout = true;
			}
		else
			{
			$is_checkout = false;
			}
			
		$retval = '';

		// OVERWRITE THE CONFIG FORM ACTION TO POST TO jcart-gateway.php INSTEAD OF POSTING BACK TO CHECKOUT PAGE
		// THIS ALSO ALLOWS US TO VALIDATE PRICES BEFORE SENDING CART CONTENTS TO PAYPAL
		if ($is_checkout == true) {
			$form_action = $_PAY_CONF['site_url'] . '/jcart/jcart-gateway.php';
		} else {
			$form_action = $_PAY_CONF['site_url'] . '/checkout.php';
		}

		// DEFAULT INPUT TYPE
		// CAN BE OVERRIDDEN IF USER SETS PATHS FOR BUTTON IMAGES
		$input_type = 'submit';

		// IF THIS ERROR IS TRUE THE VISITOR UPDATED THE CART FROM THE CHECKOUT PAGE USING AN INVALID PRICE FORMAT
		// PASSED AS A SESSION VAR SINCE THE CHECKOUT PAGE USES A HEADER REDIRECT
		// IF PASSED VIA GET THE QUERY STRING STAYS SET EVEN AFTER SUBSEQUENT POST REQUESTS
		if ($_SESSION['quantity_error'] == true) {
			$error_message = $text['quantity_error'];
			unset($_SESSION['quantity_error']);
		}

		// OUTPUT THE CART
		if ($is_checkout == true && $block == 1) {
		    return $LANG_PAYPAL_CART['checkout'] . '...';
		}

		// DISPLAY THE CART HEADER
		$cart = COM_newTemplate($_CONF['path'] . 'plugins/paypal/templates');
		if ($_REQUEST['pay_by'] == 'check' && $block == 0) {
		    $cart->set_file(array('cart_start'   => 'cart_start_check.thtml',
                                  'cart_item'    => 'cart_item_check.thtml',
								  'cart_empty'   => 'cart_empty.thtml',
                                  'cart_end'     => 'cart_end_check.thtml'));
		} 
		else if ($block == 0) {
            $cart->set_file(array('cart_start'   => 'cart_start.thtml',
                                  'cart_item'    => 'cart_item.thtml',
								  'cart_empty'   => 'cart_empty.thtml',
                                  'cart_end'     => 'cart_end.thtml'));
		} else {
		    $cart->set_file(array('cart_start'   => 'cart_block_start.thtml',
                                  'cart_item'    => 'cart_block_item.thtml',
								  'cart_empty'   => 'cart_empty.thtml',
                                  'cart_end'     => 'cart_block_end.thtml'));
		}
		
		if ($is_checkout == true)
		{
			$steps = '<div class="uk-width-1-1 uk-margin uk-margin-top"><div class="uk-child-width-1-2@s uk-child-width-1-3@m uk-text-center uk-button-group">
			        <button class="uk-button uk-button-secondary uk-margin-small-bottom uk-text-nowrap">' . $LANG_PAYPAL_1['checkout_step_1'] . '</button>
							<button class="uk-button uk-button-default uk-margin-small-bottom uk-text-nowrap">' . $LANG_PAYPAL_1['checkout_step_2'] . '</button>
							<button class="uk-button uk-button-default uk-margin-small-bottom uk-text-nowrap">' . $LANG_PAYPAL_1['checkout_step_3'] . '</button>
						</div></div>';
			$cart->set_var('steps', $steps);
		} else if ($_REQUEST['pay_by'] == 'check' || PAYBYCHECK == true) {
		    PAYBYCHECK == true;
			$steps = '<div class="uk-width-1-1 uk-marginuk-margin-top"><div class="uk-child-width-1-2@s uk-child-width-1-3@m uk-text-center uk-button-group">
			        <button class="uk-button uk-button-default uk-margin-small-bottom uk-text-nowrap">' . $LANG_PAYPAL_1['checkout_step_1'] . '</button>
							<button class="uk-button uk-button-secondary uk-margin-small-bottom uk-text-nowrap">' . $LANG_PAYPAL_1['checkout_step_2'] . '</button>
							<button class="uk-button uk-button-default uk-margin-small-bottom uk-text-nowrap">' . $LANG_PAYPAL_1['checkout_step_3'] . '</button>
						</div></div>';
			$cart->set_var('steps', $steps);
		} else {
			$cart->set_var('steps', '');
		}
		
		if ($_REQUEST['pay_by'] == 'check' && $block == 0) {
			// Get details to edit and display the form on informations.php page
			if (!COM_isAnonUser()) {
				$sql = "SELECT * FROM {$_TABLES['paypal_users']} WHERE user_id = {$_USER['uid']}";
				$res = DB_query($sql);
				$A = DB_fetchArray($res);
				if ($A['user_id'] == '' && SEC_hasRights('paypal.admin')) {
					$A['user_id'] = $_REQUEST['uid'];
				}
				if ($A['user_id'] == '') {
					$A['user_id'] = $_USER['uid'];
				}
				$informations = '<h2>' . $LANG_PAYPAL_1['review_details'] . '</h2>'; 
				$informations .= '<p>' . $LANG_PAYPAL_1['confirm_order_check'] . '</p>';
				$informations .= '<div class="uk-margin">' . PAYPAL_getDetailsForm($A, $_PAY_CONF['site_url'] . '/details.php?mode=save', $LANG_PAYPAL_1['confirm_order_button'], $_GET['shipping']) . '</div>';
				$cart->set_var('informations', $informations);
			}	
		}
		// IF THERE'S AN ERROR MESSAGE WRAP IT IN SOME HTML
		if ($error_message) {
			$error_message = "<p class='jcart-error'>$error_message</p>";
			$cart->set_var('error_message', $error_message);
		} else {
			$cart->set_var('error_message', '');
		}
        $cart->set_var('xhtml', XHTML);
		$cart->set_var('form_action', $form_action);
		$cart->set_var('cart_title', $text['cart_title']);
		$cart->set_var('itemcount', $this->itemcount . "&nbsp;" . $text['items_in_cart']);
		$cart->set_var('description', $text['description']);
		$cart->set_var('unit_price', $text['unit_price']);
		$cart->set_var('quantity', $text['quantity']);
		$cart->set_var('item_price', $text['item_price']);

    	$retval .= $cart->parse('', 'cart_start');

		// IF ANY ITEMS IN THE CART
		if($this->itemcount > 0) {
		    $categories = array();
			// DISPLAY LINE ITEMS
			foreach($this->get_contents() as $item) {
				// ADD THE ITEM ID AS THE INPUT ID ATTRIBUTE
				// THIS ALLOWS US TO ACCESS THE ITEM ID VIA JAVASCRIPT ON QTY CHANGE, AND THEREFORE UPDATE THE CORRECT ITEM
				// NOTE THAT THE ITEM ID IS ALSO PASSED AS A SEPARATE FIELD FOR PROCESSING VIA PHP
				
				$cart->set_var('name', $item['name']);
				$cart->set_var('id', $item['id']);
				//GET ALL PRODUCTS CATEGORIES
				$cat = DB_getItem($_TABLES['paypal_products'], 'cat_id', 'id='. PAYPAL_realId($item['id']));
				if ($cat != 0) 	$categories[] .= $cat;
				$cart->set_var('price', number_format($item['price'], $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator']));
				$cart->set_var('currency_symbol', $text['currency_symbol']);
				$cart->set_var('qty', $item['qty']);
				$cart->set_var('subtotal', number_format($item['subtotal'], $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator']));
				$cart->set_var('remove_png', $_PAY_CONF['site_url']. '/images/remove.png');
				$cart->set_var('remove', $LANG_PAYPAL_CART['remove']);
				$retval .= $cart->parse('', 'cart_item');
			}
		}

		// THE CART IS EMPTY
		else
			{
			$cart->set_var('empty', '<p>' . $text['empty_message'] . '</p>');
			$retval .= $cart->parse('', 'cart_empty');
			}

		// DISPLAY THE CART FOOTER

		//Subtotal
		($block == 0) ? $cart->set_var('subtotal', $text['subtotal'] . number_format($this->total,$_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator']) 
		. ' ' . $text['currency_symbol'] . '</strong>') : $cart->set_var('subtotal', number_format($this->total,$_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator']) 
		. ' ' . $text['currency_symbol'] . '</strong>');
		
		// IF THIS IS THE CHECKOUT HIDE THE CART CHECKOUT BUTTON
		if ($is_checkout !== true && $_REQUEST['pay_by'] != 'check') {
			if ($button['checkout']) {
    			$input_type = 'image';
				$src = ' src="' . $button['checkout'] . '" alt="' . $text['checkout_button'] . '" title="" ';
			}
			$cart->set_var('checkout', '<button type="' . $input_type . '" ' . $src . 'id="jcart-checkout" name="jcart_checkout" class="uk-button uk-button-primary uk-button-large uk-text-right" value="' . $text['checkout_button']
 . '">' . $text['checkout_button'] . ' &#62;&#62;</button>');
		} else {
		    $cart->set_var('checkout', '');
		}
			
		$retval .= $cart->parse('', 'cart_end');
		
/*
		//Update and empty button
		if ($block == 0) {
		    $retval .= "<div class='jcart-hide uk-text-right'>";
		    if ($button['update']) { $input_type = 'image'; $src = ' src="' . $button['update'] . '" alt="' . $text['update_button'] . '" title="" ';	}
		    $retval .= "<input type='" . $input_type . "' " . $src ."name='jcart_update_cart' value='" . $text['update_button'] . "' class='uk-button uk-button-primary uk-button-mini' />";
		    if ($button['empty']) { $input_type = 'image'; $src = ' src="' . $button['empty'] . '" alt="' . $text['empty_button'] . '" title="" ';	}
		    $retval .= "<input type='" . $input_type . "' " . $src ."name='jcart_empty' value='" . $text['empty_button'] . "' class='jcart-button' />";
		    $retval .= "</div>";
		}
*/

		$retval .= "</th>";
		$retval .= "</tr>";
		$retval .= "</table>";
		
		// IF THIS IS THE CHECKOUT DISPLAY THE PAYPAL CHECKOUT BUTTON AND SHIPPING RATE
		if ( ($is_checkout == true  && $block == 0 && ($this->itemcount > 0)) || $_REQUEST['pay_by'] == 'check' && $block == 0) {
			// HIDDEN INPUT ALLOWS US TO DETERMINE IF WE'RE ON THE CHECKOUT PAGE
			// WE NORMALLY CHECK AGAINST REQUEST URI BUT AJAX UPDATE SETS VALUE TO jcart-relay.php
			$retval .= "<input type='hidden' id='jcart-is-checkout' name='jcart_is_checkout' value='true' />";
			
			$weight = $this->totalweight;
			$weight = str_replace(",",".",$weight);
		    $weight = preg_replace('/[^\d.]/', '', $weight);
			//WEIGHT
			$retval .= "<input type='hidden' id='weight' name='weight' value='{$weight}' />";
			
			//SHIPPING RATE
			$shipping = COM_newTemplate($_CONF['path'] . 'plugins/paypal/templates');
			$shipping->set_file(array('cart_shipping'   => 'cart_shipping.thtml'));
			$shipping->set_var('choose_shipping', $LANG_PAYPAL_CART['choose_shipping']);
			if ($weight > 0) {		
				//SHIPPER SERVICE
				$sql = "SELECT
						*
					FROM {$_TABLES['paypal_shipping_cost']} AS sc
					LEFT JOIN {$_TABLES['paypal_shipper_service']} AS ss
					ON sc.shipping_shipper_id = ss.shipper_service_id
					LEFT JOIN {$_TABLES['paypal_shipping_to']} AS st
					ON sc.shipping_destination_id = st.shipping_to_id
					WHERE '{$weight}' > sc.shipping_min AND '{$weight}' < sc.shipping_max
					ORDER by st.shipping_to_order, sc.shipping_amt ASC
					";
				$res = DB_query($sql);
				if (DB_numRows($res) > 0) {
				    $i = 0;
				    while ($A = DB_fetchArray($res)) {
					    if ($_GET['shipping'] != '' && $_GET['shipping'] == $A['shipping_amt'] ) {
						    $checked = ' checked';
							$skip = 0;
						} else if ($_GET['shipping'] != '') {
						    $checked = '';
							$skip = 1; 
						} else if ($i == 0) {
							$checked = ' checked';
						} else { 
							$checked = '';
						}
						if ( ( (count($categories) == 1 && in_array($A['shipper_service_exclude_cat'], $categories)) || $A['shipper_service_exclude_cat'] == 0 || count($categories) == 0 ) && $skip == 0 ) {
    					    $shippers_radio .= '<p><input type="radio" name="shipping" value="' . $A['shipping_amt'] . '"' . $checked . ' /> '  . $A['shipping_to_name'] . ' | ' . $A['shipper_service_name'] .  ' - ' . $A['shipper_service_service'] .  '<span ' . $A['shipping_amt'] .
	    					' ' . $_PAY_CONF['currency'] . '</span></p>' . LB;
							$i++;
						}
				    }
				} else {
				     $shippers_radio = '<input type="radio" name="shipping" value="0.00" checked /> ' . $LANG_PAYPAL_CART['free_shipping'] . '<span>+ 0 ' . $_PAY_CONF['currency'] . '</span>';
				}

			} else {
			    $shippers_radio = '<input type="radio" name="shipping" value="0.00" checked /> ' . $LANG_PAYPAL_CART['free_shipping'] .
				'<span>+ 0 ' . $_PAY_CONF['currency'] . '</span>';
			}
			
			$shipping->set_var('shipping_radio_buttons', $shippers_radio);
			$retval .= $shipping->parse('', 'cart_shipping');

			// SEND THE URL OF THE CHECKOUT PAGE TO jcart-gateway.php
			// WHEN JAVASCRIPT IS DISABLED WE USE A HEADER REDIRECT AFTER THE UPDATE OR EMPTY BUTTONS ARE CLICKED
			$protocol = 'http://'; if (!empty($_SERVER['HTTPS'])) { $protocol = 'https://'; }
			$retval .= '<div class="uk-text-center"><input type="hidden" id="jcart-checkout-page" name="jcart_checkout_page" value="' . $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" />';

            // PAYPAL CHECKOUT BUTTON
			if ($button['paypal_checkout'])	{ 
                $input_type = 'image';
                $src = ' src="' . $button['paypal_checkout'] . '" alt="' . $text['checkout_paypal_button'] . '" title="" '; 
            }
			if ($_REQUEST['pay_by'] != 'check') {


				if ($is_checkout == true  && $block == 0 && ($this->itemcount > 0) && $_PAY_CONF['enable_pay_by_ckeck'] == 1) {
					if (!COM_isAnonUser()) {
							$js = 'function payby ( selectedtype )';
							$js .= '{';
							$js .= '  document.jcart.pay_by.value = selectedtype ;';
							$js .= '  document.jcart.submit() ;';
							$js .= '}';
							
							$_SCRIPTS->setJavaScript($js, true);

							$retval .= '<input type="hidden" name="pay_by" />';
							$retval .= '<a class="uk-button uk-button-primary" href="javascript:payby(\'check\')">' . $LANG_PAYPAL_CART['payment_check'] . ' <span uk-icon="icon: arrow-right"></span></a>';
					} else {
							$retval .= '<a href="https://ivywe.co.jp/bioclean/users.php" class="uk-button uk-button-default">銀行振り込みによる購入は先にログインしてください。</a>';
					}
				}
				if ($_PAY_CONF['enable_pay_by_paypal']) {
					$retval .= '<input class="jcart_footer uk-button uk-button-danger" type="' . $input_type . "' " . $src ."id='jcart-paypal-checkout' name='jcart_paypal_checkout' value='" .
						$text['checkout_paypal_button'] . "'" . $disable_paypal_checkout . ' style="border-radius:500px" />';	
				}


				$retval .= '</div><p class="uk-text-center">' . $LANG_PAYPAL_1['payment_method'] . '</p>';


			}
		}
		$retval .= "\t</form>";

		// IF UPDATING AN ITEM, FOCUS ON ITS QTY INPUT AFTER THE CART IS LOADED (DOESN'T SEEM TO WORK IN IE7)
		if ($_POST['jcart_update_item'])
			{
			$retval .= "\t" . '<script type="text/javascript">jQuery(function(){jQuery("#jcart-item-id-' . $_POST['item_id'] . '").focus()});</script>' . "";
			}
		
        $retval .= "\t<div class=\"jcart_footer\">";
		
		//CONTINUE SHOPPING
        if ($is_checkout == true  && $block == 0) {

           $retval .= '<div><a class="uk-button uk-button-primary uk-float-left" href="' . $_PAY_CONF['site_url'] . '/index.php"><span uk-icon="icon: arrow-left"></span>' . $LANG_PAYPAL_CART['continue_shopping'] . '</a></div>';

/*
            $retval .= '<div><a class="uk-button uk-button-primary uk-float-left" href="' . $_PAY_CONF['site_url'] . '/index.php">

<span class="uk-margin-small-right uk-icon" uk-icon="icon: arrow-left"></span>


 ' . $LANG_PAYPAL_CART['continue_shopping'] . '</a></div>';
*/

        }
		
		$retval .= "\t</div></div>";
						
		return $retval;

		}
	}
?>