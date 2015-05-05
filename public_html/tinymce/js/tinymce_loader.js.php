<?php

// +---------------------------------------------------------------------------+
// | TinyMCE Plugin for Geeklog - The Ultimate Weblog                          |
// +---------------------------------------------------------------------------+
// | public_html/tinymce/js/tiny_mce/tinymce_loader.php                        |
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

require_once '../../lib-common.php';

// Collects group ids of the current user
if (count($_GROUPS) === 0) {	// Quite unlikely but ...
	exit;
}

/**
* Returns those plugins which the current user is allowed to use
*
* @param   string  $plugins  plugin names separated by a comma
* @return  array($useTinyBrowser: boolean, $plugins: string)
*/
function TMCE_checkPlugins($plugins) {
	global $_TMCE_CORE_PLUGINS;
	
	$retval = array();
	
	$useTinyBrowser = FALSE;
	$allowedPlugins = TMCE_getPlugins();
	
	foreach(explode(',', $plugins) as $plugin) {
		$plugin = trim($plugin);
		
		if ($plugin === 'tinybrowser') {
			$useTinyBrowser = TRUE;
		} else if (in_array($plugin, $allowedPlugins)) {
			$retval[] = $plugin;
		}
	}
	
	// Appends core plugins
	$retval = array_merge_recursive($retval, $_TMCE_CORE_PLUGINS);
	$retval = array_unique($retval);
	
	return array($useTinyBrowser, implode(',', $retval));
}

/**
* Checks if buttons are valid
*
* @param   string  $buttons  button names separated by a comma
* @return  string            button names separated by a comma
*/
function TMCE_checkButtons($buttons) {
	static $allowedButtons = NULL;
	
	if ($allowedButtons === NULL) {
		$allowedButtons = TMCE_getButtons();
	}

	$retval = array();
	
	foreach (explode(',', $buttons) as $button) {
		if (($button === '|') OR in_array($button, $allowedButtons)) {
			$retval[] = $button;
		}
	}
	
	return implode(',', $retval);
}

//===================================================================
// Main
//===================================================================

// Gets configuration from db
$A = TMCE_getConfig();

$theme = $A['theme'];
$toolbars = unserialize($A['buttons']);
list($useTinyBrowser, $plugins) = TMCE_checkPlugins($A['plugins']);
$enter_function = (int) $A['enter_function'];

// Decides locale
list($language, $directionality) = TMCE_getLocale();

// Decides mode
switch ($_TMCE_CONF['targets']) {
	case 'all':
		$mode = 'textareas';
		break;
	
	case 'css_class':
		$mode = 'specific_textareas';
		break;
	
	case 'css_id':
		$mode = 'none';
		break;
	
	case 'auto':
	default:
		$mode = 'specific_textareas';
		break;
}

if (($mode === 'specific_textareas') AND ($_TMCE_CONF['target_class'] == '')) {
	$_TMCE_CONF['target_class'] = 'tinymce_enabled';
}

$target_ids = '"' . implode('", "', $_TMCE_CONF['target_ids']) . '"';

// Loads style format file if one exists
$style_formats = FALSE;
$style_path = $_CONF['path_html'] . 'tinymce/js/style_formats.js';
clearstatcache();

if (is_readable($style_path)) {
	$style_formats = file_get_contents($style_path);
}

if ($style_formats === FALSE) {
	$style_formats = $_TMCE_CONF['default_styles'];
}

$element_format = (defined('XHTML') AND (XHTML === ' /')) ? 'xhtml' : 'html';

// Builds a template list
$templates = TMCE_getTemplateList();

if (count($templates) > 0) {
	$temp = array();
	
	foreach ($templates as $template) {
		$temp[] = '{title: "' . TMCE_esc($template['title']) . '", src: "'
				. $template['src'] . '", description: "'
				. TMCE_esc($template['description']) . '"}';
	}
	
	$templates = implode(', ', $temp);
} else {
	$templates = '';
}

// Starts storing JavaScript code as a string
$js = <<<EOD
// +---------------------------------------------------------------------------+
// | TinyMCE Plugin for Geeklog - The Ultimate Weblog                          |
// +---------------------------------------------------------------------------+
// | public_html/tinymce/js/tinymce_loader.js.php                              |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 mystral-kk - geeklog AT mystral-kk DOT net             |
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

