<?php
// +--------------------------------------------------------------------------+
// | Media Gallery Plugin - glFusion CMS                                      |
// +--------------------------------------------------------------------------+
// | functions.inc                                                            |
// |                                                                          |
// | This file does two things: 1) it implements the necessary glFusion Plugin|
// | API method and 2) implements all the common code needed by the Media     |
// | Gallery PHP files.                                                       |
// +--------------------------------------------------------------------------+
// | $Id:: functions.inc 5953 2010-04-17 14:51:22Z mevans0263                $|
// +--------------------------------------------------------------------------+
// | Copyright (C) 2002-2010 by the following authors:                        |
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

/**
* Language file Include
*/
$langfile = $_CONF['path'] . 'plugins/mediagallery/language/' . $_CONF['language'] . '.php';

if (file_exists ($langfile)) {
    include_once $langfile;
} else {
    include_once $_CONF['path'] . 'plugins/mediagallery/language/english.php';
}

/*
* Include MediaGallery config file
*/
//require_once $_CONF['path'].'plugins/mediagallery/mediagallery.php';
require_once $_CONF['path'].'plugins/mediagallery/config.php';

define("MG_JPG",1);
define("MG_PNG",2);
define("MG_TIF",4);
define("MG_GIF",8);
define("MG_BMP",16);
define("MG_TGA",32);
define("MG_PSD",64);
define("MG_MP3",128);
define("MG_OGG",256);
define("MG_ASF",512);
define("MG_SWF",1024);
define("MG_MOV",2048);
define("MG_MP4",4096);
define("MG_MPG",8192);
define("MG_ZIP",16384);
define("MG_OTHER",32768);
define("MG_PDF",65536);
define("MG_FLV",131072);
define("MG_RFLV",262144);
define("MG_EMB",524288);

// This is a list of valid extensions for image media thumbnails / display images
$_MG_CONF['validExtensions'] = array('.jpg','.png','.gif','.bmp');

// placeholder until implemented
$_MG_CONF['ad_shopping_cart'] = 0;

$swfjsinclude = 0;
$mgLightBox = 0;
$themeStyle = '';

$_MG_CONF['random_skin'] = 'default';

$_MG_CONF['tmp_path'] = $_CONF['path'] . 'plugins/mediagallery/tmp/';

// Read config data
$result = DB_query("SELECT * FROM " . $_TABLES['mg_config'], 1);
if (!empty($result)) {
    while ($row = DB_fetchArray($result)) {
        $_MG_CONF[$row['config_name']] = $row['config_value'];
    }
}

if ($_CONF['loginrequired'] == 1) {
    $_MG_CONF['loginrequired'] = 1;
}

$_MG_CONF['up_mp3_player_enabled'] = 0;

$_MG_CONF['installed_version'] = DB_getItem($_TABLES['plugins'], 'pi_version', "pi_name = 'mediagallery'");

if ($_MG_CONF['installed_version']) {
    $_MG_CONF['dateformat'] = DB_getItem($_TABLES['dateformats'], 'format', "dfid = {$_MG_CONF['dfid']}");
    // safety checks....
    if ($_MG_CONF['album_display_columns'] < 1) {
        $_MG_CONF['album_display_columns'] = 1;
    }
    if ($_MG_CONF['album_display_rows'] < 1) {
        $_MG_CONF['album_display_rows'] = 9;
    }
    if ($_MG_CONF['js_lib'] != 'mootools') {
        $_MG_CONF['js_lib'] = 'jquery';
    }
}

$_MG_CONF['path_to_imagemagick'] = '';
if ($_CONF['image_lib'] == 'imagemagick') {
    $tmpstr = $_CONF['path_to_mogrify'];
    if (strtolower(substr($tmpstr, -4)) == '.exe') {
        $tmpstr = substr($tmpstr, 0, strlen($tmpstr) - 4);
    }
    if (strtolower(substr($tmpstr, -8)) == '/mogrify') {
        $tmpstr = substr($tmpstr, 0, strlen($tmpstr) - 7); // Note that the path must end in a slash
    }
    $_MG_CONF['path_to_imagemagick'] = $tmpstr;
}

$_MG_CONF['verbose'] = false;

require_once $_MG_CONF['path_html'] . 'common.php';

/**
* Check to see if this Plugin uses comments
*
* @return   boolean true or false
*
*/
function plugin_commentsupport_mediagallery()
{
    return true;
}


/**
 * Plugin function to save a comment
 *
 * @param   string  $title  comment title
 * @param   string  $comment comment text
 * @param   string  $id     Item id to which $cid belongs
 * @param   int     $pid    comment parent
 * @param   string  $postmode 'html' or 'text'
 * @return  mixed   false for failure, HTML string (redirect?) for success
 */
function plugin_savecomment_mediagallery($title, $comment, $id, $pid, $postmode)
{
    global $_CONF, $_MG_CONF, $_TABLES, $LANG03;

    $retval = '';

    $sql = "SELECT a.enable_comments "
         . "FROM {$_TABLES['mg_albums']} AS a "
         . "LEFT JOIN {$_TABLES['mg_media_albums']} AS ma ON a.album_id = ma.album_id "
         . "WHERE ma.media_id = '$id'";
    $result = DB_query($sql);
    $A = DB_fetchArray($result);

    if ($A['enable_comments'] != 1) {
        return COM_refresh($_CONF['site_url'] . '/index.php');
    }

    $ret = CMT_saveComment($title, $comment, $id, $pid, 'mediagallery', $postmode);

    if ($ret > 0) { // failure //FIXME: some failures should not return to comment form
        $retval .= CMT_commentForm($title, $comment, $id, $pid, 'mediagallery',
                                  $LANG03[14], $postmode);
        
        if (!defined('COMMENT_ON_SAME_PAGE')) {
            $retval = COM_createHTMLDocument($retval, array('pagetitle' => $LANG03[1]));
        } else {
            if (!COMMENT_ON_SAME_PAGE) {
                $retval = COM_createHTMLDocument($retval, array('pagetitle' => $LANG03[1]));
            }
        }
        return $retval;
    } else { // success
        $comments = DB_count($_TABLES['comments'], array('sid', 'type'), array(addslashes($id), 'mediagallery'));
        DB_change($_TABLES['mg_media'], 'media_comments', $comments, 'media_id', addslashes($id));
        return COM_refresh($_MG_CONF['site_url'] . "/media.php?s=$id");
    }

    return false;
}

/**
 * Plugin function to delete a comment
 * $cid    Comment to be deleted
 * $id     Item id to which $cid belongs
 *
 */
