<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// |                                                         |
// +---------------------------------------------------------------------------+
// $Id: plugins/ASSIST/config.php
//20101201 tsuchitani AT ivywe DOT co DOT  jp http://www.ivywe.co.jp/

$_ASSIST_CONF = array();

$assist_config = config::get_instance();
$_ASSIST_CONF = $assist_config->get_config('assist');

include_once 'version.php';


// データベーステーブル名 - 変更禁止
//proversion 用
$_TABLES['ASSIST_def_xml']    = $_DB_table_prefix . 'assist_def_xml';

?>
