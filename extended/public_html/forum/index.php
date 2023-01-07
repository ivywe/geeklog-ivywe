<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog Forums Plugin 2.8.0                                               |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// | Main program to view forum                                                |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011 by the following authors:                              |
// |    Geeklog Community Members   geeklog-forum AT googlegroups DOT com      |
// |                                                                           |
// | Copyright (C) 2000,2001,2002,2003 by the following authors:               |
// |    Tony Bibbs       tony AT tonybibbs DOT com                             |
// |                                                                           |
// | Forum Plugin Authors                                                      |
// |    Mr.GxBlock                                        www.gxblock.com      |
// |    Matthew DeWyer   matt AT mycws DOT com            www.cweb.ws          |
// |    Blaine Lang      geeklog AT langfamily DOT ca     www.langfamily.ca    |
// +---------------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// +---------------------------------------------------------------------------+

require_once '../lib-common.php'; // Path to your lib-common.php

if (!in_array('forum', $_PLUGINS)) {
    COM_handle404();
    exit;
}

require_once $CONF_FORUM['path_include'] . 'gf_format.php';

// Pass thru filter any get or post variables to only allow numeric values and remove any hostile data
$op        = isset($_REQUEST['op'])        ? COM_applyFilter($_REQUEST['op'])          : '';
$msg       = isset($_GET['msg'])           ? COM_applyFilter($_GET['msg'])             : '';
$page      = isset($_REQUEST['page'])      ? COM_applyFilter($_REQUEST['page'],true)   : 0;
$sort      = isset($_REQUEST['sort'])      ? COM_applyFilter($_REQUEST['sort'],true)   : '';
$order     = isset($_REQUEST['order'])     ? COM_applyFilter($_REQUEST['order'],true)  : '';
$forum     = isset($_REQUEST['forum'])     ? COM_applyFilter($_REQUEST['forum'],true)  : '';
$category  = isset($_REQUEST['category'])  ? COM_applyFilter($_REQUEST['category'],true) : ''; // Used for display category forum index and to mark all posts as read
$populartype = isset($_REQUEST['populartype']) ? COM_applyFilter($_REQUEST['populartype']) : '';

// Check is anonymous users can access and if not, regular user can access
// Plus if can access forum
forum_chkUsercanAccess(false, $forum);

// Set search criteria (same as Geeklog Search) for highlight
if (isset($_REQUEST['query'])) { 
	$query = Geeklog\Input::request('query'); // use request instead of frequest so no filtering
	$query = urldecode($query);
	$query = GLText::remove4byteUtf8Chars($query);
	$query = GLText::stripTags($query);
	$queryEncoded = urlencode($query);
	$queryEscaped = DB_escapeString($query);
	
} else {
	$query = '';
	$queryEncoded = '';
	$queryEscaped = '';
}

$display = '';

$todaysdate = date($_CONF['shortdate']);

// Check to see if request to mark all topics read was requested
if (!COM_isAnonUser() && $op == 'markallread') {
    $now = time();
	
    $categories = array();
	$forumSQL = "";
	if (!empty($forum)) {
		$categories[] = DB_getItem($_TABLES['forum_forums'],'forum_cat',"forum_id = $forum");
		$forumSQL = " AND forum_id = $forum";
		$url = $_CONF['site_url'] . "/forum/index.php?forum=$forum";
		$message = $LANG_GF02['msg302a'];
	} else {
		if ($category == 0) {
			// filter issue
			COM_handle404('/forum/index.php');			
		} elseif (empty($category)) {
			$csql = DB_query("SELECT id FROM {$_TABLES['forum_categories']} ORDER BY id");
			while (list ($categoryID) = DB_fetchArray($csql)) {
				$categories[] = $categoryID;
			}
			
			$url = $_CONF['site_url'] .'/forum/index.php';
			$message = $LANG_GF02['msg301a'];
		} else {
			$categories[] = $category;
			$url = $_CONF['site_url'] . "/forum/index.php?category=$category";
			$message = $LANG_GF02['msg303a'];
		}
	}

	$markedFlag = false;
    foreach ($categories as $category) {
        $fsql = DB_query("SELECT forum_id,grp_id FROM {$_TABLES['forum_forums']} WHERE forum_cat=$category $forumSQL");

        while ($frecord = DB_fetchArray($fsql)) {
            $groupname = DB_getItem($_TABLES['groups'],'grp_name',"grp_id='{$frecord['grp_id']}'");
            if (SEC_inGroup($groupname)) {
				// Means have access
				$markedFlag = true;
                DB_query("DELETE FROM {$_TABLES['forum_log']} WHERE uid={$_USER['uid']} AND forum={$frecord['forum_id']}");
                $tsql = DB_query("SELECT id FROM {$_TABLES['forum_topic']} WHERE forum={$frecord['forum_id']} AND pid=0");
                while($trecord = DB_fetchArray($tsql)){
                    $log_sql = DB_query("SELECT * FROM {$_TABLES['forum_log']} WHERE uid={$_USER['uid']} AND topic={$trecord['id']} AND forum={$frecord['forum_id']}");
                    if (DB_numRows($log_sql) == 0) {
                        DB_query("INSERT INTO {$_TABLES['forum_log']} (uid,forum,topic,time) VALUES ('{$_USER['uid']}','{$frecord['forum_id']}','{$trecord['id']}','$now')");
                    }
                }
            }
        }
    }

	if ($markedFlag) {
		COM_setSystemMessage($message, $LANG_GF02['msg197']);
		COM_redirect($url);
	} else {
		// Someone trying to mark all as read who doesn't have access (category issue not forum as that is checked already)
		COM_handle404('/forum/index.php');
	}
}

// Debug Code to show variables
$display .= gf_showVariables();

if ($msg==1) {
    $display .= COM_showMessageText($LANG_GF02['msg134'] . "<br" . XHTML . ">" . $LANG_GF02['msg135']);
}
if ($msg==2) {
    $display .= COM_showMessageText($LANG_GF02['msg166']);
}
if ($msg==3) {
    $display .= COM_showMessageText($LANG_GF02['msg55']);
}

if ($msg==9) {
	// Forum Post Cancelled
	$display .= COM_showMessageText($LANG_GF02['msg149']);
}

// Check if this is the first page.
if ($page == 0) {
    $page = 1;
}

