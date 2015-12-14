<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | user.php 更新
// +---------------------------------------------------------------------------+
// $Id: profile.php
// public_html/admin/plugins/assist/user.php
// 2009/01/26 tsuchitani AT ivywe DOT co DOT jp

// Set this to true to get various debug messages from this script
$_ASSIST_VERBOSE = false;

define ('THIS_SCRIPT', 'user.php');
//define ('THIS_SCRIPT', 'test.php');

include_once('assist_functions.php');

require_once( $_CONF['path_system'] . 'lib-admin.php' );

// +---------------------------------------------------------------------------+
// | 機能  一覧表示                                                            |
// | 書式 fncList()                                                            |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:一覧                                                           |
// +---------------------------------------------------------------------------+
function fncList()
{
    global $_CONF;
    global $_TABLES;
    global $LANG_ADMIN;
    global $LANG09;
    global $LANG28 ;
    global $LANG_ASSIST_ADMIN;

    $retval = '';

//    $retval .= COM_startBlock ("", '',
//                            COM_getBlockTemplate ('_admin_block', 'header'));

    //LIST設定 はじまり
    //ただいま準備中
    //ヘッダ：編集～
    $header_arr = array(
        array('text' => $LANG_ADMIN['edit'], 'field' => 'editid', 'sort' => false),
        array('text' => $LANG_PROFILE_ADMIN['uid'], 'field' => 'uid', 'sort' => true),
        array('text' => $LANG_PROFILE_ADMIN['udatetime'], 'field' => 'udatetime', 'sort' => true),
        array('text' => $LANG28[3], 'field' => 'username', 'sort' => true),
        array('text' => $LANG_PROFILE_ADMIN['draft'], 'field' => 'draft_flag', 'sort' => true)
    );
    //
    $text_arr = array('has_menu' =>  true,
      'has_extras'   => true,
      'form_url' => $_CONF['site_admin_url'] . "/plugins/".THIS_SCRIPT);

    //Query
    $sql = "SELECT ";
    $sql .= " t1.uid";
    $sql .= " t1.uname";
    $sql .= " FROM ";
    $sql .= " {$_TABLES['users']} AS t1";

    $sql .= " WHERE ";
    $sql .= " 1=1";
    //
    $query_arr = array(
        'table' => 'users',
        'sql' => $sql,
        'query_fields' => array('seqno','username','draft_flag'),
        'default_filter' => $exclude);
    //デフォルトソート項目:
    $defsort_arr = array('field' => 'uid', 'direction' => 'ASC');
    //List 取得
    //ADMIN_list($component, $fieldfunction, $header_arr, $text_arr,
    //       $query_arr, $menu_arr, $defsort_arr, $filter = '', $extra = '', $options = '')
//    $retval .= ADMIN_list(
//        'assist_user'
//        , "fncGetListField"
//        , $header_arr
//        , $text_arr
//        , $query_arr
//        , $defsort_arr
//        );
    //LIST設定 おわり

//    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    return $retval;
}

