<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin - geeklog CMS                                             |
// +--------------------------------------------------------------------------+
// | index.php                                                                |
// |                                                                          |
// | Admin index page for the paypal plugin.  By default, lists products      |
// | available for editing                                                    |
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
 * Admin index page for the paypal plugin.  By default, lists products available for editing
 *
 * @author Vincent Furia <vinny01 AT users DOT sourceforge DOT net>
 * @copyright Vincent Furia 2005 - 2006
 * @package paypal
 */

/**
 * Required geeklog
 */
require_once('../../../lib-common.php');

// Check for required permissions
paypal_access_check('paypal.admin');

$vars = array('msg'                         => 'text',
              'mode'                        => 'text',
			  'op'                          => 'text',
			  'cat_id'                      => 'number',
			  'cat_name'                    => 'texte',
			  'at_id'                       => 'number',
			  'at_tid'                      => 'number',
			  'parent_id'                   => 'number',
			  'category'                    => 'text',
			  'at_name'                     => 'text',
			  'type'                        => 'text',
			  'description'                 => 'html',
			  'image'                       => 'text',
              'enabled'                     => 'number',
              'owner_id'                    => 'number',
			  'group_id'                    => 'number',
			  'perm_owner[0]'               => 'number',
			  'perm_owner[1]'               => 'number',
			  'perm_group[0]'               => 'number',
			  'perm_group[1]'               => 'number',
			  'perm_members[0]'             => 'number',
			  'perm_anon[0]'                => 'number',
			  'code'                        => 'alpha',
			  'price'                       => 'text',
			  'order'                       => 'number',
			  'shipper_service_id'          => 'number',
			  'shipper_service_name'        => 'text',
			  'shipper_service_service'     => 'text',
			  'shipper_service_description' => 'text',
			  'shipper_service_exclude_cat' => 'number',
			  'shipping_to_id'              => 'number',
			  'shipping_to_name'            => 'text',
			  'shipping_to_torder'          => 'number',
			  'shipping_id'                 => 'number',
			  'shipping_amt'                => 'text',
			  'shipping_shipper_id'         => 'number',
			  'shipping_destination_id'     => 'number',
			  'shipping_min'                => 'text',
			  'shipping_max'                => 'text',
			  );
			  
paypal_filterVars($vars, $_REQUEST);

