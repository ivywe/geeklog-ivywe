<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | DataBox プラグイン                                                        |
// +---------------------------------------------------------------------------+
// $Id: plugins/databox/functions.inc
//20110622 tsuchitani AT ivywe DOT co DOT jp http://www.ivywe.co.jp/

if (strpos ($_SERVER['PHP_SELF'], 'functions.inc') !== false) {
    die ('This file can not be used on its own.');
}
$pi_name         = 'databox';

//設定ファイル
require_once ($_CONF['path'] . "plugins/{$pi_name}/config.php");

//言語ファイル
$langfile = $_CONF['path'] . "plugins/{$pi_name}/language/"
          . $_CONF['language'] . '.php';

if (file_exists ($langfile)) {
    require_once ($langfile);

} else {
    require_once ($_CONF['path'] . "plugins/{$pi_name}/language/japanese_utf-8.php");
}

//custom 言語ファイル
$langfile_custom = $_CONF['path'] . "plugins/{$pi_name}/custom/language/"
          . $_CONF['language'] . '.php';

if (file_exists ($langfile_custom)) {
    require_once ($langfile_custom);
}

//custom 関数
$functions_custom = $_CONF['path'] . "plugins/{$pi_name}/custom/functions_custom.inc";
if (file_exists ($functions_custom)) {
    require_once ($functions_custom);
}


//BOXシリーズ共通関数
if (function_exists("DATABOX_nl2br")==false) {
    require_once ($_CONF['path'] . "plugins/{$pi_name}/lib/databox_functions.inc");
}

$_DATABOX_VERBOSE = false;
//$_DATABOX_VERBOSE = true;//FOR TEST

//自動タグ関連の関数
require_once ($_CONF['path'] . "plugins/{$pi_name}/functions_autotag.inc");

//検索機能
require_once ($_CONF['path'] . "plugins/{$pi_name}/functions_search.inc");

//フィード機能
require_once ($_CONF['path'] . "plugins/{$pi_name}/functions_feed.inc");

//STATS機能
require_once ($_CONF['path'] . "plugins/{$pi_name}/functions_stats.inc");

//コメント機能
require_once ($_CONF['path'] . "plugins/{$pi_name}/functions_comment.inc");
//疑似CRON機能
if ($_DATABOX_CONF['csv_cron_schedule_interval'] > 0) {
    $functions_sc = $_CONF['path'] . "plugins/{$pi_name}/csv/csv_scheduled.inc";
    if (file_exists ($functions_sc)) {
		require_once ($functions_sc);
    }
}
if ($_DATABOX_CONF['maps_cron_schedule_interval'] > 0) {
    $functions_sc = $_CONF['path'] . "plugins/{$pi_name}/maps/maps_scheduled.inc";
    if (file_exists ($functions_sc)) {
		require_once ($functions_sc);
    }
}

// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementation                                         |
// +---------------------------------------------------------------------------+

function plugin_chkVersion_databox ()
// +---------------------------------------------------------------------------+
// | 現在インストールされているバージョン取得                                  |
// +---------------------------------------------------------------------------+
{
    global $_DATABOX_CONF;

    return $_DATABOX_CONF['version'];
}


function plugin_upgrade_databox ()
// +---------------------------------------------------------------------------+
// | 機能  プラグイン更新:バージョンナンバーの更新                             |
// +---------------------------------------------------------------------------+
{
	global $_TABLES;
	global $_DATABOX_CONF;
	global $_CONF;
	global $config;
	
	$pi_name    = 'databox';
	
    // the plugin needs these functions so complain when they doesn't exist
    if (!function_exists ('COM_truncate') ||
        !function_exists ('MBYTE_strpos')) {
        return 3002;
    }
	
	$installed_version = DB_getItem($_TABLES['plugins'], 'pi_version',
                                    "pi_name = '{$pi_name}'");
	$newversion=$_DATABOX_CONF['version'];
	
	//update the configuration
		require_once ($_CONF['path'] . 'plugins/databox/lib/lib_configuration.php');

		$rt=LIB_Backupconfig($pi_name,"update");
		$rt=LIB_Deleteconfig($pi_name,$config);
		$rt=LIB_Initializeconfig($pi_name);
		$rt=LIB_Restoreconfig($pi_name,$config,"update");
	
	//update the version numbers
	$sql="UPDATE {$_TABLES['plugins']} SET";
	$sql.=" pi_version = '{$newversion}' ";
	$sql.=" ,pi_gl_version = '" . VERSION . "'";
	$sql.="	WHERE pi_name = 'databox'";
    DB_query ($sql);

    return true;
}

