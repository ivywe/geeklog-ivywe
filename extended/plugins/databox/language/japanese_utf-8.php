<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | DataBox Plugin 0.0.0 for Geeklog
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// | Authors    : Tsuchi            - tsuchi AT geeklog DOT jp                 |
// | Authors    : Tetsuko Komma/Ivy - komma AT ivywe DOT co DOT jp             |
// +---------------------------------------------------------------------------+

###############################################################################
# plugins/databox/language/japanese_utf-8.php
# もし万一エンコードの種類が　UTF-8でない場合は、utf-8に変換してください。
# Last Update 20161206

###############################################################################
## 管理画面 menu
$LANG_DATABOX_admin_menu = array();
$LANG_DATABOX_admin_menu['1']= '情報';
$LANG_DATABOX_admin_menu['2']= 'データ';
$LANG_DATABOX_admin_menu['3']= 'アトリビュート';
$LANG_DATABOX_admin_menu['31']='タイプ';
$LANG_DATABOX_admin_menu['4']= 'カテゴリ';
$LANG_DATABOX_admin_menu['5']= 'グループ';
$LANG_DATABOX_admin_menu['51']= 'マスター';
$LANG_DATABOX_admin_menu['6']= 'バックアップ＆リストア';
//
$LANG_DATABOX_admin_menu['8']= 'XML';
$LANG_DATABOX_admin_menu['9']= 'CSV';
$LANG_DATABOX_admin_menu['10']= 'MAPS';
$LANG_DATABOX_admin_menu['11']= 'SEL';


## ユーザー画面
$LANG_DATABOX_user_menu = array();
$LANG_DATABOX_user_menu['2']= 'マイデータ';


###############################################################################
$LANG_DATABOX = array();
$LANG_DATABOX['list']="一覧";
$LANG_DATABOX['countlist']="別件数一覧";
$LANG_DATABOX['selectit']="指定なし";
$LANG_DATABOX['selectall']="すべて";
$LANG_DATABOX['byconfig']="コンフィギュレーション設定による";

$LANG_DATABOX['data'] = 'データ表示';
$LANG_DATABOX['mydata'] = 'マイデータ';

$LANG_DATABOX['Norecentnew'] = '新しいデータはありません';
$LANG_DATABOX['nohit'] = '表示可能なデータはありません';
$LANG_DATABOX['nopermission'] = '閲覧できません';
$LANG_DATABOX['notapplicable'] = '該当データはありません';

$LANG_DATABOX['more'] = 'もっとみる';
$LANG_DATABOX['day'] = "{$_CONF['shortdate']}";

$LANG_DATABOX['home']="HOME";
$LANG_DATABOX['view']="表示";
$LANG_DATABOX['count']="件数";
$LANG_DATABOX['category_top']="カテゴリ別件数一覧";
$LANG_DATABOX['attribute_top']="アトリビュート別件数一覧";
$LANG_DATABOX['search_link']="";
$LANG_DATABOX['return'] = '戻る';
$LANG_DATABOX['search'] = '検索';

$LANG_DATABOX['download'] = 'ダウンロード';
$LANG_DATABOX['downloadrequired'] = 'クリックして、ダウンロードしてください。';
$LANG_DATABOX['display'] = '表示';
$LANG_DATABOX['displayrequired'] = 'クリックして、表示してください。';

$LANG_DATABOX['category_separater']=" / "; //テーマ変数でカテゴリ名称を列挙する際の区切
$LANG_DATABOX['category_separater_code']=" "; //テーマ変数でカテゴリコードを列挙する際の区切
$LANG_DATABOX['category_separater_text']=", "; //送信メールでカテゴリを列挙する際の区切
$LANG_DATABOX['field_separater']="|"; //マルチセレクトリストの区切り

$LANG_DATABOX['loginrequired'] = '(ログインしてください)';

$LANG_DATABOX['lastmodified'] = '%Y年%B%e日更新';
$LANG_DATABOX['lastcreated'] = '%Y年%B%e日追加';
$LANG_DATABOX['deny_msg'] =  'このデータへアクセスできません。(このデータは移動したか削除されているか、あるいはアクセス権がありません。)';


###############################################################################
# admin/plugins/

$LANG_DATABOX_ADMIN['piname'] = 'DataBox';

# 管理画面　start block title
$LANG_DATABOX_ADMIN['admin_list'] = 'DataBox';

$LANG_DATABOX_ADMIN['edit'] = '編集';
$LANG_DATABOX_ADMIN['ref'] = '参考';
$LANG_DATABOX_ADMIN['view'] = '表示確認';
$LANG_DATABOX_ADMIN['add'] = '追加';

$LANG_DATABOX_ADMIN['new'] = '新規登録';
$LANG_DATABOX_ADMIN['drafton'] = 'ドラフト一括オン';//'下書一括オン';
$LANG_DATABOX_ADMIN['draftoff'] = 'ドラフト一括オフ';//'下書一括オフ';
$LANG_DATABOX_ADMIN['export'] = 'エクスポート';
$LANG_DATABOX_ADMIN['import'] = 'インポート';
$LANG_DATABOX_ADMIN['unlinkafterimport'] = '終了後入力ファイル削除する';
$LANG_DATABOX_ADMIN['sampleimport'] = 'サンプルインポート';
$LANG_DATABOX_ADMIN['datadelete'] = '一括削除';

$LANG_DATABOX_ADMIN['importfile'] = 'パス';
$LANG_DATABOX_ADMIN['importurl'] = 'URL';

$LANG_DATABOX_ADMIN['delete'] = '削除';
$LANG_DATABOX_ADMIN['deletemsg_user'] = "データを一括削除します。<br ".XHTML.">";
$LANG_DATABOX_ADMIN['deletemsg_check'] = '削除する場合、ここをチェックしてください';

$LANG_DATABOX_ADMIN['idfrom'] = "開始ID";
$LANG_DATABOX_ADMIN['idto'] = "終了ID";

$LANG_DATABOX_ADMIN['mail1'] = '送信実行';
$LANG_DATABOX_ADMIN['mail2'] = '送信設定';

$LANG_DATABOX_ADMIN['submit'] = '実行';
$LANG_DATABOX_ADMIN['confirm'] = '実行してよいですか？';

$LANG_DATABOX_ADMIN['delete1'] = '選択したタイプのドラフトデータを削除する';
$LANG_DATABOX_ADMIN['delete2'] = '選択したタイプの公開終了データを削除する';
$LANG_DATABOX_ADMIN['delete3'] = '選択したタイプのすべてのデータを削除する';

//
$LANG_DATABOX_ADMIN['link_admin'] = '管理画面：';
$LANG_DATABOX_ADMIN['link_admin_top'] = '一覧管理画面TOPへ';
$LANG_DATABOX_ADMIN['link_public'] = '｜表示画面：';
$LANG_DATABOX_ADMIN['link_list'] = '一覧ページへ';
$LANG_DATABOX_ADMIN['link_detail'] = '詳細ページへ';



//
$LANG_DATABOX_ADMIN['id'] = 'ID';
$LANG_DATABOX_ADMIN['help_id'] ="
";

$LANG_DATABOX_ADMIN['seq'] = 'SEQ';

$LANG_DATABOX_ADMIN['tag'] = 'TAG';
$LANG_DATABOX_ADMIN['value'] = 'VALUE';
$LANG_DATABOX_ADMIN['value2'] = 'VALUE2';
$LANG_DATABOX_ADMIN['disp'] = 'disp';
$LANG_DATABOX_ADMIN['relno'] = 'relno';

