<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin - geeklog CMS                                             |
// +--------------------------------------------------------------------------+
// | IPN.class.php                                                            |
// |                                                                          |
// | This file contains the IPN class which extends the BaseIPN class         |
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
 * This file contains the IPN class which extends the BaseIPN class
 *
 * @author Vincent Furia <vinny01 AT users DOT sourceforge DOT net>
 * @copyright Vincent Furia 2005 - 2006
 * @package paypal
 */

/**
 * Get the BaseIPN class, which this class extends
 */
require_once 'BaseIPN.class.php';

/**
 * This class provides an interface to deal with IPN transactions from paypal.
 *
 * The IPN class extneds the BaseIPN class.  Users of the paypal plugin should
 * use this class to customize the handling of incoming IPNs.  The functions
 * most likely (and most usefully) overridden here are handlePurchase,
 * handleSubscription, and handleDonation.
 *
 * @package paypal
 */
class IPN extends BaseIPN {

}

?>