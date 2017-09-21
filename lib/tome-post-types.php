<?php 


// This function loads the Tome plugins.
function tome_load_post_types() {
	
	$TOME_DEPS_URI = get_template_directory() . '/tome-deps/';

	// Tome Chapter
	if ( ! function_exists('tomechapter_register' ) ) {
		require_once( $TOME_DEPS_URI . 'tome_post_type_chapter/tome_post_type_chapter.php' );  
	}

	// Tome Comment
	if ( ! function_exists('tome_register_chapter_comment' ) ) {
		require_once( $TOME_DEPS_URI . 'tome_post_type_comment/tome_post_type_comment.php' );  
	}

	// Tome Places
	if ( ! function_exists('tome_place_register') ) {
		require_once( $TOME_DEPS_URI . 'tome_post_type_place/tome_post_type_place.php' );  
	}
	// Tome Gallery
	if ( ! function_exists('tome_gallery_post_type') ) {
		require_once( $TOME_DEPS_URI . 'tome_post_type_gallery/tome_post_type_gallery.php' );  
	}

}

//Load up all the Tome Post Types
add_action( 'after_setup_theme', 'tome_load_post_types' );