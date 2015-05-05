<?php

// +---------------------------------------------------------------------------+
// | TinyMCE Plugin for Geeklog - The Ultimate Weblog                          |
// +---------------------------------------------------------------------------+
// | geeklog/plugins/tinymce/sql/mysql_install.php                             |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010-2011 mystral-kk - geeklog AT mystral-kk DOT net        |
// |                                                                           |
// | Constructed with the Universal Plugin                                     |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is licensed under the terms of the GNU General Public License|
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                      |
// | See the GNU General Public License for more details.                      |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

if (strpos(strtolower($_SERVER['PHP_SELF']), strtolower(basename(__FILE__))) !== FALSE) {
    die('This file can not be used on its own!');
}

// Table definitions
$_SQL[] = "
CREATE TABLE {$_TABLES['tinymce_configs']} (
	cid MEDIUMINT(8) NOT NULL AUTO_INCREMENT,
	title VARCHAR(100) NOT NULL DEFAULT '',
	theme VARCHAR(30) NOT NULL DEFAULT 'advanced',
	buttons TEXT,
	plugins TEXT,
	tb_allow_upload TINYINT NOT NULL DEFAULT '1',
	tb_allow_edit TINYINT NOT NULL DEFAULT '0',
	tb_allow_delete TINYINT NOT NULL DEFAULT '0',
	tb_allow_folders TINYINT NOT NULL DEFAULT '1',
	enter_function TINYINT NOT NULL DEFAULT '0',
	group_id MEDIUMINT(8) NOT NULL DEFAULT '2',
	PRIMARY KEY cid(cid)
) ENGINE=MyISAM
";

// Default data for "All users"
$tmce_title   = 'default';
$tmce_theme   = 'advanced';
$tmce_buttons = serialize(
	array(
		'buttons1'	=> 'bold,italic,underline,strikethrough,|,styleselect,formatselect,fontselect,fontsizeselect',
		'buttons2'	=> 'cut,copy,paste,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor',
		'buttons3'	=> 'tablecontrols,|,hr,visualaid,|,emotions,media,|,emojiau,emojidocomo,emojisoftbank',
		'buttons4'	=> 'styleprops,|,visualchars,restoredraft',
	)
);
$tmce_plugins = "style,table,advimage,advlink,emotions,iespell,inlinepopups,media,searchreplace,contextmenu,paste,visualchars,xhtmlxtras,advlist,autosave,autoresize,tinybrowser,emojiau,emojidocomo,emojisoftbank";

$_SQL[] = "
INSERT INTO {$_TABLES['tinymce_configs']} 
VALUES(NULL, '{$tmce_title}', '{$tmce_theme}', '{$tmce_buttons}', '{$tmce_plugins}', 1, 0, 0, 1, 0, 2)
";

// Default data for "Root" users
$tmce_title   = 'admin';
$tmce_theme   = 'advanced';
$tmce_buttons = serialize(
	array(
		'buttons1'	=> 'cut,copy,paste,pastetext,pasteword,undo,redo,|,bold,italic,underline,strikethrough,|,search,replace,|,justifyleft,justifycenter,justifyright,justifyfull,|,visualchars',
		'buttons2'	=> 'bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,image,media,|,forecolor,backcolor,|,pagebreak,hr,charmap,|,emojiau,emojidocomo,emojisoftbank',
		'buttons3'	=> 'tablecontrols,|,emotions,iespell,|,code,cleanup,template,visualaid',
		'buttons4'	=> 'styleprops,removeformat,|,restoredraft,|,styleselect,formatselect,fontselect,fontsizeselect',
	)
);
$tmce_plugins = "pagebreak,style,table,save,advimage,advlink,emotions,iespell,inlinepopups,media,searchreplace,print,contextmenu,paste,visualchars,nonbreaking,xhtmlxtras,template,advlist,autosave,autoresize,tinybrowser,emojiau,emojidocomo,emojisoftbank";

$_SQL[] = "
INSERT INTO {$_TABLES['tinymce_configs']} 
VALUES(NULL, '{$tmce_title}', '{$tmce_theme}', '{$tmce_buttons}', '{$tmce_plugins}', 1, 1, 1, 1, 0, 1)
";
