<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog Forums Plugin 2.8.0                                               |
// +---------------------------------------------------------------------------+
// | gf_functions.php                                                          |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'gf_functions.php') !== false) {
    die('This file can not be used on its own.');
}


// Needed to do this for Github Issue #57
// Created Admin Forum Subscriptions page which reuses notify.php. This means this file can get called from the publich_html/forum and admin directories
$foundFile = false;
$included_files = get_included_files();
foreach ($included_files as $filename) {
    if (basename($filename) == 'lib-common.php') {
		$foundFile = true;
	}
}
if (!$foundFile) {
	require_once '../../../lib-common.php';
}

// Needed to set these since global scope is different when upgrading (at least when you upload the plugin)
global $_PLUGINS, $LANG_GF06, $_CONF;
if (!in_array('forum', $_PLUGINS)) {
	COM_handle404();
}

/**
* Security check to ensure user even belongs on this page
*/
require_once $_CONF['path_admin'] . 'auth.inc.php';

if (!SEC_hasRights('forum.edit')) {
    $display = COM_showMessageText($MESSAGE[29], $MESSAGE[30]);
    $display = COM_createHTMLDocument($display, array('pagetitle' => $MESSAGE[30]));
    COM_accessLog("User {$_USER['username']} tried to illegally access the forum administration.");
    COM_output($display);
    exit;
}

gf_updateSystem();

require_once $_CONF['path_system'] . 'classes/navbar.class.php';
$navbar = new navbar;
$navbar->add_menuitem($LANG_GF06['1'], $_CONF['site_admin_url'] .'/plugins/forum/index.php');
$navbar->set_onclick($LANG_GF06['1'], 'location.href="' . "{$_CONF['site_admin_url']}/plugins/forum/index.php" . '";'); // Added as a fix for the navbar class (since uikit tabs do not support urls)
$navbar->add_menuitem($LANG_GF06['3'], $_CONF['site_admin_url'] .'/plugins/forum/boards.php');
$navbar->set_onclick($LANG_GF06['3'], 'location.href="' . "{$_CONF['site_admin_url']}/plugins/forum/boards.php" . '";'); // Added as a fix for the navbar class (since uikit tabs do not support urls)
$navbar->add_menuitem($LANG_GF06['4'], $_CONF['site_admin_url'] .'/plugins/forum/mods.php');
$navbar->set_onclick($LANG_GF06['4'], 'location.href="' . "{$_CONF['site_admin_url']}/plugins/forum/mods.php" . '";'); // Added as a fix for the navbar class (since uikit tabs do not support urls)
$navbar->add_menuitem($LANG_GF06['5'], $_CONF['site_admin_url'] .'/plugins/forum/migrate.php');
$navbar->set_onclick($LANG_GF06['5'], 'location.href="' . "{$_CONF['site_admin_url']}/plugins/forum/migrate.php" . '";'); // Added as a fix for the navbar class (since uikit tabs do not support urls)
$navbar->add_menuitem($LANG_GF06['6'], $_CONF['site_admin_url'] .'/plugins/forum/messages.php');
$navbar->set_onclick($LANG_GF06['6'], 'location.href="' . "{$_CONF['site_admin_url']}/plugins/forum/messages.php" . '";'); // Added as a fix for the navbar class (since uikit tabs do not support urls)
$navbar->add_menuitem($LANG_GF06['7'], $_CONF['site_admin_url'] .'/plugins/forum/notify.php');
$navbar->set_onclick($LANG_GF06['7'], 'location.href="' . "{$_CONF['site_admin_url']}/plugins/forum/notify.php" . '";'); // Added as a fix for the navbar class (since uikit tabs do not support urls)
$navbar->add_menuitem($LANG_GF06['8'], $_CONF['site_admin_url'] .'/plugins/forum/ips.php');
$navbar->set_onclick($LANG_GF06['8'], 'location.href="' . "{$_CONF['site_admin_url']}/plugins/forum/ips.php" . '";'); // Added as a fix for the navbar class (since uikit tabs do not support urls)
$navbar->add_menuitem($LANG_GF06['2'], $_CONF['site_admin_url'] .'/plugins/forum/settings.php');
$navbar->set_onclick($LANG_GF06['2'], 'location.href="' . "{$_CONF['site_admin_url']}/plugins/forum/settings.php" . '";'); // Added as a fix for the navbar class (since uikit tabs do not support urls)

