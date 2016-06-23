<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin - geeklog CMS                                             |
// +--------------------------------------------------------------------------+
// | ipnlog.php                                                               |
// |                                                                          |
// | Allows paypal administrators to view the ipnlog from the database        |
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
 * Allows paypal administrators to view the ipnlog from the database
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
              'txn_id' => 'alpha',
              'id' => 'number');
paypal_filterVars($vars, $_REQUEST);

/**
 * Displays the list of ipn history from the log stored in the database
 *
 */
function PAYPAL_listIPNlog()
{
    global $_CONF, $_TABLES, $LANG_PAYPAL_1, $_USER;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';

	if (DB_count($_TABLES['paypal_ipnlog']) == 0){
	    $retval .= '<p>' . $LANG_PAYPAL_1['ipnlog_empty'] . '</p>';
	}


    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_PAYPAL_1['ID'], 'field' => 'id', 'sort' => true),
        array('text' => $LANG_PAYPAL_1['IP_address'], 'field' => 'ip_addr', 'sort' => true),
        array('text' => $LANG_PAYPAL_1['date_time'], 'field' => 'time', 'sort' => true),
        array('text' => $LANG_PAYPAL_1['verified'], 'field' => 'verified', 'sort' => true),
		array('text' => $LANG_PAYPAL_1['txn_id'], 'field' => 'txn_id', 'sort' => true),
		array('text' => $LANG_PAYPAL_1['payment_status'], 'field' => 'payment_status', 'sort' => true),
        array('text' => $LANG_PAYPAL_1['purchaser'], 'field' => 'custom', 'sort' => true)
    );
    $defsort_arr = array('field' => 'id', 'direction' => 'desc');

    $text_arr = array(
        'has_extras' => true,
        'form_url' => $_CONF['site_admin_url'] . '/plugins/paypal/ipnlog.php'
    );
	
	$sql = "SELECT * FROM {$_TABLES['paypal_ipnlog']} WHERE 1=1";

    $query_arr = array(
        'table'          => 'paypal_ipnlog',
        'sql'            => $sql,
        'query_fields'   => array('id', 'ip_addr', 'time', 'verified', 'txn_id', 'ipn_data'),
        'default_filter' => COM_getPermSQL ('AND', 0, 3)
    );

    $retval .= ADMIN_list('paypal', 'plugin_getListField_paypal_IPNlog',
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
function plugin_getListField_paypal_IPNlog($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $_PAY_CONF, $LANG_PAYPAL_1;
	
	//$A['ipn_data'] = base64_decode($A['ipn_data']);
	
	$out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $A['ipn_data'] ); 
	
	if (!$ipn = unserialize($out)) {
	    $ipn = repairSerializedArray($A['ipn_data']);
	}
	
	if (!is_array($ipn)) {
        $ipn = array();
    }

    switch($fieldname) {
        case "id":
            $retval = $A['id'];
            break;
		case "ip_addr":
            $retval = $A['ip_addr'];
            break;
		case "time":
            $retval = $A['time'];
            break;
		case "verified":
            $retval = $A['verified'];
            break;
		case "txn_id":
            $retval = '<a href="' . $_CONF['site_url'] . '/admin/plugins/paypal/ipnlog.php?view=ipnlog&op=single&txn_id=' . $A['txn_id'] . '">' . $A['txn_id'] . '</a>';
            break;
		case "payment_status":
            $retval = $ipn['payment_status'];
            break;
		case "custom":
            ($ipn['custom'] > 1) ? $retval = '<a href="' . $_CONF['site_url'] . '/users.php?mode=profile&uid=' . $ipn['custom'] . '">' . $ipn['last_name'] . '</a>' . ' (' . $ipn['custom'] . ')' : $retval = $ipn['last_name'] ;
            break;

        default:
            $retval = stripslashes($fieldvalue);
            break;
    }
    return $retval;
}

/**
 * Display a single row from the ipnlog
 *
 * Not only display the row, but beautify the serialized POST data from the transaction
 * for easy viewing.
 *
 * @param int $id Unique identifier for the row of the ipnlog to display
 * @param string $txn_id Unique identifier for the row (txn_id) of the ipnlog to display
 * @return string HTML of the ipnlog row specified by $id
 */
