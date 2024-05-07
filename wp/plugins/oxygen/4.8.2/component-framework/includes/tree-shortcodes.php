<?php 

/**
 * Interface to transform Components Tree JSON object to
 * WordPress shortcodes and vice versa.
 *  
 */


/**
 * Transform JSON Components Tree to WordPress nested shortcodes
 * 
 * @return string
 * @since 0.1
 */

function components_json_to_shortcodes( $json, $reusable = false ) {

	$components_tree = json_decode( $json, true );

	if ( $reusable ) {
		$components_tree['children'] = ct_update_ids( $components_tree['children'], 1, $components_tree );
	};

	if ( is_null ( $components_tree ) )
		return false;
	
	if ( !array_key_exists( 'children', $components_tree ) )
		return false;
	
	$output = parse_components_tree( $components_tree['children'] );

	return $output;
}

/**
 * Recursive function that escapes operator/relation/compare param values for wp_advanced_query
 *
 * @since 3.7
 */

function ct_rec_aq_symbols(&$arr, $decode=false) {
	foreach($arr as $key => $item) {
		if(!empty($item['values']) && is_array($item['values'])) {
			ct_rec_aq_symbols($arr[$key]['values'], $decode);
		}
		elseif(!empty($item['key']) && !empty($item['value']) && in_array($item['key'], array('compare', 'operator'))) {
			if($decode) {
				$arr[$key]['value'] = base64_decode($item['value']);	
			} else {
				$arr[$key]['value'] = base64_encode($item['value']);
			}
		}
	}
}

/**
 * Recursive function that actually transform an Object to WordPress shortcodes
 *
 * @since 0.1
 */

