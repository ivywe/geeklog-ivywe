<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.2                                                           |
// +---------------------------------------------------------------------------+
// | overlays.php                                                               |
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
			   'msg' => 'text'
			  );

MAPS_filterVars($vars, $_REQUEST);

/**
* List all overlays that the user has access to
*
* @retun    string      HTML for the list
*
*/
function MAPS_listOverlays()
{
    global $_CONF, $_TABLES, $_IMAGE_TYPE, $LANG_ADMIN, $LANG_MAPS_1;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_MAPS_1['id'], 'field' => 'oid', 'sort' => true),
        array('text' => $LANG_MAPS_1['name'], 'field' => 'o_name', 'sort' => true),
		array('text' => $LANG_MAPS_1['group'], 'field' => 'o_group_name', 'sort' => true),
		array('text' => $LANG_MAPS_1['order'], 'field' => 'o_order', 'sort' => true),
		array('text' => $LANG_MAPS_1['move'], 'field' => 'move', 'sort' => true),
        array('text' => $LANG_MAPS_1['active_field'], 'field' => 'o_active', 'sort' => true),
        array('text' => $LANG_ADMIN['edit'], 'field' => 'edit', 'sort' => false),
    );
    $defsort_arr = array('field' => 'o_order', 'direction' => 'asc');

    $text_arr = array(
        'has_extras' => true,
        'form_url' => $_CONF['site_admin_url'] . '/plugins/maps/overlays.php'
    );
	
	$sql = "SELECT
	            o.*, og.o_group_name
            FROM {$_TABLES['maps_overlays']} AS o
			LEFT JOIN {$_TABLES['maps_overlays_groups']} AS og
			ON o.o_group = og.o_group_id
			WHERE 1=1";

    $query_arr = array(
        'sql'            => $sql,
		'query_fields'   => array('o_name'),
        'default_filter' => ''
    );

    $retval .= ADMIN_list('overlays', 'MAPS_getListField_overlays',
                          $header_arr, $text_arr, $query_arr, $defsort_arr);

    return $retval;
}

/**
*   Get an individual field for the overlays screen.
*
*   @param  string  $fieldname  Name of field (from the array, not the db)
*   @param  mixed   $fieldvalue Value of the field
*   @param  array   $A          Array of all fields from the database
*   @param  array   $icon_arr   System icon array
*   @param  object  $EntryList  This entry list object
*   @return string              HTML for field display in the table
*/
function MAPS_getListField_overlays($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $_MAPS_CONF, $LANG_ADMIN, $LANG_STATIC, $_TABLES;
	
	$token = SEC_createToken();

    switch($fieldname) {
        case "edit":
            $retval = COM_createLink($icon_arr['edit'],
                "{$_CONF['site_admin_url']}/plugins/maps/overlay_edit.php?mode=edit&amp;oid={$A['oid']}");
            break;
			
		case "o_name":
		    $overlay_image = $_MAPS_CONF['path_overlay_images'] . $A['o_image'];
		    if (is_file($overlay_image)) {
			    $retval = COM_getTooltip($A['o_name'], '<img src="' . $_MAPS_CONF['site_url'] . '/timthumb.php?src='
				. $_MAPS_CONF['images_overlay_url'] . $A['o_image'] . '&amp;w=200&amp;q=70&amp;zc=1" alt="" />', '', $A['o_name'], $template = 'help');
				
			} else {
				$retval = $A['o_name'];
			}
		    
			break;
			
		case 'move':
			$csrftoken = '&amp;' . CSRF_TOKEN . '=' . $token;
			$retval.="<a href=\"{$_CONF['site_admin_url']}/plugins/maps/overlays.php?op=move&amp;oid={$A['oid']}&amp;where=up{$csrftoken}\" alt=\"{$LANG21[58]}\"" . XHTML . ">"
			        ."<img src=\"{$_CONF['layout_url']}/images/admin/up.png\"></a> "
					."<a href=\"{$_CONF['site_admin_url']}/plugins/maps/overlays.php?op=move&amp;oid={$A['oid']}&amp;where=dn${csrftoken}\" alt=\"{$LANG21[57]}\"" . XHTML . ">"
					."<img src=\"{$_CONF['layout_url']}/images/admin/down.png\"></a> "
					."</a>";
            break;

		case "o_active":
            if ($fieldvalue == 1) {
			$retval = '<img src="'. $_CONF['site_admin_url'] . '/plugins/maps/images/green_dot.gif" alt="" valign="center">';
			} else {
			$retval = '<img src="'. $_CONF['site_admin_url'] . '/plugins/maps/images/red_dot.gif" alt="">';
			}
            break;

        default:
            $retval = $fieldvalue;
            break;
    }
    return $retval;
}

