<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | databox		                                                           |
// +---------------------------------------------------------------------------+
// | configuration_validation.php                                              |
// |                                                                           |
// | List of validation rules for the Links plugin configurations              |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// |                                                                           |
// | Authors: Akeda Bagus       - admin AT gedex DOT web DOT id                |
// |          Tom Homer         - tomhomer AT gmail DOT com                    |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'configuration_validation.php') !== false) {
    die('This file can not be used on its own!');
}

$_CONF_VALIDATE['databox']['perpage'] = array('rule' => 'numeric');

$_CONF_VALIDATE['databox']['loginrequired'] = array(
    'rule' => array('inList', array('0','1','2','3'), true)
);

$_CONF_VALIDATE['databox']['hidemenu'] = array(
    'rule' => array('inList', array('0', '1'), true)
);

$_CONF_VALIDATE['databox']['categorycode'] = array(
    'rule' => array('inList', array(FALSE, TRUE), true)
);

$_CONF_VALIDATE['databox']['datacode'] = array(
    'rule' => array('inList', array(FALSE, TRUE), true)
);

$_CONF_VALIDATE['databox']['groupcode'] = array(
    'rule' => array('inList', array(FALSE, TRUE), true)
);

//$_DATABOX_DEFAULT['top']="/databox/data.php";


$_CONF_VALIDATE['databox']['templates'] = array(
	'rule' => array('inList', array('standard','custom', 'theme'), true)
);
$_CONF_VALIDATE['databox']['templates_admin'] = array(
	'rule' => array('inList', array('standard','custom', 'theme'), true)
);




$_CONF_VALIDATE['databox']['themespath'] = array('rule' => 'notEmpty');

$_CONF_VALIDATE['databox']['delete_data'] = array(
    'rule' => array('inList', array(FALSE, TRUE), true)
);

$_CONF_VALIDATE['databox']['datefield'] = array(
	'rule' => array('inList', array('modified','created','released'), true)
);

$_CONF_VALIDATE['databox']['meta_tags'] = array(
    'rule' => array('inList', array('0', '1'), true)
);

$_DATABOX_DEFAULT['layout'] = 'standard';
$_DATABOX_DEFAULT['layout_admin'] = 'standard';



//$_DATABOX_DEFAULT['mail_to'] = array();

//日付書式　datepicker用
$_DATABOX_DEFAULT['dateformat'] = 'Y/m/d';


$_CONF_VALIDATE['databox']['aftersave'] = array(
	'rule' => array('inList', array('no','item', 'list', 'home', 'admin', 'plugin'), true)
);
$_CONF_VALIDATE['databox']['aftersave_admin'] = array(
	'rule' => array('inList', array('no','item', 'list', 'home', 'admin', 'plugin'), true)
);


//$_DATABOX_DEFAULT['grp_id_default'] = $grp_id;


$_CONF_VALIDATE['databox']['allow_data_update'] = array(
    'rule' => array('inList', array('0', '1'), true)
);

$_CONF_VALIDATE['databox']['allow_data_delete'] = array(
    'rule' => array('inList', array('0', '1'), true)
);

$_CONF_VALIDATE['databox']['allow_data_insert'] = array(
    'rule' => array('inList', array('0', '1'), true)
);


$_CONF_VALIDATE['databox']['admin_draft_default'] = array(
    'rule' => array('inList', array('0', '1'), true)
);

$_CONF_VALIDATE['databox']['user_draft_default'] = array(
    'rule' => array('inList', array('0', '1'), true)
);

$_CONF_VALIDATE['databox']['maxlength_description'] = array('rule' => 'numeric');
$_CONF_VALIDATE['databox']['maxlength_meta_description'] = array('rule' => 'numeric');
$_CONF_VALIDATE['databox']['maxlength_meta_keywords'] = array('rule' => 'numeric');


$_CONF_VALIDATE['databox']['whatsnew_interval'] = array('rule' => 'numeric');

$_CONF_VALIDATE['databox']['hide_whatsnew'] = array(
	'rule' => array('inList', array('hide','modified', 'created','released'), true)
);

$_CONF_VALIDATE['databox']['title_trim_length'] = array('rule' => 'numeric');

$_CONF_VALIDATE['databox']['include_search'] = array(
    'rule' => array('inList', array('0', '1'), true)
);

$_CONF_VALIDATE['databox']['additionsearch'] = array('rule' => 'numeric');


//$_DATABOX_DEFAULT['default_permissions'] = array(3, 2, 2, 2);

$_CONF_VALIDATE['databox']['intervalday'] = array('rule' => 'numeric');

$_CONF_VALIDATE['databox']['limitcnt'] = array('rule' => 'numeric');
$_CONF_VALIDATE['databox']['newmarkday'] = array('rule' => 'numeric');

//$_DATABOX_DEFAULT['categories']="";
//$_DATABOX_DEFAULT['new_img']=
//$_DATABOX_DEFAULT['rss_img']=


$_CONF_VALIDATE['databox']['imgfile_size'] = array('rule' => 'numeric');

//$_DATABOX_DEFAULT['imgfile_type'] = array('image/jpeg','image/gif');
//png bmp

$_CONF_VALIDATE['databox']['imgfile_size2'] = array('rule' => 'numeric');

//$_DATABOX_DEFAULT['imgfile_type2'] = array('image/jpeg','image/gif');

//$_DATABOX_DEFAULT['imgfile_frd'] = "images/databox/";
//$_DATABOX_DEFAULT['imgfile_thumb_frd'] = "images/databox/";

$_CONF_VALIDATE['databox']['imgfile_thumb_ok'] = array(
    'rule' => array('inList', array('0', '1'), true)
);

$_CONF_VALIDATE['databox']['imgfile_thumb_w'] = array('rule' => 'numeric');
$_CONF_VALIDATE['databox']['imgfile_thumb_h'] = array('rule' => 'numeric');

$_CONF_VALIDATE['databox']['imgfile_thumb_w2'] = array('rule' => 'numeric');
$_CONF_VALIDATE['databox']['imgfile_thumb_h2'] = array('rule' => 'numeric');

$_CONF_VALIDATE['databox']['imgfile_smallw'] = array('rule' => 'numeric');

//$_DATABOX_DEFAULT['file_path'] = $_CONF['path_data']."databox_data/";
//$_DATABOX_DEFAULT['file_size'] = "";
//$_DATABOX_DEFAULT['file_type'] = array();


$_CONF_VALIDATE['databox']['path_xml'] = array('rule' => 'notEmpty');
$_CONF_VALIDATE['databox']['path_xml_out'] = array('rule' => 'notEmpty');


// = array('rule' => 'boolean');

?>
