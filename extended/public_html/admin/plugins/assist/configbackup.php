<?php
//http://labo3.itsup.net/gl180/admin/plugins/assist/configbackup.php

define ('THIS_SCRIPT', 'configbackup.php');
require_once('../../../lib-common.php');
require_once ($_CONF['path'] . 'plugins/assist/lib/lib_configuration.php');

// 権限チェック
if (SEC_hasRights('assist.admin')) {
}else{
    $display="";
    $display .= COM_siteHeader('menu', $MESSAGE[30]);
    $display .= COM_startBlock ($MESSAGE[30], '',
                                COM_getBlockTemplate ('_msg_block', 'header'));
    $display .= $MESSAGE[35];
    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
    $display .= COM_siteFooter();

    // Log attempt to error.log
    COM_accessLog("User {$_USER['username']} tried to illegally access admin/plugins/assist/configback.php ");

    echo $display;

    exit;
}

//MAIN
//**************
$pi_name="assist";
//$pi_name="databox";
//$pi_name="";

//**************
$action ="";
if (isset ($_REQUEST['action'])) {
    $action = COM_applyFilter($_REQUEST['action'],false);
}

echo "piname=".$pi_name."<br>";
$display="";
if  ($action ==""){
    $display=LIB_Disply($pi_name);
}elseif ($action =="submit"){
    $display=LIB_Backupconfig($pi_name);
}else{
    $display ="cancel!";
}
echo $display;

?>