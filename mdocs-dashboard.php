<?php
function mdocs_dashboard_menu() {
	global $add_error;
	//MEMPHIS CUSTOM LOGIN INTEGRATION 3.0 AND HIGHER
	$plugin_path = preg_replace('/memphis-documents-library/','',dirname(__FILE__));
	if (is_plugin_active('memphis-wordpress-custom-login/memphis-wp-login.php')) $memphis_custom_login = (get_plugin_data($plugin_path.'memphis-wordpress-custom-login/memphis-wp-login.php'));
	if(isset($memphis_custom_login['Version'])) $memphis_version = intval($memphis_custom_login['Version']);
	else $memphis_version = 0;
	If (!is_plugin_active('memphis-wordpress-custom-login/memphis-wp-login.php') || $memphis_version < 3) {
		add_menu_page( __('Memphis Documents Library','mdocs'), __('Memphis Docs','mdocs'), 'administrator', 'memphis-documents.php', 'mdocs_dashboard', MDOC_URL.'/assets/imgs/kon.ico'  );
	}
	if ( is_admin() ){
		add_action('admin_init','mdocs_register_settings');
		add_action('admin_enqueue_scripts', 'mdocs_admin_script');
	}
	// ERRORS AND UPDATES
	if(isset($_FILES['mdocs']) && $_FILES['mdocs']['name'] == '' && $_POST['mdocs-type'] == 'mdocs-add')  { mdocs_errors(MDOCS_ERROR_1,'error'); $add_error = true; }	
}

function mdocs_dashboard() {
	global $add_error;
	if(isset($_FILES['mdocs']) && $_FILES['mdocs']['name'] != '' && $_POST['mdocs-type'] == 'mdocs-add') mdocs_file_upload();
	if(isset($_FILES['mdocs']) && $_POST['mdocs-type'] == 'mdocs-update') mdocs_file_upload();
	elseif(isset($_GET['action']) && $_GET['action'] == 'add-doc' && MDOCS_NONCE == $_SESSION['mdocs-nonce'] && !isset($_GET['mdocs-sort'])) mdocs_uploader(__('Add Document','mdocs'));
	elseif(isset($_GET['action']) && $_GET['action'] == 'update-doc') mdocs_uploader(__('Update Document','mdocs'));
	elseif(isset($_GET['action']) && $_GET['action'] == 'delete-doc') mdocs_delete();
	elseif(isset($_GET['action']) && $_GET['action'] == 'delete-version') mdocs_delete_version();
	elseif(isset($_POST['action']) && $_POST['action'] == 'mdocs-import') mdocs_import_zip();
	elseif(isset($_POST['action']) && $_POST['action'] == 'mdocs-update-revision') mdocs_update_revision();
	elseif(isset($_GET['action']) && $_GET['action'] == 'mdocs-versions') mdocs_versions();
	elseif(isset($_POST['action']) && $_POST['action'] == 'mdocs-update-cats') mdocs_update_cats();
	mdocs_dashboard_view();
}

function mdocs_dashboard_view() {
	$current_cat = mdocs_get_current_cat();
	if($current_cat == 'import') mdocs_import($current_cat);
	elseif($current_cat == 'export') mdocs_export($current_cat);
	elseif($current_cat == 'cats') mdocs_edit_cats($current_cat);
	elseif($current_cat == 'settings') mdocs_settings($current_cat);
	elseif($current_cat == 'batch') mdocs_batch_upload($current_cat);
	elseif($current_cat == 'short-codes') mdocs_shortcodes($current_cat);
	elseif($current_cat == 'filesystem-cleanup') mdocs_filesystem_cleanup($current_cat);
	elseif($current_cat == 'restore') mdocs_restore_defaults($current_cat);
	elseif($current_cat == 'allowed-file-types') mdocs_allowed_file_types($current_cat);
	else mdocs_the_list();
}

