<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | list.php データ一覧
// +---------------------------------------------------------------------------+
// $Id: list.php
// public_html/databox/list.php
// 2012/11/29 tsuchitani AT ivywe DOT co DOT jp data.php より分離

define ('THIS_SCRIPT', 'databox/list.php');
//define ('THIS_SCRIPT', 'databox/test.php');

require_once('../lib-common.php');
if (!in_array('databox', $_PLUGINS)) {
    COM_handle404();
    exit;
}

//debug 時 true
$_DATABOX_VERBOSE = false;

//ログイン要否チェック
if (COM_isAnonUser()){
    if  ($_CONF['loginrequired']
            OR ($_DATABOX_CONF['loginrequired'] == 3)
            OR ($_DATABOX_CONF['loginrequired'] == 2)
            ){
        $display .= DATABOX_siteHeader($pi_name,'',$page_title);
        $display .= SEC_loginRequiredForm();
        $display .= DATABOX_siteFooter($pi_name);
        COM_output($display);
        exit;
    }

}

function fncList(
)
// +---------------------------------------------------------------------------+
// | 機能  一覧表示                                                            |
// | 書式 fncList()                                                            |
// +---------------------------------------------------------------------------+\
// | 戻値 nomal:一覧                                                           |
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $_TABLES;
    global $LANG_ADMIN;
    global $LANG09;

    global $_DATABOX_CONF;
    global $LANG_DATABOX_ADMIN;
    global $LANG_DATABOX;

    require_once( $_CONF['path_system'] . 'lib-admin.php' );

    $retval = '';
	$retval .= COM_startBlock($LANG_DATABOX['list']);
	
    //MENU1:管理画面
    $menu_arr = array ();

    if ($_DATABOX_CONF['hide_whatsnew'] == 'hide') {
        $datecolumn = 'created';
    } else {
        $datecolumn = $_DATABOX_CONF['hide_whatsnew'];
    }

    //ヘッダ：編集～
    $header_arr[] = array('text' => $LANG_DATABOX_ADMIN['orderno'], 'field' => 'orderno', 'sort' => true);
    if ($_DATABOX_CONF['datacode']){
        $header_arr[] = array('text' => $LANG_DATABOX_ADMIN['code'], 'field' => 'code', 'sort' => true);
    }else{
        $header_arr[] = array('text' => $LANG_DATABOX_ADMIN['id'], 'field' => 'id', 'sort' => true);
    }

	$header_arr[] = array('text' => $LANG_DATABOX_ADMIN['title'], 'field' => 'title', 'sort' => true);
    $header_arr[]=array('text' => $LANG_DATABOX_ADMIN['remaingdays'], 'field' => 'remaingdays', 'sort' => true);
    $header_arr[]=array('text' => $LANG_DATABOX_ADMIN[$datecolumn], 'field' => $datecolumn, 'sort' => true);
    //
	$text_arr = array('has_menu' =>  true,
      'has_extras'   => true,
      'form_url' => $form_url);
	$tet_arr['has_menu']=true;
	$tet_arr['has_extras']=true;
	$tet_arr['form_url']=$_CONF['site_url'] . "/".THIS_SCRIPT;


    $sql = "SELECT ";
    $sql .= " id";
    $sql .= " ,title";
    $sql .= " ,code";
    $sql .= " ,draft_flag";
    $sql .= " ,UNIX_TIMESTAMP(udatetime) AS udatetime";
    $sql .= " ,orderno";
    $sql .= " ,UNIX_TIMESTAMP(".$datecolumn.") AS ".$datecolumn;
	
	$sql .= " ,(SELECT DATEDIFF(expired , NOW()) ";
	$sql .= " FROM {$_TABLES['DATABOX_base']} AS t3  ";
	$sql .= " where   t.id=t3.id AND DATEDIFF(expired , NOW())>0)";
    $sql .= "	+ 1 AS remaingdays";

    $sql .= " FROM ";
    $sql .= " {$_TABLES['DATABOX_base']} AS t";
    $sql .= " WHERE ";
    $sql .= " 1=1";
	
	$sql .= COM_getLangSQL ('code', 'AND', 't').LB;
	
    //管理者の時,下書データも含む
    //if ( SEC_hasRights('databox.admin')) {
    //}else{
       $sql .= " AND draft_flag=0".LB;
    //}
    //アクセス権のないデータ はのぞく
    $sql .= COM_getPermSql('AND');
    //公開日以前のデータはのぞく
    $sql .= " AND (released <= NOW())";

    //公開終了日を過ぎたデータはのぞく
    $sql .= " AND (expired=0 OR expired > NOW())";
    //

    $query_arr = array(
        'table' => 'DATABOX_base',
        'sql' => $sql,
        'query_fields' => array('orderno','id','title','code','draft_flag'),
        'default_filter' => $exclude);
    //デフォルトソート項目:
    $defsort_arr = array('field' => 'orderno', 'direction' => 'ASC');
    //List 取得
    //ADMIN_list($component, $fieldfunction, $header_arr, $text_arr,
    //       $query_arr, $menu_arr, $defsort_arr, $filter = '', $extra = '', $options = '')
    $retval .= ADMIN_list(
        'databox'
        , "fncGetListField"
        , $header_arr
        , $text_arr
        , $query_arr
        , $defsort_arr
	);
	
    $retval .= COM_endBlock();

    return $retval;
}

function fncGetListField(
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
    global $_DATABOX_CONF;

    $retval = '';

    switch($fieldname) {
        //名
        case 'title':
            $name=COM_stripslashes($A['title']);
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
            $retval= COM_createLink($name, $url);
            break;
		case 'udatetime':
		case 'modified':
		case 'created':
		case 'released':
			$curtime = COM_getUserDateTimeFormat($A['{$fieldname}']);
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
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
//############################
$pi_name    = 'databox';
//############################

// 引数
//order=2&prevorder=orderno&direction=ASC&databoxlistpage=2&q=hotel&query_limit=50

$msg = '';
if (isset ($_REQUEST['msg'])) {
    $msg = COM_applyFilter ($_REQUEST['msg'], true);
}

$display = '';
$information = array();
$information['pagetitle']=$LANG_DATABOX['data'];
$layout=$_DATABOX_CONF['layout'];
if (isset ($msg)) {
    $display .= COM_showMessage ($msg,$pi_name);
}
$display .= fncList($languageid);
$display=DATABOX_displaypage($pi_name,$layout,$display,$information);
COM_output($display);

?>
