<?php
function mdocs_file_info_small($the_mdoc, $page_type='site', $index=0, $current_cat) {
	$upload_dir = wp_upload_dir();
	$the_mdoc_permalink = get_permalink($the_mdoc['parent']);
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
	$mdocs_show_new_banners = get_option('mdocs-show-new-banners');
	$mdocs_time_to_display_banners = get_option('mdocs-time-to-display-banners');
	$permalink = get_permalink($post->ID);
	if(preg_match('/\?page_id=/',$permalink) || preg_match('/\?p=/',$permalink)) {
		$mdocs_get = $permalink.'&cat=';
	} else $mdocs_get = $permalink.'?cat=';
	$tooltip = '<b class="mdocs-blue">'.__('Description').'</b><br>';
	$tooltip .= $the_mdoc['desc'].'';
	if($page_type == 'dashboard') {
		$tooltip .= '<br><br>'.__('File Status').':<b class="mdocs-olive"> '.$the_mdoc['file_status'].'</b><br>';
		$tooltip .= __('Post Status').':<b class="mdocs-salmon"> '.$the_post->post_status.'</b><br>';
		if($mdocs_hide_all_files) $tooltip .= '<br><i class="icon-lock mdocs-orange"></i> '.__('All Files Are Hidden');
		if($mdocs_hide_all_posts) $tooltip .= '<br><i class="icon-lock mdocs-red"></i> '.__('All Posts Are Hidden'); 
	}
	
	$tooltip = htmlspecialchars($tooltip);
	
	if($mdocs_hide_all_files_non_members && $user_logged_in == false) $show_files = false;
	elseif($mdocs_hide_all_files == false || $page_type == 'dashboard') $show_files = true;
	else $show_files = false;
	if( $show_files) {
		if($mdocs_show_new_banners) {
			$modified = floor($the_mdoc['modified']/86400)*86400;
			$today = floor(time()/86400)*86400;
			$days = (($today-$modified)/86400);
			if($mdocs_time_to_display_banners > $days) {
				if($is_new == true) $status_tag = MDOCS_NEW_SMALL;
				else $status_tag = MDOCS_UPDATE_SMALL;
			}
		}
	?>
		<tr>
			<td id="title" class="mdocs-tooltip"><?php if($page_type == 'dashboard') { ?><i class="mdocs-show-social icon-plus-sign-alt mdocs-green" id="mdocs-show-social-<?php echo $index; ?> "></i> <a href="<?php echo 'admin.php?page=memphis-documents.php&cat='.$current_cat.'&action=update-doc&mdocs-index='.$index; ?>" title="<?php echo $tooltip; ?> "><?php echo $the_mdoc['name'].$status_tag; ?></a><?php } else { ?><a href="<?php echo $the_mdoc_permalink; ?>" title="<?php echo $tooltip; ?> "><?php echo $the_mdoc['name'].$status_tag; ?></a><?php } ?></td>
			<?php if($mdocs_show_downloads) { ?><td id="downloads"><i class="icon-cloud-download"></i> <b class="mdocs-orange"><?php echo $the_mdoc['downloads'].' '.__('Downloads'); ?></b></td><?php } ?>
			<?php if($mdocs_show_version) { ?><td id="version"><i class="icon-off"></i><b class="mdocs-blue"> <?php echo $the_mdoc['version']; ?></b></td><?php } ?>
			<?php if($mdocs_show_author) { ?><td id="owner"><i class="icon-pencil"></i> <i class="mdocs-green"><?php echo $the_mdoc['owner']; ?></i></td><?php } ?>
			<?php if($mdocs_show_update) { ?><td id="update"><i class="icon-calendar"></i> <b class="mdocs-red"><?php echo $last_modified; ?></b></td><?php } ?>
			<td id="download">
				<?php
				if($mdocs_show_non_members  == 'off' && $user_logged_in == false ) { ?>
					<a href="<?php echo wp_login_url($the_mdoc_permalink); ?>" title="Login">Login</a>
				<?php } elseif($the_mdoc['non_members'] == 'on' || $user_logged_in) { ?>
					<a href="<?php echo site_url().'/?mdocs-file='.$the_mdoc['id']; ?>">Download</a>
				<?php } else { ?>
					<a href="<?php echo wp_login_url($the_mdoc_permalink); ?>" title="Login">Login</a>
				<?php }
				if($page_type == 'dashboard') {
					?>
					| <a href="<?php echo $the_mdoc_permalink; ?>" title="Visit the files post page" class="edit" target="_blank"><?php _e('Visit'); ?></a> |
					<a class='submitdelete' onclick="return showNotice.warn();" href="<?php echo 'admin.php?mdocs-nonce='.$_SESSION['mdocs-nonce'].'&page=memphis-documents.php&cat='.$current_cat.'&action=delete-doc&mdocs-index='.$index; ?>"><?php _e('Delete'); ?></a> |
					<a href="<?php echo 'admin.php?page=memphis-documents.php&cat='.$current_cat.'&mdocs-index='.$index; ?>&action=mdocs-versions" title="<?php _e('Versions'); ?>" class="edit"><?php _e('Versions'); ?></a>
					<?php
				} ?>
			</td>
		</tr>
		<tr><td colspan="6" style="display: none" id="mdocs-social-index-<?php echo $index; ?>"><?php mdocs_social($the_mdoc); ?></td></tr>
<?php
	}
}
function mdocs_file_info_large($the_mdoc, $page_type='site', $current_cat) {
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
			<input type="button" onclick="mdocs_download_file('<?php echo $the_mdoc['id']; ?>');" class="mdocs-download-btn" value="<?php echo __('Download'); ?>">
		</h2>
		<?php } else { ?>
			<div class="mdocs-login-msg"><?php _e('Please login<br>to download this file'); ?></div>
		<?php } ?>
	</div>
	<div class="mdocs-post-file-info">
		<!--<p><i class="icon-star"></i> 4.4 Stars (102)</p>-->
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
	}
}

