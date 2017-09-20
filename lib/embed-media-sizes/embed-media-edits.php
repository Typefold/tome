<?php

/**
 * In this file we adding functionality for:
 * 1 - embed media size
 * 2 - edit embed media view
 * 3 - Edit default embed media size
 */

add_filter( 'embed_defaults', 'bigger_embed_size' );

add_action( 'wp_enqueue_media', 'wp_enqueue_custom_media' );
add_action( 'print_media_templates', 'print_media_templates' );


function wp_enqueue_custom_media() {

	if ( ! isset( get_current_screen()->id ) || get_current_screen()->base != 'post' )
		return;

	wp_enqueue_script( 'custom-gallery-settings', get_template_directory_uri() . '/lib/embed-media-sizes/custom-embed-media-settings.js', array( 'media-views' ) );

}


function print_media_templates() {

	if ( ! isset( get_current_screen()->id ) || get_current_screen()->base != 'post' )
		return;

	?>
	<script type="text/html" id="tmpl-custom-embed-media-setting">
		<label class="setting">
			<span>Media Size</span>
			<select class="media-size" name="size" data-setting="size">
				<?php

				$sizes = array(
					'full-column'    => __( 'Full Column' ),
					'half-column' => __( 'Half Column' ),
					);

					foreach ( $sizes as $value => $name ) { ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, 'full-column' ); ?>>
						<?php echo esc_html( $name ); ?>
					</option>
					<?php } ?>
				</select>
			</label>
		</script>
		<?php
	}




function bigger_embed_size() {
	return array( 'width' => 800, 'height' => 450 );
}