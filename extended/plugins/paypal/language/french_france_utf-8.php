<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Paypal Plugin 1.1                                                         |
// +---------------------------------------------------------------------------+
// | french_france_utf-8.php                                                   |
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
	'submit'                  => 'Envoyer',
	'products'                => 'Le catalogue',
	'buy_now_button'          => 'Acheter maintenant',
	'add_to_cart'             => 'Ajouter au panier',
	'featured_products'       => 'Notre catalogue',
	'category_heading'        => 'Catégories',
	'price_label'             => 'Prix',
	'no_purchase_history'     => 'Pas d\'historique d\'achat',
	'access_to_purchasers'    => 'Cette page est réservée aux membres qui peuvent achetez les articles. Si vous pensez avoir atteint cette page par erreur, merci de contacter l\'administrateur du site.',
	'purchase_history'        => 'Historique d\'achat',
	'Download'                => 'Téléchargement',
	'N/A'                     => 'N/A',
	'contact_admin'           => 'Merci de contacter l\'administrateur du site pour toute question.',
	'no_record'               => 'Il n\'y a pas d\'enregistrement de transaction. Merci de contacter l\'administrateur du site si vous pensez qu\'il y a une erreur.',
	'name'                    => 'Nom',
    'quantity'                => 'Qté',
    'description'             => 'Description',
    'purchase_date'           => 'Date d\'achat',
    'txn_id'                  => 'Txn id',
    'expiration'              => 'Expiration',
    'download'                => 'Téléchargement',
	'message'                 => 'Message',
	'ipn_history'             => 'Historique IPN',
	'true'                    => 'Vrai',
	'false'                   => 'Faux',
	'login'                   => 'Connecter',
	'create_account'          => 'Créer un compte',
	'to_purchase'             => 'pour commander cet article.',
	'IPN_log'                 => 'IPN Log ID',
    'IP_address'              => 'Adresse IP',
    'date_time'               => 'Date/Heure',
    'verified'                => 'Vérifié',
    'transaction'             => 'Transaction ',
    'gross_payment'           => 'Montant',
    'payment_status'          => 'Statut',
	'raw'                     => 'Raw',
	'ID'                      => 'ID',
	'purchaser'               => 'Acheteur (uid)',
	'name_label'              => 'Nom',
	'name_list'               => 'Nom',
	'category_label'          => 'Catégorie ',
	'short_description_label' => 'Description intro courte ',
	'description_label'       => 'Description détails',
	'description_list'        => 'Description',
	'small_picture_label'     => 'Petite image (url) ',
	'picture_label'           => 'Image (url) ',
	'downloaded_product_label'=> 'Article à télécharger ',
	'yes'                     => 'Oui',
	'no'                      => 'Non',
	'shipped_product_label'   => 'Article expédié ',
	'filename_label'          => 'Nom du fichier (sans le chemin) ',
	'expiration_label'        => 'Date d\'expiration (jours) ',
	'save_button'             => 'Enregistrer les modifications',
	'delete_button'           => 'Effacer l\'article',
	'required_field'          => 'Indique un champ obligatoire',
	'deletion_succes'         => 'La suppression de l\'article a bien été effectuée.',
    'deletion_fail'           => 'La suppression du l\'article n\'a pas été effectuée.',
	'error'                   => 'Message d\'erreur',
	'missing_field'           => 'Il manque un champ obligatoire...',
	'save_fail'               => 'La sauvegarde de l\'article n\'a pas été effectuée.',
	'save_success'            => 'La sauvegarde de l\'article a bien été effectuée.',
	'menu_label'              => 'Menu',
	'homepage_label'          => 'Accueil',
	'product_list_label'      => 'Liste des articles',
	'view_cart'               => 'Voir le panier',
	'store'                   => 'La boutique',
	'new_product'             => 'Ajouter un article',
	'view_IPN_log'            => 'Voir le log IPN',
	'access_denied'           => 'Accès refusé',
	'access_denied_message'   => 'Vous n\'avez pas accès à cette page. Si vous pensez rencontrer ce message par erreur, merci de contacter l\'administrateur du site. Toute tentative d\'accès est enregistrée.',
	'edit_label'              => 'Edition de l\'article : ',
	'ok_button'               => 'Ok',
	'edit_button'             => 'Editer',
	'admin'                   => 'Admin',
	'thanks'                  => 'Merci pour votre achat',
	'thanks_details'          => 'Le payement a été effectué via Paypal et une confirmation de votre achat vous a été expédié par email. Vous pouvez vous connecter à votre compte paypal pour voir les détails de cette transaction.',
	'cancel'                  => 'Annuler',
	'cancel_details'          => 'Votre commande a été annulée. Aucun débit ne sera effectué.',
	'cbt'                     => 'Retourner sur le site',
	'total'                   => 'Total:',
	'ipn_data'                => 'IPN Data',
	'info_picture'            => 'Agrandir l\'image',
	'online'                  => 'en ligne',
	'plugin_conf'             => 'La configuration du plugin paypal est aussi',
	'plugin_doc'              => 'La documentation pour l\'installation, la mise à jour et l\'usage du plugin paypal est',
	'products_list'           => 'Listes des articles',
	'create_product'          => 'créer un nouvel article',
    'you_can'                 => 'Vous pouvez ',
	'email'                   => 'Email :',
	'existing_categories'     => 'Les catégories existantes sont',
	'details'                 => 'Fiche détaillée',
	'payment_method'          => 'Choisir votre mode de règlement',
	'checkout_step_1'         => 'Etape 1.<br>Vérifiez votre sélection',
    'checkout_step_2' 	  	  => 'Etape 2.<br>Complétez vos coordonnées',
	'checkout_step_3'	 	  => 'Etape 3.<br>Confirmez votre commande',
	'access_reserved'         => 'Accès réservé',
	'you_must_log_in'         => 'Cette page est réservée aux membres du site. Merci de vous identifier pour y accéder.',
	'logged_to_purchase'      => 'Les membres doivent être connectés pour commander cet article',
	'or'                      => 'ou',
	'product_informations'    => 'Description de l\'article',
	'product_images'          => 'Les images de l\'article',
	'upload_new'              => 'Télécharger un nouveau fichier',
	'select_file'             => 'Choisir un fichier existant',
	'hidden_product'          => 'Cacher le produit dans le catalogue',
	'hidden'                  => 'Cet article est caché',
	'hidden_field'            => 'Caché',
	'hits_field'              => 'Hits',
	'downloads_history'       => 'Historique téléchargements',
	'active_product'          => 'Cet article est activé',
	'active'                  => 'Cet article est désactivé',
	'active_field'            => 'Activé',
	'not_active_message'      => 'Désolé mais cet article n\'est pas disponible.',
	'product_id'              => 'Article ID',
	'user_id'                 => 'Membre',
	'search_button'           => 'Chercher',
	'downloads_history_empty' => 'L\'historique des téléchargements est vide.',
	//v 1.1.5
	'wrong_type'              => 'Désolé, je ne connais pas ce type d\'article...',
	'subscription_label'      => 'Adhésion',
	'new_membership'        => 'une nouvelle souscription',
	'item_id_label'           => 'Référence de l\'article',
	'create_new_product'      => 'Création d\'un nouvel article',
	'is_downloadable'         => 'Cet article est téléchargeable',
	'access_display'          => 'Accès et affichage',
	'show_in_blocks'          => 'Montrer cet article dans les blocks',
	'duration_label'          => 'Durée de la souscription',
	'add_to_group_label'      => 'Ajouter le souscripteur au groupe',
	'day'                     => 'jour(s)',
	'week'                    => 'semaine(s)',
	'month'                   => 'mois',
	'year'                    => 'an(s)',
	'subscriptions'           => 'Adhésions',
	'purchases_history_empty' => 'L\'historique d\'achat est vide.',
	'status'                  => 'Status',
	'N.A'                     => 'N.A',
	'ipnlog_empty'            => 'IPN log est vide.',
	'purchases'               => 'Ventes',
	'downloads'               => 'Téléchargements',
	'IPN_logs'                => 'IPN logs',
	'memberships_list'        => 'Liste des adhésions',
	'create_subscription'     => 'créer une nouvelle adhésion',
	'subscriptions_empty'     => 'Il n\'y a pas d\'adhésion.',
	'add_to_group'            => 'Groupe',
	'create_new_subscription' => 'Création d\'une nouvelle souscription - Fonction Paypal Pro',
	'edit_subscription'       => 'Edition de la souscription',
	'subscription_informations' => 'Informations sur la souscription',
	'notification'            => 'Notification',
	'memberships'             => 'Adhésions',
	'my_purchases'            => 'Mes achats',
	'amount'                  => 'Montant',
	'member'                  => 'Membre',
	'accession_date'          => 'Date d\'adhésion',
	'membership_history'      => 'Historique des adhésions',
	'subscription'            => 'Cotisation',
	'product'                 => 'Article',
	'members_list'            => 'Liste des membres',
	'you_must'                => 'Vous devez vous',
	//transaction
	'see_transaction'         => 'Voir la transaction',
	'membership_receipt'      => 'Reçu d\'adhésion',
	'print'                   => 'Imprimer',
	'from'                    => 'du',
	'to'                      => 'au',
	'paid_on'                 => 'Payé le',
	'by'                      => 'par',
	'sum'                     => 'la somme de',
	//users
	'my_details'              => 'Mes coordonnées',
	'pro_name'                    => 'Nom ou entreprise',
    'street1'                 => 'Adresse 1',
	'street2'                 => 'Adresse 2',
	'postal'                  => 'Code Postal',
	'city'                    => 'Ville',
    'country'                 => 'Pays',
	'phone1'                  => 'Téléphone', 
	'phone2'                  => 'Téléphone',
    'fax'                     => 'Fax',
    'contact'                 => 'Contact',
	'proid'                   => 'SIRET',
	'edit_details'            => 'Editer mes coordonnées',
	'edit_user_details'       => 'Editer les coordonnées',
	'editing_user_details'    => 'Edition des coordonnées du membre',
	'membership_informations' => 'Description de l\'adhésion',
    'install_jquery'          => 'Pour permettre aux utilisateurs de votre site d\'afficher les images des articles dans une lightbox, vous devez installer le plugin jQuery pour Geeklog.',
    'see_members_list'        => 'Voir la liste publique des membres',
    'details_save_success'    => 'Vos coordonnées ont bien été enregistrées.',
    'details_save_fail'       => 'Oups une erreur s\'est produite. Pouvez vous essayer de saisir à nouveau vos coordonnées.',
    'anonymous'               => 'anonyme',
    'Anonymous'               => 'Anonyme',
    'see_subscription'        => 'Voir la souscription',
    'purchase_receipt'        => 'Reçu de transaction',
    'image_not_writable'      => 'Ce dossier de stockage des images du plugin paypal n\'existe pas ou n\'est pas accessible en écriture. Vous devez vérifier ce problème avant d\'utiliser le plugin paypal.',
    'downloadable_products'   => 'Mes téléchargements',
    'price_edit'              => 'Si besoin, saisir une virgule pour le séparateur des milliers et un point pour les décimals.',
	//paypal plugin v1.3
	'create_membership_first' => 'Vous devez d\'abord créer une souscription. Rendez-vous sur la page de la liste des articles et créez une souscription.',
	'products_search'         => 'Articles de la boutique',
	'pending'                 => 'En attente',
	'complete'                => 'Complète',
	'admin_only'              => 'Pour l\'administrateur seulement',
	'unit_price_label'        => 'Prix unitaire',
	'total_row_label'         => 'Total',
	'total_price_label'       => 'TOTAL',
	'order_on'                => 'Status : En attente. Nous attendons votre règlement. Commande effectuée le',
	'confirm_edit_status'     => 'Etes-vous certain de vouloir valider cette commande ?',
	'order_validated'         => 'Le bon de commande a bien été validé et l\'achat mis à jour.',
	'confirm_order_button'    => 'Je confirme ma commande',
	'confirm_order_check'     => 'Afin de valider votre commande, merci de compléter si besoin les données ci-dessous et cliquez sur le bouton "Je confirme ma commande". Vous devrez ensuite nous faire parvenir votre règlement afin de régulariser cette commande.',
	'review_details'          => 'Merci de vérifier vos coordonnées',
	'order_received'          => 'Votre commande est en attente',
	'confirm_by_email'        => 'Nous venons de vous confirmer les détails de votre commande par email.',
	'autotag_desc_paypal'     => '<p>[paypal: id alternate title] - Affiche un lien vers la fiche du produit en utilisant le nom de l\'article come titre. Un titre alternatif peut être précisé mais n\'est pas nécessaire.</p>',
	'autotag_desc_paypal_product' => '<p>[paypal_product:  id] - Affiche l\'image d\'un produit, son nom, une courte description, et les boutons "Achetez maintenant" et "Ajouter au panier".</p>',
	'validate_order'          => 'Valider cette commande',
	'category'                => 'Categorie',
	'home'                    => 'Boutique',
	'empty_category'          => 'Il semblerait que cette catégorie soit vide pour l\'instant',
	'customise'               => 'Personnaliser',
	'discount_label'          => 'Remises et prix de référence',
	'discount_a_label'        => 'Remise par montant',
	'discount_p_label'        => 'Remise par pourcentage',
	'discount_legend'         => 'Vous pouvez indiquer une remise pour ce produit. Le système ne permet pas l\'utilisation de la remise par montant et de la remise par pourcentage en même temps. Vous devez faire un choix.',
	'price_ref_label'         => 'Prix de référence (optionnel)',
	'price_ref_edit'          => 'Vous pouvez déclarer ici un prix de référence, qui devra être plus élevé que le prix normal de l\'article. Il sera indiqué à vos visiteurs.',
	'no_product_to_display'   => 'Aucun article à afficher',
	'one_product_to_display'  => 'article',
	'products_to_display'     => 'articles',
	'shipping'                => 'Livraison et traitement',
	// paypal 1.5
	'sales_history'           => 'Historique des ventes',
	'handle_purchase'         => 'Vous pouvez valider cette commande. A utiliser avec précaution!',
	'confirm_handle_purchase' => 'Souhaitez vous valider cette commande ?',
	'done'                    => 'Effectué !',
	'replace_ipn'             => 'Pour remplacer l\'IPN, allez sur votre compte paypal https://www.paypal.com/cgi-bin/webscr?cmd=_display-ipns-history et copier-coller l\'IPN ci-dessous :',
	'ipn_replaced'            => 'IPN remplacé avec succès. Actualisez la page pour voir les changements.',
	'period_stat'             => 'Période :',
	'month_stat'              => 'Mois :',
	'year_stat'               => 'Année :',
	'evolution_sales_stat'    => 'Evolution des ventes',
	'no_sales_stat'           => 'Aucune ventes pour la période',
	'no_download_folder'        => 'Attention: Le dossier téléchargement n\'existe pas !',
);

