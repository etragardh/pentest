<?php

/**
 * Icon box Component Class
 * 
 * @since 2.0
 * @author Louis & Ilya
 */


class Oxygen_VSB_Icon_Box extends CT_Component{

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
            "icon_box_content_alignment",
            "icon_box_icon_position",
            "icon_box_icon_vertical_alignment",
            "icon_box_icon_space_before",
            "icon_box_icon_space_after",
            "icon_box_heading_typography",
            "icon_box_text_typography",
            "icon_box_heading_space_above",
            "icon_box_heading_space_below",
            "icon_box_text_space_above",
            "icon_box_text_space_below",
            "icon_box_link_space_above",
            "icon_box_link_space_below",
            'icon_box_heading_typography_font-family',
            'icon_box_heading_typography_font-size',
            'icon_box_heading_typography_color',
            'icon_box_heading_typography_font-weight',
            'icon_box_heading_typography_line-height',
            'icon_box_heading_typography_letter-spacing',
            'icon_box_heading_typography_text-decoration',
            'icon_box_heading_typography_font-style',
            'icon_box_heading_typography_text-transform',
            'icon_box_text_typography_font-family',
            'icon_box_text_typography_font-size',
            'icon_box_text_typography_color',
            'icon_box_text_typography_font-weight',
            'icon_box_text_typography_line-height',
            'icon_box_text_typography_letter-spacing',
            'icon_box_text_typography_text-decoration',
            'icon_box_text_typography_font-style',
            'icon_box_text_typography_text-transform',
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

