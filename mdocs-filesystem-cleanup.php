<?php
function mdocs_filesystem_cleanup() {
if(!isset($_POST['mdocs-filesystem-cleanup'])) {
	mdocs_list_header();
	?>
	<div class="updated">
	<form enctype="multipart/form-data" method="post" action="" class="mdocs-setting-form">
		<h3><?php _e('Filesystem Cleanup','mdocs'); ?></h3>
		<p><?php _e('Use this functionality to run a system check to locate and remove any broken files/data links inside Memphis Documents Library.<br>Be sure to make a backup copy before running this check.','mdocs'); ?></p>
		<input type="hidden" name="mdocs-filesystem-cleanup" value="init-cleanup" />
		<input style="margin:15px;" type="submit" class="button-primary" id="mdocs-filesystem-cleanup" value="<?php _e('Run Filesystem Cleanup','mdocs') ?>" />
	</form>
	</div>
<?php
} elseif(isset($_POST['mdocs-filesystem-cleanup']) && $_POST['mdocs-filesystem-cleanup'] == 'init-cleanup') {
	mdocs_cleanup_init_html();
} elseif(isset($_POST['mdocs-filesystem-cleanup']) && $_POST['mdocs-filesystem-cleanup'] == 'submit-cleanup') {
	mdocs_cleanup_submit_html();
}
}
function mdocs_cleanup_submit_html() {
	mdocs_filesystem_cleanup_submit();
	mdocs_list_header();
	?>
	<div class="updated">
		<h3><?php _e('Filesystem Cleanup Complete','mdocs'); ?></h3>
		<p><?php _e('Your file system has been cleaned.  Remember if you encounter any issues revert back to your previous version using the import tool.','mdocs'); ?></p>
		<div class="cleanup-files">
			<h3><?php _e('Unlinked Files','mdocs'); ?></h3>
		<?php
		$cleanup = mdocs_filesystem_cleanup_init();
		foreach($cleanup['files'] as $file) echo $file.'<br>';
		if(count($cleanup['files']) == 0) _e('There are no unlinked files.','mdocs');
		?>
		</div>
		<div class="cleanup-data">
			<h3><?php _e('Unlinked Data','mdocs'); ?></h3>
		<?php
		foreach($cleanup['data'] as $data) echo __('Element ID','mdocs').': '.$data['index'].'<br>';
		if(count($cleanup['data']) == 0) _e('There is no unlinked data.','mdocs');
		?>
		</div>
		<div class="mdocs-clear-both"></div>
		<form enctype="multipart/form-data" method="post" action="" class="mdocs-setting-form">
			<input type="hidden" name="mdocs-filesystem-cleanup" value="init-cleanup" />
			<input style="margin:15px;" type="submit" class="button-primary" id="mdocs-filesystem-cleanup" value="<?php _e('Run File System Cleanup Again','mdocs') ?>" />
		</form>
	</div>
	<?php
}
function mdocs_cleanup_init_html() {
	$cleanup = mdocs_filesystem_cleanup_init();
	mdocs_list_header();
	?>
	<div class="updated">
		<h3><?php _e('Filesystem Analyzed','mdocs'); ?></h3>
		<p><?php _e('Below is a list of files and data that look to be broken and or unused by Memphis Documents Library.  The next phase of the process will try and remove all this unlinked information.<br>Please make sure you have made an export of the files before continuing.  If anything goes wrong just import your export file to revert all changes.','mdocs'); ?></p>
		<div class="cleanup-files">
			<h3><?php _e('Unlinked Files','mdocs'); ?></h3>
		<?php
		foreach($cleanup['files'] as $file) echo $file.'<br>';
		if(count($cleanup['files']) == 0) _e('There are no unlinked files.','mdocs');
		?>
		</div>
		<div class="cleanup-data">
			<h3><?php _e('Unlinked Data','mdocs'); ?></h3>
		<?php
		foreach($cleanup['data'] as $data) echo __('Element ID','mdocs').': '.$data['index'].'<br>';
		if(count($cleanup['data']) == 0) _e('There is no unlinked data.','mdocs');
		?>
		</div>
		<div class="mdocs-clear-both"></div>
		<form enctype="multipart/form-data" method="post" action="" class="mdocs-setting-form">
		   <input type="hidden" name="mdocs-filesystem-cleanup" value="submit-cleanup" />
		   <input style="margin:15px;" type="submit" class="button-primary" id="mdocs-filesystem-cleanup" value="<?php _e('Cleanup The File System','mdocs') ?>" />
	   </form>
	</div>
	<?php
}

function mdocs_filesystem_cleanup_submit() {
	$mdocs = get_option('mdocs-list');
	$cleanup = mdocs_filesystem_cleanup_init();
	$upload_dir = wp_upload_dir();
	foreach($cleanup['files'] as $file) {
		unlink($upload_dir['basedir'].'/mdocs/'.$file);
	}
	foreach($cleanup['data'] as $data) {
		if(isset($data['id'])) wp_delete_attachment( intval($data['id']), true );
		if(isset($data['parent'])) wp_delete_post( intval($data['parent']), true );
		unset($mdocs[$data['index']]);
		$mdocs = array_values($mdocs);
		update_option('mdocs-list',$mdocs);
	}
	
	//wp_delete_attachment( intval($mdocs[$key]['id']), true );
	//wp_delete_post( intval($mdocs[$key]['parent']), true );
	//var_dump($cleanup);
}

function mdocs_filesystem_cleanup_init() {
	$mdocs_zip_file = get_option('mdocs-zip');
	$upload_dir = wp_upload_dir();
	$mdocs = get_option('mdocs-list');
	$files = glob($upload_dir['basedir'].'/mdocs/*');
	$clean_up_files = array();
	$valid_file = false;
	foreach($files as $the_file) {
		foreach($mdocs as $key => $the_doc) {
			$the_file = explode('/',$the_file);
			$the_file = $the_file[count($the_file)-1];
			$d = explode('.',$the_doc['filename']);
			$d = $d[0];
			if(preg_match('/'.$d.'/', $the_file)) {
				$valid_file = true;
				break;
			}
			if($the_file == $mdocs_zip_file) {
				$valid_file = true;
				break;
			}
			if($the_file == $the_doc['filename']) {	
				$valid_file = true;
				break;
			} 
			if(is_array($the_doc['archived'])) {
				$valid_data = true;
				foreach($the_doc['archived'] as $the_archive) {
					if($the_file == $the_archive) {
						$valid_file = true;
						break;
					}
				}
			}
		}
		if($valid_file == false) array_push($clean_up_files, $the_file);
		$valid_file = false;
	}
	$valid_data = false;
	$clean_up_data = array();
	foreach($mdocs as $key => $the_doc) {
		if(!isset($the_doc['filename']) || $the_doc['filename'] == '' || !is_array($the_doc['archived'])) {
			$the_doc['index'] = $key;
			array_push($clean_up_data, $the_doc);
		}
	}
	
	return array('files'=> $clean_up_files, 'data'=>$clean_up_data);
}
?>