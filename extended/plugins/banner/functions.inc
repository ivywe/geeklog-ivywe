<?php

// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | Banner Plugin 1.1                                                         |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// |                                                                           |
// | This file does two things: 1) it implements the necessary Geeklog Plugin  |
// | API method and 2) implements all the common code needed by the Banner     |
// | Plugins' PHP files.                                                       |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000-2010 by the following authors:                         |
// |                                                                           |
// | Authors: Hiroron            - hiroron AT hiroron DOT com                  |
// | Presented by:Ivy            - komma AT ivywe DOT co DOT jp                |
// |                               http://www.ivywe.co.jp                      |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is licensed under the terms of the GNU General Public License|
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                      |
// | See the GNU General Public License for more details.                      |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die('This file can not be used on its own.');
}

$plugin_path = $_CONF['path'] . 'plugins/banner/';
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';

if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}

/*
* Check and see if we need to load the plugin configuration
*/
if (!isset($_BAN_CONF['bannerloginrequired'])) {
    require_once $_CONF['path_system'] . 'classes/config.class.php';

    $ban_config = config::get_instance();
    $_BAN_CONF = $ban_config->get_config('banner');
}

$_BAN_CONF['version'] = '1.1.1';

$_TABLES['bannercategories'] = $_DB_table_prefix . 'bannercategories';
$_TABLES['banner'] = $_DB_table_prefix . 'banner';
$_TABLES['bannersubmission'] = $_DB_table_prefix . 'bannersubmission';

// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementation                                         |
// +---------------------------------------------------------------------------+

/**
* Returns the items for this plugin that should appear on the main menu
*
* NOTE: this MUST return the url/value pairs in the following format
* $<arrayname>[<label>] = <url>
*
* @return   mixed   menu entry, or boolean false if disabled / hidden
*
*/
function plugin_getmenuitems_banner()
{
    global $_CONF, $_USER, $_BAN_CONF, $LANG_BANNER;

    $anon = (empty ($_USER['uid']) || ($_USER['uid'] <= 1)) ? true : false;
    if (($_BAN_CONF['hidebannermenu'] == 1) || ($anon &&
                ($_CONF['loginrequired'] || $_BAN_CONF['bannerloginrequired']))) {
        return false;
    }

    $menuitems[$LANG_BANNER[114]] = $_CONF['site_url'] . '/banner/index.php';

    return $menuitems;
}

/**
* Return headlines for New Banner section in the What's New block, if enabled
*
* @return   mixed       array(headline, byline), or boolean false is disabled
*
*/
function plugin_whatsnewsupported_banner()
{
    global $_TABLES, $_BAN_CONF, $LANG_BANNER, $LANG_WHATSNEW;

    if ( $_BAN_CONF['hidenewbanner'] == 0 ) {
        $retval = array( $LANG_BANNER[84],
                         COM_formatTimeString( $LANG_WHATSNEW['new_last'],
                                               $_BAN_CONF['newbannerinterval'] )
                       );
    } else {
        $retval = false;
    }

    return $retval;
}

/**
* Return new banner for the What's New block
*
* @return   string  HTML list of new banner
*
*/
function plugin_getwhatsnew_banner()
{
    global $_CONF, $_TABLES, $_USER, $_BAN_CONF, $LANG_BANNER;

    $retval = '';

    // Get newest banner
    $sql = "SELECT bid,title,description,url FROM {$_TABLES['banner']} WHERE (date >= (DATE_SUB(NOW(), INTERVAL {$_BAN_CONF['newbannerinterval']} SECOND))) AND (publishstart IS NULL OR publishstart < NOW()) and (publishend IS NULL OR publishend > NOW())" . COM_getPermSQL( 'AND' ) . ' ORDER BY date DESC LIMIT 15';
    $result = DB_query($sql);
    $nrows = DB_numRows($result);

    if ($nrows > 0) {
        $newbanner = array();
        for ($x = 0; $x < $nrows; $x++) {
            $A = DB_fetchArray($result);
            $A['title'] = stripslashes($A['title']);
            $flg_link = empty($A['url']) ? false : true ;
            $newbanner[] = banner_buildBanner($A['bid'], $A['title'], $A['description'], $flg_link) . LB;
        }

        $retval .= COM_makeList($newbanner, 'list-new-plugins');
    } else {
        $retval .= $LANG_BANNER[88]  . LB;
    }

    return $retval;
}

/**
* Implements the [banner:] [randombanner:] autotag.
*
* @param    string  $op         operation to perform
* @param    string  $content    item (e.g. story text), including the autotag
* @param    array   $autotag    parameters used in the autotag
* @param    mixed               tag names (for $op='tagname') or formatted content
*
*/
function plugin_autotags_banner($op, $content = '', $autotag = '')
{
    global $_CONF, $_TABLES, $LANG_DIRECTION;

    if ($op == 'tagname' ) {
        return array('banner','randombanner','bannercategory');
    } else if ($op == 'parse') {
        $banner = ''; $bannertitle = '';

        switch( $autotag['tag'] ) {
            case 'banner' :
                $bid = COM_applyFilter($autotag['parm1']);
                $bannertitle = DB_getItem($_TABLES['banner'], 'title', "bid = '{$bid}' AND (publishstart IS NULL OR publishstart < NOW()) and (publishend IS NULL OR publishend > NOW())");
                if (!empty($bannertitle)) {
                    $bannerimg = DB_getItem($_TABLES['banner'], 'description', "bid = '{$bid}' AND (publishstart IS NULL OR publishstart < NOW()) and (publishend IS NULL OR publishend > NOW())");
                    if (!empty($autotag['parm2'])) {
                        $bannertitle = $autotag['parm2'];
                    }
                    $bannerurl = DB_getItem($_TABLES['banner'], 'url', "bid = '{$bid}' AND (publishstart IS NULL OR publishstart < NOW()) and (publishend IS NULL OR publishend > NOW())");
                    $flg_link = empty($bannerurl) ? false : true ;
                    $banner = banner_buildBanner($bid, $bannertitle, $bannerimg, $flg_link);
                }
                break;
            case 'randombanner' :
                $cid = COM_applyFilter($autotag['parm1']);
                $result = DB_query("SELECT b.bid, b.title, b.description, b.url FROM {$_TABLES['banner']} AS b LEFT JOIN {$_TABLES['bannercategories']} AS c ON b.cid=c.cid WHERE c.cid='{$cid}' AND (b.publishstart IS NULL OR b.publishstart < NOW()) and (b.publishend IS NULL OR b.publishend > NOW())" . COM_getPermSQL('AND', 0, 2, 'c') . ' ORDER BY RAND() LIMIT 1');
                $nrows = DB_numRows($result);
                if ($nrows > 0) {
                    list($bid, $bannertitle, $bannerimg) = DB_fetchArray($result);
                    if (!empty($autotag['parm2'])) {
                        $bannertitle = $autotag['parm2'];
                    }
                    $flg_link = empty($A['url']) ? false : true ;
                    $banner = banner_buildBanner($bid, $bannertitle, $bannerimg, $flg_link);
                }
                break;
            case 'bannercategory' :
                $cid = COM_applyFilter($autotag['parm1']);
                $result = DB_query("SELECT b.bid, b.title, b.description, b.url FROM {$_TABLES['banner']} AS b LEFT JOIN {$_TABLES['bannercategories']} AS c ON b.cid=c.cid WHERE c.cid='{$cid}' AND (b.publishstart IS NULL OR b.publishstart < NOW()) and (b.publishend IS NULL OR b.publishend > NOW())" . COM_getPermSQL('AND', 0, 2, 'c') . ' ORDER BY RAND()');
                $nrows = DB_numRows($result);
                if ($nrows > 0) {
                    $format = '%s';
                    if (!empty($autotag['parm2']) && strpos($autotag['parm2'], '%s') !== false) {
                        $format = $autotag['parm2'];
                    }
                    $format = COM_checkHTML (COM_checkWords ($format) );
                    for ($i = 0; $i < $nrows; $i++) {
                        $A = DB_fetchArray($result);
                        $bid = $A['bid'];
                        $bannertitle = stripslashes($A['title']);
                        $bannerimg = stripslashes($A['description']);
                        $flg_link = empty($A['url']) ? false : true ;
                        $build = banner_buildBanner($bid, $bannertitle, $bannerimg, $flg_link);
                        $banner .= sprintf($format, $build);
                    }
                }
                break;
        }
        
        $content = str_replace($autotag['tagstr'], $banner, $content);
        return $content;
    }
}

