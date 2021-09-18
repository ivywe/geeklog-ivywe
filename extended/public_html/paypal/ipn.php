<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin - geeklog CMS                                             |
// +--------------------------------------------------------------------------+
// | ipn.php                                                                  |
// |                                                                          |
// | page that accepts IPN transaction information from the paypal servers.   |
// | A link to this page needs to be associated with your paypal business     |
// | account.                                                                 |
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
 * page that accepts IPN transaction information from the paypal servers.  A link to
 * this page needs to be associated with your paypal business account.
 *
 * @author Vincent Furia <vinny01 AT users DOT sourceforge DOT net>
 * @copyright Vincent Furia 2005 - 2006
 * @package paypal
 */

//sandboxではipnデータに日本語があるとshift_jisで送られてきてutf-8に変更できないのでcharsetやitem_nameXなどをutf-8に変更する
$convert_utf8 = (isset($_POST['charset']) && strtolower($_POST['charset']) == 'shift_jis') ? true : false;
if ($convert_utf8) {
	if (isset($_POST['charset'])) {
		$_POST['charset'] = 'utf-8';
	}
	foreach (array('address_street','first_name','address_name','address_country','address_city','last_name','address_state','transaction_subject') as $key) {
		if (isset($_POST[$key]) && !empty($_POST[$key])) {
			$_POST[$key] = mb_convert_encoding($_POST[$key],'UTF-8','SJIS');
		}
	}
	for ($i = 1; $i <= 10; $i++ ) {
		if (isset($_POST['item_name'.$i])) {
			$_POST['item_name'.$i] = mb_convert_encoding($_POST['item_name'.$i],'UTF-8','SJIS');
		}
	}
}

/**
 * Require geeklog
 */
require_once('../lib-common.php');

if (!in_array('paypal', $_PLUGINS)) {
    COM_handle404();
    exit;
}


/**
 * Get needed paypal classes
 */

require_once($_CONF['path'] . 'plugins/paypal/classes/IPN.class.php');

// Process IPN request
$ipn = new IPN();
$ipn->Process($_POST);

// Finished (this isn't necessary...but heck...why not?)
echo "Thanks";

