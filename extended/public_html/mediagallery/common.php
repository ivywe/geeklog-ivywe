<?php
// +--------------------------------------------------------------------------+
// | Media Gallery Plugin - glFusion CMS                                      |
// +--------------------------------------------------------------------------+
// | common.php                                                               |
// |                                                                          |
// | Startup and general purpose routines                                     |
// +--------------------------------------------------------------------------+
// | $Id:: common.php 5614 2010-03-19 19:08:33Z mevans0263                   $|
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

// read user prefs, if any...
function MG_getUserPrefs($uid = '', $fields = '*')
{
    global $_TABLES, $_USER;

    if (empty($uid)) {
        if (isset($_USER['uid'])) {
            $uid = $_USER['uid'];
        }
    }
    if (!COM_isAnonUser($uid)) {
        $result = DB_query("SELECT $fields FROM " . $_TABLES['mg_userprefs'] . " WHERE uid='" . $uid . "'", 1);
        if (DB_numRows($result) > 0) {
            return DB_fetchArray($result);
        }
    }
    return '';
}

function MG_initAlbums()
{
    global $_CONF, $_GROUPS, $_MG_CONF, $MG_albums, $_TABLES, $_USER, $_DB_dbms;

    if (isset($MG_albums)) return;

    $mgadmin = SEC_hasRights('mediagallery.admin');
    $root = SEC_inGroup('Root');
    $groups = $_GROUPS;

    if ($_DB_dbms == "mssql") {
        $sql = "SELECT *, CAST(album_desc AS TEXT) as album_desc FROM " . $_TABLES['mg_albums'] . " ORDER BY album_order DESC";
    } else {
        $sql = "SELECT * FROM " . $_TABLES['mg_albums'] . " ORDER BY album_order DESC";
    }
    $result = DB_query($sql, 1);
    $MG_albums = array();

    require_once $_CONF['path'] . 'plugins/mediagallery/include/classAlbum.php';
    $album = new mgAlbum();
    $album->id = 0;
    $album->title = 'root album';
    $album->owner_id = $mgadmin;
    $album->group_id = $root;
    $album->skin     = isset($_MG_CONF['indextheme']) ? $_MG_CONF['indextheme'] : 'default';
    if ($mgadmin) {
        $album->access = 3;
    }

    $MG_albums[$album->id] = $album;

    while ($A = DB_fetchArray($result)) {
        $album  = new mgAlbum();
        $album->constructor($A,$mgadmin,$root,$groups);

        /*
         * We include hidden albums in the array since they
         * can be used in auto tags which a user will have
         * access to.
         */

        if ($album->access > 0) {
            $MG_albums[$album->id] = $album;
        }
    }

    foreach ($MG_albums as $id => $album) {
        if ($id != 0 && isset($MG_albums[$album->parent]->id)) {
            $MG_albums[$album->parent]->setChild($id);
        }
    }
}

function MG_getSortOrder($aid, $sortOrder)
{
    global $MG_albums;

    if ($MG_albums[$aid]->enable_sort == 0) {
        return ' ORDER BY ma.media_order ASC';
    }

    switch ( $sortOrder ) {
        case 0 :    // default
            $orderBy = ' ORDER BY ma.media_order ASC';
            break;
        case 1 :    // default, reverse order
            $orderBy = ' ORDER BY ma.media_order ASC';
            break;
        case 2 :    //  upload time, DESC
            $orderBy = ' ORDER BY m.media_upload_time DESC';
            break;
        case 3 :
            $orderBy = ' ORDER BY m.media_upload_time ASC';
            break;
        case 4 :    // capture time, DESC
            $orderBy = ' ORDER BY m.media_time DESC';
            break;
        case 5 :
            $orderBy = ' ORDER BY m.media_time ASC';
            break;
        case 6 :
            $orderBy = ' ORDER BY m.media_rating DESC';
            break;
        case 7 :
            $orderBy = ' ORDER BY m.media_rating ASC';
            break;
        case 8 :
            $orderBy = ' ORDER BY m.media_views DESC';
            break;
        case 9 :
            $orderBy = ' ORDER BY m.media_views ASC';
            break;
        case 10 :
            $orderBy = ' ORDER BY m.media_title DESC';
            break;
        case 11 :
            $orderBy = ' ORDER BY m.media_title ASC';
            break;

        default :
            $orderBy = ' ORDER BY ma.media_order DESC';
            break;
    }
    return $orderBy;
}