function parse_components_tree( $components_tree ) {

	global $oxygen_signature;

	if ( !is_array( $components_tree ) ) {
		return false;
	}

	$output = "";

	foreach( $components_tree as $id => $item ) {
		$name = sanitize_text_field( $item['name'] );
		$ct_options = null;
		$shortcode_atts = array();
		$ct_options_string = null;
		$ct_content = '';
		$nested = false;

		$full_shortcode = false;

		// handle 'Shortcode' component
		if ( $item['name'] == "ct_shortcode" ) {

			// get 'original' options
			$original = $item['options']['original'];

			if ( isset($original['full_shortcode']) && $original['full_shortcode'] ) {

				$full_shortcode = $original['full_shortcode'];
				$original = array();
				$item['options']['original']['full_shortcode'] = true;
			}
		}
		
		// check if nested column or section
		if ( $item['depth'] > 1 && in_array( $item['name'], array( 'ct_link', 'ct_section', 'ct_container', 'ct_inner_content', 'ct_columns', 'ct_column', 'ct_new_columns', 'ct_div_block', 'ct_nestable_shortcode', 'oxy_superbox', 'oxy_toggle', 'oxy_tab', 'oxy_tabs', 'oxy_tab_content', 'oxy_tabs_contents', 'oxy_dynamic_list', 'oxy-product-builder', 'ct_modal', 'oxy_header', 'oxy_header_row', 'oxy_header_right', 'oxy_header_center', 'oxy_header_left' ) ) ) {
			$nested = true;
		}

		// add depth suffix if needed
		if ( $nested ) {
			$name .= '_' . $item['depth'];
		}
		
		// add shortcode parameters
		if ( is_array( $item['options'] ) ) {
			
			foreach ( $item['options'] as $key => $value ) {
	
				if ( $key == "url" && $item['name'] == "embed" ) {
					unset( $item['options']['url'] );
				}

				if ( $key == "classes" ) {
					continue;
				}

				if ( is_array( $value ) ) {

					if ( ! empty( $value ) ) {

						foreach ( $value as $array_key => $array_value ) {
							if($array_key === 'wp_query_advanced') {
								// convert symbols like < > == to base64, so that it does not break the shortcodes
								ct_rec_aq_symbols($item['options'][$key][$array_key]);
							}
							// make sure widget parameters won't brake the shortcode with quotes
							if ( $item['name'] == "ct_widget" && $array_key == "instance" ) {
								$item['options'][$key][$array_key] = ct_encode_widget_instance($array_value);
								$item['options'][$key]['paramsBase64'] = true;
							}

							if ( $array_key == "custom-css" ) {
								$item['options'][$key][$array_key] = normalize_custom_css( $array_value );
							}

							$options_to_encode = ["code-php","code-css","code-js","alt",
												  "testimonial_text","testimonial_author","testimonial_author_info",
												  "icon_box_heading","icon_box_text",
												  "progress_bar_left_text","progress_bar_right_text",
												  'pricing_box_package_title','pricing_box_package_subtitle','pricing_box_content','pricing_box_package_regular'];

							if ( in_array($array_key, $options_to_encode) ) {
								$option_value = $item['options'][$key][$array_key];
								// sign shortcodes before base64_encode, or later encoded shortcodes won't be signed
								$option_value = preg_replace_callback('/\[oxygen ([^\]]*)\]/i', 'ct_sign_oxy_dynamic_shortcode', $option_value);
								$item['options'][$key][$array_key] = base64_encode($option_value);
							}

							$options_to_encode_backward_compat = [					  
								"paginate_prev_link_text","paginate_next_link_text"
							];

							$options_to_encode_backward_compat = apply_filters("oxy_base64_encode_options", $options_to_encode_backward_compat);

							if ( in_array($array_key, $options_to_encode_backward_compat) ) {
								$item['options'][$key][$array_key] = oxygen_vsb_base64_encode($item['options'][$key][$array_key]);
							}

							if ( $array_key == "url" ) {
								$item['options'][$key][$array_key] = oxygen_vsb_quotes_encode($item['options'][$key][$array_key]);
								$item['options'][$key]["url_encoded"] = "true";
							}

							if($array_key == 'globalconditions') {
								foreach($item['options'][$key][$array_key] as $conditionKey => $condition) {
									if (stripos($condition['value'], '[oxygen')  !== false) {
										$value = ct_sign_oxy_dynamic_shortcode(array($condition['value']));
									}
									else {
										$value = $condition['value'];
									}
									$item['options'][$key][$array_key][$conditionKey]['value'] = base64_encode($value);
									if (isset($item['options'][$key][$array_key][$conditionKey]['searchValue'])) {
										if (stripos($condition['value'], '[oxygen')  !== false) {
											$searchValue = ct_sign_oxy_dynamic_shortcode(array($condition['searchValue']));
										}
										else {
											$searchValue = $condition['searchValue'];
										}
										$item['options'][$key][$array_key][$conditionKey]['searchValue'] = base64_encode($searchValue);
									}
								}
							}
						}
					}
					else {
						unset( $item['options'][$key] );
					}
				}
				elseif ( $key == "ct_content" ) {
					
					$ct_content = $item['options']['ct_content'];

					unset($item['options'][$key]);// = htmlspecialchars( $value, ENT_QUOTES );
				}
			}

			$item['options']['ct_depth'] = $item['depth'];

			$value = json_encode( $item['options'], JSON_FORCE_OBJECT );

			$value = ct_unicode_decode( $value );
			$shortcode_atts['ct_options'] = $value;
			$ct_options_string = "ct_options='{$value}'";
		}

		// handle embed URL
		if ( isset( $item['options']['url'] ) && $item['name'] == "embed" ) {
			$output .= $item['options']['url'];
		}

		$ct_content = oxygen_vsb_filter_shortcode_content_encode($ct_content);

		if ( isset($item['children']) ) {
			if ( !empty( $ct_content ) ) {
				// if we have content and children at the same time
				$temp_content = $ct_content;

				$shortcodes = array();

				// get shortcodes for each child
				foreach( $item['children'] as $id => $child ) {
					// check if placeholder is in the outer template
					$placeholder_id = ($child['id']>=100000) ? $child['id']-100000 : $child['id'];
					$shortcodes["<span id=\"ct-placeholder-{$placeholder_id}\"></span>"] = parse_components_tree( array( $id => $child ) );
				}
				// replace placeholders with shortcodes
				foreach($shortcodes as $key => $val) {
					$temp_content = str_replace($key,  $val, $temp_content);
				}

				// output
				$ct_content = $temp_content;
			} else {
				// go deeper into the tree if item has children and have no content
				$ct_content .= parse_components_tree( $item['children'] );
			}
		}

		// Component classes aren't coupled here, so leverage WordPress filters for validation
		if ( false !== $component = apply_filters( "oxygen_vsb_filter_{$item['name']}", array( 'item' => $item, 'content' => $ct_content ) ) ) {
			if ( $item['name'] == "ct_shortcode" && $full_shortcode) {
				$component['content'] = $full_shortcode;
			}
			
			// sign oxy shortcodes inside the content
			$component['content'] = preg_replace_callback('/\[oxygen ([^\]]*)\]/i', 'ct_sign_oxy_dynamic_shortcode', $component['content']);
			
			// sign oxy shortcodes inside the properties
			if(isset($shortcode_atts['ct_options'])) {

				$shortcode_atts['ct_options'] = preg_replace_callback('/\[oxygen ([^\]]*)\]/i', 'ct_sign_oxy_dynamic_shortcode', $shortcode_atts['ct_options']);
				$ct_options_string = "ct_options='".$shortcode_atts['ct_options']."'";

			}
			
			// Generate signature
			$signature = $oxygen_signature->generate_signature_shortcode_string( $name, $shortcode_atts, $component['content'] );
			// Generate output
			$output .= "[{$name} {$signature} {$ct_options_string}]{$component['content']}[/{$name}]";
		}

	}
	
	return $output;
}


