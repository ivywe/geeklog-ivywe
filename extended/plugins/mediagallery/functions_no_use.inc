<?php
// +--------------------------------------------------------------------------+
// | Media Gallery Plugin - glFusion CMS                                      |
// +--------------------------------------------------------------------------+
// | functions_no_use.inc                                                     |
// |                                                                          |
// | This file does two things: 1) it implements the necessary glFusion Plugin|
// | API method and 2) implements all the common code needed by the Media     |
// | Gallery PHP files.                                                       |
// +--------------------------------------------------------------------------+
// | $Id:: functions.inc 5183 2009-11-30 21:59:49Z usableweb                 $|
// +--------------------------------------------------------------------------+
// | Copyright (C) 2002-2009 by the following authors:                        |
// |                                                                          |
// | Mark R. Evans          mark AT glfusion DOT org                          |
// +--------------------------------------------------------------------------+
// |                                                                          |
// | This program is free software; you can redistribute it and/or            |
// | modify it under the terms of the GNU General Public License              |
// | as published by the Free Software Foundation; either version 2           |
// | of the License, or (at your option) any later version.                   |
// |                                                                          |
// | This program is distributed in the hope that it will be useful,          |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
// | GNU General Public License for more details.                             |
// |                                                                          |
// | You should have received a copy of the GNU General Public License        |
// | along with this program; if not, write to the Free Software Foundation,  |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.          |
// |                                                                          |
// +--------------------------------------------------------------------------+

if (strpos(strtolower($_SERVER['PHP_SELF']), strtolower(basename(__FILE__))) !== false) {
    die('This file can not be used on its own!');
}

// 以下のコードはglFusion固有のAPIに対応したものであり、Geeklogでは無効である。

function plugin_editcomment_mediagallery($cid,$id) { // no use in Geeklog
    global $_CONF, $_MG_CONF, $_TABLES;

    echo COM_refresh ($_MG_CONF['site_url'] . "/media.php?s=$id");
    exit;
}

function plugin_getheadercss_mediagallery() {
    global $_CONF, $_MG_CONF, $mgLightBox, $glversion,$swfjsinclude,$themeStyle;

    $styles = array();

    if ( @file_exists($_CONF['path_layout'].'plugins/mediagallery/style.css') ) {
        $styleFile = $_CONF['path_layout'].'plugins/mediagallery/style.css';
    } else if ( $_MG_CONF['template_path'] == $_CONF['path'] . 'plugins/mediagallery/templates' ) {
	    $styleFile = $_MG_CONF['path_html'] . 'style.css';
	} else {
		$styleFile = $_CONF['path_layout'] . '/mediagallery/style.css';
	}
	$styles[] = $styleFile;

    // check for random skin css
    $cacheInstance = 'mgrs' . $_MG_CONF['random_skin'] . '__' . $_CONF['theme'];
    $retval = CACHE_check_instance($cacheInstance, 0);
    if ( $retval !== false) {
        $styles[] = CACHE_instance_filename($cacheInstance,0);
    } else {
        require_once $_CONF['path'] . 'plugins/mediagallery/include/classFrame.php';
        $nFrame = new mgFrame();
        $nFrame->constructor( $_MG_CONF['random_skin'] );
        $fCSS= $nFrame->getCSS();
        CACHE_create_instance($cacheInstance, $fCSS, 0);
        $styles[] = CACHE_instance_filename($cacheInstance,0);
    }

	return ($styles);
}

function plugin_getheaderjs_mediagallery() {
    global $_CONF, $_MG_CONF, $mgLightBox, $glversion,$swfjsinclude,$themeStyle;

    $js = array();

    // the order is important here.  swfobject must be 'sandwiched' between
    // swfupload.js and swupload.swfobject.js.
    $js[] = $_MG_CONF['path_html'] . 'swfupload/swfupload.js';
    $js[] = $_MG_CONF['path_html'] . 'js/swfobject.js';
    $js[] = $_MG_CONF['path_html'] . 'swfupload/swfupload.swfobject.js';
    $js[] = $_MG_CONF['path_html'] . 'js/mediagallery.js';
    if ( $_MG_CONF['disable_lightbox'] != true ) {
        $js[] = $_MG_CONF['path_html'] . 'js/slimbox.js';
    }
    $js[] = $_MG_CONF['path_html'] . 'js/slideshow.js';

	return ($js);
}

function plugin_getglobaljs_mediagallery() {
    global $_MG_CONF, $_CONF;

    $globalJS = array();
    $globalJS['mgBaseURL'] = $_MG_CONF['site_url'];

    return($globalJS);
}

