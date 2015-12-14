<?php

if (strpos ($_SERVER['PHP_SELF'], 'lib_fieldset.inc') !== false) {
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

    $table=$_TABLES[strtoupper($pi_name).'_def_fieldset'];

    require_once( $_CONF['path_system'] . 'lib-admin.php' );

    $retval = '';
    //MENU1:管理画面
    $url1=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=new';
    $url2=$_CONF['site_url'] . '/'.$pi_name.'/list.php';
    $url5=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=export';
    $url6=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=import';

    $menu_arr[]=array('url' => $url1,'text' => $lang_box_admin['new']);
    $menu_arr[]=array('url' => $url2,'text' => $lang_box['list']);
    $menu_arr[]=array('url' => $url5,'text' => $lang_box_admin['export']);
    //$menu_arr[]=array('url' => $url6,'text' => $lang_box['export']);
    $menu_arr[]=array('url' => $_CONF['site_admin_url'],'text' => $LANG_ADMIN['admin_home']);


    $retval .= COM_startBlock($lang_box_admin['admin_list'], '',
                              COM_getBlockTemplate('_admin_block', 'header'));

    $function="plugin_geticon_".$pi_name;
    $icon=$function();
    $retval .= ADMIN_createMenu(
        $menu_arr,
        $lang_box_admin['instructions'],
        $icon
    );

    //ヘッダ：編集～
    $header_arr[]=array('text' => $LANG_ADMIN['edit'], 'field' => 'editid', 'sort' => false);
    $header_arr[]=array('text' => $LANG_ADMIN['copy'], 'field' => 'copy', 'sort' => false);
    $header_arr[]=array('text' => $lang_box_admin['fieldset_id'], 'field' => 'fieldset_id', 'sort' => true);
    $header_arr[]=array('text' => $lang_box_admin['name'], 'field' => 'name', 'sort' => true);
    $header_arr[]=array('text' => $lang_box_admin['fieldsetfields'], 'field' => 'list', 'sort' => false);
    $header_arr[]=array('text' => $lang_box_admin['fieldsetgroups'], 'field' => 'listg', 'sort' => false);

    //
    $text_arr = array('has_menu' =>  true,
      'has_extras'   => true,
      'form_url' => $_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT);

    //Query
    $sql = "SELECT ";
    $sql .= " fieldset_id";
    $sql .= " ,name";
    $sql .= " FROM ";
    $sql .= " {$table} AS t";
    $sql .= " WHERE ";
    $sql .= " fieldset_id<>0";
    //

    $query_arr = array(
        'table' => $table,
        'sql' => $sql,
        'query_fields' => array('fieldset_id','name'),
        'default_filter' => $exclude);
    //デフォルトソート項目:
    $defsort_arr = array('field' => 'fieldset_id', 'direction' => 'ASC');
    //List 取得
    //ADMIN_list($component, $fieldfunction, $header_arr, $text_arr,
    //       $query_arr, $menu_arr, $defsort_arr, $filter = '', $extra = '', $options = '')
    $retval .= ADMIN_list(
        $pi_name
        , "LIB_GetListField"
        , $header_arr
        , $text_arr
        , $query_arr
        , $defsort_arr
        );

    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    return $retval;
}

