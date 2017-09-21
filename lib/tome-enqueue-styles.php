<?php 

/**
 * Enqueue Stylesheets
 */

//TODO: Find all partials loading styles and control them more closely here.
function tome_scripts() {
    wp_enqueue_style( 'style-name', get_stylesheet_uri() );
}

add_action( 'wp_enqueue_scripts', 'tome_scripts' );

function tome_add_editor_styles() {
	add_editor_style( 'css/admin-styles.css' );
}

add_action( 'init', 'tome_add_editor_styles' );