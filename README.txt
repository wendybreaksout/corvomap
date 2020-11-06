=== Plugin Name ===
Contributors: wkempferjr, jnobles
Donate link: http://guestaba.com/donate
Tags: crowd sourcing, crowdmap, crowd source map
Requires at least: 4.0
Tested up to: 4.3.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

CorvoMap enables the display and management of crowd sourcing maps within WordPress.

== Description ==

CorvoMap provides a way for people to collaborate on the locations of things. For example, if the Anytown
Department Transportation wants to get public input on where to put speed bumps, a CorvoMap enables that
by displaying map on which visitors can drop pins to represent their suggested location. Other visitors
vote for or support the first user's proposal and also make comments. As input is gathered, heat map
cluster marker view of the map help visualize concentrations of both proposals and support for each one.


CorvoMap can also be used to display where things are. For example, it could be used to map and document
where all of the highest carbon emitters are located within a given region.

= Latest Updates =
This is the initial release of this plugin.

= Shortcodes =

The shortcode "[corvomap]" is provided that permits a CorvoMap to be displayed just about anywhere
on your site. Otherwise, maps may be displayed at the path /maps/slug.

= Custom Post Types =

The CorvoMap plugin utilized two custom post types, Maps and Proposals. The Map custom post type
holds the parameters for the map, including the map center, default zoom, and map input bounds. The
Proposals custom post type contains the information in a user proposal. When a user drops a pin on
a map and submits the proposal form, a Proposal post is created. Supports and comments for the proposal
are simply WordPress comments associated with the Proposal.


== Installation ==

From the Wordpress Dashboard Plugins->Add New page:

1. Search for "CrowdMap". Find Hospality from Guestaba in the listing and clic its "Install Now". 
1. Click the "Activate Now" once the plugin is downloaded and installed.

or 

1. Download the plugin to a local folder on your computer from the WordPress plugin repository.
1. On the Plugin->Add New page, click the "Upload Plugin" button, navigate to wherever you have downloaded plugin zip file,
and then select it to start the upload. 
1.  Click the "Activate Now" link to activate.

The plugin can also by installed by unzipping the crowdmap.zip file to your wp-content/plugins folder. 

Once the plugin is installed, you can find its settings page by clicking the "action" link found in the CrowdMap listing in the 
dashboard Plugins page, or got to Settings->CrowdMap. See our [support site](http://support.guestaba.com/support/home) to find out how
configure the plugin and begin entering information about your maps and meeting spaces. 


== Frequently Asked Questions ==

See our the most up-to-date version of our [help documentation](http://support.guestaba.com/support/home)


== Screenshots ==

1. A map ...

== Changelog ==

= 1.0.0 =
* The initial version.



== Upgrade Notice ==

= 1.0.0 = 
Initial version.



