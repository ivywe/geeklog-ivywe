<?php

if (strpos ($_SERVER['PHP_SELF'], 'lib_group.php') !== false) {
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

    $table=$_TABLES[strtoupper($pi_name).'_def_group'];

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
    $header_arr[]=array('text' => $lang_box_admin['orderno'], 'field' => 'orderno', 'sort' => true);
    $header_arr[]=array('text' => $LANG_ADMIN['edit'], 'field' => 'editid', 'sort' => false);
    $header_arr[]=array('text' => $LANG_ADMIN['copy'], 'field' => 'copy', 'sort' => false);
    $header_arr[]=array('text' => $lang_box_admin['group_id'], 'field' => 'group_id', 'sort' => true);
    $header_arr[]=array('text' => $lang_box_admin['code'], 'field' => 'code', 'sort' => true);
    $header_arr[]=array('text' => $lang_box_admin['name'], 'field' => 'name', 'sort' => true);

    //
    $text_arr = array('has_menu' =>  true,
      'has_extras'   => true,
      'form_url' => $_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT);

    //Query
    $sql = "SELECT ";
    $sql .= " group_id";
    $sql .= " ,code";
    $sql .= " ,name";
    $sql .= " ,orderno";
    $sql .= " FROM ";
    $sql .= " {$table} AS t";
    $sql .= " WHERE ";
    $sql .= " group_id<>0";
    //

    $query_arr = array(
        'table' => $table,
        'sql' => $sql,
        'query_fields' => array('group_id','code','name','orderno'),
        'default_filter' => $exclude);
    //デフォルトソート項目:
    $defsort_arr = array('field' => 'orderno', 'direction' => 'ASC');
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
            $retval .= "&amp;id={$A['group_id']}\">";
            $retval .= "{$icon_arr['edit']}</a>";
            break;
        case 'copy':
            $url=$_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT;
            $url.="?";
            $url.="mode=copy";
            $url.="&amp;id={$A['group_id']}";
            $retval = COM_createLink($icon_arr['copy'],$url);
            break;
        case 'code':
            $name=COM_applyFilter($A['code']);
            $url=$_CONF['site_url'] . "/".THIS_SCRIPT2;
            $url.="?";
            $url.="gcode=".$A['code'];
            $url.="&amp;m=gcode";
            $url = COM_buildUrl( $url );
            $retval= COM_createLink($name, $url);
            break;
        case 'group_id':
            $name=COM_applyFilter($A['group_id']);
            $url=$_CONF['site_url'] . "/".THIS_SCRIPT2;
            $url.="?";
            $url.="gid=".$A['group_id'];
            $url.="&amp;m=gid";
            $url = COM_buildUrl( $url );
            $retval= COM_createLink($name, $url);
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

    $lang_box_inputtype="LANG_".strtoupper($pi_name)."_INPUTTYPE";
    global $$lang_box_inputtype;
    $lang_box_inputtype=$$lang_box_inputtype;

    $table=$_TABLES[strtoupper($pi_name).'_def_group'];
    $table1=$_TABLES[strtoupper($pi_name).'_def_category'];
    $table2=$_TABLES[strtoupper($pi_name).'_def_field'];

//        $cur_year = date( 'Y' );
//        $year_startoffset=1990 - $cur_year +1;
//        $year_endoffset=0;

    $retval = '';


    $delflg=false;

    //メッセージ表示
    if (!empty ($msg)) {
        $retval .= COM_showMessage ($msg,$pi_name);
        $retval .= $errmsg;

        // clean 'em up
        $code = COM_applyFilter($_POST['code']);
        $name = COM_applyFilter($_POST['name']);
        $description = $_POST['description'];//COM_applyFilter($_POST['description']);

        $orderno = COM_applyFilter ($_POST['orderno']);
        $parent_flg = COM_applyFilter ($_POST['parent_flg'],true);
        $input_type = COM_applyFilter ($_POST['input_type'],true);

        $uuid=$_USER['uid'];

    }else{
        if (empty($id)) {

            $id=0;

            $code ="";
            $name ="";
            $description ="";

            $orderno ="";
            $parent_flg =0;

            $uuid=0;
            $udatetime="";//"";

        }else{
            $sql = "SELECT ";

            $sql .= " *";
			$sql .= " ,UNIX_TIMESTAMP(udatetime) AS udatetime_un".LB;

            $sql .= " FROM ";
            $sql .= $table;
            $sql .= " WHERE ";
            $sql .= " group_id = $id";
            $result = DB_query($sql);

            $A = DB_fetchArray($result);

            $code = COM_stripslashes($A['code']);
            $name = COM_stripslashes($A['name']);
            $description = COM_stripslashes($A['description']);
            $orderno=COM_stripslashes($A['orderno']);
            $parent_flg=COM_stripslashes($A['parent_flg']);
            $input_type=COM_stripslashes($A['input_type']);

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
    $templates->set_file('editor',"group_editor.thtml");
    //--
    $templates->set_var('about_thispage', $lang_box_admin['about_admin_group']);
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
    $templates->set_var('lang_group_id', $lang_box_admin['group_id']);
    $templates->set_var('id', $id);

    //コード、名前＆説明
    $templates->set_var('lang_code', $lang_box_admin['code']);
    $templates->set_var ('code', $code);
    $templates->set_var('lang_name', $lang_box_admin['name']);
    $templates->set_var ('name', $name);
    $templates->set_var('lang_description', $lang_box_admin['description']);
    $templates->set_var ('description', $description);

    //順番
    $templates->set_var('lang_orderno', $lang_box_admin['orderno']);
    $templates->set_var ('orderno', $orderno);

    //親ブループ?
    $templates->set_var('lang_parent_flg', $lang_box_admin['parent_flg']);
    $list_parent_flg=DATABOX_getradiolist ($lang_box_noyes,"parent_flg",$parent_flg);
    $templates->set_var( 'list_parent_flg', $list_parent_flg);

    //入力タイプ
    $templates->set_var('lang_input_type', $lang_box_admin['input_type']);
    $list_input_type=DATABOX_getradiolist ($lang_box_inputtype,"input_type",$input_type);
    $templates->set_var( 'list_input_type', $list_input_type);

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
        $wkcnt=DB_count($table1,"categorygroup_id",$id);
        if ($wkcnt>0){
            $templates->set_var('lang_delete_help', $lang_box_admin['delete_help_group']);
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
	
	$lang_box_inputtype="LANG_".strtoupper($pi_name)."_INPUTTYPE";
    global $$lang_box_inputtype;
    $lang_box_inputtype=$$lang_box_inputtype;

    $table=$_TABLES[strtoupper($pi_name).'_def_group'];

    $retval = '';

    // clean 'em up
    $id = COM_applyFilter($_POST['id'],true);

    $code=COM_applyFilter($_POST['code']);
    $code = addslashes (COM_checkHTML (COM_checkWords ($code)));

    $name=COM_applyFilter($_POST['name']);
    $name = addslashes (COM_checkHTML (COM_checkWords ($name)));

    $description=$_POST['description'];//COM_applyFilter($_POST['description']);
    $description = addslashes (COM_checkHTML (COM_checkWords ($description)));

    $parent_flg=COM_applyFilter($_POST['parent_flg'],true);
	
	$input_type=COM_applyFilter($_POST['input_type'],true);

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
         $cntsql.=" AND group_id<>{$id}";
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
		$retval['title']=$lang_box_admin['piname'].$lang_box_admin['edit'];
        $retval['display']= LIB_Edit($pi_name,$id, $edt_flg,3,$err);
        return $retval;

    }

    // CHECK　おわり

    if ($id==0){
        $w=DB_getItem($table,"max(group_id)","1=1");
        if ($w=="") {
            $w=0;
        }
        $id=$w+1;
    }

    $fields="group_id";
    $values="$id";

    $fields.=",code";
    $values.=",'$code'";

    $fields.=",name";
    $values.=",'$name'";

    $fields.=",description";
    $values.=",'$description'";

    $fields.=",orderno";//
    $values.=",$orderno";

    $fields.=",parent_flg";//
    $values.=",$parent_flg";
	
    $fields.=",input_type";//
    $values.=",$input_type";

    $fields.=",uuid";
    $values.=",$uuid";

    $fields.=",udatetime";
    $values.=",NOW( )";
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
            $url = $_CONF['site_admin_url'] . "/plugins/$pi_name/group.php";
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

    $table=$_TABLES[strtoupper($pi_name).'_def_group'];

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
    DB_delete ($table, 'group_id', $id);

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

    $table=$_TABLES[strtoupper($pi_name).'_def_group'];

    require_once ($_CONF['path'].'plugins/'.$pi_name.'/lib/comj_dltbldt.php');

    // 項目の見出リスト
    $fld = array ();


$fld['group_id']['name']  = $lang_box_admin['group_id'];
$fld['code']['name']  = $lang_box_admin['code'];
$fld['name']['name']  = $lang_box_admin['name'];
$fld['description']['name']  = $lang_box_admin['description'];

$fld['orderno']['name']  = $lang_box_admin['orderno'];

$fld['udatetime']['name']  = $lang_box_admin['udatetime'];
$fld['uuid']['name']  = $lang_box_admin['uuid'];

//----------------------
$filenm=$pi_name."_group";
$tbl ="{$table}";
$where = "";
$order = "group_id";

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


    $table=$_TABLES[strtoupper($pi_name).'_def_group'];



    $retval = '';


    $sql = "SELECT ";

    $sql .= " *";

    $sql .= " FROM ";
    $sql .= $_TABLES[strtoupper($pi_name).'_def_group'];
    $sql .= " WHERE ";
    $sql .= " group_id = $id";



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
        $msg.= $_CONF['site_url'] . '/'.THIS_SCRIPT.'?id=' .$A['group_id'].LB;

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


?>