$LANG_PAYPAL_ADMIN = array(
    'products'                => 'Produits',
	'manage_categories'       => 'gérer les catégories',
	'create_category'         => 'créer une nouvelle catégorie',
	'choose_category'         => 'Choisir une catégorie',
	'category_label'          => 'Nom de la catégorie',
	'description_label'       => 'Description',
	'enabled_category'        => 'La catégorie est active',
	'parent_id'               => 'Catégorie parente',
	'top_cat'                 => '-- Top --',
	'image_message'           => 'Sélectionner une image sur votre disque dur',
	'image_replace'           => 'Uploader une nouvelle image remplacera celle-ci :',
	'image'                   => 'Image',
	'main_settings'           => 'Paramètres principaux',
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
	'weight'                  => 'Weight(en kilogrammes)',
	'shipping_type'           => 'Shipping type',
	'shipping_amt'            => 'Shipping amount',
	'shipping_options'        => array(0 => 'Pas de frais de port / Port gratuit', 1 => 'Appliquer les frais de port'),
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
	'create_shipper_destination' => 'Vous devez créer au moins un expéditeur et une destination avant de créer un tarif d\'expédition',
	'exclude_cat_label'       => 'Catégorie d\'articles reservée exclusivement à ce service',
	'none'                    => 'Aucune',

);

