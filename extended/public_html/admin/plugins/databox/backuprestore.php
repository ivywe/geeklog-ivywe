<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | BACKUP & RESTORE
// +---------------------------------------------------------------------------+
// $Id: backuprestore.php
// public_html/admin/plugins/databox/backuprestore.php
// 20121023 tsuchitani AT ivywe DOT co DOT jp

// @@@@@追加予定：サンプルデータのインポート

define ('THIS_SCRIPT', 'databox/backuprestore.php');
//define ('THIS_SCRIPT', 'databox/test.php');

require_once('databox_functions.php');
require_once ($_CONF['path'] . 'plugins/databox/lib/lib_configuration.php');

require_once( $_CONF['path_system'] . 'lib-admin.php' );


function fncDisply(
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 画面表示
// | 書式 fncDisply($pi_name)
// +---------------------------------------------------------------------------+
// | 戻値 nomal:編集画面
// +---------------------------------------------------------------------------+
// 20101126
{
    global $_CONF;
    global $LANG_DATABOX_ADMIN;

    $tmplfld=DATABOX_templatePath('admin','default',$pi_name);
    $templates = new Template($tmplfld);
    $templates->set_file (array (
        'list' => 'backuprestore.thtml',
    ));

    $templates->set_var('about_thispage', $LANG_DATABOX_ADMIN['about_admin_backuprestore']);
    $templates->set_var ('site_admin_url', $_CONF['site_admin_url']);

    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);
    $templates->set_var('gltoken_name', CSRF_TOKEN);
    $templates->set_var('gltoken', $token);
    $templates->set_var ( 'xhtml', XHTML );
    
    $templates->set_var('script', THIS_SCRIPT);

    $templates->set_var ( 'config', $LANG_DATABOX_ADMIN['config']);
    $templates->set_var ( 'config_backup', $LANG_DATABOX_ADMIN['config_backup']);
    $templates->set_var ( 'config_init', $LANG_DATABOX_ADMIN['config_init']);
    $templates->set_var ( 'config_restore', $LANG_DATABOX_ADMIN['config_restore']);
    $templates->set_var ( 'config_update', $LANG_DATABOX_ADMIN['config_update']);
    
    $templates->set_var ( 'config_backup_help', $LANG_DATABOX_ADMIN['config_backup_help']);
    $templates->set_var ( 'config_init_help', $LANG_DATABOX_ADMIN['config_init_help']);
    $templates->set_var ( 'config_restore_help', $LANG_DATABOX_ADMIN['config_restore_help']);
    $templates->set_var ( 'config_update_help', $LANG_DATABOX_ADMIN['config_update_help']);
    
    $templates->set_var ( 'datamaster', $LANG_DATABOX_ADMIN['datamaster']);
    $templates->set_var ( 'data_clear', $LANG_DATABOX_ADMIN['data_clear']);
    $templates->set_var ( 'data_allclear', $LANG_DATABOX_ADMIN['data_allclear']);
    $templates->set_var ( 'data_backup', $LANG_DATABOX_ADMIN['data_backup']);
    $templates->set_var ( 'data_restore', $LANG_DATABOX_ADMIN['data_restore']);

    $err_backup_file= "";
    if (file_exists($_CONF["path_data"]."databoxconfig_bak.php")) {
        $templates->set_var ('restore_disable', "");
        if (is_writable($_CONF["path_data"]."databoxconfig_bak.php")) {
        }else{
            $err_backup_file= $LANG_DATABOX_ADMIN['err_backup_file_non_writable'];
        }

    }else{
        $templates->set_var ('restore_disabled', "disabled");
        $err_backup_file= $LANG_DATABOX_ADMIN['err_backup_file_not_exist'];
    }
    $templates->set_var ('err_backup_file', $err_backup_file);

    $templates->parse ('output', 'list');

    $content = $templates->finish ($templates->get_var ('output'));
    $retval .=$content;

    return $retval ;

}
function fncMenu(
)
// +---------------------------------------------------------------------------+
// | 機能  menu表示  
// | 書式 fncMenu()
// +---------------------------------------------------------------------------+
// | 戻値 menu 
// +---------------------------------------------------------------------------+
{

    global $_CONF;
    global $LANG_ADMIN;

    global $LANG_DATABOX_ADMIN;

    $retval = '';
    //
    $menu_arr[]=array('url' => $_CONF['site_admin_url'],'text' => $LANG_ADMIN['admin_home']);

    $retval .= ADMIN_createMenu(
        $menu_arr,
        $LANG_DATABOX_ADMIN['instructions'],
        plugin_geticon_databox()
    );
    
    return $retval;
}

