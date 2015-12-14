<?php

// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | Banner Plugin 1.1                                                         |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// |                                                                           |
// | Geeklog Banner Plugin administration page.                                |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000-2010 by the following authors:                         |
// |                                                                           |
// | Authors: Tony Bibbs        - tony AT tonybibbs DOT com                    |
// |          Mark Limburg      - mlimburg AT users DOT sourceforge DOT net    |
// |          Jason Whittenburg - jwhitten AT securitygeeks DOT com            |
// |          Dirk Haun         - dirk AT haun-online DOT de                   |
// |          Hiroron           - hiroron AT hiroron DOT com                   |
// | Presented by:IvyWe          - http://www.ivywe.co.jp                      |
// +---------------------------------------------------------------------------+
// |                                                                           |
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
// |                                                                           |
// +---------------------------------------------------------------------------+

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';

// Uncomment the lines below if you need to debug the HTTP variables being passed
// to the script.  This will sometimes cause errors but it will allow you to see
// the data being passed in a POST operation
// echo COM_debug($_POST);
// exit;

$display = '';

if (!SEC_hasRights('banner.edit')) {
    $display .= COM_siteHeader('menu', $MESSAGE[30])
             . COM_showMessageText($MESSAGE[34], $MESSAGE[30])
             . COM_siteFooter();
    COM_accessLog("User {$_USER['username']} tried to illegally access the banner administration screen.");
    echo $display;
    exit;
}

