<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | index.php TOP 
// +---------------------------------------------------------------------------+
// $Id: index.php
// public_html/databox/index.php
// 2014/12/02 tsuchitani AT ivywe DOT co DOT jp

require_once ('../lib-common.php');
if (!in_array('databox', $_PLUGINS)) {
    COM_handle404();
    exit;
}
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
    if  ($_SERVER['PATH_INFO']==""){
        $code="";
        $template="";
    }else{
        COM_setArgNames(array('code','template','dummy1','dummy2'));
        $code=COM_applyFilter(COM_getArgument('code'));
        $template=COM_applyFilter(COM_getArgument('template'));
    }
}else{
    $code = COM_applyFilter($_GET['code']);
    $template =COM_applyFilter($_GET['template']);
}
if  ($_DATABOX_CONF['detail']==""){
    $prg="data.php";
}else{
    $prg=$_DATABOX_CONF['detail'];
}

if  ($code==""){
    echo COM_refresh($_CONF['site_url'].$_DATABOX_CONF['top']);
}else{
    if  ($prg=="data.php"){
        $url=$_CONF['site_url']."/databox/data.php/".$code."/code/";
        if  ($template<>""){
            $url.=$template."/";
        }
    }else{
        $url=str_replace("index.php", $prg, $url);
    }
    echo COM_refresh($url);
}

?>
