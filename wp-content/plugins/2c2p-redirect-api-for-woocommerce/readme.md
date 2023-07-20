=== 2C2P Redirect API for WooCommerce ===
Contributors: 2C2P
Author URI: https://www.2C2P.com
Tags: ecommerce, e-commerce, woocommerce, wordpress ecommerce, 2c2p, payment gateway, credit card
Requires at least: 2.6.0
Tested up to: 4.8
Stable tag: 7.0.3
License: MIT

Accept Payment (Credit/Debit Cards, Alipay, Alternative/Cash Payments) on your WooCommerce webstore.

== Description ==
2C2P provides payment solution to your woocommerce webstore. We allow you to accept payments from Credit/Debit Cards, Alipay, or Alternative/Cash Payments using local counter services.

2C2P is available in :
 * Singapore
 * Thailand
 * Myanmar
 * Indonesia
 * Malaysia
 * Philipines
 * Hong Kong

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the \'Plugins\' screen in WordPress
3. Use the Settings->Plugin Name screen to configure the plugin enter the below data for setting page.
	1. Enable/Status -> Enable/Disable the plugin. If disabled , it will not show 2C2P on the checkout page
	2. Title      	 -> Display title for 2C2P payment getaway.
	3. Description   -> Display the description when you select payment type as 2C2P.
	* Merchant ID   -> Merchant ID provided by 2C2P, available in my2C2P Portal
	* Secrect Key   -> Secret Key for authentication, available in my2C2P Portal
	* Mode		-> Select mode - Test: Sandbox, Live: Production
	* Store Card Payment -> Enable the stored card payment when you stick the checkbox.
	* 123 payment expiry -> Payment validity duration for 1-2-3(APM) Payment slip, in hours between (8-720).	

= Updating =
Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Frequently Asked Questions ==
= How to get test / sandbox account? = 
For testing, you may use the sandbox credentials provided at our developer portal @ https://developer.2c2p.com/docs/sandbox

= Where can I find the documentation about the integration manual / plugin? = 
Visit our 2C2P's developer portal for more information about the integration https://developer.2c2p.com

= How can I go live with 2C2P? = 
Please contact our sales team at support@2c2p.com

== Screenshots ==
1. Open the plugin's setting page and enter configuration setting field for 2c2p payment.
2. During checkout, customer can select 2C2P Payment and also select previously stored cards (if enabled stored card payment)
3. Customer shall be redirected to 2C2P's payment page, where they will be able to save their card (if enabled stored card payment) 
4. Customer can see the payment result page.

== Changelog ==
= 7.0.3 =
* Stock deduction after payment successful

= 7.0.2 =
* Added label to show plugin version on setting page
* Added fixed description field

= 7.0.1 =
* Added language support
* Fix issue with payment status
* Cosmetic changes on display texts

= 7.0.0 =
Initial release for redirect payment api, payment version 7.0