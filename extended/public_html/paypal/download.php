<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin - geeklog CMS                                             |
// +--------------------------------------------------------------------------+
// | download.php                                                             |
// |                                                                          |
// | Download page for files purchased using the paypal plugin.               |
// | No other files will be accessable via this script.                       |
// +--------------------------------------------------------------------------+
// |                                                                          |
// | Copyright (C) 2005-2006 by the following authors:                        |
// |                                                                          |
// | Authors: Vincent Furia     - vinny01 AT users DOT sourceforge DOT net    |
// +--------------------------------------------------------------------------+
// |                                                                          |
// | This program is free software; you can redistribute it and/or            |
// | modify it under the terms of the GNU General Public License              |
// | as published by the Free Software Foundation; either version 2           |
// | of the License, or (at your option) any later version.                   |
// |                                                                          |
// | This program is distributed in the hope that it will be useful,          |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
// | GNU General Public License for more details.                             |
// |                                                                          |
// | You should have received a copy of the GNU General Public License        |
// | along with this program; if not, write to the Free Software Foundation,  |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.          |
// |                                                                          |
// +--------------------------------------------------------------------------+

/**
 * Download page for files purchased using the paypal plugin.  No other files will be
 * accessable via this script.
 *
 * @author Vincent Furia <vinny01 AT users DOT sourceforge DOT net>
 * @copyright Vincent Furia 2005 - 2006
 * @package paypal
 */

/**
 * Requires geeklog
 */
require_once('../lib-common.php');

/**
 * Require Downloader Class
 */
require_once($_CONF['path'] . 'system/classes/downloader.class.php');

// Incoming variable filter
$vars = array('id' => 'number');
paypal_filterVars($vars, $_REQUEST);

// This sql does double duty of getting the file name to download and making sure that the
// user has 'permission' to get it
$sql = "SELECT d.id, d.file, d.product_type, d.active FROM "
     . "{$_TABLES['paypal_products']} as d LEFT JOIN {$_TABLES['paypal_purchases']} as p "
     . "ON d.id = p.product_id WHERE d.id = {$_REQUEST['id']} AND "
     . "((p.user_id = {$_USER['uid']} AND (p.expiration > NOW() OR p.expiration IS NULL)) "
     . "OR (d.price <= 0)) LIMIT 1";
$res = DB_query($sql);
$A = DB_fetchArray($res);

// If a file was found, do the download.  Otherwise refresh to the home page and log it.
if (!empty($A['file']) && $A['product_type'] == '1' && $A['active'] == '1') {
    $dwnld = new downloader();
    $dwnld->setLogFile($_CONF['path_log'] . 'paypal_downloads.log');
    $dwnld->setLogging(true);
    $dwnld->setAllowedExtensions($_PAY_CONF['allowedextensions']);
    $dwnld->setPath($_PAY_CONF['download_path']);
    $dwnld->downloadFile($A['file']);

    // Check for errors
    if ($dwnld->areErrors()) {
        $errs = $dwnld->printErrors(false);
        COM_errorLog("PAYPAL-DWNLD: {$_USER['username']} tried to download the file with id "
                   . "{$_REQUEST['id']} but for some reason could not",1);
        COM_errorLog("PAYPAL-DWNLD: $errs",1);
        echo COM_refresh($_CONF['site_url']);
    }

    $dwnld->_logItem('Download Success', "{$_USER['username']} successfully downloaded "
                                       . "the file with id {$_REQUEST['id']}.");
	$sql = "INSERT INTO {$_TABLES['paypal_downloads']} SET product_id = {$A['id']}, "
                     . "file = '{$A['file']}', user_id = {$_USER['uid']}, "
                     . "dl_date = NOW()";
    DB_query($sql);
} else {
    COM_errorLog("PAYPAL-DWNLD: {$_USER['username']}/{$_USER['uid']} tried to download the file "
               . "with id {$_REQUEST['id']} but for some reason could not",1);
    echo COM_refresh($_CONF['site_url']);
}

?>