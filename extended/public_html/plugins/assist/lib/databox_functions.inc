<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | DataBox プラグイン   function.inc からrequire
// | Box シリーズ共通
// +---------------------------------------------------------------------------+
// $Id: plugins/databox/functions_databox.inc
//20100818 tsuchitani AT ivywe DOT co DOT jp http://www.ivywe.co.jp/
//update 20101222


if (strpos ($_SERVER['PHP_SELF'], 'databox_functions.inc') !== false) {
    die ('This file can not be used on its own.');
}


// +---------------------------------------------------------------------------+
// | 機能 改行をBRに変換
// | 書式 DATABOX	_nl2br ($text)
// +---------------------------------------------------------------------------+
// | 引数 $text:文字列
// +---------------------------------------------------------------------------+
// | 戻値 nomal:改行をBRに変換後の文字列
// +---------------------------------------------------------------------------+
// copy20100803 from  "hiroron - 改行をBRに変換"
function DATABOX_nl2br (
    $text
) {
    return PLG_replacetags ( str_replace(array("\r\n","\r","\n"), '<br'.XHTML.'>', $text) );

}
// +---------------------------------------------------------------------------+
// | 機能 urlをリンク変換
// | 書式 DATABOX_autolink($text)                                              |
// +---------------------------------------------------------------------------+
// | 引数 $text         :文字列                                                |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:urlをリンク変換後の文字列                                       |
// +---------------------------------------------------------------------------+
// copy20100803 from  "hiroron - 自動リンクを
function DATABOX_autolink (
    $text
) {
    $patterns = array("/(https?|ftp)(:\/\/[[:alnum:]\+\$\;\?\.%,!#~*\/:@&=_-]+)/i");
    $replacements = array("<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>");
    return preg_replace($patterns, $replacements, $text);
}

// +---------------------------------------------------------------------------+
// | 機能  コードをidに変換
// | 書式 DATABOX_codetoid($code,'DATABOX_base')
// | 書式 DATABOX_codetoid($code,'users',"uid","username")
// +---------------------------------------------------------------------------+
// | 引数 $code:コード
// | 引数 $table
// | 引数 $idname
// | 引数 $codename
// +---------------------------------------------------------------------------+
// | 戻値 nomal:id
// +---------------------------------------------------------------------------+
//update20101125
function DATABOX_codetoid(
    $code
    ,$table
    ,$idname = "id"
    ,$codename = "code"
)
{
    global $_TABLES;
    $retval=DB_getItem( $_TABLES[$table]   ,$idname,$codename."='{$code}'");
    if (empty($retval)){
        $retval=0;
    }
    return $retval;
}


// +---------------------------------------------------------------------------+
// | 機能  テンプレートフォルダパスを取得
// | 書式 DATABOX_templatePath('data','default','databox')
// +---------------------------------------------------------------------------+
// | 引数 $kind:'data' 'category' ''newlist' 'admin'
// | 引数 $template:  'default'
// | 引数 $pi_name: 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 :テンプレートフォルダパス
// +---------------------------------------------------------------------------+
// 20101115
function DATABOX_templatePath (
    $kind = 'data'
    ,$template ='default'
    ,$pi_name= 'databox'
    )
{

    global $_CONF;

    if (is_null($template) OR ($template==="")){
        $template="default";
    }

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;


    if ($kind==="admin"){
        $conf_templates=$box_conf["templates_admin"];
    }else{
        $conf_templates=$box_conf["templates"];
    }

    //"standard";//標準テンプレートを使用する
    //"custom";//カスタムテンプレートを使用する
    //"theme";//テーマテンプレートを使用する
    if  ($conf_templates==="theme"){

        $tmplfld=$_CONF['path_layout'] .$box_conf['themespath'].$kind;
        if ($kind<>"admin"){

            $tmplfld.="/".$template;
        }
        if (is_dir($tmplfld)) {

        }else{

            $tmplfld=$_CONF['path'] .'plugins/'.$pi_name.'/templates/'.$kind;
            if ($kind<>"admin"){
                $tmplfld.="/default";
            }
        }
    }else if  ($conf_templates==="custom"){

        $tmplfld=$_CONF['path'] .'plugins/'.$pi_name.'/custom/templates/'.$kind;

        if ($kind<>"admin"){

            $tmplfld.="/".$template;

        }

        if (is_dir($tmplfld)){

        }else{

            $tmplfld=$_CONF['path'] .'plugins/'.$pi_name.'/templates/'.$kind;
            if ($kind<>"admin"){

                $tmplfld.="/default";

            }
        }
    }else{

        $tmplfld=$_CONF['path'] .'plugins/'.$pi_name.'/templates/'.$kind;
        if ($kind<>"admin"){

            $tmplfld.="/default";
        }
    }


    return $tmplfld;
}

// +---------------------------------------------------------------------------+
// | 機能  ヘッダを編集
// | 書式 DATABOX_siteHeader($pi_name,'_admin',$page_title,$headercode)
// | 書式 DATABOX_siteHeader($pi_name,'_admin',$page_title)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name: 'databox' 'userbox' 'formbox'
// | 引数 $kind:'' '_admin'
// | 引数 $pagetitle:
// | 引数 $headercode:
// +---------------------------------------------------------------------------+
// | 戻値 :ヘッダ
// +---------------------------------------------------------------------------+
// 20110119
function DATABOX_siteHeader (
    $pi_name= 'databox'
    ,$kind = ''
    ,$page_title = ''
    ,$headercode = ''
    )
{
    global $_CONF;

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;


    $display="";

    $layout=$box_conf['layout'.$kind];
    switch ($layout) {
        // ヘッダ・フッタ・左ブロックあり（右ブロックはテーマ設定による）
        case 'standard':
        //ヘッダ・フッタ・左右ブロックあり
        case 'leftrightblocks':
        //ヘッダ・フッタ・左ブロックあり（右ブロックなし）
        case 'leftblocks':
            $display=COM_siteHeader ('menu', $page_title,$headercode);
            break;
        //ヘッダ・フッタ・右ブロックあり（左ブロックなし）
        case 'rightblocks':
        //ヘッダ・フッタあり（ブロックなし）
        case 'noblocks':
            $display=COM_siteHeader ('none', $page_title,$headercode);
            break;

        //全画面表示（ヘッダ・フッタ・ブロックなし）
        case 'blankpage':
            break;

        default:

    }



    return $display;
}
// +---------------------------------------------------------------------------+
// | 機能  フッタを編集
// | 書式 DATABOX_siteFooter($pi_name,'_admin',$custom)
// | 書式 DATABOX_siteFooter($pi_name)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name: 'databox' 'userbox' 'formbox'
// | 引数 $kind:'' '_admin'
// | 引数 $custom:
// +---------------------------------------------------------------------------+
// | 戻値 : フッタ
// +---------------------------------------------------------------------------+
// 20110119
function DATABOX_siteFooter (
    $pi_name= 'databox'
    ,$kind = ''
    ,$custom=''
    )
{
    global $_CONF;

    if (is_null($pi_name)){
        $pi_name="databox";
    }

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    $display="";

    $layout=$box_conf['layout'.$kind];

    switch ($layout) {
        // ヘッダ・フッタ・左ブロックあり（右ブロックはテーマ設定による）
        case 'standard':
            $display .= COM_siteFooter(false,$custom);
            break;
        //ヘッダ・フッタ・左右ブロックあり
        case 'leftrightblocks':
        //ヘッダ・フッタ・右ブロックあり（左ブロックなし）
        case 'rightblocks':
            $display .= COM_siteFooter(true,$custom);
            break;
        //ヘッダ・フッタ・左ブロックあり（右ブロックなし）
        case 'leftblocks':
        //ヘッダ・フッタあり（ブロックなし）
        // 右ブロックを常時表示する設定であっても右ブロックを表示させない
        case 'noblocks':
            $_CONF['show_right_blocks']=0;
            $_CONF['left_blocks_in_footer']=0;
            $display .= COM_siteFooter(false,$custom);
            break;

        //全画面表示（ヘッダ・フッタ・ブロックなし）
        case 'blankpage':
            break;

        default:

    }



    return $display;
}


// +---------------------------------------------------------------------------+
// | 機能  チェックリスト作成
// | 書式 DATABOX_getcheckList($kind,$selected = "")
// +---------------------------------------------------------------------------+
// | 引数 $kind:種類
// | 引数 $selected:選択値　省略時
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox' 省略時  DATABOX
// +---------------------------------------------------------------------------+
// | 戻値 nomal:チェックリスト
// +---------------------------------------------------------------------------+
//20101116
function DATABOX_getcheckList(
    $kind
    ,$selected = ""
    ,$pi_name=""
)
{
global $_TABLES;

    if ($pi_name==""){
        $table_def_category=$_TABLES['DATABOX_def_category'];
        $table_def_group=$_TABLES['DATABOX_def_group'];
        $table_mst=$_TABLES['DATABOX_mst'];
    }else{
        $table_def_category=$_TABLES[strtoupper($pi_name).'_def_category'];
        $table_def_group=$_TABLES[strtoupper($pi_name).'_def_group'];
        $table_mst=$_TABLES[strtoupper($pi_name).'_mst'];
    }

    $retval = '';

    if ($kind="category"){
        $sql = "SELECT ";
        $sql .= " m1.category_id".LB;
        $sql .= ",m1.name".LB;
        $sql .= ",m1.orderno".LB;
        $sql .= ",m2.group_id".LB;
        $sql .= ",m2.name AS group_name".LB;
        $sql .= " FROM {$table_def_category} AS m1".LB;
        $sql .= " , {$table_def_group} AS m2".LB;
        $sql .= " WHERE ".LB;
        $sql .= " m2.group_id = m1.categorygroup_id".LB;
        $sql .= " AND m2.parent_flg = 0".LB;
        $sql .= " ORDER BY m2.group_id,m2.orderno, m1.orderno".LB;
    }else{
        $sql = "SELECT ".LB;
        $sql .= " no".LB;
        $sql .= ",value".LB;
        $sql .= ",orderno".LB;
        $sql .= ",'1' AS group_id".LB;
        $sql .= " FROM {$table_mst}".LB;
        $sql .= " WHERE kind='{$kind}'".LB;
        $sql .= " ORDER BY orderno".LB;
    }
    $result = DB_query( $sql );
    $nrows = DB_numRows( $result );

    if (is_array($selected)){
            $S = $selected;
    }else{
        if( !empty( $selected ))    {
            $S = explode( ' ', $selected );
        }else {
            $S = array();
        }
    }
    $old_group_id="";
    for( $i = 0; $i < $nrows; $i++ )    {

        $A = DB_fetchArray( $result, true );
        if ($kind="category"){
            if ($old_group_id<>$A['group_id']){
                $retval .="<div id=\"group\">";
                $retval .=$A['group_name'];
                $retval .="</div>";
                $old_group_id=$A['group_id'];
            }
        }
        $retval .="<nobr>";
        $retval .= '<input type="checkbox" name="' . $kind . '[]" value="' . $A[0] ;
        $retval .= '"';
        $retval .= ' class="clcr" ';
        for( $x = 0; $x < sizeof( $S ); $x++ )            {
            if( $A[0] == $S[$x] )                {
                $retval .= ' checked="checked"';
            }
        }
        $retval .= '>' . stripslashes( $A[1] ) . '</nobr>' . LB;
    }

    return $retval;
}

// +---------------------------------------------------------------------------+
// | 機能  複数データ読込                                                      |
// | 書式 DATABOX_getdatas($fld,$tbl,$where)                                   |
// | $rt=DATABOX_getdatas(                                                     |
// |      "category_id",$_TABLES['DATABOX_category'],"id=$id");                |
// +---------------------------------------------------------------------------+
// | 引数 $fld:項目
// | 引数 $tbl:テーブル
// | 引数 $where:条件式
// +---------------------------------------------------------------------------+
// | 戻値 nomal:複数データリスト
// +---------------------------------------------------------------------------+
//update20100420
function DATABOX_getdatas(
    $fld
    ,$tbl
    ,$where
)
{

    $sql="SELECT {$fld} FROM {$tbl} WHERE {$where}";
    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);

    $selected="";
    for ($i = 0; $i < $numrows; $i++) {
        $A = DB_fetchArray ($result);
        $selected.= $A[$fld]." ";
    }
    $selcted=rtrim($selected," ");
    return $selected;

}


