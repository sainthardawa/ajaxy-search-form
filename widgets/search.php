<?php

class AJAXY_SF_WIDGET extends WP_Widget 
{
	function AJAXY_SF_WIDGET() {
		parent::WP_Widget( false, $name = 'Ajaxy live search' );	
	}
	function widget( $args, $instance ) 
	{
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		echo $before_title.$title.$after_title;
		$this->searchform($instance);
		echo $after_widget;
	}
	function form($instance)
	{
		$text_before = $instance['text_before'] ;
		$text_after = $instance['text_after'] ;
		?>
		<p><label for="<?php echo $this->get_field_id( 'text_before' ); ?>"><?php _e( 'Text before:' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'text_before' ); ?>" name="<?php echo $this->get_field_name( 'text_before' ); ?>" type="text" value="<?php echo $text_before; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'text_after' ); ?>"><?php _e( 'Text after:' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'text_after' ); ?>" name="<?php echo $this->get_field_name( 'text_after' ); ?>" type="text" value="<?php echo $text_after; ?>" /></label></p>
	<?php
	}
	function searchform($instance)
	{
		echo $instance['text_before'] ;
		ajaxy_search_form();
		echo $instance['text_after'] ;
	}
	function update( $new_instance, $instance )
	{	
		$old_instance = $instance;
		$old_instance['text_before'] = $new_instance['text_before'] ;
		$old_instance['text_after'] = $new_instance['text_after'] ;
        return $old_instance;
	}
}

?>