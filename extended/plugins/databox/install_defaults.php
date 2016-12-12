<?php

// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | DataBox Plugin  データボックスプラグイン                                  |
// +---------------------------------------------------------------------------+z
// | install_defaults.php                                                      |
// |                                                                           |
// | Initial Installation Defaults used when loading the online configuration  |
// | records. These settings are only used during the initial installation     |
// | and not referenced any more once the plugin is installed.                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000-2008 by the following authors:                         |
// |                                                                           |
// | Authors: Tony Bibbs         - tony AT tonybibbs DOT com                   |
// |          Mark Limburg       - mlimburg AT users.sourceforge DOT net       |
// |          Jason Whittenburg  - jwhitten AT securitygeeks DOT com           |
// |          Dirk Haun          - dirk AT haun-online DOT de                  |
// |          Trinity Bays       - trinity93 AT gmail DOT com                  |z
// |          Oliver Spiesshofer - oliver AT spiesshofer DOT com               |
// |          Euan McKay         - info AT heatherengineering DOT com          |
// +---------------------------------------------------------------------------+
// |                                                                           |
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
// |                                                                           |
// +---------------------------------------------------------------------------+
//
// $Id: install_defaults.php
// 20120403

if (strpos($_SERVER['PHP_SELF'], 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

global $_DATABOX_DEFAULT;
global $_TABLES;

$_DATABOX_DEFAULT = array();


// +---------------------------------------------------------------------------+
// + 初期値
//メイン設定
// 1ページの行数
$_DATABOX_DEFAULT['perpage']=9;
//ログインを要求する
$_DATABOX_DEFAULT['loginrequired'] = 0;

//メニュに表示しない
$_DATABOX_DEFAULT['hidemenu'] = 0;

//カテゴリ　コードを使用する
$_DATABOX_DEFAULT['categorycode']=1;
//データ　コードを使用する
$_DATABOX_DEFAULT['datacode']=1;
//グループ　コードを使用する
$_DATABOX_DEFAULT['groupcode']=1;
//DataBox のTOPで表示するプログラム
$_DATABOX_DEFAULT['top']="/databox/list.php";
//DataBox の個別表示で使用するプログラム
$_DATABOX_DEFAULT['detail']="data.php";

//テンプレート 一般画面
$_DATABOX_DEFAULT['templates']="standard";
//テンプレート 管理画面
$_DATABOX_DEFAULT['templates_admin']="standard";

//テーマテンプレートパス';//@@@@@
$_DATABOX_DEFAULT['themespath'] ="databox/templates/";
//所有者の削除と共に削除する
$_DATABOX_DEFAULT['delete_data'] = 0;//@@@@@
//使用する日付（編集日付or作成日付）';//@@@@@
$_DATABOX_DEFAULT['datefield'] = "modified";

// Display Meta Tags for databox pages (1 = show, 0 = don't)
$_DATABOX_DEFAULT['meta_tags'] = 0;

$_DATABOX_DEFAULT['layout'] = 'standard';
$_DATABOX_DEFAULT['layout_admin'] = 'standard';

// メールの送信先
$_DATABOX_DEFAULT['mail_to'] = array();

//所有者に修正を通知する　default いいえ
$_DATABOX_DEFAULT['mail_to_owner'] = 0;

//下書データの修正を通知する　default いいえ
$_DATABOX_DEFAULT['mail_to_draft'] = 0;

//日付書式　datepicker用
$_DATABOX_DEFAULT['dateformat'] = 'Y/m/d';

//データ保存後の画面遷移　一般画面
$_DATABOX_DEFAULT['aftersave'] = 'item';
//データ保存後の画面遷移　管理画面
$_DATABOX_DEFAULT['aftersave_admin'] = 'item';

//グループのデフォルト
$grp_id =DB_getItem($_TABLES['groups'], 'grp_id', "grp_name='DataBox Editor'");
$_DATABOX_DEFAULT['grp_id_default'] = $grp_id;


//----------------------
//（データ）更新を許可する　default はい
$_DATABOX_DEFAULT['allow_data_update'] = 1;//@@@@@@

//（データ）削除を許可する　default いいえ
$_DATABOX_DEFAULT['allow_data_delete'] = 0;

//（データ）新規登録を許可する　default いいえ
$_DATABOX_DEFAULT['allow_data_insert'] = 0;


//（データ）管理者の新規登録のドラフトのデフォルト　default いいえ
$_DATABOX_DEFAULT['admin_draft_default'] = 0;
//（データ）ユーザの新規登録のドラフトのデフォルト　default はい
$_DATABOX_DEFAULT['user_draft_default'] = 1;

//デフォルト画像URL
$_DATABOX_DEFAULT['default_img_url'] = "";

//入力制限文字数
$_DATABOX_DEFAULT['maxlength_description'] = "1677215";
$_DATABOX_DEFAULT['maxlength_meta_description'] = "65535";
$_DATABOX_DEFAULT['maxlength_meta_keywords'] = "65535";

//ユーザーメニューに表示しない
$_DATABOX_DEFAULT['hideuseroption'] = 0;

//新規登録時のコメントのデフォルト
$_DATABOX_DEFAULT['commentcode'] = 0;

//管理者ページ（データ）の並び替え
$_DATABOX_DEFAULT['sort_list_by'] = "orderno";
//マイデータの並び替え
$_DATABOX_DEFAULT['sort_list_by_my'] = "orderno";

//デフォルトキャッシュタイム
$_DATABOX_DEFAULT['default_cache_time'] = "0";

//permission ignoreを無効にする　default いいえ
$_DATABOX_DEFAULT['disable_permission_ignore'] = 0;

// サイトマップ（XMLSitemap）から除外するコード
$_DATABOX_DEFAULT['sitemap_excepts'] = array();

//---（１）新着
// 新着の期間
$_DATABOX_DEFAULT['whatsnew_interval'] = 1209600; // 2 weeks
//新着を表示しない
$_DATABOX_DEFAULT['hide_whatsnew'] = "hide";
//タイトル最大長
$_DATABOX_DEFAULT['title_trim_length'] = 20;

//---(2)検索
//データボックスを検索する
$_DATABOX_DEFAULT['include_search'] = 1;
//検索対象にする追加項目の数
$_DATABOX_DEFAULT['additionsearch'] = 10;


//---(3)パーミッションデフォルト
$_DATABOX_DEFAULT['default_permissions'] = array(3, 2, 2, 2);

//（４）自動タグ
$_DATABOX_DEFAULT['intervalday'] = 90;
$_DATABOX_DEFAULT['limitcnt'] = 10;
$_DATABOX_DEFAULT['newmarkday']=3;
$_DATABOX_DEFAULT['categories']="";
$_DATABOX_DEFAULT['new_img']='<span class="databox_new">new!</span>';
$_DATABOX_DEFAULT['rss_img']='<span class="databox_rss">RSS</span>';

//（5）file
$_DATABOX_DEFAULT['imgfile_size'] = "1048576";
$_DATABOX_DEFAULT['imgfile_type'] = array('image/jpeg','image/gif','image/png','image/jpg','image/pjpeg','image/x-png' );
//png bmp

$_DATABOX_DEFAULT['imgfile_size2'] = "1048576";
$_DATABOX_DEFAULT['imgfile_type2'] = array('image/jpeg','image/gif','image/png','image/jpg','image/pjpeg','image/x-png','image/svg+xml');

// ★画像保存URL  サイトURL/ の後の指定
$_DATABOX_DEFAULT['imgfile_frd'] = "images/databox/";
//サムネイル
// ★サムネイル画像保存URL  サイトURL/ の後の指定
$_DATABOX_DEFAULT['imgfile_thumb_frd'] = "images/databox/";

/*-- サムネイル--*/
//サムネイルを使用する？ Yes=1 No=0(GDライブラリ利用不可の場合は自動判定)
$_DATABOX_DEFAULT['imgfile_thumb_ok'] = "1";
//サムネイルの大きさ(これ以上の大きい画像はjpg,pngのサムネイル作成)
$_DATABOX_DEFAULT['imgfile_thumb_w'] = 160;
$_DATABOX_DEFAULT['imgfile_thumb_h'] = 100;
//リンク先の画像の大きさ(これ以上の大きい画像は縮小する)
$_DATABOX_DEFAULT['imgfile_thumb_w2'] = 640;
$_DATABOX_DEFAULT['imgfile_thumb_h2'] = 640;

/*-- サムネイル作成できない(orしない)場合)--*/
//アイテム内に表示する画像の最大横幅(imgタグ内のwidthの値)
$_DATABOX_DEFAULT['imgfile_smallw'] = 160;

//画像保存URLにサブディレクトリを使用する
//はい、いいえ　デフォルト＝いいえ 
$_DATABOX_DEFAULT['imgfile_subdir'] = 0;


// ★ファイル保存  絶対アドレスの指定
$_DATABOX_DEFAULT['file_path'] = $_CONF['path_data']."databox_data/";
$_DATABOX_DEFAULT['file_size'] = "";
$_DATABOX_DEFAULT['file_type'] = array();
$_DATABOX_DEFAULT['file_type'][] ="application/vnd.oasis.opendocument.text";//odt :open office writer*
$_DATABOX_DEFAULT['file_type'][] ="application/msword";//doc :Microsoft Word
$_DATABOX_DEFAULT['file_type'][] ="application/vnd.openxmlformats";//docx: Microsoft Office Word 2007*
$_DATABOX_DEFAULT['file_type'][] ="text/html";//html
$_DATABOX_DEFAULT['file_type'][] ="application/octet-stream ";//odb :OpenOffice Base*
$_DATABOX_DEFAULT['file_type'][] ="application/vnd.oasis.opendocument.formula";//odf:OpenOffice Calc*
$_DATABOX_DEFAULT['file_type'][] ="application/vnd.oasis.opendocument.graphics";//odg:OpenOffice Draw*
$_DATABOX_DEFAULT['file_type'][] ="application/vnd.oasis.opendocument.text-master";//odm:OpenOffice Writer*
$_DATABOX_DEFAULT['file_type'][] ="application/vnd.oasis.opendocument.presentation";//odp:OpenOffice Impress*
$_DATABOX_DEFAULT['file_type'][] ="application/vnd.oasis.opendocument.spreadsheet";//ods:OpenOffice Calc*
$_DATABOX_DEFAULT['file_type'][] ="application/vnd.oasis.opendocument.graphics-template";//otg:OpenOffice Draw*
$_DATABOX_DEFAULT['file_type'][] ="application/octet-stream";//oth: OpenOffice.org Writer*
$_DATABOX_DEFAULT['file_type'][] ="application/vnd.oasis.opendocument.presentation-template";//otp:OpenOffice Impress*
$_DATABOX_DEFAULT['file_type'][] ="application/octet-stream";//ots:OtsAV*
$_DATABOX_DEFAULT['file_type'][] ="application/vnd.oasis.opendocument.formula-template ";//ott:OpenOffice Writer*
$_DATABOX_DEFAULT['file_type'][] ="application/vnd.openofficeorg.extension";//oxt:OpenOffice *
$_DATABOX_DEFAULT['file_type'][] ="application/pdf";//pdf
$_DATABOX_DEFAULT['file_type'][] ="application/mspowerpoint";//ppt:Microsoft PowerPoint*
$_DATABOX_DEFAULT['file_type'][] ="application/vnd.openxmlformats";//pptx:Microsoft Office PowerPoint 2007*
$_DATABOX_DEFAULT['file_type'][] ="text/plain";//txt
$_DATABOX_DEFAULT['file_type'][] ="application/vnd.ms-excel";//xls:Microsoft Excel*
$_DATABOX_DEFAULT['file_type'][] ="application/vnd.openxmlformats ";//xlsx:Microsoft Office Excel 2007*
$_DATABOX_DEFAULT['file_type'][] ="text/xml";//xml

$_DATABOX_DEFAULT['file_subdir'] = 0;

//(6) autotag permissions
$_DATABOX_DEFAULT['autotag_permissions_databox'] = array (2, 2, 2, 2);


//（9）XML
$_DATABOX_DEFAULT['path_xml'] = $_CONF['path_html']."databox_data";
$_DATABOX_DEFAULT['path_xml_out']=$_CONF['path']."data/databox_data";
$_DATABOX_DEFAULT['xml_default_fieldset_id']="";

//（10）CSV
$_DATABOX_DEFAULT['path_csv'] = $_CONF['path_html']."databox_csv";
$_DATABOX_DEFAULT['path_csv_out']=$_CONF['path']."data/databox_csv";
$_DATABOX_DEFAULT['csv_default_fieldset_id']="";
$_DATABOX_DEFAULT['csv_default_owner_id']="2";
$_DATABOX_DEFAULT['csv_cron_schedule_interval']="0";
$_DATABOX_DEFAULT['csv_cron_schedule_unlink']="0";
$_DATABOX_DEFAULT['csv_cron_schedule_nextmaps']="0";
$_DATABOX_DEFAULT['csv_cron_schedule_sel_id']="0";

//（11）MAPS
$_DATABOX_DEFAULT['maps_mid'] = "mapid";
$_DATABOX_DEFAULT['maps_lat'] = "lat";
$_DATABOX_DEFAULT['maps_lng'] = "lng";
$_DATABOX_DEFAULT['maps_pref'] = "pref";
$_DATABOX_DEFAULT['maps_address1'] = "address1";
$_DATABOX_DEFAULT['maps_address2'] = "address2";
$_DATABOX_DEFAULT['maps_address3'] = "address3";
$_DATABOX_DEFAULT['maps_cron_schedule_interval']="0";

// +---------------------------------------------------------------------------+


/**
* Initialize Links plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. Initial values will be taken from $_LI_CONF if available (e.g. from
* an old config.php), uses $_LI_DEFAULT otherwise.
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_databox()
{
    global $_DATABOX_CONF;
    global $_DATABOX_DEFAULT;



    if (is_array($_DATABOX_CONF) && (count($_DATABOX_CONF) > 1)) {
        $_DATABOX_DEFAULT = array_merge($_DATABOX_DEFAULT, $_DATABOX_CONF);
    }

    $pi_name="databox";


    $c = config::get_instance();

    if (!$c->group_exists($pi_name)) {
    /**
     * Adds a configuration variable to the config object
     *
     * @param string $param_name        name of the parameter to add
     * @param mixed  $default_value     the default value of the parameter
     *                                  (also will be the initial value)
     * @param string $display_name      name that will be displayed on the
     *                                  user interface
     * @param string $type              the type of the configuration variable
     *
     *    If the configuration variable is an array, prefix this string with
     *    '@' if the administrator should NOT be able to add or remove keys
     *    '*' if the administrator should be able to add named keys
     *    '%' if the administrator should be able to add numbered keys
     *    These symbols can be repeated like such: @@text if the configuration
     *    variable is an array of arrays of text.
     *    The base variable types are:
     *    'text'    textbox displayed     string  value stored
     *    'select'  selectbox displayed   string  value stored
     *    'hidden'  no display            string  value stored
     *
     * @param string $subgroup          subgroup of the variable
     *                                  (the second row of tabs on the user interface)
     * @param string $fieldset          the fieldset to display the variable under
     * @param array  $selection_array   possible selections for the 'select' type
     *                                  this MUST be passed if you use the 'select'
     *                                  type
     * @param int    $sort              sort rank on the user interface (ascending)
     *
     * @param boolean $set              whether or not this parameter is set
     */
        //function add(
        // $param_name
        //  , $default_value
        //  , $type,$subgroup,$fieldset,$selection_array, $sort, $set
        //  , $group=$pi_name)

        //メイン
        $c->add(
            'sg_main'
            , NULL , 'subgroup'
            , 0, 0
            , NULL, 0, true
            , $pi_name);
        //(0)メイン設定
        $c->add('tab_main', NULL, 'tab', 0, 0, NULL, 0, true, $pi_name, 0);
        $c->add(
            'fs_main'
            , NULL, 'fieldset'
            , 0, 0
            , NULL, 0, true
            , $pi_name
            ,0);
        $c->add(
            'perpage'
            ,$_DATABOX_DEFAULT['perpage']
            ,'text', 0, 0, NULL, 10, TRUE
            , $pi_name
            ,0);
        $c->add(
            'loginrequired'
            ,$_DATABOX_DEFAULT['loginrequired']
            ,'select', 0, 0, 23, 20, TRUE
            , $pi_name
            ,0);
        $c->add(
            'hidemenu'
            ,$_DATABOX_DEFAULT['hidemenu']
            ,'select', 0, 0, 0, 30, true
            , $pi_name
            ,0);

        $c->add(
            'categorycode'
            , $_DATABOX_DEFAULT['categorycode']
            , 'select',  0, 0, 1, 40, true
            , $pi_name
            ,0);
        $c->add(
            'datacode'
            , $_DATABOX_DEFAULT['datacode']
            , 'select',  0, 0, 1, 50, true
            , $pi_name
            ,0);
        $c->add(
            'groupcode'
            , $_DATABOX_DEFAULT['groupcode']
            , 'select',  0, 0, 1, 60, true
            , $pi_name
            ,0);

        $c->add(
            'top'
            ,$_DATABOX_DEFAULT['top']
            ,'text', 0, 0, NULL, 70, TRUE
            , $pi_name
            ,0);
        $c->add(
            'detail'
            ,$_DATABOX_DEFAULT['detail']
            ,'text', 0, 0, NULL, 80, TRUE
            , $pi_name
            ,0);
        $c->add(
            'templates'
            , $_DATABOX_DEFAULT['templates']
            , 'select',  0, 0, 20, 90, true
            , $pi_name
            ,0);
        $c->add(
            'templates_admin'
            , $_DATABOX_DEFAULT['templates_admin']
            , 'select',  0, 0, 20, 100, true
            , $pi_name
            ,0);
        $c->add(
            'themespath'
            ,$_DATABOX_DEFAULT['themespath']
            ,'text', 0, 0, NULL, 110, TRUE
            , $pi_name
            ,0);

        $c->add(
            'delete_data'
            , $_DATABOX_DEFAULT['delete_data']
            , 'select',  0, 0, 1, 120, true
            , $pi_name
            ,0);
        $c->add(
            'datefield'
            , $_DATABOX_DEFAULT['datefield']
            , 'select',  0, 0, 21, 130, true
            , $pi_name
            ,0);


        $c->add('meta_tags'
            , $_DATABOX_DEFAULT['meta_tags']
            , 'select',  0, 0, 0, 140, true
            , $pi_name
            ,0);

        $c->add(
            'layout'
            , $_DATABOX_DEFAULT['layout']
            , 'select',  0, 0, 22, 150, true
            , $pi_name
            ,0);

        $c->add(
            'layout_admin'
            , $_DATABOX_DEFAULT['layout_admin']
            , 'select',  0, 0, 22, 160, true
            , $pi_name
            ,0);

        $c->add(
            'mail_to'
            ,array()
            ,'%text', 0, 0, 0, 170, TRUE
            , $pi_name
            ,0);
        
        $c->add(
            'mail_to_owner'
            ,$_DATABOX_DEFAULT['mail_to_owner']
            ,'select', 0, 0, 0, 180, true
            , $pi_name
            ,0);
        
        $c->add(
            'mail_to_draft'
            ,$_DATABOX_DEFAULT['mail_to_draft']
            ,'select', 0, 0, 0, 190, true
            , $pi_name
            ,0);
        
        
        
        
        
        
        $c->add(
            'allow_data_update'
            ,$_DATABOX_DEFAULT['allow_data_update']
            ,'select', 0, 0, 0, 200, true
            , $pi_name
            ,0);



        $c->add(
            'allow_data_delete'
            ,$_DATABOX_DEFAULT['allow_data_delete']
            ,'select', 0, 0, 0, 210, true
            , $pi_name
            ,0);

        $c->add(
            'allow_data_insert'
            ,$_DATABOX_DEFAULT['allow_data_insert']
            ,'select', 0, 0, 0, 220, true
            , $pi_name
            ,0);
        
        $c->add(
            'admin_draft_default'
            ,$_DATABOX_DEFAULT['admin_draft_default']
            ,'select', 0, 0, 0, 230, true
            , $pi_name
            ,0);
        
        $c->add(
            'user_draft_default'
            ,$_DATABOX_DEFAULT['user_draft_default']
            ,'select', 0, 0, 0, 240, true
            , $pi_name
            ,0);

        //
        $c->add(
            'dateformat'
            ,$_DATABOX_DEFAULT['dateformat']
            ,'text', 0, 0, NULL, 250, TRUE
            , $pi_name
            ,0);

        $c->add(
            'aftersave'
            ,$_DATABOX_DEFAULT['aftersave']
            ,'select', 0, 0, 25, 260, true
            , $pi_name
            ,0);

        $c->add(
            'aftersave_admin'
            ,$_DATABOX_DEFAULT['aftersave_admin']
            ,'select', 0, 0, 9, 270, true
            , $pi_name
            ,0);

        $c->add(
            'grp_id_default'
            ,$_DATABOX_DEFAULT['grp_id_default']
            ,'select', 0, 0, 24, 280, true
            , $pi_name
            ,0);

        $c->add(
            'default_img_url'
            ,$_DATABOX_DEFAULT['default_img_url']
            ,'text', 0, 0, NULL, 290, TRUE
            , $pi_name
            ,0);
        
        $c->add(
            'maxlength_description'
            ,$_DATABOX_DEFAULT['maxlength_description']
            ,'text', 0, 0, NULL, 300, TRUE
            , $pi_name
            ,0);
        $c->add(
            'maxlength_meta_description'
            ,$_DATABOX_DEFAULT['maxlength_meta_description']
            ,'text', 0, 0, NULL, 310, TRUE
            , $pi_name
            ,0);
        $c->add(
            'maxlength_meta_keywords'
            ,$_DATABOX_DEFAULT['maxlength_meta_keywords']
            ,'text', 0, 0, NULL, 320, TRUE
            , $pi_name
            ,0);
        
        $c->add(
            'hideuseroption'
            ,$_DATABOX_DEFAULT['hideuseroption']
            ,'select', 0, 0, 0, 330, true
            , $pi_name
            ,0);
        
        $c->add(
            'commentcode'
            ,$_DATABOX_DEFAULT['commentcode']
            ,'select', 0, 0, 26, 340, true
            , $pi_name
            ,0);
        
        $c->add(
            'sort_list_by'
            ,$_DATABOX_DEFAULT['sort_list_by']
            ,'select', 0, 0, 27, 350, true
            , $pi_name
            ,0);
        $c->add(
            'sort_list_by_my'
            ,$_DATABOX_DEFAULT['sort_list_by_my']
            ,'select', 0, 0, 30, 360, true
            , $pi_name
            ,0);
			
        $c->add(
            'default_cache_time'
            ,$_DATABOX_DEFAULT['default_cache_time']
            ,'text', 0, 0, NULL, 370, TRUE
            , $pi_name
            ,0);
        
        $c->add(
            'disable_permission_ignore'
            ,$_DATABOX_DEFAULT['disable_permission_ignore']
            ,'select', 0, 0, 0, 380, true
            , $pi_name
            ,0);
		
        $c->add(
            'sitemap_excepts'
            ,$_DATABOX_DEFAULT['sitemap_excepts']
            ,'%text', 0, 0, 0, 390, TRUE
            , $pi_name
            ,0);

        //(1)新着
        $c->add('tab_whatsnew', NULL, 'tab', 0, 1, NULL, 0, true, $pi_name, 1);
        $c->add(
            'fs_whatsnew'
            , NULL, 'fieldset'
            , 0, 1
            , NULL, 0, true
            , $pi_name
            ,1);
        $c->add(
            'whatsnew_interval'
            ,$_DATABOX_DEFAULT['whatsnew_interval']
            ,'text', 0, 1, NULL, 10, TRUE
            , $pi_name
            ,1);
        $c->add(
            'hide_whatsnew'
            ,$_DATABOX_DEFAULT['hide_whatsnew']
            ,'select', 0, 1, 5, 20, true
            , $pi_name
            ,1);
        $c->add(
            'title_trim_length'
            ,$_DATABOX_DEFAULT['title_trim_length']
            ,'text', 0, 1, NULL, 30, TRUE
            , $pi_name
            ,1);


        //(2)検索
        $c->add('tab_search', NULL, 'tab', 0, 2, NULL, 0, true, $pi_name, 2);
        $c->add(
            'fs_search'
            , NULL, 'fieldset'
            , 0, 2, NULL, 0, true
            , $pi_name
            ,2);
        
        $c->add(
            'include_search'
            , $_DATABOX_DEFAULT['include_search']
            , 'select',  0, 2, 0, 10, true
            , $pi_name
            ,2);
        
        $c->add(
            'additionsearch'
            , $_DATABOX_DEFAULT['additionsearch']
            ,'text', 0, 2, NULL, 20, TRUE
            , $pi_name
            ,2);


        //(3)パーミッション
        $c->add('tab_permissions', NULL, 'tab', 0, 3, NULL, 0, true, $pi_name, 3);
        $c->add(
            'fs_permissions'
            , NULL, 'fieldset'
            , 0, 3
            , NULL, 0, true
            , $pi_name
            ,3);
        
        $c->add(
            'default_permissions'
            , $_DATABOX_DEFAULT['default_permissions']
            ,'@select' , 0, 3 , 12, 130, true
            , $pi_name
            ,3);

        //(4)自動タグ
        $c->add('tab_autotag', NULL, 'tab', 0, 4, NULL, 0, true, $pi_name, 4);
        $c->add(
            'fs_autotag'
            , NULL, 'fieldset'
            , 0, 4, NULL, 0, true
            , $pi_name
            ,4);

        $c->add(
            'intervalday'
            ,$_DATABOX_DEFAULT['intervalday']
            ,'text', 0, 4, NULL, 10, TRUE
            , $pi_name
            ,4);
        
        $c->add(
            'limitcnt'
            ,$_DATABOX_DEFAULT['limitcnt']
            ,'text', 0, 4, NULL, 20, TRUE
            , $pi_name
            ,4);
        
        $c->add(
            'newmarkday'
            ,$_DATABOX_DEFAULT['newmarkday']
            ,'text', 0, 4, NULL, 30, TRUE
            , $pi_name
            ,4);
        
        $c->add(
            'categories'
            ,$_DATABOX_DEFAULT['categories']
            ,'text', 0, 4, NULL, 40, TRUE
            , $pi_name
            ,4);
        
        $c->add(
            'new_img'
            ,$_DATABOX_DEFAULT['new_img']
            ,'textarea', 0, 4, NULL, 50, TRUE
            , $pi_name
            ,4);
        
        $c->add(
            'rss_img'
            ,$_DATABOX_DEFAULT['rss_img']
            ,'textarea', 0, 4, NULL, 60, TRUE
            , $pi_name
            ,4);

       //(5)file
        $c->add('tab_file', NULL, 'tab', 0, 5, NULL, 0, true, $pi_name, 5);
        $c->add(
            'fs_file'
            , NULL, 'fieldset'
            , 0, 5, NULL, 0, true
            , $pi_name
            ,5);

        $c->add(
            'imgfile_size'
            ,$_DATABOX_DEFAULT['imgfile_size']
            ,'text', 0, 5, NULL, 10, TRUE
            , $pi_name
            ,5);
        
        $c->add(
            'imgfile_type'
            ,$_DATABOX_DEFAULT['imgfile_type']
            ,'%text', 0, 5, 0, 20, TRUE
            , $pi_name
            ,5);
        
        
        $c->add(
            'imgfile_size2'
            ,$_DATABOX_DEFAULT['imgfile_size2']
            ,'text', 0, 5, NULL, 30, TRUE
            , $pi_name
            ,5);
        $c->add(
            'imgfile_type2'
            ,$_DATABOX_DEFAULT['imgfile_type2']
            ,'%text', 0, 5, 0, 40, TRUE
            , $pi_name
            ,5);
        
        $c->add(
            'imgfile_frd'
            ,$_DATABOX_DEFAULT['imgfile_frd']
            ,'text', 0, 5, NULL, 50, TRUE
            , $pi_name
            ,5);
        
        $c->add(
            'imgfile_thumb_frd'
            ,$_DATABOX_DEFAULT['imgfile_thumb_frd']
            ,'text', 0, 5, NULL, 60, TRUE
            , $pi_name
            ,5);
        
        $c->add(
            'imgfile_thumb_ok'
            ,$_DATABOX_DEFAULT['imgfile_thumb_ok']
            ,'select', 0, 5, 0, 70, TRUE
            , $pi_name
            ,5);
        
        $c->add(
            'imgfile_thumb_w'
            ,$_DATABOX_DEFAULT['imgfile_thumb_w']
            ,'text', 0, 5, NULL, 80, TRUE
            , $pi_name
            ,5);
        
        $c->add(
            'imgfile_thumb_h'
            ,$_DATABOX_DEFAULT['imgfile_thumb_h']
            ,'text', 0, 5, NULL, 90, TRUE
            , $pi_name
            ,5);
        
        $c->add(
            'imgfile_thumb_w2'
            ,$_DATABOX_DEFAULT['imgfile_thumb_w2']
            ,'text', 0, 5, NULL,100, TRUE
            , $pi_name
            ,5);
        
        $c->add(
            'imgfile_thumb_h2'
            ,$_DATABOX_DEFAULT['imgfile_thumb_h2']
            ,'text', 0, 5, NULL, 110, TRUE
            , $pi_name
            ,5);
        
        $c->add(
            'imgfile_smallw'
            ,$_DATABOX_DEFAULT['imgfile_smallw']
            ,'text', 0, 5, NULL, 120, TRUE
            , $pi_name
            ,5);

        $c->add(
            'imgfile_subdir'
            ,$_DATABOX_DEFAULT['imgfile_subdir']
            ,'select', 0, 5, 0, 130, TRUE
            , $pi_name
            ,5);

        $c->add(
            'file_path'
            ,$_DATABOX_DEFAULT['file_path']
            ,'text', 0, 5, NULL, 140, TRUE
            , $pi_name
            ,5);
        
        
        $c->add(
            'file_size'
            ,$_DATABOX_DEFAULT['file_size']
            ,'text', 0, 5, NULL, 150, TRUE
            , $pi_name
            ,5);
        
        $c->add(
            'file_type'
            ,$_DATABOX_DEFAULT['file_type']
            ,'%text', 0, 5, 0, 160, TRUE
            , $pi_name
            ,5);
        $c->add(
            'file_subdir'
            ,$_DATABOX_DEFAULT['file_subdir']
            ,'select', 0, 5, 0, 170, TRUE
            , $pi_name
            ,5);

        
        //(6)autotag_permissions
        $c->add('tab_autotag_permissions', NULL, 'tab', 0, 6, NULL, 0, true, $pi_name, 6);
        $c->add(
            'fs_autotag_permissions'
            , NULL, 'fieldset'
            , 0, 6, NULL, 0, true
            , $pi_name
            ,6);

        $c->add(
            'autotag_permissions_databox'
            ,$_DATABOX_DEFAULT['autotag_permissions_databox']
            ,'@select', 0, 6, 13, 10, TRUE
            , $pi_name
            ,6);
        
        
        //(9)XML
        $c->add('tab_xml', NULL, 'tab', 0, 9, NULL, 0, true, $pi_name,9);
        $c->add(
            'fs_xml'
            , NULL, 'fieldset'
            , 0, 9, NULL, 0, true
            , $pi_name
            ,9);

        $c->add(
            'path_xml'
            ,$_DATABOX_DEFAULT['path_xml']
            ,'text', 0, 9, NULL, 10, TRUE
            , $pi_name
            ,9);
        
        $c->add(
            'path_xml_out'
            ,$_DATABOX_DEFAULT['path_xml_out']
            ,'text', 0, 9, NULL, 20, TRUE
            , $pi_name
            ,9);
        $c->add(
            'xml_default_fieldset_id'
            ,$_DATABOX_DEFAULT['xml_default_fieldset_id']
            ,'select', 0, 9, 28, 30, true
            , $pi_name
            ,9);
        
        //(10)CSV
        $c->add('tab_csv', NULL, 'tab', 0, 10, NULL, 0, true, $pi_name,10);
        $c->add(
            'fs_csv'
            , NULL, 'fieldset'
            , 0, 10, NULL, 0, true
            , $pi_name
            ,10);

        $c->add(
            'path_csv'
            ,$_DATABOX_DEFAULT['path_csv']
            ,'text', 0, 10, NULL, 10, TRUE
            , $pi_name
            ,10);
        
        $c->add(
            'path_csv_out'
            ,$_DATABOX_DEFAULT['path_csv_out']
            ,'text', 0, 10, NULL, 20, TRUE
            , $pi_name
            ,10);
        $c->add(
            'csv_default_fieldset_id'
            ,$_DATABOX_DEFAULT['csv_default_fieldset_id']
            ,'select', 0, 10, 28, 30, true
            , $pi_name
            ,10);
        $c->add(
            'csv_default_owner_id'
            ,$_DATABOX_DEFAULT['csv_default_owner_id']
            ,'text', 0, 10, NULL, 40, TRUE
            , $pi_name
            ,10);

        $c->add(
            'csv_cron_schedule_interval'
            ,$_DATABOX_DEFAULT['csv_cron_schedule_interval']
            ,'text', 0, 10, NULL, 50, TRUE
            , $pi_name
            ,10);
        $c->add(
            'csv_cron_schedule_unlink'
            ,$_DATABOX_DEFAULT['csv_cron_schedule_unlink']
            ,'select', 0, 10, 0, 60, true
            , $pi_name
            ,10);
        $c->add(
            'csv_cron_schedule_nextmaps'
            ,$_DATABOX_DEFAULT['csv_cron_schedule_nextmaps']
            ,'select', 0, 10, 0, 70, true
            , $pi_name
            ,10);
        $c->add(
            'csv_cron_schedule_sel_id'
            ,$_DATABOX_DEFAULT['csv_cron_schedule_sel_id']
            ,'select', 0, 10, 29, 80, true
            , $pi_name
            ,10);
        

        //(11)MAPS
        $c->add('tab_maps', NULL, 'tab', 0, 11, NULL, 0, true, $pi_name,11);
        $c->add(
            'fs_maps'
            , NULL, 'fieldset'
            , 0, 11, NULL, 0, true
            , $pi_name
            ,11);

        $c->add(
            'maps_mid'
            ,$_DATABOX_DEFAULT['maps_mid']
            ,'text', 0, 11, NULL, 10, TRUE
            , $pi_name
            ,11);
        $c->add(
            'maps_pref'
            ,$_DATABOX_DEFAULT['maps_pref']
            ,'text', 0, 11, NULL, 20, TRUE
            , $pi_name
            ,11);
        $c->add(
            'maps_address1'
            ,$_DATABOX_DEFAULT['maps_address1']
            ,'text', 0, 11, NULL, 30, TRUE
            , $pi_name
            ,11);
        $c->add(
            'maps_lat'
            ,$_DATABOX_DEFAULT['maps_lat']
            ,'text', 0, 11, NULL, 40, TRUE
            , $pi_name
            ,11);
        $c->add(
            'maps_lng'
            ,$_DATABOX_DEFAULT['maps_lng']
            ,'text', 0, 11, NULL, 50, TRUE
            , $pi_name
            ,11);
        $c->add(
            'maps_address2'
            ,$_DATABOX_DEFAULT['maps_address2']
            ,'text', 0, 11, NULL, 60, TRUE
            , $pi_name
            ,11);
        $c->add(
            'maps_address3'
            ,$_DATABOX_DEFAULT['maps_address3']
            ,'text', 0, 11, NULL, 70, TRUE
            , $pi_name
            ,11);
        $c->add(
            'maps_cron_schedule_interval'
            ,$_DATABOX_DEFAULT['maps_cron_schedule_interval']
            ,'text', 0, 11, NULL, 80, TRUE
            , $pi_name
            ,11);
    }

    return true;
}

?>
