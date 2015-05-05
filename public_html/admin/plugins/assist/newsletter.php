<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | newsletter.php 更新
// +---------------------------------------------------------------------------+
// $Id: newsletter.php
// public_html/admin/plugins/asit/newsletter.php
// 2010/12/24 tsuchitani AT ivywe DOT co DOT jp
// 20111220 update ニュースレター疑似クーロン実行日の使用をやめた
// 20120618 gl2.0.0  対応

// Set this to true to get various debug messages from this script
$_ASSIST_VERBOSE = false;

define ('THIS_SCRIPT', 'newsletter.php');
//define ('THIS_SCRIPT', 'test.php');

require_once('assist_functions.php');

require_once( $_CONF['path_system'] . 'lib-admin.php' );

if( function_exists( "LIB_OutLog" )) {
}else{
	require_once ($_CONF['path'] . 'plugins/assist/lib/lib_outlog.php');
}
require_once ($_CONF['path'] . 'plugins/assist/lib/lib_datetimeedit.php');
require_once ($_CONF['path'] . 'plugins/assist/lib/lib_mail_is_mobile.php');

require_once( $_CONF['path_system'] . 'lib-admin.php' );

// +---------------------------------------------------------------------------+
// | 機能  編集画面表示                                                        |
// | 書式 fncEdit()                                                            |
// +---------------------------------------------------------------------------+
// | 引数 $message:メッセージ                                                  |
// | 引数 $wkymlmguserflg:wkymlmguserプラグがインストールされている            |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:編集画面                                                       |
// +---------------------------------------------------------------------------+
//20111220
function fncEdit(
    $message=""
    ,$wkymlmguserflg=false
)
{
    global $_CONF;
    global $_TABLES;
    global $LANG_ASSIST_ADMIN;
    global $LANG_ADMIN;
    global $_ASSIST_CONF;
    global $LANG_ASSIST_INTROBODY;
    global $LANG_ASSIST_TOENV;
    global $LANG31;
	global $_SCRIPTS;

    $retval = '';

    //メッセージ表示
    if (!empty ($message)) {

        $retval .= COM_startBlock ($LANG_ASSIST_ADMIN['msg'], '',
                           COM_getBlockTemplate ('_msg_block', 'header'));
        $retval .=$message;
        $retval .=COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));

        // clean 'em up
        $fromname=COM_applyFilter($_POST['fromname']);
        $replyto=COM_applyFilter($_POST['replyto']);
        $sprefix=COM_applyFilter($_POST['sprefix']);
        $sid=COM_applyFilter($_POST['sid']);
        $testto=COM_applyFilter($_POST['testto']);
        $uidfrom=COM_applyFilter($_POST['uidfrom'],true);
        $uidto=COM_applyFilter($_POST['uidto'],true);
		// hiroron start 2010/07/13
        $dt_year= COM_applyFilter($_POST['datetime_year'],true);
        $dt_month= COM_applyFilter($_POST['datetime_month'],true);
        $dt_day= COM_applyFilter($_POST['datetime_day'],true);
        $dt_hour= COM_applyFilter($_POST['datetime_hour'],true);
        $dt_minute= COM_applyFilter($_POST['datetime_minute'],true);
        $datetime_value = COM_convertDate2Timestamp($dt_year.'-'.$dt_month.'-'.$dt_day, $dt_hour.':'.$dt_minute.':00');
        // 冒頭文　本文 introbody
		$introbody= COM_applyFilter($_POST['introbody'],true);
		
		//送信先環境
		$toenv=COM_applyFilter($_POST['toenv'],true);
		//送信先グループ
		$selectgroup=COM_applyFilter($_POST['selectgroup'],true);
		
        // ユーザの受信許可設定を無視して送る
        $overstyr= COM_applyFilter($_POST['overstyr'],true);

		//一括予約
		$bulkmm= COM_applyFilter($_POST['bulkmm'],true);
        $bulkcnt= COM_applyFilter($_POST['bulkcnt'],true);

    }else{
       $fromname = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_fromname'" );
       $fromname = COM_stripslashes($fromname);
       if ($fromname==""){
           $fromname = $_CONF['site_name'];
       }
       $replyto = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_replyto'" );
       $replyto = COM_stripslashes($replyto);
       if ($replyto==""){
           $replyto = $_CONF['site_mail'];
       }
       $sprefix = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_sprefix'" );
       $sprefix = COM_stripslashes($sprefix);
       $sid = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_sid'" );
       $sid = COM_stripslashes($sid);
       $testto = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_testto'" );
       $testto = COM_stripslashes($testto);
       $uidfrom = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_uidfrom'" );
       $uidfrom = COM_stripslashes($uidfrom);
       $uidto = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_uidto'" );
       $uidto = COM_stripslashes($uidto);
		// hiroron start 2010/07/13
       $datetime_value = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_re_datetime'" );

       // 冒頭文　本文 introbody
       $introbody = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_introbody'" );
		
		//送信先環境
		$toenv=DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_toenv'" );
		//送信先グループ
		$selectgroup=DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_selectgroup'" );
		
		// ユーザの受信許可設定を無視して送る
        $overstyr = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_overstyr'" );
		
        $bulkmm = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_bulkmm'" );
        $bulkcnt = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_bulkcnt'" );

    }
	
    $retval .= COM_startBlock ($LANG_ASSIST_ADMIN['edit'], '',
                               COM_getBlockTemplate ('_admin_block', 'header'));

    $pi_name="assist";
    $tmplfld=assist_templatePath('admin','default',$pi_name);
    $templates = new Template($tmplfld);
	
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

    $templates->set_file('editor',"newsletter.thtml");
	//--
    $templates->set_var('lang_must', $LANG_ASSIST_ADMIN['must']);
    $templates->set_var('site_url', $_CONF['site_url']);
    $templates->set_var('site_admin_url', $_CONF['site_admin_url']);

    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);
    $templates->set_var('gltoken_name', CSRF_TOKEN);
    $templates->set_var('gltoken', $token);
    $templates->set_var ( 'xhtml', XHTML );

    $templates->set_var('script', THIS_PLUGIN."/".THIS_SCRIPT);


    //-----
    $w="";
    $logfile = $_CONF['path_log'] . 'assist_newsletter.log';
    if (!file_exists($logfile)) {
        $w.= sprintf ($LANG_ASSIST_ADMIN['mail_logfile']
                    , $logfile);
    }else if (!is_writable($logfile)) {
        $w.= sprintf ($LANG_ASSIST_ADMIN['mail_logfile']
                    , $logfile);
    }
	
    $tid=$_ASSIST_CONF['newsletter_tid'];
    $topicname = DB_getItem ($_TABLES['topics'], 'topic',"tid = '$tid'");
    IF ($topicname==""){
        $topicname=$tid;
    }
    $w.= sprintf ($LANG_ASSIST_ADMIN['mail_msg'], $topicname);

    $templates->set_var('mail_msg', $w);

    $templates->set_var('mail_msg1', $LANG_ASSIST_ADMIN['mail_msg1']);
    $templates->set_var('mail_msg2', $LANG_ASSIST_ADMIN['mail_msg2']);
    $templates->set_var('mail_msg3', $LANG_ASSIST_ADMIN['mail_msg3']);
    $templates->set_var('mail_msg4', $LANG_ASSIST_ADMIN['mail_msg4']);

    $templates->set_var('lang_fromname', $LANG_ASSIST_ADMIN['fromname']);
    //@@@@@ $templates->set_var('help_fromname', $LANG_ASSIST_ADMIN['help']);
    $templates->set_var('fromname', $fromname);

    //replyto
    $templates->set_var('lang_replyto', $LANG_ASSIST_ADMIN['replyto']);
    $templates->set_var('replyto', $replyto);

    //subject_prefix
    $templates->set_var('lang_sprefix', $LANG_ASSIST_ADMIN['sprefix']);
    $templates->set_var('sprefix', $sprefix);

    //sid
    $templates->set_var('lang_sid', $LANG_ASSIST_ADMIN['sid']);
    $templates->set_var('sid', $sid);
	
	//FOR GL2.0.0 
	if (COM_versionCompare(VERSION, "2.0.0",  '>=')){
		//$where ="s.sid = t.id AND t.tid=\"".$tid."\"";
		//$tables="{$_TABLES['stories']} AS s ,{$_TABLES['topic_assignments']} AS ta";
		$topics = TOPIC_getChildList($tid);
		
		$where ="s.sid = ta.id ";
		if  ($topics==""){
			$where .=" AND tid=\"".$tid."\"";;
		}else{
			$where .=" AND ta.tid IN ($topics)";
		}
		
		$tables="{$_TABLES['stories']} AS s ";
		$tables.=" ,{$_TABLES['topic_assignments']} AS ta";
		
		$optionlist_sid= "<option value=''>{$LANG_ASSIST_ADMIN['select_sid']}</option>".LB;
		$optionlist_sid.=COM_optionList ($tables , 'distinct s.sid,s.title,s.date*-1',$sid,2,$where);
	}else{
		$where ="tid=\"".$tid."\"";
		$optionlist_sid= "<option value=''>{$LANG_ASSIST_ADMIN['select_sid']}</option>".LB;
		$optionlist_sid.=COM_optionList ($_TABLES['stories'], 'sid,title,date*-1',$sid,2,$where);
	}

    $templates->set_var ('optionlist_sid', $optionlist_sid);

    // 冒頭文　本文 introbody
    $templates->set_var('lang_introbody', $LANG_ASSIST_ADMIN['introbody']);
    $list_introbody=assist_getradiolist ($LANG_ASSIST_INTROBODY,"introbody",$introbody);
    $templates->set_var( 'list_introbody', $list_introbody);
	
	//送信先環境
    $templates->set_var('lang_toenv', $LANG_ASSIST_ADMIN['toenv']);
    $list_toenv=assist_getradiolist ($LANG_ASSIST_TOENV,"toenv",$toenv);
	$templates->set_var( 'list_toenv', $list_toenv);
	
	//送信先グループ
    $thisUsersGroups = SEC_getUserGroups();
    uksort($thisUsersGroups, 'strcasecmp');
	$optionlist_selectgroup = '';
    if ($wkymlmguserflg==true){
        $optionlist_selectgroup .= '<option value="' . 99999 . '"';
        if (($selectgroup > 0) && ($selectgroup == "99999")) {
            $optionlist_selectgroup .= ' selected="selected"';
        }
        $optionlist_selectgroup .= '>' . $LANG_ASSIST_ADMIN['wkymlmguser_user']  . '</option>'.LB;
	}
	foreach ($thisUsersGroups as $groupName => $groupID) {
        if ($groupName != 'All Users') {
            $optionlist_selectgroup .= '<option value="' . $groupID . '"';
            if (($selectgroup > 0) && ($selectgroup == $groupID)) {
                $optionlist_selectgroup .= ' selected="selected"';
            }
            $optionlist_selectgroup .= '>' . ucwords($groupName) . '</option>'.LB;
        }
    }
    $templates->set_var('lang_selectgroup', $LANG_ASSIST_ADMIN['selectgroup']);
    $templates->set_var('optionlist_selectgroup', $optionlist_selectgroup);
	
    // ユーザの受信許可設定を無視して送る
    $templates->set_var('lang_overstyr', $LANG31['14']);
    if ($overstyr==0){
        $templates->set_var('is_checked_overstyr', '');
    }else{
        $templates->set_var('is_checked_overstyr', 'checked="checked"');
    }

    //testto
    $templates->set_var('lang_testto', $LANG_ASSIST_ADMIN['testto']);
    $templates->set_var('testto', $testto);

    //uidfrom-to
    $templates->set_var('lang_sendto', $LANG_ASSIST_ADMIN['sendto']);
    $templates->set_var('lang_uidfrom', $LANG_ASSIST_ADMIN['uidfrom']);
    $templates->set_var('uidfrom', $uidfrom);
    $templates->set_var('lang_uidto', $LANG_ASSIST_ADMIN['uidto']);
    $templates->set_var('uidto', $uidto);
    $templates->set_var('lang_sendto_remarks', $LANG_ASSIST_ADMIN['sendto_remarks']);

    if ($wkymlmguserflg==true){
        $templates->set_var('user_wkymlmguser', $LANG_ASSIST_ADMIN['wkymlmguser_on']);
    }else{
        $templates->set_var('user_wkymlmguser', $LANG_ASSIST_ADMIN['wkymlmguser_off']);
    }

	// hiroron start 2010/07/13
    if ($datetime_value==="") {
        $datetime_value = time();
    }
    $datetime_month = date('m', $datetime_value);
    $datetime_day = date('d', $datetime_value);
    $datetime_year = date('Y', $datetime_value);
    $datetime_hour = date ('H', $datetime_value);
    $datetime_minute = date ('i', $datetime_value);

    $month_options = COM_getMonthFormOptions ($datetime_month);
    $day_options = COM_getDayFormOptions ($datetime_day);
    $year_options = COM_getYearFormOptions ($datetime_year);
    $hour_options = COM_getHourFormOptions ($datetime_hour, 24);
    $minute_options = COM_getMinuteFormOptions ($datetime_minute);

    $templates->set_var( 'lang_reserv_datetime', $LANG_ASSIST_ADMIN['reserv_datetime']);
    $templates->set_var( 'datetime', 'datetime');

    $templates->set_var( 'datetime_year_options', $year_options);
    $templates->set_var( 'datetime_month_options', $month_options);
    $templates->set_var( 'datetime_day_options', $day_options);
    $templates->set_var( 'datetime_hour_options', $hour_options);
    $templates->set_var( 'datetime_minute_options', $minute_options);

    $templates->set_var( 'lang_yy', $LANG_ASSIST_ADMIN['yy']);
    $templates->set_var( 'lang_mm', $LANG_ASSIST_ADMIN['mm']);
    $templates->set_var( 'lang_dd', $LANG_ASSIST_ADMIN['dd']);
	// hiroron end 2010/07/13
    $templates->set_var('lang_reserv_datetime_remarks', $LANG_ASSIST_ADMIN['reserv_datetime_remarks']);
	
	//予約送信
    //$templates->set_var( 'lang_bulkbooking', $LANG_ASSIST_ADMIN['mail_bulkbooking']);
    $templates->set_var( 'minute', $LANG_ASSIST_ADMIN['minute']);
    $templates->set_var( 'every', $LANG_ASSIST_ADMIN['every']);
    $templates->set_var( 'increments', $LANG_ASSIST_ADMIN['increments']);
    $templates->set_var( 'bulkmm', $bulkmm);
    $templates->set_var( 'bulkcnt', $bulkcnt);
	
    // SAVE、CANCEL ボタン
    $templates->set_var('lang_save', $LANG_ADMIN['save']);
    $templates->set_var('lang_cancel', $LANG_ADMIN['cancel']);
    $templates->set_var('lang_testsend', $LANG_ASSIST_ADMIN['mail_test']);
    $templates->set_var('lang_send', $LANG_ASSIST_ADMIN['mail_send']);
	// hiroron start 2010/07/13
    $templates->set_var('lang_reserv', $LANG_ASSIST_ADMIN['mail_reserv']);
	// hiroron end 2010/07/13

	// hiroron start 2010/07/15
    $templates->set_var('list_reserv', fncListReserv());
	// hiroron end 2010/07/15

    //
    $templates->parse('output', 'editor');
    $retval .= $templates->finish($templates->get_var('output'));
    $retval .= COM_endBlock (COM_getBlockTemplate ('_admin_block', 'footer'));

    return $retval;
}
// +---------------------------------------------------------------------------+
// | 機能  保存                                                                |
// | 書式 fncSave ($edt_flg)                                                   |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:戻り画面＆メッセージ                                           |
// +---------------------------------------------------------------------------+
//20111220
function fncSave ($mode)
{
    global $_CONF;
    global $LANG_ASSIST_ADMIN;
    global $_TABLES;

    $retval = '';

	//------------------
	$fary=array();
	$fary[]=array('name' =>'fromname'   ,'reserv' =>'fn' );//@@
    $fary[]=array('name' =>'replyto'    ,'reserv' =>'rt');
    $fary[]=array('name' =>'sprefix'    ,'reserv' =>'sp');
    $fary[]=array('name' =>'sid'        ,'reserv' =>'si');
    $fary[]=array('name' =>'testto');
    $fary[]=array('name' =>'uidfrom'    ,'reserv' =>'uf');
    $fary[]=array('name' =>'uidto'      ,'reserv' =>'ut');
    ///@@@@@ 20111220 delete $fary[]=array('name' =>'last_schedule' );
	
	$fary[]=array('name' =>'introbody'  ,'reserv' =>'ib');//@@
    $fary[]=array('name' =>'overstyr'   ,'reserv' =>'os');//@@
    $fary[]=array('name' =>'toenv'      ,'reserv' =>'te');//@@
	$fary[]=array('name' =>'selectgroup','reserv' =>'sg');//@@
		
	$fary[]=array('name' =>'bulkmm','reserv' =>'bm');//@@
	$fary[]=array('name' =>'bulkcnt','reserv' =>'bc');//@@
	
	//------------------
	// clean 'em up
	
    $fromname=COM_applyFilter($_POST['fromname']);
    $fromname=addslashes (COM_checkHTML (COM_checkWords ($fromname)));

    $replyto=COM_applyFilter($_POST['replyto']);
    $replyto=addslashes (COM_checkHTML (COM_checkWords ($replyto)));

	//--
    $sprefix=COM_applyFilter($_POST['sprefix']);
    $sprefix=addslashes (COM_checkHTML (COM_checkWords ($sprefix)));

    $sid=COM_applyFilter($_POST['sid']);
    $sid=addslashes (COM_checkHTML (COM_checkWords ($sid)));

    $testto=COM_applyFilter($_POST['testto']);
    $testto=addslashes (COM_checkHTML (COM_checkWords ($testto)));

	$uidfrom=COM_applyFilter($_POST['uidfrom'],true);
    $uidfrom=addslashes (COM_checkHTML (COM_checkWords ($uidfrom)));

    $uidto=COM_applyFilter($_POST['uidto'],true);
    $uidto=addslashes (COM_checkHTML (COM_checkWords ($uidto)));

	// hiroron start 2010/07/13
    $dt_year= COM_applyFilter($_POST['datetime_year'],true);
    $dt_month= COM_applyFilter($_POST['datetime_month'],true);
    $dt_day= COM_applyFilter($_POST['datetime_day'],true);
    $dt_hour= COM_applyFilter($_POST['datetime_hour'],true);
    $dt_minute= COM_applyFilter($_POST['datetime_minute'],true);
	// hiroron end 2010/07/13
    $dt = COM_convertDate2Timestamp($dt_year.'-'.$dt_month.'-'.$dt_day, $dt_hour.':'.$dt_minute.':00');

    $uidto=COM_applyFilter($_POST['uidto'],true);
    $uidto=addslashes (COM_checkHTML (COM_checkWords ($uidto)));

	// 冒頭文　本文 introbody
    $introbody=COM_applyFilter($_POST['introbody'],true);
    $introbody=addslashes (COM_checkHTML (COM_checkWords ($introbody)));
    // ユーザの受信許可設定を無視して送る
    $overstyr=COM_applyFilter($_POST['overstyr'],true);
    $overstyr=addslashes (COM_checkHTML (COM_checkWords ($overstyr)));
	
	//送信先環境
	$toenv=COM_applyFilter($_POST['toenv'],true);
    $toenv=addslashes (COM_checkHTML (COM_checkWords ($toenv)));
	//送信先グループ
	$selectgroup=COM_applyFilter($_POST['selectgroup'],true);
    $selectgroup=addslashes (COM_checkHTML (COM_checkWords ($selectgroup)));
	//一括予約
	$bulkmm= COM_applyFilter($_POST['bulkmm'],true);
    $bulkmm=addslashes (COM_checkHTML (COM_checkWords ($bulkmm)));
    $bulkcnt= COM_applyFilter($_POST['bulkcnt'],true);
    $bulkcnt=addslashes (COM_checkHTML (COM_checkWords ($bulkcnt)));


    // CHECK　はじめ
    $err="";
    //差出人必須
    if (empty($fromname)){
        $err.=$LANG_ASSIST_ADMIN['err_fromname']."<br/>".LB;
    }
    if (COM_isEmail($replyto)==false){
        $err.=$LANG_ASSIST_ADMIN['err_replyto']."<br/>".LB;
    }
    //差出人必須
	// hiroron start 2010/07/13
	//    if ($mode==="test" OR $mode==="send"){
    if ($mode==="test" OR $mode==="send" OR $mode==="reserv"){
	// hiroron end 2010/07/13
        if ($sid==""){
            $err.=$LANG_ASSIST_ADMIN['err_sid']."<br/>".LB;
        }
    }
    //test　送信先
    if ($mode==="test"){
        if ($test===""){
            $err.=$LANG_ASSIST_ADMIN['err_testto']."<br/>".LB;
        }
    }
    if ($testto<>""){
        if (COM_isEmail($testto)==false){
            $err.=$LANG_ASSIST_ADMIN['err_testto']."<br/>".LB;
        }
    }
	// hiroron start 2010/07/13
    // 予約送信
    if ($mode === 'reserv') {
        if ($dt_year==="" OR $dt_month==="" OR $dt_day==="" OR $dt_hour==="" OR $dt_minute==="") {
            $err.=$LANG_ASSIST_ADMIN['err_reserv']."<br/>".LB;
        }
    }
	// hiroron end 2010/07/13
	
	
	
    //errorのあるとき
    if ($err<>"") {

        return $err;

    }
    // CHECK　おわり


    $fields="name";
    $fields.=",value";
	//
	for ($i=0; $i < count( $fary ); $i++) {
		$fname=$fary[$i]['name'];
		$values="'assist_{$fname}'";
		$values.=",'${$fname}'";
		DB_save($_TABLES['vars'],$fields,$values);
	}
	
    //assist_re_datetime
    $values="'assist_re_datetime'";
    $values.=",'$dt'";
    DB_save($_TABLES['vars'],$fields,$values);
	
	// hiroron start 2010/07/13
	if ($mode === "reserv") {
        $ts = $dt;

        //assist_fn_1234567890
		for ($i=0; $i < count( $fary ); $i++) {
			$reserv=$fary[$i]['reserv'];
			if ($reserv<>""){
				$fname=$fary[$i]['name'];
				$values="'assist_{$reserv}_{$ts}'";
				$values.=",'${$fname}'";
				DB_save($_TABLES['vars'],$fields,$values);
			}
		}
		$values="'assist_li_{$ts}'";
		$values.=",'0'";
		DB_save($_TABLES['vars'],$fields,$values);

        touch($_CONF['path_data']."assist_reserv_$ts");
    }
	
	// hiroron end 2010/07/13

    $rt=$LANG_ASSIST_ADMIN['mail_save_ok'];
    return $rt;

}



