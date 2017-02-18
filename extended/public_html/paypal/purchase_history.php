<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin v1.1 - geeklog CMS                                         |
// +--------------------------------------------------------------------------+
// | purchase_history.php                                                     |
// |                                                                          |
// | Purchase History View.  Displays the current user's history of purchases.|
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                             |
// |                                                                          |
// | Authors: ::Ben - cordiste AT free DOT fr                                 |
// +--------------------------------------------------------------------------+
// | Based on the original paypal Plugin                                      |
// | Copyright (C) 2005 - 2006 by the following authors:                      |
// |                                                                          |
// | Vincent Furia <vinny01 AT users DOT sourceforge DOT net>                 |   
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
 * Purchase History View.  Displays the current user's history of purchases.
 *
 * @author Vincent Furia <vinny01 AT users DOT sourceforge DOT net>
 * @copyright Vincent Furia 2005 - 2006
 * @package paypal
 */

/**
 * require core geeklog code
 */
require_once '../lib-common.php';

/* Ensure sufficient privs to read this page */
paypal_access_check();

/* Purchase history for anonymous users/paypal viewers doesn't make sense */
if (!SEC_hasRights('paypal.user','paypal.admin','OR') || COM_isAnonUser() ) {

		$display = "";

    switch( $_PAY_CONF['display_blocks'] ) {
    case 0 :    // none
    case 2 :    // right only
        $display .= COM_siteHeader('none', $pagetitle);
        break;
    case 1 :    // left only
    case 3 :    // both
    default :
        $display .= COM_siteHeader('menu', $pagetitle);
        break;
    }

    $display .= paypal_viewer_menu();
    $display .= PAYPAL_loginRequiredForm();
    $display .= COM_siteFooter();
    echo $display;
    exit;
}

//Main

$display = COM_siteHeader();
$display .= paypal_user_menu();

if (!empty($_REQUEST['msg'])) $display .= COM_showMessageText( stripslashes($_REQUEST['msg']), $LANG_PAYPAL_1['message']);

$display .= PAYPAL_displayPurchaseHistory ();

$display .= COM_siteFooter();

COM_output($display);

?>