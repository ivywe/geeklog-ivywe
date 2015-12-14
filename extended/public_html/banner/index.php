<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Banner Plugin 1.1                                                         |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// |                                                                           |
// | This is the main page for the Geeklog Banner Plugin                       |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000-2010 by the following authors:                         |
// |                                                                           |
// | Authors: Tony Bibbs        - tony AT tonybibbs DOT com                    |
// |          Mark Limburg      - mlimburg AT users DOT sourceforge DOT net    |
// |          Jason Whittenburg - jwhitten AT securitygeeks DOT com            |
// |          Tom Willett       - tomw AT pigstye DOT net                      |
// |          Trinity Bays      - trinity93 AT gmail DOT com                   |
// |          Dirk Haun         - dirk AT haun-online DOT de                   |
// |          Hiroron           - hiroron AT hiroron DOT com                   |
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

require_once '../lib-common.php';

if (!in_array('banner', $_PLUGINS)) {
    echo COM_refresh($_CONF['site_url'] . '/index.php');
    exit;
}

/**
* Create the banner list depending on the category given
*
* @param    array   $message    message(s) to display
* @return   string              the banner page
*
*/
function banner_list($message)
{
    global $_CONF, $_TABLES, $_BAN_CONF, $LANG_BANNER_ADMIN, $LANG_BANNER,
           $LANG_BANNER_STATS;

    $cid = $_BAN_CONF['root'];
    $display = '';
    if (isset($_GET['category'])) {
        $cid = strip_tags(COM_stripslashes($_GET['category']));
    } elseif (isset($_POST['category'])) {
        $cid = strip_tags(COM_stripslashes($_POST['category']));
    }
    $cat = addslashes($cid);
    $page = 0;
    if (isset ($_GET['page'])) {
        $page = COM_applyFilter ($_GET['page'], true);
    }
    if ($page == 0) {
        $page = 1;
    }

    if (empty($cid)) {
        if ($page > 1) {
            $page_title = sprintf ($LANG_BANNER[114] . ' (%d)', $page);
        } else {
            $page_title = $LANG_BANNER[114];
        }
    } else {
        if ($cid == $_BAN_CONF['root']) {
            $category = $LANG_BANNER['root'];
        } else {
            $category = DB_getItem($_TABLES['bannercategories'], 'category',
                                   "cid = '{$cat}'");
        }
        if ($page > 1) {
            $page_title = sprintf ($LANG_BANNER[114] . ': %s (%d)', $category,
                                                                   $page);
        } else {
            $page_title = sprintf ($LANG_BANNER[114] . ': %s', $category);
        }
    }
    
    // Check has access to this category
    if ($cid != $_BAN_CONF['root']) {
        $result = DB_query("SELECT owner_id,group_id,perm_owner,perm_group,perm_members,perm_anon FROM {$_TABLES['bannercategories']} WHERE cid='{$cat}'");
        $A = DB_fetchArray($result);
        if (SEC_hasAccess ($A['owner_id'], $A['group_id'], $A['perm_owner'], $A['perm_group'], $A['perm_members'], $A['perm_anon']) < 2) {
            $display .= COM_siteHeader ('menu', $page_title);
            $display .= COM_showMessage (5, 'banner');
            $display .= COM_siteFooter ();
            echo $display;
            exit;
        }
    }

    $display .= COM_siteHeader ('menu', $page_title);

    if (is_array($message) && !empty($message[0])) {
        $display .= COM_startBlock($message[0], '',
                                 COM_getBlockTemplate('_msg_block', 'header'));
        $display .= $message[1];
        $display .= COM_endBlock(COM_getBlockTemplate('_msg_block', 'footer'));
    } else if (isset($_REQUEST['msg'])) {
        $msg = COM_applyFilter($_REQUEST['msg'], true);
        if ($msg > 0) {
            $display .= COM_showMessage($msg, 'banner');
        }
    }

    $bannerlist = new Template ($_CONF['path'] . 'plugins/banner/templates/');
    $bannerlist->set_file (array ('bannerlist' => 'banner.thtml',
                                'catbanner' => 'categorybanner.thtml',
                                'banner'     => 'bannerdetails.thtml',
                                'catnav'   => 'categorynavigation.thtml',
                                'catrow'   => 'categoryrow.thtml',
                                'catcol'   => 'categorycol.thtml',
                                'actcol'   => 'categoryactivecol.thtml',
                                'pagenav'  => 'pagenavigation.thtml',
                                'catdrop'  => 'categorydropdown.thtml'));
    $bannerlist->set_var('xhtml', XHTML);
    $bannerlist->set_var('blockheader', COM_startBlock($LANG_BANNER[114]));
    $bannerlist->set_var('layout_url', $_CONF['layout_url']);

    if ($_BAN_CONF['bannercols'] > 0) {
        // Create breadcrumb trail
        $bannerlist->set_var('breadcrumbs',
                           banner_breadcrumbs($_BAN_CONF['root'], $cid));

        // Set dropdown for category jump
        $bannerlist->set_var('lang_go', $LANG_BANNER[124]);
        $bannerlist->set_var('banner_dropdown', banner_select_box(2, $cid));

        // Show categories
        $sql = "SELECT cid,pid,category,description FROM {$_TABLES['bannercategories']} WHERE pid='{$cat}'";
        $sql .= COM_getLangSQL('cid', 'AND');
        $sql .= COM_getPermSQL('AND') . " ORDER BY category";
        $result = DB_query($sql);
        $nrows  = DB_numRows ($result);
        if ($nrows > 0) {
            $bannerlist->set_var ('lang_categories', $LANG_BANNER_ADMIN[14]);
            for ($i = 1; $i <= $nrows; $i++) {
                $C = DB_fetchArray($result);
                // Get number of child banner user can see in this category
                $ccid = addslashes($C['cid']);
                $result1 = DB_query("SELECT COUNT(*) AS count FROM {$_TABLES['banner']} WHERE cid='{$ccid}'" . COM_getPermSQL('AND'));
                $D = DB_fetchArray($result1);

                // Get number of child categories user can see in this category
                $result2 = DB_query("SELECT COUNT(*) AS count FROM {$_TABLES['bannercategories']} WHERE pid='{$ccid}'" . COM_getPermSQL('AND'));
                $E = DB_fetchArray($result2);

                // Format numbers for display
                $display_count = '';
                // don't show zeroes
                if ($E['count']>0) {
                    $display_count = COM_numberFormat ($E['count']);
                }
                if (($E['count']>0) && ($D['count']>0)) {
                    $display_count .= ', ';
                }
                if ($D['count']>0) {
                    $display_count .= COM_numberFormat ($D['count']);
                }
                // add brackets if child items exist
                if ($display_count<>'') {
                    $display_count = '('.$display_count.')';
                }

                $bannerlist->set_var ('category_name', $C['category']);
                if ($_BAN_CONF['show_category_descriptions']) {
                    $bannerlist->set_var ('category_description', $C['description']);
                } else {
                    $bannerlist->set_var ('category_description', '');
                }
                $bannerlist->set_var ('category_link', $_CONF['site_url'] .
                    '/banner/index.php?category=' . urlencode ($C['cid']));
                $bannerlist->set_var ('category_count', $display_count);
                $bannerlist->set_var ('width', floor (100 / $_BAN_CONF['bannercols']));
                if (!empty($cid) && ($cid == $C['cid'])) {
                    $bannerlist->parse ('category_col', 'actcol', true);
                } else {
                    $bannerlist->parse ('category_col', 'catcol', true);
                }
                if ($i % $_BAN_CONF['bannercols'] == 0) {
                    $bannerlist->parse ('category_row', 'catrow', true);
                    $bannerlist->set_var ('category_col', '');
                }
            }
            if ($nrows % $_BAN_CONF['bannercols'] != 0) {
                $bannerlist->parse ('category_row', 'catrow', true);
            }
            $bannerlist->parse ('category_navigation', 'catnav', true);
        } else {
            $bannerlist->set_var ('category_navigation', '');
        }
    } else {
        $bannerlist->set_var ('category_navigation', '');
    }
    if ($_BAN_CONF['bannercols'] == 0) {
        $bannerlist->set_var('category_dropdown', '');
    } else {
        $bannerlist->parse('category_dropdown', 'catdrop', true);
    }

    $bannerlist->set_var('site_url', $_CONF['site_url']);
    $bannerlist->set_var('cid', $cid);
    $bannerlist->set_var('cid_plain', $cid);
    $bannerlist->set_var('cid_encoded', urlencode($cid));
    $bannerlist->set_var('lang_addabanner', $LANG_BANNER[116]);

    // Build SQL for banner
    $sql = 'SELECT bid,cid,url,description,title,hits,owner_id,group_id,perm_owner,perm_group,perm_members,perm_anon';
    $from_where = " FROM {$_TABLES['banner']}";
    if ($_BAN_CONF['bannercols'] > 0) {
        if (!empty($cid)) {
            $from_where .= " WHERE cid='" . addslashes($cid) . "'";
        } else {
            $from_where .= " WHERE cid=''";
        }
        $from_where .= ' AND (publishstart IS NULL OR publishstart < NOW()) and (publishend IS NULL OR publishend > NOW())';
        $from_where .= COM_getPermSQL ('AND');
    } else {
        $from_where .= COM_getPermSQL ();
    }
    $order = ' ORDER BY cid ASC,title';
    $limit = '';
    if ($_BAN_CONF['bannerperpage'] > 0) {
        if ($page < 1) {
            $start = 0;
        } else {
            $start = ($page - 1) * $_BAN_CONF['bannerperpage'];
        }
        $limit = ' LIMIT ' . $start . ',' . $_BAN_CONF['bannerperpage'];
    }
    $result = DB_query ($sql . $from_where . $order . $limit);
    $nrows = DB_numRows ($result);

    if ($nrows == 0) {
        if (($cid == $_BAN_CONF['root']) && ($page <= 1) && $_BAN_CONF['show_top10']) {
            $result = DB_query ("SELECT bid,url,title,description,hits,owner_id,group_id,perm_owner,perm_group,perm_members,perm_anon FROM {$_TABLES['banner']} WHERE (hits > 0) AND (publishstart IS NULL OR publishstart < NOW()) and (publishend IS NULL OR publishend > NOW())" . COM_getPermSQL ('AND') . " ORDER BY hits DESC LIMIT 10");
            $nrows  = DB_numRows ($result);
            if ($nrows > 0) {
                $bannerlist->set_var ('banner_details', '');
                $bannerlist->set_var ('banner_category',
                                    $LANG_BANNER_STATS['stats_headline']);
                for ($i = 0; $i < $nrows; $i++) {
                    $A = DB_fetchArray ($result);
                    prepare_banner_item ($A, $bannerlist);
                    $bannerlist->parse ('banner_details', 'banner', true);
                }
                $bannerlist->parse ('category_banner', 'catbanner', true);
            }
        }
        $bannerlist->set_var ('page_navigation', '');
    } else {
        $currentcid = '';
        for ($i = 0; $i < $nrows; $i++) {
            $A = DB_fetchArray($result);
            if (strcasecmp ($A['cid'], $currentcid) != 0) {
                // print the category and banner
                if ($i > 0) {
                    $bannerlist->parse('category_banner', 'catbanner', true);
                    $bannerlist->set_var('banner_details', '');
                }
                $currentcid = $A['cid'];
                $currentcategory = DB_getItem($_TABLES['bannercategories'],
                        'category', "cid = '" . addslashes($currentcid) . "'");
                $bannerlist->set_var('banner_category', $currentcategory);
            }

            prepare_banner_item($A, $bannerlist);
            $bannerlist->parse('banner_details', 'banner', true);
        }
        $bannerlist->parse('category_banner', 'catbanner', true);

        $result = DB_query ('SELECT COUNT(*) AS count ' . $from_where);
        list($numbanner) = DB_fetchArray ($result);
        $pages = 0;
        if ($_BAN_CONF['bannerperpage'] > 0) {
            $pages = (int) ($numbanner / $_BAN_CONF['bannerperpage']);
            if (($numbanner % $_BAN_CONF['bannerperpage']) > 0 ) {
                $pages++;
            }
        }
        if ($pages > 0) {
            if (($_BAN_CONF['bannercols'] > 0) && !empty($currentcid)) {
                $catbanner = '?category=' . urlencode($currentcid);
            } else {
                $catbanner = '';
            }
            $bannerlist->set_var('page_navigation',
                    COM_printPageNavigation($_CONF['site_url']
                        . '/banner/index.php' . $catbanner, $page, $pages));
        } else {
            $bannerlist->set_var ('page_navigation', '');
        }
    }
    $bannerlist->set_var ('blockfooter',COM_endBlock());
    $bannerlist->parse ('output', 'bannerlist');
    $display .= $bannerlist->finish ($bannerlist->get_var ('output'));

    return $display;
}


