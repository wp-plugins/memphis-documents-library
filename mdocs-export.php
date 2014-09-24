<?php
function mdocs_export() {
	$upload_dir = wp_upload_dir();
	//$zip_url = $upload_dir['baseurl'].'/'.DOCS_ZIP;
	$path = $upload_dir['basedir'];
	//$vars_url = $upload_dir['baseurl'].'/'.VARS_FILE;
	$mdocs = get_option('mdocs-list');
	//$mdocs = mdocs_sort_by($mdocs, 0, 'dashboard', false);
	$mdocs = htmlspecialchars(serialize($mdocs));
	$cats = htmlspecialchars(serialize(get_option('mdocs-cats')));
?>
<p>When you click the buttons below the document repository will create a ZIP files for you to save to your computer.</p>
<p>This compressed data, will contain your documents, saved variables, and media posts tied to each document.</p>
<p>Once you've saved the download file, you can use the Import function in another WordPress installation to import the content from this site.</p>
<h3>Click the Button to Export Memphis Documents</h3>
<form action method="post" id="mdocs-export">
	<input type="button" onclick="mdocs_download_zip('<?php echo get_option('mdocs-zip'); ?>');" id="mdocs-export-submit" class="button button-primary" value="<?php _e('Export Memphis Documents Library','mdocs'); ?>">
</form><br>
<?php
	if($_GET['cat'] == 'export' || $_GET['cat'] == 'import') mdocs_export_file_status();
}

function mdocs_export_zip() {
	$mdocs_zip = get_option('mdocs-zip');
	$mdocs_list = get_option('mdocs-list');
	//$mdocs_list = mdocs_sort_by($mdocs_list, 0, 'dashboard', false);
	if(empty($mdocs_list)) $mdocs_list = array();
	$mdocs_cats = get_option('mdocs-cats');
	if(is_string($mdocs_cats)) $mdocs_cats = array();
	$upload_dir = wp_upload_dir();
	$mdocs_zip_file = $upload_dir['basedir'].'/mdocs/'.$mdocs_zip;
	$mdocs_cats_file = $upload_dir['basedir'].'/mdocs/'.MDOCS_CATS;
	$mdocs_list_file = $upload_dir['basedir'].'/mdocs/'.MDOCS_LIST;
	file_put_contents($mdocs_cats_file, serialize($mdocs_cats));
	file_put_contents($mdocs_list_file, serialize($mdocs_list));
	mdocs_zip_dir($upload_dir['basedir'].'/mdocs',$mdocs_zip_file,true);
	unlink($mdocs_cats_file);
	unlink($mdocs_list_file);
}

function mdocs_zip_dir($sourcePath, $outZipPath)  { 
    @unlink($outZipPath);
	$pathInfo = pathInfo($sourcePath); 
    $parentPath = $pathInfo['dirname']; 
    $dirName = $pathInfo['basename']; 
    $z = new ZipArchive(); 
    $z->open($outZipPath, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE); 
    $z->addEmptyDir($dirName); 
    mdocs_folder_zip($sourcePath, $z, strlen("$parentPath/")); 
    $z->close();
}

function mdocs_folder_zip($folder, &$zipFile, $exclusiveLength) { 
	$handle = opendir($folder); 
    while (false !== $f = readdir($handle)) { 
      if ($f != '.' && $f != '..') { 
        $filePath = "$folder/$f";
        // Remove prefix from file path before add to zip. 
        $localPath = substr($filePath, $exclusiveLength); 
        if (is_file($filePath)) { 
          $zipFile->addFile($filePath, $localPath); 
        } elseif (is_dir($filePath)) { 
          // Add sub-directory. 
          $zipFile->addEmptyDir($localPath); 
          mdocs_folder_zip($filePath, $zipFile, $exclusiveLength); 
        } 
      } 
    } 
    closedir($handle); 
}

function mdocs_zip_files($files, $out_zip_path, $overwrite_zip=true) {
	if($overwrite_zip) @unlink($out_zip_path);
	$z = new ZipArchive();
	$z->open($out_zip_path, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
	foreach($files as $key => $value) {
		$pathInfo = pathInfo($value);
		$parentPath = $pathInfo['dirname']; 
		$localPath = substr($value, strlen($parentPath)); 
		$z->addFile($value, $localPath);
	}
	$z->close();
}

function mdocs_download_zip($path) {
	$doc_dir =$path.'/mphs-docs';
	$zip_file = $path.'/'.DOCS_ZIP;
	mphs_zip_dir($doc_dir,$zip_file,true);
}

function mdocs_download_vars($vars, $path) {
	$doc_dir =$path.'/mphs-docs';
	$vars_file = $path.'/'.VARS_FILE;
	$zip_var = $path.'/'.VARS_ZIP;
	file_put_contents($vars_file, $vars);
	mphs_zip_files(array($vars_file),$zip_var);
}
?>