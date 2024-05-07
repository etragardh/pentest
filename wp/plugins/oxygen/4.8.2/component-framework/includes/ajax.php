<?php

class CT_Depth_Parser {
	
	private $references, $depths;

	function __construct() {
		$this->references = array();
	}

	function storeParentReference($item) {
		
		$id = $item['options']['ct_id'];
		$parent = $item['options']['ct_parent'];
		$name = $item['name'];
		$depth = $item['depth'] ?? 0;

		$this->references[$id] = array('parent' => $parent, 'name' => $name, 'depth' => $depth);
	}

	function storeDepths($parent) {

		$this->depths = 1;

		while($parent && $parent > 0) {
			$this->depths++;
			$item = $this->references[$parent];
			$parent = $item['parent']; // for next iteration
		}

	}

	function getDepths() {
		return $this->depths;
	}

}

function ct_prepare_inner_content_for_save($children, $inner_content_id, $depths) {
	
	foreach($children as $key => $value) {

		// replace the parent_id of the elements having that equal to the ID of the inner_content module with 0 (assign to root)
		if($children[$key]['options']['ct_parent'] == $inner_content_id) {
			$children[$key]['options']['ct_parent'] = 0;
		}

		if(isset($children[$key]['depth']) && $children[$key]['depth'] > 0) {
			$children[$key]['depth'] -= $depths;

			if($children[$key]['depth'] < 0) {
				$children[$key]['depth'] = 0;
			}
		}
		
		if(isset($children[$key]['children'])) {
			$children[$key]['children'] = ct_prepare_inner_content_for_save($children[$key]['children'], $inner_content_id, $depths);
		}
	}

	return $children;
}

function ct_find_inner_contents($children, $ctDepthParser) {

	$inner_content = false;
	
	foreach($children as $key => $value) {
		
		if($inner_content !== false) {
			continue;
		}

		$name = $children[$key]['name'];
		
		$ctDepthParser->storeParentReference($children[$key]);
		
		if($name == 'ct_inner_content' && isset($children[$key]['children'])) {

			$ctDepthParser->storeDepths($children[$key]['options']['ct_parent']);

			$inner_content =  $children[$key];

			return array(
				'content' => $inner_content,
				'depths' => $ctDepthParser->getDepths()
			);
		}
		else {

			if(isset($children[$key]['children'])) {
				$inner_content = ct_find_inner_contents($children[$key]['children'], $ctDepthParser);
			}
		}
	}

	return $inner_content;
}

/**
 * Receive Components Tree and other options as JSON object
 * and save as post conent and meta
 * 
 * @since 0.1
 */

function ct_save_components_tree_as_post() {

	if ( !isset( $_GET['action'] ) || $_GET['action'] != "ct_save_components_tree") {
		return;
	}
	
	$nonce  	= $_REQUEST['nonce'];
	$post_id 	= intval( $_REQUEST['post_id'] );
	$blankContents = false;

	// check nonce
	if ( ! isset( $nonce, $post_id ) || ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	    // This nonce is not valid.
	    die( 'Security check' );
	}

	// check if user can edit this post
	if ( ! oxygen_vsb_current_user_can_access() ) {
		die ( 'Security check' );
	}

	// get all data JSON
	$data = file_get_contents('php://input');

	// encode and separate tree from options
	$data = json_decode($data, true);

	if (!isset($data['params'])||!isset($data['tree'])){
		// no data arrived, don't save the page and exit
	  	header('Content-Type: application/json');
	  	echo json_encode( array("post_saved" => 0,"error"=>"No data arrived. Redirect?") );
		die();
	}

	$params = $data['params'];
	$tree 	= $data['tree'];

	// settings
	$page_settings	 	= ct_filter_content( 'page_settings', $params['page_settings'] );
	$global_settings 	= ct_filter_content( 'global_settings', $params['global_settings'] );
	$ct_global_settings = get_option('ct_global_settings', array());
	
	// classes and selectors
	$classes		 	= ct_filter_content( 'classes', $params['classes'] );
	$style_sets 		= ct_filter_content( 'style_sets', $params['style_sets'] );
	$style_folders		= isset($params['style_folders']) ? ct_filter_content( 'style_folders', $params['style_folders'] ) : array();
	$custom_selectors 	= isset($params['custom_selectors']) ? ct_filter_content( 'custom_selectors', $params['custom_selectors'] ) : array();

	// Don't save stylesheets if they've been 'locked' via Oxygen > Stylesheets
	if( get_option("ct_lock_stylesheets_in_builder") == false ) {
		$style_sheets = ct_filter_content( 'style_sheets', $params['style_sheets'] );
	}
	
	$easy_posts_templates 		= ct_filter_content( 'easy_posts_templates', $params['easy_posts_templates'] ?? "" );
	$comments_list_templates 	= ct_filter_content( 'comments_list_templates', $params['comments_list_templates'] ?? "" );

	$typekit_fonts 		= ct_filter_content( 'typekit_fonts', $params['typekit_fonts'] );
	$global_colors 		= ct_filter_content( 'global_colors', $params['global_colors'] );

	// element presets
	$element_presets 	= ct_filter_content( 'element_presets', $params['element_presets'] );

	// is it a ct_template?
	$post = get_post($post_id);
	$preview_url = '';
	if($post->post_type === 'ct_template') {
		$preview_url = isset($params['preview'])?esc_url($params['preview']):'';
	}

	// if it is page's inner content, then discard all the template related shortcodes here
	$ct_inner = isset($_REQUEST['ct_inner'])? true:false;

	if($ct_inner) {
		// find the inner contents inside the $tree['children'] and separate it from the main tree, assign it back to $tree['children']
		
		$ctDepthParser = new CT_Depth_Parser();

		$innerResults = ct_find_inner_contents($tree['children'], $ctDepthParser);

		if(!empty($innerResults)) {
			$tree['children'] = ct_prepare_inner_content_for_save($innerResults['content']['children'], $innerResults['content']['id'], $innerResults['depths']);
		}
		else {
			$blankContents = true;
		}
	}
	
    $shortcodes = '';
    $components_tree_json = '';
    
	if(!$blankContents) {	
	
		// code tree back to JSON to pass into old function
		$components_tree_json = json_encode($tree, JSON_UNESCAPED_UNICODE);

		// base64 encode js,css code and (wrapping_shortcode in case of nestable_shortcode component) in the IDs
		if(isset($tree['children'])) {
			$tree['children'] = ct_base64_encode_decode_tree($tree['children']);
		}
		$components_tree_json_for_shortcodes = json_encode($tree);
	
		// transform JSON to shortcodes
		$shortcodes = components_json_to_shortcodes( $components_tree_json_for_shortcodes );
	}

	Oxygen_Revisions::create_revision( $post_id );
	

	// Save as post Meta (NEW WAY)
	update_post_meta( $post_id, 'ct_builder_shortcodes', $shortcodes );
	
	$components_tree_json = preg_replace_callback('/\[oxygen ([^\]]*)\]/i', 'ct_sign_oxy_dynamic_shortcode', $components_tree_json);
	update_post_meta( $post_id, 'ct_builder_json', addslashes($components_tree_json) );

	if( !empty($preview_url) ) {
		update_post_meta( $post_id, 'ct_preview_url', $preview_url );
	}

	do_action( 'save_post', $post_id, get_post( $post_id ), true );
  	
  	// Process settings
  	$page_settings_saved 	= update_post_meta( $post_id, "ct_page_settings", $page_settings );
  	$global_settings_saved 	= update_option("ct_global_settings", $global_settings );
  	$classes_saved 			= update_option("ct_components_classes", $classes, get_option("oxygen_options_autoload"));
	// update global classes variable for correct further CSS generation
  	global $oxygen_vsb_css_classes;
	$oxygen_vsb_css_classes = get_option("ct_components_classes", array());

  	// Process custom CSS selectors
  	$custom_selectors_saved = update_option("ct_custom_selectors", $custom_selectors, get_option("oxygen_options_autoload") );
  	$style_sets_updated 	= update_option("ct_style_sets", $style_sets );
  	$style_folders_updated 	= update_option("ct_style_folders", $style_folders );

	// Don't save stylesheets if locked via Oxygen > Stylesheets.
	if( isset( $style_sheets ) ) {
		$style_sheets_saved 	= update_option("ct_style_sheets", $style_sheets, get_option("oxygen_options_autoload") );
	}

  	$easy_posts_saved 		= update_option("oxygen_vsb_easy_posts_templates", $easy_posts_templates );
  	$comments_list_saved 	= update_option("oxygen_vsb_comments_list_templates", $comments_list_templates );
  	$typekit_fonts 			= update_option("oxygen_vsb_latest_typekit_fonts", $typekit_fonts );
  	$global_colors 			= update_option("oxygen_vsb_global_colors", $params['global_colors'] );
  	$element_presets 		= update_option("oxygen_vsb_element_presets", $params['element_presets'], get_option("oxygen_options_autoload") );
  	$codemirror_theme 		= update_option("oxygen_vsb_codemirror_theme", $params['codemirror_theme'], get_option("oxygen_options_autoload") );
  	$codemirror_wrap 		= update_option("oxygen_vsb_codemirror_wrap", $params['codemirror_wrap'], get_option("oxygen_options_autoload") );
  	
  	// udpate global colors global var to latest to proper CSS generation
  	global $oxygen_vsb_global_colors;
	$oxygen_vsb_global_colors = oxy_get_global_colors();

	// CSS cache needs to be updated when breakpoints changed
	if ($global_settings_saved) {
		$ct_global_settings['breakpoints'] = isset($ct_global_settings['breakpoints']) ? $ct_global_settings['breakpoints'] : "";
		if (isset($global_settings['breakpoints']) && $global_settings['breakpoints'] != $ct_global_settings['breakpoints']) {
			update_option("oxygen_breakpoints_cache_update_required", true);
		}
	}

	// CSS cache needs to be updated when global colors changed
	if ($global_colors) {
		update_option("oxygen_global_colors_cache_update_required", true);
	}

	global $oxy_ajax_post_id;
	$oxy_ajax_post_id = $post_id;
  	
  	oxygen_vsb_cache_universal_css();
	oxygen_vsb_cache_page_css($post_id);

	// save Google Fonts cache
    $google_fonts_cache = false;
	if (isset($params['google_fonts_cache'])) {
		$google_fonts_cache = update_option("oxygen_vsb_google_fonts_cache", $params['google_fonts_cache'], get_option("oxygen_options_autoload") );
	}

  	$return_object = array(
  		"page_settings_saved" 	 => $page_settings_saved, // true or false
  		"global_settings_saved"  => $global_settings_saved, // true or false
  		"classes_saved" 		 => $classes_saved, // true or false
  		"custom_selectors_saved" => $custom_selectors_saved, // true or false
  		"style_sheets_saved" 	 => $style_sheets_saved ?? false, // true or false
  		"global_colors" 	 	 => $global_colors, // true or false
  		"google_fonts_cache" 	 => $google_fonts_cache, // true or false
  		"element_presets" 	 	 => $element_presets, // true or false
  	);

	// echo JSON
  	header('Content-Type: application/json');
  	echo json_encode( $return_object );
	die();
}
// keep old admin-ajax.php way as a fallback for 404 or other possible templates that have no frontend URL
add_action('wp_ajax_ct_save_components_tree', 'ct_save_components_tree_as_post');
// new frontend way
add_filter( 'template_redirect', 'ct_save_components_tree_as_post' );


/**
 * Helper function to base 64 encode/decode custom-css and js recursively through the tree
 * default is encode operation
 * Set second param to be true, for decode operation
 * 
 * @since 0.3.4
 * @author gagan goraya 
 */

function ct_base64_encode_decode_tree($children, $decode = false) {
	
	if (oxygen_doing_oxygen_elements()) {
		return $children;
	}

	if(!is_array($children))
		return array();


	foreach($children as $key => $item) {

		if(isset($item['children']))
			$children[$key]['children'] = ct_base64_encode_decode_tree( $item['children'], $decode );
		
		if(!isset($item['options']))
			continue;

		foreach($item['options'] as $optionkey => $option) {
			// ignore ct_id // ignore ct_parent

			if($optionkey == 'ct_id' || $optionkey == 'ct_parent' || $optionkey == 'selector' || $optionkey == 'ct_content')
				continue;

			// if media then 
			if($optionkey == 'media') {
				foreach($option as $mediakey => $mediaoption) {
					foreach($mediaoption as $mediastatekey => $mediastate) {
						if(isset($mediastate['custom-css'])) {
							if($decode) {
								if(!strpos($mediastate['custom-css'], ' ')) {
									$children[$key]['options'][$optionkey][$mediakey][$mediastatekey]['custom-css'] = base64_decode($mediastate['custom-css']);
								}
							}
							else {
								$children[$key]['options'][$optionkey][$mediakey][$mediastatekey]['custom-css'] = base64_encode($mediastate['custom-css']);
							}
						}
						if(isset($mediastate['custom-js'])) {
							if($decode) {
								if(!strpos($mediastate['custom-js'], ' ')) {
									$children[$key]['options'][$optionkey][$mediakey][$mediastatekey]['custom-js'] = base64_decode($mediastate['custom-js']);
								}
							}
							else {
								$children[$key]['options'][$optionkey][$mediakey][$mediastatekey]['custom-js'] = base64_encode($mediastate['custom-js']);
							}
						}
						if(isset($mediastate['custom-code'])) {
							if($decode) {
								if(!strpos($mediastate['custom-code'], ' ')) {
									$children[$key]['options'][$optionkey][$mediakey][$mediastatekey]['custom-code'] = base64_decode($mediastate['custom-code']);
								}
							}
							else {
								$children[$key]['options'][$optionkey][$mediakey][$mediastatekey]['custom-code'] = base64_encode($mediastate['custom-code']);
							}
						}

						// base64 encode the content of the before and after states
						if(is_pseudo_element($mediastatekey)) {
							if(isset($mediastate['content'])) {
								if($decode) {
									$children[$key]['options'][$optionkey][$mediakey][$mediastatekey]['content'] = base64_decode($mediastate['content']);
								}
								else {
									$children[$key]['options'][$optionkey][$mediakey][$mediastatekey]['content'] = base64_encode($mediastate['content']);
								}
							}
						}
					}
				}
				continue;
			}

			if(isset($option['wrapping_shortcode'])) {
				if($decode) {
					if(!strpos($option['wrapping_shortcode'], ' ')) {
						$children[$key]['options'][$optionkey]['wrapping_shortcode'] = base64_decode($option['wrapping_shortcode']);
					}
				}
				else {
					$children[$key]['options'][$optionkey]['wrapping_shortcode'] = base64_encode($option['wrapping_shortcode']);
				}
			}

			if(isset($option['wrapping_start'])) {
				if($decode) {
					if(!strpos($option['wrapping_start'], ' ')) {
						$children[$key]['options'][$optionkey]['wrapping_start'] = base64_decode($option['wrapping_start']);
					}
				}
				else {
					$children[$key]['options'][$optionkey]['wrapping_start'] = base64_encode($option['wrapping_start']);
				}
			}

			if(isset($option['wrapping_end'])) {
				if($decode) {
					if(!strpos($option['wrapping_end'], ' ')) {
						$children[$key]['options'][$optionkey]['wrapping_end'] = base64_decode($option['wrapping_end']);
					}
				}
				else {
					$children[$key]['options'][$optionkey]['wrapping_end'] = base64_encode($option['wrapping_end']);
				}
			}

			// for all others, do the thing
			if(isset($option['custom-css'])) {
				if($decode) {
					if(!strpos($option['custom-css'], ' ')) {
						$children[$key]['options'][$optionkey]['custom-css'] = base64_decode($option['custom-css']);
					}
				}
				else {
					$children[$key]['options'][$optionkey]['custom-css'] = base64_encode($option['custom-css']);
				}
			}
			if(isset($option['custom-js'])) {
				if($decode) {
					if(!strpos($option['custom-js'], ' ')) {
						$children[$key]['options'][$optionkey]['custom-js'] = base64_decode($option['custom-js']);
					}
				}
				else {
					$children[$key]['options'][$optionkey]['custom-js'] = base64_encode($option['custom-js']);
				}
			}

			if(isset($option['custom-code'])) {
				if($decode) {
					if(!strpos($option['custom-code'], ' ')) {
						$children[$key]['options'][$optionkey]['custom-code'] = base64_decode($option['custom-code']);
					}
				}
				else {
					$children[$key]['options'][$optionkey]['custom-code'] = base64_encode($option['custom-code']);
				}
			}
			
			// base64 encode the content of the before and after states
			if(is_pseudo_element($optionkey)) {
				if(isset($option['content'])) {
					if($decode) {
						//if(substr($option['content'], -2) == '==') {
							$children[$key]['options'][$optionkey]['content'] = base64_decode($option['content']);
						//}
					}
					else {
						$children[$key]['options'][$optionkey]['content'] = base64_encode($option['content']);
					}
				}
			}
		}

	}

	return $children;
}