function LIB_GetListField(
	$fieldname
	, $fieldvalue
	, $A
	, $icon_arr
)
// +---------------------------------------------------------------------------+
// | 一覧取得 ADMIN_list で使用
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $LANG_ACCESS;

    $retval = '';

    switch($fieldname) {
        //編集アイコン
        case 'editid':
            $retval = "<a href=\"{$_CONF['site_admin_url']}";
            $retval .= "/plugins/".THIS_SCRIPT;
            $retval .= "?mode=edit";
            $retval .= "&amp;id={$A['fieldset_id']}\">";
            $retval .= "{$icon_arr['edit']}</a>";
            break;
        case 'copy':
            $url=$_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT;
            $url.="?";
            $url.="mode=copy";
            $url.="&amp;id={$A['fieldset_id']}";
            $retval = COM_createLink($icon_arr['copy'],$url);
            break;
		
		case 'list':
            $url=$_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT;
			$url.="?";
		
            $url1=$url."mode=listfields";
            $url1.="&amp;id={$A['fieldset_id']}";
		    $retval = COM_createLink($icon_arr['list'],$url1 );

            $url1=$url."mode=editfields";
            $url1.="&amp;id={$A['fieldset_id']}";
            $retval .= '&nbsp;&nbsp;' . COM_createLink($icon_arr['edit'],$url1 );
            break;
		
		case 'listg':
            $url=$_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT;
			$url.="?";
		
            $url1=$url."mode=listgroups";
            $url1.="&amp;id={$A['fieldset_id']}";
		    $retval = COM_createLink($icon_arr['list'],$url1 );

            $url1=$url."mode=editgroups";
            $url1.="&amp;id={$A['fieldset_id']}";
            $retval .= '&nbsp;&nbsp;' . COM_createLink($icon_arr['edit'],$url1 );
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
// | 機能  編集画面表示
// | 書式 LIB_Edit($pi_name,$id , $edt_flg,$msg,$errmsg)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// | 引数 $id:
// | 引数 $edt_flg:
// | 引数 $msg:メッセージ番号
// | 引数 $mode:edit ,copy
// +---------------------------------------------------------------------------+
// | 戻値 nomal:編集画面
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

    $lang_box_noyes="LANG_".strtoupper($pi_name)."_NOYES";
    global $$lang_box_noyes;
    $lang_box_noyes=$$lang_box_noyes;

    $table=$_TABLES[strtoupper($pi_name).'_def_fieldset'];
    $table2=$_TABLES[strtoupper($pi_name).'_base'];

    $retval = '';
    $delflg=false;

    //メッセージ表示
    if (!empty ($msg)) {
        $retval .= COM_showMessage ($msg,$pi_name);
        $retval .= $errmsg;

        // clean 'em up
        $code = COM_applyFilter($_POST['code']);
        $name = COM_applyFilter($_POST['name']);
		$description = $_POST['description'];
		
		$defaulttemplatesdirectory = COM_applyFilter($_POST['defaulttemplatesdirectory']);
		$layout = COM_applyFilter($_POST['layout']);
		
		$orderno = COM_applyFilter ($_POST['orderno']);
        $parent_flg = COM_applyFilter ($_POST['parent_flg'],true);

        $uuid=$_USER['uid'];

    }else{
        if (empty($id)) {

            $id=0;

            $code ="";
            $name ="";
            $description ="";
			$defaulttemplatesdirectory = "";
			$layout = "";

            $uuid=0;
            $udatetime="";//"";

        }else{
            $sql = "SELECT ";

            $sql .= " *";
			$sql .= " ,UNIX_TIMESTAMP(udatetime) AS udatetime_un".LB;

            $sql .= " FROM ";
            $sql .= $table;
            $sql .= " WHERE ";
            $sql .= " fieldset_id = $id";
            $result = DB_query($sql);

            $A = DB_fetchArray($result);

            $name = COM_stripslashes($A['name']);
            $description = COM_stripslashes($A['description']);
            $defaulttemplatesdirectory=COM_stripslashes($A['defaulttemplatesdirectory']);
            $layout=COM_stripslashes($A['layout']);

            $uuid = COM_stripslashes($A['uuid']);
			$wary = COM_getUserDateTimeFormat(COM_stripslashes($A['udatetime_un']));
			$udatetime = $wary[0];

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

    $retval .= COM_startBlock ($lang_box_admin['edit'], '',
                               COM_getBlockTemplate ('_admin_block', 'header'));


    $tmplfld=DATABOX_templatePath('admin','default',$pi_name);
    $templates = new Template($tmplfld);
    $templates->set_file('editor',"fieldset_editor.thtml");
    //--
    $templates->set_var('about_thispage', $lang_box_admin['about_admin_fieldset']);
    $templates->set_var('lang_must', $lang_box_admin['must']);
    $templates->set_var('site_url', $_CONF['site_url']);
    $templates->set_var('site_admin_url', $_CONF['site_admin_url']);

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
    $templates->set_var('lang_fieldset_id', $lang_box_admin['fieldset_id']);
    $templates->set_var('id', $id);

    //コード、名前＆説明etc
    $templates->set_var('lang_name', $lang_box_admin['name']);
    $templates->set_var ('name', $name);
    $templates->set_var('lang_description', $lang_box_admin['description']);
    $templates->set_var ('description', $description);
	
	$templates->set_var('lang_defaulttemplatesdirectory', $lang_box_admin['defaulttemplatesdirectory']);
	$templates->set_var ('defaulttemplatesdirectory', $defaulttemplatesdirectory);

	$select_defaulttemplatesdirectory=LIB_templatesdirectory ($pi_name,$defaulttemplatesdirectory);
	$templates->set_var ('select_defaulttemplatesdirectory', $select_defaulttemplatesdirectory);
	
	$templates->set_var('lang_layout', $lang_box_admin['layout']);
	$templates->set_var ('layout', $layout);
    $list_layout=DATABOX_getoptionlist("layout",$layout,0,$pi_name,"",0 );//@@@@@
    $templates->set_var ('list_layout', $list_layout);

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
    if ($delflg){
        $wkcnt=DB_count($table2,"fieldset_id",$id);
        if ($wkcnt>0){
            $templates->set_var('lang_delete_help', $lang_box_admin['delete_help_fieldset']);
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
    $retval .= COM_endBlock (COM_getBlockTemplate ('_admin_block', 'footer'));

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

    $table=$_TABLES[strtoupper($pi_name).'_def_fieldset'];

    $retval = '';

    // clean 'em up
    $id = COM_applyFilter($_POST['id'],true);

    $code=COM_applyFilter($_POST['code']);
    $code = addslashes (COM_checkHTML (COM_checkWords ($code)));

    $name=COM_stripslashes($_POST['name']);
    $name = addslashes (COM_checkHTML (COM_checkWords ($name)));

	$description=COM_stripslashes($_POST['description']);
    $description = addslashes (COM_checkHTML (COM_checkWords ($description)));
	
	$layout=COM_applyFilter($_POST['layout']);
    $layout = addslashes (COM_checkHTML (COM_checkWords ($layout)));
	
	$defaulttemplatesdirectory=COM_applyFilter($_POST['defaulttemplatesdirectory']);
    $defaulttemplatesdirectory = addslashes (COM_checkHTML (COM_checkWords ($defaulttemplatesdirectory)));

    $parent_flg=COM_applyFilter($_POST['parent_flg'],true);

    $orderno = mb_convert_kana($_POST['orderno'],"a");//全角英数字を半角英数字に変換する
    $orderno=COM_applyFilter($orderno,true);

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
    //コード


    if ($code<>""){
         $cntsql="SELECT code FROM {$table} ";
         $cntsql.=" WHERE ";
         $cntsql.=" code='{$code}' ";
         $cntsql.=" AND fieldset_id<>{$id}";
         $result = DB_query ($cntsql);
         $numrows = DB_numRows ($result);
         if ($numrows<>0 ) {
             $err.=$lang_box_admin['err_code_w']."<br/>".LB;
         }
    }

    //タイトル必須
    if (empty($name)){
        $err.=$lang_box_admin['err_name']."<br/>".LB;
    }

    //errorのあるとき
    if ($err<>"") {
        $page_title=$lang_box_admin['piname'].$lang_box_admin['edit'];
        $retval .= DATABOX_siteHeader($pi_name,'_admin',$page_title);
        $retval .=ppNavbarjp($navbarMenu,$lang_box_admin_menu[$menuno]);

        $retval .= LIB_Edit($pi_name,$id, $edt_flg,3,$err);
        $retval .= DATABOX_siteFooter($pi_name,'_admin');

        return $retval;

    }

    // CHECK　おわり

    if ($id==0){
        $w=DB_getItem($table,"max(fieldset_id)","1=1");
        if ($w=="") {
            $w=0;
        }
        $id=$w+1;
    }

    $fields="fieldset_id";
    $values="$id";

    $fields.=",name";
    $values.=",'$name'";

    $fields.=",description";
    $values.=",'$description'";
	
    $fields.=",layout";
    $values.=",'$layout'";
	
    $fields.=",defaulttemplatesdirectory";
    $values.=",'$defaulttemplatesdirectory'";
	
    $fields.=",uuid";
    $values.=",$uuid";

    //

//    if ($edt_flg){
//        $return_page=$_CONF['site_url'] . "/".THIS_SCRIPT;
//        $return_page.="?id=".$id;
//    }else{
//        $return_page=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?msg=1';
//    }

    DB_save($table,$fields,$values,$return_page);

//    $rt=fncsendmail ($id);

	$message="";
    if ($box_conf['aftersave_admin']==='no') {
		$retval['title']=$lang_box_admin['piname'].$lang_box_admin['edit'];
        $retval['display']= LIB_Edit($pi_name,$id, $edt_flg,1,"");

        return $retval;

	}else if ($box_conf['aftersave_admin']==='list'
				OR $box_conf['aftersave_admin']==='item'){
            $url = $_CONF['site_admin_url'] . "/plugins/$pi_name/fieldset.php";
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
// | 書式 fncdelete ($pi_name)
// +---------------------------------------------------------------------------+
// | 戻値 nomal:戻り画面＆メッセージ
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $_TABLES;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $table1=$_TABLES[strtoupper($pi_name).'_def_fieldset'];
    $table2=$_TABLES[strtoupper($pi_name).'_def_fieldset_assignments'];
    $table3=$_TABLES[strtoupper($pi_name).'_def_fieldset_group'];

    $id = COM_applyFilter($_POST['id'],true);

    // CHECK
    $err="";
    //category addtionfield check!!!
    if ($err<>"") {
        $pagetitle = $lang_box_admin['err'];
        $retval .= DATABOX_siteHeader($pi_name,'_admin',$page_title);

        $retval .= COM_startBlock ($lang_box_admin['err'], '',
                            COM_getBlockTemplate ('_msg_block', 'header'));
        $retval .= $err;
        $retval .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
        $retval .= DATABOX_siteFooter($pi_name,'_admin');
        return $retval;
    }

    //
    DB_delete ($table3, 'fieldset_id', $id);
    DB_delete ($table2, 'fieldset_id', $id);
    DB_delete ($table1, 'fieldset_id', $id);

    return COM_refresh ($_CONF['site_admin_url']
                        . '/plugins/'.THIS_SCRIPT.'?msg=2');
}



function LIB_export (
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能  エキスポート
// | 書式 fncexport ($pi_name)
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $_TABLES;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $table=$_TABLES[strtoupper($pi_name).'_def_fieldset'];

    require_once ($_CONF['path'].'plugins/'.$pi_name.'/lib/comj_dltbldt.php');

    // 項目の見出リスト
    $fld = array ();


$fld['fieldset_id']['name']  = $lang_box_admin['fieldset_id'];
$fld['name']['name']  = $lang_box_admin['name'];
$fld['description']['name']  = $lang_box_admin['description'];

$fld['udatetime']['name']  = $lang_box_admin['udatetime'];
$fld['uuid']['name']  = $lang_box_admin['uuid'];

//----------------------
$filenm=$pi_name."_fieldset";
$tbl ="{$table}";
$where = "";
$order = "fieldset_id";

$addition=false;

$rt= DATABOX_dltbldt($filenm,$fld,$tbl,$where,$order,$pi_name,$addition);

return;
}

function LIB_import (
    $pi_name
    )
// +---------------------------------------------------------------------------+
// | 機能  インポート画面表示
// | 書式 fncimport ($pi_name)
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
    $retval .= COM_startBlock ($lang_box_admin['import'], '',
                            COM_getBlockTemplate ('_admin_block', 'header'));
    $retval .= $import;
    $retval .= COM_endBlock (COM_getBlockTemplate ('_admin_block', 'footer'));


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


    $table=$_TABLES[strtoupper($pi_name).'_def_fieldset'];

    $retval = '';

    $sql = "SELECT ";

    $sql .= " *";

    $sql .= " FROM ";
    $sql .= $_TABLES[strtoupper($pi_name).'_def_fieldset'];
    $sql .= " WHERE ";
    $sql .= " fieldset_id = $id";

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
        $msg.= $_CONF['site_url'] . '/'.THIS_SCRIPT.'?id=' .$A['fieldset_id'].LB;

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

    }

    return $retval;
}

function LIB_ListFields(
	$pi_name
	,$id
)
// +---------------------------------------------------------------------------+
// | 機能  field一覧表示
// | 書式 LIB_ListFields($pi_name,$id)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// | 引数 $id:
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

    $table=$_TABLES[strtoupper($pi_name).'_def_fieldset_assignments'];
    $table2=$_TABLES[strtoupper($pi_name).'_def_field'];
	$tables = " {$table} AS t ,{$table2} AS t2";

    require_once( $_CONF['path_system'] . 'lib-admin.php' );

    $retval = '';
    //MENU1:管理画面
    $url2=$_CONF['site_url'] . '/admin/plugins/'.$pi_name.'/fieldset.php';
	
    $menu_arr[]=array('url' => $url2,'text' => $lang_box_admin['fieldsetlist']);
    $menu_arr[]=array('url' => $_CONF['site_admin_url'],'text' => $LANG_ADMIN['admin_home']);


    $retval .= COM_startBlock($lang_box_admin['admin_list'], '',
                              COM_getBlockTemplate('_admin_block', 'header'));

    $function="plugin_geticon_".$pi_name;
    $icon=$function();
    $retval .= ADMIN_createMenu(
        $menu_arr,
        $lang_box_admin['instructions'],
        $icon
    );

   //ヘッダ：編集～
    $header_arr[]=array('text' => $lang_box_admin['orderno'], 'field' => 'orderno', 'sort' => true);
    $header_arr[]=array('text' => $LANG_ADMIN['edit'], 'field' => 'editid', 'sort' => false);
    //$header_arr[]=array('text' => $LANG_ADMIN['copy'], 'field' => 'copy', 'sort' => false);
    $header_arr[]=array('text' => $lang_box_admin['field_id'], 'field' => 'field_id', 'sort' => true);
    $header_arr[]=array('text' => $lang_box_admin['name'], 'field' => 'name', 'sort' => true);
    $header_arr[]=array('text' => $lang_box_admin['templatesetvar'], 'field' => 'templatesetvar', 'sort' => true);
	
    //
    $text_arr = array('has_menu' =>  true,
      'has_extras'   => true,
      'form_url' => $_CONF['site_admin_url'] . "/plugins/databox/fieldset.php?mode=listfields&id=".$id);

	//Query
    $sql = "SELECT ".LB;
    $sql .= " t2.field_id".LB;
    $sql .= " ,t2.name".LB;
    $sql .= " ,t2.templatesetvar".LB;
    $sql .= " ,t2.orderno".LB;

    $sql .= " ,t2.type".LB;
    $sql .= " ,t2.allow_display".LB;

    $sql .= " FROM ".$tables.LB;
	
    $sql .= " WHERE ".LB;
    $sql .= " t.fieldset_id=".$id.LB;
    $sql .= " AND t.field_id=t2.field_id".LB;
	//


    $query_arr = array(
        'table' => $tables,
        'sql' => $sql,
        'query_fields' => array('t2.field_id','name'),
        'default_filter' => $exclude);
    //デフォルトソート項目:
    $defsort_arr = array('field' => 't2.field_id', 'direction' => 'ASC');
    //List 取得
    //ADMIN_list($component, $fieldfunction, $header_arr, $text_arr,
    //       $query_arr, $menu_arr, $defsort_arr, $filter = '', $extra = '', $options = '')
    $retval .= ADMIN_list(
        $pi_name
        , "LIB_GetListField_fields"
        , $header_arr
        , $text_arr
        , $query_arr
        , $defsort_arr
        );

    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    return $retval;
}

function LIB_GetListField_fields(
	$fieldname
	, $fieldvalue
	, $A
	, $icon_arr
)
// +---------------------------------------------------------------------------+
// | 一覧取得 ADMIN_list で使用
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    $LANG_ACCESS;

    $retval = '';

    $type=COM_applyFilter($A['type'],true);
    $allow_display=COM_applyFilter($A['allow_display'],true);
    $allow_type = array(0,2,3,7,8);
	
    switch($fieldname) {
        //編集アイコン
        case 'editid':
            $retval = "<a href=\"{$_CONF['site_admin_url']}";
            $retval .= "/plugins/databox/field.php";
            $retval .= "?mode=edit";
            $retval .= "&amp;id={$A['field_id']}\">";
            $retval .= "{$icon_arr['edit']}</a>";
            break;
        //名
        case 'name':
            if (in_array ($type,$allow_type)){
                if ($allow_display<2){
                    $name=COM_applyFilter($A['name']);
                    $url=$_CONF['site_url'] . "/databox/field.php";
                    $url.="?";
                    $url.="m=id";
                    $url.="&id=".$A['field_id'];
                    $url = COM_buildUrl( $url );
                    $retval= COM_createLink($name, $url);
                    break;
                }
            }
        case 'templatesetvar':
            if (in_array ($type,$allow_type)){
                if ($allow_display<2){
                    $name=COM_applyFilter($A['templatesetvar']);
                    $url=$_CONF['site_url'] . "/databox/field.php";
                    $url.="?";
                    $url.="m=code";
                    $url.="&code=".$A['templatesetvar'];
                    $url = COM_buildUrl( $url );
                    $retval= COM_createLink($name, $url);
                    break;
                }
            }


        //各項目
        default:
            $retval = $fieldvalue;
            break;
    }

    return $retval;


}

function LIB_editfields(
	$pi_name
	,$id
)
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

	//global  $_USER;
	global  $LANG_ACCESS;
	global  $LANG28;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';
	
	$table=$_TABLES[strtoupper($pi_name).'_def_fieldset'];
    $fieldset_name = DB_getItem($table, 'name', "fieldset_id = $id");

    $fieldset_listing_url=$_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT;
	
    //MENU1:管理画面
    $url2=$_CONF['site_url'] . '/admin/plugins/'.$pi_name.'/fieldset.php';
	
    $menu_arr[]=array('url' => $url2,'text' => $lang_box_admin['fieldsetlist']);
    $menu_arr[]=array('url' => $_CONF['site_admin_url'],'text' => $LANG_ADMIN['admin_home']);

    $retval .= COM_startBlock($lang_box_admin['admin_list']. " - $fieldset_name", '',
                              COM_getBlockTemplate('_admin_block', 'header'));

	
	$function="plugin_geticon_".$pi_name;
    $icon=$function();
    $retval .= ADMIN_createMenu(
        $menu_arr,
        $lang_box_admin['inst_fieldsetfields'],
        $icon
    );
	
    $tmplfld=DATABOX_templatePath('admin','default',$pi_name);
    $templates = new Template($tmplfld);
	$templates->set_file('editor',"fieldset_fields.thtml");
	
	
    //--
	
	
	
    $templates->set_var('site_url', $_CONF['site_url']);
    $templates->set_var('site_admin_url', $_CONF['site_admin_url']);

    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);
    $templates->set_var('gltoken_name', CSRF_TOKEN);
    $templates->set_var('gltoken', $token);
	
	$templates->set_var ( 'xhtml', XHTML );

    $templates->set_var('script', THIS_SCRIPT);

	//
    $templates->set_var('lang_link_admin', $lang_box_admin['link_admin']);
    $templates->set_var('lang_link_admin_top', $lang_box_admin['link_admin_top']);

    $templates->set_var('LANG_fieldsetfields',$lang_box_admin['fieldsetfieldsregistered']);
    $templates->set_var('fieldsetfields', LIB_selectFields($pi_name,$id, true));
	
	$templates->set_var('LANG_fieldlist', $lang_box_admin['fieldlist']);
	$templates->set_var('field_list', LIB_selectFields($pi_name,$id));
	
    $templates->set_var('LANG_add',$LANG_ACCESS['add']);
    $templates->set_var('LANG_remove',$LANG_ACCESS['remove']);
    $templates->set_var('lang_save', $LANG_ADMIN['save']);
    $templates->set_var('lang_cancel', $LANG_ADMIN['cancel']);
	
    $templates->set_var('id',$id);
	
    $templates->parse('output', 'editor');
    $retval .= $templates->finish($templates->get_var('output'));
	
    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    return $retval;
}
function LIB_selectFields(
	$pi_name
	, $fieldset_id
	, $selected = false)
{
	global $_TABLES;
	
	$table1=$_TABLES[strtoupper($pi_name).'_def_field'];
	$table2=$_TABLES[strtoupper($pi_name).'_def_fieldset_assignments'];

    $retval = '';

	// Get a list of additionfields in the selected field
	$sql  = "SELECT DISTINCT field_id ";
	$sql  .= " FROM {$table2}";
	$sql  .= " WHERE  ";
    $sql  .= " fieldset_id = $fieldset_id";
	$result = DB_query ($sql);
    $num = DB_numRows ($result);
	$fieldlist="";
	if ($num<>0){
		$selectedfields = array();
		while ($A = DB_fetchArray($result)) {
			$selectedfields[] = $A['field_id'];
		}
		$fieldlist = '(' . implode (',', $selectedfields) . ')';
	}
	
	//
	
	if  ($selected AND $fieldlist==""){
	}else{
		$sql = "SELECT DISTINCT field_id,name ";
		$sql  .= " FROM {$table1}";
	
		if ($fieldlist<>""){
			$sql  .= " WHERE  ";
			$sql  .= " field_id ";
			if ($selected==FALSE) {
				$sql .= 'NOT ';
			}
			$sql .= "IN {$fieldlist} ";
		}
	
		$sql .= " ORDER BY orderno";
		$result = DB_query ($sql);
		while ($A = DB_fetchArray($result)) {
		
            $field_id = COM_stripslashes($A['field_id']);
            $name = COM_stripslashes($A['name']);
			$retval .= '<option value="' . $field_id . '">' . $name . '</option>';
		}
	}
    return $retval;
}