$LANG_PAYPAL_CART = array(
    'cart'                    => 'Votre panier',
	'item'                    => 'article',
	'items'                   => 'articles',
	'subtotal'                => 'Sous-total',
	'update'                  => 'Mettre à jour',
	'checkout'                => 'Commander',
	'paypal_checkout'         => 'Régler avec Paypal',
	'remove'                  => 'Supprimer',
	'empty'                   => 'Vide',
	'cart_empty'              => 'Votre panier est vide!',
	'added'                   => 'Article ajouté!',
	'invalid_price'           => 'Prix non valide!',
	'item_quantities'         => 'Les quantités d\'articles doivent être des nombres entiers !',
	'error'                   => 'Votre commande n\'a pas aboutie!',
	'description'             => 'Description',
    'unit_price'              => 'Prix unitaire',
    'quantity'                => 'Qté',
    'item_price'              => 'Prix',
    'continue_shopping'       => 'Continuer mes achats',
	'payment_check'           => 'Payer par chèque',
	'total'                   => 'Total',
	'free_shipping'           => 'Frais de port offerts',
	'choose_shipping'         => 'Choix des frais d\'expédition',
);

$LANG_PAYPAL_TYPE = array(
    'product'                 => 'Product',
	'subscrition'             => 'Subscription',
	'donation'                => 'Donation',
	'rent'                    => 'Rent',

);

