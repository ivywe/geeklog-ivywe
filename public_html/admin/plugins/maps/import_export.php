<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Maps Plugin 1.2                                                           |
// +---------------------------------------------------------------------------+
// | import_export.php                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
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
$vars = array('mode' => 'alpha',
              'mid' => 'number',
			  'separator_in' => 'text',
			  'separator_out' => 'text',
			  'separator' => 'text',
			  'filename' => 'text',
			  'submit'  => 'alpha',
			  'import_export[address]' => 'number'
			  );

MAPS_filterVars($vars, $_REQUEST);

/**
 * This function creates an import Form
 *
 * @return string HTML string of form
 */
function getImportExportForm() {

    global $_CONF, $_TABLES, $LANG_MAPS_1;
	
    $return = COM_startBlock($LANG_MAPS_1['import_export']);

    $template = COM_newTemplate($_CONF['path'] . 'plugins/maps/templates');
    $template->set_file(array('import_export' => 'import_export_form.thtml'));
	$template->set_var('site_admin_url', $_CONF['site_admin_url']);
	
	$template->set_var('import', $LANG_MAPS_1['import']);
	$template->set_var('import_message', $LANG_MAPS_1['import_message']);
	$template->set_var('export', $LANG_MAPS_1['export']);
	$template->set_var('export_message', $LANG_MAPS_1['export_message']);
	$template->set_var('select_file', $LANG_MAPS_1['select_file']);
	
	//delimiters
	$template->set_var('separator_in', $LANG_MAPS_1['separator']);
	$template->set_var('separator_out', $LANG_MAPS_1['separator']);
	$separator_options = '<option value=";">;</option>' . LB;
	$separator_options .= '<option value="tab">tab</option>' . LB;
	$separator_options .= '<option value=",">,</option>' . LB;
	$template->set_var('separator_options_in', $separator_options);
	$template->set_var('separator_options_out', $separator_options);
	//select map
	$template->set_var('mid_label', $LANG_MAPS_1['name_label']);
	$map_options = MAPS_recurseMaps($marker['mid']);
	$template->set_var('map_options', $map_options);
	//Fields to import or export
	$template->set_var('choose_fields_import', $LANG_MAPS_1['choose_fields_import']);
	$template->set_var('choose_fields_export', $LANG_MAPS_1['choose_fields_export']);
	$template->set_var('checkall', $LANG_MAPS_1['checkall']);
	$valid_fieds = MAPS_getFieldsImportExport();
	foreach ( $valid_fieds as $value ) {
		$fields_selector .= '<input type="checkbox" name="import_export[]" value="' . $value . '" />' . $value . '<br' . XHTML . '>' . LB;
    } 
	$template->set_var('fields_selector', $fields_selector);
	//Form validation
	$template->set_var('ok_button', $LANG_MAPS_1['ok_button']);
	
    $return .= $template->parse('output', 'import_export');

    $return .= COM_endBlock();

    return $return;
}

