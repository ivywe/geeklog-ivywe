<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.2                                                           |
// +---------------------------------------------------------------------------+
// | icons.php                                                                 |
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
              'id' => 'alpha',
              'icon_name' => 'text',
			  'icon_image' => 'text',
			  );

MAPS_filterVars($vars, $_REQUEST);

/**
* List all icons that the admin has access to
*
* @retun    string      HTML for the list
*
*/
function MAPS_listIcons()
{
    global $_CONF, $_TABLES, $_IMAGE_TYPE, $LANG_ADMIN, $LANG_MAPS_1;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_MAPS_1['id'], 'field' => 'icon_id', 'sort' => true),
        array('text' => $LANG_MAPS_1['icons'], 'field' => 'icon_name', 'sort' => true),
        array('text' => $LANG_MAPS_1['image'], 'field' => 'icon_image', 'sort' => false),
    );
    $defsort_arr = array('field' => 'icon_name', 'direction' => 'asc');

    $text_arr = array(
        'has_extras' => true,
        'form_url' => $_CONF['site_admin_url'] . '/plugins/maps/icons.php'
    );
	
	$sql = "SELECT
	            *
            FROM {$_TABLES['maps_map_icons']}
			WHERE 1=1";

    $query_arr = array(
        'sql'            => $sql,
		'query_fields'   => array('icon_name'),
        'default_filter' => ''
    );

    $retval .= ADMIN_list('icons', 'MAPS_getListField_icons',
                          $header_arr, $text_arr, $query_arr, $defsort_arr);

    return $retval;
}

/**
*   Get an individual field for the icons screen.
*
*   @param  string  $fieldname  Name of field (from the array, not the db)
*   @param  mixed   $fieldvalue Value of the field
*   @param  array   $A          Array of all fields from the database
*   @param  array   $icon_arr   System icon array
*   @param  object  $EntryList  This entry list object
*   @return string              HTML for field display in the table
*/
function MAPS_getListField_icons($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $_MAPS_CONF, $LANG_ADMIN, $LANG_STATIC, $_TABLES;

    switch($fieldname) {
        case "icon_id":
            $retval = COM_createLink($icon_arr['edit'],
                "{$_CONF['site_admin_url']}/plugins/maps/icons.php?mode=edit&amp;id={$A['icon_id']}");
            break;
			
		case "icon_name":
			$retval = $A['icon_name'];
			break;
		
		case "icon_image":
		    $icon_image = $_MAPS_CONF['path_icons_images'] . $A['icon_image'];
		    if (is_file($icon_image)) {
			    $retval = '<img src="' . $_MAPS_CONF['images_icons_url'] . $A['icon_image'] . '" alt="' . $A['icon_image'] . '">';
			} else {
				$retval = '';
			}
		    
			break;

        default:
            $retval = $fieldvalue;
            break;
    }
    return $retval;
}

/**
 * This function creates a icon Form
 *
 * Creates a Form for an icon using the supplied defaults (if specified).
 *
 * @param array $icon array of values describing an icon
 * @return string HTML string of icon form
 */
function MAPS_geticonForm($icon = array()) {

    global $_CONF, $_TABLES, $_MAPS_CONF, $LANG_MAPS_1, $LANG_configselects, $LANG_ACCESS, $_USER, $_GROUPS, $_SCRIPTS;
    
	$display = COM_startBlock('<h1>' . $LANG_MAPS_1['icon_edit'] . '</h1>');
	
	$template = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
	$template->set_file(array('icon' => 'icon_form.thtml'));
	$template->set_var('yes', $LANG_MAPS_1['yes']);
	$template->set_var('no', $LANG_MAPS_1['no']);
	
	//informations
	$template->set_var('icon_presentation', $LANG_MAPS_1['icon_presentation']);
	$template->set_var('informations', $LANG_MAPS_1['informations']);
	$template->set_var('name_label', $LANG_MAPS_1['icon_name_label']);
	$template->set_var('name', stripslashes($icon['icon_name']));
	$template->set_var('required_field', $LANG_MAPS_1['required_field']);
	
	//Image
	$template->set_var('image', $LANG_MAPS_1['image']);
	$template->set_var('image_message', $LANG_MAPS_1['image_message']);
	$icon_image = $_MAPS_CONF['path_icons_images'] . $icon['icon_image'];
	if (is_file($icon_image)) {
		$template->set_var('icon_image','<p>' . $LANG_MAPS_1['image_replace'] . '<p><p><img src="' . $_MAPS_CONF['images_icons_url'] . $icon['icon_image'] .'" alt="' . $icon['icon_image'] . '" /></p>');
	} else {
		$template->set_var('icon_image', '');
	}
	
	//Form validation
	$template->set_var('save_button', $LANG_MAPS_1['save_button']);
	
	if ($icon['icon_id'] > 0) {
		$template->set_var('delete_button', '<option value="delete">' . $LANG_MAPS_1['delete_button'] . '</option>');
	} else {
		$template->set_var('delete_button', '');
	}
	$template->set_var('ok_button', $LANG_MAPS_1['ok_button']);
	if (isset($icon['icon_id']) && $icon['icon_id'] != '') {
		$template->set_var('id', '<input type="hidden" name="id" value="' . $icon['icon_id'] .'" />');
	} else {
		$template->set_var('id', '');
	}
	
	$display .= $template->parse('output', 'icon');


    $display .= COM_endBlock();

    return $display;
}

