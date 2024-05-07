<?php

class SiteNav extends OxyEl
{

    // var $js_added = false;

    function name()
    {
        return __('Site Navigation');
    }

    function enableFullPresets()
    {
        return true;
    }

    function icon()
    {
        return CT_FW_URI . '/toolbar/UI/oxygen-icons/add-icons/pro-menu.svg';
    }

    function button_place()
    {
        return "wordpress";
    }

    function tag()
    {
        return "nav";
    }

    function keywords() 
    {
        return "menu,nav,navigation";
    }

    function attributes()
    {
        return array(
            array(
                'name' => 'x-data',
                'value' => 'oxyA11yMenuData'
            )
        );
    }
    
    function dynamic_attributes( $options )
    {
        return array(
            array(
                'name' => 'aria-label',
                'value' => $options['navigation_title']
            ),
        );
    }

    function button_priority()
    {
        return 9;
    }

    function init()
    {

        add_action("oxygen_default_classes_output", array( $this->El, "generate_defaults_css" ) );
        if( !is_admin() ) {
            add_filter('wp_get_nav_menu_items', array($this, 'oxy_prefix_nav_menu_classes'), 10, 3);
        }
        add_filter('wp_get_nav_menu_items', array($this, 'oxy_output_nav_menu_meta'), 10, 3);

        // Menu modifications for use with the new Site Nav element
        add_action('wp_nav_menu_item_custom_fields', function ($item_id, $item) {
            $menu_item_description = get_post_meta($item_id, '_menu_item_description', true);
            $menu_item_image = get_post_meta($item_id, '_menu_item_image', true);

        ?>
            <p class="description description-wide">
                <label for="edit-menu-item-description-<?= $item_id; ?>">
                    Description<br>
                    <input type="text" id="edit-menu-item-description-<?= $item_id; ?>" class="widefat edit-menu-item-description" name="menu-item-description[<?= $item_id; ?>]" value="<?= $menu_item_description; ?>">
                </label>
            </p>
            <p class="description description-wide">
                <label for="edit-menu-item-image-<?= $item_id; ?>">
                    Image<br>
                    <input type="text" id="edit-menu-item-image-<?= $item_id; ?>" class="widefat edit-menu-item-image" name="menu-item-image[<?= $item_id; ?>]" value="<?= $menu_item_image; ?>">
                </label>
            </p>
        <?php
        }, 10, 2);

        add_action('wp_update_nav_menu_item', function ($menu_id, $menu_item_db_id) {
            //$field_value = $_POST['menu-item-description[' . $menu_item_db_id . ']'];
            if (isset($_POST['menu-item-description'][$menu_item_db_id])) {
                $desc_field_value = $_POST['menu-item-description'][$menu_item_db_id];
            }
            else {
                $desc_field_value = "";
            }
            update_post_meta($menu_item_db_id, '_menu_item_description', $desc_field_value);

            if (isset($_POST['menu-item-image'][$menu_item_db_id])) {
                $img_field_value = $_POST['menu-item-image'][$menu_item_db_id];
            }
            else {
                $img_field_value = "";
            }
            update_post_meta($menu_item_db_id, '_menu_item_image', $img_field_value);
        
        }, 10, 2);

    }

    function presetsDefaults($defaults)
    {

        // $default_pro_menu_presets = array();

        // include("menu-pro/menu-pro-default-presets.php");

        // $defaults = array_merge($defaults, $default_pro_menu_presets);

        // return $defaults;
    }

    function afterInit()
    {
        $this->removeApplyParamsButton();
    }

    function allowedEmptyOptions($options)
    {
    }

    function customCSS($options, $selector)
    {
        $css = "";

        return $css;
    }

