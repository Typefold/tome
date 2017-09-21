<?php
/////////////// MENUS
if ( ! function_exists('tome_nav_menus') ) {

// Register Navigation Menus
function tome_nav_menus() {
	$locations = array(
		'tome_top_nav' => __( 'Top Bar Nav', 'text_domain' ),
		'tome_footer_menu' => __( 'Footer Menu', 'text_domain' ),
	);

	register_nav_menus( $locations );
}

// Hook into the 'init' action
add_action( 'init', 'tome_nav_menus' );

}

//Widget Areas
//TODO DELETE THESE?
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
function tome_posts_link_next_class() {
	return 'class="next-paginav large button expand"';
} 
add_filter('next_posts_link_attributes', 'tome_posts_link_next_class');

function tome_posts_link_prev_class() {
	return 'class="prev-paginav large button expand"';
} 
add_filter('previous_posts_link_attributes', 'tome_posts_link_prev_class');
 
//Replaces "current-menu-item" with "active"
function tome_current_to_active($text){
		$replace = array(
				//List of menu item classes that should be changed to "active"
				'current_page_item' => 'active',
				'current_page_parent' => 'active',
				'current_page_ancestor' => 'active',
		);
		$text = str_replace(array_keys($replace), $replace, $text);
				return $text;
		}
add_filter ('wp_nav_menu','tome_current_to_active');

//From 320Press:    Deletes empty classes and removes the sub menu class
function tome_strip_empty_classes($menu) {
	$menu = preg_replace('/ class=""| class="sub-menu"/','',$menu);
	return $menu;
}
add_filter ('wp_nav_menu','tome_strip_empty_classes');




//Tome Specific Bar Nav
//This adds a topbar with some tome controls on the right.
function tome_topnav() {
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
					'theme_location'  => 'tome_top_nav',
					'container' => 'section',
					'container_class' => 'top-bar-section',
					'menu_class' => 'left',
					'walker' => new tome_top_bar_walker
				)
			);
	?>
			<ul class="right">
				<?php do_action( 'tome_topnav_right_list' ); ?>
			</ul>
			</section>
		</nav>
	<?php
}

// add the 'has-dropdown' class to any li's that have children and add the arrows to li's with children
// also adds the 'dropdown' to the elements that are one...
class tome_top_bar_walker extends Walker_Nav_Menu
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