if ($op == 'newposts' AND !COM_isAnonUser()) {
	// Check if the number of records was specified to show - part of page navigation.
	$show = $CONF_FORUM['show_newposts_perpage'];
	
    $report = COM_newTemplate(CTL_plugin_templatePath('forum'));
    $report->set_file (array (
                    'report'         => 'reports/report_results.thtml',
                    'forum_icons'    => 'forum_icons.thtml', 
                    'forum_links'    => 'forum_links.thtml'));                    

    $report->set_block('report', 'report_record');
    $report->set_block('report', 'title');
    $report->set_block('report', 'links');
    $report->set_block('report', 'no_records_message');
    $report->set_block('forum_links', 'return_link');
    $report->set_block('forum_links', 'returnmarkread_link');
    
    $blocks = array('sort_desc', 'sort_desc_on', 'sort_asc', 'sort_asc_on');
    foreach ($blocks as $block) {
        $report->set_block('forum_icons', $block);
    }
	
    $report->parse ('img_asc1', 'sort_asc');
    $report->parse ('img_asc2', 'sort_asc');
    $report->parse ('img_asc3', 'sort_asc');
    $report->parse ('img_asc4', 'sort_asc');
    $report->parse ('img_desc1', 'sort_desc');
    $report->parse ('img_desc2', 'sort_desc');
    $report->parse ('img_desc3', 'sort_desc');
    $report->parse ('img_desc4', 'sort_desc');
    
    switch($sort) {
        case 1:
        	$orderby = 'subject';
            break;
        case 2:
        	$orderby = 'replies';
            break;
        case 3:
        	$orderby = 'views';
            break;
        case 4:
        	$orderby = 'date';
            break;            
        default:
        	$sort = 4;
        	$order = 1;
        	$orderby = 'date';
            break;
    }
	if ($order == 0) {
		$sortOrder = "$orderby ASC";
		$report->parse ("img_asc$sort", 'sort_asc_on');
	} else {
		$sortOrder = "$orderby DESC";
		$report->parse ("img_desc$sort", 'sort_desc_on');
	}    
	
	$base_url = "&amp;order=$order&amp;sort=$sort&amp;populartype=$populartype"; 

    $report->set_var ('imgset', $CONF_FORUM['imgset']);
    $report->set_var ('layout_url', $CONF_FORUM['layout_url']);
    $report->set_var ('phpself',$_CONF['site_url'] . '/forum/index.php?op=newposts');
	$report->set_var('report_title', $LANG_GF02['msg111']);
    $report->parse('title', 'title');
    
    $report->set_var ('LANG_returnmarkread', $LANG_GF02['msg164']);
    $report->set_var ('returnmarkreadlink', $_CONF['site_url'] .'/forum/index.php?op=markallread');
	$report->parse ('link2','returnmarkread_link');    
    
	$report->set_var ('LANG_return', $LANG_GF02['msg175']);
	if ($forum > 0) {
    	$report->set_var ('returnlink', "{$_CONF['site_url']}/forum/index.php?forum=$forum");
	} else {
		$report->set_var ('returnlink', "{$_CONF['site_url']}/forum/index.php");
	}
	$report->parse ('link1','return_link');
    
	$report->parse ('links','links');
    
    $report->set_var ('spacerwidth', '40%');
    
    if ($CONF_FORUM['usermenu'] == 'navbar') {
        $report->set_var('navmenu', forumNavbarMenu());
    } else {
        $report->set_var('navmenu','');
    }

    $report->set_var ('LANG_Heading1', $LANG_GF01['SUBJECT']);
    $report->set_var ('LANG_Heading2', $LANG_GF01['REPLIES']);
    $report->set_var ('LANG_Heading3', $LANG_GF01['VIEWS']);
    $report->set_var ('LANG_Heading4', $LANG_GF01['DATE']);

    if ($forum > 0) {
        $inforum = "AND ft.forum = '$forum'";
    } else {
        $inforum = "";
    }
    
    $grouplist = implode (',', $_GROUPS);    
    $grouplist = " AND (ff.grp_id IN ($grouplist))";
    
    $sql  = "SELECT ft.id 
    		FROM {$_TABLES['forum_topic']} ft
    		JOIN {$_TABLES['forum_forums']} ff ON ft.forum = ff.forum_id 
    		LEFT JOIN {$_TABLES['forum_log']} fl ON fl.topic = ft.id AND fl.uid={$_USER['uid']}
			WHERE ft.pid = 0  
			AND ((ft.lastupdated > fl.time) OR ISNULL(fl.time)) 
    		$grouplist $inforum";	
    
	$result = DB_query($sql);
	$topicCount = DB_numRows($result);
	$numpages = ceil($topicCount / $show);
	if ($numpages == 0) {
		$numpages = 1;
	}
	$offset = ($page - 1) * $show;
	$base_url = "{$_CONF['site_url']}/forum/index.php?op=newposts";
	if ($forum > 0) {
		$base_url .= "&amp;order=$order&amp;sort=$sort&amp;forum=$forum";  	
		$report->set_var ('sort_url_extra', "&amp;forum=$forum");
	} else {
		$base_url .= "&amp;order=$order&amp;sort=$sort";  	
		$report->set_var ('sort_url_extra', "");
	}	
	// Check to see if requesting a page that does not exist
	if ($page > $numpages) {
		COM_handle404($base_url);    
	}    
    
	$sql = "SELECT ft.lastupdated, ft.subject, ft.comment, ft.replies, ft.views, ft.id, ft.forum 
    		FROM {$_TABLES['forum_topic']} ft
    		JOIN {$_TABLES['forum_forums']} ff ON ft.forum = ff.forum_id 
    		LEFT JOIN {$_TABLES['forum_log']} fl ON fl.topic = ft.id AND fl.uid={$_USER['uid']}
			WHERE ft.pid = 0  
			AND ((ft.lastupdated > fl.time) OR ISNULL(fl.time)) 
    		$grouplist $inforum";	
    
	/* Un-comment if you want users to see new posts since last login only */
    //$lastlogin = DB_getItem($_TABLES['userinfo'],'lastlogin',"uid='" . $_USER['uid'] ."'");
    //$sql .= "AND lastupdated > {$lastlogin} ";
    $sql .= " ORDER BY $sortOrder, id DESC LIMIT $offset, $show";

    $result = DB_query($sql);
    $nrows = DB_numRows($result);
    $csscode = 1;
    if ($nrows > 0) {
        for ($i = 1; $i <= $nrows; $i++) {
            $P = DB_fetchArray($result);

			//$link = "<a href=\"{$_CONF['site_url']}/forum/viewtopic.php?forum={$P['forum']}&amp;showtopic={$P['id']}\">";
			$link = '<a href="' . forum_buildForumPostURL($P['id']) . '">';
			$report->set_var('post_start_ahref', $link);
			$report->set_var('post_subject', $P['subject']);
			$report->set_var('csscode', $csscode);
			$report->set_var('post_end_ahref', '</a>');
            $postdate = COM_getUserDateTimeFormat($P['lastupdated']);
			$report->set_var('post_date',$postdate[0]);
			$report->set_var('post_replies', COM_numberFormat($P['replies']));
			$report->set_var('post_views', COM_numberFormat($P['views']));
			$report->parse ('report_record', 'report_record',true);
			if ($csscode == 2) {
				$csscode = 1;
			} else {
				$csscode++;
			}
        }
    } else {
        $report->set_var ('message', $LANG_GF02['msg202']);
        $report->parse ('no_records_message', 'no_records_message');
    }
        
    $report->set_var ('pagenavigation', COM_printPageNavigation($base_url,$page, $numpages));
    
    $report->set_var('block_start', COM_startBlock($CONF_FORUM['forums_name']));
    $report->set_var('block_end', COM_endBlock());    

    $report->parse ('output', 'report');
    $display .= $report->finish ($report->get_var('output'));
    $title = $LANG_GF01['NEWFORUMPOSTS'];
    $display = gf_createHTMLDocument($display, $title);
    COM_output($display);
    exit();
}

