<?php
// +--------------------------------------------------------------------------+
// | Media Gallery Plugin - Geeklog                                           |
// +--------------------------------------------------------------------------+
// | albumedit.php                                                            |
// |                                                                          |
// | Album editing administration                                             |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2015 by the following authors:                             |
// |                                                                          |
// | Yoshinori Tahara       taharaxp AT gmail DOT com                         |
// |                                                                          |
// | Based on the Media Gallery Plugin for glFusion CMS                       |
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
* Shows security control for an object
*
* This will return the HTML needed to create the security control see on the admin
* screen for GL objects (i.e. stories, links, etc)
*
* @param        int     $perm_members   Permissions logged in members have
* @param        int     $perm_anon      Permissions anonymous users have
* @return       string  needed HTML (table) in HTML $perm_owner = array of permissions [edit,read], etc edit = 1 if permission, read = 2 if permission
*
*/
function MG_getMemberPermissionsHTML($perm_members, $perm_anon)
{
    global $LANG_ACCESS;

    $retval = '<table cellpadding="0" cellspacing="0" class="admin-list-smalltable">' . LB . '<tr>' . LB
            . '<th class="edit-perm-up admin-list-headerfield">' . $LANG_ACCESS['members'] . '</th>' . LB
            . '<th class="edit-perm-up admin-list-headerfield">' . $LANG_ACCESS['anonymous'] . '</th>' . LB
            . '</tr>' . LB . '<tr class="pluginRow1">' . LB;

    // Member Permissions
    $retval .= '<td class="edit-perm-down admin-list-field"><b>R</b><br' . XHTML . '>'
             . '<input type="checkbox" class="uk-checkbox" name="perm_members[]" value="2"';
    if ($perm_members == 2) {
        $retval .= ' checked="checked"';
    }
    $retval .= XHTML . '></td>' . LB;

    // Anonymous Permissions

    $retval .= '<td class="edit-perm-down admin-list-field"><b>R</b><br' . XHTML . '>'
             . '<input type="checkbox" class="uk-checkbox" name="perm_anon[]" value="2"';
    if ($perm_anon == 2) {
        $retval .= ' checked="checked"';
    }
    $retval .= XHTML . '></td>' . LB;

    // Finish off and return

    $retval .= '</tr>' . LB . '</table>' . LB;

    return $retval;
}

