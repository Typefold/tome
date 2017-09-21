<?php

/**
 * If WPML is activated add language switcher to the top menu
 */
if ( function_exists( 'icl_object_id' ) ) {

	function tome_get_wpml_language_name($code=''){
		
		global $sitepress;
		
		$details = $sitepress->get_language_details($code);
		$language_name = $details['english_name'];
		
		return $language_name;

	}

	function top_menu_tome_language_switcher(){
		
		$languages = icl_get_languages('skip_missing=0&orderby=code');

		echo '<li class="has-dropdown not-click">';
		echo '<a>'. tome_get_wpml_language_name(ICL_LANGUAGE_CODE) .'</a>';
		echo '<ul class="dropdown">';

			if ( ! empty( $languages ) ) {

				foreach ( $languages as $lang ) {

					if ( ! $lang['active'] ) {
						echo '<li><a href="'.$lang['url'].'">' . icl_disp_language($lang['translated_name']) . '</a></li>';
					}

				}

			}

		echo '</ul>';
		echo '</li>';
	}

	add_action( 'tome_topnav_right_list', 'top_menu_tome_language_switcher' );

}

function tome_language_switcher()
{
	if ( $_GET['lang'] ) {
		$current_language = $_GET['lang'];
	} else {
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
