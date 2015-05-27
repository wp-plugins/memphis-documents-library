<?php
function mdocs_restore_defaults() {
	mdocs_list_header();
	?>
	<div class="updated">
		<h3><?php _e('Restore Memphis Document Library\'s to Defaults','mdocs'); ?></h3>
		<p><?php _e('This will return Memphis Documents Library to its default install state.  This means that all you files, post, and categories will be remove and all setting will return to their default state. <b>Please backup your files before continuing.</b>','mdocs'); ?></p>
		<div class="mdocs-clear-both"></div>
		<form enctype="multipart/form-data" method="post" action="" class="mdocs-setting-form">
			<input type="hidden" name="mdocs-restore-default" value="clean-up" />
			<input style="margin:15px;" type="button" class="button-primary" onclick="mdocs_restore_default()" value="<?php _e('Restore To Default','mdocs') ?>" />
		</form>
	</div>
	<?php
}
?>