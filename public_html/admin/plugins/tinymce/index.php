<?php

// +---------------------------------------------------------------------------+
// | TinyMCE Plugin for Geeklog - The Ultimate Weblog                          |
// +---------------------------------------------------------------------------+
// | public_html/admin/plugins/tinymce/index.php                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010-2011 mystral-kk - geeklog AT mystral-kk DOT net        |
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

require_once '../../../lib-common.php';
require_once $_CONF['path_system'] . 'lib-admin.php';

// Only lets admin users access this page
if (!SEC_hasRights('tinymce.edit')) {
    // Someone is trying to illegally access this page
    COM_errorLog("Someone has tried to illegally access the tinymce Admin page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: {$_SERVER['REMOTE_ADDR']}", 1);
    $display = COM_siteHeader()
			 . COM_startBlock(TMCE_str('access_denied'))
			 . TMCE_str('access_denied_msg')
			 . COM_endBlock()
			 . COM_siteFooter();
    COM_output($display);
    exit;
}

//===================================================================
// Functions
//===================================================================

if (!is_callable('file_put_contents')) {
	function file_put_contents($filename, $data) {
		$retval = FALSE;
		
		if (($fh = @fopen($filename, 'r+b')) !== FALSE) {
			if (flock($fh, EX_LOCK_EX)) {
				if (ftruncate($fh, 0)) {
					if (rewind($fh)) {
						$retval = fwrite($fh, $data);
					}
				}
			}
			
			fclose($fh);
		}
		
		return $retval;
	}
}

/**
* Cleans a string so that it won't include any HTML tags
*
* @param   string  $str
* @return  string
*/
function TMCE_cleanString($str) {
	$str = str_replace('<!--', '', $str);
	$str = str_replace('-->', '', $str);
	$str = strip_tags($str);
	
	return $str;
}

/**
* Returns a unique file name
*/
function TMCE_getNewFilename() {
	global $_CONF;
	
	$basePath = realpath($_CONF['path_html'] . 'tinymce/templates/');
	
	do {
		$rnd = substr('00000' . (string) rand(), -5, 5);
		$filename = 't' . date('Ymd') . $rnd . '.html';
		clearstatcache();
	} while (file_exists($basePath . $filename));
	
	return $filename;
}

/**
* Returns admin header
*
* @param   string  $what  'config' or 'template'
* @param   string  $mode  'list' or 'edit'
* @return  string
*/
function TMCE_getAdminHeader($what, $mode) {
	global $_CONF;
	
	// Builds menu items
	$menu_arr = array();
	$menu_arr[] = array(
		'text' => TMCE_str('admin_menu_cc'),
		'url'  => $_CONF['site_admin_url']
	);
	
	foreach (array('config', 'template') as $tempWhat) {
		foreach (array('list', 'edit') as $tempMode) {
			if (($tempWhat !== $what) OR ($tempMode !== $mode)) {
				$menu_arr[] = array(
					'text' => TMCE_str('admin_menu_' . $tempWhat . '_' . $tempMode),
					'url'  => $_CONF['site_admin_url'] . '/plugins/tinymce/index.php?what=' . $tempWhat . '&amp;mode=' . $tempMode,
	);			}
		}
	}
	
	$text = TMCE_str('admin_desc_' . $what, TRUE);
	$icon = plugin_geticon_tinymce();
	
	return ADMIN_createMenu($menu_arr, $text, $icon);
}

function TMCE_adminConfigFieldFunction($fieldname, $fieldvalue, $row_data, $icon_arr) {
	global $_CONF, $_TABLES;
	
	if ($fieldname === 'cid') {
		$retval = '<a href="' . $_CONF['site_admin_url']
				. '/plugins/tinymce/index.php?what=config&amp;mode=edit&amp;cid='
				. TMCE_esc($fieldvalue) . '" title="' . TMCE_str('admin_edit')
				. '">' . $icon_arr['edit'] . '</a>';
	} else {
		$retval = TMCE_esc($fieldvalue);
	}
	
	return $retval;
}

