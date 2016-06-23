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

if (!SEC_hasRights('paypal.admin')) {
    exit;
}

// Incoming variable filter
$vars = array('action' => 'alpha',
              'id' => 'number',
			  'pid' => 'number',
			  'ipn' => 'alpha',
			  'content' => 'html',
			  //'content' => 'text',
			  );
			  
paypal_filterVars($vars, $_POST);

function to_utf8($in)
{
        if (is_array($in)) {
            foreach ($in as $key => $value) {
                $out[to_utf8($key)] = to_utf8($value);
            }
        } elseif(is_string($in)) {
            if(mb_detect_encoding($in) != "UTF-8")
                return utf8_encode($in);
            else
                return $in;
        } else {
            return $in;
        }
        return $out;
} 

switch ($_POST['action']) {
	case 'delete':
		DB_delete($_TABLES['paypal_product_attribute'],'pa_id',$_POST['id']);
		echo '<div id="attributes_actions"><div id="attributes_list">' . PAYPALPRO_displayAttributes($_POST['pid']) . '</div>';
		echo "<script type=\"text/javascript\">jQuery(document).ready(function() {
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
	</script>";
		echo '<div id="attributes_list">' . PAYPALPRO_displayAttributesToAdd($_POST['pid']) . '</div></div>';
		break;
		
	case 'add' :
	    $sql = "pa_pid = '{$_POST['pid']}', "
        	 . "pa_aid = '{$_POST['id']}'
			 ";
	    $sql = "INSERT INTO {$_TABLES['paypal_product_attribute']} SET $sql ";
	    DB_query ($sql, $ignore_errors = 0);
		echo '<div id="attributes_actions"><div id="attributes_list">' . PAYPALPRO_displayAttributes($_POST['pid']) . '</div>';
		echo "<script type=\"text/javascript\">jQuery(document).ready(function() {
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
	</script>";
		echo '<div id="attributes_list">' . PAYPALPRO_displayAttributesToAdd($_POST['pid']) . '</div></div>';
	    break;
		
	case 'paypal_handle_purchase' :
	    
		//Get and check IPN values
		$txn_id = $_POST['ipn'];
		$sql = "SELECT * FROM {$_TABLES['paypal_ipnlog']} WHERE txn_id = '$txn_id'";
        $res = DB_query($sql);
        $A = DB_fetchArray($res);
		
		// Allow all serialized data to be available to the template
		$ipn ='';
		if ($A['ipn_data'] != '') {
			$out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $A['ipn_data'] ); 
			$ipn = unserialize($out);
		}
		
		// If verified = false
		if ( isset($A['verified']) && $A['verified'] != 1 ) {
		
			// Handle purchase
			$i = 1;
			for ( ; ; ) {
                if ($ipn['item_number'.$i] == '') {
					break;
				}
			    $products[$i] = $ipn['item_number'.$i];
				$quantity[$i] = $ipn['quantity'.$i];
				$names[$i] = $ipn['item_name'.$i];
				$prices[$i] = $ipn['mc_gross_'.$i];
				$i++;
			}
			
			$timestamp = strtotime($ipn['payment_date']);
			//Testing for check
			//$timestamp = strtotime($ipn['order_date']);
            $mysql_date = date("Y-m-d H:i:s",$timestamp);
			 
			PAYPAL_handlePurchase( $products, $quantity, $ipn, $names, $prices, 0, 'complete', $ipn['custom'], $ipn['txn_id'], $mysql_date );
			
			// Set verified to true
			DB_query("UPDATE {$_TABLES['paypal_ipnlog']} SET verified = 1 WHERE txn_id = '$txn_id'");
			
			if (DEBUG) COM_errorLog("PAYPAL: handle purchase for old IPN $txn_id done!");
        }
		
		echo $LANG_PAYPAL_1['done'];
		break;
		
	case 'paypal_new_ipn' :
	    
		$content = $_POST['content'];
		$raw_ipn = explode('&', $content);
		
		foreach ($raw_ipn as $keyval) {
				$keyval = explode ('=', $keyval);
				if (count($keyval) == 2) {
					//if (DEBUG) COM_errorLog('PAYPAL: IPN pair: ' . $keyval[0] . ' | ' . $keyval[1]);
					$new_ipn[$keyval[0]] =  addslashes($keyval[1]); 
				}
		}
				
		$ipn = serialize($new_ipn);
		
		$sql = "UPDATE {$_TABLES['paypal_ipnlog']} SET ipn_data = '" .  $ipn . "' WHERE txn_id = '{$_POST['ipn']}'";
		DB_query($sql,1);
		if (DEBUG) COM_errorLog('PAYPAL: IPN updated! SQL: ' . $sql);
		
		echo '<p>' . $LANG_PAYPAL_1['ipn_replaced'] . '</p>';
		break;
}

?>
