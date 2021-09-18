<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin - geeklog CMS                                              |
// +--------------------------------------------------------------------------+
// | subscriptions.php                                                        |
// |                                                                          |
// | Admin index page for the paypal plugin.  By default, lists products      |
// | available for editing                                                    |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2021 by the following authors:                             |
// |                                                                          |
// | Authors: ::Ben - cordiste AT free DOT fr                                 |
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
 * @package paypal
 */

/**
 * Required geeklog
 */
require_once('../../../lib-common.php');

// Check for required permissions
paypal_access_check('paypal.admin');

$vars = array('msg' => 'text',
              'mode' => 'alpha',
              'id' => 'number',
			  'user_id' => 'number',
			  'txn_id' => 'alpha',
			  'product_id' => 'number',
              'price' => 'alpha',
			  'status' => 'alpha', 
			  'purchase_date' => 'alpha',
              'expiration' => 'alpha',
              'add_to_group' => 'number',
			  'notification' => 'number');
			  
paypal_filterVars($vars, $_REQUEST);



function PAYPAL_getSubscriptionForm ($subscription = array())
{
    global $_CONF, $_PAY_CONF, $LANG_PAYPAL_1, $_TABLES;
	
	//PHP 5.4 set all $subscription[key] 
	PAYPAL_setAllKeys($subscription, array('id', 'product_id', 'user_id', 'txn_id', 'purchase_date', 'expiration', 'price', 'status', 'add_to_group', 'notification'));
	
	//Display form
	$retval = '';
	($subscription['id'] == '') ? $retval .= COM_startBlock($LANG_PAYPAL_1['create_new_subscription']) : $retval .= COM_startBlock($LANG_PAYPAL_1['edit_subscription'] . ' ' . $subscription['id']);
	
	$template = new Template($_CONF['path'] . 'plugins/paypal/templates');
    $template->set_file(array('subscription' => 'subscription_form.thtml'));
    $template->set_var('site_url', $_CONF['site_url']);
	$template->set_var('xhtml', XHTML);
    $template->set_var('id', '<input type="hidden" name="id" value="' . $subscription['id'] .'" />');

	//Subscrition infos
	$template->set_var('informations', $LANG_PAYPAL_1['subscription_informations']);
	//Membership
    $template->set_var('product_id_label', $LANG_PAYPAL_1['product_id']);
	$result = DB_query("SELECT * FROM {$_TABLES['paypal_products']} WHERE type='subscription'");
    $nRows  = DB_numRows($result);
	if ($nRows == 0) return $LANG_PAYPAL_1['create_membership_first'];
    $product_id_select = '<select class="uk-select" name="product_id">';
    for ($i=0; $i<$nRows;$i++) {
        $row = DB_fetchArray($result);
        $product_id_select .= '<option value="' . $row['id'] . '"' . ($subscription['product_id'] == $row['id'] ? 'selected="selected"' : '') . '>' . $row['id'] . '. ' . $row['name'] . ' | ' . $row['price'] . ' ' . $_PAY_CONF['currency'] . '</option>';
    }
    $product_id_select .= '</select>';
	$template->set_var('product_id_select', $product_id_select);
	//user
	$template->set_var('user_id_label', $LANG_PAYPAL_1['user_id']);
	$result = DB_query("SELECT * FROM {$_TABLES['users']} ORDER BY uid");
    $nRows  = DB_numRows($result);
    $user_select = '<select class="uk-select" name="user_id">';
    for ($i=0; $i<$nRows;$i++) {
        $row = DB_fetchArray($result);
        if ( $row['uid'] == 1 ) {
            continue;
        }
        $user_select .= '<option value="' . $row['uid'] . '"' . ($subscription['user_id'] == $row['uid'] ? 'selected="selected"' : '') . '>' . $row['uid'] . '. ' .COM_getDisplayName($row['uid']) . '</option>';
    }
    $user_select .= '</select>';
	$template->set_var('user_select', $user_select);
	$template->set_var('txn_id_label', $LANG_PAYPAL_1['txn_id']);
	$template->set_var('txn_id', $subscription['txn_id']);
	$template->set_var('purchase_date_label', $LANG_PAYPAL_1['purchase_date']);
	if ($subscription['purchase_date'] != '') {
			$date = date("Y/m/d", strtotime($subscription['purchase_date']));
			$template->set_var('purchase_date', $date);
	} else {
		$date = date("Y/m/d");
		$template->set_var('purchase_date', $date);
	}
	$template->set_var('expiration_label', $LANG_PAYPAL_1['expiration']);
	$template->set_var('expiration', $subscription['expiration']);
	if ($subscription['expiration'] != '') {
			$date = date("Y/m/d", strtotime($subscription['expiration']));
			$template->set_var('expiration', $date);
	} else {
		$date = date("Y/m/d");
		$template->set_var('expiration', $date);
	}
    $template->set_var('price_label', $LANG_PAYPAL_1['price_label']);
	$template->set_var('price', $subscription['price']);
	$template->set_var('status_label', $LANG_PAYPAL_1['status']);
	$template->set_var('status', $subscription['status']);
	$template->set_var('add_to_group_label', $LANG_PAYPAL_1['add_to_group_label']);
	$template->set_var('add_to_group_options', COM_optionList( $_TABLES['groups'], 'grp_id,grp_name', $subscription['add_to_group'], 1));
	
	$template->set_var('notification_label', $LANG_PAYPAL_1['notification']);
	$notification_select = '<select class="uk-select" name="notification">';
    for ($i=0; $i<4; $i++) {
        $notification_select .= '<option value="' . $i . '"' . ($subscription['notification'] == $i ? 'selected="selected"' : '') . '>' . $i . '</option>';
    }
    $notification_select .= '</select>';
	$template->set_var('notification_select', $notification_select);

	$template->set_var('currency', $_PAY_CONF['currency']);

	//validation button
	$template->set_var('save_button', $LANG_PAYPAL_1['save_button']);
	$template->set_var('delete_button', $LANG_PAYPAL_1['delete_button']);
	$template->set_var('ok_button', $LANG_PAYPAL_1['ok_button']);
	$template->set_var('required_field', $LANG_PAYPAL_1['required_field']);
	
	$retval .= $template->parse('', 'subscription');
	
	$retval .= COM_endBlock();
	
	return $retval;
}

