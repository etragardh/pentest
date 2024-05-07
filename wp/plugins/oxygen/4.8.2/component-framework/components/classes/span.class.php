<?php

/**
 * Span Class
 * 
 * @since 0.1.8
 */


Class CT_Span extends CT_Component {

	var $options;

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// remove component button
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		
		// Add shortcode
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );
	}


	/**
	 * Add a [ct_span] shortcode to WordPress
	 *
	 * @since 0.1.2
	 */

	function add_shortcode( $atts, $content, $name ) {
		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		// Rudimentary detection of dynamic data span
		$dynamic_data = false;
		if( strpos( $content, '[oxygen ' ) !== FALSE ) $dynamic_data = true;

		$frontend_block = false;
		// Is there a better way of detecting if we are reaching this code from Oxygen_Gutenberg->render_gutenberg_block()
		$dbt = debug_backtrace();
		foreach ($dbt as $debug_item) {
			if( isset($debug_item['function']) && ( $debug_item['function'] == 'render_gutenberg_block' ) ){
				// Rendered inside a block in frontend
				$frontend_block = true;
				break;
			}
		}

		$shortcode_output = '';
		if( !class_exists( 'Oxygen_Gutenberg' ) || $frontend_block ) {
			ob_start();
			$content = (is_array($content)) ? do_oxygen_elements( $content ) : do_shortcode( $content ); //Shortcodes will be rendered later by Oxygen Gutenberg plugin, if activated
			$shortcode_output = ob_get_clean();
		}
		
		ob_start();

		$editable_attribute = $shortcode_output.$content;
		// Only activate gutenberg integration if the span component is a dynamic data wrapper. Otherwise, it will be editable by it's parent text container anyways
		if( class_exists( 'Oxygen_Gutenberg' ) && $dynamic_data ) {
		    // Get the nicename set inside Oxygen
		    $options_temp = json_decode( $atts['ct_options'] );
			global $rendered_components;
			if( !$rendered_components ) $rendered_components = array();
		    $parent_text_nicename = isset( $rendered_components[ $options_temp->ct_parent ] ) && !empty( $rendered_components[ $options_temp->ct_parent ]->nicename ) ? $rendered_components[ $options_temp->ct_parent ]->nicename : '';
            $options['gutenberg_placeholder'] = !empty( $options_temp->nicename ) ? $options_temp->nicename : ( empty( $parent_text_nicename ) ? 'Dynamic Data' : $parent_text_nicename );
			$editable_attribute = Oxygen_Gutenberg::decorate_attribute( $options, $editable_attribute, 'string' );
		}

		?><span id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php echo $editable_attribute; ?></span><?php

		return ob_get_clean();
	}
}

global $oxygen_vsb_components;
$oxygen_vsb_components['span'] = new CT_Span ( 

		array( 
			'name' 		=> 'Span',
			'tag' 		=> 'ct_span',
			'params' 	=> array(
					array(
						"type" 			=> "content",
						"param_name" 	=> "ct_content",
						"value" 		=> __("Span text", "oxygen"),
						"css" 			=> false,
					),
					array(
						"type" 			=> "font-family",
						"heading" 		=> __("Font Family", "oxygen"),
						"css" 			=> false,
					),
					array(
						"type" 			=> "colorpicker",
						"heading" 		=> __("Text Color", "oxygen"),
						"param_name" 	=> "color",
						"value" 		=> "",
					),
					array(
						"type" 			=> "slider-measurebox",
						"heading" 		=> __("Font Size", "oxygen"),
						"param_name" 	=> "font-size",
					),
					array(
						"type" 			=> "dropdown",
						"heading" 		=> __("Font Weight", "oxygen"),
						"param_name" 	=> "font-weight",
						"value" 		=> array (
											"" 		=> "&nbsp;",
											"100" => "100",
											"200" => "200",
											"300" => "300",
											"400" => "400",
											"500" => "500",
											"600" => "600",
											"700" => "700",
											"800" => "800",
											"900" => "900",
										),
					),
				),
			'advanced' 	=> array(
					"positioning" => array(
						"values" 	=> array (
							'display' 	=> 'inline-block',
							)
					),
					'typography' => array(
						'values' 	=> array (
								'font-size' 			=> '',
								'font-weight' 			=> '',
								'font-style' 			=> '',
								'text-decoration' 		=> 'inherit',
								'text-transform' 		=> '',
							)
					),
					'allowed_html' => 'post',
                    'allowed_shortcodes' => false,
			),
			'content_editable' => true,
		)
);