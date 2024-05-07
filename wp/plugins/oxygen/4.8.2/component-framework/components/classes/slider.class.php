<?php 

Class CT_Slider extends CT_Component {

	var $js_css_added = false;

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		for ( $i = 2; $i <= 16; $i++ ) {
			add_shortcode( $this->options['tag'] . "_" . $i, array( $this, 'add_shortcode' ) );
		}

		// add specific options
		add_action("ct_toolbar_component_settings", array( $this, "slider_settings") );
		
		// change component button place
        remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
        add_action("oxygen_helpers_components_interactive", array( $this, "component_button" ) );

        // generate defaults styles class
        add_action("oxygen_default_classes_output", array( $this, "generate_defaults_css" ) );

        // generate user styles class
        add_filter("oxygen_user_classes_output", array( $this, "generate_classes_css"), 10, 7);

        // generate #id stlyes
        add_filter("oxy_component_css_styles", array( $this, "generate_id_css"), 10, 5);
	}


	/**
	 * Add a toolbar button
	 *
	 * @since 0.1
	 */
	
	function component_button() { 
		
		if (oxygen_hide_element_button($this->options['tag'])) {
			return;
		}
		
		?>

		<div class="oxygen-add-section-element"
			data-searchid="<?php echo strtolower( preg_replace('/\s+/', '_', sanitize_text_field( $this->options['name'] ) ) ) ?>"
			ng-click="iframeScope.addComponent('<?php echo esc_attr($this->options['tag']); ?>');addSlides()">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/slider.svg' />
			<?php echo esc_html($this->options['name']); ?>
		</div>

	<?php }


	/**
	 * Add a [ct_slider] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content = null, $name = null ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		ob_start();

		// add JS/CSS to footer only once
		$this->js_css_added = false; // TODO: find out why both shortcode and JSON callbacks used and remove this line
		if ($this->js_css_added === false && !wp_doing_ajax()) {
			echo "<link rel='stylesheet' id='oxygen-unslider-css'  href='" . CT_FW_URI . "/vendor/unslider/unslider.css' type='text/css' media='all'/>";
			add_action("wp_footer", array( $this, "js_css_output") );
			$this->js_css_added = true;
		}

		$options = $this->set_options( $atts );
		$unique_class = 'ct_unique_slider_'.rand(0,9999); //workaround for duplicated ID's

		$gutenberg_class = "";
		if( !empty( $_GET['oxygen_gutenberg_script'] ) ) {
        	$options['slider_autoplay']='no';
        	$options['slider_show_arrows']='yes';

        	if (isset($options['slider_remove_padding']) && $options['slider_remove_padding']=='yes') {
	        	$gutenberg_class .= " oxygen-slider-remove-padding";
        	}
        	if (isset($options['slider_dots_overlay']) && $options['slider_dots_overlay']=='yes') {
	        	$gutenberg_class .= " oxygen-slider-dots-overlay";
        	}
        	if (!isset($options['slider_animation']) || $options['slider_animation']!='fade') {
	        	$gutenberg_class .= " oxygen-slider-horizontal";
        	}
        }

		if ( isset($_GET['action']) && $_GET['action'] == "ct_render_innercontent") {
			// include Unslider when loading inside templates Inner Content
			wp_enqueue_script( 'oxygen-unslider', 		CT_FW_URI . '/vendor/unslider/unslider-min.js', array('jquery') );
			wp_enqueue_script( 'oxygen-event-move', 	CT_FW_URI . '/vendor/unslider/jquery.event.move.js');
			wp_enqueue_script( 'oxygen-event-swipe', 	CT_FW_URI . '/vendor/unslider/jquery.event.swipe.js');
			wp_print_scripts(array('oxygen-unslider','oxygen-event-move','oxygen-event-swipe'));
		}

		$hashSelector = $this->get_corrected_element_selector($options['selector']);
		
		?><div id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']). ' ' .$unique_class.$gutenberg_class; ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><div class="oxygen-unslider-container"><ul><?php echo (is_array($content)) ? do_oxygen_elements( $content ) : do_shortcode( $content ); ?></ul></div></div><script class="ct-slider-script">jQuery(document).ready(function($){$('<?php echo $hashSelector.'.'.$unique_class; ?> .oxygen-unslider-container:not(.unslider-horizontal,.unslider-fade)').unslider({autoplay: <?php echo ($options['slider_autoplay']=='yes') ? "true" : "false"; ?>, delay: <?php echo $options['slider_autoplay_delay']; ?>, animation: '<?php echo $options['slider_animation']; ?>', speed : <?php echo $options['slider_animation_speed']; ?>, arrows: <?php echo ($options['slider_show_arrows']=='yes') ? "true" : "false" ?>, nav: <?php echo ($options['slider_show_dots']=='yes') ? "true" : "false"; ?>, infinite: <?php echo ($options['slider_infinite']=='yes'&&$options['slider_animation']!='fade') ? "true" : "false"; ?>})});</script><?php

		return ob_get_clean();
	}

	/**
	 * Output settings
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function slider_settings() { 

		global $oxygen_toolbar; ?>

		<div class="oxygen-sidebar-flex-panel"
			ng-show="isActiveName('ct_slider')">

			<div class="oxygen-sidebar-advanced-subtab" 
				ng-click="switchTab('slider', 'styling')" 
				ng-show="!hasOpenTabs('slider')">
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
					<?php _e("Styling", "oxygen"); ?>
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
			</div>

			<div class="oxygen-sidebar-advanced-subtab" 
				ng-click="switchTab('slider', 'configuration')" 
				ng-show="!hasOpenTabs('slider')">
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/general-config.svg">
					<?php _e("Configuration", "oxygen"); ?>
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
			</div>
		
			<div ng-if="isShowTab('slider','styling')">
				
				<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
					<div class="oxygen-sidebar-breadcrumb-icon" 
						ng-click="tabs.slider=[]">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
					</div>
					<div class="oxygen-sidebar-breadcrumb-all-styles" 
						ng-click="tabs.slider=[]"><?php _e("All Styles","oxygen"); ?></div>
					<div class="oxygen-sidebar-breadcrumb-separator">/</div>
					<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Styling","oxygen"); ?></div>
				</div>

				<div class='oxygen-control-row'>
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Animation","oxygen"); ?></label>
						<div class='oxygen-control'>
							<div class='oxygen-button-list'>
								<label class='oxygen-button-list-button'
									ng-class="{'oxygen-button-list-button-active':iframeScope.getOption('slider-animation')=='horizontal'}">
										<input type="radio" name="slider-animation" value="horizontal"
											ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-animation']" 
											ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-animation');iframeScope.rebuildDOM(iframeScope.component.active.id)"/>
										horizontal
								</label>
								<label class='oxygen-button-list-button'
									ng-class="{'oxygen-button-list-button-active':iframeScope.getOption('slider-animation')=='fade'}">
										<input type="radio" name="slider-animation" value="fade"
											ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-animation']" 
											ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-animation');iframeScope.rebuildDOM(iframeScope.component.active.id)"/>
										fade
								</label>
							</div>
						</div>
					</div>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Animation Speed (milliseconds)","oxygen"); ?></label>
						<div class='oxygen-control'>
							<div class='oxygen-input'>
								<input type="text" spellcheck="false"
									ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-animation-speed']" 
									ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-animation-speed');iframeScope.rebuildDOM(iframeScope.component.active.id)"/>
							</div>
						</div>
					</div>
				</div>

				<div class='oxygen-control-row'>
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Arrow Color","oxygen"); ?></label>
						<div class='oxygen-control'>
							<div class='oxygen-button-list'>
								<label class='oxygen-button-list-button'
									ng-class="{'oxygen-button-list-button-active':iframeScope.getOption('slider-arrow-color')=='darker'}">
										<input type="radio" name="slider-arrow-color" value="darker"
											<?php $this->ng_attributes('slider-arrow-color', 'model,change'); ?>
											ng-change="iframeScope.rebuildDOM(iframeScope.component.active.id)"/>
										darker
								</label>
								<label class='oxygen-button-list-button'
									ng-class="{'oxygen-button-list-button-active':iframeScope.getOption('slider-arrow-color')=='lighter'}">
										<input type="radio" name="slider-arrow-color" value="lighter"
											<?php $this->ng_attributes('slider-arrow-color', 'model,change'); ?>
											ng-change="iframeScope.rebuildDOM(iframeScope.component.active.id)"/>
										lighter
								</label>
							</div>
						</div>
					</div>
				</div>

				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("slider-dot-color", __("Dot Color", "oxygen")); ?>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class="oxygen-checkbox">
							<input type="checkbox"
								ng-true-value="'yes'" 
								ng-false-value="'no'"
								ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-remove-padding']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-remove-padding')">
							<div class='oxygen-checkbox-checkbox'
								ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('slider-remove-padding')=='yes'}">
								<?php _e("Remove Padding Around Slides","oxygen"); ?>
							</div>
						</label>
					</div>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class="oxygen-checkbox">
							<input type="checkbox"
								ng-true-value="'yes'" 
								ng-false-value="'no'"
								ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-dots-overlay']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-dots-overlay')">
							<div class='oxygen-checkbox-checkbox'
								ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('slider-dots-overlay')=='yes'}">
								<?php _e("Dots Overlay Slider","oxygen"); ?>
							</div>
						</label>
					</div>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class="oxygen-checkbox">
							<input type="checkbox"
								ng-true-value="'yes'" 
								ng-false-value="'no'"
								ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-stretch-slides']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-stretch-slides')">
							<div class='oxygen-checkbox-checkbox'
								ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('slider-stretch-slides')=='yes'}">
								<?php _e("All Slides Stretch To Same Height","oxygen"); ?>
							</div>
						</label>
					</div>
				</div>

				<?php $oxygen_toolbar->measure_box_with_wrapper('slider-slide-padding',__("Force Slide Padding To", "oxygen") ); ?>

			</div>

			<div ng-if="isShowTab('slider','configuration')">

				<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
					<div class="oxygen-sidebar-breadcrumb-icon" 
						ng-click="tabs.slider=[]">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
					</div>
					<div class="oxygen-sidebar-breadcrumb-all-styles" 
						ng-click="tabs.slider=[]"><?php _e("All Styles","oxygen"); ?></div>
					<div class="oxygen-sidebar-breadcrumb-separator">/</div>
					<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Configuration","oxygen"); ?></div>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class="oxygen-checkbox">
							<input type="checkbox"
								ng-true-value="'yes'" 
								ng-false-value="'no'"
								ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-show-arrows']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-show-arrows');iframeScope.rebuildDOM(iframeScope.component.active.id)">
							<div class='oxygen-checkbox-checkbox'
								ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('slider-show-arrows')=='yes'}">
								<?php _e("Show Arrows","oxygen"); ?>
							</div>
						</label>
					</div>
				</div>
				
				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class="oxygen-checkbox">
							<input type="checkbox"
								ng-true-value="'yes'" 
								ng-false-value="'no'"
								ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-show-dots']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-show-dots');iframeScope.rebuildDOM(iframeScope.component.active.id)">
							<div class='oxygen-checkbox-checkbox'
								ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('slider-show-dots')=='yes'}">
								<?php _e("Show Dots","oxygen"); ?>
							</div>
						</label>
					</div>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class="oxygen-checkbox">
							<input type="checkbox"
								ng-true-value="'yes'" 
								ng-false-value="'no'"
								ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-autoplay']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-autoplay');iframeScope.rebuildDOM(iframeScope.component.active.id)">
							<div class='oxygen-checkbox-checkbox'
								ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('slider-autoplay')=='yes'}">
								<?php _e("Autoplay","oxygen"); ?>
							</div>
						</label>
					</div>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class="oxygen-checkbox">
							<input type="checkbox"
								ng-true-value="'yes'" 
								ng-false-value="'no'"
								ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-infinite']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-infinite');">
							<div class='oxygen-checkbox-checkbox'
								ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('slider-infinite')=='yes'}">
								<?php _e("Infinite","oxygen"); ?>
							</div>
						</label>
					</div>
				</div>

				<div class="oxygen-control-row"
					ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-autoplay'] == 'yes'">
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Delay (milliseconds)","oxygen"); ?></label>
						<div class='oxygen-control'>
							<div class='oxygen-input'>
								<input type="text" spellcheck="false"
									ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-autoplay-delay']" 
									ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-autoplay-delay');iframeScope.rebuildDOM(iframeScope.component.active.id)"/>
							</div>
						</div>
					</div>
				</div>
			
			</div>

		</div>
	
	<?php }


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

        $options['selector'] = ".ct-slider";

        // ugly fix for slider only, seems like other components all has "_" 
        foreach ($options as $key => $value) {
        	$normalized_options[str_replace("_", "-", $key)] = $value;
        }

        echo $this->generate_css($normalized_options, true);
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

        $is_component = false;

        foreach ($options as $key => $value) {
            if (strpos($key,"slider-")!==false) {
                $is_component = true;
                break;
            }
        }

        $options['selector'] = ".".$class;
        if ($is_component) {
            $css .= $this->generate_css($options, true, $defaults[$this->options['tag']]);
            $is_component = false;
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

        ob_start();

        if ( isset($params['slider-arrow-color']) && 
			 $params['slider-arrow-color'] == 'lighter') {
        	$styles = "";
			$styles .= $params['selector'] . " .unslider-arrow {";
			$styles .= "background-color: rgba(255,255,255,0.2); ";
			$styles .= "}";
			echo $styles;
		}

		if ( isset($params['slider-dot-color']) ) {
			$styles = "";
			$styles .= $params['selector'] . " .unslider-nav ol li {";
			$styles .= "border-color: " . oxygen_vsb_get_global_color_value($params['slider-dot-color']) . "; ";
			$styles .= "}";

			$styles .= $params['selector'] . " .unslider-nav ol li.unslider-active {";
			$styles .= "background-color: " . oxygen_vsb_get_global_color_value($params['slider-dot-color']) . "; ";
			$styles .= "}";
			echo $styles;
		}

        if (isset($params['slider-remove-padding']) && $params['slider-remove-padding']=='yes') { ?>
            <?php echo $params['selector']; ?> .unslider {
				padding: 0px;
			}
			<?php echo $params['selector']; ?> .unslider-wrap.unslider-carousel > li {
				padding: 0px;
			}
			<?php echo $params['selector']; ?> .unslider-arrow.next {
				right: 10px;
				z-index: 100;
			}
			<?php echo $params['selector']; ?> .unslider-arrow.prev {
				left: 10px;
				z-index: 100;
			}
        <?php }
        
        if (isset($params['slider-dots-overlay']) && $params['slider-dots-overlay']=='yes') { ?>
			<?php echo $params['selector']; ?> .unslider .unslider-nav {
			    position: absolute;
			    bottom: 0;
			    left: 0;
			    right: 0;
			    z-index: 100;
			}
		<?php }

		if (isset($params['slider-stretch-slides']) && $params['slider-stretch-slides']=='yes') { ?>
			<?php echo $params['selector']; ?> .unslider-wrap {
				display: flex;
			}
			<?php echo $params['selector']; ?> .ct-slide {
				height: 100%;
			}
			<?php echo $params['selector']; ?> .unslider, 
			<?php echo $params['selector']; ?> .oxygen-unslider-container, 
			<?php echo $params['selector']; ?> .unslider-wrap, 
			<?php echo $params['selector']; ?> .unslider-wrap li {
				height: 100%;
			}
		<?php 
		}

		if (isset($params['slider-stretch-slides']) && $params['slider-stretch-slides']=='yes'&&
			isset($params['slider-animation']) && $params['slider-animation']=='fade') { ?>
			<?php echo $params['selector']; ?> .unslider-fade ul li.unslider-active {
				width: 100%;
			}
		<?php 
		}

		if (isset($params['slider-slide-padding'])) {
			echo $params['selector']; ?> .ct-slide {
				<?php $this->output_single_css_property("padding", $params['slider-slide-padding'], $this->get_css_unit('slider-slide-padding', $params, $defaults)); ?>
			}
		<?php
		}
		
		return ob_get_clean();
    }


	
	/**
	 * Output JS/CSS to footer
	 *
	 * @since 2.0
	 */

	function js_css_output() {

		// include Unslider
		wp_enqueue_script( 'oxygen-unslider', 		CT_FW_URI . '/vendor/unslider/unslider-min.js', array('jquery') );
		wp_enqueue_script( 'oxygen-event-move', 	CT_FW_URI . '/vendor/unslider/jquery.event.move.js');
		wp_enqueue_script( 'oxygen-event-swipe', 	CT_FW_URI . '/vendor/unslider/jquery.event.swipe.js');
	}
}


