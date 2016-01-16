<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Language Selection Block Plugin 1.0.0                                     |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// |                                                                           |
// | This file does two things: 1) it implements the necessary Geeklog Plugin  |
// | API methods and 2) implements all the common code needed by this plugin.  |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011 by the following authors:                              |
// |                                                                           |
// | Authors: Rouslan Placella - rouslan AT placella DOT com                   |
// +---------------------------------------------------------------------------+
// | Created with the Geeklog Plugin Toolkit.                                  |
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

/**
* @package LanguageSelectionBlock
*/

if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die ('This file can not be used on its own.');
}

/**
 * Language file include
 */
$plugin_path = $_CONF['path'] . 'plugins/langsel/';
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';

if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}
$PLG_langsel_MESSAGE3002 = $LANG32[9];

/**
* Check and see if we need to load the plugin configuration
*/ 
if (!isset($_LANGSEL_CONF)) {
    require_once $_CONF['path_system'] . 'classes/config.class.php';
    
    $langsel_config = config::get_instance();
    $_LANGSEL_CONF = $langsel_config->get_config('langsel');
}

// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementation                                         |
// +---------------------------------------------------------------------------+

/**
* Gets Geeklog blocks from plugins
*
* Returns data for blocks on a given side and, potentially, for
* a given topic.
*
* @param    string  $side   Side to get blocks for (right or left for now)
* @param    string  $topic  Only get blocks for this topic
* @return   array           array of block data
* @link     http://wiki.geeklog.net/index.php/Dynamic_Blocks
*
*/
function plugin_getBlocks_langsel($side, $topic='')
{
    global $_USER, $_CONF, $_LANGSEL_CONF, $LANG_LANGSEL_1, $_SCRIPTS;

    $retval = array();

    // check if we need to show the block
    if (($side=='left' && $_LANGSEL_CONF['block_pos'] == 1) || ($side=='right' && $_LANGSEL_CONF['block_pos'] == 0)) {
        $_SCRIPTS->setJavascriptLibrary('jquery');
        if ($_USER['uid'] <= 1) {
            // For anonymous users
            $_SCRIPTS->setJavascript('
                <script type="text/javascript">
                    $(document).ready(function () {
                        $("#langsel").change(function () {
                            var selection = $("#langsel option:selected").val();
                            document.cookie = "' . $_CONF['cookie_language'] . '=" + escape(selection) + ";path=" + escape("/");
                            location.href = "";
                        });
                    });
                </script>
            ');
        } else {
            // For logged-in users
            $_SCRIPTS->setJavascript('
                <script type="text/javascript">
                    $(document).ready(function () {
                        $("#langsel").change(function () {
                            $(this).parent().parent().submit();
                        });
                    });
                </script>
            ');
        }
        $display = '';
        $language = MBYTE_languageList($_CONF['default_charset']);
        $display .= '<form action="' . $_CONF['site_url'] . '/langsel/index.php" method="post" class="uk-form">' . LB;
        $display .= '<div>' . LB;
        $display .= '<select style="width: 100%" name="langsel" id="langsel">' . LB;
        foreach ($language as $langFile => $langName) {
            $display .= '<option value="' . $langFile . '"';
            if ($langFile == COM_getLanguage()) {
                $display .= ' selected="selected"';
            }
            $display .= '>' . $langName . '</option>' . LB;
        }
        $display .= '</select>' . LB;
        $display .= '<input type="hidden" name="target" value="' . htmlentities(COM_getCurrentURL(), ENT_QUOTES) . '">' . LB;
        $display .= '<noscript><input style="width: 100%" type="submit" value="' . htmlentities($LANG_LANGSEL_1['submit'], ENT_QUOTES) . '"></noscript>' . LB;
        $display .= '</div>' . LB;
        $display .= '</form>' . LB;

        $retval[] = array('name'           => 'langsel',
                          'type'           => 'dynamic',
                          'onleft'         => $_LANGSEL_CONF['block_pos'],
                          'title'          => $LANG_LANGSEL_1['title'],
                          'blockorder'     => $_LANGSEL_CONF['block_order'],
                          'content'        => $display,
                          'allow_autotags' => false,
                          'help'           => '');
    }
    return $retval;
}

/**
* Return plugin entry for "Command and Control" (moderation.php)
*
* @return  array  Array containing (plugin name, admin url, url of plugin icon)
*
*/
function plugin_cclabel_langsel()
{
    global $_CONF, $LANG_LANGSEL_1;

    $retval = array();
    if (SEC_hasRights('langsel.admin')) {
        $retval = array($LANG_LANGSEL_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/langsel/index.php',
            plugin_geticon_langsel());
    }

    return $retval;
}

/**
* Return plugin entry for the Admins Only block
*
* @return array Array containing (plugin name, plugin admin url, # of plugin items or '')
*/
function plugin_getadminoption_langsel()
{
    global $_CONF, $LANG_LANGSEL_1;

    if (SEC_hasRights('langsel.admin')) {
        return array($LANG_LANGSEL_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/langsel/index.php', 0);
    }
}

/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_langsel()
{
    global $_CONF;

    // popular alternative location:
    // return $_CONF['site_url'] . '/langsel/images/langsel.png';

    return $_CONF['site_admin_url'] . '/plugins/langsel/images/langsel.png';
}

/**
 * Returns the current plugin code version
 *
 * @return string    plugin version
 */
function plugin_chkVersion_langsel()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/langsel/autoinstall.php';

    $inst_parms = plugin_autoinstall_langsel('langsel');

    return $inst_parms['info']['pi_version'];
}

/**
* Update the plugin
*
* @return   int     Number of message to display or true for success
*
*/
function plugin_upgrade_langsel()
{
    global $_CONF, $_TABLES;

    $installed_version = DB_getItem($_TABLES['plugins'], 'pi_version',
                                    "pi_name = 'langsel'");
    $code_version = plugin_chkVersion_langsel();
    if ($installed_version == $code_version) {
        // nothing to do
        return true;
    }

    require_once $_CONF['path'] . 'plugins/langsel/autoinstall.php';

    if (! plugin_compatible_with_this_version_langsel('langsel')) {
        return 3002;
    }

    // other update code goes here

    // update plugin version number
    $inst_parms = plugin_autoinstall_langsel('langsel');
    $pi_gl_version = $inst_parms['info']['pi_gl_version'];

    DB_query("UPDATE {$_TABLES['plugins']} SET pi_version = '$code_version', pi_gl_version = '$pi_gl_version' WHERE pi_name = 'langsel'");

    return true;
}

/**
* Automatic uninstall function for plugins
*
* This code is automatically uninstalling the plugin.
* It passes an array to the core code function that removes
* tables, groups, features and php blocks from the tables.
* Additionally, this code can perform special actions that cannot be
* foreseen by the core code (interactions with other plugins for example)
*
* @return   array   Plugin information
*
*/
function plugin_autouninstall_langsel()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array(/* e.g. 'langsel' */),
        /* give the full name of the group, as in the db */
        'groups' => array('Language Selection Block Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('langsel.admin'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(),
        /* give all vars with their name */
        'vars' => array()
    );

    return $out;
}

?>
