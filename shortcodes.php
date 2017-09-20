<?php
function tome_embed( $atts, $content = null ) {
	global $wp_embed; // see wp-includes/class-wp-embed.php

	extract( shortcode_atts( array(
	'src' => '',
	'caption' => '',
	), $atts ) );

	$output = '<div class="tome-embed">';
		$output .= $wp_embed->shortcode( null, $src );
		if ( !empty( $caption ) )
			$output .= '<h3 class="media-caption">'.$caption.'</h3>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'tome_embed', 'tome_embed' );




function foundation4_row( $atts, $content = null ) {
	
	$output = '<div class="row">';
	$output .= do_shortcode($content);
	$output .= '</div>';
	
	return $output;
}
add_shortcode('row', 'foundation4_row');


function foundation4_columns( $atts, $content = null ) {
	extract( shortcode_atts( array(
	'large' => 'large-6', /* large-1 through large-12 are valid */
	'small' => '', /* small-1 through small-12 are valid */
	), $atts ) );
	
	$output = '<div class="'.$small.' '. $large. ' columns">';
	$output .= $content;
	$output .= '</div>';
	
	return $output;
}
add_shortcode('column', 'foundation4_columns');


/*
	Zurb Orbit shortcode by Agustin M. Sevilla III
*/ 
function foundation4_orbit( $atts, $content = null ) {
	
	$output = '<ul data-orbit>';
	$output .= do_shortcode(shortcode_unautop($content));
	$output .= '</ul>';
	
	return $output;
}
add_shortcode('orbit', 'foundation4_orbit');

function foundation4_orbit_slide( $atts, $content = null ) {
	extract( shortcode_atts( array(
	'caption' => '', 
	'slide_name' => '', 
	), $atts ) );
	$output = '<li';
	if ($slide_name != '') $output .=  ' data-orbit-slide="' . $slide_name . '"';	
	$output .= '>';
	$output .= '		' . $content;
	if ($caption != '') $output .=  '<div class="orbit-caption">' . $caption . '</div>';
	$output .= '</li>';

	// 	<img src="../img/demos/demo1.jpg" />
	// 	<div class="orbit-caption">...</div>
	// </li>
	return $output;
}
add_shortcode('orbit_slide', 'foundation4_orbit_slide');

// Gallery shortcode
// Override standard shortcode
remove_shortcode('gallery', 'gallery_shortcode');
add_shortcode('gallery', 'f4_block_grid_gallery_shortcode');

function f4_block_grid_gallery_shortcode($attr) {
	$post = get_post();

	static $instance = 0;
	$instance++;

	if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) )
			$attr['orderby'] = 'post__in';
		$attr['include'] = $attr['ids'];
	}

	// Allow plugins/themes to override the default gallery template.
	$output = apply_filters('post_gallery', '', $attr);
	if ( $output != '' )
		return $output;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post ? $post->ID : 0,
		'itemtag'    => 'li',
		'icontag'    => 'div',
		'captiontag' => 'p',
		'columns'    => 4,
		'columnsmd'  => 3,
		'columnssm'  => 2,
		'size'       => 'big-square-thumb',
		'include'    => '',
		'exclude'    => '',
		'link'       => ''
	), $attr, 'gallery'));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$icontag = tag_escape($icontag);
	$valid_tags = wp_kses_allowed_html( 'post' );
	if ( ! isset( $valid_tags[ $itemtag ] ) )
		$itemtag = 'li';
	if ( ! isset( $valid_tags[ $captiontag ] ) )
		$captiontag = 'div';
	if ( ! isset( $valid_tags[ $icontag ] ) )
		$icontag = 'p';

	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$size_class = sanitize_html_class( $size );
	$gallery_div = "<ul id='$selector' class='gallery galleryid-{$id} small-block-grid-{$columnssm} medium-block-grid-{$columnsmd} large-block-grid-{$columns} gallery-size-{$size_class}' data-clearing>";
	$output = $gallery_div;

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {
		if ( ! empty( $link ) && 'file' === $link )
			$image_output = wp_get_attachment_link( $id, $size, false, false );
		elseif ( ! empty( $link ) && 'none' === $link )
			$image_output = wp_get_attachment_image( $id, $size, false );
		else
			$image_output = wp_get_attachment_link( $id, $size, true, false );

		$image_meta  = wp_get_attachment_metadata( $id );

		$orientation = '';
		if ( isset( $image_meta['height'], $image_meta['width'] ) )
			$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';

		$output .= "<{$itemtag} class='gallery-item'>";
		$output .= "
			<{$icontag} class='gallery-icon {$orientation}'>
				$image_output
			</{$icontag}>";

		// if ( $captiontag && trim($attachment->post_excerpt) ) {
		// 	$output .= "
		// 		<{$captiontag} class='wp-caption-text gallery-caption'>
		// 		" . wptexturize($attachment->post_excerpt) . "
		// 		</{$captiontag}>";
		// }
		$output .= "</{$itemtag}>";
	}

	$output .= "
			<br style='clear: both;' />
		</ul>\n";

	return $output;
}
// Buttons
function buttons( $atts, $content = null ) {
	extract( shortcode_atts( array(
	'type' => 'radius', /* radius, round */
	'size' => 'medium', /* small, medium, large */
	'color' => 'blue',
	'nice' => 'false',
	'url'  => '',
	'text' => '', 
	), $atts ) );
	
	$output = '<a href="' . $url . '" class="button '. $type . ' ' . $size . ' ' . $color;
	if( $nice == 'true' ){ $output .= ' nice';}
	$output .= '">';
	$output .= $text;
	$output .= '</a>';
	
	return $output;
}

