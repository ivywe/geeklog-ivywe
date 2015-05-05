<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.2.2                                                         |
// +---------------------------------------------------------------------------+
// | overlay_edit.php                                                          |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011-2012 by the following authors:                         |
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
$vars = array('mode'     => 'alpha',
              'oid'      => 'alpha',
              'name'     => 'text',
			  'o_group'  => 'number',
			  'image'    => 'text',
			  'sw_lat'   => 'alpha',
			  'sw_lng'   => 'alpha',
			  'ne_lat'   => 'alpha',
			  'ne_lng'   => 'alpha',
			  'mid'      => 'number',
			  'active'   => 'number',
			  'zoom_min' => 'number',
			  'zoom_max' => 'number',
			  'order'    => 'number',
			  );

MAPS_filterVars($vars, $_REQUEST);

/**
 * This function creates a Overlay Form
 *
 * Creates a Form for an overlay using the supplied defaults (if specified).
 *
 * @param array $overlay array of values describing an overlay
 * @return string HTML string of overlay form
 */
function MAPS_getOverlayForm($overlay = array()) {

    global $_CONF, $_TABLES, $_MAPS_CONF, $LANG_MAPS_1, $LANG_configselects, $LANG_ACCESS, $_USER, $_GROUPS, $_SCRIPTS;
    
	$display = COM_startBlock('<h1>' . $LANG_MAPS_1['overlay_edit'] . ' ' . $overlay['name']. '</h1>');
	
	$map_options = MAPS_recurseMaps($overlay['mid']);

	if ($map_options == '') {
		$display .= COM_startBlock($LANG_MAPS_1['error'],'','blockheader-message.thtml');
        $display .= $LANG_MAPS_1['maps_empty'];
        $display .= COM_endBlock('blockfooter-message.thtml');

	} else {
		$template = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
		$template->set_file(array('map' => 'overlay_form.thtml'));

	    $template->set_var('edit_overlay_text', $LANG_MAPS_1['edit_overlay_text']);
		
		$template->set_var('yes', $LANG_MAPS_1['yes']);
		$template->set_var('no', $LANG_MAPS_1['no']);
		
		//informations
		$template->set_var('overlay_presentation', $LANG_MAPS_1['overlay_presentation']);
		$template->set_var('informations', $LANG_MAPS_1['informations']);
		$template->set_var('name_label', $LANG_MAPS_1['overlay_name_label']);
		$template->set_var('name', stripslashes($overlay['o_name']));
		$template->set_var('group', MAPS_selectGroupOverlays($overlay['o_group']) );
		$template->set_var('sw_lat', $LANG_MAPS_1['sw_lat']);
		$template->set_var('sw_lat_value', $overlay['o_sw_lat']);
		$template->set_var('sw_lng', $LANG_MAPS_1['sw_lng']);
		$template->set_var('sw_lng_value', $overlay['o_sw_lng']);
		$template->set_var('ne_lat', $LANG_MAPS_1['ne_lat']);
		$template->set_var('ne_lat_value', $overlay['o_ne_lat']);
		$template->set_var('ne_lng', $LANG_MAPS_1['ne_lng']);
		$template->set_var('ne_lng_value', $overlay['o_ne_lng']);
		$template->set_var('required_field', $LANG_MAPS_1['required_field']);
				
		//active
		$template->set_var('active', $LANG_MAPS_1['overlay_active']);
		if ($overlay['o_active'] == '') {
			$overlay['o_active'] = $_MAPS_CONF['map_active'];
		}
		if ($overlay['o_active'] == 1) {
			$template->set_var('active_yes', ' selected');
			$template->set_var('active_no', '');
		} else {
			$template->set_var('active_yes', '');
			$template->set_var('active_no', ' selected');
		}
		//zoom
		$template->set_var('zoom_min_label', $LANG_MAPS_1['zoom_min_label']);
		$template->set_var('zoom_min', $overlay['o_zoom_min']);
		$template->set_var('zoom_max_label', $LANG_MAPS_1['zoom_max_label']);
		$template->set_var('zoom_max', $overlay['o_zoom_max']);
		
		//Image
		$template->set_var('image', $LANG_MAPS_1['image']);
		$template->set_var('image_message', $LANG_MAPS_1['image_message']);
		$overlay_image = $_MAPS_CONF['path_overlay_images'] . $overlay['o_image'];
		if (is_file($overlay_image)) {
			$template->set_var('overlay_image','<p>' . $LANG_MAPS_1['image_replace'] . '<p><p><img src="' . $_MAPS_CONF['site_url'] . '/timthumb.php?src='
			. $_MAPS_CONF['images_overlay_url'] . $overlay['o_image'] . '&amp;w=350&amp;q=70&amp;zc=1" alt="" /></p>');
		} else {
			$template->set_var('overlay_image', '');
		}
		
		//Form validation
		$template->set_var('save_button', $LANG_MAPS_1['save_button']);
		
		if ($overlay['oid'] > 0) {
    		$template->set_var('delete_button', '<option value="delete">' . $LANG_MAPS_1['delete_button'] . '</option>');
		} else {
		    $template->set_var('delete_button', '');
		}
		$template->set_var('ok_button', $LANG_MAPS_1['ok_button']);
		if (isset($overlay['oid'])) {
			$template->set_var('oid', '<input type="hidden" name="oid" value="' . $overlay['oid'] .'" />');
		} else {
			$template->set_var('oid', '');
		}
		
		$display .= $template->parse('output', 'map');
	}

    $display .= COM_endBlock();

    return $display;
}