function PAYPAL_ipnlog_single($id, $txn_id) {
    
	global $_TABLES, $LANG_PAYPAL_1, $_CONF, $_PAY_CONF, $_SCRIPTS;
	
	$input_ipn = 0;

    $js = 'jQuery(document).ready(function() {
	    jQuery(".paypal_handle_purchase").live("click", function() {
			var action = jQuery(this).attr("class");
			var ipn = jQuery(this).attr("ipn");
			var string = \'&action=\' + action + \'&ipn=\' + ipn;
			if (confirm(\'' . $LANG_PAYPAL_1['confirm_handle_purchase'] . '\')) {	
				//jQuery(this).parent().parent().fadeOut("slow");
				jQuery.ajax({
					type: "POST",
					url: "' . $_CONF['site_url'] . '/admin/plugins/paypal/ajax.php",
					data: string,
					cache: false,
					async:false,
					success: function(result){
						jQuery(".paypal_handle_purchase").parent().replaceWith("<span style=\"color:red;\">"+result+"</span>");
					}   
				});
			}			
			return false;
		});
	});';
    
	$_SCRIPTS->setJavaScriptLibrary('jquery');
	$_SCRIPTS->setJavaScript($js, true);
	
	// Get ipnlog from database
    if ($id > 0) {
        $sql = "SELECT * FROM {$_TABLES['paypal_ipnlog']} WHERE id = $id";
    } else {
        $sql = "SELECT * FROM {$_TABLES['paypal_ipnlog']} WHERE txn_id = '$txn_id'";
    }
    $res = DB_query($sql);
    $A = DB_fetchArray($res);

	// Start Display
    $display .= COM_startBlock($LANG_PAYPAL_1['ipn_history'] . " (#{$A['id']})");

    // Create ipnlog template
    $ipnlog = new Template($_CONF['path'] . 'plugins/paypal/templates');
    $ipnlog->set_file(array('ipnlog' => 'ipnlog_detail.thtml'));
    $ipnlog->set_var('site_url', $_CONF['site_url']);
    $ipnlog->set_var('IPN_log', $LANG_PAYPAL_1['IPN_log']);
    $ipnlog->set_var('IP_address', $LANG_PAYPAL_1['IP_address']);
    $ipnlog->set_var('date_time', $LANG_PAYPAL_1['date_time']);
    $ipnlog->set_var('Verified', $LANG_PAYPAL_1['verified']);
    $ipnlog->set_var('transaction', $LANG_PAYPAL_1['transaction']);
    $ipnlog->set_var('gross_payment', $LANG_PAYPAL_1['gross_payment']);
    $ipnlog->set_var('payment_status_label', $LANG_PAYPAL_1['payment_status']);
	$ipnlog->set_var('ipn_data', $LANG_PAYPAL_1['ipn_data']);
	$ipnlog->set_var('mc_gross', $A['mc_gross']);
	$ipnlog->set_var('mc_currency', $_PAY_CONF['currency']);
	$ipnlog->set_var('txn_id', $A['txn_id']);
	
	// Allow all serialized data to be available to the template
	$ipn ='';
	if ($A['ipn_data'] != '') {

		//Diagnotic
		PAYPAL_check_serialization( $A['ipn_data'], $errmsg );
		//Serialize fixer
		$out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $A['ipn_data'] ); 
		
		PAYPAL_check_serialization( $out, $errmsg );
        if (!$ipn = unserialize($out)) {
		    $ipn = repairSerializedArray($A['ipn_data']) ;
			$errmsg = 'IPN ' . $A['txn_id'] . ' is not complete';
			$input_ipn = 1;
		}
		
		if (!is_array($ipn)) {
            $ipn = array();
        }
        foreach ($ipn as $name => $value) {
            $ipnlog->set_var($name, $value);
        }
	}

    // Display the specified ipnlog row
    $ipnlog->set_var('id', $A['id']);
    $ipnlog->set_var('ip_addr', $A['ip_addr']);
    $ipnlog->set_var('time', $A['time']);
    if ($A['verified']) {
        $ipnlog->set_var('verified', $LANG_PAYPAL_1['true']);
    } else {
        $txt = $LANG_PAYPAL_1['false'] . ' | Payment status: ' . strtolower($ipn['payment_status']);
		//Update IPN and handle purchase
		if (strtolower($ipn['payment_status']) == ('complete' || 'completed')) $txt .= ' >> <a class="paypal_handle_purchase" ipn="' . $A['txn_id'] . '" href="">' . $LANG_PAYPAL_1['handle_purchase'] . '</a>';
		$ipnlog->set_var('verified', $txt );
    }
    
    // Grab a raw print of the ipn data
    ob_start();
    print_r($ipn);
    $raw = ob_get_contents();
    ob_end_clean();

    // replace \n with <br>
    $raw = nl2br($raw);

    $ipnlog->set_var('raw', stripslashes($raw) );
   
	if ( $errmsg != '') {
	    
		if (DEBUG) COM_errorLog('PAYPAL: Error(s) in IPN ' . $errmsg);
		$errors .= '<p><span style="color:red;">! Error(s) in IPN: '  . $errmsg . '</span></p>';
		
		if ( $input_ipn == 1 ) {
		    //Display textarea for new IPN
			$js2 = 'jQuery(".paypal_ipn_replace").delegate(".paypal_new_ipn","click",function() {
							var action = jQuery(this).attr("class");
							var id = jQuery(this).attr("id");
							var content = jQuery("textarea#ipn_textarea").val();
							content = encodeURIComponent(content);
							var string = \'&action=\' + action + \'&ipn=\' + id + \'&content=\' + content;
											
							jQuery.ajax({
								type: "POST",
								url: "' . $_CONF['site_url'] . '/admin/plugins/paypal/ajax.php",
								data: string,
								cache: false,
								async:false,
								success: function(result){
									jQuery("div#paypal_ipn_replace").replaceWith(result);
									} 
							});
							return false;
						});';
						
			$_SCRIPTS->setJavaScript($js2, true);
						
			$errors .= '<div id="paypal_ipn_replace" class="paypal_ipn_replace">
			    <p>' .  $LANG_PAYPAL_1['replace_ipn'] . '</p>
				<form action="">
				    <textarea id="ipn_textarea" class="paypal_handle_ipn"></textarea>
					<br' . XHTML . '><input class="paypal_new_ipn" id="' . $A['txn_id'] . '" type="submit">
				<form>
				</div>';
		}
		
		$ipnlog->set_var('errormsg', $errors);
		
	} else {
	    
		$ipnlog->set_var('errormsg', '');
	}
	
	 $display .= $ipnlog->parse('output', 'ipnlog');

    $display .= COM_endBlock();
    return $display;

}

