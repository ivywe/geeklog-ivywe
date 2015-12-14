<?php
// +---------------------------------------------------------------------------+
// | Assist プラグイン
// | cache 作成プログラムから require
// +---------------------------------------------------------------------------+
// $Id: plugins/assist/fnc_staticpagecache.inc
//20130730 tsuchitani AT ivywe DOT co DOT jp http://www.ivywe.co.jp/

if (strpos ($_SERVER['PHP_SELF'], 'fnc_staticpagecache.inc') !== false) {
    die ('This file can not be used on its own.');
}

function fnc_staticpagecache(
	$page
	)
// +---------------------------------------------------------------------------+
// | 機能  staticpage のキャッシュファイル作成
// | 書式  fnc_staticpagecache("staticpageID")
// +---------------------------------------------------------------------------+
// | 引数 $page:staticpage ID
// +---------------------------------------------------------------------------+
{
	global $_CACHE_VERBOSE;
	global $_ASSIST_CONF;
	
	$path=$_ASSIST_CONF["path_cache"].'staticpages/';
	$filename=$path.$page.".html";
	$query = '';
    $comment_order = '';
    $comment_mode  = '';
    $comment_page = 1;
	$display_mode = '';
	$msg = 0;
	$retval = SP_returnStaticpage($page, $display_mode, $comment_order, $comment_mode, $comment_page, $msg, $query);
	
	@unlink($filename);
	file_put_contents($filename, $retval);
    @chmod($filename, 0666);
	if  ($_CACHE_VERBOSE){
		$logmsg="[assist_staticpagecache] ".$filename;
		echo $logmsg."<br>";
		COM_errorLog($logmsg);
	}

}

?>