function MG_siteHeader($title='', $meta='')
{
    global $_MG_CONF;

    switch($_MG_CONF['displayblocks']) {
        case 0 : // left only
        case 2 :
            return COM_siteHeader('menu',$title,$meta);
            break;
        case 1 : // right only
        case 3 :
            return COM_siteHeader('none',$title,$meta);
            break;
        default :
            return COM_siteHeader('menu',$title,$meta);
            break;
    }
}

function MG_siteFooter()
{
    global $_CONF, $_MG_CONF;

    $retval = '';
    if (!$_MG_CONF['hide_poweredby_link']) {
        $retval = '<br' . XHTML . '><div style="text-align:center;"><a href="http://www.glfusion.org"><img src="'
                . MG_getImageFile('powerby_mg.png') . '" alt="" style="border:none;"' . XHTML . '></a></div><br' . XHTML . '>';
    }
    switch($_MG_CONF['displayblocks']) {
        case 0 : // left only
        case 3 : // none
            $retval .= COM_siteFooter();
            break;
        case 1 : // right only
        case 2 : // left and right
            $retval .= COM_siteFooter(true);
            break;
        default :
            $retval .= COM_siteFooter();
            break;
    }

    // DEBUG
//    if ( function_exists('xdebug_peak_memory_usage') ) {
//        $retval .= '<br>Peak Memory: ' . (xdebug_peak_memory_usage() / 1024) / 1024 . ' mb';
//    }

    return $retval;
}

function MG_createHTMLDocument(&$content, $information = array()/*$title='', $meta=''*/)
{
    global $_MG_CONF;
/*
    $information = array(
        'pagetitle'  => $title,
        'headercode' => $meta
    );
*/
    if (!isset($information['what'])) {
        switch ($_MG_CONF['displayblocks']) {
            case 0 : // left only
                $information['what'] = 'menu';
                break;
            case 1 : // right only
                $information['what'] = 'none';
                break;
            case 2 : // left and right
                $information['what'] = 'menu';
                break;
            case 3 : // none
                $information['what'] = 'none';
                break;
            default :
                $information['what'] = 'menu';
                break;
        }
    }

    if (!isset($information['rightblock'])) {
        switch ($_MG_CONF['displayblocks']) {
            case 0 : // left only
                $information['rightblock'] = false;
                break;
            case 1 : // right only
                $information['rightblock'] = true;
                break;
            case 2 : // left and right
                $information['rightblock'] = true;
                break;
            case 3 : // none
                $information['rightblock'] = false;
                break;
            default :
                $information['rightblock'] = false;
                break;
        }
    }

    // DEBUG
//    if (function_exists('xdebug_peak_memory_usage')) {
//        $content .= '<br>Peak Memory: ' . (xdebug_peak_memory_usage() / 1024) / 1024 . ' mb';
//    }

    if (function_exists('COM_createHTMLDocument')) {
        return COM_createHTMLDocument($content, $information);
    }

    // Retrieve required variables from information array
    $what = $information['what'];
    $rightblock = $information['rightblock'];
    $pagetitle = '';
    if (isset($information['pagetitle'])) {
        $pagetitle = $information['pagetitle'];
    }
    $headercode = '';
    if (isset($information['headercode'])) {
        $headercode = $information['headercode'];
    }
    $custom = '';
    if (isset($information['custom'])) {
        $custom = $information['custom'];
    }

    return COM_siteHeader($what, $pagetitle, $headercode) . $content
         . COM_siteFooter($rightblock, $custom);
}

