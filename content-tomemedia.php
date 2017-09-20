<?php
/**
 * The  template for displaying embedded Media.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

		$meta = get_post_meta( $id );
		$embedHTML = $meta["tome_media_embed_script"][0];
		
		//Always landscape, since they are videos
		//Sorry, no vertical phone videos supported ;)
		$media_container_classes = 'small-12 large-8';
		$desc_container_classes = 'small-12 large-4';

		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class('row full-width attachment-data'); ?>>
			<nav class="back-to-grid"><a href="<?php echo site_url('/media/') ?>"><span class="fi-thumbnails"></span> Back to grid</a></nav> 
	        <div class="<?php echo $media_container_classes ?> columns">
	          	<figure>
					<div class="flex-video widescreen">
					<?php echo '        ' . $embedHTML; ?>
					</div>						
	            	<?/* <figcaption><?php the_excerpt() ?></figcaption>*/?>
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
	            <?php /* 
	            ekittell: 3/18: this stuff just isn't ready yet, but the rest of the page is usable in the meantime
	            <section>      
	              <p class="title" data-section-title><a href="#">Tags</a></p>    
	              <div class="content" data-section-content>
	                <h1>Tags</h1>
	                <ul class="inline-list media-tags">
	                	<?php echo get_the_tag_list("<li>", "</li><li>", "</li>"); ?>
	                </ul>
	                <?php /*
	                <h1>Appears In</h1>
	                <ul class="appears-in">
	                  <li><a href="#">Chapter Name Can Go Here</a></li>
	                  <li> <a href="#">Maybe a Place Could be Here</a></li>
	                </ul>
	               
	              </div>
	            </section>
	             */ ?>             
	          </div>
			  <div class="media-pagination">
				<span class"previous"><?php previous_post_link('%link', 'Previous Media'); ?></span> / 
				<span class"next"><?php next_post_link('%link', 'Next Media'); ?></span>
			  </div>	
	      </div>

		</article>
		<?php ?>