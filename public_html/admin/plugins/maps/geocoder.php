<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.0                                                           |
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
 * Displays a form for the editing maps
 *
 * Allows for the altering and deletion of existing maps as well as the
 * creation of new maps
 *
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

// MAIN
$js = '    <script  type="text/javascript" src= "https://maps.googleapis.com/maps/api/js?key=' . $_MAPS_CONF['google_api_key'] . '&sensor=false"> </script>
    <script type="text/javascript">
    
	var geocoder = new google.maps.Geocoder();
    var map;

    function initializeGMap() {
        
        var mapOptions = {
          center: new google.maps.LatLng(0, 0),
          zoom: 1,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("map_canvas"),
            mapOptions);


    }

	function codeAddress() {
	  var address = document.getElementById(\'address\').value;
	  geocoder.geocode( { 
	      \'address\': address
		  }, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
		  
		  map.setCenter(results[0].geometry.location);
		  
		  var marker = new google.maps.Marker({
			  map: map,
			  position: results[0].geometry.location
		  });
		  
		  var text = \'<div style="width:200px;">Lat: \' + map.getCenter().lat() + \'<br />Lng: \'+ map.getCenter().lng() + \'<br />\'+results[0].formatted_address+\'</div>\';
		  
		  var infowindow = new google.maps.InfoWindow({ content: text });
		  
		  infowindow.open(map,marker);
		  
		} else {
		  alert(\'Geocode was not successful for the following reason: \' + status);
		}
	  });
	}
	
	google.maps.event.addDomListener(window, \'load\', initializeGMap);

    </script>
    ';
$_SCRIPTS->setJavaScript($js, false);

$display .= COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
$display .= MAPS_admin_menu();

$display .= MAPS_geocoding();

$display .= COM_siteFooter(0);


COM_output($display);

?>
