<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | csv maintenannce
// +---------------------------------------------------------------------------+
// $Id: csv.inc

define ('THIS_SCRIPT', 'databox/csv.php');
//define ('THIS_SCRIPT', 'databox/test.php');

require_once( $_CONF['path_system'] . 'lib-admin.php' );

//
require_once($_CONF['path'] . 'plugins/databox/csv/csv_functions.inc');
require_once($_CONF['path'] . 'plugins/databox/csv/csv_importexec.inc');

//log 出力モード設定　0:作成しない,1:ファイルに出力
$logmode =1;
$logfile = $_CONF['path_log'] . 'databox_csvimport.log';

// +---------------------------------------------------------------------------+
// | 一覧取得 ADMIN_list 経由で使用
// +---------------------------------------------------------------------------+
function fncGetListField($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF;

    $pi_name="databox";

    $retval = '';

    switch($fieldname) {
        //編集アイコン
        case 'editid':
            $retval = "<a href=\"{$_CONF['site_admin_url']}";
            $retval .= "/plugins/".THIS_SCRIPT;
            $retval .= "?mode=edit";
            $retval .= "&amp;seq={$A['seq']}\">";
            $retval .= "{$icon_arr['edit']}</a>";
            break;
        case 'field2':
            $field_ary=array();
            fnc_GetFieldAry1($pi_name,$field_ary);
            fnc_GetFieldAry2($pi_name,$field_ary);
            $retval = $field_ary[$fieldvalue];
            break;
        case 'seleditid':
            $retval = "<a href=\"{$_CONF['site_admin_url']}";
            $retval .= "/plugins/".THIS_SCRIPT;
            $retval .= "?mode=seledit";
            $retval .= "&amp;id={$A['csv_sel_id']}\">";
            $retval .= "{$icon_arr['edit']}</a>";
            break;
        case 'seleditseq':
            $retval = "<a href=\"{$_CONF['site_admin_url']}";
            $retval .= "/plugins/".THIS_SCRIPT;
            $retval .= "?mode=seldtledit";
            $retval .= "&amp;id={$A['csv_sel_id']}";
            $retval .= "&amp;seq={$A['seq']}\">";
            $retval .= "{$icon_arr['edit']}</a>";
            break;

        //各項目
        default:
            $retval = $fieldvalue;
            break;
    }

    return $retval;

}
function fncOutBox(
    $pi_name
    ,$outfrd
    ,$defcsv
    )
{
    global $_TABLES;
    
    $retval="";
    $filename="databox_".date("YmdHis").".csv";
    $filename = $outfrd."/".$filename;  //  ファイル名
    
    //file output open
    //$outfile = tempnam($_CONF['path_data'] ."tmp", $filenm);
    $file = @fopen( $filename, 'w' );
    if ( $file === false ) {
        $retval .= "ERR! ".$filename ." is not writable!<br />" . LB;
        return $retval;
    }
    $cnt=count($defcsv);
    
    //-----
    $tbl1=$_TABLES['DATABOX_base'];
    $tbl21=$_TABLES['DATABOX_addition'];
    $tbl22=$_TABLES['DATABOX_def_field'];
    $tbl23=$_TABLES['DATABOX_mst'];
    $tbl31=$_TABLES['DATABOX_category'];
    $tbl32=$_TABLES['DATABOX_def_category'];
    $tbl4=$_TABLES['DATABOX_def_csv'];
    //-----
    $sql = "SELECT ".LB;
    $sql .= " * ".LB;
    $sql .= " FROM ".LB;
    $sql .= " {$tbl1} AS t1 ".LB;

    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);
    
    if ($numrows > 0) {
        //-----First line 1行目
        $wary=array();
        for ($j = 0; $j < $cnt; $j++) {
            $wary[$j]=$defcsv[$j]['csvheader'];
        }
		fputcsv($file,$wary);
         
        //-----Second and subsequent lines　2行目以降
        $wary=array();
        for ($i = 0; $i < $numrows; $i++) {
            $A = DB_fetchArray ($result);
			$A = array_map('stripslashes', $A);
            $id=COM_applyFilter($A['id']);
            $wtime=time();

            $w="";
            for ($j = 0; $j < $cnt; $j++) {
            
                $csvheader=$defcsv[$j]['csvheader'];
                $value=    $defcsv[$j]['value'];
                $type =    $defcsv[$j]['type'];
                $field=    $defcsv[$j]['field'];
                $csvheader=ltrim(rtrim($csvheader));
				if  ($csvheader=="") {
					continue;
				}
                $wk="";
                //base項目
                if ($type==="base"){
                    $wk =$A[$field];
                }else if ($type==="addition" ){
                    $wk=DB_getItem($tbl21,"value","id=$id AND field_id=$field");
                }else if ($type==="addition_v" ){
                    $wk=DB_getItem(
                        $tbl21." AS t1 ,".$tbl22." AS t2 ,".$tbl23." AS t3 "
                        ,"t3.value"
                        ,"t1.id=$id AND t1.field_id=$field AND t1.field_id=t2.field_id AND t3.kind=t2.selectlist AND t1.value =t3.no");
                }else{
                    $sql3 = "SELECT ".LB;
                    $sql3 .= " t4.value ".LB;
                    $sql3 .= " FROM ".LB;
                    $sql3 .= " {$tbl31} AS t1 ".LB;
                    $sql3 .= " ,{$tbl32} AS t2 ".LB;
                    $sql3 .= " ,{$tbl4} AS t4 ".LB;
                    $sql3 .= " WHERE t1.id={$id} ".LB;
                    $sql3 .= " AND t1.category_id={$field}".LB;
                    $sql3 .= " AND t2.category_id=t1.category_id".LB;
                    $sql3 .= " AND t4.field=CONCAT('category:',t2.category_id)".LB;
                    $result3 = DB_query ($sql3);
                    $numrows3 = DB_numRows ($result3);
                    if ($numrows3 > 0) {
                        $A3 = DB_fetchArray ($result3);
                        $wk=COM_applyFilter($A3['value']);
                    }
                }    
                $wary[$j]=addslashes($wk);
            }
		    fputcsv($file,$wary);
        }

    }
    fclose($file);


    $rt="DataBox ".$numrows." out(".$filename.")";
    return $rt;

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
if (isset ($_REQUEST['mode2'])) {
    $mode2 = COM_applyFilter ($_REQUEST['mode2'], false);
}
$msg = '';
if (isset ($_REQUEST['msg'])) {
    $msg = COM_applyFilter ($_REQUEST['msg'], true);
}
$seq = '';
if (isset ($_REQUEST['seq'])) {
    $seq = COM_applyFilter ($_REQUEST['seq'], true);
}
$id = '';
if (isset ($_REQUEST['id'])) {
    $id = COM_applyFilter ($_REQUEST['id'], true);
}

