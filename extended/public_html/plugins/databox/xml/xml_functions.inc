<?php

if (strpos ($_SERVER['PHP_SELF'], 'xml_functions.inc') !== false) {
    die ('This file can not be used on its own.');
}

function fnc_Menu(
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能  menu表示  
// | 書式 fnc_Menu("databox")
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

    $url1=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=init';

    $url10=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=list';
    $url11=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=new';
    $url12=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=exportform';
    $url13=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=importform';
    //
    $menu_arr[]=array('url' => $url10,'text' => $lang_box_admin['xml_def'].$lang_box_admin['list']);
    $menu_arr[]=array('url' => $url11,'text' => $lang_box_admin['xml_def'].$lang_box_admin['new']);
    $menu_arr[]=array('url' => $url12,'text' => $lang_box_admin['export']);
    $menu_arr[]=array('url' => $url13,'text' => $lang_box_admin['import']);
    $menu_arr[]=array('url' => $url1,'text' => $lang_box_admin['xml_def'].$lang_box_admin['init']);
    //
    $menu_arr[]=array('url' => $_CONF['site_admin_url'],'text' => $LANG_ADMIN['admin_home']);
    
    
    $function="plugin_geticon_".$pi_name;
    $icon=$function();

    $retval .= ADMIN_createMenu(
        $menu_arr
        ,$lang_box_admin['instructions']
        ,$icon
    );
    
    return $retval;
}


function fnc_List(
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能  一覧表示
// | 書式 fnc_List($pi_name)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:一覧
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $_TABLES;
    global $LANG_ADMIN;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

     $table=$_TABLES[strtoupper($pi_name).'_def_xml'];
     $sql="SELECT * FROM A".$table;
     //echo "sql=".$sql."<br>";
     //$w=@mysql_query($sql , $err);
     //echo "w=".$w."<br>";
     //echo "err=".$err."<br>";
     //if (@mysql_query("SELECT * FROM ".$table , $err)){
     //}else{
     //     return;
     //}

    require_once( $_CONF['path_system'] . 'lib-admin.php' );

    $retval = '';

    //ヘッダ：編集～
    $header_arr[]=array('text' => $lang_box_admin['seq'], 'field' => 'seq', 'sort' => true);
    $header_arr[]=array('text' => $LANG_ADMIN['edit'], 'field' => 'editid', 'sort' => false);
    $header_arr[]=array('text' => $lang_box_admin['tag'], 'field' => 'tag', 'sort' => true);

    $header_arr[]=array('text' => $lang_box_admin['value'], 'field' => 'value', 'sort' => true);

    $header_arr[]=array('text' => $lang_box_admin['field'], 'field' => 'field', 'sort' => true);
    $header_arr[]=array('text' => $lang_box_admin['field'], 'field' => 'field2', 'sort' => true);

    //
    $text_arr = array('has_menu' =>  true,
      'has_extras'   => true,
      'form_url' => $_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT."?mode=list");

    //Query
    $sql = "SELECT ";
    $sql .= " t.seq".LB;
    $sql .= " ,t.tag".LB;
    $sql .= " ,t.value".LB;
    $sql .= " ,t.field".LB;
    $sql .= " ,t.field AS field2".LB;

    $sql .= " FROM ";
    $sql .= " {$table} AS t".LB;
    $sql .= " WHERE ".LB;
    $sql .= " 1=1".LB;
    //
    $query_arr = array(
        'table' =>$table,
        'sql' => $sql,
        'query_fields' => array('t.seq','t.tag','t.value','t.field'
        ),
        'default_filter' => $exclude);
    //デフォルトソート項目:
    $defsort_arr = array('field' => 't.seq', 'direction' => 'ASC');

    //List 取得
    //ADMIN_list($component, $fieldfunction, $header_arr, $text_arr,
    //       $query_arr, $menu_arr, $defsort_arr, $filter = '', $extra = '', $options = '')
    $retval .= ADMIN_list(
        $pi_name
        , "fncGetListField"
        , $header_arr
        , $text_arr
        , $query_arr
        , $defsort_arr
        , $filter
        );


    return $retval;
}

