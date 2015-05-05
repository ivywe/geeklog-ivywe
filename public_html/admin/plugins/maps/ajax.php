<?php
// +--------------------------------------------------------------------------+
// | Maps Plugin 1.1 - geeklog CMS                                            |
// +--------------------------------------------------------------------------+
// | ajax.php                                                                 |
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
require_once 'edit_functions.php';

if (!SEC_hasRights('maps.admin')) {
    exit;
}

// Incoming variable filter
$vars = array('action' => 'alpha',
              'id' => 'number',
			  'mid' => 'number'
			  );
maps_filterVars($vars, $_POST);

switch ($_POST['action']) {
	case 'delete':
		DB_delete($_TABLES['maps_map_overlay'],'mo_id',$_POST['id']);
		echo '<div id="overlays_actions"><div id="overlays_list">' . MAPS_displayOverlays($_POST['mid']) . '</div>';
		echo "<script type=\"text/javascript\">jQuery(document).ready(function() {
		jQuery('#load').hide();
		});

		jQuery(function() {
			jQuery(\".delete\").click(function() {
				jQuery('#load').show();
				var id = jQuery(this).attr(\"id\");
				var mid = jQuery(this).attr(\"mid\");
				var oid = jQuery(this).attr(\"oid\");
				var action = jQuery(this).attr(\"class\");
				var string = 'id='+ id + '&action=' + action + '&mid=' + mid;
					
				jQuery.ajax({
					type: \"POST\",
					url: \"ajax.php\",
					data: string,
					cache: false,
					async:false,
					success: function(result){
						jQuery(\"#overlays_actions\").replaceWith(result);
					}   
				});
				jQuery('#load').hide();
				return false;
			});
			jQuery(\".add\").click(function() {
				jQuery('#load').show();
				var id = jQuery(this).attr(\"id\");
				var mid = jQuery(this).attr(\"mid\");
				var oid = jQuery(this).attr(\"oid\");
				var action = jQuery(this).attr(\"class\");
				var string = 'id='+ id + '&action=' + action + '&mid=' + mid;
					
				jQuery.ajax({
					type: \"POST\",
					url: \"ajax.php\",
					data: string,
					cache: false,
					async:false,
					success: function(result){
						jQuery(\"#overlays_actions\").replaceWith(result);
					}   
				});
				jQuery('#load').hide();
				return false;
			});
		});
	</script>";
		echo '<div id="overlays_list">' . MAPS_displayOverlaysToAdd($_POST['mid']) . '</div>';
		break;
		
	case 'add' :
	    $sql = "mo_mid = '{$_POST['mid']}', "
        	 . "mo_oid = '{$_POST['id']}'
			 ";
	    $sql = "INSERT INTO {$_TABLES['maps_map_overlay']} SET $sql ";
	    DB_query ($sql, $ignore_errors = 0);
		echo '<div id="overlays_actions"><div id="overlays_list">' . MAPS_displayOverlays($_POST['mid']) . '</div>';
		echo "<script type=\"text/javascript\">jQuery(document).ready(function() {
		jQuery('#load').hide();
		});

		jQuery(function() {
			jQuery(\".delete\").click(function() {
				jQuery('#load').show();
				var id = jQuery(this).attr(\"id\");
				var mid = jQuery(this).attr(\"mid\");
				var oid = jQuery(this).attr(\"oid\");
				var action = jQuery(this).attr(\"class\");
				var string = 'id='+ id + '&action=' + action + '&mid=' + mid;
					
				jQuery.ajax({
					type: \"POST\",
					url: \"ajax.php\",
					data: string,
					cache: false,
					async:false,
					success: function(result){
						jQuery(\"#overlays_actions\").replaceWith(result);
					}   
				});
				jQuery('#load').hide();
				return false;
			});
			jQuery(\".add\").click(function() {
				jQuery('#load').show();
				var id = jQuery(this).attr(\"id\");
				var mid = jQuery(this).attr(\"mid\");
				var oid = jQuery(this).attr(\"oid\");
				var action = jQuery(this).attr(\"class\");
				var string = 'id='+ id + '&action=' + action + '&mid=' + mid;
					
				jQuery.ajax({
					type: \"POST\",
					url: \"ajax.php\",
					data: string,
					cache: false,
					async:false,
					success: function(result){
						jQuery(\"#overlays_actions\").replaceWith(result);
					}   
				});
				jQuery('#load').hide();
				return false;
			});
		});
	</script>";
		echo '<div id="overlays_list">' . MAPS_displayOverlaysToAdd($_POST['mid']) . '</div>';
	    break;
}

?>
