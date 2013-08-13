<?php
$mdocs = get_option('mdocs-list');
$upload_dir = wp_upload_dir();
unregister_setting('mdocs-settings', 'mdocs-list');
unregister_setting('mdocs-settings', 'mdocs-cats');
unregister_setting('mdocs-settings', 'mdocs-zip');
delete_option('mdocs-list');
delete_option('mdocs-cats');
delete_option('mdocs-zip');
foreach($mdocs as $key => $value) {
	wp_delete_attachment( intval($mdocs[$key]['id']), true );
	wp_delete_post( intval($mdocs[$key]['parent']), true );
}
$files = glob($upload_dir['basedir'].'/mdocs/*'); 
foreach($files as $file){ 
  if(is_file($file)) unlink($file);
}
rmdir($upload_dir['basedir'].'/mdocs/');
$query = new WP_Query('pagename=mdocuments-library');
wp_delete_post( $query->post->ID, true );
?>