function ct_sign_oxy_dynamic_shortcode($results) {

	if ( !oxygen_vsb_current_user_can_access() ) {
		return $results[0];
	}
	
	global $oxygen_signature;
	
	$signature_arg = $oxygen_signature->get_shortcode_signature_arg();

	$pattern = get_shortcode_regex( array('oxygen') );
	$result = str_replace('\"', '"', $results[0]);
 	preg_match_all( "/$pattern/", $result, $m );

 	$tag = $m[2][0];
 	
 	$attr = shortcode_parse_atts( $m[3][0] );

 	if(isset($attr[$signature_arg])) {
 		unset($attr[$signature_arg]);
 	}

  	$signature = $oxygen_signature->generate_signature_shortcode_string( $tag, $attr, null );

 	$options_string = "";
	
	if (is_array($attr)) {
		foreach($attr as $key => $val) {
			$options_string .= "{$key}='{$val}' "; 
		}
	}

	return "[{$tag} {$signature} {$options_string}]";

}

/**
 * Update IDs for Re-usable parts start from $counter
 *
 * @since 0.2.3
 */

function ct_update_ids( $components_tree, $count, &$parent ) {

	global $counter;

	$counter = $count;

	foreach ( $components_tree as $key => $child ) {
		// update placeholder id's
		if(isset($parent['options']['ct_content'])) {
			$parent['options']['ct_content'] = str_replace("ct-placeholder-" . $components_tree[$key]['options']['ct_id'], 
												"ct-placeholder-" . $counter, 
												$parent['options']['ct_content']);
		}
		
		$components_tree[$key]['id'] 					= $counter;
		$components_tree[$key]['options']['ct_id'] 		= $counter;
		$components_tree[$key]['options']['ct_parent'] 	= $parent['id'];

		$counter++;

		if ( $components_tree[$key]['children'] ) {
			$components_tree[$key]['children'] = ct_update_ids( $components_tree[$key]['children'], $counter, $components_tree[$key] );
		}
	}

	return $components_tree;
}


/**
 * Transform WordPress post content to JSON Components Tree
 * 
 * @return JSON or false
 * @since 0.1
 */

function content_to_components_json( $content ) {

	$shortcodes = parse_shortcodes( $content );

	if ( $shortcodes['is_shortcode'] === false && $content != "" ) {
		return json_encode( false );
	}

	$root = array ( 
		"id"	=> 0,
		"name" 	=> "root",
		"depth"	=> 0 
	);
	
	$root['children'] = $shortcodes['content'];

	$components_tree = json_encode( $root );

	if ( is_null( $components_tree ) ) {
		return false;
	}
	else {
		return $components_tree;
	}
}

/**
 * Recursively deobfucate value of a param, in a nested array
 *
 * @author Gagan S Goraya
 * @since 3.7
 */

function ct_rec_deobfuscate_param(&$children, $param) {
	foreach($children as $key => $item) {
		if(is_array($item)) {
			ct_rec_deobfuscate_param($children[$key], $param);
		} elseif($key === $param && strpos($item, '+oxygen') !== false) {
			$children[$key] = preg_replace_callback('/\+oxygen(.+?)\+/i', 'ct_deobfuscate_oxy_url', $item);
		}
	}
}


/**
 * Recursive function that actually transform WordPress shortcodes to Array
 *
 * @return Array
 * @since 0.1
 */

