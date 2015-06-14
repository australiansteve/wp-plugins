<?php
/**
 * Plugin Name: austeve Image Gallery
 * Plugin URI: https://github.com/australiansteve/wp-plugins/austeve-image-gallery
 * Description: Display a set on images in a page or post
 * Version: 1.0
 * Author: AustralianSteve
 * Author URI: http://AustralianSteve.com
 * License: GPL2
 */

function austeve_image_gallery_styles() {
    wp_enqueue_style( 'slick_styles', plugin_dir_url( __FILE__ ) . 'bower_components/slick.js/slick/slick.css', array() );
    wp_enqueue_style( 'slick_theme_styles', plugin_dir_url( __FILE__ ) . 'bower_components/slick.js/slick/slick-theme.css', array() );
    wp_enqueue_style( 'austeve_image_gallery_preview_styles', plugin_dir_url( __FILE__ ) . 'style.css', array() );
}
add_action( 'wp_enqueue_scripts', 'austeve_image_gallery_styles', 11 );

function austeve_image_gallery_scripts() {
    if ( WP_DEBUG ) :
        // Enqueue our debug scripts
        wp_register_script ( 'slick_scripts', plugin_dir_url( __FILE__ ) . 'bower_components/slick.js/slick/slick.js', array('jquery'), '', false );
        wp_enqueue_script ( 'slick_scripts', plugin_dir_url( __FILE__ ) . 'bower_components/slick.js/slick/slick.js', array('jquery'), '', false );
    else :
        // Enqueue our minified scripts
        wp_register_script ( 'slick_scripts', plugin_dir_url( __FILE__ ) . 'bower_components/slick.js/slick/slick.min.js', array('jquery'), '', false );
        wp_enqueue_script ( 'slick_scripts', plugin_dir_url( __FILE__ ) . 'bower_components/slick.js/slick/slick.min.js', array('jquery'), '', false );
    endif;
}
add_action( 'wp_enqueue_scripts', 'austeve_image_gallery_scripts' );


function insert_images($atts) {
	$atts = shortcode_atts( array(
        'taxonomy' => 'media_category',
        'slug' => 'default-slug',
        'numImages' => '-1'
    ), $atts );

	$returnString = "<div class='austeve-gallery-images'>";

	$image_ids = get_attachment_ids_by_slug( $atts['slug'], $atts['taxonomy']);

    if (count($image_ids) > $atts['numImages'] && $atts['numImages'] > 0)
    {
    	//Return random set of images - shuffle them for shufflings sake
    	$return_keys = array_rand($image_ids, $atts['numImages']);

    	foreach($return_keys as $key)
    	{
    		$returnString .= '<div class="austeve-gallery-image-container">'.wp_get_attachment_image( $image_ids[$key], "full" ).'</div>';
    	}
    }
    else if (count($image_ids) == 0)
    {
        	$returnString .= '<div class="austeve-gallery-image-container">No images found for this category</div>';
    }
    else 
    {
    	//return all
        foreach($image_ids as $id)
		{
        	$returnString .= '<div class="austeve-gallery-image-container">'.wp_get_attachment_image( $id, 'full' ).'</div>';
		}
    }

    $returnString .= "</div>";

    return $returnString;

}
add_shortcode( 'insert_images', 'insert_images');

function get_attachment_ids_by_slug( $slug, $taxonomy = 'media_category', $shuffle = true) {

	//Get the term ID for the given slug
	$term = get_term_by('slug', $slug, 'media_category');
    $attachments = null;
    
    if ( $term )
    {
    	//Get all attachments with term_id
    	$attachments = get_objects_in_term( $term->term_id, $taxonomy );
    }

	//Get the URL for each attachment
	$ids = array();
	if ( $attachments ) {
		foreach ( $attachments as $post ) {
			$ids[] = get_post($post)->ID;
		}
		wp_reset_postdata();
	}

    if ( $shuffle )
	   shuffle($ids);

	return $ids;
}

function add_init_scripts() {
    wp_enqueue_script('austeve_init_script', plugin_dir_url(__FILE__) . 'js/init.js', array('slick_scripts') );
}
add_action('init', 'add_init_scripts');


