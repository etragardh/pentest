<?php 

/**
 * Header Builder component
 *
 * @since 2.0
 * @author Ilya K.
 */

Class Oxy_Header_Builder extends CT_Component {

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
		add_action("ct_toolbar_component_settings", array( $this, "header_settings"), 9 );

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_helpers_components_composite", array( $this, "component_button" ) );

		// generate #id stlyes
        add_filter("oxy_component_css_styles", array( $this, "generate_id_css"), 10, 5);
	}


	/**
	 * Add a toolbar button
	 *
	 * @since 2.0
	 */
	function component_button() { 
		
		if (oxygen_hide_element_button($this->options['tag'])) {
			return;
		}
		
		?>

		<div class="oxygen-add-section-element"
			data-searchid="<?php echo strtolower( preg_replace('/\s+/', '_', sanitize_text_field( $this->options['name'] ) ) ) ?>"
			ng-click="iframeScope.addComponents('<?php echo esc_attr($this->options['tag']); ?>','oxy_header_row')">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/header.svg' />
			<?php echo esc_html($this->options['name']); ?>
		</div>

	<?php }


	/**
	 * Add a [oxy_header] shortcode to WordPress
	 *
	 * @since 2.0
	 */

	function add_shortcode( $atts, $content, $name ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
            return '';
        }

		$options = $this->set_options( $atts );

		global $media_queries_list_above;
		if (isset($options['sticky_media'])&&$options['sticky_media']!='always') {
			$min_size = $media_queries_list_above[$options['sticky_media']]['minSize'];
		}

		$overlay_class = "";
		if (isset($options['overlay_header_above'])&&$options['overlay_header_above']!=='never') {
			$overlay_class = "oxy-overlay-header";
		}

		ob_start();

		?><header id="<?php echo esc_attr($options['selector']); ?>" class="oxy-header-wrapper <?php echo ($options["sticky_header"]=="yes") ? "oxy-sticky-header " : ""; echo $overlay_class; ?> <?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php echo (is_array($content)) ? do_oxygen_elements( $content ) : do_shortcode( $content ); ?></header>
		<?php if ($options["sticky_header"]=="yes") : ?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				var selector = "#<?php echo esc_attr($options['selector']); ?>",
					scrollval = parseInt("<?php echo esc_attr($options['sticky_scroll_distance']); ?>");
				if (!scrollval || scrollval < 1) {
					<?php if (isset($options['sticky_media'])&&$options['sticky_media']!='always') : ?>
					if (jQuery(window).width() >= <?php echo intval($min_size); ?>){
					<?php endif; ?>
						jQuery("body").css("margin-top", jQuery(selector).outerHeight());
						jQuery(selector).addClass("oxy-sticky-header-active");
					<?php if (isset($options['sticky_media'])&&$options['sticky_media']!='always') : ?>
					}
					<?php endif; ?>
				}
				else {
					var scrollTopOld = 0;
					jQuery(window).scroll(function() {
						if (!jQuery('body').hasClass('oxy-nav-menu-prevent-overflow')) {
							if (jQuery(this).scrollTop() > scrollval 
								<?php if (isset($options["sticky_header_upward"])&&$options["sticky_header_upward"]=="yes") : ?> 
								&& jQuery(this).scrollTop() < scrollTopOld 
								<?php endif; ?>
								) {
								if (
									<?php if (isset($options['sticky_media'])&&$options['sticky_media']!='always') : ?>
									jQuery(window).width() >= <?php echo intval($min_size); ?> && 
									<?php endif; ?>
									!jQuery(selector).hasClass("oxy-sticky-header-active")) {
									if (jQuery(selector).css('position')!='absolute') {
										jQuery("body").css("margin-top", jQuery(selector).outerHeight());
									}
									jQuery(selector)
										.addClass("oxy-sticky-header-active")
									<?php if (isset($options['sticky_header_fade_in'])&&$options['sticky_header_fade_in']=='yes') : ?>
										.addClass("oxy-sticky-header-fade-in");
									<?php endif; ?>
								}
							}
							else {
								jQuery(selector)
									.removeClass("oxy-sticky-header-fade-in")
									.removeClass("oxy-sticky-header-active");
								if (jQuery(selector).css('position')!='absolute') {
									jQuery("body").css("margin-top", "");
								}
							}
							scrollTopOld = jQuery(this).scrollTop();
						}
					})
				}
			});
		</script><?php
		endif;

		$outputContent = ob_get_clean();

		$outputContent = apply_filters('oxygen_vsb_after_component_render', $outputContent, $this->options, $name);

        return $outputContent;

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
     * Generate specific CSS
     * 
     * @since 2.0
     * @author Ilya K.
     */

    function generate_css($params=false, $class=false, $defaults=array()) {

        global $media_queries_list_above;
    	
    	if ($params===false) {
            $params = $this->param_array;
        }

        if ($this->in_repeater_cycle()) return;

        $params["selector"] = $this->get_corrected_element_selector($params["selector"], $class);

        if (!isset($params['overlay-header-above'])) {
        	$params['overlay-header-above'] = $defaults['overlay-header-above'];
        }

 		ob_start(); 

 		if (isset($params['sticky_header_fade_in_speed'])): ?>
 		<?php echo $params['selector']; ?>.oxy-sticky-header-active {
			animation-duration: <?php echo $params['sticky_header_fade_in_speed']; ?>s;
 		}
 		<?php endif; ?>

 		<?php if ($params['overlay-header-above']=='never'||$params['overlay-header-above']=='') {
        	return ob_get_clean();
        }
        
   		if ($params['overlay-header-above']!='always') :
     	$min_size = $media_queries_list_above[$params['overlay-header-above']]['minSize']; ?>
        @media (min-width: <?php echo $min_size; ?>) {
    	<?php endif; ?>

			<?php echo $params['selector']; ?>.oxy-header.oxy-overlay-header {
				position: absolute;
				left: 0;
				right: 0;
				z-index: 20;
			}

			<?php echo $params['selector']; ?>.oxy-header.oxy-overlay-header:not(.oxy-sticky-header-active) .oxy-header-row,
			<?php echo $params['selector']; ?>.oxy-header.oxy-overlay-header:not(.oxy-sticky-header-active) {
				background-color: initial !important;
			}

			<?php echo $params['selector']; ?>.oxy-header.oxy-overlay-header .oxygen-hide-in-overlay{
				display: none;
			}

			<?php echo $params['selector']; ?>.oxy-header.oxy-overlay-header .oxygen-only-show-in-overlay{
				display: block;
			}

		<?php if ($params['overlay-header-above']!='always') : ?>
        }
    	<?php endif;

        return ob_get_clean();
    }


	/**
	 * Output special settings in Basic Styles tab
	 *
	 * @since 2.0
	 */

	function header_settings() { 

		global $oxygen_toolbar; ?>

		<?php if (!oxygen_hide_element_button('oxy_header')) : ?>
		<div class="oxygen-control-row"
			ng-show="isActiveName('oxy_header')&&!hasOpenTabs('oxy_header')">
			<div class="oxygen-control-wrapper">
				<div id="oxygen-add-another-row" class="oxygen-add-section-element"
					ng-click="iframeScope.addComponent('oxy_header_row')">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/header.svg' />
					<?php _e("Add Another Row","oxygen"); ?>
				</div>
			</div>
		</div>
		<?php endif; ?>
		
		<div ng-show="isActiveName('<?php echo $this->options['tag']; ?>')&&!hasOpenTabs('oxy_header')">
			<?php $oxygen_toolbar->media_queries_list_with_wrapper("stack-header-vertically", __("Stack Vertically Below","oxygen"), true); ?>
		</div>

	<?php }
}


