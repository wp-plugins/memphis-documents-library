function mdocs_admin(plugin_url) {	
	var cat_index = parseInt(jQuery('input[name$="[order]"]').last().prop('value'))+1;
	if (isNaN(cat_index)) cat_index = 1;
	jQuery('#mdocs-add-cat').click(function(event) {
		mdocs_set_onleave();
		event.preventDefault();
		jQuery('.mdocs-nofiles').parent().remove();
		jQuery('#the-list').append('\
			<tr id="mdocs-cats-new-'+cat_index+'">\
				<td id="name">\
					<input type="hidden" name="mdocs-cats[new-cat-'+cat_index+'][slug]" value="new-cat-'+cat_index+'"/>\
					<input type="text" name="mdocs-cats[new-cat-'+cat_index+'][name]"  value="'+mdocs_js.new_category+' '+cat_index+'"  />\
				</td>\
				<td id="order">\
					<input type="text" name="mdocs-cats[new-cat-'+cat_index+'][order]"  value="'+cat_index+'"  />\
				</td>\
				<td id="remove">\
					<input type="hidden" name="mdocs-cats[new-cat-'+cat_index+'][remove]" value="0"/>\
					<input type="button"  id="mdocs-cat-remove-new" name="new-cat-'+cat_index+'" class="button button-primary" value="'+mdocs_js.remove+'"  />\
				</td>\
			</tr>\
		');
		cat_index++;
		jQuery('input[id="mdocs-cat-remove-new"]').click(function() {
			jQuery(this).parent().parent().remove();
		});
	});
	jQuery('input[id="mdocs-cat-remove"]').click(function() {
		var confirm = window.confirm(mdocs_js.category_delete);
		var cat = jQuery(this).prop('name');
		if (confirm) {
			jQuery('[name="mdocs-cats['+cat+'][remove]"]').prop('value',1);
			jQuery('#mdocs-cats').submit();
		}
		
	});
}
function mdocs_set_onleave() { window.onbeforeunload = function() { return mdocs_js.leave_page;}; }
function mdocs_reset_onleave() { window.onbeforeunload = null; }
function mdocs_download_zip(zip_file) { window.location.href = '?mdocs-export-file='+zip_file; }
function mdocs_download_version(version_file) { window.location.href = '?mdocs-version='+version_file; }
function mdocs_delete_version(version_file, index, category, nonce) {
	var confirm = window.confirm(mdocs_js.version_delete);
	if (confirm) {
		window.location.href = 'admin.php?page=memphis-documents.php&cat='+category+'&action=delete-version&version-file='+version_file+'&mdocs-index='+index+'&mdocs-nonce='+nonce;
	}
}
function mdocs_download_file(mdocs_file) { window.location.href = '?mdocs-file='+mdocs_file; }
function mdocs_share(mdocs_link,the_id) {
	jQuery('#'+the_id+' .mdocs-share-link').remove();
	jQuery('#'+the_id).append('<div class="mdocs-share-link">'+mdocs_link+"</div>");
}