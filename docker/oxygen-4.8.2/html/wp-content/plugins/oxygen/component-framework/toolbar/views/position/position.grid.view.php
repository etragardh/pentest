<div class="oxygen-inset-controls oxygen-grid-controls"
	ng-if="iframeScope.getOption('flex-direction')=='grid'||iframeScope.getOption('display')=='grid'"
	ng-hide="(isActiveName('oxy_dynamic_list')&&!isShowTab('dynamicList','grid_layout'))||(iframeScope.getOption('display')=='grid' && iframeScope.isInherited(iframeScope.component.active.id,'display'))">
	
	<div class="oxygen-settings-section-heading">
		<label class='oxygen-control-label'><?php _e("Columns","oxygen"); ?></label>
	</div>
	<div class="oxygen-control-row"
		ng-class="{'oxygen-disabled':iframeScope.getOption('grid-columns-auto-fit')=='true'}">
		<?php $oxygen_toolbar->slider_with_wrapper('grid-column-count',__("Column Count", "oxygen"), 0, 10)?>
	</div>

	<?php $oxygen_toolbar->checkbox_with_wrapper('grid-columns-auto-fit', __("Auto-Fit Columns", "oxygen"), "true", "false") ?>

	<div class="oxygen-control-row">
		<?php $oxygen_toolbar->measure_box_with_wrapper('grid-column-min-width',__("Min Width", "oxygen"),"fr,px,%,em,rem,auto,vw,vh,none")?>
		<?php $oxygen_toolbar->measure_box_with_wrapper('grid-column-max-width',__("Max Width", "oxygen"),"fr,px,%,em,rem,auto,vw,vh,none")?>
	</div>
	<div class="oxygen-control-row">
		<?php $oxygen_toolbar->measure_box_with_wrapper('grid-column-gap',__("Gap", "oxygen"))?>
	</div>

	<div class='oxygen-control-row oxy-indicator-underline'>
		<div class='oxygen-control-wrapper'>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('grid-justify-items')&&!IDHasOption('grid-justify-items'),'oxygen-has-id-value':iframeScope.IDHasOption('grid-justify-items')}">
			</div>
			<label class='oxygen-control-label'><?php _e("Horizontal Item Alignment","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-icon-button-list'>

					<?php $oxygen_toolbar->icon_button_list_button(
						'grid-justify-items','left','flex/flex_horiz_left_icon.svg',false,__("Left", "oxygen"), 'iframeScope.setTextAlign()'); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						'grid-justify-items','center',	'flex/flex_horiz_center_icon.svg',false,__("Center", "oxygen"), 'iframeScope.setTextAlign()'); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						'grid-justify-items','right','flex/flex_horiz_right_icon.svg',false,__("Right", "oxygen"), 'iframeScope.setTextAlign()'); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						'grid-justify-items','stretch','flex/flex_horiz_justify_icon.svg',false,__("Stretch", "oxygen"), 'iframeScope.setTextAlign()'); ?>

				</div>
			</div>
		</div>
	</div>

	<div class="oxygen-settings-section-heading">
		<label class='oxygen-control-label'><?php _e("Rows","oxygen"); ?></label>
	</div>
	<div class="oxygen-control-row">
		<div class='oxygen-control-wrapper oxy-indicator-underline'>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('grid-row-behavior')&&!IDHasOption('grid-row-behavior'),'oxygen-has-id-value':iframeScope.IDHasOption('grid-row-behavior')}">
			</div>
			<label class='oxygen-control-label'><?php _e("Row Behavior","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-button-list'>
					<?php $oxygen_toolbar->button_list_button('grid-row-behavior','Auto'); ?>
					<?php $oxygen_toolbar->button_list_button('grid-row-behavior','Explicit'); ?>
				</div>
			</div>
		</div>
	</div>
	<?php $oxygen_toolbar->checkbox('grid-match-height-of-tallest-child',__("Match Height Of Tallest Child", "oxygen"), "true", "false")?>
	<div ng-if="iframeScope.getOption('grid-row-behavior')=='Explicit'">
		<div class="oxygen-control-row">
			<?php $oxygen_toolbar->slider_with_wrapper('grid-row-count',__("Row Count", "oxygen"), 0, 10)?>
		</div>
		<div class="oxygen-control-row">
			<?php $oxygen_toolbar->measure_box_with_wrapper('grid-row-min-height',__("Min Height", "oxygen"),"fr,px,%,em,rem,auto,vw,vh,none")?>
			<?php $oxygen_toolbar->measure_box_with_wrapper('grid-row-max-height',__("Max Height", "oxygen"),"fr,px,%,em,rem,auto,vw,vh,none")?>
		</div>
	</div>
	<div class="oxygen-control-row">
		<?php $oxygen_toolbar->measure_box_with_wrapper('grid-row-gap',__("Gap", "oxygen"))?>
	</div>

	<div class='oxygen-control-row'>
		<div class='oxygen-control-wrapper oxy-indicator-underline'>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('grid-align-items')&&!IDHasOption('grid-align-items'),'oxygen-has-id-value':iframeScope.IDHasOption('grid-align-items')}">
			</div>
			<label class='oxygen-control-label'><?php _e("Vertical Item Alignment","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-icon-button-list'>

					<?php $oxygen_toolbar->icon_button_list_button(
						'grid-align-items','start','flex/flex_vert_top_icon.svg',false,__("Start", "oxygen"), ''); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						'grid-align-items','center','flex/flex_vert_middle_icon.svg',false,__("Center", "oxygen"), ''); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						'grid-align-items','end','flex/flex_vert_bottom_icon.svg',false,__("End", "oxygen"), ''); ?>
					<?php $oxygen_toolbar->icon_button_list_button(
						'grid-align-items','stretch','flex/flex_vert_stretch_icon.svg',false,__("Stretch", "oxygen"), ''); ?>

				</div>
			</div>
		</div>
	</div>

	<?php if (!isset($options)) {$options=array();} ?>

	<div class='oxygen-settings-section-heading'>
		<label class='oxygen-control-label'><?php _e("Default Child Span","oxygen"); ?></label>
	</div>

	<div class='oxygen-control-row oxy-indicator-underline'>
		<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('grid-all-children-rule')&&!IDHasOption('grid-all-children-rule'),'oxygen-has-id-value':iframeScope.IDHasOption('grid-all-children-rule')}">
		</div>
		<div class='oxygen-control-wrapper'>
			<label class='oxygen-control-label'><?php _e("Column Span","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-input'>
					<input type="text" spellcheck="false"
						<?php self::ng_attributes("grid-all-children-rule']['column-span",'model',$options); ?>
						<?php self::ng_attributes("grid-all-children-rule",'change',$options); ?>/>
				</div>
			</div>
		</div>
		<div class='oxygen-control-wrapper'>
			<label class='oxygen-control-label'><?php _e("Row Span","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-input'>
					<input type="text" spellcheck="false"
						<?php self::ng_attributes("grid-all-children-rule']['row-span",'model',$options); ?>
						<?php self::ng_attributes("grid-all-children-rule",'change',$options); ?>/>
				</div>
			</div>
		</div>
	</div>

	<div class='oxygen-settings-section-heading'>
		<label class='oxygen-control-label'><?php _e("Child Span Override","oxygen"); ?></label>
	</div>

	<div class='oxygen-control-row'>
		<style>
			.oxygen-control-grid{
				display: grid;
				grid-auto-rows: minmax(min-content,1fr);
				grid-template-columns: repeat({{$parent.iframeScope.component.options[$parent.iframeScope.component.active.id]['model']['grid-column-count']}},minmax(0px,1fr));
			}
			.oxygen-control-grid > :nth-child({{realGridChildIndex}}) > div {
				background-color: #304967;
			}
			{{$parent.iframeScope.getGridChildCSS($parent.iframeScope.component.options[$parent.iframeScope.component.active.id]['model'],'oxygen-control-grid',"",false,"is section",false,"is preview")}}
		</style>
		<div class='oxygen-control-wrapper'>
			<div class='oxygen-control-grid'>
				<div
					ng-repeat="(key,value) in iframeScope.getElementChildren()"
					ng-init="$parent.currentGridChildIndex=-1;$parent.showGridAllChildrenRuleRow=false;$parent.showGridAllChildrenRuleColumn=false;">
					<div class='oxygen-control-grid-cell'
						ng-show="key+1 < iframeScope.getElementChildren().length"
						ng-click="$parent.showGridAllChildrenRuleRow=false;$parent.showGridAllChildrenRuleColumn=false;$parent.iframeScope.addGridChildRule(key); $parent.currentGridChildIndex=key+1; $parent.realGridChildIndex=key+1">
						&nbsp;
					</div>
					<div class='oxygen-control-grid-cell' 
						title="<?php _e("Edit :last-child", "oxygen"); ?>"
						ng-show="key+1 == iframeScope.getElementChildren().length"
						ng-click="$parent.showGridAllChildrenRuleRow=false;$parent.showGridAllChildrenRuleColumn=false;$parent.iframeScope.addGridChildRule(-1); $parent.currentGridChildIndex=0; $parent.realGridChildIndex=key+1">
						&nbsp;
					</div>
				</div>
			</div>
		</div>
	</div>

	<div ng-repeat="(key,value) in iframeScope.getOption('grid-child-rules') track by $index"
		 ng-show="currentGridChildIndex==key">
		<div class='oxygen-control-row'>
			<div class='oxygen-control-wrapper'>
				<label class='oxygen-control-label'><?php _e("Column Span","oxygen"); ?></label>
				<div class='oxygen-control'>
					<div class='oxygen-input'>
						<input type="text" spellcheck="false"
							<?php self::ng_attributes("grid-child-rules'][key]['column-span",'model',$options); ?>
							<?php self::ng_attributes("grid-child-rules",'change',$options); ?>/>
					</div>
				</div>
			</div>
			<div class='oxygen-control-wrapper'>
				<label class='oxygen-control-label'><?php _e("Row Span","oxygen"); ?></label>
				<div class='oxygen-control'>
					<div class='oxygen-input'>
						<input type="text" spellcheck="false"
							<?php self::ng_attributes("grid-child-rules'][key]['row-span",'model',$options); ?>
							<?php self::ng_attributes("grid-child-rules",'change',$options); ?>/>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="oxygen-control-row">
		<div class="oxygen-control-wrapper">
			<a href="#" class="oxygen-ghost-button" 
				ng-click="iframeScope.resetGridChildRules()"><?php _e("Reset Grid Children", "oxygen"); ?></a>
		</div>
	</div>

</div>