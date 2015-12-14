<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | maps
// +---------------------------------------------------------------------------+
// $Id: maps.inc

define ('THIS_SCRIPT', 'databox/maps.php');
//define ('THIS_SCRIPT', 'databox/test.php');

require_once( $_CONF['path_system'] . 'lib-admin.php' );
//
require_once ($_CONF['path'] . 'plugins/databox/maps/maps_markersupdate.inc');

//log 出力モード設定　0:作成しない,1:ファイルに出力
$logmode =1;
$logfile = $_CONF['path_log'] . 'databox_mapsupdate.log';

// +---------------------------------------------------------------------------+
// | 機能  menu表示                                                            |
// | 書式 fncMenu()                                                            |
// +---------------------------------------------------------------------------+
// | 戻値 menu                                                                 |
// +---------------------------------------------------------------------------+
function fncMenu(

)
{

    global $_CONF;
    global $_TABLES;
    global $LANG_ADMIN;
    global $LANG09;
    global $_PLUGINS;

    global $LANG_MAPS_1;

    global $LANG_DATABOX_ADMIN;
    global $LANG_DATABOX;

    $retval = '';

    //MENU1:管理画面
    $url11=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=mapsmarkersupdate';
    $url12=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=mapsmarkersclear';
    $url13=$_CONF['site_admin_url'] . '/plugins/maps/markers.php';
    $url14=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=mapsscheduled';

    //
    $menu_arr[]=array('url' => $url11,'text' => $LANG_DATABOX_ADMIN['mapsmarkersupdate']);
    $menu_arr[]=array('url' => $url12,'text' => $LANG_DATABOX_ADMIN['mapsmarkersclear']);
    $menu_arr[]=array('url' => $url13,'text' => $LANG_DATABOX_ADMIN['mapsmarkers']);
    $menu_arr[]=array('url' => $url14,'text' => $LANG_DATABOX_ADMIN['schedule']);
    //
    $menu_arr[]=array('url' => $_CONF['site_admin_url'],'text' => $LANG_ADMIN['admin_home']);

    $retval .= ADMIN_createMenu(
        $menu_arr
        ,$LANG_DATABOX_ADMIN['instructions']
        ,plugin_geticon_databox()
    );
    

    return $retval;
}

function fncConfirmation(
    $mode
)
// +---------------------------------------------------------------------------+
// | 機能 確認画面
// | 書式 Con_firmation($mode)
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $LANG_ADMIN;

    global $LANG_DATABOX_ADMIN;

    global $_DATABOX_CONF;
    global $LANG_confignames;
    
    global $_TABLES;
    $tbl3=$_TABLES['DATABOX_def_field'];
    
    $maps_mid=$_DATABOX_CONF['maps_mid'];
    $maps_lat=$_DATABOX_CONF['maps_lat'];
    $maps_lng=$_DATABOX_CONF['maps_lng'];
    $maps_pref=$_DATABOX_CONF['maps_pref'];
    $maps_address1=$_DATABOX_CONF['maps_address1'];
    $maps_address2=$_DATABOX_CONF['maps_address2'];
    $maps_address3=$_DATABOX_CONF['maps_address3'];
 
    $tmpl = new Template ($_CONF['path'] . "plugins/".THIS_PLUGIN."/templates/admin/");
    $tmpl->set_file(array('confirm' => 'confirmation.thtml'));

    $tmpl->set_var('site_admin_url', $_CONF['site_admin_url']);
    $tmpl->set_var('gltoken_name', CSRF_TOKEN);
    $tmpl->set_var('gltoken', SEC_createToken());
    $tmpl->set_var ( 'xhtml', XHTML );

    //
    $actionname=$LANG_DATABOX_ADMIN[$mode];
    $actionprg="{$_CONF['site_admin_url']}/plugins/".THIS_SCRIPT."?mode={$mode}exec";

    $tmpl->set_var('actionname', $actionname);
    $tmpl->set_var('actionprg', $actionprg);

    $field_name_mid=DB_getItem( $tbl3   ,"name","templatesetvar='{$maps_mid}'");
    if  ($field_name_mid=="") {
        $field_name_mid=$LANG_DATABOX_ADMIN['err_invalid'] ;
    }
    $field_name_pref=DB_getItem( $tbl3   ,"name","templatesetvar='{$maps_pref}'");
    if  ($field_name_pref=="") {
        $field_name_pref=$LANG_DATABOX_ADMIN['err_invalid'] ;
    }
    $field_name_address1=DB_getItem( $tbl3   ,"name","templatesetvar='{$maps_address1}'");
    if  ($field_name_address1=="") {
        $field_name_address1=$LANG_DATABOX_ADMIN['err_invalid'] ;
    }
    $field_name_lat=DB_getItem( $tbl3   ,"name","templatesetvar='{$maps_lat}'");
    $field_name_lng=DB_getItem( $tbl3   ,"name","templatesetvar='{$maps_lng}'");
    $field_name_address2=DB_getItem( $tbl3   ,"name","templatesetvar='{$maps_address2}'");
    $field_name_address3=DB_getItem( $tbl3   ,"name","templatesetvar='{$maps_address3}'");

    $must='<span class="must">'.$LANG_DATABOX_ADMIN['must']    .'</span>';
    $msg=$LANG_DATABOX_ADMIN[$mode.'msg']."<br ".XHTML.">"."<br ".XHTML.">";
    $msg.=$LANG_confignames['databox']['maps_mid'].":".$maps_mid.":".$field_name_mid." ". $must."<br ".XHTML.">";
    $msg.=$LANG_confignames['databox']['maps_pref'].":".$maps_pref.":".$field_name_pref." ". $must."<br ".XHTML.">";
    $msg.=$LANG_confignames['databox']['maps_address1'].":".$maps_address1.":".$field_name_address1." ". $must."<br ".XHTML.">";
    $msg.=$LANG_confignames['databox']['maps_lat'].":".$maps_lat.":".$field_name_lat."<br ".XHTML.">";
    $msg.=$LANG_confignames['databox']['maps_lng'].":".$maps_lng.":".$field_name_lng."<br ".XHTML.">";
    $msg.=$LANG_confignames['databox']['maps_address2'].":".$maps_address2.":".$field_name_address2."<br ".XHTML.">";
    $msg.=$LANG_confignames['databox']['maps_address3'].":".$maps_address3.":".$field_name_address3."<br ".XHTML.">";

    $tmpl->set_var('help', $msg);

    // SAVE、CANCEL ボタン
    $tmpl->set_var('lang_confirm', $LANG_DATABOX_ADMIN['confirm']);
    $tmpl->set_var('lang_submit', $LANG_ADMIN['submit']);
    $tmpl->set_var('lang_cancel', $LANG_ADMIN['cancel']);
    $tmpl->set_var('mode', $mode."exec");

    $tmpl->parse ('output', 'confirm');
    $comfirm = $tmpl->finish ($tmpl->get_var ('output'));

    $retval = $comfirm;


    return $retval;
}

