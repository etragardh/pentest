/**
 * All UI staff here
 * 
 */

var CTFrontendBuilderUI = angular.module('CTFrontendBuilderUI', ['ngAnimate', 'CTCommonDirectives'])
.config( ['$provide', function ($provide){
    $provide.decorator('$browser', ['$delegate', function ($delegate) {
        $delegate.onUrlChange = function () {};
        $delegate.url = function () { return ""};
        return $delegate;
    }]);
}]);

CTFrontendBuilderUI.config(function($rootScopeProvider) {
    $rootScopeProvider.digestTtl(16);
});

CTFrontendBuilderUI.controller("ControllerUI", function($controller, $anchorScroll, $location, $scope, $timeout, $interval, $window, $compile, ctScopeService) {  
    ctScopeService.store('uiscope', $scope);
    window.$scope = $scope;

    $scope['CtBuilderAjax'] = $window['CtBuilderAjax'];
    
    /**
     * Include other controllers
     */

    $controller('ControllerSlider', {
        $scope: $scope,
        $timeout: $timeout,
        $interval: $interval
    });

    // Background Layers
    $scope.ctBgLayerType = 'image';
    $scope.bgLayersSortableOptions = {
      update: function(e, ui) {
        setTimeout(function() {
            var layers = $scope.iframeScope.getOption('background-layers');
            $scope.iframeScope.setOptionModel('background-layers', layers);
        }, 100);
      }
    };
    $scope.media_uploader = {};

    $scope.oxygenUIElement      = jQuery("#oxygen-ui");
    $scope.toolbarElement       = jQuery("#oxygen-topbar");
    $scope.viewportContainer    = jQuery("#ct-viewport-container");
    $scope.artificialViewport   = jQuery("#ct-artificial-viewport");
    $scope.viewportRulerWrap    = jQuery("#ct-viewport-ruller-wrap");
    $scope.sidePanelElement     = jQuery("#ct-sidepanel");
    $scope.settingsPanelElement = jQuery("#oxygen-global-settings");
    $scope.verticalSidebar      = jQuery("#oxygen-sidebar");

    // load viewport iframe only after UI app is initialized  
    $scope.artificialViewport.prop("src", function(){
        return angular.element(this).data("src");
    });

    $scope.viewportScale        = 1;
    $scope.viewportScaleLocked  = false;

    window.$scope = $scope;
    // variable to show/hide toolbar elements
    $scope.showEmptyMessage     = true;
    $scope.showAllStyles        = true;
    $scope.showClasses          = true;
    $scope.showLeftSidebar      = true;
    $scope.showButtonFlashing   = false;
    $scope.showComponentBar     = false;
    $scope.showDOMTreeNavigator = false;
    $scope.dialogWindow         = false;
    $scope.viewportRullerShown  = false;
    $scope.showSidePanel        = false;
    $scope.showSettingsPanel    = false;
    $scope.styleTabAdvance      = false;
    $scope.activeForEditBgLayer = false;
    $scope.statusBarActive      = false;
    $scope.showDataPanel        = false;
    $scope.showSidebarLoader    = false;
    $scope.copySelectorFromClass = false;
    $scope.copySelectorFromID    = false;
    $scope.builtinContentEditing = false;
    $scope.conditionsDialogOptions = {
        selectedIndex : 0,
        userCondition:'',
    };

    $scope.currentBorder        = "all";

    $scope.actionTabs = {
        "componentBrowser"  : false,
        "advancedSettings"  : false,
        "contentEditing"    : false,
        "settings"          : false,
        "styleSheet"        : false,
        "codeEditor"        : false
    };

    $scope.highlight        = [];
    
    $scope.tabs                         = [];
    $scope.tabs.components              = [];
    $scope.tabs.components.fundamentals = true;

    $scope.tabs.advanced = [];

    // Background tab
    $scope.tabs.advanced.Background     = [];

    // Position & Size tab
    $scope.tabs.advanced.positionSize   = [];
    
    $scope.tabs.settings                = [];
    //$scope.tabs.settings.page           = true;

    $scope.tabs.navMenu                 = [];
    $scope.tabs.slider                  = [];

    $scope.tabs.sidePanel               = [];
    $scope.tabs.sidePanel.DOMTree       = true;
    $scope.tabs.sidePanel.History       = true;

    $scope.tabs.codeEditor              = [];
    $scope.tabs.codeEditor["code-php"]  = true;
    
    $scope.isSelectableEnabled  = false;
    $scope.isDOMNodesSelected   = false;

    // start with no overlays
    $scope.overlaysCount = 0;
    $scope.pageLoaded = false;

    $scope.dialogForms = [];

    $scope.iframeScope = false;

    // default to Global Colors
    $scope.colorSetIDToAdd = 0;

    // toolbar search bar begin 

    // search bar query
    $scope.componentsSearchQuery = '';

    // cached elements for client-side search
    var searchElementOriginalList = jQuery('#oxygen-toolbar-original-panels');
    var searchElementFilteredList = jQuery('#oxygen-toolbar-search-panels');
    var searchElementNoResultsText = searchElementFilteredList.find('.oxygen-add-panels-no-search-results');
    var searchElementSearchResultsContainer = jQuery('#oxygen-toolbar-search-results');
    var searchElementSearchResults = jQuery('#oxygen-toolbar-search-results > div');
    function getSearchElementFirstVisible() {
        return jQuery('#oxygen-toolbar-search-results > div:visible:first');
    }
    // name of the data-* key used to retrieve searchable elements ids
    var searchIdKey = 'searchid';
    var searchCategoryKey = 'searchcat';
    var searchNameKey = 'searchname';
    var searchKeywordsKey = 'searchkeys';
    var searchElementSearchInput = jQuery('#oxygen-add-sidebar .oxygen-add-searchbar');

    // internal search cache
    // id should match data-`searchIdKey` attribute of the searchable elements
    // search is done on both title and category
    // you can write custom text inside items to enhance search
    var searchCache = {
      items: [
          { title: 'Section', category: 'Basics Containers', id: 'section' },
          { title: 'Div', category: 'Basics Containers', id: 'div' },
          { title: 'Columns', category: 'Basics Containers', id: 'columns' },

          { title: 'Heading', category: 'Basics Text', id: 'heading' },
          { title: 'Text', category: 'Basics Text', id: 'text' },
          { title: 'Rich Text', category: 'Basics Text', id: 'rich_text' },

          { title: 'Text Link', category: 'Basics Links', id: 'text_link' },
          { title: 'Link Wrapper', category: 'Basics Links', id: 'link_wrapper' },
          { title: 'Button', category: 'Basics Links', id: 'button' },

          { title: 'Image', category: 'Basics Visual', id: 'image' },
          { title: 'Video', category: 'Basics Visual', id: 'video' },
          { title: 'Icon', category: 'Basics Visual', id: 'icon' },

          { title: 'Code Block', category: 'Basics Other', id: 'code_block' },
          { title: 'Inner Content', category: 'Basics Other', id: 'inner_content' },

          { title: 'Header Builder', category: 'Helpers Composite', id: 'header_builder' },
          { title: 'Social Icons', category: 'Helpers Composite', id: 'social_icons' },
          { title: 'Testimonial', category: 'Helpers Composite', id: 'testimonial' },
          { title: 'Icon Box', category: 'Helpers Composite', id: 'icon_box' },
          { title: 'Pricing Box', category: 'Helpers Composite', id: 'pricing_box' },
          { title: 'Progress Bar', category: 'Helpers Composite', id: 'progress_bar' },
          { title: 'Modals', category: 'Helpers Composite', id: 'modal' },

          { title: 'Easy Posts', category: 'Helpers Dynamic', id: 'easy_posts' },
          { title: 'Gallery', category: 'Helpers Dynamic', id: 'gallery' },
          { title: 'Repeater', category: 'Helpers Dynamic', id: 'repeater' },

          { title: 'Slider', category: 'Helpers Interactive elements', id: 'slider' },
          { title: 'Tabs', category: 'Helpers Interactive elements', id: 'tabs' },
          { title: 'Superbox', category: 'Helpers Interactive elements', id: 'superbox' },
          { title: 'Toggle', category: 'Helpers Interactive elements', id: 'toggle' },

          { title: 'Google Maps', category: 'Helpers External', id: 'google_maps' },
          { title: 'SoundCloud', category: 'Helpers External', id: 'soundcloud' },

          { title: 'Menu', category: 'Wordpress', id: 'menu' },
          { title: 'Shortcode', category: 'Wordpress', id: 'shortcode' },
          { title: 'Shortcode Wrapper', category: 'Wordpress', id: 'shortcode_wrapper' },
          { title: 'Comments List', category: 'Wordpress', id: 'comments_list' },
          { title: 'Comment Form', category: 'Wordpress', id: 'comment_form' },
          { title: 'Login Form', category: 'Wordpress', id: 'login_form' },
          { title: 'Search Form', category: 'Wordpress', id: 'search_form' },

          { title: 'Title', category: 'Dynamic data', id: 'dynamic_data_title' },
          { title: 'Content', category: 'Dynamic data', id: 'dynamic_data_content' },
          { title: 'Date', category: 'Dynamic data', id: 'dynamic_data_date' },
          { title: 'Categories', category: 'Dynamic data', id: 'dynamic_data_categories' },
          { title: 'Tags', category: 'Dynamic data', id: 'dynamic_data_tags' },
          { title: 'Featured Image', category: 'Dynamic data', id: 'dynamic_data_featured_image' },
          { title: 'Author', category: 'Dynamic data', id: 'dynamic_data_author' },
          { title: 'Author Avatar', category: 'Dynamic data', id: 'dynamic_data_author_avatar' },
          { title: 'Custom Field', category: 'Dynamic data', id: 'dynamic_data_custom_field' },

          { title: 'Title', category: 'Widgets', id: 'dynamic_data_title' },
          { title: 'Content', category: 'Widgets', id: 'dynamic_data_content' },
          { title: 'Date', category: 'Widgets', id: 'dynamic_data_date' },
          { title: 'Categories', category: 'Widgets', id: 'dynamic_data_categories' },
          { title: 'Tags', category: 'Widgets', id: 'dynamic_data_tags' },
          { title: 'Featured Image', category: 'Widgets', id: 'dynamic_data_featured_image' },
          { title: 'Author', category: 'Widgets', id: 'dynamic_data_author' },
          { title: 'Author Avatar', category: 'Widgets', id: 'dynamic_data_author_avatar' },
          { title: 'Custom Field', category: 'Widgets', id: 'dynamic_data_custom_field' },
          { title: "Widget Pages", category: "Widgets", id: "widget_pages" },
          { title: "Widget Calendar", category: "Widgets", id: "widget_calendar" },
          { title: "Widget Archives", category: "Widgets", id: "widget_archives" },
          { title: "Widget Audio", category: "Widgets", id: "widget_audio" },
          { title: "Widget Image", category: "Widgets", id: "widget_image" },
          { title: "Widget Gallery", category: "Widgets", id: "widget_gallery" },
          { title: "Widget Video", category: "Widgets", id: "widget_video" },
          { title: "Widget Meta", category: "Widgets", id: "widget_meta" },
          { title: "Widget Search", category: "Widgets", id: "widget_search" },
          { title: "Widget Text", category: "Widgets", id: "widget_text" },
          { title: "Widget Categories", category: "Widgets", id: "widget_categories" },
          { title: "Widget Recent Posts", category: "Widgets", id: "widget_recent_posts" },
          { title: "Widget Recent Comments", category: "Widgets", id: "widget_recent_comments" },
          { title: "Widget Rss", category: "Widgets", id: "widget_rss" },
          { title: "Widget Tag Cloud", category: "Widgets", id: "widget_tag_cloud" },
          { title: "Widget Navigation Menu", category: "Widgets", id: "widget_navigation_menu" },
          { title: "Widget Custom Html", category: "Widgets", id: "widget_custom_html" },
      ],
    };

    var searchIds = {};
    for (var i = 0; i < searchCache.items.length; i++) {
        searchIds[searchCache.items[i].id] = true;
    }

    // hack that waits until the html with searchable elements is rendered 
    // some elements are dynamic so we wait until the data is rendered
    // and then inject it into a client-side list of searchable elements 
    var loaderInterval = window.setInterval(function() { 
        if (searchElementSearchResults.length == 0) return;

        searchElementSearchResults.each(function() {
            var searchId = angular.element(this).data(searchIdKey);
            if (searchIds.hasOwnProperty(searchId)) return;
            var searchCategory = angular.element(this).data(searchCategoryKey);
            var searchName = angular.element(this).data(searchNameKey);
            var searchKeywords = angular.element(this).data(searchKeywordsKey);
            if (typeof searchName === 'undefined') {
                searchName = angular.element(this).text().trim();
            }
            
            searchCache.items.push({
                title: searchName,
                category: searchCategory,
                id: searchId,
                keywords: searchKeywords
            });
        });

        // exclude category Widgets, Dynamic Data and uncategorized (i.e., sidebars)
        searchCache.items = _.filter(searchCache.items, function(item) {
          return (item.category != 'Widgets' && item.category != 'Dynamic data' && typeof(item.category) !== 'undefined');
        });

        searchCache.fuse = new Fuse(searchCache.items, {
            shouldSort: true,
            tokenize: true,
            matchAllTokens: true,
            findAllMatches: true,
            threshold: 0.05,
            includeScore: true,
            location: 0,
            distance: 250,
            maxPatternLength: 36,
            minMatchCharLength: 1,
            keys: [
                { name: "title", weight: 0.6 },
                { name: "category", weight: 0.2 },
                { name: "keywords", weight: 0.2 },
            ],
        });
        clearTimeout(loaderInterval);
    }, 350);

    // toolbar search bar end 

    /**
     * Handle visibility of empty sidebar message
     */
    $scope.$watch(function() {
        $scope.showEmptyMessage = $scope.iframeScope && $scope.showLeftSidebar !== false && !(
            $scope.isActiveName('root') === false ||
            $scope.isActiveActionTab('componentBrowser') === true ||
            $scope.iframeScope.selectedNodeType === 'stylesheet' ||
            $scope.iframeScope.selectedNodeType === 'selectorfolder' ||
            $scope.iframeScope.selectedNodeType === 'cssfolder' ||
            $scope.iframeScope.selectedNodeType === 'styleset'
        );
    });

    /**
     * Get iframe scope and save within UI scope
     *
     */

    $scope.$on('iframe-scope', function(e, iframeScope) {
        $scope.iframeScope = iframeScope;
    });

    $scope.stopPropagation = function($event) {
      $event.stopPropagation();
    }

    /**
     * Triggered from iframe to apply UI scope 
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
     * Helping functions for the new dom structure panel
     *
     * @since new dom structure panel
     * @author Gagan S Goraya.
     */

    $scope.treeHandleRenameKeypress = function(e, state) {
        if(e.keyCode === 13) {
          state.editable = false;
        }
    }

    $scope.isDraggable = function(parentId) {

        if (!$scope.iframeScope.component.options[parentId]) {
            return false;
        }

        var draggable = true;
        var parentName = $scope.iframeScope.component.options[parentId].name;

        // Set elements as undraggable based on parent class
        var unDraggableParents = [
            'oxy_header_row',
            'oxy_icon_box',
            'oxy_pricing_box',
            'oxy_dynamic_list'
        ]

        if (unDraggableParents.indexOf(parentName) > -1) {
            draggable = false;
        }

        return draggable;
    }

    $scope.categoryList = function() {

        var categoriesList = false;

        if(CtBuilderAjax.componentCategories && CtBuilderAjax.componentCategories.length > 0) {
            categoriesList = [];
           

            for(key in CtBuilderAjax.componentCategories) {
                categoriesList.push(CtBuilderAjax.componentCategories[key]);
                //categoriesList += '<li ng-class="{\'active\': component.options[component.active.id][\'ct_category\'] === \''+CtBuilderAjax.componentCategories[key]+'\'}" ng-click="showCategorize=false; setComponentCategory('+item.id+', \''+CtBuilderAjax.componentCategories[key]+'\', $event)">'+CtBuilderAjax.componentCategories[key]+'</li>';
            }

        }
        
        return categoriesList;

    }

    // $scope.nodeShowMoreOptions = function(e, state) {
    //     state.showMoreOptions = !state.showMoreOptions;
    //     console.log(state.showMoreOptions);
    //     // let target = e.target.closest('.dom-tree-node');
    //     // $scope.$broadcast('nodeShowMore', target.getAttribute('ng-attr-tree-id'));
    // }


    /**
     * Apply iframe scope on UI scope digest
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
            if ($scope.iframeScope){
                $scope.iframeScope.safeApply();
            }
        });
    });


    /**
     * Check if component active by component id
     *
     * @since 0.1.6
     * @return {bool}
     */

    $scope.isActiveId = function(id) {

        if (!$scope.iframeScope) {
            return false;
        }

        return ( id == $scope.iframeScope.component.active.id ) ? true : false;
    }


    $scope.insertShortcodeToLink = function(text) {
        text=text.replace(/\"/ig, "'");
        angular.element('input#wp-link-url').val(text);
    }

    /**
     * Check if component active by component name
     * 
     * @since 0.1
     * @return {bool}
     */
    
    $scope.isActiveName = function(name) {

        if (!$scope.iframeScope) {
            return false;
        }

        return (name == $scope.iframeScope.component.active.name) ? true : false;
    }


    /**
     * Check if component parent active by component id
     *
     * @since 2.0
     * @return {bool}
     */

    $scope.isActiveParentId = function(id) {

        if (!$scope.iframeScope) {
            return false;
        }

        return ( id == $scope.iframeScope.component.active.parent.id ) ? true : false;
    }


    /**
     * Check if component parent active by component id
     *
     * @since 2.0
     * @return {bool}
     */

    $scope.isActiveParentName = function(name) {

        if (!$scope.iframeScope) {
            return false;
        }

        return ( name == $scope.iframeScope.component.active.parent.name ) ? true : false;
    }
    

    /**
     * Set a tab to show
     * 
     * @since 0.1.7
     */
    
    $scope.switchTab = function(tabGroup, tabName) {       

        window.currentCMEditor = false
        window.mixedCMEditors = false

        if (tabGroup=="advanced") {
            $scope.showAllStyles = false;
        
            if (["custom-js","custom-css","code-js","code-css","code-php","code-mixed"].indexOf(tabName)>=0) {
                $scope.expandSidebar();
            }
            else {
                $scope.toggleSidebar(true);
            }
        } else {
            $scope.iframeScope.selectedNodeType = null;
        }

        if (tabGroup=="sidePanel") {
            if ( $scope.tabs[tabGroup][tabName] != true ) {
                $scope.toggleSidePanel(true);
            }
            else {
                $scope.toggleSidePanel();
            }
        }

        $scope.tabs[tabGroup] = [];

        if (tabGroup !== "effects") {
            $scope.tabs["effects"] = [];
        }

        if (tabGroup=="components") {
            $scope.iframeScope.closeAllFolders();
        }
        
        switch (tabName) {
            // all tabs with children
            case "position" : 
                $scope.tabs[tabGroup][tabName] = {margin_padding:true};
                break;

            case "background" : 
                $scope.tabs[tabGroup][tabName] = {color:true};
                break;

            case "borders" : 
                $scope.tabs[tabGroup][tabName] = {border:true};
                break;

            case "cssjs" : 
                $scope.tabs[tabGroup][tabName] = {css:true};
                break;

            case "code" : 
                $scope.tabs[tabGroup][tabName] = {'code-php':true};
                break;

            // other regular tabs
            default :
                $scope.tabs[tabGroup][tabName] = ($scope.tabs[tabGroup][tabName]) ? false : true;
        }

        // if advanced/background tab is opened, collapse the background layers to default state
        if(tabGroup === 'advanced' && tabName === 'background')
            $scope.activeForEditBgLayer = false;

        $scope.showSVGIcons = false;
        
        $scope.disableSelectable();

    }


    /**
     * Check if any subtab open
     * 
     * @since 2.0
     * @author Ilya K.
     */
    
    $scope.hasOpenTabs = function(name) {

        if (undefined===$scope.tabs[name])
            return false;
        
        return Object.keys($scope.tabs[name]).length > 0;
    }

    /**
     * delete child values or query params in advanced query tab of repeater
     * 
     * @since 3.7
     * @author Gagan S Goraya
     */

    $scope.deleteDynamicQueryChild = function(children, e, selector) {
      var dex = angular.element(e.target).closest(selector).index();
      children.splice(dex, 1);
    }

    /**
     * load preset for advanced query for repeater/easy posts
     * 
     * @since 3.7
     * @author Gagan S Goraya
     */

    $scope.getDynamicQueryPreset = function(preset, params, title) {
      var paramEvaled = {};

      var finaly = function() {
        preset = JSON.stringify(preset);

        for(var i in paramEvaled) {
          var reg = new RegExp('\\{\\{'+i+'\\}\\}', "g");
          preset = preset.replace(reg, paramEvaled[i])
        }
        
        $scope.iframeScope.component.options[$scope.iframeScope.component.active.id].model['wp_query_advanced'] = JSON.parse(preset);
        $scope.iframeScope.setOption($scope.iframeScope.component.active.id, $scope.iframeScope.component.active.name, 'wp_query_advanced');

        if($scope.iframeScope.component.active.name === 'oxy_dynamic_list')  {
          $scope.iframeScope.updateRepeaterQuery();
        } else if($scope.iframeScope.component.active.name === 'oxy_posts_grid') {
          $scope.iframeScope.renderComponentWithAJAX('oxy_render_easy_posts');
        }

        $scope.iframeScope.component.options[$scope.iframeScope.component.active.id].model['wp_query_advanced_preset'] = title;
        $scope.iframeScope.setOption($scope.iframeScope.component.active.id, $scope.iframeScope.component.active.name, 'wp_query_advanced_preset');
      }

      if(params) {
        $scope.showDialogWindow();

        setTimeout(function() {
          var container = angular.element('.ct-dialog-window-content-wrap');
          
          var presetDialog = container.find('.ct-query-preset-dialog');

          if(presetDialog.length < 1) {
            presetDialog = angular.element('<div class="ct-query-preset-dialog"></div>');
            container.append(presetDialog);
          }
          
          presetDialog.html('');

          params.forEach(function(param) {
            var name = param['name'];
            var values = param['values'];
            var template = '';
            if(values) {
              template = '<div class="oxygen-control-row">'+
                  '<div class="oxygen-control-wrapper">'+
                      '<label class="oxygen-control-label">'+name+'</label>'+
                      '<div class="oxygen-select oxygen-select-box-wrapper">'+
                          '<div class="oxygen-select-box">'+
                              '<div class="oxygen-select-box-current"></div>'+
                              '<div class="oxygen-select-box-dropdown"></div>'+
                          '</div>'+
                          '<div class="oxygen-select-box-options">'+
                            (Object.values(values).map(function(item) { return '<div class="oxygen-select-box-option">'+item+'</div>'}).join("\n"))+
                          '</div>'+
                      '</div>'+
                  '</div>'+
                '</div>';
            } else {
              template = '<div class="oxygen-control-row">'+
                    '<div class="oxygen-control-wrapper">'+
                      '<label class="oxygen-control-label">'+name+'</label>'+
                      '<div class="oxygen-control">'+
                          '<div class="oxygen-input">'+
                              '<input type="text" spellcheck="false" >'+
                          '</div>'+
                      '</div>'+
                  '</div>'+
                '</div>';
            }

            presetDialog.append(template);

          })

          var button = angular.element('<div class="oxygen-apply-button">Apply</div>');
          
          button.on('click', function() {
            finaly();
            presetDialog.remove();
            $scope.hideDialogWindow();
          })

          presetDialog.on("click", ".oxygen-select", function(e) {
              $scope.toggleOxygenSelectBox(e, this);
          })

          // don't hide the box on input click
          .on("click", ".oxygen-select-box-option", function(e) {
              var value = e.target.innerText;
              angular.element(e.target).closest('.oxygen-select').find('.oxygen-select-box-current').text(value);
              paramEvaled[angular.element(e.target).closest('.oxygen-control-row').find('.oxygen-control-label').text()] = value;
          })

          .on("change", ".oxygen-input input", function(e) {
              var value = e.target.value;
              paramEvaled[angular.element(e.target).closest('.oxygen-control-row').find('.oxygen-control-label').text()] = value;
          })

          presetDialog.append(button);

         
        }, 200);

      } else {
        finaly();
      }


      
    }



    /**
     * Check if any subtab open
     * 
     * @since 2.0
     * @author Ilya K.
     */
    
    $scope.hasOpenChildTabs = function(name,child) {
        
        if ($scope.tabs[name]===undefined||$scope.tabs[name][child]===undefined)
            return false;

        if ($scope.tabs[name][child] == undefined)
            return false;

        return Object.keys($scope.tabs[name][child]).length > 0;
    }


    /**
     * Set advanced settings tab to show
     * 
     * @since 0.3.0
     */
    
    $scope.switchChildTab = function(tabGroup, tabName, childTabName) {

        if ( tabName=="cssjs" ) {
            $scope.tabs[tabGroup][tabName] = [];
            $scope.tabs[tabGroup][tabName][childTabName] = true;
            return false;
        }

        if ( !$scope.tabs[tabGroup] ) {
            $scope.tabs[tabGroup] = [];
        }

        if ( !$scope.tabs[tabGroup][tabName] || typeof $scope.tabs[tabGroup][tabName] !== "object") {
            $scope.tabs[tabGroup][tabName] = [];
        }

        $scope.tabs[tabGroup][tabName][childTabName] = ($scope.tabs[tabGroup][tabName][childTabName]) ? false : true;

        $scope.showSVGIcons = false;
        $scope.showAddGlobalColorPanel = false;
        $scope.addGlobalColorSetPanel = false;

        $scope.disableSelectable();
    }


    /**
     * Check if opened tab is not available for current component and switch to default
     * 
     * @since 0.2.4
     */

    $scope.checkTabs = function() {

        if ($scope.iframeScope.log) {
            console.log("checkTabs()")
        }

        if ( $scope.isActiveName("root") ) {
            $scope.closeAllTabs();
            return;
        }

        // check code block tabs
        if ( $scope.isActiveName("ct_code_block")
             && ( $scope.tabs.advanced['custom-js'] || 
             $scope.tabs.advanced['custom-css'] ) 
            ) {
                $scope.showAllStylesFunc();          
        }

        // check code block tabs
        if ( !$scope.isActiveName("ct_code_block")
             && ( $scope.tabs.advanced['code-js'] || 
             $scope.tabs.advanced['code-css'] || 
             $scope.tabs.advanced['code-php'] ||
             $scope.tabs.advanced['code-mixed'] ) 
            ) {
                $scope.showAllStylesFunc();          
        }

        // check widget
        /*else if ( $scope.isActiveName("ct_widget") ) {
            $scope.closeAllTabs(["componentBrowser"]);
        }
        // check shortcode
        else if ( $scope.isActiveName("ct_shortcode") ) {
            $scope.closeAllTabs(["componentBrowser"]);
        }*/
        // check others
        else if ( $scope.tabs.advanced['code'] && ( $scope.tabs.advanced['code']['code-php'] || 
                    $scope.tabs.advanced['code']['code-js'] ||
                    $scope.tabs.advanced['code']['code-css'] ) && $scope.iframeScope.component.active.name != "ct_code_block" ) {
            $scope.switchChildTab("advanced", "background", "color");
        }

        // check custom JS tab
        /*if ($scope.tabs.advanced['cssjs'] && $scope.tabs.advanced['cssjs']['js'] && ($scope.iframeScope.isEditing('media') || $scope.iframeScope.isEditing('class') || $scope.iframeScope.isEditing('state'))) {
            $scope.switchChildTab('advanced', 'cssjs', 'css');
        }*/
    }


    /**
     * Close all action tabs, except the tabs specified in keepTabs array
     * 
     * @since 0.1.7
     */
    
    $scope.closeAllTabs = function(keepTabs) {

        if ($scope.iframeScope.log) {
            console.log("closeAllTabs()", keepTabs);
        }
        
        if (keepTabs==undefined){
            keepTabs = [];
        }
        
        angular.forEach($scope.actionTabs, function(value, tab) {
            if (keepTabs.indexOf(tab) == -1) {
                $scope.actionTabs[tab] = false;
            }
        });

        $scope.showSVGIcons = false;

        $scope.adjustViewportContainer();
    }


    /**
     * Close a list of tabs or all of them
     * 
     * @since 2.0
     * @author Ilya K.
     */
    
    $scope.closeTabs = function(tabs) {

        if ($scope.iframeScope.log) {
            console.log("closeTabs()", tabs);
        }

        for (var key in $scope.tabs) {
            if ($scope.tabs.hasOwnProperty(key)) {

                if (tabs==undefined){
                    $scope.tabs[key] = false;
                }
                else if (tabs.indexOf(key) >= 0) {
                    $scope.tabs[key] = false;
                }
            }
        }
    }


    /**
     * Switch to code editor if Code Block is active
     * 
     * @since 1.3
     * @author Ilya K.
     */

    $scope.possibleSwitchToCodeEditor = function(tabGroup, tabName) {

        if ( $scope.isActiveName("ct_code_block") ) {
            $scope.switchActionTab("codeEditor");
            $scope.switchTab("codeEditor","code-css");
        }
        else {
            $scope.switchTab(tabGroup, tabName);   
        }
    }


    $scope.activateCopySelectorMode = function(className, event) {

        event.stopPropagation();

        if (className) {
          $scope.copySelectorFromClass = className;
        }
        else {
          $scope.copySelectorFromID = $scope.iframeScope.component.active.id;
        }
    }

    $scope.deactivateCopySelectorMode = function(event) {

        event.stopPropagation();

        $scope.copySelectorFromClass = false;
        $scope.copySelectorFromID = false;
    }


    /**
     * Show all styles tabs
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.showAllStylesFunc = function() {
        
        $scope.showAllStyles=true;
        $scope.tabs['advanced'] = [];
        $scope.tabs['effects'] = [];
        $scope.toggleSidebar(true);
    }


    /**
     * Toggle sidebar to/from 50%
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.toggleSidebar = function(forceCollapse) {

      if ($scope.iframeScope.log) {
        console.log("toggleSidebar()", forceCollapse);
      }

      var isExpanded = $scope.verticalSidebar.data("expanded"),
          button = jQuery('.oxygen-code-editor-expand', $scope.verticalSidebar);

      if (isExpanded) {
        // collapse
        $scope.verticalSidebar.css({'width': '300px'});
        $scope.adjustViewportContainer()
        $scope.verticalSidebar.data("expanded", false);
        jQuery(button).text(jQuery(button).attr('data-expand'));
      }
      else if (!forceCollapse) {
        // expand
        $scope.verticalSidebar.css({'width': '50%'});
        $scope.adjustViewportContainer();
        $scope.verticalSidebar.data("expanded", true);
        jQuery(button).text(jQuery(button).attr('data-collapse'));
      }

    }


    /**
     * Open sidebar to 50%
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.expandSidebar = function() {

        var timeout = $timeout(function() {
            var button = jQuery('.oxygen-code-editor-expand', $scope.verticalSidebar);

            $scope.verticalSidebar.css({'width': '50%'});
            $scope.adjustViewportContainer();
            $scope.verticalSidebar.data("expanded", true);
            jQuery(button).text(jQuery(button).attr('data-collapse'));
            
            $timeout.cancel(timeout);
        }, 0, false);   
    }

    
    /**
     * Show add new color dialog window
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.showAddNewColorDialog = function($event) {

        $scope.addNewColorDialog = true;
        $scope.addNewColorDialogEvent = $event;

        var input = jQuery($event.currentTarget).parents('.oxygen-color-picker').find('.oxygen-color-picker-color + input'),
            color = input.val();

        var timeout = $timeout(function() {
            jQuery('input', '#oxygen-global-colors-new-color-dialog').focus();
            $timeout.cancel(timeout);
        }, 0, false);

        $scope.addNewColorDialogValue = color;

        jQuery(document).on('keydown', $scope.colorDialogKeyDown);
    }


    /**
     * Hide add new color dialog window
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.hideAddNewColorDialog = function() {

        $scope.addNewColorDialog = false;
        $scope.addNewColorDialogEvent = false;
        $scope.addNewColorDialogValue = "";

        jQuery(document).off('keydown', $scope.colorDialogKeyDown);
    }


    /**
     * Keydown callback for global color dialog
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.colorDialogKeyDown = function($event) {
        // if Enter key pressed
        if ($event.which === 13) {
            $scope.iframeScope.addNewColor($scope.newGlobalSettingsColorName, $scope.colorSetIDToAdd, 'latest');
            var timeout = $timeout(function() {
                $scope.$apply();
                $timeout.cancel(timeout);
            }, 0, false);
        }
    }


    /**
     * Keydpress callback for Global Settings > New global color set 
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.addGlobalColorSetKeyPress = function($event) {
        // if Enter key pressed
        if ($event.which === 13) {
            $scope.iframeScope.addNewColorSet($scope.newGlobalColorSetName);
            var timeout = $timeout(function() {
                $scope.$apply();
                $timeout.cancel(timeout);
            }, 0, false);
        }
    }


    /**
     * Keydpress callback for Global Settings > new global color 
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.newGlobalColorNameKeyPress = function($event, setID) {
        // if Enter key pressed
        if ($event.which === 13) {
            $scope.iframeScope.addNewColor($scope.newGlobalColorName, setID, $scope.newGlobalColorValue);
            var timeout = $timeout(function() {
                $scope.$apply();
                $timeout.cancel(timeout);
            }, 0, false);
        }
    }


    /**
     * Set global color to the param
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.setGlobalColor = function(colorID, $event) {
        
        if ($event===undefined) {
            $event = $scope.addNewColorDialogEvent;
        }

        if ($scope.iframeScope.log){
            console.log("setGlobalColor()", colorID, $event)
        }

        if ($event===undefined) {
            return;
        }

        var input = jQuery($event.currentTarget).parents('.oxygen-color-picker').find('.oxygen-color-picker-color + input'),
            color = $scope.iframeScope.getGlobalColor(colorID);

        $scope.activeGlobalColor = color;

        if ("color("+colorID+")"!==input.val()) {
            input.val("color("+colorID+")");
        }
        else {
            // do nothing if global color already set
            return;
        }

        // make angular trigger the ng-change
        angular.element(input).triggerHandler('input');
    }


    /**
     * Update global color name and set 
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.updateGlobalColorValue = function(newValue) {
        
        $scope.globalColorToEdit.value = newValue;
    }


    /**
     * Update global color name and set 
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.updateGlobalColor = function(name, set) {
        
        if ($scope.iframeScope.log){
            console.log("updateGlobalColor()", name, set)
        }

        if (name!==undefined) {
            $scope.globalColorToEdit.name = name;
        }

        if (set!==undefined) {
            $scope.globalColorToEdit.set = set;
        }
    }


    /**
     * Unset global color
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.unsetGlobalColor = function($event) {

        var input = jQuery($event.currentTarget).parents('.oxygen-color-picker').find('.oxygen-color-picker-color + input');
        input.val("");

        // make angular trigger the ng-change
        angular.element(input).triggerHandler('input');
    }

    
    /**
     * Trigger on Settings -> Global Styles -> Colors update
     * 
     * @since 2.1
     * @author Ilya K.
     */

    $scope.globalColorChange = function(id) {

        $scope.iframeScope.classesCached = false;
        $scope.iframeScope.updateAllComponentsCacheStyles();
        $scope.iframeScope.outputCSSOptions();
        
        var timeout = $timeout(function() {
            $scope.$apply();
            $timeout.cancel(timeout);
        }, 0, false);
    }


    /**
     * Check is to show tab
     * 
     * @since 0.1.7
     * @return {bool}
     */
    
    $scope.isShowTab = function(tabGroup, tabName) {  

        if ( $scope.tabs[tabGroup] ) {
            return ( $scope.tabs[tabGroup][tabName] ) ? true : false;
        }
        else {
            return false;
        }
    }

    $scope.isShowTabOfGroup = function(tabGroup) {
        return $scope.tabs[tabGroup] && Object.keys($scope.tabs[tabGroup]).length > 0;
    }


    /**
     * Check is to show child tab
     * 
     * @since 0.3.0
     * @return {bool}
     */
    
    $scope.isShowChildTab = function(tabGroup, tabName, childTabName) {  

        if ( $scope.tabs[tabGroup] ) {
            return ( $scope.tabs[tabGroup][tabName] && $scope.tabs[tabGroup][tabName][childTabName] ) ? true : false;
        }
        else {
            return false;
        }
    }


    /**
     * Toggle Side Panel
     *
     * @since 0.1.5
     */

    $scope.toggleSidePanel = function(forceOpen) {

        if (forceOpen==true&&$scope.showSidePanel) {
            return
        } 

        $scope.showSidePanel = !$scope.showSidePanel;

        if (!$scope.showSettingsPanel) {
            if ($scope.showSidePanel) {
                $scope.sidePanelElement.css({width:"300px"});
            }
            else {
                $scope.sidePanelElement.css({width:"0px"});
            }
        }
        else {
            $scope.showSettingsPanel = false;
        }

        $scope.adjustViewportContainer();

        if (!$scope.showSidePanel) {
            $scope.disableSelectable();
        }
    }


    /**
     * Toggle Settings Panel
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.toggleSettingsPanel = function(forceOpen) {

        if (forceOpen===true) {
            $scope.showSettingsPanel = true;
        }
        else {
            $scope.showSettingsPanel = !$scope.showSettingsPanel;
        }

        if (!$scope.showSidePanel) {
            if ($scope.showSettingsPanel) {
                $scope.settingsPanelElement.css({right:"0px"});
            }
            else {
                $scope.settingsPanelElement.css({right:"-300px"});
            }
        }
        else {
            $scope.settingsPanelElement.css({
                right: "0px"
            });
            $scope.showSidePanel = false;
        }

        $scope.adjustViewportContainer();

        if (!$scope.showSettingsPanel) {
            $scope.disableSelectable();
        }
    }


    /**
     * Show editor panel for contenteditable elements
     *
     * @since 0.1.5
     */

    $scope.enableContentEdit = function(element) {

        if ( $scope.actionTabs["contentEditing"] == true ) {
            return false;
        }

        // Pause undo/redo watcher
        $scope.iframeScope.pauseDataWatcher();

        // switch edit to id
        $scope.iframeScope.switchEditToId();

        var activeComponent = $scope.iframeScope.getActiveComponent();

        if ( !element.is(activeComponent) ){
            activeComponent=element;
            $scope.builtinContentEditing = true;
        }
        else {
            $scope.builtinContentEditing = false;
        }
        
        if ( activeComponent[0].attributes['contenteditable'] ) {
            // FireFox fix for the invisible cursor issue 
            if ( $scope.isActiveName("ct_link_text") ) {
                jQuery("<input style='position:fixed;top:40%;left:40%' type='text'>").appendTo("body").focus().remove();
            }

            activeComponent[0].setAttribute("contenteditable", "true");
            activeComponent[0].setAttribute("spellcheck", "true");

            if(!$scope.iframeScope.isChrome) {
                $scope.iframeScope.disableElementDraggable(true);
            }            
            
            activeComponent.focus();
            
            $scope.iframeScope.setEndOfContenteditable(activeComponent[0]);

            $scope.actionTabs["contentEditing"] = true;
        }

        $scope.iframeScope.hideResizeBox(0.1);
    }


    /**
     * Hide editor panel for contenteditable elements
     *
     * @since 0.1.5
     */

    $scope.disableContentEdit = function() {

        if ( !$scope.actionTabs["contentEditing"] )
            return false;

        if ($scope.iframeScope.log) {
            console.log('disableContentEdit()');
        }

        var activeComponent = $scope.iframeScope.getActiveComponent();
        var activeComponentID = $scope.iframeScope.component.active.id;

        $scope.builtinContentEditing = false;

        // clear selection
        if (window.getSelection) {
            if (window.getSelection().empty) {  // Chrome
                window.getSelection().empty();
            } else if (window.getSelection().removeAllRanges) {  // Firefox
                window.getSelection().removeAllRanges();
                }
        } else if (document.selection) {  // IE?
            document.selection.empty();
        }

        $scope.iframeScope.dynamicListTextChanged = false;
        if($scope.iframeScope.contentEditableData.beingEdited != $scope.iframeScope.getOption('ct_content')) {
            $scope.iframeScope.dynamicListTextChanged = true;
        }

        var oxyDynamicList;

        if ( activeComponent[0].attributes['contenteditable'] ) {

            if(!$scope.iframeScope.isChrome) {
                $scope.iframeScope.disableElementDraggable(false);
            }
          
            var content = activeComponent.html();

            activeComponent.html("");

           /* var el = activeComponent[0];
            while ((el = el.parentElement) && !el.classList.contains('ct_link'));
            if(el)
                el.setAttribute("href", ''); */

            activeComponent[0].setAttribute("contenteditable", "false");
            activeComponent[0].removeAttribute("spellcheck");
            
            activeComponent.html(content);

            oxyDynamicList = activeComponent.closest('.oxy-dynamic-list');

            if ($scope.iframeScope.component.active.name != 'ct_span') {

                if(typeof(activeComponent[0].attributes['plaintext']) === 'undefined' || activeComponent[0].attributes['plaintext'] !== "true") {

                    var content = $scope.iframeScope.getOption('ct_content');
                    
                    content = content.replace(/\[oxygen[^\]]*\]/ig, function(match) {

                        // create a span component out of match
                        // embed it in the tree as a child of $scope.iframeScope.component.active.id
                        // get the new component's id

                        var newComponent = {
                          id : $scope.iframeScope.dynamicSpanCycleIDs.length > 0?$scope.iframeScope.dynamicSpanCycleIDs.shift():$scope.iframeScope.component.id++, 
                          name : "ct_span"
                        }

                        // set default options first
                        $scope.iframeScope.applyComponentDefaultOptions(newComponent.id, "ct_span");
                        
                        // insert new component to Components Tree
                        $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.insertComponentToTree, newComponent);

                        // update span options
                        $scope.iframeScope.component.options[newComponent.id]["model"]["ct_content"] = match;
                        
                        $scope.iframeScope.setOption(newComponent.id, "ct_span", "ct_content");

                        $scope.iframeScope.findParentComponentItem($scope.iframeScope.componentsTree, activeComponentID, $scope.iframeScope.updateCurrentActiveParent);

                        return "<span id=\"ct-placeholder-"+newComponent.id+"\"></span>"
                    });

                    $scope.iframeScope.setOptionModel('ct_content', content, $scope.iframeScope.component.active.id, $scope.iframeScope.component.active.name);

                }

                // update active parent
                $scope.iframeScope.findParentComponentItem($scope.iframeScope.componentsTree, $scope.iframeScope.component.active.id, $scope.iframeScope.updateCurrentActiveParent);

                var idToRebuild = $scope.iframeScope.component.active.id,
                    parent = $scope.iframeScope.getComponentById($scope.iframeScope.component.active.parent.id),
                    isParentContentEditable = (parent) ? parent[0].attributes['contenteditable'] : false;

                if (isParentContentEditable) {
                    idToRebuild = $scope.iframeScope.component.active.parent.id;
                }
                
                if($scope.iframeScope.dynamicListTextChanged) {
                  if(oxyDynamicList.length > 0 && !$scope.iframeScope.component.options[parseInt(oxyDynamicList.attr('ng-attr-component-id'))]['model']['listrendertype']) {
                    $scope.iframeScope.updateRepeaterQuery(parseInt(oxyDynamicList.attr('ng-attr-component-id')));
                  } else {
                    $scope.iframeScope.rebuildDOM(idToRebuild);
                  }
                }
                else {
                  $scope.iframeScope.rebuildDOM(idToRebuild);
                }

            } else {
                
                if(oxyDynamicList.length > 0) {
                    
                    var id = oxyDynamicList.attr('ng-attr-component-id');

                    angular.element('#ct-artificial-viewport').contents().find('[ng-attr-component-id="'+activeComponentID+'"][disabled="disabled"]').text(activeComponent.text());
                    

                    //$scope.iframeScope.dynamicListAction(id, activeComponentID);
                    //$scope.iframeScope.dynamicListAction(id);
                    // if($scope.iframeScope.contentEditableData.original.replace(/<span[^\/]*\/span>/ig, '') != $scope.iframeScope.getOption('ct_content', activeComponentID).replace(/<span[^\/]*\/span>/ig, '')) {
                    //     $scope.iframeScope.rebuildDOM(id);
                    // }
                }
                if($scope.iframeScope.contentEditableData.beingEdited != $scope.iframeScope.getOption('ct_content'))
                    $scope.iframeScope.rebuildDOM($scope.iframeScope.component.active.parent.id);
            }
            
        }
        else {
            
            var element = activeComponent.find("[contenteditable=true]");

            if (element.length > 0) {
                
                if(!$scope.iframeScope.isChrome) {
                    $scope.iframeScope.disableElementDraggable(false);
                }
              
                var content = element.html();

                element.html("");

                element[0].setAttribute("contenteditable", "false");
                element[0].removeAttribute("spellcheck");
                
                element.html(content);

                var option = element.data('optionname'),
                    content = $scope.iframeScope.getOption(option);

                content = content.replace(/\[oxygen[^\]]*\]/ig, function(match) {

                    var newComponent = {
                        id : $scope.iframeScope.dynamicSpanCycleIDs.length > 0?$scope.iframeScope.dynamicSpanCycleIDs.shift():$scope.iframeScope.component.id++, 
                        name : "ct_span"
                    }

                    // set default options first
                    $scope.iframeScope.applyComponentDefaultOptions(newComponent.id, "ct_span");

                    // insert new component to Components Tree
                    $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.insertComponentToTree, newComponent);

                    // update span options
                    $scope.iframeScope.component.options[newComponent.id]["model"]["ct_content"] = match;
                    $scope.iframeScope.setOption(newComponent.id, "ct_span", "ct_content");

                    return "<span id=\"ct-placeholder-"+newComponent.id+"\"></span>"
                });

                $scope.iframeScope.setOptionModel(option, content, $scope.iframeScope.component.active.id, $scope.iframeScope.component.active.name);

                var timeout = $timeout(function() {
                    $scope.$apply();
                    $timeout.cancel(timeout);
                }, 0, false);
            }
        }
        
        /*if($scope.iframeScope.component.active.name != 'ct_text_block')
            $scope.rebuildDOM($scope.iframeScope.component.active.id);*/

        $scope.actionTabs["contentEditing"] = false;
        $scope.showDataPanel = false;

        if(oxyDynamicList === undefined || oxyDynamicList === null || oxyDynamicList.length < 1) {
            $scope.iframeScope.adjustResizeBox();
        }

        // Resume undo/redo watcher
        $scope.iframeScope.resumeDataWatcher();
    }


    /**
     * Open TinyMCE dialog window and set the text from ct_content
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.openTinyMCEDialog = function() {

        $scope.tinyMCEWindow = true;
        var content = $scope.iframeScope.getOption("ct_content");
        
        if ( jQuery('#wp-oxygen_vsb_tinymce-wrap').hasClass('tmce-active') && tinyMCE.get("oxygen_vsb_tinymce") ) {
            tinyMCE.get("oxygen_vsb_tinymce").setContent(content);
        } else{
            jQuery('#oxygen_vsb_tinymce').val(content);
        }
    }


    /**
     * Close TinyMCE dialog window and set the text to ct_content
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.closeTinyMCEDialog = function() {

        $scope.tinyMCEWindow = false;
        var content = "";

        if ( jQuery('#wp-oxygen_vsb_tinymce-wrap').hasClass('tmce-active') && tinyMCE.get("oxygen_vsb_tinymce") ) {
            content = tinyMCE.get("oxygen_vsb_tinymce").getContent();
        }
        else {
            content = jQuery('#oxygen_vsb_tinymce').val();
        }

        $scope.iframeScope.setOptionModel("ct_content", content);
        $scope.iframeScope.setOption($scope.component.active.id, $scope.component.active.name, "ct_content");
    }

    /**
     * Trigger the "click" event on "browse" button for mediaurl if the user clicks
     * the INPUT element. Only for Attachment ID version of mediaurl
     *
     * @since 2.2
     *
     */

    $scope.triggerBrowseButton = function($event) {
        var timeout = $timeout(function() {
            angular.element($event.target).next().click()
            $timeout.cancel(timeout);
        }, 0, false);
        return false;
    }


    /**
     * Wrap active component with link (if not already a link) and show settings
     *
     * @since 0.1.6
     * @author Ilya K.
     */

    $scope.processLink = function() {

        $scope.iframeScope.cancelDeleteUndo();

        if ($scope.iframeScope.log){
            console.log("processLink()");
        }

        var linkComponentId = $scope.iframeScope.getLinkId();

        if (!linkComponentId) {

            // convert to Text Link
            if ($scope.isActiveName("ct_text_block")) {
                $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTreeComponentTag, "ct_link_text");
            }
            else
            // convert to Link Wrapper
            if ($scope.isActiveName("ct_div_block")) {
                $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTreeComponentTag, "ct_link");
                
                // convert all links inside div block
                $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTagsByName, 
                    {from:"ct_link_text",to:"ct_text_block"});
                $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTagsByName, 
                    {from:"ct_link",to:"ct_div_block"});
            }
            else
            if ($scope.iframeScope.component.active.name === 'ct_span') {
                $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTreeComponentTag, "ct_link_text");
    
                // rebuild parent
                var timeout = $timeout(function() {
                    $scope.iframeScope.rebuildDOM($scope.iframeScope.component.active.parent.id);
                    $timeout.cancel(timeout);
                }, 0, false);
            }
            // wrap with Link Wrapper
            else {
                var newComponentId = $scope.iframeScope.wrapComponentWith("ct_link");

                $scope.iframeScope.activateComponent(newComponentId, "ct_link");
            }
        }
        else {
            $scope.iframeScope.activateComponent(linkComponentId, "ct_link");
        }

        var button = jQuery('.oxygen-link-button');
        var timeout = $timeout(function() {
            jQuery('<textarea>')
                .attr('id', 'ct-link-dialog-txt')
                .css('display', 'none')
                .attr('data-linkProperty', button.attr('data-linkProperty'))
                .attr('data-linkTarget', button.attr('data-linkTarget'))
                .appendTo('body');

            wpLink.open('ct-link-dialog-txt'); //open the link popup*/
            
            jQuery('#wp-link-url').val($scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['url']);

            jQuery('#wp-link-target').prop( 'checked', '_blank' === $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['target'] );
            jQuery('#wp-link-wrap').removeClass('has-text-field');

            jQuery('#oxygen-link-data-dialog-opener').insertAfter(jQuery('#wp-link-wrap.has-text-field #wp-link-url'));
            jQuery('#oxygen-link-data-dialog').insertAfter(jQuery('#wp-link-wrap.has-text-field'));

            $scope.showLinkDataDialog = false;
            $scope.$apply();

            $timeout.cancel(timeout);
        }, 0, false);
    }

    
    /**
     * Convert link components from link Div or Text Block
     * 
     * @since 0.3.3
     * @author Ilya K.
     */

    $scope.removeLink = function() {

        // handle Text Link
        if ($scope.isActiveName("ct_link_text")) {

            var componentParent = $scope.getComponentById($scope.iframeScope.component.active.parent.id);

            if ( !componentParent[0] || componentParent[0].attributes['contenteditable'] ) {
                // convert a ct_link_text to ct_span
                $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTreeComponentTag, "ct_span");
                
                var placeholderID = $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['selector'];

                var parentContent = $scope.iframeScope.component.options[$scope.iframeScope.component.active.parent.id]["id"]['ct_content'];

                $scope.cleanReplace(placeholderID, "<span id=\"ct-placeholder-"+$scope.iframeScope.component.active.id+"\"></span>");
            }
            else {
                $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTreeComponentTag, "ct_text_block");
            }
        }

        // handle Link Wrapper
        if ($scope.isActiveName("ct_link")) {
            $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, $scope.iframeScope.component.active.id, $scope.iframeScope.updateTreeComponentTag, "ct_div_block");
        }
    }



    /**
     * Show overlay to prevent user action when save the page, etc
     * 
     * @since 0.1.3
     */

    $scope.showLoadingOverlay = function(trigger) {

        var pageOverlay = document.getElementById("ct-page-overlay");
            pageOverlay = angular.element(pageOverlay);

        $scope.overlaysCount++;

        //console.log("showLoadingOverlay()", trigger, $scope.overlaysCount);
        if ($scope.pageLoaded == true) {
            pageOverlay.show();
        }
    }


    /**
     * Remove overlay
     * 
     * @since 0.1.3
     */

    $scope.hideLoadingOverlay = function(trigger) {
        
        var pageOverlay = document.getElementById("ct-page-overlay");
            pageOverlay = angular.element(pageOverlay);

        $scope.overlaysCount--;

        //console.log("hideLoadingOverlay()", trigger, $scope.overlaysCount);
        // hide spinner only when all overlays closed
        if ($scope.overlaysCount === 0) {
            if( trigger == 'savePage()' && window.parent ){
                window.parent.postMessage({
                    type: 'blockSaved'
                },'*');
            }

            if( !pageOverlay.hasClass("transparent") ){
                pageOverlay.fadeOut(200, function(){
                    pageOverlay.addClass("transparent");
                });
            } else {
                pageOverlay.hide();
            }

            oxyEndProgressBar(200);
            var timeout = $timeout(function() {
                $scope.pageLoaded = true;

                $timeout.cancel(timeout);
            }, 200, false);
        }
    }


    /**
     * Show widget loading overlay
     * 
     * @since 2.0
     */

    $scope.showWidgetOverlay = function(id) {

        if ($scope.iframeScope.log) {
            console.log("showWidgetOverlay()", id);
        }

        var widget = $scope.iframeScope.getComponentById(id),
            position = widget.css("position");

        if (position == "static") {
            widget.addClass("oxygen-positioned-element");
        }

        widget.append("<div class='oxygen-widget-overlay'><i class='fa fa-cog fa-2x fa-spin'></i></div>");
    }


    /**
     * Hide widget loading overlay
     * 
     * @since 2.0
     */

    $scope.hideWidgetOverlay = function(id) {

        var widget = $scope.iframeScope.getComponentById(id);

        jQuery(".oxygen-widget-overlay", widget).remove();
        if(widget.length > 0) {
          widget.removeClass("oxygen-positioned-element");
        }
    }

    
    /**
     * Switch action tabs
     * 
     * @since 0.1.7
     */

    $scope.switchActionTab = function(action) {

        if ($scope.iframeScope.log) {
            console.log("switchActionTab()", action);
        }

        $scope.iframeScope.selectedNodeType = null;

        // Do not allow to edit the settings while editing inner_content
        if( action === 'settings' && jQuery('body').hasClass('ct_inner')) {
            alert('To edit the settings for this page, load the containing template in the builder.');
            return;
        }

        // on open Add+ section
        var forceComponentBrowser = false;
        if ( action == "componentBrowser" ) {
            $scope.showAllStylesFunc();
            $scope.iframeScope.stylesheetToEdit = false;
            $scope.styleTabAdvance = false;
            $scope.toggleSidebar(true);
            $scope.resetComponentsSearch();

            if ($scope.showLeftSidebar == false) {
                $scope.doShowLeftSidebar(false);
                forceComponentBrowser = true;
            }
        }

        // Check Code Block tabs
        if ( $scope.tabs.advanced['cssjs'] && (
             $scope.tabs.advanced['cssjs']['js'] ||
             $scope.tabs.advanced['cssjs']['css'] ) && 
             $scope.iframeScope.component.active.name == "ct_code_block" ) {
            
            //$scope.switchChildTab("advanced", "background", "color");
        }

        // check content editing
        if ( action == "contentEditing" ) {

            if ( !$scope.actionTabs["contentEditing"]) {
                $scope.enableContentEdit();
            } 
            else {
                $scope.disableContentEdit();
            }
        }
        else if ( action === 'styleSheet') {
            if($scope.iframeScope.stylesheetToEdit && $scope.iframeScope.stylesheetToEdit !== $scope.actionTabs[action]) {
                $scope.actionTabs = {};
                $scope.actionTabs[action] = $scope.iframeScope.stylesheetToEdit;
            }
            else {
                $scope.actionTabs[action] = false;
            }
        }
        else {
            
            // disable content editing
            $scope.disableContentEdit();

            // set tab flag
            if ( action == "componentBrowser" && forceComponentBrowser ) {
                $scope.actionTabs[action] = true;
            } else {
                if ( $scope.actionTabs[action] ) {
                    $scope.actionTabs[action] = false;
                } 
                else {
                    $scope.actionTabs = {};
                    $scope.actionTabs[action] = true;
                }
            }
        }

        $scope.adjustViewportContainer();
        $scope.disableSelectable();
    }


    /**
     * Activate action tabs
     * 
     * @since 0.1.7
     */

    $scope.activateActionTab = function(action) {

        // check content editing
        if ( action == "contentEditing" ) {
                
            // close all tabs before enable
            $scope.actionTabs = {};
            $scope.enableContentEdit();
        }
        else {
            
            // disable content editing
            $scope.disableContentEdit();

            $scope.actionTabs = {};
            $scope.actionTabs[action] = true;
        }
    }

    /**
     * Selectors Search
     */
    var selectorsSearchCache = {};

    var selectorsSearchCacheFiltered = [];
    var disabledSelectorsSearchCacheFiltered = [];
    
    $scope.filterSelectors = function() {

        var query = $scope.selectorsSearchQuery;

        if (!query) {
            return;
        }

        var classes = Object.values($scope.iframeScope.classes),
            selectors = Object.values($scope.iframeScope.customSelectors);

        classes = classes.concat(selectors);

        selectorsSearchCache.items = classes;
        selectorsSearchCache.fuse = new Fuse(classes, {
            shouldSort: true,
            tokenize: true,
            matchAllTokens: true,
            includeScore: true,
            threshold: 0.05,
            ignoreLocation:  true,
            minMatchCharLength: 1,
            keys: [
                { name: "key", weight: 1 },
            ]
        });

        var items = selectorsSearchCache.fuse.search(query);

        selectorsSearchCacheFiltered = items.filter(function(item) {
            var parent = item['item']['parent'],
                setName = item['item']['set_name'],
                parentStatus = 0;
            
            if ($scope.iframeScope.styleSets[setName]!==undefined) {
                parent = $scope.iframeScope.styleSets[setName]['parent'];
            }

            if (parent==-1){
                return false;
            }

            if ($scope.iframeScope.styleFolders[parent]!==undefined) {
                parentStatus = $scope.iframeScope.styleFolders[parent]['status'];
            }
            else {
                return true;
            }

            if (parentStatus==0) {
                return false;
            }
            else {
                return true;
            }
        });

        disabledSelectorsSearchCacheFiltered = items.filter(function(item) {
            return selectorsSearchCacheFiltered.indexOf(item) == -1;
        });

        selectorsSearchCacheFiltered = selectorsSearchCacheFiltered.map(function(item) {
            return item['item']['key'];
        });

        disabledSelectorsSearchCacheFiltered = disabledSelectorsSearchCacheFiltered.map(function(item) {
            return item['item']['key'];
        });

    }

    $scope.isInSelectorsSearchCache = function(selector) {
        return selectorsSearchCacheFiltered.indexOf(selector.key) != -1;
    }

    $scope.isInDisabledSelectorsSearchCache = function(selector) {
        return disabledSelectorsSearchCacheFiltered.indexOf(selector.key) != -1;
    }

    $scope.resetSelectorsSearch = function() {
        $scope.selectorsSearchQuery = "";
    }

    /**
     * Client-side components filtering based on search query.
     */
    $scope.filterComponents = function($event) {
        var query = $scope.componentsSearchQuery;
        var displayOriginalList = query ? 'none' : 'flex';
        var displayFilteredList = query ? 'flex' : 'none';
        searchElementOriginalList.css('display', displayOriginalList);
        searchElementFilteredList.css('display', displayFilteredList);
        searchElementNoResultsText.css('display', 'none');

        if (!query) return;

        var items = searchCache.fuse.search(query);

        if (items.length === 0) {
            searchElementNoResultsText.css('display', 'block');
            searchElementSearchResults.css('display', 'none');
            return;
        }

        searchElementSearchResults.removeClass('oxygen-add-search-result-highlighted');
        
        var scores = {};
        for (var i = 0; i < items.length; i++) {
            scores[items[i].item.id] = items[i].score;
        }

        searchElementSearchResults.sort(function(a, b) {
            var sa = scores[jQuery(a).data(searchIdKey)];
            var sb = scores[jQuery(b).data(searchIdKey)];
            // if not found, push to the end of sorted list
            if (typeof(sa) === 'undefined') sa = 1.0;
            if (typeof(sb) === 'undefined') sb = 1.0;
	    return sa - sb;
        }).appendTo(searchElementSearchResultsContainer);

        searchElementSearchResults.css('display', 'flex')
            .each(function() {
                if (!scores.hasOwnProperty(jQuery(this).data(searchIdKey))) {
                    jQuery(this).css('display', 'none');
                }
            });


        getSearchElementFirstVisible().addClass('oxygen-add-search-result-highlighted');
    }

    /**
     * Reset component search.
     */
    $scope.resetComponentsSearch = function() {
        searchElementSearchInput.val('');
        angular.element(searchElementSearchInput).triggerHandler('change');
        var timeout = $timeout(function() {
            searchElementSearchInput.focus();
        });
    }

    /**
     * Adds first filtered component from the toolbar, if search query is not empty.
     */
    $scope.addFilteredComponent = function($event) {
        if (!$scope.componentsSearchQuery) return;
        $timeout(function() {
            var firstSearchResult = getSearchElementFirstVisible();
            if (firstSearchResult.data(searchIdKey).startsWith('__reusable_')) {
                // reusable components click "Single" option on Enter
                angular.element(firstSearchResult.find('.oxygen-add-section-element-option:first')[0]).triggerHandler('click')
            } else {
                angular.element(firstSearchResult[0]).triggerHandler('click');
            }
        });
    }

    /**
     * Check if action tab is active
     * 
     * @since 0.1.7
     */
    $scope.isActiveActionTab = function(action) {

        return ( $scope.actionTabs[action] ) ? true : false;
    }

    $scope.showBackgroundLayer = function($event) {
        angular.element($event.target).closest('ul').find('> li > div').hide();
        angular.element($event.target).siblings('div').toggle();
    }

    $scope.toggleActiveForEditBgLayer = function(index, $event) {

        if($scope.activeForEditBgLayer === index) {
            $scope.activeForEditBgLayer = false;
        }
        else {
            $scope.activeForEditBgLayer = index;
        }
    }

    $scope.addBackgroundLayer = function(layerType) {

        var type = typeof($scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['background-layers']);
        
        if(type === 'string' || type === 'undefined') {
            $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['background-layers'] = [];
        }

        var layer = {
            type: layerType
        }

        if(layerType === 'image') {
            //units
            layer['background-size-width-unit'] = 'px';
            layer['background-size-height-unit'] = 'px';

            //units
            layer['background-position-left-unit'] = 'px';
            layer['background-position-top-unit'] = 'px';

        }
        else if(layerType === 'gradient') {
            layer['colors'] = [];

            layer['radial-position-top-unit'] = '%';
            layer['radial-position-left-unit'] = '%';

        }

        var layers = $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['background-layers'];
        layers.push(layer);
        $scope.iframeScope.setOptionModel('background-layers', layers);
        
    }

    $scope.addGradientColor = function() {
                
        var id = $scope.iframeScope.component.active.id;
        var name = $scope.iframeScope.component.active.name;

        var gradient = $scope.iframeScope.component.options[id]['model']['gradient'];
        
        var type = typeof(gradient);

        if(type === 'string' || type === 'undefined') {
            gradient = {};
        }

        gradient['colors'] = gradient['colors'] || [];

        gradient['colors'].push({
            'position-unit': 'px'
        })

        $scope.iframeScope.component.options[id]['model']['gradient'] = gradient;

        $scope.iframeScope.setOption(id, name, 'gradient');
        
    }

    $scope.removeBackgroundLayer = function($event) {

        var index = angular.element($event.target).closest('li').index();

        var layers = $scope.iframeScope.getOption('background-layers');

        layers.splice(index, 1);

        $scope.iframeScope.setOptionModel('background-layers', layers);

    }

    $scope.removeGradientColor = function($event, index) {
        
        var gradient = $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['gradient'];

        gradient['colors'].splice(index, 1);

        $scope.iframeScope.setOptionModel('gradient', gradient);
    }

    $scope.setGradientForBG = function() {

        var gradient = $scope.iframeScope.getOption('gradient');
        $scope.iframeScope.setOptionModel('gradient', gradient);
    }

    $scope.toggleGradientRadio = function(param, value, index, $event) {
        // specific to background layers
        var gradient = $scope.iframeScope.getOption('gradient');
        
        if(gradient[param] === value) {
            delete(gradient[param]);
            angular.element($event.target).prop('checked', false);
        }
        else {
            gradient[param] = value;
        }

        $scope.iframeScope.setOptionModel('gradient', gradient);

    }


    $scope.removeCustomAttribute = function($event, index) {
        
        var customAttributes = $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['custom-attributes'];

        customAttributes.splice(index, 1);

        $scope.iframeScope.setOptionModel('custom-attributes', customAttributes);
    }


    $scope.toggleCustomAttribute = function(index) {

        if ($scope.isCustomAttributePinned(index)) {

            $scope.unpinCustomAttribute(index)
        }
        else {
            $scope.pinCustomAttribute(index)
        }
    }

    $scope.pinCustomAttribute = function(index) {

        var customAttributes = $scope.iframeScope.getOption('custom-attributes');

        if (customAttributes[index]) {
            customAttributes[index].pinned = true;
            $scope.iframeScope.setOptionModel('custom-attributes', customAttributes);
        }
    }


    $scope.unpinCustomAttribute = function(index) {

        var customAttributes = $scope.iframeScope.getOption('custom-attributes');

        if (customAttributes[index]) {
            customAttributes[index].pinned = false;
            $scope.iframeScope.setOptionModel('custom-attributes', customAttributes);
        }
    }


    $scope.isCustomAttributePinned = function(index) {

        var customAttributes = $scope.iframeScope.getOption('custom-attributes');

        if (!customAttributes[index]) {
            return false
        }

        return customAttributes[index].pinned;
    }


    $scope.addCustomAttribute = function(attrName, attrValue) {

        if (undefined === attrName) {
            attrName == ""
        }
        if (undefined === attrValue) {
            attrValue == ""
        }
                
        var id = $scope.iframeScope.component.active.id;
        var name = $scope.iframeScope.component.active.name;

        var customAttributes = $scope.iframeScope.component.options[id]['model']['custom-attributes'];
        
        var type = typeof(customAttributes);

        if(type === 'string' || type === 'undefined') {
            customAttributes = [];
        }

        customAttributes.push({
          name: attrName,
          value: attrValue,
        })

        $scope.iframeScope.component.options[id]['model']['custom-attributes'] = customAttributes;
        $scope.iframeScope.setOption(id, name, 'custom-attributes');
    }

    $scope.checkBoxClick = function(componentName, paramName, paramValue) {

        if ( paramName == 'display' && paramValue == 'grid' ) {
            
            var modelValue = $scope.iframeScope.getOption(paramName);

            if ( modelValue == 'grid' && $scope.iframeScope.isInherited($scope.iframeScope.component.active.id, paramName, paramValue) && $scope.iframeScope.isEditing("media")) {
                var timeout = $timeout(function() {
                    $scope.iframeScope.setOptionModel(paramName, 'grid');
                    $timeout.cancel(timeout);
                }, 100, false);
            }
        }

    }

    /**
     * Uncheck radio button
     * 
     * @since 0.2.3
     */

    $scope.radioButtonClick = function(componentName, paramName, paramValue) {

        if ($scope.iframeScope.log) {
            console.log("radioButtonClick()", componentName, paramName, paramValue);
        }
        
        var modelValue      = $scope.iframeScope.getOption(paramName),
            defaultValue    = $scope.iframeScope.defaultOptions[componentName][paramName];

        if ($scope.iframeScope.isEditing("custom-selector")) {
            var idValue = $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]["model"][paramName];
        }
        else {
            var idValue = $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]["id"][paramName];   
        }
        
        if ($scope.iframeScope.isEditing("id") && !$scope.iframeScope.isEditing("media") && !$scope.iframeScope.isEditing("state")) {
            // set
            if ( modelValue == paramValue && !idValue ) {
                $scope.iframeScope.setOptionModel(paramName, paramValue);
            }
        }
        else {
            idValue = true;
        }

        // Check out the inherited value and prevent empty value set when changing breakpoints
        if ($scope.iframeScope.isInherited($scope.iframeScope.component.active.id,paramName,paramValue) && $scope.iframeScope.isEditing("media")) {
            $scope.iframeScope.setOptionModel(paramName, paramValue);
            idValue = false;
        }

        // unset
        if ( modelValue == paramValue && idValue ) {
            $scope.iframeScope.setOptionModel(paramName, "");
        }

        // Only one modal can be in "live preview" model at once
        if( componentName == "ct_modal" ) {
            if( typeof $scope.currentModal === 'undefined' ) {
                $scope.currentModal = $scope.iframeScope.component.active.id;
            } else if( $scope.currentModal != $scope.iframeScope.component.active.id  ) {

                if(paramValue == "2") {
                    if($scope.iframeScope.component.options[$scope.currentModal]["model"]["behavior"] == "2") $scope.iframeScope.setOptionModel( "behavior", "1", $scope.currentModal );
                    $scope.currentModal = $scope.iframeScope.component.active.id;
                }

            }
        }
    }

    $scope.acfRepeaterDynamicDialogProcess = function(id) {
        
        $scope.iframeScope.dynamicShortcodesContentMode[0]['children'] = _.reject($scope.iframeScope.dynamicShortcodesContentMode[0]['children'], function(item) { return item.data == 'acfreparray'});
        $scope.iframeScope.dynamicShortcodesImageMode[0]['children'] = _.reject($scope.iframeScope.dynamicShortcodesImageMode[0]['children'], function(item) { return item.data == 'acfreparray'});
        $scope.iframeScope.dynamicShortcodesLinkMode[0]['children'] = _.reject($scope.iframeScope.dynamicShortcodesLinkMode[0]['children'], function(item) { return item.data == 'acfreparray'});

        if(!id) {
          return;
        }

        var repeater = $scope.iframeScope.component.options[id]['model'] ? $scope.iframeScope.component.options[id]['model']['acf_repeater'] : null;

        


        if(repeater && repeater != '') {
            
            var fields = {};

            $scope.iframeScope.acfRepeaters[repeater]['fields'].forEach(function(item) {
                fields[item.label] = item.name;
            });

            var data = {
                name: "Repeater Field", 
                data:"acfreparray", 
                properties: [
                    {
                        data: "field", 
                        name:"Field", 
                        type:"select", 
                        options: fields
                    },
                    {
                        data: "repeater",
                        type: "hidden",
                        value: repeater
                    }
                ]
            };

            $scope.iframeScope.dynamicShortcodesContentMode[0]['children'].push(data);
            $scope.iframeScope.dynamicShortcodesImageMode[0]['children'].push(data);
            $scope.iframeScope.dynamicShortcodesLinkMode[0]['children'].push(data);
        }
    }

    $scope.metaBoxRepeaterDynamicDialogProcess = function(id) {

        $scope.iframeScope.dynamicShortcodesContentMode[0]['children'] = _.reject($scope.iframeScope.dynamicShortcodesContentMode[0]['children'], function(item) { return item.data == 'metaboxreparray'});
        $scope.iframeScope.dynamicShortcodesImageMode[0]['children'] = _.reject($scope.iframeScope.dynamicShortcodesImageMode[0]['children'], function(item) { return item.data == 'metaboxreparray'});
        $scope.iframeScope.dynamicShortcodesLinkMode[0]['children'] = _.reject($scope.iframeScope.dynamicShortcodesLinkMode[0]['children'], function(item) { return item.data == 'metaboxreparray'});

        if(!id) {
          return;
        }

        var group = $scope.iframeScope.component.options[id]['model'] ? $scope.iframeScope.component.options[id]['model']['metabox_group'] : null;

        if(group && group != '') {
            
            var fields = {};

            $scope.iframeScope.metaBoxGroupFields[group]['fields'].forEach(function(item) {
                fields[item.name] = item.id;
            });

            var data = {
                name: "MetaBox Group Field", 
                data:"metaboxreparray", 
                properties: [
                    {
                        data: "field", 
                        name:"Field", 
                        type:"select", 
                        options: fields
                    },
                    {
                        data: "repeater",
                        type: "hidden",
                        value: group
                    }
                ]
            };

            $scope.iframeScope.dynamicShortcodesContentMode[0]['children'].push(data);
            $scope.iframeScope.dynamicShortcodesImageMode[0]['children'].push(data);
            $scope.iframeScope.dynamicShortcodesLinkMode[0]['children'].push(data);
        }
    }

    /**
     * Uncheck radio button
     * 
     * @since 2.0
     * @author Ilya K.
     */

    $scope.globalSettingsRadioButtonClick = function(obj, param, value) {

        if ($scope.iframeScope.log) {
            console.log("globalSettingsRadioButtonClick()", param, value);
        }
        
        if (obj[param] == value) {
            obj[param] = "";
        }   
    }


    /**
     * Show pop-up dialog with options
     * 
     * @since 0.2.3
     */
    
    $scope.showDialogWindow = function() {
        
        $scope.dialogWindow = true;
    }


    /**
     * Hide pop-up dialog with options
     * 
     * @since 0.2.3
     */
    
    $scope.hideDialogWindow = function() {
        
        $scope.dialogWindow = false;

        // hide forms
        $scope.dialogForms = [];
        
        jQuery(document).off("keydown", $scope.switchComponent);
    }


    /**
     * Enable/disable selectable for DOM Tree
     * 
     * @since 0.2.4
     * @deprecated
     */

    $scope.switchSelectable = function() {

        if ( $scope.isSelectableEnabled ) {
            $scope.disableSelectable();
        }
        else {
            $scope.enableSelectable();          
        }
    }


    /**
     * Enable selectable for DOM Tree
     * 
     * @since 0.2.4
     * @deprecated
     */

    $scope.enableSelectable = function() {

        if ( $scope.isSelectableEnabled ) {
            return;
        }

        if ($scope.iframeScope.log) {
            console.log("enableSelectable()");
        }

        // fake component
        $scope.activateComponent(-2); // "-1" is for custom selectors

        $scope.isSelectableEnabled = true;

        // init nuSelecatble plugin
        $scope.selectable = angular.element("#ct-dom-tree").nuSelectable({
            items: '.ct-dom-tree-name',
            selectionClass: 'ct-selection-box',
            selectedClass: 'ct-selected-dom-node',
            autoRefresh: 'true',
            onMove: function(selected) {
                if (selected.length > 0 ) {
                    $scope.isDOMNodesSelected = true;
                }
                else {
                    $scope.isDOMNodesSelected = false;
                };
                $scope.$apply();
            },
            onMouseDown: function() {
                $scope.isDOMNodesSelected = false;
                $scope.$apply();
            }
        });
    }

    /**
     * Disable selectable for DOM Tree
     * 
     * @since 0.2.4
     * @deprecated
     */

    $scope.disableSelectable = function() {

        return false;

        if ( !$scope.isSelectableEnabled ) {
            return;
        }

        $scope.isSelectableEnabled = false;

        // remove data and events
        if ( $scope.selectable ) {
            $scope.selectable.removeData();
            $scope.selectable.unbind('mousedown mouseup');
        }

        // clear selection
        $scope.selectable.find('.ct-selected-dom-node').removeClass('ct-selected-dom-node');

        // activate root
        $scope.activateComponent(0, 'root');
    }
    

    /**
     * Check if componenet is in viewport 
     * 
     * @since 0.3.0
     */

    $scope.isElementInViewport = function(el, threshold) {


        //special bonus for those using jQuery
        if (typeof jQuery === "function" && el instanceof jQuery) {
            el = el[0];
        }

        if (typeof el.getBoundingClientRect !== "function") {
            return false;
        }

        var rect = el.getBoundingClientRect();

        if ( rect.top >= ($scope.artificialViewport[0].contentWindow.innerHeight || $scope.artificialViewport[0].contentWindow.document.documentElement.clientHeight) ) {
            return "below";
        }

        var bottom = threshold ? (rect.top+threshold):rect.bottom;

        if (  bottom <= 0 ) {
            return "above";
        }

        return "visible";

        //rect.top >= 0 &&
        //rect.left >= 0 &&
        //rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) //&& /*or $(window).height() */
        //rect.right <= (window.innerWidth || document.documentElement.clientWidth) /*or $(window).width() */
    }

    
    /**
     * Smooth scroll window to component by selector
     * 
     * @since 0.3.0
     */
    
    $scope.scrollToComponent = function(selector, threshold) {

        if(typeof(threshold) === 'undefined') {
            threshold = 100;
        }

        if ($scope.iframeScope.log) {
            console.log("scrollToComponent() #"+ selector);
        }
        var target = false;

        try {
          target = $scope.artificialViewport.contents().find('#'+selector);
        }
        catch(e) {
          return;
        }

        if ( $scope.isElementInViewport(target, threshold) == "above" ) {
        
            $scope.artificialViewport.contents().find('html,body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }

        if ( $scope.isElementInViewport(target, threshold) == "below" ) {
        
            $scope.artificialViewport.contents().find('html,body').stop().animate({
                scrollTop: target.offset().top - window.innerHeight + target.outerHeight() + 100
            }, 500);
        }
    };


    /**
     * Show status with a status message
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.showStatusBar = function(status) {

        $scope.statusMessage = status;
        $scope.statusBarActive = true;
    }

    
    /**
     * Hide status bar
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.hideStatusBar = function() {

        $scope.statusBarActive = false;
        var timeout = $timeout(function() {
            $scope.statusMessage = "";
            $timeout.cancel(timeout);
        }, 400, false);
    }


    /**
     * Insert HTML
     *
     * @since 2.0
     * @author Ilya K.
     */

    $scope.cleanInsertUI = function(element, parentElement, index) {

        if ($scope.iframeScope.log) {
            console.log("cleanInsertUI()",parentElement,index);
        }
            
        if ( parentElement ) {
            parentElement = jQuery(parentElement);
            parentElement.html("");
            $scope.insertAtIndexUI(element, parentElement, index);
        } 
        else {
            angular.element(element).replaceWith(element);
        }
    }

    /**
     * Compile and insert HTML with ng-attrs
     *
     * @since 3.0
     * @author Ilya K.
     */

    $scope.compileInsertUI = function(element, parentElement, index) {

        angular.element(document).injector().invoke(['$compile',function($compile) {

          var newScope        = $scope.$new();
          var compiledElement = $compile(element)(newScope);
            
          if ( parentElement ) {
              parentElement = jQuery(parentElement);
              $scope.insertAtIndexUI(compiledElement, parentElement, index);
          } 
          else {
              angular.element(element).replaceWith(compiledElement);
          }
        }]);
    }


    /**
     * Insert child DOM element at a specific index in a parent element
     *
     * @since 0.1.7
     * @author Ilya K.
     */

    $scope.insertAtIndexUI = function(child, parent, index) {

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

    $scope.applyMenuAim = function() {
        jQuery('.oxygen-add-section-library-flyout-panel').off('mouseleave');
        jQuery('.oxygen-add-section-library-flyout-panel').off('mouseenter');
        jQuery('.oxygen-add-section-library-flyout-panel').on('mouseenter', function() {
            jQuery(this).addClass('oxygen-add-section-library-flyout-panel-open');
        });
        jQuery('.oxygen-add-section-library-flyout-panel').on('mouseleave', function() {
            jQuery(this).removeClass('oxygen-add-section-library-flyout-panel-open');
            jQuery('.oxygen-add-section-library-menu-subcategories a.oxygen-add-section-library-menu-subcategories-active').removeClass('oxygen-add-section-library-menu-subcategories-active');
        });
        setTimeout(function() {
            jQuery('.oxygen-add-section-library-menu-subcategories').menuAim({
                activate: function(e) {
                    
                    jQuery('.oxygen-add-section-library-flyout-category').css('display', 'none');
                    jQuery('#category-' + jQuery(e).data('cat')).css('display', 'block');

                    jQuery(e).addClass('oxygen-add-section-library-menu-subcategories-active');
                    jQuery('.oxygen-add-section-library-flyout-panel').addClass('oxygen-add-section-library-flyout-panel-open');

                },
                deactivate: function(e) {

                    jQuery('.oxygen-add-section-library-menu-subcategories a.oxygen-add-section-library-menu-subcategories-active').removeClass('oxygen-add-section-library-menu-subcategories-active');
                    jQuery('.oxygen-add-section-library-flyout-panel').removeClass('oxygen-add-section-library-flyout-panel-open');

                },
                exitMenu: function(e) {
                    setTimeout(function() {

                        if(!jQuery('.oxygen-add-section-library-flyout-panel').hasClass('oxygen-add-section-library-flyout-panel-open')) {
                            jQuery('.oxygen-add-section-library-menu-subcategories a.oxygen-add-section-library-menu-subcategories-active').removeClass('oxygen-add-section-library-menu-subcategories-active');
                        }

                    }, 100);

                    jQuery('.oxygen-add-section-library-flyout-panel').removeClass('oxygen-add-section-library-flyout-panel-open');

                },
                rowSelector: "> a",
            });
        }, 100);
    }


    /**
     * All UI/jQuery stuff here
     * 
     * @since 0.3
     */
    
    $scope.setupUI = function() {

        // unfocus colorpicker to prevent digest infinte loop on Enter keydown
        jQuery('body').on('mouseup', function() {
            jQuery('.iris-square-value:focus').blur();
        });

        /**
         * Hide Colorpicker on iframe document click
         */
         
        jQuery($scope.artificialViewport[0].contentWindow.document)
        .on("click", function(e) {
            // needed to hide colorpicker
            if(!e.target.getAttribute('contenteditable')&&!jQuery(e.target).closest('.ct-active').attr('contenteditable'))
                jQuery("html,body").trigger("click");
        })

        jQuery(document).ready(function() {

            jQuery('body').on('change', '#oxygen-stylesheet-folder-dropdown', function(e) {
                
                $scope.iframeScope.stylesheetToEdit['parent'] = jQuery(e.target).val();
                $scope.$apply();

            });

        });




        /**
         * Apply sticky-header class if scrolled enough
         */

        $scope.scrollTopOld = 0;

        jQuery($scope.artificialViewport[0].contentWindow).scroll(function() {           
            $scope.adjustStickyHeaders($scope.artificialViewport[0].contentWindow)
        })

        $scope.adjustStickyHeaders = function(windowObj) {

            $scope.artificialViewport.contents().find(".oxy-sticky-header").each(function(){

                // skip header with no ng-attributes, this is reusable part and have js code in place already
                if (jQuery(this).attr("ng-attr-component-id")===undefined) {
                    return;
                }
                
                var headerID    = jQuery(this).attr("ng-attr-component-id"),
                    selector    = "#"+$scope.iframeScope.component.options[headerID]["selector"],
                    header      = $scope.artificialViewport.contents().find(selector),
                    scrollval   = $scope.iframeScope.component.options[headerID]["model"]["sticky_scroll_distance"],
                    onlyUpward  = $scope.iframeScope.component.options[headerID]["model"]["sticky_header_upward"],
                    isFade      = $scope.iframeScope.component.options[headerID]["model"]["sticky_header_fade_in"] == 'yes',
                    stickySize  = parseInt($scope.iframeScope.getMediaMinSize($scope.iframeScope.component.options[headerID]['model']['sticky-media'])) || 0;

                if (onlyUpward=='yes') { // easiest but not the most intelegnece way
                    if ( (!scrollval || scrollval < 1 || jQuery(windowObj).scrollTop() > scrollval) &&
                        jQuery(windowObj).scrollTop() < $scope.scrollTopOld ){
                        if ($scope.artificialViewport.width() > stickySize && !header.hasClass("oxy-sticky-header-active")){
                            if (header.css('position')!='absolute') {
                                $scope.artificialViewport.contents().find("body").css("margin-top", header.outerHeight());
                            }
                            header.addClass("oxy-sticky-header-active");
                            if (isFade) {
                                header.addClass("oxy-sticky-header-fade-in");
                            }
                        }
                    }
                    else {
                        header.removeClass("oxy-sticky-header-fade-in").removeClass("oxy-sticky-header-active");
                        if (header.css('position')!='absolute') {
                            $scope.artificialViewport.contents().find("body").css("margin-top", "");
                        }
                    }
                }
                else {
                    if (!scrollval || scrollval < 1 || jQuery(windowObj).scrollTop() > scrollval ){
                        if ($scope.artificialViewport.width() > stickySize && !header.hasClass("oxy-sticky-header-active")){
                            if (header.css('position')!='absolute') {
                                $scope.artificialViewport.contents().find("body").css("margin-top", header.outerHeight());
                            }
                            header.addClass("oxy-sticky-header-active");
                            if (isFade) {
                                header.addClass("oxy-sticky-header-fade-in");
                            }
                        }
                    }
                    else {
                        header.removeClass("oxy-sticky-header-fade-in").removeClass("oxy-sticky-header-active");
                        if (header.css('position')!='absolute') {
                            $scope.artificialViewport.contents().find("body").css("margin-top", "");
                        }
                    }  
                }

                $scope.scrollTopOld = jQuery(windowObj).scrollTop();
            })

        }


        /**
         * Highlight components on hover
         */
        
        // DOM
        $scope.builderElement
        .on("mouseover", ".ct-component:not(.ct-contains-oxy)", function(e){
            e.stopPropagation();
            $scope.artificialViewport.contents().find('.ct-highlight').removeClass('ct-highlight');

            // in case we are editing the ct_inner content, then no need to hilight the outer template elements
            if(jQuery('body').hasClass('ct_inner') 
                && (jQuery(this).hasClass('ct-inner-content') || jQuery(this).closest('.ct-component.ct-inner-content').length < 1 )) {
                return;
            }

            if (jQuery(this).parent().is('.oxy-header-container')) {
                // highlight header row when hover  left/right/center sections
                jQuery(this).parents('.oxy-header-row').addClass('ct-highlight');
            }
            else {
                jQuery(this).addClass('ct-highlight');
            }
        })
        .on("mouseout", ".ct-component", function(e){
            e.stopPropagation();
            if (jQuery(this).parent().is('.oxy-header-container')) {
                // highlight header row when hover  left/right/center sections
                jQuery(this).parents('.oxy-header-row').removeClass('ct-highlight');
            }
            jQuery(this).removeClass('ct-highlight');
        })

        $scope.oxygenUIElement
        // DOM Tree
        .on("mouseover", ".ct-dom-tree-node-anchor", function(e){
            var componentId = jQuery(this).attr("ng-attr-node-id");
            $scope.artificialViewport.contents().find('.ct-component[ng-attr-component-id="'+componentId+'"]').addClass('ct-highlight');
        })
        .on("mouseout", ".ct-dom-tree-node-anchor", function(e){
            var componentId = jQuery(this).attr("ng-attr-node-id");
            $scope.artificialViewport.contents().find('.ct-component[ng-attr-component-id="'+componentId+'"]').removeClass('ct-highlight');
        })

        // Resize box titlebar
        $scope.artificialViewport.contents().find('body')
        .on("mouseover", "#oxygen-resize-box-parent-titlebar, .oxygen-resize-box-top", function(e){
            var componentId = $scope.iframeScope.component.active.parent.id;
            $scope.artificialViewport.contents().find('.ct-component[ng-attr-component-id="'+componentId+'"]').addClass('ct-highlight');
        })
        .on("mouseout", "#oxygen-resize-box-parent-titlebar, .oxygen-resize-box-top", function(e){
            var componentId = $scope.iframeScope.component.active.parent.id;
            $scope.artificialViewport.contents().find('.ct-component[ng-attr-component-id="'+componentId+'"]').removeClass('ct-highlight');
        })


        /*
         * Open colorpciker on Global color label click
         */

        $scope.oxygenUIElement
        .on("click", ".oxy-global-color-label", function(e){
            if(CtBuilderAjax.freeVersion) {
                $scope.showDialogWindow();
                $scope.dialogForms['showProGlobalColorEditDialog'] = true;
                return;
            }

            if (jQuery(e.target).is('.oxy-global-color-label-remove')) {
                // skip if just removing the global color
                return;
            }
            var activeColorPicker = jQuery('.wp-picker-active');
            if (activeColorPicker.length===0) {
                $scope.editGlobalColor = true;
            }
            jQuery(e.target).siblings(".oxygen-color-picker-color").find(".wp-color-result").trigger('click');
            e.stopPropagation();
        })

        
        /*
         * Global colors panel
         */

        jQuery('body').on('click.wpcolorpicker', function(e) {

            var activeColorPicker = jQuery('.wp-picker-active'),
                input = activeColorPicker.parents('.oxygen-color-picker').find('.oxygen-color-picker-color + input'),
                globalColorID = $scope.iframeScope.getGlobalColorID(input.val());

            $scope.globalColorToEdit = {};
            if (globalColorID && $scope.editGlobalColor) {             
                $scope.activeGlobalColor = $scope.iframeScope.getGlobalColor(globalColorID);
                $scope.globalColorToEdit = $scope.iframeScope.getGlobalColor(globalColorID);
            }
            else {
                $scope.activeGlobalColor = false;
            }

            $scope.editGlobalColor = false;

            // safely apply scope
            var timeout = $timeout(function() {
                $scope.$apply();
                $timeout.cancel(timeout);
            }, 0, false);

            if ($scope.globalColorToEdit.id!==undefined) {
                jQuery(".oxygen-global-colors-wrap",activeColorPicker).remove();
                return false;
            }
            
            // don't show for Settings > Global Colors
            if (jQuery(".oxy-settings-global-styles-color",activeColorPicker).length) {
                return false;
            }

            var globalColorsHTML = 
            '<div class="oxygen-global-colors-wrap">' +
                '<div class="oxygen-global-colors-sets">' +
                    '<div class="oxygen-global-colors-set" ' +
                        'ng-repeat="(setID,set) in iframeScope.globalColorSets.sets" ' +
                        'ng-show="iframeScope.globalColorSetHasColors(set.id)">' +
                        '<div class="oxygen-global-colors-set-heading">' +
                            '<h2>{{set.name}}</h2>' +
                        '</div>' +
                        '<div class="oxygen-global-colors">' +
                            //'<div class="oxygen-add-global-color-button" ' +
                                //'ng-click="$parent.colorSetNameToAdd=set.name;$parent.colorSetIDToAdd=set.id">+ add color</div>' +
                            '<div class="oxygen-global-color" title="{{color.name}}"' +
                                'ng-repeat="(key,color) in iframeScope.globalColorSets.colors | filter: {set: set.id}" ' +
                                'ng-style="{backgroundColor:color.value}" '+
                                'ng-class="{\'oxygen-active-global-color\':color.id==activeGlobalColor.id}" '+
                                'ng-click="setGlobalColor(color.id,$event)"></div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="oxygen-manage-global-colors-link" ' +
                    'ng-click="toggleSettingsPanel(true);switchTab(\'settings\',\'colors\')">Manage Colors</div>' +
            '</div>';
            
            jQuery('.wp-picker-holder', activeColorPicker).each(function(){
                var picker = jQuery(this);
                // add if global colors is not yet added for this colorpicker
                if (jQuery(".oxygen-global-colors-wrap",picker).length===0) {
                    picker.append($compile(globalColorsHTML)($scope))
                }
            });
        });

        // Global Color label close
        jQuery('body').on('click', '.oxy-global-color-label-remove', function(e) {
            var input = jQuery(this).parents('.oxygen-color-picker').find('input');
            input.val("");
            angular.element(input).triggerHandler('input');
        })


        /**
         * Special property messages
         */

        jQuery("body")
        // Not available for classes
        .on("mouseover", ".oxygen-editing-class:not(.oxygen-editing-media) .not-available-for-classes:not(.oxygen-active-select)", function(e){
            var $this = jQuery(this);
            if ($this.find('.oxygen-active-select').length) {
                return;
            }
            jQuery('#oxy-no-class-msg').css({
                "display": "block",
                "top": $this.offset().top + $this.height(),
            });
        })
        .on("mouseleave", ".oxygen-editing-class:not(.oxygen-editing-media) .not-available-for-classes", function(e){
            jQuery('#oxy-no-class-msg').css({
                "display": "none",
            });
        })
        // not available for media
        .on("mouseover", ".oxygen-editing-media:not(.oxygen-editing-class) .not-available-for-media:not(.oxygen-active-select)", function(e){
            var $this = jQuery(this);
            if ($this.find('.oxygen-active-select').length) {
                return;
            }
            jQuery('#oxy-no-media-msg').css({
                "display": "block",
                "top": $this.offset().top + $this.height(),
            });
        })
        .on("mouseleave", ".oxygen-editing-media:not(.oxygen-editing-class) .not-available-for-media", function(e){
            jQuery('#oxy-no-media-msg').css({
                "display": "none",
            });
        })
        // not available for classes and media
        .on("mouseover", ".oxygen-editing-class.oxygen-editing-media .not-available-for-media.not-available-for-classes:not(.oxygen-active-select)", function(e){
            var $this = jQuery(this);
            if ($this.find('.oxygen-active-select').length) {
                return;
            }
            jQuery('#oxy-no-class-no-media-msg').css({
                "display": "block",
                "top": $this.offset().top + $this.height(),
            });
        })
        .on("mouseleave", ".oxygen-editing-class.oxygen-editing-media .not-available-for-media.not-available-for-classes", function(e){
            jQuery('#oxy-no-class-no-media-msg').css({
                "display": "none",
            });
        })

        .on("mouseover", ".oxygen-editing-class.oxygen-editing-media .not-available-for-media:not(.not-available-for-classes, .oxygen-active-select)", function(e){
            var $this = jQuery(this);
            if ($this.find('.oxygen-active-select').length) {
                return;
            }
            jQuery('#oxy-no-media-msg').css({
                "display": "block",
                "top": $this.offset().top + $this.height(),
            });
        })
        .on("mouseleave", ".oxygen-editing-class.oxygen-editing-media .not-available-for-media:not(.not-available-for-classes)", function(e){
            jQuery('#oxy-no-media-msg').css({
                "display": "none",
            });
        })

        .on("mouseover", ".oxygen-editing-class.oxygen-editing-media :not(.not-available-for-media, .oxygen-active-select).not-available-for-classes", function(e){
            var $this = jQuery(this);
            if ($this.find('.oxygen-active-select').length) {
                return;
            }
            jQuery('#oxy-no-class-msg').css({
                "display": "block",
                "top": $this.offset().top + $this.height(),
            });
        })
        .on("mouseleave", ".oxygen-editing-class.oxygen-editing-media :not(.not-available-for-media).not-available-for-classes", function(e){
            jQuery('#oxy-no-class-msg').css({
                "display": "none",
            });
        })

        /**
         * Selector Choose Input
         */

        $scope.oxygenUIElement
            .on('click', '.oxygen-selector-browse', function(e) {
                jQuery('body').addClass('choosing-selector');
                $scope.iframeScope.enterChoosingSelectorMode( jQuery(e.target).data('option') );
            })

        jQuery('#ct-ui-overlay')
            .on('click' ,function(e) {
                jQuery('body').removeClass('choosing-selector');
                $scope.iframeScope.exitChoosingSelectorMode( jQuery(e.target).data('option') );
            })
        $scope.exitChoosingSelectorMode = function() {
            jQuery('body').removeClass('choosing-selector');
        }
        /**
         * Media upload
         */
        
        /** 
         * In order to make this functionality available for foreground images as well, 
         * this function relies on data- attributes provided in the .oxygen-file-input-browse html element
         * this attributes can be as follows 
         * data-mediaTitle for the title of the media dialog
         * data-mediaButton for the text of the 'insert' button on the media dialog
         * data-mediaProperty for specifying the model's param that will be updated with the url
         * data-heightProperty for updating the height 
         * data-widthProperty for updating the width
         *
         */
        $scope.oxygenUIElement
        .on('click', '.oxygen-file-input-browse', function(e) {
            
            // save the target in scope
            $scope.mediaUploadTarget = jQuery(e.target);
            
            var mediaType = $scope.mediaUploadTarget.attr('data-mediaType');
            var current_image_ids = false;
            
            if (mediaType=='gallery') {
                current_image_ids = $scope.iframeScope.getOption($scope.mediaUploadTarget.attr('data-mediaProperty'));
                // we need re-init gallery to be sure current images loads correctly
                $scope.media_uploader[mediaType] = null;
            }

            if(!$scope.media_uploader[mediaType]) {

                var options = {
                    title:     $scope.mediaUploadTarget.attr('data-mediaTitle') || 'Set Image',
                    button:{
                        text:  $scope.mediaUploadTarget.attr('data-mediaButton') || 'Set Image',
                    },
                    library:{ 
                        type:  $scope.mediaUploadTarget.attr('data-mediaContent') || 'image' 
                    },
                    multiple:  $scope.mediaUploadTarget.attr('data-mediaMultiple') || false,
                }

                if ($scope.mediaUploadTarget.attr('data-mediaMultiple')=='true') {
                    options.state = 'gallery';
                    options.frame = 'post';
                }

                $scope.media_uploader[mediaType] = wp.media(options);

                if (current_image_ids) {
                    $scope.media_uploader[mediaType] = wp.media.gallery.edit('[gallery ids="' + current_image_ids + '"]');
                }

                // gallery
                $scope.media_uploader[mediaType].on("update", function(selection){

                    var ids = [];
                    selection.each( function( image ) {
                        ids.push( image.get( 'id' ) );
                    } );

                    $scope.iframeScope.setOptionModel($scope.mediaUploadTarget.attr('data-mediaProperty'), ids.join(","));
                    $scope.iframeScope.renderComponentWithAJAX('oxy_render_gallery');          
                    $scope.$apply();
                });

                // single
                $scope.media_uploader[mediaType].on("select", function(){

                    var json = $scope.media_uploader[mediaType].state().get("selection").first().toJSON();
                    var returnValue = $scope.mediaUploadTarget.attr('data-returnValue') || 'url';
                    //console.log(json);
                    // update scope and model
                        
                    if($scope.mediaUploadTarget.attr('data-fieldId')) {
                        jQuery('#'+$scope.mediaUploadTarget.attr('data-fieldId')).val(json[returnValue]).trigger('change');
                    }
                    else {
                        $scope.iframeScope.setOptionModel($scope.mediaUploadTarget.attr('data-mediaProperty'), json[returnValue]);
                        if( returnValue == 'id' ) {

                            var id = $scope.iframeScope.component.active.id,
                                options = $scope.iframeScope.component.options[id];

                            $scope.iframeScope.component.options[id].sizes = null;
                            $scope.iframeScope.component.options[id].sizes_requested = null;
                            // For the attachment size dropdown
                            $scope.iframeScope.component.options[id].size_labels = Object.keys( json.sizes );

                            if( typeof options['model']['attachment_size'] === 'undefined' ||
                                options.size_labels.indexOf( options['model']['attachment_size'] ) == -1) {
                                $scope.iframeScope.setOptionModel('attachment_size', 'full');
                            }
                            $scope.iframeScope.setOptionModel('attachment_height', json.sizes[ options['model']['attachment_size'] ].height);
                            $scope.iframeScope.setOptionModel('attachment_width', json.sizes[ options['model']['attachment_size'] ].width);

                            $scope.iframeScope.setOptionModel('attachment_url', json.sizes[ options['model']['attachment_size'] ].url);
                        }
                        if ($scope.mediaUploadTarget.attr('data-mediaProperty')=='video_background') {
                            $scope.iframeScope.rebuildDOM(id);
                        }
                    }

                    if($scope.mediaUploadTarget.attr('data-heightProperty'))
                        $scope.iframeScope.setOptionModel($scope.mediaUploadTarget.attr('data-heightProperty'), json.height);
                    if($scope.mediaUploadTarget.attr('data-widthProperty'))
                        $scope.iframeScope.setOptionModel($scope.mediaUploadTarget.attr('data-widthProperty'), json.width);
                        
                    // set image alt attr
                    $scope.iframeScope.setOptionModel("alt", json.alt);
                    
                    $scope.$apply();

                    var timeout = $timeout(function() {
                        $scope.iframeScope.processImageSizes();
                        $timeout.cancel(timeout);
                    }, 0, false);
                });
            }

            $scope.media_uploader[mediaType].open();
        })

        
        jQuery('body')
        .on('click', '#wp-link-submit', function(e) {
            
            var attrs = wpLink.getAttrs();
            $scope.iframeScope.setOptionModel(jQuery('#ct-link-dialog-txt').attr('data-linkProperty'), attrs.href);
            $scope.iframeScope.setOptionModel(jQuery('#ct-link-dialog-txt').attr('data-linkTarget'), attrs.target);
            
            $scope.$apply();

            if( attrs.href.trim() === '') {
                $scope.removeLink();
            }

            jQuery('body #ct-link-dialog-txt').remove();
            wpLink.close();

        })
        .on('click', '#wp-link-cancel, #wp-link-close, #wp-link-backdrop', function(e) {
            jQuery('body #ct-link-dialog-txt').remove();
            $scope.showLinkDataDialog = false;
            wpLink.close();
            $scope.$apply();
        });

        /**
         * Builder handle move
         */
        var dragging = false;

        // handle move start
        jQuery('#ct-viewport-handle')
            .mousedown(function(e){    
                e.preventDefault();
           
                dragging = true;

                var ghostbar = jQuery('<div>',{id: 'ct-ghost-viewport-handle'}).prependTo('#ct-viewport-ruller-wrap');

                // init ghost position
                var position = e.pageX-$scope.artificialViewport.offset().left-3;    
                ghostbar.css("left", position/$scope.viewportScale);
                
                // adjust ghost position on move
                jQuery(document).mousemove(function(e){
                    position = e.pageX-$scope.artificialViewport.offset().left-3;
                    ghostbar.css("left", position/$scope.viewportScale);
                });
            })
            .dblclick(function(){
                if ($scope.iframeScope.getCurrentMedia()!= "default") {
                    $scope.iframeScope.setCurrentMedia("default");
                }
                else {
                    $scope.artificialViewport.css("width", "");
                    $scope.hideViewportRuller();
                    $scope.adjustArtificialViewport();
                }
            });

        // handle move end
        jQuery(document).mouseup(function(e){
           if (dragging) {
               
                var width = e.pageX-$scope.artificialViewport.offset().left;

                $scope.setMediaByWidth(width/$scope.viewportScale);

                jQuery('#ct-ghost-viewport-handle').remove();
                jQuery(document).unbind('mousemove');
                dragging = false;

                $scope.$apply();
            }
        });


        $scope.setMediaByWidth = function(width) {

            if (undefined==width) {
                width = $scope.viewportContainer[0].scrollWidth;
            }
            
            var mediaName = $scope.iframeScope.getMediaNameBySize(width);

            if (mediaName) {
                $scope.iframeScope.setCurrentMedia(mediaName, false);
            }

            // adjust viewport
            $scope.adjustViewport(width + "px");
            $scope.adjustArtificialViewport();
            $scope.adjustViewportRuller();
        }

        $scope.showViewportRuller = function() {
            $scope.viewportRulerWrap.css("display", "block");
            $scope.viewportRullerShown = true;
        }

        $scope.hideViewportRuller = function() {
            $scope.viewportRulerWrap.css("display", "");
            $scope.viewportRullerShown = false;
        }

        $scope.adjustViewportRuller = function() {

            $scope.viewportRulerWrap.css("width", 0);

            var offset = 0,
                width = ($scope.viewportRullerWidth > $scope.viewportContainer.width()) ? $scope.viewportRullerWidth : $scope.viewportContainer.width() - offset - 1;

            $scope.viewportRulerWrap.css({
                    left : offset,
                    width : width/$scope.viewportScale,
                    transform : "scaleX("+$scope.viewportScale+")",
                });

            //console.log("adjustViewportRuller()", offset, container.width() - offset);
            
            jQuery('#ct-viewport-handle').css("left", $scope.artificialViewport.width()-3);

            $scope.viewportRullerWidth = $scope.artificialViewport.width();
        }


        /**
         * Adjust artificial viewport
         *
         * @since 0.3.2
         */
        
        $scope.adjustViewport = function(size) {

            //console.log("adjustViewport()", size);
        
            $scope.artificialViewport.css("width", size);

            $scope.adjustViewportRuller();
        }


        /**
         * Adjust viewport container
         *
         * @since 0.3.2
         */

        $scope.adjustViewportContainer = function(artificialViewportWidth) {

            if ($scope.iframeScope.log) {
                console.log("adjustViewportContainer()", artificialViewportWidth);
            }

            var sidebarWidth = $scope.verticalSidebar.width();
            
            // DOM Tree opened, Add+ opened
            if ( ($scope.showSidePanel || $scope.showSettingsPanel ) && $scope.isActiveActionTab('componentBrowser') ) {

                if (artificialViewportWidth===undefined) {
                    artificialViewportWidth = window.innerWidth - 300 - sidebarWidth - 12
                }

                $scope.viewportContainer.css({
                    marginLeft: sidebarWidth,
                    width: window.innerWidth - 300 - sidebarWidth,
                })
                
                $scope.adjustArtificialViewport(artificialViewportWidth);
                $scope.adjustViewportRuller();
                
                $scope.sidePanelElement.css({
                    width: "300px"
                });
            }
            else

            // DOM Tree opened, Add+ closed
            if ( ($scope.showSidePanel || $scope.showSettingsPanel ) && !$scope.isActiveActionTab('componentBrowser') ) {

                if (artificialViewportWidth===undefined) {
                    artificialViewportWidth = window.innerWidth - 300 - sidebarWidth - 12
                }
               
                $scope.viewportContainer.css({
                    marginLeft: sidebarWidth,
                    width: window.innerWidth - 300 - sidebarWidth,
                    paddingTop: 0
                });
                
                $scope.adjustArtificialViewport(artificialViewportWidth);
                $scope.adjustViewportRuller();
                
                $scope.sidePanelElement.css({
                    width: "300px"
                });
            }
            else

            // DOM Tree closed, Add+ opened
            if ( ( !$scope.showSidePanel && !$scope.showSettingsPanel ) && $scope.isActiveActionTab('componentBrowser') ) {

                if (artificialViewportWidth===undefined) {
                    artificialViewportWidth = window.innerWidth - sidebarWidth - 12
                }
               
                $scope.viewportContainer.css({
                    marginLeft: sidebarWidth,
                    width: window.innerWidth - sidebarWidth,
                });

                $scope.adjustArtificialViewport(artificialViewportWidth);
                $scope.adjustViewportRuller();
                
                $scope.sidePanelElement.css({
                    width: "0"
                });
            }
            else
            
            // All closed
            {   

                if (artificialViewportWidth===undefined) {
                    artificialViewportWidth = window.innerWidth - sidebarWidth - 12
                }

                $scope.viewportContainer.css({
                    marginLeft: sidebarWidth,
                    width: window.innerWidth - sidebarWidth,
                    paddingTop: 0
                });
                
                $scope.adjustArtificialViewport(artificialViewportWidth);
                $scope.adjustViewportRuller();
                
                $scope.sidePanelElement.css({
                    width: "0"
                });
            }

        }


        /**
         * Adjust artificial viewport
         */

        $scope.adjustArtificialViewport = function(artificialViewportWidth) {

            //console.log(artificialViewportWidth);

            var heightOffset = 53; 

            if ($scope.viewportRullerShown) {
                heightOffset += 19;
            }

            // adjust artificial viewport based on "Page width"
            if (!$scope.viewportRullerShown) {

                var viewportContainerWidth = $scope.viewportContainer.width();
                    pageWidth = $scope.iframeScope.getWidth($scope.iframeScope.getPageWidth());

                if (artificialViewportWidth===undefined) {
                    artificialViewportWidth = $scope.artificialViewport.width();
                }
                
                if ( pageWidth.value > artificialViewportWidth ) {
                    
                    var neededSpace = parseInt($scope.iframeScope.getPageWidth()) + 20;
                     
                    $scope.artificialViewport.css({
                        "width": neededSpace,
                        "min-width": ""
                    });
                    
                    // rescale iframe if not fit
                    if ( !$scope.viewportScaleLocked ) {
                        if ( neededSpace > artificialViewportWidth ) {
                            var scale = artificialViewportWidth / neededSpace;
                            $scope.artificialViewport.css({
                                transform: "scale("+scale+")",
                                height: "calc("+(100/scale)+"vh - "+(heightOffset/scale)+"px)"});
                            $scope.viewportScale = scale;
                        }
                        $scope.viewportContainer.css("overflow-x","");
                    }
                    else {
                        $scope.artificialViewport.css({
                            "transform": "scale(1)",
                            "height": "calc(100vh - "+heightOffset+"px)",
                        });
                        $scope.viewportContainer.css("overflow-x","auto");
                        $scope.viewportScale = 1;
                    }
                }
                else
                if ( pageWidth.value < viewportContainerWidth - 12 ) {
                    $scope.artificialViewport.css({
                        "transform": "scale(1)",
                        "height": "calc(100vh - "+heightOffset+"px)",
                    });
                    $scope.viewportContainer.css("overflow-x","auto");
                    if ( !$scope.viewportRullerShown ) {
                        $scope.artificialViewport.css({
                            "width": "",
                            "min-width": ""
                        });
                    }
                    $scope.viewportScale = 1;
                    //console.log("adjustArtificialViewport()", "")
                }

                // unset builder width
                $scope.builderElement.css("width", "");

            }
            else {
                // unset builder width
                $scope.builderElement.css("width", "");
                //console.log("adjustArtificialViewport()", "")

                var viewportContainertWidth = $scope.viewportContainer.width(),
                    artificialViewportWidth = $scope.artificialViewport.width() + 20,
                    scale = viewportContainertWidth / artificialViewportWidth;
                                
                // rescale iframe if not fit
                if ( artificialViewportWidth > viewportContainertWidth ) {
                    
                    if ( !$scope.viewportScaleLocked ) {
                         $scope.artificialViewport.css({
                            transform: "scale("+scale+")",
                            height: "calc("+(100/scale)+"vh - "+(heightOffset/scale)+"px)"
                        });
                        $scope.viewportContainer.css("overflow-x","");
                        $scope.viewportScale = scale;
                    }
                    else {
                        $scope.artificialViewport.css({
                            transform: "scale(1)",
                            height: "calc(100vh - "+heightOffset+"px)",
                            marginBottom: 23
                        });
                        $scope.viewportContainer.css("overflow-x","auto");
                        $scope.viewportScale = 1;
                    }
                }
                else {
                    $scope.artificialViewport.css({
                        transform: "scale(1)",
                        height: "calc(100vh - "+heightOffset+"px)",
                        marginBottom: 23
                    });
                    $scope.viewportContainer.css("overflow-x","auto");
                    $scope.viewportScale = 1;
                }
            }

            $scope.iframeScope.adjustResizeBox();
            
            // safely apply scope
            var timeout = $timeout(function() {
                $scope.$apply();
                $timeout.cancel(timeout);
            }, 0, false);

        }

        /**
         * Lock/unlock viewport scale qo 100%
         *
         * @since 2.0
         * @author Ilya K.
         */

        $scope.lockViewportScale = function() {

          $scope.viewportScaleLocked = !$scope.viewportScaleLocked;
          $scope.adjustViewportContainer();
          $scope.viewportContainer.scrollLeft(0);
        }

        /**
         * Hide left sidebar
         * 
         * @since 3.3
         * @author Abdelouahed E.
         */
        $scope.doHideLeftSidebar = function(adjustViewport) {
            $scope.showLeftSidebar = false;
            $scope.verticalSidebar.addClass('ui-collapse');
            
            if (adjustViewport) {
                $scope.adjustViewportContainer();
            }
        }

        /**
         * Show left sidebar
         * 
         * @since 3.3
         * @author Abdelouahed E.
         */
        $scope.doShowLeftSidebar = function(adjustViewport) {
            $scope.showLeftSidebar = true;
            $scope.showButtonFlashing = false;
            $scope.verticalSidebar.removeClass('ui-collapse');
            
            if (adjustViewport) {
                $scope.adjustViewportContainer();
            }
        }

        /**
         * Measureboxes
         */
        
        $scope.oxygenUIElement
        .on("click", ".oxygen-measure-box-unit-selector", function(e) {
            // hide all boxes
            jQuery(".oxygen-measure-box", $scope.oxygenUIElement)
                .removeClass("oxygen-measure-box-unit-selector-active")
            // show the box
            jQuery(this).closest(".oxygen-measure-box", $scope.oxygenUIElement).addClass("oxygen-measure-box-unit-selector-active");
            measureboxOutsideClick();
        })
        .on("click", ".oxygen-measure-box-unit", function(e) {
            // hide the box
            jQuery(this).closest(".oxygen-measure-box", $scope.oxygenUIElement).removeClass("oxygen-measure-box-unit-selector-active");
        })
        .on("click", "div:not(.oxygen-measure-box-options)>.oxygen-measure-box>input", function(e) {
            // hide all boxes
            jQuery(".oxygen-measure-box", $scope.oxygenUIElement)
                .removeClass("oxygen-measure-box-unit-selector-active");
            // show one box
            jQuery(this).closest(".oxygen-measure-box", $scope.oxygenUIElement)
                .find('input').focus();
            measureboxOutsideClick();
        })
        .on("focus", ".oxygen-measure-box>input", function(e) {
            // select all text
            this.setSelectionRange(0, this.value.length)
            jQuery(".oxygen-measure-box>input")
                .removeClass("oxygen-measure-box-focused-input");
            jQuery(this)
                .addClass("oxygen-measure-box-focused-input")
                .parents(".oxygen-four-sides-measure-box").addClass("oxygen-measure-box-focused");
        })
        .on("focusout", ".oxygen-measure-box>input", function(e) {
            if ($scope.applyAllInProgress !== true) {
                jQuery(".oxygen-measure-box-focused").removeClass("oxygen-measure-box-focused");                
            }
        })
        // make box not closed when ('html').click triggered 
        .on("click", ".oxygen-measure-box", function(e){
            e.stopPropagation();
        })
        .on("click", ".oxygen-measure-box-units", function(e){
            e.stopPropagation();
        });

        function measureboxOutsideClick() {
            // close the box if user click outside it
            jQuery('html').click(function(clickEvent) {
                // hide all boxes
                jQuery(".oxygen-measure-box", $scope.oxygenUIElement)
                    .removeClass("oxygen-measure-box-unit-selector-active")
                    .removeClass("oxygen-measure-box-active");

                // unbid it immideately
                jQuery(this).unbind(clickEvent);
            });
        }


        /**
         * Apply all/opposite options
         */
        
        // mark/unmark measurebox as 'apply all'
        $scope.oxygenUIElement
        .on("mousedown", ".oxygen-apply-all-trigger", function(e) {
            $scope.applyAllInProgress = true;
        })
        .on("mouseup", ".oxygen-apply-all-trigger", function(e) {
            $scope.applyAllInProgress = false;
            applyAll(this);
            // reselect value
            jQuery(".oxygen-measure-box-focused-input").focus();
            // make it show again
            jQuery(this).parents(".oxygen-four-sides-measure-box").addClass("oxygen-measure-box-focused");
        })

        function applyAll(element, value, unit) {

            if ($scope.iframeScope.log) {
                console.log("applyAll()");
                $scope.iframeScope.functionStart("applyAll()");
            }

            var sizeBox     = jQuery(element).parents(".oxygen-four-sides-measure-box"),
                option      = jQuery(".oxygen-measure-box-focused-input").data("option");

            // get values from $scope if not defined
            if (undefined === value) {
                value = $scope.iframeScope.getOption(option);
            }
            if (undefined === unit) {
                unit = $scope.iframeScope.getOptionUnit(option);
            }

            // loop all size box values to apply currently editing value
            jQuery(".oxygen-measure-box>input", sizeBox).each(function(){

                var option          = jQuery(this).data("option"),
                    currentValue    = $scope.iframeScope.getOption(option),
                    currentUnit     = $scope.iframeScope.getOptionUnit(option);

                if (currentValue != value) {
                    $scope.iframeScope.setOptionModel(option, value, $scope.iframeScope.component.active.id, $scope.iframeScope.component.active.name, true);
                }

                if (currentUnit != unit) {
                    $scope.iframeScope.setOptionUnit(option, unit, true);
                }
            })

            // safely apply scope
            var timeout = $timeout(function() {
                $scope.$apply();
                $timeout.cancel(timeout);
            }, 0, false);

            // update styles
            $scope.iframeScope.outputCSSOptions($scope.iframeScope.component.active.id);

            $scope.iframeScope.functionEnd("applyAll()");
        }
        

        /**
         * Selects
         */
        
        $scope.oxygenUIElement
        .on("click", ".ct-select:not(.ct-ui-disabled,.ct-custom-selector)", function(e) {

            if ( jQuery(this).parents('.ct-style-set-dropdown') ) {
                jQuery(".ct-new-component-class-input",this).focus();
            }

            e.stopPropagation();
        })

        .on("click", ".oxygen-select", function(e) {
            $scope.toggleOxygenSelectBox(e, this);
        })

        // don't hide the box on input click
        .on("click", ".oxygen-select-box-option input", function(e) {
            e.stopPropagation();
        })

        // media icon click
        .on("click", ".oxygen-media-query-box, .oxygen-active-selector-box-state, .oxygen-active-selector-box, .oxygen-copy-to-trigger", function(e) {

            var select = jQuery(this).closest('.oxygen-select'),
                isActive = select.hasClass("oxygen-active-select"),
                isActiveStates = select.hasClass("oxygen-active-states-select"),
                isActiveClasses = select.hasClass("oxygen-active-classes-select");

            $scope.closeOxygenDropdowns();

            // show certain dropdown
            if (!isActive) {
                select.addClass("oxygen-active-select");
                selectOutsideClick();
            }

            if (!isActiveStates && jQuery(this).hasClass('oxygen-active-selector-box-state')) {
                select.addClass("oxygen-active-states-select");
                selectOutsideClick();
            }

            if (!isActiveClasses && jQuery(this).hasClass('oxygen-active-selector-box')) {
                select.addClass("oxygen-active-classes-select");
                selectOutsideClick();
            }

            if (jQuery(this).hasClass('oxygen-copy-to-trigger')) {
                var select = jQuery(this).siblings('.oxygen-copy-to-dropdown');
                select.addClass("oxygen-active-copy-to-select");
                selectOutsideClick();
            }

            e.stopPropagation();
        })


        // media item click
        .on("click", ".oxygen-media-query-dropdown li, .oxygen-copy-to-dropdown li", function(e) {
            $scope.closeOxygenDropdowns();
            e.stopPropagation();
        })

        function selectOutsideClick() {
            // close the box if user click outside it
            jQuery('html').click(function(clickEvent) {
                // close
                jQuery(".ct-select", $scope.oxygenUIElement).removeClass("ct-active").removeClass("ct-active-media").removeClass("ct-active-states");

                $scope.copySelectorFromID = false;
                $scope.copySelectorFromClass = false;

                // unbid it immideately
                jQuery(this).unbind(clickEvent);
            });

            // close the box if user click outside it
            jQuery('html').click(function(clickEvent) {
                $scope.closeOxygenDropdowns();
                // unbid it immideately
                jQuery(this).unbind(clickEvent);
            });
        }

        /**
         * Helper to close media queries/classes/copy-to dropdowns
         */

        $scope.closeOxygenDropdowns = function() {
          jQuery(".oxygen-select", $scope.oxygenUIElement)
                    .removeClass("oxygen-active-select")
                    .removeClass("oxygen-active-copy-to-select")
                    .removeClass("oxygen-active-classes-select")
                    .removeClass("oxygen-active-states-select");
        }

        /**
         * .on('click') callback for Select box wrapped in a function to use in templates
         */

        $scope.toggleOxygenSelectBox = function(e, $this) {

            if ($this == undefined) {
                $this = e.currentTarget;
            }

            // if the click was inside a text input for new classname, do not hide the select dropdown
            if(jQuery(e.target).hasClass('oxygen-classes-dropdown-input')) {
                e.stopPropagation();
                return;
            }

            var isActive = jQuery($this).hasClass("oxygen-active-select");

            $scope.closeOxygenDropdowns();

            // show dropdown
            if (!isActive) {
                jQuery($this).addClass("oxygen-active-select");
                jQuery(".oxygen-overlay-property-msg").hide();
                selectOutsideClick();
            }

            // focus on search
            jQuery(".oxygen-select-box-option input",$this).focus();
            
            e.stopPropagation();
        }

        
        /**
         * Increase descrease measure values with top/bottom key press
         */
        
        $scope.oxygenUIElement
        .on("keydown", ".oxygen-measure-box>input", function(e) {
            
            // increase 
            if (e.keyCode==38) {

                if (this.value == parseFloat(this.value, 10)){
                    this.value++;
                    var input = jQuery(this);
                    input.trigger("change").trigger("input");
                }
            };
            
            // decrease
            if (e.keyCode==40) {

                if (this.value == parseFloat(this.value, 10)){
                    this.value--;
                    var input = jQuery(this);
                    input.trigger("change").trigger("input");
                }
            }
        });


        // Returns a function, that, as long as it continues to be invoked, will not
        // be triggered. The function will be called after it stops being called for
        // N milliseconds. If `immediate` is passed, trigger the function on the
        // leading edge, instead of the trailing.
        function debounce(func, wait, immediate) {
            var timeout;
            return function() {
                var context = this, args = arguments;
                var later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        };


        /**
         * Open/close DOM tree node options
         */
        
        // toggle on icon click
        $scope.oxygenUIElement
        .on("mousedown", ".ct-more-options-icon", function(e) {

            var isExpanted = jQuery(this).parent().hasClass("ct-more-options-expanded");
                $scope.optionsToOpen = jQuery(this);

            // close all options
            jQuery(".ct-more-options-expanded", $scope.sidePanelElement).removeClass("ct-more-options-expanded");
            
            // open this option
            if ( !isExpanted ) {
                var timeout = $timeout(function(_self) {

                    $scope.optionsToOpen.parent().addClass("ct-more-options-expanded");
                    // cancel timeout
                    $timeout.cancel(timeout);
                }, 100, false);
            }
        })

        .on("click", ":not(.ct-more-options-icon)", function(e) {
            // close all options
            jQuery(".ct-more-options-expanded", $scope.sidePanelElement).removeClass("ct-more-options-expanded");
        })

        // fix for templates dropdown click in content edit mode
        .on("mousedown", ".oxygen-template-previewing-control", function(e) {
            $scope.disableContentEdit();
        })

        $scope.builderElement
        .on("click", '.ct-active:not([contenteditable="true"])', function(e) {
            $scope.disableContentEdit();
        })
        .on("click", '[contenteditable="true"]', function(e) {
            e.stopPropagation();
        })
        
        // This is not working as ng-click triggered first. TODO: find a fix
        // close on item click
        //.on("click", ".ct-more-options-expanded li", function(e) {
        //    jQuery(this).closest('.ct-node-options').removeClass("ct-more-options-expanded");
        //})

        // window resize
        jQuery(window).resize(function() {
            $scope.adjustViewportContainer();
        });

        jQuery(window).on('click', function(e) {

            if(jQuery(e.target).closest('.ct-active').length < 1 && 
                jQuery(e.target).closest('.oxygen-formatting-toolbar').length < 1 && 
                jQuery(e.target).closest('#ctdynamicdata-popup').length < 1)
                { 
                    $scope.disableContentEdit();
                }

            /*var clickedComponent = parseInt(jQuery(e.target).closest('.ct-component').attr('ng-attr-component-id'));
           
            $scope.iframeScope.component.active.id = clickedComponent;*/

            
            /*if(clickedComponent === 0) {
                $scope.activateComponent(0, 'root');
            } else if(clickedComponent > 100000) {
                $scope.activateComponent(clickedComponent, 'ct_inner_content');
            }*/
            
           // console.log($scope.iframeScope.component.active.id, parseInt(jQuery(e.target).closest('.ct-component').attr('ng-attr-component-id')));
            
        });


        /**
         * Show/hide Reusable button options
         */

        $scope.oxygenUIElement.on("click", ".oxygen-add-section-element", function(e) {
            jQuery(this).siblings().removeClass('oxygen-add-section-element-active');
            jQuery(this).toggleClass('oxygen-add-section-element-active');
        });

        $scope.initTooltips = function() {

            // media queries
            var timeout = $timeout(function() {
    
                tippy('[data-tippy-template]', {
                    content: function(reference) {
                        var id = reference.getAttribute('data-tippy-template');
                        var template = document.getElementById(id);
                        return template.innerHTML;
                    },
                    allowHTML: true,
                    animation: 'shift-toward',
                    interactive: true,
                });
    
                $timeout.cancel(timeout);
            }, 0, false);

            tippy('.oxygen-hide-sidebar-button', {
            content: 'Hide Sidebar',
            animation: 'shift-toward',
            placement: 'right',
            });
        
            tippy('.oxygen-show-sidebar-button', {
            content: 'Show Sidebar',
            animation: 'shift-toward',
            placement: 'right',
            });
        
            tippy('.oxygen-add-button', {
            content: 'Add Element',
            animation: 'shift-toward',
            });
        
            tippy('.oxygen-undo-button', {
            content: 'Undo',
            animation: 'shift-toward',
            });
        
            tippy('.oxygen-redo-button', {
            content: 'Redo',
            animation: 'shift-toward',
            });
        
            tippy('.oxygen-dom-tree-button', {
            content: 'Structure',
            animation: 'shift-toward',
            });
        
            tippy('.oxygen-history-button', {
            content: 'History',
            animation: 'shift-toward',
            });
        
            tippy('.oxygen-settings-button', {
            content: 'Settings',
            animation: 'shift-toward',
            });
        
            tippy('.oxygen-stylesheets-button', {
            content: 'Stylesheets',
            animation: 'shift-toward',
            });
        
            tippy('.oxygen-selectors-button', {
            content: 'Selectors',
            animation: 'shift-toward',
            });
        
            tippy('.oxygen-back-to-wp-menu', {
            content: 'Exit to...',
            animation: 'shift-toward',
            });
        
            tippy('.oxygen-save-button', {
            content: 'Save Changes',
            animation: 'shift-toward',
            });
        
            tippy('.oxygen-zoom-icon', {
            content: 'Lock Zoom',
            animation: 'shift-toward',
            });
        }

        $scope.oxygenUIElement.on("click", ".oxy-media-query-tooltip-clear-action", function(e) {
            var mediaName = jQuery(this).data('media-name');
            $scope.iframeScope.removeComponentMedia(mediaName); 
            e.stopPropagation()
        });

        $scope.initTooltips();
        
    }

	/**
	 * Updates dynamically the contents of the Custom Fields dropdown
	 *
	 * @since 2.0
	 */

    $scope.updateMetaDropdown = function( ) {
        var keys = $scope.current_post_meta_keys;
        var keysList = "";
        for( var i = 0; i < keys.length; i++ ) keysList += "<li ng-click=\"iframeScope.setOptionModel('meta_key','" + keys[ i ] + "');\">" + keys[ i ] + "</li>";
        jQuery( '.ct-ct_data_custom_field-meta_key' ).find('ul.ct-dropdown-list').html( $compile( keysList )($scope) );
    };


    /**
     * Init Select2 after $scope being loaded
     *
     * @since 2.0
     */

    $scope.initSelect2 = function(selector, placeholder, selectorType) {

        if( selectorType == undefined ) {
            var select2 = jQuery("#"+selector).select2({
                placeholder: placeholder
            });

            var timeout = $timeout(function(_self) {
                select2.trigger('change');
                $timeout.cancel(timeout);
            }, 0, false);
        } 
        else if( selectorType == 'class' ) {
            jQuery("."+selector).select2({
                placeholder: placeholder
            })
            .on("select2:open", function (e) { 
                document.querySelector('.select2-search__field').focus();

                var selected = jQuery(this).find(':selected');
                if (selected[0] !== undefined && selected[0].value[0] == "?") {
                    jQuery('.select2-results__options').addClass('oxygen-select2-default-option-fix')
                }
                else {
                    jQuery('.select2-results__options').removeClass('oxygen-select2-default-option-fix')
                }
            });
        }
    }

    $scope.advancedQuerySelect2Change = function(newKey, oldKey, param) {
        
        param.values = (oldKey !== newKey ) ? [] : param.values; 
        param.key=newKey; 
        
        // Taken from ng-click="" in advanced-query.php, seems to work fine without it. Didn't find where "temp" variable being used
        // temp = ((key.indexOf('__') === -1 && array_query_params.indexOf(param.key) === -1) && param.values.length > 1) 
        //     ? param.values.splice(1, param.values.length-1) 
        //     : null ; 

        $scope.iframeScope.setOption($scope.iframeScope.component.active.id, $scope.iframeScope.component.active.name, 'wp_query_advanced')
    }

    // these are dynamic conditions specific

    $scope.assignOxyCodeToCondition = function(data) {
        $scope.conditionsDialogOptions.userCondition = data;

        if($scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['globalconditions'][$scope.conditionsDialogOptions.selectedIndex]['name']!=='ZZOXYVSBDYNAMIC') {
          $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['globalconditions'][$scope.conditionsDialogOptions.selectedIndex]['value'] = '';
          $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['globalconditions'][$scope.conditionsDialogOptions.selectedIndex]['operator'] = 0;
        }

        $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['globalconditions'][$scope.conditionsDialogOptions.selectedIndex]['name']='ZZOXYVSBDYNAMIC'; 
        $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['globalconditions'][$scope.conditionsDialogOptions.selectedIndex]['oxycode']=$scope.conditionsDialogOptions.userCondition;
        
        $scope.dialogForms['showAddGlobalConditionName'] = null;

        
        $scope.iframeScope.setOptionModel('globalconditions', $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['globalconditions']); 
        $scope.evalGlobalConditions();
    }

    $scope.assignOxyCodeToConditionValue = function(data) {
        
        $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['globalconditions'][$scope.conditionsDialogOptions.selectedIndex]['value']=data;
        
        $scope.iframeScope.setOptionModel('globalconditions', $scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['globalconditions']); 
        $scope.evalGlobalConditions();
    }

    $scope.hasGlobalConditionsUserText = function() {
        return $scope.iframeScope.globalConditions[$scope.iframeScope.component.options[$scope.iframeScope.component.active.id]['model']['globalconditions'][$scope.conditionsDialogOptions.selectedIndex]['name']]['values'].find(function(item) { return item == 'USERTEXT' });
    }

    $scope.getConditionsResult = function(callback, conditions, id) {

        if(typeof(id) === 'undefined') {
          id = $scope.iframeScope.component.active.id;
        }

        if(typeof(conditions) === 'undefined' || conditions === null) {
            conditions = $scope.iframeScope.component.options[id]['model']['globalconditions'];
        }

        var results = {};
        var result = true;
        var evaledLocaly = false;
        var needsToBeEvaled = true

        conditions.forEach( function(condition, index) {
            
            //if(condition['preview'] === true) {
                //needsToBeEvaled = true;
                results[index] = condition;
            // }
            // else {
            //     evaledLocaly = true;
            //     results[index] = true;
            //     result = result && results[index];
            // }
        })

        var saneConditions = conditions.filter(function(item) { return item.name != '' && item.operator !== null && item.value !== null;})

        if(saneConditions.length > 0 && needsToBeEvaled) { 
            $scope.iframeScope.evalConditionsViaAjax({conditions: results, type: $scope.iframeScope.component.options[id]['model']['conditionstype']}, function(data) {

                if(typeof(data['value']) !== 'undefined') {
                    result = data['value'];
                }
                else {
                    result = null;
                }

                if(callback) {
                    callback(result);
                }

            });
        } else { // if all conditions were being previewed, then the calculation is already stored in the variable result
             
            if(evaledLocaly) {
                if(callback) {
                    callback(result);
                }
            }
            else {
                if(callback) {
                    callback(null);
                }
                
            }
        }

    }

    $scope.evalGlobalConditionsInList = function() {

        id = $scope.iframeScope.component.active.id;

        if(!id) {
          return;
        }

        var activeComponent = $scope.iframeScope.getComponentById(id);

        var oxyList = activeComponent.closest('.oxy-dynamic-list');

        if(oxyList.length > 0) {
          $scope.iframeScope.updateRepeaterQuery(parseInt(oxyList.attr('ng-attr-component-id')));
        }
    }

    $scope.evalGlobalConditions = function(id) {

        if(typeof(id) === 'undefined') {
          id = $scope.iframeScope.component.active.id;
        }

        if(!id) {
          return;
        }

        var activeComponent = $scope.iframeScope.getComponentById(id);

        var oxyDynamicList;

        if(activeComponent) {
          oxyDynamicList = activeComponent.closest('.oxy-dynamic-list');

          if(oxyDynamicList.length > 0) {
              var conditionspreview = parseInt($scope.iframeScope.component.options[id]['model']['conditionspreview']);

              if(conditionspreview === 2) {
                  $scope.iframeScope.component.options[id]['model']['globalConditionsResult'] = true;
              } 
              else if (conditionspreview === 0 && activeComponent.closest('.oxy_repeater_original').length < 1) {
                  $scope.iframeScope.component.options[id]['model']['globalConditionsResult'] = false;
              }

              return;
          }
        

          if($scope.iframeScope.component.options[id]['model'] && $scope.iframeScope.component.options[id]['model']['conditionspreview']) {

            var conditionspreview = parseInt($scope.iframeScope.component.options[id]['model']['conditionspreview']);

            if(conditionspreview === 2) {
                $scope.iframeScope.component.options[id]['model']['globalConditionsResult'] = true;
                $scope.iframeScope.setOptionModel('globalConditionsResult', true, id);
            } 
            else if (conditionspreview === 0) {
                $scope.iframeScope.component.options[id]['model']['globalConditionsResult'] = false;
                $scope.iframeScope.setOptionModel('globalConditionsResult', false, id);
            }
            else if($scope.iframeScope.component.options[id]['model']['globalconditions']) {
              $scope.getConditionsResult(function(result) {
                  $scope.iframeScope.component.options[id]['model']['globalConditionsResult'] = result;
                  $scope.iframeScope.setOptionModel('globalConditionsResult', result, id);
              }, null, id);
            }
          }
        }
        
    }

    $scope.updateCodeMirrorTheme = function() {

        var newTheme = ( $scope.iframeScope.globalCodeMirrorTheme != 'default' ) ? $scope.iframeScope.globalCodeMirrorTheme : "materialDark"
        
        if (window.currentCMEditor) {
            window.currentCMEditor.dispatch({
                effects: window.currentCMTheme.reconfigure(OxyCM.modules[newTheme])
            })
        }
        var mixedEditors = ['css','js','php']
        for (var key in mixedEditors) {
            if (mixedEditors.hasOwnProperty(key) && window.mixedCMEditors && window.mixedCMEditors[mixedEditors[key]]) {
                window.mixedCMEditors[mixedEditors[key]].dispatch({
                    effects: window.currentCMTheme.reconfigure(OxyCM.modules[newTheme])
                })
            }
        }
        
    }

    $scope.updateCodeMirrorWrap = function() {

        var wrap = $scope.iframeScope.globalCodeMirrorWrap === 'true';

        if (window.currentCMEditor) {    
            window.currentCMEditor.dispatch({
                effects: window.currentCMWrap.reconfigure(wrap ? [OxyCM.EditorView.lineWrapping, OxyCM.modules.search()] : OxyCM.modules.search())
            })
        }
        var mixedEditors = ['css','js','php']
        for (var key in mixedEditors) {
            if (mixedEditors.hasOwnProperty(key) && window.mixedCMEditors && window.mixedCMEditors[mixedEditors[key]]) {
                window.mixedCMEditors[mixedEditors[key]].dispatch({
                    effects: window.currentCMWrap.reconfigure(wrap ? [OxyCM.EditorView.lineWrapping, OxyCM.modules.search()] : OxyCM.modules.search())
                })
            }
        }
    }


    /**
     * Element Export/Import UI functions.
     *
     * @since 4.1
     */

    $scope.showExportModal = function(id) {
        
        var component               = $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, id, $scope.iframeScope.getComponentItem);
        var allClasses              = $scope.iframeScope.getAllElementsClasses(component);
        var allClassesWithStyles    = $scope.iframeScope.getClassesWithStyles(allClasses); 

        var exportObj = {
            "component": component,
            "classes": allClassesWithStyles,
        }
        
        var json = JSON.stringify(exportObj);
        
        json = $scope.iframeScope.replaceGlobalColors(json);

        // remove Angular internal variable to prevent "dupes" error when import
        json = json.replace(new RegExp(/,"\$\$hashKey":"object:\d*"/, 'g'), "");

        $scope.elementExportJSON = json;
        $scope.showExportDialog = true;
    }

    $scope.copyElementExportJSON = function(id) {

        var component               = $scope.iframeScope.findComponentItem($scope.iframeScope.componentsTree.children, id, $scope.iframeScope.getComponentItem);
        var allClasses              = $scope.iframeScope.getAllElementsClasses(component);
        var allClassesWithStyles    = $scope.iframeScope.getClassesWithStyles(allClasses);

        var exportObj = {
            "component": component,
            "classes": allClassesWithStyles,
        }
        
        var json = JSON.stringify(exportObj);
        
        json = $scope.iframeScope.replaceGlobalColors(json);

        // remove Angular internal variable to prevent "dupes" error when import
        json = json.replace(new RegExp(/,"\$\$hashKey":"object:\d*"/, 'g'), "");

        $scope.elementExportJSON = json;

        $scope.iframeScope.copyToClipboard($scope.elementExportJSON);

        $scope.iframeScope.showNoticeModal("<div>Element JSON copied to clipboard</div>", "ct-notice");
    }

    $scope.showImportModal = function() {
        $scope.showImportDialog = true;
    }

    $scope.processElementImportJSON = function() {
        $scope.iframeScope.addComponentFromSource($scope.elementImportJSON);
        $scope.showImportDialog = false;
        $scope.elementImportJSON = "";
    }

    /**
     *.Exact copy of stripFormatting from controllser.main.js
     * Was not able to make it universal due to window, document, jQuery dependencies
     *
     * @since 3.9
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
        .on('paste', '[contenteditable]:not(.cm-content)', stripFormatting);

});


/**
 * Animation
 *
 * @since 0.2.2
 */


/**
 * Animate DOM Tree Details
 * 
 */
animateDOMTreeNodeDetails = function($window) {

    return {

        addClass: function(element, className, doneFn) {

            if (className!="ct-dom-tree-node-active") {
                doneFn();
                return false;
            }

            var details = jQuery(".ct-dom-tree-node-details", element);

            details.hide();
            details.stop().slideDown({
                duration: 250,
                easing: "linear",
                complete: function(){
                    doneFn();
                }
            });
        },

        removeClass: function(element, className, doneFn) {

            if (className!="ct-dom-tree-node-active") {
                doneFn();
                return false;
            }

            var details = jQuery(".ct-dom-tree-node-details", element);

            details.stop().slideUp({
                duration: 250,
                easing: "linear",
                complete: function(){
                    doneFn();
                }
            });
        },
    }
}

CTFrontendBuilderUI.animation('.ct-dom-tree-node-anchor', animateDOMTreeNodeDetails);


/**
 * Animate DOM Tree Node
 * 
 */
animateDOMTreeNode = function($window) {

    return {

        addClass: function(element, className, doneFn) {

            console.log("add",className);

            if (className!="ct-dom-tree-node-expanded") {
                doneFn();
                return false;
            }

            var details = jQuery(element).children(".ct-dom-tree-node");

            details.hide();
            details.stop().slideDown(250, function(){
                doneFn();
            });
        },
        removeClass: function(element, className, doneFn) {

            console.log("remove",className);

            if (className!="ct-dom-tree-node-expanded") {
                doneFn();
                return false;
            }

            var details = jQuery(element).children(".ct-dom-tree-node");

            details.stop().slideUp(250, function(){
                doneFn();
            });
        },
    }
}

CTFrontendBuilderUI.animation('.ct-dom-tree-node', animateDOMTreeNode);


/**
 * Animate DOM Tree Details
 * 
 */

animateStyleSetNode = function($window) {

    return {

        addClass: function(element, className, doneFn) {

            if (className!="ct-style-set-expanded") {
                doneFn();
                return false;
            }

            var details = jQuery(element).nextAll(".ct-style-set-child-selector");
            
            details.hide();
            details.stop().slideDown(250, function(){
                details.css("height","");
                doneFn();
            });
        },
        removeClass: function(element, className, doneFn) {

            if (className!="ct-style-set-expanded") {
                doneFn();
                return false;
            }

            var details = jQuery(element).nextAll(".ct-style-set-child-selector");
            
            details.stop().slideUp(250, function(){
                doneFn();
            });
        },
    }
}

// CTFrontendBuilderUI.animation('.ct-style-set-node', animateStyleSetNode);
// CTFrontendBuilderUI.animation('.ct-css-node-header', animateStyleSetNode);


/**
 * Disable ng-animate for elements with "ct-no-animate" class
 *
 * @since 0.2.2
 */

CTFrontendBuilderUI.config(['$animateProvider', function($animateProvider){
  // disable animation for elements with the ct-no-animate css class with a regexp.
  // note: "ct-*" is our css namespace
  $animateProvider.classNameFilter(/^((?!(ct-no-animate)).)*$/);
}]);


/**
 * Used to set componentize screenshots
 *
 */

CTFrontendBuilderUI.directive('fileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;
            
            element.bind('change', function(){
                scope.$apply(function(){
                    modelSetter(scope, element[0].files[0]);
                });
            });
        }
    };
}]);


