<?php
function mdocs_shortcodes($current_cat) {
	?>
	<br>
	<div class="updated">
		<h3>Short Codes</h3>
		<table class="mdocs-shortcode-table" >
			<tr>
				<th><?php _e('Short Codes','mdocs');?></th>
				<th><?php _e('Description','mdocs');?></th>
			</tr>
			<tr>
				<td>[mdocs]</td>
				<td><?php _e('Adds the default Memphis Documents Library file list to any page, post or widget.','mdocs');?></td>
			</tr>
			<tr>
				<td>[mdocs cat="<?php _e('The Category Name','mdocs');?>"]</td>
				<td><?php _e('Adds files from  a specific main folder of the Memphis Documents Library on any page, post or widget.','mdocs');?></td>
			</tr>
			<tr>
				<td>[mdocs cat="All Files"]</td>
				<td><?php _e('Adds a list of all files of the Memphis Documents Library on any page, post or widget.','mdocs');?></td>
			</tr>
			<tr>
				<td>[mdocs header="<?php _e('This text will show up above the documents list.','mdocs'); ?>"]</td>
				<td><?php _e('Adds a header to the Memphis Documents LIbrary on ay page, post or widget.','mdocs');?></td>
			</tr>
		</table>
	</div>
	<?php
}
?>