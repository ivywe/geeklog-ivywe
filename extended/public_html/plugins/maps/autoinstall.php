<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.4                                                           |
// +---------------------------------------------------------------------------+
// | autoinstall.php                                                           |
// |                                                                           |
// | This file provides helper functions for the automatic plugin install.     |
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

/**
* Plugin autoinstall function
*
* @param    string  $pi_name    Plugin name
* @return   array               Plugin information
*
*/
function plugin_autoinstall_maps($pi_name)
{
    $pi_name         = 'maps';
    $pi_display_name = 'Maps';
    $pi_admin        = $pi_display_name . ' Admin';

    $info = array(
        'pi_name'         => $pi_name,
        'pi_display_name' => $pi_display_name,
        'pi_version'      => '1.4.0',
        'pi_gl_version'   => '1.8.0',
        'pi_homepage'     => 'http://geeklog.fr'
    );

    $groups = array(
        $pi_admin => 'Users in this group can administer the '
                     . $pi_display_name . ' plugin'
    );

    $features = array(
        $pi_name . '.admin'    => 'Full access to ' . $pi_display_name
                                  . ' plugin'
    );

    $mappings = array(
        $pi_name . '.admin'     => array($pi_admin)
    );

    $tables = array(
        'maps_maps',
		'maps_geo',
		'maps_markers',
		'maps_submission',
		'maps_markers_cat',
		'maps_markers_fields',
		'maps_markers_values',
		'maps_overlays',
		'maps_map_overlay',
		'maps_map_icons',
		'maps_overlays_groups'
    );

    $inst_parms = array(
        'info'      => $info,
        'groups'    => $groups,
        'features'  => $features,
        'mappings'  => $mappings,
        'tables'    => $tables
    );

    return $inst_parms;
}

/**
* Check if the plugin is compatible with this Geeklog version
*
* @param    string  $pi_name    Plugin name
* @return   boolean             true: plugin compatible; false: not compatible
*
*/
function plugin_compatible_with_this_version_maps($pi_name)
{
    global $_CONF, $_DB_dbms;

    // check if we support the DBMS the site is running on
    $dbFile = $_CONF['path'] . 'plugins/' . $pi_name . '/sql/'
            . $_DB_dbms . '_install.php';
    if (! file_exists($dbFile)) {
        return false;
    }

    // add checks here

    return true;
}

function plugin_load_configuration_maps($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_maps();
}

function plugin_postinstall_maps($pi_name)
{
    global $_TABLES, $_CONF, $_USER;
	
    /* This code is for statistics ONLY */
    $message =  'Completed maps plugin install: ' .date('m d Y',time()) . "   AT " . date('H:i', time()) . "\n";
    $message .= 'Site: ' . $_CONF['site_url'] . ' and Sitename: ' . $_CONF['site_name'] . "\n";
    $pi_version = DB_getItem($_TABLES['plugins'], 'pi_version', "pi_name = 'maps'");
    COM_mail("ben@geeklog.fr","$pi_name Version:$pi_version Install successfull",$message);

	return true;
}

//Cleaning maps plugins before install to fix v1.2 bug
global $_PLUGINS , $_TABLES, $_CONF, $_DB_table_prefix;

// check if the plugin is installed
$installed = DB_getItem($_TABLES['plugins'],'pi_name', "pi_name='maps'");

if (!$installed) {
	//Remove db and group
	require_once $_CONF['path'] . 'plugins/maps/functions.inc';
	require_once $_CONF['path'] . 'plugins/maps/maps.php';
	if (function_exists('plugin_autouninstall_maps')) {
		$function = 'plugin_autouninstall_maps';
		$remvars = $function();

		if (empty($remvars) || $remvars == false) {
			 COM_errorLog("Variables not found.", 1);
		}

		// removing tables
		if (isset($remvars['tables'])) {
			$num_tables = count($remvars['tables']);
		} else {
			$num_tables = 0;
		}
		for ($i = 0; $i < $num_tables; $i++) {
			if (isset($_TABLES[$remvars['tables'][$i]])) {
				if( DB_numRows( DB_query("SHOW TABLES LIKE '" . $_TABLES[$remvars['tables'][$i]] . "'"))) {
					COM_errorLog("Dropping table {$_TABLES[$remvars['tables'][$i]]} if exists", 1);
					DB_query("DROP TABLE IF EXISTS {$_TABLES[$remvars['tables'][$i]]}", 0);
					COM_errorLog('...success', 1);
				}
			}
		}

		// removing groups
		if (isset($remvars['groups'])) {
			$num_groups = count($remvars['groups']);
		} else {
			$num_groups = 0;
		}
		for ($i = 0; $i < $num_groups; $i++) {
			$grp_id = DB_getItem($_TABLES['groups'], 'grp_id',
								 "grp_name = '{$remvars['groups'][$i]}'");
			if (!empty($grp_id)) {
				COM_errorLog("Attempting to remove the {$remvars['groups'][$i]} group", 1);
				DB_delete($_TABLES['groups'], 'grp_id', $grp_id);
				COM_errorLog('...success', 1);
				COM_errorLog("Attempting to remove the {$remvars['groups'][$i]} group from all groups.", 1);
				DB_delete($_TABLES['group_assignments'], 'ug_main_grp_id', $grp_id);
				COM_errorLog('...success', 1);
			}
		}
		
		// remove config table data for this plugin
		DB_delete($_TABLES['conf_values'], 'group_name', 'maps');
		
		// removing variables
		if (isset($remvars['vars'])) {
			$num_vars = count($remvars['vars']);
		} else {
			$num_vars = 0;
		}
		for ($i = 0; $i < $num_vars; $i++) {
			DB_delete($_TABLES['vars'], 'name', $remvars['vars'][$i]);
		}
		
	} else {
		COM_errorLog("Auto-cleaning plugin maps: Fail.", 1);
	}
}
?>
