/**
 * All Templates/Views related functions
 * 
 */

CTFrontendBuilder.controller("ControllerTemplates", function($scope, $parentScope, $timeout, $interval){

	$scope.replaceAfterReusable = false;
	$scope.template = {};
	$scope.template.postData = {};

	$scope.componentizeOptions = {};
	$scope.componentizeOptions.name 	= "Re-usable component";
	$scope.componentizeOptions.pageName = "Re-usable page";
	$scope.componentizeOptions.setName  = "My Design Set";
	$scope.componentizeOptions.status 	= "public";
	$scope.previewType = '';
	$scope.separatorAdded = false;

	
	/**
     * Load term data by term id
     * 
     * @since 0.3.3
     * @author Ilya K.
     */

	$scope.loadTemplatesTerm = function(id, type){
		$scope.previewType = type;
		// make AJAX call
		$scope.loadTemplateData($scope.setTemplateData, id);
	}
	
	

	/**
     * Callback to set post data
     * 
     * @since 0.2.0
     * @author Ilya K.
     */

	$scope.setTemplateData = function(data) {

		if ($scope.log) {
			console.log("setTemplateData()", data);
		}

		// update body classes if set in data
		if (undefined!==data.bodyClass) {
			jQuery("body").addClass(data.bodyClass).addClass("oxygen-builder-body");
		}
		
		if (undefined!==data.postData) {
			$scope.template.postData = data.postData;
		}

		// update edit link if set in data
		if (undefined!==data.edit_link) {
			$scope.template.postData['edit_link'] = data.edit_link;
		}

		if(undefined!==data.postsList || undefined!==data.termsList) {

			var sentPreviewRequest = false;

			if (undefined!==data.postsList) {

				if (data.postsList === null) {
					//alert("No posts found to preview");
				}
				else {
					$scope.template.postsList = data.postsList;
					
					// if default is set
					if(data['default']) {
						$scope.loadTemplatesTerm(data['default'].id, 'post');
						sentPreviewRequest = true;
					}
					else if(data.postsList[0]) {
						// load first post
						$scope.loadTemplatesTerm(data.postsList[0].id, 'post');
						sentPreviewRequest = true;
					}
				}
			}
			
			if (undefined!==data.termsList && data.termsList[0]) {
				
				$scope.template.termsList = data.termsList;

				// load first term
				if(!sentPreviewRequest) {
					$scope.loadTemplatesTerm(data.termsList[0].id, 'term');
					sentPreviewRequest = true;
				}
				
			}

			if(sentPreviewRequest) {
				return;
			}

		}

		if(data.meta_keys) {
			$parentScope.current_post_meta_keys = data.meta_keys;
		}

		// rebuild DOM
		// TODO: rebuild only code blocks??
		$scope.updateAttachmentSizes = true;
		$scope.rebuildDOM(0);
		$scope.classesCached = false;
		$scope.outputCSSOptions();
		$scope.waitOxygenTree(function(){
			$scope.updateAttachmentSizes = false;
		})
	}


	/**
     * Load Re-usable part via AJAX by it's ID
     * If componentId defined load only children into this component
     * 
     * @since 0.2.3
     * @author Ilya K.
     */

	$scope.loadReusablePart = function(viewPostId, componentId) {

		if (componentId !== undefined) {
			$scope.loadComponentsTree($scope.addReusableChildren, viewPostId, componentId);
		}
		else {
			$scope.loadComponentsTree($scope.addReusable, viewPostId, componentId);
		}		
	}


	/**
     * Insert "Re-usable part" Component to the Components Tree and rebuild the DOM
	 * 
     * @since 0.2.3
     * @author Ilya K.
     */

	$scope.addReusable = function(data, viewPostId) {
		
		var tree = {};

		// response from oxygen server
		if ( data[0] !== undefined && data[0]["content"] !== undefined ) {
			tree = data[0]["content"];
		}
		// response from user install
		else {
			tree = data;
		}

		if ($scope.log) {
			console.log("addReusable()", viewPostId);
		}

		var componentId = $scope.component.active.id;
		
		var reusable = {
			id: 0,
			name: "ct_reusable",
			options: {
				view_id: viewPostId
			}
		}

		var componentToInsert = $scope.getClosestPossibleInsert(componentId, tree, $scope.replaceAfterReusable);
		
		$scope.componentBuffer = reusable;
		$scope.componentInsertId = componentToInsert.id;

		if (componentToInsert.index >= 0) {
			$scope.newComponentKey = componentToInsert.index;
		}

		var insertedId = $scope.component.id;

		// update new element ids and selector
		$scope.updateNewComponentIds($scope.componentBuffer, componentToInsert);

		// paste reusable to current tree
		$scope.findComponentItem($scope.componentsTree.children, componentToInsert.id, $scope.pasteComponentToTree);

		if($scope.replaceAfterReusable) {

			$scope.removeComponentById($scope.replaceAfterReusable)
			$scope.replaceAfterReusable = false;
		}

		// disable undo option
        $scope.cancelDeleteUndo();

		// updates
		$scope.rebuildDOM(insertedId);
		$scope.activateComponent(insertedId);

		// scroll to the component after selector is parsed
        var timeout = $timeout(function() {
            $parentScope.scrollToComponent($scope.getActiveComponent().attr('id'));
            $timeout.cancel(timeout);
        }, 0, false);

		// init Tabs if any inside the reusable            
        if (oxygenVSBInitTabs!==undefined) {
	        var timeout2 = $timeout(function() {
		       	oxygenVSBInitTabs('#'+$scope.component.options[insertedId].selector);
	            $timeout.cancel(timeout2);
	        }, 500, false);
	        var timeout3 = $timeout(function() {
		       	oxygenVSBInitTabs('#'+$scope.component.options[insertedId].selector);
	            $timeout.cancel(timeout3);
	        }, 1500, false);
	    }

		$scope.unsavedChanges();
	}

	$scope.recursively_find_reusable = function(children) {

		_.forEach(children, function(item) {

			if(item.name == 'ct_reusable') {
				// try looking if this post already exists in the db, how?

				// import the post, store it in the db, and updates the IDS in the component
				var post_id = item.options['view_id'];
				
			}

			if(item['children']) {
				$scope.recursively_find_reusable(item['children']);
			}

		});
	
	}
	
	$scope.addPageFromSource = function(data, source, designSet) {
		
		var componentId = $scope.component.active.id;
		
		if(componentId < 0) {
			iframeScope.activateComponent(0, 'root');
			componentId = 0;
			iframeScope.currentClass = false;
		}

		var contents = JSON.parse(data);

		if(!contents || !contents.components || contents.components.length < 1) {
            $scope.showErrorModal(0, 'Could not load the page. '+(contents['error']?contents['error']:'Try again!'));
            return;
        }

		var components = false, classes = false;
		
		if(contents['components']) {
			components = contents['components'];
		}

		if(!components) {
			$scope.showErrorModal(0, "Page not loaded, try again");
			return;
		}

		if(contents['classes']) {
			classes = contents['classes'];
		}

		var globalColorsMap = {};

		if(contents['colors']) {
			globalColorsMap = $scope.mergeIncomingSourceColors(contents['colors'], designSet);
		}

		var lookupTable = {};
		if(contents['lookuptable']) {
			lookupTable = contents['lookuptable'];
		}

		if(classes) {

			// for all global color values in the classes, replace the id from globalColorsMap
			if(Object.keys(globalColorsMap).length > 0) {
				classes = $scope.ctMapSourceColors(classes, globalColorsMap);
			}

			// if data is from an old design set? then convert all static colors to variables
			if(CtBuilderAjax.classicDesignsets.find(function(item) { return item == designSet;})) {
				classes = $scope.ctCreateVariableColors(classes, designSet, lookupTable);
			}

			// all the classes are supposed to go under a folder that is named after the url of the source site.
			// if a folder with this name does not exists, create one
			if(!$scope.styleFolders.hasOwnProperty(designSet)) {
				$scope.styleFolders[designSet] = { status: 1, key: designSet};
			}
			
			for(key in classes) {
				classes[key]['parent'] = designSet;
			}

			$scope.classes = Object.assign(classes, $scope.classes);
	    	$scope.classesCached = false;
	    }
    	
    	var firstInsertedID = $scope.component.id;

    	_.each(components, function(component) {
    		
    		var insertedId = $scope.component.id;

    		// for all global color values in the component options, replace the id from globalColorsMap
			if(Object.keys(globalColorsMap).length > 0) {
				component = $scope.ctMapSourceColors(component, globalColorsMap);
			}

			// if data is from an old design set? then convert all static colors to variables
			if(CtBuilderAjax.classicDesignsets.find(function(item) { return item == designSet;})) {
				component = $scope.ctCreateVariableColors(component, designSet, lookupTable);
			}

    		var componentToInsert = { id: 0, index: iframeScope.getComponentById(componentId).index()+1};
    		//$scope.getClosestPossibleInsert(componentId, component);
    		
			$scope.componentBuffer = component;
			
			if (componentToInsert.index >= 0) {
				$scope.newComponentKey = componentToInsert.index;
			}

			// update new element ids and selector
	        $scope.updateNewComponentIds($scope.componentBuffer, componentToInsert);

			// paste loaded content to current tree
	    	$scope.findComponentItem($scope.componentsTree.children, componentToInsert.id, $scope.pasteComponentToTree);

	    	// disable undo option
	    	$scope.cancelDeleteUndo();

	    	// updates
	    	$scope.rebuildDOM(insertedId);
	    	$scope.updateComponentCacheStyles(insertedId, function() {
				// clear cache
	            $scope.cache.idCSS = "";

	            Object.keys($scope.cache.idStyles).map(function(key, index) {
	                $scope.cache.idCSS += $scope.cache.idStyles[key];
	            }); 
	            
	            $scope.outputCSSStylesAfterWait('id', $scope.cache.idCSS);
			});

			componentId = insertedId;
    	});

		$scope.rebuildDOM(0);
		
		// updates
		$scope.outputCSSOptions();

		$scope.activateComponent(firstInsertedID);

		var timeout = $timeout(function() {
			$parentScope.scrollToComponent($scope.getActiveComponent().attr('id'));
			$timeout.cancel(timeout);
		}, 0, false);

	}

	/**
	 * replace old global color IDs to the new stored ones, in the incoming data from a source site
	 * 
	 * @since 2.1
	 * @author Gagan S Goraya
	 */

	$scope.ctMapSourceColors = function (settings, globalColorsMap) {
   
		for(key in settings) {

			if(typeof(settings[key]) === 'string') {
				settings[key] = settings[key].replace(/color\((\d*)\)/g, function(matches, match) { return 'color('+globalColorsMap[parseInt(match)]+')'})
			}
			else if(settings[key] instanceof Array || settings[key] instanceof Object) {
				settings[key] = $scope.ctMapSourceColors(settings[key], globalColorsMap);
			}
		}

		return settings;

	}


	$scope.ctCreateVariableColorsCallback = function(match, designSet, lookupTable) { 

		// if a set with the source site's name does not exist, create one, if it exists, get its id
		var set = _.findWhere(iframeScope.globalColorSets['sets'], {name: designSet});

		if(typeof(set) === 'undefined') {
			set = {
				id : ++iframeScope.globalColorSets['setsIncrement'],
				name : designSet
			}
			
			iframeScope.globalColorSets['sets'].push(set);
		}

		var colorVal = match.toLowerCase();

		var nameFromLookupTable = false;

		if(lookupTable[colorVal]) {
			nameFromLookupTable = lookupTable[colorVal];
		}

		else if(lookupTable[colorVal.replace(/ /g, '')]) { // an rgb or rgba value with/out spaces
			nameFromLookupTable = lookupTable[colorVal.replace(/ /g, '')];
		}

		if(colorVal.length === 4) { // then it is a 3 letter hex color, convert it into 6
			colorVal = '#'+colorVal.substr(1).repeat(2);
			if(nameFromLookupTable === false && lookupTable[colorVal]) {
				nameFromLookupTable = lookupTable[colorVal];
			}
		}

		var existingColor;
		var firstColor = false;
		// check if the color with the same lookup table name exists and it belongs to the same set
		if(nameFromLookupTable !== false) {

			// find the first occurance of this name in the table
			_.each(lookupTable, function(item, key) {

				if(firstColor===false) {

					if(item == nameFromLookupTable) {
						firstColor = key;
					}
				}

				if(firstColor.length === 4) { // then it is a 3 letter hex color, convert it into 6
					firstColor = '#'+firstColor.substr(1).repeat(2);
				}

			})

			existingColor = _.findWhere(iframeScope.globalColorSets['colors'], {name: nameFromLookupTable, set: set['id']});

			if(typeof(existingColor) !== 'undefined') {
				if(firstColor !== colorVal) { // then change the color to match with the first occurance with the same name in the lookup table
					colorVal = firstColor;
					existingColor['value'] = firstColor;
				}
			}
		}

		// check if the color with the same value exists and it belongs to the same set
		if(typeof(existingColor) === 'undefined') {
			existingColor = _.findWhere(iframeScope.globalColorSets['colors'], {sourceVal: colorVal, set: set['id']});
		}

		if(typeof(existingColor) === 'undefined') {

			if(nameFromLookupTable == 'IGNORE') {
				return colorVal;
			}

			//create a new colors in this set
			existingColor = {
				id: ++iframeScope.globalColorSets['colorsIncrement'],
				name: nameFromLookupTable?nameFromLookupTable:'color #'+iframeScope.globalColorSets['colorsIncrement'],
				value: colorVal, // this color value will be used to render the components
				sourceVal: colorVal, // this will keep a record of the original color value that this name was assigned to
				set: set['id']
			}
			
			iframeScope.globalColorSets['colors'].push(existingColor);

		}

		return 'color('+existingColor['id']+')';

	}

	/**
	 * create color variables out of static colors in the provided data
	 * 
	 * @since 2.1
	 * @author Gagan S Goraya
	 */

	$scope.ctCreateVariableColors = function (settings, designSet, lookupTable ) {
   
		for(key in settings) {

			if(typeof(settings[key]) === 'string') {
				settings[key] = settings[key].replace(/#[0-9|a-f]{3,6}|rgba\([\d|\s]*\,[\d|\s]*\,[\d|\s]*\,[^\)]*\)/ig, function(match) {
						return $scope.ctCreateVariableColorsCallback(match, designSet, lookupTable)
					}
				)
			}
			else if(settings[key] instanceof Array || settings[key] instanceof Object) {
				settings[key] = $scope.ctCreateVariableColors(settings[key], designSet, lookupTable);
			}
		}

		// find the set id
		var set = _.find(iframeScope.globalColorSets['sets'], {name: designSet});

		if(typeof(set) === 'undefined') {
			return settings;
		}

		var sortedColors = [];

		// reorder global colors as per the lookup table
		_.each(lookupTable, function(name) {

			var found = _.find(iframeScope.globalColorSets['colors'], {set: set['id'], name: name });
			var index;

			if(found) {
				index = iframeScope.globalColorSets['colors'].indexOf(found);
			}

			if(typeof(index) !== 'undefined') {
				sortedColors.push(found);
				iframeScope.globalColorSets['colors'].splice(index, 1);
			}

		})

		iframeScope.globalColorSets['colors'] = sortedColors.concat(iframeScope.globalColorSets['colors']);

		return settings;

	}

	/**
	 * merge incoming global colors from a source site
	 * 
	 * @since 2.1
	 * @author Gagan S Goraya
	 */

	$scope.mergeIncomingSourceColors = function(colors, designSet) {

		// merge incoming colors into existing global colors and generate a map of incoming id against new id
		var globalColorsMap = {};

		// set Name for the incoming colors
		// if a set with the source site's name does not exist, create one, if it exists, get its id
		var set = _.findWhere(iframeScope.globalColorSets['sets'], {name: designSet});

		if(typeof(set) === 'undefined') {
			set = {
				id : ++iframeScope.globalColorSets['setsIncrement'],
				name : designSet
			}
			
			iframeScope.globalColorSets['sets'].push(set);
		}


		for(key in colors) {
			// check if the color with the same name exists and it belongs to the same set
			var existingColor = _.findWhere(iframeScope.globalColorSets['colors'], {name: colors[key]['name'], set: set['id']});

			if(typeof(existingColor) !== 'undefined') {
				globalColorsMap[parseInt(key)] = parseInt(existingColor['id']);
			}
			else {
				//create a new colors in this set
				iframeScope.globalColorSets['colors'].push({
					id: ++iframeScope.globalColorSets['colorsIncrement'],
					name: colors[key]['name'],
					value: colors[key]['value'],
					set: set['id'],
				});

				globalColorsMap[parseInt(key)] = parseInt(iframeScope.globalColorSets['colorsIncrement']);
			}
		}

		return globalColorsMap;
	}

	$scope.addComponentFromSource = function(data, componentId, source, designSet) {

		if (componentId == undefined || !componentId) {
			componentId = $scope.component.active.id;
		}

		if(componentId < 0) {
			iframeScope.activateComponent(0, 'root');
			componentId = 0;
			iframeScope.currentClass = false;
		}

		var contents = JSON.parse(data);
		
        if(!contents || !contents.component || contents.component.length < 1) {
            $scope.showErrorModal(0, 'Could not load the component. '+(contents['error']?contents['error']:'Try again!'));
            return;
        }

		var component = false, classes = false;

		if(contents['component']) {
			component = contents['component'];
		}

		if(!component) {
			$scope.showErrorModal(0, "Component not loaded, try again");
			return;
		}
		
		if(contents['classes']) {
			classes = contents['classes'];
		}

		var globalColorsMap = {};
		if(contents['colors']) {
			globalColorsMap = $scope.mergeIncomingSourceColors(contents['colors'], designSet);
		}

		var lookupTable = {};
		if(contents['lookuptable']) {
			lookupTable = contents['lookuptable'];
		}
	
		// for all global color values in the component options, replace the id from globalColorsMap
		if(Object.keys(globalColorsMap).length > 0) {
			component = $scope.ctMapSourceColors(component, globalColorsMap);
		}

		// if data is from an old design set? then convert all static colors to variables
		if(CtBuilderAjax.classicDesignsets.find(function(item) { return item == designSet;})) {
			component = $scope.ctCreateVariableColors(component, designSet, lookupTable);
		}

		// if the component uses any re-usable items, find if those already exist in the db, if not, pull them to the db and update the post IDS and stuff
		// if(component['children']) {
		// 	$scope.recursively_find_reusable(component['children']);
		// 	console.log(component);
		// }

		var insertedId = $scope.component.id;
		
		var componentToInsert = $scope.getClosestPossibleInsert(componentId, component);

		$scope.componentBuffer = component;
		
		if (componentToInsert.index >= 0) {
			$scope.newComponentKey = componentToInsert.index;
		}

		

		if(classes) {

			// for all global color values in the classes, replace the id from globalColorsMap
			if(Object.keys(globalColorsMap).length > 0) {
				classes = $scope.ctMapSourceColors(classes, globalColorsMap);
			}

			// if data is from an old design set? then convert all static colors to variables
			if(CtBuilderAjax.classicDesignsets.find(function(item) { return item == designSet;})) {
				classes = $scope.ctCreateVariableColors(classes, designSet, lookupTable);
			}

			// all the classes are supposed to go under a folder that is named after the url of the source site.
			// if a folder with this name does not exists, create one
			if(designSet && !$scope.styleFolders.hasOwnProperty(designSet)) {
				$scope.styleFolders[designSet] = { status: 1, key: designSet};
			}

			for(key in classes) {
				classes[key]['parent'] = designSet;
			}

	    	$scope.classes = Object.assign(classes, $scope.classes);
	    	
	    	$scope.classesCached = false;
	    }

		// update new element ids and selector
        $scope.updateNewComponentIds($scope.componentBuffer, componentToInsert);

		// paste loaded content to current tree
    	$scope.findComponentItem($scope.componentsTree.children, componentToInsert.id, $scope.pasteComponentToTree);

    	// disable undo option
    	$scope.cancelDeleteUndo();

    	// updates
    	$scope.rebuildDOM(insertedId);
    	$scope.updateComponentCacheStyles(insertedId, function() {
			 // clear cache
            $scope.cache.idCSS = "";

            Object.keys($scope.cache.idStyles).map(function(key, index) {
                $scope.cache.idCSS += $scope.cache.idStyles[key];
            }); 
            
            $scope.outputCSSStylesAfterWait('id', $scope.cache.idCSS);
		});
	

		// updates
		$scope.outputCSSOptions();

		$scope.activateComponent(insertedId);

		var timeout = $timeout(function() {
			$parentScope.scrollToComponent($scope.getActiveComponent().attr('id'));
			$timeout.cancel(timeout);
		}, 0, false);

	}

	/**
     * Insert "Re-usable part" chidlren to the Components Tree and build each child DOM node
     * 
     * @since 0.2.3
     * @author Ilya K.
     */

	$scope.addReusableChildren = function(data, viewPostId, componentId) {

		if (componentId == undefined) {
			componentId = $scope.component.active.id;
		}

		if ($scope.log) {
			console.log("addReusableChildren()", data, viewPostId, componentId);
		}
		
		var tree = {};

		// response from oxygen server
		if ( data[0] !== undefined && data[0]["content"] !== undefined ) {
			// do nothing
		}
		// response from user install
		else {
			tree = data;
		}

        var componentToInsert = $scope.getClosestPossibleInsert(componentId, tree),
        	firstInsertedID = -1;

		$scope.addReusableChild(tree.children, 0, firstInsertedID, componentToInsert);
		
		// reset key (position where to insert element) after reusable is added
		$scope.newComponentKey = -1;
	}


	$scope.addReusableChild = function(children, key, firstInsertedID, componentToInsert) {

		if (children.length==0){
			
			$scope.activateComponent(firstInsertedID);
			
			// scroll to the component after selector is parsed
			var timeout = $timeout(function() {
				$parentScope.scrollToComponent($scope.getActiveComponent().attr('id'));
				$timeout.cancel(timeout);
			}, 0, false);

			// updates
			$scope.outputCSSOptions();

			// hide dialog widnow for API
			$parentScope.hideDialogWindow();
			
			return;
		}

		var insertedId = $scope.component.id;

		if (firstInsertedID<0) {
			firstInsertedID = insertedId;
		}
		
		$scope.componentBuffer = children[0];
		
		if (componentToInsert.index >= 0) {
			$scope.newComponentKey = componentToInsert.index + key;
		}

		// update new element ids and selector
		$scope.updateNewComponentIds($scope.componentBuffer, componentToInsert);
	
		// paste loaded content to current tree
		$scope.findComponentItem($scope.componentsTree.children, componentToInsert.id, $scope.pasteComponentToTree);

		// disable undo option
		$scope.cancelDeleteUndo();

		// updates
		$scope.rebuildDOM(insertedId);
		$scope.updateComponentCacheStyles(insertedId, function() {
			 // clear cache
			$scope.cache.idCSS = "";

			Object.keys($scope.cache.idStyles).map(function(key, index) {
				$scope.cache.idCSS += $scope.cache.idStyles[key];
			}); 
			
			$scope.outputCSSStylesAfterWait('id', $scope.cache.idCSS);
		});

		// init Tabs if any inside the reusable            
		if (oxygenVSBInitTabs!==undefined) {
			var timeout2 = $timeout(function() {
				oxygenVSBInitTabs('#'+$scope.component.options[insertedId].selector);
				$timeout.cancel(timeout2);
			}, 500, false);
			var timeout3 = $timeout(function() {
				oxygenVSBInitTabs('#'+$scope.component.options[insertedId].selector);
				$timeout.cancel(timeout3);
			}, 1500, false);
		}

		var counter = 0;

		function waitOxygenTreeReusableTimeout(counter) {
			counter++;
			setTimeout(function(){
				waitOxygenTreeReusable(counter);
			}, 100);
		}

		function waitOxygenTreeReusable(counter) {

			if ( $scope.buildingOxygenTreeCounter > 0 && counter < 900) {
				// keep waiting tree to be built while buildComponentsFromTree() in progress
				waitOxygenTreeReusableTimeout(counter);
			}
			else {
				// increment id after buildComponentsFromTree() is completed
				$scope.addReusableChild(children.splice(1), key+1, firstInsertedID, componentToInsert)
			}

			// buildComponentsFromTree() took over 90s (100ms x 900) probably due to slow AJAX elements loading
			if ( $scope.buildingOxygenTreeCounter > 0 && counter >= 900) {
				console.log('Tree building timeout. ID counter is not incremented.');
			}
		}

		waitOxygenTreeReusable(counter);
	}


	/**
     * Insert "Re-usable part" Content to the DOM
     * 
     * @since 0.2.3
     * @author Ilya K.
     */

	$scope.addReusableContent = function(data, componentId) {

		if (!data) {
			return;
		}

		if ($scope.log) {
			console.log("addReusableContent()", data);
		}

		if(data['edit_link']) {
			$scope.reusableEditLinks[componentId] = data['edit_link'];
		}
		
		var componentToInsert = $scope.getComponentById(componentId);

		if (componentToInsert) {
			componentToInsert.append(data.post_content);			

			// replace dnd-type attribute from "ct-reusable" to original type
			var contentObject = jQuery(data.post_content);

			if(contentObject) {

				var classList = [];
				if(contentObject.attr("class")) {
					classList = contentObject.attr("class").split(/\s+/);
				}

				for(i = 0; i < classList.length; ++i) {
				    if (classList[i].indexOf("ct-") >= 0) {

				        var type = classList[i].replace(/\-/g, '_');
				        $scope.component.options[componentId].dndtype = type;
				        
				    	break;
				    }
				}
			}
		}

		// holder for reusable CSS
		$scope.reusableCSS 			= {};
		$scope.reusableCSS.styles 	= "";

		// add this item CSS
		if (data.post_tree) {
			$scope.generateTreeCSS(data.post_tree, $scope.reusableCSS, function() {
				// output CSS styles generated for all reusable's items
				$scope.outputCSSStyles("ct-re-usable-styles-"+data.ID, $scope.reusableCSS.styles);		
			});
		}

		
	}


	/**
	 * Get closest possibly nestable for re-usable part component ID and children count
	 *
	 * @since 0.2.3
	 */
	
	 $scope.getClosestPossibleInsert = function(componentId, tree, atleastParent) {

	 	if ($scope.log) {
			console.log("getClosestPossibleInsert()", componentId, tree);
		}

	 	if (undefined == componentId) {
	 		return {};
	 	}

	 	var componentToInsertIn = $scope.getComponentById(componentId);
	 		insertData = {
	 			componentToInsertIn : componentToInsertIn,
				componentInsertId : 0,
				parentId : 0,
				index : (componentId > 0 ) ? componentToInsertIn.index() + 1 : -1
			},
			i = 0;

		var isNestable 	= insertData.componentToInsertIn[0].attributes['is-nestable'],
			isReusable	= insertData.componentToInsertIn[0].classList.contains("ct_reusable");

		var treeJSON 	= JSON.stringify(tree),
			hasSection 	= (treeJSON.indexOf('"name":"ct_section"') > -1),
			hasLink 	= (treeJSON.indexOf('"name":"ct_link"') > -1 || 
						   treeJSON.indexOf('"name":"ct_link_text"') > -1);
		
		function findClosestParent(insertData, className) {

			var parent 		= insertData.componentToInsertIn,
				parentId 	= insertData.parentId;

			while ( !parent.hasClass(className) && !parent.hasClass('ct-builder') ) {
				parent = parent.parent();
			}

			insertData.parentId = parent[0].getAttribute('ng-attr-component-id');

			// found component in parents
			if (insertData.parentId != 0) {

				insertData.index = parent.index() + 1;

			    // go level up from component
			    insertData.componentToInsertIn = jQuery(parent).parent().closest("[is-nestable]");
			    insertData.componentInsertId   = insertData.componentToInsertIn[0].getAttribute('ng-attr-component-id');
			}
			else {
				// set previous parent back
				insertData.parentId = parentId;
			}
		}

		// search for section in parents
		if (hasSection) {
			findClosestParent(insertData, "ct_section");
		}

		// search for link wrapper in parents
		if (hasLink) {
			findClosestParent(insertData, "ct_link");
		}

		if (insertData.parentId == 0) {

			//console.log(componentToInsertIn, isNestable, isReusable);
		
			// adding to nestable component
			if ( isNestable && !isReusable && !atleastParent ) {
			    insertData.componentInsertId = componentId;
			    insertData.index = -1
			}
			// find nestable parent
			else {

				while ( ((!isNestable || atleastParent) || isReusable) && i < 10 ) {
				
			        insertData.componentToInsertIn = jQuery(insertData.componentToInsertIn).parent().closest("[is-nestable]");

					if ( insertData.componentToInsertIn ) {
						isNestable 	= insertData.componentToInsertIn[0].attributes['is-nestable'];
						isReusable	= insertData.componentToInsertIn[0].classList.contains("ct_reusable");
			        	insertData.componentInsertId = insertData.componentToInsertIn[0].getAttribute('ng-attr-component-id');
			        }

			        if(isNestable)
			        	atleastParent = false;
			        // prevent infinite loop
			        i++;
				}
			}
		}

        return {
        	id: insertData.componentInsertId,
        	index: insertData.index,
        }
	}


	/**
     * Check if component can be componentized as Re-usable part
     * 
     * @since 0.2.3
     * @author Ilya K.
     */

	$scope.isCanComponentize = function(id, name) {

		var exclude = ['ct_reusable','oxy_header_row','ct_slide','oxy_tab','oxy_tab_content']

		if (exclude.indexOf(name)>-1) {
			return false;
		}

		if ( id > 0 ) {
			return true;
		}
		else {
			return false;
		}
	}


	/**
	 * Send Tree node to save as View CPT
	 * 
	 * @since 0.2.3
	 * @author Ilya K.
	 */
	
	$scope.saveReusable = function(id, saveToBlock) {

		if( typeof saveToBlock === 'undefined') saveToBlock = false;

		$scope.cancelDeleteUndo();

		if (undefined === id) {
			id = $scope.component.active.id;
		}

		var promptTitle = saveToBlock ? "New Block Name" : "New Re-Usable Name";

		var name = prompt(promptTitle, $scope.componentizeOptions.name);
        
		if (name!=null) {

			$parentScope.showLoadingOverlay("saveReusable()");

			$scope.componentizeOptions.name = name;

			// choose components to componentize
			if ( $scope.isSelectableEnabled && $scope.isDOMNodesSelected ) {
				
				var parent      = jQuery("#ct-dom-tree").find('.ct-selected-dom-node').first().parent().parent(),
					nodes       = parent.children('.ct-dom-tree-node').has('.ct-selected-dom-node'),
					ids         = [];

				// get top level selected component ids
				nodes.each(function(){
					ids.push(jQuery(this).attr('ng-attr-tree-id')); 
				});

				$scope.selectedComponents = [];
				
				// add all components from tree
				for (var i = 0, id; id = ids[i], i <= ids.length - 1; i++) {
					$scope.findComponentItem($scope.componentsTree.children, id, $scope.addSelectedComponent);
				}
				
				$scope.saveComponentAsView(null, $scope.selectedComponents, saveToBlock);
			}
			else {
				// save component as View CPT
				$scope.findComponentItem($scope.componentsTree.children, id, $scope.saveComponentAsView, saveToBlock);
			}
		}
		else {
			alert("Name can't be empty");
		}
	}


	/**
	 * Replace components saved as re-usable with actual Re-usable component
	 * 
	 * @since 1.0.1
	 * @author Ilya K.
	 */
	
	$scope.replaceReusablePart = function(id, post_id) {
		
		$scope.replaceAfterReusable = id;

		$scope.loadReusablePart(post_id);
	};


	/**
	 * Show componentize dialog
	 * 
	 * @since 0.4.0
	 * @author Ilya K.
	 */
	
	$scope.showComponentize = function(id) {
		
		$parentScope.showDialogWindow();
		$scope.componentizeOptions.id = id;
		$parentScope.dialogForms['showComponentizeForm'] = true;
	}


	/**
	 * Send Tree node to save on server
	 * 
	 * @since 0.2.3
	 * @author Ilya K.
	 */
	
	$scope.componentize = function() {

		// TODO: add validation here to not send request to the server

		// choose components to componentize
		if ( $scope.isSelectableEnabled && $scope.isDOMNodesSelected ) {
			
			var parent      = jQuery("#ct-dom-tree").find('.ct-selected-dom-node').first().parent().parent(),
				nodes       = parent.children('.ct-dom-tree-node').has('.ct-selected-dom-node'),
				ids         = [];

			// get top level selected component ids
			nodes.each(function(){
				ids.push(jQuery(this).attr('ng-attr-tree-id'));
			});

			$scope.selectedComponents = [];
			
			// add all components from tree
			for (var i = 0, id; id = ids[i], i <= ids.length - 1; i++) {
				$scope.findComponentItem($scope.componentsTree.children, id, $scope.addSelectedComponent);
			}
			
			$scope.postComponentize(null, $scope.selectedComponents);
		}
		else {
			if ($scope.componentizeOptions.idToUpdate>0) {
				// update existed component
				$scope.componentizeOptions.assetId = null;
				$scope.findComponentItem($scope.componentsTree.children, $scope.componentizeOptions.id, $scope.postComponentize);
				$scope.componentizeOptions.id = null;
			}
			else {
				// post new component
				$scope.postAsset($scope.componentizeOptions.screenshot, function(){
					$scope.findComponentItem($scope.componentsTree.children, $scope.componentizeOptions.id, $scope.postComponentize);
					$scope.componentizeOptions.id = null;
				});
			}
		}

		$parentScope.hideDialogWindow();
	}


	/**
	 * Show componentize dialog for page
	 * 
	 * @since 0.4.0
	 * @author Ilya K.
	 */
	
	$scope.showPageComponentize = function(id) {
		
		$parentScope.showDialogWindow();
		$parentScope.dialogForms['showPageComponentizeForm'] = true;
	}


	/**
	 * Send whole Components Tree to server
	 * 
	 * @since 0.4.0
	 * @author Ilya K.
	 */

	$scope.tryPageComponentize = function() {

		// post component to server
		$scope.postAsset($scope.componentizeOptions.screenshot, function(){
			$scope.pageComponentize();
		});

		$parentScope.hideDialogWindow();
	}


	/**
	 * Simply adds one component to scope variable to save selectaed as componentize
	 * 
	 * @since 0.2.4
	 */

	$scope.addSelectedComponent = function(key, component) {

		$scope.selectedComponents.push(component);
	}


	/**
	 * Add widget component
	 * 
	 * @since 0.2.3
	 */
	
	$scope.addWidget = function(className, idBase, prettyName) {

		var widgetId = $scope.component.id;

		$scope.addComponent('ct_widget', 'widget');

		// update class name
		$scope.component.options[widgetId]['model']['class_name'] = className;
		$scope.setOption(widgetId, "ct_widget", "class_name");

		// update id base
		$scope.component.options[widgetId]['model']['id_base'] = idBase;
		$scope.setOption(widgetId, "ct_widget", "id_base");

		// update niceName
		$scope.component.options[widgetId]['model']['pretty_name'] = prettyName;
		$scope.setOption(widgetId, "ct_widget", "pretty_name");

		$scope.component.options[widgetId]['nicename'] = prettyName + " (#"+widgetId+")";
		$scope.updateFriendlyName(widgetId);

		//$scope.rebuildDOM(widgetId);
	}


	/**
	 * Add sidebar component
	 * 
	 * @since 2.0
	 */
	
	$scope.addSidebar = function(id) {

		var sidebarId = $scope.component.id;

		$scope.addComponent('ct_sidebar', 'sidebar');

		// update class name
		$scope.setOptionModel("sidebar_id", id, sidebarId, "ct_sidebar");

		$scope.rebuildDOM(sidebarId);
	}


	/**
	 * Load widget form with options
	 * 
	 * @since 0.2.3
	 */
	
	$scope.loadWidgetForm = function() {

		$scope.renderWidget($scope.component.active.id, true);
	}


	/**
	 * Apply widget options and render
	 * 
	 * @since 0.2.3
	 */

	$scope.applyWidgetInstance = function(id) {

		if ($scope.log) {
			console.log("applyWidgetInstance()",id);
		}

		if (!id) {
			id = $scope.component.active.id;
		}

		var widgetOptions = $scope.component.options[id];
		
		if (widgetOptions.name != "ct_widget") {
			return false;
		}
		
		var values = {},
			inputs = jQuery('#ct-widget-form',window.parent.document).serializeArray(),

			// get base
			idBase = widgetOptions['original']['id_base'];

		// add uncheked checkboxes
		var checkboxes = jQuery('#ct-widget-form input:checkbox',window.parent.document).map(function() {
			if ( !this.checked )
				return { name: this.name, value: this.checked ? this.value : false };
		});

		for (i = 0; i < checkboxes.length; i++) {
			inputs.push(checkboxes[i]);
		}

		jQuery.each(inputs, function(i, field) {

			// remove widget-id_base[1][...]
			var name = field.name.replace(idBase, "");
			//console.log(name);
			name = name.replace(/widget-\[\d\]\[/, "");
			//console.log(name);
			name = name.replace("]", "");
			//console.log(name);

			// Parse multidimensional names
			if (name.indexOf('[') > -1) {

				var newName = name.replace(/\[(.*)\]/, ''),
					key  	= name.replace(newName, ''),
					keys	= [],
					matches = key.match(/\[[^\]]*\]/g);

				var parseNames = function(object, value, head, tail) {
					if (tail.length == 0) {
						object[head] = value;
					} else {
						if('undefined' == typeof object[head] ) {
							object[head] = {};
						}
						parseNames(object[head], value, tail.shift(), tail);
					}
				}

				for (j = 0; j < matches.length; j++) {
					if ('[]' == matches[j])
						continue;
					keys.push(matches[j].replace( /\[|\]/g, '' ));
				}

				if (keys.length > 0) {
					var keysCopy = keys.slice();
					if ('undefined' == typeof values[newName]) {
						values[newName] = {};
					}
					parseNames(values[newName], field.value, keysCopy.shift(), keysCopy);
				} else {
					if ('undefined' == typeof values[newName]) {
						values[newName] = [];
					}
					values[newName].push(field.value);
				}
			}
			// Regular names
			else {
				if (name!=="")
					values[name] = field.value;
			}
		});

		// update model and Components Tree
		$scope.component.options[$scope.component.active.id]['model']['instance'] = values;
		$scope.setOption($scope.component.active.id, "ct_widget", "instance");

		// render widget with new options
		$scope.renderWidget($scope.component.active.id);
	}


	/**
	 * Show dialog with a form for HTML component
	 * 
	 * @since 0.2.3
	 */

	$scope.loadHTMLForm = function() {
		
		$parentScope.showDialogWindow();

		angular.element("#ct-html-component-form").val($scope.component.options[$scope.component.active.id]['model']['html']);
	}


	/**
	 * Apply all code block parts: PHP, JS, CSS
	 * 
	 * @since 0.3.1
	 * @author Ilya K.
	 */

	$scope.applyCodeBlock = function(componentId, updateTree) {

		if ($scope.log) {
			console.log("applyCodeBlock()", componentId, updateTree);
		}

		$scope.applyCodeBlockPHP(componentId, updateTree);
		$scope.applyCodeBlockJS(componentId, updateTree);
		$scope.applyCodeBlockCSS(componentId, updateTree);
	}


	/**
	 * Get executed code block PHP
	 * 
	 * @since 0.3.1
	 * @author Ilya K.
	 */

	$scope.applyCodeBlockPHP = function(componentId, updateTree) {

		if (componentId === undefined) {
			componentId = $scope.component.active.id;
		}

		if (updateTree === undefined) {
			updateTree = true;
		}

		if ($scope.log) {
			console.log("applyCodeBlockPHP()", componentId);
		}

		// update Tree
		if (updateTree) {
			$scope.setOption(componentId, 'ct_code_block', 'code-php', false, false);
		}
			
		var selector 	= "#" + $scope.component.options[componentId]['selector'],
			code 		= $scope.getOption('code-php', componentId);
		
		$scope.execCode(code, selector, $scope.insertElementContent);
	}


	/**
	 * Get executed code block JS
	 * 
	 * @since 0.3.1
	 * @author Ilya K.
	 */

	$scope.applyCodeBlockJS = function(componentId, updateTree) {

		$scope.applyingComponentJS = true;
        jQuery(".oxygen-code-error-container", $parentScope.oxygenUIElement).hide().html("");

		if (componentId === undefined) {
			componentId = $scope.component.active.id;
		}

		if (updateTree === undefined) {
			updateTree = true;
		}

		if ($scope.log) {
			console.log("applyCodeBlockJS()", componentId, updateTree);
		}

		// update Tree
		if (updateTree) {
			$scope.setOption(componentId, 'ct_code_block', 'code-js', false, false);
		}
		
		// output to DOM
		code = $scope.getOption('code-js', componentId);
		$scope.outputJSScript("js-code-", componentId, code)

		$scope.applyingComponentJS = false;
	}


	/**
	 * Get executed code block CSS
	 * 
	 * @since 0.3.1
	 * @author Ilya K.
	 */

	$scope.applyCodeBlockCSS = function(componentId, updateTree) {

		if (componentId === undefined) {
			componentId = $scope.component.active.id;
		}

		if (updateTree === undefined) {
			updateTree = true;
		}

		if ($scope.log) {
			console.log("applyCodeBlockCSS()", componentId, updateTree);
		}

		// update Tree
		if (updateTree) {
			$scope.setOption(componentId, 'ct_code_block', 'code-css', false, false);
		}

		// output to <head>
		code = $scope.getOption('code-css', componentId);
		
		var selector = $scope.component.options[componentId]['selector'];
    	code = code.replace(new RegExp("%%ELEMENT_ID%%", 'g'), selector);
    	code = $scope.replaceGlobalColors(code);

		$scope.outputCSSStyles("ct_code_block_css_"+componentId, code);
	}


	/**
     * Append <script> element to DOM with passed code
     *
     * @since 0.3.1
     * @author Ilya K.
     */
    
    $scope.outputJSScript = function(name, id, code) {

    	if ($scope.log){
    		console.log("outputJSScript()", name, id)
    	}

    	// replace %%ELEMENT_ID%% with actual id attribute
    	var selector = $scope.component.options[id]['selector'];

    	code = code.replace(new RegExp("%%ELEMENT_ID%%", 'g'), selector);
    	
    	// remove old script
    	var oldScript = document.getElementById(name+id);
    	oldScript && document.body.removeChild(oldScript);
    	
    	// create new script
    	var script = document.createElement('script');
		script.type = 'text/javascript';
		script.setAttribute("id", name+id);

		try {
			script.appendChild(document.createTextNode(code));
			document.body.appendChild(script);
		} catch (e) {
			script.text = code;
			document.body.appendChild(script);
		}
    }

    /**
     * Add window.onerror function to output errors as notice in JS tab
     * 
     * @since 1.0.1
     * @author Ilya K.
     */
    
    $scope.setupJSErrorNotice = function() {

    	window.onerror = function oxygenErrorHandler(errorMsg, url, lineNumber) {
            if ( $scope.applyingComponentJS ) {
                jQuery(".oxygen-code-error-container", $parentScope.oxygenUIElement).show().html(errorMsg+" on line #"+lineNumber);
            }
            else {
				if (typeof url == "object") {
					return;
				}
            	// don't show AOS selector error, it doesn't affect the builder in any way
            	if (url.indexOf !== undefined && url.indexOf("/component-framework/vendor/aos/aos.js")>-1) {
            		return;
            	}
				// don't show MetaBox video caused error, it doesn't affect the builder in any way
				if (errorMsg.indexOf('ResizeObserver loop limit exceeded') >= 0) {
					return;
				}
				// don't show errors caused by 3rd party plugins within AJAX rendered elements
				if ($scope.isRenderingAJAXElement) {
					return;
				}
            	$scope.showNoticeModal("<div>"+errorMsg+" on line #"+lineNumber+" in "+url+"</div>");
            }
        }
    }


	/**
     * Insert text/html content to any HTML element by selector
     *
     * @since 0.2.4
     * @author Ilya K.
     */
    
    $scope.insertElementContent = function(result, placholderSelector, isUI) {

    	if ($scope.log) {
    		console.log("insertElementContent()", result, placholderSelector)
    	}

		var component = angular.element(placholderSelector);

		component.html("");
    	$scope.cleanInsert("<span class='removeOnInsert'>"+result+"</span>", component);
    	
        $scope.adjustResizeBox();
    }


    /**
     * Get ID of a Link Wrapper
     *
     * @since 0.3.1
     * @author Ilya K.
     */
    
    $scope.getLinkId = function(id) {

    	if ( undefined === id ) {
    		id = $scope.component.active.id;
    	}

    	// get closest parent
    	var link = jQuery("[ng-attr-component-id='"+id+"']", "#ct-builder").closest(".ct-links");

    	// link found
    	if (link.length > 0) {
    		return link.attr("ng-attr-component-id");
    	}
    	// not found
    	else {
    		return false;
    	}
    }
    

    /**
     * Get ID of a Link Wrapper
     *
     * @since 0.3.1
     * @author Ilya K.
     */
    
    $scope.addSeparator = function(id) {

    	$scope.separatorAdded = true;
    }


    /**
     * Add classes styles to components tree to pass with API
     *
     * @since 0.4.0
     * @author Ilya K.
     */
    
    $scope.addComponentClassesStyles = function(component) {

    	if (component.options && component.options.classes !== undefined ) {
    		for (var className in $scope.classes) {
				if (component.options && component.options.classes.indexOf(className) > -1) {
					
					var index = component.options.classes.indexOf(className);
					component.options.classes.splice(index,1);
					
					component.options.classes.push({
						'name': className,
						'styles': $scope.classes[className]
					})
				}
			}
		}
		
		// loop children
		for(var key in component.children) { 
			if (component.children.hasOwnProperty(key)) {
				// get child
				var child = component.children[key];
				$scope.addComponentClassesStyles(child);
			}
		}
    }


    /**
     * Parse components tree from API to add classes styles
     *
     * @since 0.4.0
     * @author Ilya K.
     */
    
    $scope.parseTreeClassesStyles = function(component) {

    	if (component.options && component.options.classes !== undefined ) {
    		for (var key in component.options.classes) { 
				if ( component.options.classes.hasOwnProperty(key)) {

					if (component.options.classes[key]["name"] != undefined) {
						var className = component.options.classes[key]["name"],
							styles = component.options.classes[key]["styles"];
						
						// add class name back to the tree
						component.options.classes[key] = className;

						// check if class exist
						if ( $scope.classes[className] !== undefined ) {
							//console.log("'"+className+"' class already exist in your install. Styles for this class won't be added.");
						}
						else {
							// add class styles to global classes object
							$scope.classes[className] = styles;
						}
					}
				}
			}
			$scope.classesCached = false;
			$scope.outputCSSOptions();
		}
		
		// loop children
		for(var key in component.children) { 
			if (component.children.hasOwnProperty(key)) {
				// get child
				var child = component.children[key];
				$scope.parseTreeClassesStyles(child);
			}
		}
	}

});

