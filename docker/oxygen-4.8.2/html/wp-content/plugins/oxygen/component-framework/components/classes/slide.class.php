<?php 

Class CT_Slide extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		for ( $i = 2; $i <= 16; $i++ ) {
			add_shortcode( $this->options['tag'] . "_" . $i, array( $this, 'add_shortcode' ) );
		}
	}


	/**
	 * Add a toolbar button
	 *
	 * @since 0.1
	 */
	
	function component_button() { 
		
		if (oxygen_hide_element_button($this->options['tag'])) {
			return;
		}
		
		?>

		<div class="oxygen-add-section-element"
			data-searchid="<?php echo strtolower( preg_replace('/\s+/', '_', sanitize_text_field( $this->options['name'] ) ) ) ?>"
			ng-click="iframeScope.addComponent('<?php echo esc_attr($this->options['tag']); ?>')"
			ng-if="isActiveName('ct_slide')||isActiveName('ct_slider')">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/widgets.svg' />
			<?php echo esc_html($this->options['name']); ?>
		</div>

	<?php }


	/**
	 * Add a [ct_slide] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content = null, $name = null ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		ob_start();
		
		?><li><div id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php echo (is_array($content)) ? do_oxygen_elements( $content ) : do_shortcode( $content ); ?></div></li><?php

		$outputContent = ob_get_clean();

		$outputContent = apply_filters('oxygen_vsb_after_component_render', $outputContent, array('name'=>__('Slider','oxygen')), $name);

        return $outputContent;
	}

}

// Create Slide instance
global $oxygen_vsb_components;
$oxygen_vsb_components['slide'] = new CT_Slide( array( 
			'name' 		=> __('Slide', 'oxygen'),
			'tag' 		=> 'ct_slide',
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
						"value" 		=> "nowrap",
						"true_value" 	=> "wrap",
						"false_value" 	=> "nowrap",
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
						'values' => array(
								'font-family' 	=> "",
								'font-size' 	=> "",
								'font-weight' 	=> "",
							)
					),
					'flex' => array(
						'values' => array(
								'display' 		 => 'flex',
								'flex-direction' => 'column',
								'align-items' 	 => 'center',
								'justify-content'=> 'center',
								'text-align' 	 => 'center'
							)
					),
                    'allowed_html' => 'post',
                    'allow_shortcodes' => true,
			),

		)
);
