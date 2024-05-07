
/**
 * Selector Detector Controller
 *
 */

CTFrontendBuilderUI.controller("ControllerSelectorDetector", function($controller, $scope, $timeout, $window) {

    $scope.chooseSelectorBehavior = "update";
    $scope.selectorsList    = [];
    $scope.appliedSelectors = [];

    var parentScope = $scope.$parent;

    parentScope.$on('iframe-init', function(e, iframeScope) {

        $scope.iframeScope.selectorDetector.mode = false;

        /**
         * Setup UI
         *
         */

        $scope.builderElement
        .on("mouseover", ".oxy-gallery *:not(img), .ct-inner-content *, .ct-contains-oxy *, .oxy-comments *, .oxy-comment-form *, .oxy-login-form *, .oxy-search-form *, .ct-shortcode *, .ct-code-block *, .ct-widget *, .oxy-posts-grid *, .oxy-nav-menu *, .oxy-rich-text *, .ct-sidaber *", function(e){

            // check if we in detector mode
            if ( !$scope.iframeScope.selectorDetector.mode ) {
                return;
            }

            /*if ( $scope.iframeScope.selectorDetector.modePause ) {
                return;
            }*/

            var activeComponent = $scope.iframeScope.getActiveComponent(),
                highlight = false;

            if (activeComponent) {
                componentSelector = "#"+activeComponent.attr("id");
            }
            else {
                componentSelector = $scope.iframeScope.componentSelector.name;   
            }

            highlight = jQuery(this).parents(componentSelector).length > 0;

            if (highlight) {
                jQuery(this).addClass('ct-highlight-selector');
                e.stopPropagation();
            }
        })
        .on("mouseout", ".oxy-gallery *:not(img), .ct-inner-content *, .ct-contains-oxy *, .oxy-comments *, .oxy-comment-form *, .oxy-login-form *, .oxy-search-form *, .ct-shortcode *, .ct-code-block *, .ct-widget *, .oxy-posts-grid *, .oxy-nav-menu *, .oxy-rich-text *, .ct-sidaber *", function(e){
            // check if we in detector mode
            if ( !$scope.iframeScope.selectorDetector.mode ) {
                return;
            }
            e.stopPropagation();
            jQuery(this).removeClass('ct-highlight-selector');
        })

        // Selector detector
        .on("mousedown", ".oxy-gallery *:not(img), .ct-inner-content *, .ct-contains-oxy *, .oxy-comments *, .oxy-comment-form *, .oxy-login-form *, .oxy-search-form *, .ct-shortcode *, .ct-code-block *, .ct-code-block, .ct-widget *, .oxy-posts-grid *, .oxy-nav-menu *, .oxy-rich-text *, .ct-sidaber *", function(e){
            // check if we in detector mode
            if ( !$scope.iframeScope.selectorDetector.mode ) {
                return;
            }

            /*if ( $scope.iframeScope.selectorDetector.modePause ) {
                // check if we are not clicking the same element
                if (!jQuery(this).is($scope.iframeScope.selectorToEdit)) {

                    // back to initial selector detector state
                    $scope.iframeScope.selectorDetector.modePause = false;
                    parentScope.disableSelectorDetectorMode();
                    $scope.activateComponent($scope.iframeScope.componentSelector.id);
                    $scope.iframeScope.selectorDetector.mode      = true;
                    $scope.iframeScope.selectorDetector.bubble    = false;
                    $scope.$apply();
                }

                e.stopPropagation();
                return;
            }*/

            jQuery(this).removeClass('ct-highlight-selector');

            var activeComponent = $scope.iframeScope.getActiveComponent(),
                activate = false;

            if (activeComponent) {
                componentSelector = "#"+activeComponent.attr("id");
                $scope.iframeScope.componentSelector.name   = componentSelector;
                $scope.iframeScope.componentSelector.id     = activeComponent.attr("ng-attr-component-id")
            }
            else {
                componentSelector = $scope.iframeScope.componentSelector.name;   
            }
            
            //if (jQuery(this).parents(componentSelector).length > 0) {

                $scope.existingSelectorFound = false;

                // look for saved selectors
                for(var selector in $scope.iframeScope.customSelectors) { 
                    if ($scope.iframeScope.customSelectors.hasOwnProperty(selector)) {

                        try {
                            // first selector found
                            if (jQuery(this).is(selector)) {
                                $scope.existingSelectorFound = selector;
                                //$scope.disableSelectorDetectorMode();
                                //$scope.$apply();
                                //e.stopPropagation();
                                // do not to open choose a selector box, so we can exit
                                //return;
                            }
                        } catch (err) {
                            console.log(err);
                        }
                    }
                }

                if ($scope.existingSelectorFound) {
                    $scope.parseAppliedSelectors($scope.existingSelectorFound, this);
                }
                else {

                    // get array of all the selectors of the applied to the element styles
                    var appliedSelectors = $scope.findElementSelectors(this);

                    $scope.iframeScope.selectorDetector.element = this;
                    $scope.appliedSelectors = appliedSelectors;
                    
                    // get most specific selector
                    if (appliedSelectors && appliedSelectors[0]) {
                        appliedSelector = appliedSelectors[0];
                    }
                    else {
                        appliedSelector = false;
                    }

                    $scope.parseAppliedSelectors(appliedSelector,this);
                }

                e.stopPropagation();

                $scope.iframeScope.selectorDetector.bubble = false;
                $scope.iframeScope.selectorDetector.modePause = true;
            //}
        })

        $scope.oxygenUIElement
        .on('mouseover', '.ct-choose-selector-item-heading', function() {
            var $menuItem = angular.element(this),
                $variants = angular.element('> .ct-choose-selector-variants', $menuItem.parent()),
                $variantsHeight = $variants.outerHeight();
            
            // grab the menu item's position relative to its positioned parent
            var menuItemPos = $menuItem.position(),
                left = menuItemPos.left + Math.round($menuItem.outerWidth());

            if (left>250) left = 250; // 300px sidebar width minus 16px+16px side padding minus possible scrollbar

            // place the submenu in the correct position relevant to the menu item
            $variants.css({
              top: menuItemPos.top,
              left: left,
              bottom: "",
            });

            var variantsOffest = $variants.offset(); 
            
            if (window.innerHeight<variantsOffest.top+$variantsHeight) {
                $variants.css({
                  top: "",
                  bottom: 0
                });
            }
        })

    });


    /**
     * Update selector object key
     *
     * @since 1.0
     * @author Ilya K.
     */

    parentScope.selectorChange = function(oldSelector, newSelector) {

        var newSelectors = {};

        if (newSelector === undefined) {
            newSelector = $scope.iframeScope.selectorToEdit;
        }

        if (parentScope.log) {
            console.log("selectorChange()", oldSelector, 'to', newSelector)
        }
        
        /**
         * Tricky way to update custom selector within main scope
         */        
        Object.keys($scope.iframeScope.customSelectors).map(function(key, index) {
            if (key==oldSelector) {
                newSelectors[newSelector] = $scope.iframeScope.customSelectors[key];
            }
            else {
                newSelectors[key] = $scope.iframeScope.customSelectors[key];
            }
        });

        // delete old keys
        Object.keys($scope.iframeScope.customSelectors).map(function(key, index) {
            delete $scope.iframeScope.customSelectors[key];
        });

        // add new keys
        Object.keys(newSelectors).map(function(key, index) {
            $scope.iframeScope.customSelectors[key] = newSelectors[key];
        });

        $scope.iframeScope.outputCSSOptions();
        $scope.iframeScope.unsavedChanges();
    }

    
    /**
     * Build clickable nested structure of selectors for users to choose from
     *
     * @since 1.0
     * @author Ilya K.
     */ 

    parentScope.buildChooseSelector = function(selectorsList) {

        // save to scope
        if (selectorsList !== undefined) {
            $scope.selectorsList = selectorsList;
        }
        else {
            selectorsList = $scope.selectorsList;
        }
        
        var chooseSelectorContainer = document.getElementById("ct-choose-selector-content");

        // create jqLite element and clear HTML
        chooseSelectorContainer = angular.element(chooseSelectorContainer);
        chooseSelectorContainer.empty();

        var html = "",
            closingTags = "";

        // open tags
        for(var key in selectorsList) { 
            if (selectorsList.hasOwnProperty(key)) {
                html += "<div class=\"ct-choose-selector-item\">" +
                            "<div class=\"ct-choose-selector-item-heading\" " +
                                "ng-class=\"{'ct-choose-selector-item-active':selectorsList["+key+"].status=='active'}\" "+ 
                                "ng-click=\"selectorItemAction("+key+")\">" +
                                    selectorsList[key].selector +
                            "</div>";

                html += "<div class=\"ct-choose-selector-variants\">";

                for (var variantKey in selectorsList[key].variants ) {
                    if (selectorsList[key].variants.hasOwnProperty(variantKey)) {
                        html += "<div class=\"ct-choose-selector-variant\"" +
                                    "ng-class=\"{'ct-choose-selector-variant-active':selectorsList["+key+"].variants["+variantKey+"].status=='active'}\" "+ 
                                    "ng-click=\"selectorVariantAction("+key+","+variantKey+")\">" + 
                                        selectorsList[key].variants[variantKey].value.replace(/\-/g, "&#8209;") + 
                                "</div>";
                    }
                }

                html += "</div>";

                closingTags += "</div>";
            }
        }

        html += closingTags;

        if (selectorsList.length==0&&$scope.iframeScope.selectorDetector.mode) {
            html = "<span>Click elements to see the selectors</span>"
        }

        // inseret to DOM
        angular.element(document).injector().invoke(function($compile) {
            chooseSelectorContainer.append($compile(html)($scope));
        });
    };


    /**
     * Parse applied selectors to "activate" correct items in Choose selector box
     *
     * @since 1.0
     * @author Ilya K.
     */ 

    $scope.parseAppliedSelectors = function(appliedSelectors,element) {

        if (parentScope.log) {
            console.log("parseAppliedSelectors()", appliedSelectors, element)
        }

        if (element===undefined) {
            element = $scope.iframeScope.selectorDetector.element;
        }

        if (appliedSelectors!==false) {
            appliedSelectors = appliedSelectors.trim().split(" ");
        }
        else {
            appliedSelectors = [];
        }
        
        // wrap with jQuery
        element = jQuery(element)

        // get most specific selector
        if (appliedSelectors.length==0) {
            var noSelectorsApplied = true;
        }

        var selectorsList = [], selector;

        // parse DOM up until body
        while (!element.is('html')) {

            var variants = [],
                status = "inactive", 
                bottomAppliedSelector = "";

            if (appliedSelectors.length>0) {
                bottomAppliedSelector = appliedSelectors[appliedSelectors.length-1];
            }
            
            if (element.is("#ct-viewport-container")||element.is("#ct-artificial-viewport")||element.is("#ct-builder")) {
                element = jQuery(element).parent();
                continue;
            }

            /**
             * ID (#my_button_12, ...)
             */

            if ( jQuery(element).attr('id') ) {

                var idStatus = "inactive",
                    idValue = "#"+jQuery(element).attr('id');   

                // "activate" id if found
                if (bottomAppliedSelector.indexOf(idValue) > -1) {
                    idStatus    = "active";
                    status      = "active";
                    // remove from selector
                    bottomAppliedSelector = bottomAppliedSelector.replace(idValue,"");
                }

                variants.push({ value : idValue,
                                status : idStatus });
            }
            

            /**
             * Class (.woocommerce, .button, ...)
             */

            if ( jQuery(element).attr('class') ) {

                classes = jQuery(element).attr('class').split(" ").filter(function(v){
                    
                    // exlcude classes used only in builder
                    excludeClasses = ["oxygen-body", "oxygen-builder-body","ct-highlight-selector","ct-active","ct-inner-wrap","ct-component","ct_code_block",
                                    "ct_div_block","ct_column","ct_columns","ct_div_block","ct_li","ct_section","ct_link","oxy_map","oxy_social_icons","oxy_easy_posts","oxy_soundcloud"];
                    
                    // exclude angular classes
                    if (v.substring(0, 3) == "ng-") {
                        return false;
                    }

                    return excludeClasses.indexOf(v) == -1;
                });
                
                for(var key in classes) { 
                    if (classes.hasOwnProperty(key)) {
                        
                        var classStatus = "inactive",
                            classValue = "."+classes[key];
                        
                        // "activate" class if found
                        if (bottomAppliedSelector.indexOf(classValue) > -1) {
                            classStatus = "active";
                            status      = "active";
                            // remove from selector
                            bottomAppliedSelector = bottomAppliedSelector.replace(new RegExp(classValue, "g"),"");
                        }
                        variants.push({ value : classValue,
                                        status : classStatus });
                    }
                }
            }


            /**
             * Tag (div, li, a, ...)
             */

            var tagStatus = "inactive",
                tagValue = jQuery(element).prop("tagName").toLowerCase();

            if ( bottomAppliedSelector.toLowerCase() == tagValue ) {
                tagStatus   = "active";
                status      = "active";
                // clear selector
                bottomAppliedSelector = "";
            }

            variants.push({ value : tagValue,
                            status : tagStatus });

            // remove bottom selector part if it is empty
            if (!bottomAppliedSelector) {
                appliedSelectors.splice(appliedSelectors.length-1,1);
            }

            var combinedSelector = [];

            // concatanate all selectors parts
            for(var variantKey in variants) {
                if (variants.hasOwnProperty(variantKey) && variants[variantKey].status=="active") {
                    // make sure tagname is in the beginig
                    if (variantKey==variants.length-1) {
                        combinedSelector.splice(0,0,variants[variantKey].value);
                    }
                    else {
                        combinedSelector.push(variants[variantKey].value);
                    }
                }
            }

            if (combinedSelector.length > 0) {
                selector = combinedSelector.join("");
            }
            else {
                selector = variants[0].value;
            }

            // activate first variant by default if no selectors
            if (noSelectorsApplied) {
                variants[0].status = "active";
            }

            // finaly push all the variants
            selectorsList.push({ status : status,
                                 selector : selector,
                                 variants : variants });
            
            element = jQuery(element).parent();
        }

        // activate first selector by default if no selectors
        if (noSelectorsApplied&&selectorsList[0]) {
            selectorsList[0].status = "active";
        }

        //$scope.iframeScope.setCustomSelectorToEdit(selectorsList[0].selector);
        parentScope.buildChooseSelector(selectorsList.reverse());
        $scope.updateSelectorFromParts();
        //$scope.$apply();
    }

    
    /**
     * Update selector when user clicks in the Choose selector box
     * 
     * @since 1.0
     * @author Ilya K.
     */

    $scope.selectorItemAction = function(key) {

        // update status
        $scope.selectorsList[key].status = ($scope.selectorsList[key].status == "active") ? "inactive" : "active";
        
        // update selector to edit
        $scope.updateSelectorFromParts();
    }


    /**
     * Get all "active" selector parts from Choose selector box and set as selector to edit
     * 
     * @since 1.0
     * @author Ilya K.
     */

    $scope.updateSelectorFromParts = function() {

        var combinedSelector = [];

        // concatanate all selectors parts
        for(var key in $scope.selectorsList) { 
            if ($scope.selectorsList.hasOwnProperty(key) && $scope.selectorsList[key].status=="active") {
                combinedSelector.push($scope.selectorsList[key].selector);
            }
        }

        // change selector
        //parentScope.selectorChange($scope.iframeScope.selectorToEdit, combinedSelector.join(" "));

        if ( $scope.existingSelectorFound ) {
            $scope.iframeScope.setCustomSelectorToEdit($scope.existingSelectorFound);
            $scope.disableSelectorDetectorMode();
            $scope.existingSelectorFound = false;
        }
        else {

            if ( $scope.chooseSelectorBehavior == "update" ) {
                parentScope.selectorChange($scope.iframeScope.selectorToEdit, combinedSelector.join(" "));
            }
            
            $scope.iframeScope.setCustomSelectorToEdit(combinedSelector.join(" "));
        }
    }


    /**
     * Activate/deactivate selector part variant and update selector to edit
     * 
     * @since 1.0
     * @author Ilya K.
     */

    $scope.selectorVariantAction = function(key, variantKey) {
        
        // update variant status
        $scope.selectorsList[key].variants[variantKey].status = ($scope.selectorsList[key].variants[variantKey].status == "active") ? "inactive" : "active";
    
        // update selector
        var combinedSelector = [];
        
        for(var variantKey in $scope.selectorsList[key].variants) {
            if ($scope.selectorsList[key].variants.hasOwnProperty(variantKey) && $scope.selectorsList[key].variants[variantKey].status=="active") {
                // make sure tagname is in the beginig
                if (variantKey==$scope.selectorsList[key].variants.length-1) {
                    combinedSelector.splice(0,0,$scope.selectorsList[key].variants[variantKey].value);
                }
                else {
                    combinedSelector.push($scope.selectorsList[key].variants[variantKey].value);
                }
            }
        }

        if (combinedSelector.length > 0) {
            $scope.selectorsList[key].selector = combinedSelector.join("");
        }
        else {
            $scope.selectorsList[key].selector = $scope.selectorsList[key].variants[0].value;
        }

        parentScope.buildChooseSelector();
        $scope.updateSelectorFromParts();
    }

    
    /**
     * Disable Selector Detector Mode
     *
     * @since 1.0
     * @author Ilya K.
     */

    parentScope.disableSelectorDetectorMode = function() {

        if (parentScope.log) {
            console.log("disableSelectorDetectorMode()");
        }
        
        $scope.iframeScope.selectorDetector.mode = false;
        //$scope.selectorsList    = [];
        $scope.appliedSelectors = [];
        parentScope.buildChooseSelector();
        parentScope.cleanEmptySelectors();
    }


    /**
     * Disable Selector Detector Mode
     *
     * @since 2.0
     * @author Ilya K.
     */

    parentScope.enableSelectorDetectorMode = function() {

        if (parentScope.log) {
            console.log("enableSelectorDetectorMode()");
        }

        $scope.iframeScope.selectorDetector.mode = true;
        parentScope.buildChooseSelector();
        parentScope.styleTabAdvance=false;
    }


    /**
     * Switch Selector Detector Mode
     *
     * @since 2.0
     * @author Ilya K.
     */

    parentScope.toggleSelectorDetectorMode = function() {

        if ($scope.iframeScope.selectorDetector.mode) {
            parentScope.disableSelectorDetectorMode();
        }
        else {
            parentScope.enableSelectorDetectorMode();
        }
    }

    
    /**
     * Find selectors applied to the element
     *
     * @since 1.0
     * @author Ilya K.
     */

    $scope.findElementSelectors = function(a) {
        
        var sheets = document.styleSheets, o = [];
        a.matches = a.matches || a.webkitMatchesSelector || a.mozMatchesSelector || a.msMatchesSelector || a.oMatchesSelector;
        
        for (var i in sheets) {
            try {
                var rules = sheets[i].rules || sheets[i].cssRules;
                for (var r in rules) {
                    try {
                        if (a.matches(rules[r].selectorText)) {
                            var selectorsList = rules[r].selectorText.split(',');
                            // find exact selector from "," list
                            for(var index in selectorsList) {
                                if (jQuery(a).is(selectorsList[index])) {
                                    
                                    if (o.indexOf(selectorsList[index].trim())==-1) {
                                        o.push(selectorsList[index].trim());
                                    }
                                }
                                //o[rules[r].selectorText] = rules[r].cssText;
                            }
                        }
                    } 
                    catch(err) {
                        //console.log('skipping ' + rules[r].selectorText);
                    }
                }
            }
            catch(err) {
                //console.log('skipping ' + );
            }
        }

        function filterOxygenClasses(value) {
            var prohibited = ['[','+','~','>',':','*','.ct-highlight-selector', '.ct-selector-highlight', '.ct-selector-detector-overlay', '.ct-active', '.oxygen-body'];
            for (var i = 0; i < prohibited.length; i++) {
                if (value.indexOf(prohibited[i]) > -1) {
                    return false;
                }
            }
            return true
        }
        o = o.filter(filterOxygenClasses);
        o.sort($scope.iframeScope.SPECIFICITY.compare).reverse();

        return o;
    }


    /**
     * Add style set
     *
     * @since 1.0
     * @author Ilya K.
     */
    
    parentScope.addNewStyleSet = function() {

        var setName = parentScope.newStyleSetName;

        if(typeof(setName) === 'undefined' || setName.trim() === '') {
            setName = prompt("Style set name:");
        }

        if(typeof(setName) === 'undefined' || setName.trim() === '') {
            return false;
        }

        for(var set in $scope.iframeScope.styleSets) {
            if ($scope.iframeScope.styleSets.hasOwnProperty(set) && set == setName) {
    
                alert("Style set with this name already exist.");
                return false;
            }
        }

        if (setName!=null) {
            
            $scope.iframeScope.styleSets[setName] = {};
            $scope.iframeScope.unsavedChanges();
            
            parentScope.setSelectorStyleSet(setName);
            parentScope.newStyleSetName = "";

            if (!$scope.iframeScope.customSelectors[$scope.iframeScope.selectorToEdit]) {
                $scope.iframeScope.customSelectors[$scope.iframeScope.selectorToEdit] = [];
            }

            if (!$scope.iframeScope.customSelectors[$scope.iframeScope.selectorToEdit]['set_name']) {
                $scope.iframeScope.customSelectors[$scope.iframeScope.selectorToEdit]['set_name'] = setName;
            }
        }
    }


    /**
     * Delete selector style set and all its selectors
     *
     * @since 1.0
     * @author Ilya K.
     */

    parentScope.deleteStyleSet = function(set) {
        
        var confirmed = confirm("Are you sure to delete \""+set+"\" style set and all its selectors?");
        
        if (!confirmed) {
            return false;
        }

        // delete all selectors
        for(var selector in $scope.iframeScope.customSelectors) { 
            if ($scope.iframeScope.customSelectors.hasOwnProperty(selector) && $scope.iframeScope.customSelectors[selector]["set_name"] == set) {
                $scope.iframeScope.customSelectors[selector] = null;
                delete $scope.iframeScope.customSelectors[selector];
            }
        }

        delete $scope.iframeScope.styleSets[set];
        $scope.iframeScope.outputCSSOptions();
        $scope.iframeScope.unsavedChanges();
    }


    /**
     * Delete style set
     *
     * @since 1.0
     * @author Ilya K.
     */

    parentScope.setSelectorStyleSet = function(setName) {
        if (!$scope.iframeScope.customSelectors[$scope.iframeScope.selectorToEdit]) {
            $scope.iframeScope.customSelectors[$scope.iframeScope.selectorToEdit] = {};
        }
        $scope.iframeScope.customSelectors[$scope.iframeScope.selectorToEdit]['set_name'] = setName;
    }


    /**
     * Primary purpose is to listen for a return key input in the new class name textbox
     * 
     * @since 1.0
     * @author Ilya K.
     */

    parentScope.processStyleSetNameInput = function(e) {

        // create the Style Set if it is a return key
        if(e.keyCode === 13) {
            parentScope.addNewStyleSet();

            // hide the dropdown
            jQuery(".oxygen-select", parentScope.oxygenUIElement).trigger("click");// hide the dropdown
        }
    }


    /**
     * Check if selector detector availbale for currently active component
     *
     * @since 2.0
     * @author Ilya K.
     */

    parentScope.isSelectorComponent = function(excludeComponent) {

        if (parentScope.isActiveName(excludeComponent) ) {
            return false;
        }        

        if (parentScope.isActiveName('ct_widget') ||
            parentScope.isActiveName('ct_code_block') ||
            parentScope.isActiveName('ct_shortcode') ||
            parentScope.isActiveName('oxy_posts_grid') ||
            parentScope.isActiveName('oxy_rich_text') ||
            parentScope.isActiveName('oxy_nav_menu') ||
            parentScope.isActiveName('oxy_comments') ||
            parentScope.isActiveName('oxy_comment_form') ||
            parentScope.isActiveName('oxy_login_form') ||
            parentScope.isActiveName('oxy_search_form') ||
            parentScope.isActiveName('oxy_gallery') ||
            parentScope.isActiveName('ct_sidebar') ) {
            return true;
        }

        return false;
    }


    /**
     * Clear empty selectors
     *
     * @since 2.0
     * @author Ilya K.
     */

    parentScope.cleanEmptySelectors = function() {

        for(var selector in $scope.iframeScope.customSelectors) { 
            if ($scope.iframeScope.customSelectors.hasOwnProperty(selector) && 
                (selector === undefined || selector === "")) {
                $scope.iframeScope.customSelectors[selector] = null;
                delete $scope.iframeScope.customSelectors[selector];
            }
        }
    }

})