// +---------------------------------------------------------------------------+
// | 機能  複数データ書込
// | 書式 DATABOX_savedatas($fld,$tbl,$id,$fldary)
// +---------------------------------------------------------------------------+
// | 引数 $fld:項目
// | 引数 $tbl:テーブル
// | 引数 $id:
// | 引数 $fldary:データ配列
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
//update20100420
function DATABOX_savedatas(
    $fld
    ,$tbl
    ,$id
    ,$fldary=""
)
{

    global $_TABLES;

    $rt=DB_delete($tbl,"id",$id);

    if (is_array($fldary)){
        for ($i = 1; $i <= count($fldary); $i++) {
            $val=current($fldary);
            $sql="INSERT INTO {$tbl} (";
            $sql.="id, {$fld}";
            $sql.=") VALUES (";
            $sql.="{$id}, {$val}";
            $sql.=")";
            DB_query($sql);
            next($fldary);
        }
    }

    return ;

}
// +---------------------------------------------------------------------------+
// | 機能  複数データ書込(カテゴリ)
// | 書式 DATABOX_savecategorydatas($fld,$tbl,$id,$fldary)
// +---------------------------------------------------------------------------+
// | 引数 $id:
// | 引数 $fldary:データ配列
// | 引数 $pi_name: 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
//update20100824
function DATABOX_savecategorydatas(
    $id
    ,$fldary
    ,$pi_name=""
)
{

    global $_TABLES;

    if ($pi_name==""){
        $table_def_category=$_TABLES['DATABOX_def_category'];
        $table_category=$_TABLES['DATABOX_category'];
    }else{
        $table_def_category=$_TABLES[strtoupper($pi_name).'_def_category'];
        $table_category=$_TABLES[strtoupper($pi_name).'_category'];
    }

    $rt=DB_delete($table_category,"id",$id);

    if (is_array($fldary)){
        for ($i = 1; $i <= count($fldary); $i++) {
            $val=current($fldary);
            $sql="INSERT INTO {$table_category} (".LB;
            $sql.="id, category_id".LB;
            $sql.=") VALUES (".LB;
            $sql.="{$id}, {$val}".LB;
            $sql.=")".LB;
            DB_query($sql);
            /////parent_id が0になるまでinsert
            $parent_id=DB_getItem( $table_def_category   ,"parent_id","category_id={$val}");
            while ($parent_id<>0) {
                $category_id=$parent_id;
                $seq=DB_getItem( $table_category   ,"seq","id={$id} AND category_id={$category_id} ");
                if (is_null($seq)) {
                    $sql="INSERT INTO {$table_category} (".LB;
                    $sql.="id, category_id".LB;
                    $sql.=") VALUES (".LB;
                    $sql.="{$id}, {$category_id}".LB;
                    $sql.=")".LB;
                    DB_query($sql);
                }
                $parent_id=DB_getItem( $table_def_category   ,"parent_id","category_id={$category_id}");
            }
            /////
            next($fldary);
        }
    }

    return ;

}
//==============================================================================
// +---------------------------------------------------------------------------+
// | 機能  ユーザチェック
// | 書式 $chk_user=DATABOX_chkuser($group_id,$owner_id,"databox.admin")
// | $group_id = stripslashes($A['group_id']);
// | $owner_id = stripslashes($A['owner_id']);
// | $chk_user=DATABOX_chkuser($group_id,$owner_id,"databox.admin");
// +---------------------------------------------------------------------------+
// | 引数 $group_id
// | 引数 $owner_id
// | 引数 $adminrights　databox.admin
// +---------------------------------------------------------------------------+
// | 戻値
// |    0 guest
// |   10 guest and allusers
// | 1000 user
// | 1010 group　(owner 含む)
// | 1011 owner
// | 1100 admin
// | 1110 group and admin
// | 1101 owner and admin
// | 1111 group owner and admin
// +---------------------------------------------------------------------------+
//update20101004 group_id
function DATABOX_chkuser(
        $group_id
        ,$owner_id
        ,$adminrights="databox.admin"
)
{
    global $_USER;

    $chk_user=0;
    if (isset($_USER['uid']) && $_USER['uid'] > 1) {
        $chk_user=1000;//ログインユーザ
    }
    if ( SEC_hasRights($adminrights)) {
        $chk_user=$chk_user+100;//admin
    }
    //group に属している人
    $usergroups=SEC_getUserGroups();

    if ( in_array($group_id, $usergroups)){
        $chk_user=$chk_user+10;//group
    }

    if ($owner_id===$_USER['uid']){
        $chk_user=$chk_user+1;//owner
    }

    return $chk_user;
}



// +---------------------------------------------------------------------------+
// | 機能  追加項目定義読込
// | 書式 DATABOX_getadditiondef($pi_name)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name: 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:追加項目定義
// +---------------------------------------------------------------------------+
// 20101116
function DATABOX_getadditiondef(
    $pi_name=""
)
{
    global $_TABLES;

    if ($pi_name==""){
        $table=$_TABLES['DATABOX_def_field'];
    }else{
        $table=$_TABLES[$pi_name.'_def_field'];
        $table=$_TABLES[strtoupper($pi_name).'_def_field'];
    }

    $rt=array();

    $sql="SELECT ";
    $sql.= " field_id ";
    $sql.= ",name ";
    $sql.= ",description";
    $sql.= ",type";
    $sql.= ",fieldgroup_id AS group_id";
    $sql.= ",selection";
    $sql.= ",selectlist";
    $sql.= ",checkrequried";
    $sql.= ",size";
    $sql.= ",maxlength";
    $sql.= ",rows";
    $sql.= ",br";
    $sql.= ",templatesetvar";

    $sql.= ",allow_display";
    $sql.= ",allow_edit";


    $sql.=" FROM";
    $sql.=" {$table}  AS m";
    $sql.= " ORDER BY orderno";

    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);
    for ($i = 1; $i <= $numrows; $i++) {
        $A = DB_fetchArray ($result);
        $field_id = COM_stripslashes($A['field_id']);
        $rt[$field_id]['name'] = COM_stripslashes($A['name']);
        $rt[$field_id]['checkrequried'] = COM_stripslashes($A['checkrequried']);
        $rt[$field_id]['description'] = COM_stripslashes($A['description']);
        $rt[$field_id]['allow_display'] = COM_stripslashes($A['allow_display']);
        $rt[$field_id]['allow_edit'] = COM_stripslashes($A['allow_edit']);
        $rt[$field_id]['type'] = COM_stripslashes($A['type']);
        $rt[$field_id]['group_id'] = COM_stripslashes($A['group_id']);
        $rt[$field_id]['selection'] = COM_stripslashes($A['selection']);

        $rt[$field_id]['size'] = COM_stripslashes($A['size']);
        if ($rt[$field_id]['size']==0){
            $rt[$field_id]['size']=60;
        }
        $rt[$field_id]['maxlength'] = COM_stripslashes($A['maxlength']);
        if ($rt[$field_id]['maxlength']==0){
            $rt[$field_id]['maxlength']=160;
        }

        $rt[$field_id]['rows'] = COM_stripslashes($A['rows']);
        if ($rt[$field_id]['rows']==0){
            $rt[$field_id]['rows']=2;
        }
        $rt[$field_id]['br'] = COM_stripslashes($A['br']);

        $rt[$field_id]['selectlist'] = COM_stripslashes($A['selectlist']);
        $rt[$field_id]['templatesetvar'] = COM_stripslashes($A['templatesetvar']);

        //@@@@@---->
        $selection= COM_stripslashes($A['selection']);
        //$selectionary=array();
        //$ary=array();
        //$ary=split("\r" , $selection);
        //$j=0;
        //for ($ii = 0; $ii < count($ary); $ii++) {
        //    if ($ary[$ii]<>""){
        //        $selectionary[$j]=$ary[$ii];
        //        $j=$j+1;
        //    }
        //}
        //$rt[$field_id]['selectionary']=$selectionary;

        $rt[$field_id]['selectionary']=DATABOX_getselectionary($selection);

        //@@@@@<-----

    }
    return $rt;

}
function DATABOX_getselectionary(
    $selection
)
{
    $selectionary=array();
    $ary=array();
    $ary=split("\r" , $selection);

    $j=0;
    for ($ii = 0; $ii < count($ary); $ii++) {
        if ($ary[$ii]<>""){
            $selectionary[$j]=$ary[$ii];
            $j=$j+1;
        }
    }
    return $selectionary;

}

// 検索用引数作成
// +---------------------------------------------------------------------------+
// | 機能  検索用引数作成
// | 書式 DATABOX_getadditionsearchdef($pi_name)
// +---------------------------------------------------------------------------+
// | 引数 $pi_name: 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 配列
// |    $rt["sql1"]: ",{$_TABLES['DATABOX_addition']} AS ad1"
// |    $rt["sql2"]: " AND t1.id=ad1.id AND ad1.field_id=1 "
// |    $rt["columns"][]: 'ad1.value '
// +---------------------------------------------------------------------------+
function DATABOX_getadditionsearchdef(
    $pi_name=""
)
{
	global $_TABLES;
	
    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    $table_def_field=$_TABLES[strtoupper($pi_name).'_def_field'];
    $table_addition=$_TABLES[strtoupper($pi_name).'_addition'];
	
	if ($box_conf['additionsearch']==0){
		return;
	}
	$lmt=$box_conf['additionsearch'];
	
    $retval=array();

    $rt=array();
    $sql1="";
    $sql2="";
    $columns=array();


    $sql=LB;
    $sql.="SELECT ";
    $sql.= " field_id ";
	
	$sql.=" FROM";
    $sql.=" {$table_def_field}  AS m";

    // 表示する項目のみ
    $sql.=" WHERE";
    $sql.=" allow_display='0'";
    //0: 一行テキストフィールド
    //1: 複数行テキストフィールド
    //10: 複数行テキストフィールド
    $sql.=" AND type IN (0,1,10)";

    $sql.=" ORDER  BY  orderno,field_id";
    $sql.=" LIMIT 0 ,".$lmt;

    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);

    for ($i = 1; $i <= $numrows; $i++) {
        $A = DB_fetchArray ($result);
        $field_id = COM_stripslashes($A['field_id']);
        $type = COM_stripslashes($A['type']);
        $selection= COM_stripslashes($A['selection']);
        $selectionary=DATABOX_getselectionary($selection);

        $sql1 .= " ,{$table_addition} AS ad{$field_id}".LB;
        $sql2 .= " AND t1.id=ad{$field_id}.id ";
        $sql2 .= " AND ad{$field_id}.field_id={$field_id} ".LB;
        $columns[]= "ad{$field_id}.value ";

    }

    $rt["sql1"]=$sql1;
    $rt["sql2"]=$sql2;
    $rt["columns"]=$columns;
	
    return $rt;

}



// +---------------------------------------------------------------------------+
// | 機能  追加項目読込
// | 書式 DATABOX_getadditiondatas($id)
// | $array=DATABOX_getaddtuindatas($id);
// +---------------------------------------------------------------------------+
// | 引数 $id:
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:追加項目リスト                                                 |
// +---------------------------------------------------------------------------+
function DATABOX_getadditiondatas(
    $id
    ,$pi_name=""
    )
{
    global $_TABLES;

    if ($pi_name==""){
        $table_addition=$_TABLES['DATABOX_addition'];
        $table_def_field=$_TABLES['DATABOX_def_field'];
    }else{
        $table_addition=$_TABLES[strtoupper($pi_name).'_addition'];
        $table_def_field=$_TABLES[strtoupper($pi_name).'_def_field'];
    }

    $rt=array();

    if ($id==0){
        $sql="SELECT m.field_id,'' AS value  ";
        $sql.=" FROM";
        $sql.=" {$table_def_field}  AS m";
        $sql.=" order by m.orderno ";

    }else{
        $sql="SELECT t.field_id,value  ";
        $sql.=" FROM";
        $sql.=" {$table_addition}  AS t";
        $sql.=" ,{$table_def_field}  AS m";
        $sql.=" where";
        $sql.=" t.id={$id}";
        $sql.=" AND t.field_id=m.field_id";
        $sql.=" order by m.orderno ";
    }

    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);

    for ($i = 1; $i <= $numrows; $i++) {
        $A = DB_fetchArray ($result);
        $field_id=$A['field_id'];
        $value=$A['value'];
        $rt[$field_id]=COM_stripslashes($value);
    }
    return $rt;

}