function TMCE_getAdminConfigList() {
	global $_CONF, $_TABLES;
	
	$fieldfunction = 'TMCE_adminConfigFieldFunction';
	$header_arr = array(
		array(
			'text'  => TMCE_str('admin_edit'),
			'field' => 'cid',
			'sort'  => FALSE,
		),
		array(
			'text'  => TMCE_str('admin_title'),
			'field' => 'title',
			'sort'  => FALSE,
		),
		array(
			'text'  => TMCE_str('admin_theme'),
			'field' => 'theme',
			'sort'  => FALSE,
		),
		array(
			'text'  => TMCE_str('admin_grp_name'),
			'field' => 'grp_name',
			'sort'  => FALSE,
		),
	);
	
	$text_arr = array(
		'form_url' => $_CONF['site_admin_url'] . '/plugins/tinymce/index.php',
	);
	
	$data_arr = array();
	$sql = "SELECT cid, title, theme, grp_name "
		 . "FROM {$_TABLES['tinymce_configs']} "
		 . "LEFT JOIN {$_TABLES['groups']} "
		 . "ON group_id = grp_id ";
	$result = DB_query($sql);
	
	if (!DB_error()) {
		while (($A = DB_fetchArray($result, FALSE)) !== FALSE) {
			$data_arr[] = $A;
		}
	}
	
	$options  = array('chkdelete' => FALSE);
	$form_arr = array();
	
	return ADMIN_simpleList($fieldfunction, $header_arr, $text_arr, $data_arr, $options, $form_arr);
}

function TMCE_getAdminTemplateList() {
	global $_CONF;
	
	$retval = array();
	$templates = TMCE_getTemplateList();
	
	if (count($templates) === 0) {
		$retval = '<p>' . TMCE_str('admin_no_template') . '</p>';
	} else {
		$sw = 1;
		
		foreach ($templates as $template) {
			$className = 'pluginRow' . (string) $sw;
			$retval[] = '<tr class="' . $className
					  . '" onmouseover="className=\'pluginRollOver\';" onmouseout="className=\''
					  . $className . '\';"><td class="admin-list-field"><a href="'
					  . $_CONF['site_admin_url']
					  . '/plugins/tinymce/index.php?what=template&amp;mode=edit&amp;template='
					  . rawurlencode(basename($template['src'])) . '" title="'
					  . TMCE_str('admin_edit') . '"><img src="'
					  . $_CONF['site_url'] . '/tinymce/images/edit.png" title="'
					  . TMCE_str('admin_edit') . '"' . XHTML . '></a></td class="admin-list-field"><td>'
					  . TMCE_esc($template['title']) . '</td><td class="admin-list-field">'
					  . TMCE_esc($template['description']) . '</td><td class="admin-list-field">'
					  . date('Y-m-d H:i:s', $template['updated'])
					  . '</td></tr>';
			
			$sw ++;
			
			if ($sw >= 2) {
				$sw = 1;
			}
		}
		
		$retval = '<table class="admin-list-table">' . LB
				. '<tr style="background: #ffffff;"><th class="admin-list-headerfield">'
				. TMCE_str('admin_edit') . '</th><th class="admin-list-headerfield">'
				. TMCE_str('admin_title') . '</th><th class="admin-list-headerfield">'
				. TMCE_str('admin_description') . '</th><th class="admin-list-headerfield">'
				. TMCE_str('admin_updated') . '</th></tr>' . LB
				. implode(LB, $retval)
				. '</table>' . LB;
	}
	
	return $retval;
}

