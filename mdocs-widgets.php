<?php
function mdocs_widgets() {
	//register_widget( 'mdocs_top_downloads' );
}
class mdocs_top_downloads extends WP_Widget {
	function mdocs_top_downloads() {
		// Instantiate the parent object
		parent::__construct( false, 'Memphis Top Downloads' );
	}
	//412653792937508864 - CTL
	//470999648673341440 - Bhaldie
	function widget( $args, $instance ) {
		
	}

	function update( $new_instance, $old_instance ) {
		//update_option('mtwitter-id',$_POST['mtwitter-id']);
		//update_option('mtwitter-num-displayed',$_POST['mtwitter-num-displayed']);
	}

	function form( $instance ) {
		
	}
}
?>