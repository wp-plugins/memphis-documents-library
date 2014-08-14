<?php
if(isset($_POST['type']) && $_POST['type'] == 'rating') {
	require_once($_POST['wp_root'] . 'wp-load.php');
	$mdocs = get_option('mdocs-list');
	$mdocs_show_ratings = get_option( 'mdocs-show-ratings' );
	$is_admin = $_POST['is_admin'];
	$found = false;
	
	foreach($mdocs as $index => $the_mdoc) {
		if(intval($the_mdoc['id']) == intval($_POST['mdocs_file_id']) && $found == false) {
			if($mdocs_show_ratings) {
				$the_rating = mdocs_get_rating($the_mdoc);
				if($the_rating['your_rating'] == 0) $text = __("Rate Me!");
				else $text = __("Your Rating");
				echo '<div class="mdocs-rating-container">';
				echo '<h1>'.$the_mdoc['name'].'</h1>';
				echo '<div class="mdocs-ratings-stars">';
				echo '<p>'.$text,'</p>';
				for($i=1;$i<=5;$i++) {
					if($the_rating['average'] >= $i) echo '<i class="fa fa-star fa-5x mdocs-gold mdocs-big-star mdocs-my-rating" id="'.$i.'"></i>';
					elseif(ceil($the_rating['average']) == $i ) echo '<i class="fa fa-star-half-full fa-5x mdocs-gold mdocs-big-star mdocs-my-rating" id="'.$i.'"></i>';
					else echo '<i class="fa fa-star-o fa-5x mdocs-gold mdocs-big-star mdocs-my-rating" id="'.$i.'"></i>';
				}
				echo '</div>';
				echo '</div>';
			} else _e('Ratings functionality is off.');
			$found = true;
			break;
		}
	}
}
?>