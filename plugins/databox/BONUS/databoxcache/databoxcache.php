<?php
//* Reminder: always indent with 4 spaces (no tabs). */
	set_time_limit(120);

	$function=$argv[1];
	$code=$argv[2];
	if  (count($argv)<=3){
		$template="";
	}else{
		$template=$argv[3];
	}

	//★↓ディレクトリが変わる場合は修正してください
	include('/path/to/geeklog/public_html/lib-common.php');

	//DataBoxキャッシュファイル作成
	require_once ($_CONF['path'] . 'plugins/databox/fnc_databoxcache.inc');

	fnc_databoxcache($function,$code,$template);

?>