if ($op == 'search') {
	// Figure out page stuff
	// Check if the number of records was specified to show - part of page navigation.
	$show = $CONF_FORUM['show_search_perpage'];
	
    $report = COM_newTemplate(CTL_plugin_templatePath('forum'));
    $report->set_file (array (
                    'report'         => 'reports/report_results.thtml',
                    'forum_icons'    => 'forum_icons.thtml', 
                    'forum_links'    => 'forum_links.thtml'));                    

	$report->set_block('report', 'report_record');
	$report->set_block('report', 'title');
	$report->set_block('report', 'links');
	$report->set_block('report', 'no_records_message');
	$report->set_block('forum_links', 'return_link');

    $blocks = array('sort_desc', 'sort_desc_on', 'sort_asc', 'sort_asc_on');
    foreach ($blocks as $block) {
        $report->set_block('forum_icons', $block);
    }
	
    $report->parse ('img_asc1', 'sort_asc');
    $report->parse ('img_asc2', 'sort_asc');
    $report->parse ('img_asc3', 'sort_asc');
    $report->parse ('img_asc4', 'sort_asc');
    $report->parse ('img_desc1', 'sort_desc');
    $report->parse ('img_desc2', 'sort_desc');
    $report->parse ('img_desc3', 'sort_desc');
    $report->parse ('img_desc4', 'sort_desc');
    
    switch($sort) {
        case 1:
        	$orderby = 'subject';
            break;
        case 2:
        	$orderby = 'replies';
            break;
        case 3:
        	$orderby = 'views';
            break;
        case 4:
        	$orderby = 'lastupdated';
            break;            
        default:
        	$sort = 4;
        	$order = 1;
        	$orderby = 'lastupdated';
            break;
    }
	if ($order == 0) {
		$sortOrder = "$orderby ASC";
		$report->parse ("img_asc$sort", 'sort_asc_on');
	} else {
		$sortOrder = "$orderby DESC";
		$report->parse ("img_desc$sort", 'sort_desc_on');
	}

	if ($forum > 0) {
		$base_url = "&amp;order=$order&amp;sort=$sort&amp;query=$queryEncoded&amp;forum=$forum";  	
		$report->set_var ('sort_url_extra', "&amp;query=$queryEncoded&amp;forum=$forum");
	} else {
		$base_url = "&amp;order=$order&amp;sort=$sort&amp;query=$queryEncoded";  	
		$report->set_var ('sort_url_extra', "&amp;query=$queryEncoded");
	}
    
	$title = sprintf($LANG_GF02['forumsearchfor'], $query);
	$report->set_var('report_title', $title);
	$report->parse('title', 'title');    

    $report->set_var ('imgset', $CONF_FORUM['imgset']);
    $report->set_var ('layout_url', $CONF_FORUM['layout_url']);
    $report->set_var ('phpself',$_CONF['site_url'] . '/forum/index.php?op=search');
    $report->set_var ('spacerwidth', '70%');
    if ($forum > 0) {
    	$report->set_var ('returnlink', "{$_CONF['site_url']}/forum/index.php?forum=$forum");
	} else {
		$report->set_var ('returnlink', "{$_CONF['site_url']}/forum/index.php");
	}
    $report->set_var ('LANG_return', $LANG_GF02['msg175']);
    $report->parse ('link1','return_link');
    $report->parse ('links','links');

    $report->set_var ('LANG_Heading1', $LANG_GF01['SUBJECT']);
    $report->set_var ('LANG_Heading2', $LANG_GF01['REPLIES']);
    $report->set_var ('LANG_Heading3', $LANG_GF01['VIEWS']);
    $report->set_var ('LANG_Heading4', $LANG_GF01['DATE']);

    if ($CONF_FORUM['usermenu'] == 'navbar') {
        $report->set_var('navmenu', forumNavbarMenu());
    } else {
        $report->set_var('navmenu','');
    }

    if ($forum > 0) {
        $inforum = "AND (ft.forum = '$forum')";
    } else {
        $inforum = "";
    }
    
    $grouplist = implode (',', $_GROUPS);    
    $grouplist = " AND (ff.grp_id IN ($grouplist))";
    $sql  = "SELECT id FROM {$_TABLES['forum_topic']} ft, {$_TABLES['forum_forums']} ff
    		 WHERE ft.forum = ff.forum_id 
    		 $grouplist 
    		 AND ((subject LIKE '%$queryEscaped%') OR (comment LIKE '%$queryEscaped%')) $inforum";	
	$result = DB_query($sql);
	$topicCount = DB_numRows($result);
	$numpages = ceil($topicCount / $show);
	if ($numpages == 0) {
		$numpages = 1;
	}
	$offset = ($page - 1) * $show;
	$base_url = "{$_CONF['site_url']}/forum/index.php?op=search" . $base_url;
	// Check to see if requesting a page that does not exist
	if ($page > $numpages) {
		COM_handle404($base_url);    
	}	
	
    $sql  = "SELECT ft.* FROM {$_TABLES['forum_topic']} ft, {$_TABLES['forum_forums']} ff
    		 WHERE ft.forum = ff.forum_id
    		 $grouplist  
    		 AND ((subject LIKE '%$queryEscaped%') OR (comment LIKE '%$queryEscaped%')) $inforum ";
	$sql .= "ORDER BY $sortOrder, id DESC LIMIT $offset, $show";

    $result = DB_query($sql);
    $nrows = DB_numRows($result);
    if ($nrows > 0) {
        $csscode = 1;
        for ($i = 1; $i <= $nrows; $i++) {
            $P = DB_fetchArray($result);
            
			$link = '<a href="' . forum_buildForumPostURL($P['id'], "&amp;query=$queryEncoded") . '">';
			
			$report->set_var('post_start_ahref',$link);
			$report->set_var('post_subject', $P['subject']);
			$report->set_var('post_end_ahref', '</a>');
			$postdate = COM_getUserDateTimeFormat($P['date']);
			$report->set_var('post_date',$postdate[0]);
			$report->set_var('post_replies', COM_numberFormat($P['replies']));
			$report->set_var('post_views', COM_numberFormat($P['views']));
			$report->set_var ('csscode', $csscode);
			if ($csscode == 2) {
				$csscode = 1;
			} else {
				$csscode++;
			}
			
			$report->parse ('report_record', 'report_record',true);			
        }
    } else {
        $report->set_var ('message', $LANG_GF02['msg203']);
        $report->parse ('no_records_message', 'no_records_message');
    }
    
    $report->set_var ('pagenavigation', COM_printPageNavigation($base_url,$page, $numpages));

    $report->set_var('block_start', COM_startBlock($CONF_FORUM['forums_name']));
    $report->set_var('block_end', COM_endBlock());
    
    $report->parse ('output', 'report');
    $display .= $report->finish($report->get_var('output'));
    $title = $LANG_GF02['forumsearchresults'];
    $display = gf_createHTMLDocument($display, $title);
    COM_output($display);
    exit();
}

