<?php

class OxyProMenu extends OxyEl {

    var $js_added = false;

    function name() {
        return __('Pro Menu');
    }

    function enableFullPresets() {
        return true;
    }

    function icon() {
        return CT_FW_URI.'/toolbar/UI/oxygen-icons/add-icons/pro-menu.svg';
    }

    function button_place() {
        return "wordpress";
    }

    function button_priority() {
        return 9;
    }

    function init() {

        add_action("oxygen_default_classes_output", array( $this->El, "generate_defaults_css" ) );

        // include only for builder
        if (isset( $_GET['oxygen_iframe'] )) {
            add_action( 'wp_footer', array( $this, 'output_js' ) );
        }

        add_filter("oxy_allowed_empty_options_list", array( $this, "allowedEmptyOptions") );
        add_filter("oxygen_vsb_element_presets_defaults", array( $this, "presetsDefaults") );

    }

    function presetsDefaults($defaults) {

        $default_pro_menu_presets = array();
        
        include("menu-pro/menu-pro-default-presets.php");

        $defaults = array_merge($defaults, $default_pro_menu_presets);

        return $defaults;
    }

    function afterInit() {
        $this->removeApplyParamsButton();
    }

    function allowedEmptyOptions($options) {

        $options_to_add = array(
            // TODO: autoprefix with 'oxy-pro-menu_' somehow?
            "oxy-pro-menu_mobile_menu_open_icon_text",
            "oxy-pro-menu_mobile_menu_close_icon_text",
            "oxy-pro-menu_mobile_menu_close_icon_text",
            "menu_dropdown_animation",
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }

    function customCSS($options, $selector) {

        // TODO: autoprefix with 'oxy-pro-menu_' somehow?
        // make it more API way?
        if (!isset($options["oxy-pro-menu_show_mobile_menu_below"]) || $options["oxy-pro-menu_show_mobile_menu_below"]=='never') {
            return;
        }

    
        $css = "";

        if ($options["oxy-pro-menu_show_mobile_menu_below"]!="always") {
            $max_width = oxygen_vsb_get_media_query_size($options["oxy-pro-menu_show_mobile_menu_below"]);
            $css .= "@media (max-width: {$max_width}px) {";
        }
        
        $css .= "$selector .oxy-pro-menu-mobile-open-icon {
                display: inline-flex;
            }
            $selector.oxy-pro-menu-open .oxy-pro-menu-mobile-open-icon {
                display: none;
            }
            $selector .oxy-pro-menu-container {
                visibility: hidden;
                position: fixed;
            }
            $selector.oxy-pro-menu-open .oxy-pro-menu-container {
                visibility: visible;
            }";
        
        if ($options["oxy-pro-menu_show_mobile_menu_below"]!="always") {
            $css .= "}";
        }

        if (isset($options["oxy-pro-menu_dropdown_icon_size"]) && $options["oxy-pro-menu_dropdown_icon_size"]!="") {
            $icon_size = intval($options["oxy-pro-menu_dropdown_icon_size"]);
        }
        else {
            // hardcode for testing, it is better to get this from $defaults
            $icon_size = 24;
        }

        if ($icon_size < 32) {
            $margin_right = intdiv (32-$icon_size, 2);
            $css .= "$selector .oxy-pro-menu-open-container .oxy-pro-menu-list .menu-item-has-children .oxy-pro-menu-dropdown-icon-click-area, 
                   $selector .oxy-pro-menu-off-canvas-container .oxy-pro-menu-list .menu-item-has-children .oxy-pro-menu-dropdown-icon-click-area {
                    margin-right: -{$margin_right}px;
            }";
        }

        return $css;
    }


