<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | securitygroup maintenannce
// +---------------------------------------------------------------------------+
// $Id: securitygroup.php
// public_html/userbox/myprofile/securitygroup.php
// 20101129 tsuchitani AT ivywe DOT co DOT jp


define ('THIS_SCRIPT', 'userbox/myprofile/securitygroup.php');
//define ('THIS_SCRIPT', 'userbox/myprofile/test.php');

include_once('userbox_functions.php');

require_once $_CONF['path_system'] . 'lib-user.php';

//ログイン要チェック

if (empty ($_USER['username'])) {
    $page_title= $LANG_PROFILE[4];
    $display .= DATABOX_siteHeader('USERBOX','',$page_title);
    $display .= SEC_loginRequiredForm();
    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
    echo $display;
    exit;
}

if ($_USERBOX_CONF['allow_profile_update']==1 AND  $_USERBOX_CONF['allow_group_update']==1){
}else{
    if (SEC_hasRights ('userbox.joingroup')){
	}else{
	COM_accessLog("User {$_USER['username']} tried to securitygroup and failed ");
		echo COM_refresh($_CONF['site_url'] . '/index.php');
		exit;
	}
}


// +---------------------------------------------------------------------------+
// | 機能  編集画面表示
// | 書式 fncEdit()
// +---------------------------------------------------------------------------+
// | 戻値 nomal:編集画面                                                       |
// +---------------------------------------------------------------------------+
function fncEdit(
    $msg=""
)
{

    $pi_name="userbox";

    global $_CONF;
    global $_TABLES;
    global $LANG_ADMIN;

    global $LANG_ACCESS;
    global $_USER;
    global $LANG28;

    global $LANG_USERBOX_ADMIN;


    $uid=$_USER['uid'];
    $username=$_USER['username'];

    require_once( $_CONF['path_system'] . 'lib-admin.php' );

    $groupsforuser=fncGetGroupsForUser();
    if ($groupsforuser=="") {
        return $LANG_USERBOX_ADMIN['err_group_not_exist'];
    }



    $retval = '';

//    $delflg=false;

    if (!empty ($msg)) {
        $retval .= COM_showMessage ($msg,$pi_name);
    }

    //-----
    $retval .= COM_startBlock ($LANG_USERBOX_ADMIN['edit'], '',
                               COM_getBlockTemplate ('_admin_block', 'header'));

    //template フォルダ
    $tmplfld=DATABOX_templatePath('myprofile','default',$pi_name);
    $templates = new Template($tmplfld);

    $templates->set_file (array (
            'editor' => 'securitygroup_editor.thtml',
            'groupedit' => 'securitygroup_group.thtml'


            ));




    //--

    $templates->set_var('about_thispage', $LANG_USERBOX_ADMIN['about_myprofile_securitygroup']);
    $templates->set_var('site_url', $_CONF['site_url']);
    $templates->set_var('site_admin_url', $_CONF['site_admin_url']);

    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);
    $templates->set_var('gltoken_name', CSRF_TOKEN);
    $templates->set_var('gltoken', $token);
    $templates->set_var ( 'xhtml', XHTML );

    $templates->set_var('script', THIS_SCRIPT);

    // SAVE、CANCEL ボタン
    $templates->set_var('lang_save', $LANG_ADMIN['save']);
    $templates->set_var('lang_cancel', $LANG_ADMIN['cancel']);