if ($op == 'popular') {
	// Figure out page stuff
	// Check if the number of records was specified to show - part of page navigation.
	$show = $CONF_FORUM['show_popular_perpage'];
	
	// Number of posts before calling a topic popular
    if ($populartype == 'views') {
    	$populartype = 'views';
	} else {
		$populartype = 'replies';
	}	
	$popular_limit = "";
	if ($CONF_FORUM['popular_limit'] > 0) {
		$popular_limit = " AND (ft.$populartype >= {$CONF_FORUM['popular_limit']})";
	}
	
    $grouplist = implode (',', $_GROUPS);    
    $grouplist = " AND (ff.grp_id IN ($grouplist))";
    		
	$sql = "SELECT id FROM {$_TABLES['forum_topic']} ft, {$_TABLES['forum_forums']} ff 
			WHERE ft.forum = ff.forum_id AND (ft.pid = '0') $popular_limit $grouplist ";
	$result = DB_query($sql);
	$topicCount = DB_numRows($result);
	$numpages = ceil($topicCount / $show);
	if ($numpages == 0) {
		$numpages = 1;
	}
	$offset = ($page - 1) * $show;
	$base_url = "{$_CONF['site_url']}/forum/index.php?op=popular&amp;populartype=$populartype";
	// Check to see if requesting a page that does not exist
	if ($page > $numpages) {
		COM_handle404($base_url);    
	}	

    $report = COM_newTemplate(CTL_plugin_templatePath('forum'));
    $report->set_file (array (
                    'report'         => 'reports/report_results.thtml',
                    'forum_icons'    => 'forum_icons.thtml', 
                    'forum_links'    => 'forum_links.thtml'));  
    
    $report->set_block('report', 'report_record');
    $report->set_block('report', 'title');
    $report->set_block('report', 'links');
    $report->set_block('report', 'no_records_message');
    $report->set_block('report', 'report_controls_popular');
	$report->set_block('forum_links', 'return_link');
	
    $blocks = array('sort_desc', 'sort_desc_on', 'sort_asc', 'sort_asc_on');
    foreach ($blocks as $block) {
        $report->set_block('forum_icons', $block);
    }
	
    $report->parse ('img_asc1', 'sort_asc');
    $report->parse ('img_asc2', 'sort_asc');
    $report->parse ('img_asc3', 'sort_asc');
    $report->parse ('img_asc4', 'sort_asc');
    $report->parse ('img_desc1', 'sort_desc');
    $report->parse ('img_desc2', 'sort_desc');
    $report->parse ('img_desc3', 'sort_desc');
    $report->parse ('img_desc4', 'sort_desc');
    
    switch($sort) {
        case 1:
        	$orderby = 'subject';
            break;
        case 2:
        	$orderby = 'replies';
            break;
        case 3:
        	$orderby = 'views';
            break;
        case 4:
        	$orderby = 'date';
            break;            
        default:
        	if ($populartype == 'views') {
				$sort = 3;
				$order = 1;
				$orderby = 'views';
			} else {
				$sort = 2;
				$order = 1;
				$orderby = 'replies';
			}        	
            break;
    }
	if ($order == 0) {
		$sortOrder = "$orderby ASC";
		$report->parse ("img_asc$sort", 'sort_asc_on');
	} else {
		$sortOrder = "$orderby DESC";
		$report->parse ("img_desc$sort", 'sort_desc_on');
	}
    
    $base_url .= "&amp;order=$order&amp;sort=$sort&amp;populartype=$populartype";    
    
    $report->set_var ('imgset', $CONF_FORUM['imgset']);
    $report->set_var ('layout_url', $CONF_FORUM['layout_url']);
    $report->set_var ('phpself',$_CONF['site_url'] . '/forum/index.php?op=popular');
    $report->set_var ('endblock', COM_endBlock());
    $report->set_var ('spacerwidth', '70%');
    if ($forum > 0) {
    	$report->set_var ('returnlink', "{$_CONF['site_url']}/forum/index.php?forum=$forum");    	
	} else {
		$report->set_var ('returnlink', "{$_CONF['site_url']}/forum/index.php");
	}
    $report->set_var ('LANG_return', $LANG_GF02['msg175']);
    $report->set_var ('LANG_Heading1', $LANG_GF01['SUBJECT']);
    $report->set_var ('LANG_Heading2', $LANG_GF01['REPLIES']);
    $report->set_var ('LANG_Heading3', $LANG_GF01['VIEWS']);
    $report->set_var ('LANG_Heading4', $LANG_GF01['DATE']);

    if ($CONF_FORUM['usermenu'] == 'navbar') {
        $report->set_var('navmenu', forumNavbarMenu($LANG_GF02['msg201']));
    } else {
        $report->set_var('navmenu','');
    }
    $report->parse ('link1','return_link');
    $report->parse ('links','links');

    $report->set_var ('sort_url_extra', "&amp;populartype=$populartype");
    if ($populartype == 'views') {
    	$report->set_var ('replies_checked', '');
    	$report->set_var ('views_checked', 'CHECKED');
    	
    	$report->set_var('report_title', sprintf($LANG_GF02['poptopisby'], $LANG_GF02['views']));
	} else {
		$report->set_var ('replies_checked', 'CHECKED');
		$report->set_var ('views_checked', '');
		
		$report->set_var('report_title', sprintf($LANG_GF02['poptopisby'], $LANG_GF02['replies']));
	}
	$report->set_var ('LANG_By', $LANG_GF02['by']);
	$report->set_var ('LANG_Replies', $LANG_GF02['replies']);
	$report->set_var ('LANG_Views', $LANG_GF02['views']);
    $report->parse ('report_controls', 'report_controls_popular');
	$report->parse('title', 'title');

    $sql = "SELECT date,subject,comment,replies,views,id,forum FROM {$_TABLES['forum_topic']} ft, {$_TABLES['forum_forums']} ff 
    		WHERE ft.forum = ff.forum_id AND (ft.pid = '0') $popular_limit $grouplist ";
    $sql .= "ORDER BY $sortOrder, id DESC LIMIT $offset, $show";
    
    $result = DB_query($sql);
    $nrows = DB_numRows($result);
    if ($nrows > 0) {    
		for ($i = 0; $i < $nrows; $i++) {
			$P = DB_fetchArray($result);
				
			//$link = "<a href=\"{$_CONF['site_url']}/forum/viewtopic.php?forum={$P['forum']}&amp;showtopic={$P['id']}\">";
			$link = '<a href="' . forum_buildForumPostURL($P['id']) . '">';
			
			$report->set_var('post_start_ahref',$link);
			$report->set_var('post_subject', $P['subject']);
			$report->set_var('post_end_ahref', '</a>');
			$postdate = COM_getUserDateTimeFormat($P['date']);
			$report->set_var('post_date',$postdate[0]);
			$report->set_var('post_replies', COM_numberFormat($P['replies']));
			$report->set_var('post_views', COM_numberFormat($P['views']));
			$report->set_var('csscode', $i%2+1);
			$report->parse ('report_record', 'report_record',true);
		}
    } else {
        $report->set_var ('message', $LANG_GF02['msg203']);
        $report->parse ('no_records_message', 'no_records_message');
    }    
    
    $report->set_var ('pagenavigation', COM_printPageNavigation($base_url,$page, $numpages));
    
    $report->set_var('block_start', COM_startBlock($CONF_FORUM['forums_name']));
    $report->set_var('block_end', COM_endBlock());
    
    $report->parse ('output', 'report');
    $display .= $report->finish($report->get_var('output'));
    $title = $LANG_GF02['popularforumtopics'] ;
    $display = gf_createHTMLDocument($display, $title);
    COM_output($display);
    exit();
}

if ($op == 'subscribe') {
    if ($forum > 0 AND $_USER['uid'] > 1) {
        DB_query("INSERT INTO {$_TABLES['forum_watch']} (forum_id,topic_id,uid,date_added) VALUES ('$forum','0','{$_USER['uid']}', now() )");
        // Delete all individual topic notification records
        DB_query("DELETE FROM {$_TABLES['forum_watch']} WHERE uid='{$_USER['uid']}' AND forum_id='$forum' and topic_id > '0' " );
        COM_redirect($_CONF['site_url'] .'/forum/index.php?msg=1&amp;forum=' . $forum);
    } else {
		$display .= COM_showMessageText($LANG_GF02['msg136'], $LANG_GF01['ERROR']);
    }
    $display = gf_createHTMLDocument($display);
    COM_output($display);
    exit();
}

// MAIN CODE BEGINS to view forums or topics within a forum

isset($showtopic) or $showtopic = ''; // FIXME
ForumHeader($category, $forum, $showtopic, $display);

// Check if the number of records was specified to show - part of page navigation.
$show = $CONF_FORUM['show_topics_perpage'];

if ($forum > 0) {
    $addforumvar = "&amp;forum=" . $forum;
    $topicCount = DB_count($_TABLES['forum_topic'], array('pid', 'forum'), array(0, $forum));
} else {
    $topicCount = DB_count($_TABLES['forum_topic'], 'pid', 0);
}

$numpages = ceil($topicCount / $show);
if ($numpages == 0) {
    $numpages = 1;
}
$offset = ($page - 1) * $show;
$base_url = $_CONF['site_url'] . "/forum/index.php?forum=$forum";
// Check to see if requesting a page that does not exist
if ($page > $numpages) {
	COM_handle404($base_url);    
}

