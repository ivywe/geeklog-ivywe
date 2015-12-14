<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.2                                                           |
// +---------------------------------------------------------------------------+
// | utf8.php                                                                  |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben                                                            |
// +---------------------------------------------------------------------------+
// | Created with the Geeklog Plugin Toolkit.                                  |
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

/**
 * Displays a form for the editing maps
 *
 * Allows for the altering and deletion of existing maps as well as the
 * creation of new maps
 *
 */

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';
require_once 'edit_functions.php';

$display = '';

// Ensure user even has the rights to access this page
if (! SEC_hasRights('maps.admin')) {
    $display .= COM_siteHeader('menu', $MESSAGE[30])
             . COM_showMessageText($MESSAGE[29], $MESSAGE[30])
             . COM_siteFooter();

    // Log attempt to access.log
    COM_accessLog("User {$_USER['username']} tried to illegally access the Maps plugin import screen.");

    echo $display;
    exit;
}

// Incoming variable filter
$vars = array('col' => 'text',
			  );

MAPS_filterVars($vars, $_REQUEST);


// MAIN


switch ($_REQUEST['mode']) {
    case 'convert':
	    //Part of code from http://php.vrana.cz/ - Author - Jakub Vrana
	    print COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
        print  MAPS_admin_menu();
	    $collation = $_REQUEST['col'];
	    DB_query("ALTER DATABASE $_DB_name COLLATE $collation");
		print  "<h1>ALTER DATABASE $_DB_name COLLATE $collation</h1>";
		$result = mysql_query("SHOW TABLES");
		while ($row = mysql_fetch_row($result)) {
			DB_query("ALTER TABLE $row[0] COLLATE $collation");
			print  "<h2>ALTER TABLE $row[0] COLLATE $collation</h2><ul>";
			$result1 = mysql_query("SHOW COLUMNS FROM $row[0]");
			while ($row1 = mysql_fetch_assoc($result1)) {
				if (preg_match('~char|text|enum|set~', $row1["Type"])) {
					DB_query("ALTER TABLE $row[0] MODIFY $row1[Field] $row1[Type] CHARACTER SET binary",1);
					DB_query("ALTER TABLE $row[0] MODIFY $row1[Field] $row1[Type] COLLATE $collation" . ($row1["Null"] ? "" : " NOT NULL") . ($row1["Default"] && $row1["Default"] != "NULL" ? " DEFAULT '$row1[Default]'" : ""),1);
					print  "<li>ALTER TABLE $row[0] MODIFY $row1[Field] $row1[Type] COLLATE $collation</li>";
				}
			}
			print '</ul>';
		}
	    print  COM_siteFooter(0);
        break;

    default:
	    $display .= COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
        $display .= MAPS_admin_menu();
        $display .= "<h1>Change database collation</h1><h2>DATABASE: $_DB_name, ALL TABLES, ALL COLUMNS</h2>";
        $display .= '<form action="' . $_CONF['site_admin_url'] . '/plugins/maps/utf8.php?mode=convert" method="post">';
        $display .= '<p>Set new database Collation to <input type="text" name="col" value="utf8_general_ci" /> <input type="submit" value="Submit" /></p>';
        $display .= '</form>';
		$display .= COM_siteFooter(0);
        COM_output($display);
        break;
}



?>