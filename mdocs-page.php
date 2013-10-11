<?php
function mdocs_page() {
	//mdocs_update_bot_list(); 
	//echo 'Is bot: '.mdocs_is_bot();
	global $post;
	$site_url = site_url();
	$upload_dir = wp_upload_dir();	
	$mdocs = get_option('mdocs-list');
	$cats =  get_option('mdocs-cats');
	if(isset($_GET['cat'])) $current_cat = $_GET['cat'];
	elseif(!is_string($cats)) $current_cat = key($cats);
	
	$permalink = get_permalink($post->ID);
	if(preg_match('/\?page_id=/',$permalink) || preg_match('/\?p=/',$permalink)) {
		//$explode = explode('&',$_SERVER['REQUEST_URI']);
		//$mdocs_get = site_url().$explode[0].'&cat=';
		$mdocs_get = $permalink.'&cat=';
	} else $mdocs_get = $permalink.'?cat=';
	
?>
<div class="mdocs-container">	
	<h2 class="mdocs-nav-wrapper">
		<div id="icon-edit-pages" class="icon32"><br></div>
		<?php
		if(!empty($cats)) {
			foreach( $cats as $cat => $name ){
				$class = ( $cat == $current_cat ) ? ' mdocs-nav-tab-active' : '';
				echo '<a class="mdocs-nav-tab'.$class.'" href="'.$mdocs_get.$cat.'">'.$name.'<hr /></a>';
			}
		}
		?>
	</h2>
	<?php
	$count = 0;
	foreach($mdocs as $the_mdoc) {
		if($the_mdoc['cat'] == $current_cat) {
			if($the_mdoc['file_status'] == 'public' ) {
				$count ++;
				$mdocs_post = get_post($the_mdoc['parent']);
				$mdocs_desc = apply_filters('the_content', $mdocs_post->post_excerpt);
			
				?>
				<div class="mdocs-post">
					<?php mdocs_file_info($the_mdoc); ?>
					<div class="mdocs-clear-both"></div>
					<?php mdocs_social($the_mdoc); ?>
				</div>
				<div class="mdocs-clear-both"></div>
				<h3>Description</h3>
				<div class="mdoc-desc">
					<?php echo $mdocs_desc; ?>
				</div>
				<div class="mdocs-clear-both"></div>
				</div>
				<?php
			}
		} 
	}
	if($count == 0) {
		?> <p class="mdocs-nofiles" ><?php _e('No files found in this category.'); ?></p> <?php
	}
}
?>