function TMCE_getAdminTemplateEditor($template) {
	global $_CONF, $_TMCE_CONF;
	
	if ($template != '') {
		$template = basename($template);
		$filename = realpath($_CONF['path_html'] . 'tinymce/templates/' . $template);
	} else {
		$filename = '';
	}
	
	$info = TMCE_extractInfoFromTemplate($filename);
	
	$T = new Template($_CONF['path'] . 'plugins/tinymce/templates');
	$T->set_file('editor', 'template_editor.thtml');
	$T->set_var('xhtml', XHTML);
	$T->set_var('action', $_CONF['site_admin_url'] . '/plugins/tinymce/index.php');
	$T->set_var('lang_title', TMCE_str('admin_title'));
	$T->set_var('lang_description', TMCE_str('admin_description'));
	$T->set_var('lang_content', TMCE_str('admin_content'));
	$T->set_var('lang_updated', TMCE_str('admin_updated'));
	$T->set_var('lang_submit', TMCE_str('admin_submit'));
	$T->set_var('lang_delete', TMCE_str('admin_delete'));
	$T->set_var('lang_admin_confirm_delete', TMCE_str('admin_confirm_delete'));
	$T->set_var('title', TMCE_esc($info['title']));
	$T->set_var('description', TMCE_esc($info['description']));
	$T->set_var('content', TMCE_esc($info['content']));
	$T->set_var('template', rawurlencode($template));
	$T->set_var('token_name', TMCE_esc(CSRF_TOKEN));
	$T->set_var('token_value', SEC_createToken($_TMCE_CONF['token_expirary']));
	$T->parse('output', 'editor');
	
	return $T->finish($T->get_var('output'));
}

/**
* Returns <options> tags for Yes/No selectors
*
* @param   int     $selectedValue  1 = TRUE, 0 = FALSE
* @return  string
*/
function TMCE_getBooleanOptions($selectedValue) {
	global $LANG_configselects;
	
	$retval = '';
	$selectedValue = (int) $selectedValue;
	
	foreach ($LANG_configselects['tinymce'][0] as $text => $value) {
		$selected = ($selectedValue == $value) ? ' selected="selected"' : '';
		$retval .= '<option value="' . TMCE_esc($value) . '"' . $selected
				.  '>' . TMCE_esc($text) . '</option>' . LB;
	}
	
	return $retval;
}

/**
* Returns <options> tags for group id selectors
*
* @param   int     $selectedValue  group id
* @return  string
*/
function TMCE_getGroupOptions($selectedId) {
	global $_TABLES;
	
	$retval = array();
	$sql = "SELECT grp_id, grp_name "
		 . "FROM {$_TABLES['groups']} "
		 . "ORDER BY grp_id ";
	$result = DB_query($sql);
	
	if (!DB_error()) {
		while (($A = DB_fetchArray($result, FALSE)) !== FALSE) {
			if ($A['grp_id'] == $selectedId) {
				$selected = ' selected="seleced"';
			} else {
				$selected = '';
			}
			
			$retval[] = '<option value="' . TMCE_esc($A['grp_id']) . '"'
					  . $selected . '>' . TMCE_esc($A['grp_name']) . '</option>';
		}
	}
	
	return implode(LB, $retval);
}

/**
* Returns <options> tags for theme selectors
*
* @param   int     $selectedValue  group id
* @return  string
*/
function TMCE_getThemeOptions($selectedId) {
	global $_CONF;
	
	$retval = array();
	$themes = TMCE_getDirList($_CONF['path_html'] . 'tinymce/js/tiny_mce/themes');
	
	foreach ($themes as $theme) {
		$selected = ($theme === $selectedId) ? ' selected="seleced"' : '';
		$retval[] = '<option value="' . TMCE_esc($theme) . '"'
				  . $selected . '>' . TMCE_esc($theme) . '</option>';
	}
	
	return implode(LB, $retval);
}

/**
* Returns <options> tags for ENTER function selectors
*
* @param   int     $selectedId  enter_function
* @return  string
*/
function TMCE_getEnterOptions($selectedId) {
	global $LANG_TMCE;
	
	$options = array(
		'admin_ef_paragraph'	=> 0,
		'admin_ef_newline'		=> 1,
	);

	$retval = '';
	$selectedId = (int) $selectedId;
	
	foreach ($options as $text => $value) {
		$selected = ($value === $selectedId) ? ' selected="selected"' : '';
		$retval .= '<option value="' . TMCE_esc($value) . '"' . $selected
				.  '>' . TMCE_str($text) . '</option>' . LB;
	}
	
	return $retval;
}

