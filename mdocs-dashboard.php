<?php
function mdocs_dashboard_menu() {
	global $add_error;
	//MEMPHIS CUSTOM LOGIN INTEGRATION 3.0 AND HIGHER
	$plugin_path = preg_replace('/memphis-documents-library/','',dirname(__FILE__));
	$memphis_custom_login = (get_plugin_data($plugin_path.'memphis-wordpress-custom-login/memphis-wp-login.php'));
	$memphis_version = intval($memphis_custom_login['Version']);
	If (!is_plugin_active('memphis-wordpress-custom-login/memphis-wp-login.php') || $memphis_version < 3) {
		add_menu_page( __('Memphis Documents Library'), __('Memphis Docs'), 'administrator', 'memphis-documents.php', 'mdocs_dashboard', MDOC_URL.'/assets/imgs/kon.ico'  );
	} 
	add_action('admin_init','mdocs_register_settings');
	add_action('admin_enqueue_scripts', 'mdocs_admin_script');
	// ERRORS AND UPDATES
	if(isset($_FILES['mdocs']) && $_FILES['mdocs']['name'] == '' && $_POST['mdocs-type'] == 'mdocs-add')  { mdocs_errors(MDOCS_ERROR_1,'error'); $add_error = true; }	
}

function mdocs_dashboard() {
	global $add_error;
	//update_option('mdocs-list',array());
	//$list = get_option('mdocs-list');
	//var_dump($list);
	//$cats = get_option('mdocs-cats');
	//var_dump($cats);
	if(isset($_FILES['mdocs']) && $_FILES['mdocs']['name'] != '' && $_POST['mdocs-type'] == 'mdocs-add') mdocs_file_upload();
	if(isset($_FILES['mdocs']) && $_POST['mdocs-type'] == 'mdocs-update') mdocs_file_upload();
	elseif(isset($_GET['action']) && $_GET['action'] == 'add-doc' && MDOCS_NONCE == $_SESSION['mdocs-nonce']) mdocs_uploader(__('Add Document'));
	elseif(isset($_GET['action']) && $_GET['action'] == 'update-doc') mdocs_uploader(__('Update Document'));
	elseif(isset($_GET['action']) && $_GET['action'] == 'delete-doc') mdocs_delete();
	elseif(isset($_GET['action']) && $_GET['action'] == 'delete-version') mdocs_delete_version();
	elseif(isset($_POST['action']) && $_POST['action'] == 'mdocs-import') mdocs_import_zip();
	elseif(isset($_POST['action']) && $_POST['action'] == 'mdocs-update-revision') mdocs_update_revision();
	elseif(isset($_GET['action']) && $_GET['action'] == 'mdocs-versions') mdocs_versions();
	elseif(isset($_POST['action']) && $_POST['action'] == 'mdocs-update-cats') mdocs_update_cats();
	mdocs_dashboard_view();
}

function mdocs_dashboard_view() {
	$cats = get_option('mdocs-cats');
	if(isset($_GET['cat'])) $current_cat = $_GET['cat'];
	elseif(!is_string($cats)) $current_cat = key($cats);
	?>
	<script type="application/x-javascript">
		jQuery( document ).ready(function() {
			mdocs_admin('<?php echo MDOC_URL; ?>');
		});	
	</script>
	<div class="wrap">
		<?php if($message != "" && $type != 'update') { ?> <div id="message" class="error" ><p><?php _e($message); ?></p></div> <?php }?>
		<div id="icon-mdocs" class="icon32"><br></div><h2><?php _e("Documents Library"); ?> <a href="?page=memphis-documents.php&cat=<?php echo $current_cat; ?>&action=add-doc" class="mdocs-grey-btn">Add New Document</a> <a href="?page=memphis-documents.php&cat=cats" class="mdocs-grey-btn">Edit Categories</a></h2>
	
		<div id="icon-edit-pages" class="icon32"><br></div>
		<h2 class="nav-tab-wrapper">
		<?php
		if(!empty($cats)) {
			foreach( $cats as $cat => $name ){
				$class = ( $cat == $current_cat ) ? ' nav-tab-active' : '';
				echo "<a class='nav-tab$class' href='?page=memphis-documents.php&cat=$cat'>".__($name)."</a>";
			}
		}
		if($current_cat == 'import') $import_active = ' nav-tab-active';
		echo "<a class='nav-tab$import_active' href='?page=memphis-documents.php&cat=import'>".__('Import')."</a>";
		if($current_cat == 'export') $export_active = ' nav-tab-active';
		echo "<a class='nav-tab$export_active' href='?page=memphis-documents.php&cat=export'>".__('Export')."</a>";
		?>
	   </h2>
		<?php
		if($current_cat == 'import') mdocs_import($current_cat);
		elseif($current_cat == 'export') mdocs_export($current_cat);
		elseif($current_cat == 'cats') mdocs_edit_cats($current_cat);
		else mdoc_doc_list($current_cat);
}

