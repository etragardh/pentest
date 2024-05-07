<?php 

/**
 * WPML support shortcodes
 *  
 */

 
add_filter( 'wpml_pb_shortcode_content_for_translation', 'ct_wpml_filter_content_for_translation', 10 , 2 );
 
function ct_wpml_filter_content_for_translation( $content, $post_id ) {

	// skip if has JSON
	if ( oxygen_json_has_elements(get_post_meta( $post_id, "ct_builder_json", true )) ) {
		return $content;
	}

	$shortcodes = get_post_meta( $post_id, "ct_builder_shortcodes", true );
	if ( $shortcodes ) {
		$content = $shortcodes;
	}
	return $content;
}

add_filter( 'wpml_pb_shortcodes_save_translation', 'ct_wpml_filter_save_translation', 10, 3 );

function ct_wpml_filter_save_translation( $saved, $translated_post_id, $new_content ) {
	
	// skip if has JSON
	if ( oxygen_json_has_elements(get_post_meta( $translated_post_id, "ct_builder_json", true )) ) {
		return true;
	}
	
	update_post_meta( $translated_post_id, "ct_builder_shortcodes", $new_content );
	return true;
}

/**
 * WPML support JSON
 * 
 */


define( 'OXY_NAME', 'Oxygen Builder' );

function oxygen_builder_support_required( $plugins ) {
	$plugins[] = OXY_NAME; // Include an identifier for this plugin
	return $plugins;
}
add_filter( 'wpml_page_builder_support_required', 'oxygen_builder_support_required' );
	

function oxy_wpml_register_strings( $post, $package_data ) { 

	if ( 'Oxygen Builder' === $package_data['kind'] ) {
		// get JSON
		$json = get_post_meta( $post->ID, "ct_builder_json", true );
		$tree = json_decode( $json, true);
		
		oxy_wpml_register_strings_recursion($tree, $package_data);
	}
}
add_action( 'wpml_page_builder_register_strings', 'oxy_wpml_register_strings', 10, 2 );

function oxy_wpml_register_strings_recursion($tree, $package_data) {
	if (isset($tree['children'])) {
		foreach ($tree['children'] as $key => $element) {
			if (isset($element['options']['ct_content'])) {
				do_action(
					'wpml_register_string',
					$element['options']['ct_content'], // the actual string value
					'oxy-element-' . $element['id'], // a unique identifier for this string.
					$package_data,
					$element['options']['selector'] , // a title for this string
					'LINE' // the string type: 'LINE', 'TEXTAREA', 'VISUAL', 'LINK'
				);
			}
			if (isset($element['children'])) {
				oxy_wpml_register_strings_recursion($element, $package_data);
			}
		}
	}
}

function oxy_wpml_translated(
	$package_kind,
	$translated_post_id,
	$original_post,
	$string_translations,
	$lang
) {

	// Make sure the package is for our plugin
	if ( OXY_NAME === $package_kind ) {

		// Get the data from the original post
		// We'll then update the data with the translated strings and
		// save to the translated post.
		$json = get_post_meta( $original_post->ID, "ct_builder_json", true );
		$tree = json_decode( $json, true );
		$tree = oxy_wpml_translated_recursion($tree, $string_translations, $translated_post_id, $lang);
		$json = json_encode( $tree );

		// Save the post data that now includes the translations to the translated post.
		update_post_meta( $translated_post_id, "ct_builder_json", $json );
	}
}
add_action( 'wpml_page_builder_string_translated', 'oxy_wpml_translated', 10, 5 );

function oxy_wpml_translated_recursion($tree, $string_translations, $translated_post_id, $lang) {
	// Go through all the elements to replace their text
	if ( isset($tree['children'])) {
		foreach( $tree['children'] as $key => $element ) {
			$string_id = 'oxy-element-' . $element['id'];
			if ( isset($element['options']['ct_content']) ) {
				if ( isset($string_translations[$string_id][$lang]['value'] ) ) {
					$tree['children'][$key]['options']['ct_content'] = $string_translations[$string_id][$lang]['value'];
				}
			}
			if (isset($element['children'])) {
				$tree['children'][$key] = oxy_wpml_translated_recursion($element, $string_translations, $translated_post_id, $lang);
			}
		}
	}

	return $tree;
}