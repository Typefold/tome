<?php
/**
* The template for the Front Page of the Site.
* Template Name: Front Page
* @package WordPress
* @subpackage Tome Theme
*/

get_header();

?>
<?php

$title_color = get_field('title_color', 'option');
$description_color = get_field('book_description_color', 'option');
$content_position = 'cover'.get_field('content_position', 'option');
$content_background_color = get_field('content_background_color', 'option');
$content_background_opacity = get_field('content_background_opacity', 'option');
$background_type = get_field('background_type', 'option');
$button_link = get_permalink( get_field('button_link', 'option') );


// if ( get_current_blog_id() == 254 ) {
if ( get_current_blog_id() == 254 ) {
	wp_nav_menu(array(
		'menu' => 'cover_menu',
		'menu_class' => 'custom-left-menu'
	));
}


switch ( $background_type ) {
	case 'rand_img':
		$coverImages = get_field('multiple_images_background', 'option');

		// Choose one at random
		$numberOfImages = count($coverImages);
		$randomCoverKey = array_rand($coverImages, 1);
		$cover_url = $coverImages[$randomCoverKey]['url'];
	break;

	case 'video_loop':
		// We are going to construct a video tag with what we find here.
		$webm_src = get_field('video_background_webm', 'option');
		$ogv_src = get_field('video_background_ogv', 'option');
		$mp4_src = get_field('video_background_mp4', 'option');
		$fallback_src = get_field('fallback_image', 'option');

		$video_cover_element = '<div class="video-cover" data-videocover ';

		if( $webm_src ) {
			$webm_src = $webm_src['url'];
			$video_cover_element .= "data-webm=\"$webm_src\" ";
		}
		if( $ogv_src ) {
			$ogv_src = $ogv_src['url'];
			$video_cover_element .= "data-ogv=\"$ogv_src\" ";
		}
		if( $mp4_src ) {
			$mp4_src = $mp4_src['url'];
			$video_cover_element .= "data-mp4=\"$mp4_src\" ";
		}
		if( $fallback_src ) {
			$video_cover_element .= "data-fallback=\"" . $fallback_src ."\" ";
		}
		$video_cover_element .= '></div>';	
	break;
	
	case 'embed_media':
		$video_link = get_field('select_embed_media', 'option');
		echo '<div class="embed-cover">';
			echo $video_link;
		echo '</div>';
	break;


	default:
		$cover_url = get_field('custom_background_image', 'option');
	break;
}

$content_container_style = 'background-color:rgba(255,255,255,'.$contentContainerColorOpacity.'); color: #212121;';

if( $content_background_color ) {
	$container_rgb = hex2rgb( $content_background_color );
	$content_container_style = 'background-color:rgba(' . $container_rgb[0] . ','.$container_rgb[1].','.$container_rgb[2].','.$content_background_opacity.'); ';
}

?>

<?php echo ( $background_type == 'video_loop' ) ? $video_cover_element : ""; ?>

<div class="tome-cover-wrap full feature-image <?php echo $content_position; ?>" <?php if( $cover_url ){echo " style=\"background-image:url($cover_url);\"";}?>>

	<?php

	// locate_template allows us to use variables from this file in the included file
	switch ( $content_position ) {

		case 'coverbottom':
			include(locate_template('template-parts/coverbox-bottom.php'));
		break;

		case 'covertop':
			include(locate_template('template-parts/coverbox-bottom.php'));
		break;

		case 'cover':
			empty_cover();
		break;

		default:
			include(locate_template('template-parts/coverbox-center.php'));
		break;
	}
	?>
</div>

<?php get_footer(); ?>

<?php function empty_cover() {
	$cover_settings_link = '/wp-admin/admin.php?page=tome-cover-settings';
	echo '<h2 class="empty-cover">You can set up your cover page <a href="'.$cover_settings_link.'">here</a>.</h2>';
}
