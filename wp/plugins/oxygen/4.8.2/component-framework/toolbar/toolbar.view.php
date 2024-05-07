<div id="resize-overlay"></div>
<div id="ct-page-overlay" class="ct-page-overlay"><i class="fa fa-cog fa-4x fa-spin"></i></div><!-- #ct-page-overlay -->

<div id="oxy-page-loader"
	ng-hide="pageLoaded">
	<div id="oxy-page-loader-wrap">
		<div id="oxy-page-loading-animation">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/images/loader.gif" alt="">
		</div>
		<div id="oxy-page-loading-text-wrap">
			<div id="oxy-page-loading-text">
				Loading Oxygen
			</div>
			<span>...</span>
		</div>
		<div id="oxy-page-loading-bar">
			<div id="oxy-page-loading-bar-progress">
			</div>
		</div>
	</div>
</div>

<script>
	var oxyLoadingTexts = [
		"Loading Oxygen",
		"Destroying divception",
		"Maximizing performance",
		"Eliminating bloat",
		"Designing a masterpiece",
		"Flexing on the competition",
		"Warming up the engines",
		"Preparing for launch",
		"Reaching 1.21 gigawatts",
		"Tweaking typography"
	]

	var oxyLoadingTextElement = jQuery("#oxy-page-loading-text"),
		oxyLoadingTextElipsis = jQuery("#oxy-page-loading-text-wrap img"),
		oxyLoadingTextElipsisSrc = oxyLoadingTextElipsis.attr("src"),
		oxyProgressBarInterval;

	function oxySetLoadingText(texts) {

		var wordInterval = Math.floor(Math.random() * (2000 - 1000) + 1000),
			dotsInterval = Math.floor(wordInterval / 4)

		if (texts.length) {
			setTimeout(function(){

				// show text
				oxyLoadingTextElement.text(texts[0])

				// restart ellipsis gif
				oxyLoadingTextElipsis.attr("src",oxyLoadingTextElipsisSrc)
				
				// next word recursion
				texts.splice(0,1)
				oxySetLoadingText(texts)
			
			}, wordInterval)
		}
	}

	function oxyStartProgressBar() {
		
		var loadingBarProgress = jQuery("#oxy-page-loading-bar-progress"),
			x = 0;

		oxyProgressBarInterval = setInterval(function(){
			x += 0.8,	
			progress = Math.atan(x/1.5-6)+1.41;
			progress = progress * 100/3.14;

			loadingBarProgress.width(progress+"%")
		}, 200);
	}

	function oxyEndProgressBar(time) {
		
		var loadingBarProgress = jQuery("#oxy-page-loading-bar-progress");		
		clearInterval(oxyProgressBarInterval);
		loadingBarProgress.width("100%");
	}

	oxySetLoadingText(oxyLoadingTexts)
	oxyStartProgressBar()

</script>

