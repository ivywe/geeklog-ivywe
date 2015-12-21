<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | profile maintenannce
// +---------------------------------------------------------------------------+
// $Id: data.php
// public_html/admin/plugins/userbox/profile.php
// 20101207 tsuchitani AT ivywe DOT co DOT jp

// @@@@@追加予定：import
// @@@@@追加予定：export に category
// @@@@@追加予定：日付入力
//--------------------------------
//@@@@@@追加予定（案）
//@@@@@@最後のログイン日時（表示）
//@@@@@@ユーザ名（変更）
//@@@@@@メールアドレス（変更）
//@@@@@@ホームページ（変更）
//@@@@@@グループ（変更）
//@@@@@@居住地（変更）
//@@@@@@写真（変更）
//@@@@@@署名（変更）
//@@@@@@自己紹介（変更）
//@@@@@@PGP鍵（変更）
//@@@@@@デイリーニュースを受信するかどうか？


define ('THIS_SCRIPT', 'userbox/profile.php');
//define ('THIS_SCRIPT', 'userbox/test.php');

require_once('userbox_functions.php');
require_once($_CONF['path_system'] . 'lib-user.php');
require_once( $_CONF['path_system'] . 'lib-admin.php' );

function fncList()
// +---------------------------------------------------------------------------+
// | 機能  一覧表示                                                            |
// | 書式 fncList()                                                            |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:一覧                                                           |
// +---------------------------------------------------------------------------+
{

    $pi_name="userbox";

    global $_CONF;
    global $_TABLES;
    global $LANG_ADMIN;
    global $LANG09;
    global $LANG28;
    global $LANG_USERBOX_ADMIN;
    global $LANG_USERBOX;
    global $_USERBOX_CONF;
	
    $table  = $_TABLES['USERBOX_base'];
    $table1 = $_TABLES['users'];
	$table2 = $_TABLES['USERBOX_def_fieldset'];
	$table5 = $_TABLES['USERBOX_stats'];

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
        $exclude=" AND t.fieldset_id={$filter_val}";
    }

    $filter = "{$LANG_USERBOX_ADMIN['fieldset']}:";
    $filter .="<select name='filter_val' style='width: 125px' onchange='this.form.submit()'>";
    $filter .="<option value='{$LANG09[9]}'";

    if  ($filter_val==$LANG09[9]){
        $filter .=" selected='selected'";
    }
    $filter .=" >{$LANG09[9]}</option>";
    $filter .= COM_optionList ($_TABLES['USERBOX_def_fieldset']
                , 'fieldset_id,name', $filter_val,0,"fieldset_id<>0");

    $filter .="</select>";

    //ヘッダ：編集～
    $header_arr[]=array('text' => $LANG_USERBOX_ADMIN['orderno'], 'field' => 'orderno', 'sort' => true);
    $header_arr[]=array('text' => $LANG_ADMIN['edit'], 'field' => 'editid', 'sort' => false);

    $header_arr[]=array('text' => $LANG28['2'], 'field' => 'id', 'sort' => true);
    $header_arr[]=array('text' => $LANG28['3'], 'field' => 'username', 'sort' => username);
    $header_arr[]=array('text' => $LANG28['4'], 'field' => 'fullname', 'sort' => fullname);
    $header_arr[]=array('text' => $LANG_USERBOX_ADMIN['fieldset'], 'field' => 'fieldset_name', 'sort' => true);
    $header_arr[]=array('text' => $LANG_USERBOX_ADMIN['hits'], 'field' => 'hits', 'sort' => true);
    $header_arr[]=array('text' => $LANG_USERBOX_ADMIN['udatetime'], 'field' => 'udatetime', 'sort' => true);
    $header_arr[]=array('text' => $LANG_USERBOX_ADMIN['draft'], 'field' => 'draft_flag', 'sort' => true);
    //
    $text_arr = array('has_menu' =>  true,
      'has_extras'   => true,
      'form_url' => $_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT);

    //Query
    $sql = "SELECT ";
    $sql .= " t.id";
    $sql .= " ,draft_flag";
    $sql .= " ,modified";
    $sql .= " ,UNIX_TIMESTAMP(t.udatetime) AS udatetime";
    $sql .= " ,orderno";
    $sql .= " ,t2.name AS fieldset_name";
    $sql .= " ,t.fieldset_id";
    $sql .= " ,t5.hits";

    $sql .= " ,t1.username";
    $sql .= " ,t1.fullname";

    $sql .= " FROM ";
    $sql .= " {$table} AS t";
	$sql .= " JOIN {$table1} AS t1       ON t.id=t1.uid";
	$sql .= " JOIN {$table2} AS t2       ON t.fieldset_id=t2.fieldset_id";
	$sql .= " LEFT JOIN {$table5} AS t5  ON t.id=t5.id";
	$sql .= " WHERE 1=1";	

    $query_arr = array(
        'table' => " {$table} AS t ,{$table1} AS t1",
        'sql' => $sql,
        'query_fields' => array('t.id','t1.username','t1.fullname','t.draft_flag','t.orderno','hits'),
        'default_filter' => $exclude);
	//デフォルトソート項目:
	if  ($_USERBOX_CONF["sort_list_by"]=="udatetime"){
		$defsort_arr = array('field' => 'udatetime', 'direction' => 'DESC');
	}else{
		$defsort_arr = array('field' => $_USERBOX_CONF["sort_list_by"], 'direction' => 'ASC');
	}
	$form_arr = array('bottom' => '', 'top' => '');
    $pagenavurl = '&amp;filter_val=' . $filter_val;
    //List 取得
	if (COM_versionCompare(VERSION, "2.0.0",  '>=')){
		$retval .= ADMIN_list(
			'userbox'
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
			'userbox'
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
// | 一覧取得 ADMIN_list で使用
// +---------------------------------------------------------------------------+
function fncGetListField($fieldname, $fieldvalue, $A, $icon_arr)
{

    $pi_name="userbox";

    global $_CONF;
    global $LANG_ACCESS;
    global $_USERBOX_CONF;

    $retval = '';

    switch($fieldname) {
        //編集アイコン
        case 'editid':
            $url=$_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT;
            $url.="?";
            $url.="mode=edit";
            $url.="&amp;id=".$A['id'];
            $retval = COM_createLink($icon_arr['edit'],$url);
            break;

        case 'id':
            $url=$_CONF['site_url'] . "/userbox/profile.php";
            $url.="?";
            $url.="id=".$A['id'];
            $url.="&amp;m=id";
            $url = COM_buildUrl( $url );
            $retval= COM_createLink($A['id'], $url);
            break;
        case 'username':
            $username=COM_applyFilter($A['username']);
            $url=$_CONF['site_url'] . "/userbox/profile.php";
            $url.="?";
            $url.="code=".$A['username'];
            $url.="&amp;m=code";
            $url = COM_buildUrl( $url );
            $retval= COM_createLink($username, $url);
            break;
        //属性セット名
		case 'fieldset_name':
            $name=COM_applyFilter($A['fieldset_name']);
            $url=$_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT;
            $url.="?";
            $url.="mode=changeset";
            $url.="&amp;id=".$A['id'];
			$retval = COM_createLink($name,$url);
            break;
        //下書
        case 'draft_flag':
            if ($A['draft_flag'] == 1) {
                $switch = 'checked="checked"';
            } else {
                $switch = '';
            }
            $query_limit=COM_applyFilter($_REQUEST['query_limit']);
            $direction=COM_applyFilter($_REQUEST['direction']);
            $filter_val=COM_applyFilter($_REQUEST['filter_val']);
            $databoxlistpage=COM_applyFilter($_REQUEST['databoxlistpage']);
            $order=COM_applyFilter($_REQUEST['order'],true);
            $prevorder=COM_applyFilter($_REQUEST['prevorder']);

            $retval = "<form action=\"{$_CONF['site_admin_url']}";
            $retval .= "/plugins/".THIS_SCRIPT."\" method=\"post\">";
            $retval .= "<input type=\"checkbox\" name=\"drafton\" ";
            $retval .= "onclick=\"submit()\" value=\"{$A['draft_flag']}\" $switch>";
            $retval .= "<input type=\"hidden\" name=\"draftChange\" ";
            $retval .= "value=\"{$A['id']}\">";

            $retval .= "<input type=\"hidden\" name=\"".CSRF_TOKEN."\"";
            $retval .= " value=\"".SEC_createToken()."\"".XHTML.">";

            $retval .= "<input type=\"hidden\" name=\"order\" value=\"$order\" />";
            $retval .= "<input type=\"hidden\" name=\"prevorder\" value=\"$prevorder\" />";
            $retval .= "<input type=\"hidden\" name=\"query_limit\" value=\"$query_limit\" />";
            $retval .= "<input type=\"hidden\" name=\"direction\" value=\"$direction\" />";
            $retval .= "<input type=\"hidden\" name=\"filter_val\" value=\"$filter_val\" />";
            $retval .= "<input type=\"hidden\" name=\"databoxlistpage\" value=\"$databoxlistpage\" />";

            $retval .= "</form>";
            break;
 		case 'udatetime':
			$curtime = COM_getUserDateTimeFormat($A['udatetime']);
			$retval = $curtime[0];
			break;

       //各項目
        default:
            $retval = $fieldvalue;
            break;
    }

    return $retval;

}

function fncEdit(
    $id
    ,$edt_flg
    ,$msg = ''
    ,$errmsg=""
    ,$mode="edit"
)
// +---------------------------------------------------------------------------+
// | 機能  編集画面表示                                                        |
// | 書式 fncEdit($id , $edt_flg,$msg,$errmsg)                                 |
// +---------------------------------------------------------------------------+
// | 引数 $id:                                                                 |
// | 引数 $edt_flg:                                                            |
// | 引数 $msg:メッセージ番号                                                  |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:編集画面                                                       |
// +---------------------------------------------------------------------------+
//
{

    $pi_name="userbox";

    global $_CONF;
    global $_TABLES;
    global $LANG_ADMIN;
    global $MESSAGE;
    global $LANG_ACCESS;
    global $_USER;
    global $LANG28;
	global $_SCRIPTS;

    global $_USERBOX_CONF;
    global $LANG_USERBOX_ADMIN;
    global $LANG_USERBOX;

    $retval = '';

    $delflg=false;

    $addition_def=DATABOX_getadditiondef($pi_name);

    //メッセージ表示
    if (!empty ($msg)) {
        $retval .= COM_showMessage ($msg,$pi_name);
        $retval .= $errmsg;
        // clean 'em up
        $code=COM_applyFilter($_POST['code']);//@@@@@
        $title = COM_applyFilter($_POST['title']);//@@@@@
        $username=COM_applyFilter($_POST['username']);//@@@@@
        $fullname = COM_applyFilter($_POST['fullname']);//@@@@@

        $page_title = COM_applyFilter($_POST['page_title']);
        $description=$_POST['description'];//COM_applyFilter($_POST['description']);
        $defaulttemplatesdirectory = COM_applyFilter($_POST['defaulttemplatesdirectory']);//@@@@@@

        $draft_flag = COM_applyFilter ($_POST['draft_flag'],true);
        $hits = COM_applyFilter ($_POST['hits'],true);
        $comments = COM_applyFilter ($_POST['comments'],true);
        $commentcode = COM_applyFilter ($_POST['commentcode'],true);
        $trackbackcode = COM_applyFilter ($_POST['trackbackcode'],true);
        $cache_time = COM_applyFilter ($_POST['cache_time'],true);

        //@@@@@
        $comment_expire_flag = COM_applyFilter ($_POST['comment_expire_flag'],true);
        if ($comment_expire_flag===0){
            $w = mktime(0, 0, 0, date('m'),
               date('d') + $_CONF['article_comment_close_days'], date('Y'));
               $comment_expire_year=date('Y', $w);
            $comment_expire_month=date('m', $w);
            $comment_expire_day=date('d', $w);
            $comment_expire_hour=0;
            $comment_expire_minute=0;
        }else{
            $comment_expire_month = COM_applyFilter ($_POST['comment_expire_month'],true);
            $comment_expire_day = COM_applyFilter ($_POST['comment_expire_day'],true);
            $comment_expire_year = COM_applyFilter ($_POST['comment_expire_year'],true);
            $comment_expire_hour = COM_applyFilter ($_POST['comment_expire_hour'],true);
            $comment_expire_minute = COM_applyFilter ($_POST['comment_expire_minute'],true);
        }

        $meta_description = COM_applyFilter ($_POST['meta_description']);
        $meta_keywords = COM_applyFilter ($_POST['meta_keywords']);
		$language_id = COM_applyFilter ($_POST['language_id']);
		
        $category = $_POST['category'];

        $additionfields=$_POST['afield'];
        $additionfields_fnm=$_POST['afield_fnm'];//@@@@@
		$additionfields_del=$_POST['afield_del'];
		$additionfields_date=array();
        $additionfields_alt=$_POST['afield_alt'];;
		$additionfields=DATABOX_cleanaddtiondatas(
			$additionfields
			,$addition_def
			,$additionfields_fnm
			,$additionfields_del
			,$additionfields_date
			,$additionfields_alt
			,false
			);

        $owner_id = COM_applyFilter ($_POST['owner_id'],true);	
        $group_id = COM_applyFilter ($_POST['group_id'],true);
        //
        $array['perm_owner']=$_POST['perm_owner'];
        $array['perm_group']=$_POST['perm_group'];
        $array['perm_members']=$_POST['perm_members'];
        $array['perm_anon']=$_POST['perm_anon'];

        if (is_array($array['perm_owner']) || is_array($array['perm_group']) ||
                is_array($array['perm_members']) ||
                is_array($array['perm_anon'])) {

            list($perm_owner, $perm_group, $perm_members, $perm_anon)
                = SEC_getPermissionValues($array['perm_owner'], $array['perm_group'], $array['perm_members'], $array['perm_anon']);

        } else {
            $perm_owner   = $array['perm_owner'];
            $perm_group   = $array['perm_group'];
            $perm_members = $array['perm_members'];
            $perm_anon    = $array['perm_anon'];
        }


        //編集日
        $modified_autoupdate = COM_applyFilter ($_POST['modified_autoupdate'],true);
        $modified_month = COM_applyFilter ($_POST['modified_month'],true);
        $modified_day = COM_applyFilter ($_POST['modified_day'],true);
        $modified_year = COM_applyFilter ($_POST['modified_year'],true);
        $modified_hour = COM_applyFilter ($_POST['modified_hour'],true);
        $modified_minute = COM_applyFilter ($_POST['modified_minute'],true);
        //公開日
        $released_month = COM_applyFilter ($_POST['released_month'],true);
        $released_day = COM_applyFilter ($_POST['released_day'],true);
        $released_year = COM_applyFilter ($_POST['released_year'],true);
        $released_hour = COM_applyFilter ($_POST['released_hour'],true);
        $released_minute = COM_applyFilter ($_POST['released_minute'],true);
        //公開終了日
        $expired_available = COM_applyFilter ($_POST['expired_available'],true);
        $expired_flag = COM_applyFilter ($_POST['expired_flag'],true);

        if ($expired_flag===0){
            $w = mktime(0, 0, 0, date('m'),
                date('d') + $_CONF['article_comment_close_days'], date('Y'));
            $expired_year=date('Y', $w);
            $expired_month=date('m', $w);
            $expired_day=date('d', $w);
            $expired_hour=0;
            $expired_minute=0;
        }else{
            $expired_month = COM_applyFilter ($_POST['expired_month'],true);
            $expired_day = COM_applyFilter ($_POST['expired_day'],true);
            $expired_year = COM_applyFilter ($_POST['expired_year'],true);
            $expired_hour = COM_applyFilter ($_POST['expired_hour'],true);
            $expired_minute = COM_applyFilter ($_POST['expired_minute'],true);
        }
        //作成日付
        $created = COM_applyFilter ($_POST['created']);
        $created_un = COM_applyFilter ($_POST['created_un']);
		

        $orderno = COM_applyFilter ($_POST['orderno']);

        $uuid=$_USER['uid'];
        $udatetime=COM_applyFilter ($_POST['udatetime']);//"";

        $fieldset_id=COM_applyFilter ($_POST['fieldset'],true);//"";
        $fieldset_name=COM_applyFilter ($_POST['fieldset_name']);//"";

    }else{

        $sql = "SELECT ";

        $sql .= " t.*";
		$sql .= " ,t2.name AS fieldset_name".LB;

        $sql .= " ,t1.username";
        $sql .= " ,t1.fullname";

		$sql .= " ,UNIX_TIMESTAMP(t.modified) AS modified_un".LB;
		$sql .= " ,UNIX_TIMESTAMP(t.released) AS released_un".LB;
		$sql .= " ,UNIX_TIMESTAMP(t.comment_expire) AS comment_expire_un".LB;
		$sql .= " ,UNIX_TIMESTAMP(t.expired) AS expired_un".LB;
		$sql .= " ,UNIX_TIMESTAMP(t.udatetime) AS udatetime_un".LB;
		$sql .= " ,UNIX_TIMESTAMP(t.created) AS created_un".LB;
		
        $sql .= " FROM ";
        $sql .= $_TABLES['USERBOX_base'] ." AS t";
        $sql .= ",".$_TABLES['users'] ." AS t1";
		$sql .= ",".$_TABLES['USERBOX_def_fieldset'] ." AS t2 ".LB;

        $sql .= " WHERE ";
        $sql .= " t.id = $id";
        $sql .= " AND t.id = t1.uid";
		$sql .= " AND t.fieldset_id = t2.fieldset_id".LB;

        $result = DB_query($sql);

        $A = DB_fetchArray($result);
		
		$fieldset_id = COM_stripslashes($A['fieldset_id']);
        $fieldset_name = COM_stripslashes($A['fieldset_name']);

        $code = COM_stripslashes($A['code']);//@@@@@
        $title=COM_stripslashes($A['title']);//@@@@@
        $username = COM_stripslashes($A['username']);//@@@@@
        $fullname=COM_stripslashes($A['fullname']);//@@@@@

        $page_title=COM_stripslashes($A['page_title']);
        $description=COM_stripslashes($A['description']);
        $defaulttemplatesdirectory=COM_stripslashes($A['defaulttemplatesdirectory']);

        $hits = COM_stripslashes($A['hits']);

        $comments = COM_stripslashes($A['comments']);
        $comment_expire = COM_stripslashes($A['comment_expire']);
        if ($comment_expire==="0000-00-00 00:00:00"){
            $comment_expire_flag=0;
            $w = mktime(0, 0, 0, date('m'),
               date('d') + $_CONF['article_comment_close_days'], date('Y'));
            $comment_expire_year=date('Y', $w);
            $comment_expire_month=date('m', $w);
            $comment_expire_day=date('d', $w);
            $comment_expire_hour=0;
            $comment_expire_minute=0;
        }else{
            $comment_expire_flag=1;
			$wary = COM_getUserDateTimeFormat(COM_stripslashes($A['comment_expire_un']));
			$comment_expire = $wary[1];
            $comment_expire_year=date('Y', $comment_expire);
            $comment_expire_month=date('m', $comment_expire);
            $comment_expire_day=date('d', $comment_expire);
            $comment_expire_hour=date('H', $comment_expire);
            $comment_expire_minute=date('i', $comment_expire);
         }

        $commentcode = COM_stripslashes($A['commentcode']);
        $trackbackcode = COM_stripslashes($A['trackbackcode']);
        $cache_time = COM_stripslashes($A['cache_time']);

        $meta_description = COM_stripslashes($A['meta_description']);
        $meta_keywords = COM_stripslashes($A['meta_keywords']);

        $language_id = COM_stripslashes($A['language_id']);

        $owner_id = COM_stripslashes($A['owner_id']);
        $group_id = COM_stripslashes($A['group_id']);

        $perm_owner = COM_stripslashes($A['perm_owner']);
        $perm_group = COM_stripslashes($A['perm_group']);
        $perm_members = COM_stripslashes($A['perm_members']);
        $perm_anon = COM_stripslashes($A['perm_anon']);

        $category = DATABOX_getdatas("category_id",$_TABLES['USERBOX_category'],"id = $id");

        $additionfields = DATABOX_getadditiondatas($id,$pi_name);
        $additionfields_fnm=array();//@@@@@
        $additionfields_del=array();
		$additionfields_date="";

        $draft_flag=COM_stripslashes($A['draft_flag']);

        //編集日
		$wary = COM_getUserDateTimeFormat(COM_stripslashes($A['modified_un']));
		$modified = $wary[1];
        //$modified = strtotime(COM_stripslashes($A['modified']));
        $modified_month = date('m', $modified);
        $modified_day = date('d', $modified);
        $modified_year = date('Y', $modified);
        $modified_hour = date('H', $modified);
        $modified_minute = date('i', $modified);
		//公開日
		$wary = COM_getUserDateTimeFormat(COM_stripslashes($A['released_un']));
		$released = $wary[1];
        //$released = strtotime(COM_stripslashes($A['released']));
        $released_month = date('m', $released);
        $released_day = date('d', $released);
        $released_year = date('Y', $released);
        $released_hour = date('H', $released);
        $released_minute = date('i', $released);
        //公開終了日
        $expired = COM_stripslashes($A['expired']);
        if ($expired==="0000-00-00 00:00:00"){
            $expired_flag=0;
            $w = mktime(0, 0, 0, date('m'),
               date('d') + $_CONF['article_comment_close_days'], date('Y'));
            $expired_year=date('Y', $w);
            $expired_month=date('m', $w);
            $expired_day=date('d', $w);
            $expired_hour=0;
            $expired_minute=0;
        }else{
            $expired_flag=1;
			$wary = COM_getUserDateTimeFormat(COM_stripslashes($A['expired_un']));
			$expired = $wary[1];
            $expired_year=date('Y', $expired);
            $expired_month=date('m', $expired);
            $expired_day=date('d', $expired);
            $expired_hour=date('H', $expired);
            $expired_minute=date('i', $expired);
        }

        //作成日付
		$wary = COM_getUserDateTimeFormat(COM_stripslashes($A['created_un']));
		$created = $wary[0];
		$created_un = $wary[1];

        $orderno=COM_stripslashes($A['orderno']);

        $uuid = COM_stripslashes($A['uuid']);

		$wary = COM_getUserDateTimeFormat(COM_stripslashes($A['udatetime_un']));
		$udatetime = $wary[0];

        if ($edt_flg==FALSE) {
            $delflg=true;
        }
    }

    //template フォルダ
    $tmplfld=DATABOX_templatePath('admin','default',$pi_name);
    $templates = new Template($tmplfld);

    $templates->set_file (array (
                'editor' => 'profile_editor.thtml',
                'row'   => 'row.thtml',
                'col'   => "profile_col_detail.thtml",
				));
	
	
    // Add JavaScript geeklog >=2.1.0
    // Loads jQuery UI datepicker and timepicker-addon
    $_SCRIPTS->setJavaScriptLibrary('jquery.ui.slider');
//    $_SCRIPTS->setJavaScriptLibrary('jquery.ui.button');
    $_SCRIPTS->setJavaScriptLibrary('jquery.ui.datepicker');
    $_SCRIPTS->setJavaScriptLibrary('jquery-ui-i18n');
    $_SCRIPTS->setJavaScriptLibrary('jquery-ui-timepicker-addon');
    $_SCRIPTS->setJavaScriptLibrary('jquery-ui-timepicker-addon-i18n');
//    $_SCRIPTS->setJavaScriptLibrary('jquery-ui-slideraccess');
    $_SCRIPTS->setJavaScriptFile('datetimepicker', '/javascript/datetimepicker.js');
    $_SCRIPTS->setJavaScriptFile('datepicker', '/javascript/datepicker.js');

    $langCode = COM_getLangIso639Code();
    $toolTip  = $MESSAGE[118];
    $imgUrl   = $_CONF['site_url'] . '/images/calendar.png';

    $_SCRIPTS->setJavaScript(
        "jQuery(function () {"
        . "  geeklog.hour_mode = {$_CONF['hour_mode']};"
        . "  geeklog.datetimepicker.set('comment_expire', '{$langCode}', '{$toolTip}', '{$imgUrl}');"
        . "  geeklog.datetimepicker.set('modified', '{$langCode}', '{$toolTip}', '{$imgUrl}');"
        . "  geeklog.datetimepicker.set('released', '{$langCode}', '{$toolTip}', '{$imgUrl}');"
        . "  geeklog.datetimepicker.set('expired', '{$langCode}', '{$toolTip}', '{$imgUrl}');"
        . "});", TRUE, TRUE
    );
	
    //--
    if (($_CONF['meta_tags'] > 0) && ($_USERBOX_CONF['meta_tags'] > 0)) {
        $templates->set_var('hide_meta', '');
    } else {
        $templates->set_var('hide_meta', ' style="display:none;"');
    }
    $templates->set_var('maxlength_description', $_USERBOX_CONF['maxlength_description']);
    $templates->set_var('maxlength_meta_description', $_USERBOX_CONF['maxlength_meta_description']);
    $templates->set_var('maxlength_meta_keywords', $_USERBOX_CONF['maxlength_meta_keywords']);

    $templates->set_var('about_thispage', $LANG_USERBOX_ADMIN['about_admin_profile']);
    $templates->set_var('lang_must', $LANG_USERBOX_ADMIN['must']);

    $templates->set_var('site_url', $_CONF['site_url']);
    $templates->set_var('site_admin_url', $_CONF['site_admin_url']);
	
	$templates->set_var('lang_ref', $LANG_USERBOX_ADMIN['ref']);
	$templates->set_var('lang_view', $LANG_USERBOX_ADMIN['view']);

    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);
    $templates->set_var('gltoken_name', CSRF_TOKEN);
    $templates->set_var('gltoken', $token);
    $templates->set_var ( 'xhtml', XHTML );

    $templates->set_var('script', THIS_SCRIPT);

    $templates->set_var('dateformat', $_USERBOX_CONF['dateformat']);

    //ビューリンク@@@@@
    $url=$_CONF['site_url'] . "/userbox/profile.php";
    $url.="?";
    if ($_USERBOX_CONF['datacode']){
        $url.="code=".$A['username'];
        $url.="&m=code";
    }else{
        $url.="id=".$A['id'];
        $url.="&m=id";
    }
    $url = COM_buildUrl( $url );
    $view= COM_createLink($LANG_USERBOX['view'], $url);
	$templates->set_var('view', $view);
	

//
    $templates->set_var('lang_link_admin', $LANG_USERBOX_ADMIN['link_admin']);
    $templates->set_var('lang_link_admin_top', $LANG_USERBOX_ADMIN['link_admin_top']);
    $templates->set_var('lang_link_public', $LANG_USERBOX_ADMIN['link_public']);
    $templates->set_var('lang_link_list', $LANG_USERBOX_ADMIN['link_list']);
    $templates->set_var('lang_link_detail', $LANG_USERBOX_ADMIN['link_detail']);
	
	//fieldset_id
    $templates->set_var('lang_fieldset', $LANG_USERBOX_ADMIN['fieldset']);
    $templates->set_var('fieldset_id', $fieldset_id);
    $templates->set_var('fieldset_name', $fieldset_name);
    //id
    $templates->set_var('lang_id', $LANG_USERBOX_ADMIN['id']);
    //@@@@@ $templates->set_var('help_id', $LANG_USERBOX_ADMIN['help']);
    $templates->set_var('id', $id);

    //下書
    $templates->set_var('lang_draft', $LANG_USERBOX_ADMIN['draft']);
    if  ($draft_flag==1) {
        $templates->set_var('draft_flag', "checked=checked");
    }else{
        $templates->set_var('draft_flag', "");
    }

    //
    $templates->set_var('lang_field', $LANG_USERBOX_ADMIN['field']);
    $templates->set_var('lang_fields', $LANG_USERBOX_ADMIN['fields']);
    $templates->set_var('lang_content', $LANG_USERBOX_ADMIN['content']);
    $templates->set_var('lang_templatesetvar', $LANG_USERBOX_ADMIN['templatesetvar']);

    //基本項目
    $templates->set_var('lang_basicfields', $LANG_USERBOX_ADMIN['basicfields']);
    //コード＆タイトル＆説明＆テンプレートセット値@@@@@
    $templates->set_var('lang_code', $LANG_USERBOX_ADMIN['code']);
    if ($_USERBOX_CONF['datacode']){
        $templates->set_var('lang_must_code', $LANG_USERBOX_ADMIN['must']);
    }else{
        $templates->set_var('lang_must_code', "");
    }
    $templates->set_var ('code', $code);
    $templates->set_var('lang_title', $LANG_USERBOX_ADMIN['title']);
    $templates->set_var ('title', $title);

//$LANG28 = array(
//    2 => 'ユーザID',
//    3 => 'ユーザ名', username
//    4 => '氏名', fullname
    $templates->set_var('lang_uid', $LANG28['2']);
    $templates->set_var('lang_username', $LANG28['3']);
    $templates->set_var ('username', $username);
    $templates->set_var('lang_fullname', $LANG28['4']);
    $templates->set_var ('fullname', $fullname);



    //
    $templates->set_var('lang_page_title', $LANG_USERBOX_ADMIN['page_title']);
    $templates->set_var ('page_title', $page_title);
    $templates->set_var('lang_description', $LANG_USERBOX_ADMIN['description']);
    $templates->set_var ('description', $description);
    $templates->set_var('lang_defaulttemplatesdirectory', $LANG_USERBOX_ADMIN['defaulttemplatesdirectory']);
	$templates->set_var ('defaulttemplatesdirectory', $defaulttemplatesdirectory);
	$select_defaulttemplatesdirectory=fnctemplatesdirectory($defaulttemplatesdirectory);
    $templates->set_var ('select_defaulttemplatesdirectory', $select_defaulttemplatesdirectory);//@@@@@

    //meta_description
    $templates->set_var('lang_meta_description', $LANG_USERBOX_ADMIN['meta_description']);
    $templates->set_var ('meta_description', $meta_description);

    //meta_keywords
    $templates->set_var('lang_meta_keywords', $LANG_USERBOX_ADMIN['meta_keywords']);
    $templates->set_var ('meta_keywords', $meta_keywords);
	
	//language_id
    if (is_array($_CONF['languages'])) {
        $templates->set_var('hide_language_id', '');
		$select_language_id=DATABOX_getoptionlist("language_id",$language_id,0,$pi_name,"",0 );
    } else {
        $templates->set_var('hide_language_id', ' style="display:none;"');
		$select_language_id="";
    }
    $templates->set_var('lang_language_id', $LANG_USERBOX_ADMIN['language_id']);
	$templates->set_var ('language_id', $language_id);
    $templates->set_var ('select_language_id', $select_language_id);//@@@@@

    //hits
    $templates->set_var('lang_hits', $LANG_USERBOX_ADMIN['hits']);
    $templates->set_var ('hits', $hits);

    //comments
    $templates->set_var('lang_comments', $LANG_USERBOX_ADMIN['comments']);
    $templates->set_var ('comments', $comments);

    //commentcode
    $templates->set_var('lang_commentcode', $LANG_USERBOX_ADMIN['commentcode']);
    $templates->set_var ('commentcode', $commentcode);
    $optionlist_commentcode=COM_optionList ($_TABLES['commentcodes'], 'code,name',$commentcode);
    $templates->set_var ('optionlist_commentcode', $optionlist_commentcode);
	
	//trackbackcode
    $templates->set_var('lang_trackbackcode', $LANG_USERBOX_ADMIN['trackbackcode']);
    $templates->set_var ('trackbackcode', $trackbackcode);
    $optionlist_trackbackcode=COM_optionList ($_TABLES['trackbackcodes'], 'code,name',$trackbackcode);
    $templates->set_var ('optionlist_trackbackcode', $optionlist_trackbackcode);
	
    $templates->set_var('lang_cache_time', $LANG_USERBOX_ADMIN['cache_time']);
    $templates->set_var('lang_cache_time_desc', $LANG_USERBOX_ADMIN['cache_time_desc']);
    $templates->set_var ('cache_time', $cache_time);

    //comment_expire
    $templates->set_var('lang_enabled', $LANG_USERBOX_ADMIN['enabled']);

    if ($comment_expire_flag===0){
        $templates->set_var('show_comment_expire', 'false');
        $templates->set_var('is_checked_comment_expire', '');

    }else{
        $templates->set_var('show_comment_expire', 'true');
        $templates->set_var('is_checked_comment_expire', 'checked="checked"');
    }

    $templates->set_var('lang_comment_expire', $LANG_USERBOX_ADMIN['comment_expire']);
    $w=COM_convertDate2Timestamp(
        $comment_expire_year."-".$comment_expire_month."-".$comment_expire_day
        , $comment_expire_hour.":".$comment_expire_minute."::00"
        );
    $datetime_comment_expire=DATABOX_datetimeedit($w,"LANG_USERBOX_ADMIN","comment_expire");
	$templates->set_var('datetime_comment_expire', $datetime_comment_expire);

    //編集日
    $templates->set_var ('lang_modified_autoupdate', $LANG_USERBOX_ADMIN['modified_autoupdate']);
    $templates->set_var ('lang_modified', $LANG_USERBOX_ADMIN['modified']);
    $w=COM_convertDate2Timestamp(
        $modified_year."-".$modified_month."-".$modified_day
        , $modified_hour.":".$modified_minute."::00"
        );
    $datetime_modified=DATABOX_datetimeedit($w,"LANG_USERBOX_ADMIN","modified");
    $templates->set_var ('datetime_modified', $datetime_modified);
    //公開日
    $templates->set_var ('lang_released', $LANG_USERBOX_ADMIN['released']);
    $w=COM_convertDate2Timestamp(
        $released_year."-".$released_month."-".$released_day
        , $released_hour.":".$released_minute."::00"
        );
    $datetime_released=DATABOX_datetimeedit($w,"LANG_USERBOX_ADMIN","released");
    $templates->set_var ('datetime_released', $datetime_released);
    //公開終了日
    $templates->set_var ('lang_expired', $LANG_USERBOX_ADMIN['expired']);
    //if ($expired=="0000-00-00 00:00:00"){
    if ($expired_flag==0){
        $templates->set_var('show_expired', 'false');
        $templates->set_var('is_checked_expired', '');

    }else{
        $templates->set_var('show_expired', 'true');
        $templates->set_var('is_checked_expired', 'checked="expired"');
    }
    $templates->set_var('lang_expired', $LANG_USERBOX_ADMIN['expired']);
    $w=COM_convertDate2Timestamp(
        $expired_year."-".$expired_month."-".$expired_day
        , $expired_hour.":".$expired_minute."::00"
        );
    $datetime_expired=DATABOX_datetimeedit($w,"LANG_USERBOX_ADMIN","expired");
	$templates->set_var('datetime_expired', $datetime_expired);

    //順序
    $templates->set_var('lang_orderno', $LANG_USERBOX_ADMIN['orderno']);
    $templates->set_var ('orderno', $orderno);
//koko
    //カテゴリ
    $templates->set_var('lang_category', $LANG_USERBOX_ADMIN['category']);
    $checklist_category=DATABOX_getcategoriesinp ($category,$fieldset_id,$pi_name);
    $templates->set_var('checklist_category', $checklist_category);

    //追加項目
    $templates->set_var('lang_additionfields', $LANG_USERBOX_ADMIN['additionfields']);
	$rt=DATABOX_getaddtionfieldsEdit(
		$additionfields
		,$addition_def
		,$templates
		,9999
		,$pi_name
		,$additionfields_fnm
		,$additionfields_del
		,$fieldset_id
		,$additionfields_date
		);

    //保存日時
    $templates->set_var ('lang_udatetime', $LANG_USERBOX_ADMIN['udatetime']);
    $templates->set_var ('udatetime', $udatetime);
    $templates->set_var ('lang_uuid', $LANG_USERBOX_ADMIN['uuid']);
    $templates->set_var ('uuid', $uuid);
    //作成日付
    $templates->set_var ('lang_created', $LANG_USERBOX_ADMIN['created']);
    $templates->set_var ('created', $created);
    $templates->set_var ('created_un', $created_un);

    //アクセス権
    $templates->set_var('lang_accessrights',$LANG_ACCESS['accessrights']);
    $templates->set_var('lang_owner', $LANG_ACCESS['owner']);

    $owner_name = COM_getDisplayName($owner_id);
    $templates->set_var('owner_name', $owner_name);
    $templates->set_var('owner_id', $owner_id);
    $templates->set_var('lang_group', $LANG_ACCESS['group']);
    $templates->set_var('group_dropdown',SEC_getGroupDropdown ($group_id, 3));
    $templates->set_var('lang_permissions', $LANG_ACCESS['permissions']);
    $templates->set_var('lang_perm_key', $LANG_ACCESS['permissionskey']);
    $templates->set_var('permissions_editor'
                , SEC_getPermissionsHTML(
                         $perm_owner
                        ,$perm_group
                        ,$perm_members
                        ,$perm_anon));

    $templates->set_var('permissions_msg', $LANG_ACCESS['permmsg']);
    $templates->set_var('lang_permissions_msg', $LANG_ACCESS['permmsg']);


    // SAVE、CANCEL ボタン
    $templates->set_var('lang_save', $LANG_ADMIN['save']);
    $templates->set_var('lang_cancel', $LANG_ADMIN['cancel']);
    $templates->set_var('lang_preview', $LANG_ADMIN['preview']);

    //delete_option
    //$delflg=false;//@@@@@ 削除不可
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
function fnctemplatesdirectory (
    $defaulttemplatesdirectory
){

    global $_CONF;
    global $_TABLES;

    global $_USERBOX_CONF;

    //
    $selection = '<select id="defaulttemplatesdirectory" name="defaulttemplatesdirectory">' . LB;
	$selection .= "<option value=\"\">  </option>".LB;

	if ($_USERBOX_CONF['templates']==="theme"){
        $fd1=$_CONF['path_layout'].$_USERBOX_CONF['themespath']."profile/";
    }else if ($_USERBOX_CONF['templates']==="custom"){
        $fd1=$_CONF['path'] .'plugins/userbox/custom/templates/profile/';
    }else{
        $fd1=$_CONF['path'] .'plugins/userbox/templates/profile/';
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

// kokokara
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
)
{

    $pi_name="userbox";

    global $_CONF;
    global $_TABLES;
    global $_USER;

    global $_USERBOX_CONF;
    global $LANG_USERBOX_ADMIN;

    global $_FILES;

    $addition_def=DATABOX_getadditiondef($pi_name);



    $retval = '';

	// clean 'em up
    $id = COM_applyFilter($_POST['id'],true);
	$fieldset_id = COM_applyFilter ($_POST['fieldset'],true);

    //@@@@@ username fullname
    $username = COM_applyFilter($_POST['username']);
    $username = addslashes (COM_checkHTML (COM_checkWords ($username)));
	$fullname = COM_applyFilter($_POST['fullname']);
    $fullname = addslashes (COM_checkHTML (COM_checkWords ($fullname)));

    $page_title = COM_applyFilter($_POST['page_title']);
    $page_title = addslashes (COM_checkHTML (COM_checkWords ($page_title)));

    $description=$_POST['description'];//COM_applyFilter($_POST['description']);
    $description=addslashes (COM_checkHTML (COM_checkWords ($description)));

    $defaulttemplatesdirectory=COM_applyFilter($_POST['defaulttemplatesdirectory']);
    $defaulttemplatesdirectory=addslashes (COM_checkHTML (COM_checkWords ($defaulttemplatesdirectory)));

    $draft_flag = COM_applyFilter ($_POST['draft_flag'],true);

//            $hits =0;
//            $comments=0;

    $comment_expire_flag = COM_applyFilter ($_POST['comment_expire_flag'],true);
    IF ($comment_expire_flag){
        $comment_expire_month = COM_applyFilter ($_POST['comment_expire_month'],true);
        $comment_expire_day = COM_applyFilter ($_POST['comment_expire_day'],true);
        $comment_expire_year = COM_applyFilter ($_POST['comment_expire_year'],true);
        $comment_expire_hour = COM_applyFilter ($_POST['comment_expire_hour'],true);
        $comment_expire_minute = COM_applyFilter ($_POST['comment_expire_minute'],true);
		if ($comment_expire_ampm == 'pm') {
			if ($comment_expire_hour < 12) {
				$comment_expire_hour = $comment_expire_hour + 12;
			}
		}
		if ($comment_expire_ampm == 'am' AND $comment_expire_hour == 12) {
			$comment_expire_hour = '00';
		}
    }ELSE{
        $comment_expire_month = 0;
        $comment_expire_day = 0;
        $comment_expire_year = 0;
        $comment_expire_hour = 0;
        $comment_expire_minute = 0;
    }

    $commentcode = COM_applyFilter ($_POST['commentcode'],true);
    $trackbackcode = COM_applyFilter ($_POST['trackbackcode'],true);
    $cache_time = COM_applyFilter ($_POST['cache_time'],true);
	
	$meta_description = $_POST['meta_description'];
    $meta_description = addslashes (COM_checkHTML (COM_checkWords ($meta_description)));

    $meta_keywords = $_POST['meta_keywords'];
    $meta_keywords = addslashes (COM_checkHTML (COM_checkWords ($meta_keywords)));
	
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

	$dummy=DATABOX_cleanaddtiondatas (
		$additionfields
		,$addition_def
		,$additionfields_fnm
		,$additionfields_del
		,$additionfields_date
		,$additionfields_alt
		);
 
    //
    $owner_id = COM_applyFilter ($_POST['owner_id'],true);

    $group_id = COM_applyFilter ($_POST['group_id'],true);

    //
    $array['perm_owner']=$_POST['perm_owner'];
    $array['perm_group']=$_POST['perm_group'];
    $array['perm_members']=$_POST['perm_members'];
    $array['perm_anon']=$_POST['perm_anon'];

    if (is_array($array['perm_owner']) || is_array($array['perm_group']) ||
            is_array($array['perm_members']) ||
            is_array($array['perm_anon'])) {

        list($perm_owner, $perm_group, $perm_members, $perm_anon)
            = SEC_getPermissionValues($array['perm_owner'], $array['perm_group'], $array['perm_members'], $array['perm_anon']);

    } else {
        $perm_owner   = COM_applyBasicFilter($array['perm_owner'],true);
        $perm_group   = COM_applyBasicFilter($array['perm_group'],true);
        $perm_members = COM_applyBasicFilter($array['perm_members'],true);
        $perm_anon    = COM_applyBasicFilter($array['perm_anon'],true);
    }



    //編集日付
    $modified_autoupdate = COM_applyFilter ($_POST['modified_autoupdate'],true);
    IF ($modified_autoupdate==1){
        //$udate = date('Ymd');
        $modified_month = date('m');
        $modified_day = date('d');
        $modified_year = date('Y');
        $modified_hour = date('H');
        $modified_minute = date('i');
    }else{
        $modified_month = COM_applyFilter ($_POST['modified_month'],true);
        $modified_day = COM_applyFilter ($_POST['modified_day'],true);
        $modified_year = COM_applyFilter ($_POST['modified_year'],true);
        $modified_hour = COM_applyFilter ($_POST['modified_hour'],true);
        $modified_minute = COM_applyFilter ($_POST['modified_minute'],true);
		$modified_ampm=COM_applyFilter ($_POST['modified_ampm']);
		if ($modified_ampm == 'pm') {
			if ($modified_hour < 12) {
				$modified_hour = $modified_hour + 12;
			}
		}
		if ($modified_ampm == 'am' AND $modified_hour == 12) {
			$modified_hour = '00';
		}
    }
    //公開日
    $released_month = COM_applyFilter ($_POST['released_month'],true);
    $released_day = COM_applyFilter ($_POST['released_day'],true);
    $released_year = COM_applyFilter ($_POST['released_year'],true);
    $released_hour = COM_applyFilter ($_POST['released_hour'],true);
    $released_minute = COM_applyFilter ($_POST['released_minute'],true);
	if ($released_ampm == 'pm') {
		if ($released_hour < 12) {
			$released_hour = $released_hour + 12;
		}
	}
	if ($released_ampm == 'am' AND $released_hour == 12) {
		$released_hour = '00';
	}

    //公開終了日
    $expired_flag = COM_applyFilter ($_POST['expired_flag'],true);
    IF ($expired_flag){
        $expired_month = COM_applyFilter ($_POST['expired_month'],true);
        $expired_day = COM_applyFilter ($_POST['expired_day'],true);
        $expired_year = COM_applyFilter ($_POST['expired_year'],true);
        $expired_hour = COM_applyFilter ($_POST['expired_hour'],true);
        $expired_minute = COM_applyFilter ($_POST['expired_minute'],true);
		if ($expired_ampm == 'pm') {
			if ($expired_hour < 12) {
				$expired_hour = $expired_hour + 12;
			}
		}
		if ($expired_ampm == 'am' AND $expired_hour == 12) {
			$expired_hour = '00';
		}
    }ELSE{
        $expired_month = 0;
        $expired_day = 0;
        $expired_year = 0;
        $expired_hour = 0;
        $expired_minute = 0;
    }

	$created = COM_applyFilter ($_POST['created_un']);
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
    //id
    if ($id==0 ){
        //$err.=$LANG_USERBOX_ADMIN['err_uid']."<br {XHTML}>".LB;
    }else{
        if (!is_numeric($id) ){
            $err.=$LANG_USERBOX_ADMIN['err_id']."<br {XHTML}>".LB;
        }
    }
	//文字数制限チェック
	if (mb_strlen($description, 'UTF-8')>$_USERBOX_CONF['maxlength_description']) {
		$err.=$LANG_USERBOX_ADMIN['description']
				.$_USERBOX_CONF['maxlength_description']
				.$LANG_USERBOX_ADMIN['err_maxlength']."<br/>".LB;
	}
	if (mb_strlen($meta_description, 'UTF-8')>$_USERBOX_CONF['maxlength_meta_description']) {
		$err.=$LANG_USERBOX_ADMIN['meta_description']
				.$_USERBOX_CONF['maxlength_meta_description']
				.$LANG_USERBOX_ADMIN['err_maxlength']."<br/>".LB;
	}
	if (mb_strlen($meta_keywords, 'UTF-8')>$_USERBOX_CONF['maxlength_meta_keywords']) {
		$err.=$LANG_USERBOX_ADMIN['meta_keywords']
				.$_USERBOX_CONF['maxlength_meta_keywords']
				.$LANG_USERBOX_ADMIN['err_maxlength']."<br/>".LB;
	}

    //----追加項目チェック
    $err.=DATABOX_checkaddtiondatas
        ($additionfields,$addition_def,$pi_name,$additionfields_fnm
        ,$additionfields_del,$additionfields_alt);

    //編集日付
    $modified=$modified_year."-".$modified_month."-".$modified_day;
    if (checkdate($modified_month, $modified_day, $modified_year)==false) {
       $err.=$LANG_USERBOX_ADMIN['err_modified']."<br {XHTML}>".LB;
    }
    $modified=COM_convertDate2Timestamp(
        $modified_year."-".$modified_month."-".$modified_day
        , $modified_hour.":".$modified_minute."::00"
        );

    //公開日
    $released=$released_year."-".$released_month."-".$released_day;
    if (checkdate($released_month, $released_day, $released_year)==false) {
       $err.=$LANG_USERBOX_ADMIN['err_released']."<br {XHTML}>".LB;
    }
    $released=COM_convertDate2Timestamp(
        $released_year."-".$released_month."-".$released_day
        , $released_hour.":".$released_minute."::00"
        );


    //コメント受付終了日時
    IF ($comment_expire_flag){
        if (checkdate($comment_expire_month, $comment_expire_day, $comment_expire_year)==false) {

           $err.=$LANG_USERBOX_ADMIN['err_comment_expire']."<br {XHTML}>".LB;
        }
        $comment_expire=COM_convertDate2Timestamp(
            $comment_expire_year."-".$comment_expire_month."-".$comment_expire_day
            , $comment_expire_hour.":".$comment_expire_minute."::00"
            );

    }else{
        $comment_expire='0000-00-00 00:00:00';
        //$comment_expire="";

    }

    //公開終了日
    IF ($expired_flag){
        if (checkdate($expired_month, $expired_day, $expired_year)==false) {

           $err.=$LANG_USERBOX_ADMIN['err_expired']."<br {XHTML}>".LB;
        }
        $expired=COM_convertDate2Timestamp(
            $expired_year."-".$expired_month."-".$expired_day
            , $expired_hour.":".$expired_minute."::00"
            );
        if ($expired<$released) {
           $err.=$LANG_USERBOX_ADMIN['err_expired']."<br {XHTML}>".LB;
        }


    }else{
        $expired='0000-00-00 00:00:00';
        //$expired="";
    }

    //errorのあるとき
    if ($err<>"") {
        $retval['title']=$LANG_USERBOX_ADMIN['piname'].$LANG_USERBOX_ADMIN['edit'];
        $retval['display']= fncEdit($id, $edt_flg,3,$err);

        return $retval;

    }
    // CHECK　おわり

    if ($id==0){
        $w=DB_getItem($_TABLES['USERBOX_base'],"max(id)","1=1");
        if ($w=="") {
            $w=0;
        }
        $id=$w+1;
        $created_month = date('m');
        $created_day = date('d');
        $created_year = date('Y');
        $created_hour = date('H');
        $created_minute = date('i');
		$created=COM_convertDate2Timestamp(
			$created_year."-".$created_month."-".$created_day
			, $created_hour.":".$created_minute."::00"
			);
    }

    $hits=0;
    $comments=0;

    $fields="id";
    $values="$id";

    $fields.=",page_title";//
    $values.=",'$page_title'";


    $fields.=",description";//
    $values.=",'$description'";

    $fields.=",defaulttemplatesdirectory";//
    $values.=",'$defaulttemplatesdirectory'";

    //$fields.=",hits";//
    //$values.=",$hits";

    $fields.=",comments";//
    $values.=",$comments";

    $fields.=",meta_description";//
    $values.=",'$meta_description'";

    $fields.=",meta_keywords";//
    $values.=",'$meta_keywords'";

    $fields.=",commentcode";//
    $values.=",$commentcode";
	
    $fields.=",trackbackcode";//
    $values.=",$trackbackcode";
	
    $fields.=",cache_time";//
    $values.=",$cache_time";
	
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
	if  ($created<>""){
		$fields.=",created";
		$values.=",FROM_UNIXTIME('$created')";
	}

    $fields.=",expired";
	if ($expired=='0000-00-00 00:00:00'){
		$values.=",'$expired'";
	}else{
		$values.=",FROM_UNIXTIME('$expired')";
	}
 
    $fields.=",released";
    $values.=",FROM_UNIXTIME('$released')";

    $fields.=",orderno";//
    $values.=",$orderno";
	
	$fields.=",fieldset_id";//
    $values.=",$fieldset_id";

    $fields.=",uuid";
    $values.=",$uuid";

    $fields.=",draft_flag";
    $values.=",$draft_flag";

    DB_save($_TABLES['USERBOX_base'],$fields,$values);

    //カテゴリ
    $rt=DATABOX_savecategorydatas($id,$category,$pi_name);

	//追加項目
	DATABOX_uploadaddtiondatas	
        ($additionfields,$addition_def,$pi_name,$id,$additionfields_fnm,$additionfields_del
            ,$additionfields_old,$additionfields_alt);

    $rt=DATABOX_saveaddtiondatas($id,$additionfields,$addition_def,$pi_name);

    //user (コアのテーブル)
//kokoka
    $sql="UPDATE ".$_TABLES['users'] ." SET ";

    $sql.=" fullname ='".$fullname."'";

    $sql.=" WHERE uid=".$id ;
    DB_query($sql);


    $rt=fncsendmail ('data',$id);

    $cacheInstance = 'userbox__' . $id . '__' ;
    CACHE_remove_instance($cacheInstance); 

//exit;// debug 用

//    if ($edt_flg){
//        $return_page=$_CONF['site_url'] . "/".THIS_SCRIPT;
//        $return_page.="?id=".$id;
//    }else{
//        $return_page=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?msg=1';
//    }

//    return COM_refresh ($return_page);


    if ($_USERBOX_CONF['aftersave_admin']==='no'){
		$retval['title']=$LANG_USERBOX_ADMIN['piname'].$LANG_USERBOX_ADMIN['edit'];
        $retval['display'] .= fncEdit($id, $edt_flg,1,"");
        return $retval;
		
    }else if ($_USERBOX_CONF['aftersave_admin']==='list'){
            $url = $_CONF['site_admin_url'] . "/plugins/$pi_name/profile.php";
            $item_url=COM_buildURL($url);
            $target='item';

    }else{
        $url=$_CONF['site_url'] . "/userbox/profile.php";
        $url.="?";
        //コード使用の時
        if ($_USERBOX_CONF['datacode']){
            $url.="code=".$username;
            $url.="&amp;m=code";
        }else{
            $url.="id=".$id;
            $url.="&amp;m=id";
		}
        $item_url = COM_buildUrl( $url );
		$target=$_USERBOX_CONF['aftersave_admin'];
    }

    $return_page = PLG_afterSaveSwitch(
                    $target
                    ,$item_url
                    ,'userbox'
                    , 1);

	echo $return_page;

	exit;

}



//
// +---------------------------------------------------------------------------+
// | 機能  削除                                                                |
// | 書式 fncdelete ()                                                         |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:戻り画面＆メッセージ                                           |
// +---------------------------------------------------------------------------+
function fncdelete ()
{
    global $_CONF;
    global $_TABLES;
    global $LANG_DATABOX_ADMIN;

	$id = COM_applyFilter($_POST['id'],true);
    $username=DB_getItem($_TABLES['users'],"username","uid={$id}");
    $email=DB_getItem($_TABLES['users'],"email","uid={$id}");


    // CHECK
    $err="";
    if ($err<>"") {
		$retval['title']=$LANG_DATABOX_ADMIN['err'];
        $retval['display']= $err;

        return $retval;

    }

//    if (!USER_deleteAccount ($id)) {
//        $return_page=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?msg=3';
//    }else{
//        $return_page=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?msg=2';
//    }
    if (!USER_deleteAccount ($id)) {
        $msg=3;
    }else{
        $msg=2;
    }

    $rt=fncsendmail ('data_delete',$id,$username,$email);
	
    $cacheInstance = 'userbox__' . $id . '__' ;
    CACHE_remove_instance($cacheInstance); 

    //exit;// debug 用

	//return COM_refresh ($return_page);
	
    $retval['title']=$LANG_USERBOX_ADMIN['piname'];
    $retval['display']= COM_showMessage ($msg,'userbox');
    $retval['display'].= fncList();

    return $retval;


}

// +---------------------------------------------------------------------------+
// | 機能  DRAFT チェンジ更新                                                  |
// | 書式 fncchangeDraft ($seqno)                                              |
// +---------------------------------------------------------------------------+
// | 引数 $draft_flg :                                                         |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
function fncchangeDraft ($id)
{
    $pi_name="userbox";

    global $_TABLES;
    global $_USER;

    $id = COM_applyFilter($id,true);
    $uuid=$_USER['uid'];

    $sql="UPDATE {$_TABLES['USERBOX_base']} set ";
    if (DB_getItem($_TABLES['USERBOX_base'],"draft_flag", "id=$id")) {
        $sql.=" draft_flag = '0'";
    } else {
        $sql.=" draft_flag = '1'";
    }
    $sql.=",uuid='$uuid' WHERE id=$id";

	DB_query($sql);
	
    $cacheInstance = 'userbox__' . $id . '__' ;
    CACHE_remove_instance($cacheInstance); 
	
    return;

}
// +---------------------------------------------------------------------------+
// | 機能  DRAFT チェンジ更新                                                  |
// | 書式 fncchangeDraftAll ($flg)                                             |
// +---------------------------------------------------------------------------+
// | 引数 $draft_flg :                                                         |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
function fncchangeDraftAll ($flg)
{

    $pi_name="userbox";

    global $_TABLES;
    global $_USER;

    if ($flg==0) {
        $nflg=1;
    }else{
        $nflg=0;
    }
    $uuid=$_USER['uid'];

    $sql="UPDATE {$_TABLES['USERBOX_base']} set ";
    $sql.="draft_flag = '$flg'";
    $sql.=",uuid='$uuid' WHERE draft_flag='$nflg'";
    DB_query($sql);
    return;
}
function fnchitsclear ()
// +---------------------------------------------------------------------------+
// | 機能  hits clear
// | 書式 fnchitsclear()
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
{
    global $_TABLES;
    global $_USER;

    $uuid=$_USER['uid'];

    $sql="UPDATE {$_TABLES['USERBOX_stats']} set ";
    $sql.="hits = 0";
    $sql.=" WHERE hits<>0";
    DB_query($sql);
    return;
}

function fncexportexec ()
// +---------------------------------------------------------------------------+
// | 機能  エキスポート                                                        |
// | 書式 fncexport ()                                                         |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
{

$pi_name="userbox";

global $_CONF;
global $_TABLES;
global $LANG_USERBOX_ADMIN;

// 項目の見出リスト
$fld = array ();


$fld['id']['name'] = $LANG_USERBOX_ADMIN['id'];

$fld['page_title']['name'] = $LANG_USERBOX_ADMIN['page_title'];
$fld['description']['name'] = $LANG_USERBOX_ADMIN['description'];
$fld['comments']['name'] = $LANG_USERBOX_ADMIN['comments'];
$fld['meta_description']['name'] = $LANG_USERBOX_ADMIN['meta_description'];
$fld['meta_keywords']['name'] = $LANG_USERBOX_ADMIN['meta_keywords'];
$fld['commentcode']['name'] = $LANG_USERBOX_ADMIN['commentcode'];
$fld['comment_expire']['name'] = $LANG_USERBOX_ADMIN['comment_expire'];

// 準備中　$fld['language_id'] = $LANG_USERBOX_ADMIN['language_id'];
$fld['owner_id']['name'] = $LANG_USERBOX_ADMIN['owner_id'];
$fld['group_id']['name'] = $LANG_USERBOX_ADMIN['group_id'];
$fld['perm_owner']['name'] = $LANG_USERBOX_ADMIN['perm_owner'];
$fld['perm_group']['name'] = $LANG_USERBOX_ADMIN['perm_group'];
$fld['perm_members']['name'] = $LANG_USERBOX_ADMIN['perm_members'];
$fld['perm_anon']['name'] = $LANG_USERBOX_ADMIN['perm_anon'];

$fld['modified']['name'] = $LANG_USERBOX_ADMIN['modified'];
$fld['created']['name'] = $LANG_USERBOX_ADMIN['created'];
$fld['expired']['name'] = $LANG_USERBOX_ADMIN['expired'];
$fld['released']['name'] = $LANG_USERBOX_ADMIN['released'];

$fld['orderno']['name'] = $LANG_USERBOX_ADMIN['orderno'];
$fld['trackbackcode']['name'] = $LANG_USERBOX_ADMIN['trackbackcode'];
$fld['cache_time']['name'] = $LANG_USERBOX_ADMIN['cache_time'];

$fld['draft_flag']['name'] = $LANG_USERBOX_ADMIN['draft'];
$fld['udatetime']['name'] = $LANG_USERBOX_ADMIN['udatetime'];
$fld['uuid']['name'] = $LANG_USERBOX_ADMIN['uuid'];
//-----

//----------------------
$filenm=$pi_name."_data";
$tbl ="{$_TABLES['USERBOX_base']}";
$fieldset_id = COM_applyFilter ($_REQUEST['fieldset']);
if  ($fieldset_id=="all") {
	$where = "";
}else{
	$where = "fieldset_id =".$fieldset_id;
}		
$order = "id";
$addition=true;

$rt= DATABOX_dltbldt($filenm,$fld,$tbl,$where,$order,$pi_name,$addition);


return $rt;
}
// +---------------------------------------------------------------------------+
// | 機能  インポート画面表示                                                  |
// | 書式 fncimport ()                                                         |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
function fncimport ()
{
    $pi_name="userbox";

    global $_CONF;//, $LANG28;
    global $LANG_USERBOX_ADMIN;

    $tmpl = new Template ($_CONF['path'] . "plugins/".THIS_PLUGIN."/templates/admin/");
    $tmpl->set_file(array('import' => 'import.thtml'));

    $tmpl->set_var('site_admin_url', $_CONF['site_admin_url']);

    $tmpl->set_var('gltoken_name', CSRF_TOKEN);
    $tmpl->set_var('gltoken', SEC_createToken());
    $tmpl->set_var ( 'xhtml', XHTML );

    $tmpl->set_var('script', THIS_SCRIPT);

    $tmpl->set_var('importmsg', $LANG_USERBOX_ADMIN['importmsg']);
    $tmpl->set_var('importfile', $LANG_USERBOX_ADMIN['importfile']);
    $tmpl->set_var('submit', $LANG_USERBOX_ADMIN['submit']);

    $tmpl->parse ('output', 'import');
    $import = $tmpl->finish ($tmpl->get_var ('output'));

    $retval="";
    $retval .= $import;


    return $retval;
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
    ,$username=""
    ,$email=""
    )
{

    $pi_name="userbox";

    global $_CONF;
    global $_TABLES;
    global $LANG_USERBOX_MAIL;
    global $LANG_USERBOX_ADMIN;
    global $_USER ;
    global $_USERBOX_CONF ;
    global $LANG28;

    $retval = '';

    $site_name=$_CONF['site_name'];
    $subject= $LANG_USERBOX_MAIL['subject_'.$m];
    $message=$LANG_USERBOX_MAIL['message_'.$m];

    if ($m==="data_delete"){
        $msg.=$LANG28['2'].":".$id.LB;
        $msg.=$LANG28['3'].":".$title.LB;
        //URL
        $url=$_CONF['site_url'] . "/userbox/profile.php";
        $url = COM_buildUrl( $url );

    }else{
        $sql = "SELECT ";

        $sql .= " t1.*";
        $sql .= " ,t2.uid";
        $sql .= " ,t2.username";
        $sql .= " ,t2.fullname";
        $sql .= " ,t2.email";

        $sql .= " FROM ";
        $sql .= $_TABLES['USERBOX_base']." AS t1";
        $sql .= ",".$_TABLES['users']." AS t2";

        $sql .= " WHERE ";
        $sql .= " t1.id = $id";
        $sql .= " AND t1.id = t2.uid";

        $result = DB_query ($sql);
        $numrows = DB_numRows ($result);

        if ($numrows > 0) {

            $A = DB_fetchArray ($result);
			$A = array_map('stripslashes', $A);

            $email=$A['email'];
            //下書
            if ($A['draft_flag']==1) {
                $msg.=$LANG_USERBOX_ADMIN['draft'].LB;
            }

            //コア

            $msg.=$LANG28['2'].":".$A['uid'].LB;
            $msg.=$LANG28['3'].":".$A['username'].LB;
            $msg.=$LANG28['4'].":".$A['fullname'].LB;

            //基本項目
            $msg.= $LANG_USERBOX_ADMIN['page_title'].":".$A['page_title'].LB;
            $msg.= $LANG_USERBOX_ADMIN['description'].":".$A['description'].LB;

            $msg.= $LANG_USERBOX_ADMIN['hits'].":".$A['hits'].LB;
            $msg.= $LANG_USERBOX_ADMIN['comments'].":".$A['comments'].LB;
            $msg.= $LANG_USERBOX_ADMIN['meta_description'].":".$A['meta_description'].LB;
            $msg.= $LANG_USERBOX_ADMIN['meta_keywords'].":".$A['meta_keywords'].LB;
            $msg.= $LANG_USERBOX_ADMIN['commentcode'].":".$A['commentcode'].LB;
            $msg.= $LANG_USERBOX_ADMIN['comment_expire'].":".$A['comment_expire'].LB;

            // 準備中　$msg.=  $LANG_USERBOX_ADMIN['language_id'].":".$A['language_id'].LB;
            $msg.= $LANG_USERBOX_ADMIN['owner_id'].":".$A['owner_id'].LB;
            $msg.= $LANG_USERBOX_ADMIN['group_id'].":".$A['group_id'].LB;
            $msg.= $LANG_USERBOX_ADMIN['perm_owner'].":".$A['perm_owner'].LB;
            $msg.= $LANG_USERBOX_ADMIN['perm_group'].":".$A['perm_group'].LB;
            $msg.= $LANG_USERBOX_ADMIN['perm_members'].":".$A['perm_members'].LB;
            $msg.= $LANG_USERBOX_ADMIN['perm_anon'].":".$A['perm_anon'].LB;

            $msg.= $LANG_USERBOX_ADMIN['modified'].":".$A['modified'].LB;
            $msg.= $LANG_USERBOX_ADMIN['created'].":".$A['created'].LB;
            $msg.= $LANG_USERBOX_ADMIN['expired'].":".$A['expired'].LB;
            $msg.= $LANG_USERBOX_ADMIN['released'].":".$A['released'].LB;

            $msg.= $LANG_USERBOX_ADMIN['orderno'].":".$A['orderno'].LB;
            $msg.= $LANG_USERBOX_ADMIN['trackbackcode'].":".$A['trackbackcode'].LB;

            $msg.= $LANG_USERBOX_ADMIN['draft'].":".$A['draft'].LB;
            $msg.= $LANG_USERBOX_ADMIN['udatetime'].":".$A['udatetime'].LB;
            $msg.= $LANG_USERBOX_ADMIN['uuid'].":".$A['uuid'].LB;
//koko
            //カテゴリ
            $msg.=DATABOX_getcategoriesText($id ,0,$pi_name);

            //追加項目
            $group_id = stripslashes($A['group_id']);
            $owner_id = stripslashes($A['owner_id']);
            $chk_user=DATABOX_chkuser($group_id,$owner_id,"userbox.admin");
            $addition_def=DATABOX_getadditiondef($pi_name);
            $additionfields = DATABOX_getadditiondatas($id,$pi_name);
            $msg.=DATABOX_getaddtionfieldsText($additionfields,$addition_def,$chk_user,$pi_name,$A['fieldset_id']);

            //タイムスタンプ　更新ユーザ
            $msg.= $LANG_USERBOX_ADMIN['udatetime'].":".$A['udatetime'].LB;
            $msg.= $LANG_USERBOX_ADMIN['uuid'].":".$A['uuid'].LB;


            //URL
            $url=$_CONF['site_url'] . "/userbox/profile.php";
            $url.="?";
            if ($_USERBOX_CONF['datacode']){
                $url.="m=code";
                $url.="&code=".$A['username'];
            }else{
                $url.="m=id";
                $url.="&id=".$A['id'];
            }
            $url = COM_buildUrl( $url );

        }
    }
	if  (($_USERBOX_CONF['mail_to_draft']==0) AND ($A['draft_flag']==1)){
	}else{
		$message.=$msg.LB;
		$message.=$url.LB;
		$message.=$LANG_USERBOX_MAIL['sig'].LB;

		$mail_to=$_USERBOX_CONF['mail_to'];
		//--- to user
		if  ($_USERBOX_CONF['mail_to_owner']==1){
			if (array_search($email,$mail_to)===false){
				$to=$email;
				COM_mail ($to, $subject, $message);
			}
		}
		//--- to admin
		if (!empty ($mail_to)){
			$to=implode($mail_to,",");
			COM_mail ($to, $subject, $message);
		}
	}
	return $retval;
}
function fncChangeSet (
){
	global $_CONF;
	global $LANG_USERBOX_ADMIN;
	global $LANG_ADMIN;
	global $_TABLES;
	
	$pi_name="userbox";
	
    $retval = '';
	
	$id = COM_applyFilter ($_REQUEST['id'], true);
	//-----
	if  ($id==0){
		$actionname=$LANG_USERBOX_ADMIN['registset'];
	}else{
		$actionname=$LANG_USERBOX_ADMIN["changeset"];
	}
	
    $tmplfld=DATABOX_templatePath('admin','default',$pi_name);
    $templates = new Template($tmplfld);
    $templates->set_file('editor',"changeset.thtml");
	
    $templates->set_var('site_url', $_CONF['site_url']);
    $templates->set_var('site_admin_url', $_CONF['site_admin_url']);
	
    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);
    $templates->set_var('gltoken_name', CSRF_TOKEN);
    $templates->set_var('gltoken', $token);
    $templates->set_var ( 'xhtml', XHTML );

    $templates->set_var('script', THIS_SCRIPT);
	
    $templates->set_var('actionname', $actionname);
	$templates->set_var('id', $id);
	if  ($id==0){
		$inst=$LANG_USERBOX_ADMIN['inst_changeset0'];
		$templates->set_var ('lang_changeset', $LANG_USERBOX_ADMIN['registset']);
	}else{
		$inst=DB_getItem($_TABLES['users'],"username","uid=".$id);//@@@@@@
		$inst.=$LANG_USERBOX_ADMIN['inst_changesetx'];
		$templates->set_var ('lang_changeset', $LANG_USERBOX_ADMIN['changeset']);
	}
	$inst.=$LANG_USERBOX_ADMIN['inst_changeset'];
	$templates->set_var ('lang_inst_changeset', $inst);
	
	//fieldset_id
	$fieldset_id=0;
	$templates->set_var('lang_fieldset', $LANG_USERBOX_ADMIN['fieldset']);
	$list_fieldset=DATABOX_getoptionlist("fieldset",$fieldset_id,0,$pi_name,"",0 );
	$templates->set_var ('list_fieldset', $list_fieldset);
	
    $templates->set_var('lang_cancel', $LANG_ADMIN['cancel']);

	$templates->parse('output', 'editor');
    $retval .= $templates->finish($templates->get_var('output'));
	
	return $retval;
}

function fncChangeSetExec (
)
{
    global $_TABLES;
    global $_USER;

	
	$fieldset_id = COM_applyFilter ($_REQUEST['fieldset'], true);
	if  ($fieldset_id==0) {
		return;
	}
	$id = COM_applyFilter ($_REQUEST['id'], true);
    $uuid=$_USER['uid'];
	
	$sql="SELECT id FROM {$_TABLES['USERBOX_base']}  ";
	$sql .=" WHERE ";
	if  ($id==0){
		$sql .="  fieldset_id=0";
	}else{
		$sql .="  id=".$id;
	}	
	
	$result = DB_query ($sql);

    $i=0;
    while( $A = DB_fetchArray( $result ) )    {
		$A = array_map('stripslashes', $A);
		
		$sql="UPDATE {$_TABLES['USERBOX_base']} set ";
		$sql.="fieldset_id = '$fieldset_id'";
		$sql.=",uuid='$uuid' WHERE id =".$A['id'] ;
		
		DB_query($sql);
		
        $i++;
    }


    return;
}
function fncexportform (
)
// +---------------------------------------------------------------------------+
// | 機能 エキスポート画面表示
// | 書式 fncexportform()
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
{
    global $_CONF;

	global $_USERBOX_CONF;
	global $LANG_USERBOX_ADMIN;
	global $LANG_ADMIN;
	
	$pi_name="userbox";
	
	//-----
	$tmpl = new Template ($_CONF['path'] . "plugins/".THIS_PLUGIN."/templates/admin/");
    $tmpl->set_file(array('exportform' => 'exportform.thtml'));

    $tmpl->set_var('site_admin_url', $_CONF['site_admin_url']);

    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);
    $tmpl->set_var('gltoken_name', CSRF_TOKEN);
    $tmpl->set_var('gltoken', $token);
    $tmpl->set_var ( 'xhtml', XHTML );
 
    $tmpl->set_var('script', THIS_SCRIPT);
	
    $tmpl->set_var('actionname', $LANG_USERBOX_ADMIN['export']);
    $tmpl->set_var('lang_inst', $LANG_USERBOX_ADMIN['inst_dataexport']);
	
	//fieldset_id
	$fieldset_id="all";
	$tmpl->set_var('lang_fieldset', $LANG_USERBOX_ADMIN['fieldset']);
	$list_fieldset=DATABOX_getoptionlist("fieldset",$fieldset_id,0,$pi_name,"","all" );
	$tmpl->set_var ('list_fieldset', $list_fieldset);
	
    $tmpl->set_var('lang_export', $LANG_USERBOX_ADMIN["export"]);
    $tmpl->set_var('lang_cancel', $LANG_ADMIN['cancel']);
	
	
    $tmpl->parse ('output', 'exportform');
    $exportform = $tmpl->finish ($tmpl->get_var ('output'));
    $retval .= $exportform;
	
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
    global $LANG_ADMIN;

    global $LANG_USERBOX_ADMIN;

    global $LANG_USERBOX;

    $retval = '';
	//MENU1:管理画面
    $url7=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=changeset';
    $url2=$_CONF['site_url'] . '/userbox/list.php';
    $url3=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=drafton';
    $url4=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=draftoff';
	$url8=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=hitsclear';
    $url5=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=exportform';
    $url6=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=import';

    $menu_arr[]=array('url' => $url7,'text' => $LANG_USERBOX_ADMIN["registset"]);
    $menu_arr[]=array('url' => $url2,'text' => $LANG_USERBOX['list']);
    $menu_arr[]=array('url' => $url3,'text' => $LANG_USERBOX_ADMIN['drafton']);
    $menu_arr[]=array('url' => $url4,'text' => $LANG_USERBOX_ADMIN['draftoff']);
    $menu_arr[]=array('url' => $url8,'text' => $LANG_USERBOX_ADMIN['hitsclear']);
    $menu_arr[]=array('url' => $url5,'text' => $LANG_USERBOX_ADMIN['export']);
    $menu_arr[]=array('url' => $_CONF['site_admin_url'],'text' => $LANG_ADMIN['admin_home']);

    $retval .= ADMIN_createMenu(
        $menu_arr,
        $LANG_USERBOX_ADMIN['instructions'],
        plugin_geticon_userbox()
    );

    return $retval;
}

// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
//############################
$pi_name    = 'userbox';
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
    $mode="save";
}else if (($mode == $LANG_ADMIN['delete']) && !empty ($LANG_ADMIN['delete'])) {
    $mode="delete";
}else if (($mode == $LANG_USERBOX_ADMIN['changeset']) && !empty ($LANG_USERBOX_ADMIN['changeset'])) {
    $mode="changesetexec";
}else if (($mode == $LANG_USERBOX_ADMIN['registset']) && !empty ($LANG_USERBOX_ADMIN['registset'])) {
    $mode="changesetexec";
}else if (($mode == $LANG_USERBOX_ADMIN['export']) && !empty ($LANG_USERBOX_ADMIN['export'])) {
    $mode="exportexec";
}

