<?php
function mdocs_load_preview_head() {
	?>
<!DOCTYPE html>
<html>
<head>
    <title>Page Title</title>
	<?php //wp_head(); ?>
	<?php  ?>
</head>
<body>
<?php
}

if(isset($_POST['type'])  && $_POST['type'] == 'file') {
	require_once($_POST['wp_root'] . 'wp-load.php');
	$mdocs = get_option('mdocs-list');
	mdocs_load_preview_head();
	foreach($mdocs as $index => $the_mdoc) {
		if(intval($the_mdoc['id']) == intval($_POST['mdocs_file_id']) && $found == false) {
			$upload_dir = wp_upload_dir();
			$file_url = $upload_dir['baseurl'].MDOCS_DIR.$the_mdoc['filename'];
			?>
			<h1><?php echo $the_mdoc['filename']; ?></h1>
			<?php
			if(isset($_POST['is_admin'])) echo	'<button class="mdocs-close-btn " onclick="mdocs_close_preview(\'admin\');">Close</button>';
			else echo	'<button class="mdocs-close-btn " onclick="mdocs_close_preview(\'wp\');">Close</button>';
			?>
			<div class="mdocs-clear-both"></div>
			<?php mdocs_social($the_mdoc); ?></div>
			<div class="mdocs-clear-both"></div>
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
} elseif(isset($_POST['type']) && $_POST['type'] == 'img') {
	require_once($_POST['wp_root'] . 'wp-load.php');
	$mdocs = get_option('mdocs-list');
	mdocs_load_preview_head();
	mdocs_social_scripts();
	$found = false;
	foreach($mdocs as $index => $the_mdoc) {
		if(intval($the_mdoc['id']) == intval($_POST['mdocs_file_id']) && $found == false) {
			?>
			<h1><?php echo $the_mdoc['filename']; ?></h1>
			<?php
			if(isset($_POST['is_admin'])) echo	'<button class="mdocs-close-btn " onclick="mdocs_close_preview(\'admin\');">Close</button>';
			else echo	'<button class="mdocs-close-btn " onclick="mdocs_close_preview(\'wp\');">Close</button>';
			?>
			<div class="mdocs-clear-both"></div>
			<?php mdocs_social($the_mdoc); ?></div>
			<div class="mdocs-clear-both"></div>
			<?php
		?>
		<iframe class="mdocs-img-preview" src="?mdocs-img-preview=<?php echo $the_mdoc['filename']; ?>"></iframe>
		<?php
		$found = true;
		break;
		}
	}
} elseif(isset($_POST['type']) && $_POST['type'] == 'show') {
	require_once($_POST['wp_root'] . 'wp-load.php');
	global $mdocs_img_types;
	$mdocs = get_option('mdocs-list');
	$found = false;
	foreach($mdocs as $index => $the_mdoc) {
		if(intval($the_mdoc['id']) == intval($_POST['mdocs_file_id']) && $found == false) {
			if($_POST['show_type'] == 'preview') {
				$upload_dir = wp_upload_dir();
				$file_url = $upload_dir['baseurl'].MDOCS_DIR.$the_mdoc['filename'];
				if(in_array($the_mdoc['type'], $mdocs_img_types)) { ?><iframe class="mdocs-img-preview" src="?mdocs-img-preview=<?php echo $the_mdoc['filename']; ?>"></iframe><?php }
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
			$found = true;
			break;
		}
	}
}

