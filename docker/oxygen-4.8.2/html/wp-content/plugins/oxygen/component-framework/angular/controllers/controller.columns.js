/**
 * All columns DOM manipulations here
 * 
 */

CTFrontendBuilder.controller("ControllerColumns", function($scope, $parentScope, $timeout) {

    $scope.columns = [];
    $scope.emptyColumnsComponent = [];
    $scope.updateColumnsOnAdd = true;

    /**
     * Set columns inside given Columns component
     *
     * @since 0.1.5
     */
    
    $scope.setColumns = function(id, item) {
        
        var columnsCount    = item.children.length,
            difference      = $scope.columns[id] - columnsCount,
            lastColumn      = item.children[columnsCount-1];

        if ( difference > 0 ) {
            for (var i = 0; i < difference; i++) {
                $scope.addColumn(id);
            };
        }

        if ( difference < 0 ) {
            for (var i = 0; i > difference; i--) {
                $scope.removeColumn(lastColumn.id);
            };
        }
    }
    

    /**
     * Update columns inside given Columns component
     *
     * @since 0.1.5
     */
    
    $scope.updateColumns = function(id) {
        
        $scope.findComponentItem($scope.componentsTree.children, id, $scope.setColumns);
    }


    /**
     * Add a column to currently active Columns component
     *
     * @since 0.1.5
     */

    $scope.addColumn = function(id) {

        //console.log('addColumn');

        $scope.applyComponentDefaultOptions($scope.component.id, "ct_column");

        var componentName       = "ct_column",
            columnsComponent    = $scope.getActiveComponent(),
            columnTemplate      = $scope.getComponentTemplate(componentName, $scope.component.id);

        // create column tree node
        column = {
            id : $scope.component.id, 
            name : componentName
        };

        // insert to Components Tree
        $scope.findComponentItem($scope.componentsTree.children, $scope.component.active.id, $scope.insertComponentToTree, column);

        // update all columns widths
        $scope.updateColumnsOnAdd(columnsComponent, columnTemplate);

        // activate column and update options
        //$scope.activateComponent(column.id, column.name);

        // increment id
        $scope.component.id++;

        // activate columns component back
        $scope.activateComponent(id, "ct_columns");
    };


    /**
     * Remove column from currently active Columns component
     * 
     * @since 0.1.5
     */

    $scope.removeColumn = function(id) {

        if ( $scope.columns[$scope.component.active.parent.id] == 1 ) {
            alert("You can not delete the last column");
            return false;
        } else {
            $scope.columns[$scope.component.active.parent.id]--;
        }
        
        var column = $scope.getComponentById(id),
            columnsComponent = column.parent();

        // remove from DOM
        $scope.updateColumnsOnRemove(columnsComponent, column);

        // remove from Components Tree
        $scope.findParentComponentItem($scope.componentsTree, id, $scope.cutComponentFromTree);
    }


    /**
     * Update columns widths and remove given column
     *
     * @since 0.1.6
     */

    $scope.updateColumnsOnRemove = function(columnsComponent, column) {

        if ($scope.log) {
            console.log("updateColumnsOnRemove()");
        }
        
        var columns = jQuery(columnsComponent).children('.ct-div-block'),
            componentName = "ct_div_block";

        if ( columns.length > 0 ) {
            
            var newColumnsNumber    = columns.length - 1,
                columnId            = column[0].getAttribute('ng-attr-component-id'),
                freeSpace           = parseFloat($scope.getOption("width",columnId));

            // remove column from DOM
            column.remove();

            var hasActiveClass = false;

            angular.forEach(columns, function(column) {
                    
                var // get column ID
                    columnId = column.getAttribute('ng-attr-component-id'),
                    // get width value
                    columnWidth = parseFloat($scope.getOption("width",columnId));

                if (isNaN(columnWidth)) {
                    return;
                }

                var columnObj = {};
                $scope.findComponentItem($scope.componentsTree.children, columnId, $scope.getComponentActiveSelector, columnObj);
                if (columnObj.activeselector) {
                    hasActiveClass = true;
                }
            });

            if (hasActiveClass) {
                return false;
            }

            angular.forEach(columns, function(column) {
                    
                var // get column ID
                    columnId = column.getAttribute('ng-attr-component-id'),
                    // get width value
                    columnWidth = parseFloat($scope.getOption("width",columnId)),//parseFloat(column.style.width);
                    // calculate new value
                    columnWidth = (columnWidth + (freeSpace / newColumnsNumber) ).toFixed(2);
                
                // update scope
                $scope.setOptionModel("width", columnWidth, columnId, componentName);
                //column.setAttribute('size', columnWidth + "%");
            });

            $scope.cleanInsert(columnsComponent);
        }

        // make sure sum is always 100
        if ( componentName == "ct_div_block" ) {
            $scope.checkColumnsWidthSum(jQuery(columnsComponent).children('.ct-div-block'), componentName);
        }
        else {
            $scope.checkColumnsWidthSum(columnsComponent[0].querySelectorAll('.ct-column'), componentName);
        }
    }


    /**
     * Update columns widths when one of them changed
     *
     * @since 0.3.1
     */

    $scope.updateColumnsOnChange = function(id, oldWidth) {

        if ($scope.isEditing('class')) {
            return;
        }

        var newWidth            = $scope.getOption("width",id),
            diff                = oldWidth - parseFloat(newWidth),
            column              = $scope.getComponentById(id),
            columnsComponent    = column.parent(),
            columns             = columnsComponent[0].querySelectorAll('.ct-column');

        if (parseFloat(newWidth)>100||parseFloat(newWidth)<0) {
            return;
        }

        if (newWidth==="") {
            return;
        }

        if (isNaN(parseFloat(newWidth))) {
            return;
        }

        if ($parentScope.isActiveName("ct_div_block")) {

            if (!$parentScope.isActiveParentName("ct_columns")&&!$parentScope.isActiveParentName("ct_new_columns")) {
                return false;
            }

            columns = jQuery(columnsComponent).children('.ct-div-block');
            componentName = "ct_div_block";
        }
        else {
            componentName = "ct_column";
        }

        if ($scope.log) {
            console.log("updateColumnsOnChange()", oldWidth, newWidth);
        }

        // get right column
        var columnToChange = column.next();

        // if no right column, get left
        if (!columnToChange[0]) {
            columnToChange = column.prev();            
        }

        // we have only one column???
        if (!columnToChange[0]) {
            return false;
        }
        
        var columnToChangeId    = columnToChange[0].getAttribute('ng-attr-component-id'),
            columnToChangeWidth = parseFloat($scope.getOption("width",columnToChangeId));

        if (isNaN(columnToChangeWidth)) {
            return;
        }

        // we make column wider
        if ( diff < 0 ) {

            // we have enough width to subtract
            if (columnToChangeWidth > Math.abs(diff)) {

                var newColumnWidth = (columnToChangeWidth + diff).toFixed(2);
                $scope.setOptionModel("width", newColumnWidth, columnToChangeId, componentName);
            }
            // make all other columns equal width
            else {

                if (columns.length > 0) {
            
                    var columnsNumber   = columns.length - 1,
                        freeSpace       = 100 - newWidth,
                        newColumnWidth  = (freeSpace / columnsNumber).toFixed(2);

                    angular.forEach(columns, function(column) {
                            
                        var columnId = column.getAttribute('ng-attr-component-id');

                        if (columnId != id) {
                            // update scope
                            $scope.setOptionModel("width", newColumnWidth, columnId, componentName);
                        }
                    });
                }
            }
        }

        // we make column thiner
        if ( diff > 0 ) {
            var newColumnWidth = (columnToChangeWidth + diff).toFixed(2);
            $scope.setOptionModel("width", newColumnWidth, columnToChangeId, componentName);
        }

        // make sure sum is always 100
        $scope.checkColumnsWidthSum(columns,componentName,id);
    }


    /**
     * Update columns widths and add given column
     *
     * @since 0.1.6
     */

    $scope.updateColumnsOnAdd = function(columnsComponent, column) {

        if ($scope.log) {
            console.log("updateColumnsOnAdd()");
        }
        
        var columns = columnsComponent[0].querySelectorAll('.ct-column');
        if ( columns.length > 0 ) {
            
            var columnsNumber       = columns.length,
                columnsWrap         = angular.element(columns[0]).parent(),
                columnsWrapWidth    = columnsWrap[0].offsetWidth,
                newColumnWidth      = (100 / (columnsNumber+1)).toFixed(2);

            var hasActiveClass = false;

            angular.forEach(columns, function(column) {
                    
                var // get column ID
                    columnId = column.getAttribute('ng-attr-component-id'),
                    // get width value
                    columnWidth = parseFloat($scope.getOption("width",columnId));

                if (isNaN(columnWidth)) {
                    return;
                }

                var columnObj = {};
                $scope.findComponentItem($scope.componentsTree.children, columnId, $scope.getComponentActiveSelector, columnObj);
                if (columnObj.activeselector) {
                    hasActiveClass = true;
                }
            });

            if (hasActiveClass) {
                return false;
            }

            angular.forEach(columns, function(column) {
                var // get column ID
                    columnId = column.getAttribute('ng-attr-component-id'),
                    // get width value
                    columnWidth = parseFloat($scope.getOption("width",columnId)), //parseFloat(column.style.width);
                    // calculate new value
                    columnWidth = (((100 - newColumnWidth) / 100) * columnWidth).toFixed(2);
                
                // update scope
                $scope.setOptionModel("width", columnWidth, columnId, "ct_column");
            });

            // update new column width
            $scope.setOptionModel("width", newColumnWidth, $scope.component.id, "ct_column");
        }
        
        var innerWrap = $scope.getInnerWrap(columnsComponent);
        innerWrap.append(column);
        
        $scope.cleanInsert(columnsComponent);

        // make sure sum is always 100
        $scope.checkColumnsWidthSum(columnsComponent[0].querySelectorAll('.ct-column'));
    }

    
    /**
     * Check columns sum to be 100% and adjust last column if needed
     *
     * @since 0.3.1
     */
    
    $scope.checkColumnsWidthSum = function(columns, componentName, id) {

        if (columns===undefined) {
            return;
        }

        if ($scope.isEditing('class')) {
            return;
        }

        if (componentName==undefined) {
            componentName = "ct_column"
        }

        if ($scope.log) {
            console.log("checkColumnsWidthSum()", columns, componentName)
        }

        if (columns.length > 0) {

            var widthSum = 0;

            // calculate sum
            angular.forEach(columns, function(column) {

                var columnId;

                // check if tree node
                if (column.options){
                    columnId = column.id;
                }
                else {
                    columnId = column.getAttribute('ng-attr-component-id');
                }

                var columnWidth = parseFloat($scope.getOption("width",columnId));
                
                if (isNaN(columnWidth)) {
                    return;
                } 

                widthSum += columnWidth;
            });

            // calculate the diff
            var lastDiff = parseFloat(100 - widthSum).toFixed(2);

            // get last column
            var lastColumnId;
    
            // check if tree node
            if (columns[0] && columns[0].name == componentName){
                lastColumnId = jQuery(columns).last()[0].id;
                if (id==lastColumnId) {
                    lastColumnId = jQuery(columns).last().prev()[0].id;
                }
            }
            // or jQuery object
            else {
                lastColumnId = jQuery(columns).last()[0].getAttribute("ng-attr-component-id");
                if (id==lastColumnId) {
                    lastColumnId = jQuery(columns).last().prev()[0].getAttribute("ng-attr-component-id");
                }
            }

            var lastColumnWidth     = parseFloat($scope.getOption("width",lastColumnId)),
                newLastColumnWidth  = parseFloat(lastColumnWidth) + parseFloat(lastDiff);

            if (isNaN(newLastColumnWidth)) {
                return;
            }

            // update last column
            $scope.setOptionModel("width", newLastColumnWidth.toFixed(2), lastColumnId, componentName);
            //console.log(widthSum, lastDiff, lastColumnId);
        }
    }


    /**
     * Check if all columns are empty
     *
     * @since 0.2.3
     */

    $scope.checkEmptyColumns = function(id) {

        $scope.emptyColumnsComponent[id] = false;

        // find columns component
        $scope.findComponentItem($scope.componentsTree.children, id, $scope.checkEmptyColumnsCallback);

        if ( ! $scope.emptyColumnsComponent[id] ) {
            return "ct-columns-empty";
        }
    }


    /**
     * Check if all columns are empty (Callback)
     *
     * @since 0.2.3
     */

    $scope.checkEmptyColumnsCallback = function(key, columnsComponent, id) {
        
        angular.forEach(columnsComponent.children, function(column) {

            if ( column.children ) {
                $scope.emptyColumnsComponent[columnsComponent.id] = true;
            }
        });
    }

});