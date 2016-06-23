<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin - geeklog CMS                                              |
// +--------------------------------------------------------------------------+
// | BaseIPN.class.php                                                        |
// |                                                                          |
// | This file contains the BaseIPN class, it provides an interface to        |
// | deal with IPN transactions from paypal                                   |
// +--------------------------------------------------------------------------+
// |                                                                          |
// | Copyright (C) 2013 by the following authors:                             |
// |                                                                          |
// | Authors: Ben     -    ben AT geeklog DOT fr                              |
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

// this file can't be used on its own
if (!defined ('VERSION')) {
    die ('This file can not be used on its own.');
}

/**
 * This file contains the BaseIPN class, it provides an interface to deal with
 * IPN transactions from paypal
 *
 * @author Vincent Furia <vinny01 AT users DOT sourceforge DOT net>
 * @copyright Vincent Furia 2005 - 2006
 * @package paypal
 */
 
/* Paypal constants */
define('PAYPAL_FAILURE_UNKNOWN', 0);
define('PAYPAL_FAILURE_VERIFY', 1);
define('PAYPAL_FAILURE_COMPLETED', 2);
define('PAYPAL_FAILURE_UNIQUE', 3);
define('PAYPAL_FAILURE_EMAIL', 4);
define('PAYPAL_FAILURE_FUNDS', 5);

/**
 * This class provides an interface to deal with IPN transactions from paypal
 *
 * @package paypal
 */
class BaseIPN {

    /**
     * Verify the transaction by check Paypal's servers
     *
     * Validate transaction by posting data back to the paypal webserver.  The response from
     * paypal should include 'VERIFIED' on a line by itself.
     *
     * @param array $in Array containing POST variables of transaction
     * @return boolean true if result successfully validated, false otherwise
     */
    function Verify($in) 
	{
        global $_CONF, $_PAY_CONF;

		// CONFIG: Enable debug mode. This means we'll log requests into 'error.log'
		// Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
		
		if(DEBUG) COM_errorLog("PAYPAL-IPN: Verify starts");

		// Read POST data
		// reading posted data directly from $_POST causes serialization
		// issues with array data in POST. Reading raw POST data from input stream instead.

		$raw_post_data = file_get_contents('php://input');
		$raw_post_array = explode('&', $raw_post_data);
		$myPost = array();
		foreach ($raw_post_array as $keyval) {
				$keyval = explode ('=', $keyval);
				if (count($keyval) == 2)
						$myPost[$keyval[0]] = urldecode($keyval[1]);
		}
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';
		if(function_exists('get_magic_quotes_gpc')) {
				$get_magic_quotes_exists = true;
				if(DEBUG) COM_errorLog("PAYPAL-IPN: get_magic_quotes_gpc exists");
		}
		foreach ($myPost as $key => $value) {
				if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
						$value = urlencode(stripslashes($value));
				} else {
						$value = urlencode($value);
				}
				$req .= "&$key=$value";
		}

		// Post IPN data back to PayPal to validate the IPN data is genuine
		// Without this step anyone can fake IPN data

		$paypal_url = "https://" . $_PAY_CONF['paypalURL'] . "/cgi-bin/webscr";
		if(DEBUG) COM_errorLog("PAYPAL-IPN: $paypal_url");

		$ch = curl_init($paypal_url);
		if ($ch == FALSE) {
				COM_errorLog("PAYPAL-IPN: IPN result -- Curl init failed");
				return FALSE;
		}

		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

		if(DEBUG) {
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
		}

		// CONFIG: Optional proxy configuration
		//curl_setopt($ch, CURLOPT_PROXY, $proxy);
		//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);

		// Set TCP timeout to 30 seconds
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

		// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
		// of the certificate as shown below. Ensure the file is readable by the webserver.
		// This is mandatory for some environments.

		//$cert = __DIR__ . "./cacert.pem";
		//curl_setopt($ch, CURLOPT_CAINFO, $cert);

		$res = curl_exec($ch);
		
