<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.4                                                         |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// |                                                                           |
// | This file does two things: 1) it implements the necessary Geeklog Plugin  |
// | API methods and 2) implements all the common code needed by this plugin.  |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die ('This file can not be used on its own.');
}

/**
 * Language file include
 */
$plugin_path = $_CONF['path'] . 'plugins/maps/';
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';

if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}

/**
 * Load our config
 */
require_once $_CONF['path_system'].'classes/config.class.php';
$c = config::get_instance();
$_MAPS_CONF = $c->get_config('maps');

/*
* Include specific Maps config file
*/
require_once $_CONF['path'].'plugins/maps/maps.php';

// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementation                                         |
// +---------------------------------------------------------------------------+


function plugin_getheadercode_maps()
{
    global $_CONF, $_MAPS_CONF, $_SCRIPTS, $_PLUGINS,$_TABLES;
    
	$_SCRIPTS->setCSSFile('maps_css', '/' . $_MAPS_CONF['maps_folder'] . '/maps.css');
	$_SCRIPTS->setCSSFile('datepicker_ui', '/layout/' . $_CONF['theme'] . '/jquery_ui/jquery.ui.datepicker.css');
    $_SCRIPTS->setJavaScriptFile('maps_iconmaker', '/' . $_MAPS_CONF['maps_folder'] . '/js/mapiconmaker.js', false);
	$_SCRIPTS->setJavaScriptLibrary('jquery');
	
	$request_type = (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1' || strstr(strtoupper($_SERVER['HTTP_X_FORWARDED_BY']),'SSL') || strstr(strtoupper($_SERVER['HTTP_X_FORWARDED_HOST']),'SSL')) ? 'SSL' : 'NONSSL';
	if( strcmp($request_type,'ssl')){
		$js = '<script src= "https://maps-api-ssl.google.com/maps/api/js?key=' . $_MAPS_CONF['google_api_key'] . '&amp;libraries=adsense&amp;sensor=false&language='.COM_getLanguageId().'" type="text/javascript"> </script>';
	} else {
		$js = '<script src= "http://maps.googleapis.com/maps/api/js?key=' . $_MAPS_CONF['google_api_key'] . '&amp;libraries=adsense&amp;sensor=false&language='.COM_getLanguageId().'" type="text/javascript"> </script>';
	}

	$_SCRIPTS->setJavaScript($js, false, false);
	$url = COM_getCurrentURL();
    
    if ( ( stripos($url, 'calendar/index.php') !== FALSE  || stripos($url, 'event.php') !== FALSE && $_GET['eid'] != '') && in_array('calendar', $_PLUGINS) && $_MAPS_CONF['display_events_map'] )
    {
        $js_events_map = MAPS_displayEventsMap(false);
        
        $_SCRIPTS->setJavaScript($js_events_map, false, false);
        
        $str .= '    jQuery(document).ready(function() {' . LB;
        
        if (stripos($url, 'calendar/index.php') !== FALSE ) {
            $str .= '        jQuery(\'.cal-header\').after(\'<div id="map_contener_mevents"><div id="map_canvas_mevents"></div></div>\');' . LB;
        }

        if (stripos($url, 'event.php') !== FALSE && $_GET['eid'] != '') {
            // get eid
            $eid = addslashes ($_GET['eid']);
            
            $js_one_event = MAPS_displayEventsMap(false, $eid);
            $_SCRIPTS->setJavaScript($js_one_event, false, false);

            if ($eid != '')  $str .= '        jQuery(\'.location\').append(\'<div style="margin:10px 0px;" id="map_contener_mevents"><div id="map_canvas_mevents"></div></div>\');' . LB;
        }
        $str .= '    });' . LB;
    }
	
	$_SCRIPTS->setJavaScript($str, true, true);

    return;
}

/**
* Returns doc uri for Geeklog-1.6.0 or later
*/
function plugin_getdocumentationurl_maps() {
    
    return 'http://geeklog.fr/downloads/index.php/maps';
}

/**
* Returns the item(s) for this plugin that should appear on the main menu
*
* NOTE: this MUST return the url/value pairs in the following format
* $<arrayname>[<label>] = <url>
*
* @return   mixed   menu entry, or boolean false if disabled / hidden
*
*/
function plugin_getmenuitems_maps()
{
    global $_CONF, $_MAPS_CONF, $LANG_MAPS_1;
    
    $menuitems ='';
    
    if ($_MAPS_CONF['hide_maps_menu'] == 0) {
        $url = $_MAPS_CONF['site_url'] . '/index.php';
        $menuitems[$LANG_MAPS_1['plugin_name']] = $url;
    }
    return $menuitems;
}

/**
* Geeklog is asking us to provide any items that show up in the type
* drop-down on search.php.  Let's users search for links.
*
* @return   array   (plugin name/entry title) pair for the dropdown
*
*/

function plugin_searchtypes_maps()
{
    global $LANG_MAPS_1;

    $tmp['maps'] = $LANG_MAPS_1['plugin_name'];

    return $tmp;
}


/**
* This searches for entries matching the user query and returns an array for the
* header and table rows back to search.php where it will be formated and printed
*
* @param    string  $query      Keywords user is looking for
* @param    date    $datestart  Start date to get results for
* @param    date    $dateend    End date to get results for
* @param    string  $topic      The topic they were searching in
* @param    string  $type       Type of items they are searching, or 'all' (deprecated)
* @param    int     $author     Get all results by this author
* @param    string  $keyType    search key type: 'all', 'phrase', 'any'
* @param    int     $page       page number of current search (deprecated)
* @param    int     $perpage    number of results per page (deprecated)
* @return   object              search result object
*
*/

function plugin_dopluginsearch_maps($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
    global $_TABLES, $LANG_MAPS_1, $_MAPS_CONF;

    // Make sure the query is SQL safe
    $query = trim(addslashes($query));

    $sql = "SELECT mkid AS id, name as title, description, UNIX_TIMESTAMP(created) AS date, owner_id AS uid, mid, ";
    $sql .= "CONCAT('/" . $_MAPS_CONF['maps_folder'] . "/markers.php?mode=show&mkid=', mkid, '&mid=', mid) AS url ";
    $sql .= "FROM {$_TABLES['maps_markers']} WHERE 1 = 1 ";
    $sql .= COM_getPermSQL('AND') . ' ';

    if (!empty($datestart) && !empty($dateend)) {
        $delim = substr($datestart, 4, 1);
        if (!empty($delim)) {
            $DS = explode($delim, $datestart);
            $DE = explode($delim, $dateend);
            $startdate = mktime(0,0,0,$DS[1],$DS[2],$DS[0]);
            $enddate = mktime(23,59,59,$DE[1],$DE[2],$DE[0]);
            $sql .= "AND ( UNIX_TIMESTAMP(created) BETWEEN '$startdate' AND '$enddate' ) ";
        }
    }
    if (!empty ($author)) {
        $sql .= "AND (owner_id = '$author') ";
    }

    $search = new SearchCriteria('maps', $LANG_MAPS_1['plugin_name']);
    $columns = array('name', 'description');
    list($sql,$ftsql) = $search->buildSearchSQL($keyType, $query, $columns, $sql);
    $search->setSQL($sql);
    $search->setFTSQL($ftsql);
    $search->setRank(3);

    return $search;
}


/**
* Implements autotags
*
* @param    string  $op         operation to perform
* @param    string  $content    item (e.g. story text), including the autotag
* @param    array   $autotag    parameters used in the autotag
* @param    mixed               tag names (for $op='tagname') or formatted content
*
*/

function plugin_autotags_maps($op, $content = '', $autotag = '')
{
    global $_CONF, $_MAPS_CONF, $_TABLES, $LANG_MAPS_1, $_SCRIPTS;

    switch ($op) {

    case 'tagname':
        $retval = array ('geo', 'maps', 'marker');
        break;
		
	case 'description' :
        $retval = array (
            'maps' => $LANG_MAPS_1['autotag_desc_maps'],
			'geo' => $LANG_MAPS_1['autotag_desc_geo'],
			'marker' => $LANG_MAPS_1['autotag_desc_marker']
            );
		break;
		
    case 'parse':
        $HTML = '';
        switch( $autotag['tag'] ) {
            case 'geo' :
                $option = COM_applyFilter($autotag['parm1']);
                switch ($option) {
					case 'map':
						if ($_MAPS_CONF['google_api_key'] == '' ||
							$_MAPS_CONF['url_geocode'] == '') {
							break;
						}
						
						$px = explode (' ', trim ($autotag['parm2']));
						if (is_array ($px)) {
							foreach ($px as $part) {
								if (substr ($part, 0, 5) == 'zoom:') {
									$a = explode (':', $part);
									$zoom = $a[1];
									$skip++;
								} else if (substr ($part, 0, 6) == 'width:') {
									$a = explode (':', $part);
									$width = $a[1];
									$skip++;
								} else if (substr ($part, 0, 7) == 'height:') {
									$a = explode (':', $part);
									$height = $a[1];
									$skip++;
								} else {
									break;
								}
							}
							if ($skip != 0) {
								if (count ($px) > $skip) {
									for ($i = 0; $i < $skip; $i++) {
										array_shift ($px);
									}
									$address = trim (implode (' ', $px));
								} else {
									$address = '';
								}
							} else {
								$address = trim ($autotag['parm2']);
							}
						}
						
						if (! empty($address)) {
							//update mysql 5.1
							MAPS_getDBCoords($gid, $address, $lat, $lng) ;
							if ($lat == 0 || $lng == 0)
								break;
							$T = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
							$T->set_file('page', 'geo.thtml');
							//randomise to avoid duplicate map number
							$rid = mt_rand (1000,9999);
							$T->set_var('gid', $rid);
							$T->set_var('address', addslashes($address));
							$lat = str_replace(",",".",$lat);
							$lng = str_replace(",",".",$lng);
							$T->set_var('lat', $lat);
							$T->set_var('lng', $lng);

							$T->set_var('map_type', $_MAPS_CONF['map_type_geotag']);
							if ($width != '') {
								$T->set_var('map_width_geotag', $width);
							} else {
							$T->set_var('map_width_geotag', $_MAPS_CONF['map_width_geotag']);
							}
							if ($height != '') {
								$T->set_var('map_height_geotag', $height);
							} else {
							$T->set_var('map_height_geotag', $_MAPS_CONF['map_height_geotag']);
							}
							if ($zoom != '') {
								$T->set_var('zoom', $zoom);
							} else {
								$T->set_var('zoom', $_MAPS_CONF['map_zoom_geotag']);
							}
							
							if ( $_MAPS_CONF['show_directions_geo'] == 0) {
							$T->set_var('show_directions_geo', 'none');
							} else {
							$T->set_var('show_directions_geo', 'block');
							}
							$T->set_var('error_address_empty', $LANG_MAPS_1['error_address_empty']);
							$T->set_var('error_invalid_address', $LANG_MAPS_1['error_invalid_address']);
							$T->set_var('error_google_error', $LANG_MAPS_1['error_google_error']);
							$T->set_var('error_no_map_info', $LANG_MAPS_1['error_no_map_info']);
							
							//Ads
							
						    $ads = MAPS_getAds ($rid);
						
							$T->set_var('ads', $ads);
							
							$T->set_var('need_directions', $LANG_MAPS_1['need_directions']);
							$T->set_var('get_directions', $LANG_MAPS_1['get_directions']);
							$T->set_var('value', htmlentities($LANG_MAPS_1['value_directions'], ENT_QUOTES));
						
							$T->parse('output','page');
							$HTML = $T->finish($T->get_var('output'));
							}
						break;
                    } // switch($option)
                break;
				
            case 'maps' :
                $mid = COM_applyFilter($autotag['parm1']);
				$px = explode (' ', trim ($autotag['parm2']));
					if (is_array ($px)) {
						foreach ($px as $part) {
							if (substr ($part, 0, 5) == 'zoom:') {
								$a = explode (':', $part);
								$zoom = $a[1];
								$skip++;
							} else {
								break;
							}
						}
						if ($skip != 0) {
							if (count ($px) > $skip) {
								for ($i = 0; $i < $skip; $i++) {
									array_shift ($px);
								}
								$address = trim (implode (' ', $px));
							} else {
								$address = '';
							}
						} else {
							$address = trim ($autotag['parm2']);
						}
					}
                if ( !empty($mid) && $mid > 0 ) {
                    $HTML = MAPS_getMap($mid, $zoom, $address);
                } else if ($mid == 0) {
                    $HTML = MAPS_getGlobalMap($zoom, $address);
                }
                break;
                
            case 'marker' :
                $mkid = COM_applyFilter($autotag['parm1']);
                if (! empty($mkid)) {
                    $sql = "SELECT * FROM {$_TABLES['maps_markers']} WHERE mkid = {$mkid} LIMIT 1";
                    $res = DB_query($sql, 0);
                    $A = DB_fetchArray($res);
                    if (!SEC_hasAccess2($A)) {
                        $HTML ='';
                    } else if($A['mid']!=''){
                        // marker exists and access is ok
						$HTML = MAPS_getMarkerDetail($A['mid'], $mkid, 1);
                    }  else {
						$HTML ='';
					}
                } else {
                    $HTML ='';
                }
                break;
                } //switch autotag
            
            //Replace the autotag with the map
            $retval = str_replace($autotag['tagstr'], $HTML, $content);
            break;

    default:
        $retval = $content;
        break;

    }   // switch ($op)

    return $retval;
}

//Return code for ads from google adsense
function MAPS_getAds ($mid) {
    
	global $_MAPS_CONF;
	
	if ($_MAPS_CONF['AdsOnMap'] == 1) {
		
		$ads = "  var adUnitDiv_$mid = document.createElement('div');" . LB;
		$ads .= "  var adUnitOptions_$mid = {" . LB;
		$ads .= "	format: google.maps.adsense.AdFormat.HALF_BANNER," . LB;
		$ads .= "	position: google.maps.ControlPosition.RIGHT_BOTTOM," . LB;
		$ads .= "	backgroundColor: '#ffffff'," . LB;
		$ads .= "	borderColor: '#eeeeee'," . LB;
		$ads .= "	titleColor: '#cccccc'," . LB;
		$ads .= "	textColor: '#666666'," . LB;
		$ads .= "	urlColor: '#009900'," . LB;
		$ads .= "	map: map{$mid}," . LB;
		$ads .= "	visible: true," . LB;
		$ads .= "	publisherId: '{$_MAPS_CONF['publisher_id']}'," . LB;
		
		if ($_MAPS_CONF['channel_id'] != '') $ads .= "	channelNumber: '{$_MAPS_CONF['channel_id']}'" . LB;
		
		$ads .= "  }" . LB;
		$ads .= "  adUnit_$mid = new google.maps.adsense.AdUnit(adUnitDiv_$mid, adUnitOptions_$mid);" . LB;

	}
	return $ads;
}
 
/**
* Add a block on user profile to display a map if user location is set
*
*/
function plugin_profileblocksdisplay_maps ($uid)
{
    global $_CONF, $_MAPS_CONF, $_TABLES, $LANG_MAPS_1;
    
    $retval = '';
    
    if ($_MAPS_CONF['display_geo_profile'] == 0 || $_MAPS_CONF['google_api_key'] == '' ||
    $_MAPS_CONF['url_geocode'] == '') {
        return $retval;
    } else {
    	//check if address is in the db
		$sql = "SELECT location FROM {$_TABLES['userinfo']} WHERE uid = '$uid'";
		list($address) = DB_fetchArray(DB_query($sql));

		if (! empty($address)) {
			//upaded for mysql 5.1
			MAPS_getDBCoords($gid, $address, $lat, $lng) ;
			
			$T = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
			$T->set_file('page', 'geo_profile.thtml');
			$T->set_var('gid', $gid);
			$T->set_var('address', addslashes($address));
			$lat = str_replace(",",".",$lat);
			$lng = str_replace(",",".",$lng);
			$T->set_var('lat', $lat);
			$T->set_var('lng', $lng);
			
			$T->set_var('map_type', $_MAPS_CONF['global_type']);
			$T->set_var('key', $_MAPS_CONF['google_api_key']);

			$T->set_var('map_width_geotag', $_MAPS_CONF['map_width_profile']);
			$T->set_var('map_height_geotag', $_MAPS_CONF['map_height_profile']);
			$T->set_var('zoom', $_MAPS_CONF['zoom_profile']);
			
			if ( $_MAPS_CONF['show_directions_profile'] == 0) {
				$T->set_var('show_directions_geo', 'none');
			} else {
			$T->set_var('show_directions_geo', 'block');
			}
			$T->set_var('error_address_empty', $LANG_MAPS_1['error_address_empty']);
			$T->set_var('error_invalid_address', $LANG_MAPS_1['error_invalid_address']);
			$T->set_var('error_google_error', $LANG_MAPS_1['error_google_error']);
			$T->set_var('error_no_map_info', $LANG_MAPS_1['error_no_map_info']);
			
			//Ads			
			$ads = MAPS_getAds ($gid);
			$T->set_var('ads', $ads);
			
			$T->set_var('need_directions', $LANG_MAPS_1['need_directions']);
			$T->set_var('get_directions', $LANG_MAPS_1['get_directions']);
			$T->parse('output','page');
			$retval .= '<h2>' . $LANG_MAPS_1['profile_title'] . '</h2>';
			$retval .= $T->finish($T->get_var('output'));
		}
    }
 
    return $retval;
}

/**
* Return plugin entry for "Command and Control" (moderation.php)
*
* @return  array  Array containing (plugin name, admin url, url of plugin icon)
*
*/
function plugin_cclabel_maps()
{
    global $_CONF, $LANG_MAPS_1;

    $retval = array();
    if (SEC_hasRights('maps.admin')) {
        $retval = array($LANG_MAPS_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/maps/index.php',
            plugin_geticon_maps());
    }

    return $retval;
}

/**
* Return plugin entry for the Admins Only block
*
* @return array Array containing (plugin name, plugin admin url, # of plugin items or '')
*/
function plugin_getadminoption_maps()
{
    global $_CONF, $LANG_MAPS_1, $_TABLES;

    if (SEC_hasRights('maps.admin')) {
        return array($LANG_MAPS_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/maps/index.php', DB_count($_TABLES['maps_maps']));
    }
}

/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_maps()
{
    global $_CONF;
    
    return $_CONF['site_admin_url'] . '/plugins/maps/images/maps.png';
}

/**
 * Returns the current plugin code version
 *
 * @return string    plugin version
 */
function plugin_chkVersion_maps()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/maps/autoinstall.php';

    $inst_parms = plugin_autoinstall_maps('maps');

    return $inst_parms['info']['pi_version'];
}

/**
* Update the plugin
*
* @return   int     Number of message to display or true for success
*
*/
function plugin_upgrade_maps()
{
    global $_CONF, $_TABLES;

    $installed_version = DB_getItem($_TABLES['plugins'], 'pi_version',
                                    "pi_name = 'maps'");
    $code_version = plugin_chkVersion_maps();
    if ($installed_version == $code_version) {
        // nothing to do
        return true;
    }

    require_once $_CONF['path'] . 'plugins/maps/autoinstall.php';
    require_once $_CONF['path'] . 'plugins/maps/install_defaults.php';
    require_once $_CONF['path_system'] . 'classes/config.class.php';

    if (! plugin_compatible_with_this_version_maps('maps')) {
        return 3002;
    }

    // other update code goes here
    switch( $installed_version ) {
        case "1.0" :
            DB_query("INSERT INTO {$_TABLES['vars']} (`name`, `value`) VALUES ('globalMapHits', '0')");
        
        case "1.0.1" :
            DB_query("ALTER TABLE {$_TABLES['maps_markers']}
            ADD cat INT(6) default '0' after name
            ");
            
            DB_query("CREATE TABLE {$_TABLES['maps_markers_cat']} (
            cid INT auto_increment,
            category VARCHAR(32) NOT NULL,
            PRIMARY KEY (cid)
            ) ENGINE=MyISAM
            ",1);
            
            DB_query("CREATE TABLE {$_TABLES['maps_markers_values']} (
            marker BIGINT NOT NULL,
            field INT NOT NULL,
            value TEXT
            ) ENGINE=MyISAM
            ",1);
        
        case "1.0.2" :
            DB_query("ALTER TABLE {$_TABLES['maps_submission']}
            ADD cat INT(6) default '0' after name
            ");
            
            DB_query("CREATE TABLE {$_TABLES['maps_markers_fields']} (
            fid INT auto_increment,
            cat_id INT NOT NULL,
            field_order SMALLINT(5) unsigned NOT NULL default '1',
            field_name VARCHAR(255) NOT NULL,
            autotag TINYINT(1) unsigned NOT NULL default '0',
            owner_id mediumint(8) unsigned NOT NULL default '1',
            group_id mediumint(8) unsigned NOT NULL default '1',
            perm_owner tinyint(1) unsigned NOT NULL default '3',
            perm_group tinyint(1) unsigned NOT NULL default '2',
            perm_members tinyint(1) unsigned NOT NULL default '2',
            perm_anon tinyint(1) unsigned NOT NULL default '2',
            PRIMARY KEY (fid)
            ) ENGINE=MyISAM
            ",1);
        
        case "1.0.3" :
		case "1.0.4" :
            $c = config::get_instance();
            $c->add('infos_label', $_MAPS_DEFAULT['infos_label'], 
                'text', 1, 40, 0, 400, true, $_MAPS_DEFAULT['pi_name']);
		case "1.0.5" :
        case "1.0.6" :
        case "1.0.7" :		
		case "1.0.8" :          
		case "1.0.9" :
		    $c = config::get_instance();
			$c->add('zoom_profile', $_MAPS_DEFAULT['zoom_profile'], 
                'text', 1, 10, 0, 55, true, $_MAPS_DEFAULT['pi_name']);
		    DB_query("ALTER TABLE {$_TABLES['maps_markers']}
			ADD mk_default tinyint(1) unsigned NOT NULL default '1' AFTER mid,
            ADD mk_pcolor VARCHAR(7) NOT NULL default '#666666' AFTER mk_default,
            ADD mk_scolor VARCHAR(7) NOT NULL default '#666666' AFTER mk_pcolor,
            ADD mk_label char(1) AFTER mk_scolor,
            ADD mk_label_color tinyint(1) unsigned NOT NULL default '0' AFTER mk_label
            ");
			
		case "1.1" :
			DB_query("CREATE TABLE {$_TABLES['maps_overlays']} (
			  oid INT NOT NULL AUTO_INCREMENT,
			  o_mapid INT NOT NULL,
			  o_image VARCHAR(255),
			  o_sw_latlng FLOAT( 10, 6 ) NOT NULL ,
			  o_ne_latlng FLOAT( 10, 6 ) NOT NULL ,
			  o_active TINYINT(1),
			  o_zoom_min TINYINT(1),
			  o_zoom_max TINYINT(1),
			  PRIMARY KEY (oid)
			) ENGINE=MyISAM
			");

			DB_query("CREATE TABLE {$_TABLES['maps_map_overlay']} (
				mo_id int(11) NOT NULL auto_increment,
				mo_mid int(11),
				mo_oid int(11),
				PRIMARY KEY (mo_id)
			) ENGINE=MyISAM
			");
		
		case "1.1.1" :
		    DB_query("ALTER TABLE {$_TABLES['maps_overlays']}
			ADD o_name VARCHAR(255)
			");
		case "1.1.2" :
		    DB_query("ALTER TABLE {$_TABLES['maps_overlays']}
			DROP o_sw_latlng,
			DROP o_ne_latlng,
			ADD o_sw_lat FLOAT( 10, 6 ) NOT NULL,
			ADD o_sw_lng FLOAT( 10, 6 ) NOT NULL,
			ADD o_ne_lat FLOAT( 10, 6 ) NOT NULL,
			ADD o_ne_lng FLOAT( 10, 6 ) NOT NULL
			");
		case "1.1.3" :
		    DB_query("ALTER TABLE {$_TABLES['maps_overlays']}
			DROP o_mapid
			");
		case "1.1.4" :
		    DB_query("CREATE TABLE {$_TABLES['maps_map_icons']} (
			  icon_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
			  icon_name VARCHAR(255),
			  icon_image VARCHAR(255),
			  PRIMARY KEY (icon_id)
			) ENGINE=MyISAM
			");
			
		case "1.1.5" :
		    DB_query("ALTER TABLE {$_TABLES['maps_markers']}
			ADD mk_icon smallint(5) unsigned default '0' AFTER mk_label_color
			");
			
		case "1.1.6" :
            $c = config::get_instance();
            $c->add('use_cluster', $_MAPS_DEFAULT['use_cluster'], 
                'select', 1, 8, 3, 6, true, $_MAPS_DEFAULT['pi_name']);
				
		case "1.1.7" :
		    DB_query("ALTER TABLE {$_TABLES['maps_overlays']}
			ADD o_order smallint(5) unsigned NOT NULL default '1'
			");
		case "1.1.8":
		case "1.2.0":
		case "1.2.1":
            DB_query("CREATE TABLE {$_TABLES['maps_overlays_groups']} (
                o_group_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
                o_group_name VARCHAR(255),
                PRIMARY KEY (o_group_id)
                ) ENGINE=MyISAM
                ");		
		case "1.2.2":
		    DB_query("ALTER TABLE {$_TABLES['maps_overlays']}
			ADD o_group SMALLINT(5) NOT NULL DEFAULT '0' AFTER o_name
			");
		
        case "1.2.3":
		    //Migrate to google maps api v2 to v3
			$res = DB_query("SELECT * FROM {$_TABLES['maps_maps']} WHERE 1 = 1");
			while ($map = DB_fetchArray($res)) {
			    $type = 'ROADMAP';
				if ($map['type'] == 'G_SATELLITE_MAP') $type = 'SATELLITE';
				if ($map['type'] == 'G_PHYSICAL_MAP ') $type = 'TERRAIN';
				if ($map['type'] == 'G_HYBRID_MAP') $type = 'HYBRID';
				DB_query("UPDATE {$_TABLES['maps_maps']} SET type = '$type' WHERE mid = {$map['mid']}");
				COM_errorLog( "Updated map {$map['name']} to type $type", 1 );
			}
			$c = config::get_instance();
			$type = 'ROADMAP';
			if ($_MAPS_CONF['global_type'] == 'G_SATELLITE_MAP') $type = 'SATELLITE';
			if ($_MAPS_CONF['global_type'] == 'G_PHYSICAL_MAP ') $type = 'TERRAIN';
			if ($_MAPS_CONF['global_type'] == 'G_HYBRID_MAP') $type = 'HYBRID';
            $c->set('global_type', $type, $_MAPS_DEFAULT['pi_name']);
            $c->set('url_geocode', 'http://maps.googleapis.com/maps/api/geocode/json?address=%address%&sensor=false', $_MAPS_DEFAULT['pi_name']);
			
			COM_errorLog( "Updated map config to type $type", 1 );
			
			//Drop unused tables
			DB_query("DROP TABLE IF EXISTS {$_TABLES['maps_markers_fields']}, {$_TABLES['maps_markers_values']}, {$_TABLES['maps_markers_cat']}");
			
			//Remove cat field from markers table
			DB_query("ALTER TABLE {$_TABLES['maps_markers']} DROP cat");
			
			//Change type of the description markers 
			DB_query("ALTER TABLE {$_TABLES['maps_markers']} MODIFY description TEXT NOT NULL default '';");
			
		case '1.3.0':
		    DB_query("ALTER TABLE {$_TABLES['maps_submission']} MODIFY description TEXT NOT NULL default '';");
			DB_query("ALTER TABLE {$_TABLES['maps_submission']}
			ADD mk_default tinyint(1) unsigned NOT NULL default '1' AFTER mid
            ");
			
		case '1.3.1':
		case '1.3.2':
		    DB_query("ALTER TABLE {$_TABLES['maps_markers']}
			ADD type varchar(20) NOT NULL default '' AFTER hits
            ");
			DB_query("ALTER TABLE {$_TABLES['maps_markers']}
			ADD url varchar(255) NOT NULL default '' AFTER hits
            ");
			DB_query("ALTER TABLE {$_TABLES['maps_submission']}
			ADD type varchar(20) NOT NULL default '' AFTER hits
            ");
			DB_query("ALTER TABLE {$_TABLES['maps_submission']}
			ADD url varchar(255) NOT NULL default '' AFTER hits
            ");

		case '1.3.3':
		case '1.3.4':
			DB_query("ALTER TABLE {$_TABLES['maps_maps']}
			ADD mmk_default tinyint(1) unsigned NOT NULL default '1' AFTER footer
            ");
			DB_query("ALTER TABLE {$_TABLES['maps_maps']}
			ADD mmk_icon smallint(5) unsigned default '0' AFTER label_color
            ");
			
			$c = config::get_instance();
			$c->add('display_events_map', true, 
                'select', 1, 8, 3, 8, true, $_MAPS_DEFAULT['pi_name']);
		case '1.3.5':
        case '1.4.0':
			
        default :
            // update plugin version number
            DB_query("UPDATE {$_TABLES['plugins']} SET pi_version = '$code_version', pi_gl_version = '$installed_version' WHERE pi_name = 'maps'");
			COM_errorLog( "Updated maps plugin from v$installed_version to v$code_version", 1 );
            /* This code is for statistics ONLY */
            $message =  'Completed maps plugin upgrade: ' . date('m d Y',time()) . "   AT " . date('H:i', time()) . "\n";
            $message .= 'Site: ' . $_CONF['site_url'] . ' and Sitename: ' . $_CONF['site_name'] . "\n";
            COM_mail("ben@geeklog.fr","Updated maps plugin from v$installed_version to v$code_version",$message);
    }
    return true;
}

/**
* Automatic uninstall function for plugins
*
* This code is automatically uninstalling the plugin.
* It passes an array to the core code function that removes
* tables, groups, features and php blocks from the tables.
* Additionally, this code can perform special actions that cannot be
* foreseen by the core code (interactions with other plugins for example)
*
* @return   array   Plugin information
*
*/
function plugin_autouninstall_maps()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array('maps_maps', 
		                  'maps_geo', 
						  'maps_markers', 
						  'maps_submission',
						  'maps_overlays',
						  'maps_map_overlay',
						  'maps_map_icons',
						  'maps_overlays_groups'
						  ),
        /* give the full name of the group, as in the db */
        'groups' => array('Maps Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('maps.admin'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(),
        /* give all vars with their name */
        'vars' => array('globalMapHits')
    );

    return $out;
}

/**
*   Looks up coordinates from Google for the given address.
*   Requires that the 'url_geocode' value is configured, and that there
*   is a valid Google Maps API key configured.
*
*   @param  string  $address    Address to look up
*   @param  float   &$lat       Reference to latitude value
*   @param  float   &$lng       Reference to longitude value
*   @return int     HTTP code, or an integer indicating the reason
*/
//update to mysql 5.1
function MAPS_getCoords($address, &$lat, &$lng)
{
    global $_MAPS_CONF;

    // Check for valid Google config items
    if (empty($_MAPS_CONF['url_geocode']) || empty($_MAPS_CONF['google_api_key'])) {
        COM_errorLog('MAPS - Google config items is not valid. url geocode: ' . $_MAPS_CONF['url_geocode'] . ' | API key: ' . $_MAPS_CONF['google_api_key']);
		return 0;
    }
	
    $timeout = 30;
    $patterns = array(
        '/%address%/'
    );
	
    $replacements = array(
        urlencode($address)
    );
	
    $url = preg_replace($patterns, $replacements, $_MAPS_CONF['url_geocode']);

	if(function_exists(curl_init)) {
		
		$ch = @curl_init();
		@curl_setopt($ch, CURLOPT_URL, $url);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		@curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		$response = @curl_exec ($ch);
		@curl_close ($ch);
		unset($ch);
		
	} else if (ini_get('allow_url_fopen') == 0) {
        
		// Parse the url for fsockopen
		$fsockurl = @parse_url($url);
		$response = '';
		if(empty($fsockurl['port']))
		{
			$fsockurl['port'] = 80;
		}
		if(!empty($fsockurl['query']))
		{
			$fsockurl['path'].="?" . $fsockurl['query'];
		}
		// Make the request
		$fsock = @fsockopen($fsockurl['host'],  $fsockurl['port'], $errno, $errstr, $timeout);
		
		if($fsock||!is_resource($fsock))
		{
			@fputs($fsock, "GET " . $fsockurl['path'] . " HTTP/1.0\r\n");
			@fputs($fsock, "HOST: " . $fsockurl['host'] . "\r\n");
			@fputs($fsock, "Connection: close\r\n\r\n");
			while ((!@feof($fsock)))
			{
				// Load data as long as there is data and the connection didn't time out
				$response = @fgets($fsock, 1024);
			}

			@fclose($fsock);
			unset($fsockurl, $fsock);
		}
		
	} else if (ini_get('allow_url_fopen') == 1){
		
		$response = @file_get_contents($url);
		if ($response == '') {
		    COM_errorLog('MAPS geocoder: empty response by url fopen from ' . $url);
			return false;
		}
	} else {
	    
		COM_errorLog('MAPS geocoder: error to get data from google');
		return false;
	}
	
	$json_result = json_decode($response, true);
	
	$geo_result =  $json_result->results[0];

    $lat = $json_result['results'][0]['geometry']['location']['lat'];
    $lng = $json_result['results'][0]['geometry']['location']['lng'];
	
	$trace = debug_backtrace();
    $caller = array_shift($trace);
	COM_errorLog('MAPS geocoder: ' . $address .  ' call by ' . $caller['function'] . ' |' . $lat . '|' . $lng);
    
	return true;
}

/**
*   Looks up coordinates from DB for the given address.
*
*   @param  string  $address    Address to look up
*   @return int     HTTP code, or an integer indicating the reason
*/
//Updated to mysql 5.1
function MAPS_getDBCoords(&$gid, $address, &$lat, &$lng)
{
    global $_TABLES, $_MAPS_CONF;
    
    $table = $_TABLES['maps_geo'];
    $address = addslashes($address);
    
    //check if address is in the db
    $sql = "SELECT gid,lat,lng,geo FROM $table WHERE geo = '$address' LIMIT 1";
    
    list($gid,$lat,$lng,$geo) = DB_fetchArray(DB_query($sql));

    if ( empty($lat) ) {
        //Geoencode the address
        //Updated to mysql 5.1
        $map = MAPS_getCoords($address, $lat, $lng);
        $lat = str_replace(",",".",$lat);
        $lng = str_replace(",",".",$lng);
        
        //Store the result for the next time
		if ($lat =='' ) $lat =0;
		if ($lng =='' ) $lng =0;
		if ($geo == '') {
		    //Creation
            $sql = "INSERT INTO $table (geo,lat,lng) VALUES ('" . $address . "','" . addslashes($lat) . "','" . addslashes($lng) . "')";
		} else {
		    //update
			$geo = addslashes($geo);
			$sql = "UPDATE $table SET lat = '" . addslashes($lat) . "', lng = '" . addslashes($lng) . "' WHERE geo = '$geo'";
			//COM_errorLog("MAPS - Update geo: " . stripslashes($geo));
		}
        DB_query($sql);
        
        //Callback gid
        $sql = "SELECT gid,lat,lng FROM $table WHERE geo = '$address' LIMIT 1";
        list($gid,$lat,$lng) = DB_fetchArray(DB_query($sql));
    }

    $lat = (float)$lat;
    $lng = (float)$lng;
    
    return;
}

/**
 * Returns user menu display
 *
 * Generates the user menu from the template and returns the result as a string of HTML
 *
 * @return string HTML of user menu
 */
function MAPS_user_menu() {
    global $_CONF, $_MAPS_CONF, $LANG_MAPS_1, $_MAPS_CONF;

    $display = '<p>';

    // generate the menu from the template
    $menu = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
    $menu->set_file(array('menu' => 'user_menu.thtml'));
    $menu->set_var('maps_url', $_MAPS_CONF['site_url']);
    $menu->set_var('site_url', $_CONF['site_url']);
    $menu->set_var('maps', $LANG_MAPS_1['user_home']);
    
    //Check for submission rights
    if ($_MAPS_CONF['marker_submission'] == 1) {
        $menu->set_var('submit_marker', ' | <a href="' . $_CONF['site_url'] . '/submit.php?type=maps">' . $LANG_MAPS_1['submit_marker'] . '</a>');
		//Check for login users
		if (!COM_isAnonUser()) {
		    $menu->set_var('my_markers', ' | <a href="' . $_MAPS_CONF['site_url'] . '/markers.php">' . $LANG_MAPS_1['my_markers'] . '</a>');
		} else {
		    $menu->set_var('my_markers', '');
		}
    } else {
        $menu->set_var('submit_marker', '');
		$menu->set_var('my_markers', '');
    }
    
    if (SEC_hasRights('maps.admin')) {
        $menu->set_var('admin', ' | ' . '<a href="' . $_CONF['site_url'] . '/admin/plugins/maps/index.php">' . $LANG_MAPS_1['admin'] . '</a>');
    } else {
        $menu->set_var('admin', '');
    }
    $display .= $menu->parse('output', 'menu');

    $display .= '</p>';

    // return results
    return $display;
}

/**
 * Returns admin menu display
 *
 * Generates the admin menu from the template and returns the result as a string of HTML
 *
 * @return string HTML of admin menu
 * @todo add support for pending (physical) transaction, not fully implemented
 */
function MAPS_admin_menu() {
    global $_CONF, $_TABLES, $LANG_MAPS_1;

    $display = '<p>';

    // generate the menu from the template
    $menu = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
    $menu->set_file(array('menu' => 'admin_menu.thtml'));
    $menu->set_var('site_admin_url', $_CONF['site_admin_url']);
    $menu->set_var('site_url', $_CONF['site_url']);
    $menu->set_var('admin_home', $LANG_MAPS_1['maps']);
    $menu->set_var('markers', $LANG_MAPS_1['markers']);
	$menu->set_var('icons', $LANG_MAPS_1['icons']);
	$menu->set_var('overlays', $LANG_MAPS_1['overlays']);
	$menu->set_var('import_export', $LANG_MAPS_1['import_export']);
    $menu->set_var('geocoder', $LANG_MAPS_1['geocoder']);
    $menu->set_var('set_user_geo', $LANG_MAPS_1['set_user_geo']);
    $menu->set_var('configuration', $LANG_MAPS_1['configuration']);

    $display .= $menu->parse('output', 'menu');

    $display .= '</p>';

    // return results
    return $display;
}

/**
 * Filters submitted variables
 *
 * Uses geeklog functions to filter submitted variables for illegal content.  Any editing is
 * done in place and no value is returned
 *
 * @param array $vars associative array of variable name => type (type in (number,alpha,text,html))
 * @param array &$arr reference to associative array of variable name => value
 */

function MAPS_filterVars($vars, &$arr) {
    // cycle through each variable applying the appropriate filter
    foreach ($vars as $var => $type) {
        switch ($type) {
            case 'number':
                $arr[$var] = COM_applyFilter($arr[$var], true);
                break;

            case 'html':
                $arr[$var] = COM_checkHTML($arr[$var], 'maps.admin');
                break;

            case 'text':
                $arr[$var] = htmlspecialchars(strip_tags(addslashes($arr[$var])));
                break;

            case 'int':
                $arr[$var] = (int)$arr[$var];
                break;
            case 'alpha':
            default:
                $arr[$var] = COM_applyFilter($arr[$var], false);
                break;
        }
    }
}

function MAPS_geocoding()
{
    global $_CONF, $_MAPS_CONF, $_TABLES, $LANG_MAPS_1;

    $T = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
    $T->set_file('block', 'geocoder.thtml');
    $T->set_var('geocoder', $LANG_MAPS_1['geocoder']);
    $T->set_var('geocoder_text', $LANG_MAPS_1['geocoder_text']);
    $T->set_var('go', $LANG_MAPS_1['go']);
    $T->parse('output','block');
    $HTML = $T->finish($T->get_var('output'));
    
    return $HTML;
}

/*   Recurse through the maps table building an option list
*
*   @return string              HTML option list, without <select> tags
*/
function MAPS_recurseMaps($sel)
{
    global $_CONF, $_TABLES,$LANG_MAPS_1;

    $str = '';

    $sql = "
        SELECT
            mid, name
        FROM
            {$_TABLES['maps_maps']}
        WHERE
            1 = 1";

    $result = DB_query($sql);
    // If no map, just return.
    if (!$result)
        return '';

    while ($row = DB_fetchArray($result, false)) {
	    $markers = DB_count($_TABLES['maps_markers'],'mid',$row['mid']);
		($markers > 1) ? $unit = $LANG_MAPS_1['xmarkers'] : $unit = $LANG_MAPS_1['1marker'];
        $txt = $row['mid']. '. ' . stripslashes($row['name']) . ' ('. $markers . ' ' . $unit . ')';
        if ($row['mid'] == $sel) {
            $selected = 'selected="selected" ';
            $style = 'style="font-weight:bold; background:grey;"';
        } else {
            $selected = '';
            $style = '';
        }

        $str .= "<option value=\"{$row['mid']}\" $selected $style>";
        $str .= $txt;
        $str .= "</option>\n";

    }
    return $str;
}

// display a map

function MAPS_getMap ($mid, $zoom='', $address='')
{
    global $_CONF, $_TABLES, $_MAPS_CONF, $LANG_MAPS_1, $_SCRIPTS, $LANG_LOGIN, $_SCRIPTS;
    
    $retval = '';
    $res = DB_query("SELECT * FROM {$_TABLES['maps_maps']} WHERE mid = '{$mid}'");

    // count number of returned results, if unexpected redirect to maps list
    if (DB_numRows($res) != 1) {
        //echo COM_refresh($_MAPS_CONF['site_url'] . '/index.php');
		COM_errorLog('MAPS: Error when trying to display map number #' .  $mid . ' on page ' .  $_SERVER['HTTP_HOST'] );
        return;
    }
    
	//$_SCRIPTS->setJavaScriptFile('maps_iconmaker2', '/' . $_MAPS_CONF['maps_folder'] . '/js/mapiconmaker.js', false,false);
	
    $A = DB_fetchArray($res);
    foreach ($A as $i => $value) {
              $A[$i] = stripslashes($value);
            }

    if ((($A['active'] == 0)) && !SEC_hasRights('maps.admin')) {
        return $retval;
        exit();
    }
    if ($A['description'] != '') {
        $more_title = ' >> ' . $A['description'];
    } else {
        $more_title = '';
    }

    // Ensure user has the rights to access this map
    if ((!SEC_hasAccess($A['owner_id'], $A['group_id'], $A['perm_owner'],
                $A['perm_group'], $A['perm_members'], $A['perm_anon']) == 3) && COM_isAnonUser()) {
        $retval .= COM_startBlock ($LANG_LOGIN[1], '',
                                    COM_getBlockTemplate ('_msg_block', 'header'));

        $login = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
        $login->set_file (array ('login'=>'submitloginrequired.thtml'));
        $login->set_var ( 'xhtml', XHTML );
        $login->set_var ('site_url', $_CONF['site_url']);
        $login->set_var ('site_admin_url', $_CONF['site_admin_url']);
        $login->set_var ('layout_url', $_CONF['layout_url']);
        $login->set_var ('lang_login', $LANG_LOGIN[3]);
        $login->set_var ('lang_newuser', $LANG_LOGIN[4]);
        
        if ($A['perm_members'] == 2) {
            $login->set_var ('login_message', $LANG_LOGIN[2]);
        } else {
            $login->set_var ('login_message', $LANG_MAPS_1['must_belong_to'] . ' ' . SEC_getGroupDropdown($A['group_id'], $access = ''));
        }
        
        $login->parse ('output', 'login');
        $retval .= $login->finish ($login->get_var('output'));
        $retval .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
		return $retval;
    } elseif (!SEC_hasAccess($A['owner_id'], $A['group_id'], $A['perm_owner'],
                $A['perm_group'], $A['perm_members'], $A['perm_anon']) == 3) {
        $retval = COM_startBlock($LANG_MAPS_1['private_access'],'','blockheader-message.thtml');
        $retval .= $LANG_MAPS_1['must_belong_to'] . ' ' . SEC_getGroupDropdown($A['group_id'], $access = '');
        $retval .= COM_endBlock('blockfooter-message.thtml');
		return $retval;
    } else {
							
        $T = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
        $T->set_file('page', 'map.thtml');
        $T->set_var('mid', $A['mid']);
        $T->set_var('name', $A['name']);
        if ($A['description'] != '') {
            $T->set_var('description', '<p>' . stripslashes($A['description']) . '</p>');
        } else {
        $T->set_var('description', '');
        }
        if ($A['header'] != '') {
            $header = PLG_replaceTags($A['header']);
            $T->set_var('header', $header);;
        } else {
        $T->set_var('header', '');
        }
        if ($A['footer'] != '') {
            $footer = PLG_replaceTags($A['footer']);
            $T->set_var('footer', $footer);
        } else {
        $T->set_var('footer', '');
        }
		if ($address != '') {
		    // Updated to mysql 5.1
			MAPS_getDBCoords($gid, $address, $lat, $lng);
			$T->set_var('address', addslashes($address));
            $lat = str_replace(",",".",$lat);
            $lng = str_replace(",",".",$lng);
            $T->set_var('lat', $lat);
            $T->set_var('lng', $lng);
		} else {
            $T->set_var('address', addslashes($A['geo']));
            $lat = str_replace(",",".",$A['lat']);
            $lng = str_replace(",",".",$A['lng']);
            $T->set_var('lat', $lat);
            $T->set_var('lng', $lng);
		}
        $T->set_var('primaryColor', $A['primary_color']);
        $T->set_var('stroke_color', $A['stroke_color']);
        if ($A['label_color'] == '1') {
            $label_color = '#FFFFFF';
        } else {
            $label_color = '#000000';
        }

        $T->set_var('label_color', $label_color);
        $T->set_var('label', $A['label']);
		if ($zoom != '' && is_numeric($zoom)) { 
            $T->set_var('zoom', $zoom);
		} else {
		    $T->set_var('zoom', $A['zoom']);
			$zoom = $A['zoom'];
		}
        $T->set_var('goog_api_key', $_MAPS_CONF['google_api_key']);
        $T->set_var('map_type', $A['type']);
        $T->set_var('map_width', $A['width']);
        $T->set_var('map_height', $A['height']);
        
        $sql = "SELECT m.* 
                FROM {$_TABLES['maps_markers']} AS m
                WHERE m.mid = {$A['mid']} AND active = 1 AND hidden = 0
                ";

		//markers
		$jsmarkers = 'var markers = [];';
		$jsmarkers .= MAPS_getMarkers($sql, $A['primary_color'], $A['stroke_color'], $A['label'], $label_color, 0, $A['name'], $A['mid'], $A);
		if ($_MAPS_CONF['use_cluster'] == 0 ) {	
			$T->set_var('markerclusterer', '');	
		} else {
			$T->set_var('markerclusterer', "<script src=\"{$_MAPS_CONF['site_url']}/js/markerclusterer.js\" type=\"text/javascript\"></script>");
			$jsmarkers .= LB . '				var markerCluster' . $A['mid'] . ' = new MarkerClusterer(map' . $A['mid'] . ', markers);' . LB;
		}		
        
        $T->set_var('markers', $jsmarkers);
		
        //Ads			
		$ads = MAPS_getAds ($A['mid']);
		$T->set_var('ads', $ads);
		
		//overlays
		$o_sql = "SELECT DISTINCT
	            *
            FROM {$_TABLES['maps_map_overlay']} AS mo
			LEFT JOIN {$_TABLES['maps_overlays']} AS o
			ON mo.mo_oid = o.oid
			LEFT JOIN {$_TABLES['maps_overlays_groups']} AS g
			ON g.o_group_id = o.o_group
			WHERE mo.mo_mid={$mid}
			ORDER BY o.o_order
			";
	    //TODO order by group and order
		$o_res = DB_query($o_sql);
		$o_i = 1;
		$o_listener = LB . "google.maps.event.addListener(map{$mid}, 'zoom_changed', function() {" . LB ;
		$o_listener .= "var zoom = map{$mid}.getZoom();" . LB ;
		$overlays_checkboxes = '';
		$overlay_group = '';

		while ($overlay = DB_fetchArray($o_res)) {
			$overlays .= LB . "var imageBounds{$o_i} = new google.maps.LatLngBounds(" . LB;
			$overlays .= "new google.maps.LatLng({$overlay['o_sw_lat']},{$overlay['o_sw_lng']})," . LB;
			$overlays .= "new google.maps.LatLng({$overlay['o_ne_lat']},{$overlay['o_ne_lng']}));" . LB;
			
			$overlays .= "var overlay{$o_i} = new google.maps.GroundOverlay(" . LB;
			$overlays .= "\"{$_MAPS_CONF['images_overlay_url']}{$overlay['o_image']}\"," . LB;
			$overlays .= "imageBounds{$o_i});" . LB;
			$overlays .= "alloverlays[{$o_i}] = overlay{$o_i};" . LB;
			$overlays .= "zoommin[{$o_i}] = {$overlay['o_zoom_min']};" . LB;
			$overlays .= "zoommax[{$o_i}] = {$overlay['o_zoom_max']};" . LB;
			$overlays .= "var zoominit{$mid} = {$zoom};" . LB;
			$overlays .= "if ( zoominit{$mid} > ({$overlay['o_zoom_min']}-1) && zoominit{$mid} < ({$overlay['o_zoom_max']}+1) ) {" . LB;
			$overlays .= "    ToogleOverlays('overlay{$o_i}', {$o_i}, {$mid});" . LB;
			$overlays .= "}" . LB;
			
			$o_listener .= "    if ( zoom < {$overlay['o_zoom_min']} || zoom > {$overlay['o_zoom_max']} ) {" . LB;
			//$o_listener .= "        map{$mid}.removeOverlay(overlay{$o_i});" . LB;
			$o_listener .= "        overlay{$o_i}.setMap(null);" . LB;
			$o_listener .= "    }" . LB;
			
			$o_listener .= "    if ( zoom > ({$overlay['o_zoom_min']}-1) && zoom < ({$overlay['o_zoom_max']}+1)) {" . LB;
			$o_listener .= "    ToogleOverlays('overlay{$o_i}', {$o_i}, {$mid});" . LB;
			$o_listener .= "    }" . LB;
			
			//Overlays group
			if ( ($overlay['o_group_id'] != $overlay_group) && $overlay_group == '') {
			    $arr_it = 0;
				$overlay_group = $overlay['o_group_id'];
				$js .=  "var overlay_group_{$overlay_group} = new Array();" . LB;
				if ($overlay_group != 0) {
				    //parentCheckBox
					$overlays_checkboxes .= LB . '<fieldset>' . LB . '<input class="parentCheckBox" id="group_' . $overlay_group . '" onClick="ToogleOverlaysGroup(overlay_group_' 
					. $overlay_group . 	', ' . $overlay_group . ', ' . $mid . ');" checked="checked" type="checkbox" ' . XHTML . '> <strong>' 
					. $overlay['o_group_name'] . '</strong><br' . XHTML .'>' . LB;
				}
			} else if ($overlay['o_group_id'] != $overlay_group) {
			    $arr_it = 0;
			    $overlay_group = $overlay['o_group_id'];
				$js .=  "var overlay_group_{$overlay_group} = new Array();" . LB;
			    $overlays_checkboxes .= LB . '</fieldset>' . LB . '<fieldset>' . LB . '<input class="parentCheckBox" id="group_' . $overlay_group 
				. '" onClick="ToogleOverlaysGroup(overlay_group_'.  $overlay_group . ', ' . $overlay_group . ', ' 
				. $mid . ');" checked="checked" type="checkbox" ' . XHTML . '> <strong>' . $overlay['o_group_name'] . '</strong><br' . XHTML .'>';
			}
            
			//childCheckBox
			$overlays_checkboxes .= LB . '&nbsp;&nbsp;&nbsp;&nbsp;<input class="childCheckBox" name="overlay' . $overlay_group . '" id="overlay' . $o_i . '" onClick="ToogleOverlays(\'overlay'. $o_i .
    			'\', ' . $o_i . ', '. $mid . ')" checked="checked" type="checkbox" ' . XHTML . '> ' . COM_getTooltip($overlay['o_name'], '<img src="' . $_MAPS_CONF['site_url'] . '/timthumb.php?src='
				. $_MAPS_CONF['images_overlay_url'] . $overlay['o_image'] . '&amp;w=200&amp;q=70&amp;zc=1" alt="" />', '', $overlay['o_name'], $template = 'help') . '<br' . XHTML .'>' . LB;
			
			if ($overlay_group != '') $js .=  "overlay_group_{$overlay_group}[{$arr_it}] = {$o_i};" . LB;
			
			$arr_it++;
			$o_i++;
		}
		if ( $overlay_group != '') {
		    $overlays_checkboxes .= LB. '</fieldset>';
		}
		$o_listener .= "});" . LB . LB;
		
		//overlays checkboxes
		if ($overlays_checkboxes != '') {
			$overlays_checkboxes = '<p><a style="text-decoration: none;" onClick="elementToggle(\'overlays_checkboxes\'); return false;" href="#">
			<b>' . $LANG_MAPS_1['show_hide_overlays'] . '</b></a></p><div id="overlays_checkboxes" style="display:none;">' . LB . $overlays_checkboxes . LB . '</div>';
			
			$js .= LB . "function ToogleOverlays(ov, ovid, mapid) {
				var o_on = window.document.getElementById(ov);
				fmap = map[mapid];
				foverlay = alloverlays[ovid];
				zmin = zoommin[ovid];
				zmax = zoommax[ovid];
				var bounds =  fmap.getBounds();
				var zoom =  fmap.getZoom();
				if (o_on.checked == true) {
					if ( zoom > (zmin-1) && zoom < (zmax+1)) {
						//add overlay
						foverlay.setMap(fmap);
					}
				} else {
					//remove overlay
					foverlay.setMap(null);

				}
			}" . LB;
			
			$js .= LB . "function ToogleOverlaysGroup(ovg, ovgid, mapid) {
				var overlayGroupName = 'group_' + ovgid;
				var og_on = window.document.getElementById(overlayGroupName);
				fmap = map[mapid];
				var bounds =  fmap.getBounds();
				var zoom =  fmap.getZoom();
				if (og_on.checked == true) {
				    //For each overlay
                    for(var i= 0; i < ovg.length; i++)
                    {
						// alert(alloverlays[ovg[i]]);
						ovtarget = ovg[i]; 
						foverlay = alloverlays[ovtarget];
					    zmin = zoommin[ovtarget];
				        zmax = zoommax[ovtarget];
					    if ( zoom > (zmin-1) && zoom < (zmax+1)) {
						    //add overlay
							foverlay.setMap(fmap);
					    }
                    }
				} else {
				    //For each overlay
                    for(var i= 0; i < ovg.length; i++)
                    {
						ovtarget = ovg[i]; 
						foverlay = alloverlays[ovtarget];
                        //remove overlay
					    foverlay.setMap(null);

					}
				}
			}" . LB;
			
			//Thanks to http://jsfiddle.net/wfMXv/2/
            
			$jquery = "jQuery(document).ready(function(){
				jQuery(\":checkbox\").click(function(){
					var top_name = jQuery(this).attr(\"class\");
					var top_checked = jQuery(this).attr(\"checked\");
					jQuery(\":checkbox\").each(function(index){
						if (jQuery(this).attr(\"class\").match(\"^\" + top_name))
						{
							jQuery(this).attr(\"checked\", !jQuery(this).attr(\"checked\"));
						} else {
						    jQuery(this).attr(\"checked\");
						}
					});
				});
				});";
				
			$jquery = "    $(document).ready(
				function() {
				//clicking the parent checkbox should check or uncheck all child checkboxes
				$(\".parentCheckBox\").click(
				function() {
				$(this).parents('fieldset:eq(0)').find('.childCheckBox').attr('checked', this.checked);
				}
				);
				//clicking the last unchecked or checked checkbox should check or uncheck the parent checkbox
				$('.childCheckBox').click(
				function() {
				if ($(this).parents('fieldset:eq(0)').find('.parentCheckBox').attr('checked') == true && this.checked == false)
				$(this).parents('fieldset:eq(0)').find('.parentCheckBox').attr('checked', false);
				if (this.checked == true) {
				var flag = true;
				$(this).parents('fieldset:eq(0)').find('.childCheckBox').each(
				function() {
				if (this.checked == false)
				flag = false;
				}
				);
				$(this).parents('fieldset:eq(0)').find('.parentCheckBox').attr('checked', flag);
				}
				}
				);
				}
				);";
			
			$_SCRIPTS->setJavaScript($jquery, true);
			
			$T->set_var('overlays_js', $js);
			$T->set_var('overlays_checkboxes', $overlays_checkboxes);
		} else {
		    $T->set_var('overlays_checkboxes', '');
		}
		
		$T->set_var('overlays', $o_listener . $overlays);
		
        //Date and hits
        $update = COM_getUserDateTimeFormat($A['modified']);
        $T->set_var('date_and_hits', $LANG_MAPS_1['last_modification'] . ' ' . $update[0] . ' | ' . $A['hits'] . ' ' . $LANG_MAPS_1['hits']);

        if (SEC_hasRights('maps.admin')){
            $T->set_var('edit_button', '<form class="uk-form" id="edit_map" action="' . $_CONF['site_admin_url'] . '/plugins/maps/map_edit.php" method="POST">
            <div style="float:right"> <input type="image" src="' . $_CONF['site_admin_url'] . '/plugins/maps/images/edit.png" name="submit" align="absmiddle" /></div>
            <input type="hidden" name="mode" value="edit" /><input type="hidden" name="mid" value="' . $mid . '" />
        </form>');
        } else {
            $T->set_var('edit_button', '');
        }

        $T->parse('output','page');
        $retval = $T->finish($T->get_var('output'));
        
        hitMap($A['mid']);
        
        return $retval;
    }
}

function MAPS_getGlobalMap ( $zoom='', $address='', $pagelist=false ) {

    global $_TABLES, $LANG_MAPS_1, $_MAPS_CONF, $_CONF;
    
    if ($_MAPS_CONF['global_map'] == 1) {
        $res = DB_query("SELECT * FROM {$_TABLES['maps_maps']} WHERE 1=1 LIMIT 1");

        //Global map is set on mid=1. 
		//TODO change this
        $A = DB_fetchArray($res);

        $T = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
        $T->set_file('page', 'map.thtml');
        $T->set_var('mid', '0');
		if ($pagelist) {
            $T->set_var('name', '');
            $T->set_var('description', '');
		} else {
		    $T->set_var('name', $LANG_MAPS_1['global_map']);
            $T->set_var('description', '<p>' . $LANG_MAPS_1['info_global_map'] . '</p>');
		}
        $T->set_var('header', '');
        $T->set_var('footer', '');

        //address from global map
        $T->set_var('address', addslashes($A['geo']));
        $lat = str_replace(",",".",$A['lat']);
        $lng = str_replace(",",".",$A['lng']);
        $T->set_var('lat', $lat);
        $T->set_var('lng', $lng);
		
        if ($zoom == '' ) $T->set_var('zoom', $_MAPS_CONF['global_zoom']); else $T->set_var('zoom', $zoom);

        $T->set_var('map_type', $_MAPS_CONF['global_type']);
        $T->set_var('map_width', $_MAPS_CONF['global_width']);
        $T->set_var('map_height', $_MAPS_CONF['global_height']);
                
        $nRows  = DB_numRows($res);
        $jsmarkers = 'var markers = [];';
        
        //Global map
        
        $res_maps = DB_query("SELECT * FROM {$_TABLES['maps_maps']} WHERE 1=1 ORDER BY mid ASC");
        //Check if there at least 1 map
        $nRows_maps  = DB_numRows($res_maps);

		//markers
        
        //Select color and label for each marker	
        for ( $i=0; $i < $nRows_maps; $i++ ) {	
            $maps[$i] = DB_fetchArray($res_maps);
            if ((($A['active'] == 0)  || ($A['hidden'] == 1)) && !SEC_hasRights('maps.admin')) {
                continue;
            }
            $item = $maps[$i]['mid'];
            $primary_color = $maps[$i]['primary_color'];
            $stroke_color = $maps[$i]['stroke_color'];
            $marker_label = $maps[$i]['label'];
            if ($maps[$i]['label_color'] == 1) {
                $label_color = '#FFFFFF';
            } else {
                $label_color = '#000000';
            }
            $sql = "SELECT * FROM {$_TABLES['maps_markers']} WHERE mid=$item";
            $jsmarkers .= MAPS_getMarkers($sql,$primary_color,$stroke_color,$marker_label, $label_color, 1, $maps[$i]['name'],0, $maps[$i]);
        }
        
		if ($_MAPS_CONF['use_cluster'] == 0 ) {	
			$T->set_var('markerclusterer', '');	
		} else {
			$T->set_var('markerclusterer', "<script src=\"{$_MAPS_CONF['site_url']}/js/markerclusterer.js\" type=\"text/javascript\"></script>");
			$jsmarkers .= LB . '				var markerCluster0 = new MarkerClusterer(map0, markers);' . LB; 
		}
        
		$T->set_var('markers', $jsmarkers);
		
		//Ads			
		$ads = MAPS_getAds (0);
		$T->set_var('ads', $ads);

        $T->set_var('edit_button', '');

        $T->parse('output','page');
        $retval .= $T->finish($T->get_var('output'));
        
        hitMap(0);
        
        return $retval;
    } else {
        echo COM_refresh($_MAPS_CONF['site_url'] . '/index.php');
    }
    
}

function MAPS_getMarkers ($sql, $primaryColor, $strokecolor, $label, $label_color, $global, $mapname='', $mapid='', $map = array() )
{  
    global $_TABLES, $LANG_MAPS_1, $_MAPS_CONF, $_CONF;
	$map2  = $map;

    $markers =  '' . LB;
  
    if($sql != '') {
		$res_markers = DB_query($sql);
		
		$nRows  = DB_numRows($res_markers);

		for ( $i=0; $i < $nRows; $i++ ) {
			$marker = DB_fetchArray($res_markers);
			if (SEC_hasAccess2($marker)) {
				$markers .= MAPS_renderMarker($marker, $primaryColor, $strokecolor, $label, $label_color,
				$mapname, $marker_fields[$marker['mkid']], '', $mapid, true, $map2 );
			}
		}
	} else {
	
	}
      
  return $markers;
}

// $info display or not infowindow

function MAPS_renderMarker ($marker, $primaryColor, $strokecolor, $label, $label_color, $mapname='', $field_tab, $category='', $mapid=0, $infos=true, $map3)
{
    global $_CONF, $_MAPS_CONF, $LANG_MAPS_1, $_TABLES, $_USER;
	$use_icon = $map3['mmk_default']; // 0 false, 1 true
	$icon_id = $map3['mmk_icon'];

    //$category is not in use maps plugin v1.3
	
	$mkid = $marker['mkid'];
	
	if (!MAPS_checkMarkervalidity($marker)) return;
    
    if ( ($marker['hidden'] == 1 && !SEC_hasRights('maps.admin') || $marker['active'] == 0) ) return;
    if ( $marker['hidden'] == 1  && SEC_hasRights('maps.admin') ) $marker['name'] = $LANG_MAPS_1['marker_hidden'] . ' ' . $marker['name'];
    
	//custom marker priority on marker
    if ($marker['mk_default'] == 0) {
	    
		//custom icon
	    if ($marker['mk_icon'] > 0) {
		    // icon use an image
		    $icon = DB_getItem($_TABLES['maps_map_icons'],'icon_image',"icon_id={$marker['mk_icon']}");
			$image_path = $_MAPS_CONF['path_icons_images'] . $icon;
			list($width, $height, $type, $attr)= getimagesize($image_path); 
			if (file_exists($image_path) && $width != '' & $height != '') {
			    // Creating a new marker  
			    $markers .= LB . '				var marker' . $marker['mkid'] .' = new google.maps.Marker({
				     position: new google.maps.LatLng('. $marker['lat']. ', '. $marker['lng'] .'),
					 map:map' . $mapid . ',
					 title: "' .  $marker['name'] . '",
					 animation: google.maps.Animation.DROP,
					 icon: "' . $_MAPS_CONF['images_icons_url'] . $icon . '"
					 });' . LB ;
			} else {
			    $markers .= LB . '				var marker' . $marker['mkid'] .' = new google.maps.Marker({
				     position: new google.maps.LatLng('. $marker['lat']. ', '. $marker['lng'] .'),
					 map:map' . $mapid . ',
					 animation: google.maps.Animation.DROP,
					 title: "' .  $marker['name'] . '"
					 });' . LB ;
			}
        } else {
            //Use custom colors		
			$primaryColor = substr ( $marker['mk_pcolor'] ,1, 6 );
			$strokecolor = substr ( $marker['mk_scolor'] ,1, 6 );
			if ($marker['mk_label_color'] == 1) {
				$label_color = 'FFFFFF';
			} else {
				$label_color = '000000';
			}
			$label = $marker['mk_label'];

			// Creating a new marker  
			$markers .= LB . '				var marker' . $marker['mkid'] .' = new google.maps.Marker({
				     position: new google.maps.LatLng('. $marker['lat']. ', '. $marker['lng'] .'),
					 map:map' . $mapid . ',
					 title: "' .  $marker['name'] . '",
					 animation: google.maps.Animation.DROP,
					 icon: "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=' . $label . '|' . $primaryColor . '|' . $label_color . '"
					 });' . LB ; 
		}
    }  else if ($use_icon == '0' && $icon_id != '0') {
		// icon use an image
		$icon = DB_getItem($_TABLES['maps_map_icons'],'icon_image',"icon_id={$icon_id}");
		$image_path = $_MAPS_CONF['path_icons_images'] . $icon;
		list($width, $height, $type, $attr)= getimagesize($image_path); 
		if (file_exists($image_path) && $width != '' & $height != '') {
			// Creating a new marker  
			$markers .= LB . '				var marker' . $marker['mkid'] .' = new google.maps.Marker({
				 position: new google.maps.LatLng('. $marker['lat']. ', '. $marker['lng'] .'),
				 map:map' . $mapid . ',
				 title: "' .  $marker['name'] . '",
				 animation: google.maps.Animation.DROP,
				 icon: "' . $_MAPS_CONF['images_icons_url'] . $icon . '"
				 });' . LB ;
		} else {
			$markers .= LB . '				var marker' . $marker['mkid'] .' = new google.maps.Marker({
				 position: new google.maps.LatLng('. $marker['lat']. ', '. $marker['lng'] .'),
				 map:map' . $mapid . ',
				 animation: google.maps.Animation.DROP,
				 title: "' .  $marker['name'] . '"
				 });' . LB ;
		}
	}  else if ($use_icon == '0') {
		// icon use custom colors
		// Creating a new marker 		
		$primaryColor = substr ( $primaryColor ,1, 6 );
		$strokecolor = substr ( $strokecolor ,1, 6 );

		$label_color = substr ( $label_color ,1, 6 );

		//https://developers.google.com/chart/image/docs/gallery/dynamic_icons
		($primaryColor != '') ? $icon = LB . 'icon: "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=' . $label . '|' . $primaryColor . '|' . $label_color . '"' : $icon = ''; 
		
		$markers .= LB . '				var marker' . $marker['mkid'] .' = new google.maps.Marker({
				     position: new google.maps.LatLng('. $marker['lat']. ', '. $marker['lng'] .'),
					 map:map' . $mapid . ',
					 animation: google.maps.Animation.DROP,
					 title: "' .  $marker['name'] . '", ' . $icon . '
					 });' . LB ;
	} else {
		// Creating a new marker  
		$primaryColor = substr ( $primaryColor ,1, 6 );
		$strokecolor = substr ( $strokecolor ,1, 6 );

		$label_color = substr ( $label_color ,1, 6 );

		//https://developers.google.com/chart/image/docs/gallery/dynamic_icons
		($primaryColor != '') ? $icon = LB . 'icon: "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=' . $label . '|' . $primaryColor . '|' . $label_color . '"' : $icon = ''; 
		
		$markers .= LB . '				var marker' . $marker['mkid'] .' = new google.maps.Marker({
				     position: new google.maps.LatLng('. $marker['lat']. ', '. $marker['lng'] .'),
					 map:map' . $mapid . ',
					 animation: google.maps.Animation.DROP,
					 title: "' .  $marker['name'] . '", ' . $icon . '
					 });' . LB ;
    }
    
    // Content for infowindow
	if ( $mapid >= 0 && is_numeric($mapid) ) {
		$presentation = '';
		if (file_exists($_CONF['path_data'] . 'maps_data/templates/presentation_tab.thtml')) {
		    $template = COM_newTemplate($_CONF['path_data'] . 'maps_data/templates');
		} else {
    		$template = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
		}
		$template->set_file(array('presentation' => 'presentation_tab.thtml'));
		
		//Limited marker
		$limited = '';
		if ( $marker['validity'] == 1 && SEC_hasRights('maps.admin') && ( strtotime($marker['validity_end']) > time() || strtotime($marker['validity_start']) < time() ) ) $limited = ' <span style="color:red">Limited</span>';
		
		$template->set_var('name', '<strong><span style="text-transform:uppercase;">' . $marker['name'] . '</span></strong>' . $limited);
		
		//description
		(strlen($marker['description'])>150) ? $dots = '...' : $dots = '' ;
		($marker['description'] != '') ? $description = '<p>' . preg_replace( "/\r|\n/", "", nl2br(mb_substr ( $marker['description'] ,0, 150 ))) . $dots . '</p>' : '';
		$template->set_var('description', PLG_replaceTags(stripslashes($description)));
		
		//Ressources
		$ressources = '';
		$arr = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);

		foreach ($arr as $value) {
		    //For custom presentation_tab.thtml
			$template->set_var('item_'. $value . '_label', $_MAPS_CONF['item_'. $value]);
			$template->set_var('item_'. $value, PLG_replaceTags(stripslashes($marker['item_'. $value])));
			if ($_MAPS_CONF['item_'. $value] != '' && $marker['item_'. $value] != '') {
				$ressources .= '<h3>' . $_MAPS_CONF['item_'. $value] . '</h3>';
				$ressources .= '<p>' . PLG_replaceTags($marker['item_'. $value]) . '</p>';
			}
		}
		$template->set_var('ressources', $ressources);
	
		//Read more  
		$read_more_url = '';
		$lang_read_more = $LANG_MAPS_1['read_more'];

		if ($marker['url'] == '') {
		    $read_more_url = $_MAPS_CONF['site_url'] .'/markers.php?mode=show&mkid=' . $marker['mkid'] . '&mid=' . $marker['mid'];
		} else {
		    $read_more_url = $_CONF['site_url'] . '/' . $marker['url'];
		    $funcname =  'MAPS_ReadMore_' . $marker['type'];
			if (function_exists( $funcname ) ) $lang_read_more = $funcname(); 
		}
		
		$template->set_var('read_more', '<a href="' . $read_more_url . '">' . $lang_read_more . '</a></p>');

		//Map
		$mid = $marker['mid'];
		$template->set_var('map', '<p>' . $LANG_MAPS_1['on_map'] . ' <a href="'. $_MAPS_CONF['site_url'] .'/index.php?mode=map&mid=' . $marker['mid'] . '&name=' . stripslashes($mapname) . '&query_limit=500">' . stripslashes($mapname) . '</a></p>');

        //Edit link
		if (SEC_hasRights('maps.admin') || $_USER['uid'] == $marker['owner_id']) {
			$template->set_var('edit', ' <a href="'. $_MAPS_CONF['site_url'] . '/markers.php?mode=edit&amp;mkid=' . $marker['mkid'] . '">' . $LANG_MAPS_1['edit_button'] . '</a>');
		} else {
			$template->set_var('edit', '');
		}		

		$presentation .= $template->parse('output', 'presentation');
		
		$markers .= LB . '				var infowindow' . $marker['mkid'] . ' = new google.maps.InfoWindow({
			  content: \'' . addslashes($presentation) . '\'
		  });' . LB;
		
		// Adding a click-event to the marker  
		$markers .= LB . '				google.maps.event.addListener(marker' . $marker['mkid'] . ', \'click\', function() {
			infowindow' . $marker['mkid'] . '.open(map' . $mapid . ',marker' . $marker['mkid'] .');
		  });' . LB;

	} else {
	    if ( $mapid == 'events' &&  $infos ) 
		{
			//Display infoWindow for events map
			$presentation = '';
			$template = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
			$template->set_file(array('presentation_event' => 'presentation_tab_event.thtml'));
			
			$template->set_var('name', '<strong><span style="text-transform:uppercase;">' . $marker['name'] . '</span></strong>');
			
			//description
			($marker['description'] != '') ? $description = '<p>' . preg_replace( "/\r|\n/", "", nl2br(mb_substr ( $marker['description'] ,0, 150 ))) . '...<br' .  XHTML . '>' : '<p>';
			$template->set_var('description', addslashes($description));
			
			//Read more  
			$template->set_var('read_more', '<a href="'. $_CONF['site_url'] .'/calendar/event.php?eid=' . $marker['mkid'] . '">' . $LANG_MAPS_1['read_more'] . '</a></p>');

			//Map
			$template->set_var('map', '');	

			$presentation .= $template->parse('output', 'presentation_event');
			
			$markers .= LB . '				var infowindow' . $marker['mkid'] . ' = new google.maps.InfoWindow({
				  content: \'' . $presentation . '\'
			  });' . LB;
			
			// Adding a click-event to the marker  
			$markers .= LB . '				google.maps.event.addListener(marker' . $marker['mkid'] . ', \'click\', function() {
				infowindow' . $marker['mkid'] . '.open(map' . $mapid . ',marker' . $marker['mkid'] .');
			  });' . LB;
		}
		
	}
	
    // Add marker to map
    if ( $_MAPS_CONF['use_cluster'] == 1 ) {
        $markers .= '				    markers.push(marker' . $marker['mkid'] .');' . LB;
	}

    return $markers;
}

