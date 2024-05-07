/**
 * Unslider directive
 */

CTFrontendBuilder.directive('oxygenSlider', function($timeout,$interval) {

    return {
        link:function(scope,element,attrs) {

            // init unslider after directive build the element
            var timeout = $timeout(function() {

                var componentId = element.parent().attr('ng-attr-component-id');

                if (scope.$parent) {
                    scope.$parent.waitOxygenTree(function(){
                        
                        if (scope.$parent.sliders === undefined) {
                            scope.$parent.sliders = {};
                        }
                        var options = scope.component.options[componentId]['id'];
                        
                        scope.$parent.sliders[componentId] = element
                        
                        .on('unslider.ready', function() {
                            scope.$parent.adjustResizeBox();
                        })
                        
                        // init Unslider
                        .unslider({ 
                            autoplay:  (options['slider-autoplay']=='yes') ? true : false,
                            delay:      parseInt(options['slider-autoplay-delay']),
                            animation:  options['slider-animation'],
                            speed:      parseInt(options['slider-animation-speed']),
                            arrows:    (options['slider-show-arrows']=='yes') ? true : false,
                            nav:       (options['slider-show-dots']=='yes') ? true : false,
                            // other defaults
                            infinite: false
                        })
                        
                        .on('unslider.change', function(event, index, slide) {
                            if (options['slider-autoplay']=='no') {
                                scope.$parent.titleBarsVisibility("hidden");
                                var slideID = slide.children().attr('ng-attr-component-id');
                                scope.$parent.activateComponent(slideID);
                            }
                        })
                        
                        .on('unslider.moved', function() {
                            scope.$parent.adjustResizeBox();
                            scope.$parent.titleBarsVisibility("visible", "visible");
                        })

                    
                    
                        var activeComponent = scope.$parent.getActiveComponent();
                        if (activeComponent.parents(".ct-slider").length > 0) {
                            var activeIndex = activeComponent.closest("li").index();
                            scope.$parent.sliders[componentId].unslider('animate:'+activeIndex);
                        }

                        // stop parent activating when click unslider arrows
                        element.siblings(".unslider-arrow").mousedown(function(e){
                            e.stopPropagation();
                        });
                        element.siblings(".unslider-nav").find("li").mousedown(function(e){
                            e.stopPropagation();
                        });

                    });
                }
                
            }, 0, false);
        }
    }
})