=== bbPress Simple View Counts ===
Contributors: jezza101
Donate link: http://www.blogercise.com
Tags: bbpress, views, reads, forum
Requires at least: 3.0.1
Tested up to: 3.6
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a count of post views into bbPress 2 forum listing display and into posts.

== Description ==

This plugin will add a count of page views to your bbPress 2 forum.  The plugin inserts the count into two places, 
once on the forum listing page and once at the top of each post.

The count is currently incremented on each refresh of the page.  Counts are recorded using the WordPress post meta API.

Note that this is a new plugin and has not been tested on high volume sites so please perform your own tests
before implementing.  Feedback on performance greatly appreciated.  Please post in the plugin's WP forum area or 
mention @jezza101 in a tweet.

== Installation ==
Upload and install the plugin as normal.  

There are no configuration options at present.

== Frequently Asked Questions ==

= Will this plugin slow down my site? =
It works fine on low volume sites, however it does generate an additional db write on every page load so this will
cause additional db load.  This could have an impact on a high volume site, the truth is I don't know what the affect 
might be.

= The plugin doesn't work quite how I'd like it, can I make a suggestion? =
Sure, get in touch.  This version is really a proof of concept and if it is popular I will expand it.

= It's hardcoded in English, can you make it language friendly? =
Yes, this will be added in a future version.  I just need to work out how...


== Changelog ==

= 0.1 =
* Initial release

