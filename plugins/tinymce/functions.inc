<?php

// +---------------------------------------------------------------------------+
// | TinyMCE Plugin for Geeklog - The Ultimate Weblog                          |
// +---------------------------------------------------------------------------+
// | geeklog/plugins/tinymce/functions.inc                                     |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 mystral-kk - geeklog AT mystral-kk DOT net             |
// |                                                                           |
// | Constructed with the Universal Plugin                                     |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== FALSE) {
	die('This file can not be used on its own.');
}

// Language file Include
$langfile = $_CONF['path'] . 'plugins/tinymce/language/' . $_CONF['language'] . '.php';
clearstatcache();

if (file_exists($langfile)) {
	include_once $langfile;
} else {
	include_once $_CONF['path'] . 'plugins/tinymce/language/english.php';
}

// Loads the plugin configuration
global $_TMCE_CONF;

require_once $_CONF['path'] . 'plugins/tinymce/config.php';
require_once $_CONF['path'] . 'plugins/tinymce/addons.php';

TMCE_loadConfig();

// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementations                                        |
// +---------------------------------------------------------------------------+

/**
* Returns a URL to icon
*/
function plugin_geticon_tinymce() {
	global $_CONF;
	
	return $_CONF['site_url'] . '/tinymce/images/tinymce.png';
}

/**
* Returns code to be added to <head> section
*/
function plugin_getheadercode_tinymce() {
	global $_CONF;
	
	$retval = LB
			. '<!-- Start of TinyMCE plugin -->' . LB;
	$baseUrl = $_CONF['site_url'] . '/tinymce/js';
	
	$retval .= '<script type="text/javascript" src="' . $baseUrl
			.  '/tiny_mce/tiny_mce.js"></script>' . LB
			.  '<script type="application/javascript" src="' . $baseUrl
			.  '/tiny_mce/plugins/tinybrowser/tb_tinymce.js.php"></script>' . LB
			.  '<script type="text/javascript" src="' . $baseUrl
			.  '/tinymce_loader.js.php"></script>' . LB
			.  '<!-- End of TinyMCE plugin -->' . LB;
	
	return $retval;
}

/**
* Check to see if this Plugin uses comments
*
* @return   boolean TRUE or FALSE
*/
function plugin_commentsupport_tinymce() {
	return FALSE;
}

/**
* Puts an option for the plugin in the command and control block on moderation.php
*
* @return   array   Array containing (plugin name, admin url, url of plugin icon)
*/
function plugin_cclabel_tinymce() {
	global $_CONF;
	
	if (SEC_hasRights('tinymce.edit')) {
		$pi_name   = TMCE_str('admin');
		$admin_url = $_CONF['site_admin_url'] . '/plugins/tinymce/index.php';
		$icon_url  = plugin_geticon_tinymce();
		
		return array($pi_name, $admin_url, $icon_url);
	}
}

/**
* Returns the administrative option for this plugin
*
* @return   array   Array containing (plugin name, plugin admin url, # of items
* in plugin or '')
*/
function plugin_getadminoption_tinymce() {
	global $_CONF, $_TABLES;
	
	if (SEC_hasRights('tinymce.edit')) {
		$pi_name   = TMCE_str('admin');
		$admin_url = $_CONF['site_admin_url'] . '/plugins/tinymce/index.php';
		$num_items = '';
		
		return array($pi_name, $admin_url, $num_items);
	}
}

/**
* Returns the version for this plugin
*
* @return string VersionNo
*/
function plugin_chkVersion_tinymce() {
	global $_TMCE_CONF;
	
	return $_TMCE_CONF['pi_version'];
}