function mdocs_edit_file($the_mdocs, $index, $current_cat) {
	?>
	<div class="mdocs-edit-file">
		<span class="update" id="<?php echo $index ?>">
			<i class="icon-pencil"></i> <a href="<?php echo 'admin.php?page=memphis-documents.php&cat='.$current_cat.'&action=update-doc&mdocs-index='.$index; ?>" title="Update this file" class="edit"><?php _e('Update'); ?></a> |
		</span>
		<span class='delete'>
			<i class="icon-remove"></i> <a class='submitdelete' onclick="return showNotice.warn();" href="<?php echo 'admin.php?mdocs-nonce='.$_SESSION['mdocs-nonce'].'&page=memphis-documents.php&cat='.$current_cat.'&action=delete-doc&mdocs-index='.$index; ?>"><?php _e('Delete'); ?></a> |
		</span>
		<span class="versions">
			<i class="icon-off"></i> <a href="<?php echo 'admin.php?page=memphis-documents.php&cat='.$current_cat.'&mdocs-index='.$index; ?>&action=mdocs-versions" title="<?php _e('Versions'); ?>" class="edit"><?php _e('Versions'); ?></a></span>
	</div>
	<?php
}

function mdocs_post_page($att=null) {
	global $post;
	$post_category = get_the_category( $post->ID );
	if($post_category[0]->slug == 'mdocs-media') {
		$mdocs = get_option('mdocs-list');
		//$mdocs = mdocs_sort_by($mdocs);
		foreach($mdocs as $files => $value) {
			if($value['parent'] == $post->ID) { $the_mdoc = $value; break; }
		}
		$query = new WP_Query('post_type=attachment&post_status=inherit&post_parent='.$post->ID);
		$user_info = get_userdata($post->post_author);
		$mdocs_file = $query->post;
		$upload_dir = wp_upload_dir();
		$file = substr(strrchr($mdocs_file->post_excerpt, '/'), 1 );
		$filesize = filesize($upload_dir['basedir'].'/mdocs/'.$file);
		$query = new WP_Query('pagename=mdocuments-library');	
		$permalink = get_permalink($query->post->ID);
		if( strrchr($permalink, '?page_id=')) $mdocs_link = site_url().'/'.strrchr($permalink, '?page_id=');
		else $mdocs_link = site_url().'/'.$query->post->post_name.'/';
		$mdocs_desc = apply_filters('the_content', $post->post_excerpt);
		?>
		<div class="mdocs-post">
			<?php mdocs_file_info_large($the_mdoc, 'site', null); ?>
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
	} else {
		print do_shortcode(nl2br(get_the_content('Continue Reading &rarr;')));
	}
}

