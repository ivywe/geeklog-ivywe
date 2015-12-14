<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.0                                                           |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// |                                                                           |
// | Public plugin page                                                        |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben                                                            |
// +---------------------------------------------------------------------------+
// | Created with the Geeklog Plugin Toolkit.                                  |
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
* @package Maps
*/

require_once '../lib-common.php';

// take user back to the homepage if the plugin is not active
if (!in_array('maps', $_PLUGINS)) {
    echo COM_refresh($_CONF['site_url'] . '/index.php');
    exit;
}

// MAIN

$display = '';

// Ensure user has the rights to access this page
if (COM_isAnonUser() && (($_CONF['loginrequired'] == 1) || ($_MAPS_CONF['maps_login_required'] == 1))) {
	$display .= COM_siteHeader('');
	$display .= MAPS_user_menu();
    $display .= COM_startBlock ($LANG_LOGIN[1], '',
                                COM_getBlockTemplate ('_msg_block', 'header'));
    $login = new Template($_CONF['path'] . 'plugins/maps/templates');
    $login->set_file (array ('login'=>'submitloginrequired.thtml'));
    $login->set_var ( 'xhtml', XHTML );
    $login->set_var ('login_message', $LANG_LOGIN[2]);
    $login->set_var ('site_url', $_CONF['site_url']);
    $login->set_var ('site_admin_url', $_CONF['site_admin_url']);
    $login->set_var ('layout_url', $_CONF['layout_url']);
    $login->set_var ('lang_login', $LANG_LOGIN[3]);
    $login->set_var ('lang_newuser', $LANG_LOGIN[4]);
    $login->parse ('output', 'login');
    $display .= $login->finish ($login->get_var('output'));
    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
    $display .= COM_siteFooter();
    COM_output($display);
    exit;
}

// Incoming variable filter
$vars = array('mid' => 'number');
MAPS_filterVars($vars, $_REQUEST);

$display .= COM_siteHeader('menu', $LANG_MAPS_1['maps'] . ' | ' . $A['name'] . $more_title);
$display .= MAPS_user_menu();

// query database for map
if ($_REQUEST['mid'] !=0 && $_REQUEST['mid']>0) {
	$display .= MAPS_getMap($_REQUEST['mid']);
} elseif ($_REQUEST['mid'] == 0) {
    //Display the Global Map 
	$display .= MAPS_getGlobalMap();
} else {
    echo COM_refresh($_MAPS_CONF['site_url'] . '/index.php');
}

$display .= COM_siteFooter();

echo $display;

?>
