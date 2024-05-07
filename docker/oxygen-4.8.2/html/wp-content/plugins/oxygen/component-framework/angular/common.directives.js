
var CTCommonDirectives = angular.module('CTCommonDirectives', []);

CTCommonDirectives.factory('ctScopeService', function() {
    var mem = {};
    return {
        store: function(key, val) {
            mem[key] = val;
        },
        get: function(key) {
            return mem[key];
        }
    }
});

CTCommonDirectives.factory('ctOxyCache', function() {
    var mem = {};
    return {
        store: function(key, val) {
            mem[key] = val;
        },
        get: function(key) {
            return mem[key];
        }
    }
});

var newTreeControlState =  {
    acceptsDrop: [
        "ct_columns",
        "ct_new_columns",
        "ct_column", 
        "ct_section", 
        "ct_ul", 
        "ct_link",
        'ct_inner_content',
        "ct_div_block",
        "oxy_dynamic_list",
        "ct_nestable_shortcode",
        "oxy_icon_box_button_area",
        "oxy_tabs",
        "oxy_tabs_contents",
        "oxy_tab",
        "oxy_tab_content",
        "oxy_toggle",
        "oxy_header",
        "ct_if_wrap",
        "ct_else_wrap",
        "oxy_header",
        "oxy_header_left",
        "oxy_header_center",
        "oxy_header_right",
        "ct_slider",
        "ct_slide",
        "ct_modal"],
    currentShowMore: null
} 

CTCommonDirectives.directive("newTreeControl", function() {
    return {
        restrict: 'A',
        scope: {
            newTreeControl: '='
        },
        link: function(scope, element, attrs) {

            if(newTreeControlState.acceptsDrop.indexOf(scope.newTreeControl.item.name) > -1) {
                setTimeout(function() {
                    element.addClass('ct_accepts_drop')
                }, 200);
            }
            else {
                scope.$parent.iframeScope.waitComponentsTemplates(function() {
                    if ( scope.$parent.iframeScope.isAPIComponent(scope.newTreeControl.item.name) ) {
                        if ( scope.$parent.iframeScope.componentsTemplates[scope.newTreeControl.item.name]['nestable'] ) {
                            setTimeout(function() {
                                element.addClass('ct_accepts_drop')
                            }, 200);
                        }
                    }
                })
            }

            element.attr('data-component-name', scope.newTreeControl.item.name)

            scope.$parent.$on('treeCollapse', function() {
                scope.newTreeControl.collapsed = true;
            });
            scope.$parent.$on('treeExpand', function() {
                scope.newTreeControl.collapsed = false;
            });
            scope.$parent.$on('nodeExpand', function(e, node) {
                
                if(element.get(0) === node) {
                    scope.newTreeControl.collapsed = false;     
                }
            });
            
            scope.$parent.$on('nodeShowMore', function(e, id) {

                if(id === null) {
                    if(scope.newTreeControl && scope.newTreeControl.editable)  {
                        scope.newTreeControl.editable = false;
                    }

                    if(scope.newTreeControl && scope.newTreeControl.showCategorize) {
                        scope.newTreeControl.showCategorize = false;
                    }
                }

                // console.log('received event', id);
                var thisId = parseInt(element.attr('ng-attr-tree-id'));
                if(  thisId === parseInt(id) ) {
                    // console.log('turning on ', id);
                    scope.newTreeControl.showMoreOptions = !scope.newTreeControl.showMoreOptions;
                    
                    if(scope.newTreeControl.showMoreOptions) {
                        newTreeControlState.currentShowMore = id;
                        setTimeout(function() {
                            newTreeControlState.currentShowMore = null
                        }, 500)
                    }

                } else if( scope.newTreeControl.showMoreOptions === true && thisId !== parseInt(newTreeControlState.currentShowMore) ) {
                    // console.log('turning off ', element.attr('ng-attr-tree-id'))
                    scope.newTreeControl.showMoreOptions = false;
                }
            })
        }
    }
})