function PAYPAL_check_serialization( $string, &$errmsg )
{ 

    $str = 's';
    $array = 'a';
    $integer = 'i';
    $any = '[^}]*?';
    $count = '\d+';
    $content = '"(?:\\\";|.)*?";';
    $open_tag = '\{';
    $close_tag = '\}';
    $parameter = "($str|$array|$integer|$any):($count)" . "(?:[:]($open_tag|$content)|[;])";           
    $preg = "/$parameter|($close_tag)/";
    if( !preg_match_all( $preg, $string, $matches ) )
    {           
        $errmsg = 'not a serialized string';
        return false;
    }   
    $open_arrays = 0;
    foreach( $matches[1] AS $key => $value )
    {
        if( !empty( $value ) && ( $value != $array xor $value != $str xor $value != $integer ) )
        {
            $errmsg = 'undefined datatype';
            return false;
        }
        if( $value == $array )
        {
            $open_arrays++;                               
            if( $matches[3][$key] != '{' )
            {
                $errmsg = 'open tag expected';
                return false;
            }
        }
        if( $value == '' )
        {
            if( $matches[4][$key] != '}' )
            {
                $errmsg = 'close tag expected';
                return false;
            }
            $open_arrays--;
        }
        if( $value == $str )
        {
            $aVar = ltrim( $matches[3][$key], '"' );
            $aVar = rtrim( $aVar, '";' );
            if( strlen( $aVar ) != $matches[2][$key] )
            {
                $errmsg = 'stringlen for string not match but Paypal plugin can handle it';
                return false;
            }
        }
        if( $value == $integer )
        {
            if( !empty( $matches[3][$key] ) )
            {
                $errmsg = 'unexpected data';
                return false;
            }
            if( !is_integer( (int)$matches[2][$key] ) )
            {
                $errmsg = 'integer expected';
                return false;
            }
        }
    }       
    if( $open_arrays != 0 )
    {
        $errmsg = 'wrong setted arrays (' . $open_arrays . ')';
        return false;
    }
    return true;
}

