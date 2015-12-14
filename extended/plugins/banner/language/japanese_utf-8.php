<?php

###############################################################################
# japanese_utf-8.php
#
# This is the Japanese language file for the Geeklog Banner Plugin
#
# Copyright (C) 2009-2010 Hiroron - hiroron AT hiroron DOT com
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
###############################################################################

global $LANG32, $LANG_ADMIN, $LANG_ACCESS;

/**
* the banner plugin's lang array
*
* @global array $LANG_BANNER
*/
$LANG_BANNER = array(
    10 => '投稿',
    14 => 'バナー',
    84 => 'バナー',
    88 => '新しいバナーはありません',
    114 => 'バナー',
    116 => 'バナー投稿',
    117 => 'バナー切れをご報告ください。',
    118 => 'バナー切れの報告',
    119 => '次のバナーは切れていると報告されました： ',
    120 => 'バナーの編集は、ここをクリック： ',
    121 => 'バナー切れの報告者： ',
    122 => 'バナー切れをご報告いただきありがとうございます。できるだけ速やかに修正いたします。',
    123 => 'ありがとうございます。',
    124 => '表示',
    125 => 'カテゴリ',
    126 => '現在の位置：',
    'root' => 'トップ' // title used for top level category
);

###############################################################################
# for stats
/**
* the banner plugin's lang stats array
*
* @global array $LANG_BANNER_STATS
*/
$LANG_BANNER_STATS = array(
    'banner' => 'バナー数（クリック数）',
    'stats_headline' => 'バナー(上位10件)',
    'stats_page_title' => 'バナー',
    'stats_hits' => 'クリック',
    'stats_no_hits' => 'このサイトにはバナーがないか、クリックした人がいないかのどちらかのようです。',
);

###############################################################################
# for the search
/**
* the banner plugin's lang search array
*
* @global array $LANG_BANNER_SEARCH
*/
$LANG_BANNER_SEARCH = array(
 'results' => 'バナーの検索結果',
 'title' => 'タイトル',
 'date' => '投稿した日時',
 'author' => '投稿者',
 'hits' => 'クリック数'
);

###############################################################################
# for the submission form
/**
* the banner plugin's lang submit form array
*
* @global array $LANG_BANNER_SUBMIT
*/
$LANG_BANNER_SUBMIT = array(
    1 => 'バナーを投稿する',
    2 => 'バナー',
    3 => 'カテゴリ',
    4 => 'その他',
    5 => '新しいカテゴリ名',
    6 => 'エラー：カテゴリを選んでください',
    7 => '「その他」を選択する場合には新しいカテゴリ名を記入してください。',
    8 => 'タイトル',
    9 => 'URL',
    10 => 'カテゴリ',
    11 => 'バナーの投稿申請'
);

###############################################################################
# Messages for COM_showMessage the submission form

$PLG_banner_MESSAGE1 = "{$_CONF['site_name']}にバナーを登録していただき、ありがとうございます。このバナーは承認のためにスタッフに送られました。承認されますと、あなたのバナーは<a href={$_CONF['site_url']}/banner/index.php>バナーセクション</a>に表示されます。";
$PLG_banner_MESSAGE2 = 'バナーは保存されました。';
$PLG_banner_MESSAGE3 = 'バナーは削除されました。';
$PLG_banner_MESSAGE4 = "{$_CONF['site_name']}にバナーを登録していただき、ありがとうございます。<a href={$_CONF['site_url']}/banner/index.php>バナー</a>セクションでご覧いただけます。";
$PLG_banner_MESSAGE5 = "あなたには、このカテゴリを見るための十分なアクセス権がありません。";
$PLG_banner_MESSAGE6 = 'あなたには、このカテゴリを編集する十分な権利がありません。';
$PLG_banner_MESSAGE7 = 'カテゴリの名前と説明を入力してください。';

