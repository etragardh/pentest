<?php $this->settings_breadcrumbs(	
		__('Width & Breakpoints','oxygen'),
		__('Global Styles','oxygen'),
		'default-styles'); ?>

<div class="oxygen-control-row">
	<div class='oxygen-control-wrapper'>
		<label class='oxygen-control-label'><?php _e("Page Width","oxygen"); ?></label>
		<div class='oxygen-measure-box'>
			<input type="text" spellcheck="false"
                ng-model-options="{ updateOn: 'blur' }"
				ng-model="iframeScope.globalSettings['max-width']"
				ng-change="iframeScope.globalPageWidthUpdate()"/>
			<div class='oxygen-measure-box-unit-selector'>
				<div class='oxygen-measure-box-selected-unit'>px</div>
			</div>
		</div>
	</div>
</div>

<?php
$ct_breakpoints = array(
	'tablet' => __("Tablet", 'oxygen'),
	'phone-landscape' => __("Landscape", 'oxygen'),
	'phone-portrait' => __("Portrait", 'oxygen'),
);
foreach ($ct_breakpoints as $bp_name => $bp_label){
?>
<div class="oxygen-control-row">
	<div class='oxygen-control-wrapper'>
		<label class='oxygen-control-label'><?= $bp_label ?></label>
		<div class='oxygen-measure-box'>
			<input type="text" spellcheck="false"
                ng-model-options="{ updateOn: 'blur' }"
				ng-model="iframeScope.globalSettings.breakpoints['<?= $bp_name ?>']"
				ng-change="iframeScope.breakPointsUpdate('<?= $bp_name ?>')" placeholder="{{ iframeScope.globalSettingsDefaults.breakpoints['<?= $bp_name ?>'] }}"/>
			<div class='oxygen-measure-box-unit-selector'>
				<div class='oxygen-measure-box-selected-unit'>px</div>
			</div>
		</div>
	</div>
</div>
<?php
}
?>
<div class="oxygen-control-row">
	<div class='oxygen-control-wrapper'>
		<label class='oxygen-control-label'>After changing breakpoints, you must <a href="<?= admin_url('admin.php?page=oxygen_vsb_settings&tab=cache&start_cache_generation=true') ?>" target="_blank">regenerate the CSS cache</a> before they will take effect in the frontend of your site.</label>
	</div>
</div>