/**
 * jQuery serializeObject
 * @copyright 2014, macek <paulmacek@gmail.com>
 * @link https://github.com/macek/jquery-serialize-object
 * @license BSD
 * @version 2.5.0
 */
!function(e,i){if("function"==typeof define&&define.amd)define(["exports","jquery"],function(e,r){return i(e,r)});else if("undefined"!=typeof exports){var r=require("jquery");i(exports,r)}else i(e,e.jQuery||e.Zepto||e.ender||e.$)}(this,function(e,i){function r(e,r){function n(e,i,r){return e[i]=r,e}function a(e,i){for(var r,a=e.match(t.key);void 0!==(r=a.pop());)if(t.push.test(r)){var u=s(e.replace(/\[\]$/,""));i=n([],u,i)}else t.fixed.test(r)?i=n([],r,i):t.named.test(r)&&(i=n({},r,i));return i}function s(e){return void 0===h[e]&&(h[e]=0),h[e]++}function u(e){switch(i('[name="'+e.name+'"]',r).attr("type")){case"checkbox":return"on"===e.value?!0:e.value;default:return e.value}}function f(i){if(!t.validate.test(i.name))return this;var r=a(i.name,u(i));return l=e.extend(!0,l,r),this}function d(i){if(!e.isArray(i))throw new Error("formSerializer.addPairs expects an Array");for(var r=0,t=i.length;t>r;r++)this.addPair(i[r]);return this}function o(){return l}function c(){return JSON.stringify(o())}var l={},h={};this.addPair=f,this.addPairs=d,this.serialize=o,this.serializeJSON=c}var t={validate:/^[a-z_][a-z0-9_]*(?:\[(?:\d*|[a-z0-9_]+)\])*$/i,key:/[a-z0-9_]+|(?=\[\])/gi,push:/^$/,fixed:/^\d+$/,named:/^[a-z0-9_]+$/i};return r.patterns=t,r.serializeObject=function(){return new r(i,this).addPairs(this.serializeArray()).serialize()},r.serializeJSON=function(){return new r(i,this).addPairs(this.serializeArray()).serializeJSON()},"undefined"!=typeof i.fn&&(i.fn.serializeObject=r.serializeObject,i.fn.serializeJSON=r.serializeJSON),e.FormSerializer=r,r});