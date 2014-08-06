<?php
function mdocs_download_rights($the_mdoc) {
	$the_mdoc_permalink = htmlspecialchars(get_permalink($the_mdoc['parent']));
	$mdocs_show_non_members = $the_mdoc['non_members'];
	if($mdocs_show_non_members  == 'off' && is_user_logged_in() == false && is_admin() == false) { ?>
		<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo wp_login_url($the_mdoc_permalink); ?>"><i class="fa fa-cloud-download"></i> <?php _e('Download'); ?></a></li><?php
	} elseif($the_mdoc['non_members'] == 'on' || is_user_logged_in() || is_admin()) { ?>
		<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo site_url().'/?mdocs-file='.$the_mdoc['id'].'&mdocs-url='.$the_post->ID; ?>"><i class="fa fa-cloud-download"></i> <?php _e('Download'); ?></a></li><?php
	} else { ?>
	<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo wp_login_url($the_mdoc_permalink); ?>"><i class="fa fa-cloud-download"></i> <?php _e('Download'); ?></a></li>
	<?php } 
}
function mdocs_preview_rights($the_mdoc) {
	global $mdocs_img_types;
	$mdocs_show_preview = get_option('mdocs-show-preview');
	$mdocs_show_description = get_option('mdocs-show-description');
	$mdocs_show_non_members = $the_mdoc['non_members'];
	$preview_type = 'file-preview';
	
	if(!in_array($the_mdoc['type'], $mdocs_img_types) ) $preview_type = 'file-preview';
	else $preview_type = 'img-preview';
	
	?>
	<li role="presentation"><a class="<?php echo $preview_type; ?>" role="menuitem" tabindex="-1" data-toggle="modal" data-target="#mdocs-file-preview" data-mdocs-id="<?php echo $the_mdoc['id']; ?>" data-is-admin="<?php echo is_admin(); ?>" href=""><i class="fa fa-search mdocs-preview-icon" ></i> <?php _e('Preview'); ?></a></li>
	<?php
}
?>