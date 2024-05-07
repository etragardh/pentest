<?php

/**
 * Main Elements UI controls methods defined here
 *
 * @since 2.4
 * @author Ilya K.
 */

Class OxygenElementControls extends OxygenElementHelper {

	/**
	 * Add Controls Tab
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function addControlsSection($id, $title, $icon, $OxyEl=null, $parentSection=null) {

		// check parent section
		if ( is_object($parentSection) && isset($parentSection->id)) {
			$parent_section = $parentSection->id;
		}
		else {
			$parent_section = false;
		}

		$section = new OxygenElementControlsSection($id, $title, $this, $icon, $OxyEl, $parent_section);

		// no way for 3 levels deep
		if ($parent_section && !isset($this->params['tabs'][$parent_section])) {
			throw new Exception("It is not possible to insert a section into $parent_section. Two levels deep is maximum.");
		}


		if ($parent_section) {

			// init tabs array
			if (!isset($this->params['tabs'][$parent_section]['tabs'])) {
				$this->params['tabs'][$parent_section]['tabs'] = array();
			}

			// push control to array
			$this->params['tabs'][$parent_section]['tabs'][$id] = 
						array(
							"heading" 		=> $title,
							"icon" 			=> $icon,
							"params" 		=> array(),
						);
		}
		else {

			// init tabs array
			if (!isset($this->params['tabs'])) {
				$this->params['tabs'] = array();
			}

			// push control to array
			$this->params['tabs'][$id] = 
						array(
							"heading" 		=> $title,
							"icon" 			=> $icon,
							"params" 		=> array(),
						);
		}

		// add to the list of tabs
		add_filter("oxygen_component_with_tabs", array( $this, "component_with_tabs"));

		return $section;
	}


	/**
	 * Add Control to specified Section
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function add_section_control($section, $type="", $property="", $title="", $parent_section="") {

		$control = new OxygenElementControl($type, $property, $title, $this, $section, $parent_section);
		
		// push control to tabs array
		if ($parent_section) {
			$this->params['tabs'][$parent_section]['tabs'][$section]['params'][] = $control;
		}
		else {
			$this->params['tabs'][$section]['params'][] = $control;
		}

		return $control;
	}


	/**
	 * Add Preset to specified Section
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function add_section_preset($section, $type="", $property="", $title="", $parent_section="") {

		$control = new OxygenElementPreset($type, $property, $title, $this, $section, $parent_section);
		
		if ($parent_section) {
			$this->params['tabs'][$parent_section]['tabs'][$section]['params'][] = $control;
		}
		else {
			// push control to tabs array
			$this->params['tabs'][$section]['params'][] = $control;
		}
		
		return $control;
	}


	/**
	 * Add Control to Primary section
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function addControl($type="", $property="", $title="") {

		if (empty($type)) {
			$this->add_error("<br/><b>Warning:</b> ".$this->params['title']." addControl() missing 'type' parameter.");
			return;
		}

		if (empty($property) && $type!="custom_control") {
			$this->add_error("<br/><b>Warning:</b> ".$this->params['title']." addControl() missing 'property' parameter.");
			return;
		}

		$control = new OxygenElementControl($type, $property, $title, $this);

		// push control to array
		$this->params['controls'][] = $control;
	
		return $control;
	}


	/**
	 * Add Preset to Primary section
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function addPreset($type="", $property="", $title="", $selector="") {

		if (empty($type)) {
			$this->add_error("<br/><b>Warning:</b> ".$this->params['title']." addPreset() missing 'type' parameter.");
			return;
		}

		if (empty($property)) {
			$this->add_error("<br/><b>Warning:</b> ".$this->params['title']." addPreset() missing 'property' parameter.");
			return;
		}

		$preset = new OxygenElementPreset($type, $property, $title, $this);

		// push preset to controls array
		$this->params['controls'][] = $preset;

		// auto register selector if defined
		if (!empty($selector)) {

			// make it possible to define comma separted selectors 
	        $selectors = explode(",", $selector);

	        if (is_array($selectors)) {
	            foreach ($selectors as $selector) {
	        
	                $selectorObj = $this->registerCSSSelector($selector);
	                $selectorObj->mapPreset(
	                    $type,
	                    $this->prefix_option($property)
	                );
	            }
	        }
		}
	
		return $preset;
	}


	/**
	 * Way to push controls from OxygenElementControl Class, not for public
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function push_control($control) {

		$this->params['controls'][] = $control;	
	}


	/**
	 * Way to push controls from OxygenElementControl Class, not for public
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function push_section_control($section, $control, $parent_section=false) {

		if ($parent_section) {
			$this->params['tabs'][$parent_section]['tabs'][$section]['params'][] = $control;
		}
		else {
			$this->params['tabs'][$section]['params'][] = $control;
		}

	}


	/**
	 * Return all controls objects params in a single array
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	protected function get_controls() {

		$controls = array();

		foreach ($this->params['controls'] as $key => $value) {
			if (is_object($value)) {
				$controls[] = $value->options;
			}
			else {
				$controls[] = $value;
			}
		}

		return $controls;
	}


	/**
	 * Return all section controls objects params in a single array
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	protected function get_section_controls($params=false) {

		$tabs = array();

		if (!$params && isset($this->params['tabs'])) {
			$params = $this->params['tabs'];
		}

		if ( is_array($params) ) {

			foreach ($params as $key => $tab) {
				$tabs[$key] = $tab;
				$tabs[$key]['params'] = array();
				if (isset($tab['params']) && is_array($tab['params'])) {
					foreach ($tab['params'] as $value) {
						if (is_object($value)) {
							$tabs[$key]['params'][] = $value->options;
						}
						else {
							$tabs[$key]['params'][] = $value;
						}
					}
				}
				if ( isset($tab['tabs']) ) {
					$tabs[$key]['tabs'] = $this->get_section_controls($tab['tabs']);
				}
			}
		}

		return $tabs;
	}


	/**
	 * Get all Sections OxygenElementControl objects in a plain array
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	protected function get_section_controls_objects($params=false) {

		$controls = array();

		if (!$params && isset($this->params['tabs'])) {
			$params = $this->params['tabs'];
		}

		if ( is_array($params) ) {

			foreach ($params as $key => $tab) {
				if (isset($tab['params']) && is_array($tab['params'])) {
					foreach ($tab['params'] as $value) {
						if (is_object($value)) {
							$controls[] = $value;
						}
					}
				}
				if ( isset($tab['tabs']) ) {
					$controls = array_merge($controls, $this->get_section_controls_objects($tab['tabs']));
				}
			}
		}

		return $controls;
	}


	/**
	 * Output all added controls
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function controlsReady() {

		if ( empty($this->params['controls']) && empty($this->params['tabs']) ) {
//			$this->add_error("<br/><b>Warning:</b> no contorls found for controlsReady()");
			return;
		}

		$controls = $this->get_controls();
		$section_controls = $this->get_section_controls();

		// update CT_Component object
		$this->component->params 	= $controls;
		$this->component->tabs 		= $section_controls;


		if ($this->AJAXControls !== true) {
			add_action("ct_toolbar_component_settings", function() {
				$this->output_controls();
			});
		}

		// generate all the defaults based on added controls and tabs, don't fallback to any advanced defaults yet, do it later
		$this->defaults = array_merge(
			CT_Component::get_default_params_static($controls, array(), $section_controls),
			$this->defaults );

		// save initial defaults in instance
		$this->initial_defaults = $this->defaults;

		// fix to override static CT_Component advanced defaults
		// this one easily done when instantinate CT_Component, but needs this fix when used trough API
		$advanced = $this->component->options['advanced'];
		$this->component->options['advanced']['other'] = array("values" => $this->defaults);

		// fallback to advanced defaults
		$this->defaults = array_merge(
			CT_Component::get_default_params_static($controls, $advanced, $section_controls),
			$this->defaults );

		// output defaults in builder
		add_filter("ct_component_default_params", array( $this, "defaults_callback") );
	}

	/**
	 * Setup everything for AJAX controls
	 *
	 * @author Ilya K.
	 * @since 3.0
	 */

	function useAJAXControls() {

		$this->AJAXControls = true;

		add_action('wp_ajax_oxy_load_controls_'.$this->params['tag'], array( $this, 'ajax_load_controls') );
	}
	

	/**
	 * Print controls out
	 *
	 * @author Ilya K.
	 * @since 3.0
	 */

	function output_controls() {

		// if we have tabs added for this component don't show regular params if any tab is opened 		
		if (isset($this->params['tabs']) && is_array($this->params['tabs']) && !empty($this->params['tabs'])) {
			$tabs = "&&!hasOpenTabs('".esc_attr( $this->params['tag'] )."')";
		}
		else {
			$tabs = "";
		}

		$options = array('tag'=>$this->get_tag());

		// regular controls
		echo '<div ng-if="isActiveName(\'' . $this->get_tag() . '\')'.$tabs.'">';
		$this->component->component_params($this->get_controls(), $this->get_tag(), $options);
		echo '</div>';

		// controls sections
		if (!empty($this->params['tabs'])) {
			echo $this->component->component_tabs($this->get_section_controls(), $this->get_tag(), $this->params['title'], $options);
		}
	}

	/**
	 * Callback function to output all added controls in response to AJAX request
	 *
	 * @author Ilya K.
	 * @since 3.0
	 */

	function ajax_load_controls() {

		// initialize Toolbar class
		require_once(CT_FW_PATH."/toolbar/toolbar.class.php");
		global $oxygen_toolbar;
		$oxygen_toolbar = new CT_Toolbar();

		// don't add these in our controls
		remove_all_actions("ct_subtab_level_1_component_settings");

		$this->output_controls();

		die();
	}

}