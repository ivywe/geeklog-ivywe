<?php
// +--------------------------------------------------------------------------+
// | Media Gallery Plugin - glFusion CMS                                      |
// +--------------------------------------------------------------------------+
// | albim_items.php                                                          |
// |                                                                          |
// +--------------------------------------------------------------------------+
// | $Id:: lightbox.php 4919 2009-09-13 17:49:49Z mevans0263                 $|
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

require_once '../lib-common.php';

if (!in_array('mediagallery', $_PLUGINS)) {
    echo COM_refresh($_CONF['site_url'] . '/index.php');
    exit;
}

MG_initAlbums();

function MG_getItems ($mode='sv')
{
    global $MG_albums, $_TABLES, $_MG_CONF;

    $retval = '';

    if ( isset($_REQUEST['aid']) ) {
        $aid = COM_applyFilter($_REQUEST['aid'],true);
    } else {
        $aid = 0;
    }
    if ( isset($_REQUEST['src']) ) {
        $src = COM_applyFilter($_REQUEST['src']);
    } else {
        $src = 'disp';
    }
    if ( isset($_REQUEST['type']) ) {
        $type = COM_applyFilter($_REQUEST['type']);
    } else {
        $type = 'mini';
    }

    if ( $src != 'disp' && $src != 'orig' ) {
        $src = 'tn';
    }
    if ( $type != 'full' || $type != 'mini' ) {
        $type = 'mini';
    }

    if ( isset($MG_albums[$aid]->id ) ) {
        if ( $MG_albums[$aid]->access >= 1 ) {
            $orderBy = MG_getSortOrder($aid, 0);

            $sql = "SELECT * FROM {$_TABLES['mg_media_albums']} as ma INNER JOIN " . $_TABLES['mg_media'] . " as m " .
                    " ON ma.media_id=m.media_id WHERE ma.album_id=" . intval($aid) . " AND m.include_ss=1 " . $orderBy;

            $result = DB_query( $sql );
            $nRows  = DB_numRows( $result );
            $encoding = COM_getEncodingt();
            $mediaRows = 0;
            if ( $nRows > 0 ) {
                while ( $row = DB_fetchArray($result)) {
                    if ( $row['media_type'] == 0 ) {
                        foreach ($_MG_CONF['validExtensions'] as $ext ) {
                            if ( file_exists($_MG_CONF['path_mediaobjects'] . $src . '/' . $row['media_filename'][0] . '/' . $row['media_filename'] . $ext) ) {
                                $PhotoURL  = $_MG_CONF['mediaobjects_url'] . '/' . $src . '/' . $row['media_filename'][0] .'/' . $row['media_filename'] . $ext;
                                $PhotoPath = $_MG_CONF['path_mediaobjects'] . $src . '/' . $row['media_filename'][0] .'/' . $row['media_filename'] . $ext;

                                $TnURL     = $_MG_CONF['mediaobjects_url'] . '/tn/' . $row['media_filename'][0] .'/' . $row['media_filename'] . $ext;
                                $TnCropURL = $_MG_CONF['mediaobjects_url'] . '/tn/' . $row['media_filename'][0] .'/' . $row['media_filename'] . '_150x150'. $ext;
                                break;
                            }
                        }
                        if ( $row['remote_url'] != '' ) {
                            $viewURL = $row['remote_url'];
                        } else {
                            $viewURL   = $_MG_CONF['site_url']  . "/media.php?s=" . $row['media_id'];
                        }
                        $imgsize   = @getimagesize($PhotoPath);
                        if ( $imgsize == false && $row['remote_media'] != 1) {
                            continue;
                        }
                        if ( $row['remote_media'] == 1 ) {
                            $PhotoURL = $row['remote_url'];
                        }
                        $retval .= '<item>'
                                 . '<url>' . $PhotoURL . '</url>'
                                 . '<tnurl>' . $TnURL . '</tnurl>'
                                 . '<tncropurl>' . $TnCropURL . '</tncropurl>'
                                 . '<title>' . htmlentities(strip_tags($row['media_title']), ENT_QUOTES, $encoding) . '</title>'
                                 . '<desc>' . htmlentities(strip_tags($row['media_desc']), ENT_QUOTES, $encoding) . '</desc>'
                                 . '</item>' . "\n";
                    }
                }
            }
        }
        return $retval;
    }
}

function MG_xml() {
    global $MG_albums,$_MG_CONF,$LANG_CHARSET;

    if ( isset($_REQUEST['aid']) ) {
        $aid = COM_applyFilter($_REQUEST['aid'],true);
    } else {
        $aid = 0;
    }
    $xml = '';
    header( "Content-type: text/xml" );
    $xml .= "<album>\n";
    $xml .= MG_getItems();
    $xml .= "</album>\n";
    echo $xml;
}
MG_xml();
?>