var gl_tinymce = {};

// Properties
gl_tinymce.baseUrl = "{$_CONF['site_url']}";

gl_tinymce.mode = "{$_TMCE_CONF['targets']}";

gl_tinymce.targetClassName = "{$_TMCE_CONF['target_class']}";

gl_tinymce.target_ids = [{$target_ids}];

{$style_formats}

// Methods
/**
* Returns if the target HTML element has a given class
*/
gl_tinymce.hasClass = function(target, className) {
	var pattern = new RegExp("(^| )" + className + "( |$)");
	
	return pattern.test(target.className);
};

/**
* Adds a given class to the target HTML element
*/
gl_tinymce.addClass = function(targetElement, className) {
	if (!this.hasClass(targetElement, className)) {
		if (targetElement.className == "") {
			targetElement.className = className;
		} else {
			targetElement.className += " " + className;
		}
	}
};

/**
* Returns an HTML element with a given name
*/
gl_tinymce.getElementByName = function(tagType, name) {
	var elms,
		elm,
		i;
	
	if (document.getElementsByTagName) {
		elms = document.getElementsByTagName(tagType.toUpperCase());
	} else {
		elms = document.all;
	}
	
	for (i = 0; i < elms.length; i ++) {
		elm = elms[i];
		
		if (elm.name === name) {
			return elm;
		}
	}
	
	return null;
};

/**
* Attaches an event listener to an object
*/
gl_tinymce.addEventListener = function(target, type, listener) {
	if (target.addEventListener) {
		target.addEventListener(type, listener, false);
	} else if (target.attachEvent) {
		target.attachEvent("on" + type, function() { listener.call(target, window.event); });
	}
};

/**
* Lets TinyMCE show all its instances
*/
gl_tinymce.showEditors = function() {
	var that = gl_tinymce;
	
	if (tinymce.editors.length === 0) {
		// Still no instance of TinyMCE 
		that.loadTinyMCE();
	} else {
		// Already has TinyMCE instance(s)
		for (i = 0; i< tinymce.editors.length; i ++) {
			tinymce.editors[i].show();
		}
	}
};

/**
* Lets TinyMCE hide all its instances
*/
gl_tinymce.hideEditors = function() {
	for (i = 0; i< tinymce.editors.length; i ++) {
		tinymce.editors[i].hide();
	}
};



/**
* Handler for the event in which "postmode" dropdown list in story/comment/calendar
* editor has been changed.
*/
gl_tinymce.onEditorChangeCommon = function(e) {
	var that = gl_tinymce,
		value;
	
	if (e.target) {
		value = e.target.value;
	} else {
		value = e.srcElement.value;
	}
	
	if (value === "html") {
		that.showEditors();
	} else {
		that.hideEditors();
	}
};

/**
* Handler for the event in which "PHP" dropdown list in static page editor has
* been changed.
*/
gl_tinymce.onBlockEditorChange = function(e) {
	var that = gl_tinymce,
		value;
	
	if (e.target) {
		value = e.target.value;
	} else {
		value = e.srcElement.value;
	}
	
	if (value === "normal") {
		that.showEditors();
	} else {
		that.hideEditors();
	}
};

/**
* Attaches an event listener when block editor is loaded
*/
gl_tinymce.attachListenerToBlockEditor = function() {
	var selector,
		target;
	
	selector = this.getElementByName("SELECT", "type");
	target = this.getElementByName("TEXTAREA", "content");
	
	if (selector && target) {
		this.addClass(target, this.targetClassName);
		this.addEventListener(selector, "change", this.onBlockEditorChange);
		if (selector.value === "normal") {
			this.loadTinyMCE();
		}
	}
};

/**
* Handler for the event in which "html" checkbox in mail editor has been
* changed.
*/
gl_tinymce.onMailEditorChange = function(e) {
	var that = gl_tinymce,
		value;
	
	if (e.target) {
		value = e.target.checked;
	} else {
		value = e.srcElement.checked;
	}
	
	if (value === true) {
		that.showEditors();
	} else {
		that.hideEditors();
	}
};

