/**
 * UI to navigate Componenets Tree: DOM Tree, breadcrumbs, up level buttons, ...
 * 
 */

CTFrontendBuilder.controller("ControllerNavigation", function($scope, $parentScope, $http, $timeout) {

    $scope.openFolders  = {};
    $scope.toggledNodes = [];


    /**
     * returns the default Title for a component to display in the DOMTree entry
     *
     * @since 0.3.3
     * @author gagan goraya
     */

    $scope.calcDefaultComponentTitle = function(item, nameOnly) {

        if (undefined==item) {
            item = $scope.findComponentItem($scope.componentsTree.children, $scope.component.active.id, $scope.getComponentItem);
        }
        
        var niceName = $scope.niceNames[item.name];

        if ( item.name == "ct_reusable" ) {
            niceName += " (post: " + item.options.view_id + ")";
        }
        else if ( item.name == "ct_woocommerce" ) {
            hookName = $scope.getWooCommerceHookNiceName(item.options.original['hook_name']);
            niceName += hookName;
        }
        
        if(!nameOnly)
            niceName += " (#" + item.id + ")";

        return niceName;
    }

    $scope.setComponentCategory = function(id, category, $event) {
        var item = $scope.findComponentItem($scope.componentsTree.children, id, $scope.getComponentItem);
        var existingCategory = item['options']['ct_category'];

        if(existingCategory === category) {
            delete item['options']['ct_category'];    
            delete $scope.component.options[id]['ct_category'];
        }
        else {
            item['options']['ct_category'] = category;
            $scope.component.options[id]['ct_category'] = category;
        }

        jQuery($event.target).closest('.ct-more-options-expanded').removeClass("ct-more-options-expanded");
    }


    $scope.openLoadFolder = function(id, name, showDesignSets, $event) {
        
        if($scope.experimental_components[name]['fresh']) { // if it is the first time
            
            if(showDesignSets) {
                angular.element($event.target).addClass('oxygen-small-progress');
            }

            $scope.getComponentsListFromSource(id, name, 
                
                function(id) { 

                    if(showDesignSets) {
                        $parentScope.tabs['components']=[]; 
                        angular.element($event.target).removeClass('oxygen-small-progress');
                    }

                    $scope.openFolder(id, name); 
                    $parentScope.applyMenuAim();
                }
            )
        }
        else {
            
            if(showDesignSets) {
                $parentScope.tabs['components']=[]; 
            }

            $scope.openFolder(id, name);
        }

    }

    /**
     * Show folder's content by its id
     * 
     * @since 0.4.0
     * @author Ilya K.
     */
    
    $scope.openFolder = function(id, name) {

        $scope.closeAllFolders();
        
        if(typeof(name) === 'undefined') {
            $scope.openFolders[id] = true;
        }
        else {
            $scope.openFolders[id] = name;   
        }

        if(id === 'categories-categories') {
            // make an ajax call to load components from all the source sites
            if(typeof($scope.libraryCategories) === 'undefined' || $scope.libraryCategories === null) {
                $scope.libraryCategories = {};
                $scope.libraryPages = {};
                $scope.getStuffFromSource($scope.processLibraryStuff);
            }
        }
        
        var timeout = $timeout(function() {
            jQuery(".oxygen-folder-"+id+" .ct-add-item-button-image", window.parent.document).each( function() {
                jQuery(this).attr("src",jQuery(this).data("src")); 
            });
        }, 0, false);

        $parentScope.applyMenuAim();
    }


    $scope.processLibraryStuff = function(data) {
        
        var data = JSON.parse(data);

        var items = data['items'];
        var key = data['key'];
        var next = parseInt(data['next']);

        if(items) {
            var components = items['components']; // deal with pages later

            if(components) {
                _.each(components, function(item) {

                    var category = item['category'];
                    
                    if(typeof(category) === 'undefined') {
                        category = 'Other'
                    }

                    $scope.libraryCategories[category] = $scope.libraryCategories[category] || {};
                    $scope.libraryCategories[category]['slug'] = btoa(category).replace(/=/g, '');
                    $scope.libraryCategories[category]['contents'] = $scope.libraryCategories[category]['contents'] || [];
                    $scope.libraryCategories[category]['contents'].push(item);

                });
            }

            var pages = items['pages']; // deal with pages later

            if(pages) {
                _.each(pages, function(item) {

                    if(item['type'] !== 'ct_template') {

                        var category = item['category'];
                        
                        if(typeof(category) === 'undefined') {
                            category = 'Other'
                        }

                        $scope.libraryPages[category] = $scope.libraryPages[category] || {};
                        $scope.libraryPages[category]['slug'] = btoa(category).replace(/=/g, '');
                        $scope.libraryPages[category]['contents'] = $scope.libraryPages[category]['contents'] || [];

                        $scope.libraryPages[category]['contents'].push(item);
                    }

                });
            }
        }


        $scope.getStuffFromSource($scope.processLibraryStuff, next);

        $parentScope.applyMenuAim();        

    }

    /**
     * Close all folders
     * 
     * @since 0.4.0
     * @author Ilya K.
     */
    
    $scope.closeAllFolders = function(id) {

        $scope.openFolders = {};
    }


    /**
     * Check if folder open
     * 
     * @since 0.4.0
     * @author Ilya K.
     */
    
    $scope.isShowFolder = function(id) {
        
        return ( $scope.openFolders[id] ) ? true : false;
    }


    /**
     * Check if has any open folder
     * 
     * @since 2.0
     * @author Ilya K.
     */
    
    $scope.hasOpenFolders = function() {

        return ( Object.keys($scope.openFolders).length > 0 ) ? true : false;
    }

});