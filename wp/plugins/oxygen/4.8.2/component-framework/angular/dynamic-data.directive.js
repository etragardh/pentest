CTCommonDirectives.directive("ctdynamicdata", function($compile, ctScopeService) {
    return {
        restrict: "A",
        replace: true,
        scope: {
            data: "=",
            result: "=",
            resultkey: "@",
            callback: "=",
            noshadow: "=",
            backbutton: "=",
            optionname: "=",
        },
        link: function(scope, element, attrs) {

            angular.element('body').on('click', '.oxy-dynamicdata-popup-background, .oxygen-data-close-dialog', function() {
                angular.element('#ctdynamicdata-popup').remove();
                angular.element('.oxy-dynamicdata-popup-background').remove();
            });

            scope.dynamicDataModel = {};
            scope.showOptionsPanel = { item: false };
            scope.processCallback = function(item, dataitem, showOptions) {

                if(showOptions /*&& dataitem.properties && dataitem.properties.length > 0*/) {
                    scope.showOptionsPanel.item = item.name+item.data+dataitem.data;
                    if (item.type == "button") {
                        if (typeof scope.dynamicDataModel['settings_path'] === 'undefined') {
                            scope.dynamicDataModel['settings_path'] = item.data;
                        }
                        else {
                           scope.dynamicDataModel['settings_path'] = scope.dynamicDataModel['settings_path'] + "/" + item.data;
                        }
                    }
                }

                if((scope.callback || scope.result !== undefined) && (!item.properties || item.properties.length == 0 )) {
                
                    var shortcode = '[oxygen data="'+dataitem.data+'"';
                    
                    var finalVals = {};

                    var checkProperties = function(property){
                        if( scope.dynamicDataModel.hasOwnProperty(property.data) && 
                            scope.dynamicDataModel[property.data].trim !== undefined &&
                            scope.dynamicDataModel[property.data].trim()!=='' &&
                            !property.helper && 
                            scope.dynamicDataModel[property.data] !== property.nullVal && 
                            scope.fieldIsVisible( property )) {
                            
                                finalVals[property.data] = scope.dynamicDataModel[property.data];
                        }
                        _.each(property.properties, function(property) {
                            checkProperties( property );
                        });
                    };

                    _.each(dataitem.properties, function(property) {
                        checkProperties( property );
                    });

                    _.each(finalVals, function(property, key) {
                        property = property.replace(/'/g, "__SINGLE_QUOTE__");
                        shortcode+=' '+key+'="'+property+'"';
                    })

                    if(dataitem['append']) {
                        shortcode+=' '+dataitem['append'];
                    }

                    if (typeof scope.dynamicDataModel['settings_path'] !== 'undefined') {
                        shortcode+=' settings_path="'+scope.dynamicDataModel['settings_path']+'"';
                    }

                    if (typeof scope.dynamicDataModel['settings_page'] !== 'undefined') {
                        shortcode+=' settings_page="'+scope.dynamicDataModel['settings_page']+'"';
                    }

                    if (typeof scope.dynamicDataModel.map !== 'undefined') {
                        shortcode+=" metabox_field_type='map'";
                    }

                    shortcode+=']';
                    if (scope.optionname!=undefined) {
                        
                        if(scope.result !== undefined) {
                            if(scope.resultkey !== undefined) {
                                scope.result[scope.resultkey] = shortcode;    
                            } else {
                                scope.result = shortcode;    
                            }
                        }

                        if(scope.callback) {
                            scope.callback(shortcode, scope.optionname, element);
                        }
                        
                    }
                    else {
                        
                        if(scope.result !== undefined) {
                            if(scope.resultkey !== undefined) {
                                scope.result[scope.resultkey] = shortcode;    
                            } else {
                                scope.result = shortcode;    
                            }
                        }

                        if(scope.callback) {
                            scope.callback(shortcode)  
                        }
                    };

                    angular.element('#ctdynamicdata-popup').remove();
                    angular.element('.oxy-dynamicdata-popup-background').remove();
                }
                //scope.dynamicDataModel={};

                // convert to Shortcode element
                if (scope.dynamicDataModel['use_default_output_format']=="yes"||
                    scope.dynamicDataModel['use_default_taxonomy_output_format']=="yes") {
                    
                    $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTreeComponentTag, "ct_shortcode");
                    $scope.iframeScope.setOptionModel('full_shortcode',shortcode)
                    $scope.iframeScope.setOptionModel('ct_content',"")
                }

                // update elements to make Google Map to be visible 
                if (scope.dynamicDataModel.map==true && dataitem.data=="custom_metabox_content") {
                    var width = $scope.iframeScope.getOption('width');
                    if (!width) {
                        $scope.iframeScope.setOptionModel('width',"100");
                        $scope.iframeScope.setOptionModel('width-unit',"%");
                    }
                }

                // save that we are using maps, so it can update model on next iteration (INSERT button press)
                if (item.metabox_field_type == "map"||item.metabox_field_type == "osm") {
                    scope.dynamicDataModel['map'] = true;
                }
            }

            scope.applyChange = function(property) {
                if (property.data=='separator') {
                    scope.dynamicDataModel[property.data] = scope.dynamicDataModel[property.data].replace("]","").replace("[","");
                }
                if(property.change) {
                    eval(property.change);
                }
            }

            /*
            * Get the user back to the root panel
            * */
            scope.back = function( localScope ) {
                scope.dynamicDataModel={};
                scope.showOptionsPanel.item = false;
            }

            /*
            * Determines if a field should be visible by evaluating the dynamic condition, if set
            * */
            scope.fieldIsVisible = function( item ) {
                // hide all controls if default output choosen
                if (scope.dynamicDataModel.use_default_output_format=='yes' &&
                    item.data !== 'use_default_output_format' &&
                    item.data !== 'settings_page' &&
                    // only children items
                    !item.properties) {
                    return false;
                }
                if( typeof item.show_condition === 'undefined' ) return true;
                return scope.$eval( item.show_condition );
            }

            /*
            * Recursive function that determines if a child panel is visible, in order to make the parent one visible too
            */
            scope.isChildPanelVisible = function( item, dataitem ) {
                if( !scope.showOptionsPanel.item ) return false;
                if( item.properties ) {
                    var result = false;
                    for( var i = item.properties.length -1; i >=0; i--) {
                        if( scope.showOptionsPanel.item === item.properties[i].name + item.properties[i].data + dataitem.data ) {
                            return true;
                        } else if( item.properties[i].properties ) {
                            result = scope.isChildPanelVisible( item.properties[ i ], dataitem );
                            if(result) return true;
                        }
                    }
                    return result;
                } else return false;
            }

            /*
            * Determines if the current panel is a navigation-only panel, to know if we should make the "INSERT" button visible or not
            */
            scope.isNavigationOnlyPanel = function( item ) {
                var result = true;
                for( var i = item.properties.length -1; i >= 0; i-- ){
                    if( item.properties[i].type != 'button' && item.properties[i].type != "heading" && item.properties[i].type != "label" ){
                        result = false;
                        break;
                    }
                }
                return result;
            };

            element.on('click', function($event) {

                var componentID = scope.$parent.$parent.iframeScope.component.active.id;

                var component = scope.$parent.$parent.iframeScope.getComponentById(componentID);
                
                var repeater = component.parent().closest('.oxy-dynamic-list');

                var repeaterID = false;

                while(repeater && repeater.length > 0) {
                    repeaterID = parseInt(repeater.attr('ng-attr-component-id'));
                    if(scope.$parent.$parent.iframeScope.component.options[repeaterID]['original']['use_acf_repeater']||
                       scope.$parent.$parent.iframeScope.component.options[repeaterID]['original']['use_metabox_clonable_group']) {
                        repeater = false;
                    }
                    else {
                        repeaterID = false;
                        repeater = repeater.parent().closest('.oxy-dynamic-list');
                    }
                }

                scope.$parent.$parent.acfRepeaterDynamicDialogProcess(repeaterID);
                scope.$parent.$parent.metaBoxRepeaterDynamicDialogProcess(repeaterID);
                

                scope.showOptionsPanel.item = false;
                scope.dynamicDataModel={};
                angular.element('body #ctdynamicdata-popup').remove();
                angular.element('body .oxy-dynamicdata-popup-background').remove();

                if ($event.target.closest('.oxygen-toolbar-button')){
                    scope.dynamicDataModel['triggered_from_text_element'] = true;
                }
                else {
                    scope.dynamicDataModel['triggered_from_text_element'] = false;
                }
                
                var template = '<div class="oxy-dynamicdata-popup-background"></div>'+
                        '<div id="ctdynamicdata-popup" class="oxygen-data-dialog'+(scope.noshadow?' ct-global-conditions-add-modal':'')+'">'+
                        '<h1>Insert Dynamic Data</h1>'+
                        '<p style="font-size: 14px">Please <a style="color: var(--oxy-light-text)" target="_blank" href="https://oxygenbuilder.com/documentation/other/security/#filtering-dynamic-data">review this security information</a> if you plan to render untrusted data via Oxygen\’s dynamic data functions.</p>'+
                        '<div>';

                if(CtBuilderAjax.freeVersion) {
                    template+= '<div style="border: 4px solid #7046db;-webkit-font-smoothing: antialiased;background-color: white;width: 100%;margin-bottom: 16px;padding: 24px;display: flex;line-height: var(--oxy-big-line-height);flex-direction: column;align-items: center;">'+
                        '<h2 style="'+
                        'color: black;'+
                        'font-weight: var(--oxy-regular-font-weight);'+
                        'text-align: center;'+
                        'line-height: var(--oxy-small-line-height);'+
                        'font-size: 21px;'+
                    '">Dynamic Data requires Oxygen Pro.</h2>'+
                        '<a target="_blank" href="https://oxygenbuilder.com/upgrade-to-pro/?utm_source=free-version&utm_medium=in-plugin&utm_content=dynamic-data" style="'+
                        'padding-top: 11px;'+
                        'padding-bottom: 11px;'+
                        'padding-right: 24px;'+
                        'padding-left: 24px;'+
                        'color: white;'+
                        'background-color: #7046db;'+
                        '-webkit-font-smoothing: antialiased;'+
                        '-moz-osx-font-smoothing: grayscale;'+
                        'border-radius: 3px;'+
                        'font-weight: var(--oxy-regular-font-weight);'+
                        'text-decoration: none;'+
                        'box-shadow: 0px 1px 0px 0px #4016ab;'+
                        'margin-top: 16px;'+
                    '">Get Oxygen Pro</a>'+
                        '</div>';
                }
                if(scope.backbutton) {
                    template += '<div class="oxygen-data-back-button oxygen-data-close-dialog">&lt; BACK</div>';
                }
                            template+= '<div class="oxygen-data-dialog-data-picker"'+
                                    'ng-repeat="item in data">'+
                                    '<h2>{{item.name}}</h2>'+
                                    '<ul>'+
                                        '<li ng-repeat="dataitem in item.children" ng-mouseup="processCallback(dataitem, dataitem, true); $event.stopPropagation();">'+
                                            '<span>{{dataitem.name}}</span>'+
                                            '<div ng-if="dataitem.properties" ng-show="showOptionsPanel.item === dataitem.name+dataitem.data+dataitem.data || isChildPanelVisible( dataitem, dataitem )" class="oxygen-data-dialog-options" ng-mouseup="$event.stopPropagation();">'+
                                                '<h1>{{dataitem.name}} Options</h1>'+
                                                '<div>'+
                                                    '<div class="oxygen-data-back-button" ng-mouseup="back()">&lt; BACK</div>'+
                                                    '<div ng-repeat="property in dataitem.properties" ng-class="{inline: property.type==\'button\'}">'+
                                                        '<div  ng-include="\'dynamicDataRecursiveDialog\'" ng-class="{inline: property.type==\'button\'}"></div>'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class="oxygen-apply-button" ng-mouseup="processCallback(item, dataitem)" ng-show="!isNavigationOnlyPanel(dataitem)">INSERT</div>'+
                                            '</div>'+
                                        '</li>'+
                                    '</ul>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

                var compiledElement = $compile(template)(scope);

                scope.$parent.$parent.oxygenUIElement.append(compiledElement);

                scope.$apply();
            })
        }
    }
});