<?php
/**
 * The template for displaying references.
 *
 * @package WordPress
 * @subpackage Tome Theme
 */

get_header(); ?>

<div class="content-wrap">
	<div id="primary" class="content-area row">
		<div id="content" class="site-content large-8 large-centered small-10 small-offset-2 columns" role="main">
		<?php if ( have_posts() ) : ?>

			<header class="archive-header">
				<h3 class="archive-title chapter-archive full-screen-mode">Bibliography</h3>
			</header><!-- .archive-header -->

			<div class="entry-content">

				<div class="bibliography-list">

					<section class="works-cited">

                <?php

                    $posts = get_posts(array(
                    	'post_type' => 'tome_bibliography',
                    	'posts_per_page' => -1
                    ));

                    foreach($posts as $key=>$entry) {

							?>
							<p class="biblio-entry" data-biblio="<?php echo $entry->ID; ?>">
								<?php echo Biblio_Entry_Printer::print_entry( $entry ); ?>
							</p>

							<?php

                    }

                    if (!$posts)
                        echo "<h5><i>No works cited</i></h5>";
                ?>

	                </section>

				</div>
			</div>
		<?php endif; ?>

		</div><!-- #content -->
	</div><!-- #primary -->
</div>
<?php //get_sidebar(); ?>
<?php get_footer(); ?>