/**
* Re-orders all overlays in increments of 10
*
*/
function MAPS_reorderOverlays()
{
    global $_TABLES;

    $sql = "SELECT * 
			FROM {$_TABLES['maps_overlays']}
        	ORDER BY o_order ASC;";
    $result = DB_query($sql);
    $nrows = DB_numRows($result);

    $blockOrd = 10;
    $stepNumber = 10;

    for ($i = 0; $i < $nrows; $i++) {
        $A = DB_fetchArray($result);

        if ($A['o_order'] != $blockOrd) {  // only update incorrect ones
            $q = "UPDATE " . $_TABLES['maps_overlays'] . " SET o_order = '" .
                  $blockOrd . "' WHERE oid = '" . $A['oid'] ."'";
            DB_query($q);
        }
        $blockOrd += $stepNumber;
    }
}

/**
* List all group of overlays that the user has access to
*
* @retun    string      HTML for the list
*
*/
function MAPS_listOverlaysGroups ()
{
    global $_CONF, $_TABLES, $_IMAGE_TYPE, $LANG_ADMIN, $LANG_MAPS_1;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_MAPS_1['id'], 'field' => 'o_group_id', 'sort' => true),
        array('text' => $LANG_MAPS_1['name'], 'field' => 'o_group_name', 'sort' => true),
        array('text' => $LANG_ADMIN['edit'], 'field' => 'edit', 'sort' => false),
    );
    $defsort_arr = array('field' => 'o_group_name', 'direction' => 'asc');

    $text_arr = array(
        'has_extras' => true,
        'form_url' => $_CONF['site_admin_url'] . '/plugins/maps/overlays.php?mode=groups'
    );
	
	$sql = "SELECT
	            *
            FROM {$_TABLES['maps_overlays_groups']}
			WHERE 1=1";

    $query_arr = array(
        'sql'            => $sql
    );

    $retval .= ADMIN_list('overlays', 'MAPS_getListField_overlaysGroups',
                          $header_arr, $text_arr, $query_arr, $defsort_arr);

    return $retval;
}

/**
*   Get an individual field for the overlays screen.
*
*   @param  string  $fieldname  Name of field (from the array, not the db)
*   @param  mixed   $fieldvalue Value of the field
*   @param  array   $A          Array of all fields from the database
*   @param  array   $icon_arr   System icon array
*   @param  object  $EntryList  This entry list object
*   @return string              HTML for field display in the table
*/
function MAPS_getListField_overlaysGroups($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $_MAPS_CONF, $LANG_ADMIN, $LANG_STATIC, $_TABLES;
	
	$token = SEC_createToken();

    switch($fieldname) {
        case "edit":
            $retval = COM_createLink($icon_arr['edit'],
                "{$_CONF['site_admin_url']}/plugins/maps/overlay_group_edit.php?mode=edit&amp;o_group_id={$A['o_group_id']}");
            break;
			
        default:
            $retval = $fieldvalue;
            break;
    }
    return $retval;
}

// MAIN
$display .= COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
$display .= maps_admin_menu();

if (!empty($_REQUEST['msg'])) {
    $display .= MAPS_message($_REQUEST['msg']);
}

if ( !file_exists($_MAPS_CONF['path_overlay_images']) || !is_writable($_MAPS_CONF['path_overlay_images']) ) {
	$display .= COM_showMessageText( '>> '. $_MAPS_CONF['path_overlay_images'] . '<p>' . $LANG_MAPS_1['overlay_not_writable'] . '</p>');
} else {
    $display .= '<br /><h1>' . $LANG_MAPS_1['overlays_list'] . '</h1>';
    $display .= '<p><ul><li><a href="' . $_CONF['site_url'] . '/admin/plugins/maps/overlay_edit.php">' . $LANG_MAPS_1['create_overlay'] 
	    . '</a><li><a href="' . $_CONF['site_url'] . '/admin/plugins/maps/overlays.php?mode=groups">' . $LANG_MAPS_1['manage_groups'] . '</a><li><a href="' 
		. $_CONF['site_url'] . '/admin/plugins/maps/overlay_group_edit.php?mode=new">' . $LANG_MAPS_1['create_group'] . '</a></ul></p>';
	
	MAPS_reorderOverlays();
	
	switch ($_REQUEST['mode']) {
	    case 'groups':
		    $display .= MAPS_listOverlaysGroups();
		    break;
		case  'move':
		    $oid = COM_applyFilter($_GET['oid']);
		    $where = COM_applyFilter($_GET['where']);

		    if (DB_count($_TABLES['maps_overlays'], "oid", $oid) == 1) {

			    switch ($where) {

				    case ("up"): $q = "UPDATE " . $_TABLES['maps_overlays'] . " SET o_order = o_order-11 WHERE oid = '" . $oid . "'";
						DB_query($q);
					    MAPS_reorderOverlays();
					    break;

				    case ("dn"): $q = "UPDATE " . $_TABLES['maps_overlays'] . " SET o_order = o_order+11 WHERE oid = '" . $oid . "'";
						DB_query($q);
					    MAPS_reorderOverlays();
						break;
			    }
		    }

		default :
		    $display .= MAPS_listOverlays();
	}
}

$display .= COM_siteFooter(0);

COM_output($display);

?>
