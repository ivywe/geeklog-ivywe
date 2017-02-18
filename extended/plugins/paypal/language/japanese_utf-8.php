<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Paypal Plugin 1.5                                                         |
// +---------------------------------------------------------------------------+
// | japanese_utf-8.php                                                        |
// |                                                                           |
// | Japanese language file                                                    |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben - cordiste AT free DOT fr                                  |
// | Translaters: mystral-kk mystral-kk DOT net                                |
// | Translaters: kobab geeklog.crimsonj DOT net                               |
// | Translated: Ivy - komma AT ivywe DOT co DOT jp                            |
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
    'submit'                  => '送信',
    'products'                => '商品',
    'buy_now_button'          => 'すぐ購入する',
    'add_to_cart'             => 'カートに入れる',
    'featured_products'       => '商品リスト',
    'category_heading'        => 'カテゴリリスト',
    'price_label'             => '価格',
    'no_purchase_history'     => '購入履歴がありません',
    'access_to_purchasers'    => 'このページは購入ユーザのためのもので、それ以外の方には表示されない設定になっています。このページが表示されている場合はサイト管理者にご連絡ください。',
    'purchase_history'        => '購入履歴',
    'Download'                => 'ダウンロード',
    'N/A'                     => 'なし',
    'contact_admin'           => '不明な点はサイト管理者までご連絡ください。',
    'no_record'               => '購入履歴が見つかりません。購入したことがある場合はサイト管理者にご連絡ください。',
    'name'                    => '氏名:',
    'quantity'                => '数量:',
    'description'             => '説明:',
    'purchase_date'           => '購入日',
    'txn_id'                  => '取引 ID',
    'expiration'              => '有効期限',
    'download'                => 'ダウンロード:',
    'message'                 => 'メッセージ',
    'ipn_history'             => 'IPN履歴',
    'true'                    => 'はい',
    'false'                   => 'いいえ',
    'login'                   => 'ログイン',
    'create_account'          => 'アカウントを作成して',
    'to_purchase'             => '注文する',
    'IPN_log'                 => 'IPNログID',
    'IP_address'              => 'IPアドレス',
    'date_time'               => '日付/時刻',
    'verified'                => '照会済み',
    'transaction'             => '取引 ID',
    'gross_payment'           => 'お支払総額',
    'payment_status'          => 'お支払い状況',
    'raw'                     => 'Raw',
    'ID'                      => 'ID',
    'purchaser'               => '購入者 (uid)',
    'name_label'              => '商品名',
    'name_list'               => '商品名',
    'category_label'          => 'カテゴリ',
    'short_description_label' => '商品要約',
    'description_label'       => '商品説明',
    'description_list'        => '商品説明',
    'small_picture_label'     => '縮小画像 (url)',
    'picture_label'           => '画像 (url)',
    'downloaded_product_label'=> 'ダウンロード商品',
    'yes'                     => 'はい',
    'no'                      => 'いいえ',
    'shipped_product_label'   => '出荷完了商品:',
    'filename_label'          => 'ファイル名',
    'expiration_label'        => '有効期限 (日数)',
    'save_button'             => '保存',
    'delete_button'           => '削除',
    'required_field'          => '必須記入項目',
    'deletion_succes'         => '削除しました',
    'deletion_fail'           => '削除できませんでした',
    'error'                   => 'エラー',
    'missing_field'           => '必須項目に未入力があります',
    'save_fail'               => '保存できませんでした',
    'save_success'            => '保存しました',
    'menu_label'              => 'メニュー',
    'homepage_label'          => 'ホームページ',
    'product_list_label'      => '商品リスト',
    'view_cart'               => 'カートを見る',
    'store'                   => 'ショップ',
    'new_product'             => '新商品',
    'view_IPN_log'            => 'IPNログ閲覧',
    'access_denied'           => 'アクセスを拒否されました',
    'access_denied_message'   => 'このページにアクセスする権限がありません。権限を所有していると思われる場合、サイト管理者にご連絡ください。なお、このページへのアクセスは記録されます。',
    'edit_label'              => '編集',
    'ok_button'               => 'OK',
    'edit_button'             => '編集',
    'admin'                   => '管理',
    'thanks'                  => 'ご注文ありがとうございました。',
    'thanks_details'          => 'PayPalでの決済が完了し、Eメールの領収書をあなた宛てに送信しました。あなたのPayPalアカウントでこの取引の詳細を確認できます。',
    'cancel'                  => 'キャンセル',
    'cancel_details'          => 'ご注文はキャンセルされました。クレジットカードに請求されることはありません。',
    'cbt'                     => 'サイトに戻る',
    'total'                   => '合計',
    'ipn_data'                => 'IPNデータ',
    'info_picture'            => '大きな画像を見る',
    'online'                  => 'こちら',
    'plugin_conf'             => 'Paypalプラグインのコンフィギュレーション説明も',
    'plugin_doc'              => 'Paypalプラグインのインストール・アップグレードと使用法についてのドキュメントは',
    'products_list'           => '商品リスト',
    'create_product'          => '新規登録',
    'you_can'                 => '',
    'email'                   => 'Eメール',
    'existing_categories'     => '既に存在するカテゴリーは',
    'details'                 => '詳細',
    'payment_method'          => 'ご注文内容の確認メールをお送りします。お振込みのご連絡後、商品を発送させていただきます',
    'checkout_step_1'         => 'Step 1.購入商品の確認',
    'checkout_step_2'         => 'Step 2.購入者情報の入力',
    'checkout_step_3'         => 'Step 3.注文内容の確定', // 
    'access_reserved'         => 'アクセスを記録しました',
    'you_must_log_in'         => 'このページは当ショップの登録メンバ限定です。内容をご覧になるにはログインをお願いします。',
    'logged_to_purchase'      => 'この商品を買うにはログインが必要です',
    'or'                      => '｜',
    'product_informations'    => '商品情報',
    'product_images'          => '商品画像',
    'upload_new'              => '新たにファイルをアップロードする',
    'select_file'             => '既存ファイルから選択する',
    'hidden_product'          => 'カタログの商品を非表示にする',
    'hidden'                  => 'この商品は表示されません。',
    'hidden_field'            => '非表示',
    'hits_field'              => '表示回数',
    'downloads_history'       => 'ダウンロード履歴',
    'active_product'          => '商品を有効にする',
    'active'                  => 'ただいま販売を中止しています',
    'active_field'            => '有効・無効',
    'not_active_message'      => '申し訳ありません。この商品は現在販売していません。',
    'product_id'              => '商品ID',
    'user_id'                 => 'ユーザ',
    'search_button'           => '検索',
    'downloads_history_empty' => 'ダウンロード履歴がありません。',
    //v 1.1.5
    'wrong_type'              => '申し訳ありません，商品タイプが不明です...',
    'subscription_label'      => 'メンバシップ',
    'new_membership'          => 'メンバシップ新規登録',
    'item_id_label'           => '商品ID',
    'create_new_product'      => '商品を作成する',
    'is_downloadable'         => 'この商品はダウンロード可能',
    'access_display'          => 'アクセスと表示',
    'show_in_blocks'          => 'この商品をブロックに表示する',
    'duration_label'          => '契約期間',
    'add_to_group_label'      => 'グループへ追加',
    'day'                     => '日',
    'week'                    => '週',
    'month'                   => '月',
    'year'                    => '年',
    'subscriptions'           => '加入',
    'purchases_history_empty' => '購入履歴がありません。',
    'status'                  => '状態',
    'N.A'                     => 'N.A',
    'ipnlog_empty'            => 'IPN ログはありません。',
    'purchases'               => '購入',
    'downloads'               => 'ダウンロード',
    'IPN_logs'                => 'IPN ログ',
    'memberships_list'        => 'メンバリスト',
    'create_subscription'     => '加入とアクセスを作成する',
    'subscriptions_empty'     => '加入がありません。',
    'add_to_group'            => 'グループ',
    'create_new_subscription' => '加入を新たに作成する - Paypal Pro の機能',
    'edit_subscription'       => '加入を編集する',
    'subscription_informations' => '加入情報',
    'notification'            => '通知',
    'memberships'             => 'メンバシップ',
    'my_purchases'            => '購入履歴',
    'amount'                  => '合計',
    'member'                  => 'メンバ',
    'accession_date'          => 'アクセス日付',
    'membership_history'      => 'メンバシップ履歴',
    'subscription'            => '加入',
    'product'                 => '商品',
    'members_list'            => 'メンバリスト',
    'you_must'                => '必須',
    //transaction
    'see_transaction'         => '取引結果を見る',
    'membership_receipt'      => 'メンバシップ領収書',
    'print'                   => '印刷',
    'from'                    => 'FROM: ',
    'to'                      => 'TO: ',
    'paid_on'                 => '支払い',
    'by'                      => '／',
    'sum'                     => '合計',
    //users
    'my_details'              => 'お届け先情報',
    'pro_name'                => '氏名',
    'street1'                 => '住所',
    'street2'                 => '建物名と部屋番号',
    'postal'                  => '郵便番号',
    'city'                    => '都道府県',
    'country'                 => '国',
    'phone1'                  => 'TEL 1', 
    'phone2'                  => '所属部署名（任意）',
    'fax'                     => 'FAX（任意）',
    'contact'                 => '通信欄（任意）',
    'proid'                   => '組織名（任意）',
    'edit_details'            => '購入者（請求先）情報編集',
    'edit_user_details'       => '購入者（請求先）情報を編集する',
    'editing_user_details'    => '購入者（請求先）情報編集',
    'membership_informations' => 'メンバシップ情報',
    'install_jquery'          => 'ユーザに商品イメージをlightboxで表示させるためには、jQueryプラグインをGeeklogにインストールしてください。',
    'see_members_list'        => '公開会員リストを参照してください。',
    'details_save_success'    => '購入者（請求先）を保存しました。',
    'details_save_fail'       => '申し訳ありませんが、購入者（請求先）情報を保存できませんでした。再度お試しください。',
    'anonymous'               => 'ゲスト',
    'Anonymous'               => 'ゲスト',
    'see_subscription'        => '会員制サービスをご覧ください。',
    'purchase_receipt'        => '請求処理が完了しました',
    'image_not_writable'      => 'Paypal用の画像フォルダがないか、あるいは書き込みができません。Paypalプラグインの利用には、この問題を先に解決してください。<br' . XHTML . '><br' . XHTML . '>フォルダ名は、コンフィギュレーションのPaypalで変更できます。',
    'downloadable_products'   => 'マイダウンロード',
    'price_edit'              => '必要であればセパレータのカンマやデシマルのポイントを付加してください。',
	//paypal plugin v1.3
	'create_membership_first' => '最初にメンバシップを作成する必要があります。商品リストを表示してメンバシップを作成してください。',
	'products_search'         => '商品',
	'pending'                 => 'ペンディング',
	'complete'                => '完了',
	'admin_only'              => '管理者のみ',
	'unit_price_label'        => '単価',
	'total_row_label'         => '小計',
	'total_price_label'       => '合計',
	'order_on'                => 'ステータス: ペンディング。注文待ちです。',
	'confirm_edit_status'     => 'この注文を確定して良いですか?', 
	'order_validated'         => '注文を確定し、購入情報を更新しました。',
	'confirm_order_button'    => '注文する',
	'confirm_order_check'     => '以下の項目を入力して「注文する」をクリックすればご請求のメールが送付されます。',
	'review_details'          => '送付先（請求先）情報を入力してください',
	'order_received'          => '購入お申込みありがとうございます。メールを送信しました。', 
	'confirm_by_email'        => 'メールでのお振込連絡により注文が確定します。メールが届かない場合、再度お手続きいただくか、お電話にてご連絡ください。', // 
	'autotag_desc_paypal'     => '<p>[paypal: id alternate title] - 商品名へのタイトルリンクが表示されます。ページタイトルは必須ではありません。</p>',
	'autotag_desc_paypal_product' => '<p>[paypal_product:  id] - 商品の画像、タイトル、短い紹介文を表示します。「今すぐ購入する」と「購入する」ボタン</p>',
		'validate_order'          => 'Validate this order',
	'category'                => 'カテゴリ',
	'home'                    => 'Home',
    'empty_category'          => 'このカテゴリにはまだ登録されていません。.',
	'customise'               => 'カスタマイズ',
	'discount_label'          => 'ディスカウント',
	'discount_a_label'        => 'ディスカウント価格',
	'discount_p_label'        => 'ディスカウント％',
	'discount_legend'         => 'この商品のディスカウントを選択できます。両方は選べません。どちらか選択してください。',
	'price_ref_label'         => '関連価格 (オプション)',
	'price_ref_edit'          => 'カスタマーにどれが標準価格より高くなければならないか関連価格について説明してください。',
	'no_product_to_display'   => '商品は登録されていません。',
	'one_product_to_display'  => '商品',
	'products_to_display'     => '商品',
	'shipping'                => '発送と持ち帰り',
	// paypal 1.5
	'sales_history'           => '販売履歴',
	'handle_purchase'         => '購入を取り扱えます。注意してください!',
	'confirm_handle_purchase' => 'このコマンドを有効にしますか。?',
	'done'                    => '完了!',
	'replace_ipn'             => 'IPNの置換は、このURL https://www.paypal.com/cgi-bin/webscr?cmd=_display-ipns-history へ行き、IPN を下のテキストエリアにペーストしてください:',
	'ipn_replaced'            => 'IPN は正常に置換されました。ページをリロードしてください。',
	'period_stat'             => '期間:',
	'month_stat'              => '月:',
	'year_stat'               => '年:',
	'evolution_sales_stat'    => '販売のevolution',
	'no_sales_stat'           => 'その期間で販売無し',
	'no_download_folder'        => '注意: ダウンロードフォルダーがありません!',

);

