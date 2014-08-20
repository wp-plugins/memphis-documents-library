<?php
$upload_dir = wp_upload_dir();
$mdocs_zip = get_option('mdocs-zip');
//PASS VARIABLES TO JAVASCRIPT
function mdocs_js_handle($handle) {
	wp_localize_script( $handle, 'mdocs_js', array(
		'version_delete' => __("You are about to delete this version.  Once deleted you will lost this version of the file!\n\n'Cancel' to stop, 'OK' to delete."),
		'category_delete' => __("You are about to delete this category.  Any file in this category will be lost!\n\n'Cancel' to stop, 'OK' to delete."),
		'remove' => __('Remove'),
		'new_category' => __('New Category'),
		'leave_page' => __('Are you sure you want to navigate away from this page?'),
		'category_support' => __('Currently Memphis Documents Library only supports two sub categories.'),
		'restore_warning' => __('Are you sure you want continue.  All you files, posts and directories will be delete.'),
		'levels'=> 2,
		'blog_id' => get_current_blog_id(),
		'plugin_url' => plugins_url().'/memphis-documents-library/',
		'ajaxurl' => admin_url( 'admin-ajax.php' ), 
	));
}
// PROCESS AJAX REQUESTS
add_action( 'wp_ajax_nopriv_myajax-submit', 'mdocs_ajax_processing' );
add_action( 'wp_ajax_myajax-submit', 'mdocs_ajax_processing' );
function mdocs_ajax_processing() {
	switch($_POST['type']) {
		case 'file':
			mdocs_load_preview();
			break;
		case 'img':
			mdocs_load_preview();
			break;
		case 'show':
			mdocs_load_preview();
			break;
		case 'add-mime':
			mdocs_update_mime();
			break;
		case 'remove-mime':
			mdocs_update_mime();
			break;
		case 'restore-mime':
			mdocs_update_mime();
			break;
		case 'restore':
			mdocs_restore_default();
			break;
	}
	exit;
}
// this hook is fired if the current viewer is not logged in
function mdocs_get_inline_css() {
	$num_show = 0;
	if(get_option('mdocs-show-downloads')==1) $num_show++;
	if(get_option('mdocs-show-author')==1) $num_show++;
	if(get_option('mdocs-show-version')==1) $num_show++;
	if(get_option('mdocs-show-update')==1) $num_show++;
	if(get_option('mdocs-show-ratings')==1) $num_show++;
	if($num_show==5) $title_width = '35%';
	if($num_show==4) $title_width = '45%';
	if($num_show==3) $title_width = '55%';
	if($num_show==2) $title_width = '65%';
	if($num_show==1) $title_width = '75%';
	$download_button_color = get_option('mdocs-download-text-color-normal');
	$download_button_bg = get_option('mdocs-download-color-normal'); 
	$download_button_hover_color = get_option('mdocs-download-text-color-hover');
	$download_button_hover_bg = get_option('mdocs-download-color-hover');
	$set_inline_style = "
		.mdocs-list-table #title { width: $title_width !important }
		.mdocs-download-btn-config:hover { background: $download_button_hover_bg; color: $download_button_hover_color; }
		.mdocs-download-btn-config { color: $download_button_color; background: $download_button_bg ; }
		.mdocs-download-btn, .mdocs-download-btn:active { color: $download_button_color !important; background: $download_button_bg !important;  }
		.mdocs-download-btn:hover { background: $download_button_hover_bg !important; color: $download_button_hover_color !important;}
	";
	return $set_inline_style;
}
function mdocs_localize() {
	global $upload_dir, $mdocs_zip;
	$query = new WP_Query('pagename=mdocuments-library');	
	$permalink = get_permalink($query->post->ID);
	if( strrchr($permalink, '?page_id=')) $mdocs_link = site_url().'/'.strrchr($permalink, '?page_id=');
	else $mdocs_link = site_url().'/'.$query->post->post_name.'/';
	define('MDOCS_ZIP_STATUS_OK',__('Memphis Documents Library has an export file on this WordPress instance it was created on '.gmdate('F jS Y \a\t g:i A',@filemtime($upload_dir['basedir'].'/mdocs/'.$mdocs_zip)+MDOCS_TIME_OFFSET).'.<br><br><!--Click <a href="'.$upload_dir['baseurl'].'/mdocs/'.$mdocs_zip.'" tiltle="Old Export File">here</a> to download this version of the export file.-->'));
	define('MDOCS_ZIP_STATUS_FAIL',__('Memphis Documents Library has no export file on this WordPress instance.  You may want to create an export file now.'));
	define('MDOCS_DEFAULT_DESC', __('This file is part of the Documents Library.'));	
	//ERRORS
	define('MDOCS_ERROR_1',__('No file was uploaded, please try again.'));
	define('MDOCS_ERROR_2',__('Sorry, this file type is not permitted for security reasons, contact your administrator for more details.<br>If you are running Multisite you can add this file type from the Settings menu of the Network Admin.'));
	define('MDOCS_ERROR_3',__('No categories found.  The upload process can not proceed.'));
	define('MDOCS_ERROR_4',__('Data was not submitted.  The submit process is out of sync, please refresh your browser and try again.'));
	define('MDOCS_ERROR_5', __('File Upload Error.  Please try again.'));
	define('MDOCS_ERROR_6', __('You are already at the most recent version of this document.'));
}
?>