function MAPS_saveOverlayImage ($overlay, $FILES, $oid) {
    global $_CONF, $_MAPS_CONF, $_TABLES, $LANG24;
	
    $args = &$overlay;

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

	// OK, let's upload any pictures with the overlay
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
	
	if (!$upload->setPath($_MAPS_CONF['path_overlay_images'])) {
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
		$filenames = 'overlay_' . $oid  . '.' . $fextension;
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
		
		DB_query("UPDATE {$_TABLES['maps_overlays']} SET o_image = '" . $filenames . "' WHERE oid=" . $oid);
	}

	return true;
}


function MAPS_deleteOverlayImage ($image)
{
    global $_CONF;

    if (empty ($image)) {
        return;
    }
	
	$pi = $_MAPS_CONF['path_overlay_images'] . $image;
			if (!@unlink ($pi)) {
                // log the problem but don't abort the script
                echo COM_errorLog ('Unable to remove the following overlay image from maps plugin: ' . $image);
            }
}

function MAPS_selectGroupOverlays ($selected)
{
    global $_TABLES, $LANG_MAPS_1;
    
    $retval = '<b>' . $LANG_MAPS_1['group_label'] . '</b> <select name="o_group">' .
            '<option value="0">' . $LANG_MAPS_1['choose_group'] . '</option>' .
            COM_optionList( $_TABLES['maps_overlays_groups'], 'o_group_id,o_group_name', $selected) .
            '</select>'; 
    
    return $retval;
}

// MAIN
$oid = $_REQUEST['oid'];
$display .= COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
$display .= maps_admin_menu();

switch ($_REQUEST['mode']) {
    case 'delete':
	    //Remove overlay associations
		DB_delete($_TABLES['maps_map_overlay'], 'mo_oid', $oid);
		//Delete overlay
		DB_delete($_TABLES['maps_overlays'], 'oid', $oid);
		if (DB_affectedRows('') == 1) {
			$msg = $LANG_MAPS_1['deletion_succes'];
		} else {
			$msg = $LANG_MAPS_1['deletion_fail'];
		}
		// delete complete, return to map list
		echo COM_refresh($_CONF['site_url'] . "/admin/plugins/maps/overlays.php?msg=$msg");

        exit();
        break;

    case 'save':
        if (empty($_REQUEST['name']) || $_REQUEST['lat'] || $_REQUEST['lng']) {
            $display .= COM_startBlock($LANG_MAPS_1['error'],'','blockheader-message.thtml');
            $display .= $LANG_MAPS_1['missing_field'];
            $display .= COM_endBlock('blockfooter-message.thtml');
            $display .= MAPS_getOverlayForm($_REQUEST);
            break;
        }
		
        // lat, lng can only contain numbers and a decimal
		$sw_lat = strval ($_REQUEST['sw_lat']);
		$sw_lng = strval ($_REQUEST['sw_lng']);
		$ne_lat = strval ($_REQUEST['ne_lat']);
		$ne_lng = strval ($_REQUEST['ne_lng']);
		
        $sql = "o_name = '{$_REQUEST['name']}', "
             . "o_active = '{$_REQUEST['active']}', "
			 . "o_group = '{$_REQUEST['o_group']}', "
			 . "o_sw_lat = '{$sw_lat}', "
			 . "o_sw_lng = '{$sw_lng}', "
			 . "o_ne_lat = '{$ne_lat}', "
			 . "o_ne_lng = '{$ne_lng}', "
			 . "o_zoom_min = '{$_REQUEST['zoom_min']}', "
			 . "o_zoom_max = '{$_REQUEST['zoom_max']}'
             ";
		if (!empty($_REQUEST['oid'])) { //edit mode
            $sql = "UPDATE {$_TABLES['maps_overlays']} SET $sql "
                 . "WHERE oid = {$oid}";
        } else { // create mode

            $sql = "INSERT INTO {$_TABLES['maps_overlays']} SET $sql ";
        }
        DB_query($sql);

        if (DB_error()) {
            $msg = $LANG_MAPS_1['save_fail'];
        } else {
            $msg = $LANG_MAPS_1['save_success'];
        }
		//Process images
		if ( $oid == '') $oid = DB_insertId();
		if (!empty($_FILES)) {
			MAPS_saveOverlayImage($_REQUEST, $_FILES, $oid);
		}
        // save complete, return to overlays list
        echo COM_refresh($_CONF['site_admin_url'] . "/plugins/maps/overlays.php?msg=" . urlencode($msg));
        exit();
        break;

    case 'edit':
        // Get the overlay to edit and display the form
        if (isset($_REQUEST['oid'])) {
            $sql = "SELECT * FROM {$_TABLES['maps_overlays']} WHERE oid = {$_REQUEST['oid']} LIMIT 1";
            $res = DB_query($sql, 0);
            $A = DB_fetchArray($res);
            $display .= MAPS_getOverlayForm($A);
        } else {
            echo COM_refresh($_CONF['site_url']);
        }
        break;
	

    case 'new':
    default:
        $display .= MAPS_getOverlayForm($overlay);
        break;
}

$display .= COM_siteFooter(0);


COM_output($display);

?>