// +---------------------------------------------------------------------------+
// | 機能  メール送信                                                          |
// | 書式 fncsendmail ()                                                       |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
function fncsendmail (
    $mode=""
    ,$uidfrom=""
    ,$uidto=""
    ,$wkymlmguserflg=""
){

    global $_CONF;
    global $_TABLES;
    global $LANG_ASSIST_ADMIN;

    require_once $_CONF['path_system'] . 'lib-user.php';

    //$html = true ;    // If you want to send html mail
    $html = false ;    // If you want to send html mail

    /// Loop through and send the messages!
    //log 出力モード設定　0:作成しない,1:ファイルに出力
    $logmode =1;
    //$logfile = $_CONF['path_log'] . 'wkymlmguser.log';
    $logfile = $_CONF['path_log'] . 'assist_newsletter.log';

    $retval = '';

    $fromname = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_fromname'" );
    $fromname = COM_stripslashes($fromname);
    $replyto = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_replyto'" );
    $replyto = COM_stripslashes($replyto);
    $sprefix = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_sprefix'" );
    $sprefix = COM_stripslashes($sprefix);
    $sid = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_sid'" );
    $sid = COM_stripslashes($sid);
    $testto = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_testto'" );
    $testto = COM_stripslashes($testto);
    $uidfrom = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_uidfrom'" );
    $uidfrom = COM_stripslashes($uidfrom);
    $uidto = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_uidto'" );
    $uidto = COM_stripslashes($uidto);
	
	//送信先環境
    $toenv = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_toenv'" );
    $toenv = COM_stripslashes($toenv);
	//送信先グループ
    $selectgroup = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_selectgroup'" );
	$selectgroup = COM_stripslashes($selectgroup);
		
    // 冒頭文　本文 introbody
    $introbody = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_introbody'" );
    $introbody = COM_stripslashes($introbody);
    // ユーザの受信許可設定を無視して送る
    $overstyr = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_overstyr'" );
    $overstyr = COM_stripslashes($overstyr);

    $from = COM_formatEmailAddress ($fromname, $replyto);
    $subject = DB_getItem($_TABLES['stories']
                    ,"title"
                    ,"sid='{$sid}'");
    $subject = $sprefix.$subject;

    if ($introbody=="1"){
        $message = DB_getItem($_TABLES['stories']
                    ,"bodytext"
                    ,"sid='{$sid}'");
    }else{
        $message = DB_getItem($_TABLES['stories']
                    ,"introtext"
                    ,"sid='{$sid}'");
    }

    $message =  str_replace( '<br'.XHTML.'>',LB, $message );
    $message=strip_tags($message);


    $failures = array ();
    $successes=array ();

    if ($mode=="test"){
        $message=$LANG_ASSIST_ADMIN['mail_test_message'].LB.$message;

        $to = $testto;

        if (!COM_mail ($to, $subject, $message, $from, $html, $priority)) {
            $failures[] = htmlspecialchars ($to);
            $logentry= $LANG_ASSIST_ADMIN['mail_test_ng'].$to;
            $dummy = LIB_OutLog( $logentry ,$logfile,$logmode);

        } else {
            $successes[] = htmlspecialchars ($to);
            $logentry= $LANG_ASSIST_ADMIN['mail_test_ok'].$to;
            $dummy = LIB_OutLog( $logentry ,$logfile,$logmode);
        }
        $retval=$logentry;

    } else {

        $sql="SELECT DISTINCT t1.uid ,t1.email FROM ";
		
		//メルマガユーザか選択されたグループの登録ユーザか
		if ($selectgroup==="99999"){
			if ($wkymlmguserflg){
				$sql.=$_TABLES['wkymlmguser'] ." AS t1 ".LB;
				$sql.=" where ".LB;
				if ($uidfrom<>"0"){
					$sql.="  (t1.uid between ".$uidfrom ." and " .$uidto.")" .LB;
				}
			}else{
				$err="メルマガプラグインが有効ではありません";
				return $err;
			}
        }else{
			$groupList = implode(',', USER_getChildGroups($selectgroup));
			$sql.="{$_TABLES['users']} AS t1 ".LB;
			$sql.= ",{$_TABLES['userprefs']} AS t2 ".LB;
			$sql.= ",{$_TABLES['group_assignments']} AS t3 ".LB;
			
            $sql.=" where ".LB;
            $sql.=" (t1.uid = t2.uid ) ".LB;
            $sql.=" AND (t1.uid >1)  ".LB;
            $sql.=" AND (t1.status =3)  ".LB;
			
            // ユーザの受信許可設定を無視して送る でなければ
            if ($overstyr <>"1"){
                $sql.=" AND (t2.emailfromadmin =1) ".LB;
			}
			//指定グループ
			$sql .= " AND (t1.uid = t3.ug_uid) AND t3.ug_main_grp_id IN ({$groupList})".LB;
			if ($uidfrom<>"0"){
				$sql.=" AND (t1.uid between ".$uidfrom ." and " .$uidto.")" .LB;
			}
        }

        //---

        $sql.=" order by uid ".LB;

        $result = DB_query($sql);
        if ($result !== false) {
            $result = DB_query($sql);
            $nrows = DB_numRows($result);
            for ($i = 0; $i < $nrows; $i++) {
                $A = DB_fetchArray ($result);
				
				//送付先環境のチェック
				if ($toenv == '1') {              // PCのみ
					if ( LIB_mail_is_mobile($A['email']) ) {
						continue;
					}
				} elseif ($toenv == '2') {    // 携帯のみ
					if ( ! LIB_mail_is_mobile($A['email']) ) {
						continue;
					}
				}
				//
                $to = $A['email'];

                if (!COM_mail ($to, $subject, $message, $from, $html, $priority)) {
                    $failures[] = htmlspecialchars ($to);
                    $logentry= "NG uid:{$A['uid']} mail:{$A['email']}";
                    $dummy = LIB_OutLog( $logentry ,$logfile,$logmode);

                } else {
                    $successes[] = htmlspecialchars ($to);
                    $logentry= "OK uid:{$A['uid']} mail:{$A['email']}";
                    $dummy = LIB_OutLog( $logentry ,$logfile,$logmode);
                }

            }
        }

        $failcount = count ($failures);
        $successcount = count ($successes);
        $retval.=$LANG_ASSIST_ADMIN['mail_send_success']."=".$successcount.$LANG_ASSIST_ADMIN['mail_send_failure']."=".$failcount."<br>";
    }

    return $retval;
}


