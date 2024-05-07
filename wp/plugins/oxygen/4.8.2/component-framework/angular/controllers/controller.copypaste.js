/**
 * Copy/Paste Controller
 * 
 * @author Elijah M.
 * @since x.x
 */

CTFrontendBuilder.controller("ControllerCopyPaste", function( $scope, $parentScope, $timeout, $interval, $window ) {

    $scope.$on('copyElement', function() {

        var activeId = $scope.component.active.id;
        var invalidComponents = [ 'ct_inner_content','ct_reusable' ];
        
        if( activeId > 0 ){
            // copy active component to $scope.componentBuffer
            $scope.findParentComponentItem($scope.componentsTree, activeId, $scope.copyComponentTreeNode);

            if(invalidComponents.indexOf( $scope.componentBuffer.name ) === -1) {
                var componentBufferStr = JSON.stringify($scope.componentBuffer);
                window.localStorage.setItem('oxygen-componentbuffer', componentBufferStr);
                $scope.showNoticeModal("<div>Element " + $scope.componentBuffer.name + " copied to Oxygen clipboard</div>", "ct-notice");
                return;
            }

            $scope.showNoticeModal("<div>Element of type " + $scope.componentBuffer.name + " can't be copied/pasted.</div>");
        } else {
            $scope.showNoticeModal("<div>Body element can't be copied/pasted.</div>");
        }

    })

    $scope.$on('pasteElement', function() {

        
        if(!window.localStorage.getItem('oxygen-componentbuffer')) {
            return;
        }

        var componentBufferStr = window.localStorage.getItem('oxygen-componentbuffer');
        var componentBuffer;

        try {
            componentBuffer = JSON.parse(componentBufferStr);
        } catch(e) {
            return;
        }

        // trying to add new empty root component to the active parent
        // we need to find the right parent and the right child component index
        var emptyComponentId = $scope.addComponent(componentBuffer.name);
        if(!emptyComponentId)
        {
            return;
        }

        var parent = $scope.component.active.parent;
        if(!parent) return;
        var parentId = parent.id;

        // update componentId and parent property
        $scope.updateNewComponentIds(componentBuffer, parent);

        // next - replace new component by $scope.componentBuffer

        // Some components are initialised with inner stuff, this timeout is for waiting
        // until these divs are added, so we can remove the component safely
        var millis = 50;

        // AJAX rendered components need a longer timeout
        if( $scope.isAJAXElement(componentBuffer.name) ) {
            millis = 1500;
        }

        $timeout(function() {
            $scope.removeActiveComponent();

            // paste component into tree (parentId)
            $scope.componentBuffer = componentBuffer;
            $scope.findComponentItem($scope.componentsTree.children, parentId, $scope.pasteComponentToTree);

            // creating DOM element from tree item
            $scope.rebuildDOM($scope.componentBuffer.id);

            $scope.activateComponent($scope.componentBuffer.id, $scope.componentBuffer.name);
        }, millis, false);

    })


})