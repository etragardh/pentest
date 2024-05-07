/**
 * All tree manipulations here
 * 
 */

CTFrontendBuilder.controller("ComponentsTree", function($scope, $parentScope, $timeout, $interval) {

    /**
     * Components Tree object. Contain all data needed to generate DOM.
     * Passed to WordPress as JSON on save.
     * 
     */
    
    // Example sctructure
    $scope.componentsTreeExample = {

        'name' : 'root',
        'children': [ 
                {
                    'id' : 1,
                    'name' : 'section',
                    'options' : {
                            'ct_parent': 0,
                            'ct_id': 1,
                            'original' : {
                                'background' : '#515151',
                            },
                            'hover' : {
                                'background' : '#cccccc',
                            }
                        },
                    'children': [
                        {
                            'id' : 2,
                            'name' : 'button',
                            'options' : {
                                'ct_parent': 1,
                                'ct_id': 2,
                                'original' : {
                                        'value' : 'Click Me!',
                                    },
                            }
                        }
                    ],
                },
                {
                    'id' : 3,
                    'name' : 'section',
                    'options' : {
                        'ct_parent': 0,
                        'ct_id': 1,
                        'original' : {
                                'background' : '#515151',
                            },
                        'hover' : {
                                'background' : '#333333',
                            },
                        'media' : [
                            {   
                                'size' : "748px",
                                'original' : {
                                    'background' : '#929292',
                                },
                            },
                        ]
                        },
                    'children': [
                        { 
                            'id' : 4,
                            'name' : 'headline',
                            'options' : {
                                'ct_parent': 3,
                                'ct_id': 4,
                                'original' : {
                                    'tag' : 'h3',
                                    'text' : 'I am a headline!',
                                    
                                },
                            }
                        },
                    ]
                },
            ]
        }

    // Tree
    $scope.componentsTree = [
        {
            'id' : 0,
            'name' : 'root',
            'depth' : 0
        }
    ];


    $scope.findComponentByName = function(node, name, callback, variable) {

        var returnVal;

        isBreak = false;
        
        angular.forEach(node, function(item) {

            if ( !isBreak ) {
                if ( item.name == name ) {
                    // do something if find
                    returnVal = callback(item.id, item, variable);
                    // stop the loop
                    isBreak = true;
                } 
                else {
                    // go deeper in Components Tree
                    if ( item.children ) {
                        returnVal = $scope.findComponentByName(item.children, name, callback, variable);
                    }
                }
            }

        });

        if(typeof(returnVal) !== 'undefined')
            return returnVal;
    }

    /**
     * Recursively find component in Components Tree by ID
     * and pass it to callback function
     * 
     * @since 0.1
     */

    $scope.findComponentItem = function(node, id, callback, variable) {

        var returnVal;

        if ($scope.log) {
            //console.log("findComponentItem()", node, id);
        }

        isBreak = false;

        // if root
        if ( id == 0 ) {
            returnVal = callback(id, $scope.componentsTree, variable);
            if(typeof(returnVal) === 'undefined')
                return false;
            else
                return returnVal;
        }
        
        angular.forEach(node, function(item) {

            if ( !isBreak ) {
                if ( item.id == id ) {
                    // do something if find
                    returnVal = callback(id, item, variable);
                    // stop the loop
                    isBreak = true;
                } 
                else {
                    // go deeper in Components Tree
                    if ( item.children ) {
                        returnVal = $scope.findComponentItem(item.children, id, callback, variable);
                    }
                }
            }

        });

        if(typeof(returnVal) !== 'undefined')
            return returnVal;
    };


    /**
     * Finds if a component has a parent, other than the body
     * 
     * @since 0.3.4
     * @author Gagan Goraya
     */

    $scope.hasParent = function(id) {
        return $scope.findParentComponentItem($scope.componentsTree, id,  function( val ) {return val;} ) > 0;
    };


    /**
     * Recursively find a parent of component in Components Tree
     * and pass it to callback function
     * 
     * @since 0.1
     * @author Ilya K.
     */

    $scope.findParentComponentItem = function(node, id, callback, variable) {

        var returnVal;

        if ($scope.log) {
            //console.log("findParentComponentItem", id);
        }

        isBreak = false;

        angular.forEach(node.children, function(item) {

            if ( !isBreak ) {
                if ( item.id == id ) {
                    // do something if find
                    returnVal = callback(item.options['ct_parent'], node, id, variable);        
                    // stop the loop
                    isBreak = true;
                } 
                else {
                    // go deeper in Components Tree
                    if ( item.children ) {
                        returnVal = $scope.findParentComponentItem(item, id, callback, variable);
                    }
                }
            }

        });

        if(typeof(returnVal) !== 'undefined')
            return returnVal;
    };


    /**
     * Remove component from Components Tree
     * 
     * @since 0.1
     * @author Ilya K.
     */

    $scope.removeComponentFromTree = function(key, item, idToRemove, component) {

        if ($scope.log) {
            console.log("removeComponentFromTree()", idToRemove);
        }

        // fix for removing link from span
        if (component && component.removeName == "ct_span" && component.parentName == "ct_link") {
            
            item.options.ct_content = 
            item.options.ct_content.replace("<span id=\"ct-placeholder-"+component.parentId+"\"></span>",
                                            "<span id=\"ct-placeholder-"+component.removeId+"\"></span>");
        }

        angular.forEach(item.children, function(child, id){
            if ( child.id == idToRemove ) {
                item.children.splice(id, 1);
                $scope.newComponentKey = id;
            }
        })

        // remove children property if there is no more children left
        if ( item.children.length == 0 ) {
            delete item.children;
        }
        
        if ( component && component.removeName == "ct_span" && component.parentName == "ct_link" ) {
            $scope.rebuildDOM(item.id);
        }
    }


    /**
     * Insert new component to given Components Tree item
     * 
     * @since 0.1
     * @author Ilya K.
     */

    $scope.insertComponentToTree = function(key, parent, component, componentFromParent) {
        
        if ($scope.log) {
            console.log("insertComponentToTree()", key, parent, component );
        }

        if ( typeof component !== 'object' ) {
            component = componentFromParent;
        }
        
        var element = $scope.getComponentById(key),
            nestable = element.attr("is-nestable");

        if (component.isBuiltIn&&parent.name=='oxy_superbox') {
            nestable = true;
        }
        
        // look for parent if component not nestable and this is not a column, span or link over the span
        if ( !nestable && component.name != "ct_column" && component.name != "ct_span" &&  
             !(component.name == "ct_link" && component.currentName == "ct_span") &&
             // allow div block to be a child of new columns
             !(component.name == "ct_div_block" && parent.name == "ct_new_columns") &&
             // allow header row to be a child of header
             !(component.name == "oxy_header_row" && parent.name == "oxy_header") &&
             // allow header left/center/right to be a child of header row
             !((component.name == "oxy_header_left"||component.name == "oxy_header_center"||component.name == "oxy_header_right") && parent.name == "oxy_header_row")
             ) {
            
            $scope.findParentComponentItem($scope.componentsTree, key, $scope.insertComponentToTree, component);
            return;
        }

        // create empty children object if not exist
        if ( !parent.children ) {
            parent.children = [];
        }

        var child = {
            'id': component.id,
            'name': component.name,
            'options': {
                'ct_id': component.id,
                'ct_parent': key,
                'selector': component.name.slice(3) + "-" + component.id + "-" + CtBuilderAjax.postId,
                'original': {},
                'nicename': iframeScope.calcDefaultComponentTitle(component)
            },
        }

        if (iframeScope.component.options[component.id]==undefined) {
            iframeScope.component.options[component.id] = {};
        }

        iframeScope.component.options[component.id]['nicename'] = child['options']['nicename'];
        
        // TODO: change to "type" parameter or something        
        if ( component.isShortcode ) {
            child.options['ct_shortcode'] = "true";
        }

        if ( component.isWidget ) {
            child.options['ct_widget'] = "true";
        }

		if ( component.isData ) {
			child.options['ct_data'] = "true";
		}

        if ( component.isSidebar ) {
            child.options['ct_sidebar'] = "true";
        }

        if ( component.isNavMenu ) {
            child.options['ct_nav_menu'] = "true";
        }

        if ( component.isBuiltIn ) {
            child.options['oxy_builtin'] = "true";
        }

        // link and span fix
        if ( component.name == "ct_link" && component.currentName == "ct_span" ) {
            
            parent.options.ct_content = 
            parent.options.ct_content.replace(  "<span id=\"ct-placeholder-"+component.currentId+"\"></span>",
                                                "<span id=\"ct-placeholder-"+component.id+"\"></span>")
        }

        // add columns number for columns component
        if ( component.name == "ct_columns" ) {
            $scope.columns[component.id] = 1;
        }
        
        var depth = $scope.calculateDepth(component, parent);

        // apply component depth if defined
        if ( depth ) {
            child.depth = depth;
        }

        // check if index specified
        if ( component.index ) {
            $scope.idToInsert = component.index;
            // deduct builtin components to get correct index
            for(key in parent.children) {
                var possibleBuiltInComponent = parent.children[key];
                if (possibleBuiltInComponent.options['oxy_builtin']=='true') {
                    $scope.idToInsert++;
                }
            } 
        }

        // paste new child
        if ( $scope.idToInsert >= 0 && $scope.idToInsert !== false) {
            parent.children.splice($scope.idToInsert, 0, child);
            $scope.idToInsert = -1;
        } else {
            parent.children.push(child);
        }

        // update columns widths
        if ( component.name == "ct_div_block" && parent.name == "ct_new_columns" && $scope.updateColumnsOnAdd ) {

            var newColumnWidth = 0,
                columnsNumber = (parent.children.length > 1) ? parent.children.length - 1 : 100,
                newColumnWidth = 100 / columnsNumber;

            $scope.setOptionModel("width-unit", "%", component.id, component.name);

            var hasActvieClass = false;

            // check if any column has active class selector
            angular.forEach(parent.children, function(column) {
                var columnObj = {};
                $scope.findComponentItem($scope.componentsTree.children, column.id, $scope.getComponentActiveSelector, columnObj);
                if (columnObj.activeselector) {
                    hasActvieClass = true;
                }
            });

            if (hasActvieClass) {
                return false;
            }

            // change all columns
            angular.forEach(parent.children, function(column) {
                // get width value
                var columnWidth = 100 / columnsNumber;
                // calculate new value
                columnWidth = (columnWidth / ((100 + newColumnWidth) / 100)).toFixed(2);
                // update width
                $scope.setOptionModel("width", columnWidth, column.id, "ct_div_block");
                //column.options.original.width = columnWidth + "%";
            });

            // make sure sum is always 100
            $scope.checkColumnsWidthSum(parent.children, "ct_div_block");
        }
    }


    /**
     * Insert a component to a parent of given item
     * 
     * @since 0.1
     */

    $scope.insertComponentToParent = function(key, item, component) {

        $scope.findComponentItem($scope.componentsTree.children, item.options.ct_parent, $scope.insertComponentToTree, component);
    }

    $scope.insertComponentToChild = function(key, item, component) { // on first nestable child

        if(item.children && item.children.length > 0 && item.children[0]['name'] == "ct_div_block") {
            $scope.findComponentItem($scope.componentsTree.children, item.children[0]['id'], $scope.insertComponentToTree, component);
        }
    }

    /**
     * Insert a component to a grand parent of given item
     * 
     * @since 0.1.6
     */

    $scope.insertComponentToGrandParent = function(key, item, component) {

        $scope.findParentComponentItem($scope.componentsTree, item.options.ct_parent, $scope.insertComponentToTree, component);
    }


    /**
     * helper function to return a component item from the tree
     * 
     * @since 1.2.0
     */

    $scope.getComponentItem = function(key, item, variable) {
        return item;
    }

    /**
     * Paste existing component to Tree (callback in componentsReorder() and wrapWithComponent())
     * 
     * @since 0.1.3
     */

    $scope.pasteComponentToTree = function(key, parent, id) {

        if ($scope.log) {
            console.log("pasteComponentToTree()", key, parent, id, $scope.componentBuffer);
        }

        $scope.componentBuffer.options.nicename = $scope.componentBuffer.options.nicename.includes('#') ? $scope.calcDefaultComponentTitle($scope.componentBuffer) : $scope.componentBuffer.options.nicename;
 
        try {

            var componentElement = $scope.getComponentById($scope.componentBuffer.id);

            if (componentElement instanceof jQuery && componentElement.hasClass('oxy-tabs-contents')) {
                tabsContents = componentElement;
            }
            else {
                tabsContents = jQuery(componentElement).find('.oxy-tabs-contents');
            }

            if (tabsContents.length) {
                var timeout = $timeout(function() {
                    var tabsWrapperID = tabsContents.attr("data-oxy-tabs-wrapper"),
                        tabsWrapper = jQuery("#"+tabsWrapperID),
                        activeClass = tabsWrapper.attr("data-oxy-tabs-active-tab-class");

                    tabsWrapper.children("."+activeClass).trigger('click');

                    $timeout.cancel(timeout);
                }, 10, false);
            }

            if(jQuery('body').hasClass('ct_inner') && (parseInt(key) === 0 || parseInt(key) > 100000)) {
                if(jQuery('.ct-inner-content.ct-component').length > 0) {
                    key = parseInt(jQuery('.ct-inner-content.ct-component').attr('ng-attr-component-id'));
                    parent = $scope.findComponentItem($scope.componentsTree.children, key, $scope.getComponentItem);
                    $scope.componentBuffer.options.ct_parent = parseInt(key);
                }
            } 

            if ( parent.name == 'ct_inner_content' && (CtBuilderAjax['query'] && CtBuilderAjax['query']['post_type'] && CtBuilderAjax['query']['post_type'] === 'ct_template') && !jQuery('body').hasClass('ct_inner') ) {
                // paste it next to the ct_inner_content

                var lastparent = parent;

                // avoid nesting inside ct_inner_content, rather go for its parent
                key = parseInt(parent.options.ct_parent);
                parent = $scope.findComponentItem($scope.componentsTree.children, key, $scope.getComponentItem);
                $scope.componentBuffer.options.ct_parent = parseInt(key);

                // paste it next to the ct_inner_content

                $scope.newComponentKey = _.indexOf(parent.children, lastparent) + 1;

            }

            if ( !$scope.componentBuffer ) {
                return false;
            }

            if ( parent.name == "ct_columns" ) {

                var columnsNumber = parent.children.length;

                // check columns number
                if ( columnsNumber == 12 ) {
                    alert("Max number of columns is 12");
                    $scope.componentBuffer = false;
                    return false;
                }
            }

            $scope.updateComponentDepth($scope.componentBuffer, parent);
            
            // check if parent already have children
            if ( !parent.children ) {
                
                parent.children = [];
                parent.children.push($scope.componentBuffer);
                $scope.newComponentKey = 1;
            } 
            else {

                if ( $scope.newComponentKey >= 0 ) {
                    parent.children.splice($scope.newComponentKey, 0, $scope.componentBuffer);
                } else {
                    parent.children.push($scope.componentBuffer);
                }
                $scope.newComponentKey++;
            }

            var componentName = "ct_column";

            if ( $scope.componentBuffer.name == "ct_div_block" ) {
                var componentName = "ct_div_block";
            }

            // update columns widths
            if ( ( $scope.componentBuffer.name == "ct_column" ||
                   ( $scope.componentBuffer.name == "ct_div_block" && parent.name == "ct_new_columns" ) 
                ) && !$scope.reorderSameParent ) {

                var hasActvieClass = false;

                // check if any column has active class selector
                angular.forEach(parent.children, function(column) {
                    var columnObj = {};
                    $scope.findComponentItem($scope.componentsTree.children, column.id, $scope.getComponentActiveSelector, columnObj);
                    if (columnObj.activeselector) {
                        hasActvieClass = true;
                    }
                });

                if (hasActvieClass) {
                    return false;
                }

                var newColumnWidth = 0,
                    columnsNumber = (parent.children.length > 1) ? parent.children.length - 1 : 100;

                if ($scope.componentBuffer.name == "ct_div_block" && ( !$scope.componentBuffer.options.original || !$scope.componentBuffer.options.original.width )) {
                    newColumnWidth = 100 / columnsNumber;
                    $scope.setOptionModel("width-unit", "%", $scope.componentBuffer.id, $scope.componentBuffer.name);
                }
                else {
                    newColumnWidth = parseFloat($scope.componentBuffer.options.original.width);
                }

                // change all columns
                angular.forEach(parent.children, function(column) {
                    // get width value
                    if ($scope.componentBuffer.name == "ct_div_block" && ( !column.options.original || !column.options.original.width )) {
                        var columnWidth = 100 / columnsNumber;
                    }
                    else {
                        var columnWidth = parseFloat(column.options.original.width);
                    }
                    // calculate new value
                    columnWidth = (columnWidth / ((100 + newColumnWidth) / 100)).toFixed(2);
                    // update width
                    $scope.setOptionModel("width", columnWidth, column.id, componentName);
                    //column.options.original.width = columnWidth + "%";
                });

                // make sure sum is always 100
                $scope.checkColumnsWidthSum(parent.children, componentName);
            }

            $scope.unsavedChanges();

        }
        catch(error) {
            console.error(error);
            var message = error.toString();
            var timeout = $timeout(function() {
                $scope.showErrorModal(0, 'There was an error. DO NOT SAVE THE PAGE. Please copy below error message and send to our support team.', message);
                $scope.$apply();
                $timeout.cancel(timeout);
            }, 10, false);
        }
    }
    

    /**
     * Calculate new element depth based on its parent depth
     * 
     * @since 0.1.7
     */

    $scope.calculateDepth = function(component, parent) {

        if (!parent.depth && parent.name == 'root') {
            parent.depth = 0;
        }

        if ( component.name == 'ct_column' && parent.name == 'ct_columns' ) {
            depth = parseInt(parent.depth);
        }
        else if ( component.name == 'oxy_header_row' || component.name == 'oxy_header_left' || component.name == 'oxy_header_center' || component.name == 'oxy_header_right' ) {
            depth = parseInt(parent.depth);
        }
        else if ( (component.name == 'ct_div_block' || component.name == 'ct_nestable_shortcode') && (parent.name == 'ct_column' || parent.name == 'ct_new_columns') ) {
            depth = parseInt(parent.depth);
        }
        else if ( component.name == 'ct_link' && parent.name == 'ct_column' ) {
            depth = parseInt(parent.depth);
        }
        else if ( component.name == 'ct_section' && parent.name == 'ct_column' ) {
            depth = parseInt(parent.depth);
        }
        else if ( component.name == 'ct_slide' ) {
            depth = parseInt(parent.depth);
        }
        else if ( component.name == 'oxy_tab' && parent.name == 'oxy_tabs' ) {
            depth = parseInt(parent.depth);
        }
        else if ( component.name == 'oxy_tab_content' && parent.name == 'oxy_tabs_contents' ) {
            depth = parseInt(parent.depth);
        }
        else {
            depth = parseInt(parent.depth) + 1;
        }

        return depth;
    }

    /**
     * Update component depth values
     * 
     * @since 0.1.7
     * @author Ilya K.
     */

    $scope.updateComponentDepth = function(component, parent) {

        var depth = $scope.calculateDepth(component, parent);

        // apply component depth if defined
        if ( depth ) {
            component.depth = depth;
        }

        // update children
        if (component.children) {
            
            angular.forEach( component.children, function(child) {
                $scope.updateComponentDepth(child,component);
            });
        }
    }


    /**
     * Update copied components IDs and selectors
     * 
     * @since 0.1.6
     * @author Ilya K.
     */

    $scope.updateNewComponentIds = function(item, parent) {

        if ($scope.log) {
            console.log("updateNewComponentIds()", item, parent);
        }

        // TODO: update only selectors that was not changed by user
        item.options.selector = item.name.slice(3) + "-" + $scope.component.id + "-" + CtBuilderAjax.postId;

        // update placeholders
        if ( parent.options && parent.options.ct_content ) {
            parent.options.ct_content = 
            parent.options.ct_content.replace(  "<span id=\"ct-placeholder-"+item.id+"\"></span>",
                                                "<span id=\"ct-placeholder-temporary-"+$scope.component.id+"\"></span>")
        }

        // update ids
        item.id                 = $scope.component.id;
        item.options.ct_id      = $scope.component.id;
        item.options.ct_parent  = parseInt(parent.id);

        if (!item.options['nicename']) {
            item.options['nicename'] = iframeScope.calcDefaultComponentTitle(item);
        }

        // update children
        if (item.children) {
            angular.forEach( item.children, function(child) {
                
                $scope.component.id++;
                
                iframeScope.activeSelectors[$scope.component.id] = iframeScope.activeSelectors[child.id];
                
                $scope.updateNewComponentIds(child,item);
                
            });
        }

        if ( item.options && item.options.ct_content ) {
            // remove placeholders for not existing children
            item.options.ct_content = item.options.ct_content.replace(new RegExp('<span id="ct-placeholder-[0-9]+"></span>',"g"),"")
            // turn temporary placeholders back to normal
            item.options.ct_content = item.options.ct_content.replace(new RegExp('ct-placeholder-temporary-',"g"),"ct-placeholder-")
        }

        // Update Tabs
        if ( item.name == 'oxy_tabs' && !$scope.tabsContentsItemToUpdate ) {
            $scope.tabsItemToUpdate = item;
        }
        if ( item.name == 'oxy_tabs_contents' && $scope.tabsItemToUpdate ) {
            var selector    = item.options.selector,
                idToUpdate  = $scope.tabsItemToUpdate.options['ct_id'];
            $scope.tabsItemToUpdate.options.original['tabs_contents_wrapper'] = selector;
            var timeout = $timeout(function() {
                $scope.rebuildDOM(idToUpdate);
                $timeout.cancel(timeout);
            }, 0, false);
            $scope.tabsItemToUpdate = false;
        }
        else if ( item.name == 'oxy_tabs_contents' && !$scope.tabsItemToUpdate ) {
            $scope.tabsContentsItemToUpdate = item;
        }
        if ( item.name == 'oxy_tabs' && $scope.tabsContentsItemToUpdate) {
            var selector = $scope.tabsContentsItemToUpdate.options.selector;
            
            item.options.original['tabs_contents_wrapper'] = selector;
            $scope.tabsContentsItemToUpdate = false;
        }
    }


    /**
     * Copy component tree node
     * 
     * @since 0.1.6
     * @author Ilya K.
     */

    $scope.copyComponentTreeNode = function(key, item, idToCopy) {

        angular.forEach( item.children, function(child, key) {

            if ( child.id == idToCopy ) {

                // process columns
                if ( child.name == "ct_column" && !$scope.reorderSameParent ) {

                    columnsNumber = item.children.length;

                    // check columns number
                    if ( columnsNumber == 12 ) {
                        alert("Max number of columns is 12");
                        $scope.componentBuffer = false;
                        return false;
                    }
                }
                
                // save in scope 
                $scope.componentBuffer = angular.copy(child);

                   // update new element ids and selector
                $scope.updateNewComponentIds($scope.componentBuffer, item);

                // save id
                $scope.newComponentKey = key+1;
            }
        })
    }


    /**
     * Cut components from Tree (callback in componentsReorder())
     * 
     * @since 0.1.3
     * @author Ilya K.
     */

    $scope.cutComponentFromTree = function(key, item, idToRemove) {

        if ($scope.log) {
            console.log("cutComponentFromTree()", item, idToRemove);
        }

        $scope.idToInsert = -1;

        angular.forEach( item.children, function(child, index){
            
            if ( child.id == idToRemove ) {

                var hasActvieClass = false;

                // process columns
                if ( ( child.name == "ct_column" ||
                     ( child.name == "ct_div_block" && item.name == "ct_new_columns") )
                    && !$scope.reorderSameParent ) {

                    newColumnsNumber    = item.children.length - 1;
                    freeSpace           = parseFloat(child.options.original.width);

                    // check if any column has active class selector
                    angular.forEach(item.children, function(column) {
                        var columnObj = {};
                        $scope.findComponentItem($scope.componentsTree.children, column.id, $scope.getComponentActiveSelector, columnObj);
                        if (columnObj.activeselector) {
                            hasActvieClass = true;
                        }
                    });

                    if (!hasActvieClass) {
                        angular.forEach(item.children, function(column) {

                            if (column.id != idToRemove) {

                                // get width value
                                columnWidth = parseFloat(column.options.original.width);
                                // calculate new value
                                columnWidth = (columnWidth + (freeSpace / newColumnsNumber) ).toFixed(2);
                                // update scope
                                //column.options.original.width = columnWidth + "%";
                                $scope.setOptionModel("width", columnWidth, column.id, "ct_column");
                            };
                        });
                    }
                }

                // update parent ID
                child.options.ct_parent = parseInt($scope.componentInsertId);

                // save in scope 
                $scope.componentBuffer = angular.copy(child);

                // remove from tree
                item.children.splice(index, 1);

                // remove children object if no children left
                if (item.children.length == 0) {
                    delete item.children;
                }

                if ( ( child.name == "ct_column" ||
                     ( child.name == "ct_div_block" && item.name == "ct_new_columns") )
                    && !$scope.reorderSameParent && !hasActvieClass) {
                    // make sure sum is always 100
                    $scope.checkColumnsWidthSum(item.children, "ct_div_block");
                }

                // save id
                $scope.idToInsert = index;
            }
        })
    }


    /**
     * update component's parent from Tree (callback in componentsReorder())
     * 
     * @since 1.2.0
     * @author Gagan Goraya.
     */

    $scope.updateComponentParentId = function(key, item) {

        if ($scope.log) {
            console.log("updateComponentParentId()", key);
        }
        
        item.options.ct_parent = parseInt($scope.componentInsertId);
        
    }

    $scope.componentsReorderTree = function( item, startParentId, endParentId, startIndex, endIndex ) {
        
        // console.log('move index ', startIndex, ' of ', startParentId, ' to position ', endIndex, ' of ', endParentId);
        
        // to account for one index being deleted
        if(startParentId === endParentId && endIndex > startIndex) {
            endIndex--;
        }

        $scope.componentsReorder( angular.element(item), endIndex, startParentId, endParentId );

    }

    /**
     * Reorder components in Tree
     * 
     * @since 0.3.1
     */

    $scope.componentsReorder = function(item, index, startParentId, endParentId, startParent, endParent) {

        if ($scope.log) {
            console.log(item, index, startParentId, endParentId, startParent, endParent);
        }
        
        try {
            var attr        = (item[0].attributes['ng-attr-tree-id']) ? 'ng-attr-tree-id' :     // when dragging in DOM tree
                                                                        'ng-attr-component-id', // when dragging component
                componentId = item[0].attributes[attr].value,
                parent      = item.parent();
            
            if(jQuery('body').hasClass('ct_inner') && (parseInt(endParentId) === 0 || parseInt(endParentId) > 100000 || typeof(endParentId) === 'undefined')) {
                //reassign the component to the ct_inner_content
                if(jQuery('.ct-inner-content.ct-component').length > 0) {
                    endParentId = parseInt(jQuery('.ct-inner-content.ct-component').attr('ng-attr-component-id'));
                }
            }

            // update ID to be actual parent ID instead of special drag area ID
            if (endParentId==99999){
                endParentId = endParent.attr('ng-attr-tree-actual-id');
                // increase index to offset the Icon builtin component 
                index++;
            }
            
            if (startParentId==99999){
                startParentId = startParent.attr('ng-attr-tree-actual-id');
            }

            // save parent ID in scope
            $scope.componentInsertId = endParentId;

            // save order in scope
            // $scope.oldComponentKey = 0;
            $scope.newComponentKey = index;
            $scope.reorderSameParent = ( startParentId == endParentId );

            // cut component from tree
            $scope.findParentComponentItem($scope.componentsTree, componentId, $scope.cutComponentFromTree);

            // find component to paste
            $scope.findComponentItem($scope.componentsTree.children, $scope.componentInsertId, $scope.pasteComponentToTree);

            // update the parent of the pasted item
            $scope.findComponentItem($scope.componentsTree.children, componentId, $scope.updateComponentParentId);

            var endParent   = $scope.getComponentById(endParentId),
                startParent = $scope.getComponentById(startParentId);

            // handle slide component
            if (endParent && endParent.hasClass("ct-slider")) {
                $scope.rebuildDOM(endParentId);
            }
            if (!$scope.reorderSameParent && startParent && startParent.hasClass("ct-slider")) {
                $scope.rebuildDOM(startParentId);
            }

            if (endParent && endParent.hasClass("ct-new-columns") && startParentId==endParentId) {
                $scope.rebuildDOM(endParentId);
            }
            else if (endParent && endParent.hasClass("ct-new-columns")) {
                $scope.rebuildDOM(endParentId);
                $scope.rebuildDOM(componentId, true);
            } 
            else {
                $scope.rebuildDOM(componentId, true);
            }

            // look if any parent should be rebuilt 
            $scope.rebuildDOMChangeParent(endParentId);
            $scope.rebuildDOMChangeParent(startParentId);

            // check if it was the last child and startParent is AJAX element
            var parent = $scope.findComponentItem($scope.componentsTree.children, startParentId, $scope.getComponentItem);
            if ( !parent.children && $scope.isAJAXElement(parent.name)) {
                $scope.rebuildDOM(startParentId);
            }

            // check if it was the first child and endParent is AJAX element
            var parent = $scope.findComponentItem($scope.componentsTree.children, endParentId, $scope.getComponentItem);
            if ( parent.children && parent.children.length === 1 && $scope.isAJAXElement(parent.name)) {
                $scope.rebuildDOM(endParentId);
            }

            // disable undo option
            $scope.cancelDeleteUndo();
        
        }
        catch(error) {
            console.error(error);
            var message = error.toString();
            $scope.showErrorModal(0, 'There was an error. DO NOT SAVE THE PAGE. Please copy below error message and send to our support team.', message);
            $scope.$apply();
        }
    }


    /**
     * Check if we have selected or active components and wrap it with new component
     * 
     * @since 0.2.4
     */

    $scope.wrapWith = function(wrapperComponentName) {

        if ( $scope.isSelectableEnabled && $scope.isDOMNodesSelected ) {
            $scope.wrapSelectedComponentWith(wrapperComponentName);
            $scope.isDOMNodesSelected = false;
        }
        else {
            $scope.wrapComponentWith(wrapperComponentName);
        }
    }

    
    /**
     * Wrap component with new component
     * 
     * @since 0.1.5
     */

    $scope.wrapComponentWith = function(wrapperComponentName, componentId, parentId) {
        
        // component id to cut
        if (undefined === componentId) {
            componentId = $scope.component.active.id;
        }

        if (undefined === parentId) {
            parentId = $scope.component.active.parent.id;
        }

        if ($scope.log) {
            console.log("wrapComponentWith()", wrapperComponentName, componentId, parentId);
        }
        
        var newComponentId = $scope.component.id;
        
        newComponent = {
            id : newComponentId, 
            name : wrapperComponentName,
            currentId : $scope.component.active.id,
            currentName : $scope.component.active.name
        }

        // set component id to insert
        $scope.componentInsertId = newComponent.id;

        // cut component
        $scope.findParentComponentItem($scope.componentsTree, componentId, $scope.cutComponentFromTree);
        $scope.removeComponentFromDOM(componentId);
        
        // insert new component to the parent of cutted component
        $scope.findComponentItem($scope.componentsTree.children, parentId, $scope.insertComponentToTree, newComponent);

        // find component to paste
        $scope.findComponentItem($scope.componentsTree.children, $scope.componentInsertId, $scope.pasteComponentToTree);

        // update current active parent
        $scope.findParentComponentItem($scope.componentsTree, componentId, $scope.updateCurrentActiveParent);
        
        if (newComponent.currentName == "ct_span") {
            $scope.rebuildDOM(parentId);
        }
        else {
            $scope.rebuildDOM(newComponent.id);
            $scope.activateComponent(newComponent.id);
        }

        // disable undo option
        $scope.cancelDeleteUndo();

        return newComponentId;
    }


    /**
     * Wrap selected component(s) with new component
     *
     * @since 0.2.4
     */
    
    $scope.wrapSelectedComponentWith = function(wrapperComponentName) {

        var parent      = jQuery("#ct-dom-tree").find('.ct-selected-dom-node').first().parent().parent(),
            parentId    = parent.attr('ng-attr-tree-id'),
            nodes       = parent.children('.ct-dom-tree-node').has('.ct-selected-dom-node'),
            ids         = [];

        // get top level selected component ids
        nodes.each(function(){
            ids.push(jQuery(this).attr('ng-attr-tree-id'));
        });

        // create wrapper component
        newComponent = {
            id : $scope.component.id,
            name : wrapperComponentName,
            currentId : parentId,
            //currentName : $scope.component.active.name
        }

        // set component id to insert
        $scope.componentInsertId = newComponent.id;

        // insert new component to the parent of cutted component
        $scope.findComponentItem($scope.componentsTree.children, parentId, $scope.insertComponentToTree, newComponent);

        for (var i = ids.length - 1, id; id = ids[i], i >= 0; i--) {
            
            // cut component
            $scope.findParentComponentItem($scope.componentsTree, id, $scope.cutComponentFromTree);
            $scope.removeComponentFromDOM(id);

            // find component to paste
            $scope.findComponentItem($scope.componentsTree.children, $scope.componentInsertId, $scope.pasteComponentToTree);
        }

        // disable undo option
        $scope.cancelDeleteUndo();

        $scope.rebuildDOM(parentId);
    }


    /**
     * Remove parent of currently active component
     *
     * @since 0.1.8
     */

    $scope.removeActiveParent = function() {

        if ($scope.log) {
            console.log("removeActiveParent");
        }

        // component id to cut
        componentId = $scope.component.active.id;
        parentId    = $scope.component.active.parent.id

        component = {
            removeId    : $scope.component.active.id,
            removeName  : $scope.component.active.name,
            parentId    : $scope.component.active.parent.id,
            parentName  : $scope.component.active.parent.name
        }

        // update active parent
        $scope.findParentComponentItem($scope.componentsTree, parentId, $scope.updateCurrentActiveParent);

        // set component id to insert
        $scope.componentInsertId = $scope.component.active.parent.id;

        // cut component
        $scope.findParentComponentItem($scope.componentsTree, componentId, $scope.cutComponentFromTree);
        $scope.removeComponentFromDOM(componentId);

        // remove parent
        $scope.findParentComponentItem($scope.componentsTree, parentId, $scope.removeComponentFromTree, component);
        $scope.removeComponentFromDOM(parentId);

        // paste component
        $scope.findComponentItem($scope.componentsTree.children, $scope.componentInsertId, $scope.pasteComponentToTree);

        // update current active parent
        $scope.findParentComponentItem($scope.componentsTree, componentId, $scope.updateCurrentActiveParent);

        // disable undo option
        $scope.cancelDeleteUndo();
        
        $scope.rebuildDOM(componentId);
    }
    

    /**
     * Callback for AJAX call. Build DOM and other stuff
     *
     * @since 0.2.3
     * @author Ilya K.
     */

    $scope.builderInit = function(tree) {
        
        if ($scope.log) {
            console.log("builderInit()", tree);
        }

        // set scope tree to the one saved in WordPress
        $scope.componentsTree = tree;

        if (tree === false) {
            alert("Error occured while trying to build a page.");
            return;
        }

        // load Elements API component's templates with AJAX
        $scope.loadComponentsTemplates();
        $scope.loadElementsPresets();

        // show builder ui
        parent.document.getElementById("oxygen-ui").style.display = "block";

        // some functions
        $scope.outputCSSOptions();

		$parentScope.current_post_meta_keys = tree.meta_keys;

        // auto save page revisions
        /*var autoSaveTimer = setInterval(function() {
            $scope.savePage(true);
        }, 1000 * 60 * 2);*/

        $scope.allSaved();

        if(angular.element('body').hasClass('ct_inner')) {

            $scope.outerTemplateData['edit_link'] = tree.outerTemplateData['edit_link'];
            $scope.outerTemplateData['template_name'] = tree.outerTemplateData['template_name'];
            
            angular.element('body').on('click', function(e) {
                // if there exists an inner content area that the user can insert components into, but mouse click is not inside that area
                if(angular.element('.ct-inner-content-workarea').length > 0 && 
                    angular.element(e.target).closest('.ct-inner-content-workarea').length < 1 &&
                    angular.element(e.target).closest('#oxygen-resize-box').length < 1 &&
                    !jQuery(this).hasClass('oxy-dragging-resize-box')) {
                    if( typeof $scope.choosingSelectorEnabled != 'undefined' && $scope.choosingSelectorEnabled == true ) return;
                    $scope.activateComponent(0);
                    $scope.component.active.name = 'ct_template';
                    $scope.component.active.parent.id = 0
                    var timeout = $timeout(function() {
                        $scope.$apply();
                        $timeout.cancel(timeout);
                    }, 0, false);
                }
            });
        }
        var timeout = $timeout(function() {
            $parentScope.adjustStickyHeaders(window);
            if (typeof oxygenVSBInitToggleState !== 'undefined') {
                oxygenVSBInitToggleState();
            }
            $timeout.cancel(timeout);
        }, 0, false);

        if (oxygenVSBInitTabs!==undefined) {
            var timeout2 = $timeout(function() {
                oxygenVSBInitTabs();
                $timeout.cancel(timeout2);
            }, 500, false);
            var timeout3 = $timeout(function() {
                oxygenVSBInitTabs();
                $timeout.cancel(timeout3);
            }, 1000, false);
        }

        if ($scope.fixShortcodes) {
            var fixShortcodesTimeout = $timeout(function() {
                if ($scope.fixShortcodesFound) {
                    $scope.showNoticeModal('<p>Broken shortcodes found and fixed. See console log.</p>');
                }
                else {
                    $scope.showNoticeModal('<p>Broken shortcodes NOT found.</p>');
                }
                $scope.$apply();
                $timeout.cancel(fixShortcodesTimeout);
            }, 5000, false);
        }
    }

    $scope.changePreview = function(post) {

        if ($parentScope.oxygenUIElement.hasClass("oxygen-unsaved-changes")) {
            if (!confirm("All unsaved changes will be lost")) return false; 
        }

        $scope.currentPreview = post.post_title;

        // clear the design
        var root = $scope.getComponentById(0);
        root.empty();
        root = null;

        $scope.template.postData = {};
        $scope.componentsTree = []

        $scope.component = {

            // currently active component
            active : {  
                id : 0,
                name : 'root',
                state : 'original', // element state like 'hover'
                parent: {
                    id : null,
                    name : ""
                }
            },
    
            // components counter
            id : 1,
    
            // all components options
            options: {
                0 : {
                    'original' : {},
                    'media' : {
                        'original' : {}
                    }
                }
            }
        }

        $scope.componentsClasses = [];
        $scope.innerContentAdded = false;
        $scope.innerContentRoot = false;

        // Clear ID styles cache
        $scope.cache.idCSS 			= "";
	    $scope.cache.idStyles 		= {};

        CtBuilderAjax.permalink = post.url;
        $scope.ajaxVar = CtBuilderAjax;

        $scope.loadAJAXVars(function(response){
            if (response.templateTitle) {
                $scope.currentPreview = response.templateTitle
                $scope.showNoticeModal("<div>The post you tried to edit is rendered by a template without an Inner Content element, so you're editing the template instead. If you'd like to edit the post directly, add an Inner Content element to the template that renders it.</div>", "ct-notice")
                //alert('changed')
            }
            $scope.loadComponentsTree(iframeScope.builderInit, response.postId)
        });

        $scope.selectedNodeType = null;
        $scope.loadStylesheets();
    }


    /**
     * Recursively find if tree node has media styles
     * 
     * @since 0.4.0
     * @author Ilya K.
     */

    $scope.findMedia = function(node, mediaName) {

        var returnVal,
            isBreak = false;
        
        angular.forEach(node, function(item) {

            if ( !isBreak ) {
                if ( item.options['media'] && 
                     item.options['media'][mediaName] && 
                     item.options['media'][mediaName][$scope.currentState] ) {
                    // do something if find
                    returnVal = true;
                    // stop the loop
                    isBreak = true;
                } 
                else {
                    // go deeper in Components Tree
                    if ( item.children ) {
                        returnVal = $scope.findMedia(item.children, mediaName);
                    }
                }
            }

        });

        if(typeof(returnVal) !== 'undefined')
            return returnVal
    }


    /**
     * Recursively find all component's built in components and return the number of this components
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.calculateBuiltInOffset = function(key, node, variable) {

        var count = 0;

        if (!node.children) {
            return count;
        }
        
        angular.forEach(node.children, function(item) {
            if ( item.options.oxy_builtin) {
                count++;
            } 
        });

        return count;
    }

    
    /**
     * Callback to a recursive function to find out if component is buitin or not
     * 
     * @since 2.0
     * @author Ilya K.
     */
    
    $scope.isBuiltinComponent = function() {
        var id = $scope.component.active.id;
        return $scope.builtinComponents[id];
    }


    /**
     * Recursively find components in Components Tree by name
     * and update tag name
     * 
     * @since 0.4.0
     * @author Ilya K.
     */

    $scope.updateTagsByName = function(id, node, variable) {

        angular.forEach(node.children, function(item) {

            if ( item.name == variable.from ) {
                // update tag
                $scope.updateTreeComponentTag(item.id, item, variable.to)
            } 

            // go deeper in Components Tree
            if ( item.children ) {
                $scope.updateTagsByName(item.id, item, variable);
            }

        });
    };

    
    /**
     * Get Component Active Selector
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.getComponentActiveSelector = function(id, node, variable) {

        variable.activeselector = node.options.activeselector;
    }


    /**
     * Check if API component parent can have passed child
     * 
     * @since 2.3
     * @author Ilya K.
     */

    $scope.canBeChild = function(parent, child) {

        if ($scope.componentsTemplates == undefined) {
            return true;
        }

        if ($scope.componentsTemplates[parent] !== undefined && 
            $scope.componentsTemplates[parent]['onlyChild'] !== undefined) {
            
            if ($scope.componentsTemplates[parent]['onlyChild']!==child) {
                return false;
            }
        }

        if ($scope.componentsTemplates[child] !== undefined && 
            $scope.componentsTemplates[child]['onlyParent'] !== undefined) {
            
            if ($scope.componentsTemplates[child]['onlyParent']!==parent) {
                return false;
            }
        }

        return true;
    }


    /**
     * Check if it is an API component
     * 
     * @since 2.3
     * @author Ilya K.
     */

    $scope.isAPIComponent = function(name) {

        if ($scope.componentsTemplates !== undefined && 
            $scope.componentsTemplates[name] !== undefined ) {
            return true;
        }

        return false;
    }
    
    /**
     * Helper to get list of element children
     * 
     * @since 3.6
     * @author Ilya K.
     */

    $scope.getElementChildren = function(id, name) {

        if (undefined==id) {
            id = $scope.component.active.id;
            name = $scope.component.active.name;
        }

        if (name=="oxy_posts_grid") {
            var easyPosts = $scope.getComponentById(id);
            if (easyPosts) {
                return easyPosts.find('.oxy-posts > *').toArray();
            }
        }

        if (name=="oxy_gallery") {
            var gallery = $scope.getComponentById(id);
            if (gallery) {
                return gallery.find('.oxy-gallery-item').toArray();
            }
        }

        if (name=="oxy_dynamic_list") {
            var repeater = $scope.getComponentById(id);
            if (repeater) {
                var children = repeater.children().toArray();
                // repeater has one hidden div we should not to use for preview
                children.splice(1,1);
                return children;
            }
        }

        var element = $scope.findComponentItem($scope.componentsTree.children, id, $scope.getComponentItem);

        return element.children ? element.children : [];
    }


    $scope.addElementsFromEmmetAbbreviation = function(event) {

        var lastEmmetComponentID = 0;

        var element = event.target;

        try {
            var emmetTree = parseEmmetAbbreviation(element.value);
        } catch (error) {
            if (error.message.toLowerCase().indexOf("unexpected character")>=0) {
                $scope.showNoticeModal("<div>Unexpected character in abbreviation. Spaces are not allowed. <a target=\"_blank\" href=\"https://docs.emmet.io/cheat-sheet/\">Click here</a> for examples of valid abbreviations</div>")
            }
            else {
                $scope.showNoticeModal("<div>"+error.message.replace(/(?:\r\n|\r|\n)/g, '<br>')+"</div>")
            }
        }

        function emmetTreeCheck(node, found) {
            for(var index in node) { 
                if (node.hasOwnProperty(index)) {

                    var element = node[index];
                    var properName = $scope.getOxygenElementName(element.name);

                    if (properName == "oxy_header") {
                        if (element.children && element.children.length) {
                            $scope.showNoticeModal("<div>Sorry, Header Builder doesn't support children elements to be added via Emmet</div>")
                            found = true
                            break
                        }
                    }

                    var restrictedElements = [
                        "ct_slide",
                        "oxy_shape_divider",
                        "oxy_header_row",
                        "oxy_header_left",
                        "oxy_header_center",
                        "oxy_header_right",
                        "ct_span",
                        "oxy_tab",
                        "oxy_tab_content",
                        "oxy_dynamic_list"
                    ]
                    if (restrictedElements.includes(properName)) {
                        $scope.showNoticeModal("<div>" + element.name + " is not allowed to be used in Emmet"+"</div>")
                        found = true;
                        break;
                    }

                    if (!properName) {
                        $scope.showNoticeModal("<div>"+element.name + " is not an Oxygen element"+"</div>")
                        found = true;
                        break;
                    }
                    else 
                    // recursevily walk children nodes
                    if (element.children && element.children.length) {
                        found = emmetTreeCheck(element.children, found)
                    }
                }
            }
            return found;
        }

        function emmetTreeWalker(node) {
            for(var index in node) { 
                if (node.hasOwnProperty(index)) {

                    var element = node[index];
                    var properName = $scope.getOxygenElementName(element.name);

                    if (properName) {

                        $scope.$digest();
                        lastEmmetComponentID = $scope.addComponent(properName)

                        // Attributes
                        if ( element.attributes !== undefined ) {

                            for(var index in element.attributes) { 
                                if (element.attributes.hasOwnProperty(index)) {
                                    
                                    var attr = element.attributes[index],
                                        attrValue = attr.value[0];
                                    
                                    if ( attr.name == "id" ) {
                                        $scope.component.options[$scope.component.active.id]['selector'] = attrValue;
                                        $scope.setOption($scope.component.active.id, $scope.component.active.name, 'selector')
                                    }
                                    
                                    else if ( attr.name == "class" ) {
                                        var isValid = $scope.validateClassName(attrValue);
                                        if (isValid) {
                                            $scope.addClassToComponent($scope.component.active.id, attrValue);
                                        };
                                    }

                                    else {
                                        $parentScope.addCustomAttribute(attr.name, attrValue)
                                    }
                                }
                            }
                        }

                        // H1,2,3,... tags
                        if ( properName == "ct_headline") {
                            if ( ["h2","h3","h4","h5","h6"].indexOf(element.name) >= 0 ) {
                                $scope.setOptionModel('tag', element.name)
                                $scope.$digest()
                                $scope.changeTag()
                            }
                        } 

                        // text tags
                        if ( properName == "ct_text_block") {
                            if ( ["p"].indexOf(element.name) >= 0 ) {
                                $scope.setOptionModel('tag', element.name)
                                $scope.$digest()
                                $scope.changeTag()
                            }
                        } 

                        if ( properName == "ct_slider" && ( !element.children || element.children.length == 0) ) {
                            $scope.waitOxygenTree(function(){
                                $scope.addComponent("ct_slide",false,true);
                                $scope.addComponent("ct_slide",false,true);
                                $scope.addComponent("ct_slide",false,true);
                            })
                        } 

                        if ( properName == "oxy_header" ) {
                            var timeout = $timeout(function() {
                                $scope.waitOxygenTree(function(){
                                    $scope.addComponent("oxy_header_row");
                                })
                                $timeout.cancel(timeout);
                            }, 0, false);
                        } 

                        if ( element.value !== undefined ) {
                            $scope.setOptionModel('ct_content', element.value[0]);
                        }
                        
                        // recursevily walk children nodes
                        if (element.children && element.children.length) {
                            emmetTreeWalker(element.children)
                        }

                        // for siblings to work
                        $scope.activateComponent($scope.component.active.parent.id);
                    }
                }
            }
        }

        if ( !emmetTree || !emmetTree.children ) {
            return
        }

        if ( emmetTreeCheck(emmetTree.children, false) ) {
            return;
        }

        emmetTreeWalker(emmetTree.children)

        $scope.activateComponent(lastEmmetComponentID)

        $parentScope.switchActionTab('componentBrowser')
        $parentScope.emmetInputFocus = true
    }

    $scope.getOxygenElementName = function(name) {

        // look tag name element registered with i.e. ct_headline
        if ($scope.niceNames[name] !== undefined) {
            return name;
        }

        name = name.replace("-", " ");

        // look nicename i.e. Heading
        var componentsKeys = Object.keys($scope.niceNames);
        for(var key in componentsKeys) { 
            if (componentsKeys.hasOwnProperty(key)) {
                var tagName = componentsKeys[key]; // "ct_headline"
                var niceName = $scope.niceNames[tagName]; // "Heading"
                if (name.toLowerCase() == niceName.toLowerCase()) {
                    return tagName;
                }
            }
        }

        // look aliases
        var aliases = [
            { "tagName" : "ct_link_text",
              "aliases" : ["a","link"] },
            { "tagName" : "ct_headline",
              "aliases" : ["h","h1","h2","h3","h4","h5","h6"] },
            { "tagName" : "ct_image",
              "aliases" : ["img"] },
            { "tagName" : "ct_text_block",
              "aliases" : ["p"] },
        ]
        
        for(var key in aliases) { 
            if (aliases.hasOwnProperty(key)) {
                var elem = aliases[key];
                if (elem.aliases.indexOf(name)>-1) {
                    return elem.tagName;
                }
            }
        }

        return false;
    };

    $scope.emmetCLIkeypress = function(char, event) {

        var element = event.target;
        var emmetAutocompleteChar = "";
        
        switch (char) {
            case 91:
                emmetAutocompleteChar = "]"
                break;
            case 40:
                emmetAutocompleteChar = ")"
                break;
            case 123:
                emmetAutocompleteChar = "}"
                break;
        }

        if (emmetAutocompleteChar) {

            var pos = element.selectionStart;
     
            element.value = element.value.slice(0, pos) + emmetAutocompleteChar + element.value.slice(pos);
            element.setSelectionRange(pos, pos);
        }
    }

});