/**
* Attaches an event listener when mail editor is loaded
*/
gl_tinymce.attachListenerToMailEditor = function() {
	var selector,
		area,
		tx;
	
	selector = this.getElementByName("input", "html");
	area = this.getElementByName("TEXTAREA", "message");
	
	if (selector && area) {
		this.addClass(area, this.targetClassName);
		this.addEventListener(selector, "change", this.onMailEditorChange);
		if (document.all) {
			// Workaround for IE, since it doesn't fire "onchange" event as
			// soon as the state of a checkbox is changed
			this.addEventListener(selector, "click", function(e) {e.srcElement.blur();e.srcElement.focus();});
		}
		
		if (selector.check === true) {
			this.loadTinyMCE();
		}
	}
};

/**
* Attaches an event listener when calendar editor is loaded
*/
gl_tinymce.attachListenerToCalendarEditor = function() {
	var selector,
		area,
		tx;
	
	selector = this.getElementByName("SELECT", "postmode");
	area = this.getElementByName("TEXTAREA", "description");
	
	if (selector && area) {
		this.addClass(area, this.targetClassName);
		this.addEventListener(selector, "change", this.onEditorChangeCommon);
		
		if (selector.value === "html") {
			this.loadTinyMCE();
		}
	}
};

/**
* Attaches an event listener when comment editor is loaded
*/
gl_tinymce.attachListenerToCommentEditor = function() {
	var selector,
		area,
		tx;
	
	selector = this.getElementByName("SELECT", "postmode");
	area = this.getElementByName("TEXTAREA", "comment");
	
	if (selector && area) {
		this.addClass(area, this.targetClassName);
		this.addEventListener(selector, "change", this.onEditorChangeCommon);
		
		if (selector.value === "html") {
			this.loadTinyMCE();
		}
	}
};

/**
* Attaches an event listener when story editor is loaded
*/
gl_tinymce.attachListenerToStoryEditor = function() {
	var selector,
		area1,
		area2;
	
	selector = this.getElementByName("SELECT", "postmode");
	area1 = this.getElementByName("TEXTAREA", "introtext");
	area2 = this.getElementByName("TEXTAREA", "bodytext");
	
	if (selector && area1 && area2) {
		this.addClass(area1, this.targetClassName);
		this.addClass(area2, this.targetClassName);
		this.addEventListener(selector, "change", this.onEditorChangeCommon);
		
		if (selector.value === "html") {
			this.loadTinyMCE();
		}
	}
};

/**
* Handler for the event in which "PHP" dropdown list in static page editor has
* been changed.
*/
gl_tinymce.onSPEditorChange = function(e) {
	var that = gl_tinymce,
		value;
	
	if (e.target) {
		value = e.target.value;
	} else {
		value = e.srcElement.value;
	}
	
	if (parseInt(value) === 0) {
		// PHP will not be executed
		that.showEditors();
	} else {
		// PHP will be executed
		that.hideEditors();
	}
};

/**
* Attaches an event listener when static page editor is loaded
*/
gl_tinymce.attachListenerToSP = function() {
	var selector,
		target;
	
	selector = this.getElementByName("SELECT", "sp_php");
	target = this.getElementByName("TEXTAREA", "sp_content");
	
	if (target) {
		this.addClass(target, this.targetClassName);
		
		if (selector) {
			this.addEventListener(selector, "change", this.onSPEditorChange);
			
			if (parseInt(selector.value) === 0) {
				this.loadTinyMCE();
			}
		} else {
			this.loadTinyMCE();
		}
	}
};

