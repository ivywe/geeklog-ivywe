<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.2                                                           |
// +---------------------------------------------------------------------------+
// | japanese_utf-8.php                                                        |
// |                                                                           |
// | Japanese language file                                                    |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben                                                            |
// | Translated:: Ivy                                                          |
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
* @package Maps
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

$LANG_MAPS_1 = array(
    'plugin_name'           => 'Maps',
    'plugin_doc'            => 'インストール、アップグレードと利用方法のドキュメントは',
    'map'                   => 'マップ',
    'online'                => 'こちら',
    'google_maps_API'       => '<a href="">Google Maps API</a>',
    'need_google_api'       => 'まずはじめに、<a href="http://code.google.com/apis/maps/signup.html" target="_blank">Google Maps API</a>をコンフィギュレーションにセットしてください。',
    'profile_title'         => '位置情報',
    'buy_marker'            => 'マーカーを購入',
    'menu_label'            => 'マップ管理',
    'admin_home'            => 'ホーム',
    'user_home'             => 'すべてのマップ',
    'maps'                  => 'マップ',
    'markers'               => 'マーカー',
    'maps_label'            => 'マップ',
    'create_map'            => 'マップを作成する',
    'create_marker'         => 'マーカーを作成する',
    'map_edit'              => 'マップ編集:',
    'marker_edit'           => 'マーカー編集:',
    'deletion_succes'       => '削除成功',
    'deletion_fail'         => '削除失敗',
    'error'                 => 'エラー',
    'save_fail'             => '保存エラー',
    'save_success'          => '保存完了',
    'missing_field'         => '未入力フィールドがあります...',
    'geocoder'              => 'Geoコーダー',
    'geocoder_text'         => '住所を入力してください。そしてマーカーをロケーションにドラッグしてください。緯度・軽度が表示されます。',
    'go'                    => '実行!',
    'name_label'            => 'マップ名: ',
    'marker_name_label'     => 'マーカー名: ',
    'description_label'     => '詳細:',
    'ok_button'             => 'Ok',
    'edit_button'           => '編集',
    'save_button'           => '保存',
    'delete_button'         => '削除',
    'yes'                   => 'はい',
    'no'                    => 'いいえ',
    'required_field'        => '入力必須',
    'address_label'         => '住所: ',
    'message'               => 'メッセージ',
    'general_settings'      => '一般設定',
    'map_width'             => 'マップの横幅 (% または px, 最小 550px): ',
    'map_height'            => 'マップの高さ (px のみ, 最小 350px): ',
    'map_zoom'              => 'マップズーム (0-21): ',
    'map_type'              => 'マップタイプ: ',
    'active'                => 'マップ有効: ',
    'hidden'                => 'マップ非表示: ',
    'marker_active'         => 'マーカー有効: ',
    'marker_hidden'         => 'マーカー非表示: ',
    'free_marker'           => 'フリーマーカー許可: ',
    'paid_marker'           => 'ペイドマーカー許可: ',
    'error_address_empty'   => '住所を先に入力してください。',
    'error_invalid_address' => 'この住所をただしく認識できません。市区町村名と番地はただしいでしょうか?',
    'error_google_error'    => '処理できませんでした。もう一度処理してください。',
    'error_no_map_info'     => '残念ながらマップ情報を表示できません。',
    'need_directions'       => '方向が必要ですか? あなたの住所を入力してください:',
    'get_directions'        => '方向をGET  ',
    'maps_list'             => 'マップリスト',
    'you_can'               => '',
    'user_maps_list'        => 'データベースにマップリストがあります。',
    'markers_list'          => 'マーカーリスト',
    'no_map'                => 'マップがありません。マップを作成した後マーカーを登録してください。',
    'no_map_user'           => '残念ながら有効なマップがデータベースに登録されていません。',
    'value_directions'      => '例） 国名　市区町村名　番地', // No quote here please
    'id'                    => 'ID',
    'name'                  => '名称',
    'description'           => '詳細',
    'active_field'          => '有効',
    'hidden_field'          => '非表示',
    'title_display'         => 'マップページを表示',
    'map_header_label'      => '任意のマップヘッダ',
    'map_footer_label'      => '任意のマップフッタ',
    'header_footer'         => 'ヘッダとフッタ',
    'informations'          => 'インフォメーション',
    'must_belong_to'        => 'マップへのアクセスに必要なグループ登録:',
    'private_access'        => 'プライベートアクセス',
    'marker_label'          => 'マーカー',
    'primary_color_label'   => 'プライマリーカラー',
    'stroke_color_label'    => 'ストロークカラー',
    'label'                 => 'ラベル',
    'label_color'           => 'ラベルカラー',
    'black'                 => 'ブラック',
    'white'                 => 'ホワイト',
    'payed'                 => 'ペイドマーカー:',
    'lat'                   => '緯度:',
    'lng'                   => '経度:',
    'ressources_tab'        => 'リソースタブ',
    'presentation'          => 'プレゼンテーション',
    'ressources'            => 'リソース',
    'presentation_tab'      => 'プレゼンテーションタブ',
    'empty_ressources'      => 'リソースラベルが空です。少なくともひとつ、登録してください。コンフィギュレーションを参照してください。',
    'empty_for_geo'         => '住所からの自動計測をするなら、緯度経度は空白にしてください。',
    'select_marker_map'     => 'マーカーを表示するマップを選んでください。',
    'remark'                => '注意',
    'marker_created'        => 'マーカー作成:',
    'map_created'           => 'マップ作成:',
    'modified'              => '最終編集日:',
    'marker_validity'       => '有効期限を利用:',
    'maps_empty'            => '先にマップを作成してください。',
    'from'                  => 'From:',
    'to'                    => 'To:',
    'date_issue'            => '終了タグが開始タグの前にあります。すぐチェックしてください!',
    'max_char'              => '最大文字数。',
    'street_label'          => '番地:',
    'code_label'            => '〒:',
    'city_label'            => '市:',
    'state_label'           => '都道府県:',
    'country_label'         => '国:',
    'tel_label'             => 'Tel:',
    'fax_label'             => 'Fax:',
    'web_label'             => 'Web:',
    'not_use_see_config'    => '利用していません。コンフィギュレーションを参照してください。',
    //global maps
    'global_map'            => 'グローバルマップ',
    'info_global_map'       => 'これはひとつのマップにすべてのマーカーを表示したものです。',
    'users_map'             => 'サイトユーザのマップ',
    'info_users_map'        => 'これはサイトユーザのマップです。あなたのプロフィールの位置情報を追加することで表示されます。',
    //Submission
    'address'               => '住所',
    'created'               => '日時',
    'submit_marker'         => 'マーカーを投稿する',
    'submit_marker_text'    => '<p><ol><li>ロケーションマーカーを登録してください。<li>すべてのフィールドを入力してください。<li>投稿が承認されるまでお待ちください。</ol></p>',
    'markers_submissions'   => 'マーカーを登録',
    'submission_disabled'   => 'マーカーの登録申請ができませんでした。',
    'go'                    => '入力した住所の位置情報を取得する',
    //date and hits
    'last_modification'     => '最終編集日時:',
    'hits'                  => 'ヒット',
    //user marker
    'member'                => 'メンバー',
    'location'              => '位置: ',
    'regdate'               => 'メンバー登録日: ',
    'about'                 => 'アバウト',
    'my_markers'            => 'マイマーカー',
    'payed_label'           => 'ペイド',
    'from_label'            => 'バリディティ from',
    'to_label'              => 'バリディティ to',
    'no_marker'             => 'マーカーが未登録のようです。サイトのミスだと思われる場合にはサイトの管理者にお問い合わせください。',
    'marker_detail'         => 'マーカー詳細',
    'admin_can'             => 'マップ管理者としてあなたは',
    'create_map'            => 'マップ新規作成',
    'set_user_geo'          => 'ユーザーのGeoをセット',
    'set_geo_location'      => 'システムチェックとすべてのGeoロケーションのセット OK',
    'records'               => 'レコード',
    'report'                => 'マーカーレポート',
    'report_subject'        => 'マーカーのレポート',
    'edit_marker_text'      => '<p><ol><li>ロケーションマーカーをセット<li>入力フィールドに入力<li>それでＯＫです。</ol></p>',
    'admin'                 => '管理画面',
    'category_label'        => 'カテゴリ:',
    'choose_category'       => '-- カテゴリを選択 --',
    'categories'            => 'カテゴリ',
    'categories_list'       => 'カテゴリリスト',
    'cat_edit'              => 'カテゴリ編集:',
    'cat_name_label'        => 'カテゴリ名:',
    'create_cat'            => 'カテゴリ新規作成',
    'field_list'            => 'フィールドリスト',
    'addfield'              => 'フィールドを追加',
    'field_name'            => 'フィールド名',
    'field_order'           => '順番',
    'field_autotag'         => '自動タグ',
    'field_rights'          => 'パーミッション',
    'field_edit'            => '編集',
    'valid'                 => '有効',
    'editing_field'         => 'フィールド編集',
    'category'              => 'カテゴリ',
    'map_label'             => 'マップ',
    'colon'                 => ':', //Add space before and after if needed
    'view_map'              => 'マップを見る',
    'view_markers'          => 'マーカーを見る',
    'code'                  => '郵便番号',
    'city'                  => '市',
    'viewing_markers'       => 'ここはマップのマーカー',
    'details'               => '詳細',
    'view_details'          => '詳細をみる',
    'print'                 => 'プリント',
	'to_complete'           => '完了する',
	'autotag_desc_maps'     => '[maps: xx] - id=XX　を指定して マップを表示する。',
	'autotag_desc_geo'      => '[geo: map width:XX height:YY zoom:ZZ location] - 位置情報 (street, city, country)を中心としたマップを表示する。オプションは、幅と高さをピクセル指定 (例 400px) とズーム (0 から 21)。',
	'autotag_desc_marker'   => '[marker: xx] - マーカーを id=XX で指定して表示する。',
	//v1.1
	'marker_customisation'  => 'マーカーカスタマイズ',
	'mk_default'            => 'デフォルトのマーカーを使う',
	'overlays'              => 'オーバーレイ',
	'overlays_list'         => 'オーバーレイ一覧',
	'create_overlay'        => 'オーバーレイ新規作成',
	'edit_overlay_text'     => 'オーバーレイ編集:',
	'overlay_name_label'    => 'オーバーレイ名:',
	'overlay_presentation'  => 'オーバーレイ画像は緯度・経度を指定して張り付けられます。ドラッグ、ズームに応じてオーバーレイが切り替わります。',
	'overlay_active'        => 'オーバーレイアクティブ:',
	'zoom_min_label'        => 'ズーム 最小:',
	'zoom_max_label'        => 'ズーム 最大:',
	'image_message'         => '画像ファイルを選んでください。',
	'image_replace'         => '画像ファイルをアップロードして置き換え:',
	'image'                 => '画像',
	'sw_lat'                => '南西 緯度:',
    'sw_lng'                => '南西 経度:',
	'ne_lat'                => '北東 緯度:',
    'ne_lng'                => '北東 経度:',
	'overlay_not_writable'  => 'オーバーレイフォルダ書き込み化: オーバーレイ用のフォルダを作成して書き込み可能に設定してください。',
	'map_tab'               => 'マップ',
	'overlays_tab'          => 'オーバーレイ',
	'add_overlay'           => 'オーバーレイ追加',
	'remove_overlay'        => 'オーバーレイ削除',
	'overlay_label'         => 'オーバーレイ',
	'import_export'         => 'インポート/エクスポート',
	'import'                => 'インポート',
	'export'                => 'エクスポート',
	'select_file'           => '.csvファイルを選んでください。',
	'import_message'        => 'マーカーを追加したいマップを選んで, マーカーファイルを選び, データのセパレータとインポートしたいフィールドをチェックしてください。',
	'markers_added'         => 'マーカーをマップに追加:',
	'export_message'        => 'マーカーをエクスポートしたいマップを選んで, マーカーファイルを選び, データのセパレータとインポートしたいフィールドをチェックしてください。',
	'no_marker_to_export'   => 'このマップにはエクスポートするマーカーがありませんでした。',
	'icons'                 => 'アイコン',
	'icons_not_writable'    => 'アイコンフォルダ書き込み不可: アイコンフォルダを作成して書き込み可能にしてください。',
	'icons_list'            => 'アイコン一覧',
	'create_icon'           => 'アイコン新規作成',
	'icon_edit'             => 'アイコン編集',
	'icon_presentation'     => 'マーカーで表示するアイコンをアップロードできます。', 
	'icon_name_label'       => 'アイコン名',
	'xmarkers'              => 'マーカー',
	'1marker'               => 'マーカー',
	'choose_icon'           => 'このマーカーのアイコンを選べます。優先アイコンはカラーアイコンです。',
	'no_icon'               => 'アイコン無し',
	'separator'             => 'セパレータ',
	'markers_to_add'        => 'すべてのフィールドの値をチェックして、マーカーを追加するマップを選んでください。:',
	'choose_fields_import'  => 'フィールドを選んでインポート',
	'choose_fields_export'  => 'フィールドを選んでエクスポート',
	'checkall'              => 'すべてチェック',
	'order'                 => '順番',
	'move'                  => '移動',
	'name_missing'          => '少なくともCSVファイル名がありません。',
	'need_address'          => 'マーカー作成のためには少なくとも住所または緯度・経度が必要です。CSVファイルをチェックしてください。',
	'manage_groups'         => 'オーバーレイ管理',
	'create_group'          => 'オーバーレイグループを作成する',
	'group_edit'            => 'グループ編集:',
	'group_overlay_presentation' => 'オーバーレイグループの名前を編集できます。',
	'group_overlay_name_label'   => 'オーバーレイグループ名',
	'group_label'           => 'グループ (オプション)',
	'choose_group'          => 'グループ選択',
	'group'                 => 'グループ',
	//v1.3
	'geo_fail'              => '住所の入力エラーです。',
	'on_map'                => '地図上',
	'read_more'             => '詳しくはこちら',
	'from_map'              => '地図から',
	'show_hide_overlays'    => 'オーバーレイ 表示 / 非表示',
	'fields_presentation'   => 'カテゴリの編集またはフィールドを追加する。',
	'overlays_added'        => '地図にオーバーレイを追加する',
	'overlays_to_add'       => '地図にオーバーレイを追加',
	'marker_modification'   => 'マーカーの編集',
	'from_owner'            => 'から',
	'marker_limited'        => 'マーカー制限です',
	'events_map'            => 'イベントマップ',
	'info_events_map'       => '',
	'from_cal'              => 'From',
	'to_cal'                => 'to',
	'on_cal'                => 'On',
    //v1.4
    'configuration'         => 'コンフィギュレーション',

);

