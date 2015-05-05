<?php

// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | 記事へ有効な多言語記事への切り替えを可能にする自動タグ用カスタム関数      |
// +---------------------------------------------------------------------------+
// | custom_multilangstory.php                                                 |
// | version: 1.0.0                                                            |
// +---------------------------------------------------------------------------+
// | Geeklog 1.7                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors: Hiroron         - hiroron AT hiroron DOT com                     |
// +---------------------------------------------------------------------------+
// | This program is licensed under the terms of the GNU General Public License|
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                      |
// | See the GNU General Public License for more details.                      |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// +---------------------------------------------------------------------------+

if (strpos(strtolower($_SERVER['PHP_SELF']), 'custom_multilangstory.php') !== false) {
    die('This file can not be used on its own.');
}

/**
 * sidに対応する多言語記事が存在する場合はその記事へのリンクを作成し返す関数
 *   $sid : story id
 *   --------------
 *   return : 多言語記事へのリンク
**/
function CUSTOM_multilangstory ($sid)
{
    global $_CONF, $_TABLES;
    
    $retval = '';
    
    if( empty( $_CONF['languages'] ) || empty( $_CONF['language_files'] ) ||
          ( count( $_CONF['languages'] ) != count( $_CONF['language_files'] ))) {
        return $retval;
    }

    $work = split('_',$sid);
    $cur_lang = array_pop($work);
    if ( empty($cur_lang) || !array_key_exists($cur_lang, $_CONF['languages']) ) {
        return $retval;
    }

    $entries = array();
    $mini_sid = implode('_', $work);
    foreach ( $_CONF['languages'] as $key => $value) {
        if ($cur_lang != $key) {
            $mul_sid = DB_getItem($_TABLES['stories'], 'sid', 'sid="' . $mini_sid . '_' . $key . '"');
            if (!empty($mul_sid)) {
                $url = COM_buildUrl( $_CONF['site_url'] . '/article.php?story=' . $mul_sid );
                $entries[] = '<a href="' . $url . '">' . $value . '</a>';
            }
        }
    }
    if (sizeof ($entries) > 0) {
        $retval .= COM_makeList($entries);
    }
    
    return $retval;
}

/**
 * 自動タグ用関数
**/
function phpautotags_multilangstory($p1, $p2, $fulltag)
{
    if (empty($p1)) { return ''; }
    return CUSTOM_multilangstory($p1);
}

?>
