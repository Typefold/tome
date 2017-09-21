<?php

/**
* Registers a new post type
* @uses $wp_post_types Inserts new post type object into the list
*
* @param string  Post type key, must not exceed 20 characters
* @param array|string  See optional args description above.
* @return object|WP_Error the registered post type object, or an error object
*/
function tome_register_chapter_comment() {

	$args = array(
		'hierarchical'        => false,
		'description'         => 'description',
		'taxonomies'          => array(),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => false,
		'show_in_admin_bar'   => false,
		'menu_position'       => null,
		'menu_icon'           => null,
		'show_in_nav_menus'   => false,
		'publicly_queryable'  => true,
		'exclude_from_search' => true,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => true,
		'capability_type'     => 'post',
		'supports'            => array()
	);

	register_post_type( 'chapter_comment', $args );
}

add_action( 'init', 'tome_register_chapter_comment' );
	

function tome_comment( $atts, $content = null ) {
	$atts = shortcode_atts( array(
		'id' => ''
	), $atts );

	return '<span class="hello">' . $content . '</span>';
}
add_shortcode( 'tome_comment','tome_comment' );


function create_comment() {

	$postarr = array(
		'author' => get_current_user_id(),
		'content' => $_POST['comment'],
		'post_parent' => $_POST['parent']
	);

	wp_insert_post( $postarr, $wp_error );
}