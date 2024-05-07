<div class="oxygen-choose-selector-wrap ct-choose-selector-wrap" 
	ng-controller="ControllerSelectorDetector"
	ng-show="iframeScope.selectorDetector.mode">	
	
	<select class="ct-applied-selectors-list ct-select"
		ng-show="appliedSelectors.length>0" 
		ng-change="parseAppliedSelectors(chooseSelectorBoxValue)" 
		ng-model="chooseSelectorBoxValue">
		<option 
			ng-repeat="selector in appliedSelectors" 
			ng-value="selector">
			{{selector}}
		</option>
	</select>
	<div id="ct-choose-selector-content">
	</div>
	<!-- #ct-choose-selector-content -->
</div>