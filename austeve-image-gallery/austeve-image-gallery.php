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
    wp_enqueue_style( 'slick_styles', plugin_dir_url( __FILE__ ) . 'bower_components/slick.js/slick/slick.css', '' );
}
add_action( 'wp_enqueue_scripts', 'austeve_image_gallery_styles' );

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

	//Get all attachments with term_id
	$attachments = get_objects_in_term( $term->term_id, $taxonomy );
	
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

?>