$LANG_PAYPAL_ADMIN = array(
    'products'                => '商品',
	'manage_categories'       => 'カテゴリ管理',
	'create_category'         => 'カテゴリ作成',
	'choose_category'         => 'カテゴリ選択',
	'category_label'          => 'カテゴリ名',
	'description_label'       => '説明',
	'enabled_category'        => 'カテゴリ有効化',
	'parent_id'               => '親カテゴリ',
	'top_cat'                 => '-- TOP --', 
	'image_message'           => 'あなたのディスクから画像を選択してください。',
	'image_replace'           => '新たに画像をアップロードして画像を置き換えてください:',
	'image'                   => '画像',
	'main_settings'           => 'メイン設定',
	'manage_attributes'       => '属性管理',
	'create_attribute'        => '属性を作成',
	'choose_attribute'        => '属性を選択',
	'attribute_label'         => '属性名',
	'code_label'              => 'コード',
	'enabled_attribute'       => '属性は有効になりました',
	'order_label'             => '注文',
	'type_label'              => 'タイプ名',
	'manage_attributetypes'   => '属性タイプを管理',
	'create_attributetype'    => '新しい属性タイプを作成',
	'type_label'              => 'タイプ名',
	'type'                    => 'タイプ',
	'choose_type'             => '-- タイプ選択 --',
	'customisation'           => 'カスタマイズ',
	'customisable'            => 'この商品はカスタマイズ可能',
	'delete_item'             => 'このアイテムを削除',
	'add_attribute'           => 'この属性を追加',
	'remove_attribute'        => 'この属性を削除',
	'order'                   => '注文',
	'move'                    => '移動',
	'delivery_info_label'     => '説明',
	'prod_type'               => '商品タイプ',
	'prod_types'              => array(0 => '実商品', 1 => 'ダウンロード', 2 => 'バーチャル/サービス'),
	'weight'                  => '重量 (キログラム)',
	'shipping_type'           => '発送タイプ',
	'shipping_amt'            => '発送数両',
	'shipping_options'        => array(0 => '発送無料 - フリーシッピング', 1 => '送料了解'),
	'taxable'                 => '課税可能',
	'taxable_help'            => 'この商品が課税対象がどうか選択してください。デフォルトは "はい", 売上税があなたのゲートウェイ・アカウントコンフィギュレーションによって適用されるかどうか。あなたが自動的に売上税を適用するために通路を形成していなければ、このチェックボックスは効果がありません。
このチェックボックス用の主目的はあるアイテムを非課税可能にすることです。',
	'manage_shipping'         => '送料管理',
	'shipping_costs'          => '送料',
    'shipper_services'        => '発送サービス',
    'shipping_locations'      => '発送場所',
	'shipper_label'           => '発送者',
	'service'                 => 'サービス',
	'create_shipper'          => '発送者/発送サービスを作成',
	'service_label'           => 'サービス',
	'create_shipping_to'      => '発送場所を作成',
	'shipping_to_label'       => '距離',
	'create_shipping_cost'    => '発送料を作成する',
	'shipping_min'            => '最少重量 (キログラム)',
	'shipping_max'            => '最大重量 (キログラム)',
	'choose_shipper'          => '-- 発送サービスを選ぶ  --',
	'choose_destination'      => '-- 距離を選ぶ --',
	'create_shipper_destination' => '発送料金を作成する前に発送者と距離を選んでください。',
	'exclude_cat_label'       => 'このサービスのカテゴリー',
	'none'                    => '無し',
);

