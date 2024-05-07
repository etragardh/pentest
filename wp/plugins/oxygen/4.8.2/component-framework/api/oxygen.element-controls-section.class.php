<?php 

/**
 * Class to handle Controls Section via Elements API
 *
 * @since 2.4
 * @author Ilya K.
 */

Class OxygenElementControlsSection {

	public $id;
	public $element_obj;

	function __construct($id, $title, $element_obj, $icon="", $OxyEl=null, $parent_section="") {

		$this->id = $id;
		$this->element_obj = $element_obj;
		$this->OxyEl = $OxyEl;
		$this->parent_section = $parent_section;
	}


	/**
	 * Add Section Control
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function addControl($type, $property="", $title="") {

		return $this->element_obj->add_section_control($this->id, $type, $property, $title, $this->parent_section);
	}


	/**
	 * Add Preset
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function addPreset($type, $property="", $title="", $selector="") {	

		$preset = $this->element_obj->add_section_preset($this->id, $type, $property, $title, $this->parent_section);

		// auto register selector if defined
		if (!empty($selector)) {

	        $selectorObj = $this->element_obj->registerCSSSelector($selector);
	        $selectorObj->mapPreset(
	            $type,
	            $this->element_obj->prefix_option($property)
	        );
		}

		return $preset;
	}


	/**
	 * Wrapper to OxyEl function
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function addStyleControl($params) {

		return $this->OxyEl->addStyleControl($params, $this);
	}


	/**
	 * Wrapper to OxyEl function
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function addStyleControls($params) {

		$this->OxyEl->addStyleControls($params, $this);
	}


	/**
	 * Wrapper to OxyEl function
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function addOptionControl($params) {

		return $this->OxyEl->addOptionControl($params, $this);
	}


	/**
	 * Wrapper to OxyEl function
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function addCustomControl($html, $property="") {

		return $this->OxyEl->addCustomControl($html, $property, $this);
	}


	/**
	 * Wrapper to OxyEl function
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function addTagControl() {

		return $this->OxyEl->addTagControl($this);
	}

	
	/**
	 * Wrapper to OxyEl typographySection function
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */
	
	public function typographySection($name, $selector, $OxyEl=null) {

		if (!is_object($OxyEl)) {
			throw new Exception("\$OxyEl parameter missing for typographySection()");
		}
		
		return $OxyEl->typographySection($name, $selector, $OxyEl, $this);
	}


	/**
	 * Wrapper to OxyEl borderSection function
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */
	
	public function borderSection($name, $selector, $OxyEl=null) {

		if (!is_object($OxyEl)) {
			throw new Exception("\$OxyEl parameter missing for borderSection()");
		}
		
		return $OxyEl->borderSection($name, $selector, $OxyEl, $this);
	}


	/**
	 * Wrapper to OxyEl boxShadowSection function
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */
	
	public function boxShadowSection($name, $selector, $OxyEl=null, $section=null, $inset=true) {

		if (!is_object($OxyEl)) {
			throw new Exception("\$OxyEl parameter missing for boxShadowSection()");
		}
		
		return $OxyEl->boxShadowSection($name, $selector, $OxyEl, $this, $inset);
	}


	/**
	 * Wrapper to OxyEl addControlSection function
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */
	
	public function addControlSection($a, $b, $c, $OxyEl=null) {

		if (!is_object($OxyEl)) {
			throw new Exception("\$OxyEl parameter missing for addControlSection()");
		}
		
		return $OxyEl->addControlSection($a, $b, $c, $OxyEl, $this);
	}


	/**
	 * Wrapper to OxygenElementControls::addControlSection function
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */
	
	public function addControlsSection($id, $title, $icon, $OxyEl=null, $parentSection=null) {
		
		return $this->element_obj->addControlsSection($id, $title, $icon, $OxyEl, $this);
	}


	/**
	 * Wrapper to OxyEl flex function
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */
	
	public function flex($selector, $OxyEl=null) {

		if (!is_object($OxyEl)) {
			throw new Exception("\$OxyEl parameter missing for boxShadowSection()");
		}
		
		return $OxyEl->flex($selector, $this);
	}

}