function MAPS_checkMarkervalidity($marker)
{
    if ( !SEC_hasAccess2($marker) ) return false;
	
	if ( $marker['validity'] == 1 && ! SEC_hasRights('maps.admin')) {
	    if ( strtotime($marker['validity_end']) > time() || strtotime($marker['validity_start']) < time() ) return false;
	}
	
	return true;
}

function MAPS_selectedMarkers($width, $height, $zoom, $mkids=array()) {
    
	global $_TABLES;
	
	//Center the map in the centre of the markers
	$allmkid = rtrim(implode(', ', $mkids), ', ');
	$sql = "SELECT mk.*, ma.mmk_default, ma.mmk_icon, ma.name as mapname, ma.primary_color, ma.stroke_color, ma.label, ma.label_color  FROM {$_TABLES['maps_markers']} AS mk
	    LEFT JOIN {$_TABLES['maps_maps']} AS ma ON mk.mid = ma.mid
	    WHERE mkid IN ({$allmkid})";
	$res = DB_query($sql);
	if (!$res) return '';
	while ($A = DB_fetchArray($res, false)) {
		$markers[] = $A;
		$lat = $lat + $A['lat'];
		$lng = $lng + $A['lng'];
		$i++;
	}
	$lat = $lat / $i;
	$lng = $lng / $i;

	return MAPS_getMarkerDetail (0, $markers, 1, $width, $height, $zoom, $lat, $lng);
}

