<div class="oxygen-control-row">
	<div class="oxygen-control-wrapper">
		<a href="#" class="oxygen-gradient-add-color" ng-click="addCustomAttribute()">Add Attribute</a>
	</div>
</div>

<div  
	ng-repeat="attribute in iframeScope.component.options[iframeScope.component.active.id]['model']['custom-attributes'] track by $index"
	ng-if="attribute!=null">	
	
	<div class="oxygen-control-row">
		<div class="oxygen-control-wrapper custom-attributes-icons">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/trash.svg"
				title="<?php _e("Remove Attribute","oxygen"); ?>"
				ng-click="removeCustomAttribute($event, $index)">

			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/pin.svg"
				title="<?php _e("Pin to Primary settings","oxygen"); ?>"
				ng-class="{'ct-link-button-highlight':isCustomAttributePinned($index)}"
				ng-click="toggleCustomAttribute($index)">
		</div>
	</div>

	<div class="oxygen-control-row custom-attributes-name-row">
		<div class="oxygen-control-wrapper">
			<div class="oxygen-control">			
				<div class="oxygen-input">
					<input type="text" spellcheck="false"
						placeholder="<?php _e("name", "oxygen"); ?>" 
						ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,'custom-attributes');iframeScope.checkResizeBoxOptions('custom-attributes'); iframeScope.validateCustomAttributeName($index);" 
						ng-model="$parent.iframeScope.component.options[$parent.iframeScope.component.active.id]['model']['custom-attributes'][$index]['name']">
						<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode" optionname="'custom-attributes.'+$index+'.name'"><?php _e("data", "oxygen"); ?></div>
				</div>
			</div>
		</div>
	</div>

	<div class="oxygen-control-row">
		<div class="oxygen-control-wrapper">
			<div class="oxygen-control">		
				<div class="oxygen-input">
					<input type="text" spellcheck="false"
						placeholder="<?php _e("value", "oxygen"); ?>" 
						ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,'custom-attributes');iframeScope.checkResizeBoxOptions('custom-attributes'); iframeScope.validateCustomAttributeValue($index);" 
						ng-model="$parent.iframeScope.component.options[$parent.iframeScope.component.active.id]['model']['custom-attributes'][$index]['value']">
						<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode" optionname="'custom-attributes.'+$index+'.value'"><?php _e("data", "oxygen"); ?></div>
				</div>
			</div>
		</div>
	</div>

</div>

<div class="oxygen-control-row oxygen-control-row-bottom-bar">
	<a href="#" class="oxygen-apply-button"
		ng-click="iframeScope.rebuildDOM(iframeScope.component.active.id);iframeScope.updateParentRepeater(iframeScope.component.active.id);">
		<?php _e("Apply Attributes", "oxygen"); ?>
	</a>
</div>