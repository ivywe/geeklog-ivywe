<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | xml maintenannce
// +---------------------------------------------------------------------------+
// $Id: xml.inc

define ('THIS_SCRIPT', 'databox/xml.php');
//define ('THIS_SCRIPT', 'databox/test.php');

include_once( $_CONF['path_system'] . 'lib-admin.php' );

//
include_once($_CONF['path'] . 'plugins/databox/xml/xml_functions.inc');
include_once($_CONF['path'] . 'plugins/databox/xml/xml_databox_functions.inc');

//log 出力モード設定　0:作成しない,1:ファイルに出力
$logmode =1;
$logfile = $_CONF['path_log'] . 'databox_xmlimport.log';

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
    ,$defxml
    )
{
    global $_TABLES;

    //-----
    $tbl1=$_TABLES['DATABOX_base'];
    $tbl21=$_TABLES['DATABOX_addition'];
    $tbl22=$_TABLES['DATABOX_def_field'];
    $tbl23=$_TABLES['DATABOX_mst'];
    $tbl31=$_TABLES['DATABOX_category'];
    $tbl32=$_TABLES['DATABOX_def_category'];
    //-----
    $sql = "SELECT ".LB;
    $sql .= " * ".LB;
    $sql .= " FROM ".LB;
    $sql .= " {$tbl1} AS t1 ".LB;

    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);
    if ($numrows > 0) {
        for ($i = 0; $i < $numrows; $i++) {
            $A = DB_fetchArray ($result);

            $id=COM_applyFilter($A['id']);
            $code=COM_applyFilter($A['code']);
            $idcode=$id;
            if ($code<>""){
                $idcode.="_".$code;
            }
               
            $wtime=time();
            $filename = $outfrd."/".$wtime.'_databox_'.$idcode.'.xml';  //  ファイル名

            $dom = new DomDocument('1.0',"UTF-8");  // DOMを作成
            $dom->encoding = "UTF-8"; // 文字コードをUTF-8に
            $dom->formatOutput = true; // 出力XMLを整形(改行,タブ)する

            //ADD ROOT
            $ROOTData = $dom->appendChild($dom->createElement('ROOT'));

            //ADD ROOT_TAG
            $cnt=count($defxml);
            for ($j = 0; $j < $cnt; $j++) {

                $tag=    $defxml[$j]['tag'];
                $value=  $defxml[$j]['value'];
                $type =  $defxml[$j]['type'];
                $field=  $defxml[$j]['field'];
                $tag=ltrim(rtrim($tag));
                //$tag=mb_convert_encoding($tag, "UTF-8", "UTF-8");
                //$tag=mb_convert_kana($tag,"a");
                //base項目
                if ($type==="base"){
                    $value=COM_applyFilter($A[$field]);
                         $FIELDData= $ROOTData->appendChild($dom->createElement($tag));
                         $FIELDData->appendChild($dom->createTextNode($value));
                    }else if ($type==="addition" ){
                         $value=DB_getItem($tbl21,"value","id=$id AND field_id=$field");
                         $FIELDData= $ROOTData->appendChild($dom->createElement($tag));
                         $FIELDData->appendChild($dom->createTextNode($value));
                    }else if ($type==="addition_v" ){
                         $value=DB_getItem(
                              $tbl21." AS t1 ,".$tbl22." AS t2 ,".$tbl23." AS t3 "
                              ,"t3.value"
                              ,"t1.id=$id AND t1.field_id=$field AND t1.field_id=t2.field_id AND t3.kind=t2.selectlist AND t1.value =t3.no");
                         $FIELDData= $ROOTData->appendChild($dom->createElement($tag));
                         $FIELDData->appendChild($dom->createTextNode($value));
                    }else{
                         $sql3 = "SELECT ".LB;
                         $sql3 .= " t2.name ".LB;
                         $sql3 .= " FROM ".LB;
                         $sql3 .= " {$tbl31} AS t1 ".LB;
                         $sql3 .= " ,{$tbl32} AS t2 ".LB;                                                            
                         $sql3 .= " WHERE t1.id={$id} ".LB;
                         $sql3 .= " AND t1.category_id={$field}".LB;
                         $sql3 .= " AND t2.category_id=t1.category_id".LB;
                         
                         $result3 = DB_query ($sql3);
                         $numrows3 = DB_numRows ($result3);
                         if ($numrows3 > 0) {
                              $A3 = DB_fetchArray ($result3);
                              $value=COM_applyFilter($A3['name']);
                              $FIELDData= $ROOTData->appendChild($dom->createElement($tag));
                              $FIELDData->appendChild($dom->createTextNode($value));
                         }
                    }     
            }

            $dom->formatOutput = true; // set the formatOutput attribute of
                                       // domDocument to true
            // DomXMLをXML形式で出力
            $rt=$dom->saveXML();
            $dom->save($filename); // save as file
            //$dom->saveXML($filename);

        }
    }

    $rt="DataBox ".$numrows." out(".$wtime.")";
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
$sql="show tables like '{$_TABLES['DATABOX_def_xml']}'" ;

$rt=DB_query($sql);
$rt=DB_numRows($rt);

if  ($rt===0){
     if ($mode<>"initexec"){
          $mode="init";
     }
}
//echo "mode=".$mode."<br>";
if ($mode=="" OR $mode=="edit" OR $mode=="new" 
    OR $mode=="init" OR $mode=="list" 
     OR $mode=="exportform" OR $mode=="importform"
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
$menuno=8;
$display = '';

$display = '';
$information = array();
//$information['what']='menu';
//$information['rightblock']=false;

$display.=ppNavbarjp($navbarMenu,$LANG_DATABOX_admin_menu[$menuno]);


switch ($mode) {
    case 'export':// エキスポート実行
        $information['pagetitle']=$LANG_databox_ADMIN['piname'].$LANG_databox_ADMIN['export'];
        $display .= fnc_Menu($pi_name);
        $display .= fnc_exportexec($pi_name);
        break;

    case 'import':// インポート実行
        $information['pagetitle']=$LANG_databox_ADMIN['piname'].$LANG_databox_ADMIN['import'];
        $display .= fnc_Menu($pi_name);
        $display .= fnc_importexec($pi_name);
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
    case 'importform':// インポート　画面
        $information['pagetitle']=$LANG_databox_ADMIN['piname'].$LANG_databox_ADMIN[$mode];
        $display .= fnc_Menu($pi_name);
        $display .= fnc_importexport($pi_name,$mode);
        break;

    case 'init'://初期化 Confirmation
        $information['pagetitle']=$LANG_databox_ADMIN['piname'];
        $display .= fnc_Menu($pi_name);
        $display .= DATABOX_Confirmation($pi_name,$mode);
        break;
    case 'initexec'://初期化
        $information['pagetitle']=$LANG_databox_ADMIN['piname'];
        $display .= fnc_Menu($pi_name);
        $display .= fnc_init($pi_name);
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