function MG_quotaUsage( $uid ) {
    global $_MG_CONF, $_TABLES;

    $quota = 0;
    $result = DB_query("SELECT album_disk_usage FROM {$_TABLES['mg_albums']} WHERE owner_id=" . intval($uid));
    while ($A=DB_fetchArray($result)) {
        $quota += $A['album_disk_usage'];
    }
    return $quota;
}

function MG_getUserQuota( $uid ) {
    global $_TABLES;

    $result = DB_query("SELECT quota FROM {$_TABLES['mg_userprefs']} WHERE uid=" . intval($uid));
    $nRows  = DB_numRows($result);
    if ( $nRows > 0 ) {
        $row = DB_fetchArray($result);
        return ($row['quota']);
    }
    return 0;
}

function MG_getUserActive( $uid ) {
    global $_TABLES;

    $result = DB_query("SELECT active FROM {$_TABLES['mg_userprefs']} WHERE uid=" . intval($uid));
    $nRows  = DB_numRows($result);
    if ( $nRows > 0 ) {
        $row = DB_fetchArray($result);
        return ($row['active']);
    }
    return 0;
}


function MG_usage( $application, $album_title, $media_title, $media_id ) {
    global $_MG_CONF, $_USER, $_TABLES, $REMOTE_ADDR;

    if ( !$_MG_CONF['usage_tracking'] ) {
        return;
    }

    $now = time();
    if ( $now - $_MG_CONF['last_usage_purge'] > 5184000) {
        $purgetime = $now - 5184000; // 60 days
        DB_query("DELETE FROM {$_TABLES['mg_usage_tracking']} WHERE time < " . $purgetime);
        DB_save($_TABLES['mg_config'],'config_name,config_value',"'last_usage_purge','$now'");
        COM_errorLog("Media Gallery: Purged old data from Usage Tracking Tables");
    }

    $log_time = $now;
    $user_id  = intval($_USER['uid']);
    $user_ip  = addslashes($REMOTE_ADDR);
    $user_name = addslashes($_USER['username']);

    $title  = addslashes($album_title);
    $ititle = addslashes($media_title);

    $sql = "INSERT INTO " . $_TABLES['mg_usage_tracking'] .
            " (time,user_id,user_ip, user_name,application, album_title, media_title,media_id)
              VALUES ($log_time, $user_id, '$user_ip', '$user_name', '$application', '$title', '$ititle', '$media_id')";

    DB_query( $sql );
}

function MG_errorHandler( $message ) {
    global $_CONF, $LANG_MG02;

    $retval =  '<div style="width:90%;border:1px solid;padding:8px;margin-top:8px;text-align:center;">' . LB;
    $retval .= '  <span style="text-align:center;font-weight:bold;">' . $LANG_MG02['error_header'] . '</span>' . LB;
    $retval .= '  <br' . XHTML . '>' . LB;
    $retval .= '  <br' . XHTML . '>' . LB;
    $retval .= '  <span style="text-align:center;font-weight:bold;">' . $LANG_MG02['error'] . '</span>' . LB;
    $retval .= $message . LB;
    $retval .= '  <br' . XHTML . '>' . LB;
    $retval .= '  <br' . XHTML . '>' . LB;
    $retval .= '  [ <a href=\'javascript:history.go(-1)\'>' . $LANG_MG02['go_back'] . '</a> ]' . LB;
    $retval .= '  <br' . XHTML . '>' . LB;
    $retval .= '  <br' . XHTML . '>' . LB;
    $retval .= '</div>' . LB;

    return $retval;
}


//hacked COM_getUserDateTimeFormat to allow different format for Media Gallery

