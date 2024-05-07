<!-- display -->
<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper oxy-indicator-underline' id='oxygen-control-layout-display'>
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('display')&&!IDHasOption('display'),'oxygen-has-id-value':iframeScope.IDHasOption('display')}"
		></div>
		<label class='oxygen-control-label'><?php _e("Display", "oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>

				<?php $this->button_list_button('display','flex'); ?>
				<?php $this->button_list_button('display','inline-flex'); ?>
				<?php $this->button_list_button('display','block'); ?>
				<?php $this->button_list_button('display','inline-block'); ?>

				<label class='oxygen-button-list-button'
					ng-show="isActiveName('oxy_dynamic_list')||isActiveName('oxy_gallery')||isActiveName('ct_div_block')||isActiveName('ct_section')"
					ng-class="{'oxygen-button-list-button-active':iframeScope.getOption('display')=='grid','oxygen-button-list-button-default':iframeScope.isInherited(iframeScope.component.active.id,'display','grid')==true}">
					<input type="radio" name="display" value="grid"
						<?php $this->ng_attributes('display', 'model,change', array()); ?>
						ng-click="radioButtonClick(iframeScope.component.active.name, 'display', 'grid')"/>
					<?php echo 'grid'; ?>
				</label>

				<?php $this->button_list_button('display','inline'); ?>
				<?php $this->button_list_button('display','none'); ?>

			</div>
		</div>
	</div>
</div>

<?php include( CT_FW_PATH . '/toolbar/views/position/position.grid.view.php'); ?>

<!-- flexbox controls -->
<div class='oxygen-inset-controls'
	ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['display'] == 'flex'|| iframeScope.component.options[iframeScope.component.active.id]['model']['display'] == 'inline-flex'">

	<!-- flex direction. -->
	<div class='oxygen-control-row'>
		<div class='oxygen-control-wrapper oxy-indicator-underline'>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('flex-direction')&&!IDHasOption('flex-direction'),'oxygen-has-id-value':iframeScope.IDHasOption('flex-direction')}"
			></div>
			<label class='oxygen-control-label'><?php _e("Flex Direction","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-button-list'>

					<?php $this->button_list_button('flex-direction','column'); ?>
					<?php $this->button_list_button('flex-direction','row'); ?>

					<div class="oxy-style-indicator"
						ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('flex-reverse')&&!IDHasOption('flex-reverse'),'oxygen-has-id-value':iframeScope.IDHasOption('flex-reverse')}"
					></div>
					<label class="oxygen-checkbox" id="oxygen-flexbox-reverse-order-checkbox">
						<input type="checkbox"
							ng-true-value="'reverse'" 
							ng-false-value="'false'"
							<?php $this->ng_attributes('flex-reverse'); ?>> 
						<div class='oxygen-checkbox-checkbox'
							ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('flex-reverse')=='reverse'}">
							<?php _e("Reverse","component-theme"); ?>
						</div>
					</label>

				</div>
			</div>
		</div>
	</div>

	<div class='oxygen-control-row'>
		<?php $oxygen_toolbar->measure_box_with_wrapper("gap", __("Gap"), 'px,%,em'); ?>
	</div>

	<!-- align items, justify content. we should use the icons here. -->
	<div class='oxygen-control-row'>
		<div class='oxygen-control-wrapper oxy-indicator-underline'>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('align-items')&&!IDHasOption('align-items'),'oxygen-has-id-value':iframeScope.IDHasOption('align-items')}"
			></div>
			<label class='oxygen-control-label'><?php _e("Align Items","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-button-list'>

					<?php $this->button_list_button('align-items','flex-start'); ?>
					<?php $this->button_list_button('align-items','center'); ?>
					<?php $this->button_list_button('align-items','flex-end'); ?>
					<?php $this->button_list_button('align-items','stretch'); ?>

				</div>
			</div>
		</div>
	</div>

	<div class='oxygen-control-row'>
		<div class='oxygen-control-wrapper oxy-indicator-underline' id='oxygen-control-layout-justify-content'>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('justify-content')&&!IDHasOption('justify-content'),'oxygen-has-id-value':iframeScope.IDHasOption('justify-content')}"
			></div>
			<label class='oxygen-control-label'><?php _e("Justify Content","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-button-list'>

					<?php $this->button_list_button('justify-content','flex-start'); ?>
					<?php $this->button_list_button('justify-content','center'); ?>
					<?php $this->button_list_button('justify-content','flex-end'); ?>
					<?php $this->button_list_button('justify-content','space-between'); ?>
					<?php $this->button_list_button('justify-content','space-around'); ?>

				</div>
			</div>
		</div>
	</div>

	<!-- flex wrap & align-->
	<div class='oxygen-control-row'>
		<div class='oxygen-control-wrapper oxy-indicator-underline'>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('flex-wrap')&&!IDHasOption('flex-wrap'),'oxygen-has-id-value':iframeScope.IDHasOption('flex-wrap')}"
			></div>
			<label class='oxygen-control-label'><?php _e("Flex Wrap","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-button-list'>

					<?php $this->button_list_button('flex-wrap','nowrap'); ?>
					<?php $this->button_list_button('flex-wrap','wrap'); ?>
					<?php $this->button_list_button('flex-wrap','wrap-reverse'); ?>

				</div>
			</div>
		</div>
	</div>

	<div class='oxygen-control-row'>
		<div class='oxygen-control-wrapper oxy-indicator-underline' id='oxygen-control-layout-align-content'>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('align-content')&&!IDHasOption('align-content'),'oxygen-has-id-value':iframeScope.IDHasOption('align-content')}"
			></div>
			<label class='oxygen-control-label'><?php _e("Align Content","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-button-list'>

					<?php $this->button_list_button('align-content','flex-start'); ?>
					<?php $this->button_list_button('align-content','center'); ?>
					<?php $this->button_list_button('align-content','flex-end'); ?>
					<?php $this->button_list_button('align-content','space-around'); ?>
					<?php $this->button_list_button('align-content','stretch'); ?>

				</div>
			</div>
		</div>
	</div>
</div>
<!-- .oxygen-inset-controls -->

<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper oxy-indicator-underline'>
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('float')&&!IDHasOption('float'),'oxygen-has-id-value':iframeScope.IDHasOption('float')}"
		></div>
		<label class='oxygen-control-label'><?php _e("Float","oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>

				<?php $this->button_list_button('float','none'); ?>
				<?php $this->button_list_button('float','left'); ?>
				<?php $this->button_list_button('float','right'); ?>

			</div>
		</div>
	</div>
</div>

<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper oxy-indicator-underline'>
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('overflow')&&!IDHasOption('overflow'),'oxygen-has-id-value':iframeScope.IDHasOption('overflow')}"
		></div>
		<label class='oxygen-control-label'><?php _e("Overflow","oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>
				
				<?php $this->button_list_button('overflow','visible'); ?>
				<?php $this->button_list_button('overflow','hidden'); ?>
				<?php $this->button_list_button('overflow','scroll'); ?>

			</div>
		</div>
	</div>
</div>

<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper oxy-indicator-underline'>
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('clear')&&!IDHasOption('clear'),'oxygen-has-id-value':iframeScope.IDHasOption('clear')}"
		></div>
		<label class='oxygen-control-label'><?php _e("Clear","oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>
				
				<?php $this->button_list_button('clear','none'); ?>
				<?php $this->button_list_button('clear','left'); ?>
				<?php $this->button_list_button('clear','right'); ?>
				<?php $this->button_list_button('clear','both'); ?>

			</div>
		</div>
	</div>
</div>

<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper oxy-indicator-underline'>
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('visibility')&&!IDHasOption('visibility'),'oxygen-has-id-value':iframeScope.IDHasOption('visibility')}"
		></div>
		<label class='oxygen-control-label'><?php _e("Visibility","oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>
				
				<?php $this->button_list_button('visibility','visible'); ?>
				<?php $this->button_list_button('visibility','hidden'); ?>

			</div>
		</div>
	</div>
</div>

<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper'>
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('z-index')&&!IDHasOption('z-index'),'oxygen-has-id-value':iframeScope.IDHasOption('z-index')}"
		></div>
		<label class='oxygen-control-label'><?php _e("Z-index","oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class="oxygen-input">
		        <input type="text" spellcheck="false"
		            <?php $this->ng_attributes("z-index"); ?>>
		    </div>
		</div>
	</div>
</div>

<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper oxy-indicator-underline'>
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('position')&&!IDHasOption('position'),'oxygen-has-id-value':iframeScope.IDHasOption('position')}"
		></div>
		<label class='oxygen-control-label'><?php _e("Position","oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>
				
				<?php $this->button_list_button('position','static'); ?>
				<?php $this->button_list_button('position','absolute'); ?>
				<?php $this->button_list_button('position','relative'); ?>
				<?php $this->button_list_button('position','fixed'); ?>

			</div>
		</div>
	</div>
</div>

<div class='oxygen-control-row oxygen-control-row-inset'
	ng-show="iframeScope.getOption('position')=='fixed' || iframeScope.getOption('position')=='absolute'||iframeScope.getOption('position')=='relative'">
	<div class='oxygen-control-wrapper'>
		<div class='oxygen-control'>
			<div class='oxygen-four-sides-measure-box oxygen-four-sides-measure-box-position oxygen-four-sides-measure-box-labels'>

				<?php $this->measure_box('top','',true,true,"model,change,keypress","show_indicator", "Top"); ?>
				<?php $this->measure_box('left','',true,true,"model,change,keypress","show_indicator", "Left"); ?>
				<?php $this->measure_box('right','',true,true,"model,change,keypress","show_indicator", "Right"); ?>
				<?php $this->measure_box('bottom','',true,true,"model,change,keypress","show_indicator", "Bottom"); ?>

			</div>
		</div>
	</div>
</div>

<div class='oxygen-inset-controls oxygen-inset-controls-light' id='flex-child-controls'
	ng-show="iframeScope.component.options[iframeScope.component.active.parent.id]['model']['display'] == 'flex'||iframeScope.component.options[iframeScope.component.active.parent.id]['model']['display'] == 'inline-flex'">

	<h3><?php _e("Flexbox Child Controls","oxygen"); ?></h3>
	
	<div class='oxygen-control-row'>
		<div class='oxygen-control-wrapper oxy-indicator-underline'>
			<div class="oxy-style-indicator"
				ng-class="{'oxygen-has-class-value':iframeScope.classHasOption('align-self')&&!IDHasOption('align-self'),'oxygen-has-id-value':iframeScope.IDHasOption('align-self')}"
			></div>
			<label class='oxygen-control-label'><?php _e("Align Self","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-button-list'>

					<?php $this->button_list_button('align-self','auto'); ?>
					<?php $this->button_list_button('align-self','flex-start'); ?>
					<?php $this->button_list_button('align-self','center'); ?>
					<?php $this->button_list_button('align-self','flex-end'); ?>
					<?php $this->button_list_button('align-self','stretch'); ?>

				</div>
			</div>
		</div>
	</div>
	
	<div class='oxygen-control-row'>
		
		<?php $this->simple_input_with_wrapper('order','Order'); ?>
		<?php $this->simple_input_with_wrapper('flex-grow','Flex Grow'); ?>
		<?php $this->simple_input_with_wrapper('flex-shrink','Flex Shrink'); ?>

	</div>
</div>
<!-- #flex-child-controls -->