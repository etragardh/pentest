=== Anti-Malware Security and Brute-Force Firewall ===
Plugin URI: https://gotmls.net/
Author: Eli Scheetz
Author URI: http://wordpress.ieonly.com/category/my-plugins/anti-malware/
Contributors: gotmls, scheeeli
Donate link: https://gotmls.net/donate/
Tags: security, firewall, anti-malware, scanner, automatic, repair, remove, malware, virus, threat, hacked, malicious, infection, timthumb, exploit, block, brute-force, wp-login, patch, antimalware, revslider, Revolution Slider
Version: 4.21.96
Stable tag: 4.21.96
Requires at least: 3.3
Tested up to: 6.3.1

This Anti-Malware scanner searches for Malware, Viruses, and other security threats and vulnerabilities on your server and it helps you fix them.

== Description ==

**Features:**

* Download Definition Updates to protect against new threats.
* Run a Complete Scan to automatically remove known security threats, backdoor scripts, and database injections.
* Firewall block SoakSoak and other malware from exploiting Revolution Slider and other plugins with known vulnerabilites.
* Upgrade vulnerable versions of timthumb scripts.

**Premium Features:**

* Patch your wp-login and XMLRPC to block Brute-Force and DDoS attacks.
* Check the integrity of your WordPress Core files.
* Automatically download new Definition Updates when running a Complete Scan.

