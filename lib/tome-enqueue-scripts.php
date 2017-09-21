<?php
/* 
	Enqueue JS for frontend and admin 
*/

/* Register all JS assets here */

function tome_js_register() {

	$themeUri = get_template_directory_uri();

	$cpto_options = unserialize('a:6:{s:23:"show_reorder_interfaces";a:13:{s:4:"post";s:4:"show";s:10:"attachment";s:4:"hide";s:14:"tome_reference";s:4:"hide";s:17:"tome_bibliography";s:4:"hide";s:7:"chapter";s:4:"show";s:8:"tome_map";s:4:"hide";s:12:"tome_gallery";s:4:"hide";s:19:"tome_external_media";s:4:"hide";s:15:"chapter_comment";s:4:"show";s:10:"tome_place";s:4:"hide";s:10:"tome_media";s:4:"hide";s:11:"translation";s:4:"show";s:13:"attachment_ml";s:4:"show";}s:8:"autosort";i:1;s:9:"adminsort";i:1;s:17:"archive_drag_drop";i:1;s:10:"capability";s:13:"publish_pages";s:21:"navigation_sort_apply";i:1;}');

	update_option( 'default_comment_status', 'closed' );
	update_option( 'cpto_options', $cpto_options );
	update_option( 'CPT_configured', 'TRUE' );

	//Font Size/Contrast Settings
	wp_register_script( 'tome_script', $themeUri . '/dist/tome.js', array('jquery'), '1.5', true);
	
	wp_register_script( 'foundation', $themeUri . '/js/foundation/foundation.min.js', array('jquery'), '1.1', true);
	wp_register_script( 'foundation-init', $themeUri . '/js/f4-init.js', array('foundation', 'slick-js'), '1.1', true);

	wp_register_script( 'slick-js', $themeUri . '/js/plugins/slick.js', array('jquery'), '1.1', true );
	wp_register_style( 'slick-css', $themeUri . '/css/slick.css' );

	//Register Google Maps v3 JS API
	wp_register_script('google_maps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=places',array(),'3.0',true);
	wp_register_script( 'tome_maps', $themeUri . '/tome-deps/tome_maps/js/main.js', array('jquery'), true );
	
	//Register Place Maps JS, with jQuery and Google Maps as Dependencies
	wp_register_script('tome_place_js', $themeUri. '/tome-deps/tome_post_type_place/js/tome_place_maps.js', array('jquery', 'google_maps'),'3.0',true);

	//Cover JS
	wp_register_script( 'tome_cover_script', $themeUri . '/js/tome.cover.js', array('jquery'), '0.1', true);

	// Select2 Plugin
	// usage: ['embed media modal filter, tome admin dashboard ]
	wp_register_script( 'select2-js', $themeUri . '/js/plugins/select2.min.js', array('jquery'), '0.1', true);
	wp_register_style( 'select2-css', $themeUri . '/css/select2.min.css' );


	// LazyLoad plugin used in media page
	wp_register_script( 'lazysizes', $themeUri . '/js/lazysizes.min.js', array('jquery'), '0.1', true);

	// Media page lightbox
	wp_register_style( 'media-page-lightbox', $themeUri . '/css/lightgallery.min.css');
	wp_register_style( 'media-page-lightbox-transition', $themeUri . '/css/lg-transitions.min.css');
	wp_register_script( 'media-page-lightbox-script', $themeUri . '/js/plugins/lightgallery-all.min.js', array('jquery'), '0.1', true);

	// tome gallery lightbox
	wp_register_script( 'tome-gallery-lightbox', $themeUri . '/lib/lightbox2/js/lightbox-plus-jquery.min.js', array('jquery'), '0.1', true);
	wp_register_style( 'tome-gallery-lightbox', $themeUri . '/lib/lightbox2/css/lightbox.min.css');

	//New Places Code
	//Register the Google Loader API
	wp_register_script('google_loader', 'https://www.google.com/jsapi',array('jquery'),'1.0', true);    
	wp_register_script( 'tome-places-frontend', get_template_directory_uri() . '/js/tome-places-2.0.js', array('google_loader', 'jquery'), '0.1', true );

}

