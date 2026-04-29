=== Norick Confirm for Contact Form 7 ===
Contributors: noricksaeki
Requires Plugins: contact-form-7
Tags: contact, form, contact form, confirm, contact form 7
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 5.1.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: norick-confirm-for-contact-form-7

Adds a confirmation screen to Contact Form 7 and provides compatibility with modern versions of WordPress and Contact Form 7.

== Description ==

This plugin adds a confirmation step to Contact Form 7.

This plugin is a maintained fork of the original “Contact Form 7 add confirm” plugin, updated for compatibility with recent versions of WordPress and Contact Form 7.

Features:

* Confirmation screen before final submission
* Back button support
* Compatible with recent Contact Form 7 versions
* Multiple form support
* Works with Flamingo and Contact Form DB
* Compatible with policy checkbox confirmation flows

This plugin is not affiliated with the Contact Form 7 project.

== Installation ==

1. Install and activate Contact Form 7.
2. Upload the `norick-confirm-for-contact-form-7` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the "Plugins" menu in WordPress.
4. Add `[confirm]` and `[back]` tags to your Contact Form 7 form.

== Frequently Asked Questions ==

= How do I add a confirmation screen? =

Add `[confirm]` where you want the confirmation button to appear and `[back]` where you want the back button to appear.

= Does this work with modern Contact Form 7? =

Yes. This version has been updated for recent Contact Form 7 and WordPress releases.

== Changelog ==

= 5.1.2 =
* Forked and renamed for independent maintenance
* Added compatibility with modern Contact Form 7 submit status handling
* Fixed confirm button handling for multiple forms
* Added sanitization for POST values
* Added direct access protection to plugin files
* Fixed compatibility with policy checkbox confirmation flow
* Fixed enqueue handles and text domain for forked version