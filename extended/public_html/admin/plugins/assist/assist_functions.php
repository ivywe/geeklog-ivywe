<?php
// +---------------------------------------------------------------------------+
// | assist_function 共通(権限チェック)
// +---------------------------------------------------------------------------+
// $Id: assist_function.php
// public_html/admin/plugins/assist/assist_function.php
// 2010/12/17 tsuchitani AT ivywe DOT co DOT jp
// last update 20221206 hiroron AT hiroron DOT com

define ('THIS_PLUGIN', 'assist');

require_once('../../../lib-common.php');
require_once ('../../auth.inc.php');

$edt_flg=FALSE;

// 権限チェック
if (SEC_hasRights('assist.admin')) {
}else{
    $display = COM_createHTMLDocument($MESSAGE[35], array('pagetitle' => $MESSAGE[30]));

    // Log attempt to error.log
    COM_accessLog("User {$_USER['username']} tried to illegally access the assist administration screen.");

    COM_output($display);
    exit;
}

?>
