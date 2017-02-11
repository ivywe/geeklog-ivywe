<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Paypal Plugin 1.2                                                         |
// +---------------------------------------------------------------------------+
// | english.php                                                               |
// |                                                                           |
// | English language file                                                     |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben - cordiste AT free DOT fr                                  |
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
* @package Paypal
*/

/**
* Import Geeklog plugin messages for reuse
*
* @global array $LANG32
*/
global $LANG32;

// +---------------------------------------------------------------------------+
// | Array Format:                                                             |
// | $LANGXX[YY]:  $LANG - variable name                                       |
// |               XX    - specific array name                                 |
// |               YY    - phrase id or number                                 |
// +---------------------------------------------------------------------------+

$LANG_PAYPAL_1 = array(
    'plugin_name'             => 'Paypal',
	'submit'                  => 'Submit',
	'products'                => 'Catalog',
	'buy_now_button'          => 'Buy now',
	'add_to_cart'             => 'Add to cart',
	'featured_products'       => 'Our catalog',
	'category_heading'        => 'Categories',
	'price_label'             => 'Price',
	'no_purchase_history'     => 'No purchase history',
	'access_to_purchasers'    => 'This page is only available to users who can purchases products, and never to anonymous users. If you believe you have reached this page in error, please contact the site administrator.',
	'purchase_history'        => 'Purchase history',
	'Download'                => 'Download',
	'N/A'                     => 'N/A',
	'contact_admin'           => 'Please contact the site administrator if you have any questions.',
	'no_record'               => 'There is no record of any purchases, please contact the site administrator if you believe this is incorrect.',
	'name'                    => 'Name',
    'quantity'                => 'Qty',
    'description'             => 'Description',
    'purchase_date'           => 'Purchase date',
    'txn_id'                  => 'Transaction',
    'expiration'              => 'Expiration',
    'download'                => 'Download:',
	'message'                 => 'Message',
	'ipn_history'             => 'IPN history',
	'true'                    => 'True',
	'false'                   => 'False',
	'login'                   => 'Login',
	'create_account'          => 'Create an account',
	'to_purchase'             => 'to purchase this item.',
	'IPN_log'                 => 'IPN Log ID',
    'IP_address'              => 'IP Address',
    'date_time'               => 'Date/Time',
    'verified'                => 'Verified',
    'transaction'             => 'Transaction ID',
    'gross_payment'           => 'Payment',
    'payment_status'          => 'Status',
	'raw'                     => 'Raw',
	'ID'                      => 'ID',
	'purchaser'               => 'Purchaser (uid)',
	'name_label'              => 'Name',
	'name_list'               => 'Name',
	'category_label'          => 'Category',
	'short_description_label' => 'Short description',
	'description_label'       => 'Description',
	'description_list'        => 'Description',
	'small_picture_label'     => 'Small Picture (url)',
	'picture_label'           => 'Picture (url)',
	'downloaded_product_label'=> 'Downloadable Product',
	'yes'                     => 'Yes',
	'no'                      => 'No',
	'shipped_product_label'   => 'Shipped Product:',
	'filename_label'          => 'Filename',
	'expiration_label'        => 'Expiration Time (days)',
	'save_button'             => 'Save',
	'delete_button'           => 'Delete',
	'required_field'          => 'Indicates required field',
	'deletion_succes'         => 'Deletion successful',
    'deletion_fail'           => 'Deletion failed',
	'error'                   => 'Error',
	'missing_field'           => 'Missing required field...',
	'save_fail'               => 'Save failed',
	'save_success'            => 'Save succeeded',
	'menu_label'              => 'Menu',
	'homepage_label'          => 'Homepage',
	'product_list_label'      => 'Product List',
	'view_cart'               => 'View cart',
	'store'                   => 'Go to Store',
	'new_product'             => 'New Product',
	'view_IPN_log'            => 'View IPN log',
	'access_denied'           => 'Access Denied',
	'access_denied_message'   => 'You do not have access to this page. If you believe you have reached this message in error, please contact your site administrator.  All attempts to access this page are logged',
	'edit_label'              => 'Edit',
	'ok_button'               => 'Ok',
	'edit_button'             => 'Edit',
	'admin'                   => 'Admin',
	'thanks'                  => 'Thank you for your purchase',
	'thanks_details'          => 'Payment was made via PayPal and a receipt for your purchase has been emailed to you. You may log into your paypal account to view details of this transaction.',
	'cancel'                  => 'Cancel',
	'cancel_details'          => 'Your order was canceled. Your credit card will not be charge.',
	'cbt'                     => 'Return on site',
	'total'                   => 'Total',
	'ipn_data'                => 'IPN Data',
	'info_picture'            => 'Enlarge image',
	'online'                  => 'online',
	'plugin_conf'             => 'The paypal plugin configuration is also',
	'plugin_doc'              => 'Install, upgrade and usage documentation for paypal plugin are',
	'products_list'           => 'Products list',
	'create_product'          => 'create a new product',
    'you_can'                 => 'You can ',
	'email'                   => 'Email',
	'existing_categories'     => 'Existing categories are',
	'details'                 => 'Read more',
	'payment_method'          => 'Choose your method of payment', //Todo implement other gateway then this will be "Choose your method of payment"
	'checkout_step_1'         => 'Step 1.<br>Review your selection',
    'checkout_step_2' 	  	  => 'Step 2.<br>Provide your information',
	'checkout_step_3'	 	  => 'Step 3.<br>View order confirmation',
	'access_reserved'         => 'Access reserved',
	'you_must_log_in'         => 'This page is reserved to ours site members. Please, log in to access this page.',
	'logged_to_purchase'      => 'Users must be logged to purchase this item',
	'or'                      => 'or',
	'product_informations'    => 'Product informations',
	'product_images'          => 'Product images',
	'upload_new'              => 'Upload a new file',
	'select_file'             => 'Select an existing file',
	'hidden_product'          => 'Hide the product in the catalog',
	'hidden'                  => 'This product is hidden',
	'hidden_field'            => 'Hidden',
	'hits_field'              => 'Hits',
	'downloads_history'       => 'Download history',
	'active_product'          => 'This product is active',
	'active'                  => 'This product is not active',
	'active_field'            => 'Active',
	'not_active_message'      => 'Sorry but this product is not available',
	'product_id'              => 'Product ID',
	'user_id'                 => 'User',
	'search_button'           => 'Search',
	'downloads_history_empty' => 'Downloads history is empty.',
	//v 1.1.5
	'wrong_type'              => 'Sorry , I don\'t know this product type...',
    'subscription_label'      => 'Membership',
	'new_membership'          => 'a new membership',
	'item_id_label'           => 'Item id',
	'create_new_product'      => 'Creating a new product',
	'is_downloadable'         => 'This product is downloadable',
	'access_display'          => 'Access and display',
	'show_in_blocks'          => 'Show this product in blocks',
	'duration_label'          => 'Subscription duration',
	'add_to_group_label'      => 'Add subscriber to group',
	'day'                     => 'day(s)',
	'week'                    => 'week(s)',
	'month'                   => 'month(s)',
	'year'                    => 'year(s)',
	'subscriptions'           => 'Subscriptions',
	'purchases_history_empty' => 'Purchases history is empty.',
	'status'                  => 'Status',
	'N.A'                     => 'N.A',
	'ipnlog_empty'            => 'IPN log is empty.',
	'purchases'               => 'Purchases',
	'downloads'               => 'Downloads',
	'IPN_logs'                => 'IPN logs',
	'memberships_list'        => 'Memberships list',
	'create_subscription'     => 'create a new subscription or accession',
	'subscriptions_empty'     => 'There is no subscription.',
	'add_to_group'            => 'Group',
	'create_new_subscription' => 'Creating a new subscription - Paypal Pro feature',
	'edit_subscription'       => 'Edit subscription',
	'subscription_informations' => 'Subscription informations',
	'notification'            => 'Notification',
	'memberships'             => 'Memberships',
	'my_purchases'            => 'My purchases',
	'amount'                  => 'Amount',
	'member'                  => 'Member',
	'accession_date'          => 'Accession date',
	'membership_history'      => 'Membership history',
	'subscription'            => 'Subscription',
	'product'                 => 'Product',
	'members_list'            => 'Members list',
	'you_must'                => 'You must',
	//transaction
	'see_transaction'         => 'See transaction',
	'membership_receipt'      => 'Membership receipt',
	'print'                   => 'Print',
	'from'                    => 'from',
	'to'                      => 'to',
	'paid_on'                 => 'Paid on',
	'by'                      => 'by',
	'sum'                     => 'the sum of',
	//users
	'my_details'              => 'My details',
	'pro_name'                => 'Name or company name',
    'street1'                 => 'Adress 1',
	'street2'                 => 'Adress 2',
	'postal'                  => 'Postal code',
	'city'                    => 'City',
    'country'                 => 'Country',
	'phone1'                  => 'Phone', 
	'phone2'                  => 'Phone',
    'fax'                     => 'Fax',
    'contact'                 => 'Contact',
	'proid'                   => 'Professional id',
	'edit_details'            => 'Edit my details',
	'edit_user_details'       => 'Edit user details',
	'editing_user_details'    => 'Editing user details',
	'membership_informations' => 'Membership informations',
    'install_jquery'          => 'To allow your site users to display the products images in a lightbox, you need to install the jQuery plugin for Geeklog.',
    'see_members_list'        => 'See public member list',
    'details_save_success'    => 'Your details were saved',
    'details_save_fail'       => 'Sorry I can\'t save your details. Can you try to submit it one more time.',
    'anonymous'               => 'anonymous',
    'Anonymous'               => 'Anonymous',
    'see_subscription'        => 'See subscripton',
    'purchase_receipt'        => 'Transaction receipt',
    'image_not_writable'      => 'This paypal images folder does not exists or is not writable. You must check this issue before using the paypal plugin.',
    'downloadable_products'   => 'My downloads',
    'price_edit'              => 'If needed, enter a comma for the thousands separator and a point for decimals.',
	//paypal plugin v1.3
	'create_membership_first' => 'You must create a membership first. Go to product list and create a membership.',
	'products_search'         => 'Products',
	'pending'                 => 'Pending',
	'complete'                => 'Complete',
	'admin_only'              => 'For admin only',
	'unit_price_label'        => 'Unit price',
	'total_row_label'         => 'Total',
	'total_price_label'       => 'TOTAL',
	'order_on'                => 'Status: pending. We expect your payment. Order placed on',
	'confirm_edit_status'     => 'Are you sure you want to validate this order?', 
	'order_validated'         => 'Order was validated and purchase was updated.',
	'confirm_order_button'    => 'I confirm my order',
	'confirm_order_check'     => 'If necessary, complete the information below and click on "I confirm my order" to validate your order. You will need to send your payment in order to regularize this command.',
	'review_details'          => 'Please review your details',
	'order_received'          => 'Your order is pending',
	'confirm_by_email'        => 'We sent you an email to confirme the details of your order.',
	'autotag_desc_paypal'     => '<p>[paypal: id alternate title] - Displays a link to a product using the product name as the title. An alternate title may be specified but is not required.</p>',
	'autotag_desc_paypal_product' => '<p>[paypal_product:  id] - Displays a product image, product name, small description, "buy now" and "Add to cart" buttons.</p>',
	'validate_order'          => 'Validate this order',
	'category'                => 'Category',
	'home'                    => 'Home',
    'empty_category'          => 'It seems that this category is empty for now.',
	'customise'               => 'Customise',
	'discount_label'          => 'Discounts and reference price',
	'discount_a_label'        => 'Discount amount',
	'discount_p_label'        => 'Discount percentage',
	'discount_legend'         => 'Here you can choose a discount for this product. The system do not allow the use of all discount amount and discount percentage. You must make a choice.',
	'price_ref_label'         => 'Reference price (optional)',
	'price_ref_edit'          => 'Here you can declare a reference price, which should be higher than the normal price of the item. It will be shown to your visitors.',
	'no_product_to_display'   => 'No product to display',
	'one_product_to_display'  => 'product',
	'products_to_display'     => 'products',
	'shipping'                => 'Shipping and handling',
	// paypal 1.5
	'sales_history'           => 'Sales history',
	'handle_purchase'         => 'You can handle this purchase. Use with caution!',
	'confirm_handle_purchase' => 'You want to validate this command?',
	'done'                    => 'Done!',
	'replace_ipn'             => 'To replace IPN, go to your paypal account https://www.paypal.com/cgi-bin/webscr?cmd=_display-ipns-history and paste the IPN in the textearea bellow:',
	'ipn_replaced'            => 'IPN successfully replaced. Please reload the page to display changes.',
	'period_stat'             => 'Period:',
	'month_stat'              => 'Month:',
	'year_stat'               => 'Year:',
	'evolution_sales_stat'    => 'Evolution of sales',
	'no_sales_stat'           => 'No sales for the period',
	'no_download_folder'        => 'Warning: Download folder do not exists!',

);

