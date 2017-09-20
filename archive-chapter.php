<?php
/**
 * The template for displaying All Chapters.
 *
 * @package WordPress
 * @subpackage Tome Theme
 */

get_header(); ?>

<div class="content-wrap">
	<div id="primary" class="content-area row">
		<div id="content" class="site-content small-offset-2 small-8 columns" role="main">
		<?php if ( have_posts() ) : ?>
			<header class="archive-header">
				<h1 class="archive-title chapter-archive full-screen-mode">Chapters</h1>
			</header><!-- .archive-header -->

			<div class="entry-content">

				<ol class="chapter-list">
			<?php while ( have_posts() ) : the_post(); ?>
					<li>
						<a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a>
					</li>
			<?php endwhile; ?>
				</ol>
			</div>
		<?php endif; ?>

		</div><!-- #content -->
	</div><!-- #primary -->
</div>
<?php //get_sidebar(); ?>
<?php get_footer(); ?>