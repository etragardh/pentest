/** 
 *  @author Jason V.
 *  @since 2.0
 */
CTFrontendBuilder.controller("ControllerDragnDropLists", function($scope, $timeout, $parentScope, $interval) {

    $scope.isHorizontal = false;

    // Make the ghost image for img element
    $scope.ghostElementForImg = new Image();
    $scope.ghostElementForImg.src = CtBuilderAjax.oxyFrameworkURI + "/toolbar/UI/images/ghost_img.png";

    /**
     * Callback for dnd-list directive
     * 
     * @since 2.0
     */
    $scope.dragoverCallback = function(id, event, external, type) {

        // Hiding oxygen resize box
        $scope.hideResizeBox();
        jQuery("body").addClass("oxygen-dragging");

        $scope.currentDragoverElement = angular.element(document.querySelector('#' +id));
        
        if( 
            $scope.selectedDragElement.attr("dnd-type") == "'ct_div_block'" && $scope.selectedDragElement.parent().attr('dnd-type') == "'oxy_dynamic_list'"
          ) {
            return false;
        }

        // Checking if div and div section is on top level
        if( 
            $scope.currentDragoverElement.attr("dnd-type") == "'ct_div_block'" && 
            $scope.selectedDragElement.attr("dnd-type") == "'ct_section'" && 
            $scope.currentDragoverElement.parent().attr("id") != "ct-builder"
          ) {
            return false;
        }

        // don't let drag columns out of parent
        if( 
            $scope.selectedDragElement.attr("dnd-type") == "'ct_div_block'" &&
            $scope.selectedDragElement.parent().attr("dnd-type") == "'ct_new_columns'"
          ) {
            if( $scope.currentDragoverElement.attr("dnd-type") != "'ct_new_columns'" ) {
                return false;
            }
        }
    
        // don't let drag tabs out of parent
        if( 
            $scope.selectedDragElement.attr("dnd-type") == "'ct_div_block'" &&
            $scope.selectedDragElement.parent().attr("dnd-type") == "'oxy_tabs'"
          ) {
            if( $scope.currentDragoverElement.attr("dnd-type") != "'oxy_tabs'" ) {
                return false;
            }
        }
        if( 
            $scope.selectedDragElement.attr("dnd-type") == "'ct_div_block'" &&
            $scope.selectedDragElement.parent().attr("dnd-type") == "'oxy_tabs_contents'"
          ) {
            if( $scope.currentDragoverElement.attr("dnd-type") != "'oxy_tabs_contents'" ) {
                return false;
            }
        }

        // Switching the value of dnd-horizontal-list for horizontal mode and vertical mode
        if( $scope.currentDragoverElement.is('[dnd-horizontal-list]') ) {
            if( $scope.currentDragoverElement.css("flex-direction") == "column" ) {
                $scope.isHorizontal = false;
            }
            else {
                $scope.isHorizontal = true;
            }
        }
        else if( $scope.currentDragoverElement.children().eq(0).is('[dnd-horizontal-list]') ) {
            if( $scope.currentDragoverElement.children().eq(0).css("flex-direction") == "column" ) {
                $scope.isHorizontal = false;
            }
            else {
                $scope.isHorizontal = true;
            }
        }

        if( !(
            $scope.currentDragoverElement.attr("dnd-type") == "'ct_div_block'" &&
            $scope.currentDragoverElement.parent().attr("dnd-type") == "'ct_new_columns'"
          ) ) {

            // Checking if the draggable element is on the 3px edge of container
            // This part needs to be placed after checking of $scope.isHorizontal
            if( $scope.onCheckEdgeOfContainer($scope.currentDragoverElement, event) ) {
                return false;
            }
        }

        // All the possible drag targets (container elements) will be given a grey outline.
        angular.element(document.querySelectorAll('.dndDragover')).removeClass("dndDragover");
        if( $scope.currentDragoverElement.attr("dnd-type") == "'ct_section'" ) {
            $scope.currentDragoverElement.children(".ct-inner-wrap").addClass("dndDragover");
        }
        else {
            $scope.currentDragoverElement.addClass("dndDragover");
        }        

        // Previewing the drop placement
        var dndTempPlace = $scope.selectedDragElementClone;
        // Removing of the duplication of dndPlaceholder which happends by 'return false' on the container edge
        // dndPlaceholder must be only one.
        var dndPlaceholderObj = angular.element(document.querySelector('.dndPlaceholder'));
        if( typeof $scope.selectedDragElement != 'undefined' || dndPlaceholderObj.children().length == 0 ) {

            if (dndPlaceholderObj.parent('.oxy-icon-box').length) {
                dndPlaceholderObj.prependTo(dndPlaceholderObj.parent('.oxy-icon-box').children('.oxy-icon-box-content').children('.oxy-icon-box-link'));
            }

            if (dndPlaceholderObj.parent('.oxy-pricing-box').length) {
                dndPlaceholderObj.prependTo(dndPlaceholderObj.parent('.oxy-pricing-box').children('.oxy-pricing-box-cta'));
            }
            
            dndPlaceholderObj.after(dndTempPlace);
            $scope.selectedDragElement.addClass("hide");

            var dndDraggingContainer = angular.element(document.querySelector('.dndDraggingContainer'));
            if(dndDraggingContainer.attr("dnd-type") == "'ct_div_block'" && dndDraggingContainer.children(":not(.dndDraggingSource)").length == 0) {
                dndDraggingContainer.addClass("emptyDefaultSize");
            }
        }
        
        // Generating the temp 'div' for storing of the new position
        if( typeof dndPlaceholderObj != 'undefined' ) {
            angular.element(document.querySelectorAll('.dndTempEmptyHeight')).removeClass("dndTempEmptyHeight");
            dndPlaceholderObj.parent().addClass("dndTempEmptyHeight");
        }        

        angular.element(document.querySelectorAll('.dndAvailableDragTarget')).removeClass("dndAvailableDragTarget");
        //Searching the immediate children of the currently active drag target
        if( $scope.currentDragoverElement.attr("dnd-type") == "'ct_section'" ) {
            $scope.currentDragoverElement.children(".ct-inner-wrap").children(".ct-div-block")
                                         .addClass("dndAvailableDragTarget");
            $scope.currentDragoverElement.children(".ct-inner-wrap").children(".ct-section").children(".ct-section-inner-wrap")
                                         .addClass("dndAvailableDragTarget");
            $scope.currentDragoverElement.children(".ct-inner-wrap").children(".ct-new-columns").children(".ct-div-block")
                                         .addClass("dndAvailableDragTarget");
        }
        else {
            $scope.currentDragoverElement.children(".ct-div-block")
                                         .addClass("dndAvailableDragTarget");
            $scope.currentDragoverElement.children(".ct-section").children(".ct-section-inner-wrap")
                                         .addClass("dndAvailableDragTarget");
            $scope.currentDragoverElement.children(".ct-new-columns").children(".ct-div-block")
                                         .addClass("dndAvailableDragTarget");
        }

        //Searching the siblings of the currently active drag target and the immediate parent element of the currently active drag target
        $scope.currentDragoverElement.parent()
                                     .addClass("dndAvailableDragTarget")
                                     .children(".ct-div-block")
                                     .addClass("dndAvailableDragTarget");
        $scope.currentDragoverElement.parent()
                                     .children(".ct-section").children(".ct-section-inner-wrap")
                                     .addClass("dndAvailableDragTarget");
        $scope.currentDragoverElement.parent()
                                     .children(".ct-new-columns").children(".ct-div-block")
                                     .addClass("dndAvailableDragTarget");

        return true;
    }
 
    /**
     * Callback for dnd-draggable directive
     * 
     * @since 2.0
     */
    $scope.dragstartCallback = function(componentId, selector, event) {

        // Getting the draggable element
        //$scope.selectedDragElement = angular.element(document.querySelector('#' +selector));
        $scope.selectedDragElement = $scope.getComponentById(componentId);

        
        $scope.selectedDragElement.parent().addClass("dndDraggingContainer");

        //force to set "min-height" to 80px in case of Layout Cell
        if( $scope.selectedDragElement.attr("dnd-type") == "'ct_div_block'" || $scope.selectedDragElement.attr("dnd-type") == "'ct_link'" ) {
            if($scope.selectedDragElement.html() == "") {
                $scope.selectedDragElement.addClass("emptyDefaultSize");
            }
        }

        //force to retain the height for an empty ct_inner_content 
        if($scope.selectedDragElement.attr("dnd-type") == "'ct_inner_content'") {
            if($scope.selectedDragElement.html() == "") {
                $scope.selectedDragElement.css({height: $scope.selectedDragElement.height()});
            }
        }

        $scope.selectedDragElement.find('iframe').each(function(index){
            var crt = angular.element(document.createElement("div"));
            crt.css({
                "background" : "rgba(0,0,0,0.5)",
                "width" : jQuery(this).outerWidth() + "px",
                "height" : jQuery(this).outerHeight() + "px"
            });

            // prevent doble height for video component as responsive container is absolutely positioned
            if ($scope.selectedDragElement.attr("dnd-type") == "'ct_video'") {
                crt.css({
                    "position" : "absolute"
                }); 
            }

            jQuery(this).replaceWith(crt);
        });
        $scope.selectedDragElementClone = $scope.selectedDragElement.clone().removeClass("dndDraggingSource dndDragging ct-active").addClass("dndDraggingCloneSource");

        
        $scope.selectedDragElementId = $scope.selectedDragElement.attr("id");

        // var h = dndDraggingContainer.outerHeight();
        // dndDraggingContainer.css({
        //     "min-height" : h + "px"
        // });
        
        var ghostPositionX = 0, ghostPositionY = 0;
        // Replacing the ghost element
        if( $scope.selectedDragElement.attr("dnd-type") == "'ct_image'" ) {
            // when drag element is image
            event.dataTransfer.setDragImage($scope.ghostElementForImg, -ghostPositionX, -ghostPositionY);
        }
        else {
            // when drag element is not image
            $scope.changeGhostImage(componentId, selector, event, ghostPositionX, ghostPositionX);
        }
    }

    /**
     * Callback for dnd-draggable directive
     * 
     * @since 2.0
     */

    $scope.dragendCallback = function(orderNum, componentName, event) {

        var dndDraggingContainer  = $scope.selectedDragElement.removeClass("hide").parent().removeClass("dndDraggingContainer");
        dndDraggingContainer.removeClass("emptyDefaultSize");

        var obj = angular.element(document.querySelectorAll('.dndDragover'));
        obj.removeClass("dndDragover");
        angular.element(document.querySelectorAll('.dndAvailableDragTarget')).removeClass("dndAvailableDragTarget");

        var dndTempPlace = $scope.selectedDragElementClone;

        // Updating of Dom Tree
        var elObj, elObjAttr, endParentObj, endParentID, startParentObj, startParentObjAttr;
        elObjAttr = $scope.selectedDragElement.attr("ng-attr-component-id");
        elObj = angular.element(parent.document.querySelector('[ng-attr-tree-id="'+elObjAttr+'"]'));

        startParentObjAttr = $scope.selectedDragElement.parent().closest("[ng-attr-component-id]").attr("ng-attr-component-id");

        if(startParentObjAttr > 100000)  { // this is a component in the outer template, while trying to edit inner content
            if ($scope.innerContentRoot&&$scope.innerContentRoot.id) {
                startParentObjAttr = $scope.innerContentRoot.id;
            } else {
                startParentObjAttr = 0;
            }
        }
        startParentObj = angular.element(parent.document.querySelector('[ng-attr-tree-id="'+startParentObjAttr+'"]'));

        endParentID = dndTempPlace.parent().closest("[ng-attr-component-id]").attr("ng-attr-component-id");

        if(endParentID != undefined) {

            if(endParentID > 100000)  { // this is a component in the outer template, while trying to edit inner content
                if ($scope.innerContentRoot&&$scope.innerContentRoot.id) {
                    endParentID = $scope.innerContentRoot.id;
                } else {
                    endParentID = 0;
                }
            }

            endParentObj = angular.element(parent.document.querySelector('[ng-attr-tree-id="'+endParentID+'"]'));

            var indexNumber = dndTempPlace.parent().children(":not(.dndDraggingSource)").index(dndTempPlace),
                builtinOffset = $scope.findComponentItem($scope.componentsTree.children, endParentID, $scope.calculateBuiltInOffset);

            dndTempPlace.parent().removeClass("dndTempEmptyHeight");
            dndTempPlace.remove();

            $scope.updateDomTreeNBuilder(elObj, endParentObj, startParentObj, indexNumber+builtinOffset);

            var component = $scope.getComponentById(elObjAttr);


            $scope.activateComponent(orderNum);
            $scope.updateBreadcrumbs(orderNum);
            $scope.adjustResizeBox();
        }
        else {
            console.log("Drag failed: not created DNDPlaceholder");
        }

        jQuery("body").removeClass("oxygen-dragging");

    }

    /**
     * Change Ghost image
     *
     * @since 2.0
     */

    $scope.changeGhostImage = function(componentId, selector, event, xPos, yPos) {

        var container = document.createElement("div");
        container.className = "dndGhostWrapper";

        var crt = document.createElement("div");
        crt.className += "dndGhostSource";
        crt.innerHTML = $scope.component.options[componentId].nicename ? $scope.component.options[componentId].nicename.replace(" (#" + componentId + ")", "") : $scope.calcDefaultComponentTitle($scope.component.options[componentId]).replace(" (#" + componentId + ")", "");

        container.appendChild(crt);

        document.querySelector('#' +selector).appendChild(container);
        event.dataTransfer.setDragImage(container, -xPos, -yPos);

        $timeout(function() {
            container.parentNode.removeChild(container);
        }, 500, false);
    }
    
    /**
     * Update Dom Tree and iframe Builder
     * 
     * @since 2.0
     */

    $scope.updateDomTreeNBuilder = function(el, endParent, startParent, newIndex) {

        var startParentId   = startParent[0].attributes['ng-attr-tree-id'].value,
            endParentId     = endParent[0].attributes['ng-attr-tree-id'].value,
            endGrandParentId= endParent.parent().attr('ng-attr-tree-id'),
            componentId     = el[0].attributes['ng-attr-tree-id'].value;

        // save parent ID in scope
        $scope.componentInsertId = endParentId;
        // save order in scope
        // $scope.oldComponentKey = 0;
        $scope.newComponentKey = newIndex;
        $scope.reorderSameParent = ( startParentId == endParentId );

        // cut component from tree
        $scope.findParentComponentItem($scope.componentsTree, componentId, $scope.cutComponentFromTree);

        // find component to paste
        $scope.findComponentItem($scope.componentsTree.children, $scope.componentInsertId, $scope.pasteComponentToTree);

        // update the parent of the pasted item
        $scope.findComponentItem($scope.componentsTree.children, componentId, $scope.updateComponentParentId);
        
        // update active parent
        $scope.findParentComponentItem($scope.componentsTree, componentId, $scope.updateCurrentActiveParent);

        var iframeEndParent = $scope.getComponentById(endParentId);
        
        if (iframeEndParent && iframeEndParent.hasClass("ct-new-columns") && startParentId==endParentId) {
            $scope.rebuildDOM(endParentId);
        }
        else if (iframeEndParent && iframeEndParent.hasClass("ct-new-columns")) {
            $scope.rebuildDOM(startParentId);
            $scope.rebuildDOM(componentId, true);
        } 
        else if (iframeEndParent && iframeEndParent.hasClass("oxy-icon-box")) {
            $scope.rebuildDOM(componentId, true);
        } 
        else {
            $scope.rebuildDOM(componentId, true);
        }

        // disable undo option
        $scope.cancelDeleteUndo();
    }

    /**
     * Check if mouse pointer is on the 3px edge of container
     * 
     * @since 2.0
     */

    $scope.onCheckEdgeOfContainer = function(obj, e) {

        var rect = obj[0].getBoundingClientRect();
        var edgeHeight = 3; // Can be changed by demand
        var isOnEdge = ( e.clientY <= rect.top + rect.height && e.clientY >= rect.top + rect.height - edgeHeight ) || 
                       ( e.clientY >= rect.top && e.clientY <= rect.top + edgeHeight ) ||
                       ( e.clientX <= rect.left + rect.width && e.clientX >= rect.left + rect.width - edgeHeight ) || 
                       ( e.clientX >= rect.left && e.clientX <= rect.left + edgeHeight );

        return isOnEdge;
    }

    /**
     * Dragstart Callback for Oxygen-resize-box dnd-draggable directive
     * 
     * @since 2.0
     */

    $scope.dragstartResizeBoxCallback = function(event,componentId) {
        
        if (undefined===componentId) {
            componentId = $scope.component.active.id;
        }

        var selector = $scope.component.options[componentId].selector;
        angular.element(document.querySelector('#oxygen-resize-box-drag-handler')).removeClass("dndDraggingSource dndDragging");
        
        //var component = angular.element('#' +selector);

        var component = $scope.getComponentById(componentId);
       

        component.addClass("dndDraggingSource dndDragging");

        $scope.dragstartCallback(componentId, selector, event);
    }

    /**
     * Dragend Callback for Oxygen-resize-box dnd-draggable directive
     * 
     * @since 2.0
     */

    $scope.dragendResizeBoxCallback = function(event,id) {

        var orderNum = id || $scope.component.active.id;
        $scope.dragendCallback(orderNum, $scope.selectedDragElementDNDType, event);
    }

})