function plugin_deletecomment_mediagallery($cid, $id)
{
    global $_CONF, $_MG_CONF, $_TABLES, $MG_albums;

    // find the album that holds this peice of media
    $sql = "SELECT album_id FROM {$_TABLES['mg_media_albums']} WHERE media_id='" . addslashes($id) . "'";
    $result = DB_query($sql);
    $nRows  = DB_numRows($result);
    $access = 0;
    if ($nRows > 0) {

        MG_initAlbums();

        $row = DB_fetchArray($result);
        $aid = $row['album_id'];
        $access = 3;
        if (! $MG_albums[0]->owner_id) {
            $access = $MG_albums[$aid]->access;
        }
    }

    if ($access == 3 || SEC_hasRights('mediagallery.admin')) {
        if (CMT_deleteComment($cid, $id, 'mediagallery') == 0) {
            //reduce count in media table
            $comments = DB_count ($_TABLES['comments'], array('sid','type'), array(addslashes($id), 'mediagallery'));
            DB_change($_TABLES['mg_media'],'media_comments', $comments, 'media_id', addslashes($id));
            // Now redirect the program flow to the view of the file and its comments
            return (COM_refresh($_MG_CONF['site_url'] . "/media.php?s=$id"));
        }
    }
    return false;
}

function plugin_getcommenturlid_mediagallery( ) {
    global $_CONF, $_MG_CONF;
    $retval = array();
    $retval[] = $_MG_CONF['site_url'] . '/media.php';
    $retval[] = 's';
    $retval[] = 'page=';
    return $retval;
}

/**
 * Plugin function to display a specific comment thread
 * $id      Unique idenifier for item comment belongs to
 * $cid     Comment id to display (possibly including sub-comments)
 * $title   Page/comment title
 * $order   'ASC' or 'DSC' or blank
 * $format  'threaded', 'nested', or 'flat'
 * $page    Page number of comments to display
 * $view    True to view comment (by cid), false to display (by $pid)
 */
function plugin_displaycomment_mediagallery($id, $commentid, $title, $order, $format, $page, $view)
{
    global $_CONF, $_USER, $_MG_CONF, $LANG_LOGIN;

    if (COM_isAnonUser() && $_MG_CONF['loginrequired'] == 1)  {
        $display = SEC_loginRequiredForm();
        $display = MG_createHTMLDocument($display);
        echo $display;
        exit;
    }

    MG_initAlbums();
    require_once $_CONF['path'] . 'plugins/mediagallery/include/lib-media.php';
    list($ptitle,$retval,$themeCSS,$album_id) =  MG_displayMediaImage( $id, 0, 0, 0 );

    $retval = $themeCSS . $retval;
    $delete_option = SEC_hasRights('mediagallery.admin');
    $view_comment = ($view == 1);
    $retval .= CMT_userComments($id, $title, 'mediagallery', $order, $format, $commentid, $page, $view_comment, $delete_option);

    return $retval;
}


/**
* Returns the items for this plugin that should appear on the main menu
*
* NOTE: this MUST return the url/value pairs in the following format
* $<arrayname>[<label>] = <url>
*
* Universal Plugin:  Uncomment and fill in variables;
*
* @return    array   Associative array of menu items to be added to menu
*
*/

function plugin_getmenuitems_mediagallery() {
    global $_CONF, $_USER, $_MG_CONF;

    if ( COM_isAnonUser() && $_MG_CONF['loginrequired'] == 1 )  {
        return '';
    }

    $menulabel = $_MG_CONF['menulabel'];
    $menurul   = $_MG_CONF['site_url'] . "/index.php";     // Menu url here

    $menuitems = array();
    $menuitems["$menulabel"] = $menurul;

    return $menuitems;
}


function plugin_getheadercode_mediagallery()
{
    global $_CONF, $_MG_CONF, $_SCRIPTS, $mgLightBox, $swfjsinclude, $themeStyle;

    $mg_header = '';

    $f_gl18orlater = function_exists('COM_versionCompare');

    //
    // Check to see if the template path is using the plugins/ directory, if yes,
    // we must point to the stylesheet in public_html/mediagallery/
    //
    if ( $_MG_CONF['template_path'] == $_CONF['path'] . 'plugins/mediagallery/templates' ) {
        if ($_MG_CONF['js_lib'] == 'mootools') {
            $styleFile = $_MG_CONF['site_url'] . '/style.css';
        } else {
            $styleFile = $_MG_CONF['site_url'] . '/style2.css';
        }
    } else {
        if ($_MG_CONF['js_lib'] == 'mootools') {
            $styleFile = $_CONF['layout_url'] . '/mediagallery/style.css';
        } else {
            $styleFile = $_CONF['layout_url'] . '/mediagallery/style2.css';
        }
    }

    $mg_header .= LB . '<link rel="stylesheet" type="text/css" href="' . $styleFile . '"' . XHTML . '>' . LB;
    $mg_header .= '<script type="text/javascript" src="' . $_MG_CONF['site_url'] . '/js/swfobject.js"></script>' . LB;
    $mg_header .= '<script type="text/javascript" src="' . $_MG_CONF['site_url'] . '/js/mediagallery.js"></script>' . LB;

    if ($_MG_CONF['js_lib'] == 'mootools') {
        if ( $_MG_CONF['load_mootools'] ) { // MooTools
            $mg_header .= '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/mootools/1.11/mootools-yui-compressed.js"></script>' . LB;
        }
    } else {
        if ( $_MG_CONF['load_jquery'] ) {   // JQuery
            if ($f_gl18orlater) {
                $_SCRIPTS->setJavaScriptLibrary('jquery');

            } else {
                $mg_header .= '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>' . LB;
            }
        }
    }

    if ( ! $_MG_CONF['disable_lightbox'] ) {
        if ($_MG_CONF['js_lib'] == 'mootools') {
            // Slimbox for MooTools
            $mg_header .= '<script type="text/javascript" src="' . $_MG_CONF['site_url'] . '/js/slimbox.js"></script>' . LB;
        } else {
            // Slimbox 2 for JQuery
            if ($f_gl18orlater) {
                $_SCRIPTS->setJavaScriptFile('jquery.slimbox2', '/mediagallery/js/slimbox2.js');
            } else {
                $mg_header .= '<script type="text/javascript" src="' . $_MG_CONF['site_url'] . '/js/slimbox2.js"></script>' . LB;
            }
        }
    }

    if ($_MG_CONF['js_lib'] == 'mootools') {
        $mg_header .= '<script type="text/javascript" src="' . $_MG_CONF['site_url'] . '/js/slideshow.js"></script>' . LB;
//      $mg_header .= '<script type="text/javascript" src="' . $_MG_CONF['site_url'] . '/js/rating.js"></script>' . LB;
    } else {
        if ($f_gl18orlater) {
            $_SCRIPTS->setJavaScriptFile('jquery.cyclelite', '/mediagallery/js/jquery.cycle.lite.min.js');
//          $_SCRIPTS->setJavaScriptFile('jquery.rating', '/mediagallery/js/jquery.rating.js');
        } else {
            $mg_header .= '<script type="text/javascript" src="' . $_MG_CONF['site_url'] . '/js/jquery.cycle.lite.min.js"></script>' . LB;
//          $mg_header .= '<script type="text/javascript" src="' . $_MG_CONF['site_url'] . '/js/jquery.rating.js"></script>' . LB;
        }
    }

    if (substr_count($_SERVER['PHP_SELF'], '/mediagallery/slideshow.php') > 0) {
        $using_jquery = ($_MG_CONF['js_lib'] == 'mootools') ? 'false' : 'true';
        $script = '<script type="text/javascript">var using_jquery = ' . $using_jquery . ';</script>';
        if ($f_gl18orlater) {
            $_SCRIPTS->setJavaScript($script);
            $_SCRIPTS->setJavaScriptFile('mg.slideshow', '/mediagallery/js/bootloader.js');
        } else {
            $mg_header .= $script . LB;
            $mg_header .= '<script type="text/javascript" src="' . $_MG_CONF['site_url'] . '/js/bootloader.js"></script>' . LB;
        }
    }

    $swfjsinclude = 1;

    if (!empty($themeStyle)) {
        $mg_header .= $themeStyle;
    }

    require_once $_CONF['path'] . 'plugins/mediagallery/include/classFrame.php';
    $nFrame = new mgFrame();
    $nFrame->constructor( $_MG_CONF['random_skin'] );
    $fCSS= $nFrame->getCSS();
    $mg_header .= $fCSS;

    return $mg_header;
}

