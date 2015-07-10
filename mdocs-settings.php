<?php
add_filter( 'wp_default_editor', create_function('', 'return "tinymce";') );
define('MDOCS_CATS', 'mdocs-cats.txt');
define('MDOCS_LIST', 'mdocs-list.txt');
define('MDOCS_DIR','/mdocs/');
define('MDOC_PATH',plugin_dir_path(__FILE__));
define('MDOC_URL',plugin_dir_url(__FILE__));
define('MDOCS_TIME_OFFSET', get_option('gmt_offset')*60*60);
define('MDOCS_ROBOTS','http://www.kingofnothing.net/memphis/robots/memphis-robots.txt');
define('MDOCS_UPDATE', '<div class="mdocs-updated">'.__('Updated','mdocs').'</div>');
define('MDOCS_NEW', '<div class="mdocs-new">'.__('New','mdocs').'</div>');
define('MDOCS_UPDATE_SMALL', '<span class="mdocs-new-updated-small badge pull-left alert-info ">'.__('Updated','mdocs').'</span>');
define('MDOCS_NEW_SMALL', '<span class="mdocs-new-updated-small badge pull-left alert-success ">'.__('New','mdocs').'</span>');
define('MDOCS_CURRENT_TIME', date('Y-m-d H:i:s', time()+MDOCS_TIME_OFFSET));
define('BOX_DEV_ID', 'li557g92xf2fcaoxwxzxtbmhftzqsput');
//define('MDOCS_VERSION', );
$add_error = false;
$mdocs_img_types = array('jpeg','jpg','png','gif');
$mdocs_input_text_bg_colors = array('#f1f1f1','#e5eaff','#efffe7','#ffecdc','#ffe9fe','#ff5000','#00ff20');


