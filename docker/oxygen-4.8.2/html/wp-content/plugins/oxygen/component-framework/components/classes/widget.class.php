<?php 

Class CT_Widget extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// remove component button
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		
		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// add toolbar
		add_action("ct_toolbar_widgets_folder", 	array( $this, "widgets_list") );

		// add specific options
		add_action("ct_toolbar_component_settings", array( $this, "widget_settings") );

		// render builder preview
		add_filter( 'template_include', array( $this, 'widget_single_template'), 100 );
		
        add_filter("oxygen_elements_without_presets", array($this, "oxygen_elements_without_presets_callback"));
	}


	/**
	 * Add a [ct_widget] shortcode to WordPress
	 *
	 * @since 0.2.3
	 */

	function add_shortcode( $atts, $content = null, $name = null ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		$atts = json_decode($atts['ct_options'], true);

		$instance = isset($atts['original']['instance']) ? (isset($atts['original']['paramsBase64'])?$this->ct_decode_widget_shortcode_params($atts['original']['instance']):$atts['original']['instance']) : array();

		ob_start();

		if ( ! isset( $GLOBALS['wp_widget_factory']->widgets[$atts['original']['class_name']] ) ) {
			echo "<div><b>Error!</b><br/> No '".$atts['original']['class_name']."' widget registered in this installation.<br/><br/></div>";
			return ob_get_clean();
		}

		$instance = $GLOBALS['wp_widget_factory']->widgets[$atts['original']['class_name']]->update($instance, array());
		
		?><div id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php the_widget( $atts['original']['class_name'], $instance); ?></div><?php

		return ob_get_clean();
	}

	
	/**
	 * Decode widget options
	 *
	 * @since 1.2
	 */

	function ct_decode_widget_shortcode_params($array_value) {
		 return array_map(function($value) {
		 	if ( is_array($value) ) {
				return $this->ct_decode_widget_shortcode_params($value);
		 	}
		 	elseif ( is_bool($value) ) {
		 		return $value;
		 	}
		 	else {
				return oxygen_base64_decode_for_json($value);
			}
		}, $array_value );
	}


	/**
	 * Output settings
	 *
	 * @since 0.2.3
	 */

	function widget_settings() { ?>

		<div class="oxygen-widget-settings-wrapper" 
			ng-if="isActiveName('<?php echo $this->options['tag']; ?>')">
			<div class="oxygen-widget-settings">
				<div id="ct-dialog-widget-content" class="ct-dialog-widget-content">
					<!-- AJAX loaded content here -->
				</div>
			</div>
		</div>
		<div class="oxygen-widget-settings-bottom" 
			ng-if="isActiveName('<?php echo $this->options['tag']; ?>')">
			<div class="oxygen-widget-settings-apply-button" title="<?php _e("Apply widget options","oxygen"); ?>" 
				ng-click="iframeScope.applyWidgetInstance()">
				<?php _e("Apply", "oxygen"); ?>
			</div>
			<?php
				/**
				 * To add buttons (or other elements) to widget bottom row
				 * @since 2.0
				 */ 
				do_action("oxygen_widget_settings_apply_after"); 
			?>
		</div>

	<?php }


	/**
	 * Display all widgets
	 *
	 * @since  0.2.3
	 */

	function widgets_list() {

		if (oxygen_hide_element_button($this->options['tag'])) {
			return;
		}
		
		foreach ( $GLOBALS['wp_widget_factory']->widgets as $class => $widget ) { ?>

			<div class="oxygen-add-section-element" title="<?php echo $widget->widget_options['description']; ?>"
				data-searchid="widget_<?php echo strtolower( preg_replace('/\s+/', '_', sanitize_text_field( $widget->name ) ) ) ?>"

				ng-click="iframeScope.addWidget('<?php echo $class; ?>','<?php echo $widget->id_base; ?>', '<?php echo addslashes(html_entity_decode($widget->name, ENT_QUOTES) ); ?>')">
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/widgets.svg' />
				<?php echo $widget->name; ?>
			</div>

		<?php }
	}


	/**
	 * This function hijacks the template to return special template that renders the code results
	 * for the ct_code_block element to load the content into the builder for preview.
	 * 
	 * @since 0.4.0
	 * @author gagan goraya
	 */
	
	function widget_single_template( $template ) {

		$new_template = '';

		if(isset($_REQUEST['action']) && stripslashes($_REQUEST['action']) == 'ct_render_widget') {
			$nonce  	= $_REQUEST['nonce'];
			$post_id 	= $_REQUEST['post_id'];
			
			// check nonce
			if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
			    // This nonce is not valid.
			    die( 'Security check' );
			}
			
			if ( file_exists(dirname(dirname( __FILE__)) . '/layouts/' . 'widget.php') ) {
				$new_template = dirname(dirname( __FILE__)) . '/layouts/' . 'widget.php';
			}
		}

		if ( '' != $new_template ) {
				return $new_template ;
			}

		return $template;
	}
}


// Create inctance
global $oxygen_vsb_components;
$oxygen_vsb_components['widget'] = new CT_Widget( array( 
			'name' 		=> 'Widget',
			'tag' 		=> 'ct_widget',
			'params' 	=> array(
					array(
						"type" 			=> "textfield",
						"param_name" 	=> "class_name",
						"hidden" 		=> true,
						"css" 			=> false,
					),
					array(
						"type" 			=> "textfield",
						"param_name" 	=> "id_base",
						"hidden" 		=> true,
						"css" 			=> false,
					),
					array(
						"type" 			=> "textfield",
						"param_name" 	=> "instance",
						"hidden" 		=> true,
						"css" 			=> false,
					),
					array(
						"type" 			=> "checkbox",
						"heading" 		=> __("Don't render in Oxygen","oxygen"),
						"param_name" 	=> "dont_render",
						"value" 		=> "false",
						"true_value" 	=> "true",
						"false_value" 	=> "false",
						"css" 			=> false 
					),
					array(
						"type" 			=> "measurebox",
						"heading" 		=> __("Placeholder Width"),
						"param_name" 	=> "placeholder-width",
						"value" 		=> "",
						"condition" 	=> "dont_render=true",
						"css" 			=> false
					),
					array(
						"type" 			=> "measurebox",
						"heading" 		=> __("Placeholder Height"),
						"param_name" 	=> "placeholder-height",
						"value" 		=> "",
						"condition" 	=> "dont_render=true",
						"css" 			=> false
					),
				),
			'advanced' => array(
					"positioning" => array(
						"values" 	=> array (
							'width' 	 => '100',
							'width-unit' => '%',
							)
					),
					"other" => array(
						"values" 	=> array (
							'placeholder-width-unit' => 'px',
							'placeholder-height-unit' => 'px',
							)
					),
			        'allow_shortcodes' => true,
                ),
			'not_css_params' => array(
					'pretty_name',
				)
			)
		);
