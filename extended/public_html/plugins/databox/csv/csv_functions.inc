<?php

if (strpos ($_SERVER['PHP_SELF'], 'csv_functions.inc') !== false) {
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
	
    $url20=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=sellist';
    $url21=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=selnew';
    $url24=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=csvscheduled';
    //
    $menu_arr[]=array('url' => $url10,'text' => $lang_box_admin['csv_def'].$lang_box_admin['list']);
    $menu_arr[]=array('url' => $url11,'text' => $lang_box_admin['csv_def'].$lang_box_admin['new']);
    $menu_arr[]=array('url' => $url12,'text' => $lang_box_admin['export']);
    $menu_arr[]=array('url' => $url13,'text' => $lang_box_admin['import']);
    $menu_arr[]=array('url' => $url1,'text' => $lang_box_admin['init']);
    //
    $menu_arr[]=array('url' => $url20,'text' => $lang_box_admin['csv_select'].$lang_box_admin['list']);
    $menu_arr[]=array('url' => $url21,'text' => $lang_box_admin['csv_select'].$lang_box_admin['new']);
    $menu_arr[]=array('url' => $url24,'text' => $lang_box_admin['schedule']);
    //
    $menu_arr[]=array('url' => $_CONF['site_admin_url'],'text' => $LANG_ADMIN['admin_home']);
       
       
    $function="plugin_geticon_".$pi_name;
    $icon=$function();

    $retval .= ADMIN_createMenu(
        $menu_arr
        ,$lang_box_admin['instructions']
        ,$icon
       );
       
       $attr = array('class' => 'admin-menu-item');
       
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

       $table=$_TABLES[strtoupper($pi_name).'_def_csv'];
       $sql="SELECT * FROM A".$table;
       //echo "sql=".$sql."<br>";
       //$w=@mysql_query($sql , $err);
       //echo "w=".$w."<br>";
       //echo "err=".$err."<br>";
       //if (@mysql_query("SELECT * FROM ".$table , $err)){
       //}else{
       //       return;
       //}

    require_once( $_CONF['path_system'] . 'lib-admin.php' );

    $retval = '';

    //ヘッダ：編集～
    $header_arr[]=array('text' => $lang_box_admin['seq'], 'field' => 'seq', 'sort' => true);
    $header_arr[]=array('text' => $LANG_ADMIN['edit'], 'field' => 'editid', 'sort' => false);
    $header_arr[]=array('text' => $lang_box_admin['csvheader'], 'field' => 'csvheader', 'sort' => true);

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
    $sql .= " ,t.csvheader".LB;
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
        'query_fields' => array('t.seq','t.csvheader','t.value','t.field'
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

    $table=$_TABLES[strtoupper($pi_name).'_def_csv'];

    $retval = '';

    $delflg=false;

    if (!empty ($msg)) {
        $retval .= COM_showMessage ($msg,$pi_name);
        $retval .= $errmsg;

        // clean 'em up
              $csvheader = COM_applyFilter($_POST['csvheader']);
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

            $uuid=2;
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

            $csvheader = COM_stripslashes($A['csvheader']);
            $value = COM_stripslashes($A['value']);
            $field = COM_stripslashes($A['field']);

            $uuid = COM_stripslashes($A['uuid']);
            $udatetime=COM_stripslashes($A['udatetime']);

            $delflg=true;
        }
    }


    $tmplfld=DATABOX_templatePath('admin','default',$pi_name);

    $templates = new Template($tmplfld);
    $templates->set_file('editor',"csv_editor.thtml");

    //--
    $templates->set_var('about_thispage', $lang_box_admin['about_admin_csv']);
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
    $templates->set_var('lang_csvheader', $lang_box_admin['csvheader']);
    $templates->set_var ('csvheader', $csvheader);
    $templates->set_var ('help_csvheader', $lang_box_admin['help_csvheader']);

    $templates->set_var('lang_value', $lang_box_admin['value']);
    $templates->set_var ('value', $value);
    $templates->set_var ('help_value', $lang_box_admin['help_value_csv']);

    $templates->set_var('lang_field', $lang_box_admin['field']);
    $templates->set_var ('field', $field);
    $templates->set_var ('help_field', $lang_box_admin['help_field_csv']);

    $list_field=fnc_GetCSVFieldsList($pi_name,$field );
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

    return $retval;
}

