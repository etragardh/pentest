<?php 

/**
 * Handle everything need to setup a single element control
 *
 * @since 2.4
 * @author Ilya K.
 */

Class OxygenElementControl {

	private $element_obj;
	private $section;
	private $property;
	private $valueCSS;

	function __construct($type="", $property="", $title="", $element_obj="", $section=false, $parentSection=false) {

		// save element this control added to
		$this->element_obj = $element_obj;
		$this->section = $section;
		
		// check parent section
		if ( is_object($parentSection) && isset($parentSection->id)) {
			// get string id from object
			$parent_section = $parentSection->id;
		}
		else if (is_string($parentSection)){
			// string id passed
			$parent_section = $parentSection;
		}
		else {
			// no parent section
			$parent_section = false;
		}

		// save to global
		$this->parent_section = $parent_section;

		// prefix all options
		$property = $this->element_obj->prefix_option($property);

		// save all added presets to parse later
		if ($type=='typography') {
			if (!isset($this->element_obj->presets['typography'])) {
				$this->element_obj->presets['typography'] = array();
			}
			$this->element_obj->presets['typography'][] = $property;
			// TODO: check if we still need this
		}

		// map types names from Elements API to internal Oxygen
		switch ($type) {
			case 'buttons-list':
				$type = 'radio';
				break;
		}

		$this->options = array(
							"type" 			=> $type,
							"param_name" 	=> $property,
							"heading" 		=> $title,
						);

		// all options are non-CSS by default
		$css_option = false;

		// save to instance
		$this->property = $property;

		if (!$css_option) {
			$this->element_obj->add_to_not_css_options($property);
		}
		else {
			// may be some controls will be CSS options by default
			$this->element_obj->add_to_white_list($this->property);
			$this->unprefix();
		}

		// keep "html_tag" param unprefixed
		if ($type=="tag") {
			$this->unprefix();
		}

		return $this;
	}


	/**
	 * Set control value
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function setValue($value) {

		// if not an associative array copy values to keys
		if (is_array($value) && !$this->is_assoc($value)) {
			$value = array_combine($value, $value);
		}
		
		$this->options["value"] = $value;

		return $this;
	}


	/**
	 * Set control value
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function setValueCSS($value) {
		
		$this->valueCSS = $value;
		$this->element_obj->valuesCSS[$this->property] = $value;

		return $this;
	}


	/**
	 * Set control range, used in slider-measurebox
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function setRange($min, $max, $step=false) {

		$this->options["min"] = $min;
		$this->options["max"] = $max;
		
		if ($step!==false) {
			$this->options["step"] = $step;
		}

		return $this;
	}


	/**
	 * Set specific default value if needed
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function setDefaultValue($value) {

		$this->options["default"] = $value;

		return $this;
	}


	/**
	 * Field descriptions to be output below the field label
	 *
	 * @author Ilya K.
	 * @since 3.3
	 */

	public function setDescription($value) {

		$this->options["description"] = $value;

		return $this;
	}


	/**
	 * Set specific default value if needed
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function setCondition($condition) {


					if ( strpos( $condition, "&&" ) > -1 ) {
						$conditions = explode("&&", $condition);
						
						if (is_array($conditions)) {

							foreach ($conditions as $key => $value) {
								
								if ( strpos( $value, "!=" ) > -1 ) {

									$condition = explode("!=", $value);
									$conditions[$key] = $this->element_obj->prefix_option($condition[0]) . "!=" . $condition[1];
								}
								else {
									$condition = explode("=", $value);
									$conditions[$key] = $this->element_obj->prefix_option($condition[0]) . "=" . $condition[1];
								}
							}
						}

						$condition = implode("&&", $conditions);
					}
					else
					if ( strpos( $condition, "||" ) > -1 ) {
						$conditions = explode("||", $condition);
						
						if (is_array($conditions)) {
							$ng_show = 'ng-show="';

							foreach ($conditions as $key => $value) {
								
								if ( strpos( $value, "!=" ) > -1 ) {

									$condition = explode("!=", $value);
									$conditions[$key] = $this->element_obj->prefix_option($condition[0]) . "!=" . $condition[1];
								}
								else {
									$condition = explode("=", $value);
									$conditions[$key] = $this->element_obj->prefix_option($condition[0]) . "=" . $condition[1];
								}
							}
						}

						$condition = implode("||", $conditions);

					}
					else
					if ( strpos( $condition, "!=" ) > -1 ) {

						$condition = explode("!=", $condition);
						$condition = $this->element_obj->prefix_option($condition[0]) . "!=" . $condition[1];
					}
					else {
						$condition = explode("=", $condition);
						$condition = $this->element_obj->prefix_option($condition[0]) . "=" . $condition[1];
					}

		$this->options["condition"] = $condition;
	}


	/**
	 * Set control units
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function setUnits($default_unit="px", $available_units="") {

		$this->options["param_units"] = $available_units;

		$unit = array(
                         "param_name"    => $this->options['param_name']."-unit",
                         "value"         => $default_unit,
                         "hidden"        => true,
                    );

		if ($default_unit && !$this->section) {
			// push unit to element controls array
			$this->element_obj->push_control($unit);
					
		}

		if ($default_unit && $this->section) {
			// push unit to section controls array
			$this->element_obj->push_section_control($this->section, $unit, $this->parent_section);
		}

		return $this;
	}


	public function setHTML($html) {

		$this->options["html"] = $html;
	}


	/**
	 * Add option to white list that allow to use it in classes, media queries and states
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function whiteList() {

		$this->element_obj->add_to_white_list($this->property);
		$this->element_obj->add_to_white_list($this->property.'-unit');
	
		return $this;
	}


	/**
	 * Set option as a regualr CSS option that is basically shortcut to Advanced
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function CSSOption() {

		$this->element_obj->remove_from_not_css_options($this->property);
		$this->unprefix();
		$this->options['css_option'] = true;
	
		return $this;
	}


	/**
	 * Unprefix instance property
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function unprefix() {

		$this->property = $this->element_obj->unprefix_option($this->property);
		$this->options["param_name"] = $this->property;
	}


	/**
	 * Check whether array is associative or sequential
	 *
	 * https://stackoverflow.com/a/173479/2198798
	 * @since 2.4
	 */

	protected function is_assoc(array $arr) {
    	if (array() === $arr) return false;
    	return array_keys($arr) !== range(0, count($arr) - 1);
	}
	

	/**
	 * Return CSS snippet attached to option value
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function getValueCSS($params) {

		$param_name = $this->options['param_name'];
		if ( is_array($this->valueCSS) &&
			 isset($params[$param_name]) && 
			 isset($this->valueCSS[$params[$param_name]]) 
			) {

			return $this->valueCSS[$params[$param_name]];
		};

		return "";
	}


	/**
	 * Don't output HTML for this control
	 *
	 * @author Ilya K.
	 * @since 3.1
	 */

	public function hidden() {
		$this->options['hidden'] = true;
	}


	/**
	 * Use to set various params that doesn't have its own setter method
	 *
	 * @author Ilya K.
	 * @since 3.2
	 */

	public function setParam($key, $value) {
		$this->options[$key] = $value;
	}


	/**
	 * When this option is changed in builder send an AJAX request to rebuild the component
	 *
	 * @author Ilya K.
	 * @since 3.2
	 */

	public function rebuildElementOnChange() {
		
		$this->element_obj->add_to_rebuild_options($this->property);

		return $this;
	}


	/**
	 *
	 * @author Ilya K.
	 * @since 3.3
	 */
	
	public function base64() {

		$this->element_obj->add_to_base64_options($this->property);

		return $this;
	}


}