add_shortcode('button', 'buttons'); 


// Alerts
function alerts( $atts, $content = null ) {
	extract( shortcode_atts( array(
	'type' => '	', /* warning, success, error */
	'close' => 'false', /* display close link */
	'classes' => 'false', /* pass arbitrary classes */
	'text' => '', 
	), $atts ) );
	
	$output = '<div class="fade in alert-box '. $type . ' '.$classes.'">';
	
	$output .= $text;
	if($close == 'true') {
		$output .= '<a class="close" href="#">×</a>';
	}
	$output .='</div>';
	
	return $output;
}

add_shortcode('alert', 'alerts');

// Panels
function panels( $atts, $content = null ) {
	extract( shortcode_atts( array(
	'type' => '	', /* warning, success, error */
	'close' => 'false', /* display close link */
	'text' => '', 
	), $atts ) );
	
	$output = '<div class="panel">';
	$output .= $text;
	$output .= '</div>';
	
	return $output;
}


add_shortcode('panel', 'panels');

// set default link type to attachment link 
add_action( 'admin_init', 'image_default_link_type_post' );

function image_default_link_type_post() {
	update_option( 'image_default_link_type', 'post' );
}

/*
// Add shortcode for displaying image in posts instead of having to insert image html into the post body in the post editor

function tome_image_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'id' => null,
		'size' => 'full',
		'align' => null,
		'caption' => true,
		'title' => null,
		'class' => null,
		'link' => null
	), $atts ) );
	//return print_r($atts, true);

	if( $id ) {
		// MULTI LINGUAL
		$text_id = $id;
		if( isset( $_GET['lang'] ) ) {
			$translation = get_posts( array (
										"post_type" => "attachment_ml",
										"post_parent" => $id,
										"meta_key" => "language",
										"meta_value" => $_GET['lang']

									) );

			if( isset($translation[0] ) )
				$text_id = $translation[0]->ID;
		} 

		$caption_text = get_post_field( 'post_excerpt', $text_id );

		$alt_text = get_post_meta( $text_id, '_wp_attachment_image_alt', true );

		$image = wp_get_attachment_image_src( $id, $size );

		return sprintf('<figure class="%salign%s" style="width: %s"><a href="%s"><img src="%s" alt="%s" title="%s" /></a>%s</figure>',
			$class ? $class . " " : "",
			$align,
			$image[1] . "px",
			$link,
			$image[0],
			$alt_text ? $alt_text : get_the_title( $text_id ),
			$title ? $title : get_the_title( $text_id ),
			$caption && $caption_text ? "<figcaption>" . $caption_text . "</figcaption>" : ""

		);
	}

	return null;
}

add_shortcode( 'image', 'tome_image_shortcode' );

if ( get_user_option('rich_editing') == 'true') {
	add_filter("mce_external_plugins", "add_tome_tinymce_plugins");
}

// Add our shortcode plugin to TinyMCE
function add_tome_tinymce_plugins($plugin_array) {
	$plugin_array['tome_shortcodes'] =  get_bloginfo('template_url') . '/js/tinymce/tome_shortcodes.js';
	return $plugin_array;
}

function image_shortcode_ajax() {
	if( isset( $_GET['shortcode'] ) ) {
		//preg_match( '/^image id=&quot;(.*)&quot; size=&quot;(.*)&quot; align=&quot;(.*)&quot%3$/', $_GET['shortcode'], $matches );

		preg_match( '/^image id="(.*)" size="(.*)" align="(.*)"$/', html_entity_decode( urldecode( $_GET['shortcode'] ) ), $matches );

		$atts = array(
			'id' => $matches[1],
			'size' => $matches[2],
			'align' => $matches[3],
			'title' => $_GET['shortcode'],
			'class' => 'tome-image'
		);
		//echo( print_r ($atts ) );

		echo tome_image_shortcode( $atts );
		die();
		}
	}
add_action('wp_ajax_image_shortcode', 'image_shortcode_ajax');



// Insert short code into post instead of image html
add_filter( 'media_send_to_editor', 'tome_insert_shortcode_in_post', 10, 3 );

function tome_insert_shortcode_in_post( $html, $id, $attachment ) {
	$post = get_post( $id );
	//return(implode(array_keys($attachment), " "));

	if ( 'image' == substr( $post->post_mime_type, 0, 5 ) ) {
		return sprintf('[image id="%d" size="%s" align="%s" link="%s"]',
				$id,
				$attachment['image-size'],
				$attachment['align'],
				$attachment['url']
			);
	}
	die;

	return $html;

}
*/