CTCommonDirectives.directive("ctiriscolorpicker", function() {
    return {
        restrict: "A",
        require: "ngModel",
        scope: {
            ctiriscallback: '=',
            gradientindex: '='
        },
        
        link: function(scope, element, attrs, ngModel) {
            var debounceChange = false;
            setTimeout(function(){
                element.alphaColorPicker({
                    color: scope.$parent.iframeScope.getGlobalColorValue(ngModel.$modelValue),
                    change: function(ui) {                        
                        if(element.val().length == ui.color.toString().length || element.val().length === 0) {
                            if(!debounceChange) {
                                debounceChange = setTimeout(function() {
                                    if (scope.$parent.globalColorToEdit.id!==undefined) {
                                        // update global color
                                        scope.$parent.updateGlobalColorValue(element.ctColorPicker('color'));
                                        scope.$parent.globalColorChange();
                                    } else {
                                        // update regular component setting
                                        ngModel.$setViewValue(scope.$parent.iframeScope.getGlobalColorValue(element.ctColorPicker('color')));
                                    }
                                    clearTimeout(debounceChange);
                                    debounceChange = false;
                                }, 100);
                            }
                        }
                        if(scope.ctiriscallback) {
                            scope.ctiriscallback();
                        }
                    }
                });

                var modelString = attrs.ngModel;

                if(typeof(scope.gradientindex) !== 'undefined') {
                    modelString = modelString.replace('$index', scope.gradientindex);
                }

                scope.$parent.$watch(modelString, function( newVal ) {

                    var niceName = scope.$parent.iframeScope.getGlobalColorNiceName(newVal),
                        colorPicker = element.closest('.oxygen-color-picker');

                    jQuery('.oxy-global-color-label', colorPicker).remove();

                    if (niceName) {
                        colorPicker.removeClass('oxy-not-global-color-value').children('input').prop( "disabled", true )
                            .after("<span class='oxy-global-color-label' title='"+niceName+"'>"+niceName+"<span class='oxy-global-color-label-remove'>×</span></span>")
                        
                        // update alpha value
                        // use acp_get_alpha_value_from_color() function code from alpha-color-picker.js
                        var alphaVal;
                        // Remove all spaces from the passed in value to help our RGBa regex.
                        var value = scope.$parent.iframeScope.getGlobalColorValue(newVal).replace( / /g, '' );
                        if ( value.match( /rgba\(\d+\,\d+\,\d+\,([^\)]+)\)/ ) ) {
                            alphaVal = parseFloat( value.match( /rgba\(\d+\,\d+\,\d+\,([^\)]+)\)/ )[1] ).toFixed(2) * 100;
                            alphaVal = parseInt( alphaVal );
                        } else {
                            alphaVal = 100;
                        }

                        var alphaSlider = element.closest('.wp-picker-container').find('.alpha-slider');

                        // use acp_update_alpha_value_on_alpha_slider() function code from alpha-color-picker.js
                        alphaSlider.slider( 'value', alphaVal );
                        alphaSlider.find( '.ui-slider-handle' ).text( alphaVal.toString() );    
                    }
                    else {
                        colorPicker.addClass('oxy-not-global-color-value').children('input').prop( "disabled", false );
                        scope.$parent.$parent.activeGlobalColor = {};
                    }

                    colorPicker.addClass('oxy-not-empty-color-value');
                    if ((!newVal || newVal === "") && !ngModel.$modelValue ) {
                        colorPicker.removeClass('oxy-not-empty-color-value');
                        // unset background color
                        element.closest('.wp-picker-container').find('.wp-color-result').css("background-color","");
                        return;
                    }

                    element.ctColorPicker('color', scope.$parent.iframeScope.getGlobalColorValue(newVal));
                });
                
                scope.$apply();
                
            }, 0);

        }
    }
});


