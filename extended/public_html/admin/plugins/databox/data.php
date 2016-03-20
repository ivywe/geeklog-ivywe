<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | data maintenannce
// +---------------------------------------------------------------------------+
// $Id: data.php
// public_html/admin/plugins/databox/data.php
// 2010818 tsuchitani AT ivywe DOT co DOT jp
// 20101207

// @@@@@追加予定：import
// @@@@@追加予定：export に category
// @@@@@追加予定：日付入力


define ('THIS_SCRIPT', 'databox/data.php');
//define ('THIS_SCRIPT', 'databox/test.php');

require_once('databox_functions.php');
require_once( $_CONF['path_system'] . 'lib-admin.php' );


function fncList()
// +---------------------------------------------------------------------------+
// | 機能  一覧表示                                                            |
// | 書式 fncList()                                                            |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:一覧                                                           |
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $_TABLES;
    global $LANG_ADMIN;
    global $LANG09;
    global $LANG_DATABOX_ADMIN;
    global $LANG_DATABOX;
    global $_DATABOX_CONF;

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
    $header_arr[]=array('text' => $LANG_ADMIN['copy'], 'field' => 'copy', 'sort' => false);
    $header_arr[]=array('text' => $LANG_DATABOX_ADMIN['id'], 'field' => 'id', 'sort' => true);
    $header_arr[]=array('text' => $LANG_DATABOX_ADMIN['code'], 'field' => 'code', 'sort' => true);
    $header_arr[]=array('text' => $LANG_DATABOX_ADMIN['title'], 'field' => 'title', 'sort' => true);
    $header_arr[]=array('text' => $LANG_DATABOX_ADMIN['fieldset'], 'field' => 'fieldset_name', 'sort' => true);
    $header_arr[]=array('text' => $LANG_DATABOX_ADMIN['remaingdays'], 'field' => 'remaingdays', 'sort' => true);
    $header_arr[]=array('text' => $LANG_DATABOX_ADMIN['hits'], 'field' => 'hits', 'sort' => true);
	$header_arr[]=array('text' => $LANG_DATABOX_ADMIN['udatetime'], 'field' => 'udatetime', 'sort' => true);
    $header_arr[]=array('text' => $LANG_DATABOX_ADMIN['draft'], 'field' => 'draft_flag', 'sort' => true);
    //
    $text_arr = array('has_menu' =>  true,
      'has_extras'   => true,
      'form_url' => $_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT);

    //Query
    $sql = "SELECT ";
    $sql .= " t.id";
    $sql .= " ,title";
    $sql .= " ,code";
    $sql .= " ,draft_flag";
    $sql .= " ,modified";
    $sql .= " ,UNIX_TIMESTAMP(t.udatetime) AS udatetime";
    $sql .= " ,orderno";
    $sql .= " ,t2.name AS fieldset_name";
    $sql .= " ,t.fieldset_id";
    $sql .= " ,t5.hits";
	
	$sql .= " ,(SELECT DATEDIFF(expired , NOW()) ";
	$sql .= " FROM {$_TABLES['DATABOX_base']} AS t3  ";
	$sql .= " where   t.id=t3.id AND DATEDIFF(expired , NOW())>0)";
    $sql .= "	+ 1 AS remaingdays";
	
    $sql .= " FROM ";
    $sql .= " {$_TABLES['DATABOX_base']} AS t";
	$sql .= " JOIN {$_TABLES['DATABOX_def_fieldset']} AS t2       ON t.fieldset_id=t2.fieldset_id";
	$sql .= " LEFT JOIN {$_TABLES['DATABOX_stats']} AS t5  ON t.id=t5.id";
	$sql .= " WHERE 1=1";	
	
    $query_arr = array(
        'table' => 'DATABOX_base',
        'sql' => $sql,
        'query_fields' => array('t.id','title','code','draft_flag','orderno','t2.name','hits'),
        'default_filter' => $exclude);
    //デフォルトソート項目:
	if  ($_DATABOX_CONF["sort_list_by"]=="udatetime"){
		$defsort_arr = array('field' => 'udatetime', 'direction' => 'DESC');
	}else{
		$defsort_arr = array('field' => $_DATABOX_CONF["sort_list_by"], 'direction' => 'ASC');
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

function fncGetListField($fieldname, $fieldvalue, $A, $icon_arr)
// +---------------------------------------------------------------------------+
// | 一覧取得 ADMIN_list で使用
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $LANG_ACCESS;
    global $_DATABOX_CONF;

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
        case 'copy':
            $url=$_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT;
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

function fncEdit(
    $id
    ,$edt_flg
    ,$msg = ''
    ,$errmsg=""
    ,$mode="edit"
    ,$old_mode=""
)
// +---------------------------------------------------------------------------+
// | 機能  編集画面表示                                                        |
// | 書式 fncEdit($id , $edt_flg,$msg,$errmsg)                                 |
// +---------------------------------------------------------------------------+
// | 引数 $id:                                                                 |
// | 引数 $edt_flg:                                                            |
// | 引数 $msg:メッセージ番号                                                  |
// | 引数 $errmsg
// | 引数 $mode:
// +---------------------------------------------------------------------------+
// | 戻値 nomal:編集画面                                                       |
// +---------------------------------------------------------------------------+
// update 20100927-1020 defaulttemplatesdirectory add
// update 20110826- eyechatchingimage add ??? 20130701delete
// update 20120516- fieldset add
{

    $pi_name="databox";

    global $_CONF;
    global $_TABLES;
    global $LANG_ADMIN;
    global $MESSAGE;
    global $LANG_ACCESS;
    global $_USER;
	global $_SCRIPTS;
	
    global $_DATABOX_CONF;
    global $LANG_DATABOX_ADMIN;
    global $LANG_DATABOX;

    $retval = '';

    $delflg=false;

    $addition_def=DATABOX_getadditiondef();
    //メッセージ表示
    if (!empty ($msg)) {
        $retval .= COM_showMessage ($msg,'databox');
        $retval .= $errmsg;

        // clean 'em up
        $code=COM_applyFilter($_POST['code']);
        $title = COM_stripslashes($_POST['title']);
        $page_title = COM_applyFilter($_POST['page_title']);
        $description=$_POST['description'];//COM_applyFilter($_POST['description']);
		$defaulttemplatesdirectory = COM_applyFilter($_POST['defaulttemplatesdirectory']);
		
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
		$additionfields=DATABOX_cleanaddtiondatas (
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
        if (empty($id)) {
			$fieldset_id=COM_applyFilter ($_POST['fieldset'],true);//"";
			$fieldset_name=DB_getItem($_TABLES['DATABOX_def_fieldset'],"name","fieldset_id=".$fieldset_id);
			$fieldset_name=COM_stripslashes($fieldset_name);
			
            $id=0;

            $code ="";
            $title ="";
            $description="";
            $defaulttemplatesdirectory=null;
			
            $hits =0;
            $comments=0;

            $comment_expire_flag = 0;
            $w = mktime(0, 0, 0, date('m'),
                 date('d') + $_CONF['article_comment_close_days'], date('Y'));
            $comment_expire_year=date('Y', $w);
            $comment_expire_month=date('m', $w);
            $comment_expire_day=date('d', $w);
            $comment_expire_hour=0;
            $comment_expire_minute=0;

            $commentcode =$_DATABOX_CONF['commentcode'];
            $trackbackcode =$_CONF[trackback_code];
            $cache_time =$_DATABOX_CONF[default_cache_time];

            $meta_description ="";
            $meta_keywords ="";
			$language_id="";
			
            $category = "";
            $additionfields=array();
            $additionfields_fnm=array();//@@@@@
			$additionfields_del=array();
			$additionfields_date="";
            $additionfields = DATABOX_getadditiondatas(0,$pi_name);

            //
            $owner_id =$_USER['uid'];//@@@@@

            //$group_id =SEC_getFeatureGroup('databox.admin', $_USER['uid']);;
            $group_id =$_DATABOX_CONF['grp_id_default'];

            $array = array();
            SEC_setDefaultPermissions($array, $_DATABOX_CONF['default_permissions']);
			$perm_owner = $array['perm_owner'];
            $perm_group = $array['perm_group'];
            $perm_anon = $array['perm_anon'];
            $perm_members = $array['perm_members'];
			//
            $draft_flag=$_DATABOX_CONF['admin_draft_default'];
            //編集日付
            $modified_month = date('m');
            $modified_day = date('d');
            $modified_year = date('Y');
            $modified_hour = date('H');
            $modified_minute = date('i');
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

            $orderno ="";

            $uuid=0;
            $udatetime="";//"";

        }else{
            $sql = "SELECT ".LB;

			$sql .= " t.*".LB;
			$sql .= " ,t2.name AS fieldset_name".LB;
			
			$sql .= " ,UNIX_TIMESTAMP(t.modified) AS modified_un".LB;
			$sql .= " ,UNIX_TIMESTAMP(t.released) AS released_un".LB;
			$sql .= " ,UNIX_TIMESTAMP(t.comment_expire) AS comment_expire_un".LB;
			$sql .= " ,UNIX_TIMESTAMP(t.expired) AS expired_un".LB;
			$sql .= " ,UNIX_TIMESTAMP(t.udatetime) AS udatetime_un".LB;
			$sql .= " ,UNIX_TIMESTAMP(t.created) AS created_un".LB;
			
            $sql .= " FROM ".LB;
			$sql .= $_TABLES['DATABOX_base'] ." AS t ".LB;
			$sql .= ",".$_TABLES['DATABOX_def_fieldset'] ." AS t2 ".LB;
			
            $sql .= " WHERE ".LB;
			$sql .= " id = $id".LB;
			$sql .= " AND t.fieldset_id = t2.fieldset_id".LB;
			
            $result = DB_query($sql);

			$A = DB_fetchArray($result);
			
            $fieldset_id = COM_stripslashes($A['fieldset_id']);
            $fieldset_name = COM_stripslashes($A['fieldset_name']);

            $code = COM_stripslashes($A['code']);
            $title=COM_stripslashes($A['title']);
            $page_title=COM_stripslashes($A['page_title']);
            $description=COM_stripslashes($A['description']);
            $defaulttemplatesdirectory=COM_stripslashes($A['defaulttemplatesdirectory']);
			$eyechatchiimage=COM_stripslashes($A['eyechatchimage']);

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
            $category = databox_getdatas("category_id",$_TABLES['DATABOX_category'],"id = $id");

            //@@@@@
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
    }
    if ($mode==="copy"){
        $id=0;
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
        //コメント停止日時
        $comment_expire_flag = 0;
        $w = mktime(0, 0, 0, date('m'),
             date('d') + $_CONF['article_comment_close_days'], date('Y'));
        $comment_expire_year=date('Y', $w);
        $comment_expire_month=date('m', $w);
        $comment_expire_day=date('d', $w);
        $comment_expire_hour=0;
        $comment_expire_minute=0;
        //
        $delflg=false;
        $old_mode="copy";
    }

    //template フォルダ
    $tmplfld=DATABOX_templatePath('admin','default',$pi_name);
    $templates = new Template($tmplfld);

    $templates->set_file (array (
                'editor' => 'data_editor.thtml',
                'row'   => 'row.thtml',
                'col'   => "data_col_detail.thtml",
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
    if (($_CONF['meta_tags'] > 0) && ($_DATABOX_CONF['meta_tags'] > 0)) {
        $templates->set_var('hide_meta', '');
    } else {
        $templates->set_var('hide_meta', ' style="display:none;"');
    }
	
    $templates->set_var('maxlength_description', $_DATABOX_CONF['maxlength_description']);
    $templates->set_var('maxlength_meta_description', $_DATABOX_CONF['maxlength_meta_description']);
    $templates->set_var('maxlength_meta_keywords', $_DATABOX_CONF['maxlength_meta_keywords']);
	
    $templates->set_var('about_thispage', $LANG_DATABOX_ADMIN['about_admin_data']);
    $templates->set_var('lang_must', $LANG_DATABOX_ADMIN['must']);

    $templates->set_var('site_url', $_CONF['site_url']);
    $templates->set_var('site_admin_url', $_CONF['site_admin_url']);
	
	$templates->set_var('lang_ref', $LANG_DATABOX_ADMIN['ref']);
	$templates->set_var('lang_view', $LANG_DATABOX_ADMIN['view']);

    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);
    $templates->set_var('gltoken_name', CSRF_TOKEN);
    $templates->set_var('gltoken', $token);
    $templates->set_var ( 'xhtml', XHTML );

    $templates->set_var('script', THIS_SCRIPT);

    $templates->set_var('dateformat', $_DATABOX_CONF['dateformat']);

    //ビューリンク@@@@@
    $url=$_CONF['site_url'] . "/databox/data.php";
    $url.="?";
    if ($_DATABOX_CONF['datacode']){
        $url.="code=".$A['code'];
        $url.="&amp;m=code";
    }else{
        $url.="id=".$A['id'];
        $url.="&amp;m=id";
    }
    $url = COM_buildUrl( $url );
    $view= COM_createLink($LANG_DATABOX['view'], $url);
    $templates->set_var('view', $view);

//
    $templates->set_var('lang_link_admin', $LANG_DATABOX_ADMIN['link_admin']);
    $templates->set_var('lang_link_admin_top', $LANG_DATABOX_ADMIN['link_admin_top']);
    $templates->set_var('lang_link_public', $LANG_DATABOX_ADMIN['link_public']);
    $templates->set_var('lang_link_list', $LANG_DATABOX_ADMIN['link_list']);
    $templates->set_var('lang_link_detail', $LANG_DATABOX_ADMIN['link_detail']);
	//fieldset_id
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
    }else{
        $templates->set_var('draft_flag', "");
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
    $templates->set_var('lang_defaulttemplatesdirectory', $LANG_DATABOX_ADMIN['defaulttemplatesdirectory']);
	$templates->set_var ('defaulttemplatesdirectory', $defaulttemplatesdirectory);
	$select_defaulttemplatesdirectory=fnctemplatesdirectory($defaulttemplatesdirectory);//@@@@@
    $templates->set_var ('select_defaulttemplatesdirectory', $select_defaulttemplatesdirectory);//@@@@@
	
    //meta_description
    $templates->set_var('lang_meta_description', $LANG_DATABOX_ADMIN['meta_description']);
    $templates->set_var ('meta_description', $meta_description);

    //meta_keywords
    $templates->set_var('lang_meta_keywords', $LANG_DATABOX_ADMIN['meta_keywords']);
    $templates->set_var ('meta_keywords', $meta_keywords);
	
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
	
	
    //hits
    $templates->set_var('lang_hits', $LANG_DATABOX_ADMIN['hits']);
    $templates->set_var ('hits', $hits);

    //comments
    $templates->set_var('lang_comments', $LANG_DATABOX_ADMIN['comments']);
    $templates->set_var ('comments', $comments);

    //commentcode
    $templates->set_var('lang_commentcode', $LANG_DATABOX_ADMIN['commentcode']);
    $templates->set_var ('commentcode', $commentcode);
    $optionlist_commentcode=COM_optionList ($_TABLES['commentcodes'], 'code,name',$commentcode);
    $templates->set_var ('optionlist_commentcode', $optionlist_commentcode);
	
	//trackbackcode
    $templates->set_var('lang_trackbackcode', $LANG_DATABOX_ADMIN['trackbackcode']);
    $templates->set_var ('trackbackcode', $trackbackcode);
    $optionlist_trackbackcode=COM_optionList ($_TABLES['trackbackcodes'], 'code,name',$trackbackcode);
    $templates->set_var ('optionlist_trackbackcode', $optionlist_trackbackcode);
    
    $templates->set_var('lang_cache_time', $LANG_DATABOX_ADMIN['cache_time']);
    $templates->set_var('lang_cache_time_desc', $LANG_DATABOX_ADMIN['cache_time_desc']);
    $templates->set_var ('cache_time', $cache_time);

    //comment_expire
    $templates->set_var('lang_enabled', $LANG_DATABOX_ADMIN['enabled']);

    if ($comment_expire_flag===0){
        $templates->set_var('show_comment_expire', 'false');
        $templates->set_var('is_checked_comment_expire', '');

    }else{
        $templates->set_var('show_comment_expire', 'true');
        $templates->set_var('is_checked_comment_expire', 'checked="checked"');
    }

    $templates->set_var('lang_comment_expire', $LANG_DATABOX_ADMIN['comment_expire']);
    $w=COM_convertDate2Timestamp(
        $comment_expire_year."-".$comment_expire_month."-".$comment_expire_day
        , $comment_expire_hour.":".$comment_expire_minute."::00"
        );
    $datetime_comment_expire=DATABOX_datetimeedit($w,"LANG_DATABOX_ADMIN","comment_expire");
    $templates->set_var('datetime_comment_expire', $datetime_comment_expire);

    //編集日
    $templates->set_var ('lang_modified_autoupdate', $LANG_DATABOX_ADMIN['modified_autoupdate']);
    $templates->set_var ('lang_modified', $LANG_DATABOX_ADMIN['modified']);
    $w=COM_convertDate2Timestamp(
        $modified_year."-".$modified_month."-".$modified_day
        , $modified_hour.":".$modified_minute."::00"
        );
    $datetime_modified=DATABOX_datetimeedit($w,"LANG_DATABOX_ADMIN","modified");
    $templates->set_var ('datetime_modified', $datetime_modified);
    //公開日
    $templates->set_var ('lang_released', $LANG_DATABOX_ADMIN['released']);
    $w=COM_convertDate2Timestamp(
        $released_year."-".$released_month."-".$released_day
        , $released_hour.":".$released_minute."::00"
        );
    $datetime_released=DATABOX_datetimeedit($w,"LANG_DATABOX_ADMIN","released");
    $templates->set_var ('datetime_released', $datetime_released);
    //公開終了日
    $templates->set_var ('lang_expired', $LANG_DATABOX_ADMIN['expired']);
    //if ($expired=="0000-00-00 00:00:00"){
    if ($expired_flag==0){
        $templates->set_var('show_expired', 'false');
        $templates->set_var('is_checked_expired', '');

    }else{
        $templates->set_var('show_expired', 'true');
        $templates->set_var('is_checked_expired', 'checked="expired"');
    }
    $templates->set_var('lang_expired', $LANG_DATABOX_ADMIN['expired']);
    $w=COM_convertDate2Timestamp(
        $expired_year."-".$expired_month."-".$expired_day
        , $expired_hour.":".$expired_minute."::00"
        );
    $datetime_expired=DATABOX_datetimeedit($w,"LANG_DATABOX_ADMIN","expired");
	$templates->set_var('datetime_expired', $datetime_expired);
	
    //順序
    $templates->set_var('lang_orderno', $LANG_DATABOX_ADMIN['orderno']);
    $templates->set_var ('orderno', $orderno);

    //カテゴリ
    $templates->set_var('lang_category', $LANG_DATABOX_ADMIN['category']);
    $checklist_category=DATABOX_getcategoriesinp ($category,$fieldset_id,"databox");
    $templates->set_var('checklist_category', $checklist_category);

    //追加項目
    $templates->set_var('lang_additionfields', $LANG_DATABOX_ADMIN['additionfields']);
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
    $templates->set_var ('lang_udatetime', $LANG_DATABOX_ADMIN['udatetime']);
    $templates->set_var ('udatetime', $udatetime);
    $templates->set_var ('lang_uuid', $LANG_DATABOX_ADMIN['uuid']);
    $templates->set_var ('uuid', $uuid);
    //作成日付
    $templates->set_var ('lang_created', $LANG_DATABOX_ADMIN['created']);
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

function fnctemplatesdirectory (
    $defaulttemplatesdirectory
)
{

    global $_CONF;
    global $_TABLES;
        //global $_USER ;

    global $_DATABOX_CONF;

    //
    $selection = '<select id="defaulttemplatesdirectory" name="defaulttemplatesdirectory">' . LB;
	$selection .= "<option value=\"\">  </option>".LB;

	if ($_DATABOX_CONF['templates']==="theme"){
        $fd1=$_CONF['path_layout'].$_DATABOX_CONF['themespath']."data/";
    }else if ($_DATABOX_CONF['templates']==="custom"){
        $fd1=$_CONF['path'] .'plugins/databox/custom/templates/data/';
    }else{
        $fd1=$_CONF['path'] .'plugins/databox/templates/data/';
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

function fncSave (
    $edt_flg
    ,$navbarMenu
    ,$menuno

)
// +---------------------------------------------------------------------------+
// | 機能  保存                                                                |
// | 書式 fncSave ($edt_flg)                                                   |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:戻り画面＆メッセージ                                           |
// +---------------------------------------------------------------------------+
//20101207
{
    $pi_name="databox";

    global $_CONF;
    global $LANG_DATABOX_ADMIN;
    global $_TABLES;
    global $_USER;
    global $_DATABOX_CONF;

    global $_FILES;


    $addition_def=DATABOX_getadditiondef();

    // clean 'em up
	$id = COM_applyFilter($_POST['id'],true);
	
	$fieldset_id = COM_applyFilter ($_POST['fieldset'],true);
    $code=COM_applyFilter($_POST['code']);
    $code=addslashes (COM_checkHTML (COM_checkWords ($code)));

    $title = COM_stripslashes($_POST['title']);
    $title = addslashes (COM_checkHTML (COM_checkWords ($title)));

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
		$comment_expire_ampm=COM_applyFilter ($_POST['comment_expire_ampm']);
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
	$released_ampm=COM_applyFilter ($_POST['released_ampm']);
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
		$expired_ampm=COM_applyFilter ($_POST['expired_ampm']);
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
    //コード
	if ($code<>""){
		$code=rtrim(ltrim($code));
		$newcode=COM_sanitizeID($code,false);
		if  ($code<>$newcode){
            $err.=$LANG_DATABOX_ADMIN['err_code_x']."<br/>".LB;
		}else{
			$cntsql="SELECT code FROM {$_TABLES['DATABOX_base']} ";
			$cntsql.=" WHERE ";
			$cntsql.=" code='{$code}' ";
			$cntsql.=" AND id<>'{$id}' ";
			$result = DB_query ($cntsql);
			$numrows = DB_numRows ($result);
			if ($numrows<>0 ) {
				$err.=$LANG_DATABOX_ADMIN['err_code_w']."<br/>".LB;
			}
		}
	}

    //タイトル必須
    if (empty($title)){
        $err.=$LANG_DATABOX_ADMIN['err_title']."<br/>".LB;
    }
    //コード必須
    if ($_DATABOX_CONF['datacode']){
        if (empty($code)){
            $err.=$LANG_DATABOX_ADMIN['err_code']."<br/>".LB;
		}
    }
	//文字数制限チェック
	if (mb_strlen($description, 'UTF-8')>$_DATABOX_CONF['maxlength_description']) {
		$err.=$LANG_DATABOX_ADMIN['description']
				.$_DATABOX_CONF['maxlength_description']
				.$LANG_DATABOX_ADMIN['err_maxlength']."<br/>".LB;
	}
	if (mb_strlen($meta_description, 'UTF-8')>$_DATABOX_CONF['maxlength_meta_description']) {
		$err.=$LANG_DATABOX_ADMIN['meta_description']
				.$_DATABOX_CONF['maxlength_meta_description']
				.$LANG_DATABOX_ADMIN['err_maxlength']."<br/>".LB;
	}
	if (mb_strlen($meta_keywords, 'UTF-8')>$_DATABOX_CONF['maxlength_meta_keywords']) {
		$err.=$LANG_DATABOX_ADMIN['meta_keywords']
				.$_DATABOX_CONF['maxlength_meta_keywords']
				.$LANG_DATABOX_ADMIN['err_maxlength']."<br/>".LB;
	}

	
    //----追加項目チェック
    $err.=DATABOX_checkaddtiondatas
        ($additionfields,$addition_def,$pi_name,$additionfields_fnm,$additionfields_del
         ,$additionfields_alt);

    //編集日付
    $modified=$modified_year."-".$modified_month."-".$modified_day;
    if (checkdate($modified_month, $modified_day, $modified_year)==false) {
       $err.=$LANG_DATABOX_ADMIN['err_modified']."<br/>".LB;
    }
    $modified=COM_convertDate2Timestamp(
        $modified_year."-".$modified_month."-".$modified_day
        , $modified_hour.":".$modified_minute."::00"
        );

    //公開日
    $released=$released_year."-".$released_month."-".$released_day;
    if (checkdate($released_month, $released_day, $released_year)==false) {
       $err.=$LANG_DATABOX_ADMIN['err_released']."<br/>".LB;
    }
    $released=COM_convertDate2Timestamp(
        $released_year."-".$released_month."-".$released_day
        , $released_hour.":".$released_minute."::00"
		);


    //コメント受付終了日時
    IF ($comment_expire_flag){
        if (checkdate($comment_expire_month, $comment_expire_day, $comment_expire_year)==false) {

           $err.=$LANG_DATABOX_ADMIN['err_comment_expire']."<br/>".LB;
        }
        $comment_expire=COM_convertDate2Timestamp(
            $comment_expire_year."-".$comment_expire_month."-".$comment_expire_day
            , $comment_expire_hour.":".$comment_expire_minute."::00"
            );

    }else{
        $comment_expire='0000-00-00 00:00:00';
        //$comment_expire=null;//"";

    }

    //公開終了日
    IF ($expired_flag){
        if (checkdate($expired_month, $expired_day, $expired_year)==false) {

           $err.=$LANG_DATABOX_ADMIN['err_expired']."<br/>".LB;
        }
        $expired=COM_convertDate2Timestamp(
            $expired_year."-".$expired_month."-".$expired_day
            , $expired_hour.":".$expired_minute."::00"
            );
        if ($expired<$released) {
           $err.=$LANG_DATABOX_ADMIN['err_expired']."<br/>".LB;
        }
    }else{
        $expired='0000-00-00 00:00:00';
        //$expired=null;//"";
    }

    //errorのあるとき
    if ($err<>"") {
        $retval['title']=$LANG_DATABOX_ADMIN['piname'].$LANG_DATABOX_ADMIN['edit'];
        $retval['display']= fncEdit($id, $edt_flg,3,$err,"edit",$old_mode);

        return $retval;

    }
    // CHECK　おわり

    if ($id==0){
        $w=DB_getItem($_TABLES['DATABOX_base'],"max(id)","1=1");
        if ($w=="") {
            $w=0;
        }
		$id=$w+1;
		$created=COM_convertDate2Timestamp(date("Y-m-d"),date("H:i::00"));
    }

    $hits=0;
    $comments=0;

    $fields="id";
    $values="$id";

    $fields.=",code";
    $values.=",'$code'";

    $fields.=",title";//
    $values.=",'$title'";

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

//    $fields.=",udatetime";
//    $values.=",NOW( )";

    DB_save($_TABLES['DATABOX_base'],$fields,$values);

    //カテゴリ
    //$rt=DATABOX_savedatas("category_id",$_TABLES['DATABOX_category'],$id,$category);
    $rt=DATABOX_savecategorydatas($id,$category);

	//追加項目
	if  ($old_mode=="copy"){
		DATABOX_uploadaddtiondatas_cpy
		     ($additionfields,$addition_def,$pi_name,$id,$additionfields_fnm,$additionfields_del,$additionfields_old,$additionfields_alt);
	}else{	
        DATABOX_uploadaddtiondatas	
            ($additionfields,$addition_def,$pi_name,$id
            ,$additionfields_fnm,$additionfields_del,$additionfields_old,$additionfields_alt);
	}
		
    $rt=DATABOX_saveaddtiondatas
        ($id,$additionfields,$addition_def,$pi_name);

    $rt=fncsendmail ('data',$id);

    $cacheInstance = 'databox__' . $id . '__' ;
    CACHE_remove_instance($cacheInstance); 

//exit;// ＠＠＠＠＠＠debug 用

//    if ($edt_flg){
//        $return_page=$_CONF['site_url'] . "/".THIS_SCRIPT;
//        $return_page.="?id=".$id;
//    }else{
//        $return_page=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?msg=1';
//    }

    if ($_DATABOX_CONF['aftersave_admin']==='no'){
        $retval['title']=$LANG_DATABOX_ADMIN['piname'].$LANG_DATABOX_ADMIN['edit'];
        //$retval['display']= COM_showMessage (1,'databox');
        $retval['display'] .= fncEdit($id, $edt_flg,1,"");
        return $retval;
		
	}else if ($_DATABOX_CONF['aftersave_admin']==='list'){
            $url = $_CONF['site_admin_url'] . "/plugins/$pi_name/data.php";
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
                    ,'databox'
                    , 1);

    echo $return_page;

	exit;
}

function fncdelete ()
// +---------------------------------------------------------------------------+
// | 機能  削除                                                                |
// | 書式 fncdelete ()                                                         |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:戻り画面＆メッセージ                                           |
// +---------------------------------------------------------------------------+
{
    global $_CONF, $_TABLES;
    global $LANG_DATABOX_ADMIN;
	
	$pi_name="databox";
	
	$id = COM_applyFilter($_POST['id'],true);
    $addition_def=DATABOX_getadditiondef();//@@@@@
	$additionfields=$_POST['afield'];//@@@@@
	
    // CHECK
    $err="";
    if ($err<>"") {
		$retval['title']=$LANG_DATABOX_ADMIN['piname'].$LANG_DATABOX_ADMIN['edit'];
        $retval['display']= fncEdit($id, $edt_flg,3,$err);

        return $retval;

		
	}

	$rt=databox_deletedata ($id);

    $rt=fncsendmail ('data_delete',$id,$title);
	
    $cacheInstance = 'databox__' . $id . '__' ;
    CACHE_remove_instance($cacheInstance); 

    //exit;// @@@@@debug 用

    //$return_page=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?msg=2';

    //return COM_refresh ($return_page);
    //echo $return_page;
	
	$retval['title']=$LANG_DATABOX_ADMIN['piname'];
	$retval['display']= COM_showMessage (2,'databox');
    $retval['display'].= fncList();

    return $retval;

}



function fncchangeDraft (
	$id
)
// +---------------------------------------------------------------------------+
// | 機能  DRAFT チェンジ更新                                                  |
// | 書式 fncchangeDraft ($seqno)                                              |
// +---------------------------------------------------------------------------+
// | 引数 $draft_flg :                                                         |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
{
    global $_TABLES;
    global $_USER;

    $id = COM_applyFilter($id,true);
    $uuid=$_USER['uid'];

    $sql="UPDATE {$_TABLES['DATABOX_base']} set ";
    if (DB_getItem($_TABLES['DATABOX_base'],"draft_flag", "id=$id")) {
        $sql.=" draft_flag = '0'";
    } else {
        $sql.=" draft_flag = '1'";
    }
    $sql.=",uuid='$uuid' WHERE id=$id";

	DB_query($sql);
	
	$cacheInstance = 'databox__' . $id . '__' ;
    CACHE_remove_instance($cacheInstance); 

    return;

}

function fncchangeDraftAll (
	$flg
)
// +---------------------------------------------------------------------------+
// | 機能  DRAFT チェンジ更新                                                  |
// | 書式 fncchangeDraftAll ($flg)                                             |
// +---------------------------------------------------------------------------+
// | 引数 $draft_flg :                                                         |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
{
    global $_TABLES;
    global $_USER;

    if ($flg==0) {
        $nflg=1;
    }else{
        $nflg=0;
    }
    $uuid=$_USER['uid'];

    $sql="UPDATE {$_TABLES['DATABOX_base']} set ";
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

    $sql="UPDATE {$_TABLES['DATABOX_stats']} set ";
    $sql.="hits = 0";
    $sql.=" WHERE hits<>0";
    DB_query($sql);
    return;
}

function fncexportexec()
// +---------------------------------------------------------------------------+
// | 機能  エキスポート                                                        |
// | 書式 fncexport ()                                                         |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
{
global $_CONF;
global $_TABLES;
global $LANG_DATABOX_ADMIN;
//require_once ($_CONF['path'].'plugins/databox/lib/comj_dltbldt.php');

// 項目の見出リスト
$fld = array ();
	
	
//3行目タイプ　準備中
$fld['id']['name'] = $LANG_DATABOX_ADMIN['id'];
$fld['id']['type'] = "numeric";
	
$fld['code']['name'] = $LANG_DATABOX_ADMIN['code'];
$fld['code']['type'] = "text";
	
$fld['title']['name'] = $LANG_DATABOX_ADMIN['title'];
$fld['title']['type'] = "text";

$fld['page_title']['name'] = $LANG_DATABOX_ADMIN['page_title'];
$fld['description']['name'] = $LANG_DATABOX_ADMIN['description'];
$fld['comments']['name'] = $LANG_DATABOX_ADMIN['comments'];
$fld['meta_description']['name'] = $LANG_DATABOX_ADMIN['meta_description'];
$fld['meta_keywords']['name'] = $LANG_DATABOX_ADMIN['meta_keywords'];
$fld['commentcode']['name'] = $LANG_DATABOX_ADMIN['commentcode'];
$fld['comment_expire']['name'] = $LANG_DATABOX_ADMIN['comment_expire'];

// 準備中　$fld['language_id'] = $LANG_DATABOX_ADMIN['language_id'];
$fld['owner_id']['name'] = $LANG_DATABOX_ADMIN['owner_id'];
$fld['group_id']['name'] = $LANG_DATABOX_ADMIN['group_id'];
$fld['perm_owner']['name'] = $LANG_DATABOX_ADMIN['perm_owner'];
$fld['perm_group']['name'] = $LANG_DATABOX_ADMIN['perm_group'];
$fld['perm_members']['name'] = $LANG_DATABOX_ADMIN['perm_members'];
$fld['perm_anon']['name'] = $LANG_DATABOX_ADMIN['perm_anon'];

$fld['modified']['name'] = $LANG_DATABOX_ADMIN['modified'];
$fld['created']['name'] = $LANG_DATABOX_ADMIN['created'];
$fld['expired']['name'] = $LANG_DATABOX_ADMIN['expired'];
$fld['released']['name'] = $LANG_DATABOX_ADMIN['released'];

$fld['orderno']['name'] = $LANG_DATABOX_ADMIN['orderno'];
$fld['trackbackcode']['name'] = $LANG_DATABOX_ADMIN['trackbackcode'];
$fld['cache_time']['name'] = $LANG_DATABOX_ADMIN['cache_time'];

$fld['draft_flag']['name'] = $LANG_DATABOX_ADMIN['draft'];
$fld['udatetime']['name'] = $LANG_DATABOX_ADMIN['udatetime'];
$fld['uuid']['name'] = $LANG_DATABOX_ADMIN['uuid'];
//-----

//----------------------
$filenm="databox_data";
$tbl ="{$_TABLES['DATABOX_base']}";
	
$fieldset_id = COM_applyFilter ($_REQUEST['fieldset']);
if  ($fieldset_id=="all") {
	$where = "";
}else{
	$where = "fieldset_id =".$fieldset_id;
}		
		
$order = "id";
$addition=true;
$tbl_prefix="DATABOX";
	
$return_page=$url=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=exportform';
$return_page=$url=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT;


$rt= DATABOX_dltbldt($filenm,$fld,$tbl,$where,$order,$tbl_prefix,$addition,$return_page);


return $rt;
}

function fncimport ()
// +---------------------------------------------------------------------------+
// | 機能  インポート画面表示                                                  |
// | 書式 fncimport ()                                                         |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+

{
    global $_CONF;//, $LANG28;
    global $LANG_DATABOX_ADMIN;

    $tmpl = new Template ($_CONF['path'] . "plugins/".THIS_PLUGIN."/templates/admin/");
    $tmpl->set_file(array('import' => 'import.thtml'));

    $tmpl->set_var('site_admin_url', $_CONF['site_admin_url']);

    $tmpl->set_var('gltoken_name', CSRF_TOKEN);
    $tmpl->set_var('gltoken', SEC_createToken());
    $tmpl->set_var ( 'xhtml', XHTML );

    $tmpl->set_var('script', THIS_SCRIPT);

    $tmpl->set_var('importmsg', $LANG_DATABOX_ADMIN['importmsg']);
    $tmpl->set_var('importfile', $LANG_DATABOX_ADMIN['importfile']);
    $tmpl->set_var('submit', $LANG_DATABOX_ADMIN['submit']);

    $tmpl->parse ('output', 'import');
    $import = $tmpl->finish ($tmpl->get_var ('output'));

    $retval="";
    $retval .= $import;


    return $retval;
}

function fncsendmail (
    $m=""
    ,$id=0
    ,$title=""
    )
// +---------------------------------------------------------------------------+
// | 機能  メール送信                                                          |
// | 書式 fncsendmail ()                                                       |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
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
    $subject= $LANG_DATABOX_MAIL['subject_'.$m];
	$message=$LANG_DATABOX_MAIL['message_'.$m];
	
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
            $msg.= $LANG_DATABOX_ADMIN['id'].":".$A['code'].LB;
            $msg.= $LANG_DATABOX_ADMIN['code'].":".$A['code'].LB;
            $msg.= $LANG_DATABOX_ADMIN['title'].":".$A['title'].LB;
            $msg.= $LANG_DATABOX_ADMIN['page_title'].":".$A['page_title'].LB;
            $msg.= $LANG_DATABOX_ADMIN['description'].":".$A['description'].LB;

            $msg.= $LANG_DATABOX_ADMIN['hits'].":".$A['hits'].LB;
            $msg.= $LANG_DATABOX_ADMIN['comments'].":".$A['comments'].LB;
            $msg.= $LANG_DATABOX_ADMIN['meta_description'].":".$A['meta_description'].LB;
            $msg.= $LANG_DATABOX_ADMIN['meta_keywords'].":".$A['meta_keywords'].LB;
            $msg.= $LANG_DATABOX_ADMIN['commentcode'].":".$A['commentcode'].LB;
            $msg.= $LANG_DATABOX_ADMIN['comment_expire'].":".$A['comment_expire'].LB;

            // 準備中　$msg.=  $LANG_DATABOX_ADMIN['language_id'].":".$A['language_id'].LB;
            $msg.= $LANG_DATABOX_ADMIN['owner_id'].":".$A['owner_id'].LB;
            $msg.= $LANG_DATABOX_ADMIN['group_id'].":".$A['group_id'].LB;
            $msg.= $LANG_DATABOX_ADMIN['perm_owner'].":".$A['perm_owner'].LB;
            $msg.= $LANG_DATABOX_ADMIN['perm_group'].":".$A['perm_group'].LB;
            $msg.= $LANG_DATABOX_ADMIN['perm_members'].":".$A['perm_members'].LB;
            $msg.= $LANG_DATABOX_ADMIN['perm_anon'].":".$A['perm_anon'].LB;

            $msg.= $LANG_DATABOX_ADMIN['modified'].":".$A['modified'].LB;
            $msg.= $LANG_DATABOX_ADMIN['created'].":".$A['created'].LB;
            $msg.= $LANG_DATABOX_ADMIN['expired'].":".$A['expired'].LB;
            $msg.= $LANG_DATABOX_ADMIN['released'].":".$A['released'].LB;

            $msg.= $LANG_DATABOX_ADMIN['orderno'].":".$A['orderno'].LB;
            $msg.= $LANG_DATABOX_ADMIN['trackbackcode'].":".$A['trackbackcode'].LB;
            $msg.= $LANG_DATABOX_ADMIN['cache_time'].":".$A['cache_time'].LB;

            $msg.= $LANG_DATABOX_ADMIN['draft'].":".$A['draft'].LB;
            $msg.= $LANG_DATABOX_ADMIN['udatetime'].":".$A['udatetime'].LB;
            $msg.= $LANG_DATABOX_ADMIN['uuid'].":".$A['uuid'].LB;

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
                $url.="code=".$A['code'];
                $url.="&amp;m=code";
            }else{
                $url.="id=".$A['id'];
                $url.="&amp;m=id";
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

	return ;
}

function fncNew (
)
// +---------------------------------------------------------------------------+
// | 機能 新規登録 タイプ選択
// | 書式 fncNew()
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
{
	global $_CONF;
	global $LANG_DATABOX_ADMIN;
	global $LANG_ADMIN;
	
	$pi_name="databox";
	
    $retval = '';
	
	//-----
    $tmplfld=DATABOX_templatePath('admin','default',$pi_name);
    $templates = new Template($tmplfld);
    $templates->set_file('editor',"selectset.thtml");
	
    $templates->set_var('site_url', $_CONF['site_url']);
    $templates->set_var('site_admin_url', $_CONF['site_admin_url']);
	
    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);
    $templates->set_var('gltoken_name', CSRF_TOKEN);
    $templates->set_var('gltoken', $token);
    $templates->set_var ( 'xhtml', XHTML );

    $templates->set_var('script', THIS_SCRIPT);

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

function fncChangeSet (
)
// +---------------------------------------------------------------------------+
// | 機能 新規登録 タイプ登録変更
// | 書式 fncChangeSet()
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
{
	global $_CONF;
	global $LANG_DATABOX_ADMIN;
	global $LANG_ADMIN;
	global $_TABLES;
	
	$pi_name="databox";
	
    $retval = '';
	
	$id = COM_applyFilter ($_REQUEST['id'], true);
	//-----
	if  ($id==0){
		$actionname=$LANG_DATABOX_ADMIN['registset'];
	}else{
		$actionname=$LANG_DATABOX_ADMIN["changeset"];
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
		$inst=$LANG_DATABOX_ADMIN['inst_changeset0'];
		$templates->set_var ('lang_changeset', $LANG_DATABOX_ADMIN['registset']);
	}else{
		$inst=DB_getItem($_TABLES['DATABOX_base'],"title","id=".$id);
		$inst.=$LANG_DATABOX_ADMIN['inst_changesetx'];
		$templates->set_var ('lang_changeset', $LANG_DATABOX_ADMIN['changeset']);
	}
	//$inst.=$LANG_DATABOX_ADMIN['inst_changeset'];
	$templates->set_var ('lang_inst_changeset', $inst);
	
	//fieldset_id
	$fieldset_id=0;
	$templates->set_var('lang_fieldset', $LANG_DATABOX_ADMIN['fieldset']);
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
	
	$sql="SELECT id FROM {$_TABLES['DATABOX_base']}  ";
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
		
		$sql="UPDATE {$_TABLES['DATABOX_base']} set ";
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

	global $_DATABOX_CONF;
	global $LANG_DATABOX_ADMIN;
	global $LANG_ADMIN;
	
	$pi_name="databox";
	
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
	
    $tmpl->set_var('actionname', $LANG_DATABOX_ADMIN['export']);
    $tmpl->set_var('lang_inst', $LANG_DATABOX_ADMIN['inst_dataexport']);
	
	//fieldset_id
	$fieldset_id="all";
	$tmpl->set_var('lang_fieldset', $LANG_DATABOX_ADMIN['fieldset']);
	$list_fieldset=DATABOX_getoptionlist("fieldset",$fieldset_id,0,$pi_name,"","all" );
	$tmpl->set_var ('list_fieldset', $list_fieldset);
	
    $tmpl->set_var('lang_export', $LANG_DATABOX_ADMIN["export"]);
    $tmpl->set_var('lang_cancel', $LANG_ADMIN['cancel']);
	
	
    $tmpl->parse ('output', 'exportform');
    $exportform = $tmpl->finish ($tmpl->get_var ('output'));
    $retval .= $exportform;

	return $retval;
}

function fncdatadeleteExec (
    $action
)
{
	global $_TABLES;
	global $LANG_DATABOX_ADMIN;
	
	$fieldset = $_POST['fieldset'];
    //if (is_array($fieldset)){
    //        $S = $fieldset;
    //}else{
    //    if( !empty( $fieldset ))    {
    //        $S = explode( ' ', $fieldset );
    //    }else {
    //        $S = array();
    //    }
    //}
	if  ($fieldset==""){ 
		return ;
	}
	
	if  ($action==$LANG_DATABOX_ADMIN['delete1']){
		//DRAFT 下書データ
		$arg= " AND draft_flag=1".LB;
	}else if  ($action==$LANG_DATABOX_ADMIN['delete2']){
		//公開終了日を過ぎたデータ
		$arg= " AND NOT (expired=0 OR expired > NOW())";
	}else if  ($action==$LANG_DATABOX_ADMIN['delete3']){
		//すべて
		$arg= "";
	}else{
		return ;
	}	
	
	$rt="";
	$s=implode(", ", $fieldset); 
	
	$sql="SELECT id FROM {$_TABLES['DATABOX_base']}  ";
	$sql .=" WHERE ";
	$sql .="  fieldset_id IN (".$s.")";
	$sql .=$arg;
	
	$result = DB_query ($sql);
	$numrows = DB_numRows ($result);
	if ($numrows > 0) {
        for ($i = 0; $i < $numrows; $i++) {
            $A = DB_fetchArray ($result);
			$A = array_map('stripslashes', $A);
		    $dummy=databox_deletedata ($A['id']);
        }
	}
	
    return ;
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

    global $LANG_DATABOX_ADMIN;

    global $LANG_DATABOX;

    $retval = '';
    //MENU1:管理画面
    $url1=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=new';
    $url7=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=changeset';
    $url2=$_CONF['site_url'] . '/databox/list.php';
    $url3=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=drafton';
	$url4=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=draftoff';
	$url8=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=hitsclear';
    $url5=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=exportform';
    $url6=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=import';
    $url9=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=datadelete';
	
	$menu_arr[]=array('url' => $url1,'text' => $LANG_DATABOX_ADMIN["new"]);
    $menu_arr[]=array('url' => $url7,'text' => $LANG_DATABOX_ADMIN["registset"]);
    $menu_arr[]=array('url' => $url2,'text' => $LANG_DATABOX['list']);
    $menu_arr[]=array('url' => $url3,'text' => $LANG_DATABOX_ADMIN['drafton']);
    $menu_arr[]=array('url' => $url4,'text' => $LANG_DATABOX_ADMIN['draftoff']);
    $menu_arr[]=array('url' => $url8,'text' => $LANG_DATABOX_ADMIN['hitsclear']);
	$menu_arr[]=array('url' => $url5,'text' => $LANG_DATABOX_ADMIN['export']);
	$menu_arr[]=array('url' => $url9,'text' => $LANG_DATABOX_ADMIN['datadelete']);
	
    $menu_arr[]=array('url' => $_CONF['site_admin_url'],'text' => $LANG_ADMIN['admin_home']);
	
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
}else if (($mode == $LANG_DATABOX_ADMIN['new']) && !empty ($LANG_DATABOX_ADMIN['new'])) {
    $mode="newedit";
}else if (($mode == $LANG_DATABOX_ADMIN['changeset']) && !empty ($LANG_DATABOX_ADMIN['changeset'])) {
    $mode="changesetexec";
}else if (($mode == $LANG_DATABOX_ADMIN['registset']) && !empty ($LANG_DATABOX_ADMIN['registset'])) {
    $mode="changesetexec";
}else if (($mode == $LANG_DATABOX_ADMIN['export']) && !empty ($LANG_DATABOX_ADMIN['export'])) {
    $mode="exportexec";
}

if (isset ($_POST['draftChange'])) {
    $mode='draftChange';
}
if ($action == $LANG_ADMIN['cancel'])  { // cancel
    $mode="";
}

//echo "mode1=".$mode."<br>";

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
	OR $mode=="datadelete"
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
	fncChangeSetExec();
}
if ($mode=="datadeleteexec") {
	fncdatadeleteExec ($action);
}

//
$display="";
$menuno=2;
$information = array();

switch ($mode) {
    //confirmation
    case 'drafton':
    case 'draftoff':
    case 'hitsclear':
    case 'datadelete':
        $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'];
        //$display .= fnc_Menu($pi_name);
        $display .= DATABOX_Confirmation($pi_name,$mode);
        break;

    case 'exportform':// エクスポート　画面
        $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'].$LANG_DATABOX_ADMIN['export'];
        $display .= fncexportform();
        break;
    case 'exportexec':// エキスポート実行
		$display=fncexportexec ();
		if  ($display=="") {
			exit;
		}
        break;
	case 'changeset':// 属性セット変更
        $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'].$LANG_DATABOX_ADMIN['new'];
        $display .= fncChangeSet();
        break;
	case 'new':// 新規登録 タイプ選択
        $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'].$LANG_DATABOX_ADMIN['new'];
        $display .= fncNew();
        break;
	case 'newedit':// 新規登録編集
        $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'].$LANG_DATABOX_ADMIN['new'];
        $display .= fncEdit("", $edt_flg,$msg);
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
    case 'copy'://コピー
    case 'edit':// 編集
        if (!empty ($id) ) {
            $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'].$LANG_DATABOX_ADMIN['edit'];
		    $display .= fncEdit($id, $edt_flg,$msg,"",$mode);
        }
        break;
    case 'import':// インポート
        $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'].$LANG_DATABOX_ADMIN['import'];
        $display .= fncimport();
        break;

    default:// 初期表示、一覧表示
        $information['pagetitle']=$LANG_DATABOX_ADMIN['piname'];
        if (isset ($msg)) {
            $display .= COM_showMessage ($msg,'databox');
        }
        $display .= fncList();
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
