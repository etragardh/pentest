<?php

/**
 * Dynamic list 
 * 
 * @since 2.1
 */

class Oxygen_VSB_Dynamic_List extends CT_Component {

    public $param_array = array();
    public $css_util;
    public $query;
    public $action_name = "oxy_get_dynamic_data_query";
    public $template_file = "dynamic-list.php"; 
    public $flag_cache_repeaterid_fix;
    public $repeater_css_cache_generated = [];

    function __construct($options) {

        global $is_repeater_child;
        $is_repeater_child = 0;

        // run initialization
        $this->init( $options );

        // Add shortcodes
        add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
        add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

        for ( $i = 2; $i <= 16; $i++ ) {
            add_shortcode( $this->options['tag'] . "_" . $i, array( $this, 'add_shortcode' ) );
        }

        // change component button place
        remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
        add_action("oxygen_helpers_components_dynamic", array( $this, "component_button" ) );

        // output styles
        // add_filter("ct_footer_styles", array( $this, "template_css" ) );
         add_filter("ct_footer_styles", array( $this, "params_css" ) );

        //add specific options to Basic Styles tab
        add_action("ct_toolbar_component_settings", array( $this, "settings"), 11 );
        
        // output list of templates
        // add_action("ct_builder_ng_init", array( $this, "templates_list") );

        // render preveiew with AJAX
         add_filter("template_include", array( $this, "single_template"), 100 );
        
        // admin notice to regenerate cache, can be taken out later down the line
        $this->flag_cache_repeaterid_fix = get_option( 'flag_cache_repeaterid_fix');
        if( empty( $this->flag_cache_repeaterid_fix ) && oxygen_vsb_is_touched_install() ) {
            // Get the list of posts whose CSS cache have been automatically re-generated
            $this->repeater_css_cache_generated = get_option( 'repeater_css_cache_generated', []);
            add_action("admin_notices", array( $this, "css_cache_message"));
        }
    }

    function css_cache_message() {
        $plugin_data = get_plugin_data( CT_PLUGIN_MAIN_FILE );
        ?>
        <div class="notice notice-warning">
            <p><?php echo sprintf( __( 'Oxygen %s changes the way Repeater elements are styled. You must regenerate your Oxygen CSS Cache for Repeater elements to be rendered correctly.', 'oxygen' ), $plugin_data['Version'] ); ?></p>
            <p><a href="<?php echo admin_url('admin.php?page=oxygen_vsb_settings&tab=cache&start_cache_generation=true');?>"><?php _e( 'Please click here to regenerate the CSS cache.');?></a></p>
        </div>
        <?php
    }

    /**
     * Map parameters to CSS properties
     *
     * @since 2.0
     * @author Louis
     */

