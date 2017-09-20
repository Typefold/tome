<?php
/**
* The template for the Bibliography Page of the Site.
* Template Name: Bibliography
* @package WordPress
* @subpackage Tome Theme
*/

get_header(); ?>

<?php echo get_post_type_archive_link( $post_type ); ?>

<div class="content-wrap">
	<div id="primary" class="content-area row">
		<div id="content" class="site-content large-8 large-centered small-10 small-offset-2 columns" role="main">
		<?php if ( have_posts() ) : ?>


			<div class="entry-content">

				<div class="bibliography-list">

					<section class="works-cited bibliography">


					<h2 class="works-cited-title">Bibliography</h2>

					<?php do_action('bibliography'); ?>

					<?php
					if ( !$posts )
						echo "<h5><i>No works cited</i></h5>";

					?>

	                </section>

				</div>
			</div>
		<?php endif; ?>

		</div><!-- #content -->
	</div><!-- #primary -->
</div>

<?php get_footer(); ?>