<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.4                                                           |
// +---------------------------------------------------------------------------+
// | map_edit.php                                                              |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010-2014 by the following authors:                         |
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
 * Displays a form for the editing maps
 *
 * Allows for the altering and deletion of existing maps as well as the
 * creation of new maps
 *
 */
//print_r($_REQUEST);exit;
require_once '../../../lib-common.php';
require_once '../../auth.inc.php';
require_once 'edit_functions.php';

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
              'mid' => 'number',
              'name' => 'text',
              'description' => 'text',
              'created' => 'number',
              'modified' => 'number',
              'free_marker' => 'number',
              'paid_marker' => 'number',
              'active' => 'number',
              'hidden' => 'number',
              'geo' => 'text',
              'lat' => 'number',
              'lng' => 'number',
              'width' => 'alpha',
              'height' => 'alpha',
              'zoom' => 'number',
              'type' => 'text',
              'header' => 'html',
              'footer' => 'html',
			  'primary_color' => 'text',
			  'stroke_color' => 'text',
			  'label' => 'text',
			  'label_color' => 'number',
			  'map_header' => 'html',
			  'map_footer' => 'html',
			  'mk_default' => 'number',
			  'mk_icon' => 'number',
			  );

MAPS_filterVars($vars, $_REQUEST);

/**
 * This function creates a map Form
 *
 * Creates a Form for a map using the supplied defaults (if specified).
 *
 * @param array $map array of values describing a map
 * @return string HTML string of map form
 */
