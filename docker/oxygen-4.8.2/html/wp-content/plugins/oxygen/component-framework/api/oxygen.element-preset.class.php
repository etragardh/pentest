<?php 

/**
 * Handle everything need to setup a preset element control
 *
 * @since 2.4
 * @author Ilya K.
 */

Class OxygenElementPreset {

	private $element_obj;
	private $section;
	private $property;
	private $properties;

	private $defaults = array(
		'typography' => array(
	        'font-family'           		=> '',
	        'font-size'             		=> '',
	        'color'                 		=> '',
	        'font-weight'           		=> '',
	        'text-align'	           		=> '',
	        'line-height'           		=> '',
	        'letter-spacing'        		=> '',
	        'text-decoration'       		=> '',
	        'font-style'            		=> '',
	        'text-transform'       		 	=> '',
	    ),
	    'border_api' => array(
			"border-top-width" 				=> "",
			"border-top-style" 				=> "",
			"border-top-color" 				=> "",
				
			"border-right-width" 			=> "",
			"border-right-style" 			=> "",
			"border-right-color" 			=> "",
				
			"border-bottom-width" 			=> "",
			"border-bottom-style" 			=> "",
			"border-bottom-color" 			=> "",
				
			"border-left-width" 			=> "",
			"border-left-style" 			=> "",
			"border-left-color" 			=> "",

			// fake property
			"border-all-width" 				=> "",
			"border-all-style" 				=> "",
			"border-all-color" 				=> "",
		),
		// combine with 'border' preset?
		'border-radius' => array(
			"border-radius" 				=> "",
			"border-top-right-radius" 		=> "",
			"border-top-left-radius" 		=> "",
			"border-bottom-right-radius" 	=> "",
			"border-bottom-left-radius" 	=> "",
	    ),
	    'outline' => array(
			"outline-width" 				=> "",
			"outline-style" 				=> "",
			"outline-color" 				=> "",
		),
	    'padding_api' => array(
			'padding-top' 	 				=> "",
			'padding-left' 	 				=> "",
			'padding-right'  				=> "",
			'padding-bottom' 				=> "",
		),
		'margin' => array(
			'margin-top' 	 				=> "",
			'margin-left' 	 				=> "",
			'margin-right'  				=> "",
			'margin-bottom' 				=> "",
		),
		'box-shadow' => array(
			'box-shadow-color' 				=> "",
			'box-shadow-inset' 				=> "",
			'box-shadow-horizontal-offset' 	=> "",
			'box-shadow-vertical-offset' 	=> "",
			'box-shadow-blur'  				=> "",
			'box-shadow-spread' 			=> ""
		),
		'background' => array(
			'background-color' 				=> "",
			'background-image' 				=> "",
			'overlay-color'					=> "",
			'background-size' 				=> "",
			'background-repeat' 			=> "",
			'background-attachment' 		=> "",
			'background-position-left' 		=> "",
			'background-position-top' 		=> "",
			'background-blend-mode' 		=> "",
			'background-size-width'			=> "",
			'background-size-height'		=> "",
		)
    );

    private $default_units = array(
		'typography' => array(
	        'font-size'        				=> 'px',
	        'letter-spacing'   				=> 'px',
	    ),
	    'border_api' => array(
			"border-top-width"				=> "px",
			"border-right-width"			=> "px",
			"border-bottom-width"			=> "px",
			"border-left-width"				=> "px",
			"border-all-width"				=> "px",
		),
		// combine with 'border' preset?
		'border-radius' => array(
			"border-radius" 				=> "px",
			"border-top-right-radius" 		=> "px",
			"border-top-left-radius" 		=> "px",
			"border-bottom-right-radius" 	=> "px",
			"border-bottom-left-radius" 	=> "px",
	    ),
	    'outline' => array(
			"outline-width"					=> "px",
		),
	    'padding_api' => array(
			'padding-top' 	 				=> "px",
			'padding-left'   				=> "px",
			'padding-right'  				=> "px",
			'padding-bottom' 				=> "px",
		),
		'margin' => array(
			'margin-top' 	 				=> "px",
			'margin-left'   				=> "px",
			'margin-right'  				=> "px",
			'margin-bottom' 				=> "px",
		),
		'box-shadow' => array(
			'box-shadow-horizontal-offset'	=> "px",
			'box-shadow-vertical-offset' 	=> "px",
			'box-shadow-blur'  				=> "px",
			'box-shadow-spread' 			=> "px"
		),
		'background' => array(
			'background-position-left' 		=> "px",
			'background-position-top' 		=> "px",
			'background-size-width'			=> "px",
			'background-size-height'		=> "px",
		)
    );

	function __construct($type="", $property="", $title="", $element_obj="", $section=false, $parent_section=false) {

		// save element this control added to
		$this->element_obj = $element_obj;
		$this->section = $section;
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
			case 'padding':
				$type = 'padding_api';
				break;

			case 'border':
				$type = 'border_api';
				break;

			case 'flex-layout':
				$type = 'flex-layout-api';
				break;
		}

		$this->options = array(
							"type" 			=> $type,
							"param_name" 	=> $property,
							"heading" 		=> $title,
						);

		// save to instance
		$this->property = $property;

		// set defaults
		if ( isset($this->defaults[$type]) ) {
			$this->setValue($this->defaults[$type]);
		}
		else {
			$this->setValue(array());
		}

		// set default units
		if ( isset($this->default_units[$type]) ) {
			$this->setUnits($this->default_units[$type]);
		}
		else {
			$this->setUnits(array());
		}

		return $this;
	}


	/**
	 * Set preset values
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function setValue($values) {

		// init properties
		$this->properties = array();

		// save exact values defined by developer
		$this->options["original_values"] = $values;

		// this will be used to build controls
		$this->options["param_values"] = $values;

		// mark all as non CSS
		if (is_array($values)) {
			foreach ($values as $param_name => $param_value) {
			
				$name = $this->element_obj->prefix_option($this->property."_".$param_name);
				// define as non CSS by default
				$this->element_obj->add_to_not_css_options($name);
				// save to instance for later use
				$this->properties[] = $name;
			}
		}

		return $this;
	}


	/**
	 * Set preset units
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function setUnits($default_units="px", $available_units="") {

		$this->options["param_units"] = $available_units;
		
		if (is_array($default_units)) {
		
			foreach ($default_units as $key => $value) {

				$name = $this->property."_".$key."-unit";

				// save to instance for later use
				$this->properties[] = $name;

				$unit = array(
		                         "param_name"    => $name,
		                         "value"         => $value,
		                         "hidden"        => true,
		                    );

				if ($value && !$this->section) {
					// push unit to element controls array
					$this->element_obj->push_control($unit);
							
				}

				if ($value && $this->section) {
					// push unit to section controls array
					$this->element_obj->push_section_control($this->section, $unit, $this->parent_section);
				}

			}
		}
		else {
			// make single unit apply to all 4 properties
		}

		return $this;
	}


	/**
	 * Add all options to white list that allow to use them in classes, media queries and states
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function whiteList() {
		
		if (is_array($this->properties)) {
			foreach ($this->properties as $property) {
				$this->element_obj->add_to_white_list($property);
			}
		}

		return $this;
	}


	/**
	 * Set inset param to false to remove for that exact cotrol
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function removeInset() {

		$this->options['inset'] = false;
	}


	/**
	 * Remove Text Align control from the Typography preset
	 *
	 * @author Ilya K.
	 * @since 3.2
	 */

	public function removeTypographyAlign() {
		
		$this->options['remove_text_align'] = true;
	}


	/**
	 * Set option as a regualr CSS option that is basically shortcut to Advanced
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function CSSOption() {

		$this->options['css_option'] = true;

		if (isset($this->options["param_values"]) && is_array($this->options["param_values"])) {
			foreach ($this->options["param_values"] as $param_name => $param_value) {
		
				$name = $this->element_obj->prefix_option($this->property."_".$param_name);
				// define as non CSS by default
				$this->element_obj->remove_from_not_css_options($name);
				// remove from instance
				unset($this->properties[$name]);
			}
		}

		if (isset($this->options["original_values"])) {
			$this->options["param_values"] = $this->options["original_values"];
			$this->properties = $this->options["original_values"];
		}

		$this->unprefix();
		
		return $this;
	}


	/**
	 * Unprefix instance property
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	protected function unprefix() {

		$this->property = $this->element_obj->unprefix_option($this->property);
		$this->options["param_name"] = $this->property;
	}

	public function getValueCSS($params) {

		//$param_name = $this->options['param_name'];
		//if (isset($params[$param_name])) {
			//var_dump($params[$param_name]);
			//var_dump($this->valueCSS);
		//};
	}

}