        $options['selector'] = ".oxy-icon-box";

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
            if (strpos($key,"icon_box")!==false) {
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

        global $media_queries_list;

        if ($params===false) {
            $params = $this->param_array;
        }

        if ($this->in_repeater_cycle()) return;

        $params["selector"] = $this->get_corrected_element_selector($params["selector"], $class);

        if (isset($params['icon_box_icon_position'])){
            if ($params['icon_box_icon_position'] == 'top') {
                $icon_position_flex_direction = 'column';
            } else if ($params['icon_box_icon_position'] == 'left') {
                $icon_position_flex_direction = 'row';
            } else if ($params['icon_box_icon_position'] == 'right') {
                $icon_position_flex_direction = 'row-reverse';
            } else if ($params['icon_box_icon_position'] == 'bottom') {
                $icon_position_flex_direction = 'column-reverse';
            } else {
                $icon_position_flex_direction = 'top';
            }
        }

        if (isset($params["icon_box_icon_position"]) && ($params["icon_box_icon_position"]=='left' || $params["icon_box_icon_position"]=='right')) {
            $icon_box_icon_space_before = (isset($params["icon_box_icon_space_before"])) ? $params["icon_box_icon_space_before"] : $defaults["icon_box_icon_space_before"];
            $icon_box_icon_space_after = (isset($params["icon_box_icon_space_after"])) ? $params["icon_box_icon_space_after"] : $defaults["icon_box_icon_space_after"];

            $iconmargincss = "margin-left: ".$icon_box_icon_space_before .$this->get_css_unit('icon_box_icon_space_before', $params, $defaults).";\n";
            $iconmargincss .= "margin-right: ".$icon_box_icon_space_after .$this->get_css_unit('icon_box_icon_space_after', $params, $defaults).";\n";
            $iconmargincss .= "margin-bottom: 0; margin-top: 0;";
        } 
        else 
        //if ($params["icon_box_icon_position"]=='top' || $params["icon_box_icon_position"]=='bottom')
        {
            $iconmargincss = "";
            if (isset($params["icon_box_icon_space_before"]) && $params["icon_box_icon_space_before"]) {
                $iconmargincss .= "margin-top: ".$params["icon_box_icon_space_before"].
                    $this->get_css_unit('icon_box_icon_space_before', $params, $defaults).";\n";
            }
            if (isset($params["icon_box_icon_space_after"]) && $params["icon_box_icon_space_after"]) {
                $iconmargincss .= "margin-bottom: ".$params["icon_box_icon_space_after"].
                    $this->get_css_unit('icon_box_icon_space_after', $params, $defaults).";\n";
            }
        }

        if (isset($params["icon_box_icon_position"]) && ($params["icon_box_icon_position"]=='left' || $params["icon_box_icon_position"]=='right')) {
            $icon_vertical_alignment_align_self = $params['icon_box_icon_vertical_alignment'];
        } 
        else if (isset($params['icon_box_content_alignment']) || (isset($params["icon_box_icon_position"]) && ($params["icon_box_icon_position"]=='top' || $params["icon_box_icon_position"]=='bottom'))){
            
            $icon_box_content_alignment = $params['icon_box_content_alignment'];
            
            if ($icon_box_content_alignment =='left') {
                $icon_vertical_alignment_align_self = "flex-start";
            } else if ($icon_box_content_alignment =='center') {
                $icon_vertical_alignment_align_self = "center";
            } else if ($icon_box_content_alignment =='right') {
                $icon_vertical_alignment_align_self = "flex-end";
            } else {
                $icon_vertical_alignment_align_self = "flex-start";
            }
        }

        if (isset($params["icon_box_mobile_content_alignment"])) {
            if ($params["icon_box_mobile_content_alignment"]=='left') {
                $mobileflexalign = "flex-start";
                $mobiletextalign = "left";
            } else if ($params["icon_box_mobile_content_alignment"]=='center') {
                $mobileflexalign = "center";
                $mobiletextalign = "center";
            } else if ($params["icon_box_mobile_content_alignment"]=='right') {
                $mobileflexalign = "flex-end";
                $mobiletextalign = "right";
            } else {
                $mobileflexalign = "flex-start";
                $mobiletextalign = "left";            
            }
        }

        ob_start(); 

        ?> 
            
            <?php if(isset($params['icon_box_content_alignment'])||isset($icon_position_flex_direction)) : ?>
            <?php echo $params["selector"]; ?> {
                <?php if(isset($params['icon_box_content_alignment'])) $this->output_single_css_property("text-align", $params['icon_box_content_alignment']); ?>
                <?php if(isset($icon_position_flex_direction)) $this->output_single_css_property("flex-direction", $icon_position_flex_direction); ?>
            }
            <?php endif; ?>

            <?php if ($iconmargincss!=""||isset($icon_vertical_alignment_align_self)): ?>
            <?php echo $params["selector"]; ?> .oxy-icon-box-icon {
                <?php echo $iconmargincss; ?>
                <?php if(isset($icon_vertical_alignment_align_self)) $this->output_single_css_property("align-self", $icon_vertical_alignment_align_self); ?>
            }
            <?php endif; ?>

            <?php $css = ""; ?>
            <?php $css .= $this->typography_to_css($params, 'icon_box_heading_typography', $defaults); ?>
            <?php if(isset($params['icon_box_heading_space_above'])) 
                $css .= $this->get_single_css_property("margin-top", $params['icon_box_heading_space_above'], $this->get_css_unit('icon_box_heading_space_above', $params, $defaults)); ?>
            <?php if(isset($params['icon_box_heading_space_below'])) 
                $css .= $this->get_single_css_property("margin-bottom", $params['icon_box_heading_space_below'], $this->get_css_unit('icon_box_heading_space_below', $params, $defaults)); ?>
            <?php if ($css!="") : ?>
            <?php echo $params["selector"]; ?> .oxy-icon-box-heading {
                <?php echo $css; ?>
            }
            <?php endif; ?>

            <?php $css = ""; ?>
            <?php $css .= $this->typography_to_css($params, 'icon_box_text_typography', $defaults); ?>
            <?php if(isset($params['icon_box_heading_space_above'])) 
                $css .= $this->get_single_css_property("margin-top", $params['icon_box_heading_space_above'], $this->get_css_unit('icon_box_text_space_above', $params, $defaults)); ?>
            <?php if(isset($params['icon_box_heading_space_below'])) 
                $css .= $this->get_single_css_property("margin-bottom", $params['icon_box_heading_space_below'], $this->get_css_unit('icon_box_text_space_below', $params, $defaults)); ?>
            <?php if(isset($icon_vertical_alignment_align_self)) 
                $css .= $this->get_single_css_property("align-self", $icon_vertical_alignment_align_self); ?>
            <?php if ($css!="") : ?>
            <?php echo $params["selector"]; ?> .oxy-icon-box-text {
                <?php echo $css; ?>
            }
            <?php endif; ?>

            <?php if (isset($params['icon_box_link_space_above'])||isset($params['icon_box_link_space_below'])) : ?>
            <?php echo $params["selector"]; ?> .oxy-icon-box-link {
                <?php if(isset($params['icon_box_link_space_above'])) $this->output_single_css_property("margin-top",
                    $params['icon_box_link_space_above'], $this->get_css_unit('icon_box_link_space_above', $params, $defaults)); ?>
                <?php if(isset($params['icon_box_link_space_below'])) $this->output_single_css_property("margin-bottom",
                    $params['icon_box_link_space_below'], $this->get_css_unit('icon_box_link_space_below', $params, $defaults)); ?>
            }
            <?php endif; ?>

            <?php 
            $max_size = false;
            
            if(isset($params['icon_box_vertical_layout_below']) && !empty(trim($params['icon_box_vertical_layout_below']))) {
                $max_size = $media_queries_list[$params['icon_box_vertical_layout_below']]['maxSize']; 
            }
            
            if ($max_size) { ?>

            @media (max-width: <?php echo $max_size; ?>) {
                <?php echo $params["selector"]; ?>.oxy-icon-box {
                    flex-direction: column !important;
                    <?php $this->output_single_css_property("text-align", $mobiletextalign); ?>
                }   

                <?php echo $params["selector"]; ?> .oxy-icon-box-icon {
                    margin-left: 0;
                    margin-right: 0;
                    <?php $this->output_single_css_property("margin-top",
                        $params['icon_box_icon_space_before'], $this->get_css_unit('icon_box_icon_space_before', $params, $defaults)); ?>
                    <?php $this->output_single_css_property("margin-bottom",
                        $params['icon_box_icon_space_after'], $this->get_css_unit('icon_box_icon_space_after', $params, $defaults)); ?>
                }

                <?php echo $params["selector"]; ?> .oxy-icon-box-icon, 
                <?php echo $params["selector"]; ?> .oxy-icon-box-text {
                    <?php $this->output_single_css_property("align-self", $mobileflexalign); ?>
                }
            }

        <?php }

        return ob_get_clean();
    }