/**
* Returns toolbar palette
*
* @return  string
*/
function TMCE_getPalette() {
	global $LANG_TMCE;
	
	$enabled  = '<p style="line-height: 2em;">';
	$disabled = '<p style="line-height: 2em;">';
	$allButtons     = TMCE_getAllButtons();
	$allowedButtons = TMCE_getButtons();
	sort($allButtons);
	
	foreach ($allButtons as $button) {
		if (isset($LANG_TMCE[$button])) {
			$label = $LANG_TMCE[$button];
		} else {
			$label = $button;
		}
		
		if (in_array($button, $allowedButtons)) {
			$enabled .= '<span class="tmce_button" title="' . TMCE_esc($label)
					 .  '" style="color: white; background-color: #0000cc; padding: 3px;">'
					 .  TMCE_esc($button) . '</span>&nbsp;&nbsp; ';
		} else {
			$disabled .= '<span class="tmce_button" title="' . TMCE_esc($label)
					  .  '" style="color: white; background-color: #666666; padding: 3px;">'
					  .  TMCE_esc($button) . '</span>&nbsp;&nbsp; ';
		}
	}
	
	$enabled  .= '</p>';
	$disabled .= '</p>';
	
	return array($enabled, $disabled);
}

/**
* Returns checkboxes for plugins
*
* @param   string   $plugins  plugins separated by a comma
* @return  string
*/
function TMCE_unpackPlugins($plugins) {
	global $_CONF, $_TMCE_CONF, $LANG_TMCE, $_TMCE_CORE_PLUGINS;
	
	$retval = '';
	
	$plugins        = explode(',', $plugins);
	$allPlugins     = TMCE_getAllPlugins();
	sort($allPlugins);
	$allowedPlugins = TMCE_getPlugins();
	$counter = 0;
	
	foreach ($allPlugins as $plugin) {
		if (($plugin !== 'example') AND !in_array($plugin, $_TMCE_CORE_PLUGINS)) {
			if ($counter === 0) {
				$retval .= '<tr>';
			}
			
			$id       = TMCE_esc('tmce_plugin_' . $plugin);
			$title    = isset($LANG_TMCE[$plugin]) ? TMCE_str($plugin) : TMCE_esc($plugin);
			$checked  = in_array($plugin, $plugins) ? ' checked="checked"' : '';
			$disabled = in_array($plugin, $allowedPlugins) ? '' : ' disabled="disabled"';
			
			$retval .= '<td>'
					.  '<input id="' . $id. '" name="plugins[]" type="checkbox" value="'
					.  TMCE_esc($plugin) .'"' . $checked . $disabled . XHTML . '>'
					.  '<label for="' . $id . '" title="' . $title
					.  '" style="font-weight: normal;">' .  TMCE_esc($plugin)
					.  '</label>'
					.  '</td>' . LB;
			$counter ++;
			
			if ($counter >= $_TMCE_CONF['plugin_num_columns']) {
				$retval .= '</tr>' . LB;
				$counter = 0;
			}
		}
	}
	
	if ($counter > 0) {
		$retval .= str_repeat('<td>&nbsp;</td>', $_TMCE_CONF['plugin_num_columns'] - $counter)
				.  '</tr>' . LB;
	}
	
	return $retval;
}

