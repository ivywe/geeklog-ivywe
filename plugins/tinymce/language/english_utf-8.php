<?php

// +---------------------------------------------------------------------------+
// | TinyMCE Plugin for Geeklog - The Ultimate Weblog                          |
// +---------------------------------------------------------------------------+
// | geeklog/plugins/tinymce/language/english_utf-8.php                        |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 mystral-kk - geeklog AT mystral-kk DOT net             |
// |                                                                           |
// | Constructed with the Universal Plugin                                     |
// +---------------------------------------------------------------------------|
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
// | along with this program; if not, write to the Free Software               |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA|
// |                                                                           |
// +---------------------------------------------------------------------------|

if (strpos(strtolower($_SERVER['PHP_SELF']), strtolower(basename(__FILE__))) !== FALSE) {
    die('This file can not be used on its own!');
}

$LANG_TMCE = array (
    'plugin'            		=> 'TinyMCE',
	'admin'		        		=> 'TinyMCE',
	'admin_head'				=> 'TinyMCE configurations',
	'admin_desc_config'			=> 'You can specifiy toolbar buttons and plugins used in TinyMCE for each Geeklog group.  To add a new configuration, click on the above "Create config" link.  If you define more than one configuration for one group, the latest one (which will be shown in the loswest of the screen) will be valid.  To change the global setting for TinyMCE, go to <a href="' . $_CONF['site_admin_url'] . '/configuration.php">Configuration</a>.',
	'admin_desc_template'		=> 'You can edit templates to use with TinyMCE.  To ass a new template, click on the above "Create template".  To change the global setting for TinyMCE, go to <a href="' . $_CONF['site_admin_url'] . '/configuration.php">Configuration</a>.',
	'admin_menu_list'			=> 'List of configurations',
	'admin_menu_new'			=> 'New',
	'admin_menu_change'			=> 'Change',
	'admin_menu_config_list'	=> 'Config list',
	'admin_menu_config_edit'	=> 'Create config',
	'admin_menu_template_list'	=> 'Template list',
	'admin_menu_template_edit'	=> 'Create template',
	'admin_menu_cc'				=> 'Admin',
	'admin_edit'				=> 'Edit',
	'admin_title'				=> 'Title',
	'admin_theme'				=> 'Theme',
	'admin_toolbars'			=> 'Toolbars',
	'admin_avaiable_buttons'	=> 'Available buttons',
	'admin_disabled_buttons'	=> 'Disabled buttons',
	'admin_plugins'				=> 'Plugins',
	'admin_tb_perms'			=> 'Configuration for tinyBrowser plugin',
	'admin_tb_allow_upload'		=> 'Allow uploading',
	'admin_tb_allow_edit' 		=> 'Allow editing',
	'admin_tb_allow_delete'	 	=> 'Allow deleting',
	'admin_tb_allow_folders'	=> 'Allow folders',
	'admin_enter_function'		=> 'Function of ENTER key',
	'admin_ef_paragraph'		=> 'Insert a <p> tag',	// = 0
	'admin_ef_newline'			=> 'Insert a <br> tag',	// = 1
	'admin_grp_name'			=> 'Group',
	'admin_submit'				=> 'Submit',
	'admin_delete'				=> 'Delete',
	'admin_confirm_delete'		=> 'Do you really want to delete this item?',
	'admin_error'				=> 'An error occurred.  Possible causes are illegal access or the expiration of a security token.  This error has been recored.',
	'admin_config_help'			=> 'For further indormation about configuring toolbar buttons and plugins, read <a href="%s">install.html</a>.',
	'admin_no_template'			=> 'No templates',
	'admin_description'			=> 'Description',
	'admin_content'				=> 'Content',
	'admin_updated'				=> 'Updated',
	'admin_msg1'				=> 'Successfully deleted a configuration.',
	'admin_msg2'				=> 'Successfully saved a configuration.',
	'admin_msg3'				=> 'Successfully deleted a template file.',
	'admin_msg4'				=> 'Successfully saved a template file.',
	
	'abbr'				=> 'Abbreviation',
	'absolute'			=> 'Toggle absolute positioning',
	'acronym'			=> 'Acronym',
	'advhr'				=> 'Horizontal rule',
	'advimage'			=> 'Insert/edit image',
	'advlink'			=> 'Insert/edit link',
	'advlist'			=> 'List',
	'anchor'			=> 'Insert/edit anchor',
	'attribs'			=> 'Insert/Edit Attributes',
	'autoresize'		=> 'Resize automatically',
	'autosave'			=> 'Save automatically',
	'backcolor'			=> 'Background color',
	'bbcode'			=> 'BB code',
	'blockquote'		=> 'Quote',
	'bold'				=> 'Bold',
	'bullist'			=> 'Unnumbered list',
	'charmap'			=> 'Insert special characters',
	'cite'				=> 'Cite',
	'cleanup'			=> 'Clean up code',
	'code'				=> 'HTML code',
	'contextmenu'		=> 'Context menu',
	'copy'				=> 'Copy',
	'cut'				=> 'Cut',
	'del'				=> 'Delete',
	'directionality'	=> 'Directionality',
	'emotions'			=> 'Emotions',
	'fontselect'		=> 'Font',
	'fontsizeselect'	=> 'Font size',
	'forecolor'			=> 'Foreground color',
	'formatselect'		=> 'Format paragraph',
	'fullpage'			=> 'Full page',
	'fullscreen'		=> 'Full screen',
	'help'				=> 'Help',
	'hr'				=> 'Horizontal rule',
	'iespell'			=> 'Toggle spellchecker',
	'image'				=> 'Insert/edit image',
	'indent'			=> 'Indent',
	'inlinepopups'		=> 'Inline popoups',
	'ins'				=> 'Insert',
	'insertdate'		=> 'Insert date',
	'insertdate'		=> 'Insert date/time',
	'insertlayer'		=> 'Insert a new layer',
	'inserttime'		=> 'Insert time',
	'italic'			=> 'Italic',
	'justifycenter'		=> 'Justify center',
	'justifyfull'		=> 'Justifiy full',
	'justifyleft'		=> 'Justifiy left',
	'justifyright'		=> 'Justify right',
	'layer'				=> 'Layer',
	'legacyoutput'		=> 'legacyoutput',
	'link'				=> 'Insert/edit link',
	'ltr'				=> 'directionality left to right',
	'media'				=> 'Inser/edit media',
	'movebackward'		=> 'Move backward',
	'moveforward'		=> 'Move forward',
	'newdocument'		=> 'New',
	'nonbreaking'		=> 'Insert non-breaking space character',
	'noneditable'		=> 'noneditable',
	'numlist'			=> 'Numbered list',
	'outdent'			=> 'Outdent',
	'pagebreak'			=> 'Insert page break',
	'paste'				=> 'Paste',
	'pastetext'			=> 'Paste as text',
	'pasteword'			=> 'Paste from MS Word',
	'preview'			=> 'Preview',
	'print'				=> 'Print',
	'redo'				=> 'Redo',
	'removeformat'		=> 'Remove format',
	'replace'			=> 'Replace',
	'restoredraft'		=> 'Restore auto-saved content',
	'rtl'				=> 'directionality right to left',
	'save'				=> 'Save',
	'search'			=> 'Search',
	'searchreplace'		=> 'Search/replace',
	'spellchecker'		=> 'Toggle spellchecker',
	'strikethrough'		=> 'Strike through',
	'style'				=> 'Style',
	'styleprops'		=> 'Edit CSS Style',
	'styleselect'		=> 'Style',
	'sub'				=> 'Subscript',
	'sup'				=> 'Superscript',
	'tabfocus'			=> 'Tab focus',
	'table'				=> 'Table',
	'tablecontrols'		=> 'Inserts a new table',
	'template'			=> 'Insert a template',
	'tinybrowser'		=> 'tinyBrowser',
	'underline'			=> 'Underline',
	'undo'				=> 'Undo',
	'unlink'			=> 'Remove a link',
	'visualaid'			=> 'Toggle visual aid',
	'visualchars'		=> 'Visual control characters on/off',
	'wordcount'			=> 'Word count',
	'xhtmlxtras'		=> 'xhtmlxtras',
	
	'emojiau'			=> 'pictograms for au',
	'emojidocomo'		=> 'pictograms for docomo',
	'emojisoftbank'		=> 'pictograms for softbank',
);