function getMapForm($map = array()) {

    global $_CONF, $_TABLES, $_MAPS_CONF, $LANG_MAPS_1, $LANG_configselects, $LANG_ACCESS, $_USER, $_GROUPS, $_SCRIPTS;
	
	$_SCRIPTS->setJavaScriptLibrary('jquery');
	
    $js = 'jQuery(function () {
        var tabContainers = jQuery(\'div.tabs > div\');
        
        jQuery(\'div.tabs ul.tabNavigation a\').click(function () {
            tabContainers.hide().filter(this.hash).show();
            
            jQuery(\'div.tabs ul.tabNavigation a\').removeClass(\'selected\');
            jQuery(this).addClass(\'selected\');
            
            return false;
        }).filter(\':first\').click();
		
		jQuery(".delete").click(function() {
			jQuery("#load").show();
			var id = jQuery(this).attr("id");
			var mid = jQuery(this).attr("mid");
			var oid = jQuery(this).attr("oid");
			var action = jQuery(this).attr("class");
			var string = \'id=\'+ id + \'&action=\' + action + \'&mid=\' + mid;
				
			jQuery.ajax({
				type: "POST",
				url: "ajax.php",
				data: string,
				cache: false,
				async:false,
				success: function(result){
					jQuery("#overlays_actions").replaceWith(result);
				}   
			});
			jQuery("#load").hide();
			return false;
		});
		
		jQuery(".add").click(function() {
			jQuery("#load").show();
			var id = jQuery(this).attr("id");
			var mid = jQuery(this).attr("mid");
			var oid = jQuery(this).attr("oid");
			var action = jQuery(this).attr("class");
			var string = \'id=\'+ id + \'&action=\' + action + \'&mid=\' + mid;
				
			jQuery.ajax({
				type: "POST",
				url: "ajax.php",
				data: string,
				cache: false,
				async:false,
				success: function(result){
					jQuery("#overlays_actions").replaceWith(result);
				}   
			});
			jQuery("#load").hide();
			return false;
		});
		
    });' . LB;

	if ($_CONF['advanced_editor'] == true) {
		$js_ad = '// Setup editor path for FCKeditor JS Functions
		geeklogEditorBasePath = "' . $_CONF['site_url'] . '/fckeditor/" ;
		window.onload = function() {
			var map_header = new FCKeditor( \'mapheader\' ) ;
			map_header.Config[\'CustomConfigurationsPath\'] = geeklogEditorBaseUrl + \'/fckeditor/myconfig.js\';
			map_header.BasePath = geeklogEditorBasePath;
			map_header.ToolbarSet = \'editor-toolbar2\';
			map_header.Height = 300 ;
			map_header.ReplaceTextarea() ;
			
			var map_footer = new FCKeditor( \'mapfooter\' ) ;
			map_footer.Config[\'CustomConfigurationsPath\'] = geeklogEditorBaseUrl + \'/fckeditor/myconfig.js\';
			map_footer.BasePath = geeklogEditorBasePath;
			map_footer.ToolbarSet = \'editor-toolbar2\';
			map_footer.Height = 300 ;
			map_footer.ReplaceTextarea() ;
		};';
		$_SCRIPTS->setJavaScript($js_ad, true, true);
	}
	
	$js .= '      jQuery(document).ready(
        function()
        {
            jQuery("#primary_color").simpleColor({
				cellWidth: 9,
				cellHeight: 9,
				border: \'1px solid #333333\',
				displayColorCode: true
		    });
            jQuery("#stroke_color").simpleColor({
				cellWidth: 9,
				cellHeight: 9,
				border: \'1px solid #333333\',
				displayColorCode: true
		    });
			
			jQuery("#load").hide();
		});
	';
	
	$_SCRIPTS->setJavaScript('<script type="text/javascript" src="' . $_CONF['site_url'] . '/fckeditor/fckeditor.js"></script>', false);
	$_SCRIPTS->setJavaScript($js, true);
		
	$_SCRIPTS->setJavaScriptFile('maps_simplecolor', '/' . $_MAPS_CONF['maps_folder'] . '/js/simple-color.js');
	
    $display = COM_startBlock($LANG_MAPS_1['map_edit'] . ' ' . $map['name']);

    $template = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
    $template->set_file(array('map' => 'map_form.thtml'));
	$template->set_var('site_admin_url', $_CONF['site_admin_url']);
	$template->set_var('arrow', '<img src="' . $_CONF['site_url'] . '/maps/images/arrow.png" alt=""align="absmiddle">&nbsp;');
	$template->set_var('map_tab', $LANG_MAPS_1['map_tab']);
	$template->set_var('overlays_tab', $LANG_MAPS_1['overlays_tab']);
	//informations
	$template->set_var('informations', $LANG_MAPS_1['informations']);
    $template->set_var('name_label', $LANG_MAPS_1['name_label']);
    $template->set_var('name', stripslashes($map['name']));
	$template->set_var('address_label', $LANG_MAPS_1['address_label']);
    $template->set_var('geo', $map['geo']);
	$template->set_var('description_label', $LANG_MAPS_1['description_label']);
    $template->set_var('description', stripslashes($map['description']));
	$template->set_var('required_field', $LANG_MAPS_1['required_field']);
	$template->set_var('created_label', $LANG_MAPS_1['map_created']);
	$template->set_var('modified_label', $LANG_MAPS_1['modified']);
	$datecreated = COM_getUserDateTimeFormat($map['created']);
	$datemodified = COM_getUserDateTimeFormat($map['modified']);
	$template->set_var('created', $datecreated[0]);
	$template->set_var('modified', $datemodified[0]);
	
	//Genaral settings
	$template->set_var('general_settings', $LANG_MAPS_1['general_settings']);
	$template->set_var('map_width', $LANG_MAPS_1['map_width']);
	if ($map['width'] == '') {
        $map['width'] = $_MAPS_CONF['map_width'];
    }
	$template->set_var('width', $map['width']);
	
	$template->set_var('map_height', $LANG_MAPS_1['map_height']);
	if ($map['height'] == '') {
        $map['height'] = $_MAPS_CONF['map_height'];
    }
	$template->set_var('height', $map['height']);
	
	$template->set_var('map_zoom', $LANG_MAPS_1['map_zoom']);
	if ($map['zoom'] == '') {
        $map['zoom'] = $_MAPS_CONF['map_zoom'];
    }
	$template->set_var('zoom', $map['zoom']);
	
	$template->set_var('map_type', $LANG_MAPS_1['map_type']);
	$map_type = $LANG_configselects['maps']["20"]; 
	$options = '';
	foreach ($map_type as $i => $value) {
		$options .= '<option value="' . $value . '"';
		if ($value == $map['type']) {
				$options .= ' selected="selected"';
			}
		$options .= '>' . $i . '</option>' . LB;
	}
	$template->set_var('options', $options);
	
	$template->set_var('yes', $LANG_MAPS_1['yes']);
	$template->set_var('no', $LANG_MAPS_1['no']);
	
	$template->set_var('active', $LANG_MAPS_1['active']);
	if ($map['active'] == '') {
        $map['active'] = $_MAPS_CONF['map_active'];
    }
    if ($map['active'] == 1) {
        $template->set_var('active_yes', ' selected');
        $template->set_var('active_no', '');
    } else {
        $template->set_var('active_yes', '');
        $template->set_var('active_no', ' selected');
    }
	
	$template->set_var('hidden', $LANG_MAPS_1['hidden']);
	if ($map['hidden'] == '') {
        $map['hidden'] = $_MAPS_CONF['map_hidden'];
    }
	if ($map['hidden'] == 1) {
        $template->set_var('hidden_yes', ' selected');
        $template->set_var('hidden_no', '');
    } else {
        $template->set_var('hidden_yes', '');
        $template->set_var('hidden_no', ' selected');
    }
	
	$template->set_var('free_marker', $LANG_MAPS_1['free_marker']);
	if ($map['free_marker'] == '') {
        $map['free_marker'] = $_MAPS_CONF['free_markers'];
    }
	if ($map['free_marker'] == 1) {
        $template->set_var('free_marker_yes', ' selected');
        $template->set_var('free_marker_no', '');
    } else {
        $template->set_var('free_marker_yes', '');
        $template->set_var('free_marker_no', ' selected');
    }

	$template->set_var('paid_marker', $LANG_MAPS_1['paid_marker']);
	if ($map['paid_marker'] == '') {
        $map['paid_marker'] = $_MAPS_CONF['paid_markers'];
    }
	if ($map['paid_marker'] == 1) {
        $template->set_var('paid_marker_yes', ' selected');
        $template->set_var('paid_marker_no', '');
    } else {
        $template->set_var('paid_marker_yes', '');
        $template->set_var('paid_marker_no', ' selected');
    }
	
	//marker
	
	$template->set_var('mk_default', $LANG_MAPS_1['mk_default']);
	if ($map['mmk_default'] == '1') {
		$template->set_var('mk_default_yes', 'selected="selected"');
		$template->set_var('mk_default_no', '');
	} else {
		$template->set_var('mk_default_yes', '');
		$template->set_var('mk_default_no', 'selected="selected"');
	}
	//icon
	$sql = "SELECT * FROM {$_TABLES['maps_map_icons']} WHERE 1=1";
	$result = DB_query($sql, 0);
	
	$radio = '<p>' . $LANG_MAPS_1['choose_icon'] . '</p>';
	($map['mk_icon'] == 0) ? $checked = ' checked="checked"' : $checked = '';
	$radio .= '<input type="radio" name="mk_icon" value="0"' . $checked . '>' . $LANG_MAPS_1['no_icon'] . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
	while ($icon = DB_fetchArray($result, false)) {
		($map['mk_icon'] == $icon['icon_id']) ? $checked = ' checked="checked"' : $checked = '';  
		$radio .= '<input type="radio" name="mk_icon" value="' . $icon['icon_id'] . '"' . $checked . '> <img src="' . $_MAPS_CONF['images_icons_url'] . $icon['icon_image'] . '" alt="' . $icon['icon_image'] . '">&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
	}
	$radio .= '<hr'. XHTML .'>';
	$template->set_var('icon', $radio);
	
	$template->set_var('marker_label', $LANG_MAPS_1['marker_label']);
	$template->set_var('primary_color_label', $LANG_MAPS_1['primary_color_label']);
	$template->set_var('primary_color', $map['primary_color']);
	$template->set_var('stroke_color_label', $LANG_MAPS_1['stroke_color_label']);
	$template->set_var('stroke_color', $map['stroke_color']);
	$template->set_var('label_label', $LANG_MAPS_1['label']);
    $template->set_var('label', $map['label']);	
	$template->set_var('label_color_label', $LANG_MAPS_1['label_color']);
	if ($map['label_color'] == '') {
	$map['label_color'] = $_MAPS_CONF['label_color'];
    }
	if ($map['label_color'] == 1) {
        $template->set_var('label_color_white', ' selected');
        $template->set_var('label_color_black', '');
    } else {
        $template->set_var('label_color_white', '');
        $template->set_var('label_color_black', ' selected');
    }
	$template->set_var('black', $LANG_MAPS_1['black']);
	$template->set_var('white', $LANG_MAPS_1['white']);
	
	//header and footer
	$template->set_var('header_footer', $LANG_MAPS_1['header_footer']);
	
	if ($map['header'] == '') $map['header'] = '&nbsp;';
	if ($map['footer'] == '') $map['footer'] = '<p>&nbsp;</p>';
	
	$template->set_var('map_header_label', $LANG_MAPS_1['map_header_label']);
	$template->set_var('map_header', $map['header']);
	$template->set_var('map_footer_label', $LANG_MAPS_1['map_footer_label']);
	$template->set_var('map_footer', $map['footer']);
	
	// Permissions
	if ($map['perm_owner'] == '') {
	  SEC_setDefaultPermissions($map, $_MAPS_CONF['default_permissions']);
	}
	$template->set_var('lang_accessrights', $LANG_ACCESS['accessrights']);
    $template->set_var('lang_owner', $LANG_ACCESS['owner']);
	if ($map['owner_id'] == '') {
	$map['owner_id'] = $_USER['uid'];
	}
    $ownername = COM_getDisplayName($map['owner_id']);
	
	//Select owner
	$result = DB_query("SELECT * FROM {$_TABLES['users']}");
	$nRows  = DB_numRows($result);

	$owner_select = '<select name="owner_id">';
	for ($i=0; $i<$nRows;$i++) {
		$row = DB_fetchArray($result);
		if ( $row['uid'] == 1 ) {
			continue;
		}
		$owner_select .= '<option value="' . $row['uid'] . '"' . ($map['owner_id'] == $row['uid'] ? 'selected="selected"' : '') . '>' . COM_getDisplayName($row['uid']) . ' | ' . $row['uid'] . '</option>';
	}
	$owner_select .= '</select>';

	$template->set_var('owner_select', $owner_select);
	
    $template->set_var('owner_username', DB_getItem($_TABLES['users'],
                          'username',"uid = {$map['owner_id']}"));
    $template->set_var('owner_name', $ownername);
    $template->set_var('owner', $ownername);
    $template->set_var('owner_id', $map['owner_id']);
	if ($map['group_id']  == '') {
        $map['group_id'] = $_GROUPS['Maps Admin'];
    }
    $template->set_var('lang_group', $LANG_ACCESS['group']);
	$access = 3;
    $template->set_var('group_dropdown', SEC_getGroupDropdown($map['group_id'], $access));
    $template->set_var('permissions_editor', SEC_getPermissionsHTML($map['perm_owner'],$map['perm_group'],$map['perm_members'],$map['perm_anon']));
    $template->set_var('lang_permissions', $LANG_ACCESS['permissions']);
    $template->set_var('lang_perm_key', $LANG_ACCESS['permissionskey']);
    $template->set_var('permissions_msg', $LANG_ACCESS['permmsg']);
    $template->set_var('lang_permissions_msg', $LANG_ACCESS['permmsg']);
	
	//Form validation
	$template->set_var('save_button', $LANG_MAPS_1['save_button']);
	$template->set_var('delete_button', $LANG_MAPS_1['delete_button']);
	$template->set_var('ok_button', $LANG_MAPS_1['ok_button']);
    if (is_numeric($map['mid'])) {
        $template->set_var('mid', '<input type="hidden" name="mid" value="' . $map['mid'] .'" />');
    } else {
        $template->set_var('mid', '');
    }
	
	//overlays
	if($map['mid'] != '') {
	    $template->set_var('overlays', MAPS_displayOverlays($map['mid']));
		$template->set_var('add_overlay', MAPS_displayOverlaysToAdd($map['mid']));
	} else {
	    $template->set_var('overlays', '');
	    $template->set_var('add_overlay', '<p>' . $LANG_MAPS_1['add_overlay'] . '</p>');
	}
	
    $display .= $template->parse('output', 'map');

    $display .= COM_endBlock();

    return $display;
}




// MAIN
$display .= COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
$display .= MAPS_admin_menu();

switch ($_REQUEST['mode']) {
    case 'delete':
	    DB_delete($_TABLES['maps_maps'], 'mid', $_REQUEST['mid']);
    if (DB_affectedRows('') == 1) {
        $msg = $LANG_MAPS_1['deletion_succes'];
    } else {
        $msg = $LANG_MAPS_1['deletion_fail'];
    }
		// delete complete, return to map list
        echo COM_refresh($_CONF['site_url'] . "/admin/plugins/maps/index.php?msg=$msg");
        exit();
        break;

    case 'save':
        $coords = true;
		// lat, lng, zoom and height can only contain numbers and a decimal
		$coords = MAPS_getCoords(stripslashes($_REQUEST['geo']), $lat, $lng);
		
		if ( empty($_REQUEST['name']) || empty($_REQUEST['geo'])  || $coords != true ) {
            $display .= COM_startBlock($LANG_MAPS_1['error'],'','blockheader-message.thtml');
            $display .= $LANG_MAPS_1['missing_field'];
			if ($coords != true) $display .= $LANG_MAPS_1['geo_fail'];
            $display .= COM_endBlock('blockfooter-message.thtml');
            $display .= getMapForm($_REQUEST);
            break;
        }
		
        // prepare strings for insertion
		$_REQUEST['created'] = date("YmdHis");
		$_REQUEST['modified'] = date("YmdHis");
		
        
		if (! empty($_REQUEST['zoom']) ) {
            $_REQUEST['zoom'] = preg_replace('/[^\d.]/', '', $_REQUEST['zoom']);
		}
	    // Convert array values to numeric permission values
        if (is_array($_REQUEST['perm_owner']) OR is_array($_REQUEST['perm_group']) OR is_array($_REQUEST['perm_members']) OR is_array($_REQUEST['perm_anon'])) {
            list($_REQUEST['perm_owner'],$_REQUEST['perm_group'],$_REQUEST['perm_members'],$_REQUEST['perm_anon']) = SEC_getPermissionValues($_REQUEST['perm_owner'],$_REQUEST['perm_group'],$_REQUEST['perm_members'],$_REQUEST['perm_anon']);
        }
			 
		if (!empty($_REQUEST['mid'])) { //edit mode
		        $sql = "name = '{$_REQUEST['name']}', "
             . "description = '{$_REQUEST['description']}', "
			 . "modified = '{$_REQUEST['modified']}', " 
             . "free_marker = '{$_REQUEST['free_marker']}', "
             . "paid_marker = '{$_REQUEST['paid_marker']}', "
             . "active = '{$_REQUEST['active']}', "
             . "hidden = '{$_REQUEST['hidden']}', "
			 . "geo = '{$_REQUEST['geo']}', "
			 . "lat = '{$lat}', "
			 . "lng = '{$lng}', "
			 . "width = '{$_REQUEST['width']}', "
			 . "height = '{$_REQUEST['height']}', "
			 . "zoom = '{$_REQUEST['zoom']}', "
			 . "type = '{$_REQUEST['type']}', "
			 . "header = '{$_REQUEST['map_header']}', "
			 . "footer = '{$_REQUEST['map_footer']}', "
			 . "mmk_default = '{$_REQUEST['mk_default']}', "
			 . "mmk_icon = '{$_REQUEST['mk_icon']}', "
			 . "primary_color = '{$_REQUEST['primary_color']}', "
			 . "stroke_color = '{$_REQUEST['stroke_color']}', "
			 . "label = '{$_REQUEST['label']}', "
			 . "label_color = '{$_REQUEST['label_color']}', "
			 . "owner_id = '{$_REQUEST['owner_id']}', "
			 . "group_id = '{$_REQUEST['group_id']}', "
			 . "perm_owner = '{$_REQUEST['perm_owner']}', "
			 . "perm_group = '{$_REQUEST['perm_group']}', "
			 . "perm_members = '{$_REQUEST['perm_members']}', "
			 . "perm_anon = '{$_REQUEST['perm_anon']}'";
            $sql = "UPDATE {$_TABLES['maps_maps']} SET $sql "
                 . "WHERE mid = {$_REQUEST['mid']}";
        } else { // create mode
		      $sql = "name = '{$_REQUEST['name']}', "
             . "description = '{$_REQUEST['description']}', "
             . "created = '{$_REQUEST['created']}', "
			 . "modified = '{$_REQUEST['modified']}', " 
             . "free_marker = '{$_REQUEST['free_marker']}', "
             . "paid_marker = '{$_REQUEST['paid_marker']}', "
             . "active = '{$_REQUEST['active']}', "
             . "hidden = '{$_REQUEST['hidden']}', "
			 . "geo = '{$_REQUEST['geo']}', "
			 . "lat = '{$lat}', "
			 . "lng = '{$lng}', "
			 . "width = '{$_REQUEST['width']}', "
			 . "height = '{$_REQUEST['height']}', "
			 . "zoom = '{$_REQUEST['zoom']}', "
			 . "type = '{$_REQUEST['type']}', "
			 . "header = '{$_REQUEST['map_header']}', "
			 . "footer = '{$_REQUEST['map_footer']}', "
			 . "mmk_default = '{$_REQUEST['mk_default']}', "
			 . "mmk_icon = '{$_REQUEST['mk_icon']}', "
			 . "primary_color = '{$_REQUEST['primary_color']}', "
			 . "stroke_color = '{$_REQUEST['stroke_color']}', "
			 . "label = '{$_REQUEST['label']}', "
			 . "label_color = '{$_REQUEST['label_color']}', "
			 . "owner_id = '{$_REQUEST['owner_id']}', "
			 . "group_id = '{$_REQUEST['group_id']}', "
			 . "perm_owner = '{$_REQUEST['perm_owner']}', "
			 . "perm_group = '{$_REQUEST['perm_group']}', "
			 . "perm_members = '{$_REQUEST['perm_members']}', "
			 . "perm_anon = '{$_REQUEST['perm_anon']}'";
            $sql = "INSERT INTO {$_TABLES['maps_maps']} SET $sql ";
        }
        DB_query($sql);
        if (DB_error()) {
            $msg = $LANG_MAPS_1['save_fail'];
        } else {
            $msg = $LANG_MAPS_1['save_success'];
        }
        // save complete, return to map list
        echo COM_refresh($_CONF['site_admin_url'] . "/plugins/maps/index.php?msg=$msg");
        exit();
        break;

    case 'edit':
        // Get the map to edit and display the form
        if (is_numeric($_REQUEST['mid'])) {
            $sql = "SELECT * FROM {$_TABLES['maps_maps']} WHERE mid = {$_REQUEST['mid']}";
            $res = DB_query($sql);
            $A = DB_fetchArray($res);
			if( is_array($A) ) {
				foreach ($A as $i => $value) {
				  $A[$i] = stripslashes($value);
				}
			}
            $display .= getMapForm($A);
        } else {
            echo COM_refresh($_CONF['site_url']);
        }
        break;

    case 'new':
    default:
        $display .= getMapForm();
        break;
}

$display .= COM_siteFooter(0);


echo $display;

?>