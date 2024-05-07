<?php 

Class CT_Inner_Content extends CT_Component {

	var $shortcode_options;
	var $shortcode_atts;

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		add_filter( 'template_include', array( $this, 'ct_innercontent_template'), 100 );

		for ( $i = 2; $i <= 16; $i++ ) {
			add_shortcode( $this->options['tag'] . "_" . $i, array( $this, 'add_shortcode' ) );
		}
	}

	/**
	 * Add a [ct_inner_content] shortcode to WordPress
	 *
	 * @since 1.2.0
	 */

	function add_shortcode( $atts, $content, $name ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );
		$html = "";
		$shortcodes = "";
		$post_id = get_the_ID();

		ob_start();
		
		global $oxygen_vsb_css_caching_active;
		echo "<".esc_attr($options['tag'])." id='" . esc_attr( $options['selector'] ) . "' class='" . esc_attr( $options['classes'] ) . "'>";

		if(isset($_REQUEST['oxy_preview_revision']) && is_numeric($_REQUEST['oxy_preview_revision'])) {
			$shortcodes = Oxygen_Revisions::get_post_meta_db( $post_id, null, true, null, OBJECT, $_REQUEST['oxy_preview_revision'] )->meta_value;
			$json = oxygen_safe_convert_old_shortcodes_to_json($shortcodes);
			$components_tree = json_decode($json, true);
			if ($components_tree) {
				$html = do_oxygen_elements($components_tree['children']);
			}
		}
		else if (isset($oxygen_vsb_css_caching_active) && $oxygen_vsb_css_caching_active===true) {
			$shortcodes = "no shortcodes";
		}
		else {
			$json = get_post_meta( $post_id, 'ct_builder_json', true );
			$components_tree = json_decode($json, true);

			if ($components_tree) {
				$html = do_oxygen_elements($components_tree['children']);
			}
			else {
				$shortcodes = get_post_meta( $post_id, 'ct_builder_shortcodes', true );
			}
			
			if( class_exists('Oxygen_Gutenberg') && get_post_meta( $post_id, 'ct_oxygenberg_full_page_block', true ) == '1' ) {
				$post = get_post($post_id);
				$blocks = do_blocks( $post->post_content );
			}
		}

		if(empty(trim($shortcodes))&&empty(trim($html))) {

			// find the template that has been assigned to innercontent
			$template = ct_get_inner_content_template();

			if($template) {
				$json = get_post_meta($template->ID, 'ct_builder_json', true);
				$json = json_decode($json, true);
				if (isset($json['children']) && is_array($json['children']) && count($json['children'])>0) {
					echo do_oxygen_elements($json['children']);
				}
				else{
					$shortcodes = get_post_meta($template->ID, 'ct_builder_shortcodes', true);
					$json = "";
				}
			}

			if ($shortcodes) {
                echo ct_do_shortcode($shortcodes);
			}
			else if (!(isset($json['children']) && is_array($json['children']) && count($json['children'])>0)) {
				// RENDER default content
				if(function_exists('is_woocommerce') && is_woocommerce()) {
					woocommerce_content();	
				}
				else {
				    // Use WordPress post content as inner content
					while ( have_posts() ) {
						the_post();
						the_content();
					}
		        }
		    }

        } else {
			if ( isset($blocks) ) {
				$content = $blocks;
			}
			else if ($html) {
				$content .= $html;
			}
			else if (is_array($content)) {
				$content += $components_tree['children'];
			}
			else {
				$content .= $shortcodes;
			}
        }

        if ( ! empty( $content ) ) {
			echo (is_array($content)) ? do_oxygen_elements( $content ) : ct_do_shortcode( $content );
        }

        echo "</".esc_attr($options['tag']).">";

		return ob_get_clean();
	}
	
	function ct_innercontent_template( $template ) {
		$new_template = '';

		if(isset($_REQUEST['action']) && stripslashes($_REQUEST['action']) == 'ct_render_innercontent') {
			$nonce  	= $_REQUEST['nonce'];
			$post_id 	= $_REQUEST['post_id'];
			
			// check nonce
			if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
			    // This nonce is not valid.
			    die( 'Security check' );
			}
			
			if ( file_exists(dirname(dirname( __FILE__)) . '/layouts/' . 'innercontent.php') ) {
				$new_template = dirname(dirname( __FILE__)) . '/layouts/' . 'innercontent.php';
			}
		}

		if ( '' != $new_template ) {
				return $new_template ;
			}

		return $template;
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

		$post_type = get_post_type();
		
		if ( $post_type != "ct_template") {
			return;
		} ?>

		<div class="oxygen-add-section-element"
			data-searchid="<?php echo strtolower( preg_replace('/\s+/', '_', sanitize_text_field( $this->options['name'] ) ) ) ?>"
			ng-click="iframeScope.addComponent('<?php echo esc_attr( $this->options['tag'] ); ?>')">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/inner-content.svg' />
			<?php echo esc_html($this->options['name']); ?>
		</div>

	<?php }
}




// Create instance
global $oxygen_vsb_components;
$oxygen_vsb_components['inner_content'] = new CT_Inner_Content( array( 
			'name' 		=> 'Inner Content',
			'tag' 		=> 'ct_inner_content',
			'params' 	=> array(
					array(
						"type" 			=> "tag",
						"heading" 		=> __("Tag", "oxygen"),
						"param_name" 	=> "tag",
						"value" 		=> array (
											"div" 		=> "div",
											"section" 	=> "section",
											"article" 	=> "article",
											"main" 		=> "main",
										),
						"css" 			=> false,
						"rebuild" 		=> true,
					),
				),		
			'advanced' 	=> array(
					"positioning" => array(
						"values" 	=> array (
							'width' 	 => '100',
							'width-unit' => '%',
							)
					),
			        'allow_shortcodes' => true,
                )
			)
		);
