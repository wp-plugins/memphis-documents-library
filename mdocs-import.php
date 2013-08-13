<?php
function mdocs_import() {
	$upload_dir = wp_upload_dir();
?>
<p><?php _e('There are two type of imports you can choose from.'); ?></p>
<p>
	<ol>
		<li><b><?php _e('Keep Existing Saved Variables'); ?></b>
			<blockquote><?php _e('Is the safest way to import.  This option keeps all your current files and only imports new ones. 
			<br>If a file that is being imported matches one on the current system, the one on the current system will be left untouched,
			<br>and you will have to manually import these files.'); ?></blockquote>
		</li>
		<li><b><?php _e('Overwrite Saved Variables'); ?></b>
			<blockquote><?php _e('Is a good when you have a empty documents library or you at looking to refresh your current library.'); ?>   
			<br><?php _e('This method deletes all files, posted and version on the current system. After the method has completed you will
			<br>get a list of all the conflicts that have occured make note of them.'); ?>
			<i><?php _e('Please take great care in using this method as there is little to no return.'); ?></i></blockquote>
		</li>
	</ol>
</p>
<form  id="mdocs-import" method="post" action="admin.php?page=memphis-documents.php&cat=import" enctype="multipart/form-data">
	<h3><?php _e('Add the Zip File'); ?>:</h3>
	<p><b><i><?php _e('Remember to always export any valuable data before doing an import.'); ?></i></b></p>
	<input type="hidden" value="mdocs-import" id="action" name="action"/>
	<input type="radio" value="keep" name="radio1" checked> <?php _e('Keep Existing Saved Variables'); ?>
	<input type="radio" value="overwrite" name="radio1" > <?php _e('Overwrite Saved Variables'); ?><br><br>
	<input type="file" name="mdocs-import-file" id="mdocs-import-file"/>
	<input type="submit" class="button button-primary" id="mdocs-import-submit" value="<?php _e('Import Memphis Documents Library') ?>" />
</form><br>
<?php
	if($_GET['cat'] == 'export' || $_GET['cat'] == 'import') mdocs_export_file_status();
}

function mdocs_import_zip() {
	if($_FILES['mdocs-import-file']['name'] != '' ) {
		$error = false;
		$upload_dir = wp_upload_dir();
		$mdocs = get_option('mdocs-list');
		$mdocs_zip_file = $upload_dir['basedir'].'/mdocs/'.$_FILES['mdocs-import-file']['name'];
		//Backup Current Memphis Documents
		if(!file_exists($upload_dir['basedir'].'/mdocs-backup/')) mkdir($upload_dir['basedir'].'/mdocs-backup/');
		$files = glob($upload_dir['basedir'].'/mdocs/*'); 
		foreach($files as $file){ 
		  if(is_file($file))
			$explode = explode('/',$file);
			$filename = $explode[count($explode)-1];
			rename($file, $upload_dir['basedir'].'/mdocs-backup/'.$filename);
		}
		if(file_exists($mdocs_zip_file)) unlink($mdocs_zip_file);
		move_uploaded_file($_FILES['mdocs-import-file']['tmp_name'], $mdocs_zip_file);
		$zip_result = mdocs_unzip($mdocs_zip_file, $upload_dir['basedir']);
		if(is_array($zip_result)) {
			if(file_exists($upload_dir['basedir'].'/mdocs/mdocs-list.txt')) {
				$mdocs_list_file = unserialize(file_get_contents($upload_dir['basedir'].'/mdocs/mdocs-list.txt'));
			} else $error = true;
			if(file_exists($upload_dir['basedir'].'/mdocs/mdocs-cats.txt')) {
				$mdocs_cats_file = unserialize(file_get_contents($upload_dir['basedir'].'/mdocs/mdocs-cats.txt'));
			} else $error = true;
			if($mdocs_cats_file === false || $mdocs_list_file === false || $error ) {
				if(file_exists($upload_dir['basedir'].'/mdocs/mdocs-list.txt')) unlink($upload_dir['basedir'].'/mdocs/mdocs-cats.txt');
				if(file_exists($upload_dir['basedir'].'/mdocs/mdocs-cats.txt')) unlink($upload_dir['basedir'].'/mdocs/mdocs-list.txt');
				unlink($upload_dir['basedir'].'/mdocs/'.$_FILES['mdocs-import-file']['name']);
				foreach($zip_result['file'] as $file) { 
					if(is_file($file)) unlink($file);
				}
				foreach($zip_result['dir'] as $dir) { 
					rmdir($dir);
				}
				$files = glob($upload_dir['basedir'].'/mdocs-backup/*'); 
				foreach($files as $file) { 
					if(is_file($file))
						$explode = explode('/',$file);
						$filename = $explode[count($explode)-1];
						rename($file, $upload_dir['basedir'].'/mdocs/'.$filename);
				}
				rmdir($upload_dir['basedir'].'/mdocs-backup/');
				mdocs_errors('There was an error processing your saved variables file.  Sorry the import process can not continue.','error');
			} else {
				update_option('mdocs-zip',$_FILES['mdocs-import-file']['name']);
				$mdocs_list_conflicts = array();
				$mdocs_cats_conflicts = array();
				if($_POST['radio1'] == 'overwrite') {
					foreach($mdocs as $key => $value) {
						wp_delete_attachment( intval($mdocs[$key]['id']), true );
						wp_delete_post( intval($mdocs[$key]['parent']), true );
						mdocs_unzip($mdocs_zip_file, $upload_dir['basedir']);
					}
					update_option('mdocs-list', $mdocs_list_file);
					update_option('mdocs-cats', $mdocs_cats_file);
					$cats = get_option('mdocs-cats');
					$mdocs = array();
				} else {
					$mdocs = get_option('mdocs-list');
					$mdocs_cats = get_option('mdocs-cats');
					$modocs_list_return = array();
					foreach($mdocs as $key => $value) {
						$found = false;
						foreach($mdocs_list_file as $k => $v) {
							if($mdocs_list_file[$k]['filename'] == $mdocs[$key]['filename']) {
								array_push($mdocs_list_conflicts, $mdocs_list_file[$k]['filename']);
								$found = true;
								unset($mdocs_list_file[$k]);
								$thumbnails = glob($upload_dir['basedir'].'/mdocs-backup/'.$name.'-150x55*');
								$versions = glob($upload_dir['basedir'].'/mdocs-backup/'.$value['filename'].'-v*');
								foreach($thumbnails as $t) copy($t, str_replace('mdocs-backup','mdocs',$t));
								foreach($versions as $v) copy($v, str_replace('mdocs-backup','mdocs',$v));
								copy($upload_dir['basedir'].'/mdocs-backup/'.$value['filename'], $upload_dir['basedir'].'/mdocs/'.$value['filename']);
								break;
							}
						}
						if($found == false) {
							$explode = explode('.',$value['filename']);
							array_pop($explode);
							$name = implode('',$explode);
							$thumbnails = glob($upload_dir['basedir'].'/mdocs-backup/'.$name.'-150x55*');
							$versions = glob($upload_dir['basedir'].'/mdocs-backup/'.$value['filename'].'-v*');
							foreach($thumbnails as $t) copy($t, str_replace('mdocs-backup','mdocs',$t));
							foreach($versions as $v) copy($v, str_replace('mdocs-backup','mdocs',$v));
							copy($upload_dir['basedir'].'/mdocs-backup/'.$value['filename'], $upload_dir['basedir'].'/mdocs/'.$value['filename']);
						}
					}
					foreach($mdocs_cats_file as $key => $value) {
						$found = false;
						if(!empty($cats)) {
							foreach($mdocs_cats as $k => $v) {
								if($key == $k) {
									array_push($mdocs_cats_conflicts, $mdocs_cats[$k]);
									$found = true;
									break;
								}
							}
						}
						if($found == false) $mdocs_cats[$key] = $value;
					}
					
					update_option('mdocs-cats',$mdocs_cats);
				}
				foreach($mdocs_list_file as $key => $value) {
					$file = array(
						'type'=>'null',
						'tmp_name'=>'null',
						'error'=> 0,
						'size' => 0,
						'filename'=>$mdocs_list_file[$key]['filename'],
						'name'=>$mdocs_list_file[$key]['name'],
						'desc'=>$mdocs_list_file[$key]['desc']);
					$upload = mdocs_process_file($file, true);
					array_push($mdocs, array(
						id=>(string)$upload['attachment_id'],
						parent=>(string)$upload['parent_id'],
						filename=>$mdocs_list_file[$key]['filename'],
						name=>$mdocs_list_file[$key]['name'],
						desc=>$mdocs_list_file[$key]['desc'],
						type=>$mdocs_list_file[$key]['type'],
						cat=>$mdocs_list_file[$key]['cat'],
						owner=>$mdocs_list_file[$key]['owner'],
						size=>(string)$mdocs_list_file[$key]['size'],
						modified=>(string)(string)$mdocs_list_file[$key]['modified'],
						version=>(string)(string)$mdocs_list_file[$key]['version'],
						downloads=>(string)$mdocs_list_file[$key]['downloads'],
						archived=>$mdocs_list_file[$key]['archived']
					));
					$mdocs = mdocs_array_sort($mdocs, 'name', 'SORT_ASC, SORT_STRING');
					update_option('mdocs-list', $mdocs);
				}
				$files = glob($upload_dir['basedir'].'/mdocs-backup/*'); 
				foreach($files as $file){ 
					if(is_file($file)) unlink($file);
				}
				rmdir($upload_dir['basedir'].'/mdocs-backup/');
				unlink($upload_dir['basedir'].'/mdocs/mdocs-cats.txt');
				unlink($upload_dir['basedir'].'/mdocs/mdocs-list.txt');
				if(count($mdocs_list_conflicts) > 0) mdocs_errors('The following files where not added to the Documents Library. You will have to upload these files manually:<ul><li><b>' .implode('</li><li>',$mdocs_list_conflicts).'</b></li></ul>', 'error');
				if(count($mdocs_cats_conflicts) > 0) mdocs_errors('The following categories where not added to the Documents Library. You will have to add these categories manually:<ul><li><b>' .implode('</li><li>',$mdocs_cats_conflicts).'</b></li></ul>', 'error');
			} 
		} else {
			unlink($upload_dir['basedir'].'/mdocs/'.$_FILES['mdocs-import-file']['name']);
			$files = glob($upload_dir['basedir'].'/mdocs-backup/*'); 
			foreach($files as $file){ 
			  if(is_file($file))
				$explode = explode('/',$file);
				$filename = $explode[count($explode)-1];
				rename($file, $upload_dir['basedir'].'/mdocs/'.$filename);
			}
			rmdir($upload_dir['basedir'].'/mdocs-backup/');
			if(file_exists($upload_dir['basedir'].'/mdocs/mdocs-list.txt')) unlink($upload_dir['basedir'].'/mdocs/mdocs-cats.txt');
			if(file_exists($upload_dir['basedir'].'/mdocs/mdocs-cats.txt')) unlink($upload_dir['basedir'].'/mdocs/mdocs-list.txt');
		}
	} else mdocs_errors('The file you are trying to upload is not the correct file.  Please try again.','error');
}

function mdocs_unzip($zip_file, $output_path) {
	$zip = new ZipArchive;
	$zip_result = false;
	if ($zip->open($zip_file) === TRUE) {
		$zip_result = array();
		for($i = 0; $i < $zip->numFiles; $i++)  {
			$output_dir = false;
			$file = $zip->getNameIndex($i);			
			if(preg_match('/.DS_Store/',$file)) $output_file = false;
			elseif(preg_match('/__MACOSX/',$file)) $output_file = false;
			elseif(preg_match('/mdocs\//',$file)) $output_file = $output_path.'/'.$file; 
			elseif(strrpos($file, '/') === strlen($file)-1|| strrchr($file, '\\') != false ) { @mkdir($output_path.'/mdocs/'.$file); $output_file = false; $output_dir = $output_path.'/mdocs/'.$file; }
			elseif(!preg_match('/mdocs\//',$file)) $output_file = $output_path.'/mdocs/'.$file;
			elseif(preg_match('/mdocs\//',$file)) $output_file = $output_path;
			else $output_file = false;
			if($output_file) {
				@copy('zip://'. $zip_file .'#'. $file , $output_file );
				$zip_result['file'][$i] = $output_file;
			}
			if($output_dir) {
				$zip_result['dir'][$i] = $output_dir;
			}
		}
		$zip_result['file'] = array_values($zip_result['file']);
		if(isset($zip_result['dir']))  $zip_result['dir'] = array_values($zip_result['dir']);
		$zip->close();
		return $zip_result;
	} else mdocs_errors('The Documents zip file has failed to imported.','error');	
	return $zip_result;
}

?>
