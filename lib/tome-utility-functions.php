<?php

/**
 * is_edit_page 
 * function to check if the current page is a post edit page
 * 
 * @param  string  $new_edit what page to check for accepts new - new post page ,edit - edit post page, null for either
 * @return boolean
 */
function is_edit_page( $new_edit = null ){
	global $pagenow;
	//make sure we are on the backend
	if ( !is_admin() ) return false;

	if( $new_edit == 'edit' ) {
		return in_array( $pagenow, array( 'post.php' ) );
	} elseif( $new_edit == 'new' ) {
		//check for new post page
		return in_array( $pagenow, array( 'post-new.php' ) );
	} else {
		//check for either new or edit
		return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
	}
}


function clean_line_breaks($s, $replace_with='<br/>'){
	$s = str_replace("\r\n", $replace_with, $s);
	$s = str_replace("\r", $replace_with, $s);
	$s = str_replace("\n", $replace_with, $s);
	return $s;
}

function hex2rgb($hex) {

	$hex = str_replace( '#', '', $hex);

	if( strlen( $hex ) == 3 ) {
		$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
		$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
		$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
	} else {
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
	}

	$rgb = array($r, $g, $b);
	
	//return implode(",", $rgb); // returns the rgb values separated by commas
	
	return $rgb; // returns an array with the rgb values
}

/*----------

media-page.php (media page) funcions:

tome_get_portrait_class()
tome_get_gallery_image_tags()

----------*/
function tome_get_portrait_class( $image_width, $image_height ) {

	$imgClass = '';
	$attachment_aspect_ratio = $image_width / $image_height;

	if ( $attachment_aspect_ratio < 1 ) {
		$imgClass = 'portraitimg';
	};

	return $imgClass;

}

function tome_get_gallery_image_tags( $post ) {

	$tags = wp_get_post_tags( $post->ID );
	$image_tags = "";

	if ($tags) {

		foreach($tags as $tag) {
			$image_tags .= $tag->slug . ' ';
		}

	}

	return $image_tags;
}

//TODO: html2text and cleanString are gross and need to be replaced
// They are used to help with preparing text for storage  
// in data attributes in tooltips.
function html2text( $Document ) {

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

function cleanString( $text ) {

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

// Error Logging: Allows us to see any errors generated when any plugin is activated
// TODO: Replace. All error logging should be done using WP_DEBUG & The Debug Bar Plugin integration
function tome_save_error(){
	file_put_contents(ABSPATH. 'wp-content/error_activation.html', ob_get_contents());
}
add_action('activated_plugin','tome_save_error');

//TODO: Probably OK to remove, for now
// Function to make breadcrumbs, courtesy of catswhocode.com
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