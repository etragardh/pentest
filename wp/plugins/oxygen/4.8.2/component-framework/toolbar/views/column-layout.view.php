<div class='oxygen-control-row'
	ng-show="isActiveName('ct_new_columns')&&iframeScope.isEmptyComponent()">

	<div class='oxygen-control-wrapper'>
		<label class='oxygen-control-label'><?php _e("Column Layout", "oxygen"); ?></label>

		<div class='oxygen-control'>

			<div class='oxygen-icon-button-list oxygen-icon-button-list-column-chooser'>
				
				<div class='oxygen-icon-button-list-option'>
					<div class='oxygen-icon-button-list-option-icon-wrapper'
						ng-click="iframeScope.addPresetColumns([50,50])">
						<div class='oxygen-columns-icon'>
							<div class='columns-50'></div>
							<div class='columns-50'></div>
						</div>
					</div>
					<div class='oxygen-icon-button-list-option-label'>
						50 50
					</div>
				</div>

				<div class='oxygen-icon-button-list-option'>
					<div class='oxygen-icon-button-list-option-icon-wrapper'
						ng-click="iframeScope.addPresetColumns([33.33,33.33,33.34])">
						<div class='oxygen-columns-icon'>
							<div class='columns-33'></div>
							<div class='columns-33'></div>
							<div class='columns-33'></div>
						</div>
					</div>
					<div class='oxygen-icon-button-list-option-label'>
						33 33 33
					</div>
				</div>

				<div class='oxygen-icon-button-list-option'>
					<div class='oxygen-icon-button-list-option-icon-wrapper'
						ng-click="iframeScope.addPresetColumns([25,25,25,25])">
						<div class='oxygen-columns-icon'>
							<div class='columns-25'></div>
							<div class='columns-25'></div>
							<div class='columns-25'></div>
							<div class='columns-25'></div>
						</div>
					</div>
					<div class='oxygen-icon-button-list-option-label'>
						25 25 25 25
					</div>
				</div>


				<div class='oxygen-icon-button-list-option'>
					<div class='oxygen-icon-button-list-option-icon-wrapper'
						ng-click="iframeScope.addPresetColumns([25,50,25])">
						<div class='oxygen-columns-icon'>
							<div class='columns-25'></div>
							<div class='columns-50'></div>
							<div class='columns-25'></div>
						</div>
					</div>
					<div class='oxygen-icon-button-list-option-label'>
						25 50 25
					</div>
				</div>


				<div class='oxygen-icon-button-list-option'>
					<div class='oxygen-icon-button-list-option-icon-wrapper'
						ng-click="iframeScope.addPresetColumns([60,40])">
						<div class='oxygen-columns-icon'>
							<div class='columns-60'></div>
							<div class='columns-40'></div>
						</div>
					</div>
					<div class='oxygen-icon-button-list-option-label'>
						60 40
					</div>
				</div>


				<div class='oxygen-icon-button-list-option'>
					<div class='oxygen-icon-button-list-option-icon-wrapper'
						ng-click="iframeScope.addPresetColumns([40,60])">
						<div class='oxygen-columns-icon'>
							<div class='columns-40'></div>
							<div class='columns-60'></div>
						</div>
					</div>
					<div class='oxygen-icon-button-list-option-label'>
						40 60
					</div>
				</div>


			</div>
		</div>
		<!-- .oxygen-control -->
	</div>
</div>