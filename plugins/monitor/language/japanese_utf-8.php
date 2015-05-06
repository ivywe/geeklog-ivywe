<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.0                                                        |
// +---------------------------------------------------------------------------+
// | japanese_utf-8.php                                                        |
// |                                                                           |
// | Japanese language file                                                    |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011 by the following authors:                              |
// |                                                                           |
// | Authors: Ben - ben AT geeklog DOT fr                                      |
// |          Ivy - ivy AT geeklog DOT jp                                      |
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
	'doc'           => 'モニタープラグインはログ (access, error, spamx, captcha...)の送信とログ削除にGeeklogの疑似CRONを利用しています。 また、ログファイルをブラウザ上で閲覧と削除ができます。 ',
    'configuration' => 'モニタープラグインのコンフィギュレーション',
	'file'          => 'ファイル:',
	'view_log'      => 'ログファイルを見る',
	'clear_log'     => 'ログファイルを削除する',
	'log_file'      => 'ログファイル:',
	'view_clear'    => 'ログファイルを閲覧/削除する。',
	'set_cron'      => 'モニタープラグインの活用には、最初にCRONを設定してください。',
);

// Messages for the plugin upgrade
$PLG_monitor_MESSAGE3002 = $LANG32[9]; // "requires a newer version of Geeklog"

/*
**
*   Configuration system subgroup strings
*   @global array $LANG_configsubgroups['monitor']
*/
$LANG_configsubgroups['monitor'] = array(
    'sg_main' => 'メイン設定'
);

/**
*   Configuration system fieldset names
*   @global array $LANG_fs['monitor']
*/
$LANG_fs['monitor'] = array(
    'fs_main'            => '一般設定'
 );
 
/**
*   Configuration system prompt strings
*   @global array $LANG_confignames['monitor']
*/
$LANG_confignames['monitor'] = array(
    //Main settings
	'emails'  => 'ログを送信するメールリスト (必要なメールリストをカンマ区切りで)'
)

?>
