<?php

// +---------------------------------------------------------------------------+
// | TinyMCE Plugin for Geeklog - The Ultimate Weblog                          |
// +---------------------------------------------------------------------------+
// | geeklog/plugins/tinymce/language/japanese_utf-8.php                       |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010-2011 mystral-kk - geeklog AT mystral-kk DOT net        |
// |                                                                           |
// | Constructed with the Universal Plugin                                     |
// +---------------------------------------------------------------------------|
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
// | along with this program; if not, write to the Free Software               |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA|
// |                                                                           |
// +---------------------------------------------------------------------------|

if (strpos(strtolower($_SERVER['PHP_SELF']), strtolower(basename(__FILE__))) !== FALSE) {
    die('This file can not be used on its own!');
}

$LANG_TMCE = array (
    'plugin'            		=> 'TinyMCE',
	'admin'		        		=> 'TinyMCE',
	'admin_head'				=> 'TinyMCEの設定',
	'admin_desc_config'			=> 'TinyMCEで使用するプラグインとボタンをGeeklogのグループ単位で設定します。設定を追加するには、上の「設定作成」をクリックしてください。ユーザが所属するグループが複数ある場合、後から定義したものが有効になります。なお、グローバルな設定は、<a href="' . $_CONF['site_admin_url'] . '/configuration.php">コンフィギュレーション</a>で行います。',
	'admin_desc_template'		=> 'TinyMCEで使用するテンプレートを編集します。テンプレートを追加するには、上の「テンプレート作成」をクリックしてください。なお、グローバルな設定は、<a href="' . $_CONF['site_admin_url'] . '/configuration.php">コンフィギュレーション</a>で行います。',
	'admin_menu_list'			=> '一覧',
	'admin_menu_new'			=> '新規作成',
	'admin_menu_change'			=> '変更',
	'admin_menu_config_list'	=> '設定一覧',
	'admin_menu_config_edit'	=> '設定作成',
	'admin_menu_template_list'	=> 'テンプレート一覧',
	'admin_menu_template_edit'	=> 'テンプレート作成',
	'admin_menu_cc'				=> '管理画面',
	'admin_edit'				=> '編集',
	'admin_title'				=> 'タイトル',
	'admin_theme'				=> 'テーマ',
	'admin_toolbars'			=> 'ツールバー',
	'admin_avaiable_buttons'	=> '有効なボタン一覧',
	'admin_disabled_buttons'	=> '無効なボタン一覧',
	'admin_plugins'				=> 'プラグイン',
	'admin_tb_perms'			=> 'tinyBrowserの設定',
	'admin_tb_allow_upload'		=> 'アップロードを許可する',
	'admin_tb_allow_edit' 		=> '編集を許可する',
	'admin_tb_allow_delete'	 	=> '削除を許可する',
	'admin_tb_allow_folders'	=> 'フォルダを許可する',
	'admin_enter_function'		=> 'ENTERキーの機能',
	'admin_ef_paragraph'		=> '<p>タグ挿入',	// = 0
	'admin_ef_newline'			=> '<br>タグ挿入',	// = 1
	'admin_grp_name'			=> 'グループ',
	'admin_submit'				=> '送信',
	'admin_delete'				=> '削除',
	'admin_confirm_delete'		=> '本当に削除してもよいですか?',
	'admin_error'				=> 'エラーが発生しました。不正アクセスないし、セキュリティトークン失効が原因です。このエラーは記録されました。',
	'admin_config_help'			=> 'ツールバーのボタンとプラグインの設定に関しては、<a href="%s">install.html</a>をご覧ください。',
	'admin_no_template'			=> 'テンプレートがありません。',
	'admin_description'			=> '説明',
	'admin_content'				=> '内容',
	'admin_updated'				=> '更新日',
	'admin_msg1'				=> '設定を削除しました。',
	'admin_msg2'				=> '設定を保存しました。',
	'admin_msg3'				=> 'テンプレートを削除しました。',
	'admin_msg4'				=> 'テンプレートを保存しました。',
	
	'abbr'				=> '略語',
	'absolute'			=> '絶対位置指定の切替',
	'acronym'			=> '頭字語',
	'advhr'				=> '水平線',
	'advimage'			=> '画像の挿入/編集',
	'advlink'			=> 'リンクの挿入/編集',
	'advlist'			=> 'リスト',
	'anchor'			=> 'アンカーの挿入/編集',
	'attribs'			=> '属性の挿入/編集',
	'autoresize'		=> '自動リサイズ',
	'autosave'			=> '自動保存',
	'backcolor'			=> '背景色',
	'bbcode'			=> 'BBコード',
	'blockquote'		=> '引用',
	'bold'				=> '太字',
	'bullist'			=> '番号なしリスト',
	'charmap'			=> '特殊文字',
	'cite'				=> '引用',
	'cleanup'			=> 'コード整形',
	'code'				=> 'HTMLソース編集',
	'contextmenu'		=> 'コンテキストメニュー',
	'copy'				=> 'コピー',
	'cut'				=> '切り取り',
	'del'				=> '削除',
	'directionality'	=> '文字の方向',
	'emotions'			=> '表情アイコン',
	'fontselect'		=> 'フォント',
	'fontsizeselect'	=> 'フォントサイズ',
	'forecolor'			=> '文字色',
	'formatselect'		=> '段落',
	'fullpage'			=> 'フルページ',
	'fullscreen'		=> 'フルスクリーン',
	'help'				=> 'ヘルプ',
	'hr'				=> '水平線',
	'iespell'			=> 'スペルチェック（IE用）',
	'image'				=> '画像の挿入/編集',
	'indent'			=> 'インデント',
	'inlinepopups'		=> 'インラインポップアップ',
	'ins'				=> '挿入',
	'insertdate'		=> '日付の挿入',
	'insertdate'		=> '日付・時刻の挿入',
	'insertlayer'		=> '新規レイヤーの挿入',
	'inserttime'		=> '時刻の挿入',
	'italic'			=> '斜体',
	'justifycenter'		=> '中央揃え',
	'justifyfull'		=> '均等割付',
	'justifyleft'		=> '左揃え',
	'justifyright'		=> '右揃え',
	'layer'				=> 'レイヤー',
	'legacyoutput'		=> 'legacyoutput',
	'link'				=> 'リンクの挿入/編集',
	'ltr'				=> '左から右',
	'media'				=> '埋め込みメディアの挿入/編集',
	'movebackward'		=> '背面へ移動',
	'moveforward'		=> '前面へ移動',
	'newdocument'		=> '新規作成',
	'nonbreaking'		=> '改行なしスペースの挿入',
	'noneditable'		=> 'noneditable',
	'numlist'			=> '番号つきリスト',
	'outdent'			=> 'インデント解除',
	'pagebreak'			=> '改ページの挿入',
	'paste'				=> '貼り付け',
	'pastetext'			=> 'テキストとして貼り付け',
	'pasteword'			=> 'Wordから貼り付け',
	'preview'			=> 'プレビュー',
	'print'				=> '印刷',
	'redo'				=> 'やり直す',
	'removeformat'		=> 'フォーマット解除',
	'replace'			=> '検索/置換',
	'restoredraft'		=> '自動保存前に戻す',
	'rtl'				=> '右から左',
	'save'				=> '保存',
	'search'			=> '検索',
	'searchreplace'		=> '検索/置換',
	'spellchecker'		=> 'スペルチェック',
	'strikethrough'		=> '打消し線',
	'style'				=> 'スタイル',
	'styleprops'		=> 'CSS編集',
	'styleselect'		=> 'スタイル',
	'sub'				=> '下付き',
	'sup'				=> '上付き',
	'tabfocus'			=> 'タブフォーカス',
	'table'				=> '表編集',
	'tablecontrols'		=> '表を挿入',
	'template'			=> 'テンプレートの挿入',
	'tinybrowser'		=> 'tinyBrowser',
	'underline'			=> '下線',
	'undo'				=> '元に戻す',
	'unlink'			=> 'リンク解除',
	'visualaid'			=> 'ガイドラインと非表示項目の表示切替',
	'visualchars'		=> '制御文字の表示',
	'wordcount'			=> '語数カウント',
	'xhtmlxtras'		=> 'xhtmlxtras',
	
	'emojiau'			=> 'au絵文字',
	'emojidocomo'		=> 'docomo絵文字',
	'emojisoftbank'		=> 'softbank絵文字',
);

