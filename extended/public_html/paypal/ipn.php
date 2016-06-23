<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin - geeklog CMS                                             |
// +--------------------------------------------------------------------------+
// | ipn.php                                                                  |
// |                                                                          |
// | page that accepts IPN transaction information from the paypal servers.   |
// | A link to this page needs to be associated with your paypal business     |
// | account.                                                                 |
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
 * page that accepts IPN transaction information from the paypal servers.  A link to
 * this page needs to be associated with your paypal business account.
 *
 * @author Vincent Furia <vinny01 AT users DOT sourceforge DOT net>
 * @copyright Vincent Furia 2005 - 2006
 * @package paypal
 */

/**
 * Require geeklog
 */
require_once('../lib-common.php');

/**
 * Get needed paypal classes
 */

require_once($_CONF['path'] . 'plugins/paypal/classes/IPN.class.php');

// Process IPN request
$ipn = new IPN();
$ipn->Process($_POST);


// Finished (this isn't necessary...but heck...why not?)
echo "Thanks";

?>