// Localization of the Admin Configuration UI
$LANG_configsections['tinymce'] = array(
    'label' => $LANG_TMCE['plugin'],
    'title' => $LANG_TMCE['plugin'] . ' configuration',
);

$LANG_confignames['tinymce'] = array(
    'targets'					=> 'Target textareas',
	'target_class'				=> 'CSS class name',
	'target_ids'				=> 'CSS IDs',
	'height'					=> 'Editor height',
	'width'						=> 'Editor width',
	
	'tb_unixpermissions'		=> 'Folder permissions(in octals WITHOUT preceding zeros)',
	'tb_cleanfilename'			=> 'Clean filename',
	'tb_filetype_image'			=> 'Image file types',
	'tb_filetype_media'			=> 'Media file types',
	'tb_prohibited'				=> 'Prohibited file types',
	
	'tb_maxsize_image'			=> 'Image file maximum size(0=unlimited)',
	'tb_maxsize_media'			=> 'Media file maximum size(0=unlimited)',
	'tb_maxsize_file'			=> 'Other file maximum size(0=unlimited)',
	
	'tb_imagequality'			=> 'Image quality(1 to 99)',
	'tb_imageresize_width'		=> 'Width(0=no resizing)',
	'tb_imageresize_height'		=> 'Height(0=no resizing)',
	
	'tb_thumbsize'				=> 'Image thumbnail size(in pixels)',
	'tb_thumbquality'			=> 'Thumbnail quality(1 to 99)',
	
	'tb_window_width'			=> 'Pop-up window width',
	'tb_window_height'			=> 'Pop-up window height',
	'tb_pagination'				=> 'File Pagination(0=no paging)',
	'tb_dateformat'				=> 'Date format',
);

$LANG_configsubgroups['tinymce'] = array(
    'sg_main'					=> 'Main',
	'sg_tinybrwoser'			=> 'TinyBrowser',
);

$LANG_fs['tinymce'] = array(
    'fs_main'					=> 'TinyMCE main configuration',
    'fs_tb_upload'				=> 'Upload settings',
	'fs_tb_filesize'			=> 'Filesizes',
	'fs_tb_resize'				=> 'Resizing images',
	'fs_tb_thumb'				=> 'Creating thumbnails',
	'fs_tb_appearance'			=> 'Appearance',
);

// Note: entries 0, 1, 9, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['tinymce'] = array(
    0 => array('Yes' => 1, 'No' => 0),
    1 => array('Yes' => TRUE, 'No' => FALSE),
    9 => array('Forward to page' => 'item', 'Display List' => 'list', 'Display Home' => 'home', 'Display Admin' => 'admin'),
	12 => array('Select automatically' => 'auto', 'All &lt;textarea&gt; tags' => 'all', 'Specific &lt;textarea&gt; tags(with a specific CSS class name)' => 'css_class', 'Specific &lt;textarea&gt; tags(with specific CSS IDs)' => 'css_id'),
);