function PAYPAL_listProducts()
{
    global $_CONF, $_PAY_CONF, $_TABLES, $_IMAGE_TYPE, $LANG_ADMIN, $LANG_PAYPAL_1;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';
	
	if (DB_count($_TABLES['paypal_products']) == 0){
	return $retval = '';
	}

    if ($_PAY_CONF['display_item_id'] == 1 ) {
		$header_arr = array(      // display 'text' and use table field 'field'
			array('text' => $LANG_ADMIN['edit'], 'field' => 'edit', 'sort' => false),
			// Maybe later a clone option
			//array('text' => $LANG_ADMIN['copy'], 'field' => 'copy', 'sort' => false),
			array('text' => $LANG_PAYPAL_1['item_id_label'], 'field' => 'item_id', 'sort' => true),
			array('text' => $LANG_PAYPAL_1['name_list'], 'field' => 'name', 'sort' => true),
			array('text' => $LANG_PAYPAL_1['category'], 'field' => 'cat_id', 'sort' => true),
			array('text' => $LANG_PAYPAL_1['price_label'], 'field' => 'price', 'sort' => true),
			array('text' => $LANG_PAYPAL_1['active_field'], 'field' => 'active', 'sort' => true),
			array('text' => $LANG_PAYPAL_1['hidden_field'], 'field' => 'hidden', 'sort' => true),
			array('text' => $LANG_PAYPAL_1['hits_field'], 'field' => 'hits', 'sort' => true)
		);
	} else {
	    $header_arr = array(      // display 'text' and use table field 'field'
			array('text' => $LANG_ADMIN['edit'], 'field' => 'edit', 'sort' => false),
			// Maybe later a clone option
			//array('text' => $LANG_ADMIN['copy'], 'field' => 'copy', 'sort' => false),
			array('text' => $LANG_PAYPAL_1['ID'], 'field' => 'id', 'sort' => true),
			array('text' => $LANG_PAYPAL_1['name_list'], 'field' => 'name', 'sort' => true),
			array('text' => $LANG_PAYPAL_1['category'], 'field' => 'cat_id', 'sort' => true),
			array('text' => $LANG_PAYPAL_1['price_label'], 'field' => 'price', 'sort' => true),
			array('text' => $LANG_PAYPAL_1['active_field'], 'field' => 'active', 'sort' => true),
			array('text' => $LANG_PAYPAL_1['hidden_field'], 'field' => 'hidden', 'sort' => true),
			array('text' => $LANG_PAYPAL_1['hits_field'], 'field' => 'hits', 'sort' => true)
		);
	}
	
    $defsort_arr = array('field' => 'id', 'direction' => 'asc');

    $text_arr = array(
        'has_extras' => true,
        'form_url' => $_CONF['site_admin_url'] . '/plugins/paypal/index.php'
    );
	
	$sql = "SELECT
	            p.*, c.cat_name
            FROM {$_TABLES['paypal_products']} as p
			LEFT JOIN {$_TABLES['paypal_categories']} as c
			ON p.cat_id = c.cat_id
			WHERE 1=1
			";

    $query_arr = array(
        'table'          => 'paypal_products',
        'sql'            => $sql,
		'query_fields'   => array('name', 'price'),
        'default_filter' => COM_getPermSQL ('AND', 0, 3)
    );

    $retval .= ADMIN_list('paypal', 'PAYPAL_getListField_paypal',
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
function PAYPAL_getListField_paypal($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $LANG_ADMIN, $LANG_STATIC, $_TABLES, $_PAY_CONF;

    switch($fieldname) {
        case "edit":
		    $edit_url = $_CONF['site_admin_url'] . '/plugins/paypal/product_edit.php?op=edit&amp;id=' . $A['id'];
            $retval = COM_createLink($icon_arr['edit'], $edit_url);
            break;
        case "name":
            $url = $_PAY_CONF['site_url'] .
                                 '/product_detail.php?product=' . $A['id'];
            $retval = COM_createLink($A['name'], $url, array('title'=>$LANG_PAYPAL_1['title_display']));
            break;
        case "id":
            $retval = $A['id'];
            break;
		case "cat_id":
            $retval = $A['cat_name'];
            break;
        case "price":
            $retval = number_format($A['price'], $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator']);
            break;
		case "active":
            if ($fieldvalue == 1) {
			$retval = '<img src="'. $_CONF['site_admin_url'] . '/plugins/paypal/images/green_dot.gif" alt="">';
			} else {
			$retval = '<img src="'. $_CONF['site_admin_url'] . '/plugins/paypal/images/red_dot.gif" alt="">';
			}
            break;
		case "hidden":
            if ($fieldvalue == 0) {
			$retval = '<img src="'. $_CONF['site_admin_url'] . '/plugins/paypal/images/green_dot.gif" alt="">';
			} else {
			$retval = '<img src="'. $_CONF['site_admin_url'] . '/plugins/paypal/images/red_dot.gif" alt="">';
			}
            break;
		case "hits":
			$retval = $A['hits'];
            break;
        default:
            $retval = stripslashes($fieldvalue);
            break;
    }
    return $retval;
}

function PAYPAL_listCategories()
{
    global $_CONF, $_TABLES, $_IMAGE_TYPE, $LANG_ADMIN, $LANG_PAYPAL_1;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_ADMIN['edit'], 'field' => 'edit', 'sort' => false),
        array('text' => $LANG_PAYPAL_1['ID'], 'field' => 'cat_id', 'sort' => true),
		array('text' => $LANG_PAYPAL_1['category_heading'], 'field' => 'cat_name', 'sort' => true),
        array('text' => $LANG_PAYPAL_1['active_field'], 'field' => 'enabled', 'sort' => true)
    );
	

    $defsort_arr = array('field' => 'cat_name', 'direction' => 'asc');

    $text_arr = array(
        'has_extras' => true,
        'form_url' => $_CONF['site_admin_url'] . '/plugins/paypal/index.php?mode=categories'
    );
	
	$sql = "SELECT
	            *
            FROM {$_TABLES['paypal_categories']}
			WHERE 1=1 ";

    $query_arr = array(
        'table'          => 'paypal_categories',
        'sql'            => $sql,
        'query_fields'   => array('cat_id', 'cat_name', 'enabled'),
        'default_filter' => COM_getPermSQL ('AND', 0, 3)
    );

    $retval .= ADMIN_list('paypal_categories', 'PAYPAL_getListField_categories',
                          $header_arr, $text_arr, $query_arr, $defsort_arr);

    return $retval;
}

/**
*   Get an individual field for the categories screen.
*
*   @param  string  $fieldname  Name of field (from the array, not the db)
*   @param  mixed   $fieldvalue Value of the field
*   @param  array   $A          Array of all fields from the database
*   @param  array   $icon_arr   System icon array
*   @param  object  $EntryList  This entry list object
*   @return string              HTML for field display in the table
*/
function PAYPAL_getListField_categories($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $LANG_ADMIN, $LANG_STATIC, $_TABLES, $_PAY_CONF;

    switch($fieldname) {
        case "edit":
		    $edit_url = $_CONF['site_admin_url'] . '/plugins/paypal/index.php?mode=categories&amp;op=edit&amp;cat_id=' . $A['cat_id'];
            $retval = COM_createLink($icon_arr['edit'], $edit_url);
            break;

        default:
            $retval = stripslashes($fieldvalue);
            break;
    }
    return $retval;
}

/**
 * This function creates a category Form
 *
 * Creates a Form for a category using the supplied defaults (if specified).
 *
 * @param array $category array of values describing a proudct
 * @return string HTML string of category form
 */
function PAYPAL_getCategoryForm( $category = array() ) {

    global $_CONF, $_PAY_CONF, $LANG_PAYPAL_1, $LANG_PAYPAL_ADMIN, $LANG_ACCESS, $_TABLES;

    //PHP 5.4 set all $catory[key] 
	PAYPAL_setAllKeys($category, array('cat_id', 'cat_name', 'description', 'enabled', 'image', 'perm_owner', 'owner_id', 'group_id', 'perm_group', 'perm_members', 'perm_anon'));
	
	//Display form
	($category['cat_name'] == '') ? $retval = COM_startBlock($LANG_PAYPAL_ADMIN['create_category']) : $retval = COM_startBlock($LANG_PAYPAL_1['edit_label'] . ' ' . $category['cat_name']);

    $template = COM_newTemplate($_CONF['path'] . 'plugins/paypal/templates');
    $template->set_file(array('category' => 'category_form.thtml'));
	if (is_numeric($category['cat_id'])) {
        $template->set_var( array (
		                    'cat_id' => '<input type="hidden" name="cat_id" value="' . $category['cat_id'] .'" />',
							'delete_button' => '<option value="delete">' . $LANG_PAYPAL_1['delete_button'] . '</option>'
							));
		$creation = false;
    } else {
        $template->set_var( array (
		                    'cat_id' => '',
							'delete_button' => ''
							));
		$creation = true;
    }
	if ($creation) {
		$peditor = SEC_getPermissionsHTML(3,3,2,2);
		$admin_group = DB_getItem($_TABLES['groups'], 'grp_id', "grp_name = 'Paypal Admin'");
		$gdropdown = SEC_getGroupDropdown($admin_group, 3);
		$parent_cat = PAYPAL_adOptionList($_TABLES['paypal_categories'], 'cat_id,cat_name', 0, 'parent_id', 'cat_id <> ' . 0);
		$category['image'] = 'none.jpg';
    } else {
	    $peditor = SEC_getPermissionsHTML($category['perm_owner'], $category['perm_group'],$category['perm_members'], $category['perm_anon']);
		$gdropdown = SEC_getGroupDropdown($category['group_id'], 3);        
		$parent_cat = PAYPAL_adOptionList($_TABLES['paypal_categories'], 'cat_id,cat_name', $category['parent_id'], 'parent_id', 'cat_id <> ' . $category['cat_id']);
	}
    $template->set_var( array (
	                    'cat_name'           => $category['cat_name'],
						'category_label'     => $LANG_PAYPAL_ADMIN['category_label'],
						'required_field'     => $LANG_PAYPAL_1['required_field'],
						'ok_button'          => $LANG_PAYPAL_1['ok_button'],
						'save_button'        => $LANG_PAYPAL_1['save_button'],
						'lang_group'         => $LANG_ACCESS['group'],
						'group_dropdown'     => $gdropdown,
						'permissions_editor' => $peditor,
						'lang_permissions'   => $LANG_ACCESS['permissions'],
						'lang_perm_key'      => $LANG_ACCESS['permissionskey'],
						'permissions_msg'    => $LANG_ACCESS['permmsg'],
						'lang_permissions_msg' => $LANG_ACCESS['permmsg'],
						'lang_accessrights'  => $LANG_ACCESS['accessrights'],
						'lang_owner'         => $LANG_ACCESS['owner'],
						'owner_name'         => ($creation) ? COM_getDisplayName($_USERS['uid']) : COM_getDisplayName($category['owner_id']),
						'owner_id'           => $category['owner_id'],
						'admin_url'          => $_CONF['site_admin_url'],
						'description'        => $category['description'],
						'description_label'  => $LANG_PAYPAL_ADMIN['description_label'],
						'yes'                => $LANG_PAYPAL_1['yes'],
                        'no'                 => $LANG_PAYPAL_1['no'],
						'parent_id'          => $LANG_PAYPAL_ADMIN['parent_id'],
						'top_cat'            => $LANG_PAYPAL_ADMIN['top_cat'],
						'image_message'      => $LANG_PAYPAL_ADMIN['image_message'],
						'image'              => $LANG_PAYPAL_ADMIN['image'],
						'main_settings'      => $LANG_PAYPAL_ADMIN['main_settings'],
						));
	//enabled
    $template->set_var('enabled', $LANG_PAYPAL_ADMIN['enabled_category']);
	($category['enabled'] == '') ? $category['enabled'] = 1 : NULL;
	if ($category['enabled'] == 1) {
        $template->set_var('enabled_yes', ' selected');
        $template->set_var('enabled_no', '');
    } else {
        $template->set_var('enabled_yes', '');
        $template->set_var('enabled_no', ' selected');
    }
	
	//parent cat
	$template->set_var('parent_cat', $parent_cat);
	
	//Image
	$cat_image = $_PAY_CONF['path_cat_images'] . $category['image'];
	if (is_file($cat_image)) {
		$template->set_var('cat_image','<p>' . $LANG_PAYPAL_ADMIN['image_replace'] . '<p><p><img src="' . $_PAY_CONF['site_url'] . '/timthumb.php?src='
		. $_PAY_CONF['images_cat_url'] . $category['image'] . '&amp;w=150&amp;q=70&amp;zc=1" class="cat_image" alt="" /></p>');
	} else {
		$template->set_var('cat_image', '');
	}

	$retval .= $template->parse('output', 'category');

    $retval .= COM_endBlock();
	
    return $retval;
}

function PAYPAL_saveCatImage ($category, $files, $cat_id) {
    global $_CONF, $_PAY_CONF, $_TABLES, $LANG24;
	
    $args = $category;

    // Handle Magic GPC Garbage:
    while (list($key, $value) = each($args)) {
        if (!is_array($value)) {
            $args[$key] = COM_stripslashes($value);
        } else {
            while (list($subkey, $subvalue) = each($value)) {
                $value[$subkey] = COM_stripslashes($subvalue);
            }
        }
    }

	// OK, let's upload any pictures with the product
	require_once($_CONF['path_system'] . 'classes/upload.class.php');
	$upload = new upload();

	//Debug with story debug function
	if (isset ($_CONF['debug_image_upload']) && $_CONF['debug_image_upload']) {
		$upload->setLogFile ($_CONF['path'] . 'logs/error.log');
		$upload->setDebug (true);
	}
	$upload->setMaxFileUploads (1);
	if (!empty($_CONF['image_lib'])) {
		if ($_CONF['image_lib'] == 'imagemagick') {
			// Using imagemagick
			$upload->setMogrifyPath ($_CONF['path_to_mogrify']);
		} elseif ($_CONF['image_lib'] == 'netpbm') {
			// using netPBM
			$upload->setNetPBM ($_CONF['path_to_netpbm']);
		} elseif ($_CONF['image_lib'] == 'gdlib') {
			// using the GD library
			$upload->setGDLib ();
		}
		$upload->setAutomaticResize(true);
		$upload->keepOriginalImage (false);

		if (isset($_CONF['jpeg_quality'])) {
			$upload->setJpegQuality($_CONF['jpeg_quality']);
		}
	}
	$upload->setAllowedMimeTypes (array (
			'image/gif'   => '.gif',
			'image/jpeg'  => '.jpg,.jpeg',
			'image/pjpeg' => '.jpg,.jpeg',
			'image/x-png' => '.png',
			'image/png'   => '.png'
			));
	
	if (!$upload->setPath($_PAY_CONF['path_cat_images'])) {
		$output = COM_siteHeader ('menu', $LANG24[30]);
		$output .= COM_startBlock ($LANG24[30], '', COM_getBlockTemplate ('_msg_block', 'header'));
		$output .= $upload->printErrors (false);
		$output .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
		$output .= COM_siteFooter ();
		echo $output;
		exit;
	}

	// NOTE: if $_CONF['path_to_mogrify'] is set, the call below will
	// force any images bigger than the passed dimensions to be resized.
	// If mogrify is not set, any images larger than these dimensions
	// will get validation errors
	$upload->setMaxDimensions($_PAY_CONF['max_image_width'], $_PAY_CONF['max_image_height']);
	$upload->setMaxFileSize($_PAY_CONF['max_image_size']); // size in bytes, 1048576 = 1MB

	// Set file permissions on file after it gets uploaded (number is in octal)
	$upload->setPerms('0644');

	$curfile = current($files);
	if (!empty($curfile['name'])) {
		$pos = strrpos($curfile['name'],'.') + 1;
		$fextension = substr($curfile['name'], $pos);
		$filenames = 'cat_' . $cat_id  . '.' . $fextension;
	}
    if ($filenames != '') {
		$upload->setFileNames($filenames);
		reset($files);
		$upload->uploadFiles();

		if ($upload->areErrors()) {
			$retval = COM_siteHeader('menu', $LANG24[30]);
			$retval .= COM_startBlock ($LANG24[30], '',
						COM_getBlockTemplate ('_msg_block', 'header'));
			$retval .= $upload->printErrors(false);
			$retval .= COM_endBlock(COM_getBlockTemplate ('_msg_block', 'footer'));
			$retval .= COM_siteFooter();
			echo $retval;
			exit;
		}
		
		DB_query("UPDATE {$_TABLES['paypal_categories']} SET image = '" . $filenames . "' WHERE cat_id=" . $cat_id);
	}

	return true;
}


function PAYPAL_deleteCatImage ($image)
{
    global $_CONF;

    if (empty ($image)) {
        return;
    }
	
	$pi = $_CONF['path_images'] . 'paypal/categories/' . $image;
			if (!@unlink ($pi)) {
                // log the problem but don't abort the script
                echo COM_errorLog ('Unable to remove the following category image from paypal plugin: ' . $image);
            }
}

function PAYPAL_shippingServices () 
{
    global $LANG_PAYPAL_ADMIN, $_SCRIPTS;
    
    $js = 'jQuery(function () {
        var tabContainers = jQuery(\'div.tabs > div\');
        
        jQuery(\'div.tabs ul.tabNavigation a\').click(function () {
            tabContainers.hide().filter(this.hash).show();
            
            jQuery(\'div.tabs ul.tabNavigation a\').removeClass(\'selected\');
            jQuery(this).addClass(\'selected\');
            
            return false;
        }).filter(\':first\').click();
    });';
	
	$_SCRIPTS->setJavaScriptLibrary('jquery');
	$_SCRIPTS->setJavaScript($js, true);

    $retval = '&nbsp;<div class="tabs">
      <!-- tabs -->
      <ul class="tabNavigation">
        <li><a href="#shipping_costs">' . $LANG_PAYPAL_ADMIN['shipping_costs'] . '</a></li>
        <li><a href="#shipper_services">'. $LANG_PAYPAL_ADMIN['shipper_services'] . '</a></li>
        <li><a href="#shipping_locations">'. $LANG_PAYPAL_ADMIN['shipping_locations'] . '</a></li>
      </ul>

      <!-- tab containers -->
      <div id="shipping_costs">'  . PAYPAL_shippingCosts() . '</div>
      <div id="shipper_services"">' .  PAYPAL_shipperServices() . '</div>
      <div id="shipping_locations">' .  PAYPAL_shippingLocations() . '</div>
    </div>';

    return $retval;
}

function PAYPAL_shippingCosts ()
{
    return PAYPAL_listShippingCosts();
}

function PAYPAL_shipperServices ()
{
    return PAYPAL_listShipperServices();
}

function PAYPAL_shippingLocations ()
{
    return PAYPAL_listShippingTo();
}

function PAYPAL_listShipperServices()
{
    global $_CONF, $_TABLES, $_IMAGE_TYPE, $LANG_ADMIN, $LANG_PAYPAL_1,$LANG_PAYPAL_ADMIN;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_ADMIN['edit'], 'field' => 'edit', 'sort' => false),
        array('text' => $LANG_PAYPAL_1['ID'], 'field' => 'shipper_service_id', 'sort' => true),
		array('text' => $LANG_PAYPAL_ADMIN['shipper_label'], 'field' => 'shipper_service_name', 'sort' => true),
        array('text' => $LANG_PAYPAL_ADMIN['service_label'], 'field' => 'shipper_service_service', 'sort' => true)
    );
	

    $defsort_arr = array('field' => 'shipper_service_name', 'direction' => 'asc');

    $text_arr = array(
        'has_extras' => true,
        'form_url' => $_CONF['site_admin_url'] . '/plugins/paypal/index.php?mode=shipping'
    );
	
	$sql = "SELECT
	            *
            FROM {$_TABLES['paypal_shipper_service']}
			WHERE 1=1 ";

    $query_arr = array(
        'sql'            => $sql,
		'query_fields'   => array('shipper_service_name', 'shipper_service_service'),
    );

    $retval .= '<p>' . $LANG_PAYPAL_1['you_can'] . '<a href="' . $_CONF['site_url'] . '/admin/plugins/paypal/index.php?mode=shipping&amp;op=edit_shipper">' 
		   . $LANG_PAYPAL_ADMIN['create_shipper'] . '</a></p>';
		   
	$retval .= ADMIN_list('paypal_shippers', 'PAYPAL_getListField_shipperServices',
                          $header_arr, $text_arr, $query_arr, $defsort_arr);
	
    return $retval;
}

