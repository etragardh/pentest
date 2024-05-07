<?php

/**
 * Superbox Component Class
 * 
 * @since 2.0
 * @author Louis & Ilya
 */


class Oxygen_VSB_SuperBox extends CT_Component{

    public $param_array = array();
    public $data_array = array();
    var $js_added = false;

    function __construct($options) {

        // run initialization
        $this->init( $options );

        // Add shortcodes
        add_shortcode( $this->options['tag'], array( $this, 'shortcode' ) );
        add_oxygen_element( $this->options['tag'], array( $this, 'shortcode' ) );

        for ( $i = 2; $i <= 16; $i++ ) {
            add_shortcode( $this->options['tag'] . "_" . $i, array( $this, 'shortcode' ) );
        }

        // change component button place
        remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
        add_action("oxygen_helpers_components_interactive", array( $this, "component_button" ) );

        // add white list options
        add_filter("oxy_options_white_list", array( $this, "white_list_options") );
        // all the options can be unset, so we can use the same white_list_options callback
        add_filter("oxy_allowed_empty_options_list", array( $this, "white_list_options") );

        // generate defaults styles class
        add_action("oxygen_default_classes_output", array( $this, "generate_defaults_css" ) );

        // generate user styles class
        add_filter("oxygen_user_classes_output", array( $this, "generate_classes_css"), 10, 7);

        // generate #id stlyes
        add_filter("oxy_component_css_styles", array( $this, "generate_id_css"), 10, 5);

        // add specific options
        add_action("ct_toolbar_component_settings", array( $this, "builder_settings"), 90 );
    }


    /**
     * Generate CSS for arrays parameters only
     * 
     * @since 2.0
     * @author Ilya
     */

