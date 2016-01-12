<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.4                                                           |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// |                                                                           |
// | Public plugin page                                                        |
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
* @package Maps
*/

require_once '../lib-common.php';

// take user back to the homepage if the plugin is not active
if (!in_array('maps', $_PLUGINS)) {
    echo COM_refresh($_CONF['site_url'] . '/index.php');
    exit;
}

// Incoming variable filter
$vars = array('mid' => 'int',
              'mkid' => 'number',
			  'mode' => 'alpha'
            );
MAPS_filterVars($vars, $_REQUEST);

$default = false;

// Ensure user has the rights to access this page

if (COM_isAnonUser() && (($_CONF['loginrequired'] == 1) || ($_MAPS_CONF['maps_login_required'] == 1)) && isset($_REQUEST['mode'])) {
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

function MAPS_displayFrontPage ()
{
	global $_CONF, $_MAPS_CONF, $LANG_MAPS_1, $_TABLES;
	
	$retval ='';
	
	if ($_MAPS_CONF['map_main_header'] != '') {
		$header = '<div>'. PLG_replaceTags($_MAPS_CONF['map_main_header']) .'</div>';
	} else {
		$header1 = '<p style="margin-top:25px;">' . $LANG_MAPS_1['user_maps_list'] .'</p>';
	}

	// Get maps from database
	$sql = "SELECT mid, name, description, active, hidden, modified, hits FROM {$_TABLES['maps_maps']} ORDER BY name ASC";

	$res = DB_query($sql);

	// Create maps list template
	$map = new Template($_CONF['path'] . 'plugins/maps/templates');
	$map->set_file(array('map' => 'list_map_item.thtml',
						 'start'   => 'list_map_start.thtml',
						 'end'     => 'list_map_end.thtml') );

	// Display the begging of the map list
	$retval .= $map->parse('output', 'start');
		
	$list = 0;
	$lastmod = array();
	$markerssum = 0;

	while ($A = DB_fetchArray($res)) {
	    if (($A['active'] == 1)  && ($A['hidden'] == 0)) {
		    $map->set_var('mid', $A['mid']);
		    $map->set_var('name', stripslashes($A['name']));
		    $map->set_var('xhtml', XHTML);		    
			$name = urlencode($A['name']);
		    $map->set_var('map_detail',  $_MAPS_CONF['site_url']  .'/index.php?mode=map&amp;mid=' . $A['mid'] . '&name=' . $name . '&amp;query_limit=500');
		   
 		   if ($A['description'] != '') {
		        $map->set_var('description', '<br' . XHTML . '>' . stripslashes($A['description']));
		    } else {
		        $map->set_var('description', '');
		    }
		    
			//See map and markers
			if (function_exists('MAPS_getFields')) {
			    $map->set_var('view_map', '<a href="' . $_MAPS_CONF['site_url']  .'/index.php?mode=map&amp;mid=' . $A['mid'] . '&name=' . $name . '&amp;query_limit=500">' . $LANG_MAPS_1['view_map'] . '</a> | ');
			    $map->set_var('view_markers', '<a href="' . $_MAPS_CONF['site_url']  .'/index.php?mode=markers&amp;mid=' . $A['mid'] . '&name=' . $name . '">' . $LANG_MAPS_1['view_markers'] . ' | </a>');
			} else {
			    $map->set_var('view_map', '');
			    $map->set_var('view_markers', '');
			}
			
			//update
		    $currentmod = COM_getUserDateTimeFormat($A['modified']);
		    if ($currentmod[1] > $lastmod[1]) {
		        $lastmod = COM_getUserDateTimeFormat($A['modified']);
		    }
		    $update = COM_getUserDateTimeFormat($A['modified']);
		    $map->set_var('update', $LANG_MAPS_1['last_modification'] . ' ' . $update[0]);
		    
			//markers
		    $markers = DB_count($_TABLES['maps_markers'],'mid',$A['mid']);
		    $markerssum = $markerssum + $markers;
		    $map->set_var('markers',  ' | ' . $markers . ' ' . $LANG_MAPS_1['records']);
		    
			//hits
		    $map->set_var('hits',  ' | ' . $A['hits'] . ' ' . $LANG_MAPS_1['hits']);
				if (SEC_hasRights('maps.admin')){
	        $map->set_var('edit_button', '<form class="uk-form" id="edit_map" action="' . $_CONF['site_admin_url'] . '/plugins/maps/map_edit.php" method="POST">
	        <div style="float:right">
	          <input type="image" src="' . $_CONF['site_admin_url'] . '/plugins/maps/images/edit.png" align="absmiddle" />
			  <input type="hidden" name="mode" value="edit" />
			   <input type="hidden" name="mid" value="' . $A['mid'] . '" />
	        </div>
	        </form>');
		} else {
			$map->set_var('edit_button', '');
		}
		$retval .= $map->parse('output', 'map');
		$list ++;
	    }
	}

	if (($list == 0) && ($_MAPS_CONF['global_map'] == 0) && ($_MAPS_CONF['users_map'] == 1)) {
		$retval .= '<p>' . $LANG_MAPS_1['no_map_user'] . '</p>';
		if (SEC_hasRights('maps.admin')) {
			$retval .= '<p>' . $LANG_MAPS_1['admin_can'] .'<a href="' . $_CONF['site_admin_url'] . '/plugins/maps/map_edit.php?mode=new"> ' . $LANG_MAPS_1['create_map'] . '</a>.</p>';
		}
	} else {
		if (($_MAPS_CONF['global_map'] == 1) && ($list > 1)) {
			//global map
			$map->set_var('edit_button', '');
			$map->set_var('xhtml', XHTML);
			$map->set_var('name', $LANG_MAPS_1['global_map']);
			$map->set_var('map_detail',  $_MAPS_CONF['site_url']  .'/index.php?mode=map&amp;mid=0&name=' . urlencode($LANG_MAPS_1['global_map']) . '&amp;query_limit=500');
			$map->set_var('description', '<br' . XHTML . '>' . $LANG_MAPS_1['info_global_map']);
			if (function_exists('MAPS_getFields')) {
			    $map->set_var('view_map', '<a href="' . $_MAPS_CONF['site_url']  .'/index.php?mode=map&amp;mid=0&name=' . urlencode($LANG_MAPS_1['global_map']) . '&amp;query_limit=500">' . $LANG_MAPS_1['view_map'] . '</a> | ');
			    $map->set_var('view_markers', '<a href="' . $_MAPS_CONF['site_url']  .'/index.php?mode=markers&amp;mid=0&name=' . urlencode($LANG_MAPS_1['global_map']) . '">' . $LANG_MAPS_1['view_markers'] . ' | </a>');
			} else {
			    $map->set_var('view_map', '');
			    $map->set_var('view_markers', '');
			}
			//update
			$updateglobal = COM_getUserDateTimeFormat(time());
			$map->set_var('update', $LANG_MAPS_1['last_modification'] . ' ' . $updateglobal[0]);
			
			//markers
			$markers = DB_count($_TABLES['maps_markers'],'mid',$A['mid']);
			$map->set_var('markers',  ' | ' . $markerssum . ' ' . $LANG_MAPS_1['records']);
			
			//hits
			$map->set_var('hits',  ' | ' . DB_getItem($_TABLES['vars'],'value',"name='globalMapHits'") . ' ' . $LANG_MAPS_1['hits']);
			$retval .= $map->parse('output', 'map');

		}
		
		if ($_MAPS_CONF['users_map'] == 1) {
			$retval .= '<p class="maps_list_item"><strong><a href="' . $_MAPS_CONF['site_url'] . '/users_map.php">' .
			$LANG_MAPS_1['users_map'] . '</a></strong><br'. XHTML . '>' . $LANG_MAPS_1['info_users_map'] . '</p>';
		}
		
		if (SEC_hasRights('maps.admin')) {
			$retval .= '&nbsp;<p>' . $LANG_MAPS_1['admin_can'] .' <a href="' . $_CONF['site_admin_url'] . '/plugins/maps/map_edit.php?mode=new">' . $LANG_MAPS_1['create_map'] . '</a></p>';
		}	
	}

	// Display the end of the maps list
	$retval .= $map->parse('output', 'end');
	
	//Display global map if active
	if (COM_isAnonUser() && ($_MAPS_CONF['maps_login_required'] == 1)) {
	    // do not display global map
		$retval = $header1 . $retval;
	} else if ( $_MAPS_CONF['global_map'] == 1 && ($list > 0) ) {
	    $retval = MAPS_getGlobalMap( '', '', true ) . $header1  . $retval;
	} else {
	    $retval = $header1 . $retval;
	}
	
	$footer = '<div>'. PLG_replaceTags($_MAPS_CONF['map_main_footer']) .'</div>';
	
	return $header . $retval . $footer;
}

// MAIN

$display = '';

if ($_MAPS_CONF['google_api_key'] == '') {
	$display = COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
	$display .= '<p><img src="' . $_CONF['site_admin_url'] . '/plugins/maps/images/maps.png" alt="" align="left" hspace="5">';
	$display .= $LANG_MAPS_1['need_google_api'];
	$display .= COM_siteFooter(0);
	COM_output($display);
	exit ();
}

//page title
$page_title = $LANG_MAPS_1['maps_label'];

if ( $_REQUEST['mode'] == 'map' AND ($_REQUEST['mid'] !=0 && $_REQUEST['mid']>0 && is_numeric($_REQUEST['mid'])) ) $page_title = DB_getItem($_TABLES['maps_maps'], 'name', "mid={$_REQUEST['mid']}");

if ($_REQUEST['mode'] == 'markers' && $_REQUEST['mid'] != 0 && is_numeric($_REQUEST['mid'])) $page_title .= ' | ' . DB_getItem($_TABLES['maps_maps'], 'name', "mid={$_REQUEST['mid']}");

if ($_REQUEST['mode'] == 'marker' && isset($_REQUEST['mkid']) && $_REQUEST['mkid'] != '' && is_numeric($_REQUEST['mkid'])) $page_title = DB_getItem($_TABLES['maps_markers'], 'name', "mkid={$_REQUEST['mkid']}");

$display .= COM_siteHeader('menu', stripslashes($page_title) );
$display .= MAPS_user_menu();

$msg = 0;
if (isset ($_REQUEST['msg'])) {
    $msg = COM_applyFilter ($_REQUEST['msg'], true);
}
if ($msg > 0) {
    $display .= COM_showMessage ($msg, 'maps');
}

switch ($_REQUEST['mode']) {
    case 'map':
	    // query database for map
		if ($_REQUEST['mid']>0 && is_numeric($_REQUEST['mid'])) {
			
			$display .= MAPS_getMap($_REQUEST['mid']);
			
			if ( $_REQUEST['mid'] >= 0 && is_numeric($_REQUEST['mid']) ) {
				$display .= MAPS_ListMarkers($_REQUEST['mid']);
			} 
		} elseif ($_REQUEST['mid'] == 0) {
			//Display the Global Map 
			$display .= MAPS_getGlobalMap();
 
		} else {
			echo COM_refresh($_MAPS_CONF['site_url'] . '/index.php');
		}
        break;
	case 'markers':
	    if ( ($_REQUEST['mid'] >= 0) ) {
		    $display .= MAPS_ListMarkers($_REQUEST['mid']);
		} else {
			echo COM_refresh($_MAPS_CONF['site_url'] . '/index.php');
		}
	    break;
	case 'marker':
	    if ( isset($_REQUEST['mkid']) && $_REQUEST['mkid'] != '' && function_exists('MAPS_proViewMarker') ) {
		    $display .= MAPS_proViewMarker($_REQUEST['mkid']);
		} else {
			echo COM_refresh($_MAPS_CONF['site_url'] . '/index.php');
		}
	    break;

    default:	
	    $default = true;
	    $display .= MAPS_displayFrontPage();
	
}

$display .= COM_siteFooter(0);

COM_output($display);

?>