/**
* this searches the plugins comments matching the user query and returns an array of
* for the header and table rows back to search.php where it will be formated and
* printed
*
* @query            string          Keywords user is looking for
* @datestart        date/time       Start date to get results for
* @dateend          date/time       End date to get results for
* @topic            string          The topic they were searching in
* @type             string          Type of items they are searching
* @author           string          Get all results by this author
*
*/
function plugin_dopluginsearch_comment_mediagallery($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
    global $MG_albums, $_TABLES, $_CONF, $_MG_CONF, $LANG_MG00, $LANG_MG01,$LANG09;

    if ( $_MG_CONF['disable_search_integration'] == 1) {
        return '';
    }
    if ( COM_isAnonUser() && $_MG_CONF['loginrequired'] == 1 )  {
        return '';
    }

    $query = trim(addslashes(htmlspecialchars($query)));

    $sql =  "SELECT c.cid AS id1, ma.media_id AS id, c.title AS title, c.comment AS description, UNIX_TIMESTAMP(c.date) AS date, c.uid AS uid, '0' AS hits, ";
    $sql .= "CONCAT('/mediagallery/media.php?s=',ma.media_id,'#comments') AS url ";
    $sql .= "FROM {$_TABLES['users']} AS u, {$_TABLES['comments']} AS c ";
    $sql .= "LEFT JOIN {$_TABLES['mg_media_albums']} AS ma ON c.sid=ma.media_id ";
    $sql .= "LEFT JOIN {$_TABLES['mg_albums']} AS a ON ma.album_id=a.album_id ";
    $sql .= "WHERE (c.type='mediagallery') AND (u.uid = c.uid) ";
    $sql .= COM_getPermSQL('AND') . ' ';

    if (!empty($author)) {
        $sql .= "AND (c.uid = '$author') ";
    }

    $search_c = new SearchCriteria('comments', 'Media Gallery > '.$LANG09[66]);
    $columns = array('comment','c.title');
    $sql .= $search_c->getDateRangeSQL('AND', 'UNIX_TIMESTAMP(c.date)', $datestart, $dateend);
    list($sql,$ftsql) = $search_c->buildSearchSQL($keyType, $query, $columns, $sql);
    $search_c->setSQL($sql);
    $search_c->setFTSQL($ftsql);
    $search_c->setRank(2);

    return $search_c;
}

function plugin_profileicondisplay_mediagallery( $uid ) {
    global $MG_albums,$_TABLES, $_MG_CONF, $_CONF, $_USER,$LANG_MG01;

    $retval = array();

    // hook a link for the user's member album...

    if ($_MG_CONF['member_albums'] == 1 && $uid > 1 ) {
        $sql = "SELECT album_id FROM {$_TABLES['mg_albums']} WHERE owner_id=" . intval($uid) . " AND album_parent='" . $_MG_CONF['member_album_root'] . "' LIMIT 1";

        $result = DB_query($sql);
        $numRows = DB_numRows($result);

        if ( $numRows > 0 ) {
            $A = DB_fetchArray($result);
            $album_id = $A['album_id'];
            $retval['url'] = $_MG_CONF['site_url'].'/album.php?aid='.$album_id;
            $retval['text'] = $LANG_MG01['album'];
            $retval['icon'] = $_CONF['site_url'].'/fckeditor/editor/plugins/mediagallery/images/mediagallery.gif';
        }
    }

    return $retval;
}

if ( $_MG_CONF['up_display_rows_enabled'] == 1 ||
     $_MG_CONF['up_display_columns_enabled'] == 1 ||
     $_MG_CONF['up_mp3_player_enabled'] == 1 ||
     $_MG_CONF['up_av_playback_enabled'] == 1 ||
     $_MG_CONF['up_thumbnail_size_enabled'] == 1) {
    $LANG_MYACCOUNT['pe_mgprefs'] = "MG Prefs";
}

