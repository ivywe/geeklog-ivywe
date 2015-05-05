<?php

// +---------------------------------------------------------------------------+
// | TinyMCE Plugin for Geeklog - The Ultimate Weblog                          |
// +---------------------------------------------------------------------------+
// | geeklog/plugins/tinymce/autoinstall.php                                   |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010-2011 mystral-kk - geeklog AT mystral-kk DOT net        |
// |                                                                           |
// | Constructed with the Universal Plugin                                     |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), strtolower(basename(__FILE__))) !== FALSE) {
	die('This file can not be used on its own!');
}

/**
* Plugin autoinstall function
*
* @param    string  $pi_name    Plugin name
* @return   array               Plugin information
*/
function plugin_autoinstall_tinymce($pi_name) {
	global $_TMCE_CONF;
	
	require_once dirname(__FILE__) . '/config.php';
	
	$pi_name         = 'tinymce';
	$pi_display_name = 'TinyMCE';
	$pi_admin        = $pi_display_name . ' Admin';
	
	$info = array(
		'pi_name'         => $pi_name,
		'pi_display_name' => $pi_display_name,
		'pi_version'      => $_TMCE_CONF['pi_version'],
		'pi_gl_version'   => $_TMCE_CONF['gl_version'],
		'pi_homepage'     => $_TMCE_CONF['pi_url'],
	);
	
	$groups   = $_TMCE_CONF['GROUPS'];
	$features = $_TMCE_CONF['FEATURES'];
	$mappings = $_TMCE_CONF['MAPPINGS'];
	
	$tables = array('table1');
	
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
* Load plugin configuration from database
*
* @param    string  $pi_name    Plugin name
* @return   boolean             TRUE on success, otherwise FALSE
* @see      plugin_initconfig_tinymce
*/
function plugin_load_configuration_tinymce($pi_name) {
    global $_CONF;
    
    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';
    
    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';
    
    return plugin_initconfig_tinymce();
}

/**
* Checks if the plugin is compatible with this Geeklog version
*
* @param    string  $pi_name    Plugin name
* @return   boolean             true: plugin compatible; false: not compatible
*/
function plugin_compatible_with_this_version_tinymce($pi_name) {
	global $_CONF, $_DB_dbms;
	
	// checks if we support the DBMS the site is running on
	$dbFile = $_CONF['path'] . 'plugins/' . $pi_name . '/sql/'
			. $_DB_dbms . '_install.php';
	clearstatcache();
	
	if (!file_exists($dbFile)) {
		return FALSE;
	}
	
	// adds checks here
	
	return TRUE;
}

/**
* Does post-installation operations
*
* @return   boolean     TRUE = success, FALSE = otherwise
*/
function plugin_postinstall_tinymce() {
	global $_CONF, $_TABLES, $_TMCE_CONF;
	
	// Saves the current value of $_CONF['advanced_editor'] into db
	$name  = addslashes($_TMCE_CONF['db_var_name']);
	$value = addslashes(serialize($_CONF['advanced_editor']));
	$sql = "INSERT INTO {$_TABLES['vars']} "
		 . "VALUES ('{$name}', '{$value}') ";
	DB_query($sql);
	
	// Disables FCKeditor
    $c = config::get_instance();
	$c->set('advanced_editor', FALSE, 'Core');
	
	// Sets postmode to 'HTML'
	$c->set('postmode', 'html', 'Core');
	
	// Adds "img" tag to $_CONF['admin_html']
	if (!array_key_exists('img', $_CONF['admin_html'])) {
		$_CONF['admin_html']['img'] = array(
			'width'		=> 1,
			'height'	=> 1,
			'src'		=> 1,
			'align'		=> 1,
			'valign'	=> 1,
			'border'	=> 1,
			'alt'		=> 1,
			'title'		=> 1,
		);
		
		$c->set('admin_html', $_CONF['admin_html'], 'Core');
	}
	
	return TRUE;
}
