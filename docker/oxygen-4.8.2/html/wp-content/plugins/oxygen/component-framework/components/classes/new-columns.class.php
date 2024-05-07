<?php

/**
 * Div Block Component Class
 * 
 * @since 0.1.3
 */

Class CT_New_Columns extends CT_Component {

	var $options;

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
		add_action("ct_toolbar_component_settings", array( $this, "column_settings"), 9 );

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_basics_components_containers", array( $this, "component_button" ) );
	}


	/**
	 * Add a [ct_new_columns] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content, $name ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
            return '';
        }

		$options = $this->set_options( $atts );

		ob_start();

		?><div id="<?php echo esc_attr($options['selector']); ?>" class="<?php if(isset($options['classes'])) echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php echo (is_array($content)) ? do_oxygen_elements( $content ) : do_shortcode( $content ); ?></div><?php

		return ob_get_clean();
	}


	/**
	 * Output settings
	 *
	 * @since 2.0
	 * @author Ilya K. 
	 */

	function column_settings() { ?>

		<div ng-show="isActiveName('<?php echo $this->options['tag']; ?>')&&!iframeScope.isEmptyComponent()">
			<div class="oxygen-control-row">
				<div class='oxygen-control-wrapper'>
					<label class='oxygen-control-label'><?php _e("Stack Columns Vertically","oxygen"); ?></label>
					<div class='oxygen-control oxygen-special-property not-available-for-media not-available-for-classes'>

						<div class="oxygen-select oxygen-select-box-wrapper">
							<div class="oxygen-select-box"
								ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'stack-columns-vertically')}">
								<div class="oxygen-select-box-current">{{iframeScope.getMediaTitle(iframeScope.getOption('stack-columns-vertically'))}}</div>
								<div class="oxygen-select-box-dropdown"></div>
							</div>
							<div class="oxygen-select-box-options">
								<div class="oxygen-select-box-option" 
									ng-repeat="name in iframeScope.sortedMediaList()"
									ng-if="name!='default'"
									ng-click="iframeScope.setOptionModel('stack-columns-vertically',name)"
									ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('stack-columns-vertically')==name}">
									{{iframeScope.getMediaTitle(name)}}
								</div>
								<div class="oxygen-select-box-option" 
									ng-click="iframeScope.setOptionModel('stack-columns-vertically','never')"
									ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('stack-columns-vertically')=='never'}">
									<?php _e("Never","oxygen"); ?>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>

			<div class="oxygen-control-row">
				<div class='oxygen-control-wrapper'>
					<label class='oxygen-control-label'><?php _e("Reverse Column Order","oxygen"); ?></label>
					<div class='oxygen-control oxygen-special-property not-available-for-media not-available-for-classes'>

						<div class="oxygen-select oxygen-select-box-wrapper">
							<div class="oxygen-select-box"
								ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'reverse-column-order')}">
								<div class="oxygen-select-box-current">{{iframeScope.getMediaTitle(iframeScope.getOption('reverse-column-order'))}}</div>
								<div class="oxygen-select-box-dropdown"></div>
							</div>
							<div class="oxygen-select-box-options">
								<div class="oxygen-select-box-option" 
									ng-click="iframeScope.setOptionModel('reverse-column-order','always')"
									ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('reverse-column-order')=='always'}">
									<?php _e("Always","oxygen"); ?>
								</div>
								<div class="oxygen-select-box-option" 
									ng-repeat="name in iframeScope.sortedMediaList()"
									ng-if="name!='default'"
									ng-click="iframeScope.setOptionModel('reverse-column-order',name)"
									ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('reverse-column-order')==name}">
									{{iframeScope.getMediaTitle(name)}}
								</div>
								<div class="oxygen-select-box-option" 
									ng-click="iframeScope.setOptionModel('reverse-column-order','never')"
									ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('reverse-column-order')=='never'}">
									<?php _e("Never","oxygen"); ?>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>

			<div class="oxygen-control-row">
				<div class='oxygen-control-wrapper'>
					<label class='oxygen-control-label'><?php _e("Set Column Width to 50%","oxygen"); ?></label>
					<div class='oxygen-control oxygen-special-property not-available-for-media not-available-for-classes'>

						<div class="oxygen-select oxygen-select-box-wrapper">
							<div class="oxygen-select-box"
								ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'set-columns-width-50')}">
								<div class="oxygen-select-box-current">{{iframeScope.getMediaTitle(iframeScope.getOption('set-columns-width-50'))}}</div>
								<div class="oxygen-select-box-dropdown"></div>
							</div>
							<div class="oxygen-select-box-options">
								<div class="oxygen-select-box-option" 
									ng-repeat="name in iframeScope.sortedMediaList()"
									ng-if="name!='default'"
									ng-click="iframeScope.setOptionModel('set-columns-width-50',name)"
									ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('set-columns-width-50')==name}">
									{{iframeScope.getMediaTitle(name)}}
								</div>
								<div class="oxygen-select-box-option" 
									ng-click="iframeScope.setOptionModel('set-columns-width-50','never')"
									ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('set-columns-width-50')=='never'}">
									<?php _e("Never","oxygen"); ?>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

		<?php include_once( CT_FW_PATH . "/toolbar/views/column-layout.view.php" ); ?>

	<?php }
}


// Create toolbar inctances
global $oxygen_vsb_components;
$oxygen_vsb_components['new_columns'] = new CT_New_Columns ( 

		array( 
			'name' 		=> 'Columns',
			'tag' 		=> 'ct_new_columns',
			'params' 	=> array(
					array(
						"type" 			=> "colorpicker",
						"heading" 		=> __("Background color", "oxygen"),
						"param_name" 	=> "background-color",
						"ng_show" 		=> "!iframeScope.isEmptyComponent()"
					),
					// we might add this in future
					/*array(
						"type" 			=> "slider-measurebox",
						"heading" 		=> __("Column Spacing", "oxygen"),
						"param_name" 	=> "gutter",
						"value" 		=> "0",
						"param_units" 	=> "px",
						"ng_show" 		=> "!iframeScope.isEmptyComponent()"
					),*/
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
								'flex-direction' 		=> 'row',
								'flex-wrap' 	 		=> 'wrap',
								'align-items' 			=> 'stretch',
								'reverse-column-order' 	=> 'never',
								'set-columns-width-50' 	=> 'never',
								'stack-columns-vertically' => 'tablet'
							)
					),
					'positioning' => array(
						'values' 	=> array (
								'display' 	 => 'flex',
								'width' 	 => '100',
								'width-unit' => '%'
 							)
					)
			),
			'not_css_params' => array(
				'stack-columns-vertically',
				'reverse-column-order',
				'set-columns-width-50'
			)
		)
);

?>