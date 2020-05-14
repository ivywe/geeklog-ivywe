<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | UserBox Plugin 0.0.0                                                      |
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
// Updte 2020/05/14 hiroron

/**
* Plugin autoinstall function
*
* @param    string  $pi_name    Plugin name
* @return   array               Plugin information
*
*/
function plugin_autoinstall_userbox($pi_name)
{
    $pi_name         = 'userbox';
	$pi_display_name = 'UserBox';
	
	//初期登録するグループ
    $pi_admin        = $pi_display_name . ' Admin';
	$pi_editors      = $pi_display_name . ' Editor';
	$pi_managers     = $pi_display_name . ' Manager';
	$pi_user     	 = $pi_display_name . ' User';

    //設定ファイル
    $info = array(
        'pi_name'         => $pi_name,
        'pi_display_name' => $pi_display_name,
        'pi_version'      => '0.0.20200514a',
        'pi_gl_version'   => '2.1.2',
        'pi_homepage'     => 'http://www.ivywe.co.jp/',
    );

    //----------------------------------------------------------------
    // the plugin's groups - assumes first group to be the Admin group
    //----------------------------------------------------------------
    $groups = array(
        $pi_admin   => 'Has full access to ' . $pi_display_name . ' features'
       ,$pi_editors => 'Can edit profile to ' . $pi_display_name. ' plugin'
       ,$pi_managers=> 'Can edit join group to ' . $pi_display_name. ' plugin'
       ,$pi_user    => 'Can register to UserBox' . $pi_display_name. ' plugin'
   );
    //----------------------------------------------------------------
    // the plugin's feature -
    //----------------------------------------------------------------
    // *.admin 管理画面のすべての処理を実行可能
    //               一覧表示では下書も表示

    $features = array(
        $pi_name . '.admin'    => 'Full access to ' . $pi_display_name . ' plugin'
       ,$pi_name . '.edit'     => 'Can edit profile to ' . $pi_display_name . ' plugin'
       ,$pi_name . '.joingroup'=> 'Can edit join group to ' . $pi_display_name . ' plugin'
       ,$pi_name . '.user'     => 'Can register to UserBox' . $pi_display_name . ' plugin'
    );
    //----------------------------------------------------------------
    // the plugin's mappings -
    //----------------------------------------------------------------
    $mappings = array(
        $pi_name . '.admin'    => array($pi_admin)
       ,$pi_name . '.edit'     => array($pi_admin,$pi_editors,$pi_managers)
       ,$pi_name . '.joingroup'=> array($pi_admin,$pi_managers)
       ,$pi_name . '.user'     => array($pi_admin,$pi_managers,$pi_editors,$pi_user)
    );


    //----------------------------------------------------------------
    // the plugin's tables -
    //----------------------------------------------------------------
    $tables = array(
        'userbox_base'
       ,'userbox_category'
       ,'userbox_addition'
       ,'userbox_def_category'
       ,'userbox_def_field'
       ,'userbox_def_group'
       ,'userbox_def_xml'
       ,'userbox_mst'
       ,'userbox_stats'
       ,'userbox_def_fieldset'
       ,'userbox_def_fieldset_assignments'
       ,'userbox_def_fieldset_group'
    );

    $inst_parms = array(
        'info'      => $info,
        'groups'    => $groups,
        'features'  => $features,
        'mappings'  => $mappings,
		'tables'    => $tables
	);

    return $inst_parms;
}

// ----------------------------------------------------------------
// config情報ロード：Return OK:true NG:false
// ----------------------------------------------------------------
function plugin_load_configuration_userbox($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_userbox();
}

// ----------------------------------------------------------------
// コアパッケージのチェック：Return OK:true NG:false
// ----------------------------------------------------------------
function plugin_compatible_with_this_version_userbox($pi_name)
{

    if (!function_exists('COM_truncate') || !function_exists('MBYTE_strpos')) {
        return false;
    }

    if (!function_exists('SEC_createToken')) {
        return false;
    }

    // 2020/05/14 Add - hiroron
    if (!function_exists('COM_showMessageText')) {
        return false;
    }

    if (!function_exists('SEC_getTokenExpiryNotice')) {
        return false;
    }

    if (!function_exists('SEC_loginRequiredForm')) {
        return false;
    }

    return true;
}


// ----------------------------------------------------------------
// インストール時準備Return OK:true NG:false
// ----------------------------------------------------------------
function plugin_postinstall_userbox($pi_name)
{
    global $_CONF, $_TABLES, $_USERBOX_CONF;

    //  ログファイル作成
    //  サーバによっては、パーミッション変更できないのでその場合は
    //  手動で変更してください
    $logfile = $_CONF['path_log'] . 'userbox_xmlimport.log';
    $file = @fopen( $logfile, 'w' );
    @fclose($file);
    @chmod($file, 0666);

    //UserBox データの作成
    require_once ($_CONF['path'] . 'plugins/userbox/functions.inc');
	//-----テーブル	
	$sql = "SELECT uid ";
	$sql .= " FROM {$_TABLES['users']} ";
	$sql .= " WHERE uid >1";
	
	$result = DB_query ($sql);
    $numrows = DB_numRows ($result);
    if ($numrows > 0) {
        for ($i = 0; $i < $numrows; $i++) {
			$A = DB_fetchArray ($result);
			$uid=COM_applyFilter($A['uid']);
			$dummy=plugin_user_create_userbox ($uid);
		}
	}

    return true;
}

?>
