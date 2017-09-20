<?php


if ( get_current_blog_id() == 254 ) {
	register_nav_menus( array(
		'cover_menu' => 'Cover Page Menu',
		) );
}



/* 
  Add Hypothes.is to the theme 
  - Assumes you have a boolean option set up for enabling/disabling hypothesis named: "enable_hypothesis"
*/
function addHypothesis() {
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
add_action( 'wp_footer', 'addHypothesis');


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

// TODO - This is not really necessary - just use browser-sync
// echo '<script src="http://localhost:35729/livereload.js"></script>';

remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

require_once 'lib/embed-media-sizes/embed-media-edits.php';


// Don't show users wordpress update notification
add_action( 'admin_menu', 'remove_update_nag' );
function remove_update_nag() {
	remove_action( 'admin_notices', 'update_nag', 3 );
}


//Include dependencies.
include('tome-deps/include.php');
include('lib/tome-gallery-shortcode/class.shortcode.php');


if ( ! isset( $content_width ) ) {
	$content_width = 800;
}

/**
 * If WPML is activated add language switcher to the top menu
 */
if ( function_exists('icl_object_id') ) {

	function get_language_name($code=''){
		global $sitepress;
		$details = $sitepress->get_language_details($code);
		$language_name = $details['english_name'];
		return $language_name;
	}


	function top_menu_language_switcher(){
		$languages = icl_get_languages('skip_missing=0&orderby=code');

		echo '<li class="has-dropdown not-click">';
		echo '<a>'. get_language_name(ICL_LANGUAGE_CODE) .'</a>';
		echo '<ul class="dropdown">';

			if( ! empty( $languages ) ) {

				foreach ($languages as $lang ) {

					if( ! $lang['active'] ) {
						echo '<li><a href="'.$lang['url'].'">' . icl_disp_language($lang['translated_name']) . '</a></li>';
					}

				}

			}

		echo '</ul>';
		echo '</li>';
	}

	add_action( 'amsf_tome_topnav_right_list', 'top_menu_language_switcher' );

}

function language_switcher()
{
	if($_GET['lang']) {
		$current_language = $_GET['lang'];
	}	
	else {
		$current_language = "en";
	}


	$languages = get_option( "languages" );


	$key = array_search($current_language, $languages );
	unset( $languages[$key] );

	$base_url = strtok($_SERVER['REQUEST_URI'], '?');

	echo '<li class="has-dropdown">';
	echo '<a>' . $this->getEnglishName( $current_language ) . '</a>';
	echo '<ul class="dropdown">';
	foreach( $languages as $language ) {
		printf('<li><a href="%s">%s</a>',
			$language == $this->main_language ? $base_url : $base_url . '?lang=' . $language,
			$this->getEnglishName( $language )
			);
	}
	echo "</ul>";
	echo "</li>";

}



/**
* Registers a new post type
* @uses $wp_post_types Inserts new post type object into the list
*
* @param string  Post type key, must not exceed 20 characters
* @param array|string  See optional args description above.
* @return object|WP_Error the registered post type object, or an error object
*/
function tome_register_chapter_comment() {

	$args = array(
		'hierarchical'        => false,
		'description'         => 'description',
		'taxonomies'          => array(),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => false,
		'show_in_admin_bar'   => false,
		'menu_position'       => null,
		'menu_icon'           => null,
		'show_in_nav_menus'   => false,
		'publicly_queryable'  => true,
		'exclude_from_search' => true,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => true,
		'capability_type'     => 'post',
		'supports'            => array()
	);

	register_post_type( 'chapter_comment', $args );
}

add_action( 'init', 'tome_register_chapter_comment' );
	

function tome_comment( $atts, $content = null ) {
	$atts = shortcode_atts( array(
		'id' => ''
	), $atts );

	return '<span class="hello">' . $content . '</span>';
}
add_shortcode( 'tome_comment','tome_comment' );



function create_comment() {

	$postarr = array(
		'author' => get_current_user_id(),
		'content' => $_POST['comment'],
		'post_parent' => $_POST['parent']
	);

	wp_insert_post( $postarr, $wp_error );
}

if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title'    => 'Cover Page Settings',
		'menu_title'    => 'Cover Page',
		'menu_slug'     => 'tome-cover-settings',
		'capability'    => 'edit_posts',
		'parent_slug'   => 'tome-dashboard'
	));

}


// Shortcodes
include('shortcodes.php');
add_filter( 'the_content', 'wpautop', 100);

//There are the classes used for widths on single column tome content
//Used to be "small-12 medium-offset-2 medium-8 columns"
$tome_content_widths = "large-10 large-centered medium-8 medium-centered small-12 columns"; 