function MG_getUserDateTimeFormat($date = '') {
    global $_TABLES, $_CONF, $_MG_CONF, $_SYSTEM;

    if ($date == '99') return '';

    // Get display format for time
    $dateformat = ($_MG_CONF['dfid'] == '0') ? $_CONF['date'] : $_MG_CONF['dateformat'];
    if (empty($date)) {
        // Date is empty, get current date/time
        $stamp = time();
    } elseif (is_numeric($date)) {
        // This is a timestamp
        $stamp = $date;
    } else {
        // This is a string representation of a date/time
        $stamp = strtotime($date);
    }

    // Format the date
    $date = strftime($dateformat, $stamp);
    if ($_SYSTEM['swedish_date_hack'] == true && function_exists('iconv')) {
        $date = iconv('ISO-8859-1', 'UTF-8', $date);
    }

    return array( $date, $stamp );
}

function MG_genericError($errorMessage)
{
    global $_MG_CONF, $_CONF, $LANG_MG02;

    $display .= COM_startBlock ($LANG_MG02['error_header'], '',COM_getBlockTemplate ('_admin_block', 'header'));
    $T = new Template($_MG_CONF['template_path']);
    $T->set_file('admin','error.thtml');
    $T->set_var('errormessage',$errorMessage);
    $T->parse('output', 'admin');
    $display .= $T->finish($T->get_var('output'));
    $display .= COM_endBlock (COM_getBlockTemplate ('_admin_block', 'footer'));
    return $display;
}

function MG_replace_accents($str)
{
    $str = htmlentities($str, ENT_QUOTES, COM_getEncodingt());
    $str = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde);/','$1',$str);
    return html_entity_decode($str);
}

function MG_getImageFile($image)
{
    global $_MG_CONF, $_CONF;

    if ($_MG_CONF['template_path'] == $_CONF['path'] . 'plugins/mediagallery/templates') {
        return $_MG_CONF['site_url'] . '/images/' . $image;
    }
    return $_CONF['layout_url'] . '/mediagallery/images/' . $image;
}

function MG_getImageFilePath($image)
{
    global $_MG_CONF, $_CONF;

    if ($_MG_CONF['template_path'] == $_CONF['path'] . 'plugins/mediagallery/templates') {
        return $_MG_CONF['path_html'] . 'images/' . $image;
    }
    return $_CONF['layout_path'] . '/mediagallery/images/' . $image;
}

function MG_getTemplatePath( $aid, $path = '')
{
    global $MG_albums, $_MG_CONF, $_CONF;

    if ( $MG_albums[$aid]->skin == 'default' || $MG_albums[$aid]->skin == '' ) {
        return $_MG_CONF['template_path'];
    }
    return (array($path != '' ? $path : '', $_MG_CONF['template_path'] . '/themes/' . $MG_albums[$aid]->skin, $_MG_CONF['template_path']));
}

function MG_getTemplatePath_byName($name = 'default')
{
    global $MG_albums, $_MG_CONF;

    $skin = $MG_albums[0]->skin;
    if (file_exists($_MG_CONF['template_path'] . '/themes/' . $name)) {
        $skin = $name;
    }
    return (array($path != '' ? $path : '', $_MG_CONF['template_path'] . '/themes/' . $skin, $_MG_CONF['template_path']));
}

function MG_getThemeCSS(&$skin)
{
    global $_MG_CONF;

    if ($skin == 'default' || $skin == '') return '';
    $retval = '';
    if (file_exists($_MG_CONF['template_path'] . '/themes/' . $skin . '/javascript.js')) {
        $retval .= '<script type="text/javascript" src="' . $_MG_CONF['site_url']
                 . '/mgjs.php?theme=' . $skin . '"></script>' . LB;
    }
    if (file_exists($_MG_CONF['template_path'] . '/themes/' . $skin . '/style.css')) {
        $retval .= '<link rel="stylesheet" type="text/css" href="' . $_MG_CONF['site_url']
                 . '/mgcss.php?theme=' . $skin . '"'.XHTML.'>' . LB;
    }
    return $retval;
}

