<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Paypal Plugin 1.1                                                         |
// +---------------------------------------------------------------------------+
// | checkout.php                                                          |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben - cordiste AT free DOT fr                                  |
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
require_once '../lib-common.php';

// take user back to the homepage if the plugin is not active
if (! in_array('paypal', $_PLUGINS)) {
    echo COM_refresh($_CONF['site_url'] . '/index.php');
    exit;
}

/* Ensure sufficient privs to read this page */
paypal_access_check('paypal.viewer');

//Main
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




if (SEC_hasRights('paypal.user', 'paypal.admin')) {
    $display .= paypal_user_menu();
} else {
    $display .= paypal_viewer_menu();
}

//Display cart
$display .= '<div id="cart">' . PAYPAL_displayCart() .'</div>';


$display .= COM_siteFooter();

COM_output($display);

?>