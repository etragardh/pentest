<div ng-hide="iframeScope.component.options[iframeScope.component.active.id]['model']['display'] == 'grid'">
<div class='oxygen-control-rows-multiple-inset'
	ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['<?php echo esc_attr($flex_direction); ?>'] == 'row'">

	<div class='oxygen-control-row'>
		<div class='oxygen-control-wrapper oxygen-control-wrapper-center oxygen-basic-styles-flex-alignment-wrapper'>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $prefix?>align-items')&&!IDHasOption('<?php echo $prefix?>align-items'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $prefix?>align-items')}">
			</div>
			<label class='oxygen-control-label'><?php _e("Vertical Item Alignment","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-icon-button-list'>

					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'align-items','flex-start','flex/flex_vert_top_icon.svg',false,__("Top", "oxygen"), ''); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'align-items','center',	'flex/flex_vert_middle_icon.svg',false,__("Middle", "oxygen"), ''); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'align-items','flex-end','flex/flex_vert_bottom_icon.svg',false,__("Bottom", "oxygen"), ''); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'align-items','stretch','flex/flex_vert_stretch_icon.svg',false,__("Stretch", "oxygen"), ''); ?>

				</div>
			</div>
		</div>
	</div>

	<div class='oxygen-control-row'>
		<div class='oxygen-control-wrapper oxygen-control-wrapper-center oxygen-basic-styles-flex-alignment-wrapper'>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $prefix?>justify-content')&&!IDHasOption('<?php echo $prefix?>justify-content'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $prefix?>justify-content')}">
			</div>
			<label class='oxygen-control-label'><?php _e("Horizontal Item Alignment","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-icon-button-list oxygen-basic-styles-flex-alignment-padded'>

					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'justify-content','flex-start','flex/flex_vert_left_icon.svg',false,__("Left", "oxygen"), 'iframeScope.setTextAlign()'); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'justify-content','center',	'flex/flex_vert_center_icon.svg',false,__("Center", "oxygen"), 'iframeScope.setTextAlign()'); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'justify-content','flex-end','flex/flex_vert_right_icon.svg',false,__("Right", "oxygen"), 'iframeScope.setTextAlign()'); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'justify-content','space-around','flex/flex_vert_space_around_icon.svg',false,__("Space Around", "oxygen"), 'iframeScope.setTextAlign()'); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'justify-content','space-between','flex/flex_vert_space_between_icon.svg',false,__("Space Between", "oxygen"), 'iframeScope.setTextAlign()'); ?>

				</div>
			</div>
		</div>
	</div>

</div>
<!-- .oxygen-control-rows-multiple-inset -->

<div class='oxygen-control-rows-multiple-inset'
	ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['<?php echo esc_attr($flex_direction); ?>'] == 'column'">

	<div class='oxygen-control-row'>
		<div class='oxygen-control-wrapper oxygen-control-wrapper-center oxygen-basic-styles-flex-alignment-wrapper'>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $prefix?>align-items')&&!IDHasOption('<?php echo $prefix?>align-items'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $prefix?>align-items')}">
			</div>
			<label class='oxygen-control-label'><?php _e("Horizontal Item Alignment","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-icon-button-list'>

					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'align-items','flex-start','flex/flex_horiz_left_icon.svg',false,__("Left", "oxygen"), 'iframeScope.setTextAlign()'); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'align-items','center',	'flex/flex_horiz_center_icon.svg',false,__("Center", "oxygen"), 'iframeScope.setTextAlign()'); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'align-items','flex-end','flex/flex_horiz_right_icon.svg',false,__("Right", "oxygen"), 'iframeScope.setTextAlign()'); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'align-items','stretch','flex/flex_horiz_justify_icon.svg',false,__("Stretch", "oxygen"), 'iframeScope.setTextAlign()'); ?>

				</div>
			</div>
		</div>
	</div>

	<div class='oxygen-control-row'
		ng-hide="iframeScope.component.active.name == 'ct_section' && iframeScope.component.options[iframeScope.component.active.id]['model']['<?php echo esc_attr($flex_direction); ?>'] == 'column'">
		<div class='oxygen-control-wrapper oxygen-control-wrapper-center oxygen-basic-styles-flex-alignment-wrapper'>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $prefix?>justify-content')&&!IDHasOption('<?php echo $prefix?>justify-content'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $prefix?>justify-content')}">
			</div>
			<label class='oxygen-control-label'><?php _e("Vertical Item Alignment","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-icon-button-list oxygen-basic-styles-flex-alignment-padded'>

					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'justify-content','flex-start','flex/flex_horiz_top_icon.svg',false,__("Top", "oxygen")); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'justify-content','center',	'flex/flex_horiz_middle_icon.svg',false,__("Middle", "oxygen")); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'justify-content','flex-end','flex/flex_horiz_bottom_icon.svg',false,__("Bottom", "oxygen")); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						$prefix . 'justify-content','space-around','flex/flex_horiz_space_around_icon.svg',false,__("Space Around", "oxygen")); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						'justify-content','space-between','flex/flex_horiz_space_between_icon.svg',false,__("Space Between", "oxygen")); ?>

				</div>
			</div>
		</div>
	</div>

</div>
<?php if ( oxygen_vsb_get_user_edit_mode() != "edit_only" ) { ?>
<div class='oxygen-control-row'>
	<?php $oxygen_toolbar->measure_box_with_wrapper("gap", __("Gap"), 'px,%,em'); ?>
</div>
<?php } ?>
<!-- .oxygen-control-rows-multiple-inset -->
</div>