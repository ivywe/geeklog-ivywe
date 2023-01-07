<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog Forums Plugin 2.8.0                                               |
// +---------------------------------------------------------------------------+
// | notify.php                                                                |
// | View users curent monitored topics                                        |
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

// Note: To share functionality, this page also can be included by public_html/admin/plugins/forum/notify.php
// If this happens 'plugins/forum/gf_functions.php' is included to check admin permissions. If successful 
// a special global variable is set so we know we are in "ADMIN" mode
if (isset($forumNotifyMode) && $forumNotifyMode == 'admin') {
	$isAdminUser = true;
} else {
	$isAdminUser = false;
	require_once '../lib-common.php'; // Path to your lib-common.php

	if (!in_array('forum', $_PLUGINS)) {
		COM_handle404();
	}
}

require_once $CONF_FORUM['path_include'] . 'gf_format.php';

// Pass thru filter any get or post variables to only allow numeric values and remove any hostile data
$forum      = isset($_REQUEST['forum'])  ? COM_applyFilter($_REQUEST['forum'],true) 	: '';
$id         = isset($_REQUEST['id'])     ? COM_applyFilter($_REQUEST['id'],true)    	: '';
$notifytype = isset($_REQUEST['filter']) ? COM_applyFilter($_REQUEST['filter'], true)   : '';
$op         = isset($_REQUEST['op'])     ? COM_applyFilter($_REQUEST['op'])         	: '';
$page       = isset($_GET['page'])       ? (int) COM_applyFilter($_GET['page'],true)    : 0;
$topic      = isset($_REQUEST['topic'])  ? COM_applyFilter($_REQUEST['topic'],true) 	: '';
	
// If Admin then allow to look at any user subscriptions
if ($isAdminUser) { 
	$notify_url_start = $_CONF['site_admin_url'] . '/plugins';
	
	$selecteduid = isset($_REQUEST['uid']) 	 ? COM_applyFilter($_REQUEST['uid'], true)      : '';
	if (empty($selecteduid) || $selecteduid < 2) {
		$selecteduid = ''; // All Users
	} else {
		// Make sure user exists and still has notifications (as the last one could have been just deleted)
		$sql  = "SELECT u.uid, u.username FROM {$_TABLES['users']} u, {$_TABLES['forum_watch']} fw ";
		$sql .= "WHERE u.uid = fw.uid AND u.uid = $selecteduid GROUP BY u.uid ORDER BY u.username";
		$memberlistQuery = DB_query($sql);
		if (DB_numRows($memberlistQuery) == 0 ) { 
			$selecteduid = ''; // All Users
		}
	}
} else {
	$notify_url_start = $_CONF['site_url'];

	$selecteduid = $_USER['uid']; // Current Users
}

// Means forum select was set to all again
if ($forum == -1) {
	$forum = '';
}

// Check is anonymous users can access and if not, regular user can access
forum_chkUsercanAccess(true, $forum, $topic);

// Validate filter
if (!empty($notifytype) && ($notifytype > 4 || $notifytype < 1)) {
	COM_handle404('/forum/index.php');
}

$display = '';

// Debug Code to show variables
$display .= gf_showVariables();

