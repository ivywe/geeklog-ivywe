<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin - geeklog CMS                                              |
// +--------------------------------------------------------------------------+
// | transaction.php                                                          |
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
$vars = array('id' => 'number',
              'type' => 'alpha',
			  'mode' => 'alpha'
			 );
paypal_filterVars($vars, $_REQUEST);

$pid = $_REQUEST['id'];
$type = $_REQUEST['type']; //purchase or subscription

// Ensure sufficient privs to read this page
if (($_USER['uid'] < 2) && ($_PAY_CONF['anonymous_buy'] == 0)) {
    $display .= COM_siteHeader();
	if (SEC_hasRights('paypal.user', 'paypal.admin')) {
        $display .= paypal_user_menu();
    } else {
        $display .= paypal_viewer_menu();
    }
    $display .= COM_startBlock($LANG_PAYPAL_1['access_reserved']);
    $display .= $LANG_PAYPAL_1['you_must_log_in'];
    $display .= COM_endBlock();
    $display .= COM_siteFooter();
    COM_output($display);
    exit;
}

if ($_PAY_CONF['anonymous_buy'] == 0) paypal_access_check('paypal.user');

// query database for transaction
if ($type == 'purchase') $res = DB_query("SELECT DISTINCT s.*, i.ipn_data
                                        FROM {$_TABLES['paypal_purchases']} AS s,
										{$_TABLES['paypal_ipnlog']} AS i
										WHERE s.id = {$pid} AND s.txn_id=i.txn_id");

if ($type == 'subscription') $res = DB_query("SELECT DISTINCT s.*, i.ipn_data
				FROM {$_TABLES['paypal_subscriptions']} AS s,
					{$_TABLES['paypal_ipnlog']} AS i
				WHERE s.id = {$pid} AND s.txn_id=i.txn_id");


// count number of returned results, if unexpected redirect to product list
$numres = DB_numRows($res);

if ($numres != 1) {
    COM_errorLog('Error on Paypal transaction page: Number of rows: ' . $numres . ' Type=' . $type .  ' ID=' . $pid . ' User='. $_USER['uid']);
    echo COM_refresh($_PAY_CONF['site_url'] . '/index.php');
	exit;
}

$A = DB_fetchArray($res, false);

$purchase_status = $A['status'];

$transaction = new Template($_CONF['path'] . 'plugins/paypal/templates/transaction');
if ($_REQUEST['mode'] == 'print') {
    $transaction->set_file(array('transaction' => 'print_' . $type . '.thtml'));
} else {
    $transaction->set_file(array('transaction' => $type . '.thtml'));
}

// Allow all serialized data to be available to the template
$ipn ='';
if ($A['ipn_data'] != '') {
	$out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $A['ipn_data'] ); 
	$ipn = unserialize($out);
	if (!is_array($ipn)) {
		$ipn = array();
	}
	foreach ($ipn as $name => $value) {
		$transaction->set_var($name, $value);
	}
}

if ( $A['user_id'] != '' && ($_USER['uid'] != $A['user_id']) && SEC_hasRights('paypal.admin') == false) {
    COM_errorLog('Error on Paypal transaction page: User is not allowed to see transaction. Type=' . $type .  ' ID=' . $pid . ' User='. $_USER['uid'] . ' User of the transaction='. $A['user_id']);
    echo COM_refresh($_PAY_CONF['site_url'] . '/index.php');
	exit;
}

//Log-In to access
if (($_USER['uid'] < 2) && ($A['logged'] == 1)) {
    $display .= COM_siteHeader();
	if (SEC_hasRights('paypal.user', 'paypal.admin')) {
        $display .= paypal_user_menu();
    } else {
        $display .= paypal_viewer_menu();
    }
    $display .= COM_startBlock($LANG_PAYPAL_1['access_reserved']);
    $display .= $LANG_PAYPAL_1['you_must_log_in'];
    $display .= COM_endBlock();
    $display .= COM_siteFooter();
    COM_output($display);
    exit;
}

//Content

$transaction->set_var('site_url', $_PAY_CONF['site_url']);
$transaction->set_var('charset', COM_getCharset());
$transaction->set_var('br', '<br'. XHTML .'>');

if ($_REQUEST['mode'] == 'print') {
    $transaction->set_var('print', '');
} else {
    $transaction->set_var('print', ' <small>(<a href="' . $_PAY_CONF['site_url'] . '/transaction.php?type=' . $type . '&amp;id=' . $pid . '&amp;mode=print" target="_blank">'. $LANG_PAYPAL_1['print'] . '</a>)</small>');
}

//shop details
$transaction->set_var('shop_name', $_PAY_CONF['shop_name']);
$transaction->set_var('shop_street1', $_PAY_CONF['shop_street1']);
if ($_PAY_CONF['shop_street2'] != '') {
    $transaction->set_var('shop_street2', $_PAY_CONF['shop_street2'] . '<br' . XHTML .'>');
} else {
    $transaction->set_var('shop_street2', '');
}
$transaction->set_var('shop_postal', $_PAY_CONF['shop_postal']);
$transaction->set_var('shop_city', $_PAY_CONF['shop_city']);
$transaction->set_var('shop_country', '<p>' . $_PAY_CONF['shop_country'] .'</p>');
if ($_PAY_CONF['shop_phone1'] != '') {
    $transaction->set_var('shop_phone1', $LANG_PAYPAL_1['phone1'] . ' ' . $_PAY_CONF['shop_phone1']);
} else {
    $transaction->set_var('shop_phone1', '');
}
if ($_PAY_CONF['shop_phone2'] != '') {
    $transaction->set_var('shop_phone2',  '<br' . XHTML .'>' . $LANG_PAYPAL_1['phone2'] . ' ' . $_PAY_CONF['shop_phone2']);
} else {
    $transaction->set_var('shop_phone2', '');
}
if ($_PAY_CONF['shop_fax'] != '') {
    $transaction->set_var('shop_fax',  '<br' . XHTML .'>' . $LANG_PAYPAL_1['fax'] . ' ' . $_PAY_CONF['shop_fax'] . '<br' . XHTML .'>');
} else {
    $transaction->set_var('shop_fax', '');
}
$transaction->set_var('shop_proid', '<p>' . $_PAY_CONF['shop_siret'] .'</p>');

//user details
if (SEC_hasRights('paypal.admin')) {
    $transaction->set_var('edit_details', '<small><a href="' . $_PAY_CONF['site_url'] . '/details.php?mode=edit&amp;uid=' . $A['user_id'] . '">' . $LANG_PAYPAL_1['edit_details'] . '</a></small>');
} else {
    $transaction->set_var('edit_details', '');
}

$transaction->set_var('user_name', $ipn['address_name'] );
$transaction->set_var('user_street1', $ipn['address_street']);

$transaction->set_var('user_postal', $ipn['address_zip']);
$transaction->set_var('user_city', $ipn['address_city']);
$transaction->set_var('user_country', '<p>' . $ipn['address_country'] . '</p>');
$purchase_date = COM_getUserDateTimeFormat($A['purchase_date']);
$expiration = COM_getUserDateTimeFormat($A['expiration']);

$transaction->set_var('qty', $A['quantity']);
$transaction->set_var('product', $A['name']);

$transaction->set_var('from', $LANG_PAYPAL_1['from'] . ' ' . $purchase_date[0]);
$transaction->set_var('to', $LANG_PAYPAL_1['to'] . ' ' . $expiration[0]);

$transaction->set_var('edit', '');

if ($purchase_status == 'complete' || $purchase_status == '') {
    $transaction->set_var('paid_on', $LANG_PAYPAL_1['paid_on'] . ' ' . $purchase_date[0]);
} else {
    $transaction->set_var('paid_on', $LANG_PAYPAL_1['order_on'] . ' ' . $purchase_date[0]);
	if (SEC_hasRights('paypal.admin') && $A['status'] == 'pending') {
	    $transaction->set_var('edit', '<p><a href="' . $_CONF['site_url'] . '/admin/plugins/paypal/purchase_history.php?mode=edit&amp;txn_id=' . 
		$ipn['txn_id'] . '" onclick="return confirm(\'' . $LANG_PAYPAL_1['confirm_edit_status'] .'\');"> >> ' . $LANG_PAYPAL_1['validate_order'] . '</a></p>');
	}
}

//Order or invoice
$transaction->set_var('receipt', $LANG_PAYPAL_1['transaction'] . ': ' . $ipn['txn_id']);

//Todo implement payment_type on purchase
if ($ipn['payment_type'] != '') {
    $transaction->set_var('payment_type', $LANG_PAYPAL_1['by'] . ' ' . $LANG_PAYPAL_PAYMENT[$ipn['payment_type']]);
} else {
    $transaction->set_var('payment_type', '');
}

$transaction->set_var('total_price', number_format($ipn['mc_gross'], $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator']));
$transaction->set_var('currency', $_PAY_CONF['currency']);
$transaction->set_var('name', $A['name']);

//Table header
$transaction->set_var('quantity', $LANG_PAYPAL_1['quantity']);
$transaction->set_var('product_name', $LANG_PAYPAL_1['name']);
$transaction->set_var('unit_price_label', $LANG_PAYPAL_1['unit_price_label']);
$transaction->set_var('total_row_label', $LANG_PAYPAL_1['total_row_label']);

//Table row
$transaction->set_block('transaction', 'tablerow','ttablerow');

if ($ipn['quantity0'] != '') {
    $i = 0;
	for (; ; ) {
		
		if ($ipn['quantity'.$i] == '') {
			break;
		}
		
		$price = $ipn['mc_gross_'.$i] / intval($ipn['quantity'.$i]);
		$total  = $ipn['mc_gross_'.$i];
		if ($price == 0) $price = $ipn['mc_gross'.$i] / intval($ipn['quantity'.$i]);
		if ($total == 0) $total = $ipn['mc_gross'.$i];
		
		$transaction->set_var(array(
				'qty'        =>  $ipn['quantity'.$i],
				'product'    =>  $ipn['item_name'.$i],
				'unit_price' =>  number_format($price, $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator']),
				'total_row'  =>  number_format($total, $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator'])
			));
		
		$transaction->parse('ttablerow','tablerow',true);
		$i++;
	}
	
} else if ($ipn['quantity1'] != '') {
    
	$i = 1;
	
	for (; ; ) {
		if ($ipn['quantity'.$i] == '') {
			break;
		}
		
		$price = $ipn['mc_gross_'.$i] / intval($ipn['quantity'.$i]);
		$total  = $ipn['mc_gross_'.$i];
		if ($price == 0) $price = $ipn['mc_gross'.$i] / intval($ipn['quantity'.$i]);
		if ($total == 0) $total = $ipn['mc_gross'.$i];
		
		$transaction->set_var(array(
				'qty'        =>  $ipn['quantity'.$i],
				'product'    =>  $ipn['item_name'.$i],
				'unit_price' =>  number_format($price, $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator']),
				'total_row'  =>  number_format($total, $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator'])
			));
		$transaction->parse('ttablerow','tablerow',true);
		$i++;
	}
	
} else if (!isset($ipn['quantity']) || $ipn['quantity'] == 0) {

	$ipn['quantity']=1;
	
	$transaction->set_var(array(
		'qty'        =>  $ipn['quantity'],
		'product'    =>  $ipn['item_name1'],
		'unit_price' =>  number_format($ipn['mc_gross']/intval($ipn['quantity']), $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator']),
		'total_row'  =>  number_format($ipn['mc_gross'], $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator'])
	));
	
	$transaction->parse('ttablerow','tablerow',true);
}

//Handling
if ($ipn['mc_handling'] > 0) {
	$transaction->set_var(array(
	'qty'        =>  1,
	'product'    =>  $LANG_PAYPAL_1['shipping'],
	'unit_price' =>  '',
	'total_row'  =>  number_format($ipn['mc_handling'], $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator'])
	));
	$transaction->parse('ttablerow','tablerow',true);
}

//Shipping
if ($ipn['mc_shipping'] > 0) {
	$transaction->set_var(array(
	'qty'        =>  1,
	'product'    =>  $LANG_PAYPAL_1['shipping'],
	'unit_price' =>  '',
	'total_row'  =>  number_format($ipn['mc_shipping'], $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator'])
	));
	$transaction->parse('ttablerow','tablerow',true);
}
	
//Table footer 
$transaction->set_var('total_price_label', $LANG_PAYPAL_1['total_price_label']);

//print mode
$transaction->set_var('xhtml', XHTML);
if (XHTML != '') {
    $transaction->set_var('xmlns',
                             ' xmlns="http://www.w3.org/1999/xhtml"');
}
$transaction->set_var('direction', $LANG_DIRECTION);

$transaction->parse('output', 'transaction');
$content = $transaction->finish($transaction->get_var('output'));
//Display

if ($_REQUEST['mode'] == 'print') {
    $display = $content;
} else {
    $display = COM_siteHeader();
    if (SEC_hasRights('paypal.user', 'paypal.admin')) {
        $display .= paypal_user_menu();
    } else {
        $display .= paypal_viewer_menu();
    }
    $display .= COM_startBlock();
    $display .= $content;
    $display .= COM_endBlock();
    $display .= COM_siteFooter();
}

COM_output($display);

?>