$LANG_DATABOX_ADMIN['code']='コード';

$LANG_DATABOX_ADMIN['title']='タイトル';
$LANG_DATABOX_ADMIN['page_title']='ページタイトル';

$LANG_DATABOX_ADMIN['description']='説明';
$LANG_DATABOX_ADMIN['description2']='説明2';
$LANG_DATABOX_ADMIN['fieldgroupno']='フィールドグループ';
$LANG_DATABOX_ADMIN['defaulttemplatesdirectory']='テンプレートディレクトリ';
$LANG_DATABOX_ADMIN['layout']='レイアウト';

$LANG_DATABOX_ADMIN['category']='カテゴリ';

$LANG_DATABOX_ADMIN['meta_description']='説明文のメタタグ';

$LANG_DATABOX_ADMIN['meta_keywords']='キーワードのメタタグ';

$LANG_DATABOX_ADMIN['hits']='閲覧数';
$LANG_DATABOX_ADMIN['hitsclear']='閲覧数初期化';

$LANG_DATABOX_ADMIN['comments']='コメント数';

$LANG_DATABOX_ADMIN['commentcode']='コメント';

$LANG_DATABOX_ADMIN['comment_expire']='コメント停止日時';

$LANG_DATABOX_ADMIN['trackbackcode']='トラックバック';

$LANG_DATABOX_ADMIN['cache_time']='キャッシュタイム';
$LANG_DATABOX_ADMIN['cache_time_desc']='
このデータはここで指定された秒数以上にキャッシュされることはありません。もしキャッシュが0ならキャッシュ無効 (3600 = 1時間,  86400 = 1日)。
';

$LANG_DATABOX_ADMIN['group']='グループ';
$LANG_DATABOX_ADMIN['parent']='親';

$LANG_DATABOX_ADMIN['fieldset']='タイプ';
$LANG_DATABOX_ADMIN['fieldset_id']="タイプID";
$LANG_DATABOX_ADMIN['fieldsetfields']="アトリビュートの表示と編集";
$LANG_DATABOX_ADMIN['fieldsetfieldsregistered']="登録されたアトリビュート";
$LANG_DATABOX_ADMIN['fieldlist']="アトリビュート一覧";
$LANG_DATABOX_ADMIN['fieldsetgroups']="カテゴリグループの表示と編集";
$LANG_DATABOX_ADMIN['fieldsetgroupsregistered']="登録されたカテゴリグループ";
$LANG_DATABOX_ADMIN['grouplist']="カテゴリグループ一覧";
$LANG_DATABOX_ADMIN['fieldsetlist']='タイプ一覧';

$LANG_DATABOX_ADMIN['registset']='タイプ登録';
$LANG_DATABOX_ADMIN['changeset']='タイプ変更';
$LANG_DATABOX_ADMIN['inst_changeset0']="タイプが登録されていないデータのタイプを登録します。<br".XHTML.">";
$LANG_DATABOX_ADMIN['inst_changesetx']="のタイプを変更します。<br".XHTML.">";

$LANG_DATABOX_ADMIN['inst_changeset'] = 
"タイプを選んでください。<br".XHTML.">
";

$LANG_DATABOX_ADMIN['inst_dataexport'] = 
"
エクスポートするデータのタイプを選択してください。<br".XHTML.">
";


$LANG_DATABOX_ADMIN['allow_display']='表示制限(一般)';
$LANG_DATABOX_ADMIN['allow_edit']='編集制限(ユーザー)';

$LANG_DATABOX_ADMIN['type']='タイプ';

$LANG_DATABOX_ADMIN['size']='size（テキスト,マルチセレクトリスト）';
$LANG_DATABOX_ADMIN['maxlength']='maxlength（テキスト）';
$LANG_DATABOX_ADMIN['rows']='rows（複数行テキスト）';
$LANG_DATABOX_ADMIN['br']='改行（ラジオボタン）';
$LANG_DATABOX_ADMIN['help_br']='0:しない,1〜9:指定数毎に改行する';

//
$LANG_DATABOX_ADMIN['language_id']="言語ID";
$LANG_DATABOX_ADMIN['owner_id']="所有者ID";
$LANG_DATABOX_ADMIN['group_id']="グループID";
$LANG_DATABOX_ADMIN['perm_owner']="パーミッション（所有者）";
$LANG_DATABOX_ADMIN['perm_group']="パーミッション（グループ）";;
$LANG_DATABOX_ADMIN['perm_members']="パーミッション（メンバ）";
$LANG_DATABOX_ADMIN['perm_anon']="パーミッション（ゲスト）";
//

$LANG_DATABOX_ADMIN['selection']='選択肢';
$LANG_DATABOX_ADMIN['selectlist']='マスターの種別';
$LANG_DATABOX_ADMIN['checkrequried']='必須チェック';

$LANG_DATABOX_ADMIN['textcheck']='入力チェック（テキスト）';
$LANG_DATABOX_ADMIN['textconv']='入力値変換（テキスト）';
$LANG_DATABOX_ADMIN['searchtarget']='検索対象にする';

$LANG_DATABOX_ADMIN['initial_value']='初期値';
$LANG_DATABOX_ADMIN['range']='範囲';
$LANG_DATABOX_ADMIN['dfid']=$LANG04[42];//'日時のフォーマット';

$LANG_DATABOX_ADMIN['draft'] = 'ドラフト';//'下書';
$LANG_DATABOX_ADMIN['draft_msg'] = '
※現在ドラフトモードです。モードの変更はサイトの管理者へご連絡ください。';
$LANG_DATABOX_ADMIN['uid'] = 'ユーザーID';
$LANG_DATABOX_ADMIN['modified'] = '編集日付';
$LANG_DATABOX_ADMIN['created'] = '作成日付';
$LANG_DATABOX_ADMIN['released'] = '公開日';
$LANG_DATABOX_ADMIN['expired'] = '公開終了日';
$LANG_DATABOX_ADMIN['remaingdays'] = '残日数';

$LANG_DATABOX_ADMIN['udatetime'] = 'タイムスタンプ';
$LANG_DATABOX_ADMIN['uuid'] = '更新ユーザー';

$LANG_DATABOX_ADMIN['kind'] = '種別';
$LANG_DATABOX_ADMIN['no'] = 'No.';


//@@@@@-->
$LANG_DATABOX_ADMIN['inpreparation'] = '(準備中)';
$LANG_DATABOX_ADMIN['xml_def'] = 'XML定義';
$LANG_DATABOX_ADMIN['init'] = '初期化';
$LANG_DATABOX_ADMIN['list'] = '一覧';
$LANG_DATABOX_ADMIN['dataclear'] = 'データクリア';
$LANG_DATABOX_ADMIN['allclear'] = 'ALL クリア';

$LANG_DATABOX_ADMIN['configbackup'] = 'コンフィギュレーションバックアップ';
$LANG_DATABOX_ADMIN['configinit'] = 'コンフィギュレーション初期化';
$LANG_DATABOX_ADMIN['configrestore'] = 'コンフィギュレーションリストア';
$LANG_DATABOX_ADMIN['configupdate'] = 'コンフィギュレーション更新';
$LANG_DATABOX_ADMIN['configbackupmsg'] = 'バックアップを作成します';
$LANG_DATABOX_ADMIN['configinitmsg'] = 'コンフィギュレーションを初期化します';
$LANG_DATABOX_ADMIN['configrestoremsg'] = 'コンフィギュレーションをバックアップの内容に戻します';
$LANG_DATABOX_ADMIN['configupdatemsg'] = 'コンフィギュレーションを最新の仕様に更新';


