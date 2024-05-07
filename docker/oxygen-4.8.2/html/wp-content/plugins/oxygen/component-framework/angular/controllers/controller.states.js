/**
 * All states manipulations is here
 * 
 */

CTFrontendBuilder.controller("ComponentsStates", function($scope, $parentScope) {

    $scope.currentState = "original";

    /**
     * Switch currently editing state
     *
     * @since 0.1.4
     */
    
    $scope.switchState = function(state, setClass, id) {

        if ($scope.log) {
            console.log("switchState()", state, setClass, id);
        }
        
        // do nothing if trying to activate currently active state
        if ($scope.currentState == state) {
            return false;
        }

        if ( id == undefined ) {
            id = $scope.component.active.id;
        }

        if (!setClass) {
            $scope.showClasses = false;
        }

        // set current state
        $scope.currentState = state;
        
        // highlight selectors according to new state
        $scope.highlightSelector(true, $scope.selectorToEdit);

        // apply options
        $scope.applyModelOptions();

        // hide states list dropdown
        $scope.showStates = false;

        // update styles
        $scope.outputCSSOptions(id);

        $parentScope.checkTabs();

        //close backgroundlayers when component is switched
        $scope.parentScope.activeForEditBgLayer = false;
    }

    
    /**
     * Add new state to the component
     *
     * @since 0.1.8
     */
    
    $scope.addState = function() {

        var stateName = prompt("Pseudo-class name:");
        
        if (stateName != null) {

            // remove the first ( and second) starting colen, if provided
            if(stateName.indexOf(':') === 0) {
                stateName = stateName.substring(1, stateName.length);
            }
            if(stateName.indexOf(':') === 0) {
                stateName = stateName.substring(1, stateName.length);
            }

            $scope.switchState(stateName);

            // add state to active component in Components Tree
            $scope.findComponentItem($scope.componentsTree.children, $scope.component.active.id, $scope.addStateToComponent, stateName);
        }

        $scope.unsavedChanges();
    }


    /**
     * Ask user if he wants to delete a state styles from component
     *
     * @since 0.2.5
     */
    
    $scope.tryDeleteComponentState = function(state, event) {

        $scope.cancelDeleteUndo();

        event.stopPropagation();

        var confirmed = confirm("Remove \""+state+"\" styles?");
        
        if (!confirmed) {
            return false;
        }

        $scope.deleteComponentState(state);
    }


    /**
     * Delete state from component
     *
     * @since 0.2.5
     */
    
    $scope.deleteComponentState = function(state, id) {

        if ($scope.log) {
            console.log("deleteComponentState", state);
        }

        if (undefined === id) {
            id = $scope.component.active.id;
        }

        // remove from class
        if ($scope.isEditing('class')) {
            $scope.removeStateFromClass($scope.currentClass, state);
        }
        else if ($scope.isEditing('custom-selector')) {
            $scope.removeStateFromCustomSelector($scope.selectorToEdit, state);   
        }
        else {
            // remove state from active component in Components Tree
            $scope.findComponentItem($scope.componentsTree.children, id, $scope.removeStateFromTree, state);

            // delete from options
            if ($scope.isEditing("media")) {
                if ($scope.component.options[id]['media'] &&
                    $scope.component.options[id]['media'][$scope.currentMedia]) {
                    delete $scope.component.options[id]['media'][$scope.currentMedia];
                }
            }
            else {
                if ($scope.component.options[id][state]) {
                    delete $scope.component.options[id][state];
                }
            }

        }
        
        // update CSS output if editing original
        if ($scope.isCurrentState("original")) {
            $scope.outputCSSOptions(id);
        }

        $scope.switchState("original");
        $scope.unsavedChanges();
    }


    /**
     * Remove state from tree
     *
     * @since 0.1.4
     */
    
    $scope.removeStateFromTree = function(key, item, state) {

        if ($scope.log) {
            console.log("removeStateFromTree()", key, state, item);
        }

        // no need for root
        if ( key == 0 )
            return false;

        else {
            // remove state node
            if ($scope.isEditing("media")) {
                if ( item.options['media'][$scope.currentMedia][state] !== undefined ){
                    delete item.options['media'][$scope.currentMedia][state];
                }
            }
            else {
                if ( item.options[state] !== undefined ){
                    delete item.options[state];
                }
            }
        }
    }


    /**
     * Add state to component in tree and options
     *
     * @since 0.3.2
     */
    
    $scope.addStateToComponent = function(id, item, stateName) {

        if ($scope.log) {
            console.log("addStateToComponent()", id, stateName, item);
        }

        // no need for root
        if ( id == 0 )
            return false;

        else {
            // add state in tree
            item.options[stateName] = {};

            // add state to options
            $scope.component.options[id][stateName] = {};
        }
    }


    /**
     * Remove state from class
     *
     * @since 0.1.4
     */
    
    $scope.removeStateFromClass = function(className, state) {

        // remove state node
        if ($scope.isEditing("media")) {
            if ( $scope.classes[className]['media'][$scope.currentMedia][state] !== undefined ) {
                delete $scope.classes[className]['media'][$scope.currentMedia][state];
            }
        }
        else {
            if ( $scope.classes[className][state] !== undefined ) {
                delete $scope.classes[className][state];
            }
        }
    }


    /**
     * Remove state from custom selector
     *
     * @since 1.3
     * @author Ilya K.
     */
    
    $scope.removeStateFromCustomSelector = function(selector, state) {

        // remove state node
        if ($scope.isEditing("media")) {
            if ( $scope.customSelectors[selector]['media'][$scope.currentMedia][state] !== undefined ) {
                delete $scope.customSelectors[selector]['media'][$scope.currentMedia][state];
            }
        }
        else {
            if ( $scope.customSelectors[selector][state] !== undefined ) {
                delete $scope.customSelectors[selector][state];
            }
        }
    }


    /**
     * Check if current state 
     *
     * @since 0.1.7
     */
    
    $scope.isCurrentState = function(state) {

        return ( $scope.currentState == state ) ? true : false;
    }
    

    /**
     * Check if state is pseudo-element by it's name
     *
     * @since 0.3.0
     */
    
    $scope.isPseudoElement = function(name) {

        if ( 
                name.indexOf("before")          > -1 || 
                name.indexOf("after")           > -1 ||
                name.indexOf("first-letter")    > -1 || 
                name.indexOf("first-line")      > -1 || 
                name.indexOf("selection")       > -1
            ) 
        {
            return true;
        }
        else {
            return false;
        }
    }


    /**
     * Get current component or class states
     *
     * @since 0.3.2
     * @return {array}
     */

    $scope.getComponentStatesList = function(id) {

        if (undefined===id) {
            id = $scope.component.active.id;
        }

        var source,
            statesList  = ["hover","before","after"];

        if ( $scope.isEditing("id") ) {
            source = $scope.component.options[id];
        }
        else 
        if ( $scope.isEditing("class") ) {
            source = $scope.classes[$scope.currentClass];
        }
        else 
        if ( $scope.isEditing("custom-selector") ) {
            source = $scope.customSelectors[$scope.selectorToEdit];
        }

        if ( source ) {
            states = Object.keys(source);

            for(var index in states) { 

                var stateName = states[index];

                if ( typeof source[stateName] === 'object' && 
                     stateName != "media"       && 
                     stateName != "model"       && 
                     stateName != "original"    && 
                     stateName != "hover"       && 
                     stateName != "before"      && 
                     stateName != "after"       && 
                     stateName != "id") 
                {    
                    statesList.push(stateName);
                }
            }
        }

        return statesList;
    }


    /**
     * Get component states
     *
     * @since 0.3.2
     * @return {array}
     */

    $scope.isStateHasOptions = function(stateName) {

        var id = $scope.component.active.id;
            state = false;

        if ($scope.isEditing("id")) {
            
            if ($scope.isEditing("media")) {
                
                if ($scope.component.options[id]['media'] &&
                    $scope.component.options[id]['media'][$scope.currentMedia]) {

                    state = $scope.component.options[id]['media'][$scope.currentMedia][stateName];
                }
            }
            else {
                state = $scope.component.options[id][stateName];
            }
        }

        if ($scope.isEditing("class") && $scope.classes[$scope.currentClass]) {

            if ($scope.isEditing("media")) {
                
                if ( $scope.classes[$scope.currentClass]['media'] && 
                     $scope.classes[$scope.currentClass]['media'][$scope.currentMedia]) {

                    state = $scope.classes[$scope.currentClass]['media'][$scope.currentMedia][stateName];
                }
            }
            else {
                state = $scope.classes[$scope.currentClass][stateName];
            }
        }

        if ($scope.isEditing("custom-selector") && !$scope.isEditing("class") && $scope.customSelectors[$scope.selectorToEdit]) {

            if ($scope.isEditing("media")) {
                
                if ( $scope.customSelectors[$scope.selectorToEdit]['media'] && 
                     $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia]) {

                    state = $scope.customSelectors[$scope.selectorToEdit]['media'][$scope.currentMedia][stateName];
                }
            }
            else {
                state = $scope.customSelectors[$scope.selectorToEdit][stateName];
            }
        }

        return (state && !jQuery.isEmptyObject(state)) ? true : false;
    }


    /**
     * Get component states
     *
     * @since 0.3.2
     * @return {array}
     */

    $scope.isStatesHasOptions = function() {

        var states = $scope.getComponentStatesList();

        for(var index in states) { 

            var stateName = states[index];

            if ($scope.isStateHasOptions(stateName)) {
                return true;
            }
        }

        return false;
    }

// End ComponentStates controller
});