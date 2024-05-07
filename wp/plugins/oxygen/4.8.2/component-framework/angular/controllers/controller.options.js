/**
 * All Classes staff here
 * 
 */

CTFrontendBuilder.controller("ControllerOptions", function($scope, $parentScope, $timeout) {

    $scope.changesToApply = [];

    $scope.optionsHierarchy = {
        "background" : {
            
            "color"         : ["background-color"],
            "image"         : ["background","background-image","overlay-color","video_background","video_background_overlay", "background-blend-mode"],
            "size"          : ["background-size","background-repeat","background-attachment","background-position-left","background-position-top",
                               "background-size-width","background-size-height","background-clip"],
        },

        "position" : {
            
            "margin_padding" : ["container-padding-top","container-padding-right","container-padding-bottom","container-padding-left",
                                "padding-top","padding-right","padding-bottom","padding-left",
                                "margin-top","margin-right","margin-bottom","margin-left",
                                // units
                                "container-padding-top-unit","container-padding-right-unit","container-padding-bottom-unit","container-padding-left-unit",
                                "padding-top-unit","padding-right-unit","padding-bottom-unit","padding-left-unit",
                                "margin-top-unit","margin-right-unit","margin-bottom-unit","margin-left-unit"],
            "size"           : ["width","min-width","max-width","height","min-height","max-height","section-width","header-width","header-row-width",
                                // units
                                "width-unit","min-width-unit","max-width-unit","height-unit","min-height-unit","max-height-unit"],
        },

        "layout" : {
            "position"       : ["display","float","visibility","z-index","clear","overflow","position","top","right","bottom","left",
                                // flex options
                                "flex-direction","align-items","justify-content","flex-wrap","flex-reverse","align-content","order"]
        },

        "typography" : {
            
            "typography"    : ["font-family","font-size","font-weight","color","text-align","line-height","letter-spacing","text-decoration","font-style","text-transform", "-webkit-font-smoothing"],
        },

        "borders" : {
            
            "border"        : ["border-top-color","border-top-style","border-top-width",
                               "border-right-color","border-right-style","border-right-width",
                               "border-bottom-color","border-bottom-style","border-bottom-width",
                               "border-left-color","border-left-style","border-left-width"],
            "radius"        : ["border-top-left-radius","border-top-right-radius",
                               "border-bottom-left-radius","border-bottom-right-radius","border-radius"]
        },

        "effects" : {
            "opacity" : ["opacity"],
            "transform" : ["transform"],
            "transition" : ['transition-duration','transition-duration-unit','transition-timing-function','transition-delay','transition-delay-unit','transition-property',],
            "box-shadow" : ['box-shadow-inset','box-shadow-color','box-shadow-horizontal-offset','box-shadow-vertical-offset','box-shadow-blur','box-shadow-spread'],
            "text-shadow" : ['text-shadow-color','text-shadow-horizontal-offset','text-shadow-vertical-offset','text-shadow-blur'],
            "css-filter" : ['filter'],
            "animation-on-scroll" : ["aos-enable","aos-type","aos-easing","aos-duration",'aos-offset','aos-delay','aos-anchor','aos-anchor-placement','aos-once',],
        },

        "cssjs" : {
            
            "css"   : ["custom-css"],
            "js"    : ["custom-js"]
        },

        "code-php": {
            "code-php": ["code-php"]
        },
        "code-css": {
            "code-css": ["code-css"]
        },
        "code-js": {
            "code-js": ["code-js"]
        },

        "custom-attributes": {
            "custom-attributes": ["custom-attributes"]
        }
    }

    $scope.builtinComponents = {};
    $scope.lastSetEasyPostsTemplate = [];


    /**
     * Add new color to the global colors object
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.addNewColor = function(colorName, setID, colorValue) {

        if ($scope.log){
            console.log("addNewColor()", colorName, setID, colorValue)
        }

        if(CtBuilderAjax.freeVersion) {
            $parentScope.hideAddNewColorDialog()
            $parentScope.showDialogWindow();
            $parentScope.dialogForms['showProGlobalColorEditDialog'] = true;
            return;
        }

        if (colorValue == 'latest') {
            colorValue = $parentScope.addNewColorDialogValue;
        }

        if (colorValue===undefined||colorValue=='') {
            $scope.showErrorModal(0, "Color value is not defined");
            $parentScope.hideAddNewColorDialog();
            return false;
        }

        if (colorName===undefined||colorName=='') {
            $scope.showErrorModal(0, "Color name is not defined");
            $parentScope.hideAddNewColorDialog();
            return false;
        }

        var set = $scope.getGlobalColorSet(setID);

        // check if there is a color already with that name in the set
        var possibleDuplicate = $scope.getGlobalColorbyName(colorName, set.name);
        if (possibleDuplicate) {
            $scope.showErrorModal(0, "'" + colorName + "' already exist in '" + set.name + "' set");
            $parentScope.newGlobalSettingsColorName = "";
            $parentScope.hideAddNewColorDialog();
            return false; 
        }

        // Use 'Global Colors' set as default
        if (setID===undefined||setID==='') {
            setID = 0;
        }

        // increment the color key used as ID
        $scope.globalColorSets.colorsIncrement++;
        $scope.globalColorSets.colors.push({
            id: $scope.globalColorSets.colorsIncrement,
            name: colorName,
            value: colorValue,
            set: setID
        });

        $scope.parentScope.setGlobalColor($scope.globalColorSets.colorsIncrement);

        // close dialog window
        $parentScope.hideAddNewColorDialog();
        $parentScope.newGlobalColorName = "";
        $parentScope.newGlobalColorValue = "";
        $parentScope.newGlobalSettingsColorName = "";

        $parentScope.showAddGlobalColorPanel = false;
    }


    /**
     * Add new color to the global colors object
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.addNewColorSet = function(colorSetName) {

        if ($scope.log) {
            console.log("addNewColorSet()", colorSetName);
        }
        
        if(CtBuilderAjax.freeVersion) {
            $parentScope.showDialogWindow();
            $parentScope.dialogForms['showProGlobalColorEditDialog'] = true;
            return;
        }
        
        if (colorSetName==undefined||colorSetName=='') {
            $scope.showErrorModal(0, "Color Set name is not defined");
            return false;
        }

        var possibleDuplicate = $scope.getGlobalColorSetbyName(colorSetName); 
        if (possibleDuplicate) {
            $scope.showErrorModal(0, "'" + colorSetName + "' name is already taken");
            return false;
        }
        
        // increment the sets key used as ID
        $scope.globalColorSets.setsIncrement++;
        $scope.globalColorSets.sets.push({
            "id": $scope.globalColorSets.setsIncrement,
            "name": colorSetName
        });

        // hide form and clear the name
        $parentScope.newGlobalColorSetName = '';
        $parentScope.addGlobalColorSetPanel = false;
    }


    /**
     * Check if the given param is a global color or a plain value
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.isGlobalColorValue = function(option) {
        if (option.indexOf("color(")===0) {
            return true;
        }
        else {
            return false;
        }
    }


    /**
     * Parse the color() option to get the color ID
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.getGlobalColorID = function(colorOption) {

        if ($scope.log) {
            console.log("getGlobalColorID()", colorOption);
        }

        if (!colorOption || typeof(colorOption) !== 'string') {
            return false
        }

        if (colorOption.indexOf("color(")!==0) {
            return false
        }

        // get the value inside the parentheses
        var regExp = /\(([^)]+)\)/,
            matches = regExp.exec(colorOption);

        if (matches==undefined) {
            return false;
        }

        return matches[1];
    }


    /**
     * Parse the color() option to get the actual hex or rgba value
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.getGlobalColorValue = function(colorOption) {

        if ($scope.log) {
            console.log("getGlobalColorValue()", colorOption);
        }

        var colorID = $scope.getGlobalColorID(colorOption)

        if (colorID===false) {
            return colorOption;
        }

        // get color by name
        var color = $scope.globalColorSets.colors.find(function(color) {
            return color.id == colorID;
        });

        if (color===undefined||color.value===undefined) {
            return colorOption;
        }

        return color.value;
    }


    /**
     * Parse the color() option to get the actual hex or rgba value
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.getGlobalColorNiceName = function(colorOption) {

        if ($scope.log) {
            console.log("getGlobalColorValue()", colorOption);
        }

        var colorID = $scope.getGlobalColorID(colorOption)

        if (colorID===false) {
            return false;
        }

        // get color by id
        var color = $scope.globalColorSets.colors.find(function(color) {
            return color.id == colorID;
        });

        if (color===undefined||color.value===undefined) {
            return false;
        }

        var set = $scope.getGlobalColorSet(color.set);

        if (set===undefined||set.name===undefined) {
            return false;
        }

        return set.name + " - " + color.name;
    }


    /**
     * Get color object by ID
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.getGlobalColor = function(colorID) {

        if ($scope.log){
            console.log("getGlobalColor()", colorID);
        }

        return $scope.globalColorSets.colors.find(function(color) {
            return color.id == colorID;
        });
    }


    /**
     * Get color object by its name
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.getGlobalColorbyName = function(colorName, setName) {

        if ($scope.log){
            console.log("getGlobalColorbyName()", colorName, setName);
        }

        var set = $scope.getGlobalColorSetbyName(setName);

        return $scope.globalColorSets.colors.find(function(color) {
            return color.name == colorName && color.set == set.id;
        });
    }


    /**
     * Get color set object by it's ID
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.getGlobalColorSet = function(setID) {

        if ($scope.log){
            console.log("getGlobalColorSet()", setID, $scope.globalColorSets.sets);
        }

        return $scope.globalColorSets.sets.find(function(set) {
            return set.id == setID;
        });
    }


    /**
     * Get color set object by it's name
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.getGlobalColorSetbyName = function(name) {

        if ($scope.log){
            console.log("getGlobalColorSet()", name, $scope.globalColorSets.sets);
        }

        return $scope.globalColorSets.sets.find(function(set) {
            return set.name == name;
        });
    }


    /**
     * Delete existing color set from the global colors object
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.deleteGlobalColorSet = function(setID, resetSettingsPanel) {

        var confirmed = confirm("Are you sure to delete this set?");
        
        if (!confirmed) {
            return false;
        }

        var key = $scope.globalColorSets.sets.findIndex(function(set) {
            return set.id == setID;
        });

        // delete set
        $scope.globalColorSets.sets.splice(key,1);

        // delete all associated colors
        for(var key in $scope.globalColorSets.colors) { 
            if ($scope.globalColorSets.colors.hasOwnProperty(key)) {
                var color = $scope.globalColorSets.colors[key];
                if (color.set===setID) {
                    $scope.globalColorSets.colors[key] = null;
                }
            }
        }

        // clear deleted colors
        $scope.globalColorSets.colors = $scope.globalColorSets.colors.filter(function(value) {
            return value !== null;
        });

        if (resetSettingsPanel === true) {
            $parentScope.toggleSettingsPanel(true);
            $parentScope.switchTab('settings','colors');
        }

        $scope.unsavedChanges();
    }


    /**
     * Delete existing color from the global colors object
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.deleteGlobalColor = function(colorID, $event) {

        var key = $scope.globalColorSets.colors.findIndex(function(color) {
            return color.id == colorID;
        });
        
        $scope.globalColorSets.colors.splice(key, 1);

        // unset current component color
        //$parentScope.unsetGlobalColor($event);
     
        $scope.unsavedChanges();
    }


    /**
     * Checks if component has any colors associated with it
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.globalColorSetHasColors = function(setID) {
        
        return $scope.globalColorSets.colors.find(function(color) {
            return color.set == setID;
        });
    }


    /**
     * Replace global colors color(x) placeholders in a string
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.replaceGlobalColors = function(css) {

        if (css===undefined) {
            return css;
        }
        
        return css.replace(/color\(\d+\)/g, function (match) {
            // replace global colors color(x) placeholders
            return $scope.getGlobalColorValue(match);
        });
    }


    /**
     * Replace global CSS var() in WooCo default styling
     * 
     * @since 3.0
     * @author Ilya K.
     */

    $scope.replaceGlobalWooSettings = function(css) {

        if (css===undefined) {
            return css;
        }

        css = css.replace(/%%ASSETS_PATH%%/g, CtBuilderAjax.wooAssetsPath);
        
        return css.replace(/var\(.*?\)/g, function (match) {
            variable = match.replace("var(","");
            variable = variable.replace(")","");
            if ($scope.globalSettings['woo'] && $scope.globalSettings['woo'][variable] ) {
                var unit = "";
                if ($scope.globalSettings['woo'][variable+"-unit"]) {
                    unit = $scope.globalSettings['woo'][variable+"-unit"];
                }
                match = $scope.globalSettings['woo'][variable]+unit;
            }
            // replace global colors color(x) placeholders
            match = $scope.getGlobalColorValue(match);
            return match;
        });
    }


    /**
     * Replace %%option_name%% opitions with actual values in any text code
     * 
     * @since 2.3
     * @author Ilya K.
     */

    $scope.parseAPIOptions = function(code, id, options) {

        if ($scope.log) {
            console.log("replaceComponentOptions()", code, id, options);
        }

        if (code===undefined || id===undefined || !code || !id) {
            return code;
        }
        
        return code.replace(/%%\S+%%/g, function (match) {
            // keywords
            if (match==="%%ELEMENT_ID%%") {
                return $scope.component.options[id]['selector'];
            }

            match = match.replace(/%%/g,"");

            return '{{component.options['+id+'][\'model\'][\''+match+'\']}}'
        });
    }


    /**
     * Replace %%option_name%% opitions with Angular {{expressions}}
     * 
     * @since 2.3
     * @author Ilya K.
     */

    $scope.replaceComponentOptions = function(code, id, options) {

        if ($scope.log) {
            console.log("replaceComponentOptions()", code, id, options);
        }

        if (code===undefined || id===undefined || !code || !id) {
            return code;
        }
        
        return code.replace(/%%\S+%%/g, function (match) {
            // keywords
            if (match==="%%ELEMENT_ID%%") {
                return $scope.component.options[id]['selector'];
            }

            match = match.replace(/%%/g,"");

            // typography preset
            if ($scope.APIPresets['typography'] && $scope.APIPresets['typography'].indexOf(match)>-1) {
                return $scope.generateTypographyCSS(options, match);
            }

            // general options
            else {
                if (options) {
                    if (options[match]) {
                        return options[match] + $scope.getOptionUnit(match);
                    }
                    return "";
                }
                else {
                    return $scope.getOption(match, id) + $scope.getOptionUnit(match);
                }
            }
        });
    }


	/**
     * Update all component's options inside a Components Tree
     * 
     * @since 0.1
     * @author Ilya K.
     */

    $scope.updateTreeComponentOptions = function(key, item, component) {
        
        if ($scope.log) {
            console.log('updateTreeComponentOptions()', key, item, component);
        }

        var componentDefaults   = $scope.defaultOptions[item.name],
            state               = $scope.currentState;

        if (componentDefaults["ct_content"]) {
            item.options["ct_content"] = componentDefaults["ct_content"];
        }

    }


    /**
     * Update one single component's option inside a Components Tree
     * 
     * @since 0.1.7
     * @author Ilya K.
     */

    $scope.updateTreeComponentOption = function(key, item, component) {
        
        if ($scope.log) {
            console.log('updateTreeComponentOption()', key, item, component);
        }

        var componentDefaults   = $scope.defaultOptions[item.name],
            state               = $scope.currentState,
            parameter           = component.optionName,
            value               = "";

        if ( $scope.optionsWhiteList.indexOf(parameter) === -1 && 
             $scope.optionsWhiteListNoMedia.indexOf(parameter) === -1 ) {
            state = 'original';
        }

        // force "hover" state for "fake_" option
        if (component.optionName.indexOf("fake_")>=0 && component.optionName.indexOf("hover_")>=0) {
            state = "hover";
            parameter = parameter.replace("fake_","");
        }

        if ( parameter == 'selector') {
            value = $scope.component.options[component.id][parameter];
        }
        else {
            if ( $scope.isEditing("id") && !$scope.isEditing("state") && !$scope.isEditing("media") ) {
                value = $scope.component.options[component.id]['id'][parameter];
            }
            else {
                value = $scope.component.options[component.id]["model"][parameter];   
            }
        }

        // change parameter to "color" for "hover_color" option
        if (component.optionName.indexOf("fake_")>=0 && component.optionName.indexOf("hover_")>=0) {
            parameter = parameter.replace("hover_","");
        }

        // handle column width option
        if ( component.tag == "ct_column" && parameter == "width" ) {
            
            if ( !item.options[state] ||
                ( Array.isArray(item.options[state]) && item.options[state].length === 0 ) ) {
                item.options[state] = {};
            }
            // update tree
            item.options[state][parameter] = value;

            // update options object
            $scope.component.options[component.id][state][parameter] = value;
            
            return true;
        }

        /**
         * Update current Class
         */

        if ( $scope.isEditing('class') && (
            $scope.optionsWhiteList.indexOf(parameter) >= 0 || 
            $scope.optionsWhiteListNoMedia.indexOf(parameter) >= 0 ||
            // exception for Icons and Buttons
            parameter == 'icon-style' || parameter == 'button-style' || parameter == 'gradient' )) {
            
            if ( $scope.classes[$scope.currentClass] ) {
                
                // clear class cache
                $scope.cache.classStyles[$scope.currentClass] = '';
                
                if (parameter=='icon-style' || parameter == 'button-style') {
                    // set icon-style only for decktop media/original state
                    $scope.classes[$scope.currentClass]['original'][parameter] = value;
                }
                // don't include not CSS options
                else if ( parameter == 'selector'    || 
                     parameter == 'ct_id'       || 
                     parameter == 'ct_parent'   || 
                     parameter == 'ct_content'  || 
                     parameter == 'classes'     || 
                     ( parameter == "url" && component.isShortcode ) ) 
                {
                   // nothing here
                }
                else {
                    // add option to class
                    if ( !$scope.isEditing('media') || 
                          $scope.optionsWhiteListNoMedia.indexOf(parameter) >= 0 ) {

                        // init class state options
                        if (// check if state doesn't exist yet
                            !$scope.classes[$scope.currentClass][state] ||
                            // force empty {} object instead of empty [] array
                            ( Array.isArray($scope.classes[$scope.currentClass][state]) && $scope.classes[$scope.currentClass][state].length === 0 ) ) 
                        {
                            $scope.classes[$scope.currentClass][state] = {};
                        }

                        // remove empty options
                        if (value == "") {
                            delete $scope.classes[$scope.currentClass][state][parameter];
                        }
                        else {
                            $scope.classes[$scope.currentClass][state][parameter] = angular.copy(value);
                        }

                    }
                    else {
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
            }
        }
        else

        /**
         * Update Media in Components Tree
         */

        if ( $scope.isEditing('media') &&
                $scope.optionsWhiteList.indexOf(parameter) >= 0 && 
                // skip other not CSS options
                parameter != 'selector'   && 
                parameter != 'ct_id'      && 
                parameter != 'ct_parent' 
            ) {

            // update media parameter in $scope.component.options
            $scope.setMediaParameter(component.id, parameter, value, state);

            // init media state if not exist
            if ( !item.options['media'] ||
                ( Array.isArray(item.options['media']) && item.options['media'].length === 0 ) ) {
                item.options['media'] = {};
            }
            if ( !item.options['media'][$scope.currentMedia] ||
                ( Array.isArray(item.options['media'][$scope.currentMedia]) && item.options['media'][$scope.currentMedia].length === 0 ) ) {
                item.options['media'][$scope.currentMedia] = {};
            }
            if ( !item.options['media'][$scope.currentMedia][state] ||
                ( Array.isArray(item.options['media'][$scope.currentMedia][state]) && item.options['media'][$scope.currentMedia][state].length === 0 ) ) {
                item.options['media'][$scope.currentMedia][state] = {};
            }

            // remove from Tree if empty
            if ( value == "" || value == undefined ) {

                // remove property
                delete item.options['media'][$scope.currentMedia][state][parameter];
                delete $scope.component.options[component.id]['media'][$scope.currentMedia][state][parameter];
                
                // remove state if empty
                if ($scope.isObjectEmpty(item.options['media'][$scope.currentMedia][state])){
                    delete item.options['media'][$scope.currentMedia][state];
                }
                // remove current media if empty
                if ($scope.isObjectEmpty(item.options['media'][$scope.currentMedia])){
                    delete item.options['media'][$scope.currentMedia];
                }
                // remove media if empty
                if ($scope.isObjectEmpty(item.options['media'])){
                    delete item.options['media'];
                }
            } 
            // add in Tree
            else {
                item.options['media'][$scope.currentMedia][state][parameter] = value;
            }
        }

        /**
         * Update Component Tree
         */
        
        else {

            // add state for options if not exist
            if (!$scope.component.options[component.id][state]){
                $scope.component.options[component.id][state] = {};
            }

            // add state for component if not exist
            if (!item.options[state]||
                ( Array.isArray(item.options[state]) && item.options[state].length === 0 ) ){
                item.options[state] = {};
            }
            // set allowed empty options even if value is empty
            if ($scope.allowedEmptyOptions.indexOf(parameter)>-1 && state == "original") {
                $scope.component.options[component.id][state][parameter] = value;
            } 
            else 
            // set option to default for original state if value is empty
            if (typeof(value) === 'undefined' && state == "original") {
                $scope.component.options[component.id][state][parameter] = componentDefaults[parameter]
            }
            // set to current value
            else {
                if (value === "" ) {
                    delete $scope.component.options[component.id][state][parameter];
                }
                else {
                    $scope.component.options[component.id][state][parameter] = value;
                }
            }
            
            // check units (px, em, etc)
            if ( parameter.indexOf("-unit") > 0 ) {

                var unitOption = parameter.replace("-unit", "");

                // delete both
                if ( $scope.component.options[component.id][state][parameter] == componentDefaults[parameter] &&
                     $scope.component.options[component.id][state][unitOption] == componentDefaults[unitOption] &&
                     !$scope.component.options[component.id]["id"][unitOption])
                {
                    delete item.options[state][parameter];
                    delete item.options[state][unitOption];
                }
                else
                // delete only unit
                if ( $scope.component.options[component.id][state][parameter] == componentDefaults[parameter] ) {
                    delete item.options[state][parameter];
                }
                // add both
                else {
                    item.options[state][parameter] = value;
                    item.options[state][unitOption] = $scope.component.options[component.id][state][unitOption];
                }  
            }
            else
            // check options with units (font-size, etc)
            if ( componentDefaults[parameter+"-unit"] !== undefined &&
                 componentDefaults[parameter+"-unit"] !== $scope.component.options[component.id][state][parameter+"-unit"] ) {
                    
                    if ( value != "" && value != undefined ) {
                        item.options[state][parameter] = value;
                    }
                    else {
                        delete item.options[state][parameter];
                        delete $scope.component.options[component.id][state][parameter];

                        // set back to default value
                        if (state == "original") {
                            $scope.component.options[component.id][state][parameter] = componentDefaults[parameter];
                        }
                    }
            }
            else
                // handle content to replace span HTML with placeholders
                if ( parameter == 'ct_content' ) {

                    var parentComponent     = $scope.getComponentById(component.id),
                        isContentEditable   = false;
                    
                    if ( parentComponent ) {
                        isContentEditable = parentComponent.attr('contenteditable');
                    }

                    // check if component is contenteditable
                    if ( isContentEditable ) {
                    
                        var element         = angular.element("<span>"+value+"</span>"),
                            haveComponents  = false;

                        // loop all child nodes
                        angular.forEach(element.find("*"), function(child) {

                            var childNode   = angular.element(child),
                                componentId = childNode.attr('ng-attr-component-id');

                            // if child is a component
                            if ( componentId ) {

                                childNode.replaceWith("<span id=\"ct-placeholder-"+componentId+"\"></span>");
                                haveComponents = true;
                            }
                        });

                        // update tree value
                        if ( haveComponents ) {
                            item.options[parameter] = element.prop('innerHTML');
                        } 
                        else {
                            item.options[parameter] = value;
                        }
                    }
                    else {
                        item.options[parameter] = value;
                    }
                } 
                // handle options other than "ct_content"
                else {
                    // not CSS options
                    if ( parameter == 'selector'    || 
                         parameter == 'ct_id'       || 
                         parameter == 'ct_parent'   ||
                         parameter == 'classes'     || 
                         ( parameter == "url" && component.isShortcode ) ) 
                    {
                        item.options[parameter] = value;
                    }
                    else 
                        // handle state's option
                        if (state) {

                            if ((value === "" || value === undefined) && $scope.allowedEmptyOptions.indexOf(parameter)===-1) {
                                delete item.options[state][parameter];
                            }
                            else {
                                item.options[state][parameter] = value;
                            }
                        }
                    }
        }
    }


    /**
     * Change component tag
     * 
     * @since 0.3.1
     * @author Ilya K.
     */

    $scope.updateTreeComponentTag = function(id, item, newTag) {
        
        // change tag
        item.name = newTag;

        if (newTag == "ct_shortcode") {
            item.options['ct_shortcode'] = true;
        }

        // update active name
        if ( $scope.component.active.id == id ) {
            $scope.component.active.name = newTag;
        }

        // lets also update nicename if exists
        
        if($scope.component.options[id]['nicename'] && $scope.component.options[id]['nicename'].trim() !== '') {
            $scope.component.options[item.id]['nicename'] = $scope.calcDefaultComponentTitle(item);
            item.options.nicename = $scope.component.options[item.id]['nicename'];
            $scope.updateBreadcrumbs(item.id);
        }

        // rebuild DOM and Tree Navigator
        var timeout = $timeout(function() {
            
            $scope.rebuildDOM(id);
            $timeout.cancel(timeout);
        }, 0, false);
    }

    
    /**
     * Apply component's default options
     * 
     * @since 0.1.7
     */
    
    $scope.applyComponentDefaultOptions = function(id, componentName, component) {

        if ($scope.log) {
            console.log('applyComponentDefaultOptions()', id, componentName);
        }

        // init component options
        if ( !$scope.component.options[id] ) {
            $scope.component.options[id] = {};
        }
        if ( !$scope.component.options[id]['original'] ) {
            $scope.component.options[id]['original'] = {};
        }
        if ( !$scope.component.options[id]['id'] ) {
            $scope.component.options[id]['id'] = {};
        }
            
        // set default options
        for(var name in $scope.defaultOptions[componentName]) { 

            if ($scope.defaultOptions[componentName].hasOwnProperty(name)) {
                
                var value = $scope.defaultOptions[componentName][name];

                // update 'original'
                $scope.component.options[id]['original'][name] = value;

                // load web fonts
                if ( name == "font-family"||name.indexOf("font-family")>-1 ) {
                    $scope.loadWebFont(value);
                }
            }
        }

        // set component selector
        if (!$scope.component.options[id]['selector']) {
            $scope.component.options[id]['selector'] = componentName.slice(3) + "-" + id + "-" + CtBuilderAjax.postId;
        }

        // set clone source parameter in case of a dynamic list i.e., repeater element
        if (component && !$scope.component.options[id]['source_id']) {
            $scope.component.options[id]['source_id'] = component.source_id;
        }

        // set component name
        if (!$scope.component.options[id]['name']) {
            $scope.component.options[id]['name'] = componentName;
        }

        // set component category
        if(component && component.options && component.options['ct_category']) {
            $scope.component.options[id]['ct_category'] = component.options['ct_category'];
        }
        // update model
        $scope.component.options[id]['model'] = angular.copy($scope.component.options[id]['original']);



    }

 
    /**
     * Apply components options saved in Components Tree
     * 
     * @since 0.1.8
     * @author Ilya K.
     */
    
    $scope.applyComponentSavedOptions = function(id, componentTreeItem) {

        if ($scope.log) {
            console.log("applyComponentSavedOptions()", id, componentTreeItem);
        }

        // check global fonts and delete if not exist
        $scope.checkGlobalFont(componentTreeItem.options);

        // loop component's states
        for(var stateName in componentTreeItem.options) { 
            if (componentTreeItem.options.hasOwnProperty(stateName)) {
                
                var stateOptions = componentTreeItem.options[stateName];

                // use original options by default
                if (typeof stateOptions === 'object' && stateName != "classes") {

                    if ( !$scope.component.options[id][stateName] ) {
                        $scope.component.options[id][stateName] = {};
                    }

                    // loop state's options
                    for(var optionName in stateOptions) { 
                        if (stateOptions.hasOwnProperty(optionName)) {
                            
                            var optionValue = stateOptions[optionName];

                            // save 'id' options to check later against defaults
                            if ( stateName == "original" ) {
                                $scope.component.options[id]["id"][optionName] = optionValue;
                            }

                            $scope.component.options[id][stateName][optionName] = optionValue;

                            if ($scope.log) {
                                //console.log(stateName, optionName, optionValue);
                            }

                            if ( optionName == "font-family"||optionName.indexOf("font-family")>-1 ) {
                                $scope.loadWebFont(optionValue);
                            }   
                        }
                    }
                }
                else if (typeof stateOptions !== 'object') {

                    if ( stateName == "selector" ) {
                        $scope.component.options[id][stateName] = stateOptions;
                    }
                    else {
                        $scope.component.options[id]['original'][stateName] = stateOptions;
                    }
                }
            }
        }
        
        // update model
        $scope.component.options[id]['model'] = angular.copy($scope.component.options[id]['original']);

        // mark component if built in
        if (componentTreeItem.options.oxy_builtin) {
            $scope.builtinComponents[id] = true;
        }
    }

    $scope.haveNotRegisteredElements = function() {
        
        for(var id in $scope.notRegisteredElements) {
            if ($scope.notRegisteredElements.hasOwnProperty(id) ) {
                if ($scope.getComponentById(id) && id < 100000) { // more then 100000 is outer template
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Apply all options to model based on current class, state and media
     * 
     * @since 0.3.2
     * @author Ilya K.
     */
    
    $scope.applyModelOptions = function(id, tag) {

        if (undefined===id) {
            id = $scope.component.active.id;
        }
        
        if (undefined===tag) {
            tag = $scope.component.active.name;
        }

        if ($scope.log) {
            console.log("applyModelOptions()", id, tag);
        }

        // no model for root
        if (id===0||id==="0") {
            return;
        }
        
        // init options
        if (!$scope.component.options[id]) {
            $scope.component.options[id] = {};
        }

        // clear model
        $scope.component.options[id]['model'] = {}


        if (tag) {
            if (!$scope.defaultOptions[tag] || $scope.defaultOptions[tag]['not-registered'] ) {        
                $scope.component.options[id]['model']['not-registered'] = true;
                $scope.defaultOptions[tag] = {};
                $scope.defaultOptions[tag]['not-registered'] = true;
                $scope.notRegisteredElements[id] = true;
            }
        }

        // apply id's 'original'
        angular.extend( $scope.component.options[id]['model'],
                        $scope.component.options[id]['original'])

        /**
         * ID
         */
        
        if ($scope.isEditing("id")) {
            
            if ($scope.isEditing("media")) {

                $scope.component.options[id]['model'] = {};

                if ($scope.component.options[id]['media'] &&
                    $scope.component.options[id]['media'][$scope.currentMedia]) {
                    $scope.component.options[id]['model'] = angular.copy($scope.component.options[id]['media'][$scope.currentMedia][$scope.currentState] || {})
                }
            }
            else {
               
                if ($scope.isEditing("state")) {
                    $scope.component.options[id]['model'] = angular.copy($scope.component.options[id][$scope.currentState] || {})
                }
            }
        }

        /**
         * Class
         */

        if ($scope.isEditing("class") && typeof($scope.classes[$scope.currentClass]) == "object") {

            $scope.component.options[id]['model'] = angular.copy($scope.classes[$scope.currentClass]['original'] || {});

            if ($scope.isEditing("media")) {

                $scope.component.options[id]['model'] = {};

                if ($scope.classes[$scope.currentClass]['media'] &&
                    $scope.classes[$scope.currentClass]['media'][$scope.currentMedia]) {
                    $scope.component.options[id]['model'] = angular.copy($scope.classes[$scope.currentClass]['media'][$scope.currentMedia][$scope.currentState] || {})
                }

                for(var optionName in $scope.classes[$scope.currentClass][$scope.currentState]) {
                    if ($scope.classes[$scope.currentClass][$scope.currentState].hasOwnProperty(optionName) ) {
                        if ($scope.optionsWhiteListNoMedia.indexOf(optionName)>-1) {
                            $scope.component.options[id]['model'][optionName] = 
                            $scope.classes[$scope.currentClass][$scope.currentState][optionName];
                        }
                    }
                }
            }
            else {
                $scope.component.options[id]['model'] = {};

                if ($scope.classes[$scope.currentClass] &&
                    $scope.classes[$scope.currentClass][$scope.currentState]) {
                    $scope.component.options[id]['model'] = angular.copy($scope.classes[$scope.currentClass][$scope.currentState] || {})
                }
            }
        }

        /**
         * Custom selector
         */

        if ($scope.isEditing("custom-selector") && !$scope.isEditing("class")) {
            
            if ($scope.customSelectors[$scope.selectorToEdit]){
                //$scope.component.options[id]['model'] = angular.copy($scope.customSelectors[$scope.selectorToEdit][$scope.currentState] || {});

                if ($scope.isEditing("media")) {

                    $scope.component.options[id]['model'] = {};

                    if ($scope.customSelectors[$scope.selectorToEdit]['media'] &&
                        $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia]) {
                        $scope.component.options[id]['model'] = angular.copy($scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia][$scope.currentState] || {})
                    }
                }
                else {
                    $scope.component.options[id]['model'] = {};

                    if ($scope.customSelectors[$scope.selectorToEdit] &&
                        $scope.customSelectors[$scope.selectorToEdit][$scope.currentState]) {

                        $scope.component.options[id]['model'] = angular.copy($scope.customSelectors[$scope.selectorToEdit][$scope.currentState] || {})
                    }
                }
            }
        }

        // load fonts
        for(var name in $scope.component.options[id]['model']) {
            if ($scope.component.options[id]['model'].hasOwnProperty(name) && (name=="font-family")||name.indexOf('font-family')>-1) {
                $scope.loadWebFont($scope.component.options[id]['model'][name]);
            }
        }

        // check units
        for(var name in $scope.component.options[id]['model']) {
            if ($scope.component.options[id]['model'].hasOwnProperty(name) && $scope.component.options[id]['model'][name+"-unit"]) {
                
                // only if editing "id"
                if ($scope.isEditing("id") && !$scope.isEditing("media") && !$scope.isEditing("state")) {
            
                    if ($scope.component.options[id]['model'][name+"-unit"] != $scope.defaultOptions[tag][name+"-unit"] &&
                        !$scope.component.options[id]['id'][name] ){
                        
                        delete $scope.component.options[id]['model'][name];
                    }
                }
            }
        }

        if (!$scope.isEditing("custom-selector")) {

            for(var optionName in $scope.component.options[id]['original']) {
                if ($scope.component.options[id]['original'].hasOwnProperty(optionName) ) {
                    if ($scope.optionsWhiteList.indexOf(optionName)===-1) {
                        if ($scope.isEditing("class")&&(optionName=="icon-style"||optionName=="button-style"||optionName=="gradient")) {
                            if ( $scope.classes[$scope.currentClass]['original'] != undefined ) {
                                $scope.component.options[id]['model'][optionName] = 
                                angular.copy($scope.classes[$scope.currentClass]['original'][optionName]);
                            }
                        }
                        else {
                            if ($scope.isEditing("class")&&$scope.optionsWhiteListNoMedia.indexOf(optionName)>-1){
                                // these options already added above
                            }
                            else {
                                $scope.component.options[id]['model'][optionName] = 
                                $scope.component.options[id]['original'][optionName];
                            }
                        }
                    }
                }
            }
        }

        function setBreakPointsFallbacks(mediaOptions) {
            
            for (var stateName in mediaOptions) {
                if (mediaOptions.hasOwnProperty(stateName) ) {
                    var stateOptions = mediaOptions[stateName];

                    for (var optionName in stateOptions) {
                        if (stateOptions.hasOwnProperty(optionName) ) {
                            var modelValue = $scope.component.options[id]['model'][optionName];

                            if (modelValue===""||modelValue===undefined) {
                                $scope.component.options[id]['model'][optionName] = $scope.getClosestBreakpointValue(optionName);
                            }
                        }
                    }
                }
            }
        }
        
        if ($scope.isEditing('id')) {

            setBreakPointsFallbacks($scope.component.options[id]);

            // loop trhough all media options and fallback those in model if needed
            for (var mediaName in $scope.component.options[id]['media']) {
                if ($scope.component.options[id]['media'].hasOwnProperty(mediaName) ) {
                    var mediaOptions = $scope.component.options[id]['media'][mediaName];
                    setBreakPointsFallbacks(mediaOptions);
                }
            }

            // loop trough original and find fallback those in model if needed
            if ($scope.isEditing('media')) {
                for (var optionName in $scope.component.options[id][$scope.currentState]) {
                    if ($scope.component.options[id][$scope.currentState].hasOwnProperty(optionName) ) {
                        var optionValue = $scope.component.options[id][$scope.currentState][optionName];
                        if ($scope.component.options[id]['model'][optionName]===""||$scope.component.options[id]['model'][optionName]===undefined){
                            $scope.component.options[id]['model'][optionName] = optionValue;
                        }
                    }
                }
            }

        }

        if ($scope.isEditing('class')) {

            setBreakPointsFallbacks($scope.component.options[$scope.classes[$scope.currentClass]]);

            // loop trhough all media options and fallback those in model if needed
            for (var mediaName in $scope.classes[$scope.currentClass]['media']) {
                if ($scope.classes[$scope.currentClass]['media'].hasOwnProperty(mediaName) ) {
                    var mediaOptions = $scope.classes[$scope.currentClass]['media'][mediaName];
                    setBreakPointsFallbacks(mediaOptions);
                }
            }    

            // loop trough default and find fallback
            if ($scope.isEditing('media') && $scope.classes[$scope.currentClass][$scope.currentState]) {
                for (var optionName in $scope.classes[$scope.currentClass][$scope.currentState]) {
                    if ($scope.classes[$scope.currentClass][$scope.currentState].hasOwnProperty(optionName) ) {
                        var optionValue = $scope.classes[$scope.currentClass][$scope.currentState][optionName];
                        if ($scope.component.options[id]['model'][optionName]===""||$scope.component.options[id]['model'][optionName]===undefined){
                            $scope.component.options[id]['model'][optionName] = optionValue;
                        }
                    }
                }
            }
        }

        if ($scope.isEditing('custom-selector') && !$scope.isEditing('class') && $scope.customSelectors[$scope.selectorToEdit] !== undefined) {

            setBreakPointsFallbacks($scope.customSelectors[$scope.selectorToEdit]);

            // loop trhough all media options and fallback those in model if needed
            for (var mediaName in $scope.customSelectors[$scope.selectorToEdit]['media']) {
                if ($scope.customSelectors[$scope.selectorToEdit]['media'].hasOwnProperty(mediaName) ) {
                    var mediaOptions = $scope.customSelectors[$scope.selectorToEdit]['media'][mediaName];
                    setBreakPointsFallbacks(mediaOptions);
                }
            }    

            // loop trough default and find fallback
            if ($scope.isEditing('media') && $scope.customSelectors[$scope.selectorToEdit][$scope.currentState]) {
                for (var optionName in $scope.customSelectors[$scope.selectorToEdit][$scope.currentState]) {
                    if ($scope.customSelectors[$scope.selectorToEdit][$scope.currentState].hasOwnProperty(optionName) ) {
                        var optionValue = $scope.customSelectors[$scope.selectorToEdit][$scope.currentState][optionName];
                        if ($scope.component.options[id]['model'][optionName]===""||$scope.component.options[id]['model'][optionName]===undefined){
                            $scope.component.options[id]['model'][optionName] = optionValue;
                        }
                    }
                }
            }
        }

        // Load global defaults
        if (tag=="ct_section") {
            var sectionOptions = [
                        'container-padding-top',
                        'container-padding-top-unit',
                        'container-padding-bottom',
                        'container-padding-bottom-unit',
                        'container-padding-left',
                        'container-padding-left-unit',
                        'container-padding-right',
                        'container-padding-right-unit']
            
            for(var key in sectionOptions) {
                if (sectionOptions.hasOwnProperty(key) ) {
                    optionName = sectionOptions[key];
                    if ($scope.component.options[id]['model'][optionName]==undefined || $scope.component.options[id]['model'][optionName]=="") {
                        $scope.component.options[id]['model'][optionName]=$scope.globalSettings.sections[optionName];
                    }
                }
            }
        }

        // Load grid defaults for classes
        if ($scope.isEditing("class")) {
            var gridOptions = [
                        'grid-column-count',
                        'grid-columns-auto-fit',
                        'grid-column-min-width',
                        'grid-column-max-width',
                        'grid-column-gap',
                        'grid-justify-items',
                        'grid-match-height-of-tallest-child',
                        'grid-row-behavior',
                        'grid-row-count',
                        'grid-row-min-height',
                        'grid-row-max-height',
                        'grid-row-gap',
                        'grid-align-items',
                    ]
            
            for(var key in gridOptions) {
                if (gridOptions.hasOwnProperty(key) ) {
                    optionName = gridOptions[key];
                    if ($scope.component.options[id]['model'][optionName]==undefined || $scope.component.options[id]['model'][optionName]=="") {
                        $scope.component.options[id]['model'][optionName]=$scope.defaultOptions[tag][optionName] || $scope.defaultOptions["all"][optionName];
                    }
                }
            }
        }
    }


    /**
     * Set Component Options for Components Tree, 
     * update CSS and render shortcode if needed
     * 
     * @since 0.1
     * @author Ilya K.
     */

    $scope.setOption = function(id, tag, optionName, isShortcode, notUpdateCSS) {

        // advanced query presets, set to 'custom' in case the advanced query is manualy edited
        if(optionName == 'wp_query_advanced' && $scope.parentScope.dialogForms['advancedquery']) {
            $scope.component.options[$scope.component.active.id].model['wp_query_advanced_preset'] = 'Custom Settings';
            $scope.setOption($scope.component.active.id, $scope.component.active.name, 'wp_query_advanced_preset');
        }

        $scope.cancelDeleteUndo();
        
        if ($scope.log) {
            console.log("setOption() '" + optionName + "' for '" + tag + "' id:" + id + " tag to '" + $scope.component.options[id]['model'][optionName] + "'");
            $scope.functionStart("setOption()");
        }

        if ($scope.component.options[id]['model'][optionName]==null) {
            $scope.component.options[id]['model'][optionName] = "";
        }

        $scope.adjustResizeBox();

        // disable selector detector mode
        if ($parentScope.disableSelectorDetectorMode) {
            $parentScope.disableSelectorDetectorMode();
        }

        if(optionName && optionName.indexOf('aos-') === 0 && optionName !== 'aos-enable' && optionName !== 'aos-type') {
            var item = $scope.getComponentById(id);
            item.attr('data-'+optionName, $scope.component.options[id]['model'][optionName]);
        }

        // add "data-aos" AOS attribute only when at least some aos-type defined, empty "data-aos" cause lags
        if (optionName == 'aos-type') {
            var item = $scope.getComponentById(id);
            var dataAOS = $scope.getOption('aos-type',id)||$scope.pageSettingsMeta['aos']['type']||$scope.pageSettings['aos']['type']||$scope.globalSettings['aos']['type'];
            if (dataAOS) {
                item.attr('data-aos', dataAOS);
            }
            else {
                // clear data
                item.removeAttr('data-aos');
            }
        }

        // update 'id' options
        if ( $scope.isEditing("id") && !$scope.isEditing("media") && !$scope.isEditing("state")) {
            
            if (typeof($scope.component.options[id]['model'][optionName]) !== 'undefined') {
                // init 'id' options if not defined
                if (!$scope.component.options[id]["id"]) {
                    $scope.component.options[id]["id"] = {};
                }
                $scope.component.options[id]["id"][optionName] = angular.copy($scope.component.options[id]['model'][optionName]);
            }
            else {
                if($scope.component.options[id]['model'][optionName]=="") {

                    // delete empty values and set model to default    
                    if ($scope.allowedEmptyOptions.indexOf(optionName) === -1) {
                        
                        delete $scope.component.options[id]["id"][optionName];

                        // check unit options
                        if ( $scope.component.options[id]["model"][optionName+'-unit'] ) {
                            if ( $scope.component.options[id]["model"][optionName+'-unit'] == $scope.defaultOptions[tag][optionName+'-unit'] ){
                                 
                                 $scope.component.options[id]["model"][optionName]       = $scope.defaultOptions[tag][optionName];
                                 $scope.component.options[id]["original"][optionName]    = $scope.defaultOptions[tag][optionName];
                            }
                        }
                        else 
                            if (optionName!="ct_content") {
                                $scope.component.options[id]["model"][optionName]       = $scope.defaultOptions[tag][optionName];
                                $scope.component.options[id]["original"][optionName]    = $scope.defaultOptions[tag][optionName];
                            }
                    }
                    else {
                        if (!$scope.component.options[id]["id"]) {
                            $scope.component.options[id]["id"] = {};
                        }
                        
                        $scope.component.options[id]["id"][optionName] = $scope.component.options[id]['model'][optionName];
                    }
                }
            }
        }

        // check for empty class name
        if ( $scope.isEditing('class') && $scope.currentClass == "" ) {
            alert("Please choose the class from the list or set a new one.");
            $scope.functionEnd("setOption()");
            return false;
        }

        // make sure column width is no more than 100 and no less than 0
        // deprecated, delete in next bug fix release
        if ( tag == "ct_column" && optionName == "width" ) {
            if ( $scope.component.options[id]['model'][optionName] > 100 ) {
                $scope.component.options[id]['model'][optionName] = 100;
            }
            if ( $scope.component.options[id]['model'][optionName] < 0 ) {
                $scope.component.options[id]['model'][optionName] = 0;
            }
        }

        // validate Custom CSS
        if ( optionName && optionName == "custom-css" ) {
            var css = $scope.component.options[id]['model'][optionName],
                valid = true,
                text = "";

            if ( css.length > 0 ) {

                if (css.indexOf("{")>-1 || css.indexOf("}")>-1) {
                    valid = false;
                    text = "The Custom CSS field contains one or more curly braces. The CSS will not be saved until all curly braces are removed.";
                }
                else if (css.indexOf(":")==-1) {
                    valid = false;
                    text = "The Custom CSS field contains invalid CSS. The CSS will not be saved until this is corrected.";
                }

                if (!valid) {
                    jQuery(".oxygen-code-error-container", $parentScope.oxygenUIElement).show().html(text);
                    return false;
                }
            }
            
            jQuery(".oxygen-code-error-container", $parentScope.oxygenUIElement).hide().html("");
        }

        // handle fake "border-all-option"
        if ( optionName && optionName.indexOf("border-all") > -1 ) {
            // classesCached = True to avoid duplicated cache generation
            $newOptValue = $scope.component.options[id]['model'][optionName]

            $scope.setOptionModel(optionName.replace("-all-","-top-"),      $newOptValue, undefined, undefined, false, true);
            $scope.setOptionModel(optionName.replace("-all-","-right-"),    $newOptValue, undefined, undefined, false, true);
            $scope.setOptionModel(optionName.replace("-all-","-bottom-"),   $newOptValue, undefined, undefined, false, true);
            $scope.setOptionModel(optionName.replace("-all-","-left-"),     $newOptValue, undefined, undefined, false, true);
            
            // set value to "border-all-option" again
            $scope.component.options[id]['model'][optionName] = $newOptValue;

            // classesCached = false to generate cache at the end
            $scope.classesCached = false;
            $scope.functionEnd("setOption()");
        }

        // make sure flex direction is not turned on for display block and vice versa
        if ( optionName == "flex-direction" && (
            $scope.component.options[id]['model'][optionName] == "column" ||
            $scope.component.options[id]['model'][optionName] == "row") ) {
            var timeout = $timeout(function() {
                $scope.setOptionModel("display","flex",id,tag)
                $timeout.cancel(timeout);
            }, 0, false);
        }
        if ( optionName == "display" && $scope.component.options[id]['model'][optionName] != "" && $scope.component.options[id]['model'][optionName] != "flex" ) {
            var timeout = $timeout(function() {
                $scope.setOptionModel("flex-direction","",id,tag);
                // $scope.unsetOptions(["flex-direction"]);
                $timeout.cancel(timeout);
            }, 0, false);
        }

        // grid
        if ( optionName == "display" && tag == "oxy_gallery" && $scope.component.options[id]['model'][optionName] === "grid" ) {
            var timeout = $timeout(function() {
                $scope.setOptionModel("grid","true",id,tag);
                $scope.setOptionModel("layout","false",id,tag);
                $timeout.cancel(timeout);
            }, 0, false);
        }
        if ( optionName == "display" && tag == "oxy_gallery" && $scope.component.options[id]['model'][optionName] != "" && $scope.component.options[id]['model'][optionName] !== "grid" ) {
            $scope.unsetOptions(["layout","grid"]);
        }
        if ( optionName == "layout" && 
            ($scope.component.options[id]['model'][optionName] == "flex"||$scope.component.options[id]['model'][optionName] == "masonry") &&
            $scope.component.options[id]['model']['display'] == "grid" ) {
            var timeout = $timeout(function() {            
                $scope.unsetOptions(["display"]);
                $timeout.cancel(timeout);
            }, 0, false);
        }
        // update model if container padding unset
        if (["container-padding-top","container-padding-bottom","container-padding-left","container-padding-right"].indexOf(optionName)>=0
            &&$scope.component.options[id]['model'][optionName]=="") {
            var timeout = $timeout(function() {
                $scope.applyModelOptions(id, tag);
                $scope.parentScope.safeApply();
                $timeout.cancel(timeout);
            }, 0, false);
        }

        // update render Gallery component on Layout change automatically
        if ((["layout","link","lightbox","image_aspect_ratio","images_per_row","space_around_image","image_min_width",
            "gallery_captions","caption_color","caption_background_color","gallery_captions_only_hover","hide_captions_below",
            "image_opacity_hover","image_opacity","transition_duration","gallery_thumbnail_size","set_image_fit_to_cover"].indexOf(optionName)>-1)&&tag=="oxy_gallery") {
            var timeout = $timeout(function() {
                $scope.renderComponentWithAJAX('oxy_render_gallery');
                $timeout.cancel(timeout);
            }, 0, false);
        }

        // rebuild Elements API components when certain options change
        if ($scope.componentsTemplates !== undefined && $scope.componentsTemplates[tag]) {
            componentTemplate = $scope.componentsTemplates[tag];

            if (componentTemplate.rebuildTriggerOptions && componentTemplate.rebuildTriggerOptions.indexOf(optionName)>-1) {
                var timeout = $timeout(function() {
                    $scope.renderComponentWithAJAX('oxy_render_' + tag, id);
                    $timeout.cancel(timeout);
                }, 0, false);
            }
        }

        // update Tabs oxy-tabs-contents-wrapper data to keep those connected
        if (optionName=="selector"&&tag=="oxy_tabs_contents") {
            var timeout = $timeout(function() {
                var tabsSelector    = jQuery($scope.getComponentById(id)).data('oxy-tabs-wrapper'),
                    tabsID          = jQuery("#"+tabsSelector).attr('ng-attr-component-id');

                $scope.setOptionModel('tabs_contents_wrapper', $scope.component.options[id]["selector"], tabsID, "oxy_tabs");

                $scope.$apply();
                $timeout.cancel(timeout);
            }, 0, false);
        }
        if (optionName=="selector"&&tag=="oxy_tabs") {
            var timeout = $timeout(function() {
                var tabsSelector    = jQuery($scope.getComponentById(id)).data('oxy-tabs-contents-wrapper'),
                    tabsID          = jQuery("#"+tabsSelector).attr('ng-attr-component-id');

                $scope.setOptionModel('tabs_wrapper', $scope.component.options[id]["selector"], tabsID, "oxy_tabs_contents");

                $scope.$apply();
                $timeout.cancel(timeout);
            }, 0, false);
        }

        // render component on "dont_render" param update for widget. For shortcode this is done automatically 
        if (optionName=="dont_render"&&tag=="ct_widget") {
            var timeout = $timeout(function() {
                $scope.renderWidget(id,false)
            $timeout.cancel(timeout);
            }, 0, false);
        }
        
        // rebuild social icons on network update
        if ( tag == "oxy_social_icons" && optionName && $scope.socialIcons.networks.indexOf(optionName.replace('icon-','')) >= 0 ) {
            if (
                  $scope.component.options[id]['model'][optionName] == "" ||
                ( $scope.component.options[id]['model'][optionName] != "" && !$scope.component.options[id]['original'][optionName] )
            ) {
                var timeout = $timeout(function() {
                    $scope.rebuildDOM(id);
                    $timeout.cancel(timeout);
                }, 0, false);
            }
        }

        // rebuild progress bar to show updated animation
        if ( ["progress_bar_animation_stripes","progress_bar_animate_width","progress_bar_animation_stripes_duration","progress_bar_animation_width_duration"].indexOf(optionName) > -1 ) {
            var timeout = $timeout(function() {
                $scope.rebuildDOM(id);
                $timeout.cancel(timeout);
            }, 0, false);
        }

        if (optionName=="soundcloud_url") {
            $scope.getSoundCloudTrackID($scope.component.options[id]['model'][optionName]);
        }

        // backward compatibility for hide/show in sticky checkboxes to be radio buttons since 2.1
        if ( optionName == "hide_in_sticky" && $scope.component.options[id]['model'][optionName] == "yes" 
             && $scope.getOption("show_in_sticky_only",id)=='yes') {
                $scope.setOptionModel("show_in_sticky_only","",id,tag)
        }
        if ( optionName == "show_in_sticky_only" && $scope.component.options[id]['model'][optionName] == "yes" 
             && $scope.getOption("hide_in_sticky",id)=='yes') {
                $scope.setOptionModel("hide_in_sticky","",id,tag)
        }

        var component = {
            id: id,
            tag: tag,
            optionName: optionName,
            isShortcode: isShortcode,
            isData: tag.lastIndexOf('ct_data', 0) === 0 // ugly hack
        }

        // update custom-selector options
        if ( $scope.isEditing('custom-selector') ) {

            //$scope.selectorDetector.mode = false;
            
            var parameter   = component.optionName,
                value       = $scope.component.options[component.id]['model'][parameter];
            
            // don't include not CSS options
            if ( parameter == 'selector'    || 
                 parameter == 'ct_id'       || 
                 parameter == 'ct_parent'   ||
                 parameter == 'ct_content'  || 
                 parameter == 'classes'     || 
                 ( parameter == "url" && component.isShortcode ) ) 
            {
               // nothing here
            }
            else {
                $scope.updateCustomSelectorValue(parameter, value);
                delete $scope.cache.classStyles[$scope.currentClass];
                $scope.outputCSSOptions();
                $scope.unsavedChanges();

                $scope.functionEnd("setOption()");
                return;
            }
        }
        // update Components Tree
        else if ( optionName !== undefined ) {

            $scope.findComponentItem($scope.componentsTree.children, id, $scope.updateTreeComponentOption, component);
            // if fake "hover_" option also update actual option for hover state
            if ( optionName.indexOf("hover_")>=0 && tag != "oxy_posts_grid") {
                component.optionName = "fake_" + component.optionName;
                $scope.findComponentItem($scope.componentsTree.children, id, $scope.updateTreeComponentOption, component);
            }

        } else {
            // update all options if option name is not set
            $scope.findComponentItem($scope.componentsTree.children, id, $scope.updateTreeComponentOptions, component);
        }

        $scope.unsavedChanges();

        // refreshHard AOS object if any element AOS setting were updated
        if ( optionName !== undefined && optionName.indexOf('aos-') === 0 ) {
            if (typeof(AOS)!=='undefined') {
                var timeout = $timeout(function() {
                    AOS.refreshHard()
                    $timeout.cancel(timeout);
                }, 0, false);
            }
        }
        // for any other options do regular AOS refresh
        else {
            if (typeof(AOS)!=='undefined') {
                var timeout = $timeout(function() {
                    AOS.refresh()
                    $timeout.cancel(timeout);
                }, 0, false);
            }
        }

        if ( optionName == 'aos-type' ) {
            var currentElement = $scope.getComponentById(id);
            var timeoutAOSType = $timeout(function() {
                currentElement
                    .css('transitionDuration', '0s') // set duration to 0 skip the out animation
                    .removeClass('aos-animate');
                $timeout.cancel(timeoutAOSType);
            }, 100, false);
            var timeoutAOSType2 = $timeout(function() {
                currentElement
                    .css('transitionDuration', '') // unset duration to inital value
                    .addClass('aos-animate');
                $timeout.cancel(timeoutAOSType2);
            }, 200, false);
        }

        // render shortcode
        if ( isShortcode ) {
            $scope.renderShortcode(id, tag);
        }

		// render data component
		if ( component.isData ) {
			$scope.renderDataComponent(id, tag);
		}

        // don't update CSS options when editing content
        if ( optionName == "ct_content" || notUpdateCSS ) {
            $scope.functionEnd("setOption()");
            return;
        }

        if(tag === 'ct_video' && optionName === 'src') {

            if($scope.component.options[id]['model'][optionName].indexOf('[oxygen') !== -1) {
                // resolve url from the oxy shortcode
            
                var callback = function(contents) {
                    $scope.setOptionModel("embed_src",$scope.getYoutubeVimeoEmbedUrl(contents.trim()),id,tag);
                }

                $scope.applyShortcodeResults(id, $scope.component.options[id]['model'][optionName], callback);

            }
            else {
                $scope.setOptionModel("embed_src",$scope.getYoutubeVimeoEmbedUrl($scope.component.options[id]['model'][optionName]),id,tag);
            }
        }
        if(optionName === 'background-image' || (tag === 'ct_image' && optionName === 'src')) {
            // if transitioning from a dynamic shortcode to a static url
            if($scope.component.options[id]['model'][optionName].indexOf('[oxygen') !== -1) {
                // register it as a dynamic data value
                $scope.component.options[id]['model'][optionName+'dynamic'] = true;
                $scope.setOption(id, tag, optionName+'dynamic', false, true);
                
            } else if($scope.component.options[id]['original'][optionName+'dynamic'] === true && $scope.component.options[id]['model'][optionName].indexOf('[oxygen') === -1) {
                // it was dynamic, but now its static
                $scope.component.options[id]['model'][optionName+'dynamic'] = false;
                $scope.setOption(id, tag, optionName+'dynamic', false, true);

                var component = $scope.getComponentById(id);

                var oxyList = component.closest('.oxy-dynamic-list');
            
                if(oxyList.length > 0 && !oxyList.hasClass('oxy-dynamic-list-edit')) {
                    $scope.updateRepeaterQuery(parseInt(oxyList.attr('ng-attr-component-id')));
                }
                else {
                    $scope.rebuildDOM(id);
                }
            }
            
        }
        
        // update units only when editing media and if unit param actually exist
        // container padding only exist in global settings defaults
        if ( optionName && $scope.isEditing('media') && 
           ( $scope.defaultOptions['all'][optionName+"-unit"] || optionName.indexOf("container-padding"> -1) )
        ) {
            $scope.applyModelOptions(id, tag);
        }

        // update styles
        $scope.outputCSSOptions(id);

        // if there is no active selector on the current component, have it display ID as current selector
        if($scope.isNotSelectedYet(id) && typeof(optionName) !== 'undefined')
            $scope.switchEditToId(true);

        $scope.functionEnd("setOption()");
    }


    /**
     * Set option model value and update Tree
     *
     * @since 0.3.0
     * @author Ilya K.
     */
    
    $scope.setOptionModel = function(optionName, optionValue, id, name, notUpdateCSS, classesCached) {

        if (undefined === id) {
            id = $scope.component.active.id;
        }
        if (undefined === name) {
            name = $scope.component.active.name;
        }
        if (undefined === classesCached) {
            classesCached = false;
        }

        //if($scope.isEditing('class')) {
            $scope.classesCached = classesCached;
        //}

        if ($scope.log) {
            console.log("setOptionModel()", optionName, optionValue, id, name, notUpdateCSS);
            $scope.functionStart("setOptionModel()");
        }

        if (optionName.indexOf('.')>-1) {
            optionName = optionName.split('.');
            if (optionName.length==2) {
                if(!$scope.component.options[id]['model'][optionName[0]]) {
                    $scope.component.options[id]['model'][optionName[0]] = {};
                }
                console.log(optionName[0],optionName[1])
                $scope.component.options[id]['model'][optionName[0]][optionName[1]] = optionValue;
            }
            if (optionName.length==3) {
                if(!$scope.component.options[id]['model'][optionName[0]]) {
                    $scope.component.options[id]['model'][optionName[0]] = {};
                    $scope.component.options[id]['model'][optionName[0]][optionName[1]] = {};
                }
                if(!$scope.component.options[id]['model'][optionName[0]][optionName[1]]) {
                    $scope.component.options[id]['model'][optionName[0]][optionName[1]] = {};
                }
                $scope.component.options[id]['model'][optionName[0]][optionName[1]][optionName[2]] = optionValue;
            }
            $scope.setOption(id, name, optionName[0], false, notUpdateCSS);
        }
        else {
            // update model
            $scope.component.options[id]['model'][optionName] = optionValue;
            // update Tree
            $scope.setOption(id, name, optionName, false, notUpdateCSS);
        }

        $scope.functionEnd("setOptionModel()");
    }


    /**
     * Get option from model
     *
     * @since 0.3.0
     * @author Ilya K.
     * @return {string}
     */
    
    $scope.getOption = function(optionName, id) {

        if (undefined === id) {
            id = $scope.component.active.id;
        }
        
        if (optionName.indexOf('.')>-1) {
            optionName = optionName.split('.');
            if (optionName.length==2 && $scope.component.options[id]['model'][optionName[0]] && $scope.component.options[id]['model'][optionName[0]][optionName[1]]) {
                return $scope.component.options[id]['model'][optionName[0]][optionName[1]];
            }
            if (optionName.length==3 && $scope.component.options[id]['model'][optionName[0]] && $scope.component.options[id]['model'][optionName[0]][optionName[1]] && $scope.component.options[id]['model'][optionName[0]][optionName[1]][optionName[2]]) {
                return $scope.component.options[id]['model'][optionName[0]][optionName[1]][optionName[2]];
            }
        }
        else if ( $scope.component.options[id] && $scope.component.options[id]['model'] && $scope.component.options[id]['model'][optionName] !== undefined ) {
            return $scope.component.options[id]['model'][optionName];
        }
        else {
            return "";
        }
    }


    /**
     * Get Component's CSS options by id
     * 
     * @since 0.1
     * @author Ilya K.
     * @return {Array} [key values pairs of CSS properties]
     */

    $scope.getCSSOptions = function(id, stateName, customOptions, componentName, breakPointName, breakpointOptions) {
        
        if (!$scope.defaultOptions[componentName]) {
            $scope.defaultOptions[componentName] = {};
            $scope.defaultOptions[componentName]['not-registered'] = true;
        }
        
        var customCss = [],
            options = {};

        // use passed options (for classes and custom selectors)
        if ( customOptions ) {
            options = angular.copy(customOptions);
        }
        // or get component options
        else {
            if (undefined === id) {
                return {};
            }
            if (undefined === stateName) {
                stateName = $scope.currentState;
            }
            if ($scope.component.options[id]) {
                options = angular.copy($scope.component.options[id][stateName]);
            }
        }

        options = options || {};

        
        if ($scope.log) {
            console.log("getCSSOptions()", id, stateName, options);
        }

        // handle options with units 
        for(var name in options) { 
            if (options.hasOwnProperty(name) && !(componentName == 'ct_video' && name == 'padding-bottom')) {

                if (name.indexOf('-unit') < 0) {

                    if (!componentName) {
                        // #ID
                        if (id && id > 0) {
                            componentName = $scope.component.options[id].name;    
                        }
                        // .class
                        else {
                            componentName = "all";
                        }
                    }

                    if (name == 'container-padding-top'||
                        name == 'container-padding-bottom'||
                        name == 'container-padding-left'||
                        name == 'container-padding-right') {
                        var unit = ( options[name+'-unit'] ) ? options[name+'-unit'] : $scope.globalSettings.sections[name+'-unit'];
                        if (!options[name+'-unit'] && breakPointName && breakpointOptions) {
                            unit = ( $scope.getClosestBreakpointValue(name+'-unit', breakPointName, breakpointOptions) ) ? $scope.getClosestBreakpointValue(name+'-unit', breakPointName, breakpointOptions)  : $scope.defaultOptions[componentName][name+'-unit'];
                        }
                        if ( options[name] ) {
                            options[name] += unit;
                        }
                    }
                    else {
                        // skip options with no units
                        if ( undefined == $scope.defaultOptions[componentName][name+'-unit'] ) {
                            continue;
                        }

                        if (options[name+'-unit'] == 'auto') {
                            options[name] = 'auto';
                        }
                        else {
                            var unit = ( options[name+'-unit'] ) ? options[name+'-unit'] : $scope.defaultOptions[componentName][name+'-unit'];
                            if (!options[name+'-unit'] && breakPointName && breakpointOptions) {
                                unit = ( $scope.getClosestBreakpointValue(name+'-unit', breakPointName, breakpointOptions) ) ? $scope.getClosestBreakpointValue(name+'-unit', breakPointName, breakpointOptions)  : $scope.defaultOptions[componentName][name+'-unit'];
                            }
                            if ( options[name] ) {
                                options[name] += unit;
                            }
                        }
                    }

                    delete options[name+'-unit'];
                }
                else {
                    if (options[name] == 'auto') {
                        options[name.replace("-unit", "")] = 'auto';
                    }
                }
            }
        }

        // delete all -unit options
        for(var name in options) { 
            if (options.hasOwnProperty(name) && name.indexOf("-unit") > 0) {
                delete options[name];
            }
        }

        // handle background-position option
        if ( options['background-position-top'] || options['background-position-left'] ) {

            var top   = options['background-position-top'] || "0%",
                left  = options['background-position-left'] || "0%";
            
            options['background-position'] = left;
            options['background-position'] += " " + top;
        }

        // remove fake properties
        options['background-position-top'] = null;
        options['background-position-left'] = null;

        // handle background-size option
        if ( options['background-size'] == "manual" ) {

            var width   = options['background-size-width'] || "auto",
                height  = options['background-size-height'] || "auto";
            
            options['background-size'] = width;
            options['background-size'] += " " + height;
        }

        // remove fake properties
        options['background-size-width'] = null;
        options['background-size-height'] = null;

        for(var name in options) { 
            if (options.hasOwnProperty(name) && name.indexOf("font-family") > -1 && options[name][0] == 'global') {
                if ( customOptions ) {
                    options[name] = $scope.getGlobalFont( options[name][1] );
                }
                else {
                    options[name] = $scope.getComponentFont(id, false, stateName); // TODO: add support for custom selectors
                }
            }
        }

        
        return options;
    }


    /**
     * Check if current component option is different from original
     *
     * @since 0.1.4
     */
    
    $scope.checkOptionChanged = function(id, name) {

        if ($scope.log) {
            //console.log("checkOptionChanged", id, name);
        }

        // TODO: make it work with custom selectors
        if ( $scope.isEditing('custom-selector') ) {
            return false;
        }

        var original = false,
            current  = false;

        // check if changed in current id's state
        if ( $scope.isEditing('state') && $scope.isEditing('id') ) {
        
            // get current state option value
            if ( $scope.component.options[id][$scope.currentState] ) {
                current = $scope.component.options[id][$scope.currentState][name];
            }
            
            // no state option
            if ( ! current ) {
                return false;
            }

            original = $scope.component.options[id]['original'][name];

            // check global fonts
            if ( name == "font-family" ) {
                if (    current && 
                        current[0]  == 'global' && 
                        original[0] == 'global' && 
                        current[1] == original[1] 
                    ) {
                    
                    return false;
                }
            }

            // check and return
            if ( original && current != original ) {
                return "ct-option-different";
            }
        }
        // check if changed in current class
        else {

            if ( $scope.classes[$scope.currentClass] && 
                 $scope.classes[$scope.currentClass][$scope.currentState] &&
                 $scope.classes[$scope.currentClass][$scope.currentState][name] ) 
            {
                return "ct-option-different";
            }
        }
    }


    /**
     * Check what user is currently editing
     *
     * @since 0.1.7
     * @return {bool}
     * @author Ilya K.
     */

    $scope.isEditing = function(query) {

        switch (query) {
            
            case "id" :
                return ( $scope.currentClass === false && $scope.component.active.id >= 0 ) ? true : false;
                break;

            case "class" :
                return ( $scope.currentClass !== false ) ? true : false;
                break;

            case "state" :
                return ( $scope.currentState != "original" ) ? true : false;
                break;

            case "pseudo-element" :
                return ( $scope.isPseudoElement($scope.currentState) ) ? true : false;
                break;

            case "custom-selector" :
                return ( $scope.selectorToEdit !== false ) ? true : false;
                break;

            case "style-sheet" :
                return ( $scope.stylesheetToEdit !== false && typeof($scope.stylesheetToEdit) !== 'undefined' ) ? true : false;
                break;

            case "media" :
                return ( $scope.currentMedia !== "default" ) ? true : false;
                break;

            default:
                return false;
        }
    }


    /**
     * Switch editing state to 'id'
     *
     * @since 0.1.7
     * @author Ilya K.
     */
    
    $scope.switchEditToId = function(explicitly) {
        
        if ($scope.log) {
            console.log('switchEditToId()', $scope.component.active.id)
        }

        var isEditingId = $scope.isEditing('id');
        
        if(explicitly !== true)
            $scope.setCustomSelectorToEdit(false);
        
        if(!$scope.activeSelectors[$scope.component.active.id] && explicitly !== true) {
            $scope.setCurrentClass(false);
        }

        // if done explicitly via selecting from the selectors dropdown
        if(typeof(explicitly) !== 'undefined' && explicitly === true) {
            $scope.activeSelectors = $scope.activeSelectors || {};
            $scope.activeSelectors[$scope.component.active.id] = false;
            $scope.setCurrentClass(false);
        }

        $scope.switchState("original");
        $scope.showClasses = false;

        if ( isEditingId ) {
            return false;
        }
    }


    /** 
     * Switch editing state to 'id' and media to 'default'
     *
     * @since 1.0.1
     * @author Ilya K.
     */
    
    $scope.setEditingStateToDefault = function(media) {

        if ($scope.log) {
            console.log('setEditingStateToDefault()')
        }
        
        //if(!$scope.isEditing("id")) {
            $scope.switchEditToId(true);
        //}
        
        if(!media) {
            $scope.setCurrentMedia("default");
        }

        // safely apply scope
        var timeout = $timeout(function() {
            $scope.$apply();
            $timeout.cancel(timeout);
        }, 0, false);
    }


    /**
     * Called when any of the page settings changed
     *
     * @since 0.2.3
     */
    
    $scope.pageSettingsUpdate = function() {

        // update page container width media size
        var pageWidth = $scope.getPageWidth();
        
        $scope.mediaList['page-width'] = {
            maxSize: pageWidth + "px",
            title: "Page container (" + pageWidth + "px) and below"
        };
        
        $scope.mediaListAbove['page-width'] = {
            minSize: (pageWidth + 1) + "px",
            title: "Above page container (" + pageWidth + "px)"
        };

        // update overlay header body class
        if ($scope.getPageSetting('overlay-header-above')&&$scope.getPageSetting('overlay-header-above')!='never') {
            jQuery('body').addClass('oxy-overlay-header');
        }
        else {
            jQuery('body').removeClass('oxy-overlay-header');
        }
        
        // update cache
        $scope.updateAllComponentsCacheStyles();
        
        // output CSS
        $scope.outputCSSOptions();
        $scope.outputPageSettingsCSS();

        // Adjust viewport
        $parentScope.adjustViewportContainer();
    }
    
    /**
     * Called when page's page width is changed changed
     * Validate the page width against the tablet breakpoint
     *
     * @since 3.2
     * @author Abdelouahed E.
     */
    
    $scope.pagePageWidthUpdate = function() {
        // Allow page's page width to be empty
        if( $scope.pageSettingsMeta['max-width'] !== '' ) {
            var width = parseInt($scope.pageSettingsMeta['max-width']) || 0;
            var minWidth = $scope.getBreakPointWidth('tablet')+1;
            
            if( width < minWidth ){
                $scope.showNoticeModal("<div>Page width value must be greater than or equal to " + minWidth + "</div>");
                // Revert to previous breakpoint value if the width is more than the allowed minimum
                $scope.pageSettingsMeta['max-width'] = $scope.previousBreakPointsValues['page-width']['page'];
                return;
            }
        }
        
        // Store the new value as new valid page's page width
        $scope.previousBreakPointsValues['page-width']['page'] = $scope.pageSettingsMeta['max-width'];
        
        $scope.pageSettingsUpdate();
		$scope.unsavedChanges();
    }
    
    /**
     * Called when global page width is changed changed
     * Validate the page width against the tablet breakpoint
     *
     * @since 3.2
     * @author Abdelouahed E.
     */
    
    $scope.globalPageWidthUpdate = function() {
        var width = parseInt($scope.globalSettings['max-width']) || 0;
        var minWidth = $scope.getBreakPointWidth('tablet')+1;
        
        if( width < minWidth ){
            $scope.showNoticeModal("<div>Page width value must be greater than or equal to " + minWidth + "</div>");
            // Revert to previous breakpoint value if the width is more than the allowed minimum
            $scope.globalSettings['max-width'] = $scope.previousBreakPointsValues['page-width']['global'];
            return;
        }
        
        // Store the new value as new valid global page width
        $scope.previousBreakPointsValues['page-width']['global'] = $scope.globalSettings['max-width'];
        
        $scope.pageSettingsUpdate();
		$scope.unsavedChanges();
    }
    
    /**
     * Called when any of the breakpoints changed
     * Change mediaList and MediaListAbove and regenerate CSS cache
     *
     * @since 3.2
     * @author Abdelouahed E.
     */
    
    $scope.breakPointsUpdate = function(id) {
        var width = $scope.getBreakPointWidth(id), minWidth, maxWidth, name, validate = true, error = false;
        
        switch(id) {
            case 'tablet':
                minWidth = $scope.getBreakPointWidth('phone-landscape')+1;
                maxWidth = $scope.getPageWidth()-1;
                name = 'Tablet';
                break;
                
            case 'phone-landscape':
                minWidth = $scope.getBreakPointWidth('phone-portrait')+1;
                maxWidth = $scope.getBreakPointWidth('tablet')-1;
                name = 'Landscape';
                break;
                
            case 'phone-portrait':
                minWidth = 320;
                maxWidth = $scope.getBreakPointWidth('phone-landscape')-1;
                name = 'Portrait';
                break;
                
            default:
                validate = false;
        }
        
        if( validate ){
            if( width < minWidth ){
                error = "<div>" + name + " value must be greater than or equal to " + minWidth + "</div>";
            } else 
            if( width > maxWidth ) {
                error = "<div>" + name + " value must be less than or equal to " + maxWidth + "</div>";
            }
            
            if( error ){
                // Revert to previous breakpoint width if the entered value is invalid
                $scope.globalSettings.breakpoints[id] = $scope.previousBreakPointsValues[id];
                $scope.showNoticeModal(error);
                return;
            }
            
            // Store the new value as new valid breakpoint value
            $scope.previousBreakPointsValues[id] = $scope.globalSettings.breakpoints[id];
        }
        
        $scope.mediaList[id] = {
            maxSize: (width - 1) + "px",
            title: "Less than " + width + "px"
        };
        
        $scope.mediaListAbove[id] = {
            minSize: width + "px",
            title: "At or above " + width + "px"
        };
        
        // update cache
        $scope.updateAllComponentsCacheStyles();
        
        // output CSS
        $scope.outputCSSOptions();
        $scope.outputPageSettingsCSS();
        
        // Adjust viewport
        $parentScope.adjustViewportContainer();
        
        // Mark page as unsaved
        $scope.unsavedChanges();
    }
    
    
    /**
     * 
     *
     * @since 2.2
     * @author Ilya K.
     */

    $scope.$watch('globalSettings.scripts', function(){
        $scope.updateScriptsSettings();
    }, true);

    $scope.$watch('pageSettingsMeta.scripts', function(){
        $scope.updateScriptsSettings();
    }, true);

    $scope.updateScriptsSettings = function() {

        var scripts = false;

        if ($scope.pageSettingsMeta.scripts && $scope.pageSettingsMeta.scripts['scroll_to_hash']=='true'){
            scripts = $scope.pageSettingsMeta.scripts;
            if ($scope.pageSettingsMeta.scripts['scroll_to_hash_time']==='') {
                scripts['scroll_to_hash_time'] = $scope.pageSettings.scripts['scroll_to_hash_time'];
            }
        }
        else if ($scope.pageSettings.scripts && $scope.pageSettings.scripts['scroll_to_hash']=='true'){
            scripts = $scope.pageSettings.scripts;
        }
        else {
            scripts = $scope.globalSettings.scripts;
        }
        var scroll_to_hash_offset = scripts['scroll_to_hash_offset'];
        
        if (!scroll_to_hash_offset) {
           scroll_to_hash_offset = $scope.globalSettings.scripts['scroll_to_hash_offset'];
        }

        if (scripts && scripts['scroll_to_hash']=='true') {
            jQuery('body').addClass('oxygen-scroll-to-hash-links');
            jQuery('body').attr('data-oxygen-scroll-to-hash-links',scripts['scroll_to_hash_time']);
            jQuery('body').attr('data-oxygen-scroll-to-hash-links-offset',scroll_to_hash_offset);
        }
        else {
            jQuery('body').removeClass('oxygen-scroll-to-hash-links');
        }
    }


    /**
     * Get page setting by name from Page Settings or inherit from Template Settings
     *
     * @since 2.1
     */

    $scope.getPageSetting = function(settingName, isGlobal) {

        if ($scope.pageSettingsMeta[settingName]!==undefined && $scope.pageSettingsMeta[settingName]!=="") {
            return $scope.pageSettingsMeta[settingName];
        }

        if ($scope.pageSettings[settingName]!==undefined && $scope.pageSettings[settingName]!=="") {
            return $scope.pageSettings[settingName];
        }

        if (isGlobal==true && $scope.globalSettings[settingName]!==undefined && $scope.globalSettings[settingName]!=="") {
            return $scope.globalSettings[settingName];
        }

        return "";
    }


    /**
     * Set option unit like 'px', 'em' for options like margin, padding, etc
     *
     * @since 0.3.0
     */
    
    $scope.setOptionUnit = function(option, unit, notUpdateCSS) {

        var optionName = option+"-unit",
            id = $scope.component.active.id,
            tag = $scope.component.active.name;

        if (optionName.indexOf('.')>-1) {
            optionName = optionName.split('.');
            if (optionName.length==2 && $scope.component.options[id]['model'][optionName[0]]) {
                $scope.component.options[id]['model'][optionName[0]][optionName[1]] = unit;
            }
            if (optionName.length==3 && $scope.component.options[id]['model'][optionName[0]] && $scope.component.options[id]['model'][optionName[0]][optionName[1]]) {
                $scope.component.options[id]['model'][optionName[0]][optionName[1]][optionName[2]] = unit;
            }
            $scope.setOption(id, tag, optionName[0], false, notUpdateCSS);
        }
        else {
            // udpate model
            $scope.component.options[id]['model'][optionName] = unit;
            $scope.setOption(id, tag, optionName, false, notUpdateCSS);
        }

        $scope.applyModelOptions();
    }


    /**
     * Get option unit like 'px', 'em' for options like margin, padding, etc
     *
     * @since 0.3.0
     * @return {string}
     */
    
    $scope.getOptionUnit = function(option) {

        var optionName = option+"-unit",
            id = $scope.component.active.id;
        
        // deep properties
        if (optionName.indexOf('.')>-1) {
            optionName = optionName.split('.');
            if (optionName.length==2) {
                if ($scope.component.options[id]['model'][optionName[0]] && $scope.component.options[id]['model'][optionName[0]][optionName[1]]) {
                    return $scope.component.options[id]['model'][optionName[0]][optionName[1]];
                }
                else {
                    return $scope.defaultOptions["all"][optionName[1]];
                }
            }
            if (optionName.length==3) {
                if ($scope.component.options[id]['model'][optionName[0]] && $scope.component.options[id]['model'][optionName[0]][optionName[1]] && $scope.component.options[id]['model'][optionName[0]][optionName[1]][optionName[2]]) {
                    return $scope.component.options[id]['model'][optionName[0]][optionName[1]][optionName[2]];
                }
                else {
                    return $scope.defaultOptions["all"][optionName[2]];
                }
            }
        }
        else 
        // regular properties 
        if ( $scope.component.options[id]['model'] && $scope.component.options[id]['model'][optionName] ) {
        	return $scope.component.options[id]['model'][optionName];
        }
        else if ($scope.defaultOptions["all"][optionName]) {
            return $scope.defaultOptions["all"][optionName];
        }

        return "";
    }

    /**
     * Transform unit to display label
     * 
     * @author Abdelouahed E.
     * @since 3.6
     * 
     * @param {string} unit unit value
     * @return {string}
     */
    $scope.getUnitLabel = function (unit) {
        var label = unit;

        // None unit
        if (unit == ' ') {
            label = '–';
        }

        return label;
    }

    /**
     * Transform option unit to label (added mainly for the none unit)
     * 
     * @author Abdelouahed E.
     * @since 3.6
     * 
     * @param {string} option Option name
     * @return {string}
     */
    $scope.getOptionUnitLabel = function(option) {
        var unit = $scope.getOptionUnit(option);
        return $scope.getUnitLabel(unit);
    };

    /**
     * Set global option unit like 'px', 'em' for options like margin, padding, etc
     * 
     * @author Abdelouahed E.
     * @since 3.6
     * 
     * @param {string} context context object: page or global
     * @param {string} option option path
     * @param {string} value unit value
     */
    $scope.setGlobalOptionUnit = function(context, option, value) {

        var optionPath = option+"-unit";
        var contextObject = context == 'page' ? $scope.pageSettingsMeta : $scope.globalSettings;

        objectPath.set(contextObject, optionPath, value);
        
        if (context == 'page') {
            $scope.outputPageSettingsCSS();
        } else {
            $scope.updateGlobalSettingsCSS();
        }
        
        $scope.unsavedChanges();
    }

    /**
     * Get option unit like 'px', 'em' for options like margin, padding, etc
     *
     * @since 2.2
     * @return {string}
     */
    $scope.getGlobalOptionUnit = function(context, option) {
        var optionPath = option+"-unit";
        var contextObject = context == 'page' ? $scope.pageSettingsMeta : $scope.globalSettings;

        return objectPath.get(contextObject, optionPath, '');
    }

    /**
     * Transform option unit to label (added mainly for the none unit)
     * 
     * @author Abdelouahed E.
     * @since 3.6
     * 
     * @param {string} option Option name
     * @return {string}
     */
    $scope.getGlobalOptionUnitLabel = function(context, option) {
        var unit = $scope.getGlobalOptionUnit(context, option);
        return $scope.getUnitLabel(unit);
    };


    /**
     * Get component selector
     *
     * @since 0.3.0
     * @return {string}
     */

    $scope.getComponentSelector = function(id) {

        if (undefined == id) {
            id = $scope.component.active.id;
        }
        
        return $scope.component.options[id]['selector'];
    }


    /**
     * Apply component JS
     *
     * @since 0.3.1
     */

    $scope.applyComponentJS = function(id, name, updateTree) {

        $scope.applyingComponentJS = true;
        jQuery(".oxygen-code-error-container", $parentScope.oxygenUIElement).hide().html("");

        if ($scope.log) {
            console.log("applyComponentJS()", id, name);
        }

        if (undefined==id) {
            id = $scope.component.active.id;
        }

        if (undefined==name) {
            name = $scope.component.active.name;
        }

        if (!$scope.defaultOptions[name]) {
            return;
        }

        if (undefined==updateTree) {
            updateTree = true;
        }

        if (updateTree) {
            $scope.setOption(id, name, 'custom-js', false, false);
        }

        var customJS = $scope.getOption('custom-js', id);

        // output if not equal to default value
        if ($scope.defaultOptions[name]['custom-js'] !== customJS ) {
            $scope.outputJSScript("ct_custom_js_", id, customJS);       
        }

        // We don't have Custom JS for any other states
        // output to DOM
        /*angular.forEach($scope.component.options[id], function(option, key) {
            if(key === 'media') {
                // media styles shouldn't have custom js
                angular.forEach(option, function(breakpoint, bpkey) {
                    angular.forEach(breakpoint, function(bpstate, statekey) {
                        if( bpstate['custom-js']){
                            //console.log(bpkey+" "+statekey);
                            $scope.outputJSScript("ct_custom_js_"+bpkey+"-"+statekey+"-", id, bpstate['custom-js']);
                        }
                    });
                });
            }
            else if( key !== 'model' && key !== 'id' && option['custom-js']) {
                $scope.outputJSScript("ct_custom_js_"+key+"-", id, option['custom-js']);
            }
        });*/

        $scope.applyingComponentJS = false;
    }


    /**
     * Check options to see if this current id option 
     * or inherited from defaults
     *
     * @since 0.3.2
     * @return {string} CSS class to grey out values
     */

    $scope.isInherited = function(id, optionName, optionValue) {

        if (undefined===optionValue) {
            optionValue = $scope.getOption(optionName, id);
        }

        // skip not active option
        if ($scope.getOption(optionName, id) != optionValue) {
            return false;
        }

        // skip empty strings
        if (optionValue === '') {
            return true;
        }

        // skip empty objects
        if (Array.isArray(optionValue) && optionValue.length === 0 ) {
            return true;
        }

        // editing id 'original'
        if ($scope.isEditing("id") && !$scope.isEditing("media") && !$scope.isEditing("state")) {
            if ($scope.component.options[id]["id"] === undefined || $scope.component.options[id]["id"][optionName] == optionValue || angular.equals(optionValue, $scope.component.options[id]["id"][optionName])) {
                return false;
            }
        }

        // editing id state
        if ($scope.isEditing("id") && !$scope.isEditing("media") && $scope.isEditing("state")) {
            if ($scope.component.options[id][$scope.currentState] &&
                $scope.component.options[id][$scope.currentState][optionName] == optionValue) {
                return false;
            }
        }

        // editing id media
        if ($scope.isEditing("id") && $scope.isEditing("media") ) {
            if ( $scope.component.options[id]['media'] &&
                 $scope.component.options[id]['media'][$scope.currentMedia] &&
                 $scope.component.options[id]['media'][$scope.currentMedia][$scope.currentState] &&
                 $scope.component.options[id]['media'][$scope.currentMedia][$scope.currentState][optionName] == optionValue) {
                return false;
            }
        }

        // editing class
        if ($scope.isEditing("class") && !$scope.isEditing("media") ) {
            if ($scope.classes[$scope.currentClass] &&
                $scope.classes[$scope.currentClass][$scope.currentState] &&
                $scope.classes[$scope.currentClass][$scope.currentState][optionName] == optionValue) {
                return false;
            }
        }

        // editing class media
        if ($scope.isEditing("class") && $scope.isEditing("media") ) {
            if ( $scope.classes[$scope.currentClass] &&
                 $scope.classes[$scope.currentClass]['media'] &&
                 $scope.classes[$scope.currentClass]['media'][$scope.currentMedia] &&
                 $scope.classes[$scope.currentClass]['media'][$scope.currentMedia][$scope.currentState] &&
                 $scope.classes[$scope.currentClass]['media'][$scope.currentMedia][$scope.currentState][optionName] == optionValue) {
                return false;
            }
        }

        // editing custom selector
        if ($scope.isEditing("custom-selector") && !$scope.isEditing("class") ) {

            // media
            if ( $scope.isEditing("media") ) {
                if ( $scope.customSelectors[$scope.selectorToEdit] &&
                     $scope.customSelectors[$scope.selectorToEdit]['media'] &&
                     $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia] &&
                     $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia][$scope.currentState] &&
                     $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia][$scope.currentState][optionName] == optionValue) {
                    return false;
                }
            }

            // desktop
            else if (
                $scope.customSelectors[$scope.selectorToEdit] &&
                $scope.customSelectors[$scope.selectorToEdit][$scope.currentState] &&
                $scope.customSelectors[$scope.selectorToEdit][$scope.currentState][optionName] == optionValue) {
                return false;
            }
        }

        return true;
    }


    /**
     * Check options if any of border values has an option
     * or inherited from defaults
     *
     * @since 0.3.2
     * @return {bool}
     */
    
    $scope.isBorderHasStyles = function(side) {

        var width = $scope.isInherited($scope.component.active.id, "border-"+side+"-width");
            style = $scope.isInherited($scope.component.active.id, "border-"+side+"-style");
            color = $scope.isInherited($scope.component.active.id, "border-"+side+"-color");

        return (!width||!style||!color) ? true : false;
    }


    /**
     * Check if tab has at least one option defined
     *
     * @since 0.3.2
     * @author Ilya K.
     * @return {bool}
     */

    $scope.isTabHasOptions = function(key, childKey) {

        if (key===undefined) {

            for (var key in $scope.optionsHierarchy ) {

                if ($scope.optionsHierarchy.hasOwnProperty(key)) {

                    for (var tab in $scope.optionsHierarchy[key]) { 
                        if ($scope.optionsHierarchy[key].hasOwnProperty(tab)) {
                            
                            var subtub = $scope.optionsHierarchy[key][tab];
                            for (var index in subtub) { 
                                var optionName = subtub[index];
                                if ($scope.isInherited($scope.component.active.id, optionName)===false) {
                                    var optionValue = $scope.getOption(optionName, $scope.component.active.id);
                                    if ( optionValue != '' && optionValue != 'false' ) {
                                        return true;
                                    }
                                    
                                }
                            }
                        }
                    }

                }
            }
        }
        else if (childKey===undefined) {
            
            for (var tab in $scope.optionsHierarchy[key]) { 
                if ($scope.optionsHierarchy[key].hasOwnProperty(tab)) {
                    
                    var subtub = $scope.optionsHierarchy[key][tab];
                    for (var index in subtub) { 
                        var optionName = subtub[index];
                        if ($scope.isInherited($scope.component.active.id, optionName)===false) {

                            var optionValue = $scope.getOption(optionName, $scope.component.active.id);
                            if ( optionValue != '' && optionValue != 'false' ) {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        else {

            if (!$scope.optionsHierarchy[key][childKey])
                return false;
                    
            var subtub = $scope.optionsHierarchy[key][childKey];

            for (var index in subtub) { 
                var optionName = subtub[index];
                if ($scope.isInherited($scope.component.active.id, optionName)===false) {

                    var optionValue = $scope.getOption(optionName, $scope.component.active.id);
                    if ( optionValue != '' && optionValue != 'false' ) {
                        return true;
                    }
                }
            }
        }
    }


    /**
     * Check if global settings are different and propose user to change
     *
     * @since 1.1.1
     * @author Ilya K.
     */

    $scope.checkGlobalOptions = function(options) {

        // parse options
        try {
            options = JSON.parse(options);
        }
        catch (e) {
            console.log(options);
            return;
        }

        // check global options (fonts)
        if ( options.display !== undefined && options.text !== undefined ) {
            
            if ( $scope.globalSettings.fonts.Display != options.display ||
                 $scope.globalSettings.fonts.Text != options.text ) {

                var confirmed = confirm("This Design Set's recommended fonts are:\r"+
                                        "Display: "+options.display+"\r"+
                                        "Text: "+options.text+"\r"+
                                        "Would you like to change your fonts to the recommended fonts?");
            
                if (confirmed) {
                    // update global settings
                    $scope.setGlobalFont("Display", options.display);
                    $scope.setGlobalFont("Text", options.text);
                }
            }
        }

        // check page settings (page width)
        if ( options["page-width"] !== undefined ) {
            
            if ( $scope.getPageWidth() != options["page-width"] ) {

                var confirmed = confirm("This Design Set's recommended page width is "+options["page-width"]+
                                        "px, but the width of this page is "+$scope.getPageWidth()+
                                        "px. Would you like to change your page width to "+options["page-width"]+"px?");
            
                if (confirmed) {
                    // update page settings
                    $scope.pageSettings['max-width'] = options["page-width"];
                    $scope.pageSettingsUpdate();
            		$scope.unsavedChanges();
                }
            }
        }
    }


    /**
     * Reset Settings > Global Styles to the default values
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.resetGlobalStylesToDefault = function() {
        
        if (confirm("Are you sure you wish to reset your Global Settings to the defaults?")) {
            
            angular.copy($scope.globalSettingsDefaults["fonts"],        $scope.globalSettings["fonts"]);
            angular.copy($scope.globalSettingsDefaults["headings"],     $scope.globalSettings["headings"]);
            angular.copy($scope.globalSettingsDefaults["body_text"],    $scope.globalSettings["body_text"]);
            angular.copy($scope.globalSettingsDefaults["links"],        $scope.globalSettings["links"]);
            angular.copy($scope.globalSettingsDefaults["sections"],     $scope.globalSettings["sections"]);
            
            $scope.globalSettings["max-width"] = $scope.globalSettingsDefaults["max-width"];

            $scope.pageSettingsUpdate();
		    $scope.unsavedChanges();
        }
    }


    /**
     * Set Layout and Alignment for Div and Section when first time added
     * for backward compatibilty with previous versions
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.setFirstTimeOptions = function(id, name) {
        
        if ( name == "ct_slider" ) { 
            $scope.setOptionModel('slider-arrow-color',     "darker",   id, name);
            $scope.setOptionModel('slider-dot-color',       "#ffffff",  id, name);
            $scope.setOptionModel('slider-show-arrows',     "yes",      id, name);
            $scope.setOptionModel('slider-show-dots',       "yes",      id, name);
            $scope.setOptionModel('slider-autoplay',        "no",       id, name);
            $scope.setOptionModel('slider-autoplay-delay',  "3000",     id, name);
            $scope.setOptionModel('slider-infinite',        "no",       id, name);
            $scope.setOptionModel('slider-animation',       "horizontal", id, name);
            $scope.setOptionModel('slider-animation-speed', "750",      id, name);
        }

        if ( name == "oxy_nav_menu" ) { 
            $scope.setOptionModel('menu_responsive',                "page-width",   id, name);
            $scope.setOptionModel('menu_responsive_padding_size',   "0",            id, name);
            $scope.setOptionModel('menu_responsive_icon_size',      "40",           id, name);
            $scope.setOptionModel('menu_responsive_icon_margin',    "10",           id, name);
            $scope.setOptionModel('dropdowns',                      "on",           id, name);
            $scope.setOptionModel('menu_dropdown_arrow',            "on",           id, name);
            $scope.setOptionModel('menu_padding-top',               "20",           id, name);
            $scope.setOptionModel('menu_padding-left',              "20",           id, name);
            $scope.setOptionModel('menu_padding-right',             "20",           id, name);
            $scope.setOptionModel('menu_padding-bottom',            "20",           id, name);
            $scope.setOptionModel('menu_hover_background-color',    "#eee",         id, name);
            $scope.setOptionModel('menu_hover_border-top-width',    "3",            id, name);
            $scope.setOptionModel('menu_flex-direction',            "row",          id, name);
        }

        if ( name == "oxy_social_icons" ) { 
            $scope.setOptionModel('icon-facebook',  "https://facebook.com");
            $scope.setOptionModel('icon-instagram', "https://instagram.com");
            $scope.setOptionModel('icon-twitter',   "https://twitter.com");
        }

        if ( name == "oxy_header" ) { 
            $scope.setOptionModel('sticky-media',           "page-width");
            $scope.setOptionModel('sticky_scroll_distance', "300");
        }

        if ( name == "oxy_soundcloud" ) { 
            $scope.setOptionModel('height',             "300");
            $scope.setOptionModel('height-unit',        "px");
            $scope.setOptionModel('width',              "100");
            $scope.setOptionModel('width-unit',         "%");
            $scope.setOptionModel('soundcloud_url',     "https://soundcloud.com/nathaniel-eliason/13-learning-spanish-and-more");
            $scope.setOptionModel('soundcloud_track_id', "331837379");
        }

        if ( name == "oxy_superbox" ) { 
            $scope.setOptionModel('superbox_secondary_opacity_start',   "0");
            $scope.setOptionModel('superbox_secondary_opacity_finish',  "1");
        }

        if ( name == "oxy_posts_grid" ) {
            $scope.applyElementPreset(0);
        }

        if ( name == "oxy_comments" ) {
            $scope.applyElementPreset(0);
        }

        // 4.0: Fix for default image type for images where image type is not explicitly defined
        if ( name == "ct_image" ) {
            $scope.setOptionModel('image_type',  "2");
            $scope.setOptionModel('attachment_size', "full");
        }

    }


    /**
     * Check if component display can be flex
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.isFlexPossible = function(name) {

        if ( name == undefined ) {
            name = $scope.component.active.name;
        }

        if ( ["ct_div_block","ct_section","ct_link"].indexOf(name) > -1 ) {
            return true;
        }

        return false;
    }


    /**
     * Set text-align option according to horizontal/vertical alignment
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.setTextAlign = function() {

        var timeout = $timeout(function() {
            
            var id = $scope.component.active.id;

            if ( $scope.component.options[id]['model']['flex-direction'] == "column" ) {
                
                switch ($scope.component.options[id]['model']['align-items']) {
                    case "flex-start":
                        $scope.setOptionModel("text-align","left")
                        break;
                    
                    case "center":
                        $scope.setOptionModel("text-align","center")
                        break;

                    case "flex-end":
                        $scope.setOptionModel("text-align","right")
                        break;

                    case "stretch":
                        $scope.setOptionModel("text-align","justify")
                        break;

                    default: 
                        $scope.setOptionModel("text-align","")
                        break;                
                }
            }

            if ( $scope.component.options[id]['model']['flex-direction'] == "row" ) {
                
                switch ($scope.component.options[id]['model']['justify-content']) {
                    case "flex-start":
                        $scope.setOptionModel("text-align","left")
                        break;
                    
                    case "center":
                        $scope.setOptionModel("text-align","center")
                        break;

                    case "flex-end":
                        $scope.setOptionModel("text-align","right")
                        break;

                    case "space-between":
                    case "space-around":
                        $scope.setOptionModel("text-align","justify")
                        break;

                    default: 
                        $scope.setOptionModel("text-align","")
                        break;                
                }
            }

            $timeout.cancel(timeout);
        }, 11, false);

    }


    /**
     * Set Easy Posts template and render it
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.setEasyPostsTemplate = function(template, needConfirm) {

        if (needConfirm===undefined)
            needConfirm = true;

        if (needConfirm&&!confirm("Current settings for this element will be overwritten. Are you sure?")) {
            return false;
        }

        $scope.setOptionModel("code-php",template.code_php);
        $scope.setOption($scope.component.active.id,$scope.component.active.name,"code-php");
        
        $scope.setOptionModel("code-css",template.code_css);
        $scope.setOption($scope.component.active.id,$scope.component.active.name,"code-css");

        var styles = ["title_size","title_size-unit","title_color","title_hover_color","meta_size","meta_size-unit","meta_color",
                      "content_size","content_size-unit","content_color","read_more_display_as","read_more_size","read_more_size-unit",
                      "read_more_text_color","read_more_text_hover_color","read_more_button_color","read_more_button_hover_color","paginate_color",
                      "paginate_alignment","paginate_link_color","paginate_link_hover_color","posts_per_page","posts_5050_below","posts_100_below"];

        for(var key in styles) {
            var name = styles[key];
            $scope.setOptionModel(name,template[name]); 
        }

        $scope.lastSetEasyPostsTemplate[$scope.component.active.id] = template.name;

        $scope.renderComponentWithAJAX('oxy_render_easy_posts');
    }


    /**
     * Add current PHP and CSS to Easy Posts custom template
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.addEasyPostsTemplate = function() {

        if (!$scope.newEasyPostsTemplate||$scope.newEasyPostsTemplate==="") {
            alert("Please set the preset name");
            return;
        };

        var template = {
            name:$scope.newEasyPostsTemplate,
            code_php: $scope.getOption("code-php"),
            code_css: $scope.getOption("code-css"),
        }

        var styles = ["title_size","title_size-unit","title_color","title_hover_color","meta_size","meta_size-unit","meta_color",
                      "content_size","content_size-unit","content_color","read_more_display_as","read_more_size","read_more_size-unit",
                      "read_more_text_color","read_more_text_hover_color","read_more_button_color","read_more_button_hover_color","paginate_color",
                      "paginate_alignment","paginate_link_color","paginate_link_hover_color","posts_per_page","posts_5050_below","posts_100_below"];
                      
        for(var key in styles) {
            var name = styles[key];
            template[name] = $scope.getOption(name);
        }

        $scope.easyPostsCustomTemplates.push(template);

        $scope.newEasyPostsTemplate = ""
        alert("Your preset was saved successfully");

        $scope.unsavedChanges();
    }


    /**
     * Delete Easy Posts custom template
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.deleteEasyPostsTemplate = function(id, $event) {

        if (typeof $event != 'undefined') {
            $event.stopPropagation();
        }

        $scope.easyPostsCustomTemplates.splice(id, 1);
    }


    /**
     * Set Comments List template and render it
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.setCommentsListTemplate = function(template) {

        $scope.setOptionModel("code-php",template.code_php);
        $scope.setOption($scope.component.active.id,$scope.component.active.name,"code-php");
        
        $scope.setOptionModel("code-css",template.code_css);
        $scope.setOption($scope.component.active.id,$scope.component.active.name,"code-css");

        $scope.lastSetCommentsListTemplate = template.name;

        $scope.renderComponentWithAJAX('oxy_render_comments_list');
    }


    /**
     * Add current PHP and CSS to Comments List custom template
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.addCommentsListTemplate = function() {
        
        if (!$scope.newCommentsListTemplate||$scope.newCommentsListTemplate==="") {
            alert("Please set the preset name");
            return;
        };

        $scope.commentsListCustomTemplates.push({
            name:$scope.newCommentsListTemplate,
            code_php: $scope.getOption("code-php"),
            code_css: $scope.getOption("code-css"),
        })
        
        $scope.newCommentsListTemplate="";
        alert("Your preset was saved successfully");

        $scope.unsavedChanges();
    }


    /**
     * Delete Comments List custom template
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.deleteCommentsListTemplate = function(id, $event) {

        if (typeof $event != 'undefined') {
            $event.stopPropagation();
        }

        $scope.commentsListCustomTemplates.splice(id, 1);
    }


    /**
     * Return Page or Global page width value
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.getPageWidth = function(isGlobal) {

        if (!isGlobal && $scope.getPageSetting('max-width')!==undefined && $scope.getPageSetting('max-width') != "" ) {
            return parseInt($scope.getPageSetting('max-width'))
        }
        else {
            return parseInt($scope.globalSettings['max-width'])
        }
    }

    /**
     * Return breakpoint width value
     *
     * @since 3.2
     * @author Abdelouahed E.
     */

    $scope.getBreakPointWidth = function(id) {
        return parseInt($scope.globalSettings.breakpoints[id]) || parseInt($scope.globalSettingsDefaults.breakpoints[id]);
    }

    /**
     * Set Pricing Box texts to default if empty
     *
     * @since 2.0
     * @author Ilya K.
     */
    
    $scope.reEnablePricingBoxText = function() {

        if ($scope.getOption('pricing_box_package_regular')=='')
            $scope.setOptionModel('pricing_box_package_regular','monthly $399')
        
        if ($scope.getOption('pricing_box_package_subtitle')=='')
            $scope.setOptionModel('pricing_box_package_subtitle','for small business')
        
        if ($scope.getOption('pricing_box_content')==''||$scope.getOption('pricing_box_content')=='<br>')
            $scope.setOptionModel('pricing_box_content','my features<br />another feature<br />my last feature<br />')
    }


    /**
     * Get pretty title for Superbox current editing mode
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.getSuperBoxEditingModeTitle = function() {
        
        var mode = $scope.getOption('superbox_editing_mode');

        switch (mode) {
            
            case "primary_only":
                return "Show Primary Only";
            break;

            case "secondary_only":
                return "Show Secondary Only";
            break;

            case "as_hovered":
                return "As If Hovered";
            break;

            case "as_not_hovered":
                return "As If Not Hovered";
            break;

            default:
                return "Live";
        }
    }

    
    /**
     * Unset all border options for currently active component
     *
     * @since 2.0
     * @author Ilya K.
     */
    
    $scope.unsetAllBorders = function(prefix, borderOptions) {

        if (undefined == prefix) {
            prefix = "";
        }
            
        var id = $scope.component.active.id;

        if(typeof(borderOptions) === 'undefined') {
            var borderOptions = [
                "border-all-width",
                "border-all-width-unit",
                "border-all-style",
                "border-all-color"
                ];
        }

        for(var key in borderOptions) {
            var option = borderOptions[key];
            $scope.setOptionModel(prefix+option,null);
        }
    }

    
    /**
     * Unset passed options for currently active component
     *
     * @since 3.0
     * @author Ilya K.
     */

    $scope.unsetOptions = function(options, id) {

        var name = $scope.component.active.name;
        if (undefined===id) {
            id = $scope.component.active.id;
        }
        else {
            // if rebuilding specific id use "all" defaults as fallback
            name = "all";
        }

        for(var key in options) {
            var option = options[key],
                defaultValue = $scope.defaultOptions[name] ? $scope.defaultOptions[name][option] : "";
            
            $scope.setOptionModel(option, null, id);

            $scope.component.options[id]['id'][option]       = null;
            $scope.component.options[id]['model'][option]    = defaultValue;
            $scope.component.options[id]['original'][option] = defaultValue;
        }
    }

    
    /**
     * Add another Transform option to the component
     *
     * @since 2.2
     * @author Ilya K.
     */

    $scope.addComponentTransform = function() {
        
        var transform = $scope.objectToArrayObject($scope.getOption('transform')),
            type = typeof(transform);

        if(type === 'string' || type === 'undefined') {
            transform = [];
        }

        transform.push({
            'transform-type': 'rotate',
        })

        $scope.setOptionModel('transform', transform);
    }


    /**
     * Remove one Transform option from the active component options
     *
     * @since 2.2
     * @author Ilya K.
     */

    $scope.removeComponentTransform = function($event, index) {
        
        var transform = $scope.objectToArrayObject($scope.getOption('transform'));

        transform.splice(index, 1);

        $scope.setOptionModel('transform', transform);
    }


    /**
     * @since 3.6
     * @author Ilya K.
     */

    $scope.addGridChildRule = function(index) {

        index = index+1;

        var rules = $scope.getOption('grid-child-rules'),
            type = typeof(rules);

        if(type === 'string' || type === 'undefined') {
            rules = [];
        }
        if (rules[index]===null||rules[index]===undefined||rules[index]['child-index']===undefined) {
            rules[index] = {
                'child-index': index,
                'column-span': '',
                'row-span': '',
            }
        }

        $scope.setOptionModel('grid-child-rules', rules);
    }

    /**
     * @since 3.6
     * @author Ilya K.
     */

    $scope.resetGridChildRules = function() {

        var answer = confirm("All Grid Childrens rules will be deleted. Are you sure?");
        if (answer === true) {
            $scope.setOptionModel('grid-child-rules', []);
        }
    }


    /**
     * Check if API Element is AJAX based
     *
     * @since 2.2
     * @author Ilya K.
     */

    $scope.isAJAXElement = function(name) {

        if (!$scope.componentsTemplates[name]) {
            return false;
        }

        if (!$scope.componentsTemplates[name].phpCallback || $scope.componentsTemplates[name].phpCallback != 'true') {
            return false;
        }

        return true;
    }


    /**
     * Check whether component is generated by the Repeater or is original 
     *
     * @since 3.2
     * @author Ilya K.
     */

    $scope.isRepeaterGeneratedElement = function(id) {

        if (undefined === id) {
            id = $scope.component.active.id;
        }

        return $scope.component.options[id]['source_id'] !== undefined;
    }

    /**
     * Save currently active element options states as re-usable preset
     *
     * @since 3.2
     * @author Ilya K.
     */

    $scope.saveElementPreset = function() {

        if (undefined===$scope.newPresetName) {
            $scope.showNoticeModal("<div>No preset name defined</div>");
            return;
        }

        // check for validity of the name
        var re = /^[a-z\s_-][a-z\d\s_-]*$/i

        if(!re.test($scope.newPresetName)) {
            alert("Bad Preset name. Should start with letters. Special characters are not allowed");
            return;
        }

        var id = $scope.component.active.id,
            elementName = $scope.component.active.name;

        if ($scope.isEditing("id")) {
            
            var item = $scope.findComponentItem($scope.componentsTree.children, id, $scope.getComponentItem),
                options = angular.copy(item.options),
                blockedOptions = ['activeselector','classes','ct_id','ct_content','ct_parent','selector','source_id'];

            // unset blocked options
            for (var key in blockedOptions) {
                if (blockedOptions.hasOwnProperty(key)) {
                    var optionName = blockedOptions[key];
                    options[optionName] = null;
                    delete options[optionName];
                }
            }
        }
        else if ($scope.isEditing("class")) {
            var options = angular.copy($scope.classes[$scope.currentClass]);
        }
        else {
            $scope.showNoticeModal("<div>Cannot save preset. Use class or ID selector</div>");
            return;
        }

        // check existance
        if (!$scope.elementPresets || $scope.elementPresets==undefined) {
            $scope.elementPresets = {};
        }
        if (!$scope.elementPresets[elementName] || $scope.elementPresets[elementName]==undefined) {
            $scope.elementPresets[elementName] = [];
        }

        // finally save
        var presetsLength = $scope.elementPresets[elementName].push({
            name: $scope.newPresetName,
            options: angular.copy(options),
        });

        $scope.newPresetName = "";
        $scope.currentPresetKey = presetsLength - 1; 

        console.log($scope.elementPresets);
    }


    /**
     * Apply preset (passed by key or object itslef) options to currently active element 
     *
     * @since 3.2
     * @author Ilya K.
     */

    $scope.applyElementPreset = function(presetKey, presetObj) {

        if (undefined===presetKey) {
            $scope.showNoticeModal("<div>No preset key defined</div>");
            return;
        }

        var id = $scope.component.active.id,
            elementName = $scope.component.active.name;

        if (undefined!==presetObj) {
            // do nothing and continue
        } else 
        if (undefined===$scope.elementPresets[elementName] || undefined===$scope.elementPresets[elementName][presetKey]) {
            $scope.showNoticeModal("<div>Preset doesn't exist</div>");
            return;
        }
        else {
            presetObj = $scope.elementPresets[elementName][presetKey].options;
        }

        if ($scope.isEditing("id")) {
            // get a tree node
            var element = $scope.findComponentItem($scope.componentsTree.children, id, $scope.getComponentItem);

            // clear options before applying
            // TODO: avoid duplicate definition of that variable (the same we have in saveElementPreset)
            var blockedOptions = ['activeselector','classes','ct_id','ct_content','ct_parent','selector','source_id','name','nicename'];

            // unset not blocked options
            for (var key in element.options) {
                if (element.options.hasOwnProperty(key) && blockedOptions.indexOf(key)===-1) {
                    element.options[key] = null;
                    delete element.options[key];
                }
            }
            for (var key in $scope.component.options[id]) {
                if ($scope.component.options[id].hasOwnProperty(key) && blockedOptions.indexOf(key)===-1) {
                    $scope.component.options[id][key] = null;
                    delete $scope.component.options[id][key];
                }
            }

            // apply presets to a tree node
            if (!jQuery.isEmptyObject(presetObj)) {
                angular.extend(element.options, angular.copy(presetObj));
            }
            
            // apply all needed changes to $scope.component.options[id]
            $scope.applyComponentDefaultOptions(id, elementName)
            $scope.applyComponentSavedOptions(id, element)
            $scope.applyModelOptions(id, elementName);
        }

        if ($scope.isEditing("class")) {
            $scope.classes[$scope.currentClass] = angular.copy(presetObj);
            $scope.classesCached = false;
        }

        $scope.currentPresetKey = presetKey;

        // update CSS output
        $scope.rebuildDOM(id);
        $scope.outputCSSOptions(id, true);
        
    }

    
    /**
     * Delete preset from global presets object
     *
     * @since 3.2
     * @author Ilya K.
     */

    $scope.deleteElementPreset = function(presetKey, $event) {

        if (typeof $event != 'undefined') {
            $event.stopPropagation();
        }
        
        if (undefined===presetKey) {
            $scope.showNoticeModal("<div>No preset defined</div>");
            return;
        }

        var elementName = $scope.component.active.name;

        if (undefined===$scope.elementPresets[elementName] || undefined===$scope.elementPresets[elementName][presetKey]) {
            $scope.showNoticeModal("<div>No preset exist</div>");
            return;
        }
        
        if ($scope.elementPresets[elementName][presetKey]!==undefined) {
            var answer = confirm('"'+$scope.elementPresets[elementName][presetKey].name + "\" will be deleted forever from " + elementName + " presets. Are you sure?");
            if (answer === true) {
                $scope.elementPresets[elementName].splice(presetKey, 1);
            }
        }

    }


    /**
     * Check if preset is default
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.isDefaultPreset = function(elementName, presetKey) {

        if (undefined !== $scope.elementPresets[elementName] &&
            undefined !== $scope.elementPresets[elementName][presetKey] &&
            undefined !== $scope.elementPresets[elementName][presetKey].slug) {
            
            return true;
        }

        return false;
    }
    

    /**
     * Remove currently active preset from Presets global object and keep the element styles
     *
     * @since 3.2
     * @author Ilya K.
     */

    $scope.deleteCurrentElementPreset = function() {

        $scope.deleteElementPreset($scope.currentPresetKey);
        $scope.currentPresetKey = false;
    }


    /**
     * Return the name of currently set preset 
     *
     * @since 3.2
     * @author Ilya K.
     */

    $scope.getCurrentPresetName = function() {
        
        return $scope.getPresetName($scope.currentPresetKey); 
    }


    /**
     * Return the name of the preset by the key
     *
     * @since 3.2
     * @author Ilya K.
     */
    
    $scope.getPresetName = function(presetKey) {

        var preset = $scope.getPreset(presetKey);

        if (undefined == preset) {
            return "";
        }

        return preset.name;
    }


    /**
     * Return a full preset object by preset key 
     *
     * @since 3.2
     * @author Ilya K.
     */

    $scope.getPreset = function(presetKey) {

        if (undefined == presetKey ||
            undefined == $scope.elementPresets ||
            undefined == $scope.elementPresets[$scope.component.active.name]) {
            return {};
        }

        return $scope.elementPresets[$scope.component.active.name][presetKey];
    }


    /**
     * Show modal with current preset converted to JSON
     *
     * @since 3.2
     * @author Ilya K.
     */

    $scope.exportCurrentPreset = function() {

        var preset = $scope.getPreset($scope.currentPresetKey);

        $scope.parentScope.showPresetsDialog = true;
        $scope.presetExportJSON = angular.toJson(preset);
    }


    /**
     * Copy current preset to clipboard
     *
     * @since 3.2
     * @author Ilya K.
     */

    $scope.copyPresetExportJSON = function() {
        $scope.copyToClipboard($scope.presetExportJSON);
    }


    /**
     * Copy passed conntent to a clipboard
     *
     * @since 3.2
     * @author Ilya K.
     */

    $scope.copyToClipboard = function(content) {
        var el = document.createElement('textarea');
        el.value = content;
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    }


    /**
     * Show modal with input for preset JSON and adds it to presets object 
     *
     * @since 3.2
     * @author Ilya K.
     */

    $scope.importPreset = function() {
        
        var presetJSON = prompt("Enter preset JSON code");
        
        if(!presetJSON) {
            return;
        }

        presetJSON = presetJSON.trim();
        
        var preset = angular.fromJson(presetJSON),
            elementName = $scope.component.active.name;

        // check existance
        if (!$scope.elementPresets || $scope.elementPresets==undefined) {
            $scope.elementPresets = {};
        }
        if (!$scope.elementPresets[elementName] || $scope.elementPresets[elementName]==undefined) {
            $scope.elementPresets[elementName] = [];
        }

        $scope.elementPresets[elementName].push(preset);
    }

    
    /**
     * Clear all the styles for currently editing state (ID or Class)
     *
     * @since 3.2
     * @author Ilya K.
     */

    $scope.clearCurrentElementStyles = function() {

        var answer = confirm("All element's styles will be deleted for " + $scope.component.options[$scope.component.active.id]['nicename'] + ". Are you sure?");
        if (answer === true) {
            $scope.applyElementPreset(false,{});            
        }

    }


    /**
     * Moved logic from conditions.modal.view.php
     *
     * @since 3.3
     * @author Ilya K.
     */
    
    $scope.getGlobalCondition = function(index) {

        var id = $scope.component.active.id;

        if (undefined!=$scope.component.options[id]['model']['globalconditions'] &&
            undefined!=$scope.component.options[id]['model']['globalconditions'][index] && 
            undefined!=$scope.component.options[id]['model']['globalconditions'][index]['name']) {
    
            return $scope.globalConditions[$scope.component.options[id]['model']['globalconditions'][index]['name']];
        }

        return false;
    }

    
    /**
     * Moved logic from conditions.modal.view.php
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.getConditionValue = function(index, condition) {

        var globalCondition = $scope.getGlobalCondition(index);

        if (undefined==globalCondition || undefined==globalCondition['values']) {
            return '';
        }
        
        if (globalCondition['values']['keys'] && 
            globalCondition['values']['options'][condition.value]) {

            return globalCondition['values']['options'][condition.value];
        }
        else {
            return condition.value;
        }
    }


    /**
     * Moved logic from conditions.modal.view.php
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.getConditionPlaceholder = function(index) {

        var globalCondition = $scope.getGlobalCondition(index);

        if (undefined==globalCondition || undefined==globalCondition['values']) {
            return 'Custom Value...';
        }

        return globalCondition['values']['placeholder'] ?
               globalCondition['values']['placeholder'] : 'Custom Value...';
    }


    /**
     * Custom condition means user can type in any custom value
     * Moved logic from conditions.modal.view.php
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.isCustomCondition = function(index) {
        
        var globalCondition = $scope.getGlobalCondition(index);

        if (undefined==globalCondition || undefined==globalCondition['values']) {
            return false;
        }
        
        return globalCondition['values']['custom'];
    }


    /**
     * AJAX conditions loads list of options with AJAX
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.isAJAXCondition = function(index) {
        
        var globalCondition = $scope.getGlobalCondition(index);

        if (undefined==globalCondition || undefined==globalCondition['values']) {
            return false;
        }
        
        return globalCondition['values']['ajax'];
    }


    /**
     * Moved logic from conditions.modal.view.php
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.updateConditionOptions = function(index, data, searchValue) {

        if (undefined==index||undefined==data) {
            return;
        }

        if (""!==searchValue) {
            var valueFound = false;
            for (var key in data) {
                if (data.hasOwnProperty(key) && data[key] == searchValue) {
                    $scope.setConditionValue(index, key, searchValue);
                    valueFound = true;
                    break;
                }
            }
            if (!valueFound) {
                // set negative index to unset the value, as there is no real life negative ID terms
                $scope.setConditionValue(index, -1, searchValue);
            }
        }

        var globalCondition = $scope.getGlobalCondition(index);

        if (undefined==globalCondition) {
            console.log('No global condition found with index: ' + index);
            return;
        }

        globalCondition['values']['options'] = data;
    }


    /**
     * Check the active component options to find the name for the condition with passed index 
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.getConditionName = function(index) {

        var id = $scope.component.active.id;

        if ($scope.component.options[id]['model']['globalconditions'] &&
            $scope.component.options[id]['model']['globalconditions'][index] &&
            $scope.component.options[id]['model']['globalconditions'][index]['name'] ) {
        
            return $scope.component.options[id]['model']['globalconditions'][index]['name'];
        }

        return "";
    }


    /**
     * Check if condition suppose to store keys instead of names/values
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.isKeysCondition = function(index) {

        var conditionName = $scope.getConditionName(index);
        
        if ( $scope.globalConditions[conditionName] &&
             $scope.globalConditions[conditionName]['values'] &&
             $scope.globalConditions[conditionName]['values']['keys']) {
            
            return true;
        } 

        return false;
    }


    /**
     * Sometimes we need to store thing like ID keys, check if so and set the proper value
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.setConditionValue = function(index, key, value) {

        var id = $scope.component.active.id;
        
        if ( $scope.isKeysCondition(index) && undefined !== key) {
            $scope.component.options[id]['model']['globalconditions'][index]['value'] = key;
        }
        else {
            $scope.component.options[id]['model']['globalconditions'][index]['value'] = value;
        }

        // value to display in search field, it should always be the name and not the key
        $scope.component.options[id]['model']['globalconditions'][index]['searchValue'] = value;

        // update tree
        $scope.setOptionModel('globalconditions', $scope.component.options[id]['model']['globalconditions']);
    }


    /*$scope.updateConditionOperator = function(index) {

        var id = $scope.component.active.id;

        if (!$scope.component.options[id]['model']['globalconditions'][index]) {
            return;
        }

        if ( $scope.component.options[id]['model']['globalconditions'][index]['name'] !== item.name ) {
            $scope.component.options[id]['model']['globalconditions'][index]['operator'] = 0;
        } 
    }

    $scope.updateConditionValue = function(index) {

        var id = $scope.component.active.id;

        if (!$scope.component.options[id]['model']['globalconditions'][index]) {
            return;
        }

        if ( $scope.component.options[id]['model']['globalconditions'][index]['name'] !== item.name ) {
            $scope.component.options[id]['model']['globalconditions'][index]['value'] = '';
        } 
    }

    $scope.updateConditionName = function(index) {

        var id = $scope.component.active.id;

        if (!$scope.component.options[id]['model']['globalconditions'][index]) {
            return;
        }
        
        $scope.component.options[id]['model']['globalconditions'][index]['name'] = item.name;
    }*/


    /**
     * Sanitize user input for custom attribute name
     *
     * @since 3.4
     * @author Ilya K.
     */


    $scope.validateCustomAttributeName = function(index) {
        
        var id = $scope.component.active.id,
            customAttributes = $scope.getOption('custom-attributes', id)

        if (undefined===customAttributes[index]) return

        var name = customAttributes[index]['name']

        if (undefined===name) return

        var regex = RegExp('[^a-z-_]+', 'gi')

        if (regex.test(name)) {

            customAttributes[index]['name'] = name.replace(regex,"")
            $scope.setOptionModel('custom-attributes', customAttributes, id)
            $scope.showNoticeModal("<div>Name may only contain underscores (_), hyphens (-) and letters (a–z and A-Z).</div>")
        }
    }

    /**
     * Sanitize user input for custom tags
     *
     * @since 3.8
     * @author Ilya K.
     */


    $scope.validateCustomTag = function(optionName) {
        
        var id = $scope.component.active.id,
            tag = $scope.getOption(optionName, id)

        if (!$scope.regexTestCustomTag(tag)) {
            
            var regex = RegExp('[a-z]+[a-z0-9-]*', 'gi'),
                matched = tag.match(regex);

            tag =  (matched && matched[0]) ? matched[0] : "";
            
            $scope.setOptionModel(optionName, tag, id)
            $scope.showNoticeModal("<div>Tag must begin with a letter and may only contain letters (a-z and A-Z) and numbers (0-9).</div>")
        }
    }

    $scope.regexTestCustomTag = function(tag) {

        if (!tag) {
            return false;
        }

        var regex = RegExp('[a-z]+[a-z0-9-]*', 'gi')

        if (tag.match(regex) && tag.match(regex)[0]==tag) {
           return true;
        }

        return false;
    }

    $scope.filterCustomTag = function(tag) {

        if (!tag) {
            return 'div';
        }

        var regex = RegExp('[a-z]+[a-z0-9]*', 'gi'),
            matched = tag.match(regex);

        return (matched && matched[0]) ? matched[0] : "div";
    }


    /**
     * Sanitize user input for custom attribute value
     *
     * @since 3.4
     * @author Ilya K.
     */

    $scope.validateCustomAttributeValue = function(index) {
        
        var id = $scope.component.active.id,
            customAttributes = $scope.getOption('custom-attributes', id)

        if (undefined===customAttributes[index]) return

        var value = customAttributes[index]['value']

        if (undefined===value) return

        var filteredValue = $scope.replaceSpecialChars(value);
            
        if (filteredValue!==value) {
        
            customAttributes[index]['value'] = filteredValue

            $scope.setOptionModel('custom-attributes', customAttributes, id)
            $scope.showNoticeModal("<div>Value may NOT contain quotes and '&lt;', '&gt;' characters. These were auto converted to corresponding HTML enteties.</div>")
        }
        
    }


    /**
     * Sanitize user input for custom attribute value
     *
     * @since 3.4
     * @author Ilya K.
     */

    $scope.validateHTMLAttributeValue = function(optionName) {
        
        var id = $scope.component.active.id,
            value = $scope.getOption(optionName, id)

        if (undefined===value) return

        var filteredValue = $scope.replaceSpecialChars(value);

        if (filteredValue!==value) {
            $scope.setOptionModel(optionName, filteredValue, id)
            $scope.showNoticeModal("<div>Value may NOT contain quotes and '&lt;', '&gt;' characters. These were auto converted to corresponding HTML enteties.</div>")
        }
        
    }


    /**
     * Helper to replace the special chars in a given string
     *
     * @since 3.4
     * @author Ilya K.
     */

    $scope.replaceSpecialChars = function(value) {

        if (undefined===value || undefined===value.replace) return value
            
        value = value
            .replace(new RegExp('"', 'g'), "&quot;")
            .replace(new RegExp("'", 'g'), "&apos;")
            .replace(new RegExp("<", 'g'), "&lt;")
            .replace(new RegExp(">", 'g'), "&gt;")

        return value
    }


    /**
     * Render attributes with AJAX if needed and assign to an element
     *
     * @since 3.4
     * @author Ilya K.
     */

    $scope.applyCustomAttributes = function(id) {

        var customAttributes = $scope.getOption('custom-attributes', id);

        for(var key in customAttributes) { 
            if (customAttributes.hasOwnProperty(key)) {
                
                var attr = customAttributes[key];

                if (!attr||!attr['name']) {
                    continue
                }

                var nameHasDynamicData  = attr['name'].match(/\[oxygen[^\]]*\]/ig)
                var valueHasDynamicData = attr['value'].match(/\[oxygen[^\]]*\]/ig)

                if (nameHasDynamicData || valueHasDynamicData) {
                    $scope.renderCustomAttributeDynamicData(id, attr['name'], attr['value'])
                }

                if (!nameHasDynamicData && !valueHasDynamicData) {
                    var component = $scope.getComponentById(id)
                    if (component.attr && attr['name']) {
                        component.attr(attr['name'],attr['value'])
                    }
                }
            }
        }
    }

    $scope.isIDLocked = function(id) {

        if (undefined==id) {
            id = $scope.component.active.id;
        }

        if ($scope.component.options[id] && $scope.component.options[id]['id'] && $scope.component.options[id]['id']['selector-locked'] == 'true') {
            return true;
        }

        return false;
    }
    
});