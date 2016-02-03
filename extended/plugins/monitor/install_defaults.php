<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | monitor plugin 1.3                                                        |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// |                                                                           |
// | Initial Installation Defaults used when loading the online configuration  |
// | records. These settings are only used during the initial installation     |
// | and not referenced any more once the plugin is installed.                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2014-2016 by the following authors:                         |
// |                                                                           |
// | Authors: Ben        - cordiste AT free DOT fr                             |
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
 * monitor default settings
 *
 * Initial Installation Defaults used when loading the online configuration
 * records. These settings are only used during the initial installation
 * and not referenced any more once the plugin is installed
 *
 */
 
/**
*   Default values to be used during plugin installation/upgrade
*   @global array $_monitor_DEFAULT
*/
global $_DB_table_prefix, $_monitor_DEFAULT;

/**
 * Language file include
 */
$plugin_path = $_CONF['path'] . 'plugins/monitor/';
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';

if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}

$_monitor_DEFAULT = array();

/**
*   Main settings
*/
$_monitor_DEFAULT['emails']    = $_CONF['site_mail'];
$_monitor_DEFAULT['repository']    = 'Geeklog-Plugins';



/**
* Initialize monitor plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. 
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_monitor()
{
    global $_CONF, $_monitor_DEFAULT;
	
    $c = config::get_instance();
    if (!$c->group_exists('monitor')) {

        //This is main subgroup #0
		$c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'monitor');
		
		//Main settings   
		$c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true, 'monitor');
        $c->add('emails', $_monitor_DEFAULT['emails'],
        'text', 0, 0, 0, 10, true, 'monitor');
        $c->add('repository', $_monitor_DEFAULT['repository'],
                'text', 0, 0, 0, 20, true, 'monitor');

    }				

    return true;
}

?>