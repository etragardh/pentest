<div id="oxygen-sidebar-code-editor-wrap" class="oxygen-sidebar-code-editor-wrap">

    <script>
        window.currentCMWrap = new window.OxyCM.Compartment()
        window.currentCMTheme = new window.OxyCM.Compartment()
	    window.mixedCMEditors = [];
    </script>

    <div class="oxy-code-editor-part">
        <div class="oxy-code-editor-part-title">PHP <span ng-click="switchTab('advanced', 'code-php')">Expand</span></div>
        <script>
            if (typeof($scope)!=="undefined") {
                window.mixedCMEditors['php'] = new OxyCM.EditorView({
                    state: OxyCM.EditorState.create({
                        extensions: [
                            OxyCM.basicSetup,
                            OxyCM.modules.keymap.of([OxyCM.modules.indentWithTab]),
                            OxyCM.modules.php(),
                            window.currentCMWrap.of($scope.iframeScope.globalCodeMirrorWrap === "true" ? [OxyCM.EditorView.lineWrapping, OxyCM.modules.search()] : OxyCM.modules.search()),
                            window.currentCMTheme.of(OxyCM.modules[$scope.iframeScope.globalCodeMirrorTheme]),
                        ],
                        doc: $scope.iframeScope.getOption('code-php')
                    }),
                    parent: document.getElementById("oxy-mixed-code-php-cm6")
                })
                window.mixedCMEditors['php'].contentDOM.addEventListener('blur', function(){
                    $scope.iframeScope.setOptionModel('code-php',window.mixedCMEditors['php'].state.doc.toString());
                    $scope.iframeScope.applyCodeBlockPHP();
                })
            }
        </script>
        <div id="oxy-mixed-code-php-cm6" class="oxy-code-editor-wrap"></div>
    </div>

    <div class="oxy-code-editor-part">
        <div class="oxy-code-editor-part-title">CSS <span ng-click="switchTab('advanced', 'code-css')">Expand</span></div>
        <script>
			var timeoutID;
            if (typeof($scope)!=="undefined") {
                window.mixedCMEditors['css'] = new OxyCM.EditorView({
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
                                        $scope.iframeScope.setOptionModel('code-css',window.mixedCMEditors['css'].state.doc.toString());
                                        $scope.iframeScope.applyCodeBlockCSS();
                                    }, 250);
                                }
                            }),
                        ],
                        doc: $scope.iframeScope.getOption('code-css')
                    }),
                    parent: document.getElementById("oxy-mixed-code-css-cm6")
                })
                window.mixedCMEditors['css'].contentDOM.addEventListener('blur', function(){
                    $scope.iframeScope.setOptionModel('code-css', window.mixedCMEditors['css'].state.doc.toString());
                    $scope.iframeScope.applyCodeBlockCSS();
                })
            }
        </script>
        <div id="oxy-mixed-code-css-cm6" class="oxy-code-editor-wrap"></div>
    </div>
  
    <div class="oxy-code-editor-part">
        <div class="oxy-code-editor-part-title">JS <span ng-click="switchTab('advanced', 'code-js')">Expand</span></div>
        <script>
            if (typeof($scope)!=="undefined") {
                window.mixedCMEditors['js'] = new OxyCM.EditorView({
                    state: OxyCM.EditorState.create({
                        extensions: [
                            OxyCM.basicSetup,
                            OxyCM.modules.keymap.of([OxyCM.modules.indentWithTab]),
                            OxyCM.modules.javascript(),
                            window.currentCMWrap.of($scope.iframeScope.globalCodeMirrorWrap === "true" ? [OxyCM.EditorView.lineWrapping, OxyCM.modules.search()] : OxyCM.modules.search()),
                            window.currentCMTheme.of(OxyCM.modules[$scope.iframeScope.globalCodeMirrorTheme]),
                        ],
                        doc: $scope.iframeScope.getOption('code-js')
                    }),
                    parent: document.getElementById("oxy-mixed-code-js-cm6")
                })
                window.mixedCMEditors['js'].contentDOM.addEventListener('blur', function(){
                    $scope.iframeScope.setOptionModel('code-js',window.mixedCMEditors['js'].state.doc.toString());
                    $scope.iframeScope.applyCodeBlockJS();
                })
            }
        </script>
        <div id="oxy-mixed-code-js-cm6" class="oxy-code-editor-wrap"></div>
    </div>
  
</div>

<script>
    
    // function updateCodePartsHeight() {
    
    //     var wrapElement         = document.getElementById("oxygen-sidebar-code-editor-wrap"),
    //         wrapElementHeight   = wrapElement.offsetHeight,
    //         codePartHeight      = Math.floor(wrapElementHeight/3);
        
    //     // had to spcify exact px numbers to make all 3 always fits inside viewport
    //     jQuery(".oxy-code-editor-part").height(codePartHeight)
    // }

    // // intially set 3 parts equally
    // setTimeout(updateCodePartsHeight, 100)
    
    // // make sure heights are correct if browser resized
    // jQuery(window).resize(function(){
    //     updateCodePartsHeight();
    // })

    // // helper to get correct distance dragged
    // var getClientY = function(e) {
    //     return e.touches ? e.touches[0].clientY : e.clientY;
    // };

    // var dragging = false,
    //     currentCodePart,
    //     previousCodePart,
    //     currentCodePartHeight,
    //     previousCodePartHeight,
    //     startOffset
    
    // // drag start
    // document.addEventListener("mousedown",(event)=>{
    //     dragging                = true
    //     currentCodePart         = jQuery(event.target).parents('.oxy-code-editor-part')
    //     previousCodePart        = jQuery(currentCodePart).prev()
    //     currentCodePartHeight   = currentCodePart.height()
    //     previousCodePartHeight  = previousCodePart.height()
    //     startOffset             = getClientY(event)
    // })
    
    // // dragging
    // document.addEventListener("mousemove",(event)=>{
    //     if (dragging) {
    //         var currentOffset               = getClientY(event),
    //             differnce                   = startOffset - currentOffset,
    //             newCurrentCodePartHeight    = currentCodePartHeight+differnce,
    //             newPreviousCodePartHeight   = previousCodePartHeight-differnce

    //         if (newCurrentCodePartHeight<100||newPreviousCodePartHeight<100) {
    //             return
    //         }
                
    //         currentCodePart.height(newCurrentCodePartHeight)
    //         previousCodePart.height(newPreviousCodePartHeight)
    //     }
    // })

    // // drag end
    // document.addEventListener("mouseup",(event)=>{
    //     dragging = false
    // })
</script>

<div class="oxygen-control-row oxygen-control-row-bottom-bar oxygen-control-row-bottom-bar-code-editor">
  <?php global $oxygen_toolbar; 
    $oxygen_toolbar->codemirror_theme_chooser(); ?>  
  <a href="#" class="oxygen-code-editor-apply"
    ng-click="iframeScope.applyCodeBlockJS(); iframeScope.applyCodeBlockPHP(); iframeScope.applyCodeBlockCSS()">
    <?php _e("Apply Code", "oxygen"); ?>
  </a>
  <a href="#" class="oxygen-code-editor-expand"
    data-collapse="<?php _e("Collapse Editor", "oxygen"); ?>" data-expand="<?php _e("Expand Editor", "oxygen"); ?>"
    ng-click="toggleSidebar()">
    <?php _e("Expand Editor", "oxygen"); ?>
  </a>
</div>
