<?php
/**
* The template for the Blog Page of the Site.
* Template Name: Blog template
* @package WordPress
* @subpackage Tome Theme
*/
get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>


<div class="content-wrap">

	<?php $blog_query = new WP_Query(array(
		'post_type' => 'post',
		'posts_per_page' => 10,
		'post_status' => 'publish',
		'paged' => get_query_var('paged')
	)); ?>

	<?php if ( $blog_query->have_posts() ) : while ( $blog_query->have_posts() ) : $blog_query->the_post(); ?>

	  	<div class="row">
	  		<div class="large-12 columns">
				<div class="post" id="post-<?php the_ID(); ?>">

	
					<h2>
						<a href="<?php the_permalink(); ?>" 
						 rel="bookmark" 
						 title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
					</h2>

					<?php the_excerpt(); ?>

					<p>
						<?php
						$post_tags = wp_get_post_tags( $post->ID );
						if ( !empty( $post_tags ) ) {
							the_tags('Tags: ', ', ', '<br />') . ' | ';			
						}
						?>
						Posted in <?php the_category(', '); ?>
					</p>


				</div>
			</div>
		</div>


	<?php endwhile; ?>
	  	<div class="row">
	  		<div class="large-6 small-6 columns older more-entries">
				  <?php next_posts_link('Older Entries'); ?>
			</div>
	  		<div class="large-6 small-6 columns newer more-entries">
				  <?php previous_posts_link('Newer Entries'); ?>
			</div>
		</div>
	<?php else: ?>
		<p>Nothing found</p>
	<?php endif; ?>

</div>
<?php get_footer(); ?>


<?php endwhile; ?>
<!-- post navigation -->
<?php else: ?>
<?php wp_reset_query(); ?>
<?php endif; ?>