// +---------------------------------------------------------------------------+
// | 機能 databox data truncate
// | 書式 fncdataclear()
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
function fncclear(
    $mode
)
{

    global $_TABLES;
    global $_CONF;
    global $_DATABOX_CONF;

    $pi_name="databox";
    
    $_SQL =array();

    $_SQL[] = "
        TRUNCATE TABLE {$_TABLES['DATABOX_category']};
    ";
    $_SQL[] = "
        TRUNCATE TABLE {$_TABLES['DATABOX_addition']};
    ";
    $_SQL[] = "
        TRUNCATE TABLE {$_TABLES['DATABOX_base']};
    ";
    $_SQL[] = "
        TRUNCATE TABLE {$_TABLES['DATABOX_stats']};
    ";
    
    if  ($mode=="allclearexec" OR $mode=="clear"){
        
        $_SQL[] = "
            TRUNCATE TABLE {$_TABLES['DATABOX_def_field']};
        ";
        $_SQL[] = "
            TRUNCATE TABLE {$_TABLES['DATABOX_def_category']};
        ";
        $_SQL[] = "
            TRUNCATE TABLE {$_TABLES['DATABOX_def_group']};
        ";
        $_SQL[] = "
            TRUNCATE TABLE {$_TABLES['DATABOX_mst']};
        ";
        $_SQL[] = "
            TRUNCATE TABLE {$_TABLES['DATABOX_def_fieldset']};
        ";
        
        $_SQL[] = "
            TRUNCATE TABLE {$_TABLES['DATABOX_def_fieldset_assignments']};
        ";
        
        $_SQL[] = "
            TRUNCATE TABLE {$_TABLES['DATABOX_def_fieldset_group']};
        ";
        
        if  (fncTableCheck($_TABLES['DATABOX_def_xml'])<>0){
            $_SQL[] = "
                TRUNCATE TABLE {$_TABLES['DATABOX_def_xml']};
			";
		}
        
        if  (fncTableCheck($_TABLES['DATABOX_def_csv'])<>0){
            $_SQL[] = "
                TRUNCATE TABLE {$_TABLES['DATABOX_def_csv']};
            ";
            $_SQL[] = "
                TRUNCATE TABLE {$_TABLES['DATABOX_def_csv_sel']};
            ";
            $_SQL[] = "
                TRUNCATE TABLE {$_TABLES['DATABOX_def_csv_sel_dtl']};
            ";
        }
        if  ($mode=="allclearexec"){
            $_SQL[] = "
                INSERT INTO {$_TABLES['DATABOX_def_group']} (
                `group_id` 
                )
                VALUES (
                '0'
            )";
            $_SQL[] = "
                INSERT INTO {$_TABLES['DATABOX_def_fieldset']} (
                `fieldset_id` 
                )
                VALUES (
                '0'
            )";
        }
    }
    
    
    //------------------------------------------------------------------
    for ($i = 1; $i <= count($_SQL); $i++) {
        $w=current($_SQL);
        DB_query(current($_SQL));
        next($_SQL);
    }
    if (DB_error()) {
        COM_errorLog("error DataBox data clear ",1);
        return "data clear error";
    }
    
    $imgfld=$_CONF['path_html'].$_DATABOX_CONF['imgfile_frd'];
    $imgfld_thmb=$_CONF['path_html'].$_DATABOX_CONF['imgfile_thumb_frd'];
    $rt=DATABOX_deleteaddtionfiles_all($imgfld,$pi_name);
    $rt=DATABOX_deleteaddtionfiles_all($imgfld_thmb,$pi_name);
    
    COM_errorLog("Success - DataBox data clear",1);
    return "<p>".$mode ." finish </p>";
}