// hiroron start 2010/07/15
// +---------------------------------------------------------------------------+
// | 機能  予約リスト表示                                                      |
// | 書式 fncListReserv ()                                                     |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
function fncListReserv ()
{
	global $_CONF;
	global $_TABLES;
	global $LANG_ADMIN;
	global $MESSAGE;
	global $LANG_ASSIST_ADMIN;
    global $LANG_ASSIST_TOENV;

    $retval = '';
    $files = glob("{$_CONF['path_data']}assist_reserv_*",  GLOB_NOCHECK);
    if (is_array($files)) {
		if (count($files) > 0 && $files[0] != "{$_CONF['path_data']}assist_reserv_*") {
			//ヘッダ：編集～
			$header_arr[]=array('text' => $LANG_ASSIST_ADMIN['reservlist_no'], 'field' => 'no');
            $header_arr[]=array('text' => $LANG_ASSIST_ADMIN['reservlist_datetime'], 'field' => 'datetime');
			$header_arr[]=array('text' => $LANG_ASSIST_ADMIN['reservlist_range'], 'field' => 'range');
			$header_arr[]=array('text' => $LANG_ASSIST_ADMIN['toenv'], 'field' => 'toenv');
			$header_arr[]=array('text' => $LANG_ASSIST_ADMIN['selectgroup'], 'field' => 'selectgroup');
            $header_arr[]=array('text' => $LANG_ASSIST_ADMIN['reservlist_sid'], 'field' => 'sid');
            $header_arr[]=array('text' => $LANG_ASSIST_ADMIN['reservlist_cancel'], 'field' => 'reservcancel');
			
			//
			$text_arr = array('has_menu' =>  false, 'title' => $LANG_ASSIST_ADMIN['reservlist_title']);
			//
			$data_arr = array();
            $token = SEC_createToken();
            foreach ($files as $file) {
                $filename = basename($file);
                $aname = explode('_', $filename);
                if (!empty($aname[2])) {
                    $A=array();
                    $A['no'] = $aname[2];
					$A['datetime'] = date('Y-m-d H:i', $A['no']);
					//
                    $uidfrom = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_uf_{$A['no']}'" );
                    $uidfrom = COM_stripslashes($uidfrom);
					$uidto = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_ut_{$A['no']}'" );
					$uidto = COM_stripslashes($uidto);
					$lastid = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_li_{$A['no']}'" );
					$lastid = COM_stripslashes($lastid);
					
					$A['range'] = $uidfrom . ' -&gt; ' . $uidto;
					if ($lastid<>0){
						$A['range'] .="(".$lastid.")";
					}
					//
					$toenv = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_te_{$A['no']}'" );
					$toenv = COM_stripslashes($toenv);
					$A['toenv']=$LANG_ASSIST_TOENV[$toenv];
					//
					$selectgroup = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_sg_{$A['no']}'" );
					$selectgroup = COM_stripslashes($selectgroup);
					if ($selectgroup==="99999"){
						$A['selectgroup'] =$LANG_ASSIST_ADMIN['wkymlmguser_user'];
					}else{
						$A['selectgroup'] =DB_getItem( $_TABLES['groups'], 'grp_name', "grp_id = '{$selectgroup}'" );
					}
					//
					$sid = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_si_{$A['no']}'" );
                    $sid = COM_stripslashes($sid);
                    $title = DB_getItem( $_TABLES['stories'], 'title', "sid = '{$sid}'" );
                    $title = stripslashes(str_replace('$','&#36;',$title));
                    $A['sid'] = COM_createLink($title,  COM_buildUrl ($_CONF['site_url'] . "/article.php?story={$sid}"));
					//
					$iconimg = "<img src=\"{$_CONF['layout_url']}/images/deleteitem.png\" border=\"0\"";
					$iconimg .= "alt=\"{$LANG_ADMIN['delete']}\" title=\"{$LANG_ADMIN['delete']}\">";
					$url="{$_CONF['site_admin_url']}/plugins/assist/".THIS_SCRIPT."?";
					$url.="mode=reservcancel&amp;id={$A['no']}&amp;". CSRF_TOKEN ."={$token}";
					$attr = array('onClick' => "return confirm('{$MESSAGE[76]}');");
					$A['reservcancel'] = COM_createLink($iconimg,$url,$attr);
					//
					$data_arr[] = $A;
                }
            }
            $retval .= ADMIN_simpleList("", $header_arr, $text_arr, $data_arr);
        }
    }
    return $retval;
}