// NOTIFY CODE -> SAVE
if (isset($_REQUEST['submit'])) {
    if (($_REQUEST['submit'] == 'save') && ($id != 0)) {
		// **********************************************
		// FOR: Save notification on menu on viewtopic.php (for topic and forum notifications)
		// **********************************************
	    $sql = "SELECT * FROM {$_TABLES['forum_watch']} WHERE (topic_id = $id AND uid = $selecteduid OR ";
        $sql .= "(forum_id = $forum AND topic_id = 0 AND uid = $selecteduid))";
        $notifyquery = DB_query("$sql");
        $pid = DB_getItem($_TABLES['forum_topic'],'pid',"id = $id");
        if ($pid == 0) {
            $pid = $id;
        }
        if (DB_numRows($notifyquery) > 0 ) {
            $A = DB_fetchArray($notifyquery);
            if ($A['topic_id'] == 0) {     // User has subscribed to complete forum
               // Check and see if user has a non-subscribe record for this topic id
                $query = DB_query("SELECT id FROM {$_TABLES['forum_watch']} WHERE uid = $selecteduid AND forum_id = $forum AND topic_id < 0 " );
                if (DB_numRows($query) > 0) {
                    list($watchrec) = DB_fetchArray($query);
                    DB_query("DELETE FROM {$_TABLES['forum_watch']} WHERE uid = $selecteduid AND id = $watchrec");
                }  else {
                    DB_query("INSERT INTO {$_TABLES['forum_watch']} (forum_id, topic_id, uid, date_added) VALUES ($forum, $pid, $selecteduid, now())");
                }
                if (($_USER['email'] != '')  AND COM_isEmail($_USER['email'])) {
                    COM_redirect($_CONF['site_url'] . "/forum/viewtopic.php?msg=2&amp;showtopic=$id");
                } else {
                    // Invalid or no email address remind user to add one
                    COM_redirect($_CONF['site_url'] . "/forum/viewtopic.php?msg=12&amp;showtopic=$id");
                }
            } else {
                COM_redirect($_CONF['site_url']. "/forum/viewtopic.php?msg=3&amp;showtopic=$id");
            }
        } else {
            DB_query("INSERT INTO {$_TABLES['forum_watch']} (forum_id, topic_id, uid, date_added) VALUES ($forum, $pid, $selecteduid, now())");
            $nid = -$id;
            DB_query("DELETE FROM {$_TABLES['forum_watch']} WHERE uid = $selecteduid AND forum_id = $forum AND topic_id = $nid");          
            if (($_USER['email'] != '')  AND COM_isEmail($_USER['email'])) {
                COM_redirect($_CONF['site_url'] . "/forum/viewtopic.php?msg=2&amp;showtopic=$id");
            } else {
                // Invalid or no email address remind user to add one
                COM_redirect($_CONF['site_url'] . "/forum/viewtopic.php?msg=12&amp;showtopic=$id");
            }
        }
        COM_output($display);
        exit();
    } elseif (($_REQUEST['submit'] == 'delete') AND ($id != 0))  {
		// **********************************************
		// FOR: Remove Button on Subscription page
		// **********************************************
		if ($isAdminUser AND empty($selecteduid)) {
			$delUserID = DB_getItem($_TABLES['forum_watch'], 'uid', "id = $id");
		} else {
			$delUserID = $selecteduid;
		}		
		
		if (!empty($delUserID)) {
			// Get forum from id
			$delForumID = DB_getItem($_TABLES['forum_watch'], 'forum_id', "uid = $delUserID AND id = $id");
			// Now delete any topic exceptions associated with the forum subscription being deleted (topics with negative values)
			if (!empty($delForumID)) {
				DB_query("DELETE FROM {$_TABLES['forum_watch']} WHERE uid = $delUserID AND forum_id = $delForumID and topic_id < 0");
			}
			// Now delete the forum notification
			DB_query("DELETE FROM {$_TABLES['forum_watch']} WHERE uid = $delUserID AND id = $id");
		}
		
		// Set Notification deleted message and fall through to rest of page
		COM_setSystemMessage($LANG_GF02['msg146']);
    } elseif (($_REQUEST['submit'] == 'delete2') AND ($id != ''))  {
		// **********************************************
		// FOR: Remove notification on menu on viewtopic.php (for topic and forum notifications)
		// **********************************************
        // Check and see if subscribed to complete forum and if so - unsubscribe to just this topic
        if (DB_getItem($_TABLES['forum_watch'], 'topic_id', "uid = $selecteduid AND id = $id") == 0 ) {
            $ntopic = -$topic;  // Negative Value
			// Need to include user id for security
            DB_query("DELETE FROM {$_TABLES['forum_watch']} WHERE uid = $selecteduid AND forum_id = $forum AND topic_id = $topic");
            DB_query("DELETE FROM {$_TABLES['forum_watch']} WHERE uid = $selecteduid AND forum_id = $forum AND topic_id = $ntopic");
            DB_query("INSERT INTO {$_TABLES['forum_watch']} (forum_id, topic_id, uid, date_added) VALUES ($forum, $ntopic, $selecteduid, now())");
        } else {
            DB_query("DELETE FROM {$_TABLES['forum_watch']} WHERE uid = $selecteduid AND id = $id");
        }
        COM_redirect($_CONF['site_url'] . "/forum/viewtopic.php?msg=4&amp;showtopic=$topic");
    }
} else {
	// **********************************************
	// FOR: Multiple Deletes on Subscription page
	// **********************************************
	if ($op == 'delchecked' && isset($_POST['chk_record_delete'])) {
		foreach ($_POST['chk_record_delete'] as $id) {
			$id = COM_applyFilter($id);
			if ($isAdminUser AND empty($selecteduid)) {
				$delUserID = DB_getItem($_TABLES['forum_watch'], 'uid', "id = $id");
			} else {
				$delUserID = $selecteduid;
			}
			
			if (!empty($delUserID)) {
				if ($notifytype == 2) { // Forum Notifications
					// Get forum from id
					$delForumID = DB_getItem($_TABLES['forum_watch'], 'forum_id', "uid = $delUserID AND id = $id");
					// Now delete any topic exceptions associated with the forum subscription being deleted (topics with negative values)
					if (!empty($delForumID)) {
						DB_query("DELETE FROM {$_TABLES['forum_watch']} WHERE uid = $delUserID AND forum_id = $delForumID and topic_id < 0");
					}
				}
				DB_query("DELETE FROM {$_TABLES['forum_watch']} WHERE uid = $delUserID AND id = $id");
			}
		}
		
		// Set Notification deleted message and fall through to rest of page
		COM_setSystemMessage($LANG_GF02['msg146']);	
	}	
}

