<?php 

Class CT_Selector extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// remove component button
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );

		add_action("ct_toolbar_component_header", array( $this, "selector_settings" ), 9 );		
	}

	
	/**
	 * Selector options
	 *
	 * @since 1.5
	 * @author Ilya K.
	 */

	function selector_settings() { ?>

		<div ng-if="iframeScope.selectedNodeType==='selector'">

			<div class="oxygen-control-row" 
				ng-if="iframeScope.isEditing('pseudo-element')">

				<div class='oxygen-control-wrapper'>
					<div class='oxygen-control'>
						<div class='oxygen-input'>
							<input type="text" spellcheck="false" placeholder="<?php _e("content...", "component-theme"); ?>"
								ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['content']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id,iframeScope.component.active.name,'content')"/>
						</div>
					</div>
				</div>
			</div>
			<!-- .oxygen-control-row -->
			
			<div class="oxygen-control-row">

				<div class='oxygen-control-wrapper'>
					<label class='oxygen-control-label'><?php _e( "Friendly Name", "component-theme" ); ?></label>
					<div class='oxygen-control'>
						<div class='oxygen-input'>
							<input type="text" spellcheck="false"
								ng-model="iframeScope.customSelectors[iframeScope.selectorToEdit]['friendly_name']"
								ng-change="iframeScope.checkNewCustomSelector(iframeScope.selectorToEdit)"/>
						</div>
					</div>
				</div>
			</div>
			<!-- .oxygen-control-row -->

			<div class="oxygen-control-row">

				<div class="oxygen-control-wrapper">
					<label class='oxygen-control-label'><?php _e( "Style Set", "oxygen" ); ?></label>
					<div class="oxygen-select oxygen-select-box-wrapper oxygen-style-set-dropdown">
						<div class="oxygen-select-box">
							<div class="oxygen-select-box-current">{{iframeScope.customSelectors[iframeScope.selectorToEdit]['set_name']}}</div>
							<div class="oxygen-select-box-dropdown"></div>
						</div>
						<div class="oxygen-select-box-options">
							<div class="oxygen-select-box-option">
								<input type="text" class="ct-new-component-class-input" placeholder="<?php _e("Enter style set name...","oxygen");?>" 
									ng-model="$parent.newStyleSetName"
									ng-keypress="$parent.processStyleSetNameInput($event)">
								&nbsp;
								<span class="oxygen-add-style-set"
									ng-click="$parent.addNewStyleSet()">
									<?php _e("add set...", "oxygen"); ?>
								</span>
							</div>
							<div class="oxygen-select-box-option" title="<?php _e("Use this style set", "oxygen"); ?>"
								ng-repeat="(key,set) in iframeScope.styleSets"
								ng-click="$parent.setSelectorStyleSet(key);">
								{{key}}
								<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/remove_icon.svg'
									title="<?php _e("Remove style set from Oxygen", "oxygen"); ?>"
									ng-click="$parent.deleteStyleSet(key)"/>
							</div>
						</div>
					</div>
				</div>

			</div>
			<!-- .oxygen-control-row -->
		</div>
	<?php }
}


// Create inctance
global $oxygen_vsb_components;
$oxygen_vsb_components['selector'] = new CT_Selector( array( 
			'name' 		=> 'Selector',
			'tag' 		=> 'ct_selector'
			)
		);