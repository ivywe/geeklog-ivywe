<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin v1.5 - geeklog CMS                                         |
// +--------------------------------------------------------------------------+
// | login.php                                                                |
// |                                                                          |
// | Purchase History View.  Displays the current user's history of purchases.|
// +--------------------------------------------------------------------------+
// | Copyright (C) 2021 by the following authors:                             |
// |                                                                          |
// | Authors: ::Ben - cordiste AT free DOT fr                                 |
// | Authors: Hiroron    - hiroron AT hiroron DOT com                         |
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
 * Login form
 * @package paypal
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
paypal_access_check();

if ( $_PAY_CONF['view_membership'] != '1' && !SEC_hasRights('paypal.admin') ) {
    echo COM_refresh($_PAY_CONF['site_url'] . '/index.php');
    exit;
}

//Main
<<<<<<< HEAD
$content = '';
if (SEC_hasRights('paypal.user,paypal.admin')) {
    $content .= paypal_user_menu();
=======

$display = COM_createHTMLDocument();
if (SEC_hasRights('paypal.user', 'paypal.admin')) {
    $display .= paypal_user_menu();
>>>>>>> a3585c97bc195947c9f3e425113708436eeef2c8
} else {
    $content .= paypal_viewer_menu();
}

$msg = Geeklog\Input::request('msg', '');
if (!empty($msg)) $content .= COM_showMessageText( stripslashes($msg), $LANG_PAYPAL_1['message']);

$content .= '<div id="membership">' . COM_startBlock($LANG_PAYPAL_1['members_list']) . phpblock_PAYPAL_displaySubscriptions() . COM_endBlock() . '</div>';

$display = COM_createHTMLDocument($content);

COM_output($display);
