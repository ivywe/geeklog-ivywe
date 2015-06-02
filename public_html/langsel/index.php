<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Language Selection Block Plugin 1.0.0                                     |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// |                                                                           |
// | Public plugin page                                                        |
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

require_once '../lib-common.php';

// take user back to the homepage if the plugin is not active
if (! in_array('langsel', $_PLUGINS)) {
    echo COM_refresh($_CONF['site_url'] . '/index.php');
    exit;
}

// Figure out where to go back after changing the language
if (isset($_REQUEST['target']) && $_REQUEST['target'] == COM_applyFilter($_REQUEST['target'])) {
    $target = COM_applyFilter($_REQUEST['target']);
} else {
    $target = $_CONF['site_url'] . '/index.php';
}

// Check if the language is valid
if (! array_key_exists($_REQUEST['langsel'], MBYTE_languageList($_CONF['default_charset']))) {
    // Invalid language - let's silently bail out
    echo COM_refresh($target);
    exit;
} else {
    $language = $_REQUEST['langsel'];
}

// Perform language switch
setcookie($_CONF['cookie_language'], $language, 0, '/');
if ($_USER['uid'] > 1) {
    DB_query("UPDATE {$_TABLES['users']} SET language='$language' WHERE uid='{$_USER['uid']}'");
}
echo COM_refresh($target);
exit;
?>
