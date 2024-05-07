<?php


Class CT_Image extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// Add shortcode
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_basics_components_visual", array( $this, "component_button" ) );

		// Increase default 1600 max width for image sizes in srcset attribute.
		add_filter( 'max_srcset_image_width', array( $this, 'max_srcset_image_width' ) );
		
		add_action( 'template_redirect', 				array($this, 'get_attachment_sizes') );
		add_action( 'wp_ajax_ct_get_attachment_sizes',  array($this, 'get_attachment_sizes') );

	}


	/**
	 * Add a [ct_image] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content, $name ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );
		$lazy = $options['lazy'] ? 'loading="' . $options['lazy'] . '"' : null;
		
		ob_start();

		// Run shortcodes in the 'alt' option, because it is base64 encoded, so the set_options() function above won't detect any shortcode on it.
		$options['alt'] = do_shortcode( oxygen_base64_decode_for_json( $options['alt'] ) );

		if( $options['image_type'] == 1 || 
		    // default is 2, but if no ID specified we need a placeholder
		    ($options['image_type'] == 2 && !$options['attachment_id'] ) 
			)  
		{
			$image_src = $options['src'];
			$image_alt = $options['alt'];
			if( class_exists( 'Oxygen_Gutenberg' ) ) {
                $image_src = Oxygen_Gutenberg::decorate_attribute($options, $image_src, 'image');
                $image_alt = Oxygen_Gutenberg::decorate_attribute($options, $image_alt, 'alt');
            }
            
			echo '<img ' . $lazy . ' id="' . esc_attr($options['selector']) . '" alt="' . $image_alt . '" src="' . $image_src . '" class="' . esc_attr($options['classes']) . '"'; do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); echo '/>';
		} else {
            $attachment_id = intval(do_shortcode($options['attachment_id']));

            if ($attachment_id > 0) {
                $image_alt = $options['alt'];
                $attachment_size = isset($options['attachment_size']) ? $options['attachment_size'] : 'thumbnail';
                $source = wp_get_attachment_image_src($attachment_id, $attachment_size);

                if (is_array($source)) {
                    list($image_src, $image_width, $image_height) = $source;

                    // if (class_exists('Oxygen_Gutenberg')) {
                    //     $image_src = Oxygen_Gutenberg::decorate_attribute($options, $image_src, 'image');
                    // }

                    // Always pull alt text from meta data when using media library, but not if a custom alt attribute has been defined

					// Filter custom attributes to see if alt is defined there
					if( isset( $options['custom_attributes'] ) ) {
						$custom_attributes_filtered = array_filter($options['custom_attributes'], function($attr) {
							return $attr['name'] == 'alt';
						} );
					}

					if( empty($custom_attributes_filtered) ) {
						// If alt is not defined in custom attributes, use alt from media library.
						$image_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
					} else {
						// If alt is defined in custom attributes, get its value. If it's defined multiple times it will use the first one.
						// Wrap in do_shortcode to handle dynamic data.
						$image_alt = do_shortcode( reset( $custom_attributes_filtered )['value'] );
					}

                    // Remove image sources son SRCSET with bigger width than the image size selected
                    add_filter('wp_calculate_image_srcset', array($this, 'remove_bigger_srcset_sources'), 10, 5);

                    $srcset = wp_get_attachment_image_srcset($attachment_id, $attachment_size);

                    // Remove our filter so it doesn't affect 3rd party plugins
                    remove_filter('wp_calculate_image_srcset', array($this, 'remove_bigger_srcset_sources'), 10);

                    echo '<img ' . $lazy . ' id="' . esc_attr($options['selector']) . '" alt="' . esc_attr($image_alt) . '" src="' . esc_attr($image_src) . '" class="' . esc_attr($options['classes']) . '" srcset="' . $srcset . '" sizes="(max-width: '.$image_width.'px) 100vw, '.$image_width.'px" '; do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); echo '/>';
                }
            }
        }

		return ob_get_clean();
	}

	function max_srcset_image_width( ) {
		// Set max width to 8K resolution
		return 7680;
	}

	function remove_bigger_srcset_sources( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
		foreach ( $sources as $width => $data ) {
			if( $width > $size_array[0] ) {
				unset( $sources[ $width ] );
			}
		}
		return $sources;
	}

	function get_attachment_sizes() {

		if ( isset($_REQUEST['action']) && $_REQUEST['action'] == "ct_get_attachment_sizes") { 
			$nonce  	= $_REQUEST['nonce'];
			$post_id 	= intval( $_REQUEST['oxy_post_id'] );
			$attachment_id 	= stripslashes($_REQUEST['oxy_attachment_id']);

			// check nonce
			if ( ! isset( $nonce, $post_id ) || ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
				// This nonce is not valid.
				die( 'Security check' );
			}

			// check user role
			if ( ! oxygen_vsb_current_user_can_access() ) {
				die ( 'Security check' );
			}

			$options = file_get_contents('php://input');
			$options = json_decode( $options, true );
			$old_query = false;

			if ( $options ) {

				global $oxy_vsb_use_query, $oxygen_vsb_components;

				if($oxy_vsb_use_query) {
					$old_query = $oxy_vsb_use_query;
				}

				$query = $oxygen_vsb_components['repeater']->setQuery($options);
			
				$oxy_vsb_use_query = $query; //$this->query;
				
				if ($query->have_posts()) {
					$query->the_post();
				}
			
			}

			// sign "[oxygen]" shortcode on the fly
			if (stripos($attachment_id, '[oxygen') !== false) {
				$attachment_id = ct_sign_oxy_dynamic_shortcode(array($attachment_id));
			}

			$attachment_id = intval(do_shortcode($attachment_id));
			
			$args = array(
				'p'         => $attachment_id,
				'post_type' => 'attachment'
			);
			$image_query = new WP_Query($args);
			if( !$image_query->have_posts() || count( $image_query->posts ) != 1 ) {
				$json = json_encode( array("error" => __("Image not found", "oxygen")) );
			}
			else {
				$attachment = wp_prepare_attachment_for_js( $image_query->posts[0] );
				
				// full size doesn't exist in wp_get_attachment_metadata(), save it to add later
				$full = array();
				if ( isset( $attachment['sizes']['full'] ) ) {
					$full = array(
						'full' => $attachment['sizes']['full']
					);
				}
				
				// get sizes available for this particular attachment
				$meta_data = wp_get_attachment_metadata( $attachment_id );
				if ( isset( $meta_data['sizes'] ) && is_array( $meta_data['sizes'] ) ) {
					
					$attachment_url = wp_get_attachment_url( $attachment_id );
					$base_url       = str_replace( wp_basename( $attachment_url ), '', $attachment_url );

					// generate URLs from filenames (taken from wp_prepare_attachment_for_js())
					foreach ( $meta_data['sizes'] as $size_name => $size_data) {
						$meta_data['sizes'][$size_name]['url'] = $base_url . $size_data['file'];
					}
					
					$attachment['sizes'] = array_merge( $full, $meta_data['sizes'] );
				}
				$json = json_encode( $attachment['sizes'] );
			}

			// reset query to previous state
            if($old_query) {
                
                $oxy_vsb_use_query = $old_query;

                $oxy_vsb_use_query->reset_postdata();
            }

			header('Content-Type: application/json', true, 200);
			echo $json;
			die();
		}
	}
}

/**
 * Create Image Component Instance
 * 
 * @since 0.1.2
 */

