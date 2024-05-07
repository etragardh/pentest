<?php

/**
 * Comment List Component Class
 * 
 * @since 2.0
 */

define("OXYGEN_VSB_COMMENTS_LIST_TEMPLATES_PATH", plugin_dir_path(__FILE__)."comments-list-templates/");

class Oxygen_VSB_Comments_List extends CT_Component {

    public $param_array;

    function __construct($options) {

        // run initialization
        $this->init( $options );

        // Add shortcodes
        add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
        add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

        // change component button place
        remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
        add_action("oxy_folder_wordpress_components", array( $this, "component_button" ) );

        // output styles
        add_filter("ct_footer_styles", array( $this, "template_css" ) );

        // add specific options to Basic Styles tab
        add_action("ct_toolbar_component_settings", array( $this, "settings") );

        // render preveiew with AJAX
        add_filter("template_include", array( $this, "single_template"), 100 );
        
        add_filter("oxygen_vsb_element_presets_defaults", array( $this, "presets_defaults") );
        add_filter("oxygen_elements_with_full_presets", array($this, "oxygen_elements_with_full_presets_callback"));
    }

    function presets_defaults($all_elements_defaults) {
        
        require("comments-list-templates/comments-list-presets.php");
        
        $all_elements_defaults = array_merge($all_elements_defaults, $comments_list_defaults);

        return $all_elements_defaults;
    }

    
    /**
     * Add a [oxy_comments] shortcode to WordPress
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

        $this->param_array = shortcode_atts(
            array(
                "template" => 'default',
                "code_php" => '',
                "code_css" => '',
            ), $options, $this->options['tag'] );

        $this->param_array["selector"] = esc_attr($options['selector']);

        // make sure errors are shown
        $error_reporting = error_reporting(E_ERROR | E_WARNING | E_PARSE);
        $display_errors = ini_get('display_errors');
        ini_set('display_errors', 1); 
        $output = '';
        ob_start(); ?>

        <?php if (!isset($atts['preview']) || !$atts['preview']) : ?>
        <div id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>>
        <?php endif;

            $GLOBALS['Oxygen_VSB_Current_Comments_Class'] = $this;

            add_filter( "comments_template", array($this, 'comments_template') );
            comments_template('Louis Reingold is the best human to ever live.');
            remove_filter( "comments_template", array($this, 'comments_template') );

            unset($GLOBALS['Oxygen_VSB_Current_Comments_Class']);

        if (!isset($atts['preview']) || !$atts['preview']) : ?>
        </div>
        <?php endif; 
        $output = ob_get_clean();
        if(empty(trim($output))) {
            $output = '<div class="oxygen-empty-comments-list"></div>';
        }
        
        // set errors params back
        ini_set('display_errors', $display_errors); 
        error_reporting($error_reporting);

        ob_start();
        
        // output template CSS for builder preview only
        if ((isset($atts['preview']) && $atts['preview']=='true') || ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == "ct_get_post_data" ) ) {
            echo ($code_css ?? "")."<style>";
            $code_css   = $this->param_array['code_css'];
            $code_css   = str_replace("%%ELEMENT_ID%%", $options['selector'], $code_css);
            $code_css   = preg_replace_callback(
                                "/color\(\d+\)/",
                                "oxygen_vsb_parce_global_colors_callback",
                                $code_css);

            echo $code_css."</style>";
        }

        return $output.ob_get_clean();
    }

    
    /**
     * Output specific template CSS
     *
     * @since 2.0
     * @author Louis
     */

    function comments_template( $comment_template ) {

        return plugin_dir_path(__FILE__)."comments-list-templates/comments.php";
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

        // required default styles

        $code_css   = $this->param_array['code_css'];
        $code_css   = str_replace("%%ELEMENT_ID%%", isset($this->options['selector'])?$this->options['selector']:'', $code_css);
        $code_css   = preg_replace_callback(
                                "/color\(\d+\)/",
                                "oxygen_vsb_parce_global_colors_callback",
                                $code_css);

        echo $code_css;
    }

    
    /**
     * Output comments title
     *
     * @since 2.0
     * @author Louis
     */

    static function util_title() {

        if (get_comments_number() == 1) {
            return sprintf(__('One comment on &#8220;%s&#8221;'), get_the_title());
        } else {
            return number_format_i18n(get_comments_number()).sprintf(__(' comments on &#8220;%s&#8221;'), get_the_title());
        }

    }


    /**
     * Basic Styles settings
     *
     * @since 2.0
     * @author Ilya K.
     */

    function settings () { 

        if ( oxygen_vsb_get_user_edit_mode() == "edit_only" ) {
            return;
        }

        global $oxygen_toolbar; ?>

        <div class="oxygen-sidebar-flex-panel"
            ng-hide="!isActiveName('oxy_comments')">

            <div class="oxygen-sidebar-advanced-subtab" 
                ng-click="switchTab('commentsList', 'templates')" 
                ng-show="!hasOpenTabs('commentsList')">
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/code.svg">
                    <?php _e("Templates", "oxygen"); ?>
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
            </div>

            <div ng-if="isShowTab('commentsList','templates')">
                
                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="tabs.commentsList=[]">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="tabs.commentsList=[]"><?php _e("Comments List","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Templates","oxygen"); ?></div>
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('commentsList', 'templatePHP');expandSidebar();">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/phphtml.svg">
                        <?php _e("Template PHP", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('commentsList', 'templateCSS');expandSidebar();">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/css.svg">
                        <?php _e("Template CSS", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('commentsList', 'templatePHP')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('commentsList', 'templates')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('commentsList', 'templates')"><?php _e("Templates","oxygen"); ?></div>
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
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_comments_list')">
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
                ng-if="isShowTab('commentsList', 'templateCSS')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('commentsList', 'templates')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('commentsList', 'templates')"><?php _e("Templates","oxygen"); ?></div>
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
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_comments_list')">
                        <?php _e("Apply Code", "oxygen"); ?>
                    </a>
                    <a href="#" class="oxygen-code-editor-expand"
                        data-collapse="<?php _e("Collapse Editor", "oxygen"); ?>" data-expand="<?php _e("Expand Editor", "oxygen"); ?>"
                        ng-click="toggleSidebar()">
                        <?php _e("Expand Editor", "oxygen"); ?>
                    </a>
                </div>

            </div>

        </div>

    <?php }


    /**
     * This function hijacks the template to return special template that renders the code results
     * for the [oxy_comments] element to load the content into the builder for preview
     * 
     * @since 0.4.0
     * @author gagan goraya
     */
    
    function single_template( $template ) {

        $new_template = '';

        if( isset($_REQUEST['action']) && stripslashes($_REQUEST['action']) == 'oxy_render_comments_list') {
            
            if ( file_exists(dirname(dirname( __FILE__)) . '/layouts/' . 'comments-list.php') ) {
                $new_template = dirname(dirname( __FILE__)) . '/layouts/' . 'comments-list.php';
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
$oxygen_vsb_components['comments_list'] = new Oxygen_VSB_Comments_List( array(
            'name'  => __('Comments List','oxygen'),
            'tag'   => 'oxy_comments',
            'advanced'  => array(
                "positioning" => array(
                        "values"    => array (
                            'width'      => '100',
                            'width-unit' => '%',
                            )
                    ),
                "typography" => array(
                    "values" => array(
                        "text-align" => 'left',
                    )
                ),
                "other" => array(
                    "values" => array(
                        "template" => 'default',
                        "code_php" => '',
                        "code_css" => '',
                    )
                )
            ),
            'not_css_params' => array(
                "template",
            )
        ));