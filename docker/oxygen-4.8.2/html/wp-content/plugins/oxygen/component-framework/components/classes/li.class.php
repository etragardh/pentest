<?php

/**
 * Ul component
 * 
 * @since 0.3.1
 * @deprecated 2.0
 *
 * Do not remove to keep old designs work!
 */

Class CT_LI_Component extends CT_Component {

	var $options;

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );

		for ( $i = 2; $i <= 16; $i++ ) {
			add_shortcode( $this->options['tag'] . "_" . $i, array( $this, 'add_shortcode' ) );
		}

		// remove component button
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
	}


	/**
	 * Add a toolbar button
	 *
	 * @since 0.1.5
	 */

	function component_button() { ?>

		<div class="ct-add-component-button"
 			data-searchid="<?php echo strtolower( preg_replace('/\s+/', '_', sanitize_text_field( $this->options['name'] ) ) ) ?>"
			ng-click="addComponent('<?php echo isset($this->options['tag'])? esc_attr( $this->options['tag'] ) :''; ?>'<?php echo isset($type)?$type:''; ?>)"
			ng-if="isActiveName('ct_ul')||isActiveName('ct_li')">
			<div class="ct-add-component-icon">
				<span class="ct-icon <?php echo esc_attr( $this->options['tag'] ); ?>-icon"></span>
			</div>
			<?php echo esc_html( $this->options['name'] ); ?>
		</div>

	<?php }


	/**
	 * Add a [ct_li] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content, $name ) {
		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		ob_start();

		?><li id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>"><?php echo do_shortcode( $content ); ?></li><?php

		return ob_get_clean();
	}
}


// Create toolbar inctances
global $oxygen_vsb_components;
$oxygen_vsb_components['li'] = new CT_LI_Component ( 

		array( 
			'name' 		=> 'Li',
			'tag' 		=> 'ct_li',
			'params' 	=> array(
					array(
						"type" 			=> "content",
						"param_name" 	=> "ct_content",
						"value" 		=> "Double-click to edit list item text.",
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
			'advanced'  => array(
		        'allowed_html' => 'post',
                'allow_shortcodes' => false,
            ),
			'content_editable' => true,
		)
);

?>
