<?php
function mdocs_edit_cats() {
	$mdocs_cats = get_option('mdocs-cats');
	//var_dump($mdocs_cats);
	?>
	<div class="mdocs-container">
		<h2>Category Editor <a href="" id="mdocs-add-cat" class="mdocs-grey-btn">Add Category</a></h2>
		<form  id="mdocs-cats" method="post" action="admin.php?page=memphis-documents.php&cat=cats" >
			<input type="hidden" value="mdocs-update-cats" name="action"/>
			<table class="wp-list-table widefat plugins">
				<thead>
					<tr>
						<th scope="col" class="manage-column column-name" ><?php _e('Category'); ?></th>
						<th scope="col"  class="manage-column column-name" ><?php _e('Order'); ?></th>
						<th scope="col"  class="manage-column column-name" ><?php _e('Remove'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="col" class="manage-column column-name" ><?php _e('Category'); ?></th>
						<th scope="col" class="manage-column column-name" ><?php _e('Order'); ?></th>
						<th scope="col" class="manage-column column-name" ><?php _e('Remove'); ?></th>
					</tr>
				</tfoot>
				<tbody id="the-list">
			<?php
			$index = 0;
			if(!empty($mdocs_cats)) {
				foreach($mdocs_cats as $key => $value) {
					$index++;
					?>
						<tr>
							<td  id="name" >
								<input type="hidden" name="mdocs-cats[<?php echo $key; ?>][slug]" value="<?php echo $key; ?>"/>
								<input type="text" name="mdocs-cats[<?php echo $key; ?>][name]"  value="<?php echo $value; ?>"  />
							</td>
							<td id="order">
								<input type="text" name="mdocs-cats[<?php echo $key; ?>][order]"  value="<?php echo $index; ?>"  />
							</td>
							<td id="remove">
								<input type="hidden" name="mdocs-cats[<?php echo $key; ?>][remove]" value="0"/>
								<input type="button" id="mdocs-cat-remove" name="<?php echo $key; ?>" class="button button-primary" value="Remove"  />
							</td>
						</tr>
					<?php
				}
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

function mdocs_update_cats() {
	//$mdocs_cats = get_option('mdocs-cats');
	$upload_dir = wp_upload_dir();
	if(isset($_POST['mdocs-cats'])) {
		$mdocs_cats_post = $_POST['mdocs-cats'];
		foreach ($mdocs_cats_post as $key => $row) $sort_array[$key] = $row['order'];
		array_multisort($sort_array, SORT_ASC, $mdocs_cats_post);
		foreach($mdocs_cats_post as $key => $value) {
			if(preg_match('/new-cat-/',$value['slug'])) {
				$value['slug'] = preg_replace('/ /','-',strtolower($value['name']));
				$value['slug'] = preg_replace('/[^A-Za-z0-9\-]/', '', $value['slug']);
			}
			if($value['remove'] == 0) $mdocs_cats[$value['slug']] = $value['name'];
			else {
				$mdocs = get_option('mdocs-list');
				foreach($mdocs as $k => $v) {
					if($v['cat'] == $value['slug']) {
						wp_delete_attachment( intval($v['id']), true );
						wp_delete_post( intval($v['parent']), true );
						$name = substr($v['filename'], 0, strrpos($v['filename'], '.') );
						if(file_exists($upload_dir['basedir'].'/mdocs/'.$v['filename'])) @unlink($upload_dir['basedir'].'/mdocs/'.$v['filename']);
						foreach($v['archived'] as $a) unlink($upload_dir['basedir'].'/mdocs/'.$a);
						$thumbnails = glob($upload_dir['basedir'].'/mdocs/'.$name.'-150x55*');
						foreach($thumbnails as $t) unlink($t);
						unset($mdocs[$k]);
					}
				}
				update_option('mdocs-list',$mdocs);
			}
		}
		update_option('mdocs-cats',$mdocs_cats);
	}
}
?>