// +---------------------------------------------------------------------------+
// | 一覧取得   ADMIN_listで使用                                               |
// +---------------------------------------------------------------------------+
function fncGetListField($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $LANG_ACCESS;

    $retval = '';

//    $access = SEC_hasAccess($A['owner_id'],$A['group_id'],$A['perm_owner'],$A['perm_group'],$A['perm_members'],$A['perm_anon']);
    $access=1;
    //
    if ($access > 0) {
        switch($fieldname) {
            //編集アイコン
            case 'editid':
                $retval = "<a href=\"{$_CONF['site_admin_url']}";
                $retval .= "/plugins/".THIS_SCRIPT;
                $retval .= "?mode=edit";
                $retval .= "&amp;uid={$A['uid']}\">";
                $retval .= "{$icon_arr['edit']}</a>";
                break;
            //名
            case 'uname':
                $retval = "<a href=\"{$_CONF['site_url']}";
                $retval .= "/".THIS_SCRIPT;
                $retval .= "?uid={$A['seqno']}\">{$A['uname']}</a>";
                break;
            //各項目
            default:
                $retval = $fieldvalue;
                break;
        }
    }

    return $retval;

}
// +---------------------------------------------------------------------------+
// | 機能  menu表示                                                            |
// | 書式 fncMenu()                                                            |
// +---------------------------------------------------------------------------+
// | 戻値 menu                                                                 |
// +---------------------------------------------------------------------------+
function fncMenu()
{
    global $_CONF;

    global $LANG_ADMIN;
    global $LANG24;
    global $LANG_ASSIST_ADMIN;

    $retval = '';

    //MENU設定 はじまり
    //$url1=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=new';
    //$url2=$_CONF['site_url'] . "/".THIS_SCRIPT;
    //$url3=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=drafton';
    //$url4=$_CONF['site_admin_url'] . '/plugins/'.THIS_SCRIPT.'?mode=draftoff';
    $url_import=$_CONF['site_admin_url'] . '/plugins/'.THIS_PLUGIN."/".THIS_SCRIPT
            .'?mode=importform';
    $url_delete=$_CONF['site_admin_url'] . '/plugins/'.THIS_PLUGIN."/".THIS_SCRIPT
            .'?mode=deleteform';
    //
    $menu_arr = array (
        array('url' => $url_import,
              'text' => $LANG_ASSIST_ADMIN['import']),
        array('url' => $url_delete,
              'text' => $LANG_ASSIST_ADMIN['delete']),
        //
        array('url' => $_CONF['site_admin_url'],
              'text' => $LANG_ADMIN['admin_home']));
	//
	$iconurl=plugin_geticon_assist();
	
    $retval .= ADMIN_createMenu(
        $menu_arr,
        $LANG_PROFILE_ADMIN['instructions'],
        $iconurl
	);
	
    //MENU設定 おわり


    return $retval;
}


// +---------------------------------------------------------------------------+
// | 機能  インポート実行（アカウントの作成）                                  |
// | 書式 fncimportexec ()                                                     |
// +---------------------------------------------------------------------------+
// | 戻値 nomal: 結果画面（NG件数,OK件数）                                     |
// +---------------------------------------------------------------------------+

