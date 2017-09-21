<?php

// TODO - roll this behavior into one class.
// Stored in a variable for consistency. 
// There are the classes used for widths on single column tome content
$tome_content_widths = "large-10 large-centered medium-8 medium-centered small-12 columns"; 

//TODO - Why is this a thing. Ask Lex & look at this blog.
if ( get_current_blog_id() == 254 ) {
	register_nav_menus( array(
		'cover_menu' => 'Cover Page Menu',
		) );
}

// Utility functions
require_once( get_template_directory() . '/lib/tome-utility-functions.php' );

// Template functions
require_once( get_template_directory() . '/lib/tome-template-functions.php' );

// Initialization functions
require_once( get_template_directory() . '/lib/tome-init-functions.php' );

// Enable Hypothesis Support
require_once( get_template_directory() . '/lib/enable-hypothesis.php' );

// Sets up Embed Media Sizes  
require_once( get_template_directory() . '/lib/embed-media-sizes/embed-media-edits.php' );

// Modifications to the WP Dashboard
require_once( get_template_directory() . '/lib/tome-dashboard-customization.php' );

//Include dependencies.
require_once( get_template_directory() . '/tome-deps/include.php' );

// Gallery Shortcode
require_once( get_template_directory() . '/lib/tome-gallery-shortcode/class.shortcode.php' );

// WPML Support ( Language Switcher )
require_once( get_template_directory() . '/lib/tome-wpml-support.php' );

// Shortcodes
require_once( get_template_directory() . '/shortcodes.php');


require_once( get_template_directory() . '/lib/tome-post-types.php');

// Load Scripts & Styles
require_once( get_template_directory() . '/lib/tome-enqueue-scripts.php' );
require_once( get_template_directory() . '/lib/tome-enqueue-styles.php' );

// Theme image sizes, and markup changes to the output images.
require_once( get_template_directory() . '/lib/tome-media-settings.php' );

require_once( get_template_directory() . '/lib/tome-navigation.php' );

require_once( get_template_directory() . '/lib/tome-comments.php' );

require_once( get_template_directory() . '/tome-editor-buttons/tome-mce-buttons.php' );

// Tooltips… 
// Todo: Review Needed
function tooltip_pre_save_handler( $post_id ) {

	// check if this is to be a new post
	if( $post_id != 'new' ) {
		return $post_id;
	}

	// Create a new post
	$post = array(
		'post_status'  => 'draft' ,
		'post_title'  => 'A title, maybe a $_POST variable' ,
		'post_type'  => 'post' ,
	);

	// insert the post
	$post_id = wp_insert_post( $post );

	// save the fields to the post
	do_action( 'acf/save_post' , $post_id );

	// return the new ID
	return $post_id;

}

add_action('acf/pre_save_post' , 'tooltip_pre_save_handler', 5);

function add_shortcode_ajax() {
	if( $_GET['type'] ) {
		if( $posts = get_posts( array (
			"post_type" => $_GET['type'],
			"posts_per_page" => -1 
		) ) ) {

			if ( $_GET['type'] == 'tome_gallery' )
				$params = " size=\'full-column\'";


			echo "<h2>Click to insert:</h2>";

			echo "<ul>";
			foreach($posts as $post ) {
					$shortcode = sprintf( "'[%s id=\'%s\'%s]'",
						$_GET['type'],
						$post->ID,
						$params
					);
				printf( '<li><a href="javascript:window.send_to_editor(%s); tb_remove();">%s</a></li>',
					$shortcode,
					$post->post_title
				);
			}
			echo "</ul>";

		} else {
			$name = ucfirst( str_replace( "tome_", "", $_GET['type'] ) . "s" );
			$type = $_GET['type'];
			echo "<h3>There are no $name to add.</h3>";
			echo "<p>You can create new $name <a href='/wp-admin/edit.php?post_type=$type'>here</a></p>";
		}
	}
	die;
}

add_action('wp_ajax_add_shortcode', 'add_shortcode_ajax');

?>