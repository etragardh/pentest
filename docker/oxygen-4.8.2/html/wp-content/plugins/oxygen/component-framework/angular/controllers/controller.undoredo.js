/**
 * Undo Redo Controller
 *
 * @author Abdelouahed E.
 * @since 3.2
 */

CTFrontendBuilder.controller("ControllerUndoRedo", function ($scope, $parentScope, $timeout, $interval) {
    // Store data that need to be tracked for changes
    var oldData = {};
    var data = {};

    // Used to skip actions during components rebuild
    var inPogress = false;

    // Used to pause/resume data watcher
    var trackChanges = true;

    // Setup undo manager
    var undoManager = new UndoManager();

    // Expose undoManager to scope
    $scope.undoManager = undoManager;
    $scope.pauseDataWatcher = pauseDataWatcher;
    $scope.resumeDataWatcher = resumeDataWatcher;
    $scope.watchIntervalCallback = watchIntervalCallback;

    // Initialize history items
    $scope.firstHistoryItem = {};
    $scope.historyItems = [];

    // Used to skip changes that shouldn't be tracked
    $scope.skipChanges = false;

    // Initialize undo/redo status to false
    $scope.canUndo = false;
    $scope.canRedo = false;

    // Expose undo command to iframeScope
    $scope.undo = function () {
        if (inPogress == false) {
            undoManager.undo();
        }
    };

    // Expose redo command to iframeScope
    $scope.redo = function () {
        if (inPogress == false) {
            undoManager.redo();
        }
    };

    // Debounce callback
    function debounce(callback, delay) {
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

    // Update undo/redo status on every action on the stack
    undoManager.setChangeCallback(function () {
        $scope.canUndo = undoManager.hasUndo();
        $scope.canRedo = undoManager.hasRedo();

        // Populate history items
        var history = [];
        var umIndex = undoManager.getIndex();
        var umCommands = undoManager.getCommands();

        // Update first hidtory item status
        $scope.firstHistoryItem.active = (umIndex == -1);

        // Add changes to the history array
        umCommands.forEach(function (command, index) {
            var item = {
                index: index,
                time: command.time.toLocaleTimeString(),
                name: command.name,
                active: umIndex == index,
            };

            history.push(item);
        });

        $scope.historyItems = history;
    });

    function doHistoryItem(command) {
        // Force update non object data from $scope
        collectData();

        var commandData = angular.copy(command.data);
        var newData = angular.copy(data);

        // Calculate data diff
        var diff = DeepDiff(newData, commandData);

        // If there any difference between the data collections, apply the diff and restore target state
        if (diff) {
            restoreData(diff);
        }
    }

    undoManager.setDoCallback(doHistoryItem);

    $scope.doFirstHistoryItem = function () {
        undoManager.setIndex(-1);
        doHistoryItem($scope.firstHistoryItem);
    };

    function restoreData(diff) {
        inPogress = true;
        var domChanged = false, optionsChanged = false;

        var rebuildLevel = Infinity;
        var domChangedPaths = [];
        var optionsChangedPaths = [];
        var tagChangedPaths = [];

        function applyDiffItem(d) {
            var path = d.path;

            // Set a flag to rebuild DOM if components tree was changed
            if (path[0] === "componentsTree") {
                var lastIndex = path.lastIndexOf('children');

                if (lastIndex > 0) {
                    if (path.includes("options")) {
                        optionsChanged = true;
                        var componentPath = path.slice(0, lastIndex + 2).join('.');
                        if (optionsChangedPaths.indexOf(componentPath) == -1) {
                            optionsChangedPaths.push(componentPath);
                        }
                        // Check if changed tag
                        var pathEnd = _.last(path);
                        if (pathEnd == "tag" || (pathEnd == "original" && (_.has(d.lhs, "tag") || _.has(d.rhs, "tag")))) {
                            if (tagChangedPaths.indexOf(componentPath) == -1) {
                                tagChangedPaths.push(componentPath);
                            }
                        }
                    } else {
                        domChanged = true;
                        rebuildLevel = Math.min(lastIndex, rebuildLevel);
                        var componentPath = path.slice(0, rebuildLevel).join('.');
                        if (domChangedPaths.indexOf(componentPath) == -1) {
                            domChangedPaths.push(componentPath);
                        }
                    }
                }
            }

            // Handle other $scope data
            switch (d.kind) {
                case 'EDIT':
                case 'NEW':
                    objectPath.set($scope, path, d.rhs);
                    break;

                case 'DELETE':
                    objectPath.del($scope, path);
                    break;

                case 'ARRAY':
                    var dd = d.item;
                    dd.path = path.concat(d.index);

                    applyDiffItem(dd);
                    break;
            }
        }

        diff.forEach(applyDiffItem);

        // componentsTree structure has been changed
        if (domChanged) {
            var componentsToRebuild = [];

            // Calculate components ids to rebuild
            if (rebuildLevel < Infinity) {
                domChangedPaths.forEach(function (path) {
                    var component = objectPath.get($scope, path, null);

                    if (component && typeof component.id == "number") {
                        var id = component.id;
                        if (typeof componentsToRebuild[id] == "undefined") {
                            componentsToRebuild[id] = component;
                        }
                    }
                });
            }

            // If componentsToRebuild is empty, use the root component
            if (componentsToRebuild.length == 0) {
                componentsToRebuild.push($scope.componentsTree);
            }

            // Rebuild DOM
            componentsToRebuild.forEach(function (component) {
                var id = component.id, name = component.name;
                var element = $scope.getComponentById(id);

                // If the change occurs in a repeater, rebuild the parent repeater
                if (element && element.closest('[ctdynamiclist]').length > 0) {
                    $scope.updateRepeaterQuery(id);
                }

                // If the change occurs in a slide, rebuild the parent slider
                if (name == "ct_slide") {
                    id = component.options.ct_parent;
                }

                $scope.rebuildDOM(id);
            });
        }

        // componentsTree options has been changed
        if (optionsChanged) {
            var rebuildAjaxComponents = [];
            // Apply components options
            optionsChangedPaths.forEach(function (path) {
                var component = objectPath.get($scope, path, null);
                var options = $scope.component.options;
                if (component && component.id && component.options) {
                    var id = component.id, tag = component.name;
                    // Save name and parent to restore them later
                    var nicename = options[id]['nicename'];
                    var ct_parent = options[id]['ct_parent'];
                    // Reset the options model and recreate it from the tree
                    options[id] = {};
                    $scope.applyComponentDefaultOptions(id, tag, component);
                    $scope.applyComponentSavedOptions(id, component);
                    $scope.applyModelOptions(id, tag);
                    // apply saved name and parent id again
                    options[id]['nicename'] = nicename;
                    options[id]['ct_parent'] = ct_parent;

                    // Rebuild API element with rebuildElementOnChange options
                    if (!domChanged && typeof $scope.componentsTemplates == "object") {
                        if ($scope.componentsTemplates.hasOwnProperty(tag)) {
                            var triggerOptions = $scope.componentsTemplates[tag].rebuildTriggerOptions;
                            if (triggerOptions) {
                                _.each(component.options.original, function (optionValue, optionName) {
                                    if (triggerOptions.includes(optionName) && !rebuildAjaxComponents[id]) {
                                        rebuildAjaxComponents[id] = tag;
                                    }
                                });
                            }
                        }
                    }
                }
            });

            rebuildAjaxComponents.forEach(function (tag, id) {
                $timeout(function () {
                    $scope.renderComponentWithAJAX('oxy_render_' + tag, id);
                });
            });

            if (!domChanged && tagChangedPaths.length) {
                tagChangedPaths.forEach(function (path) {
                    var component = objectPath.get($scope, path, null);
                    if (component) {
                        $scope.rebuildDOM(component.id);
                    }
                });
            }
        }

        // update cache
        $scope.classesCached = false;
        $scope.updateAllComponentsCacheStyles();

        // output CSS
        $scope.outputCSSOptions();
        $scope.outputPageSettingsCSS();

        // adjust the resize box
        $scope.adjustResizeBox();

        // Remove undo delete notification, a feature that will be removed
        $scope.cancelDeleteUndo();

        // Set a flag to skip the recent changes caused by data restore
        $scope.skipChanges = true;

        // Set progress status when all components are rebuilt
        $scope.waitOxygenTree(function () {
            inPogress = false;
        });
    }

    function addUndoRedoData(undoDiff, redoDiff) {
        var name = $scope.component.options[$scope.component.active.id]['nicename'] || $scope.component.active.name || 'Unknown';

        undoManager.add({
            time: new Date(),
            name: name,
            data: angular.copy(data),

            undo: function () {
                restoreData.call(this, undoDiff);
            },
            redo: function () {
                restoreData.call(this, redoDiff);
            }
        });
    }

    function collectData() {
        data = {
            // Active Component
            "component": {
                "active": $scope.component.active
            },

            // Componenets
            "componentsTree": $scope.componentsTree,

            // Classes
            "classes": $scope.classes,
            "currentClass": $scope.currentClass,
            "componentsClasses": $scope.componentsClasses,

            // Custom selectors
            "customSelectors": $scope.customSelectors,

            // Styles
            "styleSets": $scope.styleSets,
            "styleFolders": $scope.styleFolders,

            // Stylesheets
            "styleSheets": $scope.styleSheets,
            "currentActiveStylesheetFolder": $scope.currentActiveStylesheetFolder,

            // Page settings
            "pageSettingsMeta": $scope.pageSettingsMeta,

            // Global settings
            "globalSettings": $scope.globalSettings,
            "globalColorSets": $scope.globalColorSets,

            // Templates
            "easyPostsCustomTemplates": $scope.easyPostsCustomTemplates,
            "commentsListCustomTemplates": $scope.commentsListCustomTemplates,

            // Presets
            "elementPresets": $scope.elementPresets
        };
    }

    function pauseDataWatcher() {
        watchIntervalCallback();
        trackChanges = false;
    }

    function resumeDataWatcher() {
        trackChanges = true;
        watchIntervalCallback();
    }

    function watchIntervalCallback() {
        // Check if undo manager is paused
        if (trackChanges == false) {
            return;
        }

        // Check if undo actions are in progress
        if (inPogress) {
            return;
        }

        // Force update non object data from $scope
        collectData();

        // Copy current data to compare
        var newData = angular.copy(data);

        // return if both old and new data are equal
        if (_.isEqual(newData, oldData)) {
            return;
        }

        // Calculate data diff
        var undoDiff = DeepDiff(newData, oldData);
        var redoDiff = DeepDiff(oldData, newData);

        // return if no data diff found
        if (!undoDiff || !redoDiff) {
            return;
        }

        // Prevent registration of new history points when activating components
        var changedActiveOnly = true;
        for (var i in redoDiff) {
            if (redoDiff[i].path[0] != "component") {
                changedActiveOnly = false;
                break;
            }
        }

        if (changedActiveOnly) {
            return;
        }

        // Check if the changes must be skipped
        if ($scope.skipChanges) {
            $scope.skipChanges = false;
        } else {
            // Save undo/redo states
            addUndoRedoData(undoDiff, redoDiff);
        }

        // store current data as refernce to check for changes
        oldData = angular.copy(data);
    }

    // Start watching for data changes
    function watchIntervalStarter() {
        // Force update non object data from $scope
        collectData();

        // store current data as refernce to check for changes
        oldData = angular.copy(data);

        var date = new Date();
        $scope.firstHistoryItem = {
            index: -1,
            name: "Initial State",
            time: date.toLocaleTimeString(),
            data: angular.copy(data),
            active: true,
        };

        if ($scope.historyLimit) {
            undoManager.setLimit($scope.historyLimit);
        }
    
        // Start watching on interval
        $interval(watchIntervalCallback, 1000);
    }

    // Fired when the builder content is loaded
    $scope.$on("oxygen_components_loaded", function () {
        var loading = $interval(function () {
            // Check loading status before starting the data watcher
            if ($parentScope.overlaysCount == 0) {
                // Start data watcher
                watchIntervalStarter();
                // Cancel loading interval
                $interval.cancel(loading);
            }
        }, 100);
    });

});
