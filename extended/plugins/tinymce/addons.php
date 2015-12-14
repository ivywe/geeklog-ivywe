<?php

// +---------------------------------------------------------------------------+
// | TinyMCE Plugin for Geeklog - The Ultimate Weblog                          |
// +---------------------------------------------------------------------------+
// | geeklog/plugins/tinymce/addons.php                                        |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), strtolower(basename(__FILE__))) !== FALSE) {
    die('This file can not be used on its own!');
}

/**
* Information about buttons provided by TinyMCE builtin-commands
*
*   Format:
*   'builtin_name' => array(
*       'buttons'  => array of buttons this command will provide,
*       'html'     => array of HTML elements and attributes this command will produce,
*   )
*/
$_TMCE_BUILTINS = array(
	'anchor'			=> array(
		'buttons'	=> array('anchor'),
		'html'		=> array(
							'a' => array('name'),
						),
	),
	
	'backcolor'			=> array(
		'buttons'	=> array('backcolor'),
		'html'		=> array(
							'span' => array('style'),
						),
	),
	
	'blockquote'		=> array(
		'buttons'	=> array('blockquote'),
		'html'		=> array(
							'blockquote' => array(),
						),
	),
	
	'bold'				=> array(
		'buttons'	=> array('bold'),
		'html'		=> array(
							'strong' => array(),
						),
	),
	
	'bullist'			=> array(
		'buttons'	=> array('bullist'),
		'html'		=> array(
							'ul' => array(),
							'li' => array(),
						),
	),
	
	'charmap'			=> array(
		'buttons'	=> array('charmap'),
		'html'		=> array(),
	),
	
	'cleanup'			=> array(
		'buttons'	=> array('cleanup'),
		'html'		=> array(),
	),
	
	'code'				=> array(
		'buttons'	=> array('code'),
		'html'		=> array(),
	),
	
	'copy'				=> array(
		'buttons'	=> array('copy'),
		'html'		=> array(),
	),
	
	'cut'				=> array(
		'buttons'	=> array('cut'),
		'html'		=> array(),
	),
	
	'fontselect'		=> array(
		'buttons'	=> array('fontselect'),
		'html'		=> array(
							'span' => array('style'),
						),
	),
	
	'fontsizeselect'	=> array(
		'buttons'	=> array('fontsizeselect'),
		'html'		=> array(
							'span' => array('style'),
						),
	),
	
	'forecolor'			=> array(
		'buttons'	=> array('forecolor'),
		'html'		=> array(
							'span' => array('style'),
						),
	),
	
	'formatselect'		=> array(
		'buttons'	=> array('formatselect'),
		'html'		=> array(),
	),
	
	'help'				=> array(
		'buttons'	=> array('help'),
		'html'		=> array(),
	),
	
	'hr'				=> array(
		'buttons'	=> array('hr'),
		'html'		=> array(
							'hr' => array(),
						),
	),
	
	'image'				=> array(
		'buttons'	=> array('image'),
		'html'		=> array(
							'img' => array(),
						),
	),
	
	'indent'			=> array(
		'buttons'	=> array('indent'),
		'html'		=> array(
							'p' => array('style'),
						),
	),
	
	'italic'			=> array(
		'buttons'	=> array('italic'),
		'html'		=> array(
							'em' => array(),
						),
	),
	
	'justifycenter'		=> array(
		'buttons'	=> array('justifycenter'),
		'html'		=> array(
							'p' => array('style'),
						),
	),
	
	'justifyfull'		=> array(
		'buttons'	=> array('justifyfull'),
		'html'		=> array(
							'p' => array('style'),
						),
	),
	
	'justifyleft'		=> array(
		'buttons'	=> array('justifyleft'),
		'html'		=> array(
							'p' => array('style'),
						),
	),
	
	'justifyright'		=> array(
		'buttons'	=> array('justifyright'),
		'html'		=> array(
							'p' => array('style'),
						),
	),
	
	'link'				=> array(
		'buttons'	=> array('link'),
		'html'		=> array(
							'a' => array('href'),
						),
	),
	
# !Never enable 'newdocument', since this is unnecessary for Geeklog
# 	'newdocument'		=> array(
# 		'buttons'	=> array('newdocument'),
# 		'html'		=> array(),
# 	),
	
	'numlist'			=> array(
		'buttons'	=> array('numlist'),
		'html'		=> array(
							'ol' => array(),
							'li' => array(),
						),
	),
	
	'outdent'			=> array(
		'buttons'	=> array('outdent'),
		'html'		=> array(
							'p' => array('style'),
						),
	),
	
	'paste'				=> array(
		'buttons'	=> array('paste'),
		'html'		=> array(),
	),
	
	'redo'				=> array(
		'buttons'	=> array('redo'),
		'html'		=> array(),
	),
	
	'removeformat'		=> array(
		'buttons'	=> array('removeformat'),
		'html'		=> array(),
	),
	
	'strikethrough'		=> array(
		'buttons'	=> array('strikethrough'),
		'html'		=> array(
							'span' => array('style'),
						),
	),
	
	'styleselect'		=> array(
		'buttons'	=> array('styleselect'),
		'html'		=> array(
							'span' => array('style'),
						),
	),
	
	'sub'				=> array(
		'buttons'	=> array('sub'),
		'html'		=> array(
							'sub' => array(),
						),
	),
	
	'sup'				=> array(
		'buttons'	=> array('sup'),
		'html'		=> array(
							'sup' => array(),
						),
	),
	
	'underline'			=> array(
		'buttons'	=> array('underline'),
		'html'		=> array(
							'span' => array('style'),
						),
	),
	
	'undo'				=> array(
		'buttons'	=> array('undo'),
		'html'		=> array(),
	),
	
	'unlink'			=> array(
		'buttons'	=> array('unlink'),
		'html'		=> array(
							'a' => array('href'),
						),
	),
	
	'visualaid'			=> array(
		'buttons'	=> array('visualaid'),
		'html'		=> array(),
	),
);

