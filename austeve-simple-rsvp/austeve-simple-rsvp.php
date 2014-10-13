<?php
/**
 * Plugin Name: Simple RSVP
 * Plugin URI: https://github.com/australiansteve/wp-plugins/austeve-simple-rsvp
 * Description: Create a simple RSVP form on your website
 * Version: 1.0
 * Author: AustralianSteve
 * Author URI: http://AustralianSteve.com
 * License: GPL2
 */


// create custom plugin settings menu
add_action('admin_menu', 'austeve_simple_rsvp_create_menu');

function austeve_simple_rsvp_create_menu() {

	//Load scripts for the admin page
	add_action( 'admin_enqueue_scripts', 'load_admin_scripts' );

	//create new top-level menu
	add_menu_page('Simple RSVP Settings', 'RSVP Settings', 'administrator', __FILE__, 'austeve_simple_rsvp_settings_page');

	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );

}


function register_mysettings() {
	//register our settings
	register_setting( 'austeve-simple-rsvp-settings-group', 'send_to' );
	register_setting( 'austeve-simple-rsvp-settings-group', 'subject' );
	register_setting( 'austeve-simple-rsvp-settings-group', 'message_options' );
}

function austeve_simple_rsvp_settings_page() {
	?>
	<div class="wrap">
	<h2>Simple RSVP</h2>

	<form method="post" action="options.php">
	    <?php settings_fields( 'austeve-simple-rsvp-settings-group' ); ?>
	    <?php do_settings_sections( 'austeve-simple-rsvp-settings-group' ); ?>
	    <table class="form-table" id="rsvp-options">
	        <tr valign="top">
	        <th scope="row">Send To</th>
	        <td><input type="text" name="send_to" value="<?php echo esc_attr( get_option('send_to') ); ?>" /></td>
	        </tr>
	         
	        <tr valign="top">
	        <th scope="row">Subject</th>
	        <td><input type="text" name="send_to" value="<?php echo esc_attr( get_option('subject') ); ?>" /></td>
	        </tr>
	        
	        <tr>
	        	<th>Options</th>
	        	<td><button type="button" name="add_option">Add Option</button></td>
	        </tr>

	    </table>
	    
	    <?php submit_button(); ?>

	</form>
	</div>
	<?php 
} 


function load_admin_scripts() {
	wp_enqueue_script( 'options_script', plugin_dir_url( __FILE__ ) . 'admin.js',, array('jquery')  );
}

/**
 * Add widget.for displaying RSVP form
 */
class Austeve_Simple_RSVP_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'austeve_simple_rsvp_widget', // Base ID
			__('Simple RSVP', 'austeve_simple_rsvp'), // Name
			array( 'description' => __( 'Display a Simple RSVP form', 'austeve_simple_rsvp' ), ) // Args
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
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		echo __( 'Hello, World!', 'austeve_simple_rsvp' );
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
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'austeve_simple_rsvp' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
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

		return $instance;
	}

} // class Foo_Widget


// register Foo_Widget widget
function register_austevesimplersvp_widget() {
    register_widget( 'Austeve_Simple_RSVP_Widget' );
}
add_action( 'widgets_init', 'register_austevesimplersvp_widget' );



?>