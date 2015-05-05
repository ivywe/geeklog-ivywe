<?php
/* Reminder: always indent with 4 spaces (no tabs). */
//admin/plugins/databox/job/makecache.php
//権限チェックはしていません
//当プログラムを置くディレクトリは、BASIC認証を付加することを推奨します

//デバック用 true にすると、ログを出力します
$_CACHE_VERBOSE = false;

//↓ディレクトリ位置が変わる場合は修正してください
include('../../../../lib-common.php');

//静的ページキャッシュファイル作成
require_once ($_CONF['path'] . 'plugins/databox/fnc_databoxcache.inc');

//強制的にログアウトする
if (!empty ($_USER['uid']) AND $_USER['uid'] > 1) {
    SESS_endUserSession ($_USER['uid']);
    PLG_logoutUser ($_USER['uid']);
}
SEC_setCookie($_CONF['cookie_session'], '', time() - 10000);
SEC_setCookie($_CONF['cookie_password'], '', time() - 10000);
SEC_setCookie($_CONF['cookie_name'], '', time() - 10000);

//★fnc_putcache("data"	,"データのcode" ,"テンプレートディレクトリ");
//   データ（ヘッダフッタなし）
//★fnc_putcache("category"	,"カテゴリのcode" ,"テンプレートディレクトリ");
//   カテゴリ（ヘッダフッタなし）
//★fnc_putcache("datapage"	,"データのcode" ,"テンプレートディレクトリ");
//	 データページ（ヘッダフッタは、設定による）
//★fnc_putcache("categorypage"	,"カテゴリのcode" ,"テンプレートディレクトリ");
//   カテゴリページ（ヘッダフッタは、設定による）
fnc_databoxcache("category","xxxx1");


//ホームに遷移
echo COM_refresh($_CONF['site_url'] . '/index.php');

?>