function fnc_Edit(
    $pi_name
    ,$seq
    ,$msg = ''
    ,$errmsg=""
)
// +---------------------------------------------------------------------------+
// | 機能  編集画面表示
// | 書式 fnc_Edit($pi_name,$seq)
// | 書式 fnc_Edit($pi_name,$seq,$msg,$errmsg)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:
// | 引数 $seq:
// | 引数 $msg:メッセージ番号
// | 引数 $errmsg:エラーメッセージ
// +---------------------------------------------------------------------------+
// | 戻値 nomal:編集画面                                                       |
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $_TABLES;
    global $LANG_ADMIN;
    global $MESSAGE;
    global $_USER;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $table=$_TABLES[strtoupper($pi_name).'_def_xml'];

    $retval = '';

    $delflg=false;

    if (!empty ($msg)) {
        $retval .= COM_showMessage ($msg,$pi_name);
        $retval .= $errmsg;

        // clean 'em up
        $tag = COM_applyFilter($_POST['tag']);
        $value = COM_applyFilter($_POST['value']);
        $field = COM_applyFilter ($_POST['field']);

        $uuid=$_USER['uid'];

    }else{
        if (empty($seq)) {

            $seq=0;

            $tag = "";
            $value = "";

            $table_id = "";
            $field = "";

            $uuid=0;
            $udatetime="";//"";

        }else{
            $sql = "SELECT ";

            $sql .= " *";

            $sql .= " FROM ";
            $sql .= $table;
            $sql .= " WHERE ";
            $sql .= " seq = $seq";
            $result = DB_query($sql);

            $A = DB_fetchArray($result);

            $tag = COM_stripslashes($A['tag']);
            $value = COM_stripslashes($A['value']);
            $field = COM_stripslashes($A['field']);

            $uuid = COM_stripslashes($A['uuid']);
            $udatetime=COM_stripslashes($A['udatetime']);

            $delflg=true;
        }
    }


    $tmplfld=DATABOX_templatePath('admin','default',$pi_name);

    $templates = new Template($tmplfld);
    $templates->set_file('editor',"xml_editor.thtml");

    //--
    $templates->set_var('about_thispage', $lang_box_admin['about_admin_xml']);
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


    //seq
    $templates->set_var('lang_seq', $lang_box_admin['seq']);
    $templates->set_var('seq', $seq);


    //tag value field
    $templates->set_var('lang_tag', $lang_box_admin['tag']);
    $templates->set_var ('tag', $tag);

    $templates->set_var('lang_value', $lang_box_admin['value']);
    $templates->set_var ('value', $value);

    $templates->set_var('lang_field', $lang_box_admin['field']);
    $templates->set_var ('field', $field);

    $list_field=fnc_GetXmlFieldsList($pi_name,$field );
    $templates->set_var ('list_field', $list_field);


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
        $delbutton = '<input type="submit" value="' . $LANG_ADMIN['delete']
               . '" name="mode"%s>';
        $jsconfirm = ' onclick="return confirm(\'' . $MESSAGE[76] . '\');"';
            $templates->set_var ('delete_option',
                                      sprintf ($delbutton, $jsconfirm));
    }

    //
    $templates->parse('output', 'editor');
    $retval .= $templates->finish($templates->get_var('output'));
    $retval .= COM_endBlock (COM_getBlockTemplate ('_admin_block', 'footer'));

    return $retval;
}

function fnc_GetXmlFieldsList(
    $pi_name
    ,$selected
)
// +---------------------------------------------------------------------------+
// | 機能 get xmlfields optionlist html
// | 書式 fncgetxmlfieldslist($pi_name,$field)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:
// | 引数 $selected:
// +---------------------------------------------------------------------------+
// | 戻値 nomal:xmlfieldslist (optionlist html)
// +---------------------------------------------------------------------------+
{
    $retval = '';

    //
    $field_ary=array();
    fnc_GetFieldAry1($pi_name,$field_ary);
    fnc_GetFieldAry2($pi_name,$field_ary);
    
    //------
    $retval .= "<select name=field >".LB;
    $c=count($field_ary);
    if ($c>0){
        foreach( $field_ary as $fid => $fvalue ){
            $retval .= "<option value='{$fid}'";
            if ($fid == $selected) {
                $retval .= ' selected="selected"';
            }
            $retval .=        ">{$fvalue}";
            $retval .=    "</option>".LB;
        }
    }

    $retval .= '</select>';

    return $retval;
}