//Main

$content = paypal_admin_menu();

$msg = Geeklog\Input::fRequest('msg', '');
if (!empty($msg)) $content .= COM_showMessageText( stripslashes($msg), $LANG_PAYPAL_1['message']);

// Date picker	
$_SCRIPTS->setJavaScriptFile('paypal_datepicker_js', '/jquery/datepicker/datepicker.js');

$js = "
jQuery(function() {
	jQuery('#purchase_date').DatePicker({
		format:'Y/m/d',
		date: jQuery('#purchase_date').val(),
		current: jQuery('#purchase_date').val(),
		starts: 1,
		position: 'top',
		onBeforeShow: function(){
			jQuery('#purchase_date').DatePickerSetDate(jQuery('#purchase_date').val(), true);
		},
		onChange: function(formated){
			jQuery('#purchase_date').val(formated);
			jQuery('#purchase_date').DatePickerHide();
		}
	});
	jQuery('#expiration').DatePicker({
		format:'Y/m/d',
		date: jQuery('#expiration').val(),
		current: jQuery('#expiration').val(),
		starts: 1,
		position: 'top',
		onBeforeShow: function(){
			jQuery('#expiration').DatePickerSetDate(jQuery('#expiration').val(), true);
		},
		onChange: function(formated){
			jQuery('#expiration').val(formated);
			jQuery('#expiration').DatePickerHide();
		}
	});
});
";
$_SCRIPTS->setJavaScript($js, true);

