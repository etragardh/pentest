<?php

/**
 * AOS https://michalsnik.github.io/aos/
 *
 * @since 2.2
 * @author Ilya K.
 */

Class Oxygen_AOS {

	public $animations_list;
	public $anchor_placements;
	public $easing_functions;

	private $script_loaded = false;

	/**
	 * Consrtuctor
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */

	function __construct() {

		add_action("oxygen_vsb_component_attr", 		array($this, "attributes"));
		add_action("oxygen_vsb_effects_tabs_before",	array($this, "effects_tab"));
		add_action("oxygen_vsb_effects_tab_content", 	array($this, "effects_tab_settings"));
		add_action("oxygen_vsb_page_settings_tabs", 	array($this, "page_settings_tab"));
		add_action("oxygen_vsb_page_settings_content", 	array($this, "page_settings"));
		add_action("oxygen_vsb_global_styles_tabs", 	array($this, "global_settings_tab"));
		add_action("oxygen_vsb_settings_content", 		array($this, "global_settings"));

		add_action("wp_enqueue_scripts", array($this, "check_aos_classes") );
		add_action("ct_footer_js", array($this, "classes_js") );
		add_action("ct_footer_js", array($this, "init") );

		$this->animations_list = array(
			"fade" => __("Fade","oxygen"),
			"fade-up" => __("Fade Up","oxygen"),
			"fade-down" => __("Fade Down","oxygen"),
			"fade-left" => __("Fade Left","oxygen"),
			"fade-right" => __("Fade Right","oxygen"),
			"fade-up-right" => __("Fade Up Right","oxygen"),
			"fade-up-left" => __("Fade Up Left","oxygen"),
			"fade-down-right" => __("Fade Down Right","oxygen"),
			"fade-down-left" => __("Fade Down Left","oxygen"),
			"flip-up" => __("Flip Up","oxygen"),
			"flip-down" => __("Flip Down","oxygen"),
			"flip-left" => __("Flip Left","oxygen"),
			"flip-right" => __("Flip Right","oxygen"),
			"slide-up" => __("Slide Up","oxygen"),
			"slide-down" => __("Slide Down","oxygen"),
			"slide-left" => __("Slide Left","oxygen"),
			"slide-right" => __("Slide Right","oxygen"),
			"zoom-in" => __("Zoom In","oxygen"),
			"zoom-in-up" => __("Zoom In Up","oxygen"),
			"zoom-in-down" => __("Zoom In Down","oxygen"),
			"zoom-in-left" => __("Zoom In Left","oxygen"),
			"zoom-in-right" => __("Zoom In Right","oxygen"),
			"zoom-out" => __("Zoom Out","oxygen"),
			"zoom-out-up" => __("Zoom Out Up","oxygen"),
			"zoom-out-down" => __("Zoom Out Down","oxygen"),
			"zoom-out-left" => __("Zoom Out Left","oxygen"),
			"zoom-out-right" => __("Zoom Out Right","oxygen"),
		);

		$this->anchor_placements = array(
		    "top-bottom" => __("Top Bottom","oxygen"),
		    "top-center" => __("Top Center","oxygen"),
		    "top-top" => __("Top Top","oxygen"),
		    "center-bottom" => __("Center Bottom","oxygen"),
		    "center-center" => __("Center Center","oxygen"),
		    "center-top" => __("Center Top","oxygen"),
		    "bottom-bottom" => __("Bottom Bottom","oxygen"),
		    "bottom-center" => __("Bottom Center","oxygen"),
		    "bottom-top" => __("Bottom Top","oxygen"),
		);

		$this->easing_functions = array(
		    "linear" => __("linear","oxygen"),
		    "ease" => __("ease","oxygen"),
		    "ease-in" => __("ease-in","oxygen"),
		    "ease-out" => __("ease-out","oxygen"),
		    "ease-in-out" => __("ease-in-out","oxygen"),
		    "ease-in-back" => __("ease-in-back","oxygen"),
		    "ease-out-back" => __("ease-out-back","oxygen"),
		    "ease-in-out-back" => __("ease-in-out-back","oxygen"),
		    "ease-in-sine" => __("ease-in-sine","oxygen"),
		    "ease-out-sine" => __("ease-out-sine","oxygen"),
		    "ease-in-out-sine" => __("ease-in-out-sine","oxygen"),
		    "ease-in-quad" => __("ease-in-quad","oxygen"),
		    "ease-out-quad" => __("ease-out-quad","oxygen"),
		    "ease-in-out-quad" => __("ease-in-out-quad","oxygen"),
		    "ease-in-cubic" => __("ease-in-cubic","oxygen"),
		    "ease-out-cubic" => __("ease-out-cubic","oxygen"),
		    "ease-in-out-cubic" => __("ease-in-out-cubic","oxygen"),
		    "ease-in-quart" => __("ease-in-quart","oxygen"),
		    "ease-out-quart" => __("ease-out-quart","oxygen"),
		    "ease-in-out-quart" => __("ease-in-out-quart","oxygen"),
		);
	}


	/**
	 * AOS attributes action hook 
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */
	    
	function attributes($options) {

	   	$attr = "";

	   	if ( !isset($options['aos_enable']) || $options['aos_enable'] == '' || $options['aos_enable'] == 'false' ) {
			return;
		}

	   	if ( isset($options['aos_type']) && $options['aos_type'] !== "") {
			$attr .= ' data-aos="'.$options['aos_type'].'"';
			$this->loadAOSscripts();
		}
		else {
			$page_settings = ct_get_page_settings();
			$global_settings = ct_get_global_settings();

			$aos = wp_parse_args(
				// remove empty values
				array_filter($page_settings['aos']), 
				// use global settings as defaults fallback
				$global_settings['aos']);
			
			if ( isset($aos) && isset($aos['type']) && $aos['type']!="") {
				$attr .= ' data-aos="'.$aos['type'].'"';
				$this->loadAOSscripts();
			}
			else {
				return;
			}
		}

		if ( isset($options['aos_duration']) && $options['aos_duration'] !== "" ) {
			$attr .= ' data-aos-duration="'.$options['aos_duration'].'"';
		}

		if ( isset($options['aos_easing']) && $options['aos_easing'] !== "" ) {
			$attr .= ' data-aos-easing="'.$options['aos_easing'].'"';
		}

		if ( isset($options['aos_offset']) && $options['aos_offset'] !== "" ) {
			$attr .= ' data-aos-offset="'.$options['aos_offset'].'"';
		}

		if ( isset($options['aos_delay']) && $options['aos_delay'] !== "" ) {
			$attr .= ' data-aos-delay="'.$options['aos_delay'].'"';
		}

		if ( isset($options['aos_anchor']) && $options['aos_anchor'] !== "" ) {
			$attr .= ' data-aos-anchor="'.$options['aos_anchor'].'"';
		}

		if ( isset($options['aos_anchor_placement']) && $options['aos_anchor_placement'] !== "" ) {
			$attr .= ' data-aos-anchor-placement="'.$options['aos_anchor_placement'].'"';
		}

		if ( isset($options['aos_once']) && $options['aos_once'] !== "" ) {
			$attr .= ' data-aos-once="'.$options['aos_once'].'"';
		}

		echo $attr;
	}


	/**
	 * Add AOS setttings Tab to Advanced > Effects 
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */
	    
	function effects_tab() { ?>

		<div class='oxygen-sidebar-advanced-subtab'
			ng-click="switchTab('effects', 'animation-on-scroll')"
			ng-hide="hasOpenTabs('effects')||iframeScope.isEditing('custom-selector')"
			ng-class="{'oxy-styles-present' : iframeScope.isTabHasOptions('effects','animation-on-scroll')}">
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/animation.svg' />
				<span><?php _e("Animate On Scroll","oxygen"); ?></span>
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg' />
		</div>

	<?php }


	/**
	 * Add AOS setttings to Advanced > Effects 
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */
	    
	function effects_tab_settings() { 

		global $oxygen_toolbar; ?>

	<div class="oxygen-sidebar-flex-panel" ng-if="isShowTab('effects', 'animation-on-scroll')">

		<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb" >
			<div class="oxygen-sidebar-breadcrumb-icon" ng-click="$parent.$parent.switchTab('advanced', 'effects');$parent.$parent.closeTabs(['effects']);">
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
			</div>
			<div class="oxygen-sidebar-breadcrumb-all-styles" ng-click="$parent.$parent.switchTab('advanced', 'effects');$parent.$parent.closeTabs(['effects']);"><?php _e("Effects","oxygen"); ?></div>
			<div class="oxygen-sidebar-breadcrumb-separator">/</div>
			<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Animate On Scroll","oxygen"); ?></div>
		</div>

		<?php $oxygen_toolbar->checkbox_with_wrapper('aos-enable',__('Enable Animation','oxygen'), 'true', 'false'); ?>

		<div ng-show="$parent.iframeScope.getOption('aos-enable')=='true'">

		<div class='oxygen-control-row'>
			<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
				<div class="oxy-style-indicator"
					ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('aos-type')&&!IDHasOption('aos-type'),'oxygen-has-id-value':iframeScope.IDHasOption('aos-type')}">
				</div>
				<label class='oxygen-control-label'><?php _e("Animation Type","oxygen"); ?></label>
				<div class='oxygen-control'>
					<div class="oxygen-select oxygen-select-box-wrapper">
						<div class="oxygen-select-box"
							ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'aos-type')}">
							<div class="oxygen-select-box-current">{{$parent.iframeScope.getOption('aos-type')}}</div>
							<div class="oxygen-select-box-dropdown"></div>
						</div>
						<div class="oxygen-select-box-options">
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.setOptionModel('aos-type','')">&nbsp;</div>
							<?php foreach ($this->animations_list as $name => $label) : ?>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.setOptionModel('aos-type','<?php echo $name; ?>')"><?php echo $label; ?>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class='oxygen-control-row'>
			<?php $oxygen_toolbar->slider_measure_box_with_wrapper('aos-duration',__('Animation Duration','oxygen'), 'ms', 50, 3000, null, 50); ?>
		</div>

		<div class='oxygen-control-row'>
			<div class='oxygen-control-wrapper'>
				<div class="oxy-style-indicator"
					ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('aos-anchor-placement')&&!IDHasOption('aos-anchor-placement'),'oxygen-has-id-value':iframeScope.IDHasOption('aos-anchor-placement')}">
				</div>
				<label class='oxygen-control-label'><?php _e("Anchor Placement","oxygen"); ?></label>
				<div class='oxygen-control'>
					<div class="oxygen-select oxygen-select-box-wrapper">
						<div class="oxygen-select-box"
							ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'aos-anchor-placement')}">
							<div class="oxygen-select-box-current">{{$parent.iframeScope.getOption('aos-anchor-placement')}}</div>
							<div class="oxygen-select-box-dropdown"></div>
						</div>
						<div class="oxygen-select-box-options">
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.setOptionModel('aos-anchor-placement','')">&nbsp;</div>
							<?php foreach ($this->anchor_placements as $name => $label) : ?>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.setOptionModel('aos-anchor-placement','<?php echo $name; ?>')"><?php echo $label; ?>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class='oxygen-control-row'>
			<div class='oxygen-control-wrapper'>
				<div class="oxy-style-indicator"
					ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('aos-easing')&&!IDHasOption('aos-easing'),'oxygen-has-id-value':iframeScope.IDHasOption('aos-easing')}">
				</div>
				<label class='oxygen-control-label'><?php _e("Animation Easing","oxygen"); ?></label>
				<div class='oxygen-control'>
					<div class="oxygen-select oxygen-select-box-wrapper">
						<div class="oxygen-select-box"
							ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'aos-easing')}">
							<div class="oxygen-select-box-current">{{$parent.iframeScope.getOption('aos-easing')}}</div>
							<div class="oxygen-select-box-dropdown"></div>
						</div>
						<div class="oxygen-select-box-options">
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.setOptionModel('aos-easing','')">&nbsp;</div>
							<?php foreach ($this->easing_functions as $name => $label) : ?>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.setOptionModel('aos-easing','<?php echo $name; ?>')"><?php echo $label; ?>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class='oxygen-control-row'>
			<?php $oxygen_toolbar->measure_box_with_wrapper('aos-offset',__('Animation Trigger Offset','oxygen'), 'px'); ?>
		</div>

		<div class='oxygen-control-row'>
			<?php $oxygen_toolbar->measure_box_with_wrapper('aos-delay',__('Animation Delay','oxygen'), 'ms'); ?>
		</div>

		<div class='oxygen-control-row'>
			<?php $oxygen_toolbar->simple_input_with_wrapper('aos-anchor',__('Anchor','oxygen')); ?>
		</div>

		<div class='oxygen-control-row'>
	        <div class='oxygen-control-wrapper oxy-indicator-underline'>
				<div class="oxy-style-indicator"
					ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('aos-once')&&!IDHasOption('aos-once'),'oxygen-has-id-value':iframeScope.IDHasOption('aos-once')}">
				</div>
	            <label class='oxygen-control-label'><?php _e("Animate Only Once","oxygen"); ?></label>
	            <div class='oxygen-control'>
	                <div class='oxygen-button-list'>
	                    <?php $oxygen_toolbar->button_list_button('aos-once','true', __('yes','oxygen')); ?>
	                    <?php $oxygen_toolbar->button_list_button('aos-once','false', __('no','oxygen')); ?>
	                </div>
	            </div>
	        </div>
	    </div>

		</div>

	</div><!-- ng-if="isShowTab('effects', 'animation-on-scroll')" --><?php
	}


	/**
	 * Add a Tab to Manage > Settings > Page Settings
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */
	    
	function page_settings_tab() {
		
		global $oxygen_toolbar;

		$oxygen_toolbar->settings_child_tab(__("Animate On Scroll", "oxygen"), "page", "aos", "advanced/animation.svg");
	}


	/**
	 * Output AOS Page Settings
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */
	    
	function page_settings() { ?>
		
		<div ng-show="isShowChildTab('settings','page','aos')">
			<?php require_once "page-aos.view.php"; ?>
		</div>
	
	<?php }


	/**
	 * Output AOS Global Settings Tab
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */
	    
	function global_settings_tab() {
		
		global $oxygen_toolbar;

		$oxygen_toolbar->settings_child_tab(__("Animate On Scroll", "oxygen"), "default-styles", "aos", "advanced/animation.svg");
	
	}


	/**
	 * Output AOS Global Settings
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */
	    
	function global_settings() { ?>
		
		<div ng-if="isShowChildTab('settings','default-styles','aos')">
			<?php require_once "global-aos.view.php"; ?>
		</div>
	
	<?php }


	/**
	 * Output AOS init JS code
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */
	    
	function init() {

		// don't load in Builder UI
		if (defined('SHOW_CT_BUILDER') && !defined('OXYGEN_IFRAME')) {
			return;
		}

		// don't init if there was no AOS components found
		if (!$this->script_loaded && !defined('OXYGEN_IFRAME')) {
			return;
		}

		global $media_queries_list;
		$page_settings 	 = ct_get_page_settings();
		$global_settings = ct_get_global_settings();

		if(!is_array($page_settings['aos'])) {
			return;
		}

		$aos = wp_parse_args(
			// remove empty values
			array_filter($page_settings['aos']), 
			// use global settings as defaults fallback
			$global_settings['aos']);

		if (isset($aos['disable']) && $aos['disable']!='' && isset($media_queries_list[$aos['disable']])) {
			$disable = $media_queries_list[$aos['disable']]['maxSize'];
			// remove 'px' to keep only number value
			$disable = str_replace('px', '', $disable);
		}
		else {
			$disable = false;
		}
		?>

	  	AOS.init({
	  	<?php if ($aos['duration']) : ?>
			duration: <?php echo $aos['duration']; ?>,
		<?php endif; ?>
	  	<?php if ($aos['easing']) : ?>
	  		easing: '<?php echo $aos['easing']; ?>',
		<?php endif; ?>
	  	<?php if ($aos['offset']) : ?>
		  	offset: <?php echo $aos['offset']; ?>,
		<?php endif; ?>
	  	<?php if ($aos['delay']) : ?>
		  	delay: <?php echo $aos['delay']; ?>,
		<?php endif; ?>
	  	<?php if ($aos['once']) : ?>
		  	once: <?php echo $aos['once']; ?>,
		<?php endif; ?>
	  	<?php if ($aos['mirror']) : ?>
		  	mirror: <?php echo $aos['mirror']; ?>,
		<?php endif; ?>
	  	<?php if ($aos['type']) : ?>
		  	type: '<?php echo $aos['type']; ?>',
		<?php endif; ?>
		<?php if ($disable!==false&&!defined('OXYGEN_IFRAME')) : ?>
		  	disable: window.innerWidth < <?php echo $disable; ?>,
		<?php endif; ?>
	  	<?php if ($aos['anchor-placement']) : ?>
		  	anchorPlacement: '<?php echo $aos['anchor-placement']; ?>',		  	
		<?php endif; ?>
		})
		
		<?php if (!defined('OXYGEN_IFRAME')) : ?>
		jQuery('body').addClass('oxygen-aos-enabled');
		<?php endif; ?>

		<?php if (isset($aos['refresh_on_page'])&&$aos['refresh_on_page']=='true') : ?>
		jQuery(document).ready( function() {
			AOS.refresh();
		})
		<?php endif; ?>

	<?php }


	/**
	 * Enqueue AOS scripts if present in classes
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */
	    
	function check_aos_classes() {

		$classes = get_option("oxygen_aos_classes", array());
		if (!empty($classes)) {
			$this->loadAOSscripts();
		}
	}


	/**
	 * Output AOS init JS code
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */
	    
	function classes_js() {

		$classes = get_option("oxygen_aos_classes", array());
		if (is_array($classes)) {
			foreach ($classes as $class => $aos) {
				if (is_array($aos) && $aos['aos-enable']==='true') {
					if ( isset($aos['aos_type']) && $aos['aos_type'] !== "") {
						// do nothing...
					}
					else {
						$page_settings = ct_get_page_settings();
						$global_settings = ct_get_global_settings();

						$aos_settings = wp_parse_args(
							// remove empty values
							array_filter($page_settings['aos']), 
							// use global settings as defaults fallback
							$global_settings['aos']);
						
						if ( isset($aos_settings) && isset($aos_settings['type']) && $aos_settings['type']!="") {
							$aos['aos-type'] = $aos_settings['type'];
						}
					}
					echo "jQuery('.$class').attr({";
					foreach ($aos as $key => $value) {
						if ($key == 'aos-type') {
							$key = 'aos';
						}
						echo "'data-$key': '$value',";
					}
					echo "});";
				}
			}
		}
	}
	

	/**
	 * Public function to Load AOS on frontend
	 * 
	 * @since 3.2
	 * @author Ilya K.
	 */

	public function loadAOSscripts() {

		if ($this->script_loaded) {
			return;
		}

		$this->script_loaded = true;

		wp_enqueue_script( 'oxygen-aos', 	CT_FW_URI . '/vendor/aos/aos.js', null, true);
		
		global $oxygen_vsb_css_styles;

		if ( $oxygen_vsb_css_styles && 
			 get_option("oxygen_vsb_load_aos_in_head") === "true") {
			
			$oxygen_vsb_css_styles->add( 'oxygen-aos', 	CT_FW_URI . '/vendor/aos/aos.css');
			$oxygen_vsb_css_styles->enqueue(array('oxygen-aos'));
		}
		else {
			wp_enqueue_style ( 'oxygen-aos', 	CT_FW_URI . '/vendor/aos/aos.css');
		}
	}

}

$GLOBALS['oxygen_vsb_aos'] = $oxygen_vsb_aos = new Oxygen_AOS();