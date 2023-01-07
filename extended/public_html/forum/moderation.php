<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog Forums Plugin 2.8.0                                               |
// +---------------------------------------------------------------------------+
// | moderation.php                                                            |
// | Forum Moderation Program                                                  |
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

require_once '../lib-common.php';

if (!in_array('forum', $_PLUGINS)) {
    COM_handle404();
    exit;
}

require_once $CONF_FORUM['path_include'] . 'gf_format.php';
require_once $CONF_FORUM['path_include'] . 'gf_showtopic.php';

$display = '';
$promptform = '';

// Debug Code to show variables
$display .= gf_showVariables();

// Pass thru filter any get or post variables to only allow numeric values and remove any hostile data
$confirmbanip     = isset($_POST['confirmbanip'])     ? COM_applyFilter($_POST['confirmbanip'])          : '';
$confirm_move     = isset($_POST['confirm_move'])     ? COM_applyFilter($_POST['confirm_move'])          : '';
$hostip           = isset($_POST['hostip'])           ? COM_applyFilter($_POST['hostip'])                : '';
$modconfirmdelete = isset($_POST['modconfirmdelete']) ? COM_applyFilter($_POST['modconfirmdelete'])      : '';
$modfunction      = isset($_REQUEST['modfunction'])   ? COM_applyFilter($_REQUEST['modfunction'])        : '';
$movetoforumid    = isset($_REQUEST['movetoforum'])   ? COM_applyFilter($_REQUEST['movetoforum'], true)  : '';
$movetitle 		  = isset($_REQUEST['movetitle']) 	  ? COM_stripslashes($_REQUEST['movetitle']) 		 : '';
$msgid            = isset($_REQUEST['msgid'])         ? COM_applyFilter($_REQUEST['msgid'],true)         : '';
$submit           = isset($_REQUEST['submit'])        ? COM_applyFilter($_POST['submit'])                : '';

// Check is anonymous users can access and if not, regular user can access. Also if have access to topic
if (empty($msgid)) {
	$msgid = 0; // let's intentionally error out in forum_chkUsercanAccess
}
forum_chkUsercanAccess(true, '', $msgid);
// Now check all ids if available
if (!empty($movetoforumid)) { // Move to forum id
	forum_chkUsercanAccess(true, $movetoforumid);
}

// Get Parent Topic and forum ids
$result = DB_query("SELECT forum, pid FROM {$_TABLES['forum_topic']} WHERE id = $msgid");
list($forum, $msgpid) = DB_fetchArray($result);
if ($msgpid == 0) {// Then this is a parent topic already
	$msgpid = $msgid;
	$msgidIsParent = true;
} else {
	$msgidIsParent = false;	
}

ForumHeader('', $forum, '', $display);

