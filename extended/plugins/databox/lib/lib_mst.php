<?php

if (strpos ($_SERVER['PHP_SELF'], 'lib_mst.php') !== false) {
    die ('This file can not be used on its own.');
}


function LIB_List(
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能  一覧表示
// | 書式 LIB_List($pi_name)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:一覧
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $_TABLES;
    global $LANG_ADMIN;
    global $LANG09;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $lang_box="LANG_".strtoupper($pi_name);
    global $$lang_box;
    $lang_box=$$lang_box;

    $table=$_TABLES[strtoupper($pi_name).'_mst'];

    $retval = '';

    //フィルタ filter
    if (!empty ($_GET['filter_val'])) {
        $filter_val = COM_applyFilter($_GET['filter_val']);
    } elseif (!empty ($_POST['filter_val'])) {
        $filter_val = COM_applyFilter($_POST['filter_val']);
    } else {
        $filter_val = $LANG09[9];
    }
    if  ($filter_val==$LANG09[9]){
        $exclude="";
    }else{
        $exclude=" AND kind='{$filter_val}'";
    }

    $filter = "{$lang_box_admin['kind']}:";
    $filter .="<select name='filter_val' style='width: 125px' onchange='this.form.submit()'>";
    $filter .="<option value='{$LANG09[9]}'";

    if  ($filter_val==$LANG09[9]){
        $filter .=" selected='selected'";
    }
    $filter .=" >{$LANG09[9]}</option>";
    $filter .= COM_optionList ($table
                , 'DISTINCT kind,kind,kind', $filter_val,2,"");
    $filter .="</select>";

    //ヘッダ：編集～
    $header_arr[]=array('text' => $lang_box_admin['orderno'], 'field' => 'orderno', 'sort' => true);
    $header_arr[]=array('text' => $LANG_ADMIN['edit'], 'field' => 'editid', 'sort' => false);
    $header_arr[]=array('text' => $LANG_ADMIN['copy'], 'field' => 'copy', 'sort' => false);
    $header_arr[]=array('text' => $lang_box_admin['id'], 'field' => 'id', 'sort' => true);
	
	$header_arr[]=array('text' => $lang_box_admin['kind'], 'field' => 'kind', 'sort' => true);
    $header_arr[]=array('text' => $lang_box_admin['no'], 'field' => 'no', 'sort' => true);
    $header_arr[]=array('text' => $lang_box_admin['value'], 'field' => 'value', 'sort' => true);
    $header_arr[]=array('text' => $lang_box_admin['value2'], 'field' => 'value2', 'sort' => true);

    //
    $text_arr = array('has_menu' =>  true,
      'has_extras'   => true,
      'form_url' => $_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT);

    //Query
    $sql = "SELECT ";
    $sql .= " t.id".LB;
    $sql .= " ,t.kind".LB;
    $sql .= " ,t.no".LB;
    $sql .= " ,t.value".LB;
    $sql .= " ,t.value2".LB;
    $sql .= " ,t.orderno".LB;
    $sql .= " FROM ";
    $sql .= " {$table} AS t".LB;
    $sql .= " WHERE ".LB;
    $sql .= " 1=1".LB;
    //

    $query_arr = array(
        'table' =>$table,
        'sql' => $sql,
        'query_fields' => array('t.id','t.kind','t.no','t.value','t.value2'),
        'default_filter' => $exclude);
    //デフォルトソート項目:
    $defsort_arr = array('field' => 'orderno', 'direction' => 'ASC');
	$form_arr = array('bottom' => '', 'top' => '');
    $pagenavurl = '&amp;filter_val=' . $filter_val;
    //List 取得
	if (COM_versionCompare(VERSION, "2.0.0",  '>=')){
		$retval .= ADMIN_list(
			$pi_name
			, "LIB_GetListField"
			, $header_arr
			, $text_arr
			, $query_arr
			, $defsort_arr
			, $filter
			, '', ''
			, $form_arr
			, true
			, $pagenavurl
			);
	}else{
		$retval .= ADMIN_list(
			$pi_name
			, "LIB_GetListField"
			, $header_arr
			, $text_arr
			, $query_arr
			, $defsort_arr
			, $filter
			, '', ''
			, $form_arr
			, true
			);
	}
	

    return $retval;
}

