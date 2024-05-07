
CTFrontendBuilder.directive("ngBuilderWrap", function ($parentScope, $compile, $timeout, $interval) {
    
    return {
        restrict: 'A',
        link: function(scope, element) {

            /**
             * Some components shouldn't be a child of itslef or other components
             * i.e. section can't be a child of a section or any section children
             */
            scope.insertBeforeComponent = function(name, insertData) {

                var parent = insertData.activeComponent,
                    parentId;

                // search for component in parents
                while ( !parent.is(name) && !parent.hasClass('ct-builder') ) {
                    parent = parent.parent();
                }

                parentId = parent[0].getAttribute('ng-attr-component-id');

                // found component in parents
                if (parentId != 0) {

                    insertData.index = parent.index() + 1;

                    // go level up from component
                    parent = jQuery(parent).parent().closest("[is-nestable]"); 
                    insertData.idToInsert = parent[0].getAttribute('ng-attr-component-id');

                    // activate new component
                    scope.activateComponent(insertData.idToInsert);
                    insertData.activeComponent = scope.getActiveComponent();
                }
            };

            // Example tree structure, for testing only
            scope.tree = 
                    [   // 0
                        {
                            'oxy-timeline':[
                                // 0
                                {
                                    'oxy-timeline-entry':[
                                        // 0
                                        'ct_headline', 
                                        // 1
                                        'ct_text_block', 
                                        // 2
                                        'ct_image'
                                    ]
                                },
                                // 1
                                {
                                    'oxy-timeline-entry':[
                                        // 0
                                        //'ct_text_block', 
                                        // 1
                                        {
                                            'ct_div_block':[
                                                // 0
                                                'ct_headline'
                                            ]
                                        }, 
                                        // 2
                                        'ct_text_block', 
                                        // 3
                                        'ct_image'
                                    ]
                                },
                                // 2
                                {
                                    'oxy-timeline-entry':[
                                        'ct_headline', 'ct_text_block', 'ct_image'
                                    ]
                                }
                            ]
                        },
                        // 1
                        'ct_headline'
                    ]
                
            /**
             * Recusrive function to add a pre-defind components tree
             *
             * @since 3.0
             * @author Ilya K.
             */

            scope.addComponentsTree = function(tree, parentID) {

                //console.log(tree);
                
                //for testing only: if (tree===undefined) tree = scope.tree;

                for (key in tree) { 
                    if (tree.hasOwnProperty(key)){
                        var subTree = tree[key];

                        // add sub tree
                        if (typeof(subTree)==='object') {
                            for (subKey in subTree){ // names
                                if (subTree.hasOwnProperty(subKey)){

                                    var id = scope.addComponent(subKey);
                                    //console.log(subKey + ' added', id);

                                    // add sub tree
                                    if (typeof(subTree[subKey])==='object') {
                                        // recursion
                                        scope.addComponentsTree(subTree[subKey], id);
                                    }
                                    
                                    // add leaf
                                    if (typeof(subTree[subKey])==='string') {
                                        scope.addComponent(subTree[subKey],'',true);
                                    }
                                }
                            }

                            // activate parent
                            if (parentID!==undefined) {
                                scope.activateComponent(parentID);
                                //console.log('activate',parentID);
                            }
                        }

                        // add leaf
                        if (typeof(subTree)==='string') {
                            scope.addComponent(subTree,'',true);    
                        }
                    }
                }

                // activate parent
                if (parentID!==undefined) {
                    scope.activateComponent(parentID);
                    //console.log('activate',parentID);
                }
                    
            }



            scope.addComponents = function(first, second) {
                
                scope.addComponent(first);

                // Fix for inserting second after first and not inside of it.
                // Need a timeout to update a controller scope
                if ( first == "ct_columns") {
                   
                    var timeout = $timeout(function() {
                        // add first column and keep parent active
                        scope.addComponent(second, false, true);

                        $timeout.cancel(timeout);
                    }, 0, false);

                    var timeout2 = $timeout(function() {
                        // add second column
                        scope.addColumn(scope.component.active.id);
                        scope.columns[scope.component.active.id] = 2;
                        
                        $timeout.cancel(timeout2);
                    }, 500, false);
                }
                else {
                    var timeout = $timeout(function() {
                
                        // add second component
                        scope.waitOxygenTree(function(){
                            scope.addComponent(second);
                        })

                        $timeout.cancel(timeout);
                    }, 0, false);
                }
            };

            scope.addTab = function() {

                // save active component ID before activate another
                var activeComponentID   = scope.component.active.id,
                    activeComponentName = scope.component.active.name;

                if ( scope.component.active.name == "oxy_tabs" ) {
                    var wrapper             = scope.getOption("tabs_contents_wrapper"),
                        id                  = jQuery("#"+wrapper).attr('ng-attr-component-id'),
                        addedComponentID    = scope.addComponent("oxy_tab", false, true),
                        activeClass         = scope.getOption('active_tab_class', activeComponentID);
                    
                    scope.addClassToComponentSafe(addedComponentID, activeClass.replace('-active',''));
                    scope.addClassToComponentSafe(addedComponentID, activeClass);
                    scope.activeSelectors[addedComponentID] = activeClass.replace('-active','');

                    var timeoutTab = $timeout(function() {
                        
                        scope.activateComponent(addedComponentID, "oxy_tab");
                        var textBlock = scope.addComponent("ct_text_block", false, true);
                        scope.setOptionModel('ct_content', "Another Tab", textBlock, "ct_text_block");

                        $timeout.cancel(timeoutTab);
                    }, 0, false);
                    
                    
                    scope.activateComponent(id, "oxy_tabs_contents");
                    addedComponentIDTwo = scope.addComponent("oxy_tab_content", false, true);
                    scope.addClassToComponentSafe(addedComponentIDTwo, activeClass.replace('tabs-','tabs-contents-').replace('-active',''));
                    scope.activeSelectors[addedComponentIDTwo] = activeClass.replace('tabs-','tabs-contents-').replace('-active','');
                    
                    var timeoutTabContent = $timeout(function() {
                        
                        scope.activateComponent(addedComponentIDTwo, "oxy_tab_content");
                        var textBlockTwo = scope.addComponent("ct_text_block", false, true);
                        scope.setOptionModel('ct_content', "Another Tab Contents", textBlockTwo, "ct_text_block");

                        var timeoutInner = $timeout(function() {

                            jQuery(scope.getComponentById(addedComponentID)).trigger('click');
                            scope.activateComponent(activeComponentID, activeComponentName);
                            scope.adjustResizeBox();

                            $timeout.cancel(timeoutInner);
                        }, 100, false);

                        $timeout.cancel(timeoutTabContent);
                    }, 0, false);
                }
                else if ( scope.component.active.name == "oxy_tabs_contents" ) {
                    var wrapper             = scope.getOption("tabs_wrapper"),
                        id                  = jQuery("#"+wrapper).attr('ng-attr-component-id'),
                        addedComponentID    = scope.addComponent("oxy_tab_content", false, true),
                        activeClass         = scope.getOption('active_tab_class', id);

                    scope.addClassToComponentSafe(addedComponentID, activeClass.replace('tabs-','tabs-contents-').replace('-active',''));
                    scope.activeSelectors[addedComponentID] = activeClass.replace('tabs-','tabs-contents-').replace('-active','');

                    var timeoutTabContent = $timeout(function() {
                        
                        scope.activateComponent(addedComponentID, "oxy_tab_content");
                        var textBlock = scope.addComponent("ct_text_block", false, true);
                        scope.setOptionModel('ct_content', "Another Tab Contents", textBlock, "ct_text_block");

                        $timeout.cancel(timeoutTabContent);
                    }, 0, false);
                    
                    scope.activateComponent(id, "oxy_tabs");
                    addedComponentIDTwo = scope.addComponent("oxy_tab", false, true);

                    scope.addClassToComponentSafe(addedComponentIDTwo, activeClass.replace('-active',''));
                    scope.addClassToComponentSafe(addedComponentIDTwo, activeClass);
                    scope.activeSelectors[addedComponentIDTwo] = activeClass.replace('-active','');
                    
                    var timeoutTab = $timeout(function() {
                        
                        scope.activateComponent(addedComponentIDTwo, "oxy_tab");
                        var textBlockTwo = scope.addComponent("ct_text_block", false, true);
                        scope.setOptionModel('ct_content', "Another Tab", textBlockTwo, "ct_text_block");

                        var timeoutInner = $timeout(function() {

                            jQuery(scope.getComponentById(addedComponentIDTwo)).trigger('click');
                            // activate back the initial component
                            scope.activateComponent(activeComponentID, activeComponentName);
                            scope.adjustResizeBox();

                            $timeout.cancel(timeoutInner);
                        }, 100, false);

                        $timeout.cancel(timeoutTab);
                    }, 0, false);
                }
            };
            

            scope.addComponent = function(componentName, type, notActivate, params) {

                scope.cancelDeleteUndo();

                if (scope.log) {
                    console.log("addComponent()", componentName, type, notActivate, params);
                }
                
                var innerContent = jQuery('div.ct-component.ct-inner-content:not(.ct-inner-content-workarea)');

                if(componentName === "ct_inner_content" && innerContent.length > 0) {//&& jQuery('div.ct-component.ct-inner-content').length > 0) {
                    alert('You cannot add more than one Inner Content component to a template.');
                    return;
                }

                if (componentName === "oxy-product-tabs" && jQuery(".oxy-product-tabs").length > 0) {
                    scope.showNoticeModal("<div>You cannot add more than one Product Tabs element</div>");
                    return;
                }

                if(componentName === "ct_inner_content")
                    scope.innerContentAdded = true;


                if ( componentName == "oxy_dynamic_list" ) {    
                    iframeScope.dynamicListActions.actions[scope.component.id] = {};
                }

                var parent = false;
                //console.log("addComponent", componentName);

                // set default options first
                scope.applyComponentDefaultOptions(scope.component.id, componentName);

                var parent_id = null;
                if (undefined != params) {
                    parent_id = params.parent_id;
                }

                var insertData = {},
                    componentTemplate   = scope.getComponentTemplate(componentName, scope.component.id, type, null, parent_id);

                insertData.idToInsert          = scope.component.active.id;
                insertData.activeComponent     = scope.getActiveComponent();
                insertData.index               = false;
                
                if (!insertData.activeComponent) {

                    insertData.activeComponent = document.getElementsByClassName("ct-builder"); 
                    // create jqLite element
                    insertData.activeComponent = angular.element(insertData.activeComponent);

                    insertData.idToInsert = 0;
                }

                // don't allow to insert API components inside itslef at any deep
                if ( scope.isAPIComponent(componentName) ) {
                    scope.insertBeforeComponent("."+componentName, insertData);
                }

                // Section can't be a child of a Section or Header Builder
                if ( componentName == "ct_section" ) {
                    scope.insertBeforeComponent(".ct_section, .oxy_header, .ct_slider", insertData);
                }

                // Modal can't be a child of a Section, Header Builder or another Modal
                if ( componentName == "ct_modal" ) {
                    scope.insertBeforeComponent(".ct_section, .oxy_header, .ct_slider, .ct_modal", insertData);
                    // force apply of default width
                    var timeoutModal = $timeout(function() {
                        scope.setOptionModel("width",iframeScope.component.options[scope.component.active.id].model.width,scope.component.active.id,"ct_modal");
                        $timeout.cancel(timeoutModal);
                    }, 0, false);
                }

                // new columns can't be a child of a new columns
                if ( componentName == "ct_new_columns" ) {
                    scope.insertBeforeComponent(".ct_new_columns", insertData);
                }

                // tabs can't be a child of other tabs
                if ( componentName == "oxy_tabs" || componentName == "oxy_tabs_contents" ) {
                    scope.insertBeforeComponent(".oxy_tabs, .oxy_tabs_contents", insertData);
                }

                // link wrapper or text link can't be a child of a link wrapper
                if ( componentName == "ct_link" || componentName == "ct_link_text") {
                    scope.insertBeforeComponent(".ct_link", insertData);
                }

                // Header Builder can't be a child of a Header Builder or Section
                if ( componentName == "oxy_header" ) {
                    scope.insertBeforeComponent(".oxy_header, .ct_section", insertData);
                }

                // Inner Content can't be a child of a Header Builder or Section
                if ( componentName == "ct_inner_content" ) {
                    scope.insertBeforeComponent(".oxy_header, .ct_section", insertData);
                }
                
                // slider can't be a child of a slider
                if ( componentName == "ct_slider" ) {
                    scope.insertBeforeComponent(".ct_slider", insertData);
                }

                // Icon Box can't be a child of a Icon Box
                if ( componentName == "oxy_icon_box" ) {
                    scope.insertBeforeComponent(".oxy_icon_box", insertData);
                }

                // Pricing Box can't be a child of a Pricing Box
                if ( componentName == "oxy_pricing_box" ) {
                    scope.insertBeforeComponent(".oxy_pricing_box", insertData);
                }

                // while editing an outer template, do not nest anything under ct_inner_content
                if ( scope.component.active.name == "ct_inner_content" && !scope.isTemplateInnerContent(scope.component.active)) {
                    scope.insertBeforeComponent(".ct_inner_content", insertData);
                }

                // only add Buttons or Links to Pricnig Box and Icon Box via +Add
                if ( componentName != "ct_link_button" && componentName != "ct_link_text" && type !== "builtin") {
                    scope.insertBeforeComponent(".oxy_pricing_box, .oxy_icon_box", insertData);
                }

                // check if we add a column into a columns
                if ( scope.component.active.name == "ct_columns" && componentName == "ct_column" ) {
                    
                    var innerWrap = scope.getInnerWrap(insertData.activeComponent);
                    innerWrap.append(componentTemplate);
                    
                    scope.cleanInsert(insertData.activeComponent);

                    // insert to Components Tree active element
                    callback = scope.insertComponentToTree;

                    // update model
                    scope.setOptionModel("width", "100", scope.component.id, "ct_column");
                }

                // check if we add a row into a Header Builder
                else if ( scope.component.active.name == "oxy_header" && componentName == "oxy_header_row" ) {
                    
                    var innerWrap   = scope.getInnerWrap(insertData.activeComponent);
                    callback        = scope.insertComponentToTree;
                    
                    // insert to DOM
                    innerWrap.append(componentTemplate);
                    scope.cleanInsert(insertData.activeComponent);
                }

                // check if we add a div into new columns
                else if ( scope.component.active.name == "ct_new_columns" && componentName == "ct_div_block" ) {
                    
                    var innerWrap   = scope.getInnerWrap(insertData.activeComponent);
                    callback        = scope.insertComponentToTree;

                    // insert to DOM
                    innerWrap.append(componentTemplate);
                    scope.cleanInsert(insertData.activeComponent);
                }

                // check if we add anything other than div into new columns and columns not empty
                else if ( scope.component.active.name == "ct_new_columns" && componentName != "ct_div_block" && 
                          insertData.activeComponent.children(".ct_div_block").length > 0) {
                    
                    var firstColumn         = insertData.activeComponent.children(".ct_div_block").first();
                    insertData.idToInsert   = parseInt(firstColumn.attr("ng-attr-component-id"));
                    callback                = scope.insertComponentToTree;
                    
                    // insert to DOM
                    scope.cleanInsert(componentTemplate, firstColumn);
                }

                // check if we add anything other than Div into Tabs or Tabs Contents
                else if ( (scope.component.active.name == "oxy_tabs" || scope.component.active.name == "oxy_tabs_contents") && componentName != "ct_div_block" && 
                          insertData.activeComponent.children(".ct_div_block").length > 0) {
                    
                    var firstChild         = insertData.activeComponent.children(".ct_div_block").first();
                    insertData.idToInsert   = parseInt(firstChild.attr("ng-attr-component-id"));
                    callback                = scope.insertComponentToTree;
                    
                    // insert to DOM
                    scope.cleanInsert(componentTemplate, firstChild);
                }

                // add Header Row after Header Row
                else if ( scope.component.active.name == "oxy_header_row" && componentName == "oxy_header_row" ) {
                    
                    parent              = insertData.activeComponent.parent();
                    insertData.index    = insertData.activeComponent.index() + 1; // save current component index
                    callback            = scope.insertComponentToParent;
                    
                    // insert to DOM
                    scope.cleanInsert(componentTemplate, parent, insertData.index);
                }

                // add to Header Left when trying to add to Header Row
                else if ( scope.component.active.name == "oxy_header_row" && componentName !== "oxy_header_left" && componentName !== "oxy_header_center" && componentName !== "oxy_header_right") {

                    var oxyHeaderLeft       = insertData.activeComponent.find(".oxy-header-left");
                    insertData.idToInsert   = parseInt(oxyHeaderLeft.attr("ng-attr-component-id"));
                    callback                = scope.insertComponentToTree;
                    
                    // insert to DOM
                    scope.cleanInsert(componentTemplate, oxyHeaderLeft);
                }

                // add to Header Left when trying to add to Header
                else if ( scope.component.active.name == "oxy_header" && componentName !== "oxy_header_left" && componentName !== "oxy_header_center" && componentName !== "oxy_header_right") {

                    var oxyHeaderLeft       = insertData.activeComponent.find(".oxy-header-left");
                    insertData.idToInsert   = parseInt(oxyHeaderLeft.attr("ng-attr-component-id"));
                    callback                = scope.insertComponentToTree;
                    
                    // insert to DOM
                    scope.cleanInsert(componentTemplate, oxyHeaderLeft);
                }

                // add Header columns inside Header Row
                else if ( componentName == "oxy_header_left" || componentName == "oxy_header_center" || componentName == "oxy_header_right" ) {
                    
                    var innerWrap       = scope.getInnerWrap(insertData.activeComponent);
                    callback            = scope.insertComponentToTree;
                    
                    // insert to DOM
                    innerWrap.append(componentTemplate);
                    scope.cleanInsert(insertData.activeComponent);
                }

                // Ul
                else if ( scope.component.active.name == "ct_ul" && componentName != "ct_li" ){
                    
                    parent              = insertData.activeComponent.parent();
                    insertData.index    = insertData.activeComponent.index() + 1;
                    callback            = scope.insertComponentToParent;

                    // insert to DON
                    scope.cleanInsert(componentTemplate, parent, insertData.index);
                } 

                else if ( scope.component.active.name == "ct_if_else_wrap" && componentName != "ct_if_wrap" && componentName != "ct_else_wrap" ){
                    
                    parent              = insertData.activeComponent.parent();
                    insertData.index    = insertData.activeComponent.index() + 1;
                    callback            = scope.insertComponentToParent;

                    // insert to DON
                    scope.cleanInsert(componentTemplate, parent, insertData.index);
                }  
                else if ( scope.component.active.name == "oxy_dynamic_list" && (componentName != "ct_div_block" || insertData.activeComponent.children('.ct_div_block').length > 0)){

                    var innerWrap = insertData.activeComponent.children('div:first-child');
                    
                    if(innerWrap.length > 0) {
                        scope.cleanInsert(componentTemplate, innerWrap, insertData.index);

                        // insert to Components Tree active element
                        callback = scope.insertComponentToChild;
                    }
                    else {
                        parent              = insertData.activeComponent.parent();
                        insertData.index    = insertData.activeComponent.index() + 1;
                        callback            = scope.insertComponentToParent;

                        // insert to DON
                        scope.cleanInsert(componentTemplate, parent, insertData.index);
                    }
                } 
                // Li
                else if ( scope.component.active.name == "ct_li" && componentName != "ct_li" ) {
                    
                    parent              = insertData.activeComponent.parent().parent();                    
                    insertData.index    = insertData.activeComponent.parent().index() + 1;
                    callback            = scope.insertComponentToGrandParent;

                    // insert to DOM
                    scope.cleanInsert(componentTemplate, parent, insertData.index);
                } 
                
                // insert Slide after Slide
                else if ( scope.component.active.name == "ct_slide" && componentName == "ct_slide" ) {
                    
                    parent              = insertData.activeComponent.parent().parent();
                    insertData.index    = insertData.activeComponent.parent().index() + 1; // save current component index
                    callback            = scope.insertComponentToParent;

                    // insert to DOM
                    scope.cleanInsert(componentTemplate, parent, insertData.index);
                }

                // insert API component after itself
                else if ( scope.component.active.name == componentName && scope.isAPIComponent(componentName) ) {
                    
                    parent              = insertData.activeComponent.parent().closest('[is-nestable]');
                    insertData.index    = insertData.activeComponent.parent().closest('ct-component').index() + 1; // save current component index
                    callback            = scope.insertComponentToParent;

                    // insert to DOM
                    scope.cleanInsert(componentTemplate, parent, insertData.index);
                } 

                // check if we add anything other than Slide into Slider and Slider doesn't empty
                else if ( scope.component.active.name == "ct_slider" && componentName != "ct_slide" && 
                          insertData.activeComponent.find(".unslider-active").length > 0) {
                    
                    var activeSlide         = insertData.activeComponent.find(".unslider-active").children(".ct_slide");
                    insertData.idToInsert   = parseInt(activeSlide.attr("ng-attr-component-id"));
                    callback                = scope.insertComponentToTree;

                    // insert to DOM
                    scope.cleanInsert(componentTemplate, activeSlide);
                }

                // check if we add anything other than Slide into Slider and Slider is empty
                else if ( scope.component.active.name == "ct_slider" && componentName != "ct_slide" && 
                          insertData.activeComponent.find(".unslider-active").length == 0) {
                    
                    parent              = insertData.activeComponent.parent();
                    insertData.index    = insertData.activeComponent.index() + 1;
                    callback            = scope.insertComponentToParent;

                    // insert to DOM
                    scope.cleanInsert(componentTemplate, parent, insertData.index);
                }
                
                // handle nestable components
                else if ( insertData.activeComponent[0].attributes['is-nestable'] && scope.canBeChild(scope.component.active.name, componentName)) {

                    // append to element
                    if((jQuery('body').hasClass('ct_inner') || jQuery('body', window.parent.document).hasClass('ct_inner')) && (parseInt(insertData.idToInsert) === 0 || parseInt(insertData.idToInsert) > 100000)) {
                        //reassign the component to the ct_inner_content
                        if(jQuery('.ct-inner-content.ct-component').length > 0) {

                            var innerContentID = parseInt(jQuery('.ct-inner-content.ct-component').attr('ng-attr-component-id'));
                            if (insertData.idToInsert!=innerContentID) {
                                insertData.idToInsert=innerContentID;
                                insertData.index = false;
                                scope.activateComponent(insertData.idToInsert);
                                insertData.activeComponent = scope.getActiveComponent();
                                //parent = insertData.activeComponent;
                                callback = scope.insertComponentToTree;
                            }
                        }
                    }

                    var innerWrap = scope.getInnerWrap(insertData.activeComponent);
                    if ( type == "builtin" ) {
                        innerWrap = scope.getBuiltInWrap(insertData.activeComponent);
                    }
                    scope.cleanInsert(componentTemplate, innerWrap, insertData.index);

                    // insert to Components Tree active element
                    callback = scope.insertComponentToTree;

                }

                // allow to insert builtin components to non nestable components
                else if ( type == "builtin" ) {
                    
                    innerWrap = scope.getBuiltInWrap(insertData.activeComponent);
                    scope.cleanInsert(componentTemplate, innerWrap, insertData.index);

                    // insert to Components Tree active element
                    callback = scope.insertComponentToTree;
                }

                // not nestable elements
                else {
                    
                    parent = insertData.activeComponent.parent();
                    // insert to Components Tree active element parent
                    callback = scope.insertComponentToParent;

                    // find first nestable parent
                    if ( ! parent[0].attributes['is-nestable'] && ! parent.hasClass('ct-inner-wrap') ) {
                        while ( ! parent[0].attributes['is-nestable'] && ! parent.hasClass('ct-inner-wrap') ) {
                            parent = parent.parent();
                        }
                        // insert to Components Tree active element grand parent
                        callback = scope.insertComponentToGrandParent;
                    }

                    if (scope.component.options[scope.component.active.id]['original']['oxy_builtin']=='true') {
                        parent = parent.parent();
                    }

                    // get index to insert after current component
                    insertData.index = insertData.activeComponent.index() + 1;

                    // append to parent
                    scope.cleanInsert(componentTemplate, parent, insertData.index);
                }

                // TODO: This is stupid, we need to redo it to be just "type"
                if ( type == "shortcode" ) {
                    var isShortcode = true;
                }

                if ( type == "widget" ) {
                    var isWidget = true;
                }

				if ( type == "data" ) {
					var isData = true;
				}

                if ( type == "sidebar" ) {
                    var isSidebar = true;
                }

                if ( type == "nav_menu" ) {
                    var isNavMenu = true;
                }

                if ( type == "builtin" ) {
                    var isBuiltIn = true;
                    scope.builtinComponents[scope.component.id] = true;
                }
                
                var component = {
                    id : scope.component.id,
                    name : componentName,
                    isShortcode : isShortcode,
                    isWidget: isWidget,
					isData: isData,
                    isSidebar: isSidebar,
                    isNavMenu: isNavMenu,
                    isBuiltIn: isBuiltIn,
                    index: insertData.index,
                };
                
                // insert to Components Tree
                scope.findComponentItem(scope.componentsTree.children, insertData.idToInsert, callback, component);

                // activate component
                if (!notActivate) {
                    scope.activateComponent(scope.component.id, componentName);
                }

                // Lets keep the newly added component in memory, this will help to distinguish these from the ones loaded from db
                scope.justaddedcomponents = scope.justaddedcomponents || [];
                scope.justaddedcomponents.push(scope.component.id);

                // update options
                scope.setOption(scope.component.id, componentName);
                scope.setFirstTimeOptions(scope.component.id, componentName);

                // rebuild Slider each time new slide is added
                if ( componentName == "ct_slide") {
                    var timeoutSlide = $timeout(function() {
                        
                        scope.rebuildDOM(insertData.idToInsert);

                        $timeout.cancel(timeoutSlide);
                    }, 0, false);
                }
                // rebuild Parent component each time this one is added
                if ( typeof(params) !== 'undefined' && params['rebuild_parent']) {
                    var timeoutParentRebuild = $timeout(function() {
                        console.log('rebuild', insertData.idToInsert)
                        scope.rebuildDOM(insertData.idToInsert);

                        $timeout.cancel(timeoutParentRebuild);
                    }, 0, false);
                }

                // if it is inserted inside an oxy dynamic list component, then update the list
                if(componentName !== 'ct_span') {
                    var oxyList = scope.getComponentById(component.id).parent().closest('.oxy-dynamic-list');
                    if(oxyList.length > 0 && !oxyList.hasClass('oxy-dynamic-list-edit')) {
                        
                        if(scope.afterComponentsAddedSignal) {
                            clearTimeout(scope.afterComponentsAddedSignal);
                        }

                        scope.afterComponentsAddedSignal = setTimeout(function() {
                            scope.updateRepeaterQuery(parseInt(oxyList.attr('ng-attr-component-id')));
                        }, 500);
                    }
                }

                // look for any parent that should be rebuilt and rebuild it
                scope.rebuildDOMChangeParent(insertData.idToInsert);

                // check if it was the first child and parent is AJAX element
                var parent = scope.findComponentItem(scope.componentsTree.children, insertData.idToInsert, scope.getComponentItem);
                if ( parent.children && parent.children.length === 1 && scope.isAJAXElement(parent.name)) {
                    scope.rebuildDOM(insertData.idToInsert);
                }

                // increment id
                scope.component.id++;

                $parentScope.closeAllTabs();
                scope.unsavedChanges();

                // scroll to the component after selector is parsed
                var interval = 0;
                if ( componentName == "ct_slide" || componentName == "oxy_social_icons" ) {
                    interval = 1000;
                }
                var timeoutScroll = $timeout(function() {
                    var component = scope.getActiveComponent();
                    if (component) {
                        $parentScope.scrollToComponent(component.attr('id'));
                    }
                    $timeout.cancel(timeoutScroll);
                }, interval, false);

                // Adding 3 children of a Header Builder Row
                if ( componentName == "oxy_header_row" ) {
                    var timeoutHeaderRow = $timeout(function() {
                        scope.waitOxygenTree(function(){
                            scope.addComponent("oxy_header_left", false, true);
                            scope.addComponent("oxy_header_center", false, true);
                            scope.addComponent("oxy_header_right", false, true);
                        })
                        $timeout.cancel(timeoutHeaderRow);
                    }, 0, false);
                }

                // Adding a child div to a repeater
                if ( componentName == "oxy_dynamic_list" ) {    
                    var timeoutDynamicList = $timeout(function() {
                        scope.waitOxygenTree(function(){
                            scope.addComponent("ct_div_block", false, true, {parent_id: component.id});
                        })
                        $timeout.cancel(timeoutDynamicList);
                    }, 0, false);
                }

                // Adding builtin Icon to Icon Box
                if ( componentName == "oxy_icon_box" ) {                    
                    var timeoutIconBox = $timeout(function() {
                        scope.waitOxygenTree(function(){
                            var iconID = scope.addComponent("ct_fancy_icon", "builtin", true);
                            scope.setOptionModel('icon-id', "Lineariconsicon-rocket", iconID, "ct_fancy_icon");
                        })
                        $timeout.cancel(timeoutIconBox);
                    }, 0, false);
                }

                // Adding builtin Divs to Superbox
                if ( componentName == "oxy_superbox" ) {                    
                    var timeoutSuperbox = $timeout(function() {
                        scope.waitOxygenTree(function(){
                            var primary     = scope.addComponent("ct_div_block", "builtin", true),
                                secondary   = scope.addComponent("ct_div_block", "builtin", true);

                            scope.component.options[primary]['nicename']    = "Primary";
                            scope.component.options[secondary]['nicename']  = "Secondary";

                            scope.updateFriendlyName(primary);
                            scope.updateFriendlyName(secondary);

                            scope.addClassToComponentSafe(primary,'oxy-superbox-primary');
                            scope.addClassToComponentSafe(secondary,'oxy-superbox-secondary');

                            scope.setOptionModel('align-items', "center", secondary, "ct_div_block");
                            scope.setOptionModel('justify-content', "center", secondary, "ct_div_block");
                            scope.setOptionModel('background-color', "rgba(0,0,0,0.3)", secondary, "ct_div_block");
                            scope.setOptionModel('text-align', "center", secondary, "ct_div_block");


                            scope.activateComponent(primary);
                            var imageId = scope.addComponent("ct_image", false, true);
                            scope.setOptionModel('src', "https://source.unsplash.com/random/480x270", imageId, "ct_image");
                            scope.setOptionModel('width', "400", imageId, "ct_image");

                            scope.activateComponent(secondary);
                            var headlineID = scope.addComponent("ct_headline", false, true);
                            scope.setOptionModel('ct_content', "Superbox", headlineID, "ct_headline");
                            scope.setOptionModel('font-size', "36", headlineID, "ct_headline");
                            scope.setOptionModel('color', "#ffffff", headlineID, "ct_headline");
                            scope.setOptionModel('tag', "h2", headlineID, "ct_headline");

                            // activate Superbox at the end
                            scope.activateComponent(component.id);
                        })
                        $timeout.cancel(timeoutSuperbox);
                    }, 0, false);
                }

                // Adding components to Pricing Box
                if ( componentName == "oxy_pricing_box" ) {                    
                    var timeoutImage = $timeout(function() {
                        scope.waitOxygenTree(function(){
                            var imageId = scope.addComponent("ct_image", "builtin", true);
                            scope.setOptionModel('src', "http://via.placeholder.com/300x150", imageId, "ct_image");

                            scope.addComponent("ct_link_button", false, true);
                        })
                        $timeout.cancel(timeoutImage);
                    }, 0, false);
                }

                // Adding tabs
                if ( componentName == "oxy_tabs" ) {

                    var UID = oxygenMD5(Date.now().toString()).match(/\d+/)[0].substring(0,4),
                        i = 0;
                    while (UID.length<4&&i<100000) {
                        UID = oxygenMD5(Date.now().toString()).match(/\d+/)[0].substring(0,4);
                        i++;
                    }

                    var tabOneID;
                    
                    scope.waitOxygenTree(function(){
                        var timeoutAddTabs = $timeout(function() { 

                            tabOneID    = scope.addComponent("oxy_tab", false, true); 
                            var tabTwoID    = scope.addComponent("oxy_tab", false, true);
                            var tabThreeID  = scope.addComponent("oxy_tab", false, true);

                            scope.addClassToComponentSafe(tabOneID,'tabs-'+UID+'-tab');
                            scope.addClassToComponentSafe(tabTwoID,'tabs-'+UID+'-tab');
                            scope.addClassToComponentSafe(tabThreeID,'tabs-'+UID+'-tab');

                            scope.addClassToComponentSafe(tabOneID,'tabs-'+UID+'-tab-active');
                            scope.addClassToComponentSafe(tabTwoID,'tabs-'+UID+'-tab-active');
                            scope.addClassToComponentSafe(tabThreeID,'tabs-'+UID+'-tab-active');

                            scope.activeSelectors = scope.activeSelectors || {};
                            scope.activeSelectors[tabOneID] = 'tabs-'+UID+'-tab';
                            scope.activeSelectors[tabTwoID] = 'tabs-'+UID+'-tab';
                            scope.activeSelectors[tabThreeID] = 'tabs-'+UID+'-tab';
                            
                            scope.activateComponent(tabOneID, "oxy_tab");
                            var tabOneTextID = scope.addComponent("ct_text_block", false, true);
                            scope.setOptionModel('ct_content', "Tab #1", tabOneTextID, "ct_text_block");

                            scope.activateComponent(tabTwoID, "oxy_tab");
                            var tabTwoTextID = scope.addComponent("ct_text_block", false, true);
                            scope.setOptionModel('ct_content', "Tab #2", tabTwoTextID, "ct_text_block");

                            scope.activateComponent(tabThreeID, "oxy_tab");
                            var tabThreeTextID = scope.addComponent("ct_text_block", false, true);
                            scope.setOptionModel('ct_content', "Tab #3", tabThreeTextID, "ct_text_block");
                        
                            $timeout.cancel(timeoutAddTabs);
                        }, 0, false);
                    })
                    
                    scope.waitOxygenTree(function(){
                        var timeoutTabsContents = $timeout(function() {

                            scope.activateComponent(component.id, "oxy_tabs");
                            var tabsContentsID = scope.addComponent("oxy_tabs_contents", false, true);  
                            scope.activateComponent(tabsContentsID, "oxy_tabs_contents");

                            var tabContentOneID    = scope.addComponent("oxy_tab_content", false, true); 
                            var tabContentTwoID    = scope.addComponent("oxy_tab_content", false, true);
                            var tabContentThreeID  = scope.addComponent("oxy_tab_content", false, true);

                            scope.addClassToComponentSafe(tabContentOneID,'tabs-contents-'+UID+'-tab');
                            scope.addClassToComponentSafe(tabContentTwoID,'tabs-contents-'+UID+'-tab');
                            scope.addClassToComponentSafe(tabContentThreeID,'tabs-contents-'+UID+'-tab');

                            scope.activeSelectors = scope.activeSelectors || {};
                            scope.activeSelectors[tabContentOneID] = 'tabs-contents-'+UID+'-tab';
                            scope.activeSelectors[tabContentTwoID] = 'tabs-contents-'+UID+'-tab';
                            scope.activeSelectors[tabContentThreeID] = 'tabs-contents-'+UID+'-tab';
                            
                            scope.activateComponent(tabContentOneID, "oxy_tab_content");
                            var tabOneTextID = scope.addComponent("ct_text_block", false, true);
                            scope.setOptionModel('ct_content', "Tab Content #1", tabOneTextID, "ct_text_block");

                            scope.activateComponent(tabContentTwoID, "oxy_tab_content");
                            var tabTwoTextID = scope.addComponent("ct_text_block", false, true);
                            scope.setOptionModel('ct_content', "Tab Content #2", tabTwoTextID, "ct_text_block");

                            scope.activateComponent(tabContentThreeID, "oxy_tab_content");
                            var tabThreeTextID = scope.addComponent("ct_text_block", false, true);
                            scope.setOptionModel('ct_content', "Tab Content #3", tabThreeTextID, "ct_text_block");

                            // Set data
                            scope.activateComponent(component.id, "oxy_tabs");
                            scope.setOptionModel('tabs_wrapper', scope.component.options[component.id]['selector'], tabsContentsID, "oxy_tabs_contents");
                            scope.setOptionModel('tabs_contents_wrapper', scope.component.options[tabsContentsID]['selector'], component.id, "oxy_tabs");
                            scope.setOptionModel('active_tab_class', 'tabs-'+UID+'-tab-active', component.id, "oxy_tabs");

                            var timeoutInner = $timeout(function() {
                                jQuery(scope.getComponentById(component.id)).children('.oxy-tabs-wrapper > div').eq(0).trigger('click');
                                scope.activateComponent(tabOneID, "oxy_tab");
                                $timeout.cancel(timeoutInner);
                            }, 100, false);

                            var timeoutInner2 = $timeout(function() {
                                scope.waitOxygenTree(function(){
                                    scope.adjustResizeBox();
                                })
                                $timeout.cancel(timeoutInner2);
                            }, 200, false);

                        
                            $timeout.cancel(timeoutTabsContents);
                        }, 100, false);
                    })
                }


                // Adding tabs
                if ( componentName == "oxy_toggle" ) {

                    var UID = oxygenMD5(Date.now().toString()).match(/\d+/)[0].substring(0,4),
                        i = 0;
                    while (UID.length<4&&i<100000) {
                        UID = oxygenMD5(Date.now().toString()).match(/\d+/)[0].substring(0,4);
                        i++;
                    }

                    var parentID = scope.component.active.parent.id,
                        parentName = scope.component.active.parent.name;

                    scope.addClassToComponentSafe(component.id,'toggle-'+UID);
                    scope.addClassToComponentSafe(component.id,'toggle-'+UID+'-expanded');

                    scope.activeSelectors[component.id] = 'toggle-'+UID;

                    scope.setOptionModel('toggle_active_class', 'toggle-'+UID+'-expanded', component.id, "oxy_toggle");

                    var timeoutToggle = $timeout(function() {
                        scope.waitOxygenTree(function(){
                            scope.addComponent("ct_headline", false, true);
                            scope.activateComponent(parentID, parentName);
                            scope.addComponent("ct_text_block", false, true); 
                        })
                        $timeout.cancel(timeoutToggle);
                    }, 10, false);
                }

                // return inserted component id
                return component.id;
            };
        }
    };
});