// +---------------------------------------------------------------------------+
// | 機能  追加項目入力用HTML作成
// | 書式 DATABOX_getaddtionfieldsEdit
//          ($additionfields,$addition_def,$templates,$chk_user)
// +---------------------------------------------------------------------------+
// | 引数 $fields:
// | 引数 $addition_def:
// | 引数 $templates:
// | 引数 $chk_user:
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:追加項目入力用HTML　                                           |
// +---------------------------------------------------------------------------+
// 20101206 日付
function DATABOX_getaddtionfieldsEdit(
    $fields
    ,$addition_def
    ,$templates
    ,$chk_user
    ,$pi_name=""
    ,$fields_fnm=""
    ,$fields_del=""
){
global $_TABLES;

    if ($pi_name==""){
        $lang_admin="LANG_DATABOX_ADMIN";
        $lang_noyes="LANG_DATABOX_NOYES";
        $table_def_field=$_TABLES['DATABOX_def_field'];
    }else{
        $lang_admin="LANG_".strtoupper($pi_name)."_ADMIN";
        $lang_noyes="LANG_".strtoupper($pi_name)."_NOYES";
        $table_def_field=$_TABLES[strtoupper($pi_name).'_def_field'];
    }

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    global $$lang_admin;
    global $$lang_noyes;
    $lang_admin=$$lang_admin;
    $lang_noyes=$$lang_noyes;


    $retval = '';

    if (is_array($fields)){
            $S = $fields;
    }else{
        if( !empty( $selected ))    {
            $S = explode( ' ', $fields );
        }else {
            $S = array();
        }
    }
    foreach( $S as $fid => $fvalue ){
        $name=$addition_def[$fid]['name'];
        $checkrequried=$addition_def[$fid]['checkrequried'];
        $type=$addition_def[$fid]['type'];
        $selectionary=$addition_def[$fid]['selectionary'];

        $size=$addition_def[$fid]['size'];
        $maxlength=$addition_def[$fid]['maxlength'];
        $rows=$addition_def[$fid]['rows'];
        $br=$addition_def[$fid]['br'];

        $templatesetvar=$addition_def[$fid]['templatesetvar'];

        $description=$addition_def[$fid]['description'];

        $chk=false;
        $disabled="disabled";
        if ($chk_user===9999){
            $chk=true;
            $disabled="";
        }else{
            //編集可能
            if  ($addition_def[$fid]['allow_edit']==='0') {
                $chk=true;
                $disabled="";
            //グループとadmin権のある人のみ表示
            }else if ($addition_def[$fid]['allow_edit']==='2'){
                if  ($chk_user>=1010) {
                    $chk=true;
                    $disabled="";
                }
            //所有者とadmin権のある人のみ
            }else if ($addition_def[$fid]['allow_edit']==='3'){
                if  ($chk_user>=1011) {
                    $chk=true;
                    $disabled="";
                }
            //編集不可表示のみ
            }else if ($addition_def[$fid]['allow_edit']==='4'){
                $chk=true;
            }
        }

        if ($chk) {

            $fieldhtml="";
            switch ($type) {
                case 12:// 図
                        $fvalue_fnm=$fields_fnm[$fid];
                        $fvalue_del=$fields_del[$fid];
                        $fieldhtml .=DATABOX_figurehtml12
                            ($fid,$fvalue,$fvalue_del);
                    break;
                case 11:// 図
                        $fvalue_fnm=$fields_fnm[$fid];
                        $fvalue_del=$fields_del[$fid];
                        $fieldhtml .=DATABOX_figurehtml
                            ($fid,$fvalue,$fvalue_del);
                    break;


                case 1:// 複数行テキストフィールド(HTML OK)
                case 10:// 複数行テキストフィールド(HTML NG)
                    if ($disabled===""){
                        $fieldhtml .= "<textarea ";
                        $fieldhtml .= " name=\"afield[".$fid."]\" ";
                        $fieldhtml .= " style=\"width:95%\"";
                        $fieldhtml .= " rows=\"".$rows."\"";
                        $fieldhtml .= " id=\"DATABOX_afield".$fid."\"";
                        $fieldhtml .= " ".$disabled." ";
                        $fieldhtml .= ">";
                        $fieldhtml .= $fvalue;
                        $fieldhtml .= " </textarea>";
                    }else{
                        $fieldhtml .=$fvalue;
                    }
                    break;
                case 2://いいえ/はい @!
                    if ($disabled===""){
                        if ($br==1){
                            $brx="<br".XHTML.">";
                        }else{
                            $brx="";
                        }
                        $fieldhtml=DATABOX_getradiolist
                            ($lang_noyes,"afield[".$fid."]",$fvalue,$brx ,$disabled);
                    }else{
                        $fieldhtml= $LANG_DATABOX_NOYES[$fvalue];
                    }
                    break;
                case 7://オプションリスト配列//@@@@@@
                    if ($disabled===""){
                        $fieldhtml=DATABOX_getoptionlistary($selectionary,"afield[".$fid."]",$fvalue,"DATABOX", $disabled);
                    }else{
                        $selectionary[$fvalue];
                    }
                    break;
                case 8://ラジオボタンリスト//@@@@@ @!
                    if ($disabled===""){
                        if ($br==1){
                            $brx="<br".XHTML.">";
                        }else{
                            $brx="";
                        }
                        $fieldhtml=DATABOX_getradiolist ($selectionary,"afield[".$fid."]",$fvalue,$brx,$disabled);
                    }else{
                        $selectionary[$fvalue];
                    }
                    break;
                case 3://日付（date picker対応）
                    if ($disabled===""){
                        if ($fvalue===""){

                            //$fvalue="2010/12/05";

                        }
                        $fieldhtml .= "        <input ";
                        $fieldhtml .= " type=\"text\"";
                        $fieldhtml .= " size=\"".$size."\"";
                        $fieldhtml .= " maxlength=\"".$maxlength."\" ";
                        $fieldhtml .= " class=\"inputDate\"";
                        $fieldhtml .= " id=\"afield".$fid."\" ";
                        $fieldhtml .= " name=\"afield[".$fid."]\" ";
                        $fieldhtml .= " value=\"".$fvalue."\"";
                        $fieldhtml .= " ".$disabled." ";
                        $fieldhtml .= " ".XHTML.">";
                    }else{
                        $fieldhtml .=$fvalue;
                    }
                    break;




                case 9://既定リスト　（準備中）';

                case 4://日時（準備中）

                case 5://メールアドレス　（準備中）';
                case 6://url　（準備中）';
                case 0:// 一行テキストフィールド
                default:
                    if ($disabled===""){
                        $fieldhtml .= "        <input ";
                        $fieldhtml .= " type=\"text\"";
                        $fieldhtml .= " size=\"".$size."\"";
                        $fieldhtml .= " maxlength=\"".$maxlength."\" ";
                        $fieldhtml .= " name=\"afield[".$fid."]\" ";
                        $fieldhtml .= " value=\"".$fvalue."\"";
                        $fieldhtml .= " ".$disabled." ";
                        $fieldhtml .= " ".XHTML.">";
                    }else{
                        $fieldhtml .=$fvalue;
                    }
            }


            if ($checkrequried==1){//必須
                $must= $lang_admin['must'];
            }else{
                $must="";
            }
            $templates->set_var('lang_must_additionfield', $must);

            $templates->set_var('additionfield_name', $name);
            $templates->set_var('additionfield_templatesetvar', "&#123;".$templatesetvar."&#125;");

            $addisionfields .= $fieldhtml;
            $templates->set_var('additionfield_value', $fieldhtml);

            $templates->set_var('additionfield_description', $description);

            $templates->set_var('additionfield'.$fid, $fieldhtml);
            $templates->set_var($templatesetvar, $fieldhtml);

            //=====
            $templates->parse ('col_var', 'col', true);
            $templates->parse ('additionfields', 'row', true);

            $templates->set_var ('col_var', '');
        }
    }

    return $retval;
}

//
function DATABOX_figurehtml(
    $fid
    ,$fvalue
    ,$fvalue_del
)
{

    global $LANG04;

    //
    $retval .= "        <input ";
    $retval .= " type=\"hidden\"";
    $retval .= " name=\"afield[".$fid."]\" ";
    $retval .= " value=\"".$fvalue."\"";
    $retval .= " ".XHTML.">".LB;

    $retval .="<input type=\"file\" dir=\"ltr\"";

    $retval .= " id=\"afield_fnm[".$fid."]\" ";
    $retval .= " name=\"afield_fnm[".$fid."]\" ";

//    $retval .= " value=\"".$fvalue_fnm."\"";
    $retval .="size=\"30\"";
    $retval .= " ".XHTML.">".LB;

    if ($fvalue<>""){
        $retval .= '<br' . XHTML . '>' ;
        $retval .= '<br' . XHTML . '>' ;
        $retval .="<img src=\"data:image/jpg;base64,";
        $retval .="{$fvalue}\"";
        $retval .="width=100 height=100";
        $retval .="{XHTML}>".LB;
        //
        $retval .= '<br' . XHTML . '>' ;
        $retval .= '<br' . XHTML . '>' ;
        $retval .= $LANG04[79];
        $retval .= "<input type=\"checkbox\"";
        $retval .= " name=\"afield_del[".$fid."]\" ";
        if ($fvalue_del){
            $retval .= " checked ";
        }
        $retval .= " {XHTML}>".LB;

    }

//  $photo_x = USER_getPhoto ($uid, $photo, $email, -1);

    return $retval;
}

function DATABOX_figurehtml12(
    $fid
    ,$fvalue
    ,$fvalue_del
)
{

    global $LANG04;

    //
    $retval .= "        <input ";
    $retval .= " type=\"hidden\"";
    $retval .= " name=\"afield[".$fid."]\" ";
    $retval .= " value=\"".$fvalue."\"";
    $retval .= " ".XHTML.">".LB;
	
	if ($fvalue==""){

        $retval .="<input type=\"file\" dir=\"ltr\"";

        $retval .= " id=\"afield_fnm[".$fid."]\" ";
        $retval .= " name=\"afield_fnm[".$fid."]\" ";

        $retval .="size=\"30\"";
        $retval .= " ".XHTML.">".LB;

    }else{

        $retval .="{$fvalue}";
        $retval .= '<br' . XHTML . '>' ;
        $retval .= $LANG04[79];
        $retval .= "<input type=\"checkbox\"";
        $retval .= " name=\"afield_del[".$fid."]\" ";
        if ($fvalue_del){
            $retval .= " checked ";
        }
        $retval .= " {XHTML}>".LB;

    }


    return $retval;
}

function DATABOX_imagehtml(
	$name
	,$fvalue
    ,$fvalue_del
)
{

    global $LANG04;

    //
    $retval .= "        <input ";
    $retval .= " type=\"hidden\"";
    $retval .= " name=\"".$name."\" ";
    $retval .= " value=\"".$fvalue."\"";
    $retval .= " ".XHTML.">".LB;
	
	if ($fvalue==""){

        $retval .="<input type=\"file\" dir=\"ltr\"";

        $retval .= " id=\"".$name."\" ";
        $retval .= " name=\"".$name."\" ";

        $retval .="size=\"30\"";
        $retval .= " ".XHTML.">".LB;

    }else{

        $retval .="{$fvalue}";
        $retval .= '<br' . XHTML . '>' ;
        $retval .= $LANG04[79];
        $retval .= "<input type=\"checkbox\"";
        $retval .= " name=\"".$name."_del\" ";
        if ($fvalue_del){
            $retval .= " checked ";
        }
        $retval .= " {XHTML}>".LB;

    }


    return $retval;
}


// +---------------------------------------------------------------------------+
// | 機能  追加項目入力用JavaScript作成
// | 書式 DATABOX_getaddtionfieldsJS
//          ($additionfields,$addition_def,$templates,$chk_user)
// +---------------------------------------------------------------------------+
// | 引数 $fields:
// | 引数 $addition_def:
// | 引数 $templates:
// | 引数 $chk_user:
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:追加項目入力用 JavaScript (DatePicker )
// +---------------------------------------------------------------------------+
// 20101206 日付
function DATABOX_getaddtionfieldsJS(
    $fields
    ,$addition_def
    ,$chk_user
    ,$pi_name=""
){
	global $_CONF;
	//GL のバージョンが1.8.0 より新しくなければおわり
	if (version_compare(VERSION, '1.8.0') >= 0) {
	}else{
		return ;
	}
	$scriptclass=$_CONF['path_system'] .'classes/scripts.class.php';
	if  (!file_exists($scriptclass) ){
		return;
	}
	
	global $_SCRIPTS;
	global $_TABLES;

    $table_def_field=$_TABLES[strtoupper($pi_name).'_def_field'];

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;
	
	$dateformat=$box_conf['dateformat'];

    $js= '';

    if (is_array($fields)){
            $S = $fields;
    }else{
        if( !empty( $selected ))    {
            $S = explode( ' ', $fields );
        }else {
            $S = array();
        }
    }
    foreach( $S as $fid => $fvalue ){
        $name=$addition_def[$fid]['name'];
        $checkrequried=$addition_def[$fid]['checkrequried'];
        $type=$addition_def[$fid]['type'];
        $selectionary=$addition_def[$fid]['selectionary'];

        $size=$addition_def[$fid]['size'];
        $maxlength=$addition_def[$fid]['maxlength'];
        $rows=$addition_def[$fid]['rows'];
        $br=$addition_def[$fid]['br'];

        $templatesetvar=$addition_def[$fid]['templatesetvar'];

        $description=$addition_def[$fid]['description'];

        $chk=false;
        $disabled="disabled";
        if ($chk_user===9999){
            $chk=true;
            $disabled="";
        }else{
            //編集可能
            if  ($addition_def[$fid]['allow_edit']==='0') {
                $chk=true;
                $disabled="";
            //グループとadmin権のある人のみ表示
            }else if ($addition_def[$fid]['allow_edit']==='2'){
                if  ($chk_user>=1010) {
                    $chk=true;
                    $disabled="";
                }
            //所有者とadmin権のある人のみ
            }else if ($addition_def[$fid]['allow_edit']==='3'){
                if  ($chk_user>=1011) {
                    $chk=true;
                    $disabled="";
                }
            //編集不可表示のみ
            }else if ($addition_def[$fid]['allow_edit']==='4'){
                $chk=true;
            }
        }

        if ($chk) {

            switch ($type) {

                case 3://日付（DatePicker 対応）
                    if ($disabled===""){
						$js.=DATABOX_getaddtionfieldsJS_date($fid,$dateformat);
                    }
                    break;
            }
            //=====
        }
    }
	
	
	$_SCRIPTS->setJavaScript($js, true);

    return ;
}

