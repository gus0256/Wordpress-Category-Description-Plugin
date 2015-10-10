<?php
/*
	Plugin Name: LAS Category Description Widget
	Plugin URI: http://lazyassstoner.com
	Description: Easy way to display category descriptions in the sidebar.
	Version: 1.3
	Author: Gus
	Author URI: http://lazyassstoner.com
	License: GPL2
*/
/**
 * Adds lbb_widget_desc widget.
 */
class lbb_widget_desc extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'lbb_widget_desc_widget', // Base ID
			__( 'LAS Category Widget', 'text_domain' ), // Name
			array( 'description' => __( 'Display Category Text Widget', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if(is_category( $category ) && empty( $instance['category_display'] )){
			$category_description = category_description();
			if ( ! empty( $instance['title'] ) && empty( $instance['category_title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
			}
			else if( ! empty( $instance['category_title'] ) && ! empty($category_description)){
				echo "<h4 class=\"widgettitle\">";
				echo single_cat_title();
				echo "</h2>";
			}
			if(! empty($category_description)){
				echo "<div class=\"textwidget\">";
				echo $category_description;
				echo "</div>";
			}
		}
		else if((is_home() || is_front_page()) && ! empty( $instance['homepage_display'] ))
		{
			if ( ! empty( $instance['title'] ) && empty( $instance['category_title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
			}
			echo "<div class=\"textwidget\">";
			echo wpautop($instance['textarea']);
			echo "</div>";
		}
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( '', 'text_domain' );
		$textarea = ! empty( $instance['textarea'] ) ? $instance['textarea'] : __( '', 'text_domain' );
		$category_title = ! empty($instance['category_title']) ? $instance['category_title'] : 'off';
		$category_title = ! empty($instance['homepage_display']) ? $instance['homepage_display'] : 'off';
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		<input class="checkbox" type="checkbox" <?php checked($instance['category_title'], 'on'); ?> id="<?php echo $this->get_field_id('category_title'); ?>" name="<?php echo $this->get_field_name('category_title'); ?>" /> 
		<label for="<?php echo $this->get_field_id('category_title'); ?>">Show Category Title</label><br>
		<input class="checkbox" type="checkbox" <?php checked($instance['category_display'], 'on'); ?> id="<?php echo $this->get_field_id('category_display'); ?>" name="<?php echo $this->get_field_name('category_display'); ?>" /> 
		<label for="<?php echo $this->get_field_id('category_display'); ?>">Disable Category Descriptions</label><br>
		<input class="checkbox" type="checkbox" <?php checked($instance['homepage_display'], 'on'); ?> id="<?php echo $this->get_field_id('homepage_display'); ?>" name="<?php echo $this->get_field_name('homepage_display'); ?>" /> 
		<label for="<?php echo $this->get_field_id('homepage_display'); ?>">Display Home Page Text</label><br>
		<label for="<?php echo $this->get_field_id('textarea'); ?>"><?php _e('Home Page Text:', 'wp_widget_plugin'); ?></label><br>
		<textarea class="widefat" id="<?php echo $this->get_field_id('textarea'); ?>" name="<?php echo $this->get_field_name('textarea'); ?>"><?php echo $textarea; ?></textarea>
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		//Allow unfiltered_html
		if ( current_user_can('unfiltered_html') )
			$instance['textarea'] =  $new_instance['textarea'];
		else
			$instance['textarea'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['textarea']) ) );
		
		$instance['category_title'] = ( ! empty( $new_instance['category_title'] ) ) ? strip_tags( $new_instance['category_title'] ) : '';
		$instance['category_display'] = ( ! empty( $new_instance['category_display'] ) ) ? strip_tags( $new_instance['category_display'] ) : '';
		$instance['homepage_display'] = ( ! empty( $new_instance['homepage_display'] ) ) ? strip_tags( $new_instance['homepage_display'] ) : '';
		return $instance;
	}

} // class lbb_widget_desc

// register lbb_widget_desc widget
function register_lbb_widget_desc() {
    register_widget( 'lbb_widget_desc' );
}
add_action( 'widgets_init', 'register_lbb_widget_desc' );
?>