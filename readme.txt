=== Copify ===
Version: 1.0.7
Contributors: robmcvey
Tags: blog writers, automatic blogging, post writers, auto blogging, content, copywriting, copywriters, blogging, writers, writing, seo
Requires at least: 3.2.0
Tested up to: 3.9.0
Stable tag: trunk
Github Stable tag: master
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically publish unique, relevant content every week from Copify's team of professional writers.

== Description ==

If you don’t blog regularly, you’re not alone. Most companies struggle to find the time and inspiration to update their blog, which means they are missing out on a real opportunity.

Statistics from content marketing platform HubSpot reveal that blogs which are updated regularly get 55% more visitors and 97% more links than those which aren’t.

This is where Copify can help. By installing our plugin and signing up for one of our [monthly packages](http://copify.com/blog-packages), we’ll deliver a number of blog posts every week. These posts can be pushed directly to your WordPress dashboard for quick and easy publishing.

== Requirements ==

* PHP cURL extension
* PHP JSON extension

== Installation ==

1. Unzip and upload the `copify` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu.
3. Enter your API key which can be found on the settings page of your Copify account.

== Screenshots ==

1. Add a blog package to your Copify account and we'll deliver a number of posts to you every month.
2. Install the plugin and your blog content will begin to appear in WordPress, where you can review it.
3. Select "Auto-publish" and we'll publish your blog posts automatically with an image!

== Changelog ==

= 1.0.7 =
* Allows user to choose to publish blog with the supplied royalty free image (Blog package customers only)

= 1.0.6 =
* Fixes issue with other plugins calling methods in wp-admin/includes/plugin.php such as is_plugin_active()

= 1.0.5 =
* Plugin now allows Copify to publish Creative Commons License photo as "Featured Image" when blog package has auto-publish enabled.

= 1.0.4 =
* When auto-publish is set to "On" Copify will automatically publish blog posts.
