<?php
/*
	Plugin Name: An Easy Category Description Widget
	Plugin URI: http://littlebigbyte.com
	Description: An easy way to display category descriptions in a widget.
	Version: 1.4
	Author: Gus
	Author URI: http://littlebigbyte.com
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
			__( 'An Easy Category Description Widget', 'text_domain' ), // Name
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
			if ( ! empty( $instance['title'] ) && empty( $instance['display_category_title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
			}
			else if( ! empty( $instance['display_category_title'] ) && ! empty($category_description)){
				echo "<h4 class=\"widgettitle\">".single_cat_title("",false)."</h4>";
			}
			if(! empty($category_description)){
				echo "<div class=\"textwidget\">".$category_description."</div>";
			}
		}
		else if((is_home() || is_front_page()) && ! empty( $instance['home_page_text_area'] ))
		{
			if ( ! empty( $instance['title'] ) && empty( $instance['home_title_display'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
			}
			else if( ! empty( $instance['title_home'] ) && ! empty($instance['home_title_display'])){
				echo "<h4 class=\"widgettitle\">".$instance['title_home']."</h4>";
			}
			echo "<div class=\"textwidget\">".wpautop($instance['home_page_text_area'])."</div>";
		}
		else if(is_tax() && empty( $instance['category_display'] )){
			$single_term_title = single_term_title("",false);
			if ( ! empty( $instance['title'] ) && empty( $instance['display_category_title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
			}
			else if(!empty( $instance['display_category_title'] ) && ! empty($single_term_title)){
				echo '<h4 class="widgettitle">'.$single_term_title.'</h4>';
			}				
			if(! empty(term_description())){
				echo '<div class="textwidget">'.term_description().'</div>';
			}
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
		$home_page_text_area = ! empty( $instance['home_page_text_area'] ) ? $instance['home_page_text_area'] : __( '', 'text_domain' );
		$title_home = ! empty( $instance['title_home'] ) ? $instance['title_home'] : __( '', 'text_domain' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		
		<input class="checkbox" type="checkbox" <?php checked($instance['display_category_title'], 'on'); ?> id="<?php echo $this->get_field_id('display_category_title'); ?>" name="<?php echo $this->get_field_name('display_category_title'); ?>" /> 
		<label for="<?php echo $this->get_field_id('display_category_title'); ?>">Show Category Titles</label><br>
		
		<input class="checkbox" type="checkbox" <?php checked($instance['category_display'], 'on'); ?> id="<?php echo $this->get_field_id('category_display'); ?>" name="<?php echo $this->get_field_name('category_display'); ?>" /> 
		<label for="<?php echo $this->get_field_id('category_display'); ?>">Disable Category Descriptions</label><br>
		
		<input class="checkbox" type="checkbox" <?php checked($instance['homepage_display'], 'on'); ?> id="<?php echo $this->get_field_id('homepage_display'); ?>" name="<?php echo $this->get_field_name('homepage_display'); ?>" /> 
		<label for="<?php echo $this->get_field_id('homepage_display'); ?>">Display Home Page Text</label><br>
		
		<input class="checkbox" type="checkbox" <?php checked($instance['home_title_display'], 'on'); ?> id="<?php echo $this->get_field_id('home_title_display'); ?>" name="<?php echo $this->get_field_name('home_title_display'); ?>" /> 
		<label for="<?php echo $this->get_field_id('home_title_display'); ?>">Display Home Title</label><br>
		
		<label for="<?php echo $this->get_field_id('title_home'); ?>"><?php _e('Home Page Title:', 'wp_widget_plugin'); ?></label><br>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title_home' ); ?>" name="<?php echo $this->get_field_name( 'title_home' ); ?>" type="text" value="<?php echo esc_attr( $title_home ); ?>">
		
		<label for="<?php echo $this->get_field_id('home_page_text_area'); ?>"><?php _e('Home Page Text:', 'wp_widget_plugin'); ?></label><br>
		<textarea class="widefat" id="<?php echo $this->get_field_id('home_page_text_area'); ?>" name="<?php echo $this->get_field_name('home_page_text_area'); ?>"><?php echo $home_page_text_area; ?></textarea>
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
		//Update all our instance variables
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['display_category_title'] = ( ! empty( $new_instance['display_category_title'] ) ) ? strip_tags( $new_instance['display_category_title'] ) : '';
		$instance['category_display'] = ( ! empty( $new_instance['category_display'] ) ) ? strip_tags( $new_instance['category_display'] ) : '';
		$instance['homepage_display'] = ( ! empty( $new_instance['homepage_display'] ) ) ? strip_tags( $new_instance['homepage_display'] ) : '';
		$instance['home_title_display'] = ( ! empty( $new_instance['home_title_display'] ) ) ? strip_tags( $new_instance['home_title_display'] ) : '';
		$instance['title_home'] = ( ! empty( $new_instance['title_home'] ) ) ? strip_tags( $new_instance['title_home'] ) : '';
		
		//Allow unfiltered_html
		if ( current_user_can('unfiltered_html') )
			$instance['home_page_text_area'] =  $new_instance['home_page_text_area'];
		else
			$instance['home_page_text_area'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['home_page_text_area']) ) );
		
		return $instance;
	}

} // class lbb_widget_desc

// register lbb_widget_desc widget
function register_lbb_widget_desc() {
    register_widget( 'lbb_widget_desc' );
}
add_action( 'widgets_init', 'register_lbb_widget_desc' );
?>