function LIB_GetListField($fieldname, $fieldvalue, $A, $icon_arr)
// +---------------------------------------------------------------------------+
// | 一覧取得 ADMIN_list 経由で使用
// +---------------------------------------------------------------------------+
{
    global $_CONF, $LANG_ACCESS;

    $retval = '';

    switch($fieldname) {
        //編集アイコン
        case 'editid':
            $retval = "<a href=\"{$_CONF['site_admin_url']}";
            $retval .= "/plugins/".THIS_SCRIPT;
            $retval .= "?mode=edit";
            $retval .= "&amp;id={$A['id']}\">";
            $retval .= "{$icon_arr['edit']}</a>";
            break;
        case 'copy':
            $url=$_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT;
            $url.="?";
            $url.="mode=copy";
            $url.="&amp;id={$A['id']}";
            $retval = COM_createLink($icon_arr['copy'],$url);
            break;

        //各項目
        default:
            $retval = $fieldvalue;
            break;
    }

    return $retval;

}

function LIB_Edit(
    $pi_name
    ,$id
    ,$edt_flg
    ,$msg = ''
    ,$errmsg=""
    ,$mode="edit"
)
// +---------------------------------------------------------------------------+
// | 機能  編集画面表示                                                        |
// | 書式 fncEdit($id , $edt_flg,$msg,$errmsg)                                 |
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// | 引数 $id:
// | 引数 $edt_flg:
// | 引数 $msg:メッセージ番号
// | 引数 $errmsg:エラーメッセージ
// | 引数 $mode:
// +---------------------------------------------------------------------------+
// | 戻値 nomal:編集画面                                                       |
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $_TABLES;
    global $LANG_ADMIN;
    global $MESSAGE;
    global $LANG_ACCESS;
    global $_USER;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $lang_box="LANG_".strtoupper($pi_name);
    global $$lang_box;
    $lang_box=$$lang_box;

    $table=$_TABLES[strtoupper($pi_name).'_mst'];
    $table2=$_TABLES[strtoupper($pi_name).'_def_field'];

    $retval = '';

    $delflg=false;


    if (!empty ($msg)) {
        $retval .= COM_showMessage ($msg,$pi_name);
        $retval .= $errmsg;
		
		
        // clean 'em up
        $kind = COM_applyFilter($_POST['kind']);
        $no = COM_applyFilter($_POST['no'],true);
        $value = COM_applyFilter($_POST['value']);
		$value2 = COM_applyFilter($_POST['value2']);
		
        $disp = COM_applyFilter ($_POST['disp']);
        $orderno = COM_applyFilter ($_POST['orderno'],true);
        $relno= COM_applyFilter ($_POST['relno'],true);

        $uuid=$_USER['uid'];

    }else{
        if (empty($id)) {
            $id=0;

            $kind ="";
            $no ="";
            $value ="";
            $value2 ="";
            $disp ="";
            $orderno ="";
            $relno ="";

            $uuid=0;
            $udatetime="";//"";

        }else{
            $sql = "SELECT ";

            $sql .= " *";
			$sql .= " ,UNIX_TIMESTAMP(udatetime) AS udatetime_un".LB;

            $sql .= " FROM ";
            $sql .= $table;
            $sql .= " WHERE ";
            $sql .= " id = $id";
            $result = DB_query($sql);

            $A = DB_fetchArray($result);

            $kind = COM_stripslashes($A['kind']);
            $no = COM_stripslashes($A['no']);
            $value = COM_stripslashes($A['value']);
            $value2 = COM_stripslashes($A['value2']);
            $disp=COM_stripslashes($A['disp']);
            $orderno=COM_stripslashes($A['orderno']);
            $relno=COM_stripslashes($A['relno']);
			
            $uuid = COM_stripslashes($A['uuid']);
			$wary = COM_getUserDateTimeFormat(COM_stripslashes($A['udatetime_un']));
			$udatetime = $wary[0];

            // データがあれば削除させない
            if ($edt_flg==FALSE) {
                $delflg=true;
            }
        }
    }
    if ($mode==="copy"){
        $id=0;
        //作成日付
        $created=0;
        $created_month=0;
        $created_day = 0;
        $created_year = 0;
        $created_hour = 0;
        $created_minute = 0;
        //
        $delflg=false;
    }


    $tmplfld=DATABOX_templatePath('admin','default',$pi_name);
    $templates = new Template($tmplfld);
    $templates->set_file('editor',"mst_editor.thtml");

    //--
    $templates->set_var('about_thispage', $lang_box_admin['about_admin_mst']);
    $templates->set_var('lang_must', $lang_box_admin['must']);
    $templates->set_var('site_url', $_CONF['site_url']);
    $templates->set_var('site_admin_url', $_CONF['site_admin_url']);

    //--
    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);
    $templates->set_var('gltoken_name', CSRF_TOKEN);
    $templates->set_var('gltoken', $token);
    $templates->set_var ( 'xhtml', XHTML );

    $templates->set_var('script', THIS_SCRIPT);

    //
    $templates->set_var('lang_link_admin', $lang_box_admin['link_admin']);
    $templates->set_var('lang_link_admin_top', $lang_box_admin['link_admin_top']);

    //id
    $templates->set_var('lang_id', $lang_box_admin['id']);
    $templates->set_var('id', $id);


    //コード、名前＆説明
    $templates->set_var('lang_kind', $lang_box_admin['kind']);
    $templates->set_var ('kind', $kind);
    $templates->set_var('lang_no', $lang_box_admin['no']);
    $templates->set_var ('no', $no);
    $templates->set_var('lang_value', $lang_box_admin['value']);
    $templates->set_var ('value', $value);
    $templates->set_var('lang_value2', $lang_box_admin['value2']);
    $templates->set_var ('value2', $value2);
    $templates->set_var('lang_disp', $lang_box_admin['disp']);
    $templates->set_var ('disp', $disp);
    $templates->set_var('lang_relno', $lang_box_admin['relno']);
    $templates->set_var ('relno', $relno);
	
    //順序
    $templates->set_var('lang_orderno', $lang_box_admin['orderno']);
    $templates->set_var ('orderno', $orderno);

    //保存日時
    $templates->set_var ('lang_udatetime', $lang_box_admin['udatetime']);
    $templates->set_var ('udatetime', $udatetime);
    $templates->set_var ('lang_uuid', $lang_box_admin['uuid']);
    $templates->set_var ('uuid', $uuid);

    // SAVE、CANCEL ボタン
    $templates->set_var('lang_save', $LANG_ADMIN['save']);
    $templates->set_var('lang_cancel', $LANG_ADMIN['cancel']);
    $templates->set_var('lang_preview', $LANG_ADMIN['preview']);

	//delete_option
    $wkcnt=DB_count($table2,"selectlist",$kind);
    if ($delflg){
        if ($wkcnt>0){
            $templates->set_var('lang_delete_help', $lang_box_admin['delete_help_mst']);
        }else{
            $delbutton = '<input type="submit" value="' . $LANG_ADMIN['delete']
                   . '" name="mode"%s>';
            $jsconfirm = ' onclick="return confirm(\'' . $MESSAGE[76] . '\');"';
            $templates->set_var ('delete_option',
                                      sprintf ($delbutton, $jsconfirm));
        }
    }


    //
    $templates->parse('output', 'editor');
    $retval .= $templates->finish($templates->get_var('output'));

    return $retval;
}