function fnc_GetFieldAry1(
     $pi_name
     ,&$field_ary
)
// +---------------------------------------------------------------------------+
// | 機能 get FieldAry
// | 書式 fnc_GetFieldAry1($pi_name,$field_ary)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:
// | 引数 $field_ary:
// +---------------------------------------------------------------------------+
{

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

     //base
    $field_ary['base:code']=$lang_box_admin['code'];
    $field_ary['base:title']=$lang_box_admin['title'];
    $field_ary['base:fieldset_id']=$lang_box_admin['fieldset_id'];

    return;

}

function fnc_GetFieldAry2(
    $pi_name
     ,&$field_ary
)
// +---------------------------------------------------------------------------+
// | 機能 get xmlfields optionlist html
// | 書式 fnc_GetFieldAry2($pi_name,$field_ary)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:
// | 引数 $field_ary:
// +---------------------------------------------------------------------------+
{
    global $_TABLES;

     //【ADDTION】-----------------------------------
     //00 一行テキスト 
     //10 複数行テキスト 
     //01 HTML（複数行テキスト）
     //20 TinyMCEモード（複数行テキスト）
     //07 オプションリスト（選択肢）
     //08 ラジオボタン（選択肢）
     //12 画像（ファイル保存） 6
     //09 オプションリスト（マスター）
    //16 ラジオボタン（マスター）

     $table=$_TABLES[strtoupper($pi_name).'_def_field'];
    $sql = "SELECT ".LB;
    $sql .= " field_id,name,type".LB;
    $sql .= " FROM {$table}".LB;
    $sql .= " WHERE type IN (0,10,1,20,7,8,12,9,16) ".LB;
    $sql .= " ORDER BY orderno".LB;

    $result = DB_query( $sql );
    $nrows = DB_numRows( $result );
    for( $i = 0; $i < $nrows; $i++ )    {
        $A = DB_fetchArray( $result, true );
          $type=$A['type'];
          $id=$A['field_id'];
        $name=$A['name'];
          $field_ary['addition:'.$id]=$name;
          if  ($type==9 OR $type==16){
               $field_ary['addition_v:'.$id]=$name."_value";
          }
    }
     
     //【CATEGORY】-----------------------------------------
    $table=$_TABLES[strtoupper($pi_name).'_def_category'];
    $sql = "SELECT ".LB;
    $sql .= " category_id,name".LB;
    $sql .= " FROM {$table}".LB;
    $sql .= " ORDER BY orderno".LB;

    $result = DB_query( $sql );
    $nrows = DB_numRows( $result );

    for( $i = 0; $i < $nrows; $i++ )    {
        $A = DB_fetchArray( $result, true );
        $id=$A['category_id'];
        $name=$A['name'];
        $field_ary['category:'.$id]=$name;
    }
     
     return;
}

