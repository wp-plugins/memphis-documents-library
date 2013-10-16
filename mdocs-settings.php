<?php
define('MDOCS_CATS', 'mdocs-cats.txt');
define('MDOCS_LIST', 'mdocs-list.txt');
define('MDOCS_DIR','/mdocs/');
define('MDOC_PATH',plugin_dir_path(__FILE__));
define('MDOC_URL',plugin_dir_url(__FILE__));
define('MDOCS_TIME_OFFSET', get_option('gmt_offset')*60*60);
define('MDOCS_ROBOTS','http://www.kingofnothing.net/memphis/robots/memphis-robots.txt');

$add_error = false;

function mdocs_register_settings() {
	//CREATE REPOSITORY DIRECTORY
	$upload_dir = wp_upload_dir();
	if(!is_dir($upload_dir['basedir'].'/mdocs/') && $upload_dir['error'] === false) mkdir($upload_dir['basedir'].'/mdocs/');
	elseif(!is_dir($upload_dir['basedir'].'/mdocs/') && $upload_dir['error'] !== false) mdocs_errors(__('Unable to create the directory "mdocs" which is needed by Memphis Documents Library. Is its parent directory writable by the server?'),'error');
	//CREATE MDOCS POST CATEGORY
	if(get_category_by_slug( 'mdocs_media' ) == false) wp_create_category('mdocs-media');
	//CREATE MDOCS PAGE
	$query = new WP_Query('pagename=mdocuments-library');	
	if(empty($query->posts) && empty($query->queried_object) ) {
		$mdocs_page = array(
			'post_title' => __('Documents'),
			'post_name' => 'mdocuments-library',
			'post_content' => '[mdocs]',
			'post_status' => 'publish',
			'post_author' => 1,
			'post_type' => 'page',
			'comment_status' => 'closed'
		);
		$mdocs_post_id = wp_insert_post( $mdocs_page );	
	}
	//REGISTER SAVED VARIABLES
	register_setting('mdocs-settings', 'mdocs-cats');
	add_option('mdocs-cats',array('mdocuments' => 'Documents'));
	if(is_string(get_option('mdocs-cats'))) update_option('mdocs-cats',array());
	register_setting('mdocs-settings', 'mdocs-list');
	add_option('mdocs-list',array());
	register_setting('mdocs-settings', 'mdocs-zip');
	add_option('mdocs-zip','mdocs-export.zip');
}

//ADD CONTENT TO DOCUMENTS PAGE
//[mdocs]
function mdocs_shortcode($att, $content=null) { mdocs_page(); }
add_shortcode( 'mdocs', 'mdocs_shortcode' );
//[mdocs_post_page]
function mdocs_post_page_shortcode($att, $content=null) { mdocs_post_page(); }
add_shortcode( 'mdocs_post_page', 'mdocs_post_page_shortcode' );
function mdocs_admin_script() {
	wp_register_style( 'mdocs-admin-style', MDOC_URL.'/style.php');
	wp_enqueue_style( 'mdocs-admin-style' );
	wp_register_script( 'mdocs-admin-script', MDOC_URL.'/mdocs-script.js');
	wp_enqueue_script('mdocs-admin-script');
	//FONT-AWESOME STYLE
	wp_register_style( 'mdocs-font-awesome2-style', '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css');
	wp_enqueue_style( 'mdocs-font-awesome2-style' );
	//WORDPRESS IRIS COLOR PICKER
	wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'mdocs-color-picker', plugins_url('mdocs-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
	
	
	mdocs_js_handle();
}

function mdocs_script() {
	wp_enqueue_script("jquery");
	wp_register_script( 'mdocs-script', MDOC_URL.'/mdocs-script.js');
	wp_enqueue_script('mdocs-script');
	wp_register_style( 'mdocs-style', MDOC_URL.'/style.php');
	wp_enqueue_style( 'mdocs-style' );
	//FONT-AWESOME STYLE
	//wp_register_style( 'mdocs-font-awesome1-style', '//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css');
	//wp_enqueue_style( 'mdocs-font-awesome1-style' );
	wp_register_style( 'mdocs-font-awesome2-style', '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css');
	wp_enqueue_style( 'mdocs-font-awesome2-style' );
}

?>