<?php 

Class CT_Code_Block extends CT_Component {

	var $shortcode_options;
	var $shortcode_atts;

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// output code
		add_action( "wp_footer", array( $this, 'output_code' ), 100 );
		add_action( "oxygen_inner_content_footer", array( $this, 'output_code' ), 100 );

		add_filter( 'template_include', array( $this, 'ct_code_block_single_template'), 100 );

		// woocommerce specific
		if(isset( $_REQUEST['action'], $_REQUEST['post_id'] ) && stripslashes($_REQUEST['action']) == 'ct_exec_code') {
			// do not redirect shop page when its a builder preview
			add_action( 'wp', array( $this, 'ct_code_remove_template_redirect'));
		}

		// add specific options
		add_action("ct_toolbar_component_settings", array( $this, "code_block_settings") );
	}

	function ct_code_remove_template_redirect() {
		global $wp_filter;
		if(isset($wp_filter['template_redirect']['10']['wc_template_redirect'])) {
			unset($wp_filter['template_redirect']['10']['wc_template_redirect']);
			//echo "WooCommerce Shop page is essentially a redirect to Products Archive.";
		}

	}

	/**
	 * This function hijacks the template to return special template that renders the code results
	 * for the ct_code_block element to load the content into the builder for preview.
	 * 
	 * @since 0.4.0
	 * @author gagan goraya
	 */
	
	function ct_code_block_single_template( $template ) {

		$new_template = '';

		if(isset($_REQUEST['action']) && stripslashes($_REQUEST['action']) == 'ct_exec_code') {
			$nonce  	= $_REQUEST['nonce'];
			$post_id 	= intval( $_REQUEST['post_id'] );
			
			// check nonce
			if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
			    // This nonce is not valid.
			    die( 'Security check' );
			}
			
			if ( file_exists(dirname(dirname( __FILE__)) . '/layouts/' . 'code-block.php') ) {
				$new_template = dirname(dirname( __FILE__)) . '/layouts/' . 'code-block.php';
			}
		}

		if ( '' != $new_template ) {
				return $new_template ;
			}

		return $template;
	}


	/**
	 * Add a [ct_code_block] shortcode to WordPress
	 *
	 * @since 0.3.1
	 */

	function add_shortcode( $atts, $content, $name ) {
		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );
		$id = $options['id'];
		
		// save to instance
		$this->shortcode_options[$id] = $options;

		// save to instance
		$this->shortcode_atts[$id] = $atts;

		// don't run code during CSS caching
		global $oxygen_vsb_css_caching_active;
		if ( isset( $oxygen_vsb_css_caching_active ) && $oxygen_vsb_css_caching_active === true ) {			
			return "";
		}

		// lets base64_decode all the code types, if they are not coming from the
		if (!oxygen_doing_oxygen_elements()) {
			if(isset(json_decode($atts['ct_options'])->original)) {
				if(isset(json_decode($atts['ct_options'])->original->{'code-php'}) ) {
					$options['code_php'] = 	base64_decode($options['code_php']);
				}
			}
		}
		else {
			$this->shortcode_atts[$id]["json"] = true;
		}

		//$code_php = htmlspecialchars_decode($options['code_php'], ENT_QUOTES);
		
		$code_php = $options['code_php'];

		ob_start();

		if ( isset($options['unwrap']) && $options['unwrap'] == 'true' ) {
			// don't output
		}
		else {
			?><<?php echo esc_attr($options['tag']) ?> id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php 
		}

		// make sure errors are shown
		$error_reporting = error_reporting(E_ERROR | E_WARNING | E_PARSE);
		$display_errors = ini_get('display_errors');
		ini_set('display_errors', 1); 
		
		eval(' ?>'.$code_php.'<?php ');

		// set errors params back
		ini_set('display_errors', $display_errors); 
		error_reporting($error_reporting);

		if ( isset($options['unwrap']) && $options['unwrap'] == 'true' ) {
			// don't output
		}
		else {
			?></<?php echo esc_attr($options['tag']) ?>><?php
		}
		
		return ob_get_clean();
	}


	/**
	 * Echo custom JS/CSS code added by user
	 *
	 * @since 0.3.1
	 */

	function output_code() {

		if ( is_array( $this->shortcode_options ) ) {

			$all_code_js = array();
			$all_code_css = array();

			foreach ( $this->shortcode_options as $component_id => $options ) {
				
				$component_id = esc_attr( $component_id );
				
				$selector 	= esc_attr( $options['selector'] );

				$atts = $this->shortcode_atts[$component_id];
				
				// lets base64_decode all the code types, if they are not coming from the default
				if (!isset($atts["json"])) {	
					if(isset(json_decode($atts['ct_options'])->original)) {
						if(isset(json_decode($atts['ct_options'])->original->{'code-js'}) ) {
							$options['code_js'] = 	base64_decode($options['code_js']);
						}
						if(isset(json_decode($atts['ct_options'])->original->{'code-css'}) ) {
							$options['code_css'] = 	base64_decode($options['code_css']);
						}
					}
				}

				$code_js 	= $options['code_js'];
				$code_js 	= str_replace("%%ELEMENT_ID%%", $selector, $code_js);

				if (!in_array($code_js, $all_code_js) && trim($code_js) !== "") {

					$all_code_js[] = $code_js;

					echo "<script type=\"text/javascript\" id=\"ct_code_block_js_{$component_id}\">";
					echo $code_js;
					echo "</script>\r\n";
				}

				$code_css 	= $options['code_css'];
				$code_css 	= str_replace("%%ELEMENT_ID%%", $selector, $code_css);
				$code_css 	= preg_replace_callback(
					            "/color\(\d+\)/",
					            "oxygen_vsb_parce_global_colors_callback",
					            $code_css);

				if (!in_array($code_css, $all_code_css) && trim($code_css) !== "") {
					
					$all_code_css[] = $code_css;

					echo "<style type=\"text/css\" id=\"ct_code_block_css_{$component_id}\">";
					echo $code_css;
					echo "</style>\r\n";
				}
			}
		}
	}


	/**
	 * Output settings
	 *
	 * @since 2.0
	 * @author Ilya K. 
	 */

	function code_block_settings() { 
		
		if (!oxygen_vsb_current_user_can_full_access()) {
			return;
		}
		
		?>

		<div class="oxygen-sidebar-flex-panel"
			ng-show="isActiveName('ct_code_block')">
						
			<div class="oxygen-sidebar-advanced-subtab" 
				ng-click="showAllStylesFunc(); styleTabAdvance=true; switchTab('advanced', 'code-php')" ng-class="{'oxygen-active' : isShowTab('advanced','code-php')}">
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/css.svg">
				PHP &amp; HTML
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
			</div>
	
			<div class="oxygen-sidebar-advanced-subtab" 
				ng-click="showAllStylesFunc(); styleTabAdvance=true; switchTab('advanced', 'code-css')" ng-class="{'oxygen-active' : isShowTab('advanced','code-css')}">
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/css.svg">
				CSS
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
			</div>
			
			<div class="oxygen-sidebar-advanced-subtab" 
				ng-click="showAllStylesFunc(); styleTabAdvance=true; switchTab('advanced', 'code-js')" ng-class="{'oxygen-active' : isShowTab('advanced','code-js')}">
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/js.svg">
				JavaScript
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
			</div>

			<div class="oxygen-sidebar-advanced-subtab" 
				ng-click="showAllStylesFunc(); styleTabAdvance=true; switchTab('advanced', 'code-mixed'); toggleSidebar()" ng-class="{'oxygen-active' : isShowTab('advanced','code-mixed')}">
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/settings-icon.svg">
				Mixed Code View
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
			</div>

		</div>

	<?php }

}