// This function loads the Tome plugins.
function load_tome_deps() {
	
	$TOME_DEPS_URI = get_template_directory().'/tome-deps/';

	// Tome Chapter
	if ( ! function_exists('tomechapter_register') ) {
		include_once($TOME_DEPS_URI.'tome_post_type_chapter/tome_post_type_chapter.php');  
	}
	// Tome Places
	if ( ! function_exists('tome_place_register') ) {
		include_once($TOME_DEPS_URI.'tome_post_type_place/tome_post_type_place.php');  
	}
	// Tome Gallery
	if ( ! function_exists('tome_gallery_post_type') ) {
		include_once($TOME_DEPS_URI.'tome_post_type_gallery/tome_post_type_gallery.php');  
	}
}

//Load up all the Tome Dependenies/Plugins
add_action('after_setup_theme', 'load_tome_deps');

/* Register all assets here */

function tome_js_register() {

	$themeUri = get_template_directory_uri();

	$cpto_options = unserialize('a:6:{s:23:"show_reorder_interfaces";a:13:{s:4:"post";s:4:"show";s:10:"attachment";s:4:"hide";s:14:"tome_reference";s:4:"hide";s:17:"tome_bibliography";s:4:"hide";s:7:"chapter";s:4:"show";s:8:"tome_map";s:4:"hide";s:12:"tome_gallery";s:4:"hide";s:19:"tome_external_media";s:4:"hide";s:15:"chapter_comment";s:4:"show";s:10:"tome_place";s:4:"hide";s:10:"tome_media";s:4:"hide";s:11:"translation";s:4:"show";s:13:"attachment_ml";s:4:"show";}s:8:"autosort";i:1;s:9:"adminsort";i:1;s:17:"archive_drag_drop";i:1;s:10:"capability";s:13:"publish_pages";s:21:"navigation_sort_apply";i:1;}');

	update_option( 'default_comment_status', 'closed' );
	update_option( 'cpto_options', $cpto_options );
	update_option( 'CPT_configured', 'TRUE' );

	//Font Size/Contrast Settings
	wp_register_script( 'tome_script', $themeUri . '/dist/tome.js', array('jquery'), '1.5', true);
	
	wp_register_script( 'foundation', $themeUri . '/js/foundation/foundation.min.js', array('jquery'), '1.1', true);
	wp_register_script( 'foundation-init', $themeUri . '/js/f4-init.js', array('foundation', 'slick-js'), '1.1', true);

	wp_register_script( 'slick-js', $themeUri . '/js/plugins/slick.js', array('jquery'), '1.1', true );
	wp_register_style( 'slick-css', $themeUri . '/css/slick.css' );

	//Register Google Maps v3 JS API
	wp_register_script('google_maps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=places',array(),'3.0',true);
	wp_register_script( 'tome_maps', $themeUri . '/tome-deps/tome_maps/js/main.js', array('jquery'), true );
	
	//Register Place Maps JS, with jQuery and Google Maps as Dependencies
	wp_register_script('tome_place_js', $themeUri. '/tome-deps/tome_post_type_place/js/tome_place_maps.js', array('jquery', 'google_maps'),'3.0',true);

	//Cover JS
	wp_register_script( 'tome_cover_script', $themeUri . '/js/tome.cover.js', array('jquery'), '0.1', true);

	// Select2 Plugin
	// usage: ['embed media modal filter, tome admin dashboard ]
	wp_register_script( 'select2-js', $themeUri . '/js/plugins/select2.min.js', array('jquery'), '0.1', true);
	wp_register_style( 'select2-css', $themeUri . '/css/select2.min.css' );


	// LazyLoad plugin used in media page
	wp_register_script( 'lazysizes', $themeUri . '/js/lazysizes.min.js', array('jquery'), '0.1', true);

	// Media page lightbox
	wp_register_style( 'media-page-lightbox', $themeUri . '/css/lightgallery.min.css');
	wp_register_style( 'media-page-lightbox-transition', $themeUri . '/css/lg-transitions.min.css');
	wp_register_script( 'media-page-lightbox-script', $themeUri . '/js/plugins/lightgallery-all.min.js', array('jquery'), '0.1', true);

	// tome gallery lightbox
	wp_register_script( 'tome-gallery-lightbox', $themeUri . '/lib/lightbox2/js/lightbox-plus-jquery.min.js', array('jquery'), '0.1', true);
	wp_register_style( 'tome-gallery-lightbox', $themeUri . '/lib/lightbox2/css/lightbox.min.css');

	//New Places Code
	//Register the Google Loader API
	wp_register_script('google_loader', 'https://www.google.com/jsapi',array('jquery'),'1.0', true);    
	wp_register_script( 'tome-places-frontend', get_template_directory_uri() . '/js/tome-places-2.0.js', array('google_loader', 'jquery'), '0.1', true );

}

add_action('wp_enqueue_scripts', 'tome_js_register');