function fncBackupdata(
    $pi_name
    )
{
    global $_TABLES;
    global $_CONF;
    
    //-----
    $tablenames=array();
    
    $tablenames[]='DATABOX_base';
    $tablenames[]='DATABOX_addition';
    $tablenames[]='DATABOX_stats';
    $tablenames[]='DATABOX_category';
    
    $tablenames[]='DATABOX_def_field';
    $tablenames[]='DATABOX_def_category';
    $tablenames[]='DATABOX_def_group';
    $tablenames[]='DATABOX_mst';
    
    $tablenames[]='DATABOX_def_fieldset';
    $tablenames[]='DATABOX_def_fieldset_assignments';
    $tablenames[]='DATABOX_def_fieldset_group';
    
    if  (fncTableCheck($_TABLES['DATABOX_def_xml'])<>0){
        $tablenames[]='DATABOX_def_xml';
    }
    
    if  (fncTableCheck($_TABLES['DATABOX_def_csv'])<>0){
        $tablenames[]='DATABOX_def_csv';
        $tablenames[]='DATABOX_def_csv_sel';
        $tablenames[]='DATABOX_def_csv_sel_dtl';
    }
    
    $filename="databox_".date("YmdHis").".xml";
    $filename = $_CONF['backup_path']."databox/".$filename;  //  ファイル名

    $dom = new DomDocument('1.0',"UTF-8");  // DOMを作成
    $dom->encoding = "UTF-8"; // 文字コードをUTF-8に
    $dom->formatOutput = true; // 出力XMLを整形(改行,タブ)する

    //ADD ROOT
    $ROOTData = $dom->appendChild($dom->createElement('ROOT'));
    
    foreach ($tablenames as $tablename) {
        if ($tablename<>""){
            $table=$_TABLES[$tablename];

            
            $sql = "SELECT ".LB;
            $sql .= " * ".LB;
            $sql .= " FROM ".LB;
            $sql .= " {$table} ".LB;

            $result = DB_query ($sql);
            $numrows = DB_numRows ($result);
            if ($numrows > 0) {
                $w=$tablename;
                for ($i = 0; $i < $numrows; $i++) {
                    $RECData = $ROOTData->appendChild($dom->createElement($table));
                    $A = DB_fetchArray ($result,FALSE);
                    $A = array_map('stripslashes', $A);
                    foreach ($A as $k =>$v ) {
                        $FIELDData= $RECData->appendChild($dom->createElement($k));
                        $FIELDData->appendChild($dom->createTextNode($v));
                    }
                }
            }
        }
    }
    $dom->formatOutput = true; // set the formatOutput attribute of
                                       // domDocument to true
    // DomXMLをXML形式で出力
    $dummy=$dom->saveXML();
    $dom->save($filename); // save as file
    

    $rt.="DataBox  BackUp success! (".$filename.")";
    return $rt;

}
function fncbackuprestore (
    $pi_name
    ,$mode
)
// +---------------------------------------------------------------------------+
// | 機能  バックアップリストア画面表示
// | 書式 fnc_backuprestore($pi_name,"backup")
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $LANG_ADMIN;
    
    global $DATABOX_CONF;

    global $LANG_DATABOX_ADMIN;

    $tmpl = new Template ($_CONF['path'] . "plugins/".THIS_PLUGIN."/templates/admin/");
    $tmpl->set_file(array('restore' => $mode.'.thtml'));

    $tmpl->set_var('site_admin_url', $_CONF['site_admin_url']);

    $tmpl->set_var('gltoken_name', CSRF_TOKEN);
    $tmpl->set_var('gltoken', SEC_createToken());
    $tmpl->set_var ( 'xhtml', XHTML );

    $tmpl->set_var('script', THIS_SCRIPT);

    if ($mode==="restoreform"){
        $select_filename=fncgetselectfilename();
        $tmpl->set_var('select_filename', $select_filename);
        $tmpl->set_var('actionname', $LANG_DATABOX_ADMIN['data_restore']);
        $tmpl->set_var('mode', 'restore');
        $tmpl->set_var('help', $LANG_DATABOX_ADMIN['restoremsg']);
    }else{
        $tmpl->set_var('actionname', $LANG_DATABOX_ADMIN['data_backup']);
        $tmpl->set_var('mode', 'backup');
        $tmpl->set_var('help', $LANG_DATABOX_ADMIN['backupmsg']);
    }

    $tmpl->set_var('lang_submit', $LANG_ADMIN['submit']);
    $tmpl->set_var('lang_cancel', $LANG_ADMIN['cancel']);

    $tmpl->parse ('output', 'restore');
    $restore = $tmpl->finish ($tmpl->get_var ('output'));

    $retval= $restore;

    return $retval;
}

function fncbackupexec(
    $pi_name
)
// +---------------------------------------------------------------------------+
// | 機能  バックアップ実行
// | 書式 fnc_backupexec ($pi_name)
// +---------------------------------------------------------------------------+
// | 戻値 nomal: 結果画面
// +---------------------------------------------------------------------------+

{
    global $_CONF;

    global $logmode;

    global $_DATABOX_CONF;

    global $LANG_DATABOX_ADMIN;

    $backupdir = $_CONF['backup_path']."databox/";

    if  (file_exists($backupdir) ){
        if ( is_dir($backupdir) AND is_writable($backupdir)){
            $fchk=true;
        }
    }else{
        $fchk=false;
    }

    //
    if  ($fchk){
        $rt=fncBackupdata($pi_name,$backupdir);
        COM_errorLog("[DATABOX] BackupData ".$rt);
    }else{
        $rt=$backupdir. " not exist";
        COM_errorLog("[DATABOX] BackupData Err ".$rt ." not exist");
    }

    return $rt;

}



function fncrestoreexec(
    $pi_name
)
/// +---------------------------------------------------------------------------+
// | 機能  リストア実行
// | 書式 fnc_restoreexec ($pi_name)
// +---------------------------------------------------------------------------+
// | 戻値 nomal: 結果画面
// +---------------------------------------------------------------------------+

{
    global $_CONF;

    global $logfile;
    global $logmode;
    
    global $_TABLES;
    $table_base=$_TABLES['DATABOX_base'];
    $table_def_field=$_TABLES['DATABOX_def_field'];
    
    global $_DATABOX_CONF;
    global $LANG_DATABOX_ADMIN;
    
    $rt="";
    
    $filename = COM_applyFilter($_POST['filename']);

    if ($filename===""){
        $filename=$pi_name.".xml";
    }

    $importurl=$_CONF['backup_path'].$pi_name;
    $importfile=$importurl."/".$filename;
    
    if  (file_exists($importfile)){
        $fchk=true;
    }else{
        $fchk=false;
    }
        
    if  ($fchk){
        $w= fncclear("clear");
        COM_errorLog("[DataBox] ".$w,1);
        $rt.=$w;
        
        $xmlobj = simplexml_load_file($importfile);
        foreach($xmlobj->Children() as $k => $v){
            $table=$k;
            $fields="";
            $values="";
            
            $tchk=fncTableCheck($table);
            if  ($tchk<>0){
                foreach($v->Children() as $k => $v){
                    if  ($v<>""){
                        if  (($table==$table_base AND $k =="hits")
                            OR ($table==$table_def_field AND $k =="fieldgroup_id")){
                        }else{
                            $v=addslashes($v);
                            $fields.="`".$k."`,";
                            $values.="'".$v."',";
                        }
                    }
                }
                $fields=rtrim($fields,",");
                $values=rtrim($values,",");
        
                $sql="INSERT INTO `".$table."` (";
                $sql.=$fields;
                $sql.="    ) VALUES (";
                $sql.=$values;
                $sql.="    )";
        
                DB_query($sql);
                if (DB_error()) {
                    COM_errorLog("[DataBox] Restore ".$sql,1);
                    $rt.="See the error log！ <{XHTML}br>";
                }
            }
        }
        $dummy=fncchkBasicData($pi_name);
        $dummy=fncchangeowner($pi_name);
        COM_errorLog("[DataBox] Restore execute",1);
        $rt.="<p>DataBox Restore Finish</p>";
    }else{
        COM_errorLog("[DataBox] restorephp $importfile Not exist",1);
        $rt=$importfile." ". $lang_box_admin['err_not_exist'];
    }
    
    return $rt;

}
function fncTableCheck(
    &$table
)
{
    global $_TABLES;
	global $_DB_table_prefix;
	
	$w = explode ('_', $table);
    $wprefix=$w[0]."_";
	if  ($wprefix<>$_DB_table_prefix){
	    $table=str_replace($wprefix, $_DB_table_prefix, $table);
    }
	$rt=in_array($table, $_TABLES);
    if  ($rt<>""){
        $sql="show tables like '{$table}'" ;
        $rt=DB_query($sql);
        $rt=DB_numRows($rt);
    }else{
        $rt=0;
    }
    return $rt;
}

function fncchkBasicData(
    $pi_name
)
{
	
	global $_TABLES;
	$table_group=$_TABLES[strtoupper($pi_name).'_def_group'];
	$table_fieldset=$_TABLES[strtoupper($pi_name).'_def_fieldset'];
	
	$c=DB_getItem($table_group,"group_id","group_id=0");
	if ($c==""){
		$sql = "
			INSERT INTO {$table_group} (
			`group_id` 
			)
			VALUES (
			'0'
			)";
		DB_query($sql);
		if (DB_error()) {
			COM_errorLog("[{$pi_name}] fnc_chkBasicData err".$sql,1);
		}

	}
	
	$c=DB_getItem($table_fieldset,"fieldset_id","fieldset_id=0");
	if ($c==""){
		$sql = "
			INSERT INTO {$table_fieldset} (
			`fieldset_id` 
			)
			VALUES (
			'0'
			)";
		DB_query($sql);
	}
	
}

function fncchangeowner(
    $pi_name
)
{
    global $_USER;
    $owner_id=$_USER['uid'];
	
	global $_TABLES;
	$table_base=$_TABLES[strtoupper($pi_name).'_base'];
	
    $sql="UPDATE {$table_base} set ";
    $sql.="owner_id = '$owner_id'";
	DB_query($sql);
	if (DB_error()) {
		COM_errorLog("[{$pi_name}] fnc_changeowner".$sql,1);
	}

	
}
function fncgetselectfilename (
)
{

    global $_CONF;

    //
    $selection = '<select id="filename" name="filename">' . LB;

    $fd=$_CONF['backup_path']."databox/";
    $files=DATABOX_getfilelist($fd,"xml");

    usort($files, 'strcasecmp');

    foreach ($files as $file) {
        $selection .= '<option value="' . $file . '"';
        if ($file == "databox.xml") {
            $selection .= ' selected="selected"';
        }
        $selection .= '>' . $file . '</option>' . LB;
	}
	
    $selection .= '</select>';

    return $selection;

}

// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
//############################
$pi_name    = 'databox';
//############################
$action = '';
if (isset ($_REQUEST['action'])) {
    $action = COM_applyFilter ($_REQUEST['action'], false);
}

if (isset ($_REQUEST['mode'])) {
    $mode = COM_applyFilter ($_REQUEST['mode'], false);
}
if ($action == $LANG_ADMIN['cancel'])  { // cancel
    $mode="";
}

if ($mode=="" 
    OR $mode=="configinit"
    OR $mode=="configbackup"
    OR $mode=="configrestore"
    OR $mode=="configupdate"

    OR $mode=="dataclear"
    OR $mode=="allclear"
    OR $mode=="backupform" 
    OR $mode=="restoreform" 
    ) {
}else{
	if (!SEC_checkToken()){
 //    if (SEC_checkToken()){//テスト用
        COM_accessLog("User {$_USER['username']} tried to illegally and failed CSRF checks.");
        echo COM_refresh($_CONF['site_admin_url'] . '/index.php');
        exit;
    }
}

$display = '';
$menuno=6;
$information = array();

$information['pagetitle']=$LANG_DATABOX_ADMIN['piname']."backup and restore";
$display.=ppNavbarjp($navbarMenu,$LANG_DATABOX_admin_menu[$menuno]);
if (isset ($_REQUEST['msg'])) {
    $display .= COM_showMessage (COM_applyFilter ($_REQUEST['msg'],
                                                  true), $pi_name);
}

switch ($mode) {
    case 'configinitexec':
        $display .= fncMenu($pi_name);
        $dummy=LIB_Deleteconfig($pi_name,$config);
        $dummy=LIB_Initializeconfig($pi_name);
        $display.="config init";
        $display.=fncDisply($pi_name);
        break;
    case 'configbackupexec':
        $display.=LIB_Backupconfig($pi_name);
        break;
    case 'configrestoreexec';
        $display.=LIB_Restoreconfig($pi_name,$config);
        break;
    case 'configupdateexec':
        $display .= fncMenu($pi_name);
        $dummy=LIB_Backupconfig($pi_name,"update");
        $dummy=LIB_Deleteconfig($pi_name,$config);
        $dummy=LIB_Initializeconfig($pi_name);
        $dummy=LIB_Restoreconfig($pi_name,$config,"update");
        $display.="config update";
        $display.=fncDisply($pi_name);
	    break;
	case 'configbackup':
	case 'configinit':
	case 'configrestore':
	case 'configupdate':
	case 'dataclear':
	case 'allclear':
        $information['pagetitle']=$LANG_databox_ADMIN['piname'];
        $display .= fncMenu($pi_name);
        $display .= DATABOX_Confirmation($pi_name,$mode);
        break;
    case 'dataclearexec'://data clear
    case 'allclearexec'://data clear
        $information['pagetitle']=$LANG_databox_ADMIN['piname'];
        $display .= fncMenu($pi_name);
        $display .= fncclear($mode);
        break;
    case 'backupform':// Backup　画面
    case 'restoreform':// Restore　画面
        $information['pagetitle']=$LANG_databox_ADMIN['piname'].$LANG_databox_ADMIN[$mode];
        $display .= fncMenu($pi_name);
        $display .= fncbackuprestore($pi_name,$mode);
        break;
    case 'backup':
        $information['pagetitle']=$LANG_databox_ADMIN['piname'];
        $display .= fncMenu($pi_name);
        $display .= fncbackupexec($pi_name);
        break;
    case 'restore':
        $information['pagetitle']=$LANG_databox_ADMIN['piname'];
        $display .= fncMenu($pi_name);
        $display .= fncrestoreexec($pi_name);
        break;
    default:
        $display .= fncMenu($pi_name);
        $display.=fncDisply($pi_name);
}

$display=COM_startBlock($LANG_DATABOX_ADMIN['piname'],''
         ,COM_getBlockTemplate('_admin_block', 'header'))
         .$display
         .COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

$display=DATABOX_displaypage($pi_name,'_admin',$display,$information);
COM_output($display);


?>