/**
* Shows the banner editor
*
* @param  string  $mode   Used to see if we are moderating a banner or simply editing one
* @param  string  $bid    ID of banner to edit
* @global array core config vars
* @global array core group data
* @global array core table data
* @global array core user data
* @global array banner plugin config vars
* @global array banner plugin lang vars
* @global array core lang access vars
* @return string HTML for the banner editor form
*
*/
function editbanner ($mode, $bid = '')
{
    global $_CONF, $_GROUPS, $_TABLES, $_USER, $_BAN_CONF, $_PLUGINS,
           $LANG_BANNER_ADMIN, $LANG_ACCESS, $LANG_ADMIN, $MESSAGE;

    $retval = '';

    $banner_templates = new Template($_CONF['path'] . 'plugins/banner/templates/admin/');
    $banner_templates->set_file('editor','bannereditor.thtml');
    $banner_templates->set_var( 'xhtml', XHTML );
    $banner_templates->set_var('site_url', $_CONF['site_url']);
    $banner_templates->set_var('site_admin_url', $_CONF['site_admin_url']);
    $banner_templates->set_var('layout_url',$_CONF['layout_url']);

    $banner_templates->set_var('lang_pagetitle', $LANG_BANNER_ADMIN[28]);
    $banner_templates->set_var('lang_banner_list', $LANG_BANNER_ADMIN[53]);
    $banner_templates->set_var('lang_new_banner', $LANG_BANNER_ADMIN[51]);
    $banner_templates->set_var('lang_validate_banner', $LANG_BANNER_ADMIN[26]);
    $banner_templates->set_var('lang_list_categories', $LANG_BANNER_ADMIN[50]);
    $banner_templates->set_var('lang_new_category', $LANG_BANNER_ADMIN[52]);
    $banner_templates->set_var('lang_admin_home', $LANG_ADMIN['admin_home']);
    $banner_templates->set_var('instructions', $LANG_BANNER_ADMIN[29]);

    $fcktoolbar_mg = '';
    if (in_array('mediagallery', $_PLUGINS)) {
        if (file_exists($_CONF['path_html'] . 'fckeditor/editor/plugins/mediagallery')) {
            $fcktoolbar_mg = '-mg';
        }
    }
    $banner_templates->set_var('mg', $fcktoolbar_mg);

    if ($mode <> 'editsubmission' AND !empty($bid)) {
        $result = DB_query("SELECT * FROM {$_TABLES['banner']} WHERE bid ='$bid'");
        if (DB_numRows($result) !== 1) {
            $msg = COM_startBlock ($LANG_BANNER_ADMIN[24], '',
                COM_getBlockTemplate ('_msg_block', 'header'));
            $msg .= $LANG_BANNER_ADMIN[25];
            $msg .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
            return $msg;
        }
        $A = DB_fetchArray($result);
        $access = SEC_hasAccess($A['owner_id'],$A['group_id'],$A['perm_owner'],$A['perm_group'],$A['perm_members'],$A['perm_anon']);
        if ($access == 0 OR $access == 2) {
            $retval .= COM_startBlock($LANG_BANNER_ADMIN[16], '',
                               COM_getBlockTemplate ('_msg_block', 'header'));
            $retval .= $LANG_BANNER_ADMIN[17];
            $retval .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
            COM_accessLog("User {$_USER['username']} tried to illegally submit or edit banner $bid.");
            return $retval;
        }
    } else {
        if ($mode == 'editsubmission') {
            $result = DB_query ("SELECT * FROM {$_TABLES['bannersubmission']} WHERE bid = '$bid'");
            $A = DB_fetchArray($result);
        } else {
            $A['bid'] = COM_makesid();
            $A['cid'] = '';
            $A['url'] = '';
            $A['description'] = '';
            $A['title']= '';
            $A['publishstart']= '';
            $A['publishend']= '';
            $A['owner_id'] = $_USER['uid'];
        }
        $A['hits'] = 0;
        if (isset ($_GROUPS['Banner Admin'])) {
            $A['group_id'] = $_GROUPS['Banner Admin'];
        } else {
            $A['group_id'] = SEC_getFeatureGroup ('banner.edit');
        }
        SEC_setDefaultPermissions ($A, $_BAN_CONF['default_permissions']);
        $access = 3;
    }
    $retval .= COM_startBlock ($LANG_BANNER_ADMIN[1], '',
                               COM_getBlockTemplate ('_admin_block', 'header'));

    $banner_templates->set_var('banner_id', $A['bid']);
    if (!empty($bid) && SEC_hasRights('banner.edit')) {
        $delbutton = '<input type="submit" value="' . $LANG_ADMIN['delete']
                   . '" name="mode"%s' . XHTML . '>';
        $jsconfirm = ' onclick="return confirm(\'' . $MESSAGE[76] . '\');"';
        $banner_templates->set_var ('delete_option',
                                  sprintf ($delbutton, $jsconfirm));
        $banner_templates->set_var ('delete_option_no_confirmation',
                                  sprintf ($delbutton, ''));
        if ($mode == 'editsubmission') {
            $banner_templates->set_var('submission_option',
                '<input type="hidden" name="type" value="submission"'
                . XHTML . '>');
        }
    }
    $banner_templates->set_var('lang_bannertitle', $LANG_BANNER_ADMIN[3]);
    $banner_templates->set_var('banner_title',
                             htmlspecialchars (stripslashes ($A['title'])));
    $banner_templates->set_var('lang_bannerid', $LANG_BANNER_ADMIN[2]);
    $banner_templates->set_var('lang_bannerurl', $LANG_BANNER_ADMIN[4]);
    $banner_templates->set_var('max_url_length', 255);
    $banner_templates->set_var('banner_url', $A['url']);
    $banner_templates->set_var('lang_includehttp', $LANG_BANNER_ADMIN[6]);
    $banner_templates->set_var('lang_category', $LANG_BANNER_ADMIN[5]);
    $othercategory = banner_select_box (3,$A['cid']);
    $banner_templates->set_var('category_options', $othercategory);
    $banner_templates->set_var('lang_ifotherspecify', $LANG_BANNER_ADMIN[20]);
    $banner_templates->set_var('category', $othercategory);
    $banner_templates->set_var('lang_publishstart', $LANG_BANNER_ADMIN[61]);
    $banner_templates->set_var('publishstart', $A['publishstart']);
    $banner_templates->set_var('lang_publishend', $LANG_BANNER_ADMIN[62]);
    $banner_templates->set_var('publishend', $A['publishend']);
    $banner_templates->set_var('lang_helpdatetime', $LANG_BANNER_ADMIN[63]);
    $banner_templates->set_var('lang_bannerhits', $LANG_BANNER_ADMIN[8]);
    $banner_templates->set_var('banner_hits', $A['hits']);
    $banner_templates->set_var('lang_bannerdescription', $LANG_BANNER_ADMIN[9]);
    $banner_templates->set_var('banner_description', stripslashes($A['description']));
    $banner_templates->set_var('lang_save', $LANG_ADMIN['save']);
    $banner_templates->set_var('lang_cancel', $LANG_ADMIN['cancel']);

    // user access info
    $banner_templates->set_var('lang_accessrights', $LANG_ACCESS['accessrights']);
    $banner_templates->set_var('lang_owner', $LANG_ACCESS['owner']);
    $ownername = COM_getDisplayName ($A['owner_id']);
    $banner_templates->set_var('owner_username', DB_getItem($_TABLES['users'],
                             'username', "uid = {$A['owner_id']}"));
    $banner_templates->set_var('owner_name', $ownername);
    $banner_templates->set_var('owner', $ownername);
    $banner_templates->set_var('banner_ownerid', $A['owner_id']);
    $banner_templates->set_var('lang_group', $LANG_ACCESS['group']);
    $banner_templates->set_var('group_dropdown',
                             SEC_getGroupDropdown ($A['group_id'], $access));
    $banner_templates->set_var('lang_permissions', $LANG_ACCESS['permissions']);
    $banner_templates->set_var('lang_permissionskey', $LANG_ACCESS['permissionskey']);
    $banner_templates->set_var('permissions_editor', SEC_getPermissionsHTML($A['perm_owner'],$A['perm_group'],$A['perm_members'],$A['perm_anon']));
    $banner_templates->set_var('lang_lockmsg', $LANG_ACCESS['permmsg']);
    $banner_templates->set_var('gltoken_name', CSRF_TOKEN);
    $banner_templates->set_var('gltoken', SEC_createToken());
    $banner_templates->parse('output', 'editor');
    $retval .= $banner_templates->finish($banner_templates->get_var('output'));

    $retval .= COM_endBlock (COM_getBlockTemplate ('_admin_block', 'footer'));

    return $retval;
}