function fncScheduled()
{
    global $_CONF;
    global $LANG_ADMIN;

    global $LANG_DATABOX_ADMIN;

    global $_DATABOX_CONF;
    global $LANG_confignames;
    global $_DATABOX_CONF;
    global $LANG_CONFIG;
    global $_TABLES;
	
	$name='databox_maps_cron';
    
    //擬似クーロン実行日
    $datetime = DB_getItem( $_TABLES['vars'], 'value', "name = '{$name}'" );

    $tmpl = new Template ($_CONF['path'] . "plugins/".THIS_PLUGIN."/templates/admin/");
    $tmpl->set_file(array('confirmation' => 'confirmation.thtml'));

    $tmpl->set_var('site_admin_url', $_CONF['site_admin_url']);
    $tmpl->set_var('gltoken_name', CSRF_TOKEN);
    $tmpl->set_var('gltoken', SEC_createToken());
    $tmpl->set_var ( 'xhtml', XHTML );

	//
    $actionname=$LANG_DATABOX_ADMIN['schedule'];
    $actionprg="{$_CONF['site_admin_url']}/plugins/".THIS_SCRIPT."?mode=scheduleupdate";

    $tmpl->set_var('actionname', $actionname);
    $tmpl->set_var('actionprg', $actionprg);


	if ($_DATABOX_CONF['maps_cron_schedule_interval'] == 0) {
		$msg=$LANG_DATABOX_ADMIN['cron_schedule_Enable'];
	    $lang_datetime="";
        $datetime="";
	}else{
        if ($datetime==""){
            $datetime=time();
            $nextdatetime=time();
		}else{
            $nextdatetime=$datetime+$_DATABOX_CONF['maps_cron_schedule_interval'];
		}
        $msg=$LANG_DATABOX_ADMIN['cron_schedule'];
	    $lang_datetime=$LANG_DATABOX_ADMIN["nextdatetime"];
        $datetime=DATABOX_datetimeedit($nextdatetime,"LANG_DATABOX_ADMIN","datetime");
	}
	$tmpl->set_var('help', $msg);
    $tmpl->set_var('lang_checklist',$lang_datetime);
    $tmpl->set_var('checklist', $datetime);
	

    // SAVE、CANCEL ボタン
    $tmpl->set_var('lang_confirm', $LANG_DATABOX_ADMIN['confirm']);
    $tmpl->set_var('lang_submit', $LANG_CONFIG['save_changes']);
    $tmpl->set_var('lang_cancel', $LANG_ADMIN['cancel']);
    $tmpl->set_var('mode', "mapsscheduledupdate");

    $tmpl->parse ('output', 'confirmation');
    $retval = $tmpl->finish ($tmpl->get_var ('output'));

    return $retval;
}

