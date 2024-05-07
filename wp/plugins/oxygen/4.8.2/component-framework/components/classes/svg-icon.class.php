<?php

/**
 * CT_SVG_Icon Component Class
 * 
 * @since 0.2.1
 */

Class CT_SVG_Icon extends CT_Component {

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

		// output svg sets
		add_action("wp_footer", array( $this, "output_all_svg_sets") );
		add_action("wp_footer", array( $this, "output_svg_set") );

		// remove component button
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
	}


	/**
	 * Add a [ct_svg_icon] shortcode to WordPress
	 *
	 * @since 0.2.1
	 */

	function add_shortcode( $atts, $content, $name ) {
		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		$this->icons_ids[] = $options['icon_id'];

		ob_start(); 

		?><svg id="<?php echo esc_attr($options['selector']); ?>" class="ct-<?php echo esc_attr($options['icon_id']); ?> <?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><use xlink:href="#<?php echo esc_attr($options['icon_id']); ?>"></use></svg><?php
		
		return ob_get_clean();
	}


	/**
	 * Output settings
	 *
	 * @since 0.2.1 
	 */

	function icon_settings() { ?>

		<div class="oxygen-control-row"
			ng-show="isActiveName('<?php echo $this->options['tag']; ?>')">
			<div class="oxygen-control-wrapper">
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

		<div class="oxygen-control-row"
			ng-show="isActiveName('<?php echo $this->options['tag']; ?>')">
			<div class="oxygen-control-wrapper">
				<label class="oxygen-control-label"><?php _e("Icon", "oxygen"); ?></label>
				<div class="oxygen-control">
					<div class="oxygen-icon-chooser">

						<div class="oxygen-input">
							<input type="text" placeholder="<?php _e("Start typing to search...","oxygen"); ?>"
								ng-model="iframeScope.iconFilter.title">
						</div>

						<div class="oxygen-icon-grid">
							<div class="oxygen-icon-grid-icon"
								ng-repeat="icon in iframeScope.SVGSets[iframeScope.currentSVGSet].defs.symbol | filter:iframeScope.iconFilter"
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

	<?php }


	/**
	 * Output SVG sets available in install 
	 * only with icons used on the page
	 *
	 * @since 0.2.0
	 */

	function output_svg_set() {

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

					if ( in_array( $icon_id, $this->icons_ids ) ) {

						if ( $view_box[2] != $view_box[3] ) {
							echo "<style>";
							echo ".ct-".esc_attr($attributes['id'])."{";
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

				if ( sizeof($svg->defs->symbol) == 0 ) {
					return;
				}

				// remove empty lines
				$output = str_replace("\r", "", $svg->asXML());
				$output = str_replace("\n", "", $output);

				echo $output;
			}
		}
	}


	/**
	 * Output all SVG sets available in install only for builder
	 *
	 * @since 0.2.0
	 */

	function output_all_svg_sets() {

		if ( !defined("OXYGEN_IFRAME") && !defined("SHOW_CT_BUILDER") ) 
			return;

		//$svg_sets = get_option("ct_svg_sets", array() );
		$svg_sets = oxy_get_svg_sets();

		foreach ( $svg_sets as $set ) {
			
			$svg = new SimpleXMLElement($set);

			// output only if it has valid defs and symbols
			if( isset($svg->defs) && isset($svg->defs->symbol)) {
				echo $set."\n";
			
				// output specific icon widths for some icons based on viewBox parameter
				echo "<style class='ct_svg_sets'>";

				foreach ( $svg->defs->symbol as $icon ) {

					$icon 		= (array)$icon;
					$attributes = $icon["@attributes"];
					$view_box 	= explode(" ", $attributes['viewBox']);

					if ( $view_box[2] != $view_box[3] ) {
						echo ".ct-".esc_attr($attributes['id'])."{";
						echo "width:" . ($view_box[2] / $view_box[3]) . "em";	
						echo "}";
					}
				}
				echo "</style>";
			}
		}
		
	}

}


// Create toolbar inctances
global $oxygen_vsb_components;
$oxygen_vsb_components['svg_icon'] = new CT_SVG_Icon ( 

		array( 
			'name' 		=> 'Plain Icon',
			'tag' 		=> 'ct_svg_icon',
			'params' 	=> array(
					array(
						"type" 			=> "colorpicker",
						"heading" 		=> __("Color","oxygen"),
						"param_name" 	=> "color",
						"value" 		=> "",
					),
					array(
						"type" 			=> "slider-measurebox",
						"heading" 		=> __("Icon Size", "oxygen"),
						"param_name" 	=> "font-size",
						"value" 		=> 32
					),
					array(
						"param_name" 	=> "font-size-unit",
						"value" 		=> "px",
						"hidden"		=> true,
					),
					array(
						"type" 			=> "textfield",
						"param_name" 	=> "icon-id",
						"value" 		=> "FontAwesomeicon-thumbs-up",
						"hidden"		=> true,
						"css" 			=> false,
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