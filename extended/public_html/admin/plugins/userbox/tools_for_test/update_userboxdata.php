<?php

define ('THIS_SCRIPT', 'update_userboxdata.php');
//require_once('../../../lib-common.php');
require_once('../../../../lib-common.php');

if  (in_array("userbox", $_PLUGINS)==0){
	echo "userbox not valid!";
    exit;
}

require_once ($_CONF['path'] . 'plugins/userbox/functions_dataadjustment.inc');

function fncDisply()
{
	$title="UserBox data update";
    $retval = "";
    $retval .= "<!DOCTYPE    HTML PUBLIC '-//W3C//DTD HTML   4.01 Transitional//EN'>".LB;
    $retval .= "<html>".LB;
    $retval .= "<head>".LB;
    $retval .= "    <title>{$title}</title>".LB;
    $retval .= "</head>".LB;
    $retval .= "<body   bgcolor='#ffffff'>".LB;
    $retval .= "<h1>{$title}</h1>".LB;
    $retval .= "<form action="."'".THIS_SCRIPT."'"."method='post'>".LB;
    $retval .= "    <input type='submit' name='action' value='submit'>".LB;
    $retval .= "    <input type='submit' name='action' value='cancel'>".LB;
    $retval .= "</form>".LB;
    $retval .= "</body>".LB;
    $retval .= "</html>".LB;

    return $retval ;

}

//MAIN
$action ="";
if (isset ($_REQUEST['action'])) {
    $action = COM_applyFilter($_REQUEST['action'],false);
}

$display="";
if  ($action ==""){
    $display=fncDisply();
}elseif ($action =="submit"){
    $display=userbox_dataadjustment();
}else{
    $display ="cancel!";
}
echo $display;

?>