// Cover page button shortcode
function tome_begin_button( $atts, $content = null ) {
	$first_chapter = get_posts( array( 
							'post_type' => 'chapter',
							'posts_per_page' => 1,
							'orderby' => 'menu_order',
							'order' => 'ASC'
						) );
	if( isset( $first_chapter[0] ) ):
		if($_GET['lang']) {
				$ml_lang = "?lang=" . $_GET['lang'];
		}
		return sprintf('<div class="cta"><a class="button medium radius" href="%s'.$ml_lang.'">%s</a></div>',
							get_permalink( $first_chapter[0]->ID ),
							$content

			);
	else:
		return "The begin button will appear here after at least one chapter has been published";
	endif;
}

add_shortcode( 'begin_button', 'tome_begin_button' );


function tome_place_shortcodes($atts, $content = null) {
	extract( shortcode_atts( array(
	'id' => '', 
	), $atts ) );

	ob_start();
	Tome_Map_Frontend::print_place( $id, true );
	$output = ob_get_contents();
	ob_end_clean();

	return $output;

}
add_shortcode( 'tome_place', 'tome_place_shortcodes' );

//find images shortcodes and save as customfields
add_action('save_post', 'save_tome_images_as_meta');

function save_tome_images_as_meta( $post_id ) {
	
	$post = get_post( $post_id );

	delete_post_meta( $post_id, "_tome_place_id" );
	
	preg_match_all( '/\[image id="(\d+)".*\]/', $post->post_content, $matches );

	foreach( $matches[1] as $image_id )
		add_post_meta( $post->ID, "_tome_image_id", $image_id, false );

}

function image_appears_in( $image_id ) {
	$inline_posts = get_posts( array(
				"post_type" => "chapter",
				"posts_per_page" => -1,
				"meta_key" => "_tome_image_id",
				"meta_value" => $image_id
		) );

	$thumbnail_posts = get_posts( array(
				"post_type" => "chapter",
				"posts_per_page" => -1,
				"meta_key" => "_thumbnail_id",
				"meta_value" => $image_id
		) );

	return array_merge($inline_posts, $thumbnail_posts);

}

?>