function mdocs_rename_file($upload, $file_name) {
	$upload_dir = wp_upload_dir();
	$index = 0;
	$org_filename = $file_name;
	while(file_exists($upload_dir['basedir'].'/mdocs/'.$file_name)) {
		$index++;
		$explode = explode('.',$org_filename);
		$tail = $index.'.'.$explode[count($explode)-1];
		array_pop($explode);
		$file_name = implode('',$explode).$tail;
	}
	$upload['url'] = $upload_dir['baseurl'].'/mdocs/'.$file_name;
	$upload['file'] = $upload_dir['basedir'].'/mdocs/'.$file_name;
	$upload['filename'] = $file_name;
	$name = substr($file_name, 0, strrpos($file_name, '.') );
	if($_POST['mdocs-name'] == '') $upload['name'] = $name;
	else $upload['name'] = $_POST['mdocs-name'];
	return $upload;
}

function mdocs_process_file($file, $import=false) {
	global $current_user;
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	$mdocs_type = $_POST['mdocs-type'];
	if($_POST['mdocs-desc'] == '') $desc = MDOCS_DEFAULT_DESC;
	else $desc = $_POST['mdocs-desc'];
	$post_status = $file['post-status'];
	if($import) $desc = $file['desc'];
	$upload_dir = wp_upload_dir();
	if($file['modifed'] != null) $modifed_date = date('Y-m-d H:i:s', $file['modifed']+MDOCS_TIME_OFFSET);
	else $modifed_date = MDOCS_CURRENT_TIME;
	if($import == false) {
		$upload['url'] = $upload_dir['baseurl'].'/mdocs/'.$file['name'];
		$upload['file'] = $upload_dir['basedir'].'/mdocs/'.$file['name'];
		$upload['filename'] = $file['name'];
		if(file_exists($upload_dir['basedir'].'/mdocs/'.$file['name'])) $upload = mdocs_rename_file($upload, $file['name']);
		else {
			$name = substr($file['name'], 0, strrpos($file['name'], '.') );
			if($_POST['mdocs-name'] == '') $upload['name'] = $name;
			else $upload['name'] = $_POST['mdocs-name'];
		}
		move_uploaded_file($file['tmp_name'], $upload['file']);
		//chmod($upload['file'], 0600);
	} else {
		$upload['url'] = $upload_dir['baseurl'].'/mdocs/'.$file['filename'];
		$upload['file'] = $upload_dir['basedir'].'/mdocs/'.$file['filename'];
		$upload['filename'] = $file['filename'];
		$upload['name'] = $file['name'];
	}
	$wp_filetype = wp_check_filetype($upload['file'], null );
	$mdocs_post_cat = get_category_by_slug( 'mdocs-media' );
	if($mdocs_type == 'mdocs-add' || $import == true) {
		$mdocs_post = array(
			'post_title' => $upload['name'],
			'post_status' => $post_status,
			'post_content' => '[mdocs_post_page new=true]',
			'post_author' => $current_user->ID,
			'post_category' => array($mdocs_post_cat->cat_ID),
			'post_excerpt' => $desc,
			'post_date' => $modifed_date,
		);
		$mdocs_post_id = wp_insert_post( $mdocs_post );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => preg_replace('/\.[^.]+$/', '', basename($upload['name'])),
			'post_content' => '',
			'post_author' => $current_user->ID,
			'post_status' => 'inherit',
			'post_excerpt' => $upload['url'],
			'comment_status' => 'closed',
			'post_date' => $modifed_date,
		 );
		$mdocs_attach_id = wp_insert_attachment( $attachment, $upload['file'], $mdocs_post_id );
		$mdocs_attach_data = wp_generate_attachment_metadata( $mdocs_attach_id, $upload['file'] );
		wp_update_attachment_metadata( $mdocs_attach_id, $mdocs_attach_data );
		$upload['parent_id'] = $mdocs_post_id;
		$upload['attachment_id'] = $mdocs_attach_id;
		wp_set_post_tags( $mdocs_post_id, $upload['name'].', memphis documents library,memphis,documents,library,media,'.$wp_filetype['type'] );
	} elseif($mdocs_type == 'mdocs-update') {
		$mdocs_post = array(
			'ID' => $file['parent'],
			'post_title' => $upload['name'],
			'post_status' =>$post_status,
			'post_content' => '[mdocs_post_page]',
			'post_author' => $current_user->ID,
			'post_category' => array($mdocs_post_cat->cat_ID),
			'post_excerpt' => $desc,
			'post_date' => $modifed_date,
		);
		//var_dump($mdocs_post);
		$mdocs_post_id = wp_update_post( $mdocs_post );
		$attachment = array(
			'ID' => $file['id'],
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => $upload['name'],
			'post_content' => '',
			'post_author' => $current_user->ID,
			'post_status' => 'inherit',
			'post_excerpt' => $upload['url'],
			'post_date' => $modifed_date,
		 );
		update_attached_file( $file['id'], $upload['file'] );
		$mdocs_attach_id = wp_update_post( $attachment );
		$mdocs_attach_data = wp_generate_attachment_metadata( $mdocs_attach_id, $upload['file'] );
		wp_update_attachment_metadata( $mdocs_attach_id, $mdocs_attach_data );
		wp_set_post_tags( $mdocs_post_id, $upload['name'].', memphis documents library,memphis,documents,library,media,'.$wp_filetype['type'] );
	}
	$upload['desc'] = $desc;
	return $upload;
}