function LIB_Save (
    $pi_name
    ,$edt_flg
    ,$navbarMenu
    ,$menuno

)
// +---------------------------------------------------------------------------+
// | 機能  保存
// | 書式 fncSave ($pi_name,$edt_flg)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// | 引数 $edt_flg
// | 引数 $navbarMenu
// | 引数 $menuno
// +---------------------------------------------------------------------------+
// | 戻値 nomal:戻り画面＆メッセージ
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $_TABLES;
    global $_USER;

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $lang_box_admin_menu="LANG_".strtoupper($pi_name)."_admin_menu";
    global $$lang_box_admin_menu;
    $lang_box_admin_menu=$$lang_box_admin_menu;

    $table=$_TABLES[strtoupper($pi_name).'_mst'];

    $retval = '';

    // clean 'em up
    $id = COM_applyFilter($_POST['id'],true);

    $kind=COM_applyFilter($_POST['kind']);
    $kind = addslashes (COM_checkHTML (COM_checkWords ($kind)));
	
	
    $no=COM_applyFilter($_POST['no'],true);
    $no = addslashes (COM_checkHTML (COM_checkWords ($no)));
    $value=COM_applyFilter($_POST['value']);
    $value = addslashes (COM_checkHTML (COM_checkWords ($value)));
    $value2=COM_applyFilter($_POST['value2']);
    $value2 = addslashes (COM_checkHTML (COM_checkWords ($value2)));
	
	$disp=$_POST['disp'];
    $disp= addslashes (COM_checkHTML (COM_checkWords ($disp)));

    $orderno = mb_convert_kana($_POST['orderno'],"a");//全角英数字を半角英数字に変換する
    $orderno=COM_applyFilter($orderno,true);
	
    $relno=COM_applyFilter($_POST['relno']);
    $relno = addslashes (COM_checkHTML (COM_checkWords ($relno)));
	
    //$name = mb_convert_kana($name,"AKV");
    //A:半角英数字を全角英数字に変換する
    //K:半角カタカナを全角カタカナに変換する
    //V:濁点つきの文字を１文字に変換する (K、H と共に利用する）
    //$name = str_replace ("'", "’",$name);
    //$code = mb_convert_kana($code,"a");//全角英数字を半角英数字に変換する

    //-----
    $type=1;
    $uuid=$_USER['uid'];

    // CHECK　はじめ
    $err="";
    //ID コード
    if ($id==0 ){
        //$err.=$lang_box_admin['err_uid']."<br/>".LB;
    }else{
        if (!is_numeric($id) ){
            $err.=$lang_box_admin['err_id']."<br/>".LB;
        }
    }

    //kind必須
    if (empty($kind)){
        $err.=$lang_box_admin['err_kind']."<br/>".LB;
    }
    //no必須　二重チェック
    if ($no==""){
        $err.=$lang_box_admin['err_no']."<br/>".LB;
	}else{
        $cntsql="SELECT id FROM {$table} ";
        $cntsql.=" WHERE ";
        $cntsql.=" no={$no} ";
        $cntsql.=" AND kind='{$kind}'";
        $cntsql.=" AND id<>{$id}";
		$result = DB_query ($cntsql);
        $numrows = DB_numRows ($result);
        if ($numrows<>0 ) {
			$err.=$lang_box_admin['err_no_w']."<br/>".LB;
        }
    }
	
	
	
   //errorのあるとき
    if ($err<>"") {
		$retval['title']=$lang_box_admin['piname'].$lang_box_admin['edit'];
        $retval['display']= LIB_Edit($pi_name,$id, $edt_flg,3,$err);
        return $retval;

    }
    // CHECK　おわり

    if ($id==0){
        $w=DB_getItem($table,"max(id)","1=1");
        if ($w=="") {
            $w=0;
        }
        $id=$w+1;
    }

    $fields="id";
    $values="$id";
	
    $fields.=",kind";
    $values.=",'$kind'";

    $fields.=",no";
    $values.=",$no";

    $fields.=",value";
    $values.=",'$value'";

	$fields.=",value2";
	if  ($value2==""){
		$values.=",NULL";
	}else{	
		$values.=",'$value2'";
	}
	$fields.=",disp";
	if  ($disp==""){
		$values.=",NULL";
	}else{	
		$values.=",'$disp'";
	}
	
	$fields.=",orderno";//
	if  ($orderno==""){
		$values.=",NULL";
	}else{	
		$values.=",$orderno";
	}
	
	$fields.=",relno";//
	if  ($relno==""){
		$values.=",NULL";
	}else{	
		$values.=",$relno";
	}
    $fields.=",uuid";
    $values.=",$uuid";

    $fields.=",udatetime";
    $values.=",NOW( )";
    //

    DB_save($table,$fields,$values,$return_page);

