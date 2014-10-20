<?php
function mdocs_the_list($att=null) {
	global $post, $current_cat_array, $parent_cat_array;
	ob_start();
	$is_read_write = mdocs_check_read_write();
	if($is_read_write) {
		global $post;
		$site_url = site_url();
		$upload_dir = wp_upload_dir();	
		$mdocs = get_option('mdocs-list');
		$cats =  get_option('mdocs-cats');
		$current_cat = '';
		if(isset($_GET['mdocs-cat'])) $current_cat = $_GET['mdocs-cat'];
		elseif(!is_string($cats)) $current_cat = $cats[0]['slug'];
		if(isset($att['cat']) && $att['cat'] != 'All Files' && !isset($_GET['mdocs-cat'])) {
			//$current_cat = array_search($att['cat'],$cats);
			foreach($cats as $cat) if($att['cat'] == $cat['name']) $current_cat = $cat['slug'];
		} elseif(isset($att['cat']) && $att['cat'] == 'All Files') $current_cat = 'all';
		$permalink = get_permalink($post->ID);
		if(preg_match('/\?page_id=/',$permalink) || preg_match('/\?p=/',$permalink)) {
			$mdocs_get = $permalink.'&mdocs-cat=';
		} else $mdocs_get = $permalink.'?mdocs-cat=';
		mdocs_get_children_cats(get_option('mdocs-cats'),$current_cat);
	?>
	<div class="mdocs-container">
		<?php if(isset($att['header'])) echo '<p>'.__($att['header']).'</p>'; ?>
		<?php $mdocs = mdocs_sort_by($mdocs); ?>
		<h2 class="mdocs-nav-wrapper">
			<div class="mdocs-wp-preview"></div>
			<div id="icon-edit-pages" class="icon32"><br></div>
			<?php
			if(!empty($cats) && !isset($att['cat'])) {
				foreach( $cats as $index => $cat ){
					if(isset($cat['slug']) && !empty($current_cat_array)) {
						if( $cat['slug'] == $current_cat) $class = ' mdocs-nav-tab-active';
						elseif(isset($cats[$current_cat_array['base_parent']]['slug']) && $cats[$current_cat_array['base_parent']]['slug'] == $cat['slug']) $class = ' mdocs-nav-tab-active';
						else $class = '';
					} else $class = '';
					echo '<a class="mdocs-nav-tab'.$class.'" href="'.$mdocs_get.$cat['slug'].'"><span>'.$cat['name'].'</span></a>';
				}
			} else echo '<p>'.__($att['cat']).'</p>';
			?>
		</h2>
		<?php
		$count = 0;
		
		if(get_option('mdocs-list-type') == 'small') echo '<table class="mdocs-list-table">';
		// SUB CATEGORIES
		if(isset($current_cat_array['children'])) $num_cols = mdocs_get_subcats($current_cat_array, $parent_cat_array);
		else $num_cols = mdocs_get_subcats($current_cat_array, $parent_cat_array, false);
		
		foreach($mdocs as $index => $the_mdoc) {			
			if($the_mdoc['cat'] == $current_cat || $current_cat == 'all') {
				if($the_mdoc['file_status'] == 'public' ) {
					$count ++;
					$mdocs_post = get_post($the_mdoc['parent']);
					$mdocs_desc = apply_filters('the_content', $mdocs_post->post_excerpt);
					
					if(get_option('mdocs-list-type') == 'small') {
						mdocs_file_info_small($the_mdoc, 'site', 0, $current_cat); 
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
								<?php echo mdocs_file_info_large($the_mdoc, 'site', $index, $current_cat); ?>
								<div class="mdocs-clear-both"></div>
								<?php echo mdocs_social($the_mdoc); ?>
							</div>
							<div class="mdocs-clear-both"></div>
							<?php echo mdocs_des_preview_tabs($the_mdoc); ?>
							<div class="mdocs-clear-both"></div>
							</div>
							<?php
						}
					}
				}
			} 
		}
		if($count == 0) {
			?><tr><td colspan="<?php echo $num_cols; ?>"><p class="mdocs-nofiles" ><?php _e('No files found in this category.','mdocs'); ?></p></td></tr><?php
		}
		if(get_option('mdocs-list-type') == 'small') echo '</table>';
	} else mdocs_errors(__('Unable to create the directory "mdocs" which is needed by Memphis Documents Library. Its parent directory is not writable by the server?','mdocs'),'error');
	$the_list = ob_get_clean();
	return $the_list;
}
?>