    function white_list_options($options) {

        $options_to_add = array(
            // TODO: make it fetch from params automatically based on some flag
            "superbox_transition_duration",
            "superbox_secondary_opacity_start",
            "superbox_secondary_opacity_finish",
            "superbox_secondary_scale_start",
            "superbox_secondary_scale_finish",
            "superbox_secondary_slide_inorout" ,
            "superbox_secondary_slide_direction",
            "superbox_secondary_slide_distance",
            "superbox_primary_opacity_start",
            "superbox_primary_opacity_finish",
            "superbox_primary_scale_start",
            "superbox_primary_scale_finish",
            "superbox_primary_slide_inorout",
            "superbox_primary_slide_direction",
            "superbox_primary_slide_distance",

        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }


    /**
     * Generate CSS based on shortcode params
     * 
     * @since 2.0
     * @author Ilya
     */

    function generate_defaults_css() {
        
        $options  = $this->set_options(
                // use fake JSON object to prevent set_options from stop due to empty object
                // TODO: make proper changes to set_options to avoid this hack
                array("ct_options"=>'{"key":1}')
            );

        $options['selector'] = ".oxy-superbox";

        echo $this->generate_css($options, true);
    }


    /**
     * Generate CSS for user custom classes
     * 
     * @since 2.0
     * @author Ilya
     */

    function generate_classes_css($css, $class, $state, $options, $is_media = false, $is_selector = false, $defaults = array()) {

        if ($is_selector) {
            return $css;
        }

        $is_component = false;

        foreach ($options as $key => $value) {
            if (strpos($key,"superbox_")!==false) {
                $is_component = true;
                break;
            }
        }

        $options['selector'] = ".".$class;
        if ($is_component) {
            $css .= $this->generate_css($options, true, $defaults[$this->options['tag']]);
            $is_component = false;
        }

        return $css;
    }


    /**
     * Generate ID styles
     * 
     * @since 2.0
     * @author Ilya
     */

    function generate_id_css($styles, $states, $selector, $class_obj, $defaults) {

        if ($class_obj->options['tag'] != $this->options['tag']){
            return $styles;
        }
        
        $params = $states['original'];
        $params['selector'] = $selector;

        return $styles . $this->generate_css($params, false, $defaults);
    }

    
    /**
     * 
     * 
     * @since 2.0
     * @author Louis
     */

    function position_css($position, $forcein = false) {

        ob_start();

        foreach ($position as $prop => $val) {

            if ($forcein == true) {
                if ($val !== null) {
                    $val = '0';
                }
            }

            if ($val !== null) {
                echo $prop.": ".$val.";";
            }

        }

        return ob_get_clean();
    }


    /**
     * 
     * 
     * @since 2.0
     * @author Louis
     */

    function slide_position($direction, $distanceoverride = "") {

        $distance = '100%';

        if ($distanceoverride !== "" && $distanceoverride !== "px") {
            $distance = $distanceoverride;
        }

        switch ($direction) {
            case 'left':
                $position['left'] = '-'.$distance;
                break;
            case 'right':
                $position['left'] = ''.$distance;
                break;
            case 'top':
                $position['top'] = '-'.$distance;
                break;
            case 'bottom':
                $position['top'] = ''.$distance;
                break;
        }

        $return['out_css'] = $this->position_css($position); // css for positioning the slide out of the superbox
        $return['in_css'] = $this->position_css($position, true); // css for positinoing the slide in the superbox

        return $return;
    }

    
    /**
     * Generate CSS for arrays parameters only
     * 
     * @since 2.0
     * @author Louis
     */

    function generate_css($params=false, $class=false, $defaults=array()) {

        if ($params===false) {
            $params = $this->param_array;
        }

        if ($this->in_repeater_cycle()) return;

        $params["selector"] = $this->get_corrected_element_selector($params["selector"], $class);

        // scaling
        if(isset($params['superbox_secondary_scale_start'])) $superbox_secondary_scale_start  = $params['superbox_secondary_scale_start'];
        if(isset($params['superbox_secondary_scale_finish'])) $superbox_secondary_scale_finish = $params['superbox_secondary_scale_finish'];
        if(isset($params['superbox_primary_scale_start'])) $superbox_primary_scale_start    = $params['superbox_primary_scale_start'];
        if(isset($params['superbox_primary_scale_finish'])) $superbox_primary_scale_finish   = $params['superbox_primary_scale_finish'];

        if (isset($superbox_secondary_scale_start) && $superbox_secondary_scale_start!="")     $superbox_secondary_scale_start_transform_css = "transform: scale(".$superbox_secondary_scale_start.");";
        if (isset($superbox_secondary_scale_finish) && $superbox_secondary_scale_finish!="")    $superbox_secondary_scale_finish_transform_css = "transform: scale(".$superbox_secondary_scale_finish.");";
        if (isset($superbox_primary_scale_start) && $superbox_primary_scale_start!="")       $superbox_primary_scale_start_transform_css = "transform: scale(".$superbox_primary_scale_start.");";
        if (isset($superbox_primary_scale_finish) && $superbox_primary_scale_finish!="")      $superbox_primary_scale_finish_transform_css = "transform: scale(".$superbox_primary_scale_finish.");";
        // sliding
        if (isset($params['superbox_secondary_slide_direction']) && $params['superbox_secondary_slide_direction']) {
            $css = $this->slide_position($params['superbox_secondary_slide_direction'], $params['superbox_secondary_slide_distance']."px");

            if ($params['superbox_secondary_slide_inorout'] == "in") {
                $superbox_secondary_initial_css = $css['out_css'];
                $superbox_secondary_hover_css = $css['in_css'];
            } else if ($params['superbox_secondary_slide_inorout'] == "out") {
                $superbox_secondary_initial_css = $css['in_css'];
                $superbox_secondary_hover_css = $css['out_css'];            
            }
        }

        if (isset($params['superbox_primary_slide_direction']) && $params['superbox_primary_slide_direction']) {
            $css = $this->slide_position($params['superbox_primary_slide_direction'], $params['superbox_primary_slide_distance']."px");

            if ($params['superbox_primary_slide_inorout'] == "in") {
                $superbox_primary_initial_css = $css['out_css'];
                $superbox_primary_hover_css = $css['in_css'];
            } else if ($params['superbox_primary_slide_inorout'] == "out") {
                $superbox_primary_initial_css = $css['in_css'];
                $superbox_primary_hover_css = $css['out_css'];            
            }
        }

        ob_start();

        ?>

            <?php if(isset($params['superbox_transition_duration'])) : ?>
            <?php echo $params['selector']; ?> .oxy-superbox-secondary, 
            <?php echo $params['selector']; ?> .oxy-superbox-primary {
                <?php $this->output_single_css_property("transition-duration", $params['superbox_transition_duration']); ?>
            }
            <?php endif; ?>

            <?php if (  (isset($params['superbox_secondary_opacity_start']) && $params['superbox_secondary_opacity_start'] != "") ||
                        (isset($superbox_secondary_initial_css) && $superbox_secondary_initial_css != "") ||
                        (isset($superbox_secondary_scale_start_transform_css) && $superbox_secondary_scale_start_transform_css != "") 
                    ) : ?>
            <?php echo $params['selector']; ?> .oxy-superbox-secondary {
                <?php if(isset($params['superbox_secondary_opacity_start'])) $this->output_single_css_property("opacity", $params['superbox_secondary_opacity_start']); ?>
                <?php if(isset($superbox_secondary_initial_css)) echo $superbox_secondary_initial_css; ?>
                <?php if(isset($superbox_secondary_scale_start_transform_css)) echo $superbox_secondary_scale_start_transform_css; ?>
            }
            <?php endif; ?>

            <?php if (  ( isset($params['superbox_secondary_opacity_finish']) && $params['superbox_secondary_opacity_finish'] != "" )||
                        ( isset($superbox_secondary_hover_css) && $superbox_secondary_hover_css != "" ) || 
                        ( isset($superbox_secondary_scale_finish_transform_css) && $superbox_secondary_scale_finish_transform_css != "" )
                    ) : ?>
            <?php echo $params['selector']; ?>:hover .oxy-superbox-secondary {
                <?php if(isset($params['superbox_secondary_opacity_finish'])) $this->output_single_css_property("opacity", $params['superbox_secondary_opacity_finish']); ?>
                <?php if(isset($superbox_secondary_hover_css)) echo $superbox_secondary_hover_css; ?>
                <?php if(isset($superbox_secondary_scale_finish_transform_css)) echo $superbox_secondary_scale_finish_transform_css; ?>
            }
            <?php endif; ?>

            <?php if (  ( isset($params['superbox_primary_opacity_start']) && $params['superbox_primary_opacity_start'] != "" )||
                        ( isset($superbox_primary_initial_css) && $superbox_primary_initial_css != "" ) ||
                        ( isset($superbox_primary_scale_start_transform_css) && $superbox_primary_scale_start_transform_css != "" ) 
                    ) : ?>
            <?php echo $params['selector']; ?> .oxy-superbox-primary {
                <?php if(isset($params['superbox_primary_opacity_start'])) $this->output_single_css_property("opacity", $params['superbox_primary_opacity_start']); ?>
                <?php if(isset($superbox_primary_initial_css)) echo $superbox_primary_initial_css; ?>
                <?php if(isset($superbox_primary_scale_start_transform_css)) echo $superbox_primary_scale_start_transform_css; ?>
            }
            <?php endif; ?>

            <?php if (  ( isset($params['superbox_primary_opacity_finish']) && $params['superbox_primary_opacity_finish'] != "" )||
                        ( isset($superbox_primary_hover_css) && $superbox_primary_hover_css != "" )||
                        ( isset($superbox_primary_scale_finish_transform_css) && $superbox_primary_scale_finish_transform_css != "" )
                    ): ?>
            <?php echo $params['selector']; ?>:hover .oxy-superbox-primary {
                <?php if(isset($params['superbox_primary_opacity_finish'])) $this->output_single_css_property("opacity", $params['superbox_primary_opacity_finish']); ?>
                <?php if(isset($superbox_primary_hover_css)) echo $superbox_primary_hover_css; ?>
                <?php if(isset($superbox_primary_scale_finish_transform_css)) echo $superbox_primary_scale_finish_transform_css; ?>
            }
            <?php endif; ?>

        <?php return ob_get_clean();

    }


    /**
     * Shortcode output
     * 
     * @since 2.0
     * @author Louis & Ilya
     */

    function shortcode($atts, $content = null, $name = null ) {

        if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
            return '';
        }

        $options = $this->set_options( $atts );

        $this->param_array[$options['id']] = $options;

        ob_start(); ?>

        <div id='<?php echo esc_attr($options['selector']); ?>' class='<?php echo esc_attr($options['classes']); ?>' <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>>
          <div class='oxy-superbox-wrap'>
            <?php $this->output_builtin_shortcodes( $content ); ?>
          </div>
        </div>

        <?php $html = ob_get_clean();

        return $html;
    }


