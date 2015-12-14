<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Language Selection Block Plugin 1.0.0                                     |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// |                                                                           |
// | This file is used to hook into Geeklog's configuration UI                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011 by the following authors:                              |
// |                                                                           |
// | Authors: Rouslan Placella - rouslan AT placella DOT com                   |
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
* @package LanguageSelectionBlock
*/

if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die ('This file can not be used on its own.');
}

/**
* Language Selection Block default settings
*
* Initial Installation Defaults used when loading the online configuration
* records.  These settings are only used during the initial installation
* and not referenced any more once the plugin is installed
*/
global $_LANGSEL_DEFAULT;
$_LANGSEL_DEFAULT = array();

$_LANGSEL_DEFAULT['block_pos'] = 1;
$_LANGSEL_DEFAULT['block_order'] = 1;

/**
* Initialize Language Selection Block plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist.  Initial values will be taken from $_LANGSEL_DEFAULT.
*
* @return   boolean     TRUE: success; FALSE: an error occurred
*/
function plugin_initconfig_langsel()
{
    global $_LANGSEL_CONF, $_LANGSEL_DEFAULT;

    if (is_array($_LANGSEL_CONF) && (count($_LANGSEL_CONF) > 1)) {
        $_LANGSEL_DEFAULT = array_merge($_LANGSEL_DEFAULT, $_LANGSEL_CONF);
    }

    $me = 'langsel';

    $c = config::get_instance();
    if (!$c->group_exists('langsel')) {
        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, $me, 0);
        $c->add('tab_main', NULL, 'tab', 0, 0, NULL, 0, true, $me, 0);
        $c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true, $me, 0);
        // The below two lines add two settings to Geeklog's config UI
        $c->add('block_pos', $_LANGSEL_DEFAULT['block_pos'], 'select',0, 0, 1, 10, true, $me, 0);
        $c->add('block_order', $_LANGSEL_DEFAULT['block_order'], 'text', 0, 0, null, 20, true, $me, 0);
    }

    return true;
}
?>

