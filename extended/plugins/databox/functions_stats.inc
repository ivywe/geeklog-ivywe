<?php


if (strpos ($_SERVER['PHP_SELF'], 'functuins_stats.inc') !== false) {
    die ('This file can not be used on its own.');
}


/**
* Shows the statistics for the links plugin on stats.php.
* If $showsitestats is 1 then we are to only print the overall stats in the
* 'site statistics box' otherwise we show the detailed stats
*
* @param    int     $showsitestate  Flag to let us know which stats to get
* @param    string                  HTML for the stats section
*
*/
function plugin_showstats_databox (
	$showsitestats)
{
	global $_CONF;
	global $_TABLES;
	global $LANG_DATABOX_STATS;
	global $_DATABOX_CONF;
	
    require_once $_CONF['path_system'] . 'lib-admin.php';
	
    $tbl1=$_TABLES['DATABOX_base'] ;
    $tbl2=$_TABLES['DATABOX_stats'] ;

    $retval = '';
	
	$sql = "SELECT t1.id,t1.title,t2.hits ,t1.code";
	
	$sql .= " FROM ".LB;
    $sql .= " {$tbl1} AS t1 ".LB;
    $sql .= " ,{$tbl2} AS t2 ".LB;

	$sql .= " WHERE " ;
	$sql .= " (t2.hits > 0)" ;
	$sql .= " AND (t2.id = t1.id)" ;
	$sql .= " AND " ;
    //@@@@@@@@@--------->
    //下書はのぞく
    $sql .= " t1.draft_flag=0".LB;
    //アクセス権のないデータ はのぞく
    $sql .= COM_getPermSql('AND').LB;
    //公開日以前のデータはのぞく
    $sql .= " AND (released <= NOW())".LB;
    //公開終了日を過ぎたデータはのぞく
    $sql .= " AND (expired=0 OR expired > NOW())".LB;
    //@@@@@@@@@<---------
	
	$sql .= " ORDER BY t2.hits DESC LIMIT 10";
	
    $result = DB_query($sql);
    $nrows  = DB_numRows ($result);
    if ($nrows > 0) {
        $header_arr = array(
            array('text'         => $LANG_DATABOX_STATS['stats_page_title'],
                  'field'        => 'sid',
                  'header_class' => 'stats-header-title'
            ),
            array('text'         => $LANG_DATABOX_STATS['stats_hits'],
                  'field'        => 'hits',
                  'header_class' => 'stats-header-count',
                  'field_class'  => 'stats-list-count'
            )
        );
        $data_arr = array();
        $text_arr = array('has_menu' => false,
                          'title'    => $LANG_DATABOX_STATS['stats_headline'],
        );
        for ($i = 0; $i < $nrows; $i++) {
            $A = DB_fetchArray ($result);
			$A = array_map('stripslashes', $A);
            $title = stripslashes (str_replace ('$', '&#36;', $A['title']));
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
            $url = COM_buildUrl( $url );
            $sid = COM_createLink($title, $url,array('title' => $title));
            $hits = COM_numberFormat ($A['hits']);
			
			$data_arr[] = array('title' => $title,
                                'sid' => $sid,
                                'hits' => $hits
                          );
        }
        $retval .= ADMIN_simpleList ('', $header_arr, $text_arr, $data_arr);
    } else {
        $retval .= COM_startBlock ($LANG_DATABOX_STATS['stats_headline']);
        $retval .= $LANG_DATABOX_STATS['stats_no_hits'];
        $retval .= COM_endBlock ();
    }

    return $retval;
}




/**
* New stats plugin API function for proper integration with the site stats
*
* @return   array(item text, item count);
*
*/
function plugin_statssummary_databox ()
{
    global $LANG_DATABOX_STATS;

    list($total_count, $total_clicks) = databox_countAndClicks ();

    $item_count = COM_NumberFormat ($total_count)
                . ' (' . COM_NumberFormat ($total_clicks) . ')';


    return array ($LANG_DATABOX_STATS['data'], $item_count);
}

function databox_countAndClicks()
{
    global $_TABLES;
	
    $tbl1=$_TABLES['DATABOX_base'] ;
    $tbl2=$_TABLES['DATABOX_stats'] ;

    $retval = '';
	
	$sql = "SELECT";
	$sql.=" COUNT(*) AS count";
	$sql.=",SUM(t2.hits) AS clicks ";
	
	$sql .= " FROM ".LB;
    $sql .= " {$tbl1} AS t1 ".LB;
    $sql .= "  LEFT JOIN {$tbl2} AS t2 ON t1.id = t2.id".LB;

	$sql .= " WHERE " ;
	
    //@@@@@@@@@--------->
    //下書はのぞく
    $sql .= " t1.draft_flag=0".LB;
    //アクセス権のないデータ はのぞく
    $sql .= COM_getPermSql('AND').LB;
    //公開日以前のデータはのぞく
    $sql .= " AND (released <= NOW())".LB;
    //公開終了日を過ぎたデータはのぞく
    $sql .= " AND (expired=0 OR expired > NOW())".LB;
    //@@@@@@@@@<---------
	
	
	
	$result = DB_query($sql);
    $A = DB_fetchArray($result);
	$A = array_map('stripslashes', $A);
    $total_count = $A['count'];
	$total_clicks = $A['clicks'];
    if (empty($total_clicks)) {
        $total_clicks = 0;
    }

    return array($total_count, $total_clicks);
}


?>