function MG_getThemePublicJSandCSS(&$skin)
{
    global $_MG_CONF, $_SCRIPTS;

    if ($skin == 'default' || $skin == '')  return '';
    $retval = '';
    $f_gl18orlater = function_exists('COM_versionCompare');
    if ($f_gl18orlater) {
        if (file_exists($_MG_CONF['path_html'] . 'themes/' . $skin . '/javascript.js')) {
            $_SCRIPTS->setJavaScriptFile($skin, '/mediagallery/themes/' . $skin . '/javascript.js');
        }
    } else {
        if (file_exists($_MG_CONF['path_html'] . 'themes/' . $skin . '/javascript.js')) {
            $retval .= '<script type="text/javascript" src="' . $_MG_CONF['site_url']
                     . '/themes/' . $skin . '/javascript.js"></script>' . LB;
        }
    }
    if (file_exists($_MG_CONF['path_html'] . 'themes/' . $skin . '/style.css')) {
        $retval .= '<link rel="stylesheet" type="text/css" href="' . $_MG_CONF['site_url']
                 . '/themes/' . $skin . '/style.css"'.XHTML.'>' . LB;
    }
    return $retval;
}

function MG_getThemes() {
	global $_MG_CONF, $_CONF;

    $index = 1;

    $skins = array();

    $skins[0] = 'default';

    $fd = opendir( $_MG_CONF['template_path'] . '/themes/' );

    while(( $dir = @readdir( $fd )) == TRUE )
    {
        if( is_dir( $_MG_CONF['template_path'] . '/themes/' . $dir) && $dir <> '.' && $dir <> '..' && $dir <> 'CVS' && substr( $dir, 0 , 1 ) <> '.' )
        {
            clearstatcache();
            $skins[$index] = $dir;
            $index++;
        }
    }

    return $skins;
}

function MG_get_size($size) {
$bytes = array('B','KB','MB','GB','TB');
  foreach($bytes as $val) {
   if($size > 1024){
    $size = $size / 1024;
   }else{
    break;
   }
  }
  return round($size, 2)." ".$val;
}

/**
* Get the path of the feed directory or a specific feed file
*
* @param    string  $feedfile   (option) feed file name
* @return   string              path of feed directory or file
*
*/
function MG_getFeedPath( $feedfile = '' )
{
    global $_CONF;

    $feedpath = $_CONF['rdf_file'];
    $pos = strrpos( $feedpath, '/' );
    $feed = substr( $feedpath, 0, $pos + 1 );
    $feed .= $feedfile;

    return $feed;
}

/**
* Get the URL of the feed directory or a specific feed file
*
* @param    string  $feedfile   (option) feed file name
* @return   string              URL of feed directory or file
*
*/
function MG_getFeedUrl( $feedfile = '' )
{
    global $_CONF;

    $feedpath = SYND_getFeedPath();
    $url = substr_replace ($feedpath, $_CONF['site_url'], 0,
                           strlen ($_CONF['path_html']) - 1);
    $url .= $feedfile;

    return $url;
}

/**
* Convert k/m/g size string to number of bytes
*
* @param    string      val    a string expressing size in K, M or G
* @return   int                 the resultant value in bytes
*
*/
function MG_return_bytes($val) {
   $val  = trim($val);
   $last = strtolower($val{strlen($val)-1});
   switch($last) {
       // The 'G' modifier is available since PHP 5.1.0
       case 'g':
           $val *= 1024;
       case 'm':
           $val *= 1024;
       case 'k':
           $val *= 1024;
   }
   return $val;
}

/**
* Return the max upload file size for the specified album
*
* @param    intval      album_id        the album_id to return the max upload file size for
* @return   intval      upload_limit    the upload size imit, in bytes
*
* if the type cannot be determined from the extension because the extension is
* not known, then the default value is returned (even if null)
*
* NOTE: the album array must be pre-initialized via MG_AlbumsInit()
*
*/
function MG_getUploadLimit( $album_id ) {

    global $MG_albums;

    $post_max = MG_return_bytes( ini_get( 'post_max_size' ) );
    $album_max = $MG_albums[$album_id]->max_filesize;
    if( $album_max > 0 && $album_max < $post_max ) {
        return $album_max;
    } else {
        return $post_max;
    }
}

