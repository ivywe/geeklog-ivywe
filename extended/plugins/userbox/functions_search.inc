<?php


if (strpos ($_SERVER['PHP_SELF'], 'functuins_search.inc') !== false) {
    die ('This file can not be used on its own.');
}


// +---------------------------------------------------------------------------+
// | 機能  検索タイプ （コア標準検索のオプションのタイプ）
// +---------------------------------------------------------------------------+
// | 戻値 nomal:検索タイプ 
// +---------------------------------------------------------------------------+
function plugin_searchtypes_userbox()
{
	global $LANG_USERBOX_SEARCH;
    global $_USERBOX_CONF;
	
	//検索する＝はい　の時
	if ($_USERBOX_CONF['include_search'] == 1) {
		$tmp['userbox'] = $LANG_USERBOX_SEARCH['type'];
		return $tmp;
	}
}
// +---------------------------------------------------------------------------+
// | 機能  検索                                                                |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
function plugin_dopluginsearch_userbox
($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
    global $_CONF;
    global $_TABLES;
    global $LANG09;
    global $LANG_USERBOX_SEARCH;
    global $_USERBOX_CONF;
	
	if ($_USERBOX_CONF['include_search'] != 1) {
        return;
    }

    $search_results = array();

    $result = plugin_dopluginsearch_userbox_data(
        $query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage);
    if (is_array($result)) {
        $search_results = array_merge($search_results, $result);
    } else {
        $search_results[] = $result;
    }

    return $search_results;

}
//-----
function plugin_dopluginsearch_userbox_data(
    $query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
    global $_TABLES;
    global $LANG_USERBOX_SEARCH;

    $rt=DATABOX_getadditionsearchdef("userbox");

    // Make sure the query is SQL safe
    $query = trim(addslashes($query));

    $sql = LB;

    $sql .= "SELECT ".LB;

    $sql .= " t1.id".LB;
    $sql .= " ,t2.username AS title";
    $sql .= " ,t1.description".LB;
    $sql .= ", UNIX_TIMESTAMP(t1.created) AS date".LB;
    $sql .= ", owner_id AS uid ".LB;
    $sql .= ", t3.hits".LB;
    $sql .= ", CONCAT('/userbox/profile.php?id=', t1.id) AS url ".LB;

    $sql .= " FROM ";

    $sql .= " {$_TABLES['USERBOX_base']} AS t1";
	$sql .= " LEFT JOIN {$_TABLES['users']} AS t2  ON t1.id=t2.uid";
	$sql .= " LEFT JOIN {$_TABLES['USERBOX_stats']} AS t3  ON t1.id=t3.id";

    $sql .=$rt["sql3"];

    $sql .= " WHERE ";

    $sql .= " 1=1";

    $sql .= COM_getPermSQL('AND') . ' ';

    if (!empty ($author)) {
        $sql .= "AND (owner_id = '$author') ";
    }

    //
    if (!empty($datestart) && !empty($dateend)){
        $delim = substr($datestart, 4, 1);
        if (!empty($delim)) {
            $DS = explode($delim, $datestart);
            $DE = explode($delim, $dateend);
            $startdate = mktime(0,0,0,$DS[1],$DS[2],$DS[0]);
            $enddate = mktime(23,59,59,$DE[1],$DE[2],$DE[0]);
            $sql .= "AND (UNIX_TIMESTAMP(t1.created) BETWEEN '$startdate' AND '$enddate') ";
        }
    }
    if (!empty ($author)) {
        $sql .= "AND (owner_id = '$author') ";
    }

//$w ="sql=".$sql;
//COM_errorLog ($w, 1);

    $search = new SearchCriteria('userbox', $LANG_USERBOX_SEARCH['results_userbox']);

    $columns = array('title' => 't2.username'
    , 'description');

    $ary=$rt["columns"];
    for ($i = 1; $i <= count($ary); $i++) {
        $w=current($ary);
        $columns[] =$w;
        next($ary);
    }

//    , "((SELECT value FROM {$_TABLES['USERBOX_addition']} WHERE field_id=1 AND id=t1.id) AS fld1)"


    list($sql,$ftsql) = $search->buildSearchSQL($keyType, $query, $columns, $sql);
    $search->setSQL($sql);
    $search->setFTSQL($ftsql);
    $search->setRank(3);
    //$search->setURLRewrite(true);
    $search->setURLRewrite(false);

    return $search;
}




?>