// Create instance
global $oxygen_vsb_components;
$oxygen_vsb_components['code_block'] = new CT_Code_Block( array( 
			'name' 		=> 'Code Block',
			'tag' 		=> 'ct_code_block',
			'params' 	=> array(
					array(
						"type" 			=> "textfield",
						"param_name" 	=> "code-php",
						"value" 		=> "<?php\r\n  echo \"hello world!\";\r\n?>",
						"hidden"		=> true,
						"css" 			=> false,
					),
					array(
						"type" 			=> "textfield",
						"param_name" 	=> "code-js",
						"value" 		=> "",
						"hidden"		=> true,
						"css" 			=> false,
					),
					array(
						"type" 			=> "textfield",
						"param_name" 	=> "code-css",
						"value" 		=> "",
						"hidden"		=> true,
						"css" 			=> false,
					),
					array(
						"type" 			=> "checkbox",
						"heading" 		=> __("Unwrap Code Block PHP"),
						"param_name" 	=> "unwrap",
						"value" 		=> "false",
						"true_value" 	=> "true",
						"false_value" 	=> "false",
						"description"	=> __("Unwrapping Code Block PHP will result in ID assigned styles not applying to the Code Block on the front-end.","oxygen")
					),
					array(
						"type" 			=> "tag",
						"heading" 		=> __("Tag", "oxygen"),
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
						"condition" 	=> "unwrap=false"
					),
				)
			)
		);