if (isset ($_POST['draftChange'])) {
    $mode='draftChange';
}
if ($action == $LANG_ADMIN['cancel'])  { // cancel
    $mode="";
}

//echo "mode=".$mode."<br>";
if ($mode=="" 
	OR $mode=="edit" 
	OR $mode=="new" 
	OR $mode=="drafton" 
	OR $mode=="draftoff"
	OR $mode=="hitsclear"
	OR $mode=="exportform" 
	OR $mode=="exportexec" 
	OR $mode=="import"  
	OR $mode=="copy"
	OR $mode=="changeset"
	) {
}else{
    if (!SEC_checkToken()){
 //    if (SEC_checkToken()){//テスト用
        COM_accessLog("User {$_USER['username']} tried to illegally and failed CSRF checks.");
        echo COM_refresh($_CONF['site_admin_url'] . '/index.php');
        exit;
    }
}

//DRAFT ON OFF
if (isset ($_POST['draftChange'])) {
    fncchangeDraft ($_POST['draftChange']);
}

//DRAFT 一括ON OFF
if ($mode=="draftonexec") {
    fncchangeDraftAll (1);
}
if ($mode=="draftoffexec") {
    fncchangeDraftAll (0);
}

if ($mode=="hitsclearexec") {
    fnchitsclear ();
}