$LANG_PAYPAL_CART = array(
    'cart'                    => 'カートの中身',
    'item'                    => '商品',
    'items'                   => '商品',
    'subtotal'                => '小計',
    'update'                  => '再計算',
    'checkout'                => 'Step1.購入商品の確認にすすむ',
    'paypal_checkout'         => 'PayPalでお支払い',
    'remove'                  => '取消す',
    'empty'                   => 'クリア',
    'cart_empty'              => '現在、カートに商品が入っていません。',
    'added'                   => '商品が追加されました。',
    'invalid_price'           => '無効な価格表記です。',
    'item_quantities'         => '商品の数量に誤りがあります。',
    'error'                   => 'ご注文の処理ができませんでした。',
    'description'             => '商品名',
    'unit_price'              => '単価',
    'quantity'                => '数量',
    'item_price'              => '価格',
    'continue_shopping'       => '購入を続ける',
	'payment_check'           => 'Step 2.銀行振り込みの購入: 購入者情報の入力へすすむ',
	'total'                   => '合計',
	'free_shipping'           => '送料無料',
	'choose_shipping'         => '発送の選択',
);
$LANG_PAYPAL_TYPE = array(
    'product'                 => '製品',
    'subscrition'             => '加入',
    'donation'                => '寄付',
    'rent'                    => 'レント',

);