//Display Categories
if (empty($forum)) {
    //$mytimer = new timerobject();
    //$mytimer->startTimer();
    //$exectime = $mytimer->stopTimer();
    //COM_errorLog("Forum Listing - time:$exectime");

    $groups = array ();
    $usergroups = SEC_getUserGroups();
    foreach ($usergroups as $group) {
        $groups[] = $group;
    }
    $groupAccessList = implode(',',$groups);
    $sql = "SELECT * FROM {$_TABLES['forum_categories']}";
    if ($category > 0) {
    	$sql .= " WHERE id = $category";
	}
	$sql .= " ORDER BY cat_order ASC";
	
    $categoryQuery = DB_query($sql);
    $numCategories = DB_numRows($categoryQuery);
    
    // Check to see if requesting a category that does not exist
    if ($category == "0"  OR ($category > 0 AND $numCategories == 0)) {
		$base_url = "{$_CONF['site_url']}/forum/index.php";
		COM_handle404($base_url);    
	}
    
    // Find topic assignment if exists for forum or at a higher level
    forum_getGeeklogTopic(TOPIC_TYPE_FORUM_CATEGORY, $category);
    
    $forumlisting = COM_newTemplate(CTL_plugin_templatePath('forum'));
    $forumlisting->set_file (array (
            'forumlisting'         => 'homepage.thtml',
            'category_record'      => 'categorylisting.thtml',
            'forum_icons'   	   => 'forum_icons.thtml', 
    		'forum_links'   	   => 'forum_links.thtml'));
    
    $forumlisting->set_block('category_record', 'forum_record');

    $blocks = array('new_icon', 'quiet_icon', 'active_icon', 'normal_icon', 'normalnew_icon', 'sticky_icon', 'stickynew_icon', 'locked_icon', 'lockednew_icon', 'topiclocked_icon');
    foreach ($blocks as $block) {
        $forumlisting->set_block('forum_icons', $block);
    }
        
    $blocks = array('newpost_link', 'markread_link', 'categorymenu_link');
    foreach ($blocks as $block) {
        $forumlisting->set_block('forum_links', $block);
    }    

    $forumlisting->set_var ('imgset', $CONF_FORUM['imgset']);
    $forumlisting->set_var ('forumindeximg','<img alt="forum index" src="'.gf_getImage('forumindex').'"' . XHTML . '>');
    $forumlisting->set_var ('phpself', $_CONF['site_url'] .'/forum/index.php');
    $forumlisting->set_var('layout_url', $CONF_FORUM['layout_url']);
    $viewnewpostslink = false;  // Set true when we have set the view newposts link template var

    for ($i=1; $i <= $numCategories; $i++) {
        //$exectime = $mytimer->stopTimer();
        //COM_errorLog("Start Category Listing - time:$exectime");
        $A = DB_FetchArray($categoryQuery,false);

		$forumlisting->set_var ('LANG_HOME', $LANG_GF01['HOMEPAGE']);
		$forumlisting->set_var ('forum_home',$LANG_GF01['INDEXPAGE']);
        $forumlisting->set_var ('cat_name', $A['cat_name']);
        $forumlisting->set_var ('cat_desc', $A['cat_dscp']);
        $forumlisting->set_var ('cat_id', $A['id']);
        $forumlisting->set_var ('category_id', $A['id']);
        $forumlisting->set_var ('LANGGF91_forum', $LANG_GF91['forum']);
        $forumlisting->set_var ('LANGGF01_TOPICS', $LANG_GF01['TOPICS']);
        $forumlisting->set_var ('LANGGF01_POSTS', $LANG_GF01['POSTS']);
        $forumlisting->set_var ('LANGGF01_LASTPOST', $LANG_GF01['LASTPOST']);
        $forumlisting->set_var ('cat_name_category', sprintf($LANG_GF01['FORUMCATEGORYNAME'], $A['cat_name']));
        
        $geeklog_topic = '';
        if (SEC_hasRights('forum.edit')) {
            $geeklog_topic = forum_getGeeklogTopicLabel(TOPIC_TYPE_FORUM_CATEGORY, $A['id']);
            if (!empty($geeklog_topic)) {
                $forumlisting->set_var ('lang_geeklog_topic', $LANG_GF02['gl_topics_assigned']);
            }
        }
        $forumlisting->set_var ('geeklog_topic', $geeklog_topic);        

        //Display all forums under each cat
        $sql = "SELECT * FROM {$_TABLES['forum_forums']} AS f LEFT JOIN {$_TABLES['forum_topic']} AS t ON f.last_post_rec=t.id WHERE forum_cat='{$A['id']}' ";
        $sql .= "AND grp_id IN ($groupAccessList) AND is_hidden=0 ORDER BY forum_order ASC";

        $forumQuery = DB_query($sql);
        $numForums = DB_numRows($forumQuery);

        $numForumsDisplayed = 0;
        $forumlisting->parse ('forum_record', ''); // This is just to reset the forum record so duplicates don't happen in next category

        while ($B = DB_FetchArray($forumQuery)) {
            //$exectime = $mytimer->stopTimer();
            //COM_errorLog("Start Forum Listing - time:$exectime");

            $lastforum_noaccess = false;
            $topicCount = $B['topic_count'];
            $postCount = $B['post_count'];
            if ( $CONF_FORUM['show_moderators'] ) {
                $modsql = DB_query("SELECT * FROM {$_TABLES['forum_moderators']} WHERE mod_forum='{$B['forum_id']}'");
                $moderatorcnt = 1;
                if (DB_numRows($modsql) > 0) {
                    while($showmods = DB_fetchArray($modsql,false)) {
                        if ($showmods['mod_uid'] == '0') {
                            if ($showmods['mod_groupid'] > 0) {
                                $showmods['mod_username'] = DB_getItem($_TABLES['groups'], 'grp_name', "grp_id='{$showmods['mod_groupid']}'");
                            }
                            if ($moderatorcnt == 1 OR $moderators == '') {
                                $moderators = $showmods['mod_username'];
                            } else {
                                $moderators .= ', ' . $showmods['mod_username'];
                            }
                        } else {
                            if ($moderatorcnt == 1 OR $moderators == '') {
                                $moderators = COM_getDisplayName($showmods['mod_uid']);
                            } else {
                                $moderators .= ', ' . COM_getDisplayName($showmods['mod_uid']);
                            }
                        }
                        $moderatorcnt++;
                    }
                } else {
                    $moderators = $LANG_GF01['no_one'];
                }
                $forumlisting->set_var ('moderator', sprintf($LANG_GF01['MODERATED'],$moderators));
            } else {
                $forumlisting->set_var ('moderator', '');
            }
            $numForumsDisplayed ++;
            if ($postCount > 0) {
                if ( strlen($B['subject']) > 25 ) {
                    $B['subject'] = COM_truncate($B['subject'], 25, '..');
                }
                if (!COM_isAnonUser()) {
                    // Determine if there are new topics since last visit for this user.
                    if ($topicCount > DB_getItem($_TABLES['forum_log'], 'COUNT(*)', "uid='{$_USER['uid']}' AND forum='{$B['forum_id']}' AND time > 0")) {
                        $folderimg = "active_icon";
                    } else {
                        $folderimg = "quiet_icon";
                    }
                } else {
                    $folderimg = "quiet_icon";
                }

				if (isset($CONF_FORUM['use_userdate_format']) && $CONF_FORUM['use_userdate_format']) { // FIXME: why would it not be set?
                    $lastdate = COM_getUserDateTimeFormat($B['date']);
                    $lastdate = $lastdate[0];
                } else {
                    $lastdate = COM_strftime($CONF_FORUM['default_Datetime_format'],$B['date']);
                }

                $lastpostmsgDate  = '<span class="forumtxt">' . $LANG_GF01['ON']. '</span>' .$lastdate;
                if ($B['uid'] > 1) {
                    $lastposterName = COM_getDisplayName($B['uid']);
                    //$by = '<a href="' .$_CONF['site_url']. '/users.php?mode=profile&amp;uid=' .$B['uid']. '">' .$lastposterName. '</a>';
                    $by = COM_getProfileLink($B['uid'], $lastposterName);
                } else {
                    $by = $B['name'];
                }
                $lastpostmsgBy = $LANG_GF01['BY']. $by;
                $forumlisting->set_var ('lastpostmsgDate', $lastpostmsgDate);
                $forumlisting->set_var ('lastpostmsgTopic', $B['subject']);
                $forumlisting->set_var ('lastpostmsgBy', $lastpostmsgBy);

            }  else {
                $forumlisting->set_var ('lastpostmsgDate', $LANG_GF01['nolastpostmsg']);
                $forumlisting->set_var ('lastpostmsgTopic', '');
                $forumlisting->set_var ('lastpostmsgBy', '');
                $folderimg = "quiet_icon";
            }

            if ($B['pid'] == 0) {
                $topicparent = $B['id'];
            } else {
                $topicparent = $B['pid'];
            }
			
			if ($B['is_readonly']) {
				$forumlisting->parse('forumlocked_icon', 'topiclocked_icon'); 
			} else {
				$forumlisting->set_var ('forumlocked_icon', '');
			}

            //$forumlisting->set_var ('folderimg', $folderimg);
            $forumlisting->parse ('folderimg', $folderimg);
            $forumlisting->set_var ('forum_id', $B['forum_id']);
            $forumlisting->set_var ('forum_name', $B['forum_name']);
            $forumlisting->set_var ('forum_desc', $B['forum_dscp']);
            $forumlisting->set_var ('topics', COM_numberFormat($topicCount));
            $forumlisting->set_var ('posts', COM_numberFormat($postCount));
            $forumlisting->set_var ('topic_id', $topicparent);
            $forumlisting->set_var ('lastpostid', $B['id']);
			$forumlisting->set_var ('lastpostURL', forum_buildForumPostURL($B['id']));
            $forumlisting->set_var ('LANGGF01_LASTPOST', $LANG_GF01['LASTPOST']);
            $forumlisting->parse ('forum_record', 'forum_record',true);
        }

        if ($numForumsDisplayed > 0) {
            // Categories are only displayed if forums are found
            $forum_bc_id = "forum-cat-" . $A['id'];
            $_STRUCT_DATA->add_BreadcrumbList('forum-breadcrumb', $forum_bc_id);
            $url = "{$_CONF['site_url']}/forum/index.php";
            $_STRUCT_DATA->set_breadcrumb_item('forum-breadcrumb', $forum_bc_id, 1, $url, $LANG_GF01['INDEXPAGE']);
            $url = "{$_CONF['site_url']}/forum/index.php?category={$A['id']}";
            $_STRUCT_DATA->set_breadcrumb_item('forum-breadcrumb', $forum_bc_id, 2, $url, $A['cat_name']);            
            
            if (!COM_isAnonUser()) {
                $link = $_CONF['site_url'] . '/forum/index.php?op=markallread&amp;category=' . $A['id'];
                $forumlisting->set_var ('markreadlink', $link);
                $forumlisting->set_var ('LANG_markread', $LANG_GF02['msg84']);
                $forumlisting->parse ('markread_link', 'markread_link');
                if (!$viewnewpostslink) {
                    $newpostslink = $_CONF['site_url'] .'/forum/index.php?op=newposts';
                    $forumlisting->set_var ('newpostslink', $newpostslink);
                    $forumlisting->set_var ('LANG_newposts', $LANG_GF02['msg112']);
                    $viewnewpostslink = true;
                       $forumlisting->parse ('newposts_link', 'newpost_link');
                } else {
                    $forumlisting->set_var ('newposts_link', '');
                }
                
                $forumlisting->parse ('categorymenu_link', 'categorymenu_link');
            } else {
                $forumlisting->set_var ('newposts_link', '');
                $forumlisting->set_var ('markread_link', '');
                $forumlisting->set_var ('categorymenu_link', '');
            }
            $forumlisting->parse ('category_records', 'category_record', true);
            $forumlisting->parse ('forum_records', '');
        }

    }

    if ($numCategories == 0) {         // Do we have any categories defined yet
		$display .= COM_showMessageText($LANG_GF01['MSG_NO_CAT'], $LANG_GF01['ERROR']);
    }

    $forumlisting->parse ('output', 'forumlisting');
    $display .= $forumlisting->finish ($forumlisting->get_var('output'));

    //$exectime = $mytimer->stopTimer();
    //COM_errorLog("End of Listing - time:$exectime");
}

 // Display Forums
