=== Crawlable Facebook Comments ===
Contributors:      zewlakow
Plugin Name:       Crawlable Facebook Comments
Plugin URI:        http://www.zewlak.com/crawlable-facebook-comments/
Tags:              Facebook Comments, Comments, SEO,
Author URI:        http://www.zewlak.com
Author:            Tomasz Zewlakow
Requires at least: 3.0
Tested up to:      3.1.3
Stable tag:        0.2
Version:           0.2
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=WQXR3DDCWLHFL&lc=PL&item_name=Crawlable%20Facebook%20Comments&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted

== Description ==

Let Google crawl and index your Facebook Comments. Every time Googlebot visites your site Crawlable Facebook Comments plugin retrieves comments (with authors, dates and replies) and makes them visible in your source code. As we all know deafult facebook <fb:comments></fb:comments> displays comments iframe so they are not visible to Googlebot. Make your comments search engine optimized and valuable in SEO!
Example of usage and trial here: <a href="http://www.zewlak.com/crawlable-facebook-comments/">Wordpress</a>
To see how it works just take a look at cache of test page from Google

http://webcache.googleusercontent.com/search?q=cache:ZK-ulgId4XoJ:www.zewlak.com/crawlable-facebook-comments/+crawlable+facebook+comments&cd=14&hl=pl&ct=clnk&gl=pl

You shouldn't see a difference on your site unless you are Googlebot!
== Installation ==
1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use shortcode `[crawlable-facebook-comments]` in every page you use Facebook Comments
or paste the code in your template file:
`<?php if(function_exists('load_comments')) { load_comments(); } ?>`

== Screenshots ==

1. Example of two comments and html code generated (added nofollow in 0.2 version)

== Changelog ==
= 0.2 =
* Added shortcode
* Printing visible comments just to Googlebot, not every visitor.
= 0.1 =
* Release

== Upgrade Notice ==

= 0.2 =
Restores default Facebook Comments appearance. Now plugin is presenting crawlable comments to GoogleBot!

== Donations ==
If you find this plugin useful be so kind to consider a donation:

<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=WQXR3DDCWLHFL&lc=PL&item_name=Crawlable%20Facebook%20Comments&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted">Dotation</a>