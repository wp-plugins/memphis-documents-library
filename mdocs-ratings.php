<?php
function mdocs_ratings() {
	if(isset($_POST['type']) && $_POST['type'] == 'rating') {
		$mdocs = get_option('mdocs-list');
		$mdocs_show_ratings = get_option( 'mdocs-show-ratings' );
		$found = false;
		foreach($mdocs as $index => $the_mdoc) {
			if(intval($the_mdoc['id']) == intval($_POST['mdocs_file_id']) && $found == false) {
				if($mdocs_show_ratings) {
					$the_rating = mdocs_get_rating($the_mdoc);
					if($the_rating['your_rating'] == 0) $text = __("Rate Me!");
					else $text = __("Your Rating");
					echo '<div class="mdocs-rating-container">';
					echo '<h1>'.$the_mdoc['name'].'</h1>';
					echo '<div class="mdocs-ratings-stars" data-my-rating="'.$the_rating['your_rating'].'">';
					echo '<p>'.$text,'</p>';
					for($i=1;$i<=5;$i++) {
						if($the_rating['average'] >= $i) echo '<i class="fa fa-star fa-5x mdocs-gold  mdocs-my-rating" id="'.$i.'"></i>';
						elseif(ceil($the_rating['average']) == $i ) echo '<i class="fa fa-star-half-full fa-5x mdocs-gold mdocs-my-rating" id="'.$i.'"></i>';
						else echo '<i class="fa fa-star-o fa-5x mdocs-my-rating" id="'.$i.'"></i>';
					}
					echo '</div>';
					echo '</div>';
				} else _e('Ratings functionality is off.','mdocs');
				$found = true;
				break;
			}
		}
	}
}
function mdocs_set_rating($the_id) {
	global $current_user;
	$avg = 0;
	if(isset($_GET['mdocs-rating'])) $the_rating = $_GET['mdocs-rating'];
	elseif(isset($_POST['mdocs-rating'])) $the_rating = intval($_POST['mdocs-rating']);
	$mdocs = get_option('mdocs-list');
	foreach($mdocs as $index => $doc) if($doc['id'] == $the_id) $doc_index = $index;
	$mdocs[$doc_index]['ratings'][$current_user->user_email] = $the_rating;
	foreach($mdocs[$doc_index]['ratings'] as $index => $rating) $avg += $rating;
	$mdocs[$doc_index]['rating'] = floatval(number_format($avg/count($mdocs[$doc_index]['ratings']),1));
	
	mdocs_save_list($mdocs);
	$_POST['type'] = 'rating';
	mdocs_ratings();
	
}
?>