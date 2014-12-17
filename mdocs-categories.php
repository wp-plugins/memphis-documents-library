<?php
function mdocs_edit_cats() {
	$mdocs_cats = get_option('mdocs-cats');
	//var_dump($mdocs_cats);
	//var_dump('Number of Categories: '.get_option('mdocs-num-cats'));
	//var_dump($mdocs_cats[1]['children']);
	//var_dump(get_option('mdocs-num-cats'));
	?>
	<div class="mdocs-ds-container">
		<h2>Folder Editor <a href="" id="mdocs-add-cat" onclick="add_main_category('<?php echo intval(get_option('mdocs-num-cats')); ?>')"class="mdocs-grey-btn"><?php _e('Add Main Folder','mdocs'); ?></a></h2>
		<form  id="mdocs-cats" method="post" action="admin.php?page=memphis-documents.php&cat=cats" >
			<input type="hidden" value="mdocs-update-cats" name="action"/>
			<input type="hidden" name="mdocs-update-cat-index" value="0"/>
			<table class="wp-list-table widefat plugins">
				<thead>
					<tr>
						<th scope="col" class="manage-column column-name" ><?php _e('Folder','mdocs'); ?></th>
						<th scope="col"  class="manage-column column-name" ><?php _e('Order','mdocs'); ?></th>
						<th scope="col"  class="manage-column column-name" ><?php _e('Remove','mdocs'); ?></th>
						<th scope="col" class="manage-column column-name" ><?php _e('Add Folder','mdocs'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="col" class="manage-column column-name" ><?php _e('Folder','mdocs'); ?></th>
						<th scope="col" class="manage-column column-name" ><?php _e('Order','mdocs'); ?></th>
						<th scope="col" class="manage-column column-name" ><?php _e('Remove','mdocs'); ?></th>
						<th scope="col" class="manage-column column-name" ><?php _e('Add Folder','mdocs'); ?></th>
					</tr>
				</tfoot>
				<tbody id="the-list">
			<?php
			$index = 0;
			if(!empty($mdocs_cats)) {
				$mdocs_cats = array_values($mdocs_cats);
				mdocs_build_cat_td($mdocs_cats);
			} else {
				?>
				<tr>
					<td class="mdocs-nofiles" colspan="3">
						<p><?php _e('No folders created.','mdocs'); ?></p>
					</td>
				</tr>
			<?php 
			}
			?>
				</tbody>
			</table><br>
			<input type="submit" class="button button-primary" id="mdocs-import-submit" onclick="mdocs_reset_onleave()" value="<?php _e('Save Categories','mdocs') ?>" />
		</form>
	</div>
	<?php
	if(isset($_POST['action']) && $_POST['action'] == 'mdocs-update-cats') mdocs_update_cats();
}
function mdocs_build_cat_td($mdocs_cat,$parent_index=0) {
	global $mdocs_input_text_bg_colors;
	$padding = '';
	foreach($mdocs_cat as $index => $cat) {
		$parent_id = '';
		if($cat['depth'] == 0) $parent_id = 'class="parent-cat"';
		elseif($cat['depth'] > 0) {
			$padding = 'style="padding-left: '.(40*$cat['depth']).'px; "';
		}
		$color_scheme = 'style="background: '.$mdocs_input_text_bg_colors[($cat['depth'])].'"';
		?>
		<tr <?php echo $parent_id; ?>>
			<td  id="name" <?php echo $padding; ?>>
				<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][index]" value="<?php echo $index; ?>"/>
				<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][parent_index]" value="<?php echo $parent_index; ?>"/>
				<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][num_children]" value="<?php echo count($cat['children']); ?>"/>
				<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][depth]" value="<?php echo $cat['depth']; ?>"/>
				<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][parent]" value="<?php echo $cat['parent']; ?>"/>
				<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][slug]" value="<?php echo $cat['slug']; ?>"/>
				<input <?php echo $color_scheme; ?> type="text" name="mdocs-cats[<?php echo $cat['slug']; ?>][name]"  value="<?php echo $cat['name']; ?>" />
			</td>
			<td id="order">
				<input <?php echo $color_scheme; ?> type="text" name="mdocs-cats[<?php echo $cat['slug']; ?>][order]"  value="<?php echo $index+1; ?>" <?php if($cat['parent'] != '') echo 'disabled'; ?> title="Sorry this functionality is disabled"/>
				
			</td>
			<td id="remove">
				<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][remove]" value="0"/>
				<?php if(count($cat['children']) == 0) { ?> 
				<input type="button" id="mdocs-cat-remove" name="<?php echo $cat['slug']; ?>" class="button button-primary" value="Remove"  />
				<?php } ?>
			</td>
			<td id="add-cat">
				<input  type="button" class="mdocs-add-sub-cat button button-primary" value="<?php _e('Add Folder', 'mdocs'); ?>" onclick="mdocs_add_sub_cat( '<?php echo intval(get_option('mdocs-num-cats')); ?>', '<?php echo $cat['slug']; ?>','<?php echo $cat['depth']; ?>', this);"  />
			</td>
		</tr>
		<?php
		$child = array_values($cat['children']);
		if(count($child) > 0) mdocs_build_cat_td($child,$index);
	}
}

