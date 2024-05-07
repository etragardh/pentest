<div class="oxygen-control-row">
	<div class="oxygen-control-wrapper">
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('gradient')&&!IDHasOption('gradient'),'oxygen-has-id-value':iframeScope.IDHasOption('gradient')}">
		</div>
		<a href="#" class="oxygen-gradient-add-color" ng-click="addGradientColor()"><?php _e("Add a Color", "component-theme"); ?></a>
	</div>
</div>

<div class="oxygen-control-row" ng-repeat="color in iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['colors'] track by $index">
	
	<?php $this->colorpicker_with_wrapper("gradient", false, "", "", "['colors'][".'$index'."]['value']", 'oxygen-control-wrapper-5050-wide'); ?>
	<div class="oxygen-control-wrapper">
		<div class="oxygen-control">
			<div class="oxygen-measure-box">
				<input type="text" spellcheck="false" ng-model="color.position" ng-model-options="{ debounce: 10 }" ng-change="setGradientForBG()" class="ng-pristine ng-untouched ng-valid">
				
				<div class="oxygen-measure-box-unit-selector">
					<div class="oxygen-measure-box-selected-unit">{{color['position-unit']}}</div>
					<div class="oxygen-measure-box-units">
						<div class="oxygen-measure-box-unit"
							ng-repeat="unit in ['px', '%', 'em', 'rem']"
							ng-class="{'oxygen-measure-box-unit-active':color['position-unit'] == unit}"
							ng-click="color['position-unit'] = unit; setGradientForBG()" >
							{{unit}}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<span class='ct-float-right ct-icon ct-remove-icon oxygen-gradient-remove-color' ng-Click='removeGradientColor($event, $index);'></span>
</div>


<div class="oxygen-control-row">
	<div class="oxygen-control-wrapper" id="oxygen-control-layout-display">
		<label class="oxygen-control-label"><?php _e("Type", "component-theme"); ?></label>
		<div class="oxygen-control">
			<div class="oxygen-button-list">
				<label class="oxygen-button-list-button"
					ng-class="{'oxygen-button-list-button-active': iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['gradient-type'] == 'linear', 'oxygen-button-list-button-default': !iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['gradient-type']}" >
					<input type="radio" 
						value="linear" 
						ng-checked="iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['gradient-type'] == 'linear'"
						ng-click="toggleGradientRadio('gradient-type', 'linear', $index, $event)"
						class="ng-pristine ng-untouched ng-valid" />
				<?php _e("linear", "component-theme"); ?></label>
							
				<label class="oxygen-button-list-button" ng-class="{'oxygen-button-list-button-active': iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['gradient-type'] == 'radial'}">
					<input type="radio" 
						value="radial" 
						ng-checked="iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['gradient-type'] == 'linear'"
						ng-click="toggleGradientRadio('gradient-type', 'radial', $index, $event)" 
						class="ng-pristine ng-untouched ng-valid" />
				<?php _e("radial", "component-theme"); ?></label>
			</div>
		</div>
	</div>
</div>

