var toggle_share = false;
function mdocs_wp(plugin_url, wp_root) {
	//JQUERY UI TOOLTIP INIT
	jQuery('.mdocs-tooltip' ).tooltip({
		content: function () { return jQuery(this).prop('title'); },
		position: {
			my: "left-20 top+20",
			at: "center bottom",
			using: function( position, feedback ) {
				jQuery( this ).css( position );
				jQuery( "<div>" )
				.addClass( "arrow" )
				.addClass( feedback.vertical )
				.addClass( feedback.horizontal )
				.appendTo( this );
			}
		}
	});
	jQuery('.mdocs-docs-preview' ).click(function() {
		var mdocs_file_id = jQuery(this).prop('id').split('-');
		mdocs_file_id = mdocs_file_id[mdocs_file_id.length-1];
		jQuery('.mdocs-preview').empty();
		jQuery('.mdocs-preview').fadeIn();
		jQuery.post(plugin_url+'mdocs-doc-preview.php',{mdocs_file_id: mdocs_file_id, wp_root: wp_root},function(data) {
			jQuery('html, body').css('overflow-y','hidden');
			jQuery('.mdocs-preview').empty();
			jQuery('.mdocs-preview').html(data);
		});
	});
	mdocs_ratings();
}


the_rating = 0;
init_rating = false;
function mdocs_ratings() {
	jQuery('.mdocs-my-rating').click(function() {
		window.location.href = '?mdocs-rating='+this.id;
	});
	jQuery('.mdocs-my-rating').mouseover(function() {
		for (index = 1; index < 6; ++index) {
			if (this.id >= index) jQuery('#'+index).prop('class', 'icon-star gold mdocs-my-rating');
			else  jQuery('#'+index).prop('class', 'icon-star-empty mdocs-my-rating');
			
		}
	});
	
	jQuery('.mdocs-rating-container-small').mouseover(function() {
		if (init_rating == false) {
			for (index = 1; index < 6; ++index) {
				if (jQuery('#'+index).hasClass("icon-star")) {
					the_rating = index;
				}  
			}
		}
		init_rating = true;
	});
	
	jQuery('.mdocs-rating-container-small').mouseout(function() {
		for (index = 1; index < 6; ++index) {
			if (the_rating >= index) jQuery('#'+index).prop('class', 'icon-star gold mdocs-my-rating');
			else  jQuery('#'+index).prop('class', 'icon-star-empty mdocs-my-rating');
		}
	});
}

function mdocs_admin(plugin_url) {
	//JQUERY UI TOOLTIP INIT
	jQuery('.mdocs-tooltip' ).tooltip({
		content: function () { return jQuery(this).prop('title'); },
		position: {
			my: "left-20 top+20",
			at: "center bottom",
			using: function( position, feedback ) {
				jQuery( this ).css( position );
				jQuery( "<div>" )
				.addClass( "arrow" )
				.addClass( feedback.vertical )
				.addClass( feedback.horizontal )
				.appendTo( this );
			}
		}
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
		if (jQuery(this).hasClass('icon-plus-sign-alt')) {
			jQuery(this).removeClass('icon-plus-sign-alt');
			jQuery(this).addClass('icon-minus-sign-alt');
			var raw_id = jQuery(this).prop('id');
		raw_id = raw_id.split("-");
		var id = raw_id[raw_id.length-1];
		jQuery('#mdocs-social-index-'+id).show();
		} else {
			jQuery(this).removeClass('icon-minus-sign-alt');
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
	//HIDE DESCRIPTION
	jQuery('#mdocs-doc-preview').change(function() {
		if(this.checked) jQuery('#mdocs-desc-container').slideUp();
		else jQuery('#mdocs-desc-container').slideDown();
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

function mdocs_close_preview() {
	
	jQuery('.mdocs-preview').fadeOut().empty();
}

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