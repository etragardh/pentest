<?php

/**
 * Toggle Component Class
 * 
 * @since 2.0
 */

Class Oxy_Toggle extends CT_Component {

	var $options;
	var $js_added = false;

	function __construct( $options ) {

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
        add_action("oxygen_helpers_components_interactive", array( $this, "component_button" ) );

        // add white list options
        add_filter("oxy_options_white_list", array( $this, "white_list_options") );

        // generate defaults styles class
        add_action("oxygen_default_classes_output", array( $this, "generate_defaults_css" ) );

        // generate user styles class
        add_filter("oxygen_user_classes_output", array( $this, "generate_classes_css"), 10, 7);

        // generate #id stlyes
        add_filter("oxy_component_css_styles", array( $this, "generate_id_css"), 10, 5);

        // include only for builder
		if (isset( $_GET['oxygen_iframe'] )) {
			add_action( 'wp_footer', array( $this, 'output_js' ) );
		}

	}

	 /**
     * Generate CSS for arrays parameters only
     * 
     * @since 2.0
     * @author Ilya
     */

    function white_list_options($options) {

        $options_to_add = array(
            // TODO: make it fetch from params automatically based on some flag
            "toggle_icon_size",
            "toggle_icon_size-unit",
            "toggle_icon_color",
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }

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

        $options['selector'] = ".oxy-toggle";

        echo $this->generate_css($options, true);
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

        $is_icon_box = false;

        foreach ($options as $key => $value) {
            if (strpos($key,"toggle")!==false) {
                $is_icon_box = true;
                break;
            }
        }

        $options['selector'] = ".".$class;
        if ($is_icon_box) {
            $css .= $this->generate_css($options, true, $defaults[$this->options['tag']]);
            $is_icon_box = false;
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
     * @author Ilya K.
     */

    function generate_css($params=false, $class=false, $defaults=array()) {

        if ($this->in_repeater_cycle()) return;

        $params["selector"] = $this->get_corrected_element_selector($params["selector"], $class);

        ob_start(); ?>

        <?php if(isset($params['toggle_icon_size'])) : ?>
        <?php echo $params["selector"]; ?> .oxy-expand-collapse-icon {
            <?php $this->output_single_css_property("font-size", $params['toggle_icon_size'], $this->get_css_unit('toggle_icon_size', $params, $defaults)); ?>
        }
        <?php endif; ?>

        <?php if(isset($params['toggle_icon_color'])) : ?>
        <?php echo $params["selector"]; ?> .oxy-expand-collapse-icon::before,
        <?php echo $params["selector"]; ?> .oxy-expand-collapse-icon::after {
            <?php $this->output_single_css_property("background-color", oxygen_vsb_get_global_color_value($params['toggle_icon_color'])); ?>
        }
        <?php endif; ?>

        <?php return ob_get_clean();
    }


	/**
	 * Add a [oxy_toggle] shortcode to WordPress
	 *
	 * @since 2.0
     * @author Ilya K.
	 */

	function add_shortcode( $atts, $content, $name ) {
		
		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		// add JavaScript code only once and if shortcode presented
		if ($this->js_added === false) {
			add_action( 'wp_footer', array( $this, 'output_js' ) );
			$this->js_added = true;
		}

		ob_start();

		?><div id="<?php echo esc_attr($options['selector']); ?>" class="<?php if(isset($options['classes'])) echo esc_attr($options['classes']); ?>" <?php if(isset($options['toggle_target'])): ?>data-oxy-toggle-target="<?php echo esc_attr($options['toggle_target']); ?>"<?php endif; ?> data-oxy-toggle-initial-state="<?php echo esc_attr($options['toggle_init_state']); ?>" data-oxy-toggle-active-class="<?php echo esc_attr($options['toggle_active_class']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>>
			<div class='oxy-expand-collapse-icon' href='#'></div>
			<div class='oxy-toggle-content'>
                <?php echo (is_array($content)) ? do_oxygen_elements( $content ) : do_shortcode( $content ); ?>
			</div>
		</div><?php

		return ob_get_clean();
	}


	/**
	 * Output JS for toggle menu in responsive mode
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */
	
	function output_js() { ?>

		<script type="text/javascript">

			jQuery(document).ready(function() {
                let event = new Event('oxygenVSBInitToggleJs');
                document.dispatchEvent(event);
			});

            document.addEventListener("oxygenVSBInitToggleJs",function(){
                oxygenVSBInitToggleState();
            },false);

			oxygenVSBInitToggleState = function() {

				jQuery('.oxy-toggle').each(function() {
				
					var initial_state = jQuery(this).attr('data-oxy-toggle-initial-state'),
					   toggle_target = jQuery(this).attr('data-oxy-toggle-target'),
                       active_class = jQuery(this).attr('data-oxy-toggle-active-class');
				
					if (initial_state == 'closed') {
						if (!toggle_target) {
							jQuery(this).next().hide();
						} else {
							jQuery(toggle_target).hide();
						}
						jQuery(this).children('.oxy-expand-collapse-icon').addClass('oxy-eci-collapsed');
                        jQuery(this).removeClass(active_class)
					}
                    else {
                        jQuery(this).addClass(active_class)
                    }
				});
			}

            jQuery("body").on('click', '.oxy-toggle', function() {

                var toggle_target  = jQuery(this).attr('data-oxy-toggle-target'),
                    active_class   = jQuery(this).attr('data-oxy-toggle-active-class');

                jQuery(this).toggleClass(active_class)
                jQuery(this).children('.oxy-expand-collapse-icon').toggleClass('oxy-eci-collapsed');

                if (!toggle_target) {
                    jQuery(this).next().toggle();
                } else {
                    jQuery(toggle_target).toggle();
                }

                // force 3rd party plugins to rerender things inside the toggle
                jQuery(window).trigger('resize');
            });
		</script>

	<?php } 
}


global $oxygen_vsb_components;
$oxygen_vsb_components['toggle'] = new Oxy_Toggle ( 
		array( 
			'name' 		=> __('Toggle', 'oxygen'),
			'tag' 		=> 'oxy_toggle',
			'params' 	=> array(
					array(
						"type" 			=> "colorpicker",
						"heading" 		=> __("Icon color"),
						"param_name" 	=> "toggle_icon_color",
						"css" 			=> false
					),
					array(
						"type" 			=> "slider-measurebox",
						"heading" 		=> __("Icon size","oxygen"),
						"param_name" 	=> "toggle_icon_size",
						"min"			=> "8",
						"max"			=> "72",
						"css" 			=> false
					),
					array(
						"param_name" 	=> "toggle_icon_size-unit",
						"value" 		=> "px",
						"hidden"		=> true,
						"css" 			=> false
					),
					array(
                        "type"          => "radio",
                        "heading"       => __("Toggle Initial State", "oxygen"),
                        "param_name"    => "toggle_init_state",
                        "value"         => array(
                                            'open'    => __("open", "oxygen"),
                                            'closed'   => __("closed", "oxygen"),
                                        ),
                        "css"           => false,
                    ),
                    array(
						"type" 			=> "textfield",
						"heading" 		=> __("Toggle Target","oxygen"),
						"param_name" 	=> "toggle_target",
						"placeholder" 	=> __("next element","oxygen"),
						"css" 			=> false
					),
				)
		)
);

?>