$LANG_PAYPAL_ADMIN = array(
    'products'                => 'Products',
	'manage_categories'       => 'manage categories',
	'create_category'         => 'create a new category',
	'choose_category'         => 'Choose a category',
	'category_label'          => 'Category name',
	'description_label'       => 'Description',
	'enabled_category'        => 'Category is enabled',
	'parent_id'               => 'Parent Category',
	'top_cat'                 => '-- Top --', 
	'image_message'           => 'Select an image from your hard drive.',
	'image_replace'           => 'Uploading a new image will replace this one:',
	'image'                   => 'Image',
	'main_settings'           => 'Main settings',
	'manage_attributes'       => 'manage attributes',
	'create_attribute'        => 'create a new attribute',
	'choose_attribute'        => 'Choose an attribute',
	'attribute_label'         => 'Attribute name',
	'code_label'              => 'Code',
	'enabled_attribute'       => 'Attribute is enabled',
	'order_label'             => 'Order',
	'type_label'              => 'Type name',
	'manage_attributetypes'   => 'manage attribute types',
	'create_attributetype'    => 'create a new attribute type',
	'type_label'              => 'Type name',
	'type'                    => 'Type',
	'choose_type'             => '-- Choose a type --',
	'customisation'           => 'Customisation',
	'customisable'            => 'This product is customisable',
	'delete_item'             => 'Delete this item',
	'add_attribute'           => 'Add this attribute',
	'remove_attribute'        => 'Remove this attribute',
	'order'                   => 'Order',
	'move'                    => 'Move',
	'delivery_info_label'     => 'Delivery infos',
	'prod_type'               => 'Product type',
	'prod_types'              => array(0 => 'Physical', 1 => 'Downloadable', 2 => 'Virtual/Service'),
	'weight'                  => 'Weight (in kilograms)',
	'shipping_type'           => 'Shipping type',
	'shipping_amt'            => 'Shipping amount',
	'shipping_options'        => array(0 => 'No shipping - Free shipping', 1 => 'Apply shipping costs'),
	'taxable'                 => 'Taxable',
	'taxable_help'            => 'Select whether this product is subject to sales tax. The default is "true", which means that sales tax will be applied according to your Gateway account configuration. If you have not configured the gateway to automatically apply sales tax, then this checkbox has no effect. The primary purpose for this checkbox is to make certain items non-taxable.',
	'manage_shipping'         => 'manage shipping',
	'shipping_costs'          => 'Shipping costs',
    'shipper_services'        => 'Shipper services',
    'shipping_locations'      => 'Shipping locations',
	'shipper_label'           => 'Shipper',
	'service'                 => 'service',
	'create_shipper'          => 'create a new shipper/service',
	'service_label'           => 'Service',
	'create_shipping_to'      => 'create a shipping to location',
	'shipping_to_label'       => 'Destination',
	'create_shipping_cost'    => 'create a shipping rate',
	'shipping_min'            => 'Minimum Weight (in kilograms)',
	'shipping_max'            => 'Maximum Weight (in kilograms)',
	'choose_shipper'          => '-- Choose a shipper service  --',
	'choose_destination'      => '-- Choose a destination --',
	'create_shipper_destination' => 'You must at least create a Shipper and a Destination before creating a shipping rate',
	'exclude_cat_label'       => 'Category of products dedicated to this service',
	'none'                    => 'None',
);