/**
 * base64 encode classes and custom selectors custom ccs/js
 * 
 * @since 1.3
 * @author Ilya/Gagan
 */

function ct_base64_encode_selectors($selectors) {
	
	foreach($selectors as $key => $class) {

		foreach( $class as $statekey => $state) {
			
			if( $statekey == "media") {
				foreach($state as $bpkey => $bp) {
					foreach($bp as $bpstatekey => $bp) {
						if(isset($class[$statekey][$bpkey][$bpstatekey]['custom-css']))
		  					$selectors[$key][$statekey][$bpkey][$bpstatekey]['custom-css'] = base64_encode($selectors[$key][$statekey][$bpkey][$bpstatekey]['custom-css']);

		  				if(isset($class[$statekey][$bpkey][$bpstatekey]['custom-js']))
		  					$selectors[$key][$statekey][$bpkey][$bpstatekey]['custom-js'] = base64_encode($selectors[$key][$statekey][$bpkey][$bpstatekey]['custom-js']);  						
					}
				}
			}
			else {

		  		if(isset($class[$statekey]['custom-css']))
		  			$selectors[$key][$statekey]['custom-css'] = base64_encode($class[$statekey]['custom-css']);
		  		if(isset($class[$statekey]['custom-js']))
		  			$selectors[$key][$statekey]['custom-js'] = base64_encode($class[$statekey]['custom-js']);
		  	}
	  	}
  	}

  	return $selectors;
}


/**
 * Save single component (or array of same level components)
 * as "reusable_part" view (ct_template CPT)
 * 
 * @since 0.2.3 
 */

function ct_save_component_as_view() {

	$name 		= sanitize_text_field( $_REQUEST['name'] );
	$nonce  	= $_REQUEST['nonce'];
	$post_id 	= intval( $_REQUEST['post_id'] );

	$isBlock = filter_var ($_REQUEST['block'], FILTER_VALIDATE_BOOLEAN);

	// check nonce
	if ( !isset( $nonce, $post_id ) || ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	    // This nonce is not valid.
	    die( '0' ); 
	}

	// check if user can publish posts
	if ( ! oxygen_vsb_current_user_can_access() ) {
		die ( '0' );
	}

	$component 	= file_get_contents('php://input');
	$tree 		= json_decode($component, true);

	// base64 encode js and css code in the IDs
	$tree["children"] = ct_base64_encode_decode_tree($tree['children']);

	$component = json_encode($tree);

	//var_dump($component);

	$shortcodes = components_json_to_shortcodes( $component, true );

	//var_dump($shortcodes);

	$post = array(
		'post_title'	=> $name,
		'post_type' 	=> $isBlock ? "oxy_user_library" : "ct_template",
		'post_status'	=> "publish",
		// TODO: check who is a post author
		//'post_author' 	=> ""
	);
	
	// Insert the post into the database
	$post_id = wp_insert_post( $post );
	
	if ( $post_id !== 0 ) {
		if($isBlock){
            update_post_meta( $post_id, 'ct_other_template', -1);
        }else{
			update_post_meta( $post_id, 'ct_template_type', "reusable_part");
        }
		update_post_meta( $post_id, 'ct_builder_shortcodes', $shortcodes );

		$tree['children'] = ct_update_ids( $tree['children'], 1, $tree );
		$components_tree_json = json_encode($tree, JSON_UNESCAPED_UNICODE);
		$components_tree_json = preg_replace_callback('/\[oxygen ([^\]]*)\]/i', 'ct_sign_oxy_dynamic_shortcode', $components_tree_json);
		update_post_meta( $post_id, 'ct_builder_json', addslashes($components_tree_json) );

		oxygen_vsb_cache_page_css($post_id, $shortcodes);
	}

	// echo JSON
	header('Content-Type: application/json');
	echo $post_id;
	die();
}
add_action('wp_ajax_ct_save_component_as_view', 'ct_save_component_as_view');

function ct_embed_inner_content($children, $inner_content) {
    if (is_array($children)) {
        foreach($children as $key => $val) {
            $name = $children[$key]['name'];
            if($name == 'ct_inner_content') {
                $children[$key]['children'] = $inner_content;
            }
            elseif(isset($children[$key]['children'])) { // go recursive
                $children[$key]['children'] = ct_embed_inner_content($children[$key]['children'], $inner_content);
            }
        }
	}
	return $children;
}

function ct_replace_inner_content($children, $inner_content) {
	$offset = 0;
	$found = false;
    if (is_array($children) && is_array($inner_content)) {
        foreach($children as $key => $val) {
            if($val['name'] == 'ct_inner_content') {
                $found = true;
				if (is_array($inner_content) || is_object($inner_content)) {
					foreach($inner_content as $inKey=>$inVal) {
						
						$inner_content[$inKey]['options']['ct_parent'] = $children[$key]['options']['ct_parent'];
					}
					array_splice($children, $offset, 1, $inner_content);
				}
            }
            elseif(!$found && isset($children[$key]['children'])) { // go recursive
                
                $children[$key]['children'] = ct_replace_inner_content($children[$key]['children'], $inner_content);
            }
            $offset++;
        }
	}
	return $children;
}

function ct_prepare_outer_template($children, $ctDepthParser) {
	global $ct_offsetDepths_source;
	
	$inner_content = false;
	$container_id = false;
	$parent_id = false;

    if (is_array($children)) {
        foreach($children as $key => $val) {

            $name = $children[$key]['name'];

            if($children[$key]['options']['ct_id'] > 0) {
                // obfuscate selector
                $children[$key]['options']['selector'] = str_replace('_'.$children[$key]['id'].'_post_', '_'.($children[$key]['id']+100000).'_post_', $children[$key]['options']['selector']);
                // obfuscate Ids 
                $children[$key]['options']['ct_id'] += 100000; 
            }
            
            if($children[$key]['options']['ct_parent'] > 0) { // obfuscate parent ids
                $children[$key]['options']['ct_parent'] += 100000;
            }

            $ctDepthParser->storeParentReference($children[$key]);

            if($name == 'ct_inner_content') {
                $inner_content = $children[$key];
                $container_id = $children[$key]['options']['ct_id'];;
                $parent_id = $children[$key]['options']['ct_parent'];

                $ctDepthParser->storeDepths($parent_id);
            }

            
            $depth = isset($children[$key]['depth']) ? $children[$key]['depth'] : 0;

            if(isset($ct_offsetDepths_source[$name]) ) {
                if($ct_offsetDepths_source[$name] > $depth) {
                    $ct_offsetDepths_source[$name] = $depth;
                }
            }
            else
                $ct_offsetDepths_source[$name] = $depth;

            if(isset($children[$key]['children'])) { // go recursive
                $prepared_outer_content = ct_prepare_outer_template($children[$key]['children'], $ctDepthParser);
                $children[$key]['children'] = $prepared_outer_content['content'];

                if($prepared_outer_content['inner_content']) {
                    $inner_content = $prepared_outer_content['inner_content'];
                }

                if($prepared_outer_content['container_id']) {
                    $container_id = $prepared_outer_content['container_id'];
                }

                if($prepared_outer_content['parent_id']) {
                    $parent_id = $prepared_outer_content['parent_id'];
                }
            }

            $children[$key]['id'] = $children[$key]['options']['ct_id'];
        }
	}

	return array('content' => $children, 'inner_content' => $inner_content, 'container_id' => $container_id, 'parent_id' => $parent_id);
}

function ct_prepare_outer_template_json($children, $ctDepthParser) {
	global $ct_offsetDepths_source;
	
	$inner_content 	= false;
	$container_id 	= false;
	$parent_id 		= false;

    if (is_array($children)) {
        foreach($children as $key => $val) {

            $name = $children[$key]['name'];

            if($children[$key]['options']['ct_id'] > 0) {
                // obfuscate selector
                $children[$key]['options']['selector'] = str_replace('_'.$children[$key]['id'].'_post_', '_'.($children[$key]['id']+100000).'_post_', $children[$key]['options']['selector']);
                // obfuscate Ids 
                $children[$key]['options']['ct_id'] += 100000; 
            }
            
            if($children[$key]['options']['ct_parent'] > 0) { // obfuscate parent ids
                $children[$key]['options']['ct_parent'] += 100000;
            }

            $ctDepthParser->storeParentReference($children[$key]);

            if($name == 'ct_inner_content') {
                $inner_content = $children[$key];
                $container_id = $children[$key]['options']['ct_id'];
                $parent_id = $children[$key]['options']['ct_parent'];

                $ctDepthParser->storeDepths($parent_id);
            }

            
            $depth = isset($children[$key]['depth']) ? $children[$key]['depth'] : 0;

            if(isset($ct_offsetDepths_source[$name]) ) {
                if($ct_offsetDepths_source[$name] > $depth) {
                    $ct_offsetDepths_source[$name] = $depth;
                }
            }
            else
                $ct_offsetDepths_source[$name] = $depth;

            if(isset($children[$key]['children'])) { // go recursive
                $prepared_outer_content = ct_prepare_outer_template_json($children[$key]['children'], $ctDepthParser);
                $children[$key]['children'] = $prepared_outer_content['content'];

                if($prepared_outer_content['inner_content']) {
                    $inner_content = $prepared_outer_content['inner_content'];
                }

                if($prepared_outer_content['container_id']) {
                    $container_id = $prepared_outer_content['container_id'];
                }

                if($prepared_outer_content['parent_id']) {
                    $parent_id = $prepared_outer_content['parent_id'];
                }
            }

            $children[$key]['id'] = $children[$key]['options']['ct_id'];
        }
	}

	return array('content' => $children, 'inner_content' => $inner_content, 'container_id' => $container_id, 'parent_id' => $parent_id);
}

function ct_prepare_inner_content($children, $container_id, $depth) {

	global $ct_offsetDepths_source;

    if (is_array($children)) {
        foreach($children as $key => $val) {

            if(intval($children[$key]['options']['ct_parent']) === 0) {
                $children[$key]['options']['ct_parent'] = $container_id;
            }
            
            
            if(isset($children[$key]['depth'])) {
                $children[$key]['depth'] += $depth;
            }

            if(isset($children[$key]['children'])) {
                $children[$key]['children'] = ct_prepare_inner_content($children[$key]['children'], $container_id, $depth);
            }
        }
	}

	return $children;

}


/**
 * Return post Components Tree as a JSON object 
 * in response to AJAX call
 * 
 * @since 0.1.7
 * @author Ilya K.
 */

function ct_get_components_tree() {

	// possible fix
	//error_reporting(E_ALL & ~(E_STRICT|E_NOTICE|E_WARNINGS|E_DEPRECATED));

	$nonce  	= $_REQUEST['nonce'];
	$post_id 	= intval( $_REQUEST['post_id'] );
	$id 		= intval( $_REQUEST['id'] );
	$template_id = false;
	// check nonce
	if ( !isset( $nonce, $post_id, $id ) || ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	    // This nonce is not valid.
	    die( 'Security check' ); 
	}

	// check user role
	if ( ! oxygen_vsb_current_user_can_access() ) {
		die ( 'Security check' );
	}
	
	// if the intended target to be edited is the inner content
	$shortcodes = false;
	$ct_inner = isset($_REQUEST['ct_inner'])?true:false;
	
	$post_type = get_post_type($id);
	if($post_type === 'ct_template' && $ct_inner) {
		$singular_shortcodes = oxygen_get_combined_shortcodes($id, true);
		$template_id = get_post_meta( $id, "ct_parent_template", true);
	}
	else {
		$singular_shortcodes = get_post_meta($id, "ct_builder_shortcodes", true);

		// check for the validity of the $singular_shortcodes here only
		$singular_shortcodes = parse_shortcodes($singular_shortcodes, false);
	}
	
	if ($post_type !== 'ct_template' && $ct_inner ) {
		
		$ct_other_template = get_post_meta( $id, "ct_other_template", true );

		$template = false;
		if(!empty($ct_other_template) && $ct_other_template > 0) { // no template is specified
			// try getting default template
			$template = get_post($ct_other_template);
		}
		elseif($ct_other_template != -1) { // try getting default template if not explicitly set to not use any template at all


			if(intval($id) == intval(get_option('page_on_front')) || intval($id) == intval(get_option('page_for_posts'))) {
				$template = ct_get_archives_template( $id );

				if(!$template) {  // if not template is set to apply to front page or blog posts page, then use the generic page template, as these are pages
					$template = ct_get_posts_template( $id );
				}
			}
			else {
				$template = ct_get_posts_template( $id );

				if(!$template) {
					$template = ct_get_archives_template( $id );
				}
			}
		}

		if($template) { 
			// get template shortcodes
			$shortcodes = oxygen_get_combined_shortcodes($template->ID);

			$template_id = $template->ID;
		} else { // does not even have a default template
			// then use it as a standalone custom view
			$shortcodes = get_post_meta( $id, "ct_builder_shortcodes", true );
			$shortcodes = parse_shortcodes( $shortcodes );
		}

		if($shortcodes) {

			// verify the validity of the $shortcodes here, i.e., check for the signs
			//$shortcodes = parse_shortcodes( $shortcodes ); // returns valid and parsed shortcodes
			
			//if(empty($singular_shortcodes)) {
				/*$content_post = get_post($id);
				$content = $content_post->post_content;
				$content = apply_filters('the_content', $content);
				$content = trim(str_replace(']]>', ']]&gt;', $content));*/

				//if(!empty($content))
			//	$singular_shortcodes = '[ct_code_block ct_options=\'{"ct_id":2,"ct_parent":0,"selector":"ct_code_block_2_post_7","original":{"code-php":"PD9waHAKCXdoaWxlKGhhdmVfcG9zdHMoKSkgewogICAgCXRoZV9wb3N0KCk7CgkJdGhlX2NvbnRlbnQoKTsKICAgIH0KPz4="},"activeselector":false}\'][/ct_code_block]';
			//}

			//recursively obfuscate_ids: ct_id and ct_parent of all elements in $parsed, also obfuscate_selectors
			$ctDepthParser = new CT_Depth_Parser();

			$prepared_outer_content = ct_prepare_outer_template($shortcodes['content'], $ctDepthParser);
			
			$shortcodes['content'] = $prepared_outer_content['content'];

			$container_id = $prepared_outer_content['container_id'];
			$parent_id = $prepared_outer_content['parent_id'];
			if(!empty($singular_shortcodes['content'])) {
				$singular_shortcodes['content'] = ct_prepare_inner_content($singular_shortcodes['content'], $container_id, $ctDepthParser->getDepths());

				$shortcodes['content'] = ct_embed_inner_content($shortcodes['content'], $singular_shortcodes['content']);
			}
		}

	}


	if(!$shortcodes) {
		$shortcodes = $singular_shortcodes;
	}

	$json = '{}';
	
	if($shortcodes['content']) {
		$root = array ( 
			"id"	=> 0,
			"name" 	=> "root",
			"depth"	=> 0 
		);
		
		$root['children'] = $shortcodes['content'];

		$components_tree = json_encode( $root );

		$json = $components_tree;
	}


	// base 64 decode all the custom-css and custom-js down the tree
	$tree = json_decode($json, true);

	$not_registered_shortcodes = oxygen_has_not_registered_shortcodes(get_post_meta( $post_id, "ct_builder_shortcodes", true ));
	if ($not_registered_shortcodes) {
		$tree['notRegisteredShortcodes'] = $not_registered_shortcodes;
	}
	
	if(isset($tree['children'])) {
		$tree['children'] = ct_base64_encode_decode_tree($tree['children'], true);
	}

	if(!isset($tree['name']) || $tree['name'] != 'root') {
        // data is corrupt, the name property should have been equal to root otherwise.
        // provide a clean slate
        $tree['id'] = 0;
        $tree['name'] = 'root';
        $tree['depth'] = 0;
    }

    $tree['meta_keys'] = ct_get_post_meta_keys( $post_id );
    
    if($ct_inner) {
    	$tree['outerTemplateData'] = array();
    	$tree['outerTemplateData']['edit_link'] = str_replace('&amp;', '&', get_edit_post_link($template_id));

    	$tree['outerTemplateData']['template_name'] = get_the_title($template_id);
    }

	$json = json_encode($tree);

	// echo response
  	header('Content-Type: text/html');
  	echo $json;
	die();
}

