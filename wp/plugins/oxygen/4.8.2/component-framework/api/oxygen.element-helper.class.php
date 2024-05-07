<?php

/**
 * Various helper functions to keep OxygenElement Class more clean
 * Base Class to be extended
 *
 * @since 2.4
 * @author Ilya K.
 */

Class OxygenElementHelper {

	protected $params;
	protected $defaults;
	protected $component;
	protected $css_selectors;
	protected $not_css_options;
	protected $white_list_options;
	protected $fallbackToDefaults;
	protected $AJAXControls;


	/**
	 * Sanitize element name into proper [shortcode-tag]
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	protected function generate_tag($name) {

		$tag = 'oxy-' . sanitize_title($name);
		return $tag;
	}
	

	/**
	 * Tag getter
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function get_tag() {

		return $this->params['tag'];
	}


	/**
	 * Params getter
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function getParam($name) {

		return $this->params[$name];
	}


	/**
	 * Choose a proper hook and add element button
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function addButton($show_button=true) {

		$button_place = explode("::", $this->params['button_place']);

		$priority = isset($this->params['button_priority']) ? $this->params['button_priority'] : 10;

		// Default place is Basics > Other
		$action = "ct_toolbar_fundamentals_list";
		$this->params['category'] = "Basics Other";

		/**
		 * +Add > Basics
		 */

		// ...

		/**
		 * +Add > WordPress
		 */

		if (strtolower($button_place[0])==='wordpress') {
			$action = "oxy_folder_wordpress_components";
			$this->params['category'] = "WordPress";
		}

		/**
		 * +Add > Helpers
		 */
		
		if (strtolower($button_place[0])==='helpers') {

			switch (strtolower($button_place[1])) {

				case 'composite':
					$action = "oxygen_helpers_components_composite";
					$this->params['category'] = "Helpers Composite";					
					break;

				case 'dynamic':
					$action = "oxygen_helpers_components_dynamic";
					$this->params['category'] = "Helpers Dynamic";					
					break;

				case 'interactive':
					$action = "oxygen_helpers_components_interactive";
					$this->params['category'] = "Helpers Interactive";					
					break;

				case 'external':
					$action = "oxygen_helpers_components_external";
					$this->params['category'] = "Helpers External";					
					break;
			}
		}

		else if (isset($button_place[0]) && isset($button_place[1])) {
			$action = "oxygen_add_plus_". $button_place[0] ."_". $button_place[1];
			// make element searchable
        	add_action("oxygen_add_plus_searchable_list", array( $this, "button" ) );
		}

		if ($show_button) {
    	    add_action($action, array( $this, "button" ), $priority );
    	}
    	else {
    	    remove_action($action, array( $this, "button" ), $priority );
    	}
	}


	/**
	 * Output "Apply Params" button in the bottom bar to re-render Element
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function apply_params_button() { ?>
		
		<div class="oxygen-sidebar-flex-panel"
            ng-hide="!isActiveName('<?php echo $this->get_tag(); ?>')">
			<div class="oxygen-control-row oxygen-control-row-bottom-bar">
				<a href="#" class="oxygen-apply-button"
					ng-click="iframeScope.rebuildDOM(iframeScope.component.active.id)">
					<?php _e("Apply Params", "oxygen"); ?>
				</a>
			</div>
		</div>

    <?php }


	/**
	 * Button HTML output
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function button() { 

		if (oxygen_hide_element_button($this->params['tag'])) {
			return;
		}

		$options = "{";
		if ( isset($this->params['other_options']) && 
			 isset($this->params['other_options']['rebuild_parent_on_add']) && 
			       $this->params['other_options']['rebuild_parent_on_add'] ){
			
			$options .= "'rebuild_parent':true,";
		}
		$options .= "}";

		// only parent
		$ng_if = "";
		if (isset($this->params["other_options"]['only_parent'])){
			$ng_if = 'ng_if="isActiveName(\''.$this->generate_tag($this->params["other_options"]['only_parent']).'\')||isActiveName(\''.esc_attr($this->params['tag']).'\')" ';
		}

		?>

		<div class='oxygen-add-section-element'
			<?php echo $ng_if; ?>
 			data-searchid="<?php echo strtolower( preg_replace('/\s+/', '_', sanitize_text_field( $this->params['title'] ) ) ) ?>"
 			data-searchname="<?php echo esc_attr( $this->params['title'] ); ?>"
			data-searchcat="<?php echo esc_attr( $this->params['category'] ); ?>"
			data-searchkeys="<?php echo esc_attr( $this->params['keywords'] ); ?>"
			ng-click="<?php 
			if (!isset($this->params['prefilledComponentStructure']) || !$this->params['prefilledComponentStructure']) :
			?>iframeScope.addComponent('<?php echo esc_attr( $this->params['tag'] ); ?>','','',<?php echo $options; ?>);<?php if(isset($this->params['other_options']['additional_button_js_code'])) {echo $this->params['other_options']['additional_button_js_code'];}; else : ?>iframeScope.addComponentsTree(iframeScope.componentsTemplates['<?php echo $this->params['tag']; ?>']['prefilledComponentStructure']);<?php endif;?>">
			<?php if ( isset($this->params['icon']) && $this->params['icon'] ) { ?>
			<img src='<?php echo $this->params['icon']; ?>'/>
			<?php } else { ?>
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/widgets.svg' />
			<?php } ?>
			<?php echo sanitize_text_field( $this->params['title'] ); ?>
		</div>

	<?php }


	/**
	 * Helper to create an active icon URL from regular
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	protected function active_icon($icon_url) {

		$icon_url = str_replace(".svg", "-active.svg", $icon_url);
		$icon_url = str_replace(".png", "-active.png", $icon_url);

		return $icon_url;

	}


	/**
	 * Output errors if there were any during element render process
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	protected function outputErrors() {

		if (!empty($this->errors)) {
			foreach ($this->errors as $error) {
				echo $error;
			}
		}
	}


	/**
	 * Push error text to errors array
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function add_error($error) {

		$this->errors[] = $error;
	}


	/**
	 * Add component nicename to ng-init. 
	 * ct_components_nice_names filter
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function niceName($names) {

		$name[$this->params['tag']] = $this->params['title'];

		$combined = array_merge( $names, $name );

		return $combined;
	}


	/**
	 * Add component's defaults to global defaults variable
	 * Action hook: 'ct_component_default_params'
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function defaults_callback($params=array()) {

		$defaults[$this->params['tag']] = $this->defaults;

		$combined = array_merge( $params, $defaults );

		return $combined;
	}


	/**
	 * Add this Element white list to global Oxygen list
	 * Action hook: 'oxy_options_white_list'
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function white_list_options_callback($params=array()) {

		$combined = array_merge( $params, $this->white_list_options );

		return $combined;
	}


	/**
	 * Add this Element non-css options list to global Oxygen list
	 * Action hook: 'ct_not_css_options'
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function not_css_options_callback($params=array()) {

		$params[$this->get_tag()] = $this->not_css_options;

		return $params;
	}


	/**
	 * Filter all %%name%% options and keywords in any text/code
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	protected function filterOptions($content) {

		// keywords
		if ( isset($this->params["shortcode_options"]) && isset($this->params["shortcode_options"]["selector"]) ) {
			$content = str_replace("%%ELEMENT_ID%%", $this->params["shortcode_options"]['selector'], $content);
		}

		// regular options
		$content = preg_replace_callback("/%%\S+%%/", array($this, 'replaceOptions'), $content);

		return $content;
	}


	/**
	 * preg_replace callback function to replace %%option_name%% options with actual values
	 * used by filterOptions()
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	protected function replaceOptions($matches) {

		$option = str_replace("%%", "", $matches[0]);
		$option = $this->prefix_option($option);
		$option = str_replace("-", "_", $option);

		return $this->getOptionValue($option);
	}


	/**
	 * Prefix all %%name%% options in any text/code 
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	protected function prefix_options($content) {

		// prefix regular options
		$content = preg_replace_callback("/%%\S+%%/", array($this, 'prefix_options_callback'), $content);

		// prefix keywords
		$content = preg_replace_callback("/OXY_EDITABLE\([^)]+\)/", array( $this, "prefixOxyEditableCallback"), $content);

		return $content;
	}


	/**
	 * preg_replace callback function to prefix %%option_name%% options
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	protected function prefix_options_callback($matches) {

		// don't prefix keywords
		$keywords = array("%%CONTENT%%","%%ELEMENT_ID%%");
		if (in_array($matches[0],$keywords)) {
			return $matches[0];
		}

		$option = str_replace("%%", "", $matches[0]);
		return "%%".$this->prefix_option($option)."%%";
	}


	/**
	 * Add a prefix to a single string property name
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function prefix_option($option) {

		$prefix = $this->get_tag() . '_';

		// don't prefix twice just in case
		if (strpos($option, $prefix)===0) {
			return $option;
		}

		// don't prefix empty options
		if ($option=="") {
			return $option;
		}

		return $this->params['tag'].'_'.$option;
	}


	/**
	 * Unprefix single string property name
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function unprefix_option($option) {

		$prefix = $this->get_tag() . '_';

		return str_replace($prefix, "", $option);
	}



	/**
	 * Unprefix keys in array of options
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function unprefix_options($options) {

		if (!is_array($options)) {
			return $options;
		}

		$unprefixed_array = array();
		foreach ($options as $key => $value) {
			
			$prefix = $this->get_tag()."_";
			$unprefixed_key = str_replace($prefix, "", $key);
				
			// for shortcode params we need to replace dashes to underscores
			$prefix = str_replace( "-", "_", $prefix);
			$unprefixed_key = str_replace($prefix, "", $unprefixed_key);
			
			// finally add to new array
			$unprefixed_array[$unprefixed_key] = $value;
		}

		return $unprefixed_array;
	}


	/**
	 * Base64 decode values in array of options
	 *
	 * @author Ilya K.
	 * @since 3.3
	 */

	public function base64_decode_options($options) {

		if (!is_array($options)) {
			return $options;
		}

		foreach ($options as $key => $value) {
			$options[$key] = oxygen_vsb_base64_decode($value);
		}

		return $options;
	}


	/**
	 * preg_replace callback function to prefix OXY_EDITABLE()
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	protected function prefixOxyEditableCallback($matches) {
		
		// get the value inside the parentheses
		preg_match('/\(([^)]+)\)/', $matches[0], $option);

	    if (!$option) {
	        return $matches;
	    }

	    $option = explode("|", $option[1]);

	    // default value is mandatory for OXY_EDITABLE
	    if (!$option[1]) {
	        return $matches;
	    }

	    return "OXY_EDITABLE(".$this->prefix_option($option[0])."|".$option[1].")";
	}
	

	/**
	 * preg_replace callback function to replace %%option_name%% options with actual values
	 * used by filterOptions()
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function getOptionValue($option) {

		$units = "";
		$output = "";

		// handle presets ??????
		if (isset($this->presets['typography']) && is_array($this->presets['typography']) && in_array($option, $this->presets['typography'])) {
			return $this->component->typography_to_css($this->params["shortcode_options"], $option, $this->defaults);
		}

		// check for units
		if (isset($this->params["shortcode_options"][$option."-unit"])) {
			$units = $this->params["shortcode_options"][$option."-unit"];
		}
		elseif (isset($this->defaults[$option."-unit"])) {
			$units = $this->defaults[$option."-unit"];
		}

		if (isset($this->params["shortcode_options"][$option])) {
			$replaced_option = $this->params["shortcode_options"][$option];
			$output .= $replaced_option . $units;
			return $output;
		}

		// fallback to defaults
		if ($this->fallbackToDefaults && isset($this->defaults[$option])) {
			$replaced_option = $this->defaults[$option];
			$output .= $replaced_option . $units;
			// reset
			return $output;
		}
	}


	/**
	 * Echo custom JS code with <script> wrapper
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	protected function JSOutput($js) {

		if ( trim($js) !== '' ) { 

			$js = $this->filterOptions($js);

			?><script type="text/javascript">
			<?php echo $js; ?>
			</script><?php 
		}
	}


	/**
	 * Defaults getter
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function getDefaults() {

		return $this->defaults;
	}


	/**
	 * 'oxygen_component_with_tabs' callback
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function component_with_tabs($list) {
		
		$list[] = $this->params['tag'];
		
		return $list;
	}


	/**
	 * Push option to protected white list
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function add_to_white_list($option) {
		
		$this->white_list_options[] = $option;
	}


	/**
	 * Push option to protected non-css options list
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function add_to_not_css_options($option) {

		// not css checker in component.class.php required options to be underscored
		$option = str_replace("-", "_", $option);
		
		$this->not_css_options[] = $option;
	}


	/**
	 * Remove option from protected non-css options list
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function remove_from_not_css_options($option) {

		// not css checker in component.class.php required options to be underscored
		$option = str_replace("-", "_", $option );
		$index = array_search( $option, $this->not_css_options );

		$this->not_css_options[$index] = null;
	}


	/**
	 * Push to list of the options that trigger element rebuild
	 *
	 * @author Ilya K.
	 * @since 3.2
	 */

	public function add_to_rebuild_options($option) {

		$this->rebuild_options[] = $option;
	}


	/**
	 * Push to list of the options that needs to be base64 encoded
	 *
	 * @author Ilya K.
	 * @since 3.3
	 */

	public function add_to_base64_options($option) {

		$this->base64_options[] = $option;
	}

	
	/**
	 *
	 * 
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function set_prefilled_components($value) {
		
		$this->params['prefilledComponentStructure'] = $value;
	}


	/**
	 *
	 * 
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function set_template_param($name='', $value='') {

		$this->params[$name] = $value;

		add_filter("oxy_components_html_templates", function($templates) use ($name, $value) {

			if (!isset($templates[$this->params['tag']])) {
				$templates[$this->params['tag']] = array();
			}

			$templates[$this->params['tag']][$name] = $value;

			return $templates;
		});
	}
	

	/**
	 *
	 * 
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function set_assets_path($path="") {

		$this->assets_path = $path;
	}

	/**
	 * 
	 * @author Ilya K.
	 * @since 4.1
	 */

	public function is_ajax_controls() {
		return $this->AJAXControls;
	}
}