// NOTIFY MAIN
// Page Navigation Logic
$show = $CONF_FORUM['show_messages_perpage'];
// Check if this is the first page.
if ($page === 0) {
     $page = 1;
}

$report = COM_newTemplate(CTL_plugin_templatePath('forum'));
$report->set_file (array (
                  'report'         => 'reports/notifications.thtml',
                  'forum_links'    => 'forum_links.thtml' ));

$report->set_block('report', 'notification_record');
$report->set_block('report', 'no_records_message');
$report->set_block('report', 'links');
$report->set_block('forum_links', 'trash_link');
$report->set_block('forum_links', 'return_link');

$report->set_var ('imgset', $CONF_FORUM['imgset']);
$report->set_var ('layout_url', $CONF_FORUM['layout_url']);
$report->set_var ('LANG_TITLE', $LANG_GF02['msg89']);
$report->set_var ('lang_subscriptions', $LANG_GF01['SUBSCRIPTIONS']);

if ($isAdminUser) {
	$report->set_var('isAdminUser', $isAdminUser);
	$report->set_var('LANG_selectmember', $LANG_GF02['msg107']);
	$report->set_var('user_options',f_userjump(2, $selecteduid));
}
if (!empty($selecteduid)) {
	$report->set_var('uid', $selecteduid); 
}