/**
* Saves banner to the database
*
* @param    string  $bid            ID for banner
* @param    string  $old_bid        old ID for banner
* @param    string  $cid            cid of category banner belongs to
* @param    string  $categorydd     Category banner belong to
* @param    string  $url            URL of banner to save
* @param    string  $description    Description of banner
* @param    string  $title          Title of banner
* @param    int     $hits           Number of hits for banner
* @param    int     $owner_id       ID of owner
* @param    int     $group_id       ID of group banner belongs to
* @param    int     $perm_owner     Permissions the owner has
* @param    int     $perm_group     Permissions the group has
* @param    int     $perm_members   Permissions members have
* @param    int     $perm_anon      Permissions anonymous users have
* @return   string                  HTML redirect or error message
* @global array core config vars
* @global array core group data
* @global array core table data
* @global array core user data
* @global array core msg data
* @global array banner plugin lang admin vars
*
*/
function savebanner ($bid, $old_bid, $cid, $categorydd, $url, $description, $title, $publishstart, $publishend, $hits, $owner_id, $group_id, $perm_owner, $perm_group, $perm_members, $perm_anon)
{
    global $_CONF, $_GROUPS, $_TABLES, $_USER, $MESSAGE, $LANG_BANNER_ADMIN, $_BAN_CONF;

    $retval = '';

    // Convert array values to numeric permission values
    if (is_array($perm_owner) OR is_array($perm_group) OR is_array($perm_members) OR is_array($perm_anon)) {
        list($perm_owner,$perm_group,$perm_members,$perm_anon) = SEC_getPermissionValues($perm_owner,$perm_group,$perm_members,$perm_anon);
    }

    // clean 'em up
    $description = addslashes (COM_checkHTML (COM_checkWords ($description)));
    $title = addslashes (COM_checkHTML (COM_checkWords ($title)));
    $cid = addslashes ($cid);
    //$description = str_replace('<p>','',$description);
    //$description = str_replace('</p>','',$description);

    if (empty ($owner_id)) {
        // this is new banner from admin, set default values
        $owner_id = $_USER['uid'];
        if (isset ($_GROUPS['Banner Admin'])) {
            $group_id = $_GROUPS['Banner Admin'];
        } else {
            $group_id = SEC_getFeatureGroup ('banner.edit');
        }
        $perm_owner = 3;
        $perm_group = 2;
        $perm_members = 2;
        $perm_anon = 2;
    }
    if (empty($publishstart)) { $publishstart = 'NULL'; } else { $publishstart = "'".$publishstart."'";}
    if (empty($publishend)) { $publishend = 'NULL'; } else { $publishend = "'".$publishend."'";}

    $bid = COM_sanitizeID($bid);
    $old_bid = COM_sanitizeID($old_bid);
    if (empty($bid)) {
        if (empty($old_bid)) {
            $bid = COM_makeSid();
        } else {
            $bid = $old_bid;
        }
    }

    // check for banner id change
    if (!empty($old_bid) && ($bid != $old_bid)) {
        // check if new bid is already in use
        if (DB_count($_TABLES['banner'], 'bid', $bid) > 0) {
            // TBD: abort, display editor with all content intact again
            $bid = $old_bid; // for now ...
        }
    }

    $access = 0;
    $old_bid = addslashes ($old_bid);
    if (DB_count ($_TABLES['banner'], 'bid', $old_bid) > 0) {
        $result = DB_query ("SELECT owner_id,group_id,perm_owner,perm_group,perm_members,perm_anon FROM {$_TABLES['banner']} WHERE bid = '{$old_bid}'");
        $A = DB_fetchArray ($result);
        $access = SEC_hasAccess ($A['owner_id'], $A['group_id'],
                $A['perm_owner'], $A['perm_group'], $A['perm_members'],
                $A['perm_anon']);
    } else {
        $access = SEC_hasAccess ($owner_id, $group_id, $perm_owner, $perm_group,
                $perm_members, $perm_anon);
    }
    if (($access < 3) || !SEC_inGroup($group_id)) {
        $display .= COM_siteHeader('menu', $MESSAGE[30])
                 . COM_showMessageText($MESSAGE[31], $MESSAGE[30])
                 . COM_siteFooter();
        COM_accessLog("User {$_USER['username']} tried to illegally submit or edit banner $bid.");
        echo $display;
        exit;
    } elseif (!empty($title) && !empty($description)) {

        if ($categorydd != $LANG_BANNER_ADMIN[7] && !empty($categorydd)) {
            $cid = addslashes ($categorydd);
        } else if ($categorydd != $LANG_BANNER_ADMIN[7]) {
            echo COM_refresh($_CONF['site_admin_url'] . '/plugins/banner/index.php');
        }

        DB_delete ($_TABLES['bannersubmission'], 'bid', $old_bid);
        DB_delete ($_TABLES['banner'], 'bid', $old_bid);

        DB_save ($_TABLES['banner'], 'bid,cid,url,description,title,date,publishstart,publishend,hits,owner_id,group_id,perm_owner,perm_group,perm_members,perm_anon', "'$bid','$cid','$url','$description','$title',NOW(),$publishstart,$publishend,'$hits',$owner_id,$group_id,$perm_owner,$perm_group,$perm_members,$perm_anon");
        // Get category for rdf check
        $category = DB_getItem ($_TABLES['bannercategories'],"category","cid='{$cid}'");
        COM_rdfUpToDateCheck ('banner', $category, $bid);

        return PLG_afterSaveSwitch (
            $_BAN_CONF['aftersave'],
            COM_buildURL ("{$_CONF['site_url']}/banner/portal.php?what=banner&item=$bid"),
            'banner',
            2
        );

    } else { // missing fields
        $retval .= COM_siteHeader('menu', $LANG_BANNER_ADMIN[1]);
        $retval .= COM_errorLog($LANG_BANNER_ADMIN[10],2);
        if (DB_count ($_TABLES['banner'], 'bid', $old_bid) > 0) {
            $retval .= editbanner ('edit', $old_bid);
        } else {
            $retval .= editbanner ('edit', '');
        }
        $retval .= COM_siteFooter();

        return $retval;
    }
}