function mdocs_update_cats() {
	$mdocs_cats = array();
	$upload_dir = wp_upload_dir();
	if(isset($_POST['mdocs-update-cat-index'])) mdocs_update_num_cats(intval($_POST['mdocs-update-cat-index']));
	if(isset($_POST['mdocs-cats'])) {
		$mdocs_cats_post = $_POST['mdocs-cats'];
		$parent_id = 0;
		$parent_ids = array();
		$depth = 0;
		$prev_depth = 0;
		foreach($mdocs_cats_post as $index => $cat) {
			$cat['index'] = intval($cat['index']);
			$cat['parent_index'] = intval($cat['parent_index']);
			$cat['depth'] = intval($cat['depth']);
			//$cat['order'] = intval($cat['order']);
			$test = '';
			$curr_depth = intval($cat['depth']);
			$depth = intval($cat['depth']);
			if($cat['parent'] == '')  {
				$parent_ids = array();
				$base_parent_id = intval($cat['order'])-1;
				$mdocs_cats[$base_parent_id] = array('base_parent'=>'','index' => $cat['index'], 'parent_index'=>$cat['parent_index'], 'slug' => $cat['slug'], 'name' => $cat['name'], 'parent' => '', 'children' => array(), 'depth' => 0);
				if($cat['remove'] == 1) unset($mdocs_cats[$base_parent_id]);
			} else {
				if($depth == 1) {
					$mdocs_cats[$base_parent_id]['children'][$cat['index']] = array('base_parent'=>$base_parent_id,'index' => $cat['index'], 'parent_index'=>$cat['parent_index'], 'slug' => $cat['slug'], 'name' => $cat['name'], 'parent' => $cat['parent'], 'children' => array(), 'depth' => 1);
					if($cat['remove'] == 1) unset($mdocs_cats[$base_parent_id]['children'][$cat['index']]);
					ksort($mdocs_cats[$base_parent_id]['children']);
					$index1 = $cat['index'];
				} elseif($depth == 2) {
					$mdocs_cats[$base_parent_id]['children'][$cat['parent_index']]['children'][$cat['index']] = array('base_parent'=>$base_parent_id,'index' => $cat['index'], 'parent_index'=>$cat['parent_index'],'slug' => $cat['slug'], 'name' => $cat['name'], 'parent' => $cat['parent'], 'children' => array(), 'depth' => 2);
					if($cat['remove'] == 1) unset($mdocs_cats[$base_parent_id]['children'][$cat['parent_index']]['children'][$cat['index']]);
					ksort($mdocs_cats[$base_parent_id]['children'][$cat['parent_index']]['children']);
					$index2 = $cat['index'];
				}
				/* Work in Progress
				} elseif($depth == 3) {
					$mdocs_cats[$base_parent_id]['children'][$index1]['children'][$cat['parent_index']]['children'][$cat['index']] = array('slug' => $cat['slug'], 'name' => $cat['name'], 'parent' => $cat['parent'], 'children' => array(), 'depth' =>3);
					if($cat['remove'] == 1) unset($mdocs_cats[$base_parent_id]['children'][$index1]['children'][$cat['parent_index']]['children'][$cat['index']]);
					ksort($mdocs_cats[$base_parent_id]['children'][$index1]['children'][$cat['parent_index']]['children']);
				} elseif($depth == 4) {
					$mdocs_cats[$base_parent_id]['children'][$base_id]['children'][$id_1]['children'][$id_2]['children'][$id_3] = array('slug' => $cat['slug'], 'name' => $cat['name'], 'parent' => $cat['parent'], 'children' => array(), 'depth' => 4);
					ksort($mdocs_cats[$base_parent_id]['children'][$base_id]['children'][$id_1]['children'][$id_2]['children']);
				}
				*/
			}
			$parent_slug = $cat['slug'];
			if($cat['remove'] == 1) mdocs_cleanup_cats($cat);
		}
		
		ksort($mdocs_cats);
		$mdocs_cats = array_values($mdocs_cats);
		update_option('mdocs-cats',$mdocs_cats);
	}
}

function mdocs_cats_loop($the_cats) {
	foreach($the_cats as $key => $cat) {
		if(count($cat['children']) > 0) {
			//ksort($the_cats[$key]['children']);
			$the_cats[$key]['children'] = array_values($the_cats[$key]['children']);
			mdocs_cats_loop($the_cats[$key]['children']);
		}
	}
	return $the_cats;
}

function mdocs_cleanup_cats($value) {
	$upload_dir = wp_upload_dir();
	$mdocs = get_option('mdocs-list');
	$mdocs_cats = get_option('mdocs-cats');
	foreach($mdocs as $k => $v) {
		if($v['cat'] == $value['slug']) {
			wp_delete_attachment( intval($v['id']), true );
			wp_delete_post( intval($v['parent']), true );
			$name = substr($v['filename'], 0, strrpos($v['filename'], '.') );
			if(file_exists($upload_dir['basedir'].'/mdocs/'.$v['filename'])) @unlink($upload_dir['basedir'].'/mdocs/'.$v['filename']);
			foreach($v['archived'] as $a) @unlink($upload_dir['basedir'].'/mdocs/'.$a);
			$thumbnails = glob($upload_dir['basedir'].'/mdocs/'.$name.'-150x55*');
			foreach($thumbnails as $t) unlink($t);
			unset($mdocs[$k]);
		}
	}
	if(isset($value['children'])) {
		if(count($value['children']) > 0) {
			foreach($value['children'] as $key) {
				mdocs_cleanup_cats($key);	
			}
		}
	}
	mdocs_save_list($mdocs);
}
?>