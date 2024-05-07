<?php

class OxyEl {

    public $El;
    public $selectors;
    public $button_place;
    public $has_js;

    function name2slug($name) {
        return strtolower(str_replace(' ', '-', $name));
    }

    function selector2slug($selector) {
        return preg_replace("/[^A-Za-z0-9 ]/", "-", $selector);
    }

    function tag() {
        return "div";
    }

    function attributes() {
        return null;
    }

    function textTagChoices() {
        return array('default' => 'div', 'choices' => 'div,p,figcaption,time,article,summary,details' );
    }

    function divTagChoices() {
        return array('default' => 'div', 'choices' => 'div,article,aside,details,figure,footer,header,hgroup,main,mark,nav,section' );
    }

    function headingTagChoices() {
        return array('default' => 'h1', 'choices' => 'h1,h2,h3,h4,h5,h6' );
    }


    function slug() {
        return false;
    }

    function class_names() {
        return array();
    }

    function button_place() {
        return "";
    }

    function icon() {
        return "";
    }

    function custom_init() {

    }

    function keywords() {
        return "";
    }

    function __construct() {

        // don't boot the element during data loading where it is not needed 
        if ( !isset( $_REQUEST['action'] ) || $_REQUEST['action'] != "oxy_get_components_templates" ) {
            if ( oxy_data_requests() ) {
                return;
            }
        }

        $name = $this->name();
        $slug = $this->name2slug($name);

        if ($this->slug()) {
            $slug = $this->slug();
        }

        $this->custom_init();

        // store a slug to class name reference in the global space
        global $oxy_el_slug_classes;

        if(!is_array($oxy_el_slug_classes)) {
            $oxy_el_slug_classes = array();
        }

        $oxy_el_slug_classes[$slug] = get_class($this);


        $options = array();
        if (method_exists($this, 'options')) {
            $options = $this->options();
        }

        $server_side_render = true;
        if (isset( $options['server_side_render'] )) {
            $server_side_render = $options['server_side_render'];
        }

        if (method_exists($this, 'button_priority')) {
            $options['button_priority'] = $this->button_priority();
        }

        $this->El = new OxygenElement(__($name), $slug, '', $this->icon(), $this->button_place(), $options, $this->has_js);

        $this->El->setTag( $this->tag() );

        $this->El->setAttributes( $this->attributes() );

        $this->El->set_template_param("keywords", $this->keywords());

        if (method_exists($this, 'init')) {
            $this->init();
        }

        if (method_exists($this, 'defaultCSS')) {
            $this->El->pageCSS(
                $this->defaultCSS()
            );
        }

        if (method_exists($this, 'customCSS')) {
            add_filter( "oxygen_id_styles_filter-".$this->El->get_tag(), 
                function($styles, $states, $selector){
                    // doesn't work with states or media for now only 'original' options
                    $styles.=$this->customCSS($states['original'], $selector);
                    return $styles;
                }, 
            10, 3 ); 
        }

        if (method_exists($this, 'enableFullPresets') && $this->enableFullPresets() == true) {
            add_filter("oxygen_elements_with_full_presets", function($elements) {
                if (!is_array($elements)) {
                    $elements = array();
                }
                $elements[] = $this->El->get_tag();
                return $elements;
            });
        }

        if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == "oxy_get_components_templates" ) {
            $this->controls();
        }
        else if ( isset( $_REQUEST['ct_builder'] ) && !isset( $_REQUEST['oxygen_iframe'] ) ) {
            // Toolbar frame
            if (!$this->El->is_ajax_controls()) {
                // some API elements don't load controls with AJAX, so add them to HTML 
                $this->controls();
            }
        }
        else {
            $this->controls();
        }
        $this->El->controlsReady();

        if( $server_side_render ) {
            $this->El->PHPCallback(
                array($this, 'render'),
                $this->class_names()
            );
        } else {
            $this->El->HTML(
                $this->render(),
                $this->class_names()
            );
        }
        
        $this->El->set_prefilled_components($this->prefilledComponentStructure());

        /**
         * Keep it very last one
         */

