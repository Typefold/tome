
<?php 
/**
 * The default template for displaying pages.
 *
 * @package WordPress
 * @subpackage Tome Theme
 */

get_header();

?>
<div class="content-wrap">
	<div id="primary" class="content-area row">
		
		<?php if (have_posts()) : ?>
		  <?php while (have_posts()) : the_post(); ?>
		  	<article>
		  		
			  	<div id="chapter-content" class="site-content large-10 large-centered medium-8 medium-centered small-12 columns">
			  			<?php 
							if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
							  the_post_thumbnail();
							}
						?>
						<div class="post" id="post-<?php the_ID(); ?>">
							<div class="chapter-header hgroup">
								<h1 class="entry-title"><?php the_title(); ?></h1>
							</div>
							<?php echo get_post_meta($post->ID, 'PostThumb', true); ?>

							<?php the_content('Read Full Article'); ?>

							<p><?php the_tags('Tags: ', ', ', '<br />'); ?>  <?php // | Posted in the_category(', '); ?>

							<?php comments_popup_link('No Comments;', '1 Comment', '% Comments'); ?></p>
							

						</div>
				</div>

		  	</article>
		  <?php endwhile; ?>
			
			<div>
				<?php comments_template(); ?>		
			</div>
			
		  	<div class="row">
		  		<div class="large-6 small-6 columns older more-entries">
					  <?php next_posts_link('Older Entries'); ?>
				</div>
		  		<div class="large-6 small-6 columns newer more-entries">
					  <?php previous_posts_link('Newer Entries'); ?>
				</div>
			</div>
		<?php else : ?>
		  <h2>Nothing Found</h2>
		<?php endif; ?>

	</div>
</div>
<?php get_footer(); ?>