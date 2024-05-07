<?php 

/**
 * CT_Shortcode Class
 *
 * @since 0.1.7
 */

Class CT_Shortcode extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// change button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxy_folder_wordpress_components", array( $this, "component_button" ) );

		add_filter( 'template_include', array( $this, 'ct_shortcode_single_template'), 100 );
		
		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// woocommerce specific
		if(isset($_REQUEST['action']) && stripslashes($_REQUEST['action']) == 'ct_render_shortcode') {
			// do not redirect shop page when its a builder preview
			add_action( 'wp', array( $this, 'ct_code_remove_template_redirect'));
		}
	}

	function ct_code_remove_template_redirect() {
		global $wp_filter;
		if(isset($wp_filter['template_redirect']['10']['wc_template_redirect'])) {
			//unset($wp_filter['template_redirect']['10']['wc_template_redirect']);
			//echo "WooCommerce Shop page is essentially a redirect to Products Archive.";
		}

	}


	/**
	 * Add a [ct_shortcode] shortcode to WordPress
	 *
	 * @since 0.2.3
	 */

	function add_shortcode( $atts, $content, $name ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		ob_start();	

		?><<?php echo esc_attr($options['tag']) ?> id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php echo (!$content && !empty($options['full_shortcode']) ) ? do_shortcode( $options['full_shortcode'] ) : do_shortcode( $content ); ?></<?php echo esc_attr($options['tag']) ?>><?php

		return ob_get_clean();
	}

	
	/**
	 * Add WordPress folder button
	 *
	 * @since 0.4.0
	 * @author Ilya K.
	 */
	
	function component_button() { 
		
	if (oxygen_hide_element_button($this->options['tag'])) {
		return;
	}
		
	?>

	<div class="oxygen-add-section-element"
				data-searchid="<?php echo strtolower( preg_replace('/\s+/', '_', sanitize_text_field( $this->options['name'] ) ) ) ?>"
				ng-click="iframeScope.addComponent('<?php echo esc_attr($this->options['tag']); ?>','shortcode')">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/shortcode.svg' />
			<?php echo esc_html($this->options['name']); ?>
		</div>

	<?php }


	/**
	 * This function hijacks the template to return special template that renders the code results
	 * for the ct_code_block element to load the content into the builder for preview.
	 * 
	 * @since 0.4.0
	 * @author gagan goraya
	 */
	
	function ct_shortcode_single_template( $template ) {

		$new_template = '';

		if(isset($_REQUEST['action']) && stripslashes($_REQUEST['action']) == 'ct_render_shortcode') {
			$nonce  	= $_REQUEST['nonce'];
			$post_id 	= $_REQUEST['post_id'];
			
			// check nonce
			if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
			    // This nonce is not valid.
			    die( 'Security check' );
			}
			
			if ( file_exists(dirname(dirname( __FILE__)) . '/layouts/' . 'shortcode.php') ) {
				$new_template = dirname(dirname( __FILE__)) . '/layouts/' . 'shortcode.php';
			}
		}

		if ( '' != $new_template ) {
				return $new_template ;
			}

		return $template;
	}

}

global $oxygen_vsb_components;
$oxygen_vsb_components['shortcode'] = new CT_Shortcode ( array( 
		'name' 		=> 'Shortcode',
		'tag' 		=> 'ct_shortcode',
		'shortcode'	=> true,
		'params' 	=> array(
							array(
								"param_name" 	=> "full_shortcode",
								"value" 		=> "",
								"type" 			=> "textfield",
								"heading" 		=> __("Full shortcode","oxygen"),
								"css" 			=> false,
							),
							array(
								"type" 			=> "tag",
								"heading" 		=> __("Tag"),
								"param_name" 	=> "tag",
								"value" 		=> array (
													"div" => "DIV",
													"p" => "P",
													"h1" => "H1",
													"h2" => "H2",
													"h3" => "H3",
													"h4" => "H4",
													"h5" => "H5",
													"h6" => "H6",
													"section" 	=> "section",
													"footer" 	=> "footer",
													"header" 	=> "header",
													"article" 	=> "article",
													"main" 		=> "main",
													"figcaption"=> "figcaption",
													"time" 		=> "time",
													"summary" 	=> "summary",
													"details" 	=> "details",
													"aside" 	=> "aside",
													"figure" 	=> "figure",
													"hgroup" 	=> "hgroup",
													"mark" 		=> "mark",
													"nav" 		=> "nav",
												),
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
					"other" => array(
						"values" 	=> array (
							'placeholder-width-unit' => 'px',
							'placeholder-height-unit' => 'px',
							)
					),
                    'allow_shortcodes' => true,
				)
		)
	);