<div ng-if="iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['gradient-type'] == 'radial'">
	
	<div class="oxygen-control-row">
		<div class="oxygen-control-wrapper" id="oxygen-control-layout-display">
			<label class="oxygen-control-label"><?php _e("Shape", "component-theme"); ?></label>
			<div class="oxygen-control">
				<div class="oxygen-button-list">
					<label class="oxygen-button-list-button"
						ng-class="{'oxygen-button-list-button-active': iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-shape'] == 'ellipse', 'oxygen-button-list-button-default': !iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-shape']}" >
						<input type="radio" 
							value="ellipse" 
							ng-checked="iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-shape'] == 'ellipse'"
							ng-click="toggleGradientRadio('radial-shape', 'ellipse', $index, $event)"
							class="ng-pristine ng-untouched ng-valid" />
					<?php _e("ellipse", "component-theme"); ?></label>
								
					<label class="oxygen-button-list-button" ng-class="{'oxygen-button-list-button-active': iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-shape'] == 'circle'}">
						<input type="radio" 
							value="circle" 
							ng-checked="iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-shape'] == 'circle'"
							ng-click="toggleGradientRadio('radial-shape', 'circle', $index, $event)" 
							class="ng-pristine ng-untouched ng-valid" />
					<?php _e("circle", "component-theme"); ?></label>
				</div>
			</div>
		</div>
	</div>

	<div class="oxygen-control-row">
		<div class="oxygen-control-wrapper" id="oxygen-control-layout-display">
			<label class="oxygen-control-label"><?php _e("Size", "component-theme"); ?></label>
			<div class="oxygen-control">
				<div class="oxygen-button-list">
					<label class="oxygen-button-list-button"
						ng-class="{'oxygen-button-list-button-active': iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-size'] == 'farthest-corner', 'oxygen-button-list-button-default': !iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-size']}" >
						<input type="radio" 
							value="farthest-corner" 
							ng-checked="iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-size'] == 'farthest-corner'"
							ng-click="toggleGradientRadio('radial-size', 'farthest-corner', $index, $event)"
							class="ng-pristine ng-untouched ng-valid" />
					<?php _e("farthest-corner", "component-theme"); ?></label>
								
					<label class="oxygen-button-list-button" ng-class="{'oxygen-button-list-button-active': iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-size'] == 'closest-side'}">
						<input type="radio" 
							value="closest-side" 
							ng-checked="iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-size'] == 'closest-side'"
							ng-click="toggleGradientRadio('radial-size', 'closest-side', $index, $event)" 
							class="ng-pristine ng-untouched ng-valid" />
					<?php _e("closest-side", "component-theme"); ?></label>

					<label class="oxygen-button-list-button" ng-class="{'oxygen-button-list-button-active': iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-size'] == 'closest-corner'}">
						<input type="radio" 
							value="closest-corner" 
							ng-checked="iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-size'] == 'closest-corner'"
							ng-click="toggleGradientRadio('radial-size', 'closest-corner', $index, $event)" 
							class="ng-pristine ng-untouched ng-valid" />
					<?php _e("closest-corner", "component-theme"); ?></label>

					<label class="oxygen-button-list-button" ng-class="{'oxygen-button-list-button-active': iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-size'] == 'farthest-side'}">
						<input type="radio" 
							value="farthest-side" 
							ng-checked="iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-size'] == 'farthest-side'"
							ng-click="toggleGradientRadio('radial-size', 'farthest-side', $index, $event)" 
							class="ng-pristine ng-untouched ng-valid" />
					<?php _e("farthest-side", "component-theme"); ?></label>
				</div>
			</div>
		</div>
	</div>

	<div class="oxygen-control-row">
		<div class="oxygen-control-wrapper">
			<label class="oxygen-control-label"><?php _e("Left", "component-theme"); ?></label>
			<div class="oxygen-control">
				<div class="oxygen-measure-box oxygen-option-default">
					<input type="text" spellcheck="false" 
						ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-position-left']" 
						ng-model-options="{ debounce: 10 }" 
						ng-change="setGradientForBG()" 
						class="ng-pristine ng-untouched ng-valid">

					<div class="oxygen-measure-box-unit-selector">
						<div class="oxygen-measure-box-selected-unit">{{iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-position-left-unit'] || 'px'}}</div>
						<div class="oxygen-measure-box-units">
							<div class="oxygen-measure-box-unit"
								ng-repeat="unit in ['px', '%', 'em']"
								ng-class="{'oxygen-measure-box-unit-active':iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-position-left-unit'] == unit}"
								ng-click="iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-position-left-unit'] = unit; setGradientForBG()" >
								{{unit}}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="oxygen-control-wrapper">
			<label class="oxygen-control-label"><?php _e("Top", "component-theme"); ?></label>
			<div class="oxygen-control">
				<div class="oxygen-measure-box oxygen-option-default">
					<input type="text" spellcheck="false" 
						ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-position-top']" 
						ng-model-options="{ debounce: 10 }" 
						ng-change="setGradientForBG()" 
						class="ng-pristine ng-untouched ng-valid">

					<div class="oxygen-measure-box-unit-selector">
						<div class="oxygen-measure-box-selected-unit">{{iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-position-top-unit'] || 'px'}}</div>
						<div class="oxygen-measure-box-units">
							<div class="oxygen-measure-box-unit"
								ng-repeat="unit in ['px', '%', 'em']"
								ng-class="{'oxygen-measure-box-unit-active':iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-position-top-unit'] == unit}"
								ng-click="iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['radial-position-top-unit'] = unit; setGradientForBG()" >
								{{unit}}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>

<div ng-if="!iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['gradient-type'] || iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['gradient-type'] == 'linear'">
	<div class="oxygen-control-wrapper">
		<label class="oxygen-control-label"><?php _e("Angle", "component-theme"); ?></label>
		<div class="oxygen-control">
			<div class="oxygen-measure-box oxygen-option-default">
				<input type="text" spellcheck="false" 
					ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['gradient']['linear-angle']" 
					ng-model-options="{ debounce: 10 }" 
					ng-change="setGradientForBG()" 
					class="ng-pristine ng-untouched ng-valid">

				<div class="oxygen-measure-box-unit-selector">
					<div class="oxygen-measure-box-selected-unit">&deg;</div>
					<div class="oxygen-measure-box-units">
						<div class="oxygen-measure-box-unit oxygen-measure-box-unit-active">
							&deg;
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
