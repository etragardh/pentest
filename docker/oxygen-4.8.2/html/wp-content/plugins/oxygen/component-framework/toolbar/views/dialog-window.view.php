<div id="ct-dialog-window" class="ct-dialog-window" 
	ng-if="dialogWindow" 
	ng-class="{'ct-add-form-dialog':dialogForms['showAddItemDialogForm']}"
	ng-click="hideDialogWindow()">
	<div class="ct-dialog-window-content-wrap"
		ng-click="$event.stopPropagation()">

        <!-- modal dialog form for the conditions -->
        <?php include_once( CT_FW_PATH . "/toolbar/views/conditions-modal.view.php" ); ?>
        <!-- modal dialog form for the advanced query -->
        <?php include_once( CT_FW_PATH . "/toolbar/views/advanced-query-modal.view.php" ); ?>
		
		<div class="ct-close-dialog ct-action-button" ng-click="hideDialogWindow()"><i class="fa fa-times fa-lg"></i></div>
	</div>
</div><!-- #ct-dialog-window -->

<!-- Global Color Dialog Window -->
<div class="oxygen-global-colors-new-color-bg"
    ng-show="addNewColorDialog"
    ng-click="hideAddNewColorDialog()">
</div>
<div id="oxygen-global-colors-new-color-dialog" class="oxygen-global-colors-new-color oxygen-global-colors-new-color-dialog"
    ng-show="addNewColorDialog">
    <h2><?php _e("New Color","oxygen"); ?></h2>
    <div class="oxygen-input">
        <div class="oxygen-global-color"
            ng-style="{backgroundColor:addNewColorDialogValue}"></div>
        <input type="text" spellcheck="false" placeholder="<?php _e("Color Name","oxygen"); ?>"
            ng-model="newGlobalSettingsColorName" ng-model-options="{ debounce: 10 }">
    </div>
    <div class="oxygen-select oxygen-select-box-wrapper"
        ng-click="toggleOxygenSelectBox($event)">
        <div class="oxygen-select-box">
            <div class="oxygen-select-box-current ng-binding">{{iframeScope.getGlobalColorSet(colorSetIDToAdd).name}}</div>
            <div class="oxygen-select-box-dropdown"></div>
        </div>
        <div class="oxygen-select-box-options">
            <div class="oxygen-select-box-option"
                ng-repeat="(key,set) in iframeScope.globalColorSets.sets"
                ng-click="$parent.colorSetIDToAdd=set.id">{{set.name}}</div>
        </div>
    </div>
    <div class="oxygen-add-global-color-button"
     ng-click="iframeScope.addNewColor(newGlobalSettingsColorName, colorSetIDToAdd, 'latest')">Add</div>
</div>
<!-- /Global Color Dialog Window -->

<!-- Presets Dialog Window -->
<div class="oxygen-data-dialog-bg"
    ng-show="showPresetsDialog"
    ng-click="showPresetsDialog=false">
</div>
<div id="oxygen-presets-dialog" class="oxygen-presets-dialog oxygen-data-dialog"
    ng-show="showPresetsDialog">
    <h1><?php _e("Preset","oxygen"); ?>
        <svg class="oxygen-close-icon"
            ng-click="showPresetsDialog=false"><use xlink:href="#oxy-icon-cross"></use>
        </svg>
    </h1>
    <div id="oxygen-preset-json-container">{{iframeScope.presetExportJSON}}</div>
    <div class="oxygen-apply-button"
        ng-click="iframeScope.copyPresetExportJSON()">
        <?php _e("Copy To Clipboard","oxygen"); ?>
    </div>
</div>
<!-- /Presets Dialog Window -->


<!-- Import/Export Dialog Window -->
<div class="oxygen-data-dialog-bg"
    ng-show="showImportDialog||showExportDialog"
    ng-click="showImportDialog=false;showExportDialog=false;elementExportJSON='';elementImportJSON=''">
</div>
<div id="oxygen-export-dialog" class="oxygen-data-dialog"
    ng-show="showImportDialog||showExportDialog">
    
    <h1>
        <span ng-show="showExportDialog">
            <?php _e("Export Element JSON","oxygen"); ?>
        </span>
        <span ng-show="showImportDialog">
            <?php _e("Import Element JSON","oxygen"); ?>
        </span>
        <svg class="oxygen-close-icon"
            ng-click="showImportDialog=false;showExportDialog=false;elementExportJSON='';elementImportJSON=''"><use xlink:href="#oxy-icon-cross"></use>
        </svg>
    </h1>

    <div ng-show="showExportDialog">
        <div class="oxygen-json-container">{{elementExportJSON}}</div>
        <div class="oxygen-apply-button"
            ng-click="copyElementExportJSON()">
            <?php _e("Copy To Clipboard","oxygen"); ?>
        </div>
    </div>

    <div ng-show="showImportDialog">
        <textarea 
            ng-model="elementImportJSON"></textarea>
        <div class="oxygen-apply-button"
            ng-click="processElementImportJSON()">
            <?php _e("Import","oxygen"); ?>
        </div>
    </div>
</div>
<!-- /Import/Export Dialog Window -->