/**
 * Banner banner
 * @global array core config vars
 * @global array core table data
 * @global array core user data
 * @global array core lang admin vars
 * @global array banner plugin lang vars
 * @global array core lang access vars
 */
function listbanner ()
{
    global $_CONF, $_TABLES, $LANG_ADMIN, $LANG_BANNER_ADMIN, $LANG_ACCESS,
           $_IMAGE_TYPE, $_BAN_CONF;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';

    $header_arr = array(      # display 'text' and use table field 'field'
        array('text' => $LANG_ADMIN['edit'], 'field' => 'edit', 'sort' => false),
        array('text' => $LANG_BANNER_ADMIN[2], 'field' => 'bid', 'sort' => true)
    );
    if (!isset($_BAN_CONF['admin_disptitle']) || (isset($_BAN_CONF['admin_disptitle']) && $_BAN_CONF['admin_disptitle'] === true)) {
        $header_arr[] = array('text' => $LANG_ADMIN['title'], 'field' => 'title', 'sort' => true);
    }
    if (!isset($_BAN_CONF['admin_dispdescription']) || (isset($_BAN_CONF['admin_dispdescription']) && $_BAN_CONF['admin_dispdescription'] === true)) {
        $header_arr[] = array('text' => $LANG_BANNER_ADMIN[9], 'field' => 'description', 'sort' => true);
    }
    if (!isset($_BAN_CONF['admin_dispaccess']) || (isset($_BAN_CONF['admin_dispaccess']) && $_BAN_CONF['admin_dispaccess'] === true)) {
        $header_arr[] = array('text' => $LANG_ACCESS['access'], 'field' => 'access', 'sort' => false);
    }
    if (!isset($_BAN_CONF['admin_dispcategory']) || (isset($_BAN_CONF['admin_dispcategory']) && $_BAN_CONF['admin_dispcategory'] === true)) {
        $header_arr[] = array('text' => $LANG_BANNER_ADMIN[14], 'field' => 'category', 'sort' => true);
    }
    if (!isset($_BAN_CONF['admin_disppublishstart']) || (isset($_BAN_CONF['admin_disppublishstart']) && $_BAN_CONF['admin_disppublishstart'] === true)) {
        $header_arr[] = array('text' => $LANG_BANNER_ADMIN[61], 'field' => 'publishstart', 'sort' => true);
    }
    if (!isset($_BAN_CONF['admin_disppublishend']) || (isset($_BAN_CONF['admin_disppublishend']) && $_BAN_CONF['admin_disppublishend'] === true)) {
        $header_arr[] = array('text' => $LANG_BANNER_ADMIN[62], 'field' => 'publishend', 'sort' => true);
    }
    if (!isset($_BAN_CONF['admin_disphits']) || (isset($_BAN_CONF['admin_disphits']) && $_BAN_CONF['admin_disphits'] === true)) {
        $header_arr[] = array('text' => $LANG_BANNER_ADMIN[8], 'field' => 'hits', 'sort' => true);
    }

    $menu_arr = array (
        array('url' => $_CONF['site_admin_url'] . '/plugins/banner/index.php?mode=edit',
              'text' => $LANG_BANNER_ADMIN[51])
    );

    $validate = '';
    if (isset($_GET['validate'])) {
        $token = SEC_createToken();
        $menu_arr[] = array('url' => $_CONF['site_admin_url'] . '/plugins/banner/index.php',
            'text' => $LANG_BANNER_ADMIN[53]);
        $dovalidate_url = $_CONF['site_admin_url'] . '/plugins/banner/index.php?validate=validate' . '&amp;'.CSRF_TOKEN.'='.$token;
        $dovalidate_text = $LANG_BANNER_ADMIN[58];
        $form_arr['top'] = COM_createLink($dovalidate_text, $dovalidate_url);
        if ($_GET['validate'] == 'enabled') {
            $header_arr[] = array('text' => $LANG_BANNER_ADMIN[27], 'field' => 'beforevalidate', 'sort' => false);
            $validate = '?validate=enabled';
        } else if ($_GET['validate'] == 'validate') {
            $header_arr[] = array('text' => $LANG_BANNER_ADMIN[27], 'field' => 'dovalidate', 'sort' => false);
            $validate = '?validate=validate&amp;'.CSRF_TOKEN.'='.$token;
        }
        $validate_help = $LANG_BANNER_ADMIN[59];
    } else {
        $menu_arr[] = array('url' => $_CONF['site_admin_url'] . '/plugins/banner/index.php?validate=enabled',
              'text' => $LANG_BANNER_ADMIN[26]);
        $form_arr = array();
        $validate_help = '';
    }

    $defsort_arr = array('field' => 'title', 'direction' => 'asc');

    $menu_arr[] = array('url' => $_CONF['site_admin_url'] . '/plugins/banner/bannercategory.php',
              'text' => $LANG_BANNER_ADMIN[50]);
    $menu_arr[] = array('url' => $_CONF['site_admin_url'] . '/plugins/banner/bannercategory.php?mode=edit',
              'text' => $LANG_BANNER_ADMIN[52]);
    $menu_arr[] = array('url' => $_CONF['site_admin_url'],
              'text' => $LANG_ADMIN['admin_home']);

    $retval .= COM_startBlock($LANG_BANNER_ADMIN[11], '',
                              COM_getBlockTemplate('_admin_block', 'header'));

    $retval .= ADMIN_createMenu($menu_arr, $LANG_BANNER_ADMIN[12] . $validate_help, plugin_geticon_banner());

    $text_arr = array(
        'has_extras' => true,
        'form_url' => $_CONF['site_admin_url'] . "/plugins/banner/index.php$validate"
    );

    $query_arr = array('table' => 'banner',
        'sql' => "SELECT b.bid AS bid, b.cid as cid, b.title AS title, "
            . "c.category AS category, b.url AS url, b.description AS description, "
            . "b.hits AS hits, b.publishstart AS publishstart, b.publishend AS publishend, "
            . "b.owner_id, b.group_id, b.perm_owner, b.perm_group, b.perm_members, b.perm_anon "
            . "FROM {$_TABLES['banner']} AS b "
            . "LEFT JOIN {$_TABLES['bannercategories']} AS c "
            . "ON b.cid=c.cid WHERE 1=1",
        'query_fields' => array('title', 'category', 'url', 'b.description', 'b.publishstart', 'b.publishend', 'b.hits'),
        'default_filter' => COM_getPermSql ('AND', 0, 3, 'l')
    );

    $retval .= ADMIN_list('banner', 'plugin_getBannerField_banner', $header_arr,
                    $text_arr, $query_arr, $defsort_arr, '', '', '', $form_arr);
    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    return $retval;
}

