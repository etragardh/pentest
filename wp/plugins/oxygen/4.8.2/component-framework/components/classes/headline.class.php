<?php

/**
 * Headline Component Class
 * 
 * @since 0.1.2
 */

Class CT_Headline extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_basics_components_text", array( $this, "component_button" ) );
	}


	/**
	 * Add a [ct_headline] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content, $name ) {
		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		$content = (is_array($content)) ? do_oxygen_elements( $content ) : do_shortcode( $content );
		$content = oxygen_vsb_filter_shortcode_content_decode($content);

		ob_start();
		
		$editable_attribute = $content;
		if( class_exists( 'Oxygen_Gutenberg' ) ) $editable_attribute = Oxygen_Gutenberg::decorate_attribute( $options, $editable_attribute, 'string' );

		echo "<".esc_attr($options['tag'])." id=\"".esc_attr($options['selector'])."\" class=\"".esc_attr($options['classes'])."\"";do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); echo ">" . $editable_attribute . "</".esc_attr($options['tag']).">";

		return ob_get_clean();
	}
}

global $oxygen_vsb_components;
$oxygen_vsb_components['headline'] = new CT_Headline ( 

		array( 
			'name' 		=> 'Heading',
			'tag' 		=> 'ct_headline',
			'params' 	=> array(
					array(
						"type" 			=> "content",
						"param_name" 	=> "ct_content",
						"value" 		=> "Double-click this headline to edit the text.",
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
											"" 	  => "&nbsp;",
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
											"h1" => "H1",
											"h2" => "H2",
											"h3" => "H3",
											"h4" => "H4",
											"h5" => "H5",
											"h6" => "H6",
										),
						"css" 			=> false,
					),
				),
			'advanced' 	=> array(
					'typography' => array(
						'values' 	=> array (
								'font-family' 	=> array ( 'global', 'Display' ),
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