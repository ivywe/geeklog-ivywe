<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.1                                                        |
// +---------------------------------------------------------------------------+
// | Installation SQL                                                          |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2014 by the following authors:                              |
// |                                                                           |
// | Authors: Ben               - ben AT geeklog DOT fr                        |
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
//


//database queries

$_SQL[] = "
CREATE TABLE " . $_TABLES['monitor_ban'] . " ("
    . " bantype varchar(40) NOT NULL default '',"
    . " data varchar(255) NOT NULL default '',"
    . " created datetime NOT NULL,"
    . " access int(3) NOT NULL default 0"
    . ") ENGINE=MyISAM
";

?>
