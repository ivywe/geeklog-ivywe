<?php
/*
TinyBrowser 1.41 - A TinyMCE file browser (C) 2008  Bryn Jones
(author website - http://www.lunarvis.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// switch off error handling, to use custom handler
error_reporting(E_ALL); 

// set script time out higher, to help with thumbnail generation
@set_time_limit(240);

require_once dirname(__FILE__) . '/../../../../../lib-common.php';

if (!in_array('tinymce', $_PLUGINS)) {
	die('No direct access!');
}

$tb_perms = TMCE_getConfig();

$tinybrowser = array();

// Session control and security check - to enable please uncomment
//if(isset($_GET['sessidpass'])) session_id($_GET['sessidpass']); // workaround for Flash session bug
//session_start();
//$tinybrowser['sessioncheck'] = 'authenticated_user'; //name of session variable to check

// Random string used to secure Flash upload if session control not enabled - be sure to change!
$tinybrowser['obfuscate'] = '5505a454fa748e311655fb50911b7614';

// Set default language (ISO 639-1 code)
list($tinybrowser['language'], $tinybrowser['directionality']) = TMCE_getLocale();

// Set the integration type (TinyMCE is default)
$tinybrowser['integration'] = 'tinymce'; // Possible values: 'tinymce', 'fckeditor'

// Default is rtrim($_SERVER['DOCUMENT_ROOT'],'/') (suitable when using absolute paths, but can be set to '' if using relative paths)
$tinybrowser['docroot'] = rtrim($_SERVER['DOCUMENT_ROOT'], '/');

// Folder permissions for Unix servers only
$tinybrowser['unixpermissions'] = $_TMCE_CONF['tb_unixpermissions'];

// File upload paths (set to absolute by default)
$tinybrowser['path']['image'] = preg_replace("|^https?://[^/]+|", '', $_CONF['site_url'] . '/images/library/Image/'); // Image files location - also creates a '_thumbs' subdirectory within this path to hold the image thumbnails
$tinybrowser['path']['media'] = preg_replace("|^https?://[^/]+|", '', $_CONF['site_url'] . '/images/library/Media/'); // Media files location
$tinybrowser['path']['file']  = preg_replace("|^https?://[^/]+|", '', $_CONF['site_url'] . '/images/library/File/'); // Other files location

// File link paths - these are the paths that get passed back to TinyMCE or your application (set to equal the upload path by default)
$tinybrowser['link']['image'] = $tinybrowser['path']['image']; // Image links
$tinybrowser['link']['media'] = $tinybrowser['path']['media']; // Media links
$tinybrowser['link']['file']  = $tinybrowser['path']['file']; // Other file links

// File upload size limit (0 is unlimited)
$tinybrowser['maxsize']['image'] = $_TMCE_CONF['tb_maxsize_image']; // Image file maximum size
$tinybrowser['maxsize']['media'] = $_TMCE_CONF['tb_maxsize_media']; // Media file maximum size
$tinybrowser['maxsize']['file']  = $_TMCE_CONF['tb_maxsize_file']; // Other file maximum size

// Image automatic resize on upload (0 is no resize)
$tinybrowser['imageresize']['width']  = $_TMCE_CONF['tb_imageresize_width'];
$tinybrowser['imageresize']['height'] = $_TMCE_CONF['tb_imageresize_height'];

// Image thumbnail source (set to 'path' by default - shouldn't need changing)
$tinybrowser['thumbsrc'] = 'path'; // Possible values: path, link

// Image thumbnail size in pixels
$tinybrowser['thumbsize'] = $_TMCE_CONF['tb_thumbsize'];

// Image and thumbnail quality, higher is better (1 to 99)
$tinybrowser['imagequality'] = $_TMCE_CONF['tb_imagequality']; // only used when resizing or rotating
$tinybrowser['thumbquality'] = $_TMCE_CONF['tb_thumbquality'];

// Date format, as per php date function
$tinybrowser['dateformat'] = $_TMCE_CONF['tb_dateformat'];

// Permitted file extensions
$tinybrowser['filetype']['image'] = $_TMCE_CONF['tb_filetype_image']; // Image file types
$tinybrowser['filetype']['media'] = $_TMCE_CONF['tb_filetype_media']; // Media file types
$tinybrowser['filetype']['file']  = '*.*'; // Other file types

// Prohibited file extensions
$tinybrowser['prohibited'] = $_TMCE_CONF['tb_prohibited'];

// Default file sort
$tinybrowser['order']['by']   = 'name'; // Possible values: name, size, type, modified
$tinybrowser['order']['type'] = 'asc'; // Possible values: asc, desc

// Default image view method
$tinybrowser['view']['image'] = 'thumb'; // Possible values: thumb, detail

// File Pagination - split results into pages (0 is none)
$tinybrowser['pagination'] = $_TMCE_CONF['tb_pagination'];

// TinyMCE dialog.css file location, relative to tinybrowser.php (can be set to absolute link)
$tinybrowser['tinymcecss'] = '../../themes/advanced/skins/default/dialog.css';

// TinyBrowser pop-up window size
$tinybrowser['window']['width']  = $_TMCE_CONF['tb_window_width'];
$tinybrowser['window']['height'] = $_TMCE_CONF['tb_window_height'];

// Assign Permissions for Upload, Edit, Delete & Folders
$tinybrowser['allowupload']  = ($tb_perms['tb_allow_upload'] == 1);
$tinybrowser['allowedit']    = ($tb_perms['tb_allow_edit'] == 1);
$tinybrowser['allowdelete']  = ($tb_perms['tb_allow_delete'] == 1);
$tinybrowser['allowfolders'] = ($tb_perms['tb_allow_folders'] == 1);

// Clean filenames on upload
$tinybrowser['cleanfilename'] = $_TMCE_CONF['tb_cleanfilename'];

// Set default action for edit page
$tinybrowser['defaultaction'] = 'delete'; // Possible values: delete, rename, move

// Set delay for file process script, only required if server response is slow
$tinybrowser['delayprocess'] = 0; // Value in seconds
