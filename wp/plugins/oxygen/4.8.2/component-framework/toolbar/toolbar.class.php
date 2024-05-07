<?php

/**
 * Toolbar Class
 *
 * @since 0.1
 */

Class CT_Toolbar {

	function __construct() {

		add_action("wp", array( $this, "toolbar_init" ) );
	}

	function toolbar_init() {

		// TODO: check if user can edit this exact post?
		if ( oxygen_vsb_current_user_can_access() && defined("SHOW_CT_BUILDER") ) {
			add_action("ct_before_builder", array( $this, "toolbar_view") );
		}

		global $oxygen_add_plus;

		$this->folders = $oxygen_add_plus;

		$this->options['advanced'] = array(
											"background" => array (
												"heading" 	=> __("Background", "oxygen"),
												"tab_icon" 	=> "background",
											),

											"position" => array (
												"heading" 	=> __("Size & Spacing", "oxygen"),
												"tab_icon" 	=> "size_spacing",
											),

											"layout" => array (
												"heading" 	=> __("Layout", "oxygen"),
												"tab_icon" 	=> "layout",
											),

											"typography" => array (
												"heading" 	=> __("Typography", "oxygen"),
												"tab_icon" 	=> "typography",
											),

											"borders" => array (
												"heading" 	=> __("Borders", "oxygen"),
												"tab_icon" 	=> "borders",
											),

											"effects" => array (
												"heading" 	=> __("Effects", "oxygen"),
												"tab_icon" 	=> "effects",
											),

											"code-php" => array (
												"heading" 	=> __("PHP", "oxygen"),
												"tab_icon" 	=> "borders",
											),

											"code-css" => array (
												"heading" 	=> __("CSS", "oxygen"),
												"tab_icon" 	=> "css",
											),

											"code-js" => array (
												"heading" 	=> __("JavaScript", "oxygen"),
												"tab_icon" 	=> "js",
											),

											"code-mixed" => array (
												"heading" 	=> __("Mixed Code", "oxygen"),
												"tab_icon" 	=> "js",
											),

											"custom-css" => array (
												"heading" 	=> __("Custom CSS", "oxygen"),
												"tab_icon" 	=> "css",
											),

											"custom-js" => array (
												"heading" 	=> __("JavaScript", "oxygen"),
												"tab_icon" 	=> "js",
											),

											"custom-attributes" => array (
												"heading" 	=> __("Attributes", "oxygen"),
												"tab_icon" 	=> "layout",
											),
										);

		//$this->options['advanced'] = apply_filters("ct_component_advanced_options", $this->options['advanced']);
		
		// get list of all components that has Basic Styles tabs
		$this->component_with_tabs = apply_filters("oxygen_component_with_tabs", array());

		// include styles
		add_action("wp_enqueue_scripts", array( $this, "enqueue_scripts" ) );

		// output main toolbar elements
		add_action("ct_toolbar_component_header",			array( $this, "component_header") );
		add_action("ct_toolbar_advanced_settings", 			array( $this, "advanced_settings") );

		add_action("ct_toolbar_components_list",			array( $this, "components_list") );
		add_action("ct_toolbar_components_list_searchable",	array( $this, "components_list_searchable") );
		add_action("ct_toolbar_components_anchors", 		array( $this, "components_anchors") );

		add_action("ct_toolbar_reusable_parts", 			array( $this, "ct_reusable_parts") );

		add_action("ct_toolbar_page_settings", 				array( $this, "ct_show_page_settings" ) );
		add_action("ct_toolbar_global_fonts_settings", 		array( $this, "ct_show_global_fonts_settings") );

		add_action("oxygen_toolbar_settings_headings", 		array( $this, "settings_headings") );
		add_action("oxygen_toolbar_settings_colors", 		array( $this, "settings_colors") );
		add_action("oxygen_toolbar_settings_body_text", 	array( $this, "settings_body_text") );
		add_action("oxygen_toolbar_settings_links", 		array( $this, "settings_links") );
		add_action("oxygen_before_toolbar_close", 			array( $this, "tiny_mce") );

		add_action("ct_toolbar_data_folder", 				array( $this, "data_folder"), 9 );

        add_action("ct_toolbar_component_settings", 		array( $this, "element_presets_controls"), 90 );
	}


	/**
	 * Enqueue scripts and styles
	 *
	 * @since 0.1.4
	 */

	function enqueue_scripts() {
		wp_enqueue_style ("oxygen-vars",	CT_FW_URI . "/oxygen.variables.css", array(), CT_VERSION);
		wp_enqueue_style ("ct-ui", 			CT_FW_URI . "/toolbar/UI/css/default.css", array(), CT_VERSION);
		wp_enqueue_style ("flex-ui", 		CT_FW_URI . "/toolbar/UI/css/flex-ui.css", array(), CT_VERSION);
		wp_enqueue_style ("ct-dom-tree", 	CT_FW_URI . "/toolbar/UI/css/domtree.css", array(), CT_VERSION);
	}


	/**
	 * Include toolbar view file
	 *
	 * @since 0.1.4
	 */

	function toolbar_view() {
		require_once("toolbar.view.php");
	}


	/**
	 * Echo ng attributes needed for component settings
	 *
	 * @since 0.1.7
	 */

	function ng_attributes( $param_name, $attributes = "class,model,change", $callbacks = array()) {

		$param_name = sanitize_text_field($param_name);

		$attributes = explode(',', $attributes );

		if ( in_array('class-fake', $attributes) ) { ?>
			ng-class="iframeScope.checkOptionChanged(iframeScope.component.active.id,'<?php echo $param_name; ?>')"
		<?php }

		if ( in_array('model', $attributes) ) { 
			// handle deep properties passed as "parent.child.grandchild"
			if (strrpos($param_name, ".")!==false) {
				$params = explode(".", $param_name);
				$parsed_param_name = implode("']['", $params);
				// remove + and ' characters that might be present
				$parsed_param_name = str_replace("''+", "", $parsed_param_name);
				$parsed_param_name = str_replace("+''", "", $parsed_param_name);
			}
			else {
				$parsed_param_name = $param_name;
			} ?>
			ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['<?php echo $parsed_param_name; ?>']"
			ng-model-options="{ debounce: 10 }"
		<?php }

		if ( in_array('change', $attributes) ) { 
			if (strrpos($param_name, ".")!==false) {
				$params = explode(".", $param_name);
				$parsed_param_name = $params[0];
			}
			else {
				$parsed_param_name = $param_name;
			} ?>
			ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,'<?php echo $parsed_param_name; ?>'<?php echo isset($shortcode_arg)?$shortcode_arg:''; ?>);iframeScope.checkResizeBoxOptions('<?php echo $parsed_param_name; ?>'); <?php echo isset($callbacks['change'])?$callbacks['change']:''; ?>"
		<?php }

		if ( in_array('keypress', $attributes) ) { ?>
			ng-keypress="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,'<?php echo $param_name; ?>');iframeScope.checkResizeBoxOptions('<?php echo $param_name; ?>')"
		<?php }

	}


	/**
	 * Echo ng attributes needed for component settings
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	function global_ng_attributes( $context, $param, $attributes = "model,change") {

		$param = sanitize_text_field($param);
		$attributes = explode(',', $attributes );

		if ( $context == 'page' ) {

			if ( in_array('model', $attributes) ) { ?>
				ng-model="iframeScope.pageSettingsMeta<?php echo $param; ?>"
				ng-model-options="{ debounce: 10 }"
			<?php }
		}

		if ( $context == 'global' ) {

			if ( in_array('model', $attributes) ) { ?>
				ng-model="iframeScope.globalSettings<?php echo $param; ?>"
				ng-model-options="{ debounce: 10 }"
			<?php }
		}

		if ( in_array('change', $attributes) ) { ?>
			ng-change="iframeScope.pageSettingsUpdate();"
		<?php }

		if ( in_array('keypress', $attributes) ) { ?>
			ng-keypress="iframeScope.pageSettingsUpdate();"
		<?php }	
	}


	/**
	 * Selector box
	 *
	 * @since 0.1.4
	 */

	function component_header() { ?>

		<div class='oxygen-active-element'
			ng-if="!iframeScope.isEditing('custom-selector')">

			<div class='oxygen-active-element-name'
				<?php if (oxygen_vsb_current_user_can_full_access()) { ?>
				ng-dblclick="iframeScope.setEditableFriendlyName(iframeScope.component.active.id, $event)"
				<?php } ?>
				ng-hide="iframeScope.editableFriendlyNamePropertiesPane==iframeScope.component.active.id">
				<div ng-bind="iframeScope.component.options[iframeScope.component.active.id]['nicename']"></div>
			</div>

			<div class='oxygen-active-element-name oxygen-active-element-name-editable'
				contenteditable="true"
				ng-model="iframeScope.component.options[iframeScope.component.active.id]['nicename']"
				ng-if="iframeScope.editableFriendlyNamePropertiesPane==iframeScope.component.active.id"
				ng-blur="iframeScope.setEditableFriendlyName(0, $event)"
				data-plaintext="true"
				data-previoustext="iframeScope.component.options[iframeScope.component.active.id]['nicename']"
				focus-me="true">
			</div>

			<div class='oxygen-active-element-icons'
				ng-show="iframeScope.component.active.id < 100000 && !iframeScope.isEditing('style-sheet') && !iframeScope.isEditing('custom-selector') && !iframeScope.isBuiltinComponent()">

				<?php if (oxygen_vsb_current_user_can_full_access()) : ?>
				<div class="oxy-condition-menu-container">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/condition.svg'
						title="<?php _e("Condition Settings", "oxygen"); ?>"
						data-linkProperty="url" data-linkTarget="target"
						class="oxygen-link-button"
						ng-class="{'ct-link-button-highlight' : iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'] && iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'].length > 0}"
						ng-click="showConditionsMenu = !showConditionsMenu"
						/>		
					<div class="oxy-condition-menu" ng-show="showConditionsMenu">
						<div class="oxy-condition-menu-backdrop" ng-click="showConditionsMenu=false"></div>
						<div>
							<h1 class="oxy-condition-menu-title"><?php _e("Show/Hide Element", "oxygen"); ?></h1>
							<a 
							ng-click="showConditionsMenu=false; showDialogWindow(); dialogForms['ifCondition'] = true; "
							class="oxy-condition-menu-button"><?php _e("Set Conditions", "oxygen"); ?></a>

							<div class="oxy-condition-menu-divider"></div>

							<div class='oxygen-control-row'>
								<div class='oxygen-control-wrapper' id='oxygen-control-layout-display'>
									<label class='oxygen-control-label'><?php _e("Condition Type", "oxygen"); ?></label>
									<div class='oxygen-control'>
										<div class='oxygen-button-list'>

											<?php $this->button_list_button('conditionstype',null, 'AND', '', 'evalGlobalConditions(); evalGlobalConditionsInList()'); ?>
											<?php $this->button_list_button('conditionstype',1, 'OR', '', 'evalGlobalConditions(); evalGlobalConditionsInList()'); ?>

										</div>
									</div>
								</div>
							</div>
							<div class="oxy-condition-menu-divider"></div>
							<h1 class="oxy-condition-menu-behavior"><?php _e("In-Editor Behavior", "oxygen"); ?></h1>
							<div class="oxy-condition-menu-radios">
								<label>
								<input ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['conditionspreview']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name, 'conditionspreview'); evalGlobalConditions(); evalGlobalConditionsInList()" type="radio" value="2"> <?php _e("Always Show", "oxygen"); ?></label><br>
								<label><input ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['conditionspreview']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name, 'conditionspreview'); evalGlobalConditions(); evalGlobalConditionsInList()"  type="radio" value="0"> <?php _e("Always Hide", "oxygen"); ?></label><br>
								<label><input ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['conditionspreview']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name, 'conditionspreview'); evalGlobalConditions(); evalGlobalConditionsInList()"  type="radio" value="1"> <?php _e("Show/Hide Based on Conditions", "oxygen"); ?></label>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>

				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/link.svg'
					title="<?php _e("Link Settings", "oxygen"); ?>"
					data-linkProperty="url" data-linkTarget="target"
					class="oxygen-link-button"
					ng-class="{'ct-link-button-highlight' : iframeScope.getLinkId()}"
					ng-show="!isActiveName('ct_selector') && !isActiveName('ct_widget') && !isActiveName('ct_shortcode') && !isActiveName('ct_code_block') && iframeScope.component.active.parent.name !== 'oxy_dynamic_list'"
					ng-click="processLink()"/>

				<?php if (oxygen_vsb_current_user_can_full_access()||oxygen_vsb_user_has_enabled_elements()) : ?>
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/duplicate.svg'
					title="<?php _e("Duplicate Component", "oxygen"); ?>"
					<?php if (!oxygen_vsb_current_user_can_full_access()&&oxygen_vsb_user_has_enabled_elements()) : ?>
					ng-if="iframeScope.isElementEnabledForUser()"
					<?php endif; ?>
					ng-show="iframeScope.component.active.id > 0 && iframeScope.component.active.name != 'ct_span'"
					ng-click="iframeScope.duplicateComponent()"/>

				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg'
					title="<?php _e("Remove Component", "oxygen"); ?>"
					<?php if (!oxygen_vsb_current_user_can_full_access()&&oxygen_vsb_user_has_enabled_elements()) : ?>
					ng-if="iframeScope.isElementEnabledForUser()"
					<?php endif; ?>
					ng-show="iframeScope.component.active.id > 0 && !isActiveName('oxy_header_left') && !isActiveName('oxy_header_center') && !isActiveName('oxy_header_right')"
					ng-click="iframeScope.removeActiveComponent()"/>
				<?php endif; ?>

			</div>
		</div>
		<!-- .oxygen-active-element -->

		<div class='oxygen-active-element-breadcrumb'
			ng-if="!iframeScope.isEditing('custom-selector')">
			<span ng-repeat='item in iframeScope.selectAncestors'>
				<span ng-if="item.id > 0 && item.id < 100000" ng-click="iframeScope.activateComponent(item.id, item.tag)">{{item.name}}</span>
				<span ng-if="item.id > 0 && item.id < 100000" class="oxygen-active-element-breadcrumb-arrow">&gt;</span>
				<span ng-if="item.id == 0" class='oxygen-active-element-breadcrumb-active'>{{item.name}}</span>
			</span>
		</div>
		<!-- .oxygen-active-element-breadcrumb -->

		<div class='oxygen-media-query-and-selector-wrapper'>
			
			<!-- .oxygen-media-query-box -->

			<div class='oxygen-select oxygen-active-selector-box-wrapper'>
				<?php if (oxygen_vsb_user_can_use_classes() || oxygen_vsb_current_user_can_full_access()) : ?>
				<div class='oxygen-active-selector-box'
					ng-if="iframeScope.isNotSelectedYet(iframeScope.component.active.id)&&!iframeScope.isEditing('custom-selector')"
					ng-click="iframeScope.onSelectorDropdown()">
						<input type='text' spellcheck="false" value="<?php _e( "Choose selector to edit...", "oxygen" ); ?>"/>
						<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/dropdown-arrow.svg'/>
				</div>
				<div class='oxygen-active-selector-box'
					ng-if="!iframeScope.isNotSelectedYet(iframeScope.component.active.id)"
					ng-click="iframeScope.onSelectorDropdown()">
					
					<div class='oxygen-active-selector-box-id'
						ng-show="iframeScope.isEditing('id')">id</div>
					<div class='oxygen-active-selector-box-class'
						ng-show="iframeScope.isEditing('class')&&!iframeScope.isEditing('custom-selector')">class</div>

					<input type='text' spellcheck="false"
						ng-show="iframeScope.isEditing('id')"
						ng-model="iframeScope.component.options[iframeScope.component.active.id]['selector']"
						<?php if (oxygen_vsb_current_user_can_full_access()) { ?>
						ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name, 'selector')"
						<?php } else { ?>
						readonly
						<?php } ?>
						/>

					<input type="text" spellcheck="false"
						ng-show="iframeScope.isEditing('class')&&!iframeScope.isEditing('custom-selector')"
						ng-model="iframeScope.currentClass">

					<input type="text" spellcheck="false"
						ng-show="iframeScope.isEditing('custom-selector')"
						ng-model="iframeScope.selectorToEdit"
						ng-change="selectorChange('{{iframeScope.selectorToEdit}}')">

					<div class='oxygen-active-selector-box-state'
						ng-class="{'oxy-styles-present' : iframeScope.isStatesHasOptions()}">
						{{(iframeScope.currentState=="original") ? "state" : ":"+iframeScope.currentState}}
					</div>
				</div>

				<ul class="oxygen-classes-dropdown"
					ng-if="!iframeScope.isEditing('custom-selector')">
					<li>
						<input type="text" class="oxygen-classes-dropdown-input"
							placeholder="<?php _e( "Enter class name...", "oxygen" ); ?>"
							ng-model="iframeScope.newcomponentclass.name"
							ng-change="iframeScope.updateSuggestedClasses()"
							ng-keypress="iframeScope.processClassNameInput($event, iframeScope.component.active.id)"
							focus-me="$parent.ctSelectBoxFocus" />
						<div class="oxygen-classes-dropdown-add-class"
							ng-click="iframeScope.tryAddClassToComponent(iframeScope.component.active.id)">
							<?php _e("add class...", "oxygen"); ?>
						</div>
					</li>
                    <li class="oxygen-classes-dropdown-heading" ng-show="iframeScope.suggestedClasses.length">
                        <div>Suggested Classes</div>
                    </li>
                    <li class="oxygen-classes-dropdown-suggestions" ng-show="iframeScope.suggestedClasses.length">
                        <ul class="oxygen-classes-suggestions">
                            <li ng-repeat="(key, className) in iframeScope.suggestedClasses"
                                ng-click="iframeScope.addSuggestedClassToComponent(className)">
                                <div class='oxygen-active-selector-box-class'>class</div>
                                <div>{{className}}</div>
                            </li>
                        </ul>
                    </li>
                    <li class="oxygen-classes-dropdown-heading">
                        <div>Existing Selectors</div>
					</li>
					
					<li ng-click="iframeScope.switchEditToId(true)"
						ng-hide="copySelectorFromClass||copySelectorFromID">
						<div class='oxygen-active-selector-box-id'>id</div>
						<div>{{iframeScope.getComponentSelector()}}</div>
						<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/copy-styles-to.svg'
								title="<?php _e("Copy styles to another selector", "oxygen"); ?>"
								ng-click="activateCopySelectorMode(false,$event)"/>
						<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/clear-styles.svg'
								ng-class="{'oxygen-disabled':iframeScope.isIDLocked()}"
								class="oxygen-no-margin"
								title="<?php _e("Delete all styles from this selector", "oxygen"); ?>"
								ng-click="iframeScope.clearSelectorOptions();$event.stopPropagation()"/>
						<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete-selector.svg'
								class="oxygen-no-margin oxygen-disabled"/>
					</li>
					<li ng-repeat="(key,className) in iframeScope.componentsClasses[iframeScope.component.active.id]"
						title="{{className}}"
						ng-hide="copySelectorFromClass||copySelectorFromID"
						ng-click="iframeScope.setCurrentClass(className)">
							<div class='oxygen-active-selector-box-class'>class</div>
							<div class='oxygen-active-selector-box-classname'>{{className}}</div>
							<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/copy-styles-to.svg'
								title="<?php _e("Copy styles to another selector", "oxygen"); ?>"
								ng-click="$parent.activateCopySelectorMode(className,$event)"/>
							<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/clear-styles.svg'
								class="oxygen-no-margin"
								ng-class="{'oxygen-disabled':iframeScope.isSelectorLocked(className)}"
								title="<?php _e("Delete all styles from this selector", "oxygen"); ?>"
								ng-click="iframeScope.clearSelectorOptions(className);$event.stopPropagation()"/>
							<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete-selector.svg'
								class="oxygen-no-margin"
								title="<?php _e("Remove class from component", "oxygen"); ?>"
								ng-click="iframeScope.removeComponentClass(className)"/>
					</li>

					<li title="<?php _e("Copy Styles Here", "oxygen"); ?>"
						ng-class="{'oxygen-disabled':iframeScope.isIDLocked()&&!copySelectorFromID}"
						ng-click="iframeScope.copySelectorOptions()"
						ng-show="copySelectorFromClass||copySelectorFromID">
							<div class='oxygen-active-selector-box-id'>id</div>
							<div ng-class="{'oxygen-disabled':copySelectorFromID}">{{iframeScope.getComponentSelector()}}</div>
							<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/copy-styles-to.svg'
								ng-click="$parent.deactivateCopySelectorMode($event)"
								ng-class="{'ct-link-button-highlight':copySelectorFromID,'oxygen-disabled':!copySelectorFromID}"/>
							<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/clear-styles.svg'
								class="oxygen-no-margin oxygen-disabled"/>
							<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete-selector.svg'
								class="oxygen-no-margin oxygen-disabled"/>
					</li>
						
					<li title="<?php _e("Copy Styles Here", "oxygen"); ?>"
						ng-class="{'oxygen-disabled':iframeScope.isSelectorLocked(className)&&copySelectorFromClass!==className}"
						ng-click="iframeScope.copySelectorOptions(className)"
						ng-show="copySelectorFromClass||copySelectorFromID"
						ng-repeat="(key,className) in iframeScope.componentsClasses[iframeScope.component.active.id]">
							<div class='oxygen-active-selector-box-class'>class</div>
							<div ng-class="{'oxygen-disabled':copySelectorFromClass==className}">{{className}}</div>
							<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/copy-styles-to.svg'
								ng-click="$parent.deactivateCopySelectorMode($event)"
								ng-class="{'ct-link-button-highlight':copySelectorFromClass==className,'oxygen-disabled':copySelectorFromClass!=className}"/>
							<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/clear-styles.svg'
									class="oxygen-no-margin oxygen-disabled"/>
							<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete-selector.svg'
									class="oxygen-no-margin oxygen-disabled"/>
					</li>
				
				</ul>
				<!-- .oxygen-classes-dropdown -->
				<?php else : ?>
				<div class='oxygen-active-selector-box'>
					
					<div class='oxygen-active-selector-box-id'
						ng-show="iframeScope.isEditing('id')">id</div>
					<div class='oxygen-active-selector-box-class'
						ng-show="iframeScope.isEditing('class')&&!iframeScope.isEditing('custom-selector')">class</div>
					
					<input type='text' spellcheck="false" readonly
						ng-show="!iframeScope.isEditing('class')"
						ng-model="iframeScope.component.options[iframeScope.component.active.id]['selector']"/>

					<input type="text" spellcheck="false" readonly
						ng-show="iframeScope.isEditing('class')"
						ng-model="iframeScope.currentClass">
					
					<div class='oxygen-active-selector-box-state'
						ng-class="{'oxy-styles-present' : iframeScope.isStatesHasOptions()}">
						{{(iframeScope.currentState=="original") ? "state" : ":"+iframeScope.currentState}}
					</div>
				</div>

					<?php if (oxygen_vsb_user_can_use_ids()) : ?>
					<ul class="oxygen-classes-dropdown"
						ng-if="!iframeScope.isEditing('custom-selector')">
						<li></li>
						<li ng-click="iframeScope.switchEditToId(true)">
							<div class='oxygen-active-selector-box-id'>id</div>
							<div>{{iframeScope.getComponentSelector()}}</div>
						</li>
					</ul>
					<?php endif; ?>
				<?php endif; ?>

				<ul class="oxygen-states-dropdown">
					<li title="<?php _e("Edit original state", "oxygen"); ?>"
						ng-click="iframeScope.switchState('original');">
							<?php _e("original", "oxygen"); ?>
					</li>
					<li title="<?php _e("Edit this state", "oxygen"); ?>"
						ng-repeat="state in iframeScope.getComponentStatesList()"
						ng-click="iframeScope.switchState('original'); iframeScope.switchState(state);"
						ng-class="{'oxy-styles-present':iframeScope.isStateHasOptions(state)}">
							<div>:{{state}}</div>
							<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/remove_icon.svg'
								title="<?php _e("Remove state from component", "oxygen"); ?>"
								ng-click="iframeScope.tryDeleteComponentState(state,$event)"/>

					<li ng-click="iframeScope.addState()">
						<span class="oxygen-states-dropdown-add-state">
							<?php _e("add state...", "oxygen"); ?>
						</span>
					</li>
				</ul>
				<!-- .oxygen-states-dropdown -->

				<div class='oxygen-back-to-selector-detector'
					ng-if="iframeScope.isEditing('custom-selector')&&!iframeScope.isEditing('class')&&disableSelectorDetectorMode"
					ng-click="toggleSelectorDetectorMode();">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/other/pencil.svg'
						title="<?php _e( "Selector Detector Mode", "oxygen" ); ?>"/>
				</div>
				<!-- .oxygen-back-to-selector-detector -->
			</div>
			<!-- .oxygen-active-selector-box -->

		</div>
		<!-- .oxygen-media-query-and-selector-wrapper -->

		<div class="oxygen-measure-box ct-noheader" 
			ng-if="iframeScope.isEditing('pseudo-element')&&!iframeScope.isEditing('custom-selector')">
			
				<input type="text" class="ct-expand ct-no-animate" placeholder="<?php _e("content...", "oxygen"); ?>" spellcheck="false"
					ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['content']"
					ng-change="iframeScope.setOption(iframeScope.component.active.id,iframeScope.component.active.name,'content')"/>
			
		</div>

	<?php }


	/**
	 * Output Breadcrumbs for Manage > Settings first level panels
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	function settings_home_breadcrumbs($current_label) { ?>

		<div class='oxygen-settings-breadcrumb'>
			<div class='oxygen-sidebar-breadcrumb-icon'
				ng-click="tabs.settings=[]">
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg' />
			</div>
			<div class="oxygen-sidebar-breadcrumb-all-styles"  
				ng-click="tabs.settings=[]"><?php _e("All Settings", "oxygen"); ?> </div>
			<div class='oxygen-sidebar-breadcrumb-separator'>/</div>
			<div class='oxygen-sidebar-breadcrumb-current'><?php echo $current_label; ?></div>
		</div>

	<?php }


	/**
	 * Output Breadcrumbs for Manage > Settings various panels
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	function settings_breadcrumbs($current_label, $upper_level_label, $current_tab, $hide="") { ?>

		<div class='oxygen-settings-breadcrumb'
			<?php if ($hide) : ?>
			ng-hide="<?php echo $hide; ?>"
			<?php endif; ?>>
			<div class='oxygen-sidebar-breadcrumb-icon'
				ng-click="switchTab('settings', '<?php echo $current_tab; ?>');">
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg' />
			</div>
			<div class="oxygen-sidebar-breadcrumb-all-styles"  
				ng-click="switchTab('settings', '<?php echo $current_tab; ?>');"><?php echo $upper_level_label; ?> </div>
			<div class='oxygen-sidebar-breadcrumb-separator'>/</div>
			<div class='oxygen-sidebar-breadcrumb-current'><?php echo $current_label; ?></div>
		</div>

	<?php }


	/**
	 * Output single Tab element
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	function settings_tab($label, $tab, $icon, $hide="", $class="") { ?>

		<div class="oxygen-sidebar-advanced-subtab <?php echo $class; ?>" 
			ng-click="switchTab('settings','<?php echo $tab; ?>');"
			<?php if ($hide!=="") : ?>
			ng-hide="<?php echo $hide; ?>"
			<?php endif; ?>
			>
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/<?php echo $icon; ?>">
			<?php echo $label; ?>
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
		</div>

	<?php }


	/**
	 * Output single Child Tab element
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	function settings_child_tab($label, $tab, $child_tab, $icon) { ?>

		<div class="oxygen-sidebar-advanced-subtab" 
			ng-click="switchChildTab('settings','<?php echo $tab; ?>','<?php echo $child_tab; ?>');">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/<?php echo $icon; ?>">
			<?php echo $label; ?>
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
		</div>

	<?php }


	/**
	 * Add component advanced settings tabs
	 *
	 * @since 0.1.1
	 */

	function advanced_settings() {

		foreach ( $this->options['advanced'] as $key => $tab ) :

			if (!oxygen_vsb_current_user_can_full_access() && in_array($key,array("custom-css","custom-js","custom-php","code-css","code-js","code-php"))) {
				continue;
			}

			$ng_show = '';
			//$ng_click = ( $key == "cssjs" ) ? "possibleSwitchToCodeEditor('advanced', '$key')" : "switchTab('advanced', '$key');";
			$ng_class = "iframeScope.isTabHasOptions('$key')";

			if ( $key == "custom-js" || $key == "custom-css" ) {
				$ng_show = "&& !isActiveName('ct_code_block')";
				$ng_class = "!iframeScope.isInherited(iframeScope.component.active.id,'$key')";
			}
			if ( $key == "custom-js" ) {
				$ng_show .= "&& !isActiveName('ct_selector')";
			}
			if ( $key == "code-js" || $key == "code-css" || $key == "code-php" ) {
				$ng_show = "&& isActiveName('ct_code_block')";
				$ng_class = "!iframeScope.isInherited(iframeScope.component.active.id,'$key')";
			}
			if ( $key == "code-mixed" ) {
				$ng_show .= "&& isActiveName('ct_code_block')";
			}
			if ( $key == "effects" ) {
				$ng_show .= "&& !hasOpenTabs('effects') && !isActiveName('oxy-shape-divider')";
			}

			?>

			<div class='oxygen-sidebar-advanced-subtab'
				ng-show="showAllStyles<?php if(isset($ng_show)) echo $ng_show; ?>"
				ng-click="switchTab('advanced', '<?php echo $key; ?>')"
				ng-class="{'oxy-styles-present' : <?php echo $ng_class; ?>}">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/<?php echo $tab['tab_icon']; ?>.svg' />
					<span><?php echo $tab['heading']; ?></span>
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg' />
			</div>

			<?php

			if ( strpos( $key, "code" ) !== false || strpos( $key, "cssjs" ) !== false ) {
				$classes = "oxygen-sidebar-code-editor-panel";
			}
			else {
				$classes = "";
			}

			if ( $key == "effects" ) {
				$classes = "oxygen-effects-tab";
			}

			?>

				<div class="<?php echo isset($classes)?$classes:''; ?> <?php echo $key ;?>" ng-if="isShowTab('advanced', '<?php echo $key; ?>')">
					<?php if ( file_exists( CT_FW_PATH . "/toolbar/views/$key.view.php" ) ) :
						include( "views/$key.view.php");
					else : ?>
						<span><?php printf( __( 'Wrong parameter type: %s', 'oxygen' ), "$key" ); ?></span>
					<?php endif; ?>
				</div>

			<?php /*endif;*/

		endforeach;
			?>
				<div class="background-gradient" ng-if="isShowTab('advanced', 'background-gradient')">
					<?php if ( file_exists( CT_FW_PATH . "/toolbar/views/background/background.gradient.view.php" ) ) :
						include( "views/background/background.gradient.view.php");
					else : ?>
						<span><?php printf( __( 'Wrong parameter type: %s', 'oxygen' ), "background-gradient" ); ?></span>
					<?php endif; ?>
				</div>
				
				<?php if (oxygen_vsb_current_user_can_full_access()) : ?>
				<div class="oxy-lock"
					ng-show="showAllStyles">
					<label class="oxygen-checkbox">
						<input type="checkbox"
							ng-true-value="'true'" 
							ng-false-value="'false'"
							ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['selector-locked']"
							ng-change="iframeScope.setOption(iframeScope.component.active.id,iframeScope.component.active.name,'selector-locked')">
						<div class='oxygen-checkbox-checkbox'
							ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('selector-locked')=='true'}">
							<?php _e("Lock Selector Styles","oxygen"); ?>
						</div>
					</label>
				</div>
				<?php endif; ?>

			<?php
	}


	/**
	 * Output Global Settings
	 *
	 * @since 0.1.9
	 */

	function ct_show_global_fonts_settings() { ?>

		<div class='oxygen-settings-breadcrumb'>
			<div class='oxygen-sidebar-breadcrumb-icon'
				ng-click="switchTab('settings', 'default-styles');">
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg' />
			</div>
			<div class="oxygen-sidebar-breadcrumb-all-styles"  
				ng-click="switchTab('settings', 'default-styles');"><?php _e("Global Styles", "oxygen"); ?> </div>
			<div class='oxygen-sidebar-breadcrumb-separator'>/</div>
			<div class='oxygen-sidebar-breadcrumb-current'><?php _e("Fonts", "oxygen"); ?></div>
		</div>

		<div ng-repeat="(name,font) in iframeScope.globalSettings.fonts">
			<div class='oxygen-control-wrapper'>
				<label class='oxygen-control-label'>{{name}} font</label>
				<div class='oxygen-control oxygen-control-global-font'>

					<div class="oxygen-select oxygen-select-box-wrapper">
						<div class="oxygen-select-box">
							<div class="oxygen-select-box-current">{{iframeScope.globalSettings.fonts[name]}}</div>
							<div class="oxygen-select-box-dropdown"></div>
						</div>
						<div class="oxygen-select-box-options">

							<div class="oxygen-select-box-option">
								<input type="text" value="" placeholder="<?php _e("Search...", "oxygen"); ?>" spellcheck="false"
									ng-model="iframeScope.fontsFilter"/>
							</div>
							<div class="oxygen-select-box-option"
								ng-repeat="font in iframeScope.elegantCustomFonts | filter:iframeScope.fontsFilter | limitTo: 20"
								ng-click="iframeScope.setGlobalFont(name, font);"
								title="<?php _e("Apply this font family", "oxygen"); ?>">
									{{font}}
							</div>
							<div class="oxygen-select-box-option"
								ng-repeat="font in iframeScope.typeKitFonts | filter:iframeScope.fontsFilter | limitTo: 20"
								ng-click="iframeScope.setGlobalFont(name, font.slug);"
								title="<?php _e('Apply this font family', 'oxygen'); ?>">
									{{font.name}}
							</div>
							<div class="oxygen-select-box-option"
								ng-repeat="font in iframeScope.webSafeFonts | filter:iframeScope.fontsFilter | limitTo: 20"
								ng-click="iframeScope.setGlobalFont(name, font);"
								title="<?php _e("Apply this font family", "oxygen"); ?>">
									{{font}}
							</div>
							<div class="oxygen-select-box-option"
								ng-repeat="font in iframeScope.googleFontsList | filter:iframeScope.fontsFilter | limitTo: 20"
								ng-click="iframeScope.setGlobalFont(name,font.family);"
								title="<?php _e('Apply this font family', 'oxygen'); ?>">
									{{font.family}}
							</div>

						</div>
						<!-- .oxygen-select-box-options -->
					</div>
					<!-- .oxygen-select.oxygen-select-box-wrapper -->
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg'
						title="<?php _e('Remove Font', 'oxygen'); ?>"
						ng-show="name!='Display'&&name!='Text'"
						ng-click="iframeScope.deleteGlobalFont(name)"/>

				</div>
			</div>
			<div ng-show="iframeScope.getGoogleFont(iframeScope.globalSettings.fonts[name]).variants">
				<div class="oxygen-font-weight-link" 
					ng-click="showFontWeights=!showFontWeights"><?php _e("weights »","oxygen"); ?></div>
				<div class='oxygen-control-wrapper oxygen-font-weight-options'
					ng-show='showFontWeights'>{{font.variants | json}}
					<label class="oxygen-checkbox"
						ng-repeat="weight in iframeScope.getGoogleFont(iframeScope.globalSettings.fonts[name]).variants">
						<input type="checkbox"
							ng-true-value="'true'" 
							ng-false-value="'false'"
							ng-model="iframeScope.globalSettings.fontsOptions[name][weight]"
							ng-change="iframeScope.loadWebFont(['global',name], true)"> 
						<div class='oxygen-checkbox-checkbox'
							ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.globalSettings.fontsOptions[name][weight]=='true'}">
							{{weight}}
						</div>
					</label>
				</div>
			</div>
		</div>

		<div class="oxygen-add-global-font" 
			ng-click="iframeScope.addGlobalFont()">
			<?php _e('Add font', 'oxygen'); ?>
		</div>

	<?php }


	/**
	 * Toolbar settings / Defaults Styles / Headings
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function settings_headings() { ?>

		<div class='oxygen-settings-breadcrumb'>
			<div class='oxygen-sidebar-breadcrumb-icon'
				ng-click="switchTab('settings', 'default-styles');">
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg' />
			</div>
			<div class="oxygen-sidebar-breadcrumb-all-styles"  
				ng-click="switchTab('settings', 'default-styles');"><?php _e("Global Styles", "oxygen"); ?> </div>
			<div class='oxygen-sidebar-breadcrumb-separator'>/</div>
			<div class='oxygen-sidebar-breadcrumb-current'><?php _e("Headings", "oxygen"); ?></div>
		</div>

		<?php $headings = array("H1","H2","H3","H4","H5","H6");
		
		foreach ($headings as $heading) : ?>
			<div class="oxygen-settings-section-heading">
				<?php echo $heading; ?>
			</div>

			<div class="oxygen-control-row">
				<div class="oxygen-control-wrapper">
					<label class="oxygen-control-label"><?php _e("Font Size","oxygen"); ?></label>
					<div class="oxygen-control">
						
						<div class="oxygen-measure-box" 
							ng-class="{'oxygen-measure-box-unit-none':iframeScope.getGlobalOptionUnit('global', 'headings.<?php echo $heading; ?>.font-size')==' '}">
							<input type="text" spellcheck="false" data-option="headings.<?php echo $heading; ?>.font-size"
								ng-model="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-size']" 
								ng-model-options="{ debounce: 10 }">
                            <?php self::global_measure_box_unit_selector("global", "headings.$heading.font-size", "px,%,em") ?>
						</div>
					</div>
				</div>
		
				<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
					<label class='oxygen-control-label'><?php _e("Font Weight","oxygen"); ?></label>
					<div class='oxygen-control'>

						<div class="oxygen-select oxygen-select-box-wrapper">
							<div class="oxygen-select-box">
								<div class="oxygen-select-box-current">{{$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']}}</div>
								<div class="oxygen-select-box-dropdown"></div>
							</div>
							<div class="oxygen-select-box-options">
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']=''">&nbsp;</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='100'">100</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='200'">200</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='300'">300</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='400'">400</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='500'">500</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='600'">600</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='700'">700</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='800'">800</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='900'">900</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="oxygen-control-row">
				<div class="oxygen-control-wrapper">
					<label class="oxygen-control-label"><?php _e("Line Height","oxygen"); ?></label>
					<div class="oxygen-control">
						<div class="oxygen-input">
							<input type="text" spellcheck="false" 
								ng-model="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['line-height']" 
								ng-model-options="{ debounce: 10 }">
						</div>
					</div>
				</div>
			</div>

			<div class="oxygen-control-row">
				<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
					<label class='oxygen-control-label'><?php _e("Color","oxygen"); ?></label>
					<div class='oxygen-control'>

						<div class='oxygen-color-picker'>
							<div class="oxygen-color-picker-color">
								<input ctiriscolorpicker=""
									class="ct-iris-colorpicker"
									type="text" spellcheck="false"
									ng-model="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['color']"
									ng-style="{'background-color':$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['color']}"/>
							</div>
							<input type="text" spellcheck="false"
								ng-model="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['color']"/>
							<img class="oxygen-add-global-color-icon" 
								title="<?php _e("Save as Global Color","oxygen"); ?>"
								src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/make-global-color.svg'
								ng-click="$parent.showAddNewColorDialog($event)"/>
						</div>
					</div>
				</div>
			</div>

		<?php endforeach;

	}


	/**
	 * Toolbar settings / Global Styles / Colors
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function settings_colors() { ?>

		<div class='oxygen-settings-breadcrumb'
			ng-hide="hasOpenChildTabs('settings','colors')">
			<div class='oxygen-sidebar-breadcrumb-icon'
				ng-click="switchTab('settings', 'default-styles');">
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg' />
			</div>
			<div class="oxygen-sidebar-breadcrumb-all-styles" 
				ng-click="switchTab('settings', 'default-styles');"><?php _e("Global Styles", "oxygen"); ?> </div>
			<div class='oxygen-sidebar-breadcrumb-separator'>/</div>
			<div class='oxygen-sidebar-breadcrumb-current'><?php _e("Colors", "oxygen"); ?></div>
		</div>

		<div ng-repeat="(setID,set) in iframeScope.globalColorSets.sets">
			<div class="oxygen-sidebar-advanced-subtab" 
				ng-click="switchChildTab('settings', 'colors', set.id);"
				ng-show="!hasOpenChildTabs('settings', 'colors');">
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
				{{set.name}}
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
			</div>

			<div ng-show="isShowChildTab('settings','colors',set.id)">

				<div class='oxygen-settings-breadcrumb'>
					<div class='oxygen-sidebar-breadcrumb-icon'
						ng-click="switchTab('settings','colors')">
						<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg' />
					</div>
					<div class="oxygen-sidebar-breadcrumb-all-styles"
						ng-click="switchTab('settings','colors')">
						<?php _e("Colors", "oxygen"); ?>
					</div>
					<div class='oxygen-sidebar-breadcrumb-separator'>/</div>
					<div class='oxygen-sidebar-breadcrumb-current'>
						{{set.name}}
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg"
							title="<?php _e("Delete","oxygen")?> {{set.name}}"
	                    	ng-click="iframeScope.deleteGlobalColorSet(set.id, true)">
					</div>
				</div>
				
				<div class="oxygen-control-row"
					ng-repeat="(key,color) in iframeScope.globalColorSets.colors | filter: {set: set.id}">
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'>{{color.name + ' (id: ' + color.id + ')'}}</label>
						<div class='oxygen-control'>
							<div class='oxygen-color-picker<?php if(defined('CT_FREE')) echo ' oxygen-color-picker-requires-pro';?>' <?php if(defined('CT_FREE')) echo 'ng-click="showDialogWindow(); dialogForms[\'showProGlobalColorEditDialog\'] = true;"';?>>
								<div class="oxygen-color-picker-color" >
									<input <?php if(!defined('CT_FREE')) echo 'ctiriscolorpicker=""';?>
										class="ct-iris-colorpicker oxy-settings-global-styles-color"
										type="text" spellcheck="false" 
										ng-model="color.value"
										ng-model-options="{ debounce: 10 }"
										ng-change="globalColorChange(color.id)"
										ng-style="{'background-color':color.value}"/>
								</div>
								<input type="text" spellcheck="false"
									<?php echo defined('CT_FREE') ? 'ng-attr-value={{color.value}}' : 'ng-model="color.value" ng-change="globalColorChange(color.id)"';?>
									ng-model-options="{ debounce: 10 }"
									/>
							</div>
						</div>
					</div>
					<img class="oxygen-remove-color-icon" 
						src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg" 
						title="<?php _e("Remove Color","oxygen"); ?>" 
						ng-click="iframeScope.deleteGlobalColor(color.id)">
				</div>
				
				<div class="oxygen-add-global-color"
					ng-show="!$parent.$parent.showAddGlobalColorPanel"
					ng-click="$parent.$parent.showAddGlobalColorPanel=true;$parent.$parent.newGlobalColorNameFocus=true">
					<?php _e("Add Color", "oxygen"); ?>
				</div>
				
				<div class="oxygen-global-colors-new-color" 
					ng-show="$parent.$parent.showAddGlobalColorPanel">
					
					<div class="oxygen-control-row">
						<div class='oxygen-control-wrapper'>
							<label class='oxygen-control-label'><?php _e("New Color","oxygen"); ?></label>
							<div class='oxygen-control'>
								<div class="oxygen-input">
		                        	<input type="text" spellcheck="false" placeholder="<?php _e("Color Name","oxygen"); ?>"
		                            	ng-model="$parent.$parent.newGlobalColorName" ng-model-options="{ debounce: 10 }"
		                            	focus-me="$parent.$parent.newGlobalColorNameFocus"
		                    			ng-keypress="$parent.$parent.newGlobalColorNameKeyPress($event, set.id)">
		                        </div>
	                    	</div>
	                    </div>
	                </div>

	                <div class="oxygen-control-row">
	                    <div class='oxygen-control-wrapper'>
							<div class='oxygen-control'>
								<div class='oxygen-color-picker'>
									<div class="oxygen-color-picker-color">
										<input ctiriscolorpicker=""
											class="ct-iris-colorpicker oxy-settings-global-styles-color"
											type="text" spellcheck="false" 
											ng-model="$parent.$parent.newGlobalColorValue"/>
									</div>
									<input type="text" spellcheck="false"
										ng-model="$parent.$parent.newGlobalColorValue"/>
								</div>
							</div>
						</div>
					</div>

					<div class="oxygen-control-row"
						ng-show="$parent.$parent.showAddGlobalColorPanel">
						<div class="oxygen-add-global-color"
							ng-click="iframeScope.addNewColor($parent.$parent.newGlobalColorName,set.id,$parent.$parent.newGlobalColorValue);">
							<?php _e("Add Color", "oxygen"); ?>
						</div>
					</div>

				</div>
			</div>

		</div>

		<div class="oxygen-add-global-color-set"
			ng-show="!$parent.hasOpenChildTabs('settings', 'colors');"
			ng-click="$parent.addGlobalColorSetPanel=true;$parent.addGlobalColorSetFocus=true">
			<?php _e("Add Color Set", "oxygen"); ?>
		</div>
        <div class="oxygen-global-colors-new-color-set" 
            ng-show="$parent.addGlobalColorSetPanel">
            <div class="oxygen-control-row">
				<div class='oxygen-control-wrapper'>
					<label class='oxygen-control-label'><?php _e("New Color Set","oxygen"); ?></label>
					<div class='oxygen-control'>
						<div class="oxygen-input">
		                    <input type="text" spellcheck="false" placeholder="<?php _e("Color Set Name","oxygen"); ?>"
		                    	focus-me="$parent.addGlobalColorSetFocus"
		                    	ng-keypress="$parent.addGlobalColorSetKeyPress($event)"
		                        ng-model="$parent.newGlobalColorSetName" ng-model-options="{ debounce: 10 }">
		                </div>
	             	</div>
	            </div>
	        </div>
	        <div class="oxygen-control-row">
	            <div class="oxygen-add-global-color-button"
	                ng-click="iframeScope.addNewColorSet($parent.newGlobalColorSetName)">Add</div>
	        </div>
        </div>

	<?php }


	/**
	 * Toolbar settings / Defaults Styles / Body Text
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function settings_body_text() { ?>

		<div class='oxygen-settings-breadcrumb'>
			<div class='oxygen-sidebar-breadcrumb-icon'
				ng-click="switchTab('settings', 'default-styles');">
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg' />
			</div>
			<div class="oxygen-sidebar-breadcrumb-all-styles" 
				ng-click="switchTab('settings', 'default-styles');"><?php _e("Global Styles", "oxygen"); ?> </div>
			<div class='oxygen-sidebar-breadcrumb-separator'>/</div>
			<div class='oxygen-sidebar-breadcrumb-current'><?php _e("Body Text", "oxygen"); ?></div>
		</div>

		<div class="oxygen-control-row">
			<div class="oxygen-control-wrapper">
				<label class="oxygen-control-label"><?php _e("Font Size","oxygen"); ?></label>
				<div class="oxygen-control">
					
					<div class="oxygen-measure-box">
						<input type="text" spellcheck="false" data-option="body_text.font-size"
							ng-model="$parent.iframeScope.globalSettings.body_text['font-size']" 
							ng-model-options="{ debounce: 10 }">
                        <?php self::global_measure_box_unit_selector("global", "body_text.font-size", "px,%,em") ?>
					</div>
				</div>
			</div>
		</div>
	
		<div class="oxygen-control-row">
			<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
				<label class='oxygen-control-label'><?php _e("Font Weight","oxygen"); ?></label>
				<div class='oxygen-control'>

					<div class="oxygen-select oxygen-select-box-wrapper">
						<div class="oxygen-select-box">
							<div class="oxygen-select-box-current">{{$parent.iframeScope.globalSettings.body_text['font-weight']}}</div>
							<div class="oxygen-select-box-dropdown"></div>
						</div>
						<div class="oxygen-select-box-options">
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']=''">&nbsp;</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='100'">100</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='200'">200</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='300'">300</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='400'">400</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='500'">500</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='600'">600</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='700'">700</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='800'">800</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='900'">900</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="oxygen-control-row">
			<div class="oxygen-control-wrapper">
				<label class="oxygen-control-label"><?php _e("Line Height","oxygen"); ?></label>
				<div class="oxygen-control">
					<div class="oxygen-input">
						<input type="text" spellcheck="false" 
							ng-model="$parent.iframeScope.globalSettings.body_text['line-height']" 
							ng-model-options="{ debounce: 10 }">
					</div>
				</div>
			</div>
		</div>

		<div class="oxygen-control-row">
			<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
				<label class='oxygen-control-label'><?php _e("Color","oxygen"); ?></label>
				<div class='oxygen-control'>

					<div class='oxygen-color-picker'>
						<div class="oxygen-color-picker-color">
							<input ctiriscolorpicker=""
								class="ct-iris-colorpicker"
								type="text" spellcheck="false"
								ng-model="$parent.iframeScope.globalSettings.body_text['color']"
								ng-style="{'background-color':$parent.iframeScope.globalSettings.body_text['color']}"/>
						</div>
						<input type="text" spellcheck="false"
							ng-model="$parent.iframeScope.globalSettings.body_text['color']"/>
						<img class="oxygen-add-global-color-icon" 
							title="<?php _e("Save as Global Color","oxygen"); ?>"
							src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/make-global-color.svg'
							ng-click="$parent.showAddNewColorDialog($event)"/>
					</div>
				</div>
			</div>
		</div>

	<?php }


	/**
	 * Toolbar settings / Defaults Styles / Links
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function settings_links() { ?>

		<?php $this->settings_breadcrumbs(	
			__('Links','oxygen'),
			__('Global Styles','oxygen'),
			'default-styles',
			"hasOpenChildTabs('settings','links')" ); ?>

		<?php $links = array(	"all" => __("All","oxygen"),
								"text_link" => __("Text Link","oxygen"),
								"link_wrapper" => __("Link Wrapper","oxygen"),
								"button" => __("Button","oxygen") );

		foreach ($links as $link => $title) : ?>

		<div class="oxygen-sidebar-advanced-subtab" 
			ng-hide="<?php foreach($links as $link2 => $title2):?>isShowChildTab('settings','links','<?php echo $link2 ?>')<?php if ($link2!="button") echo "||"; endforeach; ?>"
			ng-click="switchChildTab('settings', 'links', '<?php echo $link ?>');">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
			<?php echo $title; ?>
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
		</div>

		<div
			ng-show="isShowChildTab('settings','links','<?php echo $link ?>')">

			<div class='oxygen-settings-breadcrumb'>
				<div class='oxygen-sidebar-breadcrumb-icon'
					ng-click="switchTab('settings', 'links');">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg' />
				</div>
				<div class="oxygen-sidebar-breadcrumb-all-styles"  
					ng-click="switchTab('settings', 'links');"><?php _e("Links", "oxygen"); ?> </div>
				<div class='oxygen-sidebar-breadcrumb-separator'>/</div>
				<div class='oxygen-sidebar-breadcrumb-current' 
					ng-show="isShowChildTab('settings','links','<?php echo $link; ?>')">
					<?php echo $title; ?>
				</div>
			</div>
					
			<?php if ($link!="button") : ?>
			<div class="oxygen-settings-section-heading"><?php _e("Normal","oxygen"); ?></div>
			<div class="oxygen-control-row">
				<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
					<label class='oxygen-control-label'><?php _e("Color","oxygen"); ?></label>
					<div class='oxygen-control'>
						<div class='oxygen-color-picker'>
							<div class="oxygen-color-picker-color">
								<input ctiriscolorpicker=""
									class="ct-iris-colorpicker"
									type="text" spellcheck="false"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['color']"
									ng-style="{'background-color':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['color']}"/>
							</div>
							<input type="text" spellcheck="false"
								ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['color']"/>
							<img class="oxygen-add-global-color-icon" 
								title="<?php _e("Save as Global Color","oxygen"); ?>"
								src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/make-global-color.svg'
								ng-click="$parent.showAddNewColorDialog($event)"/>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<div class="oxygen-control-row">
				<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
					<label class='oxygen-control-label'><?php _e("Font Weight","oxygen"); ?></label>
					<div class='oxygen-control'>

						<div class="oxygen-select oxygen-select-box-wrapper">
							<div class="oxygen-select-box">
								<div class="oxygen-select-box-current">{{$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']}}</div>
								<div class="oxygen-select-box-dropdown"></div>
							</div>
							<div class="oxygen-select-box-options">
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']=''">&nbsp;</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='100'">100</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='200'">200</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='300'">300</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='400'">400</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='500'">500</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='600'">600</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='700'">700</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='800'">800</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='900'">900</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<?php if ($link!="button") : ?>
			<div class='oxygen-control-wrapper'>
				<label class='oxygen-control-label'><?php _e("Text Decoration"); ?></label>
				<div class='oxygen-control'>
					<div class='oxygen-button-list'>

						<label class='oxygen-button-list-button'
							ng-class="{'oxygen-button-list-button-active':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['text-decoration']=='none'}">
								<input type="radio" name="text-decoration" value="none"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['text-decoration']"
									ng-model-options="{ debounce: 10 }" 
									ng-click="$parent.globalSettingsRadioButtonClick($parent.iframeScope.globalSettings.links['<?php echo $link ?>'], 'text-decoration', 'none')"/>
								none
						</label>

						<label class='oxygen-button-list-button oxygen-text-decoration-underline'
							ng-class="{'oxygen-button-list-button-active':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['text-decoration']=='underline'}">
								<input type="radio" name="text-decoration" value="underline"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['text-decoration']"
									ng-model-options="{ debounce: 10 }" 
									ng-click="$parent.globalSettingsRadioButtonClick($parent.iframeScope.globalSettings.links['<?php echo $link ?>'], 'text-decoration', 'underline')"/>
								U
						</label>

						<label class='oxygen-button-list-button oxygen-text-decoration-overline'
							ng-class="{'oxygen-button-list-button-active':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['text-decoration']=='overline'}">
								<input type="radio" name="text-decoration" value="overline"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['text-decoration']"
									ng-model-options="{ debounce: 10 }" 
									ng-click="$parent.globalSettingsRadioButtonClick($parent.iframeScope.globalSettings.links['<?php echo $link ?>'], 'text-decoration', 'overline')"/>
								O
						</label>

						<label class='oxygen-button-list-button oxygen-text-decoration-linethrough'
							ng-class="{'oxygen-button-list-button-active':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['text-decoration']=='line-through'}">
								<input type="radio" name="text-decoration" value="line-through"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['text-decoration']"
									ng-model-options="{ debounce: 10 }" 
									ng-click="$parent.globalSettingsRadioButtonClick($parent.iframeScope.globalSettings.links['<?php echo $link ?>'], 'text-decoration', 'line-through')"/>
								S
						</label>

					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php if ($link=="button") : ?>
			<div class="oxygen-control-row">
				<div class="oxygen-control-wrapper">
					<label class="oxygen-control-label"><?php _e("Border radius","oxygen"); ?></label>
					<div class="oxygen-control">
						
						<div class="oxygen-measure-box">
							<input type="text" spellcheck="false" data-option="links.<?php echo $link; ?>.border-radius"
								ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link; ?>']['border-radius']" 
								ng-model-options="{ debounce: 10 }">
                            <?php self::global_measure_box_unit_selector("global", "links.$link.border-radius", "px,%,em") ?>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>
			<?php if ($link!="button") : ?>
			<div class="oxygen-settings-section-heading"><?php _e("Hover","oxygen"); ?></div>

			<div class="oxygen-control-row">
				<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
					<label class='oxygen-control-label'><?php _e("Color","oxygen"); ?></label>
					<div class='oxygen-control'>
						<div class='oxygen-color-picker'>
							<div class="oxygen-color-picker-color">
								<input ctiriscolorpicker=""
									class="ct-iris-colorpicker"
									type="text" spellcheck="false"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_color']"
									ng-style="{'background-color':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_color']}"/>
							</div>
							<input type="text" spellcheck="false"
								ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_color']"/>
							<img class="oxygen-add-global-color-icon" 
								title="<?php _e("Save as Global Color","oxygen"); ?>"
								src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/make-global-color.svg'
								ng-click="$parent.showAddNewColorDialog($event)"/>
						</div>
					</div>
				</div>
			</div>

			<div class='oxygen-control-wrapper'>
				<label class='oxygen-control-label'><?php _e("Text Decoration"); ?></label>
				<div class='oxygen-control'>
					<div class='oxygen-button-list'>

						<label class='oxygen-button-list-button'
							ng-class="{'oxygen-button-list-button-active':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_text-decoration']=='none'}">
								<input type="radio" name="text-decoration" value="none"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_text-decoration']"
									ng-model-options="{ debounce: 10 }" 
									ng-click="$parent.globalSettingsRadioButtonClick($parent.iframeScope.globalSettings.links['<?php echo $link ?>'], 'hover_text-decoration', 'none')"/>
								none
						</label>

						<label class='oxygen-button-list-button oxygen-text-decoration-underline'
							ng-class="{'oxygen-button-list-button-active':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_text-decoration']=='underline'}">
								<input type="radio" name="text-decoration" value="underline"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_text-decoration']"
									ng-model-options="{ debounce: 10 }" 
									ng-click="$parent.globalSettingsRadioButtonClick($parent.iframeScope.globalSettings.links['<?php echo $link ?>'], 'hover_text-decoration', 'underline')"/>
								U
						</label>

						<label class='oxygen-button-list-button oxygen-text-decoration-overline'
							ng-class="{'oxygen-button-list-button-active':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_text-decoration']=='overline'}">
								<input type="radio" name="text-decoration" value="overline"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_text-decoration']"
									ng-model-options="{ debounce: 10 }" 
									ng-click="$parent.globalSettingsRadioButtonClick($parent.iframeScope.globalSettings.links['<?php echo $link ?>'], 'hover_text-decoration', 'overline')"/>
								O
						</label>

						<label class='oxygen-button-list-button oxygen-text-decoration-linethrough'
							ng-class="{'oxygen-button-list-button-active':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_text-decoration']=='line-through'}">
								<input type="radio" name="text-decoration" value="line-through"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_text-decoration']"
									ng-model-options="{ debounce: 10 }" 
									ng-click="$parent.globalSettingsRadioButtonClick($parent.iframeScope.globalSettings.links['<?php echo $link ?>'], 'hover_text-decoration', 'line-through')"/>
								S
						</label>

					</div>
				</div>
			</div>
			<?php endif; ?>

		</div>

		<?php endforeach;
	}


	/**
	 * Components Browser tabs anchors
	 *
	 * @since 0.2.3
	 */

	function components_anchors() { ?>

		<?php
			if ( $this->folders["status"] == "ok" ) {
				$this->output_top_folders_anchors( $this->folders );
			}
			/*elseif (!get_option('oxygen_license_key')) {
				// do nothing
			}
			elseif ( $this->folders["status"] == "error" && isset($this->folders["message"])) {
				echo "<span class=\"ct-folders-anchors-error\">".sanitize_text_field($this->folders["message"])."</span>";
			}
			elseif ( $this->folders["status"] == "error" && is_array($this->folders["errors"])) {
				echo "<span class=\"ct-folders-anchors-error\">".sanitize_text_field($this->folders["errors"][0])."</span>";
			}
			else {
				var_dump( $this->folders );
			}*/
		?>

		<?php
	}


	/**
	 * Recursively output all folders' content
	 *
	 * @since 0.4.0
	 * @author Ilya K.
	 */

	function output_folders_content( $folders, $main_key = "", $title = "", $path="", $depth = 0 ) {

		if ( !is_array( $folders ) )
			return;

		$depth++;

		unset($folders["status"]);

		if ( $main_key ) {
			$path = "switchTab('components','" . esc_attr( $main_key ) . "')";
		}

		if ( $main_key && $depth > 2) {
			$path = "iframeScope.openFolder('" . esc_attr( $main_key ) . "')";
		}

		global $folder_type;
		global $folder_class;

		foreach ( $folders as $key => $folder ) :

			if ( !is_array( $folder ) )
				continue;

			$slug = (isset($folder["name"]) ? sanitize_title($folder["name"]):'') . "-" . (isset($folder["id"])?$folder["id"]:'');

			// show only top anchors
			if ($title==""&&$path=="") { ?>

				<div class='oxygen-add-section-accordion'
					ng-click="switchTab('components', '<?php echo $slug; ?>');"
					ng-hide="iframeScope.hasOpenFolders()">
					<?php echo isset($folder["name"])?sanitize_text_field($folder["name"]):''; ?>
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dropdown-arrow.svg' />
				</div>

			<?php }

			if ( isset($folder["id"]) && ($folder["id"] === "design_sets" || $folder["id"] === "components" || $folder["id"] === "pages") ) {
				$folder_type = isset($folder["id"])?$folder["id"]:false;
				$folder_class = "ct-api-items";
			}

			if ( isset($folder["name"]) && $folder["name"] === "WordPress" ) {
				$folder_class = "";
			}

			if ($path !== "") : ?>

				<div class="oxygen-sidebar-breadcrumb"
					ng-if="iframeScope.isShowFolder('<?php echo $slug; ?>') && iframeScope.designSetSubTab !== 1 && iframeScope.designSetSubTab !== 2">

						<div class="oxygen-sidebar-breadcrumb-icon">
							<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg" 
								ng-click="<?php echo $path; ?>">
						</div>
						<div class="oxygen-sidebar-breadcrumb-all-styles"
							ng-click="<?php echo $path; ?>">
							<?php echo esc_html( $title ) ?>		
						</div>
						<div class="oxygen-sidebar-breadcrumb-separator">/</div>
						<div class="oxygen-sidebar-breadcrumb-current"><?php if (isset($folder["name"])) echo sanitize_text_field($folder["name"]); ?></div>
				</div>

				<div class="oxygen-sidebar-breadcrumb"
					ng-if="iframeScope.isShowFolder('<?php echo $slug; ?>') && (iframeScope.designSetSubTab === 1 || iframeScope.designSetSubTab === 2)">
						<div class="oxygen-sidebar-breadcrumb-icon">
							<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg" 
								ng-click="iframeScope.designSetSubTab=0">
						</div>
						<div class="oxygen-sidebar-breadcrumb-all-styles"
							ng-click="iframeScope.designSetSubTab=0">
							<?php if (isset($folder["name"])) echo sanitize_text_field($folder["name"]); ?>
						</div>
						<div class="oxygen-sidebar-breadcrumb-separator">/</div>
						<div class="oxygen-sidebar-breadcrumb-current">{{(iframeScope.designSetSubTab===1?'Components':'Pages')}}</div>
				</div>

				<div class="oxygen-add-section-accordion-contents oxygen-add-section-accordion-contents-toppad oxygen-folder-<?php echo $slug; ?> <?php echo $folder_class; ?>" 
					ng-class="{'oxygen-folder-no-padding': !iframeScope.isShowFolder('design-sets-experimental') 
																	&& !iframeScope.isShowFolder('dynamic-data-data')
																	&& !iframeScope.isShowFolder('widgets-widgets')
																	&& !iframeScope.isShowFolder('sidebars-sidebars') }"
					ng-if="iframeScope.isShowFolder('<?php echo $slug; ?>')" >	

					
					<div class='oxygen-add-section-library-menu'
						ng-if="iframeScope.isShowFolder('categories-categories')">
						<div class='oxygen-add-section-library-menu-category'>
							<h1>Sections &amp; Elements</h1>
							<div class='oxygen-add-section-library-menu-subcategories'>
								<a ng-repeat="(key, category) in iframeScope.libraryCategories track by key" data-cat='category-{{category.slug}}'>{{key}}<span class='oxygen-add-section-library-count'>{{category.contents.length}}</span><img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/dropdown-arrow.svg"></a>
							</div>
						</div>
					</div>

					<div class='oxygen-add-section-library-menu'
						ng-if="iframeScope.isShowFolder('categories-categories')">
						<div class='oxygen-add-section-library-menu-category'>
							<h1>Pages</h1>
							<div class='oxygen-add-section-library-menu-subcategories'>
								<a ng-repeat="(key, category) in iframeScope.libraryPages track by key" data-cat='page-{{category.slug}}'>{{key}}<span class='oxygen-add-section-library-count'>{{category.contents.length}}</span><img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/dropdown-arrow.svg"></a>
							</div>
						</div>
					</div>
					
					<div class='oxygen-add-section-library-menu'
						ng-show="iframeScope.experimental_components[iframeScope.openFolders['<?php echo $slug; ?>']] && iframeScope.designSetSubTab!==1 && iframeScope.designSetSubTab!==2">
						<div class='oxygen-add-section-library-menu-category'>
							<div class='oxygen-add-section-library-menu-subcategories'>
								<a data-cat='designset-{{iframeScope.openFolders["<?php echo $slug; ?>"]}}-pages' class="oxygen-add-designset-pages">Pages<span class='oxygen-add-section-library-count'>{{iframeScope.experimental_components[iframeScope.openFolders['<?php echo $slug; ?>']]['pages'].length}}</span><img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/dropdown-arrow.svg"></a>
								<a data-cat='designset-{{iframeScope.openFolders["<?php echo $slug; ?>"]}}-templates' class="oxygen-add-designset-templates">Templates<span class='oxygen-add-section-library-count'>{{iframeScope.experimental_components[iframeScope.openFolders['<?php echo $slug; ?>']]['templates'].length}}</span><img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/dropdown-arrow.svg"></a>
							</div>
						</div>
					</div>


					<div style="margin: 20px;" ng-show="iframeScope.experimental_components[iframeScope.openFolders['<?php echo $slug; ?>']] && iframeScope.designSetSubTab!==1 && iframeScope.designSetSubTab!==2" class="oxygen-add-section-subsection" ng-click="iframeScope.designSetSubTab=1; applyMenuAim()">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/small-components.svg" class="oxygen-add-section-subsection-icon">
						Sections &amp; Elements
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/small-arrow.svg">
					</div>

					<div ng-show="iframeScope.designSetSubTab===1" class="oxygen-folder-no-padding oxygen-add-section-accordion-contents oxygen-add-section-accordion-contents-toppad oxygen-folder-<?php echo $slug; ?> <?php echo $folder_class; ?>"  >	

					
						<div class='oxygen-add-section-library-menu'>
							<div class='oxygen-add-section-library-menu-category'>
								
								<div class='oxygen-add-section-library-menu-subcategories'>
									<a ng-repeat="(key, category) in iframeScope.experimental_components[iframeScope.openFolders['<?php echo $slug; ?>']]['items'] track by key" data-cat='category-{{iframeScope.openFolders["<?php echo $slug; ?>"]}}-{{category.slug}}'>{{key}}<span class='oxygen-add-section-library-count'>{{category.contents.length}}</span><img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/dropdown-arrow.svg"></a>
								</div>
							</div>
						</div>

					</div>
					
					<div ng-show="iframeScope.designSetSubTab===2" class="oxygen-add-section-designed-component" ng-repeat = "item in iframeScope.experimental_components[iframeScope.openFolders['<?php echo $slug; ?>']]['pages']"> 
						<div 
							ng-click="iframeScope.showAddItemDialog(item.id, 'component', '0', '', item.source, null, iframeScope.openFolders['<?php echo $slug; ?>'])">
							<div class="oxygen-add-section-designed-component-header">
								<span class="oxygen-add-section-designed-component-title">{{item.name}}</span>
								<span class="oxygen-add-section-designed-component-design-label"></span>
								<span class="oxygen-add-section-designed-component-add-icon" title="<?php _e("Add now","oxygen")?>"
									ng-click="iframeScope.addItem(item.id, 'page', $event, item.source)"></span>
							</div>
							<img class="ct-add-item-button-image" data-src="{{item.screenshot_url}}">
						</div>
					</div>

					
			<?php else: ?>

				<div class="oxygen-add-section-accordion-contents oxygen-add-section-accordion-contents-toppad oxygen-folder-<?php echo $slug; ?> <?php echo $folder_class; ?>" 
					ng-if="isShowTab('components','<?php echo $slug; ?>')">

			<?php endif; ?>


				<?php if ( isset($folder["id"]) && $folder["id"] === "widgets" ) : ?>
					<?php do_action("ct_toolbar_widgets_folder"); ?>

				<?php elseif ( isset($folder["id"]) && $folder["id"] === "data" ) : ?>
					<?php do_action("ct_toolbar_data_folder"); ?>

				<?php elseif ( isset($folder["id"]) && $folder["id"] === "sidebars" ) : ?>
					<?php do_action("ct_toolbar_sidebars_folder"); ?>

				<?php else : ?>

					<?php if ( isset($folder["name"]) && $folder["name"] === "WordPress" ) : ?>
						<?php do_action("oxy_folder_wordpress_components"); ?>
					<?php endif; ?>

					<?php if ( isset($folder["children"]) && $folder["children"] ) : ?>
						<?php foreach ( $folder["children"] as $subkey => $subfolder ) :

							if(isset($subfolder['code'])) {
								echo $subfolder['code'];
								continue;
							}
							
							$subslug = sanitize_title($subfolder["name"]) . "-" . $subfolder["id"];
							$icon = str_replace(" ", "", strtolower($subfolder["name"]));
							// check if icon exist
							if (!in_array($icon, array("components","designsets","dynamicdata","sidebars","widgets"))) {
								$icon = "generic";
							}
							
							if (!oxygen_vsb_current_user_can_full_access() && $subfolder["id"] && in_array($subfolder["id"],array("data","sidebars","widgets"))) {
								continue;
							}

						?>
			
							<?php if ( isset($subfolder["component"]) && $subfolder["component"] ) : ?>
								<?php do_action("ct_folder_component_" . $subslug); ?>
							<?php else : ?>
	
								<?php if ( $subslug !== "composite-elements-0" ) : ?>
								<div class="oxygen-add-section-subsection"
								<?php if(isset($subfolder["fresh"])) { ?>
									ng-click="iframeScope.openLoadFolder('<?php echo $subslug; ?>', '<?php echo $subkey;?>');tabs['components']=[]"
								<?php } else { ?>
									ng-click="iframeScope.openFolder('<?php echo $subslug; ?>');tabs['components']=[]"
								<?php } ?>
									>
									<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/small-<?php echo $icon; ?>.svg" class="oxygen-add-section-subsection-icon">
									<?php echo sanitize_text_field( $subfolder["name"]) ; ?>
									<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/small-arrow.svg">
								</div>
								<?php endif; ?>
	
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>

					<?php if ( isset($folder["items"]) && $folder["items"] ) : ?>
						<?php foreach ( $folder["items"] as $subkey => $subfolder ) :
							if ( empty( $subfolder ) || ! is_array( $subfolder ) ) {
								continue;
							}
							$subslug = sanitize_title( $subfolder["name"] ) . "-" . $subfolder["id"];

							// update screenshot to use imgix
							if ( $subfolder["screenshot_url"] && strpos( $subfolder["screenshot_url"], "s3.amazonaws.com") !== false ) {
								$subfolder["screenshot_url"] = str_replace(
																	"https://s3.amazonaws.com/asset-dev-testing/",
																	"https://oxygen.imgix.net/", $subfolder["screenshot_url"]);
								$subfolder["screenshot_url"] .= "?w=520";
							}
						?>

							<?php if ( isset ( $subfolder["component"] ) ) : ?>
								<?php do_action("ct_folder_component_".$subslug ); ?>
							<?php else : ?>
								<div class="oxygen-add-section-designed-component"
									ng-click="iframeScope.showAddItemDialog(<?php echo sanitize_text_field($subfolder["id"]); ?>, '<?php echo sanitize_text_field($folder["type"]); ?>', '<?php echo sanitize_text_field($folder["id"]); ?>', '<?php echo sanitize_text_field($folder_type); ?>'<?php echo isset($subfolder["source"])?", '".sanitize_text_field($subfolder["source"])."'":""; echo isset($subfolder["page"])?", '".sanitize_text_field($subfolder["page"])."'":"";?>)">
									<div class="oxygen-add-section-designed-component-header">
										<span class="oxygen-add-section-designed-component-title"><?php echo sanitize_text_field( $subfolder["name"] ); ?></span>
										<span class="oxygen-add-section-designed-component-design-label"><?php echo sanitize_text_field( $subfolder["design_set_name"] ); ?></span>
										<span class="oxygen-add-section-designed-component-add-icon" title="<?php _e("Add now","oxygen")?>"
											ng-click="iframeScope.addItem(<?php echo sanitize_text_field($subfolder["id"]); ?>, '<?php echo sanitize_text_field($folder["type"]); ?>', $event<?php echo isset($subfolder["source"])?", '".sanitize_text_field($subfolder["source"])."'":""; echo isset($subfolder["page"])?", '".sanitize_text_field($subfolder["page"])."'":"";?>)"></span>
									</div>
									<img class="ct-add-item-button-image" data-src="<?php echo esc_url($subfolder["screenshot_url"]); ?>">
								</div>
							<?php endif; ?>

						<?php endforeach; ?>
					<?php endif; ?>

					<?php if ( isset($folder["id"]) && $folder["id"] === "design_sets" ) : ?>

						<div class="ct-add-component-button" ng-if="iframeScope.isDev()" ng-click="iframeScope.showCreateDesignSet()">
							<div class="ct-add-component-icon">
								<span class="ct-icon"></span>
							</div>
							<?php echo "Add Design Set..."; ?>
						</div>

					<?php endif; ?>

				<?php endif; ?>
			</div>

			<?php $this->output_folders_content( isset($folder["children"])?$folder["children"]:null, $slug, isset($folder["name"])?$folder["name"]:null, $path, $depth ); ?>

		<?php endforeach;

	}


	/**
	 * Components Browser tabs
	 *
	 * @since 0.2.3
	 */

	function components_list() { ?>

		<!-- Basics -->
		<?php ob_start() ; ?>
		<div class='oxygen-add-section-accordion-contents'
			ng-if="isShowTab('components','fundamentals')">

			<?php 
				ob_start();
				do_action("oxygen_basics_components_containers"); 
				$output = ob_get_clean();
				if (strpos($output,"oxygen-add-section-element")!==false) { ?>
					<h2><?php _e("Containers", "oxygen");?></h2>
					<?php echo $output;
				}
			?>
			
			<?php 
				ob_start();
				do_action("oxygen_basics_components_text"); 
				$output = ob_get_clean();
				if (strpos($output,"oxygen-add-section-element")!==false) { ?>
					<h2><?php _e("Text", "oxygen");?></h2>
					<?php echo $output;
				}
			?>
			
			<?php 
				ob_start();
				do_action("oxygen_basics_components_links"); 
				$output = ob_get_clean();
				if (strpos($output,"oxygen-add-section-element")!==false) { ?>
					<h2><?php _e("Links", "oxygen");?></h2>
					<?php echo $output;
				}
			?>
			
			<?php 
				ob_start();
				do_action("oxygen_basics_components_visual"); 
				$output = ob_get_clean();
				if (strpos($output,"oxygen-add-section-element")!==false) { ?>
					<h2><?php _e("Visual", "oxygen");?></h2>
					<?php echo $output;
				}
			?>
			
			<?php 
				ob_start();
				do_action("ct_toolbar_fundamentals_list"); 
				$output = ob_get_clean();
				if (strpos($output,"oxygen-add-section-element")!==false) { ?>
					<h2><?php _e("Other", "oxygen");?></h2>
					<?php echo $output;
				}
			?>
		</div>
		<?php $output = ob_get_clean(); 
		if (strpos($output,"oxygen-add-section-element")!==false) { ?>
			<div class='oxygen-add-section-accordion'
				ng-click="switchTab('components', 'fundamentals');"
				ng-hide="iframeScope.hasOpenFolders()">
				<?php _e("Basics", "oxygen") ?>
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dropdown-arrow.svg'/>
			</div>
			<?php echo $output;
		}
		?>
		<!-- /Basics -->

		<!-- Helpers -->
		<?php ob_start() ; ?>
		<div class='oxygen-add-section-accordion-contents oxygen-add-section-accordion-contents-toppad'
			ng-if="isShowTab('components','smart')">
			
			<?php 
				ob_start();
				do_action("oxygen_helpers_components_composite"); 
				$output = ob_get_clean();
				if (strpos($output,"oxygen-add-section-element")!==false) { ?>
					<h2><?php _e("Composite", "oxygen");?></h2>					
					<?php echo $output;
				}
			?>
			
			<?php 
				ob_start();
				do_action("oxygen_helpers_components_dynamic"); 
				$output = ob_get_clean();
				if (strpos($output,"oxygen-add-section-element")!==false) { ?>
					<h2><?php _e("Dynamic", "oxygen");?></h2>					
					<?php echo $output;
				}
			?>
			
			<?php 
				ob_start();
				do_action("oxygen_helpers_components_interactive"); 
				$output = ob_get_clean();
				if (strpos($output,"oxygen-add-section-element")!==false) { ?>
					<h2><?php _e("Interactive", "oxygen");?></h2>					
					<?php echo $output;
				}
			?>
			
			<?php 
				ob_start();
				do_action("oxygen_helpers_components_external"); 
				$output = ob_get_clean();
				if (strpos($output,"oxygen-add-section-element")!==false) { ?>
					<h2><?php _e("External", "oxygen");?></h2>					
					<?php echo $output;
				}
			?>
		</div>
		<?php $output = ob_get_clean(); 
		if (strpos($output,"oxygen-add-section-element")!==false) { ?>
			<div class='oxygen-add-section-accordion'
				ng-click="switchTab('components', 'smart');"
				ng-hide="iframeScope.hasOpenFolders()">
				<?php _e("Helpers", "oxygen") ?>
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dropdown-arrow.svg'/>
			</div>
			<?php echo $output;
		}
		?>
		<!-- /Helpers -->

		<!-- WordPress -->
		<?php ob_start() ; ?>
		<?php $this->output_folders_content( array(
												"wordpress" => array(
													"name" 	=> "WordPress",
													"children" => array(
														array(
															"name" 	=> "Dynamic Data",
															"id" 	=> "data" ),
														array(
															"name" 	=> "Widgets",
															"id" 	=> "widgets" ),
														array(
															"name" 	=> "Sidebars",
															"id" 	=> "sidebars" )
													)
												)
											) , "", "" ); ?>
		<?php $output = ob_get_clean(); 
		if (strpos($output,"oxygen-add-section-element")!==false) { 
			echo $output; 
		} ?>
		<!-- /WordPress -->

		<?php do_action("oxygen_add_plus_sections"); ?>

		<!-- Library -->
		<?php 
		if (oxygen_vsb_user_can_use_design_library()) {
			$this->output_folders_content( $this->folders, "", "");
		}
		?>
		<!-- /Library -->

		<!-- Reusable -->
		<?php if (oxygen_vsb_user_can_use_reusable_parts()) :?>
		<div class='oxygen-add-section-accordion'
			ng-click="switchTab('components', 'reusable_parts');"
			ng-hide="iframeScope.hasOpenFolders()">
			<?php _e("Reusable", "oxygen") ?>
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dropdown-arrow.svg'/>
		</div>
		<div class='oxygen-add-section-accordion-contents oxygen-add-section-accordion-contents-toppad'
			ng-if="isShowTab('components','reusable_parts')">
			<?php do_action("ct_toolbar_reusable_parts"); ?>
		</div>
		<?php endif; ?>
		<!-- /Reusable -->

	<?php }

	/**
	 * Components Browser tabs, all components for client side search
	 *
	 * @since whenever
	 */

	function components_list_searchable() { ?>
		<?php do_action("oxygen_basics_components_containers"); ?>
		<?php do_action("oxygen_basics_components_text"); ?>
		<?php do_action("oxygen_basics_components_links"); ?>
		<?php do_action("oxygen_basics_components_visual"); ?>
		<?php do_action("ct_toolbar_fundamentals_list"); ?>
		<?php do_action("oxygen_helpers_components_composite"); ?>
		<?php do_action("oxygen_helpers_components_dynamic"); ?>
		<?php do_action("oxygen_helpers_components_interactive"); ?>
		<?php do_action("oxygen_helpers_components_external"); ?>
		<?php do_action("oxy_folder_wordpress_components"); ?>
		<?php do_action("ct_toolbar_data_folder") ?>
		<?php do_action("ct_toolbar_sidebars_folder"); ?>
		<?php do_action("ct_toolbar_widgets_folder"); ?>
		<?php do_action("ct_toolbar_reusable_parts"); ?>

		<?php do_action("oxygen_add_plus_searchable_list"); ?>
	<?php }


	/**
	 * Add all "Re-usable parts" to Components browser
	 *
	 * @since  0.2.3
	 */

	function ct_reusable_parts() {

		if (!oxygen_vsb_current_user_can_full_access()&&!oxygen_vsb_user_can_use_reusable_parts()) {
			return;
		}

		// Get all archive templates
		$args = array(
			'posts_per_page'	=> -1,
			'orderby' 			=> 'date',
			'order' 			=> 'DESC',
			'post_type' 		=> 'ct_template',
			'post_status' 		=> 'publish',
			'meta_key'   		=> 'ct_template_type',
			'meta_value' 		=> 'reusable_part'
		);

		$templates = new WP_Query( $args );

		foreach ( $templates->posts as $template ) : ?>

			<div class="oxygen-add-section-element oxy-reusable-button"
				data-searchid="__reusable_<?php echo esc_attr( $template->ID ); ?>"
				data-searchname="<?php echo esc_attr( $template->post_title ); ?>"
				data-searchcat="Reusable">
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/reusable.svg">
				<?php echo esc_html( $template->post_title ); ?>
				<div class="oxygen-add-section-element-options">
					<div class="oxygen-add-section-element-option" title="<?php _e("Add Re-usable part as single component", "oxygen")?>"
						ng-click="iframeScope.loadReusablePart(<?php echo esc_attr( $template->ID ); ?>)">
						<?php _e("Single", "oxygen"); ?>
					</div>
					<div class="oxygen-add-section-element-option" title="<?php _e("Add Re-usable part as editable fundamentals", "oxygen")?>"
						ng-click="iframeScope.loadReusablePart(<?php echo esc_attr( $template->ID ); ?>, iframeScope.component.active.id)">
						<?php _e("Editable", "oxygen"); ?>
					</div>
				</div>
			</div>	

		<?php endforeach;

	}


	/**
	 * Output .measure-type-select element
	 *
	 * @since 0.3.0
	 */

	static public function measure_type_select_layers($option, $param = 'layer', $types = "px,%,em,auto") {

		$types = explode(",", $types);

		?>

		<div class="ct-measure-type-select">
			<?php if (in_array("px", $types)) : ?>
			<div class="ct-button ct-measure-type-option"
				ng-click="<?php echo $param; ?>['<?php echo esc_attr( $option ); ?>-unit'] = 'px'; setOptionForBGLayers()"
				ng-class="{'ct-active':<?php echo $param; ?>['<?php echo esc_attr( $option ); ?>-unit'] =='px'}">
				<span class="ct-bullet"></span> PX
			</div>
			<?php endif; ?>
			<?php if (in_array("%", $types)) : ?>
			<div class="ct-button ct-measure-type-option"
				ng-click="<?php echo $param; ?>['<?php echo esc_attr( $option ); ?>-unit'] = '%'; setOptionForBGLayers()"
				ng-class="{'ct-active':<?php echo $param; ?>['<?php echo esc_attr( $option ); ?>-unit'] =='%'}">
				<span class="ct-bullet"></span> &#37;
			</div>
			<?php endif; ?>
			<?php if (in_array("em", $types)) : ?>
			<div class="ct-button ct-measure-type-option"
				ng-click="<?php echo $param; ?>['<?php echo esc_attr( $option ); ?>-unit'] = 'em'; setOptionForBGLayers()"
				ng-class="{'ct-active':<?php echo $param; ?>['<?php echo esc_attr( $option ); ?>-unit'] =='em'}">
				<span class="ct-bullet"></span> EM
			</div>
			<?php endif; ?>
			<?php if (in_array("auto", $types)) : ?>
			<div class="ct-button ct-measure-type-option"
				ng-click="<?php echo $param; ?>['<?php echo esc_attr( $option ); ?>-unit'] = 'auto'; setOptionForBGLayers()"
				ng-class="{'ct-active':<?php echo $param; ?>['<?php echo esc_attr( $option ); ?>-unit'] =='auto'}">
				<span class="ct-bullet"></span> <?php _e("Auto", "oxygen"); ?>
			</div>
			<?php endif; ?>
		</div>

	<?php }


	/**
	 * Output .measure-type-select element
	 *
	 * @since 0.3.0
	 */

	static public function measure_type_select($option, $types = "px,%,em,auto,vw,vh") {

		if ( $types === "" || $types === NULL ) {
			$types = "px,%,em,auto,vw,vh";
		}

		$types = explode(",", $types);

		?>

		<div class="oxygen-measure-box-units">
			<?php if (in_array("px", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'px')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='px'}">
				px
			</div>
			<?php endif; ?>
			<?php if (in_array("%", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', '%')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='%'}">
				&#37;
			</div>
			<?php endif; ?>
			<?php if (in_array("em", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'em')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='em'}">
				em
			</div>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'rem')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='rem'}">
				rem
			</div>
			<?php endif; ?>
			<?php if (in_array("auto", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'auto')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='auto'}">
				<?php _e("auto", "oxygen"); ?>
			</div>
			<?php endif; ?>
			<?php if (in_array("vw", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'vw')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='vw'}">
				vw
			</div>
			<?php endif; ?>
			<?php if (in_array("vh", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'vh')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='vh'}">
				vh
			</div>
			<?php endif; ?>
			<?php if (in_array("fr", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'fr')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='fr'}">
				fr
			</div>
			<?php endif; ?>
			<?php if (in_array("seconds", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'seconds')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='seconds'}">
				sec
			</div>
			<?php endif; ?>
			<?php if (in_array("milliseconds", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'milliseconds')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='milliseconds'}">
				ms
			</div>
			<?php endif; ?>
			<?php if (in_array("minutes", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'minutes')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='minutes'}">
				mins
			</div>
			<?php endif; ?>
			<?php if (in_array("hours", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'hours')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='hours'}">
				hrs
			</div>
			<?php endif; ?>
			<?php if (in_array("days", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'days')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='days'}">
				days
			</div>
			<?php endif; ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', ' ')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')==' '}">
				none
			</div>
		</div>

	<?php }


	/**
	 * Output .measure-type-select element for Page/Global settings
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	static public function global_measure_type_select($context, $option, $types = "px,%,em,auto,vw,vh") {

		if ( $types === "" || $types === NULL ) {
			$types = "px,%,em,auto,vw,vh";
		}

		$types = explode(",", $types);

		?>

		<div class="oxygen-measure-box-units">
			<?php if (in_array("px", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>', 'px')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>')=='px'}">
				px
			</div>
			<?php endif; ?>
			<?php if (in_array("%", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>', '%')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>')=='%'}">
				&#37;
			</div>
			<?php endif; ?>
			<?php if (in_array("em", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>', 'em')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>')=='em'}">
				em
			</div>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>', 'rem')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>')=='rem'}">
				rem
			</div>
			<?php endif; ?>
			<?php if (in_array("auto", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>', 'auto')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>')=='auto'}">
				<?php _e("auto", "oxygen"); ?>
			</div>
			<?php endif; ?>
			<?php if (in_array("vw", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>', 'vw')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>')=='vw'}">
				vw
			</div>
			<?php endif; ?>
			<?php if (in_array("vh", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>', 'vh')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>')=='vh'}">
				vh
			</div>
			<?php endif; ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>', ' ')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>')==' '}">
				<?= __("none", "oxygen") ?>
			</div>
		</div>

	<?php }

    static public function global_measure_box_unit_selector($context, $option, $units = "", $custom = true) {
        if ($units === null) return;
        
        if ($units === "") {
            $units = 'px,%,em,auto,vw,vh';
        }

        // Auto add rem unit with em units is present
        if (strpos($units, 'em') !== false) {
            $units = str_replace('em', 'em,rem', $units);
        }

        if (strpos($units, ",")) {
            $units = explode(',', $units);
        }
        ?>
        <div class="oxygen-measure-box-unit-selector" ng-class="{'oxygen-measure-box-unit-none':iframeScope.getGlobalOptionUnit('<?= $context ?>', '<?= $option ?>')==' '}">
            <?php if (is_array($units)): ?>
            <div class="oxygen-measure-box-selected-unit">{{iframeScope.getGlobalOptionUnitLabel('global', '<?= $option ?>')}}</div>
            <div class="oxygen-measure-box-units">
                <?php foreach ($units as $unit): ?>
                <div class="oxygen-measure-box-unit" 
                    ng-click="iframeScope.setGlobalOptionUnit('<?= $context ?>', '<?= $option ?>', '<?= $unit ?>')"
                    ng-class="{'oxygen-measure-box-unit-active':iframeScope.getGlobalOptionUnit('<?= $context ?>', '<?= $option ?>')=='<?= $unit ?>'}">
                    <?= $unit ?>
                </div>
                <?php endforeach ?>
                <?php if ($custom): ?>
                <div class="oxygen-measure-box-unit" 
                    ng-click="iframeScope.setGlobalOptionUnit('<?= $context ?>', '<?= $option ?>', ' ')"
                    ng-class="{'oxygen-measure-box-unit-active':iframeScope.getGlobalOptionUnit('<?= $context ?>', '<?= $option ?>')==' '}">
                    <?= __("none", "oxygen") ?>
                </div>
                <?php endif ?>
            </div>
            <?php else: ?>
            <div class="oxygen-measure-box-selected-unit"><?= $units ?></div>
            <?php endif ?>
        </div>
        <?php
    }

	/**
	 * Output .oxygen-measure-box-options element
	 *
	 * @since 0.3.0
	 */

	function measure_box_options( $option, $units = "" ) { ?>

		<div class="oxygen-measure-box-options">

			<label>
			<?php /*	<input class="oxygen-apply-opposite-trigger" type="radio" name="<?php echo esc_attr( $option ); ?>_measure"
					data-option="<?php echo esc_attr( $option ); ?>"
					data-opposite-option="<?php echo $opposite_option; ?>"/>
				<span><?php echo $text ?></span>*/ ?>
			</label>

			<div class='oxygen-measure-box'
				ng-class="{'oxygen-measure-box-unit-auto':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='auto'}">
				<input type='text' type="text" spellcheck="false"
					<?php $this->ng_attributes($option); ?>/>
				<div class='oxygen-measure-box-unit-selector' ng-class="{'oxygen-measure-box-unit-none':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')==' '}">
					<div class='oxygen-measure-box-selected-unit'>{{iframeScope.getOptionUnitLabel('<?php echo esc_attr( $option ); ?>')}}</div>
					<?php $this->measure_type_select($option, $units); ?>
				</div>
			</div>

			<label>
				<input class="oxygen-apply-all-trigger" type="radio" name="<?php echo esc_attr( $option ); ?>_measure"
					data-option="<?php echo esc_attr( $option ); ?>"/>
				<span><?php _e("Apply All", "oxygen"); ?></span>
			</label>

		</div>
	<?php }


	/**
	 * Output button list single button
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function button_list_button($option, $value, $value_nice_name = false, $class = "", $callback="") { ?>

		<label class='oxygen-button-list-button <?php echo esc_attr($class); ?>'
			ng-class="{'oxygen-button-list-button-active':iframeScope.getOption('<?php echo esc_attr( $option ); ?>')=='<?php echo esc_attr($value); ?>','oxygen-button-list-button-default':iframeScope.isInherited(iframeScope.component.active.id,'<?php echo esc_attr( $option ); ?>','<?php echo esc_attr($value); ?>')==true}">
				<input type="radio" name="<?php echo esc_attr( $option ); ?>" value="<?php echo esc_attr($value); ?>"
					<?php $this->ng_attributes($option, 'model,change', array('change'=>$callback)); ?>
					ng-click="radioButtonClick(iframeScope.component.active.name, '<?php echo esc_attr( $option ); ?>', '<?php echo esc_attr($value); ?>')"/>
				<?php echo ( $value_nice_name ) ? esc_html($value_nice_name) : esc_html($value); ?>
		</label>
	<?php }


	/**
	 * Output button list single button for Page/Global settings
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	function global_button_list_button($context, $option, $value, $value_nice_name = false, $class = "") { 

		// parse $option if passed as ['parent']['child']
		if (strpos($option,"']['")) {
			// remove first ['
			$parsed_option = substr($option, 2);
			// remove last ']
			$parsed_option = substr($parsed_option, 0, -2);
			// split into parts
			$parsed_option = explode("']['", $parsed_option);
			// get option
			$last_option = array_pop($parsed_option);
			// combine the object back
			$option_obj = "['".implode("']['", $parsed_option)."']";
		}
		else {
			$last_option = $option;
			$option_obj = "";
		}

		?>

		<label class='oxygen-button-list-button <?php echo $class; ?>'
			<?php if ($context == 'page') : ?>
			ng-class="{'oxygen-button-list-button-active':iframeScope.pageSettingsMeta<?php echo esc_attr( $option ); ?>=='<?php echo $value; ?>','oxygen-button-list-button-default':!iframeScope.pageSettingsMeta<?php echo esc_attr( $option ); ?>&&(iframeScope.pageSettings<?php echo esc_attr( $option ); ?>=='<?php echo $value; ?>'||iframeScope.globalSettings<?php echo esc_attr( $option ); ?>=='<?php echo $value; ?>')}"
			<?php elseif ($context == 'global') : ?>
			ng-class="{'oxygen-button-list-button-active':iframeScope.globalSettings<?php echo esc_attr( $option ); ?>=='<?php echo $value; ?>'}"
			<?php endif; ?>
			>
				<input type="radio" name="<?php echo esc_attr( $option ); ?>" value="<?php echo $value; ?>"
					<?php $this->global_ng_attributes($context, $option, 'model,change'); ?>
					<?php if ($context == 'page') : ?>
					ng-click="globalSettingsRadioButtonClick(iframeScope.pageSettingsMeta<?php echo esc_attr( $option_obj ); ?>, '<?php echo $last_option ?>', '<?php echo $value; ?>')"
					<?php elseif ($context == 'global') : ?>
					ng-click="globalSettingsRadioButtonClick(iframeScope.globalSettings<?php echo esc_attr( $option_obj ); ?>, '<?php echo $last_option ?>', '<?php echo $value; ?>')"
					<?php endif; ?>/>
				<?php echo ( $value_nice_name ) ? $value_nice_name : $value; ?>
		</label>
	<?php }


	/**
	 * Output icon button list single button
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function icon_button_list_button($option, $value, $icon, $icon_active = false, $label = false, $ng_click = "") { ?>

		<label class='oxygen-icon-button-list-option'
			ng-class="{'oxygen-icon-button-list-option-active':iframeScope.getOption('<?php echo esc_attr( $option ); ?>')=='<?php echo esc_attr($value); ?>','oxygen-icon-button-list-button-default':iframeScope.isInherited(iframeScope.component.active.id,'<?php echo esc_attr( $option ); ?>','<?php echo esc_attr($value); ?>')==true}">
				<div class="oxygen-icon-button-list-option-icon-wrapper">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/<?php echo esc_attr($icon); ?>' />
					<input type="radio" name="<?php echo esc_attr( $option ); ?>" value="<?php echo esc_attr($value); ?>"
						<?php $this->ng_attributes($option, 'model,change'); ?>
						ng-click="radioButtonClick(iframeScope.component.active.name, '<?php echo esc_attr( $option ); ?>', '<?php echo esc_attr($value); ?>');<?php echo $ng_click; ?>"/>
					<?php if ( $icon_active ) : ?>
						<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/<?php echo esc_attr($icon_active); ?>' />
					<?php endif; ?>
				</div>
				<?php if ( $label ) : ?>
				<div class='oxygen-icon-button-list-option-label'>
					<?php echo esc_html($label); ?>
				</div>
				<?php endif; ?>
		</label>

	<?php }


	/**
	 * Output measure box
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function measure_box($option,$units="",$with_options = false, $default = true, $attributes = false, $show_style_indicator = false, $placeholder = false) { 

		if ($default) {
			$default_class = ",'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '".esc_attr( $option )."')";
		}
		else {
			$default_class = "";
		}

		if ($show_style_indicator) {
			$indicator_ng_class = ",'oxygen-has-class-value':iframeScope.classHasOption('".esc_attr( $option )."')&&!IDHasOption('".esc_attr( $option )."'),'oxygen-has-id-value':iframeScope.IDHasOption('".esc_attr( $option )."')";
			$indicator_class = "oxy-measure-box-styling-indicator";
		}
		else {
			$indicator_ng_class = "";
			$indicator_class = "";
		}

		if( $placeholder ) {
			$placeholder = 'placeholder="' . $placeholder . '"';
		} 
		else {
			$placeholder = null;
		}

		?>

		<div class='oxygen-measure-box oxygen-measure-box-option-<?php echo esc_attr( $option ); ?> <?php echo $indicator_class; ?>'
			ng-class="{'oxygen-measure-box-unit-auto':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='auto'<?php echo $default_class; ?><?php echo $indicator_ng_class; ?>, 'oxygen-measure-box-unit-none':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')==' '}">
			<input 
			type="text" 
			spellcheck="false"
			data-option="<?php echo esc_attr( $option ); ?>"
			<?= $placeholder; ?>
			<?php if ($attributes) $this->ng_attributes($option,$attributes); else $this->ng_attributes($option);?>/>
			<div class='oxygen-measure-box-unit-selector' ng-class="{'oxygen-measure-box-unit-none':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')==' '}">
				<?php if (strpos($units, ",")===false&&strlen($units)>0) : ?>
					<div class='oxygen-measure-box-selected-unit'><?php echo esc_html($units); ?></div>
				<?php else: ?>
					<div class='oxygen-measure-box-selected-unit'>{{iframeScope.getOptionUnitLabel('<?php echo esc_attr( $option ); ?>')}}</div>
					<?php self::measure_type_select($option,$units); ?>
				<?php endif; ?>
			</div>
		</div>

	<?php }


	/**
	 * Output slider measure box with label
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function slider_measure_box_with_wrapper($option,$label,$units="",$min=0,$max=100,$default=true,$step=false) { ?>

		<div class='oxygen-control-wrapper'>
			<?php $not_css = apply_filters( "ct_not_css_options", array() ); ?>
			<?php if ( !in_array($option, $not_css) ) {?>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $option; ?>')&&!IDHasOption('<?php echo $option; ?>'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $option; ?>')}">
			</div>
			<?php } ?>
			<label class='oxygen-control-label'><?php echo esc_html($label); ?></label>
			<div class='oxygen-control'>
				<?php self::slider_measure_box($option,$units,$min,$max,$default,$step); ?>
			</div>
		</div>

	<?php }

	/**
	 * Output textarea-like contenteditable div
	 *
	 * @since 3.7
	 * @author Gagan S Goraya.
	 */

	function textarea($option, $label) { 
		?>

		<div class='oxygen-control-wrapper'>
			<label class='oxygen-control-label'><?php echo esc_html($label); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-input textarea'>
					<div spellcheck="false" contenteditable="true" data-disabledynamic="true"
						<?php $this->ng_attributes($option); ?>>
					</div>
				</div>
			</div>
		</div>

	<?php }


	/**
	 * Output slider with label
	 *
	 * @since 3.6
	 * @author Ilya K.
	 */

	function slider_with_wrapper($option,$label,$min=0,$max=100,$default=true,$step=false) { ?>

		<div class='oxygen-control-wrapper'>
			<?php $not_css = apply_filters( "ct_not_css_options", array() ); ?>
			<?php if ( !in_array($option, $not_css) ) {?>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $option; ?>')&&!IDHasOption('<?php echo $option; ?>'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $option; ?>')}">
			</div>
			<?php } ?>
			<label class='oxygen-control-label'><?php echo esc_html($label); ?></label>
			<div class='oxygen-control'>
				<?php self::slider($option,$min,$max,$default,$step); ?>
			</div>
		</div>

	<?php }


	/**
	 * Output slider measure box with label for Page/Global settings
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	function global_slider_measure_box_with_wrapper($context,$option,$label,$units="",$min=0,$max=100,$default=true,$step=false) { ?>

		<div class='oxygen-control-wrapper'>
			<label class='oxygen-control-label'><?php echo esc_html($label); ?></label>
			<div class='oxygen-control'>
				<?php self::global_slider_measure_box($context,$option,$units,$min,$max,$default,$step); ?>
			</div>
		</div>

	<?php }


	/**
	 * Output measure box with slider
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function slider_measure_box($option,$units="",$min=0,$max=100,$default=true,$step=false) { 

		if ($default) {
			$default_class = ",'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '".esc_attr( $option )."')";
		}
		else {
			$default_class = "";
		}

		?>

		<div class="oxygen-slider-measure-box"
			ng-class="{'oxygen-measure-box-unit-auto':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='auto','oxygen-measure-box-unit-none':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')==' '<?php echo esc_attr($default_class); ?>}">
			<div class="oxygen-control">
				<?php self::measure_box($option, $units, false, $default); ?>
			</div>
			<div class="oxygen-measure-box-slider">
				<input type="range" 
					oxy-range-fix
					min="<?php echo ($min!==null&&$min!=='') ? esc_attr($min) : 0; ?>" 
					max="<?php echo ($max!==null&&$max!=='') ? esc_attr($max) : 100; ?>" 
					<?php echo ($step!==null&&$step!=='') ?  "step=\"".esc_attr($step)."\"": ""; ?>
					<?php $this->ng_attributes($option); ?> ng-disabled="iframeScope.getOptionUnit('<?= esc_attr( $option ) ?>')==' '">
			</div>
		</div>

	<?php }


	/**
	 * Output plain value slider
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function slider($option,$min=0,$max=100,$default=true,$step=false) { 

		if ($default) {
			$default_class = ",'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '".esc_attr( $option )."')";
		}
		else {
			$default_class = "";
		}

		?>

		<div class="oxygen-slider-measure-box"
			ng-class="{'oxygen-measure-box-unit-auto':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='auto','oxygen-measure-box-unit-none':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')==' '<?php echo esc_attr($default_class); ?>}">
			<div class='oxygen-measure-box oxygen-measure-box-option-<?php echo esc_attr( $option ); ?>'
				ng-class="{'oxygen-measure-box-unit-auto':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='auto'<?php echo $default_class; ?>}">
				<input type="text" spellcheck="false"
					data-option="<?php echo esc_attr( $option ); ?>"
					<?php $this->ng_attributes($option);?>/>
			</div>
			<div class="oxygen-measure-box-slider">
				<input type="range" 
					oxy-range-fix
					min="<?php echo ($min!==null&&$min!=='') ? esc_attr($min) : 0; ?>" 
					max="<?php echo ($max!==null&&$max!=='') ? esc_attr($max) : 100; ?>" 
					<?php echo ($step!==null&&$step!=='') ?  "step=\"".esc_attr($step)."\"": ""; ?>
					<?php $this->ng_attributes($option); ?> ng-disabled="iframeScope.getOptionUnit('<?= esc_attr( $option ) ?>')==' '">
			</div>
		</div>

	<?php }


	/**
	 * Output measure box with slider for Page/Global settings
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	function global_slider_measure_box($context,$option,$units="",$min=0,$max=100,$default=false,$step=false) { 

		if ($default) {
			$default_class = ",'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '".esc_attr( $option )."')";
		}
		else {
			$default_class = "";
		}

		?>

		<div class="oxygen-slider-measure-box"
			<?php if (strpos($units, ",")===false&&strlen($units)>0) : ?>
			>
			<?php else : ?>
			ng-class="{'oxygen-measure-box-unit-auto':iframeScope.getGlobalOptionUnit('<?php echo esc_attr( $option ); ?>')=='auto'<?php echo $default_class; ?>}">
			<?php endif; ?>
			<?php self::global_measure_box($context, $option, $units, false, $default); ?>
			<div class="oxygen-measure-box-slider">
				<input type="range" 
					oxy-range-fix
					min="<?php echo ($min!==null&&$min!=='') ? $min : 0; ?>" 
					max="<?php echo ($max!==null&&$max!=='') ? $max : 100; ?>" 
					<?php echo ($step!==null&&$step!=='') ?  "step=\"$step\"": ""; ?>" 
					<?php $this->global_ng_attributes($context, $option); ?>>
			</div>
		</div>

	<?php }


	/**
	 * Output measure box with label
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function measure_box_with_wrapper($option,$label,$units="") { ?>

		<div class='oxygen-control-wrapper'>
			<?php $not_css = apply_filters( "ct_not_css_options", array() ); ?>
			<?php if ( !in_array($option, $not_css) ) {?>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $option; ?>')&&!IDHasOption('<?php echo $option; ?>'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $option; ?>')}">
			</div>
			<?php } ?>
			<label class='oxygen-control-label'><?php echo $label; ?></label>
			<div class='oxygen-control'>
				<?php self::measure_box($option,$units); ?>
			</div>
		</div>

	<?php }


	/**
	 * Output measure box with label
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	function global_measure_box_with_wrapper($context,$option,$label,$units="") { ?>

		<div class='oxygen-control-wrapper'>
			<label class='oxygen-control-label'><?php echo $label; ?></label>
			<div class='oxygen-control'>
				<?php self::global_measure_box($context,$option,$units); ?>
			</div>
		</div>

	<?php }


	/**
	 * Output measure box for Page/Global settings
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	function global_measure_box($context, $option, $units="", $with_options=false, $default=false, $attributes=false) { 

		if ($default) {
			$default_class = ",'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '".esc_attr( $option )."')";
		}
		else {
			$default_class = "";
		}

		?>

		<div class='oxygen-measure-box'
			<?php if (strpos($units, ",")===false&&strlen($units)>0) : ?>
			>
			<?php else : ?>
			ng-class="{'oxygen-measure-box-unit-auto':iframeScope.getGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>')=='auto'<?php echo $default_class; ?>}"
			<?php endif; ?>
			<input type="text" spellcheck="false"
				<?php if ($context == "page") : ?>
					placeholder="{{iframeScope.pageSettings<?php echo $option; ?>||iframeScope.globalSettings<?php echo $option; ?>}}"
				<?php elseif ($context == "global") : ?>
				<?php endif; ?>
				<?php if ($attributes) $this->global_ng_attributes($context,$option,$attributes); else $this->global_ng_attributes($context,$option);?>/>
			<div class='oxygen-measure-box-unit-selector'>
				<?php if (strpos($units, ",")===false&&strlen($units)>0) : ?>
					<div class='oxygen-measure-box-selected-unit'><?php echo $units; ?></div>
				<?php else: ?>
					<div class='oxygen-measure-box-selected-unit'>{{iframeScope.getGlobalOptionUnit('<?php echo $context; ?>','<?php echo esc_attr( $option ); ?>')}}</div>
					<?php self::global_measure_type_select($option,$units); ?>
				<?php endif; ?>
			</div>
			<?php if ($with_options) : ?>
				<?php //$this->measure_box_options($option,$units); ?>
			<?php endif; ?>
		</div>

	<?php }


	/**
	 * Output font family dropdown
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function font_family_dropdown($option = false, $hide_wrapper = false) { 

		if (!$option) {
			$option = 'font-family';
		} else {
			$option = esc_attr($option);
		}

		?>

		<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $option; ?>')&&!IDHasOption('<?php echo $option; ?>'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $option; ?>')}">
			</div>
			<label class='oxygen-control-label'><?php _e("Font Family","oxygen"); ?></label>
			<div class='oxygen-control'>

				<div class="oxygen-select oxygen-select-box-wrapper">
					<div class="oxygen-select-box"
						ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '<?php echo $option; ?>')}">
						<div class="oxygen-select-box-current">{{iframeScope.getComponentFont(iframeScope.component.active.id, true, '', '<?php echo $option; ?>')}}</div>
						<div class="oxygen-select-box-dropdown"></div>
					</div>
					<div class="oxygen-select-box-options">

						<div class="oxygen-select-box-option">
							<input type="text" value="" placeholder="<?php _e("Search...", "oxygen"); ?>" spellcheck="false"
								ng-model="iframeScope.fontsFilter"/>
						</div>
						<div class="oxygen-select-box-option"
							ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, '', '<?php echo $option; ?>');"
							title="<?php _e("Unset font", "oxygen"); ?>">
								<?php _e("Default", "oxygen"); ?>
						</div>
						<div class="oxygen-select-box-option"
							ng-repeat="(name,font) in iframeScope.globalSettings.fonts"
							ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, ['global', name], '<?php echo $option; ?>');"
							title="<?php _e("Apply global font", "oxygen"); ?>">
								{{name}} ({{font}})
						</div>
						<div class="oxygen-select-box-option"
							ng-repeat="name in ['Inherit'] | filter:iframeScope.fontsFilter"
							ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, name, '<?php echo $option; ?>');"
							title="<?php _e("Use parent element font", "oxygen"); ?>">
								Inherit
						</div>
						<div class="oxygen-select-box-option"
							ng-hide="iframeScope.globalFontExist(name)"
							ng-repeat="name in iframeScope.elegantCustomFonts | filter:iframeScope.fontsFilter | limitTo: 20"
							ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, name, '<?php echo $option; ?>');"
							title="<?php _e("Apply this font family", "oxygen"); ?>">
								{{name}}
						</div>
						<div class="oxygen-select-box-option"
							ng-hide="iframeScope.globalFontExist(font.name)"
							ng-repeat="font in iframeScope.typeKitFonts | filter:iframeScope.fontsFilter | limitTo: 20"
							ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, font.slug, '<?php echo $option; ?>');"
							title="<?php _e("Apply this font family", "oxygen"); ?>">
								{{font.name}}
						</div>
						<div class="oxygen-select-box-option"
							ng-hide="iframeScope.globalFontExist(font)"
							ng-repeat="font in iframeScope.webSafeFonts | filter:iframeScope.fontsFilter | limitTo: 20"
							ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, font, '<?php echo $option; ?>');"
							title="<?php _e("Apply this font family", "oxygen"); ?>">
								{{font}}
						</div>
						<div class="oxygen-select-box-option"
							ng-hide="iframeScope.globalFontExist(font.family)"
							ng-repeat="font in iframeScope.googleFontsList | filter:iframeScope.fontsFilter | limitTo: 20"
							ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, font.family, '<?php echo $option; ?>');"
							title="<?php _e('Apply this font family', 'oxygen'); ?>">
								{{font.family}}
						</div>
					</div>
					<!-- .oxygen-select-box-options -->
				</div>
				<!-- .oxygen-select.oxygen-select-box-wrapper -->
			</div>
		</div>
		<!-- #oxygen-typography-font-family -->

	<?php }


	/**
	 * Output simple input textbox with wrapper and label
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function simple_input_with_wrapper($option,$label) { 
		
		$not_css = apply_filters( "ct_not_css_options", array() );

		?>

		<div class='oxygen-control-wrapper'>
			<?php if ( !in_array($option, $not_css) ) {?>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $option; ?>')&&!IDHasOption('<?php echo $option; ?>'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $option; ?>')}">
			</div>
			<?php } ?>
			<label class='oxygen-control-label'><?php echo esc_html($label); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-input'>
					<input type="text" spellcheck="false"
						<?php $this->ng_attributes($option); ?>/>
				</div>
			</div>
		</div>

	<?php }


	/**
	 * Output simple input textbox with wrapper and label for Page/Global Settings
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	function global_simple_input_with_wrapper($context,$option,$label) { ?>

		<div class='oxygen-control-wrapper'>
			<label class='oxygen-control-label'><?php echo $label; ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-input'>
					<input type="text" spellcheck="false"
						<?php if ($context == "page") : ?>
							placeholder="{{iframeScope.pageSettings<?php echo $option; ?>||iframeScope.globalSettings<?php echo $option; ?>}}"
						<?php endif; ?>
						<?php $this->global_ng_attributes($context,$option); ?>/>
				</div>
			</div>
		</div>

	<?php }


	/**
	 * Output simple input textbox with wrapper and label
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */


	function colorpicker_with_wrapper($option,$label = false,$id="",$ng_show="", $deepProperty = false, $wrapperClass=false) { ?>

		<div class='oxygen-control-wrapper<?php echo $wrapperClass?' '.esc_attr($wrapperClass):'';?>'<?php echo !empty($id)?"id='".esc_attr($id)."' ":''; ?><?php echo ($ng_show!="") ? 'ng-show="'.esc_attr($ng_show).'"' : ""; ?>>
		<?php $not_css = apply_filters( "ct_not_css_options", array() ); ?>
		<?php if ( !in_array($option, $not_css) ) {?>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $option; ?>')&&!IDHasOption('<?php echo $option; ?>'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $option; ?>')}">
			</div>
		<?php } ?>
		<?php if($label) { ?>
			<label class='oxygen-control-label'><?php echo esc_html($label); ?></label>
		<?php } ?>
			<div class='oxygen-control'>
				<?php self::colorpicker($option, $deepProperty); ?>
			</div>
		</div>

	<?php }


	/**
	 * Output simple input textbox with wrapper and label
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function colorpicker($option, $deepProperty = false) { ?>

		<div class='oxygen-color-picker'
			ng-class="{'oxygen-option-default':$parent.iframeScope.isInherited($parent.iframeScope.component.active.id, '<?php echo esc_attr( $option ); ?>')}">
			<div class="oxygen-color-picker-color">
				<input ctiriscolorpicker=""
					gradientindex="<?php echo $deepProperty?'$index':'';?>"
					class="ct-iris-colorpicker"
					 type="text" spellcheck="false"
					 <?php $this->ng_attributes($option, 'change'); ?>
					ng-model="$parent.iframeScope.component.options[$parent.iframeScope.component.active.id]['model']['<?php echo esc_attr( $option ); ?>']<?php echo $deepProperty?esc_attr($deepProperty):'';?>"
					ng-style="{'background-color':$parent.iframeScope.component.options[$parent.iframeScope.component.active.id]['model']['<?php echo esc_attr( $option ); ?>']<?php echo $deepProperty?esc_attr($deepProperty):'';?>}"/>
			</div>
			<input type="text" spellcheck="false"
				<?php $this->ng_attributes($option, 'change'); ?>
				ng-model="$parent.iframeScope.component.options[$parent.iframeScope.component.active.id]['model']['<?php echo esc_attr( $option ); ?>']<?php echo $deepProperty?esc_attr($deepProperty):'';?>"
				/>
			<img class="oxygen-add-global-color-icon" 
				title="<?php _e("Save as Global Color","oxygen"); ?>"
				src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/make-global-color.svg'
				ng-click="$parent.showAddNewColorDialog($event)"/>
		</div>

	<?php }


	/**
	 * Output mediaurl with wrapper and label
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function mediaurl_with_wrapper($option,$label,$id="") { ?>

		<div class='oxygen-control-wrapper' id='<?php echo esc_attr($id); ?>'>
			<?php $not_css = apply_filters( "ct_not_css_options", array() ); ?>
			<?php if ( !in_array($option, $not_css) ) {?>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $option; ?>')&&!IDHasOption('<?php echo $option; ?>'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $option; ?>')}">
			</div>
			<?php } ?>
			<label class='oxygen-control-label'><?php echo esc_html($label); ?></label>
			<div class='oxygen-control'>
				<?php self::mediaurl($option); ?>
			</div>
		</div>

	<?php }


	/**
	 * Output simple input textbox with wrapper and label
	 *
	 * @since 2.0
	 */

	function mediaurl($option, $attachment = false, $params = array()) {
		global $oxygen_meta_keys;
		?>

		<div class="oxygen-file-input">
			<input type="text" spellcheck="false"
				ng-change = "iframeScope.setOption(iframeScope.component.active.id,'ct_image','<?php echo esc_attr( $option ); ?>'); iframeScope.parseImageShortcode()"
				ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '<?php echo esc_attr( $option ); ?>')}"
				<?php if( $attachment ): ?>
				    ng-click="triggerBrowseButton($event);"
                    readonly
				<?php endif; ?>
				<?php $this->ng_attributes($option); ?>/>
			<div class="oxygen-file-input-browse"
				data-mediaTitle="Select Image" 
				data-mediaButton="Select Image" 
				data-mediaProperty="<?php echo esc_attr( $option ); ?>"
				data-mediaType="mediaUrl"
				data-returnValue="<?php echo $attachment ? 'id':'url';?>"><?php _e("browse","oxygen"); ?></div>
			<?php if( !$attachment ): ?>
			    <div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesImageMode" callback="iframeScope.insertShortcodeToImage">data</div>
			<?php endif; ?>
			<?php if(isset($params['dynamicdatacode'])) {
				echo $params['dynamicdatacode'];
			}?>
		</div>

	<?php }

	function selector($option) {
		?>
		<div class="oxygen-file-input">
			<input type="text" spellcheck="false"
				ng-change = "iframeScope.setOption(iframeScope.component.active.id,'ct_modal','<?php echo esc_attr( $option ); ?>');"
				ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '<?php echo esc_attr( $option ); ?>')}"
				<?php $this->ng_attributes($option); ?>/>
			<div class="oxygen-selector-browse" data-option="<?php echo esc_attr( $option ); ?>">
				<?php _e("choose","oxygen"); ?>
			</div>
		</div>
	<?php }

	function hyperlink($option, $param = array()) {
		?>
		<div class="oxygen-file-input"
			ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '<?php echo esc_attr( $option ); ?>')}">
			<input type="text" spellcheck="false"
				<?php $this->ng_attributes($option); ?>/>
			<div class="oxygen-set-link"
				data-linkproperty="<?php echo esc_attr( $option ); ?>" 
				data-linktarget="target"
				ng-click="processLink()"><?php _e("set","oxygen"); ?></div>
			<?php if(isset($param['dynamicdatacode'])) {
					echo $param['dynamicdatacode'];
			} ?>
		</div>
		<?php
	}


	/**
	 * Media queries list
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function media_queries_list_with_wrapper($option,$heading,$above=false,$always=false,$never=true) { ?>

		<div class="oxygen-control-row">
			<div class='oxygen-control-wrapper'>
				<?php $not_css = apply_filters( "ct_not_css_options", array() ); ?>
				<?php if ( !in_array($option, $not_css) ) {?>
				<div class="oxy-style-indicator"
					ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $option; ?>')&&!IDHasOption('<?php echo $option; ?>'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $option; ?>')}">
				</div>
				<?php } ?>
				<label class='oxygen-control-label'><?php echo esc_html($heading); ?></label>
				<div class='oxygen-control oxygen-special-property not-available-for-media not-available-for-classes'>
					<?php self::media_queries_list($option,$heading,$above=false,$always,$never) ?>
				</div>
			</div>
		</div>

	<?php }


	/**
	 * Media queries list for Pages/Global settings
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	function global_media_queries_list_with_wrapper($context,$option,$heading,$above=false,$always=false,$never=true) { ?>

		<div class="oxygen-control-row">
			<div class='oxygen-control-wrapper'>
				<label class='oxygen-control-label'><?php echo $heading; ?></label>
				<div class='oxygen-control oxygen-special-property not-available-for-media not-available-for-classes'>
					<?php self::global_media_queries_list($context,$option,$heading,$above,$always,$never) ?>
				</div>
			</div>
		</div>

	<?php }


	/**
	 * Media queries list
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function media_queries_list($option,$heading="",$above=false,$always=false,$never=true) { 

		$option = esc_attr($option);
		
		?>

		<div class="oxygen-select oxygen-select-box-wrapper">
			<div class="oxygen-select-box"
				ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '<?php echo $option; ?>')}">
				<div class="oxygen-select-box-current">{{iframeScope.getMediaTitle(iframeScope.getOption('<?php echo $option; ?>')<?php echo ($above) ? ", true" : ""?>)}}</div>
				<div class="oxygen-select-box-dropdown"></div>
			</div>
			<div class="oxygen-select-box-options">
				<?php if ($always&&!$above) : ?>
				<div class="oxygen-select-box-option" 
					ng-click="iframeScope.setOptionModel('<?php echo $option; ?>','always')"
					ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('<?php echo $option; ?>')=='always'}">
					<?php _e("Always","oxygen"); ?>
				</div>
				<?php endif; ?>
				<?php if ($never&&$above) : ?>
				<div class="oxygen-select-box-option" 
					ng-click="iframeScope.setOptionModel('<?php echo $option; ?>','never')"
					ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('<?php echo $option; ?>')=='never'}">
					<?php _e("Never","oxygen"); ?>
				</div>
				<?php endif; ?>
				<div class="oxygen-select-box-option" 
					ng-repeat="name in iframeScope.<?php echo ($above) ? "sortedMediaList(true)" : "sortedMediaList()" ?>"
					ng-if="name!='default'"
					ng-click="iframeScope.setOptionModel('<?php echo $option; ?>',name)"
					ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('<?php echo $option; ?>')==name}">
					{{iframeScope.getMediaTitle(name<?php echo ($above) ? ", true" : ""?>)}}
				</div>
				<?php if ($always&&$above) : ?>
				<div class="oxygen-select-box-option" 
					ng-click="iframeScope.setOptionModel('<?php echo $option; ?>','always')"
					ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('<?php echo $option; ?>')=='always'}">
					<?php _e("Always","oxygen"); ?>
				</div>
				<?php endif; ?>
				<?php if ($never&&!$above) : ?>
				<div class="oxygen-select-box-option" 
					ng-click="iframeScope.setOptionModel('<?php echo $option; ?>','never')"
					ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('<?php echo $option; ?>')=='never'}">
					<?php _e("Never","oxygen"); ?>
				</div>
				<?php endif; ?>
			</div>
		</div>

	<?php }


	/**
	 * Media queries list for Page/Global settings
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	function global_media_queries_list($context,$option,$heading="",$above=false,$always=false,$never=true) { ?>

		<?php if ($context=="page") : ?>
		<div class="oxygen-select oxygen-select-box-wrapper">
			<div class="oxygen-select-box"
				ng-class="{'oxygen-option-default':!iframeScope.pageSettingsMeta<?php echo esc_attr( $option ); ?>&&(iframeScope.pageSettings<?php echo esc_attr( $option ); ?>||iframeScope.globalSettings<?php echo esc_attr( $option ); ?>)}">
				<div class="oxygen-select-box-current">{{iframeScope.getMediaTitle(iframeScope.pageSettingsMeta<?php echo $option; ?>||iframeScope.pageSettings<?php echo $option; ?>||iframeScope.globalSettings<?php echo $option; ?><?php echo ($above) ? ", true" : ""?>)}}</div>
				<div class="oxygen-select-box-dropdown"></div>
			</div>
			<div class="oxygen-select-box-options">
				<div class="oxygen-select-box-option" 
					ng-click="iframeScope.pageSettingsMeta<?php echo $option; ?>=''">
					&nbsp;
				</div>
				<?php if ($always&&!$above) : ?>
				<div class="oxygen-select-box-option" 
					ng-click="iframeScope.pageSettingsMeta<?php echo $option; ?>='always'"
					ng-class="{'oxygen-select-box-option-active':iframeScope.pageSettingsMeta<?php echo $option; ?>=='always'}">
					<?php _e("Always","oxygen"); ?>
				</div>
				<?php endif; ?>
				<?php if ($never&&$above) : ?>
				<div class="oxygen-select-box-option" 
					ng-click="iframeScope.pageSettingsMeta<?php echo $option; ?>='never'"
					ng-class="{'oxygen-select-box-option-active':iframeScope.pageSettingsMeta<?php echo $option; ?>=='never'}">
					<?php _e("Never","oxygen"); ?>
				</div>
				<?php endif; ?>
				<div class="oxygen-select-box-option" 
					ng-repeat="name in iframeScope.<?php echo ($above) ? "sortedMediaList(true)" : "sortedMediaList()" ?>"
					ng-show="name!='default'"
					ng-click="$parent.iframeScope.pageSettingsMeta<?php echo $option; ?>=name"
					ng-class="{'oxygen-select-box-option-active':$parent.iframeScope.pageSettingsMeta<?php echo $option; ?>==name}">
					{{iframeScope.getMediaTitle(name<?php echo ($above) ? ", true" : ""?>)}}
				</div>
				<?php if ($always&&$above) : ?>
				<div class="oxygen-select-box-option" 
					ng-click="iframeScope.pageSettingsMeta<?php echo $option; ?>='always'"
					ng-class="{'oxygen-select-box-option-active':iframeScope.pageSettingsMeta<?php echo $option; ?>=='always'}">
					<?php _e("Always","oxygen"); ?>
				</div>
				<?php endif; ?>
				<?php if ($never&&!$above) : ?>
				<div class="oxygen-select-box-option" 
					ng-click="iframeScope.pageSettingsMeta<?php echo $option; ?>='never'"
					ng-class="{'oxygen-select-box-option-active':iframeScope.pageSettingsMeta<?php echo $option; ?>=='never'}">
					<?php _e("Never","oxygen"); ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php elseif ($context=="global") : ?>
		<div class="oxygen-select oxygen-select-box-wrapper">
			<div class="oxygen-select-box"
				ng-class="{'oxygen-option-default':false}">
				<div class="oxygen-select-box-current">{{iframeScope.getMediaTitle(iframeScope.globalSettings<?php echo $option; ?><?php echo ($above) ? ", true" : ""?>)}}</div>
				<div class="oxygen-select-box-dropdown"></div>
			</div>
			<div class="oxygen-select-box-options">
				<div class="oxygen-select-box-option" 
					ng-click="iframeScope.globalSettings<?php echo $option; ?>=''">
					&nbsp;
				</div>
				<?php if ($always&&!$above) : ?>
				<div class="oxygen-select-box-option" 
					ng-click="iframeScope.globalSettings<?php echo $option; ?>='always'"
					ng-class="{'oxygen-select-box-option-active':iframeScope.globalSettings<?php echo $option; ?>=='always'}">
					<?php _e("Always","oxygen"); ?>
				</div>
				<?php endif; ?>
				<?php if ($never&&$above) : ?>
				<div class="oxygen-select-box-option" 
					ng-click="iframeScope.globalSettings<?php echo $option; ?>='never'"
					ng-class="{'oxygen-select-box-option-active':iframeScope.globalSettings<?php echo $option; ?>=='never'}">
					<?php _e("Never","oxygen"); ?>
				</div>
				<?php endif; ?>
				<div class="oxygen-select-box-option" 
					ng-repeat="name in iframeScope.<?php echo ($above) ? "sortedMediaList(true)" : "sortedMediaList()" ?>"
					ng-show="name!='default'"
					ng-click="iframeScope.globalSettings<?php echo $option; ?>=name"
					ng-class="{'oxygen-select-box-option-active':iframeScope.globalSettings<?php echo $option; ?>==name}">
					{{iframeScope.getMediaTitle(name<?php echo ($above) ? ", true" : ""?>)}}
				</div>
				<?php if ($always&&$above) : ?>
				<div class="oxygen-select-box-option" 
					ng-click="iframeScope.globalSettings<?php echo $option; ?>='always'"
					ng-class="{'oxygen-select-box-option-active':iframeScope.globalSettings<?php echo $option; ?>=='always'}">
					<?php _e("Always","oxygen"); ?>
				</div>
				<?php endif; ?>
				<?php if ($never&&!$above) : ?>
				<div class="oxygen-select-box-option" 
					ng-click="iframeScope.globalSettings<?php echo $option; ?>='never'"
					ng-class="{'oxygen-select-box-option-active':iframeScope.globalSettings<?php echo $option; ?>=='never'}">
					<?php _e("Never","oxygen"); ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>
	<?php }


	/**
	 * Output checkbox with wrapper
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	function checkbox_with_wrapper($option, $label="", $true_val="", $false_val="") { ?>
	
		<div class="oxygen-control-row">
			<div class="oxygen-control-wrapper">
				<?php $not_css = apply_filters( "ct_not_css_options", array() ); ?>
				<?php if ( !in_array($option, $not_css) ) {?>
				<div class="oxy-style-indicator"
					ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $option; ?>')&&!IDHasOption('<?php echo $option; ?>'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $option; ?>')}">
				</div>
				<?php } ?>
				<?php $this->checkbox($option, $label, $true_val, $false_val) ?>
			</div>
		</div>

	<?php }

	
	/**
	 * Output checkbox setting
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	function checkbox($option, $label="", $true_val="", $false_val="") { ?>
	
		<label class="oxygen-checkbox">
			<input type="checkbox"
				ng-true-value="'<?php echo $true_val; ?>'" 
				ng-false-value="'<?php echo $false_val; ?>'"
				ng-click="checkBoxClick(iframeScope.component.active.name, '<?php echo $option; ?>', '<?php echo $true_val; ?>')"
				<?php $this->ng_attributes($option); ?>> 
			<div class='oxygen-checkbox-checkbox'
				ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('<?php echo $option; ?>')=='<?php echo $true_val; ?>','oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '<?php echo $option; ?>')}">
				<?php echo $label; ?>
			</div>
		</label>

	<?php }

	/**
	 * List predifened data components
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function data_folder() { 
		
		if (!oxygen_vsb_current_user_can_full_access()){
			return;
		}
		
		?>

		<p style="font-size: 14px">Please <a style="color: var(--oxy-light-text)" target="_blank" href="https://oxygenbuilder.com/documentation/other/security/#filtering-dynamic-data">review this security information</a> if you plan to render untrusted data via Oxygen’s dynamic data functions.</p>

		<div class="oxygen-add-section-element"
			data-searchid="dynamic_data_title"
			ng-click="iframeScope.addDynamicContent('ct_headline', '[oxygen data=\'title\']');">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<?php _e("Title","oxygen"); ?>
		</div>

		<div class="oxygen-add-section-element"
			data-searchid="dynamic_data_content"
			ng-click="iframeScope.addDynamicContent('ct_text_block', '[oxygen data=\'content\']');">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<?php _e("Content","oxygen"); ?>
		</div>

		<div class="oxygen-add-section-element"
			data-searchid="dynamic_data_date"
			ng-click="iframeScope.addDynamicContent('ct_text_block', '[oxygen data=\'date\']');">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<?php _e("Date","oxygen"); ?>
		</div>

		<div class="oxygen-add-section-element"
			data-searchid="dynamic_data_categories"
			ng-click="iframeScope.addDynamicContent('ct_text_block', '[oxygen data=\'terms\' taxonomy=\'category\' separator=\', \']');">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<?php _e("Categories","oxygen"); ?>
		</div>

		<div class="oxygen-add-section-element"
			data-searchid="dynamic_data_tags"
			ng-click="iframeScope.addDynamicContent('ct_text_block', '[oxygen data=\'terms\' taxonomy=\'post_tag\' separator=\', \']');">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<?php _e("Tags","oxygen"); ?>
		</div>

		<div class="oxygen-add-section-element"
			data-searchid="dynamic_data_featured_image"
			ng-click="iframeScope.addComponent('ct_image');iframeScope.insertShortcodeToImage('[oxygen data=\'featured_image\']')">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<?php _e("Featured Image","oxygen"); ?>
		</div>

		<div class="oxygen-add-section-element"
			data-searchid="dynamic_data_author"
			ng-click="iframeScope.addDynamicContent('ct_text_block', '[oxygen data=\'author\']');">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<?php _e("Author","oxygen"); ?>
		</div>

		<div class="oxygen-add-section-element"
			data-searchid="dynamic_data_author_avatar"
			ng-click="iframeScope.addComponent('ct_image');iframeScope.insertShortcodeToImage('[oxygen data=\'author_pic\']')">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<?php _e("Author Avatar","oxygen"); ?>
		</div>

		<div class="oxygen-add-section-element"
			data-searchid="dynamic_data_custom_field"
			ng-click="iframeScope.addCustomFieldComponent()">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<?php _e("Custom field","oxygen"); ?>
		</div>

	<?php }


	/**
	 * Output TinyMCE dialog window
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function tiny_mce() { ?>
	
		<div class="oxygen-tinymce-dialog-wrap" ng-show="tinyMCEWindow">
			<div class="oxygen-data-dialog">
				<h1><?php _e("Edit text", "oxygen"); ?></h1>
				<?php wp_editor("", "oxygen_vsb_tinymce", $settings = array(
					"media_buttons" => false,
					"editor_height" => 350
					)); ?>
				<br/>
				<span class="oxygen-apply-button" 
					ng-click="closeTinyMCEDialog()"><?php _e("Save & Close", "oxygen"); ?></span>
			</div>
			<div class="oxygen-data-dialog-bg"
				ng-show="tinyMCEWindow"
				ng-click="closeTinyMCEDialog()"></div>
		</div>
	
	<?php }

	
	/**
	 * Output box-shadow controls
	 *
	 * @since 2.4
	 * @author Ilya K.
	 */

	function box_shadow($prefix="", $inset=true) { ?>

		<?php if ($inset) : ?>
		<div class="oxygen-control-row">
			<div class="oxygen-control-wrapper">
				<label class="oxygen-checkbox">
					<input type="checkbox"
						ng-true-value="'inset'" 
						ng-false-value="'false'"
						<?php $this->ng_attributes($prefix.'box-shadow-inset'); ?>> 
					<div class='oxygen-checkbox-checkbox'
						ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('<?php echo $prefix; ?>box-shadow-inset')=='inset','oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '<?php echo $prefix; ?>box-shadow-inset')}">
						<div class="oxy-style-indicator"
							ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $prefix; ?>box-shadow-inset')&&!IDHasOption('<?php echo $prefix; ?>box-shadow-inset'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $prefix; ?>box-shadow-inset')}">
						</div>
						Inset
					</div>
				</label>
			</div>
		</div>
		<?php endif; ?>
		
		<div class="oxygen-control-row">
	        <?php $this->colorpicker_with_wrapper($prefix."box-shadow-color", __("Shadow Color", "oxygen") ); ?>
	    </div>
		<div class='oxygen-control-row'>
			<?php $this->slider_measure_box_with_wrapper($prefix.'box-shadow-horizontal-offset',__('Shadow Horizontal Offset','oxygen'), 'px'); ?>
		</div>
		<div class='oxygen-control-row'>
			<?php $this->slider_measure_box_with_wrapper($prefix.'box-shadow-vertical-offset',__('Shadow Vertical Offset','oxygen'), 'px'); ?>
		</div>
		<div class='oxygen-control-row'>
			<?php $this->slider_measure_box_with_wrapper($prefix.'box-shadow-blur',__('Shadow Blur','oxygen'), 'px'); ?>
		</div>
		<div class='oxygen-control-row'>
			<?php $this->slider_measure_box_with_wrapper($prefix.'box-shadow-spread',__('Shadow Spread','oxygen'), 'px'); ?>
		</div>
	
	<?php }

	
	/**
	 * Output HTML neeed to show accordion section
	 *
	 * @since 3.0
	 * @author Ilya K.
	 */

	static function oxygen_add_plus_accordion_section($id, $title) { ?>

	    <div class='oxygen-add-section-accordion'
	      ng-click="switchTab('components', '<?php echo $id; ?>');"
	      ng-hide="iframeScope.hasOpenFolders()">
	      <?php echo $title; ?>
	      <img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dropdown-arrow.svg'/>
	    </div>
	    <div class='oxygen-add-section-accordion-contents oxygen-add-section-accordion-contents-toppad'
	      ng-if="isShowTab('components','<?php echo $id; ?>')">
	      <?php do_action("oxygen_add_plus_{$id}_section_content"); ?>
	    </div>
	
	<?php }

	
	/**
	 * Output HTML for Presets controls
	 *
	 * @since 3.2
	 * @author Ilya K.
	 */

	static function element_presets_controls() { 

		$ng_show = "!hasOpenTabs(iframeScope.component.active.name)";

		$full_preset_elements = apply_filters('oxygen_elements_with_full_presets', array());
		$full_preset_elements = array_map(function($value) {
			return "isActiveName('$value')";
		}, $full_preset_elements);
		$ng_if = implode("||", $full_preset_elements);

		?>  

			<div class="oxygen-element-presets-controls oxygen-inset-controls" 
				ng-if="<?php echo $ng_if; ?>"
				ng-show="<?php echo $ng_show; ?>">

				<div class="oxygen-sidebar-flex-panel">
					<div class='oxygen-control-row'>
						<div class='oxygen-control-wrapper'>
		                    <label class='oxygen-control-label'><?php _e("Use Preset","oxygen"); ?></label>
		                    <div class='oxygen-control'>
		                        <div class="oxygen-select oxygen-select-box-wrapper">
		                            <div class="oxygen-select-box">
		                                <div class="oxygen-select-box-current">{{iframeScope.getCurrentPresetName()}}</div>
		                                <div class="oxygen-select-box-dropdown"></div>
		                            </div>
		                            <div class="oxygen-select-box-options">
		                                <div class="oxygen-select-box-option" 
		                                    title="<?php _e("Apply Preset to current element", "oxygen"); ?>"
		                                    ng-repeat="(presetKey,options) in iframeScope.elementPresets[iframeScope.component.active.name]"
		                                    ng-click="iframeScope.applyElementPreset(presetKey)">
		                                    <div>{{iframeScope.elementPresets[iframeScope.component.active.name][presetKey].name}}</div>
		                                </div>
		                            </div>
		                        </div>
		                    </div>
		                </div>
		            </div>

		            <div class='oxygen-control-row'>
		            	<div class='oxygen-control-wrapper'
		            		ng-hide="iframeScope.isDefaultPreset(iframeScope.component.active.name, iframeScope.currentPresetKey);">
		                	<div class='oxygen-control'>
		                        <div class="oxygen-apply-button"
		                            ng-click="iframeScope.deleteCurrentElementPreset()">
		                            <?php _e("Delete","oxygen"); ?>
		                        </div>
		                    </div>
		                </div>
		                <div class='oxygen-control-wrapper'>
		                	<div class='oxygen-control'>
		                        <div class="oxygen-apply-button"
		                            ng-click="iframeScope.exportCurrentPreset()">
		                            <?php _e("Export","oxygen"); ?>
		                        </div>
		                    </div>
		                </div>
		                <div class='oxygen-control-wrapper'>
		                    <div class='oxygen-control'>
		                        <div class="oxygen-apply-button"
		                            ng-click="iframeScope.importPreset()">
		                            <?php _e("Import","oxygen"); ?>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            
		            <div class='oxygen-control-row'>
		                <div class='oxygen-control-wrapper'>
		                    <label class='oxygen-control-label'><?php _e("Save New Preset","oxygen"); ?></label>
		                    <div class='oxygen-control'>
		                        <div class='oxygen-input'>
		                            <input type="text" spellcheck="false" placeholder="<?php _e("Preset Name","oxygen"); ?>" 
		                                ng-model="iframeScope.newPresetName"/>
		                        </div>
		                    </div>
	                	</div>
		                <div class='oxygen-control-wrapper'>
		                    <label class='oxygen-control-label'>&nbsp;</label>
		                    <div class='oxygen-control'>
		                        <div class="oxygen-apply-button"
		                            ng-click="iframeScope.saveElementPreset()">
		                            <?php _e("Save","oxygen"); ?>
		                        </div>
		                    </div>
		                </div>
		            </div>
				</div>

            </div>
	<?php }

	
	/**
	 * Output HTML for Presets controls
	 *
	 * @since 3.8
	 * @author Ilya K.
	 */

	static public function codemirror_theme_chooser() { ?>

		<div class="oxygen-codemirror-theme-chooser">
			<div class="oxygen-select oxygen-select-box-wrapper oxygen-select-up">
			<div class="oxygen-select-box">
				<div class="oxygen-select-box-current"
					ng-class="{'oxygen-select-box-current-default':$parent.iframeScope.globalCodeMirrorTheme=='gruvboxDark'}">
					<?php _e("Editor Theme", "oxygen"); ?>: "{{$parent.iframeScope.globalCodeMirrorTheme}}"
				</div>
				<div class="oxygen-select-box-dropdown"></div>
			</div>
			<?php $themes = array(
				'materialDark'=>__("Material","oxygen"),
				'solarizedDark'=>__("Solarized Dark","oxygen"),
				'solarizedLight'=>__("Solarized Light","oxygen"),
				'gruvboxDark'=>__("Gruvbox Dark","oxygen"),
				'gruvboxLight'=>__("Gruvbox Light","oxygen"),
				'nord'=>__("Nord","oxygen"),
				); ?>
			<div class="oxygen-select-box-options">
				<?php foreach ($themes as $key => $name) : ?>
				<div class="oxygen-select-box-option" 
				ng-click="$parent.iframeScope.globalCodeMirrorTheme='<?php echo $key; ?>';updateCodeMirrorTheme()">
				<?php echo $name; ?>
				</div>
				<?php endforeach; ?>
			</div>
			</div>
		</div>
		<div class="oxygen-codemirror-wrap">
			<label class="oxygen-checkbox">
				<input type="checkbox"
					ng-true-value="'true'" 
					ng-false-value="'false'"
					ng-change="updateCodeMirrorWrap()"
					ng-model="iframeScope.globalCodeMirrorWrap"> 
				<div class='oxygen-checkbox-checkbox'
					ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.globalCodeMirrorWrap=='true'}">
					<?php _e("Wrap","oxygen"); ?>
				</div>
			</label>
		</div>

	<?php }

	/**
	 * Output JS code to initiate CM6 instance
	 *
	 * @since 3.8
	 * @author Ilya K.
	 */

	static public function codemirror6_script($option, $element_id, $language = "javascript") { ?>
	<script>
		if (typeof($scope)!=="undefined") {
			var timeoutID;
			window.currentCMWrap = new window.OxyCM.Compartment()
			window.currentCMTheme = new window.OxyCM.Compartment()
			window.currentCMEditor = new OxyCM.EditorView({
				state: OxyCM.EditorState.create({
					extensions: [
						OxyCM.basicSetup,
						OxyCM.modules.keymap.of([OxyCM.modules.indentWithTab]),
						OxyCM.modules.<?php echo $language; ?>(),
						window.currentCMWrap.of($scope.iframeScope.globalCodeMirrorWrap === "true" ? [OxyCM.EditorView.lineWrapping, OxyCM.modules.search()] : OxyCM.modules.search()),
						window.currentCMTheme.of(OxyCM.modules[$scope.iframeScope.globalCodeMirrorTheme]),
						<?php if (strpos($option,"css")!==false) : ?>
						OxyCM.EditorView.updateListener.of((v)=> {
							if(v.docChanged) {
								clearTimeout(timeoutID);
								timeoutID = setTimeout( (event) => {
									$scope.iframeScope.setOptionModel('<?php echo $option; ?>',window.currentCMEditor.state.doc.toString());
									if ('<?php echo $option; ?>'=='code-css') {
										$scope.iframeScope.applyCodeBlockCSS();
									}
								}, 250);
							}
						}),
						<?php endif; ?>
					],
					doc: $scope.iframeScope.getOption('<?php echo $option; ?>')
				}),
				parent: document.getElementById("<?php echo $element_id; ?>")
			})
			window.currentCMEditor.contentDOM.addEventListener('blur', function(){
				
				$scope.iframeScope.setOptionModel('<?php echo $option; ?>',window.currentCMEditor.state.doc.toString());
				
				// Apply code based on context
				if ($scope.isActiveName('ct_code_block')) {
					<?php switch ($language) {
						case 'javascript':
							?>$scope.iframeScope.applyCodeBlockJS();<?php
							break;
						case 'php':
							?>$scope.iframeScope.applyCodeBlockPHP();<?php
							break;
						case 'css':
							?>$scope.iframeScope.applyCodeBlockCSS();<?php
							break;
						default:
							?>true;<?php
							break;
					}?>
				}
				else if ($scope.isActiveName('oxy_posts_grid')) {
					$scope.iframeScope.renderComponentWithAJAX('oxy_render_easy_posts')
				}
				else if ($scope.isActiveName('oxy_comments')) {
					$scope.iframeScope.renderComponentWithAJAX('oxy_render_comments_list')
				}
				else {
					<?php switch ($language) {
						case 'javascript':
							?>$scope.iframeScope.applyComponentJS();<?php
							break;
						case 'css':
							?>$scope.iframeScope.applyComponentCSS();<?php
							break;
						default:
							?>true;<?php
							break;
					}?>
				}
			})
		}
	</script>
	<?php }
}

// Create toolbar instance
if ( defined("SHOW_CT_BUILDER") ) {
	global $oxygen_toolbar;
	$oxygen_toolbar = new CT_Toolbar();
}
