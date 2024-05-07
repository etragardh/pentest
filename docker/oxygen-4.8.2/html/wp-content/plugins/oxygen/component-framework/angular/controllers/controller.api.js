/**
 * All API callbacks to handle server responses
 * 
 */

CTFrontendBuilder.controller("ControllerAPI", function($scope, $parentScope, $http, $timeout) {

	$scope.itemOptions = {};

	/**
	 * Show componentize dialog
	 * 
	 * @since 0.4.0
	 * @author Ilya K.
	 */
	
	$scope.showAddItemDialog = function(id, type, termId, termType, source, page, name, category, designSet) {
		
		var currentItem;

		if(typeof(category) !== 'undefined') {
			if(type === 'component') {
				var items = [];

				if(designSet) {
					items = $scope.experimental_components[designSet]['items'][category]['contents'];
				}
				else {
					items = $scope.libraryCategories[category].contents;
				}
				currentItem = _.findWhere(items, {id: id, name: name, page: page, source: source});
			}
			else if(type === 'page' || type === 'template') {
				var items = [];

				if(designSet) {
					if(type === 'template')
						items = $scope.experimental_components[designSet]['templates'];
					else
						items = $scope.experimental_components[designSet]['pages'];
				}
				else {
					items = $scope.libraryPages[category].contents;
				}

				currentItem = _.findWhere(items, {id: id, name: name, source: source});

			}

			if(typeof(currentItem.firstFew) !== 'undefined' && currentItem.firstFew===0) {
				$parentScope.showDialogWindow();
				$parentScope.dialogForms['showProComponentPageAddDialog'] = true;
				return;
			}
		}
		else if(typeof(name) !== 'undefined' && typeof(page) !== 'undefined') {
			currentItem = $scope.getSourceComponent(id, name, page);
		}
		else {
		 	currentItem = $scope.getAPIItem(id, type);
		}

		if(type === 'template')
			type = 'page';

		$scope.itemOptions = {
			id: 		 id,
			type: 		 type,
			termId: 	 termId,
			termType: 	 termType,
			currentItem: currentItem
		}

		if(typeof(source) !== 'undefined') {
			$scope.itemOptions['source'] = source;
		}

		if(typeof(page) !== 'undefined') {
			$scope.itemOptions['page'] = page;
		}
		
		if(typeof(designSet) !== 'undefined') {
			$scope.itemOptions['designSet'] = designSet;
		}
			

		$scope.addItem();
		return;

		// $parentScope.dialogForms['showAddItemDialogForm'] = true;

		// jQuery(document).on("keydown", $scope.switchComponent);

	}


	/**
	 * Insert in builder
	 *
	 * @since 0.4.0
	 * @author Ilya K.
	 */

	$scope.addItem = function(id, type, $event, source, page, designSet) {

		$scope.cancelDeleteUndo();

		if ( id == undefined ) {
			id = $scope.itemOptions.id
		}

		if ( type == undefined ) {
			type = $scope.itemOptions.type;
		}

		if ( source == undefined && $scope.itemOptions.source != undefined) {
			source = $scope.itemOptions.source;
		}

		if ( page == undefined && $scope.itemOptions.page != undefined) {
			page = $scope.itemOptions.page;
		}

		if ( designSet == undefined && $scope.itemOptions.designSet != undefined) {
			designSet = $scope.itemOptions.designSet;
		}

		if ( $event !== undefined ) {
			$event.stopPropagation();
		}

		$scope.itemOptions = {};

		switch (type) {

			case 'component' :
				if(typeof(source) !== 'undefined' && typeof(page) !== 'undefined') {
					$scope.getComponentFromSource(id, source, designSet, page, $scope.addComponentFromSource);
				}
				else {
					// get component from server
					$scope.makeAPICall("get_components", {
						"id": id
					}, $scope.addReusableChildren);
				}

			break;

			case 'page' :
				if(typeof(source) !== 'undefined') {
					$scope.getPageFromSource(id, source, designSet, $scope.addPageFromSource);
				}
				else {
					// get page from server
					$scope.makeAPICall("get_pages", {
						"id": id
					}, $scope.addReusableChildren);
				}

			break;
		}

		$parentScope.hideDialogWindow();
	}


	$scope.getSourceComponent = function(id, name, page) {

		var result = $scope.experimental_components[name]['items'].filter(function(item) {
			return item.id == id && item.page == page;
		});

		return result[0] ? result[0] : null;
	}

})