<?php
// +--------------------------------------------------------------------------+
// | Maps Plugin 1.1 - geeklog CMS                                            |
// +--------------------------------------------------------------------------+
// | edit_functions.php                                                       |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2011 by the following authors:                             |
// |                                                                          |
// | Authors: ::Ben - cordiste AT free DOT fr                                 |
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

require_once '../../../lib-common.php';

if (!SEC_hasRights('maps.admin')) {
    exit;
}
function MAPS_displayOverlays($mid)
{
    global $_CONF, $_TABLES, $LANG_MAPS_1, $LANG_ADMIN;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_ADMIN['edit'], 'field' => 'edit', 'sort' => false),
		array('text' => $LANG_MAPS_1['overlay_label'], 'field' => 'o_name', 'sort' => false),
		array('text' => $LANG_MAPS_1['image'], 'field' => 'o_image', 'sort' => false)
    );
	
	$sql = "SELECT DISTINCT
	            *
            FROM {$_TABLES['maps_map_overlay']} AS mo
			LEFT JOIN {$_TABLES['maps_overlays']} AS o
			ON mo.mo_oid = o.oid
			WHERE mo.mo_mid={$mid}
			";

    $query_arr = array(
        'sql'            => $sql,
    );

    $retval .= ADMIN_list('maps_overlays', 'MAPS_getListField_maps_displayOverlays',
                          $header_arr, $text_arr, $query_arr, $defsort_arr);

    return '<h2 style="margin-top:10px;">' . $LANG_MAPS_1['overlays_added'] . '</h2>' . $retval;
}

function MAPS_getListField_maps_displayOverlays($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $LANG_ADMIN, $LANG_MAPS_1, $LANG_STATIC, $_TABLES, $_MAPS_CONF;

    switch($fieldname) {
        case "edit":
		    $edit_url = '#';
            $retval = COM_createLink($icon_arr['enabled'], $edit_url,
			        array(
					'class'=>'delete',
					'id'=> $A['mo_id'],
					'mid'=> $A['mo_mid'],
                    'title' => $LANG_MAPS_1['remove_overlay']
                    )
				);
            break;
			
		case "o_image":
		    $retval = '<img src="' . $_MAPS_CONF['site_url'] . '/timthumb.php?src='
			. $_MAPS_CONF['images_overlay_url'] . $A['o_image'] . '&amp;w=75&amp;h=75&amp;q=70&amp;zc=1" alt="" />';
			break;

        default:
            $retval = stripslashes($fieldvalue);
            break;
    }
    return $retval;
}

/*Display overlays on product edit page */
function MAPS_displayOverlaysToAdd($mid)
{
    global $_CONF, $_TABLES, $LANG_MAPS_1, $LANG_ADMIN;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_ADMIN['edit'], 'field' => 'edit', 'sort' => false),
		array('text' => $LANG_MAPS_1['overlay_label'], 'field' => 'o_name', 'sort' => false)
    );
	
	$sql = "SELECT
	            m.mid, o.*, mo.*
            FROM {$_TABLES['maps_maps']} AS m, {$_TABLES['maps_overlays']} AS o
			LEFT JOIN {$_TABLES['maps_map_overlay']} AS mo
			ON (o.oid = mo.mo_oid
			AND mo.mo_mid = $mid)
			WHERE( m.mid = $mid AND mo.mo_id IS NULL)
			";

    $query_arr = array(
        'sql'            => $sql,
    );

    $retval .= ADMIN_list('maps_overlaysToAdd', 'MAPS_getListField_maps_displayOverlaysToAdd',
                          $header_arr, $text_arr, $query_arr, $defsort_arr);
	
	return '<h2 style="margin-top:10px;">' . $LANG_MAPS_1['overlays_to_add'] . '</h2>' . $retval;
}

function MAPS_getListField_maps_displayOverlaysToAdd($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $LANG_MAPS_1, $_MAPS_CONF ;

    switch($fieldname) {
	    case "edit":
		    $edit_url = '#';
            $retval = COM_createLink($icon_arr['disabled'], $edit_url,
			        array(
					'class'=>'add',
					'id'=> $A['oid'],
					'mid'=> $A['mid'],
                    'title' => $LANG_MAPS_1['add_overlay']
                    )
				);
            break;
			
		case "o_name":
		    $overlay_image = $_MAPS_CONF['path_overlay_images'] . $A['o_image'];
		    if (is_file($overlay_image)) {
			    $retval = COM_getTooltip($A['o_name'], '<img src="' . $_MAPS_CONF['site_url'] . '/timthumb.php?src='
				. $_MAPS_CONF['images_overlay_url'] . $A['o_image'] . '&amp;w=200&amp;q=70&amp;zc=1" alt="" />', '', $A['o_name'], $template = 'help');
				
			} else {
				$retval = $A['o_name'];
			}
			break;

        default:
            $retval = stripslashes($fieldvalue);
            break;
    }
    return $retval;
}

//TODO CHECK THIS ONE
/*Display overlays on map detail page */
function MAPS_displayCustomOverlays($mid)
{
    global $_TABLES, $_SCRIPTS, $_PAY_CONF;
	
	$retval = '';
	$sql = "SELECT DISTINCT
	            *
            FROM {$_TABLES['maps_product_overlay']} AS pa
			LEFT JOIN {$_TABLES['paypal_overlays']} AS at
			ON pa.pa_aid = at.at_id
			WHERE pa.pa_pid={$prod_id}
			ORDER BY at.at_order
			";
	//images
    $hsize = $wsize = $_PAY_CONF['overlay_thumbnail_size'];
	$res2 = DB_query($sql);
	while ($A = DB_fetchArray($res2)) {
		if ($A['at_image'] != '' && file_exists($_PAY_CONF['path_at_images'].$A['at_image']) ) {
		$retval .= '<div class="overlay_thumbnail"><a class="lightbox" href="' .  $_PAY_CONF['images_at_url'] . $A['at_image'] . '"><img src="' . $_PAY_CONF['site_url'] . '/timthumb.php?src=' .  $_PAY_CONF['images_at_url'] . $A['at_image'] . '&amp;w=' . $wsize . '&amp;h=' . $hsize . '&amp;zc=1&amp;q=100" alt="' . $A['name'] . '" /></a></div>';
		}
	}
	$retval .= '<div style="clear:both;"></div>';
	
	//select overlays
	$res = DB_query($sql);
	$numRows = DB_numRows($res);
	if ($numRows == 0) return $retval;
	$attr = 1;
	$last_attr = '';
	  
	$_SCRIPTS->setJavaScript($js, true);
	
    while ($A = DB_fetchArray($res)) {
		if ($last_attr != $A['at_type']) {
		    if ($attr != 1) $retval .= '</select>';
			$retval .= ' <select class="overlays_select" name="overlay[' . $A['at_type'] . ']">';	
		}
		$retval .= '<option ref="' . $A['at_code'] . '" id="' . $A['at_image'] . '" value="'. $A['at_id'] . '">' . $A['at_name'] . '</option>';
		if ($numRows == $attr) $retval .= '</select>';
		$last_attr = $A['at_type']; 
		$attr ++;
	}
	
	return $retval;
}
?>