/**
* Returns config ediot
*
* @param   int     $cid  cid
* @return  string
*/
function TMCE_getAdminConfigEditor($cid) {
	global $_CONF, $_TABLES, $_TMCE_CONF;
	
	$cid = (int) $cid;
	$sql = "SELECT * "
		 . "FROM {$_TABLES['tinymce_configs']} "
		 . "WHERE (cid = '" . addslashes($cid) . "') ";
	$result = DB_query($sql);
	
	if (!DB_error() AND (DB_numRows($result) == 1)) {
		$A = DB_fetchArray($result, FALSE);
	} else {
		$A = $_TMCE_CONF['default_data'];
		$A['title'] = 'new';
	}
	
	$A['buttons'] = unserialize($A['buttons']);
	
	$T = new Template($_CONF['path'] . 'plugins/tinymce/templates');
	$T->set_file('editor', 'editor.thtml');
	$T->set_var('xhtml', XHTML);
	
	// Sets lang vars
	$langs = array(
		'admin_title', 'admin_theme', 'admin_toolbars', 'admin_plugins',
		'admin_grp_name', 'admin_submit', 'admin_delete',
		'admin_confirm_delete', 'admin_avaiable_buttons', 'admin_disabled_buttons',
		'admin_tb_perms', 'admin_tb_allow_upload', 'admin_tb_allow_edit',
		'admin_tb_allow_delete', 'admin_tb_allow_folders', 'admin_enter_function',
	);
	
	foreach ($langs as $lang) {
		$T->set_var('lang_' . $lang, TMCE_str($lang));
	}
	
	$T->set_var('action', $_CONF['site_admin_url'] . '/plugins/tinymce/index.php');
	$T->set_var('cid', TMCE_esc($A['cid']));
	$T->set_var('title', TMCE_esc($A['title']));
	$T->set_var('theme_options', TMCE_getThemeOptions($A['theme']));
	
	// Sets the path to "install.html"
	$lang = str_replace('_utf-8', '', COM_getLanguage());
	$docPath = $_CONF['path_html'] . 'admin/plugins/tinymce/docs/' . $lang
			 . '/install.html';
	clearstatcache();
	
	if (!is_readable($docPath)) {
		$lang = 'english';
	}
	
	$docUrl = $_CONF['site_admin_url'] . '/plugins/tinymce/docs/' . $lang
			. '/install.html';
	$config_help = sprintf(TMCE_str('admin_config_help', TRUE), $docUrl);
	$T->set_var('config_help', $config_help);
	
	$palettes = TMCE_getPalette();
	$T->set_var('palette1', $palettes[0]);
	$T->set_var('palette2', $palettes[1]);
	$T->set_var('buttons1', $A['buttons']['buttons1']);
	$T->set_var('buttons2', $A['buttons']['buttons2']);
	$T->set_var('buttons3', $A['buttons']['buttons3']);
	$T->set_var('buttons4', $A['buttons']['buttons4']);
	$T->set_var('plugins', TMCE_unpackPlugins($A['plugins']));
	$T->set_var('tb_allow_upload_options', TMCE_getBooleanOptions($A['tb_allow_upload']));
	$T->set_var('tb_allow_edit_options', TMCE_getBooleanOptions($A['tb_allow_edit']));
	$T->set_var('tb_allow_delete_options', TMCE_getBooleanOptions($A['tb_allow_delete']));
	$T->set_var('tb_allow_folders_options', TMCE_getBooleanOptions($A['tb_allow_folders']));
	$T->set_var('enter_options', TMCE_getEnterOptions($A['enter_function']));
	$T->set_var('group_options', TMCE_getGroupOptions($A['group_id']));
	$T->set_var('token_name', CSRF_TOKEN);
	$T->set_var('token_value', SEC_createToken($_TMCE_CONF['token_expirary']));
	$T->set_var('visibility', ($cid === 0 ? 'none' : ''));
	$T->parse('output', 'editor');
	
	return $T->finish($T->get_var('output'));
}

//===================================================================
// Main
//===================================================================

if (!defined('XHTML')) {
	define('XHTML', '');
}

$mode = '';
$what = '';

// Retrieve request vars
if (isset($_GET['mode'])) {
	$mode = COM_applyFilter($_GET['mode']);
} else if (isset($_POST['mode'])) {
	$mode = COM_applyFilter($_POST['mode']);
}

if ($mode !== 'edit') {
	$mode = 'list';
}

if (isset($_GET['what'])) {
	$what = COM_applyFilter($_GET['what']);
} else if (isset($_POST['what'])) {
	$what = COM_applyFilter($_POST['what']);
}

if (($what !== 'config') AND ($what !== 'template')) {
	$what = 'config';
	$mode = 'list';
}

if (($mode === 'edit') AND isset($_POST['submit'])) {
	$submit = COM_stripslashes($_POST['submit']);
} else {
	$submit = '';
}

if (isset($_GET['cid'])) {
	$cid = (int) COM_applyFilter($_GET['cid'], TRUE);
} else if (isset($_POST['cid'])) {
	$cid = (int) COM_applyFilter($_POST['cid'], TRUE);
} else {
	$cid = 0;
}

if (isset($_GET['template'])) {
	$template = COM_stripslashes($_GET['template']);
} else if (isset($_POST['template'])) {
	$template = COM_stripslashes($_POST['template']);
} else {
	$template = '';
}