function parse_shortcodes( $content, $is_first = true, $verify_signature = true ) {

	$content = ct_obfuscate_shortcode($content);

	$pattern = get_shortcode_regex();
	preg_match_all( '/'. $pattern .'/s', $content, $matches );

	$tags 			= $matches[0];
	$names 			= $matches[2];
	
	$args 			= $matches[3];
	$inner_content 	= $matches[5];


	if ( ! $args ) {
		return array(
			'is_shortcode' => false,
			'content' => $content );
	}

	if ( $is_first ) {
		
		// check if 
		$total_length = 0;
		foreach ( $tags as $tag ) {
			$total_length += strlen($tag);
		}

		if ( $total_length != strlen($content) ) {
			return array(
				'is_shortcode' => false,
				'content' => $content );
		}
	}

	$shortcodes = array();

	foreach ( $args as $key => $value ) {

		$shortcode 	= array();		
		$options 	= shortcode_parse_atts( $value );

		// skip shortcode if no shortcode params
		if ( ! is_array( $options ) ) {
			continue;
		}

		$verfOptions = $options; // keep a copy to verify the signature later

		$options = isset( $options['ct_options'] ) ? json_decode( $options['ct_options'], true ) : null;

		$id 	= $options['ct_id'] ?? null;
		$depth 	= isset($options['ct_depth']) ? $options['ct_depth'] : false;

		$shortcode['id'] 		= $id;
		$shortcode['name'] 		= $names[$key];

		if(is_array($options)) {
			foreach($options as $optionKey => $option) {
				if(in_array($optionKey,  array('selector', 'activeselector', 'ct_id', 'ct_parent'))) {
					continue;
				}

				if($optionKey === 'media') {
					foreach($options['media'] as $bpKey => $breakpoint) {
						foreach($breakpoint as $stateKey => $state) {
							foreach(array('src', 'url', 'map_address', 'alt', 'background-image', 'attachment_id') as $param) {
								if(isset($options['media'][$bpKey][$stateKey][$param])) {
									$count = 0; // safety switch
									while(strpos($options['media'][$bpKey][$stateKey][$param], '+oxygen') !== false && $count < 9) {
										$count++;
										$options['media'][$bpKey][$stateKey][$param] = preg_replace_callback('/\+oxygen(.+?)\+/i', 'ct_deobfuscate_oxy_url', $options['media'][$bpKey][$stateKey][$param]);
									}
								}
							}
						}
					}
					continue;
				}

				foreach(array('src', 'url', 'map_address', 'alt', 'background-image', 'attachment_id') as $param) {
					if(isset($options[$optionKey][$param])) {
						$count = 0; // safety switch
						while(strpos($options[$optionKey][$param], '+oxygen') !== false && $count < 9) {
							$count++;
							$options[$optionKey][$param] = preg_replace_callback('/\+oxygen(.+?)\+/i', 'ct_deobfuscate_oxy_url', $options[$optionKey][$param]);
						}
					}
				}

				if($optionKey === 'original' && isset($options[$optionKey]['wp_query_advanced'])) {
					// this is a nested data structure, so need to recursively go through and deobfuscate any dynamic shortcodes
					ct_rec_deobfuscate_param($options[$optionKey]['wp_query_advanced'], 'value');
				}

				if($optionKey === 'original' && isset($options[$optionKey]['globalconditions'])) {
					foreach($options[$optionKey]['globalconditions'] as $conditionKey => $condition) {
						if(isset($condition['oxycode'])) {
							$options[$optionKey]['globalconditions'][$conditionKey]['oxycode'] = preg_replace_callback('/\+oxygen(.+?)\+/i', 'ct_deobfuscate_oxy_url', $options[$optionKey]['globalconditions'][$conditionKey]['oxycode']);
						}

						if(isset($condition['value']) && strpos($condition['value'], '+oxygen') !== false) {
							$options[$optionKey]['globalconditions'][$conditionKey]['value'] = preg_replace_callback('/\+oxygen(.+?)\+/i', 'ct_deobfuscate_oxy_url', $options[$optionKey]['globalconditions'][$conditionKey]['value']);
						}
					}
				}

				// convert "+oxygen" back to "[oxygen" im Custom Attributes 
				if($optionKey === 'original' && isset($options[$optionKey]['custom-attributes'])) {
					foreach($options[$optionKey]['custom-attributes'] as $attr_key => $attribute) {
						if(isset($attribute['name']) && strpos($attribute['name'], '+oxygen') !== false) {
							$options[$optionKey]['custom-attributes'][$attr_key]['name'] = preg_replace_callback('/\+oxygen(.+?)\+/i', 'ct_deobfuscate_oxy_url', $options[$optionKey]['custom-attributes'][$attr_key]['name']);
						}

						if(isset($attribute['value']) && strpos($attribute['value'], '+oxygen') !== false) {
							$options[$optionKey]['custom-attributes'][$attr_key]['value'] = preg_replace_callback('/\+oxygen(.+?)\+/i', 'ct_deobfuscate_oxy_url', $options[$optionKey]['custom-attributes'][$attr_key]['value']);
						}
					}
				}

				
			}
		}

		global $oxygen_signature;
		
		$verfOptions['ct_options'] 	= json_encode( $options, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT );
		
		$verfInnercontent = $inner_content[ $key ];

		// the inner content being used in the oxygen signature verification should have deobfuscated oxy dynamic shortcode strings
		if(strpos($verfInnercontent, '+oxygen') !== false) {
			$verfInnercontent = preg_replace_callback('/\+oxygen(.+?)\+/i', 'ct_deobfuscate_oxy_url', $verfInnercontent);
		}
		
		// Skip shortcodes that are not properly signed, it has to be done here only after ct_deobfuscate_oxy_url has been applied on the options
		if ( $verify_signature && ! $oxygen_signature->verify_signature( $names[ $key ], $verfOptions, $verfInnercontent ) ) {
			continue;
		}

		// advanced query operator/compare symbols are to be de escaped after signature validation
		if(is_array($options) && isset($options['original']) && isset($options['original']['wp_query_advanced'])) {
			ct_rec_aq_symbols($options['original']['wp_query_advanced'], true);
		}

		$sanitized_options = array();
		
		// sanitize option names
		if ( $options ) {
			foreach ( $options as $name => $value ) {

				$array = $value;

				if ( is_array( $array ) && ! empty( $array ) && $name != "classes" ) {

					foreach ( $array as $array_key => $array_value) {

						// make sure widget parameters won't brake the shortcode with quotes
						if ( $names[$key] == "ct_widget" && $array_key == "instance" ) {
							if(isset($array['paramsBase64'])) {

								$array[$array_key] = ct_decode_widget_instance($array_value);
							}
						}

						// TODO: add a filter here to add new options from Class
						$options_to_decode = ["code-php","code-css","code-js","alt",
											  "testimonial_text","testimonial_author","testimonial_author_info",
											  "icon_box_heading","icon_box_text",
											  "progress_bar_left_text","progress_bar_right_text",
											  'pricing_box_package_title','pricing_box_package_subtitle','pricing_box_content','pricing_box_package_regular'];

						if ( in_array($array_key, $options_to_decode) ) {
							$array[$array_key] = base64_decode( $array_value );
						}

						$options_to_decode_backward_compat = [
							"url",
							"paginate_prev_link_text","paginate_next_link_text"
						];

						$options_to_decode_backward_compat = apply_filters("oxy_base64_encode_options", $options_to_decode_backward_compat);

						if ( in_array($array_key, $options_to_decode_backward_compat) && strpos($array_value, "oxy_base64_encoded::") === 0) {
							$array[$array_key] = oxygen_vsb_base64_decode( $array_value );							
						}

						if ( $array_key == "url" && array_key_exists( "url_encoded", $array ) && $array["url_encoded"] == "true") {
							$array[$array_key] = oxygen_vsb_quotes_decode( $array_value );							
						}

						if ( $array_key == "custom-css" ) {
							$array[$array_key] = prettify_custom_css( $array[$array_key] );
						}

						if($array_key == 'globalconditions') {
							foreach($array[$array_key] as $conditionKey => $condition) {
								$array[$array_key][$conditionKey]['value'] = base64_decode($condition['value']);
								if (!isset($array[$array_key][$conditionKey]['searchValue'])) {
									// upgrade to 3.3
									$array[$array_key][$conditionKey]['searchValue'] = base64_decode($condition['value']);
								}
								else {
									$array[$array_key][$conditionKey]['searchValue'] = base64_decode($condition['searchValue']);
								}
							}
						}
					}

					$value = $array;
				}
				
				$sanitized_options[$name] = $value;
			}
		}
		
		
		$shortcode['options'] 	= $sanitized_options;

		// handle 'Shortcode' component
		if ( isset($shortcode['options']) && isset($shortcode['options']['ct_shortcode']) && $shortcode['options']['ct_shortcode'] == "true" ) {
			
			if ( isset( $shortcode['options']['original']['full_shortcode'] ) ) {
				$shortcode['options']['original']['full_shortcode'] = $inner_content[$key];
				
			}
			unset($inner_content[$key]);
		}

		// add depth 1 
		if ( $shortcode['name'] == "ct_column" || 
			 $shortcode['name'] == "ct_columns" || 
			 $shortcode['name'] == "ct_new_columns" || 
			 $shortcode['name'] == "ct_container" || 
			 $shortcode['name'] == "ct_section" ||
			 $shortcode['name'] == "ct_div_block" ||
			 $shortcode['name'] == "oxy_dynamic_list" ||
			 $shortcode['name'] == "ct_nestable_shortcode" || 
			 $shortcode['name'] == "ct_inner_content" ||
			 $shortcode['name'] == "ct_link" ||
			 $shortcode['name'] == 'oxy_superbox' || 
			 $shortcode['name'] == 'oxy_toggle' || 
			 $shortcode['name'] == 'oxy_tab' || 
			 $shortcode['name'] == 'oxy_tabs' || 
			 $shortcode['name'] == 'oxy_tab_content' || 
			 $shortcode['name'] == 'oxy_tabs_contents' ||
			 $shortcode['name'] == 'oxy-product-builder' ||
			 $shortcode['name'] == 'ct_modal' ||
			 $shortcode['name'] == 'oxy_header' ||
			 $shortcode['name'] == 'oxy_header_row' ||
			 $shortcode['name'] == 'oxy_header_left' ||
			 $shortcode['name'] == 'oxy_header_center' ||
			 $shortcode['name'] == 'oxy_header_right'
			) {

			$depth = 1;
		}

		// strip from depth postfix
		if ( strpos( $shortcode['name'], "ct_section_" ) !== false ) {
			
			$depth = substr( $shortcode['name'], 11 );
			$shortcode['name'] 	= "ct_section";
		}

		if ( strpos( $shortcode['name'], "ct_columns_" ) !== false ) {

			$depth = substr( $shortcode['name'], 11 );
			$shortcode['name'] 	= "ct_columns";
		}

		if ( strpos( $shortcode['name'], "ct_new_columns_" ) !== false ) {

			$depth = substr( $shortcode['name'], 15 );
			$shortcode['name'] 	= "ct_new_columns";
		}

		if ( strpos( $shortcode['name'], "ct_column_" ) !== false ) {

			$depth = substr( $shortcode['name'], 10 );
			$shortcode['name'] 	= "ct_column";
		}

		if ( strpos( $shortcode['name'], "ct_div_block_" ) !== false ) {

			$depth = substr( $shortcode['name'], 13 );
			$shortcode['name'] 	= "ct_div_block";
		}

		if ( strpos( $shortcode['name'], "oxy_dynamic_list_" ) !== false ) {

			$depth = substr( $shortcode['name'], 17 );
			$shortcode['name'] 	= "oxy_dynamic_list";
		}

		if ( strpos( $shortcode['name'], "ct_nestable_shortcode_" ) !== false ) {

			$depth = substr( $shortcode['name'], 22 );
			$shortcode['name'] 	= "ct_nestable_shortcode";
		}

		if ( strpos( $shortcode['name'], "ct_inner_content_" ) !== false ) {

			$depth = substr( $shortcode['name'], 17 );
			$shortcode['name'] 	= "ct_inner_content";
		}

		if ( strpos( $shortcode['name'], "ct_link_" ) !== false && (strpos( $shortcode['name'], "ct_link_text" ) === false && strpos( $shortcode['name'], "ct_link_button" ) === false)) {

			$depth = substr( $shortcode['name'], 8 );
			$shortcode['name'] 	= "ct_link";
		}

		if ( strpos( $shortcode['name'], "ct_slider_" ) !== false ) {

			$depth = substr( $shortcode['name'], 10 );
			$shortcode['name'] 	= "ct_slider";
		}

		if ( strpos( $shortcode['name'], "ct_slide_" ) !== false ) {

			$depth = substr( $shortcode['name'], 9 );
			$shortcode['name'] 	= "ct_slide";
		}

		if ( strpos( $shortcode['name'], "oxy_superbox_" ) !== false ) {

			$depth = substr( $shortcode['name'], 13 );
			$shortcode['name'] 	= "oxy_superbox";
		}

		if ( strpos( $shortcode['name'], "oxy_toggle_" ) !== false ) {

			$depth = substr( $shortcode['name'], 11 );
			$shortcode['name'] 	= "oxy_toggle";
		}

		if ( strpos( $shortcode['name'], "oxy_tab_" ) !== false && strpos( $shortcode['name'], "oxy_tab_content" ) === false ) {

			$depth = substr( $shortcode['name'], 8 );
			$shortcode['name'] 	= "oxy_tab";
		}

		if ( strpos( $shortcode['name'], "oxy_tabs_" ) !== false && strpos( $shortcode['name'], "oxy_tabs_contents" ) === false ) {

			$depth = substr( $shortcode['name'], 9 );
			$shortcode['name'] 	= "oxy_tabs";
		}

		if ( strpos( $shortcode['name'], "oxy_tab_content_" ) !== false ) {

			$depth = substr( $shortcode['name'], 16 );
			$shortcode['name'] 	= "oxy_tab_content";
		}

		if ( strpos( $shortcode['name'], "oxy_tabs_contents_" ) !== false ) {

			$depth = substr( $shortcode['name'], 18 );
			$shortcode['name'] 	= "oxy_tabs_contents";
		}

		if ( strpos( $shortcode['name'], "oxy-product-builder_" ) !== false ) {

			$depth = substr( $shortcode['name'], 20 );
			$shortcode['name'] 	= "oxy-product-builder";
		}

		if ( strpos( $shortcode['name'], "ct_modal_" ) !== false ) {

			$depth = substr( $shortcode['name'], 9 );
			$shortcode['name'] 	= "ct_modal";
		}

		if ( strpos( $shortcode['name'], "oxy_header_row_" ) !== false ) {

			$depth = substr( $shortcode['name'], 15 );
			$shortcode['name'] 	= "oxy_header_row";
		}

		else if ( strpos( $shortcode['name'], "oxy_header_right_" ) !== false ) {

			$depth = substr( $shortcode['name'], 17 );
			$shortcode['name'] 	= "oxy_header_right";
		}

		else if ( strpos( $shortcode['name'], "oxy_header_center_" ) !== false ) {

			$depth = substr( $shortcode['name'], 18 );
			$shortcode['name'] 	= "oxy_header_center";
		}

		else if ( strpos( $shortcode['name'], "oxy_header_left_" ) !== false ) {

			$depth = substr( $shortcode['name'], 16 );
			$shortcode['name'] 	= "oxy_header_left";
		}

		else if ( strpos( $shortcode['name'], "oxy_header_" ) !== false && 
				!in_array($shortcode['name'], array("oxy_header_row","oxy_header_right","oxy_header_center","oxy_header_left")) ) {

			$depth = substr( $shortcode['name'], 11 );
			$shortcode['name'] 	= "oxy_header";
		}

		// parse inner content shortcodes
		if ( isset($inner_content[$key]) ) {
			
			if(strpos($inner_content[$key], '[oxygen ') === 0) {
				$nested_content['content'] = $inner_content[$key];
			}
			else {
				$nested_content = parse_shortcodes( $inner_content[$key], false, $verify_signature );
			}
				
			
			if ( isset($nested_content['is_shortcode']) && $nested_content['is_shortcode']) {
				$shortcode['children'] = $nested_content['content'];
				
				// get shortcodes inside content
				if ( $shortcode['name'] == "ct_paragraph" || 
					 $shortcode['name'] == "ct_text_block" || 
					 $shortcode['name'] == "ct_headline" ||
					 $shortcode['name'] == "ct_link_text" ||
					 $shortcode['name'] == "ct_link_button" ||
					 $shortcode['name'] == "ct_li") {

					preg_match_all( '/'. $pattern .'/s', $inner_content[$key], $inner_matches );

					$inner_shortcodes 			= $inner_matches[0];
					$inner_shortcodes_atts		= $inner_matches[3];
					$inner_shortcodes_parsed 	= $inner_content[$key];

					foreach ( $inner_shortcodes as $key => $inner_shortcode ) {
						
						// parse "ct_options" parameter
						$atts = shortcode_parse_atts( $inner_shortcodes_atts[$key] );
						$atts = json_decode( $atts['ct_options'], true );

						$inner_shortcodes_parsed 	= str_replace( $inner_shortcode, "<span id=\"ct-placeholder-{$atts['ct_id']}\"></span>", $inner_shortcodes_parsed );

					}

					if ($inner_shortcodes) {
						$inner_shortcodes_parsed = oxygen_vsb_filter_shortcode_content_decode($inner_shortcodes_parsed);
						$shortcode['options']['ct_content'] = $inner_shortcodes_parsed;	
					}
				}

			} else {
				
				$nested_content['content'] = trim($nested_content['content']);
				
				if(!($shortcode['name'] == 'ct_inner_content' && empty($nested_content['content']))) {
					$nested_content['content'] = oxygen_vsb_filter_shortcode_content_decode($nested_content['content']);
					$shortcode['options']['ct_content'] = $nested_content['content'];
				}
			}
		}

		if ( isset ( $depth ) )
			$shortcode['depth'] = $depth;

		$shortcodes[] = $shortcode;
	}

	return array(
			'is_shortcode' 	=> true,
			'content' 		=> $shortcodes );
}