//    $rt=fncsendmail ($id);
	$message="";
    if ($box_conf['aftersave_admin']==='no') {
		$retval['title']=$lang_box_admin['piname'].$lang_box_admin['edit'];
        $retval['display']= LIB_Edit($pi_name,$id, $edt_flg,1,"");
        return $retval;
	}else if ($box_conf['aftersave_admin']==='list'
				OR $box_conf['aftersave_admin']==='item'){
            $url = $_CONF['site_admin_url'] . "/plugins/$pi_name/mst.php";
            $item_url=COM_buildURL($url);
            $target='item';
			$message=1;
    }else if ($box_conf['aftersave_admin']==='admin'){
			$target=$box_conf['aftersave_admin'];
			$message=1;
    }else{
            $item_url=$_CONF['site_url'] . $box_conf['top'];
            $target=$box_conf['aftersave_admin'];
    }

    $return_page = PLG_afterSaveSwitch(
                    $target
                    ,$item_url
                    ,$pi_name
                    , $message);

    echo $return_page;


    exit;

}
function LIB_delete (
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能  削除
// | 書式 LIB_fncdelete ($pi_name)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:戻り画面＆メッセージ
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $_TABLES;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $table=$_TABLES[strtoupper($pi_name).'_mst'];

    $id = COM_applyFilter($_POST['id'],true);

    DB_delete ($table, 'id', $id);

    return COM_refresh ($_CONF['site_admin_url']
                        . '/plugins/'.THIS_SCRIPT.'?msg=2');
}



