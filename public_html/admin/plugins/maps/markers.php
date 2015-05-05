<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.3.2                                                         |
// +---------------------------------------------------------------------------+
// | markers.php                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2014 by the following authors:                              |
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

/**
* List all markers that the user has access to
*
* @retun    string      HTML for the list
*
*/
function MAPS_listMarkersAdmin()
{
    global $_CONF, $_TABLES, $_IMAGE_TYPE, $LANG_ADMIN, $LANG_MAPS_1;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';
	
	if (DB_count($_TABLES['maps_markers']) == 0){
	return $retval = '';
	}

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_MAPS_1['id'], 'field' => 'mkid', 'sort' => true),
        array('text' => $LANG_MAPS_1['name'], 'field' => 'name', 'sort' => true),
		array('text' => $LANG_MAPS_1['map_label'], 'field' => 'mapname', 'sort' => true),
        array('text' => $LANG_MAPS_1['active_field'], 'field' => 'active', 'sort' => true),
        array('text' => $LANG_MAPS_1['hidden_field'], 'field' => 'hidden', 'sort' => true),
        array('text' => $LANG_ADMIN['edit'], 'field' => 'edit', 'sort' => false),
    );
    
	$defsort_arr = array('field' => 'modified', 'direction' => 'desc');

    $text_arr = array(
        'has_extras' => true,
        'form_url' => $_CONF['site_admin_url'] . '/plugins/maps/markers.php'
    );
	
	$sql = "SELECT
	            a.*, b.name as mapname
            FROM {$_TABLES['maps_markers']} AS a
			LEFT JOIN
			     {$_TABLES['maps_maps']} AS b
			ON a.mid = b.mid
			WHERE 1=1";
	
    $query_arr = array(
        'sql'            => $sql,
        'default_filter' => COM_getPermSQL ('AND', 0, 3)
    );

    $retval .= ADMIN_list('markers', 'plugin_getListField_markers',
                          $header_arr, $text_arr, $query_arr, $defsort_arr);

    return $retval;
}

/**
*   Get an individual field for the markers screen.
*
*   @param  string  $fieldname  Name of field (from the array, not the db)
*   @param  mixed   $fieldvalue Value of the field
*   @param  array   $A          Array of all fields from the database
*   @param  array   $icon_arr   System icon array
*   @param  object  $EntryList  This entry list object
*   @return string              HTML for field display in the table
*/
function plugin_getListField_markers($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $_MAPS_CONF, $LANG_ADMIN, $LANG_STATIC, $_TABLES;

    switch($fieldname) {
        case "edit":
            $retval = COM_createLink($icon_arr['edit'],
                "{$_CONF['site_admin_url']}/plugins/maps/marker_edit.php?mode=edit&amp;mkid={$A['mkid']}");
            break;
        case "name":
            $map_title = stripslashes ($A['name']);
            $url = $_MAPS_CONF['site_url'] .
                                 '/markers.php?mode=show&amp;mkid=' . $A['mkid'] . '&amp;mid=' . $A['mid'];
            $retval = COM_createLink($map_title, $url, array('title'=>$LANG_MAPS_1['title_display']));
            break;

        case "id":
            $retval = $A['mkid'];
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
$display .= COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
$display .= maps_admin_menu();

$display .= '<br /><h1>' . $LANG_MAPS_1['markers_list'] . '</h1>';
$display .= '<p>' . $LANG_MAPS_1['you_can'] . '<a href="' . $_CONF['site_url'] . '/admin/plugins/maps/marker_edit.php">' . $LANG_MAPS_1['create_marker'] . '</a>.</p>';

$display .= MAPS_listMarkersAdmin();

$display .= COM_siteFooter(0);

COM_output($display);

?>
