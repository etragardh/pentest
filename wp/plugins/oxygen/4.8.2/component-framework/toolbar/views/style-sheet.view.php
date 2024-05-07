<div class="oxygen-sidebar-code-editor-wrap oxygen-sidebar-stylesheet-editor-wrap">
  <script>
	var timeoutID;
	if (typeof($scope)!=="undefined") {
		window.currentCMWrap = new window.OxyCM.Compartment()
		window.currentCMTheme = new window.OxyCM.Compartment()
		window.currentCMEditor = new OxyCM.EditorView({
			state: OxyCM.EditorState.create({
				extensions: [
					OxyCM.basicSetup,
          			OxyCM.modules.keymap.of([OxyCM.modules.indentWithTab]),
					OxyCM.modules.css(),
					window.currentCMWrap.of($scope.iframeScope.globalCodeMirrorWrap === "true" ? [OxyCM.EditorView.lineWrapping, OxyCM.modules.search()] : OxyCM.modules.search()),
          			window.currentCMTheme.of(OxyCM.modules[$scope.iframeScope.globalCodeMirrorTheme]),
					OxyCM.EditorView.updateListener.of((v)=> {
						if(v.docChanged) {
							clearTimeout(timeoutID);
							timeoutID = setTimeout( (event) => {
								$scope.iframeScope.updateStylesheetCSS(window.currentCMEditor.state.doc.toString());
							}, 250);
						}
					})
				],
				doc: $scope.iframeScope.stylesheetToEdit['css']
			}),
			parent: document.getElementById("oxy-style-sheets-cm6")
		})
		window.currentCMEditor.contentDOM.addEventListener('blur', function(){
			$scope.iframeScope.updateStylesheetCSS(window.currentCMEditor.state.doc.toString());
		})
	}
	</script>
  <div id="oxy-style-sheets-cm6" class="oxy-code-cm6"></div>
  <div class="oxygen-code-error-container"></div>
</div>

<div class="oxygen-control-row oxygen-control-row-bottom-bar oxygen-control-row-bottom-bar-code-editor">
  <?php global $oxygen_toolbar; 
    $oxygen_toolbar->codemirror_theme_chooser(); ?>  
  <a href="#" class="oxygen-code-editor-expand"
    data-collapse="<?php _e("Collapse Editor", "oxygen"); ?>" data-expand="<?php _e("Expand Editor", "oxygen"); ?>"
    ng-click="toggleSidebar()">
    <?php _e("Expand Editor", "oxygen"); ?>
  </a>
</div>