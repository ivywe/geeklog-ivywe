<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog 2.1                                                               |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// |                                                                           |
// | Geeklog installation script.                                              |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2011 by the following authors:                         |
// |                                                                           |
// | Authors: Matt West         - matt AT mattdanger DOT net                   |
// |          Dirk Haun         - dirk AT haun-online DOT de                   |
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
// | You don't need to change anything in this file.  Please read              |
// | docs/english/install.html which describes how to install Geeklog.         |
// +---------------------------------------------------------------------------+

define('PATH_INSTALL', __DIR__ . '/');
define('PATH_LAYOUT', PATH_INSTALL . 'layout');
define('BASE_FILE', str_replace('\\', '/', __FILE__));

require_once __DIR__ . '/classes/micro_template.class.php';
require_once __DIR__ . '/classes/installer.class.php';

$installer = new Installer();
$installer->run();

/**
* Fix site_url in content
*
* If the site's URL changed due to the migration, this function will replace
* the old URL with the new one in text content of the given tables.
*
* @param    string  $old_url    the site's previous URL
* @param    string  $new_url    the site's new URL after the migration
* @param    array   $tablespec  (optional) list of tables to patch
*
* The $tablespec is an array of tablename => fieldlist pairs, where the field
* list contains the text fields to be searched and the table's index field
* as the first(!) entry.
*
* NOTE: This function may be used by plugins during PLG_migrate. Changes should
*       ensure backward compatibility.
*
*/
function INST_updateSiteUrl($old_url, $new_url, $tablespec = '')
{
    global $_TABLES;

    // standard tables to update if no $tablespec given
    $tables = array(
        'stories'           => 'sid, introtext, bodytext, related',
        'storysubmission'   => 'sid, introtext, bodytext',
        'comments'          => 'cid, comment',
        'trackback'         => 'cid, excerpt, url',
        'blocks'            => 'bid, content'
    );

    if (empty($tablespec) || (! is_array($tablespec))) {
        $tablespec = $tables;
    }

    if (empty($old_url) || empty($new_url)) {
        return;
    }

    if ($old_url == $new_url) {
        return;
    }

    foreach ($tablespec as $table => $fieldlist) {
        $fields = explode(',', str_replace(' ', '', $fieldlist));
        $index = array_shift($fields);

        if (empty($_TABLES[$table]) || !DB_checkTableExists($table)) {
            COM_errorLog("Table $table does not exist - skipping migration");
            continue;
        }

        $result = DB_query("SELECT $fieldlist FROM {$_TABLES[$table]}");
        $numRows = DB_numRows($result);
        for ($i = 0; $i < $numRows; $i++) {
            $A = DB_fetchArray($result);
            $changed = false;
            foreach ($fields as $field) {
                $newtxt = str_replace($old_url, $new_url, $A[$field]);
                if ($newtxt != $A[$field]) {
                    $A[$field] = $newtxt;
                    $changed = true;
                }
            }

            if ($changed) {
                $sql = "UPDATE {$_TABLES[$table]} SET ";
                foreach ($fields as $field) {
                    $sql .= "$field = '" . DB_escapeString($A[$field]) . "', ";
                }
                $sql = substr($sql, 0, -2);

                DB_query($sql . " WHERE $index = '" . DB_escapeString($A[$index]) . "'");
            }
        }
    }
}


