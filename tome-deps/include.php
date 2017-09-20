<?php 

//'PROD' or 'DEV' ?
$acf_mode = 'PROD';

//Include Advanced Custom Fields
if($acf_mode == 'PROD') :

    // 0. Hide ACF field group menu item for non admins
    if(!current_user_can( 'manage_options' )) {
        add_filter('acf/settings/show_admin', '__return_false');    
    } 

    // 1. customize ACF dir
    add_filter('acf/settings/dir', 'tome_acf_settings_dir');

    function tome_acf_settings_dir( $dir ) {

        // update path
        $dir =  get_stylesheet_directory_uri() . '/tome-deps/acf/';

        // return
        return $dir;
    }

    //2. Load up the plugin.
    include_once( get_stylesheet_directory() . '/tome-deps/acf/acf.php');

    //3. Load our Fields
    include( get_stylesheet_directory() . '/tome-deps/acf-fields.php');
elseif ($acf_mode == 'DEV') :
    /** 
    *    TODO
    *    The best would be if we could use the below approach to keep 
    *    the fields easily editable via the handy admin settings page, 
    *    but still use the approach above to load up the settings in 
    *    production. I will leave this here, commented out. 
    **/

    // 1. customize ACF path
    add_filter('acf/settings/path', 'tome_acf_settings_path');
     
    function tome_acf_settings_path( $path ) { 
        // update path
        $path = get_stylesheet_directory() . '/tome-deps/acf/';
        // return
        return $path;
    }
     
    // 2. customize ACF dir
    add_filter('acf/settings/dir', 'tome_acf_settings_dir');

    function tome_acf_settings_dir( $dir ) {
     
        // update path
        $dir =  get_stylesheet_directory_uri() . '/tome-deps/acf/';
        
        // return
        return $dir;
        
    }
     
    // 3. Optionally - Hide ACF field group menu item
    //add_filter('acf/settings/show_admin', '__return_false');

    //4.JSON SETTINGS LOADING
    add_filter('acf/settings/save_json', 'tome_acf_json_save_point');
     
    function tome_acf_json_save_point( $path ) {
        
        // update path
        $path = get_stylesheet_directory() . '/tome-deps/acf/settings';
        
        
        // return
        return $path;
        
    }

    add_filter('acf/settings/load_json', 'tome_acf_json_load_point');

    function tome_acf_json_load_point( $paths ) {
        
        // remove original path (optional)
        unset($paths[0]);

        // append path
        $paths[] = get_stylesheet_directory() . '/tome-deps/acf/settings';

        // return
        return $paths;
        
    }

    //5. Finally… load up the plugin.
    include_once( get_stylesheet_directory() . '/tome-deps/acf/acf.php');

endif;
?>