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
				/*
				array_push($mdocs_cats[0]['children'], array('slug'=>'test-fold','name'=>'SUB 1','parent'=>'documents', 'children' => array(), 'depth' => 1));
				array_push($mdocs_cats[0]['children'], array('slug'=>'test-fold1-2','name'=>'SUB 1-2','parent'=>'documents', 'children' => array(), 'depth' => 1));
				array_push($mdocs_cats[1]['children'], array('slug'=>'test-fold2','name'=>'SUB 2','parent'=>'new-category-2', 'children' => array(), 'depth' => 1));
				
				array_push($mdocs_cats[0]['children'][0]['children'], array('slug'=>'test-fold-sub1','name'=>'SUB SUB 1','parent'=>'test-folder', 'children' => array(), 'depth' => 2));
				array_push($mdocs_cats[0]['children'][1]['children'], array('slug'=>'test-fold-sub1-2','name'=>'SUB SUB 1-2','parent'=>'test-fold1-2', 'children' => array(), 'depth' => 2));
				array_push($mdocs_cats[1]['children'][0]['children'], array('slug'=>'test-fold2-sub2','name'=>'SUB SUB 2','parent'=>'test-folder2', 'children' => array(), 'depth' => 2));
				
				array_push($mdocs_cats[0]['children'][0]['children'][0]['children'], array('slug'=>'test-fold-sub3-1','name'=>'SUB SUB SUB 1','parent'=>'test-fold-sub1', 'children' => array(), 'depth' => 3));
				array_push($mdocs_cats[0]['children'][0]['children'][0]['children'], array('slug'=>'test-fold-sub3-2','name'=>'SUB SUB SUB 2','parent'=>'test-fold-sub1', 'children' => array(), 'depth' => 3));
				array_push($mdocs_cats[0]['children'][0]['children'][0]['children'], array('slug'=>'test-fold-sub3-3','name'=>'SUB SUB SUB 3','parent'=>'test-fold-sub1', 'children' => array(), 'depth' => 3));
				//var_dump($mdocs_cats);
				*/
				$mdocs_cats = array_values($mdocs_cats);
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
	if(isset($_POST['action']) && $_POST['action'] == 'mdocs-update-cats') mdocs_update_cats();
}

function mdocs_build_cat_td($mdocs_cat) {
	global $mdocs_input_text_bg_colors;
	foreach($mdocs_cat as $index => $cat) {
		if($cat['depth'] > 0) {
			$padding = 'style="padding-left: '.(40*$cat['depth']).'px; "';
			
		}
		$color_scheme = 'style="background: '.$mdocs_input_text_bg_colors[($cat['depth'])].'"';
		?>
		<tr>
			<td  id="name" <?php echo $padding; ?>>
				<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][depth]" value="<?php echo $cat['depth']; ?>"/>
				<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][parent]" value="<?php echo $cat['parent']; ?>"/>
				<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][slug]" value="<?php echo $cat['slug']; ?>"/>
				<input <?php echo $color_scheme; ?> type="text" name="mdocs-cats[<?php echo $cat['slug']; ?>][name]"  value="<?php echo $cat['name']; ?>"  />
			</td>
			<td id="order">
				<input <?php echo $color_scheme; ?> type="text" name="mdocs-cats[<?php echo $cat['slug']; ?>][order]"  value="<?php echo $index+1; ?>"  />
				
			</td>
			<td id="remove">
				<input type="hidden" name="mdocs-cats[<?php echo $cat['slug']; ?>][remove]" value="0"/>
				<?php if(count($cat['children']) == 0) { ?> 
				<input type="button" id="mdocs-cat-remove" name="<?php echo $cat['slug']; ?>" class="button button-primary" value="Remove"  />
				<?php } ?>
			</td>
			<td id="add-cat">
				<input  type="button" name="mdocs-cats[<?php echo $cat['slug']; ?>][<?php echo $cat['depth']; ?>][<?php echo $cat['slug']; ?>]" class="mdocs-add-sub-cat button button-primary" value="Add Category"  />
			</td>
		</tr>
		<?php
		$child = array_values($cat['children']);
		if(count($child) > 0) mdocs_build_cat_td($child); 
	}
}

