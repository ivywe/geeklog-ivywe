<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.0                                                        |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// |                                                                           |
// | This file does two things: 1) it implements the necessary Geeklog Plugin  |
// | API methods and 2) implements all the common code needed by this plugin.  |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011 by the following authors:                              |
// |                                                                           |
// | Authors: Ben - ben AT geeklog DOT fr                                      |
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
* @package Monitor
*/

if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die ('This file can not be used on its own.');
}

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

/**
 * Load our config
 */
require_once $_CONF['path_system'].'classes/config.class.php';
$c = config::get_instance();
$_MONITOR_CONF = $c->get_config('monitor');

// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementation                                         |
// +---------------------------------------------------------------------------+


/**
* Return plugin entry for "Command and Control" (moderation.php)
*
* @return  array  Array containing (plugin name, admin url, url of plugin icon)
*
*/
function plugin_cclabel_monitor()
{
    global $_CONF, $LANG_MONITOR_1;

    $retval = array();
    if (SEC_hasRights('monitor.admin')) {
        $retval = array($LANG_MONITOR_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/monitor/index.php',
            plugin_geticon_monitor());
    }

    return $retval;
}

/**
* Return plugin entry for the Admins Only block
*
* @return array Array containing (plugin name, plugin admin url, # of plugin items or '')
*/
function plugin_getadminoption_monitor()
{
    global $_CONF, $LANG_MONITOR_1;

    if (SEC_hasRights('monitor.admin')) {
        return array($LANG_MONITOR_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/monitor/index.php', 0);
    }
}

/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_monitor()
{
    global $_CONF;

    // popular alternative location:
    // return $_CONF['site_url'] . '/monitor/images/monitor.png';

    return $_CONF['site_admin_url'] . '/plugins/monitor/images/monitor.png';
}

/**
 * Returns the current plugin code version
 *
 * @return string    plugin version
 */
function plugin_chkVersion_monitor()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/monitor/autoinstall.php';

    $inst_parms = plugin_autoinstall_monitor('monitor');

    return $inst_parms['info']['pi_version'];
}

/**
* Update the plugin
*
* @return   int     Number of message to display or true for success
*
*/
function plugin_upgrade_monitor()
{
    global $_CONF, $_TABLES;

    $installed_version = DB_getItem($_TABLES['plugins'], 'pi_version',
                                    "pi_name = 'monitor'");
    $code_version = plugin_chkVersion_monitor();
    if ($installed_version == $code_version) {
        // nothing to do
        return true;
    }

    require_once $_CONF['path'] . 'plugins/monitor/autoinstall.php';

    if (! plugin_compatible_with_this_version_monitor('monitor')) {
        return 3002;
    }

    // other update code goes here

    // update plugin version number
    $inst_parms = plugin_autoinstall_monitor('monitor');
    $pi_gl_version = $inst_parms['info']['pi_gl_version'];

    DB_query("UPDATE {$_TABLES['plugins']} SET pi_version = '$code_version', pi_gl_version = '$pi_gl_version' WHERE pi_name = 'monitor'");
	
	/* This code is for statistics ONLY */
    $message =  'Completed monitor plugin upgrade: ' .date('m d Y',time()) . "   AT " . date('H:i', time()) . "\n";
    $message .= 'Site: ' . $_CONF['site_url'] . ' and Sitename: ' . $_CONF['site_name'] . "\n";
    $pi_version = DB_getItem($_TABLES['plugins'], 'pi_version', "pi_name = 'monitor'");
    COM_mail("ben@geeklog.fr","$pi_name Version:$pi_version Upgrade successfull",$message);

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
function plugin_autouninstall_monitor()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array(/* e.g. 'monitor' */),
        /* give the full name of the group, as in the db */
        'groups' => array('Monitor Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('monitor.admin'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(),
        /* give all vars with their name */
        'vars' => array()
    );

    return $out;
}

/**
* Mails the contents of log files
*
* @param    string  $log        The name of the log file
*/
function MONITOR_logEmail($logs)
{
    global $_CONF, $_MONITOR_CONF, $LANG_MONITOR_1;
	
	foreach ($logs as $log) {
		if (file_exists($_CONF['path_log'] . $log)) {			
			//Read log file
			$message .= "<p><b>{$LANG_MONITOR_1['log_file']} " . $log . "</b></p><pre>";
			$message .= implode('', file($_CONF['path_log'] . $log));
			$message .= "</pre><hr>";
			
			//delelete log file
			unlink($_CONF['path_log'] . $log);
			$timestamp = strftime( "%c" );
			$fd = fopen( $_CONF['path_log'] . $log, a );
			fputs( $fd, "$timestamp - Log File Cleared \n" );
			fclose($fd);
		}
	}
	//List of emails to send logs to
	$to = explode(',', $_MONITOR_CONF['emails']);
	$count = count ($to);
	for ($i=0; $i < $count; $i++) { 
		$contact = trim($to[$i]);
		$subject = $_CONF['site_name'] . ' | Log files';
		COM_mail($contact, $subject, $message, '',true);
		COM_errorLog ('Monitor - Logs send to ' . $contact);
	}
}

function plugin_runScheduledTask_monitor ()
{
    MONITOR_logEmail(array('error.log','captcha.log','access.log','spamx.log', 'purepro_downloads.log', 'paypal_downloads.log'));
}

?>
