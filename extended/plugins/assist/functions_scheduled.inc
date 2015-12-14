<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// 2010/07/13 hiroron AT hiroron DOT com
// 2011/01/04 update tsuchitani AT ivywe DOT co DOT jp

if (strpos ($_SERVER['PHP_SELF'], 'functions_scheduled.inc') !== false) {
    die ('This file can not be used on its own.');
}

include_once $_CONF['path_system'] . 'lib-user.php';

// +----------------------------------+
// | ASSIST スケジュール実行専用関数  |
// +----------------------------------+

// 予約送信がセットされているかチェック
//   セットされていたらその中で一番過去の予約のファイル名を返す
function assist_scheduled_reserv_check ()
{
    global $_CONF;

    $reserv='';
    $files = glob("{$_CONF['path_data']}assist_reserv_*",  GLOB_NOCHECK);
    if (is_array($files)) {
        if (count($files) > 0 && $files[0] != "{$_CONF['path_data']}assist_reserv_*") {
            foreach ($files as $file) {
                $filename = basename($file);
                $aname = explode('_', $filename);
                if (!empty($aname[2]) && ($reserv === '' || $reserv > $aname[2])) {
                    $reserv = $aname[2];
                }
            }
        }
    }
    return $reserv;
}

// 予約送信ファイル名のものを処理
//20111220 update
function assist_scheduled_reserv_sendmail ($file)
{
	global $_CONF;
	global $_TABLES;
	global $LANG_ASSIST_ADMIN;
	global $_PLUGINS;
	
	//COM_errorLog("[ASSIST] file={$file} assist_scheduled_reserv_sendmail execute");//@@@@@
    if (!file_exists($file)) {
        COM_errorLog("[ASSIST] reserv_sendmail: Not Reserv File! file={$file}");
        return;
    }
    $filename = basename($file);
    $aname = explode('_', $filename);
    if (empty($aname[2])) {
        COM_errorLog("[ASSIST] reserv_sendmail: Not Reserv Datetime! filename={$filename}");
        return;
    } else {
       unlink ($file);
	}

    $ts = $aname[2];
	
	if( function_exists( "LIB_OutLog" )) {
	}else{
		require_once ($_CONF['path'] . 'plugins/assist/lib/lib_outlog.php');
	}

    // admin/mail.php の fncsendmail() を参考に作成
	
	//@@@@@20110721add---->
	$fary=array();
	$fary[]=array('name' =>'fromname'   ,'reserv' =>'fn' );//@@
    $fary[]=array('name' =>'replyto'    ,'reserv' =>'rt');
    $fary[]=array('name' =>'sprefix'    ,'reserv' =>'sp');
    $fary[]=array('name' =>'sid'        ,'reserv' =>'si');
    $fary[]=array('name' =>'uidfrom'    ,'reserv' =>'uf');
    $fary[]=array('name' =>'uidto'      ,'reserv' =>'ut');
	$fary[]=array('name' =>'introbody'  ,'reserv' =>'ib');//@@
    $fary[]=array('name' =>'overstyr'   ,'reserv' =>'os');//@@
    $fary[]=array('name' =>'toenv'      ,'reserv' =>'te');//@@
	$fary[]=array('name' =>'selectgroup','reserv' =>'sg');//@@
		
	$fary[]=array('name' =>'bulkmm','reserv' =>'bm');//@@
	$fary[]=array('name' =>'bulkcnt','reserv' =>'bc');//@@
	
	$fary[]=array('name' =>'lastid','reserv' =>'li');//@@
	//@@@@@20110721add<-----

	$html = false ;    // If you want to send html mail
	
	$test_on=false;//
	//$test_on=true;//@@@@@テスト中  送信しない
	
	$logmode =1;
	if ($test_on){
		$logfile = $_CONF['path_log'] . 'assist_newsletter2.log';
        COM_errorLog("[ASSIST] reserv_sendmail: TEST MODE");
	}else{
		$logfile = $_CONF['path_log'] . 'assist_newsletter.log';
	}
	
	$ts_date=date('Y-m-d H:i', $ts);
	$logentry= "== {$ts_date} reservation start == ";
	$dummy = LIB_OutLog( $logentry, $logfile, $logmode);

    $fromname = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_fn_$ts'" );
    $fromname = COM_stripslashes($fromname);
    $replyto = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_rt_$ts'" );
    $replyto = COM_stripslashes($replyto);
    $sprefix = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_sp_$ts'" );
    $sprefix = COM_stripslashes($sprefix);
    $sid = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_si_$ts'" );
    $sid = COM_stripslashes($sid);
    $uidfrom = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_uf_$ts'" );
    $uidfrom = COM_stripslashes($uidfrom);
    $uidto = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_ut_$ts'" );
    $uidto = COM_stripslashes($uidto);
    //@@@@@$introbody = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_introbody'" );
    //@@@@@$introbody = COM_stripslashes($introbody);
    //@@@@@$overstyr = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_overstyr'" );
    //@@@@@$overstyr = COM_stripslashes($overstyr);
	
	//@@@@@20110721add----->
    $introbody = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_ib_$ts'" );
    $introbody = COM_stripslashes($introbody);
	
    $overstyr = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_os_$ts'" );
    $overstyr = COM_stripslashes($overstyr);
	
    $toenv = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_te_$ts'" );
    $toenv = COM_stripslashes($toenv);
	
    $selectgroup = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_sg_$ts'" );
    $selectgroup = COM_stripslashes($selectgroup);
		
    $bulkmm = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_bm_$ts'" );
    $bulkmm = COM_stripslashes($bulkmm);
	
    $bulkcnt = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_bc_$ts'" );
    $bulkcnt = COM_stripslashes($bulkcnt);
	
    $lastid = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_li_$ts'" );
    $lastid = COM_stripslashes($lastid);
	//@@@@@20110721add<------

    $from = COM_formatEmailAddress ($fromname, $replyto);
    $subject = DB_getItem($_TABLES['stories'] ,"title" ,"sid='{$sid}'");
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
	$wkymlmguserflg=false;
	if (in_array("wkymlmguser", $_PLUGINS)){
		$wkymlmguserflg=true;
    }

	//20110721 SQL修正
	$sql="SELECT DISTINCT t1.uid ,t1.email FROM ";
	
	//メルマガユーザか選択されたグループの登録ユーザか
	if ($selectgroup==="99999"){
		if ($wkymlmguserflg){
			$sql.=$_TABLES['wkymlmguser'] ." AS t1 ".LB;
			if ($bulkmm==0){
				if ($uidto<>"0"){
					$sql.=" where ".LB;
					$sql.=" (t1.uid between ".$uidfrom ." and " .$uidto.")" .LB;
				}
			}else{
				if ($uidto=="0"){
					$sql.=" where ".LB;
					$sql.="  (t1.uid > ".$lastid .")" .LB;
				}else{
					$nextid=$lastid + 1;
					if  ($nextid < $uidfrom){
						$nextid=$uidfrom;
					}
					$sql.=" where ".LB;
					$sql.="  (t1.uid between ".$nextid ." and " .$uidto.")" .LB;
				}
			}
			
			
			
			
			
		}else{
			$err="メルマガプラグインが有効ではありません";
			$logentry= "  {$ts_date} wkymlmguser Not Enabled ";
			$dummy = LIB_OutLog( $logentry, $logfile, $logmode);
	
			return $err;
		}
    }else{
		
		$childgroups=USER_getChildGroups($selectgroup);
		$groupList = implode(',', $childgroups);
//$logentry= "grouplist={$groupList}  ";
//$dummy = LIB_OutLog( $logentry, $logfile, $logmode);
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
		
		if ($bulkmm==0){
			if ($uidto<>"0"){
				$sql.=" AND (t1.uid between ".$uidfrom ." and " .$uidto.")" .LB;
			}
		}else{
			if ($uidto=="0"){
				$sql.=" AND (t1.uid > ".$lastid .")" .LB;
			}else{
				$nextid=$lastid + 1;
				if  ($nextid < $uidfrom){
					$nextid=$uidfrom;
				}
				$sql.=" AND (t1.uid between ".$nextid ." and " .$uidto.")" .LB;
			}
		}
	}
    //---

    $sql.=" order by uid ".LB;
	if ($bulkmm<>0){
		if ($bulkcnt<>0){
			$sql .= " LIMIT 0, $bulkcnt";
		}
	}
//$logentry= "sql={$sql}  ";
//$dummy = LIB_OutLog( $logentry, $logfile, $logmode);

    $result = DB_query($sql);
	$lastid=0;
	if ($result !== false) {
        $result = DB_query($sql);
        $nrows = DB_numRows($result);
        for ($i = 0; $i < $nrows; $i++) {
            $A = DB_fetchArray ($result);
            $to = $A['email'];
			
			if ($test_on){
				$lastid=$A['uid'];
                $logentry= "  Send reservation test uid:{$A['uid']} mail:{$A['email']}";
                $dummy = LIB_OutLog( $logentry, $logfile, $logmode);
			}else{
				if (!COM_mail ($to, $subject, $message, $from, $html, $priority)) {
					$failures[] = htmlspecialchars ($to);
					$logentry= "  Send reservation NG uid:{$A['uid']} mail:{$A['email']}";
					$dummy = LIB_OutLog( $logentry, $logfile, $logmode);
				} else {
					$successes[] = htmlspecialchars ($to);
					$logentry= "  Send reservation OK uid:{$A['uid']} mail:{$A['email']}";
					$dummy = LIB_OutLog( $logentry, $logfile, $logmode);
					$lastid=$A['uid'];
				}
			}
        }
    }

    $failcount = count ($failures);
    $successcount = count ($successes);
    $retval=$LANG_ASSIST_ADMIN['mail_send_success']."=".$successcount.$LANG_ASSIST_ADMIN['mail_send_failure']."=".$failcount;

    //COM_errorLog("[ASSIST] reserv_sendmail($ts): {$retval} (".date('Y-m-d H:i', $ts)." {$uidfrom}->{$uidto})");

    // DB delete
    DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_fn_$ts'");
    DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_rt_$ts'");
    DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_sp_$ts'");
    DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_si_$ts'");
    DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_uf_$ts'");
    DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_ut_$ts'");
	
	DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_ib_$ts'");//@@@@@
	DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_os_$ts'");//@@@@@
	DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_te_$ts'");//@@@@@
	DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_sg_$ts'");//@@@@@
	DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_bm_$ts'");//@@@@@
	DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_bc_$ts'");//@@@@@
	DB_query ("DELETE FROM {$_TABLES['vars']} WHERE name = 'assist_li_$ts'");//@@@@@
	
	$logentry= "  {$ts_date} reservation end ";
	$dummy = LIB_OutLog( $logentry, $logfile, $logmode);
	
	//一括予約
	if ($bulkmm<>0){
		if ($uidto==0){
			if ($selectgroup==="99999"){
				$maxid=DB_getItem( $_TABLES['wkymlmguser'], 'max(uid)' );
			}else{
				$maxid=DB_getItem( $_TABLES['users'], 'max(uid)' );
			}
		}else{
			$maxid=$uidto;
		}
		
		if ($lastid<>0 AND $lastid<$maxid){
			//@@@@@20111220update---->
			//@@@@@ $w = DB_getItem( $_TABLES['vars'], 'value', "name = 'assist_last_schedule'" );
			$w = time();//UNIX_TIMESTAMP();
			//20111220update<----
			$ts = $w + ($bulkmm * 60);
			
			$ts_date=date('Y-m-d H:i', $ts);
			$logentry= "  Next reservation  {$ts_date} ";
			$dummy = LIB_OutLog( $logentry, $logfile, $logmode);
			//COM_errorLog("[ASSIST] Next reservation  {$ts_date}");//@@@@@

			//assist_re_datetime
			$values="'assist_re_datetime'";
			$values.=",'$ts'";
			DB_save($_TABLES['vars'],$fields,$values);
		
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
			$values.=",'{$lastid}'";
			DB_save($_TABLES['vars'],$fields,$values);
			touch($_CONF['path_data']."assist_reserv_$ts");
		}
	}

}

?>
