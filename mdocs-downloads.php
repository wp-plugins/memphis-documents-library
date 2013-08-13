<?php
if(isset($_GET['mdocs-file'])) mdocs_download_file();
if(isset($_GET['mdocs-version'])) mdocs_download_file($_GET['mdocs-version']);
if(isset($_GET['mdocs-export-file'])) mdocs_download_file($_GET['mdocs-export-file']);

function mdocs_download_file($export_file='') {
	$upload_dir = wp_upload_dir();
	$mdocs = get_option('mdocs-list');
	if(!empty($export_file) ) { $filename = $export_file; }
	else {
		foreach($mdocs as $index => $value) {
			if($value['id'] == $_GET["mdocs-file"]) {
				$filename = $mdocs[$index]['filename'];
				$mdocs[$index]['downloads'] = (string)(intval($mdocs[$index]['downloads'])+1);
				update_option('mdocs-list', $mdocs);
				break;
			} else $filename = 'mdocs-empty';
		}
	}
	if(isset($_GET['mdocs-export-file'])) mdocs_export_zip();
	$file = $upload_dir['basedir']."/mdocs/".$filename;
	if(isset($_GET['mdocs-version'])) $filename = substr($filename, 0, strrpos($filename, '-'));
	$filetype = wp_check_filetype($file, null );
	if (file_exists($file) ) {		
		header('Content-Description: File Transfer');
		header('Content-Type: '.$filetype);
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
	} else die('Error Downloading File.');	
}
?>