<div ng-if="dialogForms['ifCondition']" id='ct-modal-if-conditions' class='ct-global-conditions-add-modal ct-global-conditions-choose-operator oxygen-data-dialog'>
    <h1>
        <?php _e("Conditions", "oxygen"); ?>
        <svg class="oxygen-close-icon" ng-click="hideDialogWindow()"><use xlink:href="#oxy-icon-cross"></use></svg>
    </h1>

    <div class='oxygen-condition-builder-condition'
        ng-repeat="(index, condition) in iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions']">
                
                <div class='oxygen-control'>
                    <div class="oxygen-select oxygen-select-box-wrapper"
                        ng-click="toggleOxygenSelectBox($event)">
                        
                        <div class="oxygen-select-box">
                            <div class="oxygen-select-box-current oxy-tooltip">
                                <span ng-if="condition.name != 'ZZOXYVSBDYNAMIC'">{{condition.name}}</span>
                                <span ng-if="condition.name == 'ZZOXYVSBDYNAMIC'">{{condition.oxycode}}</span>
                                <span class="placeholder-text" ng-if="!condition.name"><?php _e("Choose Condition...","oxygen");?></span>
                                <div class="oxy-tooltip-text" ng-if="condition.name">
                                    <span ng-if="condition.name != 'ZZOXYVSBDYNAMIC'">{{condition.name}}</span>
                                    <span ng-if="condition.name == 'ZZOXYVSBDYNAMIC'">{{condition.oxycode}}</span>
                                </div>
                            </div>
                            <div class="oxygen-select-box-dropdown"></div>
                        </div>
                        
                        <div class="oxygen-select-box-options">
                            <div class="oxygen-conditions-group-container"
                                ng-repeat="(groupname, group) in iframeScope.globalConditionsGrouped">
                                <div class="oxygen-conditions-group-label">{{groupname}}</div>
                                <div ng-repeat="item in group">
                                    
                                    <div class="oxygen-select-box-option"
                                        ng-if="item.name == 'ZZOXYVSBDYNAMIC'"
                                        ng-click="conditionsDialogOptions.selectedIndex = index"
                                        ctdynamicdata="" noshadow="1" backbutton=true data="iframeScope.dynamicShortcodesContentMode" callback="assignOxyCodeToCondition">
                                        <?php _e("Dynamic Data","oxygen");?>
                                    </div>

                                    <div class="oxygen-select-box-option"
                                        ng-if="item.name != 'ZZOXYVSBDYNAMIC'"
                                        ng-click="
                                        iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['operator'] = (iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['name']!==item.name) ? 0:iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['operator'];

                                        iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['value'] = (iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['name']!==item.name) ? '':iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['value'];

                                        iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['name']=item.name; 

                                        iframeScope.loadConditionsOptions(index,iframeScope.globalConditions[iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['name']]['values']['callback']);
                                                    
                                        iframeScope.setOptionModel('globalconditions', iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions']); evalGlobalConditions(); evalGlobalConditionsInList()"
                                        >
                                        {{item.name}}
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <!-- .oxygen-select-box-options -->
                    </div>
                    <!-- .oxygen-select.oxygen-select-box-wrapper -->
                </div>
            

                <div class='oxygen-control'>
                    <div class="oxygen-select oxygen-select-box-wrapper"
                        ng-click="toggleOxygenSelectBox($event)">
                        <div class="oxygen-select-box">
                            <div class="oxygen-select-box-current oxy-tooltip">
                               {{iframeScope.globalConditions[iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['name']]['operators'][condition.operator]}}
                               <span class="placeholder-text" ng-if="condition.operator === null">{{condition.operator}}<?php _e("==","oxygen");?></span>
                               <div class="oxy-tooltip-text"  ng-if="condition.operator !== null">{{iframeScope.globalConditions[iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['name']]['operators'][condition.operator]}}</div>
                            </div>
                            <div class="oxygen-select-box-dropdown"></div>
                        </div>
                        <div class="oxygen-select-box-options">

                            <div class="oxygen-select-box-option"
                                ng-repeat="operator in iframeScope.globalConditions[iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['name']]['operators']" ng-click="iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['operator']=$index; 
                        
                                iframeScope.setOptionModel('globalconditions', iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions']); evalGlobalConditions(); evalGlobalConditionsInList()"
                                >
                                {{operator}}
                            </div>
                            
                        </div>
                        <!-- .oxygen-select-box-options -->
                    </div>
                    <!-- .oxygen-select.oxygen-select-box-wrapper -->
                </div>

                <div class='oxygen-control'>
                    <div class="oxygen-select oxygen-select-box-wrapper"
                        ng-click="toggleOxygenSelectBox($event)">
                        <div class="oxygen-select-box">
                            <div class="oxygen-select-box-current oxy-tooltip">
                                
                                <input class="global-conditions-custom-value" type="text" value="" spellcheck="false" 
                                    placeholder="{{iframeScope.getConditionPlaceholder(index)}}" 
                                    ng-if="iframeScope.isCustomCondition(index)"
                                    ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['value']"
                                    ng-model-options='{debounce:1000}'
                                    ng-change="iframeScope.setOptionModel('globalconditions', iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions']); 
                                    evalGlobalConditions(); evalGlobalConditionsInList();"/>

                                <input class="global-conditions-custom-value" type="text" value="" spellcheck="false" 
                                    placeholder="{{iframeScope.getConditionPlaceholder(index)}}" 
                                    ng-if="iframeScope.isAJAXCondition(index)"
                                    ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['searchValue']"
                                    ng-model-options='{debounce:500}'
                                    ng-change="iframeScope.setOptionModel('globalconditions', iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions']); 
                                    evalGlobalConditions(); evalGlobalConditionsInList(); iframeScope.loadConditionsOptions(index,iframeScope.globalConditions[iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['name']]['values']['callback']);"/>

                                <div class="oxygen-dynamic-data-browse" 
                                    ng-if="iframeScope.isCustomCondition(index)" 
                                    ng-click="conditionsDialogOptions.selectedIndex = index"
                                    ctdynamicdata noshadow="1" backbutton=true 
                                    data="iframeScope.dynamicShortcodesContentMode" 
                                    callback="assignOxyCodeToConditionValue">
                                    <?php _e("data","oxygen"); ?>
                                </div>

                                <span
                                    ng-if="!iframeScope.isCustomCondition(index)&&!iframeScope.isAJAXCondition(index)">
                                    {{iframeScope.getConditionValue(index,condition)}}
                                </span>
                                
                                <span class="placeholder-text" 
                                    ng-if="condition.value && !iframeScope.globalConditions[iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['name']]['values']['custom']===null"><?php _e("Value...","oxygen");?>
                                </span>

                                <div class="oxy-tooltip-text" 
                                    ng-if="condition.value&&!iframeScope.isAJAXCondition(index)">
                                    {{iframeScope.getConditionValue(index,condition)}}
                                </div>
                            </div>
                            
                            <div class="oxygen-select-box-dropdown" 
                                ng-if="!iframeScope.isCustomCondition(index) && (iframeScope.globalConditions[iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['name']]['values']['options'].length || iframeScope.globalConditions[iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['name']]['values']['keys'])">
                            </div>
                        </div>

                        <div class="oxygen-select-box-options">
                            <div class="oxygen-select-box-option global-conditions-value-item"
                                ng-repeat="(key, value) in iframeScope.globalConditions[iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'][index]['name']]['values']['options'] track by key">
                                <span 
                                    ng-click="iframeScope.setConditionValue(index,key,value); 
                                    evalGlobalConditions(); evalGlobalConditionsInList()">
                                    {{value}}
                                </span>
                            </div>
                            
                        </div>
                        <!-- .oxygen-select-box-options -->
                    </div>
                    <!-- .oxygen-select.oxygen-select-box-wrapper -->
                </div>

                <div class='oxygen-condition-builder-condition-delete'
                    ng-click="iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'].splice(index, 1); iframeScope.setOptionModel('globalconditions', iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions']); evalGlobalConditions(); evalGlobalConditionsInList()">
                    
                    <img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/cancel-circle.svg' />
                </div>
        </div>

        <div class='oxygen-condition-builder-add-condition' 
            ng-if="iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'].length > 0">
            
            <a ng-click="iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'] = iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'] || []; iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'].push({name: '', operator: null, value: ''}); iframeScope.setOptionModel('globalconditions', iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions']); evalGlobalConditions(); evalGlobalConditionsInList()"><?php _e("Add Condition","oxygen");?></a>
        </div> 

        <div class="oxygen-add-button"
            ng-if="!iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'].length"
            ng-click="iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'] = iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'] || []; iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions'].push({name: '', operator: null, value: ''}); iframeScope.setOptionModel('globalconditions', iframeScope.component.options[iframeScope.component.active.id]['model']['globalconditions']); evalGlobalConditions(); evalGlobalConditionsInList()">
            
            <span><?php _e("Add your first condition","oxygen");?></span>
        </div>
</div>