$LANG_DATABOX_ADMIN['path'] = '絶対パス';
$LANG_DATABOX_ADMIN['url'] = 'URL';

$LANG_DATABOX_ADMIN['default'] = 'デフォルト';
$LANG_DATABOX_ADMIN['importxmlmsg'] = "
絶対パス（フォルダ、ファイル）またはURLを指定してください。<br".XHTML.">
フォルダ指定の時は、フォルダ下のxmlファイルをインポートします。<br".XHTML.">
logs/databox_xmlimport.log　にログが記録されます。<br".XHTML.">
";
$LANG_DATABOX_ADMIN['exportxmlmsg'] = "
絶対パス（フォルダ）を指定してください。<br".XHTML.">
logs/databox_xmlimport.log　にログが記録されます。<br".XHTML.">
";
$LANG_DATABOX_ADMIN['initmsg'] = '
初期化します。「XML定義一覧」の内容は削除されます。
';
$LANG_DATABOX_ADMIN['dataclearmsg'] = "
バックアップはとりましたか？<br".XHTML.">
データをクリアします。<br".XHTML.">
アップロードされたファイルも削除されます。<br".XHTML.">
アトリビュート、カテゴリ、グループ削除されません。<br".XHTML.">
";
$LANG_DATABOX_ADMIN['allclearmsg'] = "
バックアップはとりましたか？<br".XHTML.">
マスタおよびデータをクリアします。<br".XHTML.">
アップロードされたファイルも削除されます。<br".XHTML.">
";
$LANG_DATABOX_ADMIN['backupmsg'] = 
"{$_CONF['backup_path']}"."databox/に<br".XHTML.">"
."DataBox のデータベースデータをバックアップします。<br".XHTML.">
アップロードファイルは別途バックアップしてください。<br".XHTML.">
";
$LANG_DATABOX_ADMIN['restoremsg'] = 
"リストアするファイルを選択してください。<br".XHTML.">
DataBox のデータベースデータをリストアします。<br".XHTML.">
アップロードファイルは別途もどしてください。<br".XHTML.">
";

$LANG_DATABOX_ADMIN['restoremsgPHP'] = 
"{$_CONF['backup_path']}"."databox/にある"
."ファイル名を指定してください。（省略時databox.xml）<br".XHTML.">
phpMyAdmin でエクスポートしたDataBox のデータベースデータをリストアします。<br".XHTML.">
phpMyAdmin XML Dump version 3.3.8用<br".XHTML.">
接頭子が異なる場合は、あらかじめ変換しておいてください。<br".XHTML.">
アップロードファイルは別途もどしてください。<br".XHTML.">
";
$LANG_DATABOX_ADMIN['datadeletemsg'] = "
アップロードされたファイルも削除されます。<br".XHTML.">
";
//<---
//maps
$LANG_DATABOX_ADMIN['mapsmarkersclear'] = 'MAPS markers クリア';
$LANG_DATABOX_ADMIN['mapsmarkersupdate'] = 'MAPS markers 更新';
$LANG_DATABOX_ADMIN['mapsmarkers'] = 'MAPS marker';

$LANG_DATABOX_ADMIN['mapsmarkersclearmsg'] = "
アトリビュートに登録されている特定のマップのマーカーをクリアします。
";
$LANG_DATABOX_ADMIN['mapsmarkersupdatemsg'] = "
アトリビュートに登録されている特定のマップのマーカーをクリアし
データの内容で作成します。<br".XHTML.">
logs/databox_mapsupdate.log　にログが記録されます。
";
$LANG_DATABOX_ADMIN['mapsmarkersupdateend'] = "正常 %d 件 エラー %d 件";
$LANG_DATABOX_ADMIN['mapsmarkersupdateend2'] = "(緯度・経度 計算 %d 件 更新 %d 件)";

$LANG_DATABOX_ADMIN['schedule'] = "スケジュール";
$LANG_DATABOX_ADMIN['nextdatetime'] = "予定の時刻";
$LANG_DATABOX_ADMIN['cron_schedule'] = "
この作業が行われるには、予定の時刻付近で誰かがサイトを訪問する必要があることに注意してください。<br".XHTML.">
訪問者がほとんどいないサイトでは、作業の開始がかなり遅れる可能性があります。
";
$LANG_DATABOX_ADMIN['cron_schedule_Enable'] = "
この機能は無効に設定されています<br".XHTML.">
有効にするためには、DataBoxのコンフィギュレーション設定を変更してください。<br".XHTML.">
";

//csv
$LANG_DATABOX_ADMIN['csv_def'] = 'CSV定義';
$LANG_DATABOX_ADMIN['csv_select'] = '条件';
$LANG_DATABOX_ADMIN['csv_select_dtl'] = '条件:明細';
$LANG_DATABOX_ADMIN['csvheader'] = 'CSV１行目';
$LANG_DATABOX_ADMIN['help_csvheader'] = 'CSVの１行目';
$LANG_DATABOX_ADMIN['help_field_csv'] = "
マスターを使用するアトリビュートは２種類あります
例マスターのNo.を入力ファイルに編集する場合（都道府県）
マスターのvalueを入力ファイルに編集する場合（都道府県_value）
";
$LANG_DATABOX_ADMIN['help_value_csv'] = "
カテゴリの場合、カテゴリの名称を登録します
";
$LANG_DATABOX_ADMIN['help_value_csv_sel'] = "
,区切りで複数指定できます。例　駐車場,賃貸アパート
";

$LANG_DATABOX_ADMIN['importmsgcsv'] = "
絶対パス（フォルダ、ファイル）またはURLを指定してください。<br".XHTML.">
フォルダ指定の時は、フォルダ下のcsvファイルをインポートします。<br".XHTML.">
入力を省略すると%s
の下のcsvファイルをインポートします。<br".XHTML.">
logs/databox_csvimport.log　にログが記録されます。<br".XHTML.">
";
$LANG_DATABOX_ADMIN['exportmsgcsv'] = "
絶対パス（フォルダ）を指定してください。<br".XHTML.">
logs/databox_csvimport.log　にログが記録されます。<br".XHTML.">
";
$LANG_DATABOX_ADMIN['initmsgcsv'] = '
初期化します。「CSV定義一覧」の内容は削除されます。
';


$LANG_DATABOX_ADMIN['draftonmsg'] = "
すべてのドラフトをオンにします。<br".XHTML.">
";
$LANG_DATABOX_ADMIN['draftoffmsg'] = "
すべてのドラフトをオフにします。<br".XHTML.">
";
$LANG_DATABOX_ADMIN['hitsclearmsg'] = "
閲覧数を0にします。<br".XHTML.">
";

$LANG_DATABOX_ADMIN['yy'] = '年';
$LANG_DATABOX_ADMIN['mm'] = '月';
$LANG_DATABOX_ADMIN['dd'] = '日';

$LANG_DATABOX_ADMIN['must'] = '*必須';

$LANG_DATABOX_ADMIN['enabled'] = '有効';
$LANG_DATABOX_ADMIN['modified_autoupdate'] = '自動更新する';

$LANG_DATABOX_ADMIN['additionfields'] = 'アトリビュート';
$LANG_DATABOX_ADMIN['basicfields'] = '基本';

