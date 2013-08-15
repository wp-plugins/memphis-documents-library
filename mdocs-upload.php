<?php
function mdocs_file_upload() {
	global $current_user;
	$mdocs = get_option('mdocs-list');
	$mdocs_cats = get_option('mdocs-cats');
	$mdocs_index = $_POST['mdocs-index'];
	$mdocs_filename = $_FILES['mdocs']['name'];
	$mdocs_name = $_POST['mdocs-name'];
	$mdocs_fle_type = substr(strrchr($mdocs_filename, '.'), 1 );
	$mdocs_fle_size = $_FILES["mdocs"]["size"];
	$mdocs_type = $_POST['mdocs-type'];
	$mdocs_cat = $_POST['mdocs-cat'];
	$mdocs_desc = $_POST['mdocs-desc'];
	$mdocs_version = $_POST['mdocs-version'];
	$upload_dir = wp_upload_dir();	
	$mdocs_user = $current_user->display_name;
	$_FILES['mdocs']['name'] = preg_replace('/[^A-Za-z0-9\-._]/', '', $_FILES['mdocs']['name']);
	$_FILES['mdocs']['name'] = str_replace(' ','', $_FILES['mdocs']['name']);
	//MDOCS FILE TYPE VERIFICATION	
	$mimes = get_allowed_mime_types();
	foreach ($mimes as $type => $mime) {
	  if ($mime === $_FILES['mdocs']['type']) {
		$valid_mime_type = true;
		break;
	  }
	}
	//MDOCS NONCE VERIFICATION
	if ($_REQUEST['mdocs-nonce'] == MDOCS_NONCE ) {
		if(!empty($mdocs_cats)) {
			if($mdocs_type == 'mdocs-add') {
				if($valid_mime_type) {
					$upload = mdocs_process_file($_FILES['mdocs']);
					if($mdocs_version == '') $mdocs_version = '1.0';
					//elseif(!is_numeric($mdocs_version)) $mdocs_version = '1.0'; 
					if($upload['error'] == '') {
						array_push($mdocs, array(
							id=>(string)$upload['attachment_id'],
							parent=>(string)$upload['parent_id'],
							filename=>$upload['filename'],
							name=>$upload['name'],
							desc=>$upload['desc'],
							type=>$mdocs_fle_type,
							cat=>$mdocs_cat,
							owner=>$mdocs_user,
							size=>(string)$mdocs_fle_size,
							modified=>(string)time(),
							version=>(string)$mdocs_version,
							downloads=>(string)0,
							archived=>array()
						));
						$mdocs = mdocs_array_sort($mdocs, 'name', 'SORT_ASC, SORT_STRING');
						update_option('mdocs-list', $mdocs);
					} else mdocs_errors(MDOCS_ERROR_5,'error');
				} else mdocs_errors(MDOCS_ERROR_2 , 'error');
			} elseif($mdocs_type == 'mdocs-update') {
				if($_FILES['mdocs']['name'] != '') {
					if($valid_mime_type) {
						$old_doc = $mdocs[$mdocs_index];
						$old_doc_name = $old_doc['filename'].'-v'.preg_replace('/ /', '',$old_doc['version']);
						@rename($upload_dir['basedir'].'/mdocs/'.$old_doc['filename'],$upload_dir['basedir'].'/mdocs/'.$old_doc_name);
						$name = substr($old_doc['filename'], 0, strrpos($old_doc['filename'], '.') );
						$filename = $name.'.'.$mdocs_fle_type;
						$_FILES['mdocs']['name'] = $filename;
						$_FILES['mdocs']['parent'] = $old_doc['parent'];
						$_FILES['mdocs']['id'] = $old_doc['id'];
						$upload = mdocs_process_file($_FILES['mdocs']);
						if($upload['error'] == '') {
							//$new_version = floatval($mdocs_version)+floatval($mdocs[$mdocs_index]['version']);
							//if(floatval($mdocs_version) == 1) $new_version = number_format($new_version,0);
							if($mdocs_version == '' || $mdocs_version == $mdocs[$mdocs_index]['version']) $mdocs_version = $mdocs[$mdocs_index]['version'].'.'.time();
							//elseif(!is_numeric($mdocs_version)) $mdocs_version = floatval($mdocs[$mdocs_index]['version'])+0.1;
							$mdocs[$mdocs_index]['filename'] = $upload['filename'];
							$mdocs[$mdocs_index]['name'] = $upload['name'];
							$mdocs[$mdocs_index]['desc'] = $upload['desc'];
							$mdocs[$mdocs_index]['version'] = (string)$mdocs_version;
							$mdocs[$mdocs_index]['type'] = (string)$mdocs_fle_type;
							$mdocs[$mdocs_index]['cat'] = $mdocs_cat;
							$mdocs[$mdocs_index]['owner'] = $mdocs_user;
							$mdocs[$mdocs_index]['size'] = (string)$mdocs_fle_size;
							$mdocs[$mdocs_index]['modified'] = (string)time();
							array_push($mdocs[$mdocs_index]['archived'], $old_doc_name);
							$mdocs = mdocs_array_sort($mdocs, 'name', 'SORT_ASC, SORT_STRING');
							update_option('mdocs-list', $mdocs);
						} else mdocs_errors(MDOCS_ERROR_5,'error');
					} else mdocs_errors(MDOCS_ERROR_2 , 'error');
				} else {
					if($mdocs_desc == '') $desc = MDOCS_DEFAULT_DESC;
					else $desc = $mdocs_desc;
					if($mdocs_name == '') $mdocs[$mdocs_index]['name'] = $_POST['mdocs-pname'];
					else $mdocs[$mdocs_index]['name'] = $mdocs_name;
					if($mdocs_version == '') $mdocs_version = $mdocs[$mdocs_index]['version'];
					$mdocs[$mdocs_index]['desc'] = $desc;
					$mdocs[$mdocs_index]['version'] = (string)$mdocs_version;
					$mdocs[$mdocs_index]['cat'] = $mdocs_cat;
					$mdocs[$mdocs_index]['owner'] = $mdocs_user;
					$mdocs[$mdocs_index]['modified'] = (string)time();
					$mdocs_post = array(
						'ID' => $mdocs[$mdocs_index]['parent'],
						'post_title' => $mdocs_name,
						'post_content' => '[mdocs_post_page]',
						'post_status' => 'publish',
						'post_excerpt' => $desc,
					);
					wp_update_post( $mdocs_post );
					$mdocs_attachment = array(
						'ID' => $mdocs[$mdocs_index]['id'],
						'post_title' => $mdocs_name
					);
					wp_update_post( $mdocs_attachment );
					$mdocs = mdocs_array_sort($mdocs, 'name', 'SORT_ASC, SORT_STRING');
					update_option('mdocs-list', $mdocs);
				}
			}
		} else mdocs_errors(MDOCS_ERROR_3,'error');
	} else mdocs_errors(MDOCS_ERROR_4,'error');
}
?>