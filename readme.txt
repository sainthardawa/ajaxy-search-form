=== Plugin Name ===
Contributors: n-for-all
Donate link: http://ajaxy.org/
Version: 2.1.9
Tags: facebook, live-search, ajax-search, category-search
Requires at least: 3.0.0
Tested up to: 3.4.1
Stable tag: 2.1.9

A facebook like ajaxy live search for wordpress, this plugin uses the same functionality as facebook to retrieve the results from your blog.

== Description ==

* this plugin is a an ajax live search that uses the same theme as facebook search, it uses ajax and jQuery to get results from php

* 2 themes have been added one light and one dark to fit most blogs need and i am also available to customize each theme to suit your blog theme 

* this plugin can search categories, post tags, post types and supports wp-ecommerce plugin and more to go

* this plugin was a scratch when i created it, Now it is competing with the best wordpress live search plugins out there, it is now supporting all customization offered by the best live search plugins for wordpress

* the installation can be a little tricky though, "Best things doesn't always come the easy way", but i am ready to help out... please send me an email or leave me a message at ajaxy.org...

== Installation ==


1. Upload `ajaxy-search-form` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3.(optional) if your theme uses a custom search form, then it should be disabled

To disable the theme search form, go to /wp-content/themes/YOUR_THEME_NAME/ and rename searchform.php to searchforma.php, this will keep the file but remove its reference

add a new function <?php echo ajaxy_search_form(); ?>, insert it into ur theme template

== Frequently Asked Questions ==

-After the plugin is activated, nothing appears
Make sure the theme search is disabled (test the ajaxy search form widget if it appears)

-Styles are broken?
Each theme has its own styles, email me at icu090@gmail.com and i will fix it right away 

== Screenshots ==
1. screenshot-1.jpg
2. screenshot-2.png

== Changelog ==

= 2.1.9 =

* fix for content search

= 2.1.6 =

* specify the id of exisiting search box and ajaxy with implement the search there.
* fixed custom scrollbars.
* hide on page resize.
* qtranslate with Live Search.

= 2.1.5 =

* display posts results under category, example if you search for category "cat", all posts under this category will be shown under "cat" sectio, to enable this search mode, go to templates, edit the category template and set "Show "Posts under Category" to show.
* added scrollbars for more results and less size.

= 2.1.4 =

* Basic support for wpml plugin
* Fixed Excluding show posts under each template to show all posts
* Fixed javascript bug that conflicts with other themes scripts 

= 2.1.3 =

* Fixed search url for latin charachters

= 2.1.2 =

* Fixed invalid url when clicking enter in the search form input box

= 2.1.1 =

* Added the ability customize the "see more results" box and to change the search url
* Added the ability to sort out the results returned in the frontend 

= 2.1.0 =

* Added the ability to remove specific categories/post types from the search


= 2.0.2 =

* fixed carriage return "on click"

= 2.0.1 =

* fixed styles for twentyeleven theme

= 2.0.0 =

* Added themes support
* Added widget box
* Added results box settings to be independent from the search form settings
* Multiple search forms can work on the same page
* Added croping to images + fetching image from within content if there is no featured image
* Added a preview page so that the settings can be viewed on the admin page
* Used wordpress default list table for a better usability

= 1.0.5 =
* fixed taxonomy search to return result for same taxonomy

= 1.0.4 =
* Added Search post tags and custom taxonomy

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

