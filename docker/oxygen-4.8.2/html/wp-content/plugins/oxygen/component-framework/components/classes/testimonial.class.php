<?php

/**
 * Testimonial Component Class
 * 
 * @since 2.0
 * @author Louis & Ilya
 */

class Oxygen_VSB_Testimonial extends CT_Component {

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

        // render preveiew with AJAX
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
            "testimonial_layout",
            "testimonial_content_alignment",

            "testimonial_image_position",
            "testimonial_image_size",
            "testimonial_image_spacing",

            "testimonial_text_space_below",
            "testimonial_author_space_below",
            "testimonial_author_info_space_below",
        
            'testimonial_text_typography_font-family',
            'testimonial_text_typography_font-size',
            'testimonial_text_typography_color',
            'testimonial_text_typography_font-weight',
            'testimonial_text_typography_line-height',
            'testimonial_text_typography_letter-spacing',
            'testimonial_text_typography_text-decoration',
            'testimonial_text_typography_font-style',
            'testimonial_text_typography_text-transform',
            'testimonial_text_typography_-webkit-font-smoothing',

            'testimonial_author_typography_font-family',
            'testimonial_author_typography_font-size',
            'testimonial_author_typography_color',
            'testimonial_author_typography_font-weight',
            'testimonial_author_typography_line-height',
            'testimonial_author_typography_letter-spacing',
            'testimonial_author_typography_text-decoration',
            'testimonial_author_typography_font-style',
            'testimonial_author_typography_text-transform',
            'testimonial_author_typography_-webkit-font-smoothing',

