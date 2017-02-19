<?php
//Language change for programs executed by a command
//コマンドで実行されたプログラム用の言語変更
// $Id: custom_switchlang.php
// system/custom/custom_switchlang.php
// call from system/lib_custom.php
// require_once( 'custom/custom_switchlang.php' );
// 2013/02/19 tsuchitani AT ivywe DOT co DOT jp http://www.ivywe.co.jp/

if (strpos(strtolower($_SERVER['PHP_SELF']), 'custom_switchlang.php') !== false) {
    die('This file can not be used on its own!');
}

if  ($argv[1]<>""){
	$dummy=CUSTOM_switchlang();
}
function CUSTOM_switchlang()
{
	global $_CONF;
	global $_USER;
	global $argv;
	
	$page=$argv[1];
	
	if ($_CONF['allow_user_language'] == 1) {
		$oldlang = COM_getLanguageId();
		$lang_len = strlen($oldlang);
		$newlang=substr($page,-($lang_len));
		if  (!array_key_exists($newlang, $_CONF['language_files'])){
			$newlang=$oldlang;
		}
		if  ($newlang<>$oldlang){
			$_CONF['language']=$_CONF['language_files'][$newlang];
			$_USER['language'] = $_CONF['language'];
		}
	}
}