function ct_get_components_tree_json() {

	// possible fix
	//error_reporting(E_ALL & ~(E_STRICT|E_NOTICE|E_WARNINGS|E_DEPRECATED));

	$post_id 	= intval( $_REQUEST['post_id'] );
	$id 		= intval( $_REQUEST['id'] );
	$template_id = false;

	oxygen_vsb_ajax_request_header_check();
	
	// if the intended target to be edited is the inner content
	$tree = false;
	$ct_inner = isset($_REQUEST['ct_inner'])?true:false;
	
	$post_type = get_post_type($id);

	// template that has parent
	if($post_type === 'ct_template' && $ct_inner) {
		$singular_tree = oxygen_get_combined_tree($id, true);
		$template_id = get_post_meta( $id, "ct_parent_template", true);
	}
	// not a template or have no parent
	else {
		$singular_json = get_post_meta($id, "ct_builder_json", true);
		if (!$singular_json) {
			$singular_shortcodes = get_post_meta($id, "ct_builder_shortcodes", true);
			$parsed_shortcodes = parse_shortcodes($singular_shortcodes, false);
			if(isset($parsed_shortcodes['content'])) {
				$parsed_shortcodes['content'] = ct_base64_encode_decode_tree($parsed_shortcodes['content'], true);
			}
			$singular_tree = array(
				'id' => 0,
				'name' => 'root',
				'depth' => 0,
				'children' => $parsed_shortcodes['content']
			);
		}
		else {
			$singular_tree = json_decode($singular_json, true);
		}
	}
	
	// regaulr page rendered with template
	if ($post_type !== 'ct_template' && $ct_inner ) {
		
		$ct_other_template = get_post_meta( $id, "ct_other_template", true );

		$template = false;
		if(!empty($ct_other_template) && $ct_other_template > 0) { // no template is specified
			// try getting default template
			$template = get_post($ct_other_template);
		}
		elseif($ct_other_template != -1) { // try getting default template if not explicitly set to not use any template at all


			if(intval($id) == intval(get_option('page_on_front')) || intval($id) == intval(get_option('page_for_posts'))) {
				$template = ct_get_archives_template( $id );

				if(!$template) {  // if not template is set to apply to front page or blog posts page, then use the generic page template, as these are pages
					$template = ct_get_posts_template( $id );
				}
			}
			else {
				$template = ct_get_posts_template( $id );

				if(!$template) {
					$template = ct_get_archives_template( $id );
				}
			}
		}

		if($template) { 
			$tree = oxygen_get_combined_tree($template->ID);
			$template_id = $template->ID;
		} else { 
			// does not even have a default template
			// then use it as a standalone custom view
			$json = get_post_meta( $id, "ct_builder_json", true );
			$tree = json_decode( $json, true );
		}

		if($tree) {

			//recursively obfuscate_ids: ct_id and ct_parent of all elements in $parsed, also obfuscate_selectors
			$ctDepthParser = new CT_Depth_Parser();

			$content = isset($tree['children']) ? $tree['children'] : $tree;

			$prepared_outer_content = ct_prepare_outer_template_json($content, $ctDepthParser);

			$tree = $prepared_outer_content['content'];

			$container_id = $prepared_outer_content['container_id'];
			$parent_id = $prepared_outer_content['parent_id'];
			if(!empty($singular_tree)) {
				$singular_tree = ct_prepare_inner_content($singular_tree['children'], $container_id, $ctDepthParser->getDepths());
				$tree = ct_embed_inner_content($tree, $singular_tree);
			}

		}

	}

	if($tree) {
		$root = array ( 
			"id"	=> 0,
			"name" 	=> "root",
			"depth"	=> 0 
		);
		
		$root['children'] = $tree;
		$tree = $root;
	}

	if(!$tree) {
		$tree = $singular_tree;
	}

	if(!$tree) {
		// no JSON found, fallback to old shortcodes
		ct_get_components_tree();
	}


	if(!isset($tree['name']) || $tree['name'] != 'root') {
        // data is corrupt, the name property should have been equal to root otherwise.
        // provide a clean slate
		
		if (!isset($tree['children'])) {
			$fixed_tree['children'] = $tree;
		} 
		else {
			$fixed_tree = $tree;
		}

        $fixed_tree['id'] = 0;
        $fixed_tree['name'] = 'root';
        $fixed_tree['depth'] = 0;

		$tree = $fixed_tree;
    }

    $tree['meta_keys'] = ct_get_post_meta_keys( $post_id );
    
    if($ct_inner) {
    	$tree['outerTemplateData'] = array();
    	$tree['outerTemplateData']['edit_link'] = str_replace('&amp;', '&', get_edit_post_link($template_id));
    	$tree['outerTemplateData']['template_name'] = get_the_title($template_id);
    }

	$components_defaults = apply_filters( "ct_component_default_params", array() );
	$all_defaults = call_user_func_array('array_merge', array_values($components_defaults));
	$components_defaults["all"] = $all_defaults;

	foreach ($components_defaults as $component_name => $component_defaults) {
		$components_defaults[$component_name] = array_filter($component_defaults, function($item) {
			if ( empty($item) && !is_iterable($item) ) {
				return false;
			}
			return true;
		});
	}

	$json = json_encode(
		array(
			"defaultOptions" => $components_defaults,
			"tree" => $tree
		)
	);

	// echo response
  	header('Content-Type: text/html');
  	echo $json;
	die();
}

add_action('wp_ajax_ct_get_components_tree', 'ct_get_components_tree_json');


/**
 * Adds a flag to the options that the non-chrome-browser 
 * warning in the builder has been dismissed
 * 
 * @since 0.3.4
 * @author Gagan Goraya.
 */

function ct_remove_chrome_modal() {

	$nonce  	= $_REQUEST['nonce'];
	$post_id 	= intval( $_REQUEST['post_id'] );

	// check nonce
	if ( ! isset( $nonce, $post_id ) || ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	    // This nonce is not valid.
	    die( 'Security check' ); 
	}

	// check user role
	if ( ! oxygen_vsb_current_user_can_access() ) {
		die ( 'Security check' );
	}

	update_option('ct_chrome_modal', true);
	die();
}
add_action('wp_ajax_ct_remove_chrome_modal', 'ct_remove_chrome_modal');


/**
 * Get widget instance and return rendered widget view
 * 
 * @since 0.2.3
 */

function ct_render_widget_by_ajax() {

	// TODO review the security aspect

	if (!is_user_logged_in() || !oxygen_vsb_current_user_can_access()) {
	    die();
		}

	$nonce  	= $_REQUEST['nonce'];
	$post_id 	= $_REQUEST['post_id'];

	// check nonce
	if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	    // This nonce is not valid.
	    die( 'Security check' );
	}
	
	$instance = array();
	
	$component_json = file_get_contents('php://input');
	$component 		= json_decode( $component_json, true );
	$options 		= $component['options']['original'];

	//var_dump($options["instance"]);
	
	if ( is_array( $options['instance'] ) ) {
		$instance = $options['instance'];
	}

	if ( $GLOBALS['wp_widget_factory']->widgets[$options['class_name']] ) {
		wp_enqueue_scripts();

		the_widget( $options['class_name'], $instance );
		
		$wp_scripts = wp_scripts();
		$wp_styles  = wp_styles();
		wp_print_scripts( $wp_scripts->queue );
		wp_print_styles( $wp_styles->queue );
	}
	else {
		printf( __("<b>Error!</b><br/> No '%s' widget registered in this installation", "component-theme"), $options['class_name'] );
	}

	die();
}
add_action('wp_ajax_ct_render_widget', 'ct_render_widget_by_ajax');


/**
 * Get widget instance and return rendered widget form view
 * 
 * @since 0.2.3
 */

function ct_render_widget_form_by_ajax() {

	header('Content-Type: text/html');

	$nonce  	= $_REQUEST['nonce'];
	$post_id 	= $_REQUEST['post_id'];

	// check nonce
	if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	    // This nonce is not valid.
	    die( 'Security check' ); 
	}

	// check user role
	if ( ! oxygen_vsb_current_user_can_access() ) {
		die ( 'Security check' );
	}

	$component_json = file_get_contents('php://input');
	$component 		= json_decode( $component_json, true );
	$options 		= $component['options']['original'];

	$instance = array();

	if ( is_array( $options['instance'] ) ) {
		$instance = $options['instance'];
	}

	if ( $GLOBALS['wp_widget_factory']->widgets[$options['class_name']] ) {
		
		wp_enqueue_media();
		wp_enqueue_script( 'media-widgets' );
		//wp_enqueue_style( 'common' );
		//wp_enqueue_style( 'forms' );
		//wp_enqueue_style( 'widgets' );
		
		do_action( 'admin_enqueue_scripts', "widgets.php" );
		do_action( "admin_print_styles-widgets.php" );
		do_action( 'admin_print_styles' );
		do_action( "admin_print_scripts-widgets.php" );
		
		do_action( 'admin_print_scripts' );

		echo "<!-- Widget Form Start -->";
		
		echo '<div class="widget-inside media-widget-control"><div class="form wp-core-ui">';
		echo '<input type="hidden" class="id_base" value="' . $options["id_base"] . '" />';
		echo '<input type="hidden" class="widget-id" value="widget-' . "123" . '" />';
		echo '<div class="widget-content">';

		$updated_instance = 
		$GLOBALS['wp_widget_factory']->widgets[$options['class_name']]->update($instance, array());
		$GLOBALS['wp_widget_factory']->widgets[$options['class_name']]->form($updated_instance);

		echo '</div></div></div>';

		echo "<!-- Widget Form End -->";
		
		do_action( 'admin_footer' );
		do_action( 'admin_print_footer_scripts-widgets.php' );
		do_action( 'admin_print_footer_scripts' );
		do_action( 'admin_footer-widgets.php' );
		
		//wp_footer();
	}
	else {
		printf( __("<b>Error!</b><br/> No '%s' widget registered in this installation", "component-theme"), $options['class_name'] );
	}

	die();
}
add_action('wp_ajax_ct_render_widget_form', 'ct_render_widget_form_by_ajax');


/**
 * Get sidebar instance and return rendered sidebar view
 * 
 * @since 2.0
 */

function ct_render_sidebar_by_ajax() {

	header('Content-Type: text/html');

	$nonce  	= $_REQUEST['nonce'];
	$post_id 	= $_REQUEST['post_id'];

	// check nonce
	if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	    // This nonce is not valid.
	    die( 'Security check' ); 
	}

	// check user role
	if ( ! oxygen_vsb_current_user_can_access() ) {
		die ( 'Security check' );
	}
	
	$component_json = file_get_contents('php://input');
	$component 		= json_decode( $component_json, true );
	$options 		= $component['options']['original'];
	
	if ( is_active_sidebar( $options['sidebar_id'] ) ) { ?>
		<ul><?php dynamic_sidebar( $options['sidebar_id'] ); ?></ul>
	<?php }
	else {
		printf( __("<b>Warning:</b> No '%s' sidebar active in this installation", "component-theme"), $options['sidebar_id'] );
	}

	die();
}
add_action('wp_ajax_ct_render_sidebar', 'ct_render_sidebar_by_ajax');


/**
 * Return SVG Icon Sets
 * 
 * @since 0.2.1
 */

function ct_get_svg_icon_sets() {

	$nonce  	= $_REQUEST['nonce'];
	$post_id 	= intval( $_REQUEST['post_id'] );

	// check nonce
	if ( ! isset( $nonce, $post_id ) || ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	    // This nonce is not valid.
	    die( 'Security check' ); 
	}

	// check user role
    // Note: Added !is_admin() condition to allow regular users access to this,
    // it is needed by the gutenberg integration, to show an icon selection UI
	if ( ! oxygen_vsb_current_user_can_access() && !is_admin() ) {
		die ( 'Security check' );
	}

	//$svg_sets = get_option("ct_svg_sets", array() );
	$svg_sets = oxy_get_svg_sets();

	// Convert XML sets to Objects
	foreach ( $svg_sets as $name => $set ) {

		$xml = simplexml_load_string($set);

		$hasSymbols = true;

		foreach($xml->children() as $def) {
			
			if($def->getName() == 'defs') {
				
				foreach($def->children() as $symbol) {
					if($symbol->getName() == 'symbol') {
						$symbol['id'] = str_replace(str_replace(' ', '', $name), '', $symbol['id']);
					} else {
						$hasSymbols = false;
					}
				}
			} else {
				
				$hasSymbols = false;
			}
		}
		
		if( $hasSymbols ) {
			
			$set = $xml->asXML();
			$svg_sets[$name] = new SimpleXMLElement( $set );
		}
		else {
			unset($svg_sets[$name]);
		}
	}

	$json = json_encode( $svg_sets );

	// echo JSON
	// zlib output_compression automatically compresses in the output buffer at the moment it's flushed.
	// Most server configurations support zlib compression, but it's not a problem if it doesn't.
	ini_set("zlib.output_compression", "On");
	ob_start();
	header('Content-Type: application/json');
	echo $json;
	ob_end_flush();
	die();
}
add_action('wp_ajax_ct_get_svg_icon_sets', 'ct_get_svg_icon_sets');


/**
 * Return template/view data with single post or term posts as JSON
 * 
 * @since 0.1.7
 * @author Ilya K.
 */