$LANG_PAYPAL_CART = array(
    'cart'                    => 'Your shopping cart',
	'item'                    => 'item',
	'items'                   => 'items',
	'subtotal'                => 'Subtotal',
	'update'                  => 'Update',
	'checkout'                => 'Checkout',
	'paypal_checkout'         => 'Checkout with PayPal',
	'remove'                  => 'Remove',
	'empty'                   => 'Empty',
	'cart_empty'              => 'Your cart is empty!',
	'added'                   => 'Item added!',
	'invalid_price'           => 'Invalid price format!',
	'item_quantities'         => 'Item quantities must be whole numbers!',
	'error'                   => 'Your order could not be processed!',
	'description'             => 'Description',
    'unit_price'              => 'Unit price',
    'quantity'                => 'Qty',
    'item_price'              => 'Item price',
    'continue_shopping'       => 'Continue shopping',
	'payment_check'           => 'Pay by check',
	'total'                   => 'Total',
	'free_shipping'           => 'Free shipping',
	'choose_shipping'         => 'Choice of shipping',
);

$LANG_PAYPAL_TYPE = array(
    'product'                 => 'Product',
	'subscrition'             => 'Subscription',
	'donation'                => 'Donation',
	'rent'                    => 'Rent',

);

// For the purchase email message
$LANG_PAYPAL_EMAIL = array(
    'hello'                       => 'Hello',
	'purchase_receipt'            => 'Purchase Receipt',
	'thank_you'                   => 'Thanks you for your purchase of:',
	'attached_files'              => 'Attached to this email are the files you purchases. If you were logged into our site when you made your purchase, you can also download you files from the products list.',
	'thanks'                      => 'Friendly greetings,',
	'sign'                        => 'The site Admin',
	'membership_expire_soon'      => 'Your membership will expire soon',
	'membership_expire_soon_txt'  => 'Your membership will expire soon.',
	'membership_expire_today'     => 'Your membership will expire at the end of the day',
	'membership_expire_today_txt' => 'Your membership will expire at the end of the day.',
	'membership_expired'          => 'Your membership has expired',
	'membership_expired_txt'      => 'Your membership has expired.',
	'membership_expire_date'      => 'Membership expire date:',
	'membership_expiration'       => 'Membership expiration',
	'member'                      => 'Member: ',
    'download_files'              => 'If items ordered are downloadable (programmes, pictures, pdf...), you\'ll now find a download link on the detail page of each section instead of the buttons "Add to Cart" or "Buy Now".',
	'order_confirmation'          => 'Order confirmation',
	'thank_you_order'             => 'Thanks you for your order of:',
	'send_check'                  => 'To complete your purchase, send your payment to the following address:',
);

