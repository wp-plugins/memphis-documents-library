<?php
define('MDOCS_CATS', 'mdocs-cats.txt');
define('MDOCS_LIST', 'mdocs-list.txt');
define('MDOCS_DIR','/mdocs/');
define('MDOC_PATH',plugin_dir_path(__FILE__));
define('MDOC_URL',plugin_dir_url(__FILE__));
define('MDOCS_TIME_OFFSET', get_option('gmt_offset')*60*60);
define('MDOCS_ROBOTS','http://www.kingofnothing.net/memphis/robots/memphis-robots.txt');
define('MDOCS_UPDATE', '<div class="mdocs-updated">'.__('Updated').'</div>');
define('MDOCS_NEW', '<div class="mdocs-new">'.__('New').'</div>');
define('MDOCS_UPDATE_SMALL', '<div class="mdocs-updated-small">'.__('Updated').'</div>');
define('MDOCS_NEW_SMALL', '<div class="mdocs-new-small">'.__('New').'</div>');
define('MDOCS_CURRENT_TIME', date('Y-m-d H:i:s', time()+MDOCS_TIME_OFFSET));
//define('MDOCS_VERSION', );
$add_error = false;

function mdocs_register_settings() {
	//CREATE REPOSITORY DIRECTORY
	$upload_dir = wp_upload_dir();
	$is_read_write = mdocs_check_read_write();
	if($is_read_write) {
		if(!file_exists($upload_dir['basedir'].'/mdocs/.htaccess')) {
			$upload_dir = wp_upload_dir();
			$htaccess = $upload_dir['basedir'].'/mdocs/.htaccess';
			$fh = fopen($htaccess, 'w');
			$rules = "Deny from all\n";
			$rules .= "Options +Indexes\n";
			$rules .= "IndexOptions FancyIndexing FoldersFirst SuppressIcon\n";
			$rules .= "Allow from none\n";
			fwrite($fh, $rules);
			fclose($fh);
			chmod($htaccess, 0660);
		}
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
		//PATCHES
		//2.1 PATCH 1
		register_setting('mdocs-settings', 'mdocs-2-1-patch-1');
		add_option('mdocs-2-1-patch-1',false);
		if(get_option('mdocs-2-1-patch-1') == false) {
			$the_list = get_option('mdocs-list');
			foreach(get_option('mdocs-list') as $index => $the_mdoc) {
				if(!is_array($the_mdoc['ratings'])) {
					$the_mdoc['ratings'] = array();
					$the_mdoc['rating'] = 0;
					$the_list[$index] = $the_mdoc;
				}
				if(!key_exists('rating', $the_list)) {
					$the_mdoc['rating'] = 0;
					$the_list[$index] = $the_mdoc;
				}
			}
			update_option('mdocs-list', $the_list);
			update_option('mdocs-2-1-patch-1', true);
		}
		//REGISTER SAVED VARIABLES
		register_setting('mdocs-settings', 'mdocs-cats');
		add_option('mdocs-cats',array('mdocuments' => 'Documents'));
		if(is_string(get_option('mdocs-cats'))) update_option('mdocs-cats',array());
		register_setting('mdocs-settings', 'mdocs-list');
		add_option('mdocs-list',array());
		register_setting('mdocs-settings', 'mdocs-zip');
		add_option('mdocs-zip','mdocs-export.zip');
		//GLOBAL VARIABLES
		register_setting('mdocs-global-settings', 'mdocs-list-type');
		add_option('mdocs-list-type','small');
		register_setting('mdocs-global-settings', 'mdocs-list-type-dashboard');
		add_option('mdocs-list-type-dashboard','large');
		register_setting('mdocs-global-settings', 'mdocs-hide-all-files-non-members');
		add_option('mdocs-hide-all-files-non-members', false);
		register_setting('mdocs-global-settings', 'mdocs-hide-all-posts-non-members');
		add_option('mdocs-hide-all-posts-non-members', false);
		register_setting('mdocs-global-settings', 'mdocs-hide-all-posts-non-members-default');
		add_option('mdocs-hide-all-posts-non-members-default', false);
		register_setting('mdocs-global-settings', 'mdocs-hide-all-files');
		add_option('mdocs-hide-all-files', false);
		register_setting('mdocs-global-settings', 'mdocs-hide-all-posts');
		add_option('mdocs-hide-all-posts', false);
		register_setting('mdocs-global-settings', 'mdocs-hide-all-posts-default');
		add_option('mdocs-hide-all-posts-default', false);
		register_setting('mdocs-global-settings', 'mdocs-show-downloads');
		add_option('mdocs-show-downloads', true);
		register_setting('mdocs-global-settings', 'mdocs-show-author');
		add_option('mdocs-show-author', true);
		register_setting('mdocs-global-settings', 'mdocs-show-version');
		add_option('mdocs-show-version', true);
		register_setting('mdocs-global-settings', 'mdocs-show-update');
		add_option('mdocs-show-update', true);
		register_setting('mdocs-global-settings', 'mdocs-show-social');
		add_option('mdocs-show-social', true);
		register_setting('mdocs-global-settings', 'mdocs-show-ratings');
		add_option('mdocs-show-ratings', true);
		register_setting('mdocs-global-settings', 'mdocs-download-color-normal');
		add_option('mdocs-download-color-normal', '#d14836');
		register_setting('mdocs-global-settings', 'mdocs-download-color-hover');
		update_option('mdocs-download-color-hover', '#c34131');
		register_setting('mdocs-global-settings', 'mdocs-show-new-banners');
		add_option('mdocs-show-new-banners', true);
		register_setting('mdocs-global-settings', 'mdocs-time-to-display-banners');
		add_option('mdocs-time-to-display-banners', 14);
			
		//unregister_setting('mdocs-patch-vars', 'mdocs-v2-0-patch-var-1');
		//delete_option('mdocs-v2-0-patch-var-1');
		
	} else mdocs_errors(__('Unable to create the directory "mdocs" which is needed by Memphis Documents Library. Its parent directory is not readable/writable by the server?'),'error');
	
}

//ADD CONTENT TO DOCUMENTS PAGE
//[mdocs]
function mdocs_shortcode($att, $content=null) { mdocs_the_list(); }
add_shortcode( 'mdocs', 'mdocs_shortcode' );
//[mdocs_post_page]
function mdocs_post_page_shortcode($att, $content=null) {
	mdocs_post_page($att);
}
add_shortcode( 'mdocs_post_page', 'mdocs_post_page_shortcode' );

function mdocs_admin_script() {
	wp_enqueue_script("jquery");
	//JQUERY UI
	wp_register_style( 'mdocs-jquery-ui-style', '//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
	wp_enqueue_style( 'mdocs-jquery-ui-style' );
	wp_enqueue_script( 'mdocs-jquery-ui-script', '//code.jquery.com/ui/1.10.3/jquery-ui.js' );
	//MEMPHIS DOCS
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
	//JQUERY UI
	wp_register_style( 'mdocs-jquery-ui-style', '//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
	wp_enqueue_style( 'mdocs-jquery-ui-style' );
	wp_enqueue_script( 'mdocs-jquery-ui-script', '//code.jquery.com/ui/1.10.3/jquery-ui.js' );
	//MEMPHIS DOCS 
	wp_register_script( 'mdocs-script', MDOC_URL.'mdocs-script.js');
	wp_enqueue_script('mdocs-script');
	wp_register_style( 'mdocs-style', MDOC_URL.'style.php');
	wp_enqueue_style( 'mdocs-style' );
	//FONT-AWESOME STYLE
	wp_register_style( 'mdocs-font-awesome2-style', '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css');
	wp_enqueue_style( 'mdocs-font-awesome2-style' );
}

function mdocs_document_ready_wp() {
?>
<script type="application/x-javascript">
		jQuery( document ).ready(function() {
			mdocs_wp('<?php echo MDOC_URL; ?>');
		});	
	</script>
<?php
}
function mdocs_document_ready_admin() {
?>
<script type="application/x-javascript">
		jQuery( document ).ready(function() {
			mdocs_admin('<?php echo MDOC_URL; ?>');
		});	
	</script>
<?php
}
function mdocs_ie_compat() { ?><meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" /><?php }
function mdocs_send_headers() {
	//SET SORT VALUES SITE
	if(isset($_POST['mdocs-sort-type'])) setcookie('mdocs-sort-type-site', $_POST['mdocs-sort-type']); 
	if(isset($_POST['mdocs-sort-range'])) setcookie('mdocs-sort-range-site', $_POST['mdocs-sort-range']);
	mdocs_ie_compat();
}
function mdocs_send_headers_dashboard() {
	//SET SORT VALUES DASHBOARD
	if(isset($_POST['mdocs-sort-type'])) setcookie('mdocs-sort-type-dashboard', $_POST['mdocs-sort-type']); 
	if(isset($_POST['mdocs-sort-range'])) setcookie('mdocs-sort-range-dashboard', $_POST['mdocs-sort-range']);
	//mdocs_ie_compat();
}

?>