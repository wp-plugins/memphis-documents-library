<?php
function mdocs_load_preview_head() {
	?>
<!DOCTYPE html>
<html>
<head>
    <title>Page Title</title>
	<?php wp_head(); ?>
	<?php //mdocs_document_ready_wp() ; ?>
	<?php  ?>
	<script>//mdocs_wp('test', 'test');</script>
</head>
<body>
<?php
}

if(isset($_POST['mdocs_file_id'])) {
	require_once($_POST['wp_root'] . 'wp-load.php');
	$mdocs = get_option('mdocs-list');
	mdocs_load_preview_head();
	foreach($mdocs as $index => $the_mdoc) {
		if(intval($the_mdoc['id']) == intval($_POST['mdocs_file_id']) && $found == false) {
			$upload_dir = wp_upload_dir();
			$file_url = $upload_dir['baseurl'].MDOCS_DIR.$the_mdoc['filename'];
			?>
			<h1><?php echo $the_mdoc['filename']; ?></h1>
			<button class="mdocs-close-btn " onclick="mdocs_close_preview();">Close</button>
			<div class="mdocs-clear-both"></div>
			<?php mdocs_social($the_mdoc); ?>
			<div class="mdocs-clear-both"></div><br>
			<?php
			mdocs_doc_preview($file_url);
			
			$found = true;
			break;
		}
	}
?>
</body>
<?php mdocs_social_scripts(); ?>
</html>
<?php
}

