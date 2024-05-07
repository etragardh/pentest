<?php

/**
 * Comment Form Component Class
 * 
 * @since 2.0
 */

class Oxygen_VSB_Comment_Form extends CT_Component {

    public $param_array;
    public $css_util;
    public $query;

    function __construct($options) {

        // run initialization
        $this->init( $options );

        $this->register_properties();

        // Add shortcodes
        add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
        add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

        // change component button place
        remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
        add_action("oxy_folder_wordpress_components", array( $this, "component_button" ) );

        // output styles
        add_filter("ct_footer_styles", array( $this, "css" ) );

        // add specific options to Basic Styles tab
        add_action("ct_toolbar_component_settings", array( $this, "settings") );

        // render preveiew with AJAX
        add_filter("template_include", array( $this, "single_template"), 100 );
        
    }

    
    /**
     * Add a [oxy_comment_form] shortcode to WordPress
     *
     * @since 2.0
     * @author Louis & Ilya
     */

    function add_shortcode( $atts, $content, $name ) {

        if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
            return '';
        }
        
        $options = $this->set_options( $atts );
        // this is only needed in the builder
        if( false === $options ) {
            $options = ['selector' => ''];
        }

        $this->param_array = shortcode_atts(
            array(
                'form_field_border_color' => "",
                'form_field_text_color' => "",
                'field_border_radius' => "",
                'field_border_radius-unit' => "px",
                'submit_button_background_color' => "",
                'submit_button_text_color' => "",
            ), $options, $this->options['tag'] );

        $this->param_array["selector"] = esc_attr($options['selector']);

        // make sure errors are shown
        $error_reporting = error_reporting(E_ERROR | E_WARNING | E_PARSE);
        $display_errors = ini_get('display_errors');
        ini_set('display_errors', 1); 

        ob_start(); ?>

        <?php if (!isset($atts['preview']) || !$atts['preview']) : ?>
        <div id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>>
        <?php endif; ?>
        <?php if (!oxygen_is_setting_default_data()) : ?>
        <?php comment_form(); ?>
        <?php endif; ?>
        <?php if (!isset($atts['preview']) || !$atts['preview']) : ?>
        </div>
        <?php endif; ?>

        <?php
        // set errors params back
        ini_set('display_errors', $display_errors); 
        error_reporting($error_reporting);

        return ob_get_clean();
    }


    /**
     * Map parameters to CSS properties
     *
     * @since 2.0
     * @author Louis
     */

    function register_properties() {

        $this->cssutil = new Oxygen_VSB_CSS_Util;

        $this->cssutil->register_selector('input, textarea');
        $this->cssutil->register_selector('#submit');

        $this->cssutil->map_property('form_field_border_color', 'border-color',     'input, textarea');
        $this->cssutil->map_property('form_field_text_color', 'color',              'input, textarea');
        $this->cssutil->map_property('field_border_radius', 'border-radius',        'input, textarea');

        $this->cssutil->map_property('submit_button_background_color', 'background-color',  '#submit');
        $this->cssutil->map_property('submit_button_text_color', 'color',                   '#submit');
    }


    /**
     * Output CSS based on user params
     *
     * @since 2.0
     * @author Louis
     */

    function css() {

        if(is_array($this->param_array)) {
            echo $this->cssutil->generate_css($this->param_array);
        }
    }


    /**
     * Basic Styles settings
     *
     * @since 2.0
     * @author Ilya K.
     */

    function settings() { 

        global $oxygen_toolbar; ?>

        <div class="oxygen-sidebar-flex-panel"
            ng-if="isActiveName('oxy_comment_form')">

            <div class="oxygen-settings-section-heading">
                <?php _e("Form Field", "oxygen"); ?>
            </div>

            <div class="oxygen-control-row">
                <?php $oxygen_toolbar->colorpicker_with_wrapper("form_field_border_color", __("Border Color", "oxygen") ); ?>
            </div>

            <div class="oxygen-control-row">
                <?php $oxygen_toolbar->colorpicker_with_wrapper("form_field_text_color", __("Text Color", "oxygen") ); ?>
            </div>

            <div class='oxygen-control-row'>
                <?php $oxygen_toolbar->measure_box_with_wrapper('field_border_radius',__('Border Radius','oxygen'), "px"); ?>
            </div>
            
            <div class="oxygen-settings-section-heading">
                <?php _e("Submit Button", "oxygen"); ?>
            </div>

            <div class="oxygen-control-row">
                <?php $oxygen_toolbar->colorpicker_with_wrapper("submit_button_background_color", __("Background Color", "oxygen") ); ?>
            </div>

            <div class="oxygen-control-row">
                <?php $oxygen_toolbar->colorpicker_with_wrapper("submit_button_text_color", __("Text Color", "oxygen") ); ?>
            </div>

        </div>

    <?php }


    /**
     * This function hijacks the template to return special template that renders the code results
     * for the [oxy_comment_form] element to load the content into the builder for preview
     * 
     * @since 0.4.0
     * @author gagan goraya
     */
    
    function single_template( $template ) {

        $new_template = '';

        if( isset($_REQUEST['action']) && stripslashes($_REQUEST['action']) == 'oxy_render_comment_form') {
            
            if ( file_exists(dirname(dirname( __FILE__)) . '/layouts/' . 'comment-form.php') ) {
                $new_template = dirname(dirname( __FILE__)) . '/layouts/' . 'comment-form.php';
            }
        }

        if ( '' != $new_template ) {
            return $new_template ;
        }

        return $template;
    }

}

// Create component instance
global $oxygen_vsb_components;
$oxygen_vsb_components['comment_form'] = new Oxygen_VSB_Comment_Form( array(
            'name'  => __('Comment Form','oxygen'),
            'tag'   => 'oxy_comment_form',
            'advanced'  => array(
                "positioning" => array(
                        "values"    => array (
                            'width'      => '100',
                            'width-unit' => '%',
                            )
                    ),
                "other" => array(
                    "values" => array(
                        'form_field_border_color' => "",
                        'form_field_text_color' => "",
                        'field_border_radius' => "",
                        'field_border_radius-unit' => "px",
                        'submit_button_background_color' => "",
                        'submit_button_text_color' => "",
                    )
                )
            ),
            'not_css_params' => array(
                        'form_field_border_color',
                        'form_field_text_color',
                        'field_border_radius',
                        'field_border_radius-unit',
                        'submit_button_background_color',
                        'submit_button_text_color',
            )
        ));