/**
  * Whats New Block API Support
  * Return the Headline and Byline for the new section in the Whatsnew Block
*/
function plugin_whatsnewsupported_mediagallery() {
    global $_CONF, $_MG_CONF, $LANG_MG00;


    if ( $_MG_CONF['whatsnew'] == 0 ) {
        return '';
    }

    if ( $_MG_CONF['installed_version'] != $_MG_CONF['pi_version'] ) {
        return '';
    }

    if ( COM_isAnonUser() && $_MG_CONF['loginrequired'] == 1 )  {
        return '';
    }

    $label = sprintf($LANG_MG00['whatsnew_prompt'],$_MG_CONF['whatsnew_time']);

    return array($LANG_MG00['whatsnew_title'],$label);
}
/**
  * API function provides the content of our "What's New" feed
*/
function plugin_getwhatsnew_mediagallery() {
    global $MG_albums, $_TABLES, $_CONF, $_MG_CONF, $_USER, $LANG_MG03;

    $retval = '';

    if ($_MG_CONF['whatsnew'] == 0 ) {
        return;
    }

    if ( $_MG_CONF['installed_version'] != $_MG_CONF['pi_version'] ) {
        return;
    }

    if ( COM_isAnonUser() && $_MG_CONF['loginrequired'] == 1 )  {
        return;
    }

    MG_initAlbums();

    $newMediaCount    = 0;
    $newCommentCount = 0;

    $items = array();

    $newAge = time() - ($_MG_CONF['whatsnew_time'] * 86400);

    $sql = "SELECT album_id FROM {$_TABLES['mg_albums']} WHERE last_update >= '" . $newAge . "' ORDER BY last_update DESC";
    $result = DB_query($sql,1);
    while ($row = DB_fetchArray($result)) {
        $aid = $row['album_id'];

        if ( $MG_albums[$aid]->access > 0 && $MG_albums[$aid]->hidden != 1 && $MG_albums[$aid]->getOffset() != -1) {
            $title = $MG_albums[$aid]->title;
            if ( strlen($title) > $_MG_CONF['title_length'] ) {
                $dTitle = substr($title,0,$_MG_CONF['title_length'] - 3) . '...';
            } else {
                $dTitle = $title;
            }
            $items[] = '<a href="' . $_MG_CONF['site_url'] . '/album.php?aid=' . $aid . '" title="'.htmlspecialchars($title,ENT_COMPAT,COM_getEncodingt()).'">' . $dTitle . '</a>';
            $newMediaCount++;
        }

    }
    if ( $newMediaCount == 0 ) {
        $retval .= $LANG_MG03['no_new_items'] . '<br' . XHTML . '>' . LB;
    }
    if ( $_MG_CONF['disable_whatsnew_comments'] == 0 ) {
        $sql = "SELECT * FROM {$_TABLES['comments']} AS c LEFT JOIN {$_TABLES['mg_media_albums']} AS ma ON c.sid=ma.media_id LEFT JOIN {$_TABLES['mg_albums']} AS a ON ma.album_id=a.album_id WHERE c.date >=  DATE_SUB(NOW(), INTERVAL " . $_MG_CONF['whatsnew_time'] . " DAY ) AND c.type='mediagallery' GROUP BY c.sid ORDER BY c.date DESC LIMIT 15";

        $result = DB_query( $sql,1);
        $nrows2 = DB_numRows( $result );
        if( $nrows2 == 0 ) {
            $retval .= '' . '<br' . XHTML . '>' . LB;
        } else {
            while ($A = DB_fetchArray($result)) {
                $access = SEC_hasAccess ($A['owner_id'],
                                         $A['group_id'],
                                         $A['perm_owner'],
                                         $A['perm_group'],
                                         $A['perm_members'],
                                         $A['perm_anon']);
                if ( $access > 0 ) {
                    $titleLength = $_MG_CONF['title_length'];        // Compensate for the added HTML
                    $title = "<b>C:</b>&nbsp;".$A['title'];
                    $str = "<a href=\"{$_CONF['site_url']}/comment.php?mode=view&amp;cid={$A['cid']}&amp;sid={$A['sid']}&amp;type=mediagallery\">";
                    $str .= substr($title,0,$titleLength);
                    $str .= '</a>';
                    $items[] = $str;
                    $newCommentCount++;
                }
            }
        }
        if ( $newCommentCount == 0 ) {
            $retval .= $LANG_MG03['no_comments'] . '<br' . XHTML . '>' . LB;
        }
    }
    if ( $newMediaCount == 0 && $newCommentCount == 0 ) {
        return $retval;
    } else {
        return $items;
    }
}

