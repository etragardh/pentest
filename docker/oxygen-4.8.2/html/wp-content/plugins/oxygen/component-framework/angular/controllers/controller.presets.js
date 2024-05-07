
CTFrontendBuilder.controller("ControllerPresets", function($scope, $parentScope, $timeout) {    

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

        var options = $scope.getCurrentSelectorOptions();
        
        if (options===false) {
            $scope.showNoticeModal("<div>Cannot save preset. Use class or ID selector</div>");
            return;
        }

        var elementName = $scope.component.active.name;

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
    }


    /**
     * Return ready to copy filtered options for current selector (ID or class)
     *
     * @since 3.4
     * @author Ilya K.
     */

    $scope.getCurrentSelectorOptions = function() {

        var id = $scope.component.active.id,
            options;

        if ($scope.isEditing("id")) {
            var item = $scope.findComponentItem($scope.componentsTree.children, id, $scope.getComponentItem);
            options = angular.copy(item.options);
        }
        else if ($scope.isEditing("class")) {
            options = angular.copy($scope.classes[$scope.currentClass]);
        }
        else {
            options = false;
        }

        $scope.clearPresetPrivateOptions(options);

        return options;
    }


    /**
     * Return ready to copy filtered options for specified ID
     *
     * @since 3.4
     * @author Ilya K.
     */

    $scope.getIDOptionsToCopy = function(id) {

        var item = $scope.findComponentItem($scope.componentsTree.children, id, $scope.getComponentItem)

        if ( !item ) return

        var options = angular.copy(item.options)

        $scope.clearPresetPrivateOptions(options)

        return options
    }

    
    /**
     * Return ready to copy filtered options for specified class name
     *
     * @since 3.4
     * @author Ilya K.
     */

    $scope.getClassOptionsToCopy = function(className) {

        var options = angular.copy($scope.classes[className])

        if ( !options ) return

        $scope.clearPresetPrivateOptions(options)

        return options
    }


    /**
     * Remove "private" options from given options object
     *
     * @since 3.4
     * @author Ilya K.
     */

    $scope.clearPresetPrivateOptions = function(options) {

        for (var key in options) {
            if (options.hasOwnProperty(key)) {
                if ($scope.isPrivateOption(key)) {
                    options[key] = null;
                    delete options[key];
                }
                else if (typeof options[key] === "object" && !Array.isArray(options[key])) {
                    // recursively go deeper
                    $scope.clearPresetPrivateOptions(options[key]);
                }
            }
        }
    }


    /**
     * Remove all options except "private" from given options object
     *
     * @since 3.4
     * @author Ilya K.
     */

    $scope.clearOptionsAndKeepPrivate = function(options) {

        for (var key in options) {
            if (options.hasOwnProperty(key)) {
                if (!$scope.isPrivateOption(key)) {
                    if (typeof options[key] === "object" && !Array.isArray(options[key]) && key !== "before" && key !== "after") {
                        // recursively go deeper
                        $scope.clearOptionsAndKeepPrivate(options[key]);
                    }
                    else {
                        // actual option to check
                        options[key] = null;
                        delete options[key];
                    }
                }
            }
        }
    }


    /**
     * Check against the list of options that shouldn't be saved to preset nor cleared with "Clear Current Styles"
     *
     * @since 3.4
     * @author Ilya K.
     */

    $scope.isPrivateOption = function(optionName) {

        var privateOptions = [
            // common options
            'activeselector','classes','ct_id','ct_content','ct_parent','selector','source_id','name','nicename',
            'ct_shortcode','ct_widget','ct_data','ct_sidebar','ct_nav_menu','oxy_builtin','custom-attributes','custom-js',
            // Tabs
            'tabs_contents_wrapper',
            'active_tab_class',
            // Gallery
            'image_ids',
            'acf_field',
            'woo_product_id',
            // Menu
            'menu_id',
            // Icon
            'icon-id',
        ];

        if (privateOptions.indexOf(optionName) > -1) {
            return true;
        }

        return false;
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

            $scope.clearOptionsAndKeepPrivate(element.options);
            $scope.clearOptionsAndKeepPrivate($scope.component.options[id]);

            // apply presets to a tree node
            if (!jQuery.isEmptyObject(presetObj)) {
                jQuery.extend(true, element.options, angular.copy(presetObj));
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

        if (undefined !== $scope.elementPresets &&
            undefined !== $scope.elementPresets[elementName] &&
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
     * Copy JSON to clipboard
     *
     * @since 3.2
     * @author Ilya K.
     */

    $scope.copyPresetExportJSON = function() {
        var el = document.createElement('textarea');
        el.value = $scope.presetExportJSON;
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

    $scope.clearCurrentSelectorOptions = function(noConfirmNeeded) {

        if (noConfirmNeeded === true) {
            answer = true;
        }
        else {
            var answer = confirm("Current selector styles will be deleted for " + $scope.component.options[$scope.component.active.id]['nicename'] + ". Are you sure?");
        }

        if (answer === true) {
            $scope.applyElementPreset(false,{});
            $scope.setFirstTimeOptions($scope.component.active.id,$scope.component.active.name)         
        }
    }


    /**
     * Clear styels for defined class, if no class defined clear currently active element ID styles
     *
     * @since 3.4
     * @author Ilya K.
     */

    $scope.clearSelectorOptions = function(className) {

        var answer = false;

        if (undefined !== className) {
            answer = confirm("\"" + className + "\" styles will be deleted. Are you sure?");
        }
        else {
            answer = confirm("ID styles will be deleted for " + $scope.component.options[$scope.component.active.id]['nicename'] + ". Are you sure?");
        }

        if (answer === true) {

            var savedClass = ($scope.currentClass) ? $scope.currentClass : false

            // set selector to clear styles for
            if (undefined !== className) {
                $scope.setCurrentClass(className)
            }
            else {
                $scope.switchEditToId(true)
            }
            
            // clear styles
            $scope.applyElementPreset(false,{})
            $scope.setFirstTimeOptions($scope.component.active.id,$scope.component.active.name)

            // restore originally active selector
            if (savedClass) {
                $scope.setCurrentClass(savedClass)
            }
            else {
                $scope.switchEditToId(true)
            }
        }
    }


    /**
     * Copy styles from globally defined source ($parentScope.copySelectorFromID or $parentScope.copySelectorFromClass)
     * to specifed class. If no class specifed to currently active ID
     *
     * @since 3.3
     * @author Ilya K.
     */

     $scope.copySelectorOptions = function(targetClass) {

        var options;

        if ($parentScope.copySelectorFromID) {
            options = $scope.getIDOptionsToCopy($parentScope.copySelectorFromID)
        }
        else if ($parentScope.copySelectorFromClass) {
            options = $scope.getClassOptionsToCopy($parentScope.copySelectorFromClass)
        }
        else {
            $scope.showNoticeModal("<div>Nothing to copy from</div>");
            return;
        }

        var savedClass = ($scope.currentClass) ? $scope.currentClass : false
        
        // switch to target selector
        if (undefined !== targetClass){
            $scope.setCurrentClass(targetClass);
        }
        else {
            $scope.switchEditToId(true);
        }

        // do the magic
        $scope.applyElementPreset(false, options);

        // restore originally active selector
        if (savedClass) {
            $scope.setCurrentClass(savedClass);
        }
        else if ($scope.copySelectorFromClass) {
            $scope.switchEditToId(true);
        }

        $parentScope.copySelectorFromID = false;
        $parentScope.copySelectorFromClass = false;
    }

});