    function controls()
    {
        // Menu selection
        $menus = get_terms('nav_menu', array('hide_empty' => true));
        $menusRefined = array();

        foreach ($menus as $menu) {
			if (is_object($menu)) {
            	array_push($menusRefined, $menu->name);
			}
        }

        $this->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __("Navigation Title"),
                "slug" => 'navigation_title',
                "default" => "Main"
            )
        );

        // WordPress menu selection
        $this->addOptionControl(
            array(
                "type" => 'dropdown',
                "name" => __("WordPress Menu"),
                "slug" => 'wordpress_menu',
            )
        )->setValue($menusRefined)->rebuildElementOnChange();

        $mobile_settings = $this->addControlsection("mobile_settings", __("Mobile Settings"), "assets/icon.png", $this);

        $mobile_menu_below = $mobile_settings->addOptionControl(
            array(
                "name" => __('Switch To Mobile Menu At', 'oxygen'),
                "slug" => 'show_mobile_menu_below',
                "type" => 'medialist',
            )
        );
        $mobile_menu_below->setParam("always_option", true);
        $mobile_menu_below->rebuildElementOnChange();

        $mobile_settings->addOptionControl(
            array(
                "type" => "buttons-list",
                "name" => __("Mobile Menu Position"),
                "slug" => "mobile_menu_position",
                "default" => "left",
                "condition" => "show_mobile_menu_below!=never"
            )
        )->setValue(
            array(
                "left" => "Left",
                "right" => "Right"
            )
        )->setValueCSS(
            array(
                "left" => " > ul { left: 0; }",
                "right" => " > ul { left: unset; right: 0; }"
            )
        )->whitelist();

        // Color controls
        $general_styles = $this->addControlsection("general_styles", __("General Styles"), "assets/icon.png", $this);

        $general_styles->addOptionControl(
            array(
                "name" => __("Colors"),
                "slug" => "layout_colors_label",
                "type" => "label"
            )
        );

        $general_styles->addStyleControl(
            array(
                "name" => __('Primary Color'),
                "selector" => "",
                "property" => '--oxynav-brand-color',
                "control_type" => "colorpicker",
            )
        )->setValue('#4831B0');

        $general_styles->addStyleControl(
            array(
                "name" => __('Neutral Color'),
                "selector" => "",
                "property" => '--oxynav-neutral-color',
                "control_type" => "colorpicker",
            )
        )->setValue('#FFFFFF');

        $general_styles->addStyleControl(
            array(
                "name" => __('Active / Hover Color'),
                "selector" => "",
                "property" => '--oxynav-activehover-color',
                "control_type" => "colorpicker",
            )
        )->setValue('#EFEDF4');

        $general_styles->addStyleControl(
            array(
                "name" => __('Background Color'),
                "selector" => "",
                "property" => '--oxynav-background-color',
                "control_type" => "colorpicker"
            )
        )->setValue('#4831B0');

        $general_styles->addOptionControl(
            array(
                "name" => __("Radius & Spacing"),
                "slug" => "layout_radiusspacing_label",
                "type" => "label"
            )
        );

        $general_styles->addStyleControl(
            array(
                "name" => __('Border Radius'),
                "selector" => "",
                "property" => '--oxynav-border-radius',
                "control_type" => "measurebox",
                "unit" => "px"
            )
        )->setValue('0');

        $general_styles->addStyleControl(
            array(
                "name" => __('Spacing'),
                "selector" => "",
                "property" => '--oxynav-other-spacing',
                "control_type" => "measurebox",
                "unit" => "px"
            )
        )->setValue('8');

        $general_styles->addOptionControl(
            array(
                "name" => __("Add Space Between Items"),
                "slug" => "add_space_between_items",
                "type" => "checkbox"
            )
        )->setValueCSS(
            array(
                "true" => "
                    > ul {
                        gap: var(--oxynav-other-spacing);
                    }
                ",
                "false" => ""
            )
        )->whitelist();

        $general_styles->addOptionControl(
            array(
                "name" => __("Other Options"),
                "slug" => "layout_otheroptions_label",
                "type" => "label"
            )
        );

        $general_styles->addOptionControl(
            array(
                "name" => __("Use Transparent Background For Top Level Items"),
                "slug" => "transparent_background_on_top_level",
                "type" => "checkbox"
            )
        )->setValueCSS(
            array(
                "true" => "
                    > ul > li {
                        background: none;
                    }
                ",
                "false" => ""
            )
        )->whitelist();

        $general_styles->addOptionControl(
            array(
                "name" => __("Disable 'Current' Menu Item Underline"),
                "slug" => "disable_current_menu_item_styles",
                "type" => "checkbox",
                "default" => "false"
            )
        )->setValueCSS(
            array(
                "true" => "
                     > ul > .current-menu-ancestor > a::before,
                     > ul > .current-menu-item > a::before {
                        display: none;
                    }
                ",
                "false" => ""
            )
        )->whitelist();

        $general_animation_styles = $general_styles->addControlsection("general_styles_animation", __("Animation"), "assets/icon.png", $this);

        $general_animation_styles->addOptionControl(
            array(
                "name" => __('Style', 'oxygen'),
                "slug" => 'animation_style',
                "type" => 'dropdown',
                "default" => "None"
            )
        )->setValue(
            array(
                "Slide Up",
                "Dropdown",
                "Scale",
                "None"
            )
        )->setValueCSS(
            array(
                "Dropdown" => "
                    {
                        --oxynav-animation-name: oxel-sitenav-dropdown; 
                    }
                ",
                "Slide Up" => "
                    {
                        --oxynav-animation-name: oxel-sitenav-slideup;
                    }
                ",
                "Scale" => "
                    {
                        --oxynav-animation-name: oxel-sitenav-scale;
                    }
                ",
                "None" => "
                    {
                        --oxynav-animation-name: none; 
                    }
                ",

            )
        )->whitelist();

        $general_animation_styles->addStyleControl(
            array(
                "name" => __('Duration', 'oxygen'),
                "slug" => 'animation_duration',
                "type" => 'measurebox',
                "unit" => 's',
                "default" => "0.3",
                "selector" => "",
                "property" => "--oxynav-transition-duration"
            )
        )->whitelist();

        $general_animation_styles->addStyleControl(
            array(
                "name" => __('Timing Function'),
                "slug" => 'animation_timing_function',
                "type" => 'text',
                "default" => "cubic-bezier(.84,.05,.31,.93)",
                "selector" => "",
                "property" => "--oxynav-transition-timing-function"
            )
        )->whitelist();

        $cta_styles = $this->addControlSection('cta_styles', __('CTA Styles'), 'assets/icon.png', $this);

        $use_cta = $cta_styles->addOptionControl(
            array(
                "type" => 'checkbox',
                "name" => __('Style Last Item(s) As CTA'),
                "slug" => 'style_last_item_as_cta',
                "default" => "false"
            )
        );

        $use_cta->rebuildElementOnChange();

        $cta_styles->addOptionControl(
            array(
                "name" => __("How Many CTAs?"),
                "slug" => "how_many_ctas",
                "type" => "buttons-list",
                "condition" => "style_last_item_as_cta=true",
                "default" => "1"
            )
        )->setValue(
            array(
                "1" => "One",
                "2" => "Two"
            )
        )->rebuildElementOnChange();

        $cta_style = $cta_styles->addOptionControl(
            array(
                "name" => __("CTA Style"),
                "slug" => "cta_style",
                "type" => "buttons-list",
                "condition" => "style_last_item_as_cta=true&&how_many_ctas=2",
                "default" => "solidoutline"
            )
        )->setValue(
            array(
                "solidoutline" => "Solid/Outline",
                "outlinesolid" => "Outline/Solid"
            )
        );

        $cta_style = $cta_styles->addOptionControl(
            array(
                "name" => __("CTA Style"),
                "slug" => "cta_style",
                "type" => "buttons-list",
                "condition" => "style_last_item_as_cta=true&&how_many_ctas=1",
                "default" => "outlinesolid"
            )
        )->setValue(
            array(
                "solidoutline" => "Outline",
                "outlinesolid" => "Solid"
            )
        );
        
        $cta_style->setValueCSS(
            array(
                "solidoutline" => "
                > ul:not(.open) > li[data-cta='true']:nth-last-child(2) { 
                    background-color: var(--oxynav-brand-color);
                    transition: var(--oxynav-transition-duration);
                    margin-left: var(--oxynav-other-spacing);
                    border: none;
                }

                > ul:not(.open) > li[data-cta='true']:nth-last-child(1) { 
                    background: transparent;
                    border: 1px solid currentColor;
                    transition: var(--oxynav-transition-duration);
                    margin-left: var(--oxynav-other-spacing);
                }

                > ul:not(.open) > li[data-cta='true']:nth-last-child(1):not(:hover) > img {
                    filter: invert(0) !important;
                }

                > ul:not(.open) > li[data-cta='true']:hover {
                    background-color: var(--oxynav-activehover-color);
                }
                
                > ul:not(.open) > li[data-cta='true']:nth-last-child(2) > a {
                    color: var(--oxynav-neutral-color);
                }

                > ul:not(.open) > li[data-cta='true']:nth-last-child(1) > a {
                    color: var(--oxynav-brand-color);
                }

                > ul:not(.open) > li[data-cta='true']:hover > a {
                    color: var(--oxynav-brand-color);
                }
                
                > ul:not(.open) > li[data-cta='true']:nth-last-child(2) > a::after {
                    color: var(--oxynav-neutral-color);   
                }

                > ul:not(.open) > li[data-cta='true']:nth-last-child(1) > a::after {
                    color: var(--oxynav-brand-color);   
                }

                > ul:not(.open) > li[data-cta='true']:hover > a::after {
                    color: var(--oxynav-brand-color);   
                }
                
                > ul:not(.open) > li[data-cta='true'] > ul {
                    display: none;
                }
                
                > ul:not(.open) > li[data-cta='true'] > button,
                > ul:not(.open) > li[data-cta='true'] > ul {
                    display: none;
                }
                ",
                "outlinesolid" => "
                > ul:not(.open) > li[data-cta='true']:nth-last-child(1) { 
                    background-color: var(--oxynav-brand-color);
                    transition: var(--oxynav-transition-duration);
                    margin-left: var(--oxynav-other-spacing);
                    border: none;
                }

                > ul:not(.open) > li[data-cta='true']:nth-last-child(2) { 
                    background: transparent;
                    border: 1px solid currentColor;
                    transition: var(--oxynav-transition-duration);
                    margin-left: var(--oxynav-other-spacing);
                }

                > ul:not(.open) > li[data-cta='true']:nth-last-child(2):not(:hover) > img {
                    filter: invert(0) !important;
                }

                > ul:not(.open) > li[data-cta='true']:hover {
                    background-color: var(--oxynav-activehover-color);
                }
                
                > ul:not(.open) > li[data-cta='true']:nth-last-child(1) > a {
                    color: var(--oxynav-neutral-color);
                }

                > ul:not(.open) > li[data-cta='true']:nth-last-child(2) > a {
                    color: var(--oxynav-brand-color);
                }

                > ul:not(.open) > li[data-cta='true']:hover > a {
                    color: var(--oxynav-brand-color);
                }
                
                > ul:not(.open) > li[data-cta='true']:nth-last-child(1) > a::after {
                    color: var(--oxynav-neutral-color);   
                }

                > ul:not(.open) > li[data-cta='true']:nth-last-child(2) > a::after {
                    color: var(--oxynav-brand-color);   
                }

                > ul:not(.open) > li[data-cta='true']:hover > a::after {
                    color: var(--oxynav-brand-color);   
                }
                
                > ul:not(.open) > li[data-cta='true'] > ul {
                    display: none;
                }
                
                > ul:not(.open) > li[data-cta='true'] > button,
                > ul:not(.open) > li[data-cta='true'] > ul {
                    display: none;
                }
                "
            )
        )->whitelist();;

        $cta_styles->addOptionControl(
            array(
                "name" => __("Colors"),
                "slug" => "layout_colors_label",
                "type" => "label",
                "condition" => "style_last_item_as_cta=true"
            )
        );

        $cta_styles->addStyleControl(
            array(
                "control_type" => 'colorpicker',
                "name" => __('Background'),
                "slug" => 'cta_bg_color',
                "selector" => " > ul:not(.open) > li.menu-item[data-cta='true']:nth-last-child(1),  > ul:not(.open) > li.menu-item[data-cta='true']:nth-last-child(2)",
                "property" => "background-color",
                "condition" => "style_last_item_as_cta=true"
            )
        )->whitelist();

        $cta_styles->addStyleControl(
            array(
                "control_type" => 'colorpicker',
                "name" => __('Color'),
                "slug" => 'cta_color',
                "selector" => "
                    > ul:not(.open) > li.menu-item[data-cta='true']:nth-last-child(1),
                    > ul:not(.open) > li.menu-item[data-cta='true']:nth-last-child(2),
                    > ul:not(.open) > li.menu-item[data-cta='true']:nth-last-child(1) > a,  
                    > ul:not(.open) > li.menu-item[data-cta='true']:nth-last-child(2) > a, 
                    > ul:not(.open) > li.menu-item[data-cta='true']:nth-last-child(1) > a::after,  
                    > ul:not(.open) > li.menu-item[data-cta='true']:nth-last-child(2) > a::after",
                "property" => "color",
                "condition" => "style_last_item_as_cta=true"
            )
        )->whitelist();

        $cta_styles->addOptionControl(
            array(
                "name" => __("Colors On Hover"),
                "slug" => "layout_colors_label",
                "type" => "label",
                "condition" => "style_last_item_as_cta=true"
            )
        );

        $cta_styles->addStyleControl(
            array(
                "control_type" => 'colorpicker',
                "name" => __('Background'),
                "slug" => 'cta_bg_color_hover',
                "selector" => " > ul:not(.open) > li.menu-item[data-cta='true']:hover",
                "property" => "background-color",
                "condition" => "style_last_item_as_cta=true"
            )
        )->whitelist();

        $cta_styles->addStyleControl(
            array(
                "control_type" => 'colorpicker',
                "name" => __('Color'),
                "slug" => 'cta_color_hover',
                "selector" => "
                     > ul:not(.open) > li.menu-item[data-cta='true']:hover,
                     > ul:not(.open) > li.menu-item[data-cta='true']:hover > a,
                     > ul:not(.open) > li.menu-item[data-cta='true']:hover > a::after",
                "property" => "color",
                "condition" => "style_last_item_as_cta=true"
            )
        )->whitelist();

        $typographySection = $this->addControlSection("typography_settings", __("Typography"), "assets/icon.png", $this);

        $allTypographySection = $typographySection->addControlSection("all_typography_settings", __("All Typography"), "assets/icon.png", $this);

        $allTypographySection->addPreset(
            'typography',
            'all_one_typography',
            __('All Typography'),
            'a'
        )->whiteList();

        $levelOneTypographySection = $typographySection->addControlSection("l1_typography_settings", __("Main"), "assets/icon.png", $this);

        $levelOneTypographySection->addPreset(
            'typography',
            'level_one_typography',
            __('Level One Typography'),
            '> ul > li > a,
            > ul > li > a::after,
            > ul > li > button'
        )->whiteList();

        $levelTwoTypographySection = $typographySection->addControlSection("l2_typography_settings", __("Sub-Menu"), "assets/icon.png", $this);

        $levelTwoTypographySection->addPreset(
            'typography',
            'level_two_typography',
            __('Level Two Typography'),
            '> ul > li > ul > li > a,
            > ul > li > ul > li > a::after,
            > ul > li > ul > li > button'
        )->whiteList();

        $levelThreeTypographySection = $typographySection->addControlSection("l3_typography_settings", __("Sub Sub-Menu"), "assets/icon.png", $this);

        $levelThreeTypographySection->addPreset(
            'typography',
            'level_three_typography',
            __('Level Three Typography'),
            '> ul > li > ul > li > ul > li > a,
            > ul > li > ul > li > ul > li > a::after,
            > ul > li > ul > li > ul > li > button'
        )->whiteList();

        $descriptionTypographySection = $typographySection->addControlSection("desc_typography_settings", __("Description"), "assets/icon.png", $this);

        $descriptionTypographySection->addPreset(
            'typography',
            'desc_typography',
            __('Description Typography'),
            '> ul > li > a::after,
            > ul > li > ul > li > a::after,
            > ul > li > ul > li > ul > li > a::after'
        )->whiteList();

        $iconSection = $this->addControlSection("icon_settings", __("Icons"), "assets/icon.png", $this);

        $linkIconSection = $iconSection->addControlSection("link_icon_settings", __("Link Icons"), "assets/icon.png", $this);

        $dropdownIconSection = $iconSection->addControlSection("dropdown_icon_settings", __("Dropdown Icon"), "assets/icon.png", $this);

        $mobileOpenIconSection = $iconSection->addControlSection("mobile_open_icon_settings", __("Mobile Open Icon"), "assets/icon.png", $this);

        $mobileCloseIconSection = $iconSection->addControlSection("mobile_close_icon_settings", __("Mobile Close Icon"), "assets/icon.png", $this);

        $linkIconSection->addStyleControl(
            array(
                "control_type" => "slider-measurebox",
                "name" => __("Icon Size"),
                "slug" => "link_icon_size",
                "selector" => " li > img",
                "property" => "width"
            )
        )->whitelist();

        $linkIconSection->addOptionControl(
            array(
                "type" => "checkbox",
                "name" => __("Invert On Sub-Menus & CTAs"),
                "slug" => "link_icon_invert",
                "default" => "false"
            )
        )->setValueCSS(
            array(
                "true" => " ul ul li:not(:hover) > img,
                            > ul:not(.open) > li[data-cta='true']:not(:hover) > img {
                    filter: invert(1);
                }",
                "false" => ""
            )
        );

        $dropdownIconSection->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Dropdown Icon'),
                "slug" => 'dropdown_icon',
                "default" => 'FontAwesomeicon-chevron-down'
            )
        )->rebuildElementOnChange();

        $dropdownIconSection->addStyleControl(
            array(
                "control_type" => 'slider-measurebox',
                "name" => __('Icon Size'),
                "slug" => 'dropdown_icon_size',
                "selector" => " button:not(.oxy-site-navigation__mobile-open-button):not(.oxy-site-navigation__mobile-close-button) > svg",
                "property" => 'width'
            )
        )->whitelist();

        $dropdownIconSection->addStyleControl(
            array(
                "control_type" => 'colorpicker',
                "name" => __('Color'),
                "slug" => 'dropdown_icon_color',
                "selector" => " button:not(.oxy-site-navigation__mobile-open-button):not(.oxy-site-navigation__mobile-close-button) > svg",
                "property" => 'fill'
            )
        )->whitelist();

        $mobileOpenIconSection->addOptionControl(
            array(
                "type" => 'dropdown',
                "name" => __('Icon Type'),
                "slug" => 'mobile_open_icon_type',
                "default" => 'svg'
            )
        )->setValue(
            array(
                "svg" => "SVG",
                "css" => "CSS"
            )
        )->rebuildElementOnChange();

        $mobileOpenIconSection->addOptionControl(
            array(
                "type" => 'dropdown',
                "name" => __('Animation Type'),
                "slug" => 'mobile_open_icon_animation_type',
                "default" => 'none',
                "condition" => "mobile_open_icon_type=css"
            )
        )->setValue(
            array(
                "none" => "None",
                "sticks" => "Sticks",
                "basic" => "Basic",
                "collapse" => "Collapse",
                "dropin" => "Drop In"
            )
        )->rebuildElementOnChange();

        $mobileOpenIconSection->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Mobile Open Icon'),
                "slug" => 'mobile_open_icon',
                "default" => 'FontAwesomeicon-bars',
                "condition" => "mobile_open_icon_type=svg"
            )
        )->rebuildElementOnChange();

        $mobileOpenIconSection->addStyleControl(
            array(
                "control_type" => 'slider-measurebox',
                "name" => __('Icon Size'),
                "slug" => 'mobile_open_icon_size',
                "selector" => " button.oxy-site-navigation__mobile-open-button > svg, .oxy-site-navigation__mobile-open-button .oxy-site-navigation__css-icon",
                "property" => 'width'
            )
        )->whitelist();

        $mobileOpenIconSection->addStyleControl(
            array(
                "control_type" => 'colorpicker',
                "name" => __('Color'),
                "slug" => 'mobile_open_icon_color',
                "selector" => " button.oxy-site-navigation__mobile-open-button > svg",
                "property" => 'fill',
                "condition" => "mobile_open_icon_type=svg"
            )
        )->whitelist();

        $mobileOpenIconSection->addStyleControl(
            array(
                "control_type" => 'colorpicker',
                "name" => __('Color'),
                "slug" => 'mobile_open_icon_color',
                "selector" => " .oxy-site-navigation__mobile-open-button .oxy-site-navigation__css-icon",
                "property" => 'color',
                "condition" => "mobile_open_icon_type=css"
            )
        )->whitelist();

        $mobileCloseIconSection->addOptionControl(
            array(
                "type" => 'dropdown',
                "name" => __('Icon Type'),
                "slug" => 'mobile_close_icon_type',
                "default" => 'svg'
            )
        )->setValue(
            array(
                "svg" => "SVG",
                "css" => "CSS"
            )
        )->rebuildElementOnChange();

        $mobileCloseIconSection->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Mobile Close Icon'),
                "slug" => 'mobile_close_icon',
                "default" => 'FontAwesomeicon-close',
                "condition" => "mobile_close_icon_type=svg"
            )
        )->rebuildElementOnChange();

        $mobileCloseIconSection->addStyleControl(
            array(
                "control_type" => 'slider-measurebox',
                "name" => __('Icon Size'),
                "slug" => 'mobile_close_icon_size',
                "selector" => " button.oxy-site-navigation__mobile-close-button > svg, .oxy-site-navigation__mobile-close-button .oxy-site-navigation__css-icon",
                "property" => 'width'
            )
        )->whitelist();

        $mobileCloseIconSection->addStyleControl(
            array(
                "control_type" => 'colorpicker',
                "name" => __('Color'),
                "slug" => 'mobile_close_icon_color',
                "selector" => " button.oxy-site-navigation__mobile-close-button > svg",
                "property" => 'fill',
                "condition" => "mobile_close_icon_type=svg"
            )
        )->whitelist();

        $mobileCloseIconSection->addStyleControl(
            array(
                "control_type" => 'colorpicker',
                "name" => __('Color'),
                "slug" => 'mobile_close_icon_color',
                "selector" => " .oxy-site-navigation__mobile-close-button .oxy-site-navigation__css-icon",
                "property" => 'color',
                "condition" => "mobile_close_icon_type=css"
            )
        )->whitelist();

        $mobileCloseIconSection->addStyleControl(
            array(
                "control_type" => "buttons-list",
                "name" => "Position",
                "slug" => "mobile_close_icon_position",
                "selector" => " .oxy-site-navigation__mobile-close-wrapper",
                "property" => "text-align",
                "default" => "left"
            )
        )->setValue(
            array(
                "left" => "Left",
                "center" => "Center",
                "right" => "Right"
            )
        )->whitelist();
    }

    function render($options, $defaults, $content)
    {

        $this->El->dynamicAttributes( $this->dynamic_attributes($options) );

        $menu_items = array();

        /* I think I can just add 'menu_items_' . $options[menu_id] or something, then fetch the items on a per menu ID basis from the array. */

        if (isset($options['wordpress_menu'])) {
            $menu_items = json_encode(wp_get_nav_menu_items($options['wordpress_menu']), JSON_HEX_APOS);
        }
        else {
            $menu_items = 'false';
        }

        if ($menu_items == 'false') {
            $menu_items = '[{"ID":34,"post_author":"1","post_date":"2022-03-24 14:10:58","post_date_gmt":"2022-03-24 14:10:58","post_content":"","post_title":"No Menu Selected","post_excerpt":"","post_status":"publish","comment_status":"closed","ping_status":"closed","post_password":"","post_name":"no-menu-selected","to_ping":"","pinged":"","post_modified":"2022-03-24 14:10:58","post_modified_gmt":"2022-03-24 14:10:58","post_content_filtered":"","post_parent":0,"guid":"http:\/\/oxyalpinemenu.local\/?p=34","menu_order":1,"post_type":"nav_menu_item","post_mime_type":"","comment_count":"0","filter":"raw","db_id":34,"menu_item_parent":"0","object_id":"34","object":"custom","type":"custom","type_label":"Custom Link","title":"No Menu Selected","url":"#","target":"","attr_title":"","description":"","classes":[""],"xfn":""}]';
        }
        $menu_items = str_replace('"', "'", $menu_items);

        //$menu_items = $menu_items ? $menu_items : '[{"ID":34,"post_author":"1","post_date":"2022-03-24 14:10:58","post_date_gmt":"2022-03-24 14:10:58","post_content":"","post_title":"No Menu Selected","post_excerpt":"","post_status":"publish","comment_status":"closed","ping_status":"closed","post_password":"","post_name":"no-menu-selected","to_ping":"","pinged":"","post_modified":"2022-03-24 14:10:58","post_modified_gmt":"2022-03-24 14:10:58","post_content_filtered":"","post_parent":0,"guid":"http:\/\/oxyalpinemenu.local\/?p=34","menu_order":1,"post_type":"nav_menu_item","post_mime_type":"","comment_count":"0","filter":"raw","db_id":34,"menu_item_parent":"0","object_id":"34","object":"custom","type":"custom","type_label":"Custom Link","title":"No Menu Selected","url":"#","target":"","attr_title":"","description":"","classes":[""],"xfn":""}]';
        $desktop_style = 'classic';
        $mobile_style = 'full-screen';

        if (isset($options['desktop_dropdown_style']) && $options['desktop_dropdown_style'] != $desktop_style) {
            $desktop_style = $options['desktop_dropdown_style'];
        }

        if (isset($options['mobile_menu_style']) && $options['mobile_menu_style'] != 'full-screen') {
            $mobile_style = $options['mobile_menu_style'];
        }

        $dropdownIcon = isset($options['dropdown_icon']) ? esc_attr($options['dropdown_icon']) : "";
        $mobileOpenIcon = isset($options['mobile_open_icon']) ? esc_attr($options['mobile_open_icon']) : "";
        $mobileCloseIcon = isset($options['mobile_close_icon']) ? esc_attr($options['mobile_close_icon']) : "";

        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $dropdownIcon;
        $oxygen_svg_icons_to_load[] = $mobileOpenIcon;
        $oxygen_svg_icons_to_load[] = $mobileCloseIcon;

        ?>

        <!-- Default styles -->
        <style>
            
        <?php if (isset($options["show_mobile_menu_below"])) : ?>
        <?php if ($options["show_mobile_menu_below"] != 'always') : ?>
        @media (max-width: <?php echo oxygen_vsb_get_media_query_size($options["show_mobile_menu_below"]) ?>px) {
        <?php endif; ?>
            #<?php echo $options['selector']; ?>.oxy-site-navigation .oxy-site-navigation__mobile-open-button {
                display: initial;
            }

            #<?php echo $options['selector']; ?>.oxy-site-navigation .oxy-site-navigation__mobile-close-wrapper {
                display: initial;
            }
            /* End Mobile Styles - Buttons */

            /* Mobile Styles -- Off-Canvas */
            #<?php echo $options['selector']; ?> .oxy-site-navigation__skip-link {
                display: none;
            }

            #<?php echo $options['selector']; ?>.oxy-site-navigation > ul:not(.open) {
            display: none;
            }

            #<?php echo $options['selector']; ?>.oxy-site-navigation > ul {
                position: fixed;
                top: 0;
                flex-direction: column;
                height: 100vh;
                width: 300px;
                /* SETTING */
                padding: 64px 0px;
                /* SETTING */
                overflow: auto;
                background-color: var(--oxynav-neutral-color);
                box-shadow:
                    0px 12.5px 10px rgba(0, 0, 0, 0.035),
                    0px 100px 80px rgba(0, 0, 0, 0.07);
                margin-block-end: 0px;
                margin-block-start: 0px;
                z-index: 9999;
            }

            #<?php echo $options['selector']; ?>.oxy-site-navigation ul > li {
                width: 100%;
                flex-wrap: wrap;
            }

            #<?php echo $options['selector']; ?>.oxy-site-navigation ul > li > ul > li,
            #<?php echo $options['selector']; ?>.oxy-site-navigation ul > li > ul > li > ul > li {
                width: 100%;
                flex-wrap: nowrap;
            }

            #<?php echo $options['selector']; ?>.oxy-site-navigation ul > li > ul > li > a,
            #<?php echo $options['selector']; ?>.oxy-site-navigation ul > li > ul > li > ul > li > a {
                white-space: normal;
            }

            /* Sub & sub-submenu layout fixes */
            #<?php echo $options['selector']; ?>.oxy-site-navigation > ul > li > ul > li {
                flex-wrap: wrap;
            }

            #<?php echo $options['selector']; ?>.oxy-site-navigation > ul > li > ul > li > a {
                max-width: 89%;
            }

            #<?php echo $options['selector']; ?>.oxy-site-navigation > ul > li > ul > li > img + a {
                width: 60%;
            }
            
            /* Don't use border radius for mobile menu */
            #<?php echo $options['selector']; ?>.oxy-site-navigation > ul > li,
            #<?php echo $options['selector']; ?>.oxy-site-navigation > ul > li > ul > li:first-of-type,
            #<?php echo $options['selector']; ?>.oxy-site-navigation > ul > li > ul > li:last-of-type,
            #<?php echo $options['selector']; ?>.oxy-site-navigation > ul > li > ul > li > ul > li:first-of-type,
            #<?php echo $options['selector']; ?>.oxy-site-navigation > ul > li > ul > li > ul > li:last-of-type {
                border-radius: 0px;
            }

            #<?php echo $options['selector']; ?>.oxy-site-navigation li > ul {
                position: static;
            }
        <?php if ($options["show_mobile_menu_below"] != 'always') : ?>
        }
        <?php endif; ?>
        <?php endif; ?>
        /* End Mobile Styles -- Off-Canvas */
        </style>

        <noscript>
            <div class="oxy-site-navigation__noscript">
            JavaScript is disabled in your browser. Please enable JavaScript for a better experience.
            </div>
            <?php 
            if (!isset($options['wordpress_menu']) ) $options['wordpress_menu'] = "";
            wp_nav_menu( array( 'menu' => $options['wordpress_menu'] ) ); ?>
        </noscript>

        <?php
        if( $options["mobile_open_icon_type"] == "svg" ) {
            ?>
                <button 
                class="oxy-site-navigation__mobile-open-button" 
                @click.prevent="rootOpen = !rootOpen; $nextTick( () => $event.target.closest('button').nextElementSibling.querySelector('.oxy-site-navigation__mobile-close-wrapper > button').focus() )" 
                aria-label="Open navigation menu" 
                :aria-expanded="rootOpen" 
                x-ref="openButton">
                    <svg class='icon' viewBox="0 0 25 28">
                        <use xlink:href="#<?php echo $mobileOpenIcon; ?>"></use>
                    </svg>
                </button>
            <?php
        }
        ?>

        <?php
        if( $options["mobile_open_icon_type"] == "css" ) {
            ?>
                <button 
                class="oxy-site-navigation__mobile-open-button" 
                @click.prevent="rootOpen = !rootOpen; $nextTick( () => $event.target.closest('button').nextElementSibling.querySelector('.oxy-site-navigation__mobile-close-wrapper > button').focus() )" 
                aria-label="Open navigation menu" 
                :aria-expanded="rootOpen" 
                x-ref="openButton">
                    <div class="oxy-site-navigation__css-icon oxy-site-navigation__css-icon--animation-<?php echo $options['mobile_open_icon_animation_type']; ?>">
                        <div class="oxy-site-navigation__css-icon-inner"></div>
                    </div>
                </button>
            <?php
        }
        ?>

        <ul 
        :class="rootOpen ? 'open' : null" 
        @click.outside="!$event.target?.closest('.oxy-site-navigation') ? rootOpen = false : null"
        @focusout="( ( $event.relatedTarget != null ) && !$event.relatedTarget?.closest('.oxy-site-navigation') ) ? rootOpen = false : null"
        @closemobile="rootOpen = false; $nextTick( () => $refs.openButton.focus() )"
        x-init="menu = <?php echo $menu_items; ?>;"
        x-data="{
            useCtaStyles: '<?php echo $options['style_last_item_as_cta']; ?>',
            howManyCtas: '<?php echo $options['how_many_ctas']; ?>'
        }">
            <li class="oxy-site-navigation__mobile-close-wrapper">
                <button 
                aria-label="Close navigation menu" 
                class="oxy-site-navigation__mobile-close-button" 
                @click.prevent="rootOpen = false; $refs.openButton.focus()" 
                @keydown.escape="$dispatch('closemobile')"
                x-ref="closeButton">
                    <?php if( $options["mobile_close_icon_type"] == "svg" ) { ?>
                        <svg class='icon' viewBox="0 0 25 28">
                            <use xlink:href="#<?php echo $mobileCloseIcon; ?>"></use>
                        </svg>
                    <?php } ?>
                    <?php if( $options["mobile_close_icon_type"] == "css" ) { ?>
                        <div class="oxy-site-navigation__css-icon oxy-site-navigation__css-icon--animation-none">
                            <div class="oxy-site-navigation__css-icon-inner"></div>
                        </div>
                    <?php } ?>
                </button>
            </li>
            <template x-for="(item, index) in getRootMenuItems">
                <li x-init="if (typeof(item)=='undefined') item={}"
                    x-data="{ 
                    open: false,
                    cta: ( index + 1 ) > ( getRootMenuItems().length - howManyCtas ) && useCtaStyles, 
                    close( focusAfter ) {
                        this.open = false;

                        focusAfter && focusAfter.focus();
                    }
                }" 
                @keydown.escape.prevent.stop="if( !open ) { $dispatch( 'closemobile' ) }; close($refs.parent);" 
                @focusin.window="!$refs.submenu?.contains($event.target) && close()" 
                @mouseover="!rootOpen ? open = true : null" 
                @mouseleave="!rootOpen ? open = false : null" 
                :data-cta="cta"
                :class="typeof(item)!='undefined' && item.classes ? Object.values(item.classes) : ''">
                    <template x-if="item.menu_image">
                        <img :src="item.menu_image" alt="" />
                    </template>
                    <a 
                    x-html="isLanguageSwitcher( item.type_label ) ? item.title : sanitizeItemTitle( item.title )"
                    :data-description="item.description ? item.description : null" 
                    :data-image="item.menu_image ? item.menu_image : null"
                    :target="item.target ? item.target : '_self'"
                    :href="item.url" :aria-current='isCurrentPage(item.url)' 
                    :data-parent-of-current='item.current_item_parent' 
                    @click="rootOpen ? rootOpen = false : null"></a>
                    <template x-if="getChildren(item.ID).length != 0">
                        <button 
                        @touchstart.prevent="open = !open" 
                        @mousedown.prevent="open = !open" 
                        @keydown.enter="open = !open" 
                        @keydown.space="open = !open" 
                        :aria-label='item.title + " sub-menu"' 
                        :aria-expanded="open"
                        x-ref="parent">
                            <svg class='icon' viewBox="0 0 25 28">
                                <use xlink:href="#<?php echo $dropdownIcon; ?>"></use>
                            </svg>
                        </button>
                    </template>
                    <template x-if="getChildren(item.ID).length != 0">
                        <ul 
                        :class="open ? 'open' : null" 
                        x-ref="submenu" 
                        x-intersect="calculatePosition($el)">
                            <template x-for="item in getChildren(item.ID)">
                                <li x-data="{ 
                                    open: false, 
                                    close( focusAfter ) {
                                        this.open = false;

                                        focusAfter && focusAfter.focus();
                                    }
                                }" 
                                @focusin.window="!$refs.submenu?.contains($event.target) && close()" 
                                @mouseover="!rootOpen ? open = true : null" 
                                @mouseleave="!rootOpen ? open = false : null" 
                                :class="item.classes ? Object.values(item.classes) : ''">
                                <template x-if="item.menu_image">
                                    <img :src="item.menu_image" alt="" />
                                </template>    
                                <a 
                                x-html="item.title" 
                                :data-description="item.description ? item.description : null" 
                                :data-image="item.menu_image ? item.menu_image : null"
                                :target="item.target ? item.target : '_self'"
                                :href="item.url" :aria-current='isCurrentPage(item.url)' 
                                @click="rootOpen ? rootOpen = false : null" 
                                @mouseover="!rootOpen ? open = true : null"></a>
                                    <template x-if="getChildren(item.ID).length != 0">
                                        <button @touchstart.prevent="open = !open" @mousedown.prevent="open = !open" @keydown.enter="open = !open" @keydown.space="open = !open" :aria-label='item.title + " sub-menu"' :aria-expanded="open">
                                            <svg class='icon' viewBox="0 0 25 28">
                                                <use xlink:href="#<?php echo $dropdownIcon; ?>"></use>
                                            </svg>
                                        </button>
                                    </template>
                                    <template x-if="getChildren(item.ID).length != 0">
                                        <ul :class="open ? 'open' : null" x-ref="submenu" x-intersect="calculatePosition($el)">
                                            <template x-for="item in getChildren(item.ID)">
                                                <li :class="item.classes ? Object.values(item.classes) : ''">
                                                    <template x-if="item.menu_image">
                                                        <img :src="item.menu_image" alt="" />
                                                    </template>  
                                                    <a 
                                                    x-html="item.title" 
                                                    :data-description="item.description ? item.description : null" 
                                                    :data-image="item.menu_image ? item.menu_image : null" 
                                                    :target="item.target ? item.target : '_self'"
                                                    :href="item.url" :aria-current='isCurrentPage(item.url)' 
                                                    @click="rootOpen ? rootOpen = false : null"></a>
                                                </li>
                                            </template>
                                        </ul>
                                    </template>
                                </li>
                            </template>
                        </ul>
                    </template>
                </li>
            </template>
        </ul>

        <script>
            var alpineIntersect = document.createElement('script');
            var alpine = document.createElement('script');

            // Intersect
            alpineIntersect.setAttribute('defer', 'true');
            alpineIntersect.setAttribute('id', 'alpineintersect');
            alpineIntersect.setAttribute('src', '<?php echo CT_FW_URI; ?>/vendor/alpinejs/alpinejs.intersect.3.10.5.js')

            // Alpine
            alpine.setAttribute('defer', 'true');
            alpine.setAttribute('id', 'alpine');
            alpine.setAttribute('src', '<?php echo CT_FW_URI; ?>/vendor/alpinejs/alpinejs.3.10.5.js')

            if (!document.getElementById('alpineintersect')) {
                document.head.appendChild(alpineIntersect);
            }

            if (!document.getElementById('alpine')) {
                document.head.appendChild(alpine);
            }

            document.addEventListener('alpine:init', () => {
                Alpine.data('oxyA11yMenuData', () => ({
                    menu: <?php echo $menu_items; ?>,
                    rootOpen: false, // Used for mobile menus only
                    desktopDropdowns: '<?php echo $desktop_style; ?>',
                    mobileStyle: '<?php echo $mobile_style; ?>',
                    isKeyDown: false,
                    getChildren(id) {
                        return this.menu.filter((item) => {
                            return (parseInt(item.menu_item_parent) == id)
                        })
                    },
                    getRootMenuItems() {
                        return this.menu.filter((item) => {
                            return (item.menu_item_parent == 0);
                        })
                    },
                    isCurrentPage(url) {
                        if (!url) return false
                        var currentUrl = window.location.pathname;

                        url = url.replace('//', '');
                        url = "/" + url.substring(url.indexOf('/') + 1)

                        if (url == currentUrl) {
                            return 'page';
                        }

                        return false;
                    },
                    calculatePosition(element) {
                        var rect = element.getBoundingClientRect();

                        if (rect.right > innerWidth) {
                            element.classList.add('oxy-site-navigation-submenu--off-r');
                        }
                    },
                    isLanguageSwitcher( type ) {
                        let languageSwitcherTypes = [
                            'Language switcher',
                            'Language Switcher'
                        ];

                        if( languageSwitcherTypes.includes(type) ) return true;

                        return false;
                    },
                    sanitizeItemTitle( title ) {
                        let parser = new DOMParser();
                        let html = parser.parseFromString(title, 'text/html');

                        // Check for script tags
                        let foundScripts = html.querySelectorAll('script');
                        
                        // Check for "on" attributes
                        let nodes = html.body.children;
                        let foundOnAttributes = this.containOnAttributes(nodes);

                        // If any red flags are present, don't output the user generated title
                        if( foundScripts.length !== 0 || foundOnAttributes ) {
                            return "Invalid Title";
                        }

                        return html.body.innerHTML;
                    },
                    containOnAttributes( nodes ) {
                        for( let node of nodes ) {
                            let atts = node.attributes;
                            for( let {name, value} of atts ) {
                                if( !this.isBadAttribute( {name, value} ) ) continue;
                                return true
                            }
                            if (node.children) {
                                return this.containOnAttributes(node.children)
                            }
                        }
                        return false
                    },
                    isBadAttribute( attribute ) {
                        // Normalize value string, removing whitespace and converting to lower case
                        let val = attribute.value.replace(/\s+/g, '').toLowerCase();

                        // Check for src, href, and xlink:href attributes with "javascript" or "data:text/html" values
                        if( ["src", "href", "xlink:href"].includes(attribute.name) ) {
                            if( val.includes("javascript") || val.includes("data:text/html") ) return true;
                        }

                        // Check for any attribute starting with "on"
                        if( attribute.name.startsWith("on") ) return true;

                        return false;
                    }
                }))
            })
        </script>
    <?php

    }

    function defaultCSS()
    {
        $css = file_get_contents(__DIR__.'/a11y-site-nav/a11y-site-nav.css');

        return $css;
    }

    function oxy_prefix_nav_menu_classes($items, $menu, $args)
    {
        _wp_menu_item_classes_by_context($items);
        return $items;
    }

    function oxy_output_nav_menu_meta($items, $menu, $args) {

        foreach( $items as $item ) {
            $item->menu_image = get_post_meta($item->ID,'_menu_item_image', true);
        }

        return $items;
    }
}

add_action( "init", function() {
    new SiteNav();
});