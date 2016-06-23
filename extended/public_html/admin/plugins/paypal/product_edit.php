<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin 1.2 - geeklog CMS                                          |
// +--------------------------------------------------------------------------+
// | product_edit.php                                                         |
// |                                                                          |
// | Displays a form for the editing products                                 |
// |                                                                          |
// | Allows for the altering and deletion of existing products as well as the |
// | creation of new products                                                 |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009 - 2010 by the following authors:                      |
// |                                                                          |
// | Authors: ::Ben - cordiste AT free DOT fr                                 |
// +--------------------------------------------------------------------------+
// | Created with the Geeklog Plugin Toolkit.                                 |
// +--------------------------------------------------------------------------+
// | Based on the original paypal Plugin                                      |
// | Copyright (C) 2005 - 2006 by the following authors:                      |
// |                                                                          |
// | Vincent Furia <vinny01 AT users DOT sourceforge DOT net>                 |   
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
 * Displays a form for the editing products
 *
 * Allows for the altering and deletion of existing products as well as the
 * creation of new products
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

// Incoming variable filter
$vars = array('op' => 'alpha',
              'id' => 'number',
			  'type' => 'alpha',
			  'item_id' => 'alpha',
              'name' => 'text',
              'category' => 'number',
              'short_description' => 'text',
              'description' => 'html',
              'price' => 'text',
			  'price_ref' => 'text',
			  'discount_a' => 'text',
			  'discount_p' => 'text',
			  'logged' => 'number',
			  'hidden' => 'number',
			  'active' => 'number',
			  'customisable' => 'number',
              'product_type' => 'number',
			  'shipping_type' => 'number',
			  'weight' => 'text',
              'file' => 'alpha',
              'expiration' => 'number',
			  'show_in_blocks' => 'number',
			  'duration' => 'number',
              'duration_type' => 'text',
              'add_to_group' => 'number',
			  'owner_id' => 'number',
			  'group_id' => 'number',
              'perm_owner[0]' => 'number',
              'perm_owner[1]' => 'number',
              'perm_group[0]' => 'number',
              'perm_group[1]' => 'number',
              'perm_members[0]' => 'number',
              'perm_anon[0]' => 'number'
			  );
paypal_filterVars($vars, $_REQUEST);



/**
 * This function creates a product Form
 *
 * Creates a Form for a product using the supplied defaults (if specified).
 *
 * @param array $product array of values describing a proudct
 * @return string HTML string of product form
 */