$report->set_var('filter_options', COM_optionListFromLangVariables('LANG_GF08', $notifytype));
if ($notifytype == 3) {
	if ($isAdminUser) {
		$report->set_var('lang_notify_filter_desc', $LANG_GF02['msg139c']);	
	} else {
		$report->set_var('lang_notify_filter_desc', $LANG_GF02['msg138c']);	
	}
} elseif ($notifytype == 2) {
	if ($isAdminUser) {
		$report->set_var('lang_notify_filter_desc', $LANG_GF02['msg139b']);	
	} else {
		$report->set_var('lang_notify_filter_desc', $LANG_GF02['msg138b']);	
	}
} else {
	if ($isAdminUser) {
		$report->set_var('lang_notify_filter_desc', $LANG_GF02['msg139a']);	
	} else {
		$report->set_var('lang_notify_filter_desc', $LANG_GF02['msg138a']);	
	}
}
$report->set_var('notifytype', $notifytype);
$report->set_var('forum', $forum);   
//$report->set_var ('LANG_Heading1', $LANG_GF01['ID']);
$report->set_var ('LANG_Heading2', $LANG_GF01['FORUM']);
$report->set_var ('LANG_Heading3', $LANG_GF01['SUBJECT']);
$report->set_var ('LANG_Heading4', $LANG_GF01['DATEADDED']);
$report->set_var ('LANG_Heading5', $LANG_GF01['STARTEDBY']);
$report->set_var ('LANG_Heading6', $LANG_GF01['VIEWS']);
$report->set_var ('LANG_Heading7', $LANG_GF01['REPLIES']);
$report->set_var ('LANG_Heading8', $LANG_GF01['REMOVE']);
$report->set_var ('LANG_Heading9', $LANG_GF01['USER']);

$report->set_var ('LANG_deleteall', $LANG_GF01['DELETEALL']);
$report->set_var ('LANG_DELALLCONFIRM', $LANG_GF01['DELALLCONFIRM']);
$report->parse ('trash_link', 'trash_link');

// Display warning if no email found (usually happens with user oauth accounts)
if (($_USER['email'] == '') OR !COM_isEmail($_USER['email'])) {
	$emailMsg = COM_showMessageText($LANG_GF02['msg145'], $LANG_GF01['WARNING']);
} else {
	$emailMsg = '';
}

if ($isAdminUser) {
	// $navbar created when gf_functions.php included
	$navbar->set_selected($LANG_GF01['SUBSCRIPTIONS']);
	$report->set_var('navmenu', $navbar->generate());
} elseif ($CONF_FORUM['usermenu'] == 'navbar') {
    $report->set_var('navmenu', forumNavbarMenu($LANG_GF01['SUBSCRIPTIONS']) . $emailMsg);
} else {
    $report->set_var('navmenu', $emailMsg);
}

$sql = "SELECT id, forum_id, topic_id, date_added, uid FROM {$_TABLES['forum_watch']} WHERE 1 = 1";
if (!empty($selecteduid)) {
	$sql .= " AND uid = $selecteduid";   
}
if ($forum > 0 ) {
    $sql .= " AND forum_id = $forum";   
}
if ($notifytype == '2') {
    $sql .= " AND topic_id = 0";
} elseif ($notifytype == '3') {
    $sql .= " AND topic_id < 0";
} else {
    $sql .= " AND topic_id > 0";
}

$sql .= " ORDER BY forum_id ASC, date_added DESC";
$notifications = DB_query($sql);
$nrows = DB_numRows($notifications);
$numpages = ceil($nrows / $show);
$offset = ($page - 1) * $show;

$url_sep = '?';
if (!empty($notifytype)) {
	$notify_url = "{$url_sep}filter=$notifytype";
	$url_sep = '&amp;';
} else {
	$notify_url = "";
}
if ($isAdminUser && !empty($selecteduid)) {
	$admin_url = "{$url_sep}uid=$selecteduid";
	$url_sep = '&amp;';
} else {
	$admin_url = "";
}
if ($forum > 0 ) {
    $forum_url = "{$url_sep}forum=$forum";   
	$url_sep = '&amp;';
} else {
	$forum_url = "";
}

$base_url = $notify_url_start . "/forum/notify.php$notify_url$admin_url$forum_url";
$report->set_var ('phpself', $base_url);
$report->set_var('select_forum', f_forumjump($base_url, $forum));

$sql .= " LIMIT $offset, $show";
$notifications = DB_query($sql);