$LANG_MAPS_MESSAGE = array(
    'message'               => 'システムからのメッセージ',
    'add_new_field'         => 'フィールドが新たに作成されました。',
    'save_field'            => 'フィールドが新たに保存されました。',
    'delete_field'          => 'フィールドが削除されました。',
);

$LANG_MAPS_EMAIL = array(
    'hello_admin'           => 'ようこそ、管理者さん,',
    'new_marker'            => '承認待ちのマーカーが作成されました。',
    'name'                  => '名称:',
    'on_map'                => 'マップ上:',
    'submissions'           => '投稿: ',
    'marker_submissions'    => 'マーカーの投稿',
);

// Messages for the plugin upgrade
$PLG_maps_MESSAGE3002 = $LANG32[9]; // "requires a newer version of Geeklog"

$PLG_maps_MESSAGE1  = "マーカー登録ありがとう！ {$_CONF['site_name']}. 投稿はスタッフに通知されました。";
$PLG_maps_MESSAGE2  = "マーカーの投稿は終了しました。";
$PLG_maps_MESSAGE3  = "エラーです。マーカーを保存できません。";

/**
*   Localization of the Admin Configuration UI
*   @global array $LANG_configsections['maps']
*/
$LANG_configsections['maps'] = array(
    'label' => 'Maps',
    'title' => 'Maps'
);

