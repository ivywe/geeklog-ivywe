<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | DataBox Plugin 0.0.0                                                      |
// +---------------------------------------------------------------------------+
// | autoinstall.php                                                           |
// |                                                                           |
// | This file provides helper functions for the automatic plugin install.     |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors: Tsuchi           - tsuchi AT geeklog DOT jp                      |
// +---------------------------------------------------------------------------+
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
// 20110622

/**
* Autoinstall API functions for the DataBox plugin
*
* @package databox
*/

/**
* Plugin autoinstall function
*
* @param    string  $pi_name    Plugin name
* @return   array               Plugin information
*
*/
function plugin_autoinstall_databox($pi_name)
{
    $pi_name         = 'databox';
    $pi_display_name = 'DataBox';
    $pi_admin        = $pi_display_name . ' Admin';
    $pi_submitters   = $pi_display_name . ' Submitters';
    $pi_editors      = $pi_display_name . ' Editor';

    //設定ファイル
    global $_CONF;
    global $_TABLES;
    global $_DB_table_prefix;
    include_once ($_CONF['path'] . "plugins/{$pi_name}/config.php");
    include 'version.php';

    $info = array(
        'pi_name'         => $pi_name,
        'pi_display_name' => $pi_display_name,
        'pi_version'      => $_DATABOX_CONF['version'],
        'pi_gl_version'   => '2.1.0',
        'pi_homepage'     => 'http://www.ivywe.co.jp',
    );

    //----------------------------------------------------------------
    // the plugin's groups - assumes first group to be the Admin group
    //----------------------------------------------------------------
    $groups = array(
        $pi_admin => 'Has full access to ' . $pi_display_name . ' features'
        ,$pi_submitters => 'Can submit data to ' . $pi_display_name. ' plugin'
        ,$pi_editors => 'Can edit data to ' . $pi_display_name. ' plugin'
    );
    //----------------------------------------------------------------
    // the plugin's feature -
    //----------------------------------------------------------------
    // *.admin 管理画面のすべての処理を実行可能
    //               一覧表示では下書も表示
    // *.submitters データの新規登録が実行可能なユーザ

    $features = array(
        $pi_name . '.admin'    => 'Full access to ' . $pi_display_name
                                  . ' plugin'
        ,$pi_name . '.submit'    => 'Can submit data to ' . $pi_display_name
                                  . ' plugin'
        ,$pi_name . '.edit'    => 'Can edit data to ' . $pi_display_name
                                  . ' plugin'

    );


    //----------------------------------------------------------------
    // the plugin's mappings -
    //----------------------------------------------------------------
    $mappings = array(
        $pi_name . '.admin'    => array($pi_admin)
        ,$pi_name . '.submit'    => array($pi_admin,$pi_submitters)
        ,$pi_name . '.edit'    => array($pi_admin,$pi_submitters,$pi_editors)
    );


    //----------------------------------------------------------------
    // the plugin's tables -
    //----------------------------------------------------------------
    //@@@@@@@@@@
    $tables = array(
    );
	
	$requires = array(
        array(
               'db' => 'mysql',
               'version' => '5.0'
             )
    );

    $inst_parms = array(
        'info'      => $info,
        'groups'    => $groups,
        'features'  => $features,
        'mappings'  => $mappings,
		'tables'    => $tables,
		'requires'  => $requires
    );

    return $inst_parms;
}
// ----------------------------------------------------------------
// config情報ロード：Return OK:true NG:false
// ----------------------------------------------------------------

function plugin_load_configuration_databox($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_databox();
}
// ----------------------------------------------------------------
// コアパッケージのチェック：Return OK:true NG:false
// ----------------------------------------------------------------
function plugin_compatible_with_this_version_databox($pi_name)
{

    if (!function_exists('COM_truncate') || !function_exists('MBYTE_strpos')) {
        return false;
    }

    if (!function_exists('SEC_createToken')) {
        return false;
    }

    // xxxx があるかどうかチェック！
    //if( function_exists( 'xxxx' )){
    //    return false;
    //}

    return true;
}


// ----------------------------------------------------------------
// インストール時準備Return OK:true NG:false
// ----------------------------------------------------------------
function plugin_postinstall_databox(
    $pi_name
)
{

    global $_TABLES;
    global $_CONF;

//  サーバによっては、パーミッション変更できないのでその場合は
//  手動で変更してください
    $logfile = $_CONF['path_log'] . 'databox_xmlimport.log';
    $file = @fopen( $logfile, 'w' );
    @fclose($file);
    @chmod($file, 0666);

    //マスタのデータ
    $_SQL =array();
    //require_once ($_CONF['path']."plugins/databox/sql/sql_mst_prefect.php");

    for ($i = 1; $i <= count($_SQL); $i++) {
        $w=current($_SQL);
        DB_query(current($_SQL));
        next($_SQL);
    }
    if (DB_error()) {
        return false;
    }


    return true;
}

?>
