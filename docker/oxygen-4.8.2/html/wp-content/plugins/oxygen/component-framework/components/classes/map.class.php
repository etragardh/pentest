<?php 

/**
 * Google Maps component
 *
 * @since 2.0
 * @author Ilya K.
 */

Class Oxy_Google_Maps extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_helpers_components_external", array( $this, "component_button" ) );
	}


	/**
	 * Add a [oxy_map] shortcode to WordPress
	 *
	 * @since 2.0
	 */

	function add_shortcode( $atts, $content, $name ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
            return '';
        }

		$options = $this->set_options( $atts );

		ob_start();
		
		?><div id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><iframe src='https://www.google.com/maps/embed/v1/place?key=<?php echo get_option("oxygen_vsb_google_maps_api_key", ""); ?>&q=<?php echo urlencode($options['map_address']); ?>&zoom=<?php echo $options['map_zoom']; ?>' frameborder=0></iframe></div><?php

		return ob_get_clean();
	}

}


// Create Google Maps instance
global $oxygen_vsb_components;
$oxygen_vsb_components['google_maps'] = new Oxy_Google_Maps( array( 
		'name' 		=> __('Google Maps','oxygen'),
		'tag' 		=> 'oxy_map',
		'params' 	=> array(
			array(
				"type" 			=> "textfield",
				"heading" 		=> __("Address","oxygen"),
				"param_name" 	=> "map_address",
				"value" 		=> "",
				"css" 			=> false,
				"dynamicdatacode"	=>	'<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertShortcodeToMapAddress">data</div>'
			),
			array(
				"type" 			=> "slider-measurebox",
				"heading" 		=> __("Zoom", "oxygen"),
				"param_name" 	=> "map_zoom",
				"value" 		=> "14",
				"min"			=> "1",
				"max"			=> "22",
				"css" 			=> false,
			),
			array(
				"type" 			=> "measurebox",
				"heading" 		=> __("Height", "oxygen"),
				"param_name" 	=> "height",
				"value" 		=> "",
			),
		),
		'advanced' 	=> array(
				"positioning" => array(
					"values" 	=> array(
						)
				)
		)
	)
);