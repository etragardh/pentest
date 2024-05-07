<!-- Section Paddings -->
<div class="oxygen-control-row">
	<div class='oxygen-control-wrapper oxygen-padding-controls'
		ng-if="isActiveName('ct_section')">
		<label class='oxygen-control-label'><?php _e("Padding", "component-theme"); ?></label>
		
		<div class='oxygen-control'>

			<div class='oxygen-four-sides-measure-box'>

				<?php $this->measure_box('container-padding-top','',true,true,false,"show_indicator"); ?>
				<?php $this->measure_box('container-padding-right','',true,true,false,"show_indicator"); ?>
				<?php $this->measure_box('container-padding-bottom','',true,true,false,"show_indicator"); ?>
				<?php $this->measure_box('container-padding-left','',true,true,false,"show_indicator"); ?>
				<div class="oxygen-flex-line-break"></div>
				<div class="oxygen-apply-all-trigger">
					<?php _e("apply all »", "oxygen"); ?>
				</div>

			</div>
			<!-- .oxygen-four-sides-measure-box -->
		</div>
		<!-- .oxygen-control -->
		</div>
	<!-- .oxygen-control-wrapper -->
</div>
<!-- .oxygen-control-row -->
<!-- End of Section Paddings -->

<!-- Margins -->
<div class="oxygen-control-row">
		<!-- .oxygen-control-wrapper -->
		<div class='oxygen-control-wrapper oxygen-padding-controls oxygen-padding-centered'
		ng-if="!isActiveName('ct_section')">
		<label class='oxygen-control-label'><?php _e("Padding", "component-theme"); ?></label>
		
		<div class='oxygen-control'>

			<div class='oxygen-four-sides-measure-box'>

				<?php $this->measure_box('padding-top','',true,true,"model,change,keypress","show_indicator"); ?>
				<?php $this->measure_box('padding-right','',true,true,"model,change,keypress","show_indicator"); ?>
				<?php $this->measure_box('padding-bottom','',true,true,"model,change,keypress","show_indicator"); ?>
				<?php $this->measure_box('padding-left','',true,true,"model,change,keypress","show_indicator"); ?>
				<div class="oxygen-flex-line-break"></div>
				<div class="oxygen-apply-all-trigger">
					<?php _e("apply all »", "oxygen"); ?>
				</div>

			</div>
			<!-- .oxygen-four-sides-measure-box -->
		</div>
		<!-- .oxygen-control -->
	</div>
	<!-- .oxygen-control-wrapper -->
	<div class='oxygen-control-wrapper oxygen-margin-controls' ng-show='iframeScope.component.active.name != "ct_section"'>
		<label class='oxygen-control-label oxygen-margins-label'><?php _e("Margin", "component-theme"); ?></label>
		
		<div class='oxygen-control'>

			<div class='oxygen-four-sides-measure-box'>
				<?php $this->measure_box('margin-top','',true,true,"model,change,keypress","show_indicator"); ?>
				<?php $this->measure_box('margin-right','',true,true,"model,change,keypress","show_indicator"); ?>
				<?php $this->measure_box('margin-bottom','',true,true,"model,change,keypress","show_indicator"); ?>
				<?php $this->measure_box('margin-left','',true,true,"model,change,keypress","show_indicator"); ?>
				<div class="oxygen-flex-line-break"></div>
				<div class="oxygen-apply-all-trigger">
					<?php _e("apply all »", "oxygen"); ?>
				</div>

			</div>
			<!-- .oxygen-four-sides-measure-box -->
		</div>
		<!-- .oxygen-control -->
		</div>
</div>
<!-- .oxygen-control-row -->
<!-- End of Margins -->
<!-- .oxygen-control-row -->
<!-- End of Paddings -->