        if (method_exists($this, 'afterInit')) {
            $this->afterInit();
        }
    }

    function getSlug() {
        return $this->El->get_tag();
    }

    function setAssetsPath($path='') {
        $this->El->set_assets_path($path);
    }

    function enableNesting() {
        $this->El->nestable();
    }

    function addApplyParamsButton() {
        $this->El->addApplyParamsButton();
    }

    function removeApplyParamsButton() {
        $this->El->removeApplyParamsButton();
    }

    function removeAddButton() {
        $this->El->addButton(false);
    }

    // obviously a bunch of methods here for registering vairous types of 
    // controls is insanity. so we'll refactor this later once we make a big mess
    // and know what we actually have to clean up and what the necessary functionality
    // abstraction layer should look like

    function flex($selector, $section=null) {

        if ($section) {
            $l = $section;
        } else {
            $l = $this->El;
        }

        $slug = $this->selector2slug($selector);
        $slug.= "_flex";

        $FlexLayout = $l->addPreset("flex-layout", $slug, __("Child Element Layout"));
        $FlexLayout->setValue( array(
            'flex-direction' => 'column',
            'align-items' => '',
            'justify-content' => '',
        ));
        $FlexLayout->whiteList();
    
        $RootSelector = $this->El->registerCSSSelector($selector);
        $RootSelector->mapPreset('flex-layout',$slug);
    }

    function typographySection($name, $selector, $OxyEl=null, $section=null) {

        $slug = $this->selector2slug($selector);
        $slug.= "_typography";

        $typographySection = $this->El->addControlsSection(
            $slug,
            __($name),
            "assets/icon.png",
            $OxyEl,
            $section
        );

        $typographyPreset = $typographySection->addPreset(
            "typography",
            $slug,
            __($name." Typography")
        );

        $typographyPreset->whiteList();
        
        $typographySelector = $this->El->registerCSSSelector($selector);
        $typographySelector->mapPreset(
            'typography',
            $slug
        );

        return $typographySection;
    
    }

    function borderSection($name, $selector, $OxyEl=null, $section=null) {

        $slug = $this->selector2slug($selector);
        $slug.= "_border";

        $borderSection = $this->El->addControlsSection(
            $slug,
            __($name),
            "assets/icon.png",
            $OxyEl,
            $section
        );

        $borderPreset = $borderSection->addPreset(
            "border",
            $slug,
            __($name." Border")
        );

        $borderRadiusPreset = $borderSection->addPreset(
            "border-radius",
            $slug."_border_radius",
            __($name." Border Radius")
        );

        $borderPreset->whiteList();
        $borderRadiusPreset->whiteList();

        $borderSelector = $this->El->registerCSSSelector($selector);
        $borderSelector->mapPreset(
            'border',
            $slug
        );
        $borderSelector->mapPreset(
            'border-radius',
            $slug."_border_radius"
        );

        return $borderSection;
    
    }

    
    function boxShadowSection($name, $selector, $OxyEl=null, $section=null, $inset=true) {

        $slug = $this->selector2slug($selector);
        $slug.= "_box_shadow";

        $boxShadowSection = $this->El->addControlsSection(
            $slug,
            __($name),
            "assets/icon.png",
            $OxyEl,
            $section
        );

        $boxShadowPreset = $boxShadowSection->addPreset(
            "box-shadow",
            $slug,
            __($name)
        );

        $boxShadowPreset->whiteList();
        if ($inset===false) {
            $boxShadowPreset->removeInset();
        }

        $borderSelector = $this->El->registerCSSSelector($selector);
        $borderSelector->mapPreset(
            'box-shadow',
            $slug
        );

        return $boxShadowSection;
    
    }

    function get_control_type_by_css_property($css_property) {

        switch ($css_property) {
            case 'color':
            case 'background-color':
            case 'border-color':
            case 'border-top-color':
            case 'border-bottom-color':
            case 'border-left-color':
            case 'border-right-color':

                return 'colorpicker';
                break;

            case 'font-size':
            case 'border-radius':
            
                return 'slider-measurebox';
                break;

            case 'letter-spacing':
            case 'height':
            case 'width':
            case 'max-width':
            case 'min-width':
            case 'margin-top':
            case 'margin-right':
            case 'margin-bottom':
            case 'margin-left':
            case 'top':
            case 'right':
            case 'bottom':
            case 'left':
            case 'border-width':
            case 'border-top-width':
            case 'border-right-width':
            case 'border-bottom-width':
            case 'border-left-width':
            case 'padding-top':
            case 'padding-right':
            case 'padding-bottom':
            case 'padding-left':
            
                return 'measurebox';
                break;

            case 'opacity':
            
                return 'slider-measurebox';
                break;

            case 'text-transform':
            case 'text-decoration':
            case 'float':
            case 'display':
            case 'flex-wrap':
            case 'visibility':
            case 'align-items':
            
                return 'radio';
                break;

            case 'font-family':
            
                return 'font-family';
                break;

            case 'font-weight':
            
                return 'dropdown';
                break;
            
            default:
                return "textfield";
                break;
        }
    }


    function addControlSection($a, $b, $c, $d=null, $section=null) {

        $control_section = $this->El->addControlsSection($a, $b, $c, $d, $section);
        return $control_section;

    }

    function addTagControl($section = null) {

        $tag = $this->tag();

        // if section parameter is passed, add the control to the section
        // otherwise add it to the element root
        if ($section) {
            $l = $section;
        } else {
            $l = $this->El;
        }


        if (isset($tag['choices'])) {
            $control = $l->addControl('tag', 'html_tag', __("Tag"));            
            $control->setValue(explode(",", $tag['choices']));
            // set default
            if (isset($tag['default'])){
                $control->setDefaultValue($tag['default']);
            }
        }
        else {
           throw new Exception("addTagControl() requires comma separted 'choices' param defined in tag() method.");
        }

    }

    function addCustomControl($html, $property="", $section=null) {

        // check for usage before 3.2, as section was a second argument
        if (is_object($property)) {
            $section = $property;
            $property = "";
        }
        
        // if section parameter is passed, add the control to the section
        // otherwise add it to the element root
        if ($section) {
            $l = $section;
        } else {
            $l = $this->El;
        }

        $control = $l->addControl("custom_control", $property);
        $control->setHTML($html);
        $control->unprefix();

        return $control;
    }

    function addOptionControl($params, $section = null) {

        // if section parameter is passed, add the control to the section
        // otherwise add it to the element root
        if ($section) {
            $l = $section;
        } else {
            $l = $this->El;
        }

        $control_type = $params['type'];

        if (isset($params['name'])) {
            $control_label = $params['name'];
        } else {
           throw new Exception("addOptionControl params['name'] must be set");
        }

        // generate a unique slug for the control based on the name
        if (isset($params['slug'])) {
            $control_slug = $params['slug'];
        } else {
            $control_slug = str_replace(" ", "-", $params['name']);
            $control_slug = 'slug_'.preg_replace("/[^A-Za-z0-9 ]/", "", $control_slug);
        }

        $Control = $l->addControl($control_type, $control_slug, __($control_label));

        // option controls is not a style controls, make those auto rebuild element to avoid "Apply param" button
        /*$rebuild_element = isset($params['rebuild_element']) ? $params['rebuild_element'] : true; 
        if ($rebuild_element) {
            $Control->rebuildElementOnChange();
        }*/

        if (isset($params['condition'])) {
            $Control->setCondition($params['condition']);
        }

        if (isset($params['value'])) {
            $Control->setValue($params['value']);
        }

        if (isset($params['default'])) {
            $Control->setDefaultValue($params['default']);
        }

        if (isset($params['base64']) && $params['base64'] == true) {
            $Control->base64();
        }

        return $Control;

    }

    function addStyleControl($params, $section = null) {

        // if section parameter is passed, add the control to the section
        // otherwise add it to the element root
        if ($section) {
            $l = $section;
        } else {
            $l = $this->El;
        }

        $control_type = isset($params['control_type']) ? $params['control_type'] : $this->get_control_type_by_css_property($params['property']);

        // use the provided name as the control label if it is set
        // otherwise auto generate a name based on the CSS property it sets
        if (isset($params['name'])) {
            $control_label = $params['name'];
        } else {
            $control_label = ucwords(str_replace('-', ' ', $params['property']));
        }

        // generate a unique slug for the control
        // based on the selector and the css property
        if (isset($params['selectors'])) {
            // take the first selector from array to generate the slug. Is there a better way?
            $property = str_replace('-', '_', $params['selectors'][0]['property']);

            $selector = $params['selectors'][0]['selector'];
            $selector = str_replace(" ", "-", $selector);
            $selector = preg_replace("/[^A-Za-z0-9 ]/", "", $selector);

            $control_slug = 'slug_'.$selector.'_'.$property;

        }
        else if (isset($params['selector'])) {
            
            $property = str_replace('-', '_', $params['property']);
            
            $selector = $params['selector'];
            $selector = str_replace(" ", "-", $selector);
            $selector = preg_replace("/[^A-Za-z0-9 ]/", "", $selector);

            $control_slug = 'slug_'.$selector.'_'.$property;
        }
        else {
            $control_slug = $params['property'];
        }
        if (isset($params['slug'])) {
            $control_slug = $params['slug'];
        }

        $control = $l->addControl($control_type, $control_slug, __($control_label));

        // now map the control to the appropriate css selector, based on the control slug
        if (isset($params['selector'])){
            // single
            $this->mapPropertyHelper($params['selector'], $params['property'], $control_slug);
        }
        elseif (isset($params['selectors']) && is_array($params['selectors'])) {
            // multiple
            foreach ($params['selectors'] as $selector) {
                $this->mapPropertyHelper($selector['selector'], $selector['property'], $control_slug);
                $this->fixUnitsAndValues($control, $selector['property']);
            }
        }
        // if no selector defined assume this is a CSSOption
        else {
            $control->CSSOption();
        }

        // utility function to set the units and values properply
        // depending on the CSS property the control is affecting
        // for example, it will add the px unit to a control that is applied to font-size
        // and 100 - 900 values to a control setting font-weight 
        if (isset($params['property'])){
            $this->fixUnitsAndValues($control, $params['property']);
        }

        if (isset($params['unit'])){
            $control->setUnits($params['unit']);
        }
        if (isset($params['value'])){
            $control->setValue($params['value']);
        }

        if (isset($params['default'])) {
            $control->setDefaultValue($params['default']);
        }

        if (isset($params['condition'])) {
            $control->setCondition($params['condition']);
        }

        if (isset($params['description'])) {
            $control->setDescription($params['description']);
        }

        if (isset($params['hidden'])) {
            $control->hidden();
        }

        // call whiteList to make the control settable in classes, states, and media queries
        $control->whiteList();


        return $control;

    }

    function addStyleControls($controls, $section = null) {
        if (is_array($controls)){
            foreach ($controls as $control_params) {
                $this->addStyleControl($control_params, $section);
            }
        }
    }

    function fixUnitsAndValues($control, $prop) {

        switch ($prop) {

            case 'font-size':
            case 'letter-spacing':
            
                $control->setUnits("px", "px,em");
                break;

            case 'border-radius':

                $control->setUnits("px");
                break;
            
            case 'border-width':
            case 'border-top-width':
            case 'border-right-width':
            case 'border-bottom-width':
            case 'border-left-width':

                $control->setUnits("px", "px,em,%");
                break;
            
            case 'font-weight':
                
                $control->setValue(array(
                    "" => "&nbsp;", 
                    "100" => "100", 
                    "200" => "200", 
                    "300" => "300", 
                    "400" => "400", 
                    "500" => "500", 
                    "600" => "600", 
                    "700" => "700", 
                    "800" => "800", 
                    "900" => "900"));
                break;

            case 'text-transform':
                
                $control->setValue(array("none", "capitalize", "uppercase", "lowercase"));
                break;

            case 'text-decoration':
                
                $control->setValue(array("none", "underline", "overline", "line-through"));
                break;

            case 'display':
                
                $control->setValue(array("flex", "inline-flex", "block", "inline-block", "inline", "none"));
                break;

            case 'visibility':
                
                $control->setValue(array("visible", "hidden"));
                break;

            case 'flex-wrap':
                
                $control->setValue(array("nowrap", "wrap", "wrap-reverse"));
                break;

            case 'align-items':
                
                $control->setValue(array("flex-start", "center", "flex-end", "baseline", "stretch"));
                break;

            case 'letter-spacing':
            case 'height':
            case 'width':
            case 'max-width':
            case 'min-width':
            case 'margin-top':
            case 'margin-right':
            case 'margin-bottom':
            case 'margin-left':
            case 'top':
            case 'right':
            case 'bottom':
            case 'left':
                
                $control->setUnits("px");
                break;

            case 'padding':
                $control->setValue( array(
                        'padding-top' => "0",
                        'padding-left' => "0",
                        'padding-right' => "0",
                        'padding-bottom' => "0",
                ));
                $control->setUnits( array(
                        'padding-top' => "px",
                        'padding-left' => "px",
                        'padding-right' => "px",
                        'padding-bottom' => "px",
                ));
                break;

            case 'float':
                
                $control->setValue(array("none", "left", "right"));
                break;

            case 'opacity':
                
                $control->setRange(0, 1, 0.1);
                break;

        }

    }

    function mapPropertyHelper($selector, $property, $slug) {

        if (!isset($this->selectors[$selector])) {
            $this->selectors[$selector] = $this->El->registerCSSSelector($selector);
        }

        $this->selectors[$selector]->mapProperty(
            $property,
            $slug
        );
    }


    function defineProperty($property, $value) {

        $this->El->defineProperty($property, $value);

    }

    function prefilledComponentStructure() {

        return array();
    }


    function setTemplateParam($name, $value) {

        $this->El->set_template_param($name, $value);
    }


}