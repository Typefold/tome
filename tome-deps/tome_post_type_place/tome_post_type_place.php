<?php
/*
Plugin Name: Tome Place Post Type Plugin 
Plugin URI: TBD
Description: A plugin to create a special post type representing a place.
Version: 1.0
Author: Agustin Sevilla
Author URI: http://agustinsevilla.com/

License: GPL2

Copyright 2013  Agustin Sevilla  (email : augs@agustinsevilla.com)

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


add_action('init', 'tome_place_register');
 
function tome_place_register() {
 
  $labels = array(
    'name' => _x('Places', 'post type general name'),
    'singular_name' => _x('Place', 'post type singular name'),
    'add_new' => _x('Add New', 'Place'),
    'all_items' => __( 'My Places', 'text_domain' ),
    'add_new_item' => __('Add New Place'),
    'edit_item' => __('Edit Place'),
    'new_item' => __('New Place'),
    'view_item' => __('View Place'),
    'search_items' => __('Search Places'),
    'not_found' =>  __('No places found'),
    'not_found_in_trash' => __('No places found in Trash'),
    'parent_item_colon' => ''
  );
 
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'query_var' => true,
    'menu_icon' => 'dashicons-location',//get_template_directory_uri() . '/tome-deps/tome_post_type_place/img/icon.png',
    'rewrite' => true,
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_position' => 12,
    'supports' => false,
    'has_archive' => true,
    'taxonomies' => array('post_tag')
    ); 
 
	register_post_type( 'tome_place' , $args );
	
	//If there is permalink wonkiness enable this:
	//flush_rewrite_rules();
}

if ( ! function_exists('tome_map_post_type') ) {

// Register Custom Post Type
function tome_map_post_type() {

  $labels = array(
    'name'                => _x( 'Tome Maps', 'Post Type General Name', 'text_domain' ),
    'singular_name'       => _x( 'Tome Map', 'Post Type Singular Name', 'text_domain' ),
    'menu_name'           => __( 'Map', 'text_domain' ),
    'parent_item_colon'   => __( 'Parent Map:', 'text_domain' ),
    'all_items'           => __( 'My Maps', 'text_domain' ),
    'view_item'           => __( 'View Map', 'text_domain' ),
    'add_new_item'        => __( 'Add New Map', 'text_domain' ),
    'add_new'             => __( 'Add New', 'text_domain' ),
    'edit_item'           => __( 'Edit Map', 'text_domain' ),
    'update_item'         => __( 'Update Map', 'text_domain' ),
    'search_items'        => __( 'Search Maps', 'text_domain' ),
    'not_found'           => __( 'Not found', 'text_domain' ),
    'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
  );
  $args = array(
    'label'               => __( 'tome_map', 'text_domain' ),
    'description'         => __( 'A map made up of various places', 'text_domain' ),
    'labels'              => $labels,
    'supports'            => array( 'title' ),
    'taxonomies'          => array(),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => 'edit.php?post_type=tome_place',
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 10,
    'can_export'          => true,
    'has_archive'         => true,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'capability_type'     => 'page',
    'menu_icon' => 'dashicons-location-alt',
  );
  register_post_type( 'tome_map', $args );

}

// Hook into the 'init' action
add_action( 'init', 'tome_map_post_type', 0 );

}


add_action('admin_menu' , 'tome_place_options');

function tome_place_options() {
  add_submenu_page('edit.php?post_type=tome_place', 'Options', 'Options', 'manage_options', 'tome_place_options', 'tome_place_options_page' );
}

/**
 * Options page callback
 */
function tome_place_options_page() {
    ?>
    <div class="wrap">
        <?php screen_icon(); ?>         
        <form method="post" action="options.php">
        <?php
            // This prints out all hidden setting fields
            settings_fields( 'google_maps_api_key' );   
            do_settings_sections( 'tome_place_options_page' );
            submit_button(); 
        ?>
        </form>
    </div>
    <?php
}

add_action( 'admin_init', 'page_init' );

/**
 * Register and add settings
 */
function page_init() {        

  register_setting(
      'google_maps_api_key', // Option group
      'google_maps_api_key'
  );

  add_settings_section(
      'setting_section_id', // ID
      'Google Maps API Key', // Title
      'print_section_info', // Callback
      'tome_place_options_page' // Page
  );  

  add_settings_field(
      'google_maps_api_key', // ID
      'API Key', // Title 
      'google_maps_api_key_callback', // Callback
      'tome_place_options_page', // Page
      'setting_section_id' // Section        
  ); 
}