$i = 0;
while (list($notify_recid,$forum_id,$topic_id,$date_added, $uid) = DB_fetchArray($notifications)) {
    $forum_name = DB_getItem($_TABLES['forum_forums'],"forum_name","forum_id='$forum_id'");
	$forum_link = '<a href="' . $notify_url_start . '/forum/index.php?forum=' .$forum_id. '" title="' . $forum_name . '">' . $forum_name . '</a>';
    if ($topic_id == '0') {
        $subject = '';
		$topic_link = '';
    } else {
        if ($topic_id < 0) {
            $neg_subscription = true;
            $topic_id = -$topic_id;
        } else {
            $neg_subscription = false;
        }
        $result = DB_query("SELECT subject, name, replies, views, uid, id FROM {$_TABLES['forum_topic']} WHERE id = $topic_id");
        $A = DB_fetchArray($result);
        if ($A['subject'] == '') {
			// This only happens if forum topics get deleted and the watch does not (forum v2.9.3 and lower)
            $topic_link = $LANG_GF01['MISSINGSUBJECT'];
        } elseif (strlen($A['subject']) > 50) {
            $subject = htmlspecialchars(COM_truncate($A['subject'], 50, '...'), ENT_QUOTES, $CONF_FORUM['charset']);
        } else {
            $subject = htmlspecialchars($A['subject'], ENT_QUOTES, $CONF_FORUM['charset']);
        }
		if (!empty($subject)) {
			$topic_link = '<a href="' . $notify_url_start . '/forum/viewtopic.php?showtopic=' . $topic_id . '" title="' . $subject . '">' . $subject . '</a>';
		}
    }

    $report->set_var ('id', $notify_recid);
    $report->set_var ('csscode', $i%2+1);
    
	if ($isAdminUser && empty($selecteduid)) {
		$username = COM_getDisplayName($uid);
		$userlink = COM_getProfileLink($uid, $username);			
		$report->set_var('user_link', $userlink);	
	}
	
	$report->set_var ('forum_link', $forum_link);
	if (!empty($subject)) {
		$report->set_var ('linksubject', htmlspecialchars($subject,ENT_QUOTES,$CONF_FORUM['charset']));
	}
    $report->set_var ('topic_link', $topic_link);
	$report->set_var ('date_added', $date_added);
	if (isset($A['name'])) {
		if ($A['uid'] > 1) {
			$username = COM_getDisplayName($A['uid']);
			$userlink = COM_getProfileLink($A['uid'], $username);			
			$report->set_var ('topicauthor', $userlink);
		} else {
			$report->set_var ('topicauthor', $A['name']);
		}
		$report->set_var ('views', COM_numberFormat($A['views']));
		$report->set_var ('replies', COM_numberFormat($A['replies']));		
	}
    $report->set_var ('topic_id', $topic_id);
    $report->set_var ('notify_id', $notify_recid);
    $report->set_var ('LANG_REMOVE', $LANG_GF01['REMOVE']);
    $report->parse ('notification_record', 'notification_record',true);
    $i++;
}

if ($nrows == 0) {
    $report->set_var ('message',$LANG_GF02['msg44']);
    $report->parse ('no_records_message', 'no_records_message');
} else {
    $report->set_var ('pagenavigation', COM_printPageNavigation($base_url, $page, $numpages));
    if ($forum > 0) {
        $report->set_var ('LANG_return', $LANG_GF02['msg144']);
        $report->set_var ('returnlink', "{$notify_url_start}/forum/index.php?forum=$forum");
    } else {
        $report->set_var ('LANG_return', $LANG_GF02['msg175']);
        $report->set_var ('returnlink', "{$notify_url_start}/forum/index.php");
    }
    $report->parse ('return_link', 'return_link');
    $report->parse ('links', 'links');
}

if ($isAdminUser) {
	$report->set_var('block_start', COM_startBlock($LANG_GF97['gfsubscriptions']));
} else {
	$report->set_var('block_start', COM_startBlock($CONF_FORUM['forums_name']));
}
$report->set_var('block_end', COM_endBlock()); 

$report->parse ('output', 'report');
$display .= $report->finish ($report->get_var('output'));
$display = gf_createHTMLDocument($display);

COM_output($display);