function LIB_savefields(
	$pi_name
	,$fieldset_id
)
{
	global $_CONF;
	global $_TABLES;
	
	$fieldsetfields=$_POST['groupmembers'];
	$table=$_TABLES[strtoupper($pi_name).'_def_fieldset_assignments'];
	$table2=$_TABLES[strtoupper($pi_name).'_addition'];
	$table3=$_TABLES[strtoupper($pi_name).'_base'];

    $retval = '';

    $updatefields = explode("|", $fieldsetfields);
	$updateCount = count($updatefields);
	
	if ($updateCount > 0) {
		$sql="DELETE FROM {$table} ";
		$sql.=" WHERE fieldset_id = $fieldset_id";
		DB_query($sql);
		
        foreach ($updatefields as $field_id) {
            $field_id = COM_applyFilter($field_id, true);
			$sql="INSERT INTO {$table} ";
			$sql.=" (fieldset_id, field_id) VALUES ('$fieldset_id', $field_id)";
			DB_query($sql);
		}
		
		$sql="DELETE FROM {$table2} ";
		$sql.=" WHERE"; 
		$sql.=" id IN  (SELECT m.id FROM {$table3} AS m WHERE  fieldset_id={$fieldset_id})";
		$sql.=" AND field_id not IN (SELECT field_id FROM {$table} WHERE fieldset_id={$fieldset_id})";
		DB_query($sql);
		
	}
	$return_page=COM_refresh ($_CONF['site_admin_url']
		. '/plugins/'.THIS_SCRIPT.'?msg=1');
			
    return $return_page;

    //exit;
}

