<div class="oxygen-sidebar-code-editor-wrap">
  <div class="fake-code-mirror cm-s-default">
  	<pre class="CodeMirror-line"><span ng-show="iframeScope.isEditing('id')" style="padding-right: 0.1px;"><span class="cm-builtin">#{{iframeScope.component.options[iframeScope.component.active.id]['selector']}}{{iframeScope.currentState !== "original" ? ":"+iframeScope.currentState : ""}}</span>{</span><span ng-show="iframeScope.isEditing('custom-selector')" style="padding-right: 0.1px;"><span class="cm-builtin">{{iframeScope.selectorToEdit}}{{iframeScope.currentState !== "original" ? ":"+iframeScope.currentState : ""}}</span>{</span></pre>
  </div>
  <?php 
		global $oxygen_toolbar;
		$oxygen_toolbar->codemirror6_script("custom-css","oxy-custom-css-cm6", "css");
	?>
	<div id="oxy-custom-css-cm6" class="oxy-code-cm6"></div>

  <div class="fake-code-mirror fake-code-mirror-last cm-s-default">
  	<pre class="CodeMirror-line"><span style="padding-right: 0.1px;">}</span></pre>
  </div>

  <div class="oxygen-code-error-container"></div>
</div>

<div class="oxygen-control-row oxygen-control-row-bottom-bar oxygen-control-row-bottom-bar-code-editor">
  <?php global $oxygen_toolbar; 
    $oxygen_toolbar->codemirror_theme_chooser(); ?>
  <a href="#" class="oxygen-code-editor-apply"
    ng-click="iframeScope.applyComponentCSS()">
    <?php _e("Apply Code", "oxygen"); ?>
  </a>
  <a href="#" class="oxygen-code-editor-expand"
    data-collapse="<?php _e("Collapse Editor", "oxygen"); ?>" data-expand="<?php _e("Expand Editor", "oxygen"); ?>"
    ng-click="toggleSidebar()">
    <?php _e("Expand Editor", "oxygen"); ?>
  </a>
</div>