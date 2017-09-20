<div id="tome-cover" class="<?php echo $content_position; ?>" style="<?php echo $content_container_style ; ?>">

	<h1 style="color: <?php echo $title_color; ?>;">
			<?php the_field('book_title', 'option'); ?>
	</h1>

	<div class="book-description" style="color: <?php echo $description_color; ?>;">
		<?php the_field('book_description', 'option'); ?>
	</div>

	<h2 class="book-author"><?php the_field('book_author', 'option'); ?></h2>

	<?php echo "<a href='$button_link' class='cover-link'>$button_text</a>"; ?>

</div>