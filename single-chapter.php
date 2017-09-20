<?php
/**
 * The Template for displaying Single Chapters.
 *
 * @package WordPress
 * @subpackage Tome Theme
 */

	
get_header(); 

get_template_part( 'fixedheader');
?>

<div class="content-wrap">

<?php


	//Find out if it is a "places as a chapter" chapter.
	$custom = get_post_custom($post->ID);
	$tome_chapter_header_option = $custom["tome_chapter_header_option"][0];

	if ($tome_chapter_header_option !="allplaces") { ?>

		<div id="primary" class="content-area row">

			<div id="chapter-content" class="site-content <?php echo $tome_content_widths ?>" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'chapter' ); ?>

				<?php endwhile; ?>

			</div>

		</div>

	<?php
	}


	// This template part will make previous/next links
	// ordered by Menu Order, rather than by dates (as with posts)
	//
	get_template_part('pagination','pages');
	?>

	<div>
		<?php comments_template(); ?>
	</div>

</div><!-- .content-wrap -->
<?php //get_sidebar(); ?>
<?php get_footer(); ?>
