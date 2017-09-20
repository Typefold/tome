<?php
/**
 * The Template for displaying Single Embedded Media.
 *
 * @package WordPress
 * @subpackage Tome Theme
 */

	get_header(); 
?>

<div class="content-wrap">
	
	<div id="primary" class="content-area row">
		
		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', 'tomemedia' ); ?>

		<?php endwhile; ?>

	</div><!-- #primary -->

</div><!-- .content-wrap -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>