<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.3.3                                                         |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// |                                                                           |
// | Plugin administration page                                                |
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

//echo COM_Debug($_POST);

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
              'mkid' => 'alpha',
              'name' => 'text',
              'address' => 'text',
			  'lat' => 'alpha',
			  'lng' => 'alpha',
			  'mk_default' => 'number', 
			  'primary_color' => 'text',
			  'stroke_color' => 'text',
			  'label' => 'text',
			  'label_color' => 'number',
			  'mid' => 'number',
			  'payed' => 'number',
			  'create' => 'number',
			  'modified' => 'number',
			  'validity' => 'number',
			  'from' => 'number',
			  'to' => 'number',
			  'active' => 'number',
			  'hiddden' => 'number',
			  'remark' => 'text',
			  'description' => 'text',
			  'street' => 'text',
			  'code' => 'alpha',
			  'city' => 'text',
			  'country' => 'text',
			  'state' => 'text',
			  'tel' => 'alpha',
			  'fax' => 'alpha',
			  'web' => 'text',
			  'item_1' => 'text',
			  'item_2' => 'text',
			  'item_3' => 'text',
			  'item_4' => 'text',
			  'item_5' => 'text',
			  'item_6' => 'text',
			  'item_7' => 'text',
			  'item_8' => 'text',
			  'item_9' => 'text',
			  'item_10' => 'text',
              'perm_owner[0]' => 'number',
              'perm_owner[1]' => 'number',
              'perm_group[0]' => 'number',
              'perm_group[1]' => 'number',
              'perm_members[0]' => 'number',
              'perm_anon[0]' => 'number',
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
function getMarkerForm($marker = array()) {

    global $_CONF, $_TABLES, $_MAPS_CONF, $LANG_MAPS_1, $LANG_configselects, $LANG_ACCESS, $_USER, $_GROUPS, $_SCRIPTS;
    
	$display = COM_startBlock('<h1>' . $LANG_MAPS_1['marker_edit'] . ' ' . $marker['name']. '</h1>');
	
	$map_options = MAPS_recurseMaps($marker['mid']);

	if ($map_options == '') {
		$display .= COM_startBlock($LANG_MAPS_1['error'],'','blockheader-message.thtml');
        $display .= $LANG_MAPS_1['maps_empty'];
        $display .= COM_endBlock('blockfooter-message.thtml');

	} else {
		$template = new Template($_CONF['path'] . 'plugins/maps/templates');
		$template->set_file(array('map' => 'marker_form.thtml'));
		$template->set_var('site_url', $_CONF['site_url']);
		$template->set_var('xhtml', XHTML);

	    $template->set_var('goog_api_key', $_MAPS_CONF['google_api_key']);
	    $template->set_var('go', $LANG_MAPS_1['go']);
	    $template->set_var('edit_marker_text', $LANG_MAPS_1['edit_marker_text']);
		if (isset($marker['mkid'])) {
			$template->set_var('default_address', $marker['address']);
		} else {
			$template->set_var('default_address', '1600 Amphitheatre Pky, Mountain View, CA');
		}
		
		$template->set_var('yes', $LANG_MAPS_1['yes']);
		$template->set_var('no', $LANG_MAPS_1['no']);
		$template->set_var('arrow', '<img src="' . $_MAPS_CONF['site_url'] . '/images/arrow.png" alt=""align="absmiddle">&nbsp;');
		
		//informations
		$template->set_var('informations', $LANG_MAPS_1['informations']);
		$template->set_var('name_label', $LANG_MAPS_1['marker_name_label']);
		$template->set_var('name', stripslashes($marker['name']));
		$template->set_var('address_label', $LANG_MAPS_1['address_label']);
		$template->set_var('address', stripslashes($marker['address']));
		$template->set_var('empty_for_geo', $LANG_MAPS_1['empty_for_geo']);
		$template->set_var('lat', $LANG_MAPS_1['lat']);
		$template->set_var('lat_value', $marker['lat']);
		$template->set_var('lng', $LANG_MAPS_1['lng']);
		$template->set_var('lng_value', $marker['lng']);
		$template->set_var('select_marker_map', $LANG_MAPS_1['select_marker_map']);
		$template->set_var('mid_label', $LANG_MAPS_1['name_label']);
		$template->set_var('mid', $marker['mid']);
		$template->set_var('map_options', $map_options);

		$template->set_var('created_label', $LANG_MAPS_1['marker_created']);
		$template->set_var('modified_label', $LANG_MAPS_1['modified']);
		$datecreated = COM_getUserDateTimeFormat($marker['created']);
		$datemodified = COM_getUserDateTimeFormat($marker['modified']);
		$template->set_var('created', $datecreated[0]);
		$template->set_var('modified', $datemodified[0]);
		$template->set_var('required_field', $LANG_MAPS_1['required_field']);
		
		//marker
		$template->set_var('marker_customisation', $LANG_MAPS_1['marker_customisation']);
		$template->set_var('mk_default', $LANG_MAPS_1['mk_default']);
		if ($marker['mk_default'] == 0) {
			$template->set_var('mk_default_yes', '');
			$template->set_var('mk_default_no', ' selected');
		} else {
			$template->set_var('mk_default_yes', ' selected');
			$template->set_var('mk_default_no', '');
		}
		//icon
		$sql = "SELECT * FROM {$_TABLES['maps_map_icons']} WHERE 1=1";
		$result = DB_query($sql, 0);
		
		$radio = '<p>' . $LANG_MAPS_1['choose_icon'] . '</p>';
		($marker['mk_icon'] == 0) ? $checked = ' checked="checked"' : $checked = '';
		$radio .= '<input type="radio" name="mk_icon" value="0"' . $checked . '>' . $LANG_MAPS_1['no_icon'] . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
		while ($icon = DB_fetchArray($result, false)) {
		    ($marker['mk_icon'] == $icon['icon_id']) ? $checked = ' checked="checked"' : $checked = '';  
		    $radio .= '<input type="radio" name="mk_icon" value="' . $icon['icon_id'] . '"' . $checked . '> <img src="' . $_MAPS_CONF['images_icons_url'] . $icon['icon_image'] . '" alt="' . $icon['icon_image'] . '">&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
		}
		$radio .= '<hr'. XHTML .'>';
		$template->set_var('icon', $radio);
		$template->set_var('primary_color_label', $LANG_MAPS_1['primary_color_label']);
		$template->set_var('primary_color', $marker['mk_pcolor']);
		$template->set_var('stroke_color_label', $LANG_MAPS_1['stroke_color_label']);
		$template->set_var('stroke_color', $marker['mk_scolor']);
		$template->set_var('label_label', $LANG_MAPS_1['label']);
		$template->set_var('label', $marker['mk_label']);	
		$template->set_var('label_color_label', $LANG_MAPS_1['label_color']);
		if ($marker['mk_label_color'] == '') {
		$marker['label_color'] = $_MAPS_CONF['label_color'];
		}
		if ($marker['mk_label_color'] == 1) {
			$template->set_var('label_color_white', ' selected');
			$template->set_var('label_color_black', '');
		} else {
			$template->set_var('label_color_white', '');
			$template->set_var('label_color_black', ' selected');
		}
		$template->set_var('black', $LANG_MAPS_1['black']);
		$template->set_var('white', $LANG_MAPS_1['white']);
		
		//Genaral settings
		$template->set_var('general_settings', $LANG_MAPS_1['general_settings']);
		//payed
		$template->set_var('payed', $LANG_MAPS_1['payed']);
		if ($marker['payed'] == '') {
			$marker['payed'] = $_MAPS_CONF['payed'];
		}
		if ($marker['payed'] == 1) {
			$template->set_var('payed_yes', ' selected');
			$template->set_var('payed_no', '');
		} else {
			$template->set_var('payed_yes', '');
			$template->set_var('payed_no', ' selected');
		}
		//active
		$template->set_var('active', $LANG_MAPS_1['marker_active']);
		if ($marker['active'] == '') {
			$marker['active'] = $_MAPS_CONF['map_active'];
		}
		if ($marker['active'] == 1) {
			$template->set_var('active_yes', ' selected');
			$template->set_var('active_no', '');
		} else {
			$template->set_var('active_yes', '');
			$template->set_var('active_no', ' selected');
		}
		
		//hidden
		$template->set_var('hidden', $LANG_MAPS_1['marker_hidden']);
		if ($marker['hidden'] == '') {
			$marker['hidden'] = $_MAPS_CONF['map_hidden'];
		}
		if ($marker['hidden'] == 1) {
			$template->set_var('hidden_yes', ' selected');
			$template->set_var('hidden_no', '');
		} else {
			$template->set_var('hidden_yes', '');
			$template->set_var('hidden_no', ' selected');
		}
		
		//validity
		$template->set_var('validity', $marker['validity']);
		$template->set_var('marker_validity', $LANG_MAPS_1['marker_validity']);
		if ($marker['validity'] == '') {
			$marker['validity'] = $_MAPS_CONF['map_hidden'];
		}
		if ($marker['validity'] == 1) {
			$template->set_var('validity_yes', ' selected');
			$template->set_var('validity_no', '');
			$template->set_var('disabled', '');
		} else {
			$template->set_var('validity_yes', '');
			$template->set_var('validity_no', ' selected');
			$template->set_var('disabled', ' disabled');
		}
		
		$template->set_var('from_label', $LANG_MAPS_1['from']);
		if ($marker['validity_start'] != '') {
			$datefrom = date("m/d/Y", strtotime($marker['validity_start']));
			$template->set_var('from', $datefrom);
		} else {
		$datefrom = date("m/d/Y");
		$template->set_var('from', $datefrom);
		}
		
		$template->set_var('to_label', $LANG_MAPS_1['to']);
		if ($marker['validity_end'] != '') {
			$dateto = date("m/d/Y", strtotime($marker['validity_end']));
			$template->set_var('to', $dateto);
		} else {
		$dateto = date("m/d/Y");
		$template->set_var('to', $dateto);
		}
		
		//note
		$template->set_var('remark_label', $LANG_MAPS_1['remark']);
		$template->set_var('remark', stripslashes($marker['remark']));

		//Tab presentation
		$template->set_var('presentation_tab', $LANG_MAPS_1['presentation_tab']);
		$template->set_var('description_label', $LANG_MAPS_1['description_label']);
		$template->set_var('description', stripslashes($marker['description']));
		
	    $template->set_var('street_label', $LANG_MAPS_1['street_label']);
		if ($_MAPS_CONF['street'] == 1) {
		  $template->set_var('street', '<input type="text" name="street" value="' . stripslashes($marker['street']) . '" size="80" maxlength="255">');
		} else {
		  $template->set_var('street', $LANG_MAPS_1['not_use_see_config']);
		}
		
		$template->set_var('code_label', $LANG_MAPS_1['code_label']);
		if ($_MAPS_CONF['code'] == 1) {
		  $template->set_var('code', '<input type="text" name="code" value="' . $marker['code'] . '" size="10" maxlength="10">');
		} else {
		  $template->set_var('code', $LANG_MAPS_1['not_use_see_config']);
		}
		
		$template->set_var('city_label', $LANG_MAPS_1['city_label']);
		if ($_MAPS_CONF['city'] == 1) {
		  $template->set_var('city', '<input type="text" name="city" value="' . stripslashes($marker['city']) . '" size="80" maxlength="255">');
		} else {
		  $template->set_var('city', $LANG_MAPS_1['not_use_see_config']);
		}
		
		$template->set_var('state_label', $LANG_MAPS_1['state_label']);
		if ($_MAPS_CONF['state'] == 1) {
		  $template->set_var('state', '<input type="text" name="state" value="' . stripslashes($marker['state']) . '" size="80" maxlength="255">');
		} else {
		  $template->set_var('state', $LANG_MAPS_1['not_use_see_config']);
		}
		
		$template->set_var('country_label', $LANG_MAPS_1['country_label']);
		if ($_MAPS_CONF['country'] == 1) {
		  $template->set_var('country', '<input type="text" name="country" value="' . stripslashes($marker['country']) . '" size="80" maxlength="255">');
		} else {
		  $template->set_var('country', $LANG_MAPS_1['not_use_see_config']);
		}
		
		$template->set_var('tel_label', $LANG_MAPS_1['tel_label']);
		if ($_MAPS_CONF['tel'] == 1) {
		  $template->set_var('tel', '<input type="text" name="tel" value="' . $marker['tel'] . '" size="20" maxlength="20">');
		} else {
		  $template->set_var('tel', $LANG_MAPS_1['not_use_see_config']);
		}
		
		$template->set_var('fax_label', $LANG_MAPS_1['fax_label']);
		if ($_MAPS_CONF['fax'] == 1) {
		  $template->set_var('fax', '<input type="text" name="fax" value="' . $marker['fax'] . '" size="20" maxlength="20">');
		} else {
		  $template->set_var('fax', $LANG_MAPS_1['not_use_see_config']);
		}
		
		$template->set_var('web_label', $LANG_MAPS_1['web_label']);
		if ($_MAPS_CONF['web'] == 1) {
		  $template->set_var('web', '<input type="text" name="web" value="' . stripslashes($marker['web']) . '" size="80" maxlength="255">');
		} else {
		  $template->set_var('web', $LANG_MAPS_1['not_use_see_config']);
		}

		//Tab ressources
		$template->set_var('ressources_tab', $LANG_MAPS_1['ressources_tab']);
		$template->set_var('max_char', $LANG_MAPS_1['max_char']);
		
		$arr = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
		$ressources ='';
		foreach ($arr as &$value) {
			if ($_MAPS_CONF['item_'. $value] == '') {
				$template->set_var('item_'. $value . '_label', '');
				$template->set_var('item_'. $value, '');
				$ressources .= '';
			} else {
				$template->set_var('item_'. $value . '_label', $_MAPS_CONF['item_'. $value]);
				$template->set_var('item_'. $value, $marker['item_'. $value]);
				$ressources .= '<p>' . $_MAPS_CONF['item_'. $value] . ' <input type"text" name="item_' . $value . '" size="80" maxlength="255" value="' . $marker['item_'. $value] . '"></p>';
			}
		}
		if ($ressources == '') {
			$ressources = $LANG_MAPS_1['empty_ressources'];
		}
		$template->set_var('ressources', $ressources);
		
		// Permissions
		if ($marker['perm_owner'] == '') {
		  SEC_setDefaultPermissions($marker, $_MAPS_CONF['default_permissions']);
		}
		$template->set_var('lang_accessrights', $LANG_ACCESS['accessrights']);
		$template->set_var('lang_owner', $LANG_ACCESS['owner']);
		if ($marker['owner_id'] == '') {
		$marker['owner_id'] = $_USER['uid'];
		}
		$ownername = COM_getDisplayName($marker['owner_id']);
		
		//Select owner
		$result = DB_query("SELECT * FROM {$_TABLES['users']}");
		$nRows  = DB_numRows($result);

		$owner_select = '<select name="owner_id">';
		for ($i=0; $i<$nRows;$i++) {
			$row = DB_fetchArray($result);
			if ( $row['uid'] == 1 ) {
				continue;
			}
			$owner_select .= '<option value="' . $row['uid'] . '"' . ($marker['owner_id'] == $row['uid'] ? 'selected="selected"' : '') . '>' . COM_getDisplayName($row['uid']) . ' | ' . $row['uid'] . '</option>';
		}
		$owner_select .= '</select>';

		$template->set_var('owner_select', $owner_select);
		
		$template->set_var('owner_username', DB_getItem($_TABLES['users'],
							  'username',"uid = {$marker['owner_id']}"));
		$template->set_var('owner_name', $ownername);
		$template->set_var('owner', $ownername);
		$template->set_var('owner_id', $marker['owner_id']);
		if ($marker['group_id']  == '') {
			$marker['group_id'] = $_GROUPS['Maps Admin'];
		}
		$template->set_var('lang_group', $LANG_ACCESS['group']);
        //Todo make group = maps.admin
		$access = 3;
		$template->set_var('group_dropdown', SEC_getGroupDropdown($marker['group_id'], $access));
		$template->set_var('permissions_editor', SEC_getPermissionsHTML($marker['perm_owner'],$marker['perm_group'],$marker['perm_members'],$marker['perm_anon']));
		$template->set_var('lang_permissions', $LANG_ACCESS['permissions']);
		$template->set_var('lang_perm_key', $LANG_ACCESS['permissionskey']);
		$template->set_var('permissions_msg', $LANG_ACCESS['permmsg']);
		$template->set_var('lang_permissions_msg', $LANG_ACCESS['permmsg']);
		
		//Form validation
		$template->set_var('submission', $marker['submission']);
		$template->set_var('save_button', $LANG_MAPS_1['save_button']);
		$template->set_var('delete_button', $LANG_MAPS_1['delete_button']);
		$template->set_var('ok_button', $LANG_MAPS_1['ok_button']);
		if (isset($marker['mkid'])) {
			$template->set_var('mkid', '<input type="hidden" name="mkid" value="' . $marker['mkid'] .'" />');
		} else {
			$template->set_var('mkid', '');
		}
		
		$display .= $template->parse('output', 'map');
	}

    $display .= COM_endBlock();
	
	$_SCRIPTS->setJavaScriptLibrary('jquery');
	$_SCRIPTS->setJavaScriptFile('maps_simplecolor', '/' . $_MAPS_CONF['maps_folder'] . '/js/simple-color.js');
	$js = LB . '<script  type="text/javascript" src= "https://maps.googleapis.com/maps/api/js?key=' . $_MAPS_CONF['google_api_key'] . '&sensor=false"> </script>
    <script type="text/javascript">
	jQuery(document).ready(
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
			$( "#from" ).datepicker();
		    $( "#to" ).datepicker();
        });
		
		function changeValidity()
		{
		  if (document.getElementById(\'validity\').value == 0){
			 $("#from").prop(\'disabled\', true);
			 $("#to").prop(\'disabled\', true);

		  }
		  else {
			$("#from").prop(\'disabled\', false);
			$("#to").prop(\'disabled\', false);
		  }
		}

		jQuery(function() {
			jQuery(\'#from\').datepicker({
				altFormat:\'m/d/Y\'
			});
			jQuery(\'#to\').datepicker({
				altFormat:\'m/d/Y\',
			});
		});
		
		
		var geocoder = new google.maps.Geocoder();
		var map;

		function initializeGMap() {
			
			var mapOptions = {
			  center: new google.maps.LatLng(' . $marker['lat'] . ', ' . $marker['lng'] . '),
			  zoom: 10,
			  mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			
			map = new google.maps.Map(document.getElementById("map_canvas"),
				mapOptions);
				
			var marker = new google.maps.Marker({
			  map: map,
			  position: new google.maps.LatLng('. $marker['lat']. ', '. $marker['lng'] .'),
			  title: "' .  $marker['name'] . '",
			  draggable:true,
              animation: google.maps.Animation.DROP,
			});
			
			google.maps.event.addDomListener(marker, "dragend", function(evt) {
				document.getElementById(\'lat\').value = evt.latLng.lat().toFixed(6);
				document.getElementById(\'lng\').value = evt.latLng.lng().toFixed(6);
				//showInfoWindowHtml(marker);
			});
			
		}
		
		google.maps.event.addDomListener(window, \'load\', initializeGMap);
		
		function showInfoWindowHtml (marker) {
		  var latlng= marker.getLatLng();
		  var lat=latlng.lat();
		  var lng=latlng.lng();
		  //marker.openInfoWindowHtml(\'<p>{lat} \' + lat.toString() + \'</p><p>{lng} \' + lng.toString());
		  document.getElementById(\'lat\').value = lat;
		  document.getElementById(\'lng\').value = lng;
		}

		function codeAddress() {
		  var address = document.getElementById(\'geoaddress\').value;
		  geocoder.geocode( { \'address\': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
			  map.setCenter(results[0].geometry.location);
			  var marker = new google.maps.Marker({
				  map: map,
				  position: results[0].geometry.location
			  });
			  document.getElementById(\'lat\').value = results[0].geometry.location.lat(); 
              document.getElementById(\'lng\').value = results[0].geometry.location.lng(); 
			} else {
			  alert(\'Geocode was not successful for the following reason: \' + status);
			}
		  });
		}

		function limitText(limitField, limitCount, limitNum)
		{
			if (limitField.value.length > limitNum) {
				limitField.value = limitField.value.substring(0, limitNum);
			} else {
				limitCount.value = limitNum - limitField.value.length;
			}
		}

		function copyText()
		{
			var t1 = document.getElementById(\'geoaddress\').value;
			document.getElementById(\'address\').value = t1;
		}
		</script>' . LB. LB;
		
	$_SCRIPTS->setJavaScript($js, false);
	$_SCRIPTS->setJavaScriptFile('ui_core', '/javascript/jquery_ui/jquery.ui.core.min.js');
	$_SCRIPTS->setJavaScriptFile('datepicker', '/javascript/jquery_ui/jquery.ui.datepicker.min.js');

    return $display;
}

// MAIN
$mkid = $_REQUEST['mkid'];
$display .= COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
$display .= maps_admin_menu();

switch ($_REQUEST['mode']) {
    case 'delete':
	    if ($_REQUEST['submission'] <> 1) {
			DB_delete($_TABLES['maps_markers'], 'mkid', $mkid);
			if (DB_affectedRows('') == 1) {
				$msg = $LANG_MAPS_1['deletion_succes'];
			} else {
				$msg = $LANG_MAPS_1['deletion_fail'];
			}
			// delete complete, return to map list
			echo COM_refresh($_CONF['site_url'] . "/admin/plugins/maps/markers.php?msg=$msg");
		} else {
		    echo COM_refresh($_CONF['site_url'] . "/admin/moderation.php");
		}
        exit();
        break;

    case 'save':
        if (empty($_REQUEST['name']) || empty($_REQUEST['address'])) {
            $display .= COM_startBlock($LANG_MAPS_1['error'],'','blockheader-message.thtml');
            $display .= $LANG_MAPS_1['missing_field'];
            $display .= COM_endBlock('blockfooter-message.thtml');
            $display .= getMarkerForm($_REQUEST);
            break;
        }
		if ($_POST['from'] > $_POST['to']) {
            $display .= COM_startBlock($LANG_MAPS_1['error'],'','blockheader-message.thtml');
            $display .= $LANG_MAPS_1['date_issue'];
            $display .= COM_endBlock('blockfooter-message.thtml');
			$_REQUEST['validity_start'] = $_POST['from'];
			$_REQUEST['validity_end'] = $_POST['to'];
            $display .= getMarkerForm($_REQUEST);
            break;
        }
		
		// prepare strings for insertion
		$_REQUEST['created'] = date("YmdHis");
		$_REQUEST['modified'] = date("YmdHis");

		($_REQUEST['from'] == '') ? $_REQUEST['from'] = date("Ymd") : $_REQUEST['from'] = date("Ymd", strtotime($_POST['from']));
		($_REQUEST['to'] == '') ? $_REQUEST['to'] = date("Ymd") : $_REQUEST['to'] = date("Ymd", strtotime($_POST['to']));
		
        // lat, lng can only contain numbers and a decimal
		if (empty($_REQUEST['lat']) || empty($_REQUEST['lng'])) {
		    $address = $_REQUEST['address'];
		    $coords = MAPS_getCoords($address, $lat, $lng);
			if ($lat == 0 && $lng == 0) {
			    $display .= getMarkerForm($_REQUEST);
				$display .= COM_siteFooter();
				COM_output($display);
				exit();
			}
			
		} else {
		    $lat = strval ($_REQUEST['lat']);
			$lng = strval ($_REQUEST['lng']);
		}
		
	    // Convert array values to numeric permission values
        if (is_array($_REQUEST['perm_owner']) OR is_array($_REQUEST['perm_group']) OR is_array($_REQUEST['perm_members']) OR is_array($_REQUEST['perm_anon'])) {
            list($_REQUEST['perm_owner'],$_REQUEST['perm_group'],$_REQUEST['perm_members'],$_REQUEST['perm_anon']) = SEC_getPermissionValues($_REQUEST['perm_owner'],$_REQUEST['perm_group'],$_REQUEST['perm_members'],$_REQUEST['perm_anon']);
        }
		
		// addslashes
		$_REQUEST['name'] = addslashes($_REQUEST['name']);
		$_REQUEST['description'] = addslashes($_REQUEST['description']);
        $_REQUEST['address'] = addslashes($_REQUEST['address']);
		$_REQUEST['remark'] = addslashes($_REQUEST['remark']);
		$_REQUEST['street'] = addslashes($_REQUEST['street']);
		$_REQUEST['city'] = addslashes($_REQUEST['city']);
		$_REQUEST['state'] = addslashes($_REQUEST['state']);
		$_REQUEST['country'] = addslashes($_REQUEST['country']);
		$_REQUEST['tel'] = addslashes($_REQUEST['tel']);
		$_REQUEST['fax'] = addslashes($_REQUEST['fax']);
		$_REQUEST['web'] = addslashes($_REQUEST['web']);
		$_REQUEST['item_1'] = addslashes($_REQUEST['item_1']);
		$_REQUEST['item_2'] = addslashes($_REQUEST['item_2']);
		$_REQUEST['item_3'] = addslashes($_REQUEST['item_3']);
		$_REQUEST['item_4'] = addslashes($_REQUEST['item_4']);
		$_REQUEST['item_5'] = addslashes($_REQUEST['item_5']);
		$_REQUEST['item_6'] = addslashes($_REQUEST['item_6']);
		$_REQUEST['item_7'] = addslashes($_REQUEST['item_7']);
		$_REQUEST['item_8'] = addslashes($_REQUEST['item_8']);
		$_REQUEST['item_9'] = addslashes($_REQUEST['item_9']);
		$_REQUEST['item_10'] = addslashes($_REQUEST['item_10']);
     
		if (!empty($_REQUEST['mkid']) && ($_REQUEST['submission'] <> 1)) { //edit mode
		        $sql = "name = '{$_REQUEST['name']}', "
             . "description = '{$_REQUEST['description']}', "
			 . "modified = '{$_REQUEST['modified']}', "
			 . "payed = '{$_REQUEST['payed']}', "
             . "validity = '{$_REQUEST['validity']}', "
			 . "validity_start = '{$_REQUEST['from']}', "
			 . "validity_end = '{$_REQUEST['to']}', "
             . "active = '{$_REQUEST['active']}', "
             . "hidden = '{$_REQUEST['hidden']}', "
			 . "address = '{$_REQUEST['address']}', "
			 . "lat = '{$lat}', "
			 . "lng = '{$lng}', "
			 . "mk_default = '{$_REQUEST['mk_default']}', "
			 . "mk_pcolor = '{$_REQUEST['primary_color']}', "
			 . "mk_scolor = '{$_REQUEST['stroke_color']}', "
			 . "mk_label = '{$_REQUEST['label']}', "
			 . "mk_label_color = '{$_REQUEST['label_color']}', "
			 . "mk_icon = '{$_REQUEST['mk_icon']}', "
			 . "mid = '{$_REQUEST['mid']}', "
			 . "remark = '{$_REQUEST['remark']}', "
			 . "street = '{$_REQUEST['street']}', "
			 . "city = '{$_REQUEST['city']}', "
			 . "code = '{$_REQUEST['code']}', "
			 . "state = '{$_REQUEST['state']}', "
			 . "country = '{$_REQUEST['country']}', "
			 . "tel = '{$_REQUEST['tel']}', "
			 . "fax = '{$_REQUEST['fax']}', "
			 . "web = '{$_REQUEST['web']}', "
			 . "item_1 = '{$_REQUEST['item_1']}', "
			 . "item_2 = '{$_REQUEST['item_2']}', "
			 . "item_3 = '{$_REQUEST['item_3']}', "
			 . "item_4 = '{$_REQUEST['item_4']}', "
			 . "item_5 = '{$_REQUEST['item_5']}', "
			 . "item_6 = '{$_REQUEST['item_6']}', "
			 . "item_7 = '{$_REQUEST['item_7']}', "
			 . "item_8 = '{$_REQUEST['item_8']}', "
			 . "item_9 = '{$_REQUEST['item_9']}', "
			 . "item_10 = '{$_REQUEST['item_10']}', "
			 . "owner_id = '{$_REQUEST['owner_id']}', "
			 . "group_id = '{$_REQUEST['group_id']}', "
			 . "perm_owner = '{$_REQUEST['perm_owner']}', "
			 . "perm_group = '{$_REQUEST['perm_group']}', "
			 . "perm_members = '{$_REQUEST['perm_members']}', "
			 . "perm_anon = '{$_REQUEST['perm_anon']}', "
			 . "submission = '0'";
            $sql = "UPDATE {$_TABLES['maps_markers']} SET $sql "
                 . "WHERE mkid = {$mkid}";
        } else { // create mode
		    if ($_REQUEST['submission'] <> 1) {
		        $newmkid = addslashes (COM_makeSid ());
			} else {
			    $newmkid = $mkid;
			}
		      $sql = "mkid = '{$newmkid}', "
			 . "name = '{$_REQUEST['name']}', "
             . "description = '{$_REQUEST['description']}', "
             . "created = '{$_REQUEST['created']}', "
			 . "modified = '{$_REQUEST['modified']}', "
			 . "payed = '{$_REQUEST['payed']}', "
             . "validity = '{$_REQUEST['validity']}', "	
			 . "validity_start = '{$_REQUEST['from']}', "
			 . "validity_end = '{$_REQUEST['to']}', "			 
             . "active = '{$_REQUEST['active']}', "
             . "hidden = '{$_REQUEST['hidden']}', "
			 . "address = '{$_REQUEST['address']}', "
			 . "lat = '{$lat}', "
			 . "lng = '{$lng}', "
			 . "mk_default = '{$_REQUEST['mk_default']}', "
			 . "mk_pcolor = '{$_REQUEST['primary_color']}', "
			 . "mk_scolor = '{$_REQUEST['stroke_color']}', "
			 . "mk_label = '{$_REQUEST['label']}', "
			 . "mk_label_color = '{$_REQUEST['label_color']}', "
			 . "mk_icon = '{$_REQUEST['mk_icon']}', "
			 . "mid = '{$_REQUEST['mid']}', "
			 . "remark = '{$_REQUEST['remark']}', "
			 . "street = '{$_REQUEST['street']}', "
			 . "city = '{$_REQUEST['city']}', "
			 . "code = '{$_REQUEST['code']}', "
			 . "state = '{$_REQUEST['state']}', "
			 . "country = '{$_REQUEST['country']}', "
			 . "tel = '{$_REQUEST['tel']}', "
			 . "fax = '{$_REQUEST['fax']}', "
			 . "web = '{$_REQUEST['web']}', "
			 . "item_1 = '{$_REQUEST['item_1']}', "
			 . "item_2 = '{$_REQUEST['item_2']}', "
			 . "item_3 = '{$_REQUEST['item_3']}', "
			 . "item_4 = '{$_REQUEST['item_4']}', "
			 . "item_5 = '{$_REQUEST['item_5']}', "
			 . "item_6 = '{$_REQUEST['item_6']}', "
			 . "item_7 = '{$_REQUEST['item_7']}', "
			 . "item_8 = '{$_REQUEST['item_8']}', "
			 . "item_9 = '{$_REQUEST['item_9']}', "
			 . "item_10 = '{$_REQUEST['item_10']}', "
			 . "owner_id = '{$_REQUEST['owner_id']}', "
			 . "group_id = '{$_REQUEST['group_id']}', "
			 . "perm_owner = '{$_REQUEST['perm_owner']}', "
			 . "perm_group = '{$_REQUEST['perm_group']}', "
			 . "perm_members = '{$_REQUEST['perm_members']}', "
			 . "perm_anon = '{$_REQUEST['perm_anon']}', "
			 . "submission = 0";
            $sql = "INSERT INTO {$_TABLES['maps_markers']} SET $sql ";
        }
        DB_query($sql);
		updateMap($_REQUEST['mid']);
		if ($_REQUEST['submission'] == 0 ) {
		    DB_delete($_TABLES['maps_submission'], 'mkid', $mkid);
		}
        if (DB_error()) {
            $msg = $LANG_MAPS_1['save_fail'];
        } else {
            $msg = $LANG_MAPS_1['save_success'];
			//Delete marker submission
			DB_delete($_TABLES['maps_submission'], 'mkid', $mkid);
        }
        // save complete, return to markers list
        echo COM_refresh($_CONF['site_admin_url'] . "/plugins/maps/markers.php?msg=" . urlencode($msg));
        exit();
        break;

    /* this case is currently not used... future expansion? */
    case 'preview':
        $display = getMapForm($_REQUEST);
        break;

    case 'edit':
        // Get the marker to edit and display the form
        if (isset($_GET['mkid'])) {
            $sql = "SELECT * FROM {$_TABLES['maps_markers']} WHERE mkid = {$_REQUEST['mkid']} LIMIT 1";
            $res = DB_query($sql, 0);
            $A = DB_fetchArray($res);
            $display .= getMarkerForm($A);
        } else {
            echo COM_refresh($_CONF['site_url']);
        }
        break;
	
	case 'editsubmission':
        // Get the marker to edit and display the form
        if (isset($_GET['mkid'])) {
            $sql = "SELECT * FROM {$_TABLES['maps_submission']} WHERE mkid = {$_GET['mkid']} LIMIT 1";
            $res = DB_query($sql);
			
			$nRows  = DB_numRows($res);
		    if ($nRows < 1) {
			    //echo COM_refresh($_MAPS_CONF['site_url'] . '/index.php');
			    //exit ();
		    }
			
            $A = DB_fetchArray($res);
            $display .= getMarkerForm($A);
        } else {
            echo COM_refresh($_CONF['site_url']);
        }
        break;

    case 'new':
    default:
	    $marker['mk_default'] = 1;
		$marker['lat'] = '37.4217913';
		$marker['lng'] = '-122.08371390000002';
        $display .= getMarkerForm($marker);
        break;
}

$display .= COM_siteFooter(0);

COM_output($display);

?>