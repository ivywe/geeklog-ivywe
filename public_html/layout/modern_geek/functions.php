<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog 2.0                                                               |
// +---------------------------------------------------------------------------+
// | functions.php                                                             |
// |                                                                           |
// | Functions implementing the theme API                                      |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000-2012 by the following authors:                         |
// |                                                                           |
// | Authors: Tony Bibbs        - tony AT tonybibbs DOT com                    |
// |          Mark Limburg      - mlimburg AT users DOT sourceforge DOT net    |
// |          Jason Whittenburg - jwhitten AT securitygeeks DOT com            |
// |          Dirk Haun         - dirk AT haun-online DOT de                   |
// |          Vincent Furia     - vinny01 AT users DOT sourceforge DOT net     |
// |          Rouslan Placella  - rouslan AT placella DOT com                  |
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

// this file can't be used on its own
if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.php') !== false) {
    die('This file can not be used on its own!');
}

/**
 * Return the configuration values for the theme
 */
function theme_config_modern_geek()
{
    return array(
        'image_type' => 'png',
        'doctype'    => 'html5',
        'etag' => true,
        'supported_version_theme' => '2.0.0' // support new theme format for the later Geeklog 2.0.0
    );
}

/**
 * Return an array of CSS files to be loaded
 */
function theme_css_modern_geek()
{
    global $_CONF, $LANG_DIRECTION;

    return array(
        array('file' => '/vendor/uikit/css' . $direction . '/uikit.gradient.css', 'attributes' => array('media' => 'all'), 'priority' => 80),
        array('file'       => '/layout/' . $_CONF['theme'] . '/css/add_to_uikit.css',  'priority' => 90),

         array('file' => '/layout/' . $_CONF['theme'] . '/css/compatible.css', 'priority' => 100),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/default.css', 'priority' => 101),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/common.css', 'priority' => 102),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/layout.css', 'priority' => 103),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/block.css', 'priority' => 104),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/option.css', 'priority' => 105),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/form.css', 'priority' => 106),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/story.css', 'priority' => 107),
         
         array('file' => '/layout/' . $_CONF['theme'] . '/css/article/article.css', 'priority' => 108),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/comment/comment.css', 'priority' => 109),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/navbar/navbar.css', 'priority' => 110),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/preferences/preferences.css', 'priority' => 111),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/search/search.css', 'priority' => 112),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/stats/stats.css', 'priority' => 113),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/submit/submit.css', 'priority' => 114),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/trackback/trackback.css', 'priority' => 115),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/users/users.css', 'priority' => 116),
         
         array('file' => '/layout/' . $_CONF['theme'] . '/css/admin/common.css', 'priority' => 117),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/admin/block.css', 'priority' => 118),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/admin/envcheck.css', 'priority' => 119),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/admin/group.css', 'priority' => 120),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/admin/lists.css', 'priority' => 121),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/admin/commandcontrol.css', 'priority' => 122),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/admin/plugins.css', 'priority' => 123),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/admin/story.css', 'priority' => 124),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/admin/topic.css', 'priority' => 125),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/admin/trackback.css', 'priority' => 126),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/admin/user.css', 'priority' => 127),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/admin/configuration.css', 'priority' => 128),
         
         array('file' => '/layout/' . $_CONF['theme'] . '/css/plugin/japanize.css', 'priority' => 129),
         array('file' => '/layout/' . $_CONF['theme'] . '/css/plugin/sitecalendar.css', 'priority' => 130),
         
         array('file' => '/layout/' . $_CONF['theme'] . '/css/tooltips/tooltips.css', 'priority' => 131),

        array('file' => '/layout/' . $_CONF['theme'] . '/custom.css', 'priority' => 200)
    );
}

/**
 * Return an array of JS libraries to be loaded
 */
function theme_js_libs_modern_geek()
{
    return array(
       array(
            'library' => 'jquery',
            'footer'  => false // Not requred, default = true
        )
    );
}

/**
 * Return an array of JS files to be loaded
 */
