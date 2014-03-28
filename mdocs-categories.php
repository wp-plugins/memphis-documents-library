<?php
function mdocs_edit_cats() {
	$mdocs_cats = get_option('mdocs-cats');
	//var_dump($mdocs_cats);
	?>
	<div class="mdocs-ds-container">
		<h2>Category Editor <a href="" id="mdocs-add-cat" class="mdocs-grey-btn">Add Category</a></h2>
		<form  id="mdocs-cats" method="post" action="admin.php?page=memphis-documents.php&cat=cats" >
			<input type="hidden" value="mdocs-update-cats" name="action"/>
			<table class="wp-list-table widefat plugins">
				<thead>
					<tr>
						<th scope="col" class="manage-column column-name" ><?php _e('Category'); ?></th>
						<th scope="col"  class="manage-column column-name" ><?php _e('Order'); ?></th>
						<th scope="col"  class="manage-column column-name" ><?php _e('Remove'); ?></th>
						<th scope="col" class="manage-column column-name" ><?php _e('Add Category'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="col" class="manage-column column-name" ><?php _e('Category'); ?></th>
						<th scope="col" class="manage-column column-name" ><?php _e('Order'); ?></th>
						<th scope="col" class="manage-column column-name" ><?php _e('Remove'); ?></th>
						<th scope="col" class="manage-column column-name" ><?php _e('Add Category'); ?></th>
					</tr>
				</tfoot>
				<tbody id="the-list">
			<?php
			$index = 0;
			if(!empty($mdocs_cats)) {
				array_push($mdocs_cats[0]['children'], array('slug'=>'test-fold','name'=>'SUB 1','parent'=>'documents', 'children' => array(), 'depth' => 1));
				array_push($mdocs_cats[0]['children'], array('slug'=>'test-fold2','name'=>'SUB 2','parent'=>'documents', 'children' => array(), 'depth' => 1));
				array_push($mdocs_cats[0]['children'][0]['children'], array('slug'=>'test-fold2ss','name'=>'SUB SUB 1','parent'=>'test-folder', 'children' => array(), 'depth' => 2));
				mdocs_build_cat_td($mdocs_cats);
			} else {
				?>
				<tr>
					<td class="mdocs-nofiles" colspan="3">
						<p><?php _e('No categories created.'); ?></p>
					</td>
				</tr>
			<?php 
			}
			?>
				</tbody>
			</table><br>
			<input type="submit" class="button button-primary" id="mdocs-import-submit" onclick="mdocs_reset_onleave()" value="<?php _e('Save Categories') ?>" />
		</form>
	</div>
	<?php
}

function mdocs_build_cat_td($mdocs_cat) {
	foreach($mdocs_cat as $index => $cat) {
		if($cat['depth'] > 0) $padding = 'style="padding-left: '.(40*$cat['depth']).'px; "';
		?>
		<tr>
			<td  id="name" <?php echo $padding; ?>>
				<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][depth]" value="<?php echo $cat['depth']; ?>"/>
				<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][parent]" value="<?php echo $cat['parent']; ?>"/>
				<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][slug]" value="<?php echo $cat['slug']; ?>"/>
				<input type="text" name="mdocs-cats[<?php echo $cat['slug']; ?>][name]"  value="<?php echo $cat['name']; ?>"  />
			</td>
			<td id="order">
				<input type="text" name="mdocs-cats[<?php echo $cat['slug']; ?>][order]"  value="<?php echo $index+1; ?>"  />
				
			</td>
			<td id="remove">
				<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][remove]" value="0"/>
				<input type="button" id="mdocs-cat-remove" name="<?php echo $index; ?>" class="button button-primary" value="Remove"  />
			</td>
			<td id="add-cat">
				<input  type="button" name="mdocs-cats[<?php echo $cat['slug']; ?>][<?php echo $cat['depth']; ?>][<?php echo $cat['slug']; ?>]" class="mdocs-add-sub-cat button button-primary" value="Add Category"  />
			</td>
		</tr>
		<?php
		$child = $cat['children'];
		if(count($child) > 0) mdocs_build_cat_td($child, $depth); 
	}
}

function mdocs_update_cats() {
	//$mdocs_cats = get_option('mdocs-cats');
	$upload_dir = wp_upload_dir();
	if(isset($_POST['mdocs-cats'])) {
		$mdocs_cats_post = $_POST['mdocs-cats'];
		foreach($mdocs_cats_post as $key => $value) {
			//var_dump($value['remove']);
		}
		/*
		foreach ($mdocs_cats_post as $key => $row) $sort_array[$key] = $row['order'];
		array_multisort($sort_array, SORT_ASC, $mdocs_cats_post);
		foreach($mdocs_cats_post as $key => $value) {
			if(preg_match('/new-cat-/',$value['slug'])) {
				$value['slug'] = preg_replace('/ /','-',strtolower($value['name']));
				$value['slug'] = preg_replace('/[^A-Za-z0-9\-]/', '', $value['slug']);
			}
			if($value['remove'] == 0)  $mdocs_cats[$key] = array('slug' => $value['slug'], 'name' => $value['name'], 'parent' => '', 'children' => array(), 'depth' => 0);
			else {
				$mdocs = get_option('mdocs-list');
				//$mdocs = mdocs_sort_by($mdocs);
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
				update_option('mdocs-list',$mdocs);
			}
		}
		if(is_array($mdocs_cats)) $mdocs_cats = array_values($mdocs_cats);
		update_option('mdocs-cats',$mdocs_cats);
		*/
	}
}
?>