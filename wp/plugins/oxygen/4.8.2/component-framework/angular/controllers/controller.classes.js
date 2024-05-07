/**
 * All Classes staff here
 * 
 */


CTFrontendBuilder.controller("ControllerClasses", function($scope, $parentScope, $timeout) {

    $scope.currentClass         = false;
    $scope.activeSelectors      = {};
    $scope.componentsClasses    = [];
  	$scope.classes              = {};
    $scope.suggestedClasses     = [];
    $scope.newcomponentclass = { name: ''};



    /**
     * Update component classes in Components Tree
     * 
     * @since 0.1.7
     * @author Ilya K.
     */

    $scope.updateTreeComponentClasses = function(key, item, removeClass) {

		// remove class
		if ( removeClass ) {
			index = item.options.classes.indexOf(removeClass);
			if ( index > -1 ) {
				// remove class
				item.options.classes.splice(index, 1);
			}
		}
		// or add it
		else {
			if ( !item.options.classes ) {
				item.options.classes = [];
			}
			item.options.classes.push($scope.currentClass);
		}
    }


    /**
     * Set Current Class and apply it's options
     * 
     * @since 0.1.7
     * @author Ilya K.
     */

    $scope.setCurrentClass = function(className, silent) {

        if ($scope.log) {
            console.log('setCurrentClass()', className);
        }
    	
        $scope.switchState('original');
        
		//$scope.disableContentEdit();

        $scope.currentClass = className;
        
        // also set the current class for the particular component in order use it when user comes back to this component
        if(className !== false) {
            $scope.activeSelectors = $scope.activeSelectors || {};
            $scope.activeSelectors[$scope.component.active.id] = className;
        }


        $scope.showClasses = false;

        if(silent)
            return;

        // apply options
        $scope.applyModelOptions();

        $scope.outputCSSOptions($scope.component.active.id);

        $parentScope.checkTabs();
   	}


    /**
     * Remove certian class from active component
     * 
     * @since 0.1.8
     * @author Ilya K.
     */

    $scope.removeComponentClass = function(className, id) {

        if ($scope.log) {
            console.log('removeComponentClass()', className, id);
        }

        $scope.switchEditToId();

        if (undefined === id) {
            id = $scope.component.active.id;
        }

        // check if component has any classes
        if ( $scope.componentsClasses[id] ) {
            // look for class we need
            key = $scope.componentsClasses[id].indexOf(className);
        }
        else {
            key = -1;
        }

        // if component already have this class applied
        if ( key > -1 ) {
            // remove this class
            $scope.componentsClasses[id].splice(key, 1);
            var remove = className;
        }

        // if this is the active selector being edited, remove it from activeselectors
        // because the component's active selector should default back to its ID
        if($scope.activeSelectors[id] === className) {
            $scope.activeSelectors[id] = false;
            $scope.switchEditToId();
        }

        // update component classes in tree
        $scope.findComponentItem($scope.componentsTree.children, id, $scope.updateTreeComponentClasses, remove);

        $scope.unsavedChanges();

        // if it is part of a dynamic list, update the component
        var oxyList = iframeScope.getComponentById(id).closest('.oxy-dynamic-list');
        
        if(oxyList.length > 0) {
            iframeScope.dynamicListAction(oxyList.attr('ng-attr-component-id'), id);
        }
    }


    /**
     * Add class to component
     * 
     * @since 0.1.8
     */

    $scope.tryAddClassToComponent = function(id) {

        $scope.cancelDeleteUndo();

        var className = $scope.newcomponentclass.name;

        if(typeof(className) === 'undefined' || className.trim() === '')
            className = prompt("Class name:");
        
        if (className != null) {

            var valid = $scope.validateClassName(className);

            if (!valid) {
                
                alert("Wrong selector name. Name must begin with an underscore (_), a hyphen (-), or a letter(a–z), followed by any number of hyphens, underscores or letters.");
                return false;
            };
            
            $scope.addClassToComponent(id, className);

            //clear the new component class name text field
            $scope.newcomponentclass.name = '';

            // if it is part of a dynamic list, update the component
            var oxyList = iframeScope.getComponentById(id).closest('.oxy-dynamic-list');
            
            if(oxyList.length > 0) {
                iframeScope.dynamicListAction(oxyList.attr('ng-attr-component-id'), id);
            }

        }
    }


    /**
     * Primary purpose is to listen for a return key input in the new class name textbox
     * 
     * @since 0.3.3
     * @author Gagan Goraya
     */

    $scope.processClassNameInput = function(e, id) {

        // create the className if it is a return key
        if(e.keyCode === 13) {
            $scope.tryAddClassToComponent(id);

            // hide the dropdown
            var timeout = $timeout(function() {
                jQuery(".oxygen-select",$parentScope.oxygenUIElement).trigger("click");
                // cancel timeout
                $timeout.cancel(timeout);
            }, 0, false);
        }

    }


    /**
     * When choose a selector is clicked for a new element
     * 
     * @since 0.3.3
     * @author Gagan Goraya
     */

    $scope.onSelectorDropdown = function() {
        // helps the new class name field gain focus
        $parentScope.ctSelectBoxFocus = true;
    }


    /**
     * Determines if a css or ID selector has been explicitly selected for editing for the current component
     * 
     * @since 0.3.3
     */

    $scope.isNotSelectedYet = function(id) {

        if($scope.activeSelectors && typeof($scope.activeSelectors[id]) !== 'undefined' )  {
            return false;
        }

        if($scope.justaddedcomponents && $scope.justaddedcomponents.indexOf(id) > -1) {
            return true;
        }

        return false;
    }

    /**
     * Add class to component
     * 
     * @since 0.1.8
     */

    $scope.addClassToComponent = function(id, className, setCurrent) {

        if ($scope.log) {
            console.log('addClassToComponent()', id, className);
        }

        if (undefined===setCurrent) {
            setCurrent = true;
        }
            
        // check if component has any classes
        if ( $scope.componentsClasses[id] ) {
            // look for the class we need
            key = $scope.componentsClasses[id].indexOf(className);
        }
        else {
            // create empty array for this component classes
            $scope.componentsClasses[id] = [];
            key = -1;
        }

        var newlyCreatedClass = false;

        // if newly created class
        if ( !$scope.classes[className] ) {    
            // create object
            $scope.classes[className] = {};
            $scope.classes[className]['original'] = {};
            newlyCreatedClass = true;
        }

        // if component already have this class applied
        if ( key > -1 ) {
            if (setCurrent) { 
                $scope.setCurrentClass(className);
            }
            return false;
        }
        else {
            // add this class to component
            $scope.componentsClasses[id].push(className);
            if (setCurrent) { 
                $scope.setCurrentClass(className);
            }
        }

        // columns default width unit check
        if (newlyCreatedClass && id==$scope.component.active.id && $parentScope.isActiveName('ct_div_block') && $parentScope.isActiveParentName('ct_new_columns')) {
            $scope.setOptionModel("width-unit", "%", id, "ct_div_block");
        }

        // update component classes in tree
        $scope.findComponentItem($scope.componentsTree.children, id, $scope.updateTreeComponentClasses);

        $scope.unsavedChanges();
    }
    

    /**
     * Add class to component not using builder UI
     * 
     * @since 2.0
     */

    $scope.addClassToComponentSafe = function(id, className) {
        
        // hack needed to properly update components class in components tree
        $scope.currentClass = className;
                    
        $scope.addClassToComponent(id, className, false)
                    
        // hack needed to properly update components class in components tree
        $scope.currentClass = false;
    }


	 /**
     * Check if component has particular class added
     * 
     * @since 0.1.7
     */

    $scope.isComponentHasClass = function(id, className) {

        // if has any classes
    	if ( $scope.componentsClasses[id] ) {
 			
            // look for this particular class
 			key = $scope.componentsClasses[id].indexOf(className);

 			if ( key > -1 ) {
 				return true;
 			}
 			else {
 				return false;
 			}
 		}
 		else {
 			return false;
 		}
    }

    /**
     * Check if component has particular class added
     * 
     * @since 3.3
     * @author Ilya K.
     */

    $scope.isComponentHasClasses = function(id) {

        if (undefined===id) {
            id = $scope.component.active.id;
        }
        if ( $scope.componentsClasses[id] && $scope.componentsClasses[id].length > 0 ) {
            return true;
        }
        else {
            return false;
        }
    }


    /**
     * Ask user if he wants to delete a class from install
     * 
     * @since 0.2.5
     */

    $scope.tryDeleteClass = function(className) {

        $scope.cancelDeleteUndo();

    	var confirmed = confirm("Delete \""+className+"\" from install? (Changes will take effect on Save)");
		
		if (!confirmed) {
			return false;
		}

        $scope.deleteClass(className);
    }


    /**
     * Delete class and all references from install
     * 
     * @since 0.1.7
     */

    $scope.deleteClass = function(className) {

        if ($scope.log) {
            console.log("deleteClass()", className);
        }

        // delete from classes
        delete $scope.classes[className];

        $scope.selectorToEdit   = false;
        $scope.currentClass     = false;

        // delete from component classes
        angular.forEach($scope.componentsClasses, function(componentClasses, componentId) {

            var key = componentClasses.indexOf(className);

            if (key > -1) {
                // remove class
                componentClasses.splice(key, 1);

                // update component classes in Tree
                $scope.findComponentItem($scope.componentsTree.children, componentId, $scope.updateTreeComponentClasses, className);
            }
        });

        if ($scope.component.active.id == -1) {
            $scope.activateComponent(0,'root')
        }

        delete $scope.cache.classStyles[$scope.currentClass];
        $scope.outputCSSOptions();

        $scope.unsavedChanges();
    }


    $scope.getGlobalConditionsClass = function(id) {
        
        var globalCondition = iframeScope.component.options[id]['model']['globalConditionsResult'];
        if(globalCondition === false && typeof(globalCondition) !== 'undefined') {
            return " ct_hidden_by_conditional_logic";
        }

        return '';
    }

    /**
     * Return all component's classes concatenated into one string
     * 
     * @since 0.2.0
     */

    $scope.getComponentsClasses = function(id, componentName) {

        if ( !iframeScope.component.options[id] ) {
            return "";
        }

        var classNames = "ct-component " + componentName; 
        var condition = iframeScope.component.options[id]['model']['conditionsresult'];
        
        if(condition !== 1 && typeof(condition) !== 'undefined') {
            classNames += " ct_hidden_by_conditional_logic";
        }

        classNames += $scope.getGlobalConditionsClass(id);
        
        if ( componentName != "ct_section" && componentName != "ct_columns" && componentName != "ct_column" ) {
            classNames += " " + componentName.replace(new RegExp("_", 'g'), "-");
        }

    	if ( $scope.componentsClasses[id] ) {

            var classesList = angular.copy($scope.componentsClasses[id]);
            
            // remove active class for components like Toggle or Tabs
            for(var key in classesList) { 
                if (classesList.hasOwnProperty(key)) {
                    var className = classesList[key];

                    if (className && className.indexOf("-tab-active") > 0 && className.indexOf("tabs") === 0) {
                        classesList[key] = null;
                    }

                    if (className && className.indexOf("-expanded") > 0 && className.indexOf("toggle-") === 0) {
                        classesList[key] = null;
                    }
                }
            }

    		classNames += " " + classesList.join(" ");
    	}

        if ( componentName == "ct_svg_icon") {
            classNames += " ct-" + $scope.component.options[id]['model']['icon-id'];
        }

        if ( componentName == "ct_link" || componentName == "ct_link_text" || componentName == "ct_link_button" ) {
            classNames += " ct-links";
        }

        if ( componentName == "ct_section" && $scope.component.options[id]['model']['video_background']) {
            classNames += " oxy-video-background";
        }

        if ( componentName == "oxy_header" && $scope.component.options[id]['model']['sticky_header']=="yes") {
            classNames += " oxy-sticky-header";
        }

        if ( componentName == "oxy_header" && $scope.component.options[id]['model']['overlay-header-above'] && $scope.component.options[id]['model']['overlay-header-above']!="never") {
            classNames += " oxy-overlay-header";
        }

        if ( componentName == "oxy_header_row" && $scope.component.options[id]['model']['hide_in_sticky']=="yes") {
            classNames += " oxygen-hide-in-sticky";
        }

        if ( componentName == "oxy_header_row" && $scope.component.options[id]['model']['show_in_sticky_only']=="yes") {
            classNames += " oxygen-show-in-sticky-only";
        }

        if ( componentName == "oxy_header_row" && $scope.component.options[id]['model']['overlay_display']=="only_show_in_overlay") {
            classNames += " oxygen-only-show-in-overlay";
        }

        if ( componentName == "oxy_header_row" && $scope.component.options[id]['model']['overlay_display']=="hide_in_overlay") {
            classNames += " oxygen-hide-in-overlay";
        }

        if ( componentName == "oxy_nav_menu" && $scope.component.options[id]['model']['dropdowns']=="on") {
            classNames += " oxy-nav-menu-dropdowns";
        }

        if ( componentName == "oxy_nav_menu" && $scope.component.options[id]['model']['menu_dropdown_arrow']=="on") {
            classNames += " oxy-nav-menu-dropdown-arrow";
        }

        if ( componentName == "oxy_nav_menu" && $scope.component.options[id]['model']['menu_responsive_dropdowns']=="on") {
            classNames += " oxy-nav-menu-responsive-dropdowns";
        }

        if ( componentName == "oxy_nav_menu" && $scope.component.options[id]['model']['menu_flex-direction']=="column") {
            classNames += " oxy-nav-menu-vertical";
        }

        if ( componentName == "oxy_gallery" ) {
            if ( $scope.component.options[id]['model']['layout']=="masonry") {
                classNames += " oxy-gallery-masonry";
            }
            else if ( $scope.component.options[id]['model']['display']=="grid") {
                classNames += " oxy-gallery-grid";
            }
            else {
                classNames += " oxy-gallery-flex";
            }

            if ( $scope.component.options[id]['model']['gallery_captions']=="yes") {
                classNames += " oxy-gallery-captions";
            }
        }

        if ( $scope.component.options[id]['model']['dont_render']=="true" ) {
            classNames += " oxy-placeholder-element";
        }

        if ( componentName == "oxy_testimonial" ) {
            if ( $scope.component.options[id]['model']['testimonial_layout']=="vertical") {
                classNames += " oxy-testimonial-vertical";
            }
            else {
                classNames += " oxy-testimonial-horizontal";
            }
        }

        if ( componentName == "oxy_superbox" ) {
            if ( $scope.component.options[id]['model']['superbox_editing_mode']!="live") {
                classNames += " oxy-superbox-editing-"+$scope.component.options[id]['model']['superbox_editing_mode'];
            }
        }
        
        return classNames;
    }


    /**
     * Add new class
     * 
     * @since 0.2.0
     */

    $scope.addClass = function(className) {

		// check if this class already added
		if ( !$scope.classes[className]) {

			$scope.classes[className] = {};
            $scope.classes[className]['original'] = {};

            return true;
        } 
        else {
        	return false;
        }
    }


    /**
     * Validate a class name
     * 
     * @since 0.2.0
     */

    $scope.validateClassName = function(name) {
    	var re = /^[a-z_-][a-z\d_-]*$/i
	    //var re = /-?[_a-zA-Z]+[_a-zA-Z0-9-]*$/i;
	    return re.test(name);
	}


    /**
     * Check component's active selector and classes to be present in Oxygen
     * and delete it from component if not
     * 
     * @since 2.2
     * @author Ilya K.
     */

    $scope.checkComponentClasses = function(id) {

        // skip root
        if (id===0||id==="0") {
            return false;
        }

        // switch active selector if it is not present in Oxygen
        var activeSelector = $scope.activeSelectors[id];
        if (activeSelector && typeof($scope.classes[activeSelector]) === 'undefined') {
            $scope.activeSelectors[id] = false;
        }
        
        // remove components classes not present in Oxygen
        if ($scope.componentsClasses[id]) {

            var classesList = angular.copy($scope.componentsClasses[id]);
            
            for (var key in classesList) { 
                if (classesList.hasOwnProperty(key)) {
                    var className = classesList[key];
                    if (typeof($scope.classes[className]) === 'undefined') {
                        $scope.removeComponentClass(className, id);
                    }
                }
            }
        }

    }

    /**
     * Update classes suggestion as user tape
     * 
     * @since 3.3
     * @author Abdelouahed E.
     */
    
    $scope.updateSuggestedClasses = function() {
        var searchQuery = $scope.newcomponentclass.name;
        $scope.suggestedClasses = [];
        
        if (searchQuery) {
            // Collect only class names from the classes object
            var classNames = Object.keys($scope.classes);
            
            if (classNames.length) {
                // Kep only classes that contains the taped string
                var suggestedClasses = classNames.filter(function(className) {
                    return className.indexOf(searchQuery) !== -1;
                });
                
                // Exclude already added classes to active component
                if ($scope.componentsClasses && $scope.component.active.id) {
                    var componentClasses = $scope.componentsClasses[$scope.component.active.id];
                    
                    if (componentClasses) {
                        suggestedClasses = suggestedClasses.filter(function(className) {
                            return componentClasses.includes(className) == false;
                        });
                    }
                }
                
                // Sort the suggestion based on search string position
                suggestedClasses.sort(function(classNameA, classNameB) {
                    var a = classNameA.indexOf(searchQuery);
                    var b = classNameB.indexOf(searchQuery);
                    
                    // Class A after class B
                    if( a > b ) {
                        return 1;
                    }
                    
                    // Class A before class B
                    if( a < b ) {
                        return -1;
                    }
                    
                    // Both classes have the same weight
                    return 0;
                });
                
                // limit the number of suggestion
                var limit = parseInt($scope.globalSettings.classSuggestionsLimit);
                
                if (limit && suggestedClasses.length > limit) {
                    suggestedClasses = suggestedClasses.slice(0, limit);
                }
                
                $scope.suggestedClasses = suggestedClasses;
            }
        }
    };
    
    /**
     * Update classes suggestion as user tape
     * 
     * @param className string
     * 
     * @since 3.3
     * @author Abdelouahed E.
     */
    $scope.addSuggestedClassToComponent = function(className) {
        $scope.newcomponentclass.name = className;
        $scope.suggestedClasses = [];
        
        $scope.tryAddClassToComponent($scope.component.active.id);
    };
    
    /**
     * 
     */
    $scope.updateClassSuggestionsLimit = function() {
        var limit = parseInt($scope.globalSettings.classSuggestionsLimit);
        
        if (!limit || limit < 0) {
            $scope.globalSettings.classSuggestionsLimit = $scope.globalSettingsDefaults.classSuggestionsLimit;
        }
        
        if ($scope.suggestedClasses.length) {
            $scope.updateSuggestedClasses();
        }
    };


    /**
     * 
     */
    $scope.isSelectorLocked = function(className) {

        if ( $scope.classes[className] && $scope.classes[className]['original']) {    
            if ($scope.classes[className]['original']['selector-locked'] == 'true') {
                return true;
            }
        }

        return false;
        
    }


    /**
     * Return a list of used classes for given element and all his children recursively
     * 
     * @since 4.1
     * @auhor Ilya K.
     */

    $scope.getAllElementsClasses = function(component) {

        var classes = [];

        if (component.options && component.options.classes) {
            classes = component.options.classes;
        }

        if (component.children) {
            for (var key in component.children) {
                if (Object.hasOwnProperty.call(component.children, key)) {
                    var child = component.children[key];
                    classes = classes.concat($scope.getAllElementsClasses(child));
                }
            }
        }

        return classes;
    }

    
    /**
     * For a given classes list generate a new list with all classes styles
     * 
     * @since 4.1
     * @auhor Ilya K.
     */

    $scope.getClassesWithStyles = function(classes) {

        var classesWithStyles = {};

        for (var key in classes) {
            if (Object.hasOwnProperty.call(classes, key)) {
                var className = classes[key];

                if ($scope.classes[className]) {
                    classesWithStyles[className] = $scope.classes[className];
                    classesWithStyles[className].key = className;
                }
            }
        }

        return classesWithStyles;
    }

});