/**
* Shows the statistics for the plugin on stats.php.  If
* $showsitestats is 1 then we are to only print the overall stats in the 'site
* statistics' box otherwise we show the detailed stats for the plugin
*
* Universal Plugin:  Modify/uncomment use it
*
* @param    int showsitestate   Flag to let us know which stats to get
* @return   string  returns formatted HTML to insert in stats page
*
*/
function plugin_showstats_mediagallery($showsitestats)
{
    global $_CONF;

    MG_initAlbums();
    require_once $_CONF['path'] . 'plugins/mediagallery/include/statistics.php';
    return MG_showstats($showsitestats);
}


function plugin_statssummary_mediagallery() {
    global $_CONF, $_MG_CONF, $_TABLES, $LANG_MG00, $_USER;

    if ( COM_isAnonUser() && $_MG_CONF['loginrequired'] == 1 )  {
        return;
    }

    // This shows in the summary box
    $total_items=DB_count($_TABLES['mg_media']);
    $summary_label = $LANG_MG00['items_in'] . ' ' .  $LANG_MG00['plugin'];
    $retval[] = $summary_label;
    $retval[] = $total_items;
    return $retval;
}



/**
* This will put an option for the plugin in the command and control block on moderation.php
*
* Add the plugin name, icon and link to the command and control block in moderation.php
*
* @return   array   Array containing (plugin name, admin url, url of plugin icon)
*
*/

function plugin_cclabel_mediagallery() {
    global $_CONF, $_MG_CONF, $LANG_MG00;

    if (SEC_hasRights('mediagallery.config') ) {
        return array($LANG_MG00['plugin'],
            $_MG_CONF['admin_url'] . 'index.php',
            MG_getImageFile('mediagallery.png'));
    } else {
        return '';
    }
}

/**
* Returns the administrative option for this plugin
*
* Adds the plugin to the Admin menu
*
* Universal Plugin:  Modify/Uncomment use it
*
* @return   array   Array containing (plugin name, plugin admin url, # of items in plugin or '')
*
*/
function plugin_getadminoption_mediagallery() {
    global $_CONF, $_TABLES, $LANG_MG00, $_MG_CONF;

    if (SEC_hasRights('mediagallery.config')) {
        return array($LANG_MG00['plugin'], $_MG_CONF['admin_url'] . 'index.php', DB_count($_TABLES['mg_albums']));
    }
}