$LANG_DATABOX_ADMIN['category_id'] = 'カテゴリID';
$LANG_DATABOX_ADMIN['field_id'] = 'アトリビュートID';
$LANG_DATABOX_ADMIN['name'] = '名称';
$LANG_DATABOX_ADMIN['templatesetvar'] = 'テーマ変数';
$LANG_DATABOX_ADMIN['templatesetvars'] = 'テーマ変数';
$LANG_DATABOX_ADMIN['parent_id'] = '親ID';
$LANG_DATABOX_ADMIN['parent_flg'] = '親グループ？';
$LANG_DATABOX_ADMIN['input_type'] = '入力タイプ';

$LANG_DATABOX_ADMIN['orderno'] = '表示位置';

$LANG_DATABOX_ADMIN['field'] = 'フィールド';
$LANG_DATABOX_ADMIN['fields'] = 'フィールド';
$LANG_DATABOX_ADMIN['content'] = 'コンテンツ';

$LANG_DATABOX_ADMIN['byusingid'] = 'IDを使用する';
$LANG_DATABOX_ADMIN['byusingcode'] = 'コードを使用する';
$LANG_DATABOX_ADMIN['byusingtemplatesetvar'] = '登録したテーマ変数を使用する';

$LANG_DATABOX_ADMIN['withlink'] = 'リンク付';
$LANG_DATABOX_ADMIN['groupbygroup'] = 'グループ別';

$LANG_DATABOX_ADMIN['number'] ="件";
$LANG_DATABOX_ADMIN['endmessage'] = "処理終了しました";
//help
$LANG_DATABOX_ADMIN['delete_help_field'] = '削除するとデータも削除されます！';
$LANG_DATABOX_ADMIN['delete_help_group'] = '登録されているデータがあります。削除できません。';
$LANG_DATABOX_ADMIN['delete_help_category'] = '登録されているデータがあります。削除できません。親の変更もできません。';
$LANG_DATABOX_ADMIN['delete_help_fieldset'] = '登録されているデータがあります。削除できません。';
$LANG_DATABOX_ADMIN['delete_help_mst'] = '登録されているデータがあります。削除できません。';

//xmlimport_help
$LANG_DATABOX_xmlimport['help']=
"<br".XHTML.">"
."(注！)<br".XHTML.">"
."assist DataBoxプラグインのXML一括インポートディレクトリは、同一の場所を登録しておいてください  <br".XHTML.">"
."<br".XHTML.">"
."assist プラグインのxmlインポートを実行します <br".XHTML.">"
."maps:item_10 はコードに相当する内容を登録しておいてください <br".XHTML.">"
."同一コードが既に登録済の場合は、削除の後追加します <br".XHTML.">"
."<br".XHTML.">"
."DataBox プラグインのxmlインポートを実行します <br".XHTML.">"
."同一コードが既に登録済の場合は、削除の後追加します <br".XHTML.">"
."各々の処理が済んだら、XMLファイルは削除します <br".XHTML.">"
."(権限により削除できない場合があります） <br".XHTML.">"
."<br".XHTML.">"
."実行内容はdatabox_xmlimport.log に 記録されます<br".XHTML.">"

;
$LANG_DATABOX_ADMIN['jobend'] = "処理終了しました<br".XHTML.">";
$LANG_DATABOX_ADMIN['cnt_ok'] = "成功: %d 件<br".XHTML.">";
$LANG_DATABOX_ADMIN['cnt_ng'] = "エラー: %d 件<br".XHTML.">";
$LANG_DATABOX_ADMIN['cnt_ex'] = "条件対象外: %d 件<br".XHTML.">";

//backup&restore
$LANG_DATABOX_ADMIN['config'] = 'コンフィギュレーション';

$LANG_DATABOX_ADMIN['config_backup'] = 'バックアップ実行';
$LANG_DATABOX_ADMIN['config_backup_help'] = 'バックアップファイルを作成します';

$LANG_DATABOX_ADMIN['config_init'] = '初期化実行';
$LANG_DATABOX_ADMIN['config_init_help'] = '初期値に戻します ';

$LANG_DATABOX_ADMIN['config_restore'] = 'リストア実行';
$LANG_DATABOX_ADMIN['config_restore_help'] = 'バックアップファイルの内容に戻します ';

$LANG_DATABOX_ADMIN['config_update'] = '更新';
$LANG_DATABOX_ADMIN['config_update_help'] = '最新の仕様に更新します ';

//(2)
$LANG_DATABOX_ADMIN['datamaster'] = 'データ、アトリビュート、タイプ、カテゴリー、グループ、マスター';
$LANG_DATABOX_ADMIN['data_clear'] = 'データを初期化';
$LANG_DATABOX_ADMIN['data_allclear'] = 'データ, アトリビュート, タイプ, カテゴリー, グループ, マスターを初期化';
$LANG_DATABOX_ADMIN['data_backup'] = '全データバックアップ　...データ, アトリビュート, タイプ, カテゴリー, グループ, マスターをバックアップする';
$LANG_DATABOX_ADMIN['data_restore'] = '全データリストア　...データ, アトリビュート, タイプ, カテゴリー, グループ, マスターをバックアップデータからリストアする';


$LANG_DATABOX_ADMIN['document'] = 'ドキュメント';
$LANG_DATABOX_ADMIN['configuration'] = 'コンフィギュレーション設定';
$LANG_DATABOX_ADMIN['install'] = 'インストール方法';
$LANG_DATABOX_ADMIN['autotags'] = '自動タグ・ブロック用関数';
$LANG_DATABOX_ADMIN['files'] = 'ファイル一覧';
$LANG_DATABOX_ADMIN['tables'] = 'テーブル一覧';
$LANG_DATABOX_ADMIN['input'] = '入力一覧';

$LANG_DATABOX_ADMIN['online'] = 'オンライン';

//管理画面：このページについて
$LANG_DATABOX_ADMIN['about_admin_information'] = '';
$LANG_DATABOX_ADMIN['about_admin_data'] = 'データの管理';
$LANG_DATABOX_ADMIN['about_admin_category'] = 'カテゴリの管理';
$LANG_DATABOX_ADMIN['about_admin_field'] = 'アトリビュートの管理';
$LANG_DATABOX_ADMIN['about_admin_group'] = 'グループの管理';
$LANG_DATABOX_ADMIN['about_admin_fieldset'] = 'データタイプの管理';
$LANG_DATABOX_ADMIN['about_admin_backuprestore'] = "バックアップの作成とリストア<br".XHTML."><br".XHTML.">";
$LANG_DATABOX_ADMIN['about_admin_mst'] = 'マスターの管理';

$LANG_DATABOX_ADMIN['about_admin_xml'] = 'XML定義の管理';
$LANG_DATABOX_ADMIN['about_admin_csv'] = 'CSV定義の管理';


$LANG_DATABOX_ADMIN['about_admin_view'] = '一般ログインユーザーからみたページはこのようになります';

$LANG_DATABOX_ADMIN['inst_fieldsetfields'] = 
"アトリビュートの編集は、アトリビュート名をクリックして「追加」または「削除」ボタンをクリックしてください。<br".XHTML.">
アトリビュートが選択されているときは右側だけに表示されます。<br".XHTML.">
編集が終わったら、「保存」ボタンをクリックしてください。<br".XHTML.">
管理画面に戻ります。";

$LANG_DATABOX_ADMIN['inst_newdata'] = 
"新規登録するデータのタイプを選んでください。<br".XHTML.">
";


//ERR
$LANG_DATABOX_ADMIN['err'] = 'エラー';
$LANG_DATABOX_ADMIN['err_empty'] = 'ファイルがありません';