function theme_js_files_modern_geek()
{
    global $_CONF;

    return array(

       array(
            'file'      => '/layout/' . $_CONF['theme'] . '/javascript/script.js',
            'footer'    => false, // Not requred, default = true
            'priority'  => 100 // Not requred, default = 100
        ),
       array(
            'file'      => '/vendor/uikit/js/uikit.js',
            'footer'    => false, // Not requred, default = true
            'priority'  => 110 // Not requred, default = 100
        ),
       array(
            'file'      => '/vendor/uikit/js/components/datepicker.js',
            'footer'    => false, // Not requred, default = true
            'priority'  => 120 // Not requred, default = 100
        ),
       array(
            'file'      => '/vendor/uikit/js/components/form-password.js',
            'footer'    => false, // Not requred, default = true
            'priority'  => 130 // Not requred, default = 100
        ),
       array(
            'file'      => '/vendor/uikit/js/components/form-select.js',
            'footer'    => false, // Not requred, default = true
            'priority'  => 140 // Not requred, default = 100
        ),
       array(
            'file'      => '/vendor/uikit/js/components/grid.js',
            'footer'    => false, // Not requred, default = true
            'priority'  => 150 // Not requred, default = 100
        ),
       array(
            'file'      => '/vendor/uikit/js/components/lightbox.js',
            'footer'    => false, // Not requred, default = true
            'priority'  => 160 // Not requred, default = 100
        ),
       array(
            'file'      => '/vendor/uikit/js/components/slideshow.js',
            'footer'    => false, // Not requred, default = true
            'priority'  => 170 // Not requred, default = 100
        ),
       array(
            'file'      => '/vendor/uikit/js/components/slideshow-fx.js',
            'footer'    => false, // Not requred, default = true
            'priority'  => 180 // Not requred, default = 100
        ),
       array(
            'file'      => '/vendor/uikit/js/components/slideset.js',
            'footer'    => false, // Not requred, default = true
            'priority'  => 190 // Not requred, default = 100
        ),
       array(
            'file'      => '/vendor/uikit/js/components/sticky.js',
            'footer'    => false, // Not requred, default = true
            'priority'  => 200 // Not requred, default = 100
        ),
       array(
            'file'      => '/vendor/uikit/js/components/tooltip.js',
            'footer'    => false, // Not requred, default = true
            'priority'  => 210 // Not requred, default = 100
        ),
       array(
            'file'      => '/layout/' . $_CONF['theme'] . '/javascript/theme.js',
            'footer'    => true, // Not requred, default = true
            'priority'  => 220 // Not requred, default = 100
        ),
    );
}

/**
 * Do any other initialisation here
 */
function theme_init_modern_geek()
{
    global $_BLOCK_TEMPLATE, $_CONF;

    $_CONF['left_blocks_in_footer'] = 1;

    /*
     * For left/right block support there is no longer any need for the theme to
     * put code into functions.php to set specific templates for the left/right
     * versions of blocks. Instead, Geeklog will automagically look for
     * blocktemplate-left.thtml and blocktemplate-right.thtml if given
     * blocktemplate.thtml from $_BLOCK_TEMPLATE. So, if you want different left
     * and right templates from admin_block, just create blockheader-list-left.thtml
     * etc.
     */

    $_BLOCK_TEMPLATE['_msg_block'] = 'blockheader-message.thtml,blockfooter-message.thtml';
    $_BLOCK_TEMPLATE['configmanager_block'] = 'blockheader-config.thtml,blockfooter-config.thtml';
    $_BLOCK_TEMPLATE['configmanager_subblock'] = 'blockheader-config.thtml,blockfooter-config.thtml';
    $_BLOCK_TEMPLATE['whats_related_block'] = 'blockheader-related.thtml,blockfooter-related.thtml';
    $_BLOCK_TEMPLATE['story_options_block'] = 'blockheader-related.thtml,blockfooter-related.thtml';

    // Define the blocks that are a list of links styled as an unordered list - using class="blocklist"
    $_BLOCK_TEMPLATE['admin_block'] = 'blockheader-list.thtml,blockfooter-list.thtml';
    $_BLOCK_TEMPLATE['section_block'] = 'blockheader-list.thtml,blockfooter-list.thtml';

    if (! COM_isAnonUser()) {
        $_BLOCK_TEMPLATE['user_block'] = 'blockheader-user.thtml,blockfooter-user.thtml';
    } else {
        $_BLOCK_TEMPLATE['user_block'] = 'blockheader-user-login.thtml,blockfooter-user-login.thtml';
    }
}

?>
