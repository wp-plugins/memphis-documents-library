<?php
function mdocs_file_info_small($the_mdoc, $page_type='site', $index=0, $current_cat) {
	global $post, $mdocs_img_types;
	$upload_dir = wp_upload_dir();
	$the_mdoc_permalink = htmlspecialchars(get_permalink($the_mdoc['parent']));
	$the_post = get_post($the_mdoc['parent']);
	$is_new = preg_match('/new=true/',$the_post->post_content);
	$post_date = strtotime($the_post->post_date);
	$last_modified = gmdate('d-m-y H:i',$the_mdoc['modified']+MDOCS_TIME_OFFSET);
	$user_logged_in = is_user_logged_in();
	$mdocs_show_non_members = $the_mdoc['non_members'];
	$mdocs_hide_all_files = get_option( 'mdocs-hide-all-files' );
	$mdocs_hide_all_posts = get_option( 'mdocs-hide-all-posts' );
	$mdocs_hide_all_files_non_members = get_option( 'mdocs-hide-all-files-non-members' );
	$mdocs_show_downloads = get_option( 'mdocs-show-downloads' );
	$mdocs_show_author = get_option( 'mdocs-show-author' );
	$mdocs_show_version = get_option( 'mdocs-show-version' );
	$mdocs_show_update = get_option( 'mdocs-show-update' );
	$mdocs_show_ratings = get_option( 'mdocs-show-ratings' );
	$mdocs_show_new_banners = get_option('mdocs-show-new-banners');
	$mdocs_time_to_display_banners = get_option('mdocs-time-to-display-banners');
	$mdocs_default_content = get_option('mdocs-default-content');
	$mdocs_show_description = get_option('mdocs-show-description');
	$mdocs_show_preview = get_option('mdocs-show-preview');
	if(isset($post)) $permalink = get_permalink($post->ID);
	else $permalink = '';
	$mdocs_desc = apply_filters('the_content', $the_mdoc['desc']);
	$mdocs_desc = str_replace('\\','',$mdocs_desc);
	if(preg_match('/\?page_id=/',$permalink) || preg_match('/\?p=/',$permalink)) {
		$mdocs_get = $permalink.'&cat=';
	} else $mdocs_get = $permalink.'?cat=';
	$tooltip = '<button class="mdocs-close-btn mdocs-close-desc" onclick="mdocs_close_preview(\'wp\');">Close</button>';
	$tooltip .= '<h1>'.$the_mdoc['filename'].'</h1>';
	$tooltip .= '<div class="mdocs-divider"></div>';
	if($page_type == 'dashboard') {
		$tooltip .= __('File Status','mdocs').':<b class="mdocs-olive"> '.$the_mdoc['file_status'].'</b><br>';
		$tooltip .= __('Post Status','mdocs').':<b class="mdocs-salmon"> '.$the_post->post_status.'</b><br>';
		if($mdocs_hide_all_files) $tooltip .= '<br><i class="fa fa-lock mdocs-orange"></i> '.__('All Files Are Hidden','mdocs');
		if($mdocs_hide_all_posts) $tooltip .= '<br><i class="fa fa-lock mdocs-red"></i> '.__('All Posts Are Hidden','mdocs'); 
	}
	$tooltip .= '<p>'.$mdocs_desc.'</p>';
	$tooltip .= '<div class="mdocs-divider"></div>';
	
	?> <div class="mdocs-admin-preview"></div> <?php
	$tooltip = htmlspecialchars($tooltip);
	
	if($mdocs_hide_all_files_non_members && $user_logged_in == false) $show_files = false;
	elseif($mdocs_hide_all_files == false || $page_type == 'dashboard') $show_files = true;
	else $show_files = false;
	if( $show_files) {
		$the_rating = mdocs_get_rating($the_mdoc);
		//var_dump($the_mdoc);
		if($mdocs_show_new_banners) {
			$modified = floor($the_mdoc['modified']/86400)*86400;
			$today = floor(time()/86400)*86400;
			$days = (($today-$modified)/86400);
			if($mdocs_time_to_display_banners > $days) {
				if($is_new == true) $status_tag = MDOCS_NEW_SMALL;
				else $status_tag = MDOCS_UPDATE_SMALL;
			} else $status_tag = '';
		} else $status_tag = '';
		if ( current_user_can('read_private_posts') ) $read_private_posts = true;
		else $read_private_posts = false;
	?>
		<tr>
			<td id="title" class="mdocs-tooltip">
				<?php
				if(!in_array($the_mdoc['type'], $mdocs_img_types) ) {
					if($mdocs_show_non_members  == 'on'  ) { ?>
					<span>
						<?php if($mdocs_show_preview) { ?><i class="fa fa-search mdocs-preview-icon" id="file-preview-<?php echo $the_mdoc['id']; ?>"></i><?php } ?>
						<?php if($mdocs_show_description) { ?><i class="fa fa-file-text mdocs-preview-icon" id="file-desc-<?php echo $the_mdoc['id']; ?>" title="<?php echo $tooltip; ?> "> </i><?php } ?>
					</span>
					<?php } elseif($the_mdoc['non_members'] == 'on' || $user_logged_in) { ?>
						<?php if($mdocs_show_preview) { ?><i class="fa fa-search mdocs-preview-icon" id="file-preview-<?php echo $the_mdoc['id']; ?>"> </i><?php } ?>
						<?php if($mdocs_show_description) { ?><i class="fa fa-file-text mdocs-preview-icon" id="file-desc-<?php echo $the_mdoc['id']; ?>" title="<?php echo $tooltip; ?> "> </i><?php } ?>
					<?php }
				} else { ?>
						<?php if($mdocs_show_preview) { ?><i class="fa fa-search mdocs-preview-icon" id="img-preview-<?php echo $the_mdoc['id']; ?>"> </i><?php } ?>
						<?php if($mdocs_show_description) { ?><i class="fa fa-file-text mdocs-preview-icon" id="file-desc-<?php echo $the_mdoc['id']; ?>" title="<?php echo $tooltip; ?> "> </i><?php } ?>
					<?php }  
				if($page_type == 'dashboard') {
				?>
					<a href="<?php echo 'admin.php?page=memphis-documents.php&cat='.$current_cat.'&action=update-doc&mdocs-index='.$index; ?>" ><?php echo str_replace('\\','',$the_mdoc['name']).$status_tag; ?></a>
				<?php }
				else {
					if($the_mdoc['post_status'] == 'private' && $read_private_posts == false) echo str_replace('\\','',$the_mdoc['name']).$status_tag;
					else { ?><a href="<?php echo $the_mdoc_permalink; ?>" ><?php echo str_replace('\\','',$the_mdoc['name']).$status_tag; ?></a><?php }} ?>
			</td>
			<?php if($mdocs_show_downloads) { ?><td id="downloads"><i class="fa fa-cloud-download"></i> <b class="mdocs-orange"><?php echo $the_mdoc['downloads'].' '.__('Downloads','mdocs'); ?></b></td><?php } ?>
			<?php if($mdocs_show_version) { ?><td id="version"><i class="fa fa-power-off"></i><b class="mdocs-blue"> <?php echo $the_mdoc['version']; ?></b></td><?php } ?>
			<?php if($mdocs_show_author) { ?><td id="owner"><i class="fa fa-pencil"></i> <i class="mdocs-green"><?php echo $the_mdoc['owner']; ?></i></td><?php } ?>
			<?php if($mdocs_show_update) { ?><td id="update"><i class="fa fa-calendar"></i> <b class="mdocs-red"><?php echo $last_modified; ?></b></td><?php } ?>
			<?php
			
				if($mdocs_show_ratings) {
					echo '<td id="rating">';
					for($i=1;$i<=5;$i++) {
						if($the_rating['average'] >= $i) echo '<i class="fa fa-star mdocs-gold" id="'.$i.'"></i>';
						elseif(ceil($the_rating['average']) == $i ) echo '<i class="fa fa-star-half-full mdocs-gold" id="'.$i.'"></i>';
						else echo '<i class="fa fa-star-o" id="'.$i.'"></i>';
					}
					echo '</td>';
				} ?>
			<td id="download">
				<?php
				if($mdocs_show_non_members  == 'off' && $user_logged_in == false ) { ?>
					<a href="<?php echo wp_login_url($the_mdoc_permalink); ?>" title="Login">Login</a>
				<?php } elseif($the_mdoc['non_members'] == 'on' || $user_logged_in) { ?>
					<a href="<?php echo site_url().'/?mdocs-file='.$the_mdoc['id'].'&mdocs-url='.$the_post->ID; ?>">Download</a>
				<?php } else { ?>
					<a href="<?php echo wp_login_url($the_mdoc_permalink); ?>" title="Login">Login</a>
				<?php }
				if($page_type == 'dashboard') {
					?>
					| <a href="<?php echo $the_mdoc_permalink; ?>" title="Visit the files post page" class="edit" target="_blank"><?php _e('Visit','mdocs'); ?></a> |
					<a class='submitdelete' onclick="return showNotice.warn();" href="<?php echo 'admin.php?mdocs-nonce='.$_SESSION['mdocs-nonce'].'&page=memphis-documents.php&cat='.$current_cat.'&action=delete-doc&mdocs-index='.$index; ?>"><?php _e('Delete','mdocs'); ?></a> |
					<a href="<?php echo 'admin.php?page=memphis-documents.php&cat='.$current_cat.'&mdocs-index='.$index; ?>&action=mdocs-versions" title="<?php _e('Versions','mdocs'); ?>" class="edit"><?php _e('Versions','mdocs'); ?></a>
					<?php
				} ?>
			</td>
		</tr>
<?php
	}
}
?>