<?php

// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | Banner Plugin 1.1                                                         |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// |                                                                           |
// | Initial Installation Defaults used when loading the online configuration  |
// | records. These settings are only used during the initial installation     |
// | and not referenced any more once the plugin is installed.                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000-2010 by the following authors:                         |
// |                                                                           |
// | Authors: Hiroron            - hiroron AT hiroron DOT com                  |
// |          Tony Bibbs         - tony AT tonybibbs DOT com                   |
// |          Mark Limburg       - mlimburg AT users.sourceforge DOT net       |
// |          Jason Whittenburg  - jwhitten AT securitygeeks DOT com           |
// |          Dirk Haun          - dirk AT haun-online DOT de                  |
// |          Trinity Bays       - trinity93 AT gmail DOT com                  |
// |          Oliver Spiesshofer - oliver AT spiesshofer DOT com               |
// |          Euan McKay         - info AT heatherengineering DOT com          |
// | Presented by:IvyWe          - http://www.ivywe.co.jp                      |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is licensed under the terms of the GNU General Public License|
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                      |
// | See the GNU General Public License for more details.                      |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

if (strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

/*
 * Banner default settings
 *
 * Initial Installation Defaults used when loading the online configuration
 * records. These settings are only used during the initial installation
 * and not referenced any more once the plugin is installed
 *
 */

/**
* the banner plugin's config array
*/
global $_BAN_DEFAULT;
$_BAN_DEFAULT = array();

/**
 * It is possible to develop the banner by this in the template or to select it.
 */
$_BAN_DEFAULT['bannertemplatevariables'] = 0;

/**
 * this lets you select which functions are available for registered users only
 */
$_BAN_DEFAULT['bannerloginrequired'] = 0;

/**
 * Submission Settings
 * enable (set to 1) or disable (set to 0) submission queues:
 */
$_BAN_DEFAULT['bannersubmission']  = 1;

/**
 * Following times are in seconds
 */
$_BAN_DEFAULT['newbannerinterval']    = 1209600; // = 14 days

/**
 * Set to 1 to hide a section from the What's New block:
 */
$_BAN_DEFAULT['hidenewbanner']    = 0;

/**
 * Set to 1 to hide the "Banner" entry from the top menu:
 */
$_BAN_DEFAULT['hidebannermenu']    = 0;

/**
 * categories per column
 * You can set this and $_BAN_DEFAULT['bannerperpage'] to 0 to get back the old
 * (pre-1.3.6) style of the banner section. Setting only bannercols to 0 will hide
 * the categories but keep the paging. Setting only bannerperpage to 0 will list
 * all the banner of the selected category on one page.
 */
$_BAN_DEFAULT['bannercols']     =  3;

/**
 * banner per page
 * You can set this and $_BAN_DEFAULT['bannercols'] to 0 to get back the old
 * (pre-1.3.6) style of the banner section. Setting only bannercols to 0 will hide
 * the categories but keep the paging. Setting only bannerperpage to 0 will list
 * all the banner of the selected category on one page.
 */
$_BAN_DEFAULT['bannerperpage'] = 10;

/**
 * show top ten banner
 * Whether to show the Top Ten Banner on the main page or not.
 */
$_BAN_DEFAULT['show_top10']   = true;

/**
 * notify when a new banner was submitted
 */
$_BAN_DEFAULT['notification'] = 0;

/**
 * should we remove banner submitted by users if account is removed? (1)
 * or change owner to root (0)
 */
$_BAN_DEFAULT['delete_banner'] = 0;

/** What to show after a banner has been saved? Possible choices:
 * 'item' -> forward to the target of the banner
 * 'list' -> display the admin-list of banner
 * 'plugin' -> display the public homepage of the banner plugin
 * 'home' -> display the site homepage
 * 'admin' -> display the site admin homepage
 */
$_BAN_DEFAULT['aftersave'] = 'list';

/**
 * show category descriptions
 * Whether to show subcategory descriptions when viewing a category or not.
 */
$_BAN_DEFAULT['show_category_descriptions'] = true;

/**
 * Banner root category id
 */
$_BAN_DEFAULT['root'] = 'site';

/**
 * Edit link to Banner dipslay if you have admin
 */
$_BAN_DEFAULT['admin_editlink'] = true;

/**
 * Disp Item Admin List
 */
$_BAN_DEFAULT['admin_disptitle'] = true;
$_BAN_DEFAULT['admin_dispdescription'] = true;
$_BAN_DEFAULT['admin_dispaccess'] = true;
$_BAN_DEFAULT['admin_dispcategory'] = true;
$_BAN_DEFAULT['admin_disppublishstart'] = true;
$_BAN_DEFAULT['admin_disppublishend'] = true;
$_BAN_DEFAULT['admin_disphits'] = true;

/**
 * Define default permissions for new banner created from the Admin panel.
 * Permissions are perm_owner, perm_group, perm_members, perm_anon (in that
 * order). Possible values:<br>
 * - 3 = read + write permissions (perm_owner and perm_group only)
 * - 2 = read-only
 * - 0 = neither read nor write permissions
 * (a value of 1, ie. write-only, does not make sense and is not allowed)
 */
$_BAN_DEFAULT['default_permissions'] = array (3, 2, 2, 2);


/**
* Initialize Banner plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. Initial values will be taken from $_BAN_CONF if available (e.g. from
* an old config.php), uses $_BAN_DEFAULT otherwise.
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_banner()
{
    global $_BAN_CONF, $_BAN_DEFAULT;

    if (is_array($_BAN_CONF) && (count($_BAN_CONF) > 1)) {
        $_BAN_DEFAULT = array_merge($_BAN_DEFAULT, $_BAN_CONF);
    }

    $me = 'banner';
    $c = config::get_instance();
    if (!$c->group_exists($me)) {

        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, $me);
        $c->add('fs_public', NULL, 'fieldset', 0, 0, NULL, 0, true, $me);
        $c->add('bannerloginrequired', $_BAN_DEFAULT['bannerloginrequired'], 'select', 0, 0, 0, 10, true, $me);
        $c->add('bannercols', $_BAN_DEFAULT['bannercols'], 'text', 0, 0, 0, 20, true, $me);
        $c->add('bannerperpage', $_BAN_DEFAULT['bannerperpage'], 'text', 0, 0, 0, 30, true, $me);
        $c->add('show_top10', $_BAN_DEFAULT['show_top10'], 'select', 0, 0, 1, 40, true, $me);
        $c->add('show_category_descriptions', $_BAN_DEFAULT['show_category_descriptions'], 'select', 0, 0, 1, 50, true, $me);

        $c->add('fs_admin', NULL, 'fieldset', 0, 1, NULL, 0, true, $me);
        $c->add('bannertemplatevariables', $_BAN_DEFAULT['bannertemplatevariables'], 'select', 0, 1, 0, 60, true, $me);        
        $c->add('hidenewbanner', $_BAN_DEFAULT['hidenewbanner'], 'select', 0, 1, 0, 70, true, $me);
        $c->add('newbannerinterval', $_BAN_DEFAULT['newbannerinterval'], 'text', 0, 1, 0, 80, true, $me);
        $c->add('hidebannermenu', $_BAN_DEFAULT['hidebannermenu'], 'select', 0, 1, 0, 90, true, $me);
        $c->add('bannersubmission', $_BAN_DEFAULT['bannersubmission'], 'select', 0, 1, 0, 100, true, $me);
        $c->add('notification', $_BAN_DEFAULT['notification'], 'select', 0, 1, 0, 110, true, $me);
        $c->add('delete_banner', $_BAN_DEFAULT['delete_banner'], 'select', 0, 1, 0, 120, true, $me);
        $c->add('aftersave', $_BAN_DEFAULT['aftersave'], 'select', 0, 1, 9, 130, true, $me);
        $c->add('root', $_BAN_DEFAULT['root'], 'text', 0, 1, 0, 140, true, $me);
        $c->add('admin_editlink', $_BAN_DEFAULT['admin_editlink'], 'select', 0, 1, 1, 150, true, $me);

        $c->add('fs_adminlist', NULL, 'fieldset', 0, 2, NULL, 0, true, $me);
        $c->add('admin_disptitle', $_BAN_DEFAULT['admin_disptitle'], 'select', 0, 2, 1, 200, true, $me);
        $c->add('admin_dispdescription', $_BAN_DEFAULT['admin_dispdescription'], 'select', 0, 2, 1, 210, true, $me);
        $c->add('admin_dispaccess', $_BAN_DEFAULT['admin_dispaccess'], 'select', 0, 2, 1, 220, true, $me);
        $c->add('admin_dispcategory', $_BAN_DEFAULT['admin_dispcategory'], 'select', 0, 2, 1, 230, true, $me);
        $c->add('admin_disppublishstart', $_BAN_DEFAULT['admin_disppublishstart'], 'select', 0, 2, 1, 240, true, $me);
        $c->add('admin_disppublishend', $_BAN_DEFAULT['admin_disppublishend'], 'select', 0, 2, 1, 250, true, $me);
        $c->add('admin_disphits', $_BAN_DEFAULT['admin_disphits'], 'select', 0, 2, 1, 260, true, $me);

        $c->add('fs_permissions', NULL, 'fieldset', 0, 3, NULL, 0, true, $me);
        $c->add('default_permissions', $_BAN_DEFAULT['default_permissions'], '@select', 0, 3, 12, 300, true, $me);

    }

    return true;
}

?>