// For the purchase email message
$LANG_PAYPAL_EMAIL = array(
    'hello'                       => '',
    'purchase_receipt'        => 'ご注文の確認',
    'thank_you'               => 'ご注文ありがとうございます。<br />ご注文内容は以下の通りです。<br /><br /><br />ご注文商品',
    'attached_files'          => 'ご注文いただいたファイルをこのEメールに添付しました。ログインして購入された場合には、ログインして商品リストを表示してダウンロードすることも可能です。',
    'thanks'                  =>  '以上、ご注文ありがとうございました。',
    'sign'                        => 'サイト管理者',
    'membership_expire_soon'      => 'あなたのメンバシップ期限切れ',
    'membership_expire_soon_txt'  => 'あなたのメンバシップ期限はまもなく切れます。',
    'membership_expire_today'     => 'あなたのメンバシップ期限切',
    'membership_expire_today_txt' => 'あなたのメンバシップ期限は切れます。',
    'membership_expired'          => 'あなたのメンバシップ期限切れ',
    'membership_expired_txt'      => 'あなたのメンバシップ期限は切れました。',
    'membership_expire_date'      => 'メンバシップ期日:',
    'membership_expiration'       => 'メンバシップ期限',
    'member'                      => 'メンバ: ',
    'download_files'              => '注文品がファイルなら (プログラム、画像、PDF…)、ダウンロードリンクがカートに追加するボタンか今すぐ購入ボタンのかわりに表示されます。',
	'order_confirmation'          => 'ご注文ありがとうございます。',
	'thank_you_order'             => 'ご注文内容:',
	'send_check'                  => 'お振込後、メールまたはお電話にて、支払い完了をご連絡ください。入金を確認後、2営業日以内に発送させていただきます。', 
);

