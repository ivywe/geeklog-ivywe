<?php
###############################################################################
# plugins/assist/language/japanese_utf-8.php
# もし万一エンコードの種類が　UTF-8でない場合は、utf-8に変換してください。
# Last Update 20160819
###############################################################################
## 管理画面
$LANG_ASSIST_admin_menu = array();
$LANG_ASSIST_admin_menu['1']= '情報';
$LANG_ASSIST_admin_menu['2']= 'ユーザー関連';

$LANG_ASSIST_admin_menu['3']= '変数関連';
$LANG_ASSIST_admin_menu['4']= 'ニュースレター';

$LANG_ASSIST_admin_menu['5']= 'バックアップ＆リストア';

$LANG_ASSIST_admin_menu['8']= 'Proversion';

###############################################################################
$LANG_ASSIST= array(
    1 => 'もっと見る',
    2 => "{$_CONF['shortdate']}",

    999 => ''
);


$LANG_ASSIST['home_id']['sub'] = 'sub';
$LANG_ASSIST['home_id']['home'] = 'home';

$LANG_ASSIST['login_status'][0] = 'guest';
$LANG_ASSIST['login_status'][1] = 'member';

$LANG_ASSIST['login_logout'][0] = "
<a href=\"{$_CONF['site_url']}/users.php?mode=login\">\"ログイン\"></a>
";

$LANG_ASSIST['login_logout'][1] = "
<a href=\"{$_CONF['site_url']}/users.php?mode=loout\">\"ログアウト\"></a>
";
###############################################################################
$LANG_ASSIST_autotag_desc['newstories']="
[newstories:話題ID RSSファイル] - 新着記事（おしらせ）。<br".xhtml.">
詳細は、assistプラグインのドキュメントを参照してください。
";
$LANG_ASSIST_autotag_desc['newstories2']="
[newstories2:話題ID RSSファイルほか例1	参照] - 新着記事（おしらせ）。<br".xhtml.">
詳細は、assistプラグインのドキュメントを参照してください。
";

$LANG_ASSIST_autotag_desc['conf']="
[conf:変数名] - <br".xhtml.">
例1：[conf:site_url]<br".xhtml.">
例2：[conf:site_name]<br".xhtml.">
例3：[conf:site_mail]<br".xhtml.">
例4：[conf:site_slogan]<br".xhtml.">
詳細は、assistプラグインのドキュメントを参照してください。
";
$LANG_ASSIST_autotag_desc['assist']="
[assist:〜] - <br".xhtml.">	
[assist:usercount]ほか<br".xhtml.">
詳細は、assistプラグインのドキュメントを参照してください。
<a href=\"{$_CONF['site_admin_url']}/plugins/assist/docs/japanese/autotags.html\">*</a>
";


###########
$LANG_ASSIST['home']="HOME";
$LANG_ASSIST['view']="表示";
$LANG_ASSIST['articles']="記事一覧";


$LANG_ASSIST['topic_separater']=" / ";
$LANG_ASSIST['topic_separater_id']=" ";
###############################################################################
# admin/plugins/

$LANG_ASSIST_ADMIN['instructions'] =
'
';

$LANG_ASSIST_ADMIN['piname'] = 'Assist';
$LANG_ASSIST_ADMIN['edit'] = '編集';
$LANG_ASSIST_ADMIN['new'] = '新規登録';

$LANG_ASSIST_ADMIN['export'] = 'エクスポート';

$LANG_ASSIST_ADMIN['import'] = 'インポート';
$LANG_ASSIST_ADMIN['importfile'] = 'パス';
$LANG_ASSIST_ADMIN['importmsg_user'] =
"ユーザーをGeeklogに一括登録できます。<br".xhtml.">"
."一括登録するファイルはユーザーは1行あたり1人づつで、各ユーザーのデータはタブ区切りで"
."「ユーザーID、氏名、ユーザー名、メールアドレス」のフィールド順です。<br".xhtml.">"
."ユーザーID、ユーザー名、メールアドレスは、重複できません。<br".xhtml.">"
."ユーザーのパスワードは無作為に決定されます。<br".xhtml.">"
."ユーザーIDが0の場合は自動発番します。<br".xhtml.">"
."ユーザーへの登録通知はおこないません。<br".xhtml.">"
."ファイルはかならず本サイトと同じUTF-8コードのテキスト形式で保存してください。<br".xhtml.">"
;

$LANG_ASSIST_ADMIN['delete'] = '削除';
$LANG_ASSIST_ADMIN['deletemsg_user'] = "ユーザーを一括削除します。<br".xhtml.">";