function DATABOX_getaddtionfieldsJS_date(
	$fid
	,$dateformat
){
	global $_SCRIPTS;
	//GL のバージョンが1.8.0 より新しくなければおわり
	if (version_compare(VERSION, '1.8.0') >= 0) {
	}else{
		return ;
	}
	$jquery="";
	if (function_exists('plugin_chkVersion_jquery')) {
		$jquery=plugin_chkVersion_jquery();
	}
	if ($jquery==="") {
		return;
	}
	$jsfooter=$_SCRIPTS->getFooter();
	$datepicker=mb_strpos($jsfooter,"datepicker");
	if (!$datepicker) {
		return;
	}
	
	$str="".LB;
	$str .= "	jQuery(function() {".LB;
	$str .= "        jQuery('#afield{$fid}').DatePicker({".LB;
    $str .= "            format:'{$dateformat}',".LB;
    $str .= "            date: jQuery('#afield{$fid}').val(),".LB;
    $str .= "            current: jQuery('#afield{$fid}').val(),".LB;
    $str .= "            starts: 1,".LB;
    $str .= "            position: 'top',".LB;
	
//	$str .= "            onBeforeShow: function(){".LB;
//    $str .= "                    jQuery('#afield{$fid}').DatePickerSetDate(jQuery('#afield{$fid}').val(), true);".LB;
//	$str .= "            },".LB;
	
    $str .= "            onChange: function(formated){".LB;
    $str .= "                    jQuery('#afield{$fid}').val(formated);".LB;
    $str .= "                    jQuery('#afield{$fid}').DatePickerHide();".LB;
    $str .= "            }".LB;
    $str .= "    	});".LB;
	$str .= "   });".LB;
	
	
	return $str;
}

function DATABOX_getenableexpired(
	$field
	,$flg
	,$pi_name
){
	global $_CONF;
	
	//GL のバージョンが1.8.0 より新しくなければおわり
	if (version_compare(VERSION, '1.8.0') >= 0) {
	}else{
		return ;
	}
	
	$scriptclass=$_CONF['path_system'] .'classes/scripts.class.php';
	if  (!file_exists($scriptclass) ){
		return;
	}

	
	global $_SCRIPTS;
	
	$str="".LB;
	
	$str .= "var disable = {$flg};".LB;
	$str .= "document.frm{$pi_name}.{$field}_year.disabled=!disable;".LB;
	$str .= "document.frm{$pi_name}.{$field}_month.disabled=!disable;".LB;
	$str .= "document.frm{$pi_name}.{$field}_day.disabled=!disable;".LB;
	$str .= "document.frm{$pi_name}.{$field}_hour.disabled=!disable;".LB;
	$str .= "document.frm{$pi_name}.{$field}_minute.disabled=!disable;".LB;
    if ($_CONF['hour_mode'] == 12) {
		$str .= "document.frm{$pi_name}.{$field}_ampm.disabled=!disable;".LB;
	}
	$str .=LB;
	$str .= "function enable_{$field}(obj) {".LB;
	$str .= "    var f = obj.form;".LB;
	$str .= "    var disable = obj.checked;".LB;
	$str .= "    f.elements[\"{$field}_year\"].disabled=!disable;".LB;
	$str .= "    f.elements[\"{$field}_month\"].disabled=!disable;".LB;
	$str .= "    f.elements[\"{$field}_day\"].disabled=!disable;".LB;
	$str .= "    f.elements[\"{$field}_hour\"].disabled=!disable;".LB;
	$str .= "    f.elements[\"{$field}_minute\"].disabled=!disable;".LB;
    if ($_CONF['hour_mode'] == 12) {
		$str .= "    f.elements[\"{$field}_ampm\"].disabled=!disable;".LB;
	}
	$str .= "}".LB;
	
	$_SCRIPTS->setJavaScript($str, true);
	
	return ;
}


// +---------------------------------------------------------------------------+
// | 機能  追加項目TEXT作成
// | 書式 DATABOX_getaddtionfieldsText
// |        ($additionfields,$addition_def,$chk_user)
// +---------------------------------------------------------------------------+
// | 引数 $fields:
// | 引数 $addition_def:
// | 引数 $chk_user:
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:追加項目text
// +---------------------------------------------------------------------------+
//update 20100420
function DATABOX_getaddtionfieldsText(
    $fields
    ,$addition_def
    ,$chk_user
    ,$pi_name=""
){

    if ($pi_name==""){
        $lang_noyes="LANG_DATABOX_NOYES";
    }else{
        $lang_noyes="LANG_".strtoupper($pi_name)."_NOYES";
    }

    global $$lang_noyes;
    $lang_noyes=$$lang_noyes;

    $retval = '';

    if (is_array($fields)){
            $S = $fields;
    }else{
        if( !empty( $selected ))    {
            $S = explode( ' ', $fields );
        }else {
            $S = array();
        }
    }

    foreach( $S as $fid => $fvalue ){

        $name=$addition_def[$fid]['name'];
        $checkrequried=$addition_def[$fid]['checkrequried'];
        $type=$addition_def[$fid]['type'];
        $selectionary=$addition_def[$fid]['selectionary'];

        $size=$addition_def[$fid]['size'];
        $maxlength=$addition_def[$fid]['maxlength'];
        $rows=$addition_def[$fid]['rows'];
        $br=$addition_def[$fid]['br'];

        $templatesetvar=$addition_def[$fid]['templatesetvar'];

        $chk=false;
        $disabled="disabled";
        if ($chk_user===9999){
            $chk=true;
            $disabled="";
        }else{
            //編集可能
            if  ($addition_def[$fid]['allow_edit']==='0') {
                $chk=true;
                $disabled="";
            //グループとadmin権のある人のみ表示
            }else if ($addition_def[$fid]['allow_edit']==='2'){
                if  ($chk_user>=1010) {
                    $chk=true;
                    $disabled="";
                }
            //所有者とadmin権のある人のみ
            }else if ($addition_def[$fid]['allow_edit']==='3'){
                if  ($chk_user>=1011) {
                    $chk=true;
                    $disabled="";
                }
            //編集不可表示のみ
            }else if ($addition_def[$fid]['allow_edit']==='4'){
                $chk=true;
            }
        }

        if ($chk) {
            $retval.=$name.":";

            switch ($type) {
                case 11://図
                    $retval .= "";
                    break;
                case 2://いいえ/はい
                    $retval .= $lang_box_noyes[$fvalue];
                    break;
                case 7://オプションリスト配列//@@@@@@
                case 8://ラジオボタンリスト//@@@@@
                    $retval .= $selectionary[$fvalue];
                    break;
                case 9://既定リスト　（準備中）';
                case 4://日時（準備中）
                case 3://日付（準備中）
                case 5://メールアドレス　（準備中）';
                case 6://url　（準備中）';
                case 0:// 一行テキストフィールド
                case 1:// 複数行テキストフィールド
                case 10:// 複数行テキストフィールド
                default:
                    $retval .= $fvalue;
            }
            $retval.=LB;
        }
    }
    return $retval;
}


// +---------------------------------------------------------------------------+
// | 機能  追加項目表示用HTML作成                                              |
// | 書式 DATABOX_getaddtionfieldsDisp
// |        ($additionfields,$addition_def,$templates,$chk_user)
// +---------------------------------------------------------------------------+
// | 引数 $fields:
// | 引数 $addition_def:
// | 引数 $templates:
// | 引数 $chk_user:
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:追加項目表示用HTML　                                           |
// +---------------------------------------------------------------------------+
//update 20101012 $chk_user
function DATABOX_getaddtionfieldsDisp(
    $fields
    ,$addition_def
    ,$templates
    ,$chk_user
    ,$pi_name=""
){
    global $_CONF;

    if ($pi_name==""){
        $lang_noyes="LANG_DATABOX_NOYES";
    }else{
        $lang_noyes="LANG_".strtoupper($pi_name)."_NOYES";
    }

    global $$lang_noyes;
    $lang_noyes=$$lang_noyes;

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

	$noimage="lib/noimage.jpg";

    $retval = '';


    if (is_array($fields)){
            $S = $fields;
    }else{
        if( !empty( $selected ))    {
            $S = explode( ' ', $fields );
        }else {
            $S = array();
        }
    }


    foreach( $S as $fid => $fvalue ){


        $name=$addition_def[$fid]['name'];
        $checkrequried=$addition_def[$fid]['checkrequried'];
        $type=$addition_def[$fid]['type'];
        $selectionary=$addition_def[$fid]['selectionary'];

        $size=$addition_def[$fid]['size'];
        $maxlength=$addition_def[$fid]['maxlength'];
        $rows=$addition_def[$fid]['rows'];
        $br=$addition_def[$fid]['br'];

        $templatesetvar=$addition_def[$fid]['templatesetvar'];


        $chk=false;
        //表示する
        if  ($addition_def[$fid]['allow_display']==='0') {
            $chk=true;
        //ログインユーザのみ
        }else if ($addition_def[$fid]['allow_display']==='1'){
            if  ($chk_user>=1000) {
                $chk=true;
            }
        //グループとadmin権のある人のみ表示
        }else if ($addition_def[$fid]['allow_display']==='2'){
            if  ($chk_user>=1010) {
                $chk=true;
            }
        //所有者とadmin権のある人のみ
        }else if ($addition_def[$fid]['allow_display']==='3'){
            if  ($chk_user>=1011) {
                $chk=true;
            }
        //admin権のある人のみ
        }else if ($addition_def[$fid]['allow_display']==='4'){
            if  ($chk_user>=1100) {
                $chk=true;
            }
        }

        if ($chk) {
            $fieldhtml="";//外画像の場合ファイル名
            $fieldhtml_tn="";//サムネイルファイル名
			$fieldhtmldisp="";//外画像　画像表示　（準備中）
			$fieldhtmldisp_tn="";//外画像　画像表示　（準備中）
			$fieldhtml_noimage="";
            $fieldhtml_tn_noimage="";
 
            switch ($type) {
                case 12://画像（外記憶）（準備中）
                    $fieldhtml=$fvalue;//ファイル名
					$fieldhtml_noimage=$fvalue;
                    //
					if  ($fvalue===""){
						$fieldhtml_noimage=$noimage;
						$fieldhtml_tn_noimage=$noimage;
					}else{
						$fieldurl= $_CONF['site_url']   ."/".$box_conf['imgfile_frd'] ;
						$fieldurl.= $fvalue ;

						$width=$box_conf['imgfile_thumb_w'] ;
						$height=$box_conf['imgfile_thumb_h'];

                        $fieldhtmldisp .="<img ";
                        $fieldhtmldisp .=" src=\"{$fieldurl}\"";
                        $fieldhtmldisp .=" width=\"{$width}\"";
                        $fieldhtmldisp .=" height=\"{$height}\"";
                        $fieldhtmldisp .=" border=\"0\"";
                        $fieldhtmldisp .=" alt=\"{$fvalue}\"";
                        $fieldhtmldisp .=" {XHTML}>";
					
						//サムネイル
					    $filename = substr($fvalue, 0, strrpos($fvalue,"."));
						$thum_filename = $filename . "-small.jpg";
						$imgfld_thmb=$_CONF['path_html'].$box_conf['imgfile_thumb_frd'];
					
						if(file_exists($imgfld_thmb.$thum_filename)) {
							$fieldhtml_tn=$thum_filename;
							$fieldhtml_tn_noimage=$thum_filename;
							//
							$fieldhtmldisp_tn="";//サムネイル

							$fieldhtmldisp_tn .="<a href=\"{tmp_url}\" target=\"_blank\">";
							$fieldhtmldisp_tn .="<img ";
							$fieldhtmldisp_tn .=" src=\"{$fieldurl}\"";
							$fieldhtmldisp_tn .=" width=\"{$width}\"";
							$fieldhtmldisp_tn .=" height=\"{$height}\"";
							$fieldhtmldisp_tn .=" border=\"0\"";
							$fieldhtmldisp_tn .=" alt=\"{$fvalue}";
							$fieldhtmldisp_tn .=" {XHTML}>";
							$fieldhtmldisp_tn .=" </a>";
						}else{
							$fieldhtml_tn_noimage=$noimage;
						}
						
                    }

                    break;
                case 13://添付ファイル （準備中）
                    $fieldhtml .= $fvalue;//ファイル名
                    break;

                case 11://画像（DB記憶)
                    if  ($fvalue===""){
                    }else{
                        $fieldhtml .="<img src=\"data:image/jpg;base64,";
                        $fieldhtml .="{$fvalue}\"{XHTML}>";
                    }
                    break;
                case 2://いいえ/はい
                    $fieldhtml .= $lang_box_noyes[$fvalue];
                    break;
                case 7://オプションリスト配列//@@@@@@
                case 8://ラジオボタンリスト//@@@@@
                    $fieldhtml .= $selectionary[$fvalue];
                    break;
				case 10:// 複数行テキストフィールド
					$w=DATABOX_nl2br($fvalue);
					$w=DATABOX_autolink($w);
					$fieldhtml .= $w;
                    break;
                case 9://既定リスト　（準備中）';
                case 4://日時（準備中）
                case 3://日付（準備中）
                case 5://メールアドレス　（準備中）';
                case 6://url　（準備中）';

                case 0:// 一行テキストフィールド
                case 1:// 複数行テキストフィールド
                default:
                    $fieldhtml .= $fvalue;
            }





            $templates->set_var('additionfield_name', $name);
            $templates->set_var('additionfield_templatesetvar', $templatesetvar);

            $templates->set_var('additionfield_value', $fieldhtml);
            $templates->set_var('additionfield_value_tn', $fieldhtml_tn);
            $templates->set_var('additionfield_value_disp', $fieldhtmldisp);
            $templates->set_var('additionfield_value_noimage', $fieldhtml_noimage);
            $templates->set_var('additionfield_value_tn_noimage', $fieldhtml_tn_noimage);

			$templates->set_var('name_additionfield'.$fid, $name);
			
            $templates->set_var('name_'.$templatesetvar, $name);

			$templates->set_var('additionfield'.$fid, $fieldhtml);
			$templates->set_var('additionfield'.$fid."_tn", $fieldhtml_tn);
			$templates->set_var('additionfield'.$fid."_noimage", $fieldhtml_noimage);
			$templates->set_var('additionfield'.$fid."_tn_noimage", $fieldhtml_tn_noimage);
			$templates->set_var('additionfield'.$fid."_disp", $fieldhtmldisp);
			//@@@@@ 準備中　$templates->set_var('additionfield'.$fid."_disptn", $fieldhtmldisp_tn);
			
			
			$templates->set_var($templatesetvar, $fieldhtml);
			$templates->set_var($templatesetvar."_tn", $fieldhtml_tn);
			$templates->set_var($templatesetvar."_noimage", $fieldhtml_noimage);
			$templates->set_var($templatesetvar."_tn_noimage", $fieldhtml_tn_noimage);
			$templates->set_var($templatesetvar."_disp", $fieldhtmldisp);
			//@@@@@ 準備中　$templates->set_var($templatesetvar."_disptn", $fieldhtmldisp_tn);

            //=====
            $templates->parse ('col_var', 'col', true);
            $templates->parse ('additionfields', 'row', true);

            $templates->set_var ('col_var', '');

        }
    }


    return $retval;
}