function plugin_autotags_mediagallery ($op, $content = '', $autotag = '')
{
    global $_CONF, $_TABLES, $_MG_CONF;

    if ( $_MG_CONF['installed_version'] != $_MG_CONF['pi_version'] ) {
        return $content;
    }
    if ($op == 'tagname') {
        return array('album','media','img','slideshow','fslideshow','video','audio',
				'download','image','oimage','mlink','alink','playall','medialist'
				,'albumlist');//@@@@@
    } else if ($op == 'parse') {
        MG_initAlbums();
        require_once $_CONF['path'] . 'plugins/mediagallery/include/autotags.php';
        return MG_autotags($op, $content, $autotag);
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
function plugin_moderationvalues_mediagallery() {
    return;
}

/**
* show files for moderation on submissions page
*
* Uses the Plugin class to return data required by moderation.php to list
* plugin objects that need to be moderated.
*
* @return       Plugin       return Plugin class containing submission data
*
*/
function plugin_itemlist_mediagallery() {
    return;
}

/**
* Returns the plugins submission form
*
* Returns a string contain the HTML of the submission form for the plugin.
*
* @return       string       The HTML form
*
*/
function plugin_submit_mediagallery() {
    $retval = '<h1>submit file</h1>';
    $retval .= '<p>This would be the input form for uploading files. ';
    $retval .= 'But that functionality is already implemented elsewhere.</p>';

    return($retval);
}

/**
*
* Checks that the current user has the rights to moderate the
* plugin, returns true if this is the case, false otherwise
*
* @return        boolean       Returns true if moderator
*
*/
function plugin_ismoderator_mediagallery() {
    return SEC_hasRights('mediagallery.admin');
}

/**
* Returns the number of submissions waiting moderation
*
* Calculates the number of submissions that are currently waiting
* to be moderated and returns that number.
*
* @return      int       number of submissions awaiting moderation
*/
function plugin_submissioncount_mediagallery() {
    return;
}

/**
* Performs plugin exclusive work for items approved by moderation
*
* While moderation.php handles the actual move from mediagallery submission
* to mediagallery tables, within the function we handle all other approval
* relate tasks
*
* @param      string       $id      Identifying string
* @return     string       Any wanted HTML output
*
*/
function plugin_moderationapprove_mediagallery($id)
{
    return '';
}

/**
* Performs plugin exclusive work for items deleted by moderation
*
* While moderation.php handles the actual removal from mediagallery submission
* table, within this function we handle all other deletion
* relate tasks
*
* @param      string       $id      Identifying string
* @return     string       Any wanted HTML output
*
*/
function plugin_moderationdelete_mediagallery($id) {
    return '';
}

/**
* saves submission to mediagallery submission table
*
* takes data input by submission form and populates the mediagallery submission
* table.
*
* @param       array       $A       array (key => value) of form items
* @return      boolean     returns true if successful
*
*/
function plugin_savesubmission_mediagallery($A) {
    return false;
}

function plugin_searchtypes_mediagallery()
{
    global $_MG_CONF, $LANG_MG00;

    if ($_MG_CONF['disable_search_integration'] == 1) {
        return '';
    }
    if (COM_isAnonUser() && $_MG_CONF['loginrequired'] == 1)  {
        return '';
    }

    $tmp['mediagallery'] = $LANG_MG00['plugin'];
    return $tmp;
}


/**
* this searches for pages matching the user query and returns an array of
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
function plugin_dopluginsearch_mediagallery($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage) {
    global $_TABLES, $_CONF, $_MG_CONF, $LANG_MG00, $LANG_MG01, $LANG09;

    if ($_MG_CONF['disable_search_integration'] == 1) {
        return '';
    }
    if (COM_isAnonUser() && $_MG_CONF['loginrequired'] == 1)  {
        return '';
    }

    $query = trim(addslashes(htmlspecialchars($query)));

    $sql = "SELECT DISTINCT a.*,ma.album_id,a.perm_anon, m.media_id AS id,m.media_user_id AS uid,m.media_desc AS description ,m.media_title AS title,m.media_time AS date,m.media_views AS hits, CONCAT('/mediagallery/media.php?s=',m.media_id) AS url  FROM " .
            $_TABLES['mg_media'] . " as m " .
            " INNER JOIN " . $_TABLES['mg_media_albums'] . " as ma " .
            " ON m.media_id=ma.media_id " .
            " INNER JOIN {$_TABLES['mg_albums']} AS a ON ma.album_id=a.album_id " .
            " WHERE 1=1 ";
    $sql .= COM_getPermSQL('AND') . ' ';

    if( !SEC_inGroup( 'Root')) {
        $sql .= ' AND hidden=0 ';
    }

    if (!empty ($author))
        $sql .= "AND (m.media_user_id = '$author') ";

    $search = new SearchCriteria('mediagallery', $LANG_MG00['plugin']);
    $columns = array('m.media_desc','m.media_title');
    $sql .= $search->getDateRangeSQL('AND', 'm.media_time', $datestart, $dateend);
    list($sql,$ftsql) = $search->buildSearchSQL($keyType, $query, $columns, $sql);
    $search->setSQL($sql);
    $search->setFTSQL($ftsql);
    $search->setRank(3);

    return $search;
}


/**
* Returns the user option for this plugin
*
* Adds the plugin to the user menu
*
* Universal Plugin:  modify/uncomment use it
*
* @return   array   Array containing (plugin name, plugin user url, 0)
*
*/
function plugin_getuseroption_mediagallery() {
    global $_CONF, $_MG_CONF, $_USER, $LANG_MG01, $LANG_MG03, $_TABLES;

    if (COM_isAnonUser()) return '';

    $menuitems = array();

    if ($_MG_CONF['up_display_rows_enabled'] != 0 ||
        $_MG_CONF['up_display_columns_enabled'] != 0 ||
        $_MG_CONF['up_mp3_player_enabled'] != 0 ||
        $_MG_CONF['up_av_playback_enabled'] != 0 ||
        $_MG_CONF['up_thumbnail_size_enabled'] != 0) {
        $menuitems[] = array($LANG_MG01['user_prefs'], $_MG_CONF['site_url'] . '/userprefs.php', 0);
    }

    if ($_MG_CONF['member_albums'] == 1 && $_MG_CONF['link_to_member_album'] == 1) {
        $sql = "SELECT album_id FROM {$_TABLES['mg_albums']} WHERE owner_id=" . $_USER['uid'] . " AND album_parent='" . $_MG_CONF['member_album_root'] . "' LIMIT 1";
        $result = DB_query($sql);
        $numRows = DB_numRows($result);
        if ( $numRows > 0 ) {
            $A = DB_fetchArray($result);
            $album_id = $A['album_id'];
            $menuitems[] = array($LANG_MG03['your_member_album'], $_MG_CONF['site_url'] . '/album.php?aid=' . $album_id, 0);
        }
    }
    return $menuitems;
}

function phpblock_mg_randommedia($parm1=array(), $parm2='') {
    global $_CONF, $_MG_CONF, $_TABLES, $_USER, $LANG_MG03, $_DB_dbms;

    $retval = '';

    if ( $_MG_CONF['installed_version'] != $_MG_CONF['pi_version'] ) {
        return $retval;
    }

    if ( !isset($_MG_CONF['random_width']) || $_MG_CONF['random_width'] == 0 ) {
        $_MG_CONF['random_width'] = 120;
    }

    $tn_mode = $_MG_CONF['random_img_ratio'];
    $tn_size = intval($_MG_CONF['random_width']);
    if (!empty($parm2)) {
        $args = explode(',', $parm2);
        switch ($args[0]) {
            case 'cube':
            case 'square':
            case 1:
                $tn_mode = 1;
                break;
            case 'normal':
            case 'original':
            case 0:
                $tn_mode = 0;
                break;
        }
        if (!empty($args[1])) {
            $tn_size = intval($args[1]);
        }
    }
    if ($tn_size < 10)  $tn_size = 100;
    if ($tn_size > 200) $tn_size = 200;
    $tn_fname_add = ($tn_mode == 1) ? '_150x150' : '';

    if ( $_DB_dbms == 'mssql' ) {
        $randomSelect = "NewID() ";
    } else {
        $randomSelect = "RAND(NOW()) ";
    }
    $sql = "SELECT ma.album_id,m.media_id,m.media_filename,m.media_title FROM {$_TABLES['mg_albums']} as a LEFT JOIN {$_TABLES['mg_media_albums']} as ma
            on a.album_id=ma.album_id LEFT JOIN {$_TABLES['mg_media']} as m on ma.media_id=m.media_id WHERE
            m.media_type=0 AND a.enable_random=1 AND a.hidden=0 " . COM_getPermSQL('and') . " ORDER BY " . $randomSelect . " LIMIT 1";
    $result = DB_query( $sql,1 );
    $nRows  = DB_numRows( $result );
    if ($nRows <= 0) return '';
    $row = DB_fetchArray($result);

    require_once $_CONF['path'] . 'plugins/mediagallery/include/classFrame.php';
    $nFrame = new mgFrame();
    $nFrame->constructor( $_MG_CONF['random_skin'] );
    $imageFrameTemplate = $nFrame->getTemplate();
    $frWidth = $nFrame->frame['wHL'] + $nFrame->frame['wHR'];
    $frHeight = $nFrame->frame['hVT'] + $nFrame->frame['hVB'];
//    $fCSS= $nFrame->getCSS();

    $F = new Template($_MG_CONF['template_path']);
    $F->set_var('xhtml',XHTML);
    $F->set_var('media_frame',$imageFrameTemplate);

    $T = MG_templateInstance( MG_getTemplatePath(0) );
    $T->set_file('rblock', 'random_block.thtml');
    $T->set_var('site_url', $_CONF['site_url']);
    $T->set_var('site_admin_url', $_CONF['site_admin_url']);

    $url_media = $_MG_CONF['site_url'] . '/media.php?s=' . $row['media_id'];
    $url_album = $_MG_CONF['site_url'] . '/album.php?aid=' . $row['album_id'] .'&amp;s='.$row['media_id'].'#'.$row['media_id'];

    $msize = false;
    foreach ($_MG_CONF['validExtensions'] as $ext) {
        if ( file_exists($_MG_CONF['path_mediaobjects'] . 'tn/' . $row['media_filename'][0] .'/' . $row['media_filename'] . $tn_fname_add . $ext) ) {
            $media_thumbnail= $_MG_CONF['mediaobjects_url'] . '/tn/' . $row['media_filename'][0] .'/' . $row['media_filename'] . $tn_fname_add . $ext;
            $msize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'disp/' . $row['media_filename'][0] .'/' . $row['media_filename'] . $ext);
            break;
        }
    }

    if ($msize == false) return '';

    $imgwidth  = $msize[0];
    $imgheight = $msize[1];
    if ($tn_mode == 1) {
        $imgwidth  = $tn_size;
        $imgheight = $tn_size;
    }

    if ( $imgwidth > $imgheight ) {
        $ratio = $imgwidth / $tn_size;
        $width = $tn_size;
        $height = round($imgheight / $ratio);
    } else {
        $ratio = $imgheight / $tn_size;
        $height = $tn_size;
        $width = round($imgwidth / $ratio);
    }

    if ( $parm2 == 'album' ) {
        $url_media = $url_album;
    }

    // frame template variables
    $F->set_var(array(
        'media_link_start'  => '<a href="' . $url_media . '">',
        'media_link_end'    => '</a>',
        'url_display_item'  =>  $url_media,
        'media_thumbnail'   =>  $media_thumbnail,
        'media_size'        =>  'width="' . $width . '" height="' . $height . '"',
        'media_height'      =>  $height,
        'media_width'       =>  $width,
        'border_width'      =>  $width  + (($_MG_CONF['random_skin'] == 'mgShadow') ? 12 : 18),
        'border_height'     =>  $height + (($_MG_CONF['random_skin'] == 'mgShadow') ? 12 : 18),
        'row_height'        =>  $height + 40,
        'frWidth'           =>  $width  - $frWidth,
        'frHeight'          =>  $height - $frHeight,
        'media_tag'         =>  '',
    ));
    $F->parse('media','media_frame');
    $media_item_thumbnail = $F->finish($F->get_var('media'));

    $T->set_var(array(
        'url_media'         =>  $url_media,
        'url_album'         =>  $url_album,
        'media_thumbnail'   =>  $media_item_thumbnail,
        'media_height'      =>  $height,
        'media_width'       =>  $width,
        'border_width'      =>  $width + 14,
        'media_title'       =>  PLG_replaceTags(strip_tags($row['media_title'])),
        'lang_browse_album' =>  $LANG_MG03['browse_album'],
        'xhtml'             =>  XHTML,
        'framecss'          =>  '', //$fCSS,
    ));

    $T->parse('output', 'rblock');
    $retval .= $T->finish($T->get_var('output'));

    return $retval;
}

function phpblock_mg_maenroll(){
    global $_CONF, $_MG_CONF, $_MG_USERPREFS, $_TABLES, $_USER, $LANG_MG03;

    if ( COM_isAnonUser() )  {
        return;
    }

    $_MG_USERPREFS = MG_getUserPrefs();
    if ( $_MG_CONF['member_albums'] != 1 || $_MG_USERPREFS['member_gallery'] == 1 || $_USER['uid'] < 3) {
        return;
    }
    if ( SEC_inGroup("Remote Users") && $_MG_CONF['allow_remote'] != 1 ) {
        return;
    }

    $url = sprintf($LANG_MG03['member_album_signup'], $_MG_CONF['site_url'] . '/enroll.php');
    return $url;
}


function plugin_centerblock_mediagallery($where=1, $page=1, $topic ='')
{
    global $_CONF;

    MG_initAlbums();
    require_once $_CONF['path'] . 'plugins/mediagallery/include/centerblock.php';
    return MG_centerblock($op, $content, $autotag);
}


function plugin_chkVersion_mediagallery()
{
    global $_MG_CONF;

    return $_MG_CONF['pi_version'];
}


function MG_clean_directories($path, $needle = '')
{
    if ( $path[strlen($path)-1] != '/' ) {
        $path .= '/';
    }
    if ($dir = @opendir($path)) {
        while ($entry = readdir($dir)) {
            if ($entry == '.' || $entry == '..' || is_link($entry)) {
                continue;
            } elseif (is_dir($path . $entry)) {
                MG_clean_directories($path . $entry, $needle);
                @rmdir($entry);
            } elseif (empty($needle) || strpos($entry, $needle) !== false) {
                @unlink($path . $entry);
            }
        }
        @closedir($dir);
    }
}

function plugin_upgrade_mediagallery() {
    global $_CONF, $_TABLES, $_MG_CONF;

    require_once $_CONF['path'].'plugins/mediagallery/upgrade.php';

    return mediagallery_upgrade();
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
function plugin_autouninstall_mediagallery ()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array('mg_albums','mg_media','mg_media_albums','mg_usage_tracking','mg_config', 'mg_mediaqueue', 'mg_media_album_queue','mg_playback_options','mg_userprefs','mg_exif_tags', 'mg_watermarks', 'mg_category', 'mg_sessions', 'mg_session_items', 'mg_session_items2','mg_session_log', 'mg_sort', 'mg_postcard','mg_rating'),
        /* give the full name of the group, as in the db */
        'groups' => array('mediagallery Admin','mediagallery Config'),
        /* give the full name of the feature, as in the db */
        'features' => array('mediagallery.admin','mediagallery.config'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array('phpblock_mg_randommedia','phpblock_mg_maenroll'),
        /* give all vars with their name */
        'vars'=> array('mediagallery_gid','mediagallery_cid')
    );
    return $out;
}

/**
* Removes the data structures for this plugin from the Geeklog database.
* This routine will get called from the Plugin install program if user select De-Install or if Delete is used in the Plugin Editor.
* The Plugin Installer will also call this routine upon and install error to remove anything it has created.
* The Plugin installer will pass the optional parameter which will then double check that plugin has first been disabled.
*
* For this plugin, this routine will also remove the Block definition.
*
* Returns True if all Plugin related data is removed without error
*
* @param    string   $installCheck     Defult is blank but if set, check if plugin is disabled first
*
* @return   boolean True if successful false otherwise
*
*/
function plugin_uninstall_mediagallery($installCheck='') {
    global $_CONF, $_DB_table_prefix, $_TABLES, $LANG_MG00;

    // Check and see if plugin is still enabled - if so display warning and exit
    if ($installCheck != '' && DB_getItem($_TABLES['plugins'],'pi_enabled', 'pi_name = "' .$pi_name. '"')) {
        COM_errorLog("Plugin is installed and enabled. Disable first if you want to de-install it",1);
        $display .= COM_startBlock($LANG_MG00['warning']);
        $display .= $LANG_MG00['enabled'];
        $display .= COM_endBlock();
        echo $display;
        return false;
    }

    require_once $_CONF['path'] . 'plugins/mediagallery/install.inc';

    $rc = plugin_remove_mediagallery();

    return $rc;
}

function plugin_user_create_mediagallery( $uid, $force = 0 ) {
    global $_TABLES, $_MG_CONF, $LANG_MG01, $_CONF;

    $sql = "INSERT INTO {$_TABLES['mg_userprefs']} (uid, active, display_rows, display_columns, mp3_player, playback_mode, tn_size, quota, member_gallery) VALUES (" . intval($uid) . ",1,0,0,-1,-1,-1," . $_MG_CONF['member_quota'] . ",0)";
    DB_query($sql,1);

    $retval = -1;
    if ( $force == 1 || ($_MG_CONF['member_albums'] == 1 && $_MG_CONF['member_auto_create'] == 1) ) {
        $username               = DB_getItem($_TABLES['users'],'username','uid=' . intval($uid));
        $fullname               = DB_getItem($_TABLES['users'],'fullname','uid=' . intval($uid));
        $grp_id                 = DB_getItem($_TABLES['groups'],'grp_id','grp_name="mediagallery Admin"');

        if ( $grp_id == NULL || $grp_id == '' || $grp_id < 2 ) {
            $grp_id = 2;
        }

        require_once $_CONF['path'] . 'plugins/mediagallery/include/classAlbum.php';
        $album = new mgAlbum();

        $title = $username . $LANG_MG01['member_album_postfix'];
        if ( !empty($fullname) && $_MG_CONF['member_use_fullname'] == 1 ) {
            $title = $fullname . $LANG_MG01['member_album_postfix'];
        }
        $album->title           = htmlspecialchars(strip_tags(COM_checkWords($title)));
        $album->parent          = $_MG_CONF['member_album_root'];
        $album->group_id        = $grp_id;
        $album->mod_group_id    = $grp_id;
        $album->owner_id        = $uid;
        $album->moderate        = $_MG_CONF['member_moderate'];
        $album->email_mod       = $_MG_CONF['member_email_mod'];
        $album->perm_owner      = $_MG_CONF['member_perm_owner'];
        $album->perm_group      = $_MG_CONF['member_perm_group'];
        $album->perm_members    = $_MG_CONF['member_perm_members'];
        $album->perm_anon       = $_MG_CONF['member_perm_anon'];
        $album->id              = $album->createAlbumID();
        $retval = $album->id;
        $album->saveAlbum();
        $result = DB_query("UPDATE {$_TABLES['mg_userprefs']} SET member_gallery=1 WHERE uid=" . intval($uid),1);
    }
    return $retval;
}

function plugin_user_delete_mediagallery( $uid ) {
    global $_USER, $_CONF, $_TABLES, $_MG_CONF, $LANG_MG00, $LANG_MG01;

    // remove any user preferences in the database

    $sql = "DELETE FROM {$_TABLES['mg_userprefs']} WHERE uid=" . intval($uid);
    DB_query($sql,1);

    if ( $_MG_CONF['member_albums'] ) {
        if ( $_MG_CONF['member_album_archive'] == 0 ) {
            $sql = "SELECT album_id FROM {$_TABLES['mg_albums']} WHERE owner_id=" . intval($uid);
            $result = DB_query($sql);
            while ( $A = DB_fetchArray($result) ) {
                MG_deleteMemberAlbums($A['album_id']);
            }
        } else {
            // update parent album to be archive album
            $sql = "UPDATE {$_TABLES['mg_albums']} SET album_parent=" . $_MG_CONF['member_album_archive'] . " WHERE owner_id=" . intval($uid) . " AND album_parent='" . $_MG_CONF['member_album_root'] ."'";
            DB_query($sql);
        }
    }
}

function MG_deleteMemberAlbums( $album_id ){
    global $_CONF, $_MG_CONF, $_TABLES, $_USER;

    $sql = "SELECT ma.media_id, m.media_filename, m.media_mime_ext
            FROM " . $_TABLES['mg_media_albums'] .
            " as ma LEFT JOIN " . $_TABLES['mg_media'] .
            " as m ON ma.media_id=m.media_id
            WHERE ma.album_id = '" . addslashes($album_id) ."'";

    $result = DB_query( $sql );
    $nRows = DB_numRows( $result );
    $mediarow = array();
    for ( $i=0; $i < $nRows; $i++) {
      $row = DB_fetchArray( $result );
      $mediarow[] = $row;
    }
    if ( count( $mediarow ) != 0 ) {
        for ( $i = 0; $i < count( $mediarow ); $i++ ) {
            $sql = "SELECT COUNT(media_id) AS count FROM " . $_TABLES['mg_media_albums'] . "  WHERE media_id = '" . addslashes($mediarow[$i]['media_id']) . "'";
            $result = DB_query( $sql );
            $row = DB_fetchArray( $result );
            if ( $row['count'] <= 1 ) {
                @unlink($_MG_CONF['path_mediaobjects'] . 'tn/'   . $mediarow[$i]['media_filename'][0] .'/' . $mediarow[$i]['media_filename'] . '.*');
                @unlink($_MG_CONF['path_mediaobjects'] . 'disp/' . $mediarow[$i]['media_filename'][0] .'/' . $mediarow[$i]['media_filename'] . '.*');
                @unlink($_MG_CONF['path_mediaobjects'] . 'orig/' . $mediarow[$i]['media_filename'][0] .'/' . $mediarow[$i]['media_filename'] . '.' . $mediarow[$i]['media_mime_ext']);
                $sql = "DELETE FROM " . $_TABLES['mg_media'] . "  WHERE media_id = '" . addslashes($mediarow[$i]['media_id']) . "'";
                DB_query( $sql );
                DB_delete($_TABLES['comments'], 'sid', addslashes($mediarow[$i]['media_id']));
                DB_delete($_TABLES['mg_playback_options'],'media_id', addslashes($mediarow[$i]['media_id']));
            }
        }
    }
    $sql = "DELETE FROM " . $_TABLES['mg_media_albums'] . " WHERE album_id = '" . addslashes($album_id) ."'";
    DB_query( $sql );
    $sql = "DELETE FROM " . $_TABLES['mg_albums'] . " WHERE album_id = '" . addslashes($album_id)."'";
    DB_query( $sql );
}


// display user info in profile

function plugin_profileblocksdisplay_mediagallery( $uid )
{
    global $_CONF;

    MG_initAlbums();
    require_once $_CONF['path'] . 'plugins/mediagallery/include/profile.php';
    return MG_profileblocksdisplay($uid);
}

/*
 * Helper functions for executing external programs...
 */

function MG_exec($cmd) {
    global $_CONF, $_MG_CONF;

    $debugfile = "";
    $status="";
    $results=array();
    if ($_MG_CONF['verbose'] ) {
        COM_errorLog(sprintf("MG_exec: Executing: %s",$cmd));
    }

    $debugfile = $_CONF['path'] . 'logs/debug.log';

    if (PHP_OS == "WINNT") {
        $cmd .= ">NUL 2>&1";
//        exec("cmd.exe /c " . $cmd, $results, $status);
        exec('"' . $cmd . '"',$results,$status);
    } else {
        exec($cmd, $results, $status);
    }

    return array($results, $status);
}

// Used in climport.php, lib-batch.php and lib-upload.php
function MG_execWrapper($cmd) {

    list($results, $status) = MG_exec($cmd);

    if ( $status == 0 ) {
        return true;
    } else {
        COM_errorLog("MG_execWrapper: Failed Command: " . $cmd);
        return false;
    }
}

/**
* Return information for a media item
*
* @param    string  $id         media id or *
* @param    string  $what       comma-separated list of properties
* @param    int     $uid        user ID or 0 = current user
* @param    array   $options    (reserved for future extensions)
* @return   mixed               string or array of strings with the information
*
*/
function plugin_getiteminfo_mediagallery($id, $what, $uid = 0, $options = array())
{
    global $_CONF, $_TABLES, $_MG_CONF, $LANG_MG00;

    $properties = explode(',', $what);
    $fields = array();
    foreach ($properties as $p) {
        switch ($p) {
            case 'date-modified':
                $fields[] = 'UNIX_TIMESTAMP(media_upload_time) AS unixdate';
                break;
            case 'description':
            case 'excerpt':
            case 'raw-description' :
                $fields[] = 'media_desc';
                break;
            case 'id':
                $fields[] = 'a.media_id';
                break;
            case 'title':
                $fields[] = 'media_title';
                break;
            case 'label':
            case 'url':
                $fields[] = 'a.media_id';
                break;
            default:
                break;
        }
    }

    $fields = array_unique($fields);

    if (count($fields) == 0) {
        return array();
    }

    $sql = "SELECT " . implode(',', $fields)
         . ", owner_id, group_id, perm_owner, perm_group, perm_members, perm_anon "
         . "FROM {$_TABLES['mg_media']} AS a "
         . "LEFT JOIN {$_TABLES['mg_media_albums']} AS b ON a.media_id=b.media_id "
         . "LEFT JOIN {$_TABLES['mg_albums']} AS c ON b.album_id=c.album_id";

    if ($id != '*') {
        $sql .= " WHERE a.media_id = '" . addslashes($id) . "' LIMIT 1";
    }

    $result = DB_query($sql);
    $numRows = DB_numRows($result);

    $mgadmin = SEC_hasRights('mediagallery.admin');
    $retval = array();
    for ($i = 0; $i < $numRows; $i++) {
        $A = DB_fetchArray($result);
        $access = 3;
        if (!$mgadmin) {
            $access = SEC_hasAccess($A['owner_id'], $A['group_id'], $A['perm_owner'],
                                    $A['perm_group'], $A['perm_members'], $A['perm_anon']);
        }
        if ($access > 0) {
            $props = array();
            foreach ($properties as $p) {
                switch ($p) {
                    case 'date-modified':
                        $props['date-modified'] = $A['unixdate'];
                        break;
                    case 'description':
                    case 'excerpt':
                        $prop[$p] = PLG_replaceTags($A['media_desc']);
                        break;
                    case 'raw-description' :
                        $props[$p] = $A['media_desc'];
                        break;
                    case 'id':
                        $props['id'] = $A['media_id'];
                        break;
                    case 'title':
                        $props['title'] = stripslashes($A['media_title']);
                        break;
                    case 'url':
                        if (empty($A['id'])) {
                            $props['url'] = $_MG_CONF['site_url'].'/media.php?f=0&amp;sort=0&amp;s='.$id;
                        } else {
                            $props['url'] = $_MG_CONF['site_url'].'/media.php?f=0&amp;sort=0&amp;s='.$A['media_id'];
                        }
                        break;
                    case 'label':
                        $props['label'] = $LANG_MG00['menulabel'];
                        break;
                    case 'status':
                        $props['status'] = 1; // stub - default
                        break;
                    default:
                        $props[$p] = '';
                        break;
                }
            }
        }

        $mapped = array();
        foreach ($props as $key => $value) {
            if ($id == '*') {
                if ($value != '') {
                    $mapped[$key] = $value;
                }
            } else {
                $mapped[$key] = $value;
            }
        }

        if ($id == '*') {
            $retval[] = $mapped;
        } else {
            $retval = $mapped;
            break;
        }
    }

    if (($id != '*') && (count($retval) == 1)) {
        $retval = $retval[0];
    }

    return $retval;
}

if (!function_exists('COM_truncate'))
{
    function COM_truncate( $text, $maxlen, $filler = '' ) {
        $newlen = $maxlen - strlen( $filler );
        $len = strlen( $text );
        if( $len > $maxlen ) {
            $text = substr( $text, 0, $newlen ) . $filler;
        }
        return $text;
    }
}


if (!function_exists('COM_isAnonUser'))
{
    /**
      * Checks to see if a specified user, or the current user if non-specified
      * is the anonymous user.
      *
      * @param  int $uid    ID of the user to check, or none for the current user.
      * @return boolean     true if the user is the anonymous user.
      */
    function COM_isAnonUser($uid = '')
    {
        global $_USER;

        /* If no user was specified, fail over to the current user if there is one */
        if (empty($uid)) {
            if (isset($_USER['uid'])) {
                $uid = $_USER['uid'];
            }
        }

        if (!empty($uid)) {
            return ($uid == 1);
        } else {
            return true;
        }
    }
}

if (!function_exists('SEC_loginRequiredForm'))
{
    function SEC_loginRequiredForm()
    {
        global $_CONF, $LANG_LOGIN;
        
        $retval = COM_startBlock ($LANG_LOGIN[1], '',
                  COM_getBlockTemplate ('_msg_block', 'header'));
        $login = new Template($_CONF['path_layout'] . 'submit');
        $login->set_file (array ('login'=>'submitloginrequired.thtml'));
        $login->set_var ('login_message', $LANG_LOGIN[2]);
        $login->set_var ('site_url', $_CONF['site_url']);
        $login->set_var ('lang_login', $LANG_LOGIN[3]);
        $login->set_var ('lang_newuser', $LANG_LOGIN[4]);
        $login->parse ('output', 'login');
        $retval .= $login->finish ($login->get_var('output'));
        $retval .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));

        return $retval;
    }
}

?>