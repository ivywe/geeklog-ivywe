<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog Forums Plugin 2.8.0                                               |
// +---------------------------------------------------------------------------+
// | gf_showtopic.php                                                          |
// | Main functions to show - format topics in the forum                       |
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

// this file can't be used on its own
if (strpos(strtolower($_SERVER['PHP_SELF']), 'gf_showtopic.php') !== false) {
    die ('This file can not be used on its own.');
}

if (!function_exists( 'str_ireplace' ))
{
    require_once 'PHP/Compat.php';
    PHP_Compat::loadFunction( 'str_ireplace' );
}

require_once $_CONF['path'] . 'system/lib-user.php';

/**
* Creates an HTML string with the pictures that display a given rank
*/
function showrank($rank, $rankname)
{
    global $LANG_GF01, $CONF_FORUM;
    
    $userrank = COM_newTemplate(CTL_plugin_templatePath('forum'));
    $userrank->set_file (array('forum_icons' => 'forum_icons.thtml')); 
    
    $blocks = array('rankon_icon', 'rankoff_icon', 'rankmod_icon', 'rankadmin_icon');
    foreach ($blocks as $block) {
        $userrank->set_block('forum_icons', $block);
    }    
    
    $i = '';
    $retval = '';
    $userrank->set_var ('imgset', $CONF_FORUM['imgset']);
    $userrank->set_var ('ranktitle', $rankname);
    if ($rank <= 5) {
    	$userrank->set_var ('rankonicon', gf_getImage('rank_on','ranks'));
    	$userrank->set_var ('rankofficon', gf_getImage('rank_off','ranks'));
    	
    	$on = $userrank->parse ('output', 'rankon_icon');
    	$off = $userrank->parse ('output', 'rankoff_icon');
        for ($i=1; $i<=$rank; $i++) {
            $retval .= $on;
        }
        for ($i=$rank+1; $i<=5; $i++) {
            $retval .= $off;
        }
    } else {
        if ($rank == 6) {
        	$userrank->set_var ('rankmodicon', gf_getImage('rank_mod','ranks'));
        	$i = $userrank->parse ('output', 'rankmod_icon');
        } else if ($rank == 7) {
        	$userrank->set_var ('rankadminicon', gf_getImage('rank_admin','ranks'));
        	$i = $userrank->parse ('output', 'rankadmin_icon');
        }
        $retval = $i . $i . $i . $i. $i;
    }

    return $retval;
}

