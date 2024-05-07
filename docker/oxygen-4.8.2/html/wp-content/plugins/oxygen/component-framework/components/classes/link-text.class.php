<?php

/**
 * Link Text Component Class
 * 
 * @since 0.3.1
 */

Class CT_Link_Text extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_basics_components_links", array( $this, "component_button" ) );
	}


	/**
	 * Add a [ct_link_text] shortcode to WordPress
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
		$url = oxygen_vsb_base64_decode($options['url']);
		if (isset($options['url_encoded']) && $options['url_encoded'] === "true") {
			$url = oxygen_vsb_quotes_decode($options['url']);
		}
		$url = do_shortcode($url);
		$editable_link = esc_url($url);

		$target = ( esc_attr($options['target']) != "" ) ? "target=\"".esc_attr($options['target'])."\"" : "";
		$rel 	= ( esc_attr($options['rel']) != "" ) 	 ? "rel=\"".esc_attr($options['rel'])."\"" : "";

		if( class_exists( 'Oxygen_Gutenberg' ) ) {
			$editable_attribute = Oxygen_Gutenberg::decorate_attribute( $options, $editable_attribute, 'string' );
			$editable_link = Oxygen_Gutenberg::decorate_attribute( $options, $editable_link, 'link' );
        }

		?><a id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" href="<?php echo $editable_link; ?>" <?php echo "$target $rel"; ?> <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php echo $editable_attribute; ?></a><?php

		return ob_get_clean();
	}

}


// Create toolbar inctances
global $oxygen_vsb_components;
$oxygen_vsb_components['link_text'] = new CT_Link_Text ( 

		array( 
			'name' 		=> 'Text Link',
			'tag' 		=> 'ct_link_text',
			'params' 	=> array(

					array(
						"type" 			=> "hyperlink",
						"heading" 		=> __("URL"),
						"param_name" 	=> "url",
						"value" 		=> "http://",
						"css" 			=> false,
						"dynamicdatacode"	=>	'<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesLinkMode" callback="iframeScope.insertShortcodeToUrl">data</div>'
					),
					array(
						"type" 			=> "dropdown",
						"heading" 		=> __("Target","oxygen"),
						"param_name" 	=> "target",
						"value" 		=> array (
											"" => "&nbsp;",
											"_self" => "_self",
											"_blank" => "_blank",
											"_parent" => "_parent",
											"_top" => "_top",
										),
						"default" 		=> "_self",
						"css" 			=> false,
					),
					array(
						"type" 			=> "html_attribute",
						"heading" 		=> __("Rel","oxygen"),
						"param_name" 	=> "rel",
						"value" 		=> "",
						"css" 			=> false,
					),
					array(
						"type" 			=> "content",
						"param_name" 	=> "ct_content",
						"value" 		=> "Double-click to edit link text.",
						"css" 			=> false,
					),
					array(
						"type" 			=> "font-family",
						"heading" 		=> __("Font Family", "oxygen"),
						"css" 			=> false,
					),
					array(
						"type" 				=> "colorpicker",
						"param_name" 		=> "color",
						"heading" 			=> __("Text Color", "oxygen"),
						"hide_wrapper_end" 	=> true,
					),
					array(
						"type" 				=> "colorpicker",
						"param_name" 		=> "hover_color",
						"heading" 			=> __("Hover Color", "oxygen"),
						"hide_wrapper_start"=> true,
						"state_condition" 	=> "!=hover"
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
						"type" 			=> "checkbox",
						"heading" 		=> __("Underline", "oxygen"),
						"label" 		=> __("Underline link text", "oxygen"),
						"param_name" 	=> "text-decoration",
						"value" 		=> "none",
						"true_value" 	=> "underline",
						"false_value" 	=> "none",
					),
					
					array(
						"type" 			=> "textfield",
						"heading" 		=> __("Target"),
						"param_name" 	=> "target",
						"value" 		=> "_self",
						"hidden"		=> true,
						"css" 			=> false,
					),
				),
			'advanced' 	=> array(
				"positioning" => array(
					"values" => array (
						'display' => 'inline-block',
					)
				),
				'typography' => array(
					'values' => array (
						'font-size' 	=> "",
						'font-weight' 	=> "",
					)
				),
				'allowed_html'      => 'post',
                'allow_shortcodes'  => false,
			),
			'content_editable' => true,
		)
);

?>