$LANG_DATABOX_ADMIN['err_invalid'] = 'データがありません';
$LANG_DATABOX_ADMIN['err_permission_denied'] = '許可されていません';

$LANG_DATABOX_ADMIN['err_id'] = 'IDが不正です';
$LANG_DATABOX_ADMIN['err_name'] = '名前が不正です';
$LANG_DATABOX_ADMIN['err_templatesetvar'] = 'テーマ変数が不正です';
$LANG_DATABOX_ADMIN['err_templatesetvar_w'] = 'テーマ変数はすでに使用されています';
$LANG_DATABOX_ADMIN['err_code_w'] = 'このコードはすでに登録されています';
$LANG_DATABOX_ADMIN['err_code_x'] = 'コードは英数字と.-_のみ入力可能です';
$LANG_DATABOX_ADMIN['err_code'] = 'コードが入力されていません';
$LANG_DATABOX_ADMIN['err_title'] = 'タイトルが入力されていません';
$LANG_DATABOX_ADMIN['err_numeric'] = '数値のみ入力可能です';

$LANG_DATABOX_ADMIN['err_text1'] = '半角数字のみ入力可能です';
$LANG_DATABOX_ADMIN['err_text2'] = '英数字のみ入力可能です';
$LANG_DATABOX_ADMIN['err_text3'] = '半角英数字/-.のみ入力可能です';
$LANG_DATABOX_ADMIN['err_text4'] = '英数字記号のみ入力可能です';
$LANG_DATABOX_ADMIN['err_range'] = '範囲外です';

$LANG_DATABOX_ADMIN['err_selection'] = '選択肢が入力されていません';

$LANG_DATABOX_ADMIN['err_modified'] = '編集日付が不正です';
$LANG_DATABOX_ADMIN['err_created'] = '作成日付が不正です';
$LANG_DATABOX_ADMIN['err_released'] = '公開日が不正です';
$LANG_DATABOX_ADMIN['err_expired'] = '公開終了日が不正です';

$LANG_DATABOX_ADMIN['err_checkrequried'] = ' 必ず入力してください';

$LANG_DATABOX_ADMIN['err_date'] = '日付が不正です';
$LANG_DATABOX_ADMIN['err_time'] = '時刻が不正です';
$LANG_DATABOX_ADMIN['err_writable'] = ' 書込可能にしてください';

$LANG_DATABOX_ADMIN['err_size'] = 'サイズが不正です';//@@@@@
$LANG_DATABOX_ADMIN['err_type'] = 'タイプが不正です';//@@@@@

$LANG_DATABOX_ADMIN['err_field_w'] = '当フィールドはすでに登録されています';
$LANG_DATABOX_ADMIN['err_tag_w'] = '当タグはすでに登録されています';

$LANG_DATABOX_ADMIN['err_csvheader_w'] = '当CSVヘッダーはすでに登録されています';
$LANG_DATABOX_ADMIN['err_csvheader'] = 'CSVヘッダーが不正です';

$LANG_DATABOX_ADMIN['err_url'] = 'このURLは有効なアドレスではないようです';
$LANG_DATABOX_ADMIN['err_maxlength'] = '文字以内で入力してください';

$LANG_DATABOX_ADMIN['err_backup_file_not_exist'] = "コンフィギュレーションバックアップファイルがありません<br".XHTML.">";
$LANG_DATABOX_ADMIN['err_backup_file_non_rewritable'] = "コンフィギュレーションバックアップファイル書換できません<br".XHTML.">";

$LANG_DATABOX_ADMIN['err_not_exist'] = '存在しません';

$LANG_DATABOX_ADMIN['err_kind'] = '種別が不正です。';
$LANG_DATABOX_ADMIN['err_no'] = 'No. が不正です。';
$LANG_DATABOX_ADMIN['err_no_w'] = 'このNo. はすでに登録されています。';

###############################################################################
$LANG_DATABOX_ORDER['random']="ランダム";
$LANG_DATABOX_ORDER['date']="日付順";
$LANG_DATABOX_ORDER['orderno']="表示位置順";
$LANG_DATABOX_ORDER['code']="コード順";
$LANG_DATABOX_ORDER['title']="タイトル順";
$LANG_DATABOX_ORDER['description']="説明順";
$LANG_DATABOX_ORDER['id']="登録順";
$LANG_DATABOX_ORDER['released']="公開日順";
$LANG_DATABOX_ORDER['order']="順";

###############################################################################
##
$LANG_DATABOX_XML['base:code']=$LANG_DATABOX_ADMIN['code'];
$LANG_DATABOX_XML['base:title']=$LANG_DATABOX_ADMIN['title'];



###############################################################################
$LANG_DATABOX_MAIL['subject_data'] =
"【{$_CONF['site_name']}】データ更新 by %s";

$LANG_DATABOX_MAIL['message_data']=
"%sさん(user no.%s)によって、データが更新されました。".LB.LB;







$LANG_DATABOX_MAIL['subject_category'] =
"【{$_CONF['site_name']}】カテゴリ更新 by {$_USER['username']}";

$LANG_DATABOX_MAIL['message_category']=
"{$_USER['username']}さん(user no.{$_USER['uid']})によって、カテゴリが更新されました。".LB.LB;

$LANG_DATABOX_MAIL['subject_group'] =
"【{$_CONF['site_name']}】グループ更新 by {$_USER['username']}";

$LANG_DATABOX_MAIL['message_group']=
"{$_USER['username']}さん(user no.{$_USER['uid']})によって、グループが更新されました。".LB.LB;

$LANG_DATABOX_MAIL['subject_fieldset'] =
"【{$_CONF['site_name']}】データタイプ更新 by {$_USER['username']}";

$LANG_DATABOX_MAIL['message_fieldset']=
"{$_USER['username']}さん(user no.{$_USER['uid']})によって、データタイプが更新されました。".LB.LB;

#
$LANG_DATABOX_MAIL['sig'] = LB
."------------------------------------".LB
."{$_CONF['site_name']}".LB
."{$_CONF['site_url']}".LB
."このメールは自動送信されたものです。".LB
."------------------------------------".LB
;

$LANG_DATABOX_MAIL['subject_data_delete'] =
"【{$_CONF['site_name']}】データ削除 by {$_USER['username']}";
$LANG_DATABOX_MAIL['message_data_delete']=
"{$_USER['username']}さん(user no.{$_USER['uid']})によって、データが削除されました。".LB;


$LANG_DATABOX_MAIL['subject_category_delete'] =
"【{$_CONF['site_name']}】カテゴリ削除 by {$_USER['username']}";
$LANG_DATABOX_MAIL['message_category_delete']=
"{$_USER['username']}さん(user no.{$_USER['uid']})によって、カテゴリが削除されました。".LB;

$LANG_DATABOX_MAIL['subject_group_delete'] =
"【{$_CONF['site_name']}】グループ削除 by {$_USER['username']}";
$LANG_DATABOX_MAIL['message_group_delete']=
"{$_USER['username']}さん(user no.{$_USER['uid']})によって、グループが削除されました。".LB;

$LANG_DATABOX_MAIL['subject_fieldset_delete'] =
"【{$_CONF['site_name']}】データタイプ削除 by {$_USER['username']}";
$LANG_DATABOX_MAIL['message_fieldset_delete']=
"{$_USER['username']}さん(user no.{$_USER['uid']})によって、データタイプが削除されました。".LB;