function fncimportexec()
{
    global $_CONF;
    global $_TABLES;
    global $LANG_ASSIST_ADMIN;

    // true:画面にもOKNG経過表示,ログファイルへ出力
    // false:ログファイルへ出力のみ
    $verbose_import = true;

    $retval = '';

    // Bulk import implies admin authorisation:
    $_CONF['usersubmission'] = 0;

    // First, upload the file
    require_once $_CONF['path_system'] . 'classes/upload.class.php';

    $upload = new upload ();
    $upload->setPath ($_CONF['path_data']);
    $upload->setAllowedMimeTypes (array ('text/plain' => '.txt'));
    $upload->setFileNames ('user_import_file.txt');


    if ($upload->uploadFiles()) {
        // Good, file got uploaded, now install everything
        $thefile = current($_FILES);
        $filename = $_CONF['path_data'] . 'user_import_file.txt';
        if (!file_exists($filename)) { // empty upload form
            $retval = COM_refresh($_CONF['site_admin_url']
                                  . "/plugins/".THIS_PLUGIN."/".THIS_SCRIPT."?msg=err_empty");
            return $retval;
        }
    } else {
        //echo "かくにんのこと"."<br>";
        $retval = COM_siteHeader ('menu', $LANG28[22]);
        $retval .= COM_startBlock ($LANG28[24], '',
                COM_getBlockTemplate ('_msg_block', 'header'));
        $retval .= $upload->printErrors(false);
        $retval .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));

        return $retval;
    }

    //file 処理
    $users = file ($filename);

    $retval .= COM_startBlock ($LANG_ASSIST_ADMIN['import'], '',
                            COM_getBlockTemplate ('_admin_block', 'header'));


    $successes = 0;
    $failures = 0;

    foreach ($users as $line) {
        $line = rtrim ($line);
        if (empty ($line)) {
            continue;
        }
        //echo "line2=".$line."<br>";

        list ($u_id,$full_name, $u_name, $email) = explode ("\t", $line);

        $u_id = COM_applyFilter ($u_id,true);
        $full_name = strip_tags ($full_name);
        $u_name = COM_applyFilter ($u_name);
        $email = COM_applyFilter ($email);

        if ($verbose_import) {
            $w="<br" . XHTML . ">";
            $w .="<b>Working on ";
            $w .="uid=$u_id";
            $w .=", username=$u_name";
            $w .=", fullname=$full_name";
            $w .=", email=$email";
            $w .="</b>";
            $w .="<br" . XHTML . ">\n";
            $retval .=$w;
            COM_errorLog ($w,1);
        }

        // 読み込みデータ
        $uid  = trim ($u_id);//ユーザID
        $userName  = trim ($u_name);//ユーザ名
        $fullName  = trim ($full_name);//氏名
        $emailAddr = trim ($email);// メールアドレス


        // CHECK　はじめ
        $err=0;
        // E_mailAdress が正しい
        if (COM_isEmail ($email)) {
            $ucount = DB_count ($_TABLES['users'], 'username',
                                addslashes ($userName));
            $ecount = DB_count ($_TABLES['users'], 'email',
                                addslashes ($emailAddr));
            $icount = DB_count ($_TABLES['users'], 'uid',
                                $uid);
            if (($ucount == 0) && ($ecount == 0) && ($icount == 0)) {
            // ユーザ名 メールアドレス ユーザID　いずれかが登録済
            } else {
                $err=-1;
                $w= "<br" . XHTML . ">";
                $w.= "<b>$u_name</b> or <b>$email</b> or <b>uid=$uid</b> already exists";
                $w.= ", account not created.<br" . XHTML . ">\n";
                COM_errorLog($w,1);
                if ($verbose_import) {
                    $retval.=$w;
                }
            }
        // E_mailAdress が正しくない
        } else {
            $err=-1;
            $w= "<br" . XHTML . ">";
            $w.="<b>$email</b> is not a valid email address";
            $w.=", account not created<br" . XHTML . ">\n";
            COM_errorLog($w,1);
            if ($verbose_import) {
                $retval.=$w;
            }
        }
        // ユーザ名 が未設定
        if ($u_name=="") {
            $err=-1;
            $w= "<br" . XHTML . ">";
            $w.="<b>$uname</b> is not a valid user name";
            $w.=", account not created<br" . XHTML . ">\n";
            COM_errorLog($w,1);
            if ($verbose_import) {
                $retval.=$w;
            }
        }
        //-----エラーがなければ、アカウントを作成する
        if ($err==0){
            $passwd="";
            $homepage = '';

            //アカウントの作成
            $result = fnccreateAccount (
                $userName
                , $emailAddr
                , $passwd
                , $fullName
                , $homepage
                , $uid
            );
            // アカウントの作成OK
            if ($result) {
                $successes++;
                $w="<br" . XHTML . "> ";
                $w.="Account for <b>$u_name</b> created successfully.<br" . XHTML . ">\n";
                COM_errorLog($w,1);
                if ($verbose_import) {
                    $retval .= $w;
                }
            // アカウントの作成NG
            }else{
                $failures++;
                // user creation failed
                $w="<br" . XHTML . ">ERROR: There was a problem creating the account for ";
                $w.="<b>$u_name</b>.<br" . XHTML . ">\n";
                $retval .= $w;
                COM_errorLog($w,1);
                $failures++;
            }
        }else{
             $failures++;
        }
    } // end foreach

    unlink ($filename);


    $retval .= '<p>' . sprintf ($LANG28[32], $successes, $failures);

    $retval .= COM_endBlock (COM_getBlockTemplate ('_admin_block', 'footer'));

    return $retval;
}
// +---------------------------------------------------------------------------+
// | 機能  アカウント作成　一括処理                                            |
// | 書式  $result = fnccreateAccount ( $username, $email);                    |
// | 書式  $result = fnccreateAccount (                                        |
// |          $userName , $emailAddr, $passwd , $fullName, $homepage , $uid ); |
// +---------------------------------------------------------------------------+
// | 引数: $username ユーザ名                                                  |
// | 引数: $email メールアドレス                                               |
// | 引数: $passwd パスワード  省略時 乱数により作成                           |
// | 引数: $fullusername 氏名                                                  |
// | 引数: $homepage ホームページ                                              |
// | 引数: $username ユーザID  省略時 自動作成                                 |
// +---------------------------------------------------------------------------+
// | 戻値 nomal: true                                                          |
// +---------------------------------------------------------------------------+

