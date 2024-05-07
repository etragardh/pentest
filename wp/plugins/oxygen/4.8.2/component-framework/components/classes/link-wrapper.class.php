<?php

/**
 * Link Component Class
 * 
 * @since 0.1.5
 */

Class CT_Link_Wrapper extends CT_Component {

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
		add_action("oxygen_basics_components_links", array( $this, "component_button" ) );
	}

	
	/**
	 * Add a toolbar button
	 *
	 * @since 0.1.5
	 */

	function component_button_old() { ?>
		
	  <div class="ct-add-component-button ct-action-button"
 		data-searchid="<?php echo strtolower( preg_replace('/\s+/', '_', sanitize_text_field( $this->options['name'] ) ) ) ?>"
		 ng-click="addComponents('<?php echo $this->options['tag']; ?>', 'ct_text_block')">
			<?php echo $this->options['name']; ?>
		</div>

	<?php }


	/**
	 * Add a [ct_link] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content, $name ) {
		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		ob_start();

		$url = oxygen_vsb_base64_decode($options['url']);
		if (isset($options['url_encoded']) && $options['url_encoded'] === "true") {
			$url = oxygen_vsb_quotes_decode($options['url']);
		}
		$url = do_shortcode($url);
		$editable_link = esc_url($url);

		$target = ( esc_attr($options['target']) != "" ) ? "target=\"".esc_attr($options['target'])."\"" : "";
		$rel 	= ( esc_attr($options['rel']) != "" ) 	 ? "rel=\"".esc_attr($options['rel'])."\"" : "";
		
		if( class_exists( 'Oxygen_Gutenberg' ) ) $editable_link = Oxygen_Gutenberg::decorate_attribute( $options, $editable_link, 'link' );

		?><a id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" href="<?php echo $editable_link; ?>" <?php echo "$target $rel"; ?> <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php echo (is_array($content)) ? do_oxygen_elements( $content ) : do_shortcode( $content ); ?></a><?php

		return ob_get_clean();
	}

}


// Create toolbar inctances
global $oxygen_vsb_components;
$oxygen_vsb_components['link_wrapper'] = new CT_Link_Wrapper ( 

		array( 
			'name' 		=> 'Link Wrapper',
			'tag' 		=> 'ct_link',
			'params' 	=> array(
					array(
						"type" 			=> "hyperlink",
						"heading" 		=> __("URL","oxygen"),
						"param_name" 	=> "url",
						"value" 		=> "http://",
						"css" 			=> false,
						"dynamicdatacode"	=>	'<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesLinkMode" callback="iframeScope.insertShortcodeToUrl">data</div>'

					),
					array(
						"type" 			=> "dropdown",
						"heading" 		=> __("Target","oxygen"),
						"param_name" 	=> "target",
						"value" 		=> array (
											"" => "&nbsp;",
											"_self" => "_self",
											"_blank" => "_blank",
											"_parent" => "_parent",
											"_top" => "_top",
										),
						"default" 		=> "_self",
						"css" 			=> false,
					),
					array(
						"type" 			=> "html_attribute",
						"heading" 		=> __("Rel","oxygen"),
						"param_name" 	=> "rel",
						"value" 		=> "",
						"css" 			=> false,
					),
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
						"value" 		=> "nowrap",
						"true_value" 	=> "wrap",
						"false_value" 	=> "nowrap",
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
								'flex-direction' => 'column',
								'align-items' 	 => 'center',
								'justify-content'=> 'center',
								'text-align' 	 => 'center'
							)
					),
                	'allowed_html'      => 'post',
                    'allow_shortcodes'  => true,
			)
		)
);

?>
