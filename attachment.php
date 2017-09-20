<?php
/**
 * The Template for displaying Single Attachments.
 *
 * @package WordPress
 * @subpackage Tome Theme
 */
	get_header(); 
?>
<div class="content-wrap">
	<div id="primary" class="content-area">

		<?php /* The loop */?>
		
		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', 'attachment' ); ?>

		<?php endwhile; ?>

	</div><!-- #primary -->

</div><!-- .content-wrap -->
<?php //get_sidebar(); ?>
<?php get_footer(); ?>