    /**
     * Shortcode output
     * 
     * @since 2.0
     * @author Louis & Ilya
     */

    function shortcode($atts, $content, $name) {
        
        if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
            return '';
        }

        $options = $this->set_options( $atts );

        $this->param_array[$options['id']] = $options;

	    $editable_icon_box_heading = oxygen_base64_decode_for_json($this->param_array[$options['id']]['icon_box_heading']);
	    $editable_icon_box_text = oxygen_base64_decode_for_json($this->param_array[$options['id']]['icon_box_text']);

	    if( class_exists( 'Oxygen_Gutenberg' ) ) {
		    $editable_icon_box_heading = Oxygen_Gutenberg::decorate_attribute($options, $editable_icon_box_heading, 'string', 'heading');
		    $editable_icon_box_text = Oxygen_Gutenberg::decorate_attribute($options, $editable_icon_box_text, 'string', 'text');
	    }

        ob_start(); ?>

        <div id='<?php echo esc_attr($options['selector']); ?>' class='<?php echo esc_attr($options['classes']); ?>' <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>>
          <div class='oxy-icon-box-icon'>
              <?php $this->output_builtin_shortcodes( $content ); ?>
          </div>
          
          <div class='oxy-icon-box-content'>
            <h2 id='<?php echo esc_attr($options['selector']); ?>_heading' class='oxy-icon-box-heading oxygenberg-<?php echo esc_attr($options['selector']); ?>_heading'>
              <?php echo $editable_icon_box_heading; ?>
            </h2>
            <p id='<?php echo esc_attr($options['selector']); ?>_text' class='oxy-icon-box-text oxygenberg-<?php echo esc_attr($options['selector']); ?>_text'>
              <?php echo $editable_icon_box_text; ?>
            </p>
            <div class='oxy-icon-box-link'>
              <?php $this->output_builtin_shortcodes( $content, false ); ?>
            </div>
          </div>
        </div>

        <?php $html = ob_get_clean();

        return $html;
    }
}

