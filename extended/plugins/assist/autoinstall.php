<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | assist Plugin 1.1.0                                                       |
// +---------------------------------------------------------------------------+
// | autoinstall.php                                                           |
// |                                                                           |
// | This file provides helper functions for the automatic plugin install.     |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                              |
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
// update 20120206
if (stripos($_SERVER['PHP_SELF'], basename(__FILE__)) !== FALSE) {
	die('This file can not be used on its own!');
}

/**
* Plugin autoinstall function
*
* @param    string  $pi_name    Plugin name
* @return   array               Plugin information
*
*/
function plugin_autoinstall_assist(
	$pi_name
){
    $pi_name         = 'assist';
    $pi_display_name = 'Assist';
    $pi_admin        = $pi_display_name . ' Admin';

    //設定ファイル
    global $_CONF;
    global $_TABLES;
    global $_DB_table_prefix;

    require_once ($_CONF['path'] . "plugins/{$pi_name}/config.php");
    require 'version.php';

    $info = array(
        'pi_name'         => $pi_name,
        'pi_display_name' => $pi_display_name,
        'pi_version'      => $_ASSIST_CONF['version'],
        'pi_gl_version'   => '1.8.1',
        'pi_homepage'     => 'http://www.geeklog.jp/filemgmt/index.php?id=339',
    );

    //----------------------------------------------------------------
    // the plugin's groups - assumes first group to be the Admin group
    //----------------------------------------------------------------
    $groups = array(
        $pi_admin => 'Has full access to ' . $pi_display_name . ' features'
    );
    //----------------------------------------------------------------
    // the plugin's feature -
    //----------------------------------------------------------------
    $features = array(
        $pi_name . '.admin'    => 'Full access to ' . $pi_display_name
                                  . ' plugin'
    );

    //----------------------------------------------------------------
    // the plugin's mappings -
    //----------------------------------------------------------------
    $mappings = array(
        $pi_name . '.admin'     => array($pi_admin)
    );

    //----------------------------------------------------------------
    // the plugin's tables -
    //----------------------------------------------------------------
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

function plugin_load_configuration_assist($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_assist();
}
// ----------------------------------------------------------------
// コアパッケージのチェック：Return OK:true NG:false
// ----------------------------------------------------------------
function plugin_compatible_with_this_version_assist($pi_name)
{

    if (!function_exists('COM_truncate') || !function_exists('MBYTE_strpos')) {
        return false;
    }

    if (!function_exists('SEC_createToken')) {
        return false;
    }

    // xxxx があるかどうかチェック！
    //if( function_exists( 'custom_mail' )){
    //    return false;
    //}

    return true;
}


// ----------------------------------------------------------------
// インストール時準備Return OK:true NG:false
// ----------------------------------------------------------------
function plugin_postinstall_assist(
    $pi_name
)
{
//  サーバによっては、パーミッション変更できないのでその場合は
//  手動で変更してください
    global $_CONF;

    $logfile = $_CONF['path_log'] . 'assist_newsletter.log';
    $file = @fopen( $logfile, 'w' );
    @fclose($file);
    @chmod($file, 0666);

    $logfile = $_CONF['path_log'] . 'assist_xmlimport.log';
    $file = @fopen( $logfile, 'w' );
    @fclose($file);
    @chmod($file, 0666);

    return true;
}

?>
