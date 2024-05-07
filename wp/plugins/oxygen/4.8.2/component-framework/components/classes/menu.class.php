<?php 

Class Oxy_Nav_Menu extends CT_Component {

	public $action_name = "oxy_render_nav_menu";
    public $template_file = "nav-menu.php";

    var $js_added = false;

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// replace component button
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxy_folder_wordpress_components", array( $this, "component_button" ) );

		// add specific options
		add_action("ct_toolbar_component_settings", array( $this, "menu_settings") );
		
		// add shortcode
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		add_filter( 'template_include', array( $this, 'single_template'), 100 );

		// include only for builder
		if (isset( $_GET['oxygen_iframe'] )) {
			add_action( 'wp_footer', array( $this, 'output_js' ) );
		}
	}


	/**
	 * Add an [oxy_nav_menu] shortcode to WordPress
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function add_shortcode( $atts, $content, $name ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
            return '';
        }

		// add JavaScript code only once and if shortcode presented
		if ($this->js_added === false) {
			add_action( 'wp_footer', array( $this, 'output_js' ) );
			$this->js_added = true;
		}

		$options = $this->set_options( $atts );

		$atts = json_decode($atts['ct_options'], true);

		if ( isset($options['dropdowns']) && $options['dropdowns'] == "on" ) {
			$options['classes'] .= " oxy-nav-menu-dropdowns";
		}

		if ( isset($options['menu_dropdown_arrow']) && $options['menu_dropdown_arrow'] == "on" ) {
			$options['classes'] .= " oxy-nav-menu-dropdown-arrow";
		}

		if ( isset($options['menu_responsive_dropdowns']) && $options['menu_responsive_dropdowns'] == "on" ) {
			$options['classes'] .= " oxy-nav-menu-responsive-dropdowns";
		}

		if ( isset($options['menu_flex_direction']) && $options['menu_flex_direction'] == "column" ) {
			$options['classes'] .= " oxy-nav-menu-vertical";
		}

		ob_start();
		
		?><nav id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><div class='oxy-menu-toggle'><div class='oxy-nav-menu-hamburger-wrap'><div class='oxy-nav-menu-hamburger'><div class='oxy-nav-menu-hamburger-line'></div><div class='oxy-nav-menu-hamburger-line'></div><div class='oxy-nav-menu-hamburger-line'></div></div></div></div><?php 

		$menu = wp_nav_menu( array(
			"menu" 			=> ( isset($options["menu_id"]) ) ? $options["menu_id"] : null, 
			"depth" 		=> ( $options["dropdowns"] == "on" ) ? 0 : 1,
			"menu_class" 	=> "oxy-nav-menu-list",
			"fallback_cb" 	=> false,
			"echo" 			=> false
		) );

		if ($menu!==false) :
	
			echo $menu;

		else : 

			?><div class="menu-example-menu-container"><ul id="menu-example-menu" class="oxy-nav-menu-list"><li id="menu-item-12" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-12"><a href="#">Example Menu</a></li><li id="menu-item-13" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-13"><a href="#">Link One</a></li><li id="menu-item-14" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-14"><a href="#">Link Two</a><?php if ( $options["dropdowns"] == "on" ) : ?><ul class="sub-menu"><li id="menu-item-15" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-15"><a href="#">Dropdown Link One</a></li><li id="menu-item-17" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-17"><a href="#">Dropdown Link Two</a></li></ul><?php endif; ?></li><li id="menu-item-16" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-16"><a href="#">Link Three</a></li></ul></div><?php 
		
		endif;

		echo "</nav>"; 

		return ob_get_clean();
	}


	/**
	 * Add WordPress folder button
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */
	
	function component_button() { 
		
		if (oxygen_hide_element_button($this->options['tag'])) {
			return;
		}
		
		?>

		<div class="oxygen-add-section-element"
 			data-searchid="<?php echo strtolower( preg_replace('/\s+/', '_', sanitize_text_field( $this->options['name'] ) ) ) ?>"
			ng-click="iframeScope.addComponent('<?php echo esc_attr($this->options['tag']); ?>','nav_menu')">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/menu.svg' />
			<?php echo esc_html($this->options['name']); ?>
		</div>

	<?php }


	/**
	 * Output settings
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function menu_settings() { 

		global $oxygen_toolbar;

		$menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) ); 

		// prepare a list of id:name pairs
		$menus_list = array(); 
		foreach ( $menus as $key => $menu ) {
			$menus_list[$menu->term_id] = $menu->name;
		} 
		$menus_list = json_encode( $menus_list );
		$menus_list = htmlspecialchars( $menus_list, ENT_QUOTES );

		?>
		
		<div class="oxygen-sidebar-flex-panel"
			ng-hide="!isActiveName('oxy_nav_menu')">
			
			<div class="oxygen-control-row"
				ng-show="!hasOpenTabs('navMenu')">
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
								<div class="oxygen-select-box-option" 
									ng-click="iframeScope.setOptionModel('menu_id','<?php echo $menu->term_id; ?>');iframeScope.renderNavMenu()"><?php echo $menu->name; ?></div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="oxygen-sidebar-advanced-subtab" 
				ng-click="switchTab('navMenu', 'text')" 
				ng-show="!hasOpenTabs('navMenu')">
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/typography.svg">
					<?php _e("Text", "oxygen"); ?>
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
			</div>

			<div class="oxygen-sidebar-advanced-subtab" 
				ng-click="switchTab('navMenu', 'spacing')" 
				ng-show="!hasOpenTabs('navMenu')">
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/size_spacing.svg">
					<?php _e("Spacing", "oxygen"); ?>
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
			</div>

			<div class="oxygen-sidebar-advanced-subtab" 
				ng-click="switchTab('navMenu', 'hover_active')" 
				ng-show="!hasOpenTabs('navMenu')">
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/hover.svg">
					<?php _e("Hover & Active", "oxygen"); ?>
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
			</div>

			<div class="oxygen-sidebar-advanced-subtab" 
				ng-click="switchTab('navMenu', 'dropdowns')" 
				ng-show="!hasOpenTabs('navMenu')">
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/dropdown.svg">
					<?php _e("Dropdowns", "oxygen"); ?>
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
			</div>

			<div class="oxygen-sidebar-advanced-subtab" 
				ng-click="switchTab('navMenu', 'responsive')" 
				ng-show="!hasOpenTabs('navMenu')">
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/responsive.svg">
					<?php _e("Mobile Responsive", "oxygen"); ?>
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
			</div>

			<div ng-if="isShowTab('navMenu','text')">
				
				<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
					<div class="oxygen-sidebar-breadcrumb-icon" 
						ng-click="tabs.navMenu=[]">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
					</div>
					<div class="oxygen-sidebar-breadcrumb-all-styles" 
						ng-click="tabs.navMenu=[]"><?php _e("All Styles","oxygen"); ?></div>
					<div class="oxygen-sidebar-breadcrumb-separator">/</div>
					<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Text","oxygen"); ?></div>
				</div>

				<div class='oxygen-control-row'>
					<?php $oxygen_toolbar->font_family_dropdown(""); ?>
					<?php $oxygen_toolbar->measure_box_with_wrapper("menu_font-size", __("Font size", "oxygen"), 'px,%,em'); ?>
				</div>

				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("menu_color", __("Color", "oxygen"), 'oxygen-typography-font-color'); ?>
				</div>

				<div class="oxygen-control-row" ng-repeat="data in [{paramName:'menu_font-weight', idName:'oxygen-typography-font-family'}]" ng-include="'ctFontWeightTemplate'">

				</div>

				<!-- line height & letter spacing -->
				<div class='oxygen-control-row'>
					<?php $oxygen_toolbar->measure_box_with_wrapper('menu_letter-spacing',__('Letter Spacing','oxygen')); ?>
				</div>

				<!-- text align -->
				<div class='oxygen-control-row'>
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Text Align","oxygen"); ?></label>
						<div class='oxygen-control'>
							<div class='oxygen-icon-button-list'>
								<?php $oxygen_toolbar->icon_button_list_button('menu_justify-content','flex-start','text-align/left.svg'); ?>
								<?php $oxygen_toolbar->icon_button_list_button('menu_justify-content','center','text-align/center.svg'); ?>
								<?php $oxygen_toolbar->icon_button_list_button('menu_justify-content','flex-end','text-align/right.svg'); ?>
								<?php $oxygen_toolbar->icon_button_list_button('menu_justify-content','stretch','text-align/justify.svg'); ?>
							</div>
						</div>
					</div>
				</div>

				<!-- text transform -->
				<div class='oxygen-control-row'>
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Text Transform","oxygen"); ?></label>
						<div class='oxygen-control'>
							<div class='oxygen-button-list'>
								<?php $oxygen_toolbar->button_list_button('menu_text-transform','none'); ?>
								<?php $oxygen_toolbar->button_list_button('menu_text-transform','capitalize'); ?>
								<?php $oxygen_toolbar->button_list_button('menu_text-transform','uppercase'); ?>
								<?php $oxygen_toolbar->button_list_button('menu_text-transform','lowercase'); ?>
							</div>
						</div>
					</div>
				</div>

				<!-- text decration -->
				<div class='oxygen-control-row'>
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Text Decoration","oxygen"); ?></label>
						<div class='oxygen-control'>
							<div class='oxygen-button-list'>
								<?php $oxygen_toolbar->button_list_button('menu_text-decoration','none','none'); ?>
								<?php $oxygen_toolbar->button_list_button('menu_text-decoration','underline','U', 'oxygen-text-decoration-underline'); ?>
								<?php $oxygen_toolbar->button_list_button('menu_text-decoration','overline','O', 'oxygen-text-decoration-overline'); ?>
								<?php $oxygen_toolbar->button_list_button('menu_text-decoration','line-through','S', 'oxygen-text-decoration-linethrough'); ?>
							</div>
						</div>
					</div>
				</div>

				<!-- font smoothing -->
				<div class='oxygen-control-row'>
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Font Smoothing","oxygen"); ?></label>
						<div class='oxygen-control'>
							<div class='oxygen-button-list'>
								<?php $oxygen_toolbar->button_list_button('menu_-webkit-font-smoothing','initial'); ?>
								<?php $oxygen_toolbar->button_list_button('menu_-webkit-font-smoothing','antialiased'); ?>
								<?php $oxygen_toolbar->button_list_button('menu_-webkit-font-smoothing','subpixel-antialiased'); ?>
							</div>
						</div>
					</div>
				</div>

			</div><!-- isShowTab('navMenu','text') -->

			<div ng-show="isShowTab('navMenu','spacing')">

				<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
					<div class="oxygen-sidebar-breadcrumb-icon" 
						ng-click="tabs.navMenu=[]">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
					</div>
					<div class="oxygen-sidebar-breadcrumb-all-styles" 
						ng-click="tabs.navMenu=[]"><?php _e("All Styles","oxygen"); ?></div>
					<div class="oxygen-sidebar-breadcrumb-separator">/</div>
					<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Spacing","oxygen"); ?></div>
				</div>
				
				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Padding", "component-theme"); ?></label>
						<div class='oxygen-control'>
							<div class='oxygen-four-sides-measure-box'>
								<?php $oxygen_toolbar->measure_box('menu_padding-top','px',true); ?>
								<?php $oxygen_toolbar->measure_box('menu_padding-right','px',true); ?>
								<?php $oxygen_toolbar->measure_box('menu_padding-bottom','px',true); ?>
								<?php $oxygen_toolbar->measure_box('menu_padding-left','px',true); ?>
                                <div class="oxygen-flex-line-break"></div>
								<div class="oxygen-apply-all-trigger">
									<?php _e("apply all »", "oxygen"); ?>
								</div>
							</div>
						</div>
					</div>
				
				</div>
				<div class="oxygen-control-row">

					<div class='oxygen-control-wrapper' ng-show='iframeScope.component.active.name != "ct_section"'>
						<label class='oxygen-control-label'><?php _e("Margin", "component-theme"); ?></label>
						<div class='oxygen-control'>
							<div class='oxygen-four-sides-measure-box'>
								<?php $oxygen_toolbar->measure_box('menu_margin-top','',true); ?>
								<?php $oxygen_toolbar->measure_box('menu_margin-right','',true); ?>
								<?php $oxygen_toolbar->measure_box('menu_margin-bottom','',true); ?>
								<?php $oxygen_toolbar->measure_box('menu_margin-left','',true); ?>
                                <div class="oxygen-flex-line-break"></div>
								<div class="oxygen-apply-all-trigger">
									<?php _e("apply all »", "oxygen"); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			
			</div><!-- isShowTab('navMenu','spacing') -->

			<div ng-if="isShowTab('navMenu','hover_active')">

				<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
					<div class="oxygen-sidebar-breadcrumb-icon" 
						ng-click="tabs.navMenu=[]">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
					</div>
					<div class="oxygen-sidebar-breadcrumb-all-styles" 
						ng-click="tabs.navMenu=[]"><?php _e("All Styles","oxygen"); ?></div>
					<div class="oxygen-sidebar-breadcrumb-separator">/</div>
					<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Hover & Active","oxygen"); ?></div>
				</div>
				
				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("menu_hover_color", __("Hover Text Color", "oxygen"), 'oxygen-typography-font-color'); ?>
				</div>

				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("menu_hover_background-color", __("Hover Background Color", "oxygen") ); ?>
				</div>

				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->measure_box_with_wrapper('menu_hover_border-top-width', __('Hover Border Top','oxygen'), 'px'); ?>
					<?php $oxygen_toolbar->measure_box_with_wrapper('menu_hover_border-bottom-width', __('Hover Border Bottom','oxygen'), 'px'); ?>
				</div>

				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("menu_active_color", __("Active Text Color", "oxygen"), 'oxygen-typography-font-color'); ?>
				</div>
				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("menu_active_background-color", __("Active Background Color", "oxygen"), 'oxygen-typography-font-color'); ?>
				</div>

				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->measure_box_with_wrapper('menu_active_border-top-width', __('Active Border Top','oxygen'), 'px'); ?>
					<?php $oxygen_toolbar->measure_box_with_wrapper('menu_active_border-bottom-width', __('Active Border Bottom','oxygen'), 'px'); ?>
				</div>

				<div class="oxygen-control-row">
					<div class="oxygen-control-wrapper">
						<label class='oxygen-control-label'><?php _e("Transition Duration", "oxygen"); ?></label>
						<div class='oxygen-control'>
							<?php $oxygen_toolbar->slider_measure_box('menu_transition-duration', 's', 0, 1, true, 0.1); ?>
						</div>
					</div>
				</div>
			
			</div><!-- isShowTab('navMenu','hover_active') -->

			<div ng-if="isShowTab('navMenu','dropdowns')">

				<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
					<div class="oxygen-sidebar-breadcrumb-icon" 
						ng-click="tabs.navMenu=[]">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
					</div>
					<div class="oxygen-sidebar-breadcrumb-all-styles" 
						ng-click="tabs.navMenu=[]"><?php _e("All Styles","oxygen"); ?></div>
					<div class="oxygen-sidebar-breadcrumb-separator">/</div>
					<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Dropdowns","oxygen"); ?></div>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class="oxygen-checkbox">
							<input type="checkbox"
								ng-true-value="'on'" 
								ng-false-value="'off'"
								ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['dropdowns']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_nav_menu','dropdowns');iframeScope.renderNavMenu()">
							<div class='oxygen-checkbox-checkbox'
								ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('dropdowns')=='on'}">
								<?php _e("Enable Dropdowns","oxygen"); ?>
							</div>
						</label>
					</div>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class="oxygen-checkbox">
							<input type="checkbox"
								ng-true-value="'on'" 
								ng-false-value="'off'"
								ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['menu_dropdown_arrow']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_nav_menu','menu_dropdown_arrow');">
							<div class='oxygen-checkbox-checkbox'
								ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('menu_dropdown_arrow')=='on'}">
								<?php _e("Show dropdown arrows","oxygen"); ?>
							</div>
						</label>
					</div>
				</div>

				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("menu_dropdowns_background-color", __("Background Color", "oxygen"), 'oxygen-typography-font-color'); ?>
				</div>
				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("menu_dropdowns_hover_background-color", __("Background Hover Color", "oxygen"), 'oxygen-typography-font-color'); ?>
				</div>
				
				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("menu_dropdowns_color", __("Link Color", "oxygen"), 'oxygen-typography-font-color'); ?>
				</div>
				
				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("menu_dropdowns_hover_color", __("Link Hover Color", "oxygen"), 'oxygen-typography-font-color'); ?>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Padding", "component-theme"); ?></label>
						<div class='oxygen-control'>
							<div class='oxygen-four-sides-measure-box'>
								<?php $oxygen_toolbar->measure_box('menu_dropdowns_padding-top','px',true); ?>
								<?php $oxygen_toolbar->measure_box('menu_dropdowns_padding-right','px',true); ?>
								<?php $oxygen_toolbar->measure_box('menu_dropdowns_padding-bottom','px',true); ?>
								<?php $oxygen_toolbar->measure_box('menu_dropdowns_padding-left','px',true); ?>
                                <div class="oxygen-flex-line-break"></div>
								<div class="oxygen-apply-all-trigger">
									<?php _e("apply all »", "oxygen"); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				
			</div><!-- isShowTab('navMenu','dropdowns') -->

			<div ng-if="isShowTab('navMenu','responsive')">

				<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
					<div class="oxygen-sidebar-breadcrumb-icon" 
						ng-click="tabs.navMenu=[]">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
					</div>
					<div class="oxygen-sidebar-breadcrumb-all-styles" 
						ng-click="tabs.navMenu=[]"><?php _e("All Styles","oxygen"); ?></div>
					<div class="oxygen-sidebar-breadcrumb-separator">/</div>
					<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Mobile Responsive","oxygen"); ?></div>
				</div>
				
				<?php $oxygen_toolbar->media_queries_list_with_wrapper("menu_responsive", __("Mobile Menu / Toggle Below","oxygen"), true, true); ?>

				<div class="oxygen-sidebar-advanced-subtab" 
					ng-click="switchTab('navMenu', 'iconStyles')">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
						<?php _e("Icon Styles", "oxygen"); ?>
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
				</div>

				<div class="oxygen-sidebar-advanced-subtab" 
					ng-click="switchTab('navMenu', 'menuStyles')">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
						<?php _e("Menu Styles", "oxygen"); ?>
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
				</div>

				<div class="oxygen-sidebar-advanced-subtab" 
					ng-click="switchTab('navMenu', 'responsiveDropdowns')">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/dropdown.svg">
						<?php _e("Dropdowns", "oxygen"); ?>
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
				</div>
			
			</div><!-- isShowTab('navMenu','responsive') -->
		
		

			<div ng-if="isShowTab('navMenu','iconStyles')&&isActiveName('oxy_nav_menu')">

				<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
					<div class="oxygen-sidebar-breadcrumb-icon" 
						ng-click="switchTab('navMenu','responsive')">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
					</div>
					<div class="oxygen-sidebar-breadcrumb-all-styles" 
						ng-click="switchTab('navMenu','responsive')"><?php _e("Mobile Responsive","oxygen"); ?></div>
					<div class="oxygen-sidebar-breadcrumb-separator">/</div>
					<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Icon Styles","oxygen"); ?></div>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Icon Size", "oxygen"); ?></label>
						<?php $oxygen_toolbar->slider_measure_box('menu_responsive_icon_size', 'px', 30, 140, false); ?>
					</div>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Margin Above / Below", "oxygen"); ?></label>
						<?php $oxygen_toolbar->slider_measure_box('menu_responsive_icon_margin', 'px', 0, 50, false); ?>
					</div>
				</div>
				
				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("menu_responsive_icon_color", __("Icon Color", "oxygen") ); ?>
				</div>

				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("menu_responsive_icon_hover_color", __("Icon Hover Color", "oxygen") ); ?>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Padding Size", "oxygen"); ?></label>
						<?php $oxygen_toolbar->slider_measure_box('menu_responsive_padding_size', 'px', 0, 50, false); ?>
					</div>
				</div>
				
				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("menu_responsive_padding_color", __("Padding Color", "oxygen") ); ?>
				</div>

				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("menu_responsive_padding_hover_color", __("Padding Hover Color", "oxygen") ); ?>
				</div>
			
			</div><!-- isShowTab('navMenu','iconStyles') -->

			<div ng-if="isShowTab('navMenu','menuStyles')&&isActiveName('oxy_nav_menu')">

				<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
					<div class="oxygen-sidebar-breadcrumb-icon" 
						ng-click="switchTab('navMenu','responsive')">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
					</div>
					<div class="oxygen-sidebar-breadcrumb-all-styles" 
						ng-click="switchTab('navMenu','responsive')"><?php _e("Mobile Responsive","oxygen"); ?></div>
					<div class="oxygen-sidebar-breadcrumb-separator">/</div>
					<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Menu Styles","oxygen"); ?></div>
				</div>

				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("menu_responsive_background_color", __("Background Color", "oxygen")); ?>
				</div>

				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("menu_responsive_link_color", __("Link Text Color", "oxygen"), 'oxygen-typography-font-color'); ?>
				</div>

				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("menu_responsive_hover_link_color", __("Link Hover Color", "oxygen"), 'oxygen-typography-font-color'); ?>
				</div>

				<div class='oxygen-control-wrapper'>
					<label class='oxygen-control-label'><?php _e("Link Padding", "component-theme"); ?></label>
					<div class='oxygen-control'>
						<div class='oxygen-four-sides-measure-box'>
							<?php $oxygen_toolbar->measure_box('menu_responsive_padding_top','px', true, false); ?>
							<?php $oxygen_toolbar->measure_box('menu_responsive_padding_right','px', true, false); ?>
							<?php $oxygen_toolbar->measure_box('menu_responsive_padding_bottom','px', true, false); ?>
							<?php $oxygen_toolbar->measure_box('menu_responsive_padding_left','px', true, false); ?>
							<div class="oxygen-flex-line-break"></div>
							<div class="oxygen-apply-all-trigger">
								<?php _e("apply all »", "oxygen"); ?>
							</div>
						</div>
					</div>
				</div>

			</div><!-- isShowTab('navMenu','menuStyles') -->


			<div ng-if="isShowTab('navMenu','responsiveDropdowns')&&isActiveName('oxy_nav_menu')">

				<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
					<div class="oxygen-sidebar-breadcrumb-icon" 
						ng-click="switchTab('navMenu','responsive')">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
					</div>
					<div class="oxygen-sidebar-breadcrumb-all-styles" 
						ng-click="switchTab('navMenu','responsive')"><?php _e("Mobile Responsive","oxygen"); ?></div>
					<div class="oxygen-sidebar-breadcrumb-separator">/</div>
					<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Dropdowns","oxygen"); ?></div>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class="oxygen-checkbox">
							<input type="checkbox"
								ng-true-value="'on'" 
								ng-false-value="'off'"
								ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['menu_responsive_dropdowns']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_nav_menu','menu_responsive_dropdowns');">
							<div class='oxygen-checkbox-checkbox'
								ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('menu_responsive_dropdowns')=='on'}">
								<?php _e("Include dropdowns links in the responsive menu","oxygen"); ?>
							</div>
						</label>
					</div>
				</div>

			</div><!-- isShowTab('navMenu','reposnsiveDropdowns') -->
		
		</div>

	<?php }


	/**
	 * Output JS for toggle menu in responsive mode
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */
	
	function output_js() { ?>

		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('body').on('click', '.oxy-menu-toggle', function() {
					jQuery(this).parent('.oxy-nav-menu').toggleClass('oxy-nav-menu-open');
					jQuery('body').toggleClass('oxy-nav-menu-prevent-overflow');
					jQuery('html').toggleClass('oxy-nav-menu-prevent-overflow');
				});
				var selector = '.oxy-nav-menu-open .menu-item a[href*="#"]';
				jQuery('body').on('click', selector, function(){
					jQuery('.oxy-nav-menu-open').removeClass('oxy-nav-menu-open');
					jQuery('body').removeClass('oxy-nav-menu-prevent-overflow');
					jQuery('html').removeClass('oxy-nav-menu-prevent-overflow');
					jQuery(this).click();
				});
			});
		</script>

	<?php }

}