global $oxygen_vsb_components;
$oxygen_vsb_components['icon_box'] = new Oxygen_VSB_Icon_Box( array(
            'name'  => __('Icon Box','oxygen'),
            'tag'   => 'oxy_icon_box',
            'tabs'  => array(
                'layout_spacing' => array(
                    'heading' => __('Layout & Spacing','oxygen'),
                    'params' => array(
                        array(
                            "type"          => "radio",
                            "heading"       => __("Content alignment", "oxygen"),
                            "param_name"    => "icon_box_content_alignment",
                            "value"         => array(
                                                 'left'     => __("left", "oxygen"),
                                                 'center'   => __("center", "oxygen"),
                                                 'right'    => __("right", "oxygen"),
                                            ),
                            "css"           => false,
                        ),
                        array(
                            "type"          => "radio",
                            "heading"       => __("Icon Position", "oxygen"),
                            "param_name"    => "icon_box_icon_position",
                            "value"         => array(
                                                 'top'      => __("top", "oxygen"),
                                                 'left'     => __("left", "oxygen"),
                                                 'right'    => __("right", "oxygen"),
                                                 'bottom'   => __("bottom", "oxygen"),
                                            ),
                            "css"           => false,
                        ),
                        array(
                            "type"          => "radio",
                            "heading"       => __("Icon Vertical Alignment", "oxygen"),
                            "param_name"    => "icon_box_icon_vertical_alignment",
                            "value"         => array(
                                                 'flex-start'   => __("top", "oxygen"),
                                                 'center'       => __("middle", "oxygen"),
                                                 'flex-end'     => __("bottom", "oxygen"),
                                            ),
                            "condition"     => "icon_box_icon_position=left||icon_box_icon_position=right",
                            "css"           => false,
                        ),

                        // Icon
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Icon Space Before", "oxygen"),
                            "param_name"    => "icon_box_icon_space_before",
                            "value"         => "",
                            "css"           => false,
                        ),
                        array(
                            "param_name"    => "icon_box_icon_space_before-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),

                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Icon Space After", "oxygen"),
                            "param_name"    => "icon_box_icon_space_after",
                            "value"         => "12",
                            "css"           => false,
                        ),
                        array(
                            "param_name"    => "icon_box_icon_space_after-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),

                        // Heading
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Heading Space Above", "oxygen"),
                            "param_name"    => "icon_box_heading_space_above",
                            "value"         => "",
                            "css"           => false,
                        ),
                        array(
                            "param_name"    => "icon_box_heading_space_above-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),

                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Heading Space Below", "oxygen"),
                            "param_name"    => "icon_box_heading_space_below",
                            "value"         => "12",
                            "css"           => false,
                        ),
                        array(
                            "param_name"    => "icon_box_heading_space_below-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),

                        // Text
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Text Space Above", "oxygen"),
                            "param_name"    => "icon_box_text_space_above",
                            "value"         => "",
                            "css"           => false,
                        ),
                        array(
                            "param_name"    => "icon_box_text_space_above-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),

                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Text Space Below", "oxygen"),
                            "param_name"    => "icon_box_text_space_below",
                            "value"         => "20",
                            "css"           => false,
                        ),
                        array(
                            "param_name"    => "icon_box_text_space_below-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),

                        // Text
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Link Space Above", "oxygen"),
                            "param_name"    => "icon_box_link_space_above",
                            "value"         => "20",
                            "css"           => false,
                        ),
                        array(
                            "param_name"    => "icon_box_link_space_above-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),

                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Link Space Below", "oxygen"),
                            "param_name"    => "icon_box_link_space_below",
                            "value"         => "",
                            "css"           => false,
                        ),
                        array(
                            "param_name"    => "icon_box_link_space_below-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),
                    ),
                ),

                'responsive' => array(
                    'heading' => __('Responsive','oxygen'),
                    'params' => array(
                        array(
                            "type"          => "medialist",
                            "heading"       => __("Vertical Layout Below","oxygen"),
                            "value"         => "",
                            "param_name"    => "icon_box_vertical_layout_below",
                            "css"           => false
                        ),
                        array(
                            "type"          => "radio",
                            "heading"       => __("Content Alignment", "oxygen"),
                            "param_name"    => "icon_box_mobile_content_alignment",
                            "value"         => array(
                                                 'left'     => __("left", "oxygen"),
                                                 'center'   => __("center", "oxygen"),
                                                 'right'    => __("right", "oxygen"),
                                            ),
                            "css"           => false,
                        ),
                    ),
                ),

                'typography' => array(
                    'heading' => __('Typography','oxygen'),
                    'tabs' => array(
                        'heading_typography' => array(
                            'heading' => __("Heading","oxygen"),
                            'params' => array(
                                array(
                                    "type"          => "typography",
                                    "param_name"    => "icon_box_heading_typography",
                                    "param_values"  => array(
                                                        'font-family'           => '',
                                                        'font-size'             => '21',
                                                        'font-size-unit'        => 'px',
                                                        'color'                 => '',
                                                        'font-weight'           => '',
                                                        'line-height'           => '',
                                                        'letter-spacing'        => '',
                                                        'letter-spacing-unit'   => 'px',
                                                        'text-decoration'       => '',
                                                        'font-style'            => '',
                                                        'text-transform'        => '',
                                                    ),
                                    "css"           => false,
                                ),
                            )
                        ),
                        'text_typography' => array(
                            'heading' => __("Text","oxygen"),
                            'params' => array(
                                array(
                                    "type"          => "typography",
                                    "param_name"    => "icon_box_text_typography",
                                    "param_values"  => array(
                                                        'font-family'           => '',
                                                        'font-size'             => '16',
                                                        'font-size-unit'        => 'px',
                                                        'color'                 => '',
                                                        'font-weight'           => '',
                                                        'line-height'           => '',
                                                        'letter-spacing'        => '',
                                                        'letter-spacing-unit'   => 'px',
                                                        'text-decoration'       => '',
                                                        'font-style'            => '',
                                                        'text-transform'        => '',
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
                            'width'      => '100',
                            'width-unit' => '%',
                            )
                    ),
                "data" => array(
                        "values"    => array (
                            "icon_box_heading" => 'Oxygen Icon Box',
                            "icon_box_text" => 'Drag a button, link, or anything else into the icon box to place it below the text. Lorem ipsum dolor sit amet elit.',
                            "icon_box_icon" => 'https://upload.wikimedia.org/wikipedia/commons/4/4f/Google_Photos_icon.svg'
                            )
                    ),
            )
        )
);