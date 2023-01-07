<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog Forums Plugin 2.8.0                                               |
// +---------------------------------------------------------------------------+
// | print.php                                                                 |
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
require_once $CONF_FORUM['path_include'] . 'bbcode/stringparser_bbcode.class.php';

function gf_FormatForPrint( $str, $postmode='html' ) {
    global $CONF_FORUM;

    // Handle Pre ver 2.5 quoting and New Line Formatting - consider adding this to a migrate function
    if ($CONF_FORUM['pre2.5_mode'] && isset($showtopic['comment'])) {
        if ( stristr($showtopic['comment'],'[code') == false ) {
            $showtopic['comment'] = str_replace('<pre>','[code]',$showtopic['comment']);
            $showtopic['comment'] = str_replace('</pre>','[/code]',$showtopic['comment']);
        }
        $showtopic['comment'] = str_replace(array("<br />\r\n","<br />\n\r","<br />\r","<br />\n","<br>\r\n","<br>\n\r","<br>\r","<br>\n"), '<br' . XHTML . '>', $showtopic['comment'] );
        $showtopic['comment'] = preg_replace("/\[QUOTE\sBY=\s(.+?)\]/i","[QUOTE] Quote by $1:",$showtopic['comment']);
        /* Reformat code blocks - version 2.3.3 and prior */
        $showtopic['comment'] = str_replace( '<pre class="forumCode">', '[code]', $showtopic['comment'] );
        $showtopic['comment'] = preg_replace("/\[QUOTE\sBY=(.+?)\]/i","[QUOTE] Quote by $1:",$showtopic['comment']);
    }
    
    $str = gf_formatTextBlock($str,$postmode,'preview');
    
    $str = str_replace('{','&#123;',$str);
    $str = str_replace('}','&#125;',$str);

    // we don't have a stylesheet for printing, so replace our div with the style...
    $str = str_replace('<div class="quotemain">','<div style="border: 1px dotted #000;border-left: 4px solid #8394B2;color:#465584;  padding: 4px;  margin: 5px auto 8px auto;">',$str);
    return $str;
}

// Pass thru filter any get or post variables to only allow numeric values and remove any hostile data
$id = isset($_REQUEST['id']) ? COM_applyFilter($_REQUEST['id'],true) : '';

$display = '';

// Check is anonymous users can access and if not, regular user can access
// if ($CONF_FORUM['registration_required'] && $_USER['uid'] < 2) {
if ($CONF_FORUM['registration_required'] && !SEC_hasRights('forum.user')) {		
    $display .= COM_startBlock();
    $display .= COM_showMessageText($LANG_GF02['msg01'], $LANG_GF02['msg171']);
    $display .= COM_endBlock();
    $display = COM_createHTMLDocument($display);
    COM_output($display);
    exit;
}

if ($id == 0 OR DB_count($_TABLES['forum_topic'],"id","$id") == 0) {
    // Topic doesn't exist so exit gracefully
    COM_handle404('/forum/index.php');
    exit;
}

// Check if user can access topic
$forum = DB_getItem($_TABLES['forum_topic'],"forum","id=$id");
$query = DB_query("SELECT grp_name FROM {$_TABLES['groups']} groups, {$_TABLES['forum_forums']} forum WHERE forum.forum_id='$forum' AND forum.grp_id=groups.grp_id");
list ($groupname) = DB_fetchArray($query);
if (!SEC_inGroup($groupname)) {
	$display .= COM_showMessageText($LANG_GF02['msg02'], $LANG_GF02['msg171']);
    $display = COM_createHTMLDocument($display);
    COM_output($display);
    exit;
}

$result = DB_query("SELECT * FROM {$_TABLES['forum_topic']} WHERE (id='$id')");
$A = DB_fetchArray($result);

$username = COM_getDisplayName($A['uid']);
$A["name"] = htmlspecialchars($A["name"],ENT_QUOTES,$CONF_FORUM['charset']);

$A["subject"] = COM_checkWords($A["subject"]);
$A["subject"] = htmlspecialchars($A["subject"],ENT_QUOTES,$CONF_FORUM['charset']);

$A['comment'] = gf_FormatForPrint( $A['comment'], $A['postmode'] );
$A['comment'] = str_replace('<br />', '<br>', $A['comment'] );

$date = COM_strftime($CONF_FORUM['default_Datetime_format'], $A['date']);

$forumUrl = COM_buildUrl($_CONF['site_url'] . '/forum/viewtopic.php?showtopic=' . $A['id']);

$printableURL = COM_buildUrl($_CONF['site_url'] . '/forum/print.php?id=' .  $A['id']);

$forumTemplate = COM_newTemplate(CTL_plugin_templatePath('forum'));
$forumTemplate->set_file(array (
		'print_topic' => 'printable_topic.thtml'));                    

$forumTemplate->set_block('print_topic', 'block_reply_post');

$forumTemplate->set_var('lang_posted_on', $LANG_GF01['POSTEDON']);
$forumTemplate->set_var('lang_by', $LANG_GF01['BY']);
$forumTemplate->set_var('topic_date', $date);
$forumTemplate->set_var('topic_author', $username);
if ($A['uid'] == 1) {
	$forumTemplate->set_var('topic_nickname', $A['name']);
} else {
	$forumTemplate->set_var('topic_nickname', '');
}
$forumTemplate->set_var('topic_comment', $A['comment']);

$result2 = DB_query("SELECT * FROM {$_TABLES['forum_topic']} WHERE (pid='$id')");
while ($B = DB_fetchArray($result2)) {
    $date = COM_strftime($CONF_FORUM['default_Datetime_format'], $B['date']);
    $username = COM_getDisplayName($B["uid"]);
    $B['comment'] = gf_FormatForPrint( $B['comment'], $B['postmode'] );
    $B['comment'] = str_replace('<br />', '<br>', $B['comment'] );

	$forumTemplate->set_var('reply_subject', $B['subject']);
	$forumTemplate->set_var('reply_date', $date);
	$forumTemplate->set_var('reply_author', $username);
	if ($B['uid'] == 1) {
		$forumTemplate->set_var('reply_nickname', $B['name']);
	} else {
		$forumTemplate->set_var('reply_nickname', '');
	}
	$forumTemplate->set_var('reply_comment', $B['comment']);
	
	$forumTemplate->parse('reply_posts', 'block_reply_post', true);
}

$forumTemplate->parse('output', 'print_topic');
$display = $forumTemplate->finish($forumTemplate->get_var('output'));

$display = COM_createHTMLPrintableDocument(
	$display,
	array(
		'itemURL'   	=> $forumUrl,
		'printableURL'  => $printableURL,
		'itemtitle' 	=> sprintf($LANG_GF01['printed_subject'], $A["subject"]),
		'itembyline' 	=> "",
		'itemmodified' 	=> "",
		'itemextras' 	=> ""
	)
);		

COM_output($display);
