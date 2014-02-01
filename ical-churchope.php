<?php
/*
Plugin Name: CHURCHOPE Theme iCal Generator
Plugin URI: http://wordpress.com/
Description: Creates an iCal feed from the calendar functions in the CHURCHOPE Wordpress Theme.  Could be modified to work with any custom event post type; contact the author at web.development.help@gmail.com for details.
Version: 1.0
Author: Derek
Author URI: https://github.com/derekdata
*/

/*
Copyright 2014 Derek - web.development.help@gmail.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/
include_once('classes/ChurchopeICalGenerator.php');
include_once('classes/WordPressFunctionProxy.php');
include_once('classes/ChurchHopeFunctionProxy.php');

/**
 * This file is responsible for constructing the plugin and injecting needed dependencies.
 */

$timezoneString=get_option( 'timezone_string' );

$blogName = strip_tags(get_bloginfo('name'));
$blogUrl = strip_tags(get_bloginfo('home'));

$widgetEvent = new Widget_Event();

$churchopeICalGenerator = new ChurchopeICalGenerator($timezoneString, new ChurchopeFunctionProxy($widgetEvent), $blogName, $blogUrl, new WordPressFunctionProxy(), SHORTNAME);
$churchopeICalGenerator->addGenerateICalDataFeedAction();

?>