function fnc_Save (
    $pi_name
    ,$navbarMenu
    ,$menuno

)
// +---------------------------------------------------------------------------+
// | 機能  保存
// | 書式 fnc_Save ($pi_name,,$navbarMenu,$menuno)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
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

    $table=$_TABLES[strtoupper($pi_name).'_def_xml'];

    $retval = '';

    // clean 'em up
    $seq = COM_applyFilter($_POST['seq'],true);

    $tag=COM_applyFilter($_POST['tag']);
    $tag = addslashes (COM_checkHTML (COM_checkWords ($tag)));
     $tag=mb_convert_kana($tag,"a");
     
    $value=COM_applyFilter($_POST['value']);
    $value = addslashes (COM_checkHTML (COM_checkWords ($value)));

    $field=COM_applyFilter($_POST['field']);
    $field= addslashes (COM_checkHTML (COM_checkWords ($field)));

    //-----
    $type=1;
    $uuid=$_USER['uid'];

    // CHECK　はじめ
    $err="";
    //ID コード
    if ($seq==0 ){
        //$err.=$lang_box_admin['err_uid']."<br/>".LB;
    }else{
        if (!is_numeric($seq) ){
            $err.=$lang_box_admin['err_id']."<br/>".LB;
        }
    }
    //tag必須
    if (empty($tag)){
        $err.=$lang_box_admin['err_tag']."<br/>".LB;
    }
    //field必須
    if (empty($field)){
        $err.=$lang_box_admin['err_field']."<br/>".LB;
    }
    //tag と field
    if ($tag==="" OR $field===""){
    }else{
         $cntsql="SELECT tag FROM {$table} ";
         $cntsql.=" WHERE ";
         $cntsql.=" tag='{$tag}' ";
         $cntsql.=" AND field='{$field}' ";

         $cntsql.=" AND seq<>{$seq}";
         $result = DB_query ($cntsql);
         $numrows = DB_numRows ($result);
         if ($numrows<>0 ) {
             $err.=$lang_box_admin['err_tag_w']."<br/>".LB;
         }
    }
    //field
    if ($field<>""){
         $cntsql="SELECT field FROM {$table} ";
         $cntsql.=" WHERE ";
         $cntsql.=" field='{$field}' ";
         $cntsql.=" AND seq<>{$seq}";
         $result = DB_query ($cntsql);
         $numrows = DB_numRows ($result);
         if ($numrows<>0 ) {
             $err.=$lang_box_admin['err_field_w']."<br/>".LB;
         }
    }


    //errorのあるとき
    if ($err<>"") {
        $retval .= fnc_Edit($pi_name,$id,3,$err);
        return $retval;

    }
    // CHECK　おわり

    if ($seq==0){
        $w=DB_getItem($table,"max(seq)","1=1");
        if ($w=="") {
            $w=0;
        }
        $seq=$w+1;
    }

    $fields="seq";
    $values="$seq";

    $fields.=",tag";
    $values.=",'$tag'";

    $fields.=",value";
    $values.=",'$value'";

    $fields.=",field";
    $values.=",'$field'";

    $fields.=",uuid";
    $values.=",$uuid";

    $fields.=",udatetime";
    $values.=",NOW( )";
    //


    DB_save($table,$fields,$values,$return_page);

    if ($box_conf['aftersave_admin']==='no') {
        $retval['title']=$lang_box_admin['piname'].$lang_box_admin['edit'];
        $retval['display'] .= fnc_Edit($pi_name,$seq,1);
        return $retval;
    }else if ($box_conf['aftersave_admin']==='list' 
                            OR $box_conf['aftersave_admin']==='item'){
            $url = $_CONF['site_admin_url'] . "/plugins/$pi_name/xml.php";
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
                    , 1);

    echo $return_page;


    exit;

}

function fnc_delete (
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能  削除
// | 書式 fnc_delete ($pi_name)
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

    $table=$_TABLES[strtoupper($pi_name).'_def_xml'];

    $seq = COM_applyFilter($_POST['seq'],true);

    // CHECK
    $err="";
    if ($err<>"") {
        $page_title=  $lang_box_admin['err'];
        $retval .= DATABOX_siteHeader($pi_name,'_admin',$page_title);
        $retval .= COM_startBlock ($lang_box_admin['err'], '',
                            COM_getBlockTemplate ('_msg_block', 'header'));
        $retval .= $err;
        $retval .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
        $retval .= DATABOX_siteFooter($pi_name,'_admin');
        return $retval;
    }

    //
    DB_delete ($table, 'seq', $seq);

    return COM_refresh ($_CONF['site_admin_url']
                        . '/plugins/'.THIS_SCRIPT.'?msg=2');
}

function fnc_importexport (
    $pi_name
    ,$mode
)
// +---------------------------------------------------------------------------+
// | 機能  インポートエキスポート画面表示
// | 書式 fnc_importexport ($pi_name,"import")
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
{
    global $_CONF;

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $tmpl = new Template ($_CONF['path'] . "plugins/".THIS_PLUGIN."/templates/admin/");
    $tmpl->set_file(array('import' => 'import_url.thtml'));

    $tmpl->set_var('site_admin_url', $_CONF['site_admin_url']);

    $tmpl->set_var('gltoken_name', CSRF_TOKEN);
    $tmpl->set_var('gltoken', SEC_createToken());
    $tmpl->set_var ( 'xhtml', XHTML );

    $tmpl->set_var('script', THIS_SCRIPT);

    if ($mode==="importform"){
        $tmpl->set_var('mode', 'import');
        $tmpl->set_var('defaultpath', $box_conf['path_xml']);
        $tmpl->set_var('help', $lang_box_admin['importxmlmsg']);
    }else{
        $tmpl->set_var('mode', 'export');
        $tmpl->set_var('defaultpath', $box_conf['path_xml_out']);
        $tmpl->set_var('help', $lang_box_admin['exportxmlmsg']);
    }

    $tmpl->set_var('default', $lang_box_admin['default']);
    $tmpl->set_var('submit', $lang_box_admin['submit']);

    $tmpl->parse ('output', 'import');
    $import = $tmpl->finish ($tmpl->get_var ('output'));

    $retval="";
    $retval .= $import;

    return $retval;
}


