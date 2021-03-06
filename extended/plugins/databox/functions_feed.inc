<?php
/* Reminder: always indent with 4 spaces (no tabs). */

// $Id: plugins/databox/functions_feed.inc
//2010/08/09 tsuchitani AT ivywe DOT co DOT jp http://www.ivywe.co.jp/
//20110801

if (strpos ($_SERVER['PHP_SELF'], 'functions_feed.inc') !== false) {
    die ('This file can not be used on its own.');
}

// +---------------------------------------------------------------------------+
// | 機能  feed type=databox のコンテンツリストを返す
// +---------------------------------------------------------------------------+
// | 戻値 nomal:feed type=databox のコンテンツリストを返す
// |      0:all categories
// |      >0:category_id  category name
// +---------------------------------------------------------------------------+
function plugin_getfeednames_databox()
{
    global $_TABLES;

    $feeds = array ();
    $feeds[] = array ('id' => '0', 'name' => 'all categories');

    $sql = "SELECT category_id,name FROM {$_TABLES['DATABOX_def_category']}  ORDER BY orderno";
    $result = DB_query ($sql);
    $num = DB_numRows ($result);

    if ($num > 0) {

        for ($i = 0; $i < $num; $i++) {
            $A = DB_fetchArray ($result);
            $feeds[] = array ('id' => $A['category_id'], 'name' => $A['name']);
        }
    }

    return $feeds;
}

/**
* Checking if links feeds are up to date
*
* @param    int     $feed           id of feed to be checked
* @param    string  $category_id    topic
* @param    string  $update_data    data describing current feed contents
* @param    string  $limit          number of entries or number of hours
* @param    string  $updated_type   (optional) type of feed to be updated
* @param    string  $updated_topic  (optional) feed's "topic" to be updated
* @param    string  $updated_id     (optional) id of entry that has changed
* @return   boolean                 true: feed data is up to date; false: isn't
*
*/

// +---------------------------------------------------------------------------+
// | 機能  コンテンツが更新されていたら true 更新されていなければfalseを返す
// +---------------------------------------------------------------------------+
function plugin_feedupdatecheck_databox (
    $feed
    , $category_id
    , $update_data
    , $limit
    , $updated_type = ''
    , $updated_topic = ''
    , $updated_id = ''
)
{
    global $_TABLES;

    $is_current = true;

	$sql = "SELECT t1.id FROM {$_TABLES['DATABOX_base']} AS t1" ;
    if ($category_id<>0 ){
        $sql .= " ,{$_TABLES['DATABOX_category']} AS t2 ".LB;
    }


	$sql .=databox_buildSql_feed ($category_id, $limit);
    $result = DB_query ($sql);
    $num = DB_numRows ($result);

    $ids = array ();
    for ($i = 0; $i < $num; $i++) {
        $A = DB_fetchArray ($result);

        if ($A['id'] == $updated_id) {
            // this feed has to be updated - no further checks needed
            return false;
        }

        $ids[] = $A['id'];
    }
    $current = implode(',', $ids);

    return ($current != $update_data) ? false : true;
}


// +---------------------------------------------------------------------------+
// | 機能  feed用SQLの条件式の一部とORDER句を返す
// +---------------------------------------------------------------------------+
// | 引数 $category_id :                                                       |
// | 引数 $limits :(9 9件まで)または(9h 9時間前まで) default=10                |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
function databox_buildSql_feed(
    $category_id
    , $limits)
{
    global $_DB_dbms;//'pgsql' の場合の判定用 未テスト
    global $_DATABOX_CONF;

    $where = '';



    //管理者の時,下書データはのぞく
    $where .= " t1.draft_flag=0".LB;
    //アクセス権のないデータ はのぞく
    $where .= " AND t1.perm_anon > 0".LB;
    //公開日以前のデータはのぞく
    $where .= " AND (released <= NOW())".LB;
    //公開終了日を過ぎたデータはのぞく
    $where .= " AND (expired=0 OR expired > NOW())".LB;

    //カテゴリ
    if ($category_id != 0) {
        $where .= " AND t1.id=t2.id"  ;
        $where .= " AND category_id=" . addslashes($category_id) ;
    }


    //使用する日付（編集日付or作成日付or公開日
    $datefield=$_DATABOX_CONF['datefield'];

    $limitsql = '';
    if (!empty ($limits)) {
        if (substr ($limits, -1) == 'h') { // last xx hours
            $limitsql = '';
            $hours = substr ($limits, 0, -1);
            if (!empty ($where)) {
                $where .= ' AND ';
            }
            if($_DB_dbms=='pgsql') {$where .= $datefield ." >= (NOW() - INTERVAL '$hours HOURS')"; } else
            {$where .= $datefield ." >= DATE_SUB(NOW(),INTERVAL $hours HOUR)"; }
        } else {
            $limitsql = ' LIMIT ' . $limits;
        }
    } else {
        $limitsql = ' LIMIT 10';
    }

    if (!empty ($where)) {
        $where = ' WHERE ' . $where;
    }

    $sql = $where . ' ORDER BY '.$datefield . ' DESC' . $limitsql;

    return $sql;
}


