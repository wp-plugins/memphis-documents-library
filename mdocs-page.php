<?php
function mdocs_page() {
		$is_read_write = mdocs_check_read_write();
	if($is_read_write) {
		global $post;
		$site_url = site_url();
		$upload_dir = wp_upload_dir();	
		$mdocs = get_option('mdocs-list');
		$cats =  get_option('mdocs-cats');
		if(isset($_GET['cat'])) $current_cat = $_GET['cat'];
		elseif(!is_string($cats)) $current_cat = key($cats);
		
		$permalink = get_permalink($post->ID);
		if(preg_match('/\?page_id=/',$permalink) || preg_match('/\?p=/',$permalink)) {
			$mdocs_get = $permalink.'&cat=';
		} else $mdocs_get = $permalink.'?cat=';
		
	?>
	<div class="mdocs-container">
		<?php $mdocs = mdocs_sort_by($mdocs); ?>
		<h2 class="mdocs-nav-wrapper">
			<div id="icon-edit-pages" class="icon32"><br></div>
			<?php
			if(!empty($cats)) {
				foreach( $cats as $cat => $name ){
					$class = ( $cat == $current_cat ) ? ' mdocs-nav-tab-active' : '';
					echo '<a class="mdocs-nav-tab'.$class.'" href="'.$mdocs_get.$cat.'">'.$name.'<hr /></a>';
				}
			}
			?>
		</h2>
		<?php
		$count = 0;
		
		if(get_option('mdocs-list-type') == 'small') echo '<table class="mdocs-list-table">';
		foreach($mdocs as $the_mdoc) {
			if($the_mdoc['cat'] == $current_cat) {
				if($the_mdoc['file_status'] == 'public' ) {
					$count ++;
					$mdocs_post = get_post($the_mdoc['parent']);
					$mdocs_desc = apply_filters('the_content', $mdocs_post->post_excerpt);
					if(get_option('mdocs-list-type') == 'small') {
					?>
						<?php mdocs_file_info_small($the_mdoc, 'site', 0, $current_cat); ?>
					<?php
					} else {
						$user_logged_in = is_user_logged_in();
						$mdocs_hide_all_files = get_option( 'mdocs-hide-all-files' );
						$mdocs_hide_all_files_non_members = get_option( 'mdocs-hide-all-files-non-members' );
						if($mdocs_hide_all_files_non_members && $user_logged_in == false) $show_files = false;
						elseif($mdocs_hide_all_files == false ) $show_files = true;
						else $show_files = false;
						if( $show_files) {
							?>
							<div class="mdocs-post">
								<?php mdocs_file_info_large($the_mdoc, 'site', $current_cat); ?>
								<div class="mdocs-clear-both"></div>
								<?php mdocs_social($the_mdoc); ?>
							</div>
							<div class="mdocs-clear-both"></div>
							<h3>Description</h3>
							<div class="mdoc-desc">
								<?php echo $mdocs_desc; ?>
							</div>
							<div class="mdocs-clear-both"></div>
							</div>
							<?php
						}
					}
				}
			} 
		}
		if($count == 0) {
			?> <p class="mdocs-nofiles" ><?php _e('No files found in this category.'); ?></p> <?php
		}
		if(get_option('mdocs-list-type') == 'small') echo '</table>';
	} else mdocs_errors(__('Unable to create the directory "mdocs" which is needed by Memphis Documents Library. Its parent directory is not writable by the server?'),'error');
}
?>