/**
* Delete a banner
*
* @param    string  $bid    id of banner to delete
* @param    string  $type   'submission' when attempting to delete a submission
* @return   string          HTML redirect
*
*/
function deleteBanner($bid, $type = '')
{
    global $_CONF, $_TABLES, $_USER;

    if (empty($type)) { // delete regular banner
        $result = DB_query("SELECT owner_id,group_id,perm_owner,perm_group,perm_members,perm_anon FROM {$_TABLES['banner']} WHERE bid ='$bid'");
        $A = DB_fetchArray($result);
        $access = SEC_hasAccess($A['owner_id'], $A['group_id'],
                    $A['perm_owner'], $A['perm_group'], $A['perm_members'],
                    $A['perm_anon']);
        if ($access < 3) {
            COM_accessLog("User {$_USER['username']} tried to illegally delete banner $bid.");
            return COM_refresh($_CONF['site_admin_url']
                               . '/plugins/banner/index.php');
        }

        DB_delete($_TABLES['banner'], 'bid', $bid);

        return COM_refresh($_CONF['site_admin_url']
                           . '/plugins/banner/index.php?msg=3');
    } elseif ($type == 'submission') {
        if (plugin_ismoderator_banner()) {
            DB_delete($_TABLES['bannersubmission'], 'bid', $bid);

            return COM_refresh($_CONF['site_admin_url']
                               . '/plugins/banner/index.php?msg=3');
        } else {
            COM_accessLog("User {$_USER['username']} tried to illegally delete banner submission $bid.");
        }
    } else {
        COM_errorLog("User {$_USER['username']} tried to illegally delete banner $bid of type $type.");
    }

    return COM_refresh($_CONF['site_admin_url'] . '/plugins/banner/index.php');
}

