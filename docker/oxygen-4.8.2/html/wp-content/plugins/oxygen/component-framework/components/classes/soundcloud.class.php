<?php 

/**
 * Google Maps component
 *
 * @since 2.0
 * @author Ilya K.
 */

Class Oxy_SoundCloud extends CT_Component {

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
	 * Add a [oxy_soundcloud] shortcode to WordPress
	 *
	 * @since 2.0
	 */

	function add_shortcode( $atts, $content = null, $name = null ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		ob_start();
		
		?><div id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><iframe scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/<?php echo $options["soundcloud_track_id"]; ?>&amp;color=<?php echo urlencode($options["soundcloud_color"]); ?>&amp;auto_play=<?php echo urlencode($options["soundcloud_auto_play"]); ?>&amp;hide_related=<?php echo urlencode($options["soundcloud_hide_related"]); ?>&amp;show_comments=<?php echo urlencode($options["soundcloud_show_comments"]); ?>&amp;show_user=true&amp;show_reposts=false&amp;show_teaser=true&amp;visual=true"></iframe></div><?php

		return ob_get_clean();
	}

}


// Create instance
global $oxygen_vsb_components;
$oxygen_vsb_components['soundcloud'] = new Oxy_SoundCloud( array( 
			'name' 		=> __('SoundCloud','oxygen'),
			'tag' 		=> 'oxy_soundcloud',
			'params' 	=> array(
				array(
					"type" 			=> "textfield",
					"heading" 		=> __("SoundCloud URL","oxygen"),
					"param_name" 	=> "soundcloud_url",
					"value" 		=> "https://soundcloud.com/nathaniel-eliason/13-learning-spanish-and-more",
					"css" 			=> false,
					"ng_show" 		=> "!iframeScope.isEditing('state')&&!iframeScope.isEditing('media')"
				),
				array(
					"type" 			=> "measurebox",
					"heading" 		=> __("Width", "oxygen"),
					"param_name" 	=> "width",
					"value" 		=> "100",
				),
				array(
					"type" 			=> "measurebox",
					"heading" 		=> __("Height", "oxygen"),
					"param_name" 	=> "height",
					"value" 		=> "300",
				),
				array(
					"type" 			=> "colorpicker",
					"heading" 		=> __("Color", "oxygen"),
					"param_name" 	=> "soundcloud_color",
					"value" 		=> "",
					"css" 			=> false,
					"ng_show" 		=> "!iframeScope.isEditing('state')&&!iframeScope.isEditing('media')"
				),
				array(
					"type" 			=> "checkbox",
					"heading" 		=> __("Auto Play","oxygen"),
					"param_name" 	=> "soundcloud_auto_play",
					"value" 		=> "false",
					"true_value" 	=> "true",
					"false_value" 	=> "false",
					"css" 			=> false,
					"ng_show" 		=> "!iframeScope.isEditing('state')&&!iframeScope.isEditing('media')"
				),
				array(
					"type" 			=> "checkbox",
					"heading" 		=> __("Show Comments","oxygen"),
					"param_name" 	=> "soundcloud_show_comments",
					"value" 		=> "true",
					"true_value" 	=> "true",
					"false_value" 	=> "false",
					"css" 			=> false,
					"ng_show" 		=> "!iframeScope.isEditing('state')&&!iframeScope.isEditing('media')"
				),
				array(
					"type" 			=> "checkbox",
					"heading" 		=> __("Hide Related ","oxygen"),
					"param_name" 	=> "soundcloud_hide_related",
					"value" 		=> "false",
					"true_value" 	=> "true",
					"false_value" 	=> "false",
					"css" 			=> false,
					"ng_show" 		=> "!iframeScope.isEditing('state')&&!iframeScope.isEditing('media')"
				),
			),
			'advanced' 	=> array(
					"size" => array(
						"values" => array(
								"height-unit" => "px",
								"width-unit" => "%"
							)
					)
			)
		)
);