$LANG_ASSIST_ADMIN['uidfrom'] = "開始ユーザーID";
$LANG_ASSIST_ADMIN['uidto'] = "終了ユーザーID";

$LANG_ASSIST_ADMIN['mail1'] = '送信実行';
$LANG_ASSIST_ADMIN['mail2'] = '送信設定';

$LANG_ASSIST_ADMIN['submit'] = '実行';

//newsletter
$LANG_ASSIST_ADMIN['mail_logfile'] ="ログファイル%sが無効になっています。<br".xhtml.">";

$LANG_ASSIST_ADMIN['mail_msg'] =
"送信用記事を話題「 %s 」であらかじめ用意してください。<br".xhtml.">"
."ニュースレターはテキスト形式に変換して送信します。<br".xhtml.">"
."テスト送信で送信内容を十分確認したうえでニュースレターを送信してください。<br".xhtml.">"
."※送信結果は、assist_newsletter.log に記録されます。<br".xhtml.">"
."予約送信は遅延する場合があります。<br".xhtml.">"
."（当サイトが誰にもアクセスされなかった場合）<br".xhtml.">"
;

$LANG_ASSIST_ADMIN['mail_msg1'] ="(1)送信内容を設定する";
$LANG_ASSIST_ADMIN['mail_msg2'] ="(2)テスト送信する";
$LANG_ASSIST_ADMIN['mail_msg3'] ="(3)送信先を指定する";
$LANG_ASSIST_ADMIN['mail_msg4'] ="(4)送信する";

$LANG_ASSIST_ADMIN['select_sid'] = '記事を選択してください';
$LANG_ASSIST_ADMIN['wkymlmguser_on'] = '（メルマガプラグインユーザーに送信できます）';
$LANG_ASSIST_ADMIN['wkymlmguser_off'] = '※登録者全員に送信するには、送信先を「Logged-in Users」としてください。';
$LANG_ASSIST_ADMIN['wkymlmguser_user'] = 'メルマガプラグインユーザー';

$LANG_ASSIST_ADMIN['fromname']="差出人";
$LANG_ASSIST_ADMIN['replyto']="差出人メールアドレス";
$LANG_ASSIST_ADMIN['sprefix']="SUBJECT接頭子";
$LANG_ASSIST_ADMIN['sid']="記事ID";

$LANG_ASSIST_ADMIN['toenv']="送信先環境";
$LANG_ASSIST_ADMIN['selectgroup']="送信先グループ";

$LANG_ASSIST_ADMIN['testto']="テスト送信先";
$LANG_ASSIST_ADMIN['sendto']="送信先ユーザーID範囲";
$LANG_ASSIST_ADMIN['sendto_remarks']="※範囲指定しない場合は、0〜0にしてください。";

$LANG_ASSIST_ADMIN['mail_test'] = 'テスト送信';
$LANG_ASSIST_ADMIN['mail_send'] = '即送信';

//backup&restore
$LANG_ASSIST_ADMIN['config'] = 'コンフィギュレーション';

$LANG_ASSIST_ADMIN['config_backup'] = 'バックアップ実行';
$LANG_ASSIST_ADMIN['config_backup_help'] = 'バックアップファイルを作成します';

$LANG_ASSIST_ADMIN['config_init'] = '初期化実行';
$LANG_ASSIST_ADMIN['config_init_help'] = '初期値に戻します ';

$LANG_ASSIST_ADMIN['config_restore'] = 'リストア実行';
$LANG_ASSIST_ADMIN['config_restore_help'] = 'バックアップファイルの内容に戻します ';

$LANG_ASSIST_ADMIN['config_update'] = '更新';
$LANG_ASSIST_ADMIN['config_update_help'] = '最新の仕様に更新します ';

//
$LANG_ASSIST_ADMIN['importform'] = 'インポート';
$LANG_ASSIST_ADMIN['exportform'] = 'エクスポート';

$LANG_ASSIST_ADMIN['seq'] = 'SEQ';

$LANG_ASSIST_ADMIN['tag'] = 'TAG';
$LANG_ASSIST_ADMIN['value'] = 'VALUE';

$LANG_ASSIST_ADMIN['must'] = '*必須';
$LANG_ASSIST_ADMIN['field'] = 'フィールド';
$LANG_ASSIST_ADMIN['fields'] = 'フィールド';

$LANG_ASSIST_ADMIN['udatetime'] = 'タイムスタンプ';
$LANG_ASSIST_ADMIN['uuid'] = '更新ユーザー';

