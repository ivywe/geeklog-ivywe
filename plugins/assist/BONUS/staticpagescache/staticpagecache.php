<?php
/* Reminder: always indent with 4 spaces (no tabs). */

	set_time_limit(120);

	$page=$argv[1];

	//      ↓ディレクトリが変わる場合は修正してください
	include('/path/to/geeklog/public_html/lib-common.php');
	$path=$_ASSIST_CONF["path_cache"].'staticpages/'.$page.".html";

	//静的ページキャッシュファイル作成
	require_once ($_CONF['path'] . 'plugins/assist/fnc_staticpagecache.inc');

	fnc_staticpagecache($page);

?>