//var hardLimit = 0;
CTCommonDirectives.directive("ctdynamiclist", function($compile, ctScopeService) {
    return {
        restrict: "A",
        replace: true,
        scope: {
            dynamicListOptions: "=",
            dynamicActions: "=",
        },
        link: function(scope, element, attrs) {

            var id = parseInt(element.attr('ng-attr-component-id'));

            var spanIDs = {};

            function setFocus(event) {
                var activeOriginal = angular.element(event.target).closest('.oxy_repeater_original');

                if(activeOriginal.closest('#'+element.attr('id')).length < 1) {
                    activeOriginal = false;
                }

                if(!activeOriginal || activeOriginal.length < 1) {
                    // scroll to the original component
                    scope.$parent.$parent.parentScope.scrollToComponent(element.children().attr('id'), 100);
                }
            }

            element.on('click', setFocus);

            angular.extend(scope.dynamicActions['actions'][id], {
                action: function(instanceId, componentID, doit, virtualTreeItem, data) {

                    instanceId = parseInt(instanceId);

                    if(typeof(doit) === 'undefined') {
                        return;
                    }
                    
                    scope.$parent.$parent.waitOxygenTree(function () {
                        startProcessing(instanceId, componentID, virtualTreeItem, data);
                    }); 
                }

            });


            function loadData(item, parent, data) {

                if(typeof(item.source_id) === 'undefined') {
                    item.source_id = item.id;
                }

                if(typeof(spanIDs[item.source_id]) !== 'undefined') { // if an id was already determined for a nested span
                    item.id = spanIDs[item.source_id];
                }
                else {
                    item.id = scope.$parent.$parent.recycleIDs.length > 0 ? scope.$parent.$parent.recycleIDs.shift() : scope.$parent.$parent.component.id++;
                }

                item.options['ct_id'] = item.id;

                // clean up the options
                scope.$parent.$parent.component.options[item.id] = {id: {}, model:{}, original:{}}; 

                if(typeof(parent) !== 'undefined') {
                    item['options']['ct_parent'] = parent;
                }

                var dataPiece = data.find(function(dp) {
                    return dp['ct_id'] === item.source_id;
                })

                if(dataPiece && dataPiece['ct_content']) {
                    item['options']['ct_content'] = dataPiece['ct_content'];

                    // if it contains a reference to a span

                    item['options']['ct_content'] = item['options']['ct_content'].replace(/\<span id\=\"ct-placeholder-([^\"]*)\"\>\<\/span\>/ig, function(match, id) {
                        var newID = scope.$parent.$parent.recycleIDs.length > 0 ? scope.$parent.$parent.recycleIDs.shift() : scope.$parent.$parent.component.id++;

                        spanIDs[id] = newID;

                        return '<span id="ct-placeholder-'+newID+'"></span>'
                    });
                }

                if(dataPiece && dataPiece['renderedHTML']) {
                    item['options']['renderedHTML'] = dataPiece['renderedHTML'];
                }

                if(dataPiece && dataPiece['original']) {
                    item['options']['original'] = dataPiece['original'];
                }

                if ( item.name == "oxy_dynamic_list" ) {    
                    scope.$parent.dynamicListActions.actions[item.id] = {};
                }

                // if(item.name === 'oxy_dynamic_list') {
                //     // remove the children, we dont want the list to reproduce here
                //     delete(item['children']);
                // }
                
                scope.$parent.$parent.dynamicListTrees['forcleanup'][id].push(item.id);
                
                if(item.children && item.children.length > 0) {
                    item.children.forEach(function(child) {
                        loadData(child, item.id, data);
                    })
                }
            }

            function collectModels(children) {
                
                var models = [];

                children.forEach(function(child) {
                    
                    var model = angular.element.extend(true, {}, child['options']);

                    if(child['name'] === 'oxy_dynamic_list') {
                        model['children'] = collectModels(child.children)
                    }

                    model['name'] = child['name'];

                    var componentTemplate = scope.$parent.$parent.componentsTemplates[model['name']];

                    if( componentTemplate) {
                        if(componentTemplate.phpCallback) {
                            model['renderedHTML'] = true;
                            model['component'] = child;
                        }
                    }

                    models.push(model);

                    if(child['name'] !== 'oxy_dynamic_list' && child.children) {
                        models = models.concat(collectModels(child.children));
                    }
                });

                return models;
            }

            function generateList(results, holder, pagination) {
                
                if(scope.$parent.$parent.dynamicListTrees['forcleanup'][id] && scope.$parent.$parent.dynamicListTrees['forcleanup'][id].length > 0) {
                    cleanOldData(scope.$parent.$parent.dynamicListTrees['forcleanup'][id]);
                }

                scope.$parent.$parent.dynamicListTrees['forcleanup'][id] = [];
                scope.$parent.$parent.dynamicListTrees['trees'][id] = [];

                results.forEach(function(result, index) {
                    var clone = JSON.parse(JSON.stringify(holder));
                    clone.repeaterListIndex = index;
                    loadData(clone, null, result);
                    scope.$parent.$parent.dynamicListTrees['trees'][id].push(clone);
                })

                element.attr('data-for-id', id);

                var original = element.children('.oxy_repeater_original');
                
                if(original.length < 1) {
                    original = element.children().first();
                    original.addClass('oxy_repeater_original');
                }

                original.siblings().remove();

                scope.$parent.buildComponentsFromTree( scope.$parent.$parent.dynamicListTrees['trees'][id], null, false, element); 

                original.insertBefore(element.children().first());

                //if any of the children contain a repeater, run it as well
                scope.$parent.$parent.dynamicListTrees['trees'][id].forEach(function(item, index) {
                    runChildLists(item, results, index);
                });

                scope.$parent.$parent.adjustResizeBox();

                scope.$parent.$parent.$emit('oxy-dynamic-list-'+id);

                // all the children inside this repeater, if any is an API element and has_js, then rebuild its dom so that the required js loads and executes.
                if(holder && holder.children) {
                    findAndLoadJS(holder.children);
                }

                if(pagination) {
                    element.append(angular.element('<div class="oxy-repeater-pages-wrap" style="opacity:1">').append(angular.element('<div class="oxy-repeater-pages">').append(pagination)));
                }
            }


            function findAndLoadJS(items) {
                items.forEach(function(item) {
                    if(scope.$parent.componentsTemplates[item.name] && scope.$parent.componentsTemplates[item.name].has_js) {
                        scope.$parent.rebuildDOM(item.id);
                    } else if(item['children']) {
                        findAndLoadJS[item['children']];
                    }
                });
                scope.$parent.componentsTemplates['']
            }


            function runChildLists(item, results, index) {

                if(item.children) {
                    item.children.forEach(function(child) {
                        if(child.name == "oxy_dynamic_list") {
                            
                            var data = results[index].find(function(thing) {
                                return thing.ct_id === child.source_id;
                            });
                            
                            scope.$parent.$parent.dynamicListAction(child.id, 1, true, child, data['children']);
                            
                            
                        } else {
                            runChildLists(child, results, index);
                        }
                    });
                }

            }

            function cleanOldData(children) {
                
                    children.forEach(function(item) {
                        // delete idStyles
                        delete(scope.$parent.$parent.cache.idStyles[item]);

                        // put ID in the pool to be used for another instance
                        scope.$parent.$parent.recycleIDs.push(item);

                    });

            }

            function startProcessing(instanceId, componentID, virtualTreeItem, data) {

                var id = parseInt(element.attr('ng-attr-component-id'));

                // return if it is single mode
                if( !scope.$parent.$parent ) return;
                if(scope.$parent.$parent.component.options[id]['model']['listrendertype']) {
                    var original = element.children().first();
                    original.addClass('oxy_repeater_original');
                    scope.$parent.$parent.adjustResizeBox();
                    scope.$parent.$parent.$emit('oxy-dynamic-list-'+id);
                    return;
                }
                
                if(instanceId !== id) {
                    return;    
                }
                var listComponent = null;

                if(virtualTreeItem) {
                    listComponent = virtualTreeItem;
                }
                else {
                    listComponent = scope.$parent.findComponentItem(scope.$parent.componentsTree.children, id, scope.$parent.getComponentItem);
                }

                if(!listComponent || !listComponent.children || listComponent.children.length < 1) {
                    return;
                }

                var holder = listComponent.children[0];
                

                
                if(virtualTreeItem) {
                    generateList(data, holder);
                }
                else {
                    // recursively collect models of all the items under a tree
                    var models = collectModels(listComponent.children);
                    // send the models along with the query data of the list component to receive an array of resolved models.

                    // if is an acf query, and has a ancestor repeater, then send that ancestor's query data

                    var parentRepeaterID = false;

                    // its an acf repeater, use parent repeater's query instead or default page query
                    var repeater = element;
                    var repeaterFields = [];
                    var count = 0;
                    while(repeater && repeater.length > 0) {

                        var repeaterID = parseInt(repeater.attr('ng-attr-component-id'));
                        if(scope.$parent.$parent.component.options[repeaterID]['original']['use_acf_repeater']) {
                            repeater = repeater.parent().closest('.oxy-dynamic-list');
                            if(count > 0) { // so that the current repeater, if ACF, not be included
                                repeaterFields.push(scope.$parent.$parent.component.options[repeaterID]['original']['acf_repeater']);
                            }
                        }
                        else if(scope.$parent.$parent.component.options[repeaterID]['original']['use_metabox_clonable_group']) {
                            repeater = repeater.parent().closest('.oxy-dynamic-list');
                            if(count > 0) { // so that the current repeater, if MetaBox, not be included
                                repeaterFields.push(scope.$parent.$parent.component.options[repeaterID]['original']['metabox_group']);
                            }
                        }
                        else {
                            parentRepeaterID = parseInt(repeater.attr('ng-attr-component-id'));
                            repeater = false;
                        }
                        count++;
                    }


                    scope.$parent.getDynamicDataFromQuery(id, models, generateList, holder, parentRepeaterID, repeaterFields);
                }
                //console.log(container.html());
                
            }

            // queue for execution once the DOM is built out of the tree            
            scope.$parent.$parent.dynamicListTrees['runMainOnLoad'].push(id);


        }
    }
})