/**
* Called by the plugin Editor to display the current plugin code version
* This may be different than the version installed and registered currently.
* If newer then you may want to run the update
*
* @return   string  version number
*
*/
function plugin_chkVersion_banner ()
{
    global $_BAN_CONF;

    return $_BAN_CONF['version'];
}

/**
* Helper function: count number of banner and total number of clicks
*
* @return   array(number of banner, number of clicks);
*
*/
function BANNER_countBannerAndClicks ()
{
    global $_TABLES;

    $result = DB_query ("SELECT COUNT(*) AS count,SUM(hits) AS clicks FROM {$_TABLES['banner']} WHERE  (publishstart IS NULL OR publishstart < NOW()) and (publishend IS NULL OR publishend > NOW())" . COM_getPermSQL ('AND'));
    $A = DB_fetchArray ($result);
    $total_banner = $A['count'];
    $total_clicks = $A['clicks'];
    if (empty ($total_clicks)) {
        $total_clicks = 0;
    }

    return array ($total_banner, $total_clicks);
}

/**
* Shows the statistics for the banner plugin on stats.php.
* If $showsitestats is 1 then we are to only print the overall stats in the
* 'site statistics box' otherwise we show the detailed stats
*
* @param    int     $showsitestate  Flag to let us know which stats to get
* @param    string                  HTML for the stats section
*
*/
function plugin_showstats_banner ($showsitestats)
{
    global $_CONF, $_TABLES, $LANG_BANNER_STATS;

    require_once ($_CONF['path_system'] . 'lib-admin.php');

    $retval = '';

    $result = DB_query ("SELECT bid,url,title,hits,description FROM {$_TABLES['banner']} WHERE (hits > 0) AND (publishstart IS NULL OR publishstart < NOW()) and (publishend IS NULL OR publishend > NOW())" . COM_getPermSQL ('AND') . " ORDER BY hits DESC LIMIT 10");
    $nrows  = DB_numRows ($result);
    if ($nrows > 0) {
        $header_arr = array(
            array('text'         => $LANG_BANNER_STATS['stats_page_title'],
                  'field'        => 'sid',
                  'header_class' => 'stats-header-title'
            ),
            array('text'         => $LANG_BANNER_STATS['stats_hits'],
                  'field'        => 'hits',
                  'header_class' => 'stats-header-count',
                  'field_class'  => 'stats-list-count'
            )
        );
        $data_arr = array();
        $text_arr = array('has_menu' => false,
                          'title'    => $LANG_BANNER_STATS['stats_headline'],
        );
        for ($i = 0; $i < $nrows; $i++) {
            $A = DB_fetchArray ($result);
            $title = stripslashes (str_replace ('$', '&#36;', $A['title']));
            $flg_link = empty($A['url']) ? false : true ;
            $sid = banner_buildBanner($A['bid'], $title, $A['description'], $flg_link);
            $hits = COM_numberFormat ($A['hits']);
            $data_arr[] = array('title' => $title,
                                'sid' => $sid,
                                'hits' => $hits
                          );
        }
        $retval .= ADMIN_simpleList ('', $header_arr, $text_arr, $data_arr);
    } else {
        $retval .= COM_startBlock ($LANG_BANNER_STATS['stats_headline']);
        $retval .= $LANG_BANNER_STATS['stats_no_hits'];
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
function plugin_statssummary_banner ()
{
    global $LANG_BANNER_STATS;

    list($total_banner, $total_clicks) = BANNER_countBannerAndClicks ();

    $item_count = COM_NumberFormat ($total_banner)
                . ' (' . COM_NumberFormat ($total_clicks) . ')';


    return array ($LANG_BANNER_STATS['banner'], $item_count);
}

/**
* Geeklog is asking us to provide any items that show up in the type
* drop-down on search.php.  Let's users search for banner.
*
* @return   array   (plugin name/entry title) pair for the dropdown
*
*/
function plugin_searchtypes_banner()
{
    global $LANG_BANNER;

    return;

    $tmp['banner'] = $LANG_BANNER[14];

    return $tmp;
}


/**
* This searches for banner matching the user query and returns an array for the
* header and table rows back to search.php where it will be formated and printed
*
* @param    string  $query      Keywords user is looking for
* @param    date    $datestart  Start date to get results for
* @param    date    $dateend    End date to get results for
* @param    string  $topic      The topic they were searching in
* @param    string  $type       Type of items they are searching, or 'all'
* @param    int     $author     Get all results by this author
* @param    string  $keyType    search key type: 'all', 'phrase', 'any'
* @param    int     $page       page number of current search
* @param    int     $perpage    number of results per page
* @return   object              search result object
*
*/
function plugin_dopluginsearch_banner($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
    global $_CONF, $_TABLES, $LANG_BANNER_SEARCH;

    return;

    if (empty ($type)) {
        $type = 'all';
    }

    // Bail if we aren't supppose to do our search
    if ($type <> 'all' AND $type <> 'banner') {
        $plugin_results = new Plugin();
        $plugin_results->plugin_name = 'banner';
        $plugin_results->searchlabel = $LANG_BANNER_SEARCH['results'];

        return $plugin_results;
    }

    // banner search
    $select = 'SELECT bid,title,description,owner_id,hits,UNIX_TIMESTAMP(date) AS day,url';
    $sql = " FROM {$_TABLES['banner']} WHERE date <> 1";

    if (!empty ($query)) {
        if ($keyType == 'phrase') {
            $mysearchterm = addslashes ($query);
            $sql .= " AND ((description LIKE '%$mysearchterm%')"
                 . " OR (title LIKE '%$mysearchterm%'))";
        } else if ($keyType == 'all') {
            $mywords = explode (' ', $query);
            $sql .= ' AND (';
            $tmp = '';
            foreach ($mywords AS $mysearchterm) {
                $mysearchterm = addslashes (trim ($mysearchterm));
                if (!empty ($mysearchterm)) {
                    $tmp .= "((description LIKE '%$mysearchterm%')"
                         . " OR (title LIKE '%$mysearchterm%')) AND ";
                }
            }
            $tmp = substr ($tmp, 0, strlen ($tmp) - 5);
            $sql .= $tmp . ')';
        } else if ($keyType == 'any') {
            $mywords = explode (' ', $query);
            $sql .= ' AND (';
            $tmp = '';
            foreach ($mywords AS $mysearchterm) {
                $mysearchterm = addslashes (trim ($mysearchterm));
                if (!empty ($mysearchterm)) {
                    $tmp .= "((description LIKE '%$mysearchterm%')"
                         . " OR (title LIKE '%$mysearchterm%')) OR ";
                }
            }
            $tmp = substr ($tmp, 0, strlen ($tmp) - 4);
            $sql .= $tmp . ')';
        } else {  // quick search
            $mysearchterm = addslashes ($query);
            $sql .= " AND ((description LIKE '%$mysearchterm%')"
                 . " OR (title LIKE '%$mysearchterm%'))";
        }
    }

    if (!empty ($datestart) && !empty ($dateend)) {
        $delim = substr ($datestart, 4, 1);
        if (!empty($delim)) {
            $DS = explode ($delim, $datestart);
            $DE = explode ($delim, $dateend);
            $startdate = mktime (0, 0, 0, $DS[1], $DS[2], $DS[0]);
            $enddate = mktime (23, 59, 59, $DE[1], $DE[2], $DE[0]);
            $sql .= " AND (UNIX_TIMESTAMP(date) BETWEEN '$startdate' AND '$enddate')";
        }
    }

    if (!empty ($author)) {
        $sql .= " AND (owner_id = '$author')";
    }
    $sql .= ' AND (publishstart IS NULL OR publishstart < NOW()) and (publishend IS NULL OR publishend > NOW())';
    $sql .= COM_getPermSQL('AND');
    $sql .= ' GROUP BY date, bid, cid, url, description, title, hits, owner_id, group_id, perm_owner, perm_group, perm_members, perm_anon ORDER BY date DESC';
    $l = ($perpage * $page) - $perpage;
    $sql .= ' LIMIT ' . $l . ',' . $perpage;

    // Perform search
    $result = DB_query ($select . $sql);
    $mycount = DB_numRows ($result);
    $result_count = DB_query ('SELECT COUNT(*)' . $sql);
    $B = DB_fetchArray ($result_count, true);

    $plugin_results = new Plugin();
    $plugin_results->plugin_name = 'banner';
    $plugin_results->searchlabel = $LANG_BANNER_SEARCH['results'];
    $plugin_results->addSearchHeading ($LANG_BANNER_SEARCH['title']);
    $plugin_results->addSearchHeading ($LANG_BANNER_SEARCH['date']);
    $plugin_results->addSearchHeading ($LANG_BANNER_SEARCH['author']);
    $plugin_results->addSearchHeading ($LANG_BANNER_SEARCH['hits']);
    $plugin_results->num_searchresults = 0;
    $plugin_results->num_itemssearched = $B[0];
    $plugin_results->supports_paging = true;

    // NOTE if any of your data items need to be banner then add them here!
    // make sure data elements are in an array and in the same order as your
    // headings above!
    for ($i = 0; $i < $mycount; $i++) {
        $A = DB_fetchArray ($result);

        $thetime = COM_getUserDateTimeFormat ($A['day']);
        $A['title'] = stripslashes ($A['title']);
        $submitter = COM_getDisplayName ($A['owner_id']);
        if ($A['owner_id'] > 1) {
            $profile_url = $_CONF['site_url']
                     . '/users.php?mode=profile&amp;uid=' . $A['owner_id'];
            $profile = COM_createLink($submitter, $profile_url);
        } else {
            $profile = $submitter;
        }
        $flg_link = empty($A['url']) ? false : true ;
        $anchor = banner_buildBanner($A['bid'], $A['title'], $A['description'], $flg_link);
        $row = array ($anchor, $thetime[0], $profile,
                      COM_NumberFormat ($A['hits']));
        $plugin_results->addSearchResult ($row);
        $plugin_results->num_searchresults++;
    }

    return $plugin_results;
}


/**
* This will put an option for banner in the command and control block on
* moderation.php
*
* @return   mixed   array(title, url, icon), or boolean false when not allowed
*
*/
function plugin_cclabel_banner()
{
    global $_CONF, $LANG_BANNER;

    if (SEC_hasRights ('banner.edit')) {
        return array ($LANG_BANNER[14],
                      $_CONF['site_admin_url'] . '/plugins/banner/index.php',
                      plugin_geticon_banner ());
    }

    return false;
}

/**
* returns the administrative option for this plugin
*
* @return   mixed   array(title, url, num. banner), or void when not allowed
*
*/
function plugin_getadminoption_banner()
{
    global $_CONF, $_TABLES, $LANG_BANNER;

    if (SEC_hasRights ('banner.edit,banner.delete', 'OR')) {
        $total_banner = DB_getItem ($_TABLES['banner'], 'COUNT(*)',
                                   COM_getPermSql (''));

        return array ($LANG_BANNER[14],
                      $_CONF['site_admin_url'] . '/plugins/banner/index.php',
                      $total_banner);
    }
}

/**
* A user is about to be deleted. Update ownership of any banner owned
* by that user or delete them.
*
* @param    int     $uid    User id of deleted user
* @return   void
*
*/
function plugin_user_delete_banner ($uid)
{
    global $_TABLES, $_BAN_CONF;

    if (DB_count ($_TABLES['banner'], 'owner_id', $uid) == 0) {
        return;
    }

    if ($_BAN_CONF['delete_banner'] == 1) {
        // delete the banner
        DB_query ("DELETE FROM {$_TABLES['banner']} WHERE owner_id = $uid");
    } else {
        // assign ownership to a user from the Root group
        $rootgroup = DB_getItem ($_TABLES['groups'], 'grp_id',
                                 "grp_name = 'Root'");
        $result = DB_query ("SELECT DISTINCT ug_uid FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id = $rootgroup ORDER BY ug_uid LIMIT 1");
        $A = DB_fetchArray ($result);
        $rootuser = $A['ug_uid'];
        DB_query ("UPDATE {$_TABLES['banner']} SET owner_id = $rootuser WHERE owner_id = $uid");
    }
}

/**
* Do we support feeds?
*
* @return   array   id/name pairs of all supported feeds
*
*/
function plugin_getfeednames_banner()
{
    global $_TABLES;

    $feeds = array ();

    $result = DB_query ("SELECT cid,category FROM {$_TABLES['bannercategories']} GROUP BY category ORDER BY category ASC");
    $num = DB_numRows ($result);

    if ($num > 0) {
        $feeds[] = array ('id' => 'all', 'name' => 'all categories');

        for ($i = 0; $i < $num; $i++) {
            $A = DB_fetchArray ($result);
            $feeds[] = array ('id' => $A['cid'], 'name' => $A['category']);
        }
    }

    return $feeds;
}

/**
* Provide feed data
*
* @param    int     $feed       feed ID
* @param    ref     $banner
* @param    ref     $update
* @return   array               feed entries
*
*/
function plugin_getfeedcontent_banner ($feed, &$banner, &$update)
{
    global $_CONF, $_TABLES;

    $result = DB_query( "SELECT topic,limits,content_length FROM {$_TABLES['syndication']} WHERE fid = '$feed'" );
    $S = DB_fetchArray( $result );

    $result = DB_query( "SELECT bid,owner_id,title,description,UNIX_TIMESTAMP(date) AS modified FROM " . $_TABLES['banner'] . banner_buildSql ($S['topic'], $S['limits']) );

    $content = array();
    $bids = array();
    $nrows = DB_numRows( $result );

    for( $i = 0; $i < $nrows; $i++ )
    {
        $row = DB_fetchArray( $result );
        $bids[] = $row['bid'];

        $bannertitle = stripslashes( $row['title'] );
        $bannerdesc = stripslashes( $row['description'] );

        $bannerlink = COM_buildUrl( $_CONF['site_url']
                  . '/banner/portal.php?what=banner&amp;item=' . $row['bid'] );

        $content[] = array( 'title'   => $bannertitle,
                            'summary' => $bannerdesc,
                            'link'    => $bannerlink,
                            'uid'     => $row['owner_id'],
                            'author'  => COM_getDisplayName( $row['owner_id'] ),
                            'date'    => $row['modified'],
                            'format'  => 'plaintext'
                          );
    }

    $link = $_CONF['site_url'] . '/banner/index.php';
    $update = implode( ',', $bids );

    return $content;
}

/**
* Helper function: Build part of an SQL request
*
* @param    string  $cid        category id
* @param    string  $limits     limit (number of entries or number of hours)
* @return   string              part of an SQL request
*
*/
function banner_buildSql($cid, $limits)
{
    $where = '';
    if ($cid != 'all') {
        $where = "cid='" . addslashes($cid) . "'";
    }

    $limitsql = '';
    if (!empty ($limits)) {
        if (substr ($limits, -1) == 'h') { // last xx hours
            $limitsql = '';
            $hours = substr ($limits, 0, -1);
            if (!empty ($where)) {
                $where .= ' AND ';
            }
            $where .= "date >= DATE_SUB(NOW(),INTERVAL $hours HOUR)";
        } else {
            $limitsql = ' LIMIT ' . $limits;
        }
    }
    else
    {
        $limitsql = ' LIMIT 10';
    }

    if (!empty ($where)) {
        $where = ' WHERE ' . $where;
    }
    $where .= ' AND (publishstart IS NULL OR publishstart < NOW()) and (publishend IS NULL OR publishend > NOW())';

    $sql = $where . ' ORDER BY date DESC' . $limitsql;

    return $sql;
}

/**
* Checking if banner feeds are up to date
*
* @param    int     $feed           id of feed to be checked
* @param    string  $topic          topic (actually: cid)
* @param    string  $update_data    data describing current feed contents
* @param    string  $limit          number of entries or number of hours
* @param    string  $updated_type   (optional) type of feed to be updated
* @param    string  $updated_topic  (optional) feed's "topic" to be updated
* @param    string  $updated_id     (optional) id of entry that has changed
* @return   boolean                 true: feed data is up to date; false: isn't
*
*/
function plugin_feedupdatecheck_banner ($feed, $topic, $update_data, $limit, $updated_type = '', $updated_topic = '', $updated_id = '')
{
    global $_TABLES;

    $is_current = true;

    if ($updated_type != 'banner') {
        // we're not interested
        $updated_type = '';
        $updated_topic = '';
        $updated_id = '';
    }

    $sql = "SELECT bid FROM {$_TABLES['banner']}" . banner_buildSql ($topic, $limit);
    $result = DB_query ($sql);
    $num = DB_numRows ($result);

    $bids = array ();
    for ($i = 0; $i < $num; $i++) {
        $A = DB_fetchArray ($result);

        if ($A['bid'] == $updated_id) {
            // this feed has to be updated - no further checks needed
            return false;
        }

        $bids[] = $A['bid'];
    }
    $current = implode (',', $bids);

    return ($current != $update_data) ? false : true;
}

/**
* Update the Banner plugin
*
* @return   int     Number of message to display (true = generic success msg)
*
*/
function plugin_upgrade_banner()
{
    global $_TABLES, $_BAN_CONF;

    // the plugin needs these function so complain when they don't exist
    if (!function_exists('PLG_uninstall') ||
            !function_exists('COM_createLink') ||
            !function_exists('COM_showMessageText')) {
        return 3002;
    }

    $pi_name = 'banner';
    $currentVersion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name='$pi_name'");

    // Do upgrading
    $c = config::get_instance();
    switch ( $currentVersion ) {
        case '1.0.0':
        case '1.0.1':
        case '1.1.0':
            $c->del('fs_permissions', $pi_name);
            $c->del('default_permissions', $pi_name);
            $c->add('admin_editlink', true, 'select', 0, 1, 1, 150, true, $pi_name);
            $c->add('fs_adminlist', NULL, 'fieldset', 0, 2, NULL, 0, true, $pi_name);
            $c->add('admin_disptitle', true, 'select', 0, 2, 1, 200, true, $pi_name);
            $c->add('admin_dispdescription', true, 'select', 0, 2, 1, 210, true, $pi_name);
            $c->add('admin_dispaccess', true, 'select', 0, 2, 1, 220, true, $pi_name);
            $c->add('admin_dispcategory', true, 'select', 0, 2, 1, 230, true, $pi_name);
            $c->add('admin_disppublishstart', true, 'select', 0, 2, 1, 240, true, $pi_name);
            $c->add('admin_disppublishend', true, 'select', 0, 2, 1, 250, true, $pi_name);
            $c->add('admin_disphits', true, 'select', 0, 2, 1, 260, true, $pi_name);
            $c->add('fs_permissions', NULL, 'fieldset', 0, 3, NULL, 0, true, $pi_name);
            $c->add('default_permissions', $_BAN_CONF['default_permissions'], '@select', 0, 3, 12, 300, true, $pi_name);
        default :
            break;
    }

    // Update plugin info
    $pi_version      = $_BAN_CONF['version'];
    $gl_version      = '1.5.2'; // GL Version ( admin/install.php )
    
    $result = DB_query("UPDATE {$_TABLES['plugins']} SET pi_version = \"{$pi_version}\", pi_gl_version = \"{$gl_version}\" WHERE (pi_name = '{$pi_name}')");
    COM_errorLog($pi_name . " plugin was successfully updated to version {$pi_version}.");

    return true;
}

/**
* Geeklog informs us that we're about to be enabled or disabled
*
* @param    boolean     $enable     true = we're being enabled, false = disabled
* @return   void
*
*/
function plugin_enablestatechange_banner ($enable)
{
    global $_TABLES;

    $is_enabled = $enable ? 1 : 0;

    // toggle banner feeds
    DB_query ("UPDATE {$_TABLES['syndication']} SET is_enabled = $is_enabled WHERE type = 'banner'");
}

/**
* Counts the items that are submitted
*
* @return   int     number of items in submission queue
*
*/
function plugin_submissioncount_banner()
{
    global $_TABLES;

    $retval = 0;

    if (plugin_ismoderator_banner ()) {
        $retval = DB_count ($_TABLES['bannersubmission']);
    }

    return $retval;
}

/**
* Checks that the current user has the rights to moderate the
* plugin, returns true if this is the case, false otherwise
*
* @return        boolean       Returns true if moderator
*
*/
function plugin_ismoderator_banner()
{
    return SEC_hasRights ('banner.moderate');
}


/**
* Returns SQL & Language texts to moderation.php
*
* @return   mixed   plugin object or void if not allowed
*
*/
function plugin_itemlist_banner()
{
    global $_TABLES, $LANG_BANNER_SUBMIT;

    if (plugin_ismoderator_banner()) {
        $plugin = new Plugin();
        $plugin->submissionlabel = $LANG_BANNER_SUBMIT[11];
        $plugin->submissionhelpfile = 'ccbannersubmission.html';
        $plugin->getsubmissionssql = "SELECT bid AS id,title,cid AS category,url FROM {$_TABLES['bannersubmission']} ORDER BY title ASC";
        $plugin->addSubmissionHeading($LANG_BANNER_SUBMIT[8]);
        $plugin->addSubmissionHeading($LANG_BANNER_SUBMIT[10]);
        $plugin->addSubmissionHeading($LANG_BANNER_SUBMIT[9]);

        return $plugin;
    }
}

/**
* returns list of moderation values
*
* The array returned contains (in order): the row 'id' label, main plugin
* table, moderation fields (comma seperated), and plugin submission table
*
* @return       array        Returns array of useful moderation values
*
*/
function plugin_moderationvalues_banner()
{
    global $_TABLES;

    return array ('bid',
                  $_TABLES['banner'],
                  'bid,cid,url,description,title,date,owner_id',
                  $_TABLES['bannersubmission']);
}


/**
* Performs plugin exclusive work for items approved by moderation
*
* While moderation.php handles the actual move from bannersubmission
* to banner tables, within the function we handle all other approval
* relate tasks
*
* @param    string  $id     Identifying string
* @return   string          Any wanted HTML output
*
*/
function plugin_moderationapprove_banner($id)
{
    global $_TABLES, $_USER, $_GROUPS, $_BAN_CONF;

    // The bannersubmission only keeps track of the submitter's uid, but not
    // of grous and permissions. So set those to sensible defaults.

    if (isset($_GROUPS['Banner Admin'])) {
        $group_id = $_GROUPS['Banner Admin'];
    } else {
        $group_id = SEC_getFeatureGroup('banner.moderate');
    }

    $A = array();
    SEC_setDefaultPermissions($A, $_BAN_CONF['default_permissions']);

    DB_query("UPDATE {$_TABLES['banner']} SET group_id = '$group_id', perm_owner = '{$A['perm_owner']}', perm_group = '{$A['perm_group']}', perm_members = '{$A['perm_members']}', perm_anon = '{$A['perm_anon']}' WHERE bid = '$id'");

    return '';
}

/**
* Performs plugin exclusive work for items deleted by moderation
*
* While moderation.php handles the actual removal from <plugin>submission
* table, within this function we handle all other deletion
* related tasks
*
* @param    string  $id     Identifying string
* @return   string          Any wanted HTML output
*
*/
function plugin_moderationdelete_banner($id)
{
    global $_TABLES;

    // these tables should not contain any rows with ml_id = $id
    // this is done 'just in case'
    DB_delete ($_TABLES['bannersubmission'], 'bid', $id);

    return '';
}

/**
* Check submission form values and save if OK. Else show form again
*
* @param    array   $A  The banner record
* @return   string      Any wanted HTML output
*
*/
function plugin_savesubmission_banner($A)
{
    global $LANG12;

    $retval = '';

    if (!empty ($A['title']) && !empty ($A['description']) &&
                !empty ($A['url'])) {
        $retval = plugin_save_submit_banner ($A);
    } else {
        $retval .= COM_siteHeader()
            . COM_startBlock ($LANG12[22], '',
                           COM_getBlockTemplate ('_msg_block', 'header'))
            . $LANG12[23]
            . COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'))
            . submissionform('banner')
            . COM_siteFooter ();
    }

    return $retval;
}

/**
* Shows banner submission form
*
* @return   string  HTML for the banner submission form
*
*/
function plugin_submit_banner()
{
    global $_CONF, $LANG_BANNER_SUBMIT, $LANG12;

    $retval = COM_startBlock ($LANG_BANNER_SUBMIT[1], 'submitbanner.html');

    $bannerform = new Template($_CONF['path'] . 'plugins/banner/templates');
    $bannerform->set_file('bannerform', 'submitbanner.thtml');
    $bannerform->set_var('site_url', $_CONF['site_url']);
    $bannerform->set_var('layout_url', $_CONF['layout_url']);
    $bannerform->set_var('lang_title', $LANG12[10]);
    $bannerform->set_var('lang_banner', $LANG_BANNER_SUBMIT[2]);
    $bannerform->set_var('lang_category', $LANG_BANNER_SUBMIT[3]);
    $category = '';
    if (isset($_REQUEST['cid'])) {
        $category = $_REQUEST['cid'];
    }
    $bannerform->set_var('banner_category_options', banner_select_box(2, $category));
    $bannerform->set_var('lang_description', $LANG12[15]);
    $bannerform->set_var('lang_htmlnotallowed', $LANG12[35]);
    $bannerform->set_var('lang_submit', $LANG12[8]);
    $bannerform->set_var('max_url_length', 255);
    $bannerform->set_var('xhtml', XHTML);
    $bannerform->parse('theform', 'bannerform');
    $retval .= $bannerform->finish($bannerform->get_var('theform'));
    $retval .= COM_endBlock();

    return $retval;
}

/**
* Saves a banner submission
*
* @param    array   $A  Data for that submission
* @return   string      HTML redirect
*
*/
function plugin_save_submit_banner($A)
{
    global $_CONF, $_TABLES, $_USER, $_BAN_CONF, $LANG12;

    $retval = '';

    // pseudo-formatted banner description for the spam check
    $spamcheck = '<p>'. COM_createLink($A['title'], $A['url']) .' ('
               . $A['categorydd'] . ')' . $A['description']
               . '</p>';
    $result = PLG_checkforSpam($spamcheck, $_CONF['spamx']);
    if ($result > 0) {
        COM_updateSpeedlimit('submit');
        COM_displayMessageAndAbort($result, 'spamx', 403, 'Forbidden');
    }

    $A['cid'] = strip_tags(COM_stripslashes($A['categorydd']));

    $validcat = false;
    if (!empty($A['cid'])) {
        $cid = addslashes($A['cid']);
        $cat = DB_getItem($_TABLES['bannercategories'], 'category',
                          "cid = '$cid'");
        if (!empty($cat)) {
            $validcat = true;
        }
    }
    if (!$validcat) {
        $retval .= COM_startBlock($LANG12[22], '',
                       COM_getBlockTemplate('_msg_block', 'header'))
                . $LANG12[23]
                . COM_endBlock(COM_getBlockTemplate('_msg_block', 'footer'))
                . submissionform('banner')
                . COM_siteFooter();

        return $retval;
    }

    $A['cid'] = addslashes($A['cid']);
    $A['description'] = addslashes(htmlspecialchars(COM_checkWords($A['description'])));
    $A['title'] = addslashes(strip_tags(COM_checkWords($A['title'])));
    $A['url'] = addslashes(COM_sanitizeUrl($A['url']));
    $A['bid'] = addslashes(COM_makeSid());

    COM_updateSpeedlimit('submit');
    if (COM_isAnonUser()) {
        $owner_id = 1; // anonymous user
    } else {
        $owner_id = $_USER['uid'];
    }

    if (($_BAN_CONF['bannersubmission'] == 1) && !SEC_hasRights('banner.submit')) {
        $result = DB_save($_TABLES['bannersubmission'],
                    'bid,cid,url,description,title,date,owner_id',
                    "{$A['bid']},'{$A['cid']}','{$A['url']}','{$A['description']}','{$A['title']}',NOW(),$owner_id");

        if ($_BAN_CONF['notification'] == 1) {
            BANNER_sendNotification($_TABLES['bannersubmission'], $A);
        }

        $retval = COM_refresh($_CONF['site_url']
                              . '/index.php?msg=1&amp;plugin=banner');

    } else { // add banner directly

        if (SEC_hasRights('banner.submit')) {
            $A['group_id'] = SEC_getFeatureGroup('banner.submit');
        } else {
            $A['group_id'] = DB_getItem($_TABLES['groups'], 'grp_id',
                                        "grp_name = 'All Users'");
        }
        SEC_setDefaultPermissions($A, $_BAN_CONF['default_permissions']);

        $result = DB_save($_TABLES['banner'],
                    'bid,cid,url,description,title,date,owner_id,group_id,perm_owner,perm_group,perm_members,perm_anon',
                    "{$A['bid']},'{$A['cid']}','{$A['url']}','{$A['description']}','{$A['title']}',NOW(),$owner_id,{$A['group_id']},{$A['perm_owner']},{$A['perm_group']},{$A['perm_members']},{$A['perm_anon']}");

        if ($_BAN_CONF['notification'] == 1) {
            BANNER_sendNotification($_TABLES['banner'], $A);
        }
        COM_rdfUpToDateCheck('banner', $A['cid'], $A['bid']);

        $retval = COM_refresh($_CONF['site_url']
                              . '/index.php?msg=4&amp;plugin=banner');
    }

    return $retval;
}

/**
* Send an email notification for a new submission.
*
* @param    string  $table  Table where the new submission can be found
* @param    array   $A      submission data
*
*/
function BANNER_sendNotification ($table, $A)
{
    global $_CONF, $_TABLES, $LANG_BANNER, $LANG_BANNER_SUBMIT, $LANG08;

    $title = stripslashes ($A['title']);
    $description = stripslashes ($A['description']);

    $mailbody = "$LANG_BANNER_SUBMIT[8]: $title\n"
              . "$LANG_BANNER_SUBMIT[9]: <{$A['url']}>\n"
              . "$LANG_BANNER_SUBMIT[3]: {$A['category']}\n\n"
              . $description . "\n\n";
    if ($table == $_TABLES['bannersubmission']) {
        $mailbody .= "$LANG_BANNER[10] <{$_CONF['site_admin_url']}/moderation.php>\n\n";
    } else {
        $mailbody .= "$LANG_BANNER[114] <{$_CONF['site_url']}/banner/index.php?category=" . urlencode ($A['category']) . ">\n\n";
    }
    $mailsubject = $_CONF['site_name'] . ' ' . $LANG_BANNER_SUBMIT[11];

    $mailbody .= "\n------------------------------\n";
    $mailbody .= "\n$LANG08[34]\n";
    $mailbody .= "\n------------------------------\n";

    COM_mail ($_CONF['site_mail'], $mailsubject, $mailbody);
}

/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_banner ()
{
    global $_CONF;

    return $_CONF['site_url'] . '/banner/images/banner.png';
}

function plugin_getBannerField_banner($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $LANG_ACCESS, $LANG_BANNER_ADMIN;

    $retval = '';

    $access = SEC_hasAccess($A['owner_id'],$A['group_id'],$A['perm_owner'],$A['perm_group'],$A['perm_members'],$A['perm_anon']);
    if ($access > 0) {
        switch($fieldname) {
            case 'edit':
                if ($access == 3) {
		            $attr = array('class' => 'editlink');
                    $retval = COM_createLink(
                        $icon_arr['edit'],
                        "{$_CONF['site_admin_url']}/plugins/banner/index.php?mode=edit&amp;bid={$A['bid']}",$attr
                    );
                }
                break;
            case 'access':
                if ($access == 3) {
                   $retval = $LANG_ACCESS['edit'];
                } else {
                   $retval = $LANG_ACCESS['readonly'];
                }
                break;
            case 'title':
                $retval = COM_createLink(stripslashes($A['title']), $A['url']);
                break;
            case 'dovalidate';
                $retval = banner_validateUrl($A['url']);
                break;
            case 'beforevalidate';
                $retval = $LANG_BANNER_ADMIN[57];
                break;
            case 'category':
                if (isset($A['indent'])) {
                    $indent = ($A['indent'] - 1) * 20;
                } else {
                    $indent = 0;
                }
                $cat = COM_createLink($A['category'],
                        "{$_CONF['site_url']}/banner/index.php?category="
                        . urlencode($A['cid']));
                $retval = "<span style=\"padding-left:{$indent}px;\">$cat</span>";
                break;
            case 'description':
                if (strpos($fieldvalue,'[') !== false) {
                    $retval = PLG_replaceTags( $fieldvalue );
                } else {
                    $retval = $fieldvalue;
                }
                break;
            default:
                $retval = $fieldvalue;
                break;
        }
    }

    return $retval;
}

function plugin_getListField_bannercategories($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $_TABLES, $LANG_ACCESS, $LANG_BANNER_ADMIN;

    $retval = '';

    $access = SEC_hasAccess($A['owner_id'],$A['group_id'],$A['perm_owner'],$A['perm_group'],$A['perm_members'],$A['perm_anon']);
    if ($access > 0) {
        switch($fieldname) {
            case 'edit':
                if ($access == 3) {
		            $attr = array('class' => 'editlink');
                    $retval = COM_createLink(
                        $icon_arr['edit'],
                        "{$_CONF['site_admin_url']}/plugins/banner/bannercategory.php?mode=edit&amp;cid=" . urlencode($A['cid']), $attr
                    );
                }
                break;
            case 'access':
                if ($access == 3) {
                   $retval = $LANG_ACCESS['edit'];
                } else {
                   $retval = $LANG_ACCESS['readonly'];
                }
                break;
            case 'bannercategory':
                $indent = ($A['indent'] - 1) * 20;
                $cat = COM_createLink($A['category'],
                        "{$_CONF['site_url']}/banner/index.php?bannercategory="
                        . urlencode($A['cid']));
                $retval = "<span style=\"padding-left:{$indent}px;\">$cat</span>";
                break;
            case 'addchild';
                if ($access == 3) {
					$attr = array('class' => 'editlink');
                    $retval = COM_createLink(
                        $icon_arr['addchild'],
                        "{$_CONF['site_admin_url']}/plugins/banner/bannercategory.php?mode=edit&amp;pid=" . urlencode($A['cid']), $attr
                    );
                }
                break;
            case 'tid';
                if ($A['tid'] == 'all') {
                    $retval = $LANG_BANNER_ADMIN[35];
                } else {
                    $retval = DB_getItem($_TABLES['topics'], 'topic',
                                         "tid = '{$A['tid']}'");
                }
                if (empty($retval)) {
                    $retval = $A['tid'];
                }
                break;
            default:
                $retval = $fieldvalue;
                break;
        }
    }

    return $retval;
}

function banner_validateUrl($url)
{
    global $LANG_BANNER_STATUS;

    require_once 'HTTP/Request.php';

    $retval = '';

    $req = new HTTP_Request($url);
    $req->setMethod(HTTP_REQUEST_METHOD_HEAD);
    $req->addHeader('User-Agent', 'Geeklog/' . VERSION);

    $response = $req->sendRequest();
    if (PEAR::isError($response)) {
        $retval = $response->getMessage();
    } else {
        $status_code = $req->getResponseCode();
        if (isset($LANG_BANNER_STATUS[$status_code])) {
            $retval = $status_code . ": " . $LANG_BANNER_STATUS[$status_code];
        } else {
            $retval = $LANG_BANNER_STATUS[999];
        }
    }

    return $retval;
}

/**
* Set template variables
*
* @param    string  $templatename   name of template, e.g. 'header'
* @param    ref     $template       reference of actual template
* @return   void
*
* Note: A plugin should use its name as a prefix for the names of its
* template variables, e.g. 'banner_xxx' and 'lang_banner_xxx'.
* 'button_banner' is an exception, as such a variable existed for header.thtml
* in Geeklog 1.3.11 and earlier, where the Links section was an integral part
* of Geeklog. It is added here for backward-compatibility.
*
*/
function plugin_templatesetvars_banner ($templatename, &$template)
{
    global $_TABLES, $_BAN_CONF;
    
    $id = '';
    
    switch ($templatename) {
        case 'header':
        case 'footer':
            if (isset($_BAN_CONF['bannertemplatevariables']) && $_BAN_CONF['bannertemplatevariables'] > 0) {
                $id = $templatename;
            }
            break;
    }
    
    if (!empty($id)) {
        $sqls = array(
                'randombanner' => "SELECT b.bid, b.title, b.description, b.url FROM {$_TABLES['banner']} AS b LEFT JOIN {$_TABLES['bannercategories']} AS c ON b.cid=c.cid WHERE c.cid='{$id}' AND (b.publishstart IS NULL OR b.publishstart < NOW()) and (b.publishend IS NULL OR b.publishend > NOW())" . COM_getPermSQL('AND', 0, 2, 'c') . ' ORDER BY RAND() LIMIT 1',
                'banner' => "SELECT bid,title,description,url FROM {$_TABLES['banner']} WHERE bid='{$id}' AND (publishstart IS NULL OR publishstart < NOW()) and (publishend IS NULL OR publishend > NOW())" . COM_getPermSQL('AND')
               );

        foreach ($sqls as $name => $sql) {
            $banner = '';
            $result = DB_query($sql);
            $nrows = DB_numRows($result);
            if ($nrows > 0) {
                for ($i = 0; $i < $nrows; $i++) {
                    list($bid, $bannertitle, $bannerimg, $url) = DB_fetchArray($result);
                    $flg_link = empty($url) ? false : true ;
                    $banner .= banner_buildBanner($bid, $bannertitle, $bannerimg, $flg_link);
                }
            }
            $template->set_var($name, $banner);
        }
    }
}

/**
* Automatic uninstall function for plugins
*
* @return   array
*
* This code is automatically uninstalling the plugin.
* It passes an array to the core code function that removes
* tables, groups, features and php blocks from the tables.
* Additionally, this code can perform special actions that cannot be
* foreseen by the core code (interactions with other plugins for example)
*
*/
function plugin_autouninstall_banner ()
{
    global $_BAN_CONF;
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array('banner','bannersubmission','bannercategories'),
        /* give the full name of the group, as in the db */
        'groups' => array('Banner Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('banner.edit', 'banner.moderate', 'banner.submit'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array('phpblock_topic_banner','phpblock_topic_randombanner'),
        /* give all vars with their name */
        'vars'=> array()
    );
    if (isset($_BAN_CONF['bannertemplatevariables'])) {
        $_BAN_CONF['bannertemplatevariables'] = 0;
    }
    return $out;
}

// +--------------------------------------------------------------------------+
// | Category HTML functions                                                  |
// | Functions for building select boxes and breadcrumb trails etc.           |
// +--------------------------------------------------------------------------+

/*
* Build selection list
*/
function banner_select_box ($access, $sel = '')
{
    global $_CONF, $LANG_BANNER, $_BAN_CONF;

    // set root value
    $menu = '<option value="' . $_BAN_CONF['root'] . '">' . $LANG_BANNER['root'] . '</option>';
    // get option values
    $list = banner_select_box_recursive($menu, $_BAN_CONF['root'], $sel, '&nbsp;&nbsp;', $access);
    // return list of options
    return $list;
}


/*
* Build recursive tree
*/
function banner_select_box_recursive (&$menu, $cid, $sel, $indent, $access)
{
    global $_CONF, $_TABLES;

    $cat = addslashes($cid);
    $sql = "SELECT cid,category
            FROM {$_TABLES['bannercategories']}
            WHERE (pid='{$cat}') " . COM_getPermSQL('AND', 0, $access) . "
            ORDER BY category";
    $query = DB_query($sql);
    while (list($cid, $category) = DB_fetchArray($query)) {
        // set selected item
        if ($cid == $sel) {
            // yes, selected
            $menu .= '<option value="' . htmlspecialchars($cid)
                  . '" selected="selected">' . $indent . $category
                  . '</option>';
        } else {
            // no, not selected
            $menu .= '<option value="' . htmlspecialchars($cid) . '">'
                  . $indent . $category . '</option>';
        }
        // Check and see if this category has any sub categories
        if (DB_count($_TABLES['bannercategories'], 'pid', addslashes($cid)) > 0) {
            // yes, call self
            $dum = banner_select_box_recursive ($menu, $cid, $sel,
                        $indent . '&nbsp;&nbsp;', $access);
        }
   }

   return $menu;
}


/*
* Build breadcrumb trail
*
* Breadcrumb trail does not use the "root" category in the database: the top
* level category is set from the language file using $LANG_BANNER['root']
*/
function banner_breadcrumbs($root, $cid)
{
    global $_CONF, $_TABLES, $LANG_BANNER;

    $breadcrumb = '';
    $separator  = ' &gt; ';

    $cat = addslashes($cid);
    $c = $cid;
    $pid = '';
    if ($root != $cid) {
        while ($pid != $root) {
            $parent = DB_query("SELECT cid,pid,category FROM {$_TABLES['bannercategories']} WHERE cid='{$cat}'");
            $A = DB_fetchArray($parent);
            if ($cid != $c) {
                $content = stripslashes($A['category']);
                $url = $_CONF['site_url'] . '/banner/index.php?category='
                                          . urlencode($A['cid']);
                $breadcrumb = COM_createLink($content, $url) . $separator
                            . $breadcrumb;
            } else {
                $breadcrumb = '<b>' . $A['category'] . '</b>' . $breadcrumb;
            }
            $pid = $A['pid'];
            $c = $A['pid'];
            $cat = addslashes($c);
        }
    }

    $url = $_CONF['site_url'] . '/banner/index.php';
    if (empty($breadcrumb)) {
        $breadcrumb = '<b>' . $LANG_BANNER['root'] . '</b>';
    } else {
        $breadcrumb = COM_createLink($LANG_BANNER['root'], $url) . $separator . $breadcrumb;
    }

    $breadcrumb = '<span class="banner-breadcrumb">' . $LANG_BANNER[126] . ' '
                . $breadcrumb . '</span>';

    return $breadcrumb;
}

function banner_buildBanner ($bid, $title, $img, $link=true)
{
    global $_CONF, $LANG_DIRECTION, $LANG_ADMIN, $_BAN_CONF, $_IMAGE_TYPE, $_TABLES, $_USER;
    
    if (empty($bid) || empty($title) || empty($img)) { return; }
    if (strpos($img, '[') !== false) { $img = PLG_replaceTags($img); }
    $link_edit = '';
    if (!empty ($_USER['uid']) && $_USER['uid'] > 1) {
        if (isset($_BAN_CONF['admin_editlink']) && $_BAN_CONF['admin_editlink'] === true) {
            $result = DB_query("SELECT owner_id,group_id,perm_owner,perm_group,perm_members,perm_anon FROM {$_TABLES['banner']} WHERE bid='{$bid}'");
            $A = DB_fetchArray($result);
            if ( SEC_hasAccess($A['owner_id'], $A['group_id'], $A['perm_owner'], $A['perm_group'], $A['perm_members'], $A['perm_anon']) == 3 ) {
                $icon_edit = COM_createImage( "{$_CONF['layout_url']}/images/edit.$_IMAGE_TYPE", $LANG_ADMIN['edit'] );
            	$attr = array('class' => 'editlink');
                $link_edit = COM_createLink( $icon_edit, "{$_CONF['site_admin_url']}/plugins/banner/index.php?mode=edit&amp;bid={$bid}", $attr );
            }
        }
    }
    if ($link === false) { return $img . $link_edit; }
    
    $url = COM_buildUrl($_CONF['site_url'] . '/banner/portal.php?what=banner&amp;item=' . $bid);
    $class = 'ext-banner';
    if ((!empty($LANG_DIRECTION)) && ($LANG_DIRECTION == 'rtl')) {
        $class .= '-rtl';
    }
    $attr = array(
                'title' => $title,
                'class' => $class. " editlink"
                 );
    return COM_createLink($img, $url, $attr) . $link_edit;
}

// +--------------------------------------------------------------------------+
// | PHP Block functions                                                      |
// +--------------------------------------------------------------------------+

/**
 * Returns a list of banner that belong to categories associated with
 * the current topic
 *
 * Relies on the fact that $topic is set (and sanitized) in lib-common.php
 */
function phpblock_topic_banner()
{
    global $_CONF, $_TABLES, $LANG_BANNER, $topic;

    $retval = '';

    if (!empty($topic)) {
        $tid = addslashes($topic);
        $result = DB_query("SELECT b.bid, b.title, b.description, c.cid, b.url FROM {$_TABLES['banner']} AS b LEFT JOIN {$_TABLES['bannercategories']} AS c ON b.cid=c.cid WHERE c.tid='{$tid}' OR c.tid='all' AND (b.publishstart IS NULL OR b.publishstart < NOW()) and (b.publishend IS NULL OR b.publishend > NOW())" . COM_getPermSQL('AND', 0, 2, 'c'));
        $nrows = DB_numRows($result);
        if ($nrows > 0) {
            for ($i = 0; $i < $nrows; $i++) {
                $A = DB_fetchArray($result);
                $bid = $A['bid'];
                $bannertitle = stripslashes($A['title']);
                $bannerimg = stripslashes($A['description']);
                $flg_link = empty($A['url']) ? false : true ;
                $retval .= banner_buildBanner($bid, $bannertitle, $bannerimg, $flg_link)
                        . '<br' . XHTML . '>';
            }
        }
    }

    return $retval;
}

/**
 * Returns a random of banner that belong to categories associated with
 * the current topic
 *
 * Relies on the fact that $topic is set (and sanitized) in lib-common.php
 */
function phpblock_topic_randombanner()
{
    global $_CONF, $_TABLES, $LANG_BANNER, $topic;

    $retval = '';

    if (!empty($topic)) {
        $tid = addslashes($topic);
        $result = DB_query("SELECT b.bid, b.title, b.description, c.cid, b.url FROM {$_TABLES['banner']} AS b LEFT JOIN {$_TABLES['bannercategories']} AS c ON b.cid=c.cid WHERE c.tid='{$tid}' OR c.tid='all' AND (b.publishstart IS NULL OR b.publishstart < NOW()) and (b.publishend IS NULL OR b.publishend > NOW())" . COM_getPermSQL('AND', 0, 2, 'c') . ' ORDER BY RAND() LIMIT 1');
        $nrows = DB_numRows($result);
        if ($nrows > 0) {
            for ($i = 0; $i < $nrows; $i++) {
                $A = DB_fetchArray($result);
                $bid = $A['bid'];
                $bannertitle = stripslashes($A['title']);
                $bannerimg = stripslashes($A['description']);
                $flg_link = empty($A['url']) ? false : true ;
                $retval .= banner_buildBanner($bid, $bannertitle, $bannerimg, $flg_link);
            }
        }
    }

    return $retval;
}


?>