function mdocs_nonce() {
	session_start();
	define('MDOCS_NONCE',$_SESSION['mdocs-nonce']);
	//var_dump(MDOCS_NONCE);
	if(!isset($_SESSION['mdocs-nonce']) || isset($_REQUEST['mdocs-nonce'])) $_SESSION['mdocs-nonce'] = md5(rand(0,1000000));
	session_write_close();	
}

function mdocs_sort_by($mdocs, $ypos=0, $page_type='site', $echo=true) {
	if(isset($_POST['mdocs-sort-type'])) $sort_type = $_POST['mdocs-sort-type']; 
	elseif(isset($_COOKIE['mdocs-sort-type-'.$page_type])) $sort_type = $_COOKIE['mdocs-sort-type-'.$page_type];
	else $sort_type = 'modified';
	if(isset($_POST['mdocs-sort-range'])) $sort_range = $_POST['mdocs-sort-range'];
	elseif(isset($_COOKIE['mdocs-sort-range-'.$page_type])) $sort_range = $_COOKIE['mdocs-sort-range-'.$page_type];
	else $sort_range = 'desc';
	if(isset($_POST['mdocs-list-type'])) update_option('mdocs-list-type-dashboard',$_POST['mdocs-list-type']);
	$list_type = get_option('mdocs-list-type-dashboard');
	if($echo) {
?>
<div class="mdocs-sort" style="top:<?php echo $ypos; ?>px">
	<form enctype="multipart/form-data" method="post" action="">
		<?php if($page_type == 'dashboard') { ?>
		<i class="icon-cogs mdocs-orange"></i>
		<label><?php _e('List Size'); ?>:</label>
		<input type="radio" name="mdocs-list-type" value="large" <?php if($list_type == 'large') echo 'checked'; ?> /> <label><?php _e('Large'); ?></label>
		<input type="radio" name="mdocs-list-type" value="small" <?php if($list_type == 'small') echo 'checked'; ?>/> <label><?php _e('Small'); ?></label>
		<?php } ?>
		
		<i class="icon-cogs mdocs-green"></i><label>
		<input type="radio" name="mdocs-sort-range" value="asc" <?php if($sort_range == 'asc') echo 'checked'; ?> /> <label><?php _e('Ascending'); ?></label>
		<input type="radio" name="mdocs-sort-range" value="desc" <?php if($sort_range == 'desc') echo 'checked'; ?>/> <label><?php _e('Descending'); ?></label>
		
		<i class="icon-cogs mdocs-blue"></i><label> <?php _e('Sort By'); ?>:</label>
		<select name="mdocs-sort-type">
			<option value="name" <?php if($sort_type == 'name') echo 'selected'; ?>><?php _e('File Name'); ?></option>
			<option value="downloads" <?php if($sort_type == 'downloads') echo 'selected'; ?>><?php _e('Number of Downloads'); ?></option>
			<option value="version" <?php if($sort_type == 'version') echo 'selected'; ?>><?php _e('Version'); ?></option>
			<option value="owner" <?php if($sort_type == 'owner') echo 'selected'; ?>><?php _e('Author'); ?></option>
			<option value="modified" <?php if($sort_type == 'modified') echo 'selected'; ?>><?php _e('Last Updated'); ?></option>
		</select>
		<input type="submit" value="go" />
	</form>
</div>
<?php
	}
	if($sort_range == 'desc') $mdocs = mdocs_array_sort($mdocs, $sort_type, SORT_DESC);
	if($sort_range == 'asc') $mdocs = mdocs_array_sort($mdocs, $sort_type, SORT_ASC);
	if($mdocs == null) $mdocs = array();
	return $mdocs;
}