    function register_properties($id) {

        $this->cssutil[$id] = new Oxygen_VSB_CSS_Util;

        $this->cssutil[$id]->register_selector('.oxy-repeater-pages');
        $this->cssutil[$id]->register_selector('.oxy-repeater-pages a.page-numbers');
        $this->cssutil[$id]->register_selector('.oxy-repeater-pages a.page-numbers:hover');
        
        // pagination general
        $this->cssutil[$id]->map_property('paginate_alignment', 'justify-content',                '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_wrap_alignment', 'justify-content',                '.oxy-repeater-pages-wrap');
        $this->cssutil[$id]->map_property('paginate_size', 'font-size',                '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_color', 'color',                         '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_link_color', 'color',                    '.oxy-repeater-pages a.page-numbers');
        $this->cssutil[$id]->map_property('paginate_link_hover_color', 'color',              '.oxy-repeater-pages a.page-numbers:hover');

        // pagination container padding and margin
        $this->cssutil[$id]->map_property('paginate_padding_top', 'padding-top',                '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_padding_left', 'padding-left',                '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_padding_right', 'padding-right',                         '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_padding_bottom', 'padding-bottom',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_margin_top', 'margin-top',                '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_margin_left', 'margin-left',                '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_margin_right', 'margin-right',                         '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_margin_bottom', 'margin-bottom',                    '.oxy-repeater-pages');
        // pagination container size
        $this->cssutil[$id]->map_property('paginate_width', 'width',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_min_width', 'min-width',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_max_width', 'max-width',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_height', 'height',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_min_height', 'min-height',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_max_height', 'max-height',                    '.oxy-repeater-pages');

        // pagination child elements layout
        $this->cssutil[$id]->map_property('paginate_flex_direction', 'flex-direction',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_flex_wrap', 'flex-wrap',                    '.oxy-repeater-pages');
    
        // pagination borders
        $this->cssutil[$id]->map_property('paginate_border_all_color', 'border-color',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_top_color', 'border-top-color',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_left_color', 'border-left-color',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_bottom_color', 'border-bottom-color',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_right_color', 'border-right-color',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_all_width', 'border-width',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_top_width', 'border-top-width',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_left_width', 'border-left-width',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_bottom_width', 'border-bottom-width',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_right_width', 'border-right-width',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_all_style', 'border-style',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_top_style', 'border-top-style',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_left_style', 'border-left-style',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_bottom_style', 'border-bottom-style',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_right_style', 'border-right-style',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_radius', 'border-radius',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_top_left_radius', 'border-top-left-radius',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_top_right_radius', 'border-top-right-radius',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_bottom_right_radius', 'border-bottom-right-radius',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property('paginate_border_bottom_left_radius', 'border-bottom-left-radius',                    '.oxy-repeater-pages');
        
        // pagination background
        $this->cssutil[$id]->map_property("paginate_background_color", 'background-color',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property("paginate_background_image", 'background-image',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property("paginate_background_size", 'background-size',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property("paginate_background_size_width", 'background-size-width',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property("paginate_background_size_height", 'background-size-height',                    '.oxy-repeater-pages');
        $this->cssutil[$id]->map_property("paginate_background_repeat", 'background-repeat',                    '.oxy-repeater-pages');
        // pagination link background
        $this->cssutil[$id]->map_property("paginatelink_background_color", 'background-color',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property("paginatelink_background_image", 'background-image',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property("paginatelink_background_size", 'background-size',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property("paginatelink_background_size_width", 'background-size-width',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property("paginatelink_background_size_height", 'background-size-height',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property("paginatelink_background_repeat", 'background-repeat',                    '.oxy-repeater-pages a');

        // pagination Active link background
        $this->cssutil[$id]->map_property("paginatelinkactive_background_color", 'background-color',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property("paginatelinkactive_background_image", 'background-image',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property("paginatelinkactive_background_size", 'background-size',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property("paginatelinkactive_background_size_width", 'background-size-width',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property("paginatelinkactive_background_size_height", 'background-size-height',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property("paginatelinkactive_background_repeat", 'background-repeat',                    '.oxy-repeater-pages span.current');

        // pagination links size and spacing
        $this->cssutil[$id]->map_property('paginatelink_padding_top', 'padding-top',                '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_padding_left', 'padding-left',                '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_padding_right', 'padding-right',                         '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_padding_bottom', 'padding-bottom',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_margin_top', 'margin-top',                '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_margin_left', 'margin-left',                '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_margin_right', 'margin-right',                         '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_margin_bottom', 'margin-bottom',                    '.oxy-repeater-pages a');

        $this->cssutil[$id]->map_property('paginatelink_width', 'width',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_min_width', 'min-width',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_max_width', 'max-width',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_height', 'height',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_min_height', 'min-height',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_max_height', 'max-height',                    '.oxy-repeater-pages a');

        // pagination Active links size and spacing
        $this->cssutil[$id]->map_property('paginatelinkactive_padding_top', 'padding-top',                '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_padding_left', 'padding-left',                '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_padding_right', 'padding-right',                         '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_padding_bottom', 'padding-bottom',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_margin_top', 'margin-top',                '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_margin_left', 'margin-left',                '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_margin_right', 'margin-right',                         '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_margin_bottom', 'margin-bottom',                    '.oxy-repeater-pages span.current');

        $this->cssutil[$id]->map_property('paginatelinkactive_width', 'width',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_min_width', 'min-width',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_max_width', 'max-width',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_height', 'height',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_min_height', 'min-height',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_max_height', 'max-height',                    '.oxy-repeater-pages span.current');

        // pagination link borders
        $this->cssutil[$id]->map_property('paginatelink_border_all_color', 'border-color',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_top_color', 'border-top-color',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_left_color', 'border-left-color',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_bottom_color', 'border-bottom-color',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_right_color', 'border-right-color',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_all_width', 'border-width',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_top_width', 'border-top-width',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_left_width', 'border-left-width',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_bottom_width', 'border-bottom-width',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_right_width', 'border-right-width',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_all_style', 'border-style',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_top_style', 'border-top-style',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_left_style', 'border-left-style',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_bottom_style', 'border-bottom-style',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_right_style', 'border-right-style',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_radius', 'border-radius',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_top_left_radius', 'border-top-left-radius',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_top_right_radius', 'border-top-right-radius',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_bottom_right_radius', 'border-bottom-right-radius',                    '.oxy-repeater-pages a');
        $this->cssutil[$id]->map_property('paginatelink_border_bottom_left_radius', 'border-bottom-left-radius',                    '.oxy-repeater-pages a');
        // pagination active link borders
        $this->cssutil[$id]->map_property('paginatelinkactive_border_all_color', 'border-color',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_top_color', 'border-top-color',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_left_color', 'border-left-color',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_bottom_color', 'border-bottom-color',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_right_color', 'border-right-color',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_all_width', 'border-width',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_top_width', 'border-top-width',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_left_width', 'border-left-width',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_bottom_width', 'border-bottom-width',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_right_width', 'border-right-width',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_all_style', 'border-style',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_top_style', 'border-top-style',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_left_style', 'border-left-style',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_bottom_style', 'border-bottom-style',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_right_style', 'border-right-style',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_radius', 'border-radius',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_top_left_radius', 'border-top-left-radius',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_top_right_radius', 'border-top-right-radius',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_bottom_right_radius', 'border-bottom-right-radius',                    '.oxy-repeater-pages span.current');
        $this->cssutil[$id]->map_property('paginatelinkactive_border_bottom_left_radius', 'border-bottom-left-radius',                    '.oxy-repeater-pages span.current');

        // pagination link hover background
        $this->cssutil[$id]->map_property("paginatelinkhover_background_color", 'background-color',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property("paginatelinkhover_background_image", 'background-image',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property("paginatelinkhover_background_size", 'background-size',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property("paginatelinkhover_background_size_width", 'background-size-width',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property("paginatelinkhover_background_size_height", 'background-size-height',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property("paginatelinkhover_background_repeat", 'background-repeat',                    '.oxy-repeater-pages a:hover');

        // pagination Active link hover background
        $this->cssutil[$id]->map_property("paginatelinkactivehover_background_color", 'background-color',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property("paginatelinkactivehover_background_image", 'background-image',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property("paginatelinkactivehover_background_size", 'background-size',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property("paginatelinkactivehover_background_size_width", 'background-size-width',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property("paginatelinkactivehover_background_size_height", 'background-size-height',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property("paginatelinkactivehover_background_repeat", 'background-repeat',                    '.oxy-repeater-pages span.current:hover');

        // pagination links hover size and spacing
        $this->cssutil[$id]->map_property('paginatelinkhover_padding_top', 'padding-top',                '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_padding_left', 'padding-left',                '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_padding_right', 'padding-right',                         '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_padding_bottom', 'padding-bottom',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_margin_top', 'margin-top',                '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_margin_left', 'margin-left',                '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_margin_right', 'margin-right',                         '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_margin_bottom', 'margin-bottom',                    '.oxy-repeater-pages a:hover');

        $this->cssutil[$id]->map_property('paginatelinkhover_width', 'width',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_min_width', 'min-width',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_max_width', 'max-width',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_height', 'height',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_min_height', 'min-height',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_max_height', 'max-height',                    '.oxy-repeater-pages a:hover');

        // pagination Active links hover size and spacing
        $this->cssutil[$id]->map_property('paginatelinkactivehover_padding_top', 'padding-top',                '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_padding_left', 'padding-left',                '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_padding_right', 'padding-right',                         '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_padding_bottom', 'padding-bottom',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_margin_top', 'margin-top',                '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_margin_left', 'margin-left',                '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_margin_right', 'margin-right',                         '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_margin_bottom', 'margin-bottom',                    '.oxy-repeater-pages span.current:hover');

        $this->cssutil[$id]->map_property('paginatelinkactivehover_width', 'width',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_min_width', 'min-width',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_max_width', 'max-width',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_height', 'height',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_min_height', 'min-height',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_max_height', 'max-height',                    '.oxy-repeater-pages span.current:hover');

        // pagination link hover borders
        $this->cssutil[$id]->map_property('paginatelinkhover_border_all_color', 'border-color',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_top_color', 'border-top-color',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_left_color', 'border-left-color',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_bottom_color', 'border-bottom-color',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_right_color', 'border-right-color',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_all_width', 'border-width',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_top_width', 'border-top-width',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_left_width', 'border-left-width',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_bottom_width', 'border-bottom-width',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_right_width', 'border-right-width',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_all_style', 'border-style',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_top_style', 'border-top-style',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_left_style', 'border-left-style',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_bottom_style', 'border-bottom-style',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_right_style', 'border-right-style',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_radius', 'border-radius',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_top_left_radius', 'border-top-left-radius',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_top_right_radius', 'border-top-right-radius',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_bottom_right_radius', 'border-bottom-right-radius',                    '.oxy-repeater-pages a:hover');
        $this->cssutil[$id]->map_property('paginatelinkhover_border_bottom_left_radius', 'border-bottom-left-radius',                    '.oxy-repeater-pages a:hover');
        // pagination active link hover borders
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_all_color', 'border-color',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_top_color', 'border-top-color',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_left_color', 'border-left-color',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_bottom_color', 'border-bottom-color',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_right_color', 'border-right-color',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_all_width', 'border-width',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_top_width', 'border-top-width',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_left_width', 'border-left-width',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_bottom_width', 'border-bottom-width',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_right_width', 'border-right-width',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_all_style', 'border-style',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_top_style', 'border-top-style',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_left_style', 'border-left-style',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_bottom_style', 'border-bottom-style',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_right_style', 'border-right-style',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_radius', 'border-radius',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_top_left_radius', 'border-top-left-radius',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_top_right_radius', 'border-top-right-radius',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_bottom_right_radius', 'border-bottom-right-radius',                    '.oxy-repeater-pages span.current:hover');
        $this->cssutil[$id]->map_property('paginatelinkactivehover_border_bottom_left_radius', 'border-bottom-left-radius',                    '.oxy-repeater-pages span.current:hover');

        // pagination link transition
        $this->cssutil[$id]->map_property('paginate_link_transition', 'transition',                    '.oxy-repeater-pages > *');
    }

    /**
     * Output CSS based on user params
     *
     * @since 2.0
     * @author Louis
     */

    function params_css() {

        if (!is_array($this->param_array)||empty($this->param_array)) {
            return;
        }

        foreach ($this->param_array as $id => $params) {

            echo $this->cssutil[$id]->generate_css($params, $id);
        }
    }

    /**
     * Encode/Decode special characters to HTML entities in codeblock script
     *
     * @since 4.0.1
     * @author Nico S.
     */

    function script_to_html_entities($content, $is_encode) {
        $scriptPos1 = 0;
        $scriptPos2 = 0;

        while (strpos($content, '<script>', $scriptPos1)!== false) {
            $prePos1 = $scriptPos1;
            $prePos2 = $scriptPos2;

            $scriptPos1 = strpos($content, '<script>', $scriptPos1);
            $scriptPos2 = strpos($content, '</script>', $scriptPos2);

            $content = str_replace(
                substr($content, $scriptPos1 + 8, $scriptPos2 - $scriptPos1 - 8),
                $is_encode? 
                    htmlspecialchars(substr($content, $scriptPos1 + 8, $scriptPos2 - $scriptPos1 - 8)):
                    htmlspecialchars_decode(substr($content, $scriptPos1 + 8, $scriptPos2 - $scriptPos1 - 8)), 
                $content
            );

            $scriptPos1 = strpos($content, '<script>', $prePos1) + strlen('<script>');
            $scriptPos2 = strpos($content, '</script>', $prePos2) + strlen('</script>');
        }

        return $content;
    }

    /**
     * Add a [oxy_dynamic_list] shortcode to WordPress
     *
     * @since 2.1
     * @author Gagan S Goraya
     */

    function add_shortcode( $atts, $content, $name ) {
        
        // Re-generate CSS cache for the post containing this repeater
        // if CSS cache was not re-generated after updating to Oxygen 4.0
        if ( isset($GLOBALS['post']) ) {
            $post_id=intval( $GLOBALS['post']->ID );
            if(empty($this->flag_cache_repeaterid_fix) && !in_array($post_id,$this->repeater_css_cache_generated)){
                // add this post to the list so this is not processed
                // again during this or future requests
                $this->repeater_css_cache_generated[] = $post_id;
                update_option( 'repeater_css_cache_generated', $this->repeater_css_cache_generated);
                // Perform the actual CSS cache generation after setting the flags
                // to avoid enless loops (css cache generation will render shortcodes again)
                $result = oxygen_vsb_cache_page_css( $post_id );
            }
        }

        $options = $this->set_options( $atts );
        
        $query = $this->setQuery($options);
        
        $this->register_properties($options['id']);

        if (!is_array($this->param_array)) {
            $this->param_array = array();
        }

        $this->param_array[$options['id']] = shortcode_atts(
            array(
                "paginate_color" => '#00aa00',
                "paginate_size" => '12',
                "paginate_size_unit" => 'px',
                "paginate_alignment" => 'center',
                "paginate_wrap_alignment" => 'flex-start',
                "paginate_link_color" => 'blue',
                "paginate_link_hover_color" => 'orange',

                // paginate size and spacing
                "paginate_padding_left" => '',
                "paginate_padding_left_unit" => 'px',
                "paginate_padding_right" => '',
                "paginate_padding_right_unit" => 'px',
                "paginate_padding_top" => '',
                "paginate_padding_top_unit" => 'px',
                "paginate_padding_bottom" => '',
                "paginate_padding_bottom_unit" => 'px',
                
                "paginate_margin_left" => '',
                "paginate_margin_left_unit" => 'px',
                "paginate_margin_right" => '',
                "paginate_margin_right_unit" => 'px',
                "paginate_margin_top" => '',
                "paginate_margin_top_unit" => 'px',
                "paginate_margin_bottom" => '',
                "paginate_margin_bottom_unit" => 'px',

                "paginate_width" => '',
                "paginate_width_unit" => 'px',
                "paginate_max_width" => '',
                "paginate_max_width_unit" => 'px',
                "paginate_min_width" => '',
                "paginate_min_width_unit" => 'px',

                "paginate_height" => '',
                "paginate_height_unit" => 'px',
                "paginate_max_height" => '',
                "paginate_max_height_unit" => 'px',
                "paginate_min_height" => '',
                "paginate_min_height_unit" => 'px',
                // paginate child layout
                "paginate_flex_direction" => '',
                "paginate_flex_wrap" => '',
                // paginate borders
                "paginate_border_all_color" => '',
                "paginate_border_top_color" => '',
                "paginate_border_left_color" => '',
                "paginate_border_bottom_color" => '',
                "paginate_border_right_color" => '',
                "paginate_border_all_width" => '',
                "paginate_border_all_width_unit" => 'px',
                "paginate_border_top_width" => '',
                "paginate_border_top_width_unit" => 'px',
                "paginate_border_left_width" => '',
                "paginate_border_left_width_unit" => 'px',
                "paginate_border_bottom_width" => '',
                "paginate_border_bottom_width_unit" => 'px',
                "paginate_border_right_width" => '',
                "paginate_border_right_width_unit" => 'px',
                "paginate_border_all_style" => '',
                "paginate_border_top_style" => '',
                "paginate_border_left_style" => '',
                "paginate_border_bottom_style" => '',
                "paginate_border_right_style" => '',
                "paginate_border_radius" => '',
                "paginate_border_radius_unit" => 'px',
                "paginate_border_top_left_radius" => '',
                "paginate_border_top_left_radius_unit" => 'px',
                "paginate_border_top_right_radius" => '',
                "paginate_border_top_right_radius_unit" => 'px',
                "paginate_border_bottom_right_radius" => '',
                "paginate_border_bottom_right_radius_unit" => 'px',
                "paginate_border_bottom_left_radius" => '',
                "paginate_border_bottom_left_radius_unit" => 'px',
                // paginate Link borders
                "paginatelink_border_all_color" => '',
                "paginatelink_border_top_color" => '',
                "paginatelink_border_left_color" => '',
                "paginatelink_border_bottom_color" => '',
                "paginatelink_border_right_color" => '',
                "paginatelink_border_all_width" => '',
                "paginatelink_border_all_width_unit" => 'px',
                "paginatelink_border_top_width" => '',
                "paginatelink_border_top_width_unit" => 'px',
                "paginatelink_border_left_width" => '',
                "paginatelink_border_left_width_unit" => 'px',
                "paginatelink_border_bottom_width" => '',
                "paginatelink_border_bottom_width_unit" => 'px',
                "paginatelink_border_right_width" => '',
                "paginatelink_border_right_width_unit" => 'px',
                "paginatelink_border_all_style" => '',
                "paginatelink_border_top_style" => '',
                "paginatelink_border_left_style" => '',
                "paginatelink_border_bottom_style" => '',
                "paginatelink_border_right_style" => '',
                "paginatelink_border_radius" => '',
                "paginatelink_border_radius_unit" => 'px',
                "paginatelink_border_top_left_radius" => '',
                "paginatelink_border_top_left_radius_unit" => 'px',
                "paginatelink_border_top_right_radius" => '',
                "paginatelink_border_top_right_radius_unit" => 'px',
                "paginatelink_border_bottom_right_radius" => '',
                "paginatelink_border_bottom_right_radius_unit" => 'px',
                "paginatelink_border_bottom_left_radius" => '',
                "paginatelink_border_bottom_left_radius_unit" => 'px',
                // paginate Active Link borders
                "paginatelinkactive_border_all_color" => '',
                "paginatelinkactive_border_top_color" => '',
                "paginatelinkactive_border_left_color" => '',
                "paginatelinkactive_border_bottom_color" => '',
                "paginatelinkactive_border_right_color" => '',
                "paginatelinkactive_border_all_width" => '',
                "paginatelinkactive_border_all_width_unit" => 'px',
                "paginatelinkactive_border_top_width" => '',
                "paginatelinkactive_border_top_width_unit" => 'px',
                "paginatelinkactive_border_left_width" => '',
                "paginatelinkactive_border_left_width_unit" => 'px',
                "paginatelinkactive_border_bottom_width" => '',
                "paginatelinkactive_border_bottom_width_unit" => 'px',
                "paginatelinkactive_border_right_width" => '',
                "paginatelinkactive_border_right_width_unit" => 'px',
                "paginatelinkactive_border_all_style" => '',
                "paginatelinkactive_border_top_style" => '',
                "paginatelinkactive_border_left_style" => '',
                "paginatelinkactive_border_bottom_style" => '',
                "paginatelinkactive_border_right_style" => '',
                "paginatelinkactive_border_radius" => '',
                "paginatelinkactive_border_radius_unit" => 'px',
                "paginatelinkactive_border_top_left_radius" => '',
                "paginatelinkactive_border_top_left_radius_unit" => 'px',
                "paginatelinkactive_border_top_right_radius" => '',
                "paginatelinkactive_border_top_right_radius_unit" => 'px',
                "paginatelinkactive_border_bottom_right_radius" => '',
                "paginatelinkactive_border_bottom_right_radius_unit" => 'px',
                "paginatelinkactive_border_bottom_left_radius" => '',
                "paginatelinkactive_border_bottom_left_radius_unit" => 'px',
                // paginate background
                "paginate_background_color" => '',
                "paginate_background_image" => '',
                "paginate_background_size" => '',
                "paginate_background_size_width" => '',
                "paginate_background_size_width_unit" => 'px',
                "paginate_background_size_height" => '',
                "paginate_background_size_height_unit" => 'px',
                "paginate_background_repeat" => '',

                // pagination link background
                "paginatelink_background_color" => '',
                "paginatelink_background_image" => '',
                "paginatelink_background_size" => '',
                "paginatelink_background_size_width" => '',
                "paginatelink_background_size_width_unit" => 'px',
                "paginatelink_background_size_height" => '',
                "paginatelink_background_size_height_unit" => 'px',
                "paginatelink_background_repeat" => '',

                // pagination active link background
                "paginatelinkactive_background_color" => '',
                "paginatelinkactive_background_image" => '',
                "paginatelinkactive_background_size" => '',
                "paginatelinkactive_background_size_width" => '',
                "paginatelinkactive_background_size_width_unit" => 'px',
                "paginatelinkactive_background_size_height" => '',
                "paginatelinkactive_background_size_height_unit" => 'px',
                "paginatelinkactive_background_repeat" => '',
                // paginate link size and spacing
                "paginatelink_padding_left" => '',
                "paginatelink_padding_left_unit" => 'px',
                "paginatelink_padding_right" => '',
                "paginatelink_padding_right_unit" => 'px',
                "paginatelink_padding_top" => '',
                "paginatelink_padding_top_unit" => 'px',
                "paginatelink_padding_bottom" => '',
                "paginatelink_padding_bottom_unit" => 'px',
                
                "paginatelink_margin_left" => '',
                "paginatelink_margin_left_unit" => 'px',
                "paginatelink_margin_right" => '',
                "paginatelink_margin_right_unit" => 'px',
                "paginatelink_margin_top" => '',
                "paginatelink_margin_top_unit" => 'px',
                "paginatelink_margin_bottom" => '',
                "paginatelink_margin_bottom_unit" => 'px',

                "paginatelink_width" => '',
                "paginatelink_width_unit" => 'px',
                "paginatelink_max_width" => '',
                "paginatelink_max_width_unit" => 'px',
                "paginatelink_min_width" => '',
                "paginatelink_min_width_unit" => 'px',

                "paginatelink_height" => '',
                "paginatelink_height_unit" => 'px',
                "paginatelink_max_height" => '',
                "paginatelink_max_height_unit" => 'px',
                "paginatelink_min_height" => '',
                "paginatelink_min_height_unit" => 'px',

                // paginate Active link size and spacing
                "paginatelinkactive_padding_left" => '',
                "paginatelinkactive_padding_left_unit" => 'px',
                "paginatelinkactive_padding_right" => '',
                "paginatelinkactive_padding_right_unit" => 'px',
                "paginatelinkactive_padding_top" => '',
                "paginatelinkactive_padding_top_unit" => 'px',
                "paginatelinkactive_padding_bottom" => '',
                "paginatelinkactive_padding_bottom_unit" => 'px',
                
                "paginatelinkactive_margin_left" => '',
                "paginatelinkactive_margin_left_unit" => 'px',
                "paginatelinkactive_margin_right" => '',
                "paginatelinkactive_margin_right_unit" => 'px',
                "paginatelinkactive_margin_top" => '',
                "paginatelinkactive_margin_top_unit" => 'px',
                "paginatelinkactive_margin_bottom" => '',
                "paginatelinkactive_margin_bottom_unit" => 'px',

                "paginatelinkactive_width" => '',
                "paginatelinkactive_width_unit" => 'px',
                "paginatelinkactive_max_width" => '',
                "paginatelinkactive_max_width_unit" => 'px',
                "paginatelinkactive_min_width" => '',
                "paginatelinkactive_min_width_unit" => 'px',

                "paginatelinkactive_height" => '',
                "paginatelinkactive_height_unit" => 'px',
                "paginatelinkactive_max_height" => '',
                "paginatelinkactive_max_height_unit" => 'px',
                "paginatelinkactive_min_height" => '',
                "paginatelinkactive_min_height_unit" => 'px',

                // pagination link hover background
                "paginatelinkhover_background_color" => '',
                "paginatelinkhover_background_image" => '',
                "paginatelinkhover_background_size" => '',
                "paginatelinkhover_background_size_width" => '',
                "paginatelinkhover_background_size_width_unit" => 'px',
                "paginatelinkhover_background_size_height" => '',
                "paginatelinkhover_background_size_height_unit" => 'px',
                "paginatelinkhover_background_repeat" => '',

                // pagination active link hover background
                "paginatelinkactivehover_background_color" => '',
                "paginatelinkactivehover_background_image" => '',
                "paginatelinkactivehover_background_size" => '',
                "paginatelinkactivehover_background_size_width" => '',
                "paginatelinkactivehover_background_size_width_unit" => 'px',
                "paginatelinkactivehover_background_size_height" => '',
                "paginatelinkactivehover_background_size_height_unit" => 'px',
                "paginatelinkactivehover_background_repeat" => '',

                // paginate Link hover borders
                "paginatelinkhover_border_all_color" => '',
                "paginatelinkhover_border_top_color" => '',
                "paginatelinkhover_border_left_color" => '',
                "paginatelinkhover_border_bottom_color" => '',
                "paginatelinkhover_border_right_color" => '',
                "paginatelinkhover_border_all_width" => '',
                "paginatelinkhover_border_all_width_unit" => 'px',
                "paginatelinkhover_border_top_width" => '',
                "paginatelinkhover_border_top_width_unit" => 'px',
                "paginatelinkhover_border_left_width" => '',
                "paginatelinkhover_border_left_width_unit" => 'px',
                "paginatelinkhover_border_bottom_width" => '',
                "paginatelinkhover_border_bottom_width_unit" => 'px',
                "paginatelinkhover_border_right_width" => '',
                "paginatelinkhover_border_right_width_unit" => 'px',
                "paginatelinkhover_border_all_style" => '',
                "paginatelinkhover_border_top_style" => '',
                "paginatelinkhover_border_left_style" => '',
                "paginatelinkhover_border_bottom_style" => '',
                "paginatelinkhover_border_right_style" => '',
                "paginatelinkhover_border_radius" => '',
                "paginatelinkhover_border_radius_unit" => 'px',
                "paginatelinkhover_border_top_left_radius" => '',
                "paginatelinkhover_border_top_left_radius_unit" => 'px',
                "paginatelinkhover_border_top_right_radius" => '',
                "paginatelinkhover_border_top_right_radius_unit" => 'px',
                "paginatelinkhover_border_bottom_right_radius" => '',
                "paginatelinkhover_border_bottom_right_radius_unit" => 'px',
                "paginatelinkhover_border_bottom_left_radius" => '',
                "paginatelinkhover_border_bottom_left_radius_unit" => 'px',
                // paginate Active Link hover borders
                "paginatelinkactivehover_border_all_color" => '',
                "paginatelinkactivehover_border_top_color" => '',
                "paginatelinkactivehover_border_left_color" => '',
                "paginatelinkactivehover_border_bottom_color" => '',
                "paginatelinkactivehover_border_right_color" => '',
                "paginatelinkactivehover_border_all_width" => '',
                "paginatelinkactivehover_border_all_width_unit" => 'px',
                "paginatelinkactivehover_border_top_width" => '',
                "paginatelinkactivehover_border_top_width_unit" => 'px',
                "paginatelinkactivehover_border_left_width" => '',
                "paginatelinkactivehover_border_left_width_unit" => 'px',
                "paginatelinkactivehover_border_bottom_width" => '',
                "paginatelinkactivehover_border_bottom_width_unit" => 'px',
                "paginatelinkactivehover_border_right_width" => '',
                "paginatelinkactivehover_border_right_width_unit" => 'px',
                "paginatelinkactivehover_border_all_style" => '',
                "paginatelinkactivehover_border_top_style" => '',
                "paginatelinkactivehover_border_left_style" => '',
                "paginatelinkactivehover_border_bottom_style" => '',
                "paginatelinkactivehover_border_right_style" => '',
                "paginatelinkactivehover_border_radius" => '',
                "paginatelinkactivehover_border_radius_unit" => 'px',
                "paginatelinkactivehover_border_top_left_radius" => '',
                "paginatelinkactivehover_border_top_left_radius_unit" => 'px',
                "paginatelinkactivehover_border_top_right_radius" => '',
                "paginatelinkactivehover_border_top_right_radius_unit" => 'px',
                "paginatelinkactivehover_border_bottom_right_radius" => '',
                "paginatelinkactivehover_border_bottom_right_radius_unit" => 'px',
                "paginatelinkactivehover_border_bottom_left_radius" => '',
                "paginatelinkactivehover_border_bottom_left_radius_unit" => 'px',

                // paginate link hover size and spacing
                "paginatelinkhover_padding_left" => '',
                "paginatelinkhover_padding_left_unit" => 'px',
                "paginatelinkhover_padding_right" => '',
                "paginatelinkhover_padding_right_unit" => 'px',
                "paginatelinkhover_padding_top" => '',
                "paginatelinkhover_padding_top_unit" => 'px',
                "paginatelinkhover_padding_bottom" => '',
                "paginatelinkhover_padding_bottom_unit" => 'px',
                
                "paginatelinkhover_margin_left" => '',
                "paginatelinkhover_margin_left_unit" => 'px',
                "paginatelinkhover_margin_right" => '',
                "paginatelinkhover_margin_right_unit" => 'px',
                "paginatelinkhover_margin_top" => '',
                "paginatelinkhover_margin_top_unit" => 'px',
                "paginatelinkhover_margin_bottom" => '',
                "paginatelinkhover_margin_bottom_unit" => 'px',

                "paginatelinkhover_width" => '',
                "paginatelinkhover_width_unit" => 'px',
                "paginatelinkhover_max_width" => '',
                "paginatelinkhover_max_width_unit" => 'px',
                "paginatelinkhover_min_width" => '',
                "paginatelinkhover_min_width_unit" => 'px',

                "paginatelinkhover_height" => '',
                "paginatelinkhover_height_unit" => 'px',
                "paginatelinkhover_max_height" => '',
                "paginatelinkhover_max_height_unit" => 'px',
                "paginatelinkhover_min_height" => '',
                "paginatelinkhover_min_height_unit" => 'px',

                // paginate Active link hover size and spacing
                "paginatelinkactivehover_padding_left" => '',
                "paginatelinkactivehover_padding_left_unit" => 'px',
                "paginatelinkactivehover_padding_right" => '',
                "paginatelinkactivehover_padding_right_unit" => 'px',
                "paginatelinkactivehover_padding_top" => '',
                "paginatelinkactivehover_padding_top_unit" => 'px',
                "paginatelinkactivehover_padding_bottom" => '',
                "paginatelinkactivehover_padding_bottom_unit" => 'px',
                
                "paginatelinkactivehover_margin_left" => '',
                "paginatelinkactivehover_margin_left_unit" => 'px',
                "paginatelinkactivehover_margin_right" => '',
                "paginatelinkactivehover_margin_right_unit" => 'px',
                "paginatelinkactivehover_margin_top" => '',
                "paginatelinkactivehover_margin_top_unit" => 'px',
                "paginatelinkactivehover_margin_bottom" => '',
                "paginatelinkactivehover_margin_bottom_unit" => 'px',

                "paginatelinkactivehover_width" => '',
                "paginatelinkactivehover_width_unit" => 'px',
                "paginatelinkactivehover_max_width" => '',
                "paginatelinkactivehover_max_width_unit" => 'px',
                "paginatelinkactivehover_min_width" => '',
                "paginatelinkactivehover_min_width_unit" => 'px',

                "paginatelinkactivehover_height" => '',
                "paginatelinkactivehover_height_unit" => 'px',
                "paginatelinkactivehover_max_height" => '',
                "paginatelinkactivehover_max_height_unit" => 'px',
                "paginatelinkactivehover_min_height" => '',
                "paginatelinkactivehover_min_height_unit" => 'px',

                // paginate link transition duration
                "paginate_link_transition" => '',
            ), $options, $this->options['tag'] );

        $this->param_array[$options['id']]["selector"] = esc_attr($options['selector']);

        ob_start();
        
        global $oxygen_vsb_css_caching_active;

	    $gutenberg_placeholder = "";
        if( !empty( $_GET['oxygen_gutenberg_script'] ) ) {
		    // Get the nicename set inside Oxygen
		    $options_temp = json_decode( $atts['ct_options'] );
		    $gutenberg_placeholder = !empty( $options_temp->nicename ) ? $options_temp->nicename : 'Repeater Preview Unavailable';
	    }
        
        ?><<?php echo esc_attr($options['tag'])?> id="<?php echo esc_attr($options['selector']); ?>" <?php if( !empty( $gutenberg_placeholder ) ) echo 'gutenberg-placeholder="' . $gutenberg_placeholder . '" '; ?> class="<?php if(isset($options['classes'])) echo esc_attr($options['classes']); ?>"  <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php
            $this->repeaterStart();
	        // Do not render inside gutenberg
            if ( empty( $_GET['oxygen_gutenberg_script'] ) ){

		        if(class_exists('ACF') && isset($options['use_acf_repeater']) && $options['use_acf_repeater'] !== 'false') {

			        if(isset($options['acf_repeater']) && !empty($options['acf_repeater'])) {

                        $field = get_field($options['acf_repeater'], 'option', true);
                        $is_options_page = $field !== false ? 'option' : '';

				        $count= 0;
                        // if the css cache is to be generated and there are no rows, we still need to run the shortcodes atleast once
                        if(isset($oxygen_vsb_css_caching_active) && $oxygen_vsb_css_caching_active === true) {
                            if(!have_rows($options['acf_repeater'], $is_options_page)) {
                                do_oxygen_elements( $content );
                                $this->repeaterLoop();
                            }
                        }
                        
				        while ( have_rows($options['acf_repeater'], $is_options_page) ) : the_row();
					        // if it is the css being rendered, the only one iteration is sufficient
					        if((!isset($oxygen_vsb_css_caching_active) || $oxygen_vsb_css_caching_active !== true) || $count < 1) {
						        // Your loop code
						        echo do_oxygen_elements( $content );
					        }
					        $count++;
                            $this->repeaterLoop();
				        endwhile;

			        }
		        }
                else if(class_exists('RWMB_Loader') && isset($options['use_metabox_clonable_group']) && $options['use_metabox_clonable_group'] !== 'false') {
                    
			        if(isset($options['metabox_group']) && !empty($options['metabox_group'])) {
                        $groups = rwmb_meta($options['metabox_group']);
                        global $meta_box_current_group_fields;
                        foreach ($groups as $group => $fields) {
                            $meta_box_current_group_fields = $fields;
                            echo do_oxygen_elements( $content );
                        }
			        }
                }
		        else {
			        // global variable to hold this query so that the oxy dynamic shortcodes use it
			        global $oxy_vsb_use_query;
			        global $oxy_vsb_use_query_parent;
			        $oxy_vsb_use_query = $query;
                    
                    // Save parent query for the first Repeater
                    if (!$oxy_vsb_use_query_parent) {
			            $oxy_vsb_use_query_parent = $query;
                    }

                    //if the css cache is to be generated and there are no posts, we still need to run the shortcodes atleast once
                    if(isset($oxygen_vsb_css_caching_active) && $oxygen_vsb_css_caching_active === true) {
                        if(!$query->have_posts()) {
                            do_oxygen_elements( $content );
                            $this->repeaterLoop();
                        }
                    }

			        while ($query->have_posts()) {
				        $query->the_post();
				        echo do_oxygen_elements( $content );
				        // if it is the css being rendered, the only one iteration is sufficient
				        if(isset($oxygen_vsb_css_caching_active) && $oxygen_vsb_css_caching_active === true) {
					        break;
				        }
                        $this->repeaterLoop();
			        }

                    $big = 999999999; // need an unlikely integer
                    ?>
                    
                        <?php
                        $pagination_args = array(
                            'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                            'format' => '?paged=%#%',
                            'current' => max( 1, get_query_var('paged') ),
                            'total' => $query->max_num_pages,

                        );

                        if(isset($options['paginate_mid_size']) && is_numeric($options['paginate_mid_size'])) {
                            $pagination_args['mid_size'] =  intval($options['paginate_mid_size']);
                        }

                        if(isset($options['paginate_end_size']) && is_numeric($options['paginate_end_size'])) {
                            $pagination_args['end_size'] =  intval($options['paginate_end_size']);
                        }

                        if(isset($options['paginate_prev_link_text']) && $options['paginate_prev_link_text']) {
                            $pagination_args['prev_text'] =  sanitize_text_field(oxygen_vsb_base64_decode($options['paginate_prev_link_text']));
                        }
                        if(isset($options['paginate_next_link_text']) && $options['paginate_next_link_text']) {
                            $pagination_args['next_text'] =  sanitize_text_field(oxygen_vsb_base64_decode($options['paginate_next_link_text']));
                        }

                        $pagination = paginate_links( $pagination_args );
                       ?>
                    
                    <?php if ($pagination) { ?>
                    <div class="oxy-repeater-pages-wrap">
                        <div class="oxy-repeater-pages">
                            <?php echo $pagination; ?>
                        </div>
                    </div>
                    <?php } ?>
                    
                    <?php

                    if ($oxy_vsb_use_query_parent != $oxy_vsb_use_query) {
                        // End of the child Repeater, restore query to parent
			            $oxy_vsb_use_query = $oxy_vsb_use_query_parent; 
                        $GLOBALS['wp_query'] = $oxy_vsb_use_query;
	                    wp_reset_postdata();
                    }
                    else {
                        // End of the parent Repeater, restore to global
                        $oxy_vsb_use_query_parent = false;
                        $oxy_vsb_use_query = false;
                        wp_reset_query();
                    }
			        
		        }

            }
            $this->repeaterEnd();
        ?></<?php echo esc_attr($options['tag'])?>><?php

        // return ob_get_clean();
        
        // Below is the fix for incrementing repeater ID's
        $output = ob_get_clean();
        // $output contains the output of the repeater loop
        $doc = new DOMDocument();

        // fix html5/svg errors
        libxml_use_internal_errors(true);
        
        // Convert script content to string
        $scriptPos1 = 0;
        $scriptPos2 = 0;

        $output = $this->script_to_html_entities($output, true);
        
        ob_start();
        $doc->loadHtml(mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8'));
        ob_end_clean();

        // prevent memory overuse
        libxml_clear_errors();
        libxml_use_internal_errors(false);
        
        $incr = 0;
        
        foreach($doc->lastChild->lastChild->lastChild->childNodes as $node) {
            $this->incrementIds($node, ++$incr);
        }
        
        // remove doctype.
        if ( $doc->doctype ) {
            $doc->removeChild( $doc->doctype ); 
        }

        // remove html, body tags.
        $doc->replaceChild($doc->firstChild->firstChild->firstChild, $doc->firstChild);

        $docHtml = $doc->saveHTML();

        // revert script content
        $scriptPos1 = 0;
        $scriptPos2 = 0;

        $docHtml = $this->script_to_html_entities($docHtml, false);

        return $docHtml;
    }

    function incrementIds($node, $incr) {

        // update gallery inline styles ids
        if (isset($node->tagName) && $node->tagName == "style" && isset($node->nodeValue)){
            $id = $node->getAttribute('data-element-id');
            $node->nodeValue = str_replace( $id, $id."-".$incr, $node->nodeValue);
        }

        if(method_exists($node, 'setAttribute')) {
            $id = $node->getAttribute('id');
            $data_id = $node->getAttribute('data-id');
            
            if($id) {
                if (!$data_id) {
                    $node->setAttribute('data-id', $id);
                }
                $node->setAttribute('id', $id."-".$incr);
            }
        }

        // Don't increment IDs inside Code Blocks
        if(method_exists($node, 'getAttribute')) {
            $class = $node->getAttribute("class");
            if ( strpos($class, "ct-code-block") !== false ) {
                return;
            }
        }

        if (is_iterable($node->childNodes)) {
            foreach($node->childNodes as $child) {
                $this->incrementIds($child, $incr); 
            }
        }
    }

    function repeaterStart() {
        global $is_repeater_child;
        // 1 if children of a Repeater are being rendered
        // 3 if children of a 2nd level Repeater are being rendered
        // 2 or 4 if children of a repeater are already rendered once, so we can skip the build_css cycle in component.class.php
        $is_repeater_child = $is_repeater_child == 0 ? 1 : 3;
    }

    function repeaterEnd() {
        global $is_repeater_child;
        $is_repeater_child = $is_repeater_child == 4 || $is_repeater_child == 3 ? 1 : 0;
        
    }

    function repeaterLoop() {
        global $is_repeater_child;
        $is_repeater_child = $is_repeater_child == 1 || $is_repeater_child == 2 ? 2 : 4;
    }

    function setQuery($options , $debug=false) {
        $query = false;

        $defaults = array(
            "query_order"=> "",
            "query_order_by"=> "",
            "query_taxonomies_any"=> "",
            "query_taxonomies_all"=> "",
            "query_post_ids" => "",
        );
        
        foreach ($defaults as $key => $value) {
            if (!isset($options[$key])) {
                $options[$key] = "";
            }
        }
        
        // manual
        if (isset($options['query_args']) && isset($options['wp_query']) && $options['wp_query']=='manual') {

            $args = $options['query_args'];
            /* https://wordpress.stackexchange.com/questions/120407/how-to-fix-pagination-for-custom-loops 
            apparently doesn't work on static front pages? */
            $args .= get_query_var( 'paged' ) ? '&paged='.get_query_var( 'paged' ) : '';

            //$this->query = new WP_Query($args);

            $query = new WP_Query($args);
        }

        // query builder
        elseif (isset($options['wp_query']) && $options['wp_query']=='custom') {
            
            $args = array();
            
            // post type
            if ($options['query_post_ids']) {
                $args['post__in'] = explode(",",$options['query_post_ids']);
                $args['post_type'] = $options['query_post_types'];
            }
            else {
                $args['post_type'] = $options['query_post_types'];
            }

            // filtering
            if (is_array($options['query_taxonomies_any'])) {
                
                $taxonomies = array();
                $args['tax_query'] = array(
                    'relation' => 'OR',
                );

                // sort IDs by taxonomy slug
                foreach ($options['query_taxonomies_any'] as $value) {
                    $value = explode(",",$value);
                    $key = $value[0];
                    if ($key == "tag") {
                        $key = "post_tag";
                    }
                    $taxonomies[$key][] = $value[1];
                }

                foreach ($taxonomies as $key => $value) {
                    $args['tax_query'][] = array(
                        'taxonomy' => $key,
                        'terms'    => $value,
                    );
                }
            }
            if (is_array($options['query_taxonomies_all'])&&!empty($options['query_taxonomies_all'])) {
                
                $taxonomies = array();
                $args['tax_query'] = array(
                    'relation' => 'AND',
                );

                // sort IDs by taxonomy slug
                foreach ($options['query_taxonomies_all'] as $value) {
                    $value = explode(",",$value);
                    $key = $value[0];
                    if ($key == "tag") {
                        $key = "post_tag";
                    }
                    $taxonomies[$key][] = $value[1];
                }

                foreach ($taxonomies as $key => $value) {
                    $args['tax_query'][] = array(
                        'taxonomy' => $key,
                        'terms'    => $value,
                        'operator' => 'AND'
                    );
                }
            }
            if ($options['query_authors']) {
                $args['author__in'] = $options['query_authors'];
            }

            // order
            $args['order']   = $options['query_order'];
            $args['orderby'] = $options['query_order_by'];

            if ($options['query_ignore_sticky_posts']==='true') {
                $args['ignore_sticky_posts'] = true;
            }

            if ($options['query_count'] && is_numeric($options['query_count']) && intval($options['query_count']) > 0) {
                $args['posts_per_page'] = $options['query_count'];
            } else {
                $args['nopaging'] = true;
            }
            
            // pagination
            if (get_query_var('paged') && (!isset($args['nopaging']) || !$args['nopaging'])) {
                $args['paged'] = get_query_var( 'paged' );
            }

            //$this->query = new WP_Query($args);
            $query = new WP_Query($args);

        } elseif(isset($options['wp_query']) && $options['wp_query']==='advanced') {
            include_once(CT_FW_PATH."/includes/advanced-query.php");
            $args = Oxy_VSB_Advanced_Query::query_args($options['wp_query_advanced']);

            $query = new WP_Query($args);

        } else {
            
            // use the current default query
            global $wp_query;

            $query = $wp_query;
            //$query = new WP_Query($args);
        }

        return $query;
    }

    
    /**
     * Used in layouts\dynamic-list.php (for Builder purposes only)
     *
     * @since 2.0+
     * @author Gagan
     */

    function parse_shortcodes_map($models, $options, $parentQuery = false, $repeaterFields = false) {

        $results = $this->parse_map_recursively($models, $options, $parentQuery, $repeaterFields);

        if (isset($results['pagination'])) {
            return $results;
        }

        return array('results' => $results);
    }


    /**
     * Parse entire Repeater element recursively (for Builder purposes only)
     *
     * @since 2.0+
     * @author Gagan
     */

    function parse_map_recursively($models, $options, $parentQuery = false, $repeaterFields = false) {
        
        $results = array();
        $pagination = false;

        // ACF Repeater
        if (isset($options['use_acf_repeater']) && $options['use_acf_repeater'] !== "false" && class_exists('ACF')) {
            
            // if parentQuery exists, use that before rendering the repeater fields
            if($parentQuery && is_array($parentQuery)) { // to open a query

                global $oxy_vsb_use_query;
            
                $old_query = false;

                if($oxy_vsb_use_query) {
                    $old_query = $oxy_vsb_use_query;
                }

                $query = $this->setQuery($parentQuery);
               
                $oxy_vsb_use_query = $query;//$this->query;
                
                if ($query->have_posts()) {
                    $query->the_post();
                }
            }

            if($repeaterFields && is_array($repeaterFields)) {

                foreach($repeaterFields as $repeaterField) {

                    $field = get_field($repeaterField, 'option', true);
                    $is_options_page = $field !== false ? 'option' : '';

                    if(have_rows($repeaterField) ) {
                        the_row();
                    }
                }
            }

            $field = get_field($options['acf_repeater'], 'option', true);
            $is_options_page = $field !== false ? 'option' : '';

            while ( have_rows($options['acf_repeater'], $is_options_page) ) : the_row();

                // Your loop code
                $results[] = $this->resolve_recursively($models);

            endwhile;

            if($parentQuery && is_array($parentQuery)) { // to close the query
                // reset query to previous state
                if($old_query) {
                    $oxy_vsb_use_query = $old_query;
                    $oxy_vsb_use_query->reset_postdata();
                }
            }

        } 

        // MetaBox Repeater
        else if (isset($options['use_metabox_clonable_group']) && $options['use_metabox_clonable_group'] !== "false" && class_exists('RWMB_Loader')) {
            
            // if parentQuery exists, use that before rendering the repeater fields
            if($parentQuery && is_array($parentQuery)) { // to open a query

                global $oxy_vsb_use_query;
            
                $old_query = false;

                if($oxy_vsb_use_query) {
                    $old_query = $oxy_vsb_use_query;
                }

                $query = $this->setQuery($parentQuery);
               
                $oxy_vsb_use_query = $query;//$this->query;
                
                if ($query->have_posts()) {
                    $query->the_post();
                }
            }

            if(isset($options['metabox_group']) && !empty($options['metabox_group'])) {
                $groups = rwmb_meta($options['metabox_group']);
                global $meta_box_current_group_fields;
                foreach ($groups as $group => $fields) {
                    $meta_box_current_group_fields = $fields;
                    $results[] = $this->resolve_recursively($models, $fields);
                }
            }

            if($parentQuery && is_array($parentQuery)) { // to close the query
                // reset query to previous state
                if($old_query) {
                    $oxy_vsb_use_query = $old_query;
                    $oxy_vsb_use_query->reset_postdata();
                }
            }

        } 

        // Regular (Non ACF, non MetaBox) repeater
        else {

            // global variable to hold this query so that the oxy dynamic shortcodes use it
            global $oxy_vsb_use_query;
            
            $old_query = false;

            if($oxy_vsb_use_query) {
                $old_query = $oxy_vsb_use_query;
            }

            $query = $this->setQuery($options);
           
            $oxy_vsb_use_query = $query;//$this->query;

            while ($query->have_posts()) {
                $query->the_post();
                $results[] = $this->resolve_recursively($models);
            }

            $big = 999999999; // need an unlikely integer

            $pagination_args = array(
                'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format' => '?paged=%#%',
                'current' => max( 1, get_query_var('paged') ),
                'total' => $query->max_num_pages
            );

            if(isset($options['paginate_mid_size']) && is_numeric($options['paginate_mid_size'])) {
                $pagination_args['mid_size'] =  intval($options['paginate_mid_size']);
            }

            if(isset($options['paginate_end_size']) && is_numeric($options['paginate_end_size'])) {
                $pagination_args['end_size'] =  intval($options['paginate_end_size']);
            }

            if(isset($options['paginate_prev_link_text']) && $options['paginate_prev_link_text']) {
                $pagination_args['prev_text'] =  sanitize_text_field($options['paginate_prev_link_text']);
            }
            if(isset($options['paginate_next_link_text']) && $options['paginate_next_link_text']) {
                $pagination_args['next_text'] =  sanitize_text_field($options['paginate_next_link_text']);
            }

            $pagination = paginate_links( $pagination_args );
            
            if($pagination) {

                $cssutil = new Oxygen_VSB_CSS_Util;

                $cssutil->register_selector('.oxy-repeater-pages');
                $cssutil->register_selector('.oxy-repeater-pages a.page-numbers');
                $cssutil->register_selector('.oxy-repeater-pages a.page-numbers:hover');
                
                $cssutil->map_property('paginate_alignment', 'justify-content',                '.oxy-repeater-pages');
                $cssutil->map_property('paginate_wrap_alignment', 'justify-content',                '.oxy-repeater-pages-wrap');
                $cssutil->map_property('paginate_size', 'font-size',                '.oxy-repeater-pages');
                $cssutil->map_property('paginate_color', 'color',                         '.oxy-repeater-pages');
                $cssutil->map_property('paginate_link_color', 'color',                    '.oxy-repeater-pages a.page-numbers');
                $cssutil->map_property('paginate_link_hover_color', 'color',              '.oxy-repeater-pages a.page-numbers:hover');

                // pagination size and spacing
                $cssutil->map_property('paginate_padding_top', 'padding-top',                '.oxy-repeater-pages');
                $cssutil->map_property('paginate_padding_left', 'padding-left',                '.oxy-repeater-pages');
                $cssutil->map_property('paginate_padding_right', 'padding-right',                         '.oxy-repeater-pages');
                $cssutil->map_property('paginate_padding_bottom', 'padding-bottom',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_margin_top', 'margin-top',                '.oxy-repeater-pages');
                $cssutil->map_property('paginate_margin_left', 'margin-left',                '.oxy-repeater-pages');
                $cssutil->map_property('paginate_margin_right', 'margin-right',                         '.oxy-repeater-pages');
                $cssutil->map_property('paginate_margin_bottom', 'margin-bottom',                    '.oxy-repeater-pages');

                $cssutil->map_property('paginate_width', 'width',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_min_width', 'min-width',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_max_width', 'max-width',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_height', 'height',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_min_height', 'min-height',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_max_height', 'max-height',                    '.oxy-repeater-pages');

                // pagination children layout
                $cssutil->map_property('paginate_flex_direction', 'flex-direction',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_flex_wrap', 'flex-wrap',                    '.oxy-repeater-pages');

                // pagination borders
                $cssutil->map_property('paginate_border_all_color', 'border-color',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_top_color', 'border-top-color',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_left_color', 'border-left-color',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_bottom_color', 'border-bottom-color',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_right_color', 'border-right-color',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_all_width', 'border-width',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_top_width', 'border-top-width',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_left_width', 'border-left-width',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_bottom_width', 'border-bottom-width',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_right_width', 'border-right-width',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_all_style', 'border-style',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_top_style', 'border-top-style',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_left_style', 'border-left-style',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_bottom_style', 'border-bottom-style',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_right_style', 'border-right-style',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_radius', 'border-radius',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_top_left_radius', 'border-top-left-radius',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_top_right_radius', 'border-top-right-radius',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_bottom_right_radius', 'border-bottom-right-radius',                    '.oxy-repeater-pages');
                $cssutil->map_property('paginate_border_bottom_left_radius', 'border-bottom-left-radius',                    '.oxy-repeater-pages');
                // pagination link borders
                $cssutil->map_property('paginatelink_border_all_color', 'border-color',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_top_color', 'border-top-color',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_left_color', 'border-left-color',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_bottom_color', 'border-bottom-color',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_right_color', 'border-right-color',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_all_width', 'border-width',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_top_width', 'border-top-width',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_left_width', 'border-left-width',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_bottom_width', 'border-bottom-width',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_right_width', 'border-right-width',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_all_style', 'border-style',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_top_style', 'border-top-style',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_left_style', 'border-left-style',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_bottom_style', 'border-bottom-style',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_right_style', 'border-right-style',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_radius', 'border-radius',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_top_left_radius', 'border-top-left-radius',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_top_right_radius', 'border-top-right-radius',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_bottom_right_radius', 'border-bottom-right-radius',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_border_bottom_left_radius', 'border-bottom-left-radius',                    '.oxy-repeater-pages a');
                // pagination active link borders
                $cssutil->map_property('paginatelinkactive_border_all_color', 'border-color',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_top_color', 'border-top-color',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_left_color', 'border-left-color',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_bottom_color', 'border-bottom-color',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_right_color', 'border-right-color',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_all_width', 'border-width',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_top_width', 'border-top-width',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_left_width', 'border-left-width',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_bottom_width', 'border-bottom-width',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_right_width', 'border-right-width',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_all_style', 'border-style',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_top_style', 'border-top-style',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_left_style', 'border-left-style',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_bottom_style', 'border-bottom-style',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_right_style', 'border-right-style',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_radius', 'border-radius',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_top_left_radius', 'border-top-left-radius',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_top_right_radius', 'border-top-right-radius',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_bottom_right_radius', 'border-bottom-right-radius',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_border_bottom_left_radius', 'border-bottom-left-radius',                    '.oxy-repeater-pages span.current');
                // pagination background
                $cssutil->map_property("paginate_background_color", 'background-color',                    '.oxy-repeater-pages');
                $cssutil->map_property("paginate_background_image", 'background-image',                    '.oxy-repeater-pages');
                $cssutil->map_property("paginate_background_size", 'background-size',                    '.oxy-repeater-pages');
                $cssutil->map_property("paginate_background_size_width", 'background-size-width',                    '.oxy-repeater-pages');
                $cssutil->map_property("paginate_background_size_height", 'background-size-height',                    '.oxy-repeater-pages');
                $cssutil->map_property("paginate_background_repeat", 'background-repeat',                    '.oxy-repeater-pages');

                // pagination link background
                $cssutil->map_property("paginatelink_background_color", 'background-color',                    '.oxy-repeater-pages a');
                $cssutil->map_property("paginatelink_background_image", 'background-image',                    '.oxy-repeater-pages a');
                $cssutil->map_property("paginatelink_background_size", 'background-size',                    '.oxy-repeater-pages a');
                $cssutil->map_property("paginatelink_background_size_width", 'background-size-width',                    '.oxy-repeater-pages a');
                $cssutil->map_property("paginatelink_background_size_height", 'background-size-height',                    '.oxy-repeater-pages a');
                $cssutil->map_property("paginatelink_background_repeat", 'background-repeat',                    '.oxy-repeater-pages a');

                // pagination Active link background
                $cssutil->map_property("paginatelinkactive_background_color", 'background-color',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property("paginatelinkactive_background_image", 'background-image',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property("paginatelinkactive_background_size", 'background-size',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property("paginatelinkactive_background_size_width", 'background-size-width',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property("paginatelinkactive_background_size_height", 'background-size-height',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property("paginatelinkactive_background_repeat", 'background-repeat',                    '.oxy-repeater-pages span.current');

                // pagination links size and spacing
                $cssutil->map_property('paginatelink_padding_top', 'padding-top',                '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_padding_left', 'padding-left',                '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_padding_right', 'padding-right',                         '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_padding_bottom', 'padding-bottom',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_margin_top', 'margin-top',                '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_margin_left', 'margin-left',                '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_margin_right', 'margin-right',                         '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_margin_bottom', 'margin-bottom',                    '.oxy-repeater-pages a');

                $cssutil->map_property('paginatelink_width', 'width',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_min_width', 'min-width',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_max_width', 'max-width',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_height', 'height',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_min_height', 'min-height',                    '.oxy-repeater-pages a');
                $cssutil->map_property('paginatelink_max_height', 'max-height',                    '.oxy-repeater-pages a');

                // pagination Active links size and spacing
                $cssutil->map_property('paginatelinkactive_padding_top', 'padding-top',                '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_padding_left', 'padding-left',                '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_padding_right', 'padding-right',                         '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_padding_bottom', 'padding-bottom',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_margin_top', 'margin-top',                '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_margin_left', 'margin-left',                '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_margin_right', 'margin-right',                         '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_margin_bottom', 'margin-bottom',                    '.oxy-repeater-pages span.current');

                $cssutil->map_property('paginatelinkactive_width', 'width',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_min_width', 'min-width',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_max_width', 'max-width',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_height', 'height',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_min_height', 'min-height',                    '.oxy-repeater-pages span.current');
                $cssutil->map_property('paginatelinkactive_max_height', 'max-height',                    '.oxy-repeater-pages span.current');

                // pagination link hover background
                $cssutil->map_property("paginatelinkhover_background_color", 'background-color',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property("paginatelinkhover_background_image", 'background-image',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property("paginatelinkhover_background_size", 'background-size',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property("paginatelinkhover_background_size_width", 'background-size-width',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property("paginatelinkhover_background_size_height", 'background-size-height',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property("paginatelinkhover_background_repeat", 'background-repeat',                    '.oxy-repeater-pages a:hover');

                // pagination Active link hover background
                $cssutil->map_property("paginatelinkactivehover_background_color", 'background-color',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property("paginatelinkactivehover_background_image", 'background-image',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property("paginatelinkactivehover_background_size", 'background-size',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property("paginatelinkactivehover_background_size_width", 'background-size-width',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property("paginatelinkactivehover_background_size_height", 'background-size-height',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property("paginatelinkactivehover_background_repeat", 'background-repeat',                    '.oxy-repeater-pages span.current:hover');

                // pagination links hover size and spacing
                $cssutil->map_property('paginatelinkhover_padding_top', 'padding-top',                '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_padding_left', 'padding-left',                '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_padding_right', 'padding-right',                         '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_padding_bottom', 'padding-bottom',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_margin_top', 'margin-top',                '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_margin_left', 'margin-left',                '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_margin_right', 'margin-right',                         '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_margin_bottom', 'margin-bottom',                    '.oxy-repeater-pages a:hover');

                $cssutil->map_property('paginatelinkhover_width', 'width',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_min_width', 'min-width',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_max_width', 'max-width',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_height', 'height',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_min_height', 'min-height',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_max_height', 'max-height',                    '.oxy-repeater-pages a:hover');

                // pagination Active links hover size and spacing
                $cssutil->map_property('paginatelinkactivehover_padding_top', 'padding-top',                '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_padding_left', 'padding-left',                '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_padding_right', 'padding-right',                         '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_padding_bottom', 'padding-bottom',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_margin_top', 'margin-top',                '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_margin_left', 'margin-left',                '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_margin_right', 'margin-right',                         '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_margin_bottom', 'margin-bottom',                    '.oxy-repeater-pages span.current:hover');

                $cssutil->map_property('paginatelinkactivehover_width', 'width',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_min_width', 'min-width',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_max_width', 'max-width',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_height', 'height',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_min_height', 'min-height',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_max_height', 'max-height',                    '.oxy-repeater-pages span.current:hover');

                // pagination link hover borders
                $cssutil->map_property('paginatelinkhover_border_all_color', 'border-color',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_top_color', 'border-top-color',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_left_color', 'border-left-color',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_bottom_color', 'border-bottom-color',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_right_color', 'border-right-color',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_all_width', 'border-width',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_top_width', 'border-top-width',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_left_width', 'border-left-width',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_bottom_width', 'border-bottom-width',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_right_width', 'border-right-width',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_all_style', 'border-style',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_top_style', 'border-top-style',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_left_style', 'border-left-style',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_bottom_style', 'border-bottom-style',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_right_style', 'border-right-style',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_radius', 'border-radius',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_top_left_radius', 'border-top-left-radius',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_top_right_radius', 'border-top-right-radius',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_bottom_right_radius', 'border-bottom-right-radius',                    '.oxy-repeater-pages a:hover');
                $cssutil->map_property('paginatelinkhover_border_bottom_left_radius', 'border-bottom-left-radius',                    '.oxy-repeater-pages a:hover');
                // pagination active link hover borders
                $cssutil->map_property('paginatelinkactivehover_border_all_color', 'border-color',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_top_color', 'border-top-color',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_left_color', 'border-left-color',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_bottom_color', 'border-bottom-color',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_right_color', 'border-right-color',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_all_width', 'border-width',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_top_width', 'border-top-width',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_left_width', 'border-left-width',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_bottom_width', 'border-bottom-width',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_right_width', 'border-right-width',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_all_style', 'border-style',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_top_style', 'border-top-style',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_left_style', 'border-left-style',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_bottom_style', 'border-bottom-style',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_right_style', 'border-right-style',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_radius', 'border-radius',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_top_left_radius', 'border-top-left-radius',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_top_right_radius', 'border-top-right-radius',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_bottom_right_radius', 'border-bottom-right-radius',                    '.oxy-repeater-pages span.current:hover');
                $cssutil->map_property('paginatelinkactivehover_border_bottom_left_radius', 'border-bottom-left-radius',                    '.oxy-repeater-pages span.current:hover');

                // pagination link transition
                $cssutil->map_property('paginate_link_transition', 'transition',                    '.oxy-repeater-pages > *');

                $pagination.='<style>';
                $pagination.= $cssutil->generate_css($options);
                $pagination.='</style>';
            }

            // reset query to previous state
            if($old_query) {
                $oxy_vsb_use_query = $old_query;
                $oxy_vsb_use_query->reset_postdata();
            }
        }

        if($pagination) {
            return array('results' => $results, 'pagination' => $pagination);
        }

        return $results;
    }

    
    /**
     * Resolve each component inside the Repeater recurcively
     *
     * @since 2.0+
     * @author Gagan
     */

    function resolve_recursively($arr) {

        $results = array();
        $debug   = false;
        
        global $oxy_vsb_use_query;
        global $OxygenConditions;

        if (is_array($arr)) {
            foreach($arr as $key => $value) {
                
                // Repeater root element
                if (isset($arr['name']) && $arr['name'] === 'oxy_dynamic_list' && $key == 'children') {
                    $debug = true;
                    $result = $this->parse_map_recursively($value, $arr['original']);
                }
                else
                // ????
                if ($key === 'globalconditions') {
                    if($value && sizeof($value) > 0) {
        				if (isset($OxygenConditions)) {
                            $results['globalConditionsResult'] = $OxygenConditions->global_conditions_result(array('conditions'=>$value, 'type'=>isset($arr['conditionstype'])?$arr['conditionstype']:''));
                        }
                    }
                }
                else
                // go deeper in recursion
                if (is_array($value)) {
                    $result = $this->resolve_recursively($value);
                } 
                else
                // ????
                if ($key === 'renderedHTML') {
                    
                    $result = '';
                    $slug   = substr($arr['name'], 4, strlen($arr['name'])-4); // strip oxy- from the begining of the string
                    
                    global $oxy_el_slug_classes;
                    
                    if (is_array($oxy_el_slug_classes) && isset($oxy_el_slug_classes[$slug])) {
                        
                        $element_class      = $oxy_el_slug_classes[$slug];
                        $element_instance   = new $element_class();
                        $el                 = $element_instance->El;
                        $options            = null;
                        
                        if (isset($arr['original'])) {
                            $options = $arr['original'];
                        }

                        $content = null;
                        
                        if (isset($arr['component']) && isset($arr['component']['children']) ) {
                            $content = "[oxy-empty-shortcode]";
                        }

                        ob_start();
                        $element_instance->render(
                            $el->unprefix_options($options), 
                            $el->unprefix_options($el->defaults), 
                            $content);
                        $result = ob_get_clean();    
                    }                    

                } 
                else
                // "this has already been taken care of" what does it mean?
                if ($key !== 'globalConditionsResult' && $key !== 'component') {
                    // sign "[oxygen]" shortcode on the fly
                    if (stripos($value, '[oxygen') !== false) {
                        $value = ct_sign_oxy_dynamic_shortcode(array($value));
                    }
                    $result = do_oxygen_elements($value);
                }
                
                // "because this value is expicitly set above or not required" what does it mean?
                if ($key !== 'globalConditionsResult' && $key !== 'component') { 
                    $results[$key] = $result;
                }
            }
        }

        return $results;
    }


    /**
     * Basic Styles settings
     *
     * @since 2.0
     * @author Gagan
     */

    function settings () { 

        global $oxygen_toolbar; ?>

        <div class="oxygen-sidebar-flex-panel oxygen-sidebar-dynamic-list-panel"
            ng-hide="!isActiveName('oxy_dynamic_list')">

            <div class="oxygen-sidebar-advanced-subtab" 
                ng-click="switchTab('dynamicList', 'query')" 
                ng-show="!hasOpenTabs('dynamicList')">
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/general-config.svg">
                    <?php _e("Query", "oxygen"); ?>
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
            </div>

            <div class="oxygen-sidebar-advanced-subtab" 
                ng-click="switchTab('dynamicList', 'layout')" 
                ng-show="!hasOpenTabs('dynamicList')">
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/general-config.svg">
                    <?php _e("Layout", "oxygen"); ?>
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
            </div>

            <div class="oxygen-sidebar-advanced-subtab" 
                ng-click="switchTab('dynamicList', 'pagination')" 
                ng-show="!hasOpenTabs('dynamicList')">
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/general-config.svg">
                    <?php _e("Pagination", "oxygen"); ?>
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
            </div>
            
            <div class="oxygen-sidebar-advanced-subtab" 
                ng-click="switchTab('dynamicList', 'grid_layout')" 
                ng-show="!hasOpenTabs('dynamicList')">
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                    <?php _e("Grid Layout", "oxygen"); ?>
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
            </div>

            <div ng-if="isShowTab('dynamicList','grid_layout')"> 
                
                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="tabs.dynamicList=[]">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="tabs.dynamicList=[]"><?php _e("Repeater","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Grid Layout","oxygen"); ?></div>
                </div>
                
                <div class="oxygen-control-row">
                    <div class="oxygen-control-wrapper">
                        <div class="oxygen-control">
                            <?php 
                            $param = [];
                            $param['param_name'] = 'flex-direction';
                            $tag = $this->options['tag'];
                            ?>
                            <?php include( CT_FW_PATH . '/toolbar/views/position/position.flex-layout.view.php');?>
                        </div>
                    </div>
                </div>
            </div>

            <div class='oxygen-control-row' ng-if="!isShowTabOfGroup('dynamicList')">
                <div class='oxygen-control-wrapper' id='oxygen-control-layout-display'>
                    <label class='oxygen-control-label'><?php _e("Preview Render", "oxygen"); ?></label>
                    <div class='oxygen-control'>
                        <div class='oxygen-button-list'>

                            <?php $oxygen_toolbar->button_list_button('listrendertype',null, 'Normal Mode','', 'iframeScope.updateRepeaterQuery()'); ?>
                            <?php $oxygen_toolbar->button_list_button('listrendertype',1, 'Single Mode'); ?>

                        </div>
                    </div>
                </div>
            </div>
            

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'layout')">
                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="tabs.dynamicList=[]">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="tabs.dynamicList=[]"><?php _e("Repeater","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Layout","oxygen"); ?></div>
                </div>
                <?php
                    CT_Component::component_params(array(
                        array(
                            "type"          => "flex-layout",
                            "heading"       => __("Layout Child Elements", "oxygen"),
                            "param_name"    => "flex-direction",
                            "css"           => true,
                        ),
                        array(
                            "type"          => "checkbox",
                            "heading"       => __("Allow multiline"),
                            "param_name"    => "flex-wrap",
                            "value"         => "",
                            "true_value"    => "wrap",
                            "false_value"   => "",
                            "condition"     => "flex-direction=row"
                        ),
                        array(
                            "type" => "positioning",
                        ), 
                    ),
                    $this->options['tag']);
                ?>
            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'pagination')">
                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="tabs.dynamicList=[]">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="tabs.dynamicList=[]"><?php _e("Repeater","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Pagination","oxygen"); ?></div>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Alignment","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>

                                <?php $oxygen_toolbar->button_list_button('paginate_wrap_alignment','flex-start'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginate_wrap_alignment','center'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginate_wrap_alignment','flex-end'); ?>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gap-->
                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Gap", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-input'>
                                <input type="text" spellcheck="false"
                                    ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['paginate_end_size']"
                                    ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_dynamic_list','paginate_end_size'); iframeScope.updateRepeaterQuery()">
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Range-->
                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Range", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-input'>
                                <input type="text" spellcheck="false"
                                    ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['paginate_mid_size']"
                                    ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_dynamic_list','paginate_mid_size'); iframeScope.updateRepeaterQuery()">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Prev Link Text-->
                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Previous Link Text", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-input'>
                                <input type="text" spellcheck="false"
                                    ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['paginate_prev_link_text']"
                                    ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_dynamic_list','paginate_prev_link_text'); iframeScope.updateRepeaterQuery()">
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Next Link Text-->
                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Next Link Text", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-input'>
                                <input type="text" spellcheck="false"
                                    ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['paginate_next_link_text']"
                                    ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_dynamic_list','paginate_next_link_text'); iframeScope.updateRepeaterQuery()">
                            </div>
                        </div>
                    </div>
                </div>
                
                 <!-- Link Hover Transition Duration-->
                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Link Hover Transition Duration", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-input'>
                                <input type="text" spellcheck="false"
                                    ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['paginate_link_transition']"
                                    ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_dynamic_list','paginate_link_transition');">
                            </div>
                        </div>
                    </div>

                </div>

                <div class='oxygen-control-row'>
                    <?php $oxygen_toolbar->measure_box_with_wrapper('paginate_size',__('Font size','oxygen')); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginate_color", __("Text Color", "oxygen") ); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginate_link_color", __("Link Color", "oxygen") ); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginate_link_hover_color", __("Link Hover Color", "oxygen") ); ?>
                </div>
                
                <!-- Container Style-->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationContainerStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/size_spacing.svg">
                        <?php _e("Container style", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>
                
                <!-- Link Settings-->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationLinksStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/size_spacing.svg">
                        <?php _e("Links style", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <!-- Link Hover Settings-->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationLinksHoverStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/size_spacing.svg">
                        <?php _e("Links hover style", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>
                
            </div>

            <!-- Pagination Container Style -->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationContainerStyle')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'pagination')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'pagination')"><?php _e("Pagination","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Container Style","oxygen"); ?></div>
                </div>

                <!-- size and spacing -->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationSize')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/size_spacing.svg">
                        <?php _e("Size & Spacing", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>
                <!-- children layout -->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationLayout')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/layout.svg">
                        <?php _e("Children Layout", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>
                <!-- borders -->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationBorders')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/borders.svg">
                        <?php _e("Borders", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>
                <!-- background -->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationBackground')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/background.svg">
                        <?php _e("Background", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

            </div>
            <!-- Pagination Links Style -->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationLinksStyle')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'pagination')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'pagination')"><?php _e("Pagination","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Links Style","oxygen"); ?></div>
                </div>

                <!-- Link Size and Spacing-->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationLinkSize')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/size_spacing.svg">
                        <?php _e("Link Size & Spacing", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                 <!-- Active Link Size and Spacing-->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationLinkActiveSize')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/size_spacing.svg">
                        <?php _e("Active Link Size & Spacing", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <!-- Link Background-->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationLinkBackground')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/background.svg">
                        <?php _e("Link Background", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <!-- Active Link Background-->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationLinkActiveBackground')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/background.svg">
                        <?php _e("Active Link Background", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>
                <!-- Link Borders-->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationLinkBorders')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/borders.svg">
                        <?php _e("Link Borders", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>
                <!-- Active Link Borders-->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationLinkActiveBorders')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/borders.svg">
                        <?php _e("Active Link Borders", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

            </div>
            <!-- Pagination Links Hover Style -->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationLinksHoverStyle')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'pagination')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'pagination')"><?php _e("Pagination","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Links Hover Style","oxygen"); ?></div>
                </div>

                <!-- Link Hover Size and Spacing-->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationLinkHoverSize')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/size_spacing.svg">
                        <?php _e("Link Hover Size & Spacing", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                 <!-- Active Link Hover Size and Spacing-->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationLinkActiveHoverSize')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/size_spacing.svg">
                        <?php _e("Active Link Hover Size & Spacing", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <!-- Link Hover Background-->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationLinkHoverBackground')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/background.svg">
                        <?php _e("Link Hover Background", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <!-- Active Link Hover Background-->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationLinkActiveHoverBackground')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/background.svg">
                        <?php _e("Active Link Hover Background", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>
                <!-- Link Hover Borders-->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationLinkHoverBorders')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/borders.svg">
                        <?php _e("Link Hover Borders", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>
                <!-- Active Link Hover Borders-->
                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('dynamicList', 'paginationLinkActiveHoverBorders')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/borders.svg">
                        <?php _e("Active Link Hover Borders", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

            </div>
            <!-- Pagination Size and Spacing -->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationSize')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'paginationContainerStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'paginationContainerStyle')"><?php _e("Container Style","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Size & Spacing","oxygen"); ?></div>
                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Padding", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>

                            <div class='oxygen-four-sides-measure-box'>

                                <?php $oxygen_toolbar->measure_box('paginate_padding_top','px,%,em',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginate_padding_right','px,%,em',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginate_padding_bottom','px,%,em',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginate_padding_left','px,%,em',true,true,"model,change,keypress"); ?>
                                
                                <div class="oxygen-flex-line-break"></div>
                                
                                <div class="oxygen-apply-all-trigger">
                                    <?php _e("apply all »", "oxygen"); ?>
                                </div>

                            </div>
                            <!-- .oxygen-four-sides-measure-box -->
                        </div>
                        <!-- .oxygen-control -->
                    </div>

                </div>
                <div class="oxygen-control-row">

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Margin", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>

                            <div class='oxygen-four-sides-measure-box'>

                                <?php $oxygen_toolbar->measure_box('paginate_margin_top','',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginate_margin_right','',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginate_margin_bottom','',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginate_margin_left','',true,true,"model,change,keypress"); ?>
                                
                                <div class="oxygen-flex-line-break"></div>
                                
                                <div class="oxygen-apply-all-trigger">
                                    <?php _e("apply all »", "oxygen"); ?>
                                </div>

                            </div>
                            <!-- .oxygen-four-sides-measure-box -->
                        </div>
                        <!-- .oxygen-control -->
                    </div>
                </div>
                
                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Width", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>
                            <?php $oxygen_toolbar->measure_box('paginate_width'); ?>
                        </div>

                    </div>

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginate_min_width", __("Min-width", "oxygen"), 'px,%,em,vw,vh'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginate_max_width", __("Max-width", "oxygen"), 'px,%,em,vw,vh'); ?>

                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'
                        ng-hide="isActiveName('ct_column')&&iframeScope.isEditing('media')">
                        <label class='oxygen-control-label'><?php _e("Height", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>

                            <?php $oxygen_toolbar->measure_box('paginate_height'); ?>

                        </div>
                    </div>

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginate_min_height", __("Min-height", "oxygen"), 'px,%,em,vw,vh'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginate_max_height", __("Max-height", "oxygen"), 'px,%,em,vw,vh'); ?>
                </div>
            </div>
            
            <!-- Pagination Child layout-->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationLayout')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'paginationContainerStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'paginationContainerStyle')"><?php _e("Container Style","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Children Layout","oxygen"); ?></div>
                </div>

                <?php
                    // child elements layout
                    CT_Component::component_params(array(
                        array(
                            "type"          => "flex-layout",
                            "heading"       => __("Layout Child Elements", "oxygen"),
                            "param_name"    => "paginate_flex_direction",
                        )
                    ),
                    $this->options['tag']);
                ?>

                <div class='oxygen-control-row' ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['paginate_flex_direction'] !== 'column'">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Links Alignment","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>

                                <?php $oxygen_toolbar->button_list_button('paginate_alignment','flex-start'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginate_alignment','center'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginate_alignment','flex-end'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginate_alignment','space-between'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginate_alignment','space-around'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginate_alignment','space-evenly'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pagination Borders-->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationBorders')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'paginationContainerStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'paginationContainerStyle')"><?php _e("Container Style","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Borders","oxygen"); ?></div>
                </div>

                <!-- border side chooser -->
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Currently Editing","oxygen"); ?></label>
                        <div class='oxygen-control'>

                            <div class="oxygen-select oxygen-select-box-wrapper">
                                <div class="oxygen-select-box"
                                    ng-class="{'oxygen-option-default':currentBorder=='all'}">
                                    <div class="oxygen-select-box-current">{{currentBorder}}</div>
                                    <div class="oxygen-select-box-dropdown"></div>
                                </div>
                                <div class="oxygen-select-box-options">
                                    
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='all'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='all'}">
                                        <?php _e("all borders", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='top'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='top'}">
                                        <?php _e("top", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='right'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='right'}">
                                        <?php _e("right", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='bottom'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='bottom'}">
                                        <?php _e("bottom", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='left'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='left'}">
                                        <?php _e("left", "component-theme"); ?>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- color and size -->
                <div class='oxygen-control-row'>

                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginate_border_all_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='all'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginate_border_top_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='top'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginate_border_left_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='left'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginate_border_bottom_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='bottom'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginate_border_right_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='right'"); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginate_border_'+currentBorder+'_width", __("Width", "oxygen"), 'px,em'); ?>

                </div>

                <!-- border style -->
                <div class='oxygen-control-row'>

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Style","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>

                                <?php $oxygen_toolbar->button_list_button("paginate_border_'+currentBorder+'_style",'none'); ?>
                                <?php $oxygen_toolbar->button_list_button("paginate_border_'+currentBorder+'_style",'solid'); ?>
                                <?php $oxygen_toolbar->button_list_button("paginate_border_'+currentBorder+'_style",'dashed'); ?>
                                <?php $oxygen_toolbar->button_list_button("paginate_border_'+currentBorder+'_style",'dotted'); ?>

                            </div>
                        </div>
                    </div>

                </div>

                <div class='oxygen-control-row' style='margin-bottom: 20px;'>
                    <a href='#' id='oxygen-control-borders-unset-button'
                        ng-click="iframeScope.unsetAllBorders('', ['paginate_border_all_color', 'paginate_border_top_color', 'paginate_border_left_color', 'paginate_border_bottom_color', 'paginate_border_right_color', 'paginate_border_all_width', 'paginate_border_all_width-unit', 'paginate_border_top_width', 'paginate_border_top_width-unit', 'paginate_border_left_width', 'paginate_border_left_width-unit', 'paginate_border_bottom_width', 'paginate_border_bottom_width-unit', 'paginate_border_right_width', 'paginate_border_right_width-unit', 'paginate_border_all_style', 'paginate_border_top_style', 'paginate_border_left_style', 'paginate_border_bottom_style', 'paginate_border_right_style'])">
                        <?php _e("unset all borders","oxygen"); ?></a>
                </div>

                <div class='oxygen-control-separator'></div>

                <!-- border radius -->
                <div class='oxygen-control-row'
                    ng-show="!editIndividualRadii">

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Border Radius","oxygen"); ?></label>

                        <div class="oxygen-control">
                            <?php $oxygen_toolbar->measure_box("paginate_border_radius", 'px,%,em'); ?>
                        </div>
                    
                        <a href='#' id='oxygen-control-borders-radius-individual'
                            ng-click="editIndividualRadii=true">
                            <?php _e("edit individual radius", "oxygen"); ?> &raquo;</a>
                    </div>

                </div>

                <!-- border radius individually -->
                <div class='oxygen-control-row'
                    ng-show="editIndividualRadii">

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginate_border_top_left_radius", __("Top Left"), 'px,%,em'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginate_border_top_right_radius", __("Top Right"), 'px,%,em'); ?>

                </div>

                <div class='oxygen-control-row'
                    ng-show="editIndividualRadii">

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Bottom Left","oxygen"); ?></label>
                        
                        <div class="oxygen-control">
                            <?php $oxygen_toolbar->measure_box("paginate_border_bottom_left_radius", 'px,%,em'); ?>
                        </div>

                        <a href='#' id='oxygen-control-borders-radius-individual'
                            ng-click="editIndividualRadii=false">
                            <?php _e("edit all radii", "oxygen"); ?> &raquo;</a>
                    </div>

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginate_border_bottom_right_radius", __("Bottom Right"), 'px,%,em'); ?>

                </div>
            </div>

            <!-- Pagination Background-->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationBackground')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'paginationContainerStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'paginationContainerStyle')"><?php _e("Container Style","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Background","oxygen"); ?></div>
                </div>

                <div class='oxygen-control-row'>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginate_background_color", __("Background Color", "oxygen")); ?>
                </div>
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Background Image","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class="oxygen-file-input">
                                <input type="text" spellcheck="false" 
                                    ng-change="iframeScope.setOptionModel('paginate_background_image', iframeScope.component.options[iframeScope.component.active.id]['model']['paginate_background_image'])" 
                                    ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'paginate_background_image')}" 
                                    ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['paginate_background_image']" 
                                    ng-model-options="{ debounce: 10 }" 
                                    class="ng-valid oxygen-option-default ng-dirty ng-valid-parse ng-touched">

                                <div class="oxygen-file-input-browse" 
                                    data-mediatitle="Select Image" 
                                    data-mediabutton="Select Image" 
                                    data-mediaproperty="paginate_background_image" 
                                    data-mediatype="mediaUrl"><?php _e("browse","oxygen"); ?></div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- background-size -->
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper' id='oxygen-control-layout-display'>
                        <label class='oxygen-control-label'><?php _e("Background Size", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>

                                <?php $oxygen_toolbar->button_list_button('paginate_background_size','auto'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginate_background_size','cover'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginate_background_size','contain'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginate_background_size','manual'); ?>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="oxygen-control-row" ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['paginate_background_size'] == 'manual'">
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginate_background_size_width", __("Width", "oxygen"), 'px,%,em'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginate_background_size_height", __("Height", "oxygen"), 'px,%,em'); ?>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper' id='oxygen-control-layout-display'>
                        <label class='oxygen-control-label'><?php _e("Background Repeat", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('paginate_background_repeat','no-repeat'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginate_background_repeat','repeat'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginate_background_repeat','repeat-x'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginate_background_repeat','repeat-y'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination Link Size and Spacing-->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationLinkSize')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'paginationLinksStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'paginationLinksStyle')"><?php _e("Links Style","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Link Size & Spacing","oxygen"); ?></div>
                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Padding", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>

                            <div class='oxygen-four-sides-measure-box'>

                                <?php $oxygen_toolbar->measure_box('paginatelink_padding_top','px,%,em',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelink_padding_right','px,%,em',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelink_padding_bottom','px,%,em',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelink_padding_left','px,%,em',true,true,"model,change,keypress"); ?>
                                
                                <div class="oxygen-flex-line-break"></div>
                                
                                <div class="oxygen-apply-all-trigger">
                                    <?php _e("apply all »", "oxygen"); ?>
                                </div>

                            </div>
                            <!-- .oxygen-four-sides-measure-box -->
                        </div>
                        <!-- .oxygen-control -->
                    </div>

                </div>
                <div class="oxygen-control-row">

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Margin", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>

                            <div class='oxygen-four-sides-measure-box'>

                                <?php $oxygen_toolbar->measure_box('paginatelink_margin_top','',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelink_margin_right','',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelink_margin_bottom','',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelink_margin_left','',true,true,"model,change,keypress"); ?>
                                
                                <div class="oxygen-flex-line-break"></div>

                                <div class="oxygen-apply-all-trigger">
                                    <?php _e("apply all »", "oxygen"); ?>
                                </div>

                            </div>
                            <!-- .oxygen-four-sides-measure-box -->
                        </div>
                        <!-- .oxygen-control -->
                    </div>
                </div>
                
                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Width", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>
                            <?php $oxygen_toolbar->measure_box('paginatelink_width'); ?>
                        </div>

                    </div>

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelink_min_width", __("Min-width", "oxygen"), 'px,%,em,vw,vh'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelink_max_width", __("Max-width", "oxygen"), 'px,%,em,vw,vh'); ?>

                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'
                        ng-hide="isActiveName('ct_column')&&iframeScope.isEditing('media')">
                        <label class='oxygen-control-label'><?php _e("Height", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>

                            <?php $oxygen_toolbar->measure_box('paginatelink_height'); ?>

                        </div>
                    </div>

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelink_min_height", __("Min-height", "oxygen"), 'px,%,em,vw,vh'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelink_max_height", __("Max-height", "oxygen"), 'px,%,em,vw,vh'); ?>
                </div>
            </div>
            <!-- Pagination Active Link Size and Spacing-->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationLinkActiveSize')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'paginationLinksStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'paginationLinksStyle')"><?php _e("Links Style","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Active Link Size & Spacing","oxygen"); ?></div>
                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Padding", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>

                            <div class='oxygen-four-sides-measure-box'>

                                <?php $oxygen_toolbar->measure_box('paginatelinkactive_padding_top','px,%,em',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkactive_padding_right','px,%,em',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkactive_padding_bottom','px,%,em',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkactive_padding_left','px,%,em',true,true,"model,change,keypress"); ?>

                                <div class="oxygen-flex-line-break"></div>

                                <div class="oxygen-apply-all-trigger">
                                    <?php _e("apply all »", "oxygen"); ?>
                                </div>

                            </div>
                            <!-- .oxygen-four-sides-measure-box -->
                        </div>
                        <!-- .oxygen-control -->
                    </div>

                </div>
                <div class="oxygen-control-row">

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Margin", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>

                            <div class='oxygen-four-sides-measure-box'>

                                <?php $oxygen_toolbar->measure_box('paginatelinkactive_margin_top','Top',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkactive_margin_right','',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkactive_margin_bottom','',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkactive_margin_left','',true,true,"model,change,keypress"); ?>

                                <div class="oxygen-flex-line-break"></div>

                                <div class="oxygen-apply-all-trigger">
                                    <?php _e("apply all »", "oxygen"); ?>
                                </div>

                            </div>
                            <!-- .oxygen-four-sides-measure-box -->
                        </div>
                        <!-- .oxygen-control -->
                    </div>
                </div>
                
                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Width", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>
                            <?php $oxygen_toolbar->measure_box('paginatelinkactive_width'); ?>
                        </div>

                    </div>

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactive_min_width", __("Min-width", "oxygen"), 'px,%,em,vw,vh'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactive_max_width", __("Max-width", "oxygen"), 'px,%,em,vw,vh'); ?>

                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'
                        ng-hide="isActiveName('ct_column')&&iframeScope.isEditing('media')">
                        <label class='oxygen-control-label'><?php _e("Height", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>

                            <?php $oxygen_toolbar->measure_box('paginatelinkactive_height'); ?>

                        </div>
                    </div>

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactive_min_height", __("Min-height", "oxygen"), 'px,%,em,vw,vh'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactive_max_height", __("Max-height", "oxygen"), 'px,%,em,vw,vh'); ?>
                </div>
            </div>

            <!-- Pagination Link Borders-->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationLinkBorders')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'paginationLinksStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'paginationLinksStyle')"><?php _e("Links Style","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Link Borders","oxygen"); ?></div>
                </div>

                <!-- border side chooser -->
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Currently Editing","oxygen"); ?></label>
                        <div class='oxygen-control'>

                            <div class="oxygen-select oxygen-select-box-wrapper">
                                <div class="oxygen-select-box"
                                    ng-class="{'oxygen-option-default':currentBorder=='all'}">
                                    <div class="oxygen-select-box-current">{{currentBorder}}</div>
                                    <div class="oxygen-select-box-dropdown"></div>
                                </div>
                                <div class="oxygen-select-box-options">
                                    
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='all'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='all'}">
                                        <?php _e("all borders", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='top'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='top'}">
                                        <?php _e("top", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='right'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='right'}">
                                        <?php _e("right", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='bottom'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='bottom'}">
                                        <?php _e("bottom", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='left'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='left'}">
                                        <?php _e("left", "component-theme"); ?>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- color and size -->
                <div class='oxygen-control-row'>

                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelink_border_all_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='all'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelink_border_top_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='top'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelink_border_left_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='left'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelink_border_bottom_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='bottom'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelink_border_right_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='right'"); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelink_border_'+currentBorder+'_width", __("Width", "oxygen"), 'px,em'); ?>

                </div>

                <!-- border style -->
                <div class='oxygen-control-row'>

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Style","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>

                                <?php $oxygen_toolbar->button_list_button("paginatelink_border_'+currentBorder+'_style",'none'); ?>
                                <?php $oxygen_toolbar->button_list_button("paginatelink_border_'+currentBorder+'_style",'solid'); ?>
                                <?php $oxygen_toolbar->button_list_button("paginatelink_border_'+currentBorder+'_style",'dashed'); ?>
                                <?php $oxygen_toolbar->button_list_button("paginatelink_border_'+currentBorder+'_style",'dotted'); ?>

                            </div>
                        </div>
                    </div>

                </div>

                <div class='oxygen-control-row' style='margin-bottom: 20px;'>
                    <a href='#' id='oxygen-control-borders-unset-button'
                        ng-click="iframeScope.unsetAllBorders('', ['paginatelink_border_all_color', 'paginatelink_border_top_color', 'paginatelink_border_left_color', 'paginatelink_border_bottom_color', 'paginatelink_border_right_color', 'paginatelink_border_all_width', 'paginatelink_border_all_width-unit', 'paginatelink_border_top_width', 'paginatelink_border_top_width-unit', 'paginatelink_border_left_width', 'paginatelink_border_left_width-unit', 'paginatelink_border_bottom_width', 'paginatelink_border_bottom_width-unit', 'paginatelink_border_right_width', 'paginatelink_border_right_width-unit', 'paginatelink_border_all_style', 'paginatelink_border_top_style', 'paginatelink_border_left_style', 'paginatelink_border_bottom_style', 'paginatelink_border_right_style'])">
                        <?php _e("unset all borders","oxygen"); ?></a>
                </div>

                <div class='oxygen-control-separator'></div>

                <!-- border radius -->
                <div class='oxygen-control-row'
                    ng-show="!editIndividualRadii">

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Border Radius","oxygen"); ?></label>

                        <div class="oxygen-control">
                            <?php $oxygen_toolbar->measure_box("paginatelink_border_radius", 'px,%,em'); ?>
                        </div>
                    
                        <a href='#' id='oxygen-control-borders-radius-individual'
                            ng-click="editIndividualRadii=true">
                            <?php _e("edit individual radius", "oxygen"); ?> &raquo;</a>
                    </div>

                </div>

                <!-- border radius individually -->
                <div class='oxygen-control-row'
                    ng-show="editIndividualRadii">

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelink_border_top_left_radius", __("Top Left"), 'px,%,em'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelink_border_top_right_radius", __("Top Right"), 'px,%,em'); ?>

                </div>

                <div class='oxygen-control-row'
                    ng-show="editIndividualRadii">

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Bottom Left","oxygen"); ?></label>
                        
                        <div class="oxygen-control">
                            <?php $oxygen_toolbar->measure_box("paginatelink_border_bottom_left_radius", 'px,%,em'); ?>
                        </div>

                        <a href='#' id='oxygen-control-borders-radius-individual'
                            ng-click="editIndividualRadii=false">
                            <?php _e("edit all radii", "oxygen"); ?> &raquo;</a>
                    </div>

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelink_border_bottom_right_radius", __("Bottom Right"), 'px,%,em'); ?>

                </div>
            </div>

            <!-- Pagination Active Link Borders-->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationLinkActiveBorders')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'paginationLinksStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'paginationLinksStyle')"><?php _e("Links Style","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Active Link Borders","oxygen"); ?></div>
                </div>

                <!-- border side chooser -->
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Currently Editing","oxygen"); ?></label>
                        <div class='oxygen-control'>

                            <div class="oxygen-select oxygen-select-box-wrapper">
                                <div class="oxygen-select-box"
                                    ng-class="{'oxygen-option-default':currentBorder=='all'}">
                                    <div class="oxygen-select-box-current">{{currentBorder}}</div>
                                    <div class="oxygen-select-box-dropdown"></div>
                                </div>
                                <div class="oxygen-select-box-options">
                                    
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='all'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='all'}">
                                        <?php _e("all borders", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='top'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='top'}">
                                        <?php _e("top", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='right'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='right'}">
                                        <?php _e("right", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='bottom'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='bottom'}">
                                        <?php _e("bottom", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='left'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='left'}">
                                        <?php _e("left", "component-theme"); ?>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- color and size -->
                <div class='oxygen-control-row'>

                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkactive_border_all_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='all'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkactive_border_top_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='top'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkactive_border_left_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='left'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkactive_border_bottom_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='bottom'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkactive_border_right_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='right'"); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactive_border_'+currentBorder+'_width", __("Width", "oxygen"), 'px,em'); ?>

                </div>

                <!-- border style -->
                <div class='oxygen-control-row'>

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Style","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>

                                <?php $oxygen_toolbar->button_list_button("paginatelinkactive_border_'+currentBorder+'_style",'none'); ?>
                                <?php $oxygen_toolbar->button_list_button("paginatelinkactive_border_'+currentBorder+'_style",'solid'); ?>
                                <?php $oxygen_toolbar->button_list_button("paginatelinkactive_border_'+currentBorder+'_style",'dashed'); ?>
                                <?php $oxygen_toolbar->button_list_button("paginatelinkactive_border_'+currentBorder+'_style",'dotted'); ?>

                            </div>
                        </div>
                    </div>

                </div>

                <div class='oxygen-control-row' style='margin-bottom: 20px;'>
                    <a href='#' id='oxygen-control-borders-unset-button'
                        ng-click="iframeScope.unsetAllBorders('', ['paginatelinkactive_border_all_color', 'paginatelinkactive_border_top_color', 'paginatelinkactive_border_left_color', 'paginatelinkactive_border_bottom_color', 'paginatelinkactive_border_right_color', 'paginatelinkactive_border_all_width', 'paginatelinkactive_border_all_width-unit', 'paginatelinkactive_border_top_width', 'paginatelinkactive_border_top_width-unit', 'paginatelinkactive_border_left_width', 'paginatelinkactive_border_left_width-unit', 'paginatelinkactive_border_bottom_width', 'paginatelinkactive_border_bottom_width-unit', 'paginatelinkactive_border_right_width', 'paginatelinkactive_border_right_width-unit', 'paginatelinkactive_border_all_style', 'paginatelinkactive_border_top_style', 'paginatelinkactive_border_left_style', 'paginatelinkactive_border_bottom_style', 'paginatelinkactive_border_right_style'])">
                        <?php _e("unset all borders","oxygen"); ?></a>
                </div>

                <div class='oxygen-control-separator'></div>

                <!-- border radius -->
                <div class='oxygen-control-row'
                    ng-show="!editIndividualRadii">

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Border Radius","oxygen"); ?></label>

                        <div class="oxygen-control">
                            <?php $oxygen_toolbar->measure_box("paginatelinkactive_border_radius", 'px,%,em'); ?>
                        </div>
                    
                        <a href='#' id='oxygen-control-borders-radius-individual'
                            ng-click="editIndividualRadii=true">
                            <?php _e("edit individual radius", "oxygen"); ?> &raquo;</a>
                    </div>

                </div>

                <!-- border radius individually -->
                <div class='oxygen-control-row'
                    ng-show="editIndividualRadii">

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactive_border_top_left_radius", __("Top Left"), 'px,%,em'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactive_border_top_right_radius", __("Top Right"), 'px,%,em'); ?>

                </div>

                <div class='oxygen-control-row'
                    ng-show="editIndividualRadii">

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Bottom Left","oxygen"); ?></label>
                        
                        <div class="oxygen-control">
                            <?php $oxygen_toolbar->measure_box("paginatelinkactive_border_bottom_left_radius", 'px,%,em'); ?>
                        </div>

                        <a href='#' id='oxygen-control-borders-radius-individual'
                            ng-click="editIndividualRadii=false">
                            <?php _e("edit all radii", "oxygen"); ?> &raquo;</a>
                    </div>

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactive_border_bottom_right_radius", __("Bottom Right"), 'px,%,em'); ?>

                </div>
            </div>

            <!-- Pagination Link Background-->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationLinkBackground')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'paginationLinksStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'paginationLinksStyle')"><?php _e("Links Style","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Link Background","oxygen"); ?></div>
                </div>
                <div class='oxygen-control-row'>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelink_background_color", __("Background Color", "oxygen")); ?>
                </div>
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Background Image","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class="oxygen-file-input">
                                <input type="text" spellcheck="false" 
                                    ng-change="iframeScope.setOptionModel('paginatelink_background_image', iframeScope.component.options[iframeScope.component.active.id]['model']['paginatelink_background_image'])" 
                                    ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'paginatelink_background_image')}" 
                                    ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['paginatelink_background_image']" 
                                    ng-model-options="{ debounce: 10 }" 
                                    class="ng-valid oxygen-option-default ng-dirty ng-valid-parse ng-touched">

                                <div class="oxygen-file-input-browse" 
                                    data-mediatitle="Select Image" 
                                    data-mediabutton="Select Image" 
                                    data-mediaproperty="paginatelink_background_image" 
                                    data-mediatype="mediaUrl"><?php _e("browse","oxygen"); ?></div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- background-size -->
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper' id='oxygen-control-layout-display'>
                        <label class='oxygen-control-label'><?php _e("Background Size", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>

                                <?php $oxygen_toolbar->button_list_button('paginatelink_background_size','auto'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelink_background_size','cover'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelink_background_size','contain'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelink_background_size','manual'); ?>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="oxygen-control-row" ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['paginatelink_background_size'] == 'manual'">
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelink_background_size_width", __("Width", "oxygen"), 'px,%,em'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelink_background_size_height", __("Height", "oxygen"), 'px,%,em'); ?>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper' id='oxygen-control-layout-display'>
                        <label class='oxygen-control-label'><?php _e("Background Repeat", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('paginatelink_background_repeat','no-repeat'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelink_background_repeat','repeat'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelink_background_repeat','repeat-x'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelink_background_repeat','repeat-y'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pagination Active Link Background-->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationLinkActiveBackground')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'paginationLinksStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'paginationLinksStyle')"><?php _e("Links Style","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Active Link Background","oxygen"); ?></div>
                </div>
                <div class='oxygen-control-row'>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkactive_background_color", __("Background Color", "oxygen")); ?>
                </div>
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Background Image","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class="oxygen-file-input">
                                <input type="text" spellcheck="false" 
                                    ng-change="iframeScope.setOptionModel('paginatelinkactive_background_image', iframeScope.component.options[iframeScope.component.active.id]['model']['paginatelinkactive_background_image'])" 
                                    ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'paginatelinkactive_background_image')}" 
                                    ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['paginatelinkactive_background_image']" 
                                    ng-model-options="{ debounce: 10 }" 
                                    class="ng-valid oxygen-option-default ng-dirty ng-valid-parse ng-touched">

                                <div class="oxygen-file-input-browse" 
                                    data-mediatitle="Select Image" 
                                    data-mediabutton="Select Image" 
                                    data-mediaproperty="paginatelinkactive_background_image" 
                                    data-mediatype="mediaUrl"><?php _e("browse","oxygen"); ?></div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- background-size -->
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper' id='oxygen-control-layout-display'>
                        <label class='oxygen-control-label'><?php _e("Background Size", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>

                                <?php $oxygen_toolbar->button_list_button('paginatelinkactive_background_size','auto'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkactive_background_size','cover'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkactive_background_size','contain'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkactive_background_size','manual'); ?>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="oxygen-control-row" ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['paginatelinkactive_background_size'] == 'manual'">
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactive_background_size_width", __("Width", "oxygen"), 'px,%,em'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactive_background_size_height", __("Height", "oxygen"), 'px,%,em'); ?>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper' id='oxygen-control-layout-display'>
                        <label class='oxygen-control-label'><?php _e("Background Repeat", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkactive_background_repeat','no-repeat'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkactive_background_repeat','repeat'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkactive_background_repeat','repeat-x'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkactive_background_repeat','repeat-y'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination Link Hover Size and Spacing-->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationLinkHoverSize')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'paginationLinksHoverStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'paginationLinksHoverStyle')"><?php _e("Links Hover Style","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Link Hover Size & Spacing","oxygen"); ?></div>
                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Padding", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>

                            <div class='oxygen-four-sides-measure-box'>

                                <?php $oxygen_toolbar->measure_box('paginatelinkhover_padding_top','px,%,em',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkhover_padding_right','px,%,em',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkhover_padding_bottom','px,%,em',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkhover_padding_left','px,%,em',true,true,"model,change,keypress"); ?>

                                <div class="oxygen-flex-line-break"></div>
                                
                                <div class="oxygen-apply-all-trigger">
                                    <?php _e("apply all »", "oxygen"); ?>
                                </div>

                            </div>
                            <!-- .oxygen-four-sides-measure-box -->
                        </div>
                        <!-- .oxygen-control -->
                    </div>

                </div>
                <div class="oxygen-control-row">

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Margin", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>

                            <div class='oxygen-four-sides-measure-box'>

                                <?php $oxygen_toolbar->measure_box('paginatelinkhover_margin_top','',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkhover_margin_right','',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkhover_margin_bottom','',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkhover_margin_left','',true,true,"model,change,keypress"); ?>

                                <div class="oxygen-flex-line-break"></div>
                                
                                <div class="oxygen-apply-all-trigger">
                                    <?php _e("apply all »", "oxygen"); ?>
                                </div>

                            </div>
                            <!-- .oxygen-four-sides-measure-box -->
                        </div>
                        <!-- .oxygen-control -->
                    </div>
                </div>
                
                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Width", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>
                            <?php $oxygen_toolbar->measure_box('paginatelinkhover_width'); ?>
                        </div>

                    </div>

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkhover_min_width", __("Min-width", "oxygen"), 'px,%,em,vw,vh'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkhover_max_width", __("Max-width", "oxygen"), 'px,%,em,vw,vh'); ?>

                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'
                        ng-hide="isActiveName('ct_column')&&iframeScope.isEditing('media')">
                        <label class='oxygen-control-label'><?php _e("Height", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>

                            <?php $oxygen_toolbar->measure_box('paginatelinkhover_height'); ?>

                        </div>
                    </div>

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkhover_min_height", __("Min-height", "oxygen"), 'px,%,em,vw,vh'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkhover_max_height", __("Max-height", "oxygen"), 'px,%,em,vw,vh'); ?>
                </div>
            </div>
            <!-- Pagination Active Link Hover Size and Spacing-->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationLinkActiveHoverSize')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'paginationLinksHoverStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'paginationLinksHoverStyle')"><?php _e("Links Hover Style","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Active Link Hover Size & Spacing","oxygen"); ?></div>
                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Padding", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>

                            <div class='oxygen-four-sides-measure-box'>

                                <?php $oxygen_toolbar->measure_box('paginatelinkactivehover_padding_top','px,%,em',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkactivehover_padding_right','px,%,em',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkactivehover_padding_bottom','px,%,em',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkactivehover_padding_left','px,%,em',true,true,"model,change,keypress"); ?>

                                <div class="oxygen-flex-line-break"></div>
                                
                                <div class="oxygen-apply-all-trigger">
                                    <?php _e("apply all »", "oxygen"); ?>
                                </div>

                            </div>
                            <!-- .oxygen-four-sides-measure-box -->
                        </div>
                        <!-- .oxygen-control -->
                    </div>

                </div>
                <div class="oxygen-control-row">

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Margin", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>

                            <div class='oxygen-four-sides-measure-box'>

                                <?php $oxygen_toolbar->measure_box('paginatelinkactivehover_margin_top','',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkactivehover_margin_right','',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkactivehover_margin_bottom','',true,true,"model,change,keypress"); ?>
                                <?php $oxygen_toolbar->measure_box('paginatelinkactivehover_margin_left','',true,true,"model,change,keypress"); ?>

                                <div class="oxygen-flex-line-break"></div>

                                <div class="oxygen-apply-all-trigger">
                                    <?php _e("apply all »", "oxygen"); ?>
                                </div>

                            </div>
                            <!-- .oxygen-four-sides-measure-box -->
                        </div>
                        <!-- .oxygen-control -->
                    </div>
                </div>
                
                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Width", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>
                            <?php $oxygen_toolbar->measure_box('paginatelinkactivehover_width'); ?>
                        </div>

                    </div>

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactivehover_min_width", __("Min-width", "oxygen"), 'px,%,em,vw,vh'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactivehover_max_width", __("Max-width", "oxygen"), 'px,%,em,vw,vh'); ?>

                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'
                        ng-hide="isActiveName('ct_column')&&iframeScope.isEditing('media')">
                        <label class='oxygen-control-label'><?php _e("Height", "component-theme"); ?></label>
                        
                        <div class='oxygen-control'>

                            <?php $oxygen_toolbar->measure_box('paginatelinkactivehover_height'); ?>

                        </div>
                    </div>

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactivehover_min_height", __("Min-height", "oxygen"), 'px,%,em,vw,vh'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactivehover_max_height", __("Max-height", "oxygen"), 'px,%,em,vw,vh'); ?>
                </div>
            </div>

            <!-- Pagination Link Hover Borders-->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationLinkHoverBorders')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'paginationLinksHoverStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'paginationLinksHoverStyle')"><?php _e("Links Hover Style","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Link Hover Borders","oxygen"); ?></div>
                </div>

                <!-- border side chooser -->
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Currently Editing","oxygen"); ?></label>
                        <div class='oxygen-control'>

                            <div class="oxygen-select oxygen-select-box-wrapper">
                                <div class="oxygen-select-box"
                                    ng-class="{'oxygen-option-default':currentBorder=='all'}">
                                    <div class="oxygen-select-box-current">{{currentBorder}}</div>
                                    <div class="oxygen-select-box-dropdown"></div>
                                </div>
                                <div class="oxygen-select-box-options">
                                    
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='all'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='all'}">
                                        <?php _e("all borders", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='top'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='top'}">
                                        <?php _e("top", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='right'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='right'}">
                                        <?php _e("right", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='bottom'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='bottom'}">
                                        <?php _e("bottom", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='left'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='left'}">
                                        <?php _e("left", "component-theme"); ?>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- color and size -->
                <div class='oxygen-control-row'>

                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkhover_border_all_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='all'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkhover_border_top_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='top'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkhover_border_left_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='left'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkhover_border_bottom_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='bottom'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkhover_border_right_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='right'"); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkhover_border_'+currentBorder+'_width", __("Width", "oxygen"), 'px,em'); ?>

                </div>

                <!-- border style -->
                <div class='oxygen-control-row'>

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Style","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>

                                <?php $oxygen_toolbar->button_list_button("paginatelinkhover_border_'+currentBorder+'_style",'none'); ?>
                                <?php $oxygen_toolbar->button_list_button("paginatelinkhover_border_'+currentBorder+'_style",'solid'); ?>
                                <?php $oxygen_toolbar->button_list_button("paginatelinkhover_border_'+currentBorder+'_style",'dashed'); ?>
                                <?php $oxygen_toolbar->button_list_button("paginatelinkhover_border_'+currentBorder+'_style",'dotted'); ?>

                            </div>
                        </div>
                    </div>

                </div>

                <div class='oxygen-control-row' style='margin-bottom: 20px;'>
                    <a href='#' id='oxygen-control-borders-unset-button'
                        ng-click="iframeScope.unsetAllBorders('', ['paginatelinkhover_border_all_color', 'paginatelinkhover_border_top_color', 'paginatelinkhover_border_left_color', 'paginatelinkhover_border_bottom_color', 'paginatelinkhover_border_right_color', 'paginatelinkhover_border_all_width', 'paginatelinkhover_border_all_width-unit', 'paginatelinkhover_border_top_width', 'paginatelinkhover_border_top_width-unit', 'paginatelinkhover_border_left_width', 'paginatelinkhover_border_left_width-unit', 'paginatelinkhover_border_bottom_width', 'paginatelinkhover_border_bottom_width-unit', 'paginatelinkhover_border_right_width', 'paginatelinkhover_border_right_width-unit', 'paginatelinkhover_border_all_style', 'paginatelinkhover_border_top_style', 'paginatelinkhover_border_left_style', 'paginatelinkhover_border_bottom_style', 'paginatelinkhover_border_right_style'])">
                        <?php _e("unset all borders","oxygen"); ?></a>
                </div>

                <div class='oxygen-control-separator'></div>

                <!-- border radius -->
                <div class='oxygen-control-row'
                    ng-show="!editIndividualRadii">

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Border Radius","oxygen"); ?></label>

                        <div class="oxygen-control">
                            <?php $oxygen_toolbar->measure_box("paginatelinkhover_border_radius", 'px,%,em'); ?>
                        </div>
                    
                        <a href='#' id='oxygen-control-borders-radius-individual'
                            ng-click="editIndividualRadii=true">
                            <?php _e("edit individual radius", "oxygen"); ?> &raquo;</a>
                    </div>

                </div>

                <!-- border radius individually -->
                <div class='oxygen-control-row'
                    ng-show="editIndividualRadii">

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkhover_border_top_left_radius", __("Top Left"), 'px,%,em'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkhover_border_top_right_radius", __("Top Right"), 'px,%,em'); ?>

                </div>

                <div class='oxygen-control-row'
                    ng-show="editIndividualRadii">

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Bottom Left","oxygen"); ?></label>
                        
                        <div class="oxygen-control">
                            <?php $oxygen_toolbar->measure_box("paginatelinkhover_border_bottom_left_radius", 'px,%,em'); ?>
                        </div>

                        <a href='#' id='oxygen-control-borders-radius-individual'
                            ng-click="editIndividualRadii=false">
                            <?php _e("edit all radii", "oxygen"); ?> &raquo;</a>
                    </div>

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkhover_border_bottom_right_radius", __("Bottom Right"), 'px,%,em'); ?>

                </div>
            </div>

            <!-- Pagination Active Link Hover Borders-->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationLinkActiveHoverBorders')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'paginationLinksHoverStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'paginationLinksHoverStyle')"><?php _e("Links Hover Style","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Active Link Hover Borders","oxygen"); ?></div>
                </div>

                <!-- border side chooser -->
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Currently Editing","oxygen"); ?></label>
                        <div class='oxygen-control'>

                            <div class="oxygen-select oxygen-select-box-wrapper">
                                <div class="oxygen-select-box"
                                    ng-class="{'oxygen-option-default':currentBorder=='all'}">
                                    <div class="oxygen-select-box-current">{{currentBorder}}</div>
                                    <div class="oxygen-select-box-dropdown"></div>
                                </div>
                                <div class="oxygen-select-box-options">
                                    
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='all'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='all'}">
                                        <?php _e("all borders", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='top'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='top'}">
                                        <?php _e("top", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='right'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='right'}">
                                        <?php _e("right", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='bottom'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='bottom'}">
                                        <?php _e("bottom", "component-theme"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="currentBorder='left'"
                                        ng-class="{'oxygen-select-box-option-active':currentBorder=='left'}">
                                        <?php _e("left", "component-theme"); ?>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- color and size -->
                <div class='oxygen-control-row'>

                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkactivehover_border_all_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='all'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkactivehover_border_top_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='top'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkactivehover_border_left_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='left'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkactivehover_border_bottom_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='bottom'"); ?>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkactivehover_border_right_color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='right'"); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactivehover_border_'+currentBorder+'_width", __("Width", "oxygen"), 'px,em'); ?>

                </div>

                <!-- border style -->
                <div class='oxygen-control-row'>

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Style","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>

                                <?php $oxygen_toolbar->button_list_button("paginatelinkactivehover_border_'+currentBorder+'_style",'none'); ?>
                                <?php $oxygen_toolbar->button_list_button("paginatelinkactivehover_border_'+currentBorder+'_style",'solid'); ?>
                                <?php $oxygen_toolbar->button_list_button("paginatelinkactivehover_border_'+currentBorder+'_style",'dashed'); ?>
                                <?php $oxygen_toolbar->button_list_button("paginatelinkactivehover_border_'+currentBorder+'_style",'dotted'); ?>

                            </div>
                        </div>
                    </div>

                </div>

                <div class='oxygen-control-row' style='margin-bottom: 20px;'>
                    <a href='#' id='oxygen-control-borders-unset-button'
                        ng-click="iframeScope.unsetAllBorders('', ['paginatelinkactivehover_border_all_color', 'paginatelinkactivehover_border_top_color', 'paginatelinkactivehover_border_left_color', 'paginatelinkactivehover_border_bottom_color', 'paginatelinkactivehover_border_right_color', 'paginatelinkactivehover_border_all_width', 'paginatelinkactivehover_border_all_width-unit', 'paginatelinkactivehover_border_top_width', 'paginatelinkactivehover_border_top_width-unit', 'paginatelinkactivehover_border_left_width', 'paginatelinkactivehover_border_left_width-unit', 'paginatelinkactivehover_border_bottom_width', 'paginatelinkactivehover_border_bottom_width-unit', 'paginatelinkactivehover_border_right_width', 'paginatelinkactivehover_border_right_width-unit', 'paginatelinkactivehover_border_all_style', 'paginatelinkactivehover_border_top_style', 'paginatelinkactivehover_border_left_style', 'paginatelinkactivehover_border_bottom_style', 'paginatelinkactivehover_border_right_style'])">
                        <?php _e("unset all borders","oxygen"); ?></a>
                </div>

                <div class='oxygen-control-separator'></div>

                <!-- border radius -->
                <div class='oxygen-control-row'
                    ng-show="!editIndividualRadii">

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Border Radius","oxygen"); ?></label>
                    
                        <div class="oxygen-control">
                            <?php $oxygen_toolbar->measure_box("paginatelinkactivehover_border_radius", 'px,%,em'); ?>
                        </div>
                    
                        <a href='#' id='oxygen-control-borders-radius-individual'
                            ng-click="editIndividualRadii=true">
                            <?php _e("edit individual radius", "oxygen"); ?> &raquo;</a>
                    </div>

                </div>

                <!-- border radius individually -->
                <div class='oxygen-control-row'
                    ng-show="editIndividualRadii">

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactivehover_border_top_left_radius", __("Top Left"), 'px,%,em'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactivehover_border_top_right_radius", __("Top Right"), 'px,%,em'); ?>

                </div>

                <div class='oxygen-control-row'
                    ng-show="editIndividualRadii">

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Bottom Left","oxygen"); ?></label>
                        
                        <div class="oxygen-control">
                            <?php $oxygen_toolbar->measure_box("paginatelinkactivehover_border_bottom_left_radius", 'px,%,em'); ?>
                        </div>

                        <a href='#' id='oxygen-control-borders-radius-individual'
                            ng-click="editIndividualRadii=false">
                            <?php _e("edit all radii", "oxygen"); ?> &raquo;</a>
                    </div>

                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactivehover_border_bottom_right_radius", __("Bottom Right"), 'px,%,em'); ?>

                </div>
            </div>

            <!-- Pagination Link Hover Background-->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationLinkHoverBackground')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'paginationLinksHoverStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'paginationLinksHoverStyle')"><?php _e("Links Hover Style","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Link Hover Background","oxygen"); ?></div>
                </div>
                <div class='oxygen-control-row'>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkhover_background_color", __("Background Color", "oxygen")); ?>
                </div>
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Background Image","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class="oxygen-file-input">
                                <input type="text" spellcheck="false" 
                                    ng-change="iframeScope.setOptionModel('paginatelinkhover_background_image', iframeScope.component.options[iframeScope.component.active.id]['model']['paginatelinkhover_background_image'])" 
                                    ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'paginatelinkhover_background_image')}" 
                                    ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['paginatelinkhover_background_image']" 
                                    ng-model-options="{ debounce: 10 }" 
                                    class="ng-valid oxygen-option-default ng-dirty ng-valid-parse ng-touched">

                                <div class="oxygen-file-input-browse" 
                                    data-mediatitle="Select Image" 
                                    data-mediabutton="Select Image" 
                                    data-mediaproperty="paginatelinkhover_background_image" 
                                    data-mediatype="mediaUrl"><?php _e("browse","oxygen"); ?></div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- background-size -->
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper' id='oxygen-control-layout-display'>
                        <label class='oxygen-control-label'><?php _e("Background Size", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>

                                <?php $oxygen_toolbar->button_list_button('paginatelinkhover_background_size','auto'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkhover_background_size','cover'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkhover_background_size','contain'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkhover_background_size','manual'); ?>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="oxygen-control-row" ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['paginatelinkhover_background_size'] == 'manual'">
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkhover_background_size_width", __("Width", "oxygen"), 'px,%,em'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkhover_background_size_height", __("Height", "oxygen"), 'px,%,em'); ?>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper' id='oxygen-control-layout-display'>
                        <label class='oxygen-control-label'><?php _e("Background Repeat", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkhover_background_repeat','no-repeat'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkhover_background_repeat','repeat'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkhover_background_repeat','repeat-x'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkhover_background_repeat','repeat-y'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pagination Active Link Hover Background-->
            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'paginationLinkActiveHoverBackground')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'paginationLinksHoverStyle')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'paginationLinksHoverStyle')"><?php _e("Links Hover Style","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Active Link Hover Background","oxygen"); ?></div>
                </div>
                <div class='oxygen-control-row'>
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("paginatelinkactivehover_background_color", __("Background Color", "oxygen")); ?>
                </div>
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Background Image","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class="oxygen-file-input">
                                <input type="text" spellcheck="false" 
                                    ng-change="iframeScope.setOptionModel('paginatelinkactivehover_background_image', iframeScope.component.options[iframeScope.component.active.id]['model']['paginatelinkactivehover_background_image'])" 
                                    ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'paginatelinkactivehover_background_image')}" 
                                    ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['paginatelinkactivehover_background_image']" 
                                    ng-model-options="{ debounce: 10 }" 
                                    class="ng-valid oxygen-option-default ng-dirty ng-valid-parse ng-touched">

                                <div class="oxygen-file-input-browse" 
                                    data-mediatitle="Select Image" 
                                    data-mediabutton="Select Image" 
                                    data-mediaproperty="paginatelinkactivehover_background_image" 
                                    data-mediatype="mediaUrl"><?php _e("browse","oxygen"); ?></div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- background-size -->
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper' id='oxygen-control-layout-display'>
                        <label class='oxygen-control-label'><?php _e("Background Size", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>

                                <?php $oxygen_toolbar->button_list_button('paginatelinkactivehover_background_size','auto'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkactivehover_background_size','cover'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkactivehover_background_size','contain'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkactivehover_background_size','manual'); ?>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="oxygen-control-row" ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['paginatelinkactivehover_background_size'] == 'manual'">
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactivehover_background_size_width", __("Width", "oxygen"), 'px,%,em'); ?>
                    <?php $oxygen_toolbar->measure_box_with_wrapper("paginatelinkactivehover_background_size_height", __("Height", "oxygen"), 'px,%,em'); ?>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper' id='oxygen-control-layout-display'>
                        <label class='oxygen-control-label'><?php _e("Background Repeat", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkactivehover_background_repeat','no-repeat'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkactivehover_background_repeat','repeat'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkactivehover_background_repeat','repeat-x'); ?>
                                <?php $oxygen_toolbar->button_list_button('paginatelinkactivehover_background_repeat','repeat-y'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div  class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList','query')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="tabs.dynamicList=[]">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="tabs.dynamicList=[]"><?php _e("Repeater","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Query","oxygen"); ?></div>
                </div>

                <div class='oxygen-control-row'
                    ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['use_acf_repeater']!='true'&&iframeScope.component.options[iframeScope.component.active.id]['model']['use_metabox_clonable_group']!='true'">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("WP Query","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('wp_query', 'default'); ?>
                                <?php $oxygen_toolbar->button_list_button('wp_query', 'custom'); ?>
                                <?php $oxygen_toolbar->button_list_button('wp_query', 'manual'); ?>
                                <?php $oxygen_toolbar->button_list_button('wp_query', 'advanced'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                    include_once(CT_FW_PATH."/includes/advanced-query.php"); 
                    Oxy_VSB_Advanced_Query::controls();
                ?>
               

                <div class='oxygen-control-row'
                    ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query']=='manual' && iframeScope.component.options[iframeScope.component.active.id]['model']['use_acf_repeater']!='true' && iframeScope.component.options[iframeScope.component.active.id]['model']['use_metabox_clonable_group']!='true'">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Query Params","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class="oxygen-textarea">
                                <textarea class="oxygen-textarea-textarea"
                                    <?php $this->ng_attributes('query_args'); ?>></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query']=='custom' && iframeScope.component.options[iframeScope.component.active.id]['model']['use_acf_repeater']!='true' && iframeScope.component.options[iframeScope.component.active.id]['model']['use_metabox_clonable_group']!='true'">
                    
                    <div class="oxygen-sidebar-advanced-subtab" 
                        ng-click="switchTab('dynamicList', 'postType')">
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                            <?php _e("Post Type", "oxygen"); ?>
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                    </div>

                    <div class="oxygen-sidebar-advanced-subtab" 
                        ng-click="switchTab('dynamicList', 'filtering')">
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                            <?php _e("Filtering", "oxygen"); ?>
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                    </div>

                    <div class="oxygen-sidebar-advanced-subtab" 
                        ng-click="switchTab('dynamicList', 'order')">
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                            <?php _e("Order", "oxygen"); ?>
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                    </div>

                    <div class="oxygen-sidebar-advanced-subtab" 
                        ng-click="switchTab('dynamicList', 'count')">
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                            <?php _e("Count", "oxygen"); ?>
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                    </div>

                </div>

                
            <?php
                if(class_exists('ACF')) { // acf repeater fields tab
            ?>

                <div class="oxygen-control-row"
                    ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['use_metabox_clonable_group']!='true'">
                    <div class='oxygen-control-wrapper' style="flex-grow: unset; flex-basis: 120px;">
                        <label class="oxygen-checkbox">
                            <input type="checkbox"
                                ng-true-value="'true'" 
                                ng-false-value="'false'"
                                ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['use_acf_repeater']"
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_dynamic_list','use_acf_repeater')">
                            <div class='oxygen-checkbox-checkbox'
                                ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('use_acf_repeater')=='true'}">
                                <?php _e("Use ACF Repeater","oxygen"); ?>
                            </div>
                        </label>
                    </div>
                </div>
            
                <div class='oxygen-control-row'
                    ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['use_acf_repeater']=='true'">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("ACF Repeater Field","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <label 
                                ng-repeat="(repeater, repeaterObj) in iframeScope.acfRepeaters"
                                ng-if="(iframeScope.parentRepeaterHasACF !== false && repeaterObj.parent === iframeScope.parentRepeaterHasACF) || (iframeScope.parentRepeaterHasACF === false && repeaterObj.parent === false)"
                                class="oxygen-button-list-button" ng-class="{'oxygen-button-list-button-active':iframeScope.getOption('acf_repeater')==repeater,'oxygen-button-list-button-default':iframeScope.isInherited(iframeScope.component.active.id,'acf_repeater',repeater)==true}">
                                    <input type="radio" name="acf_repeater" value="{{repeater}}" 
                                        ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['acf_repeater']" 
                                        ng-model-options="{ debounce: 10 }" 
                                        ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,'acf_repeater');iframeScope.checkResizeBoxOptions('acf_repeater')" 
                                        ng-click="radioButtonClick(iframeScope.component.active.name, 'acf_repeater', repeater)">
                                        {{repeaterObj.label}}     </label>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                }
            ?>

            <?php
                if(class_exists('RWMB_Loader')) { // MetaBox clonable group fields tab
            ?>

                <div class="oxygen-control-row"
                    ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['use_acf_repeater']!='true'">
                    <div class='oxygen-control-wrapper' style="flex-grow: unset; flex-basis: 120px;">
                        <label class="oxygen-checkbox">
                            <input type="checkbox"
                                ng-true-value="'true'" 
                                ng-false-value="'false'"
                                ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['use_metabox_clonable_group']"
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_dynamic_list','use_metabox_clonable_group')">
                            <div class='oxygen-checkbox-checkbox'
                                ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('use_metabox_clonable_group')=='true'}">
                                <?php _e("Use Meta Box Group","oxygen"); ?>
                            </div>
                        </label>
                    </div>
                </div>
            
                <div class='oxygen-control-row'
                    ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['use_metabox_clonable_group']=='true'">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Meta Box Groups","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <label class="oxygen-button-list-button" 
                                    ng-repeat="(group, groupObj) in iframeScope.metaBoxGroupFields"
                                    ng-class="{'oxygen-button-list-button-active':iframeScope.getOption('metabox_group')==group}">
                                    <input type="radio" name="metabox_group" value="{{group}}" 
                                        ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['metabox_group']" 
                                        ng-model-options="{ debounce: 10 }" 
                                        ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,'metabox_group')" 
                                        ng-click="radioButtonClick(iframeScope.component.active.name, 'metabox_group', group)">
                                        {{groupObj.label}}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                }
            ?>

                <div class="oxygen-control-row oxygen-control-row-bottom-bar">
                    <a href="#" class="oxygen-apply-button"
                        ng-click="iframeScope.updateRepeaterQuery()">
                        <?php _e("Apply Query Params", "oxygen"); ?>
                    </a>
                </div>

            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'postType')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'query')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'query')"><?php _e("Query","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Post Type","oxygen"); ?></div>
                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Post Type", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <select id="oxy-easy-posts-post-type" name="oxy-easy-posts-post-type[]" multiple="multiple"
                                ng-init="initSelect2('oxy-easy-posts-post-type','Choose custom post types...')"
                                ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['query_post_types']"
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_dynamic_list','query_post_types')">
                                <?php $custom_post_types = get_post_types();
                                $exclude_types  = array( "ct_template", "nav_menu_item", "revision" );
                                foreach($custom_post_types as $item) {
                                    if(!in_array($item, $exclude_types)) {?>
                                        <option value="<?php echo esc_attr( $item ); ?>"><?php echo sanitize_text_field( $item ); ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Or manually specify IDs (comma separated)", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-input'>
                                <input type="text" spellcheck="false"
                                    ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['query_post_ids']"
                                    ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_dynamic_list','query_post_ids')">
                            </div>
                        </div>
                    </div>

                </div>
                    
                <div class="oxygen-control-row oxygen-control-row-bottom-bar">
                    <a href="#" class="oxygen-apply-button"
                        ng-click="iframeScope.updateRepeaterQuery()">
                        <?php _e("Apply Query Params", "oxygen"); ?>
                    </a>
                </div>

            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'filtering')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'query')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'query')"><?php _e("Query","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Filtering","oxygen"); ?></div>
                </div>

                <?php
                    $query_taxonomies = array(
                        'query_taxonomies_any' => __("In Any of the Following Taxonomies", "oxygen"),
                        'query_taxonomies_all' => __("Or In All of the Following Taxonomies", "oxygen")
                    );
                ?>

                <?php foreach ($query_taxonomies as $key => $value) : ?>
                    
                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php echo $value; ?></label>
                        <div class='oxygen-control'>
                            <select name="oxy-easy-posts-<?php echo $key; ?>[]" id="oxy-easy-posts-<?php echo $key; ?>" multiple="multiple"
                                ng-init="initSelect2('oxy-easy-posts-<?php echo $key; ?>','Choose taxonomies...')"
                                ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['<?php echo $key; ?>']"
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_dynamic_list','<?php echo $key; ?>')">
                                <?php 
                                // get default post categories
                                $default_categories = get_categories(array('hide_empty' => 0));
                                ?>
                                    <optgroup label="<?php echo __('Categories', 'component-theme'); ?>">
                                        <?php 
                                        foreach ( $default_categories as $category ) : ?>
                                            <option value="<?php echo ((!isset($alloption) || !$alloption)?'category,':'').esc_attr( $category->term_id ); ?>">
                                                <?php echo sanitize_text_field( $category->name ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php
                                // get default post tags
                                $default_tags = get_tags(array('hide_empty' => 0));
                                ?>
                                    <optgroup label="<?php echo __('Tags', 'component-theme'); ?>">
                                        <?php 
                                        foreach ( $default_tags as $tag ) : ?>
                                            <option value="<?php echo ((!isset($alloption) || !$alloption)?'tag,':'').esc_attr( $tag->term_id ); ?>">
                                                <?php echo sanitize_text_field( $tag->name ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php
                                // get custom taxonomies
                                $args = array(
                                        "_builtin" => false
                                    );
                                $taxonomies = get_taxonomies( $args, 'object' );
                                foreach ( $taxonomies as $taxonomy ) : 
                                    $args = array(
                                        'hide_empty'    => 0,
                                        'taxonomy'      => $taxonomy->name,
                                    );
                                    $categories = get_categories( $args );
                                    if ( !isset($selected_items[$taxonomy->name]) || !$selected_items[$taxonomy->name] ) {
                                        $selected_items[$taxonomy->name] = array();
                                    }
                                    ?>
                                    <optgroup label="<?php echo sanitize_text_field( $taxonomy->labels->name . " (" . $taxonomy->name . ")" ); ?>">
                                        <?php foreach ( $categories as $category ) : ?>
                                            <option value="<?php echo ((!isset($alloption) || !$alloption)?$category->taxonomy.',':'').esc_attr( $category->term_id ); ?>">
                                                <?php echo sanitize_text_field( $category->name ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("By the following authors", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <select id="oxy-easy-posts-authors" name="oxy-easy-posts-authors[]" multiple="multiple"
                                ng-init="initSelect2('oxy-easy-posts-authors','Choose authors...')"
                                ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['query_authors']"
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_dynamic_list','query_authors')">
                                <?php // get all users to loop
                                $authors = oxygen_get_authors();
                                foreach ( $authors as $author ) : ?>
                                    <option value="<?php echo esc_attr( $author->ID ); ?>">
                                        <?php echo sanitize_text_field( $author->user_login ); ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php $custom_post_types = get_post_types(); ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="oxygen-control-row oxygen-control-row-bottom-bar">
                    <a href="#" class="oxygen-apply-button"
                        ng-click="iframeScope.updateRepeaterQuery()">
                        <?php _e("Apply Query Params", "oxygen"); ?>
                    </a>
                </div>

            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'order')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'query')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'query')"><?php _e("Query","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Order","oxygen"); ?></div>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Order By","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class="oxygen-select oxygen-select-box-wrapper">
                                <div class="oxygen-select-box">
                                    <div class="oxygen-select-box-current">{{$parent.iframeScope.getOption('query_order_by')}}</div>
                                    <div class="oxygen-select-box-dropdown"></div>
                                </div>
                                <div class="oxygen-select-box-options">
                                    <div class="oxygen-select-box-option"
                                        ng-click="$parent.iframeScope.setOptionModel('query_order_by','');"
                                        title="<?php _e("Unset order by", "oxygen"); ?>">
                                        &nbsp;
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="$parent.iframeScope.setOptionModel('query_order_by','date');"
                                        title="<?php _e("Set order by", "oxygen"); ?>">
                                        <?php _e("Date", "oxygen"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="$parent.iframeScope.setOptionModel('query_order_by','modified');"
                                        title="<?php _e("Set order by", "oxygen"); ?>">
                                        <?php _e("Date Last Modified", "oxygen"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="$parent.iframeScope.setOptionModel('query_order_by','title');"
                                        title="<?php _e("Set order by", "oxygen"); ?>">
                                        <?php _e("Title", "oxygen"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="$parent.iframeScope.setOptionModel('query_order_by','comment_count');"
                                        title="<?php _e("Set order by", "oxygen"); ?>">
                                        <?php _e("Comment Count", "oxygen"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="$parent.iframeScope.setOptionModel('query_order_by','menu_order');"
                                        title="<?php _e("Set order by", "oxygen"); ?>">
                                        <?php _e("Menu Order", "oxygen"); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Order","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('query_order', 'ASC', 'ascending'); ?>
                                <?php $oxygen_toolbar->button_list_button('query_order', 'DESC', 'descending'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="oxygen-control-row oxygen-control-row-bottom-bar">
                    <a href="#" class="oxygen-apply-button"
                        ng-click="iframeScope.updateRepeaterQuery()">
                        <?php _e("Apply Query Params", "oxygen"); ?>
                    </a>
                </div>

            </div>


            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('dynamicList', 'count')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('dynamicList', 'query')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('dynamicList', 'query')"><?php _e("Query","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Count","oxygen"); ?></div>
                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class="oxygen-checkbox">
                            <input type="checkbox"
                                ng-true-value="'true'" 
                                ng-false-value="'false'"
                                ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['query_ignore_sticky_posts']"
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_dynamic_list','query_ignore_sticky_posts')">
                            <div class='oxygen-checkbox-checkbox'
                                ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('query_ignore_sticky_posts')=='true'}">
                                <?php _e("Ignore Sticky Posts","oxygen"); ?>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class='oxygen-control-wrapper'>
                    <label class='oxygen-control-label'><?php _e("Posts per page", "oxygen"); ?></label>
                    <div class='oxygen-control'>
                        <div class='oxygen-input'>
                            <input type="text" spellcheck="false"
                                ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['query_count']"
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_dynamic_list','query_count')">
                        </div>
                    </div>
                </div>

                <div class="oxygen-control-row oxygen-control-row-bottom-bar">
                    <a href="#" class="oxygen-apply-button"
                        ng-click="iframeScope.updateRepeaterQuery()">
                        <?php _e("Apply Query Params", "oxygen"); ?>
                    </a>
                </div>
            </div>



        </div>

    <?php }


}
   

    

// Create component instance
global $oxygen_vsb_components;
$oxygen_vsb_components['repeater'] = new Oxygen_VSB_Dynamic_List( array(
            'name'  => 'Repeater',
            'tag'   => 'oxy_dynamic_list',
            'tabs'  => 'dynamicList',
            'params' => array(
                array(
                    "type" 			=> "tag",
                    "heading" 		=> __("Tag", "oxygen"),
                    "param_name" 	=> "tag",
                    "value" 		=> array (
                                        "div" 		=> "div",
                                        "article" 	=> "article",
                                        "aside" 	=> "aside",
                                        "details" 	=> "details",
                                        "figure" 	=> "figure",
                                        "footer" 	=> "footer",
                                        "header" 	=> "header",
                                        "hgroup" 	=> "hgroup",
                                        "main" 		=> "main",
                                        "mark" 		=> "mark",
                                        "nav" 		=> "nav",
                                        "section" 	=> "section",
                                    ),
                    "css" 			=> false,
                    "rebuild" 		=> true,
                ),
            ),
            'advanced'  => array(
                "positioning" => array(
                        "values"    => array (
                            'width'      => '100',
                            'width-unit' => '%',
                            )
                    ),
                "flex" => array(
                        "values"    => array (
                            'gap'      => '',
                            'gap-unit' => 'px',
                            'flex-direction' => '',
                            'align-items' => '',
                            'justify-content' => '',
                            'flex-wrap' => ''
                            )
                ),
                'grid' => array(
						'values' 	=> array (
							'grid-column-count' => '1',
							'grid-column-min-width' => '200',
							'grid-column-min-width-unit' => 'px',
							'grid-column-max-width' => '1',
							'grid-column-max-width-unit' => 'fr',
							'grid-column-gap' => '20',
							'grid-column-gap-unit' => 'px',

							'grid-row-behavior' => 'Auto',
							'grid-row-count' => '',
							'grid-row-min-height' => '',
							'grid-row-min-height-unit' => 'px',
							'grid-row-max-height' => '',
							'grid-row-max-height-unit' => 'fr',
							'grid-row-gap' => '20',
                            'grid-row-gap-unit' => 'px',
                            
                            'grid-match-height-of-tallest-child' => 'true',
							'grid-justify-items' => 'stretch',
							'grid-align-items' => 'stretch',
						)
					),
                "other" => array(
                    "values" => array(
                        "wp_query_advanced" => array(),
                        "wp_query_advanced_preset" => '',
                        "wp_query" => 'default',
                        "query_args" => 'author_name=admin&category_name=uncategorized&posts_per_page=2',
                        
                        "posts_per_page" => '',
                        
                        // query
                        "query_post_types" => '',
                        "query_post_ids" => '',
                        "query_taxonomies_all" => '',
                        "query_taxonomies_any" => '',
                        "query_order_by" => '',
                        "query_order" => '',
                        "query_authors" => '',
                        "query_count" => '',
                        "query_all_posts" => '',
                        "query_ignore_sticky_posts" => 'true',
                        "paginate_color" => '',
                        "paginate_size" => '12',
                        "paginate_size-unit" => 'px',
                        "paginate_alignment" => '',
                        "paginate_wrap_alignment" => '',
                        "paginate_mid_size" => null,
                        "paginate_end_size" => null,
                        "paginate_prev_link_text" => null,
                        "paginate_next_link_text" => null,
                        "paginate_link_color" => '',
                        "paginate_link_hover_color" => '',
                        // paginate size and spacing
                        "paginate_padding_left" => '',
                        "paginate_padding_left-unit" => 'px',
                        "paginate_padding_right" => '',
                        "paginate_padding_right-unit" => 'px',
                        "paginate_padding_top" => '',
                        "paginate_padding_top-unit" => 'px',
                        "paginate_padding_bottom" => '',
                        "paginate_padding_bottom-unit" => 'px',
                        
                        "paginate_margin_left" => '',
                        "paginate_margin_left-unit" => 'px',
                        "paginate_margin_right" => '',
                        "paginate_margin_right-unit" => 'px',
                        "paginate_margin_top" => '',
                        "paginate_margin_top-unit" => 'px',
                        "paginate_margin_bottom" => '',
                        "paginate_margin_bottom-unit" => 'px',

                        "paginate_width" => '',
                        "paginate_width-unit" => 'px',
                        "paginate_max_width" => '',
                        "paginate_max_width-unit" => 'px',
                        "paginate_min_width" => '',
                        "paginate_min_width-unit" => 'px',

                        "paginate_height" => '',
                        "paginate_height-unit" => 'px',
                        "paginate_max_height" => '',
                        "paginate_max_height-unit" => 'px',
                        "paginate_min_height" => '',
                        "paginate_min_height-unit" => 'px',
                        // paginate child layout
                        "paginate_flex_direction" => '',
                        "paginate_flex_wrap" => '',

                        // paginate borders
                        "paginate_border_all_color" => '',
                        "paginate_border_top_color" => '',
                        "paginate_border_left_color" => '',
                        "paginate_border_bottom_color" => '',
                        "paginate_border_right_color" => '',
                        "paginate_border_all_width" => '',
                        "paginate_border_all_width-unit" => 'px',
                        "paginate_border_top_width" => '',
                        "paginate_border_top_width-unit" => 'px',
                        "paginate_border_left_width" => '',
                        "paginate_border_left_width-unit" => 'px',
                        "paginate_border_bottom_width" => '',
                        "paginate_border_bottom_width-unit" => 'px',
                        "paginate_border_right_width" => '',
                        "paginate_border_right_width-unit" => 'px',
                        "paginate_border_all_style" => '',
                        "paginate_border_top_style" => '',
                        "paginate_border_left_style" => '',
                        "paginate_border_bottom_style" => '',
                        "paginate_border_right_style" => '',
                        "paginate_border_radius" => '',
                        "paginate_border_radius-unit" => 'px',
                        "paginate_border_top_left_radius" => '',
                        "paginate_border_top_left_radius-unit" => 'px',
                        "paginate_border_top_right_radius" => '',
                        "paginate_border_top_right_radius-unit" => 'px',
                        "paginate_border_bottom_right_radius" => '',
                        "paginate_border_bottom_right_radius-unit" => 'px',
                        "paginate_border_bottom_left_radius" => '',
                        "paginate_border_bottom_left_radius-unit" => 'px',
                        
                        // pagination background
                        "paginate_background_color" => '',
                        "paginate_background_image" => '',
                        "paginate_background_size" => '',
                        "paginate_background_size_width" => '',
                        "paginate_background_size_width-unit" => 'px',
                        "paginate_background_size_height" => '',
                        "paginate_background_size_height-unit" => 'px',
                        "paginate_background_repeat" => '',

                        // pagination link background
                        "paginatelink_background_color" => '',
                        "paginatelink_background_image" => '',
                        "paginatelink_background_size" => '',
                        "paginatelink_background_size_width" => '',
                        "paginatelink_background_size_width-unit" => 'px',
                        "paginatelink_background_size_height" => '',
                        "paginatelink_background_size_height-unit" => 'px',
                        "paginatelink_background_repeat" => '',

                        // pagination active link background
                        "paginatelinkactive_background_color" => '',
                        "paginatelinkactive_background_image" => '',
                        "paginatelinkactive_background_size" => '',
                        "paginatelinkactive_background_size_width" => '',
                        "paginatelinkactive_background_size_width-unit" => 'px',
                        "paginatelinkactive_background_size_height" => '',
                        "paginatelinkactive_background_size_height-unit" => 'px',
                        "paginatelinkactive_background_repeat" => '',

                        // paginate Link borders
                        "paginatelink_border_all_color" => '',
                        "paginatelink_border_top_color" => '',
                        "paginatelink_border_left_color" => '',
                        "paginatelink_border_bottom_color" => '',
                        "paginatelink_border_right_color" => '',
                        "paginatelink_border_all_width" => '',
                        "paginatelink_border_all_width-unit" => 'px',
                        "paginatelink_border_top_width" => '',
                        "paginatelink_border_top_width-unit" => 'px',
                        "paginatelink_border_left_width" => '',
                        "paginatelink_border_left_width-unit" => 'px',
                        "paginatelink_border_bottom_width" => '',
                        "paginatelink_border_bottom_width-unit" => 'px',
                        "paginatelink_border_right_width" => '',
                        "paginatelink_border_right_width-unit" => 'px',
                        "paginatelink_border_all_style" => '',
                        "paginatelink_border_top_style" => '',
                        "paginatelink_border_left_style" => '',
                        "paginatelink_border_bottom_style" => '',
                        "paginatelink_border_right_style" => '',
                        "paginatelink_border_radius" => '',
                        "paginatelink_border_radius-unit" => 'px',
                        "paginatelink_border_top_left_radius" => '',
                        "paginatelink_border_top_left_radius-unit" => 'px',
                        "paginatelink_border_top_right_radius" => '',
                        "paginatelink_border_top_right_radius-unit" => 'px',
                        "paginatelink_border_bottom_right_radius" => '',
                        "paginatelink_border_bottom_right_radius-unit" => 'px',
                        "paginatelink_border_bottom_left_radius" => '',
                        "paginatelink_border_bottom_left_radius-unit" => 'px',
                        // paginate Active Link borders
                        "paginatelinkactive_border_all_color" => '',
                        "paginatelinkactive_border_top_color" => '',
                        "paginatelinkactive_border_left_color" => '',
                        "paginatelinkactive_border_bottom_color" => '',
                        "paginatelinkactive_border_right_color" => '',
                        "paginatelinkactive_border_all_width" => '',
                        "paginatelinkactive_border_all_width-unit" => 'px',
                        "paginatelinkactive_border_top_width" => '',
                        "paginatelinkactive_border_top_width-unit" => 'px',
                        "paginatelinkactive_border_left_width" => '',
                        "paginatelinkactive_border_left_width-unit" => 'px',
                        "paginatelinkactive_border_bottom_width" => '',
                        "paginatelinkactive_border_bottom_width-unit" => 'px',
                        "paginatelinkactive_border_right_width" => '',
                        "paginatelinkactive_border_right_width-unit" => 'px',
                        "paginatelinkactive_border_all_style" => '',
                        "paginatelinkactive_border_top_style" => '',
                        "paginatelinkactive_border_left_style" => '',
                        "paginatelinkactive_border_bottom_style" => '',
                        "paginatelinkactive_border_right_style" => '',
                        "paginatelinkactive_border_radius" => '',
                        "paginatelinkactive_border_radius-unit" => 'px',
                        "paginatelinkactive_border_top_left_radius" => '',
                        "paginatelinkactive_border_top_left_radius-unit" => 'px',
                        "paginatelinkactive_border_top_right_radius" => '',
                        "paginatelinkactive_border_top_right_radius-unit" => 'px',
                        "paginatelinkactive_border_bottom_right_radius" => '',
                        "paginatelinkactive_border_bottom_right_radius-unit" => 'px',
                        "paginatelinkactive_border_bottom_left_radius" => '',
                        "paginatelinkactive_border_bottom_left_radius-unit" => 'px',

                        // paginate link size and spacing
                        "paginatelink_padding_left" => '',
                        "paginatelink_padding_left-unit" => 'px',
                        "paginatelink_padding_right" => '',
                        "paginatelink_padding_right-unit" => 'px',
                        "paginatelink_padding_top" => '',
                        "paginatelink_padding_top-unit" => 'px',
                        "paginatelink_padding_bottom" => '',
                        "paginatelink_padding_bottom-unit" => 'px',
                        
                        "paginatelink_margin_left" => '',
                        "paginatelink_margin_left-unit" => 'px',
                        "paginatelink_margin_right" => '',
                        "paginatelink_margin_right-unit" => 'px',
                        "paginatelink_margin_top" => '',
                        "paginatelink_margin_top-unit" => 'px',
                        "paginatelink_margin_bottom" => '',
                        "paginatelink_margin_bottom-unit" => 'px',

                        "paginatelink_width" => '',
                        "paginatelink_width-unit" => 'px',
                        "paginatelink_max_width" => '',
                        "paginatelink_max_width-unit" => 'px',
                        "paginatelink_min_width" => '',
                        "paginatelink_min_width-unit" => 'px',

                        "paginatelink_height" => '',
                        "paginatelink_height-unit" => 'px',
                        "paginatelink_max_height" => '',
                        "paginatelink_max_height-unit" => 'px',
                        "paginatelink_min_height" => '',
                        "paginatelink_min_height-unit" => 'px',

                        // paginate Active link size and spacing
                        "paginatelinkactive_padding_left" => '',
                        "paginatelinkactive_padding_left-unit" => 'px',
                        "paginatelinkactive_padding_right" => '',
                        "paginatelinkactive_padding_right-unit" => 'px',
                        "paginatelinkactive_padding_top" => '',
                        "paginatelinkactive_padding_top-unit" => 'px',
                        "paginatelinkactive_padding_bottom" => '',
                        "paginatelinkactive_padding_bottom-unit" => 'px',
                        
                        "paginatelinkactive_margin_left" => '',
                        "paginatelinkactive_margin_left-unit" => 'px',
                        "paginatelinkactive_margin_right" => '',
                        "paginatelinkactive_margin_right-unit" => 'px',
                        "paginatelinkactive_margin_top" => '',
                        "paginatelinkactive_margin_top-unit" => 'px',
                        "paginatelinkactive_margin_bottom" => '',
                        "paginatelinkactive_margin_bottom-unit" => 'px',

                        "paginatelinkactive_width" => '',
                        "paginatelinkactive_width-unit" => 'px',
                        "paginatelinkactive_max_width" => '',
                        "paginatelinkactive_max_width-unit" => 'px',
                        "paginatelinkactive_min_width" => '',
                        "paginatelinkactive_min_width-unit" => 'px',

                        "paginatelinkactive_height" => '',
                        "paginatelinkactive_height-unit" => 'px',
                        "paginatelinkactive_max_height" => '',
                        "paginatelinkactive_max_height-unit" => 'px',
                        "paginatelinkactive_min_height" => '',
                        "paginatelinkactive_min_height-unit" => 'px',

                        // pagination link hover background
                        "paginatelinkhover_background_color" => '',
                        "paginatelinkhover_background_image" => '',
                        "paginatelinkhover_background_size" => '',
                        "paginatelinkhover_background_size_width" => '',
                        "paginatelinkhover_background_size_width-unit" => 'px',
                        "paginatelinkhover_background_size_height" => '',
                        "paginatelinkhover_background_size_height-unit" => 'px',
                        "paginatelinkhover_background_repeat" => '',

                        // pagination active link hover background
                        "paginatelinkactivehover_background_color" => '',
                        "paginatelinkactivehover_background_image" => '',
                        "paginatelinkactivehover_background_size" => '',
                        "paginatelinkactivehover_background_size_width" => '',
                        "paginatelinkactivehover_background_size_width-unit" => 'px',
                        "paginatelinkactivehover_background_size_height" => '',
                        "paginatelinkactivehover_background_size_height-unit" => 'px',
                        "paginatelinkactivehover_background_repeat" => '',

                        // paginate Link hover borders
                        "paginatelinkhover_border_all_color" => '',
                        "paginatelinkhover_border_top_color" => '',
                        "paginatelinkhover_border_left_color" => '',
                        "paginatelinkhover_border_bottom_color" => '',
                        "paginatelinkhover_border_right_color" => '',
                        "paginatelinkhover_border_all_width" => '',
                        "paginatelinkhover_border_all_width-unit" => 'px',
                        "paginatelinkhover_border_top_width" => '',
                        "paginatelinkhover_border_top_width-unit" => 'px',
                        "paginatelinkhover_border_left_width" => '',
                        "paginatelinkhover_border_left_width-unit" => 'px',
                        "paginatelinkhover_border_bottom_width" => '',
                        "paginatelinkhover_border_bottom_width-unit" => 'px',
                        "paginatelinkhover_border_right_width" => '',
                        "paginatelinkhover_border_right_width-unit" => 'px',
                        "paginatelinkhover_border_all_style" => '',
                        "paginatelinkhover_border_top_style" => '',
                        "paginatelinkhover_border_left_style" => '',
                        "paginatelinkhover_border_bottom_style" => '',
                        "paginatelinkhover_border_right_style" => '',
                        "paginatelinkhover_border_radius" => '',
                        "paginatelinkhover_border_radius-unit" => 'px',
                        "paginatelinkhover_border_top_left_radius" => '',
                        "paginatelinkhover_border_top_left_radius-unit" => 'px',
                        "paginatelinkhover_border_top_right_radius" => '',
                        "paginatelinkhover_border_top_right_radius-unit" => 'px',
                        "paginatelinkhover_border_bottom_right_radius" => '',
                        "paginatelinkhover_border_bottom_right_radius-unit" => 'px',
                        "paginatelinkhover_border_bottom_left_radius" => '',
                        "paginatelinkhover_border_bottom_left_radius-unit" => 'px',
                        // paginate Active Link hover borders
                        "paginatelinkactivehover_border_all_color" => '',
                        "paginatelinkactivehover_border_top_color" => '',
                        "paginatelinkactivehover_border_left_color" => '',
                        "paginatelinkactivehover_border_bottom_color" => '',
                        "paginatelinkactivehover_border_right_color" => '',
                        "paginatelinkactivehover_border_all_width" => '',
                        "paginatelinkactivehover_border_all_width-unit" => 'px',
                        "paginatelinkactivehover_border_top_width" => '',
                        "paginatelinkactivehover_border_top_width-unit" => 'px',
                        "paginatelinkactivehover_border_left_width" => '',
                        "paginatelinkactivehover_border_left_width-unit" => 'px',
                        "paginatelinkactivehover_border_bottom_width" => '',
                        "paginatelinkactivehover_border_bottom_width-unit" => 'px',
                        "paginatelinkactivehover_border_right_width" => '',
                        "paginatelinkactivehover_border_right_width-unit" => 'px',
                        "paginatelinkactivehover_border_all_style" => '',
                        "paginatelinkactivehover_border_top_style" => '',
                        "paginatelinkactivehover_border_left_style" => '',
                        "paginatelinkactivehover_border_bottom_style" => '',
                        "paginatelinkactivehover_border_right_style" => '',
                        "paginatelinkactivehover_border_radius" => '',
                        "paginatelinkactivehover_border_radius-unit" => 'px',
                        "paginatelinkactivehover_border_top_left_radius" => '',
                        "paginatelinkactivehover_border_top_left_radius-unit" => 'px',
                        "paginatelinkactivehover_border_top_right_radius" => '',
                        "paginatelinkactivehover_border_top_right_radius-unit" => 'px',
                        "paginatelinkactivehover_border_bottom_right_radius" => '',
                        "paginatelinkactivehover_border_bottom_right_radius-unit" => 'px',
                        "paginatelinkactivehover_border_bottom_left_radius" => '',
                        "paginatelinkactivehover_border_bottom_left_radius-unit" => 'px',

                        // paginate link hover size and spacing
                        "paginatelinkhover_padding_left" => '',
                        "paginatelinkhover_padding_left-unit" => 'px',
                        "paginatelinkhover_padding_right" => '',
                        "paginatelinkhover_padding_right-unit" => 'px',
                        "paginatelinkhover_padding_top" => '',
                        "paginatelinkhover_padding_top-unit" => 'px',
                        "paginatelinkhover_padding_bottom" => '',
                        "paginatelinkhover_padding_bottom-unit" => 'px',
                        
                        "paginatelinkhover_margin_left" => '',
                        "paginatelinkhover_margin_left-unit" => 'px',
                        "paginatelinkhover_margin_right" => '',
                        "paginatelinkhover_margin_right-unit" => 'px',
                        "paginatelinkhover_margin_top" => '',
                        "paginatelinkhover_margin_top-unit" => 'px',
                        "paginatelinkhover_margin_bottom" => '',
                        "paginatelinkhover_margin_bottom-unit" => 'px',

                        "paginatelinkhover_width" => '',
                        "paginatelinkhover_width-unit" => 'px',
                        "paginatelinkhover_max_width" => '',
                        "paginatelinkhover_max_width-unit" => 'px',
                        "paginatelinkhover_min_width" => '',
                        "paginatelinkhover_min_width-unit" => 'px',

                        "paginatelinkhover_height" => '',
                        "paginatelinkhover_height-unit" => 'px',
                        "paginatelinkhover_max_height" => '',
                        "paginatelinkhover_max_height-unit" => 'px',
                        "paginatelinkhover_min_height" => '',
                        "paginatelinkhover_min_height-unit" => 'px',

                        // paginate Active link hover size and spacing
                        "paginatelinkactivehover_padding_left" => '',
                        "paginatelinkactivehover_padding_left-unit" => 'px',
                        "paginatelinkactivehover_padding_right" => '',
                        "paginatelinkactivehover_padding_right-unit" => 'px',
                        "paginatelinkactivehover_padding_top" => '',
                        "paginatelinkactivehover_padding_top-unit" => 'px',
                        "paginatelinkactivehover_padding_bottom" => '',
                        "paginatelinkactivehover_padding_bottom-unit" => 'px',
                        
                        "paginatelinkactivehover_margin_left" => '',
                        "paginatelinkactivehover_margin_left-unit" => 'px',
                        "paginatelinkactivehover_margin_right" => '',
                        "paginatelinkactivehover_margin_right-unit" => 'px',
                        "paginatelinkactivehover_margin_top" => '',
                        "paginatelinkactivehover_margin_top-unit" => 'px',
                        "paginatelinkactivehover_margin_bottom" => '',
                        "paginatelinkactivehover_margin_bottom-unit" => 'px',

                        "paginatelinkactivehover_width" => '',
                        "paginatelinkactivehover_width-unit" => 'px',
                        "paginatelinkactivehover_max_width" => '',
                        "paginatelinkactivehover_max_width-unit" => 'px',
                        "paginatelinkactivehover_min_width" => '',
                        "paginatelinkactivehover_min_width-unit" => 'px',

                        "paginatelinkactivehover_height" => '',
                        "paginatelinkactivehover_height-unit" => 'px',
                        "paginatelinkactivehover_max_height" => '',
                        "paginatelinkactivehover_max_height-unit" => 'px',
                        "paginatelinkactivehover_min_height" => '',
                        "paginatelinkactivehover_min_height-unit" => 'px',

                        // paginate link transition duration
                        "paginate_link_transition" => '',
                    )
                )
            ),
            'not_css_params' => array(
                    
                "wp_query",
                "query_args",
                "posts_per_page",
                
                // query
                "query_post_types",
                "query_post_ids",
                "query_taxonomies_all",
                "query_taxonomies_any",
                "query_order_by",
                "query_order",
                "query_authors",
                "query_count",
                "query_all_posts",
                "query_ignore_sticky_posts",

                "paginate_color",
                "paginate_size",
                "paginate_size-unit",
                "paginate_alignment",
                "paginate_wrap_alignment",
                "paginate_mid_size",
                "paginate_end_size",
                "paginate_prev_link_text",
                "paginate_next_link_text",
                "paginate_link_color",
                "paginate_link_hover_color",

                // pagination size and spacing
                "paginate_padding_left",
                "paginate_padding_left-unit",
                "paginate_padding_right",
                "paginate_padding_right-unit",
                "paginate_padding_top",
                "paginate_padding_top-unit",
                "paginate_padding_bottom",
                "paginate_padding_bottom-unit",
                "paginate_margin_left",
                "paginate_margin_left-unit",
                "paginate_margin_right",
                "paginate_margin_right-unit",
                "paginate_margin_top",
                "paginate_margin_top-unit",
                "paginate_margin_bottom",
                "paginate_margin_bottom-unit",

                "paginate_width",
                "paginate_width-unit",
                "paginate_max_width",
                "paginate_max_width-unit",
                "paginate_min_width",
                "paginate_min_width-unit",

                "paginate_height",
                "paginate_height-unit",
                "paginate_max_height",
                "paginate_max_height-unit",
                "paginate_min_height",
                "paginate_min_height-unit",

                // pagination child layout
                "paginate_flex_direction",
                "paginate_flex_wrap",

                // pagination borders
                "paginate_border_all_color",
                "paginate_border_top_color",
                "paginate_border_left_color",
                "paginate_border_bottom_color",
                "paginate_border_right_color",
                "paginate_border_all_width",
                "paginate_border_all_width-unit",
                "paginate_border_top_width",
                "paginate_border_top_width-unit",
                "paginate_border_left_width",
                "paginate_border_left_width-unit",
                "paginate_border_bottom_width",
                "paginate_border_bottom_width-unit",
                "paginate_border_right_width",
                "paginate_border_right_width-unit",
                "paginate_border_all_style",
                "paginate_border_top_style",
                "paginate_border_left_style",
                "paginate_border_bottom_style",
                "paginate_border_right_style",
                "paginate_border_radius",
                "paginate_border_radius-unit",
                "paginate_border_top_left_radius",
                "paginate_border_top_left_radius-unit",
                "paginate_border_top_right_radius",
                "paginate_border_top_right_radius-unit",
                "paginate_border_bottom_right_radius",
                "paginate_border_bottom_right_radius-unit",
                "paginate_border_bottom_left_radius",
                "paginate_border_bottom_left_radius-unit",
                
                // pagination background
                "paginate_background_color",
                "paginate_background_image",
                "paginate_background_size",
                "paginate_background_size_width",
                "paginate_background_size_width-unit",
                "paginate_background_size_height",
                "paginate_background_size_height-unit",
                "paginate_background_repeat",
                // pagination link background
                "paginatelink_background_color",
                "paginatelink_background_image",
                "paginatelink_background_size",
                "paginatelink_background_size_width",
                "paginatelink_background_size_width-unit",
                "paginatelink_background_size_height",
                "paginatelink_background_size_height-unit",
                "paginatelink_background_repeat",

                // pagination active link background
                "paginatelinkactive_background_color",
                "paginatelinkactive_background_image",
                "paginatelinkactive_background_size",
                "paginatelinkactive_background_size_width",
                "paginatelinkactive_background_size_width-unit",
                "paginatelinkactive_background_size_height",
                "paginatelinkactive_background_size_height-unit",
                "paginatelinkactive_background_repeat",

                // paginate link size and spacing
                "paginatelink_padding_left",
                "paginatelink_padding_left-unit",
                "paginatelink_padding_right",
                "paginatelink_padding_right-unit",
                "paginatelink_padding_top",
                "paginatelink_padding_top-unit",
                "paginatelink_padding_bottom",
                "paginatelink_padding_bottom-unit",
                "paginatelink_margin_left",
                "paginatelink_margin_left-unit",
                "paginatelink_margin_right",
                "paginatelink_margin_right-unit",
                "paginatelink_margin_top",
                "paginatelink_margin_top-unit",
                "paginatelink_margin_bottom",
                "paginatelink_margin_bottom-unit",

                "paginatelink_width",
                "paginatelink_width-unit",
                "paginatelink_max_width",
                "paginatelink_max_width-unit",
                "paginatelink_min_width",
                "paginatelink_min_width-unit",

                "paginatelink_height",
                "paginatelink_height-unit",
                "paginatelink_max_height",
                "paginatelink_max_height-unit",
                "paginatelink_min_height",
                "paginatelink_min_height-unit",

                // paginate active link size and spacing
                "paginatelinkactive_padding_left",
                "paginatelinkactive_padding_left-unit",
                "paginatelinkactive_padding_right",
                "paginatelinkactive_padding_right-unit",
                "paginatelinkactive_padding_top",
                "paginatelinkactive_padding_top-unit",
                "paginatelinkactive_padding_bottom",
                "paginatelinkactive_padding_bottom-unit",
                "paginatelinkactive_margin_left",
                "paginatelinkactive_margin_left-unit",
                "paginatelinkactive_margin_right",
                "paginatelinkactive_margin_right-unit",
                "paginatelinkactive_margin_top",
                "paginatelinkactive_margin_top-unit",
                "paginatelinkactive_margin_bottom",
                "paginatelinkactive_margin_bottom-unit",

                "paginatelinkactive_width",
                "paginatelinkactive_width-unit",
                "paginatelinkactive_max_width",
                "paginatelinkactive_max_width-unit",
                "paginatelinkactive_min_width",
                "paginatelinkactive_min_width-unit",

                "paginatelinkactive_height",
                "paginatelinkactive_height-unit",
                "paginatelinkactive_max_height",
                "paginatelinkactive_max_height-unit",
                "paginatelinkactive_min_height",
                "paginatelinkactive_min_height-unit",

                // pagination link borders
                "paginatelink_border_all_color",
                "paginatelink_border_top_color",
                "paginatelink_border_left_color",
                "paginatelink_border_bottom_color",
                "paginatelink_border_right_color",
                "paginatelink_border_all_width",
                "paginatelink_border_all_width-unit",
                "paginatelink_border_top_width",
                "paginatelink_border_top_width-unit",
                "paginatelink_border_left_width",
                "paginatelink_border_left_width-unit",
                "paginatelink_border_bottom_width",
                "paginatelink_border_bottom_width-unit",
                "paginatelink_border_right_width",
                "paginatelink_border_right_width-unit",
                "paginatelink_border_all_style",
                "paginatelink_border_top_style",
                "paginatelink_border_left_style",
                "paginatelink_border_bottom_style",
                "paginatelink_border_right_style",
                "paginatelink_border_radius",
                "paginatelink_border_radius-unit",
                "paginatelink_border_top_left_radius",
                "paginatelink_border_top_left_radius-unit",
                "paginatelink_border_top_right_radius",
                "paginatelink_border_top_right_radius-unit",
                "paginatelink_border_bottom_right_radius",
                "paginatelink_border_bottom_right_radius-unit",
                "paginatelink_border_bottom_left_radius",
                "paginatelink_border_bottom_left_radius-unit",
                // pagination active link borders
                "paginatelinkactive_border_all_color",
                "paginatelinkactive_border_top_color",
                "paginatelinkactive_border_left_color",
                "paginatelinkactive_border_bottom_color",
                "paginatelinkactive_border_right_color",
                "paginatelinkactive_border_all_width",
                "paginatelinkactive_border_all_width-unit",
                "paginatelinkactive_border_top_width",
                "paginatelinkactive_border_top_width-unit",
                "paginatelinkactive_border_left_width",
                "paginatelinkactive_border_left_width-unit",
                "paginatelinkactive_border_bottom_width",
                "paginatelinkactive_border_bottom_width-unit",
                "paginatelinkactive_border_right_width",
                "paginatelinkactive_border_right_width-unit",
                "paginatelinkactive_border_all_style",
                "paginatelinkactive_border_top_style",
                "paginatelinkactive_border_left_style",
                "paginatelinkactive_border_bottom_style",
                "paginatelinkactive_border_right_style",
                "paginatelinkactive_border_radius",
                "paginatelinkactive_border_radius-unit",
                "paginatelinkactive_border_top_left_radius",
                "paginatelinkactive_border_top_left_radius-unit",
                "paginatelinkactive_border_top_right_radius",
                "paginatelinkactive_border_top_right_radius-unit",
                "paginatelinkactive_border_bottom_right_radius",
                "paginatelinkactive_border_bottom_right_radius-unit",
                "paginatelinkactive_border_bottom_left_radius",
                "paginatelinkactive_border_bottom_left_radius-unit",

                // pagination link hover background
                "paginatelinkhover_background_color",
                "paginatelinkhover_background_image",
                "paginatelinkhover_background_size",
                "paginatelinkhover_background_size_width",
                "paginatelinkhover_background_size_width-unit",
                "paginatelinkhover_background_size_height",
                "paginatelinkhover_background_size_height-unit",
                "paginatelinkhover_background_repeat",

                // pagination active link hover background
                "paginatelinkactivehover_background_color",
                "paginatelinkactivehover_background_image",
                "paginatelinkactivehover_background_size",
                "paginatelinkactivehover_background_size_width",
                "paginatelinkactivehover_background_size_width-unit",
                "paginatelinkactivehover_background_size_height",
                "paginatelinkactivehover_background_size_height-unit",
                "paginatelinkactivehover_background_repeat",

                // paginate link hover size and spacing
                "paginatelinkhover_padding_left",
                "paginatelinkhover_padding_left-unit",
                "paginatelinkhover_padding_right",
                "paginatelinkhover_padding_right-unit",
                "paginatelinkhover_padding_top",
                "paginatelinkhover_padding_top-unit",
                "paginatelinkhover_padding_bottom",
                "paginatelinkhover_padding_bottom-unit",
                "paginatelinkhover_margin_left",
                "paginatelinkhover_margin_left-unit",
                "paginatelinkhover_margin_right",
                "paginatelinkhover_margin_right-unit",
                "paginatelinkhover_margin_top",
                "paginatelinkhover_margin_top-unit",
                "paginatelinkhover_margin_bottom",
                "paginatelinkhover_margin_bottom-unit",

                "paginatelinkhover_width",
                "paginatelinkhover_width-unit",
                "paginatelinkhover_max_width",
                "paginatelinkhover_max_width-unit",
                "paginatelinkhover_min_width",
                "paginatelinkhover_min_width-unit",

                "paginatelinkhover_height",
                "paginatelinkhover_height-unit",
                "paginatelinkhover_max_height",
                "paginatelinkhover_max_height-unit",
                "paginatelinkhover_min_height",
                "paginatelinkhover_min_height-unit",

                // paginate active link hover size and spacing
                "paginatelinkactivehover_padding_left",
                "paginatelinkactivehover_padding_left-unit",
                "paginatelinkactivehover_padding_right",
                "paginatelinkactivehover_padding_right-unit",
                "paginatelinkactivehover_padding_top",
                "paginatelinkactivehover_padding_top-unit",
                "paginatelinkactivehover_padding_bottom",
                "paginatelinkactivehover_padding_bottom-unit",
                "paginatelinkactivehover_margin_left",
                "paginatelinkactivehover_margin_left-unit",
                "paginatelinkactivehover_margin_right",
                "paginatelinkactivehover_margin_right-unit",
                "paginatelinkactivehover_margin_top",
                "paginatelinkactivehover_margin_top-unit",
                "paginatelinkactivehover_margin_bottom",
                "paginatelinkactivehover_margin_bottom-unit",

                "paginatelinkactivehover_width",
                "paginatelinkactivehover_width-unit",
                "paginatelinkactivehover_max_width",
                "paginatelinkactivehover_max_width-unit",
                "paginatelinkactivehover_min_width",
                "paginatelinkactivehover_min_width-unit",

                "paginatelinkactivehover_height",
                "paginatelinkactivehover_height-unit",
                "paginatelinkactivehover_max_height",
                "paginatelinkactivehover_max_height-unit",
                "paginatelinkactivehover_min_height",
                "paginatelinkactivehover_min_height-unit",

                // pagination link hover borders
                "paginatelinkhover_border_all_color",
                "paginatelinkhover_border_top_color",
                "paginatelinkhover_border_left_color",
                "paginatelinkhover_border_bottom_color",
                "paginatelinkhover_border_right_color",
                "paginatelinkhover_border_all_width",
                "paginatelinkhover_border_all_width-unit",
                "paginatelinkhover_border_top_width",
                "paginatelinkhover_border_top_width-unit",
                "paginatelinkhover_border_left_width",
                "paginatelinkhover_border_left_width-unit",
                "paginatelinkhover_border_bottom_width",
                "paginatelinkhover_border_bottom_width-unit",
                "paginatelinkhover_border_right_width",
                "paginatelinkhover_border_right_width-unit",
                "paginatelinkhover_border_all_style",
                "paginatelinkhover_border_top_style",
                "paginatelinkhover_border_left_style",
                "paginatelinkhover_border_bottom_style",
                "paginatelinkhover_border_right_style",
                "paginatelinkhover_border_radius",
                "paginatelinkhover_border_radius-unit",
                "paginatelinkhover_border_top_left_radius",
                "paginatelinkhover_border_top_left_radius-unit",
                "paginatelinkhover_border_top_right_radius",
                "paginatelinkhover_border_top_right_radius-unit",
                "paginatelinkhover_border_bottom_right_radius",
                "paginatelinkhover_border_bottom_right_radius-unit",
                "paginatelinkhover_border_bottom_left_radius",
                "paginatelinkhover_border_bottom_left_radius-unit",
                // pagination active link hover borders
                "paginatelinkactivehover_border_all_color",
                "paginatelinkactivehover_border_top_color",
                "paginatelinkactivehover_border_left_color",
                "paginatelinkactivehover_border_bottom_color",
                "paginatelinkactivehover_border_right_color",
                "paginatelinkactivehover_border_all_width",
                "paginatelinkactivehover_border_all_width-unit",
                "paginatelinkactivehover_border_top_width",
                "paginatelinkactivehover_border_top_width-unit",
                "paginatelinkactivehover_border_left_width",
                "paginatelinkactivehover_border_left_width-unit",
                "paginatelinkactivehover_border_bottom_width",
                "paginatelinkactivehover_border_bottom_width-unit",
                "paginatelinkactivehover_border_right_width",
                "paginatelinkactivehover_border_right_width-unit",
                "paginatelinkactivehover_border_all_style",
                "paginatelinkactivehover_border_top_style",
                "paginatelinkactivehover_border_left_style",
                "paginatelinkactivehover_border_bottom_style",
                "paginatelinkactivehover_border_right_style",
                "paginatelinkactivehover_border_radius",
                "paginatelinkactivehover_border_radius-unit",
                "paginatelinkactivehover_border_top_left_radius",
                "paginatelinkactivehover_border_top_left_radius-unit",
                "paginatelinkactivehover_border_top_right_radius",
                "paginatelinkactivehover_border_top_right_radius-unit",
                "paginatelinkactivehover_border_bottom_right_radius",
                "paginatelinkactivehover_border_bottom_right_radius-unit",
                "paginatelinkactivehover_border_bottom_left_radius",
                "paginatelinkactivehover_border_bottom_left_radius-unit",

                // paginate link transition duration
                "paginate_link_transition",

            )
        ));
