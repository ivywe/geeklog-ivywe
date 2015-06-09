<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog 1.5                                                               |
// +---------------------------------------------------------------------------+
// | portal.php                                                                |
// |                                                                           |
// | Geeklog portal page that tracks banner click throughs.                    |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000-2009 by the following authors:                         |
// |                                                                           |
// | Authors: Tony Bibbs        - tony AT tonybibbs DOT com                    |
// |          Mark Limburg      - mlimburg AT users.sourceforge DOT net        |
// |          Jason Whittenburg - jwhitten AT securitygeeks DOT com            |
// |          Dirk Haun         - dirk AT haun-online DOT de                   |
// |          Hiroron           - hiroron AT hiroron DOT com                   |
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

require_once '../lib-common.php';

if (!in_array('banner', $_PLUGINS)) {
    echo COM_refresh($_CONF['site_url'] . '/index.php');
    exit;
}


// MAIN

$url = '';

COM_setArgNames(array('what', 'item'));
$what = COM_getArgument('what');

if ($what == 'banner') {

    $item = COM_applyFilter(COM_getArgument('item'));
    if (!empty($item)) {
        // Hack: due to PLG_afterSaveSwitch settings, we may get
        // an attached &msg - strip it off
        $i = explode('&', $item);
        $item = $i[0];
    }

    if (!empty($item)) {
        $url = DB_getItem($_TABLES['banner'], 'url', "bid = '{$item}' AND (publishstart IS NULL OR publishstart < NOW()) and (publishend IS NULL OR publishend > NOW())");
        if (!empty($url)) {
            DB_change($_TABLES['banner'], 'hits','hits + 1', 'bid',$item, '', true);
        }
    }

}

if (empty($url)) {
    $url = $_CONF['site_url'];
}
header('HTTP/1.1 301 Moved');
header('Location: ' . $url);
header('Connection: close');

?>
