<?php
function mdocs_allowed_file_types() {
?>
<?php mdocs_list_header(); ?>
<h2><?php _e('Allow File Types','mdocs'); ?></h2>
<table class="table form-table">
	<tr>
		<th><?php _e('Allowed File Types','mdocs'); ?></th>
		<?php
		$mimes = get_allowed_mime_types();
		?>
		<td>
			<table class="mdocs-mime-table">
				<tr>
					<th><?php _e('Extension','mdocs'); ?></th>
					<th><?php _e('Mime Type','mdocs'); ?></th>
					<th><?php _e('Options','mdocs'); ?></th>
				</tr>
					<?php
					foreach($mimes as $index => $mime) {
						echo '<tr data-file-type="'.$index.'" ><td>'.$index.'</td><td>'.$mime.'</td>';
						echo '<td><a href="#" class="mdocs-remove-mime">'.__('remove','mdocs').'</a></td>';
						echo '</tr>';
					}
					?>
				<tr class="mdocs-mime-submit">
					<td><input type="text" placeholder="Enter File Type..." name="mdocs-file-extension" value=""/></td>
					<td><input type="text" placeholder="Enter Mime Type..." name="mdocs-mime-type" value=""/></td>
					<td><a href="#" id="mdocs-add-mime"><?php _e('add','mdocs'); ?></a></td>
				</tr>
			</table>
			<a href="http://www.freeformatter.com/mime-types-list.html#mime-types-list" alt="<?php _e('List of Files and Their Mime Types','mdocs'); ?>" target="_blank"><?php _e('List of Files and Their Mime Types','mdocs'); ?></a><br>
			<a href="#" id="mdocs-restore-default-file-types" alt="<?php _e('Restore Default File Types','mdocs'); ?>"><?php _e('Restore Default File Types','mdocs'); ?></a>
		</td>
	</tr>
</table>
<?php
}
?>