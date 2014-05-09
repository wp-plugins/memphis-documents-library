<?php
function mdocs_settings($cat) {
	$upload_dir = wp_upload_dir();
	$mdocs_list_type = get_option( 'mdocs-list-type' );
	$mdocs = get_option('mdocs-list');
	$mdocs_list_type_dashboard = get_option( 'mdocs-list-type-dashboard' );
	$mdocs_hide_all_files = get_option( 'mdocs-hide-all-files' );
	$mdocs_hide_all_files_non_members = get_option( 'mdocs-hide-all-files-non-members' );
	$mdocs_hide_all_posts = get_option( 'mdocs-hide-all-posts' );
	$mdocs_hide_all_posts_default = get_option( 'mdocs-hide-all-posts-default' );
	$mdocs_hide_all_posts_non_members = get_option( 'mdocs-hide-all-posts-non-members' );
	$mdocs_hide_all_posts_non_members_default = get_option( 'mdocs-hide-all-posts-non-members-default' );
	$mdocs_show_downloads = get_option( 'mdocs-show-downloads' );
	$mdocs_show_author = get_option( 'mdocs-show-author' );
	$mdocs_show_version = get_option( 'mdocs-show-version' );
	$mdocs_show_update = get_option( 'mdocs-show-update' );
	$mdocs_show_ratings = get_option( 'mdocs-show-ratings' );
	$mdocs_show_social = get_option( 'mdocs-show-social' );
	$mdocs_show_new_banners = get_option('mdocs-show-new-banners');
	$mdocs_time_to_display_banners = strval(get_option('mdocs-time-to-display-banners'));
	$mdocs_sort_type = get_option('mdocs-sort-type');
	$mdocs_sort_style = get_option('mdocs-sort-style');
	$mdocs_default_content = get_option('mdocs-default-content');
	$mdocs_show_description = get_option('mdocs-show-description');
	$mdocs_show_preview = get_option('mdocs-show-preview');
	$mdocs_htaccess = get_option('mdocs-htaccess');
	mdocs_hide_show_toogle();	
?>
<!-- COLOR PICKER 
<input type="text" value="#bada55" class="mdocs-color-picker" />
<input type="text" value="#bada55" class="mdocs-color-picker" data-default-color="#effeff" />
-->
<h2><?php _e('Global Documents Library Settings'); ?></h2>
<form enctype="multipart/form-data" method="post" action="options.php" class="mdocs-setting-form">
<table class="form-table mdocs-settings-table">
	<?php settings_fields( 'mdocs-global-settings' ); ?>
	<input type="hidden" name="mdocs-download-color-normal" value="<?php echo get_option( 'mdocs-download-color-normal' ); ?>" />
	<input type="hidden" name="mdocs-download-color-hover" value="<?php echo get_option( 'mdocs-download-color-hover' ); ?>" />
	<input type="hidden" name="mdocs-hide-all-posts-default" value="<?php echo get_option( 'mdocs-hide-all-posts-default' ); ?>" />
	<input type="hidden" name="mdocs-hide-all-posts-non-members-default" value="<?php echo get_option( 'mdocs-hide-all-posts-non-members-default' ); ?>" />
	<tr>
		<th><?php _e('Document List Size: (Site)'); ?></th>
		<td>
			<input type="radio" name="mdocs-list-type" value="small"  <?php checked( $mdocs_list_type, 'small') ?>/> <?php _e('small'); ?><br>
			<input type="radio" name="mdocs-list-type" value="large" <?php checked( $mdocs_list_type, 'large') ?>/> <?php _e('large'); ?>
		</td>
		<th><?php _e('Document List Size: (Dashboard)'); ?></th>
		<td>
			<input type="radio" name="mdocs-list-type-dashboard" value="small"  <?php checked( $mdocs_list_type_dashboard, 'small') ?>/> <?php _e('small'); ?><br>
			<input type="radio" name="mdocs-list-type-dashboard" value="large" <?php checked( $mdocs_list_type_dashboard, 'large') ?>/> <?php _e('large'); ?>
		</td>
	</tr>
	<tr>
		<th><?php _e('Displayed File Information'); ?></th>
		<td>
			<input type="checkbox" name="mdocs-show-downloads" value="1"  <?php checked(1,$mdocs_show_downloads) ?>/> <?php _e('Downloads'); ?><br>
			<input type="checkbox" name="mdocs-show-author" value="1"  <?php checked( $mdocs_show_author, 1) ?>/> <?php _e('Author'); ?><br>
			<input type="checkbox" name="mdocs-show-version" value="1"  <?php checked( $mdocs_show_version, 1) ?>/> <?php _e('Version'); ?><br>
			<input type="checkbox" name="mdocs-show-update" value="1"  <?php checked( $mdocs_show_update, 1) ?>/> <?php _e('Updated'); ?><br>
			<input type="checkbox" name="mdocs-show-ratings" value="1"  <?php checked( $mdocs_show_ratings, 1) ?>/> <?php _e('Ratings'); ?><br>
			<input type="checkbox" name="mdocs-show-social" value="1"  <?php checked( $mdocs_show_social, 1) ?>/> <?php _e('Social'); ?>
		</td>
	
		<th><?php _e('Hide Things'); ?></th>
		<td>
			<input type="checkbox" id="mdocs-hide-all-files" name="mdocs-hide-all-files" value="1"  <?php checked(1,$mdocs_hide_all_files) ?>/> <?php _e('All Files'); ?><br>
			<input type="checkbox" id="mdocs-hide-all-posts" name="mdocs-hide-all-posts" value="1"  <?php checked(1,$mdocs_hide_all_posts) ?>/> <?php _e('All Posts (May take awhile)'); ?><br>
			<input type="checkbox" id="mdocs-hide-all-files-non-members" name="mdocs-hide-all-files-non-members" value="1"  <?php checked(1,$mdocs_hide_all_files_non_members) ?>/> <?php _e('All Files: (Non Members)'); ?><br>
			<input type="checkbox" id="mdocs-hide-all-posts-non-members" name="mdocs-hide-all-posts-non-members" value="1"  <?php checked(1,$mdocs_hide_all_posts_non_members) ?>/> <?php _e('All Posts: (Non Members)'); ?>
		</td>
	</tr>
	<tr>
		<th><?php _e('New & Updated Banner'); ?></th>
		<td>
			<input type="checkbox" id="mdocs-show-new-banners" name="mdocs-show-new-banners" value="1"  <?php checked(1,$mdocs_show_new_banners) ?>/> <?php _e('Show New & Updated Banner'); ?><br>
			<input class="width-30" type="text" id="mdocs-time-to-display-banners" name="mdocs-time-to-display-banners" value="<?php echo $mdocs_time_to_display_banners; ?>"/> <?php _e('days - Time to Displayed'); ?><br>
		</td>
		<th><?php _e('Default Sort Options'); ?></th>
		<td>
			<label><?php _e('Order Types:'); ?>
				<select name="mdocs-sort-type" id="mdocs-sort-type" >
					<option value="name" <?php if($mdocs_sort_type == 'name') echo 'selected'; ?>><?php _e('File Name'); ?></option>
					<option value="downloads" <?php if($mdocs_sort_type == 'downloads') echo 'selected'; ?>><?php _e('Number of Downloads'); ?></option>
					<option value="version" <?php if($mdocs_sort_type == 'version') echo 'selected'; ?>><?php _e('Version'); ?></option>
					<option value="owner" <?php if($mdocs_sort_type == 'owner') echo 'selected'; ?>><?php _e('Author'); ?></option>
					<option value="modified" <?php if($mdocs_sort_type == 'modified') echo 'selected'; ?>><?php _e('Last Updated'); ?></option>
					<option value="rating" <?php if($mdocs_sort_type == 'rating') echo 'selected'; ?>><?php _e('Rating'); ?></option>
				</select>
			</label><br><br>
			<label><?php _e('Order Style:'); ?>
				<select name="mdocs-sort-style" id="mdocs-sort-style" >
					<option value="desc" <?php if($mdocs_sort_style == 'desc') echo 'selected'; ?>><?php _e('Sort Descending'); ?></option>
					<option value="asc" <?php if($mdocs_sort_style == 'asc') echo 'selected'; ?>><?php _e('Sort Ascending'); ?></option>
				</select>
			</label>
		</td>
	</tr>
	<tr>
		<th><?php _e('Document Page Settings'); ?></th>
		<td>
			<label><?php _e('Default Content:'); ?>
				<select name="mdocs-default-content" id="mdocs-default-content" >
					<option value="description" <?php if($mdocs_default_content == 'description') echo 'selected'; ?>><?php _e('Description'); ?></option>
					<option value="preview" <?php if($mdocs_default_content == 'preview') echo 'selected'; ?>><?php _e('Preview'); ?></option>
				</select>
			</label><br><br>
			<input type="checkbox" id="mdocs-show-description" name="mdocs-show-description" value="1"  <?php checked(1,$mdocs_show_description) ?>/> <?php _e('Show Description'); ?><br>
			<input type="checkbox" id="mdocs-show-preview" name="mdocs-show-preview" value="1"  <?php checked(1,$mdocs_show_preview) ?>/> <?php _e('Show Preview'); ?><br>
		</td>
		<th><?php _e('.htaccess File Editor'); ?></th>
		<?php
		
		if($_GET['settings-updated'] && $_GET['page'] == 'memphis-documents.php') {
			$upload_dir = wp_upload_dir();
			$htaccess = file_put_contents($upload_dir['basedir'].MDOCS_DIR.'.htaccess', $mdocs_htaccess);
		}
		?>
		<td>
				<textarea cols="30" rows="10" name="mdocs-htaccess"><?php echo $mdocs_htaccess; ?></textarea>
		</td>
	</tr>
	<!--
	<tr>
		<td colspan="3">
			<h2><?php _e('Roles & Permissions'); ?></h2>
			<p>Roles and permissions are cascading meaning if you choose author everyone above and including author will have access to the documents library.</p>
			<label>Add Documents</label>
			<select>
			<?php
				global $wp_roles;
				$roles = $wp_roles->get_names();
				foreach($roles as $key => $name) {
					echo '<option>'.$name.'</option>';
				}
			?>
			</select><br>
			<label>Edit Categories</label>
			<select>
			<?php
				global $wp_roles;
				$roles = $wp_roles->get_names();
				foreach($roles as $key => $name) {
					echo '<option>'.$name.'</option>';
				}
			?>
			</select>
		</td>		
	</tr>
	<tr>
		<th><?php _e('Style Settings'); ?></th>
		<td>
			Normal<br>
			<input type="text" value="<?php echo get_option('mdocs-download-color-normal'); ?>" class="mdocs-color-picker"/><br><br>
			Hover<br>
			<input type="text" value="<?php echo get_option('mdocs-download-color-hover'); ?>" class="mdocs-color-picker" /> 
		</td>
		<th><?php _e('Download Button'); ?></th>
		<td>
			<div class="mdocs-download-btn-config" ><?php echo __('Download');?></div>
		</td>
		
	</tr>
	-->
</table>
<input style="margin:15px;" type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</form>
<?php
	if(!isset($_POST['mdocs-filesystem-cleanup'])) {
		?>
		<div class="updated">
		<form enctype="multipart/form-data" method="post" action="" class="mdocs-setting-form">
			<h3><?php _e('Filesystem Cleanup'); ?></h3>
			<p><?php _e('Use this functionality to run a system check to locate and remove any broken files/data links inside Memphis Documents Library.<br>Be sure to make a backup copy before running this check.'); ?></p>
			<input type="hidden" name="mdocs-filesystem-cleanup" value="init-cleanup" />
			<input style="margin:15px;" type="submit" class="button-primary" id="mdocs-filesystem-cleanup" value="<?php _e('Run Filesystem Cleanup') ?>" />
		</form>
		</div>
	<?php
	} elseif(isset($_POST['mdocs-filesystem-cleanup']) && $_POST['mdocs-filesystem-cleanup'] == 'init-cleanup') {
		mdocs_cleanup_init_html();
	} elseif(isset($_POST['mdocs-filesystem-cleanup']) && $_POST['mdocs-filesystem-cleanup'] == 'submit-cleanup') {
		mdocs_cleanup_submit_html();
	}
}