function LIB_ListGroups(
	$pi_name
	,$id
)
// +---------------------------------------------------------------------------+
// | 機能  group一覧表示
// | 書式 LIB_ListGroups($pi_name,$id)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// | 引数 $id:
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

    $table=$_TABLES[strtoupper($pi_name).'_def_fieldset_group'];
    $table2=$_TABLES[strtoupper($pi_name).'_def_group'];
	$tables = " {$table} AS t ,{$table2} AS t2";

    require_once( $_CONF['path_system'] . 'lib-admin.php' );

    $retval = '';
    //MENU1:管理画面
    $url2=$_CONF['site_url'] . '/admin/plugins/'.$pi_name.'/fieldset.php';
	
    $menu_arr[]=array('url' => $url2,'text' => $lang_box_admin['fieldsetlist']);
    $menu_arr[]=array('url' => $_CONF['site_admin_url'],'text' => $LANG_ADMIN['admin_home']);


    $retval .= COM_startBlock($lang_box_admin['admin_list'], '',
                              COM_getBlockTemplate('_admin_block', 'header'));

    $function="plugin_geticon_".$pi_name;
    $icon=$function();
    $retval .= ADMIN_createMenu(
        $menu_arr,
        $lang_box_admin['instructions'],
        $icon
    );

   //ヘッダ：編集～
    $header_arr[]=array('text' => $lang_box_admin['orderno'], 'field' => 'orderno', 'sort' => true);
    $header_arr[]=array('text' => $LANG_ADMIN['edit'], 'field' => 'editid', 'sort' => false);
    //$header_arr[]=array('text' => $LANG_ADMIN['copy'], 'field' => 'copy', 'sort' => false);
    $header_arr[]=array('text' => $lang_box_admin['group_id'], 'field' => 'group_id', 'sort' => true);
    $header_arr[]=array('text' => $lang_box_admin['name'], 'field' => 'name', 'sort' => true);
	
    //
    $text_arr = array('has_menu' =>  true,
      'has_extras'   => true,
      'form_url' => $_CONF['site_admin_url'] . "/plugins/databox/fieldset.php?mode=listgroups&id=".$id);

	//Query
    $sql = "SELECT ".LB;
    $sql .= " t2.group_id".LB;
    $sql .= " ,t2.name".LB;
    $sql .= " ,t2.orderno".LB;

    $sql .= " FROM ".$tables.LB;
	
    $sql .= " WHERE ".LB;
    $sql .= " t.fieldset_id=".$id.LB;
    $sql .= " AND t.group_id=t2.group_id".LB;
	//


    $query_arr = array(
        'table' => $tables,
        'sql' => $sql,
        'query_fields' => array('t2.group_id','name'),
        'default_filter' => $exclude);
    //デフォルトソート項目:
    $defsort_arr = array('field' => 't2.group_id', 'direction' => 'ASC');
    //List 取得
    //ADMIN_list($component, $fieldfunction, $header_arr, $text_arr,
    //       $query_arr, $menu_arr, $defsort_arr, $filter = '', $extra = '', $options = '')
    $retval .= ADMIN_list(
        $pi_name
        , "LIB_GetListField_groups"
        , $header_arr
        , $text_arr
        , $query_arr
        , $defsort_arr
        );

    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    return $retval;
}