$LANG_PAYPAL_PRO = array (
    'pro_feature'                     => 'Note: You are using the paypal plugin limited edition.  If you need full features you can upgrade to <a href="http://geeklog.fr/wiki/plugins:paypal#paypal-pro" target="_blank">Paypal Pro</a>.',
    'pro_feature_manual_subscription' => 'Manual subscription is a Paypal Pro feature. You can upgrade to <a href="http://geeklog.fr/wiki/plugins:paypal#paypal-pro" target="_blank">Paypal Pro</a> if you need this feature.',
);

$LANG_PAYPAL_LOGIN = array(
    1                         => 'Login required',
    2                         => 'Sorry, to access this area you need to be logged in as a user.'
);

$LANG_PAYPAL_MESSAGE = array(
    'message'                 => 'Message',
);

$LANG_PAYPAL_PAYMENT = array(
    'check'                   => 'check',
	'instant'                 => 'paypal',
	'echeck'                  => 'echeck',
);

// Messages for the plugin upgrade
$PLG_paypal_MESSAGE3002 = $LANG32[9]; // "requires a newer version of Geeklog"

/**
*   Localization of the Admin Configuration UI
*   @global array $LANG_configsections['paypal']
*/
$LANG_configsections['paypal'] = array(
    'label' => 'Paypal',
    'title' => 'Paypal Configuration'
);

