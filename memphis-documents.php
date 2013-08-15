<?php
/*
 
Plugin Name: Memphis Documents Library
Plugin URI: http://
Description: A documents repository for WordPress. 
Author: Ian Howatson
Version: 1.1
Author URI: http://www.kingofnothing.net/
Date: 09/15/2013

Copyright 2013 Ian Howatson  (email : ian.howatson@kingofnothing.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
include 'mdocs-settings.php';
include 'mdocs-functions.php';
include 'mdocs-page.php';
include 'mdocs-dashboard.php';
include 'mdocs-import.php';
include 'mdocs-export.php';
include 'mdocs-upload.php';
include 'mdocs-categories.php';
include 'mdocs-downloads.php';
include 'mdocs-versions.php';
include 'mdocs-localization.php';
mdocs_nonce();
add_action('admin_menu', 'mdocs_dashboard_menu');
add_action( 'wp_enqueue_scripts', 'mdocs_script' );
add_action('wp_footer', 'mdocs_social_scripts');
add_action('admin_footer', 'mdocs_social_scripts');
?>