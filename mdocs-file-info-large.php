<?php
function mdocs_file_info_large($the_mdoc, $page_type='site', $index=0, $current_cat) {
	global $post;
	ob_start();
	$upload_dir = wp_upload_dir();
	$the_mdoc_permalink = htmlspecialchars(get_permalink($the_mdoc['parent']));
	$the_post = get_post($the_mdoc['parent']);
	$is_new = preg_match('/new=true/',$the_post->post_content);
	$post_date = strtotime($the_post->post_date);
	$last_modified = gmdate('F jS Y \a\t g:i A',$the_mdoc['modified']+MDOCS_TIME_OFFSET);
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
	$post_status = $the_post->post_status;
	if(isset($post)) $permalink = get_permalink($post->ID);
	else $permalink = '';
	if(preg_match('/\?page_id=/',$permalink) || preg_match('/\?p=/',$permalink)) {
		$mdocs_get = $permalink.'&mdocs-cat=';
	} else $mdocs_get = $permalink.'?mdocs-cat=';
	//if($mdocs_hide_all_files_non_members && $user_logged_in == false) $show_files = false;
	//elseif($mdocs_hide_all_files == false || $page_type == 'dashboard') $show_files = true;
	//else $show_files = false;
	$show_files = true;
	if( $show_files) {
		mdocs_social_scripts();
		if(isset($_GET['mdocs-rating'])) $the_mdoc = mdocs_set_rating($index);
		$the_rating = mdocs_get_rating($the_mdoc);
		if($mdocs_show_new_banners) {
			$modified = floor($the_mdoc['modified']/86400)*86400;
			$today = floor(time()/86400)*86400;
			$days = (($today-$modified)/86400);
			if($mdocs_time_to_display_banners > $days) {
				if($is_new == true) echo MDOCS_NEW;
				else echo MDOCS_UPDATE;
			}
		}
	?>
	<div class="mdocs-post-header" data-mdocs-id="<?php echo $the_mdoc['id']; ?>">
	<div class="mdocs-post-button-box">
		<?php
		if ( current_user_can('read_private_posts') ) $read_private_posts = true;
		else $read_private_posts = false;
		if($the_mdoc['post_status'] == 'private' && $read_private_posts == false) echo '<h2>'.str_replace('\\','',$the_mdoc['name']).'</h2>';
		else { ?><h2><a href="<?php echo $the_mdoc_permalink; ?>" ><?php echo str_replace('\\','',$the_mdoc['name']); ?></a></h2><?php }
		?>
		<?php
		if($mdocs_hide_all_files) { ?><div class="mdocs-login-msg"><?php _e('This file can not<br>be downloaded.','mdocs'); ?></div><?php }
		else if($mdocs_show_non_members  == 'off' && $user_logged_in == false || $user_logged_in == false && $mdocs_hide_all_files_non_members) { ?>
			<div class="mdocs-login-msg"><?php _e('Please login<br>to download this file','mdocs'); ?></div>
		<?php } elseif($the_mdoc['non_members'] == 'on' || $user_logged_in ) { ?>
			<input type="button" onclick="mdocs_download_file('<?php echo $the_mdoc['id']; ?>','<?php  echo $the_post->ID; ?>');" class="mdocs-download-btn" value="<?php echo __('Download','mdocs'); ?>">
		</h2>
		<?php } else { ?>
			<div class="mdocs-login-msg"><?php _e('Please login<br>to download this file','mdocs'); ?></div>
		<?php } ?>
	</div>
	<?php
	$user_logged_in = is_user_logged_in();
	if($user_logged_in && $mdocs_show_ratings) {
			if($the_rating['your_rating'] == 0) $text = __("Rate Me!");
			else $text = __("Your Rating");
			echo '<div class="mdocs-rating-container-small">';
			echo '<div class="mdocs-green">'.$text.'</div><div id="mdocs-star-container">';
			echo '<div class="mdocs-ratings-stars" data-my-rating="'.$the_rating['your_rating'].'">';
			for($i=1;$i<=5;$i++) {
				if($the_rating['your_rating'] >= $i) echo '<i class="fa fa-star  mdocs-gold mdocs-my-rating" id="'.$i.'"></i>';
				else echo '<i class="fa fa-star-o mdocs-my-rating" id="'.$i.'"></i>';
			}
			echo '</div></div></div>';
		}
		?>
	<div class="mdocs-post-file-info">
		<?php if($mdocs_show_ratings) { ?><p><i class="fa fa-star"></i> <?php echo $the_rating['average']; ?> <?php _e('Stars', 'mdocs'); ?> (<?php echo $the_rating['total']; ?>)</p> <?php } ?>
		<?php if($mdocs_show_downloads) { ?><p class="mdocs-file-info"><i class="fa fa-cloud-download"></i> <b class="mdocs-orange"><?php echo $the_mdoc['downloads'].' '.__('Downloads','mdocs'); ?></b></p> <?php } ?>
		<?php if($mdocs_show_author) { ?><p><i class="fa fa-pencil"></i> <?php _e('Author','mdocs'); ?>: <i class="mdocs-green"><?php echo $the_mdoc['owner']; ?></i></p> <?php } ?>
		<?php if($mdocs_show_version) { ?><p><i class="fa fa-power-off"></i> <?php _e('Version','mdocs') ?>:  <b class="mdocs-blue"><?php echo $the_mdoc['version']; ?></b>
			<?php if($page_type == 'site' ) { ?>
				<!--<a href="<?php echo $the_mdoc_permalink.'&mdocs-cat='.$current_cat.'&mdocs-index='.$index; ?>&action=mdocs-versions">[ View More Versions ]</a>-->
			<?php } ?>
		</p><?php } ?>
		<?php if($mdocs_show_update) { ?><p><i class="fa fa-calendar"></i> <?php _e('Last Updated','mdocs'); ?>: <b class="mdocs-red"><?php echo $last_modified; ?></b></p><?php } ?>
		<?php if(is_admin()) { ?>
		<p><i class="fa fa-file "></i> <?php echo __('File Status','mdocs').': <b class="mdocs-olive">'.strtoupper($the_mdoc['file_status']).'</b>'; ?></p>
		<p><i class="fa fa-file-text"></i> <?php echo __('Post Status','mdocs').': <b class="mdocs-salmon">'.strtoupper($post_status).'</b>'; ?></p>
		<?php } ?>
	</div>
	</div>
<?php
		$the_page = ob_get_clean();
		return $the_page;
	}
}
?>