function gf_resyncforum($id) {
    global $_CONF,$_TABLES;
	
	$recCount = 0;

    COM_errorLog("Re-Syncing Forum id:$id");
	
	// Update forum record for: last_post_rec, topic_count, post_count
	// Even for forums with 0 posts as something could have happened 
	gf_updateLastPost($id);
	
    // Update all the Topics lastupdated timestamp to that of the last posted comment
    $topicsQuery = DB_query("SELECT id FROM {$_TABLES['forum_topic']} WHERE forum=$id AND pid=0");
    $topicCount = DB_numRows($topicsQuery);
    if ($topicCount > 0) {
        while($trecord = DB_fetchArray($topicsQuery)) {
            $recCount++;
			
			// Update topic record for: lastupdated, last_reply_rec, numreplies
			gf_updateLastPost(0, $trecord['id']);
        }
        COM_errorLog("$recCount Topic Records Updated");
    } else {
        COM_errorLog("No topic records to resync");
    }
	
	return $recCount;
}

// This function was created to clean the forum tables of orphan records.
// This could happen mainly due to deleting of forum posts in the Admin Posts list where not all records associated was also deleted
// For Github issue #82 https://github.com/Geeklog-Plugins/forum/issues/82
function gf_cleanforum($id = 0) {
    global $_CONF,$_TABLES;

	$delRowCount = 0;
	$orphanTopicRowCount = 0;
	
	if ($id > 0) {
		COM_errorLog("Cleaning Forum id:$id");
		$forumSQL = "AND forum = $id";
		$forumModSQL = "AND mod_forum = $id";
		$forumWatchSQL = "AND forum_id = $id";
		$forumWatchDupSQL = "AND fw1.forum_id = $id";
	} else {
		COM_errorLog("Cleaning all Forums");
		$forumSQL = "";
		$forumModSQL = "";
		$forumWatchSQL = "";
		$forumWatchDupSQL = "";
	}

	// last_reply_rec should only be used by parent topics
	// This could happen prior to Forum v2.9.4 where gf_updateLastPost was not used to recalculate for most post actions
	DB_query("UPDATE {$_TABLES['forum_topic']} SET last_reply_rec = 0 WHERE pid > 0 AND last_reply_rec > 0 $forumSQL");
	
	// *************************************
	// Recorded the SQL below just for testing purposes if needed later
	// The SQL below will return all parent topics with replies that are correct
	// SELECT * FROM gl_forum_topic ft1 WHERE pid != 0 AND EXISTS (SELECT id from gl_forum_topic ft2 WHERE ft2.id = ft1.pid AND ft2.pid = 0)	
	// The SQL below will return all parent topics with no replies that are correct
	// SELECT * FROM gl_forum_topic ft1 WHERE pid = 0 AND NOT EXISTS (SELECT id from gl_forum_topic ft2 WHERE ft2.id = ft1.pid AND ft2.pid = 0)
	// The total number of records from these 2 SQL statements above should match the total number of posts on the Forum Statistics page
	// *************************************
	// Find orphan replies that do not have a parent topic that exists.
	// This could happen prior to Forum v2.9.4 where Forum Posts Admin list allowed deleting of parent topic, and the replies of these parent topics where not also deleted (there should not be a lot of these).
	$sql = "SELECT MIN(id), pid FROM {$_TABLES['forum_topic']} ft1 
			WHERE pid != 0 $forumSQL 
			AND NOT EXISTS (SELECT id from {$_TABLES['forum_topic']} ft2 WHERE ft2.id = ft1.pid) 
			GROUP BY pid";
    $result = DB_query($sql);
    $numRows = DB_numRows($result);
    if ($numRows > 0) {
        while($A = DB_fetchArray($result)) {
            $orphanTopicRowCount++;
			// Now update old pid with new min forum post id
			DB_query("UPDATE {$_TABLES['forum_topic']} SET pid = {$A['id']} WHERE pid = {$A['pid']}");
			// Now update min forum post id and give pid of 0
			DB_query("UPDATE {$_TABLES['forum_topic']} SET pid = 0 WHERE id = {$A['id']}");
		}
	}
	
	// ***********************
	// Clean forum_moderators table
	// If user doesn't exist anymore then delete
	$result = DB_query("DELETE FROM {$_TABLES['forum_moderators']} WHERE mod_uid NOT IN (SELECT uid from {$_TABLES['users']}) $forumModSQL");
	$delRowCount = $delRowCount + DB_affectedRows($result);
	
	// ***********************
	// Clean forum_userinfo table
	// If user doesn't exist anymore then delete
	$result = DB_query("DELETE FROM {$_TABLES['forum_userinfo']} WHERE uid NOT IN (SELECT uid from {$_TABLES['users']})");
	$delRowCount = $delRowCount + DB_affectedRows($result);
	
	// ***********************
	// Clean forum_userprefs table
	// If user doesn't exist anymore then delete
	$result = DB_query("DELETE FROM {$_TABLES['forum_userprefs']} WHERE uid NOT IN (SELECT uid from {$_TABLES['users']})");
	$delRowCount = $delRowCount + DB_affectedRows($result);
		
	// ***********************
	// Clean forum_log table
	// If user doesn't exist anymore then delete
	$result = DB_query("DELETE FROM {$_TABLES['forum_log']} WHERE uid NOT IN (SELECT uid from {$_TABLES['users']}) $forumSQL");
	$delRowCount = $delRowCount + DB_affectedRows($result);
	// If forum doesn't exist anymore then delete
	$result = DB_query("DELETE FROM {$_TABLES['forum_log']} WHERE forum NOT IN (SELECT forum_id from {$_TABLES['forum_forums']}) $forumSQL");
	$delRowCount = $delRowCount + DB_affectedRows($result);
	// If topic (and it is parent) doesn't exist anymore then delete
	$result = DB_query("DELETE FROM {$_TABLES['forum_log']} WHERE topic NOT IN (SELECT id from {$_TABLES['forum_topic']}) $forumSQL");
	$delRowCount = $delRowCount + DB_affectedRows($result);	
	
	// ***********************
	// Clean forum_watch table
	// If user doesn't exist anymore then delete
	$result = DB_query("DELETE FROM {$_TABLES['forum_watch']} WHERE uid NOT IN (SELECT uid from {$_TABLES['users']}) $forumWatchSQL");
	$delRowCount = $delRowCount + DB_affectedRows($result);
	// If forum doesn't exist anymore then delete
	$result = DB_query("DELETE FROM {$_TABLES['forum_watch']} WHERE forum_id NOT IN (SELECT forum_id from {$_TABLES['forum_forums']}) $forumWatchSQL");
	$delRowCount = $delRowCount + DB_affectedRows($result);
	// If topic (and it is a parent) doesn't exist anymore then delete (also check negative topic_ids)
	$result = DB_query("DELETE FROM {$_TABLES['forum_watch']} WHERE topic_id != 0 AND ABS(topic_id) NOT IN (SELECT id from {$_TABLES['forum_topic']} WHERE pid = 0) $forumWatchSQL");
	$delRowCount = $delRowCount + DB_affectedRows($result);
	// Delete Topic Exemptions that don't have a corresponding Forum Notification
	$sql = "SELECT fw1.id fwid FROM {$_TABLES['forum_watch']} fw1 WHERE fw1.topic_id < 0 AND ABS(fw1.topic_id) NOT IN (SELECT ft.id from {$_TABLES['forum_topic']} ft, {$_TABLES['forum_watch']} fw2 WHERE ft.forum = fw2.forum_id AND fw2.topic_id = 0 AND fw1.uid = fw2.uid) $forumWatchDupSQL";
    $result = DB_query($sql);
    $numRows = DB_numRows($result);
    if ($numRows > 0) {
		$delIdList = '';
        while($A = DB_fetchArray($result)) {
			// Now update old pid with new min forum post id
			if (!empty($delIdList)) {
				$delIdList .= ", ";
			}
			$delIdList .= $A['fwid'];
		}
		
		$result = DB_query("DELETE FROM {$_TABLES['forum_watch']} WHERE id IN ($delIdList)");
		$delRowCount = $delRowCount + DB_affectedRows($result);
	}	
	// Remove any duplicate notifications (topic or forum) (a bug allowed this to happen in certain situations at one point)
	$result = DB_query("DELETE fw1 FROM {$_TABLES['forum_watch']} fw1, {$_TABLES['forum_watch']} fw2 WHERE fw1.id > fw2.id AND fw1.forum_id = fw2.forum_id AND fw1.topic_id = fw2.topic_id AND fw1.uid = fw2.uid $forumWatchDupSQL");
	$delRowCount = $delRowCount + DB_affectedRows($result);

	COM_errorLog("Number of Orphan Topic Records (no parent topic found) Fixed: $orphanTopicRowCount - Number of Records Cleaned from Forum Tables: $delRowCount");
	
	return array($orphanTopicRowCount, $delRowCount);
}

function gf_updateSystem() {
    global $_CONF, $_TABLES;

    if (function_exists('COM_versionCompare')) { // This function was introduced in Geeklog 1.8.0
        // So, if we have it, we can use tabs in the configuration UI
        // Which were also introduced in version 1.8.0 of Geeklog
        $code_version = plugin_chkVersion_forum();
        $result = DB_query("SELECT COUNT(*) AS count FROM {$_TABLES['conf_values']} "
                         . "WHERE group_name = 'forum' AND type = 'tab'");
        $A = DB_fetchArray($result);
        if ($A['count'] == 0 && version_compare($code_version, '2.7.99', '>')) {
            require_once $_CONF['path'] . 'plugins/forum/install_defaults.php';
            forum_update_ConfValues_addTabs();
        }
    }
}

?>