// +---------------------------------------------------------------------------+
// | 機能  エキスポート
// | 書式 LIB_export ($pi_name)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
function LIB_export (
    $pi_name
)
{
    global $_CONF;
    global $_TABLES;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $table=$_TABLES[strtoupper($pi_name).'_mst'];

require_once ($_CONF['path'].'plugins/databox/lib/comj_dltbldt.php');

// 項目の見出リスト
$fld = array ();

$fld['id']['name']  = $lang_box_admin['id'];
$fld['kind']['name']  = $lang_box_admin['kind'];
$fld['no']['name']  = $lang_box_admin['no'];
$fld['value']['name']  = $lang_box_admin['value'];
$fld['value2']['name']  = $lang_box_admin['value2'];
$fld['disp']['name']  = $lang_box_admin['disp'];
$fld['orderno']['name']  = $lang_box_admin['orderno'];
$fld['relno']['name']  = $lang_box_admin['relno'];

$fld['udatetime']['name']  = $lang_box_admin['udatetime'];
$fld['uuid']['name']  = $lang_box_admin['uuid'];

//----------------------
$filenm=$pi_name."_mst";
$tbl ="{$table}";
$where = "";
$order = "kind,no";

$addition=false;
	
$rt= DATABOX_dltbldt($filenm,$fld,$tbl,$where,$order,$pi_name,$addition);

return;
}

// +---------------------------------------------------------------------------+
// | 機能 サンプルインポート
// | 書式 LIB_sampleimport ($pi_name)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
function LIB_sampleimport (
    $pi_name
)
{
    global $_CONF;
    global $_TABLES;

    //$lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    //global $$lang_box_admin;
    //$lang_box_admin=$$lang_box_admin;

    $table=$_TABLES[strtoupper($pi_name).'_mst'];

    //サンプルマスタのデータ
    $_SQL =array();
    require_once ($_CONF['path'] . "plugins/{$pi_name}/lib/sql_mst_sample.inc");

    for ($i = 1; $i <= count($_SQL); $i++) {
        $w=current($_SQL);
        DB_query(current($_SQL));
        next($_SQL);
    }
    if (DB_error()) {
        return false;
    }

    return true;
}

function LIB_import (
    $pi_name
    )