/**
*   Configuration system prompt strings
*   @global array $LANG_confignames['maps']
*/
$LANG_confignames['maps'] = array(
    'maps_folder'           => 'マップの公開フォルダ',
    'hide_maps_menu'        => 'マップメニューを非表示',
    'maps_login_required'   => 'マップ　ログイン要求',
    'autofill_coord'        => '自動的に未入力緯度経度をセットする',
    'display_geo_profile'   => 'プロフィール geo ロケーリゼーション',
    'map_type_profile'      => 'プロフィールマップタイプ',
    'map_type_geotag'       => 'geo マップ自動タグ タイプ',
    'show_directions_geo'   => 'geo 自動タグに方向を表示する',
    'show_directions_profile' => 'プロフィールに方向を表示する',
    'map_width_geotag'      => 'geo マップ自動タグ 幅 (単位指定: % または px)',
    'map_height_geotag'     => 'geo マップ自動タグ 高さ (単位指定: px のみ)',
    'map_zoom_geotag'       => 'geo マップ自動タグ ズーム (0-21)',
    'map_width_profile'     => 'プロフィールマップの幅　(単位指定: % または px)',
    'map_height_profile'    => 'プロフィールマップの高さ (単位指定: px のみ)',
    'AdsOnMap'              => 'マップにgoogle adsを表示',
    'publisher_id'          => '公開 id',
    'channel_id'            => 'あなたのチャンネル',
    'maxAdsOnMap'           => 'マップ上の最大ads数', 
    'show_map'              => 'Google Mapを見る',
    'google_api_key'        => 'Google Maps API Key',
    'url_geocode'           => 'Google GeoコーディングサービスのURL',
    'map_width'             => 'マップの横幅のデフォルト(with % or px)',
    'map_height'            => 'マップの高さのデフォルト(with px only)',
    'map_zoom'              => 'マップズームのデフォルト(0-21)',
    'map_type'              => 'マップタイプのデフォルト',
    'default_permissions'   => 'パーミッションのデフォルト',
    'map_main_header'       => 'メインページヘッダ, autotag welcome',
    'map_main_footer'       => 'メインページフッタ, autotag welcome',
    'map_geo'               => 'すべてのプロフィールでマップを作成する',
    'map_markers'           => 'すべてのマーカーでマップを作成する',
    'map_active'            => 'マップは有効',
    'map_hidden'            => 'マップは非表示',
    'free_markers'          => 'マップはフリーマーカーを許可',
    'paid_markers'          => 'マップはペイドマーカーを許可 (Paypalプラグイン要)',
    'street'                => '番地情報を使用',
    'code'                  => '郵便番号情報を使用',
    'city'                  => '市区町村情報を使用',
    'state'                 => '都道府県情報を使用',
    'country'               => '国情報を使用',
    'tel'                   => 'TEL情報を使用',
    'fax'                   => 'FAX情報を使用',
    'web'                   => 'Web情報を使用',
    'item_1'                => 'リソース #1 label',
    'item_2'                => 'リソース #2 label',
    'item_3'                => 'リソース #3 label',
    'item_4'                => 'リソース #4 label',
    'item_5'                => 'リソース #5 label',
    'item_6'                => 'リソース #6 label',
    'item_7'                => 'リソース #7 label',
    'item_8'                => 'リソース #8 label',
    'item_9'                => 'リソース #9 label',
    'item_10'               => 'リソース #10 label',
    'label_color'           => 'ラベルカラー',
    'star_primary_color'    => 'スタープライマリーカラー',
    'star_stroke_color'     => 'スターストロークカラー',
    'marker_active'         => 'マーカー有効をデフォルト',
    'marker_hidden'         => 'マーカー非表示をデフォルト',
    'marker_payed'          => 'マーカーペイドをデフォルト',
    'marker_validity'       => 'マーカーバリディティをデフォルト',
    'monetize'              => 'マーカー収益化',
    'marker_submission'     => 'マーカー投稿を許可',
    'users_map'             => 'サイトユーザのアクティブマップ',
    'global_map'            => 'アクティブグローバルマップ',
    'global_type'           => 'グローバルマップ タイプ',    
    'global_width'          => 'グローバルマップ 横幅',
    'global_height'         => 'グローバルマップ 高さ',
    'global_zoom'           => 'グローバルマップ ズーム (0-21)',
    'detail_zoom'           => 'マーカー詳細 ズームm (0-21)',
    'submit_login_required' => 'マーカー投稿にログインが必要',
    'marker_edition'        => 'マーカー編集',
    'infos_label'           => 'インフォラベル (プロバージョン)',
	'use_cluster'           => 'マーカークラスターを利用する',
	'zoom_profile'          => 'ユーザープロファイルのマップのズーム (0-21)',
	'display_events_map'    => 'イベントマップを表示する',
);