/**
* Upgrade the plugin
*
* @return   boolean TRUE (= success)
*/
function plugin_upgrade_tinymce() {
	global $_CONF, $_TABLES, $_TMCE_CONF;
	
	$sql = "SELECT pi_version "
		 . "FROM {$_TABLES['plugins']} "
		 . "WHERE (pi_name = 'tinymce') ";
	$result = DB_query($sql);
	$A = DB_fetchArray($result);
	$pi_version = $A['pi_version'];		// Version info stored in database
	
	// Do upgrading
	
// 	switch ($pi_version) {
// 		case '':
// 		break;
// 		
// 		case '':
// 		break;
// 		
// 	}
	
	// Update plugin info
	$sql = "UPDATE {$_TABLES['plugins']} "
		 . "SET pi_version = '" . addslashes($_TMCE_CONF['pi_version']) . "' "
		 . " WHERE (pi_name = 'tinymce') ";
	$result = DB_query($sql);
	COM_errorLog("TinyMCE plugin was successfully updated to version {$_TMCE_CONF['pi_version']}.");
	
	return TRUE;
}

/**
* Automatic uninstall function for plugins
*
* @return   array
*/
function plugin_autouninstall_tinymce() {
	global $_CONF, $_TABLES, $_TMCE_CONF;
	
	// Restores the previsous value of $_CONF['advanced_editor'] from db
	$prev_value = DB_getItem(
		$_TABLES['vars'],
		'value',
		"name = '" . addslashes($_TMCE_CONF['db_var_name']) . "' "
	);
	
	if ($prev_value != '') {
		$prev_value = unserialize($prev_value);
	    $c = config::get_instance();
		$c->set('advanced_editor', $prev_value, 'Core');
	}
	
	$out = array (
		/* give the name of the tables, without $_TABLES[] */
		'tables' => array('tinymce_configs'),
		/* give the full name of the group, as in the db */
		'groups' => array('TinyMCE Admin'),
		/* give the full name of the feature, as in the db */
		'features' => $_TMCE_CONF['FEATURES'],
		/* give the full name of the block, including 'phpblock_', etc */
		'php_blocks' => array(),
		/* give all vars with their name */
		'vars'=> array($_TMCE_CONF['db_var_name'])
	);
	
	return $out;
}

/**
* Returns the URL to the plugin's documentation
*
* @param   string
* @return  string
*/
function plugin_getdocumentationurl_tinymce($arg) {
	global $_CONF;
	
	$lang = str_replace('_utf-8', '', COM_getLanguage());
	$path = $_CONF['path_html'] . 'admin/plugins/tinymce/docs/' . $lang . '/'
		  . $arg . '.html';
	clearstatcache();
	
	if (!is_readable($path)) {
		$lang = 'english';
	}
	
	$retval = $_CONF['site_admin_url'] . '/plugins/tinymce/docs/' . $lang
			. '/' . $arg . '.html';
	
	return $retval;
}

/**
* Provides text for a Configuration tooltip
*
* @param    string  $id     Id of config value
* @return   mixed           Text to use regular tooltip, NULL to use config 
*                           tooltip hack, or empty string when not available
*/
function plugin_getconfigtooltip_tinymce($id) {
	// Use config tooltip hack where tooltip is read from the config documentation
	return NULL;
}

/**
* Converts a list of file extensions into a string separated by a comma
*
* @param   array   $list
* @return  string
*/
function TMCE_buildExtensionList($list) {
	if (is_array($list) AND (count($list) > 0)) {
		$temp = array();
		
		foreach ($list as $item) {
			$temp[] = '*.' . preg_replace("/[^0-9a-z]/", '', strtolower($item));
		}
		
		return implode(',', $temp);
	} else {
		return '';
	}
}

