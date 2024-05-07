<?php

/**
 * CT_Fancy_Icon Component Class
 * 
 * @since 0.2.1
 */

Class CT_Fancy_Icon extends CT_Component {

	public $options;
	public $icons_ids = array();

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// add specific options
		add_action("ct_toolbar_component_settings", array( $this, "icon_settings") );

		// output svg set on frontend
		add_action("wp_footer", array( $this, "output_svg_set") );

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_basics_components_visual", array( $this, "component_button" ) );
	}


	/**
	 * Add a [ct_fancy_icon] shortcode to WordPress
	 *
	 * @since 0.2.1
	 */

	function add_shortcode( $atts, $content, $name ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
            return '';
        }

		$options = $this->set_options( $atts );

		global $oxygen_svg_icons_to_load;
		$oxygen_svg_icons_to_load[] = $options['icon_id'];

		ob_start();

		$editable_attribute = esc_attr($options['icon_id']);

        if( class_exists( 'Oxygen_Gutenberg' ) ) $editable_attribute = Oxygen_Gutenberg::decorate_attribute( $options, $editable_attribute, 'icon' );

		?><div id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><svg id="svg-<?php echo esc_attr($options['selector']); ?>"><use xlink:href="#<?php echo $editable_attribute; ?>"></use></svg></div><?php
		
		return ob_get_clean();
	}


	/**
	 * Output settings
	 *
	 * @since 0.2.1 
	 */

	function icon_settings() { ?>

		<div class="oxygen-sidebar-flex-panel"
			ng-show="isActiveName('<?php echo $this->options['tag']; ?>')">
		
			<div class="oxygen-control-row">

				<div class="oxygen-control-wrapper">
					<label class="oxygen-control-label"><?php _e("Icon Set", "oxygen"); ?></label>
					<div class="oxygen-select oxygen-select-box-wrapper oxygen-special-property not-available-for-media not-available-for-classes">
						<div class="oxygen-select-box">
							<div class="oxygen-select-box-current">{{iframeScope.currentSVGSet}}</div>
							<div class="oxygen-select-box-dropdown"></div>
						</div>
						<div class="oxygen-select-box-options">
							<div class="oxygen-select-box-option" title="<?php _e("Use this set", "component-theme"); ?>"
								ng-repeat="(name,set) in iframeScope.SVGSets" 
								ng-click="iframeScope.setCurrentSVGSet(name);">
								{{name}}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="oxygen-control-row">
				<div class="oxygen-control-wrapper">
					<label class="oxygen-control-label"><?php _e("Icon", "oxygen"); ?></label>
					<div class="oxygen-control">
						<div class="oxygen-icon-chooser">

							<div class="oxygen-input">
								<input type="text" placeholder="<?php _e("Start typing to search...","oxygen"); ?>"
									ng-model="iframeScope.iconFilter.title"
									ng-model-options="{debounce: 500}">
							</div>

							<div class="oxygen-icon-grid">
								<div class="oxygen-icon-grid-icon"
									ng-repeat="icon in iframeScope.SVGSets[iframeScope.currentSVGSet].defs.symbol | filter:iframeScope.iconFilter.title track by icon.title"
									ng-click="iframeScope.setSVGIcon(icon['@attributes']['id'], icon['title'])"
									ng-class="{'oxygen-icon-grid-icon-active':iframeScope.getOption('icon-id')==iframeScope.currentSVGSet.split(' ').join('')+icon['@attributes']['id']}"
									title="{{icon.title}}">
									<svg><use xlink:href="" ng-href="{{'#'+iframeScope.currentSVGSet.split(' ').join('')+icon['@attributes']['id']}}"></use></svg>
								</div>
							</div>

						</div>
						<!-- .oxygen-icon-chooser -->
					</div>
				</div>
			</div>
		</div>

	<?php }


	/**
	 * Output SVG sets available in install 
	 * only with icons used on the page
	 *
	 * @since 0.2.0
	 */

	static function output_svg_set() {

		global $oxygen_svg_icons_to_load;

		if ( !is_array($oxygen_svg_icons_to_load) || empty($oxygen_svg_icons_to_load) )
			return;

		if ( defined("SHOW_CT_BUILDER") ) 
			return;

		//$svg_sets = get_option("ct_svg_sets", array() );
		$svg_sets = oxy_get_svg_sets();

		// loop all sets
		foreach ( $svg_sets as $set ) {

			$icons_to_remove = array();
			
			$svg = new SimpleXMLElement($set);

			if($svg->defs->symbol) {
				// loop all set icons

				foreach ( $svg->defs->symbol as $key => $symbol ) {

					$icon 		= (array)$symbol;
					$attributes = $icon["@attributes"];
					$icon_id 	= $attributes['id'];
					$view_box 	= explode(" ", $attributes['viewBox']);

					if ( in_array( $icon_id, $oxygen_svg_icons_to_load ) || is_admin() ) {

						if ( $view_box[2] != $view_box[3] ) {
							echo "<style>";
							echo ".ct-".sanitize_text_field($attributes['id'])."{";
							echo "width:" . ($view_box[2] / $view_box[3]) . "em";	
							echo "}";
							echo "</style>\r\n";
						}
					}
					else {
						// remove not used icons to keep HTML output clean
						$icons_to_remove[] = $symbol;
					}
				};

				foreach ($icons_to_remove as $icon) {
				    unset($icon[0]);
				}

				if ( sizeof($svg->defs->symbol) > 0 ) {
					
					// remove empty lines
					$output = str_replace("\r", "", $svg->asXML());
					$output = str_replace("\n", "", $output);

					echo $output;
				}
			}
		}
	}

}


