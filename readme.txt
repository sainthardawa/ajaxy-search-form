=== Plugin Name ===
Contributors: n-for-all
Donate link: http://ajaxy.org/
Version: 1.0.1
Tags: facebook, live-search, ajax-search, category-search
Requires at least: 3.0.0
Tested up to: 3.3.0
Stable tag: 1.0.3

A facebook like ajaxy live search for wordpress

== Description ==

this plugin is a an ajax live search that uses the same theme as facebook search, it uses ajax and jQuery to get results from php

this plugin can search categories, post types and supports wp-ecommerce plugin

== Installation ==

1. Upload `ajaxy-search-form` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3.(optional) if your theme uses a custom search form, then it should be disabled

To disable the theme search form, go to /wp-content/themes/YOUR_THEME_NAME/ and rename searchform.php to searchforma.php, this will keep the file but remove its reference

add a new function <?php echo ajaxy_search_form(); ?>, insert it into ur theme template

== Frequently Asked Questions ==


== Screenshots ==
1. screenshot-1.png

== Changelog ==

= 1.0.3 =
* fixed some bugs with css to be compatible with all blogs

= 1.0.1 =
* fixed some bugs with css
* fixed some bugs with the script (show more button)

= 1.0 =
* First version. Basic stable version.

1. Search categories
2. Search custom post types
3. Templates customizable from backend