// Creating the widget 
class austeve_gallery_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
        // Base ID of your widget
        'austeve_gallery_widget', 

        // Widget name will appear in UI
        __('AUSteve Gallery Widget', 'austeve_gallery_widget_domain'), 

        // Widget description
        array( 'description' => __( 'Preview for a gallery', 'austeve_gallery_widget_domain' ), ) 
        );
    }

    // Creating widget front-end
    // This is where the action happens
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];

        // This is where you run the code and display the output
        $widgetOutput = "<div class='container'>";
        $widgetOutput .= "<div class='layover'><h2 class='title'>".$instance['title']."</h2>";
        if (isset($instance['description'])) {
            $widgetOutput .= "<div class='description'>".$instance['description']."</div>";
        }
        if (isset($instance['action_url'])) {
            $widgetOutput .= "<a href='".$instance['action_url']."' class='button'>".$instance['action_verb']."</a>";
        }
        $widgetOutput .= "</div>"; //div.layover
        $widgetOutput .= "<div class='img'><img src='".$instance['preview_image']."'/></div>";
        $widgetOutput .= "</div>"; //div.container

        echo __( $widgetOutput, 'austeve_gallery_widget_domain' );
        echo $args['after_widget'];
    }
        
    // Widget Backend 
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'Gallery title', 'austeve_gallery_widget_domain' );
        }

        if ( isset( $instance[ 'preview_image' ] ) ) {
            $preview_image = $instance[ 'preview_image' ];
        }
        else {
            $preview_image = __( '%image url%', 'austeve_gallery_widget_domain' );
        }
        
        if ( isset( $instance[ 'description' ] ) ) {
            $description = $instance[ 'description' ];
        }
        else {
            $description = __( '', 'austeve_gallery_widget_domain' );
        }
        
        if ( isset( $instance[ 'action_url' ] ) ) {
            $action_url = $instance[ 'action_url' ];
        }
        else {
            $action_url = __( '', 'austeve_gallery_widget_domain' );
        }
        
        if ( isset( $instance[ 'action_verb' ] ) ) {
            $action_verb = $instance[ 'action_verb' ];
        }
        else {
            $action_verb = __( 'Text to show on link button', 'austeve_gallery_widget_domain' );
        }

        // Widget admin form
?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        <label for="<?php echo $this->get_field_id( 'preview_image' ); ?>"><?php _e( 'Preview image:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'preview_image' ); ?>" name="<?php echo $this->get_field_name( 'preview_image' ); ?>" type="text" value="<?php echo esc_attr( $preview_image ); ?>" />
        <label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Short description:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" type="text" value="<?php echo esc_attr( $description ); ?>" />
        <label for="<?php echo $this->get_field_id( 'action_url' ); ?>"><?php _e( 'Link URL:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'action_url' ); ?>" name="<?php echo $this->get_field_name( 'action_url' ); ?>" type="text" value="<?php echo esc_attr( $action_url ); ?>" />
        <label for="<?php echo $this->get_field_id( 'action_verb' ); ?>"><?php _e( 'Link wording:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'action_verb' ); ?>" name="<?php echo $this->get_field_name( 'action_verb' ); ?>" type="text" value="<?php echo esc_attr( $action_verb ); ?>" />
        </p>
<?php 
    }
        
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['preview_image'] = ( ! empty( $new_instance['preview_image'] ) ) ? strip_tags( $new_instance['preview_image'] ) : '';
        $instance['description'] = ( ! empty( $new_instance['description'] ) ) ? strip_tags( $new_instance['description'] ) : '';
        $instance['action_url'] = ( ! empty( $new_instance['action_url'] ) ) ? strip_tags( $new_instance['action_url'] ) : '';
        $instance['action_verb'] = ( ! empty( $new_instance['action_verb'] ) ) ? strip_tags( $new_instance['action_verb'] ) : 'Details';
        return $instance;
    }
} // Class austeve_gallery_widget ends here

// Register and load the widget
function austeve_gallery_load_widget() {
    register_widget( 'austeve_gallery_widget' );

    register_sidebar( array(
        'name'          => 'Gallery preview sidebar',
        'id'            => 'austeve_gallery_1',
        'before_widget' => '<li class="widget_austeve_gallery_widget">',
        'after_widget'  => '</li>',
        'before_title'  => '',
        'after_title'   => '',
    ) );
}
add_action( 'widgets_init', 'austeve_gallery_load_widget' );
?>