/**
*   Get an individual field for the shippers screen.
*
*   @param  string  $fieldname  Name of field (from the array, not the db)
*   @param  mixed   $fieldvalue Value of the field
*   @param  array   $A          Array of all fields from the database
*   @param  array   $icon_arr   System icon array
*   @param  object  $EntryList  This entry list object
*   @return string              HTML for field display in the table
*/
function PAYPAL_getListField_shipperServices($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $LANG_ADMIN, $LANG_STATIC, $_TABLES, $_PAY_CONF;

    switch($fieldname) {
        case "edit":
		    $edit_url = $_CONF['site_admin_url'] . '/plugins/paypal/index.php?mode=shipping&amp;op=edit_shipper&amp;shipper_service_id=' . $A['shipper_service_id'];
            $retval = COM_createLink($icon_arr['edit'], $edit_url);
            break;
			
		case "shipper_service_service":
		    if ($A['shipper_service_description'] != '') {
			    $retval = COM_getTooltip($A['shipper_service_service'], $A['shipper_service_description'], '', '', 'information') ;
				break;
		    }

        default:
            $retval = stripslashes($fieldvalue);
            break;
    }
    return $retval;
}

/**
 * This function creates a shipper Form
 *
 * Creates a Form for a shipper using the supplied defaults (if specified).
 *
 * @param array $shipper array of values describing a proudct
 * @return string HTML string of shipper form
 */
