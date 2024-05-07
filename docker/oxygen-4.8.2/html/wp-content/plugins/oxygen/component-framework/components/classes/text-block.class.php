<?php

/**
 * Text Block Class
 * 
 * @since 0.1.2
 */


Class CT_Text_Block extends CT_Component {

	var $options;

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// Add shortcode
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_basics_components_text", array( $this, "component_button" ) );
	}


	/**
	 * Add a [ct_text_block] shortcode to WordPress
	 *
	 * @since 0.1.2
	 */

	function add_shortcode( $atts, $content, $name ) {
		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		global $rendered_components;
		if( !$rendered_components ) $rendered_components = array();
		$rendered_components[ $options['id'] ] = json_decode( $atts['ct_options'] );

		$content = (is_array($content)) ? do_oxygen_elements( $content ) : do_shortcode( $content );
		$content = oxygen_vsb_filter_shortcode_content_decode($content);

		ob_start();

		$editable_attribute = $content;

		if( class_exists( 'Oxygen_Gutenberg' ) ) $editable_attribute = Oxygen_Gutenberg::decorate_attribute( $options, $editable_attribute, 'string' );
		
		?><<?php echo esc_attr($options['tag'])?> id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php echo $editable_attribute; ?></<?php echo esc_attr($options['tag'])?>><?php

		return ob_get_clean();
	}
}

global $oxygen_vsb_components;
$oxygen_vsb_components['text_block'] = new CT_Text_Block( 

		array( 
			'name' 		=> 'Text',
			'tag' 		=> 'ct_text_block',
			'params' 	=> array(
					array(
						"type" 			=> "content",
						"param_name" 	=> "ct_content",
						"value" 		=> "This is a block of text. Double-click this text to edit it.",
						"css" 			=> false
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
					array(
						"type" 			=> "tag",
						"heading" 		=> __("Tag", "oxygen"),
						"param_name" 	=> "tag",
						"value" 		=> array (
											"div" 			=> "div",
											"p" 			=> "p",
											"figcaption" 	=> "figcaption",
											"time" 			=> "time",
											"article" 		=> "article",
											"summary" 		=> "summary",
											"details" 		=> "details",
										),

						"css" 			=> false,
					),
				),
			'advanced' 	=> array(
					'typography' => array(
						'values' 	=> array (
								'font-size' 	=> "",
								'font-weight' 	=> "",
								'text-align' 	=> ""
							)
					),
					'allowed_html'      => 'post',
                    'allow_shortcodes'  => false,
			),
			'content_editable' => true,
		)
);