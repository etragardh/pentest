<?php

/**
 * Progress Bar Component Class
 * 
 * @since 2.0
 * @author Louis & Ilya
 */


class Oxygen_VSB_Progress_Bar extends CT_Component{

    public $param_array = array();
    public $data_array = array();

    function __construct($options) {

        // run initialization
        $this->init( $options );

        // Add shortcodes
        add_shortcode( $this->options['tag'], array( $this, 'shortcode' ) );
        add_oxygen_element( $this->options['tag'], array( $this, 'shortcode' ) );

        // change component button place
        remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
        add_action("oxygen_helpers_components_composite", array( $this, "component_button" ) );

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
        add_action("ct_toolbar_component_settings", array( $this, "header_settings"), 9 );
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
            "progress_bar_progress",
            "progress_bar_bar_color",
            "progress_bar_background_color",
            "progress_bar_bar_padding",
            "progress_bar_stripes",
            "progress_bar_animation_stripes",
            "progress_bar_animate_width",
            "progress_bar_animation_stripes_duration",
            "progress_bar_animation_width_duration",
            'progress_bar_left_text_typography_font-family',
            'progress_bar_left_text_typography_font-size',
            'progress_bar_left_text_typography_color',
            'progress_bar_left_text_typography_font-weight',
            'progress_bar_left_text_typography_line-height',
            'progress_bar_left_text_typography_letter-spacing',
            'progress_bar_left_text_typography_text-decoration',
            'progress_bar_left_text_typography_font-style',
            'progress_bar_left_text_typography_text-transform',
            'progress_bar_left_text_typography_-webkit-font-smoothing',
            'progress_bar_right_text_typography_font-family',
            'progress_bar_right_text_typography_font-size',
            'progress_bar_right_text_typography_color',
            'progress_bar_right_text_typography_font-weight',
            'progress_bar_right_text_typography_line-height',
            'progress_bar_right_text_typography_letter-spacing',
            'progress_bar_right_text_typography_text-decoration',
            'progress_bar_right_text_typography_font-style',
            'progress_bar_right_text_typography_text-transform',
            'progress_bar_right_text_typography_-webkit-font-smoothing',

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

        $options['selector'] = ".oxy-progress-bar";

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

        $is_icon_box = false;

        foreach ($options as $key => $value) {
            if (strpos($key,"progress_bar")!==false) {
                $is_icon_box = true;
                break;
            }
        }

        $options['selector'] = ".".$class;
        if ($is_icon_box) {
            $css .= $this->generate_css($options, true, $defaults[$this->options['tag']]);
            $is_icon_box = false;
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

        if (isset($params['progress_bar_stripes']) && $params['progress_bar_stripes'] == "true") {
            $stripecss = "background-image: linear-gradient(-45deg,rgba(255,255,255,.12) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.12) 50%,rgba(255,255,255,.12) 75%,transparent 75%,transparent);";
        } 
        if (isset($params['progress_bar_stripes']) && $params['progress_bar_stripes'] == "false") {
            $stripecss = 'background-image: none;';
        }

        $animation_css = array();

        if (isset($params['progress_bar_animation_stripes']) && $params['progress_bar_animation_stripes'] == "true") {
            $duration = $params['progress_bar_animation_stripes_duration'];
            if (!$duration) {
                $duration = $defaults['progress_bar_animation_stripes_duration'];
            }
            $animation_css['stripes'] = "oxy_progress_bar_stripes ".$duration." linear infinite";
        }
        if (isset($params['progress_bar_animation_stripes']) && $params['progress_bar_animation_stripes'] == "false") {
            $animation_css['stripes'] = "none 0s paused";
        }

        if (isset($params['progress_bar_animate_width']) && $params['progress_bar_animate_width'] == "true") {
            $duration = $params['progress_bar_animation_width_duration'];
            if (!$duration) {
                $duration = $defaults['progress_bar_animation_width_duration'];
            }
            $animation_css['width'] = "oxy_progress_bar_width ".$duration." ease-out 1";
        }
        if (isset($params['progress_bar_animate_width']) && $params['progress_bar_animate_width'] == "false") {
            $animation_css['width'] = "none 0s paused";
        }
        
        ob_start(); ?>

        <?php if(isset($params['progress_bar_background_color'])) : ?>
        <?php $css = $this->get_single_css_property("background-color", oxygen_vsb_get_global_color_value($params['progress_bar_background_color'])); ?>
        <?php if ($css != "" || isset($stripecss) || isset($animation_css['stripes'])) : ?>
        <?php echo $params["selector"]; ?> .oxy-progress-bar-background {
            <?php echo $css; ?>
            <?php if(isset($stripecss)) echo $stripecss; ?>
            <?php if(isset($animation_css['stripes'])) $this->output_single_css_property("animation", $animation_css['stripes']); ?>
        }
        <?php endif; ?>
        <?php endif; ?>

        <?php if(isset($params['progress_bar_progress'])) : ?>
        <?php echo $params["selector"]; ?> .oxy-progress-bar-progress-wrap {
            <?php $this->output_single_css_property("width", $params['progress_bar_progress'], $this->get_css_unit('progress_bar_progress', $params, $defaults)); ?>
        }
        <?php endif; ?>

        <?php if(isset($params['progress_bar_bar_color'])) : ?>
        <?php $css = ""; ?>
        <?php $css .= $this->get_single_css_property("background-color", oxygen_vsb_get_global_color_value($params['progress_bar_bar_color'])); ?>
        <?php if(isset($params['progress_bar_bar_padding'])) 
            $css .= $this->get_single_css_property("padding", $params['progress_bar_bar_padding'], $this->get_css_unit('progress_bar_bar_padding', $params, $defaults)); ?>
        <?php $css .= $this->get_single_css_property("animation", implode(", ", $animation_css)); ?>
        <?php if ( $css != "" || isset($stripecss)) : ?>
        <?php echo $params["selector"]; ?> .oxy-progress-bar-progress {
            <?php echo $css; ?>
            <?php if(isset($stripecss)) echo $stripecss; ?>        
        }
        <?php endif; ?>
        <?php endif; ?>

        <?php $css = $this->typography_to_css($params, 'progress_bar_left_text_typography', $defaults); ?>
        <?php if ($css != "") : ?>
        <?php echo $params["selector"]; ?> .oxy-progress-bar-overlay-text {
            <?php echo $css; ?>
        }
        <?php endif; ?>

        <?php $css = $this->typography_to_css($params, 'progress_bar_right_text_typography', $defaults); ?>
        <?php if ($css != "") : ?>
        <?php echo $params["selector"]; ?> .oxy-progress-bar-overlay-percent {
            <?php echo $css; ?>
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

    function shortcode($atts, $content, $name) {

        if (! $this->validate_shortcode( $atts, $content, $name ) ) {
            return '';
        }

        $options = $this->set_options( $atts );

        $this->param_array[$options['id']] = $options;

        ob_start(); ?>

        <div id='<?php echo esc_attr($options['selector']); ?>' class='<?php echo esc_attr($options['classes']); ?>' <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>>
            <div class='oxy-progress-bar-background'>
                <div class='oxy-progress-bar-progress-wrap'>
                    <div class='oxy-progress-bar-progress'>
                        <div class='oxy-progress-bar-overlay-text'>
                            <?php echo oxygen_base64_decode_for_json($this->param_array[$options['id']]['progress_bar_left_text']); ?>
                        </div>
                        <div class='oxy-progress-bar-overlay-percent'><?php echo oxygen_base64_decode_for_json($this->param_array[$options['id']]['progress_bar_right_text']); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <?php $html = ob_get_clean();

        return $html;
    }

    /**
     * Output special settings in Basic Styles tab
     *
     * @since 2.0
     */

    function header_settings() { ?>

        <div class="oxygen-control-row"
            ng-show="isActiveName('<?php echo $this->options['tag']; ?>')&&!hasOpenTabs('oxy_progress_bar')&&iframeScope.getOption('progress_bar_right_text')==''">
            <div class="oxygen-control-wrapper">
                <div id="oxygen-add-another-row" class="oxygen-add-section-element"
                    ng-click="iframeScope.setOptionModel('progress_bar_right_text','85%')">
                    <img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/typography.svg' />
                    <img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/typography.svg' />
                    <?php _e("Re-enable Deleted Text","oxygen"); ?>
                </div>
            </div>
        </div>

    <?php }
}

global $oxygen_vsb_components;
$oxygen_vsb_components['progress_bar'] = new Oxygen_VSB_Progress_Bar( array(
            'name'  => __('Progress Bar','oxygen'),
            'tag'   => 'oxy_progress_bar',
            'params'=> array(
                array(
                    "type"          => "slider-measurebox",
                    "heading"       => __("Progress", "oxygen"),
                    "param_name"    => "progress_bar_progress",
                    "param_untis"    => "%",
                    "value"         => "85",
                    "min"           => "0",
                    "max"           => "100",
                ),
                array(
                    "param_name"    => "progress_bar_progress-unit",
                    "value"         => "%",
                    "hidden"        => true,
                ),
            ), 
            'tabs'  => array(
                'progress_bar' => array(
                    'heading' => __('Progress Bar','oxygen'),
                    'params' => array(
                        array(
                            "type"          => "colorpicker",
                            "heading"       => __("Bar Color"),
                            "param_name"    => "progress_bar_bar_color",
                            "value"         => "#66aaff",
                        ),
                        array(
                            "type"          => "colorpicker",
                            "heading"       => __("Background Color"),
                            "param_name"    => "progress_bar_background_color",
                            "value"         => "#000000",
                        ),
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Bar Padding", "oxygen"),
                            "param_name"    => "progress_bar_bar_padding",
                            "value"         => "40",
                            "css"           => false,
                        ),
                        array(
                            "param_name"    => "progress_bar_bar_padding-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),
                        array(
                            "type"          => "radio",
                            "heading"       => __("Stripes", "oxygen"),
                            "param_name"    => "progress_bar_stripes",
                            "value"         => array(
                                                 'true'     => __("true", "oxygen"),
                                                 'false'    => __("false", "oxygen"),
                                            ),
                            "css"           => false,
                        ),
                    ),
                ),

                'animation' => array(
                    'heading' => __('Animation','oxygen'),
                    'params' => array(
                       array(
                            "type"          => "radio",
                            "heading"       => __("Animation Stripes", "oxygen"),
                            "param_name"    => "progress_bar_animation_stripes",
                            "value"         => array(
                                                 'true'     => __("true", "oxygen"),
                                                 'false'    => __("false", "oxygen"),
                                            ),
                            "default"       => "false",
                            "css"           => false,
                        ),
                       array(
                            "type"          => "radio",
                            "heading"       => __("Animate Width", "oxygen"),
                            "param_name"    => "progress_bar_animate_width",
                            "value"         => array(
                                                 'true'     => __("true", "oxygen"),
                                                 'false'    => __("false", "oxygen"),
                                            ),
                            "default"       => "false",
                            "css"           => false,
                        ),
                        array(
                            "type"          => "textfield",
                            "heading"       => __("Animation Stripes Duration","oxygen"),
                            "param_name"    => "progress_bar_animation_stripes_duration",
                            "value"         => "1s",
                            "css"           => false
                        ),
                        array(
                            "type"          => "textfield",
                            "heading"       => __("Animation Width Duration","oxygen"),
                            "param_name"    => "progress_bar_animation_width_duration",
                            "value"         => "4s",
                            "css"           => false
                        ),
                    ),
                ),

                'typography' => array(
                    'heading' => __('Typography','oxygen'),
                    'tabs' => array(
                        'left_text_typography' => array(
                            'heading' => __("Left Text","oxygen"),
                            'params' => array(
                                array(
                                    "type"          => "typography",
                                    "param_name"    => "progress_bar_left_text_typography",
                                    "param_values"  => array(
                                                        'font-family'           => '',
                                                        'font-size'             => '30',
                                                        'font-size-unit'        => 'px',
                                                        'color'                 => '',
                                                        'font-weight'           => '900',
                                                        'line-height'           => '',
                                                        'letter-spacing'        => '',
                                                        'letter-spacing-unit'   => 'px',
                                                        'text-decoration'       => '',
                                                        'font-style'            => '',
                                                        'text-transform'        => '',
                                                        '-webkit-font-smoothing'=> 'subpixel-antialiased'
                                                    ),
                                    "css"           => false,
                                ),
                            )
                        ),
                        'right_text_typography' => array(
                            'heading' => __("Right Text","oxygen"),
                            'params' => array(
                                array(
                                    "type"          => "typography",
                                    "param_name"    => "progress_bar_right_text_typography",
                                    "param_values"  => array(
                                                        'font-family'           => '',
                                                        'font-size'             => '12',
                                                        'font-size-unit'        => 'px',
                                                        'color'                 => '',
                                                        'font-weight'           => '',
                                                        'line-height'           => '',
                                                        'letter-spacing'        => '',
                                                        'letter-spacing-unit'   => 'px',
                                                        'text-decoration'       => '',
                                                        'font-style'            => '',
                                                        'text-transform'        => '',
                                                        '-webkit-font-smoothing'=> ''
                                                    ),
                                    "css"           => false,
                                ),
                            )
                        ),
                    ),
                    'params' => array()
                ),
            ),
            'advanced'  => array(
                "positioning" => array(
                        "values"    => array (
                            )
                    ),
                "data" => array(
                        "values"    => array (
                            "progress_bar_left_text" => 'Lorem Ipsum Dolor',
                            "progress_bar_right_text" => '85%',
                            )
                    ),
            ),
            'not_css_params' => array(
                'progress_bar_left_text',
                'progress_bar_right_text',
            )
        )
);