function ct_get_template_data() {

	$template_id 		= intval( $_REQUEST['template_id'] );
	$preview_post_id 	= isset($_REQUEST['preview_post_id']) ? sanitize_text_field( $_REQUEST['preview_post_id'] ) : false;
	$nonce  			= $_REQUEST['nonce'];
	$post_id 			= intval( $_REQUEST['post_id'] );
	$type 				= isset($_REQUEST['preview_type']) ? sanitize_text_field($_REQUEST['preview_type'] ) : 'post';

	// check nonce
	if ( ! isset( $nonce, $post_id ) ||  ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	    // This nonce is not valid.
	    die( 'Security check' ); 
	}

	// check user role
	if ( ! oxygen_vsb_current_user_can_access() ) {
		die ( 'Security check' );
	}

	// if no option is selected (as to where this template applies, enforce all options)
	$catch_all = false;
	if(!get_post_meta( $template_id, 'ct_template_all_archives', true )
		&& !get_post_meta( $template_id, 'ct_template_single_all', true )
		&& !get_post_meta( $template_id, 'ct_template_post_types', true )
		&& !get_post_meta( $template_id, 'ct_template_all_archives', true )
		&& !get_post_meta( $template_id, 'ct_template_apply_if_archive_among_taxonomies', true )
		&& !get_post_meta( $template_id, 'ct_template_apply_if_archive_among_cpt', true )
		&& !get_post_meta( $template_id, 'ct_template_apply_if_archive_among_authors', true )
		&& !get_post_meta( $template_id, 'ct_template_date_archive', true )
		&& !get_post_meta( $template_id, 'ct_template_front_page', true )
		&& !get_post_meta( $template_id, 'ct_template_blog_posts', true )
		&& !get_post_meta( $template_id, 'ct_template_search_page', true )
		&& !get_post_meta( $template_id, 'ct_template_404_page', true )
		&& !get_post_meta( $template_id, 'ct_template_inner_content', true )
		&& !get_post_meta( $template_id, 'ct_template_index', true )) {
		$catch_all = true;
	}


	if ( $preview_post_id && "post" === $type ) {
		// single view
		$data = ct_get_templates_post( $template_id, $preview_post_id, false, $catch_all );

	} elseif ( $preview_post_id && "term" === $type ) {
		// archive view
		$data = ct_get_templates_term( $template_id, $preview_post_id, false, $catch_all );

	} elseif( !$preview_post_id ) {
		
		$data = ct_get_templates_post( $template_id, false, false, $catch_all );
		
		if(is_array($data)) {

			$template_terms = ct_get_templates_term( $template_id, false, false, $catch_all );

			if(is_array($template_terms)) {

				$data = array_merge( $data, $template_terms);
			}
		}
		else {
			$data = ct_get_templates_term( $template_id, false, false, $catch_all );	
		}
	}

	if (isset($data['postData']) && is_object($data['postData'])){
		$data['postData']->frontendURL = $data['postData']->permalink;
		if (force_ssl_admin()) {
			$data['postData']->permalink = str_replace("http://", "https://", $data['postData']->permalink);
		}
	}
	
	if (isset($data['postData']) && is_array($data['postData'])){
		$data['postData']['frontendURL'] = $data['postData']['permalink'];
		if (force_ssl_admin()) {
			$data['postData']['permalink'] = str_replace("http://", "https://", $data['postData']['permalink']);
		}
	}

	// make GET request to permalink to retrive body class
	$post_data = isset($data["postData"]) ? (array) $data["postData"] : array();
	$response = false;
	
	if(isset($post_data["permalink"])) {
		$response = wp_remote_get( add_query_arg( 'ct_get_body_class', 'true', $post_data["permalink"] ) );
	}

	if ( is_array($response) && isset($response['response']) && $response['response']['code'] === 200 ) {
		$body = $response['body'];
		$data["bodyClass"] = $body;
	}

	
	$homepage 	= get_option('page_on_front');

	if(isset($data['postsList']) && is_array($data['postsList'])) {
		foreach($data['postsList'] as $key => $item) {
			// if the item has shortcodes
			$json 		= get_post_meta($item['id'], 'ct_builder_json', true);
			$shortcodes = get_post_meta($item['id'], 'ct_builder_shortcodes', true);

			if(!isset($data['default'])) {
				if(
					($shortcodes && !empty($shortcodes))
					|| 
					($json && oxygen_json_has_elements($json))
				) {
					$data['default'] = $item;
				}
			}

			if($homepage && is_numeric($homepage) && intval($item['id']) === intval($homepage)) {
				if(
					($shortcodes && !empty($shortcodes))
					|| 
					($json && oxygen_json_has_elements($json))
				) {
					$data['default'] = $item;
					break;
				}
			}
		}
	}
	
	// Return JSON
  	header('Content-Type: application/json');
	echo json_encode($data);
	die();
}

add_action('wp_ajax_ct_get_template_data', 'ct_get_template_data');


function oxy_load_editing_list() {

	oxygen_vsb_ajax_request_header_check();

	$query = $_REQUEST['query'] ?? '';
	$post_id = $_REQUEST['post_id'];
	$limit = get_option('oxygen_vsb_preview_dropdown_limit');

	// current post
	$current_post_query = new WP_Query( array(
		"p" => $post_id,
		'post_type' => array("post","page","ct_template","oxy_user_library")
	) );
	$current_post = $current_post_query->posts;

	// templates
	$args = array(
		's' => $query,
		'post__not_in' => array($post_id),
		'posts_per_page' => intval($limit)/4,
		'post_type' => array("ct_template"),
		'orderby' => "modified",
	);
	$templates = new WP_Query( $args );
	
	// pages
	$args = array(
		's' => $query,
		'post__not_in' => array($post_id),
		'posts_per_page' => intval($limit)/4,
		'post_type' => array("page"),
		'orderby' => "modified",
	);
	$pages = new WP_Query( $args );

	// posts
	$args = array(
		's' => $query,
		'post__not_in' => array($post_id),
		'posts_per_page' => intval($limit)/4,
		'post_type' => array("post"),
		'orderby' => "modified",
	);
	$posts = new WP_Query( $args );

	// blocks
	$args = array(
		's' => $query,
		'post__not_in' => array($post_id),
		'posts_per_page' => intval($limit)/4,
		'post_type' => array("oxy_user_library"),
		'orderby' => "modified",
	);
	$blocks = new WP_Query( $args );

	// custom
	$other = [];
	$additional_post_types = apply_filters( 'oxygen_vsb_navigator_post_types', [] );

	if( !empty($additional_post_types) ) {
		$args = array(
			's' => $query,
			'post__not_in' => array($post_id),
			'posts_per_page' => intval($limit)/4,
			'post_type' => $additional_post_types,
			'orderby' => "modified",
		);

		$other = new WP_Query( $args );
	}

	$posts = array_merge( $current_post, $templates->posts, $pages->posts, $blocks->posts, $posts->posts, $other->posts ?? []);
	
	foreach($posts as $key => $postitem) {
		$posts[$key]->url = oxy_get_builder_url($postitem->ID);
		$posts[$key]->type = get_post_type($postitem->ID);
		
		if ($posts[$key]->type == "ct_template") {
			$posts[$key]->type = "template";
			
		}
		
		if ($posts[$key]->type == "oxy_user_library") {
			$posts[$key]->type = "block";
		}
	}

	// Return JSON
	header('Content-Type: application/json');
	echo json_encode($posts);
	die();
}

add_action('wp_ajax_oxy_load_editing_list', 'oxy_load_editing_list');


/**
 * Get Elements API component's HTML templates
 *
 * @author Ilya K.
 * @since 2.3
 */

function oxy_get_components_templates() {

	oxygen_vsb_ajax_request_header_check();

	$templates = apply_filters("oxy_components_html_templates", array());
	
	// Return JSON
  	header('Content-Type: application/json');
	echo json_encode($templates);
	die();	
}
add_action('wp_ajax_oxy_get_components_templates', 'oxy_get_components_templates');


/**
 * Return single post object as JSON by ID including shortcodes
 * 
 * @since 0.2.3
 * @author Ilya K.
 */

function ct_get_post_data() {
	
	$nonce  	= $_REQUEST['nonce'];
	$post_id 	= intval( $_REQUEST['post_id'] );
	$preview_post_id = isset( $_REQUEST['preview_post_id'] ) ? intval( $_REQUEST['preview_post_id'] ) : false;

	// check nonce
	if ( ! isset( $nonce, $post_id ) || ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	    // This nonce is not valid.
	    die( 'Security check' ); 
	}

	$id 	= intval( $_REQUEST['id'] );
	$post 	= get_post( $id );

	// check user role
	if ( ! oxygen_vsb_current_user_can_access() ) {
		die ( 'Security check' );
	}

	if ( $post ) {
		$data = ct_filter_post_object( $post, false, $preview_post_id ? $preview_post_id : $post_id );
	}

	// base 64 decode all the custom-css and custom-js down the tree
	$data->post_tree = ct_base64_encode_decode_tree($data->post_tree, true);
	$data->edit_link = str_replace('&amp;', '&', get_edit_post_link($id));
	// Echo JSON
  	header('Content-Type: application/json');
	echo json_encode($data);
	die();
}
add_action('wp_ajax_ct_get_post_data', 'ct_get_post_data');


/**
 * Get SoundCloud track id by URL, the hacky way
 * 
 * @since 2.0
 * @author Ilya K.
 */

function oxy_get_soundcloud_track_id() {
	
	$nonce  	= $_REQUEST['nonce'];
	$post_id 	= intval( $_REQUEST['post_id'] );

	// check nonce
	if ( ! isset( $nonce, $post_id ) || ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	    // This nonce is not valid.
	    die( 'Security check' ); 
	}

	// check user role
	if ( ! oxygen_vsb_current_user_can_access() ) {
		die ( 'Security check' );
	}

	$response = wp_remote_get( $_REQUEST['soundcloud_url'] );
	$body = wp_remote_retrieve_body($response);
	preg_match('#soundcloud://sounds:\d+#', $body, $matches);
	$track_id=str_replace("soundcloud://sounds:", "", $matches[0]);
	

	// Echo JSON
  	header('Content-Type: application/json');
	echo json_encode($track_id);
	die();
}
add_action('wp_ajax_oxy_get_soundcloud_track_id', 'oxy_get_soundcloud_track_id');


/**
 * NON REFACTORED BELOW
 * 
 */
    

/**
 * Exec PHP/HTML code and return output
 * 
 * @since 0.2.4
 * @author Ilya K.
 * @deprecated 0.4.0
 */

function ct_exec_code() {

	$nonce  	= $_REQUEST['nonce'];
	$post_id 	= intval( $_REQUEST['post_id'] );

	// check nonce
	if ( ! isset( $nonce, $post_id ) || ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	    // This nonce is not valid.
	    die( 'Security check' ); 
	}

	// check user role
	if ( ! oxygen_vsb_current_user_can_access() ) {
		die( 'Security check' );
	}

	// get all data JSON
	$data = file_get_contents('php://input');

	// encode and separate tree from options
	$data = json_decode($data, true);

	$code = $data['code'];
	$term = isset($data['term']) ? $data['term'] : false;
	$post = isset($data['post']) ? $data['post'] : false;

	$code = oxygen_base64_decode_for_json($code);

	// archive template
	if ( $term ) {
		
		$term = json_decode(stripcslashes($term), true);

		/**
		 * Archives
		 */
		
		if ( isset( $term["term_id"] ) ) {

			// get all the registered taxonomies
			$taxonomies = get_taxonomies( array() , 'objects' );

			$query = array (
				/** query_var of the registered taxonomy will act as a key here, 
				 *	e.g. for category the query_var is category_name
				 */
				$taxonomies[$term['taxonomy']]->query_var => $term['slug']
			);
		}

		/**
		 * Post types
		 */
		
		else {
			$query = array( 'post_type' => $term['name'] );
		}

	}
	// single template
	elseif ( $post ) {

		/**
		 * $post is WP_Post object, need to reproduce WP_Query for this post
		 */

		$query = get_query_vars_from_id($post["ID"]);
	}
	// not template
	else {

		$query = isset($_REQUEST['query']) ? $_REQUEST['query'] : "";
		$query = json_decode(stripcslashes($query), true);
	}

	// simulate WP Query
	global $wp_query;
	$wp_query = new WP_Query($query);

	//var_dump($wp_query); // this seems to be OK

	// check for code
	if ( $code ) {
		eval( ' ?>' . $code . '<?php ' );
	}
	else {
		_e('No code found', 'component-theme');
	}

	/* Restore original Post Data. Do we actually need this? */
	wp_reset_postdata();

	die();
}

add_action('wp_ajax_ct_exec_code', 'ct_exec_code');


function ct_new_api_remote_get($source, $path = '') {

	if(empty($path)) {
		return false;
	}

	$composite_license_key = get_option("oxygen_composite_elements_license_key");
	$oxygen_license_key = get_option("oxygen_license_key");

	$args = array(
	  'headers' => array(
	    'oxygenclientversion' => '3.7rc1',
	    'compositeelementslicensekey' => $composite_license_key,
		'oxygenlicensekey' => $oxygen_license_key,
		'compositeelementssiteurl' => OxygenCompositeElementsPluginUpdater::clean_site_url(home_url()),
	  ),
	  'timeout' => 15,
	);


   // figure out the access key

	$accessKey = '';
	$site = ct_find_source_site_from_url($source);

	if(is_array($site)) {
		if(!isset($site['system']) || $site['system'] !== true) {
			$accessKey = md5($site['accesskey']);
		}
		else {
			$accessKey = false;
		}
	}

	if($accessKey !== false) {
		$args['headers']['auth'] = $accessKey;
	}

	$result = wp_remote_request($source.'?rest_route=/oxygen-vsb-connection/v1/'.$path, $args);

	$status = wp_remote_retrieve_response_code($result);
	
	if ( is_wp_error( $result ) ) {
		header('Content-Type: application/json');
		echo json_encode(array(
			'error' => $result->get_error_message(),
		));
		die();
	} 
	elseif($status !== 200) {
		header('Content-Type: application/json');
		echo json_encode(array(
			'error' => wp_remote_retrieve_response_message($result),
		));
		die();
	}

	if(is_array($result)) {
		return $result['body'];
	}

	return false;
}

add_action('wp_ajax_ct_new_style_api_call', 'ct_new_style_api_call');

function ct_new_style_api_call_security_check($call_type) {

	$failure = false;

	if($call_type == 'setup_default_data') {

		if ( !wp_verify_nonce( $_REQUEST['nonce'], 'oxygen_vsb_default_site_setup' )) {
		    $failure = true;
		}

	}
	else {
		$nonce  	= $_REQUEST['nonce'];
		$post_id 	= $_REQUEST['post_id'];

		// check nonce
		if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
		    $failure = true;
		}
	}


	if (!oxygen_vsb_current_user_can_access()) {
		$failure = true;
	}

	if($failure) {
		wp_send_json_error();
	}

}

function ct_new_style_api_call() {
	
	$call_type = isset($_REQUEST['call_type'])?sanitize_text_field($_REQUEST['call_type']):false;
	
	ct_new_style_api_call_security_check($call_type);

	switch($call_type) {
		case 'setup_default_data':
			ct_setup_default_data();
		break;
		case 'get_component_from_source':
			ct_get_component_from_source();
		break;
		case 'get_page_from_source':
			ct_get_page_from_source();
		break;
		case 'get_items_from_source':
			ct_get_items_from_source();
		break;
		case 'get_stuff_from_source':
			ct_get_stuff_from_source();
		break;
	}

	die();
}

function ct_get_screenshot_of_source() {
	
	global $ct_source_sites;

	$name = isset($_REQUEST['name'])?sanitize_text_field($_REQUEST['name']):false;

	if(isset($ct_source_sites[$name])) {
		$result = ct_new_api_remote_get($ct_source_sites[$name]['url'], 'screenshot/');
		header('Content-Type: application/json');
		echo $result;
	}

	die();
}

function ct_get_stuff_from_source() {
	
	global $ct_source_sites;

	$next = isset($_REQUEST['next'])?sanitize_text_field($_REQUEST['next']):false;

	$index = $next?$next:0;

	$keys = array_keys($ct_source_sites);

	$site = $ct_source_sites[$keys[$index]];

	if(!is_array($site)) {
		die();
		
	}

	$result = ct_new_api_remote_get($site['url'], 'items');


	$output = array(
		'items' => json_decode($result, true),
		'key' => $keys[$index],
		'next' => (isset($keys[$index+1]) && isset($ct_source_sites[$keys[$index+1]]))?$index+1:0
	);

	header('Content-Type: application/json');

	echo json_encode($output);

	die();
}