function mdocs_cleanup_submit_html() {
	mdocs_filesystem_cleanup_submit();
	?>
	<div class="updated">
		<h3><?php _e('Filesystem Cleanup Complete'); ?></h3>
		<p><?php _e('Your file system has been cleaned.  Remember if you encounter any issues revert back to your previous version using the import tool.'); ?></p>
		<div class="cleanup-files">
			<h3><?php _e('Unlinked Files'); ?></h3>
		<?php
		$cleanup = mdocs_filesystem_cleanup_init();
		foreach($cleanup['files'] as $file) echo $file.'<br>';
		if(count($cleanup['files']) == 0) _e('There are no unlinked files.');
		?>
		</div>
		<div class="cleanup-data">
			<h3><?php _e('Unlinked Data'); ?></h3>
		<?php
		foreach($cleanup['data'] as $data) echo __('Element ID').': '.$data['index'].'<br>';
		if(count($cleanup['data']) == 0) _e('There is no unlinked data.');
		?>
		</div>
		<div class="mdocs-clear-both"></div>
		<form enctype="multipart/form-data" method="post" action="" class="mdocs-setting-form">
			<input type="hidden" name="mdocs-filesystem-cleanup" value="init-cleanup" />
			<input style="margin:15px;" type="submit" class="button-primary" id="mdocs-filesystem-cleanup" value="<?php _e('Run File System Cleanup Again') ?>" />
		</form>
	</div>
	<?php
}
function mdocs_cleanup_init_html() {
	$cleanup = mdocs_filesystem_cleanup_init();
	?>
	<div class="updated">
		<h3><?php _e('Filesystem Analyzed'); ?></h3>
		<p><?php _e('Below is a list of files and data that look to be broken and or unused by Memphis Documents Library.  The next phase of the process will try and remove all this unlinked information.<br>Please make sure you have made an export of the files before continuing.  If anything goes wrong just import your export file to revert all changes.'); ?></p>
		<div class="cleanup-files">
			<h3><?php _e('Unlinked Files'); ?></h3>
		<?php
		foreach($cleanup['files'] as $file) echo $file.'<br>';
		if(count($cleanup['files']) == 0) _e('There are no unlinked files.');
		?>
		</div>
		<div class="cleanup-data">
			<h3><?php _e('Unlinked Data'); ?></h3>
		<?php
		foreach($cleanup['data'] as $data) echo __('Element ID').': '.$data['index'].'<br>';
		if(count($cleanup['data']) == 0) _e('There is no unlinked data.');
		?>
		</div>
		<div class="mdocs-clear-both"></div>
		<form enctype="multipart/form-data" method="post" action="" class="mdocs-setting-form">
		   <input type="hidden" name="mdocs-filesystem-cleanup" value="submit-cleanup" />
		   <input style="margin:15px;" type="submit" class="button-primary" id="mdocs-filesystem-cleanup" value="<?php _e('Cleanup The File System') ?>" />
	   </form>
	</div>
	<?php
}

