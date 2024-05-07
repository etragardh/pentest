	<?php 
	
	/**
	 * Manage > Settings > Page Settings
	 *
	 */ 

	?>

	<?php global $oxygen_toolbar; ?>

	<div class="oxygen-flex-panel"
		ng-hide="!isShowTab('settings','page')||hasOpenChildTabs('settings','page')">

		<?php $this->settings_home_breadcrumbs(__("Page Settings","oxygen")); ?>

		<?php 
		
		/**
		 * Page Settings > Page width
		 *
		 */ 
		
		?>
		
		<div class="oxygen-control-row">
			<div class='oxygen-control-wrapper'>
				<label class='oxygen-control-label'><?php _e("Page Width","oxygen"); ?></label>
				<div class='oxygen-measure-box'>
					<input type="text" spellcheck="false"
						placeholder="{{$parent.iframeScope.pageSettings['max-width']}}" 
                        ng-model-options="{ updateOn: 'blur' }"
						ng-model="$parent.iframeScope.pageSettingsMeta['max-width']"
						ng-change="$parent.iframeScope.pagePageWidthUpdate()"/>
					<div class='oxygen-measure-box-unit-selector'>
						<div class='oxygen-measure-box-selected-unit'>px</div>
					</div>
				</div>
			</div>
		</div>


		<?php 
		
		/**
		 * Page Settings > Overlay Header
		 *
		 */ 
		
		?>
		
		<div class="oxygen-control-row">
			<div class='oxygen-control-wrapper'>
				<label class='oxygen-control-label'><?php _e("Overlay Header","oxygen"); ?></label>
				<div class="oxygen-select oxygen-select-box-wrapper">
					<div class="oxygen-select-box">
						<div class="oxygen-select-box-current"
							ng-class="{'oxygen-option-default':!$parent.iframeScope.pageSettingsMeta['overlay-header-above']}">{{$parent.iframeScope.getMediaTitle($parent.iframeScope.pageSettingsMeta['overlay-header-above'],true) || $parent.iframeScope.getMediaTitle($parent.iframeScope.pageSettings['overlay-header-above'],true)}}</div>
						<div class="oxygen-select-box-dropdown"></div>
					</div>
					<div class="oxygen-select-box-options">
						<div class="oxygen-select-box-option" 
							ng-click="$parent.iframeScope.pageSettingsMeta['overlay-header-above']='';$parent.iframeScope.pageSettingsUpdate()"
							ng-class="{'oxygen-select-box-option-active':$parent.iframeScope.pageSettingsMeta['overlay-header-above']==''}">
							&nbsp;
						</div>
						<div class="oxygen-select-box-option" 
							ng-click="$parent.iframeScope.pageSettingsMeta['overlay-header-above']='never';$parent.iframeScope.pageSettingsUpdate()"
							ng-class="{'oxygen-select-box-option-active':$parent.iframeScope.pageSettingsMeta['overlay-header-above']=='never'}">
							<?php _e("Never","oxygen"); ?>
						</div>
						<div class="oxygen-select-box-option" 
							ng-repeat="name in $parent.iframeScope.sortedMediaList(true)"
							ng-if="name!='default'"
							ng-click="$parent.iframeScope.pageSettingsMeta['overlay-header-above']=name;$parent.iframeScope.pageSettingsUpdate()"
							ng-class="{'oxygen-select-box-option-active':$parent.iframeScope.pageSettingsMeta['overlay-header-above']==name}">
							{{$parent.iframeScope.getMediaTitle(name, true)}}
						</div>
						<div class="oxygen-select-box-option" 
							ng-click="$parent.iframeScope.pageSettingsMeta['overlay-header-above']='always';$parent.iframeScope.pageSettingsUpdate()"
							ng-class="{'oxygen-select-box-option-active':$parent.iframeScope.pageSettingsMeta['overlay-header-above']=='always'}">
							<?php _e("Always","oxygen"); ?>
						</div>
					</div>
				</div>
			</div>
		</div>


		<?php
				
		/**
		 * Add new "Manage > Settings > Page Settings" Tabs via this action hook
		 *
		 * @since 2.2
		 */

		do_action("oxygen_vsb_page_settings_tabs");
					
		?>

	</div>


	<?php
				
	/**
	 * Add new "Manage > Settings > Page Settings" panels via this action hook
	 *
	 * @since 2.2
	 */

	do_action("oxygen_vsb_page_settings_content");	