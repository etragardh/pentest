<?php 

/**
 * Class to handle registered selectors to style Elements API
 *
 * @since 2.4
 * @author Ilya K.
 */

Class OxygenSelector {

	private $element_obj;
	private $selector;
	private $properties;

	function __construct($selector, $element_obj) {

		$this->element_obj 	= $element_obj;
		$this->selector 	= $selector;
		$this->properties 	= array();
	}


	/**
	 * Selector getter
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function getSelector() {

		return $this->selector;
	}


	/**
	 * Map a CSS property to settings name
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function mapProperty($css_property, $element_setting) {

		$css_properties = explode("|", $css_property);

		if (is_array($css_properties)) {
			foreach ($css_properties as $css_property) {
				$this->properties[$css_property] = $element_setting;
			}
		}
	}


	/**
	 * Map a CSS property to settings name
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function mapPreset($css_property, $element_setting) {

		$this->properties[$css_property] = $element_setting;
	}


	/**
	 * Generate CSS output
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function generateCSS($params, $state="") {

		global $fake_properties;

		$defaults = $this->element_obj->getDefaults();

		$css = "";

		$complexProperties = isset($this->element_obj->complexProperties) ? $this->element_obj->complexProperties : array();

		foreach ($this->properties as $css_property => $element_setting) {

			// prefix user defined options
			$element_setting_name = $this->element_obj->prefix_option($element_setting);

			$value = isset($params[$element_setting_name]) ? $params[$element_setting_name] : "";
			// check for units
			$unit = "";
			if (isset($params[$element_setting_name."-unit"])) {
				$unit = $params[$element_setting_name."-unit"];
			}
			elseif (isset($defaults[$element_setting_name."-unit"])) {
				$unit = $defaults[$element_setting_name."-unit"];
			}

			// handle typography
			if ($css_property=='typography'){
				$css .= CT_Component::typography_to_css($params, $element_setting_name, $defaults);
			}
			else if ($css_property=='box-shadow') {
				$css .= CT_Component::box_shadow_css($params, $element_setting_name, $defaults);
			}
			// complex selector i.e. box-shadow: {a} {b} {c} {d};
			// complex property syntax is 'box-shadow>a'
			else if ( strpos( $css_property, ">") !== false ) {

				// lets separate this into property and location: 'box-shadow' and 'a'
				$parts = explode('>', $css_property);
											
				if ($complexProperties && $complexProperties[$parts[0]]) {

					$complexProperties[$parts[0]] = str_replace("{".$parts[1]."}", $this->addUnit($value, $unit), $complexProperties[$parts[0]]);
				}
			}
			else if ($value!="") {

				if ($css_property == "font-family") {
					$value = oxy_get_global_font_name( $value );
					if (strpos($value, ",") === false && strtolower($value) !== "inherit") {
						$value = "'$value'";
					}
				}
				$value = $this->addUnit(oxygen_vsb_get_global_color_value($value), $unit);

				// hardcode this for now
				if ($css_property == "transform:rotate") {
					$css_property = "transform";
					$value = "rotate($value)";
				}

				if ($css_property == "background-image:url") {
					$css_property = "background-image";
					$value = "url($value)";
				}

				// plain options
				$css .= $css_property . ":" . $value . ";\r\n";
			}
			else {
				// other presets
				// unprefix params
				$unprefixed_params = array();
				foreach ($params as $key => $value) {
					if (strpos($key, $element_setting_name)===0 && strpos($key, "-unit")===false) {

						// check for units
						$unit = "";
						if (isset($params[$key."-unit"])) {
							$unit = $params[$key."-unit"];
						}
						elseif (isset($defaults[$key."-unit"])) {
							$unit = $defaults[$key."-unit"];
						}

						// unprefix property from Element name and Preset name
						$name = str_replace($element_setting_name . "_", "", $key);

						$unprefixed_params[$name] = $this->addUnit($value, $unit);
					}
				}

				// handle background-position option
				if ( (isset($unprefixed_params['background-position-left']) && $unprefixed_params['background-position-left']) || 
					 (isset($unprefixed_params['background-position-top'])  && $unprefixed_params['background-position-top']) ) {

					$left = (isset($unprefixed_params['background-position-left']) && 
								   $unprefixed_params['background-position-left']) ? 
								   $unprefixed_params['background-position-left'] : "0%";
					
					$top  = (isset($unprefixed_params['background-position-top']) && 
								   $unprefixed_params['background-position-top']) ? 
								   $unprefixed_params['background-position-top'] : "0%";
					
					$unprefixed_params['background-position'] = $left . " " . $top;

					unset($unprefixed_params['background-position-left']);
					unset($unprefixed_params['background-position-top']);
				}

				// handle background-size option
				if ( isset($unprefixed_params['background-size']) && $unprefixed_params['background-size'] == "manual" ) {

					$width  = isset($unprefixed_params['background-size-width'])  ? $unprefixed_params['background-size-width'] : "auto";
					$height = isset($unprefixed_params['background-size-height']) ? $unprefixed_params['background-size-height'] : "auto";
					
					$unprefixed_params['background-size'] = $width . " " . $height;

					unset($unprefixed_params['background-size-width']);
					unset($unprefixed_params['background-size-height']);
				}

				foreach ($unprefixed_params as $name => $value) {

						if ($name == "font-family") {
							
							$value = oxy_get_global_font_name( $value );
							if (strpos($value, ",") === false && strtolower($value) !== "inherit") {
								$value = "'$value'";
							}
						}

						if ($name == "background-image") {
							$value = "url($value)";
						}

						// skip fake properties
						if (is_array($fake_properties) && in_array( $name, $fake_properties ) ) {
							continue;
						}

						if ($value==="") {
							continue;
						}

						// finally add property:value
						$css .= $name . ":" . oxygen_vsb_get_global_color_value($value) . ";\r\n";
				}

			}
		}

		// check if there are complex properties to add
		foreach ($complexProperties as $property => $value) {
			
			// do not add property unless all parts replaced
			if (strpos($value, "{")!==false) {
				continue;
			}

			// finally add property:value
			$css .= $property . ":" . $this->addUnit(oxygen_vsb_get_global_color_value($value), $unit) . ";\r\n";
		}

		$styles = false;

		if (!empty($css)) {
			$styles = $this->selector . $state . "{\r\n";
			$styles .= $css;
			$styles .= "}\r\n";
		}

		return $styles;
	}


	/**
	 * Generate simple property:setting map array 
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function propertiesArray() {

		$array = [];

		foreach ($this->properties as $css_property => $element_setting) {

			// prefix user defined options
			$element_setting_name = $this->element_obj->prefix_option($element_setting);
			
			$array[$css_property] = $element_setting_name; 
		}

		return $array;
	}


	/**
	 * Safely add units to prevent duplication
	 *
	 * @author Ilya K.
	 * @since 3.2
	 */

	public function addUnit($value, $unit) {

		if ($value !== "" && $unit && strpos($value, $unit)===false) {
			$value .= $unit;
		}

		return $value;
	}

}