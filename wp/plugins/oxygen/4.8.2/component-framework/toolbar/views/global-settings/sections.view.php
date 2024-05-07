					
					<?php $this->settings_breadcrumbs(	
							__('Sections & Columns','oxygen'),
							__('Global Styles','oxygen'),
							'default-styles'); ?>

					<div class="oxygen-control-row">
						<div class='oxygen-control-wrapper'>
							<label class='oxygen-control-label'><?php _e("Section Container Padding","oxygen"); ?></label>
							<div class='oxygen-control'>
								<div class='oxygen-four-sides-measure-box'>
									<div 
									class='oxygen-measure-box oxygen-measure-box-option-global-sections-top'
									ng-class="{'oxygen-measure-box-unit-none':iframeScope.getGlobalOptionUnit('global', 'sections.container-padding-top')==' '}">
										<input type="text" spellcheck="false"
											data-option="sections.container-padding-top"
											ng-model="iframeScope.globalSettings.sections['container-padding-top']"
											ng-model-options="{ debounce: 10 }"/>
                                        <?php $this->global_measure_box_unit_selector("global", "sections.container-padding-top", "px,%,em") ?>
									</div>
									<div 
									class='oxygen-measure-box oxygen-measure-box-option-global-sections-right'
									ng-class="{'oxygen-measure-box-unit-none':iframeScope.getGlobalOptionUnit('global', 'sections.container-padding-right')==' '}">
										<input type="text" spellcheck="false"
											data-option="sections.container-padding-right"
											ng-model="iframeScope.globalSettings.sections['container-padding-right']"
											ng-model-options="{ debounce: 10 }"/>
									<?php $this->global_measure_box_unit_selector("global", "sections.container-padding-right", "px,%,em") ?>
									</div>
									<div 
									class='oxygen-measure-box oxygen-measure-box-option-global-sections-bottom'
									ng-class="{'oxygen-measure-box-unit-none':iframeScope.getGlobalOptionUnit('global', 'sections.container-padding-bottom')==' '}">
										<input type="text" spellcheck="false"
											data-option="sections.container-padding-bottom"
											ng-model="iframeScope.globalSettings.sections['container-padding-bottom']"
											ng-model-options="{ debounce: 10 }"/>
                                        <?php $this->global_measure_box_unit_selector("global", "sections.container-padding-bottom", "px,%,em") ?>
									</div>
									<div 
									class='oxygen-measure-box oxygen-measure-box-option-global-sections-left'
									ng-class="{'oxygen-measure-box-unit-none':iframeScope.getGlobalOptionUnit('global', 'sections.container-padding-left')==' '}">
										<input type="text" spellcheck="false"
											data-option="sections.container-padding-left"
											ng-model="iframeScope.globalSettings.sections['container-padding-left']"
											ng-model-options="{ debounce: 10 }"/>
									<?php $this->global_measure_box_unit_selector("global", "sections.container-padding-left", "px,%,em") ?>
									</div>
									<div class="oxygen-flex-line-break"></div>
									<div class="oxygen-apply-all-trigger">
										<?php _e("apply all »", "oxygen"); ?>
									</div>
								</div>
								<!-- .oxygen-four-sides-measure-box -->
							</div>
						</div>
					</div>


					<div class="oxygen-control-row">
						<div class='oxygen-control-wrapper'>
							<label class='oxygen-control-label'><?php _e("Columns Padding","oxygen"); ?></label>
							<div class='oxygen-control'>
								<div class='oxygen-four-sides-measure-box'>
									<div 
									class='oxygen-measure-box oxygen-measure-box-option-global-columns-top'
									ng-class="{'oxygen-measure-box-unit-none':iframeScope.getGlobalOptionUnit('global', 'columns.padding-top')==' '}"
									>
										<input type="text" spellcheck="false"
											data-option="columns.padding-top"
											ng-model="iframeScope.globalSettings.columns['padding-top']"
											ng-model-options="{ debounce: 10 }"/>
										<?php $this->global_measure_box_unit_selector("global", "columns.padding-top", "px,%,em") ?>
									</div>
									<div 
									class='oxygen-measure-box oxygen-measure-box-option-global-columns-right'
									ng-class="{'oxygen-measure-box-unit-none':iframeScope.getGlobalOptionUnit('global', 'columns.padding-right')==' '}"
									>
										<input type="text" spellcheck="false"
											data-option="columns.padding-right"
											ng-model="iframeScope.globalSettings.columns['padding-right']"
											ng-model-options="{ debounce: 10 }"/>
											<?php $this->global_measure_box_unit_selector("global", "columns.padding-right", "px,%,em") ?>
									</div>
									<div 
									class='oxygen-measure-box oxygen-measure-box-option-global-columns-bottom'
									ng-class="{'oxygen-measure-box-unit-none':iframeScope.getGlobalOptionUnit('global', 'columns.padding-bottom')==' '}"
									>
										<input type="text" spellcheck="false"
											data-option="columns.padding-bottom"
											ng-model="iframeScope.globalSettings.columns['padding-bottom']"
											ng-model-options="{ debounce: 10 }"/>
										<?php $this->global_measure_box_unit_selector("global", "columns.padding-bottom", "px,%,em") ?>
									</div>
									<div 
									class='oxygen-measure-box oxygen-measure-box-option-global-columns-left'
									ng-class="{'oxygen-measure-box-unit-none':iframeScope.getGlobalOptionUnit('global', 'columns.padding-left')==' '}"
									>
										<input type="text" spellcheck="false"
											data-option="columns.padding-left"
											ng-model="iframeScope.globalSettings.columns['padding-left']"
											ng-model-options="{ debounce: 10 }"/>
											<?php $this->global_measure_box_unit_selector("global", "columns.padding-left", "px,%,em") ?>
									</div>
									<div class="oxygen-flex-line-break"></div>
									<div class="oxygen-apply-all-trigger">
										<?php _e("apply all »", "oxygen"); ?>
									</div>
								</div>
								<!-- .oxygen-four-sides-measure-box -->
							</div>
						</div>
					</div>