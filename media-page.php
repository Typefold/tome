<?php
/**
 * The template for the Media Page.
 * Template Name: Media Page
 * @package WordPress
 * @subpackage Originator_F4_Tome
 * @since Originator_F4_Tome 1.0
 */


get_header();


$attachments = get_posts( array(
	"post_type" => "attachment",
	"posts_per_page" => -1,
	"post_mime_type" => 'image/jpeg,image/gif,image/jpg,image/png',
	"meta_key" => "media_page_exclude",
	"meta_value" => 0
	) );

$tags = array();

foreach( $attachments as $attachment ) {
	$post_tags = array_merge( $tags, wp_get_post_tags( $attachment->ID ) );
	foreach($post_tags as $tag) {
		$tags[$tag->term_id] = $tag;
	}
}


function print_embed_media_gallery_item( $post ) {

	$video_embed_script = get_field( 'tome_media_embed_script' );
	$media_thumbnail = get_template_directory_uri() . '/img/video_placeholder.png';


	if ( has_post_thumbnail( $post ) )
		$media_thumbnail = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );


	// look for link to a media in embedded code
	preg_match_all( '/(https:\/\/.*?)["\']/', $video_embed_script, $matches );

	?>


	<div class="isotope-item<?php echo ' ' . $image_tags; ?>">

		<a href="<?php echo $matches[1][0]; ?>" class="visible">
			<img data-src="<?php echo $media_thumbnail ?>" class="lazyload">
		</a>

	</div>

<?php } ?>



<div class="media-page isotope-wrapper">
	<div class="container filters-bar">
		<div class="row">


			<div class="media-filters media-filters-tag small-6 columns"><?php //small-8 columns ?>

				<span class="filter-name tags-label">Tags:</span>

				<div class="select-wrapper">				
					<select class="tag-filters" multiple>
						<option class="active" value="*">All</option>
						<?php foreach( $tags as $tag ): ?>
							<option value=".<?php echo $tag->slug ?>"><?php echo ucfirst( $tag->name ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

			</div> <!-- div.media-filters-->
			
			<div class="small-2 columns">
				<span class="show-tags">Show All Tags</span>
			</div>

		</div><!-- div.row -->
	</div><!-- div.container.filters-bar -->

	<div class="tags-cloud">
		<div class="container">			
			<div class="row">				
				<?php foreach( $tags as $tag ): ?>
					<span class="tag" data-tag=".<?php echo $tag->slug ?>"><?php echo ucfirst( $tag->name ); ?></span>
				<?php endforeach; ?>
			</div>
		</div>
	</div>


	<!-- Isotope gallery -->
	<div class="isotope-box loading">
		<div class="media-loader"></div>

		<?php
		$args = array(
			'post_type' => array('attachment', 'tome_media'),
			'posts_per_page' => -1,
			'post_status' => 'any',
			);

		$media_q = new WP_Query($args);


		if ( $media_q->have_posts() ) : while ( $media_q->have_posts() ) : $media_q->the_post();


			$image_tags = tome_get_gallery_image_tags( $post );


			if ( $post->post_type == 'tome_media' ):

				print_embed_media_gallery_item( $post );

			else:

				$src = wp_get_attachment_image_src( $post->ID, 'large' );
				$image_width = $src[1];
				$image_height = $src[2];
				$portrait_class = tome_get_portrait_class( $image_width, $image_height ); // portraitimg or ""

				?>

				<div class="isotope-item<?php echo $portrait_class . ' ' . $image_tags; ?>">

					<a href="<?php echo $src[0]; ?>" class="visible">
						<img data-src="<?php echo $src[0]; ?>" class="lazyload" alt="">
					</a>

				</div>

			<?php endif; ?>

		<?php
		endwhile;
		else:
		endif;

		wp_reset_query(); ?>


	</div>

</div><!-- .media-page -->


<?php get_footer(); ?>
