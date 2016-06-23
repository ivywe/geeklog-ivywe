<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Paypal Plugin 1.4                                                         |
// +---------------------------------------------------------------------------+
// | jcart-javascript.php                                                      |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben - cordiste AT free DOT fr                                  |
// +---------------------------------------------------------------------------+
// | Based on JCART v1.1                                                       |
// |                                                                           |
// | Copyright (C) 2010 by the following authors:                              |
// | JCART v1.1  http://conceptlogic.com/jcart/                                |
// |                                                                           |   
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


// CONTINUE THE SESSION
session_start();

// OUTPUT PHP FILE AS JAVASCRIPT
header('content-type:application/x-javascript');

// PREVENT CACHING
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

/**
 * require core geeklog code
 */
require_once '../../lib-common.php';

?>
// WHEN THE DOCUMENT IS READY
jQuery(function(){

	/**********************************************************************
	Tooltips based on Wayfarer Tooltip 1.0.2
	(c) 2006-2009 Abel Mohler
	http://www.wayfarerweb.com/wtooltip.php
	**********************************************************************/

	( function( jQuery ) {
		jQuery.fn.jcartTooltip = function( o, callback ) {
			o = jQuery.extend( {
				content: null,
				follow: true,
				auto: true,
				fadeIn: 0,
				fadeOut: 0,
				appendTip: document.body,
				offsetY: 25,
				offsetX: -10,
				style: {},
				id: 'jcart-tooltip'
			}, o || {});

			if ( !o.style && typeof o.style != "object" )
				{
				o.style = {}; o.style.zIndex = "1000";
				}
			else
				{
				o.style = jQuery.extend( {}, o.style || {});
			}

			o.style.display = "none";
			o.style.position = "absolute";

			var over = {};
			var maxed = false;
			var tooltip = document.createElement( 'div' );

            tooltip.id = o.id;

			for ( var p in o.style ) { tooltip.style[p] = o.style[p]; }

			function fillTooltip( condition ) { if ( condition ) { jQuery( tooltip ).html( o.content ); }}

			fillTooltip( o.content && !o.ajax );
			jQuery( tooltip ).appendTo( o.appendTip );

			return this.each( function() {
				this.onclick = function( ev ) {
					function _execute() {
						var display;
						if ( o.content )
							{
							display = "block";
							}
						else
							{
							display = "none";
							}
						if ( display == "block" && o.fadeIn )
							{
							jQuery( tooltip ).fadeIn( o.fadeIn );

							setTimeout(function(){
								jQuery( tooltip ).fadeOut( o.fadeOut );
								}, 1000);
							}
						}
					_execute();
					};

				this.onmousemove = function( ev ) {
					var e = ( ev ) ? ev : window.event;
					over = this;
					if ( o.follow ) {
						var scrollY = jQuery( window ).scrollTop();
						var scrollX = jQuery( window ).scrollLeft();
						var top = e.clientY + scrollY + o.offsetY;
						var left = e.clientX + scrollX + o.offsetX;
						var maxLeft = jQuery( window ).width() + scrollX - jQuery( tooltip ).outerWidth();
						var maxTop = jQuery( window ).height() + scrollY - jQuery( tooltip ).outerHeight();
						maxed = ( top > maxTop || left > maxLeft ) ? true : false;

						if ( left - scrollX <= 0 && o.offsetX < 0 )
							{
							left = scrollX;
							}
						else if ( left > maxLeft )
							{
							left = maxLeft;
							}
						if ( top - scrollY <= 0 && o.offsetY < 0 )
							{
							top = scrollY;
							}
						else if ( top > maxTop )
							{
							top = maxTop;
							}

						tooltip.style.top = top + "px";
						tooltip.style.left = left + "px";
						}
					};

				this.onmouseout = function() {
					jQuery( tooltip ).css('display', 'none');
				};



			});
		};
	})( jQuery );

	// SHOW A TOOLTIP AFTER VISITOR CLICKS THE ADD-TO-CART
	// IN CASE THE CART IS OFF SCREEN
	jQuery('.jcart input[name="<?php echo $jcart['item_add'];?>"]').jcartTooltip({content: '<?php echo $jcart['text']['item_added_message'];?>', fadeIn: 500, fadeOut: 350 });

	// CHECK IF THERE ARE ANY ITEMS IN THE CART
	var cartHasItems = jQuery('td.jcart-item-qty').html();
	if(cartHasItems === null)
		{
		// DISABLE THE PAYPAL CHECKOUT BUTTON
		jQuery('#jcart-paypal-checkout').attr('disabled', 'disabled');
		}

	// HIDE THE UPDATE AND EMPTY BUTTONS SINCE THESE ARE ONLY USED WHEN JAVASCRIPT IS DISABLED
	jQuery('.jcart-hide').remove();

	// DETERMINE IF THIS IS THE CHECKOUT PAGE BY CHECKING FOR HIDDEN INPUT VALUE
	// SENT VIA AJAX REQUEST TO jcart.php WHICH DECIDES WHETHER TO DISPLAY THE CART CHECKOUT BUTTON OR THE PAYPAL CHECKOUT BUTTON BASED ON ITS VALUE
	// WE NORMALLY CHECK AGAINST REQUEST URI BUT AJAX UPDATE SETS VALUE TO jcart-relay.php
	var isCheckout = jQuery('#jcart-is-checkout').val();

	// IF THIS IS NOT THE CHECKOUT THE HIDDEN INPUT DOESN'T EXIST AND NO VALUE IS SET
	if (isCheckout !== 'true') { isCheckout = 'false'; }


	// WHEN AN ADD-TO-CART FORM IS SUBMITTED
	jQuery('form.jcart').submit(function(){

		// GET INPUT VALUES FOR USE IN AJAX POST
		var attribute_name = []; 
		var attribute = [];
		var itemRef = jQuery(this).find('input[name=<?php echo $jcart['item_ref']?>]').val();
		jQuery('.jcart :selected').each(function(i, selected){ 
		    attribute_name[i] = ' '+jQuery(selected).text();
		    attribute[i] = jQuery(selected).val();
			itemRef = itemRef + jQuery(selected).attr("ref");
		});
		var itemPrice = jQuery(this).find('input[name=<?php echo $jcart['item_price']?>]').val();
		if (attribute != '') {
    		var itemName = jQuery(this).find('input[name=<?php echo $jcart['item_name']?>]').val()+' '+attribute_name+' ('+itemRef+')';
			var itemId = jQuery(this).find('input[name=<?php echo $jcart['item_id']?>]').val()+'|'+attribute;
		} else {
		    var itemName = jQuery(this).find('input[name=<?php echo $jcart['item_name']?>]').val();
			var itemId = jQuery(this).find('input[name=<?php echo $jcart['item_id']?>]').val();
		}
		var itemQty = jQuery(this).find('input[name=<?php echo $jcart['item_qty']?>]').val();
		var itemAdd = jQuery(this).find('input[name=<?php echo $jcart['item_add']?>]').val();
		var itemWeight = jQuery(this).find('input[name=<?php echo $jcart['item_weight']?>]').val();

		// SEND ITEM INFO VIA POST TO INTERMEDIATE SCRIPT WHICH CALLS jcart.php AND RETURNS UPDATED CART HTML
		jQuery.post('<?php echo $jcart['url'];?>jcart-relay.php', { "<?php echo $jcart['item_id']?>": itemId, "<?php echo $jcart['item_price']?>": itemPrice, "<?php echo $jcart['item_name']?>": itemName, "<?php echo $jcart['item_qty']?>": itemQty, "<?php echo $jcart['item_add']?>" : itemAdd, "<?php echo $jcart['item_weight']?>" : itemWeight }, function(data) {
			
			// SEND ITEM INFO VIA POST TO INTERMEDIATE SCRIPT WHICH CALLS jcart.php AND RETURNS UPDATED CART HTML
		    jQuery.post('<?php echo $jcart['url'];?>jcart-relay-block.php', { "<?php echo $jcart['item_id']?>": itemId, "<?php echo $jcart['item_price']?>": itemPrice, "<?php echo $jcart['item_name']?>": itemName, "<?php echo $jcart['item_qty']?>": itemQty, "<?php echo $jcart['item_weight']?>" : itemWeight }, function(data2) {

			    // REPLACE EXISTING CART HTML WITH UPDATED CART HTML
			    jQuery('div[name=jcart_block]').html(data2);
			    jQuery('.jcart-hide').remove();

			    });
			
			// REPLACE EXISTING CART HTML WITH UPDATED CART HTML
			jQuery('#jcart').html(data);
			jQuery('.jcart-hide').remove();

			});
			

		// PREVENT DEFAULT FORM ACTION
		return false;

		});


	// WHEN THE VISITOR HITS THEIR ENTER KEY
	// THE UPDATE AND EMPTY BUTTONS ARE ALREADY HIDDEN
	// BUT THE VISITOR MAY UPDATE AN ITEM QTY, THEN HIT THEIR ENTER KEY BEFORE FOCUSING ON ANOTHER ELEMENT
	// THIS MEANS WE'D HAVE TO UPDATE THE ENTIRE CART RATHER THAN JUST THE ITEM WHOSE QTY HAS CHANGED
	// PREVENT ENTER KEY FROM SUBMITTING FORM SO USER MUST CLICK CHECKOUT OR FOCUS ON ANOTHER ELEMENT WHICH TRIGGERS CHANGE FUNCTION BELOW
	jQuery('#jcart').keydown(function(e) {

		// IF ENTER KEY
		if(e.which == 13) {

		// PREVENT DEFAULT ACTION
		return false;
		}
	});


	// JQUERY live METHOD MAKES FUNCTIONS BELOW AVAILABLE TO ELEMENTS ADDED DYNAMICALLY VIA AJAX

	// WHEN A REMOVE LINK IS CLICKED
	jQuery('#jcart a.jcart-remove').live('click', function(){

		// GET THE QUERY STRING OF THE LINK THAT WAS CLICKED
		var queryString = jQuery(this).attr('href');
		queryString = queryString.split('=');

		// THE ID OF THE ITEM TO REMOVE
		var removeId = queryString[1];

		// SEND ITEM ID VIA GET TO INTERMEDIATE SCRIPT WHICH CALLS jcart.php AND RETURNS UPDATED CART HTML
		jQuery.get('<?php echo $jcart['url'];?>jcart-relay.php', { "jcart_remove": removeId, "jcart_is_checkout":  isCheckout },
			function(data) {

			// REPLACE EXISTING CART HTML WITH UPDATED CART HTML
			jQuery('#jcart').html(data);
			jQuery('.jcart-hide').remove();
			});
			
		// SEND ITEM ID VIA GET TO INTERMEDIATE SCRIPT WHICH CALLS jcart.php AND RETURNS UPDATED CART HTML
		jQuery.get('<?php echo $jcart['url'];?>jcart-relay-block.php', { "jcart_remove": removeId, "jcart_is_checkout":  isCheckout },
			function(data) {

			// REPLACE EXISTING CART HTML WITH UPDATED CART HTML
			jQuery('div[name=jcart_block]').html(data);
			jQuery('.jcart-hide').remove();
			});

		// PREVENT DEFAULT LINK ACTION
		return false;
	});

	// WHEN AN ITEM QTY CHANGES
	// CHANGE EVENT IS NOT CURRENTLY SUPPORTED BY LIVE METHOD
	// STILL WORKS IN MOST BROWSERS, BUT NOT INTERNET EXPLORER
	// INSTEAD WE SIMULATE THE CHANGE EVENT USING KEYUP AND SET A DELAY BEFORE UPDATING THE CART
	jQuery('#jcart input[type="text"]').live('keyup', function(){

		// GET ITEM ID FROM THE ITEM QTY INPUT ID VALUE, FORMATTED AS jcart-item-id-n
		var updateId = jQuery(this).attr('id');
		updateId = updateId.split('-');

		// THE ID OF THE ITEM TO UPDATE
		updateId = updateId[3];

		// GET THE NEW QTY
		var updateQty = jQuery(this).val();

		// AS LONG AS THE VISITOR HAS ENTERED A QTY
		if (updateQty !== '')
			{
			// UPDATE THE CART ONE SECOND AFTER KEYUP
			var updateDelay = setTimeout(function(){

				// SEND ITEM INFO VIA POST TO INTERMEDIATE SCRIPT WHICH CALLS jcart.php AND RETURNS UPDATED CART HTML
				jQuery.post('<?php echo $jcart['url'];?>jcart-relay.php', { "item_id": updateId, "item_qty": updateQty, "jcart_update_item": '<?php echo $jcart['text']['update_button'];?>', "jcart_is_checkout": isCheckout }, function(data) {

					// REPLACE EXISTING CART HTML WITH UPDATED CART HTML
					jQuery('#jcart').html(data);
					jQuery('.jcart-hide').remove();
					});
					
				// SEND ITEM INFO VIA POST TO INTERMEDIATE SCRIPT WHICH CALLS jcart.php AND RETURNS UPDATED CART HTML
				jQuery.post('<?php echo $jcart['url'];?>jcart-relay-block.php', { "item_id": updateId, "item_qty": updateQty, "jcart_update_item": '<?php echo $jcart['text']['update_button'];?>', "jcart_is_checkout": isCheckout }, function(data) {

					// REPLACE EXISTING CART HTML WITH UPDATED CART HTML
					jQuery('div[name=jcart_block]').html(data);
					jQuery('.jcart-hide').remove();
					});

				}, 1000);
			}

		// IF THE VISITOR PRESSES ANOTHER KEY BEFORE THE TIMER HAS EXPIRED, CLEAR THE TIMER
		// THE NEW KEYDOWN RESULTS IN A NEW KEYUP, TRIGGERING THE KEYUP FUNCTION AGAIN AND RESETTING THE TIMER
		// REPEATS UNTIL THE USER DOES NOT PRESS A KEY BEFORE THE TIMER EXPIRES IN WHICH CASE THE AJAX POST IS EXECUTED
		// THIS PREVENTS THE CART FROM BEING UPDATED ON EVERY KEYSTROKE
		jQuery(this).keydown(function(){
			window.clearTimeout(updateDelay);
			});
		});


	// END THE DOCUMENT READY FUNCTION
	});