/**
*   Configuration system prompt strings
*   @global array $LANG_confignames['paypal']
*/
$LANG_confignames['paypal'] = array(
    'paypal_folder'              => 'Paypal public folder',
	'menulabel'                  => 'Paypal menu label',
	'hide_paypal_menu'           => 'Hide Paypal menu',
	'paypal_login_required'      => 'Paypal login required',
	'paypalURL'                  => 'Paypal url',
	'receiverEmailAddr'          => 'Receiver email address',
	'currency'                   => 'Currency',
	'anonymous_buy'              => 'Anonymous user can buy',
    'purchase_email_user'        => 'Email User upon purchase',
    'purchase_email_user_attach' => 'Attach files to user\'s email message',
    'purchase_email_anon'        => 'Email anonymous buyer upon purchase',
    'purchase_email_anon_attach' => 'Attach files to anonymous buyer email',
	'maxPerPage'                 => 'Number of products to display per page',
	'categoryColumns'            => 'Number of columns of categories to display',
 	'paypal_main_header'         => 'Main page header, autotag welcome',
	'paypal_main_footer'         => 'Main page footer, autotag welcome too',
	'max_images_per_products'    => 'Max images per products',
    'max_image_width'            => 'Max image width',
    'max_image_height'           => 'Max image height',
    'max_image_size'             => 'Max image size',
    'max_thumbnail_size'         => 'Max thumbnail size',
	'thumb_width'                => 'Catalogue thumb width',
	'thumb_height'               => 'Catalogue thumb height',
	'products_col'               => 'Number of columns of products to display (max 1-4)',
	//my shop
	'shop_name'                  => 'Shop name',
	'shop_street1'               => 'Street',
	'shop_street2'               => 'Street',
	'shop_postal'                => 'Postal code',
	'shop_city'                  => 'City',
	'shop_country'               => 'Country',
	'shop_siret'                 => 'Pro ID',
	'shop_phone1'                => 'Phone',
	'shop_phone2'                => 'Phone',
	'shop_fax'                   => 'Fax',
	//paypal checkout page
	'image_url'                  => 'The URL of the 150x50-pixel image displayed as your logo in the upper left corner of the PayPal checkout pages.',
	'cpp_header_image'           => 'The image at the top left of the checkout page. The image\'s maximum size is 750 pixels wide by 90 pixels high.',
	'cpp_headerback_color'       => 'The background color for the header of the checkout page. Valid value is case-insensitive six-character HTML hexadecimal color code in ASCII.',
	'cpp_headerborder_color'     => 'The border color around the header of the checkout page. The border is a 2-pixel perimeter around the header space, which has a maximum size of 750 pixels wide by 90 pixels high. Valid value is case-insensitive six-character HTML hexadecimal color code in ASCII.',
	'cpp_payflow_color'          => 'The background color for the checkout page below the header. Valid value is case-insensitive six-character HTML hexadecimal color code in ASCII.',
	'cs'                         => 'The background color of the checkout page.',
    'default_permissions'        => 'Permissions by default',
    'order'                      => 'Products list order',
    'view_membership'            => 'Display list of memberships',
    'site_name'                  => 'Folder name for images',
    'view_review'                => 'View review',
    'display_2nd_buttons'        => 'Display 2nd buttons',
	'display_blocks'             => 'Display blocks',
	'display_item_id'            => 'Display item ID on products list',
	'display_complete_memberships' => 'Display complete list of memberships',
	'enable_pay_by_ckeck'        => 'Enabled pay by check',
	'enable_buy_now'             => 'Enabled buy now buttons',
	'enable_pay_by_paypal'       => 'Enabled pay by paypal',
	'displayCatImage'            => 'Display category image',
	'catImageWidth'              => 'Category image width in pixel',
	'categoryHeading'            => 'Categories header',
	'seo_shop_title'             => 'SEO shop title',
	'displayCatDescription'      => 'Display category description',
	'attribute_thumbnail_size'   => 'Attribute thumbnail size (Pro version)',
);