function mdocs_array_sort($the_array, $orderby, $sort_types=SORT_ASC) {
    if($the_array != null) {
		foreach($the_array as $a){ 
			foreach($a as $key=>$value){ 
				if(!isset($sortArray[$key])){ 
					$sortArray[$key] = array(); 
				} 
				$sortArray[$key][] = $value; 
			} 
		}
		
		$array_lowercase = array_map('strtolower', $sortArray[$orderby]);
		array_multisort($array_lowercase, $sort_types, SORT_STRING,$the_array);
		$the_array = array_values($the_array);
		return $the_array;
	} else return null;
}

function mdocs_export_file_status() {
	$upload_dir = wp_upload_dir();
	$mdocs_zip = get_option('mdocs-zip');
	if(file_exists($upload_dir['basedir'].'/mdocs/'.$mdocs_zip)) {
		mdocs_errors(MDOCS_ZIP_STATUS_OK);
	} else mdocs_errors(MDOCS_ZIP_STATUS_FAIL,'error');
}


function mdocs_errors($error, $type='updated') {
	if($type == 'error') $error = '<b>'.__('Memphis Error').': </b>'.$error;
	else $error = '<b>'.__('Memphis Info').': </b>'.$error;
	?>
	<div class="<?php echo $type; ?>">
		<div id="mdocs-error">
		<p><?php _e($error); ?></p>
		</div>
	</div>
    <?php
}