//Register these for the backend, but do not enqueue them all!
add_action('admin_enqueue_scripts', 'tome_js_register');


include('tome-deps/tome_maps/class-tome-maps-frontend.php');

/**
 * Enqueue the scripts needed in the admin area
**/
function tome_js_frontend( ){

	global $post;

	if ( is_admin() == true )
		return;

	wp_enqueue_script( 'tome_script' );
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'tome-gallery-lightbox' );
	wp_enqueue_script( 'tome-gallery-lightbox' );


	if ( $post->post_type == 'page' && $post->post_name == 'media' ) {
		wp_enqueue_style( 'media-page-lightbox' );
		wp_enqueue_style( 'media-page-lightbox-transition' );
		wp_enqueue_script( 'media-page-lightbox-script' );
	}


	// wp_enqueue_script('tome_place_js');

	if ( 'chapter' === $post->post_type ) {
		wp_enqueue_script( 'tome_place_js'); //tome_place_maps
	}

	if ( 'post' == $post-> post_type ) {
		wp_enqueue_script( 'tome_place_js');
	}

	// homepage scripts
	if( is_home() ) { wp_enqueue_script( 'tome_cover_script' ); }

	if ( 'page' === $post->post_type ) {
		wp_enqueue_script( 'tome-places-frontend');
	}

	if ( get_page_template_slug() == 'media-page.php' ) {
		$media_page_scrips_deps = array('jquery', 'select2-js', 'isotope', 'media-page-lightbox-script');

		wp_enqueue_script( 'select2-js' );
		wp_enqueue_style( 'select2-css' );
		wp_enqueue_script( 'lazysizes' );
		wp_enqueue_script( "isotope", get_bloginfo( 'template_directory' ) . "/js/plugins/isotope2.pkgd.min.js" );

		wp_enqueue_style( "media-page", get_bloginfo( 'template_directory' ) . "/css/media-page.css" );
		wp_enqueue_script( "media-page", get_bloginfo( 'template_directory' ) . "/js/media-page.js", $media_page_scrips_deps );
	}
}

add_action('wp_enqueue_scripts', 'tome_js_frontend');



/**
 * is_edit_page 
 * function to check if the current page is a post edit page
 * 
 * @param  string  $new_edit what page to check for accepts new - new post page ,edit - edit post page, null for either
 * @return boolean
 */
function is_edit_page($new_edit = null){
	global $pagenow;
	//make sure we are on the backend
	if (!is_admin()) return false;


	if($new_edit == "edit")
		return in_array( $pagenow, array( 'post.php',  ) );
	elseif($new_edit == "new") //check for new post page
		return in_array( $pagenow, array( 'post-new.php' ) );
	else //check for either new or edit
		return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
}


/**
 * Enqueue the scripts needed in the admin area
 * Snippet Name: Add admin script on custom post types
 * Snippet URL: http://www.wpcustoms.net/snippets/add-admin-script-on-custom-post-types/
**/
function tome_add_admin_scripts( $hook ) {

	global $post;

	$themeUri = get_template_directory_uri();
	$screen = get_current_screen();

	if ( $hook == 'post-new.php' || $hook == 'post.php' ) {

		if ( 'chapter' === $post->post_type && $screen->base == 'post' ) {

			wp_register_script('tome_chapter_admin_js',  $themeUri . '/tome-deps/tome_post_type_chapter/js/tome-chapter-admin.js', array('jquery'),'1.0',true);
			wp_enqueue_script('tome_chapter_admin_js');

		}

		if ( $screen->post_type === 'tome_place' && $screen->base == 'post' ) {

			wp_register_script('tome_place_js', $themeUri. '/tome-deps/tome_post_type_place/js/tome_place_maps.js', array('jquery', 'google_maps'),'3.0',true);
			wp_enqueue_script('tome_place_js');

		}

		if ( is_edit_page() ) {
			wp_enqueue_script( 'tome_maps' );
			wp_enqueue_script('foundation');
		}

	}


}
add_action( 'admin_enqueue_scripts', 'tome_add_admin_scripts', 10, 1 ); 


function tomef4_add_editor_styles() {
	add_editor_style( 'css/admin-styles.css' );
}
add_action( 'init', 'tomef4_add_editor_styles' );