/**
* Provide feed data
*
* @param    int     $feed       feed ID
* @param    ref     $link
* @param    ref     $update
* @return   array               feed entries
*
*/
// +---------------------------------------------------------------------------+
// | 機能  feedデータを返す
// +---------------------------------------------------------------------------+
// | 引数 $feed:feed 定義情報取(syndication の fid)
// | 引数 $$databox
// | 引数 $$update
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
function plugin_getfeedcontent_databox(
    $feed
    , &$databox
    , &$update)
{  
    global $_CONF;
    global $_TABLES;
    global $_DATABOX_CONF;
	
	$pi_name="databox";
    //使用する日付（編集日付or作成日付or公開日
    $datefield=$_DATABOX_CONF['datefield'];
    //-----
    $tbl1=$_TABLES['DATABOX_base'] ;
    $tbl2=$_TABLES['DATABOX_category'] ;

    //feed 定義情報取得　$S
    $sql=  "SELECT" ;
    $sql.= " topic"; //0 or category_id
    $sql.= ",limits"; // 9 or 9h
    $sql.= ",content_length ";
    $sql.= " FROM {$_TABLES['syndication']} ";
    $sql.= " WHERE ";
    $sql.= " fid = '$feed'" ;
    $result = DB_query($sql );
    $S = DB_fetchArray( $result );
	$category_id=$S['topic'];
	
	$sql = "SELECT ";
    $sql .= " t1.id ";
    $sql .= " ,t1.code";
    $sql .= " ,t1.owner_id";
    $sql .= " ,t1.title";
    $sql .= " ,t1.description";
    $sql .= " ,UNIX_TIMESTAMP(". $datefield .") AS modified " ;
    $sql .= " FROM " ;
    $sql .= " {$tbl1} AS t1 ".LB;

    if ($category_id<>0 ){
        $sql .= " ,{$tbl2} AS t2 ".LB;
    }

    $sql .= databox_buildSql_feed ($category_id, $S['limits']) ;

    $result = DB_query( $sql);

    $content = array();
    $ids = array();
    $nrows = DB_numRows( $result );

    for( $i = 0; $i < $nrows; $i++ )
    {
        $A = DB_fetchArray( $result );
        $id = COM_applyFilter($A['id']);
        $ids[] = $id;

        $title = stripslashes( $A['title'] );
		
		$desc =DATABOX_getfeaturedimage($id,$pi_name).LB;
		$desc .= PLG_replaceTags( stripslashes($A['description']) );
		
        $url=$_CONF['site_url'] . "/databox/data.php";
        $url.="?";
        //コード使用の時
        if ($_DATABOX_CONF['datacode']){
            $url.="code=".$A['code'];
            $url.="&amp;m=code";
        }else{
            $url.="id=".$A['id'];
            $url.="&amp;m=id";
        }
        $link = COM_buildUrl( $url );
		$format='html';
		$extensionTags = array(); 

        $content[] = array( 'title'   => $title
                            ,'summary' => $desc
                            ,'link'    => $link
                            ,'uid'     => $A['owner_id']
                            ,'author'  => COM_getDisplayName( $A['owner_id'] )
                            ,'date'    => $A[$datefield]
							,'format'  => $format
						);
		//           ,'extensions' => $extensionTags

    }
    return $content;
    }


?>
