<?php 

define("OSD_PATH", 	plugin_dir_path( __FILE__ ) );
define("OSD_URI", 	plugin_dir_url( __FILE__ ) );

Class OxygenVSBSelectorDetector {

	function __construct() {

		// add scripts and styles
		add_action( 'oxygen_enqueue_ui_scripts', 	array( $this, 'enqueue_script' ) );
			
		// add views
		add_action( 'ct_toolbar_component_settings',			array( $this, 'choose_selector_view' ) );
		add_action( 'oxygen_sidepanel_before_classes', 			array( $this, 'list_style_sets_view' ) );

		add_action( 'oxygen_sidepanel_uncategorized_stylesets', array( $this, 'uncategorized_style_sets_view' ), 10, 1 );
		add_action( 'oxygen_sidepanel_categorized_stylesets', 	array( $this, 'categorized_style_sets_view' ), 10, 1 );

		if ( get_option('oxygen_vsb_enable_selector_detector') == true ) {
			add_action( 'oxygen_widget_settings_apply_after', 		array( $this, 'style_output_button' ) );
			add_action( 'ct_toolbar_component_settings',			array( $this, 'style_output_row' ) );
			add_action( 'ct_toolbar_advanced_settings',				array( $this, 'style_output_row_advanced' ), 99 );
		}
	}

	
	/**
	 * Add scripts and styles
	 *
	 * @since 1.0
	 * @author Ilya K.
	 */

	function enqueue_script() {
		
		//wp_enqueue_script( 'selector-detector-controller', 	OSD_URI . 'includes/osd.controller.js', array(), OSD_VERSION );
		wp_enqueue_script( 'selector-detector-controller', 	OSD_URI . 'includes/osd.controller.js', array() );
		wp_enqueue_style ( 'selector-detector',  		 	OSD_URI . 'includes/osd.styles.css' );
	}
	
	
	/**
	 * Include Choose Selector box HTML view
	 *
	 * @since 1.0
	 * @author Ilya K.
	 */

	function choose_selector_view() {
		require_once 'includes/views/choose-selector.view.php';
	}
	
	
	/**
	 * Include Style Sets list HTML view
	 *
	 * @since 1.0
	 * @author Ilya K.
	 */

	function list_style_sets_view() {
		require_once 'includes/views/stylesets-list.view.php';
	}

	function uncategorized_style_sets_view($parent = false) {
		require 'includes/views/uncategorized-list.view.php';
	}

	function categorized_style_sets_view($parent = false) {
		require 'includes/views/categorized-list.view.php';
	}

	
	/**
	 * Include Selectors list HTML view
	 *
	 * @since 1.0
	 * @author Ilya K.
	 */

	function list_selectors_view() {
		require_once 'includes/views/selectors-list.view.php';
	}


	/**
	 * Display style output button (add for Widgets)
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function style_output_button() {
		require 'includes/views/style-output-button.view.php';
	}
	

	/**
	 * Display style output row with button
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function style_output_row() {
		require 'includes/views/style-output-row.view.php';
	}


	/**
	 * Display style output row with button in advanced settings tab (for Inner Content)
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function style_output_row_advanced() {
		require 'includes/views/style-output-row-advanced.view.php';
	}
}

/**
 * Init Selector Detector
 */

function oxygen_vsb_selector_detector_init() {
	// Instantiate the plugin
	$oxygenSelectorDetectorInstance = new OxygenVSBSelectorDetector();
}
add_action( 'plugins_loaded', 'oxygen_vsb_selector_detector_init' );