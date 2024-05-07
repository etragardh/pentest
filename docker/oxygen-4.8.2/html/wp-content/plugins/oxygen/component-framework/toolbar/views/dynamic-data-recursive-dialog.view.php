<script type="text/ng-template" id="dynamicDataRecursiveDialog">
    <label ng-if="property.name&&property.type!=='checkbox'&&property.type!=='button'" class="oxygen-control-label" ng-show="fieldIsVisible(property)"> {{property.name}} </label>
    <!-- dropdown -->
    <div ng-if="property.type==='select'" class="oxygen-select oxygen-select-box-wrapper oxygen-control-wrapper" ng-show="fieldIsVisible(property)">
        <div class="oxygen-select-box">
            <div class="oxygen-select-box-current">{{dynamicDataModel[property.data]}}</div>
            <div class="oxygen-select-box-dropdown"></div>
        </div>
        <div class="oxygen-select-box-options">
            <div ng-repeat="(label, name) in property.options" ng-click="dynamicDataModel[property.data]=name;applyChange(property)" class="oxygen-select-box-option">{{label}}</div>
        </div>
    </div>
    <!-- hidden -->
    <div class="ng-hide" ng-if="property.type==='hidden'">
        <input type="hidden" ng-init="dynamicDataModel[property.data] = property.value" ng-model="dynamicDataModel[property.data]" />
    </div>
    <!-- input -->
    <div class="oxygen-input oxygen-control-wrapper" ng-if="property.type==='text'" ng-show="fieldIsVisible(property)">
        <input type="text" ng-model="dynamicDataModel[property.data]" ng-change="applyChange(property)" ng-trim="false"/>
    </div>
    <!-- checkbox -->
    <label class="oxygen-checkbox oxygen-control-wrapper" ng-if="property.type==='checkbox'" ng-show="fieldIsVisible(property)">{{property.name}}
        <input type="checkbox" ng-model="dynamicDataModel[property.data]" ng-true-value="'{{property.value}}'" ng-change="applyChange(property)" />
        <div class="oxygen-checkbox-checkbox" ng-class="{'oxygen-checkbox-checkbox-active':dynamicDataModel[property.data]=='{{property.value}}'}"></div>
    </label>
    <!-- button, for navigation -->
    <div class="oxygen-button" 
        ng-show="fieldIsVisible(property)"
        ng-class="{'oxygen-settings-page-button':property['settings_page']}"
        ng-if="property.type==='button'"  ng-mouseup="dynamicDataModel['settings_pages']=property['settings_pages'];dynamicDataModel['settings_page']=property['settings_page'];processCallback(property, dataitem, true); $event.stopPropagation();">
        <span>{{property.name}}</span>
        <div ng-if="property.properties" ng-show="showOptionsPanel.item === property.name+property.data+dataitem.data || isChildPanelVisible( property, dataitem )" class="oxygen-data-dialog-options" ng-mouseup="$event.stopPropagation();">
            <h1>{{dataitem.name}} Options - {{property.name}}</h1>
            <div>
                <div class="oxygen-data-back-button" ng-mouseup="back()">&lt; BACK</div>
                <div ng-repeat="property in property.properties" 
                     ng-class="{inline: property.type=='button'}"
                     ng-init="property['settings_page']=$parent.property['settings_page']">
                    <div  ng-include="'dynamicDataRecursiveDialog'" ng-class="{inline: property.type=='button'}"></div>
                </div>
            </div>
            <div class="oxygen-apply-button" ng-mouseup="processCallback(item, dataitem)" ng-show="!isNavigationOnlyPanel(property)">INSERT</div>
        </div>
    </div>

    <br ng-if="property.type==='break'" ng-show="fieldIsVisible(property)"/>
</script>
