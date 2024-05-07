
CTFrontendBuilder.controller("ControllerConditions", function($scope, $parentScope, $timeout) {    


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

});