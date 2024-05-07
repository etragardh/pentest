/**
 * All Media Queries stuff
 * 
 */

CTFrontendBuilder.controller("ControllerMediaQueries", function($scope, $parentScope, $http, $timeout, $interval) {


	$scope.currentMedia = "default";

	/**
	 * Init Default Media Queries
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	$scope.initMedia = function() {
		var pageWidth = $scope.getPageWidth();
		
		var tabletWidth = $scope.getBreakPointWidth('tablet');
		var phoneLandscapeWidth = $scope.getBreakPointWidth('phone-landscape');
		var phonePortraitWidth = $scope.getBreakPointWidth('phone-portrait');
		
        $scope.previousBreakPointsValues = {
            'page-width': {
                'page': $scope.pageSettingsMeta['max-width'],
                'global': $scope.globalSettings['max-width'],
            },
            'tablet': $scope.globalSettings.breakpoints['tablet'],
            'phone-landscape': $scope.globalSettings.breakpoints['phone-landscape'],
            'phone-portrait': $scope.globalSettings.breakpoints['phone-portrait'],
        };

		$scope.mediaList = {
            default: {
                maxSize: "100%",
                title: "Full Screen"
            },
            "page-width": {
                maxSize: pageWidth + "px",
                title: "Page container (" + pageWidth + "px) and below"
            },
            "tablet": {
                maxSize: (tabletWidth - 1) + "px",
                title: "Less than " + tabletWidth + "px"
            },
            "phone-landscape": {
                maxSize: (phoneLandscapeWidth - 1) + "px",
                title: "Less than " + phoneLandscapeWidth + "px"
            },
            "phone-portrait": {
                maxSize: (phonePortraitWidth - 1) + "px",
                title: "Less than " + phonePortraitWidth + "px"
            }
        };

		$scope.mediaListAbove = {
            default: {
                minSize: "100%",
                title: "Full Screen"
            },
            "page-width": {
                minSize: (pageWidth + 1) + "px",
                title: "Above Page container (" + pageWidth + "px)"
			},
            "tablet": {
                minSize: tabletWidth + "px",
                title: "At or above " + tabletWidth + "px"
            },
            "phone-landscape": {
                minSize: phoneLandscapeWidth + "px",
                title: "At or above " + phoneLandscapeWidth + "px"
            },
            "phone-portrait": {
                minSize: phonePortraitWidth + "px",
                title: "At or above " + phonePortraitWidth + "px"
            }
        };
	}


	/**
	 * Set current Media Query to edit
	 * 
	 * @since 0.3.2
	 */
	
	$scope.setCurrentMedia = function(name, viewportUpdate, isGlobal) {

		if ( $scope.getCurrentMedia() == name ) {
			return;
		}

		//console.log("setCurrentMedia", name);
		
		$scope.currentMedia = name;

		// update viewport
		if (viewportUpdate === undefined || viewportUpdate ) {
			var size = $scope.getMediaPreviewSize(name, isGlobal);
			$parentScope.adjustViewport(size);

			if (name=="default") {
				$parentScope.hideViewportRuller();
			}
			else {
				$parentScope.showViewportRuller();
				// reset scale
                //window.parent.jQuery("#ct-artificial-viewport").css("transform", "scale(1)");
			}
		}
		$parentScope.adjustViewportContainer();

		// disable stuff
        $parentScope.disableContentEdit();
        $parentScope.disableSelectable();
        $parentScope.closeAllTabs(["advancedSettings","componentBrowser"]); // keep certain sections

		// apply options
		$scope.applyModelOptions();

        // update all components styles
        /*$scope.updateAllComponentsCacheStyles();
        $scope.classesCached = false;
        $scope.outputCSSOptions(-1, true);*/
        $parentScope.checkTabs();

        //close backgroundlayers when component is switched
        $scope.parentScope.activeForEditBgLayer = false;

        if (!$scope.isEditing('custom-selector')) {
            var timeout = $timeout(function() {
                $parentScope.scrollToComponent($scope.getActiveComponent().attr('id'));
                $timeout.cancel(timeout);
            }, 0, false);
        }
	}


	/**
	 * Get currently editing Media Query
	 * 
	 * @since 0.3.2
	 */

	$scope.getCurrentMedia = function() {

		return $scope.currentMedia;
	}


	/**
	 * Get Media Query Title by name
	 * 
	 * @since 0.3.2
	 */

	$scope.getMediaTitle = function(name,above,isGlobal) {

		if (!name)
			return "";

		if (name == 'never')
			return 'Never';

		if (name == 'always')
			return 'Always';

		if ($scope.mediaListAbove!==undefined && above === true) {
			if (isGlobal && name == 'page-width') {
				return "Page container ("+$scope.getPageWidth('global')+"px) and above";
			}
			else {
				return $scope.mediaListAbove[name].title;
			}
		}
		else if ($scope.mediaList!==undefined) {
			if (isGlobal && name == 'page-width') {
				return "Page container ("+$scope.getPageWidth('global')+"px) and below";
			}
			else {
				return $scope.mediaList[name].title;
			}
		}
		else 
			return "";
	}


	/**
	 * Get Media Query max size by name
	 * 
	 * @since 0.3.2
	 */

	$scope.getMediaMaxSize = function(name) {

		return (typeof($scope.mediaList[name])!=='undefined') ? $scope.mediaList[name].maxSize : false;
	}


	/**
	 * Get Media Query min size by name
	 * 
	 * @since 2.0
	 * @author Ilya K.
	 */

	$scope.getMediaMinSize = function(name) {

		return (typeof($scope.mediaList[name])!=='undefined') ? $scope.mediaListAbove[name].minSize : false;
	}


	/**
	 * Get Media Query Preview Size by name
	 * 
	 * @since 2.0
	 * @author Ilya K.
	 */

	$scope.getMediaPreviewSize = function(name, isGlobal) {

		if ( name == "default" ) 
			return "100%";

		var sortedMedia = $scope.sortedMediaList(false, isGlobal);
		
		for (var index in sortedMedia) {
			var mediaName = sortedMedia[index];
			
			if (mediaName==name) {

				var name = sortedMedia[parseInt(index)+1];

				if (name !== undefined) {
					if (name=="page-width" && isGlobal) {
						var previewSize = parseInt($scope.getPageWidth(true))+1;
					} else {
						var previewSize = parseInt($scope.mediaList[name].maxSize)+1;
					}
					return previewSize+"px";
				}
				else {
                    var previewSize = $scope.getBreakPointWidth('phone-portrait') - 120;
                    previewSize = Math.max(previewSize, 320);
                    return previewSize+"px";
                }
			}
		}
	}

	/**
	 * Get currently editing Media Query Size
	 * 
	 * @since 0.3.2
	 */
	
	$scope.getMediaNameBySize = function(width) {

		var medias = [];

		for(var media in $scope.mediaList) { 
			if ($scope.mediaList.hasOwnProperty(media)) {

				//console.log(width + " < " + parseInt($scope.mediaList[media]['maxSize']));
				
				if ( width < parseInt($scope.mediaList[media]['maxSize']) ) {
					medias.push(media);
				}
			}
		}

		if ( medias.length > 0 ) {
			return medias[medias.length-1];
		}
		else {
			return "default";
		}
	}


	/**
	 * Get all medias that may apply to current i.e for max-size: 768px, also should apply max-size: 992px
	 * 
	 * @since 0.3.2
	 * @deprecated 2.0
	 */

	$scope.getAllMediaNames = function(name) {

		if (name=="default") {
			return [];
		}

		var medias = [];

		for(var media in $scope.mediaList) { 
			if ($scope.mediaList.hasOwnProperty(media)) {
				
				if ( parseInt($scope.mediaList[name]['maxSize']) < parseInt($scope.mediaList[media]['maxSize']) ) {
					medias.push(media);
				}
			}
		}
		
		medias.push(name);
		
		return medias;
	}


	/**
	 * Set current Media parameter value
	 * 
	 * @since 0.3.2
	 */
	
	$scope.setMediaParameter = function(id, parameter, value, state) {

		// create objects if not exist
		if (!$scope.component.options[id]['media']) {
			$scope.component.options[id]['media'] = {};
		}
		if (!$scope.component.options[id]['media'][$scope.currentMedia]) {
			$scope.component.options[id]['media'][$scope.currentMedia] = {};
		}
		if (!$scope.component.options[id]['media'][$scope.currentMedia][state]) {
			$scope.component.options[id]['media'][$scope.currentMedia][state] = {};
		}

		$scope.component.options[id]['media'][$scope.currentMedia][state][parameter] = value;
	}


	/**
	 * Check if currently editing component or class certain media has styles
	 * 
	 * @since 0.3.2
	 * @return {bool}
	 * @author Ilya K.
	 */

	$scope.isHasMedia = function(mediaName, id) {

		if (undefined == id) {
			id = $scope.component.active.id;
		}

		if ( $scope.isEditing("id") ) {
			if ($scope.component.active.name == "ct_reusable") {

				var viewId = $scope.component.options[id].original['view_id'];
				
				if ( !$scope.postsData[viewId] ) {
					return false;
				}
				
				var reusableTree = $scope.postsData[viewId]["post_tree"];
				
				if ($scope.findMedia(reusableTree, mediaName)) {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				if ( $scope.component.options[id]['media'] && 
					 $scope.component.options[id]['media'][mediaName] && 
					 $scope.component.options[id]['media'][mediaName][$scope.currentState] ) {
					
					return true;
				}
				else {
					return false;
				}
			}
		}

		if ( $scope.isEditing("class") ) {
			
			if ( $scope.classes[$scope.currentClass] &&
				 $scope.classes[$scope.currentClass]['media'] && 
				 $scope.classes[$scope.currentClass]['media'][mediaName] && 
				 $scope.classes[$scope.currentClass]['media'][mediaName][$scope.currentState] ) {
				
				return true;
			}
			else {
				return false;
			}
		}

		if ( $scope.isEditing("custom-selector") && !$scope.isEditing("class") ) {
			
			if ( $scope.customSelectors[$scope.selectorToEdit] &&
				 $scope.customSelectors[$scope.selectorToEdit]['media'] && 
				 $scope.customSelectors[$scope.selectorToEdit]['media'][mediaName] && 
				 $scope.customSelectors[$scope.selectorToEdit]['media'][mediaName][$scope.currentState] ) {
				
				return true;
			}
			else {
				return false;
			}
		}		
	}


	/**
	 * Check if currently editing component or class has any media styles
	 * 
	 * @since 0.3.2
	 * @return {bool}
	 * @author Ilya K.
	 */

	$scope.isHasMedias = function() {

		for(var media in $scope.mediaList) { 
			if ($scope.mediaList.hasOwnProperty(media)) {

				if (media==="default") 
					continue;
			
				if ( $scope.isHasMedia(media) === true )
					return true;
			}
		}

		return false;
	}


	/**
	 * Remove media styles from component
	 * 
	 * @since 0.3.2
	 */

	$scope.removeComponentMedia = function(mediaName, id) {

		$scope.cancelDeleteUndo();

		if ( $scope.isEditing("id") ) {
			
			if (undefined == id) {
				id = $scope.component.active.id;
			}

			if ( $scope.component.options[id]['media'] && 
				 $scope.component.options[id]['media'][mediaName] && 
				 $scope.component.options[id]['media'][mediaName][$scope.currentState] ) {
				
				$scope.component.options[id]['media'][mediaName][$scope.currentState] = {};
				delete $scope.component.options[id]['media'][mediaName][$scope.currentState];

				$scope.outputCSSOptions(id);
				$scope.applyModelOptions();

				// update Tree
            	$scope.findComponentItem($scope.componentsTree.children, id, $scope.removeMediaFromTree, mediaName);
			}
		}

		else if ( $scope.isEditing("class") ) {

			if ( $scope.classes[$scope.currentClass] &&
				 $scope.classes[$scope.currentClass]['media'] && 
				 $scope.classes[$scope.currentClass]['media'][mediaName] && 
				 $scope.classes[$scope.currentClass]['media'][mediaName][$scope.currentState] ) {

				$scope.classes[$scope.currentClass]['media'][mediaName][$scope.currentState] = {};
				delete $scope.classes[$scope.currentClass]['media'][mediaName][$scope.currentState];

				$scope.outputCSSOptions();
				$scope.applyModelOptions();
			}
		}

        else if ( $scope.isEditing("custom-selector") ) {

			if ( $scope.customSelectors[$scope.selectorToEdit] &&
				 $scope.customSelectors[$scope.selectorToEdit]['media'] && 
				 $scope.customSelectors[$scope.selectorToEdit]['media'][mediaName] && 
				 $scope.customSelectors[$scope.selectorToEdit]['media'][mediaName][$scope.currentState] ) {

				$scope.customSelectors[$scope.selectorToEdit]['media'][mediaName][$scope.currentState] = {};
				delete $scope.customSelectors[$scope.selectorToEdit]['media'][mediaName][$scope.currentState];

				$scope.outputCSSOptions();
				$scope.applyModelOptions();
			}
		}

		$scope.unsavedChanges();

		$scope.adjustResizeBox();
	}


	/**
	 * Remove media styles from Components Tree
	 * 
	 * @since 1.0.1
	 * @author Ilya K.
	 */
	
	$scope.removeMediaFromTree = function(key, item, mediaName) {
		delete item.options.media[mediaName];
	}


	/**
	 * Place the page settings media in right position 
	 * 
	 * @since 2.0.
	 * @author Ilya K.
	 */

	$scope.sortedMediaList = function(above, isGlobal){

		if (above===true) {
			var mediaList = $scope.mediaListAbove;
			size = "minSize"
		}
		else {
			var mediaList = $scope.mediaList;
			size = "maxSize"
		}

		var medias = ["default"],
			pageWidthAdded = false;
		
		if (mediaList) {
			if (isGlobal===true) {
				// classes should use Global Page Width
				var width = $scope.globalSettings['max-width'];
			}
			else {
				var width = mediaList['page-width'][size];
			}
		}
		
		for(var media in mediaList) { 
			if (mediaList.hasOwnProperty(media)) {

				if ( media == "default" || media == "page-width" )
					continue;
				
				if ( (parseInt(mediaList[media][size]) >= parseInt(width)) || pageWidthAdded ) {
					medias.push(media);
				}
				else {
					medias.push('page-width');
					medias.push(media);
					pageWidthAdded = true;
				} 
			}
		}

		if (!pageWidthAdded) {
			medias.push('page-width');
		}

		return medias;
	}

	/**
     * Return property value for current editing state, but for specific media breakpoint
     *
     * @since 3.9
     * @return {string}
     */

	$scope.getCurrentBreakpointValue = function(optionName, media) {

        if ($scope.isEditing("id")) {

			id = $scope.component.active.id;

			if (media=="default") {
				if ( $scope.component.options[id][$scope.currentState] &&
					 $scope.component.options[id][$scope.currentState][optionName]) {
					return $scope.component.options[id][$scope.currentState][optionName];
				}
			}
            else if ( $scope.component.options[id]['media'] &&
                 $scope.component.options[id]['media'][media] &&
                 $scope.component.options[id]['media'][media][$scope.currentState] &&
                 $scope.component.options[id]['media'][media][$scope.currentState][optionName]) {
                return $scope.component.options[id]['media'][media][$scope.currentState][optionName];
            }
        }

        if ($scope.isEditing("class") ) {
            
			if (media=="default") {
				if ( $scope.classes[$scope.currentClass] &&
					 $scope.classes[$scope.currentClass][$scope.currentState] &&
					 $scope.classes[$scope.currentClass][$scope.currentState][optionName]) {
					return $scope.classes[$scope.currentClass][$scope.currentState][optionName];
				}
			}
            else if ( $scope.classes[$scope.currentClass] &&
                 $scope.classes[$scope.currentClass]['media'] &&
                 $scope.classes[$scope.currentClass]['media'][media] &&
                 $scope.classes[$scope.currentClass]['media'][media][$scope.currentState] &&
                 $scope.classes[$scope.currentClass]['media'][media][$scope.currentState][optionName]) {
                return $scope.classes[$scope.currentClass]['media'][media][$scope.currentState][optionName];
            }
        }

        // editing custom selector
        if ($scope.isEditing("custom-selector") && !$scope.isEditing("class") ) {

            if (media=="default") {
				if ( $scope.customSelectors[$scope.selectorToEdit] &&
					 $scope.customSelectors[$scope.selectorToEdit][$scope.currentState] &&
					 $scope.customSelectors[$scope.selectorToEdit][$scope.currentState][optionName]) {
					return $scope.customSelectors[$scope.selectorToEdit][$scope.currentState][optionName];
				}
			}
            else if ( $scope.customSelectors[$scope.selectorToEdit] &&
                $scope.customSelectors[$scope.selectorToEdit]['media'] &&
                $scope.customSelectors[$scope.selectorToEdit]['media'][media] &&
                $scope.customSelectors[$scope.selectorToEdit]['media'][media][$scope.currentState] &&
                $scope.customSelectors[$scope.selectorToEdit]['media'][media][$scope.currentState][optionName] ){
                return $scope.customSelectors[$scope.selectorToEdit]['media'][media][$scope.currentState][optionName];
            }
        }

		return "";
    }


	/**
     * Return property value for passed editing state, but for specific media breakpoint
     *
     * @since 4.0
     * @return {string}
     */

	 $scope.getCustomBreakpointValue = function(optionName, media, options) {

        if (options.id && options.state) {

			if (media=="default") {
				if ( $scope.component.options[options.id][options.state] &&
					 $scope.component.options[options.id][options.state][optionName]) {
					return $scope.component.options[options.id][options.state][optionName];
				}
			}
            else if ( $scope.component.options[options.id]['media'] &&
                 $scope.component.options[options.id]['media'][media] &&
                 $scope.component.options[options.id]['media'][media][options.state] &&
                 $scope.component.options[options.id]['media'][media][options.state][optionName]) {
                return $scope.component.options[options.id]['media'][media][options.state][optionName];
            }
        }

        if (options.class && options.state) {
            
			if (media=="default") {
				if ( $scope.classes[options.class] &&
					 $scope.classes[options.class][options.state] &&
					 $scope.classes[options.class][options.state][optionName]) {
					return $scope.classes[options.class][options.state][optionName];
				}
			}
            else if ( $scope.classes[options.class] &&
                 $scope.classes[options.class]['media'] &&
                 $scope.classes[options.class]['media'][media] &&
                 $scope.classes[options.class]['media'][media][options.state] &&
                 $scope.classes[options.class]['media'][media][options.state][optionName]) {
                return $scope.classes[options.class]['media'][media][options.state][optionName];
            }
        }

        // editing custom selector
        if (options.selector && options.state) {

            if (media=="default") {
				if ( $scope.customSelectors[options.selector] &&
					 $scope.customSelectors[options.selector][options.state] &&
					 $scope.customSelectors[options.selector][options.state][optionName]) {
					return $scope.customSelectors[options.selector][options.state][optionName];
				}
			}
            else if ( $scope.customSelectors[options.selector] &&
                $scope.customSelectors[options.selector]['media'] &&
                $scope.customSelectors[options.selector]['media'][media] &&
                $scope.customSelectors[options.selector]['media'][media][options.state] &&
                $scope.customSelectors[options.selector]['media'][media][options.state][optionName] ){
                return $scope.customSelectors[options.selector]['media'][media][options.state][optionName];
            }
        }

		return "";
    }

	
	/**
     * Return property value for for closest bigger breakpoint for passed property
     *
     * @since 3.9
     * @return {string}
     */

	$scope.getClosestBreakpointValue = function(optionName, media, customOptions) {
		
		if (!$scope.isEditing("media") && !customOptions) {
			return "";
		}
		
		if (undefined===media) {
            media = $scope.currentMedia;
        }

		var breakpoints = $scope.sortedMediaList().reverse();
		var found = false;
		
		for(var key in breakpoints) { 
			if (breakpoints.hasOwnProperty(key)) {
				var currentMedia = breakpoints[key];

				if (found) {
					var value = $scope.getCurrentBreakpointValue(optionName, currentMedia);
					if (customOptions !== undefined) {
						value = $scope.getCustomBreakpointValue(optionName, currentMedia, customOptions);
					}
					if (value) {
						return value;
					}
				}

				if (currentMedia == media) {
					found = true;
				}
			}
		}

		// section padding fallback to global settings
		if (optionName.indexOf("-unit") > 0 && optionName.indexOf("container-padding") > -1) {
			return $scope.globalSettings.sections[optionName];
		}

		return "";
	}

})