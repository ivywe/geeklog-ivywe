<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.0                                                        |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// |                                                                           |
// | Plugin administration page                                                |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011 by the following authors:                              |
// |                                                                           |
// | Authors: Ben - ben AT geeklog DOT fr                                      |
// +---------------------------------------------------------------------------+
// | Created with the Geeklog Plugin Toolkit.                                  |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2003-2008 by the following authors:                         |
// |                                                                           |
// | Tom Willett - twillett AT users DOT sourceforge DOT net                   |
// | Mark R. Evans          mark AT glfusion DOT org                           |
// +---------------------------------------------------------------------------+
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

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';

$display = '';

// Ensure user even has the rights to access this page
if (! SEC_hasRights('monitor.admin')) {
    $display .= COM_showMessageText($MESSAGE[29], $MESSAGE[30]);

    // Log attempt to access.log
    COM_accessLog("User {$_USER['username']} tried to illegally access the Monitor plugin administration screen.");

	// Options
	$html_infos['what'] = 'menu'; // If 'none' then no left blocks are returned, if 'menu' (default) then right blocks are returned
	$html_infos['pagetitle'] = $MESSAGE[30]; // Optional content for the page's <title>
	$html_infos['breadcrumbs'] = ''; // Optional content for the page's breadcrumb
	$html_infos['headercode'] = '';  // Optional code to go into the page's <head>
	$html_infos['rightblock'] = ''; // Whether or not to show blocks on right hand side default is no (-1)
	$html_infos['custom'] = array(); // An array defining custom function to be used to format Rightblocks

	//Output
	COM_output(COM_createHTMLDocument($display, $html_infos));
    exit;
}


$log = isset($_POST['log']) ? COM_applyFilter($_POST['log']) : '';
/*
* Main Function
*/

$retval = '';

$T = new Template($_CONF['path'] . 'plugins/monitor/templates');
$T->set_file (array ('admin' => 'administration.thtml'));

$T->set_var(array(
    'title'        => $LANG_MONITOR_1['plugin_name'] . ' | ' .  $LANG_MONITOR_1['view_clear'],
));

// cron
if ($_CONF['cron_schedule_interval'] <= 0){
    $cron = "<p><a href=\"#\" onclick='document.cron_conf_link.submit()'>{$LANG_MONITOR_1['set_cron']}</a></p>
	<form name='cron_conf_link' action='{$_CONF['site_admin_url']}/configuration.php' method='POST'></form>";
} else {
    $cron = '';
}


$retval .= "<form method=\"post\" action=\"{$_CONF['site_admin_url']}/plugins/monitor/index.php\">";
$retval .= "{$LANG_MONITOR_1['file']}&nbsp;&nbsp;&nbsp;";
$files = array();
if ($dir = @opendir($_CONF['path_log'])) {
    while(($file = readdir($dir)) !== false) {
        if (is_file($_CONF['path_log'] . $file)) { array_push($files,$file); }
    }
    closedir($dir);
}
$retval .= '<SELECT name="log">';
if (empty($log)) { $log = $files[0]; } // default file to show
for ($i = 0; $i < count($files); $i++) {
    $retval .= '<option value="' . $files[$i] . '"';
    if ($log == $files[$i]) { $retval .= ' SELECTED'; }
    $retval .= '>' . $files[$i] . '</option>';
    next($files);
}
$retval .= "</SELECT>&nbsp;&nbsp;&nbsp;&nbsp;";
$retval .= "<input type=\"submit\" name=\"action\" value=\"{$LANG_MONITOR_1['view_log']}\">";
$retval .= "&nbsp;&nbsp;&nbsp;&nbsp;";
$retval .= "<input type=\"submit\" name=\"action\" value=\"{$LANG_MONITOR_1['clear_log']}\">";
$retval .= "</form>";

$action = isset($_POST['action']) ? COM_applyFilter($_POST['action']) : '';

if ($action == $LANG_MONITOR_1['clear_log']) {
    unlink($_CONF['path_log'] . $_POST['log']);
    $timestamp = strftime( "%c" );
    $fd = fopen( $_CONF['path_log'] . $_POST['log'], a );
    fputs( $fd, "$timestamp - Log File Cleared \n" );
    fclose($fd);
    $action = $LANG_MONITOR_1['view_log'];
}
if ($action == $LANG_MONITOR_1['view_log']) {
    $retval .= "<hr><p><b>{$LANG_MONITOR_1['log_file']} " . $_POST['log'] . "</b></p><pre>";
    $retval .= implode('', file($_CONF['path_log'] . $_POST['log']));
    $retval .= "</pre>";
}

$T->set_var(array(
    'configuration'  => $LANG_MONITOR_1['configuration'],
    'doc'            => $LANG_MONITOR_1['doc'],
	'admin_body'     => $retval,
	'site_admin_url' => $_CONF['site_admin_url'],
	'cron'           => $cron,
));

$T->parse('output', 'admin');
$display .= $T->finish($T->get_var('output'));



// Options
$html_infos['what'] = 'menu'; // If 'none' then no left blocks are returned, if 'menu' (default) then right blocks are returned
$html_infos['pagetitle'] = $MESSAGE[30]; // Optional content for the page's <title>
$html_infos['breadcrumbs'] = ''; // Optional content for the page's breadcrumb
$html_infos['headercode'] = '';  // Optional code to go into the page's <head>
$html_infos['rightblock'] = ''; // Whether or not to show blocks on right hand side default is no (-1)
$html_infos['custom'] = array(); // An array defining custom function to be used to format Rightblocks

//Output
COM_output(COM_createHTMLDocument($display, $html_infos));

?>
