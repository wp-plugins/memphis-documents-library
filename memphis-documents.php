<?php
/*
Plugin Name: Memphis Documents Library
Plugin URI: http://www.kingofnothing.net/memphis-documents-library/
Description: A documents repository for WordPress. 
Author: Ian Howatson
Version: 2.6.8.1.1
Author URI: http://www.kingofnothing.net/
Date: 10/20/2014

Copyright 2014 Ian Howatson  (email : ian@howatson.net)

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
include 'mdocs-the-list.php';
include 'mdocs-file-info-large.php';
include 'mdocs-file-info-small.php';
include 'mdocs-social.php';
include 'mdocs-dashboard.php';
include 'mdocs-import.php';
include 'mdocs-export.php';
include 'mdocs-upload.php';
include 'mdocs-categories.php';
include 'mdocs-downloads.php';
include 'mdocs-versions.php';
include 'mdocs-batch-upload.php';
include 'mdocs-settings-page.php';
include 'mdocs-shortcodes.php';
include 'mdocs-localization.php';
include 'mdocs-browser-compatibility.php';
include 'mdocs-widgets.php';
include 'mdocs-doc-preview.php';
include 'mdocs-update-mime.php';
include 'mdocs-restore-defaults.php';
mdocs_nonce();
if(!headers_sent() && stripos($_SERVER['REQUEST_URI'], '/feed') === false) add_action('send_headers', 'mdocs_send_headers');
elseif (!is_numeric(stripos($_SERVER['REQUEST_URI'], '/feed'))) {
	$message = sprintf('Premature output is preventing Memphis Documents Library from working properly. Outpust has started in %s on line %d.', $file, $line);
	echo '<div style="border: 1em solid red; background: #fff; color: #f00; margin:2em; padding: 1em;">', htmlspecialchars($message), '</div>';
	trigger_error($message);
	die();	
}
if ( is_admin()) add_action('admin_init', 'mdocs_send_headers_dashboard');
add_action('init', 'mdocs_localize');
add_action('admin_menu', 'mdocs_dashboard_menu');
add_action( 'wp_enqueue_scripts', 'mdocs_script' );
add_action('wp_footer', 'mdocs_document_ready_wp');
add_action('admin_footer', 'mdocs_document_ready_admin');
add_action( 'widgets_init', 'mdocs_widgets' );
?>