$old_mode="";
if (isset($_REQUEST['old_mode'])) {
    $old_mode = COM_applyFilter($_REQUEST['old_mode'],false);
    if ($mode==$LANG_ADMIN['cancel']) {
        $mode = $old_mode;
    }
}

if (($mode == $LANG_ADMIN['save']) && !empty ($LANG_ADMIN['save'])) { // save
    if  ($mode2=="select"){
        $mode="selsave";
    }else if ($mode2=="selectdtl"){
        $mode="seldtlsave";
    }else{
        $mode="save";
    }
}else if (($mode == $LANG_ADMIN['delete']) && !empty ($LANG_ADMIN['delete'])) {
    if  ($mode2=="select"){
        $mode="seldelete";
    }else if  ($mode2=="selectdtl"){
        $mode="seldtldelete";
    }else{
        $mode="delete";
	}
}else if (($mode == $LANG_ADMIN['cancel']) && !empty ($LANG_ADMIN['cancel'])) {
    if  ($mode2=="select"){
        $mode="sellist";
    }else if  ($mode2=="selectdtl"){
        $mode="seledit";
    }else{
        $mode="";
	}
}
if ($action == $LANG_ADMIN['cancel'])  { // cancel
    $mode="";
}

//---
$sql="show tables like '{$_TABLES['DATABOX_def_csv']}'" ;

$rt=DB_query($sql);
$rt=DB_numRows($rt);

