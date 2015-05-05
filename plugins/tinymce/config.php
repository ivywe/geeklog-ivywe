<?php

// +---------------------------------------------------------------------------+
// | TinyMCE Plugin for Geeklog - The Ultimate Weblog                          |
// +---------------------------------------------------------------------------+
// | geeklog/plugins/tinymce/config.php                                        |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), strtolower(basename(__FILE__))) !== FALSE) {
    die('This file can not be used on its own!');
}

/**
* Returns a list of directory names
*
* @param   string  $path  base path
* @return  array
* @note            This function must be defined in this file, not
*                  'functions.inc', because it will be called during
*                  installation.
*/
function TMCE_getDirList($path) {
	$retval = array();
	$dh = opendir($path);
	
	if ($dh !== FALSE) {
		clearstatcache();
		
		while (($item = readdir($dh)) !== FALSE) {
			if (($item != '.') AND ($item != '..') AND is_dir($path . '/' . $item)) {
				$retval[] = $item;
			}
		}
		
		closedir($dh);
	}
	
	sort($retval);
	
	return $retval;
}

global $_CONF, $_DB_table_prefix, $_TABLES, $_TMCE_CONF;

// set Plugin Table Prefix the Same as Geeklogs

$_TMCE_table_prefix = $_DB_table_prefix;

// Add to $_TABLES array the tables your plugin uses

$_TABLES['tinymce_configs'] = $_TMCE_table_prefix . 'tinymce_configs';

$_TMCE_CONF = array();

// Plugin info

$_TMCE_CONF['pi_version'] = '0.4.7';					// Plugin Version
$_TMCE_CONF['gl_version'] = '1.6.0';					// GL Version plugin for
$_TMCE_CONF['pi_url']     = 'http://mystral-kk.net/';	// Plugin Homepage
$_TMCE_CONF['GROUPS']     = array(
		'TinyMCE Admin' => 'Users in this group can administer the TinyMCE plugin',
);
$_TMCE_CONF['FEATURES']   = array(
		'tinymce.edit' => 'Access to TinyMCE editor',
);
$_TMCE_CONF['MAPPINGS']   = array(
		'tinymce.edit' => array('TinyMCE Admin'),
);

// Variable name to save the value of $_CONF['advanced_editor']
$_TMCE_CONF['db_var_name']  = 'tinymce_adveditor';

// Security token expirary in editing config (in seconds)
$_TMCE_CONF['token_expirary']  = 30 * 60;

// The number of TinyMCE plugins displayed in a row in admin editor
$_TMCE_CONF['plugin_num_columns'] = 4;

// Template header
$_TMCE_CONF['template_header'] = <<<EOD
<!--  DO NOT REMOVE THIS HTML COMMENT!
  
  TINYMCE TEMPLATE HEADER

        Title: __TITLE_START__{title}__TITLE_END__
  Description: __DESC_START__{description}__DESC_END__
      Updated: {updated}

-->

EOD;

// Default config data
$_TMCE_CONF['default_data'] = array(
	'cid'				=> 0,
	'title'				=> 'new',
	'theme'				=> 'advanced',
	'buttons'			=> serialize(
		array(
			'buttons1' => 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect',
			'buttons2' => 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,preview,|,forecolor,backcolor',
			'buttons3' => 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen',
			'buttons4' => 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft'
		)
	),
	'plugins'	=> implode(',', TMCE_getDirList($_CONF['path_html'] . 'tinymce/js/tiny_mce/plugins')),
	'tb_allow_upload'	=> 1,	// 1 = TRUE, 0 = FALSE
	'tb_allow_edit'		=> 0,	// 1 = TRUE, 0 = FALSE
	'tb_allow_delete'	=> 0,	// 1 = TRUE, 0 = FALSE
	'tb_allow_folders'	=> 1,	// 1 = TRUE, 0 = FALSE
	'enter_function'	=> 0,	// 0 = <p>, 1 = <br>
	'group_id'			=> 2,
);

// Default definitions for style selector
$_TMCE_CONF['default_styles'] = <<<EOD
gl_tinymce.style_formats = [
	{title : 'Bold text', inline : 'b'},
	{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
	{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
	{title : 'Table styles'},
	{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}	// Don't put a comma in the last line
];

EOD;