function showtopic($showtopic, $mode='', $postcount=1, $onetwo=1, $page=1, $query = '')
{
    global $CONF_FORUM, $_CONF, $_TABLES, $_USER, $LANG_GF01, $LANG_GF02, $LANG_GF09, $LANG28, $LANG31;
    global $oldPost;

    $oldPost = 0;

    //$mytimer = new timerobject();
    //$mytimer->setPercision(2);
    //$mytimer->startTimer();
    //$intervalTime = $mytimer->stopTimer();
    //COM_errorLog("Show Topic Display Time1: $intervalTime");

    if (!class_exists('StringParser') ) {
        require_once $CONF_FORUM['path_include'] . 'bbcode/stringparser_bbcode.class.php';
    }

    $topictemplate = COM_newTemplate(CTL_plugin_templatePath('forum'));
    $topictemplate->set_file (array (
            'topictemplate' =>  'topic.thtml',
            'forum_icons'   => 'forum_icons.thtml', 
            'forum_links'   => 'forum_links.thtml')); 
    		
    $topictemplate->set_block('topictemplate', 'block_user_information');
    $topictemplate->set_block('topictemplate', 'block_anon_user_information');
    $topictemplate->set_block('topictemplate', 'location');
    $topictemplate->set_block('topictemplate', 'ip_address');
    $topictemplate->set_block('topictemplate', 'anon_ip_address');
	$topictemplate->set_block('topictemplate', 'banned_ip_address');
    $topictemplate->set_block('topictemplate', 'post_signature');
    $topictemplate->set_block('topictemplate', 'mod_functions');
    
    $blocks = array('block_user_name', 'block_anon_user_name', 'block_user_information', 'block_anon_user_information', 'post_signature', 'mod_functions');
    foreach ($blocks as $block) {
        $topictemplate->set_block('topictemplate', $block);
    }    
    
    $topictemplate->set_block('forum_icons', 'topiclocked_icon');
    $topictemplate->set_block('forum_icons', 'mood_icon');
    
    $blocks = array('profile_link', 'pm_link', 'email_link', 'website_link', 'quotetopic_link', 'edittopic_link');
    foreach ($blocks as $block) {
        $topictemplate->set_block('forum_links', $block);
    }    
    
    // if preview, only stripslashes is gpc=on, else assume from db so strip        
    if ( $mode == 'preview' ) {
        $showtopic['subject'] = COM_stripslashes($showtopic['subject']);
    } else {
        $showtopic['subject'] = stripslashes($showtopic['subject']);
    }

    $min_height = 50;     // Base minimum  height of topic - will increase if avatar or sig is used
    $date = COM_strftime( $CONF_FORUM['default_Topic_Datetime_format'], $showtopic['date'] );

    if (COM_versionCompare(VERSION, '2.2.2', '>=')) {
        $sql = "SELECT u.*, ui.location FROM {$_TABLES['users']} u, {$_TABLES['user_attributes']} ui 
                WHERE u.uid = ui.uid 
                AND u.uid = '{$showtopic['uid']}'";
    } else {
        $sql = "SELECT u.*, ui.location FROM {$_TABLES['users']} u, {$_TABLES['userinfo']} ui 
                WHERE u.uid = ui.uid 
                AND u.uid = '{$showtopic['uid']}'";
    }

    $userQuery = DB_query($sql);
    $isUserBanned = false;
    if ($showtopic['uid'] > 1 AND DB_numRows($userQuery) == 1) {
        $isUserBanned = USER_isBanned($showtopic['uid']);
        $userarray = DB_fetchArray($userQuery);
        $username = COM_getDisplayName($showtopic['uid']);
        $userlink = COM_getProfileLink($showtopic['uid'], $username);
        
        $uservalid = true;
        $postcountQuery = DB_query("SELECT * FROM {$_TABLES['forum_topic']} WHERE uid='{$showtopic['uid']}'");
        $posts = DB_numRows($postcountQuery);
        // STARS CODE
        if (SEC_inGroup('Root', $showtopic['uid'])) {
            $user_level = showrank(7, $LANG_GF01['admin']);
            $user_levelname=$LANG_GF01['admin'];
        } elseif (forum_modPermission($showtopic['forum'], $showtopic['uid'])) {
            $user_level = showrank(6, $LANG_GF01['moderator']);
            $user_levelname = $LANG_GF01['moderator'];
        } else {
            if ($posts < $CONF_FORUM['level2']) {
                $user_level = showrank(1, $CONF_FORUM['level1name']);
                $user_levelname = $CONF_FORUM['level1name'];
            } elseif (($posts >= $CONF_FORUM['level2']) && ($posts < $CONF_FORUM['level3'])){
                $user_level = showrank(2, $CONF_FORUM['level2name']);
                $user_levelname = $CONF_FORUM['level2name'];
            } elseif (($posts >= $CONF_FORUM['level3']) && ($posts < $CONF_FORUM['level4'])){
                $user_level = showrank(3, $CONF_FORUM['level3name']);
                $user_levelname = $CONF_FORUM['level3name'];
            } elseif (($posts >= $CONF_FORUM['level4']) && ($posts < $CONF_FORUM['level5'])){
                $user_level = showrank(4, $CONF_FORUM['level4name']);
                $user_levelname = $CONF_FORUM['level4name'];
            } elseif (($posts > $CONF_FORUM['level5'])){
                $user_level = showrank(5, $CONF_FORUM['level5name']);
                $user_levelname = $CONF_FORUM['level5name'];
            } else {
                $user_level = '';
                $user_levelname = '';
            }
        }

        $regdate = $LANG_GF01['REGISTERED']. ': ' . COM_strftime($_CONF['shortdate'],strtotime($userarray['regdate']));
        $numposts = $LANG_GF01['POSTS']. ': ' .$posts;
        if ($isUserBanned) {
            $user_status = $LANG_GF01['STATUS']. ' ' . $LANG28[42];
        } else {
            $table = COM_versionCompare(VERSION, '2.2.2', '>=') ? $_TABLES['user_attributes'] : $_TABLES['userprefs'];

            if (DB_count( $_TABLES['sessions'], 'uid', $showtopic['uid']) > 0 AND DB_getItem($table, 'showonline', "uid={$showtopic['uid']}") == 1) {
                $user_status = $LANG_GF01['STATUS']. ' ' .$LANG_GF01['ONLINE'];
            } else {
                $user_status = $LANG_GF01['STATUS']. ' ' .$LANG_GF01['OFFLINE'];
            }
        }

        if ($userarray['sig'] != '') {
            $sig = $userarray['sig'];
            $min_height = $min_height + 30;
        } else {
			$sig = '';
		}
    } else {
        $uservalid = false;
        $userlink = urldecode($showtopic['name']);
    }

    // As of Geeklog 2.2.1 it now returns a photo for anonymous user if configured
	if ($showtopic['uid'] > 1 && !isset($userarray)) {
		// Note: Had to check if userarray doesn't exist for an actual user of a forum post as it looks like older forums posts may exist that point to a user that no longer exists (must be a bug in an older version of the forum)
		// Force user to be anonymous
		// There is an update routine to fix this in forum version 2.9.5
		$avatar = USER_getPhoto(1, '', '', $CONF_FORUM['avatar_width'], '', $showtopic['name']);
	} elseif ($showtopic['uid'] == 1) {
		$avatar = USER_getPhoto($showtopic['uid'], '', '', $CONF_FORUM['avatar_width'], '', $showtopic['name']);
	} else {
		$avatar = USER_getPhoto($showtopic['uid'], $userarray['photo'], '', $CONF_FORUM['avatar_width']);
	}
    if (!empty($avatar)) {
        $min_height = $min_height + 50;
    }

    if ($CONF_FORUM['show_moods'] &&  $showtopic['mood'] != "") {
		$topictemplate->set_var ('moodicon', gf_getImage($showtopic['mood'],'moods'));
		$topictemplate->set_var ('moodicontitle', $showtopic['mood']);
        $topictemplate->set_var ('moodtitle', $showtopic['mood']);
		$topictemplate->parse ('mood_icon', 'mood_icon');
        
		$min_height = $min_height + 30;
    } else {
		$topictemplate->set_var ('mood_icon', '');
	}


    //$intervalTime = $mytimer->stopTimer();
    //COM_errorLog("Show Topic Display Time3: $intervalTime");

    // Handle Pre ver 2.5 quoting and New Line Formatting - consider adding this to a migrate function
    if ($CONF_FORUM['pre2.5_mode']) {
        // try to determine if we have an old post...
        if (strstr($showtopic['comment'],'<pre class="forumCode">') !== false)  $oldPost = 1;
        if (strstr($showtopic['comment'],"[code]<code>") !== false) $oldPost = 1;
        if (strstr($showtopic['comment'],"<pre>") !== false ) $oldPost = 1;

        if ( stristr($showtopic['comment'],'[code') == false || stristr($showtopic['comment'],'[code]<code>') == true) {
            if (strstr($showtopic['comment'],"<pre>") !== false)  $oldPost = 1;
            $showtopic['comment'] = str_replace('<pre>','[code]',$showtopic['comment']);
            $showtopic['comment'] = str_replace('</pre>','[/code]',$showtopic['comment']);
        }
        $showtopic['comment'] = str_ireplace("[code]<code>",'[code]',$showtopic['comment']);
        $showtopic['comment'] = str_ireplace("</code>[/code]",'[/code]',$showtopic['comment']);
        $showtopic['comment'] = str_replace(array("<br />\r\n","<br />\n\r","<br />\r","<br />\n","<br>\r\n","<br>\n\r","<br>\r","<br>\n"), '<br' . XHTML . '>', $showtopic['comment'] );
        $showtopic['comment'] = preg_replace("/\[QUOTE\sBY=\s(.+?)\]/i","[QUOTE] Quote by $1:",$showtopic['comment']);
        /* Reformat code blocks - version 2.3.3 and prior */
        $showtopic['comment'] = str_replace( '<pre class="forumCode">', '[code]', $showtopic['comment'] );
        $showtopic['comment'] = preg_replace("/\[QUOTE\sBY=(.+?)\]/i","[QUOTE] Quote by $1:",$showtopic['comment']);

        if ( $oldPost ) {
            if ( strstr($showtopic['comment'],"\\'") !== false ) {
                $showtopic['comment'] = stripslashes($showtopic['comment']);
            }
        }
    }

    $showtopic['comment'] = gf_formatTextBlock($showtopic['comment'],$showtopic['postmode'],$mode);
    $showtopic['subject'] = gf_formatTextBlock($showtopic['subject'],'text',$mode);

    if (($CONF_FORUM['show_subject_length'] > 0) AND (strlen ($showtopic['subject']) > $CONF_FORUM['show_subject_length'])) {
        $showtopic['subject'] = COM_truncate("$showtopic[subject]", $CONF_FORUM['show_subject_length'], '...');
    }

    //$intervalTime = $mytimer->stopTimer();
    //COM_errorLog("Show Topic Display Time2: $intervalTime");
	
	$is_readonly = DB_getItem($_TABLES['forum_forums'],'is_readonly','forum_id=' . $showtopic['forum']);
		
	if (!isset($showtopic['pid'])) {
		$showtopic['pid'] = 0;
	}
	
    if ($showtopic['pid'] == 0) {
        $replytopicid = $showtopic['id'];
        $is_lockedtopic = $showtopic['locked'];
        $views = $showtopic['views'];
        $topictemplate->set_var ('read_msg', sprintf($LANG_GF02['msg49'], COM_numberFormat($views)) );
		
        if ($is_lockedtopic || $is_readonly) {
            $topictemplate->parse('topiclocked_icon', 'topiclocked_icon'); 
        }
    } else {
        $replytopicid = $showtopic['pid'];
        $is_lockedtopic = DB_getItem($_TABLES['forum_topic'],'locked', "id={$showtopic['pid']}");
        $topictemplate->set_var ('read_msg','');
    }

    if ($mode != 'preview' && $uservalid && !COM_isAnonUser() && ($_USER['uid'] == $showtopic['uid']) && !$isUserBanned) {
        /* Check if user can still edit this post - within allowed edit timeframe */
        $editAllowed = false;

		// Edit window must exist and topic cannot be locked
        if ($CONF_FORUM['allowed_editwindow'] > 0 && !$is_lockedtopic && !$is_readonly) {
            $t1 = $showtopic['date'];
            $t2 = $CONF_FORUM['allowed_editwindow'];
            if ((time() - $t2) < $t1) {
                $editAllowed = true;
            }
        } elseif ($CONF_FORUM['allowed_editwindow'] == -1) {
            $editAllowed = true;
        }
        if ($editAllowed) {
			//$editlink = "{$_CONF['site_url']}/forum/createtopic.php?method=edit&amp;forum={$showtopic['forum']}&amp;id={$showtopic['id']}&amp;editid={$showtopic['id']}&amp;page=$page";
            $editlink = "{$_CONF['site_url']}/forum/createtopic.php?method=edit&amp;id={$showtopic['id']}";
            $editlinktext = $LANG_GF09['edit'];
            $topictemplate->set_var ('editlink', $editlink);
            $topictemplate->set_var ('editlinktext', $editlinktext);
            $topictemplate->set_var ('LANG_edit', $LANG_GF01['EDITICON']);
            $topictemplate->parse ('edittopic_link', 'edittopic_link');
        }
    }

    if ($query != '') {
		$showtopic['subject'] = COM_highlightQuery($showtopic['subject'], $query);
		$showtopic['comment'] = COM_highlightQuery($showtopic['comment'], $query);
    }
	
    if ($CONF_FORUM['allow_user_dateformat']) {
        $date = COM_getUserDateTimeFormat($showtopic['date']);
        $topictemplate->set_var ('posted_date', $date[0]);
    } else {
        $date = COM_strftime( $CONF_FORUM['default_Topic_Datetime_format'], $showtopic['date'] );
        $topictemplate->set_var ('posted_date', $date);
    }

    if ($mode != 'preview') {
        if ($is_lockedtopic == 0) {
            if ($is_readonly == 0 OR forum_modPermission($showtopic['forum'],$_USER['uid'],'mod_edit')) {
                //$quotelink = "{$_CONF['site_url']}/forum/createtopic.php?method=postreply&amp;forum={$showtopic['forum']}&amp;id=$replytopicid&amp;quoteid={$showtopic['id']}";
				$quotelink = "{$_CONF['site_url']}/forum/createtopic.php?method=postreply&amp;id=$replytopicid&amp;quoteid={$showtopic['id']}";
                $quotelinktext = $LANG_GF09['quote'];
                $topictemplate->set_var ('quotelink', $quotelink);
                $topictemplate->set_var ('quotelinktext', $quotelinktext);
                $topictemplate->set_var ('LANG_quote', $LANG_GF01['QUOTEICON']);
                $topictemplate->parse ('quotetopic_link', 'quotetopic_link');
            }
        }

        $topictemplate->set_var ('topic_post_id', $showtopic['id']);

        if ($showtopic['uid'] > 1 && $uservalid && !$isUserBanned) {
            $profile_link = "{$_CONF['site_url']}/users.php?mode=profile&amp;uid={$showtopic['uid']}";
            $profile_linktext = $LANG_GF09['profile'];
            $topictemplate->set_var ('profilelink', $profile_link);
            $topictemplate->set_var ('profilelinktext', $profile_linktext);
            $topictemplate->set_var ('LANG_profile',$LANG_GF01['ProfileLink']);
            $topictemplate->parse ('profile_link', 'profile_link');
            if ($CONF_FORUM['use_pm_plugin']) {
                $pmusernmame = COM_getDisplayName($showtopic['uid']);
                $pmplugin_link = forumPLG_getPMlink($pmusernmame);
                if ($pmplugin_link != '') {
                    $pm_link = $pmplugin_link;
                    $pm_linktext = $LANG_GF09['pm'];
                    $topictemplate->set_var ('pmlink', $pm_link);
                    $topictemplate->set_var ('pmlinktext', $pm_linktext);
                    $topictemplate->set_var ('LANG_pm', $LANG_GF01['PMLink']);
                    $topictemplate->parse ('pm_link', 'pm_link');
                }
            }

            if (isset($userarray['email']) && $userarray['email'] != '' && $showtopic["uid"] > 1) {
                $email_link = "{$_CONF['site_url']}/profiles.php?uid={$showtopic['uid']}";
                $email_linktext = $LANG_GF09['email'];
                $topictemplate->set_var ('emaillink', $email_link);
                $topictemplate->set_var ('emaillinktext', $email_linktext);
                $topictemplate->set_var ('LANG_email', $LANG_GF01['EmailLink']);
                $topictemplate->parse ('email_link', 'email_link');
            }
            if (isset($userarray['homepage']) && $userarray['homepage'] != '') {
                $homepage = trim($userarray['homepage']);
                if (strtolower(substr($homepage, 0, 4)) != 'http') {
                    $homepage = 'http://' .$homepage;
                }
                $homepagetext = $LANG_GF09['website'];
                $topictemplate->set_var ('websitelink', $homepage);
                $topictemplate->set_var ('websitelinktext', $homepagetext);
                $topictemplate->set_var ('LANG_website', $LANG_GF01['WebsiteLink']);
                $topictemplate->parse ('website_link', 'website_link');
            }
            if (isset($userarray['location']) && $userarray['location'] != '' && $showtopic["uid"] > 1) {
                $topictemplate->set_var ('user_location', $userarray['location']);
                $topictemplate->parse ('location', 'location');
            }
        }

    } else {
        if (isset($_GET['onlytopic']) AND $_GET['onlytopic'] != 1) {
            $topictemplate->set_var ('posted_date', '');
            $topictemplate->set_var ('preview_topic_subject', $showtopic['subject']);
        } else {
			$topictemplate->set_var ('topic_post_id', $showtopic['id']);
            $topictemplate->set_var ('preview_topic_subject', '');
        }
        $topictemplate->set_var ('read_msg', '');
        $topictemplate->set_var ('topiclocked_icon', '');

        $topictemplate->set_var ('preview_mode', 'none');
    }

    //$intervalTime = $mytimer->stopTimer();
    //COM_errorLog("Show Topic Display Time4: $intervalTime");

    $showtopic['comment'] = str_replace('{','&#123;',$showtopic['comment']);
    $showtopic['comment'] = str_replace('}','&#125;',$showtopic['comment']);

    // Temporary correspondence. You should cope in more roots.
    $showtopic['comment'] = str_replace(array("<br />","<br>"), '<br' . XHTML . '>', $showtopic['comment'] );
	
    $topictemplate->set_var ('layout_url', $CONF_FORUM['layout_url']);
    $topictemplate->set_var ('csscode', $onetwo);
    $topictemplate->set_var ('postmode', $showtopic['postmode']);
    $topictemplate->set_var ('userlink', $userlink);
    $topictemplate->set_var ('lang_forum', $LANG_GF01['FORUM']);
    if (isset($user_levelname)) {
        $topictemplate->set_var ('user_levelname', $user_levelname);
    }
    if (isset($user_level)) {
        $topictemplate->set_var ('user_level', $user_level);
    }
    if (isset($avatar)) {
        $topictemplate->set_var ('avatar', $avatar);
    }
    if (isset($user_status)) {
        $topictemplate->set_var ('user_status', $user_status);
    }
    if (isset($regdate)) {
        $topictemplate->set_var ('regdate', $regdate);
    }
    if (isset($numposts)) {
        $topictemplate->set_var ('numposts', $numposts);
    }
    if (forum_modPermission($showtopic['forum'],'','mod_ban')) {
		if (isset($showtopic['ip'])) {
			$topictemplate->set_var ('ip', $showtopic['ip']);
			
			if (DB_getItem($_TABLES['forum_banned_ip'], 'host_ip', "host_ip = '{$showtopic['ip']}'")) {
				$topictemplate->set_var ('banned_ip', $showtopic['ip']);
				$topictemplate->parse ('ip', 'banned_ip_address');
			} else {
				$topictemplate->set_var ('ip', $showtopic['ip']);
			}
			
			if ($showtopic['uid'] == 1) {
				$topictemplate->parse ('ip_address', 'anon_ip_address');
			} else {
				$topictemplate->parse ('ip_address', 'ip_address');
			}
		} else {
			$topictemplate->set_var ('ip', '');
		}
	} else {
		$topictemplate->set_var ('ip_address', '');
	}
    $topictemplate->set_var ('imgset', $CONF_FORUM['imgset']);
    $topictemplate->set_var ('topic_subject', $showtopic['subject']);
    $topictemplate->set_var ('LANG_ON2', $LANG_GF01['ON2']);
    
    if ($mode != 'preview') {
    	$mod_functions = forum_getmodFunctions($showtopic);
		if (!empty($mod_functions)) {
			$topictemplate->set_var ('mod_functions', $mod_functions);
			$topictemplate->parse ('mod_functions', 'mod_functions');
		} else {
			$topictemplate->set_var ('mod_functions', '');
		}
	}
	
    $topictemplate->set_var ('topic_comment', $showtopic['comment']);
    $topictemplate->set_var ('comment_minheight', "min-height:{$min_height}px");
    if ($showtopic['uid'] > 1 && isset($userarray['sig']) && !empty(trim($userarray['sig']))) {
		$topictemplate->set_var('signature_divider_html', $LANG31['sig_divider_html']);

		// Converts to HTML, fixes links, and executes autotags
		$sig_html = GLText::getDisplayText(stripslashes($userarray['sig']), $userarray['postmode'], GLTEXT_LATEST_VERSION);
		
		$topictemplate->set_var('user_signature', $sig_html);
        $topictemplate->set_var ('show_sig', ''); // For Backwards compatible and used for display class
		$topictemplate->parse ('post_signature', 'post_signature');
    } else {
        $topictemplate->set_var ('user_signature', '');
        $topictemplate->set_var ('show_sig', 'none'); // For Backwards compatible and used for display class
        $topictemplate->set_var ('post_signature', '');
    }
    $topictemplate->set_var ('forumid', $showtopic['forum']);
    $topictemplate->set_var ('topic_id', $showtopic['id']);
    $topictemplate->set_var ('member_badge', forumPLG_getMemberBadge($showtopic['uid']));
    if ($uservalid) {
    	$topictemplate->parse ('user_name', 'block_user_name');
    	$topictemplate->parse ('user_information', 'block_user_information');
	} else {
		$topictemplate->parse ('user_name', 'block_anon_user_name');
		$topictemplate->parse ('user_information', 'block_anon_user_information');
	}

    if ($mode != 'preview') { // Still allow for locked topics and read only forums
        if ($_CONF['likes_enabled'] != 0 && $CONF_FORUM['likes_forum'] != 0) {
            $topictemplate->set_var('likes_control',LIKES_control(FORUM_PLUGIN_NAME, LIKES_TYPE_FORUM_POST, $showtopic['id'], $CONF_FORUM['likes_forum']));
        } else {
            $topictemplate->set_var('likes_control', '');
        }
    }

    if ($mode != 'preview' && ($postcount % $CONF_FORUM['blocks_showtopic_repeat_after_num_posts'] == 0)) { // Checks remainder if 1 then do not display
        PLG_templateSetVars('forum_showtopic', $topictemplate);
    }
    
    $topictemplate->parse ('output', 'topictemplate');
    $retval = $topictemplate->finish ($topictemplate->get_var('output'));

    //$intervalTime = $mytimer->stopTimer();
    //COM_errorLog("Show Topic Display Time5: $intervalTime");

    return $retval;
}