CTCommonDirectives.directive("dyncontenteditable", function($compile, $timeout,$interval, ctScopeService) {

    return {
        restrict: "A",
        link: function(scope, element, attrs) {
            element.bind("dblclick", function(e) {

                e.stopPropagation();
                
                //scope = scope.$parent;
                // replace with the original element, so that it can be edited
                var itemID = element.attr('ng-attr-component-id');

                var component = scope.findComponentItem(scope.componentsTree.children, itemID, scope.getComponentItem);

                if(component) {

                    var insertedElement;
                    // if it is a span inside an editable element, it needs to be inserted inline of the surrounding text
                    if(component.name=='ct_span') {
                        
                        // add this attribute to all other clones so that those are not considered for operations
                        angular.element('[ng-attr-component-id='+itemID+']').attr('disabled', 'disabled');
                        element.removeAttr('disabled'); // this also got disabled in the above step

                        scope.buildComponentsFromTree([component], null, false, element);

                        insertedElement = element.children();

                        element.replaceWith(insertedElement);

                    } else {
                        // its just a dom element, it requires a different method for being inserted at the exact index

                        // add this attribute to the siblings so that the cloned elements inside the list are not considered for operartion
                        angular.element('[ng-attr-component-id='+itemID+']').attr('disabled', 'disabled');
                        element.removeAttr('disabled');

                        var parent = element.parent();
                        var index = element.index();
                        
                        var container = angular.element('<div>').css('display', 'none');
                        parent.append(container);

                        scope.buildComponentsFromTree([component], null, false, container);
                        
                        insertedElement = container.children();

                        // var offset = element.offset();

                        // insertedElement.css({'position': 'absolute', 'z-index': '999999'});

                        // insertedElement.offset(offset);

                        insertedElement.insertBefore(element);
                        
                        //element.css('opacity', 0);
                        element.css('display', 'none');
                        
                        element.attr('disabled', 'disabled');
                        
                        container.remove();                        
                        
                    }

                    setTimeout(function() {
                        insertedElement.trigger('dblclick');
                    }, 100);
                }
            });

        }
    }
})

