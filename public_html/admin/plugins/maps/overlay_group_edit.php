<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.2.2                                                         |
// +---------------------------------------------------------------------------+
// | overlay_group_edit.php                                                    |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2012 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben  ben AT geeklog DOT fr                                     |
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

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';

$display = '';

// Ensure user even has the rights to access this page
if (! SEC_hasRights('maps.admin')) {
    $display .= COM_siteHeader('menu', $MESSAGE[30])
             . COM_showMessageText($MESSAGE[29], $MESSAGE[30])
             . COM_siteFooter();

    // Log attempt to access.log
    COM_accessLog("User {$_USER['username']} tried to illegally access the Maps plugin administration screen.");

    echo $display;
    exit;
}


// Incoming variable filter
$vars = array('mode'         => 'alpha',
              'o_group_id'   => 'alpha',
              'o_group_name' => 'text'
			  );

MAPS_filterVars($vars, $_REQUEST);

/**
 * This function creates a Group of Overlay Form
 *
 * Creates a Form for a group of overlay using the supplied defaults (if specified).
 *
 * @param array $group array of values describing a group of overlay
 * @return string HTML string of overlay form
 */
function MAPS_getGroupOverlayForm($group = array()) {

    global $_CONF, $_TABLES, $_MAPS_CONF, $LANG_MAPS_1, $LANG_configselects, $LANG_ACCESS, $_USER, $_GROUPS, $_SCRIPTS;
    
	$display = COM_startBlock('<h1>' . $LANG_MAPS_1['group_edit'] . ' ' . $group['o_group_name']. '</h1>');


	$template = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
	$template->set_file(array('map' => 'group_overlay_form.thtml'));
	
	$template->set_var('yes', $LANG_MAPS_1['yes']);
	$template->set_var('no', $LANG_MAPS_1['no']);
	
	//informations
	$template->set_var('group_overlay_presentation', $LANG_MAPS_1['group_overlay_presentation']);
	$template->set_var('informations', $LANG_MAPS_1['informations']);
	$template->set_var('name_label', $LANG_MAPS_1['group_overlay_name_label']);
	$template->set_var('name', stripslashes($group['o_group_name']));
	$template->set_var('required_field', $LANG_MAPS_1['required_field']);

	//Form validation
	$template->set_var('save_button', $LANG_MAPS_1['save_button']);
	
	if ($group['o_group_id'] > 0) {
		$template->set_var('delete_button', '<option value="delete">' . $LANG_MAPS_1['delete_button'] . '</option>');
	} else {
		$template->set_var('delete_button', '');
	}
	$template->set_var('ok_button', $LANG_MAPS_1['ok_button']);
	if (isset($group['o_group_id'])) {
		$template->set_var('o_group_id', '<input type="hidden" name="o_group_id" value="' . $group['o_group_id'] .'" />');
	} else {
		$template->set_var('o_group_id', '');
	}
	
	$display .= $template->parse('output', 'map');


    $display .= COM_endBlock();

    return $display;
}


// MAIN
$o_group_id = $_REQUEST['o_group_id'];
$display .= COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
$display .= maps_admin_menu();

switch ($_REQUEST['mode']) {
    case 'delete':
	    //Remove group associations
		//DB_delete($_TABLES['maps_map_overlay'], 'mo_oid', $oid);
		//Delete group of overlays
		//DB_delete($_TABLES['maps_overlays'], 'oid', $oid);
		if (DB_affectedRows('') == 1) {
			$msg = $LANG_MAPS_1['deletion_succes'];
		} else {
			$msg = $LANG_MAPS_1['deletion_fail'];
		}
		// delete complete, return to map list
		echo COM_refresh($_CONF['site_url'] . "/admin/plugins/maps/overlays.php?mode=groups&amp;msg=$msg");

        exit();
        break;

    case 'save':
        if (empty($_REQUEST['o_group_name']) ) {
            $display .= COM_startBlock($LANG_MAPS_1['error'],'','blockheader-message.thtml');
            $display .= $LANG_MAPS_1['missing_field'];
            $display .= COM_endBlock('blockfooter-message.thtml');
            $display .= MAPS_getGroupOverlayForm($_REQUEST);
            break;
        }
		$o_group_name = $_REQUEST['o_group_name'];
        $sql = "o_group_name = '{$o_group_name}'";
		
		if (!empty($_REQUEST['o_group_id'])) { 
		    //edit mode
            $sql = "UPDATE {$_TABLES['maps_overlays_groups']} SET $sql "
                 . "WHERE o_group_id = {$o_group_id}";
        } else {
		    $sql = "INSERT INTO {$_TABLES['maps_overlays_groups']} SET $sql ";
		}
		
        DB_query($sql);

        if (DB_error()) {
            $msg = $LANG_MAPS_1['save_fail'];
        } else {
            $msg = $LANG_MAPS_1['save_success'];
        }
		
        // save complete, return to overlays list
        echo COM_refresh($_CONF['site_admin_url'] . "/plugins/maps/overlays.php?mode=groups&amp;msg=" . urlencode($msg));
        exit();
        break;

    case 'edit':
        // Get the group of overlay to edit and display the form
        if (isset($_REQUEST['o_group_id'])) {
            $sql = "SELECT * FROM {$_TABLES['maps_overlays_groups']} WHERE o_group_id = {$_REQUEST['o_group_id']} LIMIT 1";
            $res = DB_query($sql, 0);
            $A = DB_fetchArray($res);
            $display .= MAPS_getGroupOverlayForm($A);
        } else {
            echo COM_refresh($_CONF['site_url']);
        }
        break;
	

    case 'new':
    default:
        $display .= MAPS_getGroupOverlayForm($overlay);
        break;
}

$display .= COM_siteFooter(0);


COM_output($display);

?>
