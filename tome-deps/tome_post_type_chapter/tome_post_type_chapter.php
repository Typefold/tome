<?php
/*
Plugin Name: Tome Chapters Plugin
Plugin URI: TBD
Description: A plugin to create Tome chapters.
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


function tome_chapter_register() {
 
	$labels = array(
		'name' => _x('Chapters', 'post type general name'),
		'singular_name' => _x('Chapter', 'post type singular name'),
		'add_new' => _x('Add New', 'Chapter'),
		'add_new_item' => __('Add New Chapter'),
		'edit_item' => __('Edit Chapter'),
		'new_item' => __('New Chapter'),
		'view_item' => __('View Chapter'),
		'search_items' => __('Search Chapters'),
		'not_found' =>  __('Nothing found'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => ''
	);


	$args = array(
		'labels' => $labels,
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 10,
		'menu_icon'       	  => 'dashicons-list-view',
		'can_export'          => true,
		'has_archive'         => true,
		'publicly_queryable'  => true,
		'query_var'  		  => true,
		'capability_type'     => 'post',
		'supports' => array('title','editor','comments','revisions','author'),
	  ); 
 
	register_post_type( 'chapter' , $args );
	
	//If there is permalink wonkiness enable this:
	//flush_rewrite_rules();
}

add_action('init', 'tome_chapter_register', 0);


add_action('save_post', 'chapter_byline');

function chapter_byline( $post_id ) {

	$post_type = get_post_type( $post_id );

	if ( $post_type == 'chapter' )
		update_post_meta( $post_id, "chapter_byline", $_POST["chapter_byline"] );
}

function tome_chapter_edit_columns( $columns ) {
        // $columns = array(
            // "cb" => "<input type=\"checkbox\" />",
            // "title" => "Business Name",
            // "description" => "Description",
            // "regdate" => "Reg.Date",
            // "regnum" => "Reg.Num.",
            // "cat" => "Category",
        // ); 

	$columns['cb'] = "<input type=\"checkbox\" />";

	return $columns;
}

add_filter('manage_edit-chapter_columns' , 'tome_chapter_edit_columns', 10, 1);

// TODO : THIS STYLESHEET IS PROBABLY UNNECESSARY? 
// Add Styles
function tome_chapter_admin_scripts(){

	// 1. Styles
	wp_enqueue_style( 'tome-chapter-edit-css' );

}

add_action('admin_enqueue_scripts', 'tome_chapter_admin_scripts');

?>