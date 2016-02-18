<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.0                                                           |
// +---------------------------------------------------------------------------+
// | users_map.php                                                                 |
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

function getUsersMap () {

    global $_TABLES, $LANG_MAPS_1, $_MAPS_CONF, $_CONF, $_USER;
		
	// Ensure user has the rights to access this map
	if ($_MAPS_CONF['users_map'] == 0) {
		echo COM_refresh($_MAPS_CONF['site_url'] . '/index.php');
		exit ();
	}
	
	$T = new Template($_CONF['path'] . 'plugins/maps/templates');
	$T->set_file('page', 'map.thtml');
	$T->set_var('mid', '0');
	$T->set_var('name', $LANG_MAPS_1['users_map']);
	$T->set_var('description', '<p>' . $LANG_MAPS_1['info_users_map'] . '</p>');
	$T->set_var('header', '');
	$T->set_var('footer', '');
	
	//Users maps is set by default on 0,0. Todo make it configurable 
	$T->set_var('address', '');
	$T->set_var('lat', 0);
	$T->set_var('lng', 0);
	$T->set_var('zoom', $_MAPS_CONF['global_zoom']);
	$T->set_var('goog_api_key', $_MAPS_CONF['google_api_key']);
	$T->set_var('map_type', $_MAPS_CONF['global_type']);
	$T->set_var('map_width', $_MAPS_CONF['global_width']);
	$T->set_var('map_height', $_MAPS_CONF['global_height']);
	
	$T->set_var('primaryColor', '');
	$T->set_var('stroke_color', '');
	if ($_MAPS_CONF['label_color'] == 1) {
		$label_color = '#FFFFFF';
	} else {
		$label_color = '#000000';
	}
	$T->set_var('label_color', $label_color);
	$T->set_var('label', '');

	$sql = "
	    SELECT info.uid, info.location, info.about, geo.lat, geo.lng, user.username, user.fullname, user.photo, user.regdate 
	    FROM {$_TABLES['userinfo']} AS info 
		INNER JOIN {$_TABLES['maps_geo']} AS geo 
		ON geo.geo = info.location 
		INNER JOIN {$_TABLES['users']} AS user 
		ON user.uid = info.uid 
		WHERE info.location <> ''
	";
	
	$user_marker = DB_query($sql);
	
    //Build markers  
    $nRows  = DB_numRows($user_marker);
	
	$markers = 'var markers = [];';	
	
	if ( $_MAPS_CONF['use_cluster'] == 0 ) {
	    $T->set_var('markerclusterer', "<script src=\"{$_MAPS_CONF['site_url']}/js/markerclusterer.js\" type=\"text/javascript\"></script>");
	} else {
	    $T->set_var('markerclusterer', '');
	}
	
    for ( $i=0; $i < $nRows; $i++ ) {
        
		$marker = DB_fetchArray($user_marker);
		
	    $marker['mkid'] = $marker['uid'];

		//icon
		$markers .= LB . '';
		
		if ( $marker['photo'] != '' && file_exists($_CONF['path_images'] . 'userphotos/' . $marker['photo']) ) {
		
		    $markers .= LB . 'var image' . $marker['mkid'] . ' = {
				url: "' . $_MAPS_CONF['site_url'] . '/timthumb.php?src='
				. $_CONF['site_url'] . '/' . substr($_CONF['path_images'], strlen($_CONF['path_html']), -1) . '/userphotos/' . $marker['photo'] . '&w=30&h=30&q=90",
				size: new google.maps.Size(30,30),
				// The origin for this image
				origin: new google.maps.Point(0,0),
				// The anchor for this image
				anchor: new google.maps.Point(15, 30)
			};';
		} else {
     		$markers .= LB . 'var image' . $marker['mkid'] . ' = {
				url: "' . $_MAPS_CONF['site_url'] . '/images/usermarker.png",
				size: new google.maps.Size(37, 37),
				// The origin for this image
				origin: new google.maps.Point(0,0),
				// The anchor for this image
				anchor: new google.maps.Point(17,37)
			};';
		}
		
		$markers .= LB . '				var marker' . $marker['mkid'] .' = new google.maps.Marker({
		 position: new google.maps.LatLng('. $marker['lat']. ', '. $marker['lng'] .'),
		 map:map0,
		 title: "' .  $marker['username'] . '",
		 animation: google.maps.Animation.DROP,
		 icon: image' . $marker['mkid'] . '
		 });' . LB ;
		 
		//Infowindow link to user profile
		$bio = '';
		if ($marker['about'] != '') $bio = preg_replace( "/\r|\n/", "", nl2br(substr ( $marker['about'] ,0, 150 ))) . '...<br' .  XHTML . '>';
		$presentation = '<div style="overflow:auto;width:250px;height:150px"><p><strong><span style="text-transform:uppercase;">' . $marker['username'] . '</span></strong></p>' . $bio;
		$presentation .= '<a href="'. $_CONF['site_url'] .'/users.php?mode=profile&uid=' . $marker['uid'] . '">' . $LANG_MAPS_1['read_more'] . '</a>';
		$presentation .= '</div>';
		
		$markers .= '				var infowindow' . $marker['mkid'] . ' = new google.maps.InfoWindow({
			  content: \'' . addslashes($presentation) . '\'
		  });' . LB;
		
		// Adding a click-event to the marker  
		$markers .= '				google.maps.event.addListener(marker' . $marker['mkid'] . ', \'click\', function() {
			infowindow' . $marker['mkid'] . '.open(map0,marker' . $marker['mkid'] .');
		  });' . LB;

		// Add marker to map
		if ( $_MAPS_CONF['use_cluster'] == 1 ) {
		    $markers .= '				    markers.push(marker' . $marker['mkid'] .');' . LB;
		}
		
	}
	
	$markers .= LB . '				var markerCluster = new MarkerClusterer(map0, markers);' . LB; 
	
	//Ads	
	$ads = MAPS_getAds (0);
	$T->set_var('ads', $ads);
							
	$T->set_var('markers', $markers);

	$T->set_var('edit_button', '');

	$T->parse('output','page');
	$retval .= $T->finish($T->get_var('output'));
	
	return $retval;
}

// MAIN

$display = '';

$display .= COM_siteHeader('menu', $LANG_MAPS_1['users_map']);
$display .= MAPS_user_menu();

if ($_MAPS_CONF['users_map'] == 1) {
    //Display the Users Map 
	$display .= getUsersMap();
} else {
    echo COM_refresh($_MAPS_CONF['site_url'] . '/index.php');
}

$display .= COM_siteFooter();

echo $display;

?>