if ($template != '') {
	$template = @basename($template);
}

$msg = 0;

// Does action
$hasError = FALSE;

if ($what === 'config') {
	if ($submit === $LANG_TMCE['admin_delete']) {
		// Deletes an entry
		if (($cid != 0) AND SEC_checkToken()) {
			$sql = "DELETE FROM {$_TABLES['tinymce_configs']} "
				 . "WHERE (cid = '" . addslashes($cid) . "') ";
			DB_query($sql);
			$msg = 1;
			$mode = 'list';
		} else {
			$hasError = TRUE;
		}
	} else if ($submit === $LANG_TMCE['admin_submit']) {
		if (SEC_checkToken()) {
			$cid              = (int) COM_applyFilter($_POST['cid'], TRUE);
			$title            = trim(COM_stripslashes($_POST['title']));
			$theme            = trim(COM_stripslashes($_POST['theme']));
			$buttons1         = trim(COM_stripslashes($_POST['buttons1']));
			$buttons2         = trim(COM_stripslashes($_POST['buttons2']));
			$buttons3         = trim(COM_stripslashes($_POST['buttons3']));
			$buttons4         = trim(COM_stripslashes($_POST['buttons4']));
			$plugins          = array_map('COM_stripslashes', $_POST['plugins']);
			$tb_allow_upload  = COM_stripslashes($_POST['tb_allow_upload']);
			$tb_allow_edit    = COM_stripslashes($_POST['tb_allow_edit']);
			$tb_allow_delete  = COM_stripslashes($_POST['tb_allow_delete']);
			$tb_allow_folders = COM_stripslashes($_POST['tb_allow_folders']);
			$enter_function   = COM_stripslashes($_POST['enter_function']);
			$group_id = COM_stripslashes($_POST['group_id'], TRUE);
			
			$enter_function = (int) $enter_function;
			if (($enter_function < 0) OR ($enter_function > 1)) {
				$enter_function = 0;
			}
			$plugins  = implode(',', $plugins);
			$buttons  = serialize(
				array(
					'buttons1' => preg_replace("/[^0-9a-zA-Z_|,]/", '', $buttons1),
					'buttons2' => preg_replace("/[^0-9a-zA-Z_|,]/", '', $buttons2),
					'buttons3' => preg_replace("/[^0-9a-zA-Z_|,]/", '', $buttons3),
					'buttons4' => preg_replace("/[^0-9a-zA-Z_|,]/", '', $buttons4),
				)
			);
			
			$cid4sql          = addslashes($cid);
			$title            = addslashes($title);
			$theme            = addslashes($theme);
			$buttons          = addslashes($buttons);
			$plugins          = addslashes($plugins);
			$tb_allow_upload  = addslashes($tb_allow_upload);
			$tb_allow_edit    = addslashes($tb_allow_edit);
			$tb_allow_delete  = addslashes($tb_allow_delete);
			$tb_allow_folders = addslashes($tb_allow_folders);
			$enter_function   = addslashes($enter_function);
			$group_id         = addslashes($group_id);
			
			if ($cid === 0) {
				$sql = "INSERT INTO {$_TABLES['tinymce_configs']} "
					 . "VALUES(NULL, '{$title}', '{$theme}', '{$buttons}', "
					 . "'{$plugins}', '{$tb_allow_upload}', '{$tb_allow_edit}', "
					 . "'{$tb_allow_delete}', '{$tb_allow_folders}', "
					 . "'{$enter_function}', '{$group_id}') ";
			} else {
				$sql = "UPDATE {$_TABLES['tinymce_configs']} "
					 . "SET title = '{$title}', theme = '{$theme}', "
					 . "buttons = '{$buttons}', plugins = '{$plugins}', "
					 . "tb_allow_upload = '{$tb_allow_upload}', "
					 . "tb_allow_edit = '{$tb_allow_edit}', "
					 . "tb_allow_delete = '{$tb_allow_delete}', "
					 . "tb_allow_folders = '{$tb_allow_folders}', "
					 . "enter_function = '{$enter_function}', "
					 . "group_id = '{$group_id}' "
					 . "WHERE (cid = '{$cid4sql}') ";
			}
			
			DB_query($sql);
			$msg = 2;
			$mode = 'list';
		} else {
			$hasError = TRUE;
		}
	}
} else if ($what === 'template') {
	if ($submit === $LANG_TMCE['admin_delete']) {
		// Deletes a template file
		if (($template != '') AND SEC_checkToken()) {
			$filename = realpath($_CONF['path_html'] . 'tinymce/templates/' . $template);
			clearstatcache();
			
			if (file_exists($filename)) {
				@unlink($filename);
			}
			
			$msg  = 3;
			$mode = 'list';
		} else {
			$hasError = TRUE;
		}
	} else if ($submit === $LANG_TMCE['admin_submit']) {
		if (isset($_POST['title']) AND isset($_POST['description'])
		 AND isset($_POST['content']) AND  SEC_checkToken()) {
		 	if ($template == '') {
				$template = TMCE_getNewFilename();
			}
			
			$filename    = $_CONF['path_html'] . 'tinymce/templates/' . $template;
			$title       = TMCE_cleanString(COM_stripslashes($_POST['title']));
			$description = TMCE_cleanString(COM_stripslashes($_POST['description']));
			$content     = COM_stripslashes($_POST['content']);
			$content     = str_replace(array("\n", "\r\n", "\r"), "\n", $content);
			$updated     = date('Y-m-d H:i:s');
			
			$content = '<!--  DO NOT REMOVE THIS HTML COMMENT!' . LB
					 . LB
					 . '  TINYMCE TEMPLATE HEADER' . LB
					 . LB
					 . '        Title: __TITLE_START__{' . $title . '}__TITLE_END__' . LB
					 . '  Description: __DESC_START__{' . $description . '}__DESC_END__' . LB
					 . '      Updated: __UPDATED_START__{' . $updated . '}__UPDATED_END__' . LB
					 . LB
					 . '-->' . LB
					 . '<div>' . LB
					 . $content
					 . '</div>' . LB;
			
			if (@file_put_contents($filename, $content) === FALSE) {
				COM_errorLog('TinyMCE: cannot write into template file "' . $filename . '".');
				$hasError = TRUE;
			} else {
				$msg  = 4;
				$mode = 'list';
			}
		} else {
			$hasError = TRUE;
		}
	}
}