/**
* Loads config infor with config.class.php
*/
function TMCE_loadConfig() {
	global $_CONF, $_TMCE_CONF;
	
	require_once $_CONF['path_system'] . 'classes/config.class.php';
	$config = config::get_instance();
	
	if ($config->group_exists('tinymce')) {
		$temp = $config->get_config('tinymce');
		
		if (is_array($temp) AND (count($temp) > 0)) {
			$_TMCE_CONF = array_merge($_TMCE_CONF, $temp);
		}
		
		// Checks and modifies config values
		$_TMCE_CONF['tb_unixpermissions'] = intval('0' . $_TMCE_CONF['tb_unixpermissions'], 8);
		
		$_TMCE_CONF['tb_filetype_image'] = TMCE_buildExtensionList($_TMCE_CONF['tb_filetype_image']);
		
		$_TMCE_CONF['tb_filetype_media'] = TMCE_buildExtensionList($_TMCE_CONF['tb_filetype_media']);
		
		if (($_TMCE_CONF['tb_imagequality'] < 1) OR ($_TMCE_CONF['tb_imagequality'] > 99)) {
			$_TMCE_CONF['tb_imagequality'] = 80;
		}
		
		if (($_TMCE_CONF['tb_thumbquality'] < 1) OR ($_TMCE_CONF['tb_thumbquality'] > 99)) {
			$_TMCE_CONF['tb_thumbquality'] = 80;
		}
	}
}

/**
* Returns a string escaped for HTML output
*/
function TMCE_esc($str) {
	static $encoding = NULL;
	
	if ($encoding === NULL) {
		$encoding = COM_getCharset();
	}
	
	$str = str_replace(
		array('&lt;', '&gt;', '&amp;', '&quot;', '&#039;'),
		array(   '<',    '>',     '&',      '"',      "'"),
		$str
	);
	
	return htmlspecialchars($str, ENT_QUOTES, $encoding);
}

/**
* Returns a lang var
*/
function TMCE_str($index, $noesc = FALSE) {
	global $LANG_TMCE;
	
	if (isset($LANG_TMCE[$index])) {
		if ($noesc) {
			return $LANG_TMCE[$index];
		} else {
			return TMCE_esc($LANG_TMCE[$index]);
		}
	} else {
		COM_errorLog('TMCE_str: undefined index: "' . $index . '"');
		return '(undefined)';
	}
}

/**
* Returns the UI language of the current user
*
* @return  array of (language id, directionality)
*/
function TMCE_getLocale() {
	global $LANG_DIRECTION;
	
	// language supported in GL => language supported in TinyMCE
	$lang_mappings = array(
		'afrikaans'           => '',
		'bosnian'             => 'bs',
		'bulgarian'           => 'bg',
		'catalan'             => 'ca',
		'chinese_simplified'  => 'zh',
		'chinese_traditional' => 'zh',
		'croatian'            => 'hr',
		'czech'               => 'cs',
		'danish'              => 'da',
		'dutch'               => 'nl',
		'english'             => 'en',
		'estonian'            => 'et',
		'farsi'               => 'fa',
		'finnish'             => 'fi',
		'french_canada'       => 'fr',
		'french_france'       => 'fr',
		'german'              => 'de',
		'german_formal'       => 'de',
		'hebrew'              => 'he',
		'hellenic'            => 'el',
		'indonesian'          => 'id',
		'italian'             => 'it',
		'japanese'            => 'ja',
		'korean'              => 'ko',
		'norwegian'           => 'no',
		'polish'              => 'pl',
		'portuguese'          => 'pt',
		'portuguese_brazil'   => 'pt',
		'romanian'            => 'ro',
		'russian'             => 'ru',
		'slovak'              => 'sk',
		'slovenian'           => 'sl',
		'spanish'             => 'es',
		'spanish_argentina'   => 'es',
		'swedish'             => 'sv',
		'turkish'             => 'tr',
		'ukrainian'           => 'uk',
	);
	
	$lang = COM_getLanguage();
	$lang = str_replace('_utf-8', '', $lang);
	
	if (isset($lang_mappings[$lang])) {
		$language = $lang_mappings[$lang];
	}
	
	if ($language == '') {
		$language = 'en';
	}
	
	$directionality = isset($LANG_DIRECTION) ? $LANG_DIRECTION : 'ltr';
	
	return array($language, $directionality);
}