function forum_getmodFunctions($showtopic) {
    global $_USER,$_CONF,$_TABLES,$LANG_GF03,$LANG_GF01,$page;

    $retval = '';
    $options = '';
	
	if (COM_isAnonUser()) {
		return $retval;
	}
	
    if (forum_modPermission($showtopic['forum'],$_USER['uid'],'mod_edit')) {
        $options .= '<option value="editpost">' .$LANG_GF03['edit']. '</option>';
        if ($showtopic['locked'] == 1) {
            $options .= '<option value="lockedpost">' .$LANG_GF03['lockedpost']. '</option>';
        }
    }
    if (forum_modPermission($showtopic['forum'],$_USER['uid'],'mod_delete')) {
        $options .= '<option value="deletepost">' .$LANG_GF03['delete']. '</option>';
    }
    if (forum_modPermission($showtopic['forum'],$_USER['uid'],'mod_ban')) {
		if (DB_getItem($_TABLES['forum_banned_ip'], 'host_ip', "host_ip = '{$showtopic['ip']}'")) {
			$options .= '<option value="banippost">' .$LANG_GF03['banippostremove']. '</option>';	
		} else {
			$options .= '<option value="banippost">' .$LANG_GF03['banippost']. '</option>';
		}
    }
    if (forum_modPermission($showtopic['forum'],$_USER['uid'],'mod_ban')) {
    	if (function_exists('BAN_for_plugins_check_access') AND BAN_for_plugins_check_access()) {
			$iptobansql = DB_query("SELECT ip FROM {$_TABLES['forum_topic']} WHERE id='{$showtopic['id']}'");
			$forumpostipnum = DB_fetchArray($iptobansql);
			$ip_address = $forumpostipnum['ip'];
			if ($ip_address != '') {
				if (BAN_for_plugins_ban_found($ip_address)) {
					$options .= '<option value="banip">' .$LANG_GF03['banipremove']. '</option>';
				} else {
					$options .= '<option value="banip">' .$LANG_GF03['banip']. '</option>';
				}
			}
		}
    }    
    if ($showtopic['pid'] == 0) {
        if (forum_modPermission($showtopic['forum'],$_USER['uid'],'mod_move')) {
            $options .= '<option value="movetopic">' .$LANG_GF03['move']. '</option>';
        }
    } elseif (forum_modPermission($showtopic['forum'],$_USER['uid'],'mod_move')) {
        $options .= '<option value="movetopic">' .$LANG_GF03['split']. '</option>';
    }

    if ($options != '') {
        $retval .= '<select name="modfunction">';
        $retval .= $options;

        if ($showtopic['pid'] == 0) {
            $msgpid = $showtopic['id'];
            $top = "yes";
        } else {
            $msgpid = $showtopic['pid'];
            $top = "no";
        }
        $retval .= '</select>&nbsp;&nbsp;';
        $retval .= '<input type="submit" name="submit" value="' .$LANG_GF01['GO'].'!"' . XHTML . '>';
        $retval .= '<input type="hidden" name="msgid" value="' .$showtopic['id']. '"' . XHTML . '>';
        // $retval .= '<input type="hidden" name="forum" value="' .$showtopic['forum']. '"' . XHTML . '>';
        // $retval .= '<input type="hidden" name="msgpid" value="' .$msgpid. '"' . XHTML . '>';
        // $retval .= '<input type="hidden" name="top" value="' .$top. '"' . XHTML . '>';
    }
    return $retval;
}


function gf_chkpostmode($postmode,$postmode_switch) {
    global $_TABLES,$CONF_FORUM;

    if ($postmode == "") {
    	if ($CONF_FORUM['allow_html'] AND $CONF_FORUM['post_htmlmode']) { // Check if html allowed and if it is the default selection
            $postmode = 'html';
        } else {
            $postmode = 'text';
        }
    } else {
        if ($postmode_switch) {
            if ($postmode == 'html') {
                $postmode = 'text';
            } else {
                $postmode = 'html';
            }
            $postmode_switch = 0;
        } else {
        	// before returning lets just make sure postmode is set properly to 1 of 2 options
        	if ($postmode != 'html' AND $postmode != 'text') {
				if ($CONF_FORUM['allow_html'] AND $CONF_FORUM['post_htmlmode']) { // Check if html allowed and if it is_a the default selection
					$postmode = 'html';
				} else {
					$postmode = 'text';
				}
			}
		}
    }
    
    return $postmode;
}

?>