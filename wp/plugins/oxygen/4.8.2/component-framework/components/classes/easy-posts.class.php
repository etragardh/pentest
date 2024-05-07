<?php

/**
 * Easy Posts Component Class
 * 
 * @since 2.0
 */

define("OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH", plugin_dir_path(__FILE__)."easy-posts-templates/");

class Oxygen_VSB_Easy_Posts extends CT_Component {

    public $param_array = array();
    public $css_util;
    public $query;
    public $action_name = "oxy_render_easy_posts";
    public $template_file = "easy-posts.php"; 

    function __construct($options) {

        // run initialization
        $this->init( $options );

        // Add shortcodes
        add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
        add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

        // change component button place
        remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
        add_action("oxygen_helpers_components_dynamic", array( $this, "component_button" ) );

        // output styles
        add_filter("ct_footer_styles", array( $this, "template_css" ) );
        add_filter("ct_footer_styles", array( $this, "params_css" ) );

        // add specific options to Basic Styles tab
        add_action("ct_toolbar_component_settings", array( $this, "settings") );
        
        // render preveiew with AJAX
        add_filter("template_include", array( $this, "single_template"), 100 );

        add_filter("oxygen_vsb_element_presets_defaults", array( $this, "presets_defaults") );
        add_filter("oxygen_elements_with_full_presets", array($this, "oxygen_elements_with_full_presets_callback"));
    }

    function presets_defaults($all_elements_defaults) {
        
        require("easy-posts-templates/easy-posts-presets.php");
        
        $all_elements_defaults = array_merge($all_elements_defaults, $easy_posts_defaults);

        return $all_elements_defaults;
    }

    
    /**
     * Add a [oxy_posts_grid] shortcode to WordPress
     *
     * @since 2.0
     * @author Louis & Ilya
     */

    function add_shortcode( $atts, $content, $name ) {

        if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
            return '';
        }

        $options = $this->set_options( $atts );

		if (!oxygen_doing_oxygen_elements()) {
            if(isset(json_decode($atts['ct_options'])->original)) {
                if(isset(json_decode($atts['ct_options'])->original->{'code-php'}) ) {
                    $options['code_php'] =  base64_decode($options['code_php']);
                }
                if(isset(json_decode($atts['ct_options'])->original->{'code-css'}) ) {
                    $options['code_css'] =  base64_decode($options['code_css']);
                }
            }
        }

        $this->register_properties($options['id']);

        if (!is_array($this->param_array)) {
            $this->param_array = array();
        }

        $query_changed = false;
        if (get_option('page_on_front') == get_the_ID()) {
            if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
            elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
            else { $paged = 1; }
            
            $query = array(
                'posts_per_page' => 3,
                'paged' => $paged
            );

            // if this is a archive make sure we count count post type 
            if ( get_query_var('post_type') ) { 
                $query['post_type'] = get_query_var('post_type'); 
            }
            
            query_posts( $query ); 
            $query_changed = true;
        }

        $this->param_array[$options['id']] = shortcode_atts(
            array(
                "wp_query_advanced" => "{}",
                "wp_query_advanced_preset" => '',
                "template" => 'grid-image-standard',
                "code_php" => '',
                "code_css" => '',
                "wp_query" => 'default',
                "query_args" => 'author_name=admin&category_name=uncategorized&posts_per_page=2',
                "title_size" => '36',
                "title_size_unit" => 'px',
                "title_color" => 'blue',
                "title_hover_color" => 'red',
                "meta_size" => '12',
                "meta_size_unit" => 'px',
                "meta_color" => 'black',
                "content_size" => '21',
                "content_size_unit" => 'px',
                "content_color" => 'black',
                "read_more_display_as" => 'button',
                "read_more_size" => '16',
                "read_more_size_unit" => 'px',
                "read_more_text_color" => 'blue',
                "read_more_text_hover_color" => 'black',
                "read_more_button_color" => 'green',
                "read_more_button_hover_color" => '#8888ff',
                "paginate_color" => '#00aa00',
                "paginate_alignment" => 'center',
                "paginate_link_color" => 'blue',
                "paginate_link_hover_color" => 'orange',
                "posts_per_page" => 7,
                "posts_5050_below" => 'tablet',
                "posts_100_below" => 'phone-landscape',
                "query_post_types" => '',
                "query_post_ids" => '',
                "query_taxonomies_any" => '',
                "query_taxonomies_all" => '',
                "query_order_by" => '',
                "query_order" => '',
                "query_authors" => '',
                "query_count" => '',
                "query_all_posts" => '',
                "query_ignore_sticky_posts" => 'true',
            ), $options, $this->options['tag'] );

