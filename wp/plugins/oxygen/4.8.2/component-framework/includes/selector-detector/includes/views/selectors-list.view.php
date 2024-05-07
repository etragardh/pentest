<div class="ct-css-node-header ct-node-options-active ct-style-set-selector"
	ng-repeat="selector in iframeScope.objectToArrayObject(iframeScope.customSelectors) | filter:{parent: '!'} track by selector.key"
	ng-show="!iframeScope.customSelectors[selector.key]['set_name']"
	ng-click="iframeScope.setCustomSelectorToEdit(selector.key);disableSelectorDetectorMode(); iframeScope.selectedStyleSet=null"
	ng-class="{'ct-active-selector':selectorToEdit==selector.key}">
	{{iframeScope.customSelectors[selector.key]['friendly_name'] || selector.key}}
	<div class="ct-node-options">
		<span class="ct-icon ct-visible-icon"
			ng-click="iframeScope.highlightSelector(true,selector.key,$event)"
			title="<?php _e("Highlight selector", "component-theme"); ?>">
			</span>
		<!-- <span class="ct-icon ct-copy-item-icon"></span> -->
		<span class="ct-icon ct-delete-icon"
			title="<?php _e("Delete selector", "component-theme"); ?>"
			ng-click="iframeScope.deleteCustomSelector(selector.key,$event)"></span>
	</div>
</div>