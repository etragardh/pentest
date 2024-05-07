<?php

/**
 * Tabs Component Class
 * 
 * @since 2.0
 */

Class Oxy_Tabs extends CT_Component {

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
		add_action("ct_toolbar_component_settings", array( $this, "tab_button"), 9 );

        // include only for builder
		if (isset( $_GET['oxygen_iframe'] )) {
			add_action( 'wp_footer', array( $this, 'output_js' ) );
		}
	}


	/**
	 * Add a [oxy_tabs] shortcode to WordPress
	 *
	 * @since 0.1
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

		ob_start();

		?><div id="<?php echo esc_attr($options['selector']); ?>" class="oxy-tabs-wrapper <?php if(isset($options['classes'])) echo esc_attr($options['classes']); ?>" data-oxy-tabs-active-tab-class='<?php echo esc_attr($options['active_tab_class']); ?>' data-oxy-tabs-contents-wrapper='<?php echo esc_attr($options['tabs_contents_wrapper']); ?>' <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php echo (is_array($content)) ? do_oxygen_elements( $content ) : do_shortcode( $content ); ?></div><?php

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

			function oxygenVSBInitTabs(element) {
				if (element!==undefined) {
					jQuery(element).find('.oxy-tabs-wrapper').addBack('.oxy-tabs-wrapper').each(function(index) {
						jQuery(this).children('.oxy-tabs-wrapper > div').eq(0).trigger('click');
					});
				}
				else {
					jQuery('.oxy-tabs-wrapper').each(function(index) {
						jQuery(this).children('.oxy-tabs-wrapper > div').eq(0).trigger('click');
					});
				}
			}

			jQuery(document).ready(function() {
                let event = new Event('oxygenVSBInitTabsJs');
                document.dispatchEvent(event);
			});

            document.addEventListener("oxygenVSBInitTabsJs",function(){
                oxygenVSBInitTabs();
            },false);
  
			// handle clicks on tabs  
			jQuery("body").on('click', '.oxy-tabs-wrapper > div', function(e) {

			    /* a tab or an element that is a child of a tab has been clicked. prevent any default behavior */
			    //e.preventDefault();
			    
			    /* which tab has been clicked? (e.target might be a child of the tab.) */
			    clicked_tab = jQuery(e.target).closest('.oxy-tabs-wrapper > div');
			    index = clicked_tab.index();  
			    
			    /* which tabs-wrapper is this tab inside? */
			    tabs_wrapper = jQuery(e.target).closest('.oxy-tabs-wrapper');

			    /* what class dp we use to signify an active tob? */
			    class_for_active_tab = tabs_wrapper.attr('data-oxy-tabs-active-tab-class');
			    
			    /* make all the other tabs in this tabs-wrapper inactive */
			    jQuery(tabs_wrapper).children('.oxy-tabs-wrapper > div').removeClass(class_for_active_tab);

			    /* make the clicked tab the active tab */    
			    jQuery(tabs_wrapper).children('.oxy-tabs-wrapper > div').eq(index).addClass(class_for_active_tab);

			    /* which tabs-contents-wrapper is used by these tabs? */
			    tabs_contents_wrapper_id = tabs_wrapper.attr('data-oxy-tabs-contents-wrapper');

			    /* try to grab the correct content wrapper, in case of duplicated ID's */
                $content_wrapper = jQuery(tabs_wrapper).next();
                if( $content_wrapper.attr("id") != tabs_contents_wrapper_id ) $content_wrapper = jQuery( '#' + tabs_contents_wrapper_id );

                $content_tabs = $content_wrapper.children( "div" );

                /* hide all of the content */
                $content_tabs.addClass('oxy-tabs-contents-content-hidden');
			    
			    /* unhide the content corresponding to the active tab*/
                $content_tabs.eq(index).removeClass('oxy-tabs-contents-content-hidden');
			  
			});                                 
		
		</script>

	<?php }


    /**
	 * Add a toolbar button
	 *
	 * @since 2.0
     * @author Ilya
	 */

	function tab_button() { 
		
		if (oxygen_hide_element_button($this->options['tag'])) {
			return;
		}
		
		?>

		<div class="oxygen-control-row"
			ng-show="isActiveName('oxy_tabs')">
			<div class="oxygen-control-wrapper">
				<div id="oxygen-add-another-row" class="oxygen-add-section-element"
					ng-click="iframeScope.addTab()">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/header.svg' />
					<?php _e("Add Another Tab","oxygen"); ?>
				</div>
			</div>
		</div>
		<div class="oxygen-control-row"
			ng-show="isActiveName('oxy_tabs_contents')">
			<div class="oxygen-control-wrapper">
				<div id="oxygen-add-another-row" class="oxygen-add-section-element"
					ng-click="iframeScope.addTab()">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/header.svg' />
					<?php _e("Add Another Tab Content","oxygen"); ?>
				</div>
			</div>
		</div>

	<?php }
}


global $oxygen_vsb_components;
$oxygen_vsb_components['tabs'] = new Oxy_Tabs ( 

		array( 
			'name' 		=> __('Tabs', 'oxygen'),
			'tag' 		=> 'oxy_tabs',
			'params' 	=> array(
					array(
						"type" 			=> "flex-layout",
						"heading" 		=> __("Layout Child Elements", "oxygen"),
						"param_name" 	=> "flex-direction",
						"css" 			=> true,
					),
					array(
						"type" 			=> "checkbox",
						"heading" 		=> __("Allow multiline"),
						"param_name" 	=> "flex-wrap",
						"value" 		=> "",
						"true_value" 	=> "wrap",
						"false_value" 	=> "",
						"condition" 	=> "flex-direction=row"
					),
					array(
						"type" => "positioning",
					),
					array(
						"type" 			=> "measurebox",
						"heading" 		=> __("Width"),
						"param_name" 	=> "width",
						"value" 		=> "",
					),
					array(
						"type" 			=> "colorpicker",
						"heading" 		=> __("Background color"),
						"param_name" 	=> "background-color",
					),
				),
			'advanced' 	=> array(
					'typography' => array(
						'values' 	=> array (
								'font-family' 	=> "",
								'font-size' 	=> "",
								'font-weight' 	=> "",
							)
					),
					'flex' => array(
						'values' 	=> array (
								'display' 		 => 'flex',
								'flex-direction' => 'row',
								'align-items' 	 => 'stretch',
								'justify-content'=> '',
								'text-align' 	 => '',
								'flex-wrap' 	 => 'nowrap',
							)
					),
                    'allowed_html' => 'post',
                    'allow_shortcodes' => true,
			),


			
		)
);

?>