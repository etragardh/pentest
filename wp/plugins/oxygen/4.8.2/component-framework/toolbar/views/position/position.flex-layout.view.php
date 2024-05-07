<div class="oxy-style-indicator"
	ng-show="isActiveName('ct_section')||isActiveName('ct_div_block')"
	ng-class="{'oxygen-has-class-value':(iframeScope.classHasOption('<?php echo $param['param_name'];?>')&&!IDHasOption('<?php echo $param['param_name'];?>'))||(iframeScope.classHasOptionEqualTo('display','grid')&&!IDHasOptionEqualTo('display','grid')),'oxygen-has-id-value':(iframeScope.IDHasOption('<?php echo $param['param_name'];?>'))||(iframeScope.IDHasOptionEqualTo('display','grid'))}"
></div>

<div class="oxy-style-indicator"
	ng-hide="isActiveName('ct_section')||isActiveName('ct_div_block')"
	ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('<?php echo $param['param_name'];?>')&&!IDHasOption('<?php echo $param['param_name'];?>'),'oxygen-has-id-value':iframeScope.IDHasOption('<?php echo $param['param_name'];?>')}"
></div>

<div class="oxygen-icon-button-list oxygen-icon-button-list-big oxygen-icon-button-list-equal"
	ng-hide="isActiveName('oxy_posts_grid')||(isActiveName('oxy_gallery'))||(isActiveName('oxy_dynamic_list')&&isShowTab('dynamicList','grid_layout'))">
									
	<label class='oxygen-icon-button-list-option'
		ng-class="{'oxygen-icon-button-list-option-active':iframeScope.getOption('<?php echo esc_attr($param['param_name'])?>')=='column','oxygen-icon-button-list-button-default':iframeScope.isInherited(iframeScope.component.active.id,'<?php echo esc_attr($param['param_name'])?>','column')==true}">
		<div class='oxygen-icon-button-list-option-icon-wrapper'>
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/flex/stack_vertically_icon.svg' />
			<input type="radio" name="<?php echo esc_attr($param['param_name'])?>" value="column"
				ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['<?php echo esc_attr($param['param_name'])?>']"
				ng-model-options="{ debounce: 10 }"
				ng-change="iframeScope.setOption(iframeScope.component.active.id,'<?php echo $tag; ?>','<?php echo esc_attr($param['param_name'])?>');"
				ng-click="radioButtonClick(iframeScope.component.active.name, '<?php echo esc_attr($param['param_name'])?>', 'column'); iframeScope.setTextAlign()"/>
				
		</div>
		<div class='oxygen-icon-button-list-option-label'>
			<?php if (!isset($param['vertical_text']) || !$param['vertical_text']) { 
				_e("Vertical");
			} else {
				echo $param['vertical_text'];
			} ?>
		</div>
	</label>

	<label class='oxygen-icon-button-list-option'
		ng-class="{'oxygen-icon-button-list-option-active':iframeScope.getOption('<?php echo esc_attr($param['param_name'])?>')=='row','oxygen-icon-button-list-button-default':iframeScope.isInherited(iframeScope.component.active.id,'<?php echo esc_attr($param['param_name'])?>','row')==true}">
			<div class='oxygen-icon-button-list-option-icon-wrapper'>
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/flex/stack_horizontally_icon.svg' />
				<input type="radio" name="<?php echo esc_attr($param['param_name'])?>" value="row"
					ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['<?php echo esc_attr($param['param_name'])?>']"
					ng-model-options="{ debounce: 10 }" 
					ng-change="iframeScope.setOption(iframeScope.component.active.id,'<?php echo $tag; ?>','<?php echo esc_attr($param['param_name'])?>');"
					ng-click="radioButtonClick(iframeScope.component.active.name, '<?php echo esc_attr($param['param_name'])?>', 'row'); iframeScope.setTextAlign()"/>
				
			</div>
			<div class='oxygen-icon-button-list-option-label'>
				<?php if (!isset($param['horizontal_text']) || !$param['horizontal_text']) { 
					_e("Horizontal");
				} else {
					echo $param['horizontal_text'];
				} ?>
			</div>
	</label>


	<label class='oxygen-icon-button-list-option'
		ng-show="isActiveName('ct_section')||isActiveName('ct_div_block')"
		ng-class="{'oxygen-icon-button-list-option-active':iframeScope.getOption('display')=='grid','oxygen-icon-button-list-button-default':iframeScope.isInherited(iframeScope.component.active.id,'display','grid')==true}">
			<div class='oxygen-icon-button-list-option-icon-wrapper'>
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/flex/grid.svg' />
				<input type="radio" name="display" value="grid"
					ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['display']"
					ng-model-options="{ debounce: 10 }" 
					ng-change="iframeScope.setOption(iframeScope.component.active.id,'<?php echo $tag; ?>','display');"
					ng-click="radioButtonClick(iframeScope.component.active.name, 'display', 'grid');"/>
			</div>
			<div class='oxygen-icon-button-list-option-label'>
				<?php if (!isset($param['grid_text']) || !$param['grid_text']) { 
					_e("Grid");
				} else {
					echo $param['grid_text'];
				} ?>
			</div>
	</label>
									
</div>

<div ng-show="isActiveName('oxy_posts_grid')||(isActiveName('oxy_dynamic_list')&&isShowTab('dynamicList','grid_layout'))">
	<?php $oxygen_toolbar->checkbox('display', __("Enable Grid", "oxygen"), 'grid', "") ?>
</div>

</div> <!-- control -->
</div> <!-- wrapper -->
</div> <!-- row -->

<?php include( CT_FW_PATH . '/toolbar/views/position/position.grid.view.php'); ?>

<div> <!-- control -->
<div> <!-- wrapper -->
<div> <!-- row -->