/**
 * Fix for Webkit to force CSS apply on input change
 * 
 */

CTFrontendBuilder.directive("ngModel",function($timeout, $interval){
    return {
        restrict: 'A',
        priority: -1, // give it lower priority than built-in ng-model
        link: function(scope, element, attr) {
            scope.$watch(attr.ngModel,function(value){
                if (element.is(':radio')){
                    element.next().addClass('ct-update-css');
                    element.next().removeClass('ct-update-css');
                }
            });
        }
      }
});


/**
 * Float editor panel. Use jQuery Draggable for user custom drag
 *
 */

CTFrontendBuilder.directive('ctFloatEditor', function() {

    return {
        link:function(scope,parentScope,el,attrs) {

            var toolBar                 = angular.element("#oxygen-topbar"),
                toolBarHeight           = toolBar.outerHeight(),
                
                activeComponent         = scope.getActiveComponent(),
                activeComponentHeight   = activeComponent.outerHeight(),
                
                windowPosition          = activeComponent[0].getBoundingClientRect(),
                documentPosition        = activeComponent.offset();


            // init jQuery UI draggable
            el.draggable({ 
                handle: ".ct-draggable-handle",
                containment: "document"
            })

            var draggableHeight = el.outerHeight(),
                draggableWidth  = el.outerWidth(),
                yOffset = xOffset = 0;

            if (el.is(".ct-choose-selector")) {
                yOffset = 110;
            }
            else 
            // calcualte Y offset
            if ( toolBarHeight + draggableHeight > windowPosition.top ) {
                yOffset = activeComponentHeight + 8;
            }
            else {
                yOffset = -(draggableHeight + 8)
            }

            var domTreeWidth = (parentScope.showSidePanel) ? 330 : 0;

            // calcualte X offset
            if ( windowPosition.left + draggableWidth > document.documentElement.clientWidth - 20 - domTreeWidth) {
                xOffset = (document.documentElement.clientWidth - 20 - domTreeWidth) - (windowPosition.left + draggableWidth);
            }

            // place draggable
            el.css({
                'top' : documentPosition.top + yOffset, 
                'left' : documentPosition.left + xOffset,
            })
        }
    }
})


