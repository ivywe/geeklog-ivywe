<?php


if (strpos ($_SERVER['PHP_SELF'], 'functuins_search.inc') !== false) {
    die ('This file can not be used on its own.');
}


// +---------------------------------------------------------------------------+
// | 機能  検索タイプ （コア標準検索のオプションのタイプ）
// +---------------------------------------------------------------------------+
// | 戻値 nomal:検索タイプ 
// +---------------------------------------------------------------------------+
function plugin_searchtypes_databox()
{
	global $LANG_DATABOX_SEARCH;
    global $_DATABOX_CONF;
	
	//検索する＝はい　の時
	if ($_DATABOX_CONF['include_search'] == 1) {
		$tmp['databox'] = $LANG_DATABOX_SEARCH['type'];
		return $tmp;
	}
}
// +---------------------------------------------------------------------------+
// | 機能  検索                                                                |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
function plugin_dopluginsearch_databox
($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
	global $_CONF;
	global $_TABLES;
	global $LANG09;
    global $LANG_DATABOX_SEARCH;
	global $_DATABOX_CONF;

	if ($_DATABOX_CONF['include_search'] != 1) {
        return;
    }

    $search_results = array();

    $result = plugin_dopluginsearch_databox_data(
        $query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage);
    if (is_array($result)) {
        $search_results = array_merge($search_results, $result);
    } else {
        $search_results[] = $result;
    }

    return $search_results;

}
//-----
function plugin_dopluginsearch_databox_data(
    $query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
    global $_TABLES;
    global $LANG_DATABOX_SEARCH;
    global $_DATABOX_CONF;

    $rt=DATABOX_getadditionsearchdef("databox");

    // Make sure the query is SQL safe
    $query = trim(addslashes($query));

    $sql = LB;

    $sql .= "SELECT ".LB;
    $sql .= " t1.id".LB;
    $sql .= " ,t1.code".LB;
    $sql .= " ,t1.title".LB;
    $sql .= " ,t1.description".LB;
    $sql .= ", UNIX_TIMESTAMP(t1.created) AS date".LB;
    $sql .= ", owner_id AS uid ".LB;
    $sql .= ", t3.hits".LB;
    if ($_DATABOX_CONF['datacode']){
        $sql .= ", CONCAT('/databox/data.php?code=', t1.code) AS url ".LB;
    }else{
        $sql .= ", CONCAT('/databox/data.php?id=', t1.id) AS url ".LB;
    }
    $sql .= " FROM ";

    $sql .= " {$_TABLES['DATABOX_base']} AS t1";
	$sql .= " LEFT JOIN {$_TABLES['DATABOX_stats']} AS t3  ON t1.id=t3.id";

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



    $search = new SearchCriteria('databox', $LANG_DATABOX_SEARCH['results_databox']);

    $columns = array('title' => 'title'
    , 'description');

    $ary=$rt["columns"];
    for ($i = 1; $i <= count($ary); $i++) {
        $w=current($ary);
        $columns[] =$w;
        next($ary);
    }


    list($sql,$ftsql) = $search->buildSearchSQL($keyType, $query, $columns, $sql);
//$w ="sql=".$sql;
//COM_errorLog ($w, 1);
    $search->setSQL($sql);
    $search->setFTSQL($ftsql);
    $search->setRank(3);
    //$search->setURLRewrite(true);
    $search->setURLRewrite(false);

    return $search;
}




?>