// Create Header Builder instance
global $oxygen_vsb_components;
$oxygen_vsb_components['header_builder'] = new Oxy_Header_Builder( array( 
			'name' 		=> __('Header Builder','oxygen'),
			'tag' 		=> 'oxy_header',
			'tabs'  => array(
                'sticky' => array(
                    'heading' => __('Sticky','oxygen'),
                    'params' => array(
                    	array(
							"type" 			=> "checkbox",
							"param_name" 	=> "sticky_header",
							"value" 		=> "no",
							"true_value" 	=> "yes",
							"false_value" 	=> "no",
							"label" 		=> __("Enable Sticky Header", "oxygen"),
							"css" 			=> false
						),
						/*array(
							"type" 			=> "checkbox",
							"label" 		=> __("Only Sticky On Upward Scroll","oxygen"),
							"param_name" 	=> "sticky_header_upward",
							"value" 		=> "no",
							"true_value" 	=> "yes",
							"false_value" 	=> "no",
							"condition"		=> "sticky_header=yes",
							"css" 			=> false
						),*/
						array(
							"type" 			=> "textfield",
							"heading" 		=> __("Scroll Distance (px)","oxygen"),
							"param_name" 	=> "sticky_scroll_distance",
							"value" 		=> "300",
							"condition"		=> "sticky_header=yes",
							"css" 			=> false
						),
						array(
							"type" 			=> "colorpicker",
							"heading" 		=> __("Sticky Background Color","oxygen"),
							"param_name" 	=> "sticky-background-color",
							"condition"		=> "sticky_header=yes",
							"css" 			=> false
						),
						array(
							"type" 			=> "medialist_above",
							"heading" 		=> __("Sticky Above","oxygen"),
							"value" 		=> "page-width",
							"param_name" 	=> "sticky-media",
							"condition"		=> "sticky_header=yes",
							"always_option" => true,
							"never_option"  => false,
							"css" 			=> false
						),
						array(
							"type" 			=> "textfield",
							"heading" 		=> __("Sticky Box Shadow","oxygen"),
							"param_name" 	=> "sticky-box-shadow",
							"value" 		=> "0px 0px 10px rgba(0,0,0,0.3);",
							"condition"		=> "sticky_header=yes",
							"css" 			=> false
						),
						array(
							"param_name" 	=> "header-custom-width-unit",
							"value" 		=> "auto",
							"hidden" 		=> true
						),
						array(
							"type" 			=> "textfield",
							"heading" 		=> __("Sticky Header Z-Index","oxygen"),
							"param_name" 	=> "sticky_zindex",
							"value" 		=> "",
							"condition"		=> "sticky_header=yes",
							"css" 			=> false
						),
						array(
							"type" 			=> "checkbox",
							"label" 		=> __("Fade In Sticky","oxygen"),
							"param_name" 	=> "sticky_header_fade_in",
							"value" 		=> "no",
							"true_value" 	=> "yes",
							"false_value" 	=> "no",
							"condition"		=> "sticky_header=yes",
							"css" 			=> false
						),
						array(
							"type" 			=> "slider-measurebox",
							"heading" 		=> __("Fade In Speed","oxygen"),
							"param_name" 	=> "sticky_header_fade_in_speed",
							"value" 		=> 0.3,
							"min"			=> "0",
							"max"			=> "1",
							"step" 			=> 0.1,
							"condition"		=> "sticky_header_fade_in=yes&&sticky_header=yes",
							"param_units" 	=> 's',
							"css" 			=> false
						),
                    ),
                ),
                'overlay' => array(
                    'heading' => __('Overlay','oxygen'),
                    'params' => array(
                    	array(
	                    	"type" 			=> "medialist_above",
							"heading" 		=> __("Overlay Header","oxygen"),
							"value" 		=> "",
							"param_name" 	=> "overlay-header-above",
							"always_option" => true,
							"never_option" 	=> true,
							"css" 			=> false
						),
						array(
	                    	"type" 			=> "text",
	                    	"text" 			=> __("Enable the header overlay on specific pages only from Settings -> Page Settings","oxygen"),
	                    	"class" 		=> "oxygen-overlay-header-text",
							"css" 			=> false
						)
                    )
                ),
            ),
			'params' 	=> array(
				array(
					"type" 			=> "colorpicker",
					"heading" 		=> __("Background color"),
					"param_name" 	=> "background-color",
				),
			),
			'advanced' => array(
					'size' => array(
							'values' => array (
									'header-width' => 'page-width',
							)
					)
			),
			'not_css_params' => array(
				'stack-header-vertically'
			)
		)
);
