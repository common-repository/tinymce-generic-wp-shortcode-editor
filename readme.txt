=== TinyMCE Generic WP Shortcode Editor ===
Contributors: caugb
Donate link: http://caugb.com.br
Tags: shortcode, editor, tinymce
Requires at least: 2.8
Tested up to: 3.1
Stable tag: trunk

It makes TinyMCE able to create or edit shortcodes in a visual way. 


== Description ==

The Generic WP Shortcode Editor plugin gives to TinyMCE the ability to create or edit shortcodes in a visual way, instead of editing the code directly. 


== Installation ==

1. Upload  the entire folder `shortcode-editor` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Search for the [SC] button in TinyMCE toolbar


== Frequently Asked Questions ==

= There are other plugins that adds buttons to manipulate shortcodes. Is this a problem? =

Some plugins that work with shortcodes adds their own buttons to TinyMCE and we try to respect it, using a lower priority when adding the TinyMCE hooks.

= When editing, we can see the`sc_id` property in all shortcodes. What is this? =

This property is used internally by the plugin and it is removed from all shortcodes when you submit the form.


== Screenshots ==

1. The button in TinyMCE toolbar.
2. The dialog window.


== Upgrade Notice ==

This is the first release.


== Changelog ==

= 1.0 =

First release.