    /**
     * Output JS for toggle menu in responsive mode
     *
     * @since 2.0
     * @author Ilya K.
     */
    
    function output_js() { ?>

    <script type="text/javascript">

        jQuery('document').ready(function() {
            jQuery('.oxy-superbox')
                .on('touchstart', function () {
                    jQuery(this).trigger('hover');
                })
                .on('touchend', function () {
                    jQuery(this).trigger('hover');
                });
        });

    </script>

    <?php }


    /**
     * Output settings to control the view Superbox shown in builder
     *
     * @since 2.0
     * @author Ilya K.
     */

    function builder_settings() { 

        global $oxygen_toolbar; ?>

        <div class="oxygen-sidebar-flex-panel"
            ng-hide="!isActiveName('oxy_superbox')||hasOpenTabs('oxy_superbox')">
            
            <div class="oxygen-control-row">
                <div class='oxygen-control-wrapper'>
                    <label class='oxygen-control-label'><?php _e("Superbox Editing Mode","oxygen"); ?></label>
                    <div class='oxygen-control'>
                            <div class="oxygen-select oxygen-select-box-wrapper">
                                <div class="oxygen-select-box">
                                    <div class="oxygen-select-box-current">{{iframeScope.getSuperBoxEditingModeTitle()}}</div>
                                    <div class="oxygen-select-box-dropdown"></div>
                                </div>
                                <div class="oxygen-select-box-options">
                                    <div class="oxygen-select-box-option" 
                                        ng-click="iframeScope.setOptionModel('superbox_editing_mode','primary_only')"><?php _e("Show Primary Only","oxygen"); ?></div>
                                    <div class="oxygen-select-box-option" 
                                        ng-click="iframeScope.setOptionModel('superbox_editing_mode','secondary_only')"><?php _e("Show Secondary Only","oxygen") ?></div>
                                    <div class="oxygen-select-box-option" 
                                        ng-click="iframeScope.setOptionModel('superbox_editing_mode','as_hovered')"><?php _e("As If Hovered","oxygen")?></div>
                                    <div class="oxygen-select-box-option" 
                                        ng-click="iframeScope.setOptionModel('superbox_editing_mode','as_not_hovered')"><?php _e("As If Not Hovered","oxygen") ?></div>
                                    <div class="oxygen-select-box-option" 
                                        ng-click="iframeScope.setOptionModel('superbox_editing_mode','live')"><?php _e("Live","oxygen") ?></div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>

    <?php }
}

