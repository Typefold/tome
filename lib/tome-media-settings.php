<?php 

// TODO This has got to go...
if ( ! isset( $content_width ) ) {
	$content_width = 800;
}

function tome_add_tags_to_attachments() {
	register_taxonomy_for_object_type( 'post_tag', 'attachment' );
}
add_action( 'init' , 'tome_add_tags_to_attachments' );

//Set up our custom image sizes for Tome.
function tome_setup_image_sizes() {

	if( function_exists('add_theme_support') ) {
		add_theme_support('post-thumbnails');
		//Add Special Tome sizes
		add_image_size( 'full-screen', 2400, 9999, false );
		add_image_size( 'mega-image-size', 1400, 1400, false );
		add_image_size( 'big-header', 1360, 780, true); 
		add_image_size( 'half', 400, 9999, false); 
	}

	update_option( 'thumbnail_size_w', 360, true );
	update_option( 'thumbnail_size_h', 360, true );
	update_option( 'thumbnail_crop', 0 );

	update_option( 'medium_size_w', 420, true );
	update_option( 'medium_size_h', 420, true );
	update_option( 'medium_crop', 0 );
	
	update_option( 'large_size_w', 800, true );
	update_option( 'large_size_h', 800, true );
	update_option( 'large_crop', 0 );

	function tome_user_image_sizes( $sizes ){


		return array_merge( $sizes, array(
			'large' => __('Full Column'),
			'half' => __('Half Column'),
			'full-screen' => __('Full Screen'),
			'full' => __('Original size'),
			// 'mega-image-size' => __('Mega Image'),
			// 'tiny-image-size' => __('Tiny Image'),
		) );
	}

	add_filter('image_size_names_choose', 'tome_user_image_sizes');
}

add_action( 'after_setup_theme', 'tome_setup_image_sizes' );


// HTML5 Semantic Image output
/**
 * Improves the caption shortcode with HTML5 figure & figcaption; microdata & wai-aria attributes
 * 
 * @param  string $empty     Empty
 * @param  array  $attr    Shortcode attributes
 * @param  string $content Shortcode content
 */
function tome_img_caption_shortcode_filter($empty, $attr, $content)
{
	extract(shortcode_atts(array(
		'id'      => '',
		'align'   => 'aligncenter',
		'width'   => '',
		'caption' => ''
	), $attr));
	
	// No caption, no dice... But why width? 
	if ( 1 > (int) $attr['width'] || empty( $attr['caption'] ) ) {
		return '';
	}

 
	if ( $id ) {
		$id = esc_attr( $id );
	}
	 
	// Add itemprop="contentURL" to image
	// do_shortcode( $content ) ?
	$classes = tome_get_html_attr_value($content, 'class' );

	$figureTag = '<figure id="' . $id . '" aria-describedby="figcaption_' . $id . '" class="wp-caption ' . esc_attr($align) . ' '.$classes.'" itemscope itemtype="http://schema.org/ImageObject">';
	$figCaptionTag = '<figcaption id="figcaption_'. $id . '" class="wp-caption-text" itemprop="description">' . $caption . '</figcaption>';
	$content = preg_replace('/<img/', '<img itemprop="contentURL"', $content);
	$content = preg_replace('/class="([^"]*)"/i', '', $content);
	$content = preg_replace('/width="([^"]*)"/i', '', $content);
	$content = preg_replace('/height="([^"]*)"/i', '', $content);

	return $figureTag . $content . $figCaptionTag . '</figure>';
}
add_filter( 'img_caption_shortcode', 'tome_img_caption_shortcode_filter', 10, 3 );

// Returns html attributes from a string
function tome_get_html_attr_value( $html_content, $attr_name ) {
	$array = array();
	preg_match( '/'.$attr_name.'="([^"]*)"/i', $html_content, $array ) ;
	return $array[1];
}

// Galleries - Get Rid of default styles
add_filter( 'use_default_gallery_style', '__return_false' );