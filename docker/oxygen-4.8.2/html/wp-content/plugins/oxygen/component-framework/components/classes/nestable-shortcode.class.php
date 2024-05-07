<?php

/**
 * Nestable Shortcode Component Class
 * 
 * @since 0.1.3
 */

Class CT_Nestable_Shortcode extends CT_Component {

	var $options;

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		add_shortcode('ror', array($this, 'temp_shortcode'));

		for ( $i = 2; $i <= 16; $i++ ) {
			add_shortcode( $this->options['tag'] . "_" . $i, array( $this, 'add_shortcode' ) );
		}

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxy_folder_wordpress_components", array( $this, "component_button" ) );
	}

	function temp_shortcode($atts, $content) {
		return '<div class="divdiv">'.$content.'</div>';
	}


	/**
	 * Add a [NESTABLE_SHORTCODE] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content, $name ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );
		
		$content = do_oxygen_elements( $content );

		$wrappingShortcode = oxygen_base64_decode_for_json($options['wrapping_shortcode']);
		
		$matches = array();

		preg_match('/\[([^\s\]]{1,})[^\]]*\]/i', $wrappingShortcode, $matches);

		if(sizeof($matches) > 0) {
			$wrappedContent = do_shortcode($matches[0].$content.'[/'.$matches[1].']');
		}
		else {
			$wrappedContent = $content;
		}

		ob_start();

		?><div id="<?php echo esc_attr($options['selector']); ?>" class="<?php if(isset($options['classes'])) echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php echo $wrappedContent;?></div><?php

		return ob_get_clean();
	}


}


// Create toolbar inctances
global $oxygen_vsb_components;
$oxygen_vsb_components['nestable_shortcode'] = new CT_Nestable_Shortcode ( 

		array( 
			'name' 		=> 'Shortcode Wrapper',
			'tag' 		=> 'ct_nestable_shortcode',
			'params' 	=> array(
					array(
						"param_name" 	=> "wrapping_shortcode",
						"value" 		=> "",
						"type" 			=> "textfield",
						"heading" 		=> __("Full shortcode","oxygen"),
						"css" 			=> false,
					),
					array(
						"param_name" 	=> "wrapping_start",
						"value" 		=> "",
						"hidden" 		=> "true",
						"css" 			=> false,
					),
					array(
						"param_name" 	=> "wrapping_end",
						"value" 		=> "",
						"hidden" 		=> "true",
						"css" 			=> false,
					),
					array(
						"type" 			=> "flex-layout",
						"heading" 		=> __("Layout Child Elements", "oxygen"),
						"param_name" 	=> "flex-direction",
						"css" 			=> true,
					),
					array(
						"type" 			=> "checkbox",
						"heading" 		=> __("Allow multiline"),
						"param_name" 	=> "flex-wrap",
						"value" 		=> "",
						"true_value" 	=> "wrap",
						"false_value" 	=> "",
						"condition" 	=> "flex-direction=row"
					),
					array(
						"type" => "positioning",
					),
					array(
						"type" 			=> "measurebox",
						"heading" 		=> __("Width"),
						"param_name" 	=> "width",
						"value" 		=> "",
					),
					array(
						"type" 			=> "colorpicker",
						"heading" 		=> __("Background color"),
						"param_name" 	=> "background-color",
					),
				),
			'advanced' 	=> array(
					'typography' => array(
						'values' 	=> array (
								'font-family' 	=> "",
								'font-size' 	=> "",
								'font-weight' 	=> "",
							)
					),
					'flex' => array(
						'values' 	=> array (
								'display' 		 => 'flex',
								'flex-direction' => 'column',
								'align-items' 	 => 'flex-start',
								'justify-content'=> '',
								'text-align' 	 => '',
								'flex-wrap' 	 => 'nowrap',
							)
					),
                    'allowed_html' => 'post',
                    'allow_shortcodes' => true,
			),


			
		)
);

?>