if ($forum > 0) {
    // Check Geeklog Topic after we make sure user has access
    // Find topic assignment if exists for forum or at a higher level
    forum_getGeeklogTopic(TOPIC_TYPE_FORUM_FORUM, $forum);
 
    $topiclisting = COM_newTemplate(CTL_plugin_templatePath('forum'));
    $topiclisting->set_file (array (
            'topiclisting'         => 'topiclisting.thtml',
            'forum_icons'   	   => 'forum_icons.thtml', 
    		'forum_links'   	   => 'forum_links.thtml'));
    
    $topiclisting->set_block('topiclisting', 'topic_record');
    $topiclisting->set_block('topiclisting', 'no_records_message');
    
    $blocks = array('new_icon', 'quiet_icon', 'active_icon', 'normal_icon', 'normalnew_icon', 'sticky_icon', 'stickynew_icon', 'locked_icon', 'lockednew_icon', 'sort_desc', 'sort_desc_on', 'sort_asc', 'sort_asc_on');
    foreach ($blocks as $block) {
        $topiclisting->set_block('forum_icons', $block);
    }
    
    $blocks = array('newtopic_link', 'subscribeforum_link', 'forummenu_link');
    foreach ($blocks as $block) {
        $topiclisting->set_block('forum_links', $block);
    }       

    $topiclisting->set_var ('imgset', $CONF_FORUM['imgset']);
    $topiclisting->set_var ('layout_url', $CONF_FORUM['layout_url']);
    $topiclisting->set_var ('LANG_HOME', $LANG_GF01['HOMEPAGE']);
    $topiclisting->set_var ('forum_home',$LANG_GF01['INDEXPAGE']);
    $topiclisting->set_var ('navbreadcrumbsimg','<img alt="" src="'.gf_getImage('nav_breadcrumbs').'"' . XHTML . '>');
    
    $topiclisting->parse ('img_asc1', 'sort_asc');
    $topiclisting->parse ('img_asc2', 'sort_asc');
    $topiclisting->parse ('img_asc3', 'sort_asc');
    $topiclisting->parse ('img_asc4', 'sort_asc');
    $topiclisting->parse ('img_asc5', 'sort_asc');
    $topiclisting->parse ('img_desc1', 'sort_desc');
    $topiclisting->parse ('img_desc2', 'sort_desc');
    $topiclisting->parse ('img_desc3', 'sort_desc');
    $topiclisting->parse ('img_desc4', 'sort_desc');
    $topiclisting->parse ('img_desc5', 'sort_desc');

    switch($sort) {
        case 1:
            if ($order == 0) {
                $sortOrder = "subject ASC";
                $topiclisting->parse ('img_asc1', 'sort_asc_on');
            } else {
                $sortOrder = "subject DESC";
                $topiclisting->parse ('img_desc1', 'sort_desc_on');
            }
            break;
        case 2:
            if ($order == 0) {
                $sortOrder = "views ASC";
                $topiclisting->parse ('img_asc2', 'sort_asc_on');
            } else {
                $sortOrder = "views DESC";
                $topiclisting->parse ('img_desc2', 'sort_desc_on');
            }
            break;
        case 3:
            if ($order == 0) {
                $sortOrder = "replies ASC";
                $topiclisting->parse ('img_asc3', 'sort_asc_on');
            } else {
                $sortOrder = "replies DESC";
                $topiclisting->parse ('img_desc3', 'sort_desc_on');
            }
            break;
        case 4:
            if ($order == 0) {
                $sortOrder = "name ASC";
                $topiclisting->parse ('img_asc4', 'sort_asc_on');
            } else {
                $sortOrder = "name DESC";
                $topiclisting->parse ('img_desc4', 'sort_desc_on');
            }
            break;
        case 5:
            if ($order == 0) {
                $sortOrder = "lastupdated ASC";
                $topiclisting->parse ('img_asc5', 'sort_asc_on');
            } else {
                $sortOrder = "lastupdated DESC";
                $topiclisting->parse ('img_desc5', 'sort_desc_on');
            }
            break;
        default:
            $sortOrder = "lastupdated DESC";
            $topiclisting->parse ('img_desc5', 'sort_desc_on');
            break;
    }

    $base_url .= "&amp;order=$order&amp;sort=$sort";

    // Retrieve all the Topic Records - where pid is 0 - check to see if user does not want to see anonymous posts. Remove any topic that does not have at least 1 post by a regular user
    if (!COM_isAnonUser() AND $CONF_FORUM['show_anonymous_posts'] == 0) {
        $sql  = "SELECT * FROM {$_TABLES['forum_topic']} topic WHERE forum = '$forum' AND pid = 0 
			AND (uid > 1 
			OR (SELECT uid FROM {$_TABLES['forum_topic']} ft2 WHERE ft2.pid = topic.id AND uid > 1 LIMIT 1) > 1)";
    } else {
        $sql  = "SELECT * FROM {$_TABLES['forum_topic']} topic WHERE forum = '$forum' AND pid = 0 ";
    }
    $sql .= "ORDER BY sticky DESC, $sortOrder, id DESC LIMIT $offset, $show";
    $topicResults = DB_query($sql);
    $totalresults = DB_numRows($topicResults);

    // Retrieve Forum details and Category name
    $sql  = "SELECT forum.forum_name,category.id,category.cat_name,forum.is_readonly FROM {$_TABLES['forum_forums']} forum ";
    $sql .= "LEFT JOIN {$_TABLES['forum_categories']} category ON category.id=forum.forum_cat ";
    $sql .= "WHERE forum.forum_id = $forum";
    $categoryRecord = DB_fetchArray(DB_query($sql));
    if ($totalresults < 1) {
        $LANG_MSG05 = $LANG_GF02['msg05'];
        $topiclisting->set_var ('records_message', $LANG_GF02['msg05']);
        $topiclisting->parse ('no_records_message', 'no_records_message');
        
    }
    $subscribelink = '';
    if (!COM_isAnonUser()) {
        // Check for user subscription status
        $sub_check = DB_getITEM($_TABLES['forum_watch'],"id","forum_id='$forum' AND topic_id=0 AND uid='{$_USER['uid']}'");
        if ($sub_check == '') {
            $subscribelink = "{$_CONF['site_url']}/forum/index.php?op=subscribe&amp;forum=$forum";
            $topiclisting->set_var ('subscribelink', $subscribelink);
            $topiclisting->set_var ('subscribelinktext', $LANG_GF01['FORUMSUBSCRIBE']);
            $topiclisting->set_var ('LANG_subscribe', $LANG_GF01['FORUMSUBSCRIBE']);
            $topiclisting->set_var ('LANG_subscribe_state', $LANG_GF01['FORUMSUBSCRIBE_FALSE']);
            $topiclisting->parse ('subscribeforum_link','subscribeforum_link');
        } else {
            $subscribelink = "{$_CONF['site_url']}/forum/notify.php?filter=2";
            $topiclisting->set_var ('subscribelink', $subscribelink);
            $topiclisting->set_var ('subscribelinktext', $LANG_GF01['FORUMUNSUBSCRIBE']);
            $topiclisting->set_var ('LANG_subscribe', $LANG_GF01['FORUMUNSUBSCRIBE']);
            $topiclisting->set_var ('LANG_subscribe_state', $LANG_GF01['FORUMSUBSCRIBE_TRUE']);
            $topiclisting->parse ('subscribeforum_link','subscribeforum_link');
        }
    }

    $topiclisting->set_var ('cat_name', $categoryRecord['cat_name']);
    $topiclisting->set_var ('category_id', $categoryRecord['id']);
    $topiclisting->set_var ('forum_name', $categoryRecord['forum_name']);
    $topiclisting->set_var ('forum_id', $forum);
    $topiclisting->set_var ('forum_name_forum', sprintf($LANG_GF01['FORUMNAME'], $categoryRecord['forum_name']));
    
    $geeklog_topic = '';
    if (forum_modPermission($forum,$_USER['uid'],'mod_edit')) {
        $geeklog_topic = forum_getGeeklogTopicLabel(TOPIC_TYPE_FORUM_FORUM, $forum);
        if (!empty($geeklog_topic)) {
            $topiclisting->set_var ('lang_geeklog_topic', $LANG_GF02['gl_topics_assigned']);
        }
    }
    $topiclisting->set_var ('geeklog_topic', $geeklog_topic);
    
    $topiclisting->set_var ('imgset', $CONF_FORUM['imgset']);
    $topiclisting->set_var ('LANG_TOPIC', $LANG_GF01['TOPICSUBJECT']);
    $topiclisting->set_var ('LANG_STARTEDBY', $LANG_GF01['STARTEDBY']);
    $topiclisting->set_var ('LANG_REPLIES', $LANG_GF01['REPLIES']);
    $topiclisting->set_var ('LANG_VIEWS', $LANG_GF01['VIEWS']);
    $topiclisting->set_var ('LANG_LASTPOST',$LANG_GF01['LASTPOST']);
    $topiclisting->set_var ('LANG_AUTHOR',$LANG_GF01['AUTHOR']);
    $topiclisting->set_var ('LANG_MSG05',$LANG_GF01['LASTPOST']);
    $topiclisting->set_var ('LANG_newforumposts', $LANG_GF02['msg113']);

    if ($categoryRecord['is_readonly'] == 0 OR forum_modPermission($forum,$_USER['uid'],'mod_edit')) {
        $topiclisting->set_var ('LANG_newtopic', $LANG_GF01['NEWTOPIC']);
        $topiclisting->set_var('newtopiclinktext', $LANG_GF09['newtopic']);
        $topiclisting->set_var('newtopiclinkimg', gf_getImage('post_newtopic'));
        $topiclisting->set_var ('newtopiclink',"{$_CONF['site_url']}/forum/createtopic.php?method=newtopic&amp;id=$forum");
        $topiclisting->parse ('newtopic_link','newtopic_link');
    } else {
        $topiclisting->set_var ('LANG_newtopic', '');
        $topiclisting->set_var ('newtopiclink','#');
    }

    $displaypostpages = $LANG_GF01['PAGES'] .':'; // FIXME: is this used anywhere?
    
    $forum_bc_id = "forum-" . $forum;
    $_STRUCT_DATA->add_BreadcrumbList('forum-breadcrumb', $forum_bc_id);
    $url = "{$_CONF['site_url']}/forum/index.php";
    $_STRUCT_DATA->set_breadcrumb_item('forum-breadcrumb', $forum_bc_id, 1, $url, $LANG_GF01['INDEXPAGE']);
    $url = "{$_CONF['site_url']}/forum/index.php?category={$categoryRecord['id']}";
    $_STRUCT_DATA->set_breadcrumb_item('forum-breadcrumb', $forum_bc_id, 2, $url, $categoryRecord['cat_name']);
    $url = "{$_CONF['site_url']}/forum/index.php?forum=$forum";
    $_STRUCT_DATA->set_breadcrumb_item('forum-breadcrumb', $forum_bc_id, 3, $url, $categoryRecord['forum_name']);    

    while ($record = DB_fetchArray($topicResults,false)) {

        if (($record['replies']+1) <= $CONF_FORUM['show_posts_perpage']) {
            $displaypageslink = "";
            $gotomsg = "";
        } else {
            $displaypageslink = "";
            $gotomsg = $LANG_GF02['msg85'] . "&nbsp;";
            if ($CONF_FORUM['show_posts_perpage'] > 0) {
                $pages = ceil(($record['replies']+1)/$CONF_FORUM['show_posts_perpage']);
            } else {
                 $pages = ceil(($record['replies']+1)/20);
            }
            
            // Only display 10 pages. If more than 10 split it up, show first 5 and last 5.
            $split_pages = false;
            if ($pages > 10) {
                $split_pages = true;
            }
            for ($p=1; $p <= $pages; $p++) {
                $displaypageslink .= "<a href=\"{$_CONF['site_url']}/forum/viewtopic.php?showtopic={$record['id']}&amp;page=$p\">";
                $displaypageslink .= "$p</a>&nbsp;";
                
                if ($split_pages AND $p == 5) {
                    $displaypageslink .= '...&nbsp;';
                    $p = $pages - 5;
                }
            }
        }

        // Check if user is an anonymous poster
        if ($record['uid'] > 1) {
            //$showuserlink = '<span class="replypagination">';
            //$showuserlink .= "<a href=\"{$_CONF['site_url']}/users.php?mode=profile&amp;uid={$record['uid']}\">{$record['name']}";
            //$showuserlink .= '</a></span>';
            $showuserlink = COM_getProfileLink($record['uid'], $record['name']);
        } else {
            $showuserlink= $record['name'];
        }

        if (isset($_USER['language']) AND substr($_USER['language'],0,2) == "ja") { // need examination
            $format1 = '%Y/%m/%d';
            $format2 = 'Y/m/d';
            $format3 = '%p&nbsp;%H:%M';
        } else {
            $format1 = '%m/%d/%Y';
            $format2 = 'm/d/Y';
            $format3 = '%H:%M&nbsp;%p';
        }

        if ($record['last_reply_rec'] > 0) {
            $lastreplysql = DB_query("SELECT * FROM {$_TABLES['forum_topic']} WHERE id={$record['last_reply_rec']}");
            $lastreply = DB_fetchArray($lastreplysql);
            if (($CONF_FORUM['show_subject_length'] > 0) AND (strlen ($lastreply['subject']) > $CONF_FORUM['show_subject_length'])) {
                $lastreply['subject'] = COM_truncate($record['subject'], $CONF_FORUM['show_subject_length'], '...');
                $lastreply['subject'] .= "...";
            }

			if (isset($CONF_FORUM['use_userdate_format']) AND $CONF_FORUM['use_userdate_format']) {
                $lastdate = COM_getUserDateTimeFormat($lastreply['date']);
                $lastdate = $lastdate[0];
            } else {
                $lastdate = COM_strftime($CONF_FORUM['default_Datetime_format'],$lastreply['date']);
            }
        } else {
            $lastdate = COM_strftime($CONF_FORUM['default_Datetime_format'],$record['lastupdated']);
            $lastreply = $record;
        }

        $firstdate1 = COM_strftime($format1, $record['date']);
        if ($firstdate1 == date($format2)) {
            $firsttime = COM_strftime($format3, $record['date']);
            $firstdate = $LANG_GF01['TODAY'] . $firsttime;
        } elseif (isset($CONF_FORUM['use_userdate_format']) && $CONF_FORUM['use_userdate_format']) { // FIXME: why would it not be set?
            $firstdate = COM_getUserDateTimeFormat($record['date']);
            $firstdate = $firstdate[0];
        } else {
            $firstdate = COM_strftime($CONF_FORUM['default_Datetime_format'],$record['date']);
        }

		
        if (!COM_isAnonUser()) {
            // Determine if there are new topics since last visit for this user.
            // If topic has been updated or is new - then the user will not have record for this parent topic in the log table
            if (DB_getItem($_TABLES['forum_log'], 'COUNT(*)', "uid='{$_USER['uid']}' AND topic='{$record['id']}' AND time > 0") == 0) {
                if ($record['sticky'] == 1) {
                    $folderimg = "stickynew_icon";
                } elseif ($record['locked'] == 1 || $categoryRecord['is_readonly']) {
                    $folderimg = "lockednew_icon";
                } else {
                    $folderimg = "normalnew_icon";
                }
            } elseif ($record['sticky'] == 1) {
                $folderimg = "sticky_icon";
            } elseif ($record['locked'] == 1 || $categoryRecord['is_readonly']) {
                $folderimg = "locked_icon";
            } else {
                $folderimg = "normal_icon";
            }
        } elseif ($record['sticky'] == 1) {
            $folderimg = "sticky_icon";
        } elseif ($record['locked'] == 1 || $categoryRecord['is_readonly']) {
            $folderimg = "locked_icon";
        } else {
           $folderimg = "normal_icon";
        }

        if ($lastreply['uid'] > 1) {
            $lastposter = COM_getDisplayName($lastreply['uid']);
        } else {
            $lastposter = $lastreply['name'];
        }

        if ($record['moved'] == 1){
            $moved = "{$LANG_GF01['MOVED']}: ";
        } else {
            $moved = "";
        }

        if (($CONF_FORUM['show_subject_length'] > 0) AND (strlen ($record['subject']) > $CONF_FORUM['show_subject_length'])) {
            $subject = COM_truncate($record['subject'], $CONF_FORUM['show_subject_length'], '...');
        } else {
            $subject = $record['subject'];
        }

        if ($record['uid'] > 1) {
            $firstposterName = COM_getDisplayName($record['uid']);
        } else {
            $firstposterName = $record['name'];
        }
        $topicinfo =  "<b>{$LANG_GF01['STARTEDBY']} {$firstposterName}, {$firstdate}</b><br" . XHTML . ">";
        $lastpostinfo = strip_tags(COM_truncate($record['comment'], $CONF_FORUM['contentinfo_numchars'], '...'));
        $lastpostinfo = forum_stripBBCode($lastpostinfo); // Simple function to strip out bbcode so tooltips display better
        $lastpostinfo = htmlspecialchars($lastpostinfo); // Escape things like " so it displays properly in tooltip
        $topicinfo .= str_replace(LB, "<br" . XHTML . ">", forum_mb_wordwrap($lastpostinfo, $CONF_FORUM['linkinfo_width'], LB));
        
        if (function_exists('COM_getTooltip')) {
            $topiclink = "viewtopic.php?showtopic={$record['id']}";
            $tooltip_subject = COM_getTooltip($subject, $topicinfo, $topiclink);
            //$subject = '';
            $topiclisting->set_var ('tooltip_subject', $tooltip_subject);
        }
        $topiclisting->set_var ('topicinfo', $topicinfo);

		$topiclisting->parse ('folderimg', $folderimg);
        $topiclisting->set_var ('topic_id', $record['id']);
        $topiclisting->set_var ('subject', $subject);
        $topiclisting->set_var ('fullsubject', $record['subject']);
        $topiclisting->set_var ('gotomsg', $gotomsg);
        $topiclisting->set_var ('displaypageslink', $displaypageslink);
        $topiclisting->set_var ('showuserlink', $showuserlink);
        $topiclisting->set_var ('lastposter', $lastposter);
        $topiclisting->set_var ('LANG_lastpost', $LANG_GF02['msg188']);
        $topiclisting->set_var ('moved', $moved);
        $topiclisting->set_var ('views', COM_numberFormat($record['views']));
        $topiclisting->set_var ('replies', COM_numberFormat($record['replies']));
        $topiclisting->set_var ('lastdate', $lastdate);
        $topiclisting->set_var ('lastpostid', $lastreply['id']);
		$topiclisting->set_var ('lastpostURL', forum_buildForumPostURL($lastreply['id']));
        $topiclisting->set_var ('LANG_BY', $LANG_GF01['BY']);
        $topiclisting->parse ('topic_record', 'topic_record',true);
    }

    $topiclisting->set_var ('pagenavigation', COM_printPageNavigation($base_url,$page, $numpages));
    
    if (empty($subscribelink)) {
    	$topiclisting->set_var ('forummenu_link', '');
    } else {
    	$topiclisting->parse ('forummenu_link', 'forummenu_link');
	}
    
    $topiclisting->set_var('block_start', COM_startBlock($CONF_FORUM['forums_name']));
    $topiclisting->set_var('block_end', COM_endBlock());        
    
    $topiclisting->parse ('output', 'topiclisting');
    $display .= $topiclisting->finish ($topiclisting->get_var('output'));
}

$title = $LANG_GF01['FORUM'];
if ($forum > 0) {
    $title = stripslashes(DB_getItem($_TABLES['forum_forums'],'forum_name',"forum_id=$forum"));
}
if ($category > 0) $title = $A['cat_name'];

$display .= BaseFooter();
$display = gf_createHTMLDocument($display, $title);
COM_output($display);
?>