// +---------------------------------------------------------------------------+
// | 機能  予約送信削除                                                        |
// | 書式 fncReservDelete ()                                                   |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
function fncReservDelete ()
{
    global $_CONF, $_TABLES, $LANG_ASSIST_ADMIN;
    $retval = ''; $err='';
    $id = COM_applyFilter ($_REQUEST['id'], true);
    if (empty($id) || !is_numeric($id)) {
        $err.=$LANG_ASSIST_ADMIN['err_reservcancel_no_id']."<br/>".LB;
    }
    if ($err <> '') {
        return $err;
    }

    // file delete
    $file = "{$_CONF['path_data']}assist_reserv_{$id}";
    if (file_exists($file)) {
        unlink ($file);
    } else {
        return $LANG_ASSIST_ADMIN['err_reservcancel_no_file']."<br/>".LB;
    }
    // DB delete
    DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_fn_$id'");
    DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_rt_$id'");
    DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_sp_$id'");
    DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_si_$id'");
    DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_uf_$id'");
	DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_ut_$id'");	

	
	DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_ib_$id'");
	DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_os_$id'");
	DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_te_$id'");
	DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_sg_$id'");
	DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_bm_$id'");
	DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_bc_$id'");
	DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_li_$id'");

    return $LANG_ASSIST_ADMIN['done_reservcancel']."<br/>".LB;
}
// hiroron end 2010/07/15



// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
// 引数
if (isset ($_REQUEST['mode'])) {
    $mode = COM_applyFilter ($_REQUEST['mode'], false);
}
if (($mode == $LANG_ADMIN['save']) && !empty ($LANG_ADMIN['save'])) { // save
    $mode="save";
}else if (($mode == $LANG_ADMIN['delete']) && !empty ($LANG_ADMIN['delete'])) {
    $mode="delete";

}else if (($mode == $LANG_ASSIST_ADMIN['mail_test']) && !empty ($LANG_ASSIST_ADMIN['mail_test'])) {
    $mode="test";
}else if (($mode == $LANG_ASSIST_ADMIN['mail_send']) && !empty ($LANG_ASSIST_ADMIN['mail_send'])) {
    $mode="send";
// hiroron start 2010/07/13
}else if (($mode == $LANG_ASSIST_ADMIN['mail_reserv']) && !empty ($LANG_ASSIST_ADMIN['mail_reserv'])) {
    $mode="reserv";
// hiroron end 2010/07/13

}


//-----

if (isset ($_REQUEST['uidfrom'])) {
    $uidfrom = COM_applyFilter ($_REQUEST['uidfrom'], true);
}
if (isset ($_REQUEST['to'])) {
    $uidto = COM_applyFilter ($_REQUEST['uidto'], true);
}


