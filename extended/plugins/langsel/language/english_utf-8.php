<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Language Selection Block Plugin 1.0.0                                     |
// +---------------------------------------------------------------------------+
// | english.php                                                               |
// |                                                                           |
// | English language file                                                     |
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

$LANG_LANGSEL_1 = array(
    'plugin_name' => 'Language Selection Block',
    'conf_link'   => 'Configuration',
	'title'       => 'Language Selection',
	'submit'      => 'Go'
);

// Localization of the Admin Configuration UI
$LANG_configsections['langsel'] = array(
    'label' => 'Language Selection Block',
    'title' => 'Language Selection Block Configuration'
);

$LANG_confignames['langsel'] = array(
    'block_pos' => 'Where to show the block',
    'block_order' => 'Block Order',
);

$LANG_configsubgroups['langsel'] = array(
    'sg_main' => 'Main Settings'
);

$LANG_tab['langsel'] = array(
    'tab_main' => 'Language Selection Block Main Settings'
);

$LANG_fs['langsel'] = array(
    'fs_main' => 'Language Selection Block Main Settings'
);

$LANG_configselects['langsel'] = array(
    1 => array('Left Blocks' => 1, 'Right Blocks' => 0)
);
?>