function ct_get_items_from_source() {
	
	global $ct_source_sites;

	$name = isset($_REQUEST['name'])?sanitize_text_field($_REQUEST['name']):false;
	
	$site = $ct_source_sites[$name];


	if(!is_array($site)) {
		
		die();
	}


	$result = ct_new_api_remote_get($site['url'], 'items');
	
	header('Content-Type: application/json');
	echo $result;
	
	die();
}

function ct_setup_default_data() {

	$type = isset($_REQUEST['type'])?sanitize_text_field($_REQUEST['type']):false;
	$name = isset($_REQUEST['site'])?sanitize_text_field($_REQUEST['site']):false;
	$delete = (isset($_REQUEST['delete']) && $_REQUEST['delete'] === 'delete')?true:false;

	$site = false;
	$accessKey = '';

	if($name) {
		global $ct_source_sites;
		if(is_array($ct_source_sites) && isset($ct_source_sites[$name])) {
			$site = $ct_source_sites[$name]['url'];
			if(!isset($ct_source_sites[$name]['system']) || $ct_source_sites[$name]['system'] !== true) {
				$accessKey = $ct_source_sites[$name]['accesskey'];
			}
			else {
				$accessKey = false;
			}
		}
		else {
			die();
		}
	}

	if(!$type || !$site) {
		die();
	}
	
	header('Content-Type: application/json');
	$response = array();

	switch($type) {
		case 'Colors':
			ct_setup_default_colors($site, $delete, $accessKey);
			$response['next'] = 'Stylesheets';
			break;
		case 'Stylesheets':
			$result = ct_setup_default_stylesheets($site, $delete, $accessKey);	
			$response['next'] = 'Settings';
			break;
		case 'Settings':
			ct_setup_default_settings($site, $delete, $accessKey);	
			$response['next'] = 'Stylesets';
			break;
		case 'Stylesets':
			ct_setup_default_stylesets($site, $delete, $accessKey);	
			$response['next'] = 'Selectors';
			break;
		case 'Selectors':
			ct_setup_default_selectors($site, $delete, $accessKey);	
			$response['next'] = 'Templates';
			break;
		case 'Templates':
			ct_setup_default_templates($site, $delete, $accessKey);	
			$response['next'] = 'Pages';
			break;
		case 'Pages':
			ct_setup_default_pages($site, $accessKey);	
			$response['next'] = 'Classes';
			break;
		case 'Classes':
			ct_setup_default_classes($site, $delete, $accessKey);	

			// it is time to generate universal.css and update meta
			oxygen_vsb_cache_universal_css();

			// register the site that has been set
			update_option('ct_last_installed_default_data', $name);
			// load up a message
			set_transient('oxygen-vsb-admin-notice-transient', 'The website was installed successfully.');
			$response['done'] = true;
			break;
	}
	
	echo json_encode($response);

	die();
	
}

function ct_recursively_manage_reusables($children, $source_info, $source) {

	foreach($children as $key => $item) {

		if($item['name'] == 'ct_reusable') {

			unset($children[$key]);
			// global $wpdb;
			// // check if a ct_source_site meta exists with value equal to that of the view_id,
			// $data = $wpdb->get_results("SELECT * FROM `".$wpdb->postmeta."` WHERE meta_key='ct_source_post' AND meta_value='".$wpdb->prepare($item['options']['view_id'])."'");
			// $post_info = array();

			// if (is_array($data) && !empty($data)) {
			// 	foreach($data as $meta) {
			// 		$post_info[] = $meta->post_id;
			// 	}
			// }

			// $result = array_intersect($source_info, $post_info);

			// // if yes, then just change the view_id to the corresponding post_id,
			// if(isset($result[0])) {
				
			// 	// ok, it exists
			// 	$children[$key]['options']['view_id'] = $result[0];

			// } else {
			// 	$shortcodes = $item['shortcodes'];

			// 	// create a post
			// 	$post_data = array(
			// 		'post_title' => $item['post_title'],
			// 		'menu_order' => $item['menu_order'],
			// 		'post_type' => 'ct_template',
			// 		'post_status' => 'publish'
			// 	);

			// 	$new_id = wp_insert_post($post_data);

			// 	update_post_meta($new_id_map[$template['ID']], 'ct_template_type', $template['template_type']);

			// 	update_post_meta($new_id, 'ct_source_site', $source);
			// 	update_post_meta($new_id, 'ct_source_post', $children[$key]['options']['view_id']);
				
			// 	update_post_meta($new_id, 'ct_builder_shortcodes', $shortcodes);
			// 	update_post_meta($new_id, 'ct_template_type', 'reusable_part');

			// 	$children[$key]['options']['view_id'] = $new_id;

			// 	unset($children[$key]['shortcodes']);
			// 	unset($children[$key]['post_title']);
			// 	unset($children[$key]['menu_order']);
				
			// }

			
		}

		if(isset($children[$key]['children']) && is_array($children[$key]['children'])) {
			$children[$key]['children'] = ct_recursively_manage_reusables($children[$key]['children'], $source_info, $source);
		}
	}

	return $children;
}

function ct_get_page_from_source() {

	$id = isset($_REQUEST['id'])?sanitize_text_field($_REQUEST['id']):false;
	$source = isset($_REQUEST['source'])?sanitize_text_field($_REQUEST['source']):false;

	$result = ct_new_api_remote_get(base64_decode($source), 'pagesclasses/'.$id);

	$components = array();
	$classes = array();
	$colors = array();
	$lookupTable = array();

	if($result) {
		$content = json_decode($result, true);
		if(isset($content['components']))
			$components = $content['components'];
		if(isset($content['classes']))
			$classes = $content['classes'];
		if(isset($content['colors']))
			$colors = $content['colors'];
		if(isset($content['lookuptable']))
			$lookupTable = $content['lookuptable'];
	}

	global $wpdb;

	foreach($components as $key => $component) {

		// if it is a reusable do something about it.
		if($component['name'] === 'ct_reusable') {
			unset($components[$key]);
		}

		if(!isset($components[$key])) {
			continue; // it could have bene deleted while dealing with a reusable in the previous step
		}

		$components[$key] = ct_base64_encode_decode_tree(array($component), true)[0];

		if(isset($component['children'])) {
			
			$data = $wpdb->get_results("SELECT * FROM `".$wpdb->postmeta."` WHERE meta_key='ct_source_site' AND meta_value='".$wpdb->prepare(base64_decode($source), array())."'");
			$source_info = array();

			if (is_array($data) && !empty($data)) {
				foreach($data as $meta) {
					// if post exists and is not in trash
					$post = get_post($meta->post_id);

					if($post && $post->post_status != 'trash') {
						$source_info[] = $meta->post_id;
					}
				}
			}

			if(is_array($components[$key]['children'])) {

				$components[$key]['children'] = ct_recursively_manage_reusables($components[$key]['children'], $source_info, base64_decode($source));
			}
		}	
	}

	$output = array(
			'components' => $components
		);

	if(sizeof($classes) > 0) {
		$output['classes'] = $classes;
	}

	if(sizeof($colors) > 0) {
		$output['colors'] = $colors;
	}

	if(sizeof($lookupTable) > 0) {
		$output['lookuptable'] = $lookupTable;
	}
	
	header('Content-Type: application/json');
	echo json_encode($output);
	die();
}

function ct_find_source_site_from_url($url) {
	global $ct_source_sites;
	$found = false;
	foreach($ct_source_sites as $key => $item) {

		if($item['url'] == $url) {
			$found = $item;
			break;
		}

	}
	return $found;
}

function ct_get_component_from_source() {

	$id = isset($_REQUEST['id'])?sanitize_text_field($_REQUEST['id']):false;
	$page = isset($_REQUEST['page'])?sanitize_text_field($_REQUEST['page']):false;
	$source = isset($_REQUEST['source'])?sanitize_text_field($_REQUEST['source']):false;

	
	$component_path = 'componentsclasses/'.$id.'/'.$page;

	
	$result = ct_new_api_remote_get(base64_decode($source), $component_path);

	$component = array();
	$classes = array();
	$colors = array();
	$lookupTable = array();
	
	if($result) {
		$content = json_decode($result, true);
		if(isset($content['component']))
			$component = $content['component'];
		if(isset($content['classes']))
			$classes = $content['classes'];
		if(isset($content['colors']))
			$colors = $content['colors'];
		if(isset($content['lookuptable']))
			$lookupTable = $content['lookuptable'];
	}

	$component = ct_base64_encode_decode_tree(array($component), true)[0];

	if(isset($component['children'])) {

		global $wpdb;
		$data = $wpdb->get_results("SELECT * FROM `".$wpdb->postmeta."` WHERE meta_key='ct_source_site' AND meta_value='".$wpdb->prepare(base64_decode($source), array())."'");
		$source_info = array();

		if (is_array($data) && !empty($data)) {
			foreach($data as $meta) {
				// if post exists and is not in trash
				$post = get_post($meta->post_id);

				if($post && $post->post_status != 'trash') {
					$source_info[] = $meta->post_id;
				}
			}
		}

		if(is_array($component['children'])) {
			$component['children'] = ct_recursively_manage_reusables($component['children'], $source_info, base64_decode($source));
		}
	}

	$output = array('component' => $component);

	if(sizeof($classes) > 0) {
		$output['classes'] = $classes;
	}

	if(sizeof($colors) > 0) {
		$output['colors'] = $colors;
	}

	if(sizeof($lookupTable) > 0) {
		$output['lookuptable'] = $lookupTable;
	}
	
	header('Content-Type: application/json');
	echo json_encode($output);
	die();
}

function ct_setup_default_pages($site, $accessKey = '') {

	$templates_id_map = get_transient('oxygen-vsb-templates-id-map');

	delete_transient('oxygen-vsb-templates-id-map');

	global $ct_source_color_map;

	$ct_source_color_map = get_transient('oxygen_vsb_source_color_map', array());

	$result = ct_new_api_remote_get($site, ($accessKey !== false?'allpages':'pages'));

	// if no error
	// $result['body'] contains the json templates from the default source
	// parse the json to array, and foreach through it, inserting each template item into the current db
	$pages = array();

	if($result) {
		$pages = json_decode($result, true);
	}

	if(!is_array($pages)) {
		return;
	}

	$new_id_map = array();


	$selectiveClasses = get_transient('oxygen-vsb-default-setup-classes', array());

	$current_user = wp_get_current_user();

	// insert posts
	foreach($pages as $page) {

		$post_data = $page;
		
		unset($post_data['ID']);

		$post_data['post_type'] = 'page';
		$post_data['post_status'] = 'publish';
		
		if($current_user && isset($current_user->ID)) {
			$post_data['post_author'] = $current_user->ID;
		}

		$new_id_map[$page['ID']] = wp_insert_post($post_data);
		foreach($page['applied_classes'] as $key => $val) {
			$selectiveClasses[$key] = $val;			
		}
	}
	
	set_transient('oxygen-vsb-default-setup-classes', $selectiveClasses);

	$siteName = isset($_REQUEST['site'])?sanitize_text_field($_REQUEST['site']):'defaultIncoming';
	global $oxygen_vsb_classic_designsets, $oxygen_vsb_global_colors;


	foreach($pages as $page) {
		
		// update parent status
		$post_data = array(
			'ID' => $new_id_map[$page['ID']],
			'post_parent' => isset($new_id_map[$page['post_parent']]) ? $new_id_map[$page['post_parent']] : 0,
		);

		wp_update_post($post_data);

		// TODO run through the shortcodes, if some re-usable in place, replace its id from the $new_id_map
		$shortcodes = parse_shortcodes($page['builder_shortcodes'], false, false);

		//$shortcodes['content'] = ct_swap_reusable_view_ids( $shortcodes['content'], $templates_id_map );

		// map colors
		if(sizeof($ct_source_color_map) > 0) {
			// replace all global color values to match with the imported ones
			$shortcodes['content'] = ct_map_source_colors($shortcodes['content']);
		}

		if(in_array($siteName, $oxygen_vsb_classic_designsets)) { // if it is an old design set
			// generate new variable colors
			// if a lookup table is avaibale?
			$lookupTable = get_transient('oxygen_vsb_source_color_lookup_table', array());
			$shortcodes['content'] = ct_create_variable_colors($shortcodes['content'], $siteName, $lookupTable);
		}

		$wrap_shortcodes = array();

		$wrap_shortcodes['children'] = $shortcodes['content'];
		// code tree back to JSON to pass into old function
		$components_tree_json = json_encode( $wrap_shortcodes );
		
		ob_start();
	
		// transform JSON to shortcodes
		$shortcodes = components_json_to_shortcodes( $components_tree_json );
	
		// we don't need anything to be output by custom shortcodes
		ob_clean();

		update_post_meta($new_id_map[$page['ID']], 'ct_builder_shortcodes', $shortcodes);
		update_post_meta($new_id_map[$page['ID']], 'ct_other_template', (isset($templates_id_map[$page['other_template']])?$templates_id_map[$page['other_template']]:$page['other_template']));
		$wrap_shortcodes['children'] = ct_base64_encode_decode_tree($wrap_shortcodes['children'], true);

		update_post_meta($new_id_map[$page['ID']], 'ct_builder_json', addslashes(json_encode(
			array(
				'ct_id' => 0,
				'name' => 'root',
				'depth' => 0,
				'children' => $wrap_shortcodes['children'],
			)))
		);

		// cache styles
		oxygen_vsb_cache_page_css($new_id_map[$page['ID']], $shortcodes);
	}

	update_option('oxygen_vsb_global_colors', $oxygen_vsb_global_colors);

}

function oxygen_is_setting_default_data() {
	return isset($_REQUEST['call_type']) && $_REQUEST['call_type'] = 'setup_default_data';
}

function ct_setup_default_selectors($site, $delete = false, $accessKey = '') {

	$result = ct_new_api_remote_get($site, 'selectors');

	// if no error
	if($result) {

		$selectors = json_decode($result, true);

		if(!is_array($selectors)) {
			return;
		}

		// map colors
		global $ct_source_color_map;
		global $oxygen_vsb_classic_designsets;

		$ct_source_color_map = get_transient('oxygen_vsb_source_color_map', array());

		if(sizeof($ct_source_color_map) > 0) {
			// replace all global color values to match with the imported ones
			$selectors = ct_map_source_colors($selectors);
		}

		// if selectors have their parent as a 'Uncategorized Custom Selectors', rename their parent to 
		$site_name = (isset($_REQUEST['site'])?sanitize_text_field($_REQUEST['site']):'defaultIncoming');

		if(in_array($site_name, $oxygen_vsb_classic_designsets)) { // if it is an old design set
			// generate new variable colors
			global $oxygen_vsb_global_colors;
			// if a lookup table is avaibale?
			$lookupTable = get_transient('oxygen_vsb_source_color_lookup_table', array());
			$selectors = ct_create_variable_colors($selectors, $site_name, $lookupTable);
			update_option('oxygen_vsb_global_colors', $oxygen_vsb_global_colors);
		}

		$create_default_styleset = false;

		foreach($selectors as $key => $item) {
			if($item['set_name'] === 'Uncategorized Custom Selectors') {
				$create_default_styleset = true;
				$selectors[$key]['set_name'] = $site_name.' style set';
			}
		}

		if($create_default_styleset) {
			$existing = get_option('ct_style_sets', array());
			$existing[$site_name.' style set'] = array('key' => $site_name.' style set', 'parent' => $site_name);
			update_option('ct_style_sets', $existing);
		}

		// append to existing style sheets
		$existing = get_option('ct_custom_selectors', array());
		
		if(is_array($existing) && !$delete) {
			$selectors = array_merge($existing, $selectors);
		}

		update_option('ct_custom_selectors', $selectors, get_option("oxygen_options_autoload"));
	}

}

