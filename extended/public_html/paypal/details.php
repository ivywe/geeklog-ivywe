<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin - geeklog CMS                                              |
// +--------------------------------------------------------------------------+
// | details .php                                                             |
// |                                                                          |
// | Admin index page for the paypal plugin.  By default, lists products      |
// | available for editing                                                    |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
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
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.             |
// |                                                                          |
// +--------------------------------------------------------------------------+


/**
 * @package paypal
 */

/**
 * Required geeklog
 */
require_once('../lib-common.php');

// Check for required permissions
paypal_access_check('paypal.user');

$vars = array('msg' => 'text',
              'mode' => 'alpha',
			  'uid'  => 'number',
              'name' => 'text',
              'street1' => 'text',
			  'street2' => 'text',
			  'postal' => 'alpha',
			  'city' => 'text',
              'country' => 'text',
			  'phone1' => 'alpha', 
			  'phone2' => 'alpha',
              'fax' => 'alpha',
              'contact' => 'text',
			  'proid' => 'alpha',
			  'pay_by' => 'alpha', 
			  'shipping' => 'text',
			  );
			  
paypal_filterVars($vars, $_REQUEST);

//Main

$display = '';
$display .= paypal_user_menu();

if (!empty($_REQUEST['msg'])) $display .= PAYPAL_message($_REQUEST['msg']);

switch ($_REQUEST['mode']) {
    case 'edit':
	    // Get the details to edit and display the form
        if (isset($_USER['uid']) && $_USER['uid'] > 1) {
            $sql = "SELECT * FROM {$_TABLES['paypal_users']} WHERE user_id = {$_USER['uid']}";
			//only admin can edit details of a user
			if (SEC_hasRights('paypal.admin')) {
			    $sql = "SELECT * FROM {$_TABLES['paypal_users']} WHERE user_id = {$_REQUEST['uid']}";
			}
            $res = DB_query($sql);
            $A = DB_fetchArray($res);
			if ($A['user_id'] == '' && SEC_hasRights('paypal.admin')) {
			    $A['user_id'] = $_REQUEST['uid'];
			}
			if ($A['user_id'] == '') {
			    $A['user_id'] = $_USER['uid'];
			}
            $display .= PAYPAL_getDetailsForm($A, $_PAY_CONF['site_url'] . '/details.php?mode=save', $LANG_PAYPAL_1['save_button']);
        } else {
            echo COM_refresh($_CONF['site_url']);
        }
	    break;
		
	case 'save':
	    $msg = 'Done!';
		//clean input
		$remove_from_tel = array(' ', '.', '|', ',', '/', ':', '-', '_');
        $phone1 = str_replace($remove_from_tel, '', $_REQUEST['phone1']);
		$phone2 = str_replace($remove_from_tel, '', $_REQUEST['phone2']);
		$fax = str_replace($remove_from_tel, '', $_REQUEST['fax']);
		$name = strtoupper(addslashes(COM_getTextContent($_REQUEST['name'])));
		$street1 = addslashes(COM_getTextContent($_REQUEST['street1']));
		$street2 = addslashes(COM_getTextContent($_REQUEST['street2']));
		$postal = $_REQUEST['postal'];
		$city =  strtoupper(addslashes(COM_getTextContent($_REQUEST['city'])));
		$country =  strtoupper(addslashes(COM_getTextContent($_REQUEST['country'])));
		$contact =  ucwords(mb_strtolower(addslashes(COM_getTextContent($_REQUEST['contact']))));
				
        if (isset($_USER['uid']) && $_USER['uid'] > 1) {
		    if ( !SEC_hasRights('paypal.admin') ) {
    			$_REQUEST['user_id'] = $_USER['uid'];
			}
			$uid_exist = DB_getItem($_TABLES['paypal_users'],'user_id', "user_id = '{$_REQUEST['user_id']}'" );
            if ( !empty($_REQUEST['user_id']) && $uid_exist == $_REQUEST['user_id'] ) {
		        $sql = "user_name = '{$name}', "
				 . "user_street1 = '{$street1}', "
				 . "user_street2 = '{$street2}', "
				 . "user_postal = '{$postal}', "
				 . "user_city = '{$city}', "
				 . "user_country = '{$country}', "
				 . "user_phone1 = '{$phone1}', "
				 . "user_phone2 = '{$phone2}', "
				 . "user_fax = '{$fax}', "
				 . "user_contact = '{$contact}', "
				 . "user_proid = '{$_REQUEST['proid']}'
			     ";
            $sql = "UPDATE {$_TABLES['paypal_users']} SET $sql "
                 . "WHERE user_id = {$_REQUEST['user_id']}";
            } else {
				$sql = "user_id = '{$_REQUEST['user_id']}', "
				 . "user_name = '{$name}', "
				 . "user_street1 = '{$street1}', "
				 . "user_street2 = '{$street2}', "
				 . "user_postal = '{$postal}', "
				 . "user_city = '{$city}', "
				 . "user_country = '{$country}', "
				 . "user_phone1 = '{$phone1}', "
				 . "user_phone2 = '{$phone2}', "
				 . "user_fax = '{$fax}', "
				 . "user_contact = '{$contact}', "
				 . "user_proid = '{$_REQUEST['proid']}'
				 ";
                $sql = "INSERT INTO {$_TABLES['paypal_users']} SET $sql ";
            }
            DB_query($sql);
            if (DB_error()) {
                $msg = urlencode($LANG_PAYPAL_1['details_save_fail']);
                // save incomplete
                echo COM_refresh($_SERVER['HTTP_REFERER'] . '?msg='. $msg);
                exit();
                break;
            } else {
                // save complete
				if ($_REQUEST['pay_by'] == 'check') {
					// shipping can only contain numbers and a decimal
					$shipping = str_replace(",","",$_REQUEST['shipping']);
					$shipping = preg_replace('/[^\d.]/', '', $shipping);
				    echo COM_refresh($_PAY_CONF[ 'site_url'] .  '/confirmation.php?pay_by=check&shipping=' . $shipping . '&amp;msg='. $msg);
				} else {
				    $msg = urlencode($LANG_PAYPAL_1['details_save_success']);
                    echo COM_refresh($_PAY_CONF[ 'site_url'] .  '/purchase_history.php?msg='. $msg);
				}
                exit();
                break;
		    }
		}


	default :
	    // Get my details to edit and display the form
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
			$validation_url =  $_PAY_CONF['site_url'] . '/details.php?mode=save';
            $display .= PAYPAL_getDetailsForm($A, $validation_url, $LANG_PAYPAL_1['save_button']);
        } else {
            echo COM_refresh($_CONF['site_url']);
        }
	}

$display = COM_siteHeader() . $display . COM_siteFooter();


COM_output($display);

?>