// $mkid unique marker id or array of markers datas
function MAPS_getMarkerDetail ($mid=0, $mkid, $autotag=0, $mapwidth ='100%', $mapheight='300px', $mapzoom='10', $lat='0', $lng='0')
{
    global $_TABLES, $LANG_MAPS_1, $_MAPS_CONF, $_CONF;

    $retval = '';
	
	//One marker
	if ($mid > 0) {
		//Map info
		$res_map = DB_query("SELECT * FROM {$_TABLES['maps_maps']} WHERE mid=$mid LIMIT 1");
		
		if (DB_numRows($res_map) < 1) {
			echo COM_refresh($_MAPS_CONF['site_url'] . '/index.php');
			exit ();
		}
		
		$A = DB_fetchArray($res_map);
		
		// Ensure user has the rights to access this map
		if (!SEC_hasAccess2($A)) return;
		
		$primaryColor = $A['primary_color'];
		$strokecolor = $A['stroke_color'];
		if ($A['label_color'] == 1) {
			$label_color = '#FFFFFF';
		} else {
			$label_color = '#000000';
		}
		$label = $A['label'];
    
		//marker info
		$markers =  '' . LB;
		$sql = "SELECT * FROM {$_TABLES['maps_markers']} WHERE mkid=$mkid LIMIT 1";
		$res_markers = DB_query($sql);
		$marker = DB_fetchArray($res_markers);
		
		//check marker validity
		if( !MAPS_checkMarkervalidity($marker) ) return $LANG_MAPS_1['marker_limited'];

		if ( !defined('MAPS_PAGE_TITLE') ) define( 'MAPS_PAGE_TITLE', stripslashes($marker['name']));
		
		$markers = MAPS_renderMarker ($marker, $primaryColor, $strokecolor, $label, $label_color, $A['name'],
									$marker_fields[$mkid], '', $mkid, true, $A);

		$T = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
		
		if ($autotag==0) {
			$T->set_file('page', 'map.thtml');
		} else {
			$T->set_file('page', 'map_autotag.thtml');
		}
		$T->set_var('mid', $mkid);
		$T->set_var('name', '');
		$T->set_var('description', '');
		$T->set_var('header', '');
		$T->set_var('footer', '');

		//address from marker
		$T->set_var('address', addslashes($marker['address']));
		$lat = str_replace(",",".",$marker['lat']);
		$lng = str_replace(",",".",$marker['lng']);
		$T->set_var('lat', $lat);
		$T->set_var('lng', $lng);
		$T->set_var('zoom', $_MAPS_CONF['detail_zoom']);

		$T->set_var('map_type', $_MAPS_CONF['global_type']);
		$T->set_var('map_width', $_MAPS_CONF['global_width']);
		
		$T->set_var('map_height', $_MAPS_CONF['map_height_geotag']);
		
		//directions
		$gid = $marker['mkid'];
		$T->set_var('gid', $gid); 
		$value = htmlentities($LANG_MAPS_1['value_directions'], ENT_QUOTES);
		
		$T->set_var('directions_input', '<div id="map_directions">
			<small>&nbsp; ' . $LANG_MAPS_1['need_directions'] . '<br />
			<input type="text" id="from_address' . $gid . '" value="' . $value . '" onfocus="if (this.value==\'' . $value . '\')this.value=\'\';" onblur="if(this.value==\'\')this.value=\'' . $value . '\';" size="30" style="width:200px;"/>
			<input type="button" value="' . $LANG_MAPS_1['get_directions'] . '"  onClick="get_directions' . $gid .'();" />
			</small>
		  </div>');
		  
		$T->set_var('current_address', addslashes($marker['address'])); 
		
		//markers
		$jsmarkers = 'var markers = [];';
		$jsmarkers .= $markers;
		if ($_MAPS_CONF['use_cluster'] == 0 ) {	
				$T->set_var('markerclusterer', '');	
		} else {
				$T->set_var('markerclusterer', "<script src=\"{$_MAPS_CONF['site_url']}/js/markerclusterer.js\" type=\"text/javascript\"></script>");

				$jsmarkers .= LB . '				var markerCluster = new MarkerClusterer(map' . $mkid .  ', markers);' . LB;

		}
		
		$T->set_var('markers', $jsmarkers);
		
		//Ads			
		$ads = MAPS_getAds ($mkid);
		$T->set_var('ads', $ads);

		//Todo edit button
		$T->set_var('edit_button', '');

		$T->parse('output','page');
		$retval .= $T->finish($T->get_var('output'));
	} else {
	    //Selected markers
        $randid = rand();
		$markers =  '' . LB;
		
		foreach ($mkid as $mkinfos) {

			//check marker validity
			if( !MAPS_checkMarkervalidity($mkinfos) ) continue;

			if ( !defined('MAPS_PAGE_TITLE') ) define( 'MAPS_PAGE_TITLE', '');
			
			$primaryColor = $mkinfos['primary_color'];
			$strokecolor = $mkinfos['stroke_color'];
			if ($mkinfos['label_color'] == 1) {
				$label_color = '#FFFFFF';
			} else {
				$label_color = '#000000';
			}
			$label = $mkinfos['label'];
			
			$markers .= MAPS_renderMarker ($mkinfos, $primaryColor, $strokecolor, $label, $label_color, $mkinfos['mapname'], '', '', $randid, true, $mkinfos);
		}

		$T = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
		
		if ($autotag==0) {
			$T->set_file('page', 'map.thtml');
		} else {
			$T->set_file('page', 'map_autotag.thtml');
		}
		$T->set_var('mid', $randid);
		$T->set_var('name', '');
		$T->set_var('description', '');
		$T->set_var('header', '');
		$T->set_var('footer', '');
		$T->set_var('address', '');
		$T->set_var('lat', $lat);
		$T->set_var('lng', $lng);
		$T->set_var('zoom', $mapzoom);

		$T->set_var('map_type', $_MAPS_CONF['global_type']);
		$T->set_var('map_width', $mapwidth);
		
		$T->set_var('map_height', $mapheight);
		
		//directions
		$T->set_var('gid', ''); 
		$value = htmlentities($LANG_MAPS_1['value_directions'], ENT_QUOTES);
		
		$T->set_var('directions_input', '');
		  
		$T->set_var('current_address', ''); 
		
		//markers
		$jsmarkers = 'var markers = [];';
		$jsmarkers .= $markers;
		if ($_MAPS_CONF['use_cluster'] == 0 ) {	
				$T->set_var('markerclusterer', '');	
		} else {
				$T->set_var('markerclusterer', "<script src=\"{$_MAPS_CONF['site_url']}/js/markerclusterer.js\" type=\"text/javascript\"></script>");

				$jsmarkers .= LB . '				var markerCluster = new MarkerClusterer(map' . $randid .  ', markers);' . LB;

		}
		
		$T->set_var('markers', $jsmarkers);
		
		//Ads			
		$ads = MAPS_getAds ($randid);
		$T->set_var('ads', $ads);

		//Edit button
		$T->set_var('edit_button', '');

		$T->parse('output','page');
		$retval .= $T->finish($T->get_var('output'));
	}
    
    return $retval;
    
}

/**
*
* Checks that the current user has the rights to moderate the
* plugin, returns true if this is the case, false otherwise
*
* @return        boolean       Returns true if moderator
*
*/
function plugin_ismoderator_maps()
{
    return SEC_hasRights('maps.admin');
}

/**
*
* Counts the items that are submitted
*
*/
function plugin_submissioncount_maps()
{
    global $_TABLES;

    return DB_count($_TABLES['maps_submission']);
}

/**
* Shows marker submission form or diverts to marker editor if admin calls in
*
*/
function plugin_submit_maps($marker='')
{
    global $_CONF, $_MAPS_CONF, $LANG12, $LANG_MAPS_1, $LANG_LOGIN, $_SCRIPTS;

    if (SEC_hasRights('maps.admin')) {
	    echo COM_refresh ($_CONF['site_admin_url'] . '/plugins/maps/marker_edit.php');
        exit;
	}
	
	//set default for geocoder
	$marker['lat'] = '37.4217913';
	$marker['lng'] = '-122.08371390000002';
	
	COM_clearSpeedlimit ($_CONF['speedlimit'], 'submit');

    if ($_MAPS_CONF['marker_submission'] == 0) {
        return $LANG_MAPS_1['submission_disabled'];
    }
    
    // Ensure user has the rights to access this page
	// TODO use geeklog built-in login form
	
    if (COM_isAnonUser() && (($_CONF['loginrequired'] == 1) || ($_MAPS_CONF['maps_login_required'] == 1) || ($_MAPS_CONF['submit_login_required'] == 1))) {
        $display .= COM_siteHeader('');
		$display .= MAPS_user_menu();
        $display .= COM_startBlock ($LANG_LOGIN[1], '',
                                    COM_getBlockTemplate ('_msg_block', 'header'));
        $login = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
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

    $template = COM_newTemplate($_CONF['path'] . "plugins/maps/templates");
    $template->set_file(array('form' => 'marker_submit_form.thtml'));
    $template->set_var('site_url', $_CONF['site_url']);

    $template->set_var('go', $LANG_MAPS_1['go']);
    $template->set_var('submit_marker', $LANG_MAPS_1['submit_marker']);
    $template->set_var('submit_marker_text', $LANG_MAPS_1['submit_marker_text']);

    $template->set_var('lang_preview', $LANG12[32]);
    $template->set_var('lang_save', $LANG12[8]);
    
    $template->set_var('arrow', '<img src="' . $_MAPS_CONF['site_url'] . '/images/arrow.png" alt=""align="absmiddle">&nbsp;');
    
    //informations
    $template->set_var('informations', $LANG_MAPS_1['informations']);
    $template->set_var('name_label', $LANG_MAPS_1['marker_name_label']);
    $template->set_var('name', $marker['name']);
    $template->set_var('address_label', $LANG_MAPS_1['address_label']);
    $template->set_var('address', $marker['address']);
    $template->set_var('empty_for_geo', $LANG_MAPS_1['empty_for_geo']);
    $template->set_var('lat', $LANG_MAPS_1['lat']);
    $template->set_var('lat_value', $marker['lat']);
    $template->set_var('lng', $LANG_MAPS_1['lng']);
    $template->set_var('lng_value', $marker['lng']);
    $template->set_var('select_marker_map', $LANG_MAPS_1['select_marker_map']);
    $template->set_var('mid_label', $LANG_MAPS_1['name_label']);
    $template->set_var('mid', $marker['mid']);
    $map_options = MAPS_recurseMaps($marker['mid']);
    if ($map_options == '') {
        $display .= COM_startBlock($LANG_MAPS_1['error'],'','blockheader-message.thtml');
        $display .= $LANG_MAPS_1['maps_empty'];
        $display .= COM_endBlock('blockfooter-message.thtml');
        return ;
    }
    $template->set_var('map_options', $map_options);
    $template->set_var('created_label', $LANG_MAPS_1['marker_created']);
    $template->set_var('modified_label', $LANG_MAPS_1['modified']);
    $datecreated = COM_getUserDateTimeFormat($marker['created']);
    $datemodified = COM_getUserDateTimeFormat($marker['modified']);
    $template->set_var('created', $datecreated[0]);
    $template->set_var('modified', $datemodified[0]);
    $template->set_var('required_field', $LANG_MAPS_1['required_field']);

    //Tab presentation
    $template->set_var('presentation_tab', $LANG_MAPS_1['presentation_tab']);
    $template->set_var('description_label', $LANG_MAPS_1['description_label']);
    $template->set_var('description', $marker['description']);
    
    $template->set_var('street_label', $LANG_MAPS_1['street_label']);
    if ($_MAPS_CONF['street'] == 1) {
      $template->set_var('street', '<input type="text" name="street" value="' . $marker['street'] . '" size="80" maxlength="255">');
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
      $template->set_var('city', '<input type="text" name="city" value="' . $marker['city'] . '" size="80" maxlength="255">');
    } else {
      $template->set_var('city', $LANG_MAPS_1['not_use_see_config']);
    }
    
    $template->set_var('state_label', $LANG_MAPS_1['state_label']);
    if ($_MAPS_CONF['state'] == 1) {
      $template->set_var('state', '<input type="text" name="state" value="' . $marker['state'] . '" size="80" maxlength="255">');
    } else {
      $template->set_var('state', $LANG_MAPS_1['not_use_see_config']);
    }
    
    $template->set_var('country_label', $LANG_MAPS_1['country_label']);
    if ($_MAPS_CONF['country'] == 1) {
      $template->set_var('country', '<input type="text" name="country" value="' . $marker['country'] . '" size="80" maxlength="255">');
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
      $template->set_var('web', '<input type="text" name="web" value="' . $marker['web'] . '" size="80" maxlength="255">');
    } else {
      $template->set_var('web', $LANG_MAPS_1['not_use_see_config']);
    }

    //Tab ressources
    $template->set_var('ressources_tab', $LANG_MAPS_1['ressources_tab']);
    $template->set_var('max_char', $LANG_MAPS_1['max_char']);
    
    $arr = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
    $ressources ='';
    foreach ($arr as $value) {
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
    
    //Form validation
    $template->set_var('save_button', $LANG_MAPS_1['save_button']);
    $template->set_var('delete_button', $LANG_MAPS_1['delete_button']);
    $template->set_var('lang_save', $LANG12[8]);
    if (is_numeric($marker['mkid'])) {
        $template->set_var('mkid', '<input type="hidden" name="mkid" value="' . $marker['mkid'] .'" />');
    } else {
        $template->set_var('mkid', '');
    }
	
	$_SCRIPTS->setJavaScriptLibrary('jquery');
	
	$js = LB . '
    <script type="text/javascript">	
		
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

		function checkValidity()
		{
		  if (document.marker_edit.validity.value == 0){
			document.marker_edit.from.disabled = true;
			document.marker_edit.to.disabled = true;

		  }
		  else {
			document.marker_edit.from.disabled = false;
			document.marker_edit.to.disabled = false; 
		  }
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
	
    return $template->parse('output', 'form');
}

/**
* Check marker submission form for missing fields
* and Saves a marker submission
*
* @param    array   $A  Data for that submission
* @return   string      HTML redirect
*
*/
function plugin_savesubmission_maps($A)
{
    global $_TABLES, $_USER, $LANG_MAPS_1, $_MAPS_CONF;

    $retval = '';
    
    // check for missing fields
    if (empty($A['name']) || empty($A['address'])) {
        COM_resetSpeedlimit($type = 'submit');
        $retval = COM_siteHeader ('menu')
            . COM_startBlock($LANG_MAPS_1['error'],'','blockheader-message.thtml')
            . $LANG_MAPS_1['missing_field']
            . COM_endBlock('blockfooter-message.thtml')
            . plugin_submit_maps($A)
            . COM_siteFooter ();

        return $retval;
    }

    if (empty($_USER['uid'])) {
        $owner_id = 1;
    } else {
        $owner_id = $_USER['uid'];
    }

    if ($_MAPS_CONF['marker_submission'] == 1) {
        if ($A['from'] > $A['to']) {
            $display .= COM_startBlock($LANG_MAPS_1['error'],'','blockheader-message.thtml');
            $display .= $LANG_MAPS_1['date_issue'];
            $display .= COM_endBlock('blockfooter-message.thtml');
            $A['validity_start'] = $A['from'];
            $A['validity_end'] = $A['to'];
            $display .= getMarkerForm($A);
            break;
        }
        
        // prepare strings for insertion
        $A['created'] = date("YmdHis");
        $A['modified'] = date("YmdHis");

        $A['from'] = date("Ymd");
        $A['to'] = date("Ymd");
        
        // lat, lng can only contain numbers and a decimal
        if (empty($A['lat']) || empty($A['lng'])) {
            $address = $A['address'];
            // Updated to mysql 5.1
			// $coords = MAPS_getCoords($address, &$lat, &$lng);
			$coords = MAPS_getCoords($address, $lat, $lng);
        } else {
            $lat = addslashes ($A['lat']);
            $lng = addslashes ($A['lng']);
        }
        
        // Convert array values to numeric permission values
        if (is_array($A['perm_owner']) OR is_array($A['perm_group']) OR is_array($A['perm_members']) OR is_array($A['perm_anon'])) {
            list($A['perm_owner'],$A['perm_group'],$A['perm_members'],$A['perm_anon']) = SEC_getPermissionValues($A['perm_owner'],$A['perm_group'],$A['perm_members'],$A['perm_anon']);
        }
        $mkid = addslashes (COM_makeSid ());
        
        $A['name'] = addslashes ($A['name']);
        $A['description'] = addslashes ($A['description']);
        $A['address'] = addslashes ($A['address']);
        $A['mid'] = addslashes ($A['mid']);
        $A['remark'] = addslashes ($A['remark']);
        $A['street'] = addslashes ($A['street']);
        $A['city'] = addslashes ($A['city']);
        $A['code'] = addslashes ($A['code']);
        $A['state'] = addslashes ($A['state']);
        $A['country'] = addslashes ($A['country']);
        $A['tel'] = addslashes ($A['tel']);
        $A['fax'] = addslashes ($A['fax']);
        $A['web'] = addslashes ($A['web']);
        $A['item_1'] = addslashes ($A['item_1']);
        $A['item_2'] = addslashes ($A['item_2']);
        $A['item_3'] = addslashes ($A['item_3']);
        $A['item_4'] = addslashes ($A['item_4']);
        $A['item_5'] = addslashes ($A['item_5']);
        $A['item_6'] = addslashes ($A['item_6']);
        $A['item_7'] = addslashes ($A['item_7']);
        $A['item_8'] = addslashes ($A['item_8']);
        $A['item_9'] = addslashes ($A['item_9']);
        $A['item_10'] = addslashes ($A['item_10']);
        
        $sql = "'{$mkid}', '{$A['name']}', "
             . "'{$A['description']}', "
             . "'{$A['created']}', "
             . "'{$A['modified']}', "
             . "'0', "
             . "'0', "	
             . "'{$A['from']}', "
             . "'{$A['to']}', "			 
             . "'1', "
             . "'0', "
             . "'{$A['address']}', "
             . "'{$lat}', "
             . "'{$lng}', "
             . "'{$A['mid']}', "
             . "'{$A['remark']}', "
             . "'{$A['street']}', "
             . "'{$A['city']}', "
             . "'{$A['code']}', "
             . "'{$A['state']}', "
             . "'{$A['country']}', "
             . "'{$A['tel']}', "
             . "'{$A['fax']}', "
             . "'{$A['web']}', "
             . "'{$A['item_1']}', "
             . "'{$A['item_2']}', "
             . "'{$A['item_3']}', "
             . "'{$A['item_4']}', "
             . "'{$A['item_5']}', "
             . "'{$A['item_6']}', "
             . "'{$A['item_7']}', "
             . "'{$A['item_8']}', "
             . "'{$A['item_9']}', "
             . "'{$A['item_10']}', "
             . "'{$_USER['uid']}', "
             . "'{$A['group_id']}', "
             . "'{$_MAPS_CONF['default_permissions'][0]}', "
             . "'{$_MAPS_CONF['default_permissions'][1]}', "
             . "'{$_MAPS_CONF['default_permissions'][2]}', "
             . "'{$_MAPS_CONF['default_permissions'][3]}', "
             . "'1', "
			 . "'1'";
        
        DB_save ($_TABLES['maps_submission'],
                'mkid, name, description, created, modified, payed, validity, validity_start, validity_end, active, hidden, address, lat, lng, mid, remark, street, city, code, state, country, tel, fax, web, item_1, item_2, item_3, item_4, item_5, item_6, item_7, item_8, item_9, item_10, owner_id, group_id, perm_owner, perm_group, perm_members, perm_anon, submission, mk_default',
                "{$sql}");
        
        if (DB_error()) {
            return COM_refresh($_MAPS_CONF['site_url'] . '/index.php?msg=3');
        } else {
            //Send notification to admin		
            MAPS_sendNotification ($A);
            return COM_refresh($_MAPS_CONF['site_url'] . '/index.php?msg=1');
        }
    } else {
        return COM_refresh($_MAPS_CONF['site_url'] . '/index.php?msg=2');
    }
}


/**
* Send an email notification for a new submission.
*
* @param    array   $A      submission data
*
*/
function MAPS_sendNotification ($data, $modification = false)
{
    global $_CONF, $_MAPS_CONF, $LANG_MAPS_EMAIL, $LANG08, $_TABLES;

    $name = stripslashes ($data['name']);
    $description = stripslashes($data['description']);
    $map = DB_getItem($_TABLES['maps_maps'], 'name', "mid = '{$data['mid']}'");

    ($modification) ? $action = $LANG_MAPS_EMAIL['marker_modification'] : $action = $LANG_MAPS_EMAIL['new_marker'];
	$mailbody = "$action\n\n"
              . "{$LANG_MAPS_EMAIL['name']} $name\n"
              . "{$LANG_MAPS_EMAIL['description']} $description\n\n"
              . "{$LANG_MAPS_EMAIL['on_map']} $map\n\n";


    ($modification) ?  $mailbody .= $_MAPS_CONF['site_url'] . '/markers.php?mode=show&mkid=' . $data['mkid'] . '&mid=' . $data['mid'] : $mailbody .= "{$LANG_MAPS_EMAIL['submissions']} >> {$_CONF['site_admin_url']}/moderation.php>\n\n";

    ($modification) ?  $mailsubject = $_CONF['site_name'] . ' ' . $LANG_MAPS_EMAIL['marker_modification'] : $mailsubject = $_CONF['site_name'] . ' ' . $LANG_MAPS_EMAIL['marker_submissions'];

    $mailbody .= "\n------------------------------\n";
    $mailbody .= "\n$LANG08[34]\n";
    $mailbody .= "\n------------------------------\n";

    COM_mail ($_CONF['site_mail'], $mailsubject, $mailbody);
}

/**
* returns list of moderation values
*
* The array returned contains (in order): the row 'id' label, main plugin
* table, moderation fields (comma seperated), and plugin submission table
*
* @return       array        Returns array of useful moderation values
*
*/
function plugin_moderationvalues_maps()
{
    global $_TABLES;

    return array('mkid', $_TABLES['maps_markers'], 'mkid, name, address, lat, lng, mid, mk_default, payed, created, modified, validity, validity_start, validity_end, active, hidden, remark, description, street, code, city, state, country, tel, fax, web, item_1, item_2, item_3, item_4, item_5, item_6, item_7, item_8, item_9, item_10, hits, owner_id, group_id, perm_owner, perm_group, perm_members, perm_anon, submission', $_TABLES['maps_submission']);
}

/**
* Returns SQL & Language texts to moderation.php
*/
function plugin_itemlist_maps()
{
    global $_TABLES, $LANG_MAPS_1;

    if (plugin_ismoderator_maps()) {
        $plugin = new Plugin();
        $plugin->submissionlabel = $LANG_MAPS_1['markers_submissions'];
        $plugin->submissionhelpfile = 'http://geeklog.fr/wiki/plugins:maps#moderation';
        $plugin->getsubmissionssql = "SELECT mkid AS id, name, created, address FROM " 
                                   . $_TABLES['maps_submission'];
        $plugin->addSubmissionHeading($LANG_MAPS_1['name']);
        $plugin->addSubmissionHeading($LANG_MAPS_1['created']);
        $plugin->addSubmissionHeading($LANG_MAPS_1['address']);

        return $plugin;
    }
}

/**
* Performs plugin exclusive work for items approved by moderation
*
* While moderation.php handles the actual move from maps_submission
* to maps_markers tables, within the function we handle all other approval
* relate tasks
*
* @param      string       $id      Identifying string
* @return     string       Any wanted HTML output
*
*/

function plugin_moderationapprove_maps($id)
{
    global $_TABLES, $_USER, $_CONF;

    $result = DB_query("SELECT * FROM " . $_TABLES['groups'] 
                     . " WHERE grp_name = 'Maps Admin'");
    $group  = DB_fetchArray($result);
    
    $sql = "UPDATE " . $_TABLES['maps_markers'] . " SET active = 1, submission = 0, group_id = " . $group['grp_id'] . " WHERE mkid = '$id'";
    $result = DB_query($sql);

    if (DB_error()) {
       return 'Error';
    }
    return '';
}

/**
* Performs plugin exclusive work for items deleted by moderation
*
* While moderation.php handles the actual removal from <plugin>submission
* table, within this function we handle all other deletion
* relate tasks
*
* @param      string       $id      Identifying string
* @return     string       Any wanted HTML output
*
*/

function plugin_moderationdelete_maps($id)
{
    global $_TABLES;
}

function hitMap ($map)
{
    global $_TABLES;
    // increment hit counter for map
    if ($map == 0) {
        DB_query("UPDATE {$_TABLES['vars']} SET value=value+1 WHERE name = 'globalMapHits'");
    } else {
        DB_query("UPDATE {$_TABLES['maps_maps']} SET hits = hits + 1 WHERE mid = '$map'");
    }
}

function hitMarker ($mkid)
{
    global $_TABLES;
    
	// increment hit counter for marker
    DB_query("UPDATE {$_TABLES['maps_markers']} SET hits = hits + 1 WHERE mkid = '$mkid'");

}

function updateMap ($map)
{
    global $_TABLES;
    // update modification date for a map
    $date = date("YmdHis");
    DB_query("UPDATE {$_TABLES['maps_maps']} SET modified = $date WHERE mid = '$map'");
}

/**
* Returns the user option for this plugin
*
* Adds the plugin to the user menu
*
* Universal Plugin:  modify/uncomment use it
*
* @return   array   Array containing (plugin name, plugin user url, 0)
*
*/
function plugin_getuseroption_maps() 
{
    global $_MAPS_CONF, $LANG_MAPS_1;
 
    if ($_MAPS_CONF['marker_submission']==1) return array($LANG_MAPS_1['my_markers'], $_MAPS_CONF['site_url'] . '/markers.php', 0);
}

function MAPS_message ($message, $title='')
{
    global $LANG_MAPS_MESSAGE;
    
    $retval = '';
    if (!empty($message)) {
        $retval = COM_startBlock(($title == '')? $LANG_MAPS_MESSAGE['message'] : $title, '', 'blockheader-message.thtml');
        $retval .= stripslashes($message);
        $retval .= COM_endBlock('blockfooter-message.thtml');
    }
    return $retval;
}

function MAPS_ViewMarkerInfos ($mkid) 
{
    global $LANG_MAPS_1, $_TABLES, $_CONF, $_MAPS_CONF, $_USER;
    
    if ($mkid == '') return;
    
    $sql = "SELECT * FROM {$_TABLES['maps_markers']} WHERE mkid=$mkid LIMIT 1";
    $res_markers = DB_query($sql);
    $marker = DB_fetchArray($res_markers);
    
    if(!MAPS_checkMarkervalidity($marker)) return;
    
    // Presentation tab
    $presentation = '';
    $template = new Template($_CONF['path'] . 'plugins/maps/templates');
    $template->set_file(array('presentation' => 'marker.thtml'));
	
	//Limited marker
	$limited = '';
	if ( $marker['validity'] == 1 && SEC_hasRights('maps.admin') && ( strtotime($marker['validity_end']) > time() || strtotime($marker['validity_start']) < time() ) ) $limited = ' <span style="color:red">Limited</span>';
	
    $template->set_var('name', '<span style=\"text-transform:uppercase;\">' . stripslashes($marker['name']) . '</span> ' . $limited);
    
	//ressources tab
    $ressources = '';
    $arr = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
        
    //payed marker
    if ( $_MAPS_CONF['monetize'] == 0 ) {
        if ($marker['description'] != '') {
        $description = nl2br(stripslashes($marker['description']));
        $description = str_replace("\n", "", $description);
        $description = str_replace("\r", "", $description);
        $template->set_var('description', '<p>' . $description . '</p>');
        } else {
        $template->set_var('description', '');
        }

        if ($_MAPS_CONF['street'] == 1 && $marker['street'] != '') {
          $template->set_var('street',  '<p><strong>' . $LANG_MAPS_1['street_label'] . '</strong> ' . stripslashes($marker['street']) . '</p>');
        } else {
          $template->set_var('street', '');
        }
        
        if ($_MAPS_CONF['code'] == 1 && $marker['code'] != '') {
          $template->set_var('code',  '<strong>' . $LANG_MAPS_1['code_label'] . '</strong> ' . $marker['code']);
        } else {
          $template->set_var('code', '');
        }
        
        if ($_MAPS_CONF['city'] == 1 && $marker['city'] != '') {
          $template->set_var('city',  '<strong>' . $LANG_MAPS_1['city_label'] . '</strong> ' . stripslashes($marker['city']));
        } else {
          $template->set_var('city', '');
        }
        
        if ($_MAPS_CONF['state'] == 1 && $marker['state'] != '') {
          $template->set_var('state',  '<p><strong>' . $LANG_MAPS_1['state_label'] . '</strong> ' . $marker['state'] . '</p>');
        } else {
          $template->set_var('state', '');
        }
        
        if ($_MAPS_CONF['country'] == 1 && $marker['country'] != '') {
          $template->set_var('country',  '<p><strong>' . $LANG_MAPS_1['country_label'] . '</strong> ' . stripslashes($marker['country']) . '</p>');
        } else {
          $template->set_var('country', '');
        }
        
        if ($_MAPS_CONF['tel'] == 1 && $marker['tel'] != '') {
          $template->set_var('tel', '<p><strong>' . $LANG_MAPS_1['tel_label'] . '</strong> ' . $marker['tel'] . '</p>');
        } else {
          $template->set_var('tel', '');
        }
        
        if ($_MAPS_CONF['fax'] == 1 && $marker['fax'] != '') {
          $template->set_var('fax', '<p><strong>' . $LANG_MAPS_1['fax_label'] . '</strong> ' . $marker['fax'] . '</p>');
        } else {
          $template->set_var('fax', '');
        }
        
        if ($_MAPS_CONF['web'] == 1 && $marker['web'] != '') {
          $template->set_var('web', '<p><strong>' . $LANG_MAPS_1['web_label'] . '</strong> ' . MAPS_convertLinkToUrl($marker['web']) . '</p>');
        } else {
          $template->set_var('web', '');
        }
        
        //Ressources tab
        foreach ($arr as &$value) {
            if ($_MAPS_CONF['item_'. $value] == '') {
                $ressources .= '';
            } else {
                if ($marker['item_'. $value] != ''){
                    $content = str_replace(array("\r\n", "\n", "\r", "  "), '', addslashes($marker['item_'. $value]));
                    $ressources .= '<p><strong>' . $_MAPS_CONF['item_'. $value] . '</strong></p><p>' . stripslashes($content) . '</p>';
                } 
            }
        }
    } elseif (($marker['payed'] == 1 ) && ($_MAPS_CONF['monetize'] == 1)) {
        if ($marker['description'] != '') {
        $template->set_var('description', '<p>' . stripslashes($marker['description']) . '</p>');
        } else {
        $template->set_var('description', '');
        }
        if ($_MAPS_CONF['street'] == 1 && $marker['street'] != '') {
          $template->set_var('street', stripslashes($marker['street']) . '&nbsp;');
        } else {
          $template->set_var('street', '');
        }
        if ($_MAPS_CONF['code'] == 1 && $marker['code'] != '') {
          $template->set_var('code', $marker['code'] . '&nbsp;');
        } else {
          $template->set_var('code', '');
        }
        if ($_MAPS_CONF['city'] == 1 && $marker['city'] != '') {
          $template->set_var('city', stripslashes($marker['city']) . '&nbsp;');
        } else {
          $template->set_var('city', '');
        }
        if ($_MAPS_CONF['state'] == 1 && $marker['state'] != '') {
          $template->set_var('state', $marker['state'] . '&nbsp;');
        } else {
          $template->set_var('state', '');
        }
        if ($_MAPS_CONF['country'] == 1 && $marker['country'] != '') {
          $template->set_var('country', stripslashes($marker['country']) . '&nbsp;');
        } else {
          $template->set_var('country', '');
        }
        if ($count == 0) {
          $template->set_var('space', '');
        } else {
          $template->set_var('space', '<br'. XHTML . '>' . '<br'. XHTML . '>');
        }
        if ($_MAPS_CONF['tel'] == 1 && $marker['tel'] != '') {
          $template->set_var('tel', '<p>' . $LANG_MAPS_1['tel_label'] . ' ' . $marker['tel'] . '</p>');
        } else {
          $template->set_var('tel', '');
        }
        if ($_MAPS_CONF['fax'] == 1 && $marker['fax'] != '') {
          $template->set_var('fax', '<p>' . $LANG_MAPS_1['fax_label'] . ' ' . $marker['fax'] . '</p>');
        } else {
          $template->set_var('fax', '');
        }
        if ($_MAPS_CONF['web'] == 1 && $marker['web'] != '') {
          $template->set_var('web', '<p>' . $LANG_MAPS_1['web_label'] . ' ' . $marker['web'] . '</p>');
        } else {
          $template->set_var('web', '');
        }
        //Ressources tab
        foreach ($arr as &$value) {
            if ($_MAPS_CONF['item_'. $value] == '') {
                $ressources .= '';
            } else {
                if ($marker['item_'. $value] != '') {
                    $content = str_replace(array("\r\n", "\n", "\r", "  "), '', addslashes($marker['item_'. $value]));
                    $ressources .= '<p><strong>' . $_MAPS_CONF['item_'. $value] . '</strong></p><p>' . stripslashes($content) . '</p>';
                } 
            }
        }
    } else {
        if ($marker['description'] != '') {
        $template->set_var('description', '<p>' . stripslashes($marker['description']) . '</p>');
        } else {
            $template->set_var('description', '');
        }
        $template->set_var('street', '');
        $template->set_var('code', '');
        $template->set_var('city', '');
        $template->set_var('state', '');
        $template->set_var('country', '');
        $template->set_var('space', '');
        $template->set_var('tel', '');
        $template->set_var('fax', '');
        $template->set_var('web', '');
        //Ressources tab
        $ressources = '';
    }
    
    $template->set_var('ressources', $ressources);
    
    //Edit link
    if (SEC_hasRights('maps.admin') || $_USER['uid'] == $marker['owner_id']) {
        $template->set_var('edit', ' | <a href="'. $_MAPS_CONF['site_url'] . '/markers.php?mode=edit&amp;mkid=' . $marker['mkid'] . '">' . $LANG_MAPS_1['edit_button'] . '</a>');
    } else {
        $template->set_var('edit', '');
    }

    //Map
    $mapname = DB_getItem($_TABLES['maps_maps'], 'name', "mid={$marker['mid']}");
    $template->set_var('map', ' | ' . $LANG_MAPS_1['from_map'] . ' <a href="' . $_MAPS_CONF['site_url'] . '/index.php?mode=map&amp;mid=' . $marker['mid'] . '&name=' . urlencode($mapname) . '">' . stripslashes($mapname) . '</a>');	
	
	//Owner
	$profile_url = $_CONF['site_url']
                     . '/users.php?mode=profile&amp;uid=' . $marker['owner_id'];
    $profile = COM_createLink(COM_getDisplayName($marker['owner_id']), $profile_url);
	$template->set_var('owner', $LANG_MAPS_1['from_owner'] . ' ' . $profile);
    
    //Update
    $update = COM_getUserDateTimeFormat($marker['modified']);
    $template->set_var('update', '<br' . XHTML . '>' . $LANG_MAPS_1['last_modification'] . ' ' . $update[0] . ' ' . ' | ' . $marker['hits'] . ' ' . $LANG_MAPS_1['hits']);
    
    //Report
    $template->set_var('report', '<a href="'. $_CONF['site_url'] .'/profiles.php?uid=2&amp;subject=' . $LANG_MAPS_1['report_subject'] . $marker['mkid'] . '">' .$LANG_MAPS_1['report'] . '</a>');
	
	//Print
    $template->set_var('print', '<a href="'. $_MAPS_CONF['site_url'] . '/markers.php?mode=print&amp;mid=' . $marker['mid'] . '&amp;mkid=' . $marker['mkid'] . '">' .$LANG_MAPS_1['print'] . '</a> | ');
	
	//directions
	$gid = $marker['mkid'];
	$template->set_var('directions_table_result', '<a name="directions_' . $gid .'_table" id="directions_' . $gid .'_table"></a>
	 <div id="directions' . $gid .'"></div>');

    $presentation .= $template->parse('output', 'presentation');
    $retval .= $presentation;
	
	hitMarker ($mkid);
    
	return $retval;
}

function MAPS_convertLinkToUrl ($url, $lenght=35) {	
	
	global $_CONF;
	
	if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $newurl = "http://" . $url;
    } else {
	    $newurl = $url;
	}
	
	//name
	$parse = parse_url($newurl);
	($parse['host'] != '' ) ? $name = $parse['host'] : $name = $url;
	
	//Target
	(strpos($newurl,$_CONF['site_url']) !== false) ? $target = '' : $target = '_blank';

	// convert link to url
    $link = preg_replace("/((http:\/\/|https:\/\/)[^ |<)
    ]+)/e", "'<a href=\"$1\" target=\"$target\">'. ((strlen(\"$name\")>=" . $lenght . " ? substr(\"$name\",0," . $lenght . ").'... ':\"$name\")).'</a> '", $newurl );
	
	return $link ;	
}

/**
* List all markers that the user has access to
*
* @retun    string      HTML for the list
*
*/
function MAPS_ListMarkers($mid = '')
{
    global $_CONF, $_MAPS_CONF, $_TABLES, $_IMAGE_TYPE, $LANG_ADMIN, $LANG_MAPS_1;

    if ($mid == '') return;
    $mid = addslashes($mid);
    
    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';
    
    if (DB_count($_TABLES['maps_markers']) == 0){
    return $retval = '';
    }

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_MAPS_1['name'], 'field' => 'name', 'sort' => true),
        array('text' => $LANG_MAPS_1['code'], 'field' => 'code', 'sort' => true),
        array('text' => $LANG_MAPS_1['city'], 'field' => 'city', 'sort' => true)
    );
    $defsort_arr = array('field' => 'code', 'direction' => 'asc');

    $text_arr = array(
        'has_extras' => true,
        'form_url' => $_MAPS_CONF['site_url'] . '/index.php?mode=markers&mid=' . $mid
    );
    
	if ($mid == 0) {
		$sql = "SELECT
                *
            FROM {$_TABLES['maps_markers']}
            WHERE active = 1 AND hidden = 0
            ";
		$map_name = $LANG_MAPS_1['global_map'];
	} else {
		$sql = "SELECT
                *
            FROM {$_TABLES['maps_markers']}
            WHERE mid = {$mid} AND active = 1 AND hidden = 0
            ";
		$map_name = DB_getItem($_TABLES['maps_maps'],'name', "mid = {$mid}");
	}

    $query_arr = array(
        'table'          => 'maps_markers',
        'sql'            => $sql,
        'query_fields'   => array('name','code','city'),
        'default_filter' => COM_getPermSQL ('AND', 0, 2)
    );

    $retval .= '<p><a style="text-decoration: none;" onClick="elementToggle(\'markers_list\'); return false;" href="#">
			' . $LANG_MAPS_1['viewing_markers'] . ' ' . $map_name .  '</a></p>';
    $retval .= '<div id="markers_list" style="display:none;">' . ADMIN_list('markers', 'MAPS_getListField_markersList',
                          $header_arr, $text_arr, $query_arr, $defsort_arr) . '</div>';

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
function MAPS_getListField_markersList($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $_MAPS_CONF, $LANG_ADMIN, $LANG_MAPS_1, $LANG_STATIC, $_TABLES;

    switch($fieldname) {
        case "name":
            $map_title = ucwords(strtolower(stripslashes ($A['name'])));
            $url = $_MAPS_CONF['site_url'] .
                                 '/markers.php?mode=show&amp;mkid=' . $A['mkid'] . '&amp;mid=' . $A['mid'];
            $retval = COM_createLink($map_title, $url);
            break;
		case "city":
            $city = ucwords(strtolower(stripslashes ($A['city'])));
            $retval = $city;
            break;
        default:
            $retval = stripslashes($fieldvalue);
            break;
    }
    return $retval;
}

function MAPS_setGeoLocation ()
{
    global $_CONF, $_TABLES, $_MAPS_CONF;
	
	$sql = "SELECT info.location 
			FROM {$_TABLES['userinfo']} AS info 
			WHERE info.location <> ''
		";
		
	$locations = DB_query($sql);
		
	$nRows  = DB_numRows($locations);
	
    for ( $i=0; $i < $nRows; $i++ ) {
        $geo = DB_fetchArray($locations);
		MAPS_getDBCoords($gid, $geo['location'], $lat, $lng) ;
	}
	
	return;
}

function MAPS_displayEventsMap ($output=true, $event_id='')
{
	global $_TABLES, $_CONF, $_MAPS_CONF, $LANG_MAPS_1, $LANG_CAL_1;

    $retval = '';
	
	//map settings
	$res = DB_query("SELECT * FROM {$_TABLES['maps_maps']} WHERE 1=1 LIMIT 1");

	//event map is set on mid=1. 
	//TODO change this
	$A = DB_fetchArray($res);

	$T = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
	if ($output) {
    	$T->set_file('page', 'map_autotag.thtml');
	} else {
	    $T->set_file('page', 'map_only_script.thtml');
	}
	$T->set_var('mid', 'events');
	$T->set_var('name', $LANG_MAPS_1['events_map']);
	$T->set_var('description', '<p>' . $LANG_MAPS_1['info_events_map'] . '</p>');
	
	$T->set_var('header', '');
	$T->set_var('footer', '');

	//address from global map
	$T->set_var('address', addslashes($A['geo']));
	$lat = str_replace(",",".",$A['lat']);
	$lng = str_replace(",",".",$A['lng']);

	$T->set_var('map_type', $_MAPS_CONF['global_type']);
	$T->set_var('map_width', $_MAPS_CONF['global_width']);
	
			
	$jsmarkers = 'var markers = [];';

	if ($event_id == '') {
	    if ($lat == '') $lat = 0;
	    if ($lng == '') $lng = 0;
    	$T->set_var('lat', $lat);
	    $T->set_var('lng', $lng);
        $T->set_var('zoom', $_MAPS_CONF['global_zoom']);
		$T->set_var('map_height', $_MAPS_CONF['global_height']);
		
		//select all coming events
	    $result = DB_query("SELECT * FROM {$_TABLES['events']} WHERE TO_DAYS(dateend) >= TO_DAYS(NOW()) ");
	} else {
	    //TODO make this configurable

        $T->set_var('zoom', 8);
	    $T->set_var('map_height', '300px');
		
		$result = DB_query("SELECT * FROM {$_TABLES['events']} WHERE eid='$event_id'");
	}

	$nrows = DB_numRows($result);
	
	if ($nrows > 0) {
		
		for ($i = 0;$i < $nrows; $i++) {
			
			$B = DB_fetchArray($result);
			
			$access = SEC_hasAccess($B['owner_id'],$B['group_id'],$B['perm_owner'],$B['perm_group'],$B['perm_members'],$B['perm_anon']);
			
			if ($access > 0 && $B['location']!='') {
				
				//markers
				$marker['mkid'] = $B['eid'];
				$marker['hidden'] = 0;
				$marker['active'] = 1;
				$marker['name'] = $B['title'];
				
				//start and end time
				$thestart = COM_getUserDateTimeFormat($B['datestart'] . ' ' . $B['timestart']);
                $theend = COM_getUserDateTimeFormat($B['dateend'] . ' ' . $B['timeend']);
				if ($B['allday'] == 0) {
					$event_start = $LANG_MAPS_1['from_cal'] . ' ' . $thestart[0];
					$event_end = ' ' . $LANG_MAPS_1['to_cal'] . ' ' . $theend[0];
					if ($thestart[0] == $theend[0]) $event_end = '';
				} else {
					$event_start = $LANG_MAPS_1['from_cal'] . ' ' . strftime($_CONF['shortdate'], $thestart[1]);
    				$event_end = ' ' . $LANG_MAPS_1['to_cal'] . ' ' . strftime($_CONF['shortdate'], $theend[1]);
					if ($thestart[1] == $theend[1]) {
					    $event_start = $LANG_MAPS_1['from_cal'] . ' ' . strftime($_CONF['shortdate'], $thestart[1]);
						$event_end = '';
					}
				}
				
				if ($event_id == '') $marker['description'] = '<p><small>' . $event_start . $event_end . '</small></p>' . $B['description'];
				
				$marker['payed'] = 0;
				$marker['mk_default'] = 1;
				$marker['validity'] = 0;
				$marker['perm_owner'] = 2;
				$marker['perm_group'] = 2;
				$marker['perm_members'] = 2;
				$marker['perm_anon'] = 2;
				
				MAPS_getDBCoords($gid, $B['location'], $lat, $lng) ;
				$marker['lat'] = str_replace(",",".",$lat);
                $marker['lng'] = str_replace(",",".",$lng);
					
				$primary_color = '';
				$stroke_color = '';
				$marker_label = '';
				$label_color = '';

				if ($event_id == '') {
				    $jsmarkers .= MAPS_renderMarker($marker,$primary_color,$stroke_color,$marker_label, $label_color, $LANG_MAPS_1['events_map'],'','','events', true, array());			
				} else {
				    $T->set_var('lat', $marker['lat']);
	                $T->set_var('lng', $marker['lng']);
				    $jsmarkers .= MAPS_renderMarker($marker,$primary_color,$stroke_color,$marker_label, $label_color, $LANG_MAPS_1['events_map'],'','','events', false, array());	
				}
					
			}
		}
	}
	
	if ($_MAPS_CONF['use_cluster'] == 0 ) {	
		$T->set_var('markerclusterer', '');	
	} else {
		$T->set_var('markerclusterer', "<script src=\"{$_MAPS_CONF['site_url']}/js/markerclusterer.js\" type=\"text/javascript\"></script>");
		$jsmarkers .= LB . '				var markerCluster_events = new MarkerClusterer(mapevents, markers);' . LB; 
	}
	
	$T->set_var('markers', $jsmarkers);
	
	//Ads			
	$ads = MAPS_getAds ('events');
	$T->set_var('ads', $ads);

	$T->set_var('edit_button', '');

	$T->parse('output','page');
	$retval .= $T->finish($T->get_var('output'));
	
    return $retval;
}

?>