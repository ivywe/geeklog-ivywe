<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.3                                                        |
// +---------------------------------------------------------------------------+
// | english.php                                                               |
// |                                                                           |
// | English language file                                                     |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2014-2016 by the following authors:                         |
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
    'plugin_name'         => 'Monitor',
    'home'                => 'Home', // change 1.3.0
    'view_clear_logs'     => 'View/Clear the Log Files',
    'file'                => 'File:',
    'log_file'            => 'Log file :',
    'view_logs'           => 'View logs',
    'clear_logs'          => 'Clear logs',
    'images_folder'       => 'Images from public_html/images folder',
    'resize'              => 'Resize images',
    'resize_images'       => 'Resize all images',
    'resize_images_help'  => 'Monitor plugin can resize pictures bigger than 1600px from your public_html/images folder. The width-height ratio will be kept. ',
    'no_images_to_resize' => 'There is no image bigger than 1600px in your public_html/images folder.',
    'change_user_photo'   => 'Change user photo',
    'comments'            => 'Comments',
    'comments_list'       => 'Comments list',
    'anonymous'           => 'Anonymous',
    'configuration'       => 'Configuration',
    'images'              => 'Images',
    'images_list'         => 'Images list',
    'main'                => 'Monitor main page',
    'logs'                => 'Log files',
    'updates'             => 'Updates',
    'available_updates'   => 'Available updates are from:',
    'plugin_list'         => 'Plugin updates',
    'no_update'           => 'This plugin can not be updated',
    'up_to_date'          => 'This plugin is already up to date',
    'update_to'           => 'Update to',
    'need_upgrade'        => 'You need to upgrade to Geeklog v',
    'before_update'       => 'before you can update to',
    'not_available'       => 'Plugin not avaible in this repository',
    'ask_author'          => 'This plugin do not support this feature. Ask his author to change this.',
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
    'emails'  => 'List of emails to send the logs to (separated with a coma if more than on email is needed)',
    'repository'  => 'Name of the repository owner you want to use for plugins updates on Github (default is Geeklog-Plugins). Leave blank to disable this feature.'
)

?>
