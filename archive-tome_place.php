<?php
/**
 * The template for displaying all Tome Places.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Tome Theme
 */

// This will add the necessary scripts for Tome Places
function add_tome_places_scripts() {

	wp_dequeue_script('google_maps');
	wp_dequeue_script('tome_place_js');

	//This will load the script and it's dependencies.
	wp_enqueue_script( 'tome-places-frontend' );
}
add_action('wp_enqueue_scripts', 'add_tome_places_scripts');

get_header(); ?>

	<div id="primary">
		<div id="content" class="site-content" role="main">
			
			<?php if ( have_posts() ) : ?>
				
				<div id="map" style="height: 100%;height:100vh; height: calc(100vh - 72px);background-color:#131313;">

				<?php $placeDivMarkupTemplate = '<div data-lat-lng=\'{"lat": %s, "lng": %s}\' data-place-title=\'%s\' data-place-content=\'%s\' data-place-url=\'%s\'></div>'; ?>
				
				<?php while ( have_posts() ) : the_post(); ?>

					<?php 
						$postID = get_the_ID();
						$lat = get_post_meta( $postID, 'tome_place_lat' );
						$lng = get_post_meta( $postID, 'tome_place_long' );
						$title = htmlspecialchars( get_the_title() );
						echo sprintf($placeDivMarkupTemplate, $lat[0], $lng[0], htmlspecialchars( get_the_title() ), htmlspecialchars( wpautop(get_the_excerpt()) ), get_the_permalink());
					?> 

				<?php endwhile; ?>

				</div>	

			<?php else : ?>

				<p><?php _e( 'Sorry, no places have been made yet!' ); ?></p>
				
				<?php get_template_part( 'content', 'none' ); ?>

			<?php endif; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>