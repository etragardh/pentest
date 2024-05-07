<?php if (!isset($prefix)) $prefix = ""; ?>
<?php global $oxygen_toolbar; ?>
<div class='oxygen-control-row'>
	<?php $oxygen_toolbar->colorpicker_with_wrapper($prefix."background-color", __("Background Color", "oxygen")); ?>
</div>
<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper'>
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $prefix; ?>background-image')&&!IDHasOption('<?php echo $prefix; ?>background-image'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $prefix; ?>background-image')}"
		></div>
		<label class='oxygen-control-label'><?php _e("Background Image","oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class="oxygen-file-input">
				<input type="text" spellcheck="false" 
					ng-change="iframeScope.setOptionModel('<?php echo $prefix; ?>background-image', iframeScope.component.options[iframeScope.component.active.id]['model']['<?php echo $prefix; ?>background-image'])" 
					ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '<?php echo $prefix; ?>background-image')}" 
					ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['<?php echo $prefix; ?>background-image']" 
					ng-model-options="{ debounce: 10 }" 
					class="ng-valid oxygen-option-default ng-dirty ng-valid-parse ng-touched">

				<div class="oxygen-file-input-browse" 
					data-mediatitle="Select Image" 
					data-mediabutton="Select Image" 
					data-mediaproperty="<?php echo $prefix; ?>background-image" 
					data-mediatype="mediaUrl"><?php _e("browse","oxygen"); ?></div>

				<div ng-if="iframeScope.currentClass === false" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesImageMode" callback="iframeScope.insertShortcodeToBackground">data</div>
			</div>
		</div>
	</div>
</div>

<div class='oxygen-control-row'>
	<?php $oxygen_toolbar->colorpicker_with_wrapper($prefix."overlay-color", __("Image Overlay Color", "oxygen")); ?>