function PAYPAL_getProductForm($product = array(), $type = 'product') {

    global $_CONF, $_PAY_CONF, $LANG_PAYPAL_1, $LANG_PAYPAL_ADMIN, $LANG_PAYPAL_PRO, $_TABLES, $LANG24, $LANG_ADMIN, $LANG_ACCESS, $_USER, $_GROUPS, $_SCRIPTS;
	
	//PHP 5.4 set all $product[key] 
	PAYPAL_setAllKeys($product, array('type', 'name', 'id', 'category', 'cat_id', 'short_description', 'description', 'item_id', 'price', 'price_ref', 'discount_a', 'discount_p', 'logged', 'hidden', 'active', 'show_in_blocks', 'customisable', 'product_type', 'file', 'weight', 'shipping_type', 'expiration', 'duration', 'duration_type', 'add_to_group', 'perm_owner', 'owner_id', 'group_id', 'perm_group', 'perm_members', 'perm_anon'));
	
	$js = 'jQuery(function () {
        var tabContainers = jQuery(\'div.tabs > div\');
        
        jQuery(\'div.tabs ul.tabNavigation a\').click(function () {
            tabContainers.hide().filter(this.hash).show();
            
            jQuery(\'div.tabs ul.tabNavigation a\').removeClass(\'selected\');
            jQuery(this).addClass(\'selected\');
            
            return false;
        }).filter(\':first\').click();
		
    });' . LB;

	$js .= "jQuery(document).ready(function() {
		jQuery('#load').hide();
		});

		jQuery(function() {
			jQuery(\".delete\").click(function() {
				jQuery('#load').show();
				var id = jQuery(this).attr(\"id\");
				var pid = jQuery(this).attr(\"pid\");
				var aid = jQuery(this).attr(\"aid\");
				var action = jQuery(this).attr(\"class\");
				var string = 'id='+ id + '&action=' + action + '&pid=' + pid;
					
				jQuery.ajax({
					type: \"POST\",
					url: \"ajax.php\",
					data: string,
					cache: false,
					async:false,
					success: function(result){
						jQuery(\"#attributes_actions\").replaceWith(result);
					}   
				});
				jQuery('#load').hide();
				return false;
			});
			jQuery(\".add\").click(function() {
				jQuery('#load').show();
				var id = jQuery(this).attr(\"id\");
				var pid = jQuery(this).attr(\"pid\");
				var aid = jQuery(this).attr(\"aid\");
				var action = jQuery(this).attr(\"class\");
				var string = 'id='+ id + '&action=' + action + '&pid=' + pid;
					
				jQuery.ajax({
					type: \"POST\",
					url: \"ajax.php\",
					data: string,
					cache: false,
					async:false,
					success: function(result){
						jQuery(\"#attributes_actions\").replaceWith(result);
					}   
				});
				jQuery('#load').hide();
				return false;
			});
		});
	"  . LB;
	
	$js .= "
	function PP_changeProdType(value)
	{
	  switch(value) {
	  case '0':
		document.getElementById('type_download').style.display = 'none';
		document.getElementById('type_physical').style.display = '';
		break;
	  case '1':
		document.getElementById('type_download').style.display = '';
		document.getElementById('type_physical').style.display = 'none';
		break;
	  case '2':
		document.getElementById('type_download').style.display = 'none';
		document.getElementById('type_physical').style.display = 'none';
		break;
	  }
	}
	
	jQuery(window).load(function(){
		PP_changeProdType('{prod_type_ini}');
	});

	" . LB;
	
	$_SCRIPTS->setJavaScriptLibrary('jquery');
	$_SCRIPTS->setJavaScript($js, true);
	
	//Validate product type
	if ($_REQUEST['type'] == '' && $product['type'] == '') $type = 'product';
	foreach ($_PAY_CONF['types'] as $item => $value) {
	    $types[$item] = $item; 
	}
	if (!in_array($type, $types)) {
	    return $LANG_PAYPAL_1['wrong_type'];
	}

    //Display form
	($product['name'] == '') ? $display = COM_startBlock($LANG_PAYPAL_1['create_new_product']) : $display = COM_startBlock($LANG_PAYPAL_1['edit_label'] . ' ' . $product['name']);

    $template = new Template($_CONF['path'] . 'plugins/paypal/templates');
    $template->set_file(array('product' => 'product_form.thtml'));
    $template->set_var('site_url', $_CONF['site_url']);
	$template->set_var('xhtml', XHTML);
	if ($_CONF['advanced_editor'] == true) {
	    $template->set_var('adveditor','<script type="text/javascript">
        // Setup editor path for FCKeditor JS Functions
        geeklogEditorBasePath = "' . $_CONF['site_url'] . '/fckeditor/" ;
        window.onload = function() {
        	var oFCKeditor1 = new FCKeditor( \'description\' ) ;
            oFCKeditor1.Config[\'CustomConfigurationsPath\'] = geeklogEditorBaseUrl + \'/fckeditor/myconfig.js\';
            oFCKeditor1.BasePath = geeklogEditorBasePath;
            oFCKeditor1.ToolbarSet = \'Default\';
            oFCKeditor1.Height = 300 ;
            oFCKeditor1.ReplaceTextarea() ;
        };
    </script>');
	} else {
	    $template->set_var('adveditor','');
	}
	
	//Product type
	if ($product['type'] != '') {
        $template->set_var('product_type', '<input type="hidden" name="type" value="' . $product['type'] .'" />');
    } else {
        $template->set_var('product_type', '<input type="hidden" name="type" value="' . $type . '" />');
    }
	
	//Product infos
	$template->set_var('informations', $LANG_PAYPAL_1['product_informations']);
	if ($_REQUEST['type'] == 'subscription' || $product['type'] == 'subscription') {
	    $template->set_var('informations', $LANG_PAYPAL_1['membership_informations']);
	}
    $template->set_var('name_label', $LANG_PAYPAL_1['name_label']);
    $template->set_var('category_label', $LANG_PAYPAL_1['category_label']);
	$template->set_var('currency', $_PAY_CONF['currency']);

    if (is_numeric($product['id'])) {
        $template->set_var('id', '<input type="hidden" name="id" value="' . $product['id'] .'" />');
    } else {
        $template->set_var('id', '');
    }
    $template->set_var('name', $product['name']);
	
	//catogory
    $template->set_var('category', $product['category']);
	
	//categorie
	$categories = '';
    $categories .= '<option value="0">' . $LANG_PAYPAL_ADMIN['choose_category'] . '</option>';
	$categories .= PAYPAL_adOptionList($_TABLES['paypal_categories'], 'cat_id,cat_name', $product['cat_id'], 'cat_name', 'enabled=1');
	$template->set_var('categories', $categories);
	
	//Descriptions
	$template->set_var('short_description_label', $LANG_PAYPAL_1['short_description_label']);
	$template->set_var('short_description',
                       strip_tags($product['short_description']));
	$template->set_var('description_label', $LANG_PAYPAL_1['description_label']);
	$template->set_var('description', $product['description']);
	//item_id
	$template->set_var('item_id_label', $LANG_PAYPAL_1['item_id_label']);
	$template->set_var('item_id', $product['item_id']);
	//Price
	$template->set_var('price_label', $LANG_PAYPAL_1['price_label']);
    if (empty($product['price'])) {
        $template->set_var('price', 0);
    } else {
        $template->set_var('price', number_format($product['price'], $_CONF['decimal_count']));
    }
	$template->set_var('price_edit', $LANG_PAYPAL_1['price_edit']);
	//Price_ref
	$template->set_var('price_ref_label', $LANG_PAYPAL_1['price_ref_label']);
    if (empty($product['price_ref'])) {
        $template->set_var('price_ref', 0);
    } else {
        $template->set_var('price_ref', number_format($product['price_ref'], $_CONF['decimal_count']));
    }
	$template->set_var('price_ref_edit', $LANG_PAYPAL_1['price_ref_edit']);
	//Discount
	$template->set_var('discount_legend', $LANG_PAYPAL_1['discount_legend']);
	$template->set_var('discount_label', $LANG_PAYPAL_1['discount_label']);
	$template->set_var('discount_a_label', $LANG_PAYPAL_1['discount_a_label']);
	$template->set_var('discount_p_label', $LANG_PAYPAL_1['discount_p_label']);
    if (empty($product['discount_a'])) {
        $template->set_var('discount_a', 0);
    } else {
        $template->set_var('discount_a', number_format($product['discount_a'], $_CONF['decimal_count']));
    }
	if (empty($product['discount_p'])) {
        $template->set_var('discount_p', 0);
    } else {
        $template->set_var('discount_p', number_format($product['discount_p'], $_CONF['decimal_count']));
    }

	//access & display
	$template->set_var('access_display', $LANG_PAYPAL_1['access_display']);
	//logged
	$template->set_var('logged_to_purchase', $LANG_PAYPAL_1['logged_to_purchase']);
	if ($type == 'subscription' || $product['type'] == 'subscription') {
	    $template->set_var('logged_yes', ' selected');
        $template->set_var('logged_no', ' disabled="disabled"');
	} else if ($product['logged'] == 1) {
        $template->set_var('logged_yes', ' selected');
        $template->set_var('logged_no', '');
    } else {
        $template->set_var('logged_yes', '');
        $template->set_var('logged_no', ' selected');
    }
	//hidden
	$template->set_var('hidden', $LANG_PAYPAL_1['hidden_product']);
	if ($product['hidden'] == 1) {
        $template->set_var('hidden_yes', ' selected');
        $template->set_var('hidden_no', '');
    } else {
        $template->set_var('hidden_yes', '');
        $template->set_var('hidden_no', ' selected');
    }
	//active
    $template->set_var('active', $LANG_PAYPAL_1['active_product']);
	(!isset($product['active'])) ? $product['active'] = 1 : NULL;
	if ($product['active'] == 1) {
        $template->set_var('active_yes', ' selected');
        $template->set_var('active_no', '');
    } else {
        $template->set_var('active_yes', '');
        $template->set_var('active_no', ' selected');
    }

	//Show in blocks
	$template->set_var('show_in_blocks', $LANG_PAYPAL_1['show_in_blocks']);
	(!isset($product['show_in_blocks'])) ? $product['show_in_blocks'] = 1 : NULL;
	if ($product['show_in_blocks'] == 1) {
        $template->set_var('show_in_blocks_yes', ' selected');
        $template->set_var('show_in_blocks_no', '');
    } else {
        $template->set_var('show_in_blocks_yes', '');
        $template->set_var('show_in_blocks_no', ' selected');
    }
	
	//customisable
	$template->set_var('customisation', $LANG_PAYPAL_ADMIN['customisation']);
	$template->set_var('customisable', $LANG_PAYPAL_ADMIN['customisable']);
	if (isset($product['customisable']) && $product['customisable'] == 1) {
        $template->set_var('customisable_yes', ' selected');
        $template->set_var('customisable_no', '');
    } else {
        $template->set_var('customisable_yes', '');
        $template->set_var('customisable_no', ' selected');
    }
	
	if(function_exists('PAYPALPRO_displayAttributes') && $product['id'] != '') {
	    $template->set_var('attributes', PAYPALPRO_displayAttributes($product['id']));
		$template->set_var('add_attributes', PAYPALPRO_displayAttributesToAdd($product['id']));
	} else if(function_exists('PAYPALPRO_displayAttributes')) {
	   $template->set_var('attributes', '');
	   $template->set_var('add_attributes', 'Add attributes');
	}  else {
	   $template->set_var('attributes', '<p>' . $LANG_PAYPAL_PRO['pro_feature'] . '</p>');
	   $template->set_var('add_attributes', '');
	}

	//images
	$template->set_var('lang_images', $LANG_PAYPAL_1['product_images']);
	$fileinputs = '';
    $saved_images = '';
    if ($_PAY_CONF['max_images_per_products'] > 0) {
	    if ($product['id'] != '') {
            $icount = DB_count($_TABLES['paypal_images'],'pi_pid', $product['id']);
            if ($icount > 0) {
                $result_products = DB_query("SELECT * FROM {$_TABLES['paypal_images']} WHERE pi_pid = '". $product['id'] ."'");
                for ($z = 1; $z <= $icount; $z++) {
                    $I = DB_fetchArray($result_products);
                    $saved_images .= '<div><p>' . $z . ') '
					    . '<a class="lightbox" href="' . $_PAY_CONF['images_url'] . $I['pi_filename'] . '"><img align="top" class="lightbox" src="'. $_PAY_CONF['site_url'] . '/timthumb.php?src='. $_PAY_CONF['images_url'] . $I['pi_filename'] . '&amp;w=75&amp;h=75&amp;zc=1&amp;q=100" alt="' . $I['pi_filename'] . '" /></a>'
                        . '&nbsp;&nbsp;&nbsp;' . $LANG_ADMIN['delete']
                        . ': <input type="checkbox" name="delete[' .$I['pi_img_num']
                        . ']"' . XHTML . '><br' . XHTML . '></p></div>';
                }
            }
		}

        $newallowed = $_PAY_CONF['max_images_per_products'] - $icount;
        for ($z = $icount + 1; $z <= $_PAY_CONF['max_images_per_products']; $z++) {
            $fileinputs .= $z . ') <input type="file" dir="ltr" name="file'
                        . $z . '"' . XHTML . '> ';
            if ($z < $_PAY_CONF['max_images_per_products']) {
                $fileinputs .= '<br' . XHTML . '>';
            }
        }
    }
    $template->set_var('saved_images', $saved_images);
    $template->set_var('image_form_elements', $fileinputs);
	
	//delivery info
    ($type != 'product') ? $template->set_var('display_product', 'display:none;') : $template->set_var('display_product', '');
	$template->set_var('delivery_info_label', $LANG_PAYPAL_ADMIN['delivery_info_label']);
	$template->set_var('prod_type', $LANG_PAYPAL_ADMIN['prod_type']);
	$template->set_var('prod_type_ini', $product['product_type']);
	
	$template->set_block('product', 'ProdTypeRadio', 'ProdType');
        foreach ($LANG_PAYPAL_ADMIN['prod_types'] as $value=>$text) {
            $template->set_var(array(
                'type_val'  => $value,
                'type_txt'  => $text,
                'type_sel'  => $product['product_type']  == $value ? 'checked="checked"' : '' 
            ));
            $template->parse('ProdType', 'ProdTypeRadio', true);
        }
	//files
	$template->set_var('filename_label', $LANG_PAYPAL_1['filename_label']);
	$files = '';
    $files_folder = @opendir($_PAY_CONF['download_path']);
    if (!$files_folder) {
		$template->set_var('select_file', $LANG_PAYPAL_1['no_download_folder']);
		$template->set_var('file_selection', '');
    } else {
        while ($file = readdir($files_folder)) {
            if ($file == '.' || $file == '..') continue;
            $sel = $file == $product['file'] ? ' selected="selected" ' : '';
            $files .= "<option value=\"$file\" $sel>$file</option>\n";
        }
        closedir($files_folder);
		$template->set_var('select_file', $LANG_PAYPAL_1['select_file']);
		$template->set_var('file_selection', $files);
    }
	
	$template->set_var('upload_new', $LANG_PAYPAL_1['upload_new']);
	$template->set_var('expiration_label', $LANG_PAYPAL_1['expiration_label']);
	
	//weight
	$template->set_var('weight_label', $LANG_PAYPAL_ADMIN['weight']);
	if ($product['weight'] == '') $product['weight'] = '0.000';
	$template->set_var('weight', $product['weight']);
	$template->set_var('per_item', $LANG_PAYPAL_ADMIN['per_item']);
	
	//shipping
	$template->set_var('shipping_type', $LANG_PAYPAL_ADMIN['shipping_type']);
	$template->set_var('shipping_type_ini', $product['shipping_type']);
	$shipping_options = '';


	if ($product['shipping_type'] == 0) {
		$selected0 = ' selected="selected"';
		$selected1 = '';
	} else {
		$selected1 = ' selected="selected"';
		$selected0 = '';
	}
	$shipping_options .= '<option value="0"'. $selected0 . '>' . $LANG_PAYPAL_ADMIN['shipping_options'][0] . '</option>';
    $shipping_options .= '<option value="1"' . $selected1 . '>' . $LANG_PAYPAL_ADMIN['shipping_options'][1] . '</option>';

	$template->set_var('shipping_options', $shipping_options);	

	$template->set_var('yes', $LANG_PAYPAL_1['yes']);
	$template->set_var('no', $LANG_PAYPAL_1['no']);
	$template->set_var('save_button', $LANG_PAYPAL_1['save_button']);
	$template->set_var('delete_button', $LANG_PAYPAL_1['delete_button']);
	$template->set_var('ok_button', $LANG_PAYPAL_1['ok_button']);
	$template->set_var('required_field', $LANG_PAYPAL_1['required_field']);
	
	if ($product['product_type'] == 1) {
        $template->set_var('download_yes', ' selected');
        $template->set_var('download_no', '');
    } else {
        $template->set_var('download_yes', '');
        $template->set_var('download_no', ' selected');
    }

    $template->set_var('file', $product['file']);
    $template->set_var('expiration', $product['expiration']);
	
	//Subscription
	($type != 'subscription') ? $template->set_var('display_subscription', 'display:none;') : $template->set_var('display_subscription', '');
	$template->set_var('subscription_product_label', $LANG_PAYPAL_1['subscription_label']);
	$template->set_var('duration_label', $LANG_PAYPAL_1['duration_label']);
	$template->set_var('duration', $product['duration']);
	($product['duration_type'] == 'day') ? $template->set_var('sel_day', ' selected="selected"') : '';
	$template->set_var('day', $LANG_PAYPAL_1['day']);
	($product['duration_type'] == 'week') ? $template->set_var('sel_week', ' selected="selected"') : '';
	$template->set_var('week', $LANG_PAYPAL_1['week']);
	($product['duration_type'] == 'month') ? $template->set_var('sel_month', ' selected="selected"') : '';
	$template->set_var('month', $LANG_PAYPAL_1['month']);
	($product['duration_type'] == 'year') ? $template->set_var('sel_year', ' selected="selected"') : '';
	$template->set_var('year', $LANG_PAYPAL_1['year']);

	$template->set_var('add_to_group_label', $LANG_PAYPAL_1['add_to_group_label']);
	$template->set_var('add_to_group_options', COM_optionList( $_TABLES['groups'], 'grp_id,grp_name', $product['add_to_group'], 1));
	
    // Permissions
	if ($product['perm_owner'] == '') {
	  SEC_setDefaultPermissions($product, $_PAY_CONF['default_permissions']);
	}
	$template->set_var('lang_accessrights', $LANG_ACCESS['accessrights']);
    $template->set_var('lang_owner', $LANG_ACCESS['owner']);
	if ($product['owner_id'] == '') {
	$product['owner_id'] = $_USER['uid'];
	}
    $ownername = COM_getDisplayName($product['owner_id']);
    $template->set_var('owner_username', DB_getItem($_TABLES['users'],
                          'username',"uid = {$product['owner_id']}"));
    $template->set_var('owner_name', $ownername);
    $template->set_var('owner', $ownername);
    $template->set_var('owner_id', $product['owner_id']);
	if ($product['group_id']  == '') {
        $product['group_id'] = $_GROUPS['Paypal Admin'];
    }
    $template->set_var('lang_group', $LANG_ACCESS['group']);
	//Todo make group = paypal.admin
    $access = 3;
    $template->set_var('group_dropdown', SEC_getGroupDropdown($product['group_id'], $access));
    $template->set_var('permissions_editor', SEC_getPermissionsHTML($product['perm_owner'],$product['perm_group'],$product['perm_members'],$product['perm_anon']));
    $template->set_var('lang_permissions', $LANG_ACCESS['permissions']);
    $template->set_var('lang_perm_key', $LANG_ACCESS['permissionskey']);
    $template->set_var('permissions_msg', $LANG_ACCESS['permmsg']);
    $template->set_var('lang_permissions_msg', $LANG_ACCESS['permmsg']);

    $display .= $template->parse('output', 'product');

    $display .= COM_endBlock();
    return $display;
}

function PAYPAL_saveImage ($product, $FILES, $pid) {
    global $_CONF, $_PAY_CONF, $_TABLES, $LANG24;
	
    $args = &$product;

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

	// Delete any images if needed
	if (array_key_exists('delete', $args)) {
		$delete = count($args['delete']);
		for ($i = 1; $i <= $delete; $i++) {
			$pi_filename = DB_getItem ($_TABLES['paypal_images'],'pi_filename', 'pi_pid = ' . $pid . ' AND pi_img_num = ' . key($args['delete']));
			PAYPAL_deleteImage ($pi_filename);
			DB_query ("DELETE FROM {$_TABLES['paypal_images']} WHERE pi_pid = ". $pid . " AND pi_img_num = " . key($args['delete']));
			next($args['delete']);
		}
	}

	// OK, let's upload any pictures with the product
	if (DB_count($_TABLES['paypal_images'], 'pi_pid', $pid) > 0) {
		$index_start = DB_getItem($_TABLES['paypal_images'],'max(pi_img_num)',"pi_pid = '". $pid. "'") + 1;
	} else {
		$index_start = 1;
	}

	if (count($FILES) > 0 AND $_PAY_CONF['max_images_per_products'] > 0) {
		require_once($_CONF['path_system'] . 'classes/upload.class.php');
		$upload = new upload();

		//Debug with story debug function
		if (isset ($_CONF['debug_image_upload']) && $_CONF['debug_image_upload']) {
			$upload->setLogFile ($_CONF['path'] . 'logs/error.log');
			$upload->setDebug (true);
		}
		$upload->setMaxFileUploads ($_PAY_CONF['max_images_per_products']);
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
			$upload->setAutomaticResize(false);
			$upload->keepOriginalImage (true);

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
		
		if (!$upload->setPath($_PAY_CONF['path_images'])) {
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
		$filenames = array();
		$end_index = $index_start + $upload->numFiles() - 1;
		for ($z = $index_start; $z <= $end_index; $z++) {
			$curfile = current($FILES);
			if (!empty($curfile['name'])) {
				$pos = strrpos($curfile['name'],'.') + 1;
				$fextension = substr($curfile['name'], $pos);
				$filenames[] = $pid . '_' . $z . '.' . $fextension;
			}
			next($FILES);
		}
		$upload->setFileNames($filenames);
		reset($FILES);
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

		reset($filenames);
		for ($z = $index_start; $z <= $end_index; $z++) {
			DB_query("INSERT INTO {$_TABLES['paypal_images']} (pi_pid, pi_img_num, pi_filename) VALUES ('" . $pid . "', $z, '" . current($filenames) . "')");
			next($filenames);
		}
	}
	return true;
}

/**
* Delete one image from a product
*
* @param    string  $image  file name of the image (without the path)
*
*/
function PAYPAL_deleteImage ($image)
{
    global $_PAY_CONF;

    if (empty ($image)) {
        return;
    }
	
	$pi_url = $_PAY_CONF['path_images'] . $image;
			if (!@unlink ($pi_url)) {
                // log the problem but don't abort the script
                echo COM_errorLog ('Unable to remove the following image from the product: ' . $image);
            }
    // Todo remove image from cache
}

/**
 * main section
 */
switch ($_REQUEST['op']) {
    case 'delete':
	    DB_delete($_TABLES['paypal_products'], 'id', $_REQUEST['id']);
		if (DB_affectedRows('') == 1) {
			$msg = $LANG_PAYPAL_1['deletion_succes'];
		} else {
			$msg = $LANG_PAYPAL_1['deletion_fail'];
		}
		// delete complete, return to product list
        echo COM_refresh($_CONF['site_url'] . "/admin/plugins/paypal/index.php?msg=$msg");
        exit();
        break;

    case 'save':
        if ( empty($_REQUEST['name']) || empty($_REQUEST['short_description']) ||
                empty($_REQUEST['price']) || !is_numeric($_REQUEST['product_type']) ||
				($_REQUEST['type'] == 'subscription' &&  ($_REQUEST['duration'] == '' || $_REQUEST['duration'] < 1)) ) {
            $display = COM_startBlock($LANG_PAYPAL_1['error']);
            $display .= $LANG_PAYPAL_1['missing_field'];
            $display .= COM_endBlock();
            $display .= PAYPAL_getProductForm($_REQUEST, $_REQUEST['type']);
            break;
        }

        // prepare strings for insertion
		$create_mode = 0;
        $_REQUEST['name'] = addslashes($_REQUEST['name']);
        $_REQUEST['category'] = addslashes($_REQUEST['category']);
        $_REQUEST['short_description'] = addslashes($_REQUEST['short_description']);
        $_REQUEST['description'] = addslashes($_REQUEST['description']);
		if ( $_REQUEST['item_id'] == '' ) $_REQUEST['item_id'] = $_REQUEST['id'];
		if ( $_REQUEST['item_id'] == '' ) $create_mode = 1;
		
        // price can only contain numbers and a decimal
        $price = str_replace(",","",$_REQUEST['price']);
        $price = preg_replace('/[^\d.]/', '', $price);
		$price_ref = str_replace(",","",$_REQUEST['price_ref']);
        $price_ref = preg_replace('/[^\d.]/', '', $price_ref);
		$discount_a = str_replace(",","",$_REQUEST['discount_a']);
        $discount_a = preg_replace('/[^\d.]/', '', $discount_a);
		if ($discount_a != '' && $discount_a != 0) {
		    $discount_p = 0;
		} else {
		    $discount_p = str_replace(",","",$_REQUEST['discount_p']);
            $discount_p = preg_replace('/[^\d.]/', '', $discount_p);
		}
		$weight = str_replace(",","",$_REQUEST['weight']);
        $weight = preg_replace('/[^\d.]/', '', $weight);
		if ($weight == '') $weight = '0.000';

	    // Convert array values to numeric permission values
        if (is_array($_REQUEST['perm_owner']) OR is_array($_REQUEST['perm_group']) OR is_array($_REQUEST['perm_members']) OR is_array($_REQUEST['perm_anon'])) {
            list($_REQUEST['perm_owner'],$_REQUEST['perm_group'],$_REQUEST['perm_members'],$_REQUEST['perm_anon']) = SEC_getPermissionValues($_REQUEST['perm_owner'],$_REQUEST['perm_group'],$_REQUEST['perm_members'],$_REQUEST['perm_anon']);
       }

        // 0 indicates no expiraton and must be stored as NULL in the DB
        if ( $_REQUEST['expiration'] == 0 ) {
            $_REQUEST['expiration'] = 'NULL';
        }
        $sql = "name = '{$_REQUEST['name']}', "
        	 . "cat_id = '{$_REQUEST['category']}', "
             . "short_description = '{$_REQUEST['short_description']}', "
             . "description = '{$_REQUEST['description']}', "
             . "price = '{$price}', "
			 . "price_ref = '{$price_ref}', "
			 . "discount_a = '{$discount_a}', "
			 . "discount_p = '{$discount_p}', "
			 . "customisable = {$_REQUEST['customisable']}, "
             . "product_type = {$_REQUEST['product_type']}, "
			 . "weight = {$weight}, "
	         . "shipping_type = '{$_REQUEST['shipping_type']}', "
			 . "logged = {$_REQUEST['logged']}, "
			 . "hidden = {$_REQUEST['hidden']}, "
			 . "active = {$_REQUEST['active']}, "
             . "file = '{$_REQUEST['file']}', "
             . "expiration = {$_REQUEST['expiration']}, "
			 . "type = '{$_REQUEST['type']}', "
			 . "item_id = '{$_REQUEST['item_id']}', "
			 . "show_in_blocks = {$_REQUEST['show_in_blocks']}, "
			 . "duration = {$_REQUEST['duration']}, "
			 . "duration_type = '{$_REQUEST['duration_type']}', "
			 . "add_to_group = {$_REQUEST['add_to_group']}, "
			 . "owner_id = '{$_REQUEST['owner_id']}', "
			 . "group_id = '{$_REQUEST['group_id']}', "
			 . "perm_owner = '{$_REQUEST['perm_owner']}', "
			 . "perm_group = '{$_REQUEST['perm_group']}', "
			 . "perm_members = '{$_REQUEST['perm_members']}', "
			 . "perm_anon = '{$_REQUEST['perm_anon']}'
			 ";
        if (!empty($_REQUEST['id'])) {
		    //Update mode
            $sql = "UPDATE {$_TABLES['paypal_products']} SET $sql "
                 . "WHERE id = {$_REQUEST['id']}";
        } else {
		    //create mode
		    $created = date("YmdHis");
            $sql .= ", created='{$created}'";			
            $sql = "INSERT INTO {$_TABLES['paypal_products']} SET $sql ";
        }
        DB_query($sql);
        if (DB_error()) {
            $msg = $LANG_PAYPAL_1['save_fail'];
        } else {
            $msg = $LANG_PAYPAL_1['save_success'];
        }
		
		//Process images and update item_id
		if (empty($_REQUEST['id'])) {
	        $last_pid = DB_insertId();
			//Update item_id
			$sql = "UPDATE {$_TABLES['paypal_products']} SET item_id=$last_pid "
                 . "WHERE id = {$last_pid}";
			if ($create_mode == 1) DB_query($sql);
	    } else {
		    $last_pid = $_REQUEST['id'];
		}
		PAYPAL_saveImage ($_REQUEST, $_FILES, $last_pid);
		
        // save complete, return to product list
        echo COM_refresh($_CONF['site_url'] . '/admin/plugins/paypal/index.php?msg=' . urlencode($msg));
        exit();
        break;

    /* this case is currently not used... future expansion? */
    case 'preview':
        $display = PAYPAL_getProductForm($_REQUEST);
        break;

    case 'edit':
        // Get the product to edit and display the form
        if (is_numeric($_REQUEST['id'])) {
            $sql = "SELECT * FROM {$_TABLES['paypal_products']} WHERE id = {$_REQUEST['id']}";
            $res = DB_query($sql);
            $A = DB_fetchArray($res);
            $display = PAYPAL_getProductForm($A, $A['type']);
        } else {
            echo COM_refresh($_CONF['site_url']);
        }
        break;

    case 'new':
    default:
        $display = PAYPAL_getProductForm(array(), $_REQUEST['type']);
        break;
}

$display = COM_siteHeader('none') . paypal_admin_menu() . $display;
$display .= COM_siteFooter();

COM_output($display);

?>