$mode = Geeklog\Input::request('mode', '');
switch ($mode) {
    case 'new':
	    if (function_exists('PAYPALPRO_newSubscription')) {
		    $content .= PAYPALPRO_newSubscription();
		} else {
    		$content .= COM_showMessageText( $LANG_PAYPAL_PRO['pro_feature_manual_subscription'], $LANG_PAYPAL_1['message']);
		}
	    break;
		
	case 'edit':
        // Get the subscription to edit and display the form
        $r_id = Geeklog\Input::fRequest('id', '');
        if (is_numeric($r_id)) {
            $sql = "SELECT * FROM {$_TABLES['paypal_subscriptions']} WHERE id = {$r_id}";
            $res = DB_query($sql);
            $A = DB_fetchArray($res);
            $content .= PAYPAL_getSubscriptionForm($A);
        } else {
            echo COM_refresh($_CONF['site_url']);
        }
        break;
		
	case 'save':
        $r_user_id = Geeklog\Input::fRequest('user_id', '');
        $r_purchase_date = Geeklog\Input::fRequest('purchase_date', '');
        $r_expiration = Geeklog\Input::fRequest('expiration', '');
        $r_add_to_group = Geeklog\Input::fRequest('add_to_group', '');
        if (empty($r_user_id) || empty($r_purchase_date) ||empty($r_expiration) ||
                empty($r_add_to_group)) {
            $content .= COM_startBlock($LANG_PAYPAL_1['error']);
            $content .= $LANG_PAYPAL_1['missing_field'];
            $content .= COM_endBlock();
            $content .= PAYPAL_getSubscriptionForm($_REQUEST);
            break;
        }

        // price can only contain numbers and a decimal
        $r_price = Geeklog\Input::fRequest('price', '');
        $r_price = preg_replace('/[^\d.]/', '', $r_price);

        $r_id = Geeklog\Input::fRequest('id', '');
        $r_product_id = Geeklog\Input::fRequest('product_id', '');
        if (!empty($r_id)) {
		    
			// Edition
			$r_txn_id = Geeklog\Input::fRequest('txn_id', '');
		    
			$sql = "product_id = '{$r_product_id}', "
        	 . "user_id = '{$r_user_id}', "
             . "txn_id = '{$r_txn_id}', "
             . "purchase_date = '{$r_purchase_date}', "
             . "expiration = '{$r_expiration}', "
             . "price = '{$r_price}', "
			 . "status = '{$_REQUEST['status']}', "
			 . "add_to_group = '{$r_add_to_group}', "
			 . "notification = '{$_REQUEST['notification']}'
			 ";
			 
            $sql = "UPDATE {$_TABLES['paypal_subscriptions']} SET $sql "
                 . "WHERE id = {$r_id}";
        } else {
		    
			// Creation
			
			$prod_id = $r_product_id;
			$products[1] = $r_product_id;
			$quantity[1] = 1;
			$product_name = DB_getItem($_TABLES['paypal_products'],'name',"id=$prod_id");
			$names[1] = $product_name;
			$prices[1] = $r_price;
			
		    $txn_id = PAYPAL_handlePurchase($products, $quantity, $data, $names, $prices, 0, 'complete', $r_user_id );
			
			$sql = "product_id = '{$r_product_id}', "
        	 . "user_id = '{$r_user_id}', "
             . "txn_id = '{$txn_id}', "
             . "purchase_date = '{$r_purchase_date}', "
             . "expiration = '{$r_expiration}', "
             . "price = '{$r_price}', "
			 . "status = '{$_REQUEST['status']}', "
			 . "add_to_group = '{$r_add_to_group}', "
			 . "notification = '{$_REQUEST['notification']}'
			 ";
			
            $sql = "INSERT INTO {$_TABLES['paypal_subscriptions']} SET $sql ";
        }
        DB_query($sql);
        if (DB_error()) {
            $msg = $LANG_PAYPAL_1['save_fail'];
        } elseif ($r_id == 0) {
            $msg = $LANG_PAYPAL_1['subscription_label'] . ' >> ' . $LANG_PAYPAL_1['save_success'];
			//add user to group
			if (isset($_POST['notification']) && $_POST['notification'] != '3') PAYPAL_addToGroup ($r_add_to_group, $r_user_id);
        } else {
		    $msg = $LANG_PAYPAL_1['subscription_label'] . ' ' . $r_id . ' >> ' . $LANG_PAYPAL_1['save_success'];
			//add user to group
			if (isset($_POST['notification']) && $_POST['notification'] != '3') PAYPAL_addToGroup ($r_add_to_group, $r_user_id);
		}
		
        // save complete, return to product list
        echo COM_refresh($_CONF['site_url'] . "/admin/plugins/paypal/subscriptions.php?msg=$msg");
        exit();
        break;
		
	case 'delete':
        $r_id = Geeklog\Input::fRequest('id', '');
	    DB_delete($_TABLES['paypal_subscriptions'], 'id', $r_id);
        if (DB_affectedRows('') == 1) {
            $msg = $LANG_PAYPAL_1['deletion_succes'];
			//remove user from group
			$r_add_to_group = Geeklog\Input::fRequest('add_to_group', '');
			$r_user_id = Geeklog\Input::fRequest('user_id', '');
			PAYPAL_removeFromGroup ($r_add_to_group, $r_user_id);
        } else {
            $msg = $LANG_PAYPAL_1['deletion_fail'];
        }
		// delete complete, return to product list
        echo COM_refresh($_CONF['site_url'] . "/admin/plugins/paypal/subscriptions.php?msg=$msg");
        exit();
        break;
		
	default :
        $content .= COM_startBlock($LANG_PAYPAL_1['memberships_list']);
        $content .= '<p>' . $LANG_PAYPAL_1['you_can'] . '<a href="' . $_CONF['site_url'] . '/admin/plugins/paypal/subscriptions.php?mode=new">' . $LANG_PAYPAL_1['create_subscription'] . '</a> | <a href="' . $_PAY_CONF['site_url'] . '/memberships_history.php">' . $LANG_PAYPAL_1['see_members_list'] . '</a>.</p>';
        $content .= PAYPAL_listSubscriptions('all');
        $content .= COM_endBlock();
	}

$display = COM_createHTMLDocument($content, ['pagetitle' => $LANG_PAYPAL_1['memberships_list']]);

//Paypal cron
plugin_runScheduledTask_paypal();

COM_output($display);

?>