// Create Slider instance
global $oxygen_vsb_components;
$oxygen_vsb_components['slider'] = new CT_Slider( array( 
			'name' 		=> __('Slider','oxygen'),
			'tag' 		=> 'ct_slider',
			'advanced' 	=> array(
				'styles' => array(
					'values' => array(
							'slider-arrow-color' 	=> "darker",
							'slider-dot-color' 		=> "#ffffff",
							'slider-remove-padding' => "no",
							'slider-dots-overlay'	=> "no",
							'slider-stretch-slides'	=> "no",
							'slider-slide-padding'	=> "0",
							'slider-slide-padding-unit'	=> "px",
						)
				),
				'configuration' => array(
					'values' => array(
							'slider-show-arrows' 	=> "yes",
							'slider-show-dots' 		=> "yes",
							'slider-autoplay' 		=> "no",
							'slider-autoplay-delay' => "3000",
							'slider-infinite' 		=> "no",
							'slider-animation' 		=> "horizontal",
							'slider-animation-speed'=> "750",
						)
				),
				'size' => array(
					'values' => array(
							'width' 		 => '100',
							'width-unit'  	 => '%'
					)
				)
			),
			'not_css_params' => array(
				'slider-show-dots', 
				'slider-show-arrows', 
				'slider-autoplay', 
				'slider-autoplay-delay', 
				'slider-infinite', 
				'slider-animation', 
				'slider-animation-speed',
				'slider-remove-padding',
				'slider-dots-overlay',
				'slider-stretch-slides',
				'slider-slide-padding',
				'slider-slide-padding-unit',
			)
		)
);
