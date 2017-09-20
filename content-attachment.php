<?php
/**
 * @package Tome
 * @since Tome 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('row full-width attachment-data'); ?>>
	
<?php
	//Determine aspect ratio, so we can apply certain classes
	global $wp_query;
	$wp_query->is_attachment = null; // so the previous link will not go to parent post


	$attachment_meta = wp_get_attachment_metadata( $id , true); //get meta without filtering

	$attachment_aspect_ratio = $attachment_meta['width'] / $attachment_meta['height']; //Calculate aspect ratio

	$desc_container_classes = 'small-12 large-4';
	
	if($attachment_aspect_ratio >= 1) { //Landscape
		
		$media_container_classes = 'small-12 large-8';
	
	} else { //Portrait
		
		$media_container_classes = 'small-10 small-offset-1 large-5 large-offset-2';
	
	}
?>      
        <?php //<div class="close-gui fi-remove">&times;</div>  ?>
		<nav class="back-to-grid"><a href="<?php echo site_url('/media/') ?>"><span class="fi-thumbnails"></span> Back to grid</a></nav> 

        <div class="<?php echo $media_container_classes ?> columns">
          <figure>
          	<?php if(preg_match('/^(oembed)/', get_post_mime_type($id))): ?>
          	<div class="flex-video">
            <?php
            $embed_code = wp_oembed_get( wp_get_attachment_url($id) );
            echo $embed_code;
            ?>
            </div>
	        <?php else: ?>
            <img src="<?php echo wp_get_attachment_url( $id) ?>"  class="primary-image-asset" alt="" />
            <?php endif; ?>

            <?php if (has_excerpt( $id )): ?>
            <figcaption><?php the_excerpt() ?></figcaption>
        	<?php endif; ?>
          </figure>
        </div>

        <div class="<?php echo $desc_container_classes ?> columns description">
  
          <div class="section-container tabs" data-section="tabs">
            <section class="active">
              <p class="title" data-section-title><a href="#">Description</a></p>
              <div class="content" data-section-content>
                
                <?php the_title('<h1>', '</h1>') ?>
                
				<?php the_content() ?>
              </div>
            </section>
            <?php $tags = get_the_tags(); $chapters = image_appears_in(get_the_ID()) ?>
            <?php if(!empty($tags) || !empty($chapters)): ?>
            <section>      
              <p class="title" data-section-title><a href="#">Tags</a></p>    
              <div class="content" data-section-content>
              	<?php if($tags): ?>
                <h1>Tags</h1>
                <ul class="inline-list media-tags">
                	<?php foreach($tags as $tag): ?>
	                	<li><a href="/media/#<?php echo $tag->slug ?>"><?php echo $tag->name ?></a></li>
	                <?php endforeach ?>
                </ul>
                <?php endif; ?>
                <?php if($chapters): ?>
	                <h1>Appears In</h1>
	                <ul class="appears-in">
	                <?php foreach($chapters as $chapter): ?>
	                	<li><a href="<?php echo get_permalink($chapter->ID) ?>"><?php echo $chapter->post_title ?></a></li>
	                <?php endforeach ?>
	                </ul>
            	<?php endif; ?>
               
              </div>
            </section>   
            <?php endif; ?>         
          </div>
		  <div class="media-pagination">
			<span class"previous"><?php previous_post_link('%link', 'Previous Media'); ?></span> / 
			<span class"next"><?php next_post_link('%link', 'Next Media'); ?></span>
		  </div>	
      </div>

<?php

/*


	<div class="entry-content col-xs-12 col-xs-offset-0 col-sm-8 col-sm-offset-2">
		
		<?php //the_attachment_link( $id , true); ?>
		<?php echo '<img src="'. wp_get_attachment_url( $id)  . '" class="primary-image-asset"/>'; ?>

		<div class="row">
			<div class="col-xs-3">
				<div class="previous-image-wrapper">
					<?php previous_image_link( false, 'Previous' ); ?>
				</div>
			</div>

			<div class="col-xs-3">
				<div class="next-image-wrapper">
					<?php next_image_link( false, 'Next' ); ?>
				</div>
			</div>					
		</div>

		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'tome' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-meta">
		<?php
			//translators: used between list items, there is a space after the comma
			$category_list = get_the_category_list( __( ', ', 'tome' ) );

			// translators: used between list items, there is a space after the comma
			$tag_list = get_the_tag_list( '', __( ', ', 'tome' ) );

			if ( ! tome_categorized_blog() ) {
				// This blog only has 1 category so we just need to worry about tags in the meta text
				if ( '' != $tag_list ) {
					//$meta_text = __( 'This entry was tagged %2$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'tome' );
				} else {
					//$meta_text = __( 'Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'tome' );
				}

			} else {
				// But this blog has loads of categories so we should probably display them here
				if ( '' != $tag_list ) {
					$meta_text = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'tome' );
				} else {
					$meta_text = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'tome' );
				}

			} // end check for categories on this blog

			printf(
				$meta_text,
				$category_list,
				$tag_list,
				get_permalink()
			);
		?>

		<?php edit_post_link( __( 'Edit', 'tome' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-meta -->

*/
	?>
</article><!-- #post-## -->
