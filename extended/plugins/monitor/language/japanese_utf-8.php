<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.3                                                        |
// +---------------------------------------------------------------------------+
// | japanese_utf-8.php                                                        |
// |                                                                           |
// | Japanese language file                                                    |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2014-2016 by the following authors:                         |
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
    'plugin_name'         => 'Monitor',
    'home'                => 'Home', // change 1.3.0
    'view_clear_logs'     => 'View/Clear the Log Files',
	'file'                => 'ファイル:',
	'log_file'            => 'ログファイル:',
	'view_logs'           => 'ログファイルを見る',
    'clear_logs'          => 'ログファイルを削除する',
    'images_folder'       => 'public_html/images 画像フォルダ',
    'resize'              => 'リサイズ',
    'resize_images'       => '全画像をリサイズする',
    'resize_images_help'  => 'public_html/images画像フォルダの1600px以上の大きさのファイルを縦横比率を変えずに縮小できます。',
    'no_images_to_resize' => 'public_html/images フォルダ以下に1600px以上の大きさの画像はありません。',
    'change_user_photo'   => 'ユーザーの写真を変更する',
    'comments'            => 'コメント',
    'comments_list'       => 'コメントリスト',
    'anonymous'           => 'ゲスト',
    'configuration'       => 'モニタープラグインのコンフィギュレーション',
    'images'              => '画像',
    'images_list'         => '画像リスト',
    'main'                => 'メインページ',
    'logs'                => 'ログファイル',
    'updates'             => 'アップデート',
    'available_updates'   => 'アップデートが有効:',
    'plugin_list'         => 'プラグインアップデート',
    'no_update'           => 'このプラグインはアップデートできません',
    'up_to_date'          => 'このプラグインはアップデートされています',
    'update_to'           => 'アップデート',
    'need_upgrade'        => 'アップグレードが必要です。Geeklog v',
    'before_update'       => 'アップデートする前に',
    'not_available'       => 'プラグインはこのリポジトリにありません',
    'ask_author'          => 'このプラグインはこの機能をサポートしていません。所有者に問い合わせてください。',
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
	'emails'  => 'ログを送信するメールリスト (必要なメールリストをカンマ区切りで)',
    'repository'  => 'リポジトリーオーナーの名前は Github (default is Geeklog-Plugins)です。この機能を無効にするにはブランクのままにしておいてください。'
)

?>