function PAYPAL_getShipperForm( $shipper = array() ) {

    global $_CONF, $_PAY_CONF, $LANG_PAYPAL_1, $LANG_PAYPAL_ADMIN, $LANG_ACCESS, $_TABLES;

    //PHP 5.4 set all $shipper[key] 
	PAYPAL_setAllKeys($shipper, array('shipper_service_id', 'shipper_service_name', 'shipper_service_description', 'shipper_service_service', 'shipper_service_exclude_cat'));
	
	//Display form
	($shipper['shipper_service_name'] == '') ? $retval = COM_startBlock($LANG_PAYPAL_ADMIN['create_shipper']) : $retval = COM_startBlock($LANG_PAYPAL_1['edit_label'] . ' ' . $shipper['shipper_service_name']);

    $template = COM_newTemplate($_CONF['path'] . 'plugins/paypal/templates');
    $template->set_file(array('shipper' => 'shipper_form.thtml'));
	if (is_numeric($shipper['shipper_service_id'])) {
        $template->set_var( array (
		                    'shipper_id' => '<input type="hidden" name="shipper_service_id" value="' . $shipper['shipper_service_id'] .'" />',
							'delete_button' => '<option value="delete_shipper">' . $LANG_PAYPAL_1['delete_button'] . '</option>'
							));
		$creation = false;
    } else {
        $template->set_var( array (
		                    'shipper_id' => '',
							'delete_button' => ''
							));
		$creation = true;
    }
    $template->set_var( array (
	                    'shipper_service_name'        => $shipper['shipper_service_name'],
						'shipper_label'               => $LANG_PAYPAL_ADMIN['shipper_label'],
						'required_field'              => $LANG_PAYPAL_1['required_field'],
						'ok_button'                   => $LANG_PAYPAL_1['ok_button'],
						'save_button'                 => $LANG_PAYPAL_1['save_button'],
						'admin_url'                   => $_CONF['site_admin_url'],
						'shipper_service_description' => $shipper['shipper_service_description'],
						'description_label'           => $LANG_PAYPAL_ADMIN['description_label'],
						'exclude_cat_label'           => $LANG_PAYPAL_ADMIN['exclude_cat_label'],
						'shipper_service_service'     => $shipper['shipper_service_service'],
						'service_label'               => $LANG_PAYPAL_ADMIN['service_label'],
						'yes'                         => $LANG_PAYPAL_1['yes'],
                        'no'                          => $LANG_PAYPAL_1['no'],
						'main_settings'               => $LANG_PAYPAL_ADMIN['main_settings'],
						));

	//exclude cat
	$exclude_cat = '';
    $exclude_cat .= '<option value="0">' . $LANG_PAYPAL_ADMIN['none'] . '</option>';
	$exclude_cat .= PAYPAL_adOptionList($_TABLES['paypal_categories'], 'cat_id, cat_name', $shipper['shipper_service_exclude_cat'], 'cat_name');
	$template->set_var('exclude_cat', $exclude_cat);
	
	$retval .= $template->parse('output', 'shipper');

    $retval .= COM_endBlock();
	
    return $retval;
}

function PAYPAL_listShippingTo ()
{
    global $_CONF, $_TABLES, $_IMAGE_TYPE, $LANG_ADMIN, $LANG_PAYPAL_1,$LANG_PAYPAL_ADMIN;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_ADMIN['edit'], 'field' => 'edit', 'sort' => false),
        array('text' => $LANG_PAYPAL_1['ID'], 'field' => 'shipping_to_id', 'sort' => true),
		array('text' => $LANG_PAYPAL_ADMIN['shipping_to_label'], 'field' => 'shipping_to_name', 'sort' => true),
		array('text' => $LANG_PAYPAL_ADMIN['order_label'], 'field' => 'shipping_to_order', 'sort' => true)
    );
	

    $defsort_arr = array('field' => 'shipping_to_order', 'direction' => 'asc');

    $text_arr = array(
        'has_extras' => true,
        'form_url' => $_CONF['site_admin_url'] . '/plugins/paypal/index.php?mode=shipping'
    );
	
	$sql = "SELECT
	            *
            FROM {$_TABLES['paypal_shipping_to']}
			WHERE 1=1 ";

    $query_arr = array(
        'sql'            => $sql,
		'query_fields'   => array('shipping_to_name'),
		
    );

    $retval .= '<p>' . $LANG_PAYPAL_1['you_can'] . '<a href="' . $_CONF['site_url'] . '/admin/plugins/paypal/index.php?mode=shipping&amp;op=edit_shipping_to">' 
		   . $LANG_PAYPAL_ADMIN['create_shipping_to'] . '</a></p>';
		   
	$retval .= ADMIN_list('paypal_shipping_to', 'PAYPAL_getListField_shippingTo',
                          $header_arr, $text_arr, $query_arr, $defsort_arr);
	
    return $retval;
}

/**
*   Get an individual field for the shippers screen.
*
*   @param  string  $fieldname  Name of field (from the array, not the db)
*   @param  mixed   $fieldvalue Value of the field
*   @param  array   $A          Array of all fields from the database
*   @param  array   $icon_arr   System icon array
*   @param  object  $EntryList  This entry list object
*   @return string              HTML for field display in the table
*/
function PAYPAL_getListField_shippingTo($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $LANG_ADMIN, $LANG_STATIC, $_TABLES, $_PAY_CONF;

    switch($fieldname) {
        case "edit":
		    $edit_url = $_CONF['site_admin_url'] . '/plugins/paypal/index.php?mode=shipping&amp;op=edit_shipping_to&amp;shipping_to_id=' . $A['shipping_to_id'];
            $retval = COM_createLink($icon_arr['edit'], $edit_url);
            break;

        default:
            $retval = stripslashes($fieldvalue);
            break;
    }
    return $retval;
}