CTCommonDirectives.directive("ctrendernestableshortcode", function($http) {
    return {
        restrict: "A",
        link: function(scope, element, attrs) {
            
            var id = parseInt(element.attr('ng-attr-component-id'));

            var callback = function(shortcode, contents) {
                
                if(typeof(contents) !== 'undefined') {
                    contents = contents.split('_#wrapped_content_replacer#_');
                    scope.$parent.component.options[id].model['wrapping_start'] = contents[0];
                    scope.$parent.component.options[id].model['wrapping_end'] = contents[1];
                } else {
                    scope.$parent.component.options[id].model['wrapping_start'] = '';
                    scope.$parent.component.options[id].model['wrapping_end'] = '';
                }

                scope.$parent.setOption(id, 'ct_nestable_shortcode', 'wrapping_start');
                scope.$parent.setOption(id, 'ct_nestable_shortcode', 'wrapping_end');
                
                scope.$parent.rebuildDOM(id);

            }
            
            var renderContent = function() {
                setTimeout(function() {
                    if(!scope.$parent) {
                        return;
                    }

                    var shortcode = scope.$parent.component.options[id].id['wrapping_shortcode'];

                    if(!shortcode) {
                        return;
                    }

                    var matches = [];

                    shortcode.replace(/\[([^\s\]]{1,})[^\]]*\]/ig, function(match, match2) {
                        matches.push(match);
                        matches.push(match2);
                        return '';
                    });

                    var shortcode_data = {
                        original: {
                            full_shortcode: matches[0]+"_#wrapped_content_replacer#_[/"+matches[1]+']',
                        }
                    }

                    scope.renderShortcode(id, 'ct_shortcode', callback, shortcode_data);
                }, 0);
            }

            var debounceChange = false;

            scope.$watch(element.attr('ct-nestable-shortcode-model'), function( newVal, oldVal ) {
                
                if(debounceChange === false && oldVal !== newVal) {
                    debounceChange = setTimeout(function() {
                        renderContent();   
                        debounceChange = false; 
                    }, 500)
                }                
            });

            //renderContent();
            
        }
    }
})

