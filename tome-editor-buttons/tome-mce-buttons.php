<?php

add_action( 'init', 'wptuts_buttons' );

function wptuts_buttons() {
    add_filter("mce_external_plugins", "tome_mce_buttons");
    add_filter('mce_buttons', 'tome_register_buttons');
}   
function tome_mce_buttons($plugin_array) {
    $plugin_array['typefoldtome'] = get_template_directory_uri() . '/tome-editor-buttons/tome-mce-plugin.js';
    return $plugin_array;
}
function tome_register_buttons($buttons) {
    array_push( $buttons, 'dropcap' ); // , 'cite', 'place', 'recentposts
    array_push( $buttons, 'tome_blockquote' );
    array_push( $buttons, 'pullquote' );
    array_push( $buttons, 'abstract' );

    $remove = array('blockquote');

    return array_diff($buttons,$remove);
}
?>