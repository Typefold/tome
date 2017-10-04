<?php 
// Dashboard Tome Multisite

add_action('admin_menu', 'tome_add_dashboard_settings', 99);

function tome_add_dashboard_settings() {
	// Add Tome Cover Page Settings Options
	if( function_exists('acf_add_options_page') ) {
		
		acf_add_options_page(array(
			'page_title'    => 'Cover Page Settings',
			'menu_title'    => 'Cover Page',
			'menu_slug'     => 'tome-cover-settings',
			'capability'    => 'edit_posts',
			'parent_slug'   => 'tome-dashboard'
		));

	}
}

// Profile Page
// Disable the color picker for users UI
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

// Don't show users wordpress update notification
function tome_remove_update_nag() {
	remove_action( 'admin_notices', 'update_nag', 3 );
}

add_action( 'admin_menu', 'tome_remove_update_nag' );

// Add Meta box
function tome_add_media_attachment_box() 
{
	add_meta_box("media_meta", "Media Page", "media_meta", "attachment", "side", "high");
}

add_action( 'add_meta_boxes', 'tome_add_media_attachment_box' );

// New Attachment Option: Exclude from media page.
function media_meta() {
	global $post;
	printf( "Exclude from Media Page? &nbsp; <input type='checkbox' name='media_page_exclude' value='1' %s />",
		get_post_meta( $post->ID, 'media_page_exclude', true) == 1 ? "checked='checked'" : ""
	);
}