<?php

// +---------------------------------------------------------------------------+
// | TinyMCE Plugin for Geeklog - The Ultimate Weblog                          |
// +---------------------------------------------------------------------------+
// | geeklog/plugins/tinymce/install_defaults.php                              |
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
* TinyMCE default settings
*
* Initial Installation Defaults used when loading the online configuration
* records.  These settings are only used during the initial installation
* and not referenced any more once the plugin is installed
*
*/
global $_TMCE_DEFAULT;

$_TMCE_DEFAULT = array();

// Currently, 'auto', 'all', 'css_class' and 'css_id' are supported
$_TMCE_DEFAULT['targets'] = 'css_class';

// CSS Class name of textarea tags to attach TinyMCE to when
// $_TMCE_CONF['targets'] is 'css_class'
$_TMCE_DEFAULT['target_class'] = 'tinymce_enabled';

// CSS ids of textarea tags to attach TinyMCE to when
// $_TMCE_CONF['targets'] is 'css_id'
$_TMCE_DEFAULT['target_ids'] = array('introtext', 'bodytext', 'html_content');

// The height of the editor in pixels or percent.  If set to '', this value
// will be igonored and instead the height of the HTML element TinyMCE replaces
// will be used.
$_TMCE_DEFAULT['height'] = '360';

// The width of the editor in pixels or percent.  If set to '', this value
// will be igonored and instead the width of the HTML element TinyMCE replaces
// will be used.
$_TMCE_DEFAULT['width'] = '90%';

//===================================================================
// for TinyBrowser plugin
//===================================================================

// TinyBrowser: Folder permissions for Unix servers only
$_TMCE_DEFAULT['tb_unixpermissions'] = '777';	// Must be a string

// TinyBrowser: Clean filenames on upload
$_TMCE_DEFAULT['tb_cleanfilename'] = TRUE;

// TinyBrowser: Permitted file extensions
$_TMCE_DEFAULT['tb_filetype_image'] = array('jpg', 'jpeg', 'gif', 'png'); // Image file types

$_TMCE_DEFAULT['tb_filetype_media'] = array('swf', 'dcr', 'mov', 'qt', 'mpg', 'mp3', 'mp4', 'mpeg', 'avi', 'wmv', 'wm', 'asf', 'asx', 'wmx', 'wvx', 'rm', 'ra', 'ram'); // Media file types

// TinyBrowser: Prohibited file extensions
$_TMCE_DEFAULT['tb_prohibited'] = array('php','php3','php4','php5','phtml','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','dll','reg','cgi', 'sh', 'py','asa','asax','config','com','inc');

// TinyBrowser: File upload size limit (0 is unlimited)
$_TMCE_DEFAULT['tb_maxsize_image'] = 0;
$_TMCE_DEFAULT['tb_maxsize_media'] = 0;
$_TMCE_DEFAULT['tb_maxsize_file']  = 0;


// TinyBrowser: Image automatic resize on upload (0 is no resize)
$_TMCE_DEFAULT['tb_imageresize_width']  = 0;
$_TMCE_DEFAULT['tb_imageresize_height'] = 0;

// TinyBrowser: Image thumbnail size in pixels
$_TMCE_DEFAULT['tb_thumbsize'] = 80;

// TinyBrowser: Image and thumbnail quality, higher is better (1 to 99)
$_TMCE_DEFAULT['tb_imagequality'] = 80; // only used when resizing or rotating
$_TMCE_DEFAULT['tb_thumbquality'] = 80;


// TinyBrowser: pop-up window size
$_TMCE_DEFAULT['tb_window_width']  = 770;
$_TMCE_DEFAULT['tb_window_height'] = 480;

// TinyBrowser: File Pagination - split results into pages (0 is none)
$_TMCE_DEFAULT['tb_pagination'] = 0;

// TinyBrowser: Date format, as per php date function
$_TMCE_DEFAULT['tb_dateformat'] = 'Y-m-d H:i';

