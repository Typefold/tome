<?php
/**
 * The template for displaying All Chapters.
 *
 * @package WordPress
 * @subpackage Tome Theme
 */
global $wp_query;
$qv = $wp_query->query_vars;
$qv['post_type'] = "any";
query_posts( $qv );
get_header(); ?>

<div class="content-wrap">
	<div id="primary" class="content-area row">
		<div id="content" class="site-content small-offset-2 small-8 columns" role="main">
		<?php if ( have_posts() ) : ?>
			<header class="archive-header">
				<h1 class="archive-title chapter-archive full-screen-mode">Tagged with: <?php echo single_tag_title( '', false ) ?></h1>
			</header><!-- .archive-header -->

			<div class="entry-content">

				<ol class="chapter-list">
			<?php while ( have_posts() ) : the_post(); ?>
					<li>
						<?php echo get_post_type_object( $post->post_type )->labels->singular_name; ?>: <a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a>
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