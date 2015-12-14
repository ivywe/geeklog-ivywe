<?php
// +--------------------------------------------------------------------------+
// | Media Gallery Plugin - glFusion CMS                                      |
// +--------------------------------------------------------------------------+
// | profile.php                                                              |
// |                                                                          |
// +--------------------------------------------------------------------------+
// | $Id:: lib-media.php 3093 2008-09-10 23:53:51Z mevans0263                $|
// +--------------------------------------------------------------------------+
// | Copyright (C) 2002-2008 by the following authors:                        |
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


// display user info in profile

function MG_profileblocksdisplay( $uid )
{
    global $MG_albums,$_TABLES, $_MG_CONF, $_CONF, $LANG_MG10, $_USER;

    $retval = '';

    if ( $_MG_CONF['profile_hook'] != 1 ) {
        return '';
    }

    if ( (!isset($_USER['uid']) || $_USER['uid'] < 2) && $_MG_CONF['loginrequired'] == 1) {
        return '';
    }

    if ( $uid == '' ) {
        return '';
    }

    $template = MG_templateInstance( MG_getTemplatePath(0) );
    $template->set_file(array(
        'mblock' => 'mediablock.thtml',
        'mrow'   => 'mediarow.thtml',
    ));

    $username = DB_getItem($_TABLES['users'], 'username', 'uid=' . intval($uid));
    if ( $username == '' ) {
        return '';
    }

    $template->set_var('start_block_last10mediaitems', COM_startBlock($LANG_MG10['last_10'] . $username));
    $template->set_var('start_block_useralbums', COM_startBlock($LANG_MG10['albums_owned'] . $username));
    $template->set_var('lang_thumbnail', $LANG_MG10['thumbnail']);
    $template->set_var('lang_title', $LANG_MG10['title']);
    $template->set_var('lang_album', $LANG_MG10['album']);
    $template->set_var('lang_album_description', $LANG_MG10['album_desc']);
    $template->set_var('lang_upload_date', $LANG_MG10['upload_date']);
    $template->set_var('end_block', COM_endBlock());

    $class = 0;

    $sql = "SELECT a.album_id,m.media_upload_time,m.media_id,m.media_filename,m.mime_type,m.media_mime_ext,m.media_title,m.remote_media,m.media_type FROM {$_TABLES['mg_albums']} as a LEFT JOIN {$_TABLES['mg_media_albums']} as ma
            on a.album_id=ma.album_id LEFT JOIN {$_TABLES['mg_media']} as m on ma.media_id=m.media_id WHERE
            m.media_user_id=" . intval($uid) . " AND a.hidden=0 " . COM_getPermSQL('and') . " ORDER BY m.media_upload_time DESC LIMIT 5";

    $result = DB_query($sql);
    $mCount = 0;
    while ( $row = DB_fetchArray($result)) {
        $album_id = $row['album_id'];
        $album_title = strip_tags($MG_albums[$album_id]->title);
        $upload_time = MG_getUserDateTimeFormat($row['media_upload_time']);
        $url_media = $_MG_CONF['site_url'] . '/media.php?s=' . $row['media_id'];
        $url_album = $_MG_CONF['site_url'] . '/album.php?aid=' . $album_id;

        switch( $row['media_type'] ) {
            case 0 :    // standard image
                $msize = false;
                foreach ($_MG_CONF['validExtensions'] as $ext ) {
                    if ( file_exists($_MG_CONF['path_mediaobjects'] . 'tn/' . $row['media_filename'][0] . '/' . $row['media_filename'] . $ext) ) {
                        $url_thumb = $_MG_CONF['mediaobjects_url'] . '/tn/' . $row['media_filename'][0] . '/' . $row['media_filename'] . $ext;
                        $msize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'disp/' . $row['media_filename'][0] .'/' . $row['media_filename'] . $ext);
                        break;
                    }
                }
                break;
            case 1 :    // video file
                switch ( $row['mime_type'] ) {
                    case 'application/x-shockwave-flash' :
                        $url_thumb = $_MG_CONF['mediaobjects_url'] . '/flash.png';
                        $msize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'flash.png');
                        break;
                    case 'video/quicktime' :
                    case 'video/mpeg' :
                    case 'video/x-m4v' :
                        $url_thumb = $_MG_CONF['mediaobjects_url'] . '/quicktime.png';
                        $msize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'quicktime.png');
                        break;
                    case 'video/x-ms-asf' :
                    case 'video/x-ms-wvx' :
                    case 'video/x-ms-wm' :
                    case 'video/x-ms-wmx' :
                    case 'video/x-msvideo' :
                    case 'application/x-ms-wmz' :
                    case 'application/x-ms-wmd' :
                        $url_thumb = $_MG_CONF['mediaobjects_url'] . '/wmp.png';
                        $msize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'wmp.png');
                        break;
                    default :
                        $url_thumb = $_MG_CONF['mediaobjects_url'] . '/video.png';
                        $msize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'video.png');
                        break;
                }
                break;
            case 2 :    // music file
                $url_thumb = $_MG_CONF['mediaobjects_url'] . '/audio.png';
                $msize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'audio.png');
                break;
            case 4 :    // other files
                switch ($row['media_mime_ext']) {
                    case 'zip' :
                    case 'arj' :
                    case 'rar' :
                    case 'gz'  :
                        $url_thumb = $_MG_CONF['mediaobjects_url'] . '/zip.png';
                        $msize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'zip.png');
                        break;
                    case 'pdf' :
                        $url_thumb = $_MG_CONF['mediaobjects_url'] . '/pdf.png';
                        $msize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'pdf.png');
                        break;
                    default :
                        $url_thumb = $_MG_CONF['mediaobjects_url'] . '/generic.png';
                        $msize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'generic.png');
                        break;
                }
                break;
            case 5 :
                $url_thumb = $_MG_CONF['mediaobjects_url'] . '/remote.png';
                $msize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'remote.png');
                break;
        }

        if ($msize == false ) {
            $url_thumb = $_MG_CONF['mediaobjects_url'] . '/missing.png';
            $msize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'missing.png');
        }
        $imgwidth = $msize[0];
        $imgheight = $msize[1];

        if ( $imgwidth > $imgheight ) {
            $ratio = $imgwidth / 120;
            $width = 120;
            $height = round($imgheight / $ratio);
        } else {
            $ratio = $imgheight / 120;
            $height = 120;
            $width = round($imgwidth / $ratio);
        }

        $template->set_var('mediaitem_image', '<img src="' . $url_thumb . '" alt="" style="width:' . $width . 'px;height:' . $height . 'px;border:none;vertical-align:bottom;"' . XHTML . '>');
        $template->set_var('mediaitem_begin_href', '<a href="' . $url_media . '">');
        $template->set_var('mediaitem_title', strip_tags($row['media_title']));
        $template->set_var('mediaitem_end_href', '</a>');
        $template->set_var('mediaitem_album_begin_href', '<a href="' . $url_album . '">');
        $template->set_var('mediaitem_album_title', $album_title);
        $template->set_var('mediaitem_date', $upload_time[0]);
        $template->set_var('rowclass', ($class % 2) ? '1' : '2');
        $template->parse('mediaitem_row', 'mrow', true);
        $class++;
        $mCount++;
    }
    // end of media block
    $template->parse('output', 'mblock', true);
    if ( $mCount != 0 ) {
        $retval .= $template->finish ($template->get_var('output'));
    }

    $template = MG_templateInstance( MG_getTemplatePath(0) );
    $template->set_file(array(
        'mblock' => 'albumblock.thtml',
        'arow'   => 'albumrow.thtml'
    ));

    $template->set_var('start_block_useralbums', COM_startBlock($LANG_MG10['albums_owned'] . $username));
    $template->set_var('lang_thumbnail', $LANG_MG10['thumbnail']);
    $template->set_var('lang_album', $LANG_MG10['album']);
    $template->set_var('lang_album_description', $LANG_MG10['album_desc']);
    $template->set_var('end_block', COM_endBlock());

    $sql = "SELECT album_id,album_title,album_desc,tn_attached "
         . "FROM " . $_TABLES['mg_albums']
         . " WHERE owner_id=" . intval($uid) . " AND hidden=0 " . COM_getPermSQL('and') . " ORDER BY last_update DESC LIMIT 10";

    $result = DB_query($sql);
    $aCount = 0;
    while ($row = DB_fetchArray($result)) {
        $aid        = $row['album_id'];
        $url_album  = $_MG_CONF['site_url'] . '/album.php?aid=' . $row['album_id'];

        $url_thumb = '';
        $msize = false;
        if ( $row['tn_attached'] == 1 ) {
            $msize = false;
            foreach ($_MG_CONF['validExtensions'] as $ext ) {
                if ( file_exists($_MG_CONF['path_mediaobjects'] . 'covers/cover_' . $row['album_id'] . $ext) ) {
	                $url_thumb = $_MG_CONF['mediaobjects_url'] . '/covers/cover_' . $row['album_id'] . $ext;
                    $msize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'covers/cover_' . $row['album_id'] . $ext);
                    break;
                }
            }
        } else {
            $cover_file = $MG_albums[$aid]->findCover();
            if ( $cover_file != '' ) {
                if ( substr($cover_file,0,3) == 'tn_' ) {
                    $offset = 3;
                } else {
                    $offset = 0;
                }
                $msize = false;
                foreach ($_MG_CONF['validExtensions'] as $ext ) {
                    if ( file_exists($_MG_CONF['path_mediaobjects'] . 'tn/' . $cover_file[$offset] .'/' . $cover_file . $ext) ) {
                        $url_thumb = $_MG_CONF['mediaobjects_url'] . '/tn/' . $cover_file[$offset] .'/' . $cover_file . $ext;
                        $msize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'tn/' . $cover_file[$offset] .'/' . $cover_file . $ext);
                        break;
                    }
                }
            }
        }

        if ($msize == false || $url_thumb == '' ) {
            $url_thumb = $_MG_CONF['mediaobjects_url'] . '/empty.png';
            $msize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'empty.png');
        }
        $imgwidth = $msize[0];
        $imgheight = $msize[1];

        if ( $imgwidth == 0 || $imgheight == 0 ) {
            $url_thumb = $_MG_CONF['mediaobjects_url'] . '/empty.png';
            $msize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'empty.png');
            $imgwidth = $msize[0];
            $imgheight = $msize[1];
            if ( $imgwidth == 0 || $imgheight == 0 ) {
                continue;
            }
        }

        if ( $imgwidth > $imgheight ) {
            $ratio = $imgwidth / 120;
            $width = 120;
            $height = round($imgheight / $ratio);
        } else {
            $ratio = $imgheight / 120;
            $height = 120;
            $width = round($imgwidth / $ratio);
        }
        $template->set_var('album_cover', '<img src="' . $url_thumb . '" alt="" style="width:' . $width . 'px;height:' . $height . 'px;border:none;vertical-align:bottom;"' . XHTML . '>');
        $template->set_var('album_begin_href', '<a href="' . $url_album . '">');
        $template->set_var('album_title', strip_tags($row['album_title']));
        $template->set_var('album_end_href', '</a>');
        $template->set_var('album_desc', strip_tags($row['album_desc']));
        $template->set_var('rowclass', ($class % 2) ? '1' : '2');
        $template->parse('useralbum_row', 'arow', true);
        $class++;
        $aCount++;
    }
    $template->parse('output', 'mblock', true);
    if ( $aCount != 0 ) {
        $retval .= $template->finish ($template->get_var('output'));
    }
    return $retval;
}
