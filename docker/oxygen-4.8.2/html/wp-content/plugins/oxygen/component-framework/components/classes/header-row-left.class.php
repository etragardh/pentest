<?php 

/**
 * Header Builder Row Left component
 *
 * @since 2.0
 * @author Ilya K.
 */

Class Oxy_Header_Builder_Row_Left extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// Remove component button
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		
		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		for ( $i = 2; $i <= 16; $i++ ) {
			add_shortcode( $this->options['tag'] . "_" . $i, array( $this, 'add_shortcode' ) );
		}
	}


	/**
	 * Add a [oxy_header_left] shortcode to WordPress
	 *
	 * @since 2.0
	 */

	function add_shortcode( $atts, $content, $name ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
            return '';
        }

		$options = $this->set_options( $atts );

		ob_start();
		
		?><div id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php echo (is_array($content)) ? do_oxygen_elements( $content ) : do_shortcode( $content ); ?></div><?php

		return ob_get_clean();
	}
}


// Create inctance
global $oxygen_vsb_components;
$oxygen_vsb_components['header_row_left'] = new Oxy_Header_Builder_Row_Left( array( 
			'name' 		=> __('Row Left', 'oxygen'),
			'tag' 		=> 'oxy_header_left',
			'params' 	=> array(),
			'advanced' 	=> array(
					"positioning" => array(
						"values" => array(
							)
					)
				)
		)
	);