function mdocs_social($the_mdoc) {
	$user_logged_in = is_user_logged_in();
	if(get_option('mdocs-hide-all-posts') == false && get_option('mdocs-hide-all-files') == false || $user_logged_in &&  get_option('mdocs-hide-all-posts-non-members') ) {
		$the_permalink = get_permalink($the_mdoc['parent']);
		?>
		<div class="mdocs-social"  id="mdocs-social-<?php echo $the_mdoc['id']; ?>">
			<div class="mdocs-share" onclick="mdocs_share('<?php echo $the_permalink; ?>', 'mdocs-social-<?php echo $the_mdoc['id']; ?>');"><p><i class="icon-share-sign mdocs-green"></i> Share</p></div> 
		<?php if($the_mdoc['show_social'] ==='on' && get_option('mdocs-show-social') ) { ?>
			<div class="mdocs-tweet"><a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo $the_permalink;?>" data-counturl="<?php echo $the_permalink;?>" data-text="<?php echo __('Download').' #'.strtolower(preg_replace('/-| /','_',$the_mdoc['name'])).' #MemphisDocumentsLibrary'; ?>" width="50">Tweet</a></div>
			<div class="mdocs-like"><iframe src="//www.facebook.com/plugins/like.php?href=<?php echo $the_permalink;?>&amp;width=450&amp;height=21&amp;colorscheme=light&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;send=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe></div>
			<div class="mdocs-plusone" ><div class="g-plusone" data-size="medium" data-href="<?php echo $the_permalink;?>"></div></div>
		<?php
		}
	} else {
		?>
		<div class="mdocs-social"  ><?php _e('You must disable hide all post and/or hide all files in order to use social functionality.'); ?>   
		<?php
	}
}