function mdoc_doc_list($current_cat) {
	global $current_cat_array, $parent_cat_array;
	$is_read_write = mdocs_check_read_write();
	if($is_read_write) {
		//mdocs_the_list();
		$mdocs = get_option('mdocs-list');
		//$mdocs = mdocs_sort_by($mdocs, 5, 'dashboard');
		$mdocs = mdocs_array_sort();
		$upload_dir = wp_upload_dir();	
		$bgcolor = "active";
		$count = 0;
		echo 	'<br><br><br>';
		$list_type = get_option('mdocs-list-type-dashboard');
		if($list_type == 'small') {
			echo '<table class="mdocs-list-table">';
			$num_cols = 2;
			if(get_option('mdocs-show-downloads') == '1' || get_option('mdocs-show-downloads')) $num_cols++;
			if(get_option('mdocs-show-author') == '1' || get_option('mdocs-show-author')) $num_cols++;
			if(get_option('mdocs-show-version') == '1' || get_option('mdocs-show-version')) $num_cols++;
			if(get_option('mdocs-show-update') == '1' || get_option('mdocs-show-update')) $num_cols++;
			if(get_option('mdocs-show-ratings') == '1' || get_option('mdocs-show-ratings')) $num_cols++;
			?>
			<tr>
				<td colspan="<?php echo $num_cols; ?>" class="mdocs-dashboard-header"></td>
			</tr>
			<?php
			// SUB CATS
			if(isset($current_cat_array['children'])) $num_cols = mdocs_get_subcats($current_cat_array, $parent_cat_array);
			else $num_cols =mdocs_get_subcats($current_cat_array, $parent_cat_array, false);
		}
		foreach($mdocs as $index => $value) {
			if($mdocs[$index]['cat'] == $current_cat) {
				$count++;
				$mdocs_post = get_post($mdocs[$index]['parent']);
				$mdocs_desc = apply_filters('the_content', $mdocs_post->post_excerpt);
				
				if($list_type == 'small') {
					
					mdocs_file_info_small($value, 'dashboard',$index, $current_cat); 
				} else {			
					?>
						<div class="mdocs-post">
							<?php mdocs_file_info_large($value, 'dashboard', $index, $current_cat); ?>
							<div class="mdocs-clear-both"></div>
							<?php mdocs_social($value); ?>
						</div>
						<div class="mdocs-clear-both"></div>
						<?php mdocs_des_preview_tabs($mdocs[$index]); ?>
						<div class="mdocs-clear-both"></div>
						<?php mdocs_edit_file($value, $index, $current_cat); ?>
						
					</div>
					<?php
				}
				
			}
		}
		if($count == 0) { ?><tr><td colspan="<?php echo $num_cols; ?>"><p class="mdocs-nofiles" ><?php _e('No files found in this category.','mdocs'); ?></p></td></tr><?php }
		if(get_option('mdocs-list-type') == 'small') echo '</table>';
	}
}

function mdocs_delete() {
	if ( $_REQUEST['mdocs-nonce'] == MDOCS_NONCE ) {
		$mdocs = get_option('mdocs-list');
		//$mdocs = mdocs_sort_by($mdocs, 0, 'dashboard', false);
		$mdocs = mdocs_array_sort();
		$index = $_GET['mdocs-index'];
		$upload_dir = wp_upload_dir();
		$mdocs_file = $mdocs[$index];
		$mdocs_post_cat = get_category_by_slug( 'mdocs-media' );
		if(is_array($mdocs[$index]['archived'])) foreach($mdocs[$index]['archived'] as $key => $value) @unlink($upload_dir['basedir'].'/mdocs/'.$value);
		wp_delete_attachment( intval($mdocs_file['id']), true );
		wp_delete_post( intval($mdocs_file['parent']), true );
		if(file_exists($upload_dir['basedir'].'/mdocs/'.$mdocs_file['filename'])) @unlink($upload_dir['basedir'].'/mdocs/'.$mdocs_file['filename']);
		unset($mdocs[$index]);
		$mdocs = array_values($mdocs);
		update_option('mdocs-list', $mdocs);
	} else mdocs_errors(MDOCS_ERROR_4,'error');
}

