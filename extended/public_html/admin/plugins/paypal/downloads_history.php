<?php
// +---------------------------------------------------------------------------+
// | PayPal Plugin - geeklog CMS                                               |
// +---------------------------------------------------------------------------+
// | downloads_history.php                                                     |
// |                                                                           |
// | Allows paypal administrators to view site-wide downloads history          |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors: Ben        - cordiste AT free DOT fr                             |
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
//

/**
 * Required geeklog
 */
require_once('../../../lib-common.php');

// Check for required permissions
paypal_access_check('paypal.admin');

$vars = array('msg', 'text');
paypal_filterVars($vars, $_REQUEST);

function PAYPAL_listDownloads()
{
    global $_CONF, $_TABLES, $_IMAGE_TYPE, $LANG_ADMIN, $LANG_PAYPAL_1;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';
	
	if (DB_count($_TABLES['paypal_downloads']) == 0){
	    $retval .= '<p>' . $LANG_PAYPAL_1['downloads_history_empty'] . '</p>';
	}

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_PAYPAL_1['ID'], 'field' => 'id', 'sort' => true),
        array('text' => $LANG_PAYPAL_1['product_id'], 'field' => 'product_id', 'sort' => true),
        array('text' => $LANG_PAYPAL_1['filename_label'], 'field' => 'file', 'sort' => true),
        array('text' => $LANG_PAYPAL_1['date_time'], 'field' => 'dl_date', 'sort' => true),
        array('text' => $LANG_PAYPAL_1['user_id'], 'field' => 'user_id', 'sort' => true)
    );
    $defsort_arr = array('field' => 'id', 'direction' => 'desc');

    $text_arr = array(
        'has_extras' => true,
        'form_url' => $_CONF['site_admin_url'] . '/plugins/paypal/downloads_history.php'
    );
	
	$sql = "SELECT
	            *
            FROM {$_TABLES['paypal_downloads']}
			WHERE 1=1";

    $query_arr = array(
        'table'          => 'paypal_downloads',
        'sql'            => $sql,
        'query_fields'   => array('id', 'product_id', 'file', 'dl_date', 'user_id'),
        'default_filter' => COM_getPermSQL ('AND', 0, 3)
    );

    $retval .= ADMIN_list('paypal', 'plugin_getListField_paypal_downloads',
                          $header_arr, $text_arr, $query_arr, $defsort_arr);

    return $retval;
}

/**
*   Get an individual field for the paypal screen.
*
*   @param  string  $fieldname  Name of field (from the array, not the db)
*   @param  mixed   $fieldvalue Value of the field
*   @param  array   $A          Array of all fields from the database
*   @param  array   $icon_arr   System icon array
*   @param  object  $EntryList  This entry list object
*   @return string              HTML for field display in the table
*/
function plugin_getListField_paypal_downloads($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $LANG_ADMIN, $LANG_STATIC, $_TABLES, $_PAY_CONF;

    switch($fieldname) {
        case "id":
            $retval = $A['id'];
            break;
		case "product_id":
            $retval = $A['product_id'];
            break;
		case "file":
            $retval = $A['file'];
            break;
		case "dl_date":
            $retval = $A['dl_date'];
            break;
		case "user_id":
            $retval = $A['user_id'];
            break;

        default:
            $retval = stripslashes($fieldvalue);
            break;
    }
    return $retval;
}

//Main

$display = COM_siteHeader('none');
$display .= paypal_admin_menu();

$display .= COM_startBlock($LANG_PAYPAL_1['downloads_history']);

if (!empty($_REQUEST['msg'])) {
    $display .= COM_startBlock($LANG_PAYPAL_1['message']);
    $display .= stripslashes($_REQUEST['msg']);
    $display .= COM_endBlock();
}

$display .= PAYPAL_listDownloads();
$display .= COM_endBlock();

$display .= COM_siteFooter();

COM_output($display);

?>