function mdocs_social_scripts() {
	?>
<div id="fb-root"></div>
<script type="text/javascript">
//FACEBOOK LIKE
(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&status=0&appId=12345";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
//TWITTER TWEET
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
//GOOGLE +1
(function() {
  var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
  po.src = 'https://apis.google.com/js/plusone.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();
</script>
	<?php
}
function mdocs_is_bot() {
	$upload_dir = wp_upload_dir();
	$bots = strip_tags(file_get_contents(MDOCS_ROBOTS));
	$bots = explode('|:::|',$bots);
	foreach($bots as $bot) {
        if ( stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false ) return true;
    }
	return false;
}

/* RETIRED FUNCTIONS 
function mdocs_update_bot_list() {
	$upload_dir = wp_upload_dir();
	$bots = strip_tags(file_get_contents('http://www.robotstxt.org/db.html'));
	$bots = nl2br($bots);
	$bot_text_start = 'If you need this data in raw format, see Robots Database Export page.';
	$bot_text_end = 'Print format';
	$bots_start = strpos($bots, $bot_text_start)+strlen($bot_text_start);
	$bots_end = strpos($bots, $bot_text_end);
	$bots = substr($bots, $bots_start, $bots_end-$bots_start);
	$bots = explode('<br />',$bots);
	$bots = str_replace(array('.', ' ', "\n", "\t", "\r"), '', $bots);
	$bots = array_filter($bots);
	$bots = array_filter($bots, 'string_length_less_than_one');
	$bots = mdocs_add_bots($bots);
	file_put_contents($upload_dir['basedir'].'/mdocs/mdocs-robots.txt', implode('|:::|',$bots));
	//print_r($bots);
}
function mdocs_add_bots($bots) {
	array_push($bots, 'NING');
	array_push($bots, 'TweetmemeBot');
	array_push($bots, 'Twitterbot');
	array_push($bots, 'Butterfly');
	array_push($bots, 'rogerbot');
	array_push($bots, 'JS-Kit');
	array_push($bots, 'UnwindFetchor');
	array_push($bots, '+https://developers.google.com/+/web/snippet/');
	array_push($bots, 'YandexBot');
	return $bots;
}
function string_length_less_than_one($var) { if(strlen($var) > 1) return str_replace(array('.', ' ', "\n", "\t", "\r"), '', $var); }
*/

function mdocs_send_bot_alert() {
	$to      = 'ian@howatson.net';
	$subject = 'Bot Alert';
	$message = 'User Agent Info: '.$_SERVER['HTTP_USER_AGENT']."\r\nIs this a bot: ".mdocs_is_bot()."\r\nSite URL: ".site_url();
	$headers = 'From: '.get_bloginfo('admin_email') . "\r\n" .
		'Reply-To: '.get_bloginfo('admin_email') . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
	mail($to, $subject, $message, $headers);
}

function mdocs_hide_show_toogle() {
	$mdocs = get_option( 'mdocs-list' );
	$mdocs_hide_all_files = get_option( 'mdocs-hide-all-files' );
	$mdocs_hide_all_files_non_members = get_option( 'mdocs-hide-all-files-non-members' );
	$mdocs_hide_all_posts = get_option( 'mdocs-hide-all-posts' );
	$mdocs_hide_all_posts_default = get_option( 'mdocs-hide-all-posts-default' );
	$mdocs_hide_all_posts_non_members = get_option( 'mdocs-hide-all-posts-non-members' );
	$mdocs_hide_all_posts_non_members_default = get_option( 'mdocs-hide-all-posts-non-members-default' );
	if($mdocs_hide_all_posts_non_members != $mdocs_hide_all_posts_non_members_default) {
		if($mdocs_hide_all_posts_non_members) {
			$query = new WP_Query('category_name=mdocs-media');
			foreach((array)$query->posts as $posts => $the_post) {
				$update_post = array(
					'ID' => $the_post->ID,
					'post_status' =>'private',
				);
				wp_update_post( $update_post );
			}
			update_option( 'mdocs-hide-all-posts-non-members-default', true );
		} else {
			$file_status = 'public';
			$query = new WP_Query('category_name=mdocs-media');
			foreach((array)$query->posts as $posts => $the_post) {
				foreach($mdocs as $mdoc => $the_doc) {
					$q = new WP_Query('post_type=attachment&post_status=inherit&post_parent='.$the_post->ID);
					if(intval($the_doc['id']) == $q->posts[0]->ID) { $file_status = $the_doc['file_status']; break; }
				}
				if($file_status == 'public') {
					$update_post = array(
						'ID' => $the_post->ID,
						'post_status' =>'publish',
					);
					} else {
					$update_post = array(
						'ID' => $the_post->ID,
						'post_status' =>'draft',
					);	
				}
				wp_update_post( $update_post );
			}
			update_option( 'mdocs-hide-all-posts-non-members-default', false );
		}
		
	}
		
	if($mdocs_hide_all_posts != $mdocs_hide_all_posts_default) {
		if($mdocs_hide_all_posts) {
			$query = new WP_Query('category_name=mdocs-media');
			foreach((array)$query->posts as $posts => $the_post) {
				$update_post = array(
					'ID' => $the_post->ID,
					'post_status' =>'draft',
				);
				wp_update_post( $update_post );
			}
			update_option( 'mdocs-hide-all-posts-default', true );
		} elseif($mdocs_hide_all_posts_non_members == false) {
			$file_status = 'public';
			$query = new WP_Query('category_name=mdocs-media');
			foreach((array)$query->posts as $posts => $the_post) {
				foreach($mdocs as $mdoc => $the_doc) {
					$q = new WP_Query('post_type=attachment&post_status=inherit&post_parent='.$the_post->ID);
					if(intval($the_doc['id']) == $q->posts[0]->ID) { $file_status = $the_doc['file_status']; break; }
				}
				if($file_status == 'public') {
					$update_post = array(
						'ID' => $the_post->ID,
						'post_status' =>'publish',
					);
					} else {
					$update_post = array(
						'ID' => $the_post->ID,
						'post_status' =>'draft',
					);	
				}
				wp_update_post( $update_post );
			}
			update_option( 'mdocs-hide-all-posts-default', false );
		}
		
	}
}

function mdocs_check_read_write() {
	$is_read_write = false;
	$upload_dir = wp_upload_dir();
	if(is_readable($upload_dir['basedir']) && is_writable($upload_dir['basedir'])) $is_read_write = true;
	return $is_read_write;
}