// +---------------------------------------------------------------------------+
// | 機能  インポート画面表示
// | 書式 LIB_import ($pi_name)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
{
    global $_CONF;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $tmpl = new Template ($_CONF['path'] . "plugins/".THIS_PLUGIN."/templates/admin/");
    $tmpl->set_file(array('import' => 'import.thtml'));

    $tmpl->set_var('site_admin_url', $_CONF['site_admin_url']);

    $tmpl->set_var('gltoken_name', CSRF_TOKEN);
    $tmpl->set_var('gltoken', SEC_createToken());
    $tmpl->set_var ( 'xhtml', XHTML );

    $tmpl->set_var('script', THIS_SCRIPT);

    $tmpl->set_var('importmsg', $lang_box_admin['importmsg']);
    $tmpl->set_var('importfile', $lang_box_admin['importfile']);
    $tmpl->set_var('submit', $lang_box_admin['submit']);

    $tmpl->parse ('output', 'import');
    $import = $tmpl->finish ($tmpl->get_var ('output'));

    $retval="";
    $retval .= $import;

    return $retval;
}


function LIB_sendmail (
    $pi_name
    ,$id
)
// +---------------------------------------------------------------------------+
// | 機能  メール送信
// | 書式 LIB_sendmail ($pi_name,$id)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $_TABLES;
    global $_USER ;

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $lang_box_mail="LANG_".strtoupper($pi_name)."_MAIL";
    global $$lang_box_mail;
    $lang_box=$$lang_box_mail;

    $table=$_TABLES[strtoupper($pi_name).'_def_category'];

    $retval = '';


    $sql = "SELECT ";

    $sql .= " *";

    $sql .= " FROM ";
    $sql .= $table;
    $sql .= " WHERE ";
    $sql .= " category_id = $id";



//ECHO "sql={$sql}<br>";

    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);

    if ($numrows > 0) {

        //
        $A = DB_fetchArray ($result);


        //保存日時
        $msg.=$lang_box_admin['udatetime'].":".$A['udatetime'].LB;

        //コード
        $msg.= $lang_box_admin['category_id'].":".$A['category_id'].LB;

        //名称
        $msg.= $lang_box_admin['name'].":".$A['name'].LB;
        //順序
        $msg.= $lang_box_admin['orderno'].":".$A['orderno'].LB;

        $msg.= $lang_box_mail['sig'] .LB;
        //
        $msg.= $_CONF['site_url'] . '/'.THIS_SCRIPT .'?id=' .$A['category_id'].LB;


        //
        //
        $to=$_USER['email'];
        //
        $subject = $lang_box_mail['subject'];
        //
        $message=$lang_box_mail['message'];
        $message.=$msg;

        $html = false;
        $priority = 0;
        $cc = '' ;
        //COM_mail ($to, $subject, $message, $from,$html,$priority,$cc);
        COM_mail ($to, $subject, $message);
        $to=$box_conf['adminmail'];
        COM_mail ($to, $subject, $message);
        //COM_mail ($to, $subject, $message, $from,$html,$priority,$cc);

    }

    return $retval;
}
function LIB_Menu(
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能  menu表示  
// | 書式 LIB_Menu("databox")
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 menu 
// +---------------------------------------------------------------------------+
{

    global $_CONF;
    global $LANG_ADMIN;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $lang_box="LANG_".strtoupper($pi_name);
    global $$lang_box;
    $lang_box=$$lang_box;

    $retval = '';
	
    //MENU1:管理画面
    $url1=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=new';
    $url2=$_CONF['site_url'] . '/'.$pi_name.'/list.php';
    $url3=$_CONF['site_url'] . '/'.$pi_name.'/mst.php';

    $url5=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=export';
    $url6=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=import';
    $url61=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=sampleimport';

    $menu_arr[]=array('url' => $url1,'text' => $lang_box_admin['new']);
    $menu_arr[]=array('url' => $url2,'text' => $lang_box['list']);
    $menu_arr[]=array('url' => $url5,'text' => $lang_box_admin['export']);
    $menu_arr[]=array('url' => $url61,'text' => $lang_box_admin['sampleimport']);
    $menu_arr[]=array('url' => $_CONF['site_admin_url'],'text' => $LANG_ADMIN['admin_home']);

    $function="plugin_geticon_".$pi_name;
    $icon=$function();

    $retval .= ADMIN_createMenu(
        $menu_arr,
        $lang_box_admin['instructions'],
        $icon
    );
    return $retval;
}