function MAPS_importCSV ($FILES = '', $map_id, $separator=';', $fields, $valid = false, $filename='') {
    
	global $_CONF, $_TABLES, $LANG24, $LANG_MAPS_1, $_USER;	
	
	if($map_id == '') return MAPS_message('Map ID is missing');
	
	if ( !in_array($separator, array(',','tab',';')) ) {
	    echo COM_refresh($_CONF['site_admin_url'] . '/plugins/maps/import_export.php');
		exit();
	}

	if ($valid == false) {
     	// OK, let's upload csv file
	    require_once($_CONF['path_system'] . 'classes/upload.class.php');
	    $upload = new upload();

	    //Debug with story debug function
	    if (isset ($_CONF['debug_image_upload']) && $_CONF['debug_image_upload']) {
		    $upload->setLogFile ($_CONF['path'] . 'logs/error.log');
		    $upload->setDebug (true);
	    }
	    $upload->setMaxFileUploads (1);

	    $upload->setAllowedMimeTypes (array (
		    	'text/csv'   => '.csv',
		    	'text/comma-separated-values'  => '.csv',
		    	'application/vnd.ms-excel' => '.csv'
		    	));
	
	    if (!$upload->setPath($_CONF['path_data'])) {
		    $output = COM_siteHeader ('menu', $LANG24[30]);
		    $output .= COM_startBlock ($LANG24[30], '', COM_getBlockTemplate ('_msg_block', 'header'));
		    $output .= $upload->printErrors (false);
		    $output .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
		    $output .= COM_siteFooter ();
		    echo $output;
		    exit;
	    }

	    // Set file permissions on file after it gets uploaded (number is in octal)
	    $upload->setPerms('0644');

		$curfile = current($FILES);
		if (!empty($curfile['name'])) {
			$pos = strrpos($curfile['name'],'.') + 1;
			$fextension = substr($curfile['name'], $pos);
			$filename = 'import_markers_' . COM_makesid()  . '.' . $fextension;
		}
		if ($filename == '') return MAPS_message('Houston, we have a problem.');
		$upload->setFileNames($filename);
		reset($FILES);
		$upload->uploadFiles();

		if ($upload->areErrors()) {
			$msg = $upload->printErrors(false);
			return MAPS_message($msg,$LANG24[30]);
		}
		$retval = '<p>'. $LANG_MAPS_1['markers_to_add'] . ' '. DB_getItem($_TABLES['maps_maps'], 'name', "mid=$map_id") . '</p><ul>';
	} else {
	    $retval = '<p>'. $LANG_MAPS_1['markers_added'] . ' '. DB_getItem($_TABLES['maps_maps'], 'name', "mid=$map_id") . '</p><ul>';
	}
		
	//open file and record markers
	$row = 1;
	$marker = array();

	$valid_fields = MAPS_getFieldsImportExport();
	
	if (($handle = fopen($_CONF['path_data'] . $filename, "r")) !== FALSE) {
		if ($separator == 'tab') $separator = "\t";
		$iteration = 0;
		
		while (($field_read = fgetcsv($handle, 0, $separator)) !== FALSE) {
		    
			$iteration ++;
			for ($i = 27; $i > -1; $i=$i-1) {
				if ( $fields[$i] == $valid_fields[$i]) {
					$marker[$i] = $field_read[$i];
				} else {
					if (!isset($marker[$i])) $marker[$i] = '';
					while ($position = current($valid_fields)) {
						if ($position == $fields[$i]) {
							$key = key($valid_fields);
							$marker[$key] = $field_read[$i];
						}
						next($valid_fields);
					}
					reset($valid_fields);
				}
			}
			
			if ($marker[3] == '') {
			    ksort($marker);
			    $retval = '<table style="margin:20px;" border="1">';
				foreach($marker as $key=>$val) { 
				        $retval .= "<tr><td><font size=2>" . $key . "</td><td><font size=2>" . $val . "</td></tr>";
					}
				$retval .= "</table>";
				return MAPS_message($LANG_MAPS_1['name_missing'] . ' | Line: ' . $iteration . $retval);
			}
			
			if ($marker[0] == '' && $marker[1] == '') return MAPS_message($LANG_MAPS_1['need_address']);
			
			if ($valid == false) {
				$retval .= '<li>#' . $iteration . ' Name: ' . $marker[3] . '<br'. XHTML . '>Address: ' . $marker[0] . '<br'. XHTML . '>Lat: ' . $marker[1] . ' | Lng: ' . $marker[2] . '<br'. XHTML . '>Description: ' . $marker[4] . '<br'. XHTML . '>mk_default: ' . $marker[5] . ' | mk_pcolor: ' . $marker[6] . ' | mk_scolor: ' . $marker[7] . ' | mk_label: ' . $marker[8] . ' | mk_label_color: ' . $marker[9] . '<br'. XHTML . '>street: ' . $marker[10] . '<br'. XHTML . '>code: ' . $marker[11] . ' | city: ' . $marker[12] . '<br'. XHTML . '>state: ' . $marker[13] . ' | country: ' . $marker[14] . '<br'. XHTML . '>tel: ' . $marker[15] . ' | fax: ' . $marker[16] . '<br'. XHTML . '>web: ' . $marker[17] . '<br'. XHTML . '>item_1: ' . $marker[18] . ' | item_2: ' . $marker[19] . ' | item_3: ' . $marker[20] . ' | item_4: ' . $marker[21] . ' | item_5: ' . $marker[22] . ' | item_6: ' . $marker[23] . ' | item_7: ' . $marker[24] . ' | item_8: ' . $marker[25] . ' | item_9: ' . $marker[26] . ' | item_10: |' . $marker[27] . '<br'. XHTML . '>Map id: ' . $map_id . ' | ' . 'Owner id: ' . $_USER['uid'] . '<br'. XHTML . '>&nbsp;';
			} else {
				ksort($marker);
				$markers = '';

				foreach($marker as $key => $value) {
					if ($key != 0) $markers .=  ",";
					// prepare strings for insertion
					switch ($key) {
						case '0': //address
						    $val[0] = $value;
				            $type = array(0 => 'text');
					        MAPS_filterVars($type,$val);
							//MAPS_convert_to ( $val[0], $_CONF['default_charset'] );
							$address = $val[0];
							break;
						case '1': //lat
							if ($value == '') {
							$lat = $lng = '';
							$coords = MAPS_getCoords($address, $lat, $lng);
							$value = $lat;
							}
							break;
						case '2': //lng
							if ($value == '') {
							$value = $lng;
							}
							break;
						default :
						    $val[0] = $value;
				            $type = array(0 => 'text');
					        MAPS_filterVars($type,$val); 
							//MAPS_convert_to ( $val[0], $_CONF['default_charset'] );
							$value = $val[0];
							break;
					}
					$markers .= " '" . $value . "'";
				}

				//pause 1/10 second to avoid 
				$nano = time_nanosleep(0, 100000);
				
				if ($nano === true) {
				  $mkid = date( 'YmdHis' ) . $iteration;
				}
				
				$created = $modified = date("Ymd");
				
				$sql = "INSERT INTO {$_TABLES['maps_markers']} (
				  mkid,
				  mid,
				  owner_id,
				  created, modified,
				  address, lat, lng, name, description,  mk_default, mk_pcolor, mk_scolor, mk_label, mk_label_color,
				  street, code, city, state, country, tel, fax, web, item_1, item_2, item_3, item_4, item_5, item_6,
				  item_7, item_8, item_9, item_10) VALUES (
				  $mkid,
				  $map_id,
				  {$_USER['uid']},
				  $created, $modified,
				  $markers)";
				
				$mkid_exists = DB_getItem($_TABLES['maps_markers'], 'mkid', "mkid=$mkid");
				
				DB_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
				
				if ( $mkid_exists == '' and $mkid != '') {
    				DB_query ($sql, 0);
				} else {
				    COM_errorLog('MAPS - Duplicate mkid during import from ' . $filename);
				}
				
				$mkid = '';

				$retval .= '<li>' . stripslashes($marker[3]) . ' | ' . stripslashes($marker[0]);
			}
		}

		fclose($handle);
	}
	$retval .= '</ul>';
	if ($valid == false) {
		$validation = '<p><form name="import" action="' . $_CONF['site_url'] . '/admin/plugins/maps/import_export.php?mode=valid" method="POST">';
		$validation .= '<input type="hidden" name="filename" value="' . $filename . '">';
		$validation .= '<input type="hidden" name="mid" value="' . $map_id . '">';
		$validation .= '<input type="hidden" name="separator_in" value="' . $separator . '">';
		$validation .= '<input type="submit" name="submit" value="' . $LANG_MAPS_1['yes'] . '"> ';
		foreach ($fields as $value) {
			$validation .= '<input type="hidden" name="import_export[]" value="' . $value . '">';
		}
		$validation .= '<input type="submit" name="submit" value="' . $LANG_MAPS_1['no'] . '">';
		$validation .= '</form></p>';
		return MAPS_message($retval) . $validation;
	} else {
	   return $retval;
	}
}