###############################################################################
#
$LANG_DATABOX_NOYES = array(
    0 => 'いいえ',
    1 => 'はい'
);
$LANG_DATABOX_INPUTTYPE = array(
    0 => 'チェックボックス',
    1 => 'マルチセレクトリスト'
    ,2 => 'ラジオボタンリスト'
    ,3 => 'オプションリスト'
);
$LANG_DATABOX_ALLOW_DISPLAY = array();
$LANG_DATABOX_ALLOW_DISPLAY[0] ='表示する（一覧表示可能）';
$LANG_DATABOX_ALLOW_DISPLAY[1] ='ログインユーザーのみ表示する（一覧表示可能）';
$LANG_DATABOX_ALLOW_DISPLAY[2] ='グループ(所有者含)とアクセス権のある人のみ表示';
$LANG_DATABOX_ALLOW_DISPLAY[3] ='所有者とアクセス権のある人のみ表示';
$LANG_DATABOX_ALLOW_DISPLAY[4] ='アクセス権のある人のみ表示';
$LANG_DATABOX_ALLOW_DISPLAY[5] = '表示しない';

$LANG_DATABOX_ALLOW_EDIT = array();
$LANG_DATABOX_ALLOW_EDIT[0] = '編集可';
$LANG_DATABOX_ALLOW_EDIT[2] = 'グループ(所有者含)とアクセス権のある人のみ編集可';
$LANG_DATABOX_ALLOW_EDIT[3] = '所有者とアクセス権のある人のみ編集可';
$LANG_DATABOX_ALLOW_EDIT[4] = '編集不可表示のみ';
$LANG_DATABOX_ALLOW_EDIT[5] = '編集表示しない';

$LANG_DATABOX_TEXTCHECK = array();
$LANG_DATABOX_TEXTCHECK[0] = 'ノーチェック';
$LANG_DATABOX_TEXTCHECK[11] = '半角数字のみ（半角に変換します）';
$LANG_DATABOX_TEXTCHECK[12] = '半角英数字のみ（半角に変換します）';
$LANG_DATABOX_TEXTCHECK[13] = '標準IDの範囲内（半角に変換します）';
$LANG_DATABOX_TEXTCHECK[14] = '半角英数字記号のみ（半角に変換します）';

$LANG_DATABOX_TEXTCONV = array();
$LANG_DATABOX_TEXTCONV[0] = 'しない';
$LANG_DATABOX_TEXTCONV[10] = '半角に変換する';
$LANG_DATABOX_TEXTCONV[20] = '全角に変換する';

//TYPE （内容の変更不可）
$LANG_DATABOX_TYPE = array();
$LANG_DATABOX_TYPE[0] = '一行テキスト';
$LANG_DATABOX_TYPE[1] = 'HTML（複数行テキスト）';
$LANG_DATABOX_TYPE[20] = 'TinyMCE（複数行テキスト）';
$LANG_DATABOX_TYPE[10] = '複数行テキスト';
$LANG_DATABOX_TYPE[19] = 'CKEditor（複数行テキスト）';

$LANG_DATABOX_TYPE[15] = '数値';
$LANG_DATABOX_TYPE[21] = '通貨';

$LANG_DATABOX_TYPE[2] = 'いいえ/はい';
$LANG_DATABOX_TYPE[3] = '日付';
$LANG_DATABOX_TYPE[22] = '日付 (jquery ui datepicker)';
$LANG_DATABOX_TYPE[23] = '日付 (Uikit datepicker)';
$LANG_DATABOX_TYPE[4] = '日時';
$LANG_DATABOX_TYPE[26] = '時刻 (Uikit timepicker)';
$LANG_DATABOX_TYPE[5] = 'メールアドレス';
$LANG_DATABOX_TYPE[6] = 'url';
$LANG_DATABOX_TYPE[7] = 'オプションリスト（選択肢）';
$LANG_DATABOX_TYPE[8] = 'ラジオボタンリスト（選択肢）';
$LANG_DATABOX_TYPE[14] = 'マルチセレクトリスト（選択肢）';
$LANG_DATABOX_TYPE[24] = 'チェックボックス（選択肢）';
$LANG_DATABOX_TYPE[17] = 'フラグ';
$LANG_DATABOX_TYPE[9] = 'オプションリスト（マスター）';
$LANG_DATABOX_TYPE[16] = 'ラジオボタンリスト（マスター）';
$LANG_DATABOX_TYPE[18] = 'マルチセレクトリスト（マスター）';
$LANG_DATABOX_TYPE[25] = 'チェックボックス（マスター）';


$LANG_DATABOX_TYPE[11] = '画像（DB保存）';
$LANG_DATABOX_TYPE[12] = '画像（ファイル保存）';
$LANG_DATABOX_TYPE[13] = '添付ファイル';


###############################################################################
#
$LANG_DATABOX_SEARCH['type'] = 'DataBox';

$LANG_DATABOX_SEARCH['results_databox'] = 'DataBoxの検索結果';

$LANG_DATABOX_SEARCH['title'] =  'タイトル';
$LANG_DATABOX_SEARCH['udate'] =  '更新日';

###############################################################################
#
$LANG_DATABOX_STATS['data'] = 'DataBox';

$LANG_DATABOX_STATS['stats_page_title']='タイトル';
$LANG_DATABOX_STATS['stats_hits']='閲覧数';
$LANG_DATABOX_STATS['stats_headline']='DataBox(上位10件) 管理人の表示は含みません';
$LANG_DATABOX_STATS['stats_no_hits']='表示した人が人がいません。';

###############################################################################
# COM_showMessage()
$PLG_databox_MESSAGE1  = '保存されました。';
$PLG_databox_MESSAGE2  = '削除されました。';
$PLG_databox_MESSAGE3  = '問題を確認してください。';

// Messages for the plugin upgrade
$PLG_databox_MESSAGE3002 = $LANG32[9];

###############################################################################
#
$LANG_DATABOX_autotag_desc['databox']="
[databox:count]他 <br".XHTML.">	
詳細は、databoxプラグインのドキュメントを参照してください。
<a href=\"{$_CONF['site_admin_url']}/plugins/databox/docs/japanese/autotags.html\">*</a>
";

###############################################################################
# configuration
// Localization of the Admin Configuration UI
$LANG_configsections['databox']['label'] = 'DataBox';
$LANG_configsections['databox']['title'] = 'DataBoxの設定';

//----------
$LANG_configsubgroups['databox']['sg_main'] = 'メイン';
//--(0)

$LANG_tab['databox'][tab_main] = 'メイン設定';
$LANG_fs['databox'][fs_main] = 'DataBoxのメイン設定';
$LANG_confignames['databox']['perpage'] = 'ページあたりのデータ数';
$LANG_confignames['databox']['loginrequired'] = 'ログイン要求する';
$LANG_confignames['databox']['hidemenu'] = 'メニューに表示しない';

$LANG_confignames['databox']['categorycode'] = 'カテゴリ　コードを使用する';
$LANG_confignames['databox']['datacode'] = 'データ　コードを使用する';
$LANG_confignames['databox']['groupcode'] = 'グループ　コードを使用する';
$LANG_confignames['databox']['top'] = 'topに表示するプログラム';
$LANG_confignames['databox']['detail'] = '個別表示に使用するプログラム';
$LANG_confignames['databox']['templates'] = 'テンプレート　一般画面';
$LANG_confignames['databox']['templates_admin'] = 'テンプレート 管理画面';

$LANG_confignames['databox']['themespath'] = 'テーマテンプレートパス';
$LANG_confignames['databox']['delete_data'] = '所有者の削除と共に削除する';
$LANG_confignames['databox']['datefield'] = '使用する日付';