// +---------------------------------------------------------------------------+
// | 機能  カテゴリ表示用HTML作成
// | 書式 DATABOX_getcategoriesText
// +---------------------------------------------------------------------------+
// | 引数 $id:
// | 引数 $group_id:
// | 引数 $pi_name: 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:カテゴリテキスト
// +---------------------------------------------------------------------------+
//20101116
function DATABOX_getcategoriesText(
    $id
    ,$group_id=0
    ,$pi_name=""
){
    global $_TABLES;
    global $_CONF;



    if ($pi_name==""){
        $lang_box="LANG_DATABOX";
        $lang_box_admin="LANG_DATABOX_ADMIN";

        $table_def_category=$_TABLES['DATABOX_def_category'];
        $table_def_group=$_TABLES['DATABOX_def_group'];
        $table_category=$_TABLES['DATABOX_category'];

    }else{
        $lang_box="LANG_".strtoupper($pi_name);
        $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";

        $table_def_category=$_TABLES[strtoupper($pi_name).'_def_category'];
        $table_def_group=$_TABLES[strtoupper($pi_name).'_def_group'];
        $table_category=$_TABLES[strtoupper($pi_name).'_category'];
    }

    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    global $$lang_box;
    $lang_box=$$lang_box;

    $retval = '';

    $sql = "SELECT ".LB;
    $sql .= " m1.category_id".LB;
    $sql .= ",m1.name AS category_name".LB;
    $sql .= ",m1.code AS category_code".LB;
    //$sql .= ",m1.orderno";
    $sql .= ",m1.categorygroup_id".LB;
    //$sql .= ",m2.name AS group";
    $sql .= ",m2.code AS group_code".LB;
    $sql .= ",m2.group_id".LB;//@@@@@@

    $sql .= " FROM ".LB;
    $sql .= " {$table_category} AS t".LB;
    $sql .= " , {$table_def_category} AS m1".LB;
    $sql .= " , {$table_def_group} AS m2".LB;
    $sql .= " WHERE ".LB;
    $sql .= " t.id = ".$id.LB;

    if ($group_id<>0){
        $sql .= " AND m1.categorygroup_id=".$group_id.LB;
    }
    $sql .= " AND m1.category_id = t.category_id".LB;
    $sql .= " AND m2.group_id = m1.categorygroup_id".LB;

    $sql .= " ORDER by m1.categorygroup_id, m2.orderno, m1.orderno".LB;

    $result = DB_query( $sql );
    $nrows = DB_numRows( $result );

    $separater=$lang_box['category_separater_text'];

    $categpries="";


    $old_group_id="";
    $old_group_code="";
    $categories_group ="";
    $rt="";
    for( $i = 0; $i < $nrows; $i++ )    {
        $A = DB_fetchArray( $result, true );
        if ($old_group_id<>$A['group_id']){
            if ($categories_group<>""){
                $categories_group=rtrim($categories_group,$separater);
                $rt.=$categories_group.LB;
            }
            $categories_group ="";
            $old_group_id=$A['group_id'];
        }
        $categories.=$A['category_name']." ".$separater;
        $categories_group.=$A['category_name']." ".$separater;

    }

    $categories=rtrim($categories,$separater);
    $categories_group=rtrim($categories_group,$separater);
    $rt.=$categories_group.LB;

    $retval.=$lang_box_admin['category'].":".$categories.LB;

    return $retval;
}

// +---------------------------------------------------------------------------+
// | 機能  カテゴリテキスト作成
// | 書式 DATABOX_getcategoriesDisp
// +---------------------------------------------------------------------------+
// | 引数 $id:
// | 引数 $addition_def:
// | 引数 $templates:
// | 引数 $group_id:
// | 引数 $pi_name: 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:カテゴリ表示用HTML
// +---------------------------------------------------------------------------+
//20101129
function DATABOX_getcategoriesDisp(
    $id
    ,$templates
    ,$group_id=0
    ,$pi_name=""
){
    global $_TABLES;
    global $_CONF;

    if ($pi_name==""){
        $lang_box="LANG_DATABOX";
        $box_conf="_DATABOX_CONF";
        $table_def_category=$_TABLES['DATABOX_def_category'];
        $table_def_group=$_TABLES['DATABOX_def_group'];
        $table_category=$_TABLES['DATABOX_category'];

    }else{
        $lang_box="LANG_".strtoupper($pi_name);
        $box_conf="_".strtoupper($pi_name)."_CONF";
        $table_def_category=$_TABLES[strtoupper($pi_name).'_def_category'];
        $table_def_group=$_TABLES[strtoupper($pi_name).'_def_group'];
        $table_category=$_TABLES[strtoupper($pi_name).'_category'];
    }

    global $$box_conf;
    $box_conf=$$box_conf;

    global $$lang_box;
    $lang_box=$$lang_box;

    $retval = '';

    $sql = "SELECT ".LB;
    $sql .= " m1.category_id".LB;
    $sql .= ",m1.name AS category_name".LB;
    $sql .= ",m1.code AS category_code".LB;
    //$sql .= ",m1.orderno";
    $sql .= ",m1.categorygroup_id".LB;
    //$sql .= ",m2.name AS group";
    $sql .= ",m2.code AS group_code".LB;
    $sql .= ",m2.group_id".LB;//@@@@@@

    $sql .= " FROM ".LB;
    $sql .= " {$table_category} AS t".LB;
    $sql .= " , {$table_def_category} AS m1".LB;
    $sql .= " , {$table_def_group} AS m2".LB;
    $sql .= " WHERE ".LB;
    $sql .= " t.id = ".$id.LB;

    if ($group_id<>0){
        $sql .= " AND m1.categorygroup_id=".$group_id.LB;
    }
    $sql .= " AND m1.category_id = t.category_id".LB;
    $sql .= " AND m2.group_id = m1.categorygroup_id".LB;

    $sql .= " ORDER by m1.categorygroup_id, m2.orderno, m1.orderno".LB;

    $result = DB_query( $sql );
    $nrows = DB_numRows( $result );

    $separater=$lang_box['category_separater'];
    $categpries="";


    $old_group_id="";
    $old_group_code="";
    $categories_group ="";
    $categories_link_id_group ="";
    $categories_link_code_group ="";

    for( $i = 0; $i < $nrows; $i++ )    {
        $A = DB_fetchArray( $result, true );
        if ($old_group_id<>$A['group_id']){
            if ($categories_group<>""){

                $categories_group=rtrim($categories_group,$separater);
                $templates->set_var('group_'.$old_group_id, $categories_group);
                $templates->set_var('group_'.$old_group_code, $categories_group);

                $categories_link_id_group=rtrim($categories_link_id_group,$separater);
                $templates->set_var('grouplink_'.$old_group_id, $categories_link_id_group);

                $categories_link_code_group=rtrim($categories_link_code_group,$separater);
                $templates->set_var('grouplink_'.$old_group_code, $categories_link_code_group);

            }
            $categories_group ="";
            $categories_link_id_group ="";
            $categories_link_code_group ="";
            $old_group_id=$A['group_id'];
            $old_group_code=$A['group_code'];
        }
        $categories.=$A['category_name']." ".$separater;
        $categories_group.=$A['category_name']." ".$separater;

        $name=COM_applyFilter($A['category_name']);
        $url=$_CONF['site_url'] ."/".$pi_name."/category.php";
        $url_code=$url."?m=code&code=".$A['category_code'];
        $url_id=$url."?m=id&id=".$A['category_id'];
        $url_code   = COM_buildUrl( $url_code );
        $category_link_id= COM_createLink($name, $url_id);
        $category_link_code= COM_createLink($name, $url_code);

        $categories_link_id.=$category_link_id." ".$separater;
        $categories_link_id_group.=$category_link_id." ".$separater;
        $categories_link_code.=$category_link_code." ".$separater;
        $categories_link_code_group.=$category_link_code." ".$separater;
    }

    $categories=rtrim($categories,$separater);
    $templates->set_var('categories', $categories);


    $categories_link_code=rtrim($categories_link_code,$separater);
    $categories_link_id=rtrim($categories_link_id,$separater);


    if ($box_conf['categoriescode']){
        $categorieslink=$categories_link_code;
    }else{
        $categorieslink=$categories_link_id;
    }
    $templates->set_var('categorieslink', $categorieslink);


    $templates->set_var('categorieslinkcode', $categories_link_code);
    $templates->set_var('categorieslinkid', $categories_link_id);

    $categories_group=rtrim($categories_group,$separater);
    $templates->set_var('group_'.$old_group_id, $categories_group);
    $templates->set_var('group_'.$old_group_code, $categories_group);

    $categories_link_id_group=rtrim($categories_link_id_group,$separater);
    $templates->set_var('grouplink_'.$old_group_id, $categories_link_id_group);
    $categories_link_code_group=rtrim($categories_link_code_group,$separater);
    $templates->set_var('grouplink_'.$old_group_code, $categories_link_code_group);


    return $retval;
}