$LANG_PAYPAL_PRO = array (
    'pro_feature'                     => 'ご注意: このプラグインはPaypalプラグインの機能制限版です。すべての機能を使いたければ<a href="http://geeklog.fr/wiki/plugins:paypal#paypal-pro" target="_blank">Paypal Pro</a>を利用してください。',
    'pro_feature_manual_subscription' => 'マニュアルサブスクリプションはPaypal Proの機能です。この機能が必要なら<a href="http://geeklog.fr/wiki/plugins:paypal#paypal-pro" target="_blank">Paypal Pro</a>にアップグレードすることで可能になります。',
);

$LANG_PAYPAL_LOGIN = array(
    1                         => 'ログインが必要です',
    2                         => '申し訳ありませんが、このページはログインしてアクセスしてください。',
);

$LANG_PAYPAL_MESSAGE = array(
    'message'                 => 'Message'
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
    'title' => 'Paypalコンフィギュレーション'
);

/**
*   Configuration system prompt strings
*   @global array $LANG_confignames['paypal']
*/
$LANG_confignames['paypal'] = array(
    'paypal_folder'              => 'Paypal公開フォルダ',
    'menulabel'                  => 'Paypalメニューラベル',
    'hide_paypal_menu'           => 'Paypalメニューを隠す',
    'paypal_login_required'      => '購入にログインが必要',
    'paypalURL'                  => 'Paypal url',
    'receiverEmailAddr'          => 'ショップ運営者Eメールアドレス',
    'currency'                   => '通貨',
    'anonymous_buy'              => 'ゲストユーザも購入できる',
    'purchase_email_user'        => '購入完了時、ログインユーザにEメール',
    'purchase_email_user_attach' => '購入者宛Eメールにファイルを添付',
    'purchase_email_anon'        => '購入完了時、ゲストユーザにEメール',
    'purchase_email_anon_attach' => 'ゲストユーザ宛Eメールにファイルを添付',
    'maxPerPage'                 => '1ページに表示する商品の数',
    'categoryColumns'            => '表示するカテゴリのカラム数',
     'paypal_main_header'         => 'メインページのヘッダーにウェルカムメッセージ（テキストまたは自動タグ）',
    'paypal_main_footer'         => 'メインページのフッタにもウェルカムメッセージ（テキストまたは自動タグ）',
    'max_images_per_products'    => '一商品当たり最大画像数',
    'max_image_width'            => '画像の最大幅',
    'max_image_height'           => '画像の最大高さ',
    'max_image_size'             => '画像の最大サイズ',
    'max_thumbnail_size'         => 'サムネイルの最大サイズ',
    'thumb_width'                => 'カタログのサムネイル幅',
    'thumb_height'               => 'カタログのサムネイル高さ',
    'products_col'               => '商品表示のカラム数 (max 1-4)',
    //my shop
    'shop_name'                  => 'ショップ名',
    'shop_street1'               => '住所',
    'shop_street2'               => '建物名と部屋番号',
    'shop_postal'                => '郵便番号',
    'shop_city'                  => '市区町村',
    'shop_country'               => '国',
    'shop_siret'                 => 'E-mail',
    'shop_phone1'                => 'TEL',
    'shop_phone2'                => 'TEL 2',
    'shop_fax'                   => 'FAX',
    //paypal checkout page
    'image_url'                  => '150x50ピクセルの画像だけがPayPalチェックアウトブロックで表示されます。',
    'cpp_header_image'          => 'チェックアウトページの左上の画像。その画像のMAXは横750ピクセル，縦90ピクセル。PayPal はその画像がSSL (https) サーバにアップロードされていることを要求しています。',
    'cpp_headerback_color'     => 'チェックアウトページの背景色。6文字の16進数のASCII文字です。',
    'cpp_headerborder_color'    => 'チェックアウトページの背景の罫線色。ヘッダは2ピクセルの罫線。最大 横 750ピクセル，縦 90ピクセル。値は6文字の16進数のASCII文字によるカラーコードです。',
    'cpp_payflow_color'         => 'チェックアウトページヘッダ下の背景色です。値は6文字の16進数のASCII文字によるカラーコードです。',
    'cs'                         => 'チェックアウトページの背景色',
    'default_permissions'        => 'パーミッションのデフォルト',
    'order'                      => '注文リストのソート',
    'view_membership'            => 'メンバシップリストを表示',
    'site_name'                  => '画像のフォルダ名',
    'view_review'                => 'レビューを見る',
    'display_2nd_buttons'        => '2番目のボタンを表示',
	'display_blocks'             => 'ブロック表示',
	'display_item_id'            => '商品リストにIDを表示',
	'display_complete_memberships' => 'メンバシップの完全リストを表示',
	'enable_pay_by_ckeck'        => '決済サイトを使わず、チェックのみで購入を可能にする',
	'enable_buy_now'             => '今すぐ購入ボタンを配置する',
	'enable_pay_by_paypal'       => 'PayPalでの支払いを有効にする',
	'displayCatImage'            => 'カテゴリ画像を表示する',
	'catImageWidth'              => 'カテゴリ画像の最大ピクセル数',
	'categoryHeading'            => 'カテゴリヘッダ',
	'seo_shop_title'             => 'SEO ショップタイトル',
	'displayCatDescription'      => 'カテゴリの説明を表示する',
	'attribute_thumbnail_size'   => '属性サムネイルのサイズ (プロバージョン)',
);

