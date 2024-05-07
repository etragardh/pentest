/**
 * All Custom CSS staff here
 * 
 */

CTFrontendBuilder.controller("ControllerCSS", function($scope, $parentScope, $http, $timeout) {

	$scope.idStylesContainer 	= document.getElementById("ct-id-styles");
	$scope.classStylesContainer = document.getElementById("ct-class-styles");

	// Example
	$scope.customSelectorsExample = {
		'#my-custom-id' : {
			'original' : {
				'color' : 'red',
				'font-size' : '24px',
			}
		}
	}

	$scope.selectedCSSFolder = null;

	$scope.expandedFolder = []; // containing reference to the open stylesheet folders
	$scope.expandedSelectorFolder = []; // containing reference to the open selector folders
	$scope.expandedStylesets = []; // containing reference to the open stylsets in the side panel

	$scope.selectedSelectorFolder = null;

	$scope.selectedNodeType = null;

	$scope.selectedStyleSet = null;

	$scope.selectorDetector = {
		mode: false
	};

	$scope.cssFolderMenuOpen = false;
	$scope.selectorFolderMenuOpen = false;

	$scope.componentSelector = {};

	$scope.selectorToEdit 		= false;
	$scope.selectorHighlighted 	= true;
	$scope.expandedStyleSets 	= [];

	$scope.APIElementsDefaultCSS = [];

	$scope.excludeProperties = [
				"ct_content",
				"ct_id",
				"ct_parent",
				"tag",
				"url",
				"src",
				"alt",
				"target",
				"icon-id",
				"section-width",
				"custom-width",
				"header-width",
				"header-custom-width",
				"header-row-width",
				"header-row-custom-width",
				"container-padding-top",
				"container-padding-right",
				"container-padding-bottom",
				"container-padding-left",
				"background-position-top",
				"background-position-left",
				"background-size-width",
				"background-size-height",
				// ct_video related
				"video-padding-bottom",
				"use-custom",
				"custom-code",
				"embed-src",

				// ct_link_button related
				"button-style",
				"button-size",
				"button-color",
				"button-text-color",

				// background related
				"gradient",
				"background",
				"overlay-color",

				// ct_fancy_icon related
				"icon-size",
				"icon-style",
				"icon-color",
				"icon-background-color",

				// oxy_dynamic_list related
				"use-acf-repeater",
				"acf-repeater",
				"background-imagedynamic",
		        "srcdynamic",
		        "urldynamic",

				// condition builder related
				"globalConditionsResult",
				"conditionspreview",
				"conditionstype",

				
				"custom-js",
				"border-all-width",
				"border-all-style",
				"border-all-color",
				"function_name",
				"full_shortcode",
				"gutter",
				"code-css",
				"code-php",
				"code-js",
				"class_name",
				"id_base",
				"pretty_name",
				"friendly_name",
				'separator',
				'date_format',
				'size',
				'meta_key',
				'hover_color',
				//flex
				'flex-reverse',
				//media
				"stack-columns-vertically",
				"reverse-column-order",
				"set-columns-width-50",
				// nav menu
				"menu_id",
				// video background
				"video_background",
				"video_background_media",
				"video_background_hide",

				// aos
				'aos-type',
				'aos-duration',
				'aos-easing',
				'aos-offset',
				'aos-delay',
				'aos-anchor',
				'aos-anchor-placement',
				'aos-once',
				'aos-enable',

				'transform',

				// grid
        		"grid-match-height-of-tallest-child",
				"grid-column-count",
				"grid-column-min-width",
				"grid-column-max-width",
				"grid-column-gap",
				"grid-row-count",
				"grid-row-behavior",
				"grid-row-min-height",
				"grid-row-max-height",
				"grid-row-gap",
				"grid-justify-items",
				"grid-align-items",

				// image element
				"image-type",
				"attachment-size"
				];

	$scope.cache.idCSS 			= "";
	$scope.cache.idStyles 		= {};
	
	$scope.cache.classCSS 		= "";
	$scope.cache.classStyles 	= {};
	
	$scope.cache.selectorCSS 	= "";
	$scope.cache.selectorStyles = {};

	$scope.cache.mediaCSS 		= "";
	$scope.cache.mediaStyles 	= {};

	$scope.contentAdded = [];

	$scope.socialIcons = [];
	$scope.socialIcons.networks = ['facebook', 'instagram', 'twitter', 'linkedin', 'rss', 'youtube'];
	$scope.socialIcons.networkColors = {
		'facebook' 	: '#3b5998',
		'instagram' : '#c32aa3',
		'twitter' 	: '#00b6f1',
		'linkedin' 	: '#007bb6',
		'rss' 		: '#ee802f',
		'youtube' 	: '#ff0000'
	};
	$scope.socialIcons.networkHoverColors = {
		'facebook' 	: '#5b79b8',
		'instagram' : '#e34ac3',
		'twitter' 	: '#20d6ff',
		'linkedin' 	: '#209bd6',
		'rss' 		: '#ffa04f',
		'youtube' 	: '#ff4444'
	};

	$scope.mappedSelectors = {};

	$scope.filterStylesheets = function(item) {

		var filtered = $scope.styleSheets.filter(function(stylesheet) { return stylesheet.id == item.parent; });
		if(filtered.length > 0 && filtered[0].status === 0) {
			return false;
		}
		
		if(parseInt(item.parent) === -1) {
			return false;
		}

		return true;
	}

	$scope.setSelectedSelectorFolder = function(folder) {
		$scope.setSelectedNodeType('selectorfolder');
		$scope.selectedSelectorFolder = folder;
	}

	$scope.getCSSFolder = function(name) {
		return $scope.styleSheets.find(function(item) { 
			return item.id === iframeScope.selectedCSSFolder;
		});
	}

	$scope.setSelectedCSSFolder = function(folder) {
		$scope.selectedCSSFolder = folder;
	}

	$scope.setActiveCSSFolder = function(folder) {
		$scope.setSelectedNodeType('cssfolder');
	}

	$scope.setSelectedNodeType = function(type) {

		if(type !== 'stylesheet') {
			$parentScope.toggleSidebar(true);
		}

		$scope.selectedNodeType = type;
	}

	$scope.greaterThan = function(parameter, value){
		
	    return function(item){
	      return item[parameter] > value;
	    }
	}

	$scope.equalsTo = function(parameter, value) {
		return function(item) {
			return item[parameter] === value;
		}
	}

	$scope.toggleCSSFolder = function(folder) {
		//$scope.expandedFolder[folder.id] = false
		$scope.cssFolderMenuOpen = null;
		folder.status = (folder.status == 1 ? 0 : 1);
		
	}

	$scope.toggleSelectorFolder = function(folder) {
		//$scope.expandedSelectorFolder[folder.key] = false
		folder.status = (folder.status == 1 ? 0 : 1)
	}

	$scope.checkNewCustomSelector = function(selector) {
		if(typeof($scope.customSelectors[selector]) === 'undefined') {
			$scope.customSelectors[selector] = {
				set_name: 'Uncategorized Custom Selectors',
				original: {}
			}
		}

		if(typeof($scope.customSelectors[selector]['set_name']) === 'undefined') {
			$scope.customSelectors[selector]['set_name'] = 'Uncategorized Custom Selectors'
		}

		if(typeof($scope.customSelectors[selector]['original']) === 'undefined') {
			$scope.customSelectors[selector]['original'] = {}
		}
	}

	/**
	 * Set custom CSS selector to edit
	 *
	 * @since 0.2.0
	 * @author Ilya K.
	 */

	$scope.setCustomSelectorToEdit = function(selector) {

		$scope.styleSetActive = false;

		// if the active component had some active css selector, bring it back to edit mode
		if($scope.component.active.id > 0) {
			$scope.activeSelectors[$scope.component.active.id] = (typeof($scope.activeSelectors[$scope.component.active.id]) !== 'undefined') ? $scope.activeSelectors[$scope.component.active.id] : $scope.component.options[$scope.component.active.id].model.activeselector;
			if($scope.activeSelectors[$scope.component.active.id]) {
				$scope.setCurrentClass($scope.activeSelectors[$scope.component.active.id] ? $scope.activeSelectors[$scope.component.active.id] : false);	
			}
		}

		if ($scope.log) {
			console.log("setCustomSelectorToEdit()", selector)
		}

		$scope.selectorToEdit = selector;

		if (typeof selector === "string") {

			$scope.activateComponent(-1, 'ct_selector');

			// if selector exist in classes
			var possibleClass = selector.substr(1);
    		if ( $scope.classes[possibleClass] ) {
    			$scope.setCurrentClass(possibleClass);
    			$scope.populateSelectorFolderDropdown(true);
    			$scope.setSelectedNodeType('class');
    		}
    		else {
    			$scope.setCurrentClass(false);
    			$scope.populateSelectorFolderDropdown();
    			$scope.setSelectedNodeType('selector');
    		}

    		if ($scope.customSelectors[selector]) {
    			$scope.expandedStyleSets[$scope.customSelectors[selector]['set_name']] = true;
			}
		}

		$scope.highlightSelector(true, selector);
		
		if ($parentScope.cleanEmptySelectors && selector !== "") {
            $parentScope.cleanEmptySelectors();
        }
	}

	$scope.setStyleSetActive = function() {
		$scope.setSelectedNodeType('styleset');
		$scope.styleSetActive = true;
		$scope.selectorToEdit = false;
		$scope.currentClass = false;
	}


	$scope.populateSelectorFolderDropdown = function(isClass) {
        var folder = '';
        if(isClass) {
        	var selectorItem = $scope.classes[$scope.currentClass];
        	if(selectorItem) {
        		folder = selectorItem.parent;
        	}
        }
        else {
            if($scope.selectedStyleSet) {
            	folder = $scope.styleSets[$scope.selectedStyleSet]['parent'];
            }
            else {
            	var selectorItem = $scope.customSelectors[$scope.selectorToEdit];
            	if(selectorItem) {
            		folder = selectorItem.parent;
            	}
            }
        }
      
		$scope.currentActiveFolder = folder;
	}

	/**
	 * Set style sheet to edit
	 *
	 * @since 0.3.4
	 * @author Gagan Goraya.
	 */

	$scope.setStyleSheetToEdit = function(stylesheet) {

		var parent = _.findWhere($scope.styleSheets, {id: stylesheet.parent});

        if(parent) {
            $scope.currentActiveStylesheetFolder = parent.name;
        }
        else {
            $scope.currentActiveStylesheetFolder = '';
        }

		$scope.cancelDeleteUndo();
	
		if ($scope.log) {
			console.log("setStyleSheetToEdit()", stylesheet)
		}

		// activate root
		$scope.activateComponent(0);

		$scope.stylesheetToEdit = stylesheet;
		$scope.parentScope.switchActionTab('styleSheet');
		$scope.setSelectedNodeType('stylesheet'); // this must come after the switchActionTab, not before
		$scope.parentScope.expandSidebar();

		// update CM6 state to show proper stylesheet content
		if (window.parent.currentCMEditor) {
			var OxyCM = window.parent.OxyCM;
			var newState = OxyCM.EditorState.create({
				extensions: [
					OxyCM.basicSetup,
          			OxyCM.modules.keymap.of([OxyCM.modules.indentWithTab]),
					OxyCM.modules.css(),
					window.parent.currentCMWrap.of($scope.globalCodeMirrorWrap === "true" ? [OxyCM.EditorView.lineWrapping, OxyCM.modules.search()] : OxyCM.modules.search()),
          			window.parent.currentCMTheme.of(OxyCM.modules[$scope.globalCodeMirrorTheme]),
				],
				doc: stylesheet['css'],
			});
			window.parent.currentCMEditor.setState(newState);
        }
	}


	$scope.updateStylesheetCSS = function(css) {
		$scope.stylesheetToEdit['css'] = css;
	}

	/**
	 * Highlight selector elements presented on the page
	 *
	 * @since 0.2.0
	 * @author Ilya K.
	 */

	$scope.highlightSelector = function(forceHighlight, selector, $event) {

		if ($scope.log){
			console.log("highlightSelector()", forceHighlight, selector);
		}

		if (selector===false) {
			$scope.outputCSSStyles("ct-selector-highlight", "");
			return false;
		}

		if ($event){
			$event.stopPropagation();
		}

		var style = "";

		if (undefined === selector) {
			selector = $scope.selectorToEdit;
		}

		if (forceHighlight === undefined) {
			$scope.selectorHighlighted = ! $scope.selectorHighlighted;
		}
		else {
			$scope.selectorHighlighted = forceHighlight;
		}

		// check if selector has any elements on the page
		if ($scope.selectorHighlighted) {
			
			var elements = []; 

			try {
				if (selector.indexOf(".")<0&&selector.indexOf("#")<0&&selector.indexOf("body")<0) {
					elements = angular.element(selector, '.oxygen-body');
				}
				else {
					elements = angular.element(selector);
				}
			}
			catch (err) {
				console.log(err);
			}

			if ( elements.length == 0 ) {

				$scope.selectorHighlighted = false;
				
				//alert("There is no elements for '" + selector + "' selector on this page.")
				$scope.outputCSSStyles("ct-selector-highlight", "");
				return false;
			}
		}

		var state = "";

		if ( $scope.currentState == "original" || $scope.currentState == "hover" || $scope.currentState == "active" || $scope.currentState == "focus" ) {
			state = "";
		}
		else {
			state = ":"+$scope.currentState;
		}

		// check if highlighted
        if ($scope.selectorHighlighted) {
        	style = selector + state + "{outline: 1px dashed rgb(19, 187, 38); outline-offset: 0px;}";
        	if (selector.indexOf(".")<0&&selector.indexOf("#")<0&&selector.indexOf("body")<0) {
        		style = ".oxygen-body " + style;
        	}
        }

        // output to <head>
        $scope.outputCSSStyles("ct-selector-highlight", style);
	}

	$scope.deleteSelectorFolder = function(folder) {
		if($scope.styleFolders.hasOwnProperty(folder)) {
			if(_.findWhere($scope.styleSets, {parent: folder}) || _.findWhere($scope.classes, {parent: folder})) {
				if(!confirm("Are you sure you wish to delete this folder and all classes and selectors inside it?")) {
					return false;
				}
			}

			angular.forEach(iframeScope.styleSets, function(item) { if(item.parent === folder) delete iframeScope.styleSets[item.key];})
			angular.forEach(iframeScope.classes, function(item) { if(item.parent === folder) delete iframeScope.classes[item.key];})

			delete $scope.styleFolders[folder];
		}
	}

	$scope.toggleUncategorizedFolderContents = function(disable) {
		if(disable) {
			_.each(_.filter($scope.styleSets, function(item) { return typeof(item['parent']) === 'undefined' || item['parent'] === null;}), function(item) {
				item['parent'] = -1;
			});
			_.each(_.filter($scope.classes, function(item) { return typeof(item['parent']) === 'undefined' || item['parent'] === null;}), function(item) {
				item['parent'] = -1;
			});
		} else {
			_.each(_.filter($scope.styleSets, function(item) { return item['parent'] === -1 || item['parent'] === "-1";}), function(item) {
				delete item['parent'];
			});
			_.each(_.filter($scope.classes, function(item) { return item['parent'] === -1 || item['parent'] === "-1";}), function(item) {
				delete item['parent'];
			});
		}
	}

	$scope.toggleUncategorizedStyleSheets = function(disable) {
		if(disable) {
			_.each(_.filter($scope.styleSheets, function(item) { return typeof(item['parent']) === 'undefined' || item['parent'] === null || parseInt(item['parent']) === 0;}), function(item) {
				item['parent'] = -1;
			});
		}
		else {
			_.each(_.filter($scope.styleSheets, function(item) { return item['parent'] === -1 || item['parent'] === "-1";}), function(item) {
				item['parent'] = 0;
			});
		}
	}

	$scope.addSelectorFolder = function() {

		$scope.cancelDeleteUndo();

		
		var folder = prompt('Provide a name for the folder').trim();

		// check for validity of the name
    	var re = /^[a-z_-][a-z\d_-]*$/i
	    //var re = /-?[_a-zA-Z]+[_a-zA-Z0-9-]*$/i;

	    if(!re.test(folder)) {
	    	alert("Bad folder name. Special characters and spaces are not allowed");
	    	return;
	    }

	    // check if already exists
		if($scope.styleFolders.hasOwnProperty(folder)) {
			alert("'" + folder + "' already exist.");
			return;
		}

		// finaly add the folder
		$scope.styleFolders[folder] = {
			status: 1
		}
	}

	$scope.addSelectorItem = function() {

		$scope.cancelDeleteUndo();

		var selector = prompt('Type a valid CSS selector').trim();

		try { // check validity
			document.querySelector(selector)
		}
		catch (e) {
			alert("Not a valid selector");
			return false
		}

		// check if already exists
		if($scope.customSelectors.hasOwnProperty(selector)) {
			alert("'" + selector + "' already exist.");
			return;
		}

		// finaly add the selector
		$scope.customSelectors[selector] = {
			set_name: 'Uncategorized Custom Selectors',
			original: {}
		}

		// if a folder is selected
		if($scope.selectedSelectorFolder && $scope.styleFolders.hasOwnProperty($scope.selectedSelectorFolder)) {
			$scope.customSelectors[selector]['parent'] = $scope.selectedSelectorFolder;
		}

		$scope.setCustomSelectorToEdit(selector);
		
		$scope.selectedStyleSet = selector;

		$scope.expandedStyleSets['Uncategorized Custom Selectors'] = true;

		var styleSetFolder = $scope.styleSets['Uncategorized Custom Selectors']['parent'];
		
		if(typeof(styleSetFolder) === 'undefined' ) {
			//expand styleset folder 0
			iframeScope.expandedSelectorFolder[0]=true;
		}
		else if(styleSetFolder === -1) {
			//expand 'uncategorized'
			iframeScope.expandedSelectorFolder['uncategorized'] = true;
		}
		else {
			iframeScope.expandedSelectorFolder[styleSetFolder] = true;
		}
	}

	/**
	 * Add Style Sheet
	 *
	 * @since 0.3.4
	 * @author Gagan Goraya
	 */

	$scope.addStyleSheet = function(isFolder) {

		$scope.cancelDeleteUndo();


		var stylesheet = prompt("Enter a name for the "+(isFolder?'Folder':'Stylesheet'));
		
		if(!stylesheet) {
			return;
		}

		stylesheet = stylesheet.trim();

	    // check for validity of the name
    	var re = /^[a-z_-][a-z\d_-]*$/i
	    //var re = /-?[_a-zA-Z]+[_a-zA-Z0-9-]*$/i;

	    if(!re.test(stylesheet)) {
	    	alert("Bad stylesheet name. Special characters and spaces are not allowed");
	    	return;
	    }
		
	    // check for repeat
	    var filtered = $scope.styleSheets.filter(function(item){ return item.name == stylesheet; });

	    if(filtered.length > 0) {
			alert("'" + stylesheet + "' already exist. Please choose another name.");
			return;
	    }
		
		// find the highest available ID;
		var newId = 1;

		if($scope.styleSheets.length > 0) {
			newId = _.max($scope.styleSheets, function(item) {
				return item.id;
			}).id + 1;
		}

		//$scope.styleSheets[stylesheet] = "";
		if(isFolder) {
			$scope.styleSheets.push({
				id: newId,
				name: stylesheet,
				status: 1,
				folder: 1
			});
		}
		else {
			var parent = $scope.selectedCSSFolder === -1 || $scope.selectedCSSFolder === null ? 0 : $scope.selectedCSSFolder;
			var newSheet = {
				id: newId,
				name: stylesheet,
				css: '',
				parent: parent,
			};

			$scope.styleSheets.push(newSheet);

			// expand the parent folder
			for(key in iframeScope.expandedFolder) { iframeScope.expandedFolder[key] = false };
			iframeScope.expandedFolder[parent] = true;
			iframeScope.setStyleSheetToEdit(newSheet);
		}

		
	}


	/**
	 * Delete Style sheet
	 *
	 * @since 0.3.4
	 * @author Gagan Goraya.
	 */

	$scope.deleteStyleSheet = function(stylesheet) {

		$scope.cancelDeleteUndo();

		if(stylesheet['folder']) {
			if(!confirm("Are you sure you wish to delete this folder and all stylesheets inside it?")) {
				return false;
			}
		}
		else { 

			if (!confirm("Delete \""+stylesheet['name']+"\" stylesheet from install? (Changes will take effect on Save).")) {
				return false;
			}
		}

		if ($scope.log) {
			console.log("deleteStyleSheet()", selector);
		}

		//delete stylesheet;
		$scope.styleSheets.splice($scope.styleSheets.indexOf(stylesheet), 1);

		if(stylesheet['folder']) {
			// make sure that children are removed as well
			$scope.styleSheets = $scope.styleSheets.filter(function(item) { return item.parent != stylesheet.id;})

		}

		// remove the style definitions rendered in the DOM
		var styleSheetContainer = angular.element(document.getElementById("ct-style-sheet-"+stylesheet['name']));

		styleSheetContainer.remove();

		// turn off the editing panel, if this style sheet was being edited
		if($scope.stylesheetToEdit === stylesheet) {
			$scope.stylesheetToEdit = false;
		    $parentScope.actionTabs['styleSheet'] = false;
		    $parentScope.toggleSidebar(true);
		}

		// disable selected folder when if it is the one being deleted
		if ( $scope.selectedCSSFolder === stylesheet.id ) {
			$scope.selectedCSSFolder = null;
		}

		$scope.selectedNodeType = null;
	}	


	/**
	 * Delete custom CSS selector
	 *
	 * @since 0.2.0
	 * @author Ilya K.
	 */

	$scope.deleteCustomSelector = function(selector) {

		$scope.cancelDeleteUndo();

		if(selector === 'Uncategorized Custom Selector') {
			alert("Sorry, you can't delete the Style Set named Uncategorized Custom Selectors");
			return false;
		}

		var confirmed = confirm("Are you sure to delete \""+selector+"\" selector?");
		
		if (!confirmed) {
			return false;
		}

		if ($scope.log) {
			console.log("deleteCustomSelector()", selector);
		}

		delete $scope.customSelectors[selector];

		$scope.selectorToEdit = false;

		if ( $scope.component.active.id == -1 ) {
			$scope.activateComponent(0,'root')
		}
		
		$scope.classesCached = false;
		$scope.outputCSSOptions();
		$scope.unsavedChanges();
	}


    /**
     * Update custom selector parameter value
     *
     * @since 0.2.0
     * @author Ilya K.
     */
    
    $scope.updateCustomSelectorValue = function(parameter, value) {

    	$scope.cancelDeleteUndo();

    	if ($scope.log) {
    		console.log("updateCustomSelectorValue()", parameter, value);
    	}

    	if ($scope.selectorToEdit === "") {
    		return false;
    	}

    	var state 		= $scope.currentState,
    		selector 	= $scope.selectorToEdit.substr(1);

    	// if selector exist in classes
    	if ( $scope.classes[selector] ) {
    		if ( !$scope.isEditing('media') ) {
                // check if this state already added
                if (!$scope.classes[selector][state]) {
                    $scope.classes[selector][state] = {};
                }
                if (value==""){
                    delete $scope.classes[selector][state][parameter];
                }
                else {
                    $scope.classes[selector][state][parameter] = value;
                }
			} else {
                // init class media options
                if (!$scope.classes[$scope.currentClass]['media']) {
                    $scope.classes[$scope.currentClass]['media'] = {};
                }
                
                if (!$scope.classes[$scope.currentClass]['media'][$scope.currentMedia]) {
                    $scope.classes[$scope.currentClass]['media'][$scope.currentMedia] = {};
                }

                if (!$scope.classes[$scope.currentClass]['media'][$scope.currentMedia][state]) {
                    $scope.classes[$scope.currentClass]['media'][$scope.currentMedia][state] = {};
                }

                // remove empty options
                if ( value == "" ) {
                    delete $scope.classes[$scope.currentClass]['media'][$scope.currentMedia][state][parameter];
                } 
                else {
                    $scope.classes[$scope.currentClass]['media'][$scope.currentMedia][state][parameter] = value;
                }
            }
    	}
    	else {

            if ( !$scope.isEditing('media') ) {
				    
				if (!$scope.customSelectors[$scope.selectorToEdit]) {
				    $scope.customSelectors[$scope.selectorToEdit] = {
				    	set_name: 'Uncategorized Custom Selectors'
				    };
				}

				if (!$scope.customSelectors[$scope.selectorToEdit][state]) {
				    $scope.customSelectors[$scope.selectorToEdit][state] = {};
				}

				if (value==""){
					delete $scope.customSelectors[$scope.selectorToEdit][state][parameter];
				}
				else {
					$scope.customSelectors[$scope.selectorToEdit][state][parameter] = value;
				}

            }
            else {
                // init class media options
                if (!$scope.customSelectors[$scope.selectorToEdit]['media']) {
                    $scope.customSelectors[$scope.selectorToEdit]['media'] = {};
                }
                
                if (!$scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia]) {
                    $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia] = {};
                }

                if (!$scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia][state]) {
                    $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia][state] = {};
                }

                // remove empty options
                if ( value == "" ) {
                    delete $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia][state][parameter];
                } 
                else {
                    $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia][state][parameter] = value;
                }
            }
	    }
    }


	/**
     * Append <style> element into <head> with all passed styles
     *
     * @since 0.2.0
     * @author Ilya K.
     */
    
    $scope.outputCSSStyles = function(name, style) {

    	var styleElement = document.getElementById(name),
    		output = "";
        
        output = "<style id=\"" + name + "\">",
        output += style;
        output += "</style>";
        
        // add style
        if ( styleElement ) {
            //angular.element(styleElement).replaceWith(output);
            styleElement.innerHTML = style;
        } 
        else {
            angular.element("head").append(output);
        }
    }


    /**
     * Delete <style> element into <head> with all passed styles
     *
     * @since 0.3.1
     * @author Ilya K.
     */
    
    $scope.deleteCSSStyles = function(name) {

    	var styleElement = document.getElementById(name); 
        
        if ( styleElement ) {
            angular.element(styleElement).remove();
        }
    }


    $scope.generateDynamicBackgrounds = function(id, force, type, callback, tree) {

    	if(typeof(tree) === 'undefined') {
    		tree = $scope.componentsTree;
    	}

    	$scope.dynamicBackgrounds = $scope.dynamicBackgrounds || {};

    	var target = { lock: true };

    	if(type === 'id') {
    		$scope.dynamicBackgrounds[id] = $scope.dynamicBackgrounds[id] || {};
    		$scope.dynamicBackgrounds[id] = target;
    	}
    	else if(type === 'class') {
    		$scope.dynamicBackgrounds['classes'] = $scope.dynamicBackgrounds['classes'] || {}
    		$scope.dynamicBackgrounds['classes'][id] = $scope.dynamicBackgrounds['classes'][id] || {};
    		$scope.dynamicBackgrounds['classes'][id] = target;
    	}
    	else if(type === 'selector') {
    		$scope.dynamicBackgrounds['selectors'] = $scope.dynamicBackgrounds['selectors'] || {}
    		$scope.dynamicBackgrounds['selectors'][id] = $scope.dynamicBackgrounds['selectors'][id] || {};
    		$scope.dynamicBackgrounds['selectors'][id] = target;
    	}

    	

    	var options = {};

    	if(type === 'id') {

    		var component = $scope.findComponentItem(tree.children, id, $scope.getComponentItem);
    		if(!component) {
    			options = {}
    		}
    		else {
	    		options = component.options;
	    	}

    	}
    	else if(type === 'class') {

    		options = $scope.classes[id] || {};

    	}
    	else if(type === 'selector') {
    		options = $scope.customSelectors[id] || {};

    	}

    	var count = 0;
    	
        for(key in options) {

        	if(['ct_id', 'ct_parent', 'set_name', 'parent', 'key', 'status', 'friendly_name', 'selector', 'activeselector', 'classes', 'nicename'].indexOf(key) > -1) {
        		continue;
        	}

        	if(key === 'media') {
        		
        		target['media'] = {};

        		var breakpoints = options['media'];

        		for(bpKey in breakpoints) {
        			target['media'][bpKey] = {};
        			for(stateKey in breakpoints[bpKey]) {
        				if(type === 'id' && breakpoints[bpKey][stateKey]['background-image'] &&
        					breakpoints[bpKey][stateKey]['background-image'].indexOf('[oxygen') > -1) {

        					target['media'][bpKey][stateKey] = breakpoints[bpKey][stateKey]['background-image'];
        					count++;
        				}
        			}
        		}
        	}
        	else
        	if(type === 'id' && options[key]&&options[key]['background-image'] &&
        		options[key]['background-image'].indexOf('[oxygen') > -1) {

        		target[key] = options[key]['background-image'];
        		count++;
        	}

        	
        }


        for(key in target) {

        	if(key === 'lock') {
        		continue;
        	}

        	if(key === 'media') {

        		var breakpoints = target['media'];

        		for(bpKey in breakpoints) {
        			
        			for(stateKey in breakpoints[bpKey]) {
    					$scope.applyShortcodeResults(id, target['media'][bpKey][stateKey], function(contents, params) {
    						
    						//target['media'][params.bpKey][params.stateKey] = contents.trim();

    						if(type === 'selector') {

								$scope.dynamicBackgrounds['selectors'][id]['media'][params.bpKey][params.stateKey] = contents.trim();
							}
							else if(type === 'class') {

								$scope.dynamicBackgrounds['classes'][id]['media'][params.bpKey][params.stateKey] = contents.trim();
							}
							else if(type === 'id') {

								$scope.dynamicBackgrounds[id]['media'][params.bpKey][params.stateKey] = contents.trim();	

							}

    						count--;
    						if(count === 0) {
    							delete target['lock'];
    							callback();
    							return
    						}
    					}, {id: id, bpKey: bpKey, stateKey: stateKey});
    					
        			}
        		}
        	}

        	else {
        		
        		$scope.applyShortcodeResults(id, target[key], function(contents, params) {
        			
					//target[params.key] = contents.trim();
					if(type === 'selector') {

						$scope.dynamicBackgrounds['selectors'][id][params.key] = contents.trim();
					}
					else if(type === 'class') {

						$scope.dynamicBackgrounds['classes'][id][params.key] = contents.trim();
					}
					else if(type === 'id') {
						$scope.dynamicBackgrounds[id][params.key] = contents.trim();	
					}

					count--;
					if(count === 0) {
						delete target['lock'];
						callback();
						return
					}
				}, {id: id, key: key});
        	}
        	
        }

        if(count === 0) {
        	delete target['lock'];
			callback();
        }
        
    }


    /**
     * Output to the <head> CSS styles for all componets, classes and custom selectors
     *
     * @since 0.2.0
     * @author Ilya K.
     */

    $scope.outputCSSOptions = function(id, force, ignoreBG) {

		$scope.APIElementsDefaultCSS = [];

		if ($scope.log) {
			console.log("outputCSSOptions()", id);
			$scope.functionStart("outputCSSOptions()");
		}

		var style = "";
		$scope.bufferedOutputStyles = $scope.bufferedOutputStyles || {};
		$scope.bufferedOutputStyles['keys'] = $scope.bufferedOutputStyles['keys'] || [];
		if (($scope.isEditing("class") || $scope.isEditing("id") || force == true)) {
			if($scope.bufferedOutputStyles['keys'].indexOf('id') < 0) {
				$scope.bufferedOutputStyles['keys'].push('id');
			}
		}

		if ($scope.isEditing("class") || !$scope.classesCached) {
			
	    	if($scope.bufferedOutputStyles['keys'].indexOf('classes') < 0) {
				$scope.bufferedOutputStyles['keys'].push('classes');
			}
		}

    	if($scope.bufferedOutputStyles['keys'].indexOf('selectors') < 0) {
			$scope.bufferedOutputStyles['keys'].push('selectors');
    	}

		/**
		 * Handle #ID styles
		 */
		
		if (($scope.isEditing("class") || $scope.isEditing("id") || force == true)) {

			if (undefined !== id && id > 0) {
				$scope.updateComponentCacheStyles(id, function() {

					// clear cache
					$scope.cache.idCSS = "";

					Object.keys($scope.cache.idStyles).map(function(key, index) {
						$scope.cache.idCSS += $scope.cache.idStyles[key];
					});	
					
					$scope.outputCSSStylesAfterWait('id', $scope.cache.idCSS);

				});
			}
			else {
				
				Object.keys($scope.cache.idStyles).map(function(key, index) {
					$scope.cache.idCSS += $scope.cache.idStyles[key];
				});	
				
				$scope.outputCSSStylesAfterWait('id', $scope.cache.idCSS);
				
			}
			
		}

		/**
    	 * Handle .class styles
    	 */
    	
    	if ($scope.isEditing("class") || !$scope.classesCached) {
    		
			if (!$scope.classesCached) {

				var processClassCSS = function(classKey) {

					var processNextClassCSS = function() {

						if(classKeys.length > 0) {
							processClassCSS(classKeys.shift());
						}
					}

					if ($scope.classes.hasOwnProperty(classKey)) {

						// get states
						var classStates = $scope.classes[classKey];
						$scope.cache.classStyles[classKey] = '';
						// add styles to cache
						if(typeof($scope.classes[classKey]['parent']) === 'undefined' || typeof($scope.styleFolders[$scope.classes[classKey]['parent']]) === 'undefined' || $scope.styleFolders[$scope.classes[classKey]['parent']]['status'] === 1) {
							
							if(parseInt($scope.classes[classKey]['parent']) !== -1) {
								// generate the results of oxy shortcodes for background images
								// and then do the below process as a callback
								$scope.generateDynamicBackgrounds(classKey, force, 'class', function() {

									$scope.cache.classStyles[classKey] = $scope.getSingleClassCSS(classKey, classStates);
									processNextClassCSS();
					    			
								})
				    			
							} else {
								processNextClassCSS();
							}

				    	} else {

				    		processNextClassCSS();
				    	}
						
					}
					else {
						processNextClassCSS();
					}
				}

				// empty styles cache
				$scope.cache.classStyles = [];

				var allClassKeys = Object.keys($scope.classes);

				if(allClassKeys.length > 0) {

					var batchSize = 1000,
						batches = Math.ceil( allClassKeys.length / batchSize );

					if ( batches <= 1 ) {
						classKeys = allClassKeys;
						processClassCSS(classKeys.shift());
					}
					else {
						for (var i = 0; i < batches; i++) {
							
							var start = i * batchSize,
								end = start + batchSize,
								classKeys = allClassKeys.slice(start, end);

							processClassCSS(classKeys.shift())
						}
					}

					// clear CSS cache
					$scope.cache.classCSS = "";

					Object.keys($scope.cache.classStyles).map(function(key, index) {
						$scope.cache.classCSS += $scope.cache.classStyles[key];

					});
					
					$scope.outputCSSStylesAfterWait('classes', $scope.cache.classCSS);

					// set cached flag
					$scope.classesCached = true;

				} else {
					// clear CSS cache
					$scope.cache.classCSS = "";
					$scope.outputCSSStylesAfterWait('classes', $scope.cache.classCSS);
					$scope.classesCached = true;
				}

			}
			// classes already cached
			else {
				if(typeof($scope.classes[$scope.currentClass]) === 'undefined' || typeof($scope.classes[$scope.currentClass]['parent']) === 'undefined' || typeof($scope.styleFolders[$scope.classes[$scope.currentClass]['parent']]) === 'undefined' || $scope.styleFolders[$scope.classes[$scope.currentClass]['parent']]['status'] === 1) {
					if(parseInt($scope.classes[$scope.currentClass]['parent']) !== -1) {
		    			$scope.cache.classStyles[$scope.currentClass] = $scope.getSingleClassCSS($scope.currentClass);
					}
		    	}

		    	// clear CSS cache
				$scope.cache.classCSS = "";

				Object.keys($scope.cache.classStyles).map(function(key, index) {
					$scope.cache.classCSS += $scope.cache.classStyles[key];
				});
				
				$scope.outputCSSStylesAfterWait('classes', $scope.cache.classCSS);

			}
			
		}

		

		/**
    	 * Add all custom selectors' options
    	 * 
    	 */
    	
    	// TODO: implement cache clearing and check for cache
    	$scope.cache.selectorStyles = [];
    	$scope.cache.selectorCSS 	= "";
    	
    	var selectorKeys = Object.keys($scope.customSelectors);

		var processSelectorCSS = function(selectorKey) {

			var processNextSelectorCSS = function() {

				if(selectorKeys.length > 0) {

					processSelectorCSS(selectorKeys.shift());

				} else {

					// clear CSS cache
					$scope.cache.selectorCSS = "";

					Object.keys($scope.cache.selectorStyles).map(function(key, index) {
						$scope.cache.selectorCSS += $scope.cache.selectorStyles[key];
					});

					$scope.outputCSSStylesAfterWait('selectors', $scope.cache.selectorCSS);
					
				}
			}

			if ($scope.customSelectors.hasOwnProperty(selectorKey)) {

				var states = $scope.customSelectors[selectorKey];
				$scope.cache.selectorStyles[selectorKey] = '';
				if(typeof($scope.customSelectors[selectorKey]['set_name']) === 'undefined' || typeof($scope.styleSets[$scope.customSelectors[selectorKey]['set_name']]) === 'undefined' || typeof($scope.styleSets[$scope.customSelectors[selectorKey]['set_name']]['parent']) === 'undefined' || typeof($scope.styleFolders[$scope.styleSets[$scope.customSelectors[selectorKey]['set_name']]['parent']]) === 'undefined' || typeof($scope.styleFolders[$scope.styleSets[$scope.customSelectors[selectorKey]['set_name']]['parent']]['status']) === 'undefined' || $scope.styleFolders[$scope.styleSets[$scope.customSelectors[selectorKey]['set_name']]['parent']]['status'] === 1) {
					if(typeof($scope.styleSets[$scope.customSelectors[selectorKey]['set_name']]) === 'undefined' || parseInt($scope.styleSets[$scope.customSelectors[selectorKey]['set_name']]['parent']) !== -1) {
						$scope.generateDynamicBackgrounds(selectorKey, force, 'selector', function() {

			    			$scope.cache.selectorStyles[selectorKey] = $scope.getSingleSelectorCSS(selectorKey, states);
							processNextSelectorCSS();	
						});
					}
					else {
		    			processNextSelectorCSS();
		    		}
		    	}
		    	else {
		    		processNextSelectorCSS();
		    	}
				
			}
			else {
				processNextSelectorCSS();
			}
		}
		
		if(selectorKeys.length > 0) {

			processSelectorCSS(selectorKeys.shift());

		} else {
			// clear CSS cache
			$scope.outputCSSStylesAfterWait('selectors', $scope.cache.selectorCSS);
		}

        $scope.functionEnd("outputCSSOptions()");

    }

    $scope.outputCSSStylesAfterWait = function(param, style) {   	

    	if(param === 'id') {
    		$scope.idStylesContainer.innerHTML = style;
    	}

    	if(param === 'classes') {
    		$scope.classStylesContainer.innerHTML = style;
    	}

    	if(param === 'selectors') {
    		$scope.outputCSSStyles("ct-custom-selectors", style);
    	}
    }


	/**
	 * Update component styles in cache array
	 *
	 * @since 0.2.5
	 * @author Ilya K.
	 */
	
	$scope.updateComponentCacheStyles = function(id, callback, forRepeater) {

		$scope.contentAdded = [];

		if ($scope.log) {
			console.log("updateComponentCacheStyles()", id, $scope.component.options[id]);
		}

		$scope.generateDynamicBackgrounds(id, false, 'id', function() {

			// update particular component Styles in cache
			$scope.cache.idStyles[id] = $scope.getSingleComponentCSS($scope.component.options[id], id, null, null, forRepeater);
			if(callback) {
				callback();
			}
		});
		
	}


	/**
	 * Update all components styles in cache array
	 *
	 * @since 0.3.0
	 * @author Ilya K.
	 */
	
	$scope.updateAllComponentsCacheStyles = function() {

		$scope.APIElementsDefaultCSS = [];

		$scope.functionStart("updateAllComponentsCacheStyles()");

		for(var id in $scope.component.options) { 
			if ($scope.component.options.hasOwnProperty(id)) {
				
				$scope.updateComponentCacheStyles(id, function() {
					 // clear cache
                    $scope.cache.idCSS = "";

                    Object.keys($scope.cache.idStyles).map(function(key, index) {
                        $scope.cache.idCSS += $scope.cache.idStyles[key];
                    });
                    
                    $scope.outputCSSStylesAfterWait('id', $scope.cache.idCSS);
				});
			}
		}

		$scope.functionEnd("updateAllComponentsCacheStyles()");
	}


	/**
	 * Remove component styles in cache array
	 *
	 * @since 0.2.5
	 * @author Ilya K.
	 */
	
	$scope.removeComponentCacheStyles = function(id) {
		// remove from array
		delete $scope.cache.idStyles[id];
	}
	

	$scope.getDynamicBackground = function(type, params) {

		var target, previewBG = false;

		if(type === 'id' && $scope.dynamicBackgrounds[params['id']]) {
			target = $scope.dynamicBackgrounds[params['id']]
		}
		else if(type === 'class' && $scope.dynamicBackgrounds['classes'][params['id']]) {
			target = $scope.dynamicBackgrounds['classes'][params['id']];
		}
		else if(type === 'selector' && $scope.dynamicBackgrounds['selectors'][params['id']]) {
			target = $scope.dynamicBackgrounds['selectors'][params['id']];
		}

		// if media 
		if(params['media']) {
			previewBG = target['media'][params['media']][params['stateName'] === 'id' ? 'original' : params['stateName']];
		}
		else {

			previewBG = target[params['stateName'] === 'id' ? 'original' : params['stateName']];
			
		}

		return previewBG;
	}

    /**
     * Get one single components CSS
     *
     * @since 0.2.5
     * @author Ilya K.
     * @return {string} CSS styles
     */

    $scope.getSingleComponentCSS = function(componentOptions, componentId, isMedia, whichMedia, forRepeater) {

    	if (componentOptions.name == "ct_reusable" && componentOptions.original) {

    		// holder for reusable CSS
			$scope.reusableCSS 			= {};
			$scope.reusableCSS.styles 	= "  ";

			var viewId = componentOptions.original.view_id;

    		// add this item CSS
			if ($scope.postsData[viewId]) {
				$scope.generateTreeCSS($scope.postsData[viewId].post_tree, $scope.reusableCSS);
				$scope.outputCSSStyles("ct-re-usable-styles-"+viewId, $scope.reusableCSS.styles);
			}

			return "";
    	}

    	if ($scope.log) {
    		console.log("getSingleComponentCSS()", componentId, componentOptions, isMedia);
    		$scope.functionStart("getSingleComponentCSS()");
		}

    	var style 		= "",
    		important 	= "",
    		paragraph 	= "",
    		currentState = "",
    		componentDefaults = $scope.defaultOptions[componentOptions.name];

    	if (undefined == componentOptions['original']) {
    		componentOptions['original'] = [];
    	}

		// loop components' states
		for(var stateName in componentOptions) { 
			if (componentOptions.hasOwnProperty(stateName)) {
				var stateOption = componentOptions[stateName];

				// skip "id" original options for media
				if ( stateName=="original" && !isMedia ) {
					continue;
				}

				if (componentId != 0 && typeof(stateOption) === "object" && 
					stateName != "model" && 
					stateName != "classes" &&
					stateName != "media" ) {
					
					// make a copy to not modify original Object
					var mergedOptions 			= angular.copy(componentOptions[stateName]),
						stateOptions  			= $scope.getCSSOptions(componentId, stateName, mergedOptions, componentOptions.name, whichMedia, {id:componentId, state:stateName});
						mergedOriginalOptions 	= angular.copy(componentOptions['original']),
						originalOptions 		= $scope.getCSSOptions(componentId, 'original', mergedOriginalOptions, componentOptions.name, whichMedia, {id:componentId, state:stateName});

					// apply styles to id
					if (
							// if "id" state
							stateName == "id" ||

							// editing media
							( isMedia && stateName == "original" ) ||
							
							// or if currently editing exactly this id and state
							( stateName 	== $scope.currentState && 
							  componentId 	== $scope.component.active.id && 
							  
							  $scope.isEditing('id') && ( stateName == "hover" || stateName == "active" || stateName == "focus")
							) 
						) 
					{
						currentState = "";
					}
					// apply style to state/pseudo-element
					else {
						currentState = ":" + stateName;
					}

					// check if options is for Paragraph component
					paragraph = ( componentOptions.name == "ct_paragraph" ) ? " p" : "";

					if(forRepeater) { // be specific to each list item, particularly for the background image, as it could be rendered from the dynamic data
						style += '#' + componentOptions.selector+'[ng-attr-component-id="'+componentId+'"]' + paragraph + currentState + "{";
		    			style += $scope.getBackgroundLayersCSS(stateOptions, componentOptions.name, 'id', componentId, whichMedia, stateName) || "";
		    			style += '}';

		    			continue;
		    		}

					// handle columns gutter
					if ( componentOptions.name == "ct_columns" ) {

						var gutter = $scope.getWidth(componentOptions['original']['gutter']);

						style += '#' + componentOptions.selector + " .ct-column" + currentState + "{";
						style += "margin-right" + ":" + (gutter.value/2) + gutter.unit + ";";
						style += "margin-left" + ":" + (gutter.value/2) + gutter.unit + ";";
						style += '}';
					}

					// handle new columns gutter
					// We might add this in future
					/*if ( componentOptions.name == "ct_new_columns" ) {

						var gutter = $scope.getWidth(componentOptions['original']['gutter']);

						style += '#' + componentOptions.selector + currentState + "{";
						style += "margin-right" + ":-" + (20+gutter.value) + "px;";
						style += "margin-left" + ":-" + (20+gutter.value) + "px;";
						style += "width: calc(100% + "+(40+gutter.value+gutter.value)+"px);";
						style += '}';


						style += '#' + componentOptions.selector + currentState + " > .ct-div-block {";
						style += "margin-right" + ":" + gutter.value + "px;";
						style += "margin-left" + ":" + gutter.value + "px;";
						style += '}';
					}*/

					// placeholder options just for builder
					if (componentOptions['original']["dont_render"]=='true') {
						style += '#' + componentOptions.selector + "{";
						if (componentOptions['original']["placeholder-width"]){
							style += 'width:' + componentOptions['original']["placeholder-width"] + (componentOptions['original']["placeholder-width-unit"] || componentDefaults["placeholder-width-unit"]) + ";";
						}
						if (componentOptions['original']["placeholder-height"]){
							style += 'height:' + componentOptions['original']["placeholder-height"] + (componentOptions['original']["placeholder-height-unit"] || componentDefaults["placeholder-height-unit"]) + ";";
						}
						style += "}";
					}

					// handle section special options
					if ( componentOptions.name == "ct_section" ) {
						
						// Set section display as none
						if (stateOptions['display'] && stateOptions['display'] == 'none') {
							style += '#' + componentOptions.selector + currentState + "{ display: "+stateOptions['display']+"; }";
						}

						if (stateOptions['display'] && stateOptions['display'] != 'none') {
							style += '#' + componentOptions.selector + currentState + "{ display: block; }";
						}


						style += '#' + componentOptions.selector + ">.ct-section-inner-wrap" + currentState + "{";

							if (undefined==stateOptions['section-width']) {
								stateOptions['section-width'] = componentDefaults['section-width'];
							}
						
							if ( stateOptions['section-width'] == "page-width" ) {
								//style += "max-width" + ":" + $scope.getPageWidth() + "px;";
							}
							if ( stateOptions['section-width'] == "custom" && stateOptions['custom-width']) {
								style += "max-width" + ":" + stateOptions['custom-width'] + ";";
							}
							if ( stateOptions['section-width'] == "full-width" ) {
								style += "max-width:100%;";
							}
						
							// custom-padding
							if ( stateOptions['container-padding-top'] ) {
								style += "padding-top" 		+ ":" + stateOptions['container-padding-top'] + ";";
							}
							if ( stateOptions['container-padding-right'] ) {
								style += "padding-right"	+ ":" + stateOptions['container-padding-right'] + ";";
							}
							if ( stateOptions['container-padding-bottom'] ) {
								style += "padding-bottom" 	+ ":" + stateOptions['container-padding-bottom'] + ";";
							}
							if ( stateOptions['container-padding-left'] ) {
								style += "padding-left" 	+ ":" + stateOptions['container-padding-left'] + ";";
							}

							// flex options since 2.0
							if ( stateOptions['display'] && stateOptions['flex-direction'] != 'grid' ) {
								style += "display:" + stateOptions['display'] + ";";
							}

							var reverse = (stateOptions['flex-reverse'] == 'reverse') ? "-reverse" : "";
							
							if ( stateOptions['flex-direction'] && stateOptions['flex-direction'] != 'grid' ) {
								style += "flex-direction:" + stateOptions['flex-direction'] + reverse + ";";
							}

							if ( stateOptions['justify-content'] ) {
								style += "justify-content:" + stateOptions['justify-content'] + ";";
							}

							if ( stateOptions['align-content'] ) {
								style += "align-content:" + stateOptions['align-content'] + ";";
							}

							if ( stateOptions['align-items'] ) {
								style += "align-items:" + stateOptions['align-items'] + ";";
							}

							if ( stateOptions['flex-wrap'] ) {
								style += "flex-wrap:" + stateOptions['flex-wrap'] + ";";
							}
							if ( stateOptions['gap'] ) {
								style += "gap:" + stateOptions['gap'] + ";";
							}

							style += $scope.getGridCSS(stateOptions, componentOptions.name) || "";
						
						style += '}';

						style += $scope.getGridChildCSS(stateOptions, componentOptions.selector, currentState, "> .ct-section-inner-wrap");

						if (componentOptions['original']["video_background_hide"]) {
							style += "@media (max-width: "+$scope.getMediaMaxSize(componentOptions['original']['video_background_hide'])+") {";
							style += '#' + componentOptions.selector + " .oxy-video-container { display: none; }";
							style += "}";
						}

						if (componentOptions['original']["video_background_overlay"]) {
							style += '#' + componentOptions.selector + " .oxy-video-overlay { background-color: " + componentOptions['original']["video_background_overlay"] + "; }";
						}
					}
					
					if (componentOptions.name == "ct_section") {
						style += $scope.getGridChildCSS(stateOptions, componentOptions.selector, currentState, false);
					}

					if (componentOptions.name == "oxy_posts_grid") {
						
						var gridStyles = $scope.getGridCSS(stateOptions, componentOptions.name, '!important') || "";
						
						if (gridStyles) {
							style += '#' + componentOptions.selector + "> .oxy-posts" + currentState + "{";
							style += gridStyles;
							style += '}';
							
							style += '#' + componentOptions.selector + currentState + "{";
							style += "flex-direction: column";
							style += '}';
						
							style += $scope.getGridChildCSS(stateOptions, componentOptions.selector, currentState, "> .oxy-posts");
						}
					}

					if ( componentOptions.name != "ct_section" && componentOptions.name != "oxy_posts_grid" && componentOptions.name != "oxy_dynamic_list" ) {
						style += $scope.getGridChildCSS(stateOptions, componentOptions.selector, currentState);
					}

					if (componentOptions.name == "oxy_dynamic_list") {

						style += $scope.getGridChildCSS(stateOptions, componentOptions.selector, currentState, "", false, "is repeater");

						$scope.mapCSSProperty('paginate_size', 'font-size',                         '.oxy-repeater-pages');
				        $scope.mapCSSProperty('paginate_color', 'color',                         '.oxy-repeater-pages');
				        $scope.mapCSSProperty('paginate_alignment', 'justify-content',                '.oxy-repeater-pages');
				        $scope.mapCSSProperty('paginate_wrap_alignment', 'justify-content',                '.oxy-repeater-pages-wrap');
				        $scope.mapCSSProperty('paginate_link_color', 'color',                    '.oxy-repeater-pages a.page-numbers');
				        $scope.mapCSSProperty('paginate_link_hover_color', 'color',              '.oxy-repeater-pages a.page-numbers:hover');

				        // pagination container padding and margin
				        $scope.mapCSSProperty('paginate_padding_top', 'padding-top',                '.oxy-repeater-pages');
                        $scope.mapCSSProperty('paginate_padding_left', 'padding-left',                '.oxy-repeater-pages');
                        $scope.mapCSSProperty('paginate_padding_right', 'padding-right',                         '.oxy-repeater-pages');
                        $scope.mapCSSProperty('paginate_padding_bottom', 'padding-bottom',                    '.oxy-repeater-pages');
                        $scope.mapCSSProperty('paginate_margin_top', 'margin-top',                '.oxy-repeater-pages');
                        $scope.mapCSSProperty('paginate_margin_left', 'margin-left',                '.oxy-repeater-pages');
                        $scope.mapCSSProperty('paginate_margin_right', 'margin-right',                         '.oxy-repeater-pages');
                        $scope.mapCSSProperty('paginate_margin_bottom', 'margin-bottom',                    '.oxy-repeater-pages');
                        
                        // pagination container size
                        $scope.mapCSSProperty('paginate_width', 'width',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_min_width', 'min-width',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_max_width', 'max-width',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_height', 'height',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_min_height', 'min-height',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_max_height', 'max-height',                    '.oxy-repeater-pages');

		                // pagination child elements layout
		                $scope.mapCSSProperty('paginate_flex_direction', 'flex-direction',                    '.oxy-repeater-pages');
                		$scope.mapCSSProperty('paginate_flex_wrap', 'flex-wrap',                    '.oxy-repeater-pages');

                		// pagination borders
		                $scope.mapCSSProperty('paginate_border_all_color', 'border-color',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_top_color', 'border-top-color',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_left_color', 'border-left-color',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_bottom_color', 'border-bottom-color',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_right_color', 'border-right-color',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_all_width', 'border-width',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_top_width', 'border-top-width',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_left_width', 'border-left-width',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_bottom_width', 'border-bottom-width',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_right_width', 'border-right-width',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_all_style', 'border-style',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_top_style', 'border-top-style',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_left_style', 'border-left-style',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_bottom_style', 'border-bottom-style',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_right_style', 'border-right-style',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_radius', 'border-radius',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_top_left_radius', 'border-top-left-radius',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_top_right_radius', 'border-top-right-radius',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_bottom_right_radius', 'border-bottom-right-radius',                    '.oxy-repeater-pages');
		                $scope.mapCSSProperty('paginate_border_bottom_left_radius', 'border-bottom-left-radius',                    '.oxy-repeater-pages');
		                // pagination link borders
				        $scope.mapCSSProperty('paginatelink_border_all_color', 'border-color',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_top_color', 'border-top-color',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_left_color', 'border-left-color',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_bottom_color', 'border-bottom-color',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_right_color', 'border-right-color',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_all_width', 'border-width',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_top_width', 'border-top-width',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_left_width', 'border-left-width',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_bottom_width', 'border-bottom-width',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_right_width', 'border-right-width',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_all_style', 'border-style',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_top_style', 'border-top-style',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_left_style', 'border-left-style',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_bottom_style', 'border-bottom-style',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_right_style', 'border-right-style',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_radius', 'border-radius',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_top_left_radius', 'border-top-left-radius',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_top_right_radius', 'border-top-right-radius',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_bottom_right_radius', 'border-bottom-right-radius',                    '.oxy-repeater-pages a');
				        $scope.mapCSSProperty('paginatelink_border_bottom_left_radius', 'border-bottom-left-radius',                    '.oxy-repeater-pages a');
				        // pagination active link borders
				        $scope.mapCSSProperty('paginatelinkactive_border_all_color', 'border-color',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_top_color', 'border-top-color',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_left_color', 'border-left-color',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_bottom_color', 'border-bottom-color',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_right_color', 'border-right-color',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_all_width', 'border-width',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_top_width', 'border-top-width',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_left_width', 'border-left-width',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_bottom_width', 'border-bottom-width',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_right_width', 'border-right-width',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_all_style', 'border-style',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_top_style', 'border-top-style',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_left_style', 'border-left-style',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_bottom_style', 'border-bottom-style',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_right_style', 'border-right-style',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_radius', 'border-radius',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_top_left_radius', 'border-top-left-radius',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_top_right_radius', 'border-top-right-radius',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_bottom_right_radius', 'border-bottom-right-radius',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_border_bottom_left_radius', 'border-bottom-left-radius',                    '.oxy-repeater-pages span.current');
		                // pagination background
				        $scope.mapCSSProperty("paginate_background_color", 'background-color',                    '.oxy-repeater-pages');
				        $scope.mapCSSProperty("paginate_background_image", 'background-image',                    '.oxy-repeater-pages');
				        $scope.mapCSSProperty("paginate_background_size", 'background-size',                    '.oxy-repeater-pages');
				        $scope.mapCSSProperty("paginate_background_size_width", 'background-size-width',                    '.oxy-repeater-pages');
				        $scope.mapCSSProperty("paginate_background_size_height", 'background-size-height',                    '.oxy-repeater-pages');
				        $scope.mapCSSProperty("paginate_background_repeat", 'background-repeat',                    '.oxy-repeater-pages');

				        // pagination link background
		                $scope.mapCSSProperty("paginatelink_background_color", 'background-color',                    '.oxy-repeater-pages a');
		                $scope.mapCSSProperty("paginatelink_background_image", 'background-image',                    '.oxy-repeater-pages a');
		                $scope.mapCSSProperty("paginatelink_background_size", 'background-size',                    '.oxy-repeater-pages a');
		                $scope.mapCSSProperty("paginatelink_background_size_width", 'background-size-width',                    '.oxy-repeater-pages a');
		                $scope.mapCSSProperty("paginatelink_background_size_height", 'background-size-height',                    '.oxy-repeater-pages a');
		                $scope.mapCSSProperty("paginatelink_background_repeat", 'background-repeat',                    '.oxy-repeater-pages a');

		                // pagination Active link background
		                $scope.mapCSSProperty("paginatelinkactive_background_color", 'background-color',                    '.oxy-repeater-pages span.current');
		                $scope.mapCSSProperty("paginatelinkactive_background_image", 'background-image',                    '.oxy-repeater-pages span.current');
		                $scope.mapCSSProperty("paginatelinkactive_background_size", 'background-size',                    '.oxy-repeater-pages span.current');
		                $scope.mapCSSProperty("paginatelinkactive_background_size_width", 'background-size-width',                    '.oxy-repeater-pages span.current');
		                $scope.mapCSSProperty("paginatelinkactive_background_size_height", 'background-size-height',                    '.oxy-repeater-pages span.current');
		                $scope.mapCSSProperty("paginatelinkactive_background_repeat", 'background-repeat',                    '.oxy-repeater-pages span.current');

				        // pagination links size and spacing
		                $scope.mapCSSProperty('paginatelink_padding_top', 'padding-top',                '.oxy-repeater-pages a');
		                $scope.mapCSSProperty('paginatelink_padding_left', 'padding-left',                '.oxy-repeater-pages a');
		                $scope.mapCSSProperty('paginatelink_padding_right', 'padding-right',                         '.oxy-repeater-pages a');
		                $scope.mapCSSProperty('paginatelink_padding_bottom', 'padding-bottom',                    '.oxy-repeater-pages a');
		                $scope.mapCSSProperty('paginatelink_margin_top', 'margin-top',                '.oxy-repeater-pages a');
		                $scope.mapCSSProperty('paginatelink_margin_left', 'margin-left',                '.oxy-repeater-pages a');
		                $scope.mapCSSProperty('paginatelink_margin_right', 'margin-right',                         '.oxy-repeater-pages a');
		                $scope.mapCSSProperty('paginatelink_margin_bottom', 'margin-bottom',                    '.oxy-repeater-pages a');

		                $scope.mapCSSProperty('paginatelink_width', 'width',                    '.oxy-repeater-pages a');
		                $scope.mapCSSProperty('paginatelink_min_width', 'min-width',                    '.oxy-repeater-pages a');
		                $scope.mapCSSProperty('paginatelink_max_width', 'max-width',                    '.oxy-repeater-pages a');
		                $scope.mapCSSProperty('paginatelink_height', 'height',                    '.oxy-repeater-pages a');
		                $scope.mapCSSProperty('paginatelink_min_height', 'min-height',                    '.oxy-repeater-pages a');
		                $scope.mapCSSProperty('paginatelink_max_height', 'max-height',                    '.oxy-repeater-pages a');

		                // pagination Active links size and spacing
				        $scope.mapCSSProperty('paginatelinkactive_padding_top', 'padding-top',                '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_padding_left', 'padding-left',                '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_padding_right', 'padding-right',                         '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_padding_bottom', 'padding-bottom',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_margin_top', 'margin-top',                '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_margin_left', 'margin-left',                '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_margin_right', 'margin-right',                         '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_margin_bottom', 'margin-bottom',                    '.oxy-repeater-pages span.current');

				        $scope.mapCSSProperty('paginatelinkactive_width', 'width',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_min_width', 'min-width',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_max_width', 'max-width',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_height', 'height',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_min_height', 'min-height',                    '.oxy-repeater-pages span.current');
				        $scope.mapCSSProperty('paginatelinkactive_max_height', 'max-height',                    '.oxy-repeater-pages span.current');

				        // pagination link hover background
				        $scope.mapCSSProperty("paginatelinkhover_background_color", 'background-color',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty("paginatelinkhover_background_image", 'background-image',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty("paginatelinkhover_background_size", 'background-size',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty("paginatelinkhover_background_size_width", 'background-size-width',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty("paginatelinkhover_background_size_height", 'background-size-height',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty("paginatelinkhover_background_repeat", 'background-repeat',                    '.oxy-repeater-pages a:hover');

				        // pagination Active link hover background
				        $scope.mapCSSProperty("paginatelinkactivehover_background_color", 'background-color',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty("paginatelinkactivehover_background_image", 'background-image',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty("paginatelinkactivehover_background_size", 'background-size',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty("paginatelinkactivehover_background_size_width", 'background-size-width',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty("paginatelinkactivehover_background_size_height", 'background-size-height',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty("paginatelinkactivehover_background_repeat", 'background-repeat',                    '.oxy-repeater-pages span.current:hover');

				        // pagination links hover size and spacing
				        $scope.mapCSSProperty('paginatelinkhover_padding_top', 'padding-top',                '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_padding_left', 'padding-left',                '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_padding_right', 'padding-right',                         '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_padding_bottom', 'padding-bottom',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_margin_top', 'margin-top',                '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_margin_left', 'margin-left',                '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_margin_right', 'margin-right',                         '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_margin_bottom', 'margin-bottom',                    '.oxy-repeater-pages a:hover');

				        $scope.mapCSSProperty('paginatelinkhover_width', 'width',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_min_width', 'min-width',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_max_width', 'max-width',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_height', 'height',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_min_height', 'min-height',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_max_height', 'max-height',                    '.oxy-repeater-pages a:hover');

				        // pagination Active links hover size and spacing
				        $scope.mapCSSProperty('paginatelinkactivehover_padding_top', 'padding-top',                '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_padding_left', 'padding-left',                '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_padding_right', 'padding-right',                         '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_padding_bottom', 'padding-bottom',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_margin_top', 'margin-top',                '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_margin_left', 'margin-left',                '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_margin_right', 'margin-right',                         '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_margin_bottom', 'margin-bottom',                    '.oxy-repeater-pages span.current:hover');

				        $scope.mapCSSProperty('paginatelinkactivehover_width', 'width',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_min_width', 'min-width',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_max_width', 'max-width',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_height', 'height',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_min_height', 'min-height',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_max_height', 'max-height',                    '.oxy-repeater-pages span.current:hover');

				        // pagination link hover borders
				        $scope.mapCSSProperty('paginatelinkhover_border_all_color', 'border-color',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_top_color', 'border-top-color',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_left_color', 'border-left-color',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_bottom_color', 'border-bottom-color',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_right_color', 'border-right-color',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_all_width', 'border-width',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_top_width', 'border-top-width',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_left_width', 'border-left-width',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_bottom_width', 'border-bottom-width',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_right_width', 'border-right-width',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_all_style', 'border-style',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_top_style', 'border-top-style',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_left_style', 'border-left-style',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_bottom_style', 'border-bottom-style',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_right_style', 'border-right-style',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_radius', 'border-radius',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_top_left_radius', 'border-top-left-radius',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_top_right_radius', 'border-top-right-radius',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_bottom_right_radius', 'border-bottom-right-radius',                    '.oxy-repeater-pages a:hover');
				        $scope.mapCSSProperty('paginatelinkhover_border_bottom_left_radius', 'border-bottom-left-radius',                    '.oxy-repeater-pages a:hover');
				        // pagination active link hover borders
				        $scope.mapCSSProperty('paginatelinkactivehover_border_all_color', 'border-color',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_top_color', 'border-top-color',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_left_color', 'border-left-color',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_bottom_color', 'border-bottom-color',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_right_color', 'border-right-color',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_all_width', 'border-width',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_top_width', 'border-top-width',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_left_width', 'border-left-width',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_bottom_width', 'border-bottom-width',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_right_width', 'border-right-width',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_all_style', 'border-style',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_top_style', 'border-top-style',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_left_style', 'border-left-style',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_bottom_style', 'border-bottom-style',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_right_style', 'border-right-style',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_radius', 'border-radius',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_top_left_radius', 'border-top-left-radius',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_top_right_radius', 'border-top-right-radius',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_bottom_right_radius', 'border-bottom-right-radius',                    '.oxy-repeater-pages span.current:hover');
				        $scope.mapCSSProperty('paginatelinkactivehover_border_bottom_left_radius', 'border-bottom-left-radius',                    '.oxy-repeater-pages span.current:hover');
				        // pagination link transition
				        $scope.mapCSSProperty('paginate_link_transition', 'transition',                    '.oxy-repeater-pages > *');
				    }

					if (componentOptions.name == "oxy_posts_grid") {

						$scope.mapCSSProperty('title_size', 'font-size',                         '.oxy-post-title');
				        $scope.mapCSSProperty('title_color', 'color',                            '.oxy-post-title');
				        $scope.mapCSSProperty('title_hover_color', 'color',                      '.oxy-post-title:hover');
				        $scope.mapCSSProperty('meta_size', 'font-size',                          '.oxy-post-meta');
				        $scope.mapCSSProperty('meta_color', 'color',                             '.oxy-post-meta');
				        $scope.mapCSSProperty('content_size', 'font-size',                       '.oxy-post-content');
				        $scope.mapCSSProperty('content_color', 'color',                          '.oxy-post-content');
				        $scope.mapCSSProperty('read_more_size', 'font-size',                     '.oxy-read-more');
				        $scope.mapCSSProperty('read_more_text_color', 'color',                   '.oxy-read-more');
				        $scope.mapCSSProperty('read_more_button_color', 'background-color',      '.oxy-read-more');
				        $scope.mapCSSProperty('read_more_text_hover_color', 'color',             '.oxy-read-more:hover');
				        $scope.mapCSSProperty('read_more_button_hover_color', 'background-color','.oxy-read-more:hover');
				        $scope.mapCSSProperty('paginate_color', 'color',                         '.oxy-easy-posts-pages');
				        $scope.mapCSSProperty('paginate_alignment', 'text-align',                '.oxy-easy-posts-pages');
				        $scope.mapCSSProperty('paginate_link_color', 'color',                    '.oxy-easy-posts-pages a.page-numbers');
				        $scope.mapCSSProperty('paginate_link_hover_color', 'color',              '.oxy-easy-posts-pages a.page-numbers:hover');

				        if (componentOptions['original']['read_more_display_as']=="button") {
							style += '#' + componentOptions.selector + " .oxy-read-more {";
							style += "text-decoration:none;";
							style += "padding:0.75em 1.5em;";
							style += "line-height:1;";
							style += "border-radius:3px;";
							style += "display:inline-block;";
							style += "}";
						}
						else {
					        $scope.unmapCSSProperty('background-color','.oxy-read-more');
					        $scope.unmapCSSProperty('background-color','.oxy-read-more:hover');
						}

				        style += "@media (max-width: "+$scope.getMediaMaxSize(componentOptions['original']['posts_5050_below'])+") {";
						style += '#' + componentOptions.selector + " .oxy-post { width: 50% !important; }";
						style += "}";

						style += "@media (max-width: "+$scope.getMediaMaxSize(componentOptions['original']['posts_100_below'])+") {";
						style += '#' + componentOptions.selector + " .oxy-post { width: 100% !important; }";
						style += "}";																					
					}

					if (componentOptions.name == "oxy_comment_form") {

				        $scope.mapCSSProperty('form_field_border_color', 'border-color', 	'input, textarea');
        				$scope.mapCSSProperty('form_field_text_color', 'color',          	'input, textarea');
        				$scope.mapCSSProperty('field_border_radius', 'border-radius',    	'input, textarea');

        				$scope.mapCSSProperty('submit_button_background_color', 'background-color',  '#submit');
        				$scope.mapCSSProperty('submit_button_text_color', 'color',                   '#submit');
				    }

				    if (componentOptions.name == "oxy_login_form") {

				        $scope.mapCSSProperty('form_field_border_color', 'border-color', 	'input, textarea');
        				$scope.mapCSSProperty('form_field_text_color', 'color',          	'input, textarea');
        				$scope.mapCSSProperty('field_border_radius', 'border-radius',    	'input, textarea');

        				$scope.mapCSSProperty('submit_button_background_color', 'background-color',  'input[type=submit]');
        				$scope.mapCSSProperty('submit_button_text_color', 'color',                   'input[type=submit]');
				    }

				    if (componentOptions.name == "oxy_search_form") {

				        $scope.mapCSSProperty('form_field_border_color', 'border-color', 	'input, textarea');
        				$scope.mapCSSProperty('form_field_text_color', 'color',          	'input, textarea');
        				$scope.mapCSSProperty('field_border_radius', 'border-radius',    	'input, textarea');

        				$scope.mapCSSProperty('submit_button_background_color', 'background-color',  'input[type=submit]');
        				$scope.mapCSSProperty('submit_button_text_color', 'color',                   'input[type=submit]');
				    }

					if (componentOptions.name == "ct_video") {

						style = style + '#' + componentOptions.selector + ">.oxygen-vsb-responsive-video-wrapper" + currentState + "{";
						
						var videoPadding = componentOptions['original']['video-padding-bottom'] || componentDefaults['video-padding-bottom'];

						if ( videoPadding ) {
							style += "padding-bottom:" + videoPadding + ";";
						}

						style  += "}";
					}
					var nonMediaOptions;

					if(componentOptions.name == 'ct_new_columns') {
						nonMediaOptions = componentOptions['original'];
					}

					if (componentOptions.name == "ct_new_columns" && nonMediaOptions['set-columns-width-50']
																  && nonMediaOptions['set-columns-width-50'] != 'never') {
						
						style += "@media (max-width: "+$scope.getMediaMaxSize(nonMediaOptions['set-columns-width-50'])+") {";
						style += '#' + componentOptions.selector + "> .ct-div-block {width: 50% !important;}";
						style += "}";
					}

					if ( componentOptions.name == "ct_new_columns" ) {
					
						var reverseColumnOrder 			 = parseInt($scope.getMediaMaxSize(nonMediaOptions['reverse-column-order'])) || 0,
							stackColumnsVertically 		 = parseInt($scope.getMediaMaxSize(nonMediaOptions['stack-columns-vertically'])) || 0,
							reverseColumnOrderStyles 	 = "",
							stackColumnsVerticallyStyles = "";

						if (nonMediaOptions['reverse-column-order']=="always") {
							reverseColumnOrder = 9999999999;
						}

						if ( nonMediaOptions['stack-columns-vertically'] && 
							 nonMediaOptions['stack-columns-vertically'] != 'never') {

							stackColumnsVerticallyStyles += "@media (max-width: "+$scope.getMediaMaxSize(nonMediaOptions['stack-columns-vertically'])+") {";
								stackColumnsVerticallyStyles += '#' + componentOptions.selector + "> .ct-div-block {";
								stackColumnsVerticallyStyles += "width: 100% !important;";
								stackColumnsVerticallyStyles += "}";
								
								stackColumnsVerticallyStyles += '#' + componentOptions.selector + "{";
								if (stackColumnsVertically>reverseColumnOrder) {
									stackColumnsVerticallyStyles += "flex-direction: column;";
								}
								else {
									stackColumnsVerticallyStyles += "flex-direction: column-reverse;";
								}
								stackColumnsVerticallyStyles += "}";
							stackColumnsVerticallyStyles += "}";
						}

						if ( nonMediaOptions['reverse-column-order'] && 
							 nonMediaOptions['reverse-column-order'] != 'never' && 
							 nonMediaOptions['reverse-column-order'] != 'always' ) {

							reverseColumnOrderStyles += "@media (max-width: "+$scope.getMediaMaxSize(nonMediaOptions['reverse-column-order'])+") {";
							reverseColumnOrderStyles += '#' + componentOptions.selector + "{";
							if (stackColumnsVertically<reverseColumnOrder) {
								reverseColumnOrderStyles += "flex-direction: row-reverse;";
							}
							else {
								reverseColumnOrderStyles += "flex-direction: column-reverse;";
							}
							reverseColumnOrderStyles += "}";
							reverseColumnOrderStyles += "}";
						}

						if ( nonMediaOptions['reverse-column-order'] == 'always') {
		
							style += '#' + componentOptions.selector + "{flex-direction: row-reverse;}";
						}

						if (stackColumnsVertically<reverseColumnOrder) {
							style += reverseColumnOrderStyles;
							style += stackColumnsVerticallyStyles;
						}
						else {
							style += stackColumnsVerticallyStyles;
							style += reverseColumnOrderStyles;
						}

					}

					if ( componentOptions.name == "oxy_header" ) {

						style += '#' + componentOptions.selector + " .oxy-header-container" + currentState + "{";

							if (undefined==stateOptions['header-width']) {
								stateOptions['header-width'] = componentDefaults['header-width'];
							}
							if ( stateOptions['header-width'] == "custom" && stateOptions['header-custom-width']) {
								style += "max-width" + ":" + stateOptions['header-custom-width'] + ";";
							}
							if ( stateOptions['header-width'] == "full-width" ) {
								style += "max-width:100%;";
							}
						style += "}";

        				if ( componentOptions['original']['overlay-header-above'] && componentOptions['original']['overlay-header-above']!="never") {

        					if ( componentOptions['original']['overlay-header-above']!="always" ) {
								style += '@media (min-width: '+$scope.getMediaMinSize(componentOptions['original']['overlay-header-above'])+') {';
							}
							style += '#' + componentOptions.selector + '.oxy-header.oxy-overlay-header {'+
									'position: absolute;'+
									'left: 0;'+
									'right: 0;'+
									'z-index: 20;'+
								'}'+
								'#' + componentOptions.selector + '.oxy-header.oxy-overlay-header:not(.oxy-sticky-header-active) .oxy-header-row,'+
								'#' + componentOptions.selector + '.oxy-header.oxy-overlay-header:not(.oxy-sticky-header-active){'+
									'background-color: initial !important;'+
								'}'+
								'#' + componentOptions.selector + '.oxy-header.oxy-overlay-header .oxygen-hide-in-overlay{'+
									'display: none'+
								'}' +
								'#' + componentOptions.selector + '.oxy-header.oxy-overlay-header .oxygen-only-show-in-overlay{'+
									'display: block'+
								'}';
        					if ( componentOptions['original']['overlay-header-above']!="always" ) {
								style += '}';
							}
						}

						var stickyStyles = "";

						if ( componentOptions['original']['sticky-media'] && 
							 componentOptions['original']['sticky-media'] != 'never') {

							if (componentOptions['original']['sticky-media'] != 'always') {
							stickyStyles += "@media (min-width: "+$scope.getMediaMinSize(componentOptions['original']['sticky-media'])+") {";
							}
								stickyStyles += '#' + componentOptions.selector + ".oxy-header-wrapper.oxy-header.oxy-sticky-header.oxy-sticky-header-active {";
								stickyStyles += "position: fixed; top: 0; left: 0; right: 0; z-index: " + (componentOptions['original']['sticky_zindex'] || '2147483640') + ";";
								stickyStyles += componentOptions['original']['sticky-background-color'] ? "background-color:"+$scope.getGlobalColorValue(componentOptions['original']['sticky-background-color'])+";" : "";
								var boxShadow = componentOptions['original']['sticky-box-shadow'] || componentDefaults['sticky-box-shadow'];
								stickyStyles += "box-shadow:" + boxShadow +";";
								stickyStyles += "}";

								stickyStyles += '#' + componentOptions.selector + ".oxy-header.oxy-sticky-header-active .oxygen-hide-in-sticky {";
								stickyStyles += "display:none;";
								stickyStyles += "}";

								stickyStyles += '#' + componentOptions.selector + ".oxy-header.oxy-header .oxygen-show-in-sticky-only {";
								stickyStyles += "display: none;";
								stickyStyles += "}";

							if (componentOptions['original']['sticky-media'] != 'always') {
							stickyStyles += "}";
							}
						}

						if (stickyStyles) {
							style += stickyStyles;
						}

						if (componentOptions['original']['sticky_header_fade_in_speed']) {
							style += '#' + componentOptions.selector + ".oxy-sticky-header-active {" +
								"animation-duration: " + componentOptions['original']['sticky_header_fade_in_speed'] + "s;"+
	 						"}";
	 					}
					}

					if ( componentOptions.name == "oxy_header" || componentOptions.name == "oxy_header_row" ) {
						
						var stackHeaderVerticallyStyles = "";

						if ( componentOptions['original']['stack-header-vertically'] && 
							 componentOptions['original']['stack-header-vertically'] != 'never') {

							stackHeaderVerticallyStyles += "@media (max-width: "+$scope.getMediaMaxSize(componentOptions['original']['stack-header-vertically'])+") {";
								
								stackHeaderVerticallyStyles += '#' + componentOptions.selector + " .oxy-header-container {";
								stackHeaderVerticallyStyles += "flex-direction: column;";
								stackHeaderVerticallyStyles += "}";
								
								stackHeaderVerticallyStyles += '#' + componentOptions.selector + " .oxy-header-container > div{";
								stackHeaderVerticallyStyles += "justify-content: center;";
								stackHeaderVerticallyStyles += "}";
								
							stackHeaderVerticallyStyles += "}";
						}

						if (stackHeaderVerticallyStyles) {
							style += stackHeaderVerticallyStyles;
						}
					}

					if ( componentOptions.name == "oxy_header_row" ) {

						style += '#' + componentOptions.selector + ".oxy-header-row .oxy-header-container" + currentState + "{";

							if (undefined==stateOptions['header-row-width']) {
								stateOptions['header-row-width'] = componentDefaults['header-row-width'];
							}
							if ( stateOptions['header-row-width'] == "custom" && stateOptions['header-row-custom-width']) {
								style += "max-width" + ":" + stateOptions['header-row-custom-width'] + ";";
							}
							if ( stateOptions['header-row-width'] == "full-width" ) {
								style += "max-width:100%;";
							}
							if ( stateOptions['header-row-width'] == "page-width" ) {
								style += "max-width:" + $scope.getPageWidth() + "px;"
							}
						style += "}";
						
						var hideRowStyles = "",
							showRowStyles = "";

						if ( componentOptions['original']['hide-row'] && 
							 componentOptions['original']['hide-row'] != 'never') {

							hideRowStyles += "@media (max-width: "+$scope.getMediaMaxSize(componentOptions['original']['hide-row'])+") {";
								
								hideRowStyles += '#' + componentOptions.selector + " {";
								hideRowStyles += "display: none;";
								hideRowStyles += "}";
								
							hideRowStyles += "}";
						}

						if (hideRowStyles) {
							style += hideRowStyles;				
						}

						var display = stateOptions['display'] || "block";
					
						showRowStyles += '.oxy-header.oxy-sticky-header-active > #' + componentOptions.selector + ".oxygen-show-in-sticky-only {";
						showRowStyles += "display: " + display + ";";
						showRowStyles += "}";

						style += showRowStyles;
					}

					if ( componentOptions.name == "ct_slider" ) {

						if (!isMedia && stateName == "id") {
							if ( stateOptions['slider-arrow-color'] && 
								 stateOptions['slider-arrow-color'] == 'lighter') {

									style += '#' + componentOptions.selector + " .unslider-arrow {";
									style += "background-color: rgba(255,255,255,0.2); ";
									style += "}";
							}

							if ( stateOptions['slider-dot-color'] ) {

									style += '#' + componentOptions.selector + " .unslider-nav ol li {";
									style += "border-color: " + $scope.getGlobalColorValue(stateOptions['slider-dot-color']) + "; ";
									style += "}";

									style += '#' + componentOptions.selector + " .unslider-nav ol li.unslider-active {";
									style += "background-color: " + $scope.getGlobalColorValue(stateOptions['slider-dot-color']) + "; ";
									style += "}";
							}

							if ( stateOptions['slider-remove-padding'] && 
								 stateOptions['slider-remove-padding'] == 'yes') {

									style += '#' + componentOptions.selector + " .unslider {";
									style += "padding: 0px; ";
									style += "}";

									style += '#' + componentOptions.selector + " .unslider-wrap.unslider-carousel > li {";
									style += "padding: 0px; ";
									style += "}";

									style += '#' + componentOptions.selector + " .unslider-arrow.next {";
									style += "right: 10px; ";
									style += "z-index: 100; ";
									style += "}";

									style += '#' + componentOptions.selector + " .unslider-arrow.prev {";
									style += "left: 10px; ";
									style += "z-index: 100; ";
									style += "}";
							}

							if ( stateOptions['slider-dots-overlay'] && 
								 stateOptions['slider-dots-overlay'] == 'yes') {

									style += '#' + componentOptions.selector + " .unslider .unslider-nav {";
									style += "position: absolute; bottom: 0; left: 0; right: 0; z-index:100";
									style += "}";
							}
							if ( stateOptions['slider-stretch-slides'] && 
								 stateOptions['slider-stretch-slides'] == 'yes') {

									style += '#' + componentOptions.selector + " .unslider-wrap {";
									style += "display: flex;";
									style += "}";

									style += '#' + componentOptions.selector + " .ct-slide {";
									style += "height: 100%;";
									style += "}";

									style += '#' + componentOptions.selector + " .unslider,";
									style += '#' + componentOptions.selector + " .oxygen-unslider-container,";
									style += '#' + componentOptions.selector + " .unslider-wrap,";
									style += '#' + componentOptions.selector + " .unslider-wrap li {";
									style += "height: 100%;";
									style += "}";
							}

							if ( stateOptions['slider-stretch-slides'] && 
								 stateOptions['slider-stretch-slides'] == 'yes' &&
								 stateOptions['slider-animation'] && 
								 stateOptions['slider-animation'] == 'fade' ) {

									style += '#' + componentOptions.selector + " .unslider-fade ul li.unslider-active {";
									style += "width: 100%;";
									style += "}";
							}

							if ( stateOptions['slider-slide-padding'] ) {

									style += '#' + componentOptions.selector + " .ct-slide {";
									style += "padding: " + stateOptions['slider-slide-padding'];
									style += "}";
							}
						}
					}


					if (componentOptions.name == "ct_link_button") {
						
						if (!isMedia && stateName == "id") {
							var buttonStyle 	= originalOptions['button-style'] || componentDefaults['button-style'],
								buttonColor 	= $scope.getGlobalColorValue(stateOptions['button-color']),
								buttonSize 		= stateOptions['button-size'],
								buttonTextColor = $scope.getGlobalColorValue(stateOptions['button-text-color']);
						}
						else {
							var buttonStyle 	= $scope.component.options[componentId] ? $scope.component.options[componentId]['original']['button-style'] : false,
								buttonColor 	= $scope.getGlobalColorValue(stateOptions['button-color']),
								buttonSize 		= stateOptions['button-size'],
								buttonTextColor = $scope.getGlobalColorValue(stateOptions['button-text-color']);
						}
						style = style + '#' + componentOptions.selector  + currentState + "{";

						if (buttonStyle == 1 && buttonColor) { // solid
							style += "background-color: " + $scope.getGlobalColorValue(buttonColor) + ";";
							style += "border: 1px solid " + buttonColor + ";";
							if (buttonTextColor) {
								style += "color: " + $scope.getGlobalColorValue(buttonTextColor) + ";";
							}
						}
						
						if (buttonStyle == 2) { // outline
							style += "background-color: transparent;";
							if (buttonColor) {
								style += "border: 1px solid " + $scope.getGlobalColorValue(buttonColor) + ";";
								style += "color: " + $scope.getGlobalColorValue(buttonColor) + ";";
							}
							else {
								style += "color: " + $scope.getGlobalColorValue(componentDefaults['button-color']) + ";";
							}
						}

						if (buttonSize) {
							var substracted = buttonStyle == 2 ? 1 : 0;
							style += "padding: " + (parseInt(buttonSize)-substracted) + 'px ' + (parseInt(buttonSize)*1.6-substracted) + 'px;';
						}

						style += "}";
					}

					if (componentOptions.name == "ct_fancy_icon") {
						
						if (!isMedia && stateName == "id") {
							var iconStyle 			= originalOptions['icon-style'] || componentDefaults['icon-style'],
								iconColor 			= $scope.getGlobalColorValue(stateOptions['icon-color']),
								iconBackgroundColor = $scope.getGlobalColorValue(stateOptions['icon-background-color']) || componentDefaults['icon-background-color'],
								iconPadding 		= stateOptions['icon-padding'] || componentDefaults['icon-padding']+componentDefaults['icon-padding-unit'],
								iconSize 			= stateOptions['icon-size'];
						}
						else {
							var iconStyle 			= $scope.component.options[componentId] ? $scope.component.options[componentId]['original']['icon-style'] : false,
								iconColor 			= $scope.getGlobalColorValue(stateOptions['icon-color']),
								iconBackgroundColor = $scope.getGlobalColorValue(stateOptions['icon-background-color']),
								iconPadding 		= stateOptions['icon-padding'],
								iconSize 			= stateOptions['icon-size'];
						}

						style = style + '#' + componentOptions.selector + currentState + "{";

						if (iconStyle == 1) { // outline
							style += "border: 1px solid;";
						}

						if (iconStyle == 2) { // solid
							if (iconBackgroundColor) {
								style += "background-color: " + $scope.getGlobalColorValue(iconBackgroundColor) + ";";
								style += "border: 1px solid " + $scope.getGlobalColorValue(iconBackgroundColor) + ";";
							}
						}
						
						if (iconStyle == 1 || iconStyle == 2) { // outline or solid
						 	if (iconPadding) {
						 		style += "padding:" + iconPadding + ";";
						 	}
						} 

						if (iconColor) {
						 	style += "color:" + $scope.getGlobalColorValue(iconColor) + ";";
						}

						style += "}";

						if (iconSize) {
							style = style + "#" + componentOptions.selector + currentState + ">svg {";
						 	style += "width: " + iconSize + ";";
						 	style += "height: " + iconSize + ";";
						 	style += "}";
						}
					}

					if (componentOptions.name == "oxy_social_icons") {

						var iconStyle 		= componentOptions['original']['icon-style'] || componentDefaults['icon-style'],
							useBrandColors 	= $scope.getGlobalColorValue(componentOptions['original']['icon-use-brand-colors']) || componentDefaults['icon-use-brand-colors'];

						style += '#' + componentOptions.selector + ".oxy-social-icons {";
							style += "flex-direction: " + ( componentOptions['original']['icon-layout'] || componentDefaults['icon-layout'] ) + ";";
							style += "margin-right: -" + ( componentOptions['original']['icon-space-between-icons'] || componentDefaults['icon-space-between-icons'] ) + "px;";
							style += "margin-bottom: -" + ( componentOptions['original']['icon-space-between-icons'] || componentDefaults['icon-space-between-icons'] ) + "px;";
						style += "}";

						style += '#' + componentOptions.selector + ".oxy-social-icons a {";
							style += "font-size: " + ( componentOptions['original']['icon-size'] || componentDefaults['icon-size'] ) + "px; ";
							style += "margin-right: " + ( componentOptions['original']['icon-space-between-icons'] || componentDefaults['icon-space-between-icons'] ) + "px;";
							style += "margin-bottom: " + ( componentOptions['original']['icon-space-between-icons'] || componentDefaults['icon-space-between-icons'] ) + "px;";
							if (iconStyle=='circle') {
								style += "border-radius: 50%;";
							}
							else if (iconStyle=='square') {
								style += "border-radius: 0%;";
							}
							if (iconStyle!='blank') {
								style += "background-color: " + ( $scope.getGlobalColorValue(componentOptions['original']['icon-background-color']) || componentDefaults['icon-background-color'] ) + ";";
							}
						style += "}";

						style += '#' + componentOptions.selector + ".oxy-social-icons a:hover {";
						if (iconStyle!='blank') {
							style += "background-color: " + ( $scope.getGlobalColorValue(componentOptions['original']['icon-background-hover-color']) || componentDefaults['icon-background-hover-color'] ) + ";";
						}
						style += "}";

						if (useBrandColors=='yes') {
							for(var key in $scope.socialIcons.networks) { 
								if ($scope.socialIcons.networks.hasOwnProperty(key)) {
									var network 	= $scope.socialIcons.networks[key],
										color 		= $scope.socialIcons.networkColors[network],
										hoverColor 	= $scope.socialIcons.networkHoverColors[network];

									if (iconStyle!='blank') {
										style += '#' + componentOptions.selector + ".oxy-social-icons a.oxy-social-icons-"+network+" {";
										style += "background-color: "+color+";";
										style += "}";

										style += '#' + componentOptions.selector + ".oxy-social-icons a.oxy-social-icons-"+network+":hover {";
										style += "background-color: "+hoverColor+";";
										style += "}";

										componentOptions['original']['icon-color'] 		= "#ffffff";
										componentOptions['original']['icon-hover-color']= "#ffffff";
									}
									else {
										style += '#' + componentOptions.selector + ".oxy-social-icons a.oxy-social-icons-"+network+" svg {";
										style += "color: "+color+";";
										style += "}";

										style += '#' + componentOptions.selector + ".oxy-social-icons a.oxy-social-icons-"+network+":hover svg {";
										style += "color: "+hoverColor+";";
										style += "}";
									}
								}
							}
						}

						style += '#' + componentOptions.selector + ".oxy-social-icons a svg {";
						if (iconStyle!='blank') {
							style += "width: 0.5em; height: 0.5em;";
						} else {
							style += "width: 1em; height: 1em;";
						}
						style += "color: " + ( $scope.getGlobalColorValue(componentOptions['original']['icon-color']) || componentDefaults['icon-color'] ) + "; ";
						style += "}";

						style += '#' + componentOptions.selector + ".oxy-social-icons a:hover svg {";
						style += "color: " + ( $scope.getGlobalColorValue(componentOptions['original']['icon-hover-color']) || componentDefaults['icon-hover-color'] ) + "; ";
						style += "}";
					}


					if (componentOptions.name == "oxy_testimonial") {

						var flexAlign,
				            textAlign,
				            mobileFlexAlign,
				            mobileTextAlign,
				            marginCSS = "",
				            spacing = stateOptions['testimonial_image_spacing'];

				        if (stateOptions["testimonial_layout"]=='vertical') {

				        	marginCSS = "margin:0;";
                			spacing = stateOptions['testimonial_image_spacing'] || componentDefaults['testimonial_image_spacing']+componentDefaults['testimonial_image_spacing-unit'];

				        	if (stateOptions["testimonial_image_position"]=='bottom') {
				            	marginCSS += "margin-top";
				            }
				            else {
				            	marginCSS += "margin-bottom";
				            }
				        } else {
				            if (stateOptions["testimonial_image_position"]=='bottom') {
				            	
				            	marginCSS = "margin:0;";
                				spacing = stateOptions['testimonial_image_spacing'] || componentDefaults['testimonial_image_spacing']+componentDefaults['testimonial_image_spacing-unit'];

				            	marginCSS += "margin-left";
				            }
				            else {
				            	marginCSS += "margin-right";
				            }
				        }

				        marginCSS += ": " + spacing + ";";

						if (stateOptions["testimonial_content_alignment"]=='left') {
				            flexAlign = "flex-start";
				            textAlign = "left";
				        } else if (stateOptions["testimonial_content_alignment"]=='center') {
				            flexAlign = "center";
				            textAlign = "center";
				        } else if (stateOptions["testimonial_content_alignment"]=='right') {
				            flexAlign = "flex-end";
				            textAlign = "right";
				        }

				        if (componentOptions['original']["testimonial_mobile_content_alignment"]=='left') {
				            mobileFlexAlign = "flex-start";
				            mobileTextAlign = "left";
				        } else if (componentOptions['original']["testimonial_mobile_content_alignment"]=='center') {
				            mobileFlexAlign = "center";
				            mobileTextAlign = "center";
				        } else if (componentOptions['original']["testimonial_mobile_content_alignment"]=='right') {
				            mobileFlexAlign = "flex-end";
				            mobileTextAlign = "right";
				        }

				        if (stateOptions["testimonial_layout"]=='vertical') {
				        	style += '#' + componentOptions.selector + ' {' +
				                'flex-direction: column;' +
				            '}';
				        }
				        else if (stateOptions["testimonial_layout"]=='horizontal')  {
				            style += '#' + componentOptions.selector + ' {' +
				                'flex-direction: row;' +
				                'align-items: center;' +
				           '}';
				        }
				        
				        if (stateOptions['testimonial_image_position'] == 'top') {
				            style += '#' + componentOptions.selector + ' .oxy-testimonial-photo-wrap {' +
				                'order: 1;' +
				           '}';
				        } 
				        else if (stateOptions['testimonial_image_position'] == 'bottom') {
				            style += '#' + componentOptions.selector + ' .oxy-testimonial-photo-wrap {' +
				                'order: 3;' +
				           '}';
				        } 

						style += '#' + componentOptions.selector + " .oxy-testimonial-photo {"+
				            "width:"+ stateOptions['testimonial_image_size'] + ";" +
				            "height:"+ stateOptions['testimonial_image_size'] + ";" +
				            marginCSS +
				        "}";

						style += '#' + componentOptions.selector + " .oxy-testimonial-photo-wrap,"+
								 '#' + componentOptions.selector + " .oxy-testimonial-author-wrap,"+
								 '#' + componentOptions.selector + " .oxy-testimonial-content-wrap {"+
				            "align-items:"+ flexAlign + ";" +
				            "text-align:"+ textAlign + ";" +
				        "}";

						style += '#' + componentOptions.selector + " .oxy-testimonial-text {"+
				            "margin-bottom:"+ stateOptions['testimonial_text_space_below'] + ";" +
				            $scope.generateTypographyCSS(stateOptions,'testimonial_text_typography') +
				        "}";

				        style += '#' + componentOptions.selector + " .oxy-testimonial-author {"+
				            "margin-bottom:" + stateOptions['testimonial_author_space_below'] + ";" +
				            $scope.generateTypographyCSS(stateOptions,'testimonial_author_typography') +
				        "}";

				        style += '#' + componentOptions.selector + " .oxy-testimonial-author-info {"+
				            "margin-bottom:" + stateOptions['testimonial_author_info_space_below'] + ";" +
				            $scope.generateTypographyCSS(stateOptions,'testimonial_author_info_typography') +
				        "}";

				        maxSize = $scope.getMediaMaxSize(componentOptions['original']['testimonial_vertical_layout_below'])
				        if (maxSize) {

				        	var marginPosition;
				        	if (stateOptions["testimonial_image_position"]=='bottom') {
		                        marginPosition = "top";
		                    } 
		                    else {
		                        marginPosition = "bottom";
		                    }

					        style += '@media (max-width: '+ maxSize + '){' +
				                '#' + componentOptions.selector + ' {' +
				                    'flex-direction: column !important;' +
				                '}' +  

				                '#' + componentOptions.selector + ' .oxy-testimonial-photo {' +
				                    'margin: 0;'+
				                    'margin-'+marginPosition+':' + stateOptions['testimonial_image_spacing'] + ';' +
				                '}'+

				                '#' + componentOptions.selector + ' .oxy-testimonial-photo-wrap, ' +
				                '#' + componentOptions.selector + ' .oxy-testimonial-author-wrap, ' +
				                '#' + componentOptions.selector + ' .oxy-testimonial-content-wrap {' +
				                    'align-items:' + mobileFlexAlign + ';' +
				                    'text-align:' + mobileTextAlign + ';' +
				                '}'+
				            '}';
				        }

					}


					if (componentOptions.name == "oxy_icon_box") {

						var icon_position_flex_direction,
							icon_vertical_alignment_align_self,
							iconmargincss,
							mobileflexalign,
							mobiletextalign;
						
						if (stateOptions['icon_box_icon_position'] == 'top') {
				            icon_position_flex_direction = 'column';
				        } else if (stateOptions['icon_box_icon_position'] == 'left') {
				            icon_position_flex_direction = 'row';
				        } else if (stateOptions['icon_box_icon_position'] == 'right') {
				            icon_position_flex_direction = 'row-reverse';
				        } else if (stateOptions['icon_box_icon_position'] == 'bottom') {
				            icon_position_flex_direction = 'column-reverse';
				        }

				        if (stateOptions["icon_box_icon_position"]=='left' || stateOptions["icon_box_icon_position"]=='right') {
				        	var left = stateOptions["icon_box_icon_space_before"] || componentDefaults["icon_box_icon_space_before"]+componentDefaults["icon_box_icon_space_before-unit"];
				        		right = stateOptions["icon_box_icon_space_after"] || componentDefaults["icon_box_icon_space_after"]+componentDefaults["icon_box_icon_space_after-unit"];
				            iconmargincss = "margin-left: "+left+";\n";
				            iconmargincss += "margin-right: "+right+";\n";
				            iconmargincss += "margin-bottom: 0; margin-top: 0;";
				        } else {
				            iconmargincss = "margin-top: "+stateOptions["icon_box_icon_space_before"]+";\n";
				            iconmargincss += "margin-bottom: "+stateOptions["icon_box_icon_space_after"]+";\n";
				        }

				        if (stateOptions["icon_box_icon_position"]=='left' || stateOptions["icon_box_icon_position"]=='right') {
				            icon_vertical_alignment_align_self = stateOptions['icon_box_icon_vertical_alignment'];
				        } else {
				            if (stateOptions["icon_box_content_alignment"]=='left') {
				                icon_vertical_alignment_align_self = "flex-start";
				            } else if (stateOptions["icon_box_content_alignment"]=='center') {
				                icon_vertical_alignment_align_self = "center";
				            } else if (stateOptions["icon_box_content_alignment"]=='right') {
				                icon_vertical_alignment_align_self = "flex-end";
				            }          
				        }

				        if (stateOptions["icon_box_mobile_content_alignment"]=='left') {
				            mobileflexalign = "flex-start";
				            mobiletextalign = "left";
				        } else if (stateOptions["icon_box_mobile_content_alignment"]=='center') {
				            mobileflexalign = "center";
				            mobiletextalign = "center";
				        } else if (stateOptions["icon_box_mobile_content_alignment"]=='right') {
				            mobileflexalign = "flex-end";
				            mobiletextalign = "right";
				        } else {
				            mobileflexalign = "flex-start";
				            mobiletextalign = "left";            
				        }

			            style += '#' + componentOptions.selector + ' {'+
			                'text-align: '+ stateOptions['icon_box_content_alignment'] + ";" +
			                'flex-direction: '+ icon_position_flex_direction + ';'+
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-icon-box-icon {'+
			                iconmargincss +
			                'align-self: ' + icon_vertical_alignment_align_self + ';' +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-icon-box-heading {'+
			                $scope.generateTypographyCSS(stateOptions,'icon_box_heading_typography')+
			                'margin-top: ' + stateOptions['icon_box_heading_space_above'] + ';' +
			                'margin-bottom: ' + stateOptions['icon_box_heading_space_below'] + ';' +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-icon-box-text {'+
			                $scope.generateTypographyCSS(stateOptions,'icon_box_text_typography')+
			                'margin-top: ' + stateOptions['icon_box_text_space_above'] + ';' +
			                'margin-bottom: ' + stateOptions['icon_box_text_space_below'] + ';' +
			                'align-self: ' + icon_vertical_alignment_align_self + ';' +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-icon-box-link {'+
			                'margin-top: ' + stateOptions['icon_box_link_space_above'] + ';' +
			                'margin-bottom: ' + stateOptions['icon_box_link_space_below'] + ';' +
			            '}';

			            maxSize = $scope.getMediaMaxSize(stateOptions['icon_box_vertical_layout_below']);
			            if (maxSize && !isMedia) {
				            style += '@media (max-width: ' + maxSize + '){' +
				                '#' + componentOptions.selector + '.oxy-icon-box {' +
				                    'flex-direction: column !important;' +
				                    'text-align: ' + mobiletextalign + ';' +
				                '}' +

				                '#' + componentOptions.selector + ' .oxy-icon-box-icon {' +
				                    'margin-left: 0;' +
				                    'margin-right: 0;' +
				                    'margin-top: ' + stateOptions["icon_box_icon_space_before"] + ';' +
				                    'margin-bottom: ' + stateOptions["icon_box_icon_space_after"] + ';' +
				                '}' +

				                '#' + componentOptions.selector + ' .oxy-icon-box-icon, .oxy-icon-box-text {' +
				                    'align-self: ' + mobileflexalign + ';' +
				                '}' +
				            '}';
				        }
					}


					if (componentOptions.name == "oxy_pricing_box") {

						var price_flex_alignment, price_flex_direction, price_justify_content, global_justify_content, image_justify_content, cta_justify_content;
						
						if (stateOptions["pricing_box_price_layout"]=='vertical') {
				            price_flex_direction = "column";
				            if (stateOptions["pricing_box_price_alignment"]=='left') {
				                price_flex_alignment = "flex-start";
				            } else if (stateOptions["pricing_box_price_alignment"]=='center') {
				                price_flex_alignment = "center";
				            } else if (stateOptions["pricing_box_price_alignment"]=='right') {
				                price_flex_alignment = "flex-end";
				            } 
				        } else if (stateOptions["pricing_box_price_layout"]=='horizontal') {
				            price_flex_direction = "row";
				        }

				        if (stateOptions["pricing_box_price_layout"]=='horizontal') {
				            if (stateOptions["pricing_box_price_alignment"]=='left') {
				                price_justify_content = "flex-start";
				            } else if (stateOptions["pricing_box_price_alignment"]=='center') {
				                price_justify_content = "center";
				            } else if (stateOptions["pricing_box_price_alignment"]=='right') {
				                price_justify_content = "flex-end";
				            }
				        }

				        if (stateOptions["pricing_box_global_alignment"]=='left') {
				            global_justify_content = "flex-start";
				        } else if (stateOptions["pricing_box_global_alignment"]=='center') {
				            global_justify_content = "center";
				        } else if (stateOptions["pricing_box_global_alignment"]=='right') {
				            global_justify_content = "flex-end";
				        }


				        if (stateOptions["pricing_box_graphic_alignment"]=='left') {
				            image_justify_content = "flex-start";
				        } else if (stateOptions["pricing_box_graphic_alignment"]=='center') {
				            image_justify_content = "center";
				        } else if (stateOptions["pricing_box_graphic_alignment"]=='right') {
				            image_justify_content = "flex-end";
				        } else if (global_justify_content) {
				        	iamge_justify_content = global_justify_content;
				        }

				        if (stateOptions["pricing_box_cta_alignment"]=='left') {
				            cta_justify_content = "flex-start";
				        } else if (stateOptions["pricing_box_cta_alignment"]=='center') {
				            cta_justify_content = "center";
				        } else if (stateOptions["pricing_box_cta_alignment"]=='right') {
				            cta_justify_content = "flex-end";
				        } else if (global_justify_content) {
				        	cta_justify_content = global_justify_content;
				        }


			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-section {' +
			                $scope.generateArrayOptionsCSS(stateOptions, 'pricing_box_global') +
			                'text-align:' + stateOptions['pricing_box_global_alignment'] + ";" +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-section.oxy-pricing-box-price {' +
			                'justify-content:' + global_justify_content + ';' +
			            '}';


			            /* IMAGE */
			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-section.oxy-pricing-box-graphic {' +
			                $scope.generateArrayOptionsCSS(stateOptions, 'pricing_box_graphic') +
			                'background-color:' + $scope.getGlobalColorValue(stateOptions['pricing_box_graphic_background']) + ';' +
			                'text-align:'+ stateOptions['pricing_box_graphic_alignment'] + ';' +
			                'justify-content:' + image_justify_content + ';' +
			            '}';

			            /* TITLE */
			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-section.oxy-pricing-box-title {' +
			                $scope.generateArrayOptionsCSS(stateOptions, 'pricing_box_title') +
			                'text-align:'+ stateOptions['pricing_box_title_alignment'] + ';' +
			                'background-color:' + $scope.getGlobalColorValue(stateOptions['pricing_box_title_background']) + ';' +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-title-title {' +
			                $scope.generateTypographyCSS(stateOptions, 'pricing_box_title_typography') +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-title-subtitle {' +
			                $scope.generateTypographyCSS(stateOptions, 'pricing_box_subtitle_typography') +
			            '}';


			            /* PRICE */

			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-section.oxy-pricing-box-price {' +
			                $scope.generateArrayOptionsCSS(stateOptions, 'pricing_box_price') +
			                'text-align:' + stateOptions['pricing_box_price_alignment'] + ';' +
			                'background-color:' + $scope.getGlobalColorValue(stateOptions['pricing_box_price_background']) + ';' +
			                'flex-direction:' + price_flex_direction + ';' +
			                'justify-content:' + price_justify_content + ';' +
			                'align-items:' + price_flex_alignment + ';' +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-currency {' +
			                $scope.generateTypographyCSS(stateOptions, 'pricing_box_price_amount_currency_typography') +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-amount-main {' +
			                $scope.generateTypographyCSS(stateOptions, 'pricing_box_price_amount_main_typography') +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-amount-decimal {' +
			                $scope.generateTypographyCSS(stateOptions, 'pricing_box_price_amount_decimal_typography') +
			            '}';
			            
			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-term {' +
			                $scope.generateTypographyCSS(stateOptions, 'pricing_box_price_amount_term_typography') +
			            '}';

			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-sale-price {' +
			                $scope.generateTypographyCSS(stateOptions, 'pricing_box_price_sale_typography') +
			                'margin-bottom:' + stateOptions['pricing_box_price_sale_space_below'] + ';' +
			            '}';

			            /* CONTENT */
			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-section.oxy-pricing-box-content {' +
			                'background-color:' + $scope.getGlobalColorValue(stateOptions['pricing_box_content_background']) + ';' +
			                'text-align:' + stateOptions['pricing_box_content_alignment'] + ';' +
			                $scope.generateTypographyCSS(stateOptions, 'pricing_box_content_typography') +
			            '}';

			            /* CTA */
			            style += '#' + componentOptions.selector + ' .oxy-pricing-box-section.oxy-pricing-box-cta {' +
			                $scope.generateArrayOptionsCSS(stateOptions, 'pricing_box_cta') +
			                'background-color:' + $scope.getGlobalColorValue(stateOptions['pricing_box_cta_background']) + ';' +
			                'text-align:' + stateOptions['pricing_box_cta_alignment'] + ';' +
			                'justify-content:' + cta_justify_content + ';' +
			            '}';
					}


					if (componentOptions.name == "oxy_progress_bar") {

						var stripecss = "",
							animation_css = [];
						
						if (stateOptions['progress_bar_stripes'] == 'true') {
				            stripecss = "background-image: linear-gradient(-45deg,rgba(255,255,255,.12) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.12) 50%,rgba(255,255,255,.12) 75%,transparent 75%,transparent);";
				        }
				        if (stateOptions['progress_bar_stripes'] == 'false') {
				            stripecss = 'background-image:none;';
				        }

				        if (stateOptions['progress_bar_animation_stripes'] == 'true') {
				        	var stripesDuration = stateOptions['progress_bar_animation_stripes_duration'] || 
				        				     componentDefaults['progress_bar_animation_stripes_duration'];
				            animation_css['stripes'] = "oxy_progress_bar_stripes "+stripesDuration+" linear infinite";
				        }
				        if (stateOptions['progress_bar_animation_stripes'] == 'false') {
				        	animation_css['stripes'] = "none 0s paused";
				        }

				        if (stateOptions['progress_bar_animate_width'] == 'true') {
				        	var widthDuration = stateOptions['progress_bar_animation_width_duration'] || 
				        				   componentDefaults['progress_bar_animation_width_duration'];
				            animation_css['width'] = "oxy_progress_bar_width "+widthDuration+" ease-out 1";
				        }
				        if (stateOptions['progress_bar_animate_width'] == 'false') {
				        	animation_css['width'] = "none 0s paused";
				        }

				        var bar_animation = [];
				        for(var key in animation_css) {
							bar_animation.push(animation_css[key]);
				        }

			            style += '#' + componentOptions.selector + currentState + ' .oxy-progress-bar-background {' +
			                'background-color:' + $scope.getGlobalColorValue(stateOptions['progress_bar_background_color']) + ';' +
			                 stripecss;
			            if (animation_css['stripes']) {
			            	style += 'animation:' + animation_css['stripes'] + ';';
			            }
			            style += '}';

			            style += '#' + componentOptions.selector + currentState + ' .oxy-progress-bar-progress-wrap {' +
			                'width:' + stateOptions['progress_bar_progress'] + ';' +
			            '}';

			            style += '#' + componentOptions.selector + currentState + ' .oxy-progress-bar-progress {' +
			                'background-color:' + $scope.getGlobalColorValue(stateOptions['progress_bar_bar_color']) + ';' +
			                'padding:' + stateOptions['progress_bar_bar_padding'] + ';' +
			                 stripecss +
			                'animation:' + bar_animation.join() + ';' +
			            '}';

			            style += '#' + componentOptions.selector + currentState + ' .oxy-progress-bar-overlay-text {' +
			                $scope.generateTypographyCSS(stateOptions, 'progress_bar_left_text_typography') +
			            '}';

			            style += '#' + componentOptions.selector + currentState + ' .oxy-progress-bar-overlay-percent {' +
			                $scope.generateTypographyCSS(stateOptions, 'progress_bar_right_text_typography') +
			            '}';
					}

					if (componentOptions.name == "oxy_toggle") {
						
						style += '#' + componentOptions.selector + currentState + ' .oxy-expand-collapse-icon {' +
			                'font-size:' + stateOptions['toggle_icon_size'] + ';' +
			            '}';

			            style += '#' + componentOptions.selector + currentState + ' .oxy-expand-collapse-icon::before,' +
			            		 '#' + componentOptions.selector + currentState + ' .oxy-expand-collapse-icon::after {' +
			                'background-color:' + $scope.getGlobalColorValue(stateOptions['toggle_icon_color']) + ';' +
			            '}';
					}

					if (componentOptions.name == "oxy_superbox") {

						// scaling
				        var superbox_secondary_scale_start  = stateOptions['superbox_secondary_scale_start'],
				        	superbox_secondary_scale_finish = stateOptions['superbox_secondary_scale_finish'],
				        	superbox_primary_scale_start    = stateOptions['superbox_primary_scale_start'],
				        	superbox_primary_scale_finish   = stateOptions['superbox_primary_scale_finish'],
				        	css, 
				        	superbox_secondary_initial_css, superbox_secondary_hover_css,
				        	superbox_primary_initial_css, superbox_primary_hover_css,
				        	superbox_secondary_scale_start_transform_css,
							superbox_secondary_scale_finish_transform_css,
							superbox_primary_scale_start_transform_css,
							superbox_primary_scale_finish_transform_css,
							superbox_secondary_initial_css = "",
							superbox_secondary_hover_css = "",
							superbox_primary_initial_css = "",
							superbox_primary_hover_css = "",
							state = ":hover";

						if ( componentOptions['original']['superbox_editing_mode'] == "as_hovered" ) {
							state = "";
						}

				        if (superbox_secondary_scale_start !== undefined)     superbox_secondary_scale_start_transform_css = "transform: scale("+superbox_secondary_scale_start+");";
				        if (superbox_secondary_scale_finish !== undefined)    superbox_secondary_scale_finish_transform_css = "transform: scale("+superbox_secondary_scale_finish+");";
				        if (superbox_primary_scale_start !== undefined)       superbox_primary_scale_start_transform_css = "transform: scale("+superbox_primary_scale_start+");";
				        if (superbox_primary_scale_finish !== undefined)      superbox_primary_scale_finish_transform_css = "transform: scale("+superbox_primary_scale_finish+");";

				        // sliding
				        if (stateOptions['superbox_secondary_slide_direction']) {
				            css = $scope.slide_position(stateOptions['superbox_secondary_slide_direction'], (stateOptions['superbox_secondary_slide_distance']||'')+"px");

				            if (stateOptions['superbox_secondary_slide_inorout'] == "in") {
				                superbox_secondary_initial_css = css['out_css'];
				                superbox_secondary_hover_css = css['in_css'];
				            } else if (stateOptions['superbox_secondary_slide_inorout'] == "out") {
				                superbox_secondary_initial_css = css['in_css'];
				                superbox_secondary_hover_css = css['out_css'];            
				            }
				        }

				        if (stateOptions['superbox_primary_slide_direction']) {

				            css = $scope.slide_position(stateOptions['superbox_primary_slide_direction'], (stateOptions['superbox_primary_slide_distance']||'')+"px");

				            if (stateOptions['superbox_primary_slide_inorout'] == "in") {
				                superbox_primary_initial_css = css['out_css'];
				                superbox_primary_hover_css = css['in_css'];
				            } else if (stateOptions['superbox_primary_slide_inorout'] == "out") {
				                superbox_primary_initial_css = css['in_css'];
				                superbox_primary_hover_css = css['out_css'];            
				            }
				        }

				        var transitionDuration = stateOptions['superbox_transition_duration'];

				        style += '#' + componentOptions.selector + " .oxy-superbox-secondary, " +
				         		 '#' + componentOptions.selector + " .oxy-superbox-primary {" +
				             'transition-duration:' + transitionDuration + ';' +
				        '}';

				        style += '#' + componentOptions.selector + " .oxy-superbox-secondary {" +
				            'opacity:' + stateOptions['superbox_secondary_opacity_start'] + ";" +
				            superbox_secondary_initial_css +
				            superbox_secondary_scale_start_transform_css +
				        '}';

				        if ( componentOptions['original']['superbox_editing_mode'] !== "as_not_hovered" ) {
					        style += '#' + componentOptions.selector + state + " .oxy-superbox-secondary {" +
					            'opacity:' + stateOptions['superbox_secondary_opacity_finish'] + ";" +
					            'z-index: 2147483642;' +
					            superbox_secondary_hover_css +
					            superbox_secondary_scale_finish_transform_css +
					        '}';
					    }

				        style += '#' + componentOptions.selector + " .oxy-superbox-primary {" +
				            'opacity:' + stateOptions['superbox_primary_opacity_start'] + ";" +
				            superbox_primary_initial_css +
				            superbox_primary_scale_start_transform_css +
				        '}';

				        if ( componentOptions['original']['superbox_editing_mode'] !== "as_not_hovered" ) {
					        style += '#' + componentOptions.selector + state + " .oxy-superbox-primary {" +
					            'opacity:' + stateOptions['superbox_primary_opacity_finish'] + ";" +
					            superbox_primary_hover_css +
					            superbox_primary_scale_finish_transform_css +
					        '}';
					    }
					}

					if (componentOptions.name == "oxy_nav_menu") {
						oxyNavMenuStyle 			= '#' + componentOptions.selector + " .oxy-nav-menu-list" + currentState + "{";
						oxyNavMenuStyleItem 		= '#' + componentOptions.selector + " .menu-item" + currentState + " a {";
						oxyNavMenuStyleActive 		= '#' + componentOptions.selector + " .current-menu-item a" + currentState + "{";
						oxyNavMenuStyleDropdowns 	= '#' + componentOptions.selector + ".oxy-nav-menu:not(.oxy-nav-menu-open) .sub-menu" + currentState + "{";
						oxyNavMenuStyleDropdownsItem= '#' + componentOptions.selector + ".oxy-nav-menu:not(.oxy-nav-menu-open) .sub-menu .menu-item a" + currentState + "{";
						oxyNavMenuStyleNotOpenItem  = '#' + componentOptions.selector + ".oxy-nav-menu:not(.oxy-nav-menu-open) .menu-item a" + currentState + "{";
						oxyNavMenuStyleDropdownsItemHover= '#' + componentOptions.selector + ".oxy-nav-menu:not(.oxy-nav-menu-open) .oxy-nav-menu-list .sub-menu .menu-item a:hover{";

						if (!componentOptions['original']['menu_dropdowns_background-color']) {
							oxyNavMenuStyleDropdowns += "background-color:" + $scope.getGlobalColorValue(componentOptions['original']['menu_hover_background-color']) + ";";
						}
						
						oxyNavMenuStyleDropdownsItem += "border: 0;";

						if (componentOptions['original']['menu_flex-direction']=='row') {
							oxyNavMenuStyleDropdownsItem += "padding-top:" 		+ componentOptions['original']['menu_padding-top'] + "px;";
							oxyNavMenuStyleDropdownsItem += "padding-bottom:"	+ componentOptions['original']['menu_padding-bottom'] + "px;";
						}
						else {
							oxyNavMenuStyleDropdownsItem += "padding-left:" 	+ componentOptions['original']['menu_padding-top'] + "px;";
							oxyNavMenuStyleDropdownsItem += "padding-rigth:"	+ componentOptions['original']['menu_padding-bottom'] + "px;";
						}

						if (componentOptions['original']['menu_justify-content']) {
							oxyNavMenuStyleNotOpenItem += "justify-content:" + componentOptions['original']['menu_justify-content'] + ";";
						}

						if (componentOptions['original']['menu_responsive']!='never') {
							if (componentOptions['original']['menu_responsive']!='always') {
							style += "@media (max-width: "+$scope.getMediaMaxSize(componentOptions['original']['menu_responsive'])+") {";
							}						
							style += "#" + componentOptions.selector + " .oxy-nav-menu-list {display: none;}";
							style += "#" + componentOptions.selector + " .oxy-menu-toggle {display: initial;}";
							style += "#" + componentOptions.selector + ".oxy-nav-menu.oxy-nav-menu-open .oxy-nav-menu-list {display: initial;}";
							if (componentOptions['original']['menu_responsive']!='always') {
							style += "}";
							}
						}

						var menuWidth 	= parseInt(componentOptions['original']['menu_responsive_icon_size']||40),
							menuWrapSize= parseInt(componentOptions['original']['menu_responsive_icon_size']||40)+(parseInt(componentOptions['original']['menu_responsive_padding_size']||0)*2),
							menuHeight 	= parseInt(menuWidth * 0.8),
							lineHeight 	= parseInt(menuWidth * 0.15),
							top 		= (menuHeight / 2) - (lineHeight / 2);

						if (!isMedia && stateName == "id") {
							style += 
							'#' + componentOptions.selector + ".oxy-nav-menu.oxy-nav-menu-open {"+
								"background-color:" + $scope.getGlobalColorValue(componentOptions['original']['menu_responsive_background_color']) + ";" +
								"margin-top: 0 !important;" +
								"margin-right: 0 !important;" +
								"margin-left: 0 !important;" +
								"margin-bottom: 0 !important;" +
							
							'} #' + componentOptions.selector + ".oxy-nav-menu.oxy-nav-menu-open .menu-item a {" +
								"color:" 			+ $scope.getGlobalColorValue(componentOptions['original']['menu_responsive_link_color']) + ";" +
								"padding-top:" 		+ (componentOptions['original']['menu_responsive_padding_top'] || componentOptions['original']['menu_padding-top']) + "px;" +
								"padding-right:" 	+ (componentOptions['original']['menu_responsive_padding_right'] || componentOptions['original']['menu_padding-right']) + "px;" +
								"padding-bottom:" 	+ (componentOptions['original']['menu_responsive_padding_bottom'] || componentOptions['original']['menu_padding-bottom']) + "px;" +
								"padding-left:" 	+ (componentOptions['original']['menu_responsive_padding_left'] || componentOptions['original']['menu_padding-left']) + "px;" +
							
							'} #' + componentOptions.selector + ".oxy-nav-menu.oxy-nav-menu-open .menu-item a:hover {" +
								"color:" + $scope.getGlobalColorValue(componentOptions['original']['menu_responsive_hover_link_color']) +

							'} #' + componentOptions.selector + ".oxy-nav-menu .menu-item a:hover {" +
								"text-decoration:" + componentOptions['original']['menu_text-decoration'] +

							'} #' + componentOptions.selector + " .oxy-nav-menu-hamburger-wrap {"+
								"width:" 			+ menuWrapSize + "px;" +
								"height:" 			+ menuWrapSize + "px;" +
								"margin-top:" 		+ componentOptions['original']['menu_responsive_icon_margin'] + "px;" +
								"margin-bottom:" 	+ componentOptions['original']['menu_responsive_icon_margin'] + "px;" +
								"background-color:" + $scope.getGlobalColorValue(componentOptions['original']['menu_responsive_padding_color']) + ";" +

							'} #' + componentOptions.selector + " .oxy-nav-menu-hamburger-wrap:hover {"+
								"background-color:" + $scope.getGlobalColorValue(componentOptions['original']['menu_responsive_padding_hover_color']) + ";" +
							
							"} #" + componentOptions.selector + " .oxy-nav-menu-hamburger {" +
								"width:"  + menuWidth + "px;" +
								"height:" + menuHeight + "px;" +
							
							"} #" + componentOptions.selector + " .oxy-nav-menu-hamburger .oxy-nav-menu-hamburger-line {" +
								"height:" + lineHeight + "px;" +
								"background-color:" + $scope.getGlobalColorValue(componentOptions['original']['menu_responsive_icon_color']) + ";" +

							"} #" + componentOptions.selector + " .oxy-nav-menu-hamburger-wrap:hover  .oxy-nav-menu-hamburger-line {" +
								"background-color:" + $scope.getGlobalColorValue(componentOptions['original']['menu_responsive_icon_hover_color']) + ";" +
							
							"} #" + componentOptions.selector + ".oxy-nav-menu-open .oxy-nav-menu-hamburger .oxy-nav-menu-hamburger-line:first-child {" +
								"top:" + top + "px;" +
							
							"} #" + componentOptions.selector + ".oxy-nav-menu-open .oxy-nav-menu-hamburger .oxy-nav-menu-hamburger-line:last-child {" +
								"top:-"+ top + "px;}";

							style +='#' + componentOptions.selector + " .menu-item > .sub-menu {" +
								'transition-duration:' + componentOptions['original']['menu_transition-duration'] + 's; }';
						}

					}

					// make menu icon same color as links color
					if ((componentOptions.name == "oxy_nav_menu") && 
						 componentOptions['original']["menu_color"]) {
						style += '#' + componentOptions.selector + " .oxy-nav-menu-hamburger-line{";
						style += "background-color:"+$scope.getGlobalColorValue(componentOptions['original']["menu_color"])+";";
						style += "}";
					}

					// make open menu bg same as header/row bg
					if ((componentOptions.name == "oxy_header" || componentOptions.name == "oxy_header_row") && 
						 componentOptions['original']["background-color"]) {
						style += '#' + componentOptions.selector + " .oxy-nav-menu-open,";
						style += '#' + componentOptions.selector + " .oxy-nav-menu:not(.oxy-nav-menu-open) .sub-menu{";
						style += "background-color:"+$scope.getGlobalColorValue(componentOptions['original']["background-color"])+";";
						style += "}";
					}

					// handle box-shadow options
					if ( stateOptions['box-shadow-color'] ) {

						var inset 	= (stateOptions['box-shadow-inset']=='inset') 		? stateOptions['box-shadow-inset']+" " : "";
						var hor 	= (stateOptions['box-shadow-horizontal-offset']) 	? stateOptions['box-shadow-horizontal-offset']+"px " : "";
						var ver 	= (stateOptions['box-shadow-vertical-offset']) 		? stateOptions['box-shadow-vertical-offset']+"px " : "";
						var blur 	= (stateOptions['box-shadow-blur']) 				? stateOptions['box-shadow-blur']+"px " : "0px ";
						var spread  = (stateOptions['box-shadow-spread']) 				? stateOptions['box-shadow-spread']+"px " : "";
						
						stateOptions['box-shadow'] = inset+hor+ver+blur+spread+$scope.getGlobalColorValue(stateOptions['box-shadow-color']);
					}

					// handle text-shadow options
					if ( stateOptions['text-shadow-color'] ) {

						var hor 	= (stateOptions['text-shadow-horizontal-offset']) 	? stateOptions['text-shadow-horizontal-offset']+"px " : "";
						var ver 	= (stateOptions['text-shadow-vertical-offset']) 	? stateOptions['text-shadow-vertical-offset']+"px " : "";
						var blur 	= (stateOptions['text-shadow-blur']) 				? stateOptions['text-shadow-blur']+"px " : "0px ";
						
						stateOptions['text-shadow'] = hor+ver+blur+$scope.getGlobalColorValue(stateOptions['text-shadow-color']);
					}

					// TODO: add check for elements with no ID styles to not output empty selectors
					style += '#' + componentOptions.selector + paragraph + currentState + "{";
					// make sure its the same selector, and styles are not being applied based on 'just' ID
			    	if ($scope.component.options[componentId] && ($scope.component.options[componentId].selector !== componentOptions.selector && !componentOptions.original)) {
						// do nothing;
			    	}
			    	else if (typeof(stateOptions) === 'object') {
						style += $scope.getBackgroundLayersCSS(stateOptions, componentOptions.name, 'id', componentId, whichMedia, stateName) || "";
						style += $scope.getTransformCSS(stateOptions) || "";

						if ( componentOptions.name != "ct_section" && componentOptions.name != "oxy_posts_grid" ) {
							style += $scope.getGridCSS(stateOptions, componentOptions.name) || "";
							if ( stateOptions['flex-direction'] == 'grid' ) {
								stateOptions['display'] = "";
								stateOptions['flex-direction'] = "";
							}
						}

						// loop state's options
						for(var parameter in stateOptions) {

							// skip fake states parameters
							if (parameter.indexOf("hover_")>=0) {
								continue;
							}

							// skip fake responsive parameters
							if (parameter.indexOf("responsive")>=0) {
								continue;
							}

							// skip fake responsive parameters
							if (parameter.indexOf("slider")>=0) {
								continue;
							}

							// skip fake icon parameters
							if (parameter.indexOf("icon")>=0) {
								continue;
							}

							// skip fake SoundCloud parameters
							if (parameter.indexOf("soundcloud")>=0) {
								continue;
							}
							

							if (parameter=="background-size" || parameter=="background") {
								continue;
							}

							if (stateOptions.hasOwnProperty(parameter)) {

								var value = stateOptions[parameter];

								if (parameter=="custom-css") {
									continue;
								}

								if (componentOptions.name == "ct_fancy_icon" &&
									(	parameter=="background" ||
										parameter=="icon-background-color" ||
										parameter=="icon-color" ||
										parameter=="padding" ||
										parameter=="icon-size"

									)) {
									continue;
								}

								// since 2.0
								if ( ["display","flex-direction","flex-wrap","align-items","align-content","justify-content", "gap"].indexOf(parameter) >= 0 
									 && componentOptions.name == "ct_section" ) { 
									continue; // this is already added for inner wrap
								}

								// load Web Fonts
								if (parameter == "font-family"||parameter.indexOf("font-family")>-1) {
									$scope.loadWebFont(value);
									if ( value.indexOf(',') === -1 && value.toLowerCase() !== "inherit") {
										value = "'"+value+"'";
									}
								}

								// filter the value for global colors 
								value = $scope.getGlobalColorValue(value);

								if (parameter.trim().toLowerCase() == "content") {
									//value = "\"" + $scope.addSlashes(value) + "\"";
									value = "\"" + value.replace('"','\\"') + "\"";
									$scope.contentAdded['#' + componentOptions.selector + paragraph + currentState] = true;
								}

								// check fro global colors

								if (parameter=="flex-direction") {
									var reverse = (stateOptions['flex-reverse'] == 'reverse') ? "-reverse" : "";
									style += parameter + ":" + value + reverse + ";";
									continue;
								}

								// css filter property
								if ( parameter == "filter" && stateOptions["filter-amount-"+value] ) {
									value += "("+stateOptions["filter-amount-"+value]+")";
								}
								else if ( parameter == "filter" ) {
									continue;
								}

								// handle specific Nav Menu options that applies to menu items
								if (componentOptions.name == "oxy_nav_menu" && parameter.indexOf("menu_dropdowns_")===0) {
									if (parameter=="menu_dropdowns_background-color" && stateName!== "hover") {
										oxyNavMenuStyleDropdowns += parameter.replace("menu_dropdowns_","") + ":" + value + ";";
									}
									else if (value && $scope.excludeProperties.indexOf(parameter) < 0 && parameter) {
										oxyNavMenuStyleDropdownsItem += parameter.replace("menu_dropdowns_","") + ":" + value + ";";
									}
									// make hover padding the same to prevent jump
									if (parameter.indexOf("menu_dropdowns_padding") == 0) {
										oxyNavMenuStyleDropdownsItemHover += parameter.replace("menu_dropdowns_","") + ":" + value + ";";
									}
								}
								else if (componentOptions.name == "oxy_nav_menu" && parameter.indexOf("menu_active_")===0) {

									if (parameter == "menu_active_border-bottom-width" && componentOptions['original']['menu_padding-bottom'] !== undefined ) {
										// subtrac border from padding
										var newPadding = parseInt(componentOptions['original']['menu_padding-bottom']) - parseInt(stateOptions['menu_active_border-bottom-width']);
										newPadding = (newPadding > 0) ? newPadding : 0;
										oxyNavMenuStyleActive += "padding-bottom:" + newPadding + "px;";
									}

									if (parameter == "menu_active_border-top-width" && componentOptions['original']['menu_padding-top'] !== undefined ) {
										// subtrac border from padding
										var newPadding = parseInt(componentOptions['original']['menu_padding-top']) - parseInt(stateOptions['menu_active_border-top-width']);
										newPadding = (newPadding > 0) ? newPadding : 0;
										oxyNavMenuStyleActive += "padding-top:" + newPadding + "px;";
									}

									// finally just add the value as is
									if (value && $scope.excludeProperties.indexOf(parameter) < 0 && parameter) {
										oxyNavMenuStyleActive += parameter.replace("menu_active_","") + ":" + value + ";";
									}
								}
								else if (componentOptions.name == "oxy_nav_menu" && parameter.indexOf("menu_")===0) {

									if (parameter == "menu_flex-direction") {
										oxyNavMenuStyle += "flex-direction:" + value;
										continue;
									}

									if (parameter == "menu_justify-content"||parameter == "menu_dropdown_arrow") {
										continue;
									}

									if (parameter == "menu_border-bottom-width" && stateName == "hover" && componentOptions['original']['menu_padding-bottom'] !== undefined ) {
										// subtrac border from padding
										var newPadding = parseInt(componentOptions['original']['menu_padding-bottom']) - parseInt(stateOptions['menu_border-bottom-width']);
										newPadding = (newPadding > 0) ? newPadding : 0;
										if (componentOptions['original']['menu_flex-direction']=='row') {
											var paddingProp = "padding-bottom:";
										}
										else {
											var paddingProp = "padding-right:";
											parameter = "border-right-width"
										}
										oxyNavMenuStyleItem += paddingProp + newPadding + "px;";
									}

									if (parameter == "menu_border-top-width" && stateName == "hover" && componentOptions['original']['menu_padding-top'] !== undefined ) {
										// subtrac border from padding
										var newPadding = parseInt(componentOptions['original']['menu_padding-top']) - parseInt(stateOptions['menu_border-top-width']);
										newPadding = (newPadding > 0) ? newPadding : 0;
										if (componentOptions['original']['menu_flex-direction']=='row') {
											var paddingProp = "padding-top:";
										}
										else {
											var paddingProp = "padding-left:";
											parameter = "border-left-width"
										}
										oxyNavMenuStyleItem += paddingProp + newPadding + "px;";
									}
									
									if (parameter == "menu_-webkit-font-smoothing") {
										oxyNavMenuStyleItem += '-moz-osx-font-smoothing' + ":" + (value === 'antialiased' ? 'grayscale' : 'unset') + ";";
									}

									if (parameter == "menu_transition-duration") {
										value += "s";
									}
									
									// finally just add the value as is
									if (value && $scope.excludeProperties.indexOf(parameter) < 0 && parameter) {
										oxyNavMenuStyleItem += parameter.replace("menu_","") + ":" + value + ";";
									}
								}
								
								else {
									if(parameter=='background-image') { // this is being taken care off by getBackgroundLayersCSS function
										continue;
									}
									if (value && $scope.excludeProperties.indexOf(parameter) < 0 && parameter !== "background-layers") {

										if ($scope.notCSSOptions[componentOptions.name]===undefined||$scope.notCSSOptions[componentOptions.name].indexOf(parameter.replace(/-/g, '_')) < 0){
											style += parameter + ":" + value + ";";
										}
										
									}	
									if (parameter == "-webkit-font-smoothing") {
										style += '-moz-osx-font-smoothing' + ":" + (value === 'antialiased' ? 'grayscale' : 'unset') + ";";
									}
								}
							}
						}	
					}
					
					if ((stateName=="before"||stateName=="after")&&!$scope.contentAdded['#' + componentOptions.selector + paragraph + currentState]) {
						style += "content:\"\";";
						$scope.contentAdded['#' + componentOptions.selector + paragraph + currentState] = true;
					}

					if (stateOptions["custom-css"]) {
						style += $scope.replaceGlobalColors(stateOptions["custom-css"]);
					}

					style += '}';

					// add specific Nav Menu styles that applies to menu items 
					if (componentOptions.name == "oxy_nav_menu") {
						style  += oxyNavMenuStyle + '}' 
								+ oxyNavMenuStyleItem + '}' 
								+ oxyNavMenuStyleActive + '}'
								+ oxyNavMenuStyleDropdowns + '}'
								+ oxyNavMenuStyleNotOpenItem + '}'
								+ oxyNavMenuStyleDropdownsItem + '}'
								+ oxyNavMenuStyleDropdownsItemHover + '}';
					}

				}
				
				// Add API element's default CSS
				if ($scope.componentsTemplates !== undefined && 
		        	$scope.componentsTemplates[componentOptions.name] && 
		        	$scope.componentsTemplates[componentOptions.name]['pageCSS'] &&
		        	$scope.APIElementsDefaultCSS[componentOptions.name] !== true ) {

					var pageCSS = $scope.componentsTemplates[componentOptions.name]['pageCSS'];

					// filter global settings
					pageCSS = $scope.replaceGlobalWooSettings(pageCSS);

					$scope.APIElementsDefaultCSS[componentOptions.name] = true;

					style += pageCSS;
				}
				
				// Add API custom CSS selectors
				if ($scope.componentsTemplates !== undefined && 
		        	$scope.componentsTemplates[componentOptions.name] && 
		        	$scope.componentsTemplates[componentOptions.name]['registeredSelectors']) {

		        	style += $scope.generateAPISelectorsCSS(componentOptions.name, componentOptions.selector, stateOptions);
				}

				// Add API option values CSS snippets
				if (stateOptions &&
					$scope.componentsTemplates !== undefined && 
		        	$scope.componentsTemplates[componentOptions.name]) {

					var valuesCSS = $scope.componentsTemplates[componentOptions.name]["valuesCSS"];

					for(var option in valuesCSS) { 
						if (valuesCSS.hasOwnProperty(option)) {
							if ( valuesCSS[option][stateOptions[option]] ) {
								var valueCSS = valuesCSS[option][stateOptions[option]];
								style += $scope.prefixCSSSelectors(valueCSS,componentOptions.selector);
							}
						}
					}
				}

			} // end if()
		} // end for() states loop

		style += $scope.getMappedCSS(componentOptions.selector, componentOptions['original']);
		
		if (componentOptions['media']) {

			// make a copy to not modify options
			var componentMedia = angular.copy(componentOptions['media']),
				sortedMedia = $scope.sortedMediaList();

			for (var index in sortedMedia) {

				var mediaName = sortedMedia[index];

				if (componentMedia[mediaName]) {
					// add name and selector
					componentMedia[mediaName].name 		= componentOptions.name;
					componentMedia[mediaName].selector 	= componentOptions.selector;
					style += "@media (max-width: "+$scope.mediaList[mediaName]["maxSize"]+") {";
					style += $scope.getSingleComponentCSS(componentMedia[mediaName], componentId, true, mediaName, forRepeater);
					style += "}";
				}
			}
		}

		$scope.functionEnd("getSingleComponentCSS()");
		return style;
    }
	

	/**
     * Prefix selectors with element #id, commas and media queries supported
     *
     * @since 3.0
     * @author Ilya K.
     */

    $scope.prefixCSSSelectors = function(css, selector, isClass) {

    	if (undefined===css||undefined===selector) {
    		return css;
    	}

    	// prefix all present selectors with element #ID 
		var parts = css.split('}');
		for (var key in parts) {
			if (parts.hasOwnProperty(key)) {
				var part = parts[key];

				// check for @media
				var partDetails = part.split('{');
				if (part.split('{').length==3) {
				    var mediaQuery = partDetails[0]+"{";
				    partDetails[0] = partDetails[1];
				    var mediaQueryStarted = true;
				}

				// prefix comma separated selectors
				var subParts = partDetails[0].split(',');
				for (var subPartKey in subParts) {
					subParts[subPartKey] = (isClass?'.':'#') + selector + ' ' + subParts[subPartKey];
				}

				if (part.split('{').length==3) {
					// media query start
					parts[key] = mediaQuery+"\n"+subParts.join(', ')+"{"+partDetails[2];
				}
				else if ( (!part[0] || part[0].length===0) && mediaQueryStarted){
					// media query end
					mediaQueryStarted = false;
					parts[key] = subParts.join(', ')+"{"+partDetails[2]+"}\n"; //finish media query
				}
				else if (partDetails[1]){
					// regular selector
					parts[key] = subParts.join(', ')+"{"+partDetails[1];
				}
			}
		}

		css = parts.join("}\n");

		return css;
	}
	
	/**
     * Generate CSS output for Grid not including selector
     *
     * @since 3.6
     * @author Ilya K.
     */

    $scope.getGridCSS = function(stateOptions, componentName, forceDisplay) {

		var styles = "";

		if (componentName==undefined) {
			componentName = "all";
		}

		if (
			( stateOptions['display'] && stateOptions['display'] == 'grid' ) ||
			( stateOptions['flex-direction'] && stateOptions['flex-direction'] == 'grid' ) 
		)
		{

			if (forceDisplay) {
				styles += "display: grid !important;";
			}
			
			// columns
			var columns_count = stateOptions['grid-column-count'] ? stateOptions['grid-column-count'] : $scope.defaultOptions[componentName]['grid-column-count'] || $scope.defaultOptions["all"]['grid-column-count'],
				min_width = 		stateOptions['grid-column-min-width'] ? stateOptions['grid-column-min-width'] : $scope.defaultOptions[componentName]['grid-column-min-width'],
				min_width_unit =   !stateOptions['grid-column-min-width'] ? $scope.defaultOptions[componentName]['grid-column-min-width-unit'] : "",
				max_width = 		stateOptions['grid-column-max-width'] ? stateOptions['grid-column-max-width'] : $scope.defaultOptions[componentName]['grid-column-max-width'];
				max_width_unit =   !stateOptions['grid-column-max-width'] ? $scope.defaultOptions[componentName]['grid-column-max-width-unit'] : "";

			if (stateOptions['grid-columns-auto-fit'] == 'true') {
				columns_count = 'auto-fit';
			}

			if (stateOptions['grid-justify-items']) {
				styles += "justify-items: " + stateOptions['grid-justify-items'] + ";";
			}
			else {
				styles += "justify-items: stretch;";
			}

			if (stateOptions['grid-align-items']) {
				styles += "align-items: " + stateOptions['grid-align-items'] + ";";
			}
			
			if (columns_count !== undefined && min_width !== undefined && max_width!== undefined ) {
				styles += "grid-template-columns: repeat("+columns_count+", minmax("+min_width+min_width_unit+", "+max_width+max_width_unit+"));";
			}
			var gap = stateOptions['grid-column-gap'] ? stateOptions['grid-column-gap'] : $scope.defaultOptions[componentName]['grid-column-gap'];
				gap_unit = !stateOptions['grid-column-gap'] ? $scope.defaultOptions[componentName]['grid-column-gap-unit'] : "";
			
			styles += "grid-column-gap: "+gap+gap_unit+";";
			
			// rows
			if (stateOptions['grid-row-behavior'] && stateOptions['grid-row-behavior']=='Explicit') {
				
				var row_count = 		stateOptions['grid-row-count'] ? stateOptions['grid-row-count'] : $scope.defaultOptions[componentName]['grid-row-count'],
					min_height = 		stateOptions['grid-row-min-height'] ? stateOptions['grid-row-min-height'] : $scope.defaultOptions[componentName]['grid-row-min-height'],
					min_height_unit =  !stateOptions['grid-row-min-height'] ? $scope.defaultOptions[componentName]['grid-row-min-height-unit'] : "",
					max_height = 		stateOptions['grid-row-max-height'] ? stateOptions['grid-row-max-height'] : $scope.defaultOptions[componentName]['grid-row-max-height'];
					max_height_unit =  !stateOptions['grid-row-max-height'] ? $scope.defaultOptions[componentName]['grid-row-max-height-unit'] : "";
				
				if (row_count != undefined && min_height != undefined && max_height != undefined ) {
					styles += "grid-template-rows: repeat("+row_count+", minmax("+min_height+min_height_unit+", "+max_height+max_height_unit+"));";
				}
			}

			gap = stateOptions['grid-row-gap'] ? stateOptions['grid-row-gap'] : $scope.defaultOptions[componentName]['grid-row-gap'],
			gap_unit = !stateOptions['grid-row-gap'] ? $scope.defaultOptions[componentName]['grid-row-gap-unit'] : "";
				
			styles += "grid-row-gap: "+gap+gap_unit+";";

			if (stateOptions['grid-match-height-of-tallest-child']=='true') {
				styles += "grid-auto-rows: minmax(min-content,1fr);";
			}
		}

		return styles;
	}


	/**
     * Generate CSS output for Grid Child including selectors
     *
     * @since 3.6
     * @author Ilya K.
     */

    $scope.getGridChildCSS = function(atts, selector, state, child_selector, is_class, is_repeater, is_preview) {

		var styles = "";

		if (undefined===child_selector) {
			child_selector = "";
		}

		if ( 
			( 
				( atts['flex-direction'] && atts['flex-direction'] == 'grid' ) ||
				( atts['display'] && atts['display'] == 'grid' )
			)
			&& 
			atts['grid-all-children-rule'] 
		) {
			
			styles += $scope.getGridSingleChildCSS(atts['grid-all-children-rule'], "*", selector, state, child_selector, is_class);
		}

		if ( 
			( 
				( atts['flex-direction'] && atts['flex-direction'] == 'grid' ) ||
				( atts['display'] && atts['display'] == 'grid' )
			)
			&& 
			atts['grid-child-rules'] 
		) {

			for (var key in atts['grid-child-rules'] ) {

				var pre_styles = "",
					rule = atts['grid-child-rules'][key];
			
				if ( rule && rule['child-index'] !== undefined ) {

					var nthChild = parseInt(rule['child-index']);

                	// repeater has one hidden div that we should offset for nth-child selector
					if (is_repeater && nthChild > 1) {
						nthChild++;
					}
				
					// Gallery has 3 scripts and styles that we should offset for nth-child selector
					else if (!is_preview && atts['grid'] == 'true' && nthChild) {
						nthChild+=3;
					}

					pre_styles += rule['column-span'] ? "grid-column: span "+rule['column-span']+";" : "";
					pre_styles += rule['row-span'] ? "grid-row: span "+rule['row-span']+";" : "";

					if (rule['row-span'] && rule['row-span'] != 1){
						pre_styles +="height: 100%;";
					}

					if (pre_styles=="") {
						continue;
					}

					var child_rule = ( nthChild == 0 ) ? ":last-child" : ":nth-child("+nthChild+")";

					if (!is_class) {
						styles += ( state && state != 'original') ? 
							"#"+selector+" "+child_selector+":"+state+ "> "+child_rule+"{\r\n" : 
							"#"+selector+" "+child_selector+ "> "+child_rule+"{\r\n";
						styles += pre_styles;
						styles += "}\r\n";
					}
					else {
						if ( state && state != 'original' ) {
							styles += "."+selector+":not(.ct-section):not(.oxy-easy-posts):"+state+",\r\n";
							if ( $scope.isPseudoElement(state) ) {
								styles += "."+selector+".ct-section .ct-section-inner-wrap:"+state+" > "+child_rule+",\r\n";
								styles += "."+selector+".oxy-easy-posts .oxy-posts:"+state+" > "+child_rule+"{\r\n";
							}
							else {
								styles += "."+selector+".ct-section:"+state+" .ct-section-inner-wrap > "+child_rule+",\r\n";
								styles += "."+selector+".oxy-easy-posts:"+state+" .oxy-posts > "+child_rule+"{\r\n";
							}
						}
						else {
							styles += "."+selector+":not(.ct-section):not(.oxy-easy-posts) > "+child_rule+",\r\n";
							styles += "."+selector+".ct-section .ct-section-inner-wrap > "+child_rule+",\r\n";
							styles += "."+selector+".oxy-easy-posts .oxy-posts > "+child_rule+"{\r\n";
						}
						styles += pre_styles;
						styles += "}\r\n";
					}
				}
			}
		}

		return styles;
	}


	$scope.getGridSingleChildCSS = function(atts, child_rule, selector, state, child_selector, is_class) {

		var styles = "";

		pre_styles = atts['column-span'] ? "grid-column: span "+atts['column-span']+";" : "";
		pre_styles += atts['row-span'] ? "grid-row: span "+atts['row-span']+";" : "";

		if (atts['row-span'] && atts['row-span'] != 1){
			pre_styles +="height: 100%;";
		}

		if (pre_styles!="") {

			if (!is_class) {
				styles += ( state && state != 'original') ? 
					"#"+selector+" "+child_selector+":"+state+ "> "+child_rule+"{\r\n" : 
					"#"+selector+" "+child_selector+ "> "+child_rule+"{\r\n";
				styles += pre_styles;
				styles += "}\r\n";
			}
			else {
				if ( state && state != 'original' ) {
					styles += "."+selector+":not(.ct-section):not(.oxy-easy-posts):"+state+",\r\n";
					if ( $scope.isPseudoElement(state) ) {
						styles += "."+selector+".ct-section .ct-section-inner-wrap:"+state+" > "+child_rule+",\r\n";
						styles += "."+selector+".oxy-easy-posts .oxy-posts:"+state+" > "+child_rule+"{\r\n";
					}
					else {
						styles += "."+selector+".ct-section:"+state+" .ct-section-inner-wrap > "+child_rule+",\r\n";
						styles += "."+selector+".oxy-easy-posts:"+state+" .oxy-posts > "+child_rule+"{\r\n";
					}
				}
				else {
					styles += "."+selector+":not(.ct-section):not(.oxy-easy-posts) > "+child_rule+",\r\n";
					styles += "."+selector+".ct-section .ct-section-inner-wrap > "+child_rule+",\r\n";
					styles += "."+selector+".oxy-easy-posts .oxy-posts > "+child_rule+"{\r\n";
				}
				styles += pre_styles;
				styles += "}\r\n";
			}
		}

		return styles;
	}


    /**
     * Generate CSS output for transforms
     *
     * @since 2.2
     * @author Ilya K.
     */

    $scope.getTransformCSS = function(stateOptions) {

    	if (!stateOptions['transform']) {
    		return;
    	}

    	var transformOptions = $scope.objectToArrayObject(stateOptions['transform']);

    	var css = "";

    	for(var key in transformOptions) { 
			if (transformOptions.hasOwnProperty(key)) {
				var transform = transformOptions[key];

				// Skew
				if (transform['transform-type']=='skew') {
					if (transform['skewX'] && transform['skewY']) {
						css += transform['transform-type'] + "(" + transform['skewX'] + 'deg,' + transform['skewY'] + "deg)";
					}
					else if (transform['skewX']) {
						css += transform['transform-type'] + "(" + transform['skewX'] + "deg)";
					}
				}

				// Translate
				if (transform['transform-type']=='translate') {
					if (transform['translateX'] && transform['translateY'] && transform['translateZ']) {
						css += "translate3d(" + transform['translateX'] + (transform['translateX-unit']||$scope.defaultOptions["all"]['translateX-unit']) + "," 
											  + transform['translateY'] + (transform['translateY-unit']||$scope.defaultOptions["all"]['translateY-unit']) + ","
											  + transform['translateZ'] + (transform['translateZ-unit']||$scope.defaultOptions["all"]['translateZ-unit']) + ")";
					}
					else if (transform['translateX'] && transform['translateY']) {
						css += transform['transform-type'] + "(" + transform['translateX'] + (transform['translateX-unit']||$scope.defaultOptions["all"]['translateX-unit']) + ',' 
							 									 + transform['translateY'] + (transform['translateY-unit']||$scope.defaultOptions["all"]['translateY-unit']) + ")";
					}
					else if (transform['translateX']) {
						css += transform['transform-type'] + "(" + transform['translateX'] + (transform['translateX-unit']||$scope.defaultOptions["all"]['translateX-unit']) + ")";
					}
					else if (transform['translateY']) {
						css += "translateY" + "(" + transform['translateY'] + (transform['translateY-unit']||$scope.defaultOptions["all"]['translateY-unit']) + ")";
					}
				}

				// Rotate
				if (transform['transform-type']=='rotate' && transform['rotateAngle']) {
					css += transform['transform-type'] + "(" + transform['rotateAngle'] + "deg)";
				}

				// Rotate X
				if (transform['transform-type']=='rotateX' && transform['rotateXAngle']) {
					css += transform['transform-type'] + "(" + transform['rotateXAngle'] + "deg)";
				}

				// Rotate Y
				if (transform['transform-type']=='rotateY' && transform['rotateYAngle']) {
					css += transform['transform-type'] + "(" + transform['rotateYAngle'] + "deg)";
				}

				// Perspective
				if (transform['transform-type']=='perspective' && transform['perspective']) {
					css += transform['transform-type'] + "(" + transform['perspective'] + (transform['perspective-unit']||$scope.defaultOptions["all"]['perspective-unit']) + ")";
				}

				// Rotate 3D
				if (transform['transform-type']=='rotate3d') {
					if (transform['rotate3dX'] && transform['rotate3dY'] && transform['rotate3dZ'] && transform['rotate3dAngle']) {
						css += transform['transform-type'] + "(" 
									+ transform['rotate3dX'] + "," 
									+ transform['rotate3dY'] + ","
									+ transform['rotate3dZ'] + ","
									+ transform['rotate3dAngle'] + "deg)";
					}
				}

				// Scale
				if (transform['transform-type']=='scale') {
					if (transform['scaleX'] && transform['scaleY'] && transform['scaleZ']) {
						css += "scale3d(" 
									+ transform['scaleX'] + "," 
									+ transform['scaleY'] + ","
									+ transform['scaleZ'] + ")";
					}
					else if (transform['scaleX'] && transform['scaleY']) {
						css += transform['transform-type'] + "(" 
									+ transform['scaleX'] + "," 
									+ transform['scaleY'] + ")";
					}
					else {

						if (transform['scaleX']) {
							css += " scaleX(" + transform['scaleX'] + ")";
						}

						if (transform['scaleY']) {
							css += " scaleY(" + transform['scaleY'] + ")";
						}

						if (transform['scaleZ']) {
							css += " scaleZ(" + transform['scaleZ'] + ")";
						}
					}
				}

				if (key < transformOptions.length-1 ) {
					css += " ";
				}
				else {
					css += ";";
				}
			}
		}

		if ( css !== "" ) {
			css = "transform:"+css;
		}

		return css;
    }


    /**
     * Generate CSS output for background gradient settings
     *
     * @since 2.1
     * @author Gagan
     */

    $scope.getBackgroundLayersCSS = function(stateOptions, componentName, isCustomSelectors, name, whichMedia, state) {
		
		var bgColor = $scope.getGlobalColorValue(stateOptions['background-color']);

		var styles = [];
		var backgroundSize = [], gradientColors = [];

		if(stateOptions['gradient'] && stateOptions['gradient']['colors']) {
			gradientColors = stateOptions['gradient']['colors']
		}

		var styleBuffer = '';
			
		// make sure that the colors are an array and not an object
		gradientColors = _.map(gradientColors, function(color) { return color;});
		gradientColors = _.filter(gradientColors, function(color) { return color.value });
		gradientColors = _.map(gradientColors, function(color) { color.value = $scope.getGlobalColorValue(color.value); return color;});

		if(gradientColors.length > 0) {

			if(stateOptions['gradient']['gradient-type'] === 'radial') {

				styleBuffer += ' radial-gradient(';

				var radialParams = '';

				if(stateOptions['gradient']['radial-shape']) {
					radialParams += ' '+stateOptions['gradient']['radial-shape'];
				}

				if(stateOptions['gradient']['radial-size']) {
					radialParams += ' '+stateOptions['gradient']['radial-size'];
				}

				if(stateOptions['gradient']['radial-position-left']) {
					radialParams += ' at '+stateOptions['gradient']['radial-position-left']+(stateOptions['gradient']['radial-position-left-unit'] || 'px');

					if(stateOptions['gradient']['radial-position-top']) {
						radialParams += ' '+stateOptions['gradient']['radial-position-top']+(stateOptions['gradient']['radial-position-top-unit'] || 'px');
					}
				}

				if(radialParams.length > 0) {
					styleBuffer += radialParams+', ';
				}
			}
			else {
				styleBuffer += ' linear-gradient(';

				if(stateOptions['gradient']['linear-angle']) {
					styleBuffer += stateOptions['gradient']['linear-angle']+'deg, ';
				}
			}

			if(gradientColors) {
				var filteredColors = _.filter(gradientColors, function(color) {
					return color.value && color.value.length > 0;
				})

				var colorStrings = _.map(filteredColors, function(color) {
					return color.value + 
						(color.position ? ' ' + color.position + color['position-unit']: '');
				});

				// if it is a single color, repeat it once to show a solid layer
				if(colorStrings.length === 1) {
					colorStrings.push(colorStrings[0]);
				}

				styleBuffer += colorStrings.join(', ');
			}

			styleBuffer += ')';

			if(styleBuffer.length > 0) {
				styles.push(styleBuffer);
				backgroundSize.push('auto');
			}
		}
		
		if(stateOptions['overlay-color']) {
			styles.push('linear-gradient(' +$scope.getGlobalColorValue(stateOptions['overlay-color'])+ ', '+$scope.getGlobalColorValue(stateOptions['overlay-color'])+')');
			backgroundSize.push('auto');
		}

		if(stateOptions['background-size'] && stateOptions['background-size'].trim().length > 0) {

			styleBuffer = '';

			if(stateOptions['background-size'] === 'manual') {
				if(stateOptions['background-size-width'] && stateOptions['background-size-width'].trim().length > 0) {
					styleBuffer += ' '+stateOptions['background-size-width'].trim()+stateOptions['background-size-width-unit'].trim();
				}
				else {
					styleBuffer += ' 0%';
				}
				
				if(stateOptions['background-size-height'] && stateOptions['background-size-height'].trim().length > 0) {
					styleBuffer += ' '+stateOptions['background-size-height'].trim()+stateOptions['background-size-height-unit'].trim();
				}
				else {
					styleBuffer += ' 0%';
				}
			}
			else {
				styleBuffer += ' ' + stateOptions['background-size'];
			}

			if(styleBuffer.length > 0) {
				backgroundSize.push(styleBuffer);
			}

		} else {
			backgroundSize = []; // if no size is specified, let all fall back to default, dont worry about size for gradient and overlay, those were just fillers
		}
		
		if(stateOptions['background']) {
			styles.push('url('+stateOptions['background']+')');
		}

		if(stateOptions['background-image']) {
			var value = stateOptions['background-image'];
			// if this has an oxy shortcode, do something about it
			if(value.indexOf('[') > -1 && $scope.dynamicBackgrounds) {
				value = $scope.getDynamicBackground(isCustomSelectors?(isCustomSelectors==='id'?'id':'selector'):'class', {id: name, media: whichMedia, stateName: state})
			}
			
			styles.push('url('+value+')');
		}	

		var background = styles.join(', ').trim();
		var style = "";

		if(background !== '') {
			style = 'background-image:' + background + ';';
		}

		if(backgroundSize.length > 0) {
			style += 'background-size:' + backgroundSize.join(', ') + ';';
		}
			
		return style;
	}
	

	/**
     * Get all typography CSS from options using paramName as prefix
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.generateTypographyCSS = function(options,paramName) {
		
		var styles = "";

		for (var key in options) {
			if (key.indexOf(paramName)>-1) {
				var name = key.replace(paramName+"_",''),
					value = options[key];

				if (name=='color') {
					value = $scope.getGlobalColorValue(value);
				}
				
				styles += name+":"+$scope.addFontFamilyQuotes(name, value)+";";
			}
		}

		return styles;
    }

    
    /**
     * Add '' quotes around font family if needed
     *
     * @since 3.0
     * @author Ilya K.
     */

    $scope.addFontFamilyQuotes = function(name, value) {

		if (name == "font-family"||name.indexOf("font-family")>-1) {
			$scope.loadWebFont(value);
			if ( value.indexOf(',') === -1 && value.toLowerCase() !== "inherit") {
				value = "'"+value+"'";
			}
		}

		return value;
    }

	
	/**
     * Get all box-shadow params and combine into one 
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.generateBoxShadowCSS = function(stateOptions, paramName) {

    	if (undefined!=paramName) {
    		paramName += "_";
    	}
    	else {
    		paramName = "";
    	}
		
		if ( stateOptions[paramName+'box-shadow-color'] ) {

			var inset 	= (stateOptions[paramName+'box-shadow-inset']=='inset') 	? stateOptions[paramName+'box-shadow-inset']+" " : "";
			var hor 	= (stateOptions[paramName+'box-shadow-horizontal-offset']) 	? stateOptions[paramName+'box-shadow-horizontal-offset']+" " : "";
			var ver 	= (stateOptions[paramName+'box-shadow-vertical-offset']) 	? stateOptions[paramName+'box-shadow-vertical-offset']+" " : "";
			var blur 	= (stateOptions[paramName+'box-shadow-blur']) 				? stateOptions[paramName+'box-shadow-blur']+" " : "0px ";
			var spread  = (stateOptions[paramName+'box-shadow-spread']) 			? stateOptions[paramName+'box-shadow-spread']+" " : "";
						
			return "box-shadow:"+inset+hor+ver+blur+spread+$scope.getGlobalColorValue(stateOptions[paramName+'box-shadow-color'])+";";
		}

		return "";
	}

    /**
     * Get all CSS from options using paramName as prefix
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.generateArrayOptionsCSS = function(options,paramName) {
		
		var styles = "";

		for (var key in options) {
			if (key.indexOf(paramName)>-1) {
				styles += key.replace(paramName+"_",'')+":"+options[key]+";";
			}
		}

		return styles;
    }


    /**
     * Get single class CSS string
     *
     * @since 0.2.5
     * @author Ilya K.
     * @return {sting} CSS code
     */

    $scope.getSingleClassCSS = function(className, classStates) {

    	if ($scope.log) {
    		console.log("getSingleClassCSS()", className);
    		$scope.functionStart("getSingleClassCSS()");
    	}

    	if (undefined === classStates) {
    		classStates = $scope.classes[className];
    	}

    	$scope.contentAdded = [];

    	// add default styles
    	var style = $scope.getSelectorStyles(className, classStates);

    	// add media styles
		if ( $scope.classes[className]['media'] ) {

			var sortedMedia = $scope.sortedMediaList(false,true);

			for (var index in sortedMedia) {

				var mediaName = sortedMedia[index];

				if ($scope.classes[className]['media'][mediaName]) {

					if (mediaName=='page-width') {
						// classes should use Global Page Width
						var maxSize = $scope.getPageWidth('global')+'px';
					}
					else {
						var maxSize = $scope.mediaList[mediaName]["maxSize"];
					}
					
					classStates = $scope.classes[className]['media'][mediaName];
					style += "@media (max-width: "+maxSize+") {";
    				style += $scope.getSelectorStyles(className, classStates, false, mediaName);
    				style += "}";
				}
			}
    	}
		
		$scope.functionEnd("getSingleClassCSS()");
		return style;
    }
    

    /**
     * Get single custom selector CSS string
     *
     * @since 1.3
     * @author Ilya K.
     * @return {sting} CSS code
     */

    $scope.getSingleSelectorCSS = function(selectorName, selectorsStates) {

    	if ($scope.log) {
    		console.log("getSingleSelectorCSS()", selectorName);
    		$scope.functionStart("getSingleSelectorCSS()");
    	}

    	if (undefined === selectorsStates) {
    		selectorsStates = $scope.customSelectors[selectorName];
    	}

    	$scope.contentAdded = [];

    	// add default styles
    	var style = $scope.getSelectorStyles(selectorName, selectorsStates, true);

    	// add media styles
		if ( $scope.customSelectors[selectorName]['media'] ) {

			var sortedMedia = $scope.sortedMediaList();

			for (var index in sortedMedia) {

				var mediaName = sortedMedia[index];

				if ($scope.customSelectors[selectorName]['media'][mediaName]) {
					
					selectorsStates = $scope.customSelectors[selectorName]['media'][mediaName];
					style += "@media (max-width: "+$scope.mediaList[mediaName]["maxSize"]+") {";
    				style += $scope.getSelectorStyles(selectorName, selectorsStates, true, mediaName);
    				style += "}";
				}

			}
    	}
		
		$scope.functionEnd("getSingleSelectorCSS()");
		return style;
    }


    /**
     * Get styles for one single class or custom selector
     *
     * @since 1.3
     * @author Ilya K.
     */

    $scope.getSelectorStyles = function(name, states, isCustomSelectors, whichMedia) {

    	if (name==="" || (!isCustomSelectors && typeof($scope.classes[name]) === 'undefined') || (isCustomSelectors && typeof($scope.customSelectors[name]) === 'undefined')) {
    		//skip empty selectors
    		return "";
    	}
    		
		var style = "",
			currentState;

		// loop all class states
		for(var state in states) { 
			if (states.hasOwnProperty(state)) {
				var styles = states[state];

				// skip "media" and sets
				if ( state == "media" || state == "set_name" || state == "key" || state == "parent" || state == "status" || state == "friendly_name" ) {
					continue;
				}

				if ( state == "original" || 
						( name == $scope.selectorToEdit 	&& state == $scope.currentState ) ||
						( name == $scope.currentClass 		&& state == $scope.currentState )
					) 
				{	
					if ( state == "original" || state == "hover" || state == "active" || state == "focus" ) {
						currentState = "";
					}
					else {
						currentState = ":" + state;
					}
				}
				else {
					currentState = ":" + state;
				}

				var breakpointOptions = {
					'state': state,
				}

				if (isCustomSelectors) {
					breakpointOptions.selector = name
				}
				else {
					breakpointOptions.class = name
				}

				// filter styles
				var options = $scope.getCSSOptions(null, null, styles, null, whichMedia, breakpointOptions);
				var contentAdded = false;

				style += $scope.generateAllAPISelectorsCSS(name, options, 'class');

				/**
				 * Special logic to handle Fnacy Icon styles for classes
				 */

				if ((options['icon-style'] !== undefined || options['icon-color'] !== undefined || options['icon-background-color'] !== undefined || options['icon-padding'] !== undefined || options['icon-size'] !== undefined ) && !isCustomSelectors) {

					if (!whichMedia && state == "original") {
						var iconStyle 			= options['icon-style'] || $scope.defaultOptions['ct_fancy_icon']['icon-style'],
							iconColor 			= $scope.getGlobalColorValue(options['icon-color']) || $scope.defaultOptions['ct_fancy_icon']['icon-color'],
							iconBackgroundColor = $scope.getGlobalColorValue(options['icon-background-color']) || $scope.defaultOptions['ct_fancy_icon']['icon-background-color'],
							iconPadding 		= options['icon-padding'] || $scope.defaultOptions['ct_fancy_icon']['icon-padding']+$scope.defaultOptions['ct_fancy_icon']['icon-padding-unit'],
							iconSize 			= options['icon-size'] || $scope.defaultOptions['ct_fancy_icon']['icon-size']+$scope.defaultOptions['ct_fancy_icon']['icon-size-unit'];
					}
					else {
						var iconStyle 			= $scope.classes[name]['original']['icon-style'] || $scope.defaultOptions['ct_fancy_icon']['icon-style'],
							iconColor 			= $scope.getGlobalColorValue(options['icon-color']),
							iconBackgroundColor = $scope.getGlobalColorValue(options['icon-background-color']),
							iconPadding 		= options['icon-padding'],
							iconSize 			= options['icon-size'];
					}

					style += '.' + name + currentState + "{";

					if (iconStyle == 1) { // outline
						style += "border: 1px solid;";
					}

					if (iconStyle == 2) { // solid
						if (iconBackgroundColor) {
							style += "background-color: " + $scope.getGlobalColorValue(iconBackgroundColor) + ";";
							style += "border: 1px solid " + $scope.getGlobalColorValue(iconBackgroundColor) + ";";
						}
					}

					if (iconStyle == 1 || iconStyle == 2) { // outline or solid
					 	style += "padding:" + iconPadding + ";";
					} 

					if (iconColor) {
					 	style += "color:" + $scope.getGlobalColorValue(iconColor) + ";";
					}

					style += "}";

					if (iconSize) {
						style += '.' + name + currentState + ">svg {";
					 	style += "width: " + iconSize + ";";
					 	style += "height: " + iconSize + ";";
					 	style += "}";
					}
				}

				
				/**
				 * Special logic to handle Button styles for classes
				 */

				if ((options['button-style'] !== undefined || options['button-color'] !== undefined || options['button-size'] !== undefined || options['button-text-color'] !== undefined) && !isCustomSelectors) {
						
						if (!whichMedia && state == "original") {
							var buttonStyle 	= options['button-style'] || $scope.defaultOptions['ct_link_button']['button-style'],
								buttonColor 	= $scope.getGlobalColorValue(options['button-color']) || $scope.defaultOptions['ct_link_button']['button-color'],
								buttonSize 		= options['button-size'] || $scope.defaultOptions['ct_link_button']['button-size'] + $scope.defaultOptions['ct_link_button']['button-size-unit'],
								buttonTextColor = $scope.getGlobalColorValue(options['button-text-color']) || $scope.defaultOptions['ct_link_button']['button-text-color'];
						}
						else {
							var buttonStyle 	= $scope.classes[name]['original']['button-style'] || $scope.defaultOptions['ct_link_button']['button-style'],
								buttonColor 	= $scope.getGlobalColorValue(options['button-color']),
								buttonSize 		= options['button-size'],
								buttonTextColor = $scope.getGlobalColorValue(options['button-text-color']);
						}
						style += '.' + name  + currentState + "{";

						if (buttonStyle == 1 && buttonColor) { // solid
							style += "background-color: " + $scope.getGlobalColorValue(buttonColor) + ";";
							style += "border: 1px solid " + $scope.getGlobalColorValue(buttonColor) + ";";
							if (buttonTextColor) {
								style += "color: " + $scope.getGlobalColorValue(buttonTextColor) + ";";
							}
						}
						
						if (buttonStyle == 2) { // outline
							style += "background-color: transparent;";
							if (buttonColor) {
								style += "border: 1px solid " + $scope.getGlobalColorValue(buttonColor) + ";";
								style += "color: " + $scope.getGlobalColorValue(buttonColor) + ";";
							}
						}

						if (buttonSize) {
							var substracted = buttonStyle == 2 ? 1 : 0;
							style += "padding: " + (parseInt(buttonSize)-substracted) + 'px ' + (parseInt(buttonSize)*1.6-substracted) + 'px;';
						}

						style += "}";
				}


				/**
				 * Special logic to handle Testimonial styles for classes
				 */

				var isTestimonial = false;
				if ($scope.classes[name]) {
					for (paramName in $scope.classes[name]['original']) {
						if (paramName.indexOf("testimonial_")===0) {
							isTestimonial = true;
						}
					}
				}

				if ( !isCustomSelectors && isTestimonial ) {

						isTestimonial = false;

						var flexAlign,
				            textAlign,
				            mobileFlexAlign,
				            mobileTextAlign,
				            marginCSS = "",
				            spacing = options['testimonial_image_spacing'];

				        if (options["testimonial_layout"]=='vertical') {

				        	marginCSS = "margin:0;";
                			spacing = options['testimonial_image_spacing'] || $scope.defaultOptions['oxy_testimonial']['testimonial_image_spacing']+$scope.defaultOptions['oxy_testimonial']['testimonial_image_spacing-unit'];
							
							if (options["testimonial_image_position"]=='bottom') {
				            	marginCSS += "margin-top";
				            }
				            else {
				            	marginCSS += "margin-bottom";
				            }
				        	
				        } else {
				            
				            if (options["testimonial_image_position"]=='bottom') {

				            	marginCSS = "margin:0;";
                				spacing = options['testimonial_image_spacing'] || $scope.defaultOptions['oxy_testimonial']['testimonial_image_spacing']+$scope.defaultOptions['oxy_testimonial']['testimonial_image_spacing-unit'];

				            	marginCSS += "margin-left";
				            }
				            else {
				            	marginCSS += "margin-right";
				            }
				        }

				        marginCSS += ": " + spacing + ";";

						if (options["testimonial_content_alignment"]=='left') {
				            flexAlign = "flex-start";
				            textAlign = "left";
				        } else if (options["testimonial_content_alignment"]=='center') {
				            flexAlign = "center";
				            textAlign = "center";
				        } else if (options["testimonial_content_alignment"]=='right') {
				            flexAlign = "flex-end";
				            textAlign = "right";
				        }

				        if ($scope.classes[name]['original']["testimonial_mobile_content_alignment"]=='left') {
				            mobileFlexAlign = "flex-start";
				            mobileTextAlign = "left";
				        } else if ($scope.classes[name]['original']["testimonial_mobile_content_alignment"]=='center') {
				            mobileFlexAlign = "center";
				            mobileTextAlign = "center";
				        } else if ($scope.classes[name]['original']["testimonial_mobile_content_alignment"]=='right') {
				            mobileFlexAlign = "flex-end";
				            mobileTextAlign = "right";
				        }

				        if (options["testimonial_layout"]=='vertical') {
				        	style += '.' + name + currentState + ' {' +
				                'flex-direction: column;' +
				            '}';
				        }
				        else if (options["testimonial_layout"]=='horizontal') {
				            style += '.' + name + currentState + ' {' +
				                'flex-direction: row;' +
				                'align-items: center;' +
				           '}';
				        }
				        
				        if (options['testimonial_image_position'] == 'top' || 
				            options['testimonial_image_position'] == '' ) {
				            style += '.' + name + currentState + ' .oxy-testimonial-photo-wrap {' +
				                'order: 1;' +
				           '}';
				        } 
				        else if (options['testimonial_image_position'] == 'bottom') {
				            style += '.' + name + currentState + ' .oxy-testimonial-photo-wrap {' +
				                'order: 3;' +
				           '}';
				        } 

						style += '.' + name + currentState + " .oxy-testimonial-photo {"+
				            "width:"+ options['testimonial_image_size'] + ";" +
				            "height:"+ options['testimonial_image_size'] + ";" +
				            marginCSS +
				        "}";

						style += '.' + name + currentState + " .oxy-testimonial-photo-wrap,"+
								 '.' + name + currentState + " .oxy-testimonial-author-wrap,"+
								 '.' + name + currentState + " .oxy-testimonial-content-wrap {"+
				            "align-items:"+ flexAlign + ";" +
				            "text-align:"+ textAlign + ";" +
				        "}";

						style += '.' + name + currentState + " .oxy-testimonial-text {"+
				            "margin-bottom:"+ options['testimonial_text_space_below'] + ";" +
				            $scope.generateTypographyCSS(options,'testimonial_text_typography') +
				        "}";

				        style += '.' + name + currentState + " .oxy-testimonial-author {"+
				            "margin-bottom:" + options['testimonial_author_space_below'] + ";" +
				            $scope.generateTypographyCSS(options,'testimonial_author_typography') +
				        "}";

				        style += '.' + name + currentState + " .oxy-testimonial-author-info {"+
				            "margin-bottom:" + options['testimonial_author_info_space_below'] + ";" +
				            $scope.generateTypographyCSS(options,'testimonial_author_info_typography') +
				        "}";

				        var maxSize = $scope.getMediaMaxSize($scope.classes[name]['original']['testimonial_vertical_layout_below']);
				        if (maxSize) {
				        	var imageSpacing = options['testimonial_image_spacing'];
				        	if (!imageSpacing) {
				        		imageSpacing = $scope.defaultOptions['oxy_testimonial']['testimonial_image_spacing']+$scope.defaultOptions['oxy_testimonial']['testimonial_image_spacing-unit'];
				        	}
				        	var marginPosition;
				        	if (options["testimonial_image_position"]=='bottom') {
		                        marginPosition = "top";
		                    } 
		                    else {
		                        marginPosition = "bottom";
		                    }
					        style += '@media (max-width: '+ maxSize + '){' +
				                '.' + name + currentState + ' {' +
				                    'flex-direction: column !important;' +
				                '}' +  

				                '.' + name + currentState + ' .oxy-testimonial-photo {' +
				                    'margin: 0;'+
				                    'margin-'+marginPosition+':' + imageSpacing + ';' +
				                '}'+

				                '.' + name + currentState + ' .oxy-testimonial-photo-wrap, ' +
				                '.' + name + currentState + ' .oxy-testimonial-author-wrap, ' +
				                '.' + name + currentState + ' .oxy-testimonial-content-wrap {' +
				                    'align-items:' + mobileFlexAlign + ';' +
				                    'text-align:' + mobileTextAlign + ';' +
				                '}'+
				            '}';
				        } 
					}

				/**
				 * Special logic to handle Icon Box styles for classes
				 */

				var isIconBox = false;
				if ($scope.classes[name]) {
					for (paramName in $scope.classes[name]['original']) {
						if (paramName.indexOf("icon_box_")===0) {
							isIconBox = true;
						}
					}
				}

				if ( !isCustomSelectors && isIconBox ) {

					isIconBox = false;
						
						var icon_position_flex_direction,
							icon_vertical_alignment_align_self,
							iconmargincss,
							mobileflexalign,
							mobiletextalign;
						
						if (options['icon_box_icon_position'] == 'top') {
				            icon_position_flex_direction = 'column';
				        } else if (options['icon_box_icon_position'] == 'left') {
				            icon_position_flex_direction = 'row';
				        } else if (options['icon_box_icon_position'] == 'right') {
				            icon_position_flex_direction = 'row-reverse';
				        } else if (options['icon_box_icon_position'] == 'bottom') {
				            icon_position_flex_direction = 'column-reverse';
				        }

				        if (options["icon_box_icon_position"]=='left' || options["icon_box_icon_position"]=='right') {
				        	var left = options["icon_box_icon_space_before"] || $scope.defaultOptions['oxy_icon_box']["icon_box_icon_space_before"]+$scope.defaultOptions['oxy_icon_box']["icon_box_icon_space_before-unit"];
				        		right = options["icon_box_icon_space_after"] || $scope.defaultOptions['oxy_icon_box']["icon_box_icon_space_after"]+$scope.defaultOptions['oxy_icon_box']["icon_box_icon_space_after-unit"];
				            iconmargincss = "margin-left: "+left+";\n";
				            iconmargincss += "margin-right: "+right+";\n";
				            iconmargincss += "margin-bottom: 0; margin-top: 0;";
				        } else {
				            iconmargincss = "margin-top: "+options["icon_box_icon_space_before"]+";\n";
				            iconmargincss += "margin-bottom: "+options["icon_box_icon_space_after"]+";\n";
				        }

				        if (options["icon_box_icon_position"]=='left' || options["icon_box_icon_position"]=='right') {
				            icon_vertical_alignment_align_self = options['icon_box_icon_vertical_alignment'];
				        } else {
				            if (options["icon_box_content_alignment"]=='left') {
				                icon_vertical_alignment_align_self = "flex-start";
				            } else if (options["icon_box_content_alignment"]=='center') {
				                icon_vertical_alignment_align_self = "center";
				            } else if (options["icon_box_content_alignment"]=='right') {
				                icon_vertical_alignment_align_self = "flex-end";
				            }          
				        }

				        if (options["icon_box_mobile_content_alignment"]=='left') {
				            mobileflexalign = "flex-start";
				            mobiletextalign = "left";
				        } else if (options["icon_box_mobile_content_alignment"]=='center') {
				            mobileflexalign = "center";
				            mobiletextalign = "center";
				        } else if (options["icon_box_mobile_content_alignment"]=='right') {
				            mobileflexalign = "flex-end";
				            mobiletextalign = "right";
				        } else {
				            mobileflexalign = "flex-start";
				            mobiletextalign = "left";            
				        }

			            style += '.' + name + currentState + ' {'+
			                'text-align: '+ options['icon_box_content_alignment'] + ";" +
			                'flex-direction: '+ icon_position_flex_direction + ';'+
			            '}';

			            style += '.' + name + currentState + ' .oxy-icon-box-icon {'+
			                iconmargincss +
			                'align-self: ' + icon_vertical_alignment_align_self + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-icon-box-heading {'+
			                $scope.generateTypographyCSS(options,'icon_box_heading_typography')+
			                'margin-top: ' + options['icon_box_heading_space_above'] + ';' +
			                'margin-bottom: ' + options['icon_box_heading_space_below'] + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-icon-box-text {'+
			                $scope.generateTypographyCSS(options,'icon_box_text_typography')+
			                'margin-top: ' + options['icon_box_text_space_above'] + ';' +
			                'margin-bottom: ' + options['icon_box_text_space_below'] + ';' +
			                'align-self: ' + icon_vertical_alignment_align_self + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-icon-box-link {'+
			                'margin-top: ' + options['icon_box_link_space_above'] + ';' +
			                'margin-bottom: ' + options['icon_box_link_space_below'] + ';' +
			            '}';
						
						var maxSize = $scope.getMediaMaxSize($scope.classes[name]['original']['icon_box_vertical_layout_below']);
				        if (maxSize && !whichMedia) {
				            style += '@media (max-width: ' + maxSize + '){' +
				                '.' + name + currentState + '.oxy-icon-box {' +
				                    'flex-direction: column !important;' +
				                    'text-align: ' + mobiletextalign + ';' +
				                '}' +

				                '.' + name + currentState + ' .oxy-icon-box-icon {' +
				                    'margin-left: 0;' +
				                    'margin-right: 0;' +
				                    'margin-top: ' + options["icon_box_icon_space_before"] + ';' +
				                    'margin-bottom: ' + options["icon_box_icon_space_after"] + ';' +
				                '}' +

				                '.' + name + currentState + ' .oxy-icon-box-icon, .oxy-icon-box-text {' +
				                    'align-self: ' + mobileflexalign + ';' +
				                '}' +
				            '}';
				        }
			    }

				/**
				 * Special logic to handle Progress Bar styles for classes
				 */

				var isProgressBar = false;
				if ($scope.classes[name]) {
					for (paramName in $scope.classes[name]['original']) {
						if (paramName.indexOf("progress_bar_")===0) {
							isProgressBar = true;
						}
					}
				}

				if ( !isCustomSelectors && isProgressBar ) {

						isProgressBar = false;

			    		var stripecss = "",
							animation_css = [];
						
						if (options['progress_bar_stripes'] == 'true') {
				            stripecss = "background-image: linear-gradient(-45deg,rgba(255,255,255,.12) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.12) 50%,rgba(255,255,255,.12) 75%,transparent 75%,transparent);";
				        }
				        if (options['progress_bar_stripes'] == 'false') {
				            stripecss = 'background-image:none;';
				        }

				        if (options['progress_bar_animation_stripes'] == 'true') {
				        	var stripesDuration = options['progress_bar_animation_stripes_duration'] || 
				        						$scope.defaultOptions['oxy_progress_bar']['progress_bar_animation_stripes_duration'];
				            animation_css['stripes'] = "oxy_progress_bar_stripes "+stripesDuration+" linear infinite";
				        }
				        if (options['progress_bar_animation_stripes'] == 'false') {
				        	animation_css['stripes'] = "none 0s paused";
				        }

				        if (options['progress_bar_animate_width'] == 'true') {
				        	var widthDuration = options['progress_bar_animation_width_duration'] || 
				        				   		$scope.defaultOptions['oxy_progress_bar']['progress_bar_animation_width_duration'];
				            animation_css['width'] = "oxy_progress_bar_width "+widthDuration+" ease-out 1";
				        }
				        if (options['progress_bar_animate_width'] == 'false') {
				        	animation_css['width'] = "none 0s paused";
				        }

				        var bar_animation = [];
				        for(var key in animation_css) {
							bar_animation.push(animation_css[key]);
				        }

			            style += '.' + name + currentState + ' .oxy-progress-bar-background {' +
			                'background-color:' + $scope.getGlobalColorValue(options['progress_bar_background_color']) + ';' +
			                 stripecss +
			                'animation:' + animation_css['stripes'] + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-progress-bar-progress-wrap {' +
			                'width:' + options['progress_bar_progress'] + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-progress-bar-progress {' +
			                'background-color:' + $scope.getGlobalColorValue(options['progress_bar_bar_color']) + ';' +
			                'padding:' + options['progress_bar_bar_padding'] + ';' +
			                 stripecss +
			                'animation:' + bar_animation.join() + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-progress-bar-overlay-text {' +
			                $scope.generateTypographyCSS(options, 'progress_bar_left_text_typography') +
			            '}';

			            style += '.' + name + currentState + ' .oxy-progress-bar-overlay-percent {' +
			                $scope.generateTypographyCSS(options, 'progress_bar_right_text_typography') +
			            '}';
			    }

				/**
				 * Special logic to handle Progress Bar styles for classes
				 */

				var isPricingBox = false;
				if ($scope.classes[name]) {
					for (paramName in $scope.classes[name]['original']) {
						if (paramName.indexOf("pricing_box_")===0) {
							isPricingBox = true;
						}
					}
				}
			    if ( !isCustomSelectors && isPricingBox ) {

			    		isPricingBox = false;

						var price_flex_alignment, price_flex_direction, price_justify_content, global_justify_content, image_justify_content, cta_justify_content;
						
						if (options["pricing_box_price_layout"]=='vertical') {
				            price_flex_direction = "column";
				            if (options["pricing_box_price_alignment"]=='left') {
				                price_flex_alignment = "flex-start";
				            } else if (options["pricing_box_price_alignment"]=='center') {
				                price_flex_alignment = "center";
				            } else if (options["pricing_box_price_alignment"]=='right') {
				                price_flex_alignment = "flex-end";
				            } 
				        } else if (options["pricing_box_price_layout"]=='horizontal') {
				            price_flex_direction = "row";
				        }

				        if (options["pricing_box_price_layout"]=='horizontal') {
				            if (options["pricing_box_price_alignment"]=='left') {
				                price_justify_content = "flex-start";
				            } else if (options["pricing_box_price_alignment"]=='center') {
				                price_justify_content = "center";
				            } else if (options["pricing_box_price_alignment"]=='right') {
				                price_justify_content = "flex-end";
				            }
				        }

				        if (options["pricing_box_global_alignment"]=='left') {
				            global_justify_content = "flex-start";
				        } else if (options["pricing_box_global_alignment"]=='center') {
				            global_justify_content = "center";
				        } else if (options["pricing_box_global_alignment"]=='right') {
				            global_justify_content = "flex-end";
				        }

				        if (options["pricing_box_graphic_alignment"]=='left') {
				            image_justify_content = "flex-start";
				        } else if (options["pricing_box_graphic_alignment"]=='center') {
				            image_justify_content = "center";
				        } else if (options["pricing_box_graphic_alignment"]=='right') {
				            image_justify_content = "flex-end";
				        } else if (global_justify_content) {
				        	image_justify_content = global_justify_content;
				        }

				        if (options["pricing_box_cta_alignment"]=='left') {
				            cta_justify_content = "flex-start";
				        } else if (options["pricing_box_cta_alignment"]=='center') {
				            cta_justify_content = "center";
				        } else if (options["pricing_box_cta_alignment"]=='right') {
				            cta_justify_content = "flex-end";
				        } else if (global_justify_content) {
				        	cta_justify_content = global_justify_content;
				        }

			            style += '.' + name + currentState + ' .oxy-pricing-box-section {' +
			                $scope.generateArrayOptionsCSS(options, 'pricing_box_global') +
			                'text-align:' + options['pricing_box_global_alignment'] + ";" +
			            '}';

			            style += '.' + name + currentState + ' .oxy-pricing-box-section.oxy-pricing-box-price {' +
			                'justify-content:' + global_justify_content + ';' +
			            '}';


			            /* IMAGE */
			            style += '.' + name + currentState + ' .oxy-pricing-box-section.oxy-pricing-box-graphic {' +
			                $scope.generateArrayOptionsCSS(options, 'pricing_box_graphic') +
			                'background-color:' + $scope.getGlobalColorValue(options['pricing_box_graphic_background']) + ';' +
			                'text-align:'+ options['pricing_box_graphic_alignment'] + ';' +
			                'justify-content:' + image_justify_content + ';' +
			            '}';

			            /* TITLE */
			            style += '.' + name + currentState + ' .oxy-pricing-box-section.oxy-pricing-box-title {' +
			                $scope.generateArrayOptionsCSS(options, 'pricing_box_title') +
			                'text-align:'+ options['pricing_box_title_alignment'] + ';' +
			                'background-color:' + $scope.getGlobalColorValue(options['pricing_box_title_background']) + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-pricing-box-title-title {' +
			                $scope.generateTypographyCSS(options, 'pricing_box_title_typography') +
			            '}';

			            style += '.' + name + currentState + ' .oxy-pricing-box-title-subtitle {' +
			                $scope.generateTypographyCSS(options, 'pricing_box_subtitle_typography') +
			            '}';


			            /* PRICE */

			            style += '.' + name + currentState + ' .oxy-pricing-box-section.oxy-pricing-box-price {' +
			                $scope.generateArrayOptionsCSS(options, 'pricing_box_price') +
			                'text-align:' + options['pricing_box_price_alignment'] + ';' +
			                'background-color:' + $scope.getGlobalColorValue(options['pricing_box_price_background']) + ';' +
			                'flex-direction:' + price_flex_direction + ';' +
			                'justify-content:' + price_justify_content + ';' +
			                'align-items:' + price_flex_alignment + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-pricing-box-currency {' +
			                $scope.generateTypographyCSS(options, 'pricing_box_price_amount_currency_typography') +
			            '}';

			            style += '.' + name + currentState + ' .oxy-pricing-box-amount-main {' +
			                $scope.generateTypographyCSS(options, 'pricing_box_price_amount_main_typography') +
			            '}';

			            style += '.' + name + currentState + ' .oxy-pricing-box-amount-decimal {' +
			                $scope.generateTypographyCSS(options, 'pricing_box_price_amount_decimal_typography') +
			            '}';
			            
			            style += '.' + name + currentState + ' .oxy-pricing-box-term {' +
			                $scope.generateTypographyCSS(options, 'pricing_box_price_amount_term_typography') +
			            '}';

			            style += '.' + name + currentState + ' .oxy-pricing-box-sale-price {' +
			                $scope.generateTypographyCSS(options, 'pricing_box_price_sale_typography') +
			                'margin-bottom:' + options['pricing_box_price_sale_space_below'] + ';' +
			            '}';

			            /* CONTENT */
			            style += '.' + name + currentState + ' .oxy-pricing-box-section.oxy-pricing-box-content {' +
			                $scope.generateArrayOptionsCSS(options, 'pricing_box_content') +
			                'background-color:' + $scope.getGlobalColorValue(options['pricing_box_content_background']) + ';' +
			                'text-align:' + options['pricing_box_content_alignment'] + ';' +
			                $scope.generateTypographyCSS(options, 'pricing_box_content_typography') +
			            '}';

			            /* CTA */
			            style += '.' + name + currentState + ' .oxy-pricing-box-section.oxy-pricing-box-cta {' +
			                $scope.generateArrayOptionsCSS(options, 'pricing_box_cta') +
			                'background-color:' + $scope.getGlobalColorValue(options['pricing_box_cta_background']) + ';' +
			                'text-align:' + options['pricing_box_cta_alignment'] + ';' +
			                'justify-content:' + cta_justify_content + ';' +
			            '}';
				}

				if ( !isCustomSelectors && $scope.classHasOptions(name, "slider-") ) {

					if (!whichMedia && state == "original") {
							
							if ( options['slider-arrow-color'] && 
								 options['slider-arrow-color'] == 'lighter') {

									style += '.' + name + " .unslider-arrow {";
									style += "background-color: rgba(255,255,255,0.2); ";
									style += "}";
							}

							if ( options['slider-dot-color'] ) {

									style += '.' + name + " .unslider-nav ol li {";
									style += "border-color: " + $scope.getGlobalColorValue(options['slider-dot-color']) + "; ";
									style += "}";

									style += '.' + name + " .unslider-nav ol li.unslider-active {";
									style += "background-color: " + $scope.getGlobalColorValue(options['slider-dot-color']) + "; ";
									style += "}";
							}

							if ( options['slider-remove-padding'] && 
								 options['slider-remove-padding'] == 'yes') {

									style += '.' + name + " .unslider {";
									style += "padding: 0px; ";
									style += "}";

									style += '.' + name + " .unslider-wrap.unslider-carousel > li {";
									style += "padding: 0px; ";
									style += "}";

									style += '.' + name + " .unslider-arrow.next {";
									style += "right: 10px; ";
									style += "z-index: 100; ";
									style += "}";

									style += '.' + name + " .unslider-arrow.prev {";
									style += "left: 10px; ";
									style += "z-index: 100; ";
									style += "}";
							}

							if ( options['slider-dots-overlay'] && 
								 options['slider-dots-overlay'] == 'yes') {

									style += '.' + name + " .unslider .unslider-nav {";
									style += "position: absolute; bottom: 0; left: 0; right: 0; z-index: 100";
									style += "}";
							}
							if ( options['slider-stretch-slides'] && 
								 options['slider-stretch-slides'] == 'yes') {

									style += '.' + name + " .unslider-wrap {";
									style += "display: flex;";
									style += "}";

									style += '.' + name + " .ct-slide {";
									style += "height: 100%;";
									style += "}";

									style += '.' + name + " .unslider,";
									style += '.' + name + " .oxygen-unslider-container,";
									style += '.' + name + " .unslider-wrap,";
									style += '.' + name + " .unslider-wrap li {";
									style += "height: 100%;";
									style += "}";
							}

							if ( options['slider-stretch-slides'] && 
								 options['slider-stretch-slides'] == 'yes' &&
								 options['slider-animation'] && 
								 options['slider-animation'] == 'fade' ) {

									style += '.' + name + " .unslider-fade ul li.unslider-active {";
									style += "width: 100%;";
									style += "}";
							}

							if ( options['slider-slide-padding'] ) {

									style += '.' + name + " .ct-slide {";
									style += "padding: " + options['slider-slide-padding'];
									style += "}";
							}
					}
				}

				var isToggle = false;
				if ($scope.classes[name]) {
					for (paramName in $scope.classes[name]['original']) {
						if (paramName.indexOf("toggle_")===0) {
							isToggle = true;
						}
					}
				}
				
				if ( !isCustomSelectors && isToggle ) {
						style += '.' + name + currentState + ' .oxy-expand-collapse-icon {' +
			                'font-size:' + options['toggle_icon_size'] + ';' +
			            '}';

			            style += '.' + name + currentState + ' .oxy-expand-collapse-icon::before,' +
			            		 '.' + name + currentState + ' .oxy-expand-collapse-icon::after {' +
			                'background-color:' + $scope.getGlobalColorValue(options['toggle_icon_color']) + ';' +
			            '}';
				}

				var isSuperBox = false;
				if ($scope.classes[name]) {
					for (paramName in $scope.classes[name]['original']) {
						if (paramName.indexOf("superbox_")===0) {
							isSuperBox = true;
						}
					}
				}
			    if ( !isCustomSelectors && isSuperBox ) {
						// scaling
				        var superbox_secondary_scale_start  = options['superbox_secondary_scale_start'],
				        	superbox_secondary_scale_finish = options['superbox_secondary_scale_finish'],
				        	superbox_primary_scale_start    = options['superbox_primary_scale_start'],
				        	superbox_primary_scale_finish   = options['superbox_primary_scale_finish'],
				        	css, 
				        	superbox_secondary_initial_css, superbox_secondary_hover_css,
				        	superbox_primary_initial_css, superbox_primary_hover_css,
				        	superbox_secondary_scale_start_transform_css,
							superbox_secondary_scale_finish_transform_css,
							superbox_primary_scale_start_transform_css,
							superbox_primary_scale_finish_transform_css,
							superbox_secondary_initial_css = "",
							superbox_secondary_hover_css = "",
							superbox_primary_initial_css = "",
							superbox_primary_hover_css = "";

				        if (superbox_secondary_scale_start !== undefined)     superbox_secondary_scale_start_transform_css = "transform: scale("+superbox_secondary_scale_start+");";
				        if (superbox_secondary_scale_finish !== undefined)    superbox_secondary_scale_finish_transform_css = "transform: scale("+superbox_secondary_scale_finish+");";
				        if (superbox_primary_scale_start !== undefined)       superbox_primary_scale_start_transform_css = "transform: scale("+superbox_primary_scale_start+");";
				        if (superbox_primary_scale_finish !== undefined)      superbox_primary_scale_finish_transform_css = "transform: scale("+superbox_primary_scale_finish+");";

				        // sliding
				        if (options['superbox_secondary_slide_direction']) {
				            css = $scope.slide_position(options['superbox_secondary_slide_direction'], (options['superbox_secondary_slide_distance']||'')+"px");

				            if (options['superbox_secondary_slide_inorout'] == "in") {
				                superbox_secondary_initial_css = css['out_css'];
				                superbox_secondary_hover_css = css['in_css'];
				            } else if (options['superbox_secondary_slide_inorout'] == "out") {
				                superbox_secondary_initial_css = css['in_css'];
				                superbox_secondary_hover_css = css['out_css'];            
				            }
				        }

				        if (options['superbox_primary_slide_direction']) {

				            css = $scope.slide_position(options['superbox_primary_slide_direction'], (options['superbox_primary_slide_distance']||'')+"px");

				            if (options['superbox_primary_slide_inorout'] == "in") {
				                superbox_primary_initial_css = css['out_css'];
				                superbox_primary_hover_css = css['in_css'];
				            } else if (options['superbox_primary_slide_inorout'] == "out") {
				                superbox_primary_initial_css = css['in_css'];
				                superbox_primary_hover_css = css['out_css'];            
				            }
				        }

				        var transitionDuration = options['superbox_transition_duration'];

				        style += '.' + name + " .oxy-superbox-secondary, " +
				         		 '.' + name + " .oxy-superbox-primary {" +
				             'transition-duration:' + transitionDuration + ';' +
				        '}';

				        style += '.' + name + " .oxy-superbox-secondary {" +
				            'opacity:' + options['superbox_secondary_opacity_start'] + ";" +
				            superbox_secondary_initial_css +
				            superbox_secondary_scale_start_transform_css +
				        '}';

				        style += '.' + name +":hover .oxy-superbox-secondary {" +
				            'opacity:' + options['superbox_secondary_opacity_finish'] + ";" +
				            'z-index: 2147483642;' +
				            superbox_secondary_hover_css +
				            superbox_secondary_scale_finish_transform_css +
				        '}';

				        style += '.' + name + " .oxy-superbox-primary {" +
				            'opacity:' + options['superbox_primary_opacity_start'] + ";" +
				            superbox_primary_initial_css +
				            superbox_primary_scale_start_transform_css +
				        '}';

				        style += '.' + name + ":hover .oxy-superbox-primary {" +
				            'opacity:' + options['superbox_primary_opacity_finish'] + ";" +
				            superbox_primary_hover_css +
				            superbox_primary_scale_finish_transform_css +
				        '}';
					}
				
				// API Registered Selectors. Support classes?
				if ($scope.componentsTemplates !== undefined) {
					
					for(var tag in $scope.componentsTemplates) { 
						if ($scope.componentsTemplates.hasOwnProperty(tag)) {
							var isTemplate = false;
							if ($scope.classes[name]) {

								//....................................Support classes?
								for (paramName in $scope.classes[name][state]) {
									if (paramName.indexOf(tag)===0) {
										isTemplate = true;
									}
								}
							}
							if (!isCustomSelectors && isTemplate && $scope.componentsTemplates[tag]['registeredSelectors']) {

					        	var selectorsOptions = $scope.componentsTemplates[tag]['registeredSelectors'];
					        		selectorsCSS = "";
					        	
					        	for(var key in selectorsOptions) { 
									if (selectorsOptions.hasOwnProperty(key)) {
										
										var selector = selectorsOptions[key],
											selectorCSS = "";
										
										if (selector) {
											for(var property in selector) { 
												if (selector.hasOwnProperty(property) && options[selector[property]]) {
													selectorCSS += property+":"+$scope.getGlobalColorValue(options[selector[property]])+";";
												}
											}
										}

										if (selectorCSS) {
											selectorsCSS += '.' + name + " " + key + currentState + "{" + selectorCSS + "}";
										}
									}
								}

								style += selectorsCSS;
							}
						}
					}
				}

				// Add API option values CSS snippets
				if (options && $scope.componentsTemplates !== undefined ) {

					for(var tag in $scope.componentsTemplates) { 
						if ($scope.componentsTemplates.hasOwnProperty(tag)) {

							var valuesCSS = $scope.componentsTemplates[tag]["valuesCSS"];

							for(var option in valuesCSS) { 
								if (valuesCSS.hasOwnProperty(option)) {
									if ( valuesCSS[option][options[option]] ) {
										var valueCSS = valuesCSS[option][options[option]];
										style += $scope.prefixCSSSelectors(valueCSS,name,true);
									}
								}
							}
						}
					}
				}

				// handle box-shadow options
				if ( options['box-shadow-color'] ) {

					var inset 	= (options['box-shadow-inset']=='inset') 		? options['box-shadow-inset']+" " : "";
					var hor 	= (options['box-shadow-horizontal-offset']) 	? options['box-shadow-horizontal-offset']+"px " : "";
					var ver 	= (options['box-shadow-vertical-offset']) 		? options['box-shadow-vertical-offset']+"px " : "";
					var blur 	= (options['box-shadow-blur']) 					? options['box-shadow-blur']+"px " : "0px ";
					var spread  = (options['box-shadow-spread']) 				? options['box-shadow-spread']+"px " : "";
						
					options['box-shadow'] = inset+hor+ver+blur+spread+$scope.getGlobalColorValue(options['box-shadow-color']);
				}

				// handle text-shadow options
				if ( options['text-shadow-color'] ) {

					var hor 	= (options['text-shadow-horizontal-offset']) 	? options['text-shadow-horizontal-offset']+"px " : "";
					var ver 	= (options['text-shadow-vertical-offset']) 		? options['text-shadow-vertical-offset']+"px " : "";
					var blur 	= (options['text-shadow-blur']) 				? options['text-shadow-blur']+"px " : "0px ";
						
					options['text-shadow'] = hor+ver+blur+$scope.getGlobalColorValue(options['text-shadow-color']);
				}


				if (!isCustomSelectors) {
					style += '.' + name  + currentState;
				}
				else {
					if (name.indexOf(".")<0&&name.indexOf("#")<0&&name.indexOf("body")<0) {
						style += ".oxygen-body "+ name + currentState;
					}
					else {
						style += name + currentState;	
					}
				}

				style += "{";

				// loop all parameters
				style += $scope.getBackgroundLayersCSS(options, null, isCustomSelectors, name, whichMedia, state) || "";
			    style += $scope.getTransformCSS(options) || "";

				for(var parameter in options) { 
					if (options.hasOwnProperty(parameter)) {

						if (parameter=="custom-css") {
							continue;
						}

						// since 2.0
						if ( ["display","flex-direction","flex-wrap","align-items","align-content","justify-content","gap"].indexOf(parameter) >= 0 &&
							 !isCustomSelectors ) { 
							continue; // this will be added for inner wrap
						}
						
						var value = options[parameter];

						if (parameter=="flex-direction") {
							var reverse = (options['flex-reverse'] == 'reverse') ? "-reverse" : "";
							style += parameter + ":" + value + reverse + ";";
							continue;
						}

						// css filter property
						if ( parameter == "filter" && options["filter-amount-"+value] ) {
							value += "("+options["filter-amount-"+value]+")";
						}
						else if ( parameter == "filter" ) {
							continue;
						}
				
						if ( parameter.trim().toLowerCase() == "content" ) {
							//value = "\"" + $scope.addSlashes(value) + "\"";
							value = "\"" + value.replace('"','\\"') + "\"";
							$scope.contentAdded[name+currentState] = true;
						}

						// load Web Fonts
						if ( (parameter == "font-family"||parameter.indexOf("font-family")>-1) && value !== undefined) {
							$scope.loadWebFont(value);
							if ( value.indexOf(',') === -1 && value.toLowerCase() !== "inherit") {
								value = "'"+value+"'";
							}
						}

						if(parameter=='background-image' || parameter=='background-size') { //this is being taken care off by getBackgroundLayersCSS function
							continue;
						}

						if ( value && $scope.excludeProperties.indexOf(parameter) < 0 && parameter !== "background-layers") {

							style += parameter + ":" + $scope.getGlobalColorValue(value) + ";";
						}	

						if (parameter == "-webkit-font-smoothing") {
							style += '-moz-osx-font-smoothing' + ":" + (value === 'antialiased' ? 'grayscale' : 'unset') + ";";
						}
						
					}
				}
				
				if ((state=="before"||state=="after")&&!$scope.contentAdded[name+currentState]) {
					style += "content:\"\";";
					$scope.contentAdded[name+currentState] = true;
				}

				if (options["custom-css"]) {
					style += $scope.replaceGlobalColors(options["custom-css"]);
				}

				style += "}";

				// handle section container
				if ( options['container-padding-top'] 	 ||
					 options['container-padding-right']  ||
					 options['container-padding-bottom'] ||
					 options['container-padding-left']
				 ) {

					style += '.' + name + currentState + " .ct-section-inner-wrap {";
					
						// custom-padding
						if ( options['container-padding-top'] ) {
							style += "padding-top" 		+ ":" + options['container-padding-top'] + ";";
						}
						if ( options['container-padding-right'] ) {
							style += "padding-right"	+ ":" + options['container-padding-right'] + ";";
						}
						if ( options['container-padding-bottom'] ) {
							style += "padding-bottom" 	+ ":" + options['container-padding-bottom'] + ";";
						}
						if ( options['container-padding-left'] ) {
							style += "padding-left" 	+ ":" + options['container-padding-left'] + ";";
						}
					
					style += '}';
				}

				// handle section flex
				if ( ( options['display'] ||
					 options['flex-direction']  ||
					 options['flex-wrap'] ||
					 options['align-items'] ||
					 options['align-content'] ||
					 options['justify-content'] ||
					 options['gap'] ) &&
					!isCustomSelectors
				 ) {

					style += '.' + name + ":not(.ct-section)" + currentState;
					if ( $scope.isPseudoElement(currentState) ) {
						style += ',.' + name + ".ct-section .ct-section-inner-wrap" + currentState;
					}
					else {
						style += ',.' + name + ".ct-section" + currentState + " .ct-section-inner-wrap";
					}

					style += "{";
					
						if ( options['display'] && options['flex-direction'] != 'grid' ) {
							style += "display:" + options['display'] + ";";
						}

						var reverse = (options['flex-reverse'] == 'reverse') ? "-reverse" : "";
						
						if ( options['flex-direction'] && options['flex-direction'] != 'grid' ) {
							style += "flex-direction:" + options['flex-direction'] + reverse + ";";
						}

						if ( options['justify-content'] ) {
							style += "justify-content:" + options['justify-content'] + ";";
						}

						if ( options['align-content'] ) {
							style += "align-content:" + options['align-content'] + ";";
						}

						if ( options['align-items'] ) {
							style += "align-items:" + options['align-items'] + ";";
						}

						if ( options['flex-wrap'] ) {
							style += "flex-wrap:" + options['flex-wrap'] + ";";
						}

						if ( options['gap'] ) {
							style += "gap:" + options['gap'] + ";";
						}
					
					style += '}';
				}

				// handle grid
				if ( options['display'] == 'grid' ) {

					var grid_css = $scope.getGridCSS(options, undefined, "forceDisplayGrid") || "";

					if (grid_css) {

						style += '.' + name + ":not(.ct-section):not(.oxy-easy-posts)" + currentState;
						if ( $scope.isPseudoElement(currentState) ) {
							style += ',.' + name + ".oxy-easy-posts .oxy-posts" + currentState;
							style += ',.' + name + ".ct-section .ct-section-inner-wrap" + currentState;
						}
						else {
							style += ',.' + name + ".oxy-easy-posts" + currentState + " .oxy-posts";
							style += ',.' + name + ".ct-section" + currentState + " .ct-section-inner-wrap";
						}

						style += "{";
						style += grid_css;
						style += '}';
					}

					style += $scope.getGridChildCSS(options, name, currentState, false, "is class");
				}
			}
		}
		return style;
	}


    /**
     * Recursive function to generate CSS based on items tree
     *
     * @since 0.2.2
     * @author Ilya K.
     */

    $scope.generateTreeCSS = function(treeNode, css, callback, callbacktrigger) {
    	
    	if(typeof(callbacktrigger) === 'undefined');
    		callbacktrigger = 0;

    	callbacktrigger++;

    	// do nothing if post contet is not made in builder
		if( Object.prototype.toString.call(treeNode) !== '[object Array]' ) {
			return false;
		}

		if ($scope.log) {
			console.log("generateTreeCSS()", treeNode, css);
		}

    	// loop all componets
    	angular.forEach(treeNode, function(component) {

    		// add name to options
    		component.options.name = component.name;

    		// set original options if not defined
    		if (undefined === component.options.original) {
    			//component.options.original = {};
    		}
    		
    		component.options.id = component.options.original;


			$scope.generateDynamicBackgrounds(component.id, false, 'id', function() {
				// get CSS styles
				css.styles += $scope.getSingleComponentCSS(component.options, component.id);
				
				// loop children if any
				if ( component.children ) {
					$scope.generateTreeCSS(component.children, css, callback, callbacktrigger);
				}
				else {
					if( callbacktrigger <= 1 && callback ) {
			    		callback();
			    	}
				}

			}, {children: treeNode});



   //  		// get CSS styles
   //  		css.styles += $scope.getSingleComponentCSS(component.options, component.id);

			// // loop children if any
			// if ( component.children ) {
			// 	$scope.generateTreeCSS(component.children, css);
			// }
    	});

    	callbacktrigger--;

	}


	/**
     * Check if specified class has any options with specfied prefix
     *
     * @since 2.1
     * @author Ilya K.
     */

	$scope.classHasOptions = function(className, prefix) {
		
		if ($scope.classes[className]) {
			for (paramName in $scope.classes[className]['original']) {
				if (paramName.indexOf(prefix)===0) {
					return true;
				}
			}
		}
	}


	/**
     * Check if any class of current element class has specific option
     *
     * @since 3.9
     * @author Ilya K.
     */

	$scope.classHasOption = function(optionName) {

		var classes = $scope.componentsClasses[$scope.component.active.id];
		if (classes != undefined) {
			for (key in classes) {
				
				var className = classes[key];

				for (stateKey in $scope.classes[className]) {
					if (stateKey=='media'||stateKey=='parent') {
						continue;
					}
					var stateOptions = $scope.classes[className][stateKey];
					if (optionName.indexOf('.')>-1) {
						optionName = optionName.split('.');
						if (optionName.length==2) {
							if( stateOptions[optionName[0]] && 
								stateOptions[optionName[0]][optionName[1]]) {
								return true;
							}
						}
						if (optionName.length==3) {
							if( stateOptions[optionName[0]] &&
								stateOptions[optionName[0]][optionName[1]] &&
								stateOptions[optionName[0]][optionName[1]][optionName[2]] )  {
								return true;
							}
						}
					}
					else if (stateOptions[optionName]) {
						return true;
					}
				}

				if (!$scope.isEditing('media')) {
					return false;
				}
				
				if ($scope.classes[className]['media']) {

					var breakpoints = $scope.sortedMediaList();

					for(var key in breakpoints) { 
						if (breakpoints.hasOwnProperty(key)) {
							var currentMedia = breakpoints[key];

							if (currentMedia=="default") {
								continue
							}

							if ($scope.classes[className]['media'][currentMedia]) { 

								var mediaStates = $scope.classes[className]['media'][currentMedia];
								for (stateKey in mediaStates) {
									var stateOptions = mediaStates[stateKey];
									if (optionName.indexOf('.')>-1) {
										optionName = optionName.split('.');
										if (optionName.length==2) {
											if( stateOptions[optionName[0]] && 
												stateOptions[optionName[0]][optionName[1]]) {
												return true;
											}
										}
										if (optionName.length==3) {
											if( stateOptions[optionName[0]] &&
												stateOptions[optionName[0]][optionName[1]] &&
												stateOptions[optionName[0]][optionName[1]][optionName[2]] )  {
												return true;
											}
										}
									}
									else if (stateOptions[optionName]) {
										return true;
									}
								}
							}

							if (currentMedia == $scope.getCurrentMedia()) {
								return false;
							}

						}
					}
				}

			}
		}
	}


	$scope.classHasOptionEqualTo = function(optionName, optionValue) {

		var classes = $scope.componentsClasses[$scope.component.active.id];
		if (classes != undefined) {
			for (key in classes) {
				
				var className = classes[key];
				
				if ($scope.classes[className]['media']) {
					for (mediaKey in $scope.classes[className]['media']) {
						var mediaStates = $scope.classes[className]['media'][mediaKey];
						for (stateKey in mediaStates) {
							var stateOptions = mediaStates[stateKey];
							if (optionName.indexOf('.')>-1) {
								optionName = optionName.split('.');
								if (optionName.length==2) {
									if( stateOptions[optionName[0]] && 
										stateOptions[optionName[0]][optionName[1]] &&
										stateOptions[optionName[0]][optionName[1]] == optionValue) {
										return true;
									}
								}
								if (optionName.length==3) {
									if( stateOptions[optionName[0]] &&
										stateOptions[optionName[0]][optionName[1]] &&
										stateOptions[optionName[0]][optionName[1]][optionName[2]] &&
										stateOptions[optionName[0]][optionName[1]][optionName[2]] == optionValue)  {
										return true;
									}
								}
							}
							else if (stateOptions[optionName]) {
								return true;
							}
						}
					}
				}

				for (stateKey in $scope.classes[className]) {
					if (stateKey=='media'||stateKey=='parent') {
						continue;
					}
					var stateOptions = $scope.classes[className][stateKey];
					if (optionName.indexOf('.')>-1) {
						optionName = optionName.split('.');
						if (optionName.length==2) {
							if( stateOptions[optionName[0]] && 
								stateOptions[optionName[0]][optionName[1]] &&
								stateOptions[optionName[0]][optionName[1]] == optionValue) {
								return true;
							}
						}
						if (optionName.length==3) {
							if( stateOptions[optionName[0]] &&
								stateOptions[optionName[0]][optionName[1]] &&
								stateOptions[optionName[0]][optionName[1]][optionName[2]] &&
								stateOptions[optionName[0]][optionName[1]][optionName[2]] == optionValue)  {
								return true;
							}
						}
					}
					else if (stateOptions[optionName] && stateOptions[optionName] == optionValue) {
						return true;
					}
				}
			}
		}
	}


	/**
     * Check if current ID has specific option
     *
     * @since 3.9
     * @author Ilya K.
     */

	$scope.IDHasOption = function(optionName) {

		var id = $scope.component.active.id;

		for (stateKey in $scope.component.options[id]) {
			if (stateKey=='media'||stateKey=='model'||stateKey=='original') {
				continue;
			}
			var stateOptions = $scope.component.options[id][stateKey] || [];

			if (optionName.indexOf('.')>-1) {
				optionName = optionName.split('.');
				if (optionName.length==2) {
					if( stateOptions[optionName[0]] && 
						stateOptions[optionName[0]][optionName[1]]) {
						return true;
					}
				}
				if (optionName.length==3) {
					if( stateOptions[optionName[0]] &&
						stateOptions[optionName[0]][optionName[1]] &&
						stateOptions[optionName[0]][optionName[1]][optionName[2]] )  {
						return true;
					}
				}
			}
			else if (stateOptions[optionName]) {
				return true;
			}
		}

		if (!$scope.isEditing('media')) {
			return false;
		}
		
		if ($scope.component.options[id]['media']) {

			var breakpoints = $scope.sortedMediaList();

			for(var key in breakpoints) { 
				if (breakpoints.hasOwnProperty(key)) {
					var currentMedia = breakpoints[key];

					if (currentMedia=="default") {
						continue
					}

					if ($scope.component.options[id]['media'][currentMedia]) { 

						var mediaStates = $scope.component.options[id]['media'][currentMedia];
						for (stateKey in mediaStates) {
							
							var stateOptions = mediaStates[stateKey];
							
							if (optionName.indexOf('.')>-1) {
								optionName = optionName.split('.');
								if (optionName.length==2) {
									if( stateOptions[optionName[0]] && 
										stateOptions[optionName[0]][optionName[1]]) {
										return true;
									}
								}
								if (optionName.length==3) {
									if( stateOptions[optionName[0]] &&
										stateOptions[optionName[0]][optionName[1]] &&
										stateOptions[optionName[0]][optionName[1]][optionName[2]] )  {
										return true;
									}
								}
							}
							else if (stateOptions[optionName]) {
								return true;
							}
						}
					}

					if (currentMedia == $scope.getCurrentMedia()) {
						return false;
					}
				}
			}
		}
	}


	$scope.IDHasOptionEqualTo = function(optionName, optionValue) {

		var id = $scope.component.active.id;
		
		if ($scope.component.options[id]['media']) {
			for (mediaKey in $scope.component.options[id]['media']) {
				var mediaStates = $scope.component.options[id]['media'][mediaKey];
				for (stateKey in mediaStates) {
					
					var stateOptions = mediaStates[stateKey];
					
					if (optionName.indexOf('.')>-1) {
						optionName = optionName.split('.');
						if (optionName.length==2) {
							if( stateOptions[optionName[0]] && 
								stateOptions[optionName[0]][optionName[1]] &&
								stateOptions[optionName[0]][optionName[1]] == optionValue ) {
								return true;
							}
						}
						if (optionName.length==3) {
							if( stateOptions[optionName[0]] &&
								stateOptions[optionName[0]][optionName[1]] &&
								stateOptions[optionName[0]][optionName[1]][optionName[2]] &&
								stateOptions[optionName[0]][optionName[1]][optionName[2]] == optionValue )  {
								return true;
							}
						}
					}
					else if (stateOptions[optionName]) {
						return true;
					}
				}
			}
		}

		for (stateKey in $scope.component.options[id]) {
			if (stateKey=='media'||stateKey=='model'||stateKey=='original') {
				continue;
			}
			var stateOptions = $scope.component.options[id][stateKey] || [];
			
			if (optionName.indexOf('.')>-1) {
				optionName = optionName.split('.');
				if (optionName.length==2) {
					if( stateOptions[optionName[0]] && 
						stateOptions[optionName[0]][optionName[1]] &&
						stateOptions[optionName[0]][optionName[1]] == optionValue) {
						return true;
					}
				}
				if (optionName.length==3) {
					if( stateOptions[optionName[0]] &&
						stateOptions[optionName[0]][optionName[1]] &&
						stateOptions[optionName[0]][optionName[1]][optionName[2]] &&
						stateOptions[optionName[0]][optionName[1]][optionName[2]] == optionValue)  {
						return true;
					}
				}
			}
			else if (stateOptions[optionName] && stateOptions[optionName] == optionValue) {
				return true;
			}
		}
	}


	/**
     * Apply component CSS
     *
     * @since 0.3.1
     * @author Ilya K.
     */

	$scope.applyComponentCSS = function(id, name) {

		if (undefined==id) {
            id = $scope.component.active.id;
        }

        if (undefined==name) {
            name = $scope.component.active.name;
        }

		// update Tree
		$scope.setOption(id, name, "custom-css", false, false);
	}
	
	
	/**
     * Apply Style Sheet CSS
     *
     * @since 0.3.4
     * @author Gagan Goraya
     */

	$scope.applyStyleSheet = function(stylesheet) {
 		
 		var stylesheetCSS = $scope.replaceGlobalColors(stylesheet['css']),
 			styleSheetsOutput = "\n"+stylesheetCSS+"\n",
			styleSheetsContainer = document.getElementById("ct-style-sheet-"+stylesheet['name']);

		if(styleSheetsContainer === null) {
			var locations = document.getElementsByClassName("ct-css-location"),
				location = angular.element(locations[locations.length - 1]);

			styleSheetsContainer = angular.element('<style>').attr('id', 'ct-style-sheet-'+stylesheet['name']).addClass('ct-css-location')
			styleSheetsContainer.insertAfter(location);
		}
		else
			styleSheetsContainer = angular.element(styleSheetsContainer);
        
        styleSheetsContainer.empty();
        
        styleSheetsContainer.append(styleSheetsOutput);

        // check for brackets
        if ( bracket = $scope.bracketsFailed(styleSheetsOutput) ) {
        	if ( bracket == "(" || bracket == "{" ) {
        		var position = "closing";
        	}
        	else {
				var position = "opening";
        	}
        	var text = "Warning: there is no " + position + " bracket for \"" + bracket + "\" somewhere in your CSS. This may break page styling."
        	jQuery(".oxygen-code-error-container", $parentScope.oxygenUIElement).show().html(text);
        }
        else {
        	jQuery(".oxygen-code-error-container", $parentScope.oxygenUIElement).hide().html("");
        }

        $scope.unsavedChanges();
    }

	
	/**
     * Add style sheets if not exist
     *
     * @author Ilya K.
     * @since 0.4.0
     */

    $scope.addDesignSetStyleSheets = function(data) {

    	for(var key in data) { 
			if (data.hasOwnProperty(key)) {
				var stylesheet = data[key];
				
				// check for repeat
				if( typeof(stylesheet) === 'object' && typeof($scope.styleSheets[stylesheet["name"]]) === 'undefined' ) {
					$scope.styleSheets[stylesheet["name"]] = $scope.stripSlashes(stylesheet["content"]);
					$scope.applyStyleSheet(stylesheet["name"]);
				}
			}
		}
    }


	/**
     * Helper function to parse width into value and unit
     *
     * @return {object}
     * @author Ilya K.
     */

	$scope.getWidth = function(width) {

		if (!width) {
			return {
				value : "",
				unit : ""
			}
		}

		var value = parseInt(width, 10);
		var unit = '';

		if(value !== width) {
			if ( width.indexOf("%") > -1 ) {
				unit = "%";
			}

			else if ( width.indexOf("em") > -1 ) {
				unit = "em";
			}

			else if ( width.indexOf("rem") > -1 ) {
				unit = "rem";
			}
			else
				unit = "px";
		}

		return {
			value: value,
			unit: unit
		}
	}


	/**
	 * Output page settings CSS
	 *
	 * @since 0.4.0
	 * @author Ilya K.
	 */
	
	$scope.outputPageSettingsCSS = function() {

		/**
		 * Page width
		 */

		var style = 'div.ct-section-inner-wrap, div.oxy-header-container{';
		style += "max-width" + ":" + $scope.getPageWidth() + "px;";
		style += "}";

		/**
		 * Header Overlay
		 */

		if ($scope.getPageSetting('overlay-header-above')&&$scope.getPageSetting('overlay-header-above')!='never') {
			if ($scope.getPageSetting('overlay-header-above')!='always') {
				style += '@media (min-width: '+$scope.getMediaMinSize($scope.getPageSetting('overlay-header-above'))+') {';
			}
			style += 'body.oxy-overlay-header .oxy-header {'+
					'position: absolute;'+
					'left: 0;'+
					'right: 0;'+
					'z-index: 2147483640;'+
				'}'+
				'body.oxy-overlay-header .oxy-header:not(.oxy-sticky-header-active),'+
				'body.oxy-overlay-header .oxy-header:not(.oxy-sticky-header-active) .oxy-header-row {'+
					'background-color: initial !important;'+
				'}'+
				'body.oxy-overlay-header .oxy-header .oxygen-hide-in-overlay{'+
					'display: none;'+
				'}'+
				'body.oxy-overlay-header .oxy-header .oxygen-only-show-in-overlay{'+
					'display: block;'+
				'}';
			if ($scope.getPageSetting('overlay-header-above')!='always') {
				style += '}';
			}
		}

		// output to head
        $scope.outputCSSStyles("ct-page-settings-styles", style);
	}


	/**
	 * Update global settings default CSS
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */
	
	$scope.updateGlobalSettingsCSS = function() {

		var css 		= "",
			textFont 	= $scope.getGlobalFont('Text'),
			displayFont = $scope.getGlobalFont('Display');

		if (textFont.indexOf(',')===-1) {
			textFont = "'"+textFont+"'";
		}
		if (displayFont.indexOf(',')===-1) {
			displayFont = "'"+displayFont+"'";
		}

		// Body Text
		css += "body {";
		css += "font-family: "+textFont+";";
		css += "line-height: "+$scope.globalSettings["body_text"]["line-height"]+";";
		css += "font-size: "+$scope.globalSettings["body_text"]["font-size"]+$scope.globalSettings["body_text"]["font-size-unit"]+";";
		css += "font-weight: "+$scope.globalSettings["body_text"]["font-weight"]+";";
		css += "color: "+$scope.getGlobalColorValue($scope.globalSettings["body_text"]["color"])+";";
		css += "}";

		css += ".oxy-nav-menu-hamburger-line {";
		css += "background-color: "+$scope.getGlobalColorValue($scope.globalSettings["body_text"]["color"])+";";
		css += "}";

		// Headings
		css += "h1, h2, h3, h4, h5, h6 {";
        css += "font-family: "+displayFont+";";
        
        if ($scope.globalSettings.headings["H1"]["font-size"] !== "") { 
        	css += "font-size: "+$scope.globalSettings.headings["H1"]["font-size"]+$scope.globalSettings.headings["H1"]["font-size-unit"]+";";
        }
        if ($scope.globalSettings.headings["H1"]["font-weight"] !== "") { 
        	css += "font-weight: "+$scope.globalSettings.headings["H1"]["font-weight"]+";";
        }
        if ($scope.globalSettings.headings["H1"]["line-height"] !== "") { 
        	css += "line-height: "+$scope.globalSettings.headings["H1"]["line-height"]+";";
        }
        if ($scope.globalSettings.headings["H1"]["color"] !== "") { 
        	css += "color: "+$scope.getGlobalColorValue($scope.globalSettings.headings["H1"]["color"])+";";
        }
        
        css += "}";

        var selector = "h2, h3, h4, h5, h6";

        for(var heading in $scope.globalSettings.headings) { 
			if ($scope.globalSettings.headings.hasOwnProperty(heading)) {

				var headingCss = "";
				
				if (heading=="H1") {
					continue;
				}
				
				if ($scope.globalSettings.headings[heading]["font-size"] !== "") {
					headingCss += "font-size: "+$scope.globalSettings.headings[heading]["font-size"]+$scope.globalSettings.headings[heading]["font-size-unit"]+";";
        		}
        		if ($scope.globalSettings.headings[heading]["font-weight"] !== "") {
        			headingCss += "font-weight: "+$scope.globalSettings.headings[heading]["font-weight"]+";";
       			}
        		if ($scope.globalSettings.headings[heading]["line-height"] !== "") {
        			headingCss += "line-height: "+$scope.globalSettings.headings[heading]["line-height"]+";";
       			}
       			if ($scope.globalSettings.headings[heading]["color"] !== "") {
       				headingCss += "color: "+$scope.getGlobalColorValue($scope.globalSettings.headings[heading]["color"])+";";
				}
       			if ( headingCss !== "" ) {
					css += selector + "{";
					css += headingCss;
					css += "}";
				}

       			// update selector
       			selector = selector.replace(heading.toLowerCase()+", ", "");
			}
		}

		// Links
		var links = {
			all:"a",
			text_link:".ct-link-text",
			link_wrapper:".ct-link",
			button:".ct-link-button"
		}

		for(var key in links) { 
			if (links.hasOwnProperty(key)) {

				var linksCss = "",
					selector = links[key];
				
				$scope.globalSettings.links[key] = $scope.globalSettings.links[key] || {}
				if ($scope.globalSettings.links[key]["color"] !== "") {
					linksCss += "color: "+$scope.getGlobalColorValue($scope.globalSettings.links[key]["color"])+";";
				}
				if ($scope.globalSettings.links[key]["font-weight"] !== "") {
					linksCss += "font-weight: "+$scope.globalSettings.links[key]["font-weight"]+";";
				}
				if ($scope.globalSettings.links[key]["text-decoration"]) {
					linksCss += "text-decoration: "+$scope.globalSettings.links[key]["text-decoration"] +";";
				}
				if ($scope.globalSettings.links[key]["border-radius"]) {
					linksCss += "border-radius: "+$scope.globalSettings.links[key]["border-radius"] + $scope.globalSettings.links[key]["border-radius-unit"] + ";";
				}

				if ( linksCss !== "" ) {
					css += selector + " {";
					css += linksCss;
					css += "}";
				}

				linksCss = "";
				if ($scope.globalSettings.links[key]["hover_color"] !== "") {
					linksCss += "color: "+$scope.getGlobalColorValue($scope.globalSettings.links[key]["hover_color"])+";";
				}
				if ($scope.globalSettings.links[key]["hover_text-decoration"]) {
					linksCss += "text-decoration: "+$scope.globalSettings.links[key]["hover_text-decoration"] +";";
				}

				if ( linksCss !== "" ) {
					css += selector + ":hover {";
					css += linksCss;
					css += "}";
				}
				
			}
		}

		css += ".ct-section-inner-wrap {";
		css += "padding-top: "+$scope.globalSettings.sections["container-padding-top"] + $scope.globalSettings.sections["container-padding-top-unit"] + ";";
		css += "padding-left: "+$scope.globalSettings.sections["container-padding-left"] + $scope.globalSettings.sections["container-padding-left-unit"] + ";";
		css += "padding-right: "+$scope.globalSettings.sections["container-padding-right"] + $scope.globalSettings.sections["container-padding-right-unit"] + ";";
		css += "padding-bottom: "+$scope.globalSettings.sections["container-padding-bottom"] + $scope.globalSettings.sections["container-padding-bottom-unit"] + ";";
		css += "}";

		css += ".ct-new-columns > .ct-div-block {";
		css += "padding-top: "+$scope.globalSettings.columns["padding-top"] + $scope.globalSettings.columns["padding-top-unit"] + ";";
		css += "padding-left: "+$scope.globalSettings.columns["padding-left"] + $scope.globalSettings.columns["padding-left-unit"] + ";";
		css += "padding-right: "+$scope.globalSettings.columns["padding-right"] + $scope.globalSettings.columns["padding-right-unit"] + ";";
		css += "padding-bottom: "+$scope.globalSettings.columns["padding-bottom"] + $scope.globalSettings.columns["padding-bottom-unit"] + ";";
		css += "}";

		css += ".oxy-header-container {";
		css += "padding-left: "+$scope.globalSettings.sections["container-padding-left"] + $scope.globalSettings.sections["container-padding-left-unit"] + ";";
		css += "padding-right: "+$scope.globalSettings.sections["container-padding-right"] + $scope.globalSettings.sections["container-padding-right-unit"] + ";";
		css += "}";
		
		// loop all globals in case google fonts were added somewhere
		for(var sectionKey in $scope.globalSettings) { 
			if ($scope.globalSettings.hasOwnProperty(sectionKey)) {
				var section = $scope.globalSettings[sectionKey];
				for(var optionKey in section) { 
					if (section.hasOwnProperty(optionKey)) {
						if (optionKey == "font-family"||optionKey.indexOf("font-family")>-1) {
							$scope.loadWebFont(section[optionKey]);
						}
					}
				}				
			}
		}

		// WooCo Global Styles
		css += $scope.replaceGlobalWooSettings(CtBuilderAjax['wooGlobalStyles']);

		// output to head
        $scope.outputCSSStyles("oxygen-global-settings-styles", css);
	}


	/**
	 * Watch globalSettings changes and update global CSS styles
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */
	
	$scope.$watch('globalSettings', function(newVal, oldVal){
		$scope.updateGlobalSettingsCSS();
	}, true);

	
	/**
	 * Watch globalSettings.sections changes and update components CSS styles
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	$scope.$watch('globalSettings.sections', function(newVal, oldVal){
		$scope.pageSettingsUpdate();
		$scope.applyModelOptions();
		$scope.unsavedChanges();
	}, true);


	/**
	 * Watch globalSettings.woo changes and update WooCommerce global styles
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	$scope.$watch('globalSettings.woo', function(newVal, oldVal){
		$scope.updateAllComponentsCacheStyles();
		$scope.outputCSSOptions();
	}, true);
	

	/**
	 * Watch pageSettings
	 *
	 * @since 2.1
	 * @author Ilya K.
	 */

	$scope.$watch('pageSettingsMeta', function(newVal, oldVal){
		$scope.pageSettingsUpdate();
		$scope.unsavedChanges();
	}, true);


	/**
	 * Watch Page Settings > AOS
	 *
	 * @since 2.s
	 * @author Ilya K.
	 */

	$scope.$watch('pageSettingsMeta.aos', function(newVal, oldVal){
		$scope.updateAOS();
	}, true);


	/**
	 * Update global AOS object with merged Page and Global AOS settings
	 *
	 * @since 2.s
	 * @author Ilya K.
	 */

	$scope.updateAOS = function() {
		// filter before merge with global
		var filtered = angular.copy($scope.pageSettingsMeta.aos);
		for (var propName in filtered) { 
		    if (filtered[propName] === "" || filtered[propName] === undefined) {
		    	delete filtered[propName];
		    }
		}
		var merged = angular.extend({}, $scope.globalSettings.aos, filtered);

		if (merged['once']==="false") {
			merged['once']=false;
		}

		// filter empty values to keep real AOS defaults
		filtered = merged;
        for (var propName in filtered) { 
		    if (filtered[propName] === "" || filtered[propName] === undefined) {
		    	delete filtered[propName];
		    }
		}

		// finally merge with AOS defaults
		aosDefaults = {
		  offset: 120,
		  delay: 0,
		  easing: 'ease',
		  duration: 400,
		  disable: false,
		  once: false,
		};
		merged = angular.extend({}, aosDefaults, filtered);

		// update all AOS active components
		jQuery('[data-aos-enabled="true"]').each(function(){
			var id = jQuery(this).attr("ng-attr-component-id");
			$scope.setOption(id, $scope.component.options[id].name, 'aos-type');
		});

		// Update global AOS object
		AOS.init(merged);
	}


	/**
	 * Check balance beetwen brackets
	 *
	 * @since 1.1.0
	 * @author Ilya K.
	 */

	$scope.bracketsFailed = function(str){

		var removeComments = function(str){
	    	var re_comment = /(\/[*][^*]*[*]\/)|(\/\/[^\n]*)/gm;
		    return (""+str).replace( re_comment, "" );
		};
		
		var getOnlyBrackets = function(str){
		    //var re = /[^()\[\]{}]/g;
		    var re = /[^(){}]/g;
		    return (""+str).replace(re, "");
		};
		
		var areBracketsInOrder = function(str){
		    str = ""+str;
		    var bracket = {
		            "}": "{",
		            ")": "("
		        },
		        openBrackets = [], 
		        isClean = true,
		        i = 0,
		        len = str.length;

		    for(; i<len; i++ ){
		        if( bracket[ str[i] ] ){
		            isClean = ( openBrackets.pop() === bracket[ str[i] ] );
		            if (!isClean) {
		            	openBrackets.push( str[i] );
		            }
		        }else{
		            openBrackets.push( str[i] );
		        }
		    }
		    return ( openBrackets.length ) ? openBrackets[0] : false;
		    //return isClean && !openBrackets.length;
		};
	    
	    str = removeComments(str);
	    str = getOnlyBrackets(str);
	    return areBracketsInOrder(str);
	};

	
	/**
	 * Map component options to CSS propperties and selectors
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */
	
	$scope.mapCSSProperty = function(optionName, propertyName, selector) {

		if (!$scope.mappedSelectors[selector]) {
			$scope.mappedSelectors[selector]={};
		}
		
		if (!$scope.mappedSelectors[selector][propertyName]) {
			$scope.mappedSelectors[selector][propertyName]={};
		}
		
		$scope.mappedSelectors[selector][propertyName] = optionName;
	}


	/**
	 * Map component options to CSS propperties and selectors
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */
	
	$scope.unmapCSSProperty = function(propertyName, selector) {
			
		$scope.mappedSelectors[selector][propertyName] = null;
		delete $scope.mappedSelectors[selector][propertyName];
	}


	/**
	 * Output CSS styles based on mapped properties
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	$scope.getMappedCSS = function(componentSelector, options) {

		var style = "";

		for (var selector in $scope.mappedSelectors) {
						
			var selectorObj = $scope.mappedSelectors[selector];

			selector = selector.split(",").map(function(sel) {
			   return '#' + componentSelector+" "+sel;
			}).join(",");
			
			style += selector + "{";
			
			for (var property in selectorObj) {
				if( typeof(selectorObj[property]) === 'undefined' ||
					typeof(options[selectorObj[property]]) === 'undefined' ||
					options[selectorObj[property]].toString().trim() === ''
				) {
						continue;
				}
				var unit = "";
				if (options[selectorObj[property]+'-unit']!==undefined) {
					unit = options[selectorObj[property]+'-unit'];
				}

				if(property === 'background-image') {
					style += property + ": url(" + options[selectorObj[property]]+");";
				} else {
					style += property + ":" + $scope.getGlobalColorValue(options[selectorObj[property]]) + unit + ";";
				}
			}

			style += "}";
		}

		return style;
	}


	/**
	 * Generate Elements API registered selectors styles for given option:value list
	 *
	 * @since 3.2
	 * @author Ilya K.
	 */

	$scope.generateAllAPISelectorsCSS = function(currentSelector, stateOptions, isClass) {

		if ($scope.componentsTemplates !== undefined) {

			var selectorsCSS = "";

			// loop all registered selectors
		    for(var componentName in $scope.componentsTemplates) { 
				if ( $scope.componentsTemplates.hasOwnProperty(componentName)) {
				    selectorsCSS += $scope.generateAPISelectorsCSS(componentName, currentSelector, stateOptions, isClass)
				}
			}
			
			return selectorsCSS;
		}

		return "";
	}

	
	/**
	 * Generate Elements API registered selectors styles for given option:value list
	 *
	 * @since 3.2
	 * @author Ilya K.
	 */

	$scope.generateAPISelectorsCSS = function(componentName, currentSelector, stateOptions, isClass) {

		if ($scope.componentsTemplates[componentName]===undefined ||
			$scope.componentsTemplates[componentName]['registeredSelectors']===undefined) {
			return "";
		}

		var selectorsCSS = "",
			selectors = $scope.componentsTemplates[componentName]['registeredSelectors'],
			complexProperties = angular.copy($scope.componentsTemplates[componentName]['complexProperties']);

		// loop all registered selectors
		for(var key in selectors) { 
			if (selectors.hasOwnProperty(key)) {
				var selector = selectors[key],
					selectorCSS = "";

				if (selector) {

					// loop properties added for each selector
					for(var property in selector) { 
						if (selector.hasOwnProperty(property)) {

							if (!stateOptions) {
								continue;
							}
											
							// handle typography
							if (property=='typography'){
								selectorCSS += $scope.generateTypographyCSS(stateOptions,selector[property]);
							}

							else if (property=='box-shadow') {
								selectorCSS += $scope.generateBoxShadowCSS(stateOptions,selector[property]);
							}

							// complex selector i.e. box-shadow: {a} {b} {c} {d};
							// complex property syntax is 'box-shadow>a'
							else if ( property.indexOf('>') > -1 ) {
												
								// lets separate this into property and location: 'box-shadow' and 'a'
								var parts = property.split('>');
												
								if (complexProperties && complexProperties[parts[0]]) {
									var value = complexProperties[parts[0]];
									complexProperties[parts[0]] = value.replace("{"+parts[1]+"}", stateOptions[selector[property]]);
								}
							}

							// plain options
							else if (stateOptions[selector[property]]) {
								var value = $scope.addFontFamilyQuotes(property, stateOptions[selector[property]]);
								
								value = $scope.getGlobalColorValue(value);

								// hardcode this for now
								if (property=="transform:rotate") {
									property="transform";
									value="rotate("+value+")";
								}
								if (property=="background-image:url") {
									property="background-image";
									value="url("+value+")";
								}

								selectorCSS += property+":"+value+";";
							}
											
							// presets
							else {
								var unprefixedOptions = [];
								for(var option in stateOptions) { 
									if (stateOptions.hasOwnProperty(option)) {
										if (option.indexOf(selector[property])>-1) {
											// unprefix property from Element name and Preset name
											var unprefixedOption;
											unprefixedOption = option.replace(selector[property]+"_", "");
											var value = $scope.addFontFamilyQuotes(unprefixedOption, stateOptions[option]);
											unprefixedOptions[unprefixedOption] = value;
										}
									}
								}

								// handle background-position option
						        if ( unprefixedOptions['background-position-top'] || unprefixedOptions['background-position-left'] ) {

						            var top   = unprefixedOptions['background-position-top'] || "0%",
						                left  = unprefixedOptions['background-position-left'] || "0%";
						            
						            unprefixedOptions['background-position'] = left;
						            unprefixedOptions['background-position'] += " " + top;
						        	// remove fake properties
							        
							        delete unprefixedOptions['background-position-top'];
							        delete unprefixedOptions['background-position-left'];
						    	}

						        // handle background-size option
						        if ( unprefixedOptions['background-size'] == "manual" ) {

						            var width   = unprefixedOptions['background-size-width'] || "auto",
						                height  = unprefixedOptions['background-size-height'] || "auto";
						            
						            unprefixedOptions['background-size'] = width;
						            unprefixedOptions['background-size'] += " " + height;
	
							        // remove fake properties
							        delete unprefixedOptions['background-size-width'];
							        delete unprefixedOptions['background-size-height'];
						        }

								
								for(var option in unprefixedOptions) { 
									if (unprefixedOptions.hasOwnProperty(option)) {
										var value = unprefixedOptions[option];
										if (option=="background-image") {
											value="url("+value+")";
										}
										selectorCSS += option+":"+$scope.getGlobalColorValue(value)+";";
									}
								}
							}
						}

						// check if there are complex properties to add								
						for (var property in complexProperties) { 
							if (complexProperties.hasOwnProperty(property)) {

								// do not add property unless all parts replaced
								if (complexProperties[property].indexOf("{")>-1) {
									continue;
								}

								selectorCSS += property + ":" + $scope.getGlobalColorValue(complexProperties[property]) + ";";
							}
						}
					}
								
					if (selectorCSS) {
						selectorCSS = key + "{" + selectorCSS + "}";
						selectorsCSS += $scope.prefixCSSSelectors(selectorCSS, currentSelector, isClass);
					}

				}
			}
		}

		return selectorsCSS;
	}


	$scope.position_css = function($position, $forcein) {

		var output;

		for (var $prop in $position) {

			var $val = $position[$prop];

            if ($forcein === true) {
                if ($val !== null) {
                    $val = '0';
                }
            }

            if ($val !== null) {
                output = $prop+":"+$val+";";
            }

        }

        return output;
    }

	$scope.slide_position = function($direction, $distanceoverride) {

        var $distance = '100%',
        	$position = [],
        	$return = [];

        if ($distanceoverride !== undefined && $distanceoverride !== "px") {
            $distance = $distanceoverride;
        }

        switch ($direction) {
            case 'left':
                $position['left'] = '-'+$distance;
                break;
            case 'right':
                $position['left'] = ''+$distance;
                break;
            case 'top':
                $position['top'] = '-'+$distance;
                break;
            case 'bottom':
                $position['top'] = ''+$distance;
                break;
        }

        $return['out_css'] = $scope.position_css($position); // css for positioning the slide out of the superbox
        $return['in_css'] = $scope.position_css($position, true); // css for positinoing the slide in the superbox

        return $return;
    }

	
	/**
	 * Specificity Calculator
	 *
	 * https://github.com/keeganstreet/specificity
	 */

	$scope.SPECIFICITY = (function() {
		var calculate,
			calculateSingle,
			compare;

		// Calculate the specificity for a selector by dividing it into simple selectors and counting them
		calculate = function(input) {
			var selectors,
				selector,
				i,
				len,
				results = [];

			// Separate input by commas
			selectors = input.split(',');

			for (i = 0, len = selectors.length; i < len; i += 1) {
				selector = selectors[i];
				if (selector.length > 0) {
					results.push(calculateSingle(selector));
				}
			}

			return results;
		};

		/**
		 * Calculates the specificity of CSS selectors
		 * http://www.w3.org/TR/css3-selectors/#specificity
		 *
		 * Returns an object with the following properties:
		 *  - selector: the input
		 *  - specificity: e.g. 0,1,0,0
		 *  - parts: array with details about each part of the selector that counts towards the specificity
		 *  - specificityArray: e.g. [0, 1, 0, 0]
		 */
		calculateSingle = function(input) {
			var selector = input,
				findMatch,
				typeCount = {
					'a': 0,
					'b': 0,
					'c': 0
				},
				parts = [],
				// The following regular expressions assume that selectors matching the preceding regular expressions have been removed
				attributeRegex = /(\[[^\]]+\])/g,
				idRegex = /(#[^\s\+>~\.\[:]+)/g,
				classRegex = /(\.[^\s\+>~\.\[:]+)/g,
				pseudoElementRegex = /(::[^\s\+>~\.\[:]+|:first-line|:first-letter|:before|:after)/gi,
				// A regex for pseudo classes with brackets - :nth-child(), :nth-last-child(), :nth-of-type(), :nth-last-type(), :lang()
				pseudoClassWithBracketsRegex = /(:[\w-]+\([^\)]*\))/gi,
				// A regex for other pseudo classes, which don't have brackets
				pseudoClassRegex = /(:[^\s\+>~\.\[:]+)/g,
				elementRegex = /([^\s\+>~\.\[:]+)/g;

			// Find matches for a regular expression in a string and push their details to parts
			// Type is "a" for IDs, "b" for classes, attributes and pseudo-classes and "c" for elements and pseudo-elements
			findMatch = function(regex, type) {
				var matches, i, len, match, index, length;
				if (regex.test(selector)) {
					matches = selector.match(regex);
					for (i = 0, len = matches.length; i < len; i += 1) {
						typeCount[type] += 1;
						match = matches[i];
						index = selector.indexOf(match);
						length = match.length;
						parts.push({
							selector: input.substr(index, length),
							type: type,
							index: index,
							length: length
						});
						// Replace this simple selector with whitespace so it won't be counted in further simple selectors
						selector = selector.replace(match, Array(length + 1).join(' '));
					}
				}
			};

			// Replace escaped characters with plain text, using the "A" character
			// https://www.w3.org/TR/CSS21/syndata.html#characters
			(function() {
				var replaceWithPlainText = function(regex) {
						var matches, i, len, match;
						if (regex.test(selector)) {
							matches = selector.match(regex);
							for (i = 0, len = matches.length; i < len; i += 1) {
								match = matches[i];
								selector = selector.replace(match, Array(match.length + 1).join('A'));
							}
						}
					},
					// Matches a backslash followed by six hexadecimal digits followed by an optional single whitespace character
					escapeHexadecimalRegex = /\\[0-9A-Fa-f]{6}\s?/g,
					// Matches a backslash followed by fewer than six hexadecimal digits followed by a mandatory single whitespace character
					escapeHexadecimalRegex2 = /\\[0-9A-Fa-f]{1,5}\s/g,
					// Matches a backslash followed by any character
					escapeSpecialCharacter = /\\./g;

				replaceWithPlainText(escapeHexadecimalRegex);
				replaceWithPlainText(escapeHexadecimalRegex2);
				replaceWithPlainText(escapeSpecialCharacter);
			}());

			// Remove the negation psuedo-class (:not) but leave its argument because specificity is calculated on its argument
			(function() {
				var regex = /:not\(([^\)]*)\)/g;
				if (regex.test(selector)) {
					selector = selector.replace(regex, '     $1 ');
				}
			}());

			// Remove anything after a left brace in case a user has pasted in a rule, not just a selector
			(function() {
				var regex = /{[^]*/gm,
					matches, i, len, match;
				if (regex.test(selector)) {
					matches = selector.match(regex);
					for (i = 0, len = matches.length; i < len; i += 1) {
						match = matches[i];
						selector = selector.replace(match, Array(match.length + 1).join(' '));
					}
				}
			}());

			// Add attribute selectors to parts collection (type b)
			findMatch(attributeRegex, 'b');

			// Add ID selectors to parts collection (type a)
			findMatch(idRegex, 'a');

			// Add class selectors to parts collection (type b)
			findMatch(classRegex, 'b');

			// Add pseudo-element selectors to parts collection (type c)
			findMatch(pseudoElementRegex, 'c');

			// Add pseudo-class selectors to parts collection (type b)
			findMatch(pseudoClassWithBracketsRegex, 'b');
			findMatch(pseudoClassRegex, 'b');

			// Remove universal selector and separator characters
			selector = selector.replace(/[\*\s\+>~]/g, ' ');

			// Remove any stray dots or hashes which aren't attached to words
			// These may be present if the user is live-editing this selector
			selector = selector.replace(/[#\.]/g, ' ');

			// The only things left should be element selectors (type c)
			findMatch(elementRegex, 'c');

			// Order the parts in the order they appear in the original selector
			// This is neater for external apps to deal with
			parts.sort(function(a, b) {
				return a.index - b.index;
			});

			return {
				selector: input,
				specificity: '0,' + typeCount.a.toString() + ',' + typeCount.b.toString() + ',' + typeCount.c.toString(),
				specificityArray: [0, typeCount.a, typeCount.b, typeCount.c],
				parts: parts
			};
		};

		/**
		 * Compares two CSS selectors for specificity
		 * Alternatively you can replace one of the CSS selectors with a specificity array
		 *
		 *  - it returns -1 if a has a lower specificity than b
		 *  - it returns 1 if a has a higher specificity than b
		 *  - it returns 0 if a has the same specificity than b
		 */
		compare = function(a, b) {
			var aSpecificity,
				bSpecificity,
				i;

			if (typeof a ==='string') {
				if (a.indexOf(',') !== -1) {
					throw 'Invalid CSS selector';
				} else {
					aSpecificity = calculateSingle(a)['specificityArray'];
				}
			} else if (Array.isArray(a)) {
				if (a.filter(function(e) { return (typeof e === 'number'); }).length !== 4) {
					throw 'Invalid specificity array';
				} else {
					aSpecificity = a;
				}
			} else {
				throw 'Invalid CSS selector or specificity array';
			}

			if (typeof b ==='string') {
				if (b.indexOf(',') !== -1) {
					throw 'Invalid CSS selector';
				} else {
					bSpecificity = calculateSingle(b)['specificityArray'];
				}
			} else if (Array.isArray(b)) {
				if (b.filter(function(e) { return (typeof e === 'number'); }).length !== 4) {
					throw 'Invalid specificity array';
				} else {
					bSpecificity = b;
				}
			} else {
				throw 'Invalid CSS selector or specificity array';
			}

			for (i = 0; i < 4; i += 1) {
				if (aSpecificity[i] < bSpecificity[i]) {
					return -1;
				} else if (aSpecificity[i] > bSpecificity[i]) {
					return 1;
				}
			}

			return 0;
		};

		return {
			calculate: calculate,
			compare: compare
		};
	}());

});