/**
* Returns the configuration of the current user
*
* @return  array
*/
function TMCE_getConfig() {
	global $_TABLES, $_GROUPS, $_TMCE_CONF;
	
	// Sets default data just in case
	$A = $_TMCE_CONF['default_data'];
	
	if (is_array($_GROUPS) AND (count($_GROUPS) > 0)) {
		// Retrieves config info from db
		$sql = "SELECT * "
			 . "FROM {$_TABLES['tinymce_configs']} "
			 . "WHERE group_id IN (" . implode(',', array_values($_GROUPS)) . ") "
			 . "ORDER BY cid DESC ";
		$result = DB_query($sql);
		
		if (!DB_error() AND (DB_numRows($result) > 0)) {
			// Retrieves only one record with the greatest cid
			$A = DB_fetchArray($result, FALSE);
		}
	}
	
	return $A;
}

/**
* Returns a list of all TinyMCE plugins
*
* @return  array
*/
function TMCE_getAllPlugins() {
	global $_CONF, $_TMCE_PLUGINS, $_TMCE_EXCLUDE_PLUGINS;
	
	static $retval = NULL;
	
	if ($retval === NULL) {
		$list = array_keys($_TMCE_PLUGINS);
		$list = array_diff($list, $_TMCE_EXCLUDE_PLUGINS);
		$dirs = TMCE_getDirList($_CONF['path_html'] . '/tinymce/js/tiny_mce/plugins');
		
		$retval = array_intersect($list, $dirs);
	}
	
	return $retval;
}

/**
* Returns a list of all TinyMCE buttons
*
* @return  array
*/
function TMCE_getAllButtons() {
	global $_CONF, $_TMCE_BUILTINS, $_TMCE_PLUGINS;
	
	static $retval = NULL;
	
	if ($retval === NULL) {
		$retval = array();
		$list = array_merge_recursive($_TMCE_BUILTINS, $_TMCE_PLUGINS);
		
		foreach ($list as $item) {
			if (is_array($item['buttons']) AND (count($item['buttons']) > 0)) {
				$retval = array_merge($retval, $item['buttons']);
			}
		}
		
		$retval = array_unique($retval);
	}
	
	return $retval;
}

/**
* Returns a list of all HTML tags the current user can use
*
* @param   boolean  $tinymceLike
* @return  array
*/
function TMCE_getAllowedHTMLTags($tinymceLike = FALSE) {
	global $_CONF;
	
	$retval = $_CONF['user_html'];
	
	if (SEC_hasRights('story.edit')) {
		$retval = array_merge_recursive($retval, $_CONF['admin_html']);
	}
	
	if ($tinymceLike) {
		$tags = $retval;
		$retval = array();
		
		foreach ($tags as $tag => $attrs) {
			if (is_array($attrs) AND (count($attrs) > 0)) {
				$tag .= '[' . implode('|', array_keys($attrs)) . ']';
			}
			
			$retval[] = $tag;
		}
	}
	
	return $retval;
}

/**
* Checks if HTML tags and attributes are allowed for the current user
*
* @param   array    $tags         HTML tags and attributes to check
* @param   &array   $allowedTags  all tags allowed for the current user
* @return  boolean
*/
function TMCE_isTagsAllowed($tags, &$allowedTags) {
	if (!is_array($tags) OR (count($tags) === 0)) {
		return TRUE;
	}
	
	foreach ($tags as $name => $attrs) {
		if (!array_key_exists($name, $allowedTags)) {
			return FALSE;
		}
		
		if (!is_array($attrs) OR (count($attrs) === 0)) {
			return TRUE;
		}
		
		foreach ($attrs as $attr) {
			if (!isset($allowedTags[$name][$attr])) {
				return FALSE;
			}
		}
	}
	
	return TRUE;
}

/**
* Returns a list of available TinyMCE plugins, taking into the current user's
* permissions, $_CONF['user_html'], $_CONF['admin_html'], and
* $_CONF['skip_html_filter_for_root']
*
* @return  array
*/
function TMCE_getPlugins() {
	global $_CONF, $_TMCE_PLUGINS, $_TMCE_CORE_PLUGINS, $_TMCE_EXCLUDE_PLUGINS;
	
	$retval = array();
	
	if (isset($_CONF['skip_html_filter_for_root'])
	 AND ($_CONF['skip_html_filter_for_root'] == 1) AND SEC_inGroup('Root')) {
		$retval = TMCE_getAllPlugins();
	} else {
		$allowedTags = TMCE_getAllowedHTMLTags(FALSE);
		
		foreach ($_TMCE_PLUGINS as $plugin => $info) {
			if (in_array($plugin, $_TMCE_CORE_PLUGINS)
			 OR (!in_array($plugin, $_TMCE_EXCLUDE_PLUGINS) AND TMCE_isTagsAllowed($info['html'], $allowedTags))) {
				$retval[] = $plugin;
			}
		}
	}
	
	return $retval;
}