function plugin_autouninstall_databox ()
// +---------------------------------------------------------------------------+
// | 機能  プラグインアンインストール                                          |
// +---------------------------------------------------------------------------+
{
    // (1) 削除するテーブル
    $out['tables'][]='DATABOX_addition';
    $out['tables'][]='DATABOX_base';
    $out['tables'][]='DATABOX_category';
    $out['tables'][]='DATABOX_def_category';
    $out['tables'][]='DATABOX_def_field';
    $out['tables'][]='DATABOX_def_group';
    $out['tables'][]='DATABOX_def_xml';
	$out['tables'][]='DATABOX_stats';
    $out['tables'][]='DATABOX_mst';
	//
    $out['tables'][]='DATABOX_def_fieldset';
    $out['tables'][]='DATABOX_def_fieldset_assignments';
    $out['tables'][]='DATABOX_def_fieldset_group';
	//
    $out['tables'][]='DATABOX_def_category_name';
    $out['tables'][]='DATABOX_def_field_name';
	$out['tables'][]='DATABOX_def_group_name';
	//
    $out['tables'][]='DATABOX_def_csv';
    $out['tables'][]='DATABOX_def_csv_sel';
    $out['tables'][]='DATABOX_def_csv_sel_dtl';
	

    // (2) 削除するグループ名
    $out['groups'][]='DataBox Admin';
	$out['groups'][]='DataBox Submitters';
	$out['groups'][]='DataBox Editor';
	
    // (3) 削除するfeatures
    $out['features'][]='databox.admin';
    $out['features'][]='databox.submit';
    $out['features'][]='databox.edit';
    // (4) 削除するphp_blocks
    $out['php_blocks']=array();
    // (5) 削除する vars */
    $out['vars']=array();

    return $out;

}

/**
* ☆★現在のユーザは権利があるのをチェック
* 使用せず
*/
function plugin_ismoderator_databox()
{
    //return SEC_hasRights ('databox.moderate');
    //return SEC_hasRights ('user.moderate');
    return true;
}

function plugin_geticon_databox ()
// +---------------------------------------------------------------------------+
// | 機能  プラグインのアイコン取得                                            |
// +---------------------------------------------------------------------------+
{
    global $_CONF;

    return $_CONF['site_url'] . '/databox/images/icons/databox.png';
}

function plugin_cclabel_databox()
// +---------------------------------------------------------------------------+
// | 機能  投稿管理admin/moderation.php　メニュ
// +---------------------------------------------------------------------------+
{
    global $_CONF;

	if (SEC_hasRights('databox.admin')) {
        return array(
        'databox'
        ,$_CONF['site_admin_url'] . "/plugins/databox/index.php"
        ,plugin_geticon_databox() );
    }
    return "";

}

function plugin_getadminoption_databox()
// +---------------------------------------------------------------------------+
// | 機能 管理者メニュ表示 (管理者ブロック)
// +---------------------------------------------------------------------------+
{
    global $_CONF, $_TABLES;
    global $LANG_DATABOX_ADMIN;

    if (SEC_hasRights ('databox.admin') ){
        //下書他も含む全件数
        $cnt = DB_getItem ($_TABLES['DATABOX_base'], 'COUNT(*)');
        return array ($LANG_DATABOX_ADMIN['piname'],
                      $_CONF['site_admin_url'] . '/plugins/databox/index.php'
                      ,$cnt);
    }
    return "";

}

function plugin_getmenuitems_databox()
// +---------------------------------------------------------------------------+
// | 機能  ヘッダメニュリンクを返す                                            |
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $_USER;
    global $_DATABOX_CONF;
    global $LANG_DATABOX;

    $anon = (empty ($_USER['uid']) || ($_USER['uid'] <= 1)) ? true : false;
    if (($_DATABOX_CONF['hidemenu'] == 1) || ($anon &&
                ($_CONF['loginrequired'] || $_DATABOX_CONF['loginrequired']))) {
        return false;
    }
    $menuitems[$LANG_DATABOX['data']] = $_CONF['site_url'] . '/databox/index.php';
    if ($_DATABOX_CONF['allow_data_update']==1 ){
        $menuitems[$LANG_DATABOX['mydata']] = $_CONF['site_url'] . '/databox/mydata/index.php';
    }

    return $menuitems;
}

