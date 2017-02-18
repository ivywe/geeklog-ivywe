<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog 2.1                                                               |
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
function theme_config_default()
{
    return array(
        'image_type' => 'png',
        'doctype'    => 'xhtml5',
        'etag'       => false, // never set this true. instead use $options['enable_etag'] above.
        'supported_version_theme' => '2.0.0', // support new theme format for the later Geeklog 2.0.0
        'theme_plugins' => 'default', // Not requred, you can specify compatible theme of template stored with some plugins
        'options'    => $options // Not requred, some options of this theme
    );
}

/**
 * Return an array of CSS files to be loaded
 */
function theme_css_default()
{
    global $_CONF, $LANG_DIRECTION;

    $direction = ($LANG_DIRECTION == 'rtl') ? '_rtl' : '';

    return array(
        array('file' => '/layout/' . $_CONF['theme'] . '/vendor/uikit/css' . $direction . '/uikit.min.css', 'attributes' => array('media' => 'all'), 'priority' => 80),
        array('file' => '/layout/' . $_CONF['theme'] . '/vendor/uikit/css' . $direction . '/slider.min.css', 'attributes' => array('media' => 'all'), 'priority' => 81),
        array('file' => '/layout/' . $_CONF['theme'] . '/vendor/uikit/css' . $direction . '/slideshow.min.css', 'attributes' => array('media' => 'all'), 'priority' => 82),
        array('file' => '/layout/' . $_CONF['theme'] . '/vendor/uikit/css' . $direction . '/slidenav.min.css', 'attributes' => array('media' => 'all'), 'priority' => 83),
        array('file' => '/layout/' . $_CONF['theme'] . '/css_' . $LANG_DIRECTION . '/style.css', 'attributes' => array('media' => 'all'), 'priority' => 100),
        array('file' => '/layout/' . $_CONF['theme'] . '/css_' . $LANG_DIRECTION . '/custom.css', 'attributes' => array('media' => 'all'), 'priority' => 110), 
        array('file' => '/layout/' . $_CONF['theme'] . '/css_' . $LANG_DIRECTION . '/theme.css', 'attributes' => array('media' => 'all'), 'priority' => 120), 

    );
}

/**
 * Return an array of JS libraries to be loaded
 */
function theme_js_libs_default()
{
    return array(
       array(
            'library' => 'jquery',
            'footer'  => false // Not required, default = true
        )
    );
}

/**
 * Return an array of JS files to be loaded
 */
function theme_js_files_default()
{
    global $_CONF;

    $theme_var = theme_config_default();

    $result = array();


    $result[] = array(
        'file'     => '/layout/' . $_CONF['theme'] . '/vendor/uikit/js/uikit.min.js',
        'footer'   => false, // Not required, default = true
        'priority' => 100 // Not required, default = 100
    );
    $result[] = array(
        'file'     => '/layout/' . $_CONF['theme'] . '/javascript/script.js',
        'footer'   => true, // Not required, default = true
        'priority' => 110 // Not required, default = 100
    );
    $result[] = array(
        'file'     => '/layout/' . $_CONF['theme'] .'/vendor/uikit/js/migrate.min.js',
        'footer'   => false, // Not required, default = true
        'priority' => 120 // Not required, default = 100
    );

    return $result;
}

/**
 * Do any other initialisation here
 */
function theme_init_default()
{
    global $_BLOCK_TEMPLATE, $_CONF;

    $_CONF['left_blocks_in_footer'] = 1;
    
    $_CONF['theme_oauth_icons'] = 0; // Default is false (not required). Will use Geeklogs own OAuth icons for login form else use icons in theme images directory

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

    if (!COM_isAnonUser()) {
        $_BLOCK_TEMPLATE['user_block'] = 'blockheader-list.thtml,blockfooter-list.thtml';
    }
}