/**
 * Update custom css variable so it can be 
 * used in shortcode attribute
 *
 * @since 0.1.4
 */

function normalize_custom_css( $css ) {

	if ( $css ) {
		$css = str_replace("\n",'', $css);
		$css = str_replace("\r",'', $css);
		$css = str_replace("\t",'', $css);
	}

	return $css;
}


/**
 * Prettify custom CSS code
 *
 * @since 0.1.8
 */

function prettify_custom_css( $css ) {

	if ( $css ) {
		$css = str_replace(";",";\n", $css);
	}

	return $css;
}


/**
 * Helper function to decode Unicode to UTF-8 characters
 *
 * @since 0.1.7
 */
function ct_unicode_decode($str) {
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'ct_replace_unicode_escape_sequence', $str);
}

function ct_replace_unicode_escape_sequence($match) {
    return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
}

/**
 * Encode/decode widget params to make sure these won't brake shortocdes with quotes or similar
 *
 * @author Ilya K.
 * @since 2.0
 */

function ct_encode_widget_instance($array_value) {
	return array_map(function($value) {
		if ( is_array($value) ) {
			return ct_encode_widget_instance($value);
		}
		elseif ( is_bool($value) ) {
			return $value;
		}
		else {
			return base64_encode($value);
		}
	}, $array_value );
}