/**
* Returns a list of available TinyMCE buttons, taking into the current user's
* permissions, $_CONF['user_html'], $_CONF['admin_html'], and
* $_CONF['skip_html_filter_for_root']
*
* @return  array
*/
function TMCE_getButtons() {
	global $_CONF, $_TMCE_BUILTINS, $_TMCE_PLUGINS;
	
	$retval = array();
	
	if (isset($_CONF['skip_html_filter_for_root'])
	 AND ($_CONF['skip_html_filter_for_root'] == 1) AND SEC_inGroup('Root')) {
		$retval = TMCE_getAllButtons();
	} else {
		$allowedTags = TMCE_getAllowedHTMLTags(FALSE);
		
		// Checks for buttons provided by basic commands
		foreach ($_TMCE_BUILTINS as $command => $info) {
			if (is_array($info['buttons']) AND (count($info['buttons']) > 0)) {
				if (TMCE_isTagsAllowed($info['html'], $allowedTags)) {
					$retval = array_merge($retval, $info['buttons']);
				}
			}
		}
		
		// Checks for buttons provided by plugins
		foreach (TMCE_getPlugins() as $plugin) {
			$buttons = $_TMCE_PLUGINS[$plugin]['buttons'];
			
			if (is_array($buttons) AND (count($buttons) > 0)) {
				$retval = array_merge($retval, $buttons);
			}
		}
		
		$retval = array_unique($retval);
	}
	
	return $retval;
}

/**
* Returns information about a template file
*
* @param   string  $filename  full path to a template file (*.html)
* @return  srray
*/
function TMCE_extractInfoFromTemplate($filename) {
	$title       = '';
	$description = '';
	$content     = '';
	$updated     = 0;
	
	if ($filename != '') {
		$doc = @file_get_contents($filename);
		
		if (($doc !== FALSE)
		 AND preg_match("|^\s*<!--(.*)-->\s*<div>(.*)</div>\s*$|mis", $doc, $match)) {
			if (preg_match("/__TITLE_START__\{(.*)\}__TITLE_END__/m", $match[1], $M)) {
				$title = strip_tags($M[1]);
			}
			
			if (preg_match("/__DESC_START__\{(.*)\}__DESC_END__/m", $match[1], $M)) {
				$description = strip_tags($M[1]);
			}
			
			if (preg_match("/__UPDATED_START__\{(.*)\}__UPDATED_END__/m", $match[1], $M)) {
				$updated = strtotime(strip_tags($M[1]));
			}
			
			$content = trim($match[2]);
		}
	}
	
	if ($updated == 0) {
		$updated = time();
	}
	
	$retval = array(
		'title'       => $title,
		'description' => $description,
		'content'     => $content,
		'updated'     => $updated,
	);
	
	return $retval;
}

function TMCE_getTemplateList() {
	global $_CONF;
	
	$retval = array();
	$templatePath = realpath($_CONF['path_html'] . 'tinymce/templates');
	
	if (($dh = opendir($templatePath)) !== FALSE) {
		while (($item = readdir($dh)) !== FALSE) {
			$filename = $templatePath . DIRECTORY_SEPARATOR . $item;
			
			if (preg_match("/\.html?$/", $item) AND is_readable($filename)) {
				$info = TMCE_extractInfoFromTemplate($filename);
				$info['src'] = $_CONF['site_url'] . '/tinymce/templates/' . $item;
				$retval[] = $info;
			}
		}
		
		closedir($dh);
	}
	
	return $retval;
}