CTCommonDirectives.directive("ctevalconditions", function() {
    return {
        restrict: "A",
        link: function(scope, element, attrs) {
            
            setTimeout(function() {
                var id = parseInt(element.attr('ng-attr-component-id'));
                scope.parentScope.evalGlobalConditions(id);
            }, 0);
        }
    }
})

CTCommonDirectives.directive("ctrenderoxyshortcode", function($http, ctOxyCache) {
    return {
        restrict: "A",
        require: "ngModel",
        link: function(scope, element, attrs, ngModel) {
            
            var callback = function(shortcode, contents, scripts) {
                element.html(contents);

                var id = parseInt(element.attr('ng-attr-component-id')),
                    component = scope.getComponentById(id);

                if ( shortcode.indexOf("metabox_field_type='map'")>0 &&
                    "undefined" !== typeof(scripts)) {
                        
                    // remove any existing links and scripts for the same shortcode component id
                    var body = component.closest('body');
                    body.find('link[data-forId="'+id+'"], script[data-forId="'+id+'"]').remove();

                    appendScripts(scripts)

                    function appendScripts(scripts) {
                        try {
                            if (scripts.length>1) {
                                var firstScript = scripts[0];
                                body.append(angular.element(firstScript).attr('data-forId', id))
                                .ready(function() {
                                    setTimeout(function() {
                                        appendScripts(scripts.slice(1));
                                    }, 300);
                                });
                            }
                            else {
                                body.append(angular.element(scripts[0]).attr('data-forId', id));
                            }
                        } catch (error) {
                            console.log('JS error happened:', error);
                        }
                    }
                }
            }

            setTimeout(function() {
                var id = parseInt(element.attr('ng-attr-component-id'));
                var shortcode = scope.$parent.getOption('ct_content', id);
                var shortcode_data = {
                    original: {
                        full_shortcode: shortcode
                    }
                }
                if (shortcode.indexOf("metabox_field_type='map'")>0) {
                    var width = scope.$parent.getOption('width', id);
                    if (!width) {
                        scope.$parent.setOptionModel('width', "100", id);
                        scope.$parent.setOptionModel('width-unit', "%", id);
                    }
                }

                // add specific class only for content dynamic data
                if (shortcode.indexOf("data='content'")>0) {
                    
                    // hack needed to properly update components class in components tree
                    scope.$parent.currentClass = "oxy-stock-content-styles";
                    
                    scope.addClassToComponent(id,'oxy-stock-content-styles',false)
                    
                    // hack needed to properly update components class in components tree
                    scope.$parent.currentClass = false;
                }

                scope.renderShortcode(id, 'ct_shortcode', callback, shortcode_data);

            }, 0);
        }
    }
})