//Set up our custom image sizes for Tome.
function tome_setup_image_sizes() {

	if( function_exists('add_theme_support') ) {
		add_theme_support('post-thumbnails');
		//Add Special Tome sizes
		add_image_size( 'full-screen', 2400, 9999, false );
		add_image_size( 'mega-image-size', 1400, 1400, false );
		add_image_size( 'big-header', 1360, 780, true); 
		add_image_size( 'half', 400, 9999, false); 
	}

	update_option( 'thumbnail_size_w', 360, true );
	update_option( 'thumbnail_size_h', 360, true );
	update_option( 'thumbnail_crop', 0 );

	update_option( 'medium_size_w', 420, true );
	update_option( 'medium_size_h', 420, true );
	update_option( 'medium_crop', 0 );
	
	update_option( 'large_size_w', 800, true );
	update_option( 'large_size_h', 800, true );
	update_option( 'large_crop', 0 );

	function tome_user_image_sizes( $sizes ){


		return array_merge( $sizes, array(
			'large' => __('Full Column'),
			'half' => __('Half Column'),
			'full-screen' => __('Full Screen'),
			'full' => __('Original size'),
			// 'mega-image-size' => __('Mega Image'),
			// 'tiny-image-size' => __('Tiny Image'),
		) );
	}

	add_filter('image_size_names_choose', 'tome_user_image_sizes');
}

add_action( 'after_setup_theme', 'tome_setup_image_sizes' );


// HTML5 Semantic Image output
/**
 * Improves the caption shortcode with HTML5 figure & figcaption; microdata & wai-aria attributes
 * 
 * @param  string $empty     Empty
 * @param  array  $attr    Shortcode attributes
 * @param  string $content Shortcode content
 */
function tome_img_caption_shortcode_filter($empty, $attr, $content)
{
	extract(shortcode_atts(array(
		'id'      => '',
		'align'   => 'aligncenter',
		'width'   => '',
		'caption' => ''
	), $attr));
	
	// No caption, no dice... But why width? 
	if ( 1 > (int) $attr['width'] || empty( $attr['caption'] ) ) {
		return '';
	}

 
	if ( $id ) {
		$id = esc_attr( $id );
	}
	 
	// Add itemprop="contentURL" to image
	// do_shortcode( $content ) ?
	$classes = tome_get_html_attr_value($content, 'class' );

	$figureTag = '<figure id="' . $id . '" aria-describedby="figcaption_' . $id . '" class="wp-caption ' . esc_attr($align) . ' '.$classes.'" itemscope itemtype="http://schema.org/ImageObject">';
	$figCaptionTag = '<figcaption id="figcaption_'. $id . '" class="wp-caption-text" itemprop="description">' . $caption . '</figcaption>';
	$content = preg_replace('/<img/', '<img itemprop="contentURL"', $content);
	$content = preg_replace('/class="([^"]*)"/i', '', $content);
	$content = preg_replace('/width="([^"]*)"/i', '', $content);
	$content = preg_replace('/height="([^"]*)"/i', '', $content);

	return $figureTag . $content . $figCaptionTag . '</figure>';
}
add_filter( 'img_caption_shortcode', 'tome_img_caption_shortcode_filter', 10, 3 );

// Returns html attributes from a string
function tome_get_html_attr_value( $html_content, $attr_name ) {
	$array = array();
	preg_match( '/'.$attr_name.'="([^"]*)"/i', $html_content, $array ) ;
	return $array[1];
}

// Galleries - Get Rid of default styles
add_filter( 'use_default_gallery_style', '__return_false' );

/////////////// MENUS
if ( ! function_exists('amsf_nav_menus') ) {

// Register Navigation Menus
function amsf_nav_menus() {
	$locations = array(
		'amsf_top_nav' => __( 'Top Bar Nav', 'text_domain' ),
		'amsf_footer_menu' => __( 'Footer Menu', 'text_domain' ),
	);

	register_nav_menus( $locations );
}

// Hook into the 'init' action
add_action( 'init', 'amsf_nav_menus' );

}

//Widget Areas
register_sidebar(array(
	'name'          => __( 'Sidebar widget area', 'theme_text_domain' ),
	'id'            => 'sidebar1',
	'description'   => 'This sidebar will display widgets on the side of pages using a two column layout.',
	'class'         => 'row',
	'before_widget' => '<div id="%1$s" class="widget %2$s large-12 columns">',
	'after_widget'  => '</div>',
	'before_title'  => '<h4 class="widgettitle">',
	'after_title'   => '</h4>' ));

register_sidebar(array(
	'name'          => __( 'Footer Sidebar', 'theme_text_domain' ),
	'id'            => 'sidebar2',
	'description'   => 'This sidebar will display widgets in the footer area.',
	'class'         => 'large-block-grid-4',
	'before_widget' => '<li id="%1$s" class="widget %2$s"><div class="panel">',
	'after_widget'  => '</div></li>',
	'before_title'  => '<h4 class="widgettitle">',
	'after_title'   => '</h4>' ));


//Add custom classes to previous/next 
function posts_link_next_class() {
	return 'class="next-paginav large button expand"';
} 
add_filter('next_posts_link_attributes', 'posts_link_next_class');

function posts_link_prev_class() {
	return 'class="prev-paginav large button expand"';
} 
add_filter('previous_posts_link_attributes', 'posts_link_prev_class');