function ct_decode_widget_instance($array_value) {
	return array_map(function($value) {
	 	if ( is_array($value) ) {
			return ct_decode_widget_instance($value);
	 	}
	 	elseif ( is_bool($value) ) {
	 		return $value;
	 	}
	 	else {
			return oxygen_base64_decode_for_json($value);
		}
	}, $array_value );
}


/**
 * Encode [] brackets to special _OXY_..._BRACKET_ string 
 * so the shortcodes doesn't render inside text components
 * 
 * @author Ilya K.
 * @since 2.1
 */

function oxygen_vsb_filter_shortcode_content_encode($content) {

	// exclude [oxygen data=""] shortcodes
	preg_match_all('/\[oxygen[^\]]*\]/m', $content, $matches);
	if (is_array($matches)) {
		foreach ($matches as $key => $match) {
			if (isset($match[0])){
				$content = str_replace($match[0], "_OXYGEN_DATA_".$key."_", $content);
			}
		}
	}
	
	$content = str_replace("[", "_OXY_OPENING_BRACKET_", $content);
	$content = str_replace("]", "_OXY_CLOSING_BRACKET_", $content);

	// include [oxygen data=""] shortcodes back
	if (is_array($matches)) {
		foreach ($matches as $key => $match) {
			if (isset($match[0])){
				$content = str_replace("_OXYGEN_DATA_".$key."_", $match[0], $content);
			}
		}
	}

	return $content;
}