function ct_setup_default_colors($site, $delete = false, $accessKey = '') {

	$result = ct_new_api_remote_get($site, 'colors');
	$colors = false;
	$lookupTable = false;

	if($result) {
		$result = json_decode($result, true);
		$colors = isset($result['colors'])? $result['colors'] : false;
		$lookupTable = isset($result['lookuptable'])? $result['lookuptable'] : false;
	}

	// if a lookup table is provided (by a classic design set, store it as transient data to be used in other steps)
	if(is_array($lookupTable)) {
		// convert keys to lower case
		$smLookupTable = array();

		foreach($lookupTable as $key => $item) {
			$smLookupTable[strToLower($key)] = $item;
		} 
		
		set_transient('oxygen_vsb_source_color_lookup_table', $smLookupTable);
	}
	else {
		delete_transient('oxygen_vsb_source_color_lookup_table');
	}

	if(is_array($colors)) {

		$sourceColorMap = array();

		if(isset($colors['code']) && $colors['code'] == 'rest_no_route') {
			return;
		}
 
		// set Name for the incoming colors
		$setName = isset($_REQUEST['site'])?sanitize_text_field($_REQUEST['site']):'defaultIncoming';

		if($delete) { // replace all old data

			$existing = array(
				'colorsIncrement' => 0,
				'setsIncrement' => 1,
				'colors' => array(),
				'sets' => array(
					array(
						'id' => 1,
						'name' => $setName,
					)
				),
			);

			foreach($colors as $key => $color) {

				$sourceColorMap[$color['id']] = ++$existing['colorsIncrement'];

				$color['id'] = $existing['colorsIncrement'];

				// assign the new Parent
				$color['set'] = 1;

				$existing['colors'][] = $color;
			}

			update_option('oxygen_vsb_global_colors', $existing);

		}

		else { // add to the existing data
			
			// colors are an array of arrays with each having a name and a value
			// get existing colors

			$existing = get_option('oxygen_vsb_global_colors', array());

			$existing['colors'];

			$existing['sets'];

			// find a set with that name
			$existingSetId = false;

			foreach($existing['sets'] as $key => $set) {

				if($set['name'] == $setName) {
					$existingSetId = $set['id'];
				}

				//remove hashkey attribute from the existing sets
				if(isset($existing['sets'][$key]['$$hashKey']))
					unset($existing['sets'][$key]['$$hashKey']);
			}

			// if this set does not already exist, create it
			if($existingSetId === false) {

				$existingSetId = ++$existing['setsIncrement'];

				$existing['sets'][] = array(
					'id' => $existingSetId, // and increment
					'name' => $setName
				);

			}
			
			// remove hash keys from all existing colors
			foreach($existing['colors'] as $key => $color) {
				if(isset($existing['colors'][$key]['$$hashKey']))
					unset($existing['colors'][$key]['$$hashKey']);
			}

			// for each of the incoming colors
			foreach($colors as $key => $color) {

				// if a color with the same name already exists in the same set, then over write it
				$existingColorUpdated = false;

				foreach($existing['colors'] as $eKey => $eColor) {

					if($eColor['name'] == $color['name'] && $eColor['set'] === $existingSetId) {

						$existing['colors'][$eKey]['value'] = strtolower($color['value']);

						$sourceColorMap[$color['id']] = $eColor['id'];

						$existingColorUpdated = true;
						break;
					}

				}

				//updating an existing color, so no need to add it as a new one, so skip the rest
				if($existingColorUpdated) {
					continue;
				}

				$sourceColorMap[$color['id']] = ++$existing['colorsIncrement'];

				// add a new ID for adjusting it into the existing colors array
				$color['id'] = $existing['colorsIncrement'];

				// assign the new Parent
				$color['set'] = $existingSetId;

				$color['value'] = strtolower($color['value']);

				// add the color to the existing colors;

				$existing['colors'][] = $color;

			}

			update_option('oxygen_vsb_global_colors', $existing);
		}

		set_transient('oxygen_vsb_source_color_map', $sourceColorMap);

	}

}

function ct_setup_default_classes($site, $delete = false, $accessKey = '') {

	$result = ct_new_api_remote_get($site, 'classes');

	// if no error
	if($result) {
		$classes = json_decode($result, true);
		if(!is_array($classes)) {
			return;
		}
		
		// if there are selective classes to be downloaded
		$selectiveClasses = get_transient('oxygen-vsb-default-setup-classes', array());
		delete_transient('oxygen-vsb-default-setup-classes');

		if(sizeof($selectiveClasses) > 0) {

			foreach($selectiveClasses as $key => $classItem) {
				if(isset($classes[$key])) {
					$selectiveClasses[$key] = $classes[$key];
				}
			}

			$classes = $selectiveClasses;
		}
		
		// map colors
		global $ct_source_color_map;
		global $oxygen_vsb_classic_designsets;

		$ct_source_color_map = get_transient('oxygen_vsb_source_color_map', array());

		delete_transient('oxygen_vsb_source_color_map');

		if(sizeof($ct_source_color_map) > 0) {
			// replace all global color values to match with the imported ones
			$classes = ct_map_source_colors($classes);
		}
		// if a folder from the incoming does not already exist, create one
		$folderName = isset($_REQUEST['site'])?sanitize_text_field($_REQUEST['site']):'defaultIncoming';

		if(in_array($folderName, $oxygen_vsb_classic_designsets)) { // if it is an old design set
			// generate new variable colors
			global $oxygen_vsb_global_colors;
			// if a lookup table is avaibale?
			$lookupTable = get_transient('oxygen_vsb_source_color_lookup_table', array());
			$classes = ct_create_variable_colors($classes, $folderName, $lookupTable);
			update_option('oxygen_vsb_global_colors', $oxygen_vsb_global_colors);
		}

		// existing classes
		$existing = get_option('ct_components_classes', array());

		$folders = get_option('ct_style_folders', array());

		// if a folder from the incoming does not already exist, create one
		$folderName = isset($_REQUEST['site'])?sanitize_text_field($_REQUEST['site']):'defaultIncoming';
		//$folderName = $site;

		$newFolders = array();

		if(!isset($folders[$folderName])) {
			$newFolders[$folderName] = array(
				'key' => $folderName,
				'status' => 1
			);
		}
		else {
			$newFolders[$folderName] = array(
				'key' => $folderName,
				'status' => $folders[$folderName]['status']
			);
		}
		
		foreach($classes as $key => $incoming) {
			$classes[$key]['parent'] = $folderName;
		}

		if(is_array($existing) && !$delete) {

			// disable all folders, now this will also take care of stylesets folders

			foreach($folders as $key => $folder) {
				// unless the folder is that of the incoming site
				if( array_key_exists( 'name', $folder ) && $folder['name'] !== $folderName) {
					$folders[$key]['status'] = 0;
				}
			}

			// and disable the classes that dont belong to folders
			foreach($existing as $key => $class) {

				if( !isset($class['parent']) || empty($class['parent'])) {
					$existing[$key]['parent'] = -1;
				}
			}

			$classes = array_merge($existing, $classes); // this will overwrite existing classes
			$newFolders = array_merge($folders, $newFolders); // this will overwrite any exisiting folder with the same name
		}

		update_option('ct_components_classes', $classes, get_option("oxygen_options_autoload"));

		global $oxygen_vsb_css_classes; // in order to have the latest classes available for generating cache
		$oxygen_vsb_css_classes = $classes;

		update_option('ct_style_folders', $newFolders);
	}
}

function ct_replace_source_color_callback($matches) {
	
	global $ct_source_color_map;

	if(is_array($ct_source_color_map) && sizeof($ct_source_color_map) > 0) {
		if(isset($ct_source_color_map[intval($matches[1])])) {
			return 'color('.$ct_source_color_map[intval($matches[1])].')';
		}
	}

	return $matches[0];
	
}

function ct_map_source_colors($settings) {
	// Sometimes we receive an empty string here
	if ( empty( $settings ) ) {
		return [];
	}
	foreach($settings as $key => $item) {
		if(is_string($item)) {
			$settings[$key] = preg_replace_callback('/color\((\d*)\)/', 'ct_replace_source_color_callback', $item); // replaced value
		}
		else if(is_array($item)) {
			$settings[$key] = ct_map_source_colors($settings[$key]);
		}
	}

	return $settings;

}

class CT_Create_Variable_Color_Callback {
	
	private $siteName, $lookupTable;

	function __construct($siteName, $lookupTable = array()) {
		$this->siteName = $siteName;
		$this->lookupTable = $lookupTable;
	}

	public function callback($matches) {

		global $oxygen_vsb_global_colors;
		
		$value =  strtolower($matches[0]);

		// find a set with that name
		$existingSetId = false;

		foreach($oxygen_vsb_global_colors['sets'] as $key => $set) {

			if($set['name'] == $this->siteName) {
				$existingSetId = $set['id'];
			}

			//remove hashkey attribute from the existing sets
			if(isset($oxygen_vsb_global_colors['sets'][$key]['$$hashKey']))
				unset($oxygen_vsb_global_colors['sets'][$key]['$$hashKey']);
		}

		// if this set does not already exist, create it
		if($existingSetId === false) {

			$existingSetId = ++$oxygen_vsb_global_colors['setsIncrement'];

			$oxygen_vsb_global_colors['sets'][] = array(
				'id' => $existingSetId, // and increment
				'name' => $this->siteName
			);

		}
		
		// remove hash keys from all existing colors
		foreach($oxygen_vsb_global_colors['colors'] as $key => $color) {
			if(isset($oxygen_vsb_global_colors['colors'][$key]['$$hashKey']))
				unset($oxygen_vsb_global_colors['colors'][$key]['$$hashKey']);
		}

		$nameFromLookupTable = false;

		if(isset($this->lookupTable[$value])) {
			$nameFromLookupTable = $this->lookupTable[$value];
		}

		if(isset($this->lookupTable[str_replace(' ', '', $value)])) { // an rgb or rgba value with/out spaces
			$nameFromLookupTable = $this->lookupTable[str_replace(' ', '', $value)];
		}

		if(strlen($value) === 4) { // then it is a 3 letter hex color, convert it into 6
			$value = '#'.str_repeat(substr($value, 1), 2);

			if(isset($this->lookupTable[$value])) {
				$nameFromLookupTable = $this->lookupTable[$value];
			}
		}
		
		$newColorId = false;
		$firstColor = false;
		// if a color with the same lookupTable name already exists in the same set, then get its id
		if($nameFromLookupTable !== false) {

			// find the first occurance of this name in the table
			$firstColor = array_search($nameFromLookupTable, $this->lookupTable);
			
			if($firstColor && strlen($firstColor) === 4) { // then it is a 3 letter hex color, convert it into 6
				$firstColor = '#'.str_repeat(substr($firstColor, 1), 2);
			}

			foreach($oxygen_vsb_global_colors['colors'] as $eKey => $eColor) {

				if($eColor['name'] == $nameFromLookupTable && $eColor['set'] === $existingSetId) {
					
					$newColorId = intval($eColor['id']);

					if($firstColor !== $value) { // then change the color to match with the first occurance with the same name in the lookup table
						$value = $firstColor;
						$oxygen_vsb_global_colors['colors'][$eKey]['value'] = $firstColor;
					}

					break;
				}

			}
		}

		// if a color with the same value already exists in the same set, then get its id
		if($newColorId === false) {
			foreach($oxygen_vsb_global_colors['colors'] as $eKey => $eColor) {

				if(isset($eColor['sourceVal']) && isset($eColor['set']) && strtolower($eColor['sourceVal']) == $value && $eColor['set'] === $existingSetId) {
					
					$newColorId = intval($eColor['id']);

					break;
				}

			}
		}

		

		// if the color does not exist then create a new one
		
		if($newColorId === false) {

			if($nameFromLookupTable == 'IGNORE') {
				return $value;
			}

			$color = array();

			// add a new ID for adjusting it into the existing colors array
			$color['id'] = ++$oxygen_vsb_global_colors['colorsIncrement'];

			$newColorId = $color['id'];


			$color['name'] = $nameFromLookupTable !== false ? $nameFromLookupTable : 'color #'.$color['id'];

			$color['value'] = $value; // this color value will be used to render the components

			$color['sourceVal'] = $value; // this will keep a record of the original color value that this name was assigned to

			// assign the new Parent
			$color['set'] = $existingSetId;

			// add the color to the existing colors;

			$oxygen_vsb_global_colors['colors'][] = $color;
			
		}

		return 'color('.$newColorId.')';

	}

}


function ct_create_variable_colors($settings, $siteName, $lookupTable = array()) {

	global $oxygen_vsb_global_colors;
	$callback = new CT_Create_Variable_Color_Callback($siteName, $lookupTable);

	foreach($settings as $key => $item) {
		if(is_string($item)) {
			$settings[$key] = preg_replace_callback('/#[0-9|a-f]{3,6}|rgba\([\d|\s]*\,[\d|\s]*\,[\d|\s]*\,[^\)]*\)/i', array($callback, 'callback'), $item); 
		}
		else if(is_array($item)) {
			$settings[$key] = ct_create_variable_colors($settings[$key], $siteName, $lookupTable);
		}
	}

	// reorder global colors according to the position of names in the lookup tables
	$sortedColors = array();
	$set = false;

	// find the set id
	foreach($oxygen_vsb_global_colors['sets'] as $key => $set) {
		if($set['name'] == $siteName) {
			$set = $set['id'];
		}
	}

	if(!$set) {
		return $settings;
	}
	if(is_array($lookupTable)) {
		foreach($lookupTable as $key => $name ) {
			// find index of a color in the colorsets where name is equal to $name and set is equal to $set
			foreach($oxygen_vsb_global_colors['colors'] as $colorKey => $color) {
				if($color['set'] == $set && $color['name'] == $name) {
					$sortedColors[] = $color;
					unset($oxygen_vsb_global_colors['colors'][$colorKey]);
				}
			}
		}
	}

	$oxygen_vsb_global_colors['colors'] = array_merge($sortedColors, $oxygen_vsb_global_colors['colors']);

	return $settings;
}

function ct_setup_default_settings($site, $delete = false, $accessKey = '') {

	$result = ct_new_api_remote_get($site, 'settings');

	$siteName = isset($_REQUEST['site'])?sanitize_text_field($_REQUEST['site']):'defaultIncoming';

	if($result) {

		$settings = json_decode($result, true);
		
		if(!is_array($settings)) {
			return;
		}

		// map colors
		global $ct_source_color_map;
		global $oxygen_vsb_classic_designsets;

		$ct_source_color_map = get_transient('oxygen_vsb_source_color_map', array());

		if(sizeof($ct_source_color_map) > 0) {
			// replace all global color values to match with the imported ones
			$settings = ct_map_source_colors($settings);

		}

		if(in_array($siteName, $oxygen_vsb_classic_designsets)) { // if it is an old design set
			// generate new variable colors
			global $oxygen_vsb_global_colors;

			// if a lookup table is avaibale?
			$lookupTable = get_transient('oxygen_vsb_source_color_lookup_table', array());

			$settings = ct_create_variable_colors($settings, $siteName, $lookupTable);
			update_option('oxygen_vsb_global_colors', $oxygen_vsb_global_colors);
		}

		// append to existing style sheets
		$existing = get_option('ct_global_settings', array());

		if(is_array($existing) && !$delete) {
			$settings = array_merge($existing, $settings);
		}

		update_option('ct_global_settings', $settings);
	}
}