// Create inctance
global $oxygen_vsb_components;
$oxygen_vsb_components['nav_menu'] = new Oxy_Nav_Menu( array( 
			'name' 		=> __('Menu','oxygen'),
			'tag' 		=> 'oxy_nav_menu',
			'params' 	=> array(
					array(
						"param_name" 	=> "menu_id",
						"hidden" 		=> true,
						"css" 			=> false,
					),
					array(
						"type" 			=> "flex-layout",
						"heading" 		=> __("Menu Layout", "oxygen"),
						"param_name" 	=> "menu_flex-direction",
						"css" 			=> true,
						"vertical_text" => __("Vertical", "oxygen"),
						"horizontal_text" => __("Horizontal", "oxygen"),
						"ng_show" 		=> "!hasOpenTabs('navMenu')&&iframeScope.currentState=='original'"
					),
				),
			'advanced' 	=> array(
					'other' => array(
						'values' 	=> array (
								'menu_flex-direction' 	=> "row",
								'menu_responsive' 		=> "page-width",
								'dropdowns'				=> "on",
								'menu_dropdown_arrow'	=> "on",
								
								// set default units. TODO: find a better way
								'menu_padding-bottom-unit' 			=> "px",
								'menu_padding-top-unit' 			=> "px",
								'menu_padding-left-unit' 			=> "px",
								'menu_padding-right-unit' 			=> "px",
								'menu_dropdowns_padding-bottom-unit'=> "px",
								'menu_dropdowns_padding-top-unit' 	=> "px",
								'menu_dropdowns_padding-left-unit' 	=> "px",
								'menu_dropdowns_padding-right-unit' => "px",
								'menu_margin-bottom-unit' 			=> "px",
								'menu_margin-top-unit' 				=> "px",
								'menu_margin-left-unit' 			=> "px",
								'menu_margin-right-unit' 			=> "px",
								'menu_font-size-unit' 				=> "px",
								'menu_letter-spacing-unit' 			=> "px",
								'menu_border-top-width-unit' 		=> "px",
								'menu_border-bottom-width-unit' 	=> "px",
								'menu_border-top-width-unit' 		=> "px",
								'menu_border-bottom-width-unit' 	=> "px",
								'menu_hover_border-top-width-unit' 		=> "px",
								'menu_hover_border-bottom-width-unit' 	=> "px",
								'menu_active_border-top-width-unit' 	=> "px",
								'menu_active_border-bottom-width-unit' 	=> "px",
							)
					),
				),
			'not_css_params' => array(
					'menu_font-size',
					'menu_color',
					'menu_font-weight',
					'menu_letter-spacing',
					'menu_justify-content',
					'menu_text-transform',
					'menu_-webkit-font-smoothing',
					'menu_padding-top',
					'menu_padding-left',
					'menu_padding-right',
					'menu_padding-bottom',
					'menu_margin-top',
					'menu_margin-left',
					'menu_margin-right',
					'menu_margin-bottom',
					'menu_color',
					'menu_background-color',
					'menu_border-top-width',
					'menu_border-bottom-width',
					'menu_transition-duration',
					'menu_hover_color',
					'menu_hover_background-color',
					'menu_hover_border-top-width',
					'menu_hover_border-bottom-width',
					'menu_active_color',
					'menu_active_background-color',
					'menu_active_border-top-width',
					'menu_active_border-bottom-width',
					'dropdowns',
					'dropdown_arrow',
					'menu_dropdown_arrow',
					'menu_dropdowns_background-color',
					'menu_dropdowns_hover_background-color',
					'menu_dropdowns_color',
					'menu_dropdowns_hover_color',
					'menu_dropdowns_padding-top',
					'menu_dropdowns_padding-left',
					'menu_dropdowns_padding-right',
					'menu_dropdowns_padding-bottom',
					'menu_responsive',
					'menu_responsive_icon_size', 
					'menu_responsive_icon_margin', 
					'menu_responsive_icon_color', 
					'menu_responsive_icon_hover_color', 
					'menu_responsive_padding_size', 
					'menu_responsive_padding_color', 
					'menu_responsive_padding_hover_color', 
					'menu_responsive_background_color',
					'menu_responsive_link_color', 
					'menu_responsive_hover_link_color', 
					'menu_responsive_padding_bottom',
					'menu_responsive_padding_top',
					'menu_responsive_padding_left',
					'menu_responsive_padding_right',
					'menu_responsive_dropdowns',
					'menu_justify-content')
			)
		); 
