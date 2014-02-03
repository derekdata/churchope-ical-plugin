churchope-ical-plugin
=====================

WordPress plugin to generate iCalendar files from Event posts.

This plugin allows for the import of the WordPress Event posts into into Google Calendar, Apple iCal, Android Calendar, Microsoft Outlook, etc.

This plugin is specifically aligned with the Churchope WordPress Theme; though it could be adapted for other themes as well.

This plugin should work on PHP 5.3.x or greater.

Installation
============

This section describes how to install the plugin and get it working.

e.g.

1. Upload all files to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure you have the proper timezone set in the WordPress settings under "Settings-->General".  For best results, choose the closest city in your timezone rather than a UTC/GMT offset.


Using the Plugin
================

When the plugin is activated, it will register at https://yoursite.com/feed/events-ical

If you have changed the path to feeds in your WordPress installation, replace /feed/ with the correct path to your feeds.

Other information
=================

CI: https://travis-ci.org/derekdata/churchope-ical-plugin

WordPress Plugin Directory: http://wordpress.org/plugins/churchope-theme-icalendar-generator/