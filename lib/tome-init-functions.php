<?php 

//Function to set default permalinks style to Tome recommended /%postname%/
function tome_reset_permalinks() {
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	$wp_rewrite->flush_rules();
}

//Handles setup on theme switch
add_action( "after_switch_theme", "tome_reset_permalinks", 10,  0);
add_action( 'admin_init', 'tome_reset_permalinks' );