function fnccreateAccount (
    $username
    , $email
    , $passwd = ''
    , $fullname = ''
    , $homepage = ''
    , $uid=""
    )
{
    global $_CONF, $_TABLES;

    $batchimport=true;//一括処理
    $ret=true;

    $username = addslashes ($username);
    $email = addslashes ($email);
    $fullname = addslashes ($fullname);
    $homepage = addslashes ($homepage);
    //UIDを取得する
    if ($uid==0) {
        $w=DB_getItem($_TABLES['users'],"max(uid)","1=1");
        if ($w=="") {
            $w=0;
        }
        $uid=$w+1;
    }

    $regdate = strftime ('%Y-%m-%d %H:%M:%S', time ());
    $fields = 'uid,username,email,regdate,cookietimeout';
    $values = "$uid,'$username','$email','$regdate','{$_CONF['default_perm_cookie_timeout']}'";

    //パスワードを更新する
    if (!empty ($passwd)) {
        $passwd = addslashes ($passwd);
        $fields .= ',passwd';
        $values .= ",'$passwd'";
    }else{
        srand ((double) microtime () * 1000000);//擬似乱数の発生系列を変更する
        $passwd1 = rand ();
        $passwd1 = md5 ($passwd1);
        $passwd1 = substr ($passwd1, 1, 8);
        $passwd2 = SEC_encryptPassword($passwd1);
        $fields .= ',passwd';
        $values .= ",'$passwd2'";
    }

    //フルネーム
    if (!empty ($fullname)) {
        $fullname = addslashes ($fullname);
        $fields .= ',fullname';
        $values .= ",'$fullname'";
    }

    //ホームページ
    if (!empty ($homepage)) {
        $homepage = addslashes ($homepage);
        $fields .= ',homepage';
        $values .= ",'$homepage'";
    }

    // DB users 追加
    DB_query ("INSERT INTO {$_TABLES['users']} ($fields) VALUES ($values)");

    // Add user to Logged-in group (i.e. members) and the All Users group
    $normal_grp = DB_getItem ($_TABLES['groups'], 'grp_id',
                              "grp_name='Logged-in Users'");
    $all_grp = DB_getItem ($_TABLES['groups'], 'grp_id',
                           "grp_name='All Users'");
    DB_query ("INSERT INTO {$_TABLES['group_assignments']}
                (ug_main_grp_id,ug_uid) VALUES ($normal_grp, $uid)");
    DB_query ("INSERT INTO {$_TABLES['group_assignments']}
                (ug_main_grp_id,ug_uid) VALUES ($all_grp, $uid)");

    // DB userprefs 追加
    DB_query ("INSERT INTO {$_TABLES['userprefs']} (uid) VALUES ($uid)");

    // デイリーダイジェスト　新規ユーザのデフォルトにより更新
    if ($_CONF['emailstoriesperdefault'] == 1) {
        DB_query ("INSERT INTO {$_TABLES['userindex']} (uid,etids) VALUES ($uid,'')");
    } else {
        DB_query ("INSERT INTO {$_TABLES['userindex']} (uid,etids) VALUES ($uid, '-')");
    }
    //DB usercomment 追加
    DB_query ("INSERT INTO {$_TABLES['usercomment']} (uid,commentmode,commentlimit) VALUES ($uid,'{$_CONF['comment_mode']}','{$_CONF['comment_limit']}')");
    //DB userinfo　追加
    DB_query ("INSERT INTO {$_TABLES['userinfo']} (uid) VALUES ($uid)");

    // call custom registration function and plugins
    if ($_CONF['custom_registration'] && (function_exists ('CUSTOM_userCreate'))) {
        CUSTOM_userCreate ($uid,$batchimport);
    }
    PLG_createUser ($uid);

    return $ret;
}




// +---------------------------------------------------------------------------+
// | 機能  インポート画面表示                                                  |
// | 書式 fncimport ()                                                         |
// +---------------------------------------------------------------------------+
// | 戻値 nomal: インポート画面（ファイル名入力）                              |
// +---------------------------------------------------------------------------+
function fncimport ()
{
    global $_CONF;//, $LANG28;
    global $LANG_ASSIST_ADMIN;


    $pi_name="assist";
    $tmplfld=databox_templatePath('admin','default',$pi_name);
    $tmpl = new Template($tmplfld);

    $tmpl->set_file(array('import' => 'import.thtml'));

    $tmpl->set_var('site_admin_url', $_CONF['site_admin_url']);

    $tmpl->set_var('gltoken_name', CSRF_TOKEN);
    $tmpl->set_var('gltoken', SEC_createToken());
    $tmpl->set_var ( 'xhtml', XHTML );

    $tmpl->set_var('script', THIS_PLUGIN."/".THIS_SCRIPT);

    $tmpl->set_var('importmsg', $LANG_ASSIST_ADMIN['importmsg_user'] );
    $tmpl->set_var('importfile', $LANG_ASSIST_ADMIN['importfile']);
    $tmpl->set_var('submit', $LANG_ASSIST_ADMIN['submit']);


    $tmpl->parse ('output', 'import');
    $import = $tmpl->finish ($tmpl->get_var ('output'));

    $retval="";
    $retval .= COM_startBlock ($LANG_ASSIST_ADMIN['import'], '',
                            COM_getBlockTemplate ('_admin_block', 'header'));
    $retval .= $import;
    $retval .= COM_endBlock (COM_getBlockTemplate ('_admin_block', 'footer'));

    return $retval;
}



// +---------------------------------------------------------------------------+
// | 機能  一括削除実行                                                        |
// | 書式 fncdeleteexec ()                                                     |
// +---------------------------------------------------------------------------+
// | 戻値 nomal: 結果画面（NG件数,OK件数）                                     |
// +---------------------------------------------------------------------------+

function fncdeleteexec()
{

    global $_CONF;
    global $_TABLES;
    global $LANG_ASSIST_ADMIN;

    require_once $_CONF['path_system'] . 'lib-user.php';

    // true:画面にもOKNG経過表示,ログファイルへ出力
    // false:ログファイルへ出力のみ
    $verbose_delete = true;

    $retval = '';

    $uidfrom="";
    $uidto="";
    if (isset ($_REQUEST['uidfrom'])) {
        $uidfrom = COM_applyFilter ($_REQUEST['uidfrom'], true);
        if ($uidfrom <=0) {
            $uidfrom=3;
        }
    }
    if (isset ($_REQUEST['uidto'])) {
        $uidto = COM_applyFilter ($_REQUEST['uidto'], true);
        if ($uidfrom <=0) {
            $uidfrom=3;
        }
    }

    $sql = "SELECT ";
    $sql .= " t.uid ";
    $sql .= " FROM ";
    $sql .= " {$_TABLES['users']} AS t ";

    $sql .= " WHERE ";
    $sql .= " t.uid>={$uidfrom}";
    $sql .= " AND t.uid<={$uidto}";

    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);

    $retval .= COM_startBlock ($LANG_ASSIST_ADMIN['delete'], '',
                            COM_getBlockTemplate ('_admin_block', 'header'));

    $c=0;
    if ($numrows > 0) {
        for ($i = 0; $i < $numrows; $i++) {
            $A = DB_fetchArray ($result);
            if (!USER_deleteAccount ($A["uid"])) {

                $w= "<br" . XHTML . ">";
                $w.="<b>{$A['uid']}</b> delete error";
                $w.="<br" . XHTML . ">\n";
                COM_errorLog($w,1);
                if ($verbose_delete) {
                    $retval.=$w;
                }

            } else {
                $c++; // count the deleted users
                $w= "<br" . XHTML . ">";
                $w.="<b>{$A['uid']}</b> deleted";
                $w.="<br" . XHTML . ">\n";
                COM_errorLog($w,1);
                if ($verbose_delete) {
                    $retval.=$w;
                }

            }
        }
    }

    COM_numberFormat($c);
    $retval .= "delete count: $c<br" . XHTML . ">\n";

    $retval .= COM_endBlock (COM_getBlockTemplate ('_admin_block', 'footer'));

    return $retval;
}

// +---------------------------------------------------------------------------+
// | 機能  一括削除画面表示                                                    |
// | 書式 fncdelete ()                                                         |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:  一括削除画面（開始UID　終了UID入力）                        |
// +---------------------------------------------------------------------------+
function fncdelete ()
{
    global $_CONF;
    global $LANG_ASSIST_ADMIN;


    $pi_name="assist";
    $tmplfld=assist_templatePath('admin','default',$pi_name);
    $tmpl = new Template($tmplfld);

    $tmpl->set_file(array('delete' => 'delete.thtml'));

    $tmpl->set_var('site_admin_url', $_CONF['site_admin_url']);

    $tmpl->set_var('gltoken_name', CSRF_TOKEN);
    $tmpl->set_var('gltoken', SEC_createToken());
    $tmpl->set_var ( 'xhtml', XHTML );

    $tmpl->set_var('script', THIS_PLUGIN."/".THIS_SCRIPT);

    $tmpl->set_var('deletemsg', $LANG_ASSIST_ADMIN['deletemsg_user'] );
    $tmpl->set_var('lang_uidfrom', $LANG_ASSIST_ADMIN['uidfrom']);
    $tmpl->set_var('lang_uidto', $LANG_ASSIST_ADMIN['uidto']);
    $tmpl->set_var('submit', $LANG_ASSIST_ADMIN['submit']);

    $tmpl->parse ('output', 'delete');
    $import = $tmpl->finish ($tmpl->get_var ('output'));

    $retval="";
    $retval .= COM_startBlock ($LANG_ASSIST_ADMIN['delete'], '',
                            COM_getBlockTemplate ('_admin_block', 'header'));
    $retval .= $import;
    $retval .= COM_endBlock (COM_getBlockTemplate ('_admin_block', 'footer'));

    return $retval;
}

// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
// 引数
if (isset ($_REQUEST['mode'])) {
    $mode = COM_applyFilter ($_REQUEST['mode'], false);
}


//echo "mode=".$mode."<br>";
if ($mode=="" OR $mode=="importform" OR $mode=="deleteform") {
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
$display = '';
$display.=ppNavbarjp($navbarMenu,$LANG_ASSIST_admin_menu[$menuno]);

$information = array();
$information['what']='menu';
$information['rightblock']=false;



//echo "mode=".$mode."<br>";
switch ($mode) {
    case 'import':// インポート実行
		$information['pagetitle']=$LANG_ASSIST_ADMIN['piname'].$LANG_ASSIST_ADMIN['import'];
        $display .= fncMenu();
        $display .= fncimportexec();
        break;
    case 'delete':// デリート実行
		$information['pagetitle']=$LANG_ASSIST_ADMIN['piname'].$LANG_ASSIST_ADMIN['delete'];
        $display .= fncMenu();
        $display .= fncdeleteexec();
        break;
    //
    case 'importform':// インポートフォーム表示
        $information['pagetitle']=$LANG_ASSIST_ADMIN['piname'].$LANG_ASSIST_ADMIN['import'];
        $display .= fncMenu();
        //メッセージ表示
        if (!empty ($msg)) {
            $display.= COM_startBlock ($LANG_ASSIST_ADMIN['err'], '',
                               COM_getBlockTemplate ('_msg_block', 'header'))
                    . $LANG_ASSIST_ADMIN[$msg]
                    . COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
        }
        $display .= fncimport();
        break;
    case 'deleteform':// デリートフォーム表示
        $information['pagetitle']=$LANG_ASSIST_ADMIN['piname'].$LANG_ASSIST_ADMIN['delete'];
        //メッセージ表示
        if (!empty ($msg)) {
            $display.= COM_startBlock ($LANG_ASSIST_ADMIN['err'], '',
                               COM_getBlockTemplate ('_msg_block', 'header'))
                    . $LANG_ASSIST_ADMIN[$msg]
                    . COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
        }
        $display .= fncMenu();
        $display .= fncdelete();
        break;

    default:// 初期表示、一覧表示

        $information['pagetitle']=$LANG_ASSIST_ADMIN['piname'];
        $display .= fncMenu();
        $display.=fncList();
        break;

}

//FOR GL2.0.0 
if (COM_versionCompare(VERSION, "2.0.0",  '>=')){
	$display = COM_createHTMLDocument($display,$information);
}else{
	$display = COM_siteHeader ($information['what'], $information['pagetitle']).$display;
	$display .= COM_siteFooter($information['rightblock']);
}

COM_output($display);

?>