// For the purchase email message
$LANG_PAYPAL_EMAIL = array(
    'hello'                       => 'Bonjour',
	'purchase_receipt'            => 'Confirmation d\'achat',
	'thank_you'                   => 'Merci pour votre règlement concernant',
	'attached_files'              => 'Vous trouverez en pièce-jointe le(s) fichier(s) commandé(s). Si vous vous identifié dans l\'espace membre du site, vous pouvez aussi télécharger le(s) fichier(s) directement dans la liste des articles.',
	'thanks'                      => 'Cordialement',
	'sign'                        => 'L\'administrateur du site.',
	'membership_expire_soon'      => 'Votre adhésion expire prochainement',
	'membership_expire_soon_txt'  => 'Votre adhésion prendra fin prochainement.',
	'membership_expire_today'     => 'Votre adhésion expire aujourd\'hui',
	'membership_expire_today_txt' => 'Votre adhésion expirera aujourd\'hui.',
	'membership_expired'          => 'Votre adhésion a expiré',
	'membership_expired_txt'      => 'Votre adhésion a expiré.',
	'membership_expire_date'      => 'Date de la fin de votre adhésion :',
	'membership_expiration'       => 'Fin d\'adhésion',
	'member'                      => 'Membre : ',
    'download_files'              => 'Si les articles commandés sont téléchargeables (programmes, photos, pdf...), vous trouverez maintenant un lien de téléchargement sur la page détaillée de chaque article  à la place des boutons "Ajouter au panier" ou "Acheter maintenant".',
	'order_confirmation'          => 'Confirmation de commande',
	'thank_you_order'             => 'Merci pour votre commande de :',
	'send_check'                  => 'Afin de valider vos achats, merci de nous transmettre votre règlement à l\'adresse suivante :',
);