$LANG_confignames['databox']['meta_tags'] = 'メタタグを使用する';

$LANG_confignames['databox']['layout'] = 'レイアウト 一般画面';
$LANG_confignames['databox']['layout_admin'] = 'レイアウト 管理画面';
//----------------------
$LANG_confignames['databox']['mail_to'] = '更新通知先メールアドレス';
$LANG_confignames['databox']['mail_to_owner'] = '所有者に更新を通知する';
$LANG_confignames['databox']['mail_to_draft'] = '下書データの更新を通知する';
$LANG_confignames['databox']['allow_data_update'] = 'ユーザーに更新を許可する';
$LANG_confignames['databox']['allow_data_delete'] = 'ユーザーに削除を許可する';
$LANG_confignames['databox']['allow_data_insert'] = 'ユーザーに新規登録を許可する';
$LANG_confignames['databox']['admin_draft_default'] = '管理者新規登録のドラフトのデフォルト';
$LANG_confignames['databox']['user_draft_default'] = 'ユーザー新規登録のドラフトのデフォルト';

$LANG_confignames['databox']['dateformat'] = '日付書式　datepicker用';

$LANG_confignames['databox']['aftersave'] = '保存後の画面遷移 一般画面';
$LANG_confignames['databox']['aftersave_admin'] = '保存後の画面遷移 管理画面';

$LANG_confignames['databox']['grp_id_default'] = 'グループのデフォルト';

$LANG_confignames['databox']['default_img_url'] = 'デフォルト画像URL';

$LANG_confignames['databox']['maxlength_description'] = '入力制限文字数　説明';
$LANG_confignames['databox']['maxlength_meta_description'] = '入力制限文字数　説明文のメタタグ';
$LANG_confignames['databox']['maxlength_meta_keywords'] = '入力制限文字数　キーワードのメタタグ';

$LANG_confignames['databox']['hideuseroption'] = 'ユーザー情報に表示しない';

$LANG_confignames['databox']['commentcode'] = '新規登録時のコメントのデフォルト';

$LANG_confignames['databox']['sort_list_by'] = '管理者ページ（データ）の並べ替え';
$LANG_confignames['databox']['sort_list_by_my'] = 'マイデータの並べ替え';
$LANG_confignames['databox']['default_cache_time'] = 'デフォルトキャッシュタイム';

$LANG_confignames['databox']['disable_permission_ignore'] = '新着データの　permission ignore を無効にする';

$LANG_confignames['databox']['sitemap_excepts'] = 'サイトマップ XMLSitemap から除外するコード';

//--(1)
$LANG_tab['databox'][tab_whatsnew] = '新着情報ブロック';
$LANG_fs['databox'][fs_whatsnew] = '新着情報ブロック';
$LANG_confignames['databox']['whatsnew_interval'] = '新着の期間';
$LANG_confignames['databox']['hide_whatsnew'] = '新着ページを表示しない';
$LANG_confignames['databox']['title_trim_length'] = 'タイトル最大長';




//---(2)
$LANG_tab['databox'][tab_search] = '検索';
$LANG_fs['databox'][fs_search] = '検索結果';
$LANG_confignames['databox']['include_search'] = 'データを検索する';
$LANG_confignames['databox']['additionsearch'] = '検索対象にするアトリビュートの数';

//---(3)
$LANG_tab['databox'][tab_permissions] = 'パーミッション';
$LANG_fs['databox'][fs_permissions] = 'データのパーミッションのデフォルト（[0]所有者 [1]グループ [2]メンバー [3]ゲスト）';
$LANG_confignames['databox']['default_permissions'] = 'パーミッション';

//---(4)
$LANG_tab['databox'][tab_autotag] = '自動タグ';
$LANG_fs['databox'][fs_autotag] = '自動タグ';
$LANG_confignames['databox']['intervalday']="表示期間（日）";
$LANG_confignames['databox']['limitcnt']="表示件数";//@@@@@
$LANG_confignames['databox']['newmarkday']="新着マーク表示期間（日）";//@@@@@
$LANG_confignames['databox']['categories']="デフォルトカテゴリ";//@@@@@!!!!
$LANG_confignames['databox']['new_img']="新着マーク";//@@@@@
$LANG_confignames['databox']['rss_img']="RSSマーク";//@@@@@

//---(５)
$LANG_tab['databox']['tab_file'] = 'アップロードファイル';
$LANG_fs['databox']['fs_file'] = 'アップロードファイル';
$LANG_confignames['databox']['imgfile_size'] = 'イメージファイル(DB)の最大サイズ';
$LANG_confignames['databox']['imgfile_type'] = 'イメージファイル(DB)のタイプ';

$LANG_confignames['databox']['imgfile_size2'] = 'イメージファイル(外部)の最大サイズ';
$LANG_confignames['databox']['imgfile_type2'] = 'イメージファイル(外部)のタイプ';
$LANG_confignames['databox']['imgfile_frd'] = '画像';
$LANG_confignames['databox']['imgfile_thumb_frd'] = 'サムネイル';

$LANG_confignames['databox']['imgfile_thumb_ok'] = 'サムネイルを使用する？';
$LANG_confignames['databox']['imgfile_thumb_w'] = 'サムネイルを作成する大きさ（w）';
$LANG_confignames['databox']['imgfile_thumb_h'] = 'サムネイルを作成する大きさ（h）';
$LANG_confignames['databox']['imgfile_thumb_w2'] = 'サムネイルリンク先画像の大きさ（w2）';
$LANG_confignames['databox']['imgfile_thumb_h2'] = 'サムネイルリンク先画像の大きさ（h2）';
$LANG_confignames['databox']['imgfile_smallw'] = '表示する画像の最大横幅';
$LANG_confignames['databox']['imgfile_subdir'] = '画像保存URLにサブディレクトリを使用する';


$LANG_confignames['databox']['file_path'] = 'ファイル保存  絶対アドレス';
$LANG_confignames['databox']['file_size'] = 'ファイルサイズ';
$LANG_confignames['databox']['file_type'] = 'ファイルタイプ';
$LANG_confignames['databox']['file_subdir'] = 'ファイル保存アドレスにサブディレクトリを使用する';


//---(６)
$LANG_tab['databox']['tab_autotag_permissions'] = '自動タグのパーミッション';
$LANG_fs['databox']['fs_autotag_permissions'] = '自動タグのパーミッション （[0]所有者 [1]グループ [2]メンバー [3]ゲスト）';
$LANG_confignames['databox']['autotag_permissions_databox'] = '[databox: ] パーミッション';

//---(９)
$LANG_tab['databox']['tab_xml'] = 'XML';
$LANG_fs['databox']['fs_xml'] = '（OPTION:XML）';
$LANG_confignames['databox']['path_xml'] = 'XML一括インポートディレクトリ';
$LANG_confignames['databox']['path_xml_out'] = 'XMLエクスポートディレクトリ';
$LANG_confignames['databox']['xml_default_fieldset_id'] = 'XML一括インポートデフォルトタイプ';

//---(１０)
$LANG_tab['databox']['tab_csv'] = 'CSV';
$LANG_fs['databox']['fs_csv'] = '（OPTION:CSV）';
$LANG_confignames['databox']['path_csv'] = 'CSV一括インポートディレクトリ';
$LANG_confignames['databox']['path_csv_out'] = 'CSVエクスポートディレクトリ';
$LANG_confignames['databox']['csv_default_fieldset_id'] = 'CSV一括インポートデフォルトタイプ';
$LANG_confignames['databox']['csv_default_owner_id'] = 'CSV一括インポートデフォルト所有者ID';
$LANG_confignames['databox']['csv_cron_schedule_interval'] = 'Cronのスケジュール間隔 ';
$LANG_confignames['databox']['csv_cron_schedule_unlink'] = 'スケジュール.終了後入力ファイル削除 ';
$LANG_confignames['databox']['csv_cron_schedule_nextmaps'] = 'スケジュール.続けてMaps実行 ';
$LANG_confignames['databox']['csv_cron_schedule_sel_id'] = 'スケジュール.条件 ';