CTFrontendBuilderUI.directive('oxygenResizableSidebar', function($timeout,$interval) {

        return {
            restrict: 'AE',
            link: function(scope, element, attr) {

                var style = window.getComputedStyle(element[0], null),
                    dir = ['right'],
                    w,
                    vx = 1, // if centered double velocity
                    vy = 1, // if centered double velocity
                    inner = '<span></span>',
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
                    var newValue, prop;

                    switch(dragDir) {
                    
                        case 'right':
                            prop = 'width';
                            newValue = w - (offset * vx);
                            var button = jQuery('.oxygen-code-editor-expand', scope.verticalSidebar);
                            if (newValue<300) { 
                              // collapse
                              scope.verticalSidebar.data("expanded", false);
                              jQuery(button).text(jQuery(button).attr('data-expand'));
                              break;
                            }
                            else {
                              // expand
                              scope.verticalSidebar.data("expanded", true);
                              jQuery(button).text(jQuery(button).attr('data-collapse'));
                            }
                            console.log(offset, w);
                            element[0].style[prop] = w - (offset * vx) + 'px';
                            break;
                    }
                };
                var dragEnd = function(e) {

                    scope.adjustViewportContainer();
                    jQuery("#resize-overlay").hide();

                    document.removeEventListener('mouseup', dragEnd, false);
                    document.removeEventListener('mousemove', dragging, false);
                    document.removeEventListener('touchend', dragEnd, false);
                    document.removeEventListener('touchmove', dragging, false);
                    element.removeClass('rg-no-transition');
                };
                var dragStart = function(e, direction) {
                    
                    jQuery("#resize-overlay").show();

                    dragDir = direction;
                    axis = ( dragDir.indexOf('left') >= 0 || dragDir.indexOf('right') >= 0 ) ? 'x' : 'y';
                    start = axis === 'x' ? getClientX(e) : getClientY(e);
                    w = parseInt(style.getPropertyValue('width'));

                    //prevent transition while dragging
                    element.addClass('rg-no-transition');

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

                    // add class for styling purposes
                    grabber.setAttribute('class', 'rg-' + direction);
                    grabber.innerHTML = inner;
                    element[0].appendChild(grabber);
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


/**
 * Attach actions to content editor buttons
 *
 */

CTFrontendBuilderUI.directive('ctEditButton', function($timeout,$interval) {

    return {
        link:function(scope,element,attrs) {

            element.bind('mousedown', function(event) {

                event.preventDefault();
                
                var role = attrs.ngEditRole;
                
                switch(role) {
                    case 'link':
                        var sLnk=prompt('Write the URL','http:\/\/');
                        if(sLnk&&sLnk!=''){
                            scope.artificialViewport[0].contentWindow.document.execCommand('createlink', false, sLnk);
                        }
                    case 'p':
                        scope.artificialViewport[0].contentWindow.document.execCommand('formatBlock', false, role);
                        break;
                    default:
                        scope.artificialViewport[0].contentWindow.document.execCommand(role, false, null);
                        break;
                }
                scope.$apply();
                // timeout for angular
                var timeout = $timeout(function() {
                    scope.iframeScope.setOption(scope.iframeScope.component.active.id, scope.iframeScope.component.active.name, 'ct_content');
                    $timeout.cancel(timeout);
                }, 0, false);
            })
        }
    }
})