		if (curl_errno($ch) != 0) {
		    // cURL error
			if(DEBUG) COM_errorLog("PAYPAL-IPN: Can't connect to PayPal to validate IPN message: " . curl_error($ch));
			curl_close($ch);
			exit;

		} else {
			// Log the entire HTTP response if debug is switched on.
			if(DEBUG) {
				COM_errorLog("PAYPAL-IPN: HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req");
				COM_errorLog("PAYPAL-IPN: HTTP response of validation request: $res");
			}
			
			// Inspect IPN 

			if (strpos($res, "VERIFIED") !== false) {

				$verified = true;
				
				if(DEBUG) COM_errorLog("PAYPAL-IPN: Paypal response is Verified - IPN:".  $req);
				
			} else if (strpos($res, "INVALID") !== false) {
				$verified = false;
				// log for manual investigation
				// Add business logic here which deals with invalid IPN messages
				if(DEBUG) COM_errorLog("PAYPAL-IPN: Paypal response is Invalid - IPN: " . $req);
			} else {
			   if(DEBUG) COM_errorLog("PAYPAL-IPN: Paypal headers are: " . $headers);
			   if(DEBUG) COM_errorLog("PAYPAL-IPN: Paypal response is: " . $res);
			}

			curl_close($ch);
		}
        
		if(DEBUG) COM_errorLog("PAYPAL-IPN: Verify finish");
		