/**
* edits or creates an album
*
* @param    int     album_id    album_id to edit
* @param    string  mode        create or edit
* @param    string  actionURL   where to redirection on finish
* @param    int     oldaid      original album id
* @return   string              HTML
*
*/
function MG_editAlbum($mode ='', $actionURL='', $oldaid = 0)
{
    global $_USER, $_CONF, $_TABLES, $_MG_CONF, $LANG_MG00,
           $LANG_MG01, $LANG_MG03, $LANG_ACCESS;

    if ($actionURL == '') {
        $actionURL = $_CONF['site_admin_url']
            . '/plugins/mediagallery/index.php';
    }

    if ($oldaid > 0 && $mode == 'edit') {
        $album = new mgAlbum($oldaid);
        $album_id = $album->id;
    } else { // create
        $album = new mgAlbum();
        $album->id = -1;
        $album_id = -1;
    }

    $retval = '';

    $T = COM_newTemplate(MG_getTemplatePath($album_id));
    $T->set_file('admin', 'editalbum.thtml');

    if ($album_id != 0 && $mode == 'edit') {
        // If edit, pull up the existing album information...
        if ($album->access != 3) {
            COM_errorLog("MediaGallery: Someone has tried to illegally edit a Media Gallery Album. "
                       . "User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
            return COM_showMessageText($LANG_MG00['access_denied_msg']);
        }
    }

    $block_title = ($mode == 'create') ? $LANG_MG01['create_album'] : ($LANG_MG01['edit_album'] . ' - ' . strip_tags($album->title));

    // construct the album jumpbox
    $select = ($mode == 'create') ? $oldaid : $album->parent;
    $valid_albums = 0;
    $album_selectbox  = '<select class="uk-select uk-form-width-medium" name="parentaid">';
    $root_album = new mgAlbum(0);
    $valid_albums += $root_album->buildAlbumBox($album_selectbox, $select, 3, $album_id, $mode);
    $album_selectbox .= '</select>';
    if ($valid_albums == 0) {
        COM_errorLog("MediaGallery: Someone has tried to illegally create a Media Gallery Album. "
                   . "User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
        return COM_showMessageText($LANG_MG00['access_denied_msg']);
    }

    // build exif select box...
    $exif_select = MG_optionlist(array(
        'name'    => 'enable_exif',
        'current' => $album->exif_display,
        'values'  => array(
            '0' => $LANG_MG01['disable_exif'],
            '1' => $LANG_MG01['display_below_media'],
            '2' => $LANG_MG01['display_in_popup'],
            '3' => $LANG_MG01['both'],
        ),
    ));

    $full_select = MG_optionlist(array(
        'name'    => 'full_display',
        'current' => $album->full,
        'values'  => array(
            '0' => $LANG_MG01['always'],
            '1' => $LANG_MG01['members_only'],
            '2' => $LANG_MG01['disabled'],
        ),
        'disabled' => $_MG_CONF['discard_original'],
    ));

    $ranking_select = MG_optionlist(array(
        'name'    => 'enable_rating',
        'current' => $album->enable_rating,
        'values'  => array(
            '0' => $LANG_MG01['disabled'],
            '1' => $LANG_MG01['members_only'],
            '2' => $LANG_MG01['always'],
        ),
    ));

    $podcast_select = MG_checkbox(array(
        'name'    => 'podcast',
        'checked' => $album->podcast,
        'value'   => '1',
    ));

    $mp3ribbon_select = MG_checkbox(array(
        'name'    => 'mp3ribbon',
        'checked' => $album->mp3ribbon,
        'value'   => '1',
    ));

    $rsschildren_select = MG_checkbox(array(
        'name'    => 'rsschildren',
        'checked' => $album->rssChildren,
        'value'   => '1',
    ));

    $comment_select = MG_checkbox(array(
        'name'    => 'enable_comments',
        'checked' => $album->enable_comments,
        'value'   => '1',
    ));

    $ss_select = MG_optionlist(array(
        'name'    => 'enable_slideshow',
        'current' => $album->enable_slideshow,
        'values'  => array(
            '0' => $LANG_MG01['disabled'],
            '1' => $LANG_MG01['js_slideshow'],
            '2' => $LANG_MG01['lightbox'],
            '3' => $LANG_MG01['flash_slideshow_disp'],
            '4' => $LANG_MG01['flash_slideshow_full'],
            '5' => $LANG_MG01['mp3_jukebox'],
        ),
    ));

    $views_select = MG_checkbox(array(
        'name'    => 'enable_views',
        'checked' => $album->enable_views,
        'value'   => '1',
    ));

    $keywords_select = MG_checkbox(array(
        'name'    => 'enable_keywords',
        'checked' => $album->enable_keywords,
        'value'   => '1',
    ));

    $sort_select = MG_checkbox(array(
        'name'    => 'enable_sort',
        'checked' => $album->enable_sort,
        'value'   => '1',
    ));

    $rss_select = MG_checkbox(array(
        'name'    => 'enable_rss',
        'checked' => $album->enable_rss,
        'value'   => '1',
    ));

    $afirst_select = MG_checkbox(array(
        'name'    => 'albums_first',
        'checked' => $album->albums_first,
        'value'   => '1',
    ));

    $usealternate_select = MG_checkbox(array(
        'name'    => 'usealternate',
        'checked' => $album->useAlternate,
        'value'   => '1',
    ));

    $album_views_select = MG_checkbox(array(
        'name'    => 'enable_album_views',
        'checked' => $album->enable_album_views,
        'value'   => '1',
    ));

    $display_album_desc_select = MG_checkbox(array(
        'name'    => 'display_album_desc',
        'checked' => $album->display_album_desc,
        'value'   => '1',
    ));

    $tn_size_select = MG_optionlist(array(
        'name'    => 'tn_size',
        'current' => $album->tn_size,
        'values'  => array(
            '0'  => $LANG_MG01['include_small'],
            '1'  => $LANG_MG01['include_medium'],
            '2'  => $LANG_MG01['include_large'],
            '3'  => $LANG_MG01['include_custom'],
            '10' => $LANG_MG01['crop_small'],
            '11' => $LANG_MG01['crop_medium'],
            '12' => $LANG_MG01['crop_large'],
            '13' => $LANG_MG01['crop_custom'],
        ),
    ));

    $display_image_size_select = MG_optionlist(array(
        'name'    => 'display_image_size',
        'current' => $album->display_image_size,
        'values'  => array(
            '0' => $LANG_MG01['size_500x375'],
            '1' => $LANG_MG01['size_600x450'],
            '2' => $LANG_MG01['size_620x465'],
            '3' => $LANG_MG01['size_720x540'],
            '4' => $LANG_MG01['size_800x600'],
            '5' => $LANG_MG01['size_900x600'],
            '6' => $LANG_MG01['size_1024x768'],
            '7' => $LANG_MG01['size_1152x864'],
            '8' => $LANG_MG01['size_2400x1600'],
            '9' => $LANG_MG01['size_custom']
                 . $_MG_CONF['custom_image_width'] . 'x'
                 . $_MG_CONF['custom_image_height'],
        ),
    ));

    $rows_input = MG_input(array(
        'type' => 'text',
        'size' => '3',
        'name' => 'display_rows',
        'value' => $album->display_rows,
    ));

    $columns_input = MG_input(array(
        'type' => 'text',
        'size' => '3',
        'name' => 'display_columns',
        'value' => $album->display_columns,
    ));

    $max_image_height_input = MG_input(array(
        'type' => 'text',
        'size' => '4',
        'name' => 'max_image_height',
        'value' => $album->max_image_height,
    ));

    $max_image_width_input = MG_input(array(
        'type' => 'text',
        'size' => '4',
        'name' => 'max_image_width',
        'value' => $album->max_image_width,
    ));

    $tnheight_input = MG_input(array(
        'type' => 'text',
        'size' => '3',
        'name' => 'tnheight',
        'value' => $album->tnHeight,
    ));

    $tnwidth_input = MG_input(array(
        'type' => 'text',
        'size' => '3',
        'name' => 'tnwidth',
        'value' => $album->tnWidth,
    ));

    $max_filesize = 0;
    if ($album->max_filesize != 0) {
        $max_filesize = $album->max_filesize / 1024;
    }
    $max_filesize_input = MG_input(array(
        'type' => 'text',
        'size' => '10',
        'name' => 'max_filesize',
        'value' => $max_filesize,
    ));

    $email_mod_select = MG_checkbox(array(
        'name'    => 'email_mod',
        'checked' => $album->email_mod,
        'value'   => '1',
    ));

    $playback_type = MG_optionlist(array(
        'name'    => 'playback_type',
        'current' => $album->playback_type,
        'values'  => array(
            '0' => $LANG_MG01['play_in_popup'],
            '1' => $LANG_MG01['download_to_local'],
            '2' => $LANG_MG01['play_inline'],
            '3' => $LANG_MG01['use_mms'],
        ),
    ));

    $themes = MG_getThemes();
    $album_theme_select = '<select class="uk-select uk-form-width-medium" name="album_theme">';
    for ($i = 0; $i < count($themes); $i++) {
        $album_theme_select .= '<option value="' . $themes[$i] . '"'
            . ($album->skin == $themes[$i] ? ' selected="selected"' : '')
            . '>' . $themes[$i] . '</option>';
    }
    $album_theme_select .= '</select>';

    $attach_select = MG_checkbox(array(
        'name'    => 'attach_tn',
        'checked' => $album->tn_attached,
        'value'   => '1',
    ));

    $result = DB_query("SELECT * FROM {$_TABLES['users']}");
    $nRows  = DB_numRows($result);
    $owner_select = '<select class="uk-select uk-form-width-medium" name="owner_id">';
    for ($i=0; $i<$nRows; $i++) {
        $row = DB_fetchArray($result);
        if ($row['uid'] == 1) continue;
        $owner_select .= '<option value="' . $row['uid'] . '"'
            . ($album->owner_id == $row['uid'] ? ' selected="selected"' : '')
            . '>' . COM_getDisplayName($row['uid']) . '</option>';
    }
    $owner_select .= '</select>';

    $album_sort_select = MG_optionlist(array(
        'name'    => 'album_sort_order',
        'current' => $album->album_sort_order,
        'values'  => array(
            '0' => $LANG_MG03['no_sort'],
            '1' => $LANG_MG03['sort_capture_asc'],
            '2' => $LANG_MG03['sort_capture'],
            '3' => $LANG_MG03['sort_upload_asc'],
            '4' => $LANG_MG03['sort_upload'],
            '5' => $LANG_MG03['sort_alpha'],
            '6' => $LANG_MG03['sort_alpha_asc'],
//          '7' => $LANG_MG03['sort_rating'],
//          '8' => $LANG_MG03['sort_rating_asc'],
        ),
    ));

    if (SEC_hasRights('mediagallery.admin')) {

        //
        // -- build the featured selects and info...
        //
        $featured_select = MG_checkbox(array(
            'name'    => 'featured',
            'checked' => $album->featured,
            'value'   => '1',
        ));

        // build featurepage select...
        $featurepage_select = '<select class="uk-select uk-form-width-medium" name="featurepage">';
        $featurepage_select .= MG_options(array(
            'current' => $album->cbpage,
            'values'  => array(
                'all'    => $LANG_MG01['all'],
                'allnhp' => $LANG_MG01['all_nhp'],
                'none'   => $LANG_MG01['homepage_only']
            )
        ));
        $featurepage_select .= COM_topicList('tid,topic', $album->cbpage);
        $featurepage_select .= '</select>';

        // position
        $feature_pos = MG_optionlist(array(
            'name'    => 'featureposition',
            'current' => $album->cbposition,
            'values'  => array(
                '1' => $LANG_MG01['top'],
                '2' => $LANG_MG01['after_featured_articles'],
                '3' => $LANG_MG01['bottom'],
            ),
        ));

        $ri_select = MG_checkbox(array(
            'name'    => 'enable_random',
            'checked' => $album->enable_random,
            'value'   => '1',
        ));

        $T->set_var(array(
            'featured_select'       => $featured_select,
            'feature_page_select'   => $featurepage_select,
            'feature_position'      => $feature_pos,
            'height_input'          => $max_image_height_input,
            'width_input'           => $max_image_width_input,
            'max_size_input'        => $max_filesize_input,
            'ri_select'             => $ri_select,
            'jpg_checked'           => ($album->valid_formats & MG_JPG   ? ' checked="checked"' : ''),
            'png_checked'           => ($album->valid_formats & MG_PNG   ? ' checked="checked"' : ''),
            'tif_checked'           => ($album->valid_formats & MG_TIF   ? ' checked="checked"' : ''),
            'gif_checked'           => ($album->valid_formats & MG_GIF   ? ' checked="checked"' : ''),
            'bmp_checked'           => ($album->valid_formats & MG_BMP   ? ' checked="checked"' : ''),
            'tga_checked'           => ($album->valid_formats & MG_TGA   ? ' checked="checked"' : ''),
            'psd_checked'           => ($album->valid_formats & MG_PSD   ? ' checked="checked"' : ''),
            'mp3_checked'           => ($album->valid_formats & MG_MP3   ? ' checked="checked"' : ''),
            'ogg_checked'           => ($album->valid_formats & MG_OGG   ? ' checked="checked"' : ''),
            'asf_checked'           => ($album->valid_formats & MG_ASF   ? ' checked="checked"' : ''),
            'swf_checked'           => ($album->valid_formats & MG_SWF   ? ' checked="checked"' : ''),
            'mov_checked'           => ($album->valid_formats & MG_MOV   ? ' checked="checked"' : ''),
            'mp4_checked'           => ($album->valid_formats & MG_MP4   ? ' checked="checked"' : ''),
            'mpg_checked'           => ($album->valid_formats & MG_MPG   ? ' checked="checked"' : ''),
            'zip_checked'           => ($album->valid_formats & MG_ZIP   ? ' checked="checked"' : ''),
            'flv_checked'           => ($album->valid_formats & MG_FLV   ? ' checked="checked"' : ''),
            'rflv_checked'          => ($album->valid_formats & MG_RFLV  ? ' checked="checked"' : ''),
            'emb_checked'           => ($album->valid_formats & MG_EMB   ? ' checked="checked"' : ''),
            'other_checked'         => ($album->valid_formats & MG_OTHER ? ' checked="checked"' : ''),
            'lang_featured_album'   => $LANG_MG01['featured_album'],
            'lang_set_featured'     => $LANG_MG01['set_featured'],
            'lang_featured_help'    => $LANG_MG01['featured_help'],
            'lang_position'         => $LANG_MG01['position'],
            'lang_topic'            => $LANG_MG01['topic'],
            'lang_ri_enable'        => $LANG_MG01['ri_enable'],
            'lang_max_image_height' => $LANG_MG01['max_image_height'],
            'lang_max_image_width'  => $LANG_MG01['max_image_width'],
            'lang_max_filesize'     => $LANG_MG01['max_filesize'],
            'lang_jpg'              => $LANG_MG01['jpg'],
            'lang_png'              => $LANG_MG01['png'],
            'lang_tif'              => $LANG_MG01['tif'],
            'lang_gif'              => $LANG_MG01['gif'],
            'lang_bmp'              => $LANG_MG01['bmp'],
            'lang_tga'              => $LANG_MG01['tga'],
            'lang_psd'              => $LANG_MG01['psd'],
            'lang_mp3'              => $LANG_MG01['mp3'],
            'lang_ogg'              => $LANG_MG01['ogg'],
            'lang_asf'              => $LANG_MG01['asf'],
            'lang_swf'              => $LANG_MG01['swf'],
            'lang_mov'              => $LANG_MG01['mov'],
            'lang_mp4'              => $LANG_MG01['mp4'],
            'lang_mpg'              => $LANG_MG01['mpg'],
            'lang_zip'              => $LANG_MG01['zip'],
            'lang_flv'              => $LANG_MG01['flv'],
            'lang_rflv'             => $LANG_MG01['rflv'],
            'lang_emb'              => $LANG_MG01['emb'],
            'lang_other'            => $LANG_MG01['other'],
            'lang_allowed_formats'  => $LANG_MG01['allowed_media_formats'],
            'lang_image'            => $LANG_MG01['image'],
            'lang_audio'            => $LANG_MG01['audio'],
            'lang_video'            => $LANG_MG01['video'],
        ));
    }
    $r = rand();
    if ($album->tn_attached) {
        list($album_last_image, $media_size) = MG_getImageUrl('covers/cover_' . $album_id);
        if ($media_size != false) {
            $T->set_var('thumbnail', '<img src="' . $album_last_image . '?r=' . $r . '" alt=""' . XHTML . '>');
        }
    }

    $filename_title_select = MG_checkbox(array(
        'name'    => 'filename_title',
        'checked' => $album->filename_title,
        'value'   => '1',
    ));

    // watermark stuff...
    $wm_auto_select = MG_checkbox(array(
        'name'    => 'wm_auto',
        'checked' => $album->wm_auto,
        'value'   => '1',
    ));

    $wm_opacity_select = MG_optionlist(array(
        'name'    => 'wm_opacity',
        'current' => $album->wm_opacity,
        'values'  => array(
            '10' => '10%',
            '20' => '20%',
            '30' => '30%',
            '40' => '40%',
            '50' => '50%',
            '60' => '60%',
            '70' => '70%',
            '80' => '80%',
            '90' => '90%',
        ),
    ));

    $wm_location_select = MG_optionlist(array(
        'name'    => 'wm_location',
        'current' => $album->wm_location,
        'values'  => array(
            '1' => $LANG_MG01['top_left'],
            '2' => $LANG_MG01['top_center'],
            '3' => $LANG_MG01['top_right'],
            '4' => $LANG_MG01['middle_left'],
            '5' => $LANG_MG01['middle_center'],
            '6' => $LANG_MG01['middle_right'],
            '7' => $LANG_MG01['bottom_left'],
            '8' => $LANG_MG01['bottom_center'],
            '9' => $LANG_MG01['bottom_right'],
        ),
    ));

    // now select what watermarks we have permission to use...
    $whereClause = "WHERE wm_id<>0 AND ";
    if (SEC_hasRights('mediagallery.admin')) {
        $whereClause .= "1=1 ";
    } else {
        $whereClause .= "(owner_id=" . intval($_USER['uid']) . " OR owner_id=0) ";
    }
    $sql = "SELECT * FROM {$_TABLES['mg_watermarks']} " . $whereClause . "ORDER BY owner_id";
    $result = DB_query($sql);
    $nRows  = DB_numRows($result);
    $wm_select =  '<select class="uk-select uk-form-width-medium" name="wm_id" onchange="change(this)">';
    $wm_select .= '<option value="blank.png">' . $LANG_MG01['no_watermark'] . '</option>';
    $wm_current = '<img src="' . $_MG_CONF['site_url'] . '/watermarks/blank.png" name="myImage" alt=""' . XHTML . '>';
    for ($i=0; $i<$nRows; $i++) {
        $row = DB_fetchArray($result);
        $wm_select .= '<option value="' . $row['filename'] . '"'
                    . ($album->wm_id==$row['wm_id'] ? ' selected="selected"' : '')
                    . '>' . $row['filename'] . '</option>';
        if ($album->wm_id == $row['wm_id']) {
            $wm_current = '<img src="' . $_MG_CONF['site_url'] . '/watermarks/' . $row['filename'] . '" name="myImage" alt=""' . XHTML . '>';
        }
    }
    $wm_select .= '</select>';

    $skins = MG_getFrames();
    $tmp = array();
    for ($i=0; $i < count($skins); $i++) {
        $tmp[$skins[$i]['dir']] = $skins[$i]['name'];
    }
    $skin_select = MG_optionlist(array(
        'name'    => 'skin',
        'current' => $album->image_skin,
        'values'  => $tmp,
    ));
    $askin_select = MG_optionlist(array(
        'name'    => 'askin',
        'current' => $album->album_skin,
        'values'  => $tmp,
    ));
    $dskin_select = MG_optionlist(array(
        'name'    => 'dskin',
        'current' => $album->display_skin,
        'values'  => $tmp,
    ));

    // permission template

    $usergroups = SEC_getUserGroups();
    $groupdd = '<select class="uk-select uk-form-width-medium" name="group_id">';
    $moddd   = '<select class="uk-select uk-form-width-medium" name="mod_id">';
    for ($i = 0; $i < count($usergroups); $i++) {
        if ($usergroups[key($usergroups)] != 2 && $usergroups[key($usergroups)] != 13) {
            $groupdd .= '<option value="' . $usergroups[key($usergroups)] . '"';
            $moddd   .= '<option value="' . $usergroups[key($usergroups)] . '"';
            if ($album->group_id == $usergroups[key($usergroups)]) {
                $groupdd .= ' selected="selected"';
            }
            if ($album->mod_group_id == $usergroups[key($usergroups)]) {
                $moddd   .= ' selected="selected"';
            }
            $groupdd .= '>' . key($usergroups) . '</option>';
            $moddd   .= '>' . key($usergroups) . '</option>';
        }
        next($usergroups);
    }
    $groupdd .= '</select>';
    $moddd   .= '</select>';

    $upload_select = MG_checkbox(array(
        'name'    => 'uploads',
        'checked' => $album->member_uploads,
        'value'   => '1',
    ));

    $moderate_select = MG_checkbox(array(
        'name'    => 'moderate',
        'checked' => $album->moderate,
        'value'   => '1',
    ));

    $child_update_select = MG_checkbox(array(
        'name'    => 'force_child_update',
        'checked' => false,
        'value'   => '1',
    ));

    $hidden_select = MG_checkbox(array(
        'name'    => 'hidden',
        'checked' => $album->hidden,
        'value'   => '1',
    ));

    $allow_download_select = MG_checkbox(array(
        'name'    => 'allow_download',
        'checked' => $album->allow_download,
        'value'   => '1',
    ));

    if (SEC_hasRights('mediagallery.admin')) {
        $perm_editor = SEC_getPermissionsHTML($album->perm_owner, $album->perm_group,
                                              $album->perm_members, $album->perm_anon);
    } else {
        $perm_editor = MG_getMemberPermissionsHTML($album->perm_members, $album->perm_anon);
    }

    $T->set_var(array(
        'site_url'                => $_CONF['site_url'],
        'site_admin_url'          => $_CONF['site_admin_url'],
        'xhtml'                   => XHTML,
        'start_block'             => COM_startBlock($block_title),
        'end_block'               => COM_endBlock(),
        'owner_username'          => DB_getItem($_TABLES['users'], 'username', "uid={$album->owner_id}"),
        'owner_id'                => $album->owner_id,
        'permissions_editor'      => $perm_editor,
        'old_album_id'            => $oldaid,
        'group_dropdown'          => $groupdd,
        'mod_dropdown'            => $moddd,
        'uploads'                 => $upload_select,
        'moderate'                => $moderate_select,
        'hidden'                  => $hidden_select,
        'force_child_update'      => $child_update_select,
        'owner_select'            => $owner_select,
        'email_mod_select'        => $email_mod_select,
        'action'                  => 'album',
        'path_mg'                 => $_MG_CONF['site_url'],
        'attach_select'           => $attach_select,
        'comment_select'          => $comment_select,
        'exif_select'             => $exif_select,
        'ranking_select'          => $ranking_select,
        'podcast_select'          => $podcast_select,
        'mp3ribbon_select'        => $mp3ribbon_select,
        'rsschildren_select'      => $rsschildren_select,
        'full_select'             => $full_select,
        'ss_select'               => $ss_select,
        'sf_select'               => isset($sf_select) ? $sf_select : 0,
        'views_select'            => $views_select,
        'keywords_select'         => $keywords_select,
        'album_views_select'      => $album_views_select,
        'display_album_desc_select' => $display_album_desc_select,
        'sort_select'             => $sort_select,
        'rss_select'              => $rss_select,
        'afirst_select'           => $afirst_select,
        'tn_size_select'          => $tn_size_select,
        'display_image_size'      => $display_image_size_select,
        'rows_input'              => $rows_input,
        'columns_input'           => $columns_input,
        'playback_type'           => $playback_type,
        'album_title'             => $album->title,
        'album_desc'              => $album->description,
        'album_id'                => $album_id,
        'parent_select'           => $album_selectbox,
        'album_cover'             => $album->cover,
        'album_owner'             => $album->owner_id,
        'album_order'             => $album->order,
        'album_cover_filename'    => $album->cover_filename,
        'last_update'             => $album->last_update,
        'media_count'             => $album->media_count,
        'wm_auto_select'          => $wm_auto_select,
        'wm_opacity_select'       => $wm_opacity_select,
        'wm_location_select'      => $wm_location_select,
        'wm_select'               => $wm_select,
        'wm_current'              => $wm_current,
        'album_theme_select'      => $album_theme_select,
        'album_sort_select'       => $album_sort_select,
        'allow_download_select'   => $allow_download_select,
        'filename_title_select'   => $filename_title_select,
        'skin_select'             => $skin_select,
        'askin_select'            => $askin_select,
        'dskin_select'            => $dskin_select,
        'tnheight_input'          => $tnheight_input,
        'tnwidth_input'           => $tnwidth_input,
        'usealternate_select'     => $usealternate_select,
        's_form_action'           => $actionURL,
        'lang_uploads'            => $LANG_MG01['anonymous_uploads_prompt'],
        'lang_accessrights'       => $LANG_ACCESS['accessrights'],
        'lang_owner'              => $LANG_ACCESS['owner'],
        'lang_group'              => $LANG_ACCESS['group'],
        'lang_permissions'        => $LANG_ACCESS['permissions'],
        'lang_perm_key'           => $LANG_ACCESS['permissionskey'],
        'lang_hidden'             => $LANG_MG01['hidden'],
        'permissions_msg'         => $LANG_ACCESS['permmsg'],
        'lang_member_upload'      => $LANG_MG01['member_upload'],
        'lang_moderate_album'     => $LANG_MG01['mod_album'],
        'lang_mod_group'          => $LANG_MG01['moderation_group'],
        'lang_force_child_update' => $LANG_MG01['force_child_update'],
        'lang_allow_download'     => $LANG_MG01['allow_download'],
        'lang_email_mods_on_submission' => $LANG_MG01['email_mods_on_submission'],
        'lang_usealternate'       => $LANG_MG01['use_alternate_url'],
        'lang_tnheight'           => $LANG_MG01['tn_height'],
        'lang_tnwidth'            => $LANG_MG01['tn_width'],
        'lang_save'               => $LANG_MG01['save'],
        'lang_edit_title'         => ($mode=='create' ? $LANG_MG01['create_album'] : $LANG_MG01['edit_album']),
        'lang_image_skin'         => $LANG_MG01['image_skin'],
        'lang_album_skin'         => $LANG_MG01['album_skin'],
        'lang_display_skin'       => $LANG_MG01['display_skin'],
        'lang_album_edit_help'    => $LANG_MG01['album_edit_help'],
        'lang_title'              => $LANG_MG01['title'],
        'lang_podcast'            => $LANG_MG01['podcast'],
        'lang_mp3ribbon'          => $LANG_MG01['mp3ribbon'],
        'lang_rsschildren'        => $LANG_MG01['rsschildren'],
        'lang_parent_album'       => $LANG_MG01['parent_album'],
        'lang_description'        => $LANG_MG01['description'],
        'lang_cancel'             => $LANG_MG01['cancel'],
        'lang_delete'             => $LANG_MG01['delete'],
        'lang_comments'           => $LANG_MG01['comments_prompt'],
        'lang_enable_exif'        => $LANG_MG01['enable_exif'],
        'lang_enable_ratings'     => $LANG_MG01['enable_ratings'],
        'lang_ss_enable'          => $LANG_MG01['ss_enable'],
        'lang_sf_enable'          => $LANG_MG01['sf_enable'],
        'lang_tn_size'            => $LANG_MG01['tn_size'],
        'lang_rows'               => $LANG_MG01['rows'],
        'lang_columns'            => $LANG_MG01['columns'],
        'lang_av_play_album'      => $LANG_MG01['av_play_album'],
        'lang_av_play_options'    => $LANG_MG01['av_play_options'],
        'lang_attached_thumbnail' => $LANG_MG01['attached_thumbnail'],
        'lang_thumbnail'          => $LANG_MG01['thumbnail'],
        'lang_album_attributes'   => $LANG_MG01['album_attributes'],
        'lang_album_cover'        => $LANG_MG01['album_cover'],
        'lang_enable_views'       => $LANG_MG01['enable_views'],
        'lang_enable_keywords'    => $LANG_MG01['enable_keywords'],
        'lang_enable_album_views' => $LANG_MG01['enable_album_views'],
        'lang_enable_sort'        => $LANG_MG01['enable_sort'],
        'lang_enable_rss'         => $LANG_MG01['enable_rss'],
        'lang_albums_first'       => $LANG_MG01['albums_first'],
        'lang_full_display'       => $LANG_MG01['full_display'],
        'lang_display_image_size' => $LANG_MG01['display_image_size'],
        'lang_album_sort'         => $LANG_MG01['default_album_sort'],
        'lang_watermark'          => $LANG_MG01['watermark'],
        'lang_wm_auto'            => $LANG_MG01['watermark_auto'],
        'lang_wm_opacity'         => $LANG_MG01['watermark_opacity'],
        'lang_wm_location'        => $LANG_MG01['watermark_location'],
        'lang_wm_id'              => $LANG_MG01['watermark_image'],
        'lang_unlimited'          => $LANG_MG01['zero_unlimited'],
        'lang_display_album_desc' => $LANG_MG01['display_album_desc'],
        'lang_filename_title'     => $LANG_MG01['filename_title'],
        'lang_media_attributes'   => $LANG_MG01['media_attributes'],
        'lang_theme_select'       => $LANG_MG01['album_theme'],
    ));

    if (SEC_hasRights('mediagallery.admin')) {
        $T->set_var('perms_editor_admin', '1');
    } else {
        $T->set_var('perms_editor_member', '1');
    }

    if ($_MG_CONF['htmlallowed'] == 1) {
        $T->set_var('allowed_html', COM_allowedHTML());
    }

    $retval .= $T->finish($T->parse('output', 'admin'));

    return $retval;
}

function MG_quickCreate($parent, $title, $desc='')
{
    global $_USER, $_CONF, $_TABLES, $_MG_CONF, $LANG_MG00;

    require_once $_CONF['path'].'plugins/mediagallery/include/classAlbum.php'; // komma debug 2016/2/22

    $parent_album = new mgAlbum($parent);
    if ($parent == 0) {
        $grp_id = DB_getItem($_TABLES['groups'], 'grp_id', "grp_name = 'mediagallery Admin'");
        $mod_grp_id = $grp_id;
    } else {
        $grp_id = $parent_album->group_id;
        $mod_grp_id = $parent_album->mod_group_id;
    }

    $album = new mgAlbum();

    if ($_MG_CONF['htmlallowed'] == 1) {
        $album->title       = $title;
        $album->description = $desc;
    } else {
        $album->title       = htmlspecialchars(strip_tags(COM_checkWords($title)));
        $album->description = htmlspecialchars(strip_tags(COM_checkWords($desc)));
    }
    if ($album->title == "") {
        return -1;
    }

    $album->parent       = $parent;
    $album->group_id     = $grp_id;
    $album->owner_id     = $_USER['uid'];
    $album->mod_group_id = $mod_grp_id;

    // simple check to see if we can create off the album root...
    if (!SEC_hasRights('mediagallery.admin')) {
        if ($parent == $_MG_CONF['member_album_root']) {
            if ($_MG_CONF['member_create_new'] == 0) {
                return -1;
            }
        }
    }

    // final permission check to make sure we have the proper rights to create here....
    if ($parent == 0 && !$_MG_CONF['member_albums'] == 1 && !$_MG_CONF['member_album_root'] == 0) {
        // see if we are mediagallery.admin
        if (!SEC_hasRights('mediagallery.admin')) {
            COM_errorLog("MediaGallery: Someone has tried to illegally save a Media Gallery Album in Root. "
                       . "User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
            return COM_showMessageText($LANG_MG00['access_denied_msg']);
        }
    } elseif ($parent != 0) {
        if (!isset($parent_album->id)) {    // does not exist...
            COM_errorLog("MediaGallery: Someone has tried to save a album to non-existent parent album. "
                       . "User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
            return COM_showMessageText($LANG_MG00['access_denied_msg']);
        } else {
            if ($parent_album->access != 3 &&
                !SEC_hasRights('mediagallery.admin') &&
                !$_MG_CONF['member_albums'] &&
                !$_MG_CONF['member_album_root'] == $parent_album->id) {
                COM_errorLog("MediaGallery: Someone has tried to illegally save a Media Gallery Album. "
                           . "User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
                return COM_showMessageText($LANG_MG00['access_denied_msg']);
            }
        }
    }

    if ($album->isMemberAlbum()) { // if a new album, set the member album defaults since we are a non-admin
        $album->perm_owner       = $_MG_CONF['member_permissions'][0];
        $album->perm_group       = $_MG_CONF['member_permissions'][1];
        $album->perm_members     = $_MG_CONF['member_permissions'][2];
        $album->perm_anon        = $_MG_CONF['member_permissions'][3];
        $album->enable_random    = $_MG_CONF['member_enable_random'];
        $album->max_image_height = $_MG_CONF['member_max_height'];
        $album->max_image_width  = $_MG_CONF['member_max_width'];
        $album->max_filesize     = $_MG_CONF['member_max_filesize'];
        $album->member_uploads   = $_MG_CONF['member_uploads'];
        $album->moderate         = $_MG_CONF['member_moderate'];
        $album->email_mod        = $_MG_CONF['member_email_mod'];
        $album->valid_formats    = $_MG_CONF['member_valid_formats'];
    }

    $album->id    = $album->createAlbumID();
    $album->order = $album->getNextSortOrder();
    $album->saveAlbum();
    $aid = $album->id;

    require_once $_CONF['path'] . 'plugins/mediagallery/include/rssfeed.php';
    MG_buildFullRSS();
    MG_buildAlbumRSS($aid);

    return $aid;
}


/**
* saves the specified album information
*
* @param    int     album_id    album_id to edit
* @return   string              HTML
*
*/
function MG_saveAlbum($album_id, $mode='')
{
    global $_DB_dbms, $_USER, $_CONF, $_TABLES, $_MG_CONF, $LANG_MG00, $LANG_MG01, $LANG_MG02;

    $aid = $album_id;
    $forceChildPermUpdate = isset($_POST['force_child_update']) ? COM_applyFilter($_POST['force_child_update'], true) : 0;
    $thumb = $_FILES['thumbnail'];
    $thumbnail = $thumb['tmp_name'];
    $att = isset($_POST['attach_tn']) ? COM_applyFilter($_POST['attach_tn']) : 0;

    if ($aid > 0) {  // should be 0 or negative 1 for create
        $album           = new mgAlbum($aid);
        $old_tn_attached = $album->tn_attached;
        $update          = 1;
    } else {
        $album           = new mgAlbum();
        $album->id       = $album->createAlbumID();
        $aid             = $album->id;
        $album->order    = $album->getNextSortOrder();
        $old_tn_attached = 0;
        $update          = 0;
    }

    $album->parent = COM_applyFilter($_POST['parentaid'], true);
    $parent_album = new mgAlbum($album->parent);

    if ($_MG_CONF['htmlallowed'] == 1) {
        $album->title       = COM_checkHTML(COM_killJS(COM_stripslashes($_POST['album_name'])));
        $album->description = COM_checkHTML(COM_killJS(COM_stripslashes($_POST['album_desc'])));
    } else {
        $album->title       = htmlspecialchars(strip_tags(COM_checkWords(COM_killJS(COM_stripslashes($_POST['album_name'])))));
        $album->description = htmlspecialchars(strip_tags(COM_checkWords(COM_killJS(COM_stripslashes($_POST['album_desc'])))));
    }
    if ($album->title == "") {
        return COM_showMessageText("You must enter an Album Name"
               . '  [ <a href=\'javascript:history.go(-1)\'>' . $LANG_MG02['go_back'] . '</a> ]');
    }

    $album->hidden = isset($_POST['hidden']) ? COM_applyFilter($_POST['hidden'], true) : 0;

    $album->cover               = COM_applyFilter($_POST['cover']);
    $album->cover_filename      = COM_applyFilter($_POST['album_cover_filename']);

    $album->enable_album_views = isset($_POST['enable_album_views']) ? COM_applyFilter($_POST['enable_album_views'], true) : 0;

    $album->image_skin          = COM_applyFilter($_POST['skin']);
    $album->album_skin          = COM_applyFilter($_POST['askin']);
    $album->display_skin        = COM_applyFilter($_POST['dskin']);

    $album->display_album_desc  = isset($_POST['display_album_desc']) ? COM_applyFilter($_POST['display_album_desc'], true) : 0;
    $album->enable_comments     = isset($_POST['enable_comments'])    ? COM_applyFilter($_POST['enable_comments'],    true) : 0;
    $album->enable_rating       = isset($_POST['enable_rating'])      ? COM_applyFilter($_POST['enable_rating'],      true) : 0;
    $album->tn_attached         = isset($_POST['attach_tn'])          ? COM_applyFilter($_POST['attach_tn'],          true) : 0;
    $album->enable_random       = isset($_POST['enable_random'])      ? COM_applyFilter($_POST['enable_random'],      true) : 0;
    $album->enable_views        = isset($_POST['enable_views'])       ? COM_applyFilter($_POST['enable_views'],       true) : 0;
    $album->enable_keywords     = isset($_POST['enable_keywords'])    ? COM_applyFilter($_POST['enable_keywords'],    true) : 0;
    $album->enable_sort         = isset($_POST['enable_sort'])        ? COM_applyFilter($_POST['enable_sort'],        true) : 0;
    $album->enable_rss          = isset($_POST['enable_rss'])         ? COM_applyFilter($_POST['enable_rss'],         true) : 0;
    $album->albums_first        = isset($_POST['albums_first'])       ? COM_applyFilter($_POST['albums_first'],       true) : 0;
    $album->allow_download      = isset($_POST['allow_download'])     ? COM_applyFilter($_POST['allow_download'],     true) : 0;
    $album->useAlternate        = isset($_POST['usealternate'])       ? COM_applyFilter($_POST['usealternate'],       true) : 0;

    $album->exif_display        = COM_applyFilter($_POST['enable_exif'],      true);
    $album->playback_type       = COM_applyFilter($_POST['playback_type'],    true);
    $album->enable_slideshow    = COM_applyFilter($_POST['enable_slideshow'], true);

    $album->full                = COM_applyFilter($_POST['full_display'],     true);
    $album->tn_size             = COM_applyFilter($_POST['tn_size'],          true);
    $album->max_image_height    = COM_applyFilter($_POST['max_image_height'], true);
    $album->max_image_width     = COM_applyFilter($_POST['max_image_width'],  true);
    $album->max_filesize        = COM_applyFilter($_POST['max_filesize'],     true);
    if ($album->max_filesize != 0) {
        $album->max_filesize = $album->max_filesize * 1024;
    }
    $album->display_image_size  = COM_applyFilter($_POST['display_image_size'], true);
    $album->display_rows        = COM_applyFilter($_POST['display_rows'],       true);
    $album->display_columns     = COM_applyFilter($_POST['display_columns'],    true);
    $album->skin                = COM_applyFilter($_POST['album_theme']);

    $album->filename_title = isset($_POST['filename_title']) ? COM_applyFilter($_POST['filename_title'], true) : 0;

    $album->shopping_cart       = 0;

    $album->wm_auto = isset($_POST['wm_auto']) ? COM_applyFilter($_POST['wm_auto'], true) : 0;

    $album->wm_id               = COM_applyFilter($_POST['wm_id']);
    $album->wm_opacity          = COM_applyFilter($_POST['wm_opacity'], true);
    $album->wm_location         = COM_applyFilter($_POST['wm_location'], true);
    $album->album_sort_order    = COM_applyFilter($_POST['album_sort_order'], true);

    $album->member_uploads  = isset($_POST['uploads'])     ? COM_applyFilter($_POST['uploads'],     true) : 0;
    $album->moderate        = isset($_POST['moderate'])    ? COM_applyFilter($_POST['moderate'],    true) : 0;
    $album->email_mod       = isset($_POST['email_mod'])   ? COM_applyFilter($_POST['email_mod'],   true) : 0;
    $album->podcast         = isset($_POST['podcast'])     ? COM_applyFilter($_POST['podcast'],     true) : 0;
    $album->mp3ribbon       = isset($_POST['mp3ribbon'])   ? COM_applyFilter($_POST['mp3ribbon'],   true) : 0;
    $album->rssChildren     = isset($_POST['rsschildren']) ? COM_applyFilter($_POST['rsschildren'], true) : 0;
    $album->tnHeight        = isset($_POST['tnheight'])    ? COM_applyFilter($_POST['tnheight'],    true) : 250;
    $album->tnWidth         = isset($_POST['tnwidth'])     ? COM_applyFilter($_POST['tnwidth'],     true) : 250;
    if ($album->tnHeight == 0) {
        $album->tnHeight = 250;
    }
    if ($album->tnWidth == 0) {
        $album->tnWidth = 250;
    }

    if (SEC_hasRights('mediagallery.admin')) {
        $format_jpg           = isset($_POST['format_jpg'])   ? COM_applyFilter($_POST['format_jpg'],  true) : 0;
        $format_png           = isset($_POST['format_png'])   ? COM_applyFilter($_POST['format_png'],  true) : 0;
        $format_tif           = isset($_POST['format_tif'])   ? COM_applyFilter($_POST['format_tif'],  true) : 0;
        $format_gif           = isset($_POST['format_gif'])   ? COM_applyFilter($_POST['format_gif'],  true) : 0;
        $format_bmp           = isset($_POST['format_bmp'])   ? COM_applyFilter($_POST['format_bmp'],  true) : 0;
        $format_tga           = isset($_POST['format_tga'])   ? COM_applyFilter($_POST['format_tga'],  true) : 0;
        $format_psd           = isset($_POST['format_psd'])   ? COM_applyFilter($_POST['format_psd'],  true) : 0;
        $format_mp3           = isset($_POST['format_mp3'])   ? COM_applyFilter($_POST['format_mp3'],  true) : 0;
        $format_ogg           = isset($_POST['format_ogg'])   ? COM_applyFilter($_POST['format_ogg'],  true) : 0;
        $format_asf           = isset($_POST['format_asf'])   ? COM_applyFilter($_POST['format_asf'],  true) : 0;
        $format_swf           = isset($_POST['format_swf'])   ? COM_applyFilter($_POST['format_swf'],  true) : 0;
        $format_mov           = isset($_POST['format_mov'])   ? COM_applyFilter($_POST['format_mov'],  true) : 0;
        $format_mp4           = isset($_POST['format_mp4'])   ? COM_applyFilter($_POST['format_mp4'],  true) : 0;
        $format_mpg           = isset($_POST['format_mpg'])   ? COM_applyFilter($_POST['format_mpg'],  true) : 0;
        $format_zip           = isset($_POST['format_zip'])   ? COM_applyFilter($_POST['format_zip'],  true) : 0;
        $format_other         = isset($_POST['format_other']) ? COM_applyFilter($_POST['format_other'],true) : 0;
        $format_flv           = isset($_POST['format_flv'])   ? COM_applyFilter($_POST['format_flv'],  true) : 0;
        $format_rflv          = isset($_POST['format_rflv'])  ? COM_applyFilter($_POST['format_rflv'], true) : 0;
        $format_emb           = isset($_POST['format_emb'])   ? COM_applyFilter($_POST['format_emb'],  true) : 0;
        $album->valid_formats = ($format_jpg + $format_png + $format_tif + $format_gif + $format_bmp + $format_tga
                               + $format_psd + $format_mp3 + $format_ogg + $format_asf + $format_swf + $format_mov
                               + $format_mp4 + $format_mpg + $format_zip + $format_other + $format_flv + $format_rflv + $format_emb);

        $album->featured      = isset($_POST['featured'])     ? COM_applyFilter($_POST['featured']) : 0;     // admin only
        $album->cbposition    = COM_applyFilter($_POST['featureposition'],true);                             // admin only
        $album->cbpage        = COM_applyFilter($_POST['featurepage']);                                      // admin only
        $album->group_id      = isset($_POST['group_id'])     ? COM_applyFilter($_POST['group_id']) : 0;     // admin only
        $album->mod_group_id  = isset($_POST['mod_id'])       ? COM_applyFilter($_POST['mod_id'],true) : 0;  // admin only
        $perm_owner           = isset($_POST['perm_owner'])   ? $_POST['perm_owner']   : 0;                  // admin only
        $perm_group           = isset($_POST['perm_group'])   ? $_POST['perm_group']   : 0;                  // admin only
        $perm_members         = isset($_POST['perm_members']) ? $_POST['perm_members'] : 0;
        $perm_anon            = isset($_POST['perm_anon'])    ? $_POST['perm_anon']    : 0;
        list($album->perm_owner,
             $album->perm_group,
             $album->perm_members,
             $album->perm_anon) = SEC_getPermissionValues($perm_owner, $perm_group, $perm_members, $perm_anon);
    } else {
        $perm_owner           = $album->perm_owner; // already set by existing album?
        $perm_group           = $album->perm_group; // already set by existing album?
        if ( $update == 0 ) {
            if (isset($parent_album->group_id)) {
                $grp_id = $parent_album->group_id;
                $album->group_id = $grp_id;
            } else {
                $grp_id = DB_getItem($_TABLES['groups'], 'grp_id', "grp_name = 'mediagallery Admin'");
                $album->group_id = $grp_id;  // only do these two if create....
            }
            $album->mod_group_id = $_MG_CONF['member_mod_group_id'];
            if ($album->mod_group_id == '' || $album->mod_group_id < 1) {
                $album->mod_group_id = $grp_id;
            }
        }
        $perm_members         = isset($_POST['perm_members']) ? $_POST['perm_members'] : 0;
        $perm_anon            = isset($_POST['perm_anon'])    ? $_POST['perm_anon']    : 0;
        list($junk1,$junk2,$album->perm_members,$album->perm_anon) = SEC_getPermissionValues($perm_owner,$perm_group,$perm_members,$perm_anon);
    }

    $album->owner_id = isset($_POST['owner_id']) ? COM_applyFilter($_POST['owner_id']) : 2;

    // simple check to see if we can create off the album root...
    if (!SEC_hasRights('mediagallery.admin')) {
        if ( $album->parent == $_MG_CONF['member_album_root'] && $update == 0 ) {
            if ( $_MG_CONF['member_create_new'] == 0 ) {
                return COM_showMessageText("Cannot create a new album off the member root, please select a new parent album"
                       . '  [ <a href=\'javascript:history.go(-1)\'>' . $LANG_MG02['go_back'] . '</a> ]');
            }
        }
    }

    // final permission check to make sure we have the proper rights to create here....
    if ( $album->parent == 0 && $update == 0 && !$_MG_CONF['member_albums'] == 1 && !$_MG_CONF['member_album_root'] == 0 ) {
        // see if we are mediagallery.admin
        if (!SEC_hasRights('mediagallery.admin')) {
            COM_errorLog("MediaGallery: Someone has tried to illegally save a Media Gallery Album in Root. "
                       . "User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
            return COM_showMessageText($LANG_MG00['access_denied_msg']);
        }
    } elseif ($album->parent != 0) {
        if (!isset($parent_album->id)) {    // does not exist...
            COM_errorLog("MediaGallery: Someone has tried to save a album to non-existent parent album. "
                       . "User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
            return COM_showMessageText($LANG_MG00['access_denied_msg']);
        } else {
            if ($parent_album->access != 3 &&
                !SEC_hasRights('mediagallery.admin') &&
                !$_MG_CONF['member_albums'] &&
                !($_MG_CONF['member_album_root'] == $parent_album->id)) {
                COM_errorLog("MediaGallery: Someone has tried to illegally save a Media Gallery Album. "
                           . "User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
                return COM_showMessageText($LANG_MG00['access_denied_msg']);
            }
        }
    }

    if ($old_tn_attached == 0 && $album->tn_attached == 1 && $thumb['tmp_name'] == '') {
        $album->tn_attached = 0;
    }

    $remove_old_tn = 0;
    if ($old_tn_attached == 1 && $album->tn_attached == 0) {
        $remove_old_tn = 1;
    }
    $attachtn = 0;
    if ($thumb['tmp_name'] != '' && $album->tn_attached == 1) {
        $thumbnail  = $thumb['tmp_name'];
        $attachtn = 1;
    }

    // pull the watermark id associated with the filename...

    $wm_id = 0;
    if ($album->wm_id != 'blank.png') {
        $wm_id = DB_getItem($_TABLES['mg_watermarks'], 'wm_id', 'filename="' . DB_escapeString($album->wm_id) . '"');
    }
    if ($wm_id == '') $wm_id = 0;

    if ($wm_id == 0) {
        $album->wm_auto = 0;
    }
    $album->wm_id = $wm_id;

    // handle new featured albums

    if (SEC_hasRights('mediagallery.admin')) {
        if ($album->featured) {
            // check for other featured albums, we can only have one
            $sql = "SELECT album_id FROM {$_TABLES['mg_albums']} WHERE featured=1 AND cbpage='" . DB_escapeString($album->cbpage) . "'";
            $result = DB_query($sql);
            while ($row = DB_fetchArray($result)) {
                DB_change($_TABLES['mg_albums'], 'featured', 0, 'album_id', $row['album_id']);
            }
        }
    } else { // if a new album, set the member album defaults since we are a non-admin
        if ($album->isMemberAlbum() && update == 0) {
            $album->perm_owner        = $_MG_CONF['member_permissions'][0];
            $album->perm_group        = $_MG_CONF['member_permissions'][1];
            $album->enable_random     = $_MG_CONF['member_permissions'][2];
            $album->max_image_height  = $_MG_CONF['member_permissions'][3];
            $album->max_image_width   = $_MG_CONF['member_max_width'];
            $album->max_filesize      = $_MG_CONF['member_max_filesize'];
            $album->member_uploads    = $_MG_CONF['member_uploads'];
            $album->moderate          = $_MG_CONF['member_moderate'];
            $album->email_mod         = $_MG_CONF['member_email_mod'];
            $album->valid_formats     = $_MG_CONF['member_valid_formats'];
        }
    }

    $album->title = substr($album->title,0,254);
    if ($_DB_dbms == "mssql") {
        $album->description = substr($album->description,0,1500);
    }

    if ($album->last_update == '') {
        $album->last_update = 0;
    }
    $album->last_update = intval($album->last_update);

    if ($album->id < 1) {
        $album->id = $album->createAlbumID();
        $aid = $album->id;
        $album->order = $album->getNextSortOrder();
    }

    if ($album->id == 0) {
        COM_errorLog("MediaGallery: Internal Error - album_id = 0 - Contact mark@glfusion.org  ");
        return COM_showMessageText($LANG_MG00['access_denied_msg']);
    }
    $album->saveAlbum();
    $album->updateChildPermissions($forceChildPermUpdate);

    // now handle the attached cover...

    if ($attachtn == 1) {
        if (!function_exists('MG_getFile')) {
            require_once $_CONF['path'] . 'plugins/mediagallery/include/lib-upload.php';
        }
        $media_filename = $_MG_CONF['path_mediaobjects'] . 'covers/cover_' . $album->id;
        MG_attachThumbnail($album->id, $thumbnail, $media_filename);
    }

    if ($remove_old_tn == 1) {
        foreach ($_MG_CONF['validExtensions'] as $ext) {
            if (file_exists($_MG_CONF['path_mediaobjects'] . 'covers/cover_' . $album->id . $ext)) {
                @unlink($_MG_CONF['path_mediaobjects'] . 'covers/cover_' . $album->id . $ext);
                break;
            }
        }
    }

    // do any album sorting here...

    if ($album->parent == 0) {
        switch ($album->album_sort_order) {
            case 0 :
                break;
            case 3 : // upload, asc
                MG_staticSortAlbum( $aid, 2, 1, 0 );
                break;
            case 4 :  // upload, desc
                MG_staticSortAlbum( $aid, 2, 0, 0 );
                break;
            case 5 :  // title, asc
                MG_staticSortAlbum( $aid, 0, 1, 0 );
                break;
            case 6 :  // title, desc
                MG_staticSortAlbum( $aid, 0, 0, 0 );
                break;
            case 7 :  // rating, desc
                MG_staticSortAlbum( $aid, 3, 0, 0 );
                break;
            case 8 :  // rating, desc
                MG_staticSortAlbum( $aid, 3, 1, 0 );
                break;
            default : // skip it...
                break;
        }
    } else {
        // not a root album...
        switch ($parent_album->album_sort_order) {
            case 0 :
                break;
            case 3 : // upload, asc
                MG_staticSortAlbum( $album->parent, 2, 1, 0 );
                break;
            case 4 :  // upload, desc
                MG_staticSortAlbum( $album->parent, 2, 0, 0 );
                break;
            case 5 :  // title, asc
                MG_staticSortAlbum( $album->parent, 0, 1, 0 );
                break;
            case 6 :  // title, desc
                MG_staticSortAlbum( $album->parent, 0, 0, 0 );
                break;
            case 7 :  // rating, desc
                MG_staticSortAlbum( $album->parent, 3, 0, 0 );
                break;
            case 8 :  // rating, desc
                MG_staticSortAlbum( $album->parent, 3, 1, 0 );
                break;
            default : // skip it...
                break;
        }
        // now call it for myself to sort my subs
        switch ($album->album_sort_order) {
            case 0 :
                break;
            case 3 : // upload, asc
                MG_staticSortAlbum( $aid, 2, 1, 0 );
                break;
            case 4 :  // upload, desc
                MG_staticSortAlbum( $aid, 2, 0, 0 );
                break;
            case 5 :  // title, asc
                MG_staticSortAlbum( $aid, 0, 1, 0 );
                break;
            case 6 :  // title, desc
                MG_staticSortAlbum( $aid, 0, 0, 0 );
                break;
            case 7 :  // rating, desc
                MG_staticSortAlbum( $aid, 3, 0, 0 );
                break;
            case 8 :  // rating, desc
                MG_staticSortAlbum( $aid, 3, 1, 0 );
                break;
            default : // skip it...
                break;
        }
    }

    require_once $_CONF['path'] . 'plugins/mediagallery/include/rssfeed.php';
    MG_buildFullRSS();
    MG_buildAlbumRSS($album->id);

    if ($mode == 'copy') {
        return $album->id;
    }

    $actionURL = $_MG_CONF['site_url'] . '/album.php?aid=' . $album->id;
    COM_redirect($actionURL);
}

function MG_staticSortAlbum($startaid, $sortfield, $sortorder, $process_subs)
{
    global $_TABLES;

    switch ($sortfield) {
        case '0' :  // album title
            $sql_sort_by = " ORDER BY album_title ";
            break;
        case '1' :  // media_count
            $sql_sort_by = " ORDER BY media_count ";
            break;
        case '2' : // last_update
            $sql_sort_by = " ORDER BY last_update ";
            break;
        case '3' : // rating
            $sql_sort_by = " ORDER BY media_rating ";
            break;
        default :
            $sql_sort_by = " ORDER BY album_title ";
            break;
    }

    switch ($sortorder) {
        case '0' :  // ascending
            $sql_order = " DESC";
            break;
        case '1' :  // descending
            $sql_order = " ASC";
            break;
        default:
            $sql_order = " ASC";
            break;
    }

    if ($process_subs == 0) {
        $sql = "SELECT album_id,album_order FROM {$_TABLES['mg_albums']} "
             . "WHERE album_parent=" . intval($startaid) . $sql_sort_by . $sql_order;
        $order = 10;
        $result = DB_query($sql);
        $numRows = DB_numRows($result);
        $album_id = array();
        $album_order = array();
        for ($x = 0; $x < $numRows; $x++) {
            $row = DB_fetchArray($result);
            $album_id[$x] = $row['album_id'];
            $album_order[$x] = $order;
            $order += 10;
        }

        $album_count = $numRows;

        for ($x = 0; $x < $album_count; $x++) {
            DB_change($_TABLES['mg_albums'], 'album_order', $album_order[$x], 'album_id', $album_id[$x]);
        }
    } else {
        MG_staticSortChildAlbum($startaid, $sql_order, $sql_sort_by);
    }
    return;
}

function MG_staticSortChildAlbum($startaid, $sql_order, $sql_sort_by)
{
    global $_TABLES;

    $sql = "SELECT album_id,album_order FROM {$_TABLES['mg_albums']} "
         . "WHERE album_parent=" . intval($startaid) . $sql_sort_by . $sql_order;
    $order = 10;
    $result = DB_query($sql);
    $numRows = DB_numRows($result);
    $album_id = array();
    $album_order = array();
    for ($x = 0; $x < $numRows; $x++) {
        $row = DB_fetchArray($result);
        $album_id[$x] = $row['album_id'];
        $album_order[$x] = $order;
        $order += 10;
    }

    $album_count = $numRows;

    for ($x = 0; $x < $album_count; $x++) {
        DB_change($_TABLES['mg_albums'], 'album_order', $album_order[$x], 'album_id', $album_id[$x]);
    }

    $album = new mgAlbum($startaid);
    $children = $album->getChildren();
    foreach ($children as $child) {
        MG_staticSortChildAlbum($child, $sql_order, $sql_sort_by);
    }
}

function MG_buildAlbumData($copy_aid, $parent_aid)
{
    global $_USER, $_TABLES, $_MG_CONF, $LANG_MG00;

    $sql = "SELECT * FROM {$_TABLES['mg_albums']} WHERE album_id=" . intval($copy_aid);
    $result = DB_query($sql);
    $row = DB_fetchArray($result);
    if (DB_error() != 0) {
        echo COM_errorLog("Media Gallery - Error retrieving copy album data.(album_id=".$copy_aid.")");
    }

    $_POST['album_name'] = isset($row['album_title']) ? 'Copy_'.$row['album_title'] : '';
    $_POST['album_desc'] = isset($row['album_desc']) ? $row['album_desc'] : '';
    $_POST['parentaid'] = $parent_aid;
    $_POST['album_theme'] = isset($row['skin']) ? $row['skin'] : 'default';
    $_POST['enable_comments'] = isset($row['enable_comments']) ? $row['enable_comments'] : 0;
    $_POST['enable_exif'] = isset($row['exif_display']) ? $row['exif_display'] : 0;
    $_POST['enable_rating'] = isset($row['enable_rating']) ? $row['enable_rating'] : 0;
    $_POST['enable_rss'] = isset($row['enable_rss']) ? $row['enable_rss'] : 0;
    $_POST['rsschildren'] = isset($row['rsschildren']) ? $row['rsschildren'] : 0;
    $_POST['podcast'] = isset($row['podcast']) ? $row['podcast'] : 0;
    $_POST['mp3ribbon'] = isset($row['mp3ribbon']) ? $row['mp3ribbon'] : 0;
    $_POST['enable_sort'] = isset($row['enable_sort']) ? $row['enable_sort'] : 0;
    $_POST['album_sort_order'] = isset($row['album_sort_order']) ? $row['album_sort_order'] : 0;
    $_POST['display_album_desc'] = isset($row['display_album_desc']) ? $row['display_album_desc'] : 0;
    $_POST['enable_album_views'] = isset($row['enable_album_views']) ? $row['enable_album_views'] : 0;
    $_POST['enable_views'] = isset($row['enable_views']) ? $row['enable_views'] : 0;
    $_POST['enable_keywords'] = isset($row['enable_keywords']) ? $row['enable_keywords'] : 0;
    $_POST['skin'] = isset($row['image_skin']) ? $row['image_skin'] : 'default';
    $_POST['dskin'] = isset($row['display_skin']) ? $row['display_skin'] : 'default';
    $_POST['askin'] = isset($row['album_skin']) ? $row['album_skin'] : 'default';
    $_POST['albums_first'] = isset($row['albums_first']) ? $row['albums_first'] : 1;
    $_POST['tn_size'] = isset($row['tn_size']) ? $row['tn_size'] : 0;
    $_POST['tnwidth'] = isset($row['tnwidth']) ? $row['tnwidth'] : 360;
    $_POST['tnheight'] = isset($row['tnheight']) ? $row['tnheight'] : 360;
    $_POST['playback_type'] = isset($row['playback_type']) ? $row['playback_type'] : 0;
    $_POST['enable_slideshow'] = isset($row['enable_slideshow']) ? $row['enable_slideshow'] : 0;
    $_POST['display_rows'] = isset($row['display_rows']) ? $row['display_rows'] : 40;
    $_POST['display_columns'] = isset($row['display_columns']) ? $row['display_columns'] : 3;
    $_POST['attach_tn'] = isset($row['tn_attached']) ? $row['tn_attached'] : 0;
    if ($_POST['attach_tn'] == 1) {
        foreach ($_MG_CONF['validExtensions'] as $ext) {
            if (file_exists($_MG_CONF['path_mediaobjects'] . 'covers/cover_' . $copy_aid . $ext)) {
                $_FILES['thumbnail']['tmp_name'] = $_MG_CONF['path_mediaobjects'] . 'covers/cover_' . $copy_aid . $ext;
                break;
            }
        }
    }
    $_POST['featured'] = isset($row['featured']) ? $row['featured'] : 0;
    $_POST['featureposition'] = isset($row['cbposition']) ? $row['cbposition'] : 0;
    $_POST['featurepage'] = isset($row['cbpage']) ? $row['cbpage'] : '';
    $_POST['filename_title'] = isset($row['filename_title']) ? $row['filename_title'] : 0;
    $_POST['allow_download'] = isset($row['allow_download']) ? $row['allow_download'] : 0;
    $_POST['full_display'] = isset($row['full_display']) ? $row['full_display'] : 0;
    $_POST['enable_random'] = isset($row['enable_random']) ? $row['enable_random'] : 0;
    $_POST['max_image_width'] = isset($row['max_image_width']) ? $row['max_image_width'] : 0;
    $_POST['max_image_height'] = isset($row['max_image_height']) ? $row['max_image_height'] : 0;
    $_POST['max_filesize'] = isset($row['max_filesize']) ? $row['max_filesize'] : 0;
    $_POST['display_image_size'] = isset($row['display_image_size']) ? $row['display_image_size'] : 2;
    $_POST['usealternate'] = isset($row['usealternate']) ? $row['usealternate'] : 0;
    $_POST['wm_auto'] = isset($row['wm_auto']) ? $row['wm_auto'] : 0;
    $_POST['wm_opacity'] = isset($row['wm_opacity']) ? $row['wm_opacity'] : 0;
    $_POST['wm_location'] = isset($row['wm_location']) ? $row['wm_location'] : 0;
    $_POST['wm_id'] = isset($row['wm_id']) ? $row['wm_id'] : 0;
    $valid_formats = isset($row['valid_formats']) ? $row['valid_formats'] : '65535';
    $_POST['format_jpg'] = ( $valid_formats & MG_JPG ) ? MG_JPG : 0;
    $_POST['format_png'] = ( $valid_formats & MG_PNG ) ? MG_PNG : 0;
    $_POST['format_tif'] = ( $valid_formats & MG_TIF ) ? MG_TIF : 0;
    $_POST['format_gif'] = ( $valid_formats & MG_GIF ) ? MG_GIF : 0;
    $_POST['format_bmp'] = ( $valid_formats & MG_BMP ) ? MG_BMP : 0;
    $_POST['format_tga'] = ( $valid_formats & MG_TGA ) ? MG_TGA : 0;
    $_POST['format_psd'] = ( $valid_formats & MG_PSD ) ? MG_PSD : 0;
    $_POST['format_mp3'] = ( $valid_formats & MG_MP3 ) ? MG_MP3 : 0;
    $_POST['format_ogg'] = ( $valid_formats & MG_OGG ) ? MG_OGG : 0;
    $_POST['format_asf'] = ( $valid_formats & MG_ASF ) ? MG_ASF : 0;
    $_POST['format_swf'] = ( $valid_formats & MG_SWF ) ? MG_SWF : 0;
    $_POST['format_mov'] = ( $valid_formats & MG_MOV ) ? MG_MOV : 0;
    $_POST['format_mp4'] = ( $valid_formats & MG_MP4 ) ? MG_MP4 : 0;
    $_POST['format_mpg'] = ( $valid_formats & MG_MPG ) ? MG_MPG : 0;
    $_POST['format_flv'] = ( $valid_formats & MG_FLV ) ? MG_FLV : 0;
    $_POST['format_rflv'] = ( $valid_formats & MG_RFLV ) ? MG_RFLV : 0;
    $_POST['format_emb'] = ( $valid_formats & MG_EMB ) ? MG_EMB : 0;
    $_POST['format_zip'] = ( $valid_formats & MG_ZIP ) ? MG_ZIP : 0;
    $_POST['format_other'] = ( $valid_formats & MG_OTHER ) ? MG_OTHER : 0;
    $_POST['uploads'] = isset($row['member_uploads']) ? $row['member_uploads'] : 0;
    $_POST['moderate'] = isset($row['moderate']) ? $row['moderate'] : 0;
    $_POST['mod_id'] = isset($row['mod_group_id']) ? $row['mod_group_id'] : 0;
    $_POST['email_mod'] = isset($row['email_mod']) ? $row['email_mod'] : 0;
    $_POST['owner_id'] = isset($row['owner_id']) ? $row['owner_id'] : 0;
    $_POST['group_id'] = isset($row['group_id']) ? $row['group_id'] : 0;
    $_POST['perm_owner'] = array();
    $perm_owner = isset($row['perm_owner']) ? $row['perm_owner'] : 0;
    if ($perm_owner >= 2) {
        $_POST['perm_owner'][] = '2';
    }
    if ($perm_owner == 3) {
        $_POST['perm_owner'][] = '1';
    }
    $_POST['perm_group'] = array();
    $perm_group = isset($row['perm_group']) ? $row['perm_group'] : 0;
    if ($perm_group >= 2) {
        $_POST['perm_group'][] = '2';
    }
    if ($perm_group == 3) {
        $_POST['perm_group'][] = '1';
    }
    $_POST['perm_members'] = array();
    $perm_members = isset($row['perm_members']) ? $row['perm_members'] : 0;
    if ($perm_members == 2) {
        $_POST['perm_members'][] = '2';
    }
    $_POST['perm_anon'] = array();
    $perm_anon = isset($row['perm_anon']) ? $row['perm_anon'] : 0;
    if ($perm_anon == 2) {
        $_POST['perm_anon'][] = '2';
    }
    $_POST['hidden'] = isset($row['hidden']) ? $row['hidden'] : 0;

    $_POST['album_id'] = '-1';
    $_POST['cover'] = '-1';
    $_POST['owner'] = $_POST['owner_id'];
    $_POST['order'] = '0';
    $_POST['album_cover_filename'] = '';
    $_POST['last_update'] = '0';
    $_POST['media_count'] = '0';
    $_POST['action'] = 'album';
    $_POST['origaid'] = $parent_aid;

}
?>