/**
* Prepare a banner item for rendering
*
* @param    array   $A          banner details
* @param    ref     $template   reference of the banner template
*
*/
function prepare_banner_item ($A, &$template)
{
    global $_CONF, $_USER, $LANG_ADMIN, $LANG_BANNER, $_IMAGE_TYPE, $LANG_DIRECTION;

    $url = COM_buildUrl ($_CONF['site_url']
                 . '/banner/portal.php?what=banner&amp;item=' . $A['bid']);
    $template->set_var ('banner_url', $url);
    $template->set_var ('banner_actual_url', $A['url']);
    $template->set_var ('banner_actual_url_encoded', urlencode($A['url']));
    $template->set_var ('banner_name', stripslashes ($A['title']));
    $template->set_var ('banner_name_encoded', urlencode($A['title']));
    $template->set_var ('banner_hits', COM_numberFormat ($A['hits']));
    $content = stripslashes ($A['title']);
    $template->set_var ('banner_html', $content);
    if (!COM_isAnonUser() && !SEC_hasRights('banner.edit')) {
        $reporturl = $_CONF['site_url']
                 . '/banner/index.php?mode=report&amp;bid=' . $A['bid'];
        $template->set_var ('banner_broken',
                COM_createLink($LANG_BANNER[117], $reporturl,
                               array('class' => 'pluginSmallText',
                                     'rel'   => 'nofollow'))
        );
    } else {
        $template->set_var ('banner_broken', '');
    }
    $bannerimg = nl2br (stripslashes ($A['description']));
    $flg_link = empty($A['url']) ? false : true ;
    $banner = banner_buildBanner($A['bid'], $content, $bannerimg, $flg_link);
    $template->set_var ('banner_description', $banner);

    if ((SEC_hasAccess ($A['owner_id'], $A['group_id'], $A['perm_owner'],
            $A['perm_group'], $A['perm_members'], $A['perm_anon']) == 3) &&
            SEC_hasRights ('banner.edit')) {
        $editurl = $_CONF['site_admin_url']
                 . '/plugins/banner/index.php?mode=edit&amp;bid=' . $A['bid'];
        $template->set_var ('banner_edit', COM_createLink($LANG_ADMIN['edit'],$editurl));
        $edit_icon = "<img src=\"{$_CONF['layout_url']}/images/edit.$_IMAGE_TYPE\" "
            . "alt=\"{$LANG_ADMIN['edit']}\" title=\"{$LANG_ADMIN['edit']}\"" . XHTML . ">";
        $template->set_var ('edit_icon', COM_createLink($edit_icon, $editurl));
    } else {
        $template->set_var ('banner_edit', '');
        $template->set_var ('edit_icon', '');
    }
}


