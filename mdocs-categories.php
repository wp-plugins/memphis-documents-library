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
				array_push($mdocs_cats[0]['children'], array('slug'=>'test-fold2','name'=>'SUB 2','parent'=>'documents', 'children' => array(), 'depth' => 1));
				
				array_push($mdocs_cats[0]['children'][0]['children'], array('slug'=>'test-fold-sub1','name'=>'SUB SUB 1','parent'=>'test-folder', 'children' => array(), 'depth' => 2));
				array_push($mdocs_cats[0]['children'][0]['children'], array('slug'=>'test-fold2-sub2','name'=>'SUB SUB 2','parent'=>'test-folder', 'children' => array(), 'depth' => 2));
				*/
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
				<input type="button" id="mdocs-cat-remove" name="<?php echo $cat['slug']; ?>" class="button button-primary" value="Remove"  />
			</td>
			<td id="add-cat">
				<input  type="button" name="mdocs-cats[<?php echo $cat['slug']; ?>][<?php echo $cat['depth']; ?>][<?php echo $cat['slug']; ?>]" class="mdocs-add-sub-cat button button-primary" value="Add Category"  />
			</td>
		</tr>
		<?php
		$child = $cat['children'];
		if(count($child) > 0) mdocs_build_cat_td($child); 
	}
}

function mdocs_update_cats() {
	$mdocs_cats = array();
	$upload_dir = wp_upload_dir();
	if(isset($_POST['mdocs-cats'])) {
		//var_dump($_POST['mdocs-cats']);
		$mdocs_cats_post = $_POST['mdocs-cats'];
		$parent_ids = 0;
		foreach($mdocs_cats_post as $key => $value) {
			if(preg_match('/new-cat-/',$value['slug'])) {
				$value['slug'] = preg_replace('/ /','-',strtolower($value['name']));
				$value['slug'] = preg_replace('/[^A-Za-z0-9\-]/', '', $value['slug']);
			}
			if($value['remove'] == 0)  {
				if($value['parent'] == '')  {
					$parent_ids = intval($value['order'])-1;
					$mdocs_cats[$parent_ids] = array('slug' => $value['slug'], 'name' => $value['name'], 'parent' => '', 'children' => array(), 'depth' => 0);
				} else {
					$child_index = intval($value['order'])-1;
					$parent_ids .= ','.$child_index;
					$the_ids = explode(',',$parent_ids);
					//PISS POOR WAY TO FIND DEPTH NEEDS MUCH IMPROVEMENT...
					//[CURRENLY ONLY SUPPORTS 6 LEVELS]
					//DEPTH = 1
					if(isset($mdocs_cats[$the_ids[0]]) && intval($value['depth']) == 1) {
						$mdocs_cats[$the_ids[0]]['children'][intval($value['order'])] = array('slug'=>$value['slug'],'name'=>$value['name'],'parent'=>$value['parent'], 'children' => array(), 'depth' => intval($value['depth']));
					//DEPTH = 2
					} elseif(isset($mdocs_cats[$the_ids[0]]['children'][intval($value['order'])]) && intval($value['depth']) == 2) {
						$mdocs_cats[$the_ids[0]]['children'][$the_ids[1]]['children'][intval($value['order'])] = array('slug'=>$value['slug'],'name'=>$value['name'],'parent'=>$value['parent'], 'children' => array(), 'depth' => intval($value['depth']));
					//DEPTH = 3
					} elseif(isset($mdocs_cats[$the_ids[0]]['children'][$the_ids[1]]['children'][$the_ids[2]]['children']) && intval($value['depth']) == 3) array_push($mdocs_cats[$the_ids[0]]['children'][$the_ids[1]]['children'][$the_ids[2]]['children'], array('slug'=>$value['slug'],'name'=>$value['name'],'parent'=>$value['parent'], 'children' => array(), 'depth' => intval($value['depth'])));
					//DEPTH = 4
					elseif(isset($mdocs_cats[$the_ids[0]]['children'][$the_ids[1]]['children'][$the_ids[2]]['children'][$the_ids[3]]['children']) && intval($value['depth']) == 4) array_push($mdocs_cats[$the_ids[0]]['children'][$the_ids[1]]['children'][$the_ids[2]]['children'][$the_ids[3]]['children'], array('slug'=>$value['slug'],'name'=>$value['name'],'parent'=>$value['parent'], 'children' => array(), 'depth' => intval($value['depth'])));
					//DEPTH = 5
					elseif(isset($mdocs_cats[$the_ids[0]]['children'][$the_ids[1]]['children'][$the_ids[2]]['children'][$the_ids[3]]['children'][$the_ids[4]]['children']) && intval($value['depth']) == 5) array_push($mdocs_cats[$the_ids[0]]['children'][$the_ids[1]]['children'][$the_ids[2]]['children'][$the_ids[3]]['children'][$the_ids[4]]['children'], array('slug'=>$value['slug'],'name'=>$value['name'],'parent'=>$value['parent'], 'children' => array(), 'depth' => intval($value['depth'])));
					//DEPTH = 6
					elseif(isset($mdocs_cats[$the_ids[0]]['children'][$the_ids[1]]['children'][$the_ids[2]]['children'][$the_ids[3]]['children'][$the_ids[4]]['children'][$the_ids[5]]['children']) && intval($value['depth']) == 6) array_push($mdocs_cats[$the_ids[0]]['children'][$the_ids[1]]['children'][$the_ids[2]]['children'][$the_ids[3]]['children'][$the_ids[4]]['children'][$the_ids[5]]['children'], array('slug'=>$value['slug'],'name'=>$value['name'],'parent'=>$value['parent'], 'children' => array(), 'depth' => intval($value['depth'])));
					
				}
			} else mdocs_cleanup_cat($value);
		}
		//var_dump($mdocs_cats[0]);
		$mdocs_cats = mdocs_cats_loop($mdocs_cats);
		
		
		
		ksort($mdocs_cats);
		if(is_array($mdocs_cats)) $mdocs_cats = array_values($mdocs_cats);
		update_option('mdocs-cats',$mdocs_cats);
		//var_dump($mdocs_cats[0]['children']);
	}
}

function mdocs_cats_loop($the_cats) {
	
	foreach($the_cats as $key => $cat) {
		if(count($cat['children']) > 0) {
			ksort($the_cats[$key]['children']);
			$the_cats[$key]['children'] = array_values($the_cats[$key]['children']);
			mdocs_cats_loop($the_cats[$key]['children']);
		}
	}
	return $the_cats;
}

function mdocs_cleanup_cat($value) {
	$upload_dir = wp_upload_dir();
	$mdocs = get_option('mdocs-list');
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
			mdocs_cleanup_cat($key);	
		}
	}
	update_option('mdocs-list',$mdocs);
}
?>