if  ($rt===0){
    if ($mode<>"initexec"){
        $mode="init";
    }
}
//echo "mode=".$mode."<br>";
if ($mode=="" OR $mode=="edit" OR $mode=="new" OR $mode=="drafton" OR $mode=="draftoff"
    OR $mode=="init" OR $mode=="list"
    OR $mode=="exportform" OR $mode=="importform"
    OR $mode=="sellist" OR $mode=="seledit" OR $mode=="selnew" OR $mode=="selimportform"
    OR $mode=="seldtledit" OR $mode=="seldtlnew"
    OR $mode=="csvscheduled"
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
$menuno=9;

$display = '';
$information = array();

$display.=ppNavbarjp($navbarMenu,$LANG_DATABOX_admin_menu[$menuno]);
//$display.="mode=".$mode."<br>";
switch ($mode) {
    case 'export':// エクスポート実行
        $information['pagetitle']=$LANG_databox_ADMIN['piname'].$LANG_databox_ADMIN['export'];
        $display .= fnc_Menu($pi_name);
        $display .= fnc_exportexec($pi_name);
        break;
    case 'import':// インポート実行
        $information['pagetitle']=$LANG_databox_ADMIN['piname'].$LANG_databox_ADMIN['import'];
        $display .= fnc_Menu($pi_name);
        if  (COM_applyFilter ($_REQUEST['unlink'], true)==1){
            $unlink="unlink";
        }
        $display .= databox_importexec($pi_name,$unlink);
        break;
    case 'new':// 新規登録
        $information['pagetitle']=$LANG_databox_ADMIN['piname'].$LANG_databox_ADMIN['new'];
        $display .= fnc_Menu($pi_name);
        $display .= fnc_Edit($pi_name,"",$msg);
        break;
    case 'save':// 保存
        $display .= fnc_Save ($pi_name,$navbarMenu,$menuno);
        break;
    case 'delete':// 削除
        $display .= fnc_delete($pi_name);
        break;
    case 'edit':// 編集
        if (!empty ($seq) ) {
            $information['pagetitle']=$LANG_databox_ADMIN['piname'].$LANG_databox_ADMIN['edit'];
            $display .= fnc_Menu($pi_name);
            $display .= fnc_Edit($pi_name,$seq,$msg);
        }
        break;
    case 'exportform':// エクスポート　画面
        $information['pagetitle']=$LANG_databox_ADMIN['piname'].$LANG_databox_ADMIN[$mode];
        $display .= fnc_Menu($pi_name);
        $display .= fnc_importexport($pi_name,$mode);
        break;
    case 'init'://初期化 Confirmation
        $information['pagetitle']=$LANG_databox_ADMIN['piname'];
        $display .= fnc_Menu($pi_name);
        $display .= DATABOX_Confirmation($pi_name,$mode);
        break;
    case 'initexec'://初期化実行
        $information['pagetitle']=$LANG_databox_ADMIN['piname'];
        $display .= fnc_Menu($pi_name);
        $display .= fnc_init($pi_name);
        break;
    case 'sellist'://抽出条件一覧
        $information['pagetitle']=$LANG_databox_ADMIN['piname'];
        if (isset ($msg)) {
            $display .= COM_showMessage ($msg,$pi_name);
        }
        $display .= fnc_Menu($pi_name);
        $display .= fnc_selList($pi_name);
        break;
    case 'selnew':// 抽出条件新規登録
        $information['pagetitle']=$LANG_databox_ADMIN['piname'].$LANG_databox_ADMIN['new'];
        $display .= fnc_Menu($pi_name);
        $display .= fnc_selEdit($pi_name,"",$msg);
        break;
    case 'selsave':// 保存
        $retval= fnc_selSave ($pi_name,$navbarMenu,$menuno);
        $information['pagetitle']=$retval['title'];
        $display.=$retval['display'];
        break;
    case 'seldelete':// 削除
        $retval= fnc_seldelete($pi_name);
        $information['pagetitle']=$retval['title'];
        $display.=$retval['display'];
        break;
    case 'seledit':// 編集
        if (!empty ($id) ) {
            $information['pagetitle']=$LANG_databox_ADMIN['piname'].$LANG_databox_ADMIN['edit'];
            $display .= fnc_Menu($pi_name);
            $display .= fnc_selEdit($pi_name,$id,$msg);
        }
        break;
    case 'seldtlnew':// 抽出条件明細新規登録
        if (!empty ($id) ) {
            $information['pagetitle']=$LANG_databox_ADMIN['piname'].$LANG_databox_ADMIN['new'];
            $display .= fnc_Menu($pi_name);
            $display .= fnc_seldtlEdit($pi_name,$id,"",$msg);
        }
        break;
    case 'seldtlsave':// 抽出条件明細保存
        $retval= fnc_seldtlSave ($pi_name,$navbarMenu,$menuno);
        $information['pagetitle']=$retval['title'];
		$display.=$retval['display'];
        break;
    case 'seldtldelete':// 抽出条件明細削除
        $retval= fnc_seldtldelete($pi_name);
        $information['pagetitle']=$retval['title'];
        $display.=$retval['display'];
        break;
    case 'seldtledit':// 編集
        if (!empty ($id) ) {
            $information['pagetitle']=$LANG_databox_ADMIN['piname'].$LANG_databox_ADMIN['edit'];
            $display .= fnc_Menu($pi_name);
            $display .= fnc_seldtlEdit($pi_name,$id,$seq,$msg);
        }
        break;
    case 'importform':// インポート　画面
        $information['pagetitle']=$LANG_databox_ADMIN['piname'].$LANG_databox_ADMIN[$mode];
        $display .= fnc_Menu($pi_name);
        $display .= fnc_importform($pi_name);
        break;
    case 'csvscheduledupdate':
        $dummy= fnc_Scheduledupdate($pi_name);
    case 'csvscheduled'://CSV 更新 疑似CRON
        $information['pagetitle']=$LANG_databox_ADMIN['piname'];
        $display .= fnc_Menu($pi_name);
        $display .= fnc_Scheduled($pi_name);
        break;
    case 'list':// 一覧
    default:// 初期表示
        $information['pagetitle']=$LANG_databox_ADMIN['piname'];
        if (isset ($msg)) {
            $display .= COM_showMessage ($msg,$pi_name);
        }
        $display .= fnc_Menu($pi_name);
        $display .= fnc_List($pi_name);
}
$display=COM_startBlock($LANG_DATABOX_ADMIN['piname'],''
             ,COM_getBlockTemplate('_admin_block', 'header'))
         .$display
         .COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

$display=DATABOX_displaypage($pi_name,'_admin',$display,$information);

COM_output($display);

?>
