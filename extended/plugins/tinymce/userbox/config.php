<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// |                                                                           |
// +---------------------------------------------------------------------------+
// $Id: plugins/userbox/config.php
//20120903 tsuchitani AT ivywe DOT co DOT  jp http://www.ivywe.co.jp/

$_USERBOX_CONF = array();

$userbox_config = config::get_instance();
$_USERBOX_CONF = $userbox_config->get_config('userbox');
include_once 'version.php';

// データベーステーブル名 - 原則変更禁止
$_TABLES['USERBOX_base']    		= $_DB_table_prefix . 'userbox_base';
$_TABLES['USERBOX_category']    	= $_DB_table_prefix . 'userbox_category';
$_TABLES['USERBOX_addition']    	= $_DB_table_prefix . 'userbox_addition';
//
$_TABLES['USERBOX_def_category']    = $_DB_table_prefix . 'userbox_def_category';
$_TABLES['USERBOX_def_field']    	= $_DB_table_prefix . 'userbox_def_field';
$_TABLES['USERBOX_def_group']    	= $_DB_table_prefix . 'userbox_def_group';
//
$_TABLES['USERBOX_def_xml']    		= $_DB_table_prefix . 'userbox_def_xml';

//
$_TABLES['USERBOX_mst']    			= $_DB_table_prefix . 'userbox_mst';
//
$_TABLES['USERBOX_stats']    		= $_DB_table_prefix . 'userbox_stats';

//20120903add----->
$_TABLES['USERBOX_def_fieldset']    = $_DB_table_prefix . 'userbox_def_fieldset';
$_TABLES['USERBOX_def_fieldset_assignments']    = $_DB_table_prefix . 'userbox_def_fieldset_assignments';
$_TABLES['USERBOX_def_fieldset_group']    = $_DB_table_prefix . 'userbox_def_fieldset_group';
?>
