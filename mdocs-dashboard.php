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
	//elseif(isset($_GET['action']) && $_GET['action'] == 'add-doc' && MDOCS_NONCE == $_SESSION['mdocs-nonce'] && !isset($_GET['mdocs-sort'])) mdocs_uploader(__('Add Document','mdocs'));
	//elseif(isset($_GET['action']) && $_GET['action'] == 'update-doc') mdocs_uploader(__('Update Document','mdocs'));
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
	else echo mdocs_the_list();
}

/* Depreciated function
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
		if($count == 0) { ?><tr><td colspan="<?php echo $num_cols; ?>"><p class="mdocs-nofiles" ><?php _e('No files found in this folder.','mdocs'); ?></p></td></tr><?php }
		if(get_option('mdocs-list-type') == 'small') echo '</table>';
	}
}
*/
function mdocs_delete() {
	if ( $_REQUEST['mdocs-nonce'] == MDOCS_NONCE ) {
		$mdocs = get_option('mdocs-list');
		//$mdocs = mdocs_sort_by($mdocs, 0, 'dashboard', false);
		$mdocs = mdocs_array_sort();
		$index = $_GET['mdocs-index'];
		$upload_dir = wp_upload_dir();
		$mdocs_file = $mdocs[$index];
		if(is_array($mdocs[$index]['archived'])) foreach($mdocs[$index]['archived'] as $key => $value) @unlink($upload_dir['basedir'].'/mdocs/'.$value);
		wp_delete_attachment( intval($mdocs_file['id']), true );
		wp_delete_post( intval($mdocs_file['parent']), true );
		if(file_exists($upload_dir['basedir'].'/mdocs/'.$mdocs_file['filename'])) @unlink($upload_dir['basedir'].'/mdocs/'.$mdocs_file['filename']);
		unset($mdocs[$index]);
		$mdocs = array_values($mdocs);
		mdocs_save_list($mdocs);
	} else mdocs_errors(MDOCS_ERROR_4,'error');
}

function mdocs_add_update_ajax($edit_type='Add Document') {
	// INPUT SANITIZATION
	$post_page = sanitize_text_field($_POST['page']);
	$post_cat = sanitize_text_field($_POST['cat']);
	$cats = get_option('mdocs-cats');
	$mdocs = get_option('mdocs-list');
	//$mdocs = mdocs_sort_by($mdocs, 0, 'dashboard', false);
	$mdocs = mdocs_array_sort();
	if(isset($_POST['mdocs-index'])) {
		$mdoc_index = $_POST['mdocs-index'];
		foreach($mdocs as $index => $doc) if($mdoc_index == $doc['id']) { $mdoc_index = $index; break; }		
		$mdocs_post = get_post($mdocs[$mdoc_index]['parent']);
		$mdocs_desc = $mdocs_post->post_excerpt;
	} else $mdoc_index = '';
	if(isset($_POST['current-cat'])) $current_cat = $_POST['current-cat'];
	else $current_cat = $current_cat = key($cats);
	if($edit_type == 'Update Document') $mdoc_type = 'mdocs-update';
	else $mdoc_type = 'mdocs-add';
	$json = json_encode($mdocs[$mdoc_index]);
	echo $json;
}

function mdocs_uploader() {
	$cats = get_option('mdocs-cats');
?>
<div class="row">
	<div class="col-md-12" id="mdocs-add-update-container">
		<div class="page-header">
			<h1 id="mdocs-add-update-header"></h1>
		</div>
		<div class="">
			<form class="form-horizontal" enctype="multipart/form-data" action="" method="POST" id="mdocs-add-update-form">
				<input type="hidden" name="mdocs-type" value="" />
				<input type="hidden" name="mdocs-index" value="" />
				<input type="hidden" name="mdocs-cat" value="" />
				<input type="hidden" name="mdocs-pname" value="" />
				<input type="hidden" name="mdocs-nonce" value="<?php echo $_SESSION['mdocs-nonce']; ?>" />
				<input type="hidden" name="mdocs-post-status-sys" value="" />
				
				<div class="well well-lg">
					<div class="page-header">
						<h2 id="mdocs-add-update-header"><?php _e('File Properties','mdocs'); ?></h2>
					</div>
					<div class="form-group form-group-lg has-success">
						<label class="col-sm-2 control-label" for="mdocs-name"><?php _e('File Name','mdocs'); ?></label>
						<div class="col-sm-10">
							<input class="form-control" type="text" name="mdocs-name" id="mdocs-name" />
						</div>
					</div>
					<div class="form-group form-group-lg has-warning">
						<label class="col-sm-2 control-label" for="mdocs-cat"><?php _e('Folder','mdocs'); ?></label>
						<div class="col-sm-10">
							<select class="form-control" name="mdocs-cat">
							<?php mdocs_get_cats($cats, $current_cat); ?>
							</select>
						</div>
					</div>
					<div class="form-group form-group-lg has-error">
						<label class="col-sm-2 control-label" for="mdocs-version"><?php _e('Version','mdocs'); ?></label>
						<div class="col-sm-10">
							<input class="form-control" type="text" name="mdocs-version" value="1.0" />
						</div>
					</div>
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label" for="mdocs"><?php _e('File Uploader','mdocs'); ?></label>
						<div class="col-sm-10">
							<input class="form-control" type="file" name="mdocs" />
							<p class="help-block" id="mdocs-current-doc"></p>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="mdocs-file-status"><?php _e('File Status','mdocs'); ?></label>
						<div class="col-sm-10">
							<select class="form-control input-lg" name="mdocs-file-status" id="mdocs-file-status" >
								<option value="public" ><?php _e('Public','mdocs'); ?></option>
								<option value="hidden" ><?php _e('Hidden','mdocs'); ?></option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="mdocs-post-status"><?php _e('Post Status','mdocs'); ?></label>
						<div class="col-sm-10">
							<select class="form-control input-lg" name="mdocs-post-status" id="mdocs-post-status" <?php  if($edit_type=='Update Document') { if($mdocs[$mdoc_index]['file_status'] == 'hidden' || get_option( 'mdocs-hide-all-files' ) || get_option( 'mdocs-hide-all-posts' )) echo 'disabled'; }?> >
								<option value="publish" ><?php _e('Published','mdocs'); ?></option>
								<option value="private" ><?php _e('Private','mdocs');  ?></option>
								<option value="pending"  ><?php _e('Pending Review','mdocs');  ?></option>
								<option value="draft" ><?php _e('Draft','mdocs');  ?></option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="mdocs-social"><?php _e('Show Social Apps','mdocs'); ?></label>
						<div class="col-sm-1">
							<input class="form-control" type="checkbox" name="mdocs-social" checked />
						</div>
						<label class="col-sm-3 control-label" for="mdocs-non-members"><?php _e('Downloadable by Non Members','mdocs'); ?></label>
						<div class="col-sm-1">
							<input class="form-control" type="checkbox" name="mdocs-non-members" checked />
						</div>
					</div>
					<div class="form-group">
						<div class="page-header">
							<h2><?php _e('Description','mdocs'); ?></h2>
							<br>
							<div>
							<?php wp_editor('', "mdocs-desc"); ?>
							</div>
						</div>
					</div>
				</div>
				
				<input type="submit" class="button button-primary" id="mdocs-save-doc-btn" value="" />
				
			</form>
		</div>
	</div>
</div>
	
<?php

}
?>