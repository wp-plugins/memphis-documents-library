<?php
$the_rating = array();
function mdocs_edit_file($the_mdocs, $index, $current_cat) {
	?>
	<div class="mdocs-edit-file">
		<span class="update" id="<?php echo $index ?>">
			<i class="fa fa-pencil"></i> <a href="<?php echo 'admin.php?page=memphis-documents.php&mdocs-cat='.$current_cat.'&action=update-doc&mdocs-index='.$index; ?>" title="Update this file" class="edit"><?php _e('Update','mdocs'); ?></a> |
		</span>
		<span class='delete'>
			<i class="fa fa-remove"></i> <a class='submitdelete' onclick="return showNotice.warn();" href="<?php echo 'admin.php?mdocs-nonce='.$_SESSION['mdocs-nonce'].'&page=memphis-documents.php&mdocs-cat='.$current_cat.'&action=delete-doc&mdocs-index='.$index; ?>"><?php _e('Delete','mdocs'); ?></a> |
		</span>
		<span class="versions">
			<i class="icon-off"></i> <a href="<?php echo 'admin.php?page=memphis-documents.php&mdocs-cat='.$current_cat.'&mdocs-index='.$index; ?>&action=mdocs-versions" title="<?php _e('Versions','mdocs'); ?>" class="edit"><?php _e('Versions','mdocs'); ?></a></span>
	</div>
	<?php
}

function  mdocs_des_preview_tabs($the_mdoc) {
	$mdocs_desc = apply_filters('the_content', $the_mdoc['desc']);
	$mdocs_desc = str_replace('\\','',$mdocs_desc);
	$mdocs_default_content = get_option('mdocs-default-content');
	$mdocs_show_description = get_option('mdocs-show-description');
	$mdocs_show_preview = get_option('mdocs-show-preview');
	$mdocs_hide_all_files = get_option( 'mdocs-hide-all-files' );
	$mdocs_hide_all_files_non_members = get_option( 'mdocs-hide-all-files-non-members' );
	$upload_dir = wp_upload_dir();
	ob_start();
	?>
	<?php if($mdocs_show_description && $mdocs_show_preview) { ?><a class="mdocs-nav-tab <?php if($mdocs_default_content=='description') echo 'mdocs-nav-tab-active'; ?>" data-mdocs-show-type="desc" data-mdocs-id="<?php echo $the_mdoc['id']; ?>"><?php _e('Description', 'mdocs'); ?></a><?php } ?>
	<?php if($mdocs_show_preview && $mdocs_show_description) { ?><a class="mdocs-nav-tab <?php if($mdocs_default_content=='preview') echo 'mdocs-nav-tab-active'; ?>"  data-mdocs-show-type="preview" data-mdocs-id="<?php echo $the_mdoc['id']; ?>"><?php _e('Preview', 'mdocs'); ?></a><?php } ?>
	<div class="mdocs-show-container" id="mdocs-show-container-<?php echo $the_mdoc['id']; ?>">
		<?php
		if(!isset($_POST['show_type']) && $mdocs_show_description && $mdocs_default_content == 'description') {
			?>
			<div class="mdoc-desc">
				<?php mdocs_show_description($the_mdoc['id']); ?>
			</div>
			<?php
		} elseif(!isset($_POST['show_type']) && $mdocs_show_preview && $mdocs_default_content == 'preview') {
			if($mdocs_hide_all_files || $the_mdoc['file_status'] == 'hidden') {
				 echo '<div class="alert alert-warning" role="alert">'.__('Preview is unavailable for this file.','mdocs').'</div class="alert alert-warning" role="alert">';
			} else if( is_user_logged_in() == false && $mdocs_hide_all_files_non_members) {
				echo '<div class="alert alert-warning" role="alert">'.__('Please login to view this file preview.','mdocs').'</div>';
			} else {
				$show_preview = mdocs_file_access($the_mdoc);
				if( $show_preview ) {
					$is_image = getimagesize($upload_dir['basedir'].MDOCS_DIR.$the_mdoc['filename']);
				?>
				<div class="mdoc-desc">
				<?php if($is_image == false) { ?>
				<p><?php mdocs_doc_preview($the_mdoc); ?></p>
				<?php
				} else mdocs_show_image_preview($the_mdoc); ?>
				</div>
				<?php
				} else { echo '<p>'.__('Please login to access the preview.','mdocs').'</p>'; }
			}
		}  ?>
	</div>
	<?php
	$the_des = ob_get_clean();
	return $the_des;
}


