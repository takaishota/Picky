=== WordPress Mobile Admin ===
Contributors: rgubby
Tags: mobile, mobile admin
Requires at least: 2.6
Tested up to: 3.3.1
Stable tag: 4.0.8

WordPress Mobile Admin allows you to blog from your mobile.

== Description ==

WordPress Mobile Admin is a fully featured WordPress control panel for your mobile. It works on every device - no matter what it's capabilities, so it doesn't matter if you don't have an iPhone or BlackBerry, you can still manage and update your blog whilst you're on the go.

It uses Wapple's advanced web services to produce perfect markup whilst at the same time maintaining the familiar look and feel of WordPress. You can write and edit posts, moderate and reply to comments as well as updating and managing tags, categories and pages.

**Features**

* Add new posts from mobile
* Post pictures from your mobile phone and dictate where they appear on the post
* Post, Page, Tag and Category Management
* Comment Moderation - including replying to comments
* User Profile page - including colour schemes
* Support for gravatars
* Fully internationalized - works in any language that WordPress has been translated into!

This plugin uses the same technology as the Wapple Architect Mobile Plugin and if you like this plugin, you'll love that one. Check out the home page here: [Wapple Architect Mobile Plugin for WordPress](http://wordpress.org/extend/plugins/wapple-architect/)

== Installation ==

1. To install through WordPress Control Panel:
	* Click "Plugins", then "Add New"
	* Enter "Wordpress mobile admin" as search term and click "Search Plugins"
	* Click the "Install" link on the right hand side against "WordPress Mobile Admin"
	* Click the red "Install Now" button
	* Click the "Activate Plugin" link
	* Enter your Wapple Architect dev key into the settings page. You can get one from here [Wapple Dev Key Registration](http://wapple.net/signup/wordpress?trk=wpma)
1. To download and install manually:
	* Upload the entire `wordpress-mobile-admin` folder to the `/wp-content/plugins/` directory.
	* Activate the plugin through the `Plugins` menu in WordPress.
	* Enter your Wapple Architect dev key into the settings page. You can get one from here [Wapple Dev Key Registration](http://wapple.net/signup/wordpress?trk=wpma)

The control panel of Wapple Architect is in `Settings` > `Wapple Architect` 
(on WordPress 2.3.3 and under, `Options` > `Wapple Architect`).

If you want to use the WordPress Mobile Plugin with WordPress MU as a site-wide plugin, install the "wordpress-mobile-plugin" folder in the plugins directory and activate on a site-by-site basis.
 
== Frequently Asked Questions ==

= How do I get a dev key? =

Head over to [Wapple](http://wapple.net/signup/wordpress?trk=wpma) and fill out the simple form, you should be able to get your dev key within a couple of minutes!

= Why doesn't the plugin work? I see the web version on my mobile! =

Have you entered your dev key into the Wapple Architect settings? 
If not, head over to Settings > Wapple Architect (or Options > Wapple Architect if you're on version 2.3.3 and under) and enter it into the "Dev Key" input box.

= Do I need SOAP and SimpleXML running for this plugin to work? =

In older versions of the plugin, you needed SOAP and SimpleXML, but not any more! From version 2.0, the dependancy on SimpleXML has been totally removed! You also do not need SOAP and can communicate with Wapple's web services via REST.

= File Uploads seem to be breaking my site =

On some handsets on certain networks, file uploading doesn't work. Even though the mobile phone supports it, it doesn't like it. If you find you're having problems with posting, head to "Dashboard", then "Profile" and turn it off for your user.

== Screenshots ==

1. Dashboard (Gray Theme)

2. Posts Page (Blue Theme)

3. Comments Moderation  (Gray Theme)

4. Login Page  (Gray Theme)

== Changelog ==

= 4.0.8 =
* Fixed issue with pagination on comments page

= 4.0.7 =
* Fixed issue with pagination on edit page

= 4.0.6 =
* Fixed missing function 

= 4.0.5 =
* Removed donate links

= 4.0.4 =
* Updated links

= 4.0.3 =
* Fixed graphic on home page not being properly transparent

= 4.0.2 =
* Added compatibility for WordPress 3.0.3

= 4.0.1 =
* Better error handling on servers that have allow_url_fopen turned off

= 4.0 =
* New admin system - configurable menus - ready for new wave of mobile admin pages!

= 3.8.2 =
* Fixed issue with simple_html_dom

= 3.8.1 =
* Bug fix on dashboard with link to pages
* Bug fix when deleting number of comments - message was wrong

= 3.8 =
* Added WordPress 3.0 Styles

= 3.7 =
* Fixed issue with approving/unapproving comments on the dashboard
* When performing comment actions on the comment page, remember the current comment_status

= 3.6 =
* Added compatibility with WordPress 3

= 3.5 =
* Added ability to change URL of a post & page
* Added option to hide or show categories on a post

= 3.4 =
* Added the ability to edit custom fields on posts
* Added horizontal alignment options for file uploads

= 3.3 =
* Fixed contextual language settings for "Add New" links

= 3.2 =
* Removed auto expanding textarea boxes for anything other than iphones - they're breaking blackberry handsets

= 3.1 =
* Added remember me function to login page

= 3.0 =
* Fully internationalized
* Fixed bug with pagination of pages
* Added auto expanding textarea boxes for iPhones

= 2.5 =
* Added option to choose what size of mobile uploaded image you want to use in your posts

= 2.4.1 =
* Fixed bug with foreign chars on comment titles

= 2.4 =
* Fixed bug with guest users getting wrong message when trying to view categories
* Fixed issue with adding posts in a different timezone to the where the server is

= 2.3 =
* Fixed bug with some sites logging on, removed filter for login and authenticate

= 2.2 =
* Added switch to mobile/desktop option in footer

= 2.1 =
* Fixed warning message when no result back from web service call
* Added compatibility for WordPress 2.9

= 2.0 =
* Removed dependancy on simpleXML
* Works with PHP4
* Footer now present on Login page - frames page nicely
* Added edit post tags page
* Added delete/add post tags
* Added edit categories page
* Added delete/add categories
* Added customized colour schemes
* Added ability to edit tags when adding/editing a post
* Bug fix: Show correct number of posts on edit posts page
* Bug fix: Fixed pingbacks on comments page

= 1.2.1 =
* Fixed bug when web theme has dynamic style sheet and loads up various functions

= 1.2 =
* Added bulk actions to comment moderation
* Added post date to add and edit posts

= 1.1.1 =
* Fixed bug when there is an admin path that isn't in the top level

= 1.1 =
* Added filter posts by type on posts page
* Fix for really long comments
* Added option to enter a post excerpt
* Fixed image post size issue
* Added compatibility with older WordPress versions
* Added compatibility with WordPress 2.8.6

= 1.0.4 =
* Fixed foreign chars bug

= 1.0.3 =
* Added compatibility with WordPress 2.8.5

= 1.0.2 =
* Fixed compatibility issue when you don't have PHP5

= 1.0.1 =
* Fixed admin bug

= 1.0 =
* Post images from mobile
* Comment moderation
* Post editing
* Gravatar support