function ct_setup_default_stylesets($site, $delete = false, $accessKey = '') {

	$result = ct_new_api_remote_get($site, 'stylesets');

	if($result) {

		// given that the classes were processed earlier, the folder structure should already be in place

		// process the incoming sets and re-assign parent


		$stylesets = json_decode($result, true);

		if(!is_array($stylesets)) {
			return;
		}

		$folderName = isset($_REQUEST['site'])?sanitize_text_field($_REQUEST['site']):'defaultIncoming';

		//$folderName = $site;

		foreach($stylesets as $key => $set) {
			$stylesets[$key]['parent'] = $folderName;
		}

		// append to existing style sheets
		$existing = get_option('ct_style_sets', array());

		// if there is an incoming 'uncategorized style set', remove it
		
		if(isset($stylesets['Uncategorized Custom Selectors'])) {
			unset($stylesets['Uncategorized Custom Selectors']);
		}
		
		//$stylesets[(isset($_REQUEST['site'])?sanitize_text_field($_REQUEST['site']):'defaultIncoming').' style set'] = $incoming_uncategorized;

		// have all the custom selectors 
		
		if(is_array($existing) && !$delete) {

			// the existing folders are already deleted while processing the classes

			foreach($existing as $key => $set) {

				if( !isset($set['parent']) || empty($set['parent'])) {
					$existing[$key]['parent'] = -1;
				}
			}

			$stylesets = array_merge($existing, $stylesets);
		}

		update_option('ct_style_sets', $stylesets);
	}
}

function ct_setup_default_stylesheets($site, $delete = false, $accessKey = '') {

	$result = ct_new_api_remote_get($site, 'stylesheets');

	$folderName = isset($_REQUEST['site'])?sanitize_text_field($_REQUEST['site']):'defaultIncoming';

	if($result) {

		$stylesheets = json_decode($result, true);

		if(!is_array($stylesheets)) {
			return;
		}

		if(isset($stylesheets['error'])) {
			return $stylesheets;
		}

		// all the incoming stylesheets should go under a folder with the name of the source site

		// append to existing style sheets
		$existing = get_option('ct_style_sheets', array());

		// find the topmost ID value in the existing stylesheets.
		$id = 0;
		$folder_id = false;
		// lets assume that this is not the old data, for now
		foreach($existing as $key => $value) {
			if(is_array($value) && isset($value['id']) && intval($value['id']) > $id) {
				$id = intval($value['id']);
			}

			// in the process also look for a folder that has the name same as $folderName
			// if such a folder already exist, grab its ID. 
			if(isset($value['folder']) && intval($value['folder']) === 1  && $value['name'] === $folderName) {
				$folder_id = $value['id'];
			}
		}
		

		$processedStylesheets = array();

		// if a folder with the name of the $folderName does not exist
		// create a new folder in the incoming data, with the name of the source site.

		if($folder_id === false) {
			$folder_id = ++$id;
			$processedStylesheets[] = array( 'id' => $folder_id, 'name' => $folderName, 'folder' => 1, 'status' => 1 );
		}

		//convert old style data and assign the new ID's
		foreach($stylesheets as $key => $value) {
			if(!is_array($value)) { // if it is the old style sheets data

				$processedStylesheets[] = array( 'id' => ++$id, 'name' => $key, 'css' => $value, 'parent' => $folder_id, 'status' => 1 );

			} else {

				// if it is not a folder
				if(!isset($value['folder']) || intval($value['folder']) !== 1) {
					$value['id'] = ++$id; // replace the id in the new style data as well
					$value['parent'] = $folder_id; // make it the child of the new parent

					$processedStylesheets[] = $value;
				}
				
			}
		}

		// now if we are keeping the existing data
		if(is_array($existing) && !$delete) {
			// disable all existing folders
			foreach($existing as $key => $item) {
				if(isset($item['folder']) && intval($item['folder']) === 1) {
					// unless the folder has the same name as that of $folderName
					if($item['name'] !== $folderName) {
						$existing[$key]['status'] = 0;
					}
				}
				else { // if it is a stylesheet

					// if an incoming stylsheet has the same name as this one? delete this
					$remove = false;

					foreach($processedStylesheets as $incoming) {
						
						if(!$remove && (!isset($incoming['folder']) || intval($incoming['folder']) === 0) && $incoming['name'] === $item['name']) {
							
							$remove = true;
						}
					}

					if($remove) {
						unset($existing[$key]);
					} // else if it does not belong to a folder
					elseif(!isset($item['parent']) || intval($item['parent']) === 0) {

						$existing[$key]['parent'] = -1; // disable it
						
					}
				}
			}

			$processedStylesheets = array_merge($existing, $processedStylesheets);

		}

		update_option('ct_style_sheets', $processedStylesheets, get_option("oxygen_options_autoload"));
	}

	return true;
}

function ct_swap_reusable_view_ids($shortcodes, $new_id_map) {

	if(is_array($shortcodes)) {
		foreach($shortcodes as $key => $shortcode) {

			if($shortcode['name'] == 'ct_reusable') {
				$shortcodes[$key]['options']['view_id'] = $new_id_map[$shortcode['options']['view_id']];
			}

			if(isset($shortcode['children']) && is_array($shortcode['children'])) {
				$shortcodes[$key]['children'] = ct_swap_reusable_view_ids($shortcodes[$key]['children'], $new_id_map);
			}
		}
	}

	return $shortcodes;
}

function ct_setup_default_templates($site, $delete = false, $accessKey = '') {
	
	$result = ct_new_api_remote_get($site, 'templates');

	if($result) {

			$templates = json_decode($result, true);

			if(!is_array($templates)) {
				return;
			}

			global $wpdb;
			
			// take care of the existing templates
			$existing = $wpdb->get_results(
				    "SELECT id, post_title
				    FROM $wpdb->posts as post
				    WHERE post_type = 'ct_template'
				    AND post.post_status IN ('publish')"
				);

			foreach($existing as $template) {
				if($delete) {
					wp_delete_post($template->id);
				}
				else {
					// unset the template
					delete_post_meta($template->id, 'ct_template_single_all');
					delete_post_meta($template->id, 'ct_template_post_types');
					delete_post_meta($template->id, 'ct_use_template_taxonomies');
					delete_post_meta($template->id, 'ct_template_apply_if_post_of_parents');

					delete_post_meta($template->id, 'ct_template_all_archives');
					delete_post_meta($template->id, 'ct_template_apply_if_archive_among_taxonomies');
					delete_post_meta($template->id, 'ct_template_apply_if_archive_among_cpt');
					delete_post_meta($template->id, 'ct_template_apply_if_archive_among_authors');
					delete_post_meta($template->id, 'ct_template_date_archive');

					delete_post_meta($template->id, 'ct_template_front_page');
					delete_post_meta($template->id, 'ct_template_blog_posts');
					delete_post_meta($template->id, 'ct_template_search_page');
					delete_post_meta($template->id, 'ct_template_404_page');
					delete_post_meta($template->id, 'ct_template_index');

					// and rename
					if(strpos($template->post_title, 'inactive - ') === false) {

						wp_update_post(array(
							'ID' => $template->id,
							'post_title' => 'inactive - '.$template->post_title
						));

					}


				}
			}

		$new_id_map = array();

		// save all class names to the transient. These will be used in the last step to download selective classes
		$selectiveClasses = array();

		$current_user = wp_get_current_user();

		// insert posts
		foreach($templates as $template) {
			
			$post_data = array(
				'ID' => 0,
				'post_title' => $template['post_title'],
				'post_type' => 'ct_template',
				'post_status' => 'publish'
			);
		
			if($current_user && isset($current_user->ID)) {
				$post_data['post_author'] = $current_user->ID;
			}

			$new_id_map[$template['ID']] = wp_insert_post($post_data);
			
			foreach($template['applied_classes'] as $key => $val) {
				$selectiveClasses[$key] = $val;			
			}		

		}
		
		set_transient('oxygen-vsb-default-setup-classes', $selectiveClasses);

		set_transient('oxygen-vsb-templates-id-map', $new_id_map);

		global $ct_source_color_map;

		$ct_source_color_map = get_transient('oxygen_vsb_source_color_map', array());

		$siteName = isset($_REQUEST['site'])?sanitize_text_field($_REQUEST['site']):'defaultIncoming';
		global $oxygen_vsb_classic_designsets;
		global $oxygen_vsb_global_colors;
		// update post meta
		foreach($templates as $template) {

			// TODO run through the shortcodes, if some re-usable in place, replace its id from the $new_id_map
			$shortcodes = parse_shortcodes($template['builder_shortcodes'], false, false);

			$shortcodes['content'] = ct_swap_reusable_view_ids( $shortcodes['content'], $new_id_map );

			// map colors
			if(sizeof($ct_source_color_map) > 0) {
				// replace all global color values to match with the imported ones
				$shortcodes['content'] = ct_map_source_colors($shortcodes['content']);
			}

			if(in_array($siteName, $oxygen_vsb_classic_designsets)) { // if it is an old design set
				// generate new variable colors
				// if a lookup table is avaibale?
				$lookupTable = get_transient('oxygen_vsb_source_color_lookup_table', array());
				$shortcodes['content'] = ct_create_variable_colors($shortcodes['content'], $siteName, $lookupTable);
			}

			$wrap_shortcodes = array();

			$wrap_shortcodes['children'] = $shortcodes['content'];
			// code tree back to JSON to pass into old function
			$components_tree_json = json_encode( $wrap_shortcodes );
			
			ob_start();
		
			// transform JSON to shortcodes
			$shortcodes = components_json_to_shortcodes( $components_tree_json );
		
			// we don't need anything to be output by custom shortcodes
			ob_clean();

			update_post_meta($new_id_map[$template['ID']], 'ct_builder_shortcodes', $shortcodes);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_type', $template['template_type']);
			$wrap_shortcodes['children'] = ct_base64_encode_decode_tree($wrap_shortcodes['children'], true);
			
			update_post_meta($new_id_map[$template['ID']], 'ct_builder_json', addslashes(json_encode(
				array(
					'ct_id' => 0,
					'name' => 'root',
					'depth' => 0,
					'children' => $wrap_shortcodes['children'],
				)))
			);

			// cache styles
			oxygen_vsb_cache_page_css($new_id_map[$template['ID']], $shortcodes);

			if(isset($template['template_type']) && $template['template_type'] == 'reusable_part') { // store the source parameters to check for redundancy while importing re-usables again
				update_post_meta($new_id_map[$template['ID']], 'ct_source_site', $site);
				update_post_meta($new_id_map[$template['ID']], 'ct_source_post', $template['ID']);
			}

			update_post_meta($new_id_map[$template['ID']], 'ct_template_order', $template['template_order']);
			if (isset($template['parent_template']) && $template['parent_template']) {
				update_post_meta($new_id_map[$template['ID']], 'ct_parent_template', $new_id_map[$template['parent_template']]);
			}

			update_post_meta($new_id_map[$template['ID']], 'ct_template_single_all', $template['template_single_all']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_post_types', $template['template_post_types']);
			update_post_meta($new_id_map[$template['ID']], 'ct_use_template_taxonomies', $template['use_template_taxonomies']);
			
			// match id to slug for each taxonomy
			if(isset($template['template_taxonomies']) && is_array($template['template_taxonomies'])) {
				foreach($template['template_taxonomies']['values'] as $key => $val) {
					// get id for the slug
					$term = get_term_by('slug', $val, $template['template_taxonomies']['names'][$key]);
					
					if($term) {
						$template['template_taxonomies']['values'][$key] = $term->term_id;
					}
					else {
						if(isset($template['template_taxonomies'])) {
							unset($template['template_taxonomies']['names'][$key]);
							unset($template['template_taxonomies']['values'][$key]);
						}
					}

				}
			}
			else {
				$template['template_taxonomies'] = array();
			}

			update_post_meta($new_id_map[$template['ID']], 'ct_template_taxonomies', $template['template_taxonomies']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_apply_if_post_of_parents', $template['template_apply_if_post_of_parents']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_post_of_parents', $template['template_post_of_parents']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_all_archives', $template['template_all_archives']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_apply_if_archive_among_taxonomies', $template['template_apply_if_archive_among_taxonomies']);

			// match id to slug for each taxonomy
			if(isset($template['template_archive_among_taxonomies']) && is_array($template['template_archive_among_taxonomies'])) {
				foreach($template['template_archive_among_taxonomies'] as $key => $val) {
					// get id for the slug
					if(is_array($val)) {
						$term = get_term_by('slug', $val['slug'], $val['taxonomy']);	
						if($term) {
							$template['template_archive_among_taxonomies'][$key] = $term->term_id;
						}
						else {
							unset($template['template_archive_among_taxonomies'][$key]);
						}
					}

				}
			} else {
				$template['template_archive_among_taxonomies'] = array();
			}
			
			update_post_meta($new_id_map[$template['ID']], 'ct_template_archive_among_taxonomies', $template['template_archive_among_taxonomies']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_apply_if_archive_among_cpt', $template['template_apply_if_archive_among_cpt']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_archive_post_types', $template['template_archive_post_types']);
			// update_post_meta($new_id_map[$template['ID']], 'ct_template_apply_if_archive_among_authors', $template['template_apply_if_archive_among_authors']);
			// update_post_meta($new_id_map[$template['ID']], 'ct_template_authors_archives', $template['template_authors_archives']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_date_archive', $template['template_date_archive']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_front_page', $template['template_front_page']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_blog_posts', $template['template_blog_posts']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_search_page', $template['template_search_page']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_404_page', $template['template_404_page']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_index', $template['template_index']);

			update_post_meta($new_id_map[$template['ID']], 'ct_template_inner_content', $template['ct_template_inner_content']);
			
		}

		if(in_array($siteName, $oxygen_vsb_classic_designsets)) { // then new color variables must have been created
			update_option('oxygen_vsb_global_colors', $oxygen_vsb_global_colors);
		}
	}
}

/**
 * Fallback to render Easy Posts when nothing to preview
 * Editing something here also edit it in Class!
 * 
 * @since 2.0
 */

function oxy_render_easy_posts_by_ajax() {
	
	oxygen_vsb_ajax_request_header_check();

	$component_json = file_get_contents('php://input');
	$component 		= json_decode( $component_json, true );
	$options 		= $component['options']['original'];

	global $oxygen_signature;

	$shortcode_atts = array(
		'preview' => 'true',
		'ct_options' => "{\"selector\":\"{$component['options']['selector']}\",\"original\":{\"code-php\":\"".base64_encode($options['code-php'])."\",\"code-css\":\"".base64_encode($options['code-css'])."\",\"posts_per_page\":\"{$options['posts_per_page']}\",\"query_post_ids\":\"{$options['query_post_ids']}\",\"wp_query\":\"{$options['wp_query']}\",\"query_order_by\":\"{$options['query_order_by']}\",\"query_count\":\"{$options['query_count']}\",\"query_all_posts\":\"{$options['query_all_posts']}\",\"query_ignore_sticky_posts\":\"{$options['query_ignore_sticky_posts']}\",\"query_order\":\"{$options['query_order']}\",\"query_args\":\"{$options['query_args']}\",\"query_post_types\":".json_encode($options['query_post_types'], JSON_FORCE_OBJECT).",\"query_taxonomies_any\":".json_encode($options['query_taxonomies_any'], JSON_FORCE_OBJECT).",\"query_taxonomies_all\":".json_encode($options['query_taxonomies_all'], JSON_FORCE_OBJECT).",\"query_authors\":".json_encode($options['query_authors'], JSON_FORCE_OBJECT)."}}",
	);

	// Generate signature
	$signature = $oxygen_signature->generate_signature_shortcode_string( 'oxy_posts_grid', $shortcode_atts, '');
	// Generate output
	$shortcode = "[oxy_posts_grid {$signature} preview=true ct_options='{$shortcode_atts['ct_options']}']";

	echo do_shortcode($shortcode);

	die();
}
add_action('wp_ajax_oxy_render_easy_posts', 'oxy_render_easy_posts_by_ajax');


/**
 * Fallback to render Easy Posts when nothing to preview
 * Editing something here also edit it in Class!
 * 
 * @since 2.0
 */

function oxy_render_comments_ajax() {

	oxygen_vsb_ajax_request_header_check();

	$component_json = file_get_contents('php://input');
	$component 		= json_decode( $component_json, true );
	$options 		= $component['options']['original'];

	global $oxygen_signature;

	$shortcode_atts = array(
		'preview' => 'true',
		'ct_options' => "{\"original\":{\"code-php\":\"".base64_encode($options['code-php'])."\",\"code-css\":\"".base64_encode($options['code-css'])."\"}}",
	);

	// Generate signature
	$signature = $oxygen_signature->generate_signature_shortcode_string( 'oxy_comments', $shortcode_atts, '');
	// Generate output
	$shortcode = "[oxy_comments {$signature} preview=true ct_options='{$shortcode_atts['ct_options']}']";

	echo do_shortcode($shortcode);
	
	die();
}
add_action('wp_ajax_oxy_render_comments_list', 'oxy_render_comments_ajax');


/**
 * Fallback to render Comment Form when nothing to preview
 * Editing something here also edit it in Class!
 * 
 * @since 2.0
 */

function oxy_render_comments_form_by_ajax() {

	oxygen_vsb_ajax_request_header_check();

	global $oxygen_signature;

	$shortcode_atts = array(
		'preview' => 'true',
		'ct_options' => '{}'
	);

	// Generate signature
	$signature = $oxygen_signature->generate_signature_shortcode_string( 'oxy_comment_form', $shortcode_atts, '');
	// Generate output
	$shortcode = "[oxy_comment_form {$signature} preview=true ct_options='{$shortcode_atts['ct_options']}']";

	echo do_shortcode($shortcode);

	die();
}
add_action('wp_ajax_oxy_render_comment_form', 'oxy_render_comments_form_by_ajax');


/**
 * Fallback to render Login Form when nothing to preview
 * Editing something here also edit it in Class!
 * 
 * @since 2.0
 */

function oxy_render_login_form_by_ajax() {

	oxygen_vsb_ajax_request_header_check();

	global $oxygen_signature;

	$shortcode_atts = array(
		'preview' => 'true',
		'ct_options' => '{}'
	);

	// Generate signature
	$signature = $oxygen_signature->generate_signature_shortcode_string( 'oxy_login_form', $shortcode_atts, '');
	// Generate output
	$shortcode = "[oxy_login_form {$signature} preview=true ct_options='{$shortcode_atts['ct_options']}']";

	echo do_shortcode($shortcode);
	
	die();
}
add_action('wp_ajax_oxy_render_login_form', 'oxy_render_login_form_by_ajax');


/**
 * Fallback to render Search Form when nothing to preview
 * Editing something here also edit it in Class!
 * 
 * @since 2.0
 */

function oxy_render_search_form_by_ajax() {

	oxygen_vsb_ajax_request_header_check();

	global $oxygen_signature;

	$shortcode_atts = array(
		'preview' => 'true',
		'ct_options' => '{}'
	);

	// Generate signature
	$signature = $oxygen_signature->generate_signature_shortcode_string( 'oxy_search_form', $shortcode_atts, '');
	// Generate output
	$shortcode = "[oxy_search_form {$signature} preview=true ct_options='{$shortcode_atts['ct_options']}']";

	echo do_shortcode($shortcode);
	
	die();
}
add_action('wp_ajax_oxy_render_search_form', 'oxy_render_search_form_by_ajax');


/**
 * Fallback to render Nav menu when nothing to preview
 * Editing something here also edit it in Class!
 * 
 * @since 2.0
 */

function oxy_render_nav_menu_by_ajax() {

	oxygen_vsb_ajax_request_header_check();

	$component_json = file_get_contents('php://input');
	$component 		= json_decode( $component_json, true );
	$options 		= $component['options']['original'];

	?>

	<div class='oxy-menu-toggle'>
		<div class='oxy-nav-menu-hamburger-wrap'>
			<div class='oxy-nav-menu-hamburger'>
				<div class='oxy-nav-menu-hamburger-line'></div>
				<div class='oxy-nav-menu-hamburger-line'></div>
				<div class='oxy-nav-menu-hamburger-line'></div>
			</div>
		</div>
	</div>

	<?php $menu = wp_nav_menu( array(
			"menu" 			=> $options["menu_id"],
			"depth" 		=>  ( $options["dropdowns"] == "on" ) ? 0 : 1,
			"menu_class" 	=> "oxy-nav-menu-list",
			"fallback_cb" 	=> false,
			"echo" 			=> false
		) );

	if ($menu!==false) :
	
	echo $menu;

	else : ?>

	<div class="menu-example-menu-container"><ul id="menu-example-menu" class="oxy-nav-menu-list"><li id="menu-item-12" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-12"><a href="#">Example Menu</a></li>
		<li id="menu-item-13" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-13"><a href="#">Link One</a></li>
		<li id="menu-item-14" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-14"><a href="#">Link Two</a>
		<?php if ( $options["dropdowns"] == "on" ) : ?>
		<ul class="sub-menu">
			<li id="menu-item-15" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-15"><a href="#">Dropdown Link One</a></li>
			<li id="menu-item-17" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-17"><a href="#">Dropdown Link Two</a></li>
		</ul>
		<?php endif; ?>
		</li>
		<li id="menu-item-16" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-16"><a href="#">Link Three</a></li>
	</ul></div>

	<?php endif;
	
	die();
}
add_action('wp_ajax_oxy_render_nav_menu', 'oxy_render_nav_menu_by_ajax');


/**
 * Fallback to render Gallery when nothing to preview
 * Editing something here also edit it in Class!
 * 
 * @since 2.0
 */

function oxy_render_gallery_by_ajax() {

	oxygen_vsb_ajax_request_header_check();

	$component_json = file_get_contents('php://input');
	$component 		= json_decode( $component_json, true );
	$options 		= $component['options']['original'];

	$options['preview'] = true;

	global $oxygen_vsb_components;
	$oxygen_vsb_components['gallery']->shortcode($options);

	die();
}
add_action('wp_ajax_oxy_render_gallery', 'oxy_render_gallery_by_ajax');


/**
 * Check security of AJAX request and output proper header
 * 
 * @since 2.0
 */

function oxygen_vsb_ajax_request_header_check() {

	header('Content-Type: text/html');

	if (!is_user_logged_in() || !oxygen_vsb_current_user_can_access()) {
		die( 'Security check' );
	}

	$nonce  	= $_REQUEST['nonce'];
	$post_id 	= $_REQUEST['post_id'];

	// check nonce
	if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	    die( 'Security check' ); 
	}

	// check user role
	if ( ! oxygen_vsb_current_user_can_access() ) {
		die ( 'Security check' );
	}

}