function mdocs_post_page($att=null) {
	global $post;
	if($post->post_type = 'mdocs-posts') {
		$mdocs = get_option('mdocs-list');
		foreach($mdocs as $index => $value) {
			if($value['parent'] == $post->ID) { $the_mdoc = $value; break; }
		}
		
		$query = new WP_Query('post_type=attachment&post_status=inherit&post_parent='.$post->ID);
		
		$user_info = get_userdata($post->post_author);
		$mdocs_file = $query->post;
		$upload_dir = wp_upload_dir();
		$file = substr(strrchr($mdocs_file->post_excerpt, '/'), 1 );
		//$filesize = filesize($upload_dir['basedir'].'/mdocs/'.$file);
		$query = new WP_Query('pagename=mdocuments-library');	
		$permalink = get_permalink($query->post->ID);
		if( strrchr($permalink, '?page_id=')) $mdocs_link = site_url().'/'.strrchr($permalink, '?page_id=');
		else $mdocs_link = site_url().'/'.$query->post->post_name.'/';
		$mdocs_desc = apply_filters('the_content', $post->post_excerpt);
		ob_start();
		$the_page = '<div class="mdocs-post mdocs-post-current-file">';
		$the_page .= mdocs_file_info_large($the_mdoc, 'site', $index, null);
		$the_page .= '<div class="mdocs-clear-both"></div>';
		$the_page .= mdocs_social($the_mdoc);
		$the_page .= '</div>';
		$the_page .= '<div class="mdocs-clear-both"></div>';
		$the_page .= mdocs_des_preview_tabs($the_mdoc);
		$the_page .= '<div class="mdocs-clear-both"></div>';
		$the_page .= '</div>';
		$the_page .= ob_get_clean();
		//var_dump(is_user_logged_in());
		//var_dump(get_option('mdocs-hide-all-posts-non-members'));
		if(get_option('mdocs-hide-all-posts') == false && get_option('mdocs-hide-all-posts-non-members') == false) return $the_page;
		elseif(is_user_logged_in() && get_option('mdocs-hide-all-posts-non-members')) return $the_page;
		elseif(is_user_logged_in() == false && get_option('mdocs-hide-all-posts-non-members')) return 'You must be logged in to see this page.';
		elseif(get_option('mdocs-hide-all-posts')) return 'Sorry you can\'t see the page.';
		   
		   
		   //|| is_user_logged_in() == false && get_option('mdocs-hide-all-posts-non-members') == '1') return $the_page;
	} else {
		print nl2br(get_the_content('Continue Reading &rarr;'));
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
	if(!isset($_POST['mdocs-name']) || $_POST['mdocs-name'] == '') $upload['name'] = $name;
	else $upload['name'] = $_POST['mdocs-name'];
	return $upload;
}

function mdocs_process_file($file, $import=false) {
	global $current_user;
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	if(isset($_POST['mdocs-type'])) $mdocs_type = $_POST['mdocs-type'];
	else $mdocs_type = null;
	if(!isset($_POST['mdocs-desc'])) $desc = MDOCS_DEFAULT_DESC;
	else $desc = $_POST['mdocs-desc'];
	$post_status = $file['post-status'];
	if($import) $desc = $file['desc'];
	$upload_dir = wp_upload_dir();
	if(isset($file['modifed'])) $modifed_date = date('Y-m-d H:i:s', $file['modifed']+MDOCS_TIME_OFFSET);
	else $modifed_date = MDOCS_CURRENT_TIME;
	if($import == false) {
		$upload['url'] = $upload_dir['baseurl'].'/mdocs/'.$file['name'];
		$upload['file'] = $upload_dir['basedir'].'/mdocs/'.$file['name'];
		$upload['filename'] = $file['name'];
		if(file_exists($upload_dir['basedir'].'/mdocs/'.$file['name'])) $upload = mdocs_rename_file($upload, $file['name']);
		else {
			$name = substr($file['name'], 0, strrpos($file['name'], '.') );
			if(!isset($_POST['mdocs-name']) || $_POST['mdocs-name'] == '') $upload['name'] = $name;
			else $upload['name'] = $_POST['mdocs-name'];
		}
		$result = move_uploaded_file($file['tmp_name'], $upload['file']);
		if($result == false) rename($file['tmp_name'], $upload['file']);
		//chmod($upload['file'], 0600);
	} else {
		$upload['url'] = $upload_dir['baseurl'].'/mdocs/'.$file['filename'];
		$upload['file'] = $upload_dir['basedir'].'/mdocs/'.$file['filename'];
		$upload['filename'] = $file['filename'];
		$upload['name'] = $file['name'];
	}
	$wp_filetype = wp_check_filetype($upload['file'], null );
	if($mdocs_type == 'mdocs-add' || $import == true) {
		$mdocs_post = array(
			'post_title' => $upload['name'],
			'post_status' => $post_status,
			'post_content' => '[mdocs_post_page new=true]',
			'post_author' => $current_user->ID,
			'post_excerpt' => $desc,
			'post_date' => $modifed_date,
			'post_type' => 'mdocs-posts',
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
		wp_set_post_tags( $mdocs_post_id, $upload['name'].', '.$file['cat'].', memphis documents library, '.$wp_filetype['type'] );
	} elseif($mdocs_type == 'mdocs-update') {
		$mdocs_post = array(
			'ID' => $file['parent'],
			'post_title' => $upload['name'],
			'post_status' =>$post_status,
			'post_content' => '[mdocs_post_page]',
			'post_author' => $current_user->ID,
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
		wp_set_post_tags( $mdocs_post_id, $upload['name'].', '.$file['cat'].', memphis documents library, '.$wp_filetype['type'] );
	}
	$upload['desc'] = $desc;
	return $upload;
}

function mdocs_nonce() {
	session_start();
	if(isset($_SESSION['mdocs-nonce'])) define('MDOCS_NONCE',$_SESSION['mdocs-nonce']);
	if(!isset($_SESSION['mdocs-nonce']) || isset($_REQUEST['mdocs-nonce'])) $_SESSION['mdocs-nonce'] = md5(rand(0,1000000));
	session_write_close();	
}
/* SCHEDULED FOR REMOVAL
function mdocs_sort_by($mdocs, $ypos=0, $page_type='site', $echo=true) {
	$mdocs_sort_type = get_option('mdocs-sort-type');
	$mdocs_sort_style = get_option('mdocs-sort-style');
	if(isset($_POST['mdocs-sort-type'])) $sort_type = $_POST['mdocs-sort-type']; 
	elseif(isset($_COOKIE['mdocs-sort-type-'.$page_type])) $sort_type = $_COOKIE['mdocs-sort-type-'.$page_type];
	else $sort_type = $mdocs_sort_type;
	if(isset($_POST['mdocs-sort-range'])) $sort_range = $_POST['mdocs-sort-range'];
	elseif(isset($_COOKIE['mdocs-sort-range-'.$page_type])) $sort_range = $_COOKIE['mdocs-sort-range-'.$page_type];
	else $sort_range = $mdocs_sort_style;
	if(isset($_POST['mdocs-list-type'])) update_option('mdocs-list-type-dashboard',$_POST['mdocs-list-type']);
	$list_type = get_option('mdocs-list-type-dashboard');
	if($echo) {
?>
<div class="mdocs-sort" style="top:<?php echo $ypos; ?>px">
	<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['REQUEST_URI']; if($page_type=='dashboard') echo '&mdocs-sort=true'; ?>">
		<?php if($page_type == 'dashboard') { ?>
		<i class="fa fa-cogs mdocs-orange"></i>
		<label><?php _e('List Size','mdocs'); ?>:</label>
		<input type="radio" name="mdocs-list-type" value="large" <?php if($list_type == 'large') echo 'checked'; ?> /> <label><?php _e('Large'); ?></label>
		<input type="radio" name="mdocs-list-type" value="small" <?php if($list_type == 'small') echo 'checked'; ?>/> <label><?php _e('Small'); ?></label>
		<?php } ?>
		
		<i class="fa fa-cogs mdocs-green"></i>
		<input type="radio" name="mdocs-sort-range" value="desc" <?php if($sort_range == 'desc') echo 'checked'; ?>/> <label><?php _e('Descending'); ?></label>
		<input type="radio" name="mdocs-sort-range" value="asc" <?php if($sort_range == 'asc') echo 'checked'; ?> /> <label><?php _e('Ascending'); ?></label>
		
		
		<i class="fa fa-cogs mdocs-blue"></i><label> <?php _e('Sort By'); ?>:</label>
		<select name="mdocs-sort-type">
			<option value="name" <?php if($sort_type == 'name') echo 'selected'; ?>><?php _e('File Name'); ?></option>
			<option value="downloads" <?php if($sort_type == 'downloads') echo 'selected'; ?>><?php _e('Number of Downloads'); ?></option>
			<option value="version" <?php if($sort_type == 'version') echo 'selected'; ?>><?php _e('Version'); ?></option>
			<option value="owner" <?php if($sort_type == 'owner') echo 'selected'; ?>><?php _e('Author'); ?></option>
			<option value="modified" <?php if($sort_type == 'modified') echo 'selected'; ?>><?php _e('Last Updated'); ?></option>
			<option value="rating" <?php if($sort_type == 'rating') echo 'selected'; ?>><?php _e('Rating'); ?></option>
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
*/
function mdocs_array_sort($the_array=null, $orderby=null, $sort_types=null) {
	if($the_array == null) $the_array = get_option('mdocs-list');
	if($orderby == null) $orderby = get_option('mdocs-sort-type');
	if($sort_types == null) {
		$sort_types = get_option('mdocs-sort-style');
		if(isset($_COOKIE['mdocs-sort-type'])) $orderby = $_COOKIE['mdocs-sort-type'];
		if(isset($_COOKIE['mdocs-sort-range'])) $sort_types = $_COOKIE['mdocs-sort-range'];
	}
	if($sort_types == 'desc') $sort_types = SORT_DESC;
	if($sort_types == 'asc') $sort_types = SORT_ASC;
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
		if(is_numeric($array_lowercase[0])) $sort_var_type = SORT_NUMERIC;
		else $sort_var_type = SORT_STRING;
		array_multisort($array_lowercase, $sort_types, $sort_var_type,$the_array);
		$the_array = array_values($the_array);
		return $the_array;
	} else return array();
}

function mdocs_export_file_status() {
	$upload_dir = wp_upload_dir();
	$mdocs_zip = get_option('mdocs-zip');
	if(file_exists($upload_dir['basedir'].'/mdocs/'.$mdocs_zip)) {
		mdocs_errors(MDOCS_ZIP_STATUS_OK);
	} else mdocs_errors(MDOCS_ZIP_STATUS_FAIL,'error');
}


function mdocs_errors($error, $type='updated') {
	if($type == 'error') $error = '<b>'.__('Memphis Error','mdocs').': </b>'.$error;
	else $error = '<b>'.__('Memphis Info','mdocs').': </b>'.$error;
	?>
	<div class="<?php echo $type; ?>" style="clear:both;">
		<div id="mdocs-error">
		<p><?php _e($error); ?></p>
		</div>
	</div>
    <?php
}

function mdocs_get_rating($the_mdoc) {
	global $current_user;
	$avg = 0;
	$the_rating = array('total'=>0,'your_rating'=>0);
	if(is_array($the_mdoc['ratings']) && count($the_mdoc['ratings']) > 0 ) {
		foreach($the_mdoc['ratings'] as $index => $average) {
			$avg += $average;
			$the_rating['total']++;
			if($current_user->user_email == $index) $the_rating['your_rating'] = floatval($average);
		}
		$the_rating['average'] =  floatval(number_format($avg/$the_rating['total'],1));
		return $the_rating;
	} else {
		$the_rating['total'] = 0;
		$the_rating['average'] = '-';
		return $the_rating;
	}
	
}

function is_modcs_google_doc_viewer() {
	if(stripos($_SERVER['HTTP_USER_AGENT'], 'AppsViewer; http://drive.google.com' )) return true;
	else return false;
}

function mdocs_is_bot() {
	$upload_dir = wp_upload_dir();
	$bots = strip_tags(file_get_contents(MDOCS_ROBOTS));
	$bots = explode('|:::|',$bots);
	foreach($bots as $bot) {
        if ( stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false && $bot != 'via docs.google.com') {
			return true;
		}
    }
	return false;
}

function mdocs_send_bot_alert($url='', $is_bot=false) {
	include_once 'wp-admin/includes/plugin.php';
	$plugin_data = get_plugin_data( dirname(__FILE__).'/memphis-documents.php');
	if($is_bot === true) $bot = "<span style='color: red;'>This is a know bot</span>";
	else $bot = "<span style='color: green;'>This is either a legitimate download or a unknown bot.</span>";
	$to      = 'ian@howatson.net';
	$subject = 'Bot Alert';
	$message = "<h4>This is a debug message for Memphis Documents Library it is used to track bots.  Only user agent information and site url are being tracked.</h4>";
	$message .= '<h3>User Agent Info</h3><b>'.preg_replace('/\)/',')<br>',$_SERVER['HTTP_USER_AGENT'])."</b>";
	$message .= "<h4>Site URL: ". get_permalink( $url ) ."</h4>";
	$message .= "<h4>Memphis Version: ".$plugin_data['Version']."</h4>";
	$message .= "<h3>Bot analyst: ".$bot."</h3>";
	$headers = 'From: '.get_bloginfo('admin_email') . "\r\n";
	$headers .= 'Reply-To: '.get_bloginfo('admin_email') . "\r\n";
	$headers .= 'X-Mailer: PHP/' . phpversion()."\r\n";
	$headers .= "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1";
	return mail($to, $subject, $message, $headers);
}

function mdocs_hide_show_toogle() {
	$mdocs = get_option( 'mdocs-list' );
	$mdocs_hide_all_files = get_option( 'mdocs-hide-all-files' );
	$mdocs_hide_all_files_non_members = get_option( 'mdocs-hide-all-files-non-members' );
	/*
	$mdocs_hide_all_posts = get_option( 'mdocs-hide-all-posts' );
	$mdocs_hide_all_posts_default = get_option( 'mdocs-hide-all-posts-default' );
	$mdocs_hide_all_posts_non_members = get_option( 'mdocs-hide-all-posts-non-members' );
	$mdocs_hide_all_posts_non_members_default = get_option( 'mdocs-hide-all-posts-non-members-default' );
	
	if($mdocs_hide_all_posts_non_members != $mdocs_hide_all_posts_non_members_default) {
		if($mdocs_hide_all_posts_non_members) {
			$query = new WP_Query('post_type=mdocs-posts&posts_per_page=-1');
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
			$query = new WP_Query('post_type=mdocs-posts&posts_per_page=-1');
			foreach((array)$query->posts as $posts => $the_post) {
				foreach($mdocs as $mdoc => $the_doc) {
					$q = new WP_Query('post_type=attachment&post_status=inherit&post_parent='.$the_post->ID);
					if(intval($the_doc['id']) == $q->posts[0]->ID) { $file_status = $the_doc['file_status']; break; }
				}
				if($file_status == 'public' ) {
					$update_post = array(
						'ID' => $the_post->ID,
						'post_status' =>'publish',
					);
					$post_status = 'publish';
				} else {
					$update_post = array(
						'ID' => $the_post->ID,
						'post_status' =>'draft',
					);
					$post_status = 'draft';
				}
				wp_update_post( $update_post );
				$mdocs[$mdoc]['post_status'] = $post_status;
			}
			mdocs_save_list($mdocs);
			update_option( 'mdocs-hide-all-posts-non-members-default', false );
		}
	}
		
	if($mdocs_hide_all_posts != $mdocs_hide_all_posts_default) {
		if($mdocs_hide_all_posts) {
			$query = new WP_Query('post_type=mdocs-posts&posts_per_page=-1');
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
			$query = new WP_Query('post_type=mdocs-posts&posts_per_page=-1');
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
	*/
}

function mdocs_check_read_write() {
	$is_read_write = false;
	$upload_dir = wp_upload_dir();
	if(is_readable($upload_dir['basedir']) && is_writable($upload_dir['basedir'])) $is_read_write = true;
	return $is_read_write;
}

function mdocs_doc_preview($file) {
	$boxview = new mdocs_box_view();
	$view_file = $boxview->downloadFile($file['box-view-id']);
	if(isset($view_file) && $view_file['type'] != 'error') { ?>
	<script>
		
		var screenHeight = window.innerHeight-275;
		
		jQuery('#mdocs-box-view-iframe').css({'height': screenHeight})
	</script>
	<iframe id="mdocs-box-view-iframe" src="//view-api.box.com/1/sessions/<?php echo $view_file['id']; ?>/view?theme=dark" seamless fullscreen style="width: 100%; "></iframe>
	<?php } else { ?>
	<div class="alert alert-warning" role="alert"><?php echo $view_file['details'][0]['message']; ?></div>
	<?php
	}
}

function mdocs_file_access($the_mdoc) {
	$access = false;
	$mdocs_hide_all_files_non_members = get_option( 'mdocs-hide-all-files-non-members' );
	$mdocs_hide_all_files = get_option( 'mdocs-hide-all-files' );
	$mdocs_hide_all_files_non_members = get_option( 'mdocs-hide-all-files-non-members' );
	$mdocs_show_non_members = $the_mdoc['non_members'];
	$user_logged_in = is_user_logged_in();
	if($mdocs_hide_all_files_non_members && $user_logged_in == false) $access = false;
	elseif($user_logged_in && $the_mdoc['non_members'] == '') $access = true;
	elseif($mdocs_show_non_members == false) $access = false;
	elseif($mdocs_hide_all_files == false ) $access = true;
	else $access = false;
	return $access;
}

function mdocs_get_cats($cats, $current_cat, $depth=0, $echo=true) {
	$nbsp = '';
	for($i=0;$i < $depth;$i++) $nbsp .= '&nbsp;&nbsp;';
	foreach( $cats as $index => $cat ){
		if($current_cat === $cat['slug']) $is_selected = 'selected="selected"';
		else $is_selected = '';
		//$is_selected = ( $cat['slug'] == $current_cat ) ? 'selected="selected"' : '';
		if($echo) echo '<option  value="'.$cat['slug'].'" '.$is_selected.'>'.$nbsp.$cat['name'].'</option>';
		if(count($cat['children']) > 0) { 
			mdocs_get_cats($cat['children'], $current_cat ,$cat['depth']+1);
		}
	}
}

$parent_cat_array = array();
$current_cat_array = array();
$found_current_cat = false;
function mdocs_get_children_cats($cats, $current_cat) {
	global $current_cat_array, $parent_cat_array, $found_current_cat;
	if($found_current_cat == false) {
		foreach( $cats as $index => $cat ){
			if($cat['slug'] == $current_cat) {
				$current_cat_array = $cat;
				$found_current_cat = true;
			}
			if(count($cat['children']) > 0 && $found_current_cat == false) {
				$parent_cat_array = $cat;
				mdocs_get_children_cats($cat['children'], $current_cat);
			} 
		}
	}
}

function mdocs_update_num_cats($increase) {	update_option('mdocs-num-cats',intval(get_option('mdocs-num-cats')+$increase)); }
function mdocs_get_subcats($current, $parent, $has_children=true) {
	global $post;
	if(!is_admin()) {
		$permalink = get_permalink($post->ID);
		if(preg_match('/\?page_id=/',$permalink) || preg_match('/\?p=/',$permalink)) {
			$permalink = $permalink.'&mdocs-cat=';
		} else $permalink = $permalink.'?mdocs-cat=';
	} else $permalink = 'admin.php?page=memphis-documents.php&mdocs-cat=';
	$num_cols = 1;
	if(get_option('mdocs-show-downloads') == '1' || get_option('mdocs-show-downloads')) $num_cols++;
	if(get_option('mdocs-show-author') == '1' || get_option('mdocs-show-author')) $num_cols++;
	if(get_option('mdocs-show-version') == '1' || get_option('mdocs-show-version')) $num_cols++;
	if(get_option('mdocs-show-update') == '1' || get_option('mdocs-show-update')) $num_cols++;
	if(get_option('mdocs-show-ratings') == '1' || get_option('mdocs-show-ratings')) $num_cols++;
	if(get_option('mdocs-list-type') == 'large') echo '<table class="mdocs-list-table">';
	if(isset($current['parent']) && $current['parent'] != '') {
	?>
	<tr class="mdocs-parent-cat" >
		<td colspan="<?php echo $num_cols; ?>" id="title" class="mdocs-tooltip">
			<a href="<?php echo $permalink.$parent['slug']; ?>" alt="<?php echo $permalink.$parent['slug']; ?>">
				<i class="fa fa-reply"></i> <?php echo $parent['name']; ?>
				
			</a>
		</td>
	</tr>
	<?php
	} 
	if($has_children == true) {
		foreach($current['children'] as $index => $child) {
			?>
			<tr class="mdocs-sub-cats" >
				<td colspan="<?php echo $num_cols; ?>" id="title" class="mdocs-tooltip">
					<a href="<?php echo $permalink.$child['slug']; ?>" alt="<?php echo $child['name']; ?>"><i class="fa fa-folder-o"></i> <?php echo $child['name']; ?>
					
				</td>
			</tr>
			<?php
		}
	}
	?>
	<tr class="mdocs-current-cat" >
		<td colspan="<?php echo $num_cols; ?>" id="title" class="mdocs-tooltip">
			<p><i class="fa fa fa-folder-open-o"></i> <?php echo $current['name']; ?></p>
			
		</td>
	</tr>
	<?php
	if(get_option('mdocs-list-type') == 'large') echo '</table>';
	return $num_cols;
}

function mdocs_custom_mime_types($existing_mimes=array()) {
	// Add file extension 'extension' with mime type 'mime/type'
	$mdocs_allowed_mime_types = get_option('mdocs-allowed-mime-types');
	foreach($mdocs_allowed_mime_types as $index => $mime) {
		$existing_mimes[$index] = $mime;
	}
	$mdocs_removed_mime_types = get_option('mdocs-removed-mime-types');
	foreach($mdocs_removed_mime_types as $index => $mime) {
		unset($existing_mimes[$mime]);
	}
	return $existing_mimes;
}

function mdocs_list_header($show=true) {
	if($show) {
		global $post, $current_cat_array, $parent_cat_array;
		$mdocs = get_option('mdocs-list');
		$num_docs = count($mdocs);
		$cats = get_option('mdocs-cats');
		$upload_dir = wp_upload_dir();
		$message = '';
		$current_cat = mdocs_get_current_cat();
		mdocs_get_children_cats(get_option('mdocs-cats'),$current_cat);
		if($post == null) $is_admin = true;
		else {
			$is_admin = false;
			$permalink = get_permalink($post->ID);
			if(preg_match('/\?page_id=/',$permalink) || preg_match('/\?p=/',$permalink)) {
				$permalink = $permalink.'&mdocs-cat=';
			} else $permalink = $permalink.'?mdocs-cat=';
		}
		?>
		<?php mdocs_load_modals(); ?>
		<div class="wrap">
			
			<div class="mdocs-admin-preview"></div>
			<?php if($message != "" && $type != 'update') { ?> <div id="message" class="error" ><p><?php _e($message); ?></p></div> <?php }?>
			<?php if(is_admin()) { ?>
			<h2 class="mdocs-h2 pull-left"><?php _e("Documents Library",'mdocs'); ?></h2>
			
			<div class="btn-group">
				<a class="add-update-btn btn btn-danger btn-sm" data-toggle="modal" data-target="#mdocs-add-update" data-mdocs-id="" data-is-admin="<?php echo is_admin(); ?>" data-action-type="add-doc"  data-current-cat="<?php echo $current_cat; ?>" href=""><?php _e('Add New Document','mdocs'); ?> <i class="fa fa-upload fa-lg"></i></a>
			</div>
			<div class="btn-group">
				<button class="btn btn-default dropdown-toggle btn-sm" type="button" id="dropdownMenu1" data-toggle="dropdown"><?php _e('Options','mdocs'); ?><span class="caret"></span></button>
				<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
					 <li role="presentation" class="dropdown-header"><?php _e('File Options','mdocs'); ?></li>
				  <li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=cats"><?php _e('Edit Folders','mdocs'); ?></a></li>
				  <li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=allowed-file-types"><?php _e('Allowed File Types','mdocs'); ?></a></li>
				  <li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=import"><?php _e('Import','mdocs'); ?></a></li>
				  <li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=export"><?php _e('Export','mdocs'); ?></a></li>
				  <li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=batch"><?php _e('Batch Upload'); ?></a></li>
				  <li role="presentation" class="divider"></li>
				  <li role="presentation" class="dropdown-header"><?php _e('Admin Options','mdocs'); ?></li>
				  <?php
				  if(get_option('mdocs-box-view-updated') == false) {
					?>
					<li role="presentation"><a style="color: #b04a48 !important;" role="menuitem" tabindex="-1" href="#" id="mdosc-3-0-patch-btn" data-num-docs="<?php echo $num_docs; ?>"><?php _e('Run 3.0 Patch','mdocs'); ?></a></li>
					<?php } ?>
				  <li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=settings"><?php _e('Settings','mdocs'); ?></a></li>
				  <li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=filesystem-cleanup"><?php _e('File System Cleanup','mdocs'); ?></a></li>
				   <li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=restore"><?php _e('Restore To Default','mdocs'); ?></a></li>
					<li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=short-codes"><?php _e('Short Codes','mdocs'); ?></a></li>
				</ul>
			</div>
			<br><br>
			<?php } ?>
			
			<nav class="navbar navbar-default" role="navigation" id="mdocs-navbar">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#mdocs-navbar-collapse">
						  <span class="sr-only">Toggle navigation</span>
						  <span class="icon-bar"></span>
						  <span class="icon-bar"></span>
						  <span class="icon-bar"></span>
						</button>
						<span class="navbar-brand" href="#"><?php _e('Folders','mdocs'); ?></span>
					</div>
					<div class="collapse navbar-collapse" id="mdocs-navbar-collapse">
						<ul class="nav navbar-nav">
							<?php
							if(!empty($cats)) {
								foreach( $cats as $index => $cat ){
									if(isset($cat['slug']) && !empty($current_cat_array)) {
										if( $cat['slug'] == $current_cat) $class = ' active';
										elseif(isset($cats[$current_cat_array['base_parent']]['slug']) && $cats[$current_cat_array['base_parent']]['slug'] == $cat['slug']) $class = ' active';
										else $class = '';
									} else $class = '';
									if(is_dir($upload_dir['basedir'].'/mdocs/')) {
										if($is_admin) echo '<li class="'.$class.'"><a href="?page=memphis-documents.php&mdocs-cat='.$cat['slug'].' ">'.__($cat['name']).'</a></li>';
										else echo '<li class="'.$class.'"><a href="'.$permalink.$cat['slug'].'">'.__($cat['name']).'</a></li>';
									}
								}
							}
							?>
						</ul>
					</div>
				</div>
			</nav>
		<?php
		
	} else {
		mdocs_load_modals(); 
		echo '<div class="wrap">';
	}
	return $current_cat;
}

function mdocs_recursive_search($array, $search_string='') {
    if ($array) {
        foreach ($array as $index => $value) {
            if (is_array($value)) {
                $result = mdocs_recursive_search($value, $search_string);
				if($result != null) return $result;
            } else {
				if($search_string === $value) return $array;
            }
        }
    }
}

function mdocs_get_current_cat($atts=null) {
	$cats =  get_option('mdocs-cats');
	$current_cat = '';
	if(isset($_GET['mdocs-cat'])) $current_cat = $_GET['mdocs-cat'];
	elseif(!is_string($cats) && !isset($atts['cat'])) $current_cat = $cats[0]['slug'];
	elseif(isset($atts['cat'])) {
		$cat = mdocs_recursive_search($cats, $atts['cat']);
		$current_cat = $cat['slug'];
	}
	return $current_cat;
}

// GET ALL MDOCS POST AND DISPLAYS THEM ON THE MAIN PAGE.
add_filter( 'pre_get_posts', 'mdocs_get_posts' );
function mdocs_get_posts( $query ) {
	if ( is_home() && $query->is_main_query() ||  $query->is_search == false && !is_admin() && has_shortcode( $post->post_content, 'mdocs' )) {
		if(get_option('mdocs-hide-all-posts') == false && get_option('mdocs-hide-all-posts-non-members') == false) $query->set( 'post_type', array( 'post', 'mdocs-posts' ) );
		elseif(is_user_logged_in() && get_option('mdocs-hide-all-posts-non-members') == true) $query->set( 'post_type', array( 'post', 'mdocs-posts' ) );
	}
}
// CREATES THE CUSTOM POST TYPE mDocs Posts which handles all the Memphis Document Libaray posts.
function mdocs_post_pages() {
	$labels = array(
		'name'               => __( 'Memphis Documents Posts', 'mdocs' ),
		'singular_name'      => _x( 'mdocs', 'mdocs' ),
		'add_new'            => __( 'Add New', 'mdocs' ),
		'add_new_item'       => __( 'Add New Documents', 'mdocs' ),
		'edit_item'          => __( 'Edit Documents', 'mdocs' ),
		'new_item'           => __( 'New Documents', 'mdocs' ),
		'all_items'          => __( 'All Documents', 'mdocs' ),
		'view_item'          => __( 'View Documents', 'mdocs' ),
		'search_items'       => __( 'Search Documents', 'mdocs' ),
		'not_found'          => __( 'No documents found', 'mdocs' ),
		'not_found_in_trash' => __( 'No documents found in the Trash', 'mdocs' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'mDocs Posts'
	);
	$supports = array( 'title', 'editor','author','comments','revisions','page-attributes','post-formats'  );
	$args = array(
		'labels'              		=> $labels,
		'public'              		=> true,
		'publicly_queryable'  => true,
		'show_ui'             	=> true,
		'show_in_menu' 		=> true,
		'query_var'           	=> true,
		'rewrite'             		=> array( 'slug' => 'mdocs-posts' ),
		'capability_type'     	=> 'post',
		'has_archive'         	=> true,
		'hierarchical'        	=> false,
		'menu_position'       => 5,
		'taxonomies' 			=> array('category','post_tag'),
		'supports'            		=> $supports,
	 );
	register_post_type( 'mdocs-posts', $args );
}
add_action( 'init', 'mdocs_post_pages' );

function mdocs_save_list($mdocs_list) {
	if($mdocs_list != null) update_option('mdocs-list', $mdocs_list);
	//else mdocs_errors(MDOCS_ERROR_7,'error'); 
}

function mdocs_nav_size($collapse) {
	if($collapse) {
?>
<style type="text/css" media="screen" id="mdocs-nav-collapse">
	@media (max-width: 10000px) {
		.navbar-header { float: none; }
		.navbar-toggle { display: block; }
		.navbar-collapse { border-top: 1px solid transparent; box-shadow: inset 0 1px 0 rgba(255,255,255,0.1); }
		.navbar-collapse.collapse { display: none!important; }
		.navbar-nav { float: none !important; margin: 7.5px -15px; }
		.navbar-nav>li { float: none; }
		.navbar-nav>li>a { padding-top: 10px; padding-bottom: 10px; }
		.navbar-collapse.collapse.in { display: block!important; }
		.collapsing { overflow: hidden!important; }
		#mdocs-navbar .navbar-collapse ul li { margin: 0; }
	}
</style>
<?php
	} else {
?>
<style type="text/css" media="screen" id="mdocs-nav-expand">
	#mdocs-navbar .navbar-toggle { display: none !important; }
	#mdocs-navbar .navbar-header { float: left; margin: 0;  }
	#mdocs-navbar .navbar-header .navbar-brand  { margin: 0; } 
	#mdocs-navbar .navbar-collapse { display: block; margin: 0px; border: none; }
	#mdocs-navbar .navbar-collapse ul, #mdocs-navbar .navbar-collapse ul li { float: left; height: 50px;}
	#mdocs-navbar .navbar-collapse ul li a { padding: 15px; }
</style>
<?php
	}
}
function mdocs_box_view_update_v3_0() {
	?>
	<style>
		body, html { overflow: hidden; }
		.bg-3-0 { width: 100%; height: 100%; background: #000; position: absolute; top: 0; left: 0; z-index: 9999; padding: 0; margin: 0;  opacity:  0.7;}
		.container-3-0 { position: absolute; top: 50px; z-index: 10000; width: 500px; background: #fff; margin-left: 50%; left: -250px; padding: 10px;}
		.container-3-0 h1 { color: #2ea2cc; }
		.container-3-0 h3 { color: red; }
		.btn-container-3-0 { text-align: center; }
		@media (max-width: 640px) {
			.container-3-0 { width: 360px; left: -180px; z-index: 10000; margin-left: 50%}
		}
	</style>
	<div class="bg-3-0"></div>
	<div class="container-3-0">
		<h1><?php _e('Memphis Documents Library', 'mdocs'); ?></h1>
		<h2><?php _e('Document Preview Updater', 'mdocs'); ?></h2>
		<p><?php _e('Version 3.0 of Memphis Documents Library now uses a new documents preview tool Called', 'mdocs'); ?> <a href="https://box-view.readme.io" target="_blank"><?php _e('Box View', 'mdocs'); ?></a>.</p>
		<p><?php _e('This process requires an update to your Memphis Documents Library, which will be adding information needed for', 'mdocs'); ?> <a href="https://box-view.readme.io" target="_blank"><?php _e('Box View', 'mdocs'); ?></a> <?php _e('to work properly.', 'mdocs'); ?></p>
		<h3><?php _e('Important, Please Read', 'mdocs'); ?></h3>
		<p><b><?php _e('The process depending on the size of your Library can take a long time, so make sure you have the time run this updater.', 'mdocs'); ?></b></p>
		<p><b><?php _e('If you choose not to run this updater now preview will not work.', 'mdocs'); ?></b></p>
		<p><b><?php _e('You may run this process anytime by going to the Settings menu and pressing "Run Box View Updater".', 'mdocs'); ?></b></p>
		<h3><?php _e('DO NOT LEAVE PAGE ONCE THIS UPDATER HAS STARTER!', 'mdocs'); ?></h3>
		<div class="btn-container-3-0">
			<button id="run-updater-3-0"><?php _e('Run Updater', 'mdocs'); ?></button>
			<button id="not-now-3-0"><?php _e('Not Right Now', 'mdocs'); ?></button>
		</div>
	</div>
	
	<?php
}
function mdocs_v3_0_patch_run_updater() {
	$mdocs = get_option('mdocs-list');
	$boxview = new mdocs_box_view();
	foreach($mdocs as $index => $the_mdoc) {
		if(!isset($the_mdoc['box-view-id'])) {
			$upload_file = $boxview->uploadFile(get_site_url().'/?mdocs-file='.$the_mdoc['id'].'&mdocs-url='.$the_mdoc['parent'].'&is-box-view=true', $the_mdoc['filename']);
			sleep(3);
			$the_mdoc['box-view-id'] = $upload_file['id'];
			$mdocs[$index] = $the_mdoc;
			update_option('mdocs-list', $mdocs);
		}
	}
	update_option('mdocs-v3-0-patch-var-1',true);
	update_option('mdocs-box-view-updated',true);
}
function mdocs_v3_0_patch_cancel_updater() {
	update_option('mdocs-v3-0-patch-var-1',true);
	update_option('mdocs-box-view-updated',false);
}
function mdocs_show_description($id) {
	$mdocs = get_option('mdocs-list');
	foreach($mdocs as $index => $the_mdoc) if($the_mdoc['id'] == $id) { break; }
	$mdocs_desc = apply_filters('the_content', $the_mdoc['desc']);
	$mdocs_desc = str_replace('\\','',$mdocs_desc);
	$boxview = new mdocs_box_view();
	$thumbnail = $boxview->getThumbnail($the_mdoc['box-view-id']);
	$json_thumbnail = json_decode($thumbnail,true);
	$image_size = getimagesize(get_site_url().'?mdocs-img-preview='.$the_mdoc['filename']);
	$thumbnail_size = 256;
	?>
	<h4><?php echo $the_mdoc['filename']; ?></h4>
	<?php
	if($json_thumbnail['type'] != 'error') {
	?>
	<div class="">
		<img class="mdocs-thumbnail pull-left img-thumbnail img-responsive" src="<?php $boxview->displayThumbnail($thumbnail); ?>" alt="<?php echo $the_mdoc['filename']; ?>" />
	</div>
	<?php } elseif( $image_size != false) {
		$width = $image_size[0];
		$height = $image_size[1];
		$aspect_ratio = round($width/$height,2);
		// Width is greater than height and width is greater than thumbnail size
		if($aspect_ratio > 1&&  $width > $thumbnail_size) {
			$thumbnail_width = $thumbnail_size;
			$thumbnail_height = $thumbnail_size/$aspect_ratio;
		// Heigth is greater than width and height is greater then thumbnail size
		} elseif($aspect_ratio < 1 && $height > $thumbnail_size) {
			$aspect_ratio = round($height/$width,2);
			$thumbnail_width = $thumbnail_size/$aspect_ratio;
			$thumbnail_height = $thumbnail_size;
		// Heigth is greater than width and height is less then thumbnail size
		} elseif($aspect_ratio < 1 && $height < $thumbnail_size) {
			$aspect_ratio = round($height/$width,2);
			$thumbnail_width = $thumbnail_size/$aspect_ratio;
			$thumbnail_height = $thumbnail_size;
		// Width and height are equal
		} elseif($aspect_ratio == 1 ) {
			$thumbnail_width = $thumbnail_size;
			$thumbnail_height = $thumbnail_size;
		// Width is greater than height and width is less than thumbnail size
		} elseif($aspect_ratio > 1 && $width < $thumbnail_size) {
			$thumbnail_width = $thumbnail_size;
			$thumbnail_height = $thumbnail_size/$aspect_ratio;
		// Hieght is greater than width and height is less than thumbnail size
		} elseif($aspect_ratio > 1 && $height < $thumbnail_size) {
			$thumbnail_width = $thumbnail_size/$aspect_ratio;
			$thumbnail_height = $thumbnail_size;
		} else {
			$thumbnail_width = $thumbnail_size;
			$thumbnail_height = $thumbnail_size;
		}
		ob_start();
		$upload_dir = wp_upload_dir();
		$src_image = $upload_dir['basedir'].MDOCS_DIR.$the_mdoc['filename'];
		if($image_size['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($src_image);
		elseif($image_size['mime'] == 'image/png') $image = imagecreatefrompng($src_image);
		elseif($image_size['mime'] == 'image/gif') $image = imagecreatefromgif($src_image);
		$thumnail =imagecreatetruecolor($thumbnail_width,$thumbnail_height);
		$white = imagecolorallocate($thumnail, 255, 255, 255);
		imagefill($thumnail, 0, 0, $white);
		imagecopyresampled($thumnail,$image,0,0,0,0,$thumbnail_width,$thumbnail_height,$image_size[0],$image_size[1]);
		
		imagepng($thumnail);
		imagedestroy($image);
		imagedestroy($thumnail);
		$png = ob_get_clean();
		$uri = "data:image/png;base64," . base64_encode($png);
	?>
	<div class="">
		<img class="mdocs-thumbnail pull-left img-thumbnail  img-responsive" src="<?php echo $uri; ?>" alt="<?php echo $the_mdoc['filename']; ?>" />
	</div>
	<?php } ?>
	<?php echo $mdocs_desc; ?>
	<div class=clearfix"></div>
	<?php
}

function mdocs_show_image_preview($the_mdoc) {
	?>
	<div style="text-align: center;">
		<img class="img-thumbnail mdocs-img-preview" src="?mdocs-img-preview=<?php echo $the_mdoc['filename']; ?>" />
	</div>
	<?php
}