		return $verified;
    }

    /**
     * Log an IPN
     *
     * Logs the incoming IPN (serialized) along with the time it arrived, the originating IP address
     * and whether or not it has been verified (caller specified).  Also inserts the txn_id
     * seperately for look-up purposes.
	 *
	 * If IPN is trucated, check your settings https://www.paypal.com/ie/cgi-bin/webscr?cmd=_profile-language-encoding
     *
     * @param array $in POST variables of IPN transaction
     * @param boolean $verified true if transaction has been verified, false otherwise
     */
    function Log($in, $verified = false) {
        
		global $_SERVER, $_TABLES, $_CONF;

        require_once $_CONF['path'] . 'plugins/paypal/classes/ForceUTF8.class.php';
		
		if(DEBUG) COM_errorLog("PAYPAL-IPN: Log start");
		
		// Change $verified into format for database
        if ($verified) {
            $verified = 1;
        } else {
            $verified = 0;
        }

        //Check if IPN already exists
		$id = DB_getItem($_TABLES['paypal_ipnlog'], 'id', "txn_id='{$in['txn_id']}'");
		
		if ( $id == '') {
		    // Alert admin of a possible charset issue
			if ( $in['charset'] != '' && strtolower($in['charset']) != strtolower($_CONF['default_charset']) )  {
				COM_errorLog('PAYPAL: IPN Charset possible issue. Please check your settings https://www.paypal.com/ie/cgi-bin/webscr?cmd=_profile-language-encoding. Paypal charset is set to ' 
				. $in['charset'] .  ' but your default charset is set to ' . $_CONF['default_charset']);
			}
		
			// Log to database
			$input_arr = array();
			//grabs the $_POST variables and adds slashes
			foreach ($in as $key => $input_arr) {
				//$input_arr = utf8_decode($input_arr);
				$in[$key] = addslashes($input_arr);
			}
			$sql = "INSERT INTO {$_TABLES['paypal_ipnlog']} SET ip_addr = '{$_SERVER['REMOTE_ADDR']}', "
				 . "time = NOW(), verified = $verified, txn_id = '{$in['txn_id']}', "
				 . "ipn_data = '" . serialize($in) . '\'';
			
			DB_query($sql);
			
			if(DEBUG) COM_errorLog('PAYPAL-IPN: IPN recorded');
			return DB_insertId();
		} else {
		    if(DEBUG) COM_errorLog("PAYPAL-IPN: Log | IPN already exists in DB");
		    return $id;
		}
    }

    /**
     * Returns true if the the supplied email address is amoung allowed receiver addresses
     *
     * @param string $receiver_email Email to verify
     * @return boolean true if valid, false otherwise
     */
    function isValidEmail($receiver_email, $business) {
        
		global $_PAY_CONF;

        if ( ($receiver_email == $_PAY_CONF['receiverEmailAddr']) || $business == $_PAY_CONF['receiverEmailAddr'] ) {
		    if(DEBUG) COM_errorLog('PAYPAL-IPN: Email ok');
		    return true;
		} else {
		    if(DEBUG) COM_errorLog('PAYPAL-IPN: Email not ok: receiver= "' . $receiver_email . '" | business= "' . $business . '" Your email set in the config is "' . $_PAY_CONF['receiverEmailAddr'] . '"');
		    return false;
		}
    }

    /**
     * Checks to make sure that the transaction id is unique to prevent double counting.
     *
     * @param string $txn_id transaction id to verify
     * @return boolean true if unique, false otherwise
     */
    function isUniqueTxnId($txn_id) {
        global $_TABLES;

        // Count purchases with txn_id, if > 0
        $count = DB_count($_TABLES['paypal_purchases'], 'txn_id', $txn_id);
        if ($count > 0) {
		    if(DEBUG) COM_errorLog('PAYPAL-IPN: Txn is not unique');
            return false;
        } else {
		    if(DEBUG) COM_errorLog('PAYPAL-IPN: Txn is unique');
            return true;
        }
    }

    /**
     * Confirms that payment status is complete (not 'denied', 'failed', 'pending', etc.)
     *
     * @param string $payment_status payment status to verify
     * @return boolean true if complete, false otherwise
     */
    function isStatusCompleted($payment_status) {
        return ($payment_status == 'Completed');
    }

    /**
     * Checks if payment status is reversed or refunded (ie some sort of cancelation)
     *
     * @param string $payment_status payment status to check
     * @return boolean true if payment status is reversed or refunded, false otherwise
     */
    function isStatusReversed($payment_status) {
        return ($payment_status == 'Reversed' || $payment_status == 'Refunded');
    }

    /**
     * Checks to make sure provided funds are sufficient to cover the cost of the purchased item(s)
     *
     * @param array $ids ids of items to check
     * @param array $quantity number ordered (per item)
     * @param real $payment_gross total payment made in current transaction
     * @param string $currency Currency of funds in payment_gross
     * @return boolean true if funds are sufficient, false otherwise
     */
    function isSufficientFunds($ids, $quantity, $payment_gross, $currency) {
        global $_CONF, $_PAY_CONF, $_TABLES;
		
		$real_ids = PAYPAL_realId($ids);
		
		if ( empty($ids) || empty($real_ids) ) {
		    if(DEBUG)COM_errorLog('PAYPAL-IPN: ids of the items is empty: ' . $ids . ' | ' . $real_ids);
			return false;
		}
		

        // Check currency
        if (!(isset($_PAY_CONF['currency']) && strcasecmp($_PAY_CONF['currency'], $currency) == 0)) {
		    if(DEBUG) COM_errorLog('PAYPAL-IPN: Currency is not ok');
            return false;
        }

        // Create a list of ids from $real_ids
        (is_array($real_ids)) ? $idlist = "'" . implode("','", $real_ids) . "'" : $idlist = "'" . $real_ids . "'";

        // Create/execute query string
		if ($idlist == '') {
		   if(DEBUG) COM_errorLog('PAYPAL-IPN: List of ids items is empty');
		    return false;
		}
        $sql = "SELECT * FROM {$_TABLES['paypal_products']} WHERE id in ($idlist)";
        $res = DB_query($sql);

        // Create a price lookup table
		//TODO add attribute price
        while ($A = DB_fetchArray($res)) {
		    $price = $A['price'];
			if ($A['discount_a'] != '' && $A['discount_a'] != 0) {
				$price = number_format($A['price'] - $A['discount_a'], 2, '.', '');
			}
			if ($A['discount_p'] != '' && $A['discount_p'] != 0) {
				$price = number_format($A['price'] - ($A['price'] * ($A['discount_p']/100)), 2, '.', '');
			}
            $cost[$A['id']] = $price;
        }

        // calculate the total purchase price
        $total = 0;
        for ($i = 0; $i < count($ids); $i++) {
            $total += $cost[$ids[$i]] * $quantity[$i];
        }

        // Compare total price to gross payment
        if ($total <= $payment_gross) {
		    if(DEBUG) COM_errorLog('PAYPAL-IPN: Funds are sufficient');
            return true;
        } else {
		    if(DEBUG) COM_errorLog('PAYPAL-IPN: Funds are not sufficient');
            //Todo send a mail to admin
            return false;
        }
    }

    /**
     * Process an incoming IPN transaction
     *
     * Do the following:
     * <ol>
     * <li>verify IPN</li>
     * <li>Log IPN</li>
     * <li>Check that transaction is complete</li>
     * <li>Check that transaction is unique</li>
     * <li>Check for valid receiver email address</li>
     * <li>Process IPN</li>
     * </ol>
     *
     * @param array $in POST variables of transaction
     * @return boolean true if processing valid and completed, false otherwise
     */
    function Process($in) {
	
	    global $_PAY_CONF;
		
	    if(DEBUG) COM_errorLog('PAYPAL-IPN: IPN received');
		
        if (!$this->Verify($in)) {
            $logId = $this->Log($in, false);
            $this->handleFailure(PAYPAL_FAILURE_VERIFY, "PAYPAL-IPN: IPN($logId) Verification failed");
            return false;
        } else {
            $logId = $this->Log($in, true);
        }

        if (!$this->isStatusCompleted($in['payment_status'])) {
            // Not logged since this probably isn't an error
            // $this->handleFailure(PAYPAL_FAILURE_COMPLETED, "PAYPAL-IPN: IPN($logId) Status not complete");
            return false;
        }

        if (!$this->isUniqueTxnId($in['txn_id'])) {
            $this->handleFailure(PAYPAL_FAILURE_UNIQUE, "($logId) Non-unique transaction id");
            return false;
        }

        if (!$this->isValidEmail($in['receiver_email'], $in['business'])) {
            $this->handleFailure(PAYPAL_FAILURE_EMAIL, "PAYPAL-IPN: IPN($logId) Invalid receiver email address");
            return false;
        }

        if(DEBUG) COM_errorLog('PAYPAL-IPN: Transaction type ' . $in['txn_type']);
		
		switch ($in['txn_type']) {
            // buy now, donate, smart logos
            case 'web_accept':  //usually buy now
            case 'send_money':  //usually donation/send money
                // Process Buy Now, ignore donations
                if (!empty($in['item_number'])) {
                    $ids = array($in['item_number']);
                    $quantity = array($in['quantity']);
					$name = array($in['item_name']);
                    if (isset($in['settle_amount'])) {
                        $payment_gross = $in['mc_gross'] * $in['exchange_rate'];
                        $currency      = $in['settle_currency'];
                    } else {
                        $payment_gross = $in['mc_gross'];
                        $currency      = $in['mc_currency'];
                    }
                    if ($this->isSufficientFunds($ids, $quantity, $payment_gross, $currency)) {
                        $this->handlePurchase($ids, $quantity, $in, $name);
                    } else {
                        $this->handleFailure(PAYPAL_FAILURE_FUNDS, "($logId) Insufficient funds for purchase");
                        return false;
                    }
                } else {
                    $this->handleDonation($in);
                }
                break;

            // shopping cart
            case 'cart':
                $ids = array();
                $quantity = array();
                $names = array();
                
				if ( $in['num_cart_items'] > 0 ) {
					for ($i = 1; $i <= $in['num_cart_items']; $i++) {
						if(DEBUG) COM_errorLog('PAYPAL-IPN: Cart case item: ' . $in["item_number$i"]);
						$ids[] = $in["item_number$i"];
						$quantity[] = $in["quantity$i"];
						$names[] = $in["item_name$i"];
					}
				} else {
				    if(DEBUG) COM_errorLog('PAYPAL-IPN: Cart case item: ' . $in['item_number1']);
					$ids[] = $in['item_number1'];
                    $quantity[] = $in['quantity1'];
					$name[] = $in['item_name1'];
				}
				
                if (isset($in['settle_amount'])) {
                    $payment_gross = $in['mc_gross'] * $in['exchange_rate'];
                    $currency      = $in['settle_currency'];
                } else {
                    $payment_gross = $in['mc_gross'];
                    $currency      = $in['mc_currency'];
                }
                
				if ($this->isSufficientFunds($ids, $quantity, $payment_gross, $currency)) {
                    $this->handlePurchase($ids, $quantity, $in, $names);
                } else {
                    $this->handleFailure(PAYPAL_FAILURE_FUNDS, "($logId) Insufficient/incorrect funds for purchase");
                    return false;
                }
                break;

            // other, unknown, unsupported
            default:
                $this->handleFailure(PAYPAL_FAILURE_UNKNOWN, "($logId) Unknown transaction type");
                return false;
        }

        COM_errorLog("PAYPAL-IPN: purchases success",1);
        return true;
    }

    /**
     * Add a record of the purchase to the DB
     *
     * @param array $products Product Id(s) of Product(s) purchased
     * @param array $quantity Quantity of products purchases
     * @param array $paypal_data IPN POST variables
     * @todo implemente physical item vs. download, reflected in 'status'
     */
    function handlePurchase($products, $quantity, $paypal_data, $product_name) {
        global $_TABLES, $_CONF, $_PAY_CONF, $LANG_PAYPAL_EMAIL;

        // initialize file and names arrays
        $files = array();
        $names = array();
		$oldids = $products;
		$products = PAYPAL_realId($products);

        // for each item purchased, record purchase in purchase table
        for ($i = 0; $i < count($products); $i++) {
		    if(DEBUG) COM_errorLog('PAYPAL-IPN: Product id:' . $products[$i]);
            // grab relevant product data from product table to insert into purchase table.
            $sql = "SELECT * FROM {$_TABLES['paypal_products']} "
                 . "WHERE id = '{$products[$i]}'";
            $res = DB_query($sql);
            $A = DB_fetchArray($res);
			if(DEBUG) COM_errorLog('PAYPAL-IPN: Type: ' . $A['type']);
            if ($A['download'] > 0) {
                $files[] = $_PAY_CONF['download_path'] . $A['file'];
            }
			
			//TODO + attribute name
			
			// Set quantity to one if empty
			if($quantity[$i] =='') $quantity[$i] = 1;
			
            $names[] = $product_name[$i] . ' x ' . $quantity[$i] ;

            // Do record anonymous users in purchase table
			//TODO record product name + product_id with attribute
            if ( is_numeric((int)$paypal_data['custom']) && (int)$paypal_data['custom'] > 0 ) {
                // Add the purchase to the paypal purchase table
                $sql = "INSERT INTO {$_TABLES['paypal_purchases']} SET product_id = '{$products[$i]}', "
                     . "quantity = '{$quantity[$i]}', user_id = '{$paypal_data['custom']}', "
                     . "txn_id = '{$paypal_data['txn_id']}', "
                     . 'purchase_date = NOW(), status = \'complete\'';

                /**
                 * @todo implemente physical item vs. download, reflected in 'status'
                 */
                // if physical item (aka, must be shipped) status = 'pending', otherwise 'complete'
                //if ( $physical == 1 ) {
                //    $sql .= ", status = 'pending'";
                //} else {
                //    $sql .= ", status = 'complete'";
                //}

                // add an expiration date if appropriate
                if (is_numeric($A['expiration']) && $A['type'] == 'product') {
                    $sql .= ", expiration = DATE_ADD(NOW(), INTERVAL {$A['expiration']} DAY)";
                }
				if(DEBUG) COM_errorLog('PAYPAL-IPN: ' . $sql);
                DB_query($sql);
				if(DEBUG) COM_errorLog('PAYPAL-IPN: Purchase recorded');
            }
			// stock movement
			$stock_id = PAYPAL_getStockId($oldids[$i]);
			$qty = $quantity[$i];
			PAYPAL_stockMovement ($stock_id, $oldids[$i], -$qty);
        }
        
		// Send the purchaser a confirmation email (if set to do so in config)
        if ( ( is_numeric((int)$paypal_data['custom']) && (int)$paypal_data['custom'] != 1 &&
               $_PAY_CONF['purchase_email_user'] ) ||
             ( (!is_numeric($paypal_data['custom']) || (int)$paypal_data['custom'] == 1) &&
               $_PAY_CONF['purchase_email_anon'] )) {
            
			// setup templates
            $message = new Template($_CONF['path'] . 'plugins/paypal/templates');
            $message->set_file(array('subject' => 'purchase_email_subject.txt',
                                     'message' => 'purchase_email_message.txt' ));
            // site variables
            $message->set_var('site_url', $_CONF['site_url']);
            $message->set_var('site_name', $_CONF['site_name']);

			//Email subject
			$message->set_var('purchase_receipt', $LANG_PAYPAL_EMAIL['purchase_receipt']);

            // list of product names
			for ($i = 0; $i < count($products); $i++) {
			$li_products .= '<li>' . $names[$i];
			}
			$message->set_var('products', $li_products);
			
			//Email messages
			$message->set_var('thank_you', $LANG_PAYPAL_EMAIL['thank_you']);
			$message->set_var('thanks', $LANG_PAYPAL_EMAIL['thanks']);

            // paypal details
            $message->set_var('payment_gross', $paypal_data['payment_gross']);
            $message->set_var('tax', $paypal_data['tax']);
            $message->set_var('shipping', $paypal_data['mc_shipping']);
            $message->set_var('handling', $paypal_data['mc_handling']);
            $message->set_var('payment_date', $paypal_data['payment_date']);
            $message->set_var('payer_email', $paypal_data['payer_email']);
            $message->set_var('first_name', $paypal_data['first_name']);
            $message->set_var('last_name', $paypal_data['last_name']);
			
			$subject = trim($message->parse('output', 'subject'));

            // if specified to mail attachment, do so, otherwise skip attachment
            if ( (( is_numeric((int)$paypal_data['custom']) && (int)$paypal_data['custom'] != 1 &&
                    $_PAY_CONF['purchase_email_user_attach'] ) ||
                  ( (!is_numeric((int)$paypal_data['custom']) || (int)$paypal_data['custom'] == 1) &&
                    $_PAY_CONF['purchase_email_anon_attach'] )) &&
                  count($files) > 0  ) {
				$message->set_var('attached_files', $LANG_PAYPAL_EMAIL['attached_files']);
				$text = $message->parse('output', 'message');
                paypal_mailAttachment($paypal_data['payer_email'], $subject, $text, $files,
                                      $_PAY_CONF['receiverEmailAddr']);
            } else {
			    if (count($files) > 0  ) {
			        $message->set_var('attached_files', $LANG_PAYPAL_EMAIL['download_files']);
				} else {
				    $message->set_var('attached_files', '');
				}
				$text = $message->parse('output', 'message');
                COM_mail($paypal_data['payer_email'], $subject, $text,
                         $_PAY_CONF['receiverEmailAddr'], true);
            }
			if(DEBUG) COM_errorLog('PAYPAL-IPN: Email was sent');
        }
		//Send email to receiver
        COM_mail($_PAY_CONF['receiverEmailAddr'], $subject, $subject . ' >> ' . $text, $_PAY_CONF['receiverEmailAddr'], true);

		//Subscription
		if ($A['type'] == 'subscription') {
		    //add subscription to db
		    PAYPAL_addsubscription ($A, $paypal_data);
			if(DEBUG) COM_errorLog('PAYPAL-IPN: Subscription recorded');
		    //add  user to group
		    if ($A['add_to_group'] > 1 && (int)$paypal_data['custom'] > 1) {
    			PAYPAL_addToGroup($A['add_to_group'], $paypal_data['custom']);
				if(DEBUG) COM_errorLog( 'PAYPAL-IPN: User with UID ' . $paypal_data['custom'] . ' added to group ID ' . $A['add_to_group'] );
			}
				
		}
    }

    /**
     * Not yet implemented
     *
     * @todo Implement handleSubscription
     */
    function handleSubscription($subscription, $paypal_data) {
		//Record subscription in DB
		
		return true;
    }

    /**
     * Not yet implemented
     *
     * @todo Implement handleDonation
     */
    function handleDonation() {
		$this->handleFailure(PAYPAL_FAILURE_UNKNOWN, "Donation not handled");
    }

    /**
     * Handle what to do in the event of a purchase/IPN failure
     *
     * This method does some basic failure handling.  For anything more
     * advanced it is recommend you override this method in IPN.class.php.
     *
     * @param int $type Type of failure that occurred
     * @param string $msg Failure message
     */
    function handleFailure($type = PAYPAL_FAILURE_UNKNOWN, $msg = '') {
        // Log the failure to geeklog's error log
        COM_errorLog('PAYPAL-IPN: ' . $msg,1);
    }
	
}


?>