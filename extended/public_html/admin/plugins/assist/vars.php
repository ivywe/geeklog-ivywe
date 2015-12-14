<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | vars.php 更新                                                             |
// +---------------------------------------------------------------------------+
// $Id: vars.php
// public_html/admin/plugins/assist/vars.php
// 2009/12/09 tsuchitani AT ivywe DOT co DOT jp
// 20120702

// Set this to true to get various debug messages from this script
$_ASSIST_VERBOSE = false;

define ('THIS_SCRIPT', 'vars.php');
//define ('THIS_SCRIPT', 'test.php');

include_once('assist_functions.php');

//
function fncdatetimeedit(
	$datetime_value
	,$title
	,$token
	,$script=""
	,$datetime="datetime"
) {
    global $_CONF;
    global $LANG_CONFIG ;
	global $_SCRIPTS;

    $pi_name="assist";
    $tmplfld=assist_templatePath('admin','default',$pi_name);
    $tmpl = new Template($tmplfld);

    $tmpl->set_file(array('datetimeedit' => 'datetime.thtml'));
	
	// Loads jQuery UI datepicker
	if (version_compare(VERSION, '2.0.0') >= 0) {
		$_SCRIPTS->setJavaScriptLibrary('jquery.ui.datepicker');
		$_SCRIPTS->setJavaScriptLibrary('jquery-ui-i18n');
		$_SCRIPTS->setJavaScriptFile('datepicker', '/javascript/datepicker.js');

		$langCode = COM_getLangIso639Code();
		$toolTip  = 'Click and select a date';	// Should be translated
		$imgUrl   = $_CONF['site_url'] . '/images/calendar.png';

		$_SCRIPTS->setJavaScript(
			"jQuery(function () {"
			. "  geeklog.datepicker.set('datetime', '{$langCode}', '{$toolTip}', '{$imgUrl}');"
			. "});", TRUE, TRUE
		);
	}

    $datetime_month = date('m', $datetime_value);
    $datetime_day = date('d', $datetime_value);
    $datetime_year = date('Y', $datetime_value);
    $datetime_hour = date ('H', $datetime_value);
    $datetime_minute = date ('i', $datetime_value) ;

    //
    $month_options = COM_getMonthFormOptions ($datetime_month);
    $day_options = COM_getDayFormOptions ($datetime_day);
    $year_options = COM_getYearFormOptions ($datetime_year);
    $hour_options = COM_getHourFormOptions ($datetime_hour, 24);
    $minute_options = COM_getMinuteFormOptions ($datetime_minute);

    $tmpl->set_var( 'site_url', $_CONF['site_url']);

    $tmpl->set_var( 'datetime_script', $script);

    $tmpl->set_var( 'datetime_title', $title);
    $tmpl->set_var( 'datetime', $datetime);

    $tmpl->set_var( 'datetime_year_options', $year_options);
    $tmpl->set_var( 'datetime_month_options', $month_options);
    $tmpl->set_var( 'datetime_day_options', $day_options);
    $tmpl->set_var( 'datetime_hour_options', $hour_options);
    $tmpl->set_var( 'datetime_minute_options', $minute_options);

    $tmpl->set_var( 'lang_yy', "年");
    $tmpl->set_var( 'lang_mm', "月");

    $tmpl->set_var('gltoken_name', CSRF_TOKEN);
    $tmpl->set_var('gltoken', $token);
    $tmpl->set_var ( 'xhtml', XHTML );
    $tmpl->set_var('save_changes', $LANG_CONFIG['save_changes']);

    //
    $tmpl->parse ('output', 'datetimeedit');
    $rt = $tmpl->finish ($tmpl->get_var ('output'));

    return $rt;

}

// +---------------------------------------------------------------------------+
// | 機能  表示                                                            |
// | 書式 fncMebu()                                                            |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:一覧                                                           |
// +---------------------------------------------------------------------------+
function fncMenu()
{
    global $_CONF;
    global $_TABLES;
    global $LANG_ADMIN;
    global $LANG09;
    global $LANG28 ;
    global $LANG_ASSIST_ADMIN;

    $retval = '';

    //擬似クーロン実行日
    $datetime = DB_getItem( $_TABLES['vars'], 'value', "name = 'last_scheduled_run'" );

    if ($datetime===""){
        $datetime=time();
    }

    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);

    $script="";
    $last_scheduled_run=fncdatetimeedit(
        $datetime
        ,$LANG_ASSIST_ADMIN['last_scheduled_run']
        ,$token
        ,$script

        );

    $retval .= $last_scheduled_run;

    return $retval;
}
// +---------------------------------------------------------------------------+
// | 機能  擬似クーロン実施日更新                                              |
// | 書式 fncdatetime ()                                                       |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
function fncdatetime ()
{
    global $_TABLES;
    global $LANG_PROFILE_ADMIN;

    $datetime_year= COM_applyFilter($_POST['datetime_year'],true);
    $datetime_month= COM_applyFilter($_POST['datetime_month'],true);
    $datetime_day= COM_applyFilter($_POST['datetime_day'],true);
    $datetime_hour= COM_applyFilter($_POST['datetime_hour'],true);
    $datetime_minute= COM_applyFilter($_POST['datetime_minute'],true);


    $w=COM_convertDate2Timestamp(
        $datetime_year."-".$datetime_month."-".$datetime_day
        , $datetime_hour.":".$datetime_minute."::00"
        );
    DB_query( "UPDATE {$_TABLES['vars']} SET value = '$w' WHERE name = 'last_scheduled_run'" );

    return;
}



// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
// 引数
if (isset ($_REQUEST['mode'])) {
    $mode = COM_applyFilter ($_REQUEST['mode'], false);
}
if ($mode=="" ) {
}else{
    if (!SEC_checkToken()){
 //    if (SEC_checkToken()){//テスト用
        COM_accessLog("User {$_USER['username']} tried to illegally and failed CSRF checks.");
        echo COM_refresh($_CONF['site_admin_url'] . '/index.php');
        exit;
    }
}

//次回回答済日時更新
if ($mode=="datetime") {
    fncdatetime();
}

//
$menuno=3;
$display = '';
$display.=ppNavbarjp($navbarMenu,$LANG_ASSIST_admin_menu[$menuno]);

$information = array();
$information['what']='menu';
$information['rightblock']=false;
$information['pagetitle']=$LANG_ASSIST_ADMIN['piname'];

$display .= fncMenu();

//FOR GL2.0.0 
if (COM_versionCompare(VERSION, "2.0.0",  '>=')){
	$display = COM_createHTMLDocument($display,$information);
}else{
	$display = COM_siteHeader ($information['what'], $information['pagetitle']).$display;
	$display .= COM_siteFooter($information['rightblock']);
}

COM_output($display);

?>
