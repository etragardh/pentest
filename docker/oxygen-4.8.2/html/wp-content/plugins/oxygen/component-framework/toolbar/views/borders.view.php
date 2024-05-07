<!-- border side chooser -->
<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper'>
		<label class='oxygen-control-label'><?php _e("Currently Editing","oxygen"); ?></label>
		<div class='oxygen-control'>

			<div class="oxygen-select oxygen-select-box-wrapper">
				<div class="oxygen-select-box"
					ng-class="{'oxygen-option-default':currentBorder=='all'}">
					<div class="oxygen-select-box-current">{{currentBorder}}</div>
					<div class="oxygen-select-box-dropdown"></div>
				</div>
				<div class="oxygen-select-box-options">
					
					<div class="oxygen-select-box-option"
						ng-click="currentBorder='all'"
						ng-class="{'oxygen-select-box-option-active':currentBorder=='all'}">
						<?php _e("all borders", "component-theme"); ?>
					</div>
					<div class="oxygen-select-box-option"
						ng-click="currentBorder='top'"
						ng-class="{'oxygen-select-box-option-active':currentBorder=='top'}">
						<?php _e("top", "component-theme"); ?>
					</div>
					<div class="oxygen-select-box-option"
						ng-click="currentBorder='right'"
						ng-class="{'oxygen-select-box-option-active':currentBorder=='right'}">
						<?php _e("right", "component-theme"); ?>
					</div>
					<div class="oxygen-select-box-option"
						ng-click="currentBorder='bottom'"
						ng-class="{'oxygen-select-box-option-active':currentBorder=='bottom'}">
						<?php _e("bottom", "component-theme"); ?>
					</div>
					<div class="oxygen-select-box-option"
						ng-click="currentBorder='left'"
						ng-class="{'oxygen-select-box-option-active':currentBorder=='left'}">
						<?php _e("left", "component-theme"); ?>
					</div>

				</div>
			</div>

		</div>
	</div>
</div>

<!-- color and size -->
<div class='oxygen-control-row'>

	<?php $this->colorpicker_with_wrapper("border-all-color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='all'"); ?>
	<?php $this->colorpicker_with_wrapper("border-top-color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='top'"); ?>
	<?php $this->colorpicker_with_wrapper("border-left-color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='left'"); ?>
	<?php $this->colorpicker_with_wrapper("border-bottom-color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='bottom'"); ?>
	<?php $this->colorpicker_with_wrapper("border-right-color",__("Color","oxygen"),"oxygen-typography-font-color", "currentBorder=='right'"); ?>
	<?php $this->measure_box_with_wrapper("border-'+currentBorder+'-width", __("Width", "oxygen"), 'px,em'); ?>

</div>

<!-- border style -->
<div class='oxygen-control-row'>

	<div class='oxygen-control-wrapper oxy-indicator-underline'>
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('border-'+currentBorder+'-style')&&!IDHasOption('border-'+currentBorder+'-style'),'oxygen-has-id-value':iframeScope.IDHasOption('border-'+currentBorder+'-style')}">
		</div>
		<label class='oxygen-control-label'><?php _e("Style","oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>

				<?php $this->button_list_button("border-'+currentBorder+'-style",'none'); ?>
				<?php $this->button_list_button("border-'+currentBorder+'-style",'solid'); ?>
				<?php $this->button_list_button("border-'+currentBorder+'-style",'dashed'); ?>
				<?php $this->button_list_button("border-'+currentBorder+'-style",'dotted'); ?>

			</div>
		</div>
	</div>

</div>

<div class='oxygen-control-row' style='margin-bottom: 20px;'>
	<a href='#' id='oxygen-control-borders-unset-button'
		ng-click="iframeScope.unsetAllBorders()">
		<?php _e("unset all borders","oxygen"); ?></a>
</div>

<div class='oxygen-control-separator'></div>

<!-- border radius -->
<div class='oxygen-control-row'
	ng-show="!editIndividualRadii">

	<div class='oxygen-control-wrapper'>
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('border-radius')&&!IDHasOption('border-radius'),'oxygen-has-id-value':iframeScope.IDHasOption('border-radius')}">
		</div>
		<label class='oxygen-control-label'><?php _e("Border Radius","oxygen"); ?></label>

		<div class="oxygen-control">
			<?php $this->measure_box("border-radius", 'px,%,em'); ?>
		</div>
	
		<a href='#' id='oxygen-control-borders-radius-individual'
			ng-click="editIndividualRadii=true">
			<?php _e("edit individual radius", "oxygen"); ?> &raquo;</a>
	</div>

</div>

<!-- border radius individually -->
<div class='oxygen-control-row'
	ng-show="editIndividualRadii">

	<?php $this->measure_box_with_wrapper("border-top-left-radius", __("Top Left"), 'px,%,em'); ?>
	<?php $this->measure_box_with_wrapper("border-top-right-radius", __("Top Right"), 'px,%,em'); ?>

</div>

<div class='oxygen-control-row'
	ng-show="editIndividualRadii">

	<div class='oxygen-control-wrapper'>
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('border-bottom-left-radius')&&!IDHasOption('border-bottom-left-radius'),'oxygen-has-id-value':iframeScope.IDHasOption('border-bottom-left-radius')}">
		</div>
		<label class='oxygen-control-label'><?php _e("Bottom Left","oxygen"); ?></label>
		
		<div class="oxygen-control">
			<?php $this->measure_box("border-bottom-left-radius", 'px,%,em'); ?>
		</div>

		<a href='#' id='oxygen-control-borders-radius-individual'
			ng-click="editIndividualRadii=false">
			<?php _e("edit all radii", "oxygen"); ?> &raquo;</a>
	</div>

	<?php $this->measure_box_with_wrapper("border-bottom-right-radius", __("Bottom Right"), 'px,%,em'); ?>

</div>