/**
*   Configuration system subgroup strings
*   @global array $LANG_configsubgroups['paypal']
*/
$LANG_configsubgroups['paypal'] = array(
    'sg_main' => 'Main Settings',
	'sg_display' => 'Display Settings',
	'sg_myshop' => 'My shop'
);

/**
*   Configuration system fieldset names
*   @global array $LANG_fs['paypal']
*/
$LANG_fs['paypal'] = array(
    'fs_main'            => 'General Settings',
    'fs_images'          => 'Images settings',
    'fs_permissions'     => 'Default Permissions',
	'fs_display'         => 'Display settings',
	'fs_checkoutpage'    => 'Paypal checkout page',
	'fs_shopdetails'     => 'Shop details'
 );

/**
*   Configuration system selection strings
*   Note: entries 0, 1, and 12 are the same as in 
*   $LANG_configselects['Core']
*
*   @global array $LANG_configselects['paypal']
*/
$LANG_configselects['paypal'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE),
    3 => array('Yes' => 1, 'No' => 0),
    4 => array('On' => 1, 'Off' => 0),
    5 => array('Top of Page' => 1, 'Below Featured Article' => 2, 'Bottom of Page' => 3),
    10 => array('5' => 5, '10' => 10, '25' => 25, '50' => 50),
    12 => array('No access' => 0, 'Read-Only' => 2, 'Read-Write' => 3),
	20 => array('USD - US Dollar' => 'USD',
            'AUD - Austrialian Dollar' => 'AUD',
			'BRL - Brazilian Real ' => 'BRL',
            'CAD - Canadian Dollar' => 'CAD',
			'CZK - Czech Koruna' => 'CZK',
            'DKK - Danish Krone' => 'DKK', 
            'EUR - Euro' => 'EUR',
            'GBP - British Pound' => 'GBP',
            'JPY - Japanese Yen' => '円',
            'NZD - New Zealand Dollar' => 'NZD',
            'CHF - Swiss Franc' => 'CHF',
            'HKD - Hong Kong Dollar' => 'HKD',
            'SGD - Singapore Dollar' => 'SGD',
            'SEK - Swedish Krona' => 'SEK',
            'PLN - Polish Zloty' => 'PLN',
            'NOK - Norwegian Krone' => 'NOK',
            'HUF - Hungarian Forint' => 'HUF',
            'ILS - Israeli Shekel' => 'ILS',
            'MXN - Mexican Peso' => 'MXN',
            'PHP - Philippine Peso' => 'PHP',
            'TWD - Taiwan New Dollars' => 'TWD',
            'THB - Thai Baht' => 'THB',
    ),
	21 => array('1' => 1, '2' => 2, '3' => 3, '4' => 4),
	22 => array('Black' => 1, 'White' => 0),
    23 => array('Product name' => 'name', 'Product price' => 'price', 'Product ID' => 'id'),
	24 => array('Left Blocks' => 1, 
				'Right Blocks' => 2, 
				'Left & Right Blocks' => 3, 
				'None' => 0,
		),
);
?>
