<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | assist			                                                           |
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



$_CONF_VALIDATE['assist']['title_trim_length'] = array('rule' => 'numeric');

$_CONF_VALIDATE['assist']['intervalday'] = array('rule' => 'numeric');

$_CONF_VALIDATE['assist']['limitcnt'] = array('rule' => 'numeric');

$_CONF_VALIDATE['assist']['newmarkday'] = array('rule' => 'numeric');

//$_ASSIST_DEFAULT['topics']
//$_ASSIST_DEFAULT['new_img']
//$_ASSIST_DEFAULT['rss_img']
//$_ASSIST_DEFAULT['newsletter_tid']

$_CONF_VALIDATE['assist']['templates'] = array(
	'rule' => array('inList', array('standard','custom', 'theme'), true)
);
$_CONF_VALIDATE['assist']['templates_admin'] = array(
	'rule' => array('inList', array('standard','custom', 'theme'), true)
);

//$_ASSIST_DEFAULT['themespath'] ="assist/templates/";

$_CONF_VALIDATE['assist']['cron_schedule_interval'] = array('rule' => 'numeric');

$_CONF_VALIDATE['assist']['onoff_emailfromadmin'] = array(
    'rule' => array('inList', array('0', '1'), true)
);


$_CONF_VALIDATE['assist']['aftersave'] = array(
	'rule' => array('inList', array('no','item', 'list', 'home', 'admin', 'plugin'), true)
);
$_CONF_VALIDATE['assist']['aftersave_admin'] = array(
	'rule' => array('inList', array('no','item', 'list', 'home', 'admin', 'plugin'), true)
);

$_CONF_VALIDATE['assist']['path_xml'] = array('rule' => 'notEmpty');
$_CONF_VALIDATE['assist']['path_xml_out'] = array('rule' => 'notEmpty');



// = array('rule' => 'boolean');

?>