// MAIN

$display = '';
$mode = '';
$root = $_BAN_CONF['root'];
if (isset ($_REQUEST['mode'])) {
    $mode = $_REQUEST['mode'];
}

$message = array();
if (($mode == 'report') && (isset($_USER['uid']) && ($_USER['uid'] > 1))) {
    if (isset ($_GET['bid'])) {
        $bid = COM_applyFilter($_GET['bid']);
    }
    if (!empty($bid)) {
        $bidsl = addslashes($bid);
        $result = DB_query("SELECT url, title FROM {$_TABLES['banner']} WHERE bid = '$bidsl'");
        list($url, $title) = DB_fetchArray($result);

        $editurl = $_CONF['site_admin_url']
                 . '/plugins/banner/index.php?mode=edit&bid=' . $bid;
        $msg = $LANG_BANNER[119] . LB . LB . "$title, <$url>". LB . LB
             .  $LANG_BANNER[120] . LB . '<' . $editurl . '>' . LB . LB
             .  $LANG_BANNER[121] . $_USER['username'] . ', IP: '
             . $_SERVER['REMOTE_ADDR'];
        COM_mail($_CONF['site_mail'], $LANG_BANNER[118], $msg);
        $message = array($LANG_BANNER[123], $LANG_BANNER[122]);
    }
}

if (empty ($_USER['username']) &&
    (($_CONF['loginrequired'] == 1) || ($_BAN_CONF['bannerloginrequired'] == 1))) {
    $display .= COM_siteHeader ('menu', $LANG_BANNER[114]);
    $display .= COM_startBlock ($LANG_LOGIN[1], '',
                                COM_getBlockTemplate ('_msg_block', 'header'));
    $login = new Template ($_CONF['path_layout'] . 'submit');
    $login->set_file (array ('login' => 'submitloginrequired.thtml'));
    $login->set_var ( 'xhtml', XHTML );
    $login->set_var ('login_message', $LANG_LOGIN[2]);
    $login->set_var ('site_url', $_CONF['site_url']);
    $login->set_var ('lang_login', $LANG_LOGIN[3]);
    $login->set_var ('lang_newuser', $LANG_LOGIN[4]);
    $login->parse ('output', 'login');
    $display .= $login->finish ($login->get_var ('output'));
    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
} else {
    $display .= banner_list($message);
}

$display .= COM_siteFooter ();

echo $display;

?>