//@@@@@
// +---------------------------------------------------------------------------+
// | 機能  追加項目値を返す　　　　                                            |
// | 書式 DATABOX_getadditionfields($id,$addition_def,$tbl_prefix,$field_id
// +---------------------------------------------------------------------------+
// | 引数 $pi_name: 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:追加項目値の配列　　　                                          |
// +---------------------------------------------------------------------------+
//update 20110204
function DATABOX_getaddtionfields(
    $id
    ,$addition_def
    ,$pi_name
    ,$field_id=0
){

    global $_TABLES;

    $table_def_field=$_TABLES[strtoupper($pi_name).'_def_field'];
    $table_addition=$_TABLES[strtoupper($pi_name).'_addition'];

    $lang_box_noyes="LANG_".strtoupper($pi_name)."_NOYES";
    global $$lang_box_noyes;
    $lang_box_noyes=$$lang_box_noyes;

    $retval=array();

    $sql = "SELECT ";
    $sql .= " value";
    $sql .= ",t.field_id";

    $sql .= " FROM {$table_addition} AS t";
    $sql .= " , {$table_def_field} AS m";
    $sql .= " WHERE ";
    $sql .= " t.id={$id}";
    $sql .= " AND t.field_id=m.field_id";
    if ($field_id<>0){
        $sql .= " AND t.field_id=".$field_id;
    }

    $sql .= " ORDER BY m.orderno";

    $result = DB_query( $sql );
    $nrows = DB_numRows( $result );

    for( $i = 0; $i < $nrows; $i++ )    {

        $A = DB_fetchArray( $result, true );

        $fid=$A["field_id"];
        $value=$A["value"];

        $fieldvalue=DATABOX_getfieldvalue(
            $value
           ,$addition_def[$fid]['type']
           ,$addition_def[$fid]['selectionary']
           ,$lang_box_noyes
            );

        $retval[$fid]=$fieldvalue;
    }

    return $retval;
}
// +---------------------------------------------------------------------------+
// | 機能  追加項目値を返す　　　　                                            |
// | 書式 DATABOX_getfieldvalue($value,$type,$selectionary,$lang_box_noyes)
// +---------------------------------------------------------------------------+
// | 引数 $value:
// | 引数 $type:
// | 引数 $selectionary:
// | 引数 $lang_box_noyes:
// +---------------------------------------------------------------------------+
// | 戻値 nomal:追加項目値
// +---------------------------------------------------------------------------+
//update 20110204
function DATABOX_getfieldvalue(
    $value
    ,$type
    ,$selectionary
    ,$lang_box_noyes
){


     switch ($type) {

        case 2://いいえ/はい
            $fieldvalue = $lang_box_noyes[$value];
            break;
        case 7://オプションリスト配列//@@@@@@
        case 8://ラジオボタンリスト//@@@@@
            $fieldvalue.= $selectionary[$value];
            break;
        case 9://既定リスト　（準備中）';
        case 4://日時（準備中）
        case 3://日付（準備中）
        case 5://メールアドレス　（準備中）';
        case 6://url　（準備中）';

        case 0:// 一行テキストフィールド
        case 1:// 複数行テキストフィールド
        case 10:// 複数行テキストフィールド
        default:
            $fieldvalue = $value;
        }

    return $fieldvalue;
}


// +---------------------------------------------------------------------------+
// | 機能  追加項目書込                                                        |
// | 書式
// +---------------------------------------------------------------------------+
// | 引数 $ide:
// | 引数 $fldary:
// | 引数 $addition_def:
// | 引数 $pi_name: 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
//
function DATABOX_saveaddtiondatas(
    $id
    ,$fldary = ""
    ,$addition_def=""
    ,$pi_name=""
)
{
    global $_TABLES;

    if ($pi_name==""){
        $table=$_TABLES['DATABOX_addition'];
    }else{
        $table=$_TABLES[strtoupper($pi_name).'_addition'];
	}
	
    $rt=DB_delete($table,"id",$id);
    if (is_array($fldary)){
		foreach( $fldary as $field_id => $value ){
			if ($addition_def[$field_id]['type']=12){
			}
			$sql="INSERT INTO {$table} (";
			$sql.="id, field_id,value ";
			$sql.=") VALUES (";
			$sql.="{$id},{$field_id}, '{$value}'";
			$sql.=")";
			DB_query($sql);
			}
	}
    return ;

}

// +---------------------------------------------------------------------------+
// | 機能  追加項目書込 user用
// | 書式 $rt=DATABOX_saveaddtiondatas_update
// |               ($id,$additionfields,$addition_def,$pi_name);
// +---------------------------------------------------------------------------+
// | 引数 $ide:
// | 引数 $fldary:
// | 引数 $addition_def:
// | 引数 $pi_name: 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
//
function DATABOX_saveaddtiondatas_update(
    $id
    ,$fldary = ""
    ,$addition_def=""
    ,$pi_name=""
)
{
    global $_TABLES;

    if ($pi_name==""){
        $table=$_TABLES['DATABOX_addition'];
    }else{
        $table=$_TABLES[strtoupper($pi_name).'_addition'];
    }

    if (is_array($fldary)){

        foreach( $fldary as $field_id => $value ){
            //if ($addition_def[$field_id]['type']=11){
            $sql="UPDATE ".$table ." SET ";
            $sql.=" value ='".$value."'";
            $sql.=" WHERE id=".$id." AND field_id=".$field_id;
            DB_query($sql);
        }
    }


    return ;

}

// +---------------------------------------------------------------------------+
// | 機能  追加項目clean                                                       |
// | 書式 DATABOX_cleanadditiondatas($fldary,$typeary)                         |
// +---------------------------------------------------------------------------+
// | 引数 $fldary:
// | 引数 $addition_def
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
//update 20100702
function DATABOX_cleanaddtiondatas(
    &$fldary
	,$addition_def
	,&$fnmary
	,&$delary
)
{


    if (is_array($fldary)){
        foreach( $fldary as $field_id => $value ){
            $type=$addition_def[$field_id]['type'];
            $wk=$value;
            switch ($type) {
                case 11:// 図
                    break;
                case 1:// 複数行テキストフィールド(HTML OK)
                    $wk=COM_stripslashes($wk);
                    break;
                case 2://いいえ/はい
                    $wk=COM_applyFilter($wk,true);
                    break;
                case 7://オプションリスト配列//@@@@@@
                case 8://ラジオボタンリスト//@@@@@
                case 9://既定リスト　（準備中）';
                case 4://日時（準備中）
                case 3://日付（準備中）
                case 5://メールアドレス　（準備中）';
                case 6://url　（準備中）';
                case 0:// 一行テキストフィールド
                case 10:// 複数行テキストフィールド(HTML NG)
                case 12:// 図
					$fnmary[$field_id]=COM_applyFilter($fnmary[$field_id]);
					$delary[$field_id]=COM_applyFilter($delary[$field_id]);
                    $wk=COM_applyFilter($wk);
                    break;
                default:
                    $wk=COM_applyFilter($wk);
            }
            $wk=addslashes (COM_checkHTML (COM_checkWords ($wk)));
            $fldary[$field_id] =$wk;
        }
    }

    return $fldary;

}
// +---------------------------------------------------------------------------+
// | 機能  追加項目check                                                       |
// | 書式 DATABOX_checkdditiondatas($fldary,$typeary,$pi_data)
// | 書式 DATABOX_checkdditiondatas($additionfields,$addition_def,$pi_name
// | 								,$additionfields_fnm,$additionfields_del)
// +---------------------------------------------------------------------------+
// | 引数 $fldary:追加項目データ配列
// | 引数 $addition_def:追加項目定義
// | 引数 $pi_name: 'databox' 'userbox' 'formbox'
// +-----
// | 引数 $fldary_fnm:追加項目データ配列　アップロードファイル名
// | 引数 $fldary_del:追加項目データ配列　アップロードファイル削除
// +---------------------------------------------------------------------------+
// | 戻値 nomal:  
// +---------------------------------------------------------------------------+
//update
function DATABOX_checkaddtiondatas(
    &$fldary
    ,$addition_def
    ,$pi_name
    ,$fldary_fnm=""
	,$fldary_del=""
){

	global $LANG04;
    global $_FILES;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    $rt="";

    if (is_array($fldary)){

		foreach( $fldary as $field_id => $value ){
			//必須
            if ($addition_def[$field_id]['checkrequried']==1){
				if ($value ===""){
                    $rt.=$addition_def[$field_id]['name'].$lang_box_admin['err_checkrequried'] ."<br {XHTML}>".LB;
                }
                //url email date
            }
            //図 外記憶
            if ($addition_def[$field_id]['type']==12){//図

                 if (is_array($fldary_del) AND $fldary_del[$field_id]){
                     //$fldary[$field_id]="";
                     //$fldary_fnm[$field_id]="";
				 }else{
					if (is_array($fldary_fnm)){
						$imgname=$_FILES['afield_fnm']['name'][$field_id];
						$imgfile=$_FILES['afield_fnm']['tmp_name'][$field_id];
						$imgsize=$_FILES['afield_fnm']['size'][$field_id];
						$imgtype=$_FILES['afield_fnm']['type'][$field_id];

						if  ($imgfile<>""){
							$imgerr="";
							if ($box_conf['imgfile_size2']<>"" AND $imgsize>$box_conf['imgfile_size2']){
								$rt.=$addition_def[$field_id]['name'].$lang_box_admin['err_size']."<br {XHTML}>".LB;
								$imgerr=1;
							}
							if (count($box_conf['imgfile_type2'])>0){
								if  (in_array($imgtype,$box_conf['imgfile_type2'])){
								}else{
									$rt.=$addition_def[$field_id]['name'].$lang_box_admin['err_type2']
										.$_FILES['afield_fnm']['name'][$field_id]."(".$imgtype.")<br {XHTML}>".LB;
									$imgerr=1;
								}
							}
						
							//$imgname=sanitize
							// 問題文字を除く
							//$filename = str_replace("&","",$filename);
							//$filename = str_replace("'","",$filename);
							//$filename = str_replace("/","",$filename);
							//$filename = str_replace(".JPG",".jpg",$filename);
							//重複チェック
							if  ($imgerr===""){
								$fldary[$field_id]=$imgname;
							}
						}
					}else{
						
					}	
                 }
            }



            //図 DB記憶
            if ($addition_def[$field_id]['type']==11){//図

                 if (is_array($fldary_del) AND $fldary_del[$field_id]){
                        $fldary[$field_id]="";
                        $fldary_fnm[$field_id]="";
                 }else{
                    //$figure = $_FILES[$w];
                    // $imgfile=$figure[ 'tmp_name' ];


//echo "name=".$_FILES['afield_fnm']['name'][$field_id]."<br>";
//echo "tmp_name=". $_FILES['afield_fnm']['tmp_name'][$field_id]."<br>";
//echo "size=". $_FILES['afield_fnm']['size'][$field_id]."<br>";
//echo "type=". $_FILES['afield_fnm']['type'][$field_id]."<br>";

                     $imgfile=$_FILES['afield_fnm']['tmp_name'][$field_id];
                     $imgsize=$_FILES['afield_fnm']['size'][$field_id];
                     $imgtype=$_FILES['afield_fnm']['type'][$field_id];

                     if  ($imgfile<>""){
                          $imgerr="";
                          if ($box_conf['imgfile_size']<>"" AND $imgsize>$box_conf['imgfile_size']){
                              $rt.=$addition_def[$field_id]['name'].$lang_box_admin['err_size']."<br {XHTML}>".LB;
                              $imgerr=1;
                          }
                          if (count($box_conf['imgfile_type'])>0){
                              if  (in_array($imgtype,$box_conf['imgfile_type'])){
                              }else{
                                  $rt.=$addition_def[$field_id]['name'].$lang_box_admin['err_type']
                                    .$_FILES['afield_fnm']['name'][$field_id]."(".$imgtype.")<br {XHTML}>".LB;
                                  $imgerr=1;
                              }
                          }
                          if  ($imgerr===""){
                              $imgbinary = fread ( fopen ( $imgfile , "r" ), filesize ( $imgfile ));
                              $fldary[$field_id]=base64_encode($imgbinary);
                          }
                     }
                 }
            }

            //日付
			if ($addition_def[$field_id]['type']==3){//日付
                if ($value ===""){
                }else{
                    $w=strtotime($value);
                    if ($w==""){
                        $w_chk=false;
                    }else{
                        $w_year=date('Y', $w);
                        $w_month=date('m', $w);
                        $w_day=date('d', $w);
                        $w_chk=checkdate($w_month, $w_day, $w_year);
                    }
                    if ($w_chk==false) {
                        $rt.=$addition_def[$field_id]['name'].$lang_box_admin['err_date'] ."<br {XHTML}>".LB;
                    }else{
                        $fldary[$field_id]=date($box_conf['dateformat'],$w);
					}
                }
            }
            //メールアドレス
            if ($addition_def[$field_id]['type']==5){
                if ($value ===""){
                }else{
                    if (COM_isEmail ($value)==false) {
                        $rt.=$addition_def[$field_id]['name'].$LANG04[18]."<br {XHTML}>".LB;
                    }
                }
            }


        }
    }

    return $rt;

}