function mdocs_update_cats() {
	$mdocs_cats = array();
	$upload_dir = wp_upload_dir();
	if(isset($_POST['mdocs-cats'])) {
		$mdocs_cats_post = $_POST['mdocs-cats'];
		//var_dump($mdocs_cats_post);	
		$parent_id = 0;
		$parent_ids = array();
		foreach($mdocs_cats_post as $index => $cat) {
			$test = '';
			if(preg_match('/new-cat-/',$cat['slug'])) {
				$cat['slug'] = preg_replace('/ /','-',strtolower($cat['name']));
				$cat['slug'] = preg_replace('/[^A-Za-z0-9\-]/', '', $cat['slug']);
			}
			//if($cat['remove'] == 0)  {
				$curr_depth = intval($cat['depth']);
				$depth = intval($cat['depth']);
				if($cat['parent'] == '')  {
					$parent_ids = array();
					$base_parent_id = intval($cat['order'])-1;
					$mdocs_cats[$base_parent_id] = array('slug' => $cat['slug'], 'name' => $cat['name'], 'parent' => '', 'children' => array(), 'depth' => 0);
					if($cat['remove'] == 1) unset($mdocs_cats[$base_parent_id]);
				} else {
					$child_order = intval($cat['order'])-1;
					array_push($parent_ids,$child_order);
					//$test = '$mdocs_cats['.$base_parent_id.']["children"]';
					if($prev_depth > $curr_depth) {
						for($i=$prev_depth;$i>0; $i--) unset($parent_ids[$i-1]);
					} elseif($prev_depth == $curr_depth) unset($parent_ids[$curr_depth-1]);
					$parent_ids = array_values($parent_ids);
					for($i=0;$i<intval($cat['depth']); $i++) {
						if($i == intval($cat['depth'])-1)	{
							//$test .= '['.$child_order.']'.'["children"]';
							$test .= $child_order;
						} else {
							//$test .= '['.$parent_ids[$i].']'.'["children"]';
							$test .= $parent_ids[$i].',';
						}
					}		
					$prev_depth =$curr_depth;
					//$test .= ' ==> '.$cat['name'];	
					$parent_id = $child_order;
					//var_dump($parent_ids);
					$output = explode(',',$test);
					//var_dump($output);
					$base_array = array();
					foreach($output as $index => $op) {
						if($index == 0) $base_id = $op;
						elseif($index == 1) $id_1 = $op;
						elseif($index == 2) $id_2 = $op;
						elseif($index == 3) $id_3 = $op;
						elseif($index == 4) $id_4 = $op;
					}
					if($depth == 1) {
						$mdocs_cats[$base_parent_id]['children'][$base_id] = array('slug' => $cat['slug'], 'name' => $cat['name'], 'parent' => $cat['parent'], 'children' => array(), 'depth' => 1);
						if($cat['remove'] == 1) unset($mdocs_cats[$base_parent_id]['children'][count($mdocs_cats[$base_parent_id]['children'])-1]);
						ksort($mdocs_cats[$base_parent_id]['children']);
					} elseif($depth == 2) {
						$mdocs_cats[$base_parent_id]['children'][$base_id]['children'][$id_1] = array('slug' => $cat['slug'], 'name' => $cat['name'], 'parent' => $cat['parent'], 'children' => array(), 'depth' => 2);
						if($cat['remove'] == 1) unset($mdocs_cats[$base_parent_id]['children'][$base_id]['children'][count($mdocs_cats[$base_parent_id]['children'][$base_id]['children'])-1]);
						ksort($mdocs_cats[$base_parent_id]['children'][$base_id]['children']);
						//$mdocs_cats[$base_parent_id]['children'][$base_id]['children'] = array_values($mdocs_cats[$base_parent_id]['children'][$base_id]['children']);
					} elseif($depth == 3) {
						$mdocs_cats[$base_parent_id]['children'][$base_id]['children'][$id_1]['children'][$id_2] = array('slug' => $cat['slug'], 'name' => $cat['name'], 'parent' => $cat['parent'], 'children' => array(), 'depth' =>3);
						if($cat['remove'] == 1) unset($mdocs_cats[$base_parent_id]['children'][$base_id]['children'][$id_1]['children'][count($mdocs_cats[$base_parent_id]['children'][$base_id]['children'][$id_1]['children'])-1]);
						ksort($mdocs_cats[$base_parent_id]['children'][$base_id]['children'][$id_1]['children']);
					} elseif($depth == 4) {
						$mdocs_cats[$base_parent_id]['children'][$base_id]['children'][$id_1]['children'][$id_2]['children'][$id_3] = array('slug' => $cat['slug'], 'name' => $cat['name'], 'parent' => $cat['parent'], 'children' => array(), 'depth' => 4);
						ksort($mdocs_cats[$base_parent_id]['children'][$base_id]['children'][$id_1]['children'][$id_2]['children']);
					}
				}
				$parent_slug = $cat['slug'];
				if($cat['remove'] == 1) mdocs_cleanup_cats($cat);
			//}
		}
		//$mdocs_cats = mdocs_cats_loop($mdocs_cats);
		//var_dump($test);
		//var_dump($mdocs_cats);
		
		ksort($mdocs_cats);
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
	if(count($value['children']) > 0) {
		foreach($value['children'] as $key) {
			mdocs_cleanup_cats($key);	
		}
	}

	update_option('mdocs-list',$mdocs);
}
?>