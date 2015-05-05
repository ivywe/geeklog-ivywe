<?php
// +--------------------------------------------------------------------------+
// | Media Gallery Plugin - glFusion CMS                                      |
// +--------------------------------------------------------------------------+
// | media.php                                                                |
// |                                                                          |
// | Handles the display of various media types                               |
// +--------------------------------------------------------------------------+
// | $Id:: media.php 5614 2010-03-19 19:08:33Z mevans0263                    $|
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

global $_MG_CONF, $_USER, $LANG_LOGIN;

require_once '../lib-common.php';

if (!in_array('mediagallery', $_PLUGINS)) {
    echo COM_refresh($_CONF['site_url'] . '/index.php');
    exit;
}

if ( COM_isAnonUser() && $_MG_CONF['loginrequired'] == 1 )  {
    $display = SEC_loginRequiredForm();
    $display = MG_createHTMLDocument($display);
    echo $display;
    exit;
}

MG_initAlbums();

require_once $_CONF['path'].'plugins/mediagallery/include/lib-media.php';

/*
* Main Function
*/

$display = '';

if ( isset($_REQUEST['msg']) ) {
    $msg = COM_applyFilter($_REQUEST['msg'],true);
} else {
    $msg = '';
}

if (isset($_REQUEST['aid'])) {
    $album_id = COM_applyFilter($_REQUEST['aid']);
} else {
    $album_id = 0;
}

if ( isset($_REQUEST['f'])) {
    $full = COM_applyFilter($_REQUEST['f'],true);
} else {
    $full = 0;
}

if ( isset($_REQUEST['s'])) {
    $mediaObject = COM_applyFilter($_REQUEST['s'],true);
} else {
    $mediaObject = 0;
}

if ( isset($_REQUEST['sort'])) {
    $sortOrder = COM_applyFilter($_REQUEST['sort'],true);
} else {
    $sortOrder = 0;
}

if ( isset($_REQUEST['i'])) {
    $sortID = COM_applyFilter($_REQUEST['i'],true);
} else {
    $sortID = 0;
}

if ( isset($_REQUEST['p'])) {
    $page = COM_applyFilter($_REQUEST['p'],true);
} else {
    $page = 0;
}

list($ptitle, $content, $themeCSS, $album_id) = MG_displayMediaImage($mediaObject, $full, $sortOrder, 1, $sortID, $page);
$themeStyle .= MG_getThemePublicJSandCSS($MG_albums[$album_id]->skin); // Quicker than MG_getThemeCSS
$themeStyle .= MG_getThemeCSS($MG_albums[$album_id]->skin);

$themeStyle .= $themeCSS;

if ( $msg != '' ) {
    $display .= COM_showMessage($msg, 'mediagallery');
}

$display .= $content;
$display = MG_createHTMLDocument($display, array('pagetitle' => $ptitle));

echo $display;
?>