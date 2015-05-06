<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.0                                                        |
// +---------------------------------------------------------------------------+
// | english.php                                                               |
// |                                                                           |
// | English language file                                                     |
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

/**
* Import Geeklog plugin messages for reuse
*
* @global array $LANG32
*/
global $LANG32;

// +---------------------------------------------------------------------------+
// | Array Format:                                                             |
// | $LANGXX[YY]:  $LANG - variable name                                       |
// |               XX    - specific array name                                 |
// |               YY    - phrase id or number                                 |
// +---------------------------------------------------------------------------+

$LANG_MONITOR_1 = array(
    'plugin_name'   => 'Monitor',
	'doc'           => 'The monitor plugin use the Cron Schedule Interval to send by email the log files (access, error, spamx, captcha...) and to clear those files. You can also display and clear the log files online. ',
    'configuration' => 'Display monitor configuration',
	'file'          => 'File:',
	'view_log'      => 'View Log file',
	'clear_log'     => 'Clear Log file',
	'log_file'      => 'Log file:',
	'view_clear'    => 'View/Clear the Log Files.',
	'set_cron'      => 'First, you need to set cron interval in the core config to use monitor',
);

// Messages for the plugin upgrade
$PLG_monitor_MESSAGE3002 = $LANG32[9]; // "requires a newer version of Geeklog"

/*
**
*   Configuration system subgroup strings
*   @global array $LANG_configsubgroups['monitor']
*/
$LANG_configsubgroups['monitor'] = array(
    'sg_main' => 'Main Settings'
);

/**
*   Configuration system fieldset names
*   @global array $LANG_fs['monitor']
*/
$LANG_fs['monitor'] = array(
    'fs_main'            => 'General Settings'
 );
 
/**
*   Configuration system prompt strings
*   @global array $LANG_confignames['monitor']
*/
$LANG_confignames['monitor'] = array(
    //Main settings
	'emails'  => 'List of emails to send the logs to (separated with a coma if more than on email is needed)'
)

?>
