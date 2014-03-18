<?php
if(isset($_GET['mdocs-file'])) mdocs_download_file();
if(isset($_GET['mdocs-version'])) mdocs_download_file($_GET['mdocs-version']);
if(isset($_GET['mdocs-export-file'])) mdocs_download_file($_GET['mdocs-export-file']);
if(isset($_GET['mdocs-img-preview'])) mdocs_img_preview();

function mdocs_download_file($export_file='') {
	require_once(ABSPATH . 'wp-includes/pluggable.php');
	$upload_dir = wp_upload_dir();
	$mdocs = get_option('mdocs-list');
	//$mdocs = mdocs_sort_by($mdocs,0,'dashboard',false);
	$mdocs_hide_all_files = get_option( 'mdocs-hide-all-files' );
	$is_logged_in = is_user_logged_in();
	$login_denied = false;
	
	if(!empty($export_file) ) { $filename = $export_file; }
	else {
		mdocs_send_bot_alert($_GET['mdocs-url']);
		foreach($mdocs as $index => $value) {
			if($value['id'] == $_GET["mdocs-file"] ) {
				$filename = $mdocs[$index]['filename'];
				$non_member = $mdocs[$index]['non_members'];
				$file_status = $mdocs[$index]['file_status'];
				
				break;
			} //else $filename = 'mdocs-empty';
		}
	}
	if($non_member == '' && $is_logged_in == false || $file_status == 'hidden' && !is_admin() || $mdocs_hide_all_files  ) $login_denied = true;
	else $login_denied = false;
	if(mdocs_is_bot() == false && $login_denied == false && !isset($_GET['mdocs-export-file'])) {
		$mdocs[$index]['downloads'] = (string)(intval($mdocs[$index]['downloads'])+1);
		update_option('mdocs-list', $mdocs);
	}
	
	if(isset($_GET['mdocs-export-file'])) mdocs_export_zip();
	$file = $upload_dir['basedir']."/mdocs/".$filename;
	if(isset($_GET['mdocs-version'])) $filename = substr($filename, 0, strrpos($filename, '-'));
	$filetype = wp_check_filetype($file );
	//if(mdocs_is_bot()) mdocs_send_bot_alert();
	
	if($login_denied == false ) {
		if (file_exists($file) && mdocs_is_bot() == false  ) {		
			header('Content-Description: File Transfer');
			header('Content-Type: '.$filetype['type']);
			header('Content-Disposition: attachment; filename='.$filename);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); 
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			ob_clean();
			flush();
			readfile($file);
			exit;
		} else die(__('Check this file out').' <b>'.$filename. '</b>.  '.  __('Download it from').' <b>'.get_bloginfo('name').'</b>.<br><sup>'.__('powered by Memphis Documents Library').'</sup>');
	} else die(__('Sorry you are unauthorized to download this file.'));
}

function mdocs_img_preview() {
	require_once(ABSPATH . 'wp-includes/pluggable.php');
	$upload_dir = wp_upload_dir();
	$image = $upload_dir['basedir'].MDOCS_DIR.$_GET['mdocs-img-preview']; 
	$content = file_get_contents($image);
	header('Content-Type: image/jpeg');
	echo $content; exit();
}
?>