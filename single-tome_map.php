<?php
/**
 * The Template for displaying all single Tome Map.
 *
 * @package WordPress
 * @subpackage Tome Theme
 */


	// This will add the necessary scripts for Tome Places

	get_header(); 	

	// print all places for current map
	Tome_Map_Frontend::print_map( $post->ID, false );
?>



<div class="content-wrap">
	<div id="primary" class="content-area row">
		<div id="chapter-content" class="site-content <?php echo $tome_content_widths ?>" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				
				<?php 
					//Create the content
					get_template_part( 'content', get_post_format() );
					the_excerpt();
				?>
				
			<?php endwhile; // end of the loop. ?>
		</div><!-- #content -->
	</div><!-- #primary -->
</div><!-- content-wrap -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>