$LANG_PAYPAL_PRO = array (
    'pro_feature'                     => 'Note: You are using the paypal plugin limited edition.  If you need full features you can upgrade to <a href="http://geeklog.fr/wiki/plugins:paypal#paypal-pro" target="_blank">Paypal Pro</a>.',
    'pro_feature_manual_subscription' => 'L\'ajout de d\'adhésions manuellement fait partie des fonction de la version Pro du plugin Paypal. Merci de passer à <a href="http://geeklog.fr/wiki/plugins:paypal#paypal-pro" target="_blank">Paypal Pro</a> si vous souhaitez bénéficier de cette fonctionnalité.',
);

$LANG_PAYPAL_LOGIN = array(
    1                         => 'Connexion nécessaire',
    2                         => 'Pour pouvoir utiliser cette fonction vous devez vous connecter à l\'espace membre du site.'
);

$LANG_PAYPAL_MESSAGE = array(
    'message'                 => 'Message',
);

$LANG_PAYPAL_PAYMENT = array(
    'check'                   => 'Chèque',
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
	'shop_name'                  => 'Nom de l\'activité',
	'shop_street1'               => 'Rue',
	'shop_street2'               => 'Rue',
	'shop_postal'                => 'Code Postal',
	'shop_city'                  => 'Ville',
	'shop_country'               => 'Pays',
	'shop_siret'                 => 'Numéro SIRET',
	'shop_phone1'                => 'Téléphone',
	'shop_phone2'                => 'Téléphone',
	'shop_fax'                   => 'Fax',
	//paypal checkout page
	'image_url'                  => 'The URL of the 150x50-pixel image displayed as your logo in the upper left corner of the PayPal checkout pages.',
	'cpp_header_image'           => 'The image at the top left of the checkout page. The image\'s maximum size is 750 pixels wide by 90 pixels high.',
	'cpp_headerback_color'       => 'The background color for the header of the checkout page. Valid value is case-insensitive six-character HTML hexadecimal color code in ASCII.',
	'cpp_headerborder_color'     => 'The border color around the header of the checkout page. The border is a 2-pixel perimeter around the header space, which has a maximum size of 750 pixels wide by 90 pixels high. Valid value is case-insensitive six-character HTML hexadecimal color code in ASCII.',
	'cpp_payflow_color'          => 'The background color for the checkout page below the header. Valid value is case-insensitive six-character HTML hexadecimal color code in ASCII.',
	'cs'                         => 'The background color of the checkout page.',
    'default_permissions'        => 'Permissions par défaut',
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
	'attribute_thumbnail_size'   => 'Attribute thumbnail size  (Pro version)',
);

/**
*   Configuration system subgroup strings
*   @global array $LANG_configsubgroups['paypal']
*/
$LANG_configsubgroups['paypal'] = array(
    'sg_main' => 'Main Settings',
	'sg_display' => 'Display Settings',
	'sg_myshop' => 'Ma boutique'
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
	22 => array('Noir' => 1, 'Blanc' => 0),
    23 => array('Product name' => 'name', 'Product price' => 'price', 'Product ID' => 'id'),
	24 => array('Left Blocks' => 1, 
			'Right Blocks' => 2, 
			'Left & Right Blocks' => 3, 
			'None' => 0,
	),
);
?>
