<?php

$args = array(
	'p' => $sc_atts->id,
	'post_type' => 'tome_gallery'
	);

$query = new WP_Query( $args );

?>

<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
	global $post;
	$gallery_size = $sc_atts->size;
	$gallery_size = " " . $gallery_size;
	$cover_photo = get_field('gallery_cover_photo');
	$cover_photo_url = $cover_photo['url'];
	$gallery_items = get_field( 'gallery_slide' );
	$cover_caption = $cover_photo['caption'];

	echo ( get_field( 'gallery_slide' ) == false ) ? '<p>You didn"t add any pictures to the gallery' : "";

	$output = "<div class='tome-gallery $gallery_size'>";

		$output .= "<a href='$cover_photo_url' data-lightbox='image-$sc_atts->id' data-title='$cover_caption'>";
			$output .= "<i class='dashicons dashicons-format-gallery'></i>";
			$output .= "<span class='title'>$post->post_title</span>";
			$output .= "<img src='$cover_photo_url' alt=''/>";
		$output .= "</a>";

		$output .= "<div class='hidden-list'>";

		foreach ( $gallery_items as $item ) {
				$gallery_item_url = $item['url'];
				$caption = $item['post_excerpt'];
				$image_meta = get_post( $item['id'] );
				$caption = $image_meta->post_excerpt;
				$output .= "<a href='$gallery_item_url' data-lightbox='image-$sc_atts->id' data-title='$caption' class='hidden' title=''></a>";
		}

		$output .= "</div>";
	$output .= "</div>";

	echo $output;

	endwhile;

	else:
		echo '<p> No gallery was found </p>';

	endif;

	wp_reset_query();