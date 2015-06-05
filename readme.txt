=== Dropstream - automated eCommerce fulfillment ===
Contributors: karlfalconer, Dropstream
Donate link: http://getdropstream.com/merchants
Tags: e-commerce, ecommerce, fulfillment, wp-e-commerce, woocommerce, fulfillment by amazon
Requires at least: 3.5
Tested up to: 4.2
Stable tag: 0.8.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Dropstream is a powerful eCommerce plugin that helps you work with third-party fulfillment centers.

== Description ==

= Dropstream Will Help Your Business Thrive During Its Next Growth Spurt =

Dropstream is the solution for growing merchants that are looking to outsource order fulfillment.

Dropstream is the best way to integrate and automate your entire order fulfillment workflow. It provides everything you need to put your logistics on auto-pilot.

* Automatically forward orders to your fulfillment center
* Send tracking numbers back to your shopping cart, and notify customers
* Update product inventory levels

= See what others say about Dropstream =

> We were hampered by data entry work-arounds, until Dropstream tore down the technical wall that stood between us and our customers. 
> -- Clay Clarkson, Whole Heart Ministries


> Dropstream is a useful capability that gives us greater flexibility in meeting our fulfillment requirements. The service was easy to set up and has been very reliable. 
> -- Scott Madsen, National Imports LLC

= Get Started With Your Free 14-day Trial =

Dropstream connects with many fulfillment centers. You can see a [full list of connectors](http://getdropstream.com/merchants/supported-connectors "Dropstream for Merchants eCommerce connectors") on our website. Don't see your fulfillment center listed? [Contact us](http://getdropstream.com/merchants/contact-us "Contact Dropstream") and we'll add it, free of charge.

== Installation ==

= Minimum Requirements =

* WordPress 3.5 or greater
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater
* Some payment gateways require fsockopen support (for IPN access)
* Compatible Wordpress eCommerce plugin. Ex: WP E-Commerce, WooCommerce
* WP E-Commerce 3.8.9.5 and greater or WooCommerce 2.0.8 and greater

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t even need to leave your web browser. To do an automatic install of Dropstream:

1. Log in to your WordPress admin panel
2. Navigate to the Plugins menu and click Add New.
3. In the search field type “Dropstream” and click Search Plugins. 

Once you’ve found our eCommerce plugin you can view details about it such as the the point release, rating and description. Most importantly of course, you can install it by simply clicking Install Now. After clicking that link you will be asked if you’re sure you want to install the plugin. Click yes and WordPress will automatically complete the installation.

= Manual installation =

The manual installation method involves downloading our plugin and uploading it to your webserver via your favourite FTP application.

1. Download the plugin file to your computer and unzip it
2. Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation’s wp-content/plugins/ directory.
3. Activate the plugin from the Plugins menu within the WordPress admin.

== Frequently Asked Questions ==

= Which WordPress eCommerce Plugin's does this plugin support = 

This plugin supports WP E-Commerce and WooCommerce with an active Dropstream account.

= What do I need to get started? =

You'll an active Dropstream account and need to have a relationship with a fulfillment center that ships your orders to customers. You can see a [full list of fulfillment centers](http://getdropstream.com/merchants/supported-connectors "Dropstream for Merchants eCommerce connectors") on our website. Don't see your fulfillment center listed? [Contact us](http://getdropstream.com/merchants/contact-us "Contact Dropstream") and we'll add it, free of charge.

= Where can I find Dropstream documentation and user guides =

For help setting up and configuring Dropstream please refer to our [user guide](http://support.getdropstream.com)

== Screenshots ==

1. Support multiple sales channels including Amazon Marketplace, and eBay
2. Paid orders are automatically sent to your fulfillment center
3. Orders are accurate every time
4. Tracking numbers are automatically sent back to your shopping cart

== Changelog ==
= 0.8.4
* Added support for order numer with plugin woocommerce-sequential-order-numbers

= 0.8.3
* Default Shipping Phone to Billing Phone

= 0.8.2
* Fixed PHP Warning for WooCommerce 2.2.8 Order Reports

= 0.8.1
* Fixed problem where WooCommerce order item references deleted product

= 0.8.0
* Added support for Woocommerce 2.2.x

= 0.7.2
* Confirmed compatibility with WP 4.0

= 0.7.1
* Update WooCommerce reports to include 'awaiting-fulfillment' order status

= 0.7.0
* Added support for additional WooCommerce order fields, including discounts, taxes, and coupons. WooCommerce 2.1 or greater is required

= 0.6.6
* Bump for WP 3.9 and WooCommerce 2.1 support

= 0.6.5
* Added support for WooCommerce subscription.
* Change to use WooCommerce shipping method title, rather than shipping method id. **NOTE** Dropstream rules will need to be updated.

= 0.6.3
* Added custom order status for WooCommerce 'awaiting-fulfillment'. This order status will be used to acknowledge orders have been received by the fulfillment center.

= 0.6.2
* Added check for WooCommerce to skip processing order_items with an invalid product_id

= 0.6.1
* Added default value for created_after date filter

= 0.6.0
* Added additional order search filter for WooCommerce. NOTE: Only available on WP 3.7 or higher
* Fix for WooCommerce order search only returning top 10 orders

= 0.5.5 =
* Added support for WooCommerce Variable product types

= 0.5.0 =
* Added support for [WooCommerce Sequential Order Numbers Pro](http://www.woothemes.com/products/sequential-order-numbers-pro/)

= 0.0.2 =

* Support for WooCommerce.
* Updated WP E-Commerce order data to include customer e-mail address.

= 0.0.1 =

* This is the initial release with support for  WP E-Commerce