/**
 * Resize Box directive for paddings and margins
 * Used insted of outlines since 2.0
 * 
 * taken from https://github.com/Reklino/angular-resizable
 */

CTFrontendBuilder.directive('oxygenResizeBox', function($timeout,$interval) {

        return {
            restrict: 'AE',
            scope: {
                rOptions: '=',
                rCenteredX: '=',
                rCenteredY: '=',
                rWidth: '=',
                rHeight: '=',
                rFlex: '=',
                rGrabber: '@',
                rDisabled: '@',
            },
            link: function(scope, element, attr) {

                var flexBasis = 'flexBasis' in document.documentElement.style ? 'flexBasis' :
                    'webkitFlexBasis' in document.documentElement.style ? 'webkitFlexBasis' :
                    'msFlexPreferredSize' in document.documentElement.style ? 'msFlexPreferredSize' : 'flexBasis';

                var style = window.getComputedStyle(element[0], null),
                    w,
                    h,
                    dir = scope.rOptions || ['top','right','bottom','left','padding-top','padding-right', 'padding-bottom','padding-left',
                                             'margin-top','margin-right', 'margin-bottom','margin-left'],
                    vx = scope.rCenteredX ? 2 : 1, // if centered double velocity
                    vy = scope.rCenteredY ? 2 : 1, // if centered double velocity
                    inner = scope.rGrabber ? scope.rGrabber : '<span></span>',
                    start,
                    dragDir,
                    axis,
                    info = {},
                    activeComponent,
                    dragHandle,
                    newValue;

                var getClientX = function(e) {
                    return e.touches ? e.touches[0].clientX : e.clientX;
                };

                var getClientY = function(e) {
                    return e.touches ? e.touches[0].clientY : e.clientY;
                };

                var dragging = function(e) {

                    if (scope.$parent.getOption('selector-locked')=='true') {
                        return;
                    }
                    
                    var offset = axis === 'x' ? start - getClientX(e) : start - getClientY(e);

                    switch(dragDir) {

                        case 'margin-top':
                            newValue = ( marginTop - (offset * vy) > 0 ) ? marginTop - (offset * vy) : 0;
                            scope.$parent.parentScope.showStatusBar("margin-top:"+newValue+marginTopUnit);
                            if (scope.$parent.isEditing('id')) {
                                jQuery(activeComponent).css('margin-top',newValue.toString()+marginTopUnit);
                            }
                            else if (scope.$parent.isEditing('class')) {
                                var style = "." + scope.$parent.currentClass + "{margin-top:"+newValue.toString()+marginTopUnit+"}";
                                scope.$parent.outputCSSStyles("oxy-dragging-styles", style);
                            }
                            break;

                        case 'margin-bottom':
                            newValue = ( marginBottom - (offset * vy) > 0 ) ? marginBottom - (offset * vy) : 0;
                            scope.$parent.parentScope.showStatusBar("margin-bottom:"+newValue+marginBottomUnit);
                            if (scope.$parent.isEditing('id')) {
                                jQuery(activeComponent).css('margin-bottom',newValue.toString()+marginBottomUnit);
                            }
                            else if (scope.$parent.isEditing('class')) {
                                var style = "." + scope.$parent.currentClass + "{margin-bottom:"+newValue.toString()+marginBottomUnit+"}";
                                scope.$parent.outputCSSStyles("oxy-dragging-styles", style);
                            }
                            break;

                        case 'margin-right':
                            newValue = ( marginRight - (offset * vx) > 0 ) ? marginRight - (offset * vx) : 0;
                            scope.$parent.parentScope.showStatusBar("margin-right:"+newValue+marginRightUnit);
                            if (scope.$parent.isEditing('id')) {
                                jQuery(activeComponent).css('margin-right',newValue.toString()+marginRightUnit);
                            }
                            else if (scope.$parent.isEditing('class')) {
                                var style = "." + scope.$parent.currentClass + "{margin-right:"+newValue.toString()+marginRightUnit+"}";
                                scope.$parent.outputCSSStyles("oxy-dragging-styles", style);
                            }
                            break;

                        case 'margin-left':
                            newValue = ( marginLeft - (offset * vx) > 0 ) ? marginLeft - (offset * vx) : 0;
                            scope.$parent.parentScope.showStatusBar("margin-left:"+newValue+marginLeftUnit);
                            if (scope.$parent.isEditing('id')) {
                                jQuery(activeComponent).css('margin-left',newValue.toString()+marginLeftUnit);
                            }
                            else if (scope.$parent.isEditing('class')) {
                                var style = "." + scope.$parent.currentClass + "{margin-left:"+newValue.toString()+marginLeftUnit+"}";
                                scope.$parent.outputCSSStyles("oxy-dragging-styles", style);
                            }
                            break;

                        case 'padding-top':
                            if (scope.$parent.parentScope.isActiveName('ct_section')) {
                                newValue = ( containerPaddingTop - (offset * vy) > 0 ) ? containerPaddingTop - (offset * vy) : 0;
                                if (scope.$parent.isEditing('id')){
                                    jQuery('.ct-section-inner-wrap', activeComponent).css('padding-top',newValue.toString()+paddingTopUnit);
                                }
                                else if (scope.$parent.isEditing('class')) {
                                    var style = "." + scope.$parent.currentClass + " .ct-section-inner-wrap{padding-top:"+newValue.toString()+paddingTopUnit+"}";
                                }
                            }
                            else {
                                newValue = ( paddingTop - (offset * vy) > 0 ) ? paddingTop - (offset * vy) : 0;
                                if (scope.$parent.isEditing('id')){
                                    jQuery(activeComponent).css('padding-top',newValue.toString()+paddingTopUnit);
                                }
                                else if (scope.$parent.isEditing('class')) {
                                    var style = "." + scope.$parent.currentClass + "{padding-top:"+newValue.toString()+paddingTopUnit+"}";
                                    scope.$parent.outputCSSStyles("oxy-dragging-styles", style);
                                }
                            }
                            scope.$parent.parentScope.showStatusBar("padding-top:"+newValue+paddingTopUnit);
                            if (scope.$parent.parentScope.isActiveName('ct_link_button')) {
                                if (scope.$parent.isEditing('id')){
                                    jQuery(activeComponent).css('padding-bottom',newValue.toString()+paddingTopUnit);
                                }
                                else if (scope.$parent.isEditing('class')) {
                                    style += "." + scope.$parent.currentClass + "{padding-bottom:"+newValue.toString()+paddingTopUnit+"}";
                                }
                            }
                            scope.$parent.outputCSSStyles("oxy-dragging-styles", style);
                            break;

                        case 'padding-bottom':
                            if (scope.$parent.parentScope.isActiveName('ct_section')) {
                                newValue = ( containerPaddingBottom - (offset * vy) > 0 ) ? containerPaddingBottom - (offset * vy) : 0;
                                if (scope.$parent.isEditing('id')) {
                                    jQuery('.ct-section-inner-wrap', activeComponent).css('padding-bottom',newValue.toString()+paddingBottomUnit);
                                }
                                else if (scope.$parent.isEditing('class')) {
                                    var style = "." + scope.$parent.currentClass + " .ct-section-inner-wrap{padding-bottom:"+newValue.toString()+paddingTopUnit+"}";
                                }
                            }
                            else {
                                newValue = ( paddingBottom - (offset * vy) > 0 ) ? paddingBottom - (offset * vy) : 0;
                                if ( scope.$parent.isEditing('id') ) {    
                                    jQuery(activeComponent).css('padding-bottom',newValue.toString()+paddingBottomUnit);
                                }
                                else if (scope.$parent.isEditing('class')) {
                                    var style = "." + scope.$parent.currentClass + "{padding-bottom:"+newValue.toString()+paddingBottomUnit+"}";
                                    scope.$parent.outputCSSStyles("oxy-dragging-styles", style);
                                }
                            }
                            scope.$parent.parentScope.showStatusBar("padding-bottom:"+newValue+paddingBottomUnit);
                            if (scope.$parent.parentScope.isActiveName('ct_link_button')) {
                                if ( scope.$parent.isEditing('id') ) {    
                                    jQuery(activeComponent).css('padding-top',newValue.toString()+paddingBottomUnit);
                                }
                                else if (scope.$parent.isEditing('class')) {
                                    style += "." + scope.$parent.currentClass + "{padding-top:"+newValue.toString()+paddingBottomUnit+"}";
                                }
                            }
                            scope.$parent.outputCSSStyles("oxy-dragging-styles", style);
                            break;

                        case 'padding-right':
                            if (scope.$parent.parentScope.isActiveName('ct_section')) {
                                newValue = ( containerPaddingRight - (offset * vy) > 0 ) ? containerPaddingRight - (offset * vy) : 0;
                                if (scope.$parent.isEditing('id')) {
                                    jQuery('.ct-section-inner-wrap', activeComponent).css('padding-right',newValue.toString()+paddingRightUnit);
                                }
                                else if (scope.$parent.isEditing('class')) {
                                    var style = "." + scope.$parent.currentClass + " .ct-section-inner-wrap{padding-right:"+newValue.toString()+paddingTopUnit+"}";
                                }
                            }
                            else {
                                newValue = ( paddingRight - (offset * vx) > 0 ) ? paddingRight - (offset * vx) : 0;
                                if ( scope.$parent.isEditing('id') ) {    
                                    jQuery(activeComponent).css('padding-right',newValue.toString()+paddingRightUnit);
                                }
                                else if (scope.$parent.isEditing('class')) {
                                    var style = "." + scope.$parent.currentClass + "{padding-right:"+newValue.toString()+paddingRightUnit+"}";
                                    scope.$parent.outputCSSStyles("oxy-dragging-styles", style);
                                }
                            }
                            scope.$parent.parentScope.showStatusBar("padding-right:"+newValue+paddingRightUnit);
                            if (scope.$parent.parentScope.isActiveName('ct_link_button')) {
                                if ( scope.$parent.isEditing('id') ) {    
                                    jQuery(activeComponent).css('padding-left',newValue.toString()+paddingRightUnit);
                                }
                                else if (scope.$parent.isEditing('class')) {
                                    style += "." + scope.$parent.currentClass + "{padding-left:"+newValue.toString()+paddingRightUnit+"}";
                                }
                            }
                            scope.$parent.outputCSSStyles("oxy-dragging-styles", style);
                            break;

                        case 'padding-left':
                            if (scope.$parent.parentScope.isActiveName('ct_section')) {
                                newValue = ( containerPaddingLeft - (offset * vy) > 0 ) ? containerPaddingLeft - (offset * vy) : 0;
                                if (scope.$parent.isEditing('id')) {
                                    jQuery('.ct-section-inner-wrap', activeComponent).css('padding-left',newValue.toString()+paddingLeftUnit);
                                }
                                else if (scope.$parent.isEditing('class')) {
                                    var style = "." + scope.$parent.currentClass + " .ct-section-inner-wrap{padding-left:"+newValue.toString()+paddingTopUnit+"}";
                                }
                            }
                            else {
                                newValue = ( paddingLeft - (offset * vx) > 0 ) ? paddingLeft - (offset * vx) : 0;
                                if ( scope.$parent.isEditing('id') ) {    
                                    jQuery(activeComponent).css('padding-left',newValue.toString()+paddingLeftUnit);
                                }
                                else if (scope.$parent.isEditing('class')) {
                                    var style = "." + scope.$parent.currentClass + "{padding-left:"+newValue.toString()+paddingLeftUnit+"}";
                                    scope.$parent.outputCSSStyles("oxy-dragging-styles", style);
                                }
                            }
                            scope.$parent.parentScope.showStatusBar("padding-left:"+newValue+paddingLeftUnit);
                            if (scope.$parent.parentScope.isActiveName('ct_link_button')) {
                                if ( scope.$parent.isEditing('id') ) {    
                                    jQuery(activeComponent).css('padding-right',newValue.toString()+paddingLeftUnit);
                                }
                                else if (scope.$parent.isEditing('class')) {
                                    style += "." + scope.$parent.currentClass + "{padding-right:"+newValue.toString()+paddingLeftUnit+"}";
                                }
                            }
                            scope.$parent.outputCSSStyles("oxy-dragging-styles", style);
                            break;
                    }

                    if ( newValue > 0 ) {
                        jQuery("#rb-"+dragDir+"-button").css("display","none");
                        jQuery("#rb-"+dragDir).addClass("rb-currently-editing");
                    }

                    if ( newValue == 0 ) {
                        jQuery("#rb-"+dragDir).removeClass("rb-currently-editing");
                    }

                    scope.$parent.adjustResizeBox();

                    scope.$apply();
                };
                var dragEnd = function(e) {

                    scope.$parent.outputCSSStyles("oxy-dragging-styles", "");

                    scope.$apply();
                    document.removeEventListener('mouseup', dragEnd, false);
                    document.removeEventListener('mousemove', dragging, false);
                    document.removeEventListener('touchend', dragEnd, false);
                    document.removeEventListener('touchmove', dragging, false);

                    // properly handle events when pointer is outside of iframe
                    window.parent.document.removeEventListener('mouseup', dragEnd, false);
                    window.parent.document.removeEventListener('touchend', dragEnd, false);

                    if (newValue!==undefined){

                        switch(dragDir) {
                            case 'margin-top':
                                scope.$parent.setOptionModel('margin-top',newValue.toString());
                                jQuery(activeComponent).css('margin-top',"");
                                break;

                            case 'margin-bottom':
                                scope.$parent.setOptionModel('margin-bottom',newValue.toString());
                                jQuery(activeComponent).css('margin-bottom',"");

                                break;

                            case 'margin-right':
                                scope.$parent.setOptionModel('margin-right',newValue.toString());
                                jQuery(activeComponent).css('margin-right',"");

                                break;

                            case 'margin-left':
                                scope.$parent.setOptionModel('margin-left',newValue.toString());
                                jQuery(activeComponent).css('margin-left',"");

                                break;

                            case 'padding-top':
                                if (scope.$parent.parentScope.isActiveName('ct_section')) {
                                    scope.$parent.setOptionModel('container-padding-top',newValue.toString());
                                    jQuery('.ct-section-inner-wrap', activeComponent).css('padding-top','');
                                }
                                else {
                                    scope.$parent.setOptionModel('padding-top',newValue.toString());
                                    jQuery(activeComponent).css('padding-top',"");
                                }
                                if (scope.$parent.parentScope.isActiveName('ct_link_button')) {
                                    scope.$parent.setOptionModel('padding-bottom',newValue.toString());
                                    jQuery(activeComponent).css('padding-bottom',"");
                                }
                                break;

                            case 'padding-bottom':
                                if (scope.$parent.parentScope.isActiveName('ct_section')) {
                                    scope.$parent.setOptionModel('container-padding-bottom',newValue.toString());
                                    jQuery('.ct-section-inner-wrap', activeComponent).css('padding-bottom','');
                                }
                                else {
                                    scope.$parent.setOptionModel('padding-bottom',newValue.toString());
                                    jQuery(activeComponent).css('padding-bottom',"");
                                }
                                if (scope.$parent.parentScope.isActiveName('ct_link_button')) {
                                    scope.$parent.setOptionModel('padding-top',newValue.toString());
                                    jQuery(activeComponent).css('padding-top',"");
                                }
                                break;

                            case 'padding-right':
                                if (scope.$parent.parentScope.isActiveName('ct_section')) {
                                    scope.$parent.setOptionModel('container-padding-right',newValue.toString());
                                    jQuery('.ct-section-inner-wrap', activeComponent).css('padding-right','');
                                }
                                else {
                                    scope.$parent.setOptionModel('padding-right',newValue.toString());
                                    jQuery(activeComponent).css('padding-right',"");
                                }
                                if (scope.$parent.parentScope.isActiveName('ct_link_button')) {
                                    scope.$parent.setOptionModel('padding-left',newValue.toString());
                                    jQuery(activeComponent).css('padding-left',"");
                                }
                                break;

                            case 'padding-left':
                                if (scope.$parent.parentScope.isActiveName('ct_section')) {
                                    scope.$parent.setOptionModel('container-padding-left',newValue.toString());
                                    jQuery('.ct-section-inner-wrap', activeComponent).css('padding-left','');
                                }
                                else {
                                    scope.$parent.setOptionModel('padding-left',newValue.toString());
                                    jQuery(activeComponent).css('padding-left',"");
                                }
                                if (scope.$parent.parentScope.isActiveName('ct_link_button')) {
                                    scope.$parent.setOptionModel('padding-right',newValue.toString());
                                    jQuery(activeComponent).css('padding-right',"");
                                }
                                break;
                        }
                    }

                    element.removeClass('oxygen-no-transition');
                    jQuery(".rb-currently-editing").removeClass("rb-currently-editing");
                    scope.$parent.parentScope.hideStatusBar();
                    scope.$parent.parentScope.$apply();

                    var timeoutDragResize = $timeout(function() {
                        jQuery('body').removeClass('oxy-dragging-resize-box');
                        $timeout.cancel(timeoutDragResize);
                    }, 0, false);
                };
                var dragStart = function(e, direction) {

                    jQuery('body').addClass('oxy-dragging-resize-box');
                    dragDir = direction;
                    dragHandle = jQuery("#rb-"+dragDir);
                    axis = ( dragDir.indexOf('left') >= 0 || dragDir.indexOf('right') >= 0 ) ? 'x' : 'y';
                    start = axis === 'x' ? getClientX(e) : getClientY(e);
                    newValue = undefined;
                    //w = parseInt(style.getPropertyValue('width'));
                    //h = parseInt(style.getPropertyValue('height'));

                    activeComponent = scope.$parent.getActiveComponent();

                    containerPaddingBottom      = parseInt(scope.$parent.getOption('container-padding-bottom'));
                    containerPaddingBottomUnit  = scope.$parent.getOptionUnit('container-padding-bottom');
                    containerPaddingTop         = parseInt(scope.$parent.getOption('container-padding-top'));
                    containerPaddingTopUnit     = scope.$parent.getOptionUnit('container-padding-top');
                    containerPaddingLeft        = parseInt(scope.$parent.getOption('container-padding-left'));
                    containerPaddingLeftUnit    = scope.$parent.getOptionUnit('container-padding-left');
                    containerPaddingRight       = parseInt(scope.$parent.getOption('container-padding-right'));
                    containerPaddingRightUnit   = scope.$parent.getOptionUnit('container-padding-right');

                    paddingTop          = parseInt(scope.$parent.getOption('padding-top')) || 0;
                    paddingTopUnit      = scope.$parent.getOptionUnit('padding-top');
                    paddingRight        = parseInt(scope.$parent.getOption('padding-right')) || 0;
                    paddingRightUnit    = scope.$parent.getOptionUnit('padding-right');
                    paddingBottom       = parseInt(scope.$parent.getOption('padding-bottom')) || 0;
                    paddingBottomUnit   = scope.$parent.getOptionUnit('padding-bottom');
                    paddingLeft         = parseInt(scope.$parent.getOption('padding-left')) || 0;
                    paddingLeftUnit     = scope.$parent.getOptionUnit('padding-left');

                    marginTop           = parseInt(scope.$parent.getOption('margin-top')) || 0;
                    marginTopUnit       = scope.$parent.getOptionUnit('margin-top');
                    marginRight         = parseInt(scope.$parent.getOption('margin-right')) || 0;
                    marginRightUnit     = scope.$parent.getOptionUnit('margin-right');
                    marginBottom        = parseInt(scope.$parent.getOption('margin-bottom')) || 0;
                    marginBottomUnit    = scope.$parent.getOptionUnit('margin-bottom');
                    marginLeft          = parseInt(scope.$parent.getOption('margin-left')) || 0;
                    marginLeftUnit      = scope.$parent.getOptionUnit('margin-left');

                    //prevent transition while dragging
                    element.addClass('oxygen-no-transition');

                    document.addEventListener('mouseup', dragEnd, false);
                    document.addEventListener('mousemove', dragging, false);
                    document.addEventListener('touchend', dragEnd, false);
                    document.addEventListener('touchmove', dragging, false);

                    // properly end dragging when pointer is outside of iframe
                    window.parent.document.addEventListener('mouseup', dragEnd, false);
                    window.parent.document.addEventListener('touchend', dragEnd, false);

                    // Disable highlighting while dragging
                    if(e.stopPropagation) e.stopPropagation();
                    if(e.preventDefault) e.preventDefault();
                    e.cancelBubble = true;
                    e.returnValue = false;

                    scope.$apply();
                };

                dir.forEach(function (direction) {
                    var grabber = document.createElement('div');

                    grabber.setAttribute('class', 'rb');
                    grabber.setAttribute('id', 'rb-' + direction);
                    grabber.innerHTML = inner;
                    element.append(grabber);
                    grabber.ondragstart = function() { return false; };

                    var down = function(e) {
                        var disabled = (scope.rDisabled === 'true');
                        if (!disabled && (e.which === 1 || e.touches)) {
                            // left mouse click or touch screen
                            dragStart(e, direction);
                        }
                    };
                    grabber.addEventListener('mousedown', down, false);
                    grabber.addEventListener('touchstart', down, false);

                    // Overlays
                    if (["top","right","left","bottom"].indexOf(direction) == -1) {

                        // add buttons
                        /*var button = document.createElement('div');

                        button.setAttribute('class', 'rb-button');
                        button.setAttribute('id', 'rb-' + direction + '-button');
                        button.innerHTML = inner;
                        var exactDir = direction.replace("margin-","").replace("padding-","");
                        jQuery("#rb-"+exactDir).append(button);
                        button.ondragstart = function() { return false; };

                        var down = function(e) {
                            var disabled = (scope.rDisabled === 'true');
                            if (!disabled && (e.which === 1 || e.touches)) {
                                // left mouse click or touch screen
                                dragStart(e, direction);
                            }
                        };
                        button.addEventListener('mousedown', down, false);
                        button.addEventListener('touchstart', down, false);*/

                        // fix Chrome hover when mousedown bug
                        jQuery(grabber).mouseenter(function () {
                            jQuery(this).addClass('rb-currently-editing');
                        }).mouseleave(function () {
                            jQuery(this).removeClass('rb-currently-editing');
                        });

                        // unset option on double click
                        jQuery(grabber).dblclick(function (){
                            activeComponent = scope.$parent.getActiveComponent();
                            if (scope.$parent.parentScope.isActiveName('ct_section')) {
                                scope.$parent.setOptionModel('container-'+direction,"");
                                jQuery('.ct-section-inner-wrap', activeComponent).css(direction,'');
                            }
                            else {
                                scope.$parent.setOptionModel(direction,"");
                                jQuery(activeComponent).css(direction,"");
                            }
                            scope.$parent.outputCSSStyles("oxy-dragging-styles", "");
                            scope.$apply();
                        })

                        jQuery(grabber).addClass("rb-overlay");
                    }
                    // Borders
                    else {
                        grabber.addEventListener("mouseover", function(event) {
                            
                            var padding;

                            if ( scope.$parent.parentScope.isActiveName("ct_section") ) {
                                jQuery("#rb-margin-"+direction+"-button").css("display", "none");
                                padding = parseInt(scope.$parent.getOption('container-padding-'+direction));
                            }
                            else {
                                // check if direction has margin or padding = 0
                                var margin  = parseInt(scope.$parent.getOption('margin-'+direction));
                                    padding = parseInt(scope.$parent.getOption('padding-'+direction));
                                if (margin > 0) {
                                    jQuery("#rb-margin-"+direction+"-button").css("display", "none");
                                }
                                else {
                                    jQuery("#rb-margin-"+direction+"-button").css("display", "");
                                }
                            }

                            if (padding > 0) {
                                jQuery("#rb-padding-"+direction+"-button").css("display", "none");
                            }
                            else {
                                jQuery("#rb-padding-"+direction+"-button").css("display", "");
                            }

                        }, false);
                        grabber.addEventListener("mouseout", function(event) {
                            jQuery("#rb-margin-"+direction+"-button").css("display", "");
                            jQuery("#rb-padding-"+direction+"-button").css("display", "");
                        }, false)
                        jQuery(grabber).addClass("rb-border");
                    }
                });

                scope.$parent.rbMarginTop = jQuery("#rb-margin-top");
                scope.$parent.rbMarginLeft = jQuery("#rb-margin-left");
                scope.$parent.rbMarginRight = jQuery("#rb-margin-right");
                scope.$parent.rbMarginBottom = jQuery("#rb-margin-bottom");

                scope.$parent.rbPaddingTop = jQuery("#rb-padding-top");
                scope.$parent.rbPaddingLeft = jQuery("#rb-padding-left");
                scope.$parent.rbPaddingRight = jQuery("#rb-padding-right");
                scope.$parent.rbPaddingBottom = jQuery("#rb-padding-bottom");

                scope.$parent.rbTop = jQuery("#rb-top");
                scope.$parent.rbLeft = jQuery("#rb-left");
                scope.$parent.rbRight = jQuery("#rb-right");
                scope.$parent.rbBottom = jQuery("#rb-bottom");

                scope.$parent.rbTitleBar = jQuery("#oxygen-resize-box-titlebar");
                scope.$parent.rbParentTitleBar = jQuery("#oxygen-resize-box-parent-titlebar");
            }
        };
    });