if ($mode=="changesetexec") {
	fncChangeSetExec ();
}
//
$menuno=2;
$display="";
$information = array();

switch ($mode) {
    case 'drafton'://drafton Confirmation
    case 'draftoff'://DATA clear　Confirmation
    case 'hitsclear'://DATA clear　Confirmation
        $information['pagetitle']=$LANG_USERBOX_ADMIN['piname'];
        $display .= DATABOX_Confirmation($pi_name,$mode);
        break;

    case 'exportform':// エクスポート　画面
        $information['pagetitle']=$LANG_USERBOX_ADMIN['piname'].$LANG_USERBOX_ADMIN['export'];
        $display .= fncexportform();
        break;
    case 'exportexec':// エキスポート実行
		$display=fncexportexec ();
		if  ($display=="") {
			exit;
		}
        break;
	case 'changeset':// 属性セット変更
        $information['pagetitle']=$LANG_USERBOX_ADMIN['piname'].$LANG_USERBOX_ADMIN['new'];
        $display .= fncChangeSet();
        break;

    case 'save':// 保存
        $retval= fncSave ($edt_flg,$navbarMenu,$menuno);
        $information['pagetitle']=$retval['title'];
		$display.=$retval['display'];
		break;
    case 'delete':// 削除
        $retval= fncdelete();
        $information['pagetitle']=$retval['title'];
		$display.=$retval['display'];
        break;
    case 'edit':// 編集
        if (!empty ($id) ) {
            $information['pagetitle']=$LANG_USERBOX_ADMIN['piname'].$LANG_USERBOX_ADMIN['edit'];
            $display .= fncEdit($id, $edt_flg,$msg,"",$mode);
        }
        break;

    case 'import':// インポート
        $information['pagetitle']=$LANG_USERBOX_ADMIN['piname'].$LANG_USERBOX_ADMIN['import'];
        $display .= fncimport();
        break;

    default:// 初期表示、一覧表示
        $information['pagetitle']=$LANG_USERBOX_ADMIN['piname'];
        if (isset ($msg)) {
            $display .= COM_showMessage ($msg,$pi_name);
        }
        $display .= fncList();
}
$display =COM_startBlock($LANG_USERBOX_ADMIN['piname'],''
            ,COM_getBlockTemplate('_admin_block', 'header'))
         .ppNavbarjp($navbarMenu,$LANG_USERBOX_admin_menu[$menuno])
         .fncMenu($pi_name)
         .$display
         .COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));
$display=DATABOX_displaypage($pi_name,'_admin',$display,$information);
COM_output($display);


?>