/**
*   Configuration system subgroup strings
*   @global array $LANG_configsubgroups['paypal']
*/
$LANG_configsubgroups['paypal'] = array(
    'sg_main' => 'メイン設定',
    'sg_display' => '表示設定',
    'sg_myshop' => 'マイショップ'
);

/**
*   Configuration system fieldset names
*   @global array $LANG_fs['paypal']
*/
$LANG_fs['paypal'] = array(
    'fs_main'            => '一般設定',
    'fs_images'          => '画像設定',
    'fs_permissions'     => 'パーミッションのデフォルト',
    'fs_display'         => '表示設定',
    'fs_checkoutpage'    => 'Paypal チェックアウトページ',
    'fs_shopdetails'     => 'ショップの詳細',
 );

/**
*   Configuration system selection strings
*   Note: entries 0, 1, and 12 are the same as in 
*   $LANG_configselects['Core']
*
*   @global array $LANG_configselects['paypal']
*/
$LANG_configselects['paypal'] = array(
    0 => array('はい' => 1, 'いいえ' => 0),
    1 => array('はい' => TRUE, 'いいえ' => FALSE),
    3 => array('はい' => 1, 'いいえ' => 0),
    4 => array('On' => 1, 'Off' => 0),
    5 => array('ページのトップ' => 1, '注目記事の下' => 2, 'ページの下' => 3),
    10 => array('5' => 5, '10' => 10, '25' => 25, '50' => 50),
    12 => array('アクセス不可' => 0, '表示' => 2, '表示・編集' => 3),
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
    22 => array('黒' => 1, '白' => 0),
    23 => array('商品名' => 'name', '商品価格' => 'price', '商品ID' => 'id'),
	24 => array('左ブロック' => 1, 
				'右ブロック' => 2, 
				'左右ブロック' => 3, 
				'なし' => 0,
		),
);
?>
