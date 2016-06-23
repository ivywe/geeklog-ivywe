<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin - geeklog CMS                                              |
// +--------------------------------------------------------------------------+
// | subscriptions.php                                                        |
// |                                                                          |
// | Admin index page for the paypal plugin.  By default, lists products      |
// | available for editing                                                    |
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
    $product_id_select = '<select name="product_id">';
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
    $user_select = '<select name="user_id">';
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
	$notification_select = '<select name="notification">';
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

$display = COM_siteHeader('none');
$display .= paypal_admin_menu();

if (!empty($_REQUEST['msg'])) $display .= COM_showMessageText( stripslashes($_REQUEST['msg']), $LANG_PAYPAL_1['message']);

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

switch ($_REQUEST['mode']) {
    case 'new':
	    if (function_exists('PAYPALPRO_newSubscription')) {
		    $display .= PAYPALPRO_newSubscription();
		} else {
    		$display .= COM_showMessageText( $LANG_PAYPAL_PRO['pro_feature_manual_subscription'], $LANG_PAYPAL_1['message']);
		}
	    break;
		
	case 'edit':
        // Get the subscription to edit and display the form
        if (is_numeric($_REQUEST['id'])) {
            $sql = "SELECT * FROM {$_TABLES['paypal_subscriptions']} WHERE id = {$_REQUEST['id']}";
            $res = DB_query($sql);
            $A = DB_fetchArray($res);
            $display .= PAYPAL_getSubscriptionForm($A);
        } else {
            echo COM_refresh($_CONF['site_url']);
        }
        break;
		
	case 'save':
        if (empty($_REQUEST['user_id']) || empty($_REQUEST['purchase_date']) ||empty($_REQUEST['expiration']) ||
                empty($_REQUEST['add_to_group'])) {
            $display .= COM_startBlock($LANG_PAYPAL_1['error']);
            $display .= $LANG_PAYPAL_1['missing_field'];
            $display .= COM_endBlock();
            $display .= PAYPAL_getSubscriptionForm($_REQUEST);
            break;
        }

        // price can only contain numbers and a decimal
        $_REQUEST['price'] = preg_replace('/[^\d.]/', '', $_REQUEST['price']);

        if (!empty($_REQUEST['id'])) {
		    
			// Edition
		    
			$sql = "product_id = '{$_REQUEST['product_id']}', "
        	 . "user_id = '{$_REQUEST['user_id']}', "
             . "txn_id = '{$_REQUEST['txn_id']}', "
             . "purchase_date = '{$_REQUEST['purchase_date']}', "
             . "expiration = '{$_REQUEST['expiration']}', "
             . "price = '{$_REQUEST['price']}', "
			 . "status = '{$_REQUEST['status']}', "
			 . "add_to_group = '{$_REQUEST['add_to_group']}', "
			 . "notification = '{$_REQUEST['notification']}'
			 ";
			 
            $sql = "UPDATE {$_TABLES['paypal_subscriptions']} SET $sql "
                 . "WHERE id = {$_REQUEST['id']}";
        } else {
		    
			// Creation
			
			$prod_id = $_REQUEST['product_id'];
			$products[1] = $_REQUEST['product_id'];
			$quantity[1] = 1;
			$product_name = DB_getItem($_TABLES['paypal_products'],'name',"id=$prod_id");
			$names[1] = $product_name;
			$prices[1] = $_REQUEST['price'];
			
		    $txn_id = PAYPAL_handlePurchase($products, $quantity, $data, $names, $prices, 0, 'complete', $_REQUEST['user_id'] );
			
			$sql = "product_id = '{$_REQUEST['product_id']}', "
        	 . "user_id = '{$_REQUEST['user_id']}', "
             . "txn_id = '{$txn_id}', "
             . "purchase_date = '{$_REQUEST['purchase_date']}', "
             . "expiration = '{$_REQUEST['expiration']}', "
             . "price = '{$_REQUEST['price']}', "
			 . "status = '{$_REQUEST['status']}', "
			 . "add_to_group = '{$_REQUEST['add_to_group']}', "
			 . "notification = '{$_REQUEST['notification']}'
			 ";
			
            $sql = "INSERT INTO {$_TABLES['paypal_subscriptions']} SET $sql ";
        }
        DB_query($sql);
        if (DB_error()) {
            $msg = $LANG_PAYPAL_1['save_fail'];
        } elseif ($_REQUEST['id'] == 0) {
            $msg = $LANG_PAYPAL_1['subscription_label'] . ' >> ' . $LANG_PAYPAL_1['save_success'];
			//add user to group
			if ($_POST['notification'] != '3') PAYPAL_addToGroup ($_REQUEST['add_to_group'], $_REQUEST['user_id']);
        } else {
		    $msg = $LANG_PAYPAL_1['subscription_label'] . ' ' . $_REQUEST['id'] . ' >> ' . $LANG_PAYPAL_1['save_success'];
			//add user to group
			if ($_POST['notification'] != '3') PAYPAL_addToGroup ($_REQUEST['add_to_group'], $_REQUEST['user_id']);
		}
		
        // save complete, return to product list
        echo COM_refresh($_CONF['site_url'] . "/admin/plugins/paypal/subscriptions.php?msg=$msg");
        exit();
        break;
		
	case 'delete':
	    DB_delete($_TABLES['paypal_subscriptions'], 'id', $_REQUEST['id']);
        if (DB_affectedRows('') == 1) {
            $msg = $LANG_PAYPAL_1['deletion_succes'];
			//remove user from group
			PAYPAL_removeFromGroup ($_REQUEST['add_to_group'], $_REQUEST['user_id']);
        } else {
            $msg = $LANG_PAYPAL_1['deletion_fail'];
        }
		// delete complete, return to product list
        echo COM_refresh($_CONF['site_url'] . "/admin/plugins/paypal/subscriptions.php?msg=$msg");
        exit();
        break;
		
	default :
        $display .= COM_startBlock($LANG_PAYPAL_1['memberships_list']);
        $display .= '<p>' . $LANG_PAYPAL_1['you_can'] . '<a href="' . $_CONF['site_url'] . '/admin/plugins/paypal/subscriptions.php?mode=new">' . $LANG_PAYPAL_1['create_subscription'] . '</a> | <a href="' . $_PAY_CONF['site_url'] . '/memberships_history.php">' . $LANG_PAYPAL_1['see_members_list'] . '</a>.</p>';
        $display .= PAYPAL_listSubscriptions('all');
        $display .= COM_endBlock();
	}

$display .= COM_siteFooter();

//Paypal cron
plugin_runScheduledTask_paypal();

COM_output($display);

?>