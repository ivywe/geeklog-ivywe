<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// |  アトリビュート別件数一覧、アトリビュート項目別一覧
// +---------------------------------------------------------------------------+
// $Id: public_html/userbox/attribute.php
define ('THIS_SCRIPT', 'userbox/attribute.php');
//define ('THIS_SCRIPT', 'userbox/test.php');
define ('NEXT_SCRIPT', 'userbox/profile.php');
//define ('NEXT_SCRIPT', 'userbox/test.php');
//20100820 tsuchitani AT ivywe DOT co DOT jp http://www.ivywe.co.jp/

require_once ('../lib-common.php');
if (!in_array('userbox', $_PLUGINS)) {
    COM_handle404();
    exit;
}

//debug 時 true
$_USERBOX_VERBOSE = false;
//==============================================================================
function fnclist(
	$id
	,$template
)
// +---------------------------------------------------------------------------+
// | 機能  アトリビュート別件数一覧表示
// | 書式
// +---------------------------------------------------------------------------+
// | 引数　$id　アトリビュートid(追加項目id)
// | 引数　$template　使用するテンプレートフォルダの名前
// | 戻値
// +---------------------------------------------------------------------------+
{
    global $_CONF;
    global $_TABLES;
    global $_USER_CONF;
    global $perpage;
    global $LANG_USERBOX;
    global $LANG_USERBOX_ADMIN;
    global $LANG_USERBOX_NOYES;
	
    //-----
    $page = COM_applyFilter($_REQUEST['page'],true);
    if (!isset($page) OR $page == 0) {
        $page = 1;
    }

    $pi_name="userbox";
    $field_def=DATABOX_getadditiondef($pi_name);

    //-----
    $tbl1=$_TABLES['USERBOX_addition'] ;
    $tbl2=$_TABLES['USERBOX_base'] ;
    $tbl3=$_TABLES['USERBOX_def_field'] ;
	$tbl5=$_TABLES['users'] ;
    //-----
    $sql = "SELECT ".LB;

    $sql .= " t1.field_id ".LB;
    $sql .= " ,t1.value ".LB;
    $sql .= " ,t3.name ".LB;
    $sql .= " ,t3.templatesetvar".LB;
    $sql .= " ,t3.description ".LB;
    $sql .= " ,Count(t1.id) AS count".LB;

    $sql .= " FROM ".LB;
    $sql .= " {$tbl1} AS t1 ".LB;
    $sql .= " ,{$tbl2} AS t2 ".LB;
    $sql .= " ,{$tbl3} AS t3 ".LB;
    $sql .= " ,{$tbl5} AS t5 ".LB;

    $sql .= " WHERE ".LB;
    $sql .= " t1.value <>''".LB;
	$sql .= " AND t1.id = t2.id ".LB;
	$sql .= " AND t1.id = t5.uid ".LB;
    $sql .= " AND t1.field_id = t3.field_id ".LB;

    //TYPE[0] = '一行テキストフィールド';
    //TYPE[2] = 'いいえ/はい';
    //TYPE[3] = '日付　（date picker対応）';
    //TYPE[7] = 'オプションリスト';
    //TYPE[8] = 'ラジオボタンリスト';
	//TYPE[9] = 'オプションリスト(マスタ)　（既定リスト）';
	$sql .= " AND t3.type IN (0,2,3,4,7,8,9,16) ".LB;

    //ALLOW_DISPLAY[0] ='表示する（orderに指定可能）';
    //ALLOW_DISPLAY[1] ='ログインユーザのみ表示する';
    if (COM_isAnonUser()){
        $sql .= " AND t3.allow_display=0 ".LB;
    }else{
        $sql .= " AND t3.allow_display IN (0,1) ".LB;
    }

    if ($id<>0){
        $sql .= " AND t1.field_id = ".$id.LB;
    }

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
    $sql .= " t1.field_id , t1.value". LB;

    $sql .= " ORDER BY ".LB;
    $sql .= " t1.field_id,t1.value".LB;

    $result = DB_query ($sql);
    $cnt = DB_numRows ($result);
    $pages = 0;
    if ($perpage > 0) {
        $pages = ceil($cnt / $perpage);
    }
    //ヘッダ、左ブロック
    //@@@@@@ 修正要

    if ($id==0){
        $w=$LANG_USERBOX['attribute_top'];
        $attribute_top=$w;
        $field_top="";
        $col="col.thtml";
    }else{
        $url=$_CONF['site_url']."/userbox/attribute.php";
        $attribute_top=":<a href='".$url."'>".$LANG_USERBOX['attribute_top']."</a>";
        $w=$field_def[$id]['name'].$LANG_USERBOX['countlist'];
        $field_top=$w;
        $col="col2.thtml";
    }
    if ($page > 1) {
        $page_title = sprintf ('%s (%d)', $w, $page);
    } else {
        $page_title = sprintf ('%s ', $w);
    }
	$headercode.=DATABOX_getheadercode(	
		"attribute"
		,$template
		,$pi_name
		,0
		,$_CONF['site_name']
		,$_CONF['meta_description']
		,$_CONF['meta_keywords']
		,$_CONF['meta_description']);
    $retval .= DATABOX_siteHeader($pi_name,'',$page_title,$headercode);
    //

    $tmplfld=DATABOX_templatePath('attribute',$template,$pi_name);
    $templates = new Template($tmplfld);


    $templates->set_file (array (
        'list' => 'list.thtml',
        'nav'   => 'navigation.thtml',
        'row'   => 'row.thtml',
        'col'   => $col,
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


    $templates->set_var ('attribute_top',$attribute_top);
    $templates->set_var ('field_top',$field_top);

    //page
    $offset = ($page - 1) * $perpage;
    $lin1=$offset+1;
    $lin2=$lin1+$perpage - 1;
    if ($lin2>$cnt){
        $lin2=$cnt;
    }
    $templates->set_var ('lang_view', $LANG_USERBOX['view']);
    $templates->set_var ('lin', $lin1."-".($lin2));
    $templates->set_var ('cnt', $cnt);

    //
    $templates->set_var ('lang_name', $LANG_USERBOX_ADMIN['name']);
    $templates->set_var ('lang_count', $LANG_USERBOX['count']);

    $sql .= " LIMIT $offset, $perpage";

    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);

    if ($numrows > 0) {
        for ($i = 0; $i < $numrows; $i++) {
            $A = DB_fetchArray ($result);

            $name=COM_applyFilter($A['name']);
            $description=COM_applyFilter($A['description']);

            $fid=$A["field_id"];
            $value=$A["value"];

            $fieldvalue=DATABOX_getfieldvalue(
                $value
                ,$field_def[$fid]['type']
                ,$field_def[$fid]['selectionary']
				,$LANG_USERBOX_NOYES
				,$field_def[$fid]['selectlist']
				,$pi_name
                );

            $url=$_CONF['site_url'] . "/".THIS_SCRIPT;
            $url.="?";
            $url.="id=".$A['field_id'];
            $url.="&amp;m=id";
            $url2=$url."&value=".$A['value'];

            $url = COM_buildUrl( $url );
            $link= COM_createLink($name, $url);

            $url2 = COM_buildUrl( $url2 );
            $link2= COM_createLink($fieldvalue, $url2);

            $templates->set_var ('field_link', $link);
            $templates->set_var ('value_link', $link2);

            $templates->set_var ('field_description', $description);
            $templates->set_var ('field_name', $name);
            $templates->set_var ('field_url', $url);

            $templates->set_var ('value_url', $url2);
            $templates->set_var ('value', $fieldvalue);


            $templates->set_var ('count', $A['count']);


            //=====
            $templates->parse ('col_var', 'col', true);
            $templates->parse ('row_var', 'row', true);

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
//(各アトリビュート)別件数一覧 の引数の順番
//public_html/attribute.php?id=1&m=id
//public_html/attribute.php?id=xxxx&m=code
//アトリビュート別一覧の引数の順番
//public_html/attribute.php?id=1&m=id&value=27&template=yyyy
//public_html/attribute.php?code=xxxx&m=code&value=27&template=yyyy
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
	COM_setArgNames(array('idcode','m','value','template'));
    $m=COM_applyFilter(COM_getArgument('m'));
    if ($m==="code"){
        $id=0;
        $code=COM_applyFilter(COM_getArgument('idcode'));
    }else{
        $id=COM_applyFilter(COM_getArgument('idcode'),true);
        $code=0;
    }
    $value=COM_applyFilter(COM_getArgument('value'));
    $template=COM_applyFilter(COM_getArgument('template'));
}else{
	$id = COM_applyFilter($_GET['id'],true);
	$code = COM_applyFilter($_GET['code']);
	$value = COM_applyFilter($_GET['value']);
	$template = COM_applyFilter($_GET['template']);
}
$page = COM_applyFilter($_GET['page'],true);
$perpage = COM_applyFilter($_GET['perpage'],true);
$order = COM_applyFilter($_GET['order']);

//@@@@@@!!!
if ($id===0){
    if ($code<>""){
        $id=DATABOX_codetoid(
            $code,'USERBOX_def_field',"field_id","templatesetvar");
    }
}
if ($perpage===0){
    $perpage=$_USERBOX_CONF['perpage']; // 1ページの行数 @@@@@
}

//ログイン要否チェック
if (COM_isAnonUser()){
	// Geeklogすべてにログインを必要とする=0 はい
	// または UserBox ログインを要求する=3 はい
	// または UserBox ログインを要求する=2 一覧と詳細　
    if  ($_CONF['loginrequired']
            OR ($_USERBOX_CONF['loginrequired'] == 3)
            OR ($_USERBOX_CONF['loginrequired'] == 2 AND $value<>"") ){
        $display .= DATABOX_siteHeader($pi_name,'',$page_title);
        $display .= SEC_loginRequiredForm();
        $display .= DATABOX_siteFooter($pi_name);
        COM_output($display);
        exit;
    }
}

//(各アトリビュート)別件数一覧
if ($value==="") { //一覧
    $display .= fnclist($id,$template);
//アトリビュート別一覧
}else{
    $display .= userbox_field(
        "notautotag"
        ,$id
        ,$value
        ,$template
        ,"yes"
        ,$perpage
        ,$page
        ,$order
        ,$code
        );
}

$display .= DATABOX_siteFooter($pi_name);

//---

COM_output($display);

?>