// Display
if ($mode === 'edit') {
	$sub_mode = ($cid === 0) ? 'new' : 'change';
	
	if ($what === 'config') {
		$content = TMCE_getAdminConfigEditor($cid);
	} else if ($what === 'template') {
		$content = TMCE_getAdminTemplateEditor($template);
	}
} else if ($hasError) {
	$err_msg = "TinyMCE plugin: Someone has tried to illegally access the Admin page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: {$_SERVER['REMOTE_ADDR']}, cid: {$cid}, mode: {$mode}, action: {$submit}.";
	COM_errorLog($err_msg);
	$content = '<p>' . TMCE_str('admin_error') . '</p>';
} else {
	$sub_mode = 'list';
	
	if ($what === 'config') {
		$content = TMCE_getAdminConfigList();
	} else {
		$content = TMCE_getAdminTemplateList();
	}
}

$T = new Template($_CONF['path'] . 'plugins/tinymce/templates');
$T->set_file('admin', 'admin.thtml');
$T->set_var('xhtml', XHTML);
$T->set_var('lang_admin_head', TMCE_str('admin_head'));
$T->set_var('header', TMCE_getAdminHeader($what, $mode));
$T->set_var('lang_sub_mode', TMCE_str('admin_menu_' . $sub_mode));

if (($msg >= 1) AND ($msg <= 4)) {
	$msg = TMCE_str('admin_msg' . (string) $msg);
	$msg = '<p style="border: solid 2px #cc0000; padding: 5px;">' . $msg . '</p>';
	$T->set_var('msg', $msg);
}

$T->set_var('content', $content);
$T->parse('output', 'admin');
$display = COM_siteHeader()
		 . $T->finish($T->get_var('output'))
		 . COM_siteFooter();
COM_output($display);