function plugin_profileedit_mediagallery($uid,$panel,$fieldset)
{
    global $_CONF, $_MG_USERPREFS, $_MG_CONF, $_TABLES, $_USER, $LANG_MG01;

    if ( COM_isAnonUser() ) {
        return;
    }

    if ($panel != '' || $fieldset != '' ) {
        return;
    }

    if ( $_MG_CONF['up_display_rows_enabled'] == 0 &&
         $_MG_CONF['up_display_columns_enabled'] == 0 &&
         $_MG_CONF['up_mp3_player_enabled'] == 0 &&
         $_MG_CONF['up_av_playback_enabled'] == 0 &&
         $_MG_CONF['up_thumbnail_size_enabled'] == 0) {
        return;
    }

//    $_MG_USERPREFS = MG_getUserPrefs();

    $retval = '';
    $x = 0;

    // let's see if anything is actually set...
    if ( !isset($_MG_USERPREFS['mp3_player']) ) {
        $_MG_USERPREFS['mp3_player']    = -1;
        $_MG_USERPREFS['playback_mode'] = 1;
        $_MG_USERPREFS['tn_size']       = -1;
        $_MG_USERPREFS['display_rows']  = 0;
        $_MG_USERPREFS['display_columns'] = 0;
    }

    $T = MG_templateInstance( MG_getTemplatePath(0) );
    $T->set_file (array ('admin' => 'profile_userprefs.thtml'));
    $T->set_block('admin', 'prefRow', 'pRow');

    // build select boxes

    $mp3_select  = '<select name="mp3_player">';
    $mp3_select .= '<option value="-1"' . ($_MG_USERPREFS['mp3_player']== -1 ? ' selected="selected"' : '') . '>' . $LANG_MG01['system_default'] . '</option>';
    $mp3_select .= '<option value="0"'  . ($_MG_USERPREFS['mp3_player'] == 0 ? ' selected="selected"' : '') . '>' . $LANG_MG01['windows_media_player'] . '</option>';
    $mp3_select .= '<option value="1"'  . ($_MG_USERPREFS['mp3_player'] == 1 ? ' selected="selected"' : '') . '>' . $LANG_MG01['quicktime_player'] . '</option>';
    $mp3_select .= '<option value="2"'  . ($_MG_USERPREFS['mp3_player'] == 2 ? ' selected="selected"' : '') . '>' . $LANG_MG01['flashplayer'] . '</option>';
    $mp3_select .= '</select>';

    $playback_select  = '<select name="playback_mode">';
    $playback_select .= '<option value="-1"' . ($_MG_USERPREFS['playback_mode'] == 1 ? ' selected="selected"' : '') . '>' . $LANG_MG01['system_default'] . '</option>';
    $playback_select .= '<option value="0"' . ($_MG_USERPREFS['playback_mode'] == 0 ? ' selected="selected"' : '') . '>' . $LANG_MG01['play_in_popup'] . '</option>';
    $playback_select .= '<option value="2"' . ($_MG_USERPREFS['playback_mode'] == 2 ? ' selected="selected"' : '') . '>' . $LANG_MG01['play_inline'] . '</option>';
    $playback_select .= '<option value="3"' . ($_MG_USERPREFS['playback_mode'] == 3 ? ' selected="selected"' : '') . '>' . $LANG_MG01['use_mms'] . '</option>';
    $playback_select .= '</select>';

    $tn_select  = '<select name="tn_size">';
    $tn_select .= '<option value="-1"' . ($_MG_USERPREFS['tn_size'] == -1 ? ' selected="selected"' : '') . '>' . $LANG_MG01['system_default'] . '</option>';
    $tn_select .= '<option value="0"' . ($_MG_USERPREFS['tn_size'] == 0 ? ' selected="selected"' : '') . '>' . $LANG_MG01['small'] . '</option>';
    $tn_select .= '<option value="1"' . ($_MG_USERPREFS['tn_size'] == 1 ? ' selected="selected"' : '') . '>' . $LANG_MG01['medium'] . '</option>';
    $tn_select .= '<option value="2"' . ($_MG_USERPREFS['tn_size'] == 2 ? ' selected="selected"' : '') . '>' . $LANG_MG01['large'] . '</option>';
    $tn_select .= '</select>';

    $helpText = '<ul>';
    if ( $_MG_CONF['up_display_rows_enabled'] ) {
        $T->set_var(array(
            'lang_prompt'   => $LANG_MG01['display_rows_prompt'],
            'input_field'   => '<input type="text" size="3" name="display_rows" value="' . $_MG_USERPREFS['display_rows'] . '"' . XHTML . '>',
            'lang_help'     => $LANG_MG01['display_rows_help'],
            'rowcounter' => $x++ % 2,
        ));
        $T->parse('pRow', 'prefRow',true);
        $helpText .= '<li>'.$LANG_MG01['display_rows_help'].'</li>';
    }
    if ( $_MG_CONF['up_display_columns_enabled'] ) {
        $T->set_var(array(
            'lang_prompt'   => $LANG_MG01['display_columns_prompt'],
            'input_field'   => '<input type="text" size="3" name="display_columns" value="' . $_MG_USERPREFS['display_columns'] . '"' . XHTML . '>',
            'lang_help'     => $LANG_MG01['display_columns_help'],
        ));
        $T->parse('pRow', 'prefRow',true);
        $helpText .= '<li>'.$LANG_MG01['display_columns_help'].'</li>';
    }
    if ( $_MG_CONF['up_mp3_player_enabled'] ) {
        $T->set_var(array(
            'lang_prompt'   => $LANG_MG01['mp3_player'],
            'input_field'   => $mp3_select,
            'lang_help'     => $LANG_MG01['mp3_player_help'],
        ));
        $T->parse('pRow', 'prefRow',true);
        $helpText .= '<li>'.$LANG_MG01['mp3_player_help'].'</li>';
    }
    if ( $_MG_CONF['up_av_playback_enabled'] ) {
        $T->set_var(array(
            'lang_prompt'   => $LANG_MG01['av_play_options'],
            'input_field'   => $playback_select,
            'lang_help'     => $LANG_MG01['av_play_options_help'],
        ));
        $T->parse('pRow', 'prefRow',true);
        $helpText .= '<li>'.$LANG_MG01['av_play_options_help'].'</li>';
    }
    if ( $_MG_CONF['up_thumbnail_size_enabled'] ) {
        $T->set_var(array(
            'lang_prompt'   => $LANG_MG01['tn_size'],
            'input_field'   => $tn_select,
            'lang_help'     => $LANG_MG01['tn_size_help'],
        ));
        $T->parse('pRow', 'prefRow',true);
        $helpText .= '<li>'.$LANG_MG01['tn_size_help'].'</li>';
    }
    $helpText .= '</ul>';

    $T->set_var('lang_mgprefs_help_title',$LANG_MG01['user_prefs_title']);
    $T->set_var('lang_mgprefs_help',$helpText);
    $T->set_var('lang_mg_prefs',$LANG_MG01['user_prefs_title']);

    $T->parse('output', 'admin');
    $retval .= $T->finish($T->get_var('output'));

    return $retval;
}