function mdocs_uploader($edit_type='Add Document') {
	// INPUT SANITIZATION
	$post_page = sanitize_text_field($_REQUEST['page']);
	$post_cat = sanitize_text_field($_REQUEST['cat']);
	$cats = get_option('mdocs-cats');
	$mdocs = get_option('mdocs-list');
	//$mdocs = mdocs_sort_by($mdocs, 0, 'dashboard', false);
	$mdocs = mdocs_array_sort();
	if(isset($_GET['mdocs-index'])) {
		$mdoc_index = $_GET['mdocs-index'];
		$mdocs_post = get_post($mdocs[$mdoc_index]['parent']);
		$mdocs_desc = $mdocs_post->post_excerpt;
	} else $mdoc_index = '';
	if(isset($_GET['mdocs-cat'])) $current_cat = $_GET['mdocs-cat'];
	else $current_cat = $current_cat = key($cats);
	if($edit_type == 'Update Document') $mdoc_type = 'mdocs-update';
	else $mdoc_type = 'mdocs-add';
?>
<div class="mdocs-uploader-bg"></div>
<div class="mdocs-uploader" >
	<h2 class="mdocs-uploader-header">
		<div class="close"><a href="<?php echo 'admin.php?page=memphis-documents.php&mdocs-cat='.$current_cat; ?>"><img src='<?php echo MDOC_URL; ?>/assets/imgs/close.png'/></a></div>
		<?php _e($edit_type); ?>
	</h2>
	<div class="mdocs-uploader-content">
		<form class="mdocs-uploader-form" enctype="multipart/form-data" action="<?php echo get_site_url().'/wp-admin/admin.php?page='.$post_page.'&mdocs-cat='.$post_cat; ?>" method="POST">
			<input type="hidden" name="mdocs-type" value="<?php echo $mdoc_type; ?>" />
			<input type="hidden" name="mdocs-index" value="<?php if($edit_type == 'Update Document') echo $mdoc_index; ?>" />
			<input type="hidden" name="mdocs-cat" value="<?php echo $current_cat; ?>" />
			<input type="hidden" name="mdocs-pname" value="<?php if($edit_type == 'Update Document') echo $mdocs[$mdoc_index]['name']; ?>" />
			<input type="hidden" name="mdocs-nonce" value="<?php echo MDOCS_NONCE; ?>" />
			<input type="hidden" name="mdocs-post-status-sys" value="<?php if($edit_type == 'Update Document') echo $mdocs[$mdoc_index]['post_status']; ?>" />
			<h3><?php _e('File Configuration','mdocs'); ?></h3>
			<div class="mdocs-form-box">
				<label><?php _e('File Uploader','mdocs'); ?>:
					<input type="file" name="mdocs" />
					<?php if($edit_type=='Update Document') echo __('Current File','mdocs').':  <p class="current-name">'.$mdocs[$mdoc_index]['filename'].'</p>'; ?>
				</label>
			</div>
			<div class="mdocs-form-box">
				<label><?php _e('File Name','mdocs'); ?>:
				<input type="text" name="mdocs-name" <?php if($edit_type=='Update Document') echo 'value="'. $mdocs[$mdoc_index]['name'].'"'; ?> />
				</label>
				<label><?php _e('Category','mdocs'); ?>:
				<select name="mdocs-cat">
				<?php mdocs_get_cats($cats, $current_cat); ?>
				</select>
				</label>
				<label>
					<?php _e('Version','mdocs'); ?>: 
					<input type="text" name="mdocs-version" <?php if($edit_type=='Update Document') echo 'value="'.$mdocs[$mdoc_index]['version'].'"'; else echo 'value="1.0"'; ?> />
				</label>
			</div>
			<div class="mdocs-form-box">
				<label><?php _e('Show Social Apps','mdocs'); ?>:
					<input type="checkbox" name="mdocs-social"
					<?php
						if($edit_type=='Update Document' && $mdocs[$mdoc_index]['show_social'] !== '') echo 'checked';
						elseif($edit_type=='Add Document') echo 'checked';
						?> />
				</label>
				<label><?php _e('Downloadable by Non Members','mdocs'); ?>:
					<input type="checkbox" name="mdocs-non-members"
					<?php
						if($edit_type=='Update Document' && $mdocs[$mdoc_index]['non_members'] !== '' ) echo 'checked';
						elseif($edit_type=='Add Document') echo 'checked';
						?> />
				</label>
				<label><?php _e('File Status','mdocs'); ?>:
					<select name="mdocs-file-status" id="mdocs-file-status" >
						<option value="public" <?php if($edit_type=='Update Document') { if($mdocs[$mdoc_index]['file_status'] == 'public') echo 'selected'; }?> ><?php _e('Public','mdocs'); ?></option>
						<option value="hidden" <?php if($edit_type=='Update Document') { if($mdocs[$mdoc_index]['file_status'] == 'hidden') echo 'selected'; }?>><?php _e('Hidden','mdocs'); ?></option>
					</select>
				</label>
				<label><?php _e('Post Status','mdocs'); ?>:
					<select name="mdocs-post-status" id="mdocs-post-status" <?php  if($edit_type=='Update Document') { if($mdocs[$mdoc_index]['file_status'] == 'hidden' || get_option( 'mdocs-hide-all-files' ) || get_option( 'mdocs-hide-all-posts' )) echo 'disabled'; }?> >
						<option value="publish" <?php  if($edit_type=='Update Document') { if($mdocs[$mdoc_index]['post_status'] == 'publish') echo 'selected';  } ?> ><?php _e('Published','mdocs'); ?></option>
						<option value="private" <?php  if($edit_type=='Update Document') { if($mdocs[$mdoc_index]['post_status'] == 'private') echo 'selected'; } ?> ><?php _e('Private','mdocs');  ?></option>
						<option value="pending" <?php  if($edit_type=='Update Document') { if($mdocs[$mdoc_index]['post_status'] == 'pending') echo 'selected'; } ?>  ><?php _e('Pending Review','mdocs');  ?></option>
						<option value="draft" <?php  if($edit_type=='Update Document') { if($mdocs[$mdoc_index]['post_status'] == 'draft') echo 'selected'; } ?> ><?php _e('Draft','mdocs');  ?></option>
					</select>
				</label>
			</div>
			<br>
			<div id="mdocs-desc-container" >
				<h2><?php _e('Description','mdocs'); ?></h2>
				<?php
					if($edit_type=='Update Document') wp_editor($mdocs_desc, "mdocs-desc");
					else wp_editor('', "mdocs-desc"); ?><br>
			</div>
			<?php if($edit_type=='Update Document') { ?>
				<input type="submit" class="button button-primary" value="<?php _e('Update Document','mdocs') ?>" /><br/>
			<?php } else { ?> <input type="submit" class="button button-primary" value="<?php _e('Add Document','mdocs') ?>" /><br/> <?php } ?>
		</form>
	</div>
</div>
<?php
}
?>