$PLG_banner_MESSAGE10 = 'カテゴリは保存されました。';
$PLG_banner_MESSAGE11 = 'カテゴリ IDを「site」または「user」に設定することはできません。これらは内部で使用するために予約されています。';
$PLG_banner_MESSAGE12 = 'あなたは、編集中のカテゴリ自身のサブカテゴリを、親カテゴリに設定しようとしています。これは孤立するカテゴリを作成することになりますので、先に子カテゴリまたはカテゴリを、より高いレベルへ移動させてください。';
$PLG_banner_MESSAGE13 = 'カテゴリは削除されました。';
$PLG_banner_MESSAGE14 = 'カテゴリはバナーやカテゴリを含んでいます。先にそれらを取り除いてください。';
$PLG_banner_MESSAGE15 = 'あなたには、このカテゴリを削除する十分な権利がありません。';
$PLG_banner_MESSAGE16 = 'そのようなカテゴリは存在しません。';
$PLG_banner_MESSAGE17 = 'このカテゴリIDはすでに使われています。';

// Messages for the plugin upgrade
$PLG_banner_MESSAGE3001 = 'プラグインのアップグレードはサポートされていません。';
$PLG_banner_MESSAGE3002 = $LANG32[9];

###############################################################################
# admin/banner.php
/**
* the banner plugin's lang admin array
*
* @global array $LANG_BANNER_ADMIN
*/
$LANG_BANNER_ADMIN = array(
    1 => 'バナーの編集',
    2 => 'バナーID',
    3 => 'タイトル',
    4 => 'URL',
    5 => 'カテゴリ',
    6 => '(http://を含む)',
    7 => 'その他',
    8 => 'クリック数',
    9 => '説明',
    10 => 'タイトル、説明の入力が必要です',
    11 => 'バナー管理',
    12 => 'バナーの編集・削除は編集アイコンをクリック、バナーまたはカテゴリの作成は上の「バナーの作成」または「カテゴリの作成」をクリックしてください。マルチカテゴリを編集する場合は、上の「カテゴリの編集」をクリックしてください。',
    14 => 'カテゴリ',
    16 => 'アクセスが拒否されました',
    17 => "権限のないバナーにアクセスしようとしましたのでログに記録しました。<a href=\"{$_CONF['site_admin_url']}/plugins/banner/index.php\">バナーの管理画面に戻って</a>ください。",
    20 => 'その他を指定',
    21 => '保存',
    22 => 'キャンセル',
    23 => '削除',
    24 => 'バナー先が見つかりません',
    25 => '編集対象のバナーが見つかりませんでした.',
    26 => 'バナーの確認',
    27 => 'HTMLステータス',
    28 => 'カテゴリの編集',
    29 => '以下の項目を入力または編集してください。',
    30 => 'カテゴリ',
    31 => '説明',
    32 => 'カテゴリID',
    33 => '話題',
    34 => '親カテゴリ',
    35 => 'すべて',
    40 => 'このカテゴリを編集する',
    41 => '子カテゴリを作成する',
    42 => 'このカテゴリを削除する',
    43 => 'サイトカテゴリ',
    44 => '子カテゴリの追加',
    46 => 'ユーザ %s は、アクセス権限がないカテゴリを削除しようとしました。',
    50 => 'カテゴリのリスト',
    51 => 'バナーの作成',
    52 => 'カテゴリの作成',
    53 => 'バナーのリスト',
    54 => 'カテゴリの管理',
    55 => '以下のカテゴリを編集してください。 バナーやカテゴリを含むカテゴリは削除できません。先にこれらを削除するか、ほかのカテゴリに移す必要があります。',
    56 => 'カテゴリの編集',
    57 => 'まだ確認されていません。',
    58 => 'バナーの確認',
    59 => '<p>表示されている全てのバナーを確認する場合は、下の「バナーの確認」をクリックしてください。この処理はバナーの数に応じてかなりの時間がかかるかもしれません。</p>',
    60 => 'ユーザ %s は権限なしにカテゴリ %s を編集しようとしました。',
    61 => '掲載開始日時',
    62 => '掲載終了日時',
    63 => '(YYYY/MM/DD hh:mm:ss)'
);