function mdoc_doc_list($current_cat) {
	$mdocs = get_option('mdocs-list');
	$upload_dir = wp_upload_dir();	
	$bgcolor = "active";
	$count = 0;
	?>
	<div>	
		<table class="wp-list-table widefat plugins" >
			<thead>
				<tr>
					<th scope="col" id="name" class="manage-column column-name" style=""><?php _e('Document'); ?></th>
					<th scope="col" id="description" class="manage-column column-description" style=""><?php _e('Description'); ?></th>	</tr>
			</thead>
			<tfoot>
				<tr>
					<th scope="col" class="manage-column column-name" style=""><?php _e('Document'); ?></th>
					<th scope="col" class="manage-column column-description" style=""><?php _e('Description'); ?></th>
				</tr>
			</tfoot><br>
			<tbody id="the-list">
	<?php	
	foreach($mdocs as $index => $value) {
		if($mdocs[$index]['cat'] == $current_cat) {
			$count++;
			$mdocs_post = get_post($mdocs[$index]['parent']);
			//$mdocs_desc = apply_filters('the_content', $content);
			$mdocs_desc = $mdocs_post->post_excerpt;
			//$mdocs_desc = apply_filters('the_content', $mdocs_desc);
			?>
			<tr id="<?php echo $mdocs[$index]['id']; ?>" class="<?php echo $bgcolor; ?>">
				<td class="plugin-title">
					<strong><?php echo $mdocs[$index]['name']; ?> </strong>
					<strong><?php echo  strtoupper($mdocs[$index]['type']); ?> </strong>
						<div class="row-actions-visible">
							<span class="download"><a href="<?php echo site_url().'/?mdocs-file='.$mdocs[$index]['id']; ?>" title="<?php _e('Download this file'); ?>" class="edit"><?php _e('Download'); ?></a> | </span>
							<span class="update" id="<?php echo $index ?>">
								<a href="<?php echo 'admin.php?page=memphis-documents.php&cat='.$current_cat.'&action=update-doc&mdocs-index='.$index; ?>" title="Update this file" class="edit"><?php _e('Update'); ?></a> |
							</span>
							<span class='delete'>
								<a class='submitdelete' onclick="return showNotice.warn();" href="<?php echo 'admin.php?mdocs-nonce='.$_SESSION['mdocs-nonce'].'&page=memphis-documents.php&cat='.$current_cat.'&action=delete-doc&mdocs-index='.$index; ?>"><?php _e('Delete'); ?></a>
							|
							</span> 
							<span class="versions"><a href="<?php echo 'admin.php?page=memphis-documents.php&cat='.$current_cat.'&mdocs-index='.$index; ?>&action=mdocs-versions" title="<?php _e('Versions'); ?>" class="edit"><?php _e('Versions'); ?></a></span>
						</div>
				</td>
				<td class="mdocs-admin-desc">
					<p><?php echo $mdocs_desc; ?> </p>
					<div class="<?php echo $bgcolor; ?>">
						<div class="mdocs-admin-share"><i class="icon-share-sign"></i> <?php _e('Share');?>: <b class="mdocs-blue"><?php echo site_url().'/?mdocs-file='.$mdocs[$index]['id']; ?></b></div>
						<p><i class="icon-cloud-download"></i> <?php _e('Downloads');?>: <b class="mdocs-orange"><?php echo $mdocs[$index]['downloads']; ?></b></p>
						<p><i class="icon-pencil"></i> <?php _e('Author');?>: <i class="mdocs-green"><?php echo $mdocs[$index]['owner']; ?></i></p>
						<p><i class="icon-off"></i> <?php _e('Version');?>: <b class="mdocs-blue"><?php echo $mdocs[$index]['version']; ?></b></p>
						<p><i class="icon-calendar"></i> <?php _e('Last Updated');?>: <b class="mdocs-red"><?php  echo gmdate('F jS Y \a\t g:i A',filemtime($upload_dir['basedir'].'/mdocs/'.$mdocs[$index]['filename'])+MDOCS_TIME_OFFSET); ?></b><br/></p>
					</div>
				</td>
			</tr>
			<tr>
				<td class="mdocs-td-social" colspan="2">
					<div class="mdocs-social">
						<input type="button" onclick="mdocs_download_file('<?php echo $mdocs[$index]['id']; ?>');" class="mdocs-download-btn small right" value="<?php echo __('Download');  ?>"></h2>
						<div class="mdocs-tweet"><a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo site_url().'/?mdocs-file='.$mdocs[$index]['id'];?>" data-counturl="<?php echo site_url().'/?mdocs-file='.$mdocs[$index]['id'];?>" width="50">Tweet</a></div>
						<div class="mdocs-like"><iframe src="//www.facebook.com/plugins/like.php?href=<?php echo site_url().'/?mdocs-file='.$mdocs[$index]['id'];?>&amp;width=450&amp;height=21&amp;colorscheme=light&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;send=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe></div>
						<div class="mdocs-plusone"><div class="g-plusone" data-size="medium" data-href="<?php echo site_url().'/?mdocs-file='.$mdocs[$index]['id'];?>"</div></div>
					</div>
				</td>
			</tr>
			<?php
			if($bgcolor == "active") $bgcolor = "inactive";
			else $bgcolor = "active"; } }
			
			if($count == 0) { ?>
				<tr>
					<td class="mdocs-nofiles" colspan="2">
						<p><?php _e('No files found in this category.'); ?></p>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<?php
}

function mdocs_delete() {
	if ( $_REQUEST['mdocs-nonce'] == MDOCS_NONCE ) {
		$mdocs = get_option('mdocs-list');
		$index = $_GET['mdocs-index'];
		$upload_dir = wp_upload_dir();
		$mdocs_file = $mdocs[$index];
		$mdocs_post_cat = get_category_by_slug( 'mdocs-media' );
		foreach($mdocs[$index]['archived'] as $key => $value) @unlink($upload_dir['basedir'].'/mdocs/'.$value);
		wp_delete_attachment( intval($mdocs_file['id']), true );
		wp_delete_post( intval($mdocs_file['parent']), true );
		if(file_exists($upload_dir['basedir'].'/mdocs/'.$mdocs_file['filename'])) @unlink($upload_dir['basedir'].'/mdocs/'.$mdocs_file['filename']);
		unset($mdocs[$index]);
		$mdocs = array_values($mdocs);
		update_option('mdocs-list', $mdocs);
	} else mdocs_errors(MDOCS_ERROR_4,'error');
}

function mdocs_uploader($edit_type='Add Document') {
	$cats = get_option('mdocs-cats');
	$mdocs = get_option('mdocs-list');
	$mdoc_index = $_GET['mdocs-index'];
	if(isset($_GET['cat'])) $current_cat = $_GET['cat'];
	else $current_cat = $current_cat = key($cats);
	if($edit_type == 'Update Document') $mdoc_type = 'mdocs-update';
	else $mdoc_type = 'mdocs-add';
	$mdocs_post = get_post($mdocs[$mdoc_index]['parent']);
	$mdocs_desc = apply_filters('the_content', $content);
	$mdocs_desc = $mdocs_post->post_excerpt;	
?>
<div class="mdocs-uploader-bg"></div>
<div class="mdocs-uploader">
	<h2 class="mdocs-uploader-header">
		<div class="close"><a href="<?php echo 'admin.php?page=memphis-documents.php&cat='.$current_cat; ?>"><img src='<?php echo MDOC_URL; ?>/assets/imgs/close.png'/></a></div>
		<?php _e($edit_type); ?>
	</h2>
	<div class="mdocs-uploader-content">
		<form class="mdocs-uploader-form" enctype="multipart/form-data" action="<?php echo $_REQUEST['REQUEST_URI']; ?>" method="POST">
			<input type="hidden" name="mdocs-type" value="<?php echo $mdoc_type; ?>" />
			<input type="hidden" name="mdocs-index" value="<?php echo $mdoc_index; ?>" />
			<input type="hidden" name="mdocs-cat" value="<?php echo $current_cat; ?>" />
			<input type="hidden" name="mdocs-pname" value="<?php echo $mdocs[$mdoc_index]['name']; ?>" />
			<input type="hidden" name="mdocs-nonce" value="<?php echo MDOCS_NONCE; ?>" />
			<h3><?php _e('File Uploader'); ?></h3>
			<input type="file" name="mdocs" />
			<p><?php if($edit_type=='Update Document') echo __('Current File').': '.$mdocs[$mdoc_index]['filename']; ?></p>
			<h3 for=""><?php _e('File Name'); ?></h3>
			<input type="text" name="mdocs-name" <?php if($edit_type=='Update Document') echo 'value="'.$mdocs[$mdoc_index]['name'].'"'; ?> />
			<h3 for=""><?php _e('Category'); ?></h3>
			<select name="mdocs-cat">
			<?php
				foreach( $cats as $select => $name ){ 
					$is_selected = ( $select == $current_cat ) ? 'selected="selected"' : '';
					echo '<option  value="'.$select.'" '.$is_selected.'>'.$name.'</option>';
				}
			?>
			</select>
			<h3><?php _e('Version'); ?></h3>
			<input type="text" name="mdocs-version" <?php if($edit_type=='Update Document') echo 'value="'.$mdocs[$mdoc_index]['version'].'"'; else echo 'value="1.0"'; ?> />
			<h3><?php _e('Description'); ?></h3>
			<?php wp_editor($mdocs_desc, "mdocs-desc", array('media_buttons'=>false)); ?><br>
			<?php if($edit_type=='Update Document') { ?>
				<input type="submit" class="button button-primary" value="<?php _e('Update Document') ?>" /><br/>
			<?php } else { ?> <input type="submit" class="button button-primary" value="<?php _e('Add Document') ?>" /><br/> <?php } ?>
		</form>
	</div>
</div>
<?php
}
?>