function fnc_GetCSVFieldsList(
    $pi_name
    ,$selected
)
// +---------------------------------------------------------------------------+
// | 機能 get csvfields optionlist html
// | 書式 fncgetcsvfieldslist($pi_name,$field)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:
// | 引数 $selected:
// +---------------------------------------------------------------------------+
// | 戻値 nomal:csvfieldslist (optionlist html)
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
    $field_ary['base:code']="<".$lang_box_admin['basicfields'].">".$lang_box_admin['code'];
    $field_ary['base:title']="<".$lang_box_admin['basicfields'].">".$lang_box_admin['title'];
    $field_ary['base:fieldset_id']="<".$lang_box_admin['basicfields'].">".$lang_box_admin['fieldset_id'];

    $field_ary['base:released']="<".$lang_box_admin['basicfields'].">".$lang_box_admin['released'];
    $field_ary['base:expired']="<".$lang_box_admin['basicfields'].">".$lang_box_admin['expired'];
	
    $field_ary['base:id']="<".$lang_box_admin['basicfields'].">".$lang_box_admin['id'];
    $field_ary['base:draft_flag']="<".$lang_box_admin['basicfields'].">".$lang_box_admin['draft'];
    $field_ary['base:page_title']="<".$lang_box_admin['basicfields'].">".$lang_box_admin['page_title'];
    $field_ary['base:description']="<".$lang_box_admin['basicfields'].">".$lang_box_admin['description'];
    $field_ary['base:defaulttemplatesdirectory']="<".$lang_box_admin['basicfields'].">".$lang_box_admin['defaulttemplatesdirectory'];
    $field_ary['base:owner_id']="<".$lang_box_admin['basicfields'].">".$lang_box_admin['owner_id'];
	
    return;

}

