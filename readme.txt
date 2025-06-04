
=== GiveWP CCAvenue Gateway ===
Contributors: yourname
Tags: givewp, ccavenue, payment gateway, donations, india
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

CCAvenue payment gateway integration for GiveWP with tax exemption features for Indian donors.

== Description ==

This plugin adds CCAvenue payment gateway support to GiveWP, specifically designed for Indian organizations accepting donations. It includes special features for tax exemption certificates:

* PAN Number collection for tax exemption
* Address collection for certificate generation
* Secure CCAvenue integration
* Support for Indian Rupees and other currencies
* Test and live mode support

= Features =

* Seamless integration with GiveWP
* CCAvenue payment processing
* Custom fields for tax exemption data
* Responsive design
* Multi-currency support
* Secure encryption/decryption

= Requirements =

* WordPress 5.0 or higher
* GiveWP plugin (latest version recommended)
* CCAvenue merchant account
* PHP 7.4 or higher

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/givewp-ccavenue-gateway/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to Donations > Settings > Payment Gateways > CCAvenue
4. Enter your CCAvenue credentials (Merchant ID, Access Code, Working Key)
5. Enable the CCAvenue gateway
6. Test the donation process

== Configuration ==

1. Log into your CCAvenue merchant account
2. Get your Merchant ID, Access Code, and Working Key
3. Set up the redirect URL in CCAvenue dashboard: `https://yoursite.com/?give-listener=ccavenue`
4. Configure the plugin settings in WordPress admin

== Frequently Asked Questions ==

= Do I need a CCAvenue account? =
Yes, you need a CCAvenue merchant account to process payments.

= Can I test before going live? =
Yes, the plugin supports CCAvenue's test mode. Enable test mode in GiveWP settings.

= What currencies are supported? =
CCAvenue supports multiple currencies. Configure your preferred currency in GiveWP settings.

== Changelog ==

= 1.0.0 =
* Initial release
* CCAvenue payment gateway integration
* Tax exemption fields (PAN Number, Address)
* Admin settings panel
* Test and live mode support