// MAIN
$mode = '';
if (isset ($_REQUEST['mode'])) {
    $mode = $_REQUEST['mode'];
}

if (($mode == $LANG_ADMIN['delete']) && !empty ($LANG_ADMIN['delete'])) {
    $bid = COM_applyFilter ($_POST['bid']);
    if (!isset ($bid) || empty ($bid)) {  // || ($bid == 0)
        COM_errorLog ('Attempted to delete banner bid=' . $bid );
        $display .= COM_refresh ($_CONF['site_admin_url'] . '/plugins/banner/index.php');
    } elseif (SEC_checkToken()) {
        $type = '';
        if (isset($_POST['type'])) {
            $type = COM_applyFilter($_POST['type']);
        }
        $display .= deleteBanner($bid, $type);
    } else {
        COM_accessLog("User {$_USER['username']} tried to illegally delete banner $bid and failed CSRF checks.");
        echo COM_refresh($_CONF['site_admin_url'] . '/index.php');
    }
} elseif (($mode == $LANG_ADMIN['save']) && !empty($LANG_ADMIN['save']) && SEC_checkToken()) {
    $cid = '';
    if (isset($_POST['cid'])) {
        $cid = $_POST['cid'];
    }
    $display .= savebanner (COM_applyFilter ($_POST['bid']),
            COM_applyFilter ($_POST['old_bid']),
            $cid, $_POST['categorydd'],
            $_POST['url'], $_POST['description'], $_POST['title'],
            COM_applyFilter ($_POST['publishstart']),
            COM_applyFilter ($_POST['publishend']),
            COM_applyFilter ($_POST['hits'], true),
            COM_applyFilter ($_POST['owner_id'], true),
            COM_applyFilter ($_POST['group_id'], true),
            $_POST['perm_owner'], $_POST['perm_group'],
            $_POST['perm_members'], $_POST['perm_anon']);
} else if ($mode == 'editsubmission') {
    $display .= COM_siteHeader ('menu', $LANG_BANNER_ADMIN[1]);
    $display .= editbanner ($mode, COM_applyFilter ($_GET['id']));
    $display .= COM_siteFooter ();
} else if ($mode == 'edit') {
    $display .= COM_siteHeader ('menu', $LANG_BANNER_ADMIN[1]);
    if (empty ($_GET['bid'])) {
        $display .= editbanner ($mode);
    } else {
        $display .= editbanner ($mode, COM_applyFilter ($_GET['bid']));
    }
    $display .= COM_siteFooter ();
} else { // 'cancel' or no mode at all
    $display .= COM_siteHeader('menu', $LANG_BANNER_ADMIN[11]);
    if (isset($_GET['msg'])) {
        $msg = COM_applyFilter($_GET['msg'], true);
        if ($msg > 0) {
            $display .= COM_showMessage($msg, 'banner');
        }
    }
    $display .= listbanner();
    $display .= COM_siteFooter();
}

echo $display;

?>