// +---------------------------------------------------------------------------+
// | 機能  追加項目upload
// | 書式 DATABOX_uploadaddtiondatas($additionfields,$addition_def,$pi_name
// | 								,$additionfields_fnm,$additionfields_del)
// +---------------------------------------------------------------------------+
// | 引数 $fldary:追加項目データ配列
// | 引数 $addition_def:追加項目定義
// | 引数 $pi_name: 'databox' 'userbox' 'formbox'
// +-----
// | 引数 $fldary_fnm:追加項目データ配列　アップロードファイル名
// | 引数 $fldary_del:追加項目データ配列　アップロードファイル削除
// +---------------------------------------------------------------------------+
// | 戻値 nomal:  
// +---------------------------------------------------------------------------+
//update
function DATABOX_uploadaddtiondatas(
    &$fldary
    ,$addition_def
	,$pi_name
	,$id
    ,$fldary_fnm=""
    ,$fldary_del=""
){
	global $_CONF;
    global $LANG04;
    global $_FILES;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    $rt="";

    if (is_array($fldary)){

        foreach( $fldary as $field_id => $value ){
			
			//図 外記憶
			if ($addition_def[$field_id]['type']==12){//図
				$imgfld=$_CONF['path_html'].$box_conf['imgfile_frd'];
				$imgfld_thmb=$_CONF['path_html'].$box_conf['imgfile_thumb_frd'];
				$filename=$fldary[$field_id];
				
				if (is_array($fldary_del) AND $fldary_del[$field_id]){
					$rt=DATABOX_deleteFile($imgfld.$filename);
					$thumbname=DATABOX_getthumbname($filename);
					$rt=DATABOX_deleteFile($imgfld_thmb.$thumbname);
					$fldary[$field_id]="";
				}else{
					$imgname=$_FILES['afield_fnm']['name'][$field_id];
					
					if ($imgname<>""){
						$imgfile=$_FILES['afield_fnm']['tmp_name'][$field_id];
						$imgsize=$_FILES['afield_fnm']['size'][$field_id];
						$imgtype=$_FILES['afield_fnm']['type'][$field_id];												
						$a=explode(".",$imgname);
						$newfilename=$addition_def[$field_id]['templatesetvar']."_".$id."_".time().".".$a[1];

						//$newfilename=$imgname;
						if (DATABOX_uploadImageFile
								($imgname,$imgfile,$imgsize, $imgtype,$imgfld,$newfilename)){
							$fldary[$field_id]=$newfilename;
							//サムネイル作成する場合
							if($box_conf['imgfile_thumb_ok'] && function_exists('ImageCreate')) {
								$rt=DATABOX_createthumb(
									$newfilename
									,$box_conf['imgfile_thumb_w'] ,$box_conf['imgfile_thumb_h'] 
									,$box_conf['imgfile_thumb_w2'] ,$box_conf['imgfile_thumb_h2'] 
									,$imgfld_thmb,$imgfld);
							}
						}else{
							$fldary[$field_id]="";
							//$fldary_fnm[$field_id]="";
						}
					}
				}
			}
		}
    }

    return $rt;

}

//update
function DATABOX_uploadaddtiondatas_xml(
    &$fldary
    ,$addition_def
	,$pi_name
	,$id
){
	global $_CONF;
    global $LANG04;
    global $_FILES;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    $rt="";

    if (is_array($fldary)){
		foreach( $fldary as $field_id => $value ){
			if ($fldary[$field_id]<>""){
				
				//図 外記憶
				if ($addition_def[$field_id]['type']==12){//図
					$imgfld=$_CONF['path_html'].$box_conf['imgfile_frd'];
					$imgfld_thmb=$_CONF['path_html'].$box_conf['imgfile_thumb_frd'];
					//
					$imgname=$addition_def[$field_id]['templatesetvar']."_".$id."_".time().".jpg";
					$imgsrc=$fldary[$field_id];
					if (DATABOX_uploadImageFile_xml($imgname,$imgsrc,$imgfld)){
						$fldary[$field_id]=$imgname;
						//サムネイル作成する場合
						if($box_conf['imgfile_thumb_ok']==="1" && function_exists('ImageCreate')) {
							$srcfile=$imgfld.$imgname;
							$rt=DATABOX_createthumb(
								$imgname
								,$box_conf['imgfile_thumb_w'] ,$box_conf['imgfile_thumb_h'] 
								,$box_conf['imgfile_thumb_w2'] ,$box_conf['imgfile_thumb_h2'] 
								,$imgfld_thmb,$imgfld);
						}
					}else{
						$fldary[$field_id]="";
					}
				}
			}
        }
    }

    return $rt;

}
function DATABOX_deleteaddtionfiles(
	$fldary
    ,$addition_def
	,$pi_name
){
	global $_CONF;

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    $rt="";

    if (is_array($fldary)){
        foreach( $fldary as $field_id => $value ){
			
			//図 外記憶
			if ($addition_def[$field_id]['type']==12){//図
				$filename=$fldary[$field_id];
				if ($filename<>""){
					$imgfld=$_CONF['path_html'].$box_conf['imgfile_frd'];
					$imgfld_thmb=$_CONF['path_html'].$box_conf['imgfile_thumb_frd'];
					$rt=DATABOX_deleteFile($imgfld.$filename);
					$thumbname=DATABOX_getthumbname($filename);
					$rt=DATABOX_deleteFile($imgfld_thmb.$thumbname);
				}
			}
		}
    }

    return $rt;

}
//
function DATABOX_deleteaddtionfiles_def(
	$field_id
	,$pi_name
){
	global $_CONF;
	global $_TABLES;

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    $rt="";
	
	$table_addition=$_TABLES[strtoupper($pi_name).'_addition'];
	//
    $sql="SELECT value  ";
    $sql.=" FROM";
    $sql.=" {$table_addition} ";
    $sql.=" where";
    $sql.=" field_id=".$field_id;

    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);
    for ($i = 1; $i <= $numrows; $i++) {
        $A = DB_fetchArray ($result);
		$filename = COM_stripslashes($A['value']);
		if ($filename<>""){
			$imgfld=$_CONF['path_html'].$box_conf['imgfile_frd'];
			$imgfld_thmb=$_CONF['path_html'].$box_conf['imgfile_thumb_frd'];
			$rt=DATABOX_deleteFile($imgfld.$filename);
			$thumbname=DATABOX_getthumbname($filename);
			$rt=DATABOX_deleteFile($imgfld_thmb.$thumbname);
		}
    }

    return $rt;

}

function DATABOX_deleteaddtionfiles_all(
	$fld
	,$pi_name
){
	
	
	
	global $_CONF;
	global $_TABLES;

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    $rt="";

	if( is_dir( $fld)){
		$fd = opendir( $fld );
        while(( $file = @readdir( $fd )) == TRUE )    {
			if( !is_dir( $fd1 . $file)
					&& $dir <> '.'
                    && $dir <> '..'
                    && $dir <> 'CVS'
                    && substr( $dir, 0 , 1 ) <> '.' ) {
				clearstatcache();
				$rt=DATABOX_deleteFile($fld.$file);
			}
        }
    }

    return $rt;

}

// +---------------------------------------------------------------------------+
// | 機能  追加項目uploadfile delete
// | 書式 DATABOX_checkdditiondatas($filename)
// +---------------------------------------------------------------------------+
// | 引数 $filename:フォルダ/ファイル
// +---------------------------------------------------------------------------+
// | 戻値 nomal:  
// +---------------------------------------------------------------------------+
//update
function DATABOX_deleteFile (
	$filename
)
{
	$rt=false;
	if (file_exists ($filename)) {
		if (!@unlink ($filename)) {
            COM_errorLog ("Unable to remove file $filename");
		}else{
			$rt=true;
            COM_errorLog ("remove file $filename");
	    }		
	}else{
            COM_errorLog ("Unable to remove file $filename not exist");
	}
	return $rt;
}

// +---------------------------------------------------------------------------+
// | 機能  upload Image File
// | 書式 DATABOX_uploadImageFile($imgname,$imgfile,$imgsize, $imgtype,$imgfld)
// +---------------------------------------------------------------------------+
// | 引数 $imgname:
// | 引数 $imgfile:
// | 引数 $imgsize:
// | 引数 $imgtype:
// | 引数 $imgfld:
// +---------------------------------------------------------------------------+
// | 戻値 nomal:  
// +---------------------------------------------------------------------------+
//update
function DATABOX_uploadImageFile (
	 $imgname
	,$imgfile
	,$imgsize
	,$imgtype
	,$imgfld
	,$newfilename
)
{
	$FilePermissions = (int) 0755;

	if (is_uploaded_file ($imgfile)) {
		//$newfile=$imgfld.$imgname;
		$newfile=$imgfld.$newfilename;
		if(!file_exists($newfile)) {
			$returnMove = move_uploaded_file($imgfile, $newfile); 
			if (!$returnMove) {
				$logmsg="DataBox ImageFile upload error: ".$tmp." to ".$name;
                COM_errorLOG($logmsg);
                return false;
             } else {
                $chown =@chmod ($newfile,$FilePermissions);
				$logmsg="DataBox ImageFile upload sucsess: ".$imgname;
				COM_errorLOG($logmsg);
				return true;
             }
         }else{
            COM_errorLOG("Filemgmt upload error: Temporary file does not exist: '".$tmp ."'");
            return false;
		 }
	}
}
//
function DATABOX_uploadImageFile_xml (
	$imgname
	,$imgsrc
	,$imgfld
)
{
	$FilePermissions = (int) 0755;

	$newfile=$imgfld.$imgname;
	//
	$fp = fopen($newfile, "w");
	$imgsrc=base64_decode($imgsrc);
    fputs($fp, $imgsrc);
    fclose($fp);
	
	if(!file_exists($newfile)) {
		$logmsg="DataBox ImageFile upload error: ".$tmp." to ".$name;
        COM_errorLOG($logmsg);
        return false;
    } else {
        $chown =@chmod ($newfile,$FilePermissions);
		$logmsg="DataBox ImageFile upload sucsess: ".$imgname;
		COM_errorLOG($logmsg);
		return true;
    }
}

