/**
 * Global Auto-Unit Controller
 * 
 * @author Elijah M.
 * @since x.x
 */

CTFrontendBuilder.controller("ControllerAutoUnits", function( $scope, $parentScope, $timeout, $interval, $rootScope ) {

// Empty array to hold units as set up by setupUnits() function
var units = []

// Returns false for any fields where the auto-unit code should not run
function autoUnitAllowed( input ) {
    // If we're not focusing an input, return false.
    if( input.nodeName != "INPUT" ) return false;
    // If we're focusing an input that doesn't have a unit selection interface, return false.
    if( !input.parentElement.querySelector('.oxygen-measure-box-units') ) return false;

    // If all above checks passed, return true.
    return true;
}

// Fires on focus to setup the available units array for the autoUnitHandler function.
function setupUnits( e ) {
    var target = e.target;
    if( !autoUnitAllowed( target ) ) return;

    units = [];

    var allowedUnits = target.parentElement.querySelector('.oxygen-measure-box-units').querySelectorAll('div:not(.oxygen-measure-box-unit-active)');

    allowedUnits.forEach( function( unit ) {
        units.push( unit.innerText.replace(/[\n\r]+|[\s]{2,}/g, ' ').trim() );
    } );
}

// Here, we check against an array of units (established by the setupUnits() function)
// If the string we've typed, minus any numerical characters, matches any of the units in the array
// Then we go ahead and fire our replaceAndUpdateUnit() function to select the unit and clean up our string
function autoUnitHandler( e ) {
    var target = e.target;
    if( !autoUnitAllowed( target ) ) return;
    if( !units.length ) return;

    units.forEach( function(unit) {
        if( target.value && target.value.replace(/-?\d*\.?\d*/g, '') == unit ) {
            // If none, we need the acutual Angular option model unit to be ' ', not 'none'
            if( unit == "none" ) unit = ' ';
            replaceAndUpdateUnit( target, target.dataset.option, unit );
        }
    })
}

// Here, we set the actual option unit the Angular model
// then we remove the unit from the input's value
// and finally blur() the input so that the builder preview updates
function replaceAndUpdateUnit( input, model, unit ) {
    if( input.attributes['ng-model'].nodeValue.includes('global') ) {
        iframeScope.setGlobalOptionUnit('global', model, unit);
    } else {
        iframeScope.setOptionUnit(model, unit);
    }

    var autoUnitTimeout = $timeout( function() {
        if( unit == ' ' ) unit = 'none';
        input.value = input.value.replace(unit, '');
        iframeScope.setOptionModel(model, input.value);

        $timeout.cancel(autoUnitTimeout);
    }, 50, false);

    if( unit != 'none' ) input.blur();
}

// Event listener on builder body
parent.angular.element('body')
  .on('keyup', autoUnitHandler)
  .on('focusin', setupUnits);

})