function oxygen_vsb_sign_shortcodes($type) {
	
	$response = array('messages' => array(), 'children' => array());
	
	$page_ids = isset($_REQUEST['page_ids']) ? $_REQUEST['page_ids'] : false;
	if (!$page_ids) {
		$pages = get_posts(
			array(
				'post_type' => array($type), 
				'numberposts' => -1,
				'orderby' => 'ID',
				'order' => 'ASC',
				'meta_key' => 'ct_builder_shortcodes',
			)
		);
		$page_ids = array_map(function($page){
			return $page->ID;
		}, $pages);
	}

	if(sizeof($page_ids) > 0) {

		// get index from request, if not available set it to zero
		$index = isset($_REQUEST['index'])?intval($_REQUEST['index']): 0;
		$page_id = $page_ids[$index];
		
		// get the shortcodes of the page
		$shortcodes = get_post_meta($page_id, 'ct_builder_shortcodes', true);
		

		if($shortcodes) {

			$not_registered_shortcodes = oxygen_has_not_registered_shortcodes($shortcodes);
			if ($not_registered_shortcodes) {
				$response['messages'][] = 'Inactive Shortcodes Present: "'.implode(', ', $not_registered_shortcodes).'" on post type "'.$type.'" with ID = '.$page->ID.' - Activate Add-Ons Before Re-Signing.';
			}
			else {
				$response['messages'][] = 'Signing shortcodes on post type "'.$type.'" with ID = '.$page_id;
				// parse without verifying signature, as these might not have any signature
				$tree = parse_shortcodes($shortcodes, false, false);
				
				//save again and re-sign in the process
				$shortcodes = parse_components_tree($tree['content']);

				update_post_meta($page_id, 'ct_builder_shortcodes', $shortcodes);
				$tree['content'] = ct_base64_encode_decode_tree($tree['content'], true);
				// create JSON if no present
				$json = get_post_meta($page_id, 'ct_builder_json', true);
				if (!$json) {
					update_post_meta($page_id, 'ct_builder_json', addslashes(json_encode(
						array(
							'id' => 0,
							'name' => 'root',
							'depth' => 0,
							'children' => $tree['content']
							), JSON_UNESCAPED_UNICODE
					)));
				}
			}

		}
		else {
			$response['messages'][] = 'No shortcodes found on post of type "'.$type.'" with ID = '.$page_id;
		}

		if($index < sizeof($page_ids) - 1) {
			$index++;
			$response['index'] = $index;
			$response['page_ids'] = $page_ids;
		}
	}
	else {
		$response['messages'][] = 'No post of type "'.$type.'" found';
	}

	return $response;
}

add_action('wp_ajax_oxygen_vsb_signing_process', 'oxygen_vsb_signing_process');

function oxygen_vsb_signing_process() {

	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'oxygen_vsb_sign_shortcodes' )) {
	    die( 'Security check' );
	}

	if (!is_user_logged_in() || !current_user_can( 'manage_options' )) {
		die( 'Security check' );
	}

	// check user role
	if ( ! current_user_can( 'edit_posts' ) ) {
		die ( 'Security check' );
	}

	// step 0 = global checks, 1 = shortcode checks, 2 = ID class active checks
	$step = isset($_REQUEST['step'])?intval($_REQUEST['step']): 1;

	$postTypeKeys = isset($_REQUEST['postTypes']) && is_array($_REQUEST['postTypes'])?array_map('sanitize_text_field', $_REQUEST['postTypes']): array();
	
	$response = array();

	if($step <= sizeof($postTypeKeys)) {

		$postType = $postTypeKeys[$step-1];

		$response = oxygen_vsb_sign_shortcodes($postType);
		
		if(isset($response['index'])) {
			$response['step'] = $step; // if we are still looping through the same post type
		}
		else {
			$response['step'] = $step+1; // move to next post type
		}

		if($response['step'] > sizeof($postTypeKeys)) { // thats the end of it
			$response['step'] = 9999; // just a signal for the client side to stop requesting
		}

	}
	else {
		$response['step'] = 9999; // just a signal for the client side to stop requesting
	}

	if ( $response['step'] == 9999 ) {
		// all shortcodes bineg converted to JSON
		add_option("oxygen_vsb_update_4_0_shortcodes_signed", true, '', 'no');
	}
	
	header('Content-Type: application/json');
	
	echo json_encode($response);

	die();
}


/**
 * Get cached list of Google fonts
 * 
 * @since 2.2
 * @author Ilya K.
 */

function oxygen_vsb_get_google_fonts_cache() {

	oxygen_vsb_ajax_request_header_check();

	$google_fonts_cache = get_option("oxygen_vsb_google_fonts_cache", false);

	// echo JSON
	$output = json_encode( $google_fonts_cache );

	// cache for 24 hours
	header('cache-control: max-age=86400');
	header('content-type: application/json; charset=UTF-8');

	echo $output;

	die();
}
add_action('wp_ajax_oxy_get_google_fonts', 'oxygen_vsb_get_google_fonts_cache');


/**
 * Return all Elements Presets
 * 
 * @since 3.4
 * @author Ilya K.
 */

function oxy_load_elements_presets() {

	//delete_option("oxygen_vsb_element_presets");

	oxygen_vsb_sync_default_presets();

	$defaults = apply_filters("oxygen_vsb_element_presets_defaults", array());
	$element_presets = get_option("oxygen_vsb_element_presets", $defaults);

	$output = json_encode( $element_presets );

	echo $output;

	die();
}
add_action('wp_ajax_oxy_load_elements_presets', 'oxy_load_elements_presets');


/**
 * AJAX Callback that set a transient to mark the post is currently editing in Oxygen builder
 * 
 * @since 3.3
 * @author Ilya K.
 */

function set_oxygen_edit_post_lock_transient() {

	oxygen_vsb_ajax_request_header_check();

	$post_id = intval( $_REQUEST['post_id'] );

	set_transient("oxygen_post_edit_lock_".$post_id, true, 15/*seconds*/);
	set_transient("oxygen_post_edit_lock", true, 15/*seconds*/);

	echo "transient set for post: " . $post_id;
	
	die();
}
add_action('wp_ajax_set_oxygen_edit_post_lock_transient', 'set_oxygen_edit_post_lock_transient');


/**
 * AJAX Callback to force transient unset and mark post as not editing in Oxygen builder
 * 
 * @since 3.3
 * @author Ilya K.
 */

function unset_oxygen_edit_post_lock_transient() {

	oxygen_vsb_ajax_request_header_check();
	
	$post_id = intval( $_REQUEST['post_id'] );

	delete_transient("oxygen_post_edit_lock_".$post_id);
	delete_transient("oxygen_post_edit_lock");

	echo "transient unset for post: " . $post_id;
	
	die();
}
add_action('wp_ajax_unset_oxygen_edit_post_lock_transient', 'unset_oxygen_edit_post_lock_transient');


/**
 * Common function that check if post is currently editing in Oxygen builder
 * 
 * @since 3.3
 * @author Ilya K.
 */

function is_oxygen_edit_post_locked($post_id=false) {
	
	if ( $post_id !== false ) {
		$transient = get_transient("oxygen_post_edit_lock_".$post_id);
	}
	else {
		$transient = get_transient("oxygen_post_edit_lock");
	}

	return ($transient) ? true : false;

}

function oxygen_render_innercontent() {
	echo "0";
	die();
}
add_action('wp_ajax_ct_render_innercontent', 'oxygen_render_innercontent');


function delete_all_oxygen_revisions() {
	
	$nonce = $_REQUEST['nonce'];

	if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-revisions' ) ) {
		die( __( 'Security check', 'oxygen' ) ); 
	} 

	global $wpdb;

	$wpdb->query( 
		$wpdb->prepare( 
			"DELETE FROM $wpdb->postmeta WHERE meta_key = %s",
			'ct_builder_shortcodes_revisions_dates'
		)
	);

	$deleted_rows = $wpdb->query( 
		$wpdb->prepare( 
			"DELETE FROM $wpdb->postmeta WHERE meta_key = %s",
			'ct_builder_shortcodes_revisions'
		)
	);

	echo "$deleted_rows revision(s) deleted successfully!";

	die;

}
add_action('wp_ajax_delete_all_oxygen_revisions', 'delete_all_oxygen_revisions');

function oxygen_set_codemirror_theme() {

	wp_verify_nonce( $_REQUEST['nonce'], "oxygen-nonce-set-codemirror-theme" );
	
	$theme = $_REQUEST['theme'];
	$result = update_option("oxygen_vsb_codemirror_theme", $theme);

	echo $result;

	die;
}
add_action('wp_ajax_oxygen_set_codemirror_theme', 'oxygen_set_codemirror_theme');