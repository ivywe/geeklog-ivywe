<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.4                                                           |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// |                                                                           |
// | Plugin administration page                                                |
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
$vars = array('mode' => 'alpha',
               'cid' => 'number',
			   'id'  => 'number',
			   'msg' => 'text'
			  );

MAPS_filterVars($vars, $_REQUEST);

/**
* List all maps that the user has access to
*
* @retun    string      HTML for the list
*
*/
function MAPS_listmaps()
{
    global $_CONF, $_TABLES, $_IMAGE_TYPE, $LANG_ADMIN, $LANG_MAPS_1;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';
	
	if (DB_count($_TABLES['maps_maps']) == 0){
	return $retval = '';
	}

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_ADMIN['edit'], 'field' => 'edit', 'sort' => false),
        array('text' => $LANG_MAPS_1['id'], 'field' => 'mid', 'sort' => true),
        array('text' => $LANG_MAPS_1['name'], 'field' => 'name', 'sort' => true),
        array('text' => $LANG_MAPS_1['active_field'], 'field' => 'active', 'sort' => true),
        array('text' => $LANG_MAPS_1['hidden_field'], 'field' => 'hidden', 'sort' => true)
    );
    $defsort_arr = array('field' => 'mid', 'direction' => 'asc');

    $text_arr = array(
        'has_extras' => true,
        'form_url' => $_CONF['site_admin_url'] . '/plugins/maps/index.php'
    );
	
	$sql = "SELECT
	            *
            FROM {$_TABLES['maps_maps']}
			WHERE 1=1";

    $query_arr = array(
        'table'          => 'maps_maps',
        'sql'            => $sql,
        'query_fields'   => array('name', 'description'),
        'default_filter' => COM_getPermSQL ('AND', 0, 3)
    );

    $retval .= ADMIN_list('maps', 'plugin_getListField_maps',
                          $header_arr, $text_arr, $query_arr, $defsort_arr);

    return $retval;
}

/**
*   Get an individual field for the maps screen.
*
*   @param  string  $fieldname  Name of field (from the array, not the db)
*   @param  mixed   $fieldvalue Value of the field
*   @param  array   $A          Array of all fields from the database
*   @param  array   $icon_arr   System icon array
*   @param  object  $EntryList  This entry list object
*   @return string              HTML for field display in the table
*/
function plugin_getListField_maps($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $LANG_ADMIN, $LANG_STATIC, $_TABLES, $_MAPS_CONF;

    switch($fieldname) {
        case "edit":
            $retval = COM_createLink($icon_arr['edit'],
                "{$_CONF['site_admin_url']}/plugins/maps/map_edit.php?mode=edit&amp;mid={$A['mid']}");
            break;
        case "name":
            $map_title = stripslashes ($A['name']);
            $url = $_MAPS_CONF['site_url'] .
                                 '/index.php?mode=map&amp;mid=' . $A['mid'];
            $link = COM_createLink($map_title, $url, array('title'=>$LANG_MAPS_1['title_display']));

			if ($A['description'] != '') {
			    $retval = COM_getTooltip($A['name'], $A['description'], $url, $A['name'], 'help');
			} else {
			    $retval = $link;
			}
            break;
        case "id":
            $retval = $A['mid'];
            break;
		case "active":
            if ($fieldvalue == 1) {
			$retval = '<img src="'. $_CONF['site_admin_url'] . '/plugins/maps/images/green_dot.gif" alt="" valign="center">';
			} else {
			$retval = '<img src="'. $_CONF['site_admin_url'] . '/plugins/maps/images/red_dot.gif" alt="">';
			}
            break;
		case "hidden":
            if ($fieldvalue == 0) {
			$retval = '<img src="'. $_CONF['site_admin_url'] . '/plugins/maps/images/green_dot.gif" alt="">';
			} else {
			$retval = '<img src="'. $_CONF['site_admin_url'] . '/plugins/maps/images/red_dot.gif" alt="">';
			}
            break;
        default:
            $retval = stripslashes($fieldvalue);
            break;
    }
    return $retval;
}

// MAIN

if ($_MAPS_CONF['google_api_key'] == '') {
	$display = COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
	$display .= '<p><img src="' . $_CONF['site_admin_url'] . '/plugins/maps/images/maps.png" alt="" align="left" hspace="5">' 
		 . $LANG_MAPS_1['plugin_doc'] . ' <a href="http://geeklog.fr/wiki/plugins:maps" target="_blank">'. $LANG_MAPS_1['online']
		 . '</a>.</p>'
		 . '<p> ' . $LANG_MAPS_1['plugin_conf'] . ' <a href="' . $_CONF['site_admin_url'] . '/configuration.php">'. $LANG_MAPS_1['online']
		 . '</a>.</p>';
	$display .= $LANG_MAPS_1['need_google_api'];
	$display .= COM_siteFooter(0);
	COM_output($display);
	exit ();
}
	
$mode = '';
if (isset($_REQUEST['mode'])) {
    $mode = $_REQUEST['mode'];
}

switch ($mode) {
		
	//Create a new marker for admin
    case 'edit' :
	    echo COM_refresh($_CONF['site_url'] . "/admin/plugins/maps/marker_edit.php");
        exit();
        break;
		
	//Edit marker sumission
    case 'editsubmission' :
        $id = $_REQUEST['id'];
	    echo COM_refresh($_CONF['site_url'] . "/admin/plugins/maps/marker_edit.php?mode=editsubmission&amp;mkid=$id");
        exit();
        break;

    case 'setgeolocation' :
	    MAPS_setGeoLocation();
	    echo COM_refresh($_CONF['site_url'] . "/admin/plugins/maps/index.php?msg=" . urlencode($LANG_MAPS_1['set_geo_location']));
        exit();
        break;

    default :	
        $display = COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
        $display .= MAPS_admin_menu();

    if (!empty($_REQUEST['msg'])) {
        $display .= COM_startBlock($LANG_MAPS_1['message'],'','blockheader-message.thtml');
        $display .= $_REQUEST['msg'];
        $display .= COM_endBlock('blockfooter-message.thtml');
    }

    $display .= '<img src="' . $_CONF['site_admin_url'] . '/plugins/maps/images/maps.png" alt="" align="left" hspace="5">' 
		     . '<p>' .$LANG_MAPS_1['plugin_doc'] . ' <a href="http://geeklog.fr/downloads/index.php/maps" target="_blank">'. $LANG_MAPS_1['online']
		     . '</a>.</p>';

    $display .= '<br /><h1>' . $LANG_MAPS_1['maps_list'] . '</h1>';
    $display .= '<p>' . $LANG_MAPS_1['you_can'] . '<a href="' . $_CONF['site_url'] . '/admin/plugins/maps/map_edit.php">' . $LANG_MAPS_1['create_map'] . '</a>.</p><p>&nbsp;</p>';

   $display .= MAPS_listmaps();

   $display .= COM_siteFooter(0);
}

COM_output($display);

?>
