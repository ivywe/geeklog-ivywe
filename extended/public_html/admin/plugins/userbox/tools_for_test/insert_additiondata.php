<?php

/* Reminder: always indent with 4 spaces (no tabs). */

define ('THIS_SCRIPT', 'insert_additiondata.php');


require_once('../../../../lib-common.php');
include_once('additiondata_functions.php');




// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
//############################
$pi_name    = 'userbox';
//############################

$action ="";
if (isset ($_REQUEST['action'])) {
    $action = COM_applyFilter($_REQUEST['action'],false);
}

$display="";
if  ($action ==""){
    $display=fncDisply($pi_name);
}elseif ($action =="submit"){
    $display=fncexec($pi_name);
}else{
    $display ="cancel!";
}
echo $display;


?>
