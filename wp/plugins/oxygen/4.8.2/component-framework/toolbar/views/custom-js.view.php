<div class="oxygen-sidebar-code-editor-wrap">
	<?php 
		global $oxygen_toolbar;
		$oxygen_toolbar->codemirror6_script("custom-js","oxy-custom-js-cm6", "javascript");
	?>
	<div id="oxy-custom-js-cm6" class="oxy-code-cm6"></div>
	<div class="oxygen-code-error-container"></div>
</div>

<div class="oxygen-control-row oxygen-control-row-bottom-bar oxygen-control-row-bottom-bar-code-editor">
	<?php global $oxygen_toolbar; 
    	$oxygen_toolbar->codemirror_theme_chooser(); ?>
	<a href="#" class="oxygen-code-editor-apply"
		ng-click="iframeScope.applyComponentJS()">
		<?php _e("Apply Code", "oxygen"); ?>
	</a>
	<a href="#" class="oxygen-code-editor-expand"
		data-collapse="<?php _e("Collapse Editor", "oxygen"); ?>" data-expand="<?php _e("Expand Editor", "oxygen"); ?>"
		ng-click="toggleSidebar()">
		<?php _e("Expand Editor", "oxygen"); ?>
	</a>
</div>