/**
*  Returns default configuration
*/
gl_tinymce.getDefaultConfig = function() {
	var d = {};
	
	// General options
	d.mode = "{$mode}";
	
	if (d.mode === "specific_textareas") {
		d.editor_selector = "{$_TMCE_CONF['target_class']}";
	}

	d.document_base_url = this.baseUrl + "/";
	d.relative_urls =  false;
	d.element_format = "{$element_format}";
	
	if ({$enter_function} === 1) {
		d.force_br_newlines = true;
		d.forced_root_block = "";
	} else {
		d.force_p_newlines = true;
	}
	
	d.theme ="{$theme}";
	d.plugins = "{$plugins}";

	// Theme options

EOD;

// Adds theme configs
foreach ($toolbars as $name => $buttons) {
	$buttons = TMCE_checkButtons($buttons);
	$js .= <<<EOD
	d.theme_{$theme}_{$name} = "{$buttons}";

EOD;
}

$js .= <<<EOD
	d.theme_{$theme}_toolbar_location = "top";
	d.theme_{$theme}_toolbar_align =  "left";
	d.theme_{$theme}_statusbar_location = "bottom";
	d.theme_{$theme}_resizing = true;

EOD;

if ($useTinyBrowser === TRUE) {
	$js .= <<<EOD
	d.file_browser_callback = "tinyBrowser";

EOD;
}

// Adds the height and width of the editor window
$height = preg_replace("/[^0-9.%]/", '', $_TMCE_CONF['height']);
if ($height != '') {
	$js .= <<<EOD
	d.height = "{$height}";

EOD;
}

$width = preg_replace("/[^0-9.%]/", '', $_TMCE_CONF['width']);
if ($width != '') {
	$js .= <<<EOD
	d.width = "{$width}";


EOD;
}

$js .= <<<EOD
	// Locale
	d.language = "{$language}";
	d.directionality = "{$directionality}";

	// Your site CSS
	d.content_css = this.baseUrl + "/tinymce/js/editor.css";

	// Drop lists for link/image/media/template dialogs
//	d.template_external_list_url = "lists/template_list.js";
	d.template_templates = [{$templates}];
//	d.external_link_list_url = "lists/link_list.js";
//	d.external_image_list_url = "lists/image_list.js";
//	d.media_external_list_url = "lists/media_list.js";

	// Style formats
	d.style_formats = this.style_formats;

	// Replace values for the template plugin
	// See http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/template
//	d.template_cdate_classes = "cdate creationdate";
//	d.template_mdate_classes = "mdate modifieddate";
//	d.template_selected_content_classes = "selcontent";
//	d.template_cdate_format = "%m/%d/%Y : %H:%M:%S";
//	d.template_mdate_format = "%m/%d/%Y : %H:%M:%S";
//	d.template_replace_values = {
//			username : "Some User",
//			staffid : "991234"
//	};
	
	return d;
};

/**
* Loads TinyMCE
*/
gl_tinymce.loadTinyMCE = function(configData) {
	configData = configData || this.getDefaultConfig();
	tinyMCE.init(configData);
}

/**
* Initializes TinyMCE
*/
gl_tinymce.init = function() {
	var that = gl_tinymce,
		loc = window.location.href,
		elemId,
		i,
		conf;
	
	if (loc.indexOf("admin/plugins/tinymce/index.php") >= 0) {
		conf = that.getDefaultConfig();
		conf.mode = "css_id";
		that.loadTinyMCE(conf);
		tinyMCE.execCommand("mceAddControl", true, "content");
	} else if (that.mode === "auto") {
		if (loc.indexOf("admin/story.php") >= 0) {
			that.attachListenerToStoryEditor();
		} else if (loc.indexOf("admin/plugins/staticpages") >= 0) {
			that.attachListenerToSP();
		} else if (loc.indexOf("comment.php") >= 0) {
			that.attachListenerToCommentEditor();
		} else if (loc.indexOf("admin/plugins/calendar") >= 0) {
			that.attachListenerToCalendarEditor();
		} else if (loc.indexOf("admin/mail.php") >= 0) {
			that.attachListenerToMailEditor();
		} else if (loc.indexOf("admin/block.php") >= 0) {
			that.attachListenerToBlockEditor();
		} else {
			that.loadTinyMCE();
		}
	} else if (that.mode === "css_id") {
		// Converts textareas with given CSS ids
		for (i = 0; i < that.target_ids.length; i ++) {
			elemId = that.target_ids[i];
			
			if (document.getElementById(elemId)) {
				that.loadTinyMCE();
				tinyMCE.execCommand("mceAddControl", true, elemId);
			}
		}
	} else {
		that.loadTinyMCE();
	}
};

// Sets window onload event handler
gl_tinymce.addEventListener(window, "load", gl_tinymce.init);

EOD;

// Sends a text as JavaScript code without allowing caching on the client side
header('Content-Type: text/javascript; charset=utf-8');
header('Cache-Control: private');
echo $js;