</div>
<!-- background-size -->
<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper oxy-indicator-underline' id='oxygen-control-layout-display'>
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $prefix; ?>background-size')&&!IDHasOption('<?php echo $prefix; ?>background-size'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $prefix; ?>background-size')}"
		></div>
		<label class='oxygen-control-label'><?php _e("Background Size", "oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>

				<?php $oxygen_toolbar->button_list_button($prefix.'background-size','auto'); ?>
				<?php $oxygen_toolbar->button_list_button($prefix.'background-size','cover'); ?>
				<?php $oxygen_toolbar->button_list_button($prefix.'background-size','contain'); ?>
				<?php $oxygen_toolbar->button_list_button($prefix.'background-size','manual'); ?>

			</div>
		</div>
	</div>
</div>

<div class="oxygen-control-row" ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['<?php echo $prefix; ?>background-size'] == 'manual'">
	<?php $oxygen_toolbar->measure_box_with_wrapper($prefix."background-size-width", __("Width", "oxygen"), 'px,%,em'); ?>
	<?php $oxygen_toolbar->measure_box_with_wrapper($prefix."background-size-height", __("Height", "oxygen"), 'px,%,em'); ?>
</div>

<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper oxy-indicator-underline' id='oxygen-control-layout-display'>
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $prefix; ?>background-repeat')&&!IDHasOption('<?php echo $prefix; ?>background-repeat'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $prefix; ?>background-repeat')}"
		></div>
		<label class='oxygen-control-label'><?php _e("Background Repeat", "oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>

				<?php $oxygen_toolbar->button_list_button($prefix.'background-repeat','no-repeat'); ?>
				<?php $oxygen_toolbar->button_list_button($prefix.'background-repeat','repeat'); ?>
				<?php $oxygen_toolbar->button_list_button($prefix.'background-repeat','repeat-x'); ?>
				<?php $oxygen_toolbar->button_list_button($prefix.'background-repeat','repeat-y'); ?>

			</div>
		</div>
	</div>
</div>

<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper oxy-indicator-underline' id='oxygen-control-layout-display'>
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $prefix; ?>background-attachment')&&!IDHasOption('<?php echo $prefix; ?>background-attachment'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $prefix; ?>background-attachment')}"
		></div>
		<label class='oxygen-control-label'><?php _e("Background Attachment (Parallax)", "oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>

				<?php $oxygen_toolbar->button_list_button($prefix.'background-attachment','scroll'); ?>
				<?php $oxygen_toolbar->button_list_button($prefix.'background-attachment','fixed'); ?>

			</div>
		</div>
	</div>
</div>

<div class="oxygen-control-row">
	<?php $oxygen_toolbar->measure_box_with_wrapper($prefix."background-position-left", __("Left", "oxygen"), 'px,%,em'); ?>
	<?php $oxygen_toolbar->measure_box_with_wrapper($prefix."background-position-top", __("Top", "oxygen"), 'px,%,em'); ?>
</div>

<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper oxy-indicator-underline' id='oxygen-control-layout-display'>
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $prefix; ?>background-clip')&&!IDHasOption('<?php echo $prefix; ?>background-clip'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $prefix; ?>background-clip')}"
		></div>
		<label class='oxygen-control-label'><?php _e("Background Clip", "oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>

				<?php $oxygen_toolbar->button_list_button($prefix.'background-clip','border-box'); ?>
				<?php $oxygen_toolbar->button_list_button($prefix.'background-clip','padding-box'); ?>
				<?php $oxygen_toolbar->button_list_button($prefix.'background-clip','content-box'); ?>

			</div>
		</div>
	</div>
</div>

<?php $blend_modes = ['normal', 'multiply', 'screen', 'overlay', 'darken', 'lighten', 'color-dodge', 'color-burn', 'hard-light', 'soft-light', 'difference', 'exclusion', 'hue', 'saturation', 'color', 'luminosity']; ?>
	<div class='oxygen-control-row'>
		<div class='oxygen-control-wrapper'>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $prefix; ?>background-blend-mode')&&!IDHasOption('<?php echo $prefix; ?>background-blend-mode'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $prefix; ?>background-blend-mode')}"
			></div>
			<label class='oxygen-control-label'><?php _e("Background Blend Mode","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class="oxygen-select oxygen-select-box-wrapper">
					<div class="oxygen-select-box"
						ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '<?php echo $prefix; ?>background-blend-mode')}">
						<div class="oxygen-select-box-current">{{$parent.iframeScope.getOption('<?php echo $prefix; ?>background-blend-mode')}}</div>
						<div class="oxygen-select-box-dropdown"></div>
					</div>
					<div class="oxygen-select-box-options">
						<div class="oxygen-select-box-option" 
							ng-click="$parent.iframeScope.setOptionModel('<?php echo $prefix; ?>background-blend-mode','')">&nbsp;</div>
						<?php foreach ($blend_modes as $mode) { ?>
						<div class="oxygen-select-box-option" 
							ng-click="$parent.iframeScope.setOptionModel('<?php echo $prefix; ?>background-blend-mode','<?php echo $mode; ?>')"><?php echo $mode; ?></div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>

<div class='oxygen-control-row'
	ng-show="isActiveName('ct_section')">
	<div class='oxygen-control-wrapper'>
		<label class='oxygen-control-label'><?php _e("Video Background URL (.mp4 / .webm)","oxygen"); ?></label>
		<div class='oxygen-control oxygen-special-property'>
			<div class="oxygen-file-input">
				<input type="text" spellcheck="false" 
					ng-change="iframeScope.setOptionModel('video_background', iframeScope.component.options[iframeScope.component.active.id]['model']['video_background']); iframeScope.rebuildDOM(iframeScope.component.active.id);" 
					ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'video_background')}" 
					ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['video_background']"
					class="ng-valid oxygen-option-default ng-dirty ng-valid-parse ng-touched">

				<div class="oxygen-file-input-browse" 
					data-mediatitle="Select Video" 
					data-mediabutton="Select Video" 
					data-mediaproperty="video_background" 
					data-mediacontent="video"
					data-mediatype="videoUrl"><?php _e("browse","oxygen"); ?></div>
			</div>
		</div>
	</div>
</div>

<div class="oxygen-control-row"
	ng-show="isActiveName('ct_section')">
	<div class='oxygen-control-wrapper'>
		<label class='oxygen-control-label'><?php _e("Hide Video Below","oxygen") ?></label>
		<div class='oxygen-control oxygen-special-property'>
			<?php $oxygen_toolbar->media_queries_list("video_background_hide") ?>
		</div>
	</div>
</div>

<div class="oxygen-control-row"
	ng-show="isActiveName('ct_section')">
	<div class='oxygen-control-wrapper'>
		<label class='oxygen-control-label'><?php _e("Video Overlay","oxygen") ?></label>
		<div class='oxygen-control oxygen-special-property'>
			<?php $oxygen_toolbar->colorpicker("video_background_overlay"); ?>
		</div>
	</div>
</div>

<?php if ( !isset( $show_gradient ) || $show_gradient !== false ) { ?>
<div class='oxygen-sidebar-advanced-subtab oxygen-gradient-subtab'
	
	ng-click="switchTab('advanced', 'background-gradient')"
	ng-class="{'ct-active' : isShowTab('advanced','background-gradient')}">
		<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/background.svg' />
		<?php _e('Gradient', 'oxygen'); ?>
		<span class="oxygen-tab-indicator"
			ng-show="iframeScope.isTabHasOptions('background')"></span>
		<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/dropdown-arrow.svg' />
</div>
<?php } ?>
