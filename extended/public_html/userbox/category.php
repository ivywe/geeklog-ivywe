<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// |  グループ別カテゴリ別件数一覧、カテゴリ別一覧
// +---------------------------------------------------------------------------+
// $Id: public_html/userbox/category.php
define ('THIS_SCRIPT', 'userbox/category.php');
//define ('THIS_SCRIPT', 'userbox/category_test.php');

define ('NEXT_SCRIPT', 'userbox/profile.php');
//define ('THIS_SCRIPT', 'userbox/test.php');
//20110927 tsuchitani AT ivywe DOT co DOT jp http://www.ivywe.co.jp/

require_once ('../lib-common.php');
if (!in_array('userbox', $_PLUGINS)) {
    COM_handle404();
    exit;
}

$perpage=$_USERBOX_CONF['perpage']; // 1ページの行数 @@@@@

//debug 時 true
$_USERBOX_VERBOSE = false;
//$_USERBOX_VERBOSE = true;

//==============================================================================

function fnclist(
	$pi_name
	,$template
    ,$group_id
	,$perpage
	,$page
	,$order
	,$gcode
)
// +---------------------------------------------------------------------------+
// | 機能  グループ別カテゴリ別件数一覧表示
// | 書式
// +---------------------------------------------------------------------------+
// | 引数　$template　使用するテンプレートフォルダの名前
// | 戻値
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $_TABLES;
    global $_USERBOX_CONF;
    global $perpage;
    global $LANG_USERBOX;
    global $LANG_USERBOX_ADMIN;
	
    //-----
	if ($group_id===""){
		if ($gcode<>""){
			$group_id=DATABOX_codetoid(
			$gcode,'DATABOX_def_group',"group_id");
		}
	}
	
    //-----
    if ($page == 0) {
        $page = 1;
    }

    //-----
    $tbl1=$_TABLES['USERBOX_category'] ;
    $tbl2=$_TABLES['USERBOX_base'] ;
    $tbl3=$_TABLES['USERBOX_def_category'] ;
    $tbl4=$_TABLES['USERBOX_def_group'] ;//@@@@@
	$tbl5=$_TABLES['users'] ;

    //-----
    $sql = "SELECT ".LB;

    $sql .= " t1.category_id ".LB;
    $sql .= " ,t3.name ".LB;
    $sql .= " ,t3.code ".LB;
    $sql .= " ,t3.description ".LB;
    $sql .= " ,Count(t1.id) AS count".LB;
    $sql .= " ,t4.name AS group_name ".LB;
    $sql .= " ,t4.group_id ".LB;
    $sql .= " ,t4.code AS group_code ".LB;

    $sql .= " FROM ".LB;
    $sql .= " {$tbl1} AS t1 ".LB;
    $sql .= " ,{$tbl2} AS t2 ".LB;
    $sql .= " ,{$tbl3} AS t3 ".LB;
    $sql .= " ,{$tbl4} AS t4 ".LB;
    $sql .= " ,{$tbl5} AS t5 ".LB;

    $sql .= " WHERE ".LB;
	$sql .= " t1.id = t2.id ".LB;
	$sql .= " AND t1.id = t5.uid ".LB;
	
    $sql .= " AND t1.category_id = t3.category_id ".LB;
	if ($group_id<>""){
		$sql .= " AND t3.categorygroup_id = ".$group_id.LB;
	}
	$sql .= " AND t3.categorygroup_id = t4.group_id ".LB;
    //管理者の時,下書データも含む
    //if ( SEC_hasRights('userbox.admin')) {
    //}else{
       $sql .= " AND t2.draft_flag=0".LB;
    //}
    //アクセス権のないデータ はのぞく
    $sql .= COM_getPermSql('AND',0,2,"t2").LB;
    //公開日以前のデータはのぞく
    $sql .= " AND (released <= NOW())".LB;

    //公開終了日を過ぎたデータはのぞく
    $sql .= " AND (expired=0 OR expired > NOW())".LB;

    $sql .= " GROUP BY ".LB;
    $sql .= " t1.category_id".LB;

    $sql .= " ORDER BY ".LB;
    $sql .= " t4.orderno,t3.orderno".LB;


    $result = DB_query ($sql);
    $cnt = DB_numRows ($result);
	
    $pages = 0;
    if ($perpage > 0) {
        $pages = ceil($cnt / $perpage);
    }
    //ヘッダ、左ブロック
    if ($page > 1) {
        $page_title = sprintf ('%s (%d)', $LANG_USERBOX['category_top'], $page);
    } else {
        $page_title = sprintf ('%s ', $LANG_USERBOX['category_top']);
    }
    $headercode="<title>".$_CONF['site_name']." - ".$page_title ."</title>";
	// Meta Tags
	$headercode.=DATABOX_getheadercode(	
		"category"
		,$template
		,$pi_name
		,0
		,$_CONF['site_name']
		,$_CONF['meta_description']
		,$_CONF['smeta_keywords']
		,$_CONF['meta_description']);
    $retval .= DATABOX_siteHeader($pi_name,'',$page_title,$headercode) ;

    $tmplfld=DATABOX_templatePath('category',$template,$pi_name);
    $templates = new Template($tmplfld);
    $templates->set_file (array (
        'list' => 'list.thtml',
        'nav'   => 'navigation.thtml',
        'row'   => 'row.thtml',
        'col'   => "col.thtml",
        'grp'   => "grp.thtml",
        'pagenav'  => 'pagenavigation.thtml'
        ));

	$languageid=COM_getLanguageId();
	$language= COM_getLanguage();
    $templates->set_var ('languageid', $languageid);
    $templates->set_var ('language', $language);
	if ($languageid<>"") {
		$templates->set_var ('_languageid', "_".$languageid);
	}else{
		$templates->set_var ('_languageid', "");
	}

    //
    $templates->set_var ('site_url',$_CONF['site_url']);
    $templates->set_var ('this_script',THIS_SCRIPT);

    $templates->set_var ('home',$LANG_USERBOX['home']);
	if ($group_id<>""){
		$group_name=DB_getItem( $tbl4, 'name',"group_id = ".$group_id);
		$templates->set_var ('lang_category_list_h2',$group_name.$LANG_USERBOX['category_top']);
	}else{
		$templates->set_var ('lang_category_list_h2',$LANG_USERBOX['category_top']);
	}

    //page
    $offset = ($page - 1) * $perpage;
    $sql .= " LIMIT $offset, $perpage";
    $lin1=$offset+1;
    $lin2=$lin1+$perpage - 1;
    if ($lin2>$cnt){
        $lin2=$cnt;
    }
    $templates->set_var ('lang_view', $LANG_USERBOX['view']);
    $templates->set_var ('lin', $lin1."-".($lin2));
    $templates->set_var ('cnt', $cnt);

    $templates->set_var ('lang_name', $LANG_USERBOX_ADMIN['name']);
    $templates->set_var ('lang_count', $LANG_USERBOX['count']);

    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);
	$old_group_name="";
    if ($numrows > 0) {
        for ($i = 0; $i < $numrows; $i++) {
            $A = DB_fetchArray ($result);
			
			$group_name=COM_applyFilter($A['group_name']);

            $name=COM_applyFilter($A['name']);
            $description=COM_applyFilter($A['description']);
            $url=$_CONF['site_url'] . "/".THIS_SCRIPT;
            $url.="?";
            //コード使用の時
            if ($_USERBOX_CONF['categorycode']){
                $url.="code=".$A['code'];
                $url.="&amp;m=code";
            }else{
                $url.="id=".$A['category_id'];
                $url.="&amp;m=id";
            }
            $url = COM_buildUrl( $url );
            $link= COM_createLink($name, $url);
            $templates->set_var ('category_link', $link);
            $templates->set_var ('category_name', $name);
            $templates->set_var ('category_description', $description);
            $templates->set_var ('category_url', $url);
            $templates->set_var ('count', $A['count']);
            $templates->set_var ('category_id', $A['category_id']);
            $templates->set_var ('category_code', $A['code']);

            //=====
			if ($old_group_name<>$group_name){
				$url=$_CONF['site_url'] . "/".THIS_SCRIPT;
				$url.="?";
				//コード使用の時
				if ($_DATABOX_CONF['groupcode']){
					$url.="gcode=".$A['group_code'];//@@@@@
					$url.="&amp;m=gcode";
				}else{
					$url.="gid=".$A['group_id'];//@@@@@
					$url.="&amp;m=gid";
				}
				$url = COM_buildUrl( $url );
				$link= COM_createLink($group_name, $url);
				$templates->set_var ('group_link', $link);
				$templates->set_var ('group_name', $group_name);
				$templates->parse ('grp_var', 'grp', true);
				$old_group_name=$group_name;
			}
			$templates->parse ('col_var', 'col', true);
            $templates->parse ('row_var', 'row', true);

            $templates->set_var ('grp_var', '');
            $templates->set_var ('col_var', '');

        }
        // Call to plugins to set template variables in the databox
        PLG_templateSetVars( 'userbox', $templates );
        //ページなび
        //$url = $_CONF['site_url']  . '/'.THIS_SCRIPT."?m=".$m;//."?order=$order";
        $url = $_CONF['site_url']  . '/'.THIS_SCRIPT;

        $templates->set_var ('page_navigation',
                  COM_printPageNavigation ($url, $page, $pages));
        //------------
        $templates->parse ('nav_var', 'nav', true);

        $templates->set_var ('blockfooter',COM_endBlock());

        $templates->set_var ('msg', "");

        $templates->parse ('output', 'list');

        $school_content = $templates->finish ($templates->get_var ('output'));
        $retval .=$school_content;

    }else{
        $templates->set_var ('msg', $LANG_USERBOX["nohit"]);
        $templates->parse ('output', 'list');
        $content = $templates->finish ($templates->get_var ('output'));
        $retval .=$content;
    }

    $retval =PLG_replacetags ($retval);

    return $retval;
}