/**
 * This function creates a shipping_to Form
 *
 * Creates a Form for a shipping_to using the supplied defaults (if specified).
 *
 * @param array $shipping_to array of values describing a proudct
 * @return string HTML string of shipping_to form
 */
function PAYPAL_getShippingToForm( $shipping_to = array() ) {

    global $_CONF, $_PAY_CONF, $LANG_PAYPAL_1, $LANG_PAYPAL_ADMIN, $LANG_ACCESS, $_TABLES;

    //PHP 5.4 set all $shipping_to[key] 
	PAYPAL_setAllKeys($shipping_to, array('shipping_to_id', 'shipping_to_name', 'shipping_to_order'));
	
	//Display form
	($shipping_to['shipping_to_name'] == '') ? $retval = COM_startBlock($LANG_PAYPAL_ADMIN['create_shipping_to']) : $retval = COM_startBlock($LANG_PAYPAL_1['edit_label'] . ' ' . $shipping_to['shipping_to_name']);

    $template = COM_newTemplate($_CONF['path'] . 'plugins/paypal/templates');
    $template->set_file(array('shipping_to' => 'shipping_to_form.thtml'));
	if (is_numeric($shipping_to['shipping_to_id'])) {
        $template->set_var( array (
		                    'shipping_to_id' => '<input type="hidden" name="shipping_to_id" value="' . $shipping_to['shipping_to_id'] .'" />',
							'delete_button' => '<option value="delete_shipping_to">' . $LANG_PAYPAL_1['delete_button'] . '</option>'
							));
		$creation = false;
    } else {
        $template->set_var( array (
		                    'shipping_to_id' => '',
							'delete_button' => ''
							));
		$creation = true;
    }
    $template->set_var( array (
	                    'shipping_to_name'            => $shipping_to['shipping_to_name'],
						'shipping_to_label'           => $LANG_PAYPAL_ADMIN['shipping_to_label'],
						'required_field'              => $LANG_PAYPAL_1['required_field'],
						'ok_button'                   => $LANG_PAYPAL_1['ok_button'],
						'save_button'                 => $LANG_PAYPAL_1['save_button'],
						'admin_url'                   => $_CONF['site_admin_url'],
						'shipping_to_order'           => $shipping_to['shipping_to_order'],
						'order_label'                 => $LANG_PAYPAL_ADMIN['order_label'],
						'yes'                         => $LANG_PAYPAL_1['yes'],
                        'no'                          => $LANG_PAYPAL_1['no'],
						'main_settings'               => $LANG_PAYPAL_ADMIN['main_settings'],
						));

	$retval .= $template->parse('output', 'shipping_to');

    $retval .= COM_endBlock();
	
    return $retval;
}

/**
 * shipping cost
 */
 
function PAYPAL_listShippingCosts ()
{
    global $_CONF, $_TABLES, $_IMAGE_TYPE, $LANG_ADMIN, $LANG_PAYPAL_1,$LANG_PAYPAL_ADMIN;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';
	$shipping_to = DB_count($_TABLES['paypal_shipping_to'], '1', '1');
	$shipper_service = DB_count($_TABLES['paypal_shipper_service'], '1', '1');
	if ( $shipping_to < 1 || $shipper_service < 1 ) {
	    return '<p>' . $LANG_PAYPAL_ADMIN['create_shipper_destination'] . '</p>';
	}

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_ADMIN['edit'], 'field' => 'edit', 'sort' => false),
		array('text' => $LANG_PAYPAL_ADMIN['shipper_services'], 'field' => 'shipping_shipper_id', 'sort' => true),
		array('text' => $LANG_PAYPAL_ADMIN['shipping_amt'], 'field' => 'shipping_amt', 'sort' => true)
    );
	

    //$defsort_arr = array('field' => 'shipping_shipper_id', 'direction' => 'asc');

    $text_arr = array(
        'has_extras' => true,
        'form_url' => $_CONF['site_admin_url'] . '/plugins/paypal/index.php?mode=shipping'
    );
	
	$sql = "SELECT
	            *
            FROM {$_TABLES['paypal_shipping_cost']} AS sc
			LEFT JOIN {$_TABLES['paypal_shipper_service']} AS ss
			ON sc.shipping_shipper_id = ss.shipper_service_id
			LEFT JOIN {$_TABLES['paypal_shipping_to']} AS st
			ON sc.shipping_destination_id = st.shipping_to_id
			WHERE 1=1 
			ORDER BY st.shipping_to_order, sc.shipping_shipper_id ASC
			";

    $query_arr = array(
        'sql'            => $sql,
		'query_fields'   => array('shipping_amt'),
    );

    $retval .= '<p>' . $LANG_PAYPAL_1['you_can'] . '<a href="' . $_CONF['site_url'] . '/admin/plugins/paypal/index.php?mode=shipping&amp;op=edit_shipping_cost">' 
		   . $LANG_PAYPAL_ADMIN['create_shipping_cost'] . '</a></p>';
		   
	$retval .= ADMIN_list('paypal_shipping_cost', 'PAYPAL_getListField_shippingCost',
                          $header_arr, $text_arr, $query_arr, $defsort_arr);
	
    return $retval;
}
 
function PAYPAL_getListField_shippingCost($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $LANG_ADMIN, $LANG_STATIC, $_TABLES, $_PAY_CONF;

    switch($fieldname) {
        case "edit":
		    $edit_url = $_CONF['site_admin_url'] . '/plugins/paypal/index.php?mode=shipping&amp;op=edit_shipping_cost&amp;shipping_id=' . $A['shipping_id'];
            $retval = COM_createLink($icon_arr['edit'], $edit_url);
            break;
		case "shipping_shipper_id":
		     $retval = $A['shipper_service_name'] .  ' - ' . $A['shipper_service_service'] . ' | ' . $A['shipping_to_name'] . ' (' . $A['shipping_min'] . '-' . $A['shipping_max'] . ')';
            break;
        default:
            $retval = stripslashes($fieldvalue);
            break;
    }
    return $retval;
}

