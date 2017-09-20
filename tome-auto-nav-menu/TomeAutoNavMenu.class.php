<?php

class TomeAutoNavMenu
{

    /**
     * Start up
     */
    public function __construct()
    {
        //register_activation_hook( __FILE__, array( $this, 'install' ) );
        $this->install(); // use hook above if we move this into a plugin

        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );

        //wp_enqueue_script( 'jquery-ui-sortable' );

    }

    public function install() {
        add_option( "auto-nav-menu-enable", 1 );

        add_option( "auto-nav-menu-order", array( 
            "chapter", 
            "tome_place", 
            "tome_media", 
            "tome_bibliography", 
            "tome_gallery", 
            "post", 
            "page"

            ) );
        add_option( "auto-nav-menu-labels", array(
            "chapter" => "Chapters", 
            "tome_place" => "Places",
            "tome_media" => "Media", 
            "tome_bibliography" => "Bibliography", 
            "tome_gallery" => "Galleries", 
            "post" => "Blog", 
            "page" => "Pages"
            ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        add_menu_page(
            'Auto Nav Menu', 
            'Auto Nav Menu', 
            'manage_options', 
            'auto-nav-menu', 
            array( $this, 'create_admin_page' )
        );

    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'language' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>         
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'auto-nav-menu' );   
                do_settings_sections( 'auto-nav-menu-settings' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        add_site_option( "auto-nav-menu-display", $this->get_menu_post_types( true ) );

        register_setting(
            'auto-nav-menu', // Option group
            'auto-nav-menu-enable' // Option name
        );

        register_setting(
            'auto-nav-menu', // Option group
            'auto-nav-menu-display', // Option name
            array( $this, 'sanitize_display' ) // Sanitize
        );

        register_setting(
            'auto-nav-menu', // Option group
            'auto-nav-menu-labels', // Option name
            array( $this, 'sanitize_labels' ) // Sanitize
        );

        //Why was this duplicated???
        // register_setting(
        //     'auto-nav-menu', // Option group
        //     'auto-nav-menu-labels', // Option name
        //     array( $this, 'sanitize_labels' ) // Sanitize
        // );        

        // MULTI LINGUAL
        if( get_option( "languages" ) ) {
            register_setting(
                'auto-nav-menu', // Option group
                'auto-nav-menu-labels-ml', // Option name
                array( $this, 'sanitize_labels_ml' ) // Sanitize
            );
        }

        register_setting(
            'auto-nav-menu', // Option group
            'auto-nav-menu-order', // Option name
            array( $this, 'sanitize_order' ) // Sanitize
        );

        add_settings_section(
            'enable_nav_menu_id', // ID
            'Auto Nav Menu', // Title
            null, // Callback
            'auto-nav-menu-settings' // Page
        );  

         add_settings_field(
                'menu-enable', // ID
                'Enable Auto Nav Menu', // Title 
                array( $this, 'menu_enable_callback' ), // Callback
                'auto-nav-menu-settings', // Page
                'enable_nav_menu_id' // Section        
            ); 

        add_settings_section(
            'setting_section_id', // ID
            null, // Title
            array( $this, 'print_section_info' ), // Callback
            'auto-nav-menu-settings' // Page
        );  

        foreach( $this->get_menu_post_types() as $type => $post_type ) {
            
            $hiddenPostTypes = array();

            if (!in_array($type, $hiddenPostTypes)) {
                add_settings_field(
                    $type, // ID
                    $post_type['object']->labels->name, // Title 
                    array( $this, 'post_type_callback' ), // Callback
                    'auto-nav-menu-settings', // Page
                    'setting_section_id', // Section        
                    array( "type" => $type, "post_type" => $post_type['object'] )
                ); 
            }
        }

        add_settings_section(
            'javascript', // ID
            null, // Title
            array( $this, 'sortable_jquery' ), // Callback
            'auto-nav-menu-settings' // Page
        );  

   
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize_display( $input )
    {
        return array_keys( $input );
    }

    public function sanitize_labels( $input )
    {
        return array_merge( get_option( 'auto-nav-menu-labels' ), $input );
    }

    // MULTI LINGUAL
    public function sanitize_labels_ml( $input )
    {
        if( get_option( 'auto-nav-menu-labels-ml' ) != "" )
            return array_merge( get_option( 'auto-nav-menu-labels-ml' ), $input );
        else 
            return $input;
    }

    public function sanitize_order( $input )
    {
        return $this->order_post_types( get_option( 'auto-nav-menu-order' ), $input );
    }

    public function order_post_types( $unordered_types, $ordered_types )
    {
        $orphans = array_diff( $ordered_types, $unordered_types); // find orphans
        $orderered_type = array_diff( $ordered_types, $orphans); // remove them, we dont' want to try to order by types that are not even in the array were are trying to order

        $copy = $ordered_types;
        $types = array();
        foreach($unordered_types as $key => $type) {
            if( in_array( $type, $copy ) )
                $types[$key] = array_shift( $ordered_types );
            else
                $types[$key] = $type;
        }
        return $types;

    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    { ?> 
        <p>Manage menu items that will appear in the automatically generated navigation menu. You can:</p>
        <ul>
            <li>Check which items you would like to appear.</li>
            <li>Enter custom labels for the items.</li>
            <li>Drag items into the order you'd like them to appear.</li>
        </ul>

        <style>
          .ui-sortable tr { 
            cursor: move;
            border: 1px solid #DDDDDD;
        }
        .ui-sortable th {
            padding-left: 20px;
        }
        </style>

    <?php }

        /** 
     * Print the Section text
     */
    public function sortable_jquery()
    { ?>
    <script type="text/javascript">
        jQuery(function() { 
            jQuery( ".form-table:eq(1) tbody" ).sortable(); 
            jQuery( ".form-table:eq(1) tbody" ).disableSelection(); 
        } )
    </script>
    <?php }


    public function menu_enable_callback( $args )
    {
        printf(
            '<input type="checkbox" name="auto-nav-menu-enable" value="1" %s />',
            get_option( "auto-nav-menu-enable" ) ? 'checked="checked"' : ''
        );

    }


    /** 
     * Get the settings option array and print one of its values
     */
    public function post_type_callback( $args )
    {
        $type = $args['type'];
        $post_type = $args['post_type'];
        printf(
            '<input type="checkbox" name="auto-nav-menu-display[%s]" value="1" %s /> 
                <input type="text" name="auto-nav-menu-labels[%s]" value="%s" />
                <input type="hidden" name="auto-nav-menu-order[]" value="%s" />',
            $type,
            in_array( $type, get_option( "auto-nav-menu-display" ) ) ? 'checked="checked"' : '',
            $type,
            $this->get_post_type_label( $type ),
            $type

        );
        if ( is_plugin_active( 'tome-multi-lingual/tome-multi-lingual.php' ) ) {
            // MULTI LINGUAL
            foreach( get_option( "languages" ) as $language ) {
                if($language == get_option( "main_language" ) )
                    continue;
                printf(
                    ' %s: <input type="text" name="auto-nav-menu-labels-ml[%s][%s]" value="%s" />',
                    $language,
                    $type,
                    $language,
                    $this->get_post_type_label( $type, $language )
                );
            }
        }
    }

    public function get_post_type_label( $type, $language = false ) {
        // MULTI LINGUAL
        if( $language === false)
            $language = $_GET['lang'];
        if( $language ) {
            $labels = get_option( "auto-nav-menu-labels-ml" );
            if( $labels[$type][$language] )
                return $labels[$type][$language];
        }

        $labels = get_option( "auto-nav-menu-labels" );
        return $labels[$type];


    }

    public function get_menu_post_types( $flat = false )
    {
        $types = $this->order_post_types( array_keys( get_post_types( array( 'public' => true ) ) ), get_option( 'auto-nav-menu-order' ) );
        $post_types = array();
        foreach( $types as $type) {
            if( $posts = $this->get_menu_posts( $type ) ) {
                if( $flat ) {
                    $post_types[] = $type;
                } else {
                    $post_types[$type]['object'] = get_post_type_object( $type );
                    $post_types[$type]['posts'] = $posts;
                }
            }
        }
        return $post_types;
    }

    // MULTI LINGUAL
    public function get_menu_post_types_ml( $flat = false )
    {
        $types = $this->order_post_types( array_keys( get_post_types( array( 'public' => true ) ) ), get_option( 'auto-nav-menu-order' ) );
        $post_types = array();
        foreach( $types as $type ) {
            if( $type == "tome_media" )
                $posts = $this->get_media_attachments();
            else
                $posts = get_posts( array( 
                            "post_type" => "translation",
                            "posts_per_page" => -1,
                            "meta_query" => array(
                                                array(
                                                    'key' => 'language',
                                                    'value' => $_GET['lang']
                                                ),
                                                array(
                                                    'key' => 'post_parent_type',
                                                    'value' => $type
                                                ) 
                                            )
                        ) );

            if( $posts ) {
                if( $flat ) {
                    $post_types[] = $type;
                } else {
                    $post_types[$type]['object'] = get_post_type_object( $type );
                    $post_types[$type]['posts'] = $posts;
                }
            }
        }
        return $post_types;
    }

    public function get_menu_posts( $type ) 
    {
        $blog_id = get_option( 'page_for_posts');
        $front_id = get_option( 'page_on_front');
        $mediaPage_id = get_page_by_path('media');
        $posts = null;

        if( $type == "tome_media" ) {
            $posts = $this->get_media_attachments();
        } 
        elseif( !in_array( $type, array( "attachment", "translation" ) ) ){
            $posts = get_posts( array(
                    'post_type' => $type,
                    'posts_per_page' => -1,
                    'post__not_in' => array( $blog_id, $front_id, $mediaPage_id->ID )
                ) );
        }
        // else {
        //         This matches attachments and translations.
        //         error_log(sprintf("get_menu_posts::: unknown type %s", $type));
        // }
        if( $posts ) {
            return $posts;
        }
        else {
            return false;
        } 
    }

    public function get_media_attachments()
    {
        return get_posts( array(
                    'post_type' => "attachment",
                    'posts_per_page' => -1,
                    "post_mime_type" => 'image/jpeg,image/gif,image/jpg,image/png',
                    "meta_key" => "media_page_exclude",
                    "meta_value" => 0
                ) );
    }



}
