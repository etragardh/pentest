/**
 * All Header DOM manipulations here
 * 
 * @since 2.0
 */

CTFrontendBuilder.controller("ControllerHeader", function($scope, $timeout) {

    $scope.emptyHeaderRowComponent = [];

    /**
     * Check if all Header rows are empty
     *
     * @since 2.0
     */

    $scope.checkEmptyHeaderRow = function(id) {

        $scope.emptyHeaderRowComponent[id] = false;

        // find Header component
        $scope.findComponentItem($scope.componentsTree.children, id, $scope.checkEmptyHeaderRowCallback);

        if ( ! $scope.emptyHeaderRowComponent[id] ) {
            return "oxy-header-row-empty";
        }
    }


    /**
     * Check if all Header rows are empty (Callback)
     *
     * @since 2.0
     */

    $scope.checkEmptyHeaderRowCallback = function(key, headerRowComponent, id) {
        
        angular.forEach(headerRowComponent.children, function(child) {
            if ( child.children ) {
                $scope.emptyHeaderRowComponent[headerRowComponent.id] = true;
            }
            
        });
    }

    
    /**
     * Check if Header row is the only one inside the Header Builder
     *
     * @since 2.0
     */

    $scope.isLastRow = function(id) {

        if (undefined===id) {
            id = $scope.component.active.id;
        }

        var header_row = $scope.getComponentById(id);

        if (!jQuery(header_row).hasClass('oxy-header-row')) {
            return false;
        }

        return jQuery(header_row).siblings('.oxy-header-row').length === 0;
    }


    /**
     * Check if component is builtin
     *
     * @since 2.0
     */

    $scope.isBuiltIn = function(id) {

        if (undefined===id) {
            id = $scope.component.active.id;
        }

        if ($scope.component.options[id]['original']&&$scope.component.options[id]['original']['oxy_builtin']=='true') {
            return true;
        }
    }


    /**
     * Check if this is Inner Conent and we are in template edit mode
     *
     * @since 2.0
     */

    $scope.isTemplateInnerContent = function(component) {
        if (component&&component.name=="ct_inner_content"&&component.id>=100000) {
            return true;
        }
    }


});