            'testimonial_author_info_typography_font-family',
            'testimonial_author_info_typography_font-size',
            'testimonial_author_info_typography_color',
            'testimonial_author_info_typography_font-weight',
            'testimonial_author_info_typography_line-height',
            'testimonial_author_info_typography_letter-spacing',
            'testimonial_author_info_typography_text-decoration',
            'testimonial_author_info_typography_font-style',
            'testimonial_author_info_typography_text-transform',
            'testimonial_author_info_typography_-webkit-font-smoothing',
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }


    /**
     * Generate CSS for defaults .oxy-testimonial class
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

        $options['selector'] = ".oxy-testimonial";

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

        $is_testimonial = false;

        foreach ($options as $key => $value) {
            if (strpos($key,"testimonial_")!==false) {
                $is_testimonial = true;
                break;
            }
        }

        $options['selector'] = ".".$class;

        if ($is_testimonial) {
            $css .= $this->generate_css($options, true, $defaults["oxy_testimonial"]);
            $is_testimonial = false;
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
        
        $options = $states['original'];
        $options['selector'] = $selector;

        return $styles . $this->generate_css($options, false, $defaults);
    }


    /**
     * Generate CSS based on passed params
     * 
     * @since 2.0
     * @author Louis & Ilya
     */

    function generate_css($params=false, $class=false, $defaults=array()) {

        global $media_queries_list;

        if ($params===false) {
            $params = $this->param_array;
        }

        ob_start();

            if ($this->in_repeater_cycle()) return;

            $params["selector"] = $this->get_corrected_element_selector($params["selector"], $class);

            $margincss  = "";
            $spacing = isset($params['testimonial_image_spacing']) ? $params['testimonial_image_spacing'] : "";//$defaults['testimonial_image_spacing'];

            if (isset($params["testimonial_layout"]) && $params["testimonial_layout"]=='vertical') {
                
                $margincss = "margin:0;";
                $spacing = isset($params['testimonial_image_spacing']) ? $params['testimonial_image_spacing'] : $defaults['testimonial_image_spacing'];

                if (isset($params["testimonial_image_position"]) && $params["testimonial_image_position"]=='bottom') {
                    $margincss .= "margin-top";
                } 
                else {
                    $margincss .= "margin-bottom";
                }
            } else {
                if (isset($params["testimonial_image_position"]) && $params["testimonial_image_position"]=='bottom') {
                    $margincss = "margin:0;";
                    $spacing = isset($params['testimonial_image_spacing']) ? $params['testimonial_image_spacing'] : $defaults['testimonial_image_spacing'];
                    $margincss .= "margin-left";
                } 
                else {
                    $margincss .= "margin-right";
                }
            }

            if ($margincss&&$spacing) {
                $margincss .= ": ".$spacing . $this->get_css_unit('testimonial_image_spacing', $params, $defaults);
            }
            else {
                $margincss = "";
            }
            
            if(isset($params["testimonial_content_alignment"])) {
                if ($params["testimonial_content_alignment"]=='left') {
                    $flexalign = "flex-start";
                    $textalign = "left";
                } else if ($params["testimonial_content_alignment"]=='center') {
                    $flexalign = "center";
                    $textalign = "center";
                } else if ($params["testimonial_content_alignment"]=='right') {
                    $flexalign = "flex-end";
                    $textalign = "right";
                }
            }
            if (isset($params["testimonial_mobile_content_alignment"])) {
                if ($params["testimonial_mobile_content_alignment"]=='left') {
                    $mobileflexalign = "flex-start";
                    $mobiletextalign = "left";
                } else if ($params["testimonial_mobile_content_alignment"]=='center') {
                    $mobileflexalign = "center";
                    $mobiletextalign = "center";
                } else if ($params["testimonial_mobile_content_alignment"]=='right') {
                    $mobileflexalign = "flex-end";
                    $mobiletextalign = "right";
                }
            }
            if (isset($params["testimonial_layout"]) && $params["testimonial_layout"]=='vertical') { ?>
            <?php echo $params["selector"]; ?> {
                flex-direction: column;
            }
            <?php } 
            else if (isset($params["testimonial_layout"]) && $params["testimonial_layout"]=='horizontal') { ?>
            <?php echo $params["selector"]; ?> {
                flex-direction: row;
                align-items: center;
            }
            <?php }
            if (isset($params['testimonial_image_position']) && $params['testimonial_image_position'] == 'top' ) { ?>
            <?php echo $params["selector"]; ?> .oxy-testimonial-photo-wrap {
                order: 1;
            }
            <?php } 
            else if (isset($params['testimonial_image_position']) && $params['testimonial_image_position'] == 'bottom') { ?>
            <?php echo $params["selector"]; ?> .oxy-testimonial-photo-wrap {
                order: 3;
            }    
            <?php } ?>

            <?php if(isset($params['testimonial_image_size']) || $margincss!="" ) : ?>
            <?php echo $params["selector"]; ?> .oxy-testimonial-photo {
                <?php $this->output_single_css_property("width",
                    $params['testimonial_image_size'], $this->get_css_unit('testimonial_image_size', $params, $defaults)); ?>
                <?php $this->output_single_css_property("height",
                    $params['testimonial_image_size'], $this->get_css_unit('testimonial_image_size', $params, $defaults)); ?>
                <?php echo $margincss; ?>;
            }
            <?php endif; ?>

            <?php if(isset($flexalign)||isset($textalign)): ?>
            <?php echo $params["selector"]; ?> .oxy-testimonial-photo-wrap, 
            <?php echo $params["selector"]; ?> .oxy-testimonial-author-wrap, 
            <?php echo $params["selector"]; ?> .oxy-testimonial-content-wrap {
                <?php if(isset($flexalign)) $this->output_single_css_property("align-items", $flexalign); ?>
                <?php if(isset($textalign)) $this->output_single_css_property("text-align", $textalign); ?>
            }
            <?php endif; ?>

            <?php $css = ""; ?>
            <?php if (isset($params['testimonial_text_space_below'])) $css .= "margin-bottom:" . $params['testimonial_text_space_below'] . $this->get_css_unit('testimonial_text_space_below', $params, $defaults) . ";"; ?>
            <?php $css .= $this->typography_to_css($params, 'testimonial_text_typography', $defaults); ?>
            <?php if ($css != "") : ?>
            <?php echo $params["selector"]; ?> .oxy-testimonial-text {
                <?php echo $css; ?>
            }
            <?php endif; ?>

            <?php $css = "";
            if (isset($params['testimonial_author_space_below'])&&$params['testimonial_author_space_below']!="") 
                $css .= "margin-bottom:" . $params['testimonial_author_space_below'] . $this->get_css_unit('testimonial_author_space_below', $params, $defaults) . ";";
            $css .= $this->typography_to_css($params, 'testimonial_author_typography', $defaults); ?>
            <?php if ($css != "") : ?>
            <?php echo $params["selector"]; ?> .oxy-testimonial-author {
                <?php echo $css; ?>
            }
            <?php endif; ?>                

            <?php $css = ""; ?>
            <?php if (isset($params['testimonial_author_info_space_below'])&&$params['testimonial_author_info_space_below']!='')
                $css .= "margin-bottom:" . $params['testimonial_author_info_space_below'] . $this->get_css_unit('testimonial_author_info_space_below', $params, $defaults) . ";";
                $css .= $this->typography_to_css($params, 'testimonial_author_info_typography', $defaults); ?>
            <?php if ($css != "") : ?>
            <?php echo $params["selector"]; ?> .oxy-testimonial-author-info {
                <?php echo $css; ?>
            }
            <?php endif; ?>

            <?php 
            $max_size = false;
            
            if(isset($params['testimonial_vertical_layout_below']) && !empty(trim($params['testimonial_vertical_layout_below']))) {
                $max_size = $media_queries_list[$params['testimonial_vertical_layout_below']]['maxSize']; 
            }
            
            if ($max_size) { ?>
            @media (max-width: <?php echo $max_size; ?>) {
                <?php echo $params["selector"]; ?>{
                    flex-direction: column !important;
                }   

                <?php echo $params["selector"]; ?> .oxy-testimonial-photo {
                    margin: 0;
                    <?php 

                    if (isset($params["testimonial_layout"]) && $params["testimonial_image_position"]=='bottom') {
                        $margin_position = "top";
                    } 
                    else {
                        $margin_position = "bottom";
                    }

                    if (isset($params['testimonial_image_spacing'])) 
                        echo "margin-".$margin_position.":" . $params['testimonial_image_spacing'] . $this->get_css_unit('testimonial_image_spacing', $params, $defaults) . ";"; 
                    else 
                        echo "margin-".$margin_position.":" . $defaults['testimonial_image_spacing'] . $this->get_css_unit('testimonial_image_spacing', $params, $defaults)
                    ?>
                }

                <?php echo $params["selector"]; ?> .oxy-testimonial-photo-wrap, 
                <?php echo $params["selector"]; ?> .oxy-testimonial-author-wrap, 
                <?php echo $params["selector"]; ?> .oxy-testimonial-content-wrap {
                    <?php $this->output_single_css_property("align-items", $mobileflexalign); ?>
                    <?php $this->output_single_css_property("text-align", $mobiletextalign); ?>
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

    function shortcode($atts, $content = null, $name = null ) {

        if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
            return '';
        }

        $options = $this->set_options( $atts );

        $this->param_array[$options['id']] = $options;

	    $editable_testimonial_text = oxygen_base64_decode_for_json($this->param_array[$options['id']]['testimonial_text']);
	    $editable_testimonial_author = oxygen_base64_decode_for_json($this->param_array[$options['id']]['testimonial_author']);
	    $editable_testimonial_author_info = oxygen_base64_decode_for_json($this->param_array[$options['id']]['testimonial_author_info']);

	    if( class_exists( 'Oxygen_Gutenberg' ) ){
		    $editable_testimonial_text = Oxygen_Gutenberg::decorate_attribute( $options, $editable_testimonial_text, 'string', 'text' );
		    $editable_testimonial_author = Oxygen_Gutenberg::decorate_attribute( $options, $editable_testimonial_author, 'string', 'author' );
		    $editable_testimonial_author_info = Oxygen_Gutenberg::decorate_attribute( $options, $editable_testimonial_author_info, 'string', 'author_info' );
        }

        ob_start();

        if ($this->param_array[$options['id']]['testimonial_photo']) {
            $editable_testimonial_photo = $this->param_array[$options['id']]['testimonial_photo'];
	        if( class_exists( 'Oxygen_Gutenberg' ) ) $editable_testimonial_photo = Oxygen_Gutenberg::decorate_attribute( $options, $editable_testimonial_photo, 'image', 'photo' );
            ?>

            <div class='oxy-testimonial-photo-wrap'>
                <img id="<?php echo esc_attr($options['selector']); ?>_photo" src='<?php echo $editable_testimonial_photo; ?>' class='oxy-testimonial-photo oxygenberg-<?php echo esc_attr($options['selector']); ?>_photo' />
            </div>
        
        <?php }
        $photo_html = ob_get_clean();

        ob_start(); ?>

        <div id="<?php echo esc_attr($options['selector']); ?>" class='<?php echo esc_attr($options['classes']); ?> <?php echo "oxy-testimonial-".$this->param_array[$options['id']]['testimonial_layout']; ?>' <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>>

            <?php echo $photo_html; ?>

            <div class='oxy-testimonial-content-wrap'>
                <div id="<?php echo esc_attr($options['selector']); ?>_text" class='oxy-testimonial-text oxygenberg-<?php echo esc_attr($options['selector']); ?>_text'>
                    <?php echo $editable_testimonial_text; ?>
                </div>
                <div class='oxy-testimonial-author-wrap'>
                    <div id="<?php echo esc_attr($options['selector']); ?>_author" class='oxy-testimonial-author oxygenberg-<?php echo esc_attr($options['selector']); ?>_author'>
                        <?php echo $editable_testimonial_author; ?>
                    </div>
                    <?php if ($this->param_array[$options['id']]['testimonial_author_info']) { ?>
                        <div id="<?php echo esc_attr($options['selector']); ?>_author_info" class='oxy-testimonial-author-info oxygenberg-<?php echo esc_attr($options['selector']); ?>_author_info'>
                            <?php echo $editable_testimonial_author_info; ?>
                        </div>
                    <?php } ?>
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
            ng-show="isActiveName('<?php echo $this->options['tag']; ?>')&&!hasOpenTabs('oxy_testimonial')&&iframeScope.getOption('testimonial_author_info')==''">
            <div class="oxygen-control-wrapper">
                <div id="oxygen-add-another-row" class="oxygen-add-section-element"
                    ng-click="iframeScope.setOptionModel('testimonial_author_info','Jarvis Web Solutions Ltd.')">
                    <img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/typography.svg' />
                    <img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/typography.svg' />
                    <?php _e("Re-enable Deleted Text","oxygen"); ?>
                </div>
            </div>
        </div>

    <?php }
}

global $oxygen_vsb_components;
$oxygen_vsb_components['testimonial'] = new Oxygen_VSB_Testimonial( array(
            'name'  => __('Testimonial','oxygen'),
            'tag'   => 'oxy_testimonial',
            'tabs'  => array(
                'image' => array(
                    'heading' => __('Image','oxygen'),
                    'params' => array(
                        
                        array(
                            "type"          => "mediaurl",
                            "heading"       => __("Image URL", "oxygen"),
                            "param_name"    => "testimonial_photo",
                            "value"         => "http://via.placeholder.com/125x125",
                            "css"           => false
                        ),
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Image Size", "oxygen"),
                            "param_name"    => "testimonial_image_size",
                            "value"         => "125",
                            "min"           => "50",
                            "max"           => "300",
                            "css"           => false
                        ),
                        array(
                            "param_name"    => "testimonial_image_size-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Image Spacing", "oxygen"),
                            "param_name"    => "testimonial_image_spacing",
                            "value"         => "20",
                            "css"           => false
                        ),
                        array(
                            "param_name"    => "testimonial_image_spacing-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),
                        array(
                            "type"          => "radio",
                            "heading"       => __("Image Position", "oxygen"),
                            "param_name"    => "testimonial_image_position",
                            "value"         => array(
                                                'top'           => __("before", "oxygen"),
                                                'bottom'        => __("after", "oxygen"),
                                            ),
                            "css"           => false,
                        ),

                    ),
                ),

                'layout' => array(
                    'heading' => __('Layout','oxygen'),
                    'params' => array(
                    
                         array(
                            "type"          => "radio",
                            "heading"       => __("Layout", "oxygen"),
                            "param_name"    => "testimonial_layout",
                            "value"         => array(
                                                 'horizontal' => __("horizontal", "oxygen"),
                                                 'vertical'   => __("vertical", "oxygen"),
                                            ),
                            "css"           => false,
                        ),
                        array(
                            "type"          => "medialist",
                            "heading"       => __("Vertical Layout Below","oxygen"),
                            "value"         => "",
                            "param_name"    => "testimonial_vertical_layout_below",
                            "css"           => false
                        ),
                        array(
                            "type"          => "radio",
                            "heading"       => __("Content Alignment", "oxygen"),
                            "param_name"    => "testimonial_content_alignment",
                            "value"         => array(
                                                 'left'     => __("left", "oxygen"),
                                                 'center'   => __("center", "oxygen"),
                                                 'right'    => __("right", "oxygen"),
                                            ),
                            "css"           => false,
                        ),
                        array(
                            "type"          => "radio",
                            "heading"       => __("Mobile Content Alignment", "oxygen"),
                            "param_name"    => "testimonial_mobile_content_alignment",
                            "value"         => array(
                                                 'left'     => __("left", "oxygen"),
                                                 'center'   => __("center", "oxygen"),
                                                 'right'    => __("right", "oxygen"),
                                            ),
                            "css"           => false,
                        ),
                    )
                ),

                'typography' => array(
                    'heading' => __('Typography','oxygen'),

                    'tabs' => array(
                        'testimonial_text' => array(
                            'heading' => __("Text","oxygen"),
                            'params' => array(
                                array(
                                    "type"          => "typography",
                                    "param_name"    => "testimonial_text_typography",
                                    "param_values"  => array(
                                                        'font-family'           => '',
                                                        'font-size'             => '21',
                                                        'font-size-unit'        => 'px',
                                                        'color'                 => '',
                                                        'font-weight'           => '',
                                                        'line-height'           => '1.4',
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
                        'testimonial_author' => array(
                            'heading' => __("Author","oxygen"),
                            'params' => array(
                                array(
                                    "type"          => "typography",
                                    "param_name"    => "testimonial_author_typography",
                                    "param_values"  => array(
                                                        'font-family'           => '',
                                                        'font-size'             => '18',
                                                        'font-size-unit'        => 'px',
                                                        'color'                 => '',
                                                        'font-weight'           => '',
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
                        'testimonial_author_info' => array(
                            'heading' => __("Author Info","oxygen"),
                            'params' => array(
                                array(
                                    "type"          => "typography",
                                    "param_name"    => "testimonial_author_info_typography",
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
                                                        '-webkit-font-smoothing'=> 'subpixel-antialiased'
                                                    ),
                                    "css"           => false,
                                ),
                            )
                        ),
                        
                    ),
                    'params' => array(
                    ),
                ),

                'spacing' => array(
                    'heading' => __('Spacing','oxygen'),
                    'params' => array(
                        
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Text Space Below", "oxygen"),
                            "param_name"    => "testimonial_text_space_below",
                            "value"         => "8",
                            "css"           => false,
                        ),
                        array(
                            "param_name"    => "testimonial_text_space_below-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),

                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Author Space Below", "oxygen"),
                            "param_name"    => "testimonial_author_space_below",
                            "value"         => "",
                            "css"           => false,
                        ),
                        array(
                            "param_name"    => "testimonial_author_space_below-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),

                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Author Info Space Below", "oxygen"),
                            "param_name"    => "testimonial_author_info_space_below",
                            "value"         => "",
                            "css"           => false,
                        ),
                        array(
                            "param_name"    => "testimonial_author_info_space_below-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),
                    )
                )
            ),
            'params' => array(
               // nothing so far
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
                            "testimonial_text" => '"This is your testimonial text. Keep it short, simple, and to the point. This is a pretty good length."',
                            "testimonial_author" => 'Jane Doe',
                            "testimonial_author_info" => 'Jarvis Web Solutions Ltd.',
                            )
                    ),
            ),
            'not_css_params' => array(
                'testimonial_text',
                'testimonial_author',
                'testimonial_author_info',
            )
            
        ));