/**
 * Testing drag to resize flex children
 * 
 */
CTFrontendBuilder.directive('oxygenResizeFlex', function($timeout,$interval) {

        return {
            restrict: 'AE',
            scope: {
                rOptions: '=',
                rCenteredX: '=',
                rCenteredY: '=',
                rWidth: '=',
                rHeight: '=',
                rFlex: '=',
                rId: '=',
                rGrabber: '@',
                rDisabled: '@',
            },
            link: function(scope, element, attr) {

                var flexBasis = 'flexBasis' in document.documentElement.style ? 'flexBasis' :
                    'webkitFlexBasis' in document.documentElement.style ? 'webkitFlexBasis' :
                    'msFlexPreferredSize' in document.documentElement.style ? 'msFlexPreferredSize' : 'flexBasis';

                var timeoutResizable = $timeout(function() {
                    var position = element.css('position')

                    if ( position == "" || position == "static" ){
                        element.addClass('oxygen-resizable-relative');
                    }
                    
                    $timeout.cancel(timeoutResizable);
                }, 0, false);

                var style = window.getComputedStyle(element[0], null),
                    w,
                    h,
                    dir = scope.rOptions || ['right'],
                    vx = scope.rCenteredX ? 2 : 1, // if centered double velocity
                    vy = scope.rCenteredY ? 2 : 1, // if centered double velocity
                    inner = scope.rGrabber ? scope.rGrabber : '<span></span>',
                    start,
                    dragDir,
                    axis,
                    info = {};

                var getClientX = function(e) {
                    return e.touches ? e.touches[0].clientX : e.clientX;
                };

                var getClientY = function(e) {
                    return e.touches ? e.touches[0].clientY : e.clientY;
                };

                var dragging = function(e) {
                    var offset = axis === 'x' ? start - getClientX(e) : start - getClientY(e);

                    switch(dragDir) {

                        case 'right':
                            var newWidth = ( width - (offset * vx) > 0 ) ? width - (offset * vx) : 0;
                            scope.$parent.setOptionModel('flex-basis',newWidth+'px',scope.rId);
                            console.log(width, newWidth);
                            break;

                    }
                };
                var dragEnd = function(e) {
                    scope.$apply();
                    document.removeEventListener('mouseup', dragEnd, false);
                    document.removeEventListener('mousemove', dragging, false);
                    document.removeEventListener('touchend', dragEnd, false);
                    document.removeEventListener('touchmove', dragging, false);
                    element.removeClass('oxygen-no-transition');
                };
                var dragStart = function(e, direction) {
                    dragDir = direction;
                    axis = ( dragDir.indexOf('left') >= 0 || dragDir.indexOf('right') >= 0 ) ? 'x' : 'y';
                    start = axis === 'x' ? getClientX(e) : getClientY(e);
                    //w = parseInt(style.getPropertyValue('width'));
                    //h = parseInt(style.getPropertyValue('height'));

                    width = parseInt(scope.$parent.getOption('width')) || parseInt(style.getPropertyValue('width'));

                    //prevent transition while dragging
                    element.addClass('oxygen-no-transition');

                    document.addEventListener('mouseup', dragEnd, false);
                    document.addEventListener('mousemove', dragging, false);
                    document.addEventListener('touchend', dragEnd, false);
                    document.addEventListener('touchmove', dragging, false);

                    // Disable highlighting while dragging
                    if(e.stopPropagation) e.stopPropagation();
                    if(e.preventDefault) e.preventDefault();
                    e.cancelBubble = true;
                    e.returnValue = false;

                    scope.$apply();
                };

                dir.forEach(function (direction) {
                    var grabber = document.createElement('div');

                    grabber.setAttribute('class', 'rg rg-' + direction);
                    grabber.innerHTML = inner;
                    element.append(grabber);
                    grabber.ondragstart = function() { return false; };

                    var down = function(e) {
                        var disabled = (scope.rDisabled === 'true');
                        if (!disabled && (e.which === 1 || e.touches)) {
                            // left mouse click or touch screen
                            dragStart(e, direction);
                        }
                    };
                    grabber.addEventListener('mousedown', down, false);
                    grabber.addEventListener('touchstart', down, false);
                });
            }
        };
    });

