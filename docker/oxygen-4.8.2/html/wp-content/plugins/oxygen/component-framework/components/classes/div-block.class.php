<?php

/**
 * Div Block Component Class
 * 
 * @since 0.1.3
 */

Class CT_DIV_Block extends CT_Component {

	var $options;

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		for ( $i = 2; $i <= 16; $i++ ) {
			add_shortcode( $this->options['tag'] . "_" . $i, array( $this, 'add_shortcode' ) );
		}

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_basics_components_containers", array( $this, "component_button" ) );
	}


	/**
	 * Add a [div_block] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content, $name ) {
		
		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		ob_start();

		?><<?php echo esc_attr($options['tag'])?> id="<?php echo esc_attr($options['selector']); ?>" class="<?php if(isset($options['classes'])) echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php echo (is_array($content)) ? do_oxygen_elements( $content ) : do_shortcode( $content ); ?></<?php echo esc_attr($options['tag'])?>><?php

		return ob_get_clean();
	}
}


// Create toolbar inctances
global $oxygen_vsb_components;
$oxygen_vsb_components['div_block'] = new CT_DIV_Block ( 

		array( 
			'name' 		=> 'Div',
			'tag' 		=> 'ct_div_block',
			'params' 	=> array(
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
						"type" 			=> "columnwidth",
						"heading" 		=> __("Width"),
						"param_name" 	=> "width",
						"value" 		=> "",
					),
					array(
						"type" 			=> "colorpicker",
						"heading" 		=> __("Background color"),
						"param_name" 	=> "background-color",
					),
					array(
						"type" 			=> "tag",
						"heading" 		=> __("Tag", "oxygen"),
						"param_name" 	=> "tag",
						"value" 		=> array (
											"div" 		=> "div",
											"article" 	=> "article",
											"aside" 	=> "aside",
											"details" 	=> "details",
											"figure" 	=> "figure",
											"footer" 	=> "footer",
											"header" 	=> "header",
											"hgroup" 	=> "hgroup",
											"main" 		=> "main",
											"mark" 		=> "mark",
											"nav" 		=> "nav",
											"section" 	=> "section",
										),
						"css" 			=> false,
						"rebuild" 		=> true,
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
					'grid' => array(
						'values' 	=> array (
                            'grid-columns-auto-fit' => '',
							'grid-column-count' => '1',
							'grid-column-min-width' => '200',
							'grid-column-min-width-unit' => 'px',
							'grid-column-max-width' => '1',
							'grid-column-max-width-unit' => 'fr',
							'grid-column-gap' => '20',
							'grid-column-gap-unit' => 'px',

							'grid-row-behavior' => 'Auto',
							'grid-row-count' => '',
							'grid-row-min-height' => '',
							'grid-row-min-height-unit' => 'px',
							'grid-row-max-height' => '',
							'grid-row-max-height-unit' => 'fr',
							'grid-row-gap' => '20',
							'grid-row-gap-unit' => 'px',
						 
							'grid-match-height-of-tallest-child' => 'true',
							'grid-justify-items' => 'stretch',
							'grid-align-items' => 'stretch',
						)
					),
                    'allowed_html' => 'post',
                    'allow_shortcodes' => true,
			),


			
		)
);

?>