if (forum_modPermission('', $_USER['uid'])) {
	if (forum_modPermission($forum, $_USER['uid'])) {

		//Moderator check OK, everything dealing with moderator permissions go here.
		if ($modconfirmdelete == 1 && $msgid != '') {
			if ($submit == $LANG_GF01['CANCEL']) {
				COM_redirect(html_entity_decode(forum_buildForumPostURL($msgpid)));
			} else {
				forum_deleteForumPost($msgid);

				if ($msgidIsParent) { // Parent topic being deleted then
					COM_redirect($_CONF['site_url'] . "/forum/index.php?msg=3&amp;forum=$forum");
				} else {
					COM_redirect(html_entity_decode(forum_buildForumPostURL($msgpid, '&amp;msg=5', '', false)));
				}
			}
		}

		if ($confirmbanip == '1') {
			if ($submit == $LANG_GF01['CANCEL']) {
				COM_redirect(html_entity_decode(forum_buildForumPostURL($msgid)));
			} else {
				if (DB_getItem($_TABLES['forum_banned_ip'], 'host_ip', "host_ip = '$hostip'")) {
					DB_query("DELETE FROM {$_TABLES['forum_banned_ip']} WHERE host_ip = '$hostip'");
					COM_redirect(html_entity_decode(forum_buildForumPostURL($msgid, '&amp;msg=13', '', false)));
				} else {
					DB_query("INSERT INTO {$_TABLES['forum_banned_ip']} (host_ip) VALUES ('$hostip')");
					COM_redirect(html_entity_decode(forum_buildForumPostURL($msgid, '&amp;msg=6', '', false)));
				}
			}
		}

		if ($confirm_move == '1' AND forum_modPermission($forum,$_USER['uid'],'mod_move') AND $msgid != 0) {
			if ($submit == $LANG_GF01['CANCEL']) {
				COM_redirect(html_entity_decode(forum_buildForumPostURL($msgid)));
			} else {
				$date = time();
				$movetitle = gf_preparefordb($movetitle,'text');
				/* Check and see if we are splitting this forum thread */
				if (isset($_POST['splittype'])) {  // - Yes
					$curpostpid = DB_getItem($_TABLES['forum_topic'],"pid","id='$msgid'");
					if ($_POST['splittype'] == 'single') {  // Move only the single post - create a new topic
						$topicdate = DB_getItem($_TABLES['forum_topic'],"date","id='$msgid'");
						$sql  = "UPDATE {$_TABLES['forum_topic']} SET forum='$movetoforumid', pid='0',lastupdated='$topicdate', ";
						$sql .= "subject='$movetitle', replies = '0' WHERE id='$msgid' ";
						DB_query($sql);
						PLG_itemSaved($msgid, 'forum');
						DB_query("UPDATE {$_TABLES['forum_topic']} SET replies=replies-1 WHERE id='$curpostpid' ");

						// Update Topic and Post Count for the effected forums
						DB_query("UPDATE {$_TABLES['forum_forums']} SET topic_count=topic_count+1, post_count=post_count+1 WHERE forum_id=$movetoforumid");
						$topicsQuery = DB_query("SELECT id FROM {$_TABLES['forum_topic']} WHERE forum=$forum AND pid=0");
						$topic_count = DB_numRows($topicsQuery);
						DB_query("UPDATE {$_TABLES['forum_forums']} SET topic_count=$topic_count, post_count=post_count-1 WHERE forum_id=$forum");

						// Update the Forum and topic indexes
						gf_updateLastPost($forum,$curpostpid);
						gf_updateLastPost($movetoforumid,$msgid);
					} else {
						$movesql = DB_query("SELECT id,date FROM {$_TABLES['forum_topic']} WHERE pid='$curpostpid' AND id >= '$msgid'");
						$numreplies = DB_numRows($movesql);
						$topicparent = 0;
						while($movetopic = DB_fetchArray($movesql)) {
							if ($topicparent == 0) {
								$sql  = "UPDATE {$_TABLES['forum_topic']} SET forum='$movetoforumid', pid='0',lastupdated='{$movetopic['date']}', ";
								$sql .= "replies=$numreplies - 1, subject='$movetitle' WHERE id='{$movetopic['id']}'";
								DB_query($sql);
								PLG_itemSaved($movetopic['id'], 'forum');
								$topicparent = $movetopic['id'];
							} else {
								// Decided not to include ",subject='$movetitle'" here in UPDATE as these are replies and the user may have changed the subject
								$sql  = "UPDATE {$_TABLES['forum_topic']} SET forum='$movetoforumid', pid='$topicparent' ";
								$sql .= "WHERE id='{$movetopic['id']}'";
								DB_query($sql);
								PLG_itemSaved($movetopic['id'], 'forum');
								$topicdate = DB_getItem($_TABLES['forum_topic'],"date","id='{$movetopic['id']}'");
								DB_query("UPDATE {$_TABLES['forum_topic']} SET lastupdated='$topicdate' WHERE id='$topicparent'");
							}
						}
						// Update the Forum and topic indexes
						gf_updateLastPost($forum,$curpostpid);
						gf_updateLastPost($movetoforumid,$topicparent);

						// Update Topic and Post Count for the effected forums
						DB_query("UPDATE {$_TABLES['forum_forums']} SET topic_count=topic_count+1, post_count=post_count+$numreplies WHERE forum_id=$movetoforumid");
						DB_query("UPDATE {$_TABLES['forum_forums']} SET topic_count=topic_count-1, post_count=post_count-$numreplies WHERE forum_id=$forum");
					}
					
					$destUrl = $_CONF['site_url'] . "/forum/viewtopic.php?msg=7&amp;showtopic=$msgid";
				} else {  // Move complete topic
					$moveResult = DB_query("SELECT id FROM {$_TABLES['forum_topic']} WHERE pid=$msgid");
					$postCount = DB_numRows($moveResult) +1;  // Need to account for the parent post
					while($movetopic = DB_fetchArray($moveResult)) {
						// Decided not to include ",subject='$movetitle'" here in UPDATE as these are replies and the user may have changed the subject
						DB_query("UPDATE {$_TABLES['forum_topic']} SET forum='$movetoforumid' WHERE id='{$movetopic['id']}'");
						PLG_itemSaved($movetopic['id'], 'forum');
					}
					// Update any topic subscription records - need to change the forum ID record
					DB_query("UPDATE {$_TABLES['forum_watch']} SET forum_id = '$movetoforumid' WHERE topic_id='{$msgid}'");
					DB_query("UPDATE {$_TABLES['forum_topic']} SET forum = '$movetoforumid', subject='$movetitle', moved = '1' WHERE id=$msgid");
					PLG_itemSaved($msgid, 'forum');

					// Update the Last Post Information
					gf_updateLastPost($movetoforumid,$msgid);
					gf_updateLastPost($forum);

					// Update Topic and Post Count for the effected forums
					DB_query("UPDATE {$_TABLES['forum_forums']} SET topic_count=topic_count+1, post_count=post_count+$postCount WHERE forum_id=$movetoforumid");
					DB_query("UPDATE {$_TABLES['forum_forums']} SET topic_count=topic_count-1, post_count=post_count-$postCount WHERE forum_id=$forum");

					// Remove any lastviewed records in the log so that the new updated topic indicator will appear
					DB_query("DELETE FROM {$_TABLES['forum_log']} WHERE topic='$msgid'");
					
					$destUrl = $_CONF['site_url'] . "/forum/viewtopic.php?msg=8&amp;showtopic=$msgid";
				}
				
				COM_rdfUpToDateCheck('forum'); // forum rss feeds update
				// Remove new block and centerblock cached items
				$cacheInstance = 'forum__newpostsblock_';
				CACHE_remove_instance($cacheInstance);
				$cacheInstance = 'forum__centerblock_';
				CACHE_remove_instance($cacheInstance);
				COM_redirect($destUrl);
			}
		}

		if ($modfunction == 'deletepost' AND forum_modPermission($forum,$_USER['uid'],'mod_delete') AND $msgid != 0) {
			if ($msgidIsParent) { // Parent topic being deleted then
				$alertmessage = $LANG_GF02['msg65'];
			} else {
				$alertmessage = '';
			}
			$subject = DB_getItem($_TABLES['forum_topic'],"subject","id='$msgpid'");
			$alertmessage .= sprintf($LANG_GF02['msg64'],$msgid,$subject);
			
			$page = COM_newTemplate(CTL_plugin_templatePath('forum', 'moderator'));
			$page->set_file(array('page'=>'delete.thtml'));
			
			$page->set_var('fortopicid', $msgid);
			$page->parse('output', 'page');
			$promptform = $page->finish($page->get_var('output'));         
			
			$display .= alertMessage($alertmessage, $LANG_GF02['msg182'], $promptform);
		} elseif ($modfunction == 'editpost' AND forum_modPermission($forum,$_USER['uid'],'mod_edit') AND $msgid != 0) {
			COM_redirect("createtopic.php?method=edit&amp;id=$msgid");
		} elseif ($modfunction == 'lockedpost' AND forum_modPermission($forum,$_USER['uid'],'mod_edit') AND $msgid != 0) {
			COM_redirect("createtopic.php?method=postreply&amp;id=$msgid");
		} elseif ($modfunction == 'movetopic' AND forum_modPermission($forum,$_USER['uid'],'mod_move') AND $msgid != 0) {
			$SECgroups = SEC_getUserGroups();  // Returns an Associative Array - need to parse out the group id's
			$modgroups = '';
			foreach ($SECgroups as $key) {
			  if ($modgroups == '') {
				 $modgroups = $key;
			  } else {
				  $modgroups .= ",$key";
			  }
			}
			/* Check and see if user had moderation rights to another forum to complete the topic move */
			$sql = "SELECT DISTINCT b.forum_id, b.forum_name FROM {$_TABLES['forum_moderators']} a , {$_TABLES['forum_forums']} b ";
			$sql .= "WHERE a.mod_forum = b.forum_id AND ( a.mod_uid='{$_USER['uid']}' OR a.mod_groupid IN ($modgroups))";
			$query = DB_query($sql);

			if (DB_numRows($query) == 0) {
				COM_setSystemMessage($LANG_GF02['msg181'], $LANG_GF01['WARNING']);
				COM_redirect(html_entity_decode(forum_buildForumPostURL($msgpid, '', '' , false)));
			} else {
				$page = COM_newTemplate(CTL_plugin_templatePath('forum', 'moderator'));
				$page->set_file(array('page'=>'split_move.thtml'));
				
				$page->set_block('page', 'split_topic');
				
				$topictitle = DB_getItem($_TABLES['forum_topic'],"subject","id='$msgid'");
				$page->set_var('fortopicid', $msgid);
				// $page->set_var('forum', $forum);
				$page->set_var('topictitle', $topictitle);
				
				$page->set_var('forumoptions', f_forumjump('', '', true));

				/* Check and see request to move complete topic or split the topic */
				if (DB_getItem($_TABLES['forum_topic'],"pid","id='$msgid'") == 0) {
					$page->parse('split_topic', '');
					
					$page->parse('output', 'page');
					$promptform = $page->finish($page->get_var('output'));                
					
					$alertmessage = sprintf($LANG_GF03['movetopicmsg'],$topictitle);
					$display .= alertMessage($alertmessage, $LANG_GF02['msg182'], $promptform);
				} else {
					$poster   = DB_getItem($_TABLES['forum_topic'],"name","id='$msgid'");
					$postdate = COM_getUserDateTimeFormat(DB_getItem($_TABLES['forum_topic'],"date","id='$msgid'"));
					$page->parse('split_topic', 'split_topic');
					$page->parse('output', 'page');
					$promptform = $page->finish($page->get_var('output')); 
					
					$alertmessage = sprintf($LANG_GF03['splittopicmsg'],$topictitle,$poster,$postdate[0]);
					$display .= alertMessage($alertmessage,$LANG_GF02['msg182'],$promptform);
				}
			}

		} elseif ($modfunction == 'banip' AND forum_modPermission($forum,$_USER['uid'],'mod_ban') AND $msgid != 0 AND (function_exists('BAN_for_plugins_check_access') AND BAN_for_plugins_check_access())) {
			$iptobansql = DB_query("SELECT ip FROM {$_TABLES['forum_topic']} WHERE id='$msgid'");
			$forumpostipnum = DB_fetchArray($iptobansql);
			if ($forumpostipnum['ip'] == '') {
				COM_setSystemMessage($LANG_GF02['msg174'], $LANG_GF01['WARNING']);
				COM_redirect(html_entity_decode(forum_buildForumPostURL($msgpid, '', '' , false)));			
			}
			
			$ip_address = $forumpostipnum['ip'];
			
			if (BAN_for_plugins_ban_found($ip_address)) {
				BAN_for_plugins_ban_ip($ip_address, 'forum', false);
				COM_redirect(html_entity_decode(forum_buildForumPostURL($msgid, '&amp;msg=11', '', false)));
			} else {
				BAN_for_plugins_ban_ip($ip_address, 'forum');
				COM_redirect(html_entity_decode(forum_buildForumPostURL($msgid, '&amp;msg=10', '', false)));
			}
		} elseif ($modfunction == 'banippost' AND forum_modPermission($forum,$_USER['uid'],'mod_ban') AND $msgid != 0) {
			$iptobansql = DB_query("SELECT ip FROM {$_TABLES['forum_topic']} WHERE id='$msgid'");
			$forumpostipnum = DB_fetchArray($iptobansql);
			if ($forumpostipnum['ip'] == '') {
				COM_setSystemMessage($LANG_GF02['msg174'], $LANG_GF01['WARNING']);
				COM_redirect(html_entity_decode(forum_buildForumPostURL($msgpid, '', '' , false)));			
			}
			
			$ip_address = $forumpostipnum['ip'];
			
			if (DB_getItem($_TABLES['forum_banned_ip'], 'host_ip', "host_ip = '$ip_address'")) {
				$alertmessage = sprintf($LANG_GF02['msg68'], $ip_address);
			} else {
				
				if (!empty($_CONF['ip_lookup'])) {
					$iplookup = str_replace('*', $ip_address, $_CONF['ip_lookup']);
					$ip_address = COM_createLink($ip_address, $iplookup);
				}
				$alertmessage = sprintf($LANG_GF02['msg69'], $ip_address);
			}
			
			$page = COM_newTemplate(CTL_plugin_templatePath('forum', 'moderator'));
			$page->set_file(array('page'=>'ban.thtml'));

			$page->set_var('hostip', $forumpostipnum['ip']);
			// $page->set_var('forum', $forum);
			$page->set_var('fortopicid', $msgid);
	 
			$page->parse('output', 'page');
			$promptform = $page->finish($page->get_var('output'));     

			$display .= alertMessage($alertmessage, $LANG_GF02['msg182'], $promptform);

		} else {
			$display .= COM_showMessageText($LANG_GF02['msg71'], $LANG_GF01['WARNING']); // No function selected
		}
	} else {
		$display .= COM_showMessageText($LANG_GF02['msg72'], $LANG_GF01['ACCESSERROR']); // Warning, you do not have rights to perform this moderation function
	}
} else {
	// No mod privileges at all so 404 to hide details about moderation.php
	COM_handle404("{$_CONF['site_url']}/forum/index.php");
}

$display = gf_createHTMLDocument($display);

COM_output($display);
