<?php
function mdocs_load_preview() {
	if(isset($_POST['type'])) {
		global $mdocs_img_types;
		$mdocs = get_option('mdocs-list');
		$mdocs_show_preview = get_option('mdocs-show-preview');
		$found = false;
		$is_admin = $_POST['is_admin'];
		foreach($mdocs as $index => $the_mdoc) {
			$mdocs_show_non_members = $the_mdoc['non_members'];
			if(intval($the_mdoc['id']) == intval($_POST['mdocs_file_id']) && $found == false) {
				if(is_user_logged_in() || $mdocs_show_non_members == 'on' && $mdocs_show_preview = '1' ) {
					if($_POST['type'] == 'file') {
						echo '<h1>'.$the_mdoc['filename'].'</h1>';
						$upload_dir = wp_upload_dir();
						$file_url = get_site_url().'/?mdocs-file='.$the_mdoc['id'].'|'.is_user_logged_in();
						mdocs_doc_preview($file_url);
					} elseif($_POST['type'] == 'img') {
						echo '<h1>'.$the_mdoc['filename'].'</h1>';
						echo '<img class="mdocs-img-preview" src="?mdocs-img-preview='.$the_mdoc['filename'].'" />';
					} elseif($_POST['type'] == 'show') {
						if($_POST['show_type'] == 'preview') {
							$upload_dir = wp_upload_dir();
							$file_url = get_site_url().'/?mdocs-file='.$the_mdoc['id'].'|'.is_user_logged_in();
							if(in_array($the_mdoc['type'], $mdocs_img_types)) echo '<img class="mdocs-img-preview" src="?mdocs-img-preview='.$the_mdoc['filename'].'" />';
							else mdocs_doc_preview($file_url);
						} else {
							$mdocs_desc = apply_filters('the_content', $the_mdoc['desc']);
							$mdocs_desc = str_replace('\\','',$mdocs_desc);
							?>
							<h3>Description</h3>
							<div class="mdoc-desc">
							<p><?php echo $mdocs_desc; ?></p>
							</div>
							<?php
						}
					}
				}  else {
					?><div class="alert alert-warning" role="alert"><h1><?php _e('Sorry you are unauthorized to preview this file.','mdocs'); ?></h1></div><?php
				}
				$found = true;
				break;
			}
		}
	}
}