    function animations_dropdown($option, $label) {

        // animation type control
        global $oxygen_vsb_aos;

        ob_start();?>
        
        <div class="oxygen-control-wrapper">
            <label class='oxygen-control-label'><?php echo $label; ?></label>
            <div class='oxygen-control'>
                <div class="oxygen-select oxygen-select-box-wrapper">
                    <div class="oxygen-select-box"
                        ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '<?php echo $option; ?>')}">
                        <div class="oxygen-select-box-current">{{$parent.iframeScope.getOption('<?php echo $option; ?>')}}</div>
                        <div class="oxygen-select-box-dropdown"></div>
                    </div>
                    <div class="oxygen-select-box-options">
                        <div class="oxygen-select-box-option" 
                            ng-click="$parent.iframeScope.setOptionModel('<?php echo $option; ?>','')">&nbsp;</div>
                        <?php foreach ($oxygen_vsb_aos->animations_list as $name => $label) : ?>
                        <div class="oxygen-select-box-option" 
                            ng-click="$parent.iframeScope.setOptionModel('<?php echo $option; ?>','<?php echo $name; ?>')"><?php echo $label; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php 

        return ob_get_clean();
    }


    function controls() {

        // Menu list custom control. TODO: Do we need an easy API way of adding this type of control?
        $menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) ); 

        // prepare a list of id:name pairs
        $menus_list = array(); 
        foreach ( $menus as $key => $menu ) {
            if( is_object( $menu ) ) {
                $menus_list[$menu->term_id] = $menu->name;
            }
        } 
        $menus_list = json_encode( $menus_list );
        $menus_list = htmlspecialchars( $menus_list, ENT_QUOTES );

        ob_start(); ?>

                <div class='oxygen-control-wrapper'>
                    <label class='oxygen-control-label'><?php _e("Menu","oxygen"); ?></label>
                    <div class='oxygen-control'>
                        <div class="oxygen-select oxygen-select-box-wrapper">
                            <div class="oxygen-select-box">
                                <div class="oxygen-select-box-current"
                                    ng-init="menusList=<?php echo $menus_list; ?>">{{menusList[iframeScope.getOption('menu_id')]}}</div>
                                <div class="oxygen-select-box-dropdown"></div>
                            </div>
                            <div class="oxygen-select-box-options">
                                <?php foreach ($menus as $key => $menu) : ?>
                                    <?php if( is_object($menu) ) { ?>
                                    <div class="oxygen-select-box-option" 
                                        ng-click="iframeScope.setOptionModel('menu_id','<?php echo $menu->term_id; ?>')"><?php echo $menu->name; ?></div>
                                    <?php } ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
        <?php 

        $html = ob_get_clean();

        $this->addCustomControl($html, 'menu_id')->rebuildElementOnChange();

        $mobile_menu_below = $this->addOptionControl(
            array(
                "name" => __('Mobile Menu / Toggle Below', 'oxygen'),
                "slug" => 'show_mobile_menu_below',
                "type" => 'medialist',
            )
        );
        $mobile_menu_below->setParam("always_option", true);
        $mobile_menu_below->rebuildElementOnChange();

        /**
         * Desktop Section
         */

        $desktop_section = $this->addControlSection("desktop", __("Desktop Menu"), "assets/icon.png", $this);
        $selector = ".oxy-pro-menu-list .menu-item a";
        
        /**
         * Typography sub-section
         */ 

        $selector = ".oxy-pro-menu-list .menu-item, .oxy-pro-menu-list .menu-item a";

        $slug = $this->selector2slug($selector);
        $slug.= "_typography";

        $typographySection = $desktop_section->addControlsSection(
            $slug,
            __("Typography"),
            "assets/icon.png",
            $this
        );

        $typographyPreset = $typographySection->addPreset(
            "typography",
            $slug
        );

        $typographyPreset->whiteList();
        $typographyPreset->removeTypographyAlign();
        
        $typographySelector = $this->El->registerCSSSelector($selector);
        $typographySelector->mapPreset(
            'typography',
            $slug
        );

        $orientation = $desktop_section->addControl("buttons-list", "orientation", __("Orientation") );
        $orientation->setValue( array("Horizontal","Vertical") );
        $orientation->setValueCSS( array(
            "Horizontal"  => "
                .oxy-pro-menu-container:not(.oxy-pro-menu-open-container):not(.oxy-pro-menu-off-canvas-container) .oxy-pro-menu-list{
                    flex-direction: row;
               }",
            "Vertical"  => "
                .oxy-pro-menu-list{
                    flex-direction: column;
                }
                .oxy-pro-menu-list > .menu-item {
                    white-space: normal;
                }
                .oxy-pro-menu-container:not(.oxy-pro-menu-open-container):not(.oxy-pro-menu-off-canvas-container) ul:not(.sub-menu) > li > .sub-menu{
                    top: 0;
                    left: 100%;
                }
                .oxy-pro-menu-list > .menu-item > .sub-menu.sub-menu-left {
                    margin-right: 100%;
                }
                .oxy-pro-menu-dropdown-animating[data-aos*=\"down\"]{
                    pointer-events: auto;
                }
                .oxy-pro-menu-dropdown-animating[data-aos*=\"right\"]{
                    pointer-events: none;
                }
                .oxy-pro-menu-dropdown-animating.sub-menu-left[data-aos*=\"left\"]{
                    pointer-events: none;
                }

            ",
        ) );
        $orientation->whiteList();

        /**
         * Spacing sub-section
         */ 

        $spacing_section = $desktop_section->addControlSection("spacing", __("Spacing, Align, Border"), "assets/icon.png", $this);
        $spacing_section->addPreset(
            "padding",
            "menu_item_padding",
            __("Item Padding"),
            ".oxy-pro-menu-list .menu-item a"
        )->whiteList();
        $spacing_section->addPreset(
            "margin",
            "menu_item_margin",
            __("Item Margin"),
            ".oxy-pro-menu-list > .menu-item"
        )->whiteList();

        $desktop_item_text_align = $spacing_section->addControl("buttons-list", "desktop_item_text_align", __("Item Text Align") );
        $desktop_item_text_align->setValue( array("Left","Center", "Right") );
        $desktop_item_text_align->setValueCSS( array(
            "Left" => "
                .oxy-pro-menu-container .menu-item a {
                    text-align: left;
                    justify-content: flex-start;
                }
                .oxy-pro-menu-container.oxy-pro-menu-open-container .menu-item,
                .oxy-pro-menu-container.oxy-pro-menu-off-canvas-container .menu-item {
                    align-items: flex-start;
                }
            ",
            "Center" => "
                .oxy-pro-menu-container .menu-item > a {
                    text-align: center;
                    justify-content: center;
                }
                .oxy-pro-menu-container.oxy-pro-menu-open-container .menu-item,
                .oxy-pro-menu-container.oxy-pro-menu-off-canvas-container .menu-item {
                    align-items: center;
                }
            ",
            "Right" => "
                .oxy-pro-menu-container .menu-item > a {
                    text-align: right;
                    justify-content: flex-end;
                }
                .oxy-pro-menu-container.oxy-pro-menu-open-container .menu-item,
                .oxy-pro-menu-container.oxy-pro-menu-off-canvas-container .menu-item {
                    align-items: flex-end;
                }
            ",
        ) );
        $desktop_item_text_align->whiteList();

        $spacing_section->addStyleControl(
            array( 
                "name" => __('Item Border Radius'),
                "selector" => ".oxy-pro-menu-list > .menu-item > a",
                "property" => 'border-radius',
                "control_type" => "measurebox",
                "unit" => "px"
            )
        );

        /**
         * Hover & Active sub-section
         */ 

        $hover_n_active_section = $desktop_section->addControlSection("hover_n_active", __("Hover & Active"), "assets/icon.png", $this);
        $hover_selector  = ".oxy-pro-menu-list .menu-item.menu-item.menu-item.menu-item a:hover, .oxy-pro-menu-list .menu-item.menu-item.menu-item.menu-item a:focus-within";
        $active_selector = ".oxy-pro-menu-list .menu-item.current-menu-item.current-menu-item a";
        
        $hover_n_active_section->addStyleControls(
            array(

                // Hover
                array(
                    "name" => __('Hover Text Color'),
                    "selector" => $hover_selector,
                    "property" => 'color',
                    // for backward compatibility
                    "slug" => "oxy-pro-menu_slug_oxypromenulistmenuitemmenuitemmenuitemmenuitemahover_color",
                ),
                array(
                    "name" => __('Hover Background Color'),
                    "selector" => $hover_selector,
                    "property" => 'background-color',
                    // for backward compatibility
                    "slug" => "oxy-pro-menu_slug_oxypromenulistmenuitemmenuitemmenuitemmenuitemahover_background_color",
                ),
                array(
                    "name" => __('Hover Border Top'),
                    "selector" => $hover_selector.",.oxy-pro-menu-list .menu-item a",
                    "property" => 'border-top-width',
                    // for backward compatibility
                    "slug" => "oxy-pro-menu_slug_oxypromenulistmenuitemmenuitemmenuitemmenuitemahoveroxypromenulistmenuitema_border_top_width",
                ),
                array(
                    "name" => __('Hover Border Bottom'),
                    "selector" => $hover_selector.",.oxy-pro-menu-list .menu-item a",
                    "property" => 'border-bottom-width',
                    "slug" => "oxy-pro-menu_slug_oxypromenulistmenuitemmenuitemmenuitemmenuitemahoveroxypromenulistmenuitema_border_bottom_width",
                ),

                // Active
                array(
                    "name" => __('Active Text Color'),
                    "selector" => $active_selector,
                    "property" => 'color',
                ),
                array(
                    "name" => __('Active Background Color'),
                    "selector" => $active_selector,
                    "property" => 'background-color',
                ),
                array(
                    "name" => __('Active Border Top'),
                    "selector" => $active_selector.",.oxy-pro-menu-list .menu-item a",
                    "property" => 'border-top-width',
                ),
                array(
                    "name" => __('Active Border Bottom'),
                    "selector" => $active_selector.",.oxy-pro-menu-list .menu-item a",
                    "property" => 'border-bottom-width',
                ),

            )
        );
        
        // Transition

        $transition = $hover_n_active_section->addStyleControl(
            array(
                "name" => __('Transition Duration'),
                "selector" => $selector,
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
            )
        );

        $transition->setUnits('s','s');
        $transition->setRange(0, 1, 0.1);


        /**
         * Dropdowns
         */

        $dropdowns_section = $this->addControlSection("dropdowns", __("Desktop Dropdowns"), "assets/icon.png", $this);
        
        $dropdowns_section->addOptionControl(
            array(
                "type" => 'checkbox',
                "name" => __('Enable Dropdown','oxygen'),
                "slug" => 'show_dropdown',
                "value" => 'true'
            )
        )->rebuildElementOnChange();

        /**
         * Box Shadow sub-section
         */

        $dropdowns_section->boxShadowSection(
            __("Box Shadow"),
            ".oxy-pro-menu-container:not(.oxy-pro-menu-open-container):not(.oxy-pro-menu-off-canvas-container) .sub-menu",
            $this,
            false,//section param not used
            false //remove inset control
        );

        /**
         * Border Radius sub-section
         */
        
        $dropdowns_section->addStyleControl(
            array( 
                "name" => __('Dropdown Border Radius'),
                "selector" => ".sub-menu",
                "property" => 'border-radius',
                "slug" => "dropdown-border-radius",
                "control_type" => "measurebox",
                "unit" => "px"
            )
        );

        $dropdowns_section->addStyleControl(
            array( 
                "selector" => '.sub-menu > li:last-child > a',
                "property" => 'border-bottom-left-radius|border-bottom-right-radius',
                "slug" => "dropdown-border-radius",
                "unit" => "px",
                "hidden" => "true"// trick to make same control apply to different selectors and properties
            )
        );

        $dropdowns_section->addStyleControl(
            array( 
                "selector" => '.sub-menu > li:first-child > a',
                "property" => 'border-top-left-radius|border-top-right-radius',
                "slug" => "dropdown-border-radius",
                "unit" => "px",
                "hidden" => "true"// trick to make same control apply to different selectors and properties
            )
        );
        
        /**
         * Icon sub-section
         */

        $dropdown_icon = $dropdowns_section->addControlSection("dropdown_icon", __("Dropdown Icon"), "assets/icon.png", $this);
        $icon_selector = ".oxy-pro-menu-show-dropdown .oxy-pro-menu-list .menu-item-has-children > a svg";

        $show_dropdown_icon = $dropdown_icon->addOptionControl(
            array(
                "type" => 'checkbox',
                "name" => __('Show Dropdown Icon','oxygen'),
                "slug" => 'show_dropdown_icon',
                "default" => 'true'
            )
        );
        $show_dropdown_icon->setValueCSS(array(
            "false" => 
                ".menu-item-has-children > a svg {
                    display: none;
                }"
            )
        );
        $show_dropdown_icon->rebuildElementOnChange();

        $dropdown_icon->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "condition" => 'show_dropdown_icon=true',
                "slug" => 'dropdown_icon',
                "default" => 'FontAwesomeicon-arrow-circle-o-down'
            )
        )->rebuildElementOnChange();

        $this->El->inlineJS(
            "jQuery('#%%ELEMENT_ID%% .oxy-pro-menu-show-dropdown .menu-item-has-children > a', 'body').each(function(){
                jQuery(this).append('<div class=\"oxy-pro-menu-dropdown-icon-click-area\"><svg class=\"oxy-pro-menu-dropdown-icon\"><use xlink:href=\"#%%dropdown_icon%%\"></use></svg></div>');
            });
            jQuery('#%%ELEMENT_ID%% .oxy-pro-menu-show-dropdown .menu-item:not(.menu-item-has-children) > a', 'body').each(function(){
                jQuery(this).append('<div class=\"oxy-pro-menu-dropdown-icon-click-area\"></div>');
            });"
        );

        $dropdown_icon->addStyleControls(
            array(
                array(
                    "name" => __('Color'),
                    "selector" => $icon_selector,
                    "property" => 'color',
                    "condition" => 'show_dropdown_icon=true',
                ),
                array(
                    "name" => __('Size'),
                    "selector" => $icon_selector,
                    "property" => 'font-size',
                    "condition" => 'show_dropdown_icon=true',
                ),
            )
        );

        $dropdown_icon->addPreset(
            "margin",
            "dropdown_icon_item_margin",
            __("Icon Margin"),
            $icon_selector
        )->whiteList();

        $dropdown_icon->addStyleControls(
            array(
                array(
                    "name" => __('Icons Space'),
                    "selector" => ".oxy-pro-menu-show-dropdown .oxy-pro-menu-list .menu-item-has-children > a div",
                    "property" => 'margin-left',
                    "control_type" => 'slider-measurebox',
                    "condition" => 'show_dropdown_icon=true',
                    "value" => '0',
                    "unit" => 'px' 
                ),
            )
        );

        $dropdown_icon->addStyleControl(
            array(
                "name" => __('Rotation'),
                "selector" => $icon_selector,
                "property" => 'transform:rotate',
                "control_type" => 'slider-measurebox',
                "condition" => 'show_dropdown_icon=true',
            )
        )
        ->setUnits('deg','deg')
        ->setRange('-180','180');

        $dropdown_icon->addStyleControl(
            array(
                "name" => __('Open Rotation'),
                "selector" => ".oxy-pro-menu-show-dropdown .oxy-pro-menu-list .menu-item-has-children:hover > a svg",
                "property" => 'transform:rotate',
                "control_type" => 'slider-measurebox',
                "condition" => 'show_dropdown_icon=true',
            )
        )
        ->setUnits('deg','deg')
        ->setRange('-180','180');

        $dropdown_icon->addStyleControl(
            array(
                "type" => 'measurebox',
                "selector" => $icon_selector,
                "name" => __('Icon Transition Duration'),
                "default" => "0.4",
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
            )
        )
        ->setUnits('s','s')
        ->setRange('0','2','0.1');

        /**
         * Dropdown Colors sub-section
         */

        $dropdown_colors_section = $dropdowns_section->addControlSection("dropdown_colors_desktop", __("Dropdown Colors"), "assets/icon.png", $this);
        $selector = ".oxy-pro-menu-container:not(.oxy-pro-menu-open-container):not(.oxy-pro-menu-off-canvas-container) .sub-menu .menu-item a";

        $dropdown_colors_section->addStyleControls(
            array(

                // Background
                array(
                    "name" => __('Background Color'),
                    "selector" => $selector,
                    "property" => 'background-color',
                    "condition" => 'show_dropdown=true',
                ),
                array(
                    "name" => __('Hover Background Color'),
                    "selector" => $selector.":hover,".$selector.":focus-within",
                    "property" => 'background-color',
                    "condition" => 'show_dropdown=true',
                    // for backward compatibility
                    "slug" => "oxy-pro-menu_slug_oxypromenucontainernotoxypromenuopencontainernotoxypromenuoffcanvascontainersubmenumenuitemahover_background_color",
                ),

                // Link
                array(
                    "name" => __('Link Color'),
                    "selector" => $selector,
                    "property" => 'color',
                    "condition" => 'show_dropdown=true',
                ),
                array(
                    "name" => __('Hover Text Color'),
                    "selector" => $selector.":hover,".$selector.":focus-within",
                    "property" => 'color',
                    "condition" => 'show_dropdown=true',
                    // for backward compatibility
                    "slug" => "oxy-pro-menu_slug_oxypromenucontainernotoxypromenuopencontainernotoxypromenuoffcanvascontainersubmenumenuitemahover_color",
                ),
            )
        );


        /**
         * Dropdown Child Links sub-section
         */

        $dropdown_child_links_section = $dropdowns_section->addControlSection("dropdown_child_links_desktop", __("Dropdown Child Links"), "assets/icon.png", $this);
        $selector = ".oxy-pro-menu-container:not(.oxy-pro-menu-open-container):not(.oxy-pro-menu-off-canvas-container) .sub-menu .menu-item a";

        $dropdown_child_links_section->addPreset(
            "padding",
            "dropdown_child_item_padding",
            __("Link Padding"),
            $selector
        )->whiteList();

        $dropdown_child_links_section->addPreset(
            "typography",
            "dropdown_child_item_typography",
            __("Typography"),
            ".oxy-pro-menu-container:not(.oxy-pro-menu-open-container):not(.oxy-pro-menu-off-canvas-container) .sub-menu .menu-item, .oxy-pro-menu-container:not(.oxy-pro-menu-open-container):not(.oxy-pro-menu-off-canvas-container) .sub-menu .menu-item a"
        )->whiteList();

        $html = $this->animations_dropdown('menu_dropdown_animation',__("Animation Type","oxygen"));
        $animation_type = $dropdowns_section->addCustomControl($html, 'menu_dropdown_animation');
        $animation_type->setParam('param_name', 'menu_dropdown_animation');
        $animation_type->setDefaultValue('fade-up');
        $animation_type->rebuildElementOnChange();

        $dropdowns_section->addOptionControl(
            array(
                "name" => __('Animation Duration'),
                "slug" => 'menu_dropdown_animation_duration',
                "type" => 'slider-measurebox',
                "default" => '0.4',
            )
        )
        ->setUnits('s','s')
        ->setRange('0','2','0.1')
        ->rebuildElementOnChange();


        /**
         * Mobile menu
         */

        $mobile_menu = $this->addControlSection("mobile_menu", __("Mobile Menu"), "assets/icon.png", $this);

        /**
         * Menu Open Icon sub-section
         */

        $mobile_menu_open_icon = $mobile_menu->addControlSection("mobile_menu_open_icon", __("Open Icon Layout"), "assets/icon.png", $this);
        $icon_selector = ".oxy-pro-menu-mobile-open-icon";

        $mobile_menu_open_icon->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'mobile_menu_open_icon',
                "default" => 'FontAwesomeicon-ellipsis-v',
            )
        )->rebuildElementOnChange();

        $mobile_menu_open_icon->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Icon Text'),
                "slug" => 'mobile_menu_open_icon_text',
                "default" => 'Menu',
            )
        )->rebuildElementOnChange();

        $mobile_menu_open_icon->addStyleControls(
            array(

                array(
                    "name" => __('Icon Size'),
                    "selector" => $icon_selector." svg",
                    "property" => 'width|height',
                    "control_type" => 'slider-measurebox',
                    "unit" => 'px',
                    "default" => '30',
                ),
                array(
                    "name" => __('Margin Above/Below'),
                    "selector" => $icon_selector."",
                    "property" => 'margin-top|margin-bottom',
                    "control_type" => 'slider-measurebox',
                    "unit" => 'px',
                ),
                array(
                    "name" => __('Icon Margin Right'),
                    "selector" => $icon_selector." svg",
                    "property" => 'margin-right',
                    "control_type" => 'slider-measurebox',
                    "unit" => 'px',
                ),
                array(
                    "name" => __('Icon Color'),
                    "selector" => $icon_selector." svg",
                    "property" => 'color',
                ),
                array(
                    "name" => __('Icon Hover Color'),
                    "selector" => $icon_selector.":hover svg",
                    "property" => 'color',
                ),
                array(
                    "name" => __('Padding'),
                    "selector" => $icon_selector."",
                    "property" => 'padding-top|padding-right|padding-bottom|padding-left',
                    "control_type" => 'slider-measurebox',
                    "unit" => 'px',
                    "default" => '15',
                ),
                array(
                    "name" => __('Padding Color'),
                    "selector" => $icon_selector."",
                    "property" => 'background-color',
                ),
                array(
                    "name" => __('Padding Hover Color'),
                    "selector" => $icon_selector.":hover",
                    "property" => 'background-color',
                ),
                array( 
                    "name" => __('Padding Border Radius'),
                    "selector" => $icon_selector,
                    "property" => 'border-radius',
                    "control_type" => "measurebox",
                    "unit" => "px"
                ),
            )
        );

        $mobile_menu_open_icon->addStyleControl(
            array(
                "name" => __('Icon Transition Duration'),
                "selector" => "$icon_selector, $icon_selector svg",
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
                "default" => '0.4',
            )
        )
        ->setUnits('s','s')
        ->setRange('0','2','0.1');

        $mobile_menu->typographySection(__("Open Icon Typography"), 
            $icon_selector, 
            $this
        );


        /**
         * Menu Close Icon sub-section
         */

        $mobile_menu_close_icon = $mobile_menu->addControlSection("mobile_menu_close_icon", __("Close Icon Layout"), "assets/icon.png", $this);
        $icon_selector = ".oxy-pro-menu-mobile-close-icon";

        $mobile_menu_close_icon->addOptionControl(
            array(
                "type" => 'icon_finder',
                "name" => __('Icon'),
                "slug" => 'mobile_menu_close_icon',
                "default" => 'FontAwesomeicon-close',
            )
        )->rebuildElementOnChange();

        $mobile_menu_close_icon->addOptionControl(
            array(
                "type" => 'textfield',
                "name" => __('Icon Text'),
                "slug" => 'mobile_menu_close_icon_text',
                "default" => 'close',
            )
        )->rebuildElementOnChange();

        $mobile_menu_close_icon->addStyleControls(
            array(

                array(
                    "name" => __('Icon Position Top'),
                    "selector" => $icon_selector,
                    "property" => 'top',
                    "default" => '20'
                ),
                array(
                    "name" => __('Icon Position Left'),
                    "selector" => $icon_selector,
                    "property" => 'left',
                    "default" => '20'
                ),

                array(
                    "name" => __('Icon Size'),
                    "selector" => $icon_selector." svg",
                    "property" => 'width|height',
                    "control_type" => 'slider-measurebox',
                    "unit" => 'px',
                    "default" => '24'
                ),
                array(
                    "name" => __('Margin Above/Below'),
                    "selector" => $icon_selector."",
                    "property" => 'margin-top|margin-bottom',
                    "control_type" => 'slider-measurebox',
                    "unit" => 'px'
                ),
                array(
                    "name" => __('Icon Margin Right'),
                    "selector" => $icon_selector." svg",
                    "property" => 'margin-right',
                    "control_type" => 'slider-measurebox',
                    "unit" => 'px',
                ),
                array(
                    "name" => __('Icon Color'),
                    "selector" => $icon_selector." svg",
                    "property" => 'color',
                ),
                array(
                    "name" => __('Icon Hover Color'),
                    "selector" => $icon_selector.":hover svg",
                    "property" => 'color',
                ),
                array(
                    "name" => __('Padding'),
                    "selector" => $icon_selector,
                    "control_type" => 'slider-measurebox',
                    "property" => 'padding-top|padding-right|padding-bottom|padding-left',
                    "unit" => 'px'
                ),
                array(
                    "name" => __('Padding Color'),
                    "selector" => $icon_selector."",
                    "property" => 'background-color',
                ),
                array(
                    "name" => __('Padding Hover Color'),
                    "selector" => $icon_selector.":hover",
                    "property" => 'background-color',
                ),
                array( 
                    "name" => __('Padding Border Radius'),
                    "selector" => $icon_selector,
                    "property" => 'border-radius',
                    "control_type" => "measurebox",
                    "unit" => "px"
                ),
            )
        );

        $mobile_menu_close_icon->addStyleControl(
            array(
                "name" => __('Icon Transition Duration'),
                "selector" => "$icon_selector, $icon_selector svg",
                "property" => 'transition-duration',
                "control_type" => 'slider-measurebox',
                "default" => '0.4',
            )
        )
        ->setUnits('s','s')
        ->setRange('0','2', '0.1');

        $mobile_menu->typographySection(__("Close Icon Typography"), 
            $icon_selector, 
            $this
        );


        /**
         * General Mobile Menu styles sub-section
         */

        $mobile_menu_styles = $mobile_menu->addControlSection("mobile_menu_styles", __("Menu Styles"), "assets/icon.png", $this);

        $mobile_menu_styles->addStyleControl(
            array(
                "name" => __('Items Min Width'),
                "slug" => 'slug_oxypromenuoffcanvascontaineroxypromenulistmenuitemoxypromenuopencontaineroxypromenulistmenuitem_min_width', /* for backward compat with 3.2 */
                "selector" => ".oxy-pro-menu-off-canvas-container > div:first-child,
                               .oxy-pro-menu-open-container > div:first-child",
                "property" => 'min-width',
                "default" => ''
            )
        )->whiteList();

        $mobile_menu_styles->addOptionControl(
            array(
                "type" => 'checkbox',
                "name" => __('Off Canvas','oxygen'),
                "slug" => 'off_canvas'
            )
        )->rebuildElementOnChange();

        $mobile_menu_styles->addStyleControl(
            array(
                "name" => __('Off Canvas Width'),
                "selector" => ".oxy-pro-menu-off-canvas-container",
                "property" => 'width',
                "default" => ''
            )
        )->setCondition("off_canvas=true");

        $off_canvas_alignment = $mobile_menu_styles->addControl("buttons-list", "off_canvas_alignment", __("Off Canvas Alignment") );
        $off_canvas_alignment->setCondition("off_canvas=true");
        $off_canvas_alignment->setValue( array("left","right") );
        $off_canvas_alignment->setValueCSS( array(
            "left" => 
                ".oxy-pro-menu-off-canvas-container {
                    top: 0;
                    bottom: 0;
                    right: auto;
                    left: 0;
               }",
            "right" => 
                ".oxy-pro-menu-off-canvas-container {
                    top: 0;
                    bottom: 0;
                    right: 0;
                    left: auto;
               }
            ",
        ) );


        $html = $this->animations_dropdown('menu_off_canvas_animation',__("Off Canvas Animation","oxygen"));
        $off_canvas_animation = $mobile_menu_styles->addCustomControl($html, 'menu_off_canvas_animation');
        $off_canvas_animation->setCondition("off_canvas=true");
        $off_canvas_animation->rebuildElementOnChange();

        $transition_duration = $mobile_menu_styles->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Animation Duration','oxygen'),
                "slug" => 'off_canvas_transition_duration',
                "default" => "0.4"
            )
        );
        $transition_duration->setUnits("s","s");
        $transition_duration->setRange('0','2','0.1');
        $transition_duration->setCondition("off_canvas=true");
        $transition_duration->rebuildElementOnChange();

        $selector = ".oxy-pro-menu-container.oxy-pro-menu-open-container .menu-item a, .oxy-pro-menu-container.oxy-pro-menu-off-canvas-container .menu-item a";
        $hover_selector = ".oxy-pro-menu-container.oxy-pro-menu-open-container .menu-item a:hover, .oxy-pro-menu-container.oxy-pro-menu-off-canvas-container .menu-item a:hover";

        $mobile_menu_styles->addStyleControls(
            array(
                array(
                    "name" => __('Background Color'),
                    "selector" => ".oxy-pro-menu-container.oxy-pro-menu-off-canvas-container, .oxy-pro-menu-container.oxy-pro-menu-open-container",
                    "property" => 'background-color',
                    "default" => '#ffffff'
                ),
                array(
                    "name" => __('Link Text Color'),
                    "selector" => $selector,
                    "property" => 'color',
                ),
                array(
                    "name" => __('Link Text Hover Color'),
                    "selector" => $hover_selector,
                    // use previous slug for backward compat
                    "slug" => 'oxy-pro-menu_slug_oxypromenucontaineroxypromenuopencontainermenuitemaoxypromenucontaineroxypromenuoffcanvascontainermenuitemahover_color',
                    "property" => 'color',
                ),
            )
        );

        $mobile_menu_styles->addPreset(
            "padding",
            "mobile_menu_item_padding",
            __("Link Padding"),
            $selector
        )->whiteList();

        $mobile_menu_styles->addPreset(
            "padding",
            "mobile_menu_container_padding",
            __("Container Padding"),
            ".oxy-pro-menu-container.oxy-pro-menu-open-container,.oxy-pro-menu-container.oxy-pro-menu-off-canvas-container"
        )->whiteList();

        $mobile_item_text_align = $mobile_menu_styles->addControl("buttons-list", "mobile_item_text_align", __("Item Text Align") );
        $mobile_item_text_align->setValue( array("Left","Center", "Right") );
        $mobile_item_text_align->setValueCSS( array(
            "Left" => "

                .oxy-pro-menu-container.oxy-pro-menu-open-container .oxy-pro-menu-list .menu-item a,
                .oxy-pro-menu-container.oxy-pro-menu-off-canvas-container .oxy-pro-menu-list .menu-item a{
                    text-align: left;
                    justify-content: flex-start;
                }
            ",
            "Center" => "

                .oxy-pro-menu-container.oxy-pro-menu-open-container .oxy-pro-menu-list .menu-item a,
                .oxy-pro-menu-container.oxy-pro-menu-off-canvas-container .oxy-pro-menu-list .menu-item a{
                    text-align: center;
                    justify-content: center;
                }
            ",
            "Right" => "

                .oxy-pro-menu-container.oxy-pro-menu-open-container .oxy-pro-menu-list .menu-item a,
                .oxy-pro-menu-container.oxy-pro-menu-off-canvas-container .oxy-pro-menu-list .menu-item a{
                    text-align: right;
                    justify-content: flex-end;
                }
            ",
        ) );
        $mobile_item_text_align->whiteList();

        /**
         * Mobile Menu > Typography
         */ 
             
        $selector = ".oxy-pro-menu-open-container .oxy-pro-menu-list .menu-item,.oxy-pro-menu-open-container .oxy-pro-menu-list .menu-item a,.oxy-pro-menu-off-canvas-container .oxy-pro-menu-list .menu-item,.oxy-pro-menu-off-canvas-container .oxy-pro-menu-list .menu-item a";

        $slug = $this->selector2slug($selector);
        $slug.= "_typography";

        $typographySection = $mobile_menu->addControlsSection(
            $slug,
            __("Typography"),
            "assets/icon.png",
            $this
        );

        $typographyPreset = $typographySection->addPreset(
            "typography",
            $slug
        );

        $typographyPreset->whiteList();
        $typographyPreset->removeTypographyAlign();
        
        $typographySelector = $this->El->registerCSSSelector($selector);
        $typographySelector->mapPreset(
            'typography',
            $slug
        );

        
        /**
         * Mobile Menu background and gradient
         */

        $mobile_menu_background = $mobile_menu->addControlSection("mobile_menu_background", __("Background"), "assets/icon.png", $this);

        $mobile_menu_background->addPreset(
            "background",
            "mobile_menu_background",
            "",// no title needed
            ".oxy-pro-menu-off-canvas-container, .oxy-pro-menu-open-container"
        );

        /**
         * Mobile Menu Dropdowns sub-section
         */

        $mobile_dropdowns = $this->addControlSection("mobile_dropdowns", __("Mobile Dropdowns"), "assets/icon.png", $this);

        $show_dropdowns_on_mobile = $mobile_dropdowns->addOptionControl(
            array(
                "type" => 'buttons-list',
                "name" => __('Include Dropdown Links In Responsive Menu','oxygen'),
                "slug" => 'show_dropdown_links_on_mobile',
                "value" => array('hide','show in line','toggle'),
                "default" => 'toggle',
            )
        );
        $show_dropdowns_on_mobile->setValueCSS( array(
            "show in line"  => 
                ".oxy-pro-menu-off-canvas-container .sub-menu,
                 .oxy-pro-menu-open-container .sub-menu {
                    display: flex;
                }",
        ) );
        $show_dropdowns_on_mobile->rebuildElementOnChange();

        $animation_duration = $mobile_dropdowns->addOptionControl(
            array(
                "type" => 'slider-measurebox',
                "name" => __('Animation Duration'),
                "value" => 0.4,
                "slug" => 'dropdown_links_on_mobile_animation_duration',
                "condition" => 'show_dropdown_links_on_mobile=toggle',
            )
        );
        $animation_duration->setUnits('s','s');
        $animation_duration->setRange('0','2','0.1');
        $animation_duration->rebuildElementOnChange();

        $entire_parent = $mobile_dropdowns->addOptionControl(
            array(
                "type" => 'checkbox',
                "name" => __('Entire Parent Toggles Dropdown'),
                "slug" => 'entire_parent_toggles_dropdown',
                "condition" => 'show_dropdown_links_on_mobile=toggle',
                "default" => 'true'
            )
        );
        
        $selector = ".oxy-pro-menu-dropdown-links-visible-on-mobile.oxy-pro-menu-off-canvas-container .menu-item-has-children > a, .oxy-pro-menu-dropdown-links-visible-on-mobile.oxy-pro-menu-open-container .menu-item-has-children > a";
        $dropdown_icon_size = $mobile_dropdowns->addStyleControl(
                array(
                    "name" => __('Dropdown Icon Size'),
                    "slug" => "dropdown_icon_size",
                    "selector" => ".oxy-pro-menu-off-canvas-container .oxy-pro-menu-list .menu-item-has-children > a svg, .oxy-pro-menu-open-container .oxy-pro-menu-list .menu-item-has-children > a svg",
                    "control_type" => 'slider-measurebox',
                    "value" => '24',
                    "property" => 'font-size',
                    "condition" => 'show_dropdown_links_on_mobile=toggle',
                )
        );
        $dropdown_icon_size->setRange(4, 72, 1);
        $dropdown_icon_size->rebuildElementOnChange();

        $selector = ".oxy-pro-menu-dropdown-links-toggle.oxy-pro-menu-open-container .menu-item-has-children ul, .oxy-pro-menu-dropdown-links-toggle.oxy-pro-menu-off-canvas-container .menu-item-has-children ul";

        $mobile_dropdowns->addStyleControl(
            array(
                "name" => __('Dropdown Background Color'),
                "selector" => $selector,
                "value" => 'rgba(0,0,0,0.2)',
                "property" => 'background-color',
                "condition" => 'show_dropdown_links_on_mobile=toggle',
            )
        );

        $mobile_dropdowns->addStyleControls(
            array(
                array(
                    "name" => __('Dropdown Border-Top'),
                    "control_type" => 'heading',
                    "property" => '',
                    "condition" => 'show_dropdown_links_on_mobile=toggle',
                    ),
                array(
                    "name" => __('Width'),
                    "selector" => $selector,
                    "property" => 'border-top-width',
                    "control_type" => 'measurebox',
                    "unit" => 'px',
                    "condition" => 'show_dropdown_links_on_mobile=toggle',
                ),
                array(
                    "name" => __('Color'),
                    "selector" => $selector,
                    "property" => 'border-top-color',
                    "condition" => 'show_dropdown_links_on_mobile=toggle',
                ),
                array(
                    "name" => __('Style'),
                    "selector" => $selector,
                    "property" => 'border-top-style',
                    "control_type" => 'buttons-list',
                    "value" => array('solid','dashed','dotted'),
                    "condition" => 'show_dropdown_links_on_mobile=toggle',
                ),
            )
        );

    }

    function render($options, $defaults, $content) {

        global $oxygen_vsb_aos;
        $oxygen_vsb_aos->loadAOSscripts();

        $global_settings = get_option("ct_global_settings", array() );

        // icons
        $open_icon_attr  = isset( $options['mobile_menu_open_icon'] ) ? esc_attr($options['mobile_menu_open_icon']) : "";
        $close_icon_attr = isset( $options['mobile_menu_close_icon'] ) ? esc_attr($options['mobile_menu_close_icon']) : "";
        $dropdown_icon = isset( $options['dropdown_icon'] ) ? esc_attr($options['dropdown_icon']) : "";

        global $oxygen_svg_icons_to_load;
        $oxygen_svg_icons_to_load[] = $open_icon_attr;
        $oxygen_svg_icons_to_load[] = $close_icon_attr;
        $oxygen_svg_icons_to_load[] = $dropdown_icon;

        $open_icon_class = "";
        $off_canvas_alignment = "";
        if ( isset( $options['off_canvas'] ) && $options['off_canvas'] == "true" ) {
            $open_icon_class .= " oxy-pro-menu-off-canvas-trigger";
            if  ( isset( $options['off_canvas_alignment'] ) ) {
                $open_icon_class .= " oxy-pro-menu-off-canvas-".$options['off_canvas_alignment'];
                $off_canvas_alignment = $options['off_canvas_alignment'];
            }
        }

        $container_class = "";
        if ( isset($options['show_dropdown_links_on_mobile'])||isset($options['show_dropdown_links_on_mobile']) ) {
            if ($options['show_dropdown_links_on_mobile']=='show in line'||$options['show_dropdown_links_on_mobile']=='toggle') {
                $container_class .= " oxy-pro-menu-dropdown-links-visible-on-mobile";
            }
            if ($options['show_dropdown_links_on_mobile']=='toggle') {
                $container_class .= " oxy-pro-menu-dropdown-links-toggle";
            }
        }

        if ( isset($options["show_dropdown"]) && $options["show_dropdown"] == "true" ) {
                $container_class .= " oxy-pro-menu-show-dropdown";
        }

        ?><div class="oxy-pro-menu-mobile-open-icon <?php echo $open_icon_class; ?>" data-off-canvas-alignment="<?php echo esc_attr($off_canvas_alignment); ?>"><svg id="<?php echo esc_attr($options['selector']); ?>-open-icon"><use xlink:href="#<?php echo $open_icon_attr; ?>"></use></svg><?php echo $options['mobile_menu_open_icon_text']; ?></div>

        <?php 
            if ( isset($options['off_canvas_transition_duration']) ) {
                $off_canvas_duration = (float) esc_attr($options['off_canvas_transition_duration']) * 1000;
            }
            else {
                $off_canvas_duration = $global_settings['aos']['duration'];
            }
        ?>
        
        <div class="oxy-pro-menu-container <?php echo $container_class; ?>" data-aos-duration="<?php echo $off_canvas_duration;  ?>" 

             data-oxy-pro-menu-dropdown-animation="<?php echo isset($options['menu_dropdown_animation']) ? esc_attr($options['menu_dropdown_animation']) : $global_settings['aos']['type']; ?>"
             data-oxy-pro-menu-dropdown-animation-duration="<?php echo isset($options['menu_dropdown_animation_duration']) ? esc_attr($options['menu_dropdown_animation_duration']) : $global_settings['aos']['duration']; ?>"
             data-entire-parent-toggles-dropdown="<?php echo isset($options['entire_parent_toggles_dropdown']) ? esc_attr($options['entire_parent_toggles_dropdown']) : $global_settings['aos']['duration']; ?>"

             <?php if ( isset($options['off_canvas']) && 
                        isset($options['menu_off_canvas_animation']) && 
                        $options['off_canvas'] == "true" ) : ?>
             data-oxy-pro-menu-off-canvas-animation="<?php echo esc_attr($options['menu_off_canvas_animation']) ?>"
             <?php endif; ?>

             <?php if (isset($options['dropdown_links_on_mobile_animation_duration'])) : ?>
             data-oxy-pro-menu-dropdown-animation-duration="<?php echo esc_attr($options['dropdown_links_on_mobile_animation_duration']) ?>"
             <?php endif; ?>

             <?php if (isset($options['show_dropdown_links_on_mobile'])) : ?>
             data-oxy-pro-menu-dropdown-links-on-mobile="<?php echo esc_attr($options['show_dropdown_links_on_mobile']) ?>">
             <?php endif; ?>

            <?php $menu = wp_nav_menu( array(
                "menu"          => ( isset($options["menu_id"]) ) ? $options["menu_id"] : null, 
                "depth"         => ( isset($options["show_dropdown"]) && $options["show_dropdown"] == "true" ) ? 0 : 1,
                "menu_class"    => "oxy-pro-menu-list",
                "container_class" => false,
                "fallback_cb"   => false,
                "echo"          => false
            ) );

            if ($menu!==false) :
        
                echo $menu;

            else : 

                ?><div class="menu-example-menu-container"><ul id="menu-example-menu" class="oxy-pro-menu-list"><li id="menu-item-12" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-12"><a href="#">Example Menu</a></li><li id="menu-item-13" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-13"><a href="#">Link One</a></li><li id="menu-item-14" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-14"><a href="#">Link Two</a><?php if ( $options["show_dropdown"] == "true" ) : ?><ul class="sub-menu"><li id="menu-item-15" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-15"><a href="#">Dropdown Link One</a></li><li id="menu-item-17" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-17"><a href="#">Dropdown Link Two</a></li></ul><?php endif; ?></li><li id="menu-item-16" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-16"><a href="#">Link Three</a></li></ul></div><?php 
            
            endif; ?>

            <div class="oxy-pro-menu-mobile-close-icon"><svg id="svg-<?php echo esc_attr($options['selector']); ?>"><use xlink:href="#<?php echo $close_icon_attr; ?>"></use></svg><?php echo $options['mobile_menu_close_icon_text']; ?></div>

        </div>

        <?php

        // add JavaScript code only once and if shortcode presented
        if ($this->js_added !== true) {
            add_action( 'wp_footer', array( $this, 'output_js' ) );
            $this->js_added = true;
        }
    }

    function defaultCSS() {

        $css = 
        ".oxy-pro-menu-list {
          display: flex;
          padding: 0;
          margin: 0;
          align-items: center;
        }
        .oxy-pro-menu .oxy-pro-menu-list .menu-item {
          list-style-type: none;
          display: flex;
          flex-direction: column;
          width: 100%;
          transition-property: background-color, color, border-color;
        }
        .oxy-pro-menu-list > .menu-item {
            white-space: nowrap;
        }
        .oxy-pro-menu .oxy-pro-menu-list .menu-item a {
          text-decoration: none;
          border-style: solid;
          border-width: 0;
          transition-timing-function: ease-in-out;
          transition-property: background-color, color, border-color;
          border-color: transparent;
        }
        .oxy-pro-menu .oxy-pro-menu-list li.current-menu-item > a,
        .oxy-pro-menu .oxy-pro-menu-list li.menu-item:hover > a {
          border-color: currentColor;
        }
        .oxy-pro-menu .menu-item,
        .oxy-pro-menu .sub-menu {
          position: relative;
        }
        .oxy-pro-menu .menu-item .sub-menu {
          padding: 0;
          flex-direction: column;
          white-space: nowrap;
          display: flex;
          visibility: hidden;
          position: absolute;
          z-index: 9999999;
          top: 100%;
          transition-property: opacity,transform,visibility;
        }
        .oxy-pro-menu-off-canvas .sub-menu:before,
        .oxy-pro-menu-open .sub-menu:before {
            display: none;
        }
        .oxy-pro-menu .menu-item .sub-menu.aos-animate,
        .oxy-pro-menu-init .menu-item .sub-menu[data-aos^=flip] {
          visibility: visible;
        }
        .oxy-pro-menu-container:not(.oxy-pro-menu-init) .menu-item .sub-menu[data-aos^=flip] {
          transition-duration: 0s;
        }

        .oxy-pro-menu .sub-menu .sub-menu,
        .oxy-pro-menu.oxy-pro-menu-vertical .sub-menu {
          left: 100%;
          top: 0;
        }
        .oxy-pro-menu .sub-menu.sub-menu-left {
          right: 0;
          left: auto !important;
          margin-right: 100%;
        }
        .oxy-pro-menu-list > .menu-item > .sub-menu.sub-menu-left {
          margin-right: 0;
        }
        .oxy-pro-menu .sub-menu li.menu-item {
          flex-direction: column;
        }
        .oxy-pro-menu-mobile-open-icon,
        .oxy-pro-menu-mobile-close-icon {
            display: none;
            cursor: pointer;
            align-items: center;
        }
        .oxy-pro-menu-off-canvas .oxy-pro-menu-mobile-close-icon,
        .oxy-pro-menu-open .oxy-pro-menu-mobile-close-icon {
            display: inline-flex;
        }
        .oxy-pro-menu-mobile-open-icon > svg,
        .oxy-pro-menu-mobile-close-icon > svg {
            fill: currentColor;
        }
        .oxy-pro-menu-mobile-close-icon {
            position: absolute;
        }
        .oxy-pro-menu.oxy-pro-menu-open .oxy-pro-menu-container{
            width: 100%;
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: auto;
            z-index: 2147483642 !important;
            background-color: #fff;
        }
        .oxy-pro-menu .oxy-pro-menu-container.oxy-pro-menu-container{
            transition-property: opacity,transform,visibility;
        }
        .oxy-pro-menu .oxy-pro-menu-container.oxy-pro-menu-container[data-aos^=slide]{
            transition-property: transform;
        }
        .oxy-pro-menu .oxy-pro-menu-container.oxy-pro-menu-off-canvas-container,
        .oxy-pro-menu .oxy-pro-menu-container.oxy-pro-menu-off-canvas-container[data-aos^=flip] {
            visibility: visible !important;
        }
        .oxy-pro-menu .oxy-pro-menu-open-container .oxy-pro-menu-list,
        .oxy-pro-menu .oxy-pro-menu-off-canvas-container .oxy-pro-menu-list{
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .oxy-pro-menu.oxy-pro-menu-open > .oxy-pro-menu-container {
            max-height: 100vh;
            overflow: auto;
            width: 100%;
        }
        .oxy-pro-menu.oxy-pro-menu-off-canvas > .oxy-pro-menu-container {
            max-height: 100vh;
            overflow: auto;
            z-index: 1000;
        }
        .oxy-pro-menu-container > div:first-child {
            margin: auto;
        }
        .oxy-pro-menu-off-canvas-container {
            display: flex !important;
            position: fixed;
            width: auto;
            align-items: center;
            justify-content: center;
        }
        .oxy-pro-menu-off-canvas-container a,
        .oxy-pro-menu-open-container a {
            word-break: break-word;
        }
        .oxy-pro-menu.oxy-pro-menu-off-canvas .oxy-pro-menu-list {
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .oxy-pro-menu.oxy-pro-menu-off-canvas .oxy-pro-menu-container .oxy-pro-menu-list .menu-item,
        .oxy-pro-menu.oxy-pro-menu-open .oxy-pro-menu-container .oxy-pro-menu-list .menu-item {
            flex-direction: column;
            width: 100%;
            text-align: center;
        }
        .oxy-pro-menu.oxy-pro-menu-off-canvas .sub-menu,
        .oxy-pro-menu.oxy-pro-menu-open .sub-menu {
            display: none;
            visibility: visible;
            opacity: 1;
            position: static;
            align-items: center;
            justify-content: center;
            white-space: normal;
            width: 100%;
        }
        .oxy-pro-menu.oxy-pro-menu-off-canvas .menu-item,
        .oxy-pro-menu.oxy-pro-menu-open .menu-item {
            justify-content: center;
            min-height: 32px;
        }
        .oxy-pro-menu .menu-item.menu-item-has-children,
        .oxy-pro-menu .sub-menu .menu-item.menu-item-has-children {
            flex-direction: row;
            align-items: center;
        }
        .oxy-pro-menu .menu-item > a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .oxy-pro-menu.oxy-pro-menu-off-canvas .menu-item-has-children > a .oxy-pro-menu-dropdown-icon-click-area,
        .oxy-pro-menu.oxy-pro-menu-open .menu-item-has-children > a .oxy-pro-menu-dropdown-icon-click-area {
            min-width: 32px;
            min-height: 32px;
        }
        .oxy-pro-menu .menu-item-has-children > a .oxy-pro-menu-dropdown-icon-click-area {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .oxy-pro-menu .menu-item-has-children > a svg {
            width: 1.4em;
            height: 1.4em;
            fill: currentColor;
            transition-property: transform;
        }
        .oxy-pro-menu.oxy-pro-menu-off-canvas .menu-item > a svg,
        .oxy-pro-menu.oxy-pro-menu-open .menu-item > a svg {
            width: 1em;
            height: 1em;
        }
        .oxy-pro-menu-off-canvas .oxy-pro-menu-container:not(.oxy-pro-menu-dropdown-links-toggle) .oxy-pro-menu-dropdown-icon-click-area, 
        .oxy-pro-menu-open .oxy-pro-menu-container:not(.oxy-pro-menu-dropdown-links-toggle) .oxy-pro-menu-dropdown-icon-click-area {
            display: none;
        }
        .oxy-pro-menu-off-canvas .menu-item:not(.menu-item-has-children) .oxy-pro-menu-dropdown-icon-click-area, 
        .oxy-pro-menu-open .menu-item:not(.menu-item-has-children) .oxy-pro-menu-dropdown-icon-click-area {
            min-height: 32px;
            width: 0px;
        }
        .oxy-pro-menu.oxy-pro-menu-off-canvas .oxy-pro-menu-show-dropdown:not(.oxy-pro-menu-dropdown-links-toggle) .oxy-pro-menu-list .menu-item-has-children,
        .oxy-pro-menu.oxy-pro-menu-open .oxy-pro-menu-show-dropdown:not(.oxy-pro-menu-dropdown-links-toggle) .oxy-pro-menu-list .menu-item-has-children {
            padding-right: 0px;
        }
        .oxy-pro-menu-container .menu-item a {
            width: 100%;
            text-align: center;
        }
        .oxy-pro-menu-container:not(.oxy-pro-menu-open-container):not(.oxy-pro-menu-off-canvas-container) ul:not(.sub-menu) > li > .sub-menu{
            top: 100%;
            left: 0;
        }
        .oxy-pro-menu-container:not(.oxy-pro-menu-open-container):not(.oxy-pro-menu-off-canvas-container) .sub-menu .sub-menu {
            top: 0;
            left: 100%;
        }
        .oxy-pro-menu-container:not(.oxy-pro-menu-open-container):not(.oxy-pro-menu-off-canvas-container) a {
            -webkit-tap-highlight-color: transparent;
        }
        .oxy-pro-menu-dropdown-links-toggle.oxy-pro-menu-open-container .menu-item-has-children ul,
        .oxy-pro-menu-dropdown-links-toggle.oxy-pro-menu-off-canvas-container .menu-item-has-children ul {
            border-radius: 0px !important;
            width: 100%;
        }
        .oxy-pro-menu-dropdown-animating[data-aos*=\"down\"]{
            pointer-events: none;
        }
        .sub-menu .oxy-pro-menu-dropdown-animating[data-aos*=\"down\"]{
            pointer-events: auto;
        }
        .sub-menu .oxy-pro-menu-dropdown-animating[data-aos*=\"right\"]{
            pointer-events: none;
        }
        .sub-menu .oxy-pro-menu-dropdown-animating.sub-menu-left[data-aos*=\"left\"]{
            pointer-events: none;
        }
        .oxy-pro-menu-dropdown-animating-out {
            pointer-events: none;
        }
        .oxy-pro-menu-list .menu-item a {
            border-color: transparent;
        }
        .oxy-pro-menu-list .menu-item.current-menu-item a,
        .oxy-pro-menu-list .menu-item.menu-item.menu-item.menu-item a:focus-within,
        .oxy-pro-menu-list .menu-item.menu-item.menu-item.menu-item a:hover {
            border-color: currentColor;
        }
        ";

        return $css;
    }

    /**
     * Output JS for toggle menu in responsive mode
     *
     * @since 2.0
     * @author Ilya K.
     */
    
    function output_js() { ?>

        <script type="text/javascript">

            function oxygen_init_pro_menu() {
                jQuery('.oxy-pro-menu-container').each(function(){
                    
                    // dropdowns
                    var menu = jQuery(this),
                        animation = menu.data('oxy-pro-menu-dropdown-animation'),
                        animationDuration = menu.data('oxy-pro-menu-dropdown-animation-duration');
                    
                    jQuery('.sub-menu', menu).attr('data-aos',animation);
                    jQuery('.sub-menu', menu).attr('data-aos-duration',animationDuration*1000);

                    oxygen_offcanvas_menu_init(menu);
                    jQuery(window).resize(function(){
                        oxygen_offcanvas_menu_init(menu);
                    });

                    // let certain CSS rules know menu being initialized
                    // "10" timeout is extra just in case, "0" would be enough
                    setTimeout(function() {menu.addClass('oxy-pro-menu-init');}, 10);
                });
            }

            jQuery(document).ready(oxygen_init_pro_menu);
            document.addEventListener('oxygen-ajax-element-loaded', oxygen_init_pro_menu, false);
            
            let proMenuMouseDown = false;

            jQuery(".oxygen-body")
            .on("mousedown", '.oxy-pro-menu-show-dropdown:not(.oxy-pro-menu-open-container) .menu-item-has-children', function(e) {
                proMenuMouseDown = true;
            })

            .on("mouseup", '.oxy-pro-menu-show-dropdown:not(.oxy-pro-menu-open-container) .menu-item-has-children', function(e) {
                proMenuMouseDown = false;
            })

            .on('mouseenter focusin', '.oxy-pro-menu-show-dropdown:not(.oxy-pro-menu-open-container) .menu-item-has-children', function(e) {
                if( proMenuMouseDown ) return;
                
                var subMenu = jQuery(this).children('.sub-menu');
                subMenu.addClass('aos-animate oxy-pro-menu-dropdown-animating').removeClass('sub-menu-left');

                var duration = jQuery(this).parents('.oxy-pro-menu-container').data('oxy-pro-menu-dropdown-animation-duration');

                setTimeout(function() {subMenu.removeClass('oxy-pro-menu-dropdown-animating')}, duration*1000);

                var offset = subMenu.offset(),
                    width = subMenu.width(),
                    docWidth = jQuery(window).width();

                    if (offset.left+width > docWidth) {
                        subMenu.addClass('sub-menu-left');
                    }
            })
            
            .on('mouseleave focusout', '.oxy-pro-menu-show-dropdown .menu-item-has-children', function( e ) {
                if( jQuery(this).is(':hover') ) return;

                jQuery(this).children('.sub-menu').removeClass('aos-animate');

                var subMenu = jQuery(this).children('.sub-menu');
                //subMenu.addClass('oxy-pro-menu-dropdown-animating-out');

                var duration = jQuery(this).parents('.oxy-pro-menu-container').data('oxy-pro-menu-dropdown-animation-duration');
                setTimeout(function() {subMenu.removeClass('oxy-pro-menu-dropdown-animating-out')}, duration*1000);
            })

            // open icon click
            .on('click', '.oxy-pro-menu-mobile-open-icon', function() {    
                var menu = jQuery(this).parents('.oxy-pro-menu');
                // off canvas
                if (jQuery(this).hasClass('oxy-pro-menu-off-canvas-trigger')) {
                    oxygen_offcanvas_menu_run(menu);
                }
                // regular
                else {
                    menu.addClass('oxy-pro-menu-open');
                    jQuery(this).siblings('.oxy-pro-menu-container').addClass('oxy-pro-menu-open-container');
                    jQuery('body').addClass('oxy-nav-menu-prevent-overflow');
                    jQuery('html').addClass('oxy-nav-menu-prevent-overflow');
                    
                    oxygen_pro_menu_set_static_width(menu);
                }
                // remove animation and collapse
                jQuery('.sub-menu', menu).attr('data-aos','');
                jQuery('.oxy-pro-menu-dropdown-toggle .sub-menu', menu).slideUp(0);
            });

            function oxygen_pro_menu_set_static_width(menu) {
                var menuItemWidth = jQuery(".oxy-pro-menu-list > .menu-item", menu).width();
                jQuery(".oxy-pro-menu-open-container > div:first-child, .oxy-pro-menu-off-canvas-container > div:first-child", menu).width(menuItemWidth);
            }

            function oxygen_pro_menu_unset_static_width(menu) {
                jQuery(".oxy-pro-menu-container > div:first-child", menu).width("");
            }

            // close icon click
            jQuery('body').on('click', '.oxy-pro-menu-mobile-close-icon', function(e) {
                
                var menu = jQuery(this).parents('.oxy-pro-menu');

                menu.removeClass('oxy-pro-menu-open');
                jQuery(this).parents('.oxy-pro-menu-container').removeClass('oxy-pro-menu-open-container');
                jQuery('.oxy-nav-menu-prevent-overflow').removeClass('oxy-nav-menu-prevent-overflow');

                if (jQuery(this).parent('.oxy-pro-menu-container').hasClass('oxy-pro-menu-off-canvas-container')) {
                    oxygen_offcanvas_menu_run(menu);
                }

                oxygen_pro_menu_unset_static_width(menu);
            });

            // dropdown toggle icon click
            jQuery('body').on(
                'touchstart click', 
                '.oxy-pro-menu-dropdown-links-toggle.oxy-pro-menu-off-canvas-container .menu-item-has-children > a > .oxy-pro-menu-dropdown-icon-click-area,'+
                '.oxy-pro-menu-dropdown-links-toggle.oxy-pro-menu-open-container .menu-item-has-children > a > .oxy-pro-menu-dropdown-icon-click-area', 
                function(e) {
                    e.preventDefault();

                    // fix for iOS false triggering submenu clicks
                    jQuery('.sub-menu').css('pointer-events', 'none');
                    setTimeout( function() {
                        jQuery('.sub-menu').css('pointer-events', 'initial');
                    }, 500);

                    // workaround to stop click event from triggering after touchstart
                    if (window.oxygenProMenuIconTouched === true) {
                        window.oxygenProMenuIconTouched = false;
                        return;
                    }
                    if (e.type==='touchstart') {
                        window.oxygenProMenuIconTouched = true;
                    }
                    oxygen_pro_menu_toggle_dropdown(this);
                }
            );

            function oxygen_pro_menu_toggle_dropdown(trigger) {

                var duration = jQuery(trigger).parents('.oxy-pro-menu-container').data('oxy-pro-menu-dropdown-animation-duration');

                jQuery(trigger).closest('.menu-item-has-children').children('.sub-menu').slideToggle({
                    start: function () {
                        jQuery(this).css({
                            display: "flex"
                        })
                    },
                    duration: duration*1000
                });
            }
                    
            // fullscreen menu link click
            var selector = '.oxy-pro-menu-open .menu-item a';
            jQuery('body').on('click', selector, function(event){
                
                if (jQuery(event.target).closest('.oxy-pro-menu-dropdown-icon-click-area').length > 0) {
                    // toggle icon clicked, no need to hide the menu
                    return;
                }
                else if ((jQuery(this).attr("href") === "#" || jQuery(this).closest(".oxy-pro-menu-container").data("entire-parent-toggles-dropdown")) && 
                         jQuery(this).parent().hasClass('menu-item-has-children')) {
                    // empty href don't lead anywhere, treat it as toggle trigger
                    oxygen_pro_menu_toggle_dropdown(event.target);
                    // keep anchor links behavior as is, and prevent regular links from page reload
                    if (jQuery(this).attr("href").indexOf("#")!==0) {
                        return false;
                    }
                }

                // hide the menu and follow the anchor
                if (jQuery(this).attr("href").indexOf("#")===0) {
                    jQuery('.oxy-pro-menu-open').removeClass('oxy-pro-menu-open');
                    jQuery('.oxy-pro-menu-open-container').removeClass('oxy-pro-menu-open-container');
                    jQuery('.oxy-nav-menu-prevent-overflow').removeClass('oxy-nav-menu-prevent-overflow');
                }

            });

            // off-canvas menu link click
            var selector = '.oxy-pro-menu-off-canvas .menu-item a';
            jQuery('body').on('click', selector, function(event){
                if (jQuery(event.target).closest('.oxy-pro-menu-dropdown-icon-click-area').length > 0) {
                    // toggle icon clicked, no need to trigger it 
                    return;
                }
                else if ((jQuery(this).attr("href") === "#" || jQuery(this).closest(".oxy-pro-menu-container").data("entire-parent-toggles-dropdown")) && 
                    jQuery(this).parent().hasClass('menu-item-has-children')) {
                    // empty href don't lead anywhere, treat it as toggle trigger
                    oxygen_pro_menu_toggle_dropdown(event.target);
                    // keep anchor links behavior as is, and prevent regular links from page reload
                    if (jQuery(this).attr("href").indexOf("#")!==0) {
                        return false;
                    }
                }
            });

            // off canvas
            function oxygen_offcanvas_menu_init(menu) {

                // only init off-canvas animation if trigger icon is visible i.e. mobile menu in action
                var offCanvasActive = jQuery(menu).siblings('.oxy-pro-menu-off-canvas-trigger').css('display');
                if (offCanvasActive!=='none') {
                    var animation = menu.data('oxy-pro-menu-off-canvas-animation');
                    setTimeout(function() {menu.attr('data-aos', animation);}, 10);
                }
                else {
                    // remove AOS
                    menu.attr('data-aos', '');
                };
            }
            
            function oxygen_offcanvas_menu_run(menu) {

                var container = menu.find(".oxy-pro-menu-container");
                
                if (!container.attr('data-aos')) {
                    // initialize animation
                    setTimeout(function() {oxygen_offcanvas_menu_toggle(menu, container)}, 0);
                }
                else {
                    oxygen_offcanvas_menu_toggle(menu, container);
                }
            }

            var oxygen_offcanvas_menu_toggle_in_progress = false;

            function oxygen_offcanvas_menu_toggle(menu, container) {

                if (oxygen_offcanvas_menu_toggle_in_progress) {
                    return;
                }

                container.toggleClass('aos-animate');

                if (container.hasClass('oxy-pro-menu-off-canvas-container')) {
                    
                    oxygen_offcanvas_menu_toggle_in_progress = true;
                    
                    var animation = container.data('oxy-pro-menu-off-canvas-animation'),
                        timeout = container.data('aos-duration');

                    if (!animation){
                        timeout = 0;
                    }

                    setTimeout(function() {
                        container.removeClass('oxy-pro-menu-off-canvas-container')
                        menu.removeClass('oxy-pro-menu-off-canvas');
                        oxygen_offcanvas_menu_toggle_in_progress = false;
                    }, timeout);
                }
                else {
                    container.addClass('oxy-pro-menu-off-canvas-container');
                    menu.addClass('oxy-pro-menu-off-canvas');
                    oxygen_pro_menu_set_static_width(menu);
                }
            }
        </script>

    <?php }


}

add_action( "init", function() {
    new OxyProMenu();
});