$LANG_ASSIST_ADMIN['inpreparation'] = '(準備中)';
$LANG_ASSIST_ADMIN['markerclear'] = 'MAPS marker クリア';
$LANG_ASSIST_ADMIN['mapsmarker'] = 'MAPS marker';
$LANG_ASSIST_ADMIN['xml_def'] = 'XML定義';
$LANG_ASSIST_ADMIN['init'] = '初期化';
$LANG_ASSIST_ADMIN['list'] = '一覧';

$LANG_ASSIST_ADMIN['path'] = '絶対パス';
$LANG_ASSIST_ADMIN['url'] = 'URL';

$LANG_ASSIST_ADMIN['default'] = '既定値';
$LANG_ASSIST_ADMIN['importmsg'] = '
絶対パス（ディレクトリ、ファイル）またはURLを指定してください。<br'.XHTML.'>
ディレクトリ指定の時は、ディレクトリ下のxmlファイルをインポートします。<br'.XHTML.'>
logs/assist_xmlimport.log　にログが記録されます。<br'.XHTML.'>
';
$LANG_ASSIST_ADMIN['exportmsg'] = '
絶対パス（ディレクトリ）を指定してください。<br'.XHTML.'>
logs/assist_xmlimport.log　にログが記録されます。<br'.XHTML.'>
';

//
$LANG_ASSIST_ADMIN['document'] = 'ドキュメント';
$LANG_ASSIST_ADMIN['configuration'] = 'コンフィギュレーション設定';
$LANG_ASSIST_ADMIN['autotags'] = '自動タグ';
$LANG_ASSIST_ADMIN['online'] = 'オンライン';
$LANG_ASSIST_ADMIN['templatesetvar'] = 'テーマ変数';

//管理画面：このページについて
$LANG_ASSIST_ADMIN['about_admin_information'] = '　';
$LANG_ASSIST_ADMIN['about_admin_backuprestore'] = 'バックアップの作成とリストア';

//ERR
$LANG_ASSIST_ADMIN['msg'] = 'メッセージ';
$LANG_ASSIST_ADMIN['err'] = 'エラー';
$LANG_ASSIST_ADMIN['err_not_writable'] = 'ディレクトリが存在しないか書込できません';
$LANG_ASSIST_ADMIN['err_not_exist'] = 'ファイルがありません';
$LANG_ASSIST_ADMIN['err_empty'] = 'ファイルがありません';

$LANG_ASSIST_ADMIN['err_invalid'] = 'データがありません';
$LANG_ASSIST_ADMIN['err_permission denied'] = '許可されていません';

$LANG_ASSIST_ADMIN['err_id'] = 'IDが不正です';
$LANG_ASSIST_ADMIN['err_name'] = '名前が不正です';
$LANG_ASSIST_ADMIN['err_templatesetvar'] = 'テーマ変数が不正です';
$LANG_ASSIST_ADMIN['err_templatesetvar_w'] = 'テーマ変数はすでに使用されています';
$LANG_ASSIST_ADMIN['err_code_w'] = 'このコードはすでに登録されています';
$LANG_ASSIST_ADMIN['err_code'] = 'コードが入力されていません';
$LANG_ASSIST_ADMIN['err_title'] = 'タイトルが入力されていません';

$LANG_ASSIST_ADMIN['err_selection'] = '選択肢が入力されていません';

$LANG_ASSIST_ADMIN['err_modified'] = '編集日付が不正です';
$LANG_ASSIST_ADMIN['err_created'] = '作成日付が不正です';
$LANG_ASSIST_ADMIN['err_released'] = '公開日が不正です';
$LANG_ASSIST_ADMIN['err_expired'] = '公開終了日が不正です';

$LANG_ASSIST_ADMIN['err_checkrequried'] = ' 必ず入力してください';

$LANG_ASSIST_ADMIN['err_date'] = '日付が不正です';//@@@@@

$LANG_ASSIST_ADMIN['err_size'] = 'サイズが不正です';//@@@@@
$LANG_ASSIST_ADMIN['err_type'] = 'タイプが不正です';//@@@@@

$LANG_ASSIST_ADMIN['err_field_w'] = '当フィールドはすでに登録されています';


$LANG_ASSIST_ADMIN['err_backup_file_not_exist'] = 'バックアップファイルがありません';
$LANG_ASSIST_ADMIN['err_backup_file_non_rewritable'] = 'バックアップファイル書換できません';

$LANG_ASSIST_ADMIN['err_fromname'] = '差出人が登録されていません';
$LANG_ASSIST_ADMIN['err_replyto'] = '差出人メールアドレスが登録されていません';
$LANG_ASSIST_ADMIN['err_sid'] = '記事が選択されていません';
$LANG_ASSIST_ADMIN['err_testto'] = 'テスト送信先が正しく登録されていません';