// Localization of the Admin Configuration UI
$LANG_configsections['tinymce'] = array(
    'label' => $LANG_TMCE['plugin'],
    'title' => $LANG_TMCE['plugin'] . 'の設定',
);

$LANG_confignames['tinymce'] = array(
    'targets'					=> '対象textareaタグ',
	'target_class'				=> 'CSSクラス名',
	'target_ids'				=> 'CSS ID',
	'height'					=> 'エディタのウィンドウサイズ(縦)',
	'width'						=> 'エディタのウィンドウサイズ(横)',
	
	'tb_unixpermissions'		=> 'フォルダ作成時のパーミッション(8進数、先頭の0なし)',
	'tb_cleanfilename'			=> 'ファイル名をチェックする',
	'tb_filetype_image'			=> '画像ファイルの拡張子',
	'tb_filetype_media'			=> 'メディアファイルの拡張子',
	'tb_prohibited'				=> 'アップロード禁止ファイルの拡張子',
	
	'tb_maxsize_image'			=> '画像ファイル(0で制限なし)',
	'tb_maxsize_media'			=> 'メディアファイル(0で制限なし)',
	'tb_maxsize_file'			=> 'その他のファイル(0で制限なし)',
	
	'tb_imagequality'			=> '画質(1～99)',
	'tb_imageresize_width'		=> 'サイズ(横)(0でリサイズなし)',
	'tb_imageresize_height'		=> 'サイズ(縦)(0でリサイズなし)',
	
	'tb_thumbsize'				=> 'サムネールのサイズ(px)',
	'tb_thumbquality'			=> 'サムネールの画質(1～99)',
	
	'tb_window_width'			=> 'ポップアップウィンドウのサイズ(横)',
	'tb_window_height'			=> 'ポップアップウィンドウのサイズ(縦)',
	'tb_pagination'				=> '1ページあたりのアイテム数(0でページ分割なし)',
	'tb_dateformat'				=> 'タイムスタンプのフォーマット',
);

$LANG_configsubgroups['tinymce'] = array(
    'sg_main'					=> 'TinyMCEの設定',
	'sg_tinybrwoser'			=> 'TinyBrowserプラグインの設定',
);

$LANG_fs['tinymce'] = array(
    'fs_main'					=> 'TinyMCEの主要設定',
    'fs_tb_upload'				=> 'アップロードの設定',
	'fs_tb_filesize'			=> 'ファイルサイズの設定',
	'fs_tb_resize'				=> 'リサイズの設定',
	'fs_tb_thumb'				=> 'サムネールの設定',
	'fs_tb_appearance'			=> '表示設定',
);

// Note: entries 0, 1, 9, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['tinymce'] = array(
    0 => array('はい' => 1, 'いいえ' => 0),
    1 => array('はい' => TRUE, 'いいえ' => FALSE),
    9 => array('Forward to page' => 'item', 'Display List' => 'list', 'Display Home' => 'home', 'Display Admin' => 'admin'),
	12 => array('自動選択' => 'auto', 'すべてのtextareaタグ' => 'all', '特定のtextareaタグ(CSSクラス名指定)' => 'css_class', '特定のtextareaタグ(CSS ID指定)' => 'css_id'),
);