/**
 * Decode special _OXY_..._BRACKET_ srtings back to []
 * so the shortcodes doesn't render inside text components
 * 
 * @author Ilya K.
 * @since 2.1
 */

function oxygen_vsb_filter_shortcode_content_decode($content) {

	$content = str_replace("_OXY_OPENING_BRACKET_", "[", $content);
	$content = str_replace("_OXY_CLOSING_BRACKET_", "]", $content);

	return $content;
}


/**
 * Helpers for safe base64 encoding/decoding
 * 
 * @author Ilya K.
 * @since 3.3
 */

function oxygen_vsb_base64_encode($content) {

	// sign shortcodes before base64_encode
	$content = preg_replace_callback('/\[oxygen ([^\]]*)\]/i', 'ct_sign_oxy_dynamic_shortcode', $content);
	$content = "oxy_base64_encoded::".base64_encode($content);

	return $content;
}

function oxygen_vsb_base64_decode($content) {

	if (is_array($content)) return $content;

	if (strpos($content, "oxy_base64_encoded::") === 0) {
		$base64 = str_replace("oxy_base64_encoded::", "", $content);
		$content = base64_decode( $base64 );
	}

	return $content;
}

/**
 * Decode base64 encoded string only if we are outside do_oxygen_elements() function
 * and not using JSON encoded design
 */

function oxygen_base64_decode_for_json($content) {

	if (oxygen_doing_oxygen_elements()) {
		return $content;
	}
	else {
		return base64_decode($content);
	}
}


/**
 * Helpers for htmlspecialchars alternative for decoding/encoding quotes
 * 
 * @author Ilya K.
 * @since 3.3.2
 */

function oxygen_vsb_quotes_encode($content) {

	if ( stripos($content, '[oxygen' ) !== false ) {
		return $content;
	}

	$content = str_replace("'", "&#039;", $content);
	$content = str_replace("\"", "&quot;", $content);

	return $content;
}

function oxygen_vsb_quotes_decode($content) {

	if ( stripos($content, '[oxygen' ) !== false ) {
		return $content;
	}

	$content = str_replace("&#039;", "'", $content);
	$content = str_replace("&quot;", "\"", $content);

	return $content;
}


function oxygen_safe_convert_old_shortcodes_to_json($content) {

	$tree = json_decode($content, true);
	
	if ($tree===null) {
		$content = content_to_components_json($content);
	}

	return $content;
}