function plugin_getuseroption_databox()
// +---------------------------------------------------------------------------+
// | 機能  ユーザメニュリンクを返す（ユーザブロック）
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $LANG_DATABOX;
    global $_DATABOX_CONF;
	
    if ($_DATABOX_CONF['hideuseroption'] == 1)  {
        return;
    }

    $url = $_CONF['site_url'] . '/databox/mydata/index.php';
    if ($_DATABOX_CONF['allow_data_update']==1 ){
        return array ($LANG_DATABOX['mydata'], $url, '');
	}else{
		if (SEC_hasRights ('databox.edit') ){
			return array ($LANG_DATABOX['mydata'], $url, '');
		}else{
			return;
		}
    }
}

function plugin_whatsnewsupported_databox()
// +---------------------------------------------------------------------------+
// | 機能  新着
// +---------------------------------------------------------------------------+
{
    global $_TABLES;
    global $_DATABOX_CONF;
    global $LANG_DATABOX;
    global $LANG_WHATSNEW;


    if ( $_DATABOX_CONF['hide_whatsnew'] == 'hide' ) {
        $retval = false;
    } else {
        $retval = array( $LANG_DATABOX['pidata'],
                         COM_formatTimeString( $LANG_WHATSNEW['new_last'],
                                               $_DATABOX_CONF['whatsnew_interval'] )
                       );
    }
    return $retval;
}

function plugin_getwhatsnew_databox()
// +---------------------------------------------------------------------------+
// | 機能  新着                                                                |
// +---------------------------------------------------------------------------+
// 再テスト要　日付あたり
{
    global $_CONF;
    global $_TABLES;
    global $_DATABOX_CONF;
    global $LANG_DATABOX;

    $retval = '';

    if ($_DATABOX_CONF['hide_whatsnew'] == 'hide') {
        $datecolumn = 'created';
    } else {
        $datecolumn = $_DATABOX_CONF['hide_whatsnew'] ;
    }


    // Get newest pro
    $sql  = "SELECT t1.id,t1.title ";
    $sql .= " FROM ";
    $sql .= " {$_TABLES['DATABOX_base']} AS t1";

    $sql .= " WHERE ";
    $sql .= " draft_flag=0 ";
    $sql .= " AND (t1.{$datecolumn} >= ";
    $sql .= " (DATE_SUB(NOW(), INTERVAL {$_DATABOX_CONF['whatsnew_interval']} SECOND)))" ;

    //アクセス権のないデータ はのぞく
    $sql .= COM_getPermSql('AND');
    //公開日以前のデータはのぞく
    $sql .= " AND (released <= NOW())".LB;
    //公開終了日を過ぎたデータはのぞく
    $sql .= " AND (expired=0 OR expired > NOW())";


    $sql .= " ORDER BY {$datecolumn} DESC LIMIT 15";
    $result = DB_query($sql);
    $nrows = DB_numRows($result);

    if ($nrows > 0) {
        $recentnew = array();
        for ($x = 0; $x < $nrows; $x++) {
            $A = DB_fetchArray($result);
            $name = stripslashes($A['title']);
            $name=COM_truncate( $name, $_DATABOX_CONF['title_trim_length'] ,"…");
            $url = COM_buildUrl($_CONF['site_url']
                    . '/databox/data.php?id=' . $A['id']);

            $recentnew[] = COM_createLink($name, $url) . LB;
        }

        $retval .= COM_makeList($recentnew, 'list-new-plugins');
    } else {
        $retval .= $LANG_DATABOX['Norecentnew'] . '<br' . XHTML . '>' . LB;
    }


    return $retval;
}