function MAPS_convert_to ( $source, $target_encoding )
    {
    // detect the character encoding of the incoming file
    $encoding = mb_detect_encoding( $source, "auto" );
      
    // escape all of the question marks so we can remove artifacts from
    // the unicode conversion process
    $target = str_replace( "?", "[question_mark]", $source );
      
    // convert the string to the target encoding
    $target = mb_convert_encoding( $target, $target_encoding, $encoding);
      
    // remove any question marks that have been introduced because of illegal characters
    $target = str_replace( "?", "", $target );
      
    // replace the token string "[question_mark]" with the symbol "?"
    $target = str_replace( "[question_mark]", "?", $target );
  
    return $target;
}

function MAPS_exportCSV ($map, $separator = ";", $fields=array()) {
  
	global $_CONF, $_MAPS_CONF, $_TABLES, $LANG_MAPS_1;
	
	$count = count($fields);
	$i = 1;
	$selected_fields = '';
	$valid_fieds = MAPS_getFieldsImportExport();
	
	foreach ( $fields as $value ) {
	    if( in_array( $value,$valid_fieds ) ) {
            $selected_fields .= $value;
	        if ($i < $count) {
	        $selected_fields .= ', ';
		    }
		}
		$i++;
    }
	
    //if ( $selected_fields == '' ) return;
	
	$result = DB_query("SELECT 
							{$selected_fields}  
							FROM {$_TABLES['maps_markers']} WHERE mid={$map}");
	//Check if there is at least 1 marker
	$rows  = DB_numRows($result);
	
	if ($rows < 1 ||  $selected_fields == '') {
		$display .= COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
        $display .= MAPS_admin_menu();
        $display .= MAPS_message($LANG_MAPS_1['no_marker_to_export']);
		$display .= COM_siteFooter(0);
        COM_output($display);
		exit();
	}
	$search = array(',','\'',' ','.','!',':');
	$sitename =  str_replace($search, "_", $_CONF['site_name']);
	// send response headers to the browser
	header( 'Content-Type: text/csv' );
	header( 'Content-Disposition: attachment;filename=map_' . $map . '_' . $sitename . '.csv');
	$fp1 = fopen('php://output', 'w');

	while($row = DB_fetchArray($result, false)) {
		if ($separator == ',') {
		    fputcsv($fp1, $row, ",", '"');
		} else if($separator == 'tab') {
		    fputcsv($fp1, $row, "\t", '"');
		} else {
		    fputcsv($fp1, $row, ";", '"');
		}
	}
	fclose($fp1);
	//header("Refresh: 0;url={$_CONF['site_admin_url']}/plugins/maps/import_export.php");
}

function MAPS_getFieldsImportExport () {
    
	$fields = array('address', 'lat', 'lng', 'name', 'description', 'mk_default', 'mk_pcolor', 'mk_scolor', 'mk_label', 'mk_label_color', 'street', 'code', 'city', 'state', 'country', 'tel', 'fax', 'web', 'item_1', 'item_2', 'item_3', 'item_4', 'item_5', 'item_6', 'item_7', 'item_8', 'item_9', 'item_10');
	
	return $fields;
}

// MAIN


switch ($_REQUEST['mode']) {
    case 'export':
	    MAPS_exportCSV($_REQUEST['mid'],  $_REQUEST['separator_out'], $_REQUEST['import_export']);
	    break;
		
	case 'import':
	    $display .= COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
        $display .= MAPS_admin_menu();
		$display .= MAPS_importCSV ($_FILES, $_REQUEST['mid'], $_REQUEST['separator_in'], $_REQUEST['import_export']);
		$display .= COM_siteFooter(0);
        COM_output($display);
	    break;
	
	case 'valid':
	    $display .= COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
        $display .= MAPS_admin_menu();
		if ( $_REQUEST['submit'] == $LANG_MAPS_1['yes']) {
    		$display .= MAPS_importCSV ('', $_REQUEST['mid'], $_REQUEST['separator_in'], $_REQUEST['import_export'], true, $_REQUEST['filename']);
		} else {
		    $display .= getImportExportForm();
		}
		$display .= COM_siteFooter(0);
        COM_output($display);
	    break;

    default:
	    $display .= COM_siteHeader('menu', $LANG_MAPS_1['plugin_name']);
        $display .= MAPS_admin_menu();
        $display .= getImportExportForm();
		$display .= COM_siteFooter(0);
        COM_output($display);
        break;
}



?>