function MAPS_saveIconImage ($icon, $FILES, $id) {
    global $_CONF, $_MAPS_CONF, $_TABLES, $LANG24;
	
    $args = &$icon;

    // Handle Magic GPC Garbage:
    while (list($key, $value) = each($args)) {
        if (!is_array($value)) {
            $args[$key] = COM_stripslashes($value);
        } else {
            while (list($subkey, $subvalue) = each($value)) {
                $value[$subkey] = COM_stripslashes($subvalue);
            }
        }
    }

	// OK, let's upload any pictures with the icon
	require_once($_CONF['path_system'] . 'classes/upload.class.php');
	$upload = new upload();

	//Debug with story debug function
	if (isset ($_CONF['debug_image_upload']) && $_CONF['debug_image_upload']) {
		$upload->setLogFile ($_CONF['path'] . 'logs/error.log');
		$upload->setDebug (true);
	}
	$upload->setMaxFileUploads (1);
	if (!empty($_CONF['image_lib'])) {
		if ($_CONF['image_lib'] == 'imagemagick') {
			// Using imagemagick
			$upload->setMogrifyPath ($_CONF['path_to_mogrify']);
		} elseif ($_CONF['image_lib'] == 'netpbm') {
			// using netPBM
			$upload->setNetPBM ($_CONF['path_to_netpbm']);
		} elseif ($_CONF['image_lib'] == 'gdlib') {
			// using the GD library
			$upload->setGDLib ();
		}
		$upload->setAutomaticResize(true);
		$upload->keepOriginalImage (false);

		if (isset($_CONF['jpeg_quality'])) {
			$upload->setJpegQuality($_CONF['jpeg_quality']);
		}
	}
	$upload->setAllowedMimeTypes (array (
			'image/gif'   => '.gif',
			'image/jpeg'  => '.jpg,.jpeg',
			'image/pjpeg' => '.jpg,.jpeg',
			'image/x-png' => '.png',
			'image/png'   => '.png'
			));
	
	if (!$upload->setPath($_MAPS_CONF['path_icons_images'])) {
		$output = COM_siteHeader ('menu', $LANG24[30]);
		$output .= COM_startBlock ($LANG24[30], '', COM_getBlockTemplate ('_msg_block', 'header'));
		$output .= $upload->printErrors (false);
		$output .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
		$output .= COM_siteFooter ();
		echo $output;
		exit;
	}

	// NOTE: if $_CONF['path_to_mogrify'] is set, the call below will
	// force any images bigger than the passed dimensions to be resized.
	// If mogrify is not set, any images larger than these dimensions
	// will get validation errors
	$upload->setMaxDimensions($_MAPS_CONF['max_image_width'], $_MAPS_CONF['max_image_height']);
	$upload->setMaxFileSize($_MAPS_CONF['max_image_size']); // size in bytes, 1048576 = 1MB

	// Set file permissions on file after it gets uploaded (number is in octal)
	$upload->setPerms('0644');

	$curfile = current($FILES);
	if (!empty($curfile['name'])) {
		$pos = strrpos($curfile['name'],'.') + 1;
		$fextension = substr($curfile['name'], $pos);
		$filenames = 'icon_' . $id  . '.' . $fextension;
	}
    if ($filenames != '') {
		$upload->setFileNames($filenames);
		reset($FILES);
		$upload->uploadFiles();

		if ($upload->areErrors()) {
			$retval = COM_siteHeader('menu', $LANG24[30]);
			$retval .= COM_startBlock ($LANG24[30], '',
						COM_getBlockTemplate ('_msg_block', 'header'));
			$retval .= $upload->printErrors(false);
			$retval .= COM_endBlock(COM_getBlockTemplate ('_msg_block', 'footer'));
			$retval .= COM_siteFooter();
			echo $retval;
			exit;
		}
		
		DB_query("UPDATE {$_TABLES['maps_map_icons']} SET icon_image = '" . $filenames . "' WHERE icon_id=" . $id);
	}

	return true;
}


