/**
 * All Fonts staff here
 * 
 */

CTFrontendBuilder.controller("ControllerFonts", function($scope, $http, $timeout) {

	// TODO: change API key from personal to corporate?
	$scope.APIKey 		= "AIzaSyBlDz9OGMf_5_-QxgHPTjjmvYVzEauwcQE";
	$scope.WebFontsUrl 	= "https://www.googleapis.com/webfonts/v1/webfonts";

	// Set default web fonts
	$scope.webSafeFonts = [
		"Georgia, serif",
		"Times New Roman, Times, serif",
		"Arial, Helvetica, sans-serif",
		"Arial Black, Gadget, sans-serif",
		"Tahoma, Geneva, sans-serif",
		"Verdana, Geneva, sans-serif",
		"Courier New, Courier, monospace"
	];

	$scope.showFonts 	= [];
	$scope.loadedFonts 	= [];
	$scope.googleFontsList = [];

	/**
     * Load Google Webfonts via AJAX
     * 
     * @since 0.1.9
     */

	$scope.getWebFontsList = function() {

		// check for cached data first
		if ($scope.googleFontsCache) {

			var params = {
	            action : 'oxy_get_google_fonts',
	            post_id : CtBuilderAjax.postId,
	            nonce : CtBuilderAjax.nonce,
	        };

			$http({
				method: "GET",
				url: CtBuilderAjax.ajaxUrl,
				params: params,
				transformResponse: false,
			})
			.then(function(response) {
				fonts = JSON.parse(response.data);
				// if for any reason there is no fonts init it as empty array
				if (!fonts) {
					fonts = [];
				}
				$scope.googleFontsList = fonts;
				$scope.loadSavedGlobalFonts();
			})
			.catch(function(data) {
				console.log(data);
			})

			return;
		}

		// Send AJAX request to Google
		$http({
			method: "GET",
			url: $scope.WebFontsUrl,
			params: {
				"key" : $scope.APIKey,
				"sort" : "popularity"
			}
		})
		.then(function(response) {
			 var fonts = response.data;
			 angular.forEach(fonts.items, function(item) {
			 	$scope.googleFontsList.push({
			 		family: item.family,
			 		variants: item.variants,
			 	});
			 });
			 $scope.loadSavedGlobalFonts();
		})
		.catch(function(data, status, headers, config) {
			console.log(data);
		});
	}


	/**
	 * Get Google Font object by font-family name
	 *
	 * @since 2.2
	 * @author Ilya K.
	 */

	$scope.getGoogleFont = function(fontFamily) {

		var result = $scope.googleFontsList.find(function(obj){
			return obj.family === fontFamily;
		})

		if ( result !== undefined ) {
			return result;
		}
		else {
			return false;
		}
	}


	/**
	 * Apply font family for certain component by it's ID
	 *
	 * @since 0.1.9
	 */

	$scope.setComponentFont = function(id, name, font, option) {

		if (undefined===option) {
			option = 'font-family';
		}

		$scope.loadWebFont(font);

		// update model
		$scope.component.options[id]['model'][option] = font;

		// update Tree
		$scope.setOption(id, name, option);

		$scope.showFonts    = [];
		$scope.fontsFilter  = "";
	}


	/**
	 * Get component font value
	 *
	 * @since 0.1.9
	 */

	$scope.getComponentFont = function(id, isName, stateName, option) {

		if ( id == 0 ) {
			return "";
		}

		if ( !stateName ) {
			stateName = 'model';
		}

		if (undefined==option) {
			option = 'font-family';
		}

		// use currently active component if id is not defined
		if ( !id ) {
			id = $scope.component.active.id;
		}

		if ( $scope.component.options[id] && $scope.component.options[id][stateName] !== undefined ) { 
			var font = $scope.component.options[id][stateName][option];
		}
		
		if ( !font ) {
			return;
		}

		if ( font[0] == 'global' ) {
			// global fonts
			if ( isName === true ) {
				return font[1] + " (" + $scope.getGlobalFont(font[1]) + ")";
			}
			else {
				return $scope.getGlobalFont(font[1]);
			}
		}
		else {
			return font;
		}
	}


	/**
	 * Apply font family for global variable
	 *
	 * @since 0.1.9
	 */

	$scope.setGlobalFont = function(name, font) {

		$scope.globalSettings.fonts[name] = font;
		$scope.fontsFilter  = "";
        $scope.loadWebFont(['global',name]);

        $scope.classesCached = false;
        $scope.updateAllComponentsCacheStyles();
        $scope.outputCSSOptions();
        $scope.unsavedChanges();
	}


	/**
	 * Return global font family by custom name
	 *
	 * @since 0.1.9
	 */

	$scope.getGlobalFont = function(name) {

		return $scope.globalSettings.fonts[name];
	}


	/**
	 * Add new custom global font
	 *
	 * @since 0.1.9
	 */

	$scope.addGlobalFont = function() {

		var name = prompt("Custom global font name (i.e 'My Font'):");
        
        if (name != null) {
			return $scope.globalSettings.fonts[name] = "Open Sans";
		}

		$scope.unsavedChanges();
	}


	/**
	 * Delete global font
	 *
	 * @since 0.1.9
	 */

	$scope.deleteGlobalFont = function(name) {

		var confirmed = confirm("Are you sure to delete \""+name+"\" font?");
        
        if ( !confirmed ) {
            return false;
        }

        // delete from global settings
		delete $scope.globalSettings.fonts[name];

		// delete from classes
		angular.forEach($scope.classes, function(classStates, className) {

			angular.forEach(classStates, function(stateOption, stateName) {

				if ( stateOption['font-family'] && stateOption['font-family'][1] == name ) {
					delete stateOption['font-family'];
				}
			});
		});

		$scope.unsavedChanges();
	}


	/**
	 * Check global font on options apply and delete fonts that doesn't exist in global settings
	 *
	 * @since 0.1.9
	 */

	$scope.checkGlobalFont = function(options) {

		// loop all component option properties
		angular.forEach(options, function(stateOptions, stateName) {

			// do not loop string options
			if (typeof stateOptions === "object") {
			
				// loop states (original, :hover, etc)
				angular.forEach(stateOptions, function(value, name) {

					// check if media present and loop it as well
					if (typeof value === "object" && stateName == 'media') {

						// recursively loop media options
						$scope.checkGlobalFont(value);
					}
					else {

						var font = true;

						// apply option
						if ( name == "font-family" && value[0] == 'global' ) {
							font = $scope.getGlobalFont(value[1]);
						}

						if (typeof name == 'string' && name.indexOf("font-family") > -1 && value[0] == 'global'){
							font = $scope.getGlobalFont(value[1]);
						}

						// check if global font is exist in global settings
						if ( font == undefined ) {
							// update Tree
							delete stateOptions[name];
						}
					}
				});
			}
		});
	}


	/**
	 * Load Google Web font
	 *
	 * @since 0.1.9
	 */

	$scope.loadWebFont = function(font, force) {

		if ($scope.disableGoogleFonts=='true') {
			return;
		}

		// Skip Inherit
		if ( font == "Inherit" ) {
			return false;
		}

		var weights = ":100,200,300,400,500,600,700,800,900";

		// check if global font used
		if ( font[0] == 'global' ) {
			name = $scope.getGlobalFont(font[1]);
			// don't load global fonts other than Google Fonts
			if ( $scope.getGoogleFont(name) === false ) {
				return false;
			}
			if ($scope.globalSettings['fontsOptions'] && $scope.globalSettings['fontsOptions'][font[1]]){
				weights = ":";
				for(var weight in $scope.globalSettings['fontsOptions'][font[1]]) { 
					//console.log(weight, $scope.globalSettings['fontsOptions'][font[1]][weight]);
					if ($scope.globalSettings['fontsOptions'][font[1]][weight] == 'true') {
						weights += weight+',';
					}
				}
				if (weights==":") {
					weights = ":100,200,300,400,500,600,700,800,900";
				}
			}
		}
		else {
			name = font;
		}

		// Don't load Web Safe fonts
		if ( $scope.webSafeFonts.indexOf(name) > -1 ) {
			return false;
		}

		// Don't load ECF fonts
		if ( $scope.elegantCustomFonts && $scope.elegantCustomFonts.indexOf(name) > -1 ) {
			return false;
		}

		// Don't load Typekit fonts
		if ($scope.typeKitFonts) {
			for(var i = 0, len = $scope.typeKitFonts.length; i < len; i++) {
				if ($scope.typeKitFonts[i].slug === name) {
					return false;
				}
			}
		}

		// Don't load fonts that already had been loaded
		if ( $scope.loadedFonts.indexOf(name) > -1 && force !== true) {
			return false;
		}

		$scope.loadedFonts.push(name);

		if(name && name !== '') {

			name += weights;

			// finally load font
			WebFont.load({
				google: {
					families: [name]
				}
			});
		}
	}


	/**
	 * Load all global fonts added in settings
	 *
	 * @since 0.4.0
	 */
	
	$scope.loadSavedGlobalFonts = function() {

		angular.forEach($scope.globalSettings.fonts, function(font, key) {
			$scope.loadWebFont(['global',key]);
		})
	}


	/**
	 * Check if given font-family assigned to any Global font
	 *
	 * @since 3.9
	 */

	$scope.globalFontExist = function(fontFamily) {
		var result = false;

		angular.forEach($scope.globalSettings.fonts, function(font, key) {
			
			if (fontFamily==font) {
				result = true;
			}
		})

		return result;
	}

});