//echo "mode=".$mode."<br>";
if ($mode=="" ) {
}else{
    if (!SEC_checkToken()){
 //    if (SEC_checkToken()){//テスト用
        COM_accessLog("User {$_USER['username']} tried to illegally and failed CSRF checks.");
        echo COM_refresh($_CONF['site_admin_url'] . '/index.php');
        exit;
    }
}

//メルマガプラグインがあれば、そのメンバーに送信
$wkymlmguserflg=false;
if (in_array("wkymlmguser", $_PLUGINS)){
    $wkymlmguserflg=true;
}

$menuno=4;
$display = '';
$display .=ppNavbarjp($navbarMenu,$LANG_ASSIST_admin_menu[$menuno]);

$information = array();
$information['what']='menu';
$information['rightblock']=false;
$information['pagetitle']=$LANG_ASSIST_ADMIN['piname'];

switch ($mode) {
    case 'save':// 保存
        $message = fncSave ($edt_flg);
        $display .= fncEdit($message,$wkymlmguserflg);
        break;
    case 'send':// 送信
        $message .= fncSave ($mode);
        if ($message===$LANG_ASSIST_ADMIN['mail_save_ok']) {
            $message= fncsendmail("send",$uidfrom,$uidto,$wkymlmguserflg);
        }
        $display .= fncEdit($message,$wkymlmguserflg);
        break;
    case 'test':// test送信
        $message .= fncSave ($mode);
        if ($message===$LANG_ASSIST_ADMIN['mail_save_ok']) {
            $message= fncsendmail("test");
        }
        $display .= fncEdit($message,$wkymlmguserflg);
        break;
// hiroron start 2010/07/13
    case 'reserv': // 送信予約
        $message .= fncSave ($mode);
        $display .= fncEdit($message,$wkymlmguserflg);
        break;
// hiroron end 2010/07/13
// hiroron start 2010/07/15
    case 'reservcancel': // 予約送信キャンセル
        $message .= fncReservDelete();
// hiroron end 2010/07/15

    default:// 初期表示、一覧表示
        $page_title=$LANG_ASSIST_ADMIN['piname'];
        $display.=fncEdit("",$wkymlmguserflg);
}

//FOR GL2.0.0 
if (COM_versionCompare(VERSION, "2.0.0",  '>=')){
	$display = COM_createHTMLDocument($display,$information);
}else{
	$display = COM_siteHeader ($information['what'], $information['pagetitle']).$display;
	$display .= COM_siteFooter($information['rightblock']);
}

COM_output($display);

?>