/**
 * Make HTML5 "contenteditable" support ng-module
 * To enforce plain text mode, use attr data-plaintext="true"
 */

CTCommonDirectives.directive("contenteditable", function($timeout,$interval, ctScopeService) {

    return {
        restrict: "A",
        require: "ngModel",
        link: function(scope, element, attrs, ngModel) {

            element.unbind("paste input");

            function read() {
                ngModel.$setViewValue(element.html());
            }

            function getCaretPosition() {
                
                if(window.getSelection) {
                    selection = window.getSelection();
                    if(selection.rangeCount) {
                        range = selection.getRangeAt(0);
                        return(element.text().length-range.endOffset);
                    }
                }
            }

            function setCaretPosition(caretOffsetRight) {
                var range, selection;

                if(document.createRange) {
                    range = document.createRange();
                    if(element.get(0) && element.get(0).childNodes[0]) {
                        var offset = element.text().length;
                        
                        range.setStart(element.get(0), 0);
                        
                        if(caretOffsetRight > 0 && caretOffsetRight <= offset) {
                            offset -= caretOffsetRight;
                        }
                        range.setEnd(element.get(0).childNodes[0], offset);
                        range.collapse(false);
                        selection = window.getSelection();
                        selection.removeAllRanges();
                        selection.addRange(range);
                        
                    }
                    
                }
                else if(document.selection) {
                    range = document.body.createTextRange();
                    if(element.get(0) && element.get(0).childNodes[0]) {
                        var offset = element.text().length;
                            
                        range.setStart(element.get(0), 0);
                        
                        if(caretOffsetRight > 0 && caretOffsetRight <= offset) {
                            offset -= caretOffsetRight;
                        }
                        range.setEnd(element.get(0).childNodes[0], offset);
                        range.collapse(false);
                        range.select();
                    }
                }
            }

            ngModel.$render = function() {
                if (typeof(attrs['disabledynamic']) !== 'undefined' && attrs['disabledynamic'] === "true") {
                    
                    element.removeClass('oxygen-disabled')

                    var oxy = ngModel.$viewValue;

                    ngModel.$viewValue.replace(/\<span id\=\"ct-placeholder-([^\"]*)\"\>\<\/span\>/ig, function(match, id) {
                        oxy = $scope.iframeScope.component.options[parseInt(id)]['model']['ct_content'];
                    });

                    var containsOxy = oxy.match(/\[oxygen[^\]]*\]/ig);
                    if(containsOxy) {
                        element.addClass('oxygen-disabled')
                    }
                }

                element.html(ngModel.$viewValue || "");

            };

            // save element content
            element.bind("input", function(e, paste) {

                scope.$apply(read);

                // if it is plaintext mode, replace any html formatting, only in paste mode
                if(paste && typeof(attrs['plaintext']) !== 'undefined' && attrs['plaintext'] === "true") {
                    
                    if(jQuery('<span>').html(element.html()).text().trim() !== element.html().trim().replace('&nbsp;', '')) {
                       // var caretPosition = getCaretPosition();
                       // element.html(jQuery('<span>').html(element.html()).text());
                       // setCaretPosition(caretPosition);
                        element.html(element.text());
                    }

                    ngModel.$setViewValue(element.text());
                }

                // if default text is provided and current text is blank. populate with defaulttext
                if(element.html().trim() === '' && typeof(attrs['defaulttext']) !== 'undefined' && attrs['defaulttext'].trim() !== '') {
                    element.text(attrs['defaulttext']);
                }

                // timeout for angular
                var timeout = $timeout(function() {
                    var dascope = scope,
                        optionName = attrs['optionname'] || "ct_content";

                    if(scope.iframeScope)
                        dascope = scope.iframeScope; 
                    dascope.setOption(dascope.component.active.id, dascope.component.active.name, optionName);
                    $timeout.cancel(timeout);
                }, 20, false);
            })

            // trick to update content after paste event performed
            element.bind("paste", function() {
                setTimeout(function() {element.trigger("input", 'paste');}, 0);
            });

            // if data-plaintext is NOT set to "true"
            if(typeof(attrs['plaintext']) === 'undefined' || attrs['plaintext'] !== "true") {

                // Prevent link wrapper click while editting content
                element.bind("click", function(e) {
                    e.preventDefault();
                });

                // enable content editing on double click
                element.bind("dblclick", function(e) {

                    e.stopPropagation();

                    var parentScope = ctScopeService.get('scope').parentScope,
                        optionName = attrs['optionname'] || "ct_content";

                    // before enabling edit content,
                    var content = scope.getOption(optionName);
                    scope.contentEditableData.original = content;
                    scope.dynamicSpanCycleIDs = [];
                    content = content.replace(/\<span id\=\"ct-placeholder-([^\"]*)\"\>\<\/span\>/ig, function(match, id) {
                        
                        var oxy = scope.component.options[parseInt(id)]['model']['ct_content'];

                        var containsOxy = oxy.match(/\[oxygen[^\]]*\]/ig);

                        if(containsOxy) {
                            scope.$parent.dynamicSpanCycleIDs.push(parseInt(id));
                            scope.removeComponentById(parseInt(id), 'span', scope.component.active.id);
                            return oxy;
                        }
                        else {
                            
                            return match;
                        }

                    });

                    scope.setOptionModel(optionName, content, scope.component.active.id, scope.component.active.name)
                    scope.contentEditableData.beingEdited = content;
                    parentScope.enableContentEdit(element);
                    scope.$apply();
                });

                // format as <p> on enter/return press
                if ( element[0].attributes['ng-attr-paragraph'] ) {
                    element.bind('keypress', function(e){
                        if ( e.keyCode == 13 ) {
                            document.execCommand('formatBlock', false, 'p');
                        }
                    });
                }
                else {
                    // format as <br/>
                    element.bind('keypress', function(e){
                        if ( e.keyCode == 13 ) { 
                            document.execCommand('insertHTML', false, '<br><br>');
                            return false;
                        }
                    });
                }
            } 
            // else if it is plaintext mode
            else {
                // we do not need line breaks
                element.bind('keypress', function(e){
                    
                    if ( e.keyCode == 13 ) { 
                        element.blur();
                        return false;
                    }
                });
            }
            
            // if ngBlur is provided
            if(typeof(attrs['ngBlur']) !== 'undefined' || attrs['ngBlur'] !== "") {
                element.bind('blur', function() {
                    var timeout = $timeout(function() {
                        scope.$apply(attrs.ngBlur);
                        $timeout.cancel(timeout);
                    }, 0, false);
                })
            }

        }
    };
});

/**
 * Helps an input text field gain focus based on a condition
 * 
 * @since 0.3.3
 * @author Gagan Goraya
 *
 * usage: <input type="text" focus-me="booleanValue" />
 */
 
CTCommonDirectives.directive('focusMe', function($timeout) {
  return {
    scope: { trigger: '=focusMe' },
    link: function(scope, element) {
      scope.$watch('trigger', function(value) {
        if(value === true) { 
          $timeout(function() {
            element[0].focus();
            scope.trigger = false;
          });
        }
      });
    }
  };
});


/**
 * https://docs.angularjs.org/error/ngModel/numfmt
 */

CTCommonDirectives.directive('oxyRangeFix', function() {
    return {
        priority: 0,
        require: 'ngModel',
        link: {
            pre: function(scope, element, attr, ctrl) {
                // remove all extra stuff added by AngularJS
                ctrl.$validators = [];
                ctrl.$formatters = [];
                ctrl.$parsers = [];
                // this function change input[type=range] value to browser's default in AngularJS 1.8
                // unset it to empty function
                ctrl.$render = function(){};
            },
        }
    };
});