function fnc_GetFieldAry2(
    $pi_name
       ,&$field_ary
)
// +---------------------------------------------------------------------------+
// | 機能 get csvfields optionlist html
// | 書式 fnc_GetFieldAry2($pi_name,$field_ary)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:
// | 引数 $field_ary:
// +---------------------------------------------------------------------------+
{
    global $_TABLES;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

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
    //15 数値
    //21 通貨

    $table=$_TABLES[strtoupper($pi_name).'_def_field'];
    $sql = "SELECT ".LB;
    $sql .= " field_id,name,type".LB;
    $sql .= " FROM {$table}".LB;
    $sql .= " WHERE type IN (0,10,1,20,7,8,12,9,16,15,21) ".LB;
    $sql .= " ORDER BY orderno".LB;

    $result = DB_query( $sql );
    $nrows = DB_numRows( $result );
    for( $i = 0; $i < $nrows; $i++ )    {
        $A = DB_fetchArray( $result, true );
              $type=$A['type'];
              $id=$A['field_id'];
        $name=$A['name'];
              $field_ary['addition:'.$id]="<".$lang_box_admin['additionfields'].">".$name;
              if  ($type==9 OR $type==16){
                     $field_ary['addition_v:'.$id]="<".$lang_box_admin['additionfields'].">".$name."_value";
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
        $field_ary['category:'.$id]="<".$lang_box_admin['category'].">".$name;
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

    $table=$_TABLES[strtoupper($pi_name).'_def_csv'];

    $retval = '';

    // clean 'em up
    $seq = COM_applyFilter($_POST['seq'],true);

    $csvheader=COM_applyFilter($_POST['csvheader']);
    $csvheader = addslashes (COM_checkHTML (COM_checkWords ($csvheader)));
    $csvheader=mb_convert_kana($csvheader,"a");
       
    $value=COM_applyFilter($_POST['value']);
    $value = addslashes (COM_checkHTML (COM_checkWords ($value)));

    $field=COM_applyFilter($_POST['field']);
    $field= addslashes (COM_checkHTML (COM_checkWords ($field)));

    //-----
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
    //csvheader必須
    if (empty($csvheader)){
        $err.=$lang_box_admin['err_csvheader']."<br/>".LB;
    }
    //field必須
    if (empty($field)){
        $err.=$lang_box_admin['err_field']."<br/>".LB;
    }
    //csvheader と field
    if ($csvheader=="" OR $field===""){
    }else{
         $cntsql="SELECT csvheader FROM {$table} ";
         $cntsql.=" WHERE ";
         $cntsql.=" csvheader='{$csvheader}' ";
         $cntsql.=" AND field='{$field}' ";

         $cntsql.=" AND seq<>{$seq}";
         $result = DB_query ($cntsql);
         $numrows = DB_numRows ($result);
         if ($numrows<>0 ) {
             $err.=$lang_box_admin['err_csvheader_w']."<br/>".LB;
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
        //$page_title=$lang_box_admin['piname'].$lang_box_admin['edit'];

        //$retval .= COM_siteHeader ('menu', $page_title);

        //$retval .=ppNavbarjp($navbarMenu,$lang_box_admin_menu[$menuno]);
        $retval .= fnc_Edit($pi_name,$id,3,$err);
        //$display .= COM_siteFooter ();

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

    $fields.=",csvheader";
    $values.=",'$csvheader'";

    $fields.=",value";
    $values.=",'$value'";

    $fields.=",field";
    $values.=",'$field'";

    $fields.=",uuid";
    $values.=",$uuid";

    $fields.=",udatetime";
    $values.=",NOW()";
    //


    DB_save($table,$fields,$values,$return_page);

    if ($box_conf['aftersave_admin']==='no') {
        $retval['title']=$lang_box_admin['piname'].$lang_box_admin['edit'];
        $retval['display'] .= fnc_Edit($pi_name,$seq,1);
        return $retval;
    }else if ($box_conf['aftersave_admin']==='list' 
                            OR $box_conf['aftersave_admin']==='item'){
            $url = $_CONF['site_admin_url'] . "/plugins/$pi_name/csv.php";
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

    $table=$_TABLES[strtoupper($pi_name).'_def_csv'];

    $seq = COM_applyFilter($_POST['seq'],true);

    // CHECK

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
        $tmpl->set_var('defaultpath', $box_conf['path_csv']);
        $tmpl->set_var('help', $lang_box_admin['importmsgcsv']);
    }else{
        $tmpl->set_var('mode', 'export');
        $tmpl->set_var('defaultpath', $box_conf['path_csv_out']);
        $tmpl->set_var('help', $lang_box_admin['exportmsgcsv']);
    }

    $tmpl->set_var('default', $lang_box_admin['default']);
    $tmpl->set_var('submit', $lang_box_admin['submit']);

    $tmpl->parse ('output', 'import');
    $import = $tmpl->finish ($tmpl->get_var ('output'));

    $retval="";
    $retval .= $import;

    return $retval;
}
function fnc_importform (
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能  インポート画面表示
// | 書式 fnc_importform ($pi_name)
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $_TABLES;
    $table=$_TABLES[strtoupper($pi_name).'_def_csv_sel'];
    
    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $tmpl = new Template ($_CONF['path'] . "plugins/".THIS_PLUGIN."/templates/admin/");
    $tmpl->set_file(array('import' => 'csvimportform.thtml'));

    $tmpl->set_var('site_admin_url', $_CONF['site_admin_url']);

    $tmpl->set_var('gltoken_name', CSRF_TOKEN);
    $tmpl->set_var('gltoken', SEC_createToken());
    $tmpl->set_var ( 'xhtml', XHTML );

    $tmpl->set_var('script', THIS_SCRIPT);
	
    $tmpl->set_var('lang_unlink', $lang_box_admin['unlinkafterimport']);
    $tmpl->set_var('lang_import', $lang_box_admin['import']);
	if  (DB_count($table)==0){
        $tmpl->set_var('lang_sel', '');
        $tmpl->set_var('list_sel', '<input type="hidden" value="0" name="csvsel" '.XHTML.'>');
    }else{
        $tmpl->set_var('lang_sel', $lang_box_admin['csv_select']);
        $list_sel=DATABOX_getoptionlist("csvsel",0,0,$pi_name,"",FALSE );
        $tmpl->set_var('list_sel', $list_sel);
    }
    $tmpl->set_var('mode', 'import');
    $tmpl->set_var('defaultpath', $box_conf['path_csv']);
    $tmpl->set_var('help', sprintf ($lang_box_admin['importmsgcsv'],$box_conf['path_csv']));
	
    $tmpl->set_var('default', $lang_box_admin['default']);
    $tmpl->set_var('submit', $lang_box_admin['submit']);

    $tmpl->parse ('output', 'import');
    $import = $tmpl->finish ($tmpl->get_var ('output'));

    $retval="";
    $retval .= $import;

    return $retval;
}
function fnc_GetDefCsv(
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能  csv 交換定義取得
// | 書式 fnc_GetDefCsv($pi_name)
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
{
    global $_TABLES;
    
    $field_ary=array();
    fnc_GetFieldAry1($pi_name,$field_ary);
    fnc_GetFieldAry2($pi_name,$field_ary);

    //-----
    $tbl1=$_TABLES[strtoupper($pi_name).'_def_csv'];

    //-----
    $sql = "SELECT ".LB;
    $sql .= " csvheader ".LB;
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

    $defcsv=array();
    if ($numrows > 0) {
        for ($i = 0; $i < $numrows; $i++) {
            $A = DB_fetchArray ($result);
            $field=COM_applyFilter($A['field']);
            if ($field_ary[$field]<>""){
                $fld=explode(":",$field);
                $defcsv[$i]['type']=$fld[0];
                $defcsv[$i]['field']=$fld[1];
                $defcsv[$i]['csvheader']=COM_applyFilter($A['csvheader']);
                $defcsv[$i]['value']=COM_applyFilter($A['value']);
            }
        }
    }

    return $defcsv;

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

    include_once($_CONF['path'] . 'plugins/databox/csv/sql/mysql_install_csv.php');

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

    COM_errorLog("Success - databox CSV Initialize",1);
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
        $importurl=$box_conf['path_csv_out'];
    }
    if  (file_exists($importurl) ){
        if ( is_dir($importurl) AND is_writable($importurl)){
            $fchk=true;
        }
    }else{
        $fchk=false;
    }


    $defcsv=fnc_GetDefCsv($pi_name);
    $cnt=count($defcsv);
    if ($cnt>0){
        if  ($fchk){
            $rt=fncOutBox($pi_name,$importurl,$defcsv);
            $dummy = DATABOX_OutLog( $rt ,$logfile,$logmode);
        }else{
            $rt=$importurl." ". $lang_box_admin['err_not_exist'];
            $dummy = DATABOX_OutLog( $rt ,$logfile,$logmode);
        }
    }else{
        $rt="csv def data not exist";
    }


    return $rt;

}


//追加項目の編集
function fnc_getfieldvalue2(
    $value
    ,$type
    ,$selectionary
       ,$langf_box_noyes
       ,$selectlist
       ,$csvtype
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
                     if  ($csvtype=="addition"){
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

function fnc_selList(
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能  一覧表示
// | 書式 fnc_selList($pi_name)
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

    $table=$_TABLES[strtoupper($pi_name).'_def_csv_sel'];

    require_once( $_CONF['path_system'] . 'lib-admin.php' );

    $retval = $lang_box_admin['csv_select'];

    //ヘッダ：編集～
    $header_arr[]=array('text' => $lang_box_admin['csv_sel_id'], 'field' => 'csv_sel_id', 'sort' => true);
    $header_arr[]=array('text' => $LANG_ADMIN['edit'], 'field' => 'seleditid', 'sort' => false);
    $header_arr[]=array('text' => $lang_box_admin['name'], 'field' => 'name', 'sort' => true);

    //
    $text_arr = array('has_menu' =>  true,
      'has_extras'   => true,
      'form_url' => $_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT."?mode=list");

    //Query
    $sql = "SELECT ";
    $sql .= " t.csv_sel_id".LB;
    $sql .= " ,t.name".LB;

    $sql .= " FROM ";
    $sql .= " {$table} AS t".LB;
    $sql .= " WHERE ".LB;
    $sql .= " 1=1".LB;
    //
    $query_arr = array(
        'table' =>$table,
        'sql' => $sql,
        'query_fields' => array('t.csv_sel_id','t.name'
        ),
        'default_filter' => $exclude);
    //デフォルトソート項目:
    $defsort_arr = array('field' => 't.csv_sel_id', 'direction' => 'ASC');

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

function fnc_selEdit(
    $pi_name
    ,$id
    ,$msg = ''
    ,$errmsg=""
)
// +---------------------------------------------------------------------------+
// | 機能  編集画面表示
// | 書式 fnc_selEdit($pi_name,$csv_sel_id)
// | 書式 fnc_selEdit($pi_name,$csv_sel_id,$msg,$errmsg)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:
// | 引数 $csv_sel_id:
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

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;
    
    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $table=$_TABLES[strtoupper($pi_name).'_def_csv_sel'];
    $table2=$_TABLES[strtoupper($pi_name).'_def_csv_sel_dtl'];

    $retval = '';

    $delflg=false;

    if (!empty ($msg)) {
        $retval .= COM_showMessage ($msg,$pi_name);
        $retval .= $errmsg;

        // clean 'em up
        $name = COM_applyFilter($_POST['name']);
        $description = COM_applyFilter ($_POST['description']);

        $uuid=$_USER['uid'];

    }else{
        if (empty($id)) {

            $id=0;

            $name = "";
            $description = "";

            $uuid=2;
            $udatetime="";//"";

        }else{
            $sql = "SELECT ";

            $sql .= " *";

            $sql .= " FROM ";
            $sql .= $table;
            $sql .= " WHERE ";
            $sql .= " csv_sel_id = $id";
            $result = DB_query($sql);

            $A = DB_fetchArray($result);

            $name = COM_stripslashes($A['name']);
            $description = COM_stripslashes($A['description']);

            $uuid = COM_stripslashes($A['uuid']);
            $udatetime=COM_stripslashes($A['udatetime']);

            $delflg=true;
        }
    }
    
    $tmplfld=DATABOX_templatePath('admin','default',$pi_name);

    $templates = new Template($tmplfld);
    $templates->set_file('editor',"csvsel_editor.thtml");

    //--
    $templates->set_var('about_thispage', $lang_box_admin['about_admin_csv_sel']);
    $templates->set_var('lang_must', $lang_box_admin['must']);
    $templates->set_var('site_url', $_CONF['site_url']);
    $templates->set_var('site_admin_url', $_CONF['site_admin_url']);
    $templates->set_var('csv_select', $lang_box_admin['csv_select']);

    //--
    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);
    $templates->set_var('gltoken_name', CSRF_TOKEN);
    $templates->set_var('gltoken', $token);
    $templates->set_var ( 'xhtml', XHTML );

    $templates->set_var('script', THIS_SCRIPT);


    //seq
    $templates->set_var('lang_id', $lang_box_admin['id']);
    $templates->set_var('id', $id);

    //tag value field
    $templates->set_var('lang_name', $lang_box_admin['name']);
    $templates->set_var ('name', $name);
    $templates->set_var ('help_name', $lang_box_admin['help_name']);
    $templates->set_var('lang_description', $lang_box_admin['description']);
    $templates->set_var ('description', $description);
    $templates->set_var ('help_description', $lang_box_admin['help_description']);
    $templates->set_var('maxlength_description', $box_conf['maxlength_description']);


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
	
	
	if  ($id<>""){
        $url11=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=seldtlnew&id='.$id;
        $menu_arr[]=array('url' => $url11,'text' => $lang_box_admin['add']);
        $icon="";
        $retval .= ADMIN_createMenu(
            $menu_arr
            ,$lang_box_admin['instructions']
            ,$icon
        );
    }
    
    //一覧　ヘッダ：編集～
    $header_arr[]=array('text' => $lang_box_admin['seq'], 'field' => 'seq', 'sort' => true);
    $header_arr[]=array('text' => $LANG_ADMIN['edit'], 'field' => 'seleditseq', 'sort' => false);
    $header_arr[]=array('text' => $lang_box_admin['csvheader'], 'field' => 'csvheader', 'sort' => true);
    $header_arr[]=array('text' => $lang_box_admin['value'], 'field' => 'value', 'sort' => true);

    //
    $text_arr = array('has_menu' =>  true,
      'has_extras'   => true,
      'form_url' => $_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT."?mode=list");

    //Query
    $sql = "SELECT ";
    $sql .= " t2.csv_sel_id".LB;
    $sql .= " ,t2.seq".LB;
    $sql .= " ,t2.csvheader".LB;
    $sql .= " ,t2.value".LB;

    $sql .= " FROM ";
    $sql .= " {$table2} AS t2".LB;
    $sql .= " WHERE ".LB;
    $sql .= " csv_sel_id=".$id.LB;
    //
    $query_arr = array(
        'table' =>$table2,
        'sql' => $sql,
        'query_fields' => array('t2.seq','t2.csvheader','t2.value'
        ),
        'default_filter' => $exclude);
    //デフォルトソート項目:
    $defsort_arr = array('field' => 't2.seq', 'direction' => 'ASC');

    //List 取得
    //ADMIN_list($component, $fieldfunction, $header_arr, $text_arr,
    //       $query_arr, $menu_arr, $defsort_arr, $filter = '', $extra = '', $options = '')
    $w= ADMIN_list(
        $pi_name
        , "fncGetListField"
        , $header_arr
        , $text_arr
        , $query_arr
        , $defsort_arr
        , $filter
        );

	
	$retval.=$w;
	
	
    return $retval;
}
function fnc_selSave (
    $pi_name
    ,$navbarMenu
    ,$menuno

)
// +---------------------------------------------------------------------------+
// | 機能  保存
// | 書式 fnc_selSave ($pi_name,,$navbarMenu,$menuno)
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

    $table=$_TABLES[strtoupper($pi_name).'_def_csv_sel'];

    $retval = '';

    // clean 'em up
    $id = COM_applyFilter($_POST['id'],true);

    $name=COM_applyFilter($_POST['name']);
    $name = addslashes (COM_checkHTML (COM_checkWords ($name)));
    $description=$_POST['description'];//COM_applyFilter($_POST['description']);
    $description=addslashes (COM_checkHTML (COM_checkWords ($description)));

    //-----
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

    //errorのあるとき
    if ($err<>"") {
        //$page_title=$lang_box_admin['piname'].$lang_box_admin['edit'];

        //$retval .= COM_siteHeader ('menu', $page_title);

        //$retval .=ppNavbarjp($navbarMenu,$lang_box_admin_menu[$menuno]);
        $retval .= fnc_Edit($pi_name,$id,3,$err);
        //$display .= COM_siteFooter ();

        return $retval;

    }
    // CHECK　おわり

    if ($id==0){
        $w=DB_getItem($table,"max(csv_sel_id)","1=1");
        if ($w=="") {
            $w=0;
        }
        $id=$w+1;
    }

    $fields="csv_sel_id";
    $values="$id";

    $fields.=",name";
    $values.=",'$name'";

    $fields.=",description";
    $values.=",'$description'";

    $fields.=",uuid";
    $values.=",$uuid";

    $fields.=",udatetime";
    $values.=",NOW()";
    //


    DB_save($table,$fields,$values,$return_page);

    $retval['title']=$lang_box_admin['piname'].$lang_box_admin['edit'];
    $retval['display'] .= fnc_Menu($pi_name);
    $retval['display'] .= fnc_selEdit($pi_name,$id,$msg);
    return $retval;


}
function fnc_seldelete (
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能  削除
// | 書式 fnc_seldelete ($pi_name)
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

    $table2=$_TABLES[strtoupper($pi_name).'_def_csv_sel_dtl'];
    $table=$_TABLES[strtoupper($pi_name).'_def_csv_sel'];

    $id = COM_applyFilter($_POST['id'],true);

    DB_delete ($table2, 'csv_sel_id', $id);
    DB_delete ($table, 'csv_sel_id', $id);

    $retval['title']=$lang_box_admin['piname'].$lang_box_admin['edit'];
    $retval['display'] .= fnc_Menu($pi_name);
    $retval['display'] .= fnc_selList($pi_name);;
    return $retval;
	
}
function fnc_seldtlEdit(
    $pi_name
    ,$id
    ,$seq
    ,$msg = ''
    ,$errmsg=""
)
// +---------------------------------------------------------------------------+
// | 機能  編集画面表示
// | 書式 fnc_selEdit($pi_name,$csv_sel_id,$seq)
// | 書式 fnc_selEdit($pi_name,$csv_sel_id,$seq,$msg,$errmsg)
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

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;
	
	$lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $table=$_TABLES[strtoupper($pi_name).'_def_csv_sel'];
    $table2=$_TABLES[strtoupper($pi_name).'_def_csv_sel_dtl'];

    $retval = '';

    $delflg=false;
	
    if (!empty ($msg)) {
        $retval .= COM_showMessage ($msg,$pi_name);
        $retval .= $errmsg;

        // clean 'em up
        $csvheader = COM_applyFilter($_POST['csvheader']);
        $value = COM_applyFilter ($_POST['value']);

        $uuid=$_USER['uid'];

    }else{
        if (empty($seq)) {

            $seq=0;

            $csvheader = "";
            $value = "";

            $uuid=2;
            $udatetime="";//"";

        }else{
            $sql = "SELECT ";

            $sql .= " *";

            $sql .= " FROM ";
            $sql .= $table2;
            $sql .= " WHERE ";
            $sql .= " seq = $seq";
            $result = DB_query($sql);

            $A = DB_fetchArray($result);

            $csvheader = COM_stripslashes($A['csvheader']);
            $value = COM_stripslashes($A['value']);

            $uuid = COM_stripslashes($A['uuid']);
            $udatetime=COM_stripslashes($A['udatetime']);

            $delflg=true;
        }
    }


    $tmplfld=DATABOX_templatePath('admin','default',$pi_name);

    $templates = new Template($tmplfld);
    $templates->set_file('editor',"csvseldtl_editor.thtml");

    //--
    $templates->set_var('about_thispage', $lang_box_admin['about_admin_csv_sel_dtl']);
    $templates->set_var('lang_must', $lang_box_admin['must']);
    $templates->set_var('site_url', $_CONF['site_url']);
    $templates->set_var('site_admin_url', $_CONF['site_admin_url']);
    $templates->set_var('csv_select_dtl', $lang_box_admin['csv_select_dtl']);

    //--
    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);
    $templates->set_var('gltoken_name', CSRF_TOKEN);
    $templates->set_var('gltoken', $token);
    $templates->set_var ( 'xhtml', XHTML );

    $templates->set_var('script', THIS_SCRIPT);


    //id seq
    $templates->set_var('lang_seq', $lang_box_admin['seq']);
    $templates->set_var('seq', $seq);
    $templates->set_var('id', $id);
    //tag value field
    $templates->set_var('lang_csvheader', $lang_box_admin['csvheader']);
    $templates->set_var ('csvheader', $csvheader);
    $templates->set_var ('help_csvheader', $lang_box_admin['help_csvheader']);
    $templates->set_var('lang_value', $lang_box_admin['value']);
    $templates->set_var ('value', $value);
    $templates->set_var ('help_value', $lang_box_admin['help_value_csv_sel']);

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
function fnc_seldtlSave (
    $pi_name
    ,$navbarMenu
    ,$menuno

)
// +---------------------------------------------------------------------------+
// | 機能  保存
// | 書式 fnc_selSave ($pi_name,,$navbarMenu,$menuno)
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

    $table=$_TABLES[strtoupper($pi_name).'_def_csv_sel_dtl'];

    $retval = '';

    // clean 'em up
    $id = COM_applyFilter($_POST['id'],true);
    $seq = COM_applyFilter($_POST['seq'],true);

    $csvheader=COM_applyFilter($_POST['csvheader']);
    $csvheader= addslashes (COM_checkHTML (COM_checkWords ($csvheader)));
    //$value=$_POST['description'];//COM_applyFilter($_POST['description']);
    $value=COM_stripslashes($_POST['value']);
    $value=addslashes (COM_checkHTML (COM_checkWords ($value)));

    //-----
    $uuid=$_USER['uid'];

    // CHECK　はじめ
    $err="";
    //ID コード
    if ($id==0 ){
        $err.=$lang_box_admin['err_	id']."<br/>".LB;
    }else{
        if (!is_numeric($id) ){
            $err.=$lang_box_admin['err_id']."<br/>".LB;
        }
    }

    //errorのあるとき
    if ($err<>"") {
        //$page_title=$lang_box_admin['piname'].$lang_box_admin['edit'];

        //$retval .= COM_siteHeader ('menu', $page_title);

        //$retval .=ppNavbarjp($navbarMenu,$lang_box_admin_menu[$menuno]);
        $retval .= fnc_seldtlEdit($pi_name,$id,3,$err);
        //$display .= COM_siteFooter ();

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

    $fields.=",csv_sel_id";
    $values.=",$id";

    $fields.=",csvheader";
    $values.=",'$csvheader'";

    $fields.=",value";
    $values.=",'$value'";

    //


    DB_save($table,$fields,$values,$return_page);

    $retval['title']=$lang_box_admin['piname'].$lang_box_admin['edit'];
    $retval['display'] .= fnc_Menu($pi_name);
    $retval['display'] .= fnc_selEdit($pi_name,$id,$msg);
    return $retval;

}
function fnc_seldtldelete (
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能  削除
// | 書式 fnc_seldtldelete ($pi_name)
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

    $table=$_TABLES[strtoupper($pi_name).'_def_csv_sel_dtl'];

    $seq = COM_applyFilter($_POST['seq'],true);
    $id = COM_applyFilter($_POST['id'],true);

    DB_delete ($table, 'seq', $seq);

    $retval['title']=$lang_box_admin['piname'].$lang_box_admin['edit'];
    $retval['display'] .= fnc_Menu($pi_name);
    $retval['display'] .= fnc_selEdit($pi_name,$id,$msg);
    return $retval;
	
}
function fnc_Scheduled(
    $pi_name
)
{
    global $_CONF;
    global $LANG_ADMIN;

    global $_DATABOX_CONF;
    global $LANG_confignames;
	global $LANG_CONFIG;
    global $_TABLES;
	
    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;
	
    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    $name=$pi_name.'_csv_cron';
    
    //擬似クーロン実行日
    $datetime = DB_getItem( $_TABLES['vars'], 'value', "name = '{$name}'" );
    $tmpl = new Template ($_CONF['path'] . "plugins/".THIS_PLUGIN."/templates/admin/");
    $tmpl->set_file(array('confirmation' => 'confirmation.thtml'));

    $tmpl->set_var('site_admin_url', $_CONF['site_admin_url']);
    $tmpl->set_var('gltoken_name', CSRF_TOKEN);
    $tmpl->set_var('gltoken', SEC_createToken());
    $tmpl->set_var ( 'xhtml', XHTML );

    //
    $actionname=$lang_box_admin["schedule"];
    $actionprg="{$_CONF['site_admin_url']}/plugins/".THIS_SCRIPT."?mode=scheduleupdate";

    $tmpl->set_var('actionname', $actionname);
    $tmpl->set_var('actionprg', $actionprg);

    if ($box_conf['csv_cron_schedule_interval'] == 0) {
        $msg=$lang_box_admin['cron_schedule_Enable'];
        $lang_datetime="";
        $datetime="";
    }else{
        if ($datetime==""){
            $datetime=time();
            $nextdatetime=time();
        }else{
            $nextdatetime=$datetime+$box_conf['csv_cron_schedule_interval'];
        }
       $msg=$lang_box_admin['cron_schedule'];
       $lang_datetime=$lang_box_admin["nextdatetime"];
       $datetime=DATABOX_datetimeedit($nextdatetime,"LANG_".strtoupper($pi_name)."_ADMIN","datetime");
    }
    $tmpl->set_var('help', $msg);
    $tmpl->set_var('lang_checklist',$lang_datetime);
    $tmpl->set_var('checklist', $datetime);
    
    // SAVE、CANCEL ボタン
    $tmpl->set_var('lang_confirm', $lang_box_admin['confirm']);
    $tmpl->set_var('lang_submit', $LANG_CONFIG['save_changes']);
    $tmpl->set_var('btn2', "");
    $tmpl->set_var('btn3', "");
    $tmpl->set_var('lang_cancel', $LANG_ADMIN['cancel']);
    $tmpl->set_var('mode', "csvscheduledupdate");
    
    $tmpl->parse ('output', 'confirmation');
    $retval = $tmpl->finish ($tmpl->get_var ('output'));

    return $retval;
}

function fnc_ScheduledUpdate (
    $pi_name)
{
    global $_TABLES;
    
   $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;
    $name=$pi_name.'_csv_cron';

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
    $w=$w-$box_conf['csv_cron_schedule_interval'];
    DB_query( "UPDATE {$_TABLES['vars']} SET value = '$w' WHERE name = '{$name}'" );

    return;
}
?>