global $oxygen_vsb_components;
$oxygen_vsb_components['superbox'] = new Oxygen_VSB_SuperBox( array(
            'name'  => __('Superbox','oxygen'),
            'tag'   => 'oxy_superbox',
            'params'=> array(
                array(
                        "type"          => "textfield",
                        "heading"       => __("Animation Speed","oxygen"),
                        "param_name"    => "superbox_transition_duration",
                        "value"         => "0.5s",
                    ),
            ), 
            'tabs'  => array(
                'superbox_primary' => array(
                    'heading' => __('Primary','oxygen'),
                    'params' => array(
                        array(
                            "type"          => "radio",
                            "heading"       => __("Slide To", "oxygen"),
                            "param_name"    => "superbox_primary_slide_inorout",
                            "value"         => array(
                                                 'in'    => __("in to view", "oxygen"),
                                                 'out'   => __("out of view", "oxygen"),
                                            ),
                            "default"       => "",
                            "css"           => false,
                        ),
                        array(
                            "type"          => "radio",
                            "heading"       => __("Slide Direction", "oxygen"),
                            "param_name"    => "superbox_primary_slide_direction",
                            "value"         => array(
                                                 'top'      => __("top", "oxygen"),
                                                 'left'     => __("left", "oxygen"),
                                                 'right'    => __("right", "oxygen"),
                                                 'bottom'   => __("bottom", "oxygen"),
                                            ),
                            "default"       => "",
                            "css"           => false,
                        ),
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Initial Opacity", "oxygen"),
                            "param_name"    => "superbox_primary_opacity_start",
                            "value"         => "",
                            "param_units"   => " ",
                            "min"           => 0,
                            "max"           => 1,
                            "step"          => 0.1,
                            "css"           => false,
                        ),
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Opacity on Hover", "oxygen"),
                            "param_name"    => "superbox_primary_opacity_finish",
                            "value"         => "",
                            "param_units"   => " ",
                            "min"           => 0,
                            "max"           => 1,
                            "step"          => 0.1,
                            "css"           => false,
                        ),
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Initial Scale", "oxygen"),
                            "param_name"    => "superbox_primary_scale_start",
                            "value"         => "",
                            "param_units"   => " ",
                            "min"           => 0,
                            "max"           => 10,
                            "css"           => false,
                        ),
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Scale on Hover", "oxygen"),
                            "param_name"    => "superbox_primary_scale_finish",
                            "value"         => "",
                            "param_units"   => " ",
                            "min"           => 0,
                            "max"           => 10,
                            "css"           => false,
                        ),
                        array(
                            "type"          => "measurebox",
                            "heading"       => __("Override Initial Slide Position","oxygen"),
                            "param_name"    => "superbox_primary_slide_distance",
                            "value"         => "",
                            "param_units"   => "px"
                        ),
                        array(
                            "param_name"    => "superbox_primary_slide_distance-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),
                    ),
                ),
                'superbox_secondary' => array(
                    'heading' => __('Secondary','oxygen'),
                    'params' => array(
                        array(
                            "type"          => "radio",
                            "heading"       => __("Slide To", "oxygen"),
                            "param_name"    => "superbox_secondary_slide_inorout",
                            "value"         => array(
                                                 'in'    => __("in to view", "oxygen"),
                                                 'out'   => __("out of view", "oxygen"),
                                            ),
                            "default"       => "",
                            "css"           => false,
                        ),
                        array(
                            "type"          => "radio",
                            "heading"       => __("Slide Direction", "oxygen"),
                            "param_name"    => "superbox_secondary_slide_direction",
                            "value"         => array(
                                                 'top'      => __("top", "oxygen"),
                                                 'left'     => __("left", "oxygen"),
                                                 'right'    => __("right", "oxygen"),
                                                 'bottom'   => __("bottom", "oxygen"),
                                            ),
                            "default"       => "",
                            "css"           => false,
                        ),
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Initial Opacity", "oxygen"),
                            "param_name"    => "superbox_secondary_opacity_start",
                            "value"         => "",
                            "param_units"   => " ",
                            "min"           => 0,
                            "max"           => 1,
                            "step"          => 0.1,
                            "css"           => false,
                        ),
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Opacity on Hover", "oxygen"),
                            "param_name"    => "superbox_secondary_opacity_finish",
                            "value"         => "",
                            "param_units"   => " ",
                            "min"           => 0,
                            "max"           => 1,
                            "step"          => 0.1,
                            "css"           => false,
                        ),
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Initial Scale", "oxygen"),
                            "param_name"    => "superbox_secondary_scale_start",
                            "value"         => "",
                            "param_units"   => " ",
                            "min"           => 0,
                            "max"           => 10,
                            "css"           => false,
                        ),
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Scale on Hover", "oxygen"),
                            "param_name"    => "superbox_secondary_scale_finish",
                            "value"         => "",
                            "param_units"   => " ",
                            "min"           => 0,
                            "max"           => 10,
                            "css"           => false,
                        ),
                        array(
                            "type"          => "measurebox",
                            "heading"       => __("Override Initial Slide Position","oxygen"),
                            "param_name"    => "superbox_secondary_slide_distance",
                            "value"         => "",
                            "param_units"   => "px"
                        ),
                        array(
                            "param_name"    => "superbox_secondary_slide_distance-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),
                    ),
                ),
            ),
               
            'advanced'  => array(
                "other" => array(
                        "values"    => array (
                            "superbox_editing_mode" => "live"
                        )
                    ),
            )
        )
);