$LANG_ASSIST_ADMIN['err_backup_file_not_exist'] = 'バックアップファイルがありません';
$LANG_ASSIST_ADMIN['err_backup_file_non_rewritable'] = 'バックアップファイル書換できません';

$LANG_ASSIST_ADMIN['err_marker_name'] = 'マーカー名が記述されていません'.LB;
$LANG_ASSIST_ADMIN['err_marker_address'] = 'マーカー住所が記述されていません'.LB;
$LANG_ASSIST_ADMIN['err_marker_coords'] = '緯度経度が計算できません'.LB;
$LANG_ASSIST_ADMIN['err_map'] = 'マップがありません'.LB;

$LANG_ASSIST_ADMIN['err_fbid'] = 
'Facebook OAuth Application IDが登録されていません。<br'.xhtml.'>'.LB
.'(コンフィギュレーション設定 Geeklog ユーザー)<br'.xhtml.'>'.LB
.'Facebook のソーシャルボタンの自動タグを使用する場合は必要です。<br'.xhtml.'>'.LB
;

//
$LANG_ASSIST_ADMIN['mail_save_ok'] = '保存しました';
$LANG_ASSIST_ADMIN['mail_test_message'] = 'これはテスト配信です。配信内容を確認してください。';
$LANG_ASSIST_ADMIN['mail_test_ok'] = 'テスト送信しました　';
$LANG_ASSIST_ADMIN['mail_test_ng'] = 'テスト送信できませんでした　';
$LANG_ASSIST_ADMIN['mail_send_success'] = '送信成功　';
$LANG_ASSIST_ADMIN['mail_send_failure'] = '送信失敗　';
// hiroron start 2010/07/13
$LANG_ASSIST_ADMIN['reserv_datetime']="送信予約";
$LANG_ASSIST_ADMIN['reserv_datetime_remarks']="※分けて送信しない場合は、0分おきにしてください。";

$LANG_ASSIST_ADMIN['mail_reserv'] = '予約送信';
$LANG_ASSIST_ADMIN['err_reserv'] = '送信予約時間が正しく選択されていません';
// hiroron end 2010/07/13
// hiroron start 2010/07/15
$LANG_ASSIST_ADMIN['reservlist_title']="送信予約状況";
$LANG_ASSIST_ADMIN['reservlist_no']="番号";
$LANG_ASSIST_ADMIN['reservlist_datetime']="日時";
$LANG_ASSIST_ADMIN['reservlist_range']="範囲(LAST送信ID)";
$LANG_ASSIST_ADMIN['reservlist_sid']="記事";
$LANG_ASSIST_ADMIN['reservlist_cancel']="削除";
$LANG_ASSIST_ADMIN['err_reservcancel_no_id']="番号が正しくありません。";
$LANG_ASSIST_ADMIN['err_reservcancel_no_file']="この番号の送信予約が存在しません。";
$LANG_ASSIST_ADMIN['done_reservcancel']="送信予約を削除しました。";
// hiroron end 2010/07/15

$LANG_ASSIST_ADMIN['introbody']="送信テキスト";
//$LANG_ASSIST_ADMIN['mail_bulkbooking'] = '一括予約';

$LANG_ASSIST_ADMIN['minute'] = '分';
$LANG_ASSIST_ADMIN['every'] = 'おき';
$LANG_ASSIST_ADMIN['increments'] = '件づつ';

$LANG_ASSIST_ADMIN['yy'] = '年';
$LANG_ASSIST_ADMIN['mm'] = '月';
$LANG_ASSIST_ADMIN['dd'] = '日';

$LANG_ASSIST_ADMIN['jobend'] = '処理終了しました<br'.XHTML.'>';
$LANG_ASSIST_ADMIN['cnt_ok'] = '成功: %d 件<br'.XHTML.'>';
$LANG_ASSIST_ADMIN['cnt_ng'] = 'エラー: %d 件<br'.XHTML.'>';

###############################################################################
#
$LANG_ASSIST_INTROBODY = array();
$LANG_ASSIST_INTROBODY[0] ='冒頭文';
$LANG_ASSIST_INTROBODY[1] ='本文';
#
$LANG_ASSIST_TOENV = array();
$LANG_ASSIST_TOENV[0] ='すべて';
$LANG_ASSIST_TOENV[1] ='PC';
$LANG_ASSIST_TOENV[2] ='携帯';