        $this->param_array[$options['id']]["selector"] = esc_attr($options['selector']);

        $posts = $this->get_the_posts($options['id']);
        
        if(isset($atts['preview']) && $atts['preview'] == 'true') {
            // make sure errors are shown
            $error_reporting = error_reporting(E_ERROR | E_WARNING | E_PARSE);
            $display_errors = ini_get('display_errors');
            ini_set('display_errors', 1); 
        }

        ob_start(); ?>
        
        <?php if (!isset($atts['preview']) || $atts['preview']!='true') : ?>
        <div id="<?php echo esc_attr($options['selector']); ?>" class='oxy-easy-posts <?php echo esc_attr($options['classes']); ?>' <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>>
        <?php endif; ?>
            <div class='oxy-posts'>
                <?php while ($this->query->have_posts()) {
                    $this->query->the_post();
                    eval("?> ".$options['code_php']."<?php ");
                } ?>
            </div>
            <?php if ( $this->param_array[$options['id']]['wp_query'] != 'custom' ||
                     ( $this->param_array[$options['id']]['wp_query'] == 'custom' && !$this->param_array[$options['id']]['query_count'] )): ?>
            <div class='oxy-easy-posts-pages'>
                <?php
                $big = 999999999; // need an unlikely integer
                echo paginate_links( array(
                    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                    'format' => '?paged=%#%',
                    'current' => max( 1, get_query_var('paged') ),
                    'total' => $this->query->max_num_pages,
                ) );
                ?>
            </div>
            <?php endif; ?>
        <?php if (!isset($atts['preview']) || $atts['preview']!='true') : ?>
        </div>
        <?php endif; ?>
        <?php

        // set errors params back
        if(isset($display_errors)) {
            ini_set('display_errors', $display_errors); 
            error_reporting($error_reporting);
        }

        // output template CSS for builder preview only
        if ((isset($atts['preview']) && $atts['preview']=='true') || (isset($_REQUEST['action']) && $_REQUEST['action'] == "ct_get_post_data")) {
            $code_css   = $options['code_css'];
            $code_css   = str_replace("%%EPID%%", "#".$options['selector'], $code_css);
            $code_css   = preg_replace_callback(
                            "/color\(\d+\)/",
                            "oxygen_vsb_parce_global_colors_callback",
                            $code_css);

            echo "<style type=\"text/css\" class='oxygen-easy-posts-ajax-styles-".$options['id']."'>";
            echo $code_css;
            echo "</style>\r\n";
        }

        $outputContent = ob_get_clean();

        $outputContent = apply_filters('oxygen_vsb_after_component_render', $outputContent, $this->options, $name);

        // restores the global $post variable to the current post in the main query
        wp_reset_postdata();

        // restore query after home page pagination fix  
        if ( $query_changed ) {
            wp_reset_query();
        }

