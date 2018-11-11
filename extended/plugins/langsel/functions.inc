<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Language Selection Block Plugin                                           |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// |                                                                           |
// | This file does two things: 1) it implements the necessary Geeklog Plugin  |
// | API methods and 2) implements all the common code needed by this plugin.  |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011-2018 by the following authors:                         |
// |                                                                           |
// | Authors: Rouslan Placella - rouslan AT placella DOT com                   |
// |          Kenji ITO        - mystralkk AT gmail DOT com                    |
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

if (stripos($_SERVER['PHP_SELF'], basename(__FILE__)) !== false) {
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

$_LANGSEL_langList = include __DIR__ . '/langlist.php';

// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementation                                         |
// +---------------------------------------------------------------------------+

function plugin_getheadercode_langsel()
{
    global $_SCRIPTS;

    $_SCRIPTS->setCSSFile('w3', CTL_plugin_themeFindFile('langsel', 'css', 'w3.css'), false);
    $_SCRIPTS->setCSSFile('langsel', CTL_plugin_themeFindFile('langsel', 'css', 'style.css'), false);
}

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
    global $_USER, $_CONF, $_LANGSEL_CONF, $LANG_LANGSEL_1, $_SCRIPTS, $_LANGSEL_langList;

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
        $display .= '<form action="' . $_CONF['site_url'] . '/langsel/index.php" method="post">' . LB;
        $display .= '<div>' . LB;
        $display .= '<select style="width: 100%" name="langsel" id="langsel">' . LB;
        foreach ($language as $langFile => $langName) {
            $langName = str_replace('_utf-8', '', $langFile);
            $display .= '<option value="' . $langFile . '" direction="'
                . $_LANGSEL_langList[$langName][0] . '" unicode-bidi="embed"';
            if ($langFile == COM_getLanguage()) {
                $display .= ' selected="selected"';
            }
            $display .= '>' . $_LANGSEL_langList[$langName][1] . '</option>' . LB;
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
        $retval = array(
            $LANG_LANGSEL_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/langsel/index.php',
            plugin_geticon_langsel()
        );
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

/**
 * Build language selector to display as a list or a dropdown menu
 *
 * @param  array  $options array of language names. e.g., ['english', 'japanese', 'french']
 * @return string
 */
function LANGSEL_buildSelector(array $options = array())
{
    global $_CONF, $LANG_LANGSEL_1, $_LANGSEL_langList;

    $currentLanguage = str_replace('_utf-8', '', COM_getLanguage());
    $allLanguages = array_keys($_LANGSEL_langList);
    $target = COM_getCurrentURL();

    // If the $options array is empty, then fill the array automatically based
    // on whether the muiti-language mode is enabled
    if (count($options) === 0) {
        if (isset($_CONF['languages'], $_CONF['language_files']) &&
                is_array($_CONF['languages']) && is_array($_CONF['language_files'])) {
            foreach ($_CONF['language_files'] as $langId => $langFile) {
                $options[] = str_replace('_utf-8', '', $langFile);
            }
        } else {
            $options = $allLanguages;
        }
    }

    $T = COM_newTemplate(CTL_plugin_templatePath('langsel'));
    $T->set_file(array('menu' => 'menu.thtml'));

    $T->set_var('lang_title', $LANG_LANGSEL_1['title']);
    $T->set_block('menu', 'item');

    foreach ($options as $option) {
        $option = strtolower($option);

        if (in_array($option, $allLanguages)) {
            $text = htmlspecialchars($_LANGSEL_langList[$option][1], ENT_QUOTES, 'utf-8');
            $dir = $_LANGSEL_langList[$option][0];
            $active = ($option === $currentLanguage);

            if ($active) {
                $url = '';
            } else {
                $url = $_CONF['site_url'] . '/langsel/index.php?'
                    . http_build_query(array(
                        'langsel' => $option . '_utf-8',
                        'target'  => $target,
                      ));
            }
        }

        $T->set_var(array(
            'text'   => $text,
            'dir'    => $dir,
            'active' => $active,
            'url'    => $url,
        ));
        $T->parse('items', 'item', true);
    }

    $T->parse('output', 'menu');
    $retval = $T->finish($T->get_var('output'));

    return $retval;
}

/**
 * Implement the [langsel: language_names_separated_by space] autotag.
 *
 * @param  string $op
 * @param  string $content
 * @param  array  $autotag
 * @return string
 */
function plugin_autotags_langsel($op, $content = '', array $autotag = array())
{
    global $LANG_LANGSEL_1;

    if ($op === 'tagname') {
        return array('langsel');
    } elseif ($op === 'permission') {
        return array('langsel');
    } elseif ($op === 'description') {
        return array(
            'langsel' => $LANG_LANGSEL_1['autotag_desc_langsel'],
        );
    } elseif ($op === 'parse') {
        if ($autotag['tag'] === 'langsel') {
            $params = $autotag['parm1'];

            if (!empty($autotag['parm2'])) {
                $params .= ' ' . $autotag['parm2'];
            }

            $params = COM_applyFilter($params);
            $params = strtolower($params);
            $params = trim($params);

            if (($params === '*') || ($params === 'auto')) {
                $params = array();
            } else {
                $params = preg_split('/\s\s*/', $params);
            }

            $replace = LANGSEL_buildSelector($params);
            $content = str_replace($autotag['tagstr'], $replace, $content);
        }

        return $content;
    }
}

/**
 * Set langueage selector as a template variable {langsel}
 *
 * @param  string   $templateName
 * @param  Template $template
*/
function plugin_templatesetvars_langsel($templateName, $template)
{
    $template->set_var('langsel', LANGSEL_buildSelector());
}
