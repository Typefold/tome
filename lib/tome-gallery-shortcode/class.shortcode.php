<?php

/**
 * Class dtbaker_Shortcode_Banner
 * handles the creation of [tome_gallery] shortcode
 * adds a button in MCE editor allowing easy creation of shortcode
 * creates a wordpress view representing this shortcode in the editor
 * edit/delete button on wp view as well makes for easy shortcode managements.
 *
 * separate css is in style.content.css - this is loaded in frontend and also backend with add_editor_style
 */

class Tome_Gallery_Banner {
    private static $instance = null;
    public static function get_instance() {
        if ( ! self::$instance )
            self::$instance = new self;
        return self::$instance;
    }

	public function init(){
		// comment this 'add_action' out to disable shortcode backend mce view feature
		add_action( 'admin_init', array( $this, 'init_plugin' ), 20 );
        add_shortcode( 'tome_gallery', array( $this, 'dtbaker_shortcode_banner' ) );
	}
	
	public function init_plugin() {
		//
		// This plugin is a back-end admin ehancement for posts and pages
		//
    	if ( current_user_can('edit_posts') || current_user_can('edit_pages') ) {
			add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );
			add_action( 'admin_head', array( $this, 'admin_head' ) );
		}
    }
    
	// front end shortcode displaying:
	public function dtbaker_shortcode_banner($atts=array(), $innercontent='', $code='') {
	    $sc_atts = shortcode_atts(
    		array(
        		'id' => false,
        		'size' => '',
    		),
    		$atts
	    );
	    $sc_atts['banner_id'] = strtolower(preg_replace('#\W+#','', $sc_atts['id'])); // lets put everything in the view-data object
	    $sc_atts = (object) $sc_atts;

		// Use Output Buffering feature to have PHP use it's own enging for templating
	    ob_start();
	    include dirname(__FILE__).'/views/dtbake_shortcode_banner_view.php';
	    return ob_get_clean();
	}

    /**
     * Outputs the view inside the wordpress editor.
     */
    public function print_media_templates() {
        if ( ! isset( get_current_screen()->id ) || get_current_screen()->base != 'post' )
            return;
        include_once dirname(__FILE__).'/templates/tmpl-editor-boutique-banner.html';
    }
    
    public function admin_head() {
    	global $post;

		$current_screen = get_current_screen();
		if ( ! isset( $current_screen->id ) || $current_screen->base !== 'post' ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script( 'boutique-banner-editor-view', get_template_directory_uri() . '/lib/tome-gallery-shortcode/js/boutique-banner-editor-view.js', array( 'shortcode', 'wp-util', 'jquery' ), false, true );
    }
}

Tome_Gallery_Banner::get_instance()->init();


