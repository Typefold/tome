<?php
/*
Plugin Name: Tome Gallery Plugin
Plugin URI: TBD
Description: A plugin to create Tome Galleries.
Version: 1.0
Author: Agustin Sevilla
Author URI: http://agustinsevilla.com/

License: GPL2

Copyright 2013  Agustin Sevilla  (email : me@agustinsevilla.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/
if ( ! function_exists('tome_gallery_post_type') ) {

// Register Custom Post Type
function tome_gallery_post_type() {

	$labels = array(
		'name'                => _x( 'Tome Galleries', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Tome Gallery', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Gallery', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Gallery: ', 'text_domain' ),
		'all_items'           => __( 'All Galleries', 'text_domain' ),
		'view_item'           => __( 'View Gallery', 'text_domain' ),
		'add_new_item'        => __( 'Add New Gallery', 'text_domain' ),
		'add_new'             => __( 'Add Gallery', 'text_domain' ),
		'edit_item'           => __( 'Edit Gallery', 'text_domain' ),
		'update_item'         => __( 'Update Gallery', 'text_domain' ),
		'search_items'        => __( 'Search Gallery', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'tome_gallery', 'text_domain' ),
		'description'         => __( 'Create a horizontal scrolling gallery.', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'revisions', ),
		'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 10,
		'menu_icon'       	  => 'dashicons-format-gallery',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	);

	register_post_type( 'tome_gallery', $args );
}

// Hook into the 'init' action
add_action( 'init', 'tome_gallery_post_type', 0 );

    // Adding Editor Styles & scripts
    function tome_gallery_styles() {
        if ( is_admin() == true && get_current_screen()->base != 'post' )
            return false;
        
        wp_enqueue_style( 'tome-gallery', get_template_directory_uri() . '/tome-deps/tome_post_type_gallery/css/styles.css', array(), '1.0' );
    }
    add_action( 'admin_enqueue_scripts', 'tome_gallery_styles' );

//    add_filter( 'the_content', 'wpautop' , 99);
//    add_filter( 'the_content', 'shortcode_unautop',100 );
}

?>