$LANG_BANNER_STATUS = array(
    100 => "継続",
    101 => "プロトコル切替",
    200 => "OK",
    201 => "作成",
    202 => "受理",
    203 => "信頼できない情報",
    204 => "内容なし",
    205 => "内容のリセット",
    206 => "部分的内容",
    300 => "複数の選択",
    301 => "永久に移動した",
    302 => "発見した",
    303 => "他を参照せよ",
    304 => "未更新",
    305 => "プロキシを使用せよ",
    307 => "一時的リダイレクト",
    400 => "リクエストが不正である",
    401 => "認証が必要である",
    402 => "支払いが必要である",
    403 => "禁止されている",
    404 => "未検出",
    405 => "許可されていないメソッド",
    406 => "受理できない",
    407 => "プロキシ認証が必要である",
    408 => "リクエストタイムアウト",
    409 => "矛盾",
    410 => "消滅した",
    411 => "長さが必要",
    412 => "前提条件で失敗した",
    413 => "リクエストエンティティが大きすぎる",
    414 => "リクエストURIが大きすぎる",
    415 => "サポートしていないメディアタイプ",
    416 => "リクエストしたレンジは範囲外にある",
    417 => "期待するヘッダに失敗",
    500 => "サーバ内部エラー",
    501 => "実装されていない",
    502 => "不正なゲートウェイ",
    503 => "サービス利用不可",
    504 => "ゲートウェイタイムアウト",
    505 => "サポートしていないHTTPバージョン",
    999 => "接続がタイムアウト"
);


// Localization of the Admin Configuration UI
$LANG_configsections['banner'] = array(
    'label' => 'バナー',
    'title' => 'バナーの設定'
);

$LANG_confignames['banner'] = array(
    'bannerloginrequired' => 'ログインを要求する',
    'bannersubmission' => 'バナーの投稿を管理者が承認する',
    'newbannerinterval' => '新規バナーと見なす期間',
    'bannertemplatevariables' => 'テンプレートでバナーを表示する',
    'hidenewbanner' => '新着情報ブロックに表示しない',
    'hidebannermenu' => 'メニューに表示しない',
    'bannercols' => 'カテゴリの表示カラム数',
    'bannerperpage' => 'ページあたりのバナー数',
    'show_top10' => 'トップ10を表示する',
    'notification' => 'メールで通知する',
    'delete_banner' => '所有者の削除と共に削除する',
    'aftersave' => 'バナー保存後の画面遷移',
    'show_category_descriptions' => 'カテゴリの説明を表示する',
    'root' => 'トップカテゴリのID',
    'admin_editlink' => 'バナー管理者にはバナー横に編集アイコンを表示する',
    'admin_disptitle' => $LANG_ADMIN['title'] . '項目を一覧に表示',
    'admin_dispdescription' => $LANG_BANNER_ADMIN[9] . '項目を一覧に表示',
    'admin_dispaccess' => $LANG_ACCESS['access'] . '項目を一覧に表示',
    'admin_dispcategory' => $LANG_BANNER_ADMIN[14] . '項目を一覧に表示',
    'admin_disppublishstart' => $LANG_BANNER_ADMIN[61] . '項目を一覧に表示',
    'admin_disppublishend' => $LANG_BANNER_ADMIN[62] . '項目を一覧に表示',
    'admin_disphits' => $LANG_BANNER_ADMIN[8] . '項目を一覧に表示',
    'default_permissions' => 'パーミッション'
);

$LANG_configsubgroups['banner'] = array(
    'sg_main' => 'メイン'
);

$LANG_fs['banner'] = array(
    'fs_public' => 'バナーの表示',
    'fs_admin' => 'バナーの管理',
    'fs_adminlist' => 'バナー管理画面',
    'fs_permissions' => 'バナーのデフォルトパーミッション（[0]所有者 [1]グループ [2]メンバー [3]ゲスト）'
);

// Note: entries 0, 1, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['banner'] = array(
    0 => array('はい' => 1, 'いいえ' => 0),
    1 => array('はい' => TRUE, 'いいえ' => FALSE),
    9 => array('バナー先サイトを表示する' => 'item', 'バナー管理を表示する' => 'list', '公開バナーリストを表示する' => 'plugin', 'ホームを表示する' => 'home', '管理画面トップを表示する' => 'admin'),
    12 => array('アクセス不可' => 0, '表示' => 2, '表示・編集' => 3)
);

?>