/**
* Information about TinyMCE plugins
*
*   Format:
*   'plugin_name' => array(
*       'plugins'  => array of plugins this plugin is dependent on,
*       'buttons'  => array of buttons this plugin will provide,
*       'html'     => array of HTML elements and attributes this plugin will produce,
*   )
*/
$_TMCE_PLUGINS = array(
	'advhr'				=> array(
		'plugins'	=> array(),
		'buttons'	=> array('advhr'),
		'html'		=> array(
							'hr' => array('style'),
						),
	),
	
	'advimage'			=> array(
		'plugins'	=> array(),
		'buttons'	=> array('image'),
		'html'		=> array(
							'img' => array(),
						),
	),
	
	'advlink'			=> array(
		'plugins'	=> array(),
		'buttons'	=> array('link'),
		'html'		=> array(
							'a' => array('href'),
						),
	),
	
	'advlist'			=> array(
		'plugins'	=> array(),
		'buttons'	=> array('bullist', 'numlist'),
		'html'		=> array(
							'ul' => array(),
							'ol' => array(),
							'li' => array(),
						),
	),
	
	'autoresize'		=> array(
		'plugins'	=> array(),
		'buttons'	=> array(),
		'html'		=> array(),
	),
	
	'autosave'			=> array(
		'plugins'	=> array(),
		'buttons'	=> array('restoredraft'),
		'html'		=> array(),
	),
	
	'bbcode'			=> array(
		'plugins'	=> array(),
		'buttons'	=> array(),
		'html'		=> array(),
	),
	
	'contextmenu'		=> array(
		'plugins'	=> array(),
		'buttons'	=> array(),
		'html'		=> array(),
	),
	
	'directionality'	=> array(
		'plugins'	=> array(),
		'buttons'	=> array('ltr', 'rtl'),
		'html'		=> array(),
	),
	
	'emotions'			=> array(
		'plugins'	=> array(),
		'buttons'	=> array('emotions'),
		'html'		=> array(
							'img' => array(),
						),
	),
	
	// Example plugin, which is just an exmple and never does anything
	'example'			=> array(
		'plugins'	=> array(),
		'buttons'	=> array(),
		'html'		=> array(),
	),
	
	// Produces an entire HTML document including <head> section
	'fullpage'			=> array(
		'plugins'	=> array(),
		'buttons'	=> array('fullpage'),
		'html'		=> array(),
	),
	
	'fullscreen'		=> array(
		'plugins'	=> array(),
		'buttons'	=> array('fullscreen'),
		'html'		=> array(),
	),
	
	'iespell'			=> array(
		'plugins'	=> array(),
		'buttons'	=> array('iespell'),
		'html'		=> array(),
	),
	
	'inlinepopups'		=> array(
		'plugins'	=> array(),
		'buttons'	=> array(),
		'html'		=> array(),
	),
	
	'insertdatetime'	=> array(
		'plugins'	=> array(),
		'buttons'	=> array('insertdate', 'inserttime'),
		'html'		=> array(),
	),
	
	'layer'				=> array(
		'plugins'	=> array(),
		'buttons'	=> array('absolute', 'insertlayer', 'movebackward', 'moveforward'),
		'html'		=> array(),
	),
	
	'legacyoutput'		=> array(
		'plugins'	=> array(),
		'buttons'	=> array(),
		'html'		=> array(
							'font'   => array(),
							'b'      => array(),
							'i'      => array(),
							'u'      => array(),
							'strike' => array(),
						),
	),
	
	'media'				=> array(
		'plugins'	=> array('contextmenu'),
		'buttons'	=> array('media'),
		'html'		=> array(
							'img' => array(),
						),
	),
	
	'nonbreaking'		=> array(
		'plugins'	=> array(),
		'buttons'	=> array('nonbreaking'),
		'html'		=> array(),
	),
	
	'noneditable'		=> array(
		'plugins'	=> array(),
		'buttons'	=> array(),
		'html'		=> array(),
	),
	
	'pagebreak'			=> array(
		'plugins'	=> array(),
		'buttons'	=> array('pagebreak'),
		'html'		=> array(
							'img' => array(),
						),
	),
	
	'paste'				=> array(
		'plugins'	=> array(),
		'buttons'	=> array('pastetext', 'selectall', 'pasteword'),
		'html'		=> array(),
	),
	
	'preview'			=> array(
		'plugins'	=> array(),
		'buttons'	=> array('preview'),
		'html'		=> array(),
	),
	
	'print'				=> array(
		'plugins'	=> array(),
		'buttons'	=> array('print'),
		'html'		=> array(),
	),
	
	// Saves the current content, thus should be unnecessary for Geeklog
	'save'				=> array(
		'plugins'	=> array(),
		'buttons'	=> array('save', 'cancel'),
		'html'		=> array(),
	),
	
	'searchreplace'		=> array(
		'plugins'	=> array(),
		'buttons'	=> array('search', 'replace'),
		'html'		=> array(),
	),
	
	'spellchecker'		=> array(
		'plugins'	=> array(),
		'buttons'	=> array('spellchecker'),
		'html'		=> array(),
	),
	
	'style'				=> array(
		'plugins'	=> array(),
		'buttons'	=> array('styleprops'),
		'html'		=> array(),
	),
	
	'tabfocus'			=> array(
		'plugins'	=> array(),
		'buttons'	=> array(),
		'html'		=> array(),
	),
	
	'table'				=> array(
		'plugins'	=> array(),
		'buttons'	=> array('tablecontrols'),
		'html'		=> array(
							'table' => array(),
						),
	),
	
	'template' 			=> array(
		'plugins'	=> array(),
		'buttons'	=> array('template'),
		'html'		=> array(),
	),
	
	'tinybrowser'		=> array(
		'plugins'	=> array(),
		'buttons'	=> array(),
		'html'		=> array(
							'img' => array(),
						),
	),
	
	'visualchars'		=> array(
		'plugins'	=> array(),
		'buttons'	=> array('visualchars'),
		'html'		=> array(),
	),
	
	'wordcount'			=> array(
		'plugins'	=> array(),
		'buttons'	=> array(),
		'html'		=> array(),
	),
	
	'xhtmlextras'		=> array(
		'plugins'	=> array(),
		'buttons'	=> array('abbr', 'acronym', 'attribs', 'cite', 'del', 'ins'),
		'html'		=> array(
							'abbr'    => array(),
							'acronym' => array(),
							'cite'    => array(),
							'del'     => array(),
							'ins'     => array(),
						),
	),
	
	'emojiau'			=> array(
		'plugins'	=> array(),
		'buttons'	=> array('emojiau'),
		'html'		=> array(
							'img' => array(),
						),
	),
	
	'emojidocomo'			=> array(
		'plugins'	=> array(),
		'buttons'	=> array('emojidocomo'),
		'html'		=> array(
							'img' => array(),
						),
	),
	
	'emojisoftbank'			=> array(
		'plugins'	=> array(),
		'buttons'	=> array('emojisoftbank'),
		'html'		=> array(
							'img' => array(),
						),
	),
);

/**
* Core plugins
*
* Essential plugins which cannot be disabled through Geeklog and TinyMCE
* configurations
*/
$_TMCE_CORE_PLUGINS = array(
	'contextmenu', 'inlinepopups', 'xhtmlxtras',
);

/**
* Plugins that should be excluded from Geeklog
*/
$_TMCE_EXCLUDE_PLUGINS = array(
	'example',	// example plugin, which is just an exmple and never does anything
	'fullpage',	// produces an entire HTML document including <head> section
	'save',		// should be unnecessary for Geeklog
);