/**
* Initializes TinyMCE plugin configuration
*
* @return   boolean     TRUE: success; FALSE: an error occurred
*/
function plugin_initconfig_tinymce() {
    global $_TMCE_CONF, $_TMCE_DEFAULT;
	
    if (is_array($_TMCE_CONF) AND (count($_TMCE_CONF) > 0)) {
        $_TMCE_DEFAULT = array_merge($_TMCE_DEFAULT, $_TMCE_CONF);
    }
	
	$me = 'tinymce';
    $c = config::get_instance();
	
    if (!$c->group_exists($me)) {
        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, TRUE, $me);
        $c->add('sg_tinybrwoser', NULL, 'subgroup', 1, 0, NULL, 0, TRUE, $me);
        $c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, TRUE, $me);
        $c->add('fs_tb_upload', NULL, 'fieldset', 1, 1, NULL, 0, TRUE, $me);
        $c->add('fs_tb_filesize', NULL, 'fieldset', 1, 2, NULL, 0, TRUE, $me);
        $c->add('fs_tb_resize', NULL, 'fieldset', 1, 3, NULL, 0, TRUE, $me);
        $c->add('fs_tb_thumb', NULL, 'fieldset', 1, 4, NULL, 0, TRUE, $me);
        $c->add('fs_tb_appearance', NULL, 'fieldset', 1, 5, NULL, 0, TRUE, $me);
		
        $c->add('targets', $_TMCE_DEFAULT['targets'], 'select', 0, 0, 12, 1, TRUE, $me);
        $c->add('target_class', $_TMCE_DEFAULT['target_class'], 'text', 0, 0, NULL, 2, TRUE, $me);
        $c->add('target_ids', $_TMCE_DEFAULT['target_ids'], '%text', 0, 0, NULL, 3, TRUE, $me);
        $c->add('height', $_TMCE_DEFAULT['height'], 'text', 0, 0, NULL, 4, TRUE, $me);
        $c->add('width', $_TMCE_DEFAULT['width'], 'text', 0, 0, NULL, 5, TRUE, $me);
		
		// for TinyBrowser plugin
		// fs_tb_upload (sg=1, fs=1)
        $c->add('tb_unixpermissions', $_TMCE_DEFAULT['tb_unixpermissions'], 'text', 1, 1, NULL, 1, TRUE, $me);
        $c->add('tb_cleanfilename', $_TMCE_DEFAULT['tb_cleanfilename'], 'select', 1, 1, 1, 2, TRUE, $me);
        $c->add('tb_filetype_image', $_TMCE_DEFAULT['tb_filetype_image'], '%text', 1, 1, 1, 3, TRUE, $me);
        $c->add('tb_filetype_media', $_TMCE_DEFAULT['tb_filetype_media'], '%text', 1, 1, NULL, 4, TRUE, $me);
        $c->add('tb_prohibited', $_TMCE_DEFAULT['tb_prohibited'], '%text', 1, 1, NULL, 5, TRUE, $me);
		
		// fs_tb_filesize (sg=1, fs=2)
        $c->add('tb_maxsize_image', $_TMCE_DEFAULT['tb_maxsize_image'], 'text', 1, 2, NULL, 0, TRUE, $me);
        $c->add('tb_maxsize_media', $_TMCE_DEFAULT['tb_maxsize_media'], 'text', 1, 2, NULL, 1, TRUE, $me);
        $c->add('tb_maxsize_file', $_TMCE_DEFAULT['tb_maxsize_file'], 'text', 1, 2, NULL, 2, TRUE, $me);
		
		// fs_tb_resize (sg=1, fs=3)
        $c->add('tb_imagequality', $_TMCE_DEFAULT['tb_imagequality'], 'text', 1, 3, NULL, 0, TRUE, $me);
        $c->add('tb_imageresize_width', $_TMCE_DEFAULT['tb_imageresize_width'], 'text', 1, 3, NULL, 1, TRUE, $me);
        $c->add('tb_imageresize_height', $_TMCE_DEFAULT['tb_imageresize_height'], 'text', 1, 3, NULL, 2, TRUE, $me);
		
		// fs_tb_thumb (sg=1, fs=4)
        $c->add('tb_thumbsize', $_TMCE_DEFAULT['tb_thumbsize'], 'text', 1, 4, NULL, 0, TRUE, $me);
        $c->add('tb_thumbquality', $_TMCE_DEFAULT['tb_thumbquality'], 'text', 1, 4, NULL, 2, TRUE, $me);
		
		// fs_tb_appearance (sg=1, fs=5)
        $c->add('tb_window_width', $_TMCE_DEFAULT['tb_window_width'], 'text', 1, 5, NULL, 0, TRUE, $me);
        $c->add('tb_window_height', $_TMCE_DEFAULT['tb_window_height'], 'text', 1, 5, NULL, 1, TRUE, $me);
        $c->add('tb_pagination', $_TMCE_DEFAULT['tb_pagination'], 'text', 1, 5, NULL, 2, TRUE, $me);
        $c->add('tb_dateformat', $_TMCE_DEFAULT['tb_dateformat'], 'text', 1, 5, NULL, 3, TRUE, $me);
    }
	
    return TRUE;
}