global $oxygen_vsb_components;
$oxygen_vsb_components['image'] = new CT_Image ( 

		array( 
			'name' 		=> 'Image',
			'tag' 		=> 'ct_image',
			'params' 	=> array(
					array(
						"type" 			=> "radio",
						"heading" 		=> "",
						"param_name" 	=> "image_type",
						"value" 		=> array(
							2   	    => __("Media Library"),
							1 	        => __("Image URL")
						),
						"default"       => 1,
						"css"			=> false,
					),
					array(
						"type" 			=> "mediaurl",
						"heading" 		=> __("Image URL"),
						"param_name" 	=> "src",
						"value" 		=> "http://via.placeholder.com/1600x900",
						"condition"		=> "image_type=1",
						"css"			=> false
					),
					array(
						"type" 			=> "mediaurl",
						"heading" 		=> __("ID"),
						"param_name" 	=> "attachment_id",
						"value" 		=> "",
						"condition"		=> "image_type=2",
						"attachment"    => true,
						"css"			=> false,
						"dynamicdatacode"	=>	'<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesImageIDMode" callback="iframeScope.insertShortcodeToImageID">data</div>'
					),
					array(
						"type" 			=> "dropdown_dynamic",
						"heading" 		=> "Size",
						"param_name" 	=> "attachment_size",
						"dynamic"       => true,
						"ngrepeat_value"=> "option in iframeScope.component.options[iframeScope.component.active.id].size_labels",
						"ngclick_value" => "iframeScope.setOptionModel('attachment_size', option); iframeScope.setOptionModel('attachment_width', iframeScope.component.options[iframeScope.component.active.id].sizes[option].width); iframeScope.setOptionModel('attachment_height', iframeScope.component.options[iframeScope.component.active.id].sizes[option].height); iframeScope.setOptionModel('attachment_url', iframeScope.component.options[iframeScope.component.active.id].sizes[option].url);",
						"default"       => "medium",
						"css"			=> false,
						"condition"		=> "image_type=2",
					),
					array(
						"type" 			=> "measurebox",
						"heading" 		=> __("Width"),
						"param_name" 	=> "width",
						"value" 		=> "",
						"hide_wrapper_end" => true,
					),
					array(
						"type" 			=> "measurebox",
						"heading" 		=> __("Height"),
						"param_name" 	=> "height",
						"value" 		=> "",
						"hide_wrapper_start" => true,
					),
					array(
						"param_name" 	=> "attachment_width",
						"value" 		=> "",
						"hidden" 		=> true,
					),
					array(
						"param_name" 	=> "attachment_height",
						"value" 		=> "",
						"hidden" 		=> true,
					),
					array(
						"param_name" 	=> "width-unit",
						"value" 		=> "auto",
						"hidden" 		=> true,
					),
					array(
						"param_name" 	=> "height-unit",
						"value" 		=> "auto",
						"hidden" 		=> true,
					),
					array(
						"type"			=> "dropdown",
						"heading"		=> __("Object Fit", "oxygen"),
						"param_name"	=> "object-fit",
						"value"		=> array(
							""				=> '&nbsp',
							"initial" 		=> "initial",
							"cover" 		=> "cover",
							"contain" 		=> "contain",
							"fill" 			=> "fill",
							"scale-down" 	=> "scale-down",
							"none" 			=> "none"
						)
					),
					array(
						"type" 			=> "textfield",
						"heading" 		=> __("Object Position", "oxygen"),
						"param_name" 	=> "object-position",
						"value" 		=> "center center",
						"css" 			=> true
					),
					array(
						"type" 			=> "textfield",
						"heading" 		=> __("Aspect Ratio", "oxygen"),
						"param_name" 	=> "aspect-ratio",
						"value" 		=> "initial",
						"css" 			=> true
					),
					array(
						"param_name" 	=> "attachment_url",
						"value" 		=> "http://via.placeholder.com/1600x900",
						"hidden" 		=> true,
					),
					array(
						"type" 			=> "textfield",
						"heading" 		=> __("Alt Text"),
						"param_name" 	=> "alt",
						"value" 		=> "",
						"css" 			=> false,
						"condition"		=> "image_type=1",
						"dynamicdatacode"	=>	'<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertShortcodeToImageAlt">data</div>'
					),
					array(
						"type" 			=> "checkbox",
						"label" 		=> __("Lazy Load", "oxygen"),
						"param_name" 	=> "lazy",
						"value" 		=> "",
						"true_value" 	=> "lazy",
						"false_value" 	=> "",
						"css" 			=> false
					),
			),
			'advanced' => array(
				"size" => array(
						"values" 	=> array (
							'max-width' 	=> '100',
							'max-width-unit' 	=> '%',
							)
					),
				'allowed_html' => 'post',
				'allow_shortcodes' => false,
			)
		)
);

?>