// +---------------------------------------------------------------------------+
// | 機能 uploadfile サムネイル作成
// | 書式 DATABOX_createthumb($srcfile,$W,$H,$W2,$H2, $thumb_dir,$tmp_dir)
// +---------------------------------------------------------------------------+
// | 引数 $imgname:
// | 引数 $imgfile:
// | 引数 $imgsize:
// | 引数 $imgtype:
// | 引数 $imgfld:
// +---------------------------------------------------------------------------+
// | 戻値 nomal:  
// +---------------------------------------------------------------------------+
//update
function DATABOX_createthumb(
	$filename
	, $W
	, $H
	, $W2
	, $H2
	, $imgfld_thmb
	, $imgfld
)
{
    //$filename = substr($srcfile, strrpos($srcfile,"/")+1);
	$srcfile=$imgfld.$filename;
	// 画像の幅と高さとタイプを取得
	$imagesize = GetImageSize($srcfile);
	$srcfile_w=$imagesize['0'];
	$srcfile_h=$imagesize['1'];
	$srcfile_type=$imagesize['2'];
	//対象タイプの確認
    switch ($srcfile_type) {
        case 1 : //gif
             return false;
             break;
        case 2 : //jpg
             $srcimage = @ImageCreateFromJPEG($srcfile);
             break;
        case 3 : //png
             $srcimage = @ImageCreateFromPNG($srcfile);
             break;
    }
	if (!$srcimage) {
        return false;
	}
	
	$logmsg="size=(w)".$srcfile_w." > ".$w." (h)".$srcfile_h." > ".$h;
    COM_errorLog ($logmsg);

	//対象サイズの確認
	if ($srcfile_w > $w || $srcfile_h > $h) {
	}else{
        return false;
	}
		
    // リサイズ
    if ($srcfile_w > $W || $srcfile_h > $H) {
        $key_w = $W / $srcfile_w;
        $key_h = $H / $srcfile_h;
        ($key_w < $key_h) ? $keys = $key_w : $keys = $key_h;
        $out_w = $srcfile_w * $keys;
        $out_h = $srcfile_h * $keys;
    } else {
        $out_w = $srcfile_w;
        $out_h = $srcfile_h;
    }

    // 出力画像（サムネイル）のイメージを作成し、元画像をコピーします。(GD2.0用)
    $outimage = ImageCreateTrueColor($out_w, $out_h);
	$resize = ImageCopyResampled(
			$outimage, $srcimage, 0, 0, 0, 0
			, $out_w, $out_h, $srcfile_w, $srcfile_h);

    // サムネイル画像をブラウザに出力、保存
    //$filename = substr($srcfile, strrpos($srcfile,"/")+1);
	$thum_filename = DATABOX_getthumbname($filename);
   	$filename = substr($filename, 0, strrpos($filename,"."));
    touch($imgfld_thmb.$thum_filename);
	ImageJPEG($outimage, $imgfld_thmb.$thum_filename);
		
	// 出力画像のイメージを作成し、元画像をコピーします。(GD2.0用)
    // リンク先のサイズが設定値より大きい時リンク先も縮小する<-----
    if ($srcfile_w > $W2 || $srcfile_h > $H2) {
        $rt = rename($imgfld.$filename.".jpg",$imgfld.$filename."-bak.jpg");
        if (!$rt) {
            echo "rename err!";
        }

        $key_w = $W2 / $srcfile_w;
        $key_h = $H2 / $srcfile_h;
        ($key_w < $key_h) ? $keys = $key_w : $keys = $key_h;
        $out_w = $srcfile_w * $keys;
        $out_h = $srcfile_h * $keys;
        $outimage2 = ImageCreateTrueColor($out_w, $out_h);
		$resize = ImageCopyResampled(
			$outimage2, $srcimage, 0, 0, 0, 0
			, $out_w, $out_h, $srcfile_w, $srcfile_h);

        touch($imgfld.$filename.".jpg");
        ImageJPEG($outimage2, $imgfld.$filename.".jpg"); 

        ImageDestroy($outimage2);
        $rt=unlink($imgfld.$filename."-bak.jpg");
        if (!$rt) {
            echo "delete err!";
        }

   }
    
    // 作成したイメージを破棄
    ImageDestroy($srcimage);
    ImageDestroy($outimage);

    return $thum_filename;

}
//
function DATABOX_getthumbname(
	$filename
)
{
	$rt="";
	if ($filename<>""){
		$filename = substr($filename, 0, strrpos($filename,"."));
		$rt = $filename . "-small.jpg";
	}
	return $rt;
}
//==============================================================================

// +---------------------------------------------------------------------------+
// | 機能  ラジヲボタン作成
// | 書式 DATABOX_getradiolist
// |                ($lang_noyes,"afield[".$fid."]",$fvalue,$brx ,$disabled);
// +---------------------------------------------------------------------------+
// | 引数 $selected:選択値　省略時                                             |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:ラジヲボタン                                                   |
// +---------------------------------------------------------------------------+
//update 20100420
function DATABOX_getradiolist(
    $ary
    ,$fld
    ,$selected = 0
    ,$br=""
    ,$disabled=""
)
{
    $retval = '';

    foreach( $ary as $id => $value ){

        //echo "ary".$i."=".$ary[$i]."<br>";
        $retval .= "<input type='radio' NAME='$fld' $disabled VALUE='$id'";
        if( $id == $selected ){
                $retval .= " CHECKED=CHECKED ";
        }

        $retval .= ">$ary[$id]&nbsp".$br;
    }

    return $retval;
}

// +---------------------------------------------------------------------------+
// | 機能  オプションリスト作成（配列より）                                    |
// | 書式 DATABOX_getyesno($no,$fld,$selected)                                 |
// +---------------------------------------------------------------------------+
// | 引数 $ary:
// | 引数 $fld:
// | 引数 $selected:選択値　省略時
// | 引数 $pi_name: 'databox' 'userbox' 'formbox'
// | 引数 $disabled:
// +---------------------------------------------------------------------------+
// | 戻値 nomal:オプションリスト                                               |
// +---------------------------------------------------------------------------+
//update 20101122
function DATABOX_getoptionlistary(
    $ary
    ,$fld
    ,$selected = ""
    ,$pi_name=""
    ,$disabled=""

)
{

    if ($pi_name==""){
        $lang_databox="LANG_DATABOX";
    }else{
        $lang_databox="LANG_".strtoupper($pi_name);
    }
    global $$lang_databox;

    $retval = '';

    $retval .= "<select name='$fld'       $disabled>".LB;
    $retval .= "<option value=0>{$$lang_databox['selectit']}</option>".LB;

    for ($i = 0; $i < count($ary); $i++) {

        $retval .= "<option value=\"".$i."\"";

        if( $i == $selected ){
                $retval .= " selected=\"selected\" ";
        }
        $retval .= ">";

        $retval .= $ary[$i];
        $retval .= "</option>".LB;

    }

    $retval .= '</select>';

    return $retval;
}



// +---------------------------------------------------------------------------+
// | 機能  オプションリスト作成                                                |
// | 書式 profile_getoptionlist($kind)                                           |
// +---------------------------------------------------------------------------+
// | 引数 $kind:項目種類
// | 引数 $selected:選択値
// | 引数
// | 引数 $pi_name: 'databox' 'userbox' 'formbox'
// | 引数
// +---------------------------------------------------------------------------+
// | 戻値 nomal:オプションリスト                                               |
// +---------------------------------------------------------------------------+
//update 20101122,20110613
function DATABOX_getoptionlist(
    $kind
    ,$selected = 0
    ,$relno=0
    ,$pi_name=""
	,$disabled=""
	,$selectit=true
)
{
global $_CONF;
global $_TABLES;

    if ($pi_name==""){
        $lang_box="LANG_DATABOX";
    }else{
        $lang_box="LANG_".strtoupper($pi_name);
    }
    global $$lang_box;
    $lang_box=$$lang_box;

    $retval = '';

	$retval .= "<select name='$kind' $disabled>".LB;
//echo "selectlist=".$selectlist."<br>";
	if ($selectit){
//echo "AA<br>";
		$retval .= "<option value=0>{$lang_box['selectit']}</option>".LB;
	}
	
    if ($kind=="group"){
        $where="";
        $table=$_TABLES[strtoupper($pi_name).'_def_group'];
        $fields= 'group_id,name,orderno';
    }else if ($kind=="parent"){
        $where="";
        $table=$_TABLES[strtoupper($pi_name).'_def_category'];
        $fields= 'category_id,name,orderno';
    }else{
        $table=$_TABLES[strtoupper($pi_name).'_mst'];
        $where="kind='{$kind}'";
        if ($relno<>0) $where.=" AND relno='{$relno}'";
        $fields= 'no,value,orderno';
    }
    $retval .= COM_optionList (
        $table
        , $fields
        , $selected,2,$where);

    $retval .= '</select>';

    if ($disabled==="disabled"){
        $retval .= "  <input type='hidden' name='{$kind}' value='{$selected}'>";
    }

    return $retval;
}


// +---------------------------------------------------------------------------+
// | 機能  テーブルのデータをCSV形式でダウンロードする
// | 書式 DATABOX_dltbldt($filenm,$fld,$tbl,$where,$order,$type,$pi_name,false);
// +---------------------------------------------------------------------------+
// | 引数 $filenm :
// | 引数 $fld :
// | 引数 $tbl :
// | 引数 $where :
// | 引数 $order :
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// | 引数 $addition:false=追加項目なし、 true=追加項目あり
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
//20100910
//@@@@@ 追加予定　カテゴリも

function DATABOX_dltbldt(
    $filenm
    ,$fld
    ,$tbl
    ,$where=""
    ,$order=""
    ,$pi_name=""
    ,$addition=false
)
{
    global $_CONF;

    $dm=",";//区切り文字
    $ec="`";//フィールド囲み文字

    if ($addition){
        $addition_def=DATABOX_getadditiondef($pi_name);
    }

    $retval="";

    //file output open
    $curdatetime = date("Y_m_d_H_i_s");
    //$outfile = tempnam($_CONF['path_data'] ."tmp", $filenm);
    $outfile = $_CONF['path_data'] . $filenm."_".$curdatetime;
    $file = @fopen( $outfile, 'w' );
    if ( $file === false ) {
        $retval .= "ERR! ".$outfile ." is not writable!<br {XHTML}>" . LB;
        return $retval;
    }

    //-----
    $sql = "SELECT DISTINCT ";
    foreach($fld as $k => $v) {
        $sql .=$k.",";
    }
    $sql=rtrim($sql,",");
    $sql.= " FROM ".$tbl;
    if (!empty($where)) {
        $sql.=" WHERE ".$where;
    }
    if (!empty($order)) {
        $sql.=" ORDER BY ".$order;
    }
    //-----
    $result = DB_query ($sql);
    //-----1行目ヘッダ
    $w='';
    foreach($fld as $k => $v) {
        $w .=$ec.$k.$ec.$dm;
    }
    //addition field
    if ($addition){

        if ($addition_def<>""){
            $s=$addition_def;
            foreach($s as $k1 =>$v1){
                $w .=$ec.'additionfield'.$k1.$ec.$dm;
            }
        }
    }
    $w=rtrim($w,$dm);

    $w = str_replace( array( '<?', '?>' ), array( '(@', '@)' ),$w );
    fputs( $file, $w.LB);

    //-----2行目ヘッダ
    $w="";
    foreach($fld as $k => $v) {
        $w .=$ec.$v.$ec.$dm;
    }
    //addition field
    if ($addition){
        if ($addition_def<>""){
            $s=$addition_def;
            foreach($s as $k1 =>$v1){
                $w .=$ec.$addition_def[$k1]['name'].$ec.$dm;
            }
        }
    }

    $w=rtrim($w,$dm);

    $w = str_replace( array( '<?', '?>' ), array( '(@', '@)' ),$w );
    fputs( $file, $w.LB);
    //-----3行目以降
    while( $A = DB_fetchArray( $result ) )    {

        $w="";
        foreach($fld as $k => $v) {
            $w .=$ec.$A[$k].$ec.$dm;
        }
//
        if ($addition){
            $wkary=DATABOX_getaddtionfields($A["id"],$addition_def,$pi_name);
            $s=$addition_def;
            foreach($s as $k1 =>$v1){
                //echo "k1=".$k1."<br {XHTML}>";


                if (isset($wkary[$k1])) {
                //echo "wkary=".$wkary[$k1]."<br>";
                    $w .=$ec.$wkary[$k1].$ec.$dm;
                }else{
                //echo "DD<br>";
                    $w .=$ec.$ec.$dm;
                }
            }
        }
        $w=rtrim($w,$dm);

        $w = str_replace( array( '<?', '?>' ), array( '(@', '@)' ),$w );
        fputs( $file, $w.LB);

    }

    $filename=basename($outfile).".csv";
    $dir=dirname($outfile);

    header ("Content-Disposition: attachment; filename=$filename");
    header ("Content-type: application/x-csv");
    readfile ($outfile);

    $rt=unlink($outfile);

    return $retval;
}

// +---------------------------------------------------------------------------+
// | 機能  featuredimage取得
// | 書式 DATABOX_getfeaturedimage($id,$pi_name)
// | typw14(外画像)表示する先頭（順番が若い）追加項目をfeaturedimageとして取得
// +---------------------------------------------------------------------------+
// | 引数 $id:
// | 引数 $pi_name:plugin name 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 nomal:featuredimage
// +---------------------------------------------------------------------------+
function DATABOX_getfeaturedimage(
	$id
	,$pi_name
)
{
	global $_CONF;
	global $_TABLES;

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

	$tbl1=$_TABLES[strtoupper($pi_name).'_addition'];
    $tbl2=$_TABLES[strtoupper($pi_name).'_def_field'];

	$rt="";

    $sql = "SELECT t1.value";
    $sql .= " FROM ";
	$sql .= " {$tbl1} AS t1";
	$sql .= "  ,{$tbl2} AS t2";
	$sql .= "  WHERE t1.id=".$id;
	$sql .= "  AND t1.field_id =t2.field_id";
	$sql .= "  AND t2.type=12";
	$sql .= "  AND allow_display=0";
	$sql .= "  ORDER BY orderno";

    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);

    if ($numrows > 0) {
        $A = DB_fetchArray ($result);
		$value=$A['value'];
		if  ($value<>""){
			$rt='<img src="';
			$rt.=$_CONF['site_url']   ."/".$box_conf['imgfile_frd'] ;
			$rt.= $value ;
			$rt.='" />';
		}
	}

	return $rt;
}

?>