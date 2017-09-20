<?php 
// Pagination for pages and post types we want to sort like pages
//
// This template part will make previous/next links
// ordered by Menu Order, rather than by dates (as with posts)


$post_type_slug = get_post_type( $post );

if ( ! $post_type_slug == 'chapter')
	return false;

//Retrieve all posts, push their IDs into array $pages
$args = array(
	'post_type' => $post_type_slug,
);

$pag_query = new WP_Query($args);
$pages_ids = array();

if ( $pag_query->have_posts() ) : while ( $pag_query->have_posts() ) : $pag_query->the_post();
	array_push($pages_ids, get_the_ID());
endwhile;
else:
endif;
wp_reset_query();

//Locate the current posts position in the array, to find the previous and next posts, if any.
$current = array_search(get_the_ID(), $pages_ids);
$prevID = $pages_ids[$current-1];
$nextID = $pages_ids[$current+1];

$prev_title = ( ! empty($prevID) ) ? get_the_title($prevID) : false;
$next_title = ( ! empty($nextID) ) ? get_the_title($nextID) : false;

?>

<!-- chapter nav buttons at the bottom of the chapter -->
<div class="pagination-wrap container ">
	<div class="row">
		<div class="large-10 large-centered medium-8 medium-centered small-12 small-uncentered columns">
			<div class="row">
				<div class="small-6 columns">
					<div class="prev-chap">

						<?php if ( !empty($prevID) ) { ?>
							<div><?php echo _x("Previous", "previous post");?></div>
							<p>
								<a href="<?php echo get_permalink($prevID); ?>" title="<?php echo $prev_title ?>"><?php echo $prev_title; ?></a>
							</p>
						<?php } ?>

					</div>
				</div>
				<div class="small-6 columns">
					<div class="next-chap">
						<?php if ( !empty($nextID) ) { ?>
							<div><?php echo _x("Next", "next post"); ?></div>
							<p>
								<a href="<?php echo get_permalink($nextID); ?>" title="<?php echo $next_title; ?>"><?php echo $next_title; ?></a>
							</p>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><!-- .pagination-wrap.conatiner -->

<!-- floating chapter nav buttons on the left and right of the screen-->
<div class="chapter-nav-buttons">
	<?php if ( ! empty($prevID) ) { ?>
		<a class="prev" href="<?php echo get_permalink($prevID); ?>"><span class="title"><?= $prev_title; ?></span></a>
	<?php } ?>

	<?php if ( ! empty($nextID) ) { ?>
		<a class="next" href="<?php echo get_permalink($nextID); ?>"><span class="title"><?= $next_title; ?></span></a>
	<?php } ?>
</div>