// +---------------------------------------------------------------------------+
// MAIN
// +---------------------------------------------------------------------------+
//############################
$pi_name    = 'userbox';
//############################
$display = '';
$page_title=$LANG_USERBOX_ADMIN['piname'];

//引数
//グループ別)カテゴリ別件数一覧　の引数の順番
//public_html/category.php?gid=1&m=gid
//public_html/category.php?gcode=country&m=gcode
//カテゴリ別一覧の引数の順番
//public_html/category.php?id=1&m=id
//public_html/category.php?code=city&m=code
$url_rewrite = false;
$q = false;
$url = $_SERVER["REQUEST_URI"];
if ($_CONF['url_rewrite']) {
    $q = strpos($url, '?');
    if ($q === false) {
        $url_rewrite = true;
    }elseif (substr($url, $q - 4, 4) != '.php') {
        $url_rewrite = true;
    }
}
//
if ($url_rewrite){
    COM_setArgNames(array('idcode','m','template'));
    $m=COM_applyFilter(COM_getArgument('m'));
    if ($m==="code"){
        $id=0;
        $code=COM_applyFilter(COM_getArgument('idcode'));
		$gid="";
		$gcode="";
	}else if ($m==="id"){
        $id=COM_applyFilter(COM_getArgument('idcode'),true);
        $code="";
		$gid="";
		$gcode="";
    }else if ($m==="gcode"){
        $gid="";
        $gcode=COM_applyFilter(COM_getArgument('idcode'));
		$id=0;
		$code="";
    }else if ($m==="gid"){
        $gid=COM_applyFilter(COM_getArgument('idcode'),true);
        $gcode="";
		$id=0;
		$code="";
	}else{
        $gid=0;
        $gcode="";
		$id=0;
		$code="";
    }
    $template=COM_applyFilter(COM_getArgument('template'));
}else{
    $gid = COM_applyFilter($_GET['gid']);
    $gcode = COM_applyFilter($_GET['gcode']);
    $id = COM_applyFilter($_GET['id'],true);
    $code = COM_applyFilter($_GET['code']);
    $template = COM_applyFilter($_GET['template']);
}
$page = COM_applyFilter($_GET['page'],true);
$perpage = COM_applyFilter($_GET['perpage'],true);
$order = COM_applyFilter($_GET['order']);
$expired = COM_applyFilter($_GET['expired']);
if ($perpage===0){
    $perpage=$_USERBOX_CONF['perpage']; // 1ページの行数 @@@@@
}
//ログイン要否チェック
if (COM_isAnonUser()){
    if  ($_CONF['loginrequired']
            OR ($_USERBOX_CONF['loginrequired'] == 3)
			OR ($_USERBOX_CONF['loginrequired'] == 2 AND $id>0	) 
			OR ($_USERBOX_CONF['loginrequired'] == 2 AND $code<>""	) 
			){
        $display .= DATABOX_siteHeader($pi_name,'',$page_title);
        $display .= SEC_loginRequiredForm();
        $display .= DATABOX_siteFooter($pi_name);
        COM_output($display);
        exit;
    }
}
if ($id===0) { //一覧
	if ($code<>""){
		$display .= userbox_category(
			"notautotag"
			,$id
			,$template
			,"yes"
			,$perpage
			,$page
			,$order
			,$code
			,$mode
			,$expired
			);
	}else{
		$display .= fnclist(
			$pi_name
			,$template
			,$gid
			,$perpage
			,$page
			,$order
			,$gcode);
	}
}else{//詳細
	$display .= userbox_category(
		"notautotag"
		,$id
		,$template
		,"yes"
		,$perpage
		,$page
		,$order
		,$code
		,$mode
		,$expired
		);
}

$display .= DATABOX_siteFooter($pi_name);

//---

COM_output($display);

?>