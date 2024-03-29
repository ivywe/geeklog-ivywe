<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin - geeklog CMS                                             |
// +--------------------------------------------------------------------------+
// | index.php                                                                |
// |                                                                          |
// | Index page for users of the paypal plugin                                |
// |                                                                          |
// | By default displays available products along with links to purchase      |
// | history and detailed product views                                       |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2021 by the following authors:                             |
// |                                                                          |
// | Authors: Vincent Furia     - vinny01 AT users DOT sourceforge DOT net    |
// | Authors: Hiroron    - hiroron AT hiroron DOT com                         |
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
 * Index page for users of the paypal plugin
 *
 * By default displays available products along with links to purchase history
 * and detailed product views
 *
 * @author Vincent Furia <vinny01 AT users DOT sourceforge DOT net>
 * @copyright Vincent Furia 2005 - 2006
 * @package paypal
 * @todo Add more complex logic to decide link display between:  purchase, login, download
 */

/**
 * require core geeklog code
 */
require_once '../lib-common.php';

// take user back to the homepage if the plugin is not active
if (! in_array('paypal', $_PLUGINS)) {
    echo COM_refresh($_CONF['site_url'] . '/index.php');
    exit;
}

/* Ensure sufficient privs to read this page */
paypal_access_check('paypal.viewer');

$vars = array('msg'      => 'text',
              'page'     => 'number',
              'category' => 'number',
			  'type'     => 'text',
			  'n'        => 'text',
              );
paypal_filterVars($vars, $_REQUEST);


//Main

$content = '';
$pagetitle = $_PAY_CONF['seo_shop_title'];
$n = Geeklog\Input::request('n', '');
if ($n != '') {
    $pagetitle = $n . ' | ' . $_PAY_CONF['seo_shop_title'];
}

if (SEC_hasRights('paypal.user', 'paypal.admin')) {
    $content .= paypal_user_menu();
} else {
    $content .= paypal_viewer_menu();
}

$mode = Geeklog\Input::request('mode', '');
switch ($mode) {
    case 'endTransaction':
	    // START SESSION
        session_start();
		// INITIALIZE JCART AFTER SESSION START
		$cart = '';
		if (!isset($_SESSION['jcart'])) {
			$cart = new jcart();
			$_SESSION['jcart'] = serialize($cart);
		}
		$cart =& $_SESSION['jcart']; 
		$cart = unserialize(serialize($cart));
		if(!is_object($cart)) $cart = new jcart();
		// EMPTY THE CART
		$cart->empty_cart();
		
		$msg = $LANG_PAYPAL_1['thanks_details'];
		$msg .= '<p>' . $LANG_PAYPAL_1['transaction'] . ' ' . $_POST['txn_id'] . '</p>';
		$msg .= '<p>' . $LANG_PAYPAL_1['name_label'] . ' ' . $_POST['first_name'] . ' ' . $_POST['last_name'] . ' | ' . $LANG_PAYPAL_1['email'] . ' ' . $_POST['payer_email'] . '</p><ul>';
		for ($i = 1; $i <= $_POST['num_cart_items']; $i++) {
            $msg .= '<li>' . $_POST["quantity$i"] . 'x '. $_POST["item_name$i"] . '... '  . $_POST["mc_gross_$i"] . ' ' . $_POST['mc_currency'];
        }
		$msg .=  '</ul><p>' . $LANG_PAYPAL_1['total']  . ' ' . $_POST['mc_gross'] . ' ' . $_POST['mc_currency'] . '</p>';
        $content .= COM_showMessageText($msg, $LANG_PAYPAL_1['thanks']);
		$content .= '<div id="jcart">' . PAYPAL_displayCart() .'</div>';
        break;
	
	case 'cancel':
		$msg = $LANG_PAYPAL_1['cancel_details']; 
        $content .= COM_showMessageText($msg, $LANG_PAYPAL_1['cancel']);
		$content .= '<div id="jcart">' . PAYPAL_displayCart() .'</div>';
        break;
		
	default :
	    if ($_PAY_CONF['paypal_main_header'] != '' && $_REQUEST['category'] == '') $content .= '<div>' . PLG_replaceTags($_PAY_CONF['paypal_main_header']) . '</div>';
		$category = Geeklog\Input::fGet('category');
		$content .= PAYPAL_displayProducts('',0,$category);
        
		if ($_PAY_CONF['paypal_main_footer'] != '') $content .= '<div>' . PLG_replaceTags($_PAY_CONF['paypal_main_footer']) . '</div>';
		
		//Display cart
        $content .= '<div id="jcart">' . PAYPAL_displayCart() .'</div>';
}

$display = PAYPAL_createHTMLDocument($content, $pagetitle);
COM_output($display);
