<?php
/**
 * The Template for displaying all single Tome places.
 *
 * @package WordPress
 * @subpackage Tome Theme
 */
?>

<?php
	get_header();
?>
    <div class="content-wrap" style=''>
    <div id="primary" class="content-area row">
    <div id="chapter-content" style="padding-top:20px;" class="site-content large-10 large-centered medium-8 medium-centered small-12 columns" role="main">
    	
    	<div class="chapter-header hgroup">
    		<h1 class="entry-title"><?php the_title(); ?></h1>
    		<hr class="title-divider">
    	</div>


	    <?php echo do_shortcode( "[tome_gallery id='$post->ID' size='full-column']" ); ?>

    </div>
    </div>
    </div>
<?php //get_sidebar(); ?>
<?php get_footer(); ?>