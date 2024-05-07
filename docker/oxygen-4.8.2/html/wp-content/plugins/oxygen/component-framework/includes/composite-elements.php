<?php

Class OxygenCompositeElements {

	public $composite_elements;

	function __construct() {

		$this->composite_elements = $this->get_composite_elements();

		if ( isset( $this->composite_elements->error ) ) {
			// do we need a way to show error message somewhere?
			return;
		}

		if ( !isset( $this->composite_elements->components ) || !is_array( $this->composite_elements->components ) ) {
			return;
		}

		add_action("oxygen_basics_components_containers", 	array( $this, "buttons_basic_containers"), 20);
		add_action("oxygen_basics_components_text", 		array( $this, "buttons_basic_text"), 20);
		add_action("oxygen_basics_components_links", 		array( $this, "buttons_basic_links"), 20);
		add_action("oxygen_basics_components_visual", 		array( $this, "buttons_basic_visual"), 20);
		add_action("ct_toolbar_fundamentals_list", 			array( $this, "buttons_basic_other"), 20);
		add_action("oxygen_helpers_components_composite", 	array( $this, "buttons_helpers_composite"), 20);
		add_action("oxygen_helpers_components_dynamic", 	array( $this, "buttons_helpers_dynamic"), 20);
		add_action("oxygen_helpers_components_interactive", array( $this, "buttons_helpers_interactive"), 20);
		add_action("oxygen_helpers_components_external", 	array( $this, "buttons_helpers_external"), 20);
		add_action("oxy_folder_wordpress_components", 		array( $this, "buttons_wordpress"), 20);
		add_action("oxygen_add_plus_woo_single", 			array( $this, "buttons_woo_single"), 20);
		add_action("oxygen_add_plus_woo_archive", 			array( $this, "buttons_woo_archive"), 20);
		add_action("oxygen_add_plus_woo_page", 				array( $this, "buttons_woo_page"), 20);
		add_action("oxygen_add_plus_woo_other", 			array( $this, "buttons_woo_other"), 20);

		// Make WooCo Composite Elements searchable
		add_action("ct_toolbar_components_list_searchable",	array( $this, "buttons_woo_single"), 20);
		add_action("ct_toolbar_components_list_searchable",	array( $this, "buttons_woo_archive"), 20);
		add_action("ct_toolbar_components_list_searchable",	array( $this, "buttons_woo_page"), 20);
		add_action("ct_toolbar_components_list_searchable",	array( $this, "buttons_woo_other"), 20);
	}


	function buttons($location) {
	
		$design_name = "composite-elements";

		foreach ( $this->composite_elements->components as $key => $element ) : 
			
			if ( !isset($element->min_version) || version_compare(CT_VERSION, $element->min_version) === -1 ) {
				continue;
			}

			if (oxygen_hide_element_button($element->id."-".$element->page)) {
				continue;
			}

			if ( isset($element->location) && $element->location == $location ) : ?>
			<div class='oxygen-add-section-element oxygen-add-composite-element'
				data-searchid="<?php echo strtolower( preg_replace('/\s+/', '_', sanitize_text_field( $element->name ) ) ) ?>"
				data-searchname="<?php echo esc_attr( $element->name ); ?>"
				data-searchcat="<?php echo esc_attr( $location ); ?>"
				ng-click="iframeScope.showAddItemDialog(<?php echo $element->id; ?>, 'component', '0', '', '<?php echo $element->source; ?>', <?php echo $element->page; ?>, '<?php echo $element->name; ?>', '<?php echo $element->category; ?>', '<?php echo $design_name; ?>')">
				<?php if ( isset($element->icon_url) ) : ?>
				<img src='<?php echo str_replace(array('http://','https://'), "//", $element->icon_url); ?>' />
				<img src='<?php echo str_replace(array('http://','https://'), "//", $element->icon_url); ?>' />
				<?php else: ?>
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/widgets.svg' />
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/widgets-active.svg' />
				<?php endif; ?>
				<?php echo ( isset($element->name) ) ? sanitize_text_field( $element->name ) : __("No name element", "oxygen"); ?>
			</div>
		<?php endif;
		endforeach;
	}

	function buttons_basic_containers () {
		$this->buttons("basic_containers");
	}


	function buttons_basic_text () {
		$this->buttons("basic_text");
	}


	function buttons_basic_links () {
		$this->buttons("basic_links");
	}


	function buttons_basic_visual () {
		$this->buttons("basic_visual");
	}


	function buttons_basic_other () {
		$this->buttons("basic_other");
	}


	function buttons_helpers_composite () {
		$this->buttons("helpers_composite");
	}


	function buttons_helpers_dynamic () {
		$this->buttons("helpers_dynamic");
	}


	function buttons_helpers_interactive () {
		$this->buttons("helpers_interactive");
	}


	function buttons_helpers_external () {
		$this->buttons("helpers_external");
	}


	function buttons_wordpress () {
		$this->buttons("wordpress");
	}

	
	function buttons_woo_single () {
		$this->buttons("woo_single");
	}


	function buttons_woo_archive () {
		$this->buttons("woo_archive");
	}


	function buttons_woo_page () {
		$this->buttons("woo_page");
	}


	function buttons_woo_other () {
		$this->buttons("woo_other");
	}

	function get_composite_elements() {

		$access_key  = "4zccZ9B5QyZg"; // to find it you should base64_decode Site Key setting from Oxygen > Settings > Library
		$desgin_url  = "https://elements.oxy.host";

		// ilya's local server
		//$access_key  = "1koQttxtVeKa";
		//$desgin_url  = "http://oxygen-server.test";

		$composite_license_key = get_option("oxygen_composite_elements_license_key");
		$oxygen_license_key = get_option("oxygen_license_key");

		$composite_license_status = get_option("oxygen_composite_elements_license_status");
		$oxygen_license_status = get_option("oxygen_license_status");

		if (!$composite_license_key && !$oxygen_license_key) {
			return array();
		}

		if ($composite_license_status!=='valid' && !$oxygen_license_key) {
			return array();
		}

		if ($composite_license_status!=='valid' && !oxygen_vsb_is_composite_elements_agency_bundle()) {
			return array();
		}

		// only ping server license check if local checks are OK
		$args = array(
			'headers' => array(
				'oxygenclientversion' => '3.7rc1',
				'compositeelementslicensekey' => $composite_license_key,
				'oxygenlicensekey' => $oxygen_license_key,
				'compositeelementssiteurl' => OxygenCompositeElementsPluginUpdater::clean_site_url(home_url()),
				'auth' => md5($access_key)
			),
			'timeout' => 30,
		);

		$response = wp_remote_request( $desgin_url . '/wp-json/oxygen-vsb-connection/v1/items', $args );
		$status   = wp_remote_retrieve_response_code( $response );

		if ( is_wp_error( $response ) ) {
			return array(
				'error' => $response->get_error_message(),
			);
		} 
		else if ( $status !== 200 ) {
			return array(
				'error' => wp_remote_retrieve_response_message( $response ),
			);
		}

		if ( is_array( $response ) && isset( $response['body'] ) ) {
			$elements = json_decode( $response['body'] );
			return $elements;
		}

		return array();
	}
}


function run_oxygen_composite_elements() {

	if (defined('SHOW_CT_BUILDER') && !defined('OXYGEN_IFRAME')) {
		$OxygenCompositeElements = new OxygenCompositeElements();
	}
}
add_action('init', "run_oxygen_composite_elements");