function MAPS_deleteIconImage ($image)
{
    global $_CONF;

    if (empty ($image)) {
        return;
    }
	
	$pi = $_MAPS_CONF['path_icons_images'] . $image;
			if (!@unlink ($pi)) {
                // log the problem but don't abort the script
                echo COM_errorLog ('Unable to remove the following icon image from maps plugin: ' . $image);
            }
}

// MAIN
$display .= COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
$display .= maps_admin_menu();

if (!empty($_REQUEST['msg'])) {
    $display .= MAPS_message($_REQUEST['msg']);
}

if ( !file_exists($_MAPS_CONF['path_icons_images']) || !is_writable($_MAPS_CONF['path_icons_images']) ) {
	$display .= MAPS_message( '>> '. $_MAPS_CONF['path_icons_images'] . '<p>' . $LANG_MAPS_1['icons_not_writable'] . '</p>');
} else {
    $id = $_REQUEST['id'];
    switch ($_REQUEST['mode']) {
	
        case 'delete':
		    //Remove icon associations
			$q = "UPDATE " . $_TABLES['maps_markers'] . " SET mk_icon = 0, mk_default = 1 WHERE mk_icon = '" . $id . "'";
			DB_query($q);
			//Delete icon
			DB_delete($_TABLES['maps_map_icons'], 'icon_id', $id);
			if (DB_affectedRows('') == 1) {
				$msg = $LANG_MAPS_1['deletion_succes'];
			} else {
				$msg = $LANG_MAPS_1['deletion_fail'];
			}
			// delete complete, return to icon list
			echo COM_refresh($_CONF['site_url'] . "/admin/plugins/maps/icons.php?msg=$msg");

			exit();
			break;;
			
		case 'edit':
		    // Get the icon to edit and display the form
			if (isset($_REQUEST['id']) && $_REQUEST['id']!= '' ) {
				$sql = "SELECT * FROM {$_TABLES['maps_map_icons']} WHERE icon_id = {$_REQUEST['id']} LIMIT 1";
				$res = DB_query($sql, 0);
				$A = DB_fetchArray($res);
				$display .= MAPS_getIconForm($A);
			} else {
			    $A = array();
				$display .= MAPS_getIconForm($A);
			}
		    break;
			
		case 'save':

		    if (empty($_REQUEST['icon_name'])) {
				$display .= COM_startBlock($LANG_MAPS_1['error'],'','blockheader-message.thtml');
				$display .= $LANG_MAPS_1['missing_field'];
				$display .= COM_endBlock('blockfooter-message.thtml');
				$display .= MAPS_getIconForm($_REQUEST);
				break;
			}
			$_REQUEST['icon_name'] = addslashes($_REQUEST['icon_name']);
			
			$sql = "icon_name = '{$_REQUEST['icon_name']}'
				 ";
			if (!empty($_REQUEST['id'])) { //edit mode
				$sql = "UPDATE {$_TABLES['maps_map_icons']} SET $sql "
					 . "WHERE icon_id = {$id}";
			} else { // create mode
				$sql = "INSERT INTO {$_TABLES['maps_map_icons']} SET $sql ";
			}
			DB_query($sql);

			if (DB_error()) {
				$msg = $LANG_MAPS_1['save_fail'];
			} else {
				$msg = $LANG_MAPS_1['save_success'];
			}
			//Process images
			if ( $id == '') $id = DB_insertId();
			if (!empty($_FILES)) {
				MAPS_saveIconImage($_REQUEST, $_FILES, $id);
			}
			// save complete, return to icons list
			echo COM_refresh($_CONF['site_admin_url'] . "/plugins/maps/icons.php?msg=" . urlencode($msg));
			exit();
			break;
			
		default :
            $display .= '<br /><h1>' . $LANG_MAPS_1['icons_list'] . '</h1>';
            $display .= '<p>' . $LANG_MAPS_1['you_can'] . '<a href="' . $_CONF['site_url'] . '/admin/plugins/maps/icons.php?mode=edit">' . $LANG_MAPS_1['create_icon'] . '</a>.</p>';
            $display .= MAPS_listIcons();
	}
}

$display .= COM_siteFooter(0);

COM_output($display);

?>
