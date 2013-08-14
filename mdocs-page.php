<?php
function mdocs_page() {
	global $post;
	$site_url = site_url();
	$upload_dir = wp_upload_dir();	
	$mdocs = get_option('mdocs-list');
	$cats =  get_option('mdocs-cats');
	if(isset($_GET['cat'])) $current_cat = $_GET['cat'];
	elseif(!is_string($cats)) $current_cat = key($cats);
	
	$permalink = get_permalink($post->ID);
	if(preg_match('/\?page_id=/',$permalink)) $mdocs_get = $_SERVER['REQUEST_URI'].'&cat=';
	else $mdocs_get = '?cat=';
	
?>
<div class="mdocs-container">	
	<h2 class="mdocs-nav-wrapper">
		<div id="icon-edit-pages" class="icon32"><br></div>
		<?php
		if(!empty($cats)) {
			foreach( $cats as $cat => $name ){
				$class = ( $cat == $current_cat ) ? ' mdocs-nav-tab-active' : '';
				
				echo "<a class='mdocs-nav-tab$class' href='$mdocs_get$cat'>$name<hr /></a>";
			}
		}
		?>
	</h2>
<div class="mdocs-box">	
	<table class="" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" class="mdocs-table-header" style=""><?php _e('Document'); ?></th>
				<th scope="col" class="mdocs-table-header" style=""><?php _e('Description'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" class="mdocs-table-header" style=""><?php _e('Document'); ?></th>
				<th scope="col" class="mdocs-table-header" style=""><?php _e('Description'); ?></th>
			</tr>
		</tfoot>
		<tbody id="the-list">
<?php
	$bgcolor = "mdocs-bg-even";
	$count = 0;
	for($index=0; $index<count($mdocs);$index++) {
		$mdocs_post = get_post($mdocs[$index]['parent']);
		$mdocs_desc = $mdocs_post->post_excerpt;
		$the_mdoc_permalink = get_permalink($mdocs[$index]['parent']);
		if($mdocs[$index]['cat'] == $current_cat) {
			$count++;
			?>
			<tr id="<?php echo $mdocs[$index][id]; ?>" class="<?php echo $bgcolor; ?>">
				<td class="mdocs-file-info">
					<a href="<?php echo $the_mdoc_permalink; ?>" title="<?php echo $mdocs[$index]['name']; ?> "><strong><?php echo $mdocs[$index]['name']; ?> </strong></a>
					<div>
						<!--<p><i class="icon-star"></i> 4.4 Stars (102)</p>-->
						<p><i class="icon-cloud-download"></i> <b class="mdocs-orange"><?php echo $mdocs[$index]['downloads'].' '. __('Downloads'); ?></b></p>
						<p><i class="icon-pencil"></i> <?php _e('Author'); ?>: <i class="mdocs-green"><?php echo $mdocs[$index]['owner']; ?></i></p>
						<p><i class="icon-off"></i> <?php _e('Version'); ?>: <b class="mdocs-blue"><?php echo $mdocs[$index]['version']; ?></b></p>
						<p><i class="icon-calendar"></i> <?php _e('Date Modified'); ?>: <b class="mdocs-red"><?php  echo gmdate('F jS Y \a\t g:i A',filemtime($upload_dir['basedir'].'/mdocs/'.$mdocs[$index]['filename'])+MDOCS_TIME_OFFSET); ?></b>
					</div>
				</td>
				<td class="mdocs-desc">
					<div>
						<p><?php print do_shortcode(nl2br($mdocs_desc)); ?> </p>
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
			if($bgcolor == "mdocs-bg-even") $bgcolor = "mdocs-bg-odd";
			else $bgcolor = "mdocs-bg-even";
		}
	}
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
</div>
<?php
}
?>