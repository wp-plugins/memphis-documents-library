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
	mdocs_list_header();
?>
<!-- COLOR PICKER 
<input type="text" value="#bada55" class="mdocs-color-picker" />
<input type="text" value="#bada55" class="mdocs-color-picker" data-default-color="#effeff" />
-->
<div class="mdocs-donate-btn">
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input name="cmd" type="hidden" value="_s-xclick" />
		<input name="encrypted" type="hidden" value="-----BEGIN PKCS7-----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-----END PKCS7----- " />
		<input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" type="image" />
		<img src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" alt="" width="1" height="1" border="0" />
	</form>
	
</div>
<h2><?php _e('Library Settings'); ?></h2>
<form enctype="multipart/form-data" method="post" action="options.php" class="mdocs-setting-form">
	<?php settings_fields( 'mdocs-global-settings' ); ?>
	<input type="hidden" name="mdocs-download-color-normal" value="<?php echo get_option( 'mdocs-download-color-normal' ); ?>" />
	<input type="hidden" name="mdocs-download-color-hover" value="<?php echo get_option( 'mdocs-download-color-hover' ); ?>" />
	<input type="hidden" name="mdocs-hide-all-posts-default" value="<?php echo get_option( 'mdocs-hide-all-posts-default' ); ?>" />
	<input type="hidden" name="mdocs-hide-all-posts-non-members-default" value="<?php echo get_option( 'mdocs-hide-all-posts-non-members-default' ); ?>" />

<table class="table form-table mdocs-settings-table">
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
		<th><?php _e('Private File Post Viewing'); ?></th>
		<td>
			<?php
			$wp_roles = get_editable_roles(); 
			$mdocs_roles = get_option('mdocs-view-private');
			foreach($wp_roles as $index => $role) {
			?>
			<input type="checkbox" name="mdocs-view-private[<?php echo $index; ?>]" value="1"  <?php checked($mdocs_roles[$index] , 1) ?> /> <span><?php echo $role['name']; ?></span><br>
			<?php
			}
			?>
		</td>
		<th><?php _e('Style Settings'); ?></th>
		<td>
			<h2><?php _e('Download Button Options'); ?></h2>
			<h4><?php _e('Background Options'); ?></h4>
			<label><?php _e('Background Color'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-download-color-normal'); ?>" name="mdocs-download-color-normal" id="bg-color-mdocs-picker" data-default-color="#d14836" /><br>
			<label><?php _e('Background Hover Color'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-download-color-hover'); ?>" name="mdocs-download-color-hover" id="bg-hover-color-mdocs-picker" data-default-color="#c34131" /><br>
			<label><?php _e('Text Color'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-download-text-color-normal'); ?>" name="mdocs-download-text-color-normal" id="bg-text-color-mdocs-picker" data-default-color="#ffffff" /><br>
			<label><?php _e('Text Hover Color'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-download-text-color-hover'); ?>" name="mdocs-download-text-color-hover" id="bg-text-hover-color-mdocs-picker" data-default-color="#ffffff" /><br>
			<h4><?php _e('Download Button Preview'); ?></h4>
			<button class="btn btn-primary mdocs-download-btn-config"><?php echo __('Download');?></button>
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
		
		if(isset($_GET['settings-updated']) && $_GET['page'] == 'memphis-documents.php') {
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
	-->
</table>

<input style="margin:15px;" type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</form>
<?php
}






?>