function fncScheduledUpdate ()
{
    global $_DATABOX_CONF;
    global $_TABLES;
	$name='databox_maps_cron';

	$datetime = DB_getItem( $_TABLES['vars'], 'value', "name = '{$name}'" );
	if  ($datetime==""){
        DB_query( "INSERT INTO {$_TABLES['vars']} (name, value) VALUES ('{$name}','')" );
    }
	$datetime_year= COM_applyFilter($_POST['datetime_year'],true);
    $datetime_month= COM_applyFilter($_POST['datetime_month'],true);
    $datetime_day= COM_applyFilter($_POST['datetime_day'],true);
    $datetime_hour= COM_applyFilter($_POST['datetime_hour'],true);
    $datetime_minute= COM_applyFilter($_POST['datetime_minute'],true);


    $w=COM_convertDate2Timestamp(
        $datetime_year."-".$datetime_month."-".$datetime_day
        , $datetime_hour.":".$datetime_minute."::00"
	);
	$w=$w-$_DATABOX_CONF['maps_cron_schedule_interval'];
    DB_query( "UPDATE {$_TABLES['vars']} SET value = '$w' WHERE name = '{$name}'" );

    return;
}

// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
//############################
$pi_name    = 'databox';
//############################

// 引数
$action = '';
if (isset ($_REQUEST['action'])) {
    $action = COM_applyFilter ($_REQUEST['action'], false);
}
if (isset ($_REQUEST['mode'])) {
    $mode = COM_applyFilter ($_REQUEST['mode'], false);
}
$msg = '';
if (isset ($_REQUEST['msg'])) {
    $msg = COM_applyFilter ($_REQUEST['msg'], true);
}
$seq = '';
if (isset ($_REQUEST['seq'])) {
    $seq = COM_applyFilter ($_REQUEST['seq'], true);
}

$old_mode="";
if (isset($_REQUEST['old_mode'])) {
    $old_mode = COM_applyFilter($_REQUEST['old_mode'],false);
    if ($mode==$LANG_ADMIN['cancel']) {
        $mode = $old_mode;
    }
}

if (($mode == $LANG_ADMIN['save']) && !empty ($LANG_ADMIN['save'])) { // save
    $mode="save";
}else if (($mode == $LANG_ADMIN['delete']) && !empty ($LANG_ADMIN['delete'])) {
    $mode="delete";
}

if ($action == $LANG_ADMIN['cancel'])  { // cancel
    $mode="";
}

//---

//echo "mode=".$mode."<br>";
if ($mode=="" 
     OR $mode=="mapsmarkersupdate"
     OR $mode=="mapsmarkersclear"
     OR $mode=="mapsscheduled"
    ) {
}else{
    if (!SEC_checkToken()){
 //    if (SEC_checkToken()){//テスト用
        COM_accessLog("User {$_USER['username']} tried to illegally and failed CSRF checks.");
        echo COM_refresh($_CONF['site_admin_url'] . '/index.php');
        exit;
    }
}



//
$menuno=10;
$display = '';

$display = '';
$information = array();

$display.=ppNavbarjp($navbarMenu,$LANG_DATABOX_admin_menu[$menuno]);

switch ($mode) {
    case 'mapsmarkersclear'://MAPSプラグイン Markers clear　Confirmation
        $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'];
        $display .= fncMenu();
        $display .= fncConfirmation($mode);
        break;
    case 'mapsmarkersclearexec'://MAPSプラグイン Markers clear
        $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'];
        $display .= fncMenu();
        $display .= fncmarkersclear();
        break;
    case 'mapsmarkersupdateexec'://MAPSプラグイン Markers 更新
        $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'];
        $display .= fncMenu();
        $display .= fncmarkersclear();
        $display .= fncmarkersupdate();
        break;
    case 'mapsscheduledupdate':
        $dummy= fncScheduledupdate();
    case 'mapsscheduled'://Markers 更新 疑似CRON
        $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'];
        $display .= fncMenu();
        $display .= fncScheduled();
        break;
    case 'mapsmarkersupdate'://MAPSプラグイン Markers update
    default:// 初期表示
        $mode='mapsmarkersupdate';
        $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'].$LANG_DATABOX_ADMIN['mapsmarkersupdate'];
        $display .= fncMenu();
        $display .= fncConfirmation($mode);
        break;
}
$display=COM_startBlock($LANG_DATABOX_ADMIN['piname'],''
             ,COM_getBlockTemplate('_admin_block', 'header'))
         .$display
         .COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));
;

$display=DATABOX_displaypage($pi_name,'_admin',$display,$information);

COM_output($display);

?>