        return $outputContent;
    }


    /**
     * Map parameters to CSS properties
     *
     * @since 2.0
     * @author Louis
     */

    function register_properties($id) {

        $this->cssutil[$id] = new Oxygen_VSB_CSS_Util;

        $this->cssutil[$id]->register_selector('.oxy-post-title');
        $this->cssutil[$id]->register_selector('.oxy-post-title:hover');
        $this->cssutil[$id]->register_selector('.oxy-post-meta');
        $this->cssutil[$id]->register_selector('.oxy-post-content');
        $this->cssutil[$id]->register_selector('.oxy-read-more');
        $this->cssutil[$id]->register_selector('.oxy-read-more:hover');
        $this->cssutil[$id]->register_selector('.oxy-easy-posts-pages');
        $this->cssutil[$id]->register_selector('.oxy-easy-posts-pages a.page-numbers');
        $this->cssutil[$id]->register_selector('.oxy-easy-posts-pages a.page-numbers:hover');

        $this->cssutil[$id]->map_property('title_size', 'font-size',                         '.oxy-post-title');
        $this->cssutil[$id]->map_property('title_color', 'color',                            '.oxy-post-title');
        $this->cssutil[$id]->map_property('title_hover_color', 'color',                      '.oxy-post-title:hover');
        $this->cssutil[$id]->map_property('meta_size', 'font-size',                          '.oxy-post-meta');
        $this->cssutil[$id]->map_property('meta_color', 'color',                             '.oxy-post-meta');
        $this->cssutil[$id]->map_property('content_size', 'font-size',                       '.oxy-post-content');
        $this->cssutil[$id]->map_property('content_color', 'color',                          '.oxy-post-content');
        $this->cssutil[$id]->map_property('read_more_size', 'font-size',                     '.oxy-read-more');
        $this->cssutil[$id]->map_property('read_more_text_color', 'color',                   '.oxy-read-more');
        $this->cssutil[$id]->map_property('read_more_button_color', 'background-color',      '.oxy-read-more');
        $this->cssutil[$id]->map_property('read_more_text_hover_color', 'color',             '.oxy-read-more:hover');
        $this->cssutil[$id]->map_property('read_more_button_hover_color', 'background-color','.oxy-read-more:hover');
        $this->cssutil[$id]->map_property('paginate_color', 'color',                         '.oxy-easy-posts-pages');
        $this->cssutil[$id]->map_property('paginate_alignment', 'text-align',                '.oxy-easy-posts-pages');
        $this->cssutil[$id]->map_property('paginate_link_color', 'color',                    '.oxy-easy-posts-pages a.page-numbers');
        $this->cssutil[$id]->map_property('paginate_link_hover_color', 'color',              '.oxy-easy-posts-pages a.page-numbers:hover');

        $this->cssutil[$id]->register_contingency_function(array($this, 'read_more_button_contingency'));
        $this->cssutil[$id]->register_css_output_function(array($this, 'responsive'));
    }

    
    /**
     * Output specific button CSS
     *
     * @since 2.0
     * @author Louis
     */

    function read_more_button_contingency($selectors, $id) {

        if(!is_array($this->param_array)||empty($this->param_array)) {
            return array();
        }

        $readmore       = isset($selectors['.oxy-read-more']) ? $selectors['.oxy-read-more'] : array();
        $readmorehover  = isset($selectors['.oxy-read-more:hover']) ? $selectors['.oxy-read-more:hover'] : array();

        if ($this->param_array[$id]['read_more_display_as'] == 'button') {

            $readmore['text-decoration'] = 'none';
            $readmore['padding'] = '0.75em 1.5em';
            $readmore['line-height'] = '1';
            $readmore['border-radius'] = '3px';
            $readmore['display'] = 'inline-block';

            $readmorehover['text-decoration'] = 'none';

        } else {
            unset($readmore['background-color']);
            unset($readmorehover['background-color']);
        }

        $selectors['.oxy-read-more'] = $readmore;
        $selectors['.oxy-read-more:hover'] = $readmorehover;

        return $selectors;
    }


    /**
     * Output specific responsive CSS
     *
     * @since 2.0
     * @author Louis & Ilya
     */

    function responsive($id) {

        if(!is_array($this->param_array)||empty($this->param_array)) {
            return "";
        }

        global $media_queries_list;

        ob_start();

        if ( isset($this->param_array[$id]['posts_5050_below']) && isset($media_queries_list[$this->param_array[$id]['posts_5050_below']]) && array_key_exists( 'maxSize', $media_queries_list[$this->param_array[$id]['posts_5050_below']] ) ) { ?>
            @media (max-width: <?php echo $media_queries_list[$this->param_array[$id]['posts_5050_below']]['maxSize']; ?>) {
                #<?php echo $this->param_array[$id]["selector"]; ?> .oxy-post {
                    width: 50% !important;
                }
            }
            <?php
        }

        if ( isset($this->param_array[$id]['posts_100_below']) && isset($media_queries_list[$this->param_array[$id]['posts_100_below']]) && array_key_exists( 'maxSize', $media_queries_list[$this->param_array[$id]['posts_100_below']] ) ) { ?>
            @media (max-width: <?php echo $media_queries_list[$this->param_array[$id]['posts_100_below']]['maxSize']; ?>) {
                #<?php echo $this->param_array[$id]["selector"]; ?> .oxy-post {
                    width: 100% !important;
                }
            }
            <?php
        }

        return ob_get_clean();
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
     * Output specific template CSS
     *
     * @since 2.0
     * @author Louis
     */

    function template_css() {

        if (!is_array($this->param_array)||empty($this->param_array)) {
            return;
        }

        foreach ($this->param_array as $params) {

            $code_css   = $params['code_css'];
            $code_css   = str_replace("%%EPID%%", "#".$params['selector'], $code_css);

            $code_css   = preg_replace_callback(
                            "/color\(\d+\)/",
                            "oxygen_vsb_parce_global_colors_callback",
                            $code_css);

            echo $code_css;
        }
    }


    /**
     * Setup the query 
     *
     * @since 2.0
     * @author Ilya K.
     */

    function get_the_posts($id) {

        if(!is_array($this->param_array)||empty($this->param_array)) {
            return;
        }

        // manual
        if ($this->param_array[$id]['query_args']&&$this->param_array[$id]['wp_query']=='manual') {

            $args = $this->param_array[$id]['query_args'];
            /* https://wordpress.stackexchange.com/questions/120407/how-to-fix-pagination-for-custom-loops 
            apparently doesn't work on static front pages? */
            $args .= get_query_var( 'paged' ) ? '&paged='.get_query_var( 'paged' ) : '';
        }

        // query builder
        elseif ($this->param_array[$id]['wp_query']=='custom') {
            
            $args = array();
            
            // post type
            if ($this->param_array[$id]['query_post_ids']) {
                $args['post__in'] = explode(",",$this->param_array[$id]['query_post_ids']);
                $args['post_type'] = 'any';
            }
            else {
                $args['post_type'] = $this->param_array[$id]['query_post_types'];
            }

            // filtering
            if (is_array($this->param_array[$id]['query_taxonomies_any'])) {
                
                $taxonomies = array();
                $args['tax_query'] = array(
                    'relation' => 'OR',
                );

                // sort IDs by taxonomy slug
                foreach ($this->param_array[$id]['query_taxonomies_any'] as $value) {
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
            if (is_array($this->param_array[$id]['query_taxonomies_all'])&&!empty($this->param_array[$id]['query_taxonomies_all'])) {
                
                $taxonomies = array();
                $args['tax_query'] = array(
                    'relation' => 'AND',
                );

                // sort IDs by taxonomy slug
                foreach ($this->param_array[$id]['query_taxonomies_all'] as $value) {
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
            if ($this->param_array[$id]['query_authors']) {
                $args['author__in'] = $this->param_array[$id]['query_authors'];
            }

            // order
            $args['order']   = $this->param_array[$id]['query_order'];
            $args['orderby'] = $this->param_array[$id]['query_order_by'];

            if ($this->param_array[$id]['query_all_posts']==='true') {
                $args['nopaging'] = true;
            }

            if ($this->param_array[$id]['query_ignore_sticky_posts']==='true') {
                $args['ignore_sticky_posts'] = true;
            }

            if ($this->param_array[$id]['query_count']) {
                $args['posts_per_page'] = $this->param_array[$id]['query_count'];
            }
            
            // pagination
            if (get_query_var('paged')&&!$this->param_array[$id]['query_count']) {
                $args['paged'] = get_query_var( 'paged' );
            }
        }
        elseif($this->param_array[$id]['wp_query']==='advanced') {
            
            include_once(CT_FW_PATH."/includes/advanced-query.php");
            
            $wp_query_advanced = $this->param_array[$id]['wp_query_advanced'];
            
            // if its not array, that means, its coming from ajax request via layouts/easy-posts.php
            if(!is_array($wp_query_advanced)) {
                $wp_query_advanced = json_decode(base64_decode($this->param_array[$id]['wp_query_advanced']), true);
            }
            
            $args = Oxy_VSB_Advanced_Query::query_args($wp_query_advanced);
            // print_r($args);
            // exit();
        }
        // default
        else {
            // use current query
            global $wp_query;
            $this->query = $wp_query;

            return;
            // $args = $wp_query->query;
            
            // // pagination
            // //$args['posts_per_page'] = $this->param_array[$options['id']]['posts_per_page'];
            // if (get_query_var('paged')) {
            //     $args['paged'] = get_query_var( 'paged' );
            // }
        }

        $this->query = new WP_Query($args);
    }


    /**
     * Basic Styles settings
     *
     * @since 2.0
     * @author Ilya K.
     */

    function settings () { 

        global $oxygen_toolbar; ?>

        <div class="oxygen-sidebar-flex-panel"
            ng-hide="!isActiveName('oxy_posts_grid')">

            <div class="oxygen-sidebar-advanced-subtab" 
                ng-click="switchTab('oxy_posts_grid', 'query')" 
                ng-show="!hasOpenTabs('oxy_posts_grid')">
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/general-config.svg">
                    <?php _e("Query", "oxygen"); ?>
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
            </div>

            <div class="oxygen-sidebar-advanced-subtab" 
                ng-click="switchTab('oxy_posts_grid', 'styles')" 
                ng-show="!hasOpenTabs('oxy_posts_grid')">
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                    <?php _e("Styles", "oxygen"); ?>
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
            </div>

            <?php if ( oxygen_vsb_get_user_edit_mode() != "edit_only" ) : ?>
            <div class="oxygen-sidebar-advanced-subtab" 
                ng-click="switchTab('oxy_posts_grid', 'templates')" 
                ng-show="!hasOpenTabs('oxy_posts_grid')">
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/code.svg">
                    <?php _e("Templates", "oxygen"); ?>
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
            </div>
            <?php endif; ?>

            <div class="oxygen-sidebar-advanced-subtab" 
                ng-click="switchTab('oxy_posts_grid', 'grid_layout')" 
                ng-show="!hasOpenTabs('oxy_posts_grid')">
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                    <?php _e("Grid Layout", "oxygen"); ?>
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
            </div>

            <div ng-if="isShowTab('oxy_posts_grid','grid_layout')"> 
                
                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="tabs.oxy_posts_grid=[]">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="tabs.oxy_posts_grid=[]"><?php _e("Easy Posts","oxygen"); ?></div>
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

            <div ng-if="isShowTab('oxy_posts_grid','styles')">
                
                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="tabs.oxy_posts_grid=[]">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="tabs.oxy_posts_grid=[]"><?php _e("Easy Posts","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Styles","oxygen"); ?></div>
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('oxy_posts_grid', 'title')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                        <?php _e("Title", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('oxy_posts_grid', 'meta')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                        <?php _e("Meta", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('oxy_posts_grid', 'content')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                        <?php _e("Content", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('oxy_posts_grid', 'readMore')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                        <?php _e("Read More", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('oxy_posts_grid', 'responsive')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                        <?php _e("Responsive", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

            </div>

            <div ng-if="isShowTab('oxy_posts_grid', 'title')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('oxy_posts_grid', 'styles')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('oxy_posts_grid', 'styles')"><?php _e("Styles","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Title","oxygen"); ?></div>
                </div>
                
                <div class='oxygen-control-row'>
                    <?php $oxygen_toolbar->measure_box_with_wrapper('title_size',__('Font size','oxygen')); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("title_color", __("Color", "oxygen") ); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("title_hover_color", __("Hover Color", "oxygen") ); ?>
                </div>

            </div>

            <div ng-if="isShowTab('oxy_posts_grid', 'meta')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('oxy_posts_grid', 'styles')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('oxy_posts_grid', 'styles')"><?php _e("Styles","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Meta","oxygen"); ?></div>
                </div>
                
                <div class='oxygen-control-row'>
                    <?php $oxygen_toolbar->measure_box_with_wrapper('meta_size',__('Font size','oxygen')); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("meta_color", __("Color", "oxygen") ); ?>
                </div>

            </div>

            <div ng-if="isShowTab('oxy_posts_grid', 'content')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('oxy_posts_grid', 'styles')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('oxy_posts_grid', 'styles')"><?php _e("Styles","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Content","oxygen"); ?></div>
                </div>
                
                <div class='oxygen-control-row'>
                    <?php $oxygen_toolbar->measure_box_with_wrapper('content_size',__('Font size','oxygen')); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("content_color", __("Color", "oxygen") ); ?>
                </div>

            </div>

            <div ng-if="isShowTab('oxy_posts_grid', 'readMore')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('oxy_posts_grid', 'styles')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('oxy_posts_grid', 'styles')"><?php _e("Styles","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Read More","oxygen"); ?></div>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Display as","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('read_more_display_as','button'); ?>
                                <?php $oxygen_toolbar->button_list_button('read_more_display_as','text link'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class='oxygen-control-row'>
                    <?php $oxygen_toolbar->measure_box_with_wrapper('read_more_size',__('Font size','oxygen')); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("read_more_text_color", __("Text Color", "oxygen") ); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("read_more_text_hover_color", __("Text Hover Color", "oxygen") ); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("read_more_button_color", __("Button Color", "oxygen") ); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("read_more_button_hover_color", __("Button Hover Color", "oxygen") ); ?>
                </div>

            </div>

            <div ng-if="isShowTab('oxy_posts_grid', 'responsive')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('oxy_posts_grid', 'styles')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('oxy_posts_grid', 'styles')"><?php _e("Styles","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Responsive","oxygen"); ?></div>
                </div>

                <?php $oxygen_toolbar->media_queries_list_with_wrapper("posts_5050_below", __("Posts are 50% Width Below","oxygen"), true); ?>
                
                <?php $oxygen_toolbar->media_queries_list_with_wrapper("posts_100_below", __("Posts are 100% Width Below","oxygen"), true); ?>

            </div>

            <div ng-if="isShowTab('oxy_posts_grid','templates')">
                
                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="tabs.oxy_posts_grid=[]">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="tabs.oxy_posts_grid=[]"><?php _e("Easy Posts","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Templates","oxygen"); ?></div>
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('oxy_posts_grid', 'templatePHP');expandSidebar();">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/phphtml.svg">
                        <?php _e("Template PHP", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('oxy_posts_grid', 'templateCSS');expandSidebar();">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/css.svg">
                        <?php _e("Template CSS", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('oxy_posts_grid', 'templatePHP')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('oxy_posts_grid', 'templates')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('oxy_posts_grid', 'templates')"><?php _e("Templates","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("PHP","oxygen"); ?></div>
                </div>

                <div class="oxygen-sidebar-code-editor-wrap">
                    <?php 
                        global $oxygen_toolbar;
                        $oxygen_toolbar->codemirror6_script("code-php","oxy-code-php-cm6","php");
                    ?>
                    <div id="oxy-code-php-cm6" class="oxy-code-cm6"></div>
                </div>

                <div class="oxygen-control-row oxygen-control-row-bottom-bar oxygen-control-row-bottom-bar-code-editor">
                    <?php global $oxygen_toolbar; 
                        $oxygen_toolbar->codemirror_theme_chooser(); ?>
                    <a href="#" class="oxygen-code-editor-apply"
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_easy_posts')">
                        <?php _e("Apply Code", "oxygen"); ?>
                    </a>
                    <a href="#" class="oxygen-code-editor-expand"
                        data-collapse="<?php _e("Collapse Editor", "oxygen"); ?>" data-expand="<?php _e("Expand Editor", "oxygen"); ?>"
                        ng-click="toggleSidebar()">
                        <?php _e("Expand Editor", "oxygen"); ?>
                    </a>
                </div>

            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('oxy_posts_grid', 'templateCSS')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('oxy_posts_grid', 'templates')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('oxy_posts_grid', 'templates')"><?php _e("Templates","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("CSS","oxygen"); ?></div>
                </div>

                <div class="oxygen-sidebar-code-editor-wrap">
                    <?php 
                        global $oxygen_toolbar;
                        $oxygen_toolbar->codemirror6_script("code-css","oxy-code-css-cm6","css");
                    ?>
                    <div id="oxy-code-css-cm6" class="oxy-code-cm6"></div>
                </div>

                <div class="oxygen-control-row oxygen-control-row-bottom-bar oxygen-control-row-bottom-bar-code-editor">
                    <?php global $oxygen_toolbar; 
                        $oxygen_toolbar->codemirror_theme_chooser(); ?>
                    <a href="#" class="oxygen-code-editor-apply"
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_easy_posts')">
                        <?php _e("Apply Code", "oxygen"); ?>
                    </a>
                    <a href="#" class="oxygen-code-editor-expand"
                        data-collapse="<?php _e("Collapse Editor", "oxygen"); ?>" data-expand="<?php _e("Expand Editor", "oxygen"); ?>"
                        ng-click="toggleSidebar()">
                        <?php _e("Expand Editor", "oxygen"); ?>
                    </a>
                </div>

            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('oxy_posts_grid', 'query')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="tabs.oxy_posts_grid=[]">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="tabs.oxy_posts_grid=[]"><?php _e("Easy Posts","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Query","oxygen"); ?></div>
                </div>

                <div class='oxygen-control-row'>
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
                    Oxy_VSB_Advanced_Query::controls('oxy_posts_grid');
                ?>

                <div class='oxygen-control-row'
                    ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query']=='manual'">
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

                <div ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query']=='custom'">
                    
                    <div class="oxygen-sidebar-advanced-subtab" 
                        ng-click="switchTab('oxy_posts_grid', 'postType')">
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                            <?php _e("Post Type", "oxygen"); ?>
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                    </div>

                    <div class="oxygen-sidebar-advanced-subtab" 
                        ng-click="switchTab('oxy_posts_grid', 'filtering')">
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                            <?php _e("Filtering", "oxygen"); ?>
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                    </div>

                    <div class="oxygen-sidebar-advanced-subtab" 
                        ng-click="switchTab('oxy_posts_grid', 'order')">
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                            <?php _e("Order", "oxygen"); ?>
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                    </div>

                    <div class="oxygen-sidebar-advanced-subtab" 
                        ng-click="switchTab('oxy_posts_grid', 'count')">
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                            <?php _e("Count", "oxygen"); ?>
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                    </div>

                </div>

                <div class="oxygen-control-row oxygen-control-row-bottom-bar">
                    <a href="#" class="oxygen-apply-button"
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_easy_posts')">
                        <?php _e("Apply Query Params", "oxygen"); ?>
                    </a>
                </div>

            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('oxy_posts_grid', 'postType')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('oxy_posts_grid', 'query')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('oxy_posts_grid', 'query')"><?php _e("Query","oxygen"); ?></div>
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
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_posts_grid','query_post_types')">
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
                                    ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_posts_grid','query_post_ids')">
                            </div>
                        </div>
                    </div>

                </div>
                    
                <div class="oxygen-control-row oxygen-control-row-bottom-bar">
                    <a href="#" class="oxygen-apply-button"
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_easy_posts')">
                        <?php _e("Apply Query Params", "oxygen"); ?>
                    </a>
                </div>

            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('oxy_posts_grid', 'filtering')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('oxy_posts_grid', 'query')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('oxy_posts_grid', 'query')"><?php _e("Query","oxygen"); ?></div>
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
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_posts_grid','<?php echo $key; ?>')">
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
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_posts_grid','query_authors')">
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
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_easy_posts')">
                        <?php _e("Apply Query Params", "oxygen"); ?>
                    </a>
                </div>

            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('oxy_posts_grid', 'order')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('oxy_posts_grid', 'query')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('oxy_posts_grid', 'query')"><?php _e("Query","oxygen"); ?></div>
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
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_easy_posts')">
                        <?php _e("Apply Query Params", "oxygen"); ?>
                    </a>
                </div>

            </div>


            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('oxy_posts_grid', 'count')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('oxy_posts_grid', 'query')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('oxy_posts_grid', 'query')"><?php _e("Query","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Count","oxygen"); ?></div>
                </div>

                <div class="oxygen-control-row">
                    <label class='oxygen-control-label'><?php _e("How Many Posts?", "oxygen"); ?></label>
                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class="oxygen-checkbox">
                            <input type="checkbox"
                                ng-true-value="'true'" 
                                ng-false-value="'false'"
                                ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['query_all_posts']"
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_posts_grid','query_all_posts')">
                            <div class='oxygen-checkbox-checkbox'
                                ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('query_all_posts')=='true'}">
                                <?php _e("All","oxygen"); ?>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class="oxygen-checkbox">
                            <input type="checkbox"
                                ng-true-value="'true'" 
                                ng-false-value="'false'"
                                ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['query_ignore_sticky_posts']"
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_posts_grid','query_ignore_sticky_posts')">
                            <div class='oxygen-checkbox-checkbox'
                                ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('query_ignore_sticky_posts')=='true'}">
                                <?php _e("Ignore Sticky Posts","oxygen"); ?>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class='oxygen-control-wrapper'>
                    <label class='oxygen-control-label'><?php _e("or specify the number", "oxygen"); ?></label>
                    <div class='oxygen-control'>
                        <div class='oxygen-input'>
                            <input type="text" spellcheck="false"
                                ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['query_count']"
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_posts_grid','query_count')">
                        </div>
                    </div>
                </div>

                <div class="oxygen-control-row oxygen-control-row-bottom-bar">
                    <a href="#" class="oxygen-apply-button"
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_easy_posts')">
                        <?php _e("Apply Query Params", "oxygen"); ?>
                    </a>
                </div>
            </div>

        </div>

    <?php }

}

// Create component instance
global $oxygen_vsb_components;
$oxygen_vsb_components['easy_posts'] = new Oxygen_VSB_Easy_Posts( array(
            'name'  => __('Easy Posts','oxygen'),
            'tag'   => 'oxy_posts_grid',
            'advanced'  => array(
                "positioning" => array(
                        "values"    => array (
                            'width'      => '100',
                            'width-unit' => '%',
                            )
                    ),
                'grid' => array(
						'values' 	=> array (
                            'grid-columns-auto-fit' => '',
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
                        "template" => 'grid-image-standard',
                        "code_php" => '',
                        "code_css" => '',
                        "wp_query" => 'default',
                        "query_args" => 'author_name=admin&category_name=uncategorized&posts_per_page=2',
                        // styles
                        "title_size" => '',
                        "title_size-unit" => 'px',
                        "title_color" => '',
                        "title_hover_color" => '',
                        "meta_size" => '',
                        "meta_size-unit" => 'px',
                        "meta_color" => '',
                        "content_size" => '',
                        "content_size-unit" => 'px',
                        "content_color" => '',
                        "read_more_display_as" => 'button',
                        "read_more_size" => '',
                        "read_more_size-unit" => 'px',
                        "read_more_text_color" => '',
                        "read_more_text_hover_color" => '',
                        "read_more_button_color" => '',
                        "read_more_button_hover_color" => '',
                        "paginate_color" => '',
                        "paginate_alignment" => '',
                        "paginate_link_color" => '',
                        "paginate_link_hover_color" => '',
                        "posts_per_page" => '',
                        "posts_5050_below" => '',
                        "posts_100_below" => '',
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
                    )
                )
            ),
            'not_css_params' => array(
                        "template",
                        "wp_query",
                        "query_args",
                        "title_size",
                        "title_size-unit",
                        "title_color",
                        "title_hover_color",
                        "meta_size",
                        "meta_size-unit",
                        "meta_color",
                        "content_size",
                        "content_size-unit",
                        "content_color",
                        "read_more_display_as",
                        "read_more_size",
                        "read_more_size-unit",
                        "read_more_text_color",
                        "read_more_text_hover_color",
                        "read_more_button_color",
                        "read_more_button_hover_color",
                        "paginate_color",
                        "paginate_alignment",
                        "paginate_link_color",
                        "paginate_link_hover_color",
                        "posts_per_page",
                        "posts_5050_below",
                        "posts_100_below",
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
            )
        ));