// From 320Press:   Change the standard class that wordpress puts on the active menu item in the nav bar
//                  Deletes all CSS classes and id's, except for those listed in the array below
function custom_wp_nav_menu($var) {
		return is_array($var) ? array_intersect($var, array(
				//List of allowed menu classes
				'current_page_item',
				'current_page_parent',
				'current_page_ancestor',
				'first',
				'last',
				'vertical',
				'horizontal'
				)
		) : '';
}
// add_filter('nav_menu_css_class', 'custom_wp_nav_menu');
// add_filter('nav_menu_item_id', 'custom_wp_nav_menu');
// add_filter('page_css_class', 'custom_wp_nav_menu');

 
//Replaces "current-menu-item" with "active"
function current_to_active($text){
		$replace = array(
				//List of menu item classes that should be changed to "active"
				'current_page_item' => 'active',
				'current_page_parent' => 'active',
				'current_page_ancestor' => 'active',
		);
		$text = str_replace(array_keys($replace), $replace, $text);
				return $text;
		}
add_filter ('wp_nav_menu','current_to_active');

//From 320Press:    Deletes empty classes and removes the sub menu class
function strip_empty_classes($menu) {
	$menu = preg_replace('/ class=""| class="sub-menu"/','',$menu);
	return $menu;
}
add_filter ('wp_nav_menu','strip_empty_classes');


// allows us to see any errors generated when any plugin is activated
function save_error(){
	file_put_contents(ABSPATH. 'wp-content/error_activation.html', ob_get_contents());
}
add_action('activated_plugin','save_error');


//Tome Specific Bar Nav
//This adds a topbar with some tome controls on the right.
function amsf_tome_topnav() {
	?>
		<nav class="top-bar">
		  <ul class="title-area">
			<!-- Title Area -->
			<li class="name">
			  <h1><a class="brand" id="logo" href="<?php echo get_bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>
			</li>
			<!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
			<li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
		  </ul>
		   <section class="top-bar-section">
	<?php
			wp_nav_menu( 
				array( 
					'theme_location'  => 'amsf_top_nav',
					'container' => 'section',
					'container_class' => 'top-bar-section',
					'menu_class' => 'left',
					'walker' => new top_bar_walker
				)
			);
	?>
			<ul class="right">
				<?php do_action( 'amsf_tome_topnav_right_list' ); ?>
			</ul>
			</section>
		</nav>
	<?php
}

// add the 'has-dropdown' class to any li's that have children and add the arrows to li's with children
// also adds the 'dropdown' to the elements that are one...
class top_bar_walker extends Walker_Nav_Menu
{
	  function start_el(&$output, $item, $depth, $args)
	  {
			global $wp_query;
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
			
			$class_names = $value = '';
			
			// If the item has children, add the dropdown class for foundation
			if ( $args->has_children ) {
				$class_names = "has-dropdown ";
			}
			
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			
			$class_names .= join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
			$class_names .= strtolower(str_replace(" ","-", $item->title));
			$class_names = ' class="'. esc_attr( $class_names ) . '-menu"';
		   
			$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

			$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
			$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
			$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
			$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
			
			$item_output = $args->before;
			$item_output .= '<a'. $attributes .'>';
			$item_output .= $args->link_before .apply_filters( 'the_title', $item->title, $item->ID );
			$item_output .= $args->link_after;
			$item_output .= '</a>';
			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
			}
			
		function start_lvl(&$output, $depth) {
			$indent = str_repeat("\t", $depth);
			$output .= "\n$indent<ul class=\"dropdown\">\n";
		}
			
		function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output )
			{
				$id_field = $this->db_fields['id'];
				if ( is_object( $args[0] ) ) {
					$args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
				}
				return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
			}       
}

//A little function to make breadcrumbs
//Courtesy of catswhocode.com
function the_breadcrumb() {
	if (!is_home()) {

		echo '<ul class="breadcrumbs">';
		echo '<li><a href="';
		echo get_option('home');
		echo '">';
		bloginfo('name');
		echo "</a></li>";
		if (is_category() || is_single()) {
			echo '<li>';
			the_category('</li><li>');
			echo '</li>';
			if (is_single()) {
				echo '<li class="current"><a href="';
				the_permalink();
				echo '">';
				the_title();
				echo '</a></li>';
			}
		} elseif (is_page()) {
				echo '<li class="current"><a href="';
				the_permalink();
				echo '">';
				the_title();
				echo '</a></li>';
		}
		echo '</ul>';
	}
}


/*----------

media-page.php (media page) funcions

----------*/
function get_portrait_class( $image_width, $image_height ) {

	$imgClass = "";
	$attachment_aspect_ratio = $image_width / $image_height;

	if ( $attachment_aspect_ratio < 1 ) {
		$imgClass = ' portraitimg';
	};

	return $imgClass;

}

