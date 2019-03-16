<?php
// +---------------------------------------------------------------------------+
// | assist_function 共通(権限チェック)
// +---------------------------------------------------------------------------+
// $Id: assist_function.php
// public_html/admin/plugins/assist/assist_function.php
// 2010/12/17 tsuchitani AT ivywe DOT co DOT jp
// last update 20181102 hiroron AT hiroron DOT com

define ('THIS_PLUGIN', 'assist');

require_once('../../../lib-common.php');
//uikit3へ対応するためnavbar機能をやめる
//require_once ($_CONF['path'] . 'plugins/assist/lib/ppNavbar.php');

$edt_flg=FALSE;

// 権限チェック
if (SEC_hasRights('assist.admin')) {
}else{
    $display = COM_createHTMLDocument($MESSAGE[35], array('what' => $what, 'pagetitle' => $page_title, 'breadcrumbs' => $breadcrumbs, 'headercode' => $headercode, 'rightblock' => $rightblock));

    // Log attempt to error.log
    COM_accessLog("User {$_USER['username']} tried to illegally access the assist administration screen.");

    echo $display;

    exit;
}

?>