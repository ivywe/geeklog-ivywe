<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | maps plugin 1.4                                                           |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// |                                                                           |
// | Initial Installation Defaults used when loading the online configuration  |
// | records. These settings are only used during the initial installation     |
// | and not referenced any more once the plugin is installed.                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben                                                            |
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
//

if (strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

/*
 * maps default settings
 *
 * Initial Installation Defaults used when loading the online configuration
 * records. These settings are only used during the initial installation
 * and not referenced any more once the plugin is installed
 *
 */
 
/**
*   Default values to be used during plugin installation/upgrade
*   @global array $_MAPS_DEFAULT
*/
global $_DB_table_prefix, $_MAPS_DEFAULT, $LANG_MAPS_1;
$_MAPS_DEFAULT = array();

$_MAPS_DEFAULT['pi_name']    = 'maps'; // Plugin name

/**
*   Main settings
*/
$_MAPS_DEFAULT['maps_folder']    = 'maps'; //Allow to move the directory where the users's Maps program is store
$_MAPS_DEFAULT['maps_login_required'] = 0;

// Set to 1 to hide the "Maps" entry from the top menu:
$_MAPS_DEFAULT['hide_maps_menu'] = 0;
$_MAPS_DEFAULT['monetize'] = 0;
$_MAPS_DEFAULT['marker_submission'] = 1;
$_MAPS_DEFAULT['marker_edition'] = 1;
$_MAPS_DEFAULT['submit_login_required'] = 1;

//Google Ads
$_MAPS_DEFAULT['AdsOnMap'] = 0;
$_MAPS_DEFAULT['publisher_id'] = 'ca-pub-3822023659914251';
$_MAPS_DEFAULT['channel_id'] = '5373779113';
$_MAPS_DEFAULT['maxAdsOnMap'] = '1';

//Google maps API
$_MAPS_DEFAULT['google_api_key'] = '';   // User must supply their own
$_MAPS_DEFAULT['url_MAPScode']  = 'http://maps.googleapis.com/maps/api/geocode/json?address=%address%&sensor=false';

// Set the default permissions
$_MAPS_DEFAULT['default_permissions'] =  array (3, 3, 2, 2);


/**
*   Display Settings
*/

//Global Map
$_MAPS_DEFAULT['users_map'] = 1;
$_MAPS_DEFAULT['global_map'] = 1;
$_MAPS_DEFAULT['global_type'] = 'ROADMAP';
$_MAPS_DEFAULT['global_zoom'] = '2';
$_MAPS_DEFAULT['global_width'] = '100%';
$_MAPS_DEFAULT['global_height'] = '600px';

//Profile
$_MAPS_DEFAULT['display_geo_profile'] = 1;
$_MAPS_DEFAULT['map_type_profile'] = 'ROADMAP';
$_MAPS_DEFAULT['map_zoom_profile'] = '10';
$_MAPS_DEFAULT['map_width_profile'] = '100%';
$_MAPS_DEFAULT['map_height_profile'] = '400px';
$_MAPS_DEFAULT['show_directions_profile'] = 0;
$_MAPS_DEFAULT['zoom_profile'] = 10;

//geo autotag
$_MAPS_DEFAULT['map_type_geotag'] = 'ROADMAP';
$_MAPS_DEFAULT['map_zoom_geotag'] = '10';
$_MAPS_DEFAULT['map_width_geotag'] = '100%';
$_MAPS_DEFAULT['map_height_geotag'] = '400px';
$_MAPS_DEFAULT['show_directions_geo'] = 0;

//Map default settings
$_MAPS_DEFAULT['map_type'] = 'ROADMAP';
$_MAPS_DEFAULT['map_zoom'] = '6';
$_MAPS_DEFAULT['map_width'] = '100%';
$_MAPS_DEFAULT['map_height'] = '600px';
$_MAPS_DEFAULT['map_active'] = 1;
$_MAPS_DEFAULT['map_hidden'] = 0;
$_MAPS_DEFAULT['free_markers'] = 1;
$_MAPS_DEFAULT['paid_markers'] = 1;

//Marker default settings
$_MAPS_DEFAULT['marker_active'] = 1;
$_MAPS_DEFAULT['marker_hidden'] = 0;
$_MAPS_DEFAULT['marker_payed'] = 0;
$_MAPS_DEFAULT['marker_validity'] = 0;
$_MAPS_DEFAULT['use_cluster'] = 1;

$_MAPS_DEFAULT['star_primary_color'] = '#FFFF00';
$_MAPS_DEFAULT['star_stroke_color'] = '#333333';
$_MAPS_DEFAULT['label_color'] = 0;

$_MAPS_DEFAULT['detail_zoom'] = '8';

$_MAPS_DEFAULT['street'] = 1;
$_MAPS_DEFAULT['code'] = 1;
$_MAPS_DEFAULT['city'] = 1;
$_MAPS_DEFAULT['state'] = 1;
$_MAPS_DEFAULT['country'] = 1;
$_MAPS_DEFAULT['tel'] = 1;
$_MAPS_DEFAULT['fax'] = 1;
$_MAPS_DEFAULT['web'] = 1;

$_MAPS_DEFAULT['item_1'] = 'Ressource #1';
$_MAPS_DEFAULT['item_2'] = 'Ressource #2';
$_MAPS_DEFAULT['item_3'] = 'Ressource #3';
$_MAPS_DEFAULT['item_4'] = 'Ressource #4';
$_MAPS_DEFAULT['item_5'] = 'Ressource #5';
$_MAPS_DEFAULT['item_6'] = 'Ressource #6';
$_MAPS_DEFAULT['item_7'] = 'Ressource #7';
$_MAPS_DEFAULT['item_8'] = 'Ressource #8';
$_MAPS_DEFAULT['item_9'] = 'Ressource #9';
$_MAPS_DEFAULT['item_10'] = 'Ressource #10';

$_MAPS_DEFAULT['infos_label'] = 'Infos';

/**
* Initialize maps plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. 
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_maps()
{
    global $_CONF, $_MAPS_DEFAULT;
	
    $c = config::get_instance();
    if (!$c->group_exists('maps')) {

        //This is main subgroup #0
		$c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, $_MAPS_DEFAULT['pi_name']);
		
		//Main settings   
		$c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true, $_MAPS_DEFAULT['pi_name']);
        $c->add('maps_folder', $_MAPS_DEFAULT['maps_folder'],
                'text', 0, 0, 0, 1, true, 'maps');
		$c->add('maps_login_required', $_MAPS_DEFAULT['maps_login_required'],
                'select', 0, 0, 3, 2, true, 'maps');
		$c->add('hide_maps_menu', $_MAPS_DEFAULT['hide_maps_menu'],
                'select', 0, 0, 3, 3, true, 'maps');
		$c->add('marker_submission', $_MAPS_DEFAULT['marker_submission'],
                'select', 0, 0, 3, 4, true, 'maps');
		$c->add('submit_login_required', $_MAPS_DEFAULT['submit_login_required'],
                'select', 0, 0, 3, 5, true, 'maps');
		$c->add('marker_edition', $_MAPS_DEFAULT['marker_edition'],
                'select', 0, 0, 3, 6, true, 'maps');
		$c->add('monetize', $_MAPS_DEFAULT['monetize'],
                'select', 0, 0, 3, 7, true, 'maps');
				
        // Google  Ads   
		$c->add('fs_ads', NULL, 'fieldset', 0, 2, NULL, 0, true, $_MAPS_DEFAULT['pi_name']);
        $c->add('AdsOnMap', $_MAPS_DEFAULT['AdsOnMap'], 
                'select', 0, 2, 3, 150, true, $_MAPS_DEFAULT['pi_name']);
        $c->add('publisher_id', $_MAPS_DEFAULT['publisher_id'], 
                'text', 0, 2, 0, 160, true, $_MAPS_DEFAULT['pi_name']);
        $c->add('channel_id', $_MAPS_DEFAULT['channel_id'], 
                'text', 0, 2, 0, 170, true, $_MAPS_DEFAULT['pi_name']);
        $c->add('maxAdsOnMap', $_MAPS_DEFAULT['maxAdsOnMap'], 
                'text', 0, 2, 0, 180, true, $_MAPS_DEFAULT['pi_name']);
				
		// Google  maps API   
		$c->add('fs_google', NULL, 'fieldset', 0, 3, NULL, 0, true, $_MAPS_DEFAULT['pi_name']);
        $c->add('autofill_coord', $_MAPS_DEFAULT['autofill_coord'], 
                'select', 0, 3, 3, 290, true, $_MAPS_DEFAULT['pi_name']);
        $c->add('google_api_key', $_MAPS_DEFAULT['google_api_key'], 
                'text', 0, 3, 0, 300, true, $_MAPS_DEFAULT['pi_name']);
        $c->add('url_geocode', $_MAPS_DEFAULT['url_MAPScode'], 
                'text', 0, 3, 0, 310, true, $_MAPS_DEFAULT['pi_name']);
				
		// Permissions
        $c->add('fs_permissions', NULL, 'fieldset', 0, 4, NULL, 0, true, $_MAPS_DEFAULT['pi_name']);
        $c->add('default_permissions', $_MAPS_DEFAULT['default_permissions'],
                '@select', 0, 4, 12, 450, true, $_MAPS_DEFAULT['pi_name']);
				
		//This is display subgroup #1
		$c->add('sg_display', NULL, 'subgroup', 1, 0, NULL, 0, true, $_MAPS_DEFAULT['pi_name']);
		
		// Display settings
		//Map
		$c->add('fs_display', NULL, 'fieldset', 1, 8, NULL, 0, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('map_main_header', NULL, 'text', 1, 8, 0, 2, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('map_main_footer', NULL, 'text', 1, 8, 0, 4, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('use_cluster', $_MAPS_DEFAULT['use_cluster'], 
                'select', 1, 8, 3, 6, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('display_events_map', true, 
                'select', 1, 8, 3, 8, true, $_MAPS_DEFAULT['pi_name']);
		
		//Global Maps
		$c->add('fs_global_map', NULL, 'fieldset', 1, 9, NULL, 0, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('users_map', $_MAPS_DEFAULT['users_map'], 
                'select', 1, 9, 3, 6, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('global_map', $_MAPS_DEFAULT['global_map'], 
                'select', 1, 9, 3, 8, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('global_type', $_MAPS_DEFAULT['global_type'], 
                'select', 1, 9, 20, 10, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('global_width', $_MAPS_DEFAULT['global_width'], 
                'text', 1, 9, 0, 12, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('global_height', $_MAPS_DEFAULT['global_height'], 
                'text', 1, 9, 0, 14, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('global_zoom', $_MAPS_DEFAULT['global_zoom'], 
                'text', 1, 9, 0, 16, true, $_MAPS_DEFAULT['pi_name']);
				
		//profile fieldset
		$c->add('fs_display_profile', NULL, 'fieldset', 1, 10, NULL, 0, true, $_MAPS_DEFAULT['pi_name']);
        $c->add('display_geo_profile', $_MAPS_DEFAULT['display_geo_profile'], 
                'select', 1, 10, 3, 20, true, $_MAPS_DEFAULT['pi_name']);
        $c->add('map_type_profile', $_MAPS_DEFAULT['map_type_profile'], 
                'select', 1, 10, 20, 30, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('map_width_profile', $_MAPS_DEFAULT['map_width_profile'], 
                'text', 1, 10, 0, 40, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('map_height_profile', $_MAPS_DEFAULT['map_height_profile'], 
                'text', 1, 10, 0, 50, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('zoom_profile', $_MAPS_DEFAULT['zoom_profile'], 
                'text', 1, 10, 0, 55, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('show_directions_profile', $_MAPS_DEFAULT['show_directions_profile'], 
                'select', 1, 10, 3, 60, true, $_MAPS_DEFAULT['pi_name']);
		//geotag fieldset
		$c->add('fs_display_geo', NULL, 'fieldset', 1, 20, NULL, 0, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('map_type_geotag', $_MAPS_DEFAULT['map_type_geotag'], 
                'select', 1, 20, 20, 70, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('map_width_geotag', $_MAPS_DEFAULT['map_width_geotag'], 
                'text', 1, 20, 0, 80, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('map_height_geotag', $_MAPS_DEFAULT['map_height_geotag'], 
                'text', 1, 20, 0, 90, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('map_zoom_geotag', $_MAPS_DEFAULT['map_zoom_geotag'], 
                'text', 1, 20, 0, 100, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('show_directions_geo', $_MAPS_DEFAULT['show_directions_geo'], 
                'select', 1, 20, 3, 110, true, $_MAPS_DEFAULT['pi_name']);
		//map fieldset
		$c->add('fs_map_default', NULL, 'fieldset', 1, 30, NULL, 0, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('map_type', $_MAPS_DEFAULT['map_type'], 
                'select', 1, 30, 20, 120, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('map_width', $_MAPS_DEFAULT['map_width'], 
                'text', 1, 30, 0, 121, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('map_height', $_MAPS_DEFAULT['map_height'], 
                'text', 1, 30, 0, 122, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('map_zoom', $_MAPS_DEFAULT['map_zoom'], 
                'text', 1, 30, 0, 123, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('map_active', $_MAPS_DEFAULT['map_active'], 
                'select', 1, 30, 3, 130, true, $_MAPS_DEFAULT['pi_name']);
        $c->add('map_hidden', $_MAPS_DEFAULT['map_hidden'], 
                'select', 1, 30, 3, 131, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('free_markers', $_MAPS_DEFAULT['free_markers'], 
                'select', 1, 30, 3, 132, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('paid_markers', $_MAPS_DEFAULT['paid_markers'], 
                'select', 1, 30, 3, 133, true, $_MAPS_DEFAULT['pi_name']);
		//marker fieldset
		$c->add('fs_marker_default', NULL, 'fieldset', 1, 40, NULL, 0, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('marker_active', $_MAPS_DEFAULT['marker_active'], 
                'select', 1, 40, 3, 135, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('marker_hidden', $_MAPS_DEFAULT['marker_hidden'], 
                'select', 1, 40, 3, 136, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('marker_payed', $_MAPS_DEFAULT['marker_payed'], 
                'select', 1, 40, 3, 137, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('marker_validity', $_MAPS_DEFAULT['marker_validity'], 
                'select', 1, 40, 31, 138, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('label_color', $_MAPS_DEFAULT['label_color'], 
                'select', 1, 40, 30, 140, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('star_primary_color', $_MAPS_DEFAULT['star_primary_color'], 
                'text', 1, 40, 0, 142, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('star_stroke_color', $_MAPS_DEFAULT['star_stroke_color'], 
                'text', 1, 40, 0, 144, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('detail_zoom', $_MAPS_DEFAULT['detail_zoom'], 
                'text', 1, 40, 0, 148, true, $_MAPS_DEFAULT['pi_name']);
		//Presentation		
		$c->add('street', $_MAPS_DEFAULT['street'], 
                'select', 1, 40, 3, 150, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('code', $_MAPS_DEFAULT['code'], 
                'select', 1, 40, 3, 152, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('city', $_MAPS_DEFAULT['city'], 
                'select', 1, 40, 3, 154, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('state', $_MAPS_DEFAULT['state'], 
                'select', 1, 40, 3, 156, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('country', $_MAPS_DEFAULT['country'], 
                'select', 1, 40, 3, 158, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('tel', $_MAPS_DEFAULT['tel'], 
                'select', 1, 40, 3, 160, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('fax', $_MAPS_DEFAULT['fax'], 
                'select', 1, 40, 3, 162, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('web', $_MAPS_DEFAULT['web'], 
                'select', 1, 40, 3, 162, true, $_MAPS_DEFAULT['pi_name']);
		//Ressources
		$c->add('item_1', $_MAPS_DEFAULT['item_1'], 
                'text', 1, 40, 0, 264, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('item_2', $_MAPS_DEFAULT['item_2'], 
                'text', 1, 40, 0, 274, true, $_MAPS_DEFAULT['pi_name']);				
		$c->add('item_3', $_MAPS_DEFAULT['item_3'], 
                'text', 1, 40, 0, 284, true, $_MAPS_DEFAULT['pi_name']);				
		$c->add('item_4', $_MAPS_DEFAULT['item_4'], 
                'text', 1, 40, 0, 294, true, $_MAPS_DEFAULT['pi_name']);				
		$c->add('item_5', $_MAPS_DEFAULT['item_5'], 
                'text', 1, 40, 0, 304, true, $_MAPS_DEFAULT['pi_name']);				
		$c->add('item_6', $_MAPS_DEFAULT['item_6'], 
                'text', 1, 40, 0, 304, true, $_MAPS_DEFAULT['pi_name']);				
		$c->add('item_7', $_MAPS_DEFAULT['item_7'], 
                'text', 1, 40, 0, 314, true, $_MAPS_DEFAULT['pi_name']);				
		$c->add('item_8', $_MAPS_DEFAULT['item_8'], 
                'text', 1, 40, 0, 324, true, $_MAPS_DEFAULT['pi_name']);				
		$c->add('item_9', $_MAPS_DEFAULT['item_9'], 
                'text', 1, 40, 0, 334, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('item_10', $_MAPS_DEFAULT['item_10'], 
                'text', 1, 40, 0, 344, true, $_MAPS_DEFAULT['pi_name']);
		$c->add('infos_label', $_MAPS_DEFAULT['infos_label'], 
                'text', 1, 40, 0, 400, true, $_MAPS_DEFAULT['pi_name']);
    }				

    return true;
}

?>