function get_gallery_image_tags( $post ) {

	$tags = wp_get_post_tags( $post->ID );
	$image_tags = "";

	if ($tags) {

		foreach($tags as $tag) {
			$image_tags .= $tag->slug . ' ';
		}

	}

	return $image_tags;
}






/*----------

Tome TinyMCE Plugins
TODO : This probably needs to be cleaned up.

----------*/

require( 'tome-editor-buttons/tome-mce-buttons.php' );


// This guy came out of _s theme. It is cool, but actually it's cool to know that if this
//is missing, then no comments are output, only a form...
if ( ! function_exists( 'tometheme_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
function tometheme_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;

	if ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) : ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
		<div class="comment-body">
			<?php _e( 'Pingback:', 'tometheme' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'tometheme' ), '<span class="edit-link">', '</span>' ); ?>
		</div>

	<?php else : ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
					<?php printf( __( '%s <span class="says">says:</span>', 'tometheme' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
				</div><!-- .comment-author -->

				<div class="comment-metadata">
					<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
						<time datetime="<?php comment_time( 'c' ); ?>">
							<?php printf( _x( '%1$s at %2$s', '1: date, 2: time', 'tometheme' ), get_comment_date(), get_comment_time() ); ?>
						</time>
					</a>
					<?php edit_comment_link( __( 'Edit', 'tometheme' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .comment-metadata -->

				<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'tometheme' ); ?></p>
				<?php endif; ?>
			</footer><!-- .comment-meta -->

			<div class="comment-content">
				<?php comment_text(); ?>
			</div><!-- .comment-content -->

			<?php
				comment_reply_link( array_merge( $args, array(
					'add_below' => 'div-comment',
					'depth'     => $depth,
					'max_depth' => $args['max_depth'],
					'before'    => '<div class="reply">',
					'after'     => '</div>',
				) ) );
			?>
		</article><!-- .comment-body -->

	<?php
	endif;
}
endif; // ends check for tometheme_comment()



add_action('media_buttons',  'custom_add_buttons');

function custom_add_buttons() {
	$admin_url = get_admin_url();
	if( post_type_exists( "tome_place" ) )
		echo '<a href="'.$admin_url.'admin-ajax.php?action=add_shortcode&type=tome_place" data-editor="content" title="Add Place" class="thickbox button" style="float: right">Add Place</a>';

	if( post_type_exists( "tome_media" ) )
		echo '<a href="'.$admin_url.'admin-ajax.php?action=add_shortcode&type=tome_media" data-editor="content" title="Add Embedded Media" class="thickbox button" style="float: right">Add Embedded Media</a>';

	if( post_type_exists( "tome_gallery" ) )
		echo '<a href="'.$admin_url.'admin-ajax.php?action=add_shortcode&type=tome_gallery" data-editor="content" title="Add Gallery" class="thickbox button" style="float: right">Add Gallery</a>';
}

function html2text($Document) {
	$Rules = array ('@<script[^>]*?>.*?</script>@si',
		'@<[\/\!]*?[^<>]*?>@si',
		'@([\r\n])[\s]+@',
		'@&(quot|#34);@i',
		'@&(amp|#38);@i',
		'@&(lt|#60);@i',
		'@&(gt|#62);@i',
		'@&(nbsp|#160);@i',
		'@&(iexcl|#161);@i',
		'@&(cent|#162);@i',
		'@&(pound|#163);@i',
		'@&(copy|#169);@i',
		'@&(reg|#174);@i',
		'@&#(d+);@e'
	);
	$Replace = array ('',
		'',
		'',
		'',
		'&',
		'<',
		'>',
		' ',
		chr(161),
		chr(162),
		chr(163),
		chr(169),
		chr(174),
		'chr()'
	);
	return preg_replace($Rules, $Replace, $Document);
}

function cleanString($text) {
	// 1) convert á ô => a o
	$text = preg_replace("/[áàâãªä]/u","a",$text);
	$text = preg_replace("/[ÁÀÂÃÄ]/u","A",$text);
	$text = preg_replace("/[ÍÌÎÏ]/u","I",$text);
	$text = preg_replace("/[íìîï]/u","i",$text);
	$text = preg_replace("/[éèêë]/u","e",$text);
	$text = preg_replace("/[ÉÈÊË]/u","E",$text);
	$text = preg_replace("/[óòôõºö]/u","o",$text);
	$text = preg_replace("/[ÓÒÔÕÖ]/u","O",$text);
	$text = preg_replace("/[úùûü]/u","u",$text);
	$text = preg_replace("/[ÚÙÛÜ]/u","U",$text);
	$text = preg_replace("/[’‘‹›‚]/u","'",$text);
	$text = preg_replace("/[“”«»„]/u",'"',$text);
	$text = str_replace("–","-",$text);
	$text = str_replace(" "," ",$text);
	$text = str_replace("ç","c",$text);
	$text = str_replace("Ç","C",$text);
	$text = str_replace("ñ","n",$text);
	$text = str_replace("Ñ","N",$text);

	//2) Translation CP1252. &ndash; => -
	$trans = get_html_translation_table(HTML_ENTITIES);
	$trans[chr(130)] = '&sbquo;';    // Single Low-9 Quotation Mark
	$trans[chr(131)] = '&fnof;';    // Latin Small Letter F With Hook
	$trans[chr(132)] = '&bdquo;';    // Double Low-9 Quotation Mark
	$trans[chr(133)] = '&hellip;';    // Horizontal Ellipsis
	$trans[chr(134)] = '&dagger;';    // Dagger
	$trans[chr(135)] = '&Dagger;';    // Double Dagger
	$trans[chr(136)] = '&circ;';    // Modifier Letter Circumflex Accent
	$trans[chr(137)] = '&permil;';    // Per Mille Sign
	$trans[chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
	$trans[chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
	$trans[chr(140)] = '&OElig;';    // Latin Capital Ligature OE
	$trans[chr(145)] = '&lsquo;';    // Left Single Quotation Mark
	$trans[chr(146)] = '&rsquo;';    // Right Single Quotation Mark
	$trans[chr(147)] = '&ldquo;';    // Left Double Quotation Mark
	$trans[chr(148)] = '&rdquo;';    // Right Double Quotation Mark
	$trans[chr(149)] = '&bull;';    // Bullet
	$trans[chr(150)] = '&ndash;';    // En Dash
	$trans[chr(151)] = '&mdash;';    // Em Dash
	$trans[chr(152)] = '&tilde;';    // Small Tilde
	$trans[chr(153)] = '&trade;';    // Trade Mark Sign
	$trans[chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
	$trans[chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
	$trans[chr(156)] = '&oelig;';    // Latin Small Ligature OE
	$trans[chr(159)] = '&Yuml;';    // Latin Capital Letter Y With Diaeresis
	$trans['euro'] = '&euro;';    // euro currency symbol
	ksort($trans);

	foreach ($trans as $k => $v) {
		$text = str_replace($v, $k, $text);
	}

	// 3) remove <p>, <br/> ...
	$text = strip_tags($text);

	// 4) &amp; => & &quot; => '
	$text = html_entity_decode($text);

	// 5) remove Windows-1252 symbols like "TradeMark", "Euro"...
	$text = preg_replace('/[^(\x20-\x7F)]*/','', $text);

	$targets=array('\r\n','\n','\r','\t');
	$results=array(" "," "," ","");
	$text = str_replace($targets,$results,$text);

	//XML compatible
	/*
	$text = str_replace("&", "and", $text);
	$text = str_replace("<", ".", $text);
	$text = str_replace(">", ".", $text);
	$text = str_replace("\\", "-", $text);
	$text = str_replace("/", "-", $text);
	*/

	return ($text);
}


function tooltip_pre_save_handler( $post_id ) {

	// check if this is to be a new post
//    file_put_contents('php://stderr', print_r("testing123", TRUE));
//    trigger_error("test123");
	if( $post_id != 'new' ) {
		return $post_id;
	}

	// Create a new post
	$post = array(
		'post_status'  => 'draft' ,
		'post_title'  => 'A title, maybe a $_POST variable' ,
		'post_type'  => 'post' ,
	);


	// insert the post
	$post_id = wp_insert_post( $post );

	// save the fields to the post
	do_action( 'acf/save_post' , $post_id );

	// return the new ID
	return $post_id;

}

add_action('acf/pre_save_post' , 'tooltip_pre_save_handler', 5);

function add_shortcode_ajax() {
	if( $_GET['type'] ) {
		if( $posts = get_posts( array (
			"post_type" => $_GET['type'],
			"posts_per_page" => -1 
		) ) ) {

			if ( $_GET['type'] == 'tome_gallery' )
				$params = " size=\'full-column\'";


			echo "<h2>Click to insert:</h2>";

			echo "<ul>";
			foreach($posts as $post ) {
					$shortcode = sprintf( "'[%s id=\'%s\'%s]'",
						$_GET['type'],
						$post->ID,
						$params
					);
				printf( '<li><a href="javascript:window.send_to_editor(%s); tb_remove();">%s</a></li>',
					$shortcode,
					$post->post_title
				);
			}
			echo "</ul>";

		} else {
			$name = ucfirst( str_replace( "tome_", "", $_GET['type'] ) . "s" );
			$type = $_GET['type'];
			echo "<h3>There are no $name to add.</h3>";
			echo "<p>You can create new $name <a href='/wp-admin/edit.php?post_type=$type'>here</a></p>";
		}
	}
	die;
}

add_action('wp_ajax_add_shortcode', 'add_shortcode_ajax');


// meta box for cover page
function add_media_attachment_box() 
{
	add_meta_box("media_meta", "Media Page", "media_meta", "attachment", "side", "high");
}

add_action( 'add_meta_boxes', 'add_media_attachment_box' );

function media_meta()
{
	global $post;
	printf( "Exclude from Media Page? &nbsp; <input type='checkbox' name='media_page_exclude' value='1' %s />",
		get_post_meta( $post->ID, "media_page_exclude", true) == 1 ? "checked='checked'" : ""
	);
}

// exclude some media from 
add_action('edit_attachment', 'save_tome_media_attachment');
add_action('add_attachment', 'save_tome_media_attachment');

function save_tome_media_attachment( $post_id ){
	$checked = 1;
	if( !isset( $_POST["media_page_exclude"]) )
		$checked = 0;
	update_post_meta( $post_id, "media_page_exclude", $checked );
}

function wptp_add_tags_to_attachments() {
	register_taxonomy_for_object_type( 'post_tag', 'attachment' );
}
add_action( 'init' , 'wptp_add_tags_to_attachments' );


///  PREV / NEXT LINKS FOR MEDIA
add_filter( 'get_previous_post_join', 'adjacent_media_join' );
add_filter( 'get_next_post_join', 'adjacent_media_join' );

function adjacent_media_join( $join ) {
	global $post;
	global $wpdb;
	if( in_array( $post->post_type, array("tome_media", "attachment" ) ) )
		return "JOIN $wpdb->postmeta ON(p.ID = $wpdb->postmeta.post_id)";
	else
		return $join;
}


add_filter( 'get_previous_post_where', 'adjacent_media_where' );
add_filter( 'get_next_post_where', 'adjacent_media_where' );

function adjacent_media_where( $where ) {
	global $post;
	global $wpdb;

	if($post->post_type == "tome_media") {
		$args = array(
		'post_parent' => $post->ID,
		'post_type'   => 'attachment', 
		'numberposts' => 1 );
		$child = reset( get_children( $args ) );

		//$where = preg_replace("/post_type = '.*'/", "post_type = 'attachment'", $where);
		// the query date should be the date of the attachment, not the media post, since they're all ordered by attachment
		$where = preg_replace("/post_date (.) '.*'/", "post_date $1 '" . $child->post_date . "'", $where); 
		$where .= " AND post_type = 'attachment'";
		$where .= " AND ($wpdb->postmeta.meta_key = 'media_page_exclude' AND $wpdb->postmeta.meta_value != 1)";
		
	} elseif($post->post_type == "attachment") {
		global $wp_query;
		//print_r($wp_query);
		$wp_query->is_attachment = null;
		$where = str_replace("AND p.post_status = 'publish'", "", $where);
		$where .= " AND $wpdb->postmeta.meta_key = 'media_page_exclude' AND $wpdb->postmeta.meta_value != 1";
	}


	return $where;   
}

add_filter( 'previous_post_link', 'adjacent_media_link', 10, 4 );
add_filter( 'next_post_link', 'adjacent_media_link', 10, 4 );

function adjacent_media_link( $output, $format, $link, $post ) {
	if($post->post_type == "attachment") {
		$parent = get_post($post->post_parent);
		if($parent->post_type == "tome_media") {
			$output = preg_replace('/href=".*"/', 'href="' . get_permalink( $parent->ID ) . '"', $output);
		}
	}
	return $output;

}

//Function to set default permalinks style to Tome recommended /%postname%/
function tome_reset_permalinks() {
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	$wp_rewrite->flush_rules();
}




//Handles setup on theme switch
add_action( "after_switch_theme", "tome_reset_permalinks", 10,  0);
add_action( 'admin_init', 'tome_reset_permalinks' );

add_filter( 'acf/fields/wysiwyg/toolbars' , 'my_toolbars'  );
function my_toolbars( $toolbars )
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

function clean_line_breaks($s, $replace_with='<br/>'){
	$s = str_replace("\r\n", $replace_with, $s);
	$s = str_replace("\r", $replace_with, $s);
	$s = str_replace("\n", $replace_with, $s);
	return $s;
}

function tome_tinymce_settings( $settings ) {
	$settings['paste_as_text'] = true;
	return $settings;
}
add_filter( 'tiny_mce_before_init', 'tome_tinymce_settings' );


function hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
	  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
	  $r = hexdec(substr($hex,0,2));
	  $g = hexdec(substr($hex,2,2));
	  $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}

// fix for shortcode returns being given extra paragraph tags everywhere.
//add_filter( 'the_content', 'shortcode_unautop',100 );

//acf_enqueue_uploader();

//----------------------------------------------------//
// function tome_media_strings($strings) {
//     //print_r($strings);
//     //unset($strings['insertIntoPost']);
//     return $strings;
// }
// add_filter('media_view_strings','tome_media_strings');

?>