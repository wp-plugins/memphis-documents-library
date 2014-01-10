<?php
function mdocs_file_info_large($the_mdoc, $page_type='site', $index=0, $current_cat) {
	$upload_dir = wp_upload_dir();
	$the_mdoc_permalink = get_permalink($the_mdoc['parent']);
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
	$permalink = get_permalink($post->ID);
	if(preg_match('/\?page_id=/',$permalink) || preg_match('/\?p=/',$permalink)) {
		$mdocs_get = $permalink.'&cat=';
	} else $mdocs_get = $permalink.'?cat=';
	//if($mdocs_hide_all_files_non_members && $user_logged_in == false) $show_files = false;
	//elseif($mdocs_hide_all_files == false || $page_type == 'dashboard') $show_files = true;
	//else $show_files = false;
	$show_files = true;
	if( $show_files) {
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
	<div class="mdocs-post-button-box">
		<h2><a href="<?php echo $the_mdoc_permalink; ?>" title="<?php echo $the_mdoc['name']; ?> "><?php echo $the_mdoc['name']; ?></a>
		<?php
		if($mdocs_show_non_members  == 'off' && $user_logged_in == false ) { ?>
			<div class="mdocs-login-msg"><?php _e('Please login<br>to download this file'); ?></div>
		<?php } elseif($the_mdoc['non_members'] == 'on' || $user_logged_in) { ?>
			<input type="button" onclick="mdocs_download_file('<?php echo $the_mdoc['id']; ?>','<?php  echo $the_post->ID; ?>');" class="mdocs-download-btn" value="<?php echo __('Download'); ?>">
		</h2>
		<?php } else { ?>
			<div class="mdocs-login-msg"><?php _e('Please login<br>to download this file'); ?></div>
		<?php } ?>
	</div>
	<?php
	$user_logged_in = is_user_logged_in();
	if($user_logged_in && $mdocs_show_ratings) {
			if($the_rating['your_rating'] == 0) $text = __("Rate Me!");
			else $text = __("Your Rating");
			echo '<div class="mdocs-rating-container-small">';
			echo '<div class="mdocs-green">'.$text.'</div><div id="mdocs-star-container">';
			for($i=1;$i<=5;$i++) {
				if($the_rating['your_rating'] >= $i) echo '<i class="icon-star  gold mdocs-my-rating" id="'.$i.'"></i>';
				else echo '<i class="icon-star-empty mdocs-my-rating" id="'.$i.'"></i>';
			}
			echo '</div></div>';
		}
		?>
	<div class="mdocs-post-file-info">
		<?php if($mdocs_show_ratings) { ?><p><i class="icon-star"></i> <?php echo $the_rating['average']; ?> Stars (<?php echo $the_rating['total']; ?>)</p> <?php } ?>
		<?php if($mdocs_show_downloads) { ?><p class="mdocs-file-info"><i class="icon-cloud-download"></i> <b class="mdocs-orange"><?php echo $the_mdoc['downloads'].' '.__('Downloads'); ?></b></p> <?php } ?>
		<?php if($mdocs_show_author) { ?><p><i class="icon-pencil"></i> <?php _e('Author'); ?>: <i class="mdocs-green"><?php echo $the_mdoc['owner']; ?></i></p> <?php } ?>
		<?php if($mdocs_show_version) { ?><p><i class="icon-off"></i> <?php _e('Version') ?>:  <b class="mdocs-blue"><?php echo $the_mdoc['version']; ?></b>
			<?php if($page_type == 'site' ) { ?>
				<!--<a href="<?php echo $the_mdoc_permalink.'&cat='.$current_cat.'&mdocs-index='.$index; ?>&action=mdocs-versions">[ View More Versions ]</a>-->
			<?php } ?>
		</p><?php } ?>
		<?php if($mdocs_show_update) { ?><p><i class="icon-calendar"></i> <?php _e('Last Updated'); ?>: <b class="mdocs-red"><?php echo $last_modified; ?></b></p><?php } ?>
		<?php if(is_admin()) { ?>
		<p><i class="icon-file "></i> <?php echo __('File Status').': <b class="mdocs-olive">'.strtoupper($the_mdoc['file_status']).'</b>'; ?></p>
		<p><i class="icon-file-text"></i> <?php echo __('Post Status').': <b class="mdocs-salmon">'.strtoupper($post_status).'</b>'; ?></p>
		<?php } ?>
	</div>
<?php
		return $the_mdoc;
	}
}
?>