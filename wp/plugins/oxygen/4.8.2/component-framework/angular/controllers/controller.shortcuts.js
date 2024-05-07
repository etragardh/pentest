/**
 * Global Keyboard Shortcut Controller
 * 
 * @author Elijah M.
 * @since x.x
 */

CTFrontendBuilder.controller("ControllerShortcuts", function( $scope, $parentScope, $timeout, $interval, $rootScope ) {

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

// Keyboard shortcuts
function globalShortcutHandler(event) {

    if (CtBuilderAjax.userCanFullAccess!="true") {
        return;
    }

    // Stop event processing if it is repeating
    if (event.originalEvent.repeat) {
        return;
    }

    // Stop event processing if content editor is active
    if ($parentScope.isActiveActionTab('contentEditing')) {
        return;
    }

    // Process the shortcut events
    var processed = false;
    var key = event.key.toLowerCase();
    var keyCode = event.keyCode;

    if( (event.ctrlKey || event.metaKey) && !event.shiftKey ) {
        switch (key) {
            case 's':
                iframeScope.savePage();
                processed = true;
                break;
            case 'backspace':
            case 'delete':
                iframeScope.removeActiveComponent();
                processed = true;
                break;
            case 'd':
                iframeScope.duplicateComponent();
                processed = true;
                break;
            case 'c':
                $rootScope.$broadcast('copyElement');
                processed = true;
                break;
            case 'v':
                $rootScope.$broadcast('pasteElement');
                processed = true;
                break;
            case 'z':
                iframeScope.undo();
                break;
            case 'y':
                iframeScope.redo();
                break;
        } 
    } else if( (event.ctrlKey || event.metaKey) && event.shiftKey ) {
        switch (key) {
            case 'c':
                $parentScope.copyElementExportJSON($parentScope.iframeScope.component.active.id);
                processed = true;
                break;
            case 'i':
                $parentScope.showImportModal();
                processed = true;
                break;
            case 'e':
                if (!$parentScope.isActiveActionTab('componentBrowser')) $parentScope.switchActionTab('componentBrowser')
                $parentScope.addSearchbarInputFocus = false
                $parentScope.showEmmetCLI = true
                var timeout = $timeout(function() {
                    $parentScope.emmetInputFocus = true
                    $timeout.cancel(timeout);
                }, 0, false);
                break;
            case 'a':
                if (!$parentScope.isActiveActionTab('componentBrowser')) $parentScope.switchActionTab('componentBrowser')
                $parentScope.emmetInputFocus = false
                $parentScope.showEmmetCLI = false
                var timeout = $timeout(function() {
                    $parentScope.addSearchbarInputFocus = true
                    $timeout.cancel(timeout);
                }, 0, false);
                break;
        } 
    }

    // If the shortcut event was processed, stop the event propagation and cancel it
    if (processed) {
        return false;
    }

}

var keyDownCallback = debounce(globalShortcutHandler, 250);

function cancelDefaults(event) {

    var keyboardShortcuts = [
        's',
        'backspace',
        'delete',
        'd',
        'c',
        'v',
        'z',
        'y',
        'i',
        'a',
        'e',
    ]

    if( !keyboardShortcuts.includes(event.key.toLowerCase()) ) {
        return;
    }

    // Scenarios where we should not override browser shortcuts
    if( event.target.parentElement.classList.contains('textarea') && event.key.toLowerCase() != 's' ) {
        return;
    }

    if( event.target.nodeName == 'INPUT' && event.key.toLowerCase() != 's' && !event.target.classList.contains('oxygen-add-searchbar') ) { 
        return; 
    }

    if( event.target.nodeName == 'TEXTAREA' && event.key.toLowerCase() != 's' && event.target.getAttribute('id') !== 'oxy-emmet-cli-input') {
        return;
    }

    // keep certain default shortcutrs for emmet and add+
    if( event.target.classList.contains('oxygen-add-searchbar') || 
        event.target.getAttribute('id') == 'oxy-emmet-cli-input') { 
        if ( (event.ctrlKey || event.metaKey) && !event.shiftKey ) {
            if (['a','c','v'].includes(event.key.toLowerCase())) return
        }
    }

    if( $parentScope.isActiveActionTab('contentEditing') ) { 
        return; 
    }

    // Stop event processing if we are in a code editor
    if (parent.document.getElementsByClassName("cm-focused").length && event.key.toLowerCase() != 's' ) {
        return;
    }

    if( event.ctrlKey || event.metaKey ) {
        keyDownCallback(event);
        return false;
    }

}

// Event listener on iframe body
angular.element('body').on('keydown', cancelDefaults);

// Event listener on builder body
parent.angular.element('body').on('keydown', cancelDefaults);

})