/**************************************************************************
 repairSerializedArray()
 --------------------------------------------------------------------------
 Extract what remains from an unintentionally truncated serialized string
 From http://shauninman.com/archive/2008/01/08/recovering_truncated_php_serialized_arrays
 Example Usage:

	// the native unserialize() function returns false on failure
	$data = @unserialize($serialized); // @ silences the default PHP failure notice
	if ($data === false) // could not unserialize
	{	
		$data = repairSerializedArray($serialized); // salvage what we can
	}
	
 $data contains your original array (or what remains of it).
 **************************************************************************/
function repairSerializedArray($serialized)
{
	$tmp = preg_replace('/^a:\d+:\{/', '', $serialized);
	return repairSerializedArray_R($tmp); // operates on and whittles down the actual argument
}

/**************************************************************************
 repairSerializedArray_R()
 --------------------------------------------------------------------------
 The recursive function that does all of the heavy lifing. Do not call directly.
 **************************************************************************/
function repairSerializedArray_R(&$broken)
{
	// array and string length can be ignored
	// sample serialized data
	// a:0:{}
	// s:4:"four";
	// i:1;
	// b:0;
	// N;
	$data		= array();
	$index		= null;
	$len		= strlen($broken);
	$i			= 0;
	
	while(strlen($broken))
	{
		$i++;
		if ($i > $len)
		{
			break;
		}
		
		if (substr($broken, 0, 1) == '}') // end of array
		{
			$broken = substr($broken, 1);
			return $data;
		}
		else
		{
			$bite = substr($broken, 0, 2);
			switch($bite)
			{	
				case 's:': // key or value
					$re = '/^s:\d+:"([^\"]*)";/';
					if (preg_match($re, $broken, $m))
					{
						if ($index === null)
						{
							$index = $m[1];
						}
						else
						{
							$data[$index] = $m[1];
							$index = null;
						}
						$broken = preg_replace($re, '', $broken);
					}
				break;
				
				case 'i:': // key or value
					$re = '/^i:(\d+);/';
					if (preg_match($re, $broken, $m))
					{
						if ($index === null)
						{
							$index = (int) $m[1];
						}
						else
						{
							$data[$index] = (int) $m[1];
							$index = null;
						}
						$broken = preg_replace($re, '', $broken);
					}
				break;
				
				case 'b:': // value only
					$re = '/^b:[01];/';
					if (preg_match($re, $broken, $m))
					{
						$data[$index] = (bool) $m[1];
						$index = null;
						$broken = preg_replace($re, '', $broken);
					}
				break;
				
				case 'a:': // value only
					$re = '/^a:\d+:\{/';
					if (preg_match($re, $broken, $m))
					{
						$broken			= preg_replace('/^a:\d+:\{/', '', $broken);
						$data[$index]	= repairSerializedArray_R($broken);
						$index = null;
					}
				break;
				
				case 'N;': // value only
					$broken = substr($broken, 2);
					$data[$index]	= null;
					$index = null;
				break;
			}
		}
	}
	
	return $data;
}

//Main

$display = COM_siteHeader('none');
$display .= paypal_admin_menu();

// base output on selected opeation (op)
switch ($_REQUEST['op']) {
    case 'single':
        $display .= PAYPAL_ipnlog_single($_REQUEST['id'], $_REQUEST['txn_id']);
        break;

    case 'all':
    default:
        $display .= COM_startBlock($LANG_PAYPAL_1['IPN_logs']);
        $display .= PAYPAL_listIPNlog();
        $display .= COM_endBlock();
        break;
}

$display .= COM_siteFooter();
echo $display;

?>