function mdocs_filesystem_cleanup_submit() {
	$mdocs = get_option('mdocs-list');
	$cleanup = mdocs_filesystem_cleanup_init();
	$upload_dir = wp_upload_dir();
	foreach($cleanup['files'] as $file) {
		unlink($upload_dir['basedir'].'/mdocs/'.$file);
	}
	foreach($cleanup['data'] as $data) {
		if($data['id'] != null) wp_delete_attachment( intval($data['id']), true );
		if($data['parent'] != null) wp_delete_post( intval($data['parent']), true );
		unset($mdocs[$data['index']]);
		$mdocs = array_values($mdocs);
		update_option('mdocs-list',$mdocs);
	}
	
	//wp_delete_attachment( intval($mdocs[$key]['id']), true );
	//wp_delete_post( intval($mdocs[$key]['parent']), true );
	//var_dump($cleanup);
}

function mdocs_filesystem_cleanup_init() {
	$mdocs_zip_file = get_option('mdocs-zip');
	$upload_dir = wp_upload_dir();
	$mdocs = get_option('mdocs-list');
	$files = glob($upload_dir['basedir'].'/mdocs/*');
	$clean_up_files = array();
	$valid_file = false;
	foreach($files as $the_file) {
		foreach($mdocs as $key => $the_doc) {
			$the_file = explode('/',$the_file);
			$the_file = $the_file[count($the_file)-1];
			$d = explode('.',$the_doc['filename']);
			$d = $d[0];
			if(preg_match('/'.$d.'/', $the_file)) {
				$valid_file = true;
				break;
			}
			if($the_file == $mdocs_zip_file) {
				$valid_file = true;
				break;
			}
			if($the_file == $the_doc['filename']) {	
				$valid_file = true;
				break;
			} 
			if(is_array($the_doc['archived'])) {
				$valid_data = true;
				foreach($the_doc['archived'] as $the_archive) {
					if($the_file == $the_archive) {
						$valid_file = true;
						break;
					}
				}
			}
		}
		if($valid_file == false) array_push($clean_up_files, $the_file);
		$valid_file = false;
	}
	$valid_data = false;
	$clean_up_data = array();
	foreach($mdocs as $key => $the_doc) {
		if(
		   $the_doc['filename'] == null
		   || $the_doc['filename'] == ''
		   || !is_array($the_doc['archived']
		)
		   ) {
			$the_doc['index'] = $key;
			array_push($clean_up_data, $the_doc);
		}
	}
	
	return array('files'=> $clean_up_files, 'data'=>$clean_up_data);
}


?>