//---(１１)
$LANG_tab['databox']['tab_maps'] = 'MAPS';
$LANG_fs['databox']['fs_maps'] = '（OPTION:MAPS）';
$LANG_confignames['databox']['maps_mid'] = 'マップIDを登録するマスターの種別';
$LANG_confignames['databox']['maps_lat'] = '緯度を登録するアトリビュートのテーマ変数';
$LANG_confignames['databox']['maps_lng'] = '経度を登録するアトリビュートのテーマ変数';
$LANG_confignames['databox']['maps_pref'] = '都道府県を登録するアトリビュートのテーマ変数';
$LANG_confignames['databox']['maps_address1'] = '住所１を登録するアトリビュートのテーマ変数';
$LANG_confignames['databox']['maps_address2'] = '住所２を登録するアトリビュートのテーマ変数';
$LANG_confignames['databox']['maps_address3'] = '住所３を登録するアトリビュートのテーマ変数';
$LANG_confignames['databox']['maps_cron_schedule_interval'] = 'Cronのスケジュール間隔 ';

// Note: entries 0, 1, 9, 12, 17 are the same as in $LANG_configselects['Core']
$LANG_configselects['databox'][0] =array('はい' => 1, 'いいえ' => 0);
$LANG_configselects['databox'][1] =array('はい' => TRUE, 'いいえ' => FALSE);
$LANG_configselects['databox'][12] =array('アクセス不可' => 0, '表示' => 2, '表示・編集' => 3);
$LANG_configselects['databox'][13] =array('アクセス不可' => 0, '利用する' => 2);

$LANG_configselects['databox'][5] =array(
    '表示しない' => 'hide'
    , '編集日付によって表示する' => 'modified'
    , '作成日付によって表示する' => 'created'
    , '公開日によって表示する' => 'released'
);

//$LANG_configselects['databox'][17] =array('アクセス不可' => 0, '表示' => 2, '表示・編集' => 3);

$LANG_configselects['databox'][20] =array(
    '標準' => 'standard'
    , 'カスタム' => 'custom'
    , 'テーマ' => 'theme');

//@@@@@
$LANG_configselects['databox'][21] =array(
     '編集日付による' => 'modified'
    , '作成日付による' => 'created'
    , '公開日による' => 'released'
);

$LANG_configselects['databox'][22] =array(
    'ヘッダ・フッタ・左ブロックあり（右ブロックはテーマ設定による）' => 'standard'
    , 'ヘッダ・フッタ・左右ブロックあり' => 'leftrightblocks'
    , '全画面表示（ヘッダ・フッタ・ブロックなし）' => 'blankpage'
    , 'ヘッダ・フッタあり（ブロックなし）' => 'noblocks'
    , 'ヘッダ・フッタ・左ブロックあり（右ブロックなし）' => 'leftblocks'
    , 'ヘッダ・フッタ・右ブロックあり（左ブロックなし）' => 'rightblocks'

    );

$LANG_configselects['databox'][23] =array(
    'はい' => 3
    ,'一覧と詳細' => 2
    ,'詳細のみ' => 1
    , 'いいえ' => 0
    );


$LANG_configselects['databox'][9] =array(
    '画面遷移なし' => 'no'
    ,'ページを表示する' => 'item'
    , '一覧を表示する' => 'list'
    , 'ホームを表示する' => 'home'
    , '管理画面トップを表示する' => 'admin'
    , 'プラグイントップを表示する' => 'plugin'

        );
$LANG_configselects['databox'][25] =array(
    '画面遷移なし' => 'no'
    ,'ページを表示する' => 'item'
    , '一覧を表示する' => 'list'
    , 'ホームを表示する' => 'home'
    , 'プラグイントップを表示する' => 'plugin'
        );
//
$LANG_configselects['databox'][24] =array();
    $sql = LB;
    $sql .= "SELECT ".LB;
    $sql .= " grp_id".LB;
    $sql .= ",grp_name".LB;
    $sql .= " FROM {$_TABLES['groups']}".LB;
    $sql .= " ORDER BY grp_name".LB;
    $result = DB_query( $sql );
    $nrows = DB_numRows( $result );

    for( $i = 0; $i < $nrows; $i++ )    {
        $A = DB_fetchArray( $result, true );
        $grp_name=$A['grp_name'];
        $grp_id=$A['grp_id'];
        $LANG_configselects['databox'][24][$grp_name]=$grp_id;
}

$LANG_configselects['databox'][26] =array( 'コメント有効' => 0, 'コメント無効' => -1);

$LANG_configselects['databox'][27] =array(
    '表示位置' => 'orderno'
    ,'ID' => 'id'
    , 'コード' => 'code'
    , 'タイトル' => 'title'
    , 'タイプ' => 'fieldset_name'
    , '残日数' => 'remaingdays'
    , '閲覧数' => 'hits'
    , 'タイムスタンプ降順' => 'udatetime'
    , 'ドラフト' => 'draft_flag'
        );

//
$LANG_configselects['databox'][28] =array();
    $sql = LB;
    $sql .= "SELECT ".LB;
    $sql .= " fieldset_id".LB;
    $sql .= ",name".LB;
    $sql .= " FROM {$_TABLES['DATABOX_def_fieldset']}".LB;
    $sql .= " ORDER BY fieldset_id".LB;
    $result = DB_query( $sql );
    $nrows = DB_numRows( $result );

    for( $i = 0; $i < $nrows; $i++ )    {
        $A = DB_fetchArray( $result, true );
        $name=$A['name'];
        $fieldset_id=$A['fieldset_id'];
        $LANG_configselects['databox'][28][$name]=$fieldset_id;
    }

//
$LANG_configselects['databox'][29] =array();
    $sql="show tables like '{$_TABLES['DATABOX_def_csv_sel']}'" ;

    $rt=DB_query($sql);
    $rt=DB_numRows($rt);

    if  ($rt<>0){
        $sql = LB;
        $sql .= "SELECT ".LB;
        $sql .= " csv_sel_id".LB;
        $sql .= ",name".LB;
        $sql .= " FROM {$_TABLES['DATABOX_def_csv_sel']}".LB;
        $sql .= " ORDER BY csv_sel_id".LB;
        $result = DB_query( $sql );
        $nrows = DB_numRows( $result );
        $LANG_configselects['databox'][29]['条件なし']="";
        for( $i = 0; $i < $nrows; $i++ )    {
            $A = DB_fetchArray( $result, true );
            $name=$A['name'];
            $csv_sel_id=$A['csv_sel_id'];
            $LANG_configselects['databox'][29][$name]=$csv_sel_id;
        }
    }
$LANG_configselects['databox'][30] =array(
    '表示位置' => 'orderno'
    ,'ID' => 'id'
    , 'コード' => 'code'
    , 'タイトル' => 'title'
    , 'タイプ' => 'fieldset_name'
    , '残日数' => 'remaingdays'
    , 'タイムスタンプ降順' => 'udatetime'
    , 'ドラフト' => 'draft_flag'
        );

?>