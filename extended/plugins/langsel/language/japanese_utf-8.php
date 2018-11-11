<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Language Selection Block Plugin                                           |
// +---------------------------------------------------------------------------+
// | japanese_utf-8.php                                                        |
// |                                                                           |
// | Japanese language file                                                    |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011-2018 by the following authors:                         |
// |                                                                           |
// | Authors: Rouslan Placella - rouslan AT placella DOT com                   |
// |          Kenji ITO        - mystralkk AT gmail DOT com                    |
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
    'plugin_name'          => '言語選択ブロック',
    'conf_link'            => '設定',
    'title'                => '言語選択',
    'submit'               => '実行',
    'autotag_desc_langsel' => 'Langsel自動タグは言語セレクターを表示します。',
);

// Localization of the Admin Configuration UI
$LANG_configsections['langsel'] = array(
    'label' => '言語選択ブロック',
    'title' => '言語選択ブロックの設定'
);

$LANG_confignames['langsel'] = array(
    'block_pos' => 'ブロックの表示位置',
    'block_order' => 'ブロックの順序',
);

$LANG_configsubgroups['langsel'] = array(
    'sg_main' => '主要設定'
);

$LANG_tab['langsel'] = array(
    'tab_main' => '言語選択ブロックの主要設定'
);

$LANG_fs['langsel'] = array(
    'fs_main' => '言語選択ブロックの主要設定'
);

$LANG_configselects['langsel'] = array(
    1 => array('ブロックなし' => 2, '左ブロック' => 1, '右ブロック' => 0)
);
