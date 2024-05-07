/**
 * All AJAX requests
 * 
 */

CTFrontendBuilder.controller("ControllerAJAX", function($scope, $parentScope, $http, $timeout) {

    // cache for loaded posts data
    $scope.postsData = [];
    

    $scope.showErrorModal = function(status, message, err, url) {
        var errorMsg = "";

        if(status && status > 0) {
            errorMsg = (typeof(message)==='string'?"<h4>"+message+"</h4>":'')
            +"<p><strong>"+(typeof(err)==='string'?err:'')+" "+status+"</strong></p>"
            +"<p>Your server returned a "+status+" error for the request to "+url+".</p>"
            +"<p><a href='http://oxygenbuilder.com/documentation/troubleshooting/troubleshooting-guide/' style='text-decoration: underline; color: #fff;' target='_blank'>Troubleshooting Guide &raquo;</a></p>";
        }
        else {
            errorMsg = (typeof(message)==='string'?"<h4>"+message+"</h4>":'')
            +"<p><strong>"+(typeof(err)==='string'?err:'')+"</strong></p>";
        }

        $scope.showNoticeModal("<div>"+errorMsg+"</div>");
    }

    /**
     * Send Components Tree and page settings to WordPress 
     * in JSON format to save as post content and meta
     * 
     * @since 0.1
     */

    $scope.savePage = function(autoSave) {

        // removes the "live preview" option from the latest edited modal, if any
        /*
        if( typeof $scope.parentScope.currentModal !== 'undefined' &&
            typeof $scope.component.options[$scope.parentScope.currentModal] !== 'undefined' &&
            $scope.component.options[$scope.parentScope.currentModal].name == "ct_modal" &&
            $scope.component.options[$scope.parentScope.currentModal].model.behavior == "2") {
            $scope.setOptionModel( "behavior", "1", $scope.parentScope.currentModal );
        }*/
        
        if ($scope.haveNotRegisteredElements()) {
            $scope.showNoticeModal("<div>This design contains elements registered by another plugin that is no longer active. Please re-activate the appropriate plugins or remove the missing elements before saving.</div>", "ct-notice");
            return;
        }

        if (!autoSave) {
            $parentScope.showLoadingOverlay("savePage()");
        }

        $parentScope.disableContentEdit();

        $scope.saveAndApplyCodeEditors();

        var params = {
            // CSS classes
            classes : $scope.classes,
            
            // Custom Selectors
            custom_selectors : $scope.customSelectors,
            style_sets : $scope.styleSets,
            style_folders: $scope.styleFolders,

            // Style Sheets
            style_sheets : $scope.styleSheets,            

            // Settings
            page_settings : $scope.pageSettingsMeta,
            global_settings : $scope.globalSettings,

            // Easy Posts templates
            easy_posts_templates: $scope.easyPostsCustomTemplates,
            comments_list_templates: $scope.commentsListCustomTemplates,

            // Typekit fonts list
            typekit_fonts: $scope.typeKitFonts,

            // Global colors
            global_colors: $scope.globalColorSets,

            // Element presets
            element_presets: $scope.elementPresets,

            // last preview URL
            preview: $scope.previewType == 'post' ? $scope.template.postData.permalink : $scope.template.postData.permalink,

            // CodeMirror Theme
            codemirror_theme: $scope.globalCodeMirrorTheme,
            codemirror_wrap: $scope.globalCodeMirrorWrap,
        };

        // save loaded google fonts to cache
        if (!$scope.googleFontsCache) {
            params['google_fonts_cache'] = $scope.googleFontsList;
        }

        // store the activeSelectors state to each of the components in the tree
        angular.forEach($scope.activeSelectors, function(selector, id) {
            $scope.skipChanges = true;
            $scope.findComponentItem($scope.componentsTree.children, id, $scope.updateComponentActiveSelector, selector);
        });
        $scope.watchIntervalCallback();
        $scope.skipChanges = false;

        var data =  { 
            params: params,
            tree: $scope.componentsTree
        }

        // Convert Components Tree to JSON string
        var data = angular.toJson(data);//JSON.stringify(data);

        var params = {
            action : 'ct_save_components_tree',
            post_id : CtBuilderAjax.postId,
            nonce : CtBuilderAjax.nonce,
        };

        if(jQuery('body').hasClass('ct_inner') || jQuery('body', window.parent.document).hasClass('ct_inner')) {
            params['ct_inner'] = true;
        }

        // Send AJAX request
        $http({
            url : $scope.getAJAXRequestURL(),
            method : "POST",
            params : params,
            data : data,
            transformResponse: false,
        })
        .then(function(response) {
            try {
                if (!autoSave) {
                    response = JSON.parse(response.data);
                    //console.log(response);
                    if ( response === 0 ) {
                        $scope.showErrorModal(0, 'YOUR PAGE WAS NOT SAVED BECAUSE YOU ARE NOT LOGGED IN. Open a new browser tab and log back in to WordPress. Then attempt to save the page again.');
                    }
                    else
                    if ( response['post_saved'] == 0 ) {
                        console.log(data);
                        $scope.showErrorModal(0, 'Error occurred while saving. Please contact support.');
                    }
                    else {
                        $scope.allSaved();
                        // update page CSS cache
                        // $scope.updatePageCSS();
                    }
                    $parentScope.hideLoadingOverlay("savePage()");
                }
                else {
                    var response = JSON.parse(data);
                    if ( response['post_saved'] != 0 ) {
                        $scope.allSaved();
                    }
                }
            } 
            catch (err) {
                console.log(data);
                console.log(err);
                if (!autoSave) {
                    $scope.showErrorModal(response.status, 'Error occurred while saving', err);
                }
            }
        })
        .catch(function(data, status, headers, config) {
            console.log(data, status);
            if ( !autoSave ) {
                $parentScope.hideLoadingOverlay("savePage()");
            }
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while saving', response.statusText, response.config.url);
        });
    }

    
    /**
     * Make sure any open code editor changes saved and applied to the design
     * 
     * @since 4.7
     */

    $scope.saveAndApplyCodeEditors = function() {

        if (window.parent.currentCMEditor) {
            if ($scope.stylesheetToEdit) {
                $scope.updateStylesheetCSS(window.parent.currentCMEditor.state.doc.toString());
            }
            else if( $parentScope.isActiveName('ct_code_block') ) {
                switch( window.parent.currentCMEditor.contentAttrs['data-language'] ) {
                    case 'php':
                        $scope.setOptionModel('code-php', window.parent.currentCMEditor.state.doc.toString());
                        $scope.applyCodeBlockPHP();
                    break;
                    case 'css':
                        $scope.setOptionModel('code-css', window.parent.currentCMEditor.state.doc.toString());
                        $scope.applyCodeBlockCSS();
                    break;
                    case 'javascript':
                        $scope.setOptionModel('code-js', window.parent.currentCMEditor.state.doc.toString());
                        $scope.applyCodeBlockJS();
                    break;
                }
            }
            else if ($parentScope.isActiveName('oxy_posts_grid')) {
                switch( window.parent.currentCMEditor.contentAttrs['data-language'] ) {
                    case 'php':
                        $scope.setOptionModel('code-php', window.parent.currentCMEditor.state.doc.toString());
                        break;
                    case 'css':
                        $scope.setOptionModel('code-css', window.parent.currentCMEditor.state.doc.toString());
                        break;
                }
                $scope.renderComponentWithAJAX('oxy_render_easy_posts')
            }
            else if ($parentScope.isActiveName('oxy_comments')) {
                switch( window.parent.currentCMEditor.contentAttrs['data-language'] ) {
                    case 'php':
                        $scope.setOptionModel('code-php', window.parent.currentCMEditor.state.doc.toString());
                        break;
                    case 'css':
                        $scope.setOptionModel('code-css', window.parent.currentCMEditor.state.doc.toString());
                        break;
                }
                $scope.renderComponentWithAJAX('oxy_render_comments_list')
            }
            else {
                switch( window.parent.currentCMEditor.contentAttrs['data-language'] ) {
                    case 'javascript':
                        $scope.setOptionModel('custom-js', window.parent.currentCMEditor.state.doc.toString());
                        $scope.applyComponentJS()
                        break;
                    case 'css':
                        $scope.setOptionModel('custom-css', window.parent.currentCMEditor.state.doc.toString());
                        $scope.applyComponentCSS()
                        break;
                }
            }
        }
        else if (window.parent.mixedCMEditors &&
            window.parent.mixedCMEditors['php'] &&
            window.parent.mixedCMEditors['css'] &&
            window.parent.mixedCMEditors['js']
            ) {
            
            $scope.setOptionModel('code-php',window.parent.mixedCMEditors['php'].state.doc.toString());
            $scope.applyCodeBlockPHP();

            $scope.setOptionModel('code-css', window.parent.mixedCMEditors['css'].state.doc.toString());
            $scope.applyCodeBlockCSS();

            $scope.setOptionModel('code-js',window.parent.mixedCMEditors['js'].state.doc.toString());
            $scope.applyCodeBlockJS();
        }
    }


    /**
     * updates the active Selector into the provided item out of the component tree
     * 
     * @since 0.3.3
     * @author gagan goraya
     */   

    $scope.updateComponentActiveSelector = function(id, item, selector) {

        /**
         * Check if no item found becuase it may be a custom selector
         */

        if (!item) {
            return;
        }

        /**
         * Check if item has no options, i.e. root 
         */

        if (!item.options) {
            return;
        }

        item.options['activeselector'] = selector;
    }


    /**
     * Send single component or Array of same level components 
     * to save as "ct_template" post via AJAX call
     * 
     * @since 0.2.3
     * @author Ilya K.
     */

    $scope.saveComponentAsView = function(key, component, saveToBlock) {

        var params = {
                action : 'ct_save_component_as_view',
                name : $scope.componentizeOptions.name,
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
                block: saveToBlock
            };

        var componentType = saveToBlock ? "Block" : "Re-usable part";

        // component(s) to save
        if ( component.constructor === Array ) {
            var children = component;
        }
        else {
            var children = [component];
        }

        // Send AJAX request
        $http({
            url: $scope.stripURLProtocol(CtBuilderAjax.ajaxUrl),
            method: "POST",
            params: params,
            data: {
                    'id' : 0,
                    'name' : 'root',
                    'depth' : 0,
                    'children': children
                }
        })
        .then(function(response) {
            //console.log(data);
            $parentScope.hideLoadingOverlay("saveComponentAsView()");

            if ( response.data != 0 ) {
                //alert(componentType + " \"" + $scope.componentizeOptions.name + "\" saved successfully.");
                var blockInstructions = saveToBlock ? "Manage Blocks in the WordPress admin at Oxygen &gt; Block Library." : "";
                $scope.showNoticeModal("<div>"+componentType+" '"+$scope.componentizeOptions.name+"' created. " + blockInstructions + "</div>", "ct-notice");
                if(!saveToBlock) $scope.replaceReusablePart(key, response.data);
            } 
            else {
                $scope.showErrorModal(0, 'Error occurred while saving \"' + componentType + '\".');
            }
        })
        .catch(function(response) {
            console.log(response.data, response.status);
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while saving \"' + componentType + '\".', response.statusText, response.config.url);
        });
    }



    /**
     * Get Components Tree JSON via AJAX
     * 
     * @since 0.1.7
     * @author Ilya K.
     */

    $scope.loadComponentsTree = function(callback, postId, hasSection, componentId) {

        if ($scope.log) {
            console.log("loadComponentsTree()", postId, hasSection, componentId);
        }
        
        $parentScope.showLoadingOverlay("loadComponentsTree()");

        // set default post id
        if ( postId === undefined ) {
            postId = CtBuilderAjax.postId;
        }

        var params = {
                action : 'ct_get_components_tree',
                id : postId,
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
            };

        
        if(jQuery('body').hasClass('ct_inner')) {
            params['ct_inner'] = true;
        }

        // Send AJAX request
        $http({
            url : $scope.stripURLProtocol(CtBuilderAjax.ajaxUrl),
            method : "POST",
            params : params,
            transformResponse: false,
        })
        .then(function(response) {
            try {
                var response = JSON.parse(response.data);
                $scope.defaultOptions = response.defaultOptions;
                callback(response.tree, postId, hasSection, componentId);
                if (response['notRegisteredShortcodes']) {
                    var notRegisteredShortcodes = Object.values(response['notRegisteredShortcodes']);
                    $scope.showErrorModal(0, 'Shortcodes present in design but not registered in WordPress: <br/>'+notRegisteredShortcodes.join(', ')+'. <br/><br/>Saving now will result in this design breaking. Please re-activate the plugins that supply the shortcodes listed above before saving this design.');
                }
            } 
            catch (err) {
                console.log(response.data, err);
                $scope.showErrorModal(0, 'Error occurred while loading post: '+postId, err);
            }
            $parentScope.hideLoadingOverlay("loadComponentsTree()");
        })
        .catch(function(response, status, headers, config) {
            console.log(response.data, status);
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while loading post: '+postId, response.statusText, response.config.url);
        });
    }


    $scope.renderInnerContent = function(id, componentName) {

        // if(typeof($scope.template.postData.ID) === 'undefined') {
        //     return;
        // }

        var url = CtBuilderAjax.permalink,
            data = {};
        
        // if archive
        if ($scope.previewType === 'term') {
            data.term = $scope.template.postData.term;
            
            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink) {
                url = CtBuilderAjax.ajaxUrl;
            }
            else {
                url = $scope.template.postData.permalink;
            }
        }

        // if single
        else  {
            data.post = $scope.template.postData;

            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink){
                url = CtBuilderAjax.ajaxUrl;
            }
            else {
                // lets make an ajax call directly to the frontend single
                url = data.post.permalink;
            }
        }

        var params = {
            action : 'ct_render_innercontent',
            post_id : CtBuilderAjax.postId,
            nonce : CtBuilderAjax.nonce,
        };

        // Send AJAX request
        $http({
            url: $scope.stripURLProtocol(url),
            method : "POST",
            params : params,
            transformResponse : false,
        })
        .then(function(response) {
           if(parseInt(response.data) !== 0) {
               var component = $scope.getComponentById(id);
               component.html();
               var wrapper = angular.element('<div>');
               wrapper.html(response.data);
               component.append(wrapper);
               $scope.adjustResizeBox();
           }
           
        })
        .catch(function(response) {
            console.log(response.data, response.status);
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while rendering innercontent', response.statusText, response.config.url);
        });
    }


    $scope.evalCondition = function(id) {

        if(typeof(id) === 'undefined') {
            id = $scope.component.active.id;
        }

        var activeComponent = $scope.getComponentById(id);
        
        var oxyDynamicList;
        oxyDynamicList = activeComponent.closest('.oxy-dynamic-list');

        if(oxyDynamicList.length > 0) {

            var listId = oxyDynamicList.attr('ng-attr-component-id');

            $scope.dynamicListAction(listId, id);
            return;
            
        }

        if(!($scope.component.options[id]['original'] && $scope.component.options[id]['original']['conditions'] && $scope.component.options[id]['original']['conditions'].length > 0)) {
            delete(iframeScope.component.options[id]['model']['conditionsresult']);
            return;
        }
        
        var url = CtBuilderAjax.permalink,
            data = {};

        // if archive
        if ($scope.previewType === 'term') {
            data.term = $scope.template.postData.term;
            
            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink) {
                url = CtBuilderAjax.permalink;
            }
            else {
                url = $scope.template.postData.permalink;
            }
        }

        // if single
        else  {
            data.post = $scope.template.postData;

            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink){
                url = CtBuilderAjax.permalink;
            }
            else {
                // lets make an ajax call directly to the frontend single
                url = data.post.permalink;
            }
        }

        var params = {
            action : 'ct_eval_condition',
            post_id : CtBuilderAjax.postId,
            nonce : CtBuilderAjax.nonce,
        };

        $http({
            url: $scope.stripURLProtocol(url),
            method : "POST",
            params : params,
            data : JSON.stringify($scope.component.options[id]),
            transformResponse : false,
        })
        .then(function(response, status, headers, config) {
            var data = JSON.parse( response.data );
            if(typeof(data['result']) !== 'undefined') {
                $scope.setOptionModel("conditionsresult", data.result, id, $scope.component.options[id].name);            
            }
        })
        .catch(function(response, status, headers, config) {
            console.log(response.data, status);
            $scope.adjustResizeBox();
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while evaluation condition', response.statusText, response.config.url);
        });
    }

    /**
     * 
     * 
     * @since 3.5
     */

    $scope.renderCustomAttributeDynamicData = function(id, name, value) {

        var url = CtBuilderAjax.permalink;

        if ( $scope.template.postData && $scope.template.postData.permalink) {
            url = $scope.template.postData.permalink;
        }

        var nameHasDynamicData  = name.match(/\[oxygen[^\]]*\]/ig)
        var valueHasDynamicData = value.match(/\[oxygen[^\]]*\]/ig)

        var params = {
            action : 'oxy_render_attribute_dyanmic_data',
            post_id : CtBuilderAjax.postId,
            nonce : CtBuilderAjax.nonce,
        };

        if (nameHasDynamicData) {
            var shortcode = name;
        }
        else {
            var shortcode = value;
        }

        // Send AJAX request
        $http({
            url: $scope.stripURLProtocol(url),
            method : "POST",
            params : params,
            data : shortcode,
            transformResponse : false,
        })
        .then(function(response, status, headers, config) {
            
            var parsedDynamicData = response.data;

            if (nameHasDynamicData && valueHasDynamicData) {
                $scope.renderCustomAttributeDynamicData(id, parsedDynamicData, value);
            }

            if (nameHasDynamicData && !valueHasDynamicData) {
                var component = $scope.getComponentById(id)
                if (component.attr) {
                    var regex = RegExp('[^a-z-_]+', 'gi')
                    if (regex.test(parsedDynamicData)) {
                        $scope.showNoticeModal("<div>"+$scope.component.options[id]['nicename']+" Custom Attribute rendered data '"+parsedDynamicData+"' is not a valid attribute <b>name</b></div>")
                    }
                    else {
                        component.attr(parsedDynamicData, value)
                    }
                    console.log(parsedDynamicData, value)
                }
            }

            if (!nameHasDynamicData && valueHasDynamicData) {
                var regex = RegExp('[^a-z-_]+', 'gi')
                if (regex.test(name)) {
                    $scope.showNoticeModal("<div>"+$scope.component.options[id]['nicename']+" Custom Attribute rendered data '"+name+"' is not a valid attribute <b>name</b></div>")
                    return
                }
                var component = $scope.getComponentById(id)
                if (component.attr) {
                    component.attr(name, $scope.replaceSpecialChars(parsedDynamicData))
                    console.log(name, parsedDynamicData)
                }
            }

        })
        .catch(function(response, status, headers, config) {
            console.log(response.data, status);
        })
    }

    /**
     * Get WordPress shortcodes generated HTML
     * 
     * @since 0.2.3
     */

    $scope.renderShortcode = function(id, shortcode, callback, shortcode_data) {

        // clear the elemnt HTML if "dont_render" param set
        if ($scope.component.options[id] && $scope.component.options[id]['original'] && $scope.component.options[id]['original']['dont_render']=='true') {
            var component = $scope.getComponentById(id);
            component.html("");
            return;
        }

        var url = CtBuilderAjax.permalink,
            data = {};

        // if archive
        if ($scope.previewType === 'term') {
            data.term = $scope.template.postData.term;
            
            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink) {
                url = CtBuilderAjax.permalink;
            }
            else {
                url = $scope.template.postData.permalink;
            }
        }

        // if single
        else  {
            data.post = $scope.template.postData;

            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink){
                url = CtBuilderAjax.permalink;
            }
            else {
                // lets make an ajax call directly to the frontend single
                url = data.post.permalink;
            }
        }

        var params = {
            action : 'ct_render_shortcode',
            shortcode_name : shortcode,
            post_id : CtBuilderAjax.postId,
            nonce : CtBuilderAjax.nonce,
        };

        var element = $scope.getComponentById(id);
        
        var repeaterID = false;
       
        
        var repeaterFields = [];

        if(element) {
            var repeater = element.parent().closest('.oxy-dynamic-list');
            while(repeater && repeater.length > 0) {

                repeaterID = parseInt(repeater.attr('ng-attr-component-id'));

                if($scope.component.options[repeaterID]['original']['use_acf_repeater']) {
                    // its an acf repeater, use parent repeater's query instead or default page query
                    repeater = repeater.parent().closest('.oxy-dynamic-list');
                    repeaterFields.push($scope.component.options[repeaterID]['original']['acf_repeater']);
                }
                else if($scope.component.options[repeaterID]['original']['use_metabox_clonable_group']) {
                    // its an acf repeater, use parent repeater's query instead or default page query
                    repeater = repeater.parent().closest('.oxy-dynamic-list');
                    repeaterFields.push($scope.component.options[repeaterID]['original']['metabox_group']);
                } else {
                    repeaterID = parseInt(repeater.attr('ng-attr-component-id'));
                    repeater = false;
                }

            }
        }

        // if it is a child of a repeater, then send the query data along
        
        var queryOptions;
        if(repeaterID && shortcode_data) {
            
            var repeaterOptions = $scope.component.options[repeaterID];

            shortcode_data['queryOptions'] = {
                query_args:repeaterOptions['original']['query_args'],
                wp_query:$scope.component.options[repeaterID]['original']['use_acf_repeater']||$scope.component.options[repeaterID]['original']['use_metabox_clonable_group']?'default':repeaterOptions['original']['wp_query'],
                query_post_ids:repeaterOptions['original']['query_post_ids'],
                query_post_types:repeaterOptions['original']['query_post_types'],
                query_taxonomies_any:repeaterOptions['original']['query_taxonomies_any'],
                query_taxonomies_all:repeaterOptions['original']['query_taxonomies_all'],
                query_authors:repeaterOptions['original']['query_authors'],
                query_order:repeaterOptions['original']['query_order'],
                query_order_by:repeaterOptions['original']['query_order_by'],
                query_all_posts:repeaterOptions['original']['query_all_posts'],
                query_ignore_sticky_posts:repeaterOptions['original']['query_ignore_sticky_posts'],
                query_count:repeaterOptions['original']['query_count'],
                wp_query_advanced:repeaterOptions['original']['wp_query_advanced']
            }

            queryOptions = JSON.stringify(shortcode_data['queryOptions']);

            if(repeaterFields.length > 0) {
                if ($scope.component.options[repeaterID]['original']['use_acf_repeater']) {
                    shortcode_data['acfRepeaterFields'] = repeaterFields.reverse();
                }
                if ($scope.component.options[repeaterID]['original']['use_metabox_clonable_group']) {
                    shortcode_data['metaboxGroupFields'] = repeaterFields.reverse();
                }
                queryOptions += JSON.stringify(shortcode_data['repeaterFields']);
            }

        }

        if(callback) { // a mechanism to use cache of oxygen shortcodes
            $scope.oxygenShortcodesCache = $scope.oxygenShortcodesCache || [];
            if( shortcode_data && 
                shortcode_data['original'] && 
                shortcode_data['original']['full_shortcode'] &&
                shortcode_data['original']['full_shortcode'].indexOf('[oxygen') > -1) 
            {
                var existing = _.findWhere($scope.oxygenShortcodesCache, {id: params.post_id, url: url, full_shortcode: shortcode_data['original']['full_shortcode'], queryOptions: queryOptions })

                if(existing) {
                    callback(shortcode_data.original.full_shortcode, existing.result, existing.scripts);
                    return;
                }

            }
        }


        // Send AJAX request
        $http({
            url: $scope.stripURLProtocol(url),
            method : "POST",
            params : params,
            data : shortcode_data?JSON.stringify(shortcode_data):JSON.stringify($scope.component.options[id]),
            transformResponse : false,
        })
        .then(function(response, status, headers, config) {
            if (response.data || response.data === "") { // shortcode can return blank and it is ok
                
                var component = $scope.getComponentById(id);
                var container = angular.element('<div>');
                container.html(response.data);

                if(callback) { // at the moment, this could only be a callback to render oxy shortcodes inline
                    // lets cache the results first of all
                    $scope.oxygenShortcodesCache = $scope.oxygenShortcodesCache || [];

                    var result = container.find('#ct-shortcode-links-scripts').html(),
                        scripts = container.find('link, script');
                    
                    $scope.oxygenShortcodesCache.push({
                        id: params.post_id, 
                        url: url, 
                        full_shortcode: shortcode_data['original']['full_shortcode'],
                        queryOptions:queryOptions,
                        result: result,
                        scripts: scripts
                    });

                    callback(shortcode_data.original.full_shortcode, result, scripts);
                }
                else { // otherwise, its just a regular wordpress shortcode being taken care of

                    component.html(container.find('#ct-shortcode-links-scripts').html());

                    var body = component.closest('body');

                    // remove any existing links and scripts for the same shortcode component id
                    body.find('link[data-forId="'+id+'"], script[data-forId="'+id+'"]').remove();

                    // also append the links and scripts into the iframe body
                    container.find('link, script').each(function() {
                        body.append(angular.element(this).attr('data-forId', id));
                    })

                    // trigger (document).ready() so some shortcodes may init
                    var timeout = $timeout(function() {
                        jQuery.ready();
                        $timeout.cancel(timeout);
                    }, 1000, false);
                
                }

                // trigger element loaded event
                var event = new Event('oxygen-ajax-element-loaded');
                document.dispatchEvent(event);
            }
            else {
                console.log(data, status);
                if(callback) { // at the moment, this could only be a callback to render oxy shortcodes inline
                    
                    callback(shortcode_data.original.full_shortcode, '');

                }
                $scope.showErrorModal(0 , 'Error occurred while rendering shortcode');
            }

            $scope.adjustResizeBox();
        })
        .catch(function(response, status, headers, config) {
            console.log(response.data, status);
            $scope.adjustResizeBox();
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while rendering shortcode', response.statusText, response.config.url);
        });
    }

    $scope.getRepeaterOptions = function(id) {

        var element = $scope.getComponentById(id),
            repeaterID = false,
            repeaterFields = [];

        if ( element ) {
            var repeater = element.parent().closest('.oxy-dynamic-list');
            while(repeater && repeater.length > 0) {

                repeaterID = parseInt(repeater.attr('ng-attr-component-id'));

                if($scope.component.options[repeaterID]['original']['use_acf_repeater']) {
                    // its an acf repeater, use parent repeater's query instead or default page query
                    repeater = repeater.parent().closest('.oxy-dynamic-list');
                    repeaterFields.push($scope.component.options[repeaterID]['original']['acf_repeater']);
                }
                else if($scope.component.options[repeaterID]['original']['use_metabox_clonable_group']) {
                    // its an acf repeater, use parent repeater's query instead or default page query
                    repeater = repeater.parent().closest('.oxy-dynamic-list');
                    repeaterFields.push($scope.component.options[repeaterID]['original']['metabox_group']);
                } else {
                    repeaterID = parseInt(repeater.attr('ng-attr-component-id'));
                    repeater = false;
                }

            }
        }

        var queryOptions;
        if ( repeaterID ) {
            
            var repeaterOptions = $scope.component.options[repeaterID];

            queryOptions = {
                query_args:repeaterOptions['original']['query_args'],
                wp_query:$scope.component.options[repeaterID]['original']['use_acf_repeater']||$scope.component.options[repeaterID]['original']['use_metabox_clonable_group']?'default':repeaterOptions['original']['wp_query'],
                query_post_ids:repeaterOptions['original']['query_post_ids'],
                query_post_types:repeaterOptions['original']['query_post_types'],
                query_taxonomies_any:repeaterOptions['original']['query_taxonomies_any'],
                query_taxonomies_all:repeaterOptions['original']['query_taxonomies_all'],
                query_authors:repeaterOptions['original']['query_authors'],
                query_order:repeaterOptions['original']['query_order'],
                query_order_by:repeaterOptions['original']['query_order_by'],
                query_all_posts:repeaterOptions['original']['query_all_posts'],
                query_ignore_sticky_posts:repeaterOptions['original']['query_ignore_sticky_posts'],
                query_count:repeaterOptions['original']['query_count'],
                wp_query_advanced:repeaterOptions['original']['wp_query_advanced']
            }

            queryOptions = JSON.stringify(queryOptions);
        }

        return queryOptions;
    }

    $scope.evalConditionsViaAjax = function(conditions, callback) {


        var url = CtBuilderAjax.permalink,
            data = {};

        // if archive
        if ($scope.previewType === 'term') {
            data.term = $scope.template.postData.term;
            
            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink) {
                url = CtBuilderAjax.permalink;
            }
            else {
                url = $scope.template.postData.permalink;
            }
        }

        // if single
        else  {
            data.post = $scope.template.postData;

            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink){
                url = CtBuilderAjax.permalink;
            }
            else {
                // lets make an ajax call directly to the frontend single
                url = data.post.permalink;
            }
        }
        
        var params = {
            action : 'ct_eval_conditions',
            post_id : CtBuilderAjax.postId,
            nonce : CtBuilderAjax.nonce,
        };


        // Send AJAX request
        $http({
            url: $scope.stripURLProtocol(url),
            method : "POST",
            params : params,
            data : JSON.stringify(conditions),
            transformResponse : false,
        })
        .then(function(response, status, headers, config) {
            response = JSON.parse(response.data);
            if(callback) {
                callback(response);
            }
        })
        .catch(function(response, status, headers, config) {
            console.log(response.data, status);
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while evaluating conditions', response.statusText, response.config.url);
        });
    }

    /**
     * Remove warning msg for non-chrome browsers
     * 
     * @since 0.3.4
     * @author gagan goraya
     */

    $scope.removeChromeModal = function(e) {
        
        e.stopPropagation();
        e.preventDefault();
     
        
        if(!jQuery(e.target).hasClass('ct-chrome-modal-bg') && !jQuery(e.target).hasClass('ct-chrome-modal-hide'))
            return;
        
        var params = {
                action : 'ct_remove_chrome_modal',
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
            };

        // Send AJAX request
        $http({
            url : $scope.stripURLProtocol(CtBuilderAjax.ajaxUrl),
            method : "POST",
            params : params,
            transformResponse : false,
        })
        .then(function(response) {
            jQuery('.ct-chrome-modal-bg').remove();
        })
        .catch(function(response) {
            console.log(response.data, response.status);
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while dismissing the notice', response.statusText, response.config.url);
        });
    }

	/**
	 * Get generated HTML from WordPress data
	 *
	 * @since 1.5
	 */

	$scope.renderDataComponent = function(id, component) {

		var url = CtBuilderAjax.permalink,
			data = {};

		// if archive
        if ($scope.previewType === 'term') {
            data.term = $scope.template.postData.term;
            
            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink) {
                url = CtBuilderAjax.ajaxUrl;
            }
            else {
                url = $scope.template.postData.permalink;
            }
        }

        // if single
        else  {
            data.post = $scope.template.postData;

            // if the postData is empty
            if(!$scope.template.postData || !$scope.template.postData.permalink){
                url = CtBuilderAjax.ajaxUrl;
            }
            else {
                // lets make an ajax call directly to the frontend single
                url = data.post.permalink;
            }
        }

		var params = {
			action : 'ct_render_data_component',
			component_name : component,
			post_id : CtBuilderAjax.postId,
			nonce : CtBuilderAjax.nonce,
		};

		// Send AJAX request
		$http({
			url: $scope.stripURLProtocol(url),
			method : "POST",
			params : params,
			data : JSON.stringify($scope.component.options[id]),
			transformResponse : false,
		})
			.then(function(response, status, headers, config) {
                var data = response.data;
				if (data || data === "") {
					var component = $scope.getComponentById(id);
				    switch( params.component_name ){
                        case "ct_data_featured_image":
                            data = JSON.parse( data );
							component[0].src = data.src;
                            break;
						case "ct_data_author_avatar":
							data = JSON.parse( data );
							component[0].src = data.src;
							break;
                        default:
							component.html(data);
                    }
				}
				else {
					console.log(data, response.status);
                    $scope.showErrorModal(0, 'Error occurred while rendering the component');
				}
			})
			.catch(function(response) {
				console.log(response.data, response.status);
			}).then(null, function(response) { 
                $scope.showErrorModal(response.status, 'Error occurred while rendering the component', response.statusText, response.config.url);
            });
	}

    /**
     * Get WordPress widget generated HTML
     * 
     * @since 0.2.3
     */

    $scope.renderWidget = function(id, isForm) {

        // clear the elemnt HTML if "dont_render" param set
        if ($scope.component.options[id]['original']['dont_render']=='true'&&!isForm) {
            var component = $scope.getComponentById(id);
            component.html("");
            return;
        }

        if ($scope.log) {
            console.log("renderWidget()",id,isForm);
        }

        // Convert Components Tree to JSON
        var data = JSON.stringify({"options" : $scope.component.options[id]}),
            url = CtBuilderAjax.ajaxUrl,
            params = {
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
            };

        if (isForm) {
            params.action = 'ct_render_widget_form';
            $parentScope.cleanInsertUI("<span></span>", "#ct-dialog-widget-content");
            $parentScope.showSidebarLoader = true;
        }
        else {
            params.action = 'ct_render_widget';
            $parentScope.showWidgetOverlay(id);
            url = $scope.getAJAXRequestURL();
        }


        // Send AJAX request
        $http({
            url : $scope.stripURLProtocol(url),
            method : "POST",
            params : params,
            data : data,
            transformResponse : false,
        })
        .then(function(response) {
            var component = $scope.getComponentById(id);
            var data = response.data;
            //console.log(data);
            if (data) {
                if (isForm) {
                    var timeout = $timeout(function() {
                        $parentScope.cleanInsertUI("<form id=\"ct-widget-form\" class=\"open\">"+data+"</form>", "#ct-dialog-widget-content");
                        // trigger the 'widget-added' action like in Customizer to support media widgets
                        window.parent.jQuery(window.parent.document).trigger('widget-added', [jQuery("#ct-widget-form",window.parent.document)] );
                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);
                    $parentScope.showSidebarLoader = false;
                } 
                else {
                    // fix for SiteOrigin Google maps widget
                    if ( window.google !== undefined && window.google.maps !== undefined && 
                        $scope.component.options[id]["id"]["id_base"] == "sow-google-map") {
                        delete window.google.maps;
                    }
                    component.html(data);
                    var timeout = $timeout(function() {
                        jQuery.ready();
                        $timeout.cancel(timeout);
                    }, 1000, false);
                    $parentScope.hideWidgetOverlay(id);
                    $scope.adjustResizeBox();
                }
            }
            
            if ((!data || component.text() === '')&&!isForm) {
                
                component.html("<div class='ct-blank-widget'>Widget Content</div>");
                //alert('Error occurred while rendering widget');
            }

            // trigger element loaded event
            var event = new Event('oxygen-ajax-element-loaded');
            document.dispatchEvent(event);
        })
        .catch(function(response) {
            var component = $scope.getComponentById(id);
            component.html("<div class='ct-blank-widget'>Widget Content<div>");
        });
    }


    /**
     * Get WordPress sidebar generated HTML
     * 
     * @since 2.0
     */

    $scope.renderSidebar = function(id, isForm) {

        // Convert Components Tree to JSON
        var data = JSON.stringify({"options" : $scope.component.options[id]}),
            params = {
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
                action : 'ct_render_sidebar'
            };

        // Send AJAX request
        $http({
            url : $scope.stripURLProtocol(CtBuilderAjax.ajaxUrl),
            method : "POST",
            params : params,
            data : data,
            transformResponse : false,
        })
        .then(function(response) {
            var component = $scope.getComponentById(id);
            var data = response.data;
            //console.log(data);
            if (data) {
                component.html(data);
            }
            
            if(!data || component.text() === '') {
                
                component.html("<div class='ct-blank-widget'>Sidebar Content</div>");
                //alert('Error occurred while rendering widget');
            }

            // trigger element loaded event
            var event = new Event('oxygen-ajax-element-loaded');
            document.dispatchEvent(event);
        })
        .catch(function(response) {
            var component = $scope.getComponentById(id);
            component.html("<div class='ct-blank-widget'>Sidebar Content<div>");
        });
    }


    /**
     * Get WordPress widget generated HTML
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.renderNavMenu = function(id) {

        if (undefined===id) {
            id = $scope.component.active.id;
        }

        if ($scope.log) {
            console.log("renderNavMenu()",id);
        }

        $parentScope.showWidgetOverlay(id);

        // Convert Components Tree to JSON
        var data = JSON.stringify({"options" : $scope.component.options[id]}),
            url = $scope.getAJAXRequestURL();
            params = {
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
                action : 'oxy_render_nav_menu'
            };

        // Send AJAX request
        $http({
            url : $scope.stripURLProtocol(url),
            method : "POST",
            params : params,
            data : data,
            transformResponse : false,
        })
        .then(function(response) {
            var component = $scope.getComponentById(id);
            //console.log(data);
            if (response.data) {
                component.html(response.data);
            } else {
                component.html('No menu found');
            }
            
            $scope.adjustResizeBox();
            $parentScope.hideWidgetOverlay(id);
        })
        .catch(function(response) {
            console.log(response.data, response.status);
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while rendering menu', response.statusText, response.config.url);
        });
    }

    $scope.getDynamicDataFromQuery = function(id, models, callback, holder, parentRepeaterID, repeaterFields, componentID) {

        if (undefined===id) {
            id = $scope.component.active.id;
        }

        if ($scope.log) {
            console.log("getDynamicListData()", action, id);
        }

        // render on the fronend page by default
        var url = CtBuilderAjax.permalink;

        if (CtBuilderAjax.oxyTemplate && !CtBuilderAjax.oxyReusable) {
            if($scope.template.postData && $scope.template.postData.permalink) {
                // render on the currently previewing page
                url = $scope.template.postData.permalink+($scope.template.postData.permalink.substr($scope.template.postData.permalink.length -1) == '/'?'':'/');
            }
            else {
                // render on admin-ajax.php if nothing to preview
                $scope.showErrorModal(0, 'Permalink not available for the current post/archive. If it is an archive for a custom post type , make sure that the parameter \'has_archive\' for the post type is set to true.');
                return;
            }
        } else if(CtBuilderAjax.oxyReusable && CtBuilderAjax.ctSiteUrl) {
            url = CtBuilderAjax.ctSiteUrl+'/';
        }
 
        var data = {"options" : $scope.component.options[id], "models": models };

        if(parentRepeaterID) {
            
            var repeaterOptions = $scope.component.options[parentRepeaterID];

            data['queryOptions'] = { // will serve as parent query options, in case the current repeater is acf
                query_args:repeaterOptions['original']['query_args'],
                wp_query:repeaterOptions['original']['wp_query'],
                query_post_ids:repeaterOptions['original']['query_post_ids'],
                query_post_types:repeaterOptions['original']['query_post_types'],
                query_taxonomies_any:repeaterOptions['original']['query_taxonomies_any'],
                query_taxonomies_all:repeaterOptions['original']['query_taxonomies_all'],
                query_authors:repeaterOptions['original']['query_authors'],
                query_order:repeaterOptions['original']['query_order'],
                query_order_by:repeaterOptions['original']['query_order_by'],
                query_all_posts:repeaterOptions['original']['query_all_posts'],
                query_ignore_sticky_posts:repeaterOptions['original']['query_ignore_sticky_posts'],
                query_count:repeaterOptions['original']['query_count'],
                wp_query_advanced:repeaterOptions['original']['wp_query_advanced']
            }

        }

        if(repeaterFields && repeaterFields.length > 0) {
            data['repeaterFields'] = repeaterFields.reverse();
        }

        // Convert Components Tree to JSON
        var data = JSON.stringify(data);

        var params = {
            post_id : CtBuilderAjax.postId,
            nonce : CtBuilderAjax.nonce,
            action : 'oxy_get_dynamic_data_query'
        };
    
        $parentScope.showWidgetOverlay(id);

        // Send AJAX request
        $http({
            url : $scope.stripURLProtocol(url),
            method : "POST",
            params : params,
            data : data,
            transformResponse : false,
        })
        .then(function(response) {
            var data = response.data;
            //console.log(data);
            if (data) {
                data = JSON.parse(data);
                if(callback) {
                    callback(data['results'], holder, data['pagination']?data['pagination']:null);
                }
            } else {
                component.html('No data received');
            }
            
            //$scope.adjustResizeBox();
            $parentScope.hideWidgetOverlay(id);
        })
        .catch(function(response) {
            console.log(response.data, response.status);
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred', response.statusText, response.config.url);
        });
    }


    /**
     * Get generated component HTML by AJAX
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.renderComponentWithAJAX = function(action, id, repeaterListIndex) {

        if (undefined===id) {
            id = $scope.component.active.id;
        }

        var component = $scope.getComponentById(id);
        if (!component) {
            return;
        }

        var parentProductBuilder = component.parents('.oxy-product-builder'),
            productBuilderID = parentProductBuilder.attr("ng-attr-component-id");

        if ($scope.log) {
            console.log("renderComponentWithAJAX()", action, id);
        }

        var options = $scope.component.options[id];

        // if element is inside Product Builder
        if (productBuilderID) {
            var productID = $scope.getOption('oxy-product-builder_product_id', productBuilderID);
            options['product_builder_id'] = productID;
        }

        // Convert Components Tree to JSON
        var data = {
                "options" : options,
                "component" : $scope.findComponentItem($scope.componentsTree.children, id, $scope.getComponentItem),
            };
        var url = $scope.getAJAXRequestURL(),
            params = {
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
                action : action
            };
        

        var element = $scope.getComponentById(id);
        var repeaterID = false;
        var repeaterFields = [];

        if (element) {
            var repeater = element.parent().closest('.oxy-dynamic-list');
            while (repeater && repeater.length > 0) {

                repeaterID = parseInt(repeater.attr('ng-attr-component-id'));

                if ($scope.component.options[repeaterID]['original']['use_acf_repeater']) {
                    // its an acf repeater, use parent repeater's query instead or default page query
                    repeater = repeater.parent().closest('.oxy-dynamic-list');
                    repeaterFields.push($scope.component.options[repeaterID]['original']['acf_repeater']);
                }
                else if ($scope.component.options[repeaterID]['original']['use_metabox_clonable_group']) {
                    // its an acf repeater, use parent repeater's query instead or default page query
                    repeater = repeater.parent().closest('.oxy-dynamic-list');
                    repeaterFields.push($scope.component.options[repeaterID]['original']['metabox_group']);
                } else {
                    repeaterID = parseInt(repeater.attr('ng-attr-component-id'));
                    repeater = false;
                }
            }
        }

        // if it is a child of a repeater, then send the query data along
        if (repeaterID) {
            
            var repeaterOptions = $scope.component.options[repeaterID];

            data['queryOptions'] = {
                query_args:repeaterOptions['original']['query_args'],
                wp_query:$scope.component.options[repeaterID]['original']['use_acf_repeater']||$scope.component.options[repeaterID]['original']['use_metabox_clonable_group']?'default':repeaterOptions['original']['wp_query'],
                query_post_ids:repeaterOptions['original']['query_post_ids'],
                query_post_types:repeaterOptions['original']['query_post_types'],
                query_taxonomies_any:repeaterOptions['original']['query_taxonomies_any'],
                query_taxonomies_all:repeaterOptions['original']['query_taxonomies_all'],
                query_authors:repeaterOptions['original']['query_authors'],
                query_order:repeaterOptions['original']['query_order'],
                query_order_by:repeaterOptions['original']['query_order_by'],
                query_all_posts:repeaterOptions['original']['query_all_posts'],
                query_ignore_sticky_posts:repeaterOptions['original']['query_ignore_sticky_posts'],
                query_count:repeaterOptions['original']['query_count'],
                wp_query_advanced:repeaterOptions['original']['wp_query_advanced']

            }

            if (repeaterFields.length > 0) {
                data['repeaterFields'] = repeaterFields.reverse();
            }
        }

        if (repeaterListIndex !== undefined) {
            data['repeaterListIndex'] = repeaterListIndex;
        }

        $parentScope.showWidgetOverlay(id);

        if (action=='oxy_render_easy_posts') {
            jQuery('.oxygen-easy-posts-ajax-styles-'+id).remove();
        }

        // Send AJAX request
        $http({
            url : $scope.stripURLProtocol(url),
            method : "POST",
            params : params,
            data : JSON.stringify(data),
            transformResponse : false,
        })
        .then(function(response) {

            var component = $scope.getComponentById(id);

            // "ng-attr-component" wrapper element doesn't exist in the DOM, not created yet or corrupted somehow 
            if (!component) {
                $scope.showNoticeModal("<div>"+$scope.component.options[id]['nicename']+" loading error.</div>");
            }
            else
            // element "ng-attr-component" exist and we place response inside of it
            if (response.data) {
                
                // assume element as loaded 
                component.removeClass("oxy-ajax-loading");
                // trigger element loaded event
                var event = new Event('oxygen-ajax-element-loaded');
                document.dispatchEvent(event);
                
                $scope.isRenderingAJAXElement = true;
                // insert loaded HTML
                component.html(response.data);
                $scope.isRenderingAJAXElement = false;

                if (action=='oxy_render_easy_posts') {
                    component.find(".oxygen-easy-posts-ajax-styles-"+id).prependTo('head');
                }

                $scope.outputCSSOptions();

            } else {
                component.html('No data received');
            }

            $scope.adjustResizeBox();
            $parentScope.hideWidgetOverlay(id);
        })
        .catch(function(response) {
            console.log(response.data, response.status);
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while rendering the component', response.statusText, response.config.url);
        });
    }

    /**
     * Get SVG Icon sets
     * 
     * @since 0.2.1
     */

    $scope.loadStylesheets = function() {

        var params = {
                action: 'oxy_get_style_sheets',
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
            };

        // Send AJAX request
        $http({
            url : $scope.stripURLProtocol(CtBuilderAjax.ajaxUrl),
            method : "POST",
            params : params,
            transformResponse : false,
        })
        .then(function(response) {
            //console.log(data);
            try {
                var stylesheets = JSON.parse(response.data);
                $scope.styleSheets = stylesheets;
            } 
            catch (err) {
                console.log(data);console.log(err);
                $scope.showErrorModal(0, 'Error occurred while loading Stylesheets');
            }
        })
        .catch(function(response) {
            console.log(response.data, response.status);
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while loading Stylesheets', response.statusText, response.config.url);
        });
    }


    /**
     * Get SVG Icon sets
     * 
     * @since 0.2.1
     */

    $scope.loadSVGIconSets = function() {

        var params = {
                action: 'ct_get_svg_icon_sets',
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
            };

        // Send AJAX request
        $http({
            url : $scope.stripURLProtocol(CtBuilderAjax.ajaxUrl),
            method : "POST",
            params : params,
            transformResponse : false,
        })
        .then(function(response) {
            //console.log(data);
            try {
                var sets = JSON.parse(response.data);
                // update scope
                $scope.SVGSets = sets;   
                // set first set as current
                $scope.currentSVGSet = Object.keys(sets)[0]; 
            } 
            catch (err) {
                console.log(data);console.log(err);
                $scope.showErrorModal(0, 'Error occurred while loading SVG icons');
            }
        })
        .catch(function(response) {
            console.log(response.data, response.status);
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Error occurred while loading SVG icons', response.statusText, response.config.url);
        });
    }

    /**
     * Get attachment sizes valid for a particular image
     *
     * @since 2.2
     */

    $scope.loadAttachmentSizes = function( attachment_id, callback, id ) {

        var params = {
            action: 'ct_get_attachment_sizes',
            oxy_post_id : CtBuilderAjax.postId,
            oxy_attachment_id : attachment_id,
            nonce : CtBuilderAjax.nonce,
        };

        var repeaterOptions = $scope.getRepeaterOptions(id)

        var imageElement = $scope.getComponentById(id);
        if ( imageElement ) { 
            imageElement.attr('data-oxy-loading-image', "true");
        }

        // Send AJAX request
        $http({
            url : $scope.getAJAXRequestURL(),
            method : "POST",
            params : params,
            data: repeaterOptions,
            transformResponse : false,
        })
            .then(function(response) {
                var data = response.data;
                try {
                    data = JSON.parse(data);
                    if (data.error) {
                        var name = $scope.component.options[id]['nicename'] || "ID: "+id;
                        $scope.unsetOptions(['attachment_height','attachment_width','attachment_url'], id);
                        $scope.showErrorModal(0, data.error + " " + name);
                    }
                    else {
                        callback( data );
                    }
                }
                catch (err) {
                    $scope.unsetOptions(['attachment_height','attachment_width','attachment_url'], id);
                    console.log(data);console.log(err);
                    $scope.showErrorModal(0, 'Error occurred while loading attachment sizes');
                }
                if ( imageElement ) { 
                    imageElement.attr('data-oxy-loading-image', "false");
                }
            })
            .catch(function(response) {
                console.log(response.data, response.status);
            }).then(null, function(response) {
            $scope.showErrorModal(response.status, 'Error occurred while loading attachment sizes', response.statusText, response.config.url);
        });
    }


    /**
     * Load WP Post object (or array of post objects from one term) 
     * 
     * @since 0.2.0
     */

    $scope.loadTemplateData = function(callback, previewPostId, overlay) {
        
        if(!overlay)
            $parentScope.showLoadingOverlay("loadTemplateData()");
        
        var params = {
                action : 'ct_get_template_data',
                template_id : CtBuilderAjax.postId,
                preview_post_id : previewPostId,
                preview_type : $scope.previewType,
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
            };

        // Send AJAX request
        $http({
            url : $scope.stripURLProtocol(CtBuilderAjax.ajaxUrl),
            method : "POST",
            params : params,
            transformResponse : false,
        })
        .then(function(response) {
            //console.log(data);
            try {
                response = JSON.parse(response.data);
                //console.log(response);
                callback(response);
            } 
            catch (err) {
                console.log(response.data);
                console.log(err);
                $scope.showErrorModal(0, 'Failed to load template data', err);
            }
            $parentScope.hideLoadingOverlay("loadTemplateData()");
        })
        .catch(function(response) {
            console.log(response.data, response.status);
            $parentScope.hideLoadingOverlay("loadTemplateData()");
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Failed to load template data', response.statusText, response.config.url);
        });
    }


    /**
     * Load Elements API component's templates HTML and load previewed template data if needed
     * 
     * @since 2.3
     * @author Ilya K.
     */

    $scope.loadComponentsTemplates = function() {
        
        $parentScope.showLoadingOverlay("loadComponentsTemplates()");
        
        var params = {
                action : 'oxy_get_components_templates',
                nonce : CtBuilderAjax.nonce,
                post_id : CtBuilderAjax.postId,
            };

        // Send AJAX request
        $http({
            url : $scope.stripURLProtocol(CtBuilderAjax.ajaxUrl),
            method : "POST",
            params : params,
            transformResponse : false,
        })
        .then(function(response) {
            try {
                var response = JSON.parse(response.data);
                $scope.componentsTemplates = response;
            } 
            catch (err) {
                console.log(response.data);
                console.log(err);
                $scope.showErrorModal(0, 'Failed to load templates', err);
                $parentScope.hideLoadingOverlay("loadComponentsTemplates()");
            }
                
            // template
            if (CtBuilderAjax.ctTemplate) {
                $scope.loadTemplateData($scope.setTemplateData, null, true); // true means that the loading overlay is already shown
            }
            // regular post/page
            else {
                $parentScope.hideLoadingOverlay("loadComponentsTemplates()");
                // update DOM after all components templates has been loaded
                if ($scope.componentsTree.children) {
                    $scope.buildComponentsFromTree($scope.componentsTree.children);

                    var counter = 0;
                    function waitOxygenTree(counter) {
                        counter++;
                        setTimeout(function(){

                            if ( $scope.buildingOxygenTreeCounter > 0 && counter < 180) {
                                // keep waiting tree to be built while buildComponentsFromTree() in progress
                                waitOxygenTree(counter);
                            }
                            else {
                                // do necassary updates only after buildComponentsFromTree() is completed and tree is built
                                $scope.classesCached = false;
                                $scope.outputCSSOptions();
                                // increment id
                                $scope.component.id++;
                            }
                            
                            // buildComponentsFromTree() took over 90s (500ms x 180) probably due to slow AJAX elements loading
                            if ( $scope.buildingOxygenTreeCounter > 0 && counter >= 180) {
                                console.log('Tree building timeout. Styles may not be fully applied and Structure panel is not updated.');
                            }

                        }, 500);
                    }
                    waitOxygenTree(counter);
                }
            }

            // Wait for the tree rebuild
            $scope.waitOxygenTree(function () {
                $scope.$emit('oxygen_components_loaded', true);
            });
        })
        .catch(function(response) {
            console.log(response);
            
            // load post data via AJAX call
            if (CtBuilderAjax.ctTemplate) {
                $scope.loadTemplateData($scope.setTemplateData, null, true); // true means that the loading overlay is already shown
            }
            else {
                $parentScope.hideLoadingOverlay("loadComponentsTemplates()");
                // update DOM
                if ($scope.componentsTree.children) {
                    $scope.buildComponentsFromTree($scope.componentsTree.children);
                    // increment id
                    $scope.component.id++;
                }
            }

            // Wait for the tree rebuild
            $scope.waitOxygenTree(function () {
                $scope.$emit('oxygen_components_loaded', false);
            });
        }).then(null, function(response) { 
            var errorURL = response.config ? response.config.url : "";
            $scope.showErrorModal(response.status, 'Failed to load templates', response.statusText, errorURL);
        });
    }


    /**
     * Load WP Post object
     * 
     * @since 0.2.3
     * @author Ilya K.
     */

    $scope.loadPostData = function(callback, postId, componentId) {

        // if data exists in the cache
        if($scope.postsData[postId]) {
            callback($scope.postsData[postId], componentId);
            return;
        }

        $parentScope.showLoadingOverlay("loadPostData()");


        var params = {
                action : 'ct_get_post_data',
                id : postId,
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
                preview_post_id : $scope.template.postData.ID
            };

        // Send AJAX request
        $http({
            url : $scope.stripURLProtocol(CtBuilderAjax.ajaxUrl),
            method : "POST",
            params : params,
            transformResponse : false,
        })
        .then(function(response) {
            //console.log(data);
            try {
                response = JSON.parse(response.data);
                callback(response, componentId);
                // save in cache
                $scope.postsData[postId] = response;
            } 
            catch (err) {
                console.log(data);console.log(err);
                $scope.showErrorModal(0, 'Failed to load post data. ID: '+postId, err);
            }
            $parentScope.hideLoadingOverlay("loadPostData()");
        })
        .catch(function(response) {
            console.log(response.data, response.status);
            $parentScope.hideLoadingOverlay("loadPostData()");
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Failed to load post data. ID: '+postId, response.statusText, response.config.url);
        });
    }


    /**
     * Load WP Post object
     * 
     * @since 0.2.3
     * @author Ilya K.
     */

    $scope.loadAJAXVars = function(callback) {

        $parentScope.showLoadingOverlay("loadAJAXVars()");

        var params = {
                action : 'oxy_load_ajax_vars',
                post_id : CtBuilderAjax.postId,
                nonce : CtBuilderAjax.nonce,
                preview_post_id : $scope.template.postData.ID
            };

        // Send AJAX request
        $http({
            url : $scope.stripURLProtocol(CtBuilderAjax.permalink),
            method : "POST",
            params : params,
            transformResponse : false,
        })
        .then(function(response) {
            try {
                response = JSON.parse(response.data);
                if (response.adminURL) {
                    response.adminURL = response.adminURL.replace(/&amp;/g, "&")
                }
                CtBuilderAjax = response;
                $scope.ajaxVar = CtBuilderAjax;

                window.parent.history.replaceState({id: response.postId}, null, response.builderLink);

                if (response.builderLink.indexOf("ct_inner=true") > -1) {
                    jQuery("body").addClass("ct_inner");
                    jQuery('body', window.parent.document).addClass('ct_inner')
                }
                else {
                    jQuery("body").removeClass("ct_inner");
                    jQuery('body', window.parent.document).removeClass('ct_inner')
                }
                callback(response);
            } 
            catch (err) {
                console.log(err);
                $scope.showErrorModal(0, 'Failed to load post AJAX vars', err);
            }
            $parentScope.hideLoadingOverlay("loadAJAXVars()");
        })
        .catch(function(response) {
            console.log(response);
            $parentScope.hideLoadingOverlay("loadAJAXVars()");
        }).then(null, function(response) { 
            $scope.showErrorModal(response.status, 'Failed to load AJAX vars', response.statusText, response.config.url);
        });
    }


    /**
     * Send PHP/HTML code block to server to execute
     * 
     * @since 0.3.1
     */

    $scope.execCode = function(code, placholderSelector, callback) {
        
        var url = $scope.getAJAXRequestURL(),
            data = {
                code: $scope.b64EncodeUnicode(code),
                query: CtBuilderAjax.query
            };

        // Convert Components Tree to JSON
        // escape special characters
        /*data.code = data.code.replace(/\n/g, "\\n")
                                      .replace(/\r/g, "\\r")
                                      .replace(/\t/g, "\\t");*/
        data = JSON.stringify(data);

        // Send AJAX request
        $http({
            method: "POST",
            transformResponse: false,
            url: $scope.stripURLProtocol(url),
            params: {
                action: 'ct_exec_code',
                post_id: CtBuilderAjax.postId,
                nonce: CtBuilderAjax.nonce,
            },
            data: data,
        })
        .then(function(response) {

            var data = response.data;
            
            // this one ensures that blank means blank, not spaces
            if(data.trim().length === 0)
                data='';

            // if data is html document. use jquery to extract the content only
            if(data.indexOf('<html') > -1) {
                data = jQuery('<div>').append(data).find('.ct-code-block').html();
            }

            // get rid of any javascript rendered here.
            data = jQuery('<div>').append(data);
            data.find('script').remove();
            data = data.html();

            callback(data, placholderSelector);
        })
        .catch(function(response) {
            console.log(response.data, response.status);
        });
    }

     $scope.getStuffFromSource = function(callback, next) {
        
        if(typeof(next) === 'undefined') {
            setTimeout(function() {
                angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).addClass('oxygen-small-progress');
            }, 100);
        }

        // Send AJAX request
        var params = {
            action: 'ct_new_style_api_call',
            call_type: 'get_stuff_from_source',
            post_id: CtBuilderAjax.postId,
            nonce: CtBuilderAjax.nonce,
        };

        if(parseInt(next) === 0) {
            angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
            return false;
        } else if(!(typeof(next) === 'undefined' || next === null)) {
            params['next'] = next;
        }
        

        $http({
            method: "GET",
            transformResponse: false,
            url: $scope.stripURLProtocol(CtBuilderAjax.ajaxUrl),
            params: params
        })
        .then(function(response) {
            callback(response.data);

        })
        .catch(function(response) {
            console.log(response.data, response.status);
        });
    }

    $scope.getComponentsListFromSource = function(id, name, callback) {
        //$parentScope.showLoadingOverlay("getComponentsListFromSource()");
        setTimeout(function() {
            angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).addClass('oxygen-small-progress');    
        }, 200)
        
        // Send AJAX request
        $http({
            method: "GET",
            transformResponse: false,
            url: $scope.stripURLProtocol(CtBuilderAjax.ajaxUrl),
            params: {
                action: 'ct_new_style_api_call',
                call_type: 'get_items_from_source',
                name: name,
                post_id: CtBuilderAjax.postId,
                nonce: CtBuilderAjax.nonce,
            }
        })
        .then(function(response) {
            var isError = false;
            var data = response.data;

            if(!data || data.trim() == '') {
                isError = true;
            }
            
            if(!isError)
                data = JSON.parse(data);

            if(isError || !data['components']) {
                $scope.showErrorModal(0, 'Items not loaded. '+(data['error']?data['error']:'Try again!'));
                angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
                return;
            }

            angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');

            delete($scope.experimental_components[name]['fresh']);

            $scope.experimental_components[name]['items'] = {};
            // $scope.parallelized_components = $scope.parallelized_components || {};

            // items need to be further classified based on the categories
            _.each(data['components'], function(item) {

                var category = item['category'];
                
                // if(typeof(category) === 'undefined') {
                //     category = 'Other'
                // }

                if(category) {
                    $scope.experimental_components[name]['items'][category] = $scope.experimental_components[name]['items'][category] || {};
                    $scope.experimental_components[name]['items'][category]['slug'] = btoa(category).replace(/=/g, '');
                    $scope.experimental_components[name]['items'][category]['contents'] = $scope.experimental_components[name]['items'][category]['contents'] || [];
                    $scope.experimental_components[name]['items'][category]['contents'].push(item);
                }

            });

            if(CtBuilderAjax.freeVersion) {
                _.each($scope.experimental_components[name]['items'], function(category, index) {
                    
                    var length = Math.round(category.contents.length/10);

                    if(length < 1) {
                        length = 1;
                    }
                    else if(length > 4) {
                        length = 4;
                    }

                    for(var i = 0; i < category.contents.length; i++) {
                        if(i < length) {
                            category.contents[i]['firstFew'] = 1;
                        } else {
                            category.contents[i]['firstFew'] = 0;
                        }
                    }

                });
            }


            $scope.experimental_components[name]['pages'] = $scope.experimental_components[name]['pages'] || [];
            $scope.experimental_components[name]['templates'] = $scope.experimental_components[name]['templates'] || [];

            _.each(data['pages'], function(item) {
                
                var type = item['type'];

                if(type === 'ct_template') {
                   // $scope.parallelized_components['templates'] = $scope.parallelized_components['templates'] || [];
                    $scope.experimental_components[name]['templates'].push(item);

                }
                else {
                    //$scope.parallelized_components['pages'] = $scope.parallelized_components['pages'] || [];                    
                    $scope.experimental_components[name]['pages'].push(item);

                    // $scope.parallelized_components['pages'].push({
                    //     slug: name,
                    //     item: item
                    // })
                }

            });


            if(CtBuilderAjax.freeVersion) {
            
                    
                var length = Math.round($scope.experimental_components[name]['pages'].length/10);

                if(length < 1) {
                    length = 1;
                }
                else if(length > 4) {
                    length = 4;
                }

                for(var i = 0; i < $scope.experimental_components[name]['pages'].length; i++) {
                    if(i < length) {
                        $scope.experimental_components[name]['pages'][i]['firstFew'] = 1;
                    } else {
                        $scope.experimental_components[name]['pages'][i]['firstFew'] = 0;
                    }
                }


                length = Math.round($scope.experimental_components[name]['templates'].length/10);

                if(length < 1) {
                    length = 1;
                }
                else if(length > 4) {
                    length = 4;
                }

                for(var i = 0; i < $scope.experimental_components[name]['templates'].length; i++) {
                    if(i < length) {
                        $scope.experimental_components[name]['templates'][i]['firstFew'] = 1;
                    } else {
                        $scope.experimental_components[name]['templates'][i]['firstFew'] = 0;
                    }
                }

                
            }

            callback(id);
            //$parentScope.hideLoadingOverlay();
            angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
        })
        .catch(function(response) {
            //angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
            console.log(response.data, response.status);
            //$parentScope.hideLoadingOverlay();
            angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
        });
    }

    $scope.getPageFromSource = function(id, source, designSet, callback) {
        $parentScope.showLoadingOverlay("getPageFromSource()");
        //angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).addClass('oxygen-small-progress');
        // Send AJAX request
        $http({
            method: "GET",
            transformResponse: false,
            url: $scope.stripURLProtocol(CtBuilderAjax.ajaxUrl),
            params: {
                action: 'ct_new_style_api_call',
                nonce: CtBuilderAjax.nonce,
                call_type: 'get_page_from_source',
                id: id,
                post_id: CtBuilderAjax.postId,
                source: btoa(source)
            }
        })
        .then(function(response) {
            //angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
            callback(response.data, source, designSet);
            $parentScope.hideLoadingOverlay();
        })
        .catch(function(response) {
            $parentScope.hideLoadingOverlay();
            //angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
            console.log(response.data, response.status);
        });
    }

    $scope.getComponentFromSource = function(id, source, designSet, page, callback) {
        $parentScope.showLoadingOverlay("getComponentFromSource()");
        //angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).addClass('oxygen-small-progress');
        // Send AJAX request
        $http({
            method: "GET",
            transformResponse: false,
            url: $scope.stripURLProtocol(CtBuilderAjax.ajaxUrl),
            params: {
                action: 'ct_new_style_api_call',
                nonce: CtBuilderAjax.nonce,
                post_id: CtBuilderAjax.postId,
                call_type: 'get_component_from_source',
                id: id, 
                page: page,
                source: btoa(source)
            }
        })
        .then(function(response) {
            $parentScope.hideLoadingOverlay();
            //angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
            callback(response.data, false, source, designSet);
        })
        .catch(function(response) {
            $parentScope.hideLoadingOverlay();
            //angular.element('.oxygen-sidebar-breadcrumb', window.parent.document).removeClass('oxygen-small-progress');
            console.log(response.data, response.status);
        });


    }
    
    /**
     * Load element controls with AJAX
     * 
     * @since 3.0
     * @author Ilya K. 
     */

    $scope.loadedControllers = [];
    $scope.loadControlsWithAJAX = function(name) {

        // don't load twice
        if ($scope.loadedControllers[name]) {
            return;
        }
        else {
            $scope.loadedControllers[name] = true;
        }
        
        $parentScope.showSidebarLoader = true;

        var url = CtBuilderAjax.ajaxUrl,
            params = {
                action: "oxy_load_controls_"+name,
                post_id: CtBuilderAjax.postId,
                nonce: CtBuilderAjax.nonce,
            };

        // Send AJAX request
        $http({
            method: "POST",
            url: $scope.stripURLProtocol(url),
            params: params
        })
        .then(function(response) {
            if(response.data){
                $parentScope.compileInsertUI(response.data, "#oxygen-sidebar-control-panel-basic-styles", 0);
            }
            $parentScope.showSidebarLoader = false;
        })
        .catch(function(response) {
            console.log(response.data, response.status);
            $parentScope.showSidebarLoader = false;
        });
    }


    /**
     * Load element controls with AJAX
     * 
     * @since 3.0
     * @author Ilya K. 
     */

    $scope.loadElementsPresets = function(name) {
    
        $scope.elementPresets = {};

        $parentScope.showLoadingOverlay("loadElementsPresets()");

        var url = CtBuilderAjax.ajaxUrl,
            params = {
                action: "oxy_load_elements_presets",
                post_id: CtBuilderAjax.postId,
                nonce: CtBuilderAjax.nonce,
            };

        // Send AJAX request
        $http({
            method: "POST",
            url: url,
            params: params,
        })
        .then(function(response) {
            if (response.data){
                $scope.elementPresets = response.data;
            }
            else {
                console.log("No default presets loaded");
            }
            $parentScope.hideLoadingOverlay("loadElementsPresets()");
        })
        .catch(function(response) {
            console.log(response.data, response.status);
            $parentScope.hideLoadingOverlay("loadElementsPresets()");
        });
    }


    /**
     * Scrape SoundCloud page with wp_remote_get()
     * 
     * @since 2.0
     * @author Ilya K. 
     */

    $scope.getSoundCloudTrackID = function(soundcloudURL) {

        $parentScope.showLoadingOverlay("getSoundCloudTrackID()");
        
        var url = CtBuilderAjax.ajaxUrl,
            params = {
                action: "oxy_get_soundcloud_track_id",
                soundcloud_url: soundcloudURL,
                post_id: CtBuilderAjax.postId,
                nonce: CtBuilderAjax.nonce,
            };

        // Send AJAX request
        $http({
            method: "POST",
            url: $scope.stripURLProtocol(url),
            params: params
        })
        .then(function(response) {
            if(response.data){
                $scope.setOptionModel("soundcloud_track_id",response.data);
            }
            else {
                $scope.showErrorModal(0, 'Error retrieving SoundCloud Track ID. Please check the URL you specified');
            }
            $parentScope.hideLoadingOverlay();
        })
        .catch(function(response) {
            console.log(response.data, response.status);
            $parentScope.hideLoadingOverlay();
        });
    }


    /**
     * Pass user input to autload suggestions for tags, catergories or other lists of options
     * 
     * @since 3.3
     * @author Ilya K. 
     */

    $scope.loadConditionsOptions = function(index, callbackActionName) {

        var id = $scope.component.active.id,
            searchValue = iframeScope.component.options[id]['model']['globalconditions'][index]['searchValue'];

        var url = CtBuilderAjax.ajaxUrl,
            params = {
                post_id: CtBuilderAjax.postId,
                nonce: CtBuilderAjax.nonce,
                action: callbackActionName,
                search_value: searchValue,
            };

        jQuery('.oxygen-select-box-options', $parentScope.oxygenUIElement).css({opacity:'0.5',pointerEvents:'none'});

        // Send AJAX request
        $http({
            method: "POST",
            url: $scope.stripURLProtocol(url),
            params: params
        })
        .then(function(response) {
            $scope.updateConditionOptions(index, response.data, searchValue);
            jQuery('.oxygen-select-box-options', $parentScope.oxygenUIElement).css({opacity:'',pointerEvents:''});
        })
        .catch(function(response) {
            console.log(response.data, response.status);
            jQuery('.oxygen-select-box-options', $parentScope.oxygenUIElement).css({opacity:'',pointerEvents:''});
        });
    }


    /**
     * Pass user input to autload suggestions for tags, catergories or other lists of options
     * 
     * @since 3.3
     * @author Ilya K. 
     */

    $scope.loadEditingList = function() {

        var url = CtBuilderAjax.ajaxUrl,
            params = {
                post_id: CtBuilderAjax.postId,
                nonce: CtBuilderAjax.nonce,
                action: 'oxy_load_editing_list',
                query: $parentScope.currentlyEditingFilter,
            };

        // Send AJAX request
        $http({
            method: "POST",
            url: $scope.stripURLProtocol(url),
            params: params
        })
        .then(function(response) {
            console.log(response)
            $scope.editingList = response.data
            $scope.currentPreview = response.data[0] ? response.data[0].post_title : "";
        })
        .catch(function(response) {
            console.log(response)
        });
    }


    /**
     * Get an URL to make AJAX call to current page or currently previewed page if editing template
     * Fallback to admin-ajax.php
     * 
     * @since 2.1
     * @author Ilya K. 
     */

    $scope.getAJAXRequestURL = function() {

        // assume we edit single post or page
        var url = CtBuilderAjax.permalink;

        // check if currently editing a template
        if (CtBuilderAjax.oxyTemplate) {
            if($scope.template.postData && $scope.template.postData.permalink) {
                // render on the currently previewed page
                url = $scope.addTrailingSlash($scope.template.postData.permalink);
            }
            else if(CtBuilderAjax.oxyReusable) {
                // Reusables dont have posts to preview, and can't use WP's ajaxUrl to save
                url = $scope.addTrailingSlash(CtBuilderAjax.ctSiteUrl) + '?post_id=' + CtBuilderAjax.postId;
            }
            else {
                // render on admin-ajax.php if nothing to preview
                url = CtBuilderAjax.ajaxUrl;
            }
        }

        url = url.replace("https:","").replace("http:","")

        return url;
    }


    /**
     * Helper function
     * 
     * @since 3.3
     * @author Ilya K. 
     */

    $scope.stripURLProtocol = function(url) {
        return  url.replace("https:","").replace("http:","");
    }


    /**
     * Safely add a trailing slash to an URL string
     * 
     * @since 3.0.1
     * @author Ilya K. 
     */

    $scope.addTrailingSlash = function(url) {

        var urlObj = new URL(url);

        // remove any present trailing slashes
        url = url.replace(/\/+$/, "")

        // don't add trailing slash for URLs with params like "/?post_type=my-cpt"
        if ( urlObj.search && urlObj.search.length > 0 ) {
            return url;
        }

        return url+"/";
    }


    /**
     * Send request to mark post as currently editing in Oxygen
     * 
     * @since 3.3
     * @author Ilya K. 
     */

    $scope.setPostEditLockTransient = function(unset) {

        var params = {
            action : 'set_oxygen_edit_post_lock_transient',
            post_id: CtBuilderAjax.postId,
            nonce: CtBuilderAjax.nonce,
        }

        if (undefined !== unset && unset===false) {
            params.action = 'unset_oxygen_edit_post_lock_transient'
        }

        $http({
            method: "POST",
            url: $scope.stripURLProtocol(CtBuilderAjax.ajaxUrl),
            params: params
        })
        .catch(function(data, status, headers, config) {
            console.log(data, status);
        });
    }

    // keep updating transient eacn n seconds
    setInterval(function(){ 
        $scope.setPostEditLockTransient();
    }, 1000 * 10 /* 10 seconds */ );


});
