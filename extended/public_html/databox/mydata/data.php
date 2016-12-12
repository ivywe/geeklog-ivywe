<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | data maintenannce
// +---------------------------------------------------------------------------+
// $Id: data.php
// public_html/databox/mydata/data.php
// 20101208 tsuchitani AT ivywe DOT co DOT jp
// 20120416 fncsave hits

//@@@@@@追加予定　メールにカテゴリ

define ('THIS_SCRIPT', 'databox/mydata/data.php');
//define ('THIS_SCRIPT', 'databox/mydata/test.php');

require_once('databox_functions.php');
require_once( $_CONF['path_system'] . 'lib-admin.php' );

if ($_DATABOX_CONF['allow_data_update']==1 ){
}else{
    if (SEC_hasRights ('databox.edit') ){
	}else{
		COM_accessLog("User {$_USER['username']} tried to data and failed ");
		echo COM_refresh($_CONF['site_url'] . '/index.php');
		exit;
	}
}

// +---------------------------------------------------------------------------+
// | 機能  一覧表示                                                            |
// | 書式 fncList()                                                            |
// +---------------------------------------------------------------------------+\
// | 戻値 nomal:一覧                                                           |
// +---------------------------------------------------------------------------+
function fncList(
	$template
)
{
    global $_CONF;
    global $_TABLES;
    global $LANG_ADMIN;
    global $LANG09;
    global $LANG_DATABOX_ADMIN;
    global $LANG_DATABOX;
    global $_DATABOX_CONF;

	$retval = '';
	
	//フィルタ Filter
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
        $exclude=" AND t.fieldset_id={$filter_val}";
    }

    $filter = "{$LANG_DATABOX_ADMIN['fieldset']}:";
    $filter .="<select name='filter_val' style='width: 125px' onchange='this.form.submit()'>";
    $filter .="<option value='{$LANG09[9]}'";

    if  ($filter_val==$LANG09[9]){
        $filter .=" selected='selected'";
    }
    $filter .=" >{$LANG09[9]}</option>";
    $filter .= COM_optionList ($_TABLES['DATABOX_def_fieldset']
                , 'fieldset_id,name', $filter_val,0,"fieldset_id<>0");

    $filter .="</select>";
	

    //ヘッダ：編集～
    $header_arr[]=array('text' => $LANG_DATABOX_ADMIN['orderno'], 'field' => 'orderno', 'sort' => true);
    $header_arr[]=array('text' => $LANG_ADMIN['edit'], 'field' => 'editid', 'sort' => false);

    if ($_DATABOX_CONF['allow_data_insert']
            OR SEC_hasRights('databox.submit')){
        $header_arr[]=array('text' => $LANG_ADMIN['copy'], 'field' => 'copy', 'sort' => false);
    }
    $header_arr[]=array('text' => $LANG_DATABOX_ADMIN['id'], 'field' => 'id', 'sort' => true);
    $header_arr[]=array('text' => $LANG_DATABOX_ADMIN['code'], 'field' => 'code', 'sort' => true);
	$header_arr[]=array('text' => $LANG_DATABOX_ADMIN['title'], 'field' => 'title', 'sort' => true);
    $header_arr[]=array('text' => $LANG_DATABOX_ADMIN['fieldset'], 'field' => 'fieldset_name', 'sort' => true);
    $header_arr[]=array('text' => $LANG_DATABOX_ADMIN['remaingdays'], 'field' => 'remaingdays', 'sort' => true);

    $header_arr[]=array('text' => $LANG_DATABOX_ADMIN['udatetime'], 'field' => 'udatetime', 'sort' => true);
    $header_arr[]=array('text' => $LANG_DATABOX_ADMIN['draft'], 'field' => 'draft_flag', 'sort' => true);

    //
    $text_arr = array('has_menu' =>  true,
      'has_extras'   => true,
      'form_url' => $_CONF['site_url'] ."/".THIS_SCRIPT);

    //Query
    $sql = "SELECT ";
    $sql .= " id";
    $sql .= " ,title";
    $sql .= " ,code";
    $sql .= " ,draft_flag";
    $sql .= " ,modified";
    $sql .= " ,UNIX_TIMESTAMP(t.udatetime) AS udatetime";
    $sql .= " ,orderno";
    $sql .= " ,t2.name AS fieldset_name";
    $sql .= " ,t.fieldset_id";
	
	$sql .= " ,(SELECT DATEDIFF(expired , NOW()) ";
	$sql .= " FROM {$_TABLES['DATABOX_base']} AS t3  ";
	$sql .= " where   t.id=t3.id AND DATEDIFF(expired , NOW())>0)";
    $sql .= "	+ 1 AS remaingdays";

    $sql .= " ,owner_id";
    $sql .= " ,group_id";
    $sql .= " ,perm_owner";
    $sql .= " ,perm_group";
    $sql .= " ,perm_members";
    $sql .= " ,perm_anon";

    $sql .= " FROM ";
    $sql .= " {$_TABLES['DATABOX_base']} AS t";
    $sql .= " ,{$_TABLES['DATABOX_def_fieldset']} AS t2";
    $sql .= " WHERE ";

    $sql .= " t.fieldset_id=t2.fieldset_id";
    //編集権のないデータ はのぞく
    $sql .= COM_getPermSql('AND',0,3);

    $query_arr = array(
        'table' => 'DATABOX_base',
        'sql' => $sql,
        'query_fields' => array('id','title','code','draft_flag','orderno','t2.name'),
        'default_filter' => $exclude);
    //デフォルトソート項目:
    if  ($_DATABOX_CONF["sort_list_by_my"]=="udatetime"){
        $defsort_arr = array('field' => 'udatetime', 'direction' => 'DESC');
    }else{
        $defsort_arr = array('field' => $_DATABOX_CONF["sort_list_by_my"], 'direction' => 'ASC');
    }
	$form_arr = array('bottom' => '', 'top' => '');
    $pagenavurl = '&amp;filter_val=' . $filter_val;
   //List 取得
	if (COM_versionCompare(VERSION, "2.0.0",  '>=')){
		$retval .= ADMIN_list(
			'databox'
			, "fncGetListField"
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
			'databox'
			, "fncGetListField"
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

// +---------------------------------------------------------------------------+
// | 一覧取得                                                                  |
// | 書式 plugin_getListField_databox                                          |
// +---------------------------------------------------------------------------+
function fncGetListField($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF;
    global $LANG_ACCESS;
    global $_DATABOX_CONF;
    global $LANG_DATABOX_ADMIN;

    $retval = '';
	$template = '';
	if (isset ($_REQUEST['template'])) {
		$template = COM_applyFilter ($_REQUEST['template']);
	}

        switch($fieldname) {
            //編集アイコン
            case 'editid':
                $url=$_CONF['site_url'] . "/".THIS_SCRIPT;
                $url.="?";
                $url.="mode=edit";
				$url.="&amp;id=".$A['id'];
				if  ($template<>""){
					$url.="&amp;template=".$template;
				}
                $retval = COM_createLink($icon_arr['edit'],$url);
                break;
            case 'copy':
                $url=$_CONF['site_url'] . "/".THIS_SCRIPT;
                $url.="?";
                $url.="mode=copy";
                $url.="&amp;id=".$A['id'];
                $retval = COM_createLink($icon_arr['copy'],$url);
                break;
			case 'id':
				$name=COM_stripslashes($A['id']);
				$url=$_CONF['site_url'] . "/databox/data.php";
				$url.="?";
				$url.="id=".$A['id'];
				$url.="&amp;m=id";
				$url = COM_buildUrl( $url );
				$retval= COM_createLink($name, $url);
				break;
			case 'code':
				$name=COM_stripslashes($A['code']);
                $rt= databox_detail_link(0,$A['code'],$name);
                $retval= $rt['link'];
				break;
            //下書
            case 'draft_flag':
                if ($A['draft_flag'] == 1) {
                    $switch = 'checked="checked"';
                } else {
                    $switch = '';
                }
                $retval = "<form action=\"{$_CONF['site_admin_url']}";
                $retval .= "/plugins/".THIS_SCRIPT."\" method=\"post\">";
                $retval .= "<input type=\"checkbox\" name=\"drafton\" ";
                $retval .= "onclick=\"submit()\" value=\"{$A['draft_flag']}\" $switch disabled>";
                $retval .= "<input type=\"hidden\" name=\"draftChange\" ";
                $retval .= "value=\"{$A['id']}\">";
                $retval .= "</form>";
				break;
			case 'udatetime':
				$curtime = COM_getUserDateTimeFormat($A['udatetime']);
				$retval = $curtime[0];
				break;
			case 'remaingdays':
				if  ($fieldvalue<>""){
					$retval = "<span class=\"databox_admin_{$fieldvalue}\">";
					$retval .= "{$fieldvalue}</span>";
				}
				break;
            //各項目
            default:
                $retval = $fieldvalue;
                break;
    }

    return $retval;

}
// +---------------------------------------------------------------------------+
// | 機能  編集画面表示
// | 書式 fncEdit($id , $edt_flg,$msg,$errmsg,$mode,$fieldset_id,$template)
// +---------------------------------------------------------------------------+
// | 引数 $id:
// | 引数 $edt_flg:
// | 引数 $msg:メッセージ番号
// +---------------------------------------------------------------------------+
// | 戻値 nomal:編集画面                                                       |
// +---------------------------------------------------------------------------+
// update 20101207
function fncEdit(
    $id
    ,$edt_flg,$msg = ''
    ,$errmsg=""
	,$mode="edit"
	,$fieldset_id=0
    ,$template=""
    ,$old_mode=""
)
{

    $pi_name="databox";

    global $_CONF;
    global $_TABLES;
    global $LANG_DATABOX_ADMIN;
    global $LANG_ADMIN;
    global $MESSAGE;
    global $LANG_ACCESS;
    global $_DATABOX_CONF;
    global $_USER;
	global $_SCRIPTS;
	
    $retval = '';

    $delflg=false;

    $addition_def=DATABOX_getadditiondef();

    //メッセージ表示
    if (!empty ($msg)) {
        $retval .= COM_showMessage ($msg,$pi_name);
        $retval .= $errmsg;
        // clean 'em up
        $code=COM_applyFilter($_POST['code']);
        $title = COM_stripslashes($_POST['title']);
        $page_title = COM_applyFilter($_POST['page_title']);
        $description=$_POST['description'];//COM_applyFilter($_POST['description']);

        $draft_flag = COM_applyFilter ($_POST['draft_flag'],true);
        $cache_time = COM_applyFilter ($_POST['cache_time'],true);

        $language_id = COM_applyFilter ($_POST['language_id']);

        $category = $_POST['category'];

        $additionfields=$_POST['afield'];
        $additionfields_fnm=$_POST['afield_fnm'];//@@@@@
		$additionfields_del=$_POST['afield_del'];
		$additionfields_date=array();
        $additionfields_alt=$_POST['afield_alt'];;
		$additionfields=DATABOX_cleanaddtiondatas (
			$additionfields
			,$addition_def
			,$additionfields_fnm
			,$additionfields_del
			,$additionfields_date
			,$additionfields_alt
			,false
			);

        //作成日付
        $created = COM_applyFilter ($_POST['created']);
        $created_un = COM_applyFilter ($_POST['created_un']);

        $orderno = COM_applyFilter ($_POST['orderno']);

        $uuid=$_USER['uid'];
        $udatetime=COM_applyFilter ($_POST['udatetime']);//"";

        $fieldset_id=COM_applyFilter ($_POST['fieldset'],true);//"";
        $fieldset_name=COM_applyFilter ($_POST['fieldset_name']);//"";

    }else{
		if (empty($id)) {
			$fieldset_name=DB_getItem($_TABLES['DATABOX_def_fieldset'],"name","fieldset_id=".$fieldset_id);
			$fieldset_name=COM_stripslashes($fieldset_name);
			
            $id=0;

            $code ="";
            $title ="";
            $description="";

			$language_id="";

            $category = "";
            $additionfields=array();
            $additionfields_fnm=array();//@@@@@
            $additionfields_del=array();
			$additionfields_date="";
            $additionfields = DATABOX_getadditiondatas(0,$pi_name);

            //
            $draft_flag=$_DATABOX_CONF['user_draft_default'];
            $cache_time=$_DATABOX_CONF['default_cache_time'];
			
			//作成日付
			$created=0;
			$created_un=0;

            $uuid=0;
            $udatetime="";//"";
			
			$defaulttemplatesdirectory="";
        }else{
            $sql = "SELECT ";

			$sql .= " t.*".LB;
			$sql .= " ,t2.name AS fieldset_name".LB;
			
			
			$sql .= " ,UNIX_TIMESTAMP(t.modified) AS modified_un".LB;
			$sql .= " ,UNIX_TIMESTAMP(t.released) AS released_un".LB;
			$sql .= " ,UNIX_TIMESTAMP(t.comment_expire) AS comment_expire_un".LB;
			$sql .= " ,UNIX_TIMESTAMP(t.expired) AS expired_un".LB;
			$sql .= " ,UNIX_TIMESTAMP(t.udatetime) AS udatetime_un".LB;
			$sql .= " ,UNIX_TIMESTAMP(t.created) AS created_un".LB;
			
			$sql .= " FROM ";
			$sql .= $_TABLES['DATABOX_base'] ." AS t ".LB;
			$sql .= ",".$_TABLES['DATABOX_def_fieldset'] ." AS t2 ".LB;
            $sql .= " WHERE ".LB;
			$sql .= " id = $id".LB;
			$sql .= " AND t.fieldset_id = t2.fieldset_id".LB;

            //編集権のないデータ はのぞく//@@@@@
            $sql .= COM_getPermSql('AND',0,3);

            $result = DB_query($sql);

            $A = DB_fetchArray($result);
            $A = array_map('stripslashes', $A);
            $fieldset_id = COM_stripslashes($A['fieldset_id']);
            $fieldset_name = COM_stripslashes($A['fieldset_name']);

            $code = COM_stripslashes($A['code']);
            $title=COM_stripslashes($A['title']);
            $page_title=COM_stripslashes($A['page_title']);
            $description=COM_stripslashes($A['description']);

            $language_id = COM_stripslashes($A['language_id']);

            $category = DATABOX_getdatas("category_id",$_TABLES['DATABOX_category'],"id = $id");

            //追加項目
            $additionfields = DATABOX_getadditiondatas($id,$pi_name);
            $additionfields_fnm=array();//@@@@@
            $additionfields_del=array();
			$additionfields_date="";

            $draft_flag=COM_stripslashes($A['draft_flag']);
            $cache_time=COM_stripslashes($A['cache_time']);

			//編集日
			$wary = COM_getUserDateTimeFormat(COM_stripslashes($A['modified_un']));
			$modified = $wary[1];
            $modified_month = date('m', $modified);
            $modified_day = date('d', $modified);
            $modified_year = date('Y', $modified);
            $modified_hour = date('H', $modified);
            $modified_minute = date('i', $modified);

            //作成日付
			$wary = COM_getUserDateTimeFormat(COM_stripslashes($A['created_un']));
			$created = $wary[0];
			$created_un = $wary[1];

            $orderno=COM_stripslashes($A['orderno']);

            $uuid = COM_stripslashes($A['uuid']);

			$wary = COM_getUserDateTimeFormat(COM_stripslashes($A['udatetime_un']));
			$udatetime = $wary[0];
			
			$defaulttemplatesdirectory=$A['defaulttemplatesdirectory'];
            if ($_DATABOX_CONF['allow_data_delete']){
                if ($edt_flg==FALSE) {
                    $delflg=true;
                }
            }
        }
    }
    if ($mode==="copy"){
        $id=0;
        $draft_flag=$_DATABOX_CONF['user_draft_default'];
		$code="";
		
        //作成日付
        $created=0;
        $created_un=0;
        //公開日
        $released_month=$modified_month;
        $released_day = $modified_day;
        $released_year = $modified_year;
        $released_hour = $modified_hour;
        $released_minute = $modified_minute;
        //公開終了日
        $expired_flag=0;
        $w = mktime(0, 0, 0, date('m'),
             date('d') + $_CONF['article_comment_close_days'], date('Y'));
        $expired_year=date('Y', $w);
        $expired_month=date('m', $w);
        $expired_day=date('d', $w);
        $expired_hour=0;
        $expired_minute=0;

        //
        $delflg=false;
        $old_mode="copy";
    }

    $chk_user=DATABOX_chkuser($group_id,$owner_id,"databox.admin");

	//template フォルダ
    if (is_null($template) or ($template==="")){
		$set_defaulttemplatesdirectory=DB_getItem($_TABLES['DATABOX_def_fieldset']
			,"defaulttemplatesdirectory","fieldset_id=".$fieldset_id);
		if ($defaulttemplatesdirectory<>""){
            $template=$defaulttemplatesdirectory;
        }elseif  ($set_defaulttemplatesdirectory<>""){
            $template=$set_defaulttemplatesdirectory;
        }else{
            $template="default";
        }
    }
	
    $tmplfld=DATABOX_templatePath('mydata',$template,'databox');
    $templates = new Template($tmplfld);
	
    $templates->set_file('editor',"data_editor.thtml");

    $templates->set_file (array (
                'editor' => 'data_editor.thtml',
                'row'   => 'row.thtml',
                'col'   => "data_col_detail.thtml",
            ));
    // Loads jQuery UI datepicker geeklog >=2.1.0
    $_SCRIPTS->setJavaScriptLibrary('jquery.ui.datepicker');
    $_SCRIPTS->setJavaScriptLibrary('jquery-ui-i18n');
    $_SCRIPTS->setJavaScriptLibrary('jquery-ui-timepicker-addon');
    $_SCRIPTS->setJavaScriptLibrary('jquery-ui-timepicker-addon-i18n');
    $_SCRIPTS->setJavaScriptFile('datetimepicker', '/javascript/datetimepicker.js');
    $_SCRIPTS->setJavaScriptFile('datepicker', '/javascript/datepicker.js');

    $langCode = COM_getLangIso639Code();
    $toolTip  = $MESSAGE[118];
    $imgUrl   = $_CONF['site_url'] . '/images/calendar.png';

	//--
    if (($_CONF['meta_tags'] > 0) && ($_DATABOX_CONF['meta_tags'] > 0)) {
        $templates->set_var('hide_meta', '');
    } else {
        $templates->set_var('hide_meta', ' style="display:none;"');
    }
    $templates->set_var('maxlength_description', $_DATABOX_CONF['maxlength_description']);

    $templates->set_var('about_thispage', $LANG_DATABOX_ADMIN['about_admin_data']);
    $templates->set_var('lang_must', $LANG_DATABOX_ADMIN['must']);
    $templates->set_var('site_url', $_CONF['site_url']);
    $templates->set_var('site_admin_url', $_CONF['site_admin_url']);
	$templates->set_var('lang_view', $LANG_DATABOX_ADMIN['view']);

    $templates->set_var('dateformat', $_DATABOX_CONF['dateformat']);

    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);
    $templates->set_var('gltoken_name', CSRF_TOKEN);
    $templates->set_var('gltoken', $token);
    $templates->set_var ( 'xhtml', XHTML );
	
	$script=THIS_SCRIPT;
	$script.="?template=".$template;
    $templates->set_var('script', $script);

    //
    $templates->set_var('lang_link_admin', $LANG_DATABOX_ADMIN['link_admin']);
    $templates->set_var('lang_link_admin_top', $LANG_DATABOX_ADMIN['link_admin_top']);
    $templates->set_var('lang_link_public', $LANG_DATABOX_ADMIN['link_public']);
    $templates->set_var('lang_link_list', $LANG_DATABOX_ADMIN['link_list']);
    $templates->set_var('lang_link_detail', $LANG_DATABOX_ADMIN['link_detail']);
	
	//field_id
    $templates->set_var('lang_fieldset', $LANG_DATABOX_ADMIN['fieldset']);
    $templates->set_var('fieldset_id', $fieldset_id);
    $templates->set_var('fieldset_name', $fieldset_name);

    //id
    $templates->set_var('lang_id', $LANG_DATABOX_ADMIN['id']);
    //@@@@@ $templates->set_var('help_id', $LANG_DATABOX_ADMIN['help']);
    $templates->set_var('id', $id);

    //下書
    $templates->set_var('lang_draft', $LANG_DATABOX_ADMIN['draft']);
    if  ($draft_flag==1) {
		$templates->set_var('draft_flag', "checked=checked");
		$templates->set_var('draft_msg', $LANG_DATABOX_ADMIN['draft_msg']);
    }else{
        $templates->set_var('draft_flag', "");
        $templates->set_var('draft_msg', "");
    }

    //
    $templates->set_var('lang_field', $LANG_DATABOX_ADMIN['field']);
    $templates->set_var('lang_fields', $LANG_DATABOX_ADMIN['fields']);
    $templates->set_var('lang_content', $LANG_DATABOX_ADMIN['content']);
    $templates->set_var('lang_templatesetvar', $LANG_DATABOX_ADMIN['templatesetvar']);

    //基本項目
    $templates->set_var('lang_basicfields', $LANG_DATABOX_ADMIN['basicfields']);
    //コード＆タイトル＆説明＆テンプレートセット値
    $templates->set_var('lang_code', $LANG_DATABOX_ADMIN['code']);
    if ($_DATABOX_CONF['datacode']){
        $templates->set_var('lang_must_code', $LANG_DATABOX_ADMIN['must']);
    }else{
        $templates->set_var('lang_must_code', "");
    }
    $templates->set_var ('code', $code);
    $templates->set_var('lang_title', $LANG_DATABOX_ADMIN['title']);
    $templates->set_var ('title', $title);
    $templates->set_var('lang_page_title', $LANG_DATABOX_ADMIN['page_title']);
    $templates->set_var ('page_title', $page_title);
    $templates->set_var('lang_description', $LANG_DATABOX_ADMIN['description']);
    $templates->set_var ('description', $description);

	
	//language_id
    if (is_array($_CONF['languages'])) {
        $templates->set_var('hide_language_id', '');
		$select_language_id=DATABOX_getoptionlist("language_id",$language_id,0,$pi_name,"",0 );
    } else {
        $templates->set_var('hide_language_id', ' style="display:none;"');
		$select_language_id="";
    }
    $templates->set_var('lang_language_id', $LANG_DATABOX_ADMIN['language_id']);
	$templates->set_var ('language_id', $language_id);
    $templates->set_var ('select_language_id', $select_language_id);//@@@@@
	
    //編集日
    $templates->set_var ('lang_modified_autoupdate', $LANG_DATABOX_ADMIN['modified_autoupdate']);
    $templates->set_var ('lang_modified', $LANG_DATABOX_ADMIN['modified']);
    $w=COM_convertDate2Timestamp(
        $modified_year."-".$modified_month."-".$modified_day
        , $modified_hour.":".$modified_minute."::00"
        );
    $datetime_modified=DATABOX_datetimeedit($w,"LANG_DATABOX_ADMIN","modified");
    $templates->set_var ('datetime_modified', $datetime_modified);
	
	//カテゴリ
    $templates->set_var('lang_category', $LANG_DATABOX_ADMIN['category']);
    $checklist_category=DATABOX_getcategoriesinp ($category,$fieldset_id,"databox",$chk_user);
    $templates->set_var('checklist_category', $checklist_category);

    //追加項目
    $templates->set_var('lang_additionfields', $LANG_DATABOX_ADMIN['additionfields']);
	$rt=DATABOX_getaddtionfieldsEdit(
		$additionfields
		,$addition_def
		,$templates
		,$chk_user
		,$pi_name
		,$additionfields_fnm
		,$additionfields_del
		,$fieldset_id
		,$additionfields_date
		);
    //$rt=DATABOX_getaddtionfieldsJS($additionfields,$addition_def,$chk_user,$pi_name);

    //保存日時
    $templates->set_var ('lang_udatetime', $LANG_DATABOX_ADMIN['udatetime']);
    $templates->set_var ('udatetime', $udatetime);
    $templates->set_var ('lang_uuid', $LANG_DATABOX_ADMIN['uuid']);
    $templates->set_var ('uuid', $uuid);
    //作成日付
    $templates->set_var ('lang_created', $LANG_DATABOX_ADMIN['created']);
    $templates->set_var ('created', $created);
    $templates->set_var ('created_un', $created_un);

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

    $templates->set_var('old_mode', $old_mode);

    //
    $templates->parse('output', 'editor');
    $retval .= $templates->finish($templates->get_var('output'));

    return $retval;
}


// +---------------------------------------------------------------------------+
// | 機能  保存                                                                |
// | 書式 fncSave ($edt_flg)                                                   |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:戻り画面＆メッセージ                                           |
// +---------------------------------------------------------------------------+
//20101207
function fncSave (
    $edt_flg
    ,$navbarMenu
	,$menuno
	,$template
)
{
    $pi_name="databox";

    global $_CONF;
    global $LANG_DATABOX_ADMIN;
    global $_TABLES;
    global $_USER;
    global $_DATABOX_CONF;
    global $LANG_DATABOX_user_menu;

    $addition_def=DATABOX_getadditiondef();

    $retval = '';

    // clean 'em up
    $id = COM_applyFilter($_POST['id'],true);
    if ($id==0){
        $new_flg=true;
    }else{
        $new_flg=false;
    }
	$fieldset_id = COM_applyFilter ($_POST['fieldset'],true);
    $code = COM_applyFilter($_POST['code']);
    $code = addslashes (COM_checkHTML (COM_checkWords ($code)));

    $title = COM_stripslashes($_POST['title']);
    $title = addslashes (COM_checkHTML (COM_checkWords ($title)));

    $page_title = COM_applyFilter($_POST['page_title']);
    $page_title = addslashes (COM_checkHTML (COM_checkWords ($page_title)));

    $description=$_POST['description'];//COM_applyFilter($_POST['description']);
    $description=addslashes (COM_checkHTML (COM_checkWords ($description)));
	
	$language_id=COM_applyFilter($_POST['language_id']);
    $language_id=addslashes (COM_checkHTML (COM_checkWords ($language_id)));
	
    $category = $_POST['category'];

    //@@@@@
    $additionfields=$_POST['afield'];
    $additionfields_old=$_POST['afield'];
    $additionfields_fnm=$_POST['afield_fnm'];
    $additionfields_del=$_POST['afield_del'];
    $additionfields_alt=$_POST['afield_alt'];
	$additionfields_date=array();

	$additionfields=DATABOX_cleanaddtiondatas(
		$additionfields
		,$addition_def
		,$additionfields_fnm
		,$additionfields_del
		,$additionfields_date
		,$additionfields_alt
		);


//            $hits =0;
//            $comments=0;
    $old_mode=COM_applyFilter($_POST['old_mode']);
    $old_mode=addslashes (COM_checkHTML (COM_checkWords ($old_mode)));

    //-----
    $type=1;
    $uuid=$_USER['uid'];


    // CHECK　はじめ
    $err="";
    //id
    if ($id==0 ){
        //$err.=$LANG_DATABOX_ADMIN['err_uid']."<br/>".LB;
    }else{
        if (!is_numeric($id) ){
            $err.=$LANG_DATABOX_ADMIN['err_id']."<br/>".LB;
        }
    }

    //タイトル必須
    if (empty($title)){
        $err.=$LANG_DATABOX_ADMIN['err_title']."<br/>".LB;
    }
	//文字数制限チェック
	if (mb_strlen($description, 'UTF-8')>$_DATABOX_CONF['maxlength_description']) {
		$err.=$LANG_DATABOX_ADMIN['description']
				.$_DATABOX_CONF['maxlength_description']
				.$LANG_DATABOX_ADMIN['err_maxlength']."<br/>".LB;
	}

    //----追加項目チェック
    $err.=DATABOX_checkaddtiondatas
        ($additionfields,$addition_def,$pi_name,$additionfields_fnm
        ,$additionfields_del,$additionfields_alt);

    //errorのあるとき
    if ($err<>"") {
        $retval['title']=$LANG_DATABOX_ADMIN['piname'].$LANG_DATABOX_ADMIN['edit'];
        $retval['display']= fncEdit($id, $edt_flg,3,$err,"edit",$fieldset_id,$template,$old_mode);

        return $retval;

    }
    // CHECK　おわり

    //-----
    // 新規登録時
    if ($new_flg){
       $w=DB_getItem($_TABLES['DATABOX_base'],"max(id)","1=1");
        if ($w=="") {
            $w=0;
        }
        $id=$w+1;
    }

    $fields=LB."id";
    $values=LB."$id";


    if ($new_flg){

        if  ($_DATABOX_CONF['datacode']){
            $code="000000".date(Ymdhis);

        }
		$created=COM_convertDate2Timestamp(date("Y-m-d"),date("H:i::00"));
        $modified=$created;
        $released=$created;
        $commentcode =$_DATABOX_CONF['commentcode'];
        $trackbackcode=$_CONF[trackback_code];;
		
		$comment_expire='0000-00-00 00:00:00';
        $expired='0000-00-00 00:00:00';
        //

        $defaulttemplatesdirectory=null;
        $draft_flag =$_DATABOX_CONF['user_draft_default'];
        $draft_flag =$_DATABOX_CONF['user_draft_default'];

        //---
        $meta_description = "";
        $meta_keywords = "";
        $owner_id =$_USER['uid'];
        $group_id =SEC_getFeatureGroup('databox.admin', $_USER['uid']);


        $array = array();
        SEC_setDefaultPermissions($array, $_DATABOX_CONF['default_permissions']);
        $perm_owner = $array['perm_owner'];
        $perm_group = $array['perm_group'];
        $perm_anon = $array['perm_anon'];
        $perm_members = $array['perm_members'];

        $draft_flag=$_DATABOX_CONF['user_draft_default'];
        $cache_time=$_DATABOX_CONF['default_cache_time'];

        //-----

        $fields.=",defaulttemplatesdirectory";//
        $values.=",'$defaulttemplatesdirectory'";


        $fields.=",draft_flag";
        $values.=",$draft_flag";
		
		$fields.=",cache_time";
        $values.=",$cache_time";

        $fields.=",meta_description";//
        $values.=",'$meta_description'";

        $fields.=",meta_keywords";//
        $values.=",'$meta_keywords'";

        $fields.=",commentcode";//
        $values.=",$commentcode";
		
		$fields.=",trackbackcode";//
        $values.=",$trackbackcode";

        $fields.=",comment_expire";//
		if ($comment_expire=='0000-00-00 00:00:00'){
			$values.=",'$comment_expire'";
		}else{
			$values.=",FROM_UNIXTIME('$comment_expire')";
		}

        $fields.=",language_id";//
        $values.=",'$language_id'";

        $fields.=",owner_id";
        $values.=",$owner_id";

        $fields.=",group_id";
        $values.=",$group_id";

        $fields.=",perm_owner";
        $values.=",$perm_owner";

        $fields.=",perm_group";
        $values.=",$perm_group";

        $fields.=",perm_members";
        $values.=",$perm_members";

        $fields.=",perm_anon";
        $values.=",$perm_anon";

		$fields.=",modified";
		$values.=",FROM_UNIXTIME('$modified')";

		$fields.=",created";
		$values.=",FROM_UNIXTIME('$created')";

        $fields.=",expired";
		if ($expired=='0000-00-00 00:00:00'){
			$values.=",'$expired'";
		}else{
			$values.=",FROM_UNIXTIME('$expired')";
		}

        $fields.=",released";
		$values.=",FROM_UNIXTIME('$released')";

        $hits=0;
        $comments=0;

        $fields.=",code";
        $values.=",'$code'";

        $fields.=",title";//
        $values.=",'$title'";

        $fields.=",page_title";//
        $values.=",'$page_title'";

        $fields.=",description";//
        $values.=",'$description'";


//        $fields.=",hits";//
//        $values.=",$hits";

        $fields.=",comments";//
        $values.=",$comments";

		$fields.=",fieldset_id";//
		$values.=",$fieldset_id";

        $fields.=",uuid";
        $values.=",$uuid";

        if ($edt_flg){
            $return_page=$_CONF['site_url'] . "/".THIS_SCRIPT;
            $return_page.="?id=".$id;
        }else{
            $return_page=$_CONF['site_url'] . '/'.THIS_SCRIPT.'?msg=1';
        }

        DB_save($_TABLES['DATABOX_base'],$fields,$values);
    }else{
		
        $sql="UPDATE {$_TABLES['DATABOX_base']} set ";
        $sql.=" title = '$title'";
        $sql.=" ,page_title = '$page_title'";
        $sql.=" ,description = '$description'";
        $sql.=" ,language_id = '$language_id'";
		
        $sql.=" ,modified = FROM_UNIXTIME('$modified')";
		
        $sql.=",uuid='$uuid' WHERE id=$id";

        DB_query($sql);

    }

    //カテゴリ
    //$rt=DATABOX_savedatas("category_id",$_TABLES['DATABOX_category'],$id,$category);
    $rt=DATABOX_savecategorydatas($id,$category,'databox','mydata');

	//追加項目
	if  ($old_mode=="copy"){
        DATABOX_uploadaddtiondatas_cpy
        ($additionfields,$addition_def,$pi_name,$id,$additionfields_fnm
        ,$additionfields_del,$additionfields_old,$additionfields_alt);
    }else{
        DATABOX_uploadaddtiondatas	
        ($additionfields,$addition_def,$pi_name,$id,$additionfields_fnm
        ,$additionfields_del,$additionfields_old,$additionfields_alt);
    }
		
    if ($new_flg){
        $rt=DATABOX_saveaddtiondatas($id,$additionfields,$addition_def,$pi_name);
    }else{
        $rt=DATABOX_saveaddtiondatas_update($id,$additionfields,$addition_def,$pi_name);
    }

    $rt=fncsendmail ('data',$id);
    
    $cacheInstance = 'databox__' . $id . '__' ;
    CACHE_remove_instance($cacheInstance); 

//exit;//@@@@@debug 用

    if ($_DATABOX_CONF['aftersave']==='no'){
        $retval['title']=$LANG_DATABOX_ADMIN['piname'].$LANG_DATABOX_ADMIN['edit'];
		$retval['display'] .= fncEdit($id, $edt_flg,1,$err,"edit",$fieldset_id,$template);
        return $retval;

    }else if ($_DATABOX_CONF['aftersave']==='list'
          OR $_DATABOX_CONF['aftersave']==='admin' ){
            $url = $_CONF['site_url'] . "/databox/mydata/data.php";
            $item_url=COM_buildURL($url);
            $target='item';
    }else{
        $url=$_CONF['site_url'] . "/databox/data.php";
        $url.="?";
        //コード使用の時
        if ($_DATABOX_CONF['datacode']){
            $url.="code=".$code;
            $url.="&amp;m=code";
        }else{
            $url.="id=".$id;
            $url.="&amp;m=id";
        }
        $item_url = COM_buildUrl( $url );
		$target=$_DATABOX_CONF['aftersave_admin'];
    }

    $return_page = PLG_afterSaveSwitch(
                    $target
                    ,$item_url
                    ,$pi_name
                    , 1);

    echo $return_page;

	exit;

}
// +---------------------------------------------------------------------------+
// | 機能  削除                                                                |
// | 書式 fncdelete ()                                                         |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:戻り画面＆メッセージ                                           |
// +---------------------------------------------------------------------------+
function fncdelete (
	$template
)
{
    global $_CONF;
    global $_TABLES;
    global $LANG_DATABOX_ADMIN;
	
	$pi_name="databox";
    $id = COM_applyFilter($_POST['id'],true);
    $title=DB_getItem ($_TABLES['DATABOX_base'], 'title',"id = ".$id);
	
	$addition_def=DATABOX_getadditiondef();//@@@@@
	$additionfields=$_POST['afield'];//@@@@@

    // CHECK
    $err="";
    if ($err<>"") {
        $page_title=$LANG_DATABOX_ADMIN['err'];
        $retval .= DATABOX_siteHeader('DATABOX','_admin',$page_title);
        $retval .= COM_startBlock ($LANG_DATABOX_ADMIN['err'], '',
                            COM_getBlockTemplate ('_msg_block', 'header'));
        $retval .= $err;
        $retval .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
        $retval .= DATABOX_siteFooter('DATABOX','_admin');
        return $retval;
    }

    //
	$rt=databox_deletedata ($id);

    $rt=fncsendmail ('data_delete',$id,$title);
	
    $cacheInstance = 'databox__' . $id . '__' ;
    CACHE_remove_instance($cacheInstance); 

    //exit;// debug 用
	
    $return_page=$_CONF['site_url'] . '/'.THIS_SCRIPT.'?msg=2';
	if  ($template<>""){
		$return_page.="&amp;template=".$template;
	}
    return COM_refresh ($return_page);



}
// +---------------------------------------------------------------------------+
// | 機能  メール送信                                                          |
// | 書式 fncsendmail ()                                                       |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
function fncsendmail (
    $m=""
    ,$id=0
    ,$title=""
    )
{
    global $_CONF;
    global $_TABLES;
    global $LANG_DATABOX_MAIL;
    global $LANG_DATABOX_ADMIN;
    global $_USER ;
    global $_DATABOX_CONF ;
	
	$pi_name="databox";
    $retval = '';

    $site_name=$_CONF['site_name'];
    $subject= sprintf($LANG_DATABOX_MAIL['subject_'.$m],$_USER['username']);
    $message=sprintf($LANG_DATABOX_MAIL['message_'.$m],$_USER['username'],$_USER['uid']);
    if ($m==="data_delete"){
        $msg= $LANG_DATABOX_ADMIN['id'].":".$id.LB;
        $msg.= $LANG_DATABOX_ADMIN['title'].":".$title.LB;
        //URL
        $url=$_CONF['site_url'] . "/databox/data.php";
        $url = COM_buildUrl( $url );
		$A['draft_flag']=0;
    }else{
        $sql = "SELECT ";

        $sql .= " *";

        $sql .= " FROM ";
        $sql .= $_TABLES['DATABOX_base'];
        $sql .= " WHERE ";
        $sql .= " id = $id";

        $result = DB_query ($sql);
        $numrows = DB_numRows ($result);

        if ($numrows > 0) {

            $A = DB_fetchArray ($result);
			$A = array_map('stripslashes', $A);

            //下書
            if ($A['draft_flag']==1) {
                $msg.=$LANG_DATABOX_ADMIN['draft'].LB;
            }

            //基本項目
            $msg.= $LANG_DATABOX_ADMIN['code'].":".$A['code'].LB;
            $msg.= $LANG_DATABOX_ADMIN['title'].":".$A['title'].LB;
            $msg.= $LANG_DATABOX_ADMIN['page_title'].":".$A['page_title'].LB;
            $msg.= $LANG_DATABOX_ADMIN['description'].":".$A['description'].LB;

            //カテゴリ
            $msg.=DATABOX_getcategoriesText($id ,0,"DATABOX");

            //追加項目
            $group_id = stripslashes($A['group_id']);
            $owner_id = stripslashes($A['owner_id']);
            $chk_user=DATABOX_chkuser($group_id,$owner_id,"databox.admin");
            $addition_def=DATABOX_getadditiondef();
            $additionfields = DATABOX_getadditiondatas($id);
            $msg.=DATABOX_getaddtionfieldsText($additionfields,$addition_def,$chk_user,$pi_name,$A['fieldset_id']);

            //タイムスタンプ　更新ユーザ
            $msg.= $LANG_DATABOX_ADMIN['udatetime'].":".$A['udatetime'].LB;
            $msg.= $LANG_DATABOX_ADMIN['uuid'].":".$A['uuid'].LB;


            //URL
            $url=$_CONF['site_url'] . "/databox/data.php";
            $url.="?";
            if ($_DATABOX_CONF['datacode']){
                $url.="m=code";
                $url.="&code=".$A['code'];
            }else{
                $url.="m=id";
                $url.="&id=".$A['id'];
            }
            $url = COM_buildUrl( $url );

        }
	}
	if  (($_DATABOX_CONF['mail_to_draft']==0) AND ($A['draft_flag']==1)){
	}else{

		$message.=$msg.LB;
		$message.=$url.LB;
		$message.=$LANG_DATABOX_MAIL['sig'].LB;

		$mail_to=$_DATABOX_CONF['mail_to'];
		//--- to owner
		if  ($_DATABOX_CONF['mail_to_owner']==1){
			$owner_email=DB_getItem($_TABLES['users'],"email","uid=".$A['owner_id']);
			if (array_search($owner_email,$mail_to)===false){
				$to=$owner_email;
				COM_mail ($to, $subject, $message);
			}
		}
		//--- mail_to
		if (!empty ($mail_to)){
			$to=implode($mail_to,",");
			COM_mail ($to, $subject, $message);
		}
	}

    return $retval;
}


function fncNew (
	$template
)
{
	global $_CONF;
	global $LANG_DATABOX_ADMIN;
	global $LANG_ADMIN;
	
	$pi_name="databox";
	
    $retval = '';
	
	//-----
    $tmplfld=DATABOX_templatePath('mydata',$template,$pi_name);

    $templates = new Template($tmplfld);
    $templates->set_file('editor',"selectset.thtml");
	
    $templates->set_var('site_url', $_CONF['site_url']);
    $templates->set_var('site_admin_url', $_CONF['site_admin_url']);
	
    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);
    $templates->set_var('gltoken_name', CSRF_TOKEN);
    $templates->set_var('gltoken', $token);
    $templates->set_var ( 'xhtml', XHTML );
	
	$script=THIS_SCRIPT;
	if  ($template<>""){
		$script.="?template=".$template;
	}
    $templates->set_var('script', $script);

	//fieldset_id
	$fieldset_id=0;
	$templates->set_var('lang_fieldset', $LANG_DATABOX_ADMIN['fieldset']);
	$list_fieldset=DATABOX_getoptionlist("fieldset",$fieldset_id,0,$pi_name,"",0 );
	$templates->set_var ('list_fieldset', $list_fieldset);
	
	$templates->set_var ('lang_inst_newdata', $LANG_DATABOX_ADMIN['inst_newdata']);
	
    $templates->set_var ('lang_new', $LANG_DATABOX_ADMIN['new']);
    $templates->set_var('lang_cancel', $LANG_ADMIN['cancel']);

	$templates->parse('output', 'editor');
    $retval .= $templates->finish($templates->get_var('output'));
	
	return $retval;
}
function fncMenu(
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能  menu表示  
// | 書式 fncMenu("databox")
// +---------------------------------------------------------------------------+
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 menu 
// +---------------------------------------------------------------------------+
{

    global $_CONF;
    global $_DATABOX_CONF;
    global $LANG_ADMIN;

    global $LANG_DATABOX_ADMIN;

    global $LANG_DATABOX;

    $retval = '';
	//MENU1:管理画面
    $url1=$_CONF['site_url'] . '/'.THIS_SCRIPT.'?mode=new';
	if  ($template<>""){
		$url1.="&amp;template=".$template;
	}
    $url2=$_CONF['site_url'] . '/databox/list.php';

    if ($_DATABOX_CONF['allow_data_insert']
            OR SEC_hasRights('databox.submit')){
        $menu_arr[]=array('url' => $url1,'text' => $LANG_DATABOX_ADMIN["new"]);
    }
    $menu_arr[]=array('url' => $url2,'text' => $LANG_DATABOX['list']);
    $retval .= ADMIN_createMenu(
        $menu_arr,
        $LANG_DATABOX_ADMIN['instructions'],
        plugin_geticon_databox()
    );

    return $retval;
}


// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
//############################
$pi_name    = 'databox';
//############################

// 引数
//public_html/mydata/data.php
//public_html/mydata/data.php?mode_id=new
//public_html/mydata/data.php?type_id=aaa
//public_html/mydata.php?mode_id=edit&id=1
//public_html/mydata.php?mode_id=edit&id=1&template=yyyy
if (isset ($_REQUEST['mode'])) {
    $mode = COM_applyFilter ($_REQUEST['mode'], false);
}
$msg = '';
if (isset ($_REQUEST['msg'])) {
    $msg = COM_applyFilter ($_REQUEST['msg'], true);
}
$id = '';
if (isset ($_REQUEST['id'])) {
    $id = COM_applyFilter ($_REQUEST['id'], true);
}

$fieldset_id = 0;
if (isset ($_REQUEST['type_id'])) {
    $fieldset_id = COM_applyFilter ($_REQUEST['type_id'], true);
}

$template = '';
if (isset ($_REQUEST['template'])) {
	$template = COM_applyFilter ($_REQUEST['template']);
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
}else if (($mode == $LANG_DATABOX_ADMIN['new']) && !empty ($LANG_DATABOX_ADMIN['new'])) {
    $mode="newedit";
}else if ($fieldset_id <> 0){
    $mode="newedit_type";
}


//echo "mode=".$mode."<br>";
if ($mode=="" OR $mode=="edit" OR $mode=="new" OR $mode=="drafton" OR $mode=="draftoff"
	OR $mode=="export" OR $mode=="import"  OR $mode=="copy"
	OR $mode=="newedit_type"
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
$menuno=2;
$display="";
$information = array();

//ログイン要否チェック
if (COM_isAnonUser()){
    $loginrequired=$_DATABOX_CONF['loginrequired'];
    $loginrequired=$_CONF['loginrequired'];

    if ($loginrequired>0) {
        $display .= DATABOX_siteHeader($pi_name,'',$page_title);
        $display .= SEC_loginRequiredForm();
        $display .= DATABOX_siteFooter($pi_name);
        COM_output($display);
        exit;
    }

}



//echo "mode=".$mode."<br>";
switch ($mode) {

    case 'new':// 新規登録
        if ($_DATABOX_CONF['allow_data_insert']
                OR SEC_hasRights('databox.submit')){

            $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'].$LANG_DATABOX_ADMIN['new'];
            $display .= fncNew($template);
            break;
        }
    case 'newedit':// 新規登録
		$fieldset_id=COM_applyFilter ($_POST['fieldset'],true);
    case 'newedit_type':// 新規登録
        if ($_DATABOX_CONF['allow_data_insert']
                OR SEC_hasRights('databox.submit')){

            $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'].$LANG_DATABOX_ADMIN['new'];
            $display .= fncEdit("", $edt_flg,$msg,"","new",$fieldset_id,$template);
            break;
        }
    case 'save':// 保存
        $retval= fncSave ($edt_flg ,$navbarMenu ,$menuno,$template);
        $information['pagetitle']=$retval['title'];
		$display.=$retval['display'];
		break;

    case 'delete':// 削除
        $display .= fncdelete($template);
        break;
    case 'copy'://コピー
        if ($_DATABOX_CONF['allow_data_insert']
                OR SEC_hasRights('databox.submit')){
        }else{
            $id="";
            $display.=$rt;
        }
    case 'edit':// 編集
        if ($id<>""  ) {
            $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'].$LANG_DATABOX_ADMIN['edit'];
            $rt=databox_chk_loaddata($id);
            if ($rt==="OK"){
                $display .= fncEdit($id, $edt_flg,$msg,"",$mode,$fieldset_id,$template);
            }else{
                $display.=$rt;
            }
        }
        break;

    default:// 初期表示、一覧表示

        $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'];
        if (isset ($msg)) {
            $display .= COM_showMessage ($msg,'databox');
        }

        $display .= fncList($template);


}
$display =COM_startBlock($LANG_DATABOX_ADMIN['piname'],''
            ,COM_getBlockTemplate('_admin_block', 'header'))
         .ppNavbarjp($navbarMenu,$LANG_DATABOX_admin_menu[$menuno])
         .fncMenu($pi_name)
         .$display
         .COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

$display=DATABOX_displaypage($pi_name,'_admin',$display,$information);


COM_output($display);

?>
