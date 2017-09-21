<?php
/*----------

Tome TinyMCE Plugins

TinyMCE Editor Modifications

----------*/

// Add editor buttons
function tome_editor_buttons() {
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
add_action( 'init', 'tome_editor_buttons' );

// Adds tome-specific buttons to TinyMCE
function tome_custom_add_buttons() {
    $admin_url = get_admin_url();
    if( post_type_exists( "tome_place" ) )
        echo '<a href="'.$admin_url.'admin-ajax.php?action=add_shortcode&type=tome_place" data-editor="content" title="Add Place" class="thickbox button" style="float: right">Add Place</a>';

    if( post_type_exists( "tome_media" ) )
        echo '<a href="'.$admin_url.'admin-ajax.php?action=add_shortcode&type=tome_media" data-editor="content" title="Add Embedded Media" class="thickbox button" style="float: right">Add Embedded Media</a>';

    if( post_type_exists( "tome_gallery" ) )
        echo '<a href="'.$admin_url.'admin-ajax.php?action=add_shortcode&type=tome_gallery" data-editor="content" title="Add Gallery" class="thickbox button" style="float: right">Add Gallery</a>';
}

add_action('media_buttons',  'tome_custom_add_buttons');

// TinyMCE Custom Toolbar for ACF Generated TinyMCE
function tome_toolbars( $toolbars )
{
    // Uncomment to view format of $toolbars
    // http://www.advancedcustomfields.com/resources/customize-the-wysiwyg-toolbars/

    /*
    echo '< pre >';
    print_r($toolbars);
    echo '< /pre >';
    die;
    */

    // Add a new toolbar called "Very Simple"
    // - this toolbar has only 1 row of buttons
    $toolbars['Very Simple' ] = array();
    $toolbars['Very Simple' ][1] = array('italic', 'link', 'unlink');

    // return $toolbars - IMPORTANT!
    return $toolbars;
}
add_filter( 'acf/fields/wysiwyg/toolbars' , 'tome_toolbars'  );

// Tiny MCE Settings - accept pasted in input as plaintext
function tome_tinymce_settings( $settings ) {
    $settings['paste_as_text'] = true;
    return $settings;
}

add_filter( 'tiny_mce_before_init', 'tome_tinymce_settings' );