function LIB_GetListField_Groups(
	$fieldname
	, $fieldvalue
	, $A
	, $icon_arr
)
// +---------------------------------------------------------------------------+
// | 一覧取得 ADMIN_list で使用
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    $LANG_ACCESS;

    $retval = '';

    switch($fieldname) {
        //編集アイコン
        case 'editid':
            $retval = "<a href=\"{$_CONF['site_admin_url']}";
            $retval .= "/plugins/databox/group.php";
            $retval .= "?mode=edit";
            $retval .= "&amp;id={$A['group_id']}\">";
            $retval .= "{$icon_arr['edit']}</a>";
            break;
        //各項目
        default:
            $retval = $fieldvalue;
            break;
    }

    return $retval;


}
function LIB_editgroups(
	$pi_name
	,$id
)
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

	//global  $_USER;
	global  $LANG_ACCESS;
	global  $LANG28;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';
	
	$table=$_TABLES[strtoupper($pi_name).'_def_fieldset'];
    $fieldset_name = DB_getItem($table, 'name', "fieldset_id = $id");

    $fieldset_listing_url=$_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT;
	
    //MENU1:管理画面
    $url2=$_CONF['site_url'] . '/admin/plugins/'.$pi_name.'/fieldset.php';
	
    $menu_arr[]=array('url' => $url2,'text' => $lang_box_admin['fieldsetlist']);
    $menu_arr[]=array('url' => $_CONF['site_admin_url'],'text' => $LANG_ADMIN['admin_home']);

    $retval .= COM_startBlock($lang_box_admin['admin_list']. " - $fieldset_name", '',
                              COM_getBlockTemplate('_admin_block', 'header'));

	
	$function="plugin_geticon_".$pi_name;
    $icon=$function();
    $retval .= ADMIN_createMenu(
        $menu_arr,
        $lang_box_admin['inst_fieldsetgroups'],
        $icon
    );
	
    $tmplfld=DATABOX_templatePath('admin','default',$pi_name);
    $templates = new Template($tmplfld);
	$templates->set_file('editor',"fieldset_groups.thtml");
	
	
    //--
	
	
	
    $templates->set_var('site_url', $_CONF['site_url']);
    $templates->set_var('site_admin_url', $_CONF['site_admin_url']);

    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);
    $templates->set_var('gltoken_name', CSRF_TOKEN);
    $templates->set_var('gltoken', $token);
	
	$templates->set_var ( 'xhtml', XHTML );

    $templates->set_var('script', THIS_SCRIPT);

	//
    $templates->set_var('lang_link_admin', $lang_box_admin['link_admin']);
    $templates->set_var('lang_link_admin_top', $lang_box_admin['link_admin_top']);

    $templates->set_var('LANG_fieldsetgroups',$lang_box_admin['fieldsetgroupsregistered']);
    $templates->set_var('fieldsetgroups', LIB_selectGroups($pi_name,$id, true));
	
	$templates->set_var('LANG_grouplist', $lang_box_admin['grouplist']);
	$templates->set_var('group_list', LIB_selectGroups($pi_name,$id));
	
    $templates->set_var('LANG_add',$LANG_ACCESS['add']);
    $templates->set_var('LANG_remove',$LANG_ACCESS['remove']);
    $templates->set_var('lang_save', $LANG_ADMIN['save']);
    $templates->set_var('lang_cancel', $LANG_ADMIN['cancel']);
	
    $templates->set_var('id',$id);
	
    $templates->parse('output', 'editor');
    $retval .= $templates->finish($templates->get_var('output'));
	
    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    return $retval;
}
function LIB_selectGroups(
	$pi_name
	, $fieldset_id
	, $selected = false)
{
	global $_TABLES;
	
	$table1=$_TABLES[strtoupper($pi_name).'_def_group'];
	$table2=$_TABLES[strtoupper($pi_name).'_def_fieldset_group'];

    $retval = '';

	// Get a list of additiongroups in the selected field
	$sql  = "SELECT DISTINCT group_id ";
	$sql  .= " FROM {$table2}";
	$sql  .= " WHERE  ";
	$sql  .= " fieldset_id = $fieldset_id";
	$result = DB_query ($sql);
    $num = DB_numRows ($result);
	$grouplist="";
	if ($num<>0){
		$selectedgroups = array();
		while ($A = DB_fetchArray($result)) {
			$selectedgroups[] = $A['group_id'];
		}
		$grouplist = '(' . implode (',', $selectedgroups) . ')';
	}
	
	//
	
	if  ($selected AND $grouplist==""){
	}else{
		$sql = "SELECT DISTINCT group_id,name ";
		$sql .= " FROM {$table1}";
		$sql .= " WHERE  group_id<>0 ";
		$sql .= " AND parent_flg = 0".LB;
	
		if ($grouplist<>""){
			$sql  .= "AND group_id ";
			if ($selected==FALSE) {
				$sql .= 'NOT ';
			}
			$sql .= "IN {$grouplist} ";
		}
	
		$sql .= " ORDER BY orderno";
		$result = DB_query ($sql);
		while ($A = DB_fetchArray($result)) {
		
            $group_id = COM_stripslashes($A['group_id']);
            $name = COM_stripslashes($A['name']);
			$retval .= '<option value="' . $group_id . '">' . $name . '</option>';
		}
	}
	return $retval;
}


