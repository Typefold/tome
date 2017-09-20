<?php
/**
 * The Template for displaying all single Tome Map.
 *
 * @package WordPress
 * @subpackage Tome Theme
 */

	// This will add the necessary scripts
	function add_places_scripts() {
		$url = get_template_directory_uri() . '/tome-deps/tome_post_type_place/js/tome_place_maps.js';
		wp_register_script('tome_place_js', $url, array('jquery'),'3.0',true);

		wp_enqueue_script('google_maps');
		wp_enqueue_script('tome_place_js');
	}
	add_action('wp_enqueue_scripts', 'add_places_scripts');


	get_header(); 

	global $post;
	$place_id = $post->ID;
	
	$meta_place = get_post_custom();
	if($meta_place["tome_place_lat"][0] != "" and $meta_place["tome_place_long"][0] != "") {
		?>
		<div class="chapter-header-media">
			<div id="map-canvas-single" class="map-canvas place-map-container tome-place-map tome-place-header"
				 data-latitude="<?php echo $meta_place["tome_place_lat"][0]; ?>"
				 data-longitude="<?php echo $meta_place["tome_place_long"][0]; ?>"
				 data-zoom="<?php echo $meta_place["tome_place_zoom"][0]; ?>"
				 data-type="<?php echo $meta_place["tome_place_map_type"][0]; ?>"
				 data-pov='<?php echo $meta_place["tome_place_pov"][0]; ?>' data-id="single"></div>

				 <p class="caption"><?php echo tome_place_location_unit_html( $place_id ) ?></p>
		</div>

		<?php
	}
?>

<div class="content-wrap">
	<div id="primary" class="content-area row">
		<div id="content" class="site-content <?php echo $tome_content_widths ?>" role="main">

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<?php
				get_template_part( 'content', get_post_format() );
				?>
			<?php endwhile; ?>
			<!-- post navigation -->
			<?php else: ?>
			<!-- no posts found -->
			<?php endif; ?>


		</div><!-- #content -->
	</div><!-- #primary -->
</div><!-- content-wrap -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>