// Create toolbar inctances
global $oxygen_vsb_components;
$oxygen_vsb_components['fancy_icon'] = new CT_Fancy_Icon ( 

		array( 
			'name' 		=> __("Icon", "oxygen"),
			'tag' 		=> 'ct_fancy_icon',
			'params' 	=> array(
					array(
						"type" 			=> "textfield",
						"param_name" 	=> "icon-id",
						"value" 		=> "FontAwesomeicon-thumbs-up",
						"hidden"		=> true,
						"css" 			=> false
					),
					array(
						"type" 			=> "radio",
						"heading" 		=> __("Icon Style", "oxygen"),
						"param_name" 	=> "icon-style",
						"value" 		=> array(
											1 	=> __("Outline", "oxygen"),
											2 	=> __("Solid", "oxygen"),
											3 	=> __("None", "oxygen"),
										),
						"default" 		=> 3,
						"line_breaks"	=> true,
						"css" 			=> false
					),
					array(
						"type" 			=> "colorpicker",
						"heading" 		=> __("Color"),
						"param_name" 	=> "icon-color",
						"value" 		=> "#333333",
						"css" 			=> false
					),
					array(
						"type" 			=> "colorpicker",
						"heading" 		=> __("Background Color","oxygen"),
						"param_name" 	=> "icon-background-color",
						"value" 		=> "#f4f4f4",
						"condition"		=> "icon-style=2",
						"css" 			=> false
					),
					array(
						"type" 			=> "slider-measurebox",
						"heading" 		=> __("Icon size","oxygen"),
						"param_name" 	=> "icon-size",
						"value" 		=> 55,
						"min"			=> "10",
						"max"			=> "100",
						"param_units" 	=> 'px',
						"css" 			=> false
					),
					array(
						"param_name" 	=> "icon-size-unit",
						"value" 		=> "px",
						"hidden"		=> true,
						"css" 			=> false
					),
					array(
						"type" 			=> "slider-measurebox",
						"heading" 		=> __("Space around icon","oxygen"),
						"param_name" 	=> "icon-padding",
						"value" 		=> 20,
						"min"			=> "10",
						"max"			=> "100",
						"condition"		=> "icon-style!=3",
						"param_units" 	=> 'px',
						"css" 			=> false
					),
					array(
						"param_name" 	=> "icon-padding-unit",
						"value" 		=> "px",
						"hidden"		=> true,
						"css" 			=> false
					),
				),
			/*'advanced' 	=> array(
					"positioning" => array(
						"values" 	=> array (
							'display' 	=> 'inline-block',
							)
					)
			)*/
		)
);

?>