function fnc_GetDefXml(
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能  xml 交換定義取得
// | 書式 fnc_GetDefXml($pi_name)
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
{
     global $_TABLES;
     
     $field_ary=array();
     fnc_GetFieldAry1($pi_name,$field_ary);
     fnc_GetFieldAry2($pi_name,$field_ary);

    //-----
    $tbl1=$_TABLES[strtoupper($pi_name).'_def_xml'];

    //-----
    $sql = "SELECT ".LB;
    $sql .= " tag ".LB;
    $sql .= " ,value ".LB;
    $sql .= " ,field ".LB;
    $sql .= " ,seq ".LB;
    $sql .= " FROM ".LB;
    $sql .= " {$tbl1} AS t1 ".LB;
    $sql .= " ORDER BY seq ".LB;
    $result = DB_query ($sql);
    $cnt = DB_numRows ($result);

    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);

    $defxml=array();
    if ($numrows > 0) {
        for ($i = 0; $i < $numrows; $i++) {
            $A = DB_fetchArray ($result);

               
               $field=COM_applyFilter($A['field']);
               
               if ($field_ary[$field]<>""){
                    
                    $fld=explode(":",$field);
                    $defxml[$i]['type']=$fld[0];
                    $defxml[$i]['field']=$fld[1];
                    
                    $defxml[$i]['tag']=COM_applyFilter($A['tag']);
                    $defxml[$i]['value']=COM_applyFilter($A['value']);
               }
        }
    }

    return $defxml;

}


