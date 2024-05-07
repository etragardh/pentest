var CTFrontendBuilder = angular.module('CTFrontendBuilder', ['ngAnimate', "dndLists", 'CTCommonDirectives'])
.config( ['$provide', function ($provide){
    $provide.decorator('$browser', ['$delegate', function ($delegate) {
        $delegate.onUrlChange = function () {};
        $delegate.url = function () { return ""};
        return $delegate;
    }]);
}]);

var iframeScope;
var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
CTFrontendBuilder.controller("MainController", function($scope, $parentScope, $http, $timeout, $window, ctScopeService, $sce) {
    iframeScope = $scope;
    ctScopeService.store('scope', $scope);
    // log
    $scope.log = false;
    
    $scope.dynamicListActions = {
        actions:[],
        editOn:[]
    };

    $scope.dynamicSpanCycleIDs = [];
    $scope.dynamicListTextChanged = null;
    $scope.repeaterIsRendering = false;

    $scope.dynamicListTrees = {
        trees:[],
        forcleanup:[],
        runOnLoad:{},
        runMainOnLoad:[]
    };

    $scope.parentRepeaterHasACF = false;
    $scope.afterComponentsBuiltSignal = null;
    $scope.afterComponentsAddedSignal = null;
    
    $scope.recycleIDs = [];
    
    $scope.dynamicListOptions = {};
    $scope.dynamicListActivatedIndex = null;
    $scope.tempcache = {
        cache: null
    };

    $scope.contentEditableData = {
        original: null,
        beingEdited: null,
    }

    $scope.notRegisteredElements = [];

    $scope.rebuildDOMforRepeater = function(repeaterID) {

        if($scope.repeaterIsRendering === false) {

            $scope.repeaterIsRendering = true;
            var repeater = $scope.getComponentById(repeaterID);
            var container = angular.element('<div>');
            container.addClass('rebuildDOMContainer');
            $scope.rebuildDOM(repeaterID, false, container);

            repeater.hide();
            container.hide();
            container.insertAfter(repeater);
            var temp = angular.element('<div style="position:relative;"></div>').html(repeater.html());
            temp.children('.oxy_repeater_original').hide();
            temp.append('<div class="oxygen-widget-overlay"><i class="fa fa-cog fa-2x fa-spin"></i></div>');

            temp.insertAfter(repeater);
            $scope.$on('oxy-dynamic-list-'+repeaterID, function() {
                temp.remove();
                repeater.replaceWith(container.children());
                container.remove();
                $scope.repeaterIsRendering = false;
            });
        }

    }

    $scope.updateParentRepeater = function(id) {
        
        var component = $scope.getComponentById(id);

        if (!component) {
            return false;
        }

        var oxyList = component.closest('.oxy-dynamic-list');

        if (oxyList.length > 0 && !oxyList.hasClass('oxy-dynamic-list-edit')) {
            $scope.updateRepeaterQuery(parseInt(oxyList.attr('ng-attr-component-id')))
        }
    }

    $scope.updateRepeaterQuery = function(id) {
        if(typeof(id) == 'undefined') {
            id = $scope.component.active.id;
        }
        var repeater = $scope.getComponentById(id);
        // if this repeater has a parent repeater, update that instead
        
        var loopdone = false;

        while(!loopdone && repeater && repeater.length > 0) {

            var parentRepeater = repeater.parent().closest('.oxy-dynamic-list');

            if(parentRepeater.length < 1) {
                
                var repeaterID = parseInt(repeater.attr('ng-attr-component-id'));
                
                $scope.rebuildDOMforRepeater(repeaterID)
                
                loopdone = true;
            } else {
                repeater = parentRepeater;
            }

        }
 
    }

    $scope.fixShortcodes = CtBuilderAjax['fixShortcodes'];
    $scope.fixShortcodesFound = false;
    $scope.latestParent = 0;

    $scope.dynamicListAction = function(instanceId, componentID, doit, virtualTreeItem, data) {
        
        if(typeof(instanceId) === 'undefined') {
            instanceId = $scope.component.active.id;
            $scope.dynamicListActions.actions[instanceId].action(instanceId, 0, true);
        } else {
            $scope.dynamicListActions.actions[instanceId].action(instanceId, componentID, doit, virtualTreeItem, data);
        }
        //$scope.dynamicListOptions.action(instanceId, componentID, doit);
    }

    // $scope.dynamicListEditMode = function(id, $event) {
    //     $event.preventDefault();
    //     $scope.rebuildDOM(id);

    // }

    $scope.isInnerContent = false;
    $scope.selectAncestors      = [];
    $scope.sidebarEditFriendlyName  = false;
    $scope.isDesiableDraggable = false;

    $scope.currentActiveFolder = '';
    $scope.currentActiveStylesheetFolder = '';

    $scope.styleFolderSelected = false;
    $scope.isChrome = isChrome;

    $scope.reusableEditLinks = {};
    $scope.outerTemplateData = {};

    // cache
    $scope.cache = [];

    $scope.iconFilter = {};

    // initial values
    $scope.component = {

        // currently active component
        active : {  
            id : 0,
            name : 'root',
            state : 'original', // element state like 'hover'
            parent: {
                id : null,
                name : ""
            }
        },

        // components counter
        id : 1,

        // all components options
        options: {
            0 : {
                'original' : {},
                'media' : {
                    'original' : {}
                }
            }
        }
    }

    $scope.defaultOptions = [];
    $scope.defaultOptions.all = [];
    

    $scope.addDynamicContent = function(holder, content) {

        iframeScope.addComponent(holder);
        
        var newComponent = {
            id : $scope.component.id, 
            name : "ct_span"
        }

        // set default options first
        $scope.applyComponentDefaultOptions(newComponent.id, "ct_span");
        
        iframeScope.setOptionModel('ct_content', "<span id=\"ct-placeholder-"+newComponent.id+"\"></span>");

        // insert new component to Components Tree
        $scope.findComponentItem($scope.componentsTree.children, $scope.component.active.id, $scope.insertComponentToTree, newComponent);

        // update span options
        $scope.component.options[newComponent.id]["model"]["ct_content"] = content;

        $scope.setOption(newComponent.id, "ct_span", "ct_content");

        $scope.rebuildDOM(newComponent.id);        
    }
    

    /**
     * Build DOM based on Components Tree JSON
     * 
     * @since 0.1
     */
    
    $scope.init = function() {

        $parentScope.$emit('iframe-scope',$scope);
        $parentScope.$apply();

        // ensure that the gradient -> colors param is an array and not an object
        $scope.oxygenGradientColorsToArray(iframeScope.classes);
        $scope.oxygenGradientColorsToArray(iframeScope.customSelectors);

        $scope.loadComponentsTree($scope.builderInit);
        
        // fonts
        $scope.getWebFontsList();
        $scope.updateGlobalSettingsCSS();
        $scope.loadEditingList();

        $scope.loadSVGIconSets();
        
        // setup UI
        $parentScope.builderElement = jQuery("#ct-builder");
        $parentScope.setupUI();

        angular.element(document).ready(function() {
            angular.element('body').on('keyup', function(e) {
                // if text is being edited, do not propagate, in order to prevent it from sliding the unslider
                if(angular.element(e.target).attr('contenteditable')) {
                    e.stopPropagation();
                }
                
            })
        })

        $parentScope.$apply();

        $scope.setupPageLeaveProtection();
        $scope.setupJSErrorNotice();

        $parentScope.adjustViewportContainer();
        $scope.initMedia();
        $scope.outputPageSettingsCSS();
        $scope.updateScriptsSettings();   

        $scope.isInnerContent = jQuery('body').hasClass('ct_inner');
        
        $scope.ajaxVar = CtBuilderAjax;

        // Load Composite Elements
        $scope.getComponentsListFromSource('composite-elements-0','composite-elements', function(){});

        // must be in the end of init()
        $parentScope.$emit('iframe-init',$scope);
        $parentScope.$apply();

        $scope.initDynamicShortcodeData();
    }

    $scope.oxygenGradientColorsToArray = function(selector) {
        
        if (selector instanceof Object) {
            for (key in selector){
                if (selector.hasOwnProperty(key)){
                    
                    if(key == 'colors' && selector[key] instanceof Object) {
                        var newArray = [];
                        for(nkey in selector[key]) {
                            newArray.push(selector[key][nkey]);
                        }
                        selector[key] = newArray;
                    }
                    else
                        $scope.oxygenGradientColorsToArray( selector[key] );  
                }                
            }
        }

    }

    $scope.initDynamicShortcodeData = function() {

        $scope.dynamicShortcodePHP = {
            name: 'PHP Function Return value',
            data: 'phpfunction',
            properties: [
                {
                    name: 'Function Name',
                    data: 'function',
                    type: 'text'
                },
                {
                    name: 'Function Arguments (separated by comma)',
                    data: 'arguments',
                    type: 'text'
                }
            ]
        };

        $scope.dynamicShortcodesCFOptions = {
                        name: 'Custom Field/Meta Options',
                        data: 'meta',
                        properties: [
                            {
                                name: 'Key',
                                data: 'key',
                                type: 'select',
                                options: _.object( _.map(CtBuilderAjax.oxygenMetaKeys, function(item) { return [item, item] }) )
                            },
                            {
                                name: 'custom',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    };

        $scope.dynamicShortcodesContentMode = [
            {
                name: 'Post',
                children: [
                    {
                        name: 'Title',
                        data: 'title',
                        properties: [
                            {
                                name: 'Link',
                                data: 'link',
                                type: 'checkbox',
                                value: 'permalink'
                            },
                        ]
                    },
                    {
                        name: 'Content',
                        data: 'content'
                    },
                    {
                        name: 'Excerpt',
                        data: 'excerpt'
                    },
                    {
                        name: 'Date',
                        data: 'date',
                        properties: [
                            {
                                name: 'Format',
                                data: 'format',
                                type: 'text'
                            }
                        ]
                    },
                    {
                        name: 'Categories, Tags, Taxonomies',
                        data: 'terms',
                        properties: [
                            {
                                name: 'Taxonomy',
                                data: 'taxonomy',
                                type: 'select',
                                options: _.object( _.map(CtBuilderAjax.taxonomies, function(item) { return [item, item] }) )
                            },
                            {
                                name: 'Separator',
                                data: 'separator',
                                type: 'text'
                            }
                        ]
                    },
                    $scope.dynamicShortcodesCFOptions,
                    {
                        name: 'Comments Number',
                        data: 'comments_number',
                        properties: [
                            {
                                name: 'No Comments',
                                data: 'zero',
                                type: 'text'
                            },
                            {
                                name: 'One Comment',
                                data: 'one',
                                type: 'text'
                            },
                            {
                                name: 'Multiple Comments (% is replaced by the number of comments)',
                                data: 'more',
                                type: 'text'
                            },
                            {
                                name: 'Link',
                                data: 'link',
                                type: 'checkbox',
                                value: 'comments_link'
                            },
                        ]
                    }

                ]
            },
            {
                name: 'Featured Image',
                children: [
                    {
                        name: 'Title',
                        data: 'featured_image_title'
                    },
                    {
                        name: 'Caption',
                        data: 'featured_image_caption'
                    },
                    {
                        name: 'Alt',
                        data: 'featured_image_alt'
                    },

                ]
            },
            {
                name: 'Author',
                children: [
                    {
                        name: 'Display Name',
                        data: 'author',
                        properties: [
                            {
                                name: 'Link',
                                data: 'link',
                                type: 'select',
                                options: {
                                    'None': 'none',
                                    'Author Website URL': 'author_website_url',
                                    'Author Posts URL': 'author_posts_url'
                                },
                                nullVal: 'none'
                            },
                        ]
                    },
                    {
                        name: 'Bio',
                        data: 'author_bio'
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'author_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]
            },
            {
                name: 'Current User',
                children: [
                    {
                        name: 'Display Name',
                        data: 'user',
                        properties: [
                            {
                                name: 'Link',
                                data: 'link',
                                type: 'checkbox',
                                value: 'user_website_url'
                            },
                        ]
                    },
                    {
                        name: 'Bio',
                        data: 'user_bio'
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'user_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]
            },
            {
                name: 'Blog Info',
                children: [
                    {
                        name: 'Site Title',
                        data: 'bloginfo',
                        append: 'show=\'name\''
                    },
                    {
                        name: 'Site Tagline',
                        data: 'bloginfo',
                        append: 'show=\'description\''
                    },
                    {
                        name: 'Other',
                        data: 'bloginfo',
                        properties: [
                            {
                                name: 'Show',
                                data: 'show',
                                type: 'select',
                                options: {
                                    'WPURL': 'wpurl',
                                    'URL': 'url',
                                    'Admin e-mail': 'admin_email',
                                    'Charset': 'charset',
                                    'Version': 'version',
                                    'HTML Type': 'html_type',
                                    'Text Direction': 'text_direction',
                                    'Language': 'language',
                                    'Stylesheet URL': 'stylesheet_url',
                                    'Stylesheet Directory': 'stylesheet_directory',
                                    'Template URL': 'template_url',
                                    'Pingback URL': 'pingback_url',
                                    'Atom URL': 'atom_url',
                                    'RDF URL': 'rdf_url',
                                    'RSS URL': 'rss_url',
                                    'RSS2 URL': 'rss2_url',
                                    'Comments Atom URL': 'comments_atom_url',
                                    'Comments RSS2 URL': 'comments_rss2_url'
                                }
                            }
                        ]
                    }
                ]
            },
            {
                name: 'Archive',
                children: [
                    {
                        name: 'Archive Title',
                        data: 'archive_title'
                    },
                    {
                        name: 'Archive Description',
                        data: 'archive_description'
                    }
                ]
            },
            {
                name: 'Advanced',
                children: [
                    $scope.dynamicShortcodePHP,
                    {
                        name: 'Post ID',
                        data: 'id'
                    },
                    {
                        name: 'Post Type',
                        data: 'post_type'
                    },
                    {
                        name: 'Taxonomy Terms',
                        data: 'post_terms',
                        properties: [
                            {
                                name: 'Taxonomy',
                                data: 'taxonomy',
                                type: 'select',
                                options: _.object( _.map(CtBuilderAjax.taxonomies, function(item) { return [item, item] }) )
                            },
                            {
                                name: 'Separator',
                                data: 'separator',
                                type: 'text',
                                value: ', '
                            }
                        ]
                    },
                ]
            }
        ];

        $scope.dynamicShortcodesCustomFieldMode = [
            {
                name: 'Post',
                children: [
                    {
                        name: 'Meta / Custom Field',
                        data: 'meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'select',
                                options: _.object( _.map(CtBuilderAjax.oxygenMetaKeys, function(item) { return [item, item] }) )
                            },
                            {
                                name: 'custom',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]

            },
            {
                name: 'Author',
                children: [
                    {
                        name: 'Meta / Custom Field',
                        data: 'author_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]

            },
            {
                name: 'Current User',
                children: [
                    {
                        name: 'Meta / Custom Field',
                        data: 'user_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]

            },
            {
                name: 'Advanced',
                children: [
                    $scope.dynamicShortcodePHP
                ]
            }

        ];
        
        $scope.dynamicShortcodesLinkMode = [
            {
                name: 'Post',
                children: [
                    {
                        name: 'Permalink',
                        data: 'permalink'
                    },
                    {
                        name: 'Comments Link',
                        data: 'comments_link'
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'select',
                                options: _.object( _.map(CtBuilderAjax.oxygenMetaKeys, function(item) { return [item, item] }) )
                            },
                            {
                                name: 'custom',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]

            },
            {
                name: 'Featured Image',
                children: [
                    {
                        name: 'Featured Image URL',
                        data: 'featured_image'
                    }
                ]

            },
            {
                name: 'Author',
                children: [
                    {
                        name: 'Author Website URL',
                        data: 'author_website_url'
                    },
                    {
                        name: 'Author Posts URL',
                        data: 'author_posts_url'
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'author_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]

            },
            {
                name: 'Current User',
                children: [
                    {
                        name: 'User Website URL',
                        data: 'user_website_url'
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'user_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]

            },
            {
                name: 'Advanced',
                children: [
                    $scope.dynamicShortcodePHP
                ]
            }

        ];

        $scope.dynamicShortcodesImageIDMode = [
            {
                name: 'Post',
                children: [
                    {
                        name: 'Featured Image',
                        data: 'featured_image_id',
                        properties: []
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'select',
                                options: _.object( _.map(CtBuilderAjax.oxygenMetaKeys, function(item) { return [item, item] }) )
                            },
                            {
                                name: 'custom',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]
            },
            {
                name: 'Author',
                children: [
                    {
                        name: 'Meta / Custom Field',
                        data: 'author_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]
            },
            {
                name: 'User',
                children: [
                    {
                        name: 'Meta / Custom Field',
                        data: 'user_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]
            },
            {
                name: 'Advanced',
                children: [
                    $scope.dynamicShortcodePHP
                ]
            }
        ];

        $scope.dynamicShortcodesImageMode = [
            {
                name: 'Post',
                children: [
                    {
                        name: 'Featured Image',
                        data: 'featured_image',
                        properties: [
                            {
                                name: 'Size',
                                data: 'size',
                                type: 'select',
                                options: {
                                    'Thumbnail':'thumbnail',
                                    'Medium':'medium',
                                    'Medium Large':'medium_large',
                                    'Large':'large'
                                },
                                change: "scope.dynamicDataModel.width = ''; scope.dynamicDataModel.height = ''"
                            },
                            {
                                name: 'or',
                                type: 'label'
                            },
                            {
                                name: 'Width',
                                data: 'width',
                                type: 'text',
                                helper: true,
                                change: "scope.dynamicDataModel.size = scope.dynamicDataModel.width+'x'+scope.dynamicDataModel.height"
                            },
                            {
                                name: 'Height',
                                data: 'height',
                                type: 'text',
                                helper: true,
                                change: "scope.dynamicDataModel.size = scope.dynamicDataModel.width+'x'+scope.dynamicDataModel.height"
                            },
                            {
                                type: 'break'
                            },
                            {
                                name: 'Default',
                                data: 'default',
                                type: 'text',
                            }
                        ]
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'select',
                                options: _.object( _.map(CtBuilderAjax.oxygenMetaKeys, function(item) { return [item, item] }) )
                            },
                            {
                                name: 'custom',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]
            },
            {
                name: 'Author',
                children: [
                    {
                        name: 'Author Pic',
                        data: 'author_pic',
                        properties: [
                            {
                                name: 'Size',
                                data: 'size',
                                type: 'select',
                                options: {
                                    '400': '400',
                                    '200': '200'
                                }
                            },
                        ]
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'author_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]
            },
            {
                name: 'User',
                children: [
                    {
                        name: 'User Pic',
                        data: 'user_pic',
                        properties: [
                            {
                                name: 'Size',
                                data: 'size',
                                type: 'select',
                                options: {
                                    'Default': 'Default',
                                    '200': '200'
                                },
                                nullval: 'Default'
                            },
                        ]
                    },
                    {
                        name: 'Meta / Custom Field',
                        data: 'user_meta',
                        properties: [
                            {
                                name: 'Meta Key',
                                data: 'key',
                                type: 'text'
                            }
                        ]
                    }
                ]
            },
            {
                name: 'Advanced',
                children: [
                    $scope.dynamicShortcodePHP
                ]
            }
        ];

        // Merge the custom dynamic data elements with the built-in ones
        for(var i = custom_dynamic_data.data.length-1; i >=0; i--) {
            var modeArray;
            var custom_dyn_data = custom_dynamic_data.data[i];
            var mode = custom_dyn_data.mode.toLowerCase();
            switch( mode ) {
                case 'content':
                    modeArray = $scope.dynamicShortcodesContentMode;
                    break;
                case 'custom-field':
                    modeArray = $scope.dynamicShortcodesCustomFieldMode;
                    break;
                case 'link':
                    modeArray = $scope.dynamicShortcodesLinkMode;
                    break;
                case 'image':
                    modeArray = $scope.dynamicShortcodesImageMode;
                    break;
                case 'image-id':
                    modeArray = $scope.dynamicShortcodesImageIDMode;
                    break;
            }
            var parent;
            if( modeArray.some( function( el ){ return el.name == custom_dyn_data.position } ) ) {
                parent = modeArray[ modeArray.map( function(e) { return e.name } ).indexOf( custom_dyn_data.position ) ];

            } else {
                parent = { name: custom_dyn_data.position, children: [] };
                modeArray.push( parent );
            }

            custom_dyn_data.data = "custom_" + custom_dyn_data.data;

            parent.children.push( custom_dyn_data /*newElement*/ );
        }

    }

    /**
     * Set cursor to the end of contenteditbale. Taken from http://stackoverflow.com/a/3866442/2198798
     *
     * @since 1.1.2
     */

    $scope.setEndOfContenteditable = function(contentEditableElement) {
        var range,selection;
        if(document.createRange)//Firefox, Chrome, Opera, Safari, IE 9+
        {
            range = document.createRange();//Create a range (a range is a like the selection but invisible)
            range.selectNodeContents(contentEditableElement);//Select the entire contents of the element with the range
            
            selection = window.getSelection();//get the selection object (allows you to change selection)
            selection.removeAllRanges();//remove any selections already made
            selection.addRange(range);//make the range you have just created the visible selection

            range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
        }
        else if(document.selection)//IE 8 and lower
        { 
            range = document.body.createTextRange();//Create a range (a range is a like the selection but invisible)
            range.moveToElementText(contentEditableElement);//Select the entire contents of the element with the range
            range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
            range.select();//Select the range (make it the visible selection
        }
    }

    /**
     * callback for inserting dynamic data to advanced query values
     * @author Gagan S Goraya
     * @since 3.6
     */
    $scope.updateDynamicQueryParams = function(text) {
        setTimeout(function() {
            $scope.setOption($scope.component.active.id, 'oxy_dynamic_list', 'wp_query_advanced');
        }, 100);
    }

    $scope.insertAtCursor = function(text) {

        var sel, range, html;

        text=text.replace(/\"/ig, "'");

        if (window.getSelection) {
            sel = window.getSelection();
            if (sel.getRangeAt && sel.rangeCount) {
                range = sel.getRangeAt(0);
                range.deleteContents();
                range.insertNode( document.createTextNode(text) );
            }
        } else if (document.selection && document.selection.createRange) {
            document.selection.createRange().text = text;
        }

        angular.element(window.getSelection().focusNode).trigger('input');
        // In Safari the actual DOM element located in parent node, trigger a change on it as well
        angular.element(window.getSelection().focusNode.parentNode).trigger('input');
    }

    $scope.insertShortcodeToMapAddress = function(text) {
        text=text.replace(/\"/ig, "'");
        var id = $scope.component.active.id;
        $scope.setOptionModel('map_address', text, id);

        $scope.parseMapShortcode(id);
    }

    $scope.parseMapShortcode = function(id) {
        
        if(typeof(id) === 'undefined') {
            id = $scope.component.active.id;
        }

        var map_address = $scope.getOption('map_address', id);

        var callback = function(contents) {
            var element = $scope.getComponentById(id);
            
            var iframe = element.find('iframe');
            if(iframe.length > 0) {
                iframe.attr('src', iframe.attr('src').replace(/q=[^\&]*/, 'q='+contents.trim()));
            }
        }

        $scope.applyShortcodeResults(id, map_address, callback);
    }

    $scope.insertShortcodeToBackground = function(text) {

        text=text.replace(/\"/ig, "'");
        var id = $scope.component.active.id;
        
        $scope.setOptionModel('background-image', text, id);

        var component = $scope.getComponentById(id);

        var oxyList = component.closest('.oxy-dynamic-list');
            
        if(oxyList.length > 0 && !oxyList.hasClass('oxy-dynamic-list-edit')) {
            $scope.updateRepeaterQuery(parseInt(oxyList.attr('ng-attr-component-id')));
        }

        //$scope.parseBackgroundShortcode(id);
    }

    $scope.parseBackgroundShortcode = function(id) {

        if(typeof(id) === 'undefined') {
            id = $scope.component.active.id;
        }

        var background = $scope.getOption('background', id);

        var callback = function(contents) {

            var component = $scope.findComponentItem($scope.componentsTree.children, id, $scope.getComponentItem);

            var classes = component.options.classes;

            var element = $scope.getComponentById(id);

            if($scope.isEditing('class') && (!component.options.original || !component.options.original.background)) {
                var foundActiveClass = false;
                var propertyTrumped = false;

                _.each(classes, function(item) {
                   
                    if(!foundActiveClass && item === $scope.currentClass) {
                        foundActiveClass = true;
                    }
                    else if(foundActiveClass) {
                        if(iframeScope.classes[item].original.background) {
                            propertyTrumped = true;
                        }
                    }
                });

                if(!propertyTrumped) {
                    element.css('background-image', 'url('+contents.trim()+')');
                }
            }
            
            if(
                $scope.isEditing('id')) {
                element.css('background-image', 'url('+contents.trim()+')');
            }
            
            
        }

        $scope.applyShortcodeResults(id, background, callback);
    }

    $scope.insertShortcodeToImageAlt = function(text) {
        text=text.replace(/\"/ig, "'");
        var id = $scope.component.active.id;
        $scope.setOptionModel('alt', text, id);
    }

    $scope.insertShortcodeToImageID = function(text) {
        text=text.replace(/\"/ig, "'");
        var id = $scope.component.active.id;
        $scope.setOptionModel('attachment_id', text, id);
        $scope.component.options[id].sizes = null;
        $scope.component.options[id].sizes_requested = false;
        
        // set image size to "full" if not set already
        var item = $scope.findComponentItem($scope.componentsTree.children, id, function(id, item) {
            return item;
        });
        if (item.options && item.options.original) {
            if (!item.options.original.attachment_size) {
                $scope.setOptionModel('attachment_size', "full", id);
            }
        }

        var component = $scope.getComponentById(id);
        if (component) {
            var oxyList = component.closest('.oxy-dynamic-list');
        }
        if(oxyList.length > 0 && !oxyList.hasClass('oxy-dynamic-list-edit')) {
            $scope.updateRepeaterQuery(parseInt(oxyList.attr('ng-attr-component-id')));
        }
        else {
            $scope.processImageSizes();
        }
    }

    $scope.insertShortcodeToUrl = function(text) {
        text=text.replace(/\"/ig, "'");
        var id = $scope.component.active.id;
        $scope.setOptionModel('url', text, id);
    }

    $scope.insertShortcodeToSrc = function(text) {
        text=text.replace(/\"/ig, "'");
        var id = $scope.component.active.id;
        $scope.setOptionModel('src', text, id);   
    }

    $scope.insertDynamicDataShortcode = function(text,optionName) {
        text=text.replace(/\"/ig, "'");
        var id = $scope.component.active.id;
        $scope.setOptionModel(optionName, text, id);   
    }

    $scope.insertShortcodeToImage = function(text) {

        var id = $scope.component.active.id;

        text=text.replace(/\"/ig, "'");
        $scope.setOptionModel('src', text, id);
   
        var timeout = $timeout(function() {
            $scope.parseImageShortcode();
            // cancel timeout
            $timeout.cancel(timeout);
        }, 0, false);  

        
        var component = $scope.getComponentById(id);

        var oxyList = component.closest('.oxy-dynamic-list');
            
        if(oxyList.length > 0 && !oxyList.hasClass('oxy-dynamic-list-edit')) {
            $scope.updateRepeaterQuery(parseInt(oxyList.attr('ng-attr-component-id')));
        }

    }

    $scope.parseImageShortcode = function(id) {
        
        if(typeof(id) === 'undefined') {
            id = $scope.component.active.id;
        }

        var src = $scope.getOption('src', id);

        var callback = function(contents) {
            var element = $scope.getComponentById(id);
            element.attr('src', contents.trim());
        }

        $scope.applyShortcodeResults(id, src, callback);

    }

    $scope.applyShortcodeResults = function(id, attribute, callback, param) {
        var matches = [];
        var matchCount = 0;

        var replacer = function(shortcode, contents) {

            matchCount++;

            attribute = attribute.replace(shortcode, contents);

            if(matchCount == matches.length) {
                if(!contents) {
                    contents = '';
                }

                callback(contents, param);
            }
        }

        matches = attribute.match(/\[oxygen[^\]]*\]/ig);

        for(key in matches) {

            var shortcode_data ={
                original: {
                    full_shortcode: matches[key].trim()
                }
            }

            $scope.renderShortcode(id, 'ct_shortcode', replacer, shortcode_data);
        }


    }


    /**
     * Parse YouTube/Vimeo page urls to embeddable links
     * 
     * @since 2.0
     * @author Gagan
     */

    $scope.getYoutubeVimeoEmbedUrl = function(url) {

        if(typeof(url) === 'undefined' || url.trim() === '') {
            return url;
        }
        var regexp = /(youtube\.com|youtu\.be|vimeo\.com)\/(watch\?v\=)?(.*)/;
        var matches = url.match(regexp)

        if(matches[1] && matches[3] && matches[3].indexOf('/') === -1) {
            if(matches[1] == 'youtube.com' || matches[1] == 'youtu.be') {
                if (matches[3].split('&').length>1) {
                    matches[3] = matches[3].split('&')[0]
                }
                return 'https://www.youtube.com/embed/' + matches[3];
            }
            else if(matches[1] == 'vimeo.com') {
                return 'https://player.vimeo.com/video/' + matches[3];
            }
        }
        else {
            return url;
        }
    }


    /**
     * Build DOM from Components Tree
     * 
     * @since 0.1
     */

    $scope.buildingOxygenTreeCounter = 0;

    $scope.buildComponentsFromTree = function(componentsTree, id, reorder, domNode, primaryIndex, forceZeroIndex) {
        
        if ($scope.log) {
            //console.log("buildComponentsFromTree()", componentsTree, id, reorder, domNode, primaryIndex);
        }

        // handle root
        if ( 0 == id ) {
            var element = $scope.getComponentById(id, domNode);
            element.empty();
            element = null;
            $scope.buildComponentsFromTree($scope.componentsTree.children, null, null, domNode);

            return false;
        }


        var stopBuilding = false,
            builtinOffset = 0;

        //for(var index in componentsTree) { 
            //if (componentsTree.hasOwnProperty(index)) {
                //var item = componentsTree[index];

        //for (index = 0; index < componentsTree.length; ++index) {
            //var item = componentsTree[index];

        angular.forEach(componentsTree, function(item, index) {

            if ( !stopBuilding ) {

                if (item.options.oxy_builtin) {
                    builtinOffset++;
                }

                var key = item.id;

                if (item.repeaterListIndex != undefined) {
                    $scope.repeaterListCurrentIndex = item.repeaterListIndex;
                }

                // if we only need to rebuild specific node
                if ( id ) {
                    
                    // found node
                    if ( key == id ) {
                        if(!reorder && !domNode)
                            $scope.removeComponentFromDOM(id);
                        stopBuilding = true;
                    } 
                    else {
                        // go deeper to find
                        if ( item.children ) {
                            $scope.buildComponentsFromTree(item.children, id, reorder, domNode, primaryIndex, forceZeroIndex);
                        }
                        // stop from building
                        return false;
                    }
                }
                
                /**
                 * Apply all options
                 */
                
                // set default options first
                $scope.applyComponentDefaultOptions(key, item.name, item);

                // set saved 'original' options
                $scope.applyComponentSavedOptions(key, item);

                // add saved classes
                if ( item.options.classes ) {
                    $scope.componentsClasses[key] = angular.copy(item.options.classes);
                }
                
                if(!item.options.nicename) {
                    item.options.nicename = $scope.calcDefaultComponentTitle(item);
                }

                if(!$scope.component.options[item.id]['nicename']) {
                    $scope.component.options[item.id]['nicename'] = item.options.nicename;
                }

                // apply model
                $scope.applyModelOptions(key, item.name);


                if ( item.name == "oxy_dynamic_list" && typeof($scope.dynamicListActions.actions[item.id]) === 'undefined') {
                    $scope.dynamicListActions.actions[item.id] = {};
                }


                /**
                 * Start building
                 */
                
                var type = "";

                // TODO: this is stupid and needs to be changed
                if ( item.options.ct_shortcode ) {
                    type = "shortcode";
                }

                if ( item.options.ct_widget ) {
                    type = "widget";
                }

				if ( item.options.ct_data ) {
					type = "data";
				}

                if ( item.options.ct_sidebar ) {
                    type = "sidebar";
                }

                if ( item.options.ct_nav_menu ) {
                    type = "nav_menu";
                }

                if ( item.options.oxy_builtin ) {
                    type = "builtin";
                }

                // Check if parent id fixer is enabled
                if ($scope.fixShortcodes) {
                    if ( $scope.latestParent !== undefined &&
                        ($scope.latestParent !== item.options.ct_parent)
                        ) {
                        console.log(item.name+" #"+item.id+" parentID changed from: "+item.options.ct_parent+" to: "+$scope.latestParent);
                        item.options.ct_parent = $scope.latestParent;
                        $scope.fixShortcodesFound = true;
                    };
                }

                var componentParent     = $scope.getComponentById(item.options.ct_parent, domNode),
                    componentTemplate   = $scope.getComponentTemplate(item.name, key, type, domNode?parseInt(domNode.attr('data-for-id')):false);
                
                if(item.id > 100000) {
                         
                    componentTemplate = componentTemplate.replace(/\s?[^\=\s]*\=\"[^\"]*\"/g, 
                        function(match, contents, offset, s)
                        {
                            if(match.indexOf(' ng-mouse') !== 0) {//(match.indexOf(' class') === 0 || match.indexOf(' id') === 0))
                                /*if(match.indexOf(' class') === 0 && item.name !== "ct_inner_content") {
                                    match = match.substring(0 , match.length-1)+', disabled-div"';
                                }*/
                                return match;
                            }
                            else
                                return '';
                        }
                    );
                }

                // set columns number for Columns component
                if ( item.name == "ct_columns" && item.children ) {
                    $scope.columns[item.id] = item.children.length;
                }

                // handle builtin components
                if (type=="builtin") {
                    var builtInWrap = $scope.getBuiltInWrap(componentParent);
                    $scope.cleanInsert(componentTemplate, builtInWrap, index, primaryIndex);
                }
                else

                // handle Re-usable components
                if ( item.name == "ct_reusable" ) {
                    
                    var viewId      = item.options.view_id,
                        componentId = item.options.ct_id;
                    
                    // insert to DOM
                    var innerWrap = $scope.getInnerWrap(componentParent);
                    $scope.cleanInsert(componentTemplate, innerWrap, index-builtinOffset, primaryIndex);
                    // load post
                    $scope.loadPostData($scope.addReusableContent, viewId, componentId);
                }
                else

                // handle span component
                if ( item.name == "ct_span" || (item.name == "ct_link_text" && (!componentParent[0] || componentParent[0].attributes['contenteditable']))) {

                    // find if there any placeholder inside any part of the component
                    var noPlaceholders = true;

                    if (componentParent) {
                        var placeholders = componentParent.find('[id^="ct-placeholder"]');
                        if (placeholders.length>0) {
                            noPlaceholders = false;
                        }
                    }

                    if ( componentParent[0] && !componentParent[0].attributes['contenteditable'] && noPlaceholders) {

                        $scope.cleanInsert(componentTemplate, componentParent, index-builtinOffset, primaryIndex);
                    }
                    else {

                        var timeout = $timeout(function() {
                            
                            var placeholderID = (item.id>=200000) ? item.id-200000 : item.id,
                                placeholderID = (placeholderID>=100000) ? placeholderID-100000 : placeholderID;
                            
                            $scope.cleanReplace("ct-placeholder-"+placeholderID , componentTemplate, domNode);

                            // cancel timeout
                            $timeout.cancel(timeout);
                        }, 0, false);
                    }

                }
                else

                // handle ct_link component
                if ( item.name == "ct_link" && componentParent[0] && componentParent[0].attributes['contenteditable'] ) {
                    
                    var timeout = $timeout(function() {

                        var placeholderID = (item.id>=200000) ? item.id-200000 : item.id,
                            placeholderID = (placeholderID>=100000) ? placeholderID-100000 : placeholderID;

                        $scope.cleanReplace("ct-placeholder-"+placeholderID,componentTemplate, domNode);
                        $scope.rebuildDOM(item.children[0].id);

                        // get highest id number
                        if ( parseInt(item.id) > $scope.component.id && parseInt(item.id) < 100000) {
                            $scope.component.id = parseInt(key);
                        }
                        if ( parseInt(item.id) == $scope.component.id ) {
                            $scope.component.id++;
                            
                        }

                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);

                    return;
                } 

                // handle other components
                else {
                    var innerWrap = $scope.getInnerWrap(componentParent);
                    $scope.cleanInsert(componentTemplate, innerWrap, forceZeroIndex?0:(index-builtinOffset), primaryIndex);
                }

                if ( item.name == "ct_separator" ) {
                    $scope.separatorAdded = true;
                }

                if (item.name == "ct_inner_content") {
                    if (parseInt(key) >= 100000) {
                        $scope.innerContentRoot = item;
                    }
                    else {
                        $scope.innerContentAdded = true;
                    }
                }

                // get highest id number
                if ( parseInt(key) > $scope.component.id  && parseInt(key) < 100000) {
                    $scope.component.id = parseInt(key);
                }

                if (item.name == "ct_image") {
                    var src = $scope.getOption('src', item.id);

                    var image_type = $scope.getOption('image_type', item.id);
                    if(src.indexOf('[oxygen') > -1 && image_type != 2) {
                        setTimeout(function() {
                            $scope.parseImageShortcode(item.id);
                        }, 0);
                    }

                    if ($scope.updateAttachmentSizes) {
                        $scope.processImageSizes(item.id);
                    }
                }

                if (item.name == "oxy_map") {
                    var map_address = $scope.getOption('map_address', item.id);
                    if(map_address.indexOf('[oxygen') > -1) {
                        setTimeout(function() {
                            $scope.parseMapShortcode(item.id);
                        }, 0);
                    }
                }

                // Animate on scroll needs to be refreshed to work on modals
                if(item.name == 'ct_modal' && typeof AOS !== 'undefined'){
                    // If there are several modals, only refresh AOS once
                    if( typeof window.refreshAOSTimeout === 'undefined' ) {
                        window.refreshAOSTimeout = setTimeout(function() {
                            AOS.refreshHard();
                        }, 2000);
                    }
                }

                if (item.name == "ct_video") {
                    setTimeout(function() {
                        // this is done to update embed_src when e.g. preveiwing different posts
                        $scope.setOption(item.id, item.name, 'src');
                        $scope.$apply();
                    }, 10);
                }

                
                // setTimeout(function() {
                //     $scope.parseBackgroundShortcode(item.id);
                // }, 0);
                

                 // save styles to cache
                $scope.updateComponentCacheStyles(key, function() {
                    // clear cache
                    $scope.cache.idCSS = "";

                    Object.keys($scope.cache.idStyles).map(function(key, index) {
                        $scope.cache.idCSS += $scope.cache.idStyles[key];
                    }); 
                    
                    $scope.outputCSSStylesAfterWait('id', $scope.cache.idCSS);

                }, domNode?domNode.hasClass('rebuildDOMContainer')?false:true:false);
                
                // go deeper in Components Tree
                if ( item.children ) {
                    // save parent ID globally
                    var oldParent = $scope.latestParent;
                    $scope.latestParent = item.id;

                    // safety switch
                    var counter = 0;
                    $scope.buildingOxygenTreeCounter++;
                    
                    function buildOxygenTreeChildTimeout(counter) {                     
                        counter++;
                        setTimeout(function(){
                            buildOxygenTreeChild(counter)
                        }, 100);
                    }

                    function buildOxygenTreeChild(counter) {
                        
                        var parentComponent = $scope.getComponentById(item.id, domNode)

                        if (!parentComponent) {
                            console.log("ID "+item.id+" element is not present on the page");
                            if (counter < 300) {
                                buildOxygenTreeChildTimeout(counter);
                            }
                            return;
                        };

                        // stop building the tree if any AJAX request to get elelment's HTML taking over 30s (100ms x 300)
                        // otherwise child elements won't be properly loaded and errors will occur
                        if ( parentComponent.hasClass('oxy-ajax-loading') && counter >= 300) {
                            $scope.showNoticeModal("<div>AJAX Timeout Error. Please contact support</div>");                            
                        }
                        else 
                        // still waiting for AJAX response
                        if ( parentComponent.hasClass('oxy-ajax-loading') && counter < 300) {
                            buildOxygenTreeChildTimeout(counter);
                        }
                        // AJAX request completed, proceed with sub tree building
                        else {
                            $scope.buildingOxygenTreeCounter--;
                            $scope.adjustResizeBox();
                            $scope.buildComponentsFromTree(item.children, null, reorder, domNode);
                        }
                    }

                    // first time no timeout
                    buildOxygenTreeChild(counter);

                    $scope.latestParent = oldParent;
                }

            }
        });
        
            //}
        //}
        
        if($scope.afterComponentsBuiltSignal) {
            clearTimeout($scope.afterComponentsBuiltSignal);
        }

        $scope.afterComponentsBuiltSignal = setTimeout(function() {
            // $scope.$broadcast('components-built');
            // now that the function ends after recursion, time to execute the dynamic lists in reverse order, from down to the top of hierarchy
            while($scope.dynamicListTrees['runMainOnLoad'].length > 0) {
                $scope.dynamicListAction($scope.dynamicListTrees['runMainOnLoad'].pop(), 0, true);
            }

            $scope.repeaterListCurrentIndex = null;
        }, 500)
    }


    /**
     * Timeout helper for $scope.waitOxygenTree()
     * 
     * @author Ilya K.
     * @since 3.1
     */

    $scope.waitOxygenTreeTimeout = function(callback) {                        
        setTimeout(function(){
            $scope.waitOxygenTree(callback)
        }, 100);
    }


    /**
     * Wait Oxygen to built a tree and finish all AJAX requests
     * 
     * @author Ilya K.
     * @since 3.1
     */

    $scope.waitOxygenTree = function(callback) {
        // don't callback while tree building in process
        if ( $scope.buildingOxygenTreeCounter > 0 ) {
            // timeout to wait for AJAX response
            $scope.waitOxygenTreeTimeout(callback)
        }
        else {
            // tree building completed, do what we need
            callback();
        }
    }

    /**
     * Wait Oxygen to load API elements templates with all their data
     * 
     * @author Ilya K.
     * @since 3.9
     */

    $scope.waitComponentsTemplatesCounter = 0;
    $scope.waitComponentsTemplates = function(callback) {
        if ($scope.componentsTemplates == undefined) {
            $scope.waitComponentsTemplatesCounter++;
            setTimeout(function(){
                if ($scope.componentsTemplates == undefined && $scope.waitComponentsTemplatesCounter < 50) {
                    // keep waiting 
                    $scope.waitComponentsTemplates(callback);
                }
                else {
                    callback();
                }
                if ( $scope.waitComponentsTemplatesCounter >= 50) {
                    console.log('waitComponentsTemplates timeout');
                    $scope.waitComponentsTemplatesCounter = 0;
                }
            }, 200);
        }
        else {
            callback();
        }
    }


    /**
     * Get currently active component DOM element
     * 
     * @return {jqLite Object}
     * @since 0.1
     */

    $scope.getActiveComponent = function() {

        if ($scope.log) {
            console.log("getActiveComponent()");
        }

        return $scope.getComponentById($scope.component.active.id);
    }


    $scope.setCurrentStylesheetFolder = function(id) {
        $scope.stylesheetToEdit['parent'] = id;

        if(id > 0) {

            var parent = _.findWhere($scope.styleSheets, {id: id});

            if(parent) {
                $scope.currentActiveStylesheetFolder = parent.name;
            }
            else {
                $scope.currentActiveStylesheetFolder = '';
            }
            
        }
        else {
            $scope.currentActiveStylesheetFolder = '';
        }

        $scope.$apply();
    }

    $scope.setCurrentSelectorFolder = function(value) {
        var selector;

        if($scope.isEditing('class')) {
            selector = $scope.classes[$scope.currentClass];
        }
        else {
            if($scope.selectedStyleSet) {
                selector = $scope.styleSets[$scope.selectedStyleSet];
            }
            else {
                selector = $scope.customSelectors[$scope.selectorToEdit];
            }
        }

        if(value !== '') {
            selector['parent'] = value;    
        }
        else {
            delete selector['parent'];
        }
        
        if(selector['parent'])
            $scope.currentActiveFolder = selector['parent']
        else
            $scope.currentActiveFolder = '';

        $scope.$apply();
        $scope.classesCached = false; 
        $scope.outputCSSOptions();
    }

    /**
     * Get component DOM element by ID
     * 
     * @return {jqLite Object}
     * @since 0.1
     */

    $scope.getComponentById = function(id, domNode) {

        if ($scope.log) {
            console.log("getComponentById()", id);
        }

        var component;

        if(domNode) {
            component = domNode.find('[ng-attr-component-id="'+id+'"]');
            if(component.length < 1) {
                return domNode;
            }
        }
        else {
            // get element by id
            component = angular.element('[ng-attr-component-id="'+id+'"]:not([disabled="disabled"])');

            if(!component || component.length < 1) {
                // it could be a span in processing
                component = angular.element('#ct-placeholder-'+id);
            }
        }

        //return false if no active component found
        if ( !component || component.length == 0 ) {
            return false;
        }
        
        return component;
    }


    /**
     * Remove currently active component
     * 
     * @since 0.1
     */

    $scope.removeActiveComponent = function() {

        return $scope.removeComponentWithUndo($scope.component.active.id, $scope.component.active.name, $scope.component.active.parent.id);
        //$scope.removeComponentById($scope.component.active.id, $scope.component.active.name);
    }

    /**
     * Check if component is empty
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.isEmptyComponent = function(id) {

        if (id===undefined) {
            id = $scope.component.active.id;
        }

        var component = $scope.getComponentById(id);

        // getComponentById() can return boolean false
        if (component && component.is(':empty')){
            return true;
        }

        return false;
    }
    

    /**
     * Set editable status for component's friendly name in the DOM tree
     * 
     * @since 0.3.3
     * @author gagan goraya
     */    

    $scope.setEditableFriendlyName = function(id, event) {

        if (event) {
            $scope.editableFriendlyNamePropertiesPane = id;
        }
        else {
            $scope.editableFriendlyName = id;
        }

        var item = $scope.findComponentItem($scope.componentsTree.children, $scope.component.active.id, $scope.getComponentItem);
        var trimmedText = $scope.component.options[$scope.component.active.id]['nicename'].replace(/&nbsp;/g, '').replace(/[^a-zA-Z0-9#() ]/g, '');
        
        // blur action
        if ( id==0 && trimmedText === '' ) {
            trimmedText = $scope.calcDefaultComponentTitle();
        }

        item.options['nicename'] = trimmedText;
        $scope.component.options[$scope.component.active.id]['nicename'] = trimmedText;
       
        // close the menu
        if(id > 0) {
            jQuery(".ct-more-options-expanded", parent.document).removeClass("ct-more-options-expanded");
        }
    }

    /**
     * searches for the node in the component tree and apply function to update nicename
     * 
     * @since 0.3.3
     * @author gagan goraya
     */    

    $scope.updateFriendlyName = function(id) {
        $scope.findComponentItem($scope.componentsTree.children, id, $scope.updateComponentNiceName);
    }

    /**
     * updates the nicename into the provided item out of the component tree
     * 
     * @since 0.3.3
     * @author gagan goraya
     */   

    $scope.updateComponentNiceName = function(id, item) {
        item.options['nicename'] = $scope.component.options[id]['nicename'];
    }


    /**
     * loads the nicename into the current state from the item of the component tree
     * 
     * @since 0.3.3
     * @author gagan goraya
     */   

    $scope.loadComponentNiceName = function(id, item) {
        if(item.options['nicename'])
            $scope.component.options[id]['nicename'] = item.options['nicename'];
    }


    /**
     * Cut the component and display notice to undo this action
     * 
     * @since 1.2
     * @author Ilya K.
     */

    $scope.removeComponentWithUndo = function(id, name, parentId) {

        $parentScope.disableContentEdit();

        if (name=="oxy_header_left"||name=="oxy_header_center"||name=="oxy_header_right"){
            return false;
        }

        if(name=="ct_div_block" && $scope.component.options[parentId].name == 'oxy_dynamic_list') {
            alert('This div is essential to the working of the repeater.');
            return false;
        }

        if (name=="oxy_header_row"){
            var header_row = $scope.getComponentById(id);
            if (jQuery(header_row).siblings('.oxy-header-row').length === 0 ) {
                
                $scope.activateComponent(parentId);
                
                // DO NOT delete the parent if it is not an oxy_header component
                if($scope.component.options[$scope.component.active.id].name == 'oxy_header' && $scope.removeActiveComponent()){
                    // Only stop deleting the Header Row if the parent was successfully deleted
                    return true;
                }
            }
        }

        var retVal = $scope.removeComponentById(id, name, parentId);

        if (name=="ct_slide") {
            $scope.rebuildDOM(parentId);
            $scope.titleBarsVisibility("hidden","hidden"); 
        }

        // check if it was the last child and parent is AJAX element
        var parent = $scope.findComponentItem($scope.componentsTree.children, parentId, $scope.getComponentItem);
        if ( !parent.children && $scope.isAJAXElement(parent.name)) {
            $scope.rebuildDOM(parentId);
            $scope.undoRebuildParentID = parentId;
        }
        else {
            $scope.undoRebuildParentID = false;
        }

        $scope.rebuildDOMChangeParent(parentId);

        // create notice        
        // var noticeContent = "<div>You deleted " + $scope.component.options[id]['nicename'] + 
        //                     ". <span class=\"ct-undo-delete\" ng-click=\"undoDelete()\">Undo</span></div>";

        // $scope.showNoticeModal(noticeContent);
        return retVal;
    }

    
    /**
     * Cancel remove timeout
     * 
     * @since 1.2
     * @author Ilya K.
     */

    $scope.undoDelete = function() {

        // find component to paste
        $scope.findComponentItem($scope.componentsTree.children, $scope.componentInsertId, $scope.pasteComponentToTree);

        if ($scope.undoRebuildParentID) {
            $scope.rebuildDOM($scope.undoRebuildParentID);
        }
        else if ($scope.removedComponentName=="ct_span"){

            // restore previous parent content
            $scope.component.options[$scope.componentInsertId]["model"]["ct_content"] = $scope.removedComponentParentContent;
            $scope.component.options[$scope.componentInsertId]["original"]["ct_content"] = $scope.removedComponentParentContent;
            $scope.setOption($scope.componentInsertId, "", "ct_content");

            $scope.rebuildDOM($scope.componentInsertId);
        }
        else {
            $scope.rebuildDOM($scope.removedComponentId);

            // if it is a child inside a dynamic list component
            var component = $scope.getComponentById($scope.removedComponentId)
            var oxyList = component.closest('.oxy-dynamic-list');
            
            if(oxyList.length > 0) {
                $scope.updateRepeaterQuery(parseInt(oxyList.attr('ng-attr-component-id')));
            }
        }

        $scope.outputCSSOptions($scope.removedComponentId)
        $scope.adjustResizeBox();
        $scope.cancelDeleteUndo();
    }


    /**
     * Cancel remove timeout
     * 
     * @since 1.2
     * @author Ilya K.
     */

    $scope.cancelDeleteUndo = function(id, name) {

        $scope.idToInsert = -1;
    }


    /**
     * Show notice modal
     * 
     * @since 1.2
     * @author Ilya K.
     */

    $scope.showNoticeModal = function(noticeContent, noticeClass) {

        $scope.noticeModalVisible = true;
        $scope.noticeClass = typeof noticeClass === 'undefined' ? "ct-warning" : noticeClass;

        // set content
        var noticeContentContainer = window.parent.document.getElementById("ct-notice-content");
        noticeContentContainer = angular.element(noticeContentContainer);

        // inseret to DOM
        angular.element(document).injector().invoke(function($compile) {
            noticeContentContainer.html($compile(noticeContent)($scope));
        });
    }


    /**
     * Hide notice modal
     * 
     * @since 1.2
     * @author Ilya K.
     */

    $scope.hideNoticeModal = function() {

        // clear content
        var noticeContentContainer = window.parent.document.getElementById("ct-notice-content");
        angular.element(noticeContentContainer).html("");

        $scope.noticeModalVisible = false;
    }

    
    /**
     * Remove component from DOM and Components Tree
     * 
     * @since 0.1.8
     */

    $scope.removeComponentById = function(id, name, parentId) {

        if ($scope.log) {
            console.log("removeComponentById()", id, name, parentId);
        }

        if ( id === 0 ) {
            //alert('You can not delete root!');
            return false;
        }

        // switch state
        $scope.switchState('original');

        // save IDs in scope
        $scope.componentInsertId = parentId;
        $scope.removedComponentId = id;
        $scope.removedComponentName = name;

        // if it is inserted inside an oxy dynamic list component, then update the list
        var oxyList = $scope.getComponentById(id).parent().closest('.oxy-dynamic-list');
        
        // update active parent
        $scope.findParentComponentItem($scope.componentsTree, id, $scope.updateCurrentActiveParent);

        // handle column remove
        if ( name == "ct_column" || (name == "ct_div_block" && $scope.isActiveParent("ct_new_columns")) ) {
            $scope.removeColumn(id);
        } 
        else {

            // remove from Components Tree
            $scope.findParentComponentItem($scope.componentsTree, id, $scope.cutComponentFromTree);

            // remove from DOM
            var component = $scope.getComponentById(id);

            if(!component) {
                return;
            }

            // save index globally
            $scope.newComponentKey = component.index();
            // not sure why we were need the index
            //$scope.newComponentKey = -1;

            $scope.removeComponentFromDOM(id);

            // handle span component
            if ( name == "ct_span" ) {

                var parent = $scope.getComponentById(parentId);

                // save content
                $scope.removedComponentParentContent = $scope.component.options[parentId]["original"]["ct_content"];

                $scope.component.options[parentId]["model"]["ct_content"] = parent[0].innerHTML;
                $scope.component.options[parentId]["original"]["ct_content"] = parent[0].innerHTML;
                $scope.setOption(parentId, "", "ct_content");

                $scope.rebuildDOM(parentId);
            }

            // handle slide component
            if ( name == "ct_slide" ) {
                $scope.rebuildDOM($scope.component.active.id);
            }

        }

        // header/footer separator
        if ( name == "ct_separator" ) {
            $scope.separatorAdded = false;
        }

        if ( name == "ct_inner_content" ) {
            $scope.innerContentAdded = false;
        }

        // activate parent
        if($scope.component.active.parent.id < 100000) {
            $scope.activateComponent($scope.component.active.parent.id, $scope.component.active.parent.name);
        }
        else {
            $scope.activateComponent(0, 'root');
        }

        // remove custom-css
        $scope.deleteCSSStyles("css-code-"+id);
        
        // clear styles cache
        $scope.removeComponentCacheStyles(id);

        $scope.contentEditingEnabled = false;
        $scope.linkEditingEnabled = false;

        $scope.unsavedChanges();

        //if the item being deleted is inside an oxygen dynamic list component, updated the dynamic list
        if(oxyList.length > 0 && !oxyList.hasClass('oxy-dynamic-list-edit') && name != "span") {
            $scope.updateRepeaterQuery(parseInt(oxyList.attr('ng-attr-component-id')))
        }
        return true;
    }

    
    /**
     * Check if current element or any child is
     * 
     * @since 3.0
     * @author Ilya K.
     */

    $scope.isComponentContain = function(name, id) {

        // check component
        if ($scope.component.options[id]['name'] == name) {
            return true;
        }

        // check if there is an element deep within the hierarchy
        var component = $scope.findComponentItem($scope.componentsTree.children, id, $scope.getComponentItem);
        // recursively go through the children, and see if there is any child with the name
        if($scope.findComponentByName(component.children, name, $scope.getComponentItem)) {
            return true;
        }

        return false;
    }

    /**
     * Duplicate component by ID
     * 
     * @since 0.3.0
     */

    $scope.duplicateComponent = function(id, name, parentId) {

        if (undefined === id) {
            id = $scope.component.active.id;
        }

        if (undefined === name) {
            name = $scope.component.active.name;
        }

        if (undefined === parentId) {
            parentId = $scope.component.active.parent.id;
        }

        // never duplicate a ct_inner_content, because there can be only one
        var innerContentExists = false;
        if($scope.component.active.name === 'ct_inner_content') {
            innerContentExists = true;
        }
        // but what if there is an inner_content deep within the hierarchy?
        var component = $scope.findComponentItem($scope.componentsTree.children, id, $scope.getComponentItem);
        // recursively go through the children, and see if there is any element with the name ct_inner_content
        if($scope.findComponentByName(component.children, 'ct_inner_content', $scope.getComponentItem)) {
            innerContentExists = true;
        }

        if(innerContentExists) {
            alert('You cannot add more than one Inner Content component to a template.');
            return;
        }

        if ($scope.isComponentContain('oxy-product-tabs',id)) {
            $scope.showNoticeModal("<div>You cannot add more than one Product Tabs element</div>");
            return;
        }

        // if parent is a repeater, it cannot contain more than one item as immediate children
        if($scope.component.options[parentId]['name'] ==  'oxy_dynamic_list') {
            alert('A repeater cannot contain more than one child element');
            return;
        }

        var newComponentId = $scope.component.id;

        // copy active Selector state if the it is 'ID'
        if(typeof($scope.activeSelectors[id]) !== 'undefined') {
            if($scope.activeSelectors[id] === false) {
                $scope.activeSelectors[newComponentId] = false;
            }
        }

        $scope.applyComponentDefaultOptions(newComponentId, $scope.component.active.name);
            
        // copy component tree node
        $scope.findParentComponentItem($scope.componentsTree, id, $scope.copyComponentTreeNode);

        // find component to paste
        $scope.findComponentItem($scope.componentsTree.children, parentId, $scope.pasteComponentToTree);

        // We must add the attachment sizes to a cloned ct_image before activating the component, so it doesn't try to grab from AJAX
        if( name == "ct_image" &&
            $scope.component.options[id]['model']['image_type'] == 2 &&
            $scope.component.options[id]['model']['attachment_id'] != "" &&
            typeof $scope.component.options[id].sizes !== 'undefined'
        ) {
            // Actual object with width and height data for each attachment size
            $scope.component.options[newComponentId].sizes = $scope.component.options[id].sizes;
            // For the attachment size dropdown
            $scope.component.options[newComponentId].size_labels = $scope.component.options[id].size_labels;
        }

        // If duplicating a live preview modal, the newly created one will be set to show inline
        if( name == "ct_modal" && $scope.component.options[id]['model']['behavior'] == '2' ) {
            $scope.setOptionModel( "behavior", "1", newComponentId );
        }

        var componentItem = $scope.getComponentById(id);
        var oxyList = componentItem.closest('.oxy-dynamic-list');
        if(oxyList.length > 0 && !oxyList.hasClass('oxy-dynamic-list-edit')) {
            $scope.updateRepeaterQuery(parseInt(oxyList.attr('ng-attr-component-id')))
        }

        $scope.activateComponent($scope.componentBuffer.id, $scope.componentBuffer.name);
                
        if (name == "ct_slide") {
            $scope.rebuildDOM(parentId);
            $scope.titleBarsVisibility("hidden");
        }
        else {
            $scope.rebuildDOM(newComponentId);
        }

        // look if any parent should be rebuilt 
        $scope.rebuildDOMChangeParent(parentId);

        $scope.outputCSSOptions(newComponentId);

        // Lets keep the newly added component in memory, this will help to distinguish these from the ones loaded from db
        $scope.justaddedcomponents = $scope.justaddedcomponents || [];
        $scope.justaddedcomponents.push(newComponentId);

        // copy active Selector state. If it is current class
        if(typeof($scope.activeSelectors[id]) !== 'undefined') {
            
            if($scope.activeSelectors[id] !== false)
                $scope.setCurrentClass($scope.activeSelectors[id], true); // the second parameter signifies that we do not want to re-apply model options atm
        }

        // disable undo option
        $scope.cancelDeleteUndo();
    }

    // activateComponent is bubbling up to oxy-modal-backdrop even though there is a stopPropagation set
    // so we are using this helper function for when the user clicks the modal backdrop
    $scope.activateModalComponent = function( id, $event ) {
        if( angular.element($event.target).hasClass("oxy-modal-backdrop") ) {
            $scope.activateComponent( id, 'ct_modal' );
        }
    }

    $scope.insertModalCloseButton = function() {
        $scope.addComponent("ct_link_button");
        $scope.setOptionModel("ct_content", "Close (Double-click to edit button text)");
        $scope.addClassToComponent($scope.component.active.id, 'oxy-close-modal');
    }

    $scope.enterChoosingSelectorMode = function(option) {
        jQuery('body').addClass('choosing-selector');
        $scope.choosingSelectorEnabled = true;
        $scope.choosingSelectorOption = option;
    }

    $scope.exitChoosingSelectorMode = function() {
        jQuery('body').removeClass('choosing-selector');
        $scope.choosingSelectorEnabled = false;
        $parentScope.exitChoosingSelectorMode();
    }
    
    /**
     * Activate Component
     * 
     * @since 0.1
     * @author Ilya K.
     */
    
    $scope.activateComponent = function(id, componentName, $event) {

        // make sure code editor content saved to the model before active component switched
        jQuery('.cm-content', parent.document).blur();

        // Activate show button flashing
        if ($parentScope.showLeftSidebar == false && $parentScope.showButtonFlashing == false) {
            $parentScope.showButtonFlashing = true;
            
            $timeout(function() {
                $parentScope.showButtonFlashing = false;
            }, 2500);
        }

        // if the item being selected is under an ACF repeater, then its ACF field reference should be stored
        var repeater, hasRepeater;
        var component = $scope.getComponentById(id);

        angular.element('.oxy-list-child-active').removeClass('oxy-list-child-active');

        if(component && component.length > 0) {
            repeater = component.parent().closest('.oxy-dynamic-list');
            $scope.parentRepeaterHasACF = false;

            while(repeater && repeater.length > 0) {

                var repeaterID = parseInt(repeater.attr('ng-attr-component-id'));

                if ( $scope.component.options[repeaterID]['original']['use_acf_repeater'] 
                  && $scope.component.options[repeaterID]['original']['use_acf_repeater'] !== 'false') {
                    $scope.parentRepeaterHasACF = $scope.component.options[repeaterID]['original']['acf_repeater'];
                    repeater = false;
                }
                else if ( $scope.component.options[repeaterID]['original']['use_metabox_clonable_group'] 
                  && $scope.component.options[repeaterID]['original']['use_metabox_clonable_group'] !== 'false') {
                    $scope.parentRepeaterHasACF = $scope.component.options[repeaterID]['original']['metabox_group'];
                    repeater = false;
                } else {
                    repeater = repeater.parent().closest('.oxy-dynamic-list');
                }
            }

            // also, need to mark all the repeaters up in the hierarchy that they have active contents inside
            repeater = component.parents('.oxy-dynamic-list');
            repeater.each(function(e) {
                angular.element(this).addClass('oxy-list-child-active');
            })
        }

        if ($scope.log) {
            console.log("activateComponent()", id, componentName);
        }

        if( typeof $scope.choosingSelectorEnabled != 'undefined' && $scope.choosingSelectorEnabled == true && typeof componentName != 'undefined') {
            if( $scope.component.active.name == "ct_modal" ){
                $scope.setOptionModel($scope.choosingSelectorOption, '#' + $scope.component.options[id]['selector']);
                $event.stopPropagation();
            }
            $scope.exitChoosingSelectorMode();
            return false;
        }
        if( $scope.component.active.name != "ct_modal" && typeof $scope.choosingSelectorEnabled != 'undefined' && $scope.choosingSelectorEnabled == true ){
            $scope.exitChoosingSelectorMode();
        }

        $scope.selectedNodeType = false;
        $scope.currentPresetKey = false; 

        // do nothing if in selector detector mode
        if ( $scope.selectorDetector.mode && ( ['ct_widget','ct_code_block','ct_shortcode','oxy_posts_grid','oxy_rich_text','oxy_gallery',
                                               'oxy_nav_menu','oxy_comments','oxy_comment_form','oxy_login_form','oxy_search_form',
                                               'ct_sidebar','ct_inner_content'].indexOf(componentName)>-1 || $scope.hasOxyDataInside(id) ) ) { 
            
            if ( $scope.componentSelector.id == id ) {
                $scope.selectorDetector.bubble = true;
                return false;
            }
        }

        // check if we are bubbling up from component with selector mode activated
        if ( id > 0 && $scope.selectorDetector.mode && $scope.selectorDetector.bubble ) {
            return false;
        }

        // disable selector detector mode
        if (componentName!="ct_selector"&&$parentScope.disableSelectorDetectorMode) {
            $parentScope.disableSelectorDetectorMode();
        }

        if (undefined===componentName) {
            componentName = $scope.component.options[id] ? $scope.component.options[id].name || "" : "";
        }

        if (componentName=="ct_widget") {
            var timeout = $timeout(function() {
                $scope.renderWidget(id,true)
                // cancel timeout
                $timeout.cancel(timeout);
            }, 0, false);            
        }

        if ($scope.buildingOxygenTreeCounter == 0 && $scope.componentsTemplates !== undefined && $scope.componentsTemplates[componentName]) {
            $scope.loadControlsWithAJAX(componentName);
        }

        $scope.stylesheetToEdit = false;
        $parentScope.actionTabs['styleSheet'] = false;

        // Fix for nested ng-click
        if (typeof $event != 'undefined') {
            // ok, but let it reach the document atleast or some click bindings on the document will not work
            angular.element(document).trigger($event.type);
            $event.stopPropagation();
        }

        if(componentName === 'ct_svg_icon' || componentName === 'ct_fancy_icon') {
            var currenticonid = $scope.component.options[id]['model']['icon-id'];
            $scope.iconFilter.title = '';
            angular.forEach($scope.SVGSets, function(SVGSet, index) {
                if(currenticonid.indexOf(index.split(' ').join('')) === 0) {
                    $scope.currentSVGSet = index;
                    angular.forEach(SVGSet['defs']['symbol'], function(symbol) {
                        if(symbol['@attributes']['id'] === currenticonid.replace(index.split(' ').join(''), ''))
                            $scope.iconFilter.title = symbol.title;
                    });     
                }
            });
        }

        // No need to actiavte component that already active
        if (id == $scope.component.active.id) {
            // adjust the resize box in case if its being done inside a dynamic list component
            $scope.adjustResizeBox();
            return false;
        }

        window.parent.currentCMEditor = false
        window.parent.mixedCMEditors = false

        // check active selector and classes to be present in Oxygen
        $scope.checkComponentClasses(id);

        if (typeof $event != 'undefined') {
            // close colorpicker when switched between same type components, i.e Headline to Headline
            jQuery('body').trigger('click.wpcolorpicker');
        }

        // make it impossible to activate any component from outer template
        if(id >= 100000) {
            //$scope.activateComponent(0, 'root');
            //return false;
        }

        // save component where selector detector were activate
        if (id !== -1) {
            $scope.componentSelector.id = id;
        }

        // unset parent element if activating root
        if (id == 0) {
            $scope.component.active.parent = {};
        }

        // disable selector detector pause mode
        $scope.selectorDetector.modePause = false;
        
        // close structure panel menus       
        jQuery(".ct-more-options-expanded", parent.document).removeClass("ct-more-options-expanded");

        var timeout = $timeout(function() {
            jQuery(".ct-highlight", "#ct-builder").removeClass('ct-highlight');
            // cancel timeout
            $timeout.cancel(timeout);
        }, 300, false);

        // while editing inner content, there is no point in activating the inner_content container
        /*if(componentName === 'ct_inner_content' && jQuery('body').hasClass('ct_inner')) {
            $scope.disableContentEdit();
            $scope.disableSelectable();
            $parentScope.closeAllTabs(["advancedSettings","componentBrowser"]); // keep certain sections
            return;
        }*/

        // fire blur on the previous nav component, if its nicename is being edited
        var previousNavElement = angular.element('[ng-attr-node-id="'+$scope.component.active.id+'"]');
        if(previousNavElement.find('span.ct-nicename.ct-nicename-editing').length) {

            $timeout(function() {
                previousNavElement.find('span.ct-nicename.ct-nicename-editing').trigger('blur');
            }, 0);
        }
        
        $scope.updateBreadcrumbs(id);

        // update dnd-type of resize box -- @author Jason
        $scope.selectedDragElementDNDType = componentName;

        // highlight only if event triggered not from the DOM Tree
        if ($event && !jQuery($event.currentTarget).hasClass('ct-dom-tree-node-anchor')){
            // expand the new structure panel to show the selected element
            var treenode = window.parent.document.querySelector('.dom-tree-node[ng-attr-tree-id="'+id+'"]');
            
            if(treenode) {
                var treeparent = treenode.parentNode.closest('.dom-tree-node');
                while(treeparent) {
                    $scope.parentScope.$broadcast('nodeExpand', treeparent);
                    treeparent = treeparent.parentNode.closest('.dom-tree-node');
                }    
            }
        }

        // trigger only from DOM Tree
        if ($event && jQuery($event.currentTarget).hasClass('ct-dom-tree-node-anchor') && jQuery($event.currentTarget).parents('.ct-sortable-tabs').length){
            var tab = jQuery($scope.getComponentById(id)).closest('.oxy-tabs-wrapper > div');
            if (tab.length) {
                tab.trigger('click');
            }
            // look if this is Tabs Contents click
            else {
                var tabsContentsTab = jQuery($scope.getComponentById(id)).closest('.oxy-tabs-contents-wrapper > div'),
                    index           = tabsContentsTab.index(),
                    tabsSelector    = tabsContentsTab.closest('.oxy-tabs-contents-wrapper').attr('data-oxy-tabs-wrapper');
                
                jQuery('#'+tabsSelector).children('.oxy-tabs-wrapper > div').eq(index).trigger('click');
            }

        }

        //close backgroundlayers when component is switched
        $scope.parentScope.activeForEditBgLayer = false;

        //close the advanced styles tab in the sidebar
        $scope.parentScope.styleTabAdvance = false;
        
        // disable stuff
        $parentScope.disableContentEdit();
        $parentScope.disableSelectable();
        $parentScope.closeAllTabs(["advancedSettings"]); // keep certain sections
        // TODO: make this list pulled automatically like for toolbar
        $parentScope.closeTabs(['advanced','oxy_posts_grid','dynamicList','slider','navMenu','gallery','oxy_testimonial','oxy_icon_box','oxy_progress_bar','oxy_pricing_box','oxy_superbox','effects','oxy_header'])
        $parentScope.closeTabs(CtBuilderAjax.componentsWithTabs);

        // temporary fix for basic subtabs
        $parentScope.tabs.slider    = [];
        $parentScope.tabs.navMenu   = [];

        // update active component id and name
        $scope.previouslyActiveId       = $scope.component.active.id;
        $scope.previouslyActiveParentId = $scope.component.active.parent.id;
        $scope.component.active.id      = id;
        $scope.component.active.name    = componentName;

        if (id > 0) {
            // set all edits (class, state...) back to id
            $scope.switchEditToId();

            // update active component parent
            $scope.findParentComponentItem($scope.componentsTree, id, $scope.updateCurrentActiveParent);
        }

        if (componentName=="oxy_header_left"||componentName=="oxy_header_center"||componentName=="oxy_header_right"){
            $scope.activateComponent($scope.component.active.parent.id,"oxy_header_row");
            return;
        }

        if ( $scope.previouslyActiveParentId === $scope.component.active.parent.id ) {
            $scope.hideTitleBars(true, false);
        }
        else {
            $scope.hideTitleBars(true, true);
        }

        // Hide panels
        $scope.showComponentsList   = false;
        $scope.linkEditingEnabled   = false;

        // Hide Expanded
        $parentScope.toggleSidebar(true);

        // apply options
        $scope.applyModelOptions();

        // update CSS for disabled component
        $scope.outputCSSOptions($scope.previouslyActiveId);
        
        $parentScope.checkTabs();

		if(componentName =="ct_data_custom_field") {
			var timeout = $timeout(function() {
				$parentScope.updateMetaDropdown();
                // cancel timeout
                $timeout.cancel(timeout);
			},300, false );
		}

        if( componentName =="ct_image" ) {
            $scope.processImageSizes();
        }

    }

    $scope.processImageSizes = function(id) {
        
        if (id===undefined) {
            id = $scope.component.active.id;
        }

        if ($scope.updateAttachmentSizes) {
            $scope.component.options[id].sizes = false;
            $scope.component.options[id].sizes_requested = false;
        }

        if (
            $scope.component.options[id]['model'] && 
            $scope.component.options[id]['model']['image_type'] == 2 &&
            $scope.component.options[id]['model']['attachment_id'] != "" &&
            $scope.component.options[id]['model']['attachment_id'] != undefined &&
            !$scope.component.options[id].sizes &&
            !$scope.component.options[id].sizes_requested
        ) {
            $scope.component.options[id].sizes_requested = true;
            
            $scope.loadAttachmentSizes( $scope.component.options[id]['model']['attachment_id'], function( data ){
                
                // Actual object with width and height data for each attachment size
                $scope.component.options[id].sizes = data;
                // For the attachment size dropdown
                $scope.component.options[id].size_labels = Object.keys( data );

                var currentSize = $scope.component.options[id]['model']['attachment_size'];
                if (!currentSize) {
                    currentSize = "full";
                }

                if( !$scope.component.options[id]['model']['attachment_size'] ||
                     $scope.component.options[id].size_labels.indexOf( currentSize ) == -1) 
                {
                    $scope.setOptionModel('attachment_size', $scope.component.options[id].size_labels[0], id, "ct_image");
                }
                
                $scope.setOptionModel('attachment_height', data[ currentSize ].height, id, "ct_image");
                $scope.setOptionModel('attachment_width', data[ currentSize ].width, id, "ct_image");
                $scope.setOptionModel('attachment_url', data[ currentSize ].url, id, "ct_image");
            }, id );

        }
    }


    /**
     * Populate the select breadcrumbs
     *
     * @since 2.0
     * @author Gagans code wrapped to a function by Ilya K.
     */
    
    $scope.updateBreadcrumbs = function(id) {
        
        var item, parent = id, counter = 0;
        $scope.selectAncestors = [];
        while(parent > 0 && counter < 100) {
            counter++;
            item = $scope.findComponentItem($scope.componentsTree.children, parent, $scope.getComponentItem);
            $scope.selectAncestors.push({id: (parent === id ? 0 : parent), name: $scope.calcDefaultComponentTitle(item, true), tag: item.name});
            parent = item.options['ct_parent'];
        }
        if($scope.selectAncestors.length > 1) {
            $scope.selectAncestors.reverse();
        }
    }


    /**
     * Show adjusted outlines, overlays and titlebar
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.adjustResizeBox = function() {

        // don't show in content edit mode
        if ( $parentScope.actionTabs["contentEditing"] ) {
            return false;
        }

        if ($scope.log) {
            console.log("adjustResizeBox()");
            $scope.functionStart("adjustResizeBox()");
        }

        var resizeBox = jQuery("#oxygen-resize-box");

        var timeout = $timeout(function() {

            var id          = $scope.component.active.id,
                parentId    = $scope.component.active.parent.id,
                name        = $scope.component.active.name;

            var component = $scope.getComponentById(id);


            if (id && $scope.component.options[id]['model'] && (parseInt($scope.component.options[id]['model']['conditionspreview']) === 0
                            ||
                            (parseInt($scope.component.options[id]['model']['conditionspreview']) === 1 && $scope.component.options[id]['model']['globalConditionsResult'] === false))) {
                resizeBox.hide();
                return false;
            }

            if (id<=0||name=="ct_paragraph"||name=="ct_ul"||name=="ct_li"||name=="ct_selector"||name=="ct_reusable") {
                resizeBox.hide();
                return false;
            }

            var component = $scope.getComponentById(id);

            // don't adjust the rszie box if no component present (i.e still loading with AJAX)
            if (!component) {
                return;
            }

            if(!component.attr('contenteditable') || component.attr('contenteditable') === 'false') {
                resizeBox.show();
            }

            jQuery(".rb").hide();

            

            if (!component) {
                $timeout.cancel(timeout);
                $scope.functionEnd("adjustResizeBox()");
                return false;
            }

            var sectionOffset   = 0,
                parent          = $scope.getComponentById(parentId),
                offset          = component.offset(),
                height          = component.outerHeight(),
                iframeHeight    = $parentScope.artificialViewport.outerHeight(),
                width           = component.outerWidth(),
                bodyWidth       = jQuery('body').outerWidth();

            if (name=="ct_section") {
                sectionOffset = 2;
                component = jQuery('.ct-inner-wrap',component);
            }
            
            var marginHeight    = component.outerHeight(true),
                marginWidth     = component.outerWidth(true),

                minHeight       = ( parseInt(height) < 30 ) ? parseInt(height)/3 : 10,
                minWidth        = ( parseInt(width) < 30 ) ? parseInt(width)/3 : 10,

                marginTop       = ( parseInt(component.css("margin-top"))       > minHeight ) ? parseInt(component.css("margin-top")) : minHeight,
                marginRight     = ( parseInt(component.css("margin-right"))     > minWidth ) ? parseInt(component.css("margin-right")) : minWidth,
                marginBottom    = ( parseInt(component.css("margin-bottom"))    > minHeight ) ? parseInt(component.css("margin-bottom")) : minHeight,
                marginLeft      = ( parseInt(component.css("margin-left"))      > minWidth ) ? parseInt(component.css("margin-left")) : minWidth,

                paddingTop      = ( parseInt(component.css("padding-top"))      > minHeight ) ? parseInt(component.css("padding-top")) : minHeight,
                paddingRight    = ( parseInt(component.css("padding-right"))    > minWidth ) ? parseInt(component.css("padding-right")) : minWidth,
                paddingBottom   = ( parseInt(component.css("padding-bottom"))   > minHeight ) ? parseInt(component.css("padding-bottom")) : minHeight,
                paddingLeft     = ( parseInt(component.css("padding-left"))     > minWidth ) ? parseInt(component.css("padding-left")) : minWidth;

            // Margin overlays
            if ( ! $parentScope.isActiveName("ct_section") ) {
                $scope.rbMarginTop.css({
                    display: "block",
                    width: width - 1,
                    top: offset.top - marginTop,
                    left: offset.left - 1,
                    height: marginTop
                })
                $scope.rbMarginBottom.css({
                    display: "block",
                    width: width - 1,
                    top: offset.top + height,
                    left: offset.left - 1,
                    height: marginBottom
                })

                if (! $parentScope.isActiveName("ct_headline") &&
                    ! $parentScope.isActiveName("ct_text_block") ) {

                    if ( bodyWidth - width - parseInt(component.css("margin-right")) > minWidth) {
                        $scope.rbMarginLeft.css({
                            display: "block",
                            width: marginLeft,
                            top: offset.top - 1,
                            left: offset.left - marginLeft,
                            height: height
                        })
                    }
                    if ( bodyWidth - width - parseInt(component.css("margin-left")) > minWidth) {
                        $scope.rbMarginRight.css({
                            display: "block",
                            width: marginRight,
                            top: offset.top - 1,
                            left: offset.left + width,
                            height: height
                        })
                    }
                }
            }

            // Padding overlays
            if (! $parentScope.isActiveName("oxy_nav_menu") &&
                ! $parentScope.isActiveName("ct_slide") &&
                ! $parentScope.isActiveName("ct_headline") &&
                ! $parentScope.isActiveName("ct_text_block") &&
                ! $parentScope.isActiveName("ct_link_text") &&
                ! $parentScope.isActiveName("ct_image") &&
                ! $parentScope.isActiveName("ct_svg_icon") &&
                ! $parentScope.isActiveName("ct_fancy_icon") ) {

                $scope.rbPaddingTop.css({
                    display: "block",
                    width: width,
                    top: offset.top,
                    left: offset.left,
                    height: paddingTop
                })
                $scope.rbPaddingBottom.css({
                    display: "block",
                    width: width,
                    top: offset.top + height - paddingBottom,
                    left: offset.left,
                    height: paddingBottom
                })
                
                if ( ! $parentScope.isActiveName("ct_section") ) {

                    $scope.rbPaddingLeft.css({
                        display: "block",
                        width: paddingLeft,
                        top: offset.top + paddingTop,
                        left: offset.left,
                        height: height - paddingTop - paddingBottom
                    })
                    $scope.rbPaddingRight.css({
                        display: "block",
                        width: paddingRight,
                        top: offset.top + paddingTop,
                        left: offset.left + width - paddingRight,
                        height: height - paddingTop - paddingBottom
                    })
                }
            }

            // Outlines. DO NOT delete, we might use this in future
            /*$scope.rbTop.css({
                display: "block",
                width: width + 4 - sectionOffset*2,
                top: offset.top - 2 + sectionOffset,
                left: Math.round(offset.left - 2 + sectionOffset)
            })
            $scope.rbLeft.css({
                display: "block",
                height: height + 4 - sectionOffset*2,
                top: offset.top - 2 + sectionOffset,
                left: Math.round(offset.left - 2 + sectionOffset)
            })
            $scope.rbRight.css({
                display: "block",
                height: height + 4 - sectionOffset*2,
                top: offset.top - 2 + sectionOffset,
                left: Math.round(offset.left + width - sectionOffset)
            })
            $scope.rbBottom.css({
                display: "block",
                width: width + 4 - sectionOffset*2,
                top: offset.top + height - sectionOffset,
                left: Math.round(offset.left - 2 + sectionOffset)
            })*/

            // Titlebar

            // adjust section offset
            var innerWrapMargin = 0;
            if (name=="ct_section") {
                innerWrapMargin = parseFloat(component.css('marginLeft'));
            }

            var outlineOffset       = parseFloat(component.css('outlineOffset')),
                outlineWidth        = parseFloat(component.css('outlineWidth'));

            if (offset.top >= 30) {
                $scope.rbTitleBar.css({
                    display: "",
                    top: offset.top - outlineOffset - $scope.rbTitleBar.height(),
                    left: offset.left - outlineOffset + innerWrapMargin - outlineWidth
                })
            }
            else {
                $scope.rbTitleBar.css({
                    display: "",
                    top: offset.top + outlineOffset + outlineWidth + height,
                    left: offset.left - outlineOffset + innerWrapMargin - outlineWidth
                })
            }

            if ( height > iframeHeight - 60 ) {
                $scope.rbTitleBar.css({
                    top: offset.top + outlineOffset + height - $scope.rbTitleBar.height(),
                })
            }

            // Parent Titlebar
            if (parent) {
                var parentOutlineOffset = parseFloat(parent.css('outlineOffset')),
                    parentOutlineWidth  = parseFloat(parent.css('outlineWidth')),
                    parentOffset    = parent.offset(),
                    parentHeight    = parent.outerHeight(),
                    parentWidth     = parent.outerWidth();

                if ($scope.globalSettings.indicateParents=='true' &&
                    !$parentScope.isActiveParentName("ct_inner_content") && 
                    !$parentScope.isActiveParentName("oxy_header_left") && 
                    !$parentScope.isActiveParentName("oxy_header_center") && 
                    !$parentScope.isActiveParentName("oxy_header_right")) {
                    
                    if (parentId>0) {
                        
                        $scope.rbParentTitleBar
                        .css({
                            display: "" // set display option first, so the width can be calculated
                        });

                        var left = parentOffset.left + parentWidth + parentOutlineOffset + parentOutlineWidth - $scope.rbParentTitleBar.width();

                        if ( left + $scope.rbParentTitleBar.width() > bodyWidth ) {
                            left = bodyWidth - $scope.rbParentTitleBar.width();
                        }

                        $scope.rbParentTitleBar
                        .css({    
                            top: parentOffset.top + parentHeight + parentOutlineOffset + parentOutlineWidth,
                            left: left
                        })
                        if ( parentHeight > iframeHeight - 60 ) {
                            $scope.rbParentTitleBar.css({
                                top: parentOffset.top + parentHeight + parentOutlineOffset - $scope.rbParentTitleBar.height(),
                            })
                        }
                    }
                    else {
                        $scope.rbParentTitleBar.hide();
                    }
                }
                else {
                    $scope.rbParentTitleBar.hide();
                }
            }

            // cancel timeout
            $timeout.cancel(timeout);
            $scope.functionEnd("adjustResizeBox()");
        }, 100, false);
    }

    
    /**
     * Hide Resize box
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.hideResizeBox = function(timeout) {
        
        jQuery("#oxygen-resize-box").hide(timeout);
    }


    /**
     * Hide Title bars
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.hideTitleBars = function(titleBar, parentTitleBar) {

        if (titleBar===true) {
            $scope.rbTitleBar.hide();
        }

        if (parentTitleBar===true) {
            $scope.rbParentTitleBar.hide();
        }
    }


    /**
     * Change Title bars visibility
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.titleBarsVisibility = function(titleBar, parentTitleBar) {

        if (titleBar) {
            $scope.rbTitleBar.css('visibility',titleBar);
        }

        if (parentTitleBar) {
            $scope.rbParentTitleBar.css('visibility',parentTitleBar);
        }
    }


    /**
     * Highlight resize box elements if neeeded
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.checkResizeBoxOptions = function (optionName) {

        if (["margin-top","margin-right","margin-bottom","margin-left",
             "padding-top","padding-right","padding-bottom","padding-left"].indexOf(optionName)>-1) 
        {
            jQuery("#rb-"+optionName).addClass("rb-currently-editing");
            clearTimeout($scope.resizeBoxTimeout);
            $scope.resizeBoxTimeout = setTimeout(function(){
                jQuery("#rb-"+optionName).removeClass("rb-currently-editing");
            }, 400);
        }
    }


    /**
     * Update current active parent
     *
     * @since 0.1.5
     */

    $scope.updateCurrentActiveParent = function(key, item) {

        $scope.component.active.parent.id   = item.id;
        $scope.component.active.parent.name = item.name;
    }


    /**
     * Disable elememnt draggable
     *
     * @author Jason
     */

    $scope.disableElementDraggable = function(val) {
        $scope.isDesiableDraggable = val;
    }

    $scope.getDragDisabledState = function() {
        return $scope.isDesiableDraggable;
    }


    /**
     * Update current active parent
     *
     * @since 0.1.5
     */

    $scope.isActiveParent = function(name) {

        return ($scope.component.active.parent.name == name) ? true : false;
    }

    /**
     * Get list of component to allow the drag and drop
     *
     * @author Jason
     */
    $scope.getDNDAllowedTypes = function (componentName) {

        //-----
        // Specifying Draggable element
        //-----
        allowedTypes = [];
        switch (componentName) {
            case 'ct-builder':
                allowedTypes = [
                                'ct_section', 'ct_div_block', 'ct_nestable_shortcode', 'ct_new_columns', 'ct_reusable', 'ct_link', 'ct_slider', 'oxy_header',
                                'ct_inner_content', 'oxy_dynamic_list'
                               ];
                break;

            case 'ct_section':
                allowedTypes = [
                                'ct_headline', 'ct_image', 'ct_text_block', 'oxy_rich_text', 'ct_link_text', 'ct_svg_icon',
                                'ct_div_block',
                                'ct_nestable_shortcode',
                                'ct_new_columns',
                                'ct_data_comment_form',
                                'ct_video',
                                'ct_link_button',
                                'ct_fancy_icon',
                                'ct_reusable',
                                'ct_shortcode',
                                'ct_widget',
                                'ct_code_block',
                                'ct_link', 
                                'ct_slider',
                                'oxy_social_icons',
                                'oxy_nav_menu',
                                'oxy_map',
                                'oxy_soundcloud',
                                'oxy_posts_grid',
                                'oxy_comments',
                                'oxy_comment_form',
                                'ct_inner_content',
                                'oxy_testimonial',
                                'oxy_icon_box',
                                'oxy_progress_bar',
                                'oxy_pricing_box',
                                'oxy_tabs',
                                'oxy_tabs_contents',
                                'oxy_superbox',
                                'oxy_login_form',
                                'oxy_search_form',
                                'oxy_toggle',
                                'oxy_gallery',
                                'oxy_dynamic_list'
                               ];
                break;

            case 'ct_div_block':
            case 'ct_nestable_shortcode':
            case 'ct_inner_content':
            case 'oxy_toggle':
                    allowedTypes = [
                                'ct_headline', 'ct_image', 'ct_text_block', 'oxy_rich_text', 'ct_link_text', 'ct_svg_icon',
                                'ct_section', 'ct_div_block', 'ct_nestable_shortcode',
                                'ct_new_columns',
                                'ct_data_comment_form',
                                'ct_video',
                                'ct_link_button',
                                'ct_fancy_icon',
                                'ct_reusable',
                                'ct_shortcode',
                                'ct_widget',
                                'ct_code_block',
                                'ct_link', 
                                'ct_slider',
                                'oxy_social_icons',
                                'oxy_nav_menu',
                                'oxy_map',
                                'oxy_header',
                                'oxy_soundcloud',
                                'oxy_posts_grid',
                                'oxy_comments',
                                'oxy_comment_form',
                                'ct_inner_content',
                                'oxy_testimonial',
                                'oxy_icon_box',
                                'oxy_progress_bar',
                                'oxy_pricing_box',
                                'oxy_tabs',
                                'oxy_tabs_contents',
                                'oxy_superbox',
                                'oxy_login_form',
                                'oxy_search_form',
                                'oxy_toggle',
                                'oxy_gallery', 
                                'oxy_dynamic_list'
                               ];
                break;

            case 'oxy_icon_box':
            case 'oxy_pricing_box':
                    allowedTypes = [
                                'ct_headline', 'ct_image', 'ct_text_block', 'oxy_rich_text', 'ct_link_text', 'ct_svg_icon',
                                'ct_div_block', 'ct_nestable_shortcode',
                                'ct_data_comment_form',
                                'ct_video',
                                'ct_link_button',
                                'ct_fancy_icon',
                                'ct_reusable',
                                'ct_shortcode',
                                'ct_widget',
                                'ct_code_block',
                                'ct_link', 
                                'oxy_social_icons',
                                'oxy_nav_menu',
                                'oxy_map',
                                'oxy_soundcloud',
                                'oxy_posts_grid',
                                'oxy_comments',
                                'oxy_comment_form',
                                'ct_inner_content',
                                'oxy_testimonial',,
                                'oxy_progress_bar',
                                'oxy_tabs',
                                'oxy_tabs_contents',
                                'oxy_superbox',
                                'oxy_login_form',
                                'oxy_search_form',
                                'oxy_toggle',
                                'oxy_gallery',
                                'oxy_dynamic_list'
                               ];
                break;

            case 'oxy_header':
                allowedTypes = [
                                'oxy_header_row',
                               ];
                break;

            case 'oxy_header_right':
            case 'oxy_header_center':
            case 'oxy_header_left':
                allowedTypes = [
                                'ct_headline', 'ct_image', 'ct_text_block', 'oxy_rich_text', 'ct_link_text', 'ct_svg_icon',
                                'ct_div_block', 'ct_nestable_shortcode',
                                'ct_new_columns',
                                'ct_data_comment_form',
                                'ct_video',
                                'ct_link_button',
                                'ct_fancy_icon',
                                'ct_reusable',
                                'ct_shortcode',
                                'ct_widget',
                                'ct_code_block',
                                'ct_link', 
                                'ct_slider',
                                'oxy_social_icons',
                                'oxy_nav_menu',
                                'oxy_testimonial',
                                'oxy_icon_box',
                                'oxy_progress_bar',
                                'oxy_pricing_box',
                                'oxy_tabs',
                                'oxy_tabs_contents',
                                'oxy_superbox',
                                'oxy_toggle', 'oxy_dynamic_list'
                               ];
                break;

            case 'ct_slide':
                allowedTypes = [
                                'ct_headline', 'ct_image', 'ct_text_block', 'oxy_rich_text', 'ct_link_text', 'ct_svg_icon',
                                'ct_div_block', 'ct_nestable_shortcode',
                                'ct_new_columns',
                                'ct_data_comment_form',
                                'ct_video',
                                'ct_link_button',
                                'ct_fancy_icon',
                                'ct_reusable',
                                'ct_shortcode',
                                'ct_widget',
                                'ct_code_block',
                                'ct_link', 
                                'oxy_social_icons',
                                'oxy_nav_menu',
                                'oxy_map',
                                'oxy_soundcloud',
                                'oxy_posts_grid',
                                'oxy_comments',
                                'oxy_comment_form',
                                'ct_inner_content',
                                'oxy_testimonial',
                                'oxy_icon_box',
                                'oxy_progress_bar',
                                'oxy_pricing_box',
                                'oxy_tabs',
                                'oxy_tabs_contents',
                                'oxy_superbox',
                                'oxy_login_form',
                                'oxy_search_form',
                                'oxy_toggle',
                                'oxy_gallery', 'oxy_dynamic_list'
                               ];
                break;

            case 'ct_link':
                allowedTypes = [
                                'ct_headline', 'ct_image', 'ct_text_block', 'oxy_rich_text', 'ct_link_text', 'ct_svg_icon',
                                'ct_section', 'ct_div_block', 'ct_nestable_shortcode',
                                'ct_new_columns',
                                'ct_data_comment_form',
                                'ct_video',
                                'ct_link_button',
                                'ct_fancy_icon',
                                'ct_reusable',
                                'ct_shortcode',
                                'ct_widget',
                                'ct_code_block',
                                'ct_inner_content',
                                'oxy_superbox',
                                'oxy_login_form',
                                'oxy_search_form',
                                'oxy_toggle',
                                'oxy_gallery'
                               ];
                break;

            case 'ct_new_columns':
                allowedTypes = [
                                'ct_div_block', 'ct_nestable_shortcode',
                               ];
                break;

            case 'oxy_tabs':
                allowedTypes = [
                                'oxy_tab'
                               ];
                break;

            case 'oxy_tabs_contents':
                allowedTypes = [
                                'oxy_tab_content'
                               ];
                break;

            case 'oxy_tab':
            case 'oxy_tab_content':
                allowedTypes = [
                                'ct_headline', 'ct_image', 'ct_text_block', 'oxy_rich_text', 'ct_link_text', 'ct_svg_icon',
                                'ct_section', 'ct_div_block', 'ct_nestable_shortcode',
                                'ct_new_columns',
                                'ct_data_comment_form',
                                'ct_video',
                                'ct_link_button',
                                'ct_fancy_icon',
                                'ct_reusable',
                                'ct_shortcode',
                                'ct_widget',
                                'ct_code_block',
                                'ct_link', 
                                'ct_slider',
                                'oxy_social_icons',
                                'oxy_nav_menu',
                                'oxy_map',
                                'oxy_header',
                                'oxy_soundcloud',
                                'oxy_posts_grid',
                                'oxy_comments',
                                'oxy_comment_form',
                                'ct_inner_content',
                                'oxy_testimonial',
                                'oxy_icon_box',
                                'oxy_progress_bar',
                                'oxy_pricing_box',
                                'oxy_superbox',
                                'oxy_login_form',
                                'oxy_search_form',
                                'oxy_toggle',
                                'oxy_gallery', 'oxy_dynamic_list'
                               ];
                break;

        }

        
        switch (componentName) {
            case 'ct_section':
            case 'ct_div_block':
            case 'ct_nestable_shortcode':
            case 'ct_inner_content':
            case 'oxy_toggle':
            case 'oxy_icon_box':
            case 'oxy_pricing_box':
            case 'oxy_header_right':
            case 'oxy_header_center':
            case 'oxy_header_left':
            case 'ct_slide':
            case 'ct_link':
            case 'oxy_tab':
            case 'oxy_tab_content':
            
            // make all API Elements nestable inside above default Oxygen elements
            
            for(var key in $scope.componentsTemplates) { 
                if ($scope.componentsTemplates.hasOwnProperty(key)) {
                    //var template = $scope.componentsTemplates[key];
                    allowedTypes.push(key);
                }
            }
        }

        return allowedTypes;

    }

    $scope.trustedVideoSource = function(id) {
        var source = $scope.component.options[id]['model'].embed_src;
        
        if(!source || source.length === 0) {
            return $scope.trustedSource('https://www.youtube.com/embed/qgcX0y1Nzhs');
        }

        return $scope.trustedSource(source);
    }

    $scope.trustedSource = function(source) {
        return $sce.trustAsResourceUrl(source);
    }

    $scope.trustedHTML = function(content) {
        return $sce.trustAsHtml(content);
    }

    $scope.getCustomElementAttributes = function() {
        
    }

    /**
     * Get Component Tempalte based on its name
     * 
     * @since 0.1
     * @author Ilya K.
     * @author Jason
     */

    $scope.getComponentTemplate = function (componentName, id, type, domNodeFor, parent_id) {

        if ($scope.log) {
            console.log("getComponentTemplate()", componentName, id, type);
        }

        var activateAttr = 'ng-mousedown="activateComponent('+id+ ', \''+componentName+'\', $event);" ';
        if (CtBuilderAjax.userEditOnly=="true" && !$scope.isElementEnabledForUser(componentName) && componentName === "ct_code_block"){
            activateAttr = "";
        }

        var options   = activateAttr +
                        'ng-attr-component-id="'+id+'" ' + 
                        'ctevalconditions ' +
                        'ng-class="{\'ct_hidden_by_conditional_logic\': component.options['+id+ '][\'model\'][\'globalConditionsResult\'] === false, \'ct-active\' : parentScope.isActiveId('+id+'),\'ct-active-parent\' : parentScope.isActiveParentId('+id+')&&globalSettings.indicateParents==\'true\''+
                        ((componentName == 'oxy_dynamic_list')?',\'oxy_list_render_single oxy-dynamic-list-edit\':component.options['+id+ '][\'model\'][\'listrendertype\']' :'') +
                        '}" ' +
                        'id="{{component.options['+id+'].selector}}" ' + 
                        'data-aos-enabled="{{getOption(\'aos-enable\','+id+')}}"' +
                        'data-aos-duration="'+$scope.getOption('aos-duration', id)+'"'+
                        'data-aos-easing="'+$scope.getOption('aos-easing', id)+'"'+
                        'data-aos-offset="'+$scope.getOption('aos-offset', id)+'"'+
                        'data-aos-delay="'+$scope.getOption('aos-delay', id)+'"'+
                        'data-aos-anchor="'+$scope.getOption('aos-anchor', id)+'"'+
                        'data-aos-anchor-placement="'+$scope.getOption('aos-anchor-placement', id)+'"'+
                        'data-aos-once="'+$scope.getOption('aos-once', id)+'"';

            classes     = 'class="{{getComponentsClasses('+id+', \''+componentName+'\')}}'+ ((type === 'data') ? " ct-data-component" : "") +'" ',

            template    = "";

            // Attributes for drag and drop
            dndListAttr      = 'dnd-list="" ' +
                               'dnd-allowed-types="getDNDAllowedTypes(\'' + componentName + '\')" ' +
                               'dnd-type="\'' + componentName + '\'" ' +
                               'dnd-dragover="dragoverCallback(\'{{component.options['+id+'].selector}}\', event, external, type)" ',

            dndListAttrHorizontal = 'dnd-horizontal-list="isHorizontal" ',
            
            dndDisableIf = '';
            if(!$scope.isChrome) {
                dndDisableIf = 'dnd-disable-if="getDragDisabledState()" ';
            }
 
            dndDraggableAttr = 'dnd-draggable="" ' +
                               dndDisableIf +
                               'dnd-effect-allowed="move" ' +
                               'dnd-type="\'' + componentName + '\'" ' +
                               'dnd-dragstart="dragstartCallback('+id+ ',\'{{component.options['+id+'].selector}}\', event)" ' +
                               'dnd-dragend="dragendCallback('+id+ ', \''+componentName+'\', event)" ';

        // add "data-aos" AOS attribute only when at least some aos-type defined, empty "data-aos" cause lags
        if ($scope.getOption('aos-enable',id)==='true') {
            var dataAOS = $scope.getOption('aos-type',id)||$scope.pageSettingsMeta['aos']['type']||$scope.pageSettings['aos']['type']||iframeScope.globalSettings['aos']['type'];
            if (dataAOS) {
                options += 'data-aos="'+dataAOS+'"';
            }
        }

        // If component is Re-usable, dndDraggableAttr will be overwritten.
        if(componentName == "ct_reusable") {
            dndDraggableAttr = 'dnd-draggable="" ' +
                           dndDisableIf +
                           'dnd-effect-allowed="move" ' +
                           'dnd-type="\'{{component.options['+id+'].dndtype}}\'" ' +
                           'dnd-dragstart="dragstartCallback('+id+ ',\'{{component.options['+id+'].selector}}\', event)" ' +
                           'dnd-dragend="dragendCallback('+id+ ', \''+componentName+'\', event)" ';
        }

        // remove dnd attributes for outer content excluding the Inner Content
        if (id >= 100000 && componentName != "ct_inner_content") {
            dndListAttr = ""; 
            dndListAttrHorizontal = "";
            dndDraggableAttr = "";
        }

        if (CtBuilderAjax.userCanFullAccess!="true"&&CtBuilderAjax.userCanDragNDrop!="true") {
            dndListAttr = ""; 
            dndListAttrHorizontal = "";
            dndDraggableAttr = "";
        }

        // we can't drag into Inner Content of the parent template if editing template
        if (componentName == "ct_inner_content" && id < 100000 && CtBuilderAjax.oxyTemplate) {
            dndListAttr = ""; 
            dndListAttrHorizontal = "";
        }

        // we can't drag the Inner Content if not editing template
        if (componentName == "ct_inner_content" && !CtBuilderAjax.oxyTemplate) {
            dndDraggableAttr = "";
        }

        // remove dnd attributes for builtin components
        if (type=='builtin') {
            //dndListAttr = ""; 
            //dndListAttrHorizontal = "";
            dndDraggableAttr = "";
        }

        if( componentName == 'ct_toolset_view' ) {
            type = "shortcode";
        }

        if (type != "shortcode" && type != "widget" && type != "sidebar" && type != "nav_menu" && type != "data") {

            switch (componentName) {

                case 'ct_section':

                    var tag = $scope.regexTestCustomTag($scope.component.options[id]['model'].tag) ? $scope.component.options[id]['model'].tag : "div",
                        pseOpen = pseClose = '';

                    classes = 'class="ct-section {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';

                    template = pseOpen+'<'+tag+' is-nestable="true" ' + options + classes + dndDraggableAttr + '>' +
                                    '<div class="oxy-video-container">' +
                                        '<video autoplay loop playsinline muted>' +
                                            '<source ng-src="{{component.options['+id+'][\'model\'][\'video_background\']}}">' +
                                        '</video>' +
                                        '<div class="oxy-video-overlay"></div>' +
                                    '</div>' +
                                    '<div class="ct-section-inner-wrap ct-inner-wrap" ' + dndListAttr + dndListAttrHorizontal + '>' +
                                    '</div>' +
                                '</'+tag+'>'+pseClose;
                    
                    break

                case 'ct_modal':

                    classes = 'class="ct-modal {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';

                    template = '' +
                        '<div class="oxy-modal-backdrop {{ component.options[' + id + '][\'model\'][\'modal_position\'] }} {{getGlobalConditionsClass('+id+')}}" ng-style="{\'background-color\': getGlobalColorValue(component.options[' + id + '][\'model\'][\'backdrop-color\']) }" ng-class="{live: component.options[' + id + '][\'model\'][\'behavior\'] == 2, hidden: component.options[' + id + '][\'model\'][\'behavior\'] == 3}" ng-click="activateModalComponent(' + id + ', $event)">' +
                        '   <div is-nestable="true" ' + options + classes + '></div>' +
                        '</div>';

                    break

                case 'ct_columns':

                    classes = 'class="ct-columns {{getComponentsClasse('+id+', \''+componentName+'\')}}" ';

                    template = '<div ' + options + classes + '>' +
                                    '<div class="ct-columns-inner-wrap ct-inner-wrap" ng-class="checkEmptyColumns('+id+')"></div>'+
                                '</div>';

                    break

                case 'ct_column': 

                    classes = 'class="ct-column {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    
                    template = '<div is-nestable="true" ' + options + classes + '></div>';
                    break

                case 'ct_headline':

                    var tag = $scope.filterCustomTag($scope.component.options[id]['model'].tag);

                    template = '<'+tag+' contenteditable="false" ng-model="component.options['+id+'][\'model\'].ct_content" ng-model-options="{ debounce: 10 }" ' + options + classes + dndDraggableAttr +'></'+tag+'>';
                    break

                case 'ct_image':

                    var attachment_id = $scope.getOption('attachment_id');

                    if (attachment_id.indexOf && 
                        attachment_id.indexOf('[oxygen') > -1) {

                        var timeout = $timeout(function() {
                            $scope.processImageSizes(id)
                            
                            // cancel timeout
                            $timeout.cancel(timeout);
                        }, 0, false);
                    }
                    

                    var ngsrc = 'ng-src="{{component.options[' + id + '][\'model\'].image_type == 1 ? component.options[' + id + '][\'model\'].src : component.options[' + id + '][\'model\'].attachment_url }}" ';

                    if ($scope.component.options[id]['model'].src.indexOf('[oxygen') > -1 && $scope.component.options[id]['model'].image_type != 2 ) {
                        ngsrc = '';
                    }

                    template = '<img ' + ngsrc + options + classes + dndDraggableAttr + ' oxyimageonload/>';
                    break

                case 'ct_video':
                    template = '<div '+ options + classes + dndDraggableAttr + '>';
                    template += '<div ng-if="component.options['+id+'][\'model\'][\'use-custom\'] !== \'1\'" class="oxygen-vsb-responsive-video-wrapper"><iframe ng-src="{{trustedVideoSource('+id+')}}" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
                    template += '<div ng-if="component.options['+id+'][\'model\'][\'use-custom\'] === \'1\'" class="oxygen-vsb-responsive-video-wrapper oxygen-vsb-responsive-video-wrapper-custom" ng-bind-html="trustedHTML(component.options['+id+'][\'model\'][\'custom-code\'])"></div>';
                    template += '</div>';
                    break

                case 'ct_text_block':
                    
                    var tag = $scope.regexTestCustomTag($scope.component.options[id]['model'].tag)? $scope.component.options[id]['model'].tag : "div";

                    template = '<'+tag+' contenteditable="false" ng-model="component.options['+id+'][\'model\'].ct_content" ng-model-options="{ debounce: 10 }" ' + options + classes + dndDraggableAttr + '></'+tag+'>';
                    break

                case 'ct_paragraph':

                    template = '<div ng-attr-paragraph="true" contenteditable="false" ng-model="component.options['+id+'][\'model\'].ct_content" ng-model-options="{ debounce: 10 }" ' + options + classes + '></div>';
                    break

                case 'ct_div_block':

                    var tag = $scope.filterCustomTag($scope.component.options[id]['model'].tag);

                    template = '<'+tag+' is-nestable="true" ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + '>' +
                                '</'+tag+'>';
                    break

                case 'ct_nestable_shortcode':
                    
                    classes = 'class="ct-nestable-shortcode {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';

                    template = '<div ctrendernestableshortcode ct-nestable-shortcode-model="component.options['+id+'][\'model\'].wrapping_shortcode" is-nestable="true" ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + '>' +
                                '</div>';
                    break

                case 'ct_new_columns':

                    template = '<div ' + options + classes + dndListAttr + dndListAttrHorizontal + dndDraggableAttr + '>' +
                                '</div>';
                    break

                case 'ct_ul':

                    template = '<ul is-nestable="true" ' + options + classes + '></ul>';
                    break

                case 'ct_li':

                    template = '<li contenteditable="false" ng-model="component.options['+id+'][\'model\'].ct_content" ng-model-options="{ debounce: 10 }"' + options + classes + '></li>';
                    break

                case 'ct_span':

                    var item = $scope.findComponentItem(domNodeFor? $scope.dynamicListTrees['trees'][domNodeFor] :$scope.componentsTree.children, id, $scope.getComponentItem);
                    var parent = $scope.findComponentItem(domNodeFor? $scope.dynamicListTrees['trees'][domNodeFor] :$scope.componentsTree.children, item['options']['ct_parent'], $scope.getComponentItem);

                    var oxyContent = false,
                    matches = item['options']['ct_content'].match(/\[oxygen[^\]]*\]/i);

                    if(matches) {
                        oxyContent = matches[0];
                    }

                    if(oxyContent && (parent.name === 'ct_headline' ||
                        parent.name === 'ct_text_block' ||
                        parent.name === 'ct_paragraph' ||
                        parent.name === 'ct_li' ||
                        parent.name === 'ct_link_text' ||
                        parent.name === 'oxy_icon_box' ||
                        parent.name === 'oxy_testimonial' ||
                        parent.name === 'ct_link_button')) {

                        classes = 'class="ct-contains-oxy {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                        template = '<span ng-attr-span="true" '+(domNodeFor?'':'ctrenderoxyshortcode') +' ng-model="component.options['+id+'][\'model\'].ct_content" ng-model-options="{ debounce: 10 }" ' + 'ng-attr-component-id="'+id+'" ' + 
                        'ng-class="{\'ct-active\' : parentScope.isActiveId('+id+'),\'ct-active-parent\' : parentScope.isActiveParentId('+id+')&&globalSettings.indicateParents==\'true\'}" ' +
                        'id="{{component.options['+id+'].selector}}" ' + classes + '></span>';
                    }
                    else {
                        template = '<span ng-attr-span="true" contenteditable="false" ng-model="component.options['+id+'][\'model\'].ct_content" ng-model-options="{ debounce: 10 }" ' + options + classes + '></span>';
                    }
                    
                    break

                case 'ct_link_text':

                    template = '<a href="{{component.options['+id+'][\'model\'].url}}" contenteditable="false" ng-model="component.options['+id+'][\'model\'].ct_content" ng-model-options="{ debounce: 10 }" ' + options + classes + dndDraggableAttr + '></a>';
                    break

                case 'ct_link_button':

                    template = '<a href="{{component.options['+id+'][\'model\'].url}}" contenteditable="false" ng-model="component.options['+id+'][\'model\'].ct_content" ng-model-options="{ debounce: 10 }" ' + options + classes + dndDraggableAttr + '></a>';
                    break

                case 'ct_link':

                    template = '<a href="{{component.options['+id+'][\'model\'].url}}" ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + 'id="{{component.options['+id+'].selector}}" is-nestable="true" ng-attr-component-id="' + id + '"></a>';
                    break

                case 'ct_svg_icon':
                    classes = 'class="svg_wrapper {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    template = '<div ' + options + classes + dndDraggableAttr +'><svg><use xlink:href="" ng-href="{{\'#\'+component.options['+id+'][\'model\'][\'icon-id\']}}"></use></svg></div>';
                    //template = '<div class="svg_wrapper" ' + dndDraggableAttr + '><svg ' + options + classes + '><use xlink:href="" ng-href="{{\'#\'+component.options['+id+'][\'model\'][\'icon-id\']}}"></use></svg></div>';
                    break

                case 'ct_fancy_icon':
                    
                    classes = 'class="svg_wrapper {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    var iconId = $scope.component.options[id]['model']['icon-id'];

                    // var iconContent = jQuery('div');

                    // iconContent.append('<?xml version="1.0"?>');

                    // iconContent.append('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1">');

                    // iconContent.append(jQuery('defs'));

                    // var iconSymbol = jQuery('#'+iconId, window.parent.document);

                    // var iconTitle = iconSymbol.next('title');
                    // var iconPath = iconTitle.next('path');
                    
                    template = '<div ' + options + classes + dndDraggableAttr +'><svg id="svg-{{component.options['+id+'].selector}}"><use xlink:href="" ng-href="{{\'#\'+component.options['+id+'][\'model\'][\'icon-id\']}}"></use></svg></div>';
                    break

                case 'ct_reusable':

                    template = '<div ' + options + classes + dndDraggableAttr +'></div>';
                    break

                case 'ct_separator':

                    template = '<div ' + options + classes + '></div>';
                    break

                case 'ct_code_block':

                    var tag = $scope.filterCustomTag($scope.component.options[id]['model'].tag);

                    template = '<'+tag+' ' + options + classes + dndDraggableAttr + '></'+tag+'>';
                    var timeout = $timeout(function() {
                        $scope.applyCodeBlock(id, false);
                        
                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);
                    break

                case 'ct_inner_content':
                    var tag = $scope.filterCustomTag($scope.component.options[id]['model'].tag);
                    var workArea = '';
                    if(id >= 100000) {
                        workArea = ' ct-inner-content-workarea ';
                    }
                    classes = 'class="ct-inner-content'+workArea+' {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';

                   // var placeholder = '<div class="ct-inner-content-placeholder">Page/Post specific content</div>';
                    template = '<'+tag+' is-nestable="true" ' + 
                        'ng-mousedown="activateComponent('+id+ ', \''+componentName+'\', $event);" ' +
                        'ng-attr-component-id="'+id+'" ' + 
                        'ng-class="{\'ct-active\' : parentScope.isActiveId('+id+')}" ' +
                        'id="{{component.options['+id+'].selector}}" ' + classes + dndDraggableAttr + dndListAttr + '></'+tag+'>';

                    if(id < 100000) {
                        var timeout = $timeout(function() {
                            if($scope.template.postsList || $scope.template.termsList) {
                                $scope.renderInnerContent(id, componentName);
                            }
                            // cancel timeout
                            $timeout.cancel(timeout);
                        }, 0, false);
                    }
                    break


                case 'oxy_header':

                    classes = 'class="oxy-header-wrapper {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    template = '<header ' + options + classes + dndDraggableAttr + dndListAttr + '>' +
                                '</header>';
                    break

                case 'oxy_header_row':

                    classes = 'class="oxy-header-row {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    template = '<div ' + options + classes + dndDraggableAttr +' dnd-disable-if="isLastRow('+id+')">' +
                                    '<div class="oxy-header-container ct-inner-wrap" ng-class="checkEmptyHeaderRow('+id+')">' +
                                    '</div>' +
                               '</div>';
                    break

                case 'oxy_header_left':
                case 'oxy_header_center':
                case 'oxy_header_right':

                    template = '<div ' + dndListAttr + 'is-nestable="true" ng-attr-component-id="'+id+'" id="{{component.options['+id+'].selector}}" ' +
                               'ng-class="{\'ct-active\':parentScope.isActiveId('+id+'),\'ct-active-parent\':parentScope.isActiveParentId('+id+')&&globalSettings.indicateParents==\'true\'}" ' + classes + '></div>';
                    break

                case 'ct_slider':

                    classes = 'class="ct-slider {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    template = '<div is-nestable="true" ' + options + classes + dndDraggableAttr +'><div class="oxygen-unslider-container" oxygen-slider="true"><ul></ul></div></div>';
                    break

                case 'ct_slide':

                    template = '<li><div is-nestable="true" ' + options + classes + dndListAttr + dndListAttrHorizontal +'></div></li>';
                    break

                case 'oxy_map':

                    template = '<div ' + options + classes + dndDraggableAttr + '><iframe ng-src="{{getMapURL('+id+')}}" frameborder=0></iframe></div>';
                    break

                case 'oxy_social_icons':
                        
                    var html = "";
                        
                    for(var key in $scope.socialIcons.networks) { 
                        if ($scope.socialIcons.networks.hasOwnProperty(key)) {
                            var network = $scope.socialIcons.networks[key];
                            if ($scope.component.options[id]['model']['icon-'+network]) {
                                html += "<a href='' class='oxy-social-icons-"+network+"'><svg><use xlink:href=\"\" ng-href=\"#oxy-social-icons-icon-"+network+"{{component.options["+id+"]['model']['icon-style']=='blank'?'-blank':''}}\"></use></svg></a>";
                            }
                        }
                    }

                    template = '<div ' + options + classes + dndDraggableAttr + '>' + html + '</div>';
                    break

                case 'oxy_soundcloud':

                    template = '<div ' + options + classes + dndDraggableAttr + '><iframe width="100%" height="{{component.options['+id+"]['model']['height']+component.options["+id+"]['model']['height-unit']}}\" ng-src=\"{{getSoundCloudURL("+id+")}}\" frameborder=0></iframe></div>";
                    break

                case 'oxy_dynamic_list':

                    var tag = $scope.filterCustomTag($scope.component.options[id]['model'].tag);

                    classes = 'class="oxy-dynamic-list {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    template = '<'+tag+' is-nestable="true" ctdynamiclist dynamic-actions="dynamicListActions" dynamic-list-options="dynamicListOptions" ' + options + classes + dndDraggableAttr + '></'+tag+'>';

                    break

                case 'oxy_posts_grid':

                    template = '<div class="ct-component oxy-easy-posts oxy-posts-grid {{getGlobalConditionsClass('+id+')}}" ' + options + dndDraggableAttr + '></div>';
                    var timeout = $timeout(function() {
                        $scope.renderComponentWithAJAX('oxy_render_easy_posts', id);
                        
                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);
                    break

                 case 'oxy_comments':

                    template = '<div ' + options + classes + dndDraggableAttr + '></div>';
                    var timeout = $timeout(function() {
                        $scope.renderComponentWithAJAX('oxy_render_comments_list', id);
                        
                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);
                    break

                case 'oxy_comment_form':

                    template = '<div ' + options + classes + dndDraggableAttr + '></div>';
                    var timeout = $timeout(function() {
                        $scope.renderComponentWithAJAX('oxy_render_comment_form', id);
                        
                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);
                    break

                case 'oxy_login_form':

                    template = '<div ' + options + classes + dndDraggableAttr + '></div>';
                    var timeout = $timeout(function() {
                        $scope.renderComponentWithAJAX('oxy_render_login_form', id);
                        
                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);
                    break

                case 'oxy_search_form':

                    template = '<div ' + options + classes + dndDraggableAttr + '></div>';
                    var timeout = $timeout(function() {
                        $scope.renderComponentWithAJAX('oxy_render_search_form', id);
                        
                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false);
                    break

                case 'oxy_gallery':

                    var currentIndex = $scope.repeaterListCurrentIndex;

                    template = '<div ' + options + classes + dndDraggableAttr + '></div>';
                    var timeout = $timeout(function() {
                        $scope.renderComponentWithAJAX('oxy_render_gallery',id,currentIndex);
                        
                        // cancel timeout
                        $timeout.cancel(timeout);
                    }, 0, false, currentIndex);
                    break

                case 'oxy_rich_text':

                    var tag = $scope.filterCustomTag($scope.component.options[id]['model'].tag);

                    template = '<'+tag+' ng-dblclick="parentScope.openTinyMCEDialog()"' + options + classes + dndDraggableAttr + 'ng-bind-html="trustedHTML(component.options['+id+"]['model']['ct_content'])\">" + '</'+tag+'>';
                    break

                case 'oxy_testimonial':
                        
                    template = 
                    '<div ' + options + classes + dndDraggableAttr + '>'+
                        '<div class="oxy-testimonial-photo-wrap">'+
                           '<img class="oxy-testimonial-photo" ng-src="{{component.options['+id+'][\'model\'].testimonial_photo}}" oxyimageonload/>'+
                        '</div>'+
                        '<div class="oxy-testimonial-content-wrap">'+
                            '<div class="oxy-testimonial-text" contenteditable="false" data-optionname="testimonial_text" ng-model="component.options['+id+'][\'model\'].testimonial_text" ng-model-options="{ debounce: 10 }" ></div>' +
                            '<div class="oxy-testimonial-author-wrap">'+
                                '<div class="oxy-testimonial-author" contenteditable="false" data-optionname="testimonial_author" ng-model="component.options['+id+'][\'model\'].testimonial_author" ng-model-options="{ debounce: 10 }" ></div>' +
                                '<div class="oxy-testimonial-author-info" contenteditable="false" data-optionname="testimonial_author_info" ng-model="component.options['+id+'][\'model\'].testimonial_author_info" ng-model-options="{ debounce: 10 }" ></div>' +
                            '</div>'+
                        '</div>'+
                    '</div>';

                    break

                 case 'oxy_icon_box':

                    // oxy-icon-box class should be there from very start to proper nesting into ct-inner-wrap
                    classes = 'class="oxy-icon-box {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    
                    template = 
                    '<div is-nestable="true" ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + '>'+
                        '<div class="oxy-icon-box-icon oxy-builtin-container">'+
                        '</div>'+
                        '<div class="oxy-icon-box-content">'+
                            '<h2 class="oxy-icon-box-heading" contenteditable="false" data-optionname="icon_box_heading" ng-model="component.options['+id+'][\'model\'].icon_box_heading" ng-model-options="{ debounce: 10 }" ></h2>' +
                            '<p class="oxy-icon-box-text" contenteditable="false" data-optionname="icon_box_text" ng-model="component.options['+id+'][\'model\'].icon_box_text" ng-model-options="{ debounce: 10 }" ></p>' +
                            '<div ' + dndListAttr + dndListAttrHorizontal + 'class="oxy-icon-box-link ct-inner-wrap">'+
                            '</div>'+
                        '</div>'+
                    '</div>';

                    break
                    
                case 'oxy_progress_bar':
                    
                    template = '<div ' + options + classes + dndDraggableAttr + '>'+
                      '<div class="oxy-progress-bar-background">'+
                        '<div class="oxy-progress-bar-progress-wrap">'+
                          '<div class="oxy-progress-bar-progress">'+
                            '<div class="oxy-progress-bar-overlay-text" contenteditable="false" data-optionname="progress_bar_left_text" ng-model="component.options['+id+'][\'model\'].progress_bar_left_text" ng-model-options="{ debounce: 10 }"></div>'+
                            '<div class="oxy-progress-bar-overlay-percent" contenteditable="false" data-optionname="progress_bar_right_text" ng-model="component.options['+id+'][\'model\'].progress_bar_right_text" ng-model-options="{ debounce: 10 }">'+
                            '</div>'+
                          '</div>'+
                        '</div>'+
                      '</div>'+
                    '</div>';

                    break

                case 'oxy_pricing_box':

                    // oxy-icon-box class should be there from very start to proper nesting into ct-inner-wrap
                    classes = 'class="oxy-pricing-box {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';

                    template = '<div is-nestable="true"' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + '>'+

                        "<div class='oxy-pricing-box-graphic oxy-pricing-box-section oxy-builtin-container' ng-show='component.options["+id+"][\"model\"].pricing_box_include_graphic==\"yes\"'></div>" + 
                        "<div class='oxy-pricing-box-title oxy-pricing-box-section'>"+
                            '<div class="oxy-pricing-box-title-title" contenteditable="false" data-optionname="pricing_box_package_title" ng-model="component.options['+id+'][\'model\'].pricing_box_package_title" ng-model-options="{ debounce: 10 }"></div>'+
                            '<div class="oxy-pricing-box-title-subtitle" contenteditable="false" data-optionname="pricing_box_package_subtitle" ng-model="component.options['+id+'][\'model\'].pricing_box_package_subtitle" ng-model-options="{ debounce: 10 }"></div>'+
                        "</div>" +
                        '<div ng-if="component.options['+id+'][\'model\'].pricing_box_include_features!=\'no\'" class="oxy-pricing-box-content oxy-pricing-box-section" contenteditable="false" data-optionname="pricing_box_content" ng-model="component.options['+id+'][\'model\'].pricing_box_content" ng-model-options="{ debounce: 10 }"></div>'+
                        "<div class='oxy-pricing-box-price oxy-pricing-box-section'>" +
                            '<span class="oxy-pricing-box-sale-price" contenteditable="false" data-optionname="pricing_box_package_regular" ng-model="component.options['+id+'][\'model\'].pricing_box_package_regular" ng-model-options="{ debounce: 10 }"></span>'+
                            "<span class='oxy-pricing-box-amount'>" +
                                "<span class='oxy-pricing-box-currency'>{{component.options["+id+"][\'model\'].pricing_box_price_amount_currency}}</span>"+
                                "<span class='oxy-pricing-box-amount-main'>{{component.options["+id+"][\'model\'].pricing_box_price_amount_main}}</span>"+
                                "<span class='oxy-pricing-box-amount-decimal'>{{component.options["+id+"][\'model\'].pricing_box_price_amount_decimal}}</span>" +
                            "</span>" +
                            "<span class='oxy-pricing-box-term'>" +
                                "<span class='oxy-pricing-box-amount-term'>{{component.options["+id+"][\'model\'].pricing_box_price_amount_term}}</span>" +
                            "</span>" +
                        "</div>" +
                        '<div class="oxy-pricing-box-cta oxy-pricing-box-section ct-inner-wrap">'+
                        '</div>' +
                    "</div>";
                
                    break

                case 'oxy_tabs':
                    classes = 'class="oxy-tabs-wrapper {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    template = '<div is-nestable="true" ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + 
                                    'data-oxy-tabs-active-tab-class="{{component.options['+id+'][\'model\'].active_tab_class}}"' + 
                                    'data-oxy-tabs-contents-wrapper="{{component.options['+id+'][\'model\'].tabs_contents_wrapper}}"' + '>' +
                                '</div>';
                    break

                case 'oxy_tabs_contents':
                    classes = 'class="oxy-tabs-contents-wrapper {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    template = '<div is-nestable="true" ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + 
                               'data-oxy-tabs-wrapper="{{component.options['+id+'][\'model\'].tabs_wrapper}}"' + '>' +
                               '</div>';
                    break

                case 'oxy_tab':
                case 'oxy_tab_content':

                    template = '<div is-nestable="true" ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + '>' +
                                '</div>';
                    break

                case 'oxy_superbox':
                    template = '<div ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + '>' +
                                    '<div class="oxy-superbox-wrap oxy-builtin-container">'+ 
                                    '</div>' +
                               '</div>';
                    break

                case 'oxy_toggle':
                    classes = 'class="oxy-toggle {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                    template = '<div is-nestable="true" ' + options + classes + dndDraggableAttr + dndListAttr + dndListAttrHorizontal + 
                                    'data-oxy-toggle-active-class="{{component.options['+id+'][\'model\'].toggle_active_class}}"' + 
                                    'data-oxy-toggle-target="{{component.options['+id+'][\'model\'].toggle_target}}"' + 
                                    'data-oxy-toggle-initial-state="{{component.options['+id+'][\'model\'].toggle_init_state}}"' + '>' +
                                    '<div class="oxy-expand-collapse-icon" href="#"></div>' +
                                    '<div class="oxy-toggle-content ct-inner-wrap">' +
                                    '</div>' +
                                '</div>';
                    break

                default:
                    var componentTemplate = "No template found",
                        nestable = "",
                        html = js = "",
                        componentClass = "",
                        attrs = "";

                    // Look if any 
                    if ($scope.componentsTemplates !== undefined && $scope.componentsTemplates[componentName]) {
                        componentTemplate = $scope.componentsTemplates[componentName];

                        // check if component is nestable
                        if (componentTemplate['nestable']=='true') {
                            nestable = 'is-nestable="true" ';
                        }

                        if (typeof($scope.component.options[id]['model']['renderedHTML']) === 'undefined' && 
                            componentTemplate.phpCallback) {
                            // render with AJAX
                            var timeout = $timeout(function() {
                                $scope.renderComponentWithAJAX('oxy_render_' + componentName, id);
                                // cancel timeout
                                $timeout.cancel(timeout);
                            }, 0, false);

                        } else {
                            // prepare HTML code
                            html = $scope.parseAPIOptions(typeof($scope.component.options[id]['model']['renderedHTML']) !== 'undefined' ? $scope.component.options[id]['model']['renderedHTML'] : componentTemplate.html, id);
                        }
                        
                        // inline JS
                        if (componentTemplate['js'] !== undefined && componentTemplate['js']) {
                            var timeout = $timeout(function() {
                                var js = $scope.replaceComponentOptions(componentTemplate['js'], id);
                                try {
                                    eval(js);
                                }
                                catch(err) {
                                    $scope.showNoticeModal("<div>Error in "+componentName+" inline JS code. See console for more details.</div>");
                                    $scope.$apply();
                                    console.log(js);
                                    console.error(err);
                                }
                                // cancel timeout
                                $timeout.cancel(timeout);
                            }, 500, false);
                        }
                        
                        // add wrapper class if defined
                        if (componentTemplate.class!==undefined && componentTemplate.class) {
                            componentClass = componentTemplate.class;
                        }
                        // check if content editable added somewhere
                        html = html.replace(/OXY_EDITABLE\([^)]+\)/g, function(match) {
                            // get the value inside the parentheses
                            var regExp = /\(([^)]+)\)/,
                                match = regExp.exec(match);
                            
                            option = match[1].split('|');

                            return 'contenteditable="false" data-optionname="'+option[0]+'" ng-model="component.options['+id+'][\'model\'][\''+option[0]+'\']"';
                        });

                        if (componentTemplate.rebuildOnDOMChange) {
                            attrs += " data-rebuild-on-dom-change";
                        }

                        if (componentTemplate.Attributes) {
                            for(var key in componentTemplate.Attributes) { 
                                if (componentTemplate.Attributes.hasOwnProperty(key)) {
                                    var attr = componentTemplate.Attributes[key];
                                    if (attr['name']) {
                                        attrs += " "+attr['name']+"='"+attr['value']+"'";
                                    }
                                }
                            }
                        }
                    }
                    
                    var tag = $scope.component.options[id]['model']['html_tag'] || componentTemplate['HTMLTag'] || "div";

                    // elements generated by the Repeater doesn't load with AJAX
                    if (!$scope.isRepeaterGeneratedElement(id)) {
                        componentClass += " oxy-ajax-loading ";
                    }

                    if ($scope.defaultOptions[componentName]['not-registered']) {
                        template = "<div class='oxy-not-registered-element' "+ options +">Element Not Registered</div>";
                    }
                    else {
                        classes = 'class="'+componentClass+' {{getComponentsClasses('+id+', \''+componentName+'\')}}" ';
                        template = '<'+tag + ' ' + nestable + options + classes + dndDraggableAttr + attrs + '>'+html+'</'+tag+'>'+js;
                    }
            }
        }

        // shortcodes 
        else if ( type == "shortcode" ) {

            var tag = $scope.filterCustomTag($scope.component.options[id]['model'].tag);

            template = '<'+tag+' ' + options + classes + dndDraggableAttr + '></'+tag+'>';
            
            var timeout = $timeout(function() {
                $scope.renderShortcode(id, componentName);
                
                // cancel timeout
                $timeout.cancel(timeout);
            }, 0, false);
        }

        // widgets
        else if ( type == "widget" ) {

            template = '<div ' + options + classes + dndDraggableAttr + '></div>';

            var timeout = $timeout(function() {
                $scope.renderWidget(id);
                
                // cancel timeout
                $timeout.cancel(timeout);
            }, 0, false);
        }

        // nav menu
        else if ( type == "nav_menu" ) {

            template = '<nav ' + dndDraggableAttr + options + classes + '></nav>';

            var timeout = $timeout(function() {
                $scope.renderNavMenu(id);
                
                // cancel timeout
                $timeout.cancel(timeout);
            }, 0, false);
        }

        // data
        else if ( type == "data" ) {

			var tag = $scope.filterCustomTag($scope.component.options[id]['model'].tag);

			switch(componentName) {
				case 'ct_data_featured_image':
				case 'ct_data_author_avatar':
					template = '<img ng-src="{{component.options[' + id + '][\'model\'].src}}"' + options + classes + dndDraggableAttr + '/>';
					break;
				default:
					template = '<' + tag + ' ' + options + classes + dndDraggableAttr + '></' + tag + '>';
			}

			var timeout = $timeout(function() {
				$scope.renderDataComponent(id, componentName);

				// cancel timeout
				$timeout.cancel(timeout);
			}, 0, false);
        }

        // sidebar
        else if ( type == "sidebar" ) {

            template = '<div ' + options + classes + '></div>';

            var timeout = $timeout(function() {
                $scope.renderSidebar(id);
                
                // cancel timeout
                $timeout.cancel(timeout);
            }, 0, false);

        }

        if ( componentName != "ct_code_block" ) {
             
            var timeout = $timeout(function() {
                $scope.applyComponentJS(id, componentName, false);

                // cancel timeout
                $timeout.cancel(timeout);
            }, 0, false);
        }

        var customAttributesTimeout = $timeout(function() {
            $scope.applyCustomAttributes(id);

            // cancel timeout
            $timeout.cancel(customAttributesTimeout);
        }, 0, false);

        return template;
    }


    /**
     * Change element tag name
     * 
     * @since 0.1.7
     */

    $scope.changeTag = function (type, id, name) {

        if (undefined==id) {
            id = $scope.component.active.id;
        }
        if (undefined==name) {
            name = $scope.component.active.name;
        }

        // if it is a child inside a dynamic list component
        var component = $scope.getComponentById(id)
        var oxyList = component.closest('.oxy-dynamic-list');
        
        if(oxyList.length > 0) {
            $scope.updateRepeaterQuery(parseInt(oxyList.attr('ng-attr-component-id')));
            return;
        }

        if(component.children('span').length > 0) {
            type = 'rebuild';
        }

        // use type variable to pass a condition to rebuild DOM if nestable components like Section or Div
        if (type=='rebuild') {
            $scope.rebuildDOM(id);
            return;
        }

        // plain elements like Headline or Text Block
        var newComponent = $scope.getComponentTemplate(name, id, type),
            selector = $scope.component.options[id]['selector'];

        $scope.cleanReplace(selector, newComponent);

    }


    /**
     * Get DOM element deepest child to insert
     * 
     * @return jqLite
     * @since 0.1.3
     */
    
    $scope.getInnerWrap = function( element ) {

        if ( !element.hasClass ) {
            return element;
        }

        if ( element.hasClass('ct-slider') ) {
            
            var child = element.find(".oxygen-unslider-container > ul");
            if ( child.prop("tagName") == "UL" ) {
                return child;
            }
            else {
                return element;
            }
        }
        
        if( element.hasClass('ct-nestable-shortcode')) {

            var child = element.children('.ct_nestable_element_wrap');

            if(child.length > 0) {
                return child;
            }
            else {
                return element;
            }
        }

        if ( element.hasClass('oxy-icon-box') ) {

            var child = element.children('.oxy-icon-box-content').children('.ct-inner-wrap');
            if ( child ) {
                return child;
            }
            else {
                return element;
            }
        }

        if ( element.hasClass('ct-columns') || element.hasClass('ct-section') || element.hasClass('oxy-header-row') || element.hasClass('oxy-pricing-box') || element.hasClass('oxy-toggle')) {
        
            var child = element.children('.ct-inner-wrap');
            if ( child ) {
                return child;
            }
            else {
                return element;
            }
        }
        else {
            var child = element.find(".oxy-inner-content").first();
            if ( // child found
                 child.length > 0 &&
                 // and it is belong to this exact component and not any child component 
                 child.closest('[ng-attr-component-id]').is(element) ) 
            {
                return child;
            }
            else {
                return element;
            }
        }
    }


    /**
     * Get DOM element to insert builtin components
     * 
     * @return jqLite
     * @since 2.0
     * @author Ilya K.
     */
    
    $scope.getBuiltInWrap = function( element ) {

        var child = element.find(".oxy-builtin-container");
        if ( child ) {
            return child;
        }
        else {
            return element;
        }
    }


    /**
     * Get Google Maps URL based on component args
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.getMapURL = function(id) {
        
        return $scope.trustedSource("https://www.google.com/maps/embed/v1/place?key="+CtBuilderAjax.googleMapsAPIKey+"&q="+encodeURI($scope.component.options[id]['model'].map_address)+"&zoom="+$scope.component.options[id]['model'].map_zoom);
    }


    /**
     * Get Google Maps URL based on component args
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.getSoundCloudURL = function(id) {
        
        return $scope.trustedSource("https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/"+encodeURI($scope.component.options[id]['model']['soundcloud_track_id'])+"&amp;color="+encodeURI($scope.component.options[id]['model']['soundcloud_color'])+"&amp;auto_play="+encodeURI($scope.component.options[id]['model']['soundcloud_auto_play'])+"&amp;hide_related="+encodeURI($scope.component.options[id]['model']['soundcloud_hide_related'])+"&amp;show_comments="+encodeURI($scope.component.options[id]['model']['soundcloud_show_comments'])+"&amp;show_user=true&amp;show_reposts=false&amp;show_teaser=true&amp;visual=true" );
    }


    /**
     * Wrap selected text with <span> component
     *
     * @since 0.1.8
     */
    
    $scope.wrapWithSpan = function(returnNode) {
        
        var parentId    = $scope.component.active.id,
            parentName  = $scope.component.active.name,

            // get selection
            selection   = $scope.getUserSelection(),

            // create span
            node = document.createElement("span"),
            att  = document.createAttribute("id"),

            // get current component DOM node
            parent = $scope.getActiveComponent();
        
        att.value = "ct-placeholder-" + $scope.component.id;
        node.setAttributeNode(att);

        // update selection
        $scope.replaceUserSelection(node);

        var newComponent = {
            id : $scope.component.id, 
            name : "ct_span"
        }

        // set default options first
        $scope.applyComponentDefaultOptions(newComponent.id, "ct_span");

        // insert new component to Components Tree
        $scope.findComponentItem($scope.componentsTree.children, $scope.component.active.id, $scope.insertComponentToTree, newComponent);

        // update span options
        $scope.component.options[newComponent.id]["model"]["ct_content"] = selection;
        $scope.setOption(newComponent.id, "ct_span", "ct_content");

        // update parent options
        $scope.component.options[parentId]["model"]["ct_content"]       = parent.html();
        $scope.component.options[parentId]["original"]["ct_content"]    = parent.html();
        $scope.setOption(parentId, parentName, "ct_content");

        $scope.component.id++;

        $scope.rebuildDOM(parentId);

        // activate component
        var timeout = $timeout(function() {
            $scope.activateComponent(newComponent.id, "ct_span");
            // cancel timeout
            $timeout.cancel(timeout);
        }, 0, false);

        if(typeof(returnNode) !== 'undefined')
            return node;
    }


    /**
     * Wrap selected text with <a> tag (not a component)
     *
     * @since 0.1.5
     * @author Ilya K.
     */
    
    $scope.wrapWithLink = function() {

        // get selection
        var sel = $scope.getUserSelection();

        // create link
        var node = document.createElement("a");
        node.appendChild(document.createTextNode(sel));

        // set URL
        var url = prompt("Define link URL", "http://");
        if (url != null) {
            var att = document.createAttribute("href");
            att.value = url;
            node.setAttributeNode(att);
        }
        
        // update selection
        $scope.replaceUserSelection(node);
    }


    /**
     * Get user selection
     *
     * @since 0.1.5
     * @author Ilya K.
     */
    
    $scope.getUserSelection = function() {

        var sel = "";

        if (typeof window.getSelection != "undefined") {
            var sel = window.getSelection();
            if (sel.rangeCount) {
                var container = document.createElement("div");
                for (var i = 0, len = sel.rangeCount; i < len; ++i) {
                    container.appendChild(sel.getRangeAt(i).cloneContents());
                }
                sel = container.innerHTML;
            }
        } else if (typeof document.selection != "undefined") {
            if (document.selection.type == "Text") {
                sel = document.selection.createRange().htmlText;
            }
        }

        return sel;
    }


    /**
     * Replace user selection 
     *
     * @since 0.1.5
     * @author Ilya K.
     */
    
    $scope.replaceUserSelection = function(node) {
        
        var range, html;
        if (window.getSelection && window.getSelection().getRangeAt) {
            range = window.getSelection().getRangeAt(0);
            range.deleteContents();
            range.insertNode(node);
        } else if (document.selection && document.selection.createRange) {
            range = document.selection.createRange();
            html = (node.nodeType == 3) ? node.data : node.outerHTML;
            range.pasteHTML(html);
        }
    }


    /**
     * Helper function to create a list of dom items to be removed from a hierarchy 
     *
     * @since 1.1.0
     * @author Gagan Goraya.
     */

    $scope.listToBeRemoved = function(toBeRemoved, componentsTree, id, collect) {

        if(typeof(collect) === 'undefined')
            collect = false;
    
        var keepGoing = true;

        angular.forEach(componentsTree, function(item, index) {
            
            if(keepGoing) {
               
                if(id && item.id === parseInt(id)) {
                    
                    toBeRemoved.splice(0,toBeRemoved.length);

                    collect = true;
                    keepGoing = false;

                }

                if(collect)
                    toBeRemoved.push(item.id);
                
                if ( item.children ) {
                    $scope.listToBeRemoved(toBeRemoved, item.children, id, collect);
                }
            }
        });
    }

    /**
     * Rebuild DOM node based on current Components Tree
     * 
     * @since 0.1
     * @author Ilya K.
     */
    
    $scope.rebuildDOM = function(id, reorder, domNode) {

        if ($scope.log) {
            console.log("rebuildDOM()", id, reorder, domNode);
        }

        // rebuild the full tree if no id defined
        if (undefined === id) {
            id = 0;
        }
        
        $scope.functionStart("rebuildDOM");

        // build children
        if ( $scope.componentsTree.children ) {
            
            if(reorder && !domNode) { // lets not dwell into removing/reordering for rendering inside a virtual dom
                
                var toBeRemoved = []
                //recurse and prepare list of items to be removed from the DOM
                $scope.listToBeRemoved(toBeRemoved, $scope.componentsTree.children, id);

                angular.forEach(toBeRemoved.reverse(), function(itemid) {
                    $scope.removeComponentFromDOM(itemid);
                });

            }

            $scope.buildComponentsFromTree($scope.componentsTree.children, id, reorder, domNode, null, domNode?true:false);
            
            // increment id after buildComponentsFromTree() is completed
            var counter = 0;
            function waitOxygenTreeRebuildTimeout(counter) {
                counter++;
                setTimeout(function(){
                    waitOxygenTreeRebuild(counter);
                }, 100);
            }
            // don't increment while tree is building i.e. wait for AJAX based components
            function waitOxygenTreeRebuild(counter) {

                if ( $scope.buildingOxygenTreeCounter > 0 && counter < 900) {
                    // keep waiting tree to be built while buildComponentsFromTree() in progress
                    waitOxygenTreeRebuildTimeout(counter);
                }
                else {
                    // increment id after buildComponentsFromTree() is completed
                    $scope.component.id++;
                }

                // buildComponentsFromTree() took over 90s (100ms x 900) probably due to slow AJAX elements loading
                if ( $scope.buildingOxygenTreeCounter > 0 && counter >= 900) {
                    console.log('Tree building timeout. ID counter is not incremented.');
                }
            }
            waitOxygenTreeRebuild(counter);

            $scope.functionEnd("rebuildDOM");
        } 
        else {

            $scope.functionEnd("rebuildDOM");
            return false;
        }
    }


    /**
     * Look for parents of element with passed ID and rebuilt the last found parent 
     * if it has "data-rebuild-on-dom-change" attribute
     * 
     * @since 2.3
     * @author Ilya K.
     */

    $scope.rebuildDOMChangeParent = function(id) {

        var component = $scope.getComponentById(id);
        var componentToRebuild = jQuery(component).parents("[data-rebuild-on-dom-change]").last();

        // include self
        if ( componentToRebuild.length === 0 ) {
            componentToRebuild = jQuery(component).filter("[data-rebuild-on-dom-change]");
        }

        // rebuilt if any
        if ( componentToRebuild.length > 0 ) {
            var timeout = $timeout(function() {
                var id = componentToRebuild[0].getAttribute('ng-attr-component-id'); 

                if ($scope.log) {
                    console.log('rebuildDOMChangeParent()', id)
                }

                $scope.rebuildDOM(id);
                $timeout.cancel(timeout);
            }, 0, false);
        }
    }


    /**
     * Cut component from DOM
     * 
     * @since 0.1.8
     * @author Ilya K.
     */

    $scope.removeComponentFromDOM = function(id) {

        var component = $scope.getComponentById(id);

        if(component && component.closest('[disabled="disabled"]').length > 0) {
            return; // this is a list item in the dynamic list component
        }

        if ( component ) {

            // check if we removing slide or slider
            if ( component.is('.ct-slide, .ct-slider') ) {
                var unslider = component.closest('.unslider');
            }

            // check if we are removing a modal
            if( component.is('.ct-modal') ){
                var modalBackdrop = component.closest('.oxy-modal-backdrop')
            }

            component.scope().$destroy();
            component.remove();
            component = null;

            // remove unslider wrap if slider detected
            if (unslider) {
                unslider.remove();
                unslider = null;
            }

            // remove backdrop if modal detected
            if (modalBackdrop) {
                modalBackdrop.remove();
                modalBackdrop = null;
            }
        }
    }


    /**
     * Add Layout cells and set their width according to 
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.addPresetColumns = function(columnsWidths) {

        var columnsId = $scope.component.active.id,
            columnsComponent = $scope.getActiveComponent(),
            columnId;

        // clear preset templates
        columnsComponent.html("");

        $scope.updateColumnsOnAdd = false;

        angular.forEach(columnsWidths, function(width) {
            
            $scope.addComponent("ct_div_block");
            
            $scope.setOptionModel("width",width.toString());
            $scope.setOptionModel("width-unit","%");

            columnId = $scope.component.active.id;
            
            // activate columns back
            $scope.activateComponent(columnsId,"ct_new_columns");
        })

        $scope.updateColumnsOnAdd = true;

    }


    /**
     * Add a text block with predifened data shortcode
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.addCustomFieldComponentWithParams = function(dynamicDataModel, dataitem) {

        var shortcode = '[oxygen data="'+dataitem.data+'"';
                    
        var finalVals = {};
        _.each(dataitem.properties, function(property) {
            if(dynamicDataModel.hasOwnProperty(property.data) && dynamicDataModel[property.data].trim !== undefined && 
                dynamicDataModel[property.data].trim()!=='' &&
                !property.helper && dynamicDataModel[property.data] !== property.nullVal) {
                finalVals[property.data] = dynamicDataModel[property.data];
            }
        });

        _.each(finalVals, function(property, key) {
            property = property.replace(/'/g, "__SINGLE_QUOTE__");
            shortcode+=' '+key+'="'+property+'"';
        })

        if(dataitem['append']) {
            shortcode+=' '+dataitem['append'];
        }

        shortcode+=']';

        $scope.addComponent('ct_text_block');

        iframeScope.setOptionModel('ct_content', shortcode);

        var content = iframeScope.getOption('ct_content');
        var idIncrement = 0;

        content = content.replace(/\[oxygen[^\]]*\]/ig, function(match) {

            // create a span component out of match
            // embed it in the tree as a child of $scope.iframeScope.component.active.id
            // get the new component's id

            var newComponent = {
              id : iframeScope.component.id + idIncrement, 
              name : "ct_span"
            }

            idIncrement++;

            // set default options first
            iframeScope.applyComponentDefaultOptions(newComponent.id, "ct_span");

            // insert new component to Components Tree
            iframeScope.findComponentItem(iframeScope.componentsTree.children, iframeScope.component.active.id, iframeScope.insertComponentToTree, newComponent);

            // update span options
            iframeScope.component.options[newComponent.id]["model"]["ct_content"] = match;
            iframeScope.setOption(newComponent.id, "ct_span", "ct_content");

            return "<span id=\"ct-placeholder-"+newComponent.id+"\"></span>"
        });

        iframeScope.setOptionModel('ct_content', content, iframeScope.component.active.id, iframeScope.component.active.name);

        $scope.parentScope.oxygenUIElement.find('#ctdynamicdata-popup').remove();
        $scope.parentScope.oxygenUIElement.find('.oxy-dynamicdata-popup-background').remove();

    }

    $scope.addCustomFieldComponent = function() {
        $scope.dynamicDataModel = {};
        var template = 
        '<div class="oxy-dynamicdata-popup-background"></div>'+
        '<div id="ctdynamicdata-popup" class="oxygen-data-dialog">'+
            '<div>'+
                '<div class="oxygen-data-dialog-data-picker">'+
                    '<ul>'+
                        '<li>'+
                            '<div ng-if="dynamicShortcodesCFOptions.properties" class="oxygen-data-dialog-options">'+
                                '<h1>{{dynamicShortcodesCFOptions.name}} Options</h1>'+
                                '<div>'+
                                '<div class="oxygen-control-wrapper" ng-repeat="property in dynamicShortcodesCFOptions.properties">'+
                                    '<label ng-if="property.name&&property.type!==\'checkbox\'" class="oxygen-control-label"> {{property.name}} </label>'+
                                    // dropdown
                                    '<div ng-if="property.type===\'select\'" class="oxygen-select oxygen-select-box-wrapper">'+
                                        '<div class="oxygen-select-box">'+
                                            '<div class="oxygen-select-box-current">{{dynamicDataModel[property.data]}}</div>'+
                                            '<div class="oxygen-select-box-dropdown"></div>'+
                                        '</div>'+
                                        '<div class="oxygen-select-box-options">'+
                                            '<div ng-repeat="option in property.options" ng-click="dynamicDataModel[property.data]=option;applyChange(property)" class="oxygen-select-box-option">{{option}}</div>'+
                                        '</div>'+
                                    '</div>'+
                                    // input
                                    '<div class="oxygen-input" ng-if="property.type===\'text\'">'+
                                        '<input type="text" ng-model="dynamicDataModel[property.data]" ng-change="applyChange(property)" ng-trim="false"/>'+
                                    '</div>'+
                                    // checkbox
                                    '<label class="oxygen-checkbox" ng-if="property.type===\'checkbox\'" >{{property.name}}'+
                                        '<input type="checkbox" ng-model="dynamicDataModel[property.data]" ng-true-value="\'{{property.value}}\'" ng-change="applyChange(property)" />'+
                                        '<div class="oxygen-checkbox-checkbox" ng-class="{\'oxygen-checkbox-checkbox-active\':dynamicDataModel[property.data]==\'{{property.value}}\'}"></div>'+
                                    '<label>'+

                                    '<br ng-if="property.type===\'break\'" />'+
                                '</div class="oxygen-control-wrapper">'+
                                '</div>'+
                                '<div class="oxygen-apply-button" ng-mousedown="addCustomFieldComponentWithParams(dynamicDataModel, dynamicShortcodesCFOptions)">Insert</div>'+
                            '</div>'+
                        '</li>';
                    '</ul>';
                '</div>';
            '</div>';
        '</div>';

        
        angular.element(document).injector().invoke(function($compile) {

            var compiledElement = $compile(template)($scope);
            
            $scope.parentScope.oxygenUIElement.append(compiledElement);

            $scope.$apply();

        });

    }


    /**
     * Compile and insert new component
     *
     * @since 0.1.6
     * @author Ilya K.
     */

    $scope.cleanInsert = function(element, parentElement, index, primaryIndex) {

        angular.element(document).injector().invoke(function($compile) {

            var newScope        = $scope.$new();
            //     existingScope   = angular.element(element).scope();
            
            // if (existingScope) {
            //     //existingScope.$destroy();
            // }

            var compiledElement = $compile(element)(newScope);

            elementId = compiledElement.attr('ng-attr-component-id');
            if(typeof(elementId) !== 'undefined' && $scope.component.options[elementId]['model'] && $scope.component.options[elementId]['model']['wrapping_start'] && $scope.component.options[elementId]['model']['wrapping_start'].length > 2) {
                compiledElement.html($scope.component.options[elementId]['model']['wrapping_start']+$scope.component.options[elementId]['model']['wrapping_end']);
                compiledElement.children().addClass('ct_nestable_element_wrap');
            }
            
            if ( parentElement ) {
                $scope.insertAtIndex(compiledElement, parentElement, primaryIndex?(primaryIndex+index):index);
            } 
            else {
                angular.element(element).replaceWith(compiledElement);
            }

            if(angular.element(element).hasClass('removeOnInsert')) {
                parentElement.html(angular.element(element).html());
            }
        });
    }


    /**
     * Compile and replace component 
     *
     * @since 0.1.7
     * @author Ilya K.
     */

    $scope.cleanReplace = function(placeholderID, replacement, domNode) {

        angular.element(document).injector().invoke(function($compile) {

            var newScope = $scope.$new(),
                compiledReplacement = $compile(replacement)(newScope);
            
            var element;

            if(domNode) {
                element = domNode.find('#'+placeholderID);
            } else {
                element = angular.element('#'+placeholderID);
            }

            element.replaceWith(compiledReplacement);
        });
    }


    /**
     * Insert child DOM element at a specific index in a parent element
     *
     * @since 0.1.7
     * @author Ilya K.
     */

    $scope.insertAtIndex = function(child, parent, index) {

        if ( index === 0 ) {
            parent.prepend(child);
        }
        else if ( index > 0 ) {
            jQuery(">*:nth-child("+index+")", parent).after(child);
        }
        else {
            parent.append(child);
        }
    }


    /**
     * Check if component contain [oxygen] data shortcode
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.hasOxyDataInside = function(id) {

        if(typeof(id) === 'undefined') {
            id = $scope.component.active.id;
        }

        if ($scope.getComponentById(id)) {
            return $scope.getComponentById(id).children('span.ct-contains-oxy').length > 0;
        }
        else {
            return false;
        }
    }
    
    /**
     * @since 3.5
     * @author Ilya K.
     */

    $scope.isElementEnabledForUser = function(name) {

        if (undefined === name) {
            name = $scope.component.active.name;
        }

        if (undefined==$scope.userEnabledElements || typeof $scope.userEnabledElements.indexOf !== "function")
            return false

        return $scope.userEnabledElements.indexOf(name) > -1
    }


    /**
     * Helper function to escape HTML special chars
     *
     * @since 0.1.7
     * @author Ilya K.
     */

    $scope.escapeHtml = function(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };

        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    

    /**
     * Helper function to add slashes before quotes
     *
     * @since 0.1.7
     * @author Ilya K.
     */
    
    $scope.addSlashes = function(str) {

        return (str + '')
            .replace(/[\\"']/g, '\\$&')
            .replace(/\u0000/g, '\\0');
    }

    /**
     * Helper function to strip slashes from server response
     *
     * @since 0.4.0
     * @author Ilya K.
     */
    
    $scope.stripSlashes = function(str){
        return str.replace(/\\(.)/mg, "$1");
    }
    

    /**
     * Helper functions to check fucntion execution speed
     *
     * @since 0.2.?
     * @author Ilya K.
     */
    
    $scope.functionStart = function(name) {
        if ( $scope.log === true ) {
            console.time(name);
        }
    }

    $scope.functionEnd = function(name) {
        if ( $scope.log === true ) {
            console.timeEnd(name);
        }
    }

    
    /**
     * Prevent user from leaving a builder if link clicked or form submitted
     * 
     * @since 0.2.5
     */
    
    $scope.setupPageLeaveProtection = function() {

        // bind click and submit events
        jQuery("#ct-builder")
            .on("click", "a", function(e) {
                if (jQuery(this).not("[href*='#']")||jQuery(this).parents('.oxygen-scroll-to-hash-links').length===0){
                    e.preventDefault()
                }
            })
            .on("submit", "form", function(e) {    
                e.preventDefault()
            });
    }


    /**
     * Show confirmation dialong on exit
     *
     * @since 0.3.2
     */
    
    $scope.confirmOnPageExit = function(e) {
        
        // If we haven't been passed the event get the window.event
        e = e || window.event;

        var message = 'There is unsaved changes.';

        // For IE6-8 and Firefox prior to version 4
        if (e) 
        {
            e.returnValue = message;
        }

        // For Chrome, Safari, IE8+ and Opera 12+
        return message;
    };


    /**
     * Attach event to show confirm dialog on exit when all saved
     *
     * @since 0.3.2
     */
    
    $scope.unsavedChanges = function() {

        if ($scope.log) {
            console.log("unsavedChanges()");
        }

        if (window.onbeforeunload===null) {
            window.onbeforeunload = $scope.confirmOnPageExit;
        }

        $parentScope.oxygenUIElement.addClass("oxygen-unsaved-changes");
    }


    /**
     * Remove event to hide confirm dialog on exit when all saved
     *
     * @since 0.3.2
     */
    
    $scope.allSaved = function() {
        window.onbeforeunload = null;
        $parentScope.oxygenUIElement.removeClass("oxygen-unsaved-changes");
    }


    /**
     * Helper to prevent Angular sort Object on ng-repeat
     * 
     * @since 0.3.2
     */
    
    $scope.notSorted = function(obj) {
        if (!obj) {
            return [];
        }
        return Object.keys(obj);
    }


    $scope.objectToArrayObject = function(obj) {
        if (!obj) {
            return [];
        }

        var arr = _.map(obj, function(item, key) {
            if(typeof(item) === 'object') {
                return Object.assign(item, {'key': key});
            }
            else {
                return item;
            }
        });

        return arr;

    }


    /**
     * Helper to prevent Angular sort Object on ng-repeat
     * 
     * @since 0.3.3
     */
    $scope.isObjectEmpty = function(obj) {
        if (obj) {
            for (var prop in obj) {
                if (obj.hasOwnProperty(prop)) {
                    return false;
                }
            }
        }
        return true;
    }


    /**
     * Helper to encode string with Unicode characters to base64
     * 
     * @since 0.4.0
     * @author Ilya K.
     */
    
    $scope.b64EncodeUnicode = function(str) {
        return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
            return String.fromCharCode('0x' + p1);
        }));
    }


    /**
     * This function intercepts the paste event, removes the formatting off the clipboard data
     * and programatically inserts the plain text. 
     * 
     * As undo/redo history of the editable region gets destroyed on programatically inserting 
     * text into the element. This function contains provision for 1 undo (ctrl-z/cmd-z) to restore
     * the state of element to what it was before the the paste.
     * @author gagan goraya
     *
     * @since 0.3.4
     */

    var stripFormatting = function(e) {
        
        var me = this,
            oldContent = jQuery(me).html(),
            strippedPaste = jQuery('<div>').append(e.originalEvent.clipboardData.getData("Text").replace(/(?:\r\n|\r|\n)/g, '%%linebreak%%')).text(),//.replace(/(?:\r\n|\r|\n)/g, '<br />');
            sel, range;

        if (window.getSelection) {

            sel = window.getSelection();
            
            if (sel.rangeCount) {
                range = sel.getRangeAt(0);
                range.deleteContents();
                var nodes = strippedPaste.split('%%linebreak%%'), 
                    node;

                for (var key in nodes) {
                    if (nodes.hasOwnProperty(key)) {
                    
                        node = document.createTextNode(nodes[key]);
                        
                        range.insertNode(node);
                        range = range.cloneRange();
                        range.selectNodeContents(node);
                        range.collapse(false);
                        
                        sel.removeAllRanges();
                        sel.addRange(range);

                        if (key < nodes.length-1) {
                            node = document.createElement('br')
                                                        
                            range.insertNode(node);
                            range = range.cloneRange();
                            range.selectNode(node);
                            range.collapse(false);
                        
                            sel.removeAllRanges();
                            sel.addRange(range);
                        }
                    }
                }
            }

        } else if (document.selection && document.selection.createRange) {

            range = document.selection.createRange();
            range.text = strippedPaste.split('%%linebreak%%').join("");
        }

        // This lets the user undo the paste action.
        var undofunc = function(e) {
            
            if(e.keyCode === 90 && (e.ctrlKey || e.metaKey)) {
                e.preventDefault();
                jQuery(me).html(oldContent);
                jQuery(me).off('keydown', undofunc);
            }
        }
        jQuery(me).on('keydown', undofunc);
        e.preventDefault();
    }

    jQuery('body')
        /*.on('paste', '.ct_paragraph', stripFormatting)
        .on('paste', '.ct_headline', stripFormatting)
        .on('paste', '.ct_li', stripFormatting)
        .on('paste', '.ct_span', stripFormatting)
        .on('paste', '.ct_link_text', stripFormatting)
        .on('paste', '.ct_text_block', stripFormatting)*/
        .on('paste', '[contenteditable]', stripFormatting);


    
    /**
     * Apply parent scope after each iframe digest
     *
     * @since 2.0
     * @author Ilya K.
     */

    var applySceduled = false;
    $scope.$watch(function() {
        if (applySceduled) return;
        applySceduled = true;
        $scope.$$postDigest(function() {
            applySceduled = false;
            if ($scope.parentScope)
                $scope.parentScope.safeApply();
        });
    });

    
    /**
     * Triggered from UI to apply iframe scope 
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.safeApply = function() {
        applySceduled = true;
        if ($scope.$root.$$phase != '$apply' && $scope.$root.$$phase != '$digest') {
            $scope.$apply();
        }
        applySceduled = false;
    }


    /**
     * Add body class
     * 
     * @since 2.1
     */
    
    $scope.addBodyClass = function(className) {
        console.log(jQuery('body'));
        jQuery('body').addClass(className);
    }


    /**
     * Remove body class
     * 
     * @since 2.1
     */
    
    $scope.removeBodyClass = function(className) {
        console.log(jQuery('body'));
        jQuery('body').removeClass(className);
    }

    /**
     * Debounce helper to use anywhere
     * 
     * @since 4.7
     */

    $scope.debounce = function(callback, delay) {
        var timeout;
        return function () {
            var context = this;
            var args = arguments;
            if (timeout) {
                clearTimeout(timeout);
            }
            timeout = setTimeout(function () {
                timeout = null;
                callback.apply(context, args);
            }, delay);
        }
    }
    
// End MainController
});

CTFrontendBuilder.factory('$parentScope', function($window) {
    return $window.parent.angular.element($window.frameElement).scope();
});

/**
 * Collect all controllers into one
 * 
 */

CTFrontendBuilder.controller('BuilderController', function($controller, $scope, $parentScope, $http, $timeout, $window) {

    // reference for views
    $scope.parentScope = $parentScope;

    var locals = {
        $scope: $scope,
        $parentScope: $parentScope,
        $http: $http,
        $timeout: $timeout
    };

    $controller('MainController',           locals);
    $controller('ComponentsTree',           locals);
    $controller('ComponentsStates',         locals);
    $controller('ControllerNavigation',     locals);
    $controller('ControllerColumns',        locals);
    $controller('ControllerAJAX',           locals);
    $controller('ControllerClasses',        locals);
    $controller('ControllerOptions',        locals);
    $controller('ControllerPresets',        locals);
    $controller('ControllerConditions',     locals);
    $controller('ControllerFonts',          locals);
    $controller('ControllerCSS',            locals);
    $controller('ControllerTemplates',      locals);
    $controller('ControllerSVGIcons',       locals);
    $controller('ControllerMediaQueries',   locals);
    $controller('ControllerAPI',            locals);
    $controller('ControllerDragnDropLists', locals);
    $controller('ControllerHeader',         locals);
    $controller('ControllerUndoRedo',       locals);
    $controller('ControllerShortcuts',      locals);
    $controller('ControllerAutoUnits',      locals);
    $controller('ControllerCopyPaste',      locals);
});