/**
* Return a string of valid upload filetypes for the specified album
*
* @param    intval      album_id        the album_id to return the max upload file size for
* @return   string      valid_types     string of filetypes allowed, delimited by semicolons
*
* if the type cannot be determined from the extension because the extension is
* not known, then the default value is returned (even if null)
*
* NOTE: the album array must be pre-initialized via MG_AlbumsInit()
*
*/
function MG_getValidFileTypes( $album_id ) {

    global $MG_albums;

    $valid_formats = $MG_albums[$album_id]->valid_formats;
    if( $valid_formats & MG_OTHER ) {
        $valid_types = '*.*';
    } else {
        $valid_types = '';
        $valid_types .= ( $valid_formats & MG_JPG ) ? '*.jpg; ' : '';
        $valid_types .= ( $valid_formats & MG_PNG ) ? '*.png; ' : '';
        $valid_types .= ( $valid_formats & MG_TIF ) ? '*.tif; ' : '';
        $valid_types .= ( $valid_formats & MG_GIF ) ? '*.gif; ' : '';
        $valid_types .= ( $valid_formats & MG_BMP ) ? '*.bmp; ' : '';
        $valid_types .= ( $valid_formats & MG_TGA ) ? '*.tga; ' : '';
        $valid_types .= ( $valid_formats & MG_PSD ) ? '*.psd; ' : '';
        $valid_types .= ( $valid_formats & MG_MP3 ) ? '*.mp3; ' : '';
        $valid_types .= ( $valid_formats & MG_OGG ) ? '*.ogg; ' : '';
        $valid_types .= ( $valid_formats & MG_ASF ) ? '*.asf; *.wma; *.wmv; ' : '';
        $valid_types .= ( $valid_formats & MG_SWF ) ? '*.swf; ' : '';
        $valid_types .= ( $valid_formats & MG_MOV ) ? '*.mov; *.qt; ' : '';
        $valid_types .= ( $valid_formats & MG_MP4 ) ? '*.mp4; ' : '';
        $valid_types .= ( $valid_formats & MG_MPG ) ? '*.mpg; ' : '';
        $valid_types .= ( $valid_formats & MG_FLV ) ? '*.flv; ' : '';
        $valid_types .= ( $valid_formats & MG_ZIP ) ? '*.zip; ' : '';
        $valid_types .= ( $valid_formats & MG_PDF ) ? '*.pdf; ' : '';
        $valid_types = substr( $valid_types, 0, strlen($valid_types)-1 );
    }
    return $valid_types;
}

/**
* Get a valid encoding for htmlspecialchars()
*
* @return   string      character set, e.g. 'utf-8'
*
*

function COM_getEncodingt() {
	global $_CONF, $LANG_CHARSET;
	
	static $encoding = null;
	
	if ($encoding === null) {
		if (isset($LANG_CHARSET)) {
			$encoding = $LANG_CHARSET;
		} else if (isset($_CONF['default_charset'])) {
			$encoding = $_CONF['default_charset'];
		} else {
			$encoding = 'iso-8859-1';
		}
	}
	
	return $encoding;
}
*/
/**
* Escapes a string for HTML output
*/
function MG_escape($str) {
	$str = str_replace(
		array('&lt;', '&gt;', '&amp;', '&quot;', '&#039;'),
		array(   '<',    '>',     '&',      '"',      "'"),
		$str
	);
	return htmlspecialchars($str, ENT_QUOTES, COM_getEncodingt());
}

function MG_templateInstance($path)
{
    global $_CONF;
    require_once $_CONF['path'] . 'plugins/mediagallery/include/kz_template.class.php';
    return new KZ_Template($path);
}

function MG_getThumbCropPath($path)
{
    $p = pathinfo($path);
    return $p['dirname'] . '/' . $p['filename'] . '_150x150.' . $p['extension'];
}

function MG_getMediaExt($path_and_filename)
{
    global $_MG_CONF;

    $ext = '';
    foreach ($_MG_CONF['validExtensions'] as $ext) {
        if (file_exists($path_and_filename . $ext)) break;
    }
    return $ext;
}

?>