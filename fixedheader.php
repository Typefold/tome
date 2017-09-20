<?php

	global $tome_content_widths;
	global $post;
	global $is_fixed_header;
	$is_fixed_header = true;
	

	$header_style_option = get_field('tome_header_style_options', $post->ID);

	switch ( $header_style_option ) {
		case 'featured_img':
			$header_caption = get_field('tome_header_caption_text', $post->ID);
		break;
		case 'embed':
			$tome_chapter_header_option_media = get_field('tome_header_embed', $post->ID);
		break;
		case 'place':
			$tome_chapter_header_option_place = get_field('tome_header_place', $post->ID);
		break;
		case 'map':
			$tome_chapter_header_option_map = get_field('tome_header_map', $post->ID);
		break;
		case 'video_loop':
			$webm_src = get_field('tome_header_video_bg_webm', $post->ID);
			$ogv_src = get_field('tome_header_video_bg_ogv', $post->ID);
			$fallback_src = get_field('tome_header_fallback_image', $post->ID);
		break;
		default:
		break;
	}


	// Build the text part of the header (same for all options)
	$chapter_title = get_the_title();
	$hgroup = '<h1 class="entry-title">' . $chapter_title . '</h1>';
	$custom = get_post_custom($post->ID);
	$byline = $custom["chapter_byline"][0];


	// Optionally display Author
	if(get_field('tome_header_byline', $post->ID) == true || ! empty( $byline ) ) {

		if(get_field('tome_header_authorname', $post->ID)) {
			$author_name = get_field('tome_header_authorname', $post->ID);
		} else {
			//Get the author ID, in case it is not specified
			$author_name = get_the_author_meta( 'display_name' , $post->post_author );
		}

		if ( ! empty( $byline ) ) {
			$author_name = $byline;
		}

		$hgroup .= '<h2 class="author">' . $author_name . '</h2>';
	}

switch ($header_style_option) {
	case 'none':
		?>
		<div class="chapter-header-media chapter-header-media--none"></div>
		<?php
		break;

	case 'video_loop' :
		?>
		<div class="chapter-header-media chapter-header-media--video">
		  <div class="video-header-loop">
			<div data-videocover <?php 
				if( $webm_src ) {
					echo "data-webm=\"$webm_src\" ";
				}
				if( $ogv_src ) {
					echo "data-ogv=\"$ogv_src\" ";
				}
				if( $fallback_src ) {
					echo "data-fallback=\"" . $fallback_src['sizes']['mega-image-size'] ."\" ";
				}?>
				></div>		  	
		  </div>
		</div>
		<?
		break;

	case 'featured_img':
		$header_image = get_field('header_image', $post->ID);
		?>
		
		<div class="chapter-header-media chapter-header-media--featured-image" style="background-image:url(<?php echo $header_image['url']; ?>);">
			<?php if ( !empty( $header_caption ) ) { ?>
				<p class="caption"><?php echo $header_caption; ?></p>
			<?php } ?>
		</div>

		<?php
		break;
	
	case 'embed':
		$meta = get_post_meta($tome_chapter_header_option_media);
		if ( $meta["media_type"][0] == 'video' ) {
			$embedHTML = $meta["external_source"][0];
			str_replace( 'vimeo', 'player.vimeo', $embedHTML );
			$iframe = wp_oembed_get( $embedHTML );
		} elseif ( $meta["media_type"][0] == 'embed' ) {
			$iframe = $meta["external_source"][0];
		}
		$header_caption = get_field('tome_header_caption_text', $post->ID);
		?>

		<div class="chapter-header-media chapter-header-media--embed">
			<div class="container">
				<div class="flex-video widescreen">
					<?php echo $iframe; ?>
				</div>						
			</div>

			<p class="caption"><?php echo $header_caption; ?></p>
		</div>

		<?php
		break;
	
	case 'place':
		$place_id = $tome_chapter_header_option_place;
		Tome_Map_Frontend::print_place( $place_id, false, false );
	break;

	case 'map':
		$map_id = $tome_chapter_header_option_map;
		Tome_Map_Frontend::print_map( $map_id, false );
	break;
	
	case 'allplaces':
		
		$meta_place = get_post_meta($tome_chapter_header_option_place);
		
		if($meta_place["tome_place_lat"][0] != "" and $meta_place["tome_place_long"][0] != "") {
			$place_url = get_permalink($tome_chapter_header_option_place);
			?>

		<div class="full all-places-map">
		  	<div class="full stuck">
				<div id="map-canvas-all" class="map-canvas all-place-map-container tome-place-map tome-place-header" style="width:100%;height:100%" data-id="all"></div>
			</div>
			<div class="map-location-unit">
				<span id="text"></span>
				<a href="#" class="small button">Visit Place</a>
			</div>
		</div>

			<?php
			// This will add the necessary scripts for Tome Places
			function add_tome_places_js() {
				$url = get_template_directory_uri() . '/tome-deps/tome_post_type_place/js/tome_place_maps.js';
				wp_register_script('tome_place_js', $url, array('jquery', 'google_maps'),'3.0',true);

				wp_enqueue_script('google_maps');
				wp_enqueue_script('tome_place_js');
			}
			add_action('wp_enqueue_scripts', 'add_tome_places_js');

		} 
		break;
	
}

if($tome_chapter_header_option != 'allplaces') :

?>
		<div class="container">
			<div class="row">
				<div class="<?php echo $tome_content_widths ?>">
					<div class="chapter-header hgroup">
						<?php echo $hgroup; ?>
						<hr class="title-divider">
					</div>

				</div>
			</div>
		</div>
<?php

endif;
$is_fixed_header = false;

?>