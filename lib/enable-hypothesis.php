<?php
/* 
  Add Hypothes.is to the theme 
  - Assumes you have a boolean option set up for enabling/disabling hypothesis named: "enable_hypothesis"
*/
function tome_addHypothesis() {
  // Check option value
  if(get_option('enable_hypothesis') == 1):
    //  Include the snippet on
    // - single posts
    // - pages
    // - cover
    if( is_front_page() || is_page() || is_single() ) :
      // Output the hypothes.is async snippet 
      echo '<script async defer src="https://hypothes.is/embed.js"></script>';
    endif;
  endif;
}
add_action( 'wp_footer', 'tome_addHypothesis');


$enable_hypothesis = new enable_hypothesis();

class enable_hypothesis {
    function enable_hypothesis( ) {
        add_filter( 'admin_init' , array( &$this , 'register_hypothesis_field' ) );
    }
    function register_hypothesis_field() {
        register_setting( 'general', 'enable_hypothesis', 'esc_attr' );
        add_settings_field('enable_hypothesis', '<label for="enable_hypothesis">'.__('Enable Hypothesis' , 'enable_hypothesis' ).'</label>' , array(&$this, 'enable_hypothesis_field') , 'general' );
    }
    function enable_hypothesis_field() {
        $value = get_option( 'enable_hypothesis', '' );
        echo '<input name="enable_hypothesis" type="checkbox" value="1" ' . checked( $value, 1, 0 ) . '/>';
    }
}
