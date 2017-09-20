<?php

class Tome_Map_Frontend
{

	function __construct()
	{
		$this->init();
	}


	private function init() {
		add_shortcode('tome_map', array($this, 'tome_map_shortcode') );
		add_action('wp_enqueue_scripts', array($this, 'map_scripts') );
	}


	public static function print_map( $map_id, $show_title ) {

	    if( have_rows('places', $map_id) ):

	    if ( $show_title != false ) { ?>
		    <h3 class="map-heading"><?php echo get_the_title($map_id); ?></h3>
	    <?php } ?>


	    <div class="chapter-header-media">

	        <div id="map" class="tome-map" style="height: 100%;background-color:#131313;">

	        	<?php self::print_places( $map_id ); ?>

	        </div>  
	    </div>
	    <?php

	    endif;
	}


	public static function print_place( $place_id, $show_title, $explode_width = true ) {

		$explode_width = ( $explode_width == true ) ? " explode-wrapper" : "";
		$place = get_post($place_id);
		$title = $place->post_title;
        $place_meta = get_post_custom( $place_id );
    	$lat = $place_meta['tome_place_lat'][0];
    	$long = $place_meta['tome_place_long'][0];
    	$zoom = $place_meta['tome_place_zoom'][0];
    	$pov = $place_meta['tome_place_pov'][0];
    	$map_type = $place_meta['tome_place_map_type'][0];
    	$excerpt = $place->post_excerpt;
    	$url = get_the_permalink( $place->ID );


	    if ( $show_title != false ) { ?>
		    <h3 class="map-heading"><?php echo $title; ?></h3>
	    <?php } ?>




	    <div class="chapter-header-media<?php echo $explode_width; ?>">


	    	<div id="map-canvas-<?php echo $place_id; ?>" class="tome-map" style="height: 100%;background-color:#131313;">

	    		<?php echo sprintf( '<div data-zoom="%s" data-lat-lng=\'{"lat": %s, "lng": %s}\' data-place-title="%s" data-place-content="%s" data-place-url="%s" data-type="%s" data-pov="%s"></div>', $zoom, $lat, $long, $title, $excerpt, $url, $map_type, $pov); ?>
	    	</div>


	    </div>
	    <?php
	}


	private function print_places( $map_id ) {

	    while( have_rows('places', $map_id) ): the_row();

	    	$place = get_sub_field('place');

	        $place_meta = get_post_custom( $place->ID );
	    	$lat = $place_meta['tome_place_lat'][0];
	    	$long = $place_meta['tome_place_long'][0];
	    	$title = $place->post_title;
	    	$excerpt = $place->post_excerpt;
	    	$url = get_the_permalink( $place->ID );

	    	// echo '<div data-lat-lng=\'{"lat": '.$lat.', "lng": '.$long.'}\' data-place-title="'.$title.'" data-place-content="'.$excerpt.'" data-place-url="'.$url.'"></div>';
	    	echo '<div data-lat-lng=\'{"lat": '.$lat.', "lng": '.$long.'}\' data-place-title="'.$title.'" data-place-content="'.$excerpt.'" data-place-url="'.$url.'"></div>';

	    endwhile;
	}


	function tome_map_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'id' => '', 
		), $atts ) );

		ob_start();
		echo '<div class="explode-width">';
		self::print_map($id, false);
		echo '</div>';
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}


	function map_scripts() {
		global $post;

		if ( 'tome_map' === $post->post_type ) {
			wp_dequeue_script( 'google_maps' );
			wp_dequeue_script( 'tome_place_js' );
			wp_enqueue_script( 'tome-places-frontend' );
		}
	}


}

$init = new Tome_Map_Frontend;

?>
