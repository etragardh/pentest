<?php

use GuzzleHttp\Promise\Is;

/**
 * Main Elements API Class to create Oxygen elements 
 *
 * @since 2.4
 * @author Ilya K.
 */


Class OxygenElement extends OxygenElementControls {

	public $defaults;
	
	function __construct($title, $id, $file='', $icon='', $button_place='', $other_options=array(), $has_js = false) {

		$this->defaults 			= array();
		$this->white_list_options 	= array();
		$this->not_css_options 		= array();
		$this->rebuild_options		= array();
		$this->fallbackToDefaults 	= true;
		$this->valuesCSS			= array();
		$this->assets_path 			= "";
		$this->base64_options		= array();

		$this->scripts 				= array();
		$this->styles 				= array();

		$this->oxygen_id_styles_output = array();

		// set params
		$this->presets 					= array();
		$this->params['controls'] 		= array();
		$this->params['title'] 			= $title;
		$this->params['icon'] 			= $icon;
		$this->params['button_place'] 	= $button_place;
		$this->params['button_priority']= isset($other_options['button_priority']) ? $other_options['button_priority'] : 10;
		$this->params['other_options'] 	= $other_options;
		$this->params['has_js'] = $has_js;
		$this->params['custom_attributes'] = array();

		if ($file!=='') {
			$this->params['dir_path'] 	= plugin_dir_path( $file );
			$this->params['dir_url'] 	= plugin_dir_url( $file );
		}
		else {
			$this->params['dir_path'] 	= "";
			$this->params['dir_url'] 	= "";
		}

		// default HTML tag is div
		$this->params['html_tag'] 		= "div";

		// set shortcode tag ourselves and don't let developers do this
		$this->params['tag'] = $this->generate_tag($id);
		
		// add shortcodes
        add_shortcode($this->params['tag'], array($this, 'shortcode'));
        add_oxygen_element($this->params['tag'], array($this, 'shortcode'));

        // +Add section button
       	$this->addButton();

       	// create old CT_Component instance to use some methods
       	$this->component = new CT_Component( 
			array( 
				'name' => $this->params['title'],
				'tag'  => $this->params['tag'],
			)
		);

		global $oxygen_vsb_components;
		$oxygen_vsb_components[$this->params['tag']] = $this->component;

		// remove the default button location hook, we will re-add this later
		remove_action("ct_toolbar_fundamentals_list", array( $this->component, "component_button" ) );

		add_filter("ct_components_nice_names", 		array( $this, "niceName") );
		add_filter("oxy_components_html_templates", array( $this, "html_template") );
		
		// generate CSS output
        //add_action("oxygen_default_classes_output", array( $this, "generate_defaults_css" ) ); DISABLED
        add_filter("oxygen_user_classes_output", 	array( $this, "generate_classes_css"), 10, 7);
        add_filter("oxy_component_css_styles", 		array( $this, "generate_id_css"), 10, 5);

        // add some data to Oxygen builder
        add_action("ct_builder_ng_init", 			array( $this, "init_api_data") );

        // options that allowed to be used in media, states and classes
        add_filter("oxy_options_white_list", 		array( $this, "white_list_options_callback") );
        add_filter("oxy_base64_encode_options", 	array( $this, "base64_options_callback") );


        // options that is not plain CSS properties or have a registered CSS selector that will render it
        // by default all Elements API options are "not_css_options"
        add_filter("ct_not_css_options",  			array( $this, "not_css_options_callback") );

        add_action("wp_enqueue_scripts", 			array( $this, "enqueue_scripts") );
        add_action("wp_enqueue_scripts", 			array( $this, "enqueue_styles") );

		add_action('wp_ajax_oxy_load_controls_'.$this->params['tag'], array( $this, 'register_ajax_controls'), 100 );
	}


	/**
	 * Element [shortcode] HTML output
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function shortcode($atts, $content=null, $name=null) {

		if ( ! $this->component->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		// set OxygenElement options to old CT_Component options
		$this->component->options['params'] = $this->get_controls();
		$options = $this->component->set_options( $atts );
		$options['tag'] = isset($options['html_tag']) ? $options['html_tag'] : $this->params['html_tag'];
		$options['custom_attributes'] = isset($options['custom_attributes']) && !empty($options['custom_attributes']) ? $options['custom_attributes'] : $this->params['custom_attributes'];
		
		// save $options to instance
		$this->params["shortcode_options"] = $options;

		// prepare HTML output
		if (isset($this->params['php_callback'])) {
			if ( is_callable($this->params['php_callback']) || function_exists($this->params['php_callback'])) {
				ob_start();

				// base 64 decode $options
				$processed_options = $this->unprefix_options($options);
				$processed_options = $this->base64_decode_options($processed_options);

				call_user_func_array($this->params['php_callback'], array($processed_options, $this->unprefix_options($this->defaults), $content));
				$html = ob_get_clean();
			}
			else {
				$this->add_error($this->params['php_callback'] . " PHP Callback does not exist");
			}
		}
		else {
			$html = $this->params['html'];
			$html = str_replace("%%CONTENT%%", do_oxygen_elements($content), $html);
			$html = $this->filterOptions($html);
		}

		// add dynamic attributes if generated in php_callback
		if ( isset($this->params['dynamic_attributes']) && is_array($this->params['dynamic_attributes']) ) {
			$options['custom_attributes'] = array_merge($options['custom_attributes'], $this->params['dynamic_attributes']);
		}
		
		ob_start(); 

		?>

		<<?php echo esc_attr($options['tag'])?> id="<?php echo esc_attr($options['selector']); ?>" class="<?php if(isset($options['classes'])) echo esc_attr($options['classes']); echo " " . $this->params['wrapper_class']; ?>" <?php do_action("oxygen_vsb_component_attr", $options); ?>><?php echo $html; ?></<?php echo esc_attr($options['tag'])?>>

		<?php 

		if ( isset($this->params['inlineJS']) ) { 
			$this->JSOutput($this->params['inlineJS']);
		}

		$this->outputErrors();

		$html = ob_get_clean();

		return $html;
	}


	/**
	 * Set Elements HTML tag 
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function setTag($tag) {

		if (is_array($tag) && isset($tag['default'])) {
			$tag = $tag['default'];
		}

		$this->params['html_tag'] = $tag;
	}

	public function setAttributes($atts) {

		if( is_array( $atts ) ) {
			$this->params['custom_attributes'] = $atts;
		}

	}

	public function dynamicAttributes($atts) {

		if( is_array( $atts ) ) {
			$this->params['dynamic_attributes'] = $atts;
		}

	}


	/**
	 * Element HTML setter 
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function HTML($html, $wrapper_class='') {

		// save HTML template to pass to Angular
		$this->params['html_template'] = $this->prefix_options($html);
		$this->params['wrapper_class'] = is_array($wrapper_class) ? implode(" ", $wrapper_class) : $wrapper_class;

		// this one to render inside shortocde, remove keywords from it
		$html = preg_replace_callback(
				            "/OXY_EDITABLE\([^)]+\)/",
				            array( $this, "oxy_editable"),
				            $html);
		$this->params['html'] = $this->prefix_options($html);

		// nestable elements
		if (strpos($this->params['html'], "%%CONTENT%%") !== false){
			$this->nestable();
		}
	}


	/**
	 * Set Element PHPCallback and add proper hooks to show AJAX button and handle AJAX requests 
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function PHPCallback($callback, $wrapper_class='') {

		$this->params['php_callback'] = $callback;
		$this->params['wrapper_class'] = is_array($wrapper_class) ? implode(" ", $wrapper_class) : $wrapper_class;

		add_action("ct_toolbar_component_settings", 			array( $this, "apply_params_button"), 100 );		
		add_action('wp_ajax_oxy_render_' . $this->get_tag(), 	array( $this, "ajax_render_callback") );
		add_filter("template_include", 							array( $this, "single_template"), 100 );
	}
	

	/**
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function addApplyParamsButton() {
		
		add_action("ct_toolbar_component_settings", array( $this, "apply_params_button"), 100 );		
	}


	/**
	 * @author Ilya K.
	 * @since 3.2
	 */

	public function removeApplyParamsButton() {
		
		remove_action("ct_toolbar_component_settings", array( $this, "apply_params_button"), 100 );		
	}


	/**
	 * Callback to remove OXY_EDITABLE attr and save defaults value
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	private function oxy_editable($matches) {
		// get the value inside the parentheses
	    // '/\(([^)]+)\)/'
		preg_match('/\(([^)]+)\)/', $matches[0], $option);

	    if (!$option) {
	        return $matches;
	    }

	    $option = explode("|", $option[1]);

	    // default value is mandatory for OXY_EDITABLE
	    if (!$option[1]) {
	        return $matches;
	    }

	    $prefixed_name = $this->prefix_option($option[0]);
	    $this->defaults[$prefixed_name] = $option[1];
	    // this is obviously not a CSS options
		$this->add_to_not_css_options($prefixed_name);
	    //var_dump($this->defaults);

		return "";
	}


	/**
	 * Generate proper template to use in Angular. Not for public use
	 * Action hook: 'oxy_components_html_templates'
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function html_template($templates) {

		$template = array();

		// prepare HTML
		$html = $this->params['html_template'] ?? "";

		// nestable elements
		if (strpos($html, "%%CONTENT%%") !== false || (isset($this->params['nestable']) && $this->params['nestable'])){
			$template["nestable"] = "true";
		}

		$html = str_replace("%%CONTENT%%", "", $html);
		$template["html"] = $html;

		// this will tell JS to make AJAX request
		if ($this->params['php_callback']){
			$template["phpCallback"] = "true";
		}

		// add wrapper class if defined
		if ($this->params['wrapper_class']!==""){
			$template["class"] = $this->params['wrapper_class'];
		}

		// add inline JS if defined
		if ( !empty( $this->params['inlineJS'] ?? "") ){
			$template["js"] = $this->params['inlineJS'];
		}

		// if has some arbitrary js script that runs after the item is loaded
		if ($this->params['has_js']!==""){
			$template["has_js"] = $this->params['has_js'];
		}

		/**
		 * Other options
		 */

		// rebuild on DOM change
		if (isset($this->params["other_options"]['rebuild_on_dom_change']) && $this->params["other_options"]['rebuild_on_dom_change']==true){
			$template["rebuildOnDOMChange"] = "true";
		}

		// only child
		if (isset($this->params["other_options"]['only_child'])){
			$template["onlyChild"] = $this->params["other_options"]['only_child'];
		}

		// only parent
		if (isset($this->params["other_options"]['only_parent'])){
			$template["onlyParent"] = $this->params["other_options"]['only_parent'];
		}

		if (isset($this->css_selectors)){
			$template["CSSSelectors"] = $this->css_selectors;
		}
		
		if (isset($this->registeredSelectors) && is_array($this->registeredSelectors)) {
	        foreach ($this->registeredSelectors as $selector) {
	        	$template["registeredSelectors"][$selector->getSelector()] = $selector->propertiesArray();
	        }
		}

		if (isset($this->complexProperties) && is_array($this->complexProperties)) {
	        $template["complexProperties"] = $this->complexProperties;
		}

		if (isset($this->valuesCSS) && is_array($this->valuesCSS)) {
	        $template["valuesCSS"] = $this->valuesCSS;
		}

		if (isset($this->params['pageCSS']) && !empty($this->params['pageCSS'])) {
	        $template["pageCSS"] = $this->params['pageCSS'];
		}

		if (isset($this->params['prefilledComponentStructure']) && is_array($this->params['prefilledComponentStructure'])) {
	        $template["prefilledComponentStructure"] = $this->params['prefilledComponentStructure'];
		}

		if (isset($this->rebuild_options) && is_array($this->rebuild_options)) {
			$template["rebuildTriggerOptions"] = $this->rebuild_options;
		}

		$template['HTMLTag'] = $this->params['html_tag'];
		
		$template['Attributes'] = $this->params['custom_attributes'];

		// push to other templates
		$templates[$this->params['tag']] = $template;

		return $templates;
	}


	/**
	 * Enqueue JS provided by user 
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function JS($scripts, $dep=array()) {

		// save scripts to include later with wp_enqueue_scripts
		$this->scripts['scripts'] = $scripts;
		$this->scripts['dep'] = $dep;
	}


	/**
	 * wp_enqueue_scripts callback to include elements scripts 
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function enqueue_scripts() {

		$js  = isset($this->scripts['scripts']) ? $this->scripts['scripts'] : false;
		$dep = isset($this->scripts['dep']) ? $this->scripts['dep'] : array();

		if (is_array($js)) {
			foreach ($js as $key => $path) {
				wp_enqueue_script( $this->params['tag'].'-'.$key, $this->params['dir_url'] . $path, $dep);
			}
		}
		else if( !empty($js) ) {
			wp_enqueue_script( $this->params['tag'], $this->params['dir_url'] . $js, $dep);
		}
	}


	/**
	 * Add custom inline JS code that goes just after the component
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function inlineJS($js) {

		$this->params['inlineJS'] = $this->prefix_options($js);
	}


	/**
	 * Add custom inline JS code that goes just after the component
	 *
	 * @author Ilya K.
	 * @since 3.4
	 */

	public function builderInlineJS($js) {

		$this->params['builderInlineJS'] = $this->prefix_options($js);
	}


	/**
	 * Add custom JS code to footer
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function footerJS($js) {

		$this->params['footerJS'] = $js;
		add_action("wp_footer", array( $this, "footer_js_output") );
	}


	/**
	 * Echo custom JS code added by user to footer
	 * Action hook: 'wp_footer'
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function footer_js_output() {

		if ( isset($this->params['footerJS']) ) { 
			$this->JSOutput($this->params['footerJS']);
		}
	}


	/**
	 * Enqueue CSS files 
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function CSS($styles) {

		$this->styles = $styles;
	}


	/**
	 * wp_enqueue_scripts callback to include Elemnet styles 
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function enqueue_styles() {

		$css = $this->styles;

		if (is_array($css)) {
			foreach ($css as $key => $path) {
				wp_enqueue_style( $this->params['tag'].'-'.$key, $this->params['dir_url'] . $path);
			}
		}
		else {
			wp_enqueue_style( $this->params['tag'], $this->params['dir_url'] . $css);
		}
	}


	/**
	 * Static component CSS to add to universal.css 
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function staticCSS($css) {

		$this->params['staticCSS'] = $css;
		add_action("oxygen_default_classes_output", array( $this, "static_css_output") );
	}


	/**
	 * Echo element's default static CSS
	 * Action hook: 'oxygen_default_classes_output'
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function static_css_output() {

		if ( isset($this->params['staticCSS']) ) {
			$css = str_replace("%%ASSETS_PATH%%", $this->assets_path, $this->params['staticCSS']);

			// filter for additional features
			// since 3.0
			$css = apply_filters("oxy_elements_api_static_css_output", $css);

			echo $css;
		}
	}

	
	/**
	 * Page CSS to add to page files cache .css 
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function pageCSS($css) {

		$this->params['pageCSS'] = $css;
		add_action("oxygen_id_styles_output-". $this->get_tag(), array( $this, "page_css_output") );
	}


	/**
	 * Echo element's default static CSS
	 * Action hook: 'oxygen_id_styles_output-{element slug}'
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function page_css_output() {

		if ( isset($this->params['pageCSS']) ) {
			$css = str_replace("%%ASSETS_PATH%%", $this->assets_path, $this->params['pageCSS']);
			
			// filter for additional features
			// since 3.0
			$css = apply_filters("oxy_elements_api_page_css_output", $css);
			
			echo $css;
		}
	}


	/**
	 * Static component CSS to add to universal.css 
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function builderCSS($css) {

		$this->params['builderCSS'] = $css;
		add_filter("oxygen_iframe_styles", array( $this, "builder_css_output") );
	}


	/**
	 * Echo element's default static CSS
	 * Action hook: 'oxygen_default_classes_output'
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function builder_css_output($styles) {

		if ( isset($this->params['builderCSS']) ) { 
			return $styles . "<style id=\"{$this->get_tag()}-styles\" type=\"text/css\">" . $this->params['builderCSS'] . "</style>";
		}

		return $styles;
	}


	/**
	 * Add custom CSS selectors 
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function addCSSSelector($selector) {
		$this->css_selectors[] = $selector;
	}


	/**
	 * Add custom CSS selectors 
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function registerCSSSelector($selector="") {

		if (!isset($this->registeredSelectors[$selector])) {
			$this->registeredSelectors[$selector] = new OxygenSelector($selector, $this);
		}

		return $this->registeredSelectors[$selector];
	}


	/**
     * Generate default '.element-name' CSS styles to be added to universal.css
     * callback to 'oxygen_default_classes_output' action hook
     * 
     * @author Ilya
     * @since 2.4
     */

    public function generate_defaults_css() {

		global $fake_properties;

        $options = $this->initial_defaults;

		$styles = "";
		
		// generate '.tag-name' defaults class
		if ( is_array( $options ) ) {
			foreach ( $options as $name => $value ) {

				// skip uints
				if ( strpos( $name, "-unit") ) {
					continue;
				}

				// skip empty values
				if ( $value === "" ) {
					continue;
				}

				// handle global fonts
				if ( $name == "font-family" && is_array( $value ) ) {
					$value = $global_settings['fonts'][$value[1]];

					if ( strpos($value, ",") === false && strtolower($value) != "inherit" ) {
						$value = "'$value'";
					}
				}

				// handle unit options
				if ( isset($options[$name.'-unit']) && $options[$name.'-unit'] ) {
					// set to auto
					if ( $options[$name.'-unit'] == 'auto' ) {
						$value = 'auto';
					}
					// or add unit
					else {
						$value .= $options[$name.'-unit'];
					}
				}

				if (in_array(str_replace("-", "_", $name), $this->not_css_options)) {
					continue;
				}

				// skip fake properties
				if ( in_array( $name, $fake_properties ) ) {
					continue;
				}

				if ( $value !== "" ) {
					$styles .= "$name:$value;\r\n";
				}

			}
		}

		if (trim($styles)!==""){
			echo ".{$this->params['tag']} {\r\n";
			echo $styles;
			echo "}\r\n";
		}

		// run through all custom CSS generation process
        echo $this->generate_css($options, true, ".".$this->params['tag']);
    }


    /**
	 * Generate CSS styles output for .classes
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function generate_classes_css($css, $class, $state, $options, $is_media = false, $is_selector = false, $defaults = array()) {

        if ($is_selector) {
            return $css;
        }

        $is_element = false;

        foreach ($options as $key => $value) {
            if (strpos($key,$this->get_tag())!==false) {
                $is_element = true;
                break;
            }
        }

        if ($is_element) {
        	$selector = ".".$class;
		    $css .= $this->generate_css($options, false, $selector, $state, $defaults);        		
        	$is_element = false;
		}

        return $css;
    }


	/**
	 * Generate CSS styles output for #IDs
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function generate_id_css($styles, $states, $selector, $class_obj, $defaults) {

        if ($class_obj->options['tag'] != $this->params['tag']){
            return $styles;
        }

		if (CT_Component::in_repeater_cycle()) return;
		
		// #id styles should not contain any defaults
		$this->fallbackToDefaults = false;
        
        if (is_array($states)) {
		
			$selector = CT_Component::get_corrected_element_selector($selector);

        	foreach ($states as $state_name => $state_options) {
        		
        		// skip classes
        		if ($state_name=='classes')
        			continue;
        		
        		// skip string options and empty arrays
        		if (!is_array($state_options)||empty($state_options))
        			continue;

		        $styles .= $this->generate_css($state_options, false, $selector, $state_name, $defaults);        		
        	}
        }
		
	    $this->fallbackToDefaults = true;

	    // run only once per element, not for each element appearence on the page
	    if (!isset($this->oxygen_id_styles_output[$this->get_tag()])) {
		    ob_start();
		    do_action("oxygen_id_styles_output-".$this->get_tag());
		    $page_css = ob_get_clean();
		    $this->oxygen_id_styles_output[$this->get_tag()] = true;
	    	$styles = $page_css . $styles;
		}

		$styles = apply_filters( "oxygen_id_styles_filter-".$this->get_tag(), $styles, $states, $selector );

        return $styles;
    }


    /**
	 * Generate CSS output  
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	private function generate_css($params=false, $class=false, $selector="", $state_name="", $defaults=array()) {

		if ($state_name=='original'||!$state_name=='original'){
			$state_name = "";
		}
		else {
			$state_name = ":".$state_name;
		}

		$css = "";

		$this->params["shortcode_options"] = $params;

		// process custom registered CSS selectors
		if (isset($this->registeredSelectors) && is_array($this->registeredSelectors)) {
	        foreach ($this->registeredSelectors as $registeredSelector) {
	        	$styles = $registeredSelector->generateCSS($params, $state_name);
	        	if ($styles) {
	        		$css .= $this->prefixCSSSelectors($styles,$selector);
	        	}
	        }
	    }

	    // process CSS values
	    // merge controls from Primary Section and all Custom Sections  
	    $controls = array_merge($this->params['controls'], $this->get_section_controls_objects());
	    
	    foreach ($controls as $control) {
	    	if (is_object($control)) { 
				$value_css = $control->getValueCSS($params, $selector);
				$css .= $this->prefixCSSSelectors($value_css, $selector);
			}
	    }

		return $css;
    }

    
    /**
	 * Output various API data to builder ng-init
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

    public function init_api_data() {
    	
    	// presets
    	$output = json_encode( $this->presets );
		$output = htmlspecialchars( $output, ENT_QUOTES );
		echo "APIPresets=$output;";

    }


    /**
	 * AJAX callback to render Element in Oxygen. Used when AJAX call made to admin-ajax.php
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

    public function ajax_render_callback() {
        global $ct_template_id;

    	define("OXY_ELEMENTS_API_AJAX", true);

		oxygen_vsb_ajax_request_header_check();

		$component_json = file_get_contents('php://input');
		$component 		= json_decode( $component_json, true );
		$options 		= $component['options']['original'];
		$options['selector'] = $component['options']['selector'];
		$options 		= $this->component->keys_dash_to_underscore($options);

		// save $options to instance
		$this->params["shortcode_options"] = $options;

		// add page-width media (taken from CT_Component::build_css())
		// TODO: Wrap this in a function?
		global $media_queries_list;
		global $media_queries_list_above;
		$media_queries_list["page-width"]["maxSize"] 		= oxygen_vsb_get_page_width($ct_template_id).'px';
		$media_queries_list_above["page-width"]["minSize"] 	= (oxygen_vsb_get_page_width($ct_template_id)+1).'px';

		// WooCo Product Builder fix to make child elements show defined post data
		if (isset($component['options']['product_builder_id']) && $component['options']['product_builder_id']) {
			global $post;
			$product = wc_get_product($component['options']['product_builder_id']);
			if ($product) {
				$post = get_post($component['options']['product_builder_id']);
				setup_postdata($post);
			}
		}
		$content = null;
		if( isset($component['component']['children']) && !empty(isset($component['component']['children'])) ) {
			$content = "[oxy-empty-shortcode]";//= $component['component']['children'];
		}

		if ( is_callable( $this->params['php_callback'] ) || function_exists( $this->params['php_callback'] ) ) {
			wp_enqueue_scripts();
			call_user_func_array( $this->getParam('php_callback'), array( $this->unprefix_options($options), $this->unprefix_options($this->defaults), $content) );
			$wp_scripts = wp_scripts();
			$wp_styles  = wp_styles();

			wp_print_scripts( $wp_scripts->queue );
			//wp_print_styles( $wp_styles->queue );
			if ( isset($this->params['inlineJS']) ) { 
				$this->JSOutput($this->params['inlineJS']);
			}
			if ( isset($this->params['builderInlineJS']) ) { 
				$this->JSOutput($this->params['builderInlineJS']);
			}
		}
		else {
			echo $this->params['php_callback'] . " PHP Callback does not exist";
		}

		// Custom CSS added with customCSS() for "oxygen_id_styles_filter-..." hook
		?>
		<style type="text/css">
		<?php echo apply_filters( "oxygen_id_styles_filter-".$this->get_tag(), "", $component['options'], '#'.$component['options']['selector'] ); ?>
		</style>
		<?php

		die();
	}

	
	/**
	 * 'template_include' filter used when AJAX call made to page/post permalink
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function single_template( $template ) {

        $new_template = '';

        if( isset($_REQUEST['action']) && stripslashes($_REQUEST['action']) == 'oxy_render_' . $this->get_tag()) {
            
            if ( file_exists(CT_FW_PATH . '/api/single.template.php') ) {
            	
            	global $oxy_api_element;
				$oxy_api_element = $this;
                
                $new_template = CT_FW_PATH . '/api/single.template.php';
            }
        }

        if ( '' != $new_template ) {
            return $new_template ;
        }

        return $template;
    }


    /**
	 * Define element as nestable and register extra shortcodes for nesting
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function nestable() {

		$this->params['nestable'] = true;

		for ( $i = 2; $i <= 16; $i++ ) {
			add_shortcode($this->params['tag'] . "_" . $i, array($this, 'shortcode'));
		}
	}


	/**
	 * Register a complex property like "box-shadow: {a} {b} {c} {d}"
	 * Use mapProperty("box-shadow>a", ...) to map each part.
	 *
	 * @author Ilya K.
	 * @since 2.4
	 */

	public function defineProperty($property, $value) {

		$this->complexProperties[$property] = $value;
	}

	
	/**
     * Prefix selectors with element #id, commas and media queries supported
	 *
	 * @author Ilya K.
	 * @since 3.0
	 */

	public function prefixCSSSelectors($css="", $selector="") {

		if ( !is_string( $css ) ) {
			return "";
		}

		$parts = explode('}', $css);

		foreach ($parts as &$part) {
		    if (empty($part)) {
		        continue;
			}
				    
			$partDetails = explode('{',$part);
			// check for @media queries 
			if (substr_count($part,"{")==2) {
			    $mediaQuery = $partDetails[0]."{";
			    $partDetails[0] = $partDetails[1];
			    $mediaQueryStarted = true;
			}
				    
			// prefix comma separated selectors
			$subParts = explode(',', $partDetails[0]);
			foreach ($subParts as &$subPart) {
			   	if (trim($subPart)=="@font-face") continue;
			        $subPart = $selector . ' ' . trim($subPart);
			}

			if (substr_count($part,"{")==2){
				// media query start
			    $part = $mediaQuery."\n".implode(', ', $subParts)."{".$partDetails[2];
			}
			elseif (empty($part[0]) && $mediaQueryStarted){
				// media query end
			    $mediaQueryStarted = false;
			    $part = implode(', ', $subParts)."{".$partDetails[2]."}\n"; //finish media query
			}
			elseif (isset($partDetails[1])){
			    $part = implode(', ', $subParts)."{".$partDetails[1];
			}
		}
		
		$css = implode("}\n", $parts);

		return $css;
	}

	
	/**
     * Prefix selectors with element #id, commas and media queries supported
	 *
	 * @author Ilya K.
	 * @since 3.0
	 */

	public function base64_options_callback($options) {

		if (!is_array($options)) {
			return $options;
		}

		$options = array_merge($options, $this->base64_options);

		return $options;
	}


	/**
     * Empty AJAX callback to prevent 404 error for Elements that doesn't use the AJAX Controls
	 *
	 * @author Ilya K.
	 * @since 3.4
	 */

	public function register_ajax_controls() {
		die();
	}	

}