add_action('wp_enqueue_scripts', 'tome_js_register');

//Register these for the backend, but do not enqueue them all!
add_action('admin_enqueue_scripts', 'tome_js_register');

/**
 * Enqueue the scripts needed in the admin area
**/
function tome_js_frontend( ){

	global $post;

	if ( is_admin() == true )
		return;

	wp_enqueue_script( 'tome_script' );
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'tome-gallery-lightbox' );
	wp_enqueue_script( 'tome-gallery-lightbox' );


	if ( $post->post_type == 'page' && $post->post_name == 'media' ) {
		wp_enqueue_style( 'media-page-lightbox' );
		wp_enqueue_style( 'media-page-lightbox-transition' );
		wp_enqueue_script( 'media-page-lightbox-script' );
	}


	// wp_enqueue_script('tome_place_js');

	if ( 'chapter' === $post->post_type ) {
		wp_enqueue_script( 'tome_place_js'); //tome_place_maps
	}

	if ( 'post' == $post-> post_type ) {
		wp_enqueue_script( 'tome_place_js');
	}

	// homepage scripts
	if( is_home() ) { wp_enqueue_script( 'tome_cover_script' ); }

	if ( 'page' === $post->post_type ) {
		wp_enqueue_script( 'tome-places-frontend');
	}

	if ( get_page_template_slug() == 'media-page.php' ) {
		$media_page_scrips_deps = array('jquery', 'select2-js', 'isotope', 'media-page-lightbox-script');

		wp_enqueue_script( 'select2-js' );
		wp_enqueue_style( 'select2-css' );
		wp_enqueue_script( 'lazysizes' );
		wp_enqueue_script( "isotope", get_bloginfo( 'template_directory' ) . "/js/plugins/isotope2.pkgd.min.js" );

		wp_enqueue_style( "media-page", get_bloginfo( 'template_directory' ) . "/css/media-page.css" );
		wp_enqueue_script( "media-page", get_bloginfo( 'template_directory' ) . "/js/media-page.js", $media_page_scrips_deps );
	}
}

add_action('wp_enqueue_scripts', 'tome_js_frontend');

// TODO Include this file directly here
require_once( get_template_directory() . '/tome-deps/tome_maps/class-tome-maps-frontend.php');

/**
 * Enqueue the scripts needed in the admin area
 * Snippet Name: Add admin script on custom post types
 * Snippet URL: http://www.wpcustoms.net/snippets/add-admin-script-on-custom-post-types/
**/
function tome_add_admin_scripts( $hook ) {

	global $post;

	$themeUri = get_template_directory_uri();
	$screen = get_current_screen();

	if ( $hook == 'post-new.php' || $hook == 'post.php' ) {

		if ( 'chapter' === $post->post_type && $screen->base == 'post' ) {

			wp_register_script('tome_chapter_admin_js',  $themeUri . '/tome-deps/tome_post_type_chapter/js/tome-chapter-admin.js', array('jquery'),'1.0',true);
			wp_enqueue_script('tome_chapter_admin_js');

		}

		if ( $screen->post_type === 'tome_place' && $screen->base == 'post' ) {

			wp_register_script('tome_place_js', $themeUri. '/tome-deps/tome_post_type_place/js/tome_place_maps.js', array('jquery', 'google_maps'),'3.0',true);
			wp_enqueue_script('tome_place_js');

		}

		if ( is_edit_page() ) {
			wp_enqueue_script( 'tome_maps' );
			wp_enqueue_script('foundation');
		}

	}


}
add_action( 'admin_enqueue_scripts', 'tome_add_admin_scripts', 10, 1 ); 