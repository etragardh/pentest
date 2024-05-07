<?php 

/**
 * Header Builder Row component
 *
 * @since 2.0
 * @author Ilya K.
 */

Class Oxy_Header_Builder_Row extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		for ( $i = 2; $i <= 16; $i++ ) {
			add_shortcode( $this->options['tag'] . "_" . $i, array( $this, 'add_shortcode' ) );
		}

		// add specific options
		add_action("ct_toolbar_component_settings", array( $this, "header_settings"), 9 );

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("ct_toolbar_smart_list", array( $this, "component_button" ) );
	}


	/**
	 * Add a toolbar button
	 *
	 * @since 2.0
	 */
	function component_button() { 
		
		if (oxygen_hide_element_button($this->options['tag'])) {
			return;
		}
		
		?>

		<div class="oxygen-add-section-element"
			data-searchid="<?php echo strtolower( preg_replace('/\s+/', '_', sanitize_text_field( $this->options['name'] ) ) ) ?>"
			ng-click="iframeScope.addComponent('<?php echo esc_attr($this->options['tag']); ?>')"
			ng-show="isActiveName('oxy_header')||isActiveName('oxy_header_row')">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/header.svg' />
			<?php echo esc_html($this->options['name']); ?>
		</div>

	<?php }


	/**
	 * Add a [oxy_header_row] shortcode to WordPress
	 *
	 * @since 2.0
	 */

	function add_shortcode( $atts, $content, $name ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
            return '';
        }

		$options = $this->set_options( $atts );

		ob_start();
		
		?><div id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo (isset($options["hide_in_sticky"])&&$options["hide_in_sticky"]=="yes") ? "oxygen-hide-in-sticky " : "";?><?php echo (isset($options["show_in_sticky_only"])&&$options["show_in_sticky_only"]=="yes") ? "oxygen-show-in-sticky-only " : "";?><?php echo ($options["overlay_display"]=="hide_in_overlay") ? "oxygen-hide-in-overlay " : "";?><?php echo ($options["overlay_display"]=="only_show_in_overlay") ? "oxygen-only-show-in-overlay " : "";?><?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><div class="oxy-header-container"><?php echo (is_array($content)) ? do_oxygen_elements( $content ) : do_shortcode( $content ); ?></div></div><?php

		return ob_get_clean();
	}


	/**
	 * Output special settings in Basic Styles tab
	 *
	 * @since 2.0
	 */

	function header_settings() { ?>

		<div ng-show="isActiveName('<?php echo $this->options['tag']; ?>')">
			<div class="oxygen-control-row">
				<div class='oxygen-control-wrapper'>
					<label class='oxygen-control-label'><?php _e("Stack Vertically Below","oxygen"); ?></label>
					<div class='oxygen-control oxygen-special-property not-available-for-media not-available-for-classes'>

						<div class="oxygen-select oxygen-select-box-wrapper">
							<div class="oxygen-select-box"
								ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'stack-header-vertically')}">
								<div class="oxygen-select-box-current">{{iframeScope.getMediaTitle(iframeScope.getOption('stack-header-vertically'))}}</div>
								<div class="oxygen-select-box-dropdown"></div>
							</div>
							<div class="oxygen-select-box-options">
								<div class="oxygen-select-box-option" 
									ng-repeat="name in iframeScope.sortedMediaList()"
									ng-if="name!='default'"
									ng-click="iframeScope.setOptionModel('stack-header-vertically',name)"
									ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('stack-header-vertically')==name}">
									{{iframeScope.getMediaTitle(name)}}
								</div>
								<div class="oxygen-select-box-option" 
									ng-click="iframeScope.setOptionModel('stack-header-vertically','never')"
									ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('stack-header-vertically')=='never'}">
									<?php _e("Never","oxygen"); ?>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>

			<div class="oxygen-control-row">
				<div class='oxygen-control-wrapper'>
					<label class='oxygen-control-label'><?php _e("Hide Row Below","oxygen"); ?></label>
					<div class='oxygen-control oxygen-special-property not-available-for-media not-available-for-classes'>

						<div class="oxygen-select oxygen-select-box-wrapper">
							<div class="oxygen-select-box"
								ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'hide-row')}">
								<div class="oxygen-select-box-current">{{iframeScope.getMediaTitle(iframeScope.getOption('hide-row'))}}</div>
								<div class="oxygen-select-box-dropdown"></div>
							</div>
							<div class="oxygen-select-box-options">
								<div class="oxygen-select-box-option" 
									ng-repeat="name in iframeScope.sortedMediaList()"
									ng-if="name!='default'"
									ng-click="iframeScope.setOptionModel('hide-row',name)"
									ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('hide-row')==name}">
									{{iframeScope.getMediaTitle(name)}}
								</div>
								<div class="oxygen-select-box-option" 
									ng-click="iframeScope.setOptionModel('hide-row','never')"
									ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('hide-row')=='never'}">
									<?php _e("Never","oxygen"); ?>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

	<?php }
}


// Create instance
global $oxygen_vsb_components;
$oxygen_vsb_components['header_row'] = new Oxy_Header_Builder_Row( array( 
			'name' 		=> __('Header Row','oxygen'),
			'tag' 		=> 'oxy_header_row',
			'params' 	=> array(
				array(
					"type" 			=> "colorpicker",
					"heading" 		=> __("Background color"),
					"param_name" 	=> "background-color",
				),
				array(
					"type" 			=> "measurebox",
					"heading" 		=> __("Height","oxygen"),
					"param_name" 	=> "height",
					"param_units" 	=> "px",
					"value" 		=> "",
				),
				array(
					// special setting type for backwrad compatibility
					"heading" 		=> __("Sticky Display","oxygen"),
					"type" 			=> "hide_show_in_sticky",
					"param_name" 	=> "",
                    "css"           => false,
				),
				array(
                    "type"          => "radio",
                    "heading"       => __("Overlay Display", "oxygen"),
                    "param_name"    => "overlay_display",
                    "value"         => array(
                                        'only_show_in_overlay'  => __("only show in overlay", "oxygen"),
                                        'hide_in_overlay'     	=> __("hide in overlay", "oxygen"),
                                    ),
                    "default" 		=> "",
                    "css"           => false,
                ),
				array(
					"param_name" 	=> "header-row-custom-width-unit",
					"value" 		=> "auto",
					"hidden" 		=> true
				),
			),
			'advanced' => array(
				"size" => array(
					"values" => array(
						'header-row-width' => '',
					)
				)
			),
			'not_css_params' => array(
				'stack-header-vertically',
				'hide-row'
			)
		)
	);
