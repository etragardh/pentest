<?php

/**
 * Re-usable Component Class
 * 
 * @since 0.2.3
 */

Class CT_Reusable extends CT_Component {

	var $options;

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// remove component button
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
	}


	/**
	 * Add a [ct_reusable] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content, $name ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		global $is_repeater_child;

		// don't run the shortcodes and include re-usable CSS to page cached CSS only if it is not inside Repeater
		if ( !$is_repeater_child  ) {
			global $oxygen_vsb_css_caching_active;
			if (isset($oxygen_vsb_css_caching_active) && $oxygen_vsb_css_caching_active===true) {			
				return;
			}
		}

		ob_start();	

		$options 	= json_decode( $atts["ct_options"], true ); 
		$view_id 	= $options["view_id"];
		$view 		= get_post( $view_id );

		if (!$view) return ob_get_clean();

		// needed to load cached CSS file if it is not inside Repeater
		if ( !$is_repeater_child  ) {
			global $oxygen_vsb_css_files_to_load;
			$oxygen_vsb_css_files_to_load[] = $view_id;
		}

		$json = get_post_meta(  $view->ID, 'ct_builder_json', true );
		$components_tree = json_decode($json, true);
		
		if ($components_tree) {
			if (!oxygen_doing_oxygen_elements()) {
				global $oxygen_doing_oxygen_elements;
				$oxygen_doing_oxygen_elements = true;
				$content = do_oxygen_elements($components_tree['children']);
				$oxygen_doing_oxygen_elements = false;
			}
			else {
				$content = do_oxygen_elements($components_tree['children']);
			}
		}
		else {
			$shortcodes = get_post_meta( $view->ID, "ct_builder_shortcodes", true );
			$content = ct_do_shortcode( $shortcodes );
		}
		
		echo $content;

		return ob_get_clean();
	}
}


// Create toolbar inctances
global $oxygen_vsb_components;
$oxygen_vsb_components['reusable'] = new CT_Reusable ( 

		array( 
			'name' 		=> 'Reusable',
			'tag' 		=> 'ct_reusable',
			'advanced'  => array(
				'allow_shortcodes'  => true,
			)
		)
);

?>