function plugin_profilesave_mediagallery()
{
    global $_CONF, $_MG_CONF, $_TABLES, $_USER;

    if ( COM_isAnonUser() ) {
        return;
    }

    if ( $_MG_CONF['up_display_rows_enabled'] == 0 &&
         $_MG_CONF['up_display_columns_enabled'] == 0 &&
         $_MG_CONF['up_mp3_player_enabled'] == 0 &&
         $_MG_CONF['up_av_playback_enabled'] == 0 &&
         $_MG_CONF['up_thumbnail_size_enabled'] == 0) {
        return;
    }

    $display_rows    = intval(COM_applyFilter($_POST['display_rows'],true));
    $display_columns = intval(COM_applyFilter($_POST['display_columns'],true));
    $mp3_player      = isset($_POST['mp3_player']) ? intval(COM_applyFilter($_POST['mp3_player'],true)) : 0;
    $playback_mode   = intval(COM_applyFilter($_POST['playback_mode'],true));
    $tn_size         = intval(COM_applyFilter($_POST['tn_size'],true));
    $uid             = intval($_USER['uid']);

    if ( $display_columns < 0 || $display_columns > 5 ) {
        $display_columns = 3;
    }
    if ( $display_rows < 0 || $display_rows > 99 ) {
        $display_rows = 4;
    }

    if ( $_MG_CONF['up_display_rows_enabled'] == 0 ) {
        $display_rows = 0;
    }
    if ( $_MG_CONF['up_display_columns_enabled'] == 0 ) {
        $display_columns = 0;
    }
    if ( $_MG_CONF['up_mp3_player_enabled'] == 0 ) {
        $mp3_player = -1;
    }
    if ( $_MG_CONF['up_av_playback_enabled'] == 0 ) {
        $playback_mode = -1;
    }
    if ( $_MG_CONF['up_thumbnail_size_enabled'] == 0 ) {
        $tn_size = -1;
    }

    DB_save($_TABLES['mg_userprefs'],'uid,display_rows,display_columns,mp3_player,playback_mode,tn_size',"$uid,$display_rows,$display_columns,$mp3_player,$playback_mode,$tn_size");
}

function plugin_itemrated_mediagallery($media_id, $rating, $votes)
{
    global $_TABLES;

    $sql = "UPDATE {$_TABLES['mg_media']} SET media_votes = $votes, media_rating = '".addslashes($rating)."'
                    WHERE media_id='" . addslashes($media_id) . "'";
    DB_query($sql);
    return true;
}

function plugin_canuserrate_mediagallery($item_id, $uid)
{
    global $MG_albums, $_TABLES, $_CONF, $_MG_CONF;

    $retval = false;

    if ( COM_isAnonUser() && $_MG_CONF['loginrequired'] == 1 )  {
        return $retval;
    }

    $result = DB_query("SELECT * FROM {$_TABLES['mg_media']} AS m LEFT JOIN {$_TABLES['mg_media_albums']} AS ma ON m.media_id=ma.media_id WHERE m.media_id='".addslashes($item_id)."'");
    if ( DB_numRows($result) > 0 ) {
        $row = DB_fetchArray($result);
        if ($MG_albums[$row['album_id']]->enable_rating > 0 ) {
            if ($uid == $row['owner_id'] ) {
                return false;
            }
            if ( $MG_albums[$row['album_id']]->enable_rating == 1 && COM_isAnonUser() ) {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
    return true;
}
?>