/**
*   Configuration system subgroup strings
*   @global array $LANG_configsubgroups['maps']
*/
$LANG_configsubgroups['maps'] = array(
    'sg_main' => 'メイン設定',
    'sg_display' => '表示設定'
);

/**
*   Configuration system fieldset names
*   @global array $LANG_fs['maps']
*/
$LANG_fs['maps'] = array(
    'fs_main'            => '一般設定',
    'fs_ads'             => 'Google Ads設定',
    'fs_google'          => 'Google API設定',
    'fs_permissions'     => 'デフォルトパーミッション',
    'fs_display'         => 'マップ',
    'fs_global_map'      => 'グローバルマップ',
    'fs_display_profile' => 'プロフィール',
    'fs_display_geo'     => 'geo自動タグ',
    'fs_map_default'     => 'マップデフォルト設定',
    'fs_marker_default'  => 'マーカーデフォルト設定',
 );

/**
*   Configuration system selection strings
*   Note: entries 0, 1, and 12 are the same as in 
*   $LANG_configselects['Core']
*
*   @global array $LANG_configselects['maps']
*/
$LANG_configselects['maps'] = array(
    0 => array('はい' => 1, 'いいえ' => 0),
    1 => array('はい' => TRUE, 'いいえ' => FALSE),
    3 => array('はい' => 1, 'いいえ' => 0),
    4 => array('オン' => 1, 'オフ' => 0),
    5 => array('ページトップ' => 1, '注目記事の下' => 2, 'ページの下' => 3),
    10 => array('5' => 5, '10' => 10, '25' => 25, '50' => 50),
    11 => array('マイル' => 'miles', 'キロメータ' => 'km'),
    12 => array('アクセス不可' => 0, '表示' => 2, '表示・編集' => 3),
    20 => array('ノーマル' => 'G_NORMAL_MAP', 'サテライトマップ' => 'G_SATELLITE_MAP', '航空写真' => 'G_PHYSICAL_MAP ', '地図＋写真' => 'G_HYBRID_MAP', '地形' => 'G_AERIAL_HYBRID_MAP'),
    30 => array('白' => 1, '黒' => 0),
    31 => array('一時的' => 1, 'パーマネント' => 0),
);

?>
