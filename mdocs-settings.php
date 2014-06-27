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
define('MDOCS_UPDATE_SMALL', '<span class="mdocs-updated-small">'.__('Updated').'</span>');
define('MDOCS_NEW_SMALL', '<span class="mdocs-new-small">'.__('New').'</span>');
define('MDOCS_CURRENT_TIME', date('Y-m-d H:i:s', time()+MDOCS_TIME_OFFSET));
//define('MDOCS_VERSION', );
$add_error = false;
$mdocs_img_types = array('jpeg','jpg','png','gif');
$mdocs_input_text_bg_colors = array('#f1f1f1','#e5eaff','#efffe7','#ffecdc','#ffe9fe','#ff5000','#00ff20');
$mdocs_options = array();

function mdocs_register_settings() {
	global $mdocs_options;
	//CREATE REPOSITORY DIRECTORY
	$upload_dir = wp_upload_dir();
	$is_read_write = mdocs_check_read_write();
	if($is_read_write) {
		$upload_dir = wp_upload_dir();
		register_setting('mdocs-patch-vars', 'mdocs-v2-5-patch-var-1');
		add_action('mdocs-v2-5-patch-var-1',false);
		if(get_option('mdocs-v2-5-patch-var-1') == false) {
			$num_cats = 0;
			foreach( get_option('mdocs-cats') as $index => $cat ){ $num_cats++;}
			update_option('mdocs-num-cats',$num_cats);
			add_action( 'admin_notices', 'mdocs_v2_5_admin_notice_v1' );
			update_option('mdocs-v2-5-patch-var-1',true);
		}
		register_setting('mdocs-patch-vars', 'mdocs-v2-4-patch-var-1');
		add_option('mdocs-v2-4-patch-var-1',false);
		if(get_option('mdocs-v2-4-patch-var-1') == false) {
			$mdocs_cats = get_option('mdocs-cats');
			$new_mdocs_cats = array();
			foreach($mdocs_cats as $index => $cat) array_push($new_mdocs_cats, array('slug' => $index,'name' => $cat, 'parent' => '', 'children' => array(), 'depth' => 0));
			update_option('mdocs-cats', $new_mdocs_cats);
			update_option('mdocs-v2-4-patch-var-1', true);
			add_action( 'admin_notices', 'mdocs_v2_4_admin_notice_v1' );
		}
		register_setting('mdocs-patch-vars', 'mdocs-v2-3-1-patch-var-1');
		add_option('mdocs-v2-3-1-patch-var-1',false);
		if(get_option('mdocs-v2-3-1-patch-var-1') == false) {
			$htaccess = $upload_dir['basedir'].'/mdocs/.htaccess';
			$fh = fopen($htaccess, 'w');
			update_option('mdocs-htaccess', "Deny from all\nOptions +Indexes\nAllow from .google.com");
			$mdocs_htaccess = get_option('mdocs-htaccess');
			fwrite($fh, $mdocs_htaccess);
			fclose($fh);
			chmod($htaccess, 0660);
			update_option('mdocs-v2-3-1-patch-var-1', true);
			add_action( 'admin_notices', 'mdocs_v2_2_1_admin_notice_v1' );
		}
		if(!file_exists($upload_dir['basedir'].'/mdocs/.htaccess')) {
			if(!file_exists($upload_dir['basedir'].'/mdocs/')) mkdir($upload_dir['basedir'].'/mdocs/');
			$htaccess = $upload_dir['basedir'].'/mdocs/.htaccess';
			$fh = fopen($htaccess, 'w');
			$mdocs_htaccess = get_option('mdocs-htaccess');
			fwrite($fh, $mdocs_htaccess);
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
		//REGISTER SAVED VARIABLES ARRAY
		//register_setting('mdocs-core-settings', 'mdocs-options');
		//add_option('mdocs-options', array());
		//$mdocs_options = get_option('mdocs-options');
		//if(empty($mdocs_options)) {
			//$mdocs_options['mdocs-cats'] = get_option('mdocs-cats');
		//}
		
		//REGISTER SAVED VARIABLES
		register_setting('mdocs-settings', 'mdocs-cats');
		add_option('mdocs-cats',array('slug' => 'mdocuments','name' => 'Documents', 'parent' => '', 'children' => array(), 'depth' => 0));
		if(is_string(get_option('mdocs-cats'))) update_option('mdocs-cats',array());
		register_setting('mdocs-settings', 'mdocs-list');
		add_option('mdocs-list',array());
		register_setting('mdocs-settings', 'mdocs-total-cats');
		add_option('mdocs-total-cats',0);
		register_setting('mdocs-settings', 'mdocs-zip');
		add_option('mdocs-zip','mdocs-export.zip');
		register_setting('mdocs-top-downloads', 'mdocs-top-downloads');
		add_option('mdocs-top-downloads',10);
		register_setting('mdocs-top-downloads', 'mdocs-top-rated');
		add_option('mdocs-top-rated',10);
		register_setting('mdocs-top-downloads', 'mdocs-last-updated');
		add_option('mdocs-last-updated',10);
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
		add_option('mdocs-download-color-hover', '#c34131');
		register_setting('mdocs-global-settings', 'mdocs-download-text-color-normal');
		add_option('mdocs-download-text-color-normal', '#ffffff');
		register_setting('mdocs-global-settings', 'mdocs-download-text-color-hover');
		add_option('mdocs-download-text-color-hover', '#ffffff');
		register_setting('mdocs-global-settings', 'mdocs-show-new-banners');
		add_option('mdocs-show-new-banners', true);
		register_setting('mdocs-global-settings', 'mdocs-time-to-display-banners');
		add_option('mdocs-time-to-display-banners', 14);
		register_setting('mdocs-global-settings', 'mdocs-doc-preview');
		add_option('mdocs-doc-preview', false);
		register_setting('mdocs-global-settings', 'mdocs-sort-type');
		add_option('mdocs-sort-type','modified');
		register_setting('mdocs-global-settings', 'mdocs-sort-style');
		add_option('mdocs-sort-style','desc');
		register_setting('mdocs-global-settings', 'mdocs-default-content');
		add_option('mdocs-default-content','description');
		register_setting('mdocs-global-settings', 'mdocs-show-description');
		add_option('mdocs-show-description',true);
		register_setting('mdocs-global-settings', 'mdocs-show-preview');
		add_option('mdocs-show-preview', true);
		register_setting('mdocs-global-settings', 'mdocs-htaccess');
		add_option('mdocs-htaccess', "Deny from all\nOptions +Indexes\nAllow from .google.com");
		//Old htaccess file
		//add_option('mdocs-htaccess', "Deny from all\nOptions +Indexes\nIndexOptions FancyIndexing\nFoldersFirst SuppressIcon\nAllow from .google.com");
		// PATCHES
		//unregister_setting('mdocs-patch-vars', 'mdocs-v2-0-patch-var-1');
		//delete_option('mdocs-v2-0-patch-var-1');
		
	} else mdocs_errors(__('Unable to create the directory "mdocs" which is needed by Memphis Documents Library. Its parent directory is not readable/writable by the server?'),'error');
	
}

//ADD CONTENT TO DOCUMENTS PAGE
//[mdocs]
function mdocs_shortcode($att, $content=null) { mdocs_the_list($att); }
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
	wp_register_style( 'mdocs-admin-style', MDOC_URL.'/style.css');
	wp_enqueue_style( 'mdocs-admin-style' );
	wp_register_script( 'mdocs-admin-script', MDOC_URL.'/mdocs-script.js');
	wp_enqueue_script('mdocs-admin-script');
	mdocs_inline_css('mdocs-admin-style');
	//FONT-AWESOME STYLE
	wp_register_style( 'mdocs-font-awesome2-style', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css');
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
	wp_register_style( 'mdocs-style', MDOC_URL.'style.css');
	wp_enqueue_style( 'mdocs-style' );
	mdocs_inline_css('mdocs-style');
	//FONT-AWESOME STYLE
	wp_register_style( 'mdocs-font-awesome2-style', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css');
	wp_enqueue_style( 'mdocs-font-awesome2-style' );
}

function mdocs_inline_css($style_name) {
	$set_inline_style = mdocs_get_inline_css();
	wp_add_inline_style( $style_name, $set_inline_style );
}
function mdocs_document_ready_wp() {
?>
<script type="application/x-javascript">
		jQuery( document ).ready(function() {
			mdocs_wp('<?php echo MDOC_URL; ?>', '<?php echo ABSPATH; ?>');
		});	
	</script>
<?php
}
function mdocs_document_ready_admin() {
	if(!is_network_admin()) {
?>
<script type="application/x-javascript">
		jQuery( document ).ready(function() {
			mdocs_admin('<?php echo MDOC_URL; ?>', '<?php echo ABSPATH; ?>');
		});	
	</script>
<?php
	}
}
function mdocs_ie_compat() { ?><meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7; IE=EDGE" /><?php }
function mdocs_send_headers() {
	//SET SORT VALUES SITE
	if(isset($_POST['mdocs-sort-type'])) setcookie('mdocs-sort-type-site', $_POST['mdocs-sort-type']); 
	if(isset($_POST['mdocs-sort-range'])) setcookie('mdocs-sort-range-site', $_POST['mdocs-sort-range']);
	//$get_browser = new mdocs_browser_compatibility();
	//$browser = $get_browser->get_browser();
	//if($browser['name'] == 'Internet Explorer') mdocs_ie_compat();
}
function mdocs_send_headers_dashboard() {
	//SET SORT VALUES DASHBOARD
	if(isset($_POST['mdocs-sort-type'])) setcookie('mdocs-sort-type-dashboard', $_POST['mdocs-sort-type']); 
	if(isset($_POST['mdocs-sort-range'])) setcookie('mdocs-sort-range-dashboard', $_POST['mdocs-sort-range']);
	//mdocs_ie_compat();
}
function mdocs_v2_2_1_admin_notice_v1() {
    ?>
    <div class="updated">
        <p><?php _e('Your Memphis <b>.htaccess</b> file has been updated to allow google.com access to the system.   This step is necessary to allow documents to be previewed.'); ?></p>
    </div>
    <?php
}
function mdocs_v2_4_admin_notice_v1() {
    ?>
    <div class="updated">
        <p><?php _e('Your Memphis <b>Categories</b> have been updated to handle subcategories this should not effect your current file system in anyway.  If there is any issues please post a comment in the support forum of this plugin.  It is recommended to re-export your files again due to the new way categories are structured.'); ?></p>
    </div
    <?php
}
function mdocs_v2_5_admin_notice_v1() {
    ?>
    <div class="updated">
        <p><?php _e('Your Memphis <b>Categories</b> have been counted to handle subcategories this should not effect your current file system in anyway.  If there is any issues please post a comment in the support forum of this plugin.  It is recommended to re-export your files again due to the new way categories are structured.'); ?></p>
    </div
    <?php
}

?>