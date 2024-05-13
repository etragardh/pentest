// Support for swiper v8
(function () {
  function BreakdanceSwiper() {
    const { mergeObjects, matchMedia, getCurrentBreakpoint, is } = BreakdanceFrontend.utils;

    function isElementInDom(selector) {
      // An element can be "hidden" (aka not in the DOM) with Hide on breakpoint -> in builder preview
      // and Swiper needs the element to be part of the DOM to work on it
      // This code was removed in https://github.com/soflyy/breakdance-elements/pull/2184/files#diff-897333d33555b463d7a30b1328e025df240eeb851ed79e85b643c65c6c6b4bffL12
      // but that introduced the following bug: https://github.com/soflyy/breakdance/issues/5956
      // so we put this code back, since the element might be SSRing.
      const isElementInDom = document.querySelector(selector);
      return !!isElementInDom;
    }

    function setBreakpoint(swiper, settings) {
      const { BASE_BREAKPOINT_ID } = window.BreakdanceFrontend.data;
      const isLoop = settings.infinite === "enabled";
      const slidesPerView = settings.advanced.slides_per_view;
      const spaceBetween = settings.advanced.between_slides;

      const onePerViewAt = settings.advanced.one_per_view_at;
      const onePerViewAtDesktop = onePerViewAt === BASE_BREAKPOINT_ID;
      const alwaysOne = settings.effect === "fade" || settings.effect === "flip" || onePerViewAtDesktop;

      const { activeIndex, initialized, loopedSlides = 0 } = swiper;
      const needsReLoop = isLoop && (slidesPerView !== swiper.params.slidesPerView);

      if (alwaysOne || matchMedia(onePerViewAt)) {
        swiper.params.slidesPerView = 1;
      } else {
        setBreakpointProperty(swiper, "slidesPerView", slidesPerView);
      }

      setBreakpointProperty(swiper, "spaceBetween", spaceBetween);

      // A reloop is necessary when changing `slidesPerView` or `direction`
      // when swiper has already been initialized.
      if (needsReLoop && initialized) {
        // Tip: This logic is different on >= v9.
        // See: https://github.com/nolimits4web/swiper/blob/master/src/core/breakpoints/setBreakpoint.js
        swiper.loopDestroy();
        swiper.loopCreate();
        swiper.updateSlides();
        swiper.slideTo(activeIndex - loopedSlides + swiper.loopedSlides, 0, false);
      } else {
        swiper.updateSlides();
        swiper.slideReset(0);
      }
    }

    function setBreakpointProperty(swiper, key, value) {
      if (Number.isFinite(value)) {
        // version >= 1.3 - backwards compatibility for non-breakpoint values.
        swiper.params[key] = value;
      } else if (is.obj(value)) {
        // The value is an object, but we don't know if it's a breakpoint yet.
        const availableBreakpoints = Object.keys(value);
        const currBreakpoint = getCurrentBreakpoint(availableBreakpoints);
        const bpValue = value[currBreakpoint.id];

        // Found a breakpoint value, use it.
        if (bpValue) {
          swiper.params[key] = isUnitValue(bpValue) ? bpValue.number : bpValue;
        } else if (isUnitValue(value) && !isResponsiveValue(value)) {
          // Otherwise, if the value is a unit value, use it.
          // Due to unknown reasons, the value is sometimes a breakpoint + unit value object, we ignore those.
          swiper.params[key] = value.number;
        }
      }
    }

    function isResponsiveValue(value) {
      const { breakpoints } = window.BreakdanceFrontend.data;
      const ids = breakpoints.map((bp) => bp.id);
      return Object.keys(value).some(key => ids.includes(key));
    }

    function isUnitValue(value) {
      return is.obj(value) && "number" in value;
    }

    // This prevents memory leak from event listeners
    function destroy(id) {
      if (
        window.swiperInstances &&
        window.swiperInstances[id] &&
        // if the element is not in the DOM, "el" will be the selector instead of the element reference
        typeof window.swiperInstances[id].el === "object"
      ) {
        window.swiperInstances[id].destroy(true, true);
        delete window.swiperInstances[id];
      }
    }

    function update({ id, selector, settings, paginationSettings, extras }) {

      if (!isElementInDom(`${selector} > .breakdance-swiper-wrapper > .swiper`)) return;

      destroy(id);

      const defaultOptions = {
        settings: {
          effect: "slide",
          coverflow: {
            rotate: {
              number: 50
            },
            depth: {
              number: 100
            },
            stretch: {
              number: 0
            }
          },
          speed: { number: 1000 },
          autoplay_settings: {
            speed: {
              number: 3000
            }
          },
          advanced: {
            between_slides: 0,
            slides_per_view: 1
          },
          direction: "horizontal",
        },
        pagination: {
          type: "bullets"
        }
      };

      settings = mergeObjects(defaultOptions.settings, settings);
      paginationSettings = mergeObjects(
        defaultOptions.pagination,
        paginationSettings
      );

      const advancedSettings = settings.advanced;
      const isBuilder = !!window?.BreakdanceFrontend.utils.isBuilder();

      const isCoverflowEffect = settings.effect === "coverflow";
      const coverFlowEffect = isCoverflowEffect
        ? {
          coverflowEffect: {
            rotate: settings.coverflow.rotate.number,
            slideShadows: !!settings.coverflow.shadow,
            depth: settings.coverflow.depth.number,
            stretch: settings.coverflow.stretch.number
          }
        }
        : {};

      const fadeEffect =
        settings.effect === "fade"
          ? {
            fadeEffect: {
              crossFade: true
            }
          }
          : {};

      const builderOnlySettings = isBuilder
        ? {
          // this prevents bugs caused by Swiper swallowing events
          preventClicksPropagation: false,
          preventClicks: false,
          simulateTouch: false,
          // doesn't play nice with our drag events
          allowTouchMove: false
        }
        : {};

      const forceAutoplay = extras && extras.autoplay === true;

      const swiperInstance = new Swiper(
        `${selector} > .breakdance-swiper-wrapper > .swiper`,
        {
          ...extras,
          speed: settings.speed.number,
          loop: settings.infinite === "enabled" && !isBuilder,
          autoplay:
            settings.autoplay === "enabled" && (!isBuilder || forceAutoplay)
              ? {
                delay: settings.autoplay_settings.speed.number,
                pauseOnMouseEnter:
                  !!settings.autoplay_settings.pause_on_hover,
                disableOnInteraction:
                  !!settings.autoplay_settings.stop_on_interaction,
                stopOnLastSlide: settings.infinite !== "enabled",
              }
              : false,

          effect: settings.effect,
          pagination: {
            el: `${selector} > .breakdance-swiper-wrapper > .swiper-pagination`,
            type: paginationSettings.type,
            clickable: true,
          },
          navigation: {
            nextEl: `${selector} > .breakdance-swiper-wrapper > .swiper-button-next`,
            prevEl: `${selector} > .breakdance-swiper-wrapper > .swiper-button-prev`,
          },
          keyboard: true,
          ...coverFlowEffect,
          ...fadeEffect,

          ...builderOnlySettings,

          // Advanced options
          mousewheel: advancedSettings.swipe_on_scroll
            ? {
              releaseOnEdges: true
            }
            : false,

          autoHeight: !!advancedSettings.auto_height,
          loopPreventsSlide: false,
          centeredSlides: isCoverflowEffect
            ? // We decided to make it always true because otherwise it looks ugly
            true
            : settings.center_slides,
          // Swiper docs advise to do this
          watchSlidesProgress: advancedSettings.slides_per_view !== 1,
          // doesn't do anything on its own, but enables elements to create cool effects with HTML
          parallax: true,

          direction: settings.direction,
        });

      setBreakpoint(swiperInstance, settings);

      swiperInstance.on("resize", () => {
        setBreakpoint(swiperInstance, settings);
      });

      window.swiperInstances = {
        ...window.swiperInstances,
        [id]: swiperInstance
      };
    }

    function updateSliderFromChild(id) {
      const sliderId = document
        // select itself
        .querySelector(`[data-node-id="${id}"]`)
        // get parent (slider) node id
        .parentElement.closest("[data-node-id]").dataset.nodeId;

      const sliderIdNumber = sliderId && parseInt(sliderId);

      if (window.swiperInstances && window.swiperInstances[sliderIdNumber]) {
        window.swiperInstances[sliderIdNumber].update();
      }
    }

    function selectSlide(id) {
      const slideElement = document
        .querySelector(`[data-node-id="${id}"]`)
        .closest(".swiper-slide");

      if (slideElement) {
        const slideIndex = Array.from(
          slideElement.parentElement.children
        ).indexOf(slideElement);

        const sliderElement =
          slideElement.parentElement &&
          slideElement.parentElement.closest("[data-node-id]");

        const sliderId = sliderElement ? sliderElement.dataset.nodeId : null;

        if (
          sliderId &&
          slideIndex !== null &&
          window.swiperInstances &&
          window.swiperInstances[sliderId]
        ) {
          window.swiperInstances[sliderId].slideTo(slideIndex, 0);
        }
      }
    }

    return {
      update,
      destroy,
      updateSliderFromChild,
      selectSlide
    };
  }

  window.BreakdanceSwiper = BreakdanceSwiper;
})();
