<div class="oxy-mixed-code-button-wrap">
	<span class="oxy-mixed-code-button" ng-click="switchTab('advanced', 'code-mixed')">Mixed View</span>
</div>
<div class="oxygen-sidebar-code-editor-wrap">
	<?php 
		global $oxygen_toolbar;
		$oxygen_toolbar->codemirror6_script("code-css","oxy-code-css-cm6","css");
	?>
	<div id="oxy-code-css-cm6" class="oxy-code-cm6"></div>
</div>

<div class="oxygen-control-row oxygen-control-row-bottom-bar oxygen-control-row-bottom-bar-code-editor">
  <?php global $oxygen_toolbar; 
    $oxygen_toolbar->codemirror_theme_chooser(); ?>  
  <a href="#" class="oxygen-code-editor-apply"
    ng-click="iframeScope.applyCodeBlockCSS()">
    <?php _e("Apply Code", "oxygen"); ?>
  </a>
  <a href="#" class="oxygen-code-editor-expand"
    data-collapse="<?php _e("Collapse Editor", "oxygen"); ?>" data-expand="<?php _e("Expand Editor", "oxygen"); ?>"
    ng-click="toggleSidebar()">
    <?php _e("Expand Editor", "oxygen"); ?>
  </a>
</div>