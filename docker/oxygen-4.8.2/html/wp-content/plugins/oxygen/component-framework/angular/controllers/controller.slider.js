
/**
 * Slider Controller
 *
 */

CTFrontendBuilderUI.controller("ControllerSlider", function($controller, $scope, $timeout, $interval) {

	$scope.$parent.$on('iframe-init', function(e, iframeScope) {

        /**
         * Setup UI
         *
         */

		jQuery("#ct-dom-tree-2").on("mousedown", ".ct_slide", function($event){

            // don't animate when DOM Tree options dropdown clicked
            if ( jQuery($event.target).parents('.dom-tree-node-options').length > 0 ) {
                return;
            }

			var sliderId 	= jQuery(this).closest(".ct_slider").attr("ng-attr-tree-id"),
				slideIndex 	= jQuery(this).index();

			$scope.animateToSlide(sliderId, slideIndex);
    	});

    })

    
    /**
     * Add first 3 slides when adding slider component
     *
     * @author Ilya K.
     * @since 2.0
     */

    $scope.$parent.addSlides = function() {

        var timeout = $timeout(function() {
            
            $scope.iframeScope.waitOxygenTree(function(){
                $scope.iframeScope.addComponent("ct_slide",false,true);
                $scope.iframeScope.addComponent("ct_slide",false,true);
                $scope.iframeScope.addComponent("ct_slide",false,true);
            })

            $timeout.cancel(timeout);
        }, 0, false);

    }


    /**
	 * Animate Unslider to certain slide
     *
     * @author Ilya K.
     * @since 2.0
     */

    $scope.$parent.animateToSlide = function(sliderId, slideIndex) {

        //console.log("animateToSlide()",sliderId, slideIndex, $scope.iframeScope.sliders[sliderId])

    	if (slideIndex >= 0) {
    		$scope.iframeScope.sliders[sliderId].unslider('animate:'+slideIndex);
    	}
    }


    /**
     * Trigger on slide component added from builder directive
     *
     * @author Ilya K.
     * @since 2.0
     */

    $scope.$parent.slideAdded = function(sliderId, slideId) {

        // rebuild slider
        $scope.iframeScope.rebuildDOM(sliderId);

        // scroll to added slide
        var slideIndex = $scope.iframeScope.getComponentById(slideId).closest("li").index();
        $scope.animateToSlide(sliderId, slideIndex);
    }

})