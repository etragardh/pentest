<?php
$transforms_list = array(
	'skew',
	'translate',
	//'translate3d',
	'rotate',
	'rotateX',
	'rotateY',
	'perspective',
	'rotate3d',
	'scale',
	//'scale3d',
);
?>
<div class="oxygen-control-row">
	<div class="oxygen-control-wrapper">
		<a href="#" class="oxygen-ghost-button" 
			ng-click="iframeScope.addComponentTransform()"><?php _e("Add Transform", "component-theme"); ?></a>
	</div>
</div>

<div class="oxygen-transform-control-wrap"
	ng-repeat="transform in iframeScope.component.options[iframeScope.component.active.id]['model']['transform'] track by $index">

		<span class='ct-float-right ct-icon ct-remove-icon oxygen-remove-transform' 
			ng-Click='iframeScope.removeComponentTransform($event, $index);'></span>
	
		<div class='oxygen-control-row'>
			<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
				<div class="oxy-style-indicator"
					ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('transform-type')&&!IDHasOption('transform-type'),'oxygen-has-id-value':iframeScope.IDHasOption('transform-type')}">
				</div>
				<label class='oxygen-control-label'><?php _e("Transform Type","oxygen"); ?></label>
				<div class='oxygen-control'>
					<div class="oxygen-select oxygen-select-box-wrapper">
						<div class="oxygen-select-box">
							<div class="oxygen-select-box-current">{{transform['transform-type']}}</div>
							<div class="oxygen-select-box-dropdown"></div>
						</div>
						<div class="oxygen-select-box-options">
							<?php foreach ($transforms_list as $name) : ?>
							<div class="oxygen-select-box-option" 
								ng-click="iframeScope.setOptionModel('transform.'+$index+'.transform-type','<?php echo $name; ?>');iframeScope.outputCSSOptions(iframeScope.component.active.id,true) "><?php echo $name; ?>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- skew -->
		<div class='oxygen-control-row'
			ng-if="iframeScope.getOption('transform.'+$index+'.transform-type')=='skew'">
			<?php $this->slider_measure_box_with_wrapper("transform.'+\$index+'.skewX", __('Skew X','oxygen'), "deg", -180, 180, false, 1); ?>
		</div>

		<div class='oxygen-control-row'
			ng-if="iframeScope.getOption('transform.'+$index+'.transform-type')=='skew'">
			<?php $this->slider_measure_box_with_wrapper("transform.'+\$index+'.skewY", __('Skew Y','oxygen'), "deg", -180, 180, false, 1); ?>
		</div>

		<!-- translate -->
		<div class='oxygen-control-row'
			ng-if="iframeScope.getOption('transform.'+$index+'.transform-type')=='translate'">
			<?php $this->slider_measure_box_with_wrapper("transform.'+\$index+'.translateX", __('Translate X','oxygen'), "em,px,%", -100, 100, false, 1); ?>
		</div>

		<div class='oxygen-control-row'
			ng-if="iframeScope.getOption('transform.'+$index+'.transform-type')=='translate'">
			<?php $this->slider_measure_box_with_wrapper("transform.'+\$index+'.translateY", __('Translate Y','oxygen'), "em,px,%", -100, 100, false, 1); ?>
		</div>

		<div class='oxygen-control-row'
			ng-if="iframeScope.getOption('transform.'+$index+'.transform-type')=='translate'">
			<?php $this->slider_measure_box_with_wrapper("transform.'+\$index+'.translateZ", __('Translate Z','oxygen'), "em,px,%", -100, 100, false, 1); ?>
		</div>

		<!-- rotate -->
		<div class='oxygen-control-row'
			ng-if="iframeScope.getOption('transform.'+$index+'.transform-type')=='rotate'">
			<?php $this->slider_measure_box_with_wrapper("transform.'+\$index+'.rotateAngle", __('Angle','oxygen'), "deg", -360, 360, false, 1); ?>
		</div>

		<!-- rotateX -->
		<div class='oxygen-control-row'
			ng-if="iframeScope.getOption('transform.'+$index+'.transform-type')=='rotateX'">
			<?php $this->slider_measure_box_with_wrapper("transform.'+\$index+'.rotateXAngle", __('Angle','oxygen'), "deg", -360, 360, false, 1); ?>
		</div>

		<!-- rotateY -->
		<div class='oxygen-control-row'
			ng-if="iframeScope.getOption('transform.'+$index+'.transform-type')=='rotateY'">
			<?php $this->slider_measure_box_with_wrapper("transform.'+\$index+'.rotateYAngle", __('Angle','oxygen'), "deg", -360, 360, false, 1); ?>
		</div>

		<!-- perspective -->
		<div class='oxygen-control-row'
			ng-if="iframeScope.getOption('transform.'+$index+'.transform-type')=='perspective'">
			<?php $this->slider_measure_box_with_wrapper("transform.'+\$index+'.perspective", __('Perspective','oxygen'), "em,px,%", 0, 100, false, 1); ?>
		</div>

		<!-- rotate3d -->
		<div class='oxygen-control-row'
			ng-if="iframeScope.getOption('transform.'+$index+'.transform-type')=='rotate3d'">
			<?php $this->slider_measure_box_with_wrapper("transform.'+\$index+'.rotate3dX", __('X','oxygen'), "&nbsp;", -100, 100, false, 1); ?>
		</div>

		<div class='oxygen-control-row'
			ng-if="iframeScope.getOption('transform.'+$index+'.transform-type')=='rotate3d'">
			<?php $this->slider_measure_box_with_wrapper("transform.'+\$index+'.rotate3dY", __('Y','oxygen'), "&nbsp;", -100, 100, false, 1); ?>
		</div>

		<div class='oxygen-control-row'
			ng-if="iframeScope.getOption('transform.'+$index+'.transform-type')=='rotate3d'">
			<?php $this->slider_measure_box_with_wrapper("transform.'+\$index+'.rotate3dZ", __('Z','oxygen'), "&nbsp;", -100, 100, false, 1); ?>
		</div>

		<div class='oxygen-control-row'
			ng-if="iframeScope.getOption('transform.'+$index+'.transform-type')=='rotate3d'">
			<?php $this->slider_measure_box_with_wrapper("transform.'+\$index+'.rotate3dAngle", __('Angle','oxygen'), "deg", -360, 360, false, 1); ?>
		</div>

		<!-- scale -->
		<div class='oxygen-control-row'
			ng-if="iframeScope.getOption('transform.'+$index+'.transform-type')=='scale'">
			<?php $this->slider_measure_box_with_wrapper("transform.'+\$index+'.scaleX", __('Scale X','oxygen'), "&nbsp;", -10, 10, false, 1); ?>
		</div>

		<div class='oxygen-control-row'
			ng-if="iframeScope.getOption('transform.'+$index+'.transform-type')=='scale'">
			<?php $this->slider_measure_box_with_wrapper("transform.'+\$index+'.scaleY", __('Scale Y','oxygen'), "&nbsp;", -10, 10, false, 1); ?>
		</div>

		<div class='oxygen-control-row'
			ng-if="iframeScope.getOption('transform.'+$index+'.transform-type')=='scale'">
			<?php $this->slider_measure_box_with_wrapper("transform.'+\$index+'.scaleZ", __('Scale Z','oxygen'), "&nbsp;", -10, 10, false, 1); ?>
		</div>

</div>