###############################################################################
# COM_showMessage()
$PLG_assist_MESSAGE1  = '保存されました。';
$PLG_assist_MESSAGE2  = '削除されました。';
$PLG_assist_MESSAGE3  = '問題を確認してください。';

// Messages for the plugin upgrade
$PLG_assist_MESSAGE3002 = $LANG32[9];

###############################################################################
# configuration
// Localization of the Admin Configuration UI
$LANG_configsections['assist']['label'] = 'Assist';
$LANG_configsections['assist']['title'] = 'Assistの設定';

//----------
$LANG_configsubgroups['assist']['sg_main'] = 'メイン';

//---()
$LANG_tab['assist']['tab_main'] = 'メイン設定';
$LANG_fs['assist']['fs_main'] = 'メイン設定';

$LANG_confignames['assist']['title_trim_length']="タイトルの長さ";
$LANG_confignames['assist']['intervalday']="表示期間（日）";
$LANG_confignames['assist']['limitcnt']="表示件数";
$LANG_confignames['assist']['newmarkday']="新着マーク表示期間（日）";
$LANG_confignames['assist']['topics']="デフォルトトピック";
$LANG_confignames['assist']['new_img']="新着マーク";
$LANG_confignames['assist']['rss_img']="RSSマーク";

$LANG_confignames['assist']['newsletter_tid']="ニュースレタートピック";

$LANG_confignames['assist']['templates'] = 'テンプレート　一般画面';
$LANG_confignames['assist']['templates_admin'] = 'テンプレート 管理画面';

$LANG_confignames['assist']['themespath'] = 'テーマテンプレートパス';

$LANG_confignames['assist']['cron_schedule_interval'] = 'assist用擬似Cronスケジュール間隔';

$LANG_confignames['assist']['onoff_emailfromadmin'] = '管理者からのメール受信設定を表示する（準備中）';

$LANG_confignames['assist']['aftersave'] = '保存後の画面遷移 一般画面';
$LANG_confignames['assist']['aftersave_admin'] = '保存後の画面遷移 管理画面';
$LANG_confignames['assist']['xmlins'] = 'xmlins';
$LANG_confignames['assist']['default_img_url'] = 'デフォルト画像URL';
$LANG_confignames['assist']['path_cache'] = 'キャッシュファイルパス';

$LANG_confignames['assist']['disable_permission_ignore'] = '新着データの　permission ignore を無効にする';

//---(１)
$LANG_tab['assist']['tab_autotag_permissions'] = '自動タグのパーミッション';
$LANG_fs['assist']['fs_autotag_permissions'] = '自動タグのパーミッション （[0]所有者 [1]グループ [2]メンバー [3]ゲスト）';
$LANG_confignames['assist']['autotag_permissions_newstories'] = '[newstories: ] パーミッション';
$LANG_confignames['assist']['autotag_permissions_newstories2'] = '[newstories2: ] パーミッション';
$LANG_confignames['assist']['autotag_permissions_conf'] = '[conf: ] パーミッション';
$LANG_confignames['assist']['autotag_permissions_assist'] = '[assist: ] パーミッション';

//---(９)
$LANG_tab['assist']['tab_pro'] = 'profesional版';
$LANG_fs['assist']['fs_pro'] = '（profesional版）';
$LANG_confignames['assist']['path_xml'] = 'XML一括インポートディレクトリ';
$LANG_confignames['assist']['path_xml_out'] = 'XMLエクスポートディレクトリ';

// Note: entries 0, 1, 9, 12, 17 are the same as in $LANG_configselects['Core']
$LANG_configselects['assist'][0] =array('はい' => 1, 'いいえ' => 0);
$LANG_configselects['assist'][1] =array('はい' => TRUE, 'いいえ' => FALSE);
$LANG_configselects['assist'][12] =array('アクセス不可' => 0, '表示' => 2, '表示・編集' => 3);
$LANG_configselects['assist'][13] =array('アクセス不可' => 0, '利用する' => 2);

$LANG_configselects['assist'][5] =array(
    '表示しない' => 'hide'
    , '編集日付によって表示する' => 'modified'
    , '作成日付によって表示する' => 'created');

$LANG_configselects['assist'][20] =array(
    '標準' => 'standard'
    , 'カスタム' => 'custom'
    , 'テーマ' => 'theme');


$LANG_configselects['assist'][9] =array(
    '画面遷移なし' => 'no'
    ,'ページを表示する' => 'item'
    , 'リストを表示する' => 'list'
    , 'ホームを表示する' => 'home'
    , '管理画面トップを表示する' => 'admin'
    , 'プラグイントップを表示する' => 'plugin'
        );

?>