function PAYPAL_getShippingCostForm( $shipping_cost = array() ) {

    global $_CONF, $_PAY_CONF, $LANG_PAYPAL_1, $LANG_PAYPAL_ADMIN, $LANG_ACCESS, $_TABLES;

    //PHP 5.4 set all $shipping_cost[key] 
	PAYPAL_setAllKeys($shipping_cost, array('shipping_id', 'shipping_shipper_id', 'shipping_cost_name', 'shipping_max', 'shipping_min', 'shipping_amt', 'shipping_destination_id'));
	
	//Display form
	($shipping_cost['shipping_cost_name'] == '') ? $retval = COM_startBlock($LANG_PAYPAL_ADMIN['create_shipping_cost']) : $retval = COM_startBlock($LANG_PAYPAL_1['edit_label'] . ' ' . $shipping_cost['shipping_cost_name']);

    $template = COM_newTemplate($_CONF['path'] . 'plugins/paypal/templates');
    $template->set_file(array('shipping_cost' => 'shipping_cost_form.thtml'));
	if (is_numeric($shipping_cost['shipping_id'])) {
        $template->set_var( array (
		                    'shipping_id' => '<input type="hidden" name="shipping_id" value="' . $shipping_cost['shipping_id'] .'" />',
							'delete_button' => '<option value="delete_shipping_cost">' . $LANG_PAYPAL_1['delete_button'] . '</option>'
							));
		$creation = false;
    } else {
        $template->set_var( array (
		                    'shipping_id' => '',
							'delete_button' => ''
							));
		$creation = true;
    }
	if ($shipping_cost['shipping_max'] == '') $shipping_cost['shipping_max'] = '0.000';
	if ($shipping_cost['shipping_min'] == '') $shipping_cost['shipping_min'] = '0.000';
	if ($shipping_cost['shipping_amt'] == '') $shipping_cost['shipping_amt'] = '0.00';
	
    $template->set_var( array (
	                    'shipping_amt'          => $shipping_cost['shipping_amt'],
						'shipping_max'                => $shipping_cost['shipping_max'],
						'shipping_min'                => $shipping_cost['shipping_min'],
						'shipping_max_label'          => $LANG_PAYPAL_ADMIN['shipping_max'],
						'shipping_min_label'          => $LANG_PAYPAL_ADMIN['shipping_min'],
						'shipping_amt_label'          => $LANG_PAYPAL_ADMIN['shipping_amt'],
						'shipper_label'               => $LANG_PAYPAL_ADMIN['shipper_label'],
						'destination_label'           => $LANG_PAYPAL_ADMIN['shipping_to_label'],
						'required_field'              => $LANG_PAYPAL_1['required_field'],
						'ok_button'                   => $LANG_PAYPAL_1['ok_button'],
						'save_button'                 => $LANG_PAYPAL_1['save_button'],
						'admin_url'                   => $_CONF['site_admin_url'],
						'yes'                         => $LANG_PAYPAL_1['yes'],
                        'no'                          => $LANG_PAYPAL_1['no'],
						'main_settings'               => $LANG_PAYPAL_ADMIN['main_settings'],
						));
	//shipper
	$shipper = '';
    $shipper .= '<option value="0">' . $LANG_PAYPAL_ADMIN['choose_shipper'] . '</option>';
	$shipper .= PAYPAL_adOptionList($_TABLES['paypal_shipper_service'], 'shipper_service_id, shipper_service_name, shipper_service_service', 
		$shipping_cost['shipping_shipper_id'], 'shipper_service_name');
	$template->set_var('shipper', $shipper);
	
	//destination
	$destination = '';
    $destination .= '<option value="0">' . $LANG_PAYPAL_ADMIN['choose_destination'] . '</option>';
	$destination .= PAYPAL_adOptionList($_TABLES['paypal_shipping_to'], 'shipping_to_id, shipping_to_name', $shipping_cost['shipping_destination_id'], 'shipping_to_name');
	$template->set_var('destination', $destination);

	$retval .= $template->parse('output', 'shipping_cost');

    $retval .= COM_endBlock();
	
    return $retval;
}

/**
* Re-orders all destinations in increments of 10
*
*/
function PAYPAL_reorderShippingTo()
{
    global $_TABLES;

    $sql = "SELECT * 
			FROM {$_TABLES['paypal_shipping_to']}
        	ORDER BY shipping_to_order;";
    $result = DB_query($sql);
    $nrows = DB_numRows($result);

    $itemOrd = 10;
    $stepNumber = 10;

    for ($i = 0; $i < $nrows; $i++) {
        $A = DB_fetchArray($result);

        if ($A['shipping_to_id'] != $itemOrd) {  // only update incorrect ones
            $q = "UPDATE " . $_TABLES['paypal_shipping_to'] . " SET shipping_to_order = '" .
                  $itemOrd . "' WHERE shipping_to_id = '" . $A['shipping_to_id'] ."'";
            DB_query($q);
        }
        $itemOrd += $stepNumber;
    }
}

function PAYPAL_requiredSettings()
{
	$retval = '<style type="text/css">
			.success { color:green; }
			.error { color:red; }
		</style>';
	$code_version = plugin_chkVersion_paypal();
	$retval .= '<small><table border="0" cellspacing="10" cellpadding="20" width="100%"><tr><td width="50%"><h3>Paypal plugin v' . $code_version . ' Required settings</h3>' . LB;
	$retval .= "<ul>";

	$phpVersion = phpversion();
	$class = 'error';
	if ($phpVersion >= 5.2) {
		$class = 'success';
	}
	$retval .= "<li class='$class'><strong>PHP version:</strong> Requires version 5.2 or newer, this server is using version $phpVersion</li>\n";

	$_SESSION['support'] = true;
	if ($_SESSION['support'] === true) {
		$retval .= "<li class='success'><strong>Session support:</strong> Enabled</li>\n";
	}
	else {
		$retval .= "<li class='error'><strong>Session support:</strong> Not enabled</li>\n";
	}
	$retval .= "</ul>";

	$retval .= "</td><td><h3>Recommended settings</h3>\n";
	$retval .= "<ul>";

	$registerGlobals = ini_get('register_globals');
	if ($registerGlobals) {
		$retval .= "<li class='error'><strong>Register globals:</strong> On</li>\n";
	}
	else {
		$retval .= "<li class='success'><strong>Register globals:</strong> Off</li>\n";
	}

	$errorReporting = ini_get('error_reporting');
	if ($errorReporting) {
		$retval .= "<li class='error'><strong>Display errors:</strong> On</li>\n";
	}
	else {
		$retval .= "<li class='success'><strong>Display errors:</strong> Off</li>\n";
	}
	$retval .= "</ul></td></tr></table></small>";
	
	return $retval;
}

//Main
$cat_id = $_REQUEST['cat_id'];
$shipper_id = $_REQUEST['shipper_service_id'];
$shipping_to_id = $_REQUEST['shipping_to_id'];
$shipping_id = $_REQUEST['shipping_id'];

$display = COM_siteHeader('none');

$display .= paypal_admin_menu();

if (!empty($_REQUEST['msg'])) $display .= PAYPAL_message($_REQUEST['msg']);

