<?php 

Class CT_Sidebar extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// remove component button
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		
		// add shortcode
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// add toolbar folder
		add_action("ct_toolbar_sidebars_folder", array( $this, "sidebars_list") );
     
        add_filter("oxygen_elements_without_presets", array($this, "oxygen_elements_without_presets_callback"));
	}


	/**
	 * Add a [ct_sidebar] shortcode to WordPress
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function add_shortcode( $atts, $content, $name ) {

		if (! $this->validate_shortcode( $atts, $content, $name ) ) {
            return '';
        }

		$options = $this->set_options( $atts );

		ob_start();

		if ( is_active_sidebar( $options["sidebar_id"] ) ) {
			dynamic_sidebar( $options["sidebar_id"] );
		}
		else {
			echo "No active \"".$options["sidebar_id"]."\" sidebar";
		}

		return ob_get_clean();
	}


	/**
	 * Display all sidebars
	 *
	 * @since  2.0
	 * @author Ilya K.
	 */

	function sidebars_list() {
		
		foreach ( $GLOBALS['wp_registered_sidebars'] as $id => $sidebar ) { ?>

			<div class="oxygen-add-section-element" title="<?php echo esc_attr($sidebar['description']); ?>"
				data-searchid="<?php echo esc_attr($sidebar['id']); ?>" data-searchcat="Sidebars"

				ng-click="iframeScope.addSidebar('<?php echo esc_attr($sidebar['id']); ?>')">
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/sidebars.svg' />
				<?php echo esc_html($sidebar['name']); ?>
			</div>

		<?php }
	}
}


// Create inctance
global $oxygen_vsb_components;
$oxygen_vsb_components['sidebar'] = new CT_Sidebar( array( 
			'name' 		=> 'Sidebar',
			'tag' 		=> 'ct_sidebar',
			'params' 	=> array(
					array(
						"type" 			=> "textfield",
						"param_name" 	=> "sidebar_id",
						"hidden" 		=> true,
						"css" 			=> false,
					),
				),
			'advanced' => false
			)
		); 