function fnc_init(
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能 初期化
// | 書式 fnc_init("databox")
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+

{

    global $_TABLES;
    global $_CONF;

    $_SQL =array();

    include_once($_CONF['path'] . 'plugins/databox/xml/sql/mysql_install_xml.php');

    //------------------------------------------------------------------
    for ($i = 1; $i <= count($_SQL); $i++) {
        $w=current($_SQL);
        DB_query(current($_SQL));
        next($_SQL);
    }
    if (DB_error()) {
        COM_errorLog("error DataBox table update ",1);
        return "initialize error";
    }

    COM_errorLog("Success - databox XML Initialize",1);
    return "initialize finish!";
}


function fnc_exportexec(
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能  エキスポート実行
// | 書式 fncexportexec ($pi_name)
// +---------------------------------------------------------------------------+
// | 戻値 nomal: 結果画面
// +---------------------------------------------------------------------------+

{
    global $_CONF;

    global $logfile;
    global $logmode;

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $importurl = COM_applyFilter($_POST['importurl']);

    if ($importurl===""){
        $importurl=$box_conf['path_xml_out'];
    }

    if  (file_exists($importurl) ){
        if ( is_dir($importurl) AND is_writable($importurl)){
            $fchk=true;
        }
    }else{
        $fchk=false;
    }


    $defxml=fnc_GetDefXml($pi_name);
    $cnt=count($defxml);
    if ($cnt>0){
        if  ($fchk){
            $rt=fncOutBox($pi_name,$importurl,$defxml);
            $dummy = DATABOX_OutLog( $rt ,$logfile,$logmode);
        }else{
            $rt=$importurl." ". $lang_box_admin['err_not_exist'];
            $dummy = DATABOX_OutLog( $rt ,$logfile,$logmode);
        }
    }else{
        $rt="xml def data not exist";
    }


    return $rt;

}

function fnc_importexec(
    $pi_name
)
/// +---------------------------------------------------------------------------+
// | 機能  インポート実行
// | 書式 fnc_importexec ($pi_name)
// +---------------------------------------------------------------------------+
// | 戻値 nomal: 結果画面
// +---------------------------------------------------------------------------+

{
    global $_CONF;

    global $logfile;
    global $logmode;

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $importurl = COM_applyFilter($_POST['importurl']);

    if ($importurl===""){
        $importurl=$box_conf['path_xml'];
    }


    if (@fopen($importurl,'r')){
        $fchk=true;
        //fclose($importurl);
    }else{
        if  (file_exists($importurl)){
            $fchk=true;
        }else{
            $fchk=false;
        }
    }
     $cnt_ok=0;
     $cnt_ng=0;

    $defxml=fnc_GetDefXml($pi_name);
    $cnt=count($defxml);
    if ($cnt>0){

        if  ($fchk){
            if (is_dir($importurl)){
                $fd=$importurl;
                $files=DATABOX_getfilelist($fd,"xml");
                for ($i = 1; $i <= count($files); $i++) {
                    $fl=$fd."/".current($files);

                    $logentry=$fl;
                    $dummy = DATABOX_OutLog( $logentry ,$logfile,$logmode);
                    $rt1=databox_xmlimport($pi_name,$fl,$defxml);
                    
                    $cnt_ok=$cnt_ok+$rt1['ok'];
                    $cnt_ng=$cnt_ng+$rt1['ng'];
                    $wmsg=current($files)." ".$rt1['msg'];
                    $dummy = DATABOX_OutLog( $wmsg ,$logfile,$logmode);
                    $rt.=$wmsg."<br>";
                    next($files);
                }
            }else{
                //$rt=fncimportexec2($pi_name,$importurl,$defxml);
                $rt1=databox_xmlimport($pi_name,$fl,$defxml);
                
                $cnt_ok=$cnt_ok+$rt1['ok'];
                $cnt_ng=$cnt_ng+$rt1['ng'];
                $rt=$rt1['msg']."<br>";
                $dummy = DATABOX_OutLog( $rt['msg'] ,$logfile,$logmode);
            }
        }else{
            $rt=$importurl." ". $lang_box_admin['err_not_exist'];
            $dummy = DATABOX_OutLog( $rt ,$logfile,$logmode);
        }
    }else{
        $rt="xml def data not exist";
     }
     
     $rt.=$LANG_DATABOX_ADMIN['jobend'];
     $rt.=sprintf ($LANG_DATABOX_ADMIN['cnt_ok'] , $cnt_ok);
     $rt.=sprintf ($LANG_DATABOX_ADMIN['cnt_ng'] , $cnt_ng);

    return $rt;

}

//追加項目の編集
function fnc_getfieldvalue2(
    $value
    ,$type
    ,$selectionary
     ,$langf_box_noyes
     ,$selectlist
     ,$xmltype
){
//インポート設定できるアトリビュートのタイプは限定しています
//    00 一行テキスト
//    10 複数行テキスト
//    01 HTML（複数行テキスト）
//    20 TinyMCEモード（複数行テキスト）
//    07 オプションリスト（選択肢）
//    08 ラジオボタン（選択肢）
//    12 画像（ファイル保存） 
     
//    09 オプションリスト（マスター）
//    16 ラジオボタン（マスター）
     
     global $_TABLES;
     $pi_name="databox";
     
     switch ($type) {
        case 9://オプションリスト（マスター）
        case 16://ラジオボタン（マスター）
               $wkary=explode(":",$value);
               $wkvalue=$wkary[0];
               $wkvalue2=$wkary[2];
               $table_mst=$_TABLES[strtoupper($pi_name).'_mst'];
               //no で検索
               if  ($xmltype=="addition"){
                    if  (is_numeric($value)){
                         $retval=DB_getItem( $table_mst  ,"no","kind='{$selectlist}' AND no={$value}");
                    }else{
                         $retval="";
                    }
               //value で検索
               }else{
                    $retval=DB_getItem( $table_mst  ,"no","kind='{$selectlist}' AND value='{$value}'");
               }
               $fieldvalue=$retval;
            break;
        default:
            $fieldvalue = $value;
        }

    return $fieldvalue;
}



?>
