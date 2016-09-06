<?php

// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | assist Plugin  assistプラグイン                                           |
// +---------------------------------------------------------------------------+
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
// |          Trinity Bays       - trinity93 AT gmail DOT com                  |
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
// Last Update 20121203

if (strpos($_SERVER['PHP_SELF'], 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

global $_ASSIST_DEFAULT;

$_ASSIST_DEFAULT = array();



//
// +---------------------------------------------------------------------------+
// + 初期値
// 自動タグ
global $_CONF;

$_ASSIST_DEFAULT['title_trim_length'] = 20;
$_ASSIST_DEFAULT['intervalday']=90;
$_ASSIST_DEFAULT['limitcnt']=10;
$_ASSIST_DEFAULT['newmarkday']=3;
$_ASSIST_DEFAULT['topics']="";
$_ASSIST_DEFAULT['new_img']='<span class="assist_new">new!</span>';
$_ASSIST_DEFAULT['rss_img']='<span class="assist_new">RSS</span>';

$_ASSIST_DEFAULT['newsletter_tid']='newsletter';

//@@@@@
//テンプレート 一般画面
$_ASSIST_DEFAULT['templates']='standard';
//テンプレート 管理画面
$_ASSIST_DEFAULT['templates_admin']='standard';

//テーマテンプレートパス';
$_ASSIST_DEFAULT['themespath'] ='assist/templates/';

//Cronスケジュール間隔';//20111219 廃棄
//$_ASSIST_DEFAULT['cron_schedule_interval'] ='3600';


//onoff 準備中
$_ASSIST_DEFAULT['onoff_emailfromadmin'] =1;


//データ保存後の画面遷移　一般画面
$_ASSIST_DEFAULT['aftersave'] = 'item';
//データ保存後の画面遷移　管理画面
$_ASSIST_DEFAULT['aftersave_admin'] = 'item';

$_ASSIST_DEFAULT['xmlns'] = 
' xmlns:fb="http://www.facebook.com/2008/fbml"'.LB
.' xmlns:og="http://ogp.me/ns#"'.LB
.' xmlns:mixi="http://mixi-platform.com/ns#"'.LB;

//デフォルト画像URL
$_ASSIST_DEFAULT['default_img_url'] = "";

//キャッシュファイルパス
$_ASSIST_DEFAULT['path_cache'] = $_CONF['path']."data/cache/";

//permission ignoreを無効にする　default いいえ
$_ASSIST_DEFAULT['disable_permission_ignore'] = 0;

//() autotag permissions
$_ASSIST_DEFAULT['autotag_permissions_newstories'] = array (2, 2, 2, 2);
$_ASSIST_DEFAULT['autotag_permissions_newstories2'] = array (2, 2, 2, 2);
$_ASSIST_DEFAULT['autotag_permissions_conf'] = array (2, 2, 2, 2);
$_ASSIST_DEFAULT['autotag_permissions_assist'] = array (2, 2, 2, 2);

//（9）XML
$_ASSIST_DEFAULT['path_xml'] = $_CONF['path_html'].'assist_data';
$_ASSIST_DEFAULT['path_xml_out']=$_CONF['path'].'data/assist';

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
function plugin_initconfig_assist()
{
    global $_ASSIST_CONF;
    global $_ASSIST_DEFAULT;

    $pi_name="assist";

    if (is_array($_ASSIST_CONF) && (count($_ASSIST_CONF) > 1)) {
        $_ASSIST_DEFAULT = array_merge($_ASSIST_DEFAULT, $_ASSIST_CONF);
    }

    $c = config::get_instance();
    if (!$c->group_exists('assist')) {

    /* add(
        $param_name
        , $default_value
        , $type
        , $subgroup, $fieldset,$selection_array=null
        , $sort=0
        , $set=true
        , $group='japanize')
    */

//        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'assist');
//        $c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true, 'assist');

        //メイン
        $c->add(
            'sg_main'
            , NULL , 'subgroup'
            , 0, 0
            , NULL, 0, true
            , $pi_name);


		//(0)
        $c->add('tab_main', NULL, 'tab', 0, 0, NULL, 0, true, $pi_name, 0);
        $c->add(
            'fs_main'
            , NULL, 'fieldset'
            , 0, 0, NULL, 0, true
			, $pi_name
			,0);

        $c->add(
            'title_trim_length'
            ,$_ASSIST_DEFAULT['title_trim_length']
            ,'text', 0, 0, NULL, 10, TRUE
			, $pi_name
			,0);
        $c->add(
            'intervalday'
            ,$_ASSIST_DEFAULT['intervalday']
            ,'text', 0, 0, NULL, 20, TRUE
			, $pi_name
			,0);
        $c->add(
            'limitcnt'
            ,$_ASSIST_DEFAULT['limitcnt']
            ,'text', 0, 0, NULL, 30, TRUE
			, $pi_name
			,0);
        $c->add(
            'newmarkday'
            ,$_ASSIST_DEFAULT['newmarkday']
            ,'text', 0, 0, NULL, 40, TRUE
			, $pi_name
			,0);
        $c->add(
            'topics'
            ,$_ASSIST_DEFAULT['topics']
            ,'text', 0, 0, NULL, 50, TRUE
			, $pi_name
			,0);
        $c->add(
            'new_img'
            ,$_ASSIST_DEFAULT['new_img']
            ,'textarea', 0, 0, NULL, 60, TRUE
			, $pi_name
			,0);
        $c->add(
            'rss_img'
            ,$_ASSIST_DEFAULT['rss_img']
            ,'textarea', 0, 0, NULL, 70, TRUE
			, $pi_name
			,0);

        $c->add(
            'newsletter_tid'
            ,$_ASSIST_DEFAULT['newsletter_tid']
            ,'text', 0, 0, NULL, 80, TRUE
			, $pi_name
			,0);

        //@@@@@
        $c->add(
            'templates'
            , $_ASSIST_DEFAULT['templates']
            , 'select',  0, 0, 20, 90, true
			, $pi_name
			,0);
        $c->add(
            'templates_admin'
            , $_ASSIST_DEFAULT['templates_admin']
            , 'select',  0, 0, 20, 100, true
			, $pi_name
			,0);
        $c->add(
            'themespath'
            ,$_ASSIST_DEFAULT['themespath']
            ,'text', 0, 0, NULL, 110, TRUE
			, $pi_name
			,0);

        $c->add(
            'onoff_emailfromadmin'
            ,$_ASSIST_DEFAULT['onoff_emailfromadmin']
            ,'select', 0, 0, 0, 130, true
			, $pi_name
			,0);


        $c->add(
            'aftersave'
            ,$_ASSIST_DEFAULT['aftersave']
            ,'select', 0, 0, 9, 140, true
			, $pi_name
			,0);

        $c->add(
            'aftersave_admin'
            ,$_ASSIST_DEFAULT['aftersave_admin']
            ,'select', 0, 0, 9, 150, true
			, $pi_name
			,0);

        $c->add(
            'xmlns'
            ,$_ASSIST_DEFAULT['xmlns']
            ,'textarea', 0, 0, NULL, 160, true
			, $pi_name
			,0);
		
		$c->add(
            'default_img_url'
            ,$_ASSIST_DEFAULT['default_img_url']
            ,'text', 0, 0, NULL, 260, TRUE
			, $pi_name
			,0);
		
		$c->add(
            'path_cache'
            ,$_ASSIST_DEFAULT['path_cache']
            ,'text', 0, 0, NULL, 270, TRUE
			, $pi_name
			,0);

        
        $c->add(
            'disable_permission_ignore'
            ,$_ASSIST_DEFAULT['disable_permission_ignore']
            ,'select', 0, 0, 0, 280, true
            , $pi_name
            ,0);

		//(1)autotag_permissions
        $c->add('tab_autotag_permissions', NULL, 'tab', 0, 1, NULL, 0, true, $pi_name, 1);
        $c->add(
            'fs_autotag_permissions'
            , NULL, 'fieldset'
            , 0, 1, NULL, 0, true
			, $pi_name
			,1);

        $c->add(
            'autotag_permissions_newstories'
            ,$_ASSIST_DEFAULT['autotag_permissions_newstories']
            ,'@select', 0, 1, 13, 10, TRUE
			, $pi_name
			,1);
        $c->add(
            'autotag_permissions_newstories2'
            ,$_ASSIST_DEFAULT['autotag_permissions_newstories2']
            ,'@select', 0, 1, 13, 20, TRUE
			, $pi_name
			,1);
        $c->add(
            'autotag_permissions_conf'
            ,$_ASSIST_DEFAULT['autotag_permissions_conf']
            ,'@select', 0, 1, 13, 30, TRUE
			,$pi_name
			,1);
        $c->add(
            'autotag_permissions_assist'
            ,$_ASSIST_DEFAULT['autotag_permissions_assist']
            ,'@select', 0, 1, 13, 40, TRUE
			, $pi_name
			,1);
		
		
		
		//(9)XML
        $c->add('tab_pro', NULL, 'tab', 0, 9, NULL, 0, true, $pi_name, 9);
        $c->add(
            'fs_pro'
            , NULL, 'fieldset'
            , 0, 9, NULL, 0, true
			, $pi_name
			,9);

        $c->add(
            'path_xml'
            ,$_ASSIST_DEFAULT['path_xml']
            ,'text', 0, 9, NULL, 10, TRUE
			, $pi_name
			,9);
        $c->add(
            'path_xml_out'
            ,$_ASSIST_DEFAULT['path_xml_out']
            ,'text', 0, 9, NULL, 20, TRUE
			, $pi_name
			,9);

   }

    return true;
}

?>