<div id="oxygen-ui"
	ng-class="{'oxygen-editing-media':iframeScope.isEditing('media'), 'oxygen-editing-class':iframeScope.isEditing('class'), 'oxygen-editing-state':iframeScope.isEditing('state'), 'oxygen-editing-special':iframeScope.isEditing('media')||iframeScope.isEditing('class')||iframeScope.isEditing('state'), 'oxygen-content-editing':actionTabs['contentEditing']}" >
	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1">
		<defs>
			<symbol id="oxy-icon-close-outline" viewBox="0 0 20 20">
				<title>close-outline</title>
				<path d="M2.93 17.070c-1.884-1.821-3.053-4.37-3.053-7.193 0-5.523 4.477-10 10-10 2.823 0 5.372 1.169 7.19 3.050l0.003 0.003c1.737 1.796 2.807 4.247 2.807 6.947 0 5.523-4.477 10-10 10-2.7 0-5.151-1.070-6.95-2.81l0.003 0.003zM4.34 15.66c1.449 1.449 3.45 2.344 5.66 2.344 4.421 0 8.004-3.584 8.004-8.004 0-2.21-0.896-4.211-2.344-5.66v0c-1.449-1.449-3.45-2.344-5.66-2.344-4.421 0-8.004 3.584-8.004 8.004 0 2.21 0.896 4.211 2.344 5.66v0zM14.24 7.17l-2.83 2.83 2.83 2.83-1.41 1.41-2.83-2.83-2.83 2.83-1.41-1.41 2.83-2.83-2.83-2.83 1.41-1.41 2.83 2.83 2.83-2.83 1.41 1.41z"></path>
			</symbol>
			<symbol id="oxy-icon-search" viewBox="0 0 20 20">
				<title>search</title>
				<path d="M12.9 14.32c-1.34 1.049-3.050 1.682-4.908 1.682-4.418 0-8-3.582-8-8s3.582-8 8-8c4.418 0 8 3.582 8 8 0 1.858-0.633 3.567-1.695 4.925l0.013-0.018 5.35 5.33-1.42 1.42-5.33-5.34zM8 14c3.314 0 6-2.686 6-6s-2.686-6-6-6v0c-3.314 0-6 2.686-6 6s2.686 6 6 6v0z"></path>
			</symbol>
		</defs>
	</svg>

	
	<div id="oxygen-topbar" class="oxygen-toolbar">

        <div class="oxygen-left-button-wrap">
			<?php if (oxygen_vsb_current_user_can_full_access()||oxygen_vsb_user_has_enabled_elements()||
					  oxygen_vsb_user_can_use_design_library()||oxygen_vsb_user_can_use_reusable_parts()) : ?>
            <div class="oxygen-add-button oxygen-toolbar-button"
                ng-click="switchActionTab('componentBrowser')"
                ng-dblclick="switchTab('components', 'fundamentals')">
                	<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/add.svg">
                	<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/add--hover.svg">
            </div>
            <?php endif; ?>
        </div>

		<!-- Media Query -->
		<div class="oxygen-media-query-panel">
			<div class="oxygen-media-queries-repeater"
				ng-repeat="name in iframeScope.sortedMediaList(false,iframeScope.isEditing('class'))"
				ng-class="{'oxy-media-styles-present':iframeScope.isHasMedia(name),'oxygen-current-media-query':iframeScope.getCurrentMedia()==name}"> 
				<div id="oxy-media-query-{{name}}" 
					class="oxygen-media-queries-item"
					data-tippy-template="oxy-media-query-template-{{name}}" 
					ng-click="iframeScope.setCurrentMedia(name,true,iframeScope.isEditing('class'));">
						<img ng-src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/media-query/{{name}}.svg'/>
				</div>
				<div style="display: none;">
					<div id="oxy-media-query-template-{{name}}" 
						class="oxy-media-query-tooltip">
						<div class="oxy-media-query-tooltip-name">
							{{iframeScope.getMediaTitle(name,false,iframeScope.isEditing('class'))}}
						</div>
						<div class="oxy-media-query-tooltip-clear-action"
							data-media-name="{{name}}">
							×
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End of Media Query -->

		<div class='oxygen-toolbar-panels'>

			<div class='oxygen-toolbar-panel'>
				<?php ct_template_builder_settings() ?>
			</div>

			<div class="oxygen-toolbar-panel oxygen-formatting-toolbar-panel">
				<?php require_once "views/editor-panel.view.php"; ?>
			</div>
			
			<div class='oxygen-toolbar-panel'>
				<div class='oxygen-zoom-control'
					ng-show="viewportScale<1||viewportScaleLocked">
					<label><?php _e("Zoom:", "oxygen"); ?></label>
					<span class='oxygen-zoom-control-zoom-amount'>{{viewportScale * 100 | number : 0}}%</span>
					<span class='oxygen-zoom-icon'
						ng-class="{'oxygen-zoom-icon-active':viewportScaleLocked}"
						ng-click="lockViewportScale()">
						<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/zoom-lock.svg' />
					</span>
				</div>
			</div>

		</div>
		<!-- .oxygen-toolbar-panels -->

		<div class="oxygen-toolbar-menus oxygen-undo-redo-menus">
            
            <div class="oxygen-undo-redo-buttons">
                <div class="oxygen-undo-button oxygen-toolbar-button" ng-click="iframeScope.undo()" ng-class="{'disabled': !iframeScope.canUndo}" >
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/undo.svg" />
                    <!-- <span><?php _e("Undo", "oxygen"); ?></span> -->
                </div>
                <div class="oxygen-redo-button oxygen-toolbar-button" ng-click="iframeScope.redo()" ng-class="{'disabled': !iframeScope.canRedo}" >
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/redo.svg" />
                    <!-- <span><?php _e("Redo", "oxygen"); ?></span> -->
                </div>
            </div>
		</div>

		
		<div class="oxygen-toolbar-menus">
			<?php if(oxygen_vsb_get_user_edit_mode() == 'edit_only' || oxygen_vsb_current_user_can_full_access()):?>
				<div class="oxygen-dom-tree-button" 
					ng-click="switchTab('sidePanel','DOMTree')">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/dom-tree.svg" />
				</div>
				<div class="oxygen-history-button" 
					ng-click="switchTab('sidePanel','History')">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/history-icon.svg" />
				</div>
			<?php endif; ?>
			<?php if (oxygen_vsb_current_user_can_full_access()) : ?>
				<div class="oxygen-settings-button"
					ng-click="toggleSettingsPanel()">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/settings-icon.svg" />
				</div>
				<div class="oxygen-stylesheets-button"
					ng-click="switchTab('sidePanel','styleSheets');">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/stylesheets-icon.svg" />
				</div>
				<div class="oxygen-selectors-button"
					ng-click="switchTab('sidePanel','selectors');">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/selectors-icon.svg" />
				</div>
			<?php endif; ?>

			<div class="oxygen-back-to-wp-menu oxygen-toolbar-button oxygen-select">
				<div class="oxygen-toolbar-button-dropdown">
					<a class="oxygen-toolbar-button-dropdown-option"
						ng-click="iframeScope.setPostEditLockTransient(false)"
						ng-href="{{iframeScope.ajaxVar.adminURL}}">
						<?php _e("WP&nbspAdmin","oxygen");?>
					</a>
					<a class="oxygen-toolbar-button-dropdown-option"
						ng-hide="iframeScope.ajaxVar.ctTemplate"
						ng-href="{{iframeScope.ajaxVar.frontendURL}}">
						<?php _e("Frontend","oxygen");?>
					</a>
					<a class="oxygen-toolbar-button-dropdown-option"
						ng-show="iframeScope.ajaxVar.ctTemplate"
						ng-href="{{iframeScope.template.postData.frontendURL}}">
						<?php _e("Frontend","oxygen");?>
					</a>
				</div>
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/back-to-wp.svg" />
			</div>

			<div class="oxygen-save-button oxygen-toolbar-button"
				ng-click="iframeScope.savePage()">
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/save.svg">
			</div>

		</div>

	</div><!-- #oxygen-topbar -->

	<div class='oxygen-add-section-library-flyout-panel' ng-class="{'ui-hide':!showLeftSidebar}">
		<div class='oxygen-add-section-library-flyout-category'
			ng-repeat="(key, designSet) in iframeScope.experimental_components track by key"
			id='category-designset-{{key}}-pages'>

			<div class='oxygen-add-section-library-addable'
				ng-repeat="(index, page) in designSet.pages track by index"
				ng-click="iframeScope.showAddItemDialog(page.id, 'page', '0', '', page.source, '', page.name, '', key)">
				<img ng-src='{{page.custom_screenshot||page.screenshot_url}}' />
				<div class='oxygen-add-section-library-addable-details'
					ng-class="{'hilite': page.firstFew === 0}">
					{{ page.firstFew !== 0 ? page.name : '<?php _e('Pro Only', 'oxygen');?>'}}
				</div>
			</div>
		</div>
		<div class='oxygen-add-section-library-flyout-category'
			ng-repeat="(key, designSet) in iframeScope.experimental_components track by key"
			id='category-designset-{{key}}-templates'>

			<div class='oxygen-add-section-library-addable'
				ng-repeat="(index, page) in designSet.templates track by index"
				ng-click="iframeScope.showAddItemDialog(page.id, 'template', '0', '', page.source, '', page.name, '', key)">
				<img ng-src='{{page.custom_screenshot||page.screenshot_url}}' />
				<div class='oxygen-add-section-library-addable-details'
					ng-class="{'hilite': page.firstFew === 0}">
					{{ page.firstFew !== 0 ? page.name : '<?php _e('Pro Only', 'oxygen');?>'}}
				</div>
			</div>
		</div>

		<div ng-repeat="(key, designSet) in iframeScope.experimental_components track by key">
			
			<div class='oxygen-add-section-library-flyout-category'
				ng-repeat="(catKey, category) in designSet.items track by category.slug"
				id='category-category-{{key}}-{{category.slug}}'>

				<div class='oxygen-add-section-library-addable'
					ng-repeat="(index, component) in category.contents track by index"
					ng-click="iframeScope.showAddItemDialog(component.id, 'component', '0', '', component.source, component.page, component.name, catKey, key)">
					<img ng-src='{{component.custom_screenshot||component.screenshot_url}}' />
					<div class='oxygen-add-section-library-addable-details'
						ng-class="{'hilite': component.firstFew === 0}">
						{{ component.firstFew !== 0 ? component.name : '<?php _e('Pro Only', 'oxygen');?>'}}
					</div>
				</div>
			</div>
		</div>
		

		<div class='oxygen-add-section-library-flyout-category'
			ng-repeat="(key, components) in iframeScope.libraryCategories track by components.slug"
			id='category-category-{{components.slug}}'>

			<div class='oxygen-add-section-library-addable'
				ng-repeat="(index, component) in components.contents track by index"
				ng-click="iframeScope.showAddItemDialog(component.id, 'component', '0', '', component.source, component.page, component.name, key)">
				<img ng-src='{{component.custom_screenshot||component.screenshot_url}}' />
				<div class='oxygen-add-section-library-addable-details'>
					{{component.name}}
				</div>
			</div>
		</div>

		<div class='oxygen-add-section-library-flyout-category'
			ng-repeat="(key, pages) in iframeScope.libraryPages track by pages.slug"
			id='category-page-{{pages.slug}}'>

			<div class='oxygen-add-section-library-addable'
				ng-repeat="(index, page) in pages.contents track by index"
				ng-click="iframeScope.showAddItemDialog(page.id, 'page', '0', '', page.source, '', page.name, key)">
				<img ng-src='{{page.custom_screenshot||page.screenshot_url}}' />
				<div class='oxygen-add-section-library-addable-details'>
					{{page.name}}
				</div>
			</div>
		</div>

	</div><!-- .oxygen-add-section-library-flyout-panel -->

	<div id="oxygen-sidebar" 
		ng-init="showLinkDataDialog = false"
		ng-class="{'oxygen-selector-detector-mode':iframeScope.selectorDetector.mode==true,'oxygen-selector-locked':iframeScope.getOption('selector-locked')=='true'}">

        <div class="oxygen-editing-empty-message" ng-show="showEmptyMessage">
            <div class="oxygen-sidebar-template">
                <img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/cursor-icon.svg' width="44" height="44">
                <div>Select an element<br> or click +Add button</div>
            </div>
        </div>

		<div class='oxygen-editing-stylesheet-message' ng-if="iframeScope.selectedNodeType==='stylesheet'" ng-class="{'ui-hide':!showLeftSidebar}">

			<div class="oxygen-sidebar-template">
				<h2><?php _e("Style Sheet", "oxygen"); ?></h2>
				
				<div class="oxygen-reusable-title">
					<h1>{{iframeScope.stylesheetToEdit['name']}}</h1>
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg'
						title="<?php _e("Remove Stylesheet", "oxygen"); ?>"
						ng-click="iframeScope.deleteStyleSheet(iframeScope.stylesheetToEdit, $event)"/>
				</div>

				
			</div>
		</div>

		<div class='oxygen-editing-folder-message' 
			ng-show="iframeScope.selectedNodeType==='selectorfolder'" ng-class="{'ui-hide':!showLeftSidebar}">

			<div class="oxygen-sidebar-template">
				<h2><?php _e("Selector Folder", "oxygen"); ?></h2>
				
				<div class="oxygen-reusable-title">
					<h1>{{iframeScope.selectedSelectorFolder && iframeScope.selectedSelectorFolder!==-1?iframeScope.selectedSelectorFolder:'Uncategorized'}}</h1>
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg'
						ng-if="iframeScope.selectedSelectorFolder && iframeScope.selectedSelectorFolder!==-1"
						title="<?php _e("Remove Folder", "oxygen"); ?>"
						ng-click="iframeScope.deleteSelectorFolder(iframeScope.selectedSelectorFolder,$event); iframeScope.selectorFolderMenuOpen = false"/>
				</div>

				<a href="#" class="oxygen-sidebar-template-button"
					ng-click="iframeScope.styleFolders[iframeScope.selectedSelectorFolder].status = (iframeScope.styleFolders[iframeScope.selectedSelectorFolder].status == 1 ? 0 : 1); iframeScope.selectorFolderMenuOpen = false; iframeScope.classesCached = false; iframeScope.outputCSSOptions()">
					{{iframeScope.styleFolders[iframeScope.selectedSelectorFolder].status?'<?php _e("Disable Folder", "oxygen"); ?>':'<?php _e("Enable Folder", "oxygen"); ?>'}}</a>

				
			</div>
		</div>

		<div class='oxygen-editing-folder-message' 
			ng-show="iframeScope.selectedNodeType==='cssfolder'" ng-class="{'ui-hide':!showLeftSidebar}">

			<div class="oxygen-sidebar-template">
				<h2><?php _e("Stylesheet Folder", "oxygen"); ?></h2>
				
				<div class="oxygen-reusable-title">
					<h1>{{iframeScope.selectedCSSFolder && iframeScope.selectedCSSFolder!==-1?iframeScope.getCSSFolder(iframeScope.selectedCSSFolder)['name']:'Uncategorized'}}</h1>
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg'
						ng-if="iframeScope.selectedCSSFolder && iframeScope.selectedCSSFolder!==-1"
						title="<?php _e("Remove Folder", "oxygen"); ?>"
						ng-click="iframeScope.deleteStyleSheet(iframeScope.getCSSFolder(iframeScope.selectedCSSFolder),$event); iframeScope.selectorFolderMenuOpen = false"/>
				</div>

				<a href="#" class="oxygen-sidebar-template-button"
					ng-if="!iframeScope.selectedCSSFolder || iframeScope.selectedCSSFolder===-1"
					ng-click="iframeScope.toggleUncategorizedStyleSheets(iframeScope.selectedCSSFolder !== -1); iframeScope.cssFolderMenuOpen = false; iframeScope.classesCached = false; iframeScope.outputCSSOptions()">
					{{iframeScope.selectedCSSFolder !== -1?'<?php _e("Disable Folder", "oxygen"); ?>':'<?php _e("Enable Folder", "oxygen"); ?>'}}</a>

				<a href="#" class="oxygen-sidebar-template-button"
					ng-if="iframeScope.selectedCSSFolder && iframeScope.selectedCSSFolder!==-1"
					ng-click="iframeScope.getCSSFolder(iframeScope.selectedCSSFolder).status = (iframeScope.getCSSFolder(iframeScope.selectedCSSFolder).status == 1 ? 0 : 1); iframeScope.cssFolderMenuOpen = false; iframeScope.classesCached = false; iframeScope.outputCSSOptions()">
					{{iframeScope.getCSSFolder(iframeScope.selectedCSSFolder).status === 1?'<?php _e("Disable Folder", "oxygen"); ?>':'<?php _e("Enable Folder", "oxygen"); ?>'}}</a>

				
			</div>
		</div>

		<div class='oxygen-editing-styleset-message' 
			ng-show="iframeScope.selectedNodeType==='styleset'" ng-class="{'ui-hide':!showLeftSidebar}">

			<div class="oxygen-sidebar-template">
				<h2><?php _e("Style Set", "oxygen"); ?></h2>
				
				<div class="oxygen-reusable-title">
					<h1>{{iframeScope.selectedStyleSet}}</h1>
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg'
						title="<?php _e("Remove Component", "oxygen"); ?>"
						ng-show="iframeScope.selectedStyleSet!=='Uncategorized Custom Selectors'"
						ng-click="$parent.deleteStyleSet(iframeScope.selectedStyleSet)"/>
				</div>

				
			</div>
		</div>

		<div class='oxygen-editing-reusable-message' 
			ng-show="isActiveName('ct_reusable') && !isActiveActionTab('componentBrowser')" ng-class="{'ui-hide':!showLeftSidebar}">

			<div class="oxygen-sidebar-template">
				<h2><?php _e("REUSABLE PART", "oxygen"); ?></h2>
				
				<div class="oxygen-reusable-title">
					<h1>{{iframeScope.component.options[iframeScope.component.active.id]['nicename']}}</h1>
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg'
						title="<?php _e("Remove Component", "oxygen"); ?>"
						ng-show="iframeScope.component.active.id > 0 && !isActiveName('oxy_header_left') && !isActiveName('oxy_header_center') && !isActiveName('oxy_header_right')"
						ng-click="iframeScope.removeActiveComponent()"/>
				</div>

				<div class='oxygen-active-element-breadcrumb'
					ng-if="!iframeScope.isEditing('custom-selector')">
					<span ng-repeat='item in iframeScope.selectAncestors'>
						<span ng-if="item.id > 0 && item.id < 100000" ng-click="iframeScope.activateComponent(item.id, item.tag)">{{item.name}}</span>
						<span ng-if="item.id > 0 && item.id < 100000" class="oxygen-active-element-breadcrumb-arrow">&gt;</span>
						<span ng-if="item.id == 0" class='oxygen-active-element-breadcrumb-active'>{{item.name}}</span>
					</span>
				</div>

				<a href="#" class="oxygen-sidebar-template-button"
					ng-href="{{iframeScope.reusableEditLinks[iframeScope.component.active.id].replace('&amp;', '&')}}">
					<?php _e("Open &amp; Edit Reusable Part", "oxygen"); ?></a>
			</div>
		</div><!-- .oxygen-editing-reusable-message -->

		<div class='oxygen-editing-template-message' 
			ng-if="isActiveName('ct_template') && !isActiveActionTab('componentBrowser')" ng-class="{'ui-hide':!showLeftSidebar}">

			<div class="oxygen-sidebar-template">

				<h2><?php _e("TEMPLATE", "oxygen"); ?></h2>
				<h1>{{iframeScope.outerTemplateData['template_name']}}</h1>

				<a href="#" ng-href="{{iframeScope.outerTemplateData['edit_link']}}" class="oxygen-sidebar-template-button"><?php _e("Open &amp; Edit Template", "oxygen"); ?></a>

			</div>
		</div>

		<div class='oxygen-editing-template-message' 
			ng-if="isActiveName('ct_inner_content') && !isActiveActionTab('componentBrowser')" ng-class="{'ui-hide':!showLeftSidebar}">

				<div class="oxygen-sidebar-template">
					<div class="oxygen-reusable-title">
						<h2><?php _e("INNER CONTENT", "oxygen"); ?></h2>
						<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg'
							title="<?php _e("Remove Component", "oxygen"); ?>"
							ng-show="iframeScope.component.active.id > 0 && !isActiveName('oxy_header_left') && !isActiveName('oxy_header_center') && !isActiveName('oxy_header_right')"
							ng-click="iframeScope.removeActiveComponent()"/>
					</div>
					
					<h1>{{template.postData.post_title}}</h1>

					<div class='oxygen-active-element-breadcrumb'
						ng-if="!iframeScope.isEditing('custom-selector')">
						<span ng-repeat='item in iframeScope.selectAncestors'>
							<span ng-if="item.id > 0 && item.id < 100000" ng-click="iframeScope.activateComponent(item.id, item.tag)">{{item.name}}</span>
							<span ng-if="item.id > 0 && item.id < 100000" class="oxygen-active-element-breadcrumb-arrow">&gt;</span>
							<span ng-if="item.id == 0" class='oxygen-active-element-breadcrumb-active'>{{item.name}}</span>
						</span>
					</div>

					<a href="#" ng-if="iframeScope.template.postData.edit_link" ng-href="{{iframeScope.template.postData.edit_link}}" class="oxygen-sidebar-template-button"><?php _e("Open &amp; Edit Inner Content", "oxygen"); ?></a>

				</div>
		</div>

		<div class="oxygen-sidebar-top" ng-class="{'ui-hide':!showLeftSidebar}">

			<div class='oxygen-sidebar-currently-editing'
				ng-show="!iframeScope.styleSetActive 
						&& iframeScope.selectedNodeType!=='selectorfolder'
						&& iframeScope.selectedNodeType!=='cssfolder'
						&& iframeScope.component.active.name 
						&& iframeScope.component.active.name!='root' 
						&& iframeScope.component.active.name!='ct_inner_content' 
						&& !iframeScope.isEditing('style-sheet') 
						&& !isActiveName('ct_reusable') 
						&& !isActiveName('ct_template') 
						&& !isActiveActionTab('componentBrowser')">
				<!-- !iframeScope.selectedNodeType || iframeScope.selectedNodeType==='selector' || iframeScope.selectedNodeType==='class'  -->
				<?php do_action("ct_toolbar_component_header"); ?>

			</div>

			<div class='oxygen-sidebar-currently-editing oxygen-sidebar-currently-editing-top'
				ng-if="iframeScope.selectedNodeType==='styleset' || iframeScope.selectedNodeType==='class'">
				<div class="oxygen-control-row">
					<div class="oxygen-control-wrapper">
						<label class="oxygen-control-label">Folder</label>

						<div class="oxygen-control">
							<div class='oxygen-select oxygen-select-box-wrapper oxygen-style-set-dropdown'>
								<div class='oxygen-select-box' id="oxygen-selector-folder-dropdown">
									<div class="oxygen-select-box-current">{{iframeScope.currentActiveFolder !== '-1' && iframeScope.currentActiveFolder !== -1 ? iframeScope.currentActiveFolder : ''}}</div>
									<div class="oxygen-select-box-dropdown"></div>
								</div>
								
								<div class="oxygen-select-box-options">
									<div class="oxygen-select-box-option" ng-click="iframeScope.setCurrentSelectorFolder('');">
											<span>
												<?php _e('None', 'oxygen');?>
											</span>
									</div>

									<div class="oxygen-select-box-option" ng-repeat="(folderName, folder) in iframeScope.styleFolders track by folderName"
										ng-click="iframeScope.setCurrentSelectorFolder(folderName);">
											
											<span>
												{{folderName}}
											</span>
									</div>
								</div>
							</div>
						</div>

					   <!-- <span ng-if="iframeScope.selectedStyleSet && !iframeScope.isEditing('class')"> for Style Set {{iframeScope.selectedStyleSet}}</span>
					    <span ng-if="iframeScope.isEditing('class')"> for class {{iframeScope.currentClass}}</span>-->
					</div>
			    </div>
			</div>


			<div class='oxygen-sidebar-currently-editing oxygen-sidebar-currently-editing-top'
				ng-if="iframeScope.selectedNodeType==='stylesheet'">
				<div class="oxygen-control-row">
				  <div class="oxygen-control-wrapper">
				    <label class="oxygen-control-label">Folder</label>
				    <div class="oxygen-control">
					    <div class='oxygen-select oxygen-select-box-wrapper oxygen-style-set-dropdown'>
					      <div class='oxygen-select-box' id="oxygen-selector-folder-dropdown">
					        <div class="oxygen-select-box-current">{{iframeScope.currentActiveStylesheetFolder}}</div>
					        <div class="oxygen-select-box-dropdown"></div>
					      </div>
					      <div class="oxygen-select-box-options">
					        <div class="oxygen-select-box-option" ng-click="iframeScope.setCurrentStylesheetFolder(0);">
					            
					            <span>
					              None
					            </span>
					           
					        </div>
					        <div class="oxygen-select-box-option" ng-repeat="folder in iframeScope.styleSheets | filter : { folder : 1 } track by folder.id"
					          ng-click="iframeScope.setCurrentStylesheetFolder(folder.id);">
					            
					            <span>
					              {{folder.name}}
					            </span>
					           
					        </div>
					      </div>
					    </div>
					</div>
				  </div>
				</div>

			</div>

			<div ng-if="iframeScope.selectedNodeType==='stylesheet' && iframeScope.isEditing('style-sheet')">
				<?php require_once "views/style-sheet.view.php" ;?>
			</div>
			
		</div>
		

		<div class="oxygen-sidebar-top" ng-class="{'ui-hide':!showLeftSidebar}" 
			ng-show="!iframeScope.styleSetActive 
					&& iframeScope.selectedNodeType!=='selectorfolder'
					&& iframeScope.selectedNodeType!=='cssfolder'
					&& iframeScope.component.active.name 
					&& iframeScope.component.active.name!='root' 
					&& !iframeScope.isEditing('style-sheet') 
					&& !isActiveName('ct_reusable') 
					&& !isActiveName('ct_template') 
					&& !isActiveActionTab('componentBrowser')">
			<?php 
			$tabs = "";
			foreach ($this->component_with_tabs as $key => $tab) {
				$tabs .= ",'$tab'"; 
			} 
			?>
			<?php if (oxygen_vsb_user_can_use_advanced_tab()) : ?>
			<div class='oxygen-sidebar-tabs'>
				<div class='oxygen-sidebar-tabs-tab'
					ng-click="styleTabAdvance=false;closeTabs(['oxy_posts_grid', 'dynamicList', 'slider','navMenu','effects','gallery'<?php echo $tabs; ?>]);toggleSidebar(true)" 
					ng-class="{'oxygen-sidebar-tabs-tab-active':!styleTabAdvance}"><?php _e("Primary", "oxygen"); ?>
				</div>
				<div class='oxygen-sidebar-tabs-tab'
					ng-click="showAllStylesFunc(); styleTabAdvance=true" 
					ng-class="{'oxygen-sidebar-tabs-tab-active':styleTabAdvance,'oxy-styles-present':iframeScope.isTabHasOptions()}"><?php _e("Advanced", "oxygen"); ?>
				</div>
			</div>
			<!-- .oxygen-sidebar-tabs -->
			<?php endif; ?>
			
		</div>


		<!-- .oxygen-sidebar-top -->

			<div class='oxygen-sidebar-breadcrumb'
				ng-class="{'oxygen-sidebar-breadcrumb-fill': isShowTab('advanced','background-gradient'),'ui-hide':!showLeftSidebar}"
				ng-show="!iframeScope.styleSetActive && showAllStyles==false && (styleTabAdvance==true||isActiveName('ct_inner_content')) && !hasOpenTabs('effects')">
				<div class='oxygen-sidebar-breadcrumb-icon'
					ng-click="showAllStylesFunc();">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg' />
				</div>
				<div class='oxygen-sidebar-breadcrumb-all-styles'
					ng-click="showAllStylesFunc();"><?php _e("All Styles", "oxygen"); ?></div>
				<?php foreach ( $this->options['advanced'] as $key => $tab ) : ?>
					<div class='oxygen-sidebar-breadcrumb-separator' 
						ng-if="isShowTab('advanced','<?php echo $key; ?>')">/</div>
					<div class='oxygen-sidebar-breadcrumb-current' 
						ng-if="isShowTab('advanced','<?php echo $key; ?>')"><?php echo $tab['heading']; ?></div>
				<?php endforeach; ?>

				<!-- Exception for Background Gradient -->
				<div class='oxygen-sidebar-breadcrumb-separator' 
					ng-show="isShowTab('advanced','background-gradient')">/</div>

				<div class='oxygen-sidebar-breadcrumb-all-styles'
					ng-show="isShowTab('advanced','background-gradient')"
					ng-click="switchTab('advanced', 'background');"><?php _e("Background", "oxygen"); ?></div>

				<div class='oxygen-sidebar-breadcrumb-separator' 
					ng-show="isShowTab('advanced','background-gradient')">/</div>

				<div class='oxygen-sidebar-breadcrumb-current' 
					ng-show="isShowTab('advanced','background-gradient')"><?php _e("Gradient", "oxygen"); ?></div>
				<!-- Exception for Background Gradient ENDS -->
			</div>
			<!-- .oxygen-sidebar-breadcrumb -->

		<?php 
		$tabs = "";
		foreach ($this->component_with_tabs as $key => $tab) {
			$tabs .= "||hasOpenTabs('$tab')"; 
		} 
		?>
		<div id="oxygen-sidebar-control-panel-basic-styles" class="oxygen-sidebar-control-panel oxygen-sidebar-control-panel-basic-styles" 
        	ng-class="{'oxygen-disabled':(<?php echo oxygen_vsb_user_can_use_ids() || oxygen_vsb_current_user_can_full_access() ? "false" : "true"; ?>&&iframeScope.isEditing('id'))||(<?php echo oxygen_vsb_user_can_use_classes() || oxygen_vsb_current_user_can_full_access() ? "false" : "true"; ?>&&iframeScope.isEditing('class')),'ui-hide':!showLeftSidebar,'oxygen-widget-controls':isActiveName('ct_widget'),'oxygen-selector-detector-controls':iframeScope.selectorDetector.mode==true,'oxygen-basic-styles-subtub':hasOpenTabs('navMenu')||hasOpenTabs('slider')||hasOpenTabs('oxy_posts_grid')||hasOpenTabs('dynamicList')||hasOpenTabs('gallery')<?php echo $tabs; ?>}"

			ng-show="!iframeScope.styleSetActive 
				&& iframeScope.selectedNodeType!=='selectorfolder'
				&& iframeScope.selectedNodeType!=='cssfolder'
				&& iframeScope.component.active.name 
				&& iframeScope.component.active.id!=0 
				&& !iframeScope.isEditing('style-sheet') 
				&& !styleTabAdvance 
				&& !isActiveActionTab('componentBrowser') 
				&& !isActiveName('ct_reusable') ">
			<?php do_action("ct_toolbar_component_settings"); ?>
			<div ng-show="showSidebarLoader" class="oxygen-sidebar-loader"><i class="fa fa-cog fa-4x fa-spin"></i></div>
		
			<div  
				ng-repeat="attribute in iframeScope.getOption('custom-attributes') track by $index"
				ng-show="attribute.pinned">
				<div class="oxygen-control-row">
					<div class="oxygen-control-wrapper">
						<label class="oxygen-control-label">{{attribute.name}}</label>
						<div class="oxygen-control">			
							<div class="oxygen-input">
								<input type="text" spellcheck="false"
									placeholder="<?php _e("value", "oxygen"); ?>" 
									ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,'custom-attributes');iframeScope.checkResizeBoxOptions('custom-attributes'); iframeScope.validateCustomAttributeValue($index);" 
									ng-model="$parent.iframeScope.component.options[$parent.iframeScope.component.active.id]['model']['custom-attributes'][$index]['value']">
									<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertDynamicDataShortcode" optionname="'custom-attributes.'+$index+'.value'"><?php _e("data", "oxygen"); ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
		<!-- .oxygen-sidebar-control-panel-basic-styles -->

		<div class='oxygen-sidebar-control-panel'
			ng-class="{'oxygen-disabled-element':iframeScope.getOption('not-registered'),'oxygen-disabled':(<?php echo oxygen_vsb_user_can_use_ids() || oxygen_vsb_current_user_can_full_access() ? "false" : "true"; ?>&&iframeScope.isEditing('id'))||(<?php echo oxygen_vsb_user_can_use_classes() || oxygen_vsb_current_user_can_full_access() ? "false" : "true"; ?>&&iframeScope.isEditing('class')),'oxygen-sidebar-advanced-home':showAllStyles,'ui-hide':!showLeftSidebar}"
			ng-show="!iframeScope.styleSetActive 
					&& iframeScope.selectedNodeType!=='selectorfolder'
					&& iframeScope.selectedNodeType!=='cssfolder'
					&& (iframeScope.component.active.name 
					&& iframeScope.component.active.name!='root' 
					&& !iframeScope.isEditing('style-sheet') 
					&& (styleTabAdvance) 
					&& !isActiveActionTab('componentBrowser') 
					&& !isActiveName('ct_reusable') )">
		 		<?php do_action("ct_toolbar_advanced_settings"); ?>
		</div>
		<!-- .oxygen-sidebar-control-panel -->
			
		<div class="oxygen-no-item-message" ng-class="{'ui-hide':!showLeftSidebar}" 
			ng-hide="(iframeScope.component.active.name && iframeScope.component.active.id!=0) || iframeScope.isEditing('style-sheet') || isActiveActionTab('componentBrowser')">
				<?php _e("No Item Selected","oxygen"); ?>
		</div>			
		<!-- .oxygen-no-item-message -->

		<?php global $oxygen_meta_keys;?>
		<div id="oxygen-link-data-dialog-wrap" style="display: none;">
			<div id="oxygen-link-data-dialog-opener" class="oxygen-dynamic-data-browse" ng-mousedown = "showLinkDataDialog = !showLinkDataDialog" >Data</div>
			<div ng-show="showLinkDataDialog" id="oxygen-link-data-dialog" class="oxygen-data-dialog oxygen-data-dialog-link">
				<h1>Insert Dynamic Data</h1>
				<span class="oxygen-data-dialog-close" ng-mousedown="showLinkDataDialog=false">×</span>
				<div>
					<div class='oxygen-data-dialog-data-picker'>
						<h2>Post</h2>
						<ul>
							<li ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"permalink\"]")'>Permalink</li>
							<li ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"comments_link\"]")'>Comments Link</li>
							<li ng-init="showOptionsPanel=false">
								<span ng-mousedown='showOptionsPanel = "postMeta"'>Meta / Custom Field</span>
								<div ng-show='showOptionsPanel === "postMeta"' class='oxygen-data-dialog-options'>
									<span class="oxygen-data-dialog-close" ng-mousedown="showOptionsPanel=false">×</span>
									<h3>Meta / Custom Field Options</h3>
									<div>
										<label>meta_key</label>
										<select ng-model='key'>
										<?php foreach($oxygen_meta_keys as $key) { ?>
											<option><?php echo $key ;?></option>
										<?php } ?>
										</select>
										<input type="text" ng-model='key' />
									</div>

									<button ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"meta\""+(key?(" key=\""+key+"\""):"")+"]")'>INSERT</button>
								</div>
							</li>
						</ul>
					</div>

					<div class='oxygen-data-dialog-data-picker'>
						<h2>Featured Image</h2>
						<ul>
							<li ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"featured_image\"]")'>Featured Image URL</li>
						</ul>
					</div>

					<div class='oxygen-data-dialog-data-picker'>
						<h2>Author</h2>
						<ul>
							<li ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"author_website_url\"]")'>Author Website URL</li>
							<li ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"author_posts_url\"]")'>Author Posts URL</li>
							<li ng-init="showOptionsPanel=false">
								<span ng-mousedown='showOptionsPanel = "authorPostMeta"'>Meta / Custom Field</span>
								<div ng-show='showOptionsPanel === "authorPostMeta"' class='oxygen-data-dialog-options'>
								<span class="oxygen-data-dialog-close" ng-mousedown="showOptionsPanel=false">×</span>
									<h3>Author Meta / Custom Field Options</h3>
									<div>
										<label>meta_key</label>
										
										<input type="text" ng-model='key' />
									</div>

									<button ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"author_meta\""+(key?(" key=\""+key+"\""):"")+"]")'>INSERT</button>
								</div>
							</li>
						</ul>
					</div>

					<div class='oxygen-data-dialog-data-picker'>
						<h2>Current User</h2>
						<ul>
							<li ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"user_website_url\"]")'>User Website URL</li>
							<li ng-init="showOptionsPanel=false">
								<span ng-mousedown='showOptionsPanel = "userPostMeta"'>Meta / Custom Field</span>
								<div ng-show='showOptionsPanel === "userPostMeta"' class='oxygen-data-dialog-options'>
									<span class="oxygen-data-dialog-close" ng-mousedown="showOptionsPanel=false">×</span>
									<h3>User Meta / Custom Field Options</h3>
									<div>
										<label>meta_key</label>
										<input type="text" ng-model='key' />
									</div>

									<button ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"user_meta\""+(key?(" key=\""+key+"\""):"")+"]")'>INSERT</button>
								</div>
							</li>
						</ul>
					</div>

				</div>
			</div>
		
		</div>


		<?php
		/**
		 * +ADD section
		 */
		?>

		<div id="oxygen-add-sidebar" class="oxygen-add-sidebar" ng-class="{'ui-hide':!showLeftSidebar}"
			ng-init="showEmmetCLI=false"
			ng-show="isActiveActionTab('componentBrowser')">
			<div class="oxygen-add-searchbar-wrapper"
				ng-hide="showEmmetCLI">
				<input class="oxygen-add-searchbar" type="text"
		  			   focus-me="addSearchbarInputFocus"
					   required
					   placeholder="Type to search components"
					   ng-keyup="$event.keyCode == 13 && addFilteredComponent()"
					   ng-model="componentsSearchQuery"
					   ng-change="filterComponents()" />
				<img class="oxygen-icon-search" src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/search.svg"
					ng-click="showEmmetCLI=true;emmetInputFocus=true"
					title="Switch to Emmet"/>
				<svg class="oxygen-icon-close-outline"
					ng-click="resetComponentsSearch()">
					<use xlink:href="#oxy-icon-close-outline"></use>
				</svg>
			</div>
			<div class="oxygen-add-searchbar-wrapper"
				ng-show="showEmmetCLI">
				<textarea id="oxy-emmet-cli-input" class="oxygen-add-searchbar" type="text"
		  			   focus-me="emmetInputFocus"
					   spellcheck="false"
					   placeholder="Type emmet abbreviation"
					   onkeypress="$scope.iframeScope.emmetCLIkeypress(event.which, event)"
					   ng-keydown="$event.keyCode == 13 && $event.preventDefault()"
					   ng-keyup="$event.keyCode == 13 && iframeScope.addElementsFromEmmetAbbreviation($event)">
				</textarea>
				<img class="oxygen-icon-search" src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/dom-tree.svg"
					ng-click="showEmmetCLI=false"
					title="Switch to Search"/>
			</div>
			<div id="oxygen-toolbar-original-panels" class='oxygen-add-panels'>
				<?php do_action("ct_toolbar_components_list"); ?>
			</div>
			<div id="oxygen-toolbar-search-panels" class="oxygen-add-panels" style="display: none;">
			    <div class="oxygen-add-panels-no-search-results oxygen-toolbar-no-search-results-wrapper">
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/search-result.svg"/>
					<h2 class="oxygen-add-panels-no-search-results">
						No results to show
					</h2>
				</div>
				<div id="oxygen-toolbar-search-results" class="oxygen-add-section-accordion-contents">
					<?php do_action("ct_toolbar_components_list_searchable") ?>
				</div>
			</div>
		</div><!-- #oxygen-add-sidebar -->


		<?php
		/**
		 * Manage > Settings
		 */
		?>

		<div id="oxygen-global-settings" class="oxygen-global-settings" 
			ng-show="showSettingsPanel"
			ng-class="{'oxygen-show-settings-panel':showSettingsPanel,'oxygen-global-settings-all-settings':!hasOpenTabs('settings')}">

			<div class="oxygen-sidepanel-header-row">
				<?php _e("Settings","oxygen"); ?>
				<img class="oxygen-close-icon" ng-click="toggleSettingsPanel()" src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/cross-icon.svg">
			</div>

			<div class="oxygen-settings-content">
				
				<?php
				/**
				 * Settings Main Panel
				 */
				?>
				
				<?php $this->settings_tab(__("Page settings", "oxygen"), "page", "panelsection-icons/pagesettings.svg", "hasOpenTabs('settings')","oxygen-settings-main-tab"); ?>
				
				<?php $this->settings_tab(__("Editor settings", "oxygen"), "editor", "panelsection-icons/general-config.svg", "hasOpenTabs('settings')","oxygen-settings-main-tab"); ?>
				
				<?php $this->settings_tab(__("Global Styles", "oxygen"), "default-styles", "panelsection-icons/visual.svg", "hasOpenTabs('settings')","oxygen-settings-main-tab"); ?>


				<?php
				/**
				 * Settings > Page Settings
				 */
				?>

				<div ng-if="isShowTab('settings','page')">
					<?php require_once "views/global-settings/page-settings.view.php"; ?>
				</div>


				<?php
				/**
				 * Settings > Editor
				 */
				?>

				<div ng-if="isShowTab('settings','editor')">
					<?php require_once "views/global-settings/editor.view.php"; ?>
				</div>


				<?php
				/**
				 * Settings > Global Styles
				 */
				?>

				<div class="oxygen-sidebar-flex-panel"
					ng-if="isShowTab('settings','default-styles')&&!hasOpenChildTabs('settings','default-styles')">

					<?php $this->settings_home_breadcrumbs(__("Global Styles","oxygen")); ?>

					<?php $this->settings_tab(__("Colors", "oxygen"), "colors", "panelsection-icons/general-config.svg"); ?>
					<?php $this->settings_child_tab(__("Fonts", "oxygen"), "default-styles", "fonts", "advanced/typography.svg"); ?>
					<?php $this->settings_child_tab(__("Headings", "oxygen"), "default-styles", "headings", "advanced/typography.svg"); ?>
					<?php $this->settings_child_tab(__("Body Text", "oxygen"), "default-styles", "body-text", "panelsection-icons/bodytext.svg"); ?>
					<?php $this->settings_tab(__("Links", "oxygen"), "links", "panelsection-icons/links.svg"); ?>
					<?php $this->settings_child_tab(__("Width & Breakpoints", "oxygen"), "default-styles", "page-width", "panelsection-icons/general-config.svg"); ?>
					<?php $this->settings_child_tab(__("Sections & Columns", "oxygen"), "default-styles", "sections", "panelsection-icons/general-config.svg"); ?>

					<?php

					/**
					 * Add new "Manage > Settings > Global Styles" tabs via this action hook
					 *
					 * @since 2.2
					 */

					do_action("oxygen_vsb_global_styles_tabs");

					?>

					<div class="oxygen-control-row oxygen-control-row-bottom-bar">
						<a href="#" class="oxygen-apply-button" ng-click="iframeScope.resetGlobalStylesToDefault()">
							<?php _e("Reset to Default"); ?></a>
					</div>

				</div>

				<?php
				/**
				 * Settings > Global Styles > ...
				 */
				?>

				<div ng-if="isShowTab('settings','colors')">
					<?php do_action("oxygen_toolbar_settings_colors"); ?>
				</div>

				<div ng-if="isShowTab('settings','links')">
					<?php do_action("oxygen_toolbar_settings_links"); ?>
				</div>

				<div ng-if="isShowChildTab('settings','default-styles','fonts')">
					<?php do_action("ct_toolbar_global_fonts_settings"); ?>
				</div>

				<div ng-if="isShowChildTab('settings','default-styles','headings')">
					<?php do_action("oxygen_toolbar_settings_headings"); ?>
				</div>

				<div ng-if="isShowChildTab('settings','default-styles','body-text')">
					<?php do_action("oxygen_toolbar_settings_body_text"); ?>
				</div>

				<div ng-if="isShowChildTab('settings','default-styles','page-width')">
					<?php require_once "views/global-settings/page-width.view.php"; ?>
				</div>

				<div ng-if="isShowChildTab('settings','default-styles','sections')">
					<?php require_once "views/global-settings/sections.view.php"; ?>
				</div>

				<?php
				
				/**
				 * Add new Manage > Settings panels via this action hook
				 *
				 * @since 2.2
				 */

				do_action("oxygen_vsb_settings_content");
				
				?>

			</div><!-- .oxygen-settings-content -->

		</div><!-- .oxygen-global-settings -->
		
		<?php require_once "views/side-panel.view.php"; ?>
		<?php require_once "views/dialog-window.view.php";?>
		<?php require_once "views/notice-modal.view.php"; ?>
        <?php require_once "views/dynamic-data-recursive-dialog.view.php"; ?>
        <?php do_action('oxygen_vsb_dialog_form'); ?>

		<?php 
			/**
			 * Hook for add-ons to add UI elements inside the toolbar
			 *
			 * @since 1.4
			 */
			do_action("oxygen_before_toolbar_close"); 
		?>

	</div><!-- #oxygen-sidebar -->

</div><!-- #oxygen-ui -->


<?php 
	/**
	 * Hook for add-ons to add UI elements outside the toolbar
	 *
	 * @since 1.4
	 */
	do_action("oxygen_after_toolbar"); 
?>

<div id="oxy-no-class-msg" class="oxygen-overlay-property-msg oxy-no-class-msg">
	<?php _e("This property is not available for classes. It will be set in the element/ID.","oxygen"); ?>
</div>
<div id="oxy-no-media-msg" class="oxygen-overlay-property-msg oxy-no-media-msg">
	<?php _e("This property is not available for media queries. It will be set for 'Full Screen’.","oxygen"); ?>
</div>
<div id="oxy-no-class-no-media-msg" class="oxygen-overlay-property-msg oxy-no-class-msg oxy-no-media-msg">
	<?php _e("This property is not available for media queries or classes. It will be set for 'Full Screen’ in the element/ID.","oxygen"); ?>
</div>