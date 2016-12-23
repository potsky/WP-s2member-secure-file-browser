=== s2member Secure File Browser ===
Contributors: Potsky
Donate link: http://www.potsky.com/donate/
Tags: s2member, file, browser, shortcode, upload, manager, files
Requires at least: 3.3
Tested up to: 4.2.7
Stable tag: 0.4.19
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

The best way to share files securely with your clients, customers, friends and community.

== Description ==

s2Member Secure File Browser is a wordpress plugin for browsing files from the secure-files location of the [s2Member® WordPress Memberships](http://wordpress.org/extend/plugins/s2member/ "s2Member") plugin.

**Shortcode**

You can display the file browser via the shortcode `[s2member_secure_files_browser /]`.

The shortcode will display a file browser item with only granted directories for current user.

The shortcode can handle :

* `access-s2member-level0` directory for level #0 and more users
* `access-s2member-level1` directory for level #1 and more users
* `access-s2member-level2` directory for level #2 and more users
* `access-s2member-level3` directory for level #3 and more users
* `access-s2member-level4` directory for level #4 and more users
* `access-s2member-ccap-*` custom capabilities directories for according users
* any directory for all users in read only (unable to download)

All these featured folders can be located anywhere and they can be used several times.

Clicking on a file will launch the download according to the s2member files access control.

Please use the shortcode generator in the *Dashboard > s2Member Menu > Secure File Browser* to generate complex values.


**Available shortcode options**

* `collapseeasing` : Easing function to use on collapse
* `collapsespeed` : Speed of the collapse folder action in ms
* `cutdirnames` : Truncate directory names to specific chars length
* `cutfilenames` : Truncate file names to specific chars length
* `dirbase` : Initial directory from the s2member-files directory
* `dirfirst` : Show directories above files
* `displayall` : Display all items without checking if user is granted to download them
* `displaybirthdate` : Display files birth date
* `displaycomment` : Display files comment
* `displayname` : Display files displayname instead of regular files name
* `displaydownloaded` : Show if a file has already been downloaded
* `displaysize` : Display files size
* `displaymodificationdate` : Display files modification date
* `dirzip` : Let directories be downloaded
* `expandeasing` : Easing function to use on expand
* `expandspeed` : Speed of the expand folder action in ms
* `filterdir` : A full regexp directories have to match to be displayed
* `filterfile` : A full regexp files have to match to be displayed
* `folderevent` : Event to trigger expand/collapse
* `hidden` : Show hidden files or not
* `multifolder` : Whether or not to limit the browser to one subfolder at a time
* `names` : Replace files name with custom values
* `openrecursive` : Whether or not to open all subdirectories when opening a directory
* `previewext` : Display file preview button for these extensions
* `s2alertbox` : Display the s2member confirmation box when a user tries to download a file
* `search` : Let user search files
* `searchgroup` : Group shortcodes with a single single search box
* `searchdisplay` : How to display search results
* `sortby` : Sort files in directories by a criteria

All informations about these options are well documented in :

* `Dashboard > s2Member > Secure File Browser` panel for admin (manage_options capability)
* `Dashboard > Tools > Secure File Browser` panel for users


**Example** (*A shortcode has to be defined on one line, here is on several lines below only for better understanding*) :

`[s2member_secure_files_browser
    folderevent="mouseover"
    expandeasing="linear"
    expandspeed="200"
    collapseeasing="swing"
    collapsespeed="200"
    multifolder="0"
    openrecursive="1"
    dirbase="/"
    hidden="1"
    dirfirst="0"
    openrecursive="1"
    filterdir="%2F(access%7Ctata)%2Fi"
    filterfile="%2F%5C.(png%7Cjpe%3Fg%7Cgif%7Czip)%24%2Fi"
    names="access-s2member-level0:General|access-s2member-ccap-video:Videos"
    search="1"
    searchdisplay="4D"
/]`

You can generate a shortcode with complex options with the `Shortcode Generator` in the `Dashboard > s2Member > Secure File Browser` panel


**Widgets**

You can display both fully customizable widgets for :

* Top downloads
* Latest downloads
* Latest available files

**Dashboard**

The admin panel is reachable via the *Dashboard > s2Member Menu > Secure File Browser* menu.

Available features are :

* Statistics : display all downloads/top downloads/top downloaders, sort and apply filters by date, user, file, IP Address, ...
* Statistics : download stats in XML and CSV format
* Statistics : display current s2Member accounting, sort and apply filters by date, user, file and file
* File Browser : Rename, delete, comment and add a display name for files and folders
* Cache management : Rebuild file cache
* Shortcode generator
* Shortcode documentation
* Settings : Received an email each time a user downloads a file
* Settings : Received scheduled reports
* Settings : How many logs you want to keep ?
* Settings : Delete logs
* Settings : Give access to others users to some parts of the admin menu


Don't hesitate to ask me new features or report bugs on [potsky.com](https://www.potsky.com/code/wordpress-plugins/s2member-secure-file-browser/ "Plugin page") !


== Installation ==

**Requirement** : you need to install first the wonderful and free s2Member® plugin [available here](http://wordpress.org/extend/plugins/s2member/ "s2Member")

**s2member Secure File Browser** is very easy to install (instructions) :
* Upload the `/s2member-secure-file-browser` folder to your `/wp-content/plugins/` directory.
* Activate the plugin through the Plugins menu in WordPress®.


== Frequently asked questions ==

= s2Member secure files are always directly downloadable, how can I protect them by forcing php handling ? =

It is recommended to add a `deny from all` directive in your `httpd.conf` for your s2member-files directory in order to avoid people directly access your protected files. Do not put the `deny` directive in the `s2member-files/.htaccess` because this file is always regenerated by s2member and your modifications are always overwritten.

= Why s2member-files/.htaccess is not displayed ? =

Even if you set shortcode option `hidden` to `1`, `.htaccess` will never been displayed.

= Are directories `access-s2member-level*` protected if they are not in the root directory ? =

Yes ! `And access-s2member-ccap*` too !

= The browser does not work, it displays `Invalid nonce` for registered users. =

The authentication on your website is broken because of a plugin (AJAX requests not correctly handled).
This behaviour is correct and it is protecting your files !
It happens for example when your authentication is only performed in a HTTPS form and the navigation is done in HTTP.
If you use the `Wordpress HTTP` plugin from http://mvied.com/projects/wordpress-https/ for example, you have to force HTTPS on each page which includes the s2member Secure File browser.

= How to handle the `s2member-files/app_data` windows directory ? =

In windows installations, put all files in `s2member-files\app_data` instead of `s2member-files` directory.



== What's next? ==

All futures requests are handled on [GitHub](https://github.com/potsky/WordPressS2MemberFileBrowser/issues?sort=comments&state=open "GitHub")

== Translators ==

* Serbo-Croatian : Borisa Djuraskovic at http://www.webhostinghub.com
* French : Potsky

== Screenshots ==

1. File browser in action
2. Admin > File browser in action
3. Admin > File browser in action when deleting a directory
4. Admin > File browser in action when renaming a directory
5. Admin > Download statistics
6. Admin > Shortcode generator
7. Admin > Shortcode documentation
8. Admin > General settings for logs management and access
9. Admin > Notification settings for email reporting
10. Widget

== Changelog ==

= 0.4.19 =
* Enhancement : Remove warning on PHP7 (part 1)(thanx to KTS915 : https://wordpress.org/support/topic/php-notice-73)
* Enhancement : Add user firstname, user lastname and nickname when exporting CSV and XML files

= 0.4.18 =
* New feature : you can inject %USERNAME%, %USEREMAIL% or %USERID% in the dir parameter of the shortcode

= 0.4.17 =
* Security fix : XSS vulnerability in the jquery.prettyPhoto.js library fix

= 0.4.16 =
* Bug fix : in some cases, downloading the CSV file could not work

= 0.4.15 =
* Enhancement : Support for non standard mysql port

= 0.4.14 =
* Enhancement : Add Serbo-Croatian language (by Borisa Djuraskovic at http://www.webhostinghub.com)

= 0.4.13 =
* Enhancement : PHP 5.5 warning removed

= 0.4.12 =
* Bug fix : dashboard navigator was broken in last version

= 0.4.11 =
* Enhancement : plugin now always loads assets at the beginning even if the shortcode is not used on a page. It handles by this way some themes which load page content next to the assets.

= 0.4.10 =
* Enhancement : plugin now checks by itself the wordpress upgrade include (problem with some customers)

= 0.4.9 =
* Bug fix     : remove debug messages in the music player
* Bug fix     : change database mysql engine to reduce overhead

= 0.4.8 =
* New feature : preview for pictures
* New feature : configuration paths in inc/define.php
* Bug fix     : navigator in all statistics panel fix

= 0.4.7 =
* New feature : change files display name in admin
* Bug fix     : downloaded files were no more tracked when link was directly displayed in a page/post

= 0.4.6 =
* Enhancement : plugin is now compatible for PHP installations between 5.2 and 5.3.6
* Enhancement : plugin is now compatible on Windows Servers
* New feature : export all stats as xml and csv files from the statistics menu
* New feature : remove all stats from the settings menu
* New feature : group shortcodes with single search
* Bug fix     : language fix for french
* Bug fix     : all meta data not displayed in search result

= 0.4.5 =
* Enhancement : force mp3 flash player (fallback to html5)  because of a bug in Chrome when playing mp3 via html5 and downloading a file in the same time
* Enhancement : large directories/files supported (tested up to 100000 files in 10000 directories)
* Enhancement : display fixes for all browsers and especially Firefox
* New feature : sort files by birth date (date when then was available in your s2member-files directory)
* New feature : display birth date column
* New feature : display file comments in browser
* New feature : add comments in the dashboard
* Bug fix     : top downloader in notification reports was empty

= 0.4.1 =
* New feature : sortby shortcode option
* New feature : modification date display shortcode option
* New feature : order files by modification date,  addition date, size
* Enhancement : disable previews for non logged users
* Bug fix : IE fix for search button
* Bug fix : mp3 previews in flash fallback was not working in IE and FF.
* Bug fix : searchdisplay shortcode option was not included in the generator
* Bug fix : filterfile was not working anymore in 0.4
* Bug fix : report notification was blank in 0.4
* Bug fix : download zip link was displayed even if dirzip shortcode option was disabled

= 0.4 =
* New feature : cut filename shortcode option
* New feature : already downloaded file warnings shortcode option
* New feature : ability to download directories as zip files
* New feature : search files
* New feature : filesize display shortcode option
* New feature : mp3 preview shortcode option
* New feature : new widget for new and modified available files
* New feature : file caching with new dashboard menu to manually update
* Bug fix : french language fix

= 0.3.7 =
* Publishing fix

= 0.3.6 =
* Enhancement : Add admin statistics (total downloads, unique files and unique downloaders)
* Enhancement : Add FAQ "Invalid Nonce"
* Enhancement : Add vsa file extension

= 0.3.5 =
* New feature : New admin submenu with top rated downloads, higher downloaders, ...
* New feature : New shortcode option to display the s2member alert box before a download
* New feature : New shortcode option to let people view directories but must be logged in to download
* New feature : Add rights in settings for file manager and stats access
* New feature : Widget for top downloads or latest downloads
* New feature : Notification daily reports
* Enhancement : HTML entities for email reports
* Enhancement : Add WP and PHP version checks
* Security fix : Protect plugin subdirectories

= 0.3.2 =
* Hotfix for recursive browsing

= 0.3.1 =
* Publishing fix

= 0.3 =
* New language : french
* New feature : display file size
* New feature : admin : Statistics - display all downloads, sort and apply filters by date, user, file, IP Address, ...
* New feature : admin : Statistics - display current s2Member accounting, sort and apply filters by date, user, file and file
* New feature : admin : File Browser - Rename and delete files and folders
* New feature : admin : Shortcode generator
* New feature : admin : Shortcode documentation
* New feature : admin : Settings - Received an email each time a user downloads a file
* New feature : admin : Settings - How many logs you want to keep ?
* Bug fix : dirbase could not work as expected sometimes
* Enhancement : total plugin rewriting for best performance, practices and security

= 0.2.1 =
* Publishing fix

= 0.2 =
* Enhancement : file and directories icons are now clickable
* New feature : shortag option filterdir
* New feature : shortag option filterfile
* New feature : shortag option openrecursive
* Security fix : real path check perform to forbid browsing above s2member-files directory
* Bug fix : dirbase now works as expected

= 0.1 =
* First release

== Upgrade Notice ==

= 0.4.6 =
Bug fix and new features!

= 0.4.2 =
This version fixes several bugs. If you had a blank screen with the previous version because of a large number of files, try this!

= 0.4.1 =
This version fixes 2 bugs. Upgrade immediately.

= 0.4 =
This update includes the new file caching feature. **On first launch, it computes all files hash. It can last several minutes!**

= 0.3.5 =
A lot of new features ! Upgrade now, seriously, it rocks !

= 0.3.2 =
This version fixes a serious browsing bug. Upgrade immediately.

= 0.3 =
This version adds improvements and admin features. Plugin is fully optimized now, upgrade immediately!

= 0.2.1 =
This version fixes a security related bug. Upgrade immediately.