function LIB_savegroups(
	$pi_name
	,$fieldset_id
)
{
	global $_CONF;
	global $_TABLES;
	
	$fieldsetgroups=$_POST['groupmembers'];
	$table=$_TABLES[strtoupper($pi_name).'_def_fieldset_group'];
	$table2=$_TABLES[strtoupper($pi_name).'_category'];
	$table3=$_TABLES[strtoupper($pi_name).'_base'];
	$table4=$_TABLES[strtoupper($pi_name).'_def_category'];

    $retval = '';

    $updategroups = explode("|", $fieldsetgroups);
	$updateCount = count($updategroups);
	if ($updateCount > 0) {
		$sql="DELETE FROM {$table} ";
		$sql.=" WHERE fieldset_id = $fieldset_id";
		DB_query($sql);
		
        foreach ($updategroups as $group_id) {
            $group_id = COM_applyFilter($group_id, true);
			$sql="INSERT INTO {$table} ";
			$sql.=" (fieldset_id, group_id) VALUES ('$fieldset_id', $group_id)";
			DB_query($sql);
		}
		$sql="DELETE FROM {$table2} ";
		$sql.=" WHERE"; 
		$sql.=" id IN  (SELECT m.id FROM {$table3} AS m WHERE  fieldset_id={$fieldset_id})";
		$sql.=" AND category_id not IN ";
		$sql.=" ( SELECT category_id FROM {$table}  as t,{$table4} as t4 ";
		$sql.=" WHERE t.fieldset_id={$fieldset_id} AND  t4.categorygroup_id=t.group_id)";
		$sql.=" AND category_id not IN "; 
		$sql.=" ( SELECT parent_id FROM {$table} as t,{$table4} as t4";
		$sql.=" WHERE t.fieldset_id={$fieldset_id} AND  t4.categorygroup_id=t.group_id AND parent_id<>0)";
		DB_query($sql);
		
	}
	$return_page=COM_refresh ($_CONF['site_admin_url']
		. '/plugins/'.THIS_SCRIPT.'?msg=1');
			
    return $return_page;

    //exit;
}
// +---------------------------------------------------------------------------+
// | 機能 テンプレートディレクトリの選択入力ｈｔｍｌ
// | 書式 LIB_templatesdirectory ($pi_name,$defaulttemplatesdirectory)
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
function LIB_templatesdirectory (
    $pi_name
	,$defaulttemplatesdirectory
){

    global $_CONF;
    global $_TABLES;
    global $_USER ;

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;
	
	if  (strtoupper($pi_name)=="DATABOX"){
		$fld="data";
	}else{
		$fld="profile";
	}
		
    //
    $selection = '<select id="defaulttemplatesdirectory" name="defaulttemplatesdirectory">' . LB;
	$selection .= "<option value=\"\">  </option>".LB;

    //
	if ($box_conf['templates']==="theme"){
		$fd1=$_CONF['path_layout'].$box_conf['themespath'].$fld."/";
	}else if ($box_conf['templates']==="custom"){
		$fd1=$_CONF['path'] .'plugins/'.$pi_name.'/custom/templates/'.$fld.'/';
    }else{
        $fd1=$_CONF['path'] .'plugins/'.$pi_name.'/templates/'.$fld.'/';
    }

    if( is_dir( $fd1)){
        $fd = opendir( $fd1 );
        $dirs= array();
        $i = 1;
        while(( $dir = @readdir( $fd )) == TRUE )    {
            if( is_dir( $fd1 . $dir)
                    && $dir <> '.'
                    && $dir <> '..'
                    && $dir <> 'CVS'
                    && substr( $dir, 0 , 1 ) <> '.' ) {
                clearstatcache();
                $dirs[$i] = $dir;
                $i++;
            }
        }

        usort($dirs, 'strcasecmp');

        foreach ($dirs as $dir) {
            $selection .= '<option value="' . $dir . '"';
            if ($defaulttemplatesdirectory == $dir) {
                $selection .= ' selected="selected"';
            }
            $words = explode('_', $dir);
            $bwords = array();
            foreach ($words as $th) {
                if ((strtolower($th[0]) == $th[0]) &&
                    (strtolower($th[1]) == $th[1])) {
                    $bwords[] = ucfirst($th);
                } else {
                    $bwords[] = $th;
                }
            }
            $selection .= '>' . implode(' ', $bwords) . '</option>' . LB;
        }
    }else{
        $selection .= '<option value="default"';
        $selection .= ' selected="selected"';
        $selection .= '>Default</option>' . LB;
    }

    $selection .= '</select>';

    return $selection;

}

?>