//$LANG28 = array(
//    2 => 'ユーザID',
//    3 => 'ユーザ名', username
    $templates->set_var('lang_uid', $LANG28['2']);
    $templates->set_var('uid', $uid);
    $templates->set_var('lang_username', $LANG28['3']);
    $templates->set_var ('username', $username);



    //-----------
    //ヘッダ：編集～
    $header_arr[]=array('text' => $LANG28[86], 'field' => 'checkbox', 'sort' => false);
    $header_arr[]=array('text' => $LANG_ACCESS['groupname'], 'field' => 'grp_name', 'sort' => true);
    $header_arr[]=array('text' => $LANG_ACCESS['description'], 'field' => 'grp_descr', 'sort' => true);


   //
    $form_url= $_CONF['site_url'] . "/plugins/".THIS_SCRIPT;

    $text_arr = array(
            'has_menu' => false
           ,'title' => ''
           ,'instructions' => ''
           ,'icon' => ''
           ,'form_url' => $form_url
           ,'inline' => true
    );

    //

    $whereGroups =  'grp_id IN (' . implode(',', $groupsforuser) . ')';

    $usergroups = SEC_getUserGroups($uid);
    if (is_array($usergroups) && !empty($uid)) {
        $selected = implode(' ', $usergroups);
    } else {
        $selected = '';
    }

    //Query
    $sql = "SELECT ";
    $sql .= " grp_id";
    $sql .= " , grp_name";
    $sql .= " , grp_descr ";
    $sql .= " FROM {$_TABLES['groups']} ";
    $sql .= " WHERE ";

    $sql .= $whereGroups;


    $query_arr = array(
        'table' => 'groups'
        ,'sql' => $sql
        ,'query_fields' => array('grp_name')
        ,'default_filter' => ''
        ,'query' => ''
        ,'query_limit' => 0
    );



    //デフォルトソート項目:
    $defsort_arr = array('field' => 'grp_name', 'direction' => 'asc');

    //List 取得
    //ADMIN_list(
    //       $component, $fieldfunction, $header_arr, $text_arr,
    //       $query_arr, $menu_arr, $defsort_arr, $filter = '', $extra = '', $options = '')
    $groupoptions= ADMIN_list(
        'userbox'
        , "fncGetListField"
        , $header_arr
        , $text_arr
        , $query_arr
        , $defsort_arr
        , ''
        , explode(' ', $selected)
        );


    $templates->set_var('group_options', $groupoptions);
    $templates->parse('group_edit', 'groupedit', true);//??





    //
    $templates->parse('output', 'editor');
    $retval .= $templates->finish($templates->get_var('output'));
    $retval .= COM_endBlock (COM_getBlockTemplate ('_admin_block', 'footer'));


    return $retval;






}

// +---------------------------------------------------------------------------+
// | 一覧取得 ADMIN_list で使用
// +---------------------------------------------------------------------------+
function fncGetListField($fieldname, $fieldvalue, $A, $icon_arr, $selected = '')
{

    $pi_name="userbox";

    global $_CONF;
    global $LANG_ACCESS;


    global $_USERBOX_CONF;

    global $thisUsersGroups;

    $retval = false;

    if(! is_array($thisUsersGroups)) {
        $thisUsersGroups = SEC_getUserGroups();
    }

    switch($fieldname) {
        case 'checkbox':
            $checked = '';
            if (is_array($selected) && in_array($A['grp_id'], $selected)) {
                $checked = ' checked="checked"';
            }
            if (($A['grp_name'] == 'All Users') ||
                ($A['grp_name'] == 'Logged-in Users') ||
                ($A['grp_name'] == 'Remote Users')) {
                $retval = '<input type="checkbox" disabled="disabled"'
                        . $checked . XHTML . '>'
                        . '<input type="hidden" name="groups[]" value="'
                        . $A['grp_id'] . '"' . $checked . XHTML . '>';
            } else {
                $retval = '<input type="checkbox" name="groups[]" value="'
                        . $A['grp_id'] . '"' . $checked . XHTML . '>';
            }
            break;

        case 'grp_name':
            $retval = ucwords($fieldvalue);
            break;
        //各項目
        default:
            $retval = $fieldvalue;
            break;
    }

    return $retval;

}
//-----
function fncGetGroupsForUser()
{
    global $_TABLES;

    $rt="";

    $sql = "SELECT ";
    $sql .= " grp_id";
    $sql .= " FROM {$_TABLES['groups']} ";
    $sql .= " WHERE ";
    $sql .= " LEFT(grp_name,1)= '_'";

    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);

    if ($numrows > 0) {
        $ary=array();
        for ($i = 0; $i < $numrows; $i++) {
            $A = DB_fetchArray ($result);
            $ary[]=$A['grp_id'];
        }
        $rt=$ary;
    }

    return $rt;
}

// +---------------------------------------------------------------------------+
// | 機能  保存                                                                |
// | 書式 fncSave ($edt_flg)                                                   |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:戻り画面＆メッセージ                                           |
// +---------------------------------------------------------------------------+
//20101020-24
function fncSave (
    $navbarMenu
    ,$menuno
)
{
    global $_CONF;

    global $_TABLES;
    global $_USER;
    global $_USERBOX_CONF;
    global $LANG_USERBOX_user_menu;
	global $LANG_USERBOX_ADMIN;
	
    $pi_name="userbox";
	
    $retval = '';

    //
    $groups=$_POST['groups'];
    $uid=$_USER['uid'];
	
    $groupsforuser=fncGetGroupsForUser();
    $whereGroups =  'ug_main_grp_id IN (' . implode(',', $groupsforuser) . ')';


    $sql="DELETE FROM {$_TABLES['group_assignments']} WHERE ";
    $sql.="(ug_uid = $uid)  ";
    $sql .= " AND ".$whereGroups;
    DB_query($sql);

    if (is_array($groups))  {
        foreach ($groups as $userGroup) {
        //foreach( $groups as $fid => $fvalue ){
//echo "fid=".$fid."  fvalue=".$fvalue."<br>";
            $sql = "INSERT INTO {$_TABLES['group_assignments']} ";
            $sql .= "(ug_main_grp_id, ug_uid) ";
            $sql .= "VALUES (";
            $sql .= " $userGroup";
            $sql .= ", $uid";
            $sql .= ")";

            DB_query($sql);
        }
    }
	
    //exit;// debug 用
	$id=$uid;
    if ($_USERBOX_CONF['aftersave']==='no'){
        $page_title=$LANG_USERBOX_ADMIN['piname'].$LANG_USERBOX_ADMIN['edit'];
        $retval .= DATABOX_siteHeader($pi_name,'_admin',$page_title);
        $retval .=ppNavbarjp($navbarMenu,$LANG_USERBOX_user_menu[$menuno]);
        //$retval .= fncEdit($id, $edt_flg,1,$err);
        $retval .= fncEdit(1);
        $retval .= DATABOX_siteFooter($pi_name,'_admin');

        return $retval;
    }else if ($_USERBOX_CONF['aftersave']==='list'
          OR $_USERBOX_CONF['aftersave']==='admin' ){
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

// $return_page="";

    $return_page = PLG_afterSaveSwitch(
                    $target
                    ,$item_url
                    ,$pi_name
                    , 1);

    echo $return_page;

    return ;

}




// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
//############################
$pi_name    = 'userbox';
//############################
$id=$_USER['uid'];


// 引数
if (isset ($_REQUEST['mode'])) {
    $mode = COM_applyFilter ($_REQUEST['mode'], false);
}
$msg = '';
if (isset ($_REQUEST['msg'])) {
    $msg = COM_applyFilter ($_REQUEST['msg'], true);
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


//echo "mode=".$mode."<br>";
if ($mode=="" OR $mode=="edit" OR $mode=="new" OR $mode=="drafton" OR $mode=="draftoff"
    OR $mode=="export" OR $mode=="import"  OR $mode=="copy") {
}else{
    if (!SEC_checkToken()){
 //    if (SEC_checkToken()){//テスト用
        COM_accessLog("User {$_USER['username']} tried to illegally and failed CSRF checks.");
        echo COM_refresh($_CONF['site_admin_url'] . '/index.php');
        exit;
    }
}
//
$menuno=7;
$display="";

switch ($mode) {

    case 'save':// 保存
        $display .= fncSave ($navbarMenu,$menuno);
        break;

    default:// 初期表示、一覧表示
        if (!empty ($id) ) {
            $page_title=$LANG_USERBOX_ADMIN['piname'].$LANG_USERBOX_ADMIN['edit'];
            $display .= DATABOX_siteHeader($pi_name,'_admin',$page_title);
            if ($edt_flg==FALSE){
                $display.=ppNavbarjp($navbarMenu,$LANG_USERBOX_user_menu[$menuno]);
            }
            $display .= fncEdit();
            $display .= DATABOX_siteFooter($pi_name,'_admin');

        }

}



echo $display;

?>
