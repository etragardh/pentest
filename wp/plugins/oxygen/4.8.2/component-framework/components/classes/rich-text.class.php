<?php

/**
 * Text Block Class
 * 
 * @since 0.1.2
 */


Class OXY_Rich_Text extends CT_Component {

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

		// add specific options
		add_action("ct_toolbar_component_settings", array( $this, "settings") );
	}


	/**
	 * Add a [oxy_rich_text] shortcode to WordPress
	 *
	 * @since 0.1.2
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
		if( class_exists( 'Oxygen_Gutenberg' ) ) $editable_attribute = Oxygen_Gutenberg::decorate_attribute( $options, $editable_attribute, 'richtext' );
		
		?><<?php echo esc_attr($options['tag'])?> id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php echo $editable_attribute; ?></<?php echo esc_attr($options['tag'])?>><?php

		return ob_get_clean();
	}


	/**
	 * Output settings
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function settings() { 

		global $oxygen_toolbar; ?>

				<!-- text align -->
				<div class='oxygen-control-row'
					ng-hide="!isActiveName('oxy_rich_text')">
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Text Align","oxygen"); ?></label>
						<div class='oxygen-control'>
							<div class='oxygen-icon-button-list'>
								<?php $oxygen_toolbar->icon_button_list_button('text-align','left','text-align/left.svg'); ?>
								<?php $oxygen_toolbar->icon_button_list_button('text-align','center','text-align/center.svg'); ?>
								<?php $oxygen_toolbar->icon_button_list_button('text-align','right','text-align/right.svg'); ?>
								<?php $oxygen_toolbar->icon_button_list_button('text-align','justify','text-align/justify.svg'); ?>
							</div>
						</div>
					</div>
				</div>
		<?php
	}
}

global $oxygen_vsb_components;
$oxygen_vsb_components['rich_text'] = new OXY_Rich_Text( 

		array( 
			'name' 		=> __('Rich Text', 'oxygen'),
			'tag' 		=> 'oxy_rich_text',
			'params' 	=> array(
					array(
						"type" 			=> "content",
						"param_name" 	=> "ct_content",
						"value" 		=> "This is a block of text. Double-click this text to edit it.",
						"css" 			=> false,
						"hidden"		=> true,
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
								'font-size' 	=> "",
								'font-weight' 	=> "",
								'text-align' 	=> ""
							)
					),
					'allowed_html'      => 'post',
                    'allow_shortcodes'  => false,
			),
		)
);