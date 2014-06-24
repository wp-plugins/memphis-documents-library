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
	var button_bg_color_normal = jQuery('#bg-color-mdocs-picker').prop('value');
	var button_bg_color_hover = jQuery('#bg-hover-color-mdocs-picker').prop('value');
	var button_text_color_normal = jQuery('#bg-text-color-mdocs-picker').prop('value');
	var button_text_color_hover =  jQuery('#bg-text-hover-color-mdocs-picker').prop('value');
	var color_options = {
	    change: function(event, ui) {
		var element = jQuery(this).prop('id');
		if (element == 'bg-color-mdocs-picker') {
		    button_bg_color_normal = ui.color.toString();
		    jQuery('.mdocs-download-btn-config').css('background', button_bg_color_normal);
		} else if (element == 'bg-hover-color-mdocs-picker') button_bg_color_hover = ui.color.toString();
		if (element == 'bg-text-color-mdocs-picker') {
		    button_text_color_normal = ui.color.toString();
		    jQuery('.mdocs-download-btn-config').css('color', button_text_color_normal);
		} else if (element == 'bg-text-hover-color-mdocs-picker') button_text_color_hover = ui.color.toString();
	    }
	}
	jQuery('[id$="mdocs-picker"]').wpColorPicker(color_options);
	// HOVER ADMIN DOWNLOAD BUTTON PREVIEW
	 jQuery('.mdocs-download-btn-config').hover(
	    function() {
		jQuery(this).css('background', button_bg_color_hover);
		jQuery(this).css('color', button_text_color_hover);
	    }, function() {
		jQuery(this).css('background', button_bg_color_normal);
		jQuery(this).css('color', button_text_color_normal);
	    }
	);
	// INITALIZE DRAGGABLE
	//jQuery(".draggable").draggable();
	// DISABLED SETTINGS
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
	// ADD ROOT CATEGORY
	jQuery('#mdocs-add-cat').click(function(event) {
		event.preventDefault();
		var num_main_cats = 0;
		jQuery('input[name$="[parent]"]').each(function() { if (jQuery(this).val() == '') num_main_cats++; });
		mdocs_add_sub_cat(num_main_cats, '', 0, jQuery('#the-list'), true); 
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
// ADD SUB CATEGORY
var subcat_index = 0;
var add_button_clicks = 1;
function mdocs_add_sub_cat(total_cats, parent, parent_depth, object, is_parent) {
    //mdocs_set_onleave();
    var child_depth = parseInt(parent_depth)+1;
    if (child_depth <= mdocs_js.levels) {
	jQuery('input[name="mdocs-update-cat-index"]').val(add_button_clicks++);
	var padding = 'style="padding-left: '+(40*child_depth)+'px; "';
	if (subcat_index == 0) subcat_index = parseInt(total_cats)+1;
	else subcat_index++;
	var order = parseInt(jQuery('input[name="mdocs-cats['+parent+'][num_children]"]').val())+1;
	var disabled = 'disabled';
	jQuery('input[name="mdocs-cats['+parent+'][num_children]"]').val(order);
	if (is_parent) {
	    padding = 0;
	    order = subcat_index;
	    disabled = '';
	}
	var html = '\
	    <tr>\
		<td  id="name" '+padding+' >\
		   <input type="hidden" name="mdocs-cats[mdocs-cat-'+subcat_index+'][index]" value="'+(order-1)+'"/>\
		    <input type="hidden" name="mdocs-cats[mdocs-cat-'+subcat_index+'][parent_index]" value="'+jQuery('input[name="mdocs-cats['+parent+'][index]"]').val()+'"/>\
		    <input type="hidden" name="mdocs-cats[mdocs-cat-'+subcat_index+'][num_children]" value="0" />\
		    <input type="hidden" name="mdocs-cats[mdocs-cat-'+subcat_index+'][depth]" value="'+child_depth+'"/>\
		    <input type="hidden" name="mdocs-cats[mdocs-cat-'+subcat_index+'][parent]" value="'+parent+'"/>\
		    <input type="hidden" name="mdocs-cats[mdocs-cat-'+subcat_index+'][slug]" value="mdocs-cat-'+subcat_index+'" />\
		    <input type="text" name="mdocs-cats[mdocs-cat-'+subcat_index+'][name]"  value="'+mdocs_js.new_category+'"  />\
		</td>\
		<td id="order">\
		    <input  type="text" name="mdocs-cats[mdocs-cat-'+subcat_index+'][order]"  value="'+order+'" '+disabled+' />\
		</td>\
		<td id="remove">\
			<input type="hidden" name="mdocs-cats[mdocs-cat-'+subcat_index+'][remove]" value="0"/>\
			<input type="button" id="mdocs-sub-cats-remove-new-'+subcat_index+'" class="button button-primary" value="Remove"  />\
		</td>\
		<td id="add-cat">\
		    <input type="button" class="mdocs-add-sub-cat button button-primary" id="mdocs-sub-cats-add-new-'+subcat_index+'" value="Add Category"   />\
		</td>\
	    </tr>\
	    ';
	if (jQuery(object).prop('id') == 'the-list') jQuery(object).append(html);
	else jQuery(html).insertAfter(jQuery(object).parent().parent());
	jQuery('input[id="mdocs-sub-cats-remove-new-'+subcat_index+'"]').click(function() { jQuery('input[name="mdocs-cats['+parent+'][num_children]"]').val(order-1); jQuery(this).parent().parent().remove(); });
	jQuery('input[id="mdocs-sub-cats-add-new-'+subcat_index+'"]').click(function() {
	    var id = jQuery(this).prop('id').split('-');
	    id = id[id.length-1];
	    var parent = jQuery('input[name="mdocs-cats[mdocs-cat-'+id+'][parent]"]').val();
	    var slug = jQuery('input[name="mdocs-cats[mdocs-cat-'+id+'][slug]"]').val();
	    var slug = jQuery('input[name="mdocs-cats[mdocs-cat-'+id+'][slug]"]').val();
	    var depths = jQuery('input[name="mdocs-cats[mdocs-cat-'+id+'][depth]"]').val();
	    console.debug(id,depths);
	    mdocs_add_sub_cat(subcat_index,slug, depths, this);
	});
    } else alert(mdocs_js.category_support);
    //jQuery('.mdocs-add-sub-cat').unbind('click');
   
}
function mdocs_set_onleave() { window.onbeforeunload = function() { return mdocs_js.leave_page;}; }
function mdocs_reset_onleave() { window.onbeforeunload = null; }
function mdocs_download_zip(zip_file) { window.location.href = '?mdocs-export-file='+zip_file; }
function mdocs_download_current_version(version_id) {window.location.href = '?mdocs-file='+version_id; }
//function mdocs_download_version(version_id) { window.location.href = '?mdocs-file='+version_id; }
function mdocs_download_version(version_file) { window.location.href = '?mdocs-version='+version_file; }
function mdocs_delete_version(version_file, index, category, nonce) {
	var confirm = window.confirm(mdocs_js.version_delete);
	if (confirm) {
		window.location.href = 'admin.php?page=memphis-documents.php&cat='+category+'&action=delete-version&version-file='+version_file+'&mdocs-index='+index+'&mdocs-nonce='+nonce;
	}
}
function mdocs_download_file(mdocs_file, mdocs_url) { window.location.href = '?mdocs-file='+mdocs_file+'&mdocs-url='+mdocs_url; }
function mdocs_share(mdocs_link,mdocs_direct,the_id) {
	if (toggle_share == false) {
		jQuery('#'+the_id+' .mdocs-share-link').remove();
		jQuery('#'+the_id).append('<div class="mdocs-share-link">');
		jQuery('.mdocs-share-link').append('<p class="mdocs-download-page">Download Page: '+mdocs_link+'</p>');
		jQuery('.mdocs-share-link').append('<p class="mdocs-direct-download">Direct Download: '+mdocs_direct+'</p>');
		jQuery('.mdocs-share-link').append('</div>');
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