/** 
 * Print the Section text
 */
function print_section_info()
{
    printf( 'Enter your Google Maps API key if you have one.');
}


function google_maps_api_key_callback()
{
    printf(
        '<input type="text" name="google_maps_api_key" value="%s"  />',
        get_site_option('google_maps_api_key')
    );
}


function maps_api_key() {
  if( $key = get_site_option( "google_maps_api_key" ) )
    return "&key=" . $key;
  else
    return "";
}


function ajax_get_all_places(){
    $args = array(
    'post_type'      => 'tome_place',
    'post_status'     => 'publish',
    'posts_per_page' => -1
    );
    $places = get_posts($args);

    foreach( $places as $post ) {
        $custom_fields = get_post_custom($post->ID);
        $lat = $custom_fields['tome_place_lat'][0];
        $long = $custom_fields['tome_place_long'][0];
        //$formatted_content = wpautop($post->post_content);
        $formatted_content = wpautop($post->post_excerpt);

        $post = get_tome_place_tag_links( $post );
        $post = get_tome_place_featured_in_links( $post );

        if( $chapters = get_posts( array( 
                                "post_type" => "chapter", 
                                "meta_key" => "_tome_place_id",
                                "meta_value" => $post->ID ) ) ) 
        {
            $featured_in = array();
            foreach( $chapters as $chapter)
                $featured_in[] = '<a href="' . get_permalink( $chapter->ID ) . '">' . $chapter->post_title . '</a>';

            $post->featured_in = " Featured In: " . implode(", ", $featured_in);

        } else {
            $post->featured_in = "";
        }

        
        $post->permalink = get_permalink( $post->ID );
        $post->latitude = $lat;
        $post->longitude = $long;
        $post->post_content = $formatted_content;
    }    

    return $places;
}

function ajax_get_map_places($mapID) {

  //This will run through each place in the ACF Repeater
  if( have_rows('places', $mapID) ): 

    $TomeMapPlaces = array();

    while( have_rows('places', $mapID) ): 

      the_row(); 
      $mapPlaceField = get_sub_field('place');
      $tomePlaceCustomMeta = get_post_custom( $mapPlaceField->ID );
      $TomePlace = array(
        'permalink' => get_permalink( $mapPlaceField->ID ),
        'ID' => $mapPlaceField->ID,
        'post_title' => $mapPlaceField->post_title,
        'post_content' => apply_filters('the_content', strip_shortcodes($mapPlaceField->post_excerpt)),
        'latitude' => $tomePlaceCustomMeta['tome_place_lat'][0],
        'longitude' => $tomePlaceCustomMeta['tome_place_long'][0]
      );
      $TomeMapPlaces[] = $TomePlace;

    endwhile;

  endif;
  
  return $TomeMapPlaces;
}

function get_tome_place_tag_links( $post ) {
  if( $post_tags = get_the_tags( $post->ID ) ) {
      $tags = array();
      foreach(  $post_tags as $tag )
          $tags[] = '<a href="' . get_tag_link( $tag->term_id ) . '">' . $tag->name . '</a>';

      $post->tags = " Tags: " . implode(", ", $tags);
  } else {
      $post->tags = "";
  }
  return $post;

}

function get_tome_place_featured_in_links( $post ) {
  if( $chapters = get_posts( array( 
                          "post_type" => "chapter", 
                          "post_status" => "published",
                          "meta_key" => "_tome_place_id",
                          "meta_value" => $post->ID ) ) ) 
  {
      $featured_in = array();
      foreach( $chapters as $chapter)
          $featured_in[] = '<a href="' . get_permalink( $chapter->ID ) . '">' . $chapter->post_title . '</a>';

      $post->featured_in = " Featured In: " . implode(", ", $featured_in);

  } else {
      $post->featured_in = "";
  }
  return $post;
}



// text for along the bottom of the map
function tome_place_location_unit_html( $place_id = false ) {
    $place = get_post( $place_id ); 
    $place = get_tome_place_tag_links( $place );
    $place = get_tome_place_featured_in_links( $place );
    return $place->post_title . $place->featured_in . $place->tags;
  }


?>