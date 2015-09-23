<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | content.php 個別表示 ヘッダフッタなし
// +---------------------------------------------------------------------------+
// $Id: data.php
// public_html/userbox/content.php
// 2013/11/05 tsuchitani AT ivywe DOT co DOT jp

define ('THIS_SCRIPT', 'userbox/content.php');
//define ('THIS_SCRIPT', 'userbox/test.php');
//debug 時 true
$_USERBOX_VERBOSE = false;

require_once('../lib-common.php');
if (!in_array('userbox', $_PLUGINS)) {
    COM_handle404();
    exit;
}

// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
//############################
$pi_name    = 'userbox';
//############################
$display = '';
$page_title=$LANG_USERBOX_ADMIN['piname'];

// 引数
//public_html/content.php?code=xxxx&template=yyyy
$url_rewrite = false;
$q = false;
$url = $_SERVER["REQUEST_URI"];
if ($_CONF['url_rewrite']) {
    $q = strpos($url, '?');
    if ($q === false) {
        $url_rewrite = true;
    }elseif (substr($url, $q - 4, 4) != '.php') {
        $url_rewrite = true;
    }
}
//
if ($url_rewrite){
	COM_setArgNames(array('code','template'));
    $code=COM_applyFilter(COM_getArgument('code'));
    $template=COM_applyFilter(COM_getArgument('template'));
}else{
    $code = COM_applyFilter($_GET['code']);
    $template = COM_applyFilter($_GET['template']);
}

//ログイン要否チェック
if (COM_isAnonUser()){
    if  ($_CONF['loginrequired']
            OR ($_USERBOX_CONF['loginrequired'] == 3)
            OR ($_USERBOX_CONF['loginrequired'] == 2)
            OR ($_USERBOX_CONF['loginrequired'] == 1 ) 
			){
		echo $LANG_USERBOX['loginrequired'];
		exit;
    }

}

$id=0;
$retval= userbox_profile($id,$template,"yes","",$code);
echo $retval['display'];

?>