CTFrontendBuilder.directive('oxyimageonload', function() {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            element.bind('load', function() {
                scope.adjustResizeBox();
            });
        }
    };
})

/**
 * Taken from http://www.myersdaily.org/joseph/javascript/md5-text.html
 *
 */

function oxygenMD5(str) {

    function md5cycle(x, k) {
        var a = x[0],
            b = x[1],
            c = x[2],
            d = x[3];

        a = ff(a, b, c, d, k[0], 7, -680876936);
        d = ff(d, a, b, c, k[1], 12, -389564586);
        c = ff(c, d, a, b, k[2], 17, 606105819);
        b = ff(b, c, d, a, k[3], 22, -1044525330);
        a = ff(a, b, c, d, k[4], 7, -176418897);
        d = ff(d, a, b, c, k[5], 12, 1200080426);
        c = ff(c, d, a, b, k[6], 17, -1473231341);
        b = ff(b, c, d, a, k[7], 22, -45705983);
        a = ff(a, b, c, d, k[8], 7, 1770035416);
        d = ff(d, a, b, c, k[9], 12, -1958414417);
        c = ff(c, d, a, b, k[10], 17, -42063);
        b = ff(b, c, d, a, k[11], 22, -1990404162);
        a = ff(a, b, c, d, k[12], 7, 1804603682);
        d = ff(d, a, b, c, k[13], 12, -40341101);
        c = ff(c, d, a, b, k[14], 17, -1502002290);
        b = ff(b, c, d, a, k[15], 22, 1236535329);

        a = gg(a, b, c, d, k[1], 5, -165796510);
        d = gg(d, a, b, c, k[6], 9, -1069501632);
        c = gg(c, d, a, b, k[11], 14, 643717713);
        b = gg(b, c, d, a, k[0], 20, -373897302);
        a = gg(a, b, c, d, k[5], 5, -701558691);
        d = gg(d, a, b, c, k[10], 9, 38016083);
        c = gg(c, d, a, b, k[15], 14, -660478335);
        b = gg(b, c, d, a, k[4], 20, -405537848);
        a = gg(a, b, c, d, k[9], 5, 568446438);
        d = gg(d, a, b, c, k[14], 9, -1019803690);
        c = gg(c, d, a, b, k[3], 14, -187363961);
        b = gg(b, c, d, a, k[8], 20, 1163531501);
        a = gg(a, b, c, d, k[13], 5, -1444681467);
        d = gg(d, a, b, c, k[2], 9, -51403784);
        c = gg(c, d, a, b, k[7], 14, 1735328473);
        b = gg(b, c, d, a, k[12], 20, -1926607734);

        a = hh(a, b, c, d, k[5], 4, -378558);
        d = hh(d, a, b, c, k[8], 11, -2022574463);
        c = hh(c, d, a, b, k[11], 16, 1839030562);
        b = hh(b, c, d, a, k[14], 23, -35309556);
        a = hh(a, b, c, d, k[1], 4, -1530992060);
        d = hh(d, a, b, c, k[4], 11, 1272893353);
        c = hh(c, d, a, b, k[7], 16, -155497632);
        b = hh(b, c, d, a, k[10], 23, -1094730640);
        a = hh(a, b, c, d, k[13], 4, 681279174);
        d = hh(d, a, b, c, k[0], 11, -358537222);
        c = hh(c, d, a, b, k[3], 16, -722521979);
        b = hh(b, c, d, a, k[6], 23, 76029189);
        a = hh(a, b, c, d, k[9], 4, -640364487);
        d = hh(d, a, b, c, k[12], 11, -421815835);
        c = hh(c, d, a, b, k[15], 16, 530742520);
        b = hh(b, c, d, a, k[2], 23, -995338651);

        a = ii(a, b, c, d, k[0], 6, -198630844);
        d = ii(d, a, b, c, k[7], 10, 1126891415);
        c = ii(c, d, a, b, k[14], 15, -1416354905);
        b = ii(b, c, d, a, k[5], 21, -57434055);
        a = ii(a, b, c, d, k[12], 6, 1700485571);
        d = ii(d, a, b, c, k[3], 10, -1894986606);
        c = ii(c, d, a, b, k[10], 15, -1051523);
        b = ii(b, c, d, a, k[1], 21, -2054922799);
        a = ii(a, b, c, d, k[8], 6, 1873313359);
        d = ii(d, a, b, c, k[15], 10, -30611744);
        c = ii(c, d, a, b, k[6], 15, -1560198380);
        b = ii(b, c, d, a, k[13], 21, 1309151649);
        a = ii(a, b, c, d, k[4], 6, -145523070);
        d = ii(d, a, b, c, k[11], 10, -1120210379);
        c = ii(c, d, a, b, k[2], 15, 718787259);
        b = ii(b, c, d, a, k[9], 21, -343485551);

        x[0] = add32(a, x[0]);
        x[1] = add32(b, x[1]);
        x[2] = add32(c, x[2]);
        x[3] = add32(d, x[3]);

    }

    function cmn(q, a, b, x, s, t) {
        a = add32(add32(a, q), add32(x, t));
        return add32((a << s) | (a >>> (32 - s)), b);
    }

    function ff(a, b, c, d, x, s, t) {
        return cmn((b & c) | ((~b) & d), a, b, x, s, t);
    }

    function gg(a, b, c, d, x, s, t) {
        return cmn((b & d) | (c & (~d)), a, b, x, s, t);
    }

    function hh(a, b, c, d, x, s, t) {
        return cmn(b ^ c ^ d, a, b, x, s, t);
    }

    function ii(a, b, c, d, x, s, t) {
        return cmn(c ^ (b | (~d)), a, b, x, s, t);
    }

    function md51(s) {
        txt = '';
        var n = s.length,
            state = [1732584193, -271733879, -1732584194, 271733878],
            i;
        for (i = 64; i <= s.length; i += 64) {
            md5cycle(state, md5blk(s.substring(i - 64, i)));
        }
        s = s.substring(i - 64);
        var tail = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for (i = 0; i < s.length; i++)
            tail[i >> 2] |= s.charCodeAt(i) << ((i % 4) << 3);
        tail[i >> 2] |= 0x80 << ((i % 4) << 3);
        if (i > 55) {
            md5cycle(state, tail);
            for (i = 0; i < 16; i++) tail[i] = 0;
        }
        tail[14] = n * 8;
        md5cycle(state, tail);
        return state;
    }

    /* there needs to be support for Unicode here,
     * unless we pretend that we can redefine the MD-5
     * algorithm for multi-byte characters (perhaps
     * by adding every four 16-bit characters and
     * shortening the sum to 32 bits). Otherwise
     * I suggest performing MD-5 as if every character
     * was two bytes--e.g., 0040 0025 = @%--but then
     * how will an ordinary MD-5 sum be matched?
     * There is no way to standardize text to something
     * like UTF-8 before transformation; speed cost is
     * utterly prohibitive. The JavaScript standard
     * itself needs to look at this: it should start
     * providing access to strings as preformed UTF-8
     * 8-bit unsigned value arrays.
     */
    function md5blk(s) { /* I figured global was faster.   */
        var md5blks = [],
            i; /* Andy King said do it this way. */
        for (i = 0; i < 64; i += 4) {
            md5blks[i >> 2] = s.charCodeAt(i) +
                (s.charCodeAt(i + 1) << 8) +
                (s.charCodeAt(i + 2) << 16) +
                (s.charCodeAt(i + 3) << 24);
        }
        return md5blks;
    }

    var hex_chr = '0123456789abcdef'.split('');

    function rhex(n) {
        var s = '',
            j = 0;
        for (; j < 4; j++)
            s += hex_chr[(n >> (j * 8 + 4)) & 0x0F] +
            hex_chr[(n >> (j * 8)) & 0x0F];
        return s;
    }

    function hex(x) {
        for (var i = 0; i < x.length; i++)
            x[i] = rhex(x[i]);
        return x.join('');
    }

    function md5(s) {
        return hex(md51(s));
    }

    /* this function is much faster,
    so if possible we use it. Some IEs
    are the only ones I know of that
    need the idiotic second function,
    generated by an if clause.  */

    function add32(a, b) {
        return (a + b) & 0xFFFFFFFF;
    }

    if (md5('hello') != '5d41402abc4b2a76b9719d911017c592') {
        function add32(x, y) {
            var lsw = (x & 0xFFFF) + (y & 0xFFFF),
                msw = (x >> 16) + (y >> 16) + (lsw >> 16);
            return (msw << 16) | (lsw & 0xFFFF);
        }
    }

    return md5(str);
}