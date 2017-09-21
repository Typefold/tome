<?php

add_theme_support( 'title-tag' );

// Run wpautop after shortcodes
add_filter( 'the_content', 'wpautop', 100);

///  PREV / NEXT LINKS FOR MEDIA
add_filter( 'get_previous_post_join', 'adjacent_media_join' );
add_filter( 'get_next_post_join', 'adjacent_media_join' );

function adjacent_media_join( $join ) {
	global $post;
	global $wpdb;
	if( in_array( $post->post_type, array("tome_media", "attachment" ) ) )
		return "JOIN $wpdb->postmeta ON(p.ID = $wpdb->postmeta.post_id)";
	else
		return $join;
}


add_filter( 'get_previous_post_where', 'adjacent_media_where' );
add_filter( 'get_next_post_where', 'adjacent_media_where' );

function adjacent_media_where( $where ) {
	global $post;
	global $wpdb;

	if($post->post_type == "tome_media") {
		$args = array(
		'post_parent' => $post->ID,
		'post_type'   => 'attachment', 
		'numberposts' => 1 );
		$child = reset( get_children( $args ) );

		//$where = preg_replace("/post_type = '.*'/", "post_type = 'attachment'", $where);
		// the query date should be the date of the attachment, not the media post, since they're all ordered by attachment
		$where = preg_replace("/post_date (.) '.*'/", "post_date $1 '" . $child->post_date . "'", $where); 
		$where .= " AND post_type = 'attachment'";
		$where .= " AND ($wpdb->postmeta.meta_key = 'media_page_exclude' AND $wpdb->postmeta.meta_value != 1)";
		
	} elseif($post->post_type == "attachment") {
		global $wp_query;
		//print_r($wp_query);
		$wp_query->is_attachment = null;
		$where = str_replace("AND p.post_status = 'publish'", "", $where);
		$where .= " AND $wpdb->postmeta.meta_key = 'media_page_exclude' AND $wpdb->postmeta.meta_value != 1";
	}


	return $where;   
}

add_filter( 'previous_post_link', 'adjacent_media_link', 10, 4 );
add_filter( 'next_post_link', 'adjacent_media_link', 10, 4 );

function adjacent_media_link( $output, $format, $link, $post ) {
	if($post->post_type == "attachment") {
		$parent = get_post($post->post_parent);
		if($parent->post_type == "tome_media") {
			$output = preg_replace('/href=".*"/', 'href="' . get_permalink( $parent->ID ) . '"', $output);
		}
	}
	return $output;

}

// exclude some media from 
add_action('edit_attachment', 'save_tome_media_attachment');
add_action('add_attachment', 'save_tome_media_attachment');

function save_tome_media_attachment( $post_id ){
	$checked = 1;
	if( !isset( $_POST["media_page_exclude"]) )
		$checked = 0;
	update_post_meta( $post_id, "media_page_exclude", $checked );
}
