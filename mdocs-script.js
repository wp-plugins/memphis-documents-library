var toggle_share = false;
the_rating = 0;
init_rating = false;
// INITIALIZE THE ADMIN JAVASCRIPT
function mdocs_wp(plugin_url, wp_root) {
	//JQUERY UI TOOLTIP INIT
	jQuery('[id^="file-desc-"]').click(function() {
		jQuery(this).tooltip({
			items: this,
			content: function () { return jQuery(this).prop('title'); },
		});
		jQuery(this).tooltip("open");
		jQuery( this ).unbind( "mouseleave" );
	});
	// TOOGLE DESCRIPTION/PREVIEW
	jQuery('[id^="mdoc-show-desc-"], [id^="mdoc-show-preview-"]' ).click(function(e) {
		e.preventDefault();
		var exploded = jQuery(this).prop('id').split('-');
		var mdocs_file_id = exploded[exploded.length-1];
		var show_type = exploded[exploded.length-2];
		jQuery.post(plugin_url+'mdocs-doc-preview.php',{type:'show', show_type:show_type,mdocs_file_id: mdocs_file_id, wp_root: wp_root},function(data) {
			jQuery('#mdocs-show-container-'+mdocs_file_id).empty();
			jQuery('#mdocs-show-container-'+mdocs_file_id).html(data);
		});
	});
	// FILE PREVIEW
	jQuery('[id^="file-preview-"]' ).click(function() {
		var mdocs_file_id = jQuery(this).prop('id').split('-');
		mdocs_file_id = mdocs_file_id[mdocs_file_id.length-1];
		jQuery('.mdocs-wp-preview').empty();
		jQuery('.mdocs-wp-preview').fadeIn();
		jQuery.post(plugin_url+'mdocs-doc-preview.php',{type: 'file',mdocs_file_id: mdocs_file_id, wp_root: wp_root},function(data) {
			jQuery('html, body').css('overflow-y','hidden');
			jQuery('.mdocs-wp-preview').empty();
			jQuery('.mdocs-wp-preview').html(data);
		});
	});
	// IMAGE PREVIEW
	jQuery('[id^="img-preview-"]' ).click(function() {
		var mdocs_file_id = jQuery(this).prop('id').split('-');
		mdocs_file_id = mdocs_file_id[mdocs_file_id.length-1];
		jQuery('.mdocs-wp-preview').empty();
		jQuery('.mdocs-wp-preview').fadeIn();
		jQuery.post(plugin_url+'mdocs-doc-preview.php',{type: 'img',mdocs_file_id: mdocs_file_id, wp_root: wp_root, is_admin: true},function(data) {
			jQuery('html, body').css('overflow-y','hidden');
			jQuery('.mdocs-wp-preview').empty();
			jQuery('.mdocs-wp-preview').html(data);
		});
	});
	mdocs_ratings();
}
// INITIALIZE THE ADMIN JAVASCRIPT
function mdocs_admin(plugin_url, wp_root) {
	//JQUERY UI TOOLTIP INIT
	jQuery('[id^="file-desc-"]').click(function() {
		jQuery(this).tooltip({
			items: this,
			content: function () { return jQuery(this).prop('title'); },
		});
		jQuery(this).tooltip("open");
		jQuery( this ).unbind( "mouseleave" );
	});
	//INITIALIZE IRIS COLOR PICKER
	var color_options = {
		change: function(event, ui) {
			jQuery('.mdocs-download-btn-config').css("background-color", ui.color.toString());
		}
	}
	jQuery('.mdocs-color-picker').wpColorPicker(color_options);
	
	mdocs_toogle_disable_setting('#mdocs-hide-all-files','#mdocs-hide-all-files-non-members');
	mdocs_toogle_disable_setting('#mdocs-hide-all-files-non-members','#mdocs-hide-all-files');
	mdocs_toogle_disable_setting('#mdocs-hide-all-posts','#mdocs-hide-all-posts-non-members');
	mdocs_toogle_disable_setting('#mdocs-hide-all-posts-non-members','#mdocs-hide-all-posts');
	
	// TOOGLE DESCRIPTION/PREVIEW
	jQuery('[id^="mdoc-show-desc-"], [id^="mdoc-show-preview-"]' ).click(function(e) {
		e.preventDefault();
		var exploded = jQuery(this).prop('id').split('-');
		var mdocs_file_id = exploded[exploded.length-1];
		var show_type = exploded[exploded.length-2];
		jQuery.post(plugin_url+'mdocs-doc-preview.php',{type:'show', show_type:show_type,mdocs_file_id: mdocs_file_id, wp_root: wp_root},function(data) {
			jQuery('#mdocs-show-container-'+mdocs_file_id).empty();
			jQuery('#mdocs-show-container-'+mdocs_file_id).html(data);
		});
	});
	// FILE PREVIEW
	jQuery('[id^="file-preview-"]' ).click(function() {
		var mdocs_file_id = jQuery(this).prop('id').split('-');
		mdocs_file_id = mdocs_file_id[mdocs_file_id.length-1];
		jQuery('.mdocs-admin-preview').empty();
		jQuery('.mdocs-admin-preview').fadeIn();
		jQuery.post(plugin_url+'mdocs-doc-preview.php',{type: 'file', mdocs_file_id: mdocs_file_id, wp_root: wp_root, is_admin: true},function(data) {
			jQuery('html, body').css('overflow-y','hidden');
			jQuery('.mdocs-admin-preview').empty();
			jQuery('.mdocs-admin-preview').html(data);
		});
	});
	// IMAGE PREVIEW
	jQuery('[id^="img-preview-"]' ).click(function() {
		var mdocs_file_id = jQuery(this).prop('id').split('-');
		mdocs_file_id = mdocs_file_id[mdocs_file_id.length-1];
		jQuery('.mdocs-admin-preview').empty();
		jQuery('.mdocs-admin-preview').fadeIn();
		jQuery.post(plugin_url+'mdocs-doc-preview.php',{type: 'img',mdocs_file_id: mdocs_file_id, wp_root: wp_root, is_admin: true},function(data) {
			jQuery('html, body').css('overflow-y','hidden');
			jQuery('.mdocs-admin-preview').empty();
			jQuery('.mdocs-admin-preview').html(data);
		});
	});
	
	/*
	jQuery('#download-normal').iris({
		hide: false,
		toggle: true,
		change: function(event, ui) {
			// event = standard jQuery event, produced by whichever control was changed.
			// ui = standard jQuery UI object, with a color member containing a Color.js object
	
			// change the headline color
			jQuery('#help').css( 'color', ui.color.toString());
		}
	});
	*/
	jQuery('.mdocs-show-social').click(function() {
		if (jQuery(this).hasClass('fa fa-plus-sign-alt')) {
			jQuery(this).removeClass('fa fa-plus-sign-alt');
			jQuery(this).addClass('fa fa-minus-sign-alt');
			var raw_id = jQuery(this).prop('id');
		raw_id = raw_id.split("-");
		var id = raw_id[raw_id.length-1];
		jQuery('#mdocs-social-index-'+id).show();
		} else {
			jQuery(this).removeClass('fa fa-minus-sign-alt');
			jQuery(this).addClass('icon-plus-sign-alt');
			var raw_id = jQuery(this).prop('id');
		raw_id = raw_id.split("-");
		var id = raw_id[raw_id.length-1];
		jQuery('#mdocs-social-index-'+id).hide();
		}
		
	});
	
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
	jQuery('#mdocs-file-status').change(function() {
		if (jQuery(this).val() == 'hidden') jQuery('#mdocs-post-status').prop('disabled','true');
		else if (jQuery(this).val() == 'public') jQuery('#mdocs-post-status').removeAttr('disabled');
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
function mdocs_download_file(mdocs_file, mdocs_url) { window.location.href = '?mdocs-file='+mdocs_file+'&mdocs-url='+mdocs_url; }
function mdocs_share(mdocs_link,the_id) {
	if (toggle_share == false) {
		jQuery('#'+the_id+' .mdocs-share-link').remove();
		jQuery('#'+the_id).append('<div class="mdocs-share-link">'+mdocs_link+"</div>");
		toggle_share = true;
	} else {
		jQuery('#'+the_id+' .mdocs-share-link').remove();
		toggle_share = false;
	}
}

function mdocs_close_preview(type) { window.location.href = '';}

function mdocs_toogle_disable_setting(main, disable) {
	var checked = jQuery(main).prop('checked');
	if (checked) jQuery(disable).prop('disabled', true);
	else jQuery(disable).prop('disabled', false);
	jQuery(main).click(function() {
		var checked = jQuery(this).prop('checked');
		if (checked) jQuery(disable).prop('disabled', true);
		else jQuery(disable).prop('disabled', false);
	});
}
// RATINGS
function mdocs_ratings() {
	jQuery('.mdocs-my-rating').click(function() {
		window.location.href = '?mdocs-rating='+this.id;
	});
	jQuery('.mdocs-my-rating').mouseover(function() {
		for (index = 1; index < 6; ++index) {
			if (this.id >= index) jQuery('#'+index).prop('class', 'fa fa-star mdocs-gold mdocs-my-rating');
			else  jQuery('#'+index).prop('class', 'fa fa-star-o mdocs-my-rating');
			
		}
	});
	
	jQuery('.mdocs-rating-container-small').mouseover(function() {
		if (init_rating == false) {
			for (index = 1; index < 6; ++index) {
				if (jQuery('#'+index).hasClass("fa fa-star")) {
					the_rating = index;
				}  
			}
		}
		init_rating = true;
	});
	
	jQuery('.mdocs-rating-container-small').mouseout(function() {
		for (index = 1; index < 6; ++index) {
			if (the_rating >= index) jQuery('#'+index).prop('class', 'fa fa-star mdocs-gold mdocs-my-rating');
			else  jQuery('#'+index).prop('class', 'fa fa-star-o mdocs-my-rating');
		}
	});
}