function plugin_user_delete_databox ($uid)
// +---------------------------------------------------------------------------+
// | 機能  所有者（ユーザ）が削除された時の処理
// +---------------------------------------------------------------------------+
{
    global $_TABLES;
    global $_DATABOX_CONF;

    if (DB_count ($_TABLES['DATABOX_base'], 'owner_id', $uid) == 0) {
        return;
    }
	
	//所有者の削除と共に削除する
    if ($_DATABOX_CONF['delete_data'] == 1) {
        $sql="SELECT id FROM ".$_TABLES['DATABOX_base']." WHERE owner_id=".$uid;

        $result = DB_query( $sql );
        $nrows = DB_numRows( $result );
        for( $i = 0; $i < $nrows; $i++ )    {
            $A = DB_fetchArray( $result, true );
            $id=$A['id'];
            DB_delete ($_TABLES['DATABOX_base'], 'id', $id);
            //カテゴリ
            $rt=DATABOX_savedatas("category_id",$_TABLES['DATABOX_category'],$id);
            //追加項目
            $rt=DATABOX_saveaddtiondatas($id);
        }

	//所有者の削除でRootユーザ所有に変更する
    } else {
        $rootgroup = DB_getItem ($_TABLES['groups'], 'grp_id',
                                 "grp_name = 'Root'");
        $sql = "SELECT DISTINCT ug_uid FROM {$_TABLES['group_assignments']} ";
        $sql .= " WHERE ug_main_grp_id = $rootgroup ORDER BY ug_uid LIMIT 1";
        $result = DB_query ($sql);
        $A = DB_fetchArray ($result);
        $rootuser = $A['ug_uid'];

        $sql="UPDATE {$_TABLES['DATABOX_base']} SET owner_id = $rootuser WHERE owner_id = $uid";
        DB_query ($sql);
    }
}

function plugin_getheadercode_databox()
// +---------------------------------------------------------------------------+
// | 機能  ヘッダコード編集：
// | set css 
// +---------------------------------------------------------------------------+
//
{
	global $_SCRIPTS;
	
	$css = '/databox/css/style.css';
	$_SCRIPTS->setCSSFile('databox_style_css', $css, true);

	return;
}


function plugin_enablestatechange_databox ($enable)
// +---------------------------------------------------------------------------+
// | 機能  無効←→有効変更された時の処理
// | フィードの有効無効の変更
// +---------------------------------------------------------------------------+
{
    global $_TABLES;

    $is_enabled = $enable ? 1 : 0;

    // toggle links feeds
    DB_query ("UPDATE {$_TABLES['syndication']} SET is_enabled = $is_enabled WHERE type = 'databox'");
	
}

/**
* Provide URL of a documentation file
*
* @param   string  $file   documentation file being requested, e.g. 'config'
* @return  mixed           URL or false when not available
*/
function plugin_getdocumentationurl_databox(
	$file
) {
	global $_CONF;

	static $doc_url = NULL;

	if ($doc_url === NULL) {
		switch ($file) {
			case 'index':
			case 'config':
				$lang = COM_getLanguageName();
				$path = 'admin/plugins/databox/docs/';
				if (!file_exists($_CONF['path_html'] . $path . $lang . '/databox.html')) {
					$lang = 'japanese';//'english';
				}
		
				$doc_url = $_CONF['site_url'] . '/' . $path . $lang . '/databox.html';
				break;

			default:
				$doc_url = FALSE;
				break;
		}
	}
	
	return $doc_url;
}

/**
* Provides text for a Configuration tooltip
*
* @param    string  $id     Id of config value
* @return   mixed           Text to use regular tooltip, NULL to use config 
*                           tooltip hack, or empty string when not available
*/
function plugin_getconfigtooltip_databox(
	$id
){
	// Use config tooltip hack where tooltip is read from the config documentation
	return NULL;
}


function plugin_getiteminfo_databox(
	$id
	, $what
	, $uid = 0
	, $options = array()
)
// +---------------------------------------------------------------------------+
// | 機能  
// | XMLSitemap id="*" what="url,date-modified" uid=1
// +---------------------------------------------------------------------------+
// | 引数 $id
// | 引数 $what:
// | 引数 $uid:
// | 引数 $options:
// +---------------------------------------------------------------------------+
{
	global $_CONF;
	global $_TABLES;
    global $_DATABOX_CONF;

    // parse $what to see what we need to pull from the database
    $properties = explode(',', $what);
    $fields = array();
    foreach ($properties as $p) {
        switch ($p) {
        case 'date-created':
            $fields[] = 'UNIX_TIMESTAMP(created) AS unixdate_created';
            break;
        case 'date-modified':
            $fields[] = 'UNIX_TIMESTAMP(modified) AS unixdate_modified';
            break;
        case 'description':
        case 'excerpt':
            $fields[] = 'description';
            break;
        case 'id':
            $fields[] = 'id';
            break;
        case 'title':
            $fields[] = 'title';
            break;
		case 'url':
            if ($id == '*') {
				$fields[] = 'id';
				$fields[] = 'code';
			}
			break;
        default:
            // nothing to do
            break;
        }
    }

    $fields = array_unique($fields);
    if (count($fields) == 0) {
        $retval = array();
        return $retval;
    }
    // prepare SQL request
    $where = LB.' WHERE'.LB;
	//下書データはのぞく
	$where .=  " draft_flag=0".LB;
	if ($id == '*') {
    } else {
        $where .= " AND (id = '" . DB_escapeString($id) . "') ".LB;
    }
	//公開日以前のデータはのぞく
	$where .=  " AND (released <= NOW())".LB;
	//公開終了日を過ぎたデータはのぞく
	$where .=  " AND (expired=0 OR expired > NOW())".LB;
 	//アクセス権のないデータ はのぞく
    $permSql = COM_getPermSql('AND', $uid) ;
	//-----
    $sql = "SELECT " . implode(',', $fields).LB
            . " FROM {$_TABLES['DATABOX_base']}" . $where . $permSql;
    if ($id != '*') {
        $sql .= ' LIMIT 1';
    }

    $result = DB_query($sql);
    $numRows = DB_numRows($result);

    $retval = array();
    for ($i = 0; $i < $numRows; $i++) {
        $A = DB_fetchArray($result);

        if (DATABOX_isExcepted($A['code'],$_DATABOX_CONF['sitemap_excepts'])) {
            continue;
        }

		
        $props = array();
        foreach ($properties as $p) {
            switch ($p) {
            case 'date-created':
                $props['date-created'] = $A['unixdate_created'];
                break;
            case 'date-modified':
                $props['date-modified'] = $A['unixdate_modified'];
                break;
            case 'description':
                $props['description'] = trim(PLG_replaceTags(stripslashes($A['description'])));
                break;
            case 'excerpt':
                $props['excerpt'] = trim(PLG_replaceTags(stripslashes($A['description'])));
                break;
            case 'id':
                $props['id'] = $A['id'];
                break;
            case 'title':
                $props['title'] = stripslashes($A['title']);
                break;
            case 'url':
                $url=$_CONF['site_url'] . "/databox/data.php";
                $url.="?";
                if (empty($A['id'])) {
					$url.="id=".$id;
					$url.="&amp;m=id";
			    }elseif ($_DATABOX_CONF['datacode']) {
				    $url.="code=".$A['code'];
                    $url.="&amp;m=code";
			    }else{		
					$url.="id=".$A['id'];
                    $url.="&amp;m=id";
				}
				$props['url'] =COM_buildUrl( $url );
				
                break;
            default:
                // return empty string for unknown properties
                $props[$p] = '';
                break;
            }
        }

        $mapped = array();
        foreach ($props as $key => $value) {
            if ($id == '*') {
                if ($value != '') {
                    $mapped[$key] = $value;
                }
            } else {
                $mapped[] = $value;
            }
        }

        if ($id == '*') {
            $retval[] = $mapped;
        } else {
            $retval = $mapped;
            break;
        }
    }

    if (($id != '*') && (count($retval) == 1)) {
        $retval = $retval[0];
    }

    return $retval;
}


function plugin_configchange_databox(
    $group
    , $changes = array()
)
{
    $cacheInstance = 'databox__';
    CACHE_remove_instance($cacheInstance);

}


// 20110928
//function plugin_user_delete_userbox ($uid)
// 使ってる？
function databox_deletedata ($id)
{
    global $_TABLES;
	
	$pi_name="databox";
	
    $addition_def=DATABOX_getadditiondef($pi_name);//@@@@@
    $additionfields = DATABOX_getadditiondatas($id,$pi_name);
	
	//--
    DB_delete ($_TABLES['DATABOX_base'], 'id', $id);

    //カテゴリ
    $rt=DATABOX_savedatas("category_id",$_TABLES['DATABOX_category'],$id);
	//追加項目
	DATABOX_deleteaddtionfiles($additionfields,$addition_def,$pi_name);
	$rt=DATABOX_saveaddtiondatas($id,"","",$pi_name); 
	//hits
    DB_delete ($_TABLES['DATABOX_stats'], 'id', $id);

}

?>