function mdocs_register_settings() {
	//CREATE REPOSITORY DIRECTORY
	$upload_dir = wp_upload_dir();
	$is_read_write = mdocs_check_read_write();
	if($is_read_write) {
		//BACKUP FILE CREATE
		$backup_list = json_encode(get_option('mdocs-list'));
		$current_list = get_option('mdocs-list');
		if($current_list != null) file_put_contents($upload_dir['basedir'].MDOCS_DIR.'mdocs-files.bak', $backup_list);
		elseif(file_exists($upload_dir['basedir'].MDOCS_DIR.'mdocs-files.bak') && !isset($_GET['restore-default'])) {
			$backup_list = json_decode(file_get_contents($upload_dir['basedir'].MDOCS_DIR.'mdocs-files.bak'),true);
			update_option('mdocs-list', $backup_list);
			//mdocs_errors(MDOCS_ERROR_7,'error'); 
		}
		if(!isset($_GET['restore-default'])) {  
			// PATCHES
			// 3.0 patch 2
			register_setting('mdocs-patch-vars', 'mdocs-v3-0-patch-var-2');
			add_option('mdocs-v3-0-patch-var-2',false);
			if(get_option('mdocs-v3-0-patch-var-2') == false && is_array(get_option('mdocs-list'))) {
				$mdocs = get_option('mdocs-list');
				global $current_user;
				foreach($mdocs as $index => $the_mdoc) {
					$mdocs[$index]['owner'] = $current_user->user_login;
					$mdocs[$index]['contributors'] = array();
				}
				update_option('mdocs-list', $mdocs);
				update_option('mdocs-v3-0-patch-var-2',true);
			} 
			// 3.0 patch 1
			//delete_option('mdocs-v3-0-patch-var-1');
			//delete_option('mdocs-box-view-updated');
			register_setting('mdocs-patch-vars', 'mdocs-v3-0-patch-var-1');
			add_option('mdocs-v3-0-patch-var-1',false);
			register_setting('mdocs-patch-vars', 'mdocs-box-view-updated');
			add_option('mdocs-box-view-updated',false);
			if(get_option('mdocs-v3-0-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
				add_action( 'admin_head', 'mdocs_v3_0_patch' );
				function mdocs_v3_0_patch() {
					$mdocs = get_option('mdocs-list');
					//MEMPHIS DOCS
					wp_register_script( 'mdocs-script-patch', MDOC_URL.'mdocs-script.js');
					wp_enqueue_script('mdocs-script-patch');
					wp_register_style( 'mdocs-font-awesome2-style-patch', '//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css');
					wp_enqueue_style( 'mdocs-font-awesome2-style-patch' );
					wp_localize_script( 'mdocs-script-patch', 'mdocs_patch_js', array('ajaxurl' => admin_url( 'admin-ajax.php' ), 'patch_text_3_0_1' => __('UPDATE HAS STARTER, DO NOT LEAVE THIS PAGE!'),'patch_text_3_0_2' => __('Go grab a coffee this my take awhile.'),));
					?>
					<script type="application/x-javascript">
						jQuery(document).ready(function() {
							mdocs_v3_0_patch(<?php echo count($mdocs); ?>);
						});
					</script>
					<?php
				}
				wp_deregister_script('mdocs-script-patch');
				wp_deregister_style('mdocs-font-awesome2-style-patch');
			} 
			// 2.6.6
			register_setting('mdocs-patch-vars', 'mdocs-v2-6-6-patch-var-1');
			add_action('mdocs-v2-6-6-patch-var-1',false);
			if(get_option('mdocs-v2-6-6-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
				$this_query = new WP_Query('category_name=mdocs-media&posts_per_page=-1');	
				foreach($this_query->posts as $index => $post) set_post_type($post->ID,'mdocs-posts');
				update_option('mdocs-v2-6-6-patch-var-1',true);
			}
			// 2.6.7
			register_setting('mdocs-patch-vars', 'mdocs-v2-6-7-patch-var-1');
			add_action('mdocs-v2-6-7-patch-var-1',false);
			if(get_option('mdocs-v2-6-7-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
				$mdocs_cat = get_category_by_slug('mdocs-media');
				wp_delete_category($mdocs_cat->cat_ID);
				update_option('mdocs-v2-6-7-patch-var-1',true);
			} 
			// 2.5
			register_setting('mdocs-patch-vars', 'mdocs-v2-5-patch-var-1');
			add_action('mdocs-v2-5-patch-var-1',false);
			if(get_option('mdocs-v2-5-patch-var-1') == false && is_array(get_option('mdocs-list'))) {
				$num_cats = 1;
				foreach( get_option('mdocs-cats') as $index => $cat ){ $num_cats++;}
				update_option('mdocs-num-cats',$num_cats);
				add_action( 'admin_notices', 'mdocs_v2_5_admin_notice_v1' );
				update_option('mdocs-v2-5-patch-var-1',true);
			} else update_option('mdocs-v2-5-patch-var-1',true);
			// 2.4
			register_setting('mdocs-patch-vars', 'mdocs-v2-4-patch-var-1');
			add_option('mdocs-v2-4-patch-var-1',false);
			if(get_option('mdocs-v2-4-patch-var-1') == false  && is_array(get_option('mdocs-list'))) {
				$mdocs_cats = get_option('mdocs-cats');
				$new_mdocs_cats = array();
				foreach($mdocs_cats as $index => $cat) array_push($new_mdocs_cats, array('slug' => $index,'name' => $cat, 'parent' => '', 'children' => array(), 'depth' => 0));
				update_option('mdocs-cats', $new_mdocs_cats);
				update_option('mdocs-v2-4-patch-var-1', true);
				add_action( 'admin_notices', 'mdocs_v2_4_admin_notice_v1' );
			} else update_option('mdocs-v2-4-patch-var-1', true);
			// 2.3
			register_setting('mdocs-patch-vars', 'mdocs-v2-3-1-patch-var-1');
			add_option('mdocs-v2-3-1-patch-var-1',false);
			if(get_option('mdocs-v2-3-1-patch-var-1') == false  && is_array(get_option('mdocs-list'))) {
				$htaccess = $upload_dir['basedir'].'/mdocs/.htaccess';
				$fh = fopen($htaccess, 'w');
				update_option('mdocs-htaccess', "Deny from all\nOptions +Indexes\nAllow from .google.com");
				$mdocs_htaccess = get_option('mdocs-htaccess');
				fwrite($fh, $mdocs_htaccess);
				fclose($fh);
				chmod($htaccess, 0660);
				update_option('mdocs-v2-3-1-patch-var-1', true);
				add_action( 'admin_notices', 'mdocs_v2_2_1_admin_notice_v1' );
			} else update_option('mdocs-v2-3-1-patch-var-1', true);
			//2.1 
			register_setting('mdocs-settings', 'mdocs-2-1-patch-1');
			add_option('mdocs-2-1-patch-1',false);
			if(get_option('mdocs-2-1-patch-1') == false  && is_array(get_option('mdocs-list'))) {
				$mdocs = get_option('mdocs-list');
				foreach(get_option('mdocs-list') as $index => $the_mdoc) {
					if(!is_array($the_mdoc['ratings'])) {
						$the_mdoc['ratings'] = array();
						$the_mdoc['rating'] = 0;
						$mdocs[$index] = $the_mdoc;
					}
					if(!key_exists('rating', $mdocs)) {
						$the_mdoc['rating'] = 0;
						$mdocs[$index] = $the_mdoc;
					}
				}
				mdocs_save_list($mdocs);
				update_option('mdocs-2-1-patch-1', true);
			} else update_option('mdocs-2-1-patch-1', true);
		} else {
			update_option('mdocs-v2-6-6-patch-var-1',true);
			update_option('mdocs-v2-6-7-patch-var-1',true);
			update_option('mdocs-v2-5-patch-var-1',true);
			update_option('mdocs-v2-4-patch-var-1', true);
			update_option('mdocs-v2-3-1-patch-var-1', true);
			update_option('mdocs-2-1-patch-1', true);
			@unlink($upload_dir['basedir'].MDOCS_DIR.'mdocs-files.bak');
		}
		// Creating File Structure
		if(!is_dir($upload_dir['basedir'].'/mdocs/') && $upload_dir['error'] === false) mkdir($upload_dir['basedir'].'/mdocs/');
		elseif(!is_dir($upload_dir['basedir'].'/mdocs/') && $upload_dir['error'] !== false) mdocs_errors(__('Unable to create the directory "mdocs" which is needed by Memphis Documents Library. Is its parent directory writable by the server?','mdocs'),'error');
		//CREATE MDOCS PAGE
		$query = new WP_Query('pagename=mdocuments-library');	
		if(empty($query->posts) && empty($query->queried_object) ) {
			$mdocs_page = array(
				'post_title' => __('Documents','mdocs'),
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
		mdocs_init_settings();
		$upload_dir = wp_upload_dir();
		if(!file_exists($upload_dir['basedir'].'/mdocs/.htaccess')) {
			if(!file_exists($upload_dir['basedir'].'/mdocs/')) mkdir($upload_dir['basedir'].'/mdocs/');
			$htaccess = $upload_dir['basedir'].'/mdocs/.htaccess';
			$fh = fopen($htaccess, 'w');
			$mdocs_htaccess = get_option('mdocs-htaccess');
			fwrite($fh, $mdocs_htaccess);
			fclose($fh);
			chmod($htaccess, 0660);
		}
	} else mdocs_errors(__('Unable to create the directory "mdocs" which is needed by Memphis Documents Library. Its parent directory is not readable/writable by the server?','mdocs'),'error');
	
}

function mdocs_init_settings() {
	add_filter('upload_mimes', 'mdocs_custom_mime_types');
	$temp_cats = array();
	$temp_cats[0] = array('base_parent' => '', 'index' => 0, 'parent_index' => 0, 'slug' => 'mdocuments', 'name' => 'Documents', 'parent' => '', 'children' => array(), 'depth' => 0,);
	register_setting('mdocs-settings', 'mdocs-cats');
	add_option('mdocs-cats',$temp_cats);
	if(is_string(get_option('mdocs-cats'))) update_option('mdocs-cats',$temp_cats);
	register_setting('mdocs-settings', 'mdocs-list');
	add_option('mdocs-list',array());
	register_setting('mdocs-settings', 'mdocs-num-cats');
	add_option('mdocs-num-cats',1);
	register_setting('mdocs-settings', 'mdocs-num-cats');
	add_option('mdocs-num-cats',1);
	register_setting('mdocs-settings', 'mdocs-zip');
	add_option('mdocs-zip','mdocs-export.zip');
	register_setting('mdocs-settings', 'mdocs-wp-root');
	update_option('mdocs-wp-root',get_home_path());
	register_setting('mdocs-top-downloads', 'mdocs-top-downloads');
	add_option('mdocs-top-downloads',10);
	register_setting('mdocs-top-downloads', 'mdocs-top-rated');
	add_option('mdocs-top-rated',10);
	register_setting('mdocs-top-downloads', 'mdocs-last-updated');
	add_option('mdocs-last-updated',10);
	//GLOBAL VARIABLES
	register_setting('mdocs-global-settings', 'mdocs-list-type');
	update_option('mdocs-list-type','small');
	register_setting('mdocs-global-settings', 'mdocs-list-type-dashboard');
	add_option('mdocs-list-type-dashboard','small');
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
	register_setting('mdocs-global-settings', 'mdocs-show-share');
	add_option('mdocs-show-share', true);
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
	register_setting('mdocs-global-settings', 'mdocs-allowed-mime-types');
	add_option('mdocs-allowed-mime-types', array());
	if(is_string(get_option('mdocs-allowed-mime-types'))) update_option('mdocs-allowed-mime-types',array());
	register_setting('mdocs-global-settings', 'mdocs-removed-mime-types');
	add_option('mdocs-removed-mime-types', array());
	if(is_string(get_option('mdocs-removed-mime-types'))) update_option('mdocs-removed-mime-types',array());
	register_setting('mdocs-global-settings', 'mdocs-view-private');
	add_option('mdocs-view-private', mdocs_init_view_private());
	register_setting('mdocs-global-settings', 'mdocs-date-format');
	add_option('mdocs-date-format', 'd-m-Y G:i');
	register_setting('mdocs-global-settings', 'mdocs-allow-upload');
	add_option('mdocs-allow-upload', array());
	
	
	//Update View Private Users
	mdocs_update_view_private_users();
}
function mdocs_update_view_private_users() {
	$mdocs_roles = get_option('mdocs-view-private');
	$wp_roles = get_editable_roles();
	foreach($wp_roles as $index => $wp_role) {
		if($mdocs_roles[$index] == 1) {
			$add_role = get_role( $index );
			$add_role->add_cap( 'read_private_pages' );
			$add_role->add_cap( 'read_private_posts' );
		} else {
			$add_role = get_role( $index );
			$add_role->remove_cap( 'read_private_pages' );
			$add_role->remove_cap( 'read_private_posts' );
		}
	}
}
function mdocs_init_view_private() {
	$roles = get_editable_roles();
	$view_private = array();
	foreach($roles as $index => $role) {
		$view_private[$index] = $role['capabilities']['read_private_pages'];
	}
	return $view_private;
}
//MODIFY TINYMCE
function wptiny($initArray){
    $initArray['height'] = '600px';
    return $initArray;
}
add_filter('tiny_mce_before_init', 'wptiny');
//ADD CONTENT TO DOCUMENTS PAGE
//[mdocs]
function mdocs_shortcode($att, $content=null) { return mdocs_the_list($att); }
add_shortcode( 'mdocs', 'mdocs_shortcode' );
//[mdocs_post_page]
function mdocs_post_page_shortcode($att, $content=null) {
	return mdocs_post_page($att);
}
add_shortcode( 'mdocs_post_page', 'mdocs_post_page_shortcode' );
function mdocs_admin_script() {
	if($_GET['page'] == 'memphis-documents.php') {
		//JQUERY
		wp_enqueue_script("jquery");
		//BOOTSTRAP
		if(isset($_GET['page']) && $_GET['page'] == 'memphis-documents.php') {
			//wp_register_style( 'mdocs-bootstrap-style', '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css');
			//wp_enqueue_style( 'mdocs-bootstrap-style' );
			wp_register_style( 'mdocs-bootstrap-style2', '//maxcdn.bootstrapcdn.com/bootswatch/3.2.0/cerulean/bootstrap.min.css');
			wp_enqueue_style( 'mdocs-bootstrap-style2' );
			wp_enqueue_script( 'mdocs-bootstrap-script', '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js' );
		}
		//JQUERY UI
		wp_register_style( 'mdocs-jquery-ui-style', '//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
		wp_enqueue_style( 'mdocs-jquery-ui-style' );
		wp_enqueue_script( 'mdocs-jquery-ui-script', '//code.jquery.com/ui/1.10.3/jquery-ui.js' );
		//TWITTER WIDGET
		wp_enqueue_script( 'widgets.js', '//platform.twitter.com/widgets.js' );
		//MEMPHIS DOCS
		wp_register_style( 'mdocs-style', MDOC_URL.'/mdocs-style.css');
		wp_enqueue_style( 'mdocs-style' );
		wp_register_style( 'mdocs-admin-style', MDOC_URL.'/mdocs-admin-style.css');
		wp_enqueue_style( 'mdocs-admin-style' );
		wp_register_style( 'mdocs-admin-style-old', MDOC_URL.'/style.css');
		wp_enqueue_style( 'mdocs-admin-style-old' );
		wp_register_script( 'mdocs-admin-script', MDOC_URL.'/mdocs-script.js');
		wp_enqueue_script('mdocs-admin-script');
		//INLINE STYLE
		wp_enqueue_script('mdocs-admin-script');
		mdocs_inline_admin_css('mdocs-admin-style');
		//FONT-AWESOME STYLE
		wp_register_style( 'mdocs-font-awesome2-style', '//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css');
		wp_enqueue_style( 'mdocs-font-awesome2-style' );
		//WORDPRESS IRIS COLOR PICKER
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'mdocs-color-picker', plugins_url('mdocs-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
		mdocs_js_handle('mdocs-admin-script');
	}
}

function mdocs_script() {
	global $post;
	if(get_post_type($post) == 'mdocs-posts' || has_shortcode( $post->post_content, 'mdocs' ) || is_home()) {
		//JQUERY
		wp_enqueue_script("jquery");
		//BOOTSTRAP
		
		//wp_register_style( 'mdocs-bootstrap-style2', '//maxcdn.bootstrapcdn.com/bootswatch/3.2.0/cerulean/bootstrap.min.css');
		//wp_enqueue_style( 'mdocs-bootstrap-style2' );
		//wp_register_script( 'bootstrap.min.js', '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js');
		//wp_enqueue_script( 'bootstrap.min.js' );
		
		$handle = 'bootstrap.min.js';
		$list = 'enqueued';
		if (wp_script_is( $handle, $list )) { return; }
		else {
			wp_register_style( 'mdocs-bootstrap-style', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css');
			wp_enqueue_style( 'mdocs-bootstrap-style' );
			wp_register_script( 'bootstrap.min.js', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js');
			wp_enqueue_script( 'bootstrap.min.js' );
		}
		
		//JQUERY UI
		wp_register_style( 'mdocs-jquery-ui-style', '//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
		wp_enqueue_style( 'mdocs-jquery-ui-style' );
		wp_enqueue_script( 'mdocs-jquery-ui-script', '//code.jquery.com/ui/1.10.3/jquery-ui.js' );
		//MEMPHIS DOCS 
		wp_register_style( 'mdocs-style', MDOC_URL.'/mdocs-style.css');
		wp_enqueue_style( 'mdocs-style' );
		wp_register_style( 'mdocs-style-old', MDOC_URL.'/style.css');
		wp_enqueue_style( 'mdocs-style-old' );
		wp_register_script( 'mdocs-script', MDOC_URL.'/mdocs-script.js');
		wp_enqueue_script('mdocs-script');
		mdocs_inline_css('mdocs-style');
		//FONT-AWESOME STYLE
		wp_register_style( 'mdocs-font-awesome2-style', '//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css');
		wp_enqueue_style( 'mdocs-font-awesome2-style' );
		mdocs_js_handle('mdocs-script');
	}
}

function mdocs_inline_css($style_name) {
	$set_inline_style = mdocs_get_inline_css();
	wp_add_inline_style( $style_name, $set_inline_style );
}
function mdocs_inline_admin_css($style_name) {
	$set_inline_style = mdocs_get_inline_admin_css();
	wp_add_inline_style( $style_name, $set_inline_style );
}

function mdocs_document_ready_wp() {
	global $post;
	if(get_post_type($post) == 'mdocs-posts' || has_shortcode( $post->post_content, 'mdocs' )) {
?>
<script type="application/x-javascript">
		jQuery( document ).ready(function() {
			mdocs_wp('<?php echo MDOC_URL; ?>', '<?php echo ABSPATH; ?>');
		});	
	</script>
<?php
	}
}
function mdocs_document_ready_admin() {
	if(!is_network_admin() && $_GET['page'] == 'memphis-documents.php') {
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
	if(isset($_POST['sort_type'])) setcookie('mdocs-sort-type-site', $_POST['mdocs-sort-type']); 
	if(isset($_POST['sort_range'])) setcookie('mdocs-sort-range-site', $_POST['mdocs-sort-range']);
	//$get_browser = new mdocs_browser_compatibility();
	//$browser = $get_browser->get_browser();
	//if($browser['name'] == 'Internet Explorer') mdocs_ie_compat();
}
function mdocs_send_headers_dashboard() {
	//SET SORT VALUES DASHBOARD
	if(isset($_POST['sort_type'])) setcookie('mdocs-sort-type-dashboard', $_POST['mdocs-sort-type']); 
	if(isset($_POST['sort_range'])) setcookie('mdocs-sort-range-dashboard', $_POST['mdocs-sort-range']);
	//mdocs_ie_compat();
}
function mdocs_v2_2_1_admin_notice_v1() {
    ?>
    <div class="update-nag">
        <p><?php _e('Your Memphis <b>.htaccess</b> file has been updated to allow google.com access to the system.   This step is necessary to allow documents to be previewed.','mdocs'); ?></p>
    </div>
    <?php
}
function mdocs_v2_4_admin_notice_v1() {
    ?>
    <div class="update-nag">
        <p><?php _e('Your Memphis <b>Categories</b> have been updated to handle subcategories this should not effect your current file system in anyway.  If there is any issues please post a comment in the support forum of this plugin.  It is recommended to re-export your files again due to the new way categories are structured.','mdocs'); ?></p>
    </div
    <?php
}
function mdocs_v2_5_admin_notice_v1() {
    ?>
    <div class="update-nag">
        <p><?php _e('Your Memphis <b>Categories</b> have been counted to handle subcategories this should not effect your current file system in anyway.  If there is any issues please post a comment in the support forum of this plugin.  It is recommended to re-export your files again due to the new way categories are structured.','mdocs'); ?></p>
    </div
    <?php
}

?>