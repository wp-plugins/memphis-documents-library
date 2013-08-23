<?php
add_action('init', 'mdocs_localize');
$upload_dir = wp_upload_dir();
$mdocs_zip = get_option('mdocs-zip');
//PASS VARIABLES TO JAVASCRIPT
function mdocs_js_handle() {
	wp_localize_script( 'mdocs-admin-script', 'mdocs_js', array(
		'version_delete' => __("You are about to delete this version.  Once deleted you will lost this version of the file!\n\n'Cancel' to stop, 'OK' to delete."),
		'category_delete' => __("You are about to delete this category.  Any file in this category will be lost!\n\n'Cancel' to stop, 'OK' to delete."),
		'remove' => __('Remove'),
		'new_category' => __('New Category'),
		'leave_page' => __('Are you sure you want to navigate away from this page?'),
	));
}
function mdocs_localize() {
	global $upload_dir, $mdocs_zip;
	$query = new WP_Query('pagename=mdocuments-library');	
	$permalink = get_permalink($query->post->ID);
	if( strrchr($permalink, '?page_id=')) $mdocs_link = '/'.strrchr($permalink, '?page_id=');
	else $mdocs_link = '/'.$query->post->post_name.'/';
	define('MDOCS_ZIP_STATUS_OK',__('Memphis Documents Library has an export file on this WordPress instance it was created on '.gmdate('F jS Y \a\t g:i A',@filemtime($upload_dir['basedir'].'/mdocs/'.$mdocs_zip)+MDOCS_TIME_OFFSET).'.<br><br>Click <a href="'.$upload_dir['baseurl'].'/mdocs/'.$mdocs_zip.'" tiltle="Old Export File">here</a> to download this version of the export file.'));
	define('MDOCS_ZIP_STATUS_FAIL',__('Memphis Documents Library has no export file on this WordPress instance.  You may want to create an export file now.'));
	define('MDOCS_DEFAULT_DESC', __('This file is part of the Documents Library.  The Documents Library can be found by clicking this link').' <a href="'.$mdocs_link.'">'.__('Goto Documents Library').'</a>.');
	define('MDOCS_DOWNLOAD_MSG',__('Check this file out').' <b>'.$filename. '</b>.  '.  __('Download it from').' <b>'.get_bloginfo('name').'</b>.<br><sup>'.__('powered by Memphis Documents Library').'</sup>');
	
	//ERRORS
	define('MDOCS_ERROR_1',__('No file was uploaded, please try again.'));
	define('MDOCS_ERROR_2',__('Sorry, this file type is not permitted for security reasons, contact your administrator for more details.<br>If you are running Multisite you can add this file type from the Settings menu of the Network Admin.'));
	define('MDOCS_ERROR_3',__('No categories found.  The upload process can not proceed.'));
	define('MDOCS_ERROR_4',__('Data was not submitted.  The submit process is out of sync, please refresh your browser and try again.'));
	define('MDOCS_ERROR_5', __('File Upload Error.  Please try again.'));
	define('MDOCS_ERROR_6', __('You are already at the most recent version of this document.'));
}
?>