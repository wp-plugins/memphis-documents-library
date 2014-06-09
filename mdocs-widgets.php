<?php
function mdocs_widgets() {
	register_widget( 'mdocs_top_downloads' );
	register_widget( 'mdocs_top_rated' );
	register_widget( 'mdocs_last_updated' );
}
class mdocs_last_updated extends WP_Widget {
	function mdocs_last_updated() {
		// Instantiate the parent object
		parent::__construct( false, 'Memphis Last Updated' );
	}
	function widget( $args, $instance ) {
		$mdocs = get_option('mdocs-list');
		$the_list  = mdocs_array_sort($mdocs,'modified', SORT_DESC);
		?>
		<div class="mdocs-widget-container">
		<h1>Last Updated</h1>
		<table>
			<tr>
				<th></th>
				<th>File</th>
				<th>Date</th>
			</tr>
		<?php
		for($i=0; $i< get_option('mdocs-last-updated');$i++) {
			if(!isset($the_list[$i])) break;
			$permalink = htmlspecialchars(get_permalink( $the_list[$i]['parent'] ));
			if($i%2 == 0) $row_type = 'mdocs-even';
			else $row_type = 'mdocs-odd';
			echo '<tr class="'.$row_type.'">';
			echo '<td>'.($i+1).'.</td>';
			echo '<td><a href="'.$permalink.'" >'.$the_list[$i]['name'].'</a></td>';
			echo '<td>'.date('m-d-y',$the_list[$i]['modified']).'</td>';
			echo '</tr>';
		}
		?>
			
		</table>
		</div>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		update_option('mdocs-last-updated',$_POST['mdocs-last-updated']);
	}

	function form( $instance ) {
		?>
		<p>
			<input type="text" name="mdocs-last-updated" value="<?php echo get_option('mdocs-last-updated'); ?>" />
		</p>
		<?php
	}
}
class mdocs_top_rated extends WP_Widget {
	function mdocs_top_rated() {
		// Instantiate the parent object
		parent::__construct( false, 'Memphis Top Rated' );
	}
	function widget( $args, $instance ) {
		$mdocs = get_option('mdocs-list');
		$the_list  = mdocs_array_sort($mdocs,'rating', SORT_DESC);
		?>
		<div class="mdocs-widget-container">
		<h1>Top Rated</h1>
		<table>
			<tr>
				<th></th>
				<th>File</th>
				<th>Rating</th>
			</tr>
		<?php
		for($i=0; $i< get_option('mdocs-top-rated');$i++) {
			if(!isset($the_list[$i])) break;
			$permalink = htmlspecialchars(get_permalink( $the_list[$i]['parent'] ));
			if($i%2 == 0) $row_type = 'mdocs-even';
			else $row_type = 'mdocs-odd';
			echo '<tr class="'.$row_type.'">';
			echo '<td>'.($i+1).'.</td>';
			echo '<td><a href="'.$permalink.'" >'.$the_list[$i]['name'].'</a></td>';
			echo '<td>';
			for($j=1;$j<=5;$j++) {
				if($the_list[$i]['rating'] >= $j) echo '<i class="fa fa-star mdocs-gold" id="'.$j.'"></i>';
				elseif(ceil($the_list[$i]['rating']) == $j ) echo '<i class="fa fa-star-half-full mdocs-gold" id="'.$j.'"></i>';
				else echo '<i class="fa fa-star-o" id="'.$j.'"></i>';
			}
			echo '</td>';
			echo '</tr>';
		}
		?>
			
		</table>
		</div>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		update_option('mdocs-top-rated',$_POST['mdocs-top-rated']);
	}

	function form( $instance ) {
		?>
		<p>
			<input type="text" name="mdocs-top-rated" value="<?php echo get_option('mdocs-top-rated'); ?>" />
		</p>
		<?php
	}
}
class mdocs_top_downloads extends WP_Widget {
	function mdocs_top_downloads() {
		// Instantiate the parent object
		parent::__construct( false, 'Memphis Top Downloads' );
	}
	function widget( $args, $instance ) {
		$mdocs = get_option('mdocs-list');
		$the_list  = mdocs_array_sort($mdocs,'downloads', SORT_DESC);
		?>
		<div class="mdocs-widget-container">
		<h1>Top Downloads</h1>
		<table>
			<tr>
				<th></th>
				<th>File</th>
				<th>DLs</th>
			</tr>
		<?php
		for($i=0; $i< get_option('mdocs-top-downloads');$i++) {
			if(!isset($the_list[$i])) break;
			$permalink = htmlspecialchars(get_permalink( $the_list[$i]['parent'] ));
			if($i%2 == 0) $row_type = 'mdocs-even';
			else $row_type = 'mdocs-odd';
			echo '<tr class="'.$row_type.'">';
			echo '<td>'.($i+1).'.</td>';
			echo '<td><a href="'.$permalink.'" >'.$the_list[$i]['name'].'</a></td>';
			echo '<td>'.$the_list[$i]['downloads'].'</td>';
			echo '</tr>';
		}
		?>
			
		</table>
		</div>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		update_option('mdocs-top-downloads',$_POST['mdocs-top-downloads']);
	}

	function form( $instance ) {
		?>
		<p>
			<input type="text" name="mdocs-top-downloads" value="<?php echo get_option('mdocs-top-downloads'); ?>" />
		</p>
		<?php
	}
}
?>