Register this plugin at [GOTMLS.NET](http://gotmls.net/) and get access to new definitions of "Known Threats" and added features like Automatic Removal, plus patches for specific security vulnerabilities like old versions of timthumb. Updated definition files can be downloaded automatically within the admin once your Key is registered. Otherwise, this plugin just scans for "Potential Threats" and leaves it up to you to identify and remove the malicious ones.

NOTICE: This plugin make call to GOTMLS.NET to check for updates not unlike what WordPress does when checking your plugins and themes for new versions. Staying up-to-date is an essential part of any security plugin and this plugin can let you know when there are new plugin and definition update available. If you're allergic to "phone home" scripts then don't use this plugin (or WordPress at all for that matter).

**Special thanks to:**

* Clarus Dignus for design suggestions and graphic design work on the banner image.
* Jelena Kovacevic and Andrew Kurtis of webhostinghub.com for providing the Spanish translation.
* Marcelo Guernieri for the Brazilian Portuguese translation.
* Umut Can Alparslan for the Turkish translation.
* [Micha Cassola](https://profiles.wordpress.org/michacassola/) for the German translation.
* [Robi Erwin Setiawan](https://profiles.wordpress.org/situstarget/) for the Indonesian translation.

== Installation ==

1. Download and unzip the plugin into your WordPress plugins directory (usually `/wp-content/plugins/`).
1. Activate the plugin through the 'Plugins' menu in your WordPress Admin.
1. Register on gotmls.net and download the newest definition updates to scan for Known Threats.

== Frequently Asked Questions ==

= Why should I register? =

If you register on [GOTMLS.NET](http://gotmls.net/) you will have access to download definitions of New Threats and added features like automatic removal of "Known Threats" and patches for specific security issues like old versions of timthumb and brute-force attacks on wp-login.php. Otherwise, this plugin only scans for "Potential Threats" on your site, it would then be up to you to identify the good from the bad and remove them accordingly. 

= How do I patch the Revolution Slider vulnerability? =

Easy, if you have installed and activated my this Anti-Malware plugin on your site then it will automatically block attempts to exploit the Revolution Slider vulnerability.

= How do I patch the wp-login vulnerability? =

The WordPress Login page is susceptible to a brute-force attack (just like any other login page). These types of attacks are becoming more prevalent these days and can sometimes cause your server to become slow or unresponsive, even if the attacks do not succeed in gaining access to your site. This plugin can apply a patch that will block access to the WordPress Login page whenever this type of attack is detected. Just click the Install Patch button under Brute-force Protection on the Anti-Malware Setting page. For more information on this subject [read my blog](http://gotmls.net/tag/wp-login-php/).

= Why can't I automatically remove the "Potential Threats" in yellow? =

Many of these files may use eval and other powerful PHP function for perfectly legitimate reasons and removing that code from the files would likely cripple or even break your site so I have only enabled the Auto remove feature for "Know Threats".

= How do I know if any of the "Potential Threats" are dangerous? =

Click on the linked filename to examine it, then click each numbered link above the file content box to highlight the suspicious code. If you cannot tell whether or not the code is malicious just leave it alone or ask someone else to look at it for you. If you find that it is malicious please send me a copy of the file so that I can add it to my definition update as a "Know Threat", then it can be automatically removed.

= What if the scan gets stuck part way through? =

First just leave it for a while. If there are a lot of files on your server it could take quite a while and could sometimes appear to not be moving along at all even if it really is working. If it still seems stuck after a while then try running the scan again, be sure you try both the Complete Scan and the Quick scan.

= How did I get hacked in the first place? =

First, don't take the attack personally. Lots of hackers routinely run automated script that crawl the internet looking for easy targets. Your site probably got hacked because you are unknowingly an easy target. This might be because you are running an older version of WordPress or have installed a Plugin or Theme with a backdoor or known security vulnerability. However, the most common type of infection I see is cross-contamination. This can happen when your site is on a shared server with other exploitable sites that got infected. In most shared hosting environments it's possible for hackers to use an one infected site to infect other sites on the same server, sometimes even if the sites are on different accounts.

= What can I do to prevent it from happening again? =

There is no sure way to protect your site from every kind of hack attempt. That said, don't be an easy target. Some basic steps should include: hardening your password, keeping all your sites up-to-date, and run regular scans with Anti-Malware software like [GOTMLS.NET](http://gotmls.net/)

= Why does sucuri.net or the Google Safe Browsing Diagnostic page still say my site is infected after I have removed the malicious code? =

sucuri.net caches their scan results and will not refresh the scan until you click the small link near the bottom of the page that says "Force a Re-scan" to clear the cache. Google also caches your infected pages and usually takes some time before crawling your site again, but you can speed up that process by Requesting a Review in the Malware or Security section of [Google Webmaster Tools](https://www.google.com/webmasters/tools/). It is a good idea to have a Webmaster Tools account for your site anyway as it can provide lots of other helpful information about your site.

== Screenshots ==

1. The menu showing Anti-Malware options.
2. The Scan Setting page in the admin.
3. An example scan that found some threats.
4. The results window when "Automatic Repair" fixes threats.
5. The Quarantine showing threats that have been fix already.

== Changelog ==

= 4.21.96 =
* Fixed another Undefined Index Warning in new installs when no definition updates have been downloaded.
* Improved timing of registration check and avoided cached results after new registrations are submitted. 
* Added an option to manually recheck the registration status of the site.
* Checked code for compatibility with WordPress 6.3.1.

= 4.21.95 =
* Fixed the Undefined Index Warning created in the last release.

= 4.21.94 =
* Improved error handling for better scan completion.
* Checked code for compatibility with WordPress 6.3 and ClassicPress 1.6.0.

= 4.21.93 =
* Fixed the Undefined Index Warning when the Brute-Force Login Protection is invoked in certain situations.
* Checked code for compatibility with WordPress 6.2.2 and ClassicPress 1.5.3.

= 4.21.92 =
* Fixed the Uncaught Value Error when scanning files that use Windows-1252 encoding which is unsupported by the PHP function mb_regex_encoding.
* Fixed other minor PHP Warnings about Undefined Indexes.

= 4.21.91 =
* Fixed some HTML formatting issues.
* Fixed a JavaScript error in the scan engine that prevented second attempts to scan directories that failed on the first try.

= 4.21.90 =
* Fixed array compatibility with older versions of PHP.

= 4.21.89 =
* Added more late escapes and sanitizated all _SERVER variables.
* Checked code for compatibility with ClassicPress 1.5.0.

= 4.21.88 =
* Added late escapes to variables that were already escaped as requested by Code review team.
* Fixed a PHP warning about is_dir when it attempts check the existance of a directory that was scanned in the past but is now outside the allowable scan path.

= 4.21.87 =
* Code review and cleanup, added more sanitization.
* Fixed an error when attempting to unserialize an array.

= 4.21.86 =
* Improved the removal of database injections when values are serialized.
* Fixed a vulnerability in using unserialize with Class Objects.
* Fixed PHP warnings about undefined indexes.

= 4.21.85 =
* Prevented infinite looping on recursive sub-directories.
* Changed some default values.
* Checked code for compatibility with WordPress 6.1.1 and ClassicPress 1.4.4.

= 4.21.84 =
* Removed the no_error_reporting option used for debugging when server errors are breaking the site.
* Checked code for compatibility with WordPress 6.0.2 and ClassicPress 1.4.2.

= 4.21.83 =
* Fixed XSS vulnerability on debug URLs introduced in the last release, thanks Erwan Le Rousseau.
* Updated code with other various minor improvements bug fixed.
* Checked code for compatibility with WordPress 6.0.1 and ClassicPress 1.4.2.

= 4.21.74 =
* Updated code with various minor improvements to efficiency and compatibility.
* Checked code for compatibility with WordPress 6.0.

= 4.20.96 =
* Fixed XSS vulnerability by removing unsanitized QUERY_STRING.
* Cleaned up Quarantine code, removing legacy functions and adding more detailed info.
* Fixed undefined variable notice and checked code for compatibility with WordPress 5.9.2.

= 4.20.95 =
* Added more sanitization and validation to all user data entered for better security.
* checked code for compatibility with WordPress 5.9.

= 4.20.94 =
* Fixed an XSS vulnerability and checked code for compatibility with WordPress 5.8.3.

= 4.20.93 =
* Fixed undefined variable warning.
* Updated code for compatibility with PHP version 8.0.

= 4.20.92 =
* Added German translation thanks to Micha Cassola.
* Improved the Apache software version checker for better firewall compatibility.
* Fixed session compatibility that was conflicting with the REST API check in Site Health.
* Checked code for compatibility with WordPress 5.8.1 and ClassicPress 1.3.1.

= 4.20.72 =
* Updated registration form to be more compatible with newer iframe restrictions.
* Fixed session check on the Brute-Force patch to no longer need mod_rewrite.
* Removed older code from WordPress Repository.

= 4.20.59 =
* Various minor bug fixes.
* Added Core Files Definitions for ClassicPress.
* Tweaked code for better compatibility with WordPress 5.7.2 and ClassicPress 1.2.0.

= 4.19.69 =
* Fixed a JavaScript error caused by a new French translation.
* Checked code for compatibility with WordPress 5.4.1.

= 4.19.68 =
* Updated some external links.
* Tweaked code for better compatibility with PHP 7.4 and WordPress 5.4.

= 4.19.50 =
* Added even more error handling to the DB Scan for servers with the PHP memory_limit set too low.
* Modified the Directory Scan Depth to accept 0 as a value to indicate skipping the Directory Scan (use this to focus on the DB Scan).
* Added some Help tips to some of the options on the Settings page.

= 4.19.44 =
* Updated links to use HTTPS by default and fixed some old URLs.
* Various performance improvements.
* Added more error handling to the DB Scan.
* Fixed a few minor bugs causing PHP Notices.
* Fixed a path search to work on Windows servers.
* Tweaked code for compatibility with WP 5.3 (latest release).

= 4.18.76 =
* Cleaned up the Nonce Token creation and storage functions.
* Cleaned up View Quarantine page and fixed recovery link.
* Added debugging for login errors WP head and footer Hooks.

= 4.18.74 =
* Fixed a bug in the Nonce Token Errors that was created by changes in the last release.

= 4.18.71 =
* Added wp_options table to the db_scan.
* Fixed a few minor bugs in the db scan quarantine view.
* Changed some wording and other minor fomatting issues.
* Checked code for compatibility with WP 5.2.1 (latest release).

= 4.18.69 =
* Added a Warning message about the vulnerability in the yuzo-related-post plugin.
* Updated the Quarantine interface and added a re-scan / re-clean feature.
* Fixed a bug in the scan depth array that would produce PHP Notices in the error_log files under certain conditions.
* Changed some wording and other minor fomatting issues.
* Removed some outdated JavaScript that is no longer needed.
* Checked code for compatibility with WP 5.2 (latest release).

= 4.18.63 =
* Fixed a major bug in the Firewall updates that could cause a False Positive lockout.

= 4.18.62 =
* Fixed a bug in the Firewall that prevented some iPad devices from logging in.
* Fixed an encoding bug that prevented the Examine File window from dispaying some file formats.
* Restored the File Details window in the Examine File window.
* Updated code for compatibility with WP 5.1.1 (latest release).

= 4.18.52 =
* Added a whole new DB Scan category that looks for links and scripts injected directly into the database content and removes them.
* Updated Firewall landing page for HTTPS compatibility.
* Removed some old code that was no longer needed.
* Added a feature to clear cache files before running the Complete Scan, this will speed up the scan and prevent malware from being saved on your cached paged.
* Updated code for compatibility with WP 5.0.2 (latest release).

= 4.17.69 =
* Updated code for compatibility with WP 4.9.8 (latest release).
* Fixed PHP Notice for the unknown offset of SERVER_parts.
* Escaped single-quotes in translated strings for use within JavaScript.

= 4.17.68 =
* Updated code for compatibility with WP 4.9.7 (latest release).
* Removed wrong size dashicon from Settings link in plugin list.
* Removed the broken link to vote WORKS on wordpress.org.
* Reordered priorety on fixing Known Threats to be more efficient.

= 4.17.58 =
* Updated code for compatibility with WP 4.9.4 (latest release).
* Fixed dashicons sizing in css.
* Add ability to update registration email from within the plugin settings.
* Cleaned up expired nonce tokens left behind from an older version.

= 4.17.57 =
* Updated code for compatibility with WP 4.9.3 (latest release).
* Fixed registration form and alternate domain for definition updates to work on HTTPS.
* Fixed the wording on the Title check error message.

= 4.17.44 =
* Added Title check to make sure it does say you were hacked.
* Updated code for compatibility with WP 4.8.3 (latest release).
* Fixed Undefined variable error in Quarantine.
* Fixed XSS vulnerability in nonce error output.

= 4.17.29 =
* Changed the definition update URL to only use SSL when required.
* Updated PayPal form for better domestic IPN compatibility.

= 4.17.28 =
* Added the Turkish translation thanks to Umut Can Alparslan.
* Improved the auto update so that old definitions could be phased out and new threat types would be selected by default.
* Fixed the admin username change feature on multisite installs.
* Fixed the details window so that it scrolls to the highlighted code.
* Set defaults to disable the Potential Threat scan if other threats definitions are enabled.
* Encoded definitions array for DB storage.
* Fixed syntax error in the XMLRPC patch for newer versions of Apache.
* Added fall-back to manual updates if the Automatic update feature fails.
* Fixed PHP Notices about undefined variable added in last Version release.
* Improved Apache version detection.
* Changed Automatic update feature to automatically download all definitions and firewall updates.
* Added PHP and Apache version detections and changed the XMLRPC patch to work with Apache 2.4 directives.
* Removed the onbeforeunload function because Norton detected it as a False Positive.
* Removed code that was deprecated in PHP Version 7.
* Fixed PHP Notice about an array to string conversion with some rare global variable conditions.
* Added more firewall options.
* Moved Scan Log from the Quarantine page to the main Setings page.
* Fixed PHP Warning about an invalid argument in foreach and some other bugs too.
* Fixed "What to look for" Options so that changes are saved.
* Changed get_currentuserinfo to wp_get_current_user because the get_currentuserinfo function was deprecated in WP 4.5

= 4.16.17 =
* Removed Menu Item Placement Options because the add_object_page function was deprecated in WP 4.5.
* Added firewall options for better compatibility with WP Firewall 2.
* Fixed an XSS vulnerability in the debug output of the nonce token.
* Moved the Firewall Options to it's own page linked to from the admin menu.
* Moved the Quick Scan from the admin menu to the top of the Scan Settings page.
* Fixed PHP Warning about in_array function expecting parameter 2 to be an array, found by Georgey B.
* Made a few minor cosmetic changes and fixed a few other small bugs in the interface.
* Fixed the Nonce Token error caused by W3 Total Cache breaking the set_transient function in WordPress.
* Added the Brazilian Portuguese language files, thanks to Marcelo Guernieri for the translation.
* Fixed the admin menu and also some links that did not work on Windows server.
* Added Core Files to the Quick Scan list on the admin menu.
* Added a nonce token to prevent Cross-Site Request Forgery by admins who are logged-in from another site.
* Hardened against XSS vulnerability triggered by the file names being scanned (thanks to Mahadev Subedi).
* Improved brute-force patch compatibility with alternate wp-config.php location.
* Had to remove the encoding of the Default Definitions to meet the WordPress Plugin Guidelines.
* Improved the JavaScript in the new Brute-Force login patch so that it works with caching enabled on the login page.
* Improved the Brute-Force login patch with custom fields and JavaScript. 
* Added a Save button to that Scan Settings page.
* Fixed a bug in the XMLRPC Patch "Unblock" feature.
* Added a link to purge the deleted Quarantine items from the database.
* Added firewall option to Block all XMLRPC calls.
* Fixed a few cosmetic bugs in the quarantine and firewall options.
* Fixed a bugs in the Quarantine that was memory_limit errors if there number of files in the was too high.
* Added the highlight malicious code feature back to the Quarantine file viewer.
* Added the ability to change the admin username if the current username is "admin".
* Improved the code in the Brute-Force Protection patch.
* Fixed a few bugs in the Core Files Check that was preventing it from fixing some unusual file modifications.
* Fixed a major bug that made multisite scan extremely slow and sometimes error out.
* Moved all ajax call out of the init function and into their own functions for better handling time.
* Moved the quarantine files into the database and deleted the old directory in uploads.
* Fixed some minor formatting issues in the HTML output on the settings page.
* Added a warning message if base64_decode has been disabled.
* Hardened against injected HTML content by encoding the tags with variables.
* Fixed debug option to exclude individual definitions.
* Hardened admin_init with current_user_can and realpath on the quarantine file deletion (thanks to J.D. Grimes).
* Fixed another XSS vulnerabilities in the admin (thanks to James H.)
* Hardened against XSS vulnerabilities in the admin (thanks to Tim Coen).
* Added feature to restore default settings for Exclude Extensions.
* Changed the encoding on the index.php file in the Quarantine to make it more human-readable.
* Fixed a few small bugs that were throwing PHP Notices in some configurations and added more info to some error messages.
* Extended execution_time during the Fix process to increase the number of files that could be fixed at a time.
* Added a Quarantine log to the database.
* Fixed a couple of minor bugs that would throw PHP notices.

= 4.15.16 =
* Created an automatic update feature that downloads any new definition updates before starting the scan.
* Added WordPress Core files to the new definitions update process and included a scan option to check the integrity of the Core files.
* Automatically whitelisted the unmodified WordPress Core files.
* Made more improvements to the Brute-Force protection patch and other minor cosmetic changes to the interface.
* Protected the HTML in my plugin from filter injections and fixed a few other minor bugs.
* Fixed a problem with deleting files from the Quarantine folder.
* Added a descriptive reason to the error displayed if the fix was unsuccessful.
* Added link to restore the default location of the Examine Results window.
* Improved the encoding of definition updates so that they would not be blocked by poorly written firewall rules.
* Suppressed the "Please make a donation" nag if the fix was unsuccessful, to avoid confusion over premium services.
* Removed debug alert from initial session check.
* Improved rewrite compatibility of session check for the Brute-Force Protection Installation.
* Improved session check for the option to Install Brute-Force Protection and added an error message on failure.
* Improved support for Multisite by only allowing Network Admins access to the Anti-Malware menu.
* Added link to view a simple scan history on the Quarantine page.
* Updated firewall to better protect agains new variations of the RevSlider Exploit.
* Improved check for session support before giving the option to Install Brute-Force patch.
* Added option to skip scanning the Quarantined files.
* Updated Brute-Force patch to fix the problem of being included more that once.
* Fixed a few minor bugs (better window positioning and css, cleaner results page, updated new help tab, etc.).
* Made sure that the plugin does not check my servers for updates unless you have registered (this opt-in requirement is part of the WordPress Repository Guidelines).
* Added exception for the social.png files to the skip files by extension list.
* Fixed removal of Known Threats from files in the Quarantine directory.
* Block SoakSoak and other malware from exploiting the Slider Revolution Vulnerability (THIS IS A WIDESPREAD THREAT RIGHT NOW).
* Enabled the Brute-Force protection option directly from the Settings page.
* Fixed window position to auto-adjust on small screens.

= 4.14.47 =
* Major upgrade to the protection for wp-login.php Brute-Force attempts.
* Fixes a bug in setting the permissions for read-only files so that they could still be cleaned.
* Fixes a minor bug with pass-by-reference which raises a fatal error in PHP v5.4.
* Enhanced the Examine File window with better styles and more info.
* Changed form submission of encrypted file lists to array values instead of keys.
* Fixes other minor bugs.
* Made the Examine File window sizable.
* Fixed a few small bugs and removed some old code.
* Added a link to my new twitter account.
* Re-purposed Quick Scan to just scan the most affected areas.
* Set the registration form to display by defaulted in the definition update section.
* Fixed a few small bugs in advanced features and directory depth determination.
* Fixed a session bug to display the last directory scanned.
* Fixed a few small cosmetic bugs for WP 3.8.
* Added Spanish translation, thanks to Jelena Kovacevic and Andrew Kurtis at webhostinghub.com.
* Updated string in the code and added a .pot file to be ready for translation into other languages.
* Added "Select All" checkbox to Quarantine and a new button to delete items from the Quarantine.
* Added a trace.php file for advanced session tracking.
* Fixed undefined index bug with menu_group item in settings array.
* Added support for multisite network admin menu and the ability to restrict admin access.
* Fixed a session bug in the progress bar related to the last release.
* Fixed a session bug that conflicted with jigoshop. (Thanks dragonflyfla)
* Fixed a few bug in the Whitelist definition feature.

= 3.07.06 =
* Added SSL support for definition updates and registration form.
* Upgraded the Whitelist feature so the it could not contain duplicates.
* Downgraded the WP-Login threat and changed it to an opt-in fix.
* Fixed a bug in the Add to Whitelist feature so the you do not need to update the definitions after whitelisting a file.
* Added ability to whitelist files.
* Fixed a major bug in yesterdays release broke the login page on some sites.
* Added a patch for the wp-login.php brute force attack that has been going around.
* Created a process to restore files from the Quarantine.
* Fixed a few other small bugs including path issues on Winblows server.

= 1.3.02.15 =
* Improved security on the Quarantine directory to fix the 500 error on some servers.
* Fixed count of Quarantined items.
* Added htaccess security to the Uploads directory.
* Linked the Quarantined items to the File Examiner.
* Added a scan category for Backdoor Scripts.
* Consolidated the Definition Types and added a Whitelist category.
* Completely redesigned the Definition Updates to handle incremental updates.
* Added "View Quarantine" to the menu.
* Enhanced Output Buffer to work with compression enabled (like ob_gzhandler).
* Moved the quarantine to the uploads directory to protect against blanket inclusion.
* Fixed Output Buffer issue for when ob_start has already been called.
* Enhanced the Automatic Fix process to handle bad directory permissions.
* Added more detailed error messages for different types of file errors.
* Improved overall error handling.
* Minor UI enhancements and a few bug fixes.
* Completely revamped the scan engine to handle large file systems with better error handling.
* Enhanced the results for the Automatic Fix process.
* Fixed a few other small bugs.
* Enhanced the iFrame for the File Viewer and Automatic Fix process.
* Improved error handling during the scan.
* Moved the File Viewer and Automatic Fix process into an iFrame to decrease scan time and memory usage.
* Enhanced the Automatic Fix process for better success with read-only files.
* Improved code cleanup process and general efficiency of the scan.
* Encoded definition update for better compatibility with some servers that have post limitation.
* Fixed XSS vulnerability.
* Changed registration to allow for multiple sites/keys to be registered under one user/email.
* Changed auto-update path to update threat level array for all new definition updates.
* Updated timthumb replacement patch to version 2.8.10 per WordPress.org plugins requirement.
* Fixed option to exclude directories so that the scan would not get stuck if omitted.
* Added support for winblows servers using BACKSLASH directory structures.
* Changed definition updates to write to the DB instead of a file.

= 1.2.03.23 =
* First versions available for WordPress (code removed, no longer compatible).

== Upgrade Notice ==

= 4.21.96 =
Fixed another Undefined Index Warning, improved timing of registration check, and added an option to manually recheck the registration status.

= 4.21.95 =
Fixed the Undefined Index Warning created in the last release.

= 4.21.94 =
Improved error handling for better scan completion and checked code for compatibility with WordPress 6.3 and ClassicPress 1.6.0.

= 4.21.93 =
Fixed the Undefined Index Warning when the Brute-Force Login Protection is invoked in certain situations and checked code for compatibility with WordPress 6.2.2 and ClassicPress 1.5.3.

= 4.21.92 =
Fixed the Uncaught Value Error in mb_regex_encoding, and other minor PHP Warnings about Undefined Indexes.

= 4.21.91 =
Fixed some HTML formatting issues and a JavaScript error in the scan engine.

= 4.21.90 =
Fixed array compatibility with older versions of PHP.

= 4.21.89 =
Added more late escapes and sanitizated all _SERVER variables and checked code for compatibility with ClassicPress 1.5.0.

= 4.21.88 =
Added late escapes to variables that were already escaped as requested by Code review team and fixed a PHP warning about is_dir.

= 4.21.87 =
Code review and cleanup, added more sanitization and fixed an error when attempting to unserialize an array.

= 4.21.86 =
Improved the removal of database injections when values are serialized, and fixed a vulnerability in using unserialize with Class Objects, as well as some other PHP warnings about undefined indexes.

= 4.21.85 =
Prevented infinite looping on recursive sub-directories and checked code for compatibility with WordPress 6.1.1 and ClassicPress 1.4.4.

= 4.21.84 =
Removed the no_error_reporting debug option and checked compatibility with WordPress 6.0.2 and ClassicPress 1.4.2.

= 4.21.83 =
Fixed XSS vulnerability, plus other minor improvements and compatibility with WordPress 6.0.1 and ClassicPress 1.4.2.

= 4.21.74 =
Updated code with various minor improvements to efficiency and compatibility with WordPress 6.0.

= 4.20.96 =
Fixed XSS vulnerability by removing unsanitized QUERY_STRING, cleaned up Quarantine code, and checked code for compatibility with WordPress 5.9.2.

= 4.20.95 =
Added more sanitization and validation to all user data entered for better security and checked code for compatibility with WordPress 5.9.

= 4.20.94 =
Fixed an XSS vulnerability and checked code for compatibility with WordPress 5.8.3.

= 4.20.93 =
Fixed undefined variable warning and updated code for compatibility with PHP version 8.0.

= 4.20.92 =
Added German translation, improved firewall compatibility with Apache, fixed session check in Site Health for REST API compatibility, and checked code compatibility with WordPress 5.8.1 and ClassicPress 1.3.1.

= 4.20.72 =
Updated registration form, fixed session check, and removed older code from WordPress Repository.

= 4.20.59 =
Various minor bug fixes, added Core Files Definitions for ClassicPress, and tweaked code for better compatibility with WordPress 5.7.2 and ClassicPress 1.2.0.

= 4.19.69 =
Fixed a JavaScript error caused by a new French translation and checked code for compatibility with WordPress 5.4.1.

= 4.19.68 =
Updated some external links and tweaked code for better compatibility with PHP 7.4 and WordPress 5.4.

= 4.19.50 =
Added even more error handling to the DB Scan for low memory_limit, modified the Directory Scan Depth to accept 0 as way to skip the Directory Scan, and added some Help tips to some of the options on the Settings page.

= 4.19.44 =
Updated links, added more error handling to the DB Scan, various performance improvements, fixed path to work on Windows servers and a few minor bugs causing PHP Notices, and weaked code for compatibility with WP 5.3 (latest release).

= 4.18.76 =
Cleaned up the Nonce Token code and Quarantine page, fixed recovery link, and added debugging for login errors plus WP head and footer Hooks.

= 4.18.74 =
Fixed a bug in the Nonce Token Errors that was created by changes in the last release.

= 4.18.71 =
Added wp_options table to the db_scan and fixed a few minor bugs in the quarantine view, and changed some wording and checked code for compatibility with WP 5.2.1 (latest release).

= 4.18.69 =
Added a Warning message about the vulnerability in the yuzo-related-post plugin,  updated the Quarantine interface with a re-scan / re-clean feature, fixed a bug in the scan depth array that would produce PHP Notices, changed some wording and other minor fomatting issues, and checked code for compatibility with WP 5.2 (latest release).

= 4.18.63 =
Fixed a major bug in the Firewall updates that could cause a False Positive lockout.

= 4.18.62 =
Fixed a few minor bugs and updated code for compatibility with WP 5.1.1 (latest release).

= 4.18.52 =
Added a whole new DB Scan category, updated Firewall landing page, removed some old code that was no longer needed, clear cache files before running the Complete Scan,, and updated code for compatibility with WP 5.0.2 (latest release).

= 4.17.69 =
Updated code for compatibility with WP 4.9.8, fixed PHP Notice and escaped single-quotes in translated strings.

= 4.17.68 =
Updated code for compatibility with WP 4.9.7, removed dashicon from Settings link and the broken vote WORKS link, and reordered priorety on fixing Known Threats.

= 4.17.58 =
Updated code for compatibility with WP 4.9.4, fixed dashicons sizing in css, add ability to update registration email from within the plugin settings, and cleaned up expired nonce tokens left behind from an older version.

= 4.17.57 =
Updated code for compatibility with WP 4.9.3, fixed registration form and alternate domain for definition updates to work on HTTPS, and fixed the wording on the Title Check error message.

= 4.17.44 =
Added Title check to make sure it does say you were hacked, updated code for compatibility with WP 4.8.3 and fixed Undefined variable error in Quarantine and an XSS vulnerability in nonce error output.

= 4.17.29 =
Changed the definition update URL to only use SSL when required, and updated PayPal form for better domestic IPN compatibility.

= 4.17.28 =
Added the Turkish translation thanks to Umut Can Alparslan, improved the auto update feature, and fixed the admin username change feature on multisite installs (Plus many other improvement from v4.16.X: see Changelog for details).

= 4.16.17 =
Removed Menu Item Placement Options that were deprecated in WP 4.5, Added firewall options for better compatibility with WP Firewall 2, and fixed an XSS vulnerability in the debug output of the nonce token (Plus many other improvement from v4.15.X: see Changelog for details).

= 4.15.16 =
Created automatic definition updates that include WordPress Core files, more improvements to the Brute-Force protection patch (Plus many other improvement from v4.14.X: see Changelog for details).

= 4.14.47 =
Major upgrade to the protection for Brute-Force attempts, and a bug fix for resetting the permissions of read-only files (Plus many other improvement from v3.X: see Changelog for details).

= 3.07.06 =
Added SSL support for definition updates and upgraded the Whitelist feature (Plus many other improvement from v1.3.X: see Changelog for details).

= 1.3.02.15 =
Improved security on the Quarantine directory to fix the 500 error on some servers (Plus many other improvement from v1.2.X: see Changelog for details).

= 1.2.03.23 =
First versions available for WordPress (code removed, no longer compatible).