//Check if picture folder is writable
if ( !file_exists($_PAY_CONF['path_images']) || !is_writable($_PAY_CONF['path_images']) ) {
    $display .= COM_showMessageText( '>> '. $_PAY_CONF['path_images'] . '<p>' . $LANG_PAYPAL_1['image_not_writable'] . '</p>');
} else {
    // check jquery plugin
    if (! in_array('jquery', $_PLUGINS)) {
        $display .= '<p>'. $LANG_PAYPAL_1['install_jquery'] . ' >> <a href="http://geeklog.fr/wiki/plugins:jquery" target="_blank">jQuery plugin</a></p>';
    }

    switch ($_REQUEST['mode']) {
	    case 'categories':
			switch ($_REQUEST['op']) {
				case 'edit':
					$cat = array();
					if (is_numeric($cat_id)) {
						$sql = "SELECT * FROM {$_TABLES['paypal_categories']} WHERE cat_id = {$cat_id}";
						$res = DB_query($sql);
						$cat = DB_fetchArray($res);
					}
					$display .= PAYPAL_getCategoryForm($cat);
					break;
				case 'save' :
						// Convert array values to numeric permission values
						if (is_array($_REQUEST['perm_owner']) OR is_array($_REQUEST['perm_group']) OR is_array($_REQUEST['perm_members']) OR is_array($_REQUEST['perm_anon'])) {
							list($_REQUEST['perm_owner'],$_REQUEST['perm_group'],$_REQUEST['perm_members'],$_REQUEST['perm_anon']) 
								= SEC_getPermissionValues($_REQUEST['perm_owner'],$_REQUEST['perm_group'],$_REQUEST['perm_members'],$_REQUEST['perm_anon']);
						}
						
						if ($_REQUEST['category'] == '') {
							$display .= PAYPAL_getCategoryForm($_REQUEST);
							break;
						}
						// prepare strings for insertion
						$_REQUEST['category'] = addslashes($_REQUEST['category']);
						$_REQUEST['description'] = addslashes($_REQUEST['description']);
						$admin_group = DB_getItem($_TABLES['groups'], 'grp_id', "grp_name = 'Paypal Admin'");

						$sql = "cat_name = '{$_REQUEST['category']}', "
						 . "description = '{$_REQUEST['description']}', "
						 . "enabled = '{$_REQUEST['enabled']}', "
						 . "parent_id = '{$_REQUEST['parent_id']}', "
						 . "group_id = '{$_REQUEST['group_id']}', "
						 . "perm_owner = '{$_REQUEST['perm_owner']}', "
						 . "perm_group = '{$_REQUEST['perm_group']}', "
						 . "perm_members = '{$_REQUEST['perm_members']}', "
						 . "perm_anon = '{$_REQUEST['perm_anon']}'						
						";
						
						if ( $cat_id != 0) {
							//Edit mode 
							$sql = "UPDATE {$_TABLES['paypal_categories']} SET $sql "
								 . "WHERE cat_id = {$cat_id}";
						} else {
							//Create mode
							$sql .= ", owner_id = '{$_USER['uid']}' ";
							$sql = "INSERT INTO {$_TABLES['paypal_categories']} SET $sql ";
						}
						DB_query($sql);
						if (DB_error()) {
							$msg = $LANG_PAYPAL_1['save_fail'];
						} else {
							$msg = $LANG_PAYPAL_1['save_success'];
						}
						
						//Process images
						if ( $cat_id == 0) $cat_id = DB_insertId();
						if (!empty($_FILES)) {
							PAYPAL_saveCatImage ($_REQUEST, $_FILES, $cat_id);
						}
						echo COM_refresh($_CONF['site_admin_url'] . '/plugins/paypal/index.php?mode=categories&amp;msg=' . urlencode($msg));
						exit();

						break;
				
				case 'delete' :
				    //todo remove cat and move subcat to cat parent or root
					//delete shipping cost
					DB_delete($_TABLES['paypal_categories'], 'cat_id', $cat_id);
					if (DB_affectedRows('') >= 1) {
						$msg = $LANG_PAYPAL_1['deletion_succes'];
					} else {
						$msg = $LANG_PAYPAL_1['deletion_fail'];
					}
					// delete complete, return to shipping page
					echo COM_refresh($_CONF['site_admin_url'] . '/plugins/paypal/index.php?mode=categories&amp;msg=' . urlencode($msg));
					exit();
					break;
					
				default :
				    if ( !file_exists($_PAY_CONF['path_cat_images']) || !is_writable($_PAY_CONF['path_cat_images']) ) {
						$display .= COM_showMessageText( '>> '. $_PAY_CONF['path_cat_images'] . '<p>' . $LANG_PAYPAL_1['image_not_writable'] . '</p>');
					}

					$display .= '<div style="clear:both;">&nbsp;</div>' . COM_startBlock($LANG_PAYPAL_1['category_heading']);
					$display .= '<p>' . $LANG_PAYPAL_1['you_can'] . '<a href="' . $_CONF['site_url'] . '/admin/plugins/paypal/index.php?mode=categories&amp;op=edit">' 
					. $LANG_PAYPAL_ADMIN['create_category'] . '</a></p>';
					$display .= PAYPAL_listCategories();
					$display .= COM_endBlock();
					break;
			}
		break;
		
		case 'attributes':
			if ( !file_exists($_PAY_CONF['path_at_images']) && function_exists('PAYPALPRO_attributes') || !is_writable($_PAY_CONF['path_at_images']) 
			&& function_exists('PAYPALPRO_attributes') ) {
				$display .= COM_showMessageText( '>> '. $_PAY_CONF['path_at_images'] . '<p>' . $LANG_PAYPAL_1['image_not_writable'] . '</p>');
			}
			if(function_exists('PAYPALPRO_attributes')) $display .= PAYPALPRO_attributes();
			break;
		
		case 'attributetypes':
			if(function_exists('PAYPALPRO_attributeTypes')) $display .= PAYPALPRO_attributeTypes();
			break;
			
		case 'shipping':
		    switch ($_REQUEST['op']) {
				case 'edit_shipper':
				    $shipper = array();
					if (is_numeric($shipper_id)) {
						$sql = "SELECT * FROM {$_TABLES['paypal_shipper_service']} WHERE shipper_service_id = {$shipper_id}";
						$res = DB_query($sql);
						$shipper = DB_fetchArray($res);
					}
					$display .= PAYPAL_getShipperForm($shipper);
				    break;
				case 'save_shipper':
				    if ($_REQUEST['shipper_service_name'] == '' || $_REQUEST['shipper_service_service'] == '') {
						$display .= PAYPAL_getShipperForm($_REQUEST);
						break;
					}
					$shipper_service_name = addslashes($_REQUEST['shipper_service_name']);
					$shipper_service_service = addslashes($_REQUEST['shipper_service_service']);
					$shipper_service_description = addslashes($_REQUEST['shipper_service_description']);
					
					$sql = "shipper_service_name = '{$shipper_service_name}', "
					 . "shipper_service_service = '{$shipper_service_service}', "
					 . "shipper_service_description = '{$shipper_service_description}', "
                     . "shipper_service_exclude_cat = '{$_REQUEST['shipper_service_exclude_cat']}'
					";
					
					if ( $shipper_id != 0) {
						//Edit mode 
						$sql = "UPDATE {$_TABLES['paypal_shipper_service']} SET $sql "
							 . "WHERE shipper_service_id = {$shipper_id}";
					} else {
						//Create mode
						$sql = "INSERT INTO {$_TABLES['paypal_shipper_service']} SET $sql ";
					}
					DB_query($sql);
					if (DB_error()) {
						$msg = $LANG_PAYPAL_1['save_fail'];
					} else {
						$msg = $LANG_PAYPAL_1['save_success'];
					}
					
					echo COM_refresh($_CONF['site_admin_url'] . '/plugins/paypal/index.php?mode=shipping&amp;msg=' . urlencode($msg));
					exit();
					break;
				
				case 'delete_shipper':
				    DB_delete($_TABLES['paypal_shipper_service'], 'shipper_service_id', $shipper_id);
					if (DB_affectedRows('') == 1) {
						$msg = $LANG_PAYPAL_1['deletion_succes'];
					} else {
						$msg = $LANG_PAYPAL_1['deletion_fail'];
					}
					//delete shipping cost
					DB_delete($_TABLES['paypal_shipping_cost'], 'shipping_shipper_id', $shipper_id);
					if (DB_affectedRows('') >= 1) {
						$msg = $LANG_PAYPAL_1['deletion_succes'];
					} else {
						$msg = $LANG_PAYPAL_1['deletion_fail'];
					}
					// delete complete, return to shipping page
					echo COM_refresh($_CONF['site_admin_url'] . '/plugins/paypal/index.php?mode=shipping&amp;msg=' . urlencode($msg));
					exit();
					break;
					
				case 'edit_shipping_to':
				    $shipping_to = array();
					if (is_numeric($shipping_to_id)) {
						$sql = "SELECT * FROM {$_TABLES['paypal_shipping_to']} WHERE shipping_to_id = {$shipping_to_id}";
						$res = DB_query($sql);
						$shipping_to = DB_fetchArray($res);
					}
					$display .= PAYPAL_getShippingToForm($shipping_to);
				    break;
				
                case 'save_shipping_to':
				    if ($_REQUEST['shipping_to_name'] == '') {
						$display .= PAYPAL_getShippingToForm($_REQUEST);
						break;
					}
					if ($_REQUEST['shipping_to_order'] == '') $_REQUEST['shipping_to_order'] = 0;
					$shipping_to_name = addslashes($_REQUEST['shipping_to_name']);
					$sql = "shipping_to_name = '{$shipping_to_name}',
					        shipping_to_order = {$_REQUEST['shipping_to_order']}";
					
					if ( $shipping_to_id != 0) {
						//Edit mode 
						$sql = "UPDATE {$_TABLES['paypal_shipping_to']} SET $sql "
							 . "WHERE shipping_to_id = {$shipping_to_id}";
					} else {
						//Create mode
						$sql = "INSERT INTO {$_TABLES['paypal_shipping_to']} SET $sql ";
					}
					DB_query($sql);
					if (DB_error()) {
						$msg = $LANG_PAYPAL_1['save_fail'];
					} else {
						$msg = $LANG_PAYPAL_1['save_success'];
					}
                    // save complete, return to shipping page
					echo COM_refresh($_CONF['site_admin_url'] . '/plugins/paypal/index.php?mode=shipping&amp;msg=' . urlencode($msg));
					exit();
					break;
								
				case 'delete_shipping_to':
				    DB_delete($_TABLES['paypal_shipping_to'], 'shipping_to_id', $shipping_to_id);
					if (DB_affectedRows('') == 1) {
						$msg = $LANG_PAYPAL_1['deletion_succes'];
					} else {
						$msg = $LANG_PAYPAL_1['deletion_fail'];
					}
					//delete shipping cost
					DB_delete($_TABLES['paypal_shipping_cost'], 'shipping_destination_id', $shipping_to_id);
					if (DB_affectedRows('') >= 1) {
						$msg = $LANG_PAYPAL_1['deletion_succes'];
					} else {
						$msg = $LANG_PAYPAL_1['deletion_fail'];
					}
					// delete complete, return to shipping page
					echo COM_refresh($_CONF['site_admin_url'] . '/plugins/paypal/index.php?mode=shipping&amp;msg=' . urlencode($msg));
					exit();
					break;
					
				case 'edit_shipping_cost':
				    $shipping_cost = array();
					if (is_numeric($shipping_id)) {
						$sql = "SELECT * FROM {$_TABLES['paypal_shipping_cost']} WHERE shipping_id = {$shipping_id}";
						$res = DB_query($sql);
						$shipping_cost = DB_fetchArray($res);
					}
					$display .= PAYPAL_getShippingCostForm($shipping_cost);
				    break;
					
				case 'save_shipping_cost':
				    // shipping_amt can only contain numbers and a decimal
					$shipping_amt = str_replace(",","",$_REQUEST['shipping_amt']);
					$shipping_amt = preg_replace('/[^\d.]/', '', $shipping_amt);
					if ($_REQUEST['shipping_min'] == '') $_REQUEST['shipping_min'] = '0.000';
					$shipping_min = str_replace(",","",$_REQUEST['shipping_min']);
					$shipping_min = preg_replace('/[^\d.]/', '', $shipping_min);
					if ($_REQUEST['shipping_max'] == '') $_REQUEST['shipping_max'] = '0.000';
					$shipping_max = str_replace(",","",$_REQUEST['shipping_max']);
					$shipping_max = preg_replace('/[^\d.]/', '', $shipping_max);
					if ($_REQUEST['shipping_shipper_id'] < 1 || $_REQUEST['shipping_destination_id'] < 1 || $shipping_min >= $shipping_max || 
					$shipping_min == '' || $shipping_max == '') {
						$display .= PAYPAL_getShippingCostForm($_REQUEST);
						break;
					}

					$sql = "shipping_shipper_id = '{$_REQUEST['shipping_shipper_id']}', "
					    . "shipping_min = '{$shipping_min}', "
						. "shipping_max = '{$shipping_max}', "
						. "shipping_destination_id = '{$_REQUEST['shipping_destination_id']}', "
						. "shipping_amt = '{$shipping_amt}'";
					
					if ( $shipping_id != 0) {
						//Edit mode 
						$sql = "UPDATE {$_TABLES['paypal_shipping_cost']} SET $sql "
							 . "WHERE shipping_id = {$shipping_id}";
					} else {
						//Create mode
						$sql = "INSERT INTO {$_TABLES['paypal_shipping_cost']} SET $sql ";
					}
					DB_query($sql);
					if (DB_error()) {
						$msg = $LANG_PAYPAL_1['save_fail'];
					} else {
						$msg = $LANG_PAYPAL_1['save_success'];
					}
					
					echo COM_refresh($_CONF['site_admin_url'] . '/plugins/paypal/index.php?mode=shipping&amp;msg=' . urlencode($msg));
					exit();
					break;
					
				case 'delete_shipping_cost':
				    //delete shipping cost
					DB_delete($_TABLES['paypal_shipping_cost'], 'shipping_id', $shipping_id );
					if (DB_affectedRows('') >= 1) {
						$msg = $LANG_PAYPAL_1['deletion_succes'];
					} else {
						$msg = $LANG_PAYPAL_1['deletion_fail'];
					}
					// delete complete, return to shipping page
					echo COM_refresh($_CONF['site_admin_url'] . '/plugins/paypal/index.php?mode=shipping&amp;msg=' . urlencode($msg));
					exit();
					break;
					
				default:
				    PAYPAL_reorderShippingTo();
    				$display .= PAYPAL_shippingServices();
	        		break;
			}
			break;
			
		default : 
			$display .= '<img src="' . $_PAY_CONF['site_url'] . '/images/paypal.gif" alt="" align="left" hspace="10">' 
			 . $LANG_PAYPAL_1['plugin_doc'] . ' <a href="http://geeklog.fr/wiki/plugins:paypal" target="_blank">'. $LANG_PAYPAL_1['online']
			 . '</a>. '
			 . $LANG_PAYPAL_1['plugin_conf'] . ' <a href="' . $_CONF['site_admin_url'] . '/configuration.php">'. $LANG_PAYPAL_1['online']
			 . '</a>. ';
			 
			$display .= '<div style="clear:both;">&nbsp;</div>' . COM_startBlock($LANG_PAYPAL_1['products_list']);
			
			if(function_exists('PAYPALPRO_attributesMenu')) $attributesmenu = PAYPALPRO_attributesMenu();
			if(function_exists('PAYPALPRO_attributeTypesMenu')) $attributetypesmenu = PAYPALPRO_attributeTypesMenu();
			
			$display .= '<p>' . $LANG_PAYPAL_1['you_can'] . '<a href="' . $_CONF['site_url'] . '/admin/plugins/paypal/product_edit.php?type=product">' 
			. $LANG_PAYPAL_1['create_product'] . '</a> | <a href="' . $_CONF['site_url'] . '/admin/plugins/paypal/product_edit.php?type=subscription">' 
			. $LANG_PAYPAL_1['new_membership'] . ' </a> | <a href="' . $_CONF['site_url'] . '/admin/plugins/paypal/index.php?mode=categories">' 
			. $LANG_PAYPAL_ADMIN['manage_categories'] . '</a>' .  $attributesmenu . $attributetypesmenu . ' | <a href="' . $_CONF['site_url'] .
			'/admin/plugins/paypal/index.php?mode=shipping">' 
			. $LANG_PAYPAL_ADMIN['manage_shipping'] . '</a></p>';
			
			$display .= PAYPAL_listProducts();
			$display .= COM_endBlock();
			
			$display .= PAYPAL_requiredSettings();
			
			break;
		}
	
	if (!function_